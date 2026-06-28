<?php
namespace App\Http\Controllers\Product\PV;
use App\Http\Controllers\Controller;
use App\Models\ElectricVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
class ElectricVehicleController extends Controller
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

        $data = ElectricVehicle::where('product_id', $product_id)
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
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'nullable|integer',
            'article_group_id'  =>  'required',
            'company' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'available' => 'nullable',
            'version' => 'nullable|string|max:255', 
            'user_id' => 'nullable|string|max:255',
            'range_wltp' => 'nullable|integer',
            'consumption' => 'nullable|numeric',
            'battery_capacity' => 'nullable|numeric',
            'discharge_power' => 'nullable|numeric',
            'motor_power' => 'nullable|numeric',
            'empty_weight' => 'nullable|integer',
            'max_speed' => 'nullable|integer',
            'payload' => 'nullable|integer',
            'seats' => 'nullable|integer',
            'charging_technology' => 'nullable|string|max:255',
            'charging_power' => 'nullable|numeric',
            'discharge_for_consumption' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $electricVehicle = ElectricVehicle::updateOrCreate(
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
    public function show(ElectricVehicle $electricVehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ElectricVehicle $electricVehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ElectricVehicle $electricVehicle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ElectricVehicle $electricVehicle)
    {
        //
    }
}
