<?php

namespace App\Http\Controllers\Product\Brand;
use App\Http\Controllers\Controller;

use App\Models\ExternalDepartments;
use Illuminate\Http\Request;
use App\Models\ExternalPersonal;
use DB;

class ExternalDepartmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['update']);
        $this->middleware('permission:Product,delete')->only(['destroy']);
        $this->middleware('auth');
    }
    public function index($id)
    {
        $search=request()->query('search');
        if($search){
            $data['external']=ExternalPersonal::find($id);
            $data['department']=DB::table('external_departments')
                                ->join('external_personals', 'external_personals.id', '=', 'external_departments.external_id')
                                ->where('external_personals.id', '=', $id)
                                ->where('external_departments.department', 'like', "%$search%")
                                ->orWhere('external_departments.name', 'like', "%$search%")
                                ->orWhere('external_departments.position', 'like', "%$search%")
                                ->orWhere('external_departments.phone', 'like', "%$search%")
                                ->orWhere('external_departments.office', 'like', "%$search%")
                                ->orWhere('external_departments.home', 'like', "%$search%")
                                ->select('external_departments.*', 'external_personals.company_name as uname' )
                                ->paginate(10);
                                
            return view('admin.employee.external.external_department', $data);
        }
        else{
                     $data['external']=ExternalPersonal::find($id);
            $data['department']=DB::table('external_departments')
                                ->join('external_personals', 'external_personals.id', '=', 'external_departments.external_id')
                                ->where('external_personals.id', '=', $id) 
                                ->select('external_departments.*', 'external_personals.company_name as uname' )
                                ->paginate(10);
                                
            return view('admin.employee.external.external_department', $data);
        }
      
    }
    public function store(Request $request)
    {

        
        $request->validate([
            'brand.*.department' => 'required',                      
            'brand.*.email' => 'required',
            'brand.*.phone'=> 'required',
         ],
         [
            'brand.*.department' => 'Abteilung: Dieser Eintrag ist erforderlich',                      
            'brand.*.email'=> 'Email: Dieser Eintrag ist erforderlich',
            'brand.*.phone'=> 'Phone: Dieser Eintrag ist erforderlich',
         ]
 
     );
         foreach ($request->brand as $key => $value) {
             ExternalDepartments::create($value); 
         }

         return back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert!');
    }

   
    public function update(Request $request, $id)
    {
        ExternalDepartments::find($id)->update($request->all());
         return back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=ExternalDepartments::find($id);
        $data->delete();
        return redirect()->back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }
}
