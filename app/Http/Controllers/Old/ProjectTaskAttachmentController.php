<?php

namespace App\Http\Controllers;

use App\Models\ProjectTaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class ProjectTaskAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }

    public function index($projectId, $phaseId, $activityId)
    {
        $attachments = ProjectTaskAttachment::with('uploader')
            ->where('project_id', $projectId)
            ->where('phase_id', $phaseId)
            ->where('activity_id', $activityId)
            ->latest()
            ->get();

        return response()->json($attachments);
    }



   public function store(Request $request)
{
    \Log::info('file uploaded', [$request->all()]);

    $request->validate([
        'project_id' => 'required|exists:projects,id',
        'phase_id' => 'required|exists:task_phases,id',
        'activity_id' => 'nullable|exists:phase_activities,id',
        'file' => 'required|file|max:10240',
    ]);

    if ($request->hasFile('file')) {
        $uploadedFile = $request->file('file');

        $originalName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $uploadedFile->getClientOriginalExtension();
        $filename = time() . '_' . Str::slug($originalName) . '.' . $extension;

        $destinationPath = public_path('images/projects');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $uploadedFile->move($destinationPath, $filename);

        $attachment = new ProjectTaskAttachment();
        $attachment->project_id = $request->project_id;
        $attachment->phase_id = $request->phase_id;
        $attachment->activity_id = $request->activity_id;
        $attachment->upload_by = auth()->user()->name;
        $attachment->image = $filename; // ✅ Store only filename
        $attachment->image_name = $uploadedFile->getClientOriginalName();
        $attachment->file_type = $extension;
        $attachment->save();

        return response()->json([
            'id' => $attachment->id,
            'image' => $attachment->image,
            'image_url' => asset('images/projects/' . $attachment->image), // ✅ full usable URL
            'image_name' => $attachment->image_name,
            'file_type' => $attachment->file_type,
            'created_at' => $attachment->created_at,
            'uploader' => [
                'name' => optional($attachment->uploader)->name ?? 'N/A',
            ],
        ]);
    }

    return response()->json(['error' => 'No file uploaded'], 422);
}



    /**
     * Display the specified resource.
     */
    public function rename(Request $request)
    {
        $validate = $request->validate([
            'id' => 'required|exists:project_task_attachments,id',
            'image_name' => 'required|string|max:255' // Validate the new name
        ]);

        $data = ProjectTaskAttachment::find($request->id);
        $data->image_name = $request->image_name;
        $data->save();

        return response()->json(['success' => true, 'message' => 'The file renamed successfully']);
    }
 

  public function update(Request $request, ProjectTaskAttachment $attachment)
    {
        $request->validate(['image_name' => 'required|string|max:255']);
        $attachment->update(['image_name' => $request->image_name]);
        return response()->json(['success' => true]);
    }

   public function destroy(ProjectTaskAttachment $attachment): JsonResponse
    {
        // Delete file from storage if it exists
        $filePath = public_path('images/projects/' . $attachment->image);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

        // Delete database record
        $attachment->delete();

        return response()->json(['success' => true, 'message' => 'Attachment deleted successfully.']);
    }


    public function getByActivity($activityId)
    {
        $userId = auth()->user()->name;

        $files = ProjectTaskAttachment::with('uploader')
            ->where('activity_id', $activityId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Add ownership flag
        $files->transform(function ($file) use ($userId) {
            $file->is_my_upload = $file->upload_by === $userId;
            return $file;
        });

        return response()->json($files);
    }

 

}



