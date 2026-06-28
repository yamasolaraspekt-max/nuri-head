<?php

namespace App\Http\Controllers\Product\PV;
use App\Http\Controllers\Controller;
use App\Models\BatteryInverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BatteryInverterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   
     public function __construct(){
        $this->middleware('auth');
     }
    public function load(Request $request)
    {
        $product_id = $request->input('product_id');
        $article_group_id = $request->input('article_group_id');

        $data = BatteryInverter::where('product_id', $product_id)
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
    public function store(Request $request){
  
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'article_group_id' => 'required|integer', 
            'company' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'available' => 'nullable|string',
            'version' => 'nullable|string|max:255', 
            'user_id' => 'nullable|string|max:255',
            'nominal_voltage' => 'nullable|integer',
            'max_ac_current' => 'nullable|integer',
            'continuous_power' => 'nullable|integer',
            'power_30min' => 'nullable|integer',
            'power_60min' => 'nullable|integer',
            'no_load_consumption' => 'nullable|integer',
            'standby_consumption' => 'nullable|integer',
            'battery_voltage' => 'nullable|integer',
            'min_battery_voltage' => 'nullable|integer',
            'max_battery_voltage' => 'nullable|integer',
            'max_battery_charge_current' => 'nullable|integer',
            'efficiency_0' => 'nullable|numeric',
            'efficiency_5' => 'nullable|numeric',
            'efficiency_10' => 'nullable|numeric',
            'efficiency_20' => 'nullable|numeric',
            'efficiency_30' => 'nullable|numeric',
            'efficiency_50' => 'nullable|numeric',
            'efficiency_75' => 'nullable|numeric',
            'efficiency_100' => 'nullable|numeric',
            'max_devices_per_phase_single' => 'nullable|integer',
            'max_devices_per_phase_dual' => 'nullable|integer',
            'max_clusters' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $batteryInverter = BatteryInverter::updateOrCreate(
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
    public function show(BatteryInverter $batteryInverter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BatteryInverter $batteryInverter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BatteryInverter $batteryInverter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BatteryInverter $batteryInverter)
    {
        //
    }
}
