<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductPV;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ProductPVController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index($id, $article)
    {
        return view('admin.product.pv.pv_create')->with('id', $id)->with('article', $article);
    }

 
   public function load(Request $request)
{
    $product_id = $request->input('product_id');
    $article_group_id = $request->input('article_group_id');

    $data = ProductPV::where('product_id', $product_id)
                    ->where('article_group_id', $article_group_id)
                    ->first();

    return response()->json($data);
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
            'product_type' => 'nullable|string',
            'cell_type' => 'nullable|string|max:255',
            'half_cell_module' => 'nullable|string',
            'num_cells' => 'nullable|integer',
            'num_bypass_diodes' => 'nullable|integer',
            'voltage_loss_per_bypass_diode' => 'nullable|numeric',
            'integrated_power_optimizer' => 'nullable|string|max:255',
            'trafo_inverter_only' => 'nullable|string',
            'cell_strands_vertical' => 'nullable|string',

            // UI Kennwerte bei STC
            'mpp_voltage' => 'nullable|numeric',
            'mpp_current' => 'nullable|numeric',
            'open_circuit_voltage' => 'nullable|numeric',
            'short_circuit_current' => 'nullable|numeric',
            'voltage_increase_before_stabilization' => 'nullable|numeric',
            'nominal_power' => 'nullable|numeric',
            'fill_factor' => 'nullable|numeric',
            'efficiency' => 'nullable|numeric',

            // UI Kennwerte bei Schwachlicht
            'low_light_model' => 'nullable|string|max:255',
            'irradiance' => 'nullable|numeric',
            'mpp_voltage_low_light' => 'nullable|numeric',
            'mpp_current_low_light' => 'nullable|numeric',
            'open_circuit_voltage_low_light' => 'nullable|numeric',
            'short_circuit_current_low_light' => 'nullable|numeric',
            'fill_factor_low_light' => 'nullable|numeric',
            'efficiency_low_light' => 'nullable|numeric',
            'standard_low_light_behavior' => 'nullable|string',

            // Weitere Parameter
            'temperature_coefficient_voc' => 'nullable|numeric',
            'temperature_coefficient_voc_pct' => 'nullable|numeric',
            'temperature_coefficient_isc' => 'nullable|numeric',
            'temperature_coefficient_isc_pct' => 'nullable|numeric',
            'temperature_coefficient_pmax' => 'nullable|numeric',
            'angle_correction_factor' => 'nullable|numeric',
            'max_system_voltage' => 'nullable|numeric',
            'bifaciality_factor' => 'nullable|numeric',

            // Abmessungen
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'area' => 'nullable|numeric',
            'depth' => 'nullable|numeric',
            'frame_width' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $pvModule = ProductPV::updateOrCreate(
            ['product_id' => $request->product_id, 'article_group_id' => $request->article_group_id],
            $request->except(['_token']) // Exclude the CSRF token
        );

            return response()->json(['message' => 'PV module configuration saved successfully!', 'pvModule' => $pvModule], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }
 
    /**
     * Display the specified resource.
     */
    public function show(ProductPV $productPV)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductPV $productPV)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductPV $productPV)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductPV $productPV)
    {
        //
    }
}
