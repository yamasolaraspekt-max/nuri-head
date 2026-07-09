<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;   
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use DB;


class EmployeeDocumentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR: HR-Rollen-Gate (permission:Employee), enforced mit heutigen user_rolls-Grants
        $this->middleware('permission:Employee,update')->only(['update']);
        $this->middleware('permission:Employee,delete')->only(['destroy']);
    }

      public function upload(Request $request)
{
    \Log::info('Request received:', $request->all());

    try {
        // Validate the incoming request
        $request->validate([
            'file' => 'required', // Ensure the file field exists
            'file.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // Validate multiple files
            'employee_id' => 'required|exists:employees,id', 
            'type' => 'required|string', // Allow string values for stage_id
        ]);

        \Log::info('Validation passed successfully.');

        // Handle both single and multiple file uploads
        $files = is_array($request->file('file')) ? $request->file('file') : [$request->file('file')];

        \Log::info('Files detected for upload:', ['file_count' => count($files)]);

        foreach ($files as $file) {
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'images/employee/document/' . $fileName;

            \Log::info('Processing file:', [
                'original_name' => $file->getClientOriginalName(),
                'file_name' => $fileName,
                'file_path' => $filePath,
            ]);

            // Move the file to the desired location
            if ($file->move('images/employee/document/', $fileName)) {
                \Log::info('File moved successfully.', ['file_name' => $fileName]);
            } else {
                \Log::error('Failed to move file.', ['file_name' => $fileName]);
                continue;
            }

            // Save the file information to the database
            $data = new EmployeeDocument();
            $data->employee_id = $request->employee_id; 
            $data->type = $request->type;
            $data->image_name = $fileName;
            $data->image = $fileName;
            $data->status = $request->status;
            $data->created_by = auth()->user()->name;
            $data->file_type = $file->getClientOriginalExtension();

            if ($data->save()) {
                \Log::info('File information saved in the database.', $data->toArray());
            } else {
                \Log::error('Failed to save file information to the database.', ['file_name' => $fileName]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Alle Dateien wurden erfolgreich hochgeladen!',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation failed:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validierung fehlgeschlagen.',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Upload failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Etwas ist schief gelaufen.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    
    public function destroy($id)
    {
        $image = EmployeeDocument::find($id);
        if ($image) {
            // Delete the image file
            $this->delete_photo($image->image);
            // Delete the image record
            $image->delete();
            $image->updated_by = auth()->user()->name;

            return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Image not found'], 404);
    }
    
    protected function delete_photo($image)
    {
        if ($image && file_exists('images/employee/document/' . $image)) {
            unlink('images/employee/document/' . $image);
        }
    }
 

  


    public function update(Request $request){
       $validate= $request->validate([
            'image_name'    =>  'required',
            'id'    =>  'required|exists:employee_documents,id'
        ]);

        $data = EmployeeDocument::find($validate['id']);
        $data->image_name = $request->image_name;
        $data->updated_by = auth()->user()->name;
        $data->save();
        return response()->json(['success' => 'Image updated successfully']);
    }

public function getImage($employee, $type)
{
    $images = EmployeeDocument::where('employee_id', $employee) 
                            ->where('type', $type)
                            ->get();

    $imageData = $images->map(function ($image) {
        return [
            'id'          => $image->id,
            'image_name'  => $image->image_name,
            'image'     =>  $image->image,
            'file_path'   =>   asset('images/employee/document/' . $image->image),  // Corrected path
        ];  
    });

    Log::info('Loaded Images:', $imageData->toArray());
    return response()->json(['data' => $imageData]);
}



public function getLicense($employee, $type)
{
    $images = EmployeeDocument::where('employee_id', $employee) 
                            ->where('type', $type)
                            ->get();

    $imageData = $images->map(function ($image) {
        return [
            'id'          => $image->id,
            'image_name'  => $image->image_name,
            'image'     =>  $image->image,
             'file_path'   =>   asset('images/employee/document/' . $image->image),  // Corrected path
             'file_type'   =>   $image->file_type,  // Corrected path
        ];  
    });

    Log::info('Loaded Images:', $imageData->toArray());
    return response()->json(['data' => $imageData]);
}
public function getDocument($employee_id) {
    $images = EmployeeDocument::where('employee_id', $employee_id)->get();

    if ($images->isEmpty()) {
        Log::warning("⚠️ No documents found for employee ID: $employee_id");
        return response()->json(['message' => 'No documents found', 'data' => []], 404);
    }

    $imageData = $images->map(function ($image) {
        return [
            'id'         => $image->id,
            'image_name' => $image->image_name,
            'file_type'  => $image->file_type,
            'type'       => $image->type,
            'file_path'  => asset('images/employee/document/' . $image->image),
        ];
    });

    Log::info("✅ Documents Found:", $imageData->toArray());
    return response()->json(['data' => $imageData]);
}


}
