<?php

namespace App\Http\Controllers;

use App\Models\WPChecklist;
use App\Models\CustomerWPCable;
use App\Models\CustomerMeterCabinet;

use Illuminate\Http\Request;


class WPChecklistController extends Controller
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
    // Validate the incoming request data
    $validatedData = $request->validate([
        'customer_id' => 'required|integer',
        'postcode' => 'required|string|max:10',
        'meter_cabinet' => 'nullable|string',
        'cabinet_size' => 'nullable|integer',
        'meter_cabinet_company' => 'nullable|integer',
        'wp_meter_adapter_plate' => 'nullable',
        'wp_ac_surge_protection' => 'nullable',
        'wp_ac_switch' => 'nullable',
        'wp_apz_field' => 'nullable',
        'wp_disconnect_relay' => 'nullable',
        'wp_equipotential_bonding' => 'nullable',
        'wp_objective' => 'nullable|string',
        'wp_object' => 'nullable|string',
        'wp_heating_type' => 'nullable|string',
        'construction_year' => 'nullable|integer',
        'living_space' => 'nullable|integer',
        'unusable_space' => 'nullable|integer',
        'number_people' => 'nullable|integer',
        'wp_number_we' => 'nullable|integer',
        'wp_number_stories' => 'nullable|integer',
        'glass1' => 'nullable',
        'glass2' => 'nullable',
        'glass3' => 'nullable',
        'window_margin' => 'nullable|string',
        'insulation_thickness' => 'nullable|integer',
        'wall_type' => 'nullable|string',
        'wall_thickness' => 'nullable|integer',
        'wp_insulation' => 'nullable',
        'wp_insulation_strength' => 'nullable|integer',
        'wp_rafter' => 'nullable',
        'wp_rafter_strength' => 'nullable|integer',
        'wp_bathrooms' => 'nullable|integer',
        'wp_bathtub' => 'nullable|string',
        'wp_bathtub_count' => 'nullable|integer',
        'wp_bathtub_measure' => 'nullable|string',
        'wp_swimming_pool' => 'nullable|string',
        'wp_swimming_pool_count' => 'nullable|integer',
        'solor' => 'nullable|string',
        'number_collector' => 'nullable|integer',
        'chimney' => 'nullable|string',
        'chimney_usage' => 'nullable',
        'hlb_calc' => 'nullable|string',
        'energy_first_year_consumption' => 'nullable',
        'energy_second_year_consumption' => 'nullable',
        'energy_third_year_consumption' => 'nullable',
        'energy_consumption_type' => 'nullable|string',
        'energy_total_year_consumption' => 'nullable',
        'energy_avg_year_consumption' => 'nullable',
        'energy_first_year_cost' => 'nullable',
        'energy_second_year_cost' => 'nullable',
        'energy_third_year_cost' => 'nullable',
        'energy_total_year_cost' => 'nullable',
        'energy_avg_year_cost' => 'nullable',
        'heatpump' => 'nullable|string',
        'exhibition_location' => 'nullable|string',
        'exhibation_location_note' => 'nullable|string',
        'heating_manufacture_year' => 'nullable|integer',
        'heating_type' => 'nullable|string',
        'system_performance' => 'nullable|integer',
        'heating_company' => 'nullable|string',
        'type_designation' => 'nullable|string',
        'hot_water_preparation' => 'nullable|string',
        'number_hotWaterConsumptionPerPerson' => 'nullable|integer',
        'general_heating_system' => 'nullable|string',
        'pipe_system' => 'nullable|string',
        'heating_circuit_distributor' => 'nullable|string',
        'actuators' => 'nullable|string',
        'suitable_cooling_system' => 'nullable|string',
        'radiator' => 'nullable|string',
        'thermostats' => 'nullable|string',
        'thermostatic_valves' => 'nullable|string',
        'radiator_cooling_system' => 'nullable|string',
        'radiator_note' => 'nullable|string',
        'ventilation'   =>  'nullable',
        'ventilation_system'  =>  'nullable',
        'ventilation_company'  =>  'nullable',
        'ventilation_type'  =>  'nullable',
        'heat_recovery'  =>  'nullable',
        'cable' => 'nullable|array', 
        'cable.*.system' => 'nullable|string',
        'cable.*.type' => 'nullable|string',
        'cable.*.dimension' => 'nullable|string',
        'cable.*.company' => 'nullable|string',
        'cable.*.designation' => 'nullable|string',
        'cable.*.note' => 'nullable|string',
    ]);

    // Use updateOrCreate to update an existing checklist or create a new one
    $wpChecklist = WPChecklist::updateOrCreate(
        ['customer_id' => $validatedData['customer_id']], // condition to find existing record
        $validatedData // data to update or create
    );

    // Save the cable data
    if (isset($validatedData['cable'])) {
        foreach ($validatedData['cable'] as $cableData) {
           CustomerWPCable::updateOrCreate(
                [
                    'customer_id' => $validatedData['customer_id'],
                    'postcode' => $validatedData['postcode'],
                    'system' => $cableData['system'] ?? null,  
                ], 
                [
                    'type' => $cableData['type'] ?? null,
                    'dimension' => $cableData['dimension'] ?? null,
                    'company' => $cableData['company'] ?? null,
                    'designation' => $cableData['designation'] ?? null,
                    'note' => $cableData['note'] ?? null,
                ]
            );
        }
    }

    // Save or update meter cabinet data
    CustomerMeterCabinet::updateOrCreate(
        [
            'customer_id' => $validatedData['customer_id'], 
            'postcode' => $validatedData['postcode'],
        ],
        [
            'meter_cabinet' => $validatedData['meter_cabinet'] ?? null,
            'cabinet_size' => $validatedData['cabinet_size'] ?? null,
            'meter_cabinet_company' => $validatedData['meter_cabinet_company'] ?? null,
            'wp_meter_adapter_plate' => $validatedData['wp_meter_adapter_plate'] ?? false,
            'wp_ac_surge_protection' => $validatedData['wp_ac_surge_protection'] ?? false,
            'wp_ac_switch' => $validatedData['wp_ac_switch'] ?? false,
            'wp_apz_field' => $validatedData['wp_apz_field'] ?? false,
            'wp_disconnect_relay' => $validatedData['wp_disconnect_relay'] ?? false,
            'wp_equipotential_bonding' => $validatedData['wp_equipotential_bonding'] ?? false,
        ]
    );




    // Return a response, e.g., redirect or JSON response
    return redirect()->back()->with('save_msg', 'The checklist and cables were saved successfully');
}

    /**
     * Display the specified resource.
     */
    public function show(WPChecklist $wPChecklist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WPChecklist $wPChecklist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WPChecklist $wPChecklist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WPChecklist $wPChecklist)
    {
        //
    }
}
