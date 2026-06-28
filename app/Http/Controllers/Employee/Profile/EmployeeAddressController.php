<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\EmployeeAddress;
use Illuminate\Http\Request;

class EmployeeAddressController extends Controller
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
    public function active($id)
    {
        $check = EmployeeAddress::where('main', 'active')->first();

        if($check){
            return redirect()->back()->with('delete_msg', 'Sie können nur eine Hauptadresse haben');
        }
        $data = EmployeeAddress::find($id);

        
        $data->main='active';
        $data->save();

        return redirect()->back()->with('save_msg', 'Adresse gilt als Hauptadresse');
    }

        public function deactive($id)
    {
        $data = EmployeeAddress::find($id);
        $data->main=null;
        $data->save();

        return redirect()->back()->with('save_msg', 'Der Vorgang wurde erfolgreich abgeschlossen');
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $validateData= $request->validateWithBag('addressForm', [
        'address.*.address_name' => 'required',
        'address.*.street'  =>  'required',
        'address.*.apartment'    =>  'required',
        'address.*.postal'  =>  'required',
        'address.*.city'  =>  'required',
    ], [
        'address.*.address_name.required' => 'Adressname ist erforderlich',
        'address.*.street.required' => 'Straßenname ist erforderlich',
        'address.*.apartment.required' => 'Wohnungsnummer ist erforderlich',
        'address.*.postal.required' => 'Postleitzahl ist erforderlich',
        'address.*.city.required' => 'Stadt ist erforderlich',
    ]);

    foreach ($request->address as $value) {
        EmployeeAddress::create($value);
    }

return back()->with('save_msg', 'Die Mitarbeiteradresse(n) wurde(n) erfolgreich gespeichert')
                 ->withInput($request->all())
                 ->with('active_tab', 'address');
                
    }


 

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id=$_POST['id'];
        $data=EmployeeAddress::findorFail($id);
        $data->emp_id=$request->emp_id;
        $data->address_name=$request->address_name;
        $data->street=$request->street;
        $data->apartment=$request->apartment;
        $data->postal=$request->postal;
        $data->city=$request->city;
        $data->save();

        return back()->with('save_msg', 'Die Mitarbeiteradresse wurde erfolgreich gespeichert');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=EmployeeAddress::find($id);
        $data->delete();
        $emp=Employee::where('id', '=', $data->emp_id)->pluck('id')->first();
        
        $tab="address";
      
        return redirect()->to('/next_employee/'.$emp)->with('tab', $tab)->with('delete_msg', 'Die Address wurde erfolgreich gelöscht');
    }
}
