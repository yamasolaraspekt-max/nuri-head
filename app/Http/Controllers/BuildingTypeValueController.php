<?php

namespace App\Http\Controllers;

use App\Models\BuildingType;
use App\Models\BuildingTypeValue;
use Illuminate\Http\Request;
use DB;

class BuildingTypeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        $this->middleware('web');
    }
    public function index($id)
    {
       
             $search = request()->query('search');

        if ($search) {
           $title = BuildingType::where('id', $id)->select('building_type', 'start_year', 'end_year')->first();
            $data = DB::table('building_type_values')
                ->join('building_types', 'building_types.id', '=', 'building_type_values.building_type_id')
                ->where('building_type_values.building_type_id', $id)
                ->where('size', 'LIKE', "%$search%")
                ->orWhere('value', 'LIKE', "%$search%")
                ->select('building_type_values.*', 'building_types.building_type')
                ->paginate(10);

                    
                return view('admin.customer.customer_page.details.building_value')->with('data', $data)->with('title', $title);
        } else {
                $title = BuildingType::where('id', $id)->select('building_type', 'start_year', 'end_year')->first();
                $data = DB::table('building_type_values')
                ->join('building_types', 'building_types.id', '=', 'building_type_values.building_type_id') 
                                ->where('building_type_values.building_type_id', $id)
                ->select('building_type_values.*', 'building_types.building_type')
                ->paginate(10);

              
                return view('admin.customer.customer_page.details.building_value')->with('data', $data)->with('title', $title);
            
         }
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
        $validatedData = $request->validate([
            'building_type_id' => 'required|integer',
            'size' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        $data = BuildingTypeValue::create($validatedData);

        return redirect()->back()->with('save_msg', 'Der Datensatz wurd erfulgreich gespeichert');
    }

    /**
     * Display the specified resource.
     */
   public function load(Request $request, $id)
    {
        $data = BuildingTypeValue::where('building_type_id', $id)->get();

        if($request->expectsJson()){
            return response()->json(['data' => $data, 'id', $id]);
        }
        else{
            return view('admin.customer.customer_page.details.building_value')->with(['data'=> $data, 'id', $id]);
        }
       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BuildingTypeValue $buildingTypeValue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
           $validatedData = $request->validate([
            'building_type_id' => 'required|integer',
            'size' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);
        $id=$request->id;
        $data = BuildingTypeValue::where('id', '=', $id)->update($validatedData);

        return redirect()->back()->with('save_msg', 'Der Datensatz wurd erfulgreich gespeichert');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    { 

      $data = BuildingTypeValue::find($id);
        if ($data) {
            $data->delete();
            return redirect()->back()->with('delete-msg', 'Der Datensatz wurd erfulgreich gelöscht');
        } else {
         return redirect()->back()->with('delete-msg', 'Der Datensatz wurd nicht erfulgreich gelöscht');
        }
    }
}
