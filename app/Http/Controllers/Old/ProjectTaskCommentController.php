<?php

namespace App\Http\Controllers;

use App\Models\ProjectTaskComment;
use App\Models\PhaseActivities;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class ProjectTaskCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index($projectId, $phaseId, $activityId)
    {
       $userId = auth()->id(); // or ->user()->name if you're using names 
        $comments = ProjectTaskComment::with(['employee', 'replies.employee'])
            ->where('project_id', $projectId)
            ->where('phase_id', $phaseId)
            ->where('activity_id', $activityId)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        // Add ownership flags
        $comments->transform(function ($comment) use ($userId) {
            $comment->is_my_comment = $comment->employee_id === $userId;

            $comment->replies->transform(function ($reply) {
                $reply->is_my_comment = false; // Replies can't be edited/deleted
                return $reply;
            });

            return $comment;
        });

        return response()->json($comments);
    }

    public function store(Request $request)
    {
        Log::info('📥 New Comment Data:', $request->all());

        $validated = $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'phase_id'     => 'required|exists:task_phases,id',
            'activity_id'  => 'required|exists:phase_activities,id',
            'comment'      => 'required|string',
            'parent_id'    => 'nullable|exists:project_task_comments,id',
        ]);

        $validated['employee_id'] = auth()->user()->name;
        $validated['status'] = 'Published';

        $comment = ProjectTaskComment::create($validated)->load('employee');

        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }

    public function update(Request $request, $id)
    {
        $comment = ProjectTaskComment::findOrFail($id);

        if ($comment->employee_id !== Auth::id()) {
            return response()->json(['message' => 'Nicht erlaubt'], 403);
        }

        $comment->update(['comment' => $request->comment]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $comment = ProjectTaskComment::findOrFail($id);

        if ($comment->employee_id !== Auth::id()) {
            return response()->json(['message' => 'Nicht erlaubt'], 403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }

    public function count($activityId)
    {
        return response()->json([
            'count' => ProjectTaskComment::where('activity_id', $activityId)->count()
        ]);
    }

    public function getMeta($projectId)
    {
        $project = Project::with('customer', 'product')->find($projectId);
    
        if (!$project) {
            return response()->json(['error' => 'Not found'], 404);
        }
    
        return response()->json([
            'customer_name' => $project->customer->name ?? '-',
            'address' => $project->customer->address ?? '-',
            'product' => $project->product->name ?? '-',
        ]);
    }
    


}
