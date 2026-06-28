<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\PVRoof;
use Illuminate\Support\Facades\Log;

class PVRoofController extends Controller
{
    /**
     * Get all roofs for a given customer and alternative.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index($customer_id, $alternative_id)
    {
        $roofs = PVRoof::where('customer_id', $customer_id)
            ->where('alternative_id', $alternative_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $roofs
        ]);
    }

    /**
     * Store a new roof.
     */
    public function store(Request $request)
    {
        // Log the incoming request
        Log::info('Roof Store Request Data:', $request->all());

        // Validate incoming data
        $validatedData = $request->validate([
            'customer_id' => 'required|integer',
            'alternative_id' => 'required|integer',
            'designation' => 'array',
            'designation.*' => 'nullable|string|max:255',
            'roof' => 'array',
            'roof.*' => 'nullable|string|max:50',
            'roof_covering' => 'array',
            'roof_covering.*' => 'nullable|integer',
            'tilt' => 'array',
            'tilt.*' => 'nullable|string|max:50',
            'pv_insulation' => 'array',
            'pv_insulation.*' => 'nullable',
            'thickness_roof_insulation' => 'array',
            'thickness_roof_insulation.*' => 'nullable|string|max:50',
            'between_rafter_insulation' => 'array',
            'between_rafter_insulation.*' => 'nullable',
            'thickness_between_rafter' => 'array',
            'thickness_between_rafter.*' => 'nullable|string|max:50',
            'asbestos' => 'array',
            'asbestos.*' => 'nullable',
            'roof_renovation' => 'array',
            'roof_renovation.*' => 'nullable',
        ]);

        try {
            // Loop through the array fields and save data
            foreach ($validatedData['designation'] as $index => $designation) {
                PVRoof::create([
                    'customer_id' => $validatedData['customer_id'],
                    'alternative_id' => $validatedData['alternative_id'],
                    'designation' => $designation,
                    'roof' => $validatedData['roof'][$index] ?? null,
                    'roof_covering' => $validatedData['roof_covering'][$index] ?? null,
                    'tilt' => $validatedData['tilt'][$index] ?? null,
                    'roof_insulation' => $validatedData['pv_insulation'][$index] ?? null,
                    'thickness_roof_insulation' => $validatedData['thickness_roof_insulation'][$index] ?? null,
                    'between_rafter_insulation' => $validatedData['between_rafter_insulation'][$index] ?? null,
                    'thickness_between_rafter' => $validatedData['thickness_between_rafter'][$index] ?? null,
                    'asbestos' => $validatedData['asbestos'][$index] ?? null,
                    'roof_renovation' => $validatedData['roof_renovation'][$index] ?? null,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Roof data saved successfully!'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error storing roof data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save data. Please try again.'
            ], 500);
        }
    }


     public function update(Request $request)
    {
        // Log the incoming request
        Log::info('Roof Store Request Data:', $request->all());

        // Validate incoming data
        $validatedData = $request->validate([
            'id'    =>  'required',
            'customer_id' => 'required|integer',
            'alternative_id' => 'required|integer',
            'designation' => 'array',
            'designation.*' => 'nullable|string|max:255',
            'roof' => 'array',
            'roof.*' => 'nullable|string|max:50',
            'roof_covering' => 'array',
            'roof_covering.*' => 'nullable|integer',
            'tilt' => 'array',
            'tilt.*' => 'nullable|string|max:50',
            'pv_insulation' => 'array',
            'pv_insulation.*' => 'nullable',
            'thickness_roof_insulation' => 'array',
            'thickness_roof_insulation.*' => 'nullable|string|max:50',
            'between_rafter_insulation' => 'array',
            'between_rafter_insulation.*' => 'nullable',
            'thickness_between_rafter' => 'array',
            'thickness_between_rafter.*' => 'nullable|string|max:50',
            'asbestos' => 'array',
            'asbestos.*' => 'nullable',
            'roof_renovation' => 'array',
            'roof_renovation.*' => 'nullable',
        ]);

        try {
            // Loop through the array fields and save data
            foreach ($validatedData['designation'] as $index => $designation) {
                PVRoof::find($request->id)->update([ 
                    'designation' => $designation,
                    'roof' => $validatedData['roof'][$index] ?? null,
                    'roof_covering' => $validatedData['roof_covering'][$index] ?? null,
                    'tilt' => $validatedData['tilt'][$index] ?? null,
                    'roof_insulation' => $validatedData['pv_insulation'][$index] ?? null,
                    'thickness_roof_insulation' => $validatedData['thickness_roof_insulation'][$index] ?? null,
                    'between_rafter_insulation' => $validatedData['between_rafter_insulation'][$index] ?? null,
                    'thickness_between_rafter' => $validatedData['thickness_between_rafter'][$index] ?? null,
                    'asbestos' => $validatedData['asbestos'][$index] ?? null,
                    'roof_renovation' => $validatedData['roof_renovation'][$index] ?? null,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Roof data saved successfully!'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error storing roof data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save data. Please try again.'
            ], 500);
        }
    }
    /**
     * Delete a roof.
     */
    public function destroy($roof_id)
    {
        $roof = PVRoof::find($roof_id);

        if (!$roof) {
            return response()->json([
                'success' => false,
                'message' => 'Dach nicht gefunden!'
            ], 404);
        }

        $roof->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dach erfolgreich gelöscht!'
        ]);
    }



        public function edit(Request $request)
    {
        \Log::info('Roof PV;', [$request->all()]);
       $request->validate([
            'customer_id' => 'required|exists:new_leads,id',
            'alternative_id' => 'required|exists:lead_alternative_adds,id',
            'roof_id' => 'required|exists:p_v_roofs,id',
            'field' => 'required|string',
            'value' => 'nullable|string'
        ]);


        $pvChecklist = PVRoof::firstOrCreate([
            'customer_id' => $request->customer_id,
            'alternative_id' => $request->alternative_id,
            'id' => $request->roof_id
        ]);

    

        $pvChecklist->update([$request->field => $request->value]); 
        
        return response()->json(['success' => true, 'message' => 'Field updated successfully', 'data' => $pvChecklist]);
    }
}
