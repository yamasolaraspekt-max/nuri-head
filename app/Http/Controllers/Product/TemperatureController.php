<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\Temperature;
use Illuminate\Http\Request;
use DB;

class TemperatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['duplicate', 'edit', 'update']);
        $this->middleware('permission:Product,delete')->only(['destroy']);
        $this->middleware("auth");
    }
    public function index()
    {
        $search = request()->query('search');

        if($search){
            $data = DB::table('temperatures')
                        ->where('postcode', 'LIke','%'. $search .'%')
                        ->orWhere('city', 'LIKE', '%'. $search .'%')
                        ->select("*")
                        ->orderBy('postcode')
                        ->paginate(10);

                return view('admin.temperature.temperature')->with('data', $data);
        }
        else{
                        $data = DB::table('temperatures') 
                        ->select("*")
                        ->orderBy('postcode')
                        ->paginate(10);

                return view('admin.temperature.temperature')->with('data', $data);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function duplicate($id)
    {
       $data = Temperature::find($id);
        $newData = $data->replicate();
        $newData->save();
        return redirect()->back()->with('save_msg', 'New Replica');


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'postcode'  => 'required',
            'city'      => 'required',
            'outside_temp'  =>  'required'
        ]);

        $data = Temperature::create($request->all());

        if($data){
            return redirect()->back()->with('save_msg', 'Der Datensatz wurd erfulgreich  gespeichert!');
        }else{
            return redirect()->back()->with('delete_msg', 'Error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Temperature $temperature)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Temperature $temperature)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
       // Retrieve the ID from the request
            $id = $request->input('id');

            // Validate the necessary inputs
            $request->validate([
                'postcode' => 'required',
                'city' => 'required',
                'outside_temp' => 'required'
            ]);

            // Attempt to find and update the record with the provided ID
            $data = Temperature::find($id);

            if ($data) {
                $data->update($request->all());
                // Redirect back with a success message if the update was successful
                return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert!');
            } else {
                // Redirect back with an error message if the record was not found
                return redirect()->back()->with('delete_msg', 'Fehler: Datensatz nicht gefunden.');
            }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Temperature::find($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurd erfulgreich  gelöscht!');
    }
}
