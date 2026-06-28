<?php

namespace App\Http\Controllers;

use App\Models\BuildingType;
use App\Models\BuildingTypeValue;
use Illuminate\Http\Request;
use DB;
class BuildingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $search = $request->query('search');

        if ($search) {
            $building_value = BuildingTypeValue::all();
            $data = DB::table('building_types')
                ->where('building_type', 'LIKE', "%$search%")
                ->select('building_types.*')
                ->paginate(10);
        } else {
            $building_value = BuildingTypeValue::all();
            $data = DB::table('building_types')
                ->select('building_types.*')
                ->paginate(10);
        }

        // If the request expects JSON, return JSON response for AJAX requests
        if ($request->expectsJson()) {
            $formattedData = $data->map(function ($item) use ($building_value) {
                return [
                    'id' => $item->id,
                    'building_type' => $item->building_type,
                    'start_year' => $item->start_year,
                    'end_year' => $item->end_year,
                    'building_value' => $building_value->where('building_type_id', $item->id)->all()
                ];
            });

            return response()->json([
                'data' => $formattedData,
                'pagination' => (string) $data->links()
            ]);
        }

        // Otherwise, return the view for non-AJAX requests
        return view('admin.customer.customer_page.details.building_type', [
            'data' => $data,
            'building_value' => $building_value
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $this->validate($request,[
            'building_type'=>'required',
            'start_year'=>'required|unique:building_types',
            'end_year'=>'required|unique:building_types',
         ]);

       $data=new BuildingType;
       $data->building_type=$request->building_type;
       $data->start_year=$request->start_year;
       $data->end_year=$request->end_year;
       $data->status="Published";
       $data->save();
        return back()->with('save_msg', 'Die Gebäudeart: wurde erfolgreich hinzugefügt');
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request,[
            'building_type'    =>  'required'
        ],
        );
        $id=$_POST['id'];
        $data=BuildingType::find($id);
        $data->building_type=$request->building_type;
        $data->start_year=$request->start_year;
        $data->end_year=$request->end_year;
        $data->status="Published";
        $data->save();

        return back()->with('save_msg', 'Die Gebäudeart: wurde erfolgreich hinzugefügt');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=BuildingType::find($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'Gebäudeart erfolgreich gelöscht');
    }
}
