<?php

namespace App\Http\Controllers;

use App\Models\ChecklistApartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 
use DB;


class ChecklistApartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index($customer_id, $address_no)
    {
        $data = DB::table('checklist_apartments')
                    ->where('customer_id', $customer_id)
                    ->where('address_no', $address_no)
                    ->get();

        return response()->json($data, 200);
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
    try {
        // Log incoming request for debugging
        Log::info('The Information', $request->all());

        // Validate the incoming request data
        $validated = $request->validate([
            'stories.*.story' => 'required|string',
            'stories.*.unit' => 'required|string',
            'stories.*.usable_space' => 'nullable|numeric',
            'stories.*.heating_living_space' => 'nullable|numeric',
            'stories.*.living_space' => 'required|numeric',
            'customer_id' => 'required|integer|exists:customers,id',
            'address_no' => 'required|integer',
        ]);

        // Loop through each story and create records
        foreach ($validated['stories'] as $story) {
            ChecklistApartment::create([
                'story' => $story['story'],
                'unit' => $story['unit'],
                'heating_living_space' => $story['heating_living_space'],
                'usable_space' => $story['usable_space'],
                'living_space' => $story['living_space'],
                'customer_id' => $request->customer_id,
                'address_no' => $request->address_no,
            ]);
        }

        return response()->json(['save_msg' => 'Die Wohnung wurde erfolgreich gespeichert']);
    } catch (\Exception $e) {
        Log::error('Error saving apartment checklist:', [
            'message' => $e->getMessage(),
            'stack' => $e->getTraceAsString(),
        ]);

        return response()->json(['error' => 'Ein interner Fehler ist aufgetreten.'], 500);
    }
}






    /**
     * Display the specified resource.
     */
    public function show(ChecklistApartment $checklistApartment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChecklistApartment $checklistApartment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, $id)
{
    try {
        // Log the incoming request data for debugging
        Log::info('Update Request Data:', $request->all());

        // Validate the incoming request with individual fields
        $validated = $request->validate([
            'story' => 'required|string',
            'unit' => 'required|string',
            'usable_space' => 'nullable|numeric',
            'heating_living_space' => 'nullable|numeric',
            'living_space' => 'required|numeric', 
        ]);

        // Find the existing record by id
        $checklistApartment = ChecklistApartment::findOrFail($id);

        // Update the record with the new data from the request
        $checklistApartment->update([
            'story' => $request->input('story'),
            'unit' => $request->input('unit'),
            'usable_space' => $request->input('usable_space'),
            'heating_living_space' => $request->input('heating_living_space'),
            'living_space' => $request->input('living_space'),
        ]);

        return response()->json(['success' => 'The apartment data was successfully updated']);
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Error updating apartment:', [
            'message' => $e->getMessage(),
            'stack' => $e->getTraceAsString(),
        ]);

        return response()->json(['error' => 'An internal server error occurred. Please check the logs for details.'], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = ChecklistApartment::find($id);

        if ($data) {
            $data->delete();
            return response()->json(['success' => 'The data deleted successfully']);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

}
