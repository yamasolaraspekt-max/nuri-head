<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\Tiles;
use Illuminate\Http\Request;

class TilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {
        $search = request()->query('search');

        if($search){
            $data = Tiles::where('name', 'like', '%'.$search.'%')->paginate(20);
            return view('admin.product.tile_settings.tile_settings')->with('data', $data);
        }
        else{
            $data = Tiles::paginate(20);
            return view('admin.product.tile_settings.tile_settings')->with('data', $data);
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
        $request->validate([
            'name'  =>  'required',
            'image' =>  'required'
        ]);

        $data = new Tiles;
        $data->name=$request->name;
        $data->model = $request->model;
         if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/products/tiles/', $image_name);
            $data->image=$image_name;
        }

        $data->save();
        return redirect()->back()->with('save_msg', 'Der Datensatz wurd erfulgreich gespeichert');

    }

      public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='images/products/tiles/'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Tiles $tiles)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tiles $tiles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tiles $tiles)
    {
       $request->validate([
            'name'  =>  'required',
            'image' =>  'required'
        ]);

        $data = Tiles::find($request->id);
        $data->name=$request->name;
        $data->model = $request->model;
         if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/products/tiles/', $image_name);
            $data->image=$image_name;
        }

        $data->save();
        return redirect()->back()->with('save_msg', 'Der Datensatz wurd erfulgreich gespeichert');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Tiles::find($id);
        $this->delete_photo($data->image);
        $data->delete();
        return redirect()->back()->with('delete_msg', 'Der Datensatz wurd gelöscht');
    }
}
