<?php

namespace App\Http\Controllers\Product\PV;
use App\Http\Controllers\Controller;

use App\Models\BatterySystem;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;



class BatterySystemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function load(Request $request)
    {
        $product_id = $request->input('product_id');
        $article_group_id = $request->input('article_group_id');

        $data = BatterySystem::where('product_id', $product_id)
                             ->where('article_group_id', $article_group_id)
                             ->first();

        if ($data) {
            return response()->json($data);
        } else {
            return response()->json(['message' => 'No data found'], 404);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'article_group_id' => 'required|integer', 
            'ess_company' => 'nullable|string|max:255',
            'ess_name' => 'nullable|string|max:255',
            'ess_description' => 'nullable|string',
            'ess_available' => 'nullable|string',
            'ess_version' => 'nullable|string|max:255', 
            'ess_user_id' => 'nullable|string|max:255',
            'nominal_power' => 'nullable|numeric',
            '_max_charge_power' => 'nullable|numeric',
            'max_discharge_power' => 'nullable|numeric',
            'coupling_type' => 'nullable|string|max:255',
            'ess_efficiency_0' => 'nullable|numeric',
            'ess_efficiency_5' => 'nullable|numeric',
            'ess_efficiency_10' => 'nullable|numeric',
            'ess_efficiency_20' => 'nullable|numeric',
            'ess_efficiency_30' => 'nullable|numeric',
            'ess_efficiency_50' => 'nullable|numeric',
            'ess_efficiency_75' => 'nullable|numeric',
            'ess_efficiency_100' => 'nullable|numeric',
            'ess_equalization_charge' => 'nullable|numeric',
            'ess_equalization_charge_end' => 'nullable|numeric',
            'ess_equalization_charge_duration' => 'nullable|numeric',
            'ess_equalization_charge_cycle' => 'nullable|numeric',
            'ess_full_charge' => 'nullable|numeric',
            'ess_full_charge_end' => 'nullable|numeric',
            'ess_full_charge_duration' => 'nullable|numeric',
            'ess_full_charge_cycle' => 'nullable|numeric',
            'ess_maintenance_charge' => 'nullable|numeric',
            'ess_uo_charge' => 'nullable|numeric',
            'ess_uo_charge_end' => 'nullable|numeric',
            'ess_uo_charge_duration' => 'nullable|numeric',
            'ess_i_charge' => 'nullable|numeric',
            'ess_i_charge_end' => 'nullable|numeric',
            'ess_battery' => 'nullable|string|max:255',
            'ess_num_batteries_per_string' => 'nullable|integer',
            'ess_num_battery_strings' => 'nullable|integer',
            'ess_system_voltage' => 'nullable|numeric',
            'ess_usable_energy' => 'nullable|numeric',
            'ess_capacity_c10' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $batterySystem = BatterySystem::updateOrCreate(
            ['product_id' => $request->product_id, 'article_group_id' => $request->article_group_id],
            $request->except(['_token']) // Exclude the CSRF token
        );
        } catch (\Exception $e) {
             Log::error('Error saving battery system configuration: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }

          
    }

    /**
     * Display the specified resource.
     */
    public function show(BatterySystem $batterySystem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BatterySystem $batterySystem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BatterySystem $batterySystem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BatterySystem $batterySystem)
    {
        //
    }
}
