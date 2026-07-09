<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\EmergencyContact;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmergencyContactController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR: HR-Rollen-Gate (permission:Employee), enforced mit heutigen user_rolls-Grants
        $this->middleware('permission:Employee,add')->only(['store']);
        $this->middleware('permission:Employee,update')->only(['update']);
        $this->middleware('permission:Employee,delete')->only(['destroy']);
    }

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
        $validatedData = $request->validateWithBag('emergencyForm', [
            'emer.*.relation' => 'required',
            'emer.*.phone'  =>  'nullable',
            'emer.*.home_phone'  =>  'nullable',
            'emer.*.email'  =>  'nullable',
            'emer.*.street'  =>  'nullable',
            'emer.*.postal'  =>  'nullable',
            'emer.*.city'  =>  'nullable',
        ], [
            'emer.*.relation.nullable' => 'Mitarbeiterbeziehung ist erforderlich',
            'emer.*.phone.nullable' => 'Telefonnummer des Notfallkontakts ist erforderlich',
            'emer.*.home_phone.nullable' => 'Heimtelefon des Notfallkontakts ist erforderlich',
            'emer.*.email.nullable' => 'E-Mail des Notfallkontakts ist erforderlich',
            'emer.*.street.nullable' => 'Straßenname des Notfallkontakts ist erforderlich',
            'emer.*.postal.nullable' => 'Postleitzahl des Notfallkontakts ist erforderlich',
            'emer.*.city.nullable' => 'Notfallkontaktstadt ist erforderlich',
        ]);

        // If validation fails, this line will not be executed.
        foreach ($request->emer as $key => $value) {
            EmergencyContact::create($value);
        }
        return back()->with('save_msg', 'Der Notfallkontakt wurde erfolgreich gespeichert')->withInput()->with('active_tab', 'address');

    }

    /**
     * Display the specified resource.
     */
    public function show(EmergencyContact $emergencyContact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmergencyContact $emergencyContact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id=$_POST['id'];
        $data=EmergencyContact::findorFail($id);
        $data->emp_id=$request->emp_id;
        $data->relation=$request->relation;
        $data->phone=$request->phone;
        $data->home_phone=$request->home_phone;
        $data->email=$request->email;
        $data->street=$request->street;
        $data->postal=$request->postal;
        $data->city=$request->city;
        $data->save();

        return back()->with('save_msg', 'Der Notfallkontakt wurde erfolgreich gespeichert');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=EmergencyContact::find($id);
        $data->delete();
        $emp=Employee::where('id', '=', $data->emp_id)->pluck('id')->first();
        
        $tab="address";
      
        return redirect()->to('/next_employee/'.$emp)->with('tab', $tab)->with('delete_msg', 'Der Notfallkontakt wurde erfolgreich gelöscht');
    }
}
