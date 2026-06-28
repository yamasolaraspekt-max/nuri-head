<?php

namespace App\Http\Controllers;

use App\Models\BranchRent;
use App\Models\ExpenseType;
use App\Models\RentProperty;
use Illuminate\Http\Request;

class RentPropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
   public function store(Request $request)
{
    // Validate the request data
    $request->validate([
        'owner' => 'required|string|max:255',
        'contract_date' => 'required|date', 
        'cold_rent' => 'required|numeric|min:0',
        'warm_rent' => 'required|numeric|min:0',
        'advance_rent' => 'nullable|numeric|min:0',
        'iban' => 'required|string|max:255',
        'termination_date' => 'nullable|date|after_or_equal:contract_date',
        'termination_type' => 'nullable|string',
        'parking' => 'nullable|integer|min:0',
        'parking_cost' => 'nullable|numeric|min:0',
    ]);

    // Calculate total rent cost
    $total = $request->cold_rent + $request->warm_rent;

    // Store the rent property data in the database
    $data = RentProperty::create([
        'object_id' => $request->object_id,
        'owner' => $request->owner, 
        'living_space' => $request->living_space,
        'parking' => $request->parking,
        'parking_cost' => $request->parking_cost,
        'parking_count' => $request->parking_count,
        'contract_type' => $request->contract_type,
        'contract_date' => $request->contract_date,
        'termination_date' => $request->termination_date, 
        'termination_type' => $request->termination_type,
        'cold_rent' => $request->cold_rent,
        'warm_rent' => $request->warm_rent,
        'advance_rent' => $request->advance_rent,
        'bank_user' => $request->bank_user,
        'bank_name' => $request->bank_name,
        'iban' => $request->iban,
        'status' => $request->status,
    ]);

    // Update the corresponding BranchRent record with rent data
    BranchRent::where('expense_details_id', '=', $request->expense_details_id)
        ->where('id', '=', $request->object_id)
        ->update([
            'rent_cost' => $request->cold_rent,
            'extra_cost' => $request->warm_rent,
            'total' => $total
        ]);

    // Return JSON response
    if ($data) {
        return response()->json(['success' => true, 'message' => 'Der Datensatz wurde erfolgreich gespeichert']);
    } else {
        return response()->json(['success' => false, 'message' => 'Der Datensatz konnte nicht gespeichert werden']);
    }
}



    /**
     * Display the specified resource.
     */
    public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='images/branch/contract/'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function save_pdf(Request $request)
    {
        $request->validate([
            'pdf_file'   =>  'required|mimes:pdf|max:4048',
        ]);

        $data = RentProperty::find($request->id);
        
        if($request->hasFile('pdf_file')){
            $this->delete_photo($data->contract);
            $image_name=time().'.'.$request->file('pdf_file')->getClientOriginalExtension();
            $request->file('pdf_file')->move('images/branch/contract/', $image_name);
            $data->contract=$image_name;
            $data->save();
            
            return redirect()->back()->with('save_msg', 'Das PDF wurde hochgeladen');

        }
        else{
            $data->save();
            return redirect()->back()->with('delete_msg', 'Das PDF wurde nicht hochgeladen');

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
        'owner'  =>  'required',
        'contract_date'=>  'required',
         'contract_duration'=>  'required',
        'contract_duration_type'=>  'required',
        'cold_rent'=>  'required',
        'warm_rent'=>  'required',
        'advance_rent'=>  'required',
        'iban'=>  'required',
         ]);
         $total= $request->cold_rent + $request->warm_rent;

         $id=request()->input('id');
         $data = RentProperty::find($id);  
         $data->update($request->all());
         BranchRent::where('expense_details_id', '=', $request->expense_details_id)->where('id', '=', $request->object_id)->update([
            'rent_cost' =>  $request->cold_rent,
            'extra_cost' =>  $request->warm_rent,
            'total'     =>  $total
    ]);
         if($data){
             return redirect()->back()->with('save_msg', 'Der Datensatz erfulgreich gespeischert');
         }
         else{
             return redirect()->back()->with('delete_msg', 'Der Datensatz nicht gespeischert');
 
         }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = RentProperty::find($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'Der Datensatz erfulgreich gelöscht');
        
    }

    public function publish($id)
    {
        $data = RentProperty::find($id);

        $data->status="Published";
        $data->save();
        return redirect()->back()->with('save_msg', 'Das Datensatz wird veröffentlicht');
        
    }

    public function unpublish($id)
    {
        $data = RentProperty::find($id);
        $data->status="Unpublished";
        $data->save();
        return redirect()->back()->with('save_msg', 'Das Datensatz wird nicht veröffentlicht');
    }

   public function cold_rent($id)
    {
        $data = RentProperty::find($id);
        $cold = $data->cold_rent;

        return response()->json($cold, 200);
    }

}
