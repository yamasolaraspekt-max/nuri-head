<?php

namespace App\Http\Controllers;

use App\Models\CustomerRoomDimension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;  
use Illuminate\Support\Facades\Log;



class CustomerRoomDimensionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
  
        
  public function store(Request $request)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'customer_id' => 'required|integer|exists:customers,id', 
        'room.*.room_number' => 'required|integer',
        'room.*.dimension_type' => 'required|string',
        'room.*.width' => 'required|numeric',
        'room.*.height' => 'required|numeric',
        'room.*.ceiling_height' => 'nullable|numeric',
        'room.*.stair_form' => 'nullable|string',
        'room.*.stair_width' => 'nullable|numeric',
        'room.*.room_story' => 'nullable|string',
    ]);

    // Return validation errors if they exist
    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    // Process and save each room's dimension
    foreach ($request->input('room') as $room) {
        // Prepare data to save, only including optional fields if they exist
        $data = [
            'customer_id' => $request->customer_id, 
            'room_number' => $room['room_number'],
            'dimension_type' => $room['dimension_type'],
            'width' => $room['width'],
            'height' => $room['height'],
            'room_story' => $room['room_story'],
        ];

        // Only add optional fields if they are set in the input
        if (isset($room['ceiling_height'])) {
            $data['ceiling_height'] = $room['ceiling_height'];
        }

        if (isset($room['stair_form'])) {
            $data['stair_form'] = $room['stair_form'];
        }

        if (isset($room['stair_width'])) {
            $data['stair_width'] = $room['stair_width'];
        }

        // Save the room dimension data
        CustomerRoomDimension::create($data);
    }

    return response()->json(['success' => true, 'message' => 'Room dimensions saved successfully.']);
}


 
    public function destroy($id)
    {
        try {
            $roomDimension = CustomerRoomDimension::findOrFail($id);
            $roomDimension->delete();
            
            return response()->json(['success' => true, 'message' => 'Room deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting room']);
        }
    }

    public function edit($id)
    {
        try {
            $roomDimension = CustomerRoomDimension::findOrFail($id);
            
            return response()->json(['success' => true, 'data' => $roomDimension]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching room details']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Retrieve the room dimension by its ID
            $roomDimension = CustomerRoomDimension::findOrFail($id);

            // Initialize variables for fields that depend on dimension_type
            $ceilingHeight = $request->ceiling_height;
            $stairForm = $request->stair_form;
            $stairWidth = $request->stair_width;

            // If the dimension type is 'Wand', set related fields to null
            if ($request->dimension_type === 'Wand') {
                $ceilingHeight = null;
                $stairForm = null;
                $stairWidth = null;
            }

            // Update the room dimension with the appropriate data
            $roomDimension->update([
                'room_number' => $request->room_number,
                'dimension_type' => $request->dimension_type,
                'width' => $request->width,
                'height' => $request->height,
                'ceiling_height' => $ceilingHeight,  // Null if 'Wand'
                'stair_form' => $stairForm,          // Null if 'Wand'
                'stair_width' => $stairWidth,        // Null if 'Wand'
                'room_story' => $request->room_story,
            ]);

            return response()->json(['success' => true, 'message' => 'Room updated successfully']);
        } catch (\Exception $e) {
            // Handle any errors that occur during the update process
            return response()->json(['success' => false, 'message' => 'Error updating room']);
        }
    }


    public function getRoomDimensions($customer_id)
    {
        try {
            $roomDimensions = CustomerRoomDimension::where('customer_id', $customer_id)->get();

            return response()->json([
                'success' => true,
                'data' => $roomDimensions
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error fetching room dimensions']);
        }
    }




}
