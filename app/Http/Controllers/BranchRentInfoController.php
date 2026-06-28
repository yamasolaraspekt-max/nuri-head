<?php

namespace App\Http\Controllers;

use App\Models\BranchRent;
use App\Models\BranchRentInfo;
use App\Models\RentExtraCost;
use App\Models\RentProperty;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Log;  
use DB;

class BranchRentInfoController extends Controller
{
    
    public function __construct(){
        $this->middleware('auth');
    }

 
 public function store(Request $request)
{
    // Validate the request data
    $validator = \Validator::make($request->all(), [
        'expense_details_id'   => 'required|exists:branch_expenses,id',
        'object_id'            => 'required|exists:branch_rents,id',
        'apartment_id'         => 'required|exists:rent_properties,id',
        'cold_rent'            => 'required|integer',
        'electricity_cost'     => 'required|integer',
        'heating_cost'         => 'required|integer',
        'repair_cost'          => 'required|integer',
        'extra'                => 'nullable|array',
        'extra.*.name'         => 'nullable|string',
        'extra.*.cost'         => 'nullable|integer',
        'extra.*.paid_to'      => 'nullable|string',
        'extra.*.company'      => 'nullable|string',
        'payee'                => 'required',
        'payment_date'         => 'required|date',
        'status'               => 'nullable|string'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Check if the rent already exists
    $rent = BranchRentInfo::where('apartment_id', $request->apartment_id)->first();
    if ($rent) {
        return response()->json(['error' => 'Sie können keine neuen Mietinformationen hinzufügen, da diese bereits vorhanden sind'], 409);
    }

    // Calculate the total base costs
    $BasicTotal =  $request->electricity_cost + $request->heating_cost + $request->repair_cost;

    // Initialize extra cost total
    $extraCostsTotal = 0;

    // Add extra costs to the total where paid_to is "Vermieter"
    if (!empty($request->extra)) {
        foreach ($request->extra as $extra) {
            if (!empty($extra['cost']) && isset($extra['paid_to']) && $extra['paid_to'] === 'Vermieter') {
                $extraCostsTotal += $extra['cost'];
            }
        }
    }

    try {
        // Create the main BranchRentInfo record
        $branchRentInfo = BranchRentInfo::create([
            'expense_details_id'    => $request->expense_details_id,
            'object_id'             => $request->object_id,
            'apartment_id'          => $request->apartment_id,
            'cold_rent'             => $request->cold_rent,
            'electricity_cost'      => $request->electricity_cost,
            'heating_cost'          => $request->heating_cost,
            'repair_cost'           => $request->repair_cost,
            'payment_date'          => $request->payment_date,
            'payee'                 => $request->payee, 
            'status'                => 'Published'
        ]);

        // Save extra costs if any
        if ($branchRentInfo && !empty($request->extra)) {
            $branch_id = $branchRentInfo->id;
            foreach ($request->extra as $extra) {
                if (!empty($extra['name']) && !empty($extra['cost'])) {
                    RentExtraCost::create([
                        'title' => $extra['name'],
                        'cost' => $extra['cost'],
                        'paid_to' => $extra['paid_to'],
                        'company' => $extra['company'],
                        'branch_rent_infos_id' => $branch_id,
                        'status' => 'Published'
                    ]);
                }
            }
        }

        // Calculate the warm rent
        $warm_cost = $request->cold_rent + $BasicTotal + $extraCostsTotal;
        $totalMinusRent = $BasicTotal + $extraCostsTotal;
        // Update BranchRentInfo with warm rent
        BranchRentInfo::find($branchRentInfo->id)->update(['total'=>$warm_cost, 'extra_cost' => $extraCostsTotal]);
        RentProperty::where('id', $request->apartment_id)->update(['extra_cost' => $totalMinusRent]);

        return response()->json(['success' => 'Datensatz erfolgreich gespeichert']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Fehler beim Speichern: ' . $e->getMessage()], 500);
    }
}



 
    /**
     * Display the specified resource.
     */
    public function show(BranchRentInfo $branchRentInfo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BranchRentInfo $branchRentInfo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BranchRentInfo $branchRentInfo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {  
        $data= BranchRentInfo::findOrFail($id);

        RentProperty::where('id', $data->apartment_id)->update(['extra_cost'=>0]);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'The data is deleted successfully');
    }
}
