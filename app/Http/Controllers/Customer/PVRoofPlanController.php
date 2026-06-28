<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use App\Models\PVRoofPlan;
use App\Models\PVLongRoof;
use Illuminate\Http\Request;

class PVRoofPlanController extends Controller
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
    try {
        \Log::info('Received request data:', $request->all());

        // Validate the roof data
        $validatedRoof = $request->validate([
            'product_id' => 'required|integer',
            'roof_id' => 'required|integer',
            'roof_dimensions' => 'nullable|string',
            'rafter_left_overhang' => 'nullable|string',
            'roof_width' => 'nullable|string',
            'roof_height' => 'nullable|string',
            'rafter_right_overhang' => 'nullable|string',
            'rafter_thickness' => 'nullable|string',
            'rafter_reinforcement_needed' => 'nullable|string',
            'statics_available' => 'nullable|string',
            'conduit_available' => 'nullable|string',
            'cable_routing_through' => 'nullable|string',
            'lightning_protection' => 'required|string',
            'geplante_termin' => 'nullable|date',
            'dachdecker' => 'nullable|string',
            'dauer' => 'nullable|string',
            'ort' => 'nullable|string',
            'solarhalteziegel' => 'nullable|string',
            'ansprechpartner' => 'nullable|string',
            'geliefert_durch' => 'nullable|string',
            'geruestnutzung' => 'nullable|string',
        ]);

        // Save the main roof data
        $roofData = PVLongRoof::create($validatedRoof);

        if (!$roofData) {
            return response()->json(['message' => 'Please save the main form first.'], 400);
        }

        // Validate the plan data
        $validatedPlans = $request->validate([
            'plan.*.roof_structures' => 'required|string',
            'plan.*.planned_action' => 'required|string',
            'plan.*.planned_note' => 'nullable|string',
        ]);

        // Iterate over each plan and save it
        if (is_array($request->plan)) {
            foreach ($request->plan as $plan) {
                PVRoofPlan::create([
                    'product_id' => $roofData->product_id,
                    'roof_id' => $roofData->id,
                    'roof_structures' => $plan['roof_structures'],
                    'planned_action' => $plan['planned_action'],
                    'planned_note' => $plan['planned_note'] ?? null,
                ]);
            }
        } else {
            return response()->json(['message' => 'Invalid plan data.'], 400);
        }

        return response()->json(['message' => 'Checklist items saved successfully!', 'roofData' => $roofData], 200);
    } catch (\Exception $e) {
        \Log::error('Error occurred while saving checklist items:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json(['message' => 'An error occurred while saving the checklist items.'], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(PVRoofPlan $pVRoofPlan)
    {
      
    }

 

        public function edit(Request $request)
    {
        \Log::info('Roof PV;', [$request->all()]);
        $request->validate([
            'roof_customer' => 'required|exists:new_leads,id',
            'roof_alternative' => 'required|exists:lead_alternative_adds,id',
            'roof_id' => 'required|exists:p_v_roofs,id',
            'field' => 'required|string',
            'value' => 'nullable|string'
        ]);

        $pvChecklist = PVRoofPlan::firstOrCreate([
            'customer_id' => $request->roof_customer,
            'alternative_id' => $request->roof_alternative
        ]);

    

        $pvChecklist->update([$request->field => $request->value]); 
        
        return response()->json(['success' => true, 'message' => 'Field updated successfully', 'data' => $pvChecklist]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PVRoofPlan $pVRoofPlan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PVRoofPlan $pVRoofPlan)
    {
        //
    }
}
