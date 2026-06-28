<?php

namespace App\Http\Controllers;

use App\Models\RentExtraCost;
use App\Models\BranchRentInfo;
use App\Models\RentProperty;
use Illuminate\Http\Request;
use DB;

class RentExtraCostController extends Controller
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
        $validator = \Validator::make($request->all(), [
            'expense_details_id'   => 'required|exists:branch_expenses,id',
            'object_id'            => 'required|exists:branch_rents,id',
            'apartment_id'         => 'required|exists:rent_properties,id', 
            'rent_id'              => 'required|exists:branch_rent_infos,id', 
            'extra'                => 'nullable|array',
            'extra.*.name'         => 'nullable|string',
            'extra.*.cost'         => 'nullable|integer',
            'extra.*.paid_to'      => 'nullable|string',
            'extra.*.company'      => 'nullable|string', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

          $extraCostsTotal = 0;

            // Add extra costs to the total where paid_to is "Vermieter"
            if (!empty($request->extra)) {
                foreach ($request->extra as $extra) {
                    if (!empty($extra['cost']) && isset($extra['paid_to']) && $extra['paid_to'] === 'Vermieter') {
                        $extraCostsTotal += $extra['cost'];
                    }
                }
            }

        // Loop through and create new records
        foreach ($request->extra as $extra) {
            if (!empty($extra['name']) && !empty($extra['cost'])) {
                RentExtraCost::create([
                    'title' => $extra['name'],
                    'cost' => $extra['cost'],
                    'paid_to' => $extra['paid_to'],
                    'company' => $extra['company'],
                    'branch_rent_infos_id' => $request->rent_id, // Assuming this is branch_rent_infos_id
                    'status' => 'Published'
                ]);
            }
        }

        $branch_infos = DB::table('branch_rent_infos')->where('id', $request->rent_id)->first();  
        $p_extra_cost = $branch_infos->extra_cost;
        $p_total = $branch_infos->total;
        
        $current_extra = $p_extra_cost + $extraCostsTotal;
        $current_total = $p_total + $extraCostsTotal;

        BranchRentInfo::findOrFail($request->rent_id)->update(['extra_cost'=>$current_extra, 'total'=>$current_total]);
        RentProperty::findOrFail($request->apartment_id)->update(['extra_cost'=>$current_extra]);
    

        return response()->json(['success' => 'Extra cost saved successfully']);
    }


    /**
     * Display the specified resource.
     */
    public function show(RentExtraCost $rentExtraCost)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RentExtraCost $rentExtraCost)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RentExtraCost $rentExtraCost)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $cost, $rent)
    {
        // Find the rent info by id
        $rentInfo = BranchRentInfo::find($rent);
        if (!$rentInfo) {
            return response()->json(['error' => 'Rent info not found'], 404);
        }

        // Find the rent property by apartment_id
        $rent_property = RentProperty::find($rentInfo->apartment_id);
        if (!$rent_property) {
            return response()->json(['error' => 'Rent property not found'], 404);
        }

        // Adjust the total and extra cost
        $currentTotal = $rentInfo->total - $cost;
        $currentExtra = $rentInfo->extra_cost - $cost;

        // Update the rent info and rent property
        $rentInfo->update(['extra_cost' => $currentExtra, 'total' => $currentTotal]);
        $rent_property->update(['extra_cost' => $currentExtra]);

        // Find the extra cost by id and delete it
        $data = RentExtraCost::find($id);
        if ($data) {
            $data->delete();
        } else {
            return response()->json(['error' => 'Extra cost not found'], 404);
        }

        return response()->json(['success' => 'The record was deleted successfully']);
    }


}
