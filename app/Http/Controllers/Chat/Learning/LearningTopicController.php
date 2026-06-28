<?php

namespace App\Http\Controllers\Chat\Learning;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LearningTopic;
use App\Models\LearningTopicAssignment;
use App\Models\LearningTopicMedia;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LearningTopicController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('department_name')->get();
        $positions   = Position::orderBy('position')->get();
        $employees   = Employee::orderBy('name')->orderBy('lastname')->get();

        return view('admin.chats.employee.learnings', compact(
            'departments',
            'positions',
            'employees'
        ));
    }

    public function list()
    {
        $topics = LearningTopic::orderBy('title')
            ->get([
                'id',
                'title',
                'prompt_label',
                'short_intro',
                'difficulty',
                'estimated_minutes',
                'is_active',
                'updated_at',
            ]);

        return response()->json($topics);
    }

    public function show(LearningTopic $topic)
    {
        $topic->load('media', 'assignments');

        $assignments = $topic->assignments;

        $departmentIds = $assignments
            ->where('assignable_type', Department::class)
            ->pluck('assignable_id')
            ->values();

        $positionIds = $assignments
            ->where('assignable_type', Position::class)
            ->pluck('assignable_id')
            ->values();

        $employeeIds = $assignments
            ->where('assignable_type', Employee::class)
            ->pluck('assignable_id')
            ->values();

        return response()->json([
            'topic'          => $topic,
            'media'          => $topic->media,
            'department_ids' => $departmentIds,
            'position_ids'   => $positionIds,
            'employee_ids'   => $employeeIds,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id'                => 'nullable|exists:learning_topics,id',
            'title'             => 'required|string|max:255',
            'prompt_label'      => 'nullable|string|max:255',
            'short_intro'       => 'nullable|string|max:255',
            'body'              => 'nullable|string',
            'estimated_minutes' => 'nullable|integer|min:1|max:480',
            'difficulty'        => 'nullable|in:beginner,intermediate,advanced',
            'audience_scope'    => 'required|in:all,department,position,employee',
            'is_active'         => 'boolean',

            'department_ids'    => 'array',
            'department_ids.*'  => 'integer|exists:departments,id',

            'position_ids'      => 'array',
            'position_ids.*'    => 'integer|exists:positions,id',

            'employee_ids'      => 'array',
            'employee_ids.*'    => 'integer|exists:employees,id',
        ]);

        $userId = Auth::id();

        if (!empty($validated['id'])) {
            $topic = LearningTopic::findOrFail($validated['id']);
            $topic->fill($validated);
        } else {
            $topic = new LearningTopic($validated);
            $topic->created_by = $userId;
        }

        if (empty($topic->slug)) {
            $topic->slug = Str::slug($topic->title) . '-' . Str::random(6);
        }

        if (empty($topic->difficulty)) {
            $topic->difficulty = 'beginner';
        }

        $topic->is_active  = $validated['is_active'] ?? true;
        $topic->updated_by = $userId;
        $topic->save();

        // Rebuild assignments
        LearningTopicAssignment::where('learning_topic_id', $topic->id)->delete();

        $departmentIds = $validated['department_ids'] ?? [];
        foreach ($departmentIds as $id) {
            LearningTopicAssignment::create([
                'learning_topic_id' => $topic->id,
                'assignable_type'   => Department::class,
                'assignable_id'     => $id,
            ]);
        }

        $positionIds = $validated['position_ids'] ?? [];
        foreach ($positionIds as $id) {
            LearningTopicAssignment::create([
                'learning_topic_id' => $topic->id,
                'assignable_type'   => Position::class,
                'assignable_id'     => $id,
            ]);
        }

        $employeeIds = $validated['employee_ids'] ?? [];
        foreach ($employeeIds as $id) {
            LearningTopicAssignment::create([
                'learning_topic_id' => $topic->id,
                'assignable_type'   => Employee::class,
                'assignable_id'     => $id,
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'topic'  => $topic->fresh(['media']),
        ]);
    }

    public function destroy(LearningTopic $topic)
    {
        $topic->delete();

        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function uploadMedia(Request $request, LearningTopic $topic)
    {
        $request->validate([
            'file'        => 'required|file|max:51200', // 50 MB
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('learning_media', 'public');
        $mime = $file->getClientMimeType();

        $mediaType = 'file';
        if (str_starts_with($mime, 'image/')) {
            $mediaType = 'image';
        } elseif (str_starts_with($mime, 'video/')) {
            $mediaType = 'video';
        } elseif (str_starts_with($mime, 'audio/')) {
            $mediaType = 'audio';
        }

        $media = LearningTopicMedia::create([
            'learning_topic_id' => $topic->id,
            'media_type'        => $mediaType,
            'title'             => $request->input('title'),
            'description'       => $request->input('description'),
            'file_path'         => $path,
            'mime_type'         => $mime,
            'sort_order'        => 0,
        ]);

        return response()->json([
            'status' => 'ok',
            'media'  => $media,
            'url'    => asset('storage/' . $media->file_path),
        ]);
    }

    public function deleteMedia(LearningTopicMedia $media)
    {
        if ($media->file_path && Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return response()->json([
            'status' => 'ok',
        ]);
    }
}
