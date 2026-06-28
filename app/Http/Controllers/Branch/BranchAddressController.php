<?php

namespace App\Http\Controllers\Branch;
use App\Http\Controllers\Controller;

use App\Models\BranchAddress;
use Illuminate\Http\Request;
use DB;
class BranchAddressController extends Controller
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
        \Log::info('The data received: ', [$request->all()]);

        $validated = $request->validate([ 
            'name' => 'required|string|max:255',
            'full_address' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'postcode' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'employee_id' => 'required|exists:employees,id',  
            'branch_id' => 'required|exists:branches,id',  
        ]);

        // Manually set the status and branch_id (adjust according to your logic)
        $validated['status'] = 'active'; 

        // Save the branch data to the database
        BranchAddress::create($validated);

        return response()->json([
            'message' => 'Zweig erfolgreich hinzugefügt.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(BranchAddress $branchAddress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data['data'] = BranchAddress::find($id);
        $data['employee']=DB::table('employees')->select('id', 'name', 'lastname', 'image', 'gender')->where('status', 'Active')->get();
        
        return view('admin.branch.branch_address_edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
{
    \Log::info('new fields', [$request->all()]);
    $validated = $request->validate([ 
        'name' => 'required|string|max:255',
        'full_address' => 'required|string|max:255',
        'street' => 'required|string|max:255',
        'postcode' => 'required|string|max:10',
        'city' => 'required|string|max:255',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'phone' => 'required|string|max:15',
        'email' => 'required|email|max:255',
        'employee_id' => 'required|exists:employees,id',  
        'branch_id' => 'required|exists:branches,id',  
        'id' => 'required|exists:branch_addresses,id'
    ]);

    // Manually set the status
    $validated['status'] = 'active';

    // Update the branch address
    $branchAddress = BranchAddress::findOrFail($request->id);
    $branchAddress->update($validated);

    return response()->json([
        'message' => 'Zweig erfolgreich aktualisiert.',
        'branch_id' => $validated['branch_id'],  // Include the branch_id for redirect
    ]);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $branchAddress = BranchAddress::findOrFail($id);
        $branchAddress->delete();

        return response()->json([
            'message' => 'Filialadresse erfolgreich gelöscht.',
        ]);
    }

}
