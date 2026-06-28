<?php
namespace App\Http\Controllers\Product\PV;
use App\Http\Controllers\Controller;

use App\Models\Battery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class BatteryController extends Controller
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

        $data = Battery::where('product_id', $product_id)
                        ->where('article_group_id', $article_group_id)
                        ->first();

        return response()->json($data);
        if ($data) {
            return response()->json($data);
        } else {
            return response()->json(['message' => 'No data found'], 404);
        }
    }
    /**
     * Store a newly created resource in storage.\
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'nullable|integer',
            'article_group_id' => 'nullable|integer', 
            'company' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'user_id'   =>  'nullable',
            'available' => 'nullable',
            'version' => 'nullable|string|max:255', 
            'battery_type' => 'nullable|string|max:255',
            'cell_voltage' => 'nullable|numeric',
            'num_cells' => 'nullable|integer',
            'nominal_voltage' => 'nullable|numeric',
            'num_strings' => 'nullable|integer',
            'internal_resistance' => 'nullable|numeric',
            'self_discharge' => 'nullable|numeric',
            'dod_20' => 'nullable|integer',
            'dod_40' => 'nullable|integer',
            'dod_60' => 'nullable|integer',
            'dod_80' => 'nullable|integer',
            'capacity_10min' => 'nullable|numeric',
            'capacity_30min' => 'nullable|numeric',
            'capacity_1h' => 'nullable|numeric',
            'capacity_5h' => 'nullable|numeric',
            'capacity_10h' => 'nullable|numeric',
            'capacity_100h' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $battery = Battery::updateOrCreate(
            ['product_id' => $request->product_id, 'article_group_id' => $request->article_group_id],
            $request->except(['_token']) // Exclude the CSRF token
        );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Battery $battery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Battery $battery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Battery $battery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Battery $battery)
    {
        //
    }
}
