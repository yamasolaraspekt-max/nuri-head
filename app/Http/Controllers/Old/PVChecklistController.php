<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PVChecklist;
use App\Models\PVRoof;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use DB;

class PVChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

   

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
{

    
    // Log the incoming request data for debugging
    Log::info('Incoming request data:', $request->all());

    $validator = Validator::make($request->all(), [
        'customer_id' => 'required|exists:customers,id',
        'intention' => 'required|in:Interesse,vorhanden,Erweiterung,später',
        'property_type' => 'required|in:EFH,MFH,Neubau,Sanierung,Einzelmaßnahmen',
        'electricity_consumption' => 'nullable|numeric',
        'electric_car' => 'nullable|string',
        'number_of_electric_cars' => 'nullable|integer',
        'wallbox_desired' => 'required|string',
        'number_of_wallboxes' => 'nullable|integer',
        'meter_cabinet' => 'nullable|string',
        'meter_cabinet_company' => 'nullable|integer',
        'cabinet_size' => 'nullable|string',
        'cabinet_size_sonstiges' => 'nullable|string',
        'designation' => 'required|array',
        'designation.*' => 'required|string',
        'tiles' => 'required|array',
        'tiles.*' => 'required|integer',
        'pv_insulation' => 'nullable|array',
        'pv_insulation.*' => 'nullable|string',
        'insulation_strength' => 'nullable|array',
        'insulation_strength.*' => 'nullable|integer',
        'rafter' => 'nullable|array',
        'rafter.*' => 'nullable|string',
        'rafter_strength' => 'nullable|array',
        'rafter_strength.*' => 'nullable|integer',
        'postcode' => 'required|string',
        'address_no' => 'required|string',
        'number_of_units' => 'nullable|integer',
        'number_of_meters' => 'nullable|integer',
        'asbestos' => 'nullable|array',
        'asbestos.*' => 'nullable|string',
        'roof_renovation' => 'nullable|array',
        'roof_renovation.*' => 'nullable|string',
        'tilt' => 'nullable|array',
        'tilt.*' => 'nullable|integer',
    ]);

    if ($validator->fails()) {
        Log::error('Validation failed:', $validator->errors()->toArray());
        return response()->json(['errors' => $validator->errors()], 422);
    }

    DB::beginTransaction();
    try {
        $duplicate = PVChecklist::where('customer_id', $request->customer_id)
            ->where('postcode', $request->postcode)
            ->where('address_no', $request->address_no)
            ->first();

        if ($duplicate) {
            // Update existing record
            $data = $duplicate;
        } else {
            // Create new record
            $data = new PVChecklist;
        }

        $data->fill($request->only([
            'customer_id', 'postcode', 'address_no', 'intention', 'property_type', 'number_of_units', 'number_of_meters',
            'electricity_consumption', 'electric_car', 'number_of_electric_cars', 'wallbox_desired', 'number_of_wallboxes',
            'meter_cabinet', 'meter_cabinet_company', 'cabinet_size', 'cabinet_size_sonstiges',
            'meter_adapter_plate', 'ac_surge_protection', 'sls_switch', 'apz_field', 'disconnect_relay', 'equipotential_bonding'
        ]));
        $data->save();

        // Update or create PVRoof entries
        if ($duplicate) {
            // Delete existing PVRoof entries for the updated PVChecklist
            PVRoof::where('pv_id', $duplicate->id)->delete();
        }

        // Ensure arrays have the same length to avoid undefined index errors
        $designations = $request->input('designation', []);
        $tiles = $request->input('tiles', []);
        $pvInsulations = $request->input('pv_insulation', []);
        $thichness_roof_insulation = $request->input('thickness_roof_insulation', []);
        $rafters = $request->input('between_rafter_insulation', []);
        $rafterStrengths = $request->input('thickness_between_rafter', []);
        $roofs = $request->input('roof', []);
        $asbestos = $request->input('asbestos', []); 
        $roofRenovation = $request->input('roof_renovation', []);
        $tilt = $request->input('tilt', []);
        $construction_fluid = $request->input('construction_fluid', []);
        $numberUnit = $request->input('number_of_units', []);
        $numberMeters = $request->input('number_of_meters', []);
        $electricityConsumption = $request->input('electricity_consumption', []);
        $electric_car = $request->input('electric_car', []);
        $numberElectricCar = $request->input('number_of_electric_cars', []);
        $wallbox_desired = $request->input('wallbox_desired', []);
        $number_of_wallboxes = $request->input('number_of_wallboxes', []);
        $meter_cabinet = $request->input('meter_cabinet', []);
        $cabinet_size = $request->input('cabinet_size', []);
        $meter_cabinet_company = $request->input('meter_cabinet_company', []);
        $meter_adapter_plate = $request->input('meter_adapter_plate', []);
        $ac_surge_protection = $request->input('ac_surge_protection', []);
        $ac_switch = $request->input('ac_switch', []);
        $apz_field = $request->input('apz_field', []);
        $disconnect_relay = $request->input('disconnect_relay', []);
        $equipotential_bonding = $request->input('equipotential_bonding', []); 


        foreach ($designations as $index => $designation) {
            $roofData = new PVRoof;
            $roofData->pv_id = $data->id;
            $roofData->designation = $designation;
            $roofData->roof_covering = $tiles[$index];
            $roofData->roof_insulation = $pvInsulations[$index] ?? null;
            $roofData->thickness_roof_insulation = $thichness_roof_insulation[$index] ?? null;
            $roofData->between_rafter_insulation = $rafters[$index] ?? null;
            $roofData->thickness_between_rafter = $rafterStrengths[$index] ?? null;
            $roofData->asbestos = $asbestos[$index] ?? null;
            $roofData->roof_renovation = $roofRenovation[$index] ?? null;
            $roofData->construction_fluid = $construction_fluid[$index] ?? null;
            $roofData->tilt = $tilt[$index] ?? null;
            $roofData->roof = $roofs[$index] ?? null;
            $roofData->roof_renovation = $roofRenovation[$index] ?? null; 
            $roofData->construction_fluid = $construction_fluid[$index] ?? null;    
            $roofData->number_of_units = $numberUnit[$index] ?? null;    
            $roofData->number_of_meters = $numberMeters[$index] ?? null;   
            $roofData->electricity_consumption = $electricityConsumption[$index] ?? null;    
            $roofData->electric_car = $electric_car[$index] ?? null;     
            $roofData->number_of_electric_cars = $numberElectricCar[$index] ?? null;      
            $roofData->wallbox_desired = $wallbox_desired[$index] ?? null;      
            $roofData->number_of_wallboxes = $number_of_wallboxes[$index] ?? null;    
            $roofData->meter_cabinet = $meter_cabinet[$index] ?? null;    
            $roofData->cabinet_size = $cabinet_size[$index] ?? null;    
            $roofData->meter_cabinet_company = $meter_cabinet_company[$index] ?? null;     
            $roofData->meter_adapter_plate = $meter_adapter_plate[$index] ?? null;   
            $roofData->ac_surge_protection = $ac_surge_protection[$index] ?? null;    
            $roofData->sls_switch = $ac_switch[$index] ?? null;   
            $roofData->apz_field = $apz_field[$index] ?? null;   
            $roofData->disconnect_relay = $disconnect_relay[$index] ?? null;    
            $roofData->equipotential_bonding = $equipotential_bonding[$index] ?? null;     
            $roofData->save();
        }

        DB::commit();
        return response()->json(['message' => $data->id . ($duplicate ? ' The record is updated successfully' : ' this is the id')]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error saving data:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json(['message' => 'Internal server error'], 500);
    }
}




    /**
     * Display the specified resource.
     */
    public function show(PVChecklist $pVChecklist)
    {
           $data['tiles'] = DB::table('products')
                            ->join('product_images', 'product_images.product_id', '=', 'products.id')
                            ->select('products.*', 'product_images.image')
                            ->where('products.category', '=', 'Dachziegel')
                            ->get();
            $data['image_category'] = DB::table('image_categories')->get();
        return view('admin.customer.products_details.pv_details', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PVChecklist $pVChecklist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PVChecklist $pVChecklist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PVChecklist $pVChecklist)
    {
        //
    }
}
