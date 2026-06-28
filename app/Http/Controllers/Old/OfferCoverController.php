<?php

namespace App\Http\Controllers;

use App\Models\OfferCover;
use Illuminate\Http\Request;
use DB;

class OfferCoverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('web');
    }
    public function index()
    {
        $search = request()->query('search');

        if($search){
            $data = DB::table('offer_covers')
            ->where('title' , 'LIKE', "%$search%")
            ->select('*')
            ->paginate(10);
            return view('admin.offer.cover.cover')->with('data', $data);


        }
        else{

            $data = DB::table('offer_covers')
            ->select('*')
            ->paginate(10);

                return view('admin.offer.cover.cover')->with('data', $data);
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
            'title' =>  'required',
            
        ]);

        $data = new OfferCover;
        $data->title=$request->title;
        $data->status="Unpublished";
        if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/offer/cover/', $image_name);
            $data->image=$image_name;
            $data->save();
            return redirect()->to('offer_cover')->with('save_msg', 'Der Datensatz wurd erfulgreich  gespeichert!');
        }else{
            return redirect()->to('offer_cover')->with('save_msg', 'Der Datensatz wurd erfulgreich  gespeichert!');
        }
    }

    public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='images/offer/cover/'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }

    public function delete_image($id,$photo){
        
        $data = OfferCover::findOrFail($id);
        
        if(!empty($photo)){
            $photo_path='images/offer/cover/'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
        $data->image=null;
        $data->save();

        return redirect()->back()->with('delete_msg', 'The record deleted successfully');
    }

    public function save_image(Request $request)
    {
       $request->validate([
        'image' =>  'required|image:jpg,png'
       ]);
        $data = OfferCover::findOrFail($request->id);

       
        if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/offer/cover/', $image_name);
            $data->image=$image_name;
            $data->save();
            return redirect()->to('offer_cover')->with('save_msg', 'Der Datensatz wurd erfulgreich  gespeichert!');
        }else{
            return redirect()->back()->with('delete_msg', 'Der Datensatz wurd nicht erfulgreich  gespeichert!');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request )
    {
        $request->validate([
            'title' =>  'required',
            
        ]);
        $id=$request->id;
        $data =OfferCover::find($id);
        $data->title=$request->title;
        $data->status="Unpublished";
        if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/offer/cover/', $image_name);
            $data->image=$image_name;
            $data->save();
            return redirect()->to('offer_cover')->with('save_msg', 'Der Datensatz wurd erfulgreich  gespeichert!');
        }else{
            $data->save();
            return redirect()->to('offer_cover')->with('save_msg', 'Der Datensatz wurd erfulgreich  gespeichert!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data= OfferCover::find($id);
        $this->delete_photo($data->image);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'The record deleted successfully');
    }

    public function publish($id){
        $data = OfferCover::find($id);
        $data->status="Published";
        $data->save();

        return redirect()->back()->with('save_msg', 'Der Datensatz wurd erfulgreich  veröffentlicht!');
    }

    public function unpublish($id){
        $data = OfferCover::find($id);
        $data->status="Unpublished";
        $data->save();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurd nicht erfulgreich veröffentlicht !');
    }
}
