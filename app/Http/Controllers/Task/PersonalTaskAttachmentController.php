<?php

namespace App\Http\Controllers\Task;
use App\Http\Controllers\Controller;

use App\Models\PersonalTaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Notifications\PersonalTaskNotification;
use Illuminate\Support\Facades\Notification;
use DB;

class PersonalTaskAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function uploadFiles(Request $request)
{
    $validate = $request->validate([
        'file' => 'required',
        'file.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,txt|max:2048',
        'task_id' => 'required|exists:personal_tasks,id',
    ]);

    \Log::info('validation of files: ', [$request->all()]);

    if ($request->hasFile('file')) {
        $files = is_array($request->file('file')) ? $request->file('file') : [$request->file('file')];

        foreach ($files as $file) {
             $originalName = $file->getClientOriginalName(); // Original file name

            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            try {
                $file->move('images/task/personal/document', $fileName);

                PersonalTaskAttachment::create([
                    'task_id' => $request->task_id,
                    'image_name' => $originalName,
                    'image' => $fileName,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            } catch (\Exception $e) {
                \Log::error('File upload error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'File upload failed.');
            }
        }

        $emp = DB::table('employees')
            ->select('name', 'lastname', 'id')
            ->where('id', auth()->user()->name)
            ->first();

        $name = $emp->name . ' ' . $emp->lastname;

        Notification::send(auth()->user(), new PersonalTaskNotification([
            'title' => 'Dateien hochgeladen',
            'message' => 'Mehrere Dateien wurden von ' . $name . ' hochgeladen',
            'task_id' => $request->task_id,
        ]));

        return redirect()->back()->with('success', 'Alle Dateien wurden erfolgreich hochgeladen!');
    } else {
        return redirect()->back()->with('error', 'Die Dateien konnten nicht hochgeladen werden.');
    }
}



    public function deleteFile($filePath)
    {
        if (!empty($filePath)) {
            $fullPath = public_path($filePath);

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function index($task_id)
    {
        $data = PersonalTaskAttachment::where('task_id', $task_id)->get();

        // Corrected logging
        if ($data->isEmpty()) {
            \Log::info("No files found for task_id: $task_id");
        } else {
            \Log::info("Files found for task_id: $task_id", $data->toArray());
        }

        return response()->json($data, 200);
    }

 
   public function update(Request $request)
    {
        // Validate the incoming request
        $validate = $request->validate([
            'id'        => 'required|exists:personal_task_attachments,id',
            'task_id'   => 'required|integer|exists:personal_tasks,id',
            'image_name'=> 'required|string',
        ]);

        try {
            // Find the record
            $data = PersonalTaskAttachment::findOrFail($request->id);

            // Fetch the previous name before updating
            $previous_name = $data->image_name;

            // Update the image name
            $data->update([
                'image_name' => $request->image_name,
            ]);

            // Retrieve the authenticated employee's details
            $emp = DB::table('employees')
                ->select('name', 'lastname')
                ->where('id', auth()->user()->name)
                ->first();

            if (!$emp) {
                throw new \Exception('Employee not found for the current user.');
            }

            $name = $emp->name . ' ' . $emp->lastname;

            // Send Notification
            Notification::send(auth()->user(), new PersonalTaskNotification([
                'title'   => 'Dateiname geändert',
                'message' => 'Der Dateiname [' . $previous_name . '] wurde in [' . $request->image_name . '] geändert von ' . $name,
                'task_id' => $request->task_id,
            ]));

            return response()->json(['success' => 'File name updated successfully!']);

        } catch (\Exception $e) {
            // Log error details
            Log::error('Error updating file name:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */

    public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='images/task/personal/document'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }
   public function destroy($id)
    {
        $file = PersonalTaskAttachment::findOrFail($id);
         $this->delete_photo($file->image_name);
  
            $emp = DB::table('employees')
            ->select('name', 'lastname')
            ->where('id', auth()->user()->name)
            ->first();

        if (!$emp) {
            throw new \Exception('Employee not found for the current user.');
        }

        $name = $emp->name . ' ' . $emp->lastname;

        // Send Notification
        Notification::send(auth()->user(), new PersonalTaskNotification([
            'title'   => 'Datei gelöscht',
            'message' => 'Die Datei ['.$file->image_name.'] wurde gelöscht von '.$name,
            'task_id' => $file->task_id,
        ]));


        $file->delete();

        return response()->json(['success' => 'File deleted successfully!'], 200);
    }

}
