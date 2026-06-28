<?php

namespace App\Http\Controllers;

use App\Models\OfferGreeting;
use Illuminate\Http\Request;
use DB;

class OfferGreetingController extends Controller
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
            $data = DB::table('offer_greetings')
            ->where('title' , 'LIKE', "%$search%")
            ->orWhere('content', 'LIKE', "%$search%")
            ->orWhere('type', 'LIKE', "%$search%")
            ->select('*')
            ->paginate(10);
            return view('admin.offer.greeting.greeting')->with('data', $data);

        }
        else{

            $data = DB::table('offer_greetings')
            ->select('*')
            ->paginate(10);
            return view('admin.offer.greeting.greeting')->with('data', $data);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.offer.greeting.greeting_create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' =>  'required',
            'editor_text'   =>  'required',
        ]);

        $data = new OfferGreeting;
        $data->title=$request->title;
        $data->type=$request->type;
        $data->content=$request->editor_text;
        $data->status="Unpublished";
        if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/offer/greeting/', $image_name);
            $data->image=$image_name;
            $data->save();
            return redirect()->to('offer_greeting_view')->with('save_msg', 'Der Datensatz wurd erfulgreich  gespeichert!');
        }else{
            $data->image=Null;
            $data->save();
            return redirect()->to('offer_greeting_view')->with('save_msg', 'Der Datensatz wurd erfulgreich  gespeichert!');
        }

    }
    public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='images/offer/greeting/'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }

    public function delete_image($id,$photo){
        
        $data = OfferGreeting::findOrFail($id);
        
        if(!empty($photo)){
            $photo_path='images/offer/greeting/'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
        $data->image=null;
        $data->save();

        return redirect()->back()->with('delete_msg', 'The record deleted successfully');
    }
    /**
     * Display the specified resource.
     */
    public function save_image(Request $request)
    {
       $request->validate([
        'image' =>  'required|image:jpg,png'
       ]);
        $data = OfferGreeting::findOrFail($request->id);

       
        if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/offer/greeting/', $image_name);
            $data->image=$image_name;
            $data->save();
            return redirect()->to('offer_greeting_view')->with('save_msg', 'Der Datensatz wurd erfulgreich  gespeichert!');
        }else{
            $data->image=Null;
            $data->save();
            return redirect()->back()->with('delete_msg', 'Der Datensatz wurd nicht erfulgreich  gespeichert!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = OfferGreeting::find($id);

        return view('admin.offer.greeting.greeting_update')->with('data', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'title' =>  'required',
            'editor_text'   =>  'required',
        ]);
        $id=$request->id;
        $data =OfferGreeting::find($id);
        $data->title=$request->title;
        $data->type=$request->type;
        $data->content=$request->editor_text;
        $data->status="Unpublished";
        
            $data->save();
            return redirect()->to('offer_greeting_view')->with('save_msg', 'Der Datensatz wurd erfulgreich  gespeichert!');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = OfferGreeting::find($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'Der Datenstaz wurd erfulgreich gelöscht!');
    }

    public function publish($id){
        $data = OfferGreeting::find($id);
        $data->status="Published";
        $data->save();

        return redirect()->back()->with('save_msg', 'Der Datensatz wurd erfulgreich  veröffentlicht!');
    }

    public function unpublish($id){
        $data = OfferGreeting::find($id);
        $data->status="Unpublished";
        $data->save();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurd nicht erfulgreich veröffentlicht !');
    }
}
