<?php

namespace App\Http\Controllers\Product\PV;
use App\Http\Controllers\Controller;
use App\Models\Inverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class InverterController extends Controller
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

        $data = Inverter::where('product_id', $product_id)
                        ->where('article_group_id', $article_group_id)
                        ->first();

        if ($data) {
            return response()->json($data);
        } else {
            return response()->json(['message' => 'No data found'], 404);
        }
    }
   public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'nullable|integer',
            'article_group_id' => 'nullable|integer', 
            'company' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'available' => 'nullable|string',
            'version' => 'nullable|string|max:255', 
            'user_id' => 'nullable|string|max:255',
            'dc_nominal_power' => 'nullable|numeric',
            'max_dc_power' => 'nullable|numeric',
            'dc_nominal_voltage' => 'nullable|integer',
            'max_input_voltage' => 'nullable|integer',
            'max_input_current' => 'nullable|integer',
            'max_dc_short_circuit_current' => 'nullable|integer',
            'num_dc_inputs' => 'nullable|integer',
            'ac_nominal_power' => 'nullable|numeric',
            'max_ac_power' => 'nullable|numeric',
            'ac_nominal_voltage' => 'nullable|integer',
            'num_phases' => 'nullable|integer',
            'with_transformer' => 'nullable|string',
            'efficiency_change' => 'nullable|numeric',
            'min_feed_power' => 'nullable|integer',
            'standby_consumption' => 'nullable|numeric',
            'night_consumption' => 'nullable|numeric',
            'power_range_below_20' => 'nullable|numeric',
            'power_range_above_20' => 'nullable|numeric',
            'parallel_operation' => 'nullable|string|max:255',
            'num_mpp_trackers' => 'nullable|integer',
            'identical_properties' => 'nullable|string',
            'max_input_current_per_mpp' => 'nullable|integer',
            'max_short_circuit_current_per_mpp' => 'nullable|integer',
            'max_input_power_per_mpp' => 'nullable|numeric',
            'min_mpp_voltage' => 'nullable|integer',
            'max_mpp_voltage' => 'nullable|integer',
            'efficiency_0' => 'nullable|numeric',
            'efficiency_5' => 'nullable|numeric',
            'efficiency_10' => 'nullable|numeric',
            'efficiency_20' => 'nullable|numeric',
            'efficiency_30' => 'nullable|numeric',
            'efficiency_50' => 'nullable|numeric',
            'efficiency_75' => 'nullable|numeric',
            'efficiency_100' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $inverter = Inverter::updateOrCreate(
            ['product_id' => $request->product_id, 'article_group_id' => $request->article_group_id],
            $request->except(['_token']) // Exclude the CSRF token
        );
        } catch (\Exception $e) {
             Log::error('Error saving Power Optimizer configuration: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Inverter $inverter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inverter $inverter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inverter $inverter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inverter $inverter)
    {
        //
    }
}
