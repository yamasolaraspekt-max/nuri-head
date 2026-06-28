<?php

namespace App\Http\Controllers;

use App\Models\BranchContractDetails;
use App\Models\BranchRent;
use App\Models\RentProperty;
use Illuminate\Http\Request;
use DB;

class BranchContractDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $search=request()->query('search');
        if($search){
            $data['rent_property']=RentProperty::find($id);
            $data['branch_contract']=DB::table('branch_contract_details')
                                ->join('rent_properties', 'rent_properties.id', '=', 'branch_contract_details.rent_properties_id')
                                ->leftJoin('branch_rents', 'branch_rents.id', '=', 'rent_properties.object_id')
                                ->where('rent_properties.id', '=', $id)
                                ->orWhere('branch_contract_details.name', 'like', "%$search%")
                                ->orWhere('branch_contract_details.position', 'like', "%$search%")
                                ->orWhere('branch_contract_details.phone', 'like', "%$search%")
                                ->orWhere('branch_contract_details.office', 'like', "%$search%")
                                ->orWhere('branch_contract_details.home', 'like', "%$search%")
                                ->select('branch_contract_details.*', 'rent_properties.owner', 'branch_rents.object_name')
                                ->paginate(10);
                                
            return view('admin.expense.rent.branch_contract_details', $data);
        }
        else{
            $data['rent_property']=RentProperty::find($id);
            $data['branch_contract']=DB::table('branch_contract_details')
                            ->join('rent_properties', 'rent_properties.id', '=', 'branch_contract_details.rent_properties_id')
                            ->leftJoin('branch_rents', 'branch_rents.id', '=', 'rent_properties.object_id')
                            ->where('rent_properties.id', '=', $id)
                            ->select('branch_contract_details.*', 'rent_properties.owner', 'branch_rents.object_name')
                            ->paginate(10);
                                                
            return view('admin.expense.rent.branch_contract_details', $data);
        }
    }
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
          
        $request->validate([
            'd.*.name' => 'required',                      
            'd.*.email' => 'required',
            'd.*.phone'=> 'required',
         ],
         [
            'd.*.name' => 'Name: Dieser Eintrag ist erforderlich',                      
            'd.*.email'=> 'Email: Dieser Eintrag ist erforderlich',
            'd.*.phone'=> 'Phone: Dieser Eintrag ist erforderlich',
         ]
 
     );
         foreach ($request->d as $key => $value) {
             BranchContractDetails::create($value);
 
         }

         return back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BranchContractDetails $branchContractDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BranchContractDetails $branchContractDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BranchContractDetails $branchContractDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = BranchContractDetails::find($id)->delete();
        return redirect()->back()->with('delete_msg', 'Der Datensatz wurd erfulgreich gelöscht');
    }
}
