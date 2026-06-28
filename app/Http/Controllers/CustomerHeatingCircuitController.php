<?php

namespace App\Http\Controllers;

use App\Models\CustomerHeatingCircuit;
use App\Models\Customer;
use DB;
use Illuminate\Support\Facades\Validator;  


use Illuminate\Http\Request;

class CustomerHeatingCircuitController extends Controller
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
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer|exists:customers,id', 
            'heatingData.*.heating_circuit_number' => 'required|integer',
            'heatingData.*.flow_temperature' => 'required|numeric',
            'heatingData.*.return_flow_temperature' => 'required|numeric',
            'heatingData.*.room_story' => 'required|string',
            'heatingData.*.pipe_dimension' => 'required|string',
            'heatingData.*.pipe_material' => 'required|string',
        ]);

        // If validation fails, return a 422 response with the errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // If validation passes, save the data
        $heatingData = $request->input('heatingData');
        foreach ($heatingData as $heating) {
            CustomerHeatingCircuit::create([
                'customer_id' => $request->customer_id, 
                'heating_circuit_number' => $heating['heating_circuit_number'],
                'flow_temperature' => $heating['flow_temperature'],
                'return_flow_temperature' => $heating['return_flow_temperature'],
                'room_story' => $heating['room_story'],
                'pipe_dimension' => $heating['pipe_dimension'],
                'pipe_material' => $heating['pipe_material']
            ]);
        }

        return response()->json(['success' => true]);
    }

 
    public function getSingleHeatingCircuit($id)
    {
        $heatingCircuit = CustomerHeatingCircuit::find($id);
        if (!$heatingCircuit) {
            return response()->json(['success' => false, 'message' => 'Heating circuit not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $heatingCircuit]);
    }


    public function getHeatingCircuits($customer_id)
    {
        // Validate if the customer exists first (optional but recommended)
        $customer = Customer::find($customer_id);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        // Retrieve heating circuits for the customer
        $heatingCircuits = CustomerHeatingCircuit::where('customer_id', $customer_id)->get();

        // Return heating circuits as a JSON response
        return response()->json([
            'success' => true,
            'data' => $heatingCircuits
        ]);
    }



    /**
     * Update the specified resource in storage.
     */
   public function updateHeatingCircuit(Request $request, $id)
    {
        // Validate request data
        $validated = $request->validate([
            'heating_circuit_number' => 'required|integer',
            'flow_temperature' => 'required|numeric',
            'return_flow_temperature' => 'required|numeric',
            'room_story' => 'required|string',
            'pipe_dimension' => 'required|string',
            'pipe_material' => 'required|string'
        ]);

        // Find the heating circuit by id and update it
        $heatingCircuit = CustomerHeatingCircuit::find($id);
        if (!$heatingCircuit) {
            return response()->json(['success' => false, 'message' => 'Heating circuit not found.'], 404);
        }

        $heatingCircuit->update($validated);

        return response()->json(['success' => true, 'message' => 'Heating circuit updated successfully.']);
    }


    /**
     * Remove the specified resource from storage.
     */
    // Delete heating circuit
        public function delete($id) {
            CustomerHeatingCircuit::find($id)->delete();
            return response()->json(['success' => true]);
        }
}
