<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\EmployeeLicense;
use App\Models\Empl;
use Illuminate\Http\Request;
use Redirect;
use DB;
use App\Models\EmployeeLicenseType;

class EmployeeLicenseController extends Controller
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
   // Validate request
  // Validate request
    $request->validate([
        'emp_id'       => 'required|exists:employees,id',
        'license_no'   => 'required|string|max:255',
        'expiry_date'  => 'nullable|date',
        'type'         => 'required|array|min:1', 
        'type.*.grade' => 'required|string|max:255',
    ], [
        'type.*.type.required'  => 'Der Autotyp ist erforderlich.',
        'type.*.type.exists'    => 'Der ausgewählte Typ ist ungültig.',
        'type.*.grade.required' => 'Der Lizenzgrad ist erforderlich.',
    ]);

    // Create new Employee License entry
    $license = EmployeeLicense::create([
        'emp_id'     => $request->emp_id,
        'license_no' => $request->license_no,
        'expiry_date' => $request->expiry_date,
    ]);

    // Save the license types into the pivot table `employee_license_license_type`
    foreach ($request->type as $item) {
        // Get the actual license type ID from the `license_types` table 
        
            EmployeeLicenseType::create([
                'employee_id'         => $request->emp_id,
                'employee_license_id' => $license->id, 
                'grade'               => $item['grade'],
                'type'               => $item['type'],
            ]);
       
    }
 

    return Redirect::route('emp.next', ['id' => $request->emp_id])
        ->with('data', $license)
        ->with('active_tab', 'license')
        ->with('save_msg', 'Lizenz erfolgreich hinzugefügt.');
}


    public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='images/employee/license'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EmployeeLicense $employeeLicense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeLicense $employeeLicense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
   
        $id=request()->input('id');  
        $data =EmployeeLicense::find($id);
      
        $data->emp_id=$request->emp_id;
        $data->license_no=$request->license_no;
        $data->expiry_date=$request->expiry_date;
        $data->grade=$request->grade;
        if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/employee/license', $image_name);
            $data->image=$image_name;
            $data->save();
          
            return Redirect::route('emp.next',['id'=>$request->emp_id])->with('data', $data)->with('save_msg', 'Lizenz erfolgreich hinzugefügt');
        }
        else{
            $data->save();
         
            return Redirect::route('emp.next',['id'=>$request->emp_id])->with('data', $data)->with('save_msg', 'Lizenz erfolgreich hinzugefügt');;

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=EmployeeLicense::find($id);
        $data->delete();
        return redirect()->back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }

    public function suspend(Request $request)
    {
        $id=request()->input('id');

        $data=EmployeeLicense::find($id);
        $data->status="Ausgesetzt";
        $data->suspend_date=$request->suspend_date;
        $data->duration=$request->duration;
        $data->save();
        return redirect()->back()->with('delete_msg', 'Führerschein entzogen');
    }


}
