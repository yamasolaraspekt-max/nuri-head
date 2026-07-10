<?php

namespace App\Http\Controllers;

use App\Models\HeatingType;
use Illuminate\Http\Request;
use DB;

class HeatingTypeController extends Controller
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
     public function index()
    {
        $search=request()->query('search');
        
        if($search){
            $data=DB::table('heating_types')
                    ->where('heating_types', 'LIKE', "%$search%")
                    ->select('heating_type.*')
                    ->paginate(10);
                    return view('admin.customer.customer_page.details.heating_type')->with('data', $data);
        }
        else{
            $data=DB::table('heating_types')
            ->select('heating_types.*')
            ->paginate(10);
            return view('admin.customer.customer_page.details.heating_type')->with('data', $data);


        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $this->validate($request,[
            'heating_type'=>'required|unique:heating_types',
         ]);

       $data=new HeatingType;
       $data->heating_type=$request->heating_type;
       $data->value=$request->value;
       $data->status="Published";
       $data->save();
        return back()->with('save_msg', 'Die Heizungsart wurde erfolgreich hinzugefügt');
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request,[
            'heating_type'    =>  'required'
        ],
        );
        $id=$_POST['id'];
        $data=HeatingType::find($id);
        $data->heating_type=$request->heating_type;
        $data->value=$request->value;
        $data->status="Published";
        $data->save();

        return back()->with('save_msg', 'Die Heizungsart wurde erfolgreich hinzugefügt');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=HeatingType::find($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'Heizungsarterfolgreich gelöscht');
    }
}
