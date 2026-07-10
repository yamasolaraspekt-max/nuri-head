<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Handover;
use Illuminate\Http\Request;
use App\Models\Asset;
use DB;
use Route;
use Session;
use URL; 
class HandoverController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct(){
        // MASTER-01 P1-IDOR Problem: Rollen-Gate (permission:Problem)
        $this->middleware('permission:Problem,update')->only(['edit', 'update', 'old_update']);
        $this->middleware('permission:Problem,delete')->only(['destroy']);
        $this->middleware('auth');
    }
    public function index()
    {
        $search=request()->query('search');

        if($search){
            $data['employee']=Employee::all();

            $data['data']=DB::table('handovers')
                        ->join('handover_tos', 'handover_tos.handover_id', '=', 'handovers.handover_id')
                        ->join('employees as handover_from', 'handover_from.id', '=', 'handover_tos.handover_from')
                        ->join('employees as handover_to', 'handover_to.id', '=', 'handover_tos.handover_to')
                        ->join('assets as item', 'item.id', '=', 'handovers.item_id')
                        ->where('handover_from.name', 'like', "%$search%")
                        ->orWhere('handover_from.lastname', 'like', "%$search%")
                        ->orWhere('handover_to.name', 'like', "%$search%")
                        ->orWhere('handover_to.lastname', 'like', "%$search%")
                        ->orWhere('item.serial_no', 'like', "%$search%")

                        ->orWhere('item.item', 'like', "%$search%")
                        ->select('handovers.*','handover_to.name as hand_to_name', 'handover_to.lastname as hand_to_lastname', 'handover_from.name as hand_from_name', 'handover_from.lastname as hand_from_lastname', 
                        'item.item as item', 'item.image as image', 'handover_to.image as hand_to_image', 'handover_from.image as hand_from_image', 'item.serial_no', 'item.article_no', 'handover_tos.handover_by', 'handover_tos.purpose')
                       
                        ->orderBy('id', 'desc')
                        ->paginate(20);

                        return view('admin.product.handover.handover_details', $data);

        }
        else{
            $data['employee']=Employee::all();
            $data['data']=DB::table('handovers')
            ->join('handover_tos', 'handover_tos.handover_id', '=', 'handovers.handover_id')
            ->join('employees as handover_from', 'handover_from.id', '=', 'handover_tos.handover_from')
            ->join('employees as handover_to', 'handover_to.id', '=', 'handover_tos.handover_to')
            ->join('assets as item', 'item.id', '=', 'handovers.item_id')
            ->select('handovers.*','handover_to.name as hand_to_name', 'handover_to.lastname as hand_to_lastname', 'handover_from.name as hand_from_name', 'handover_from.lastname as hand_from_lastname', 
            'item.item as item', 'item.image as image', 'handover_to.image as hand_to_image', 'handover_from.image as hand_from_image', 'item.serial_no', 'item.article_no', 'handover_tos.handover_by', 'handover_tos.purpose')
           
            ->distinct()
            ->paginate(20);
            return view('admin.product.handover.handover_details', $data);
        }
    }

    public function multiple(){
        $data['employee']=Employee::all();
        $data['data']=DB::table('handover_tos')
                        ->join('employees as handover_from', 'handover_from.id', '=', 'handover_tos.handover_from')
                        ->join('employees as handover_to', 'handover_to.id', '=', 'handover_tos.handover_to')
                        ->select('handover_tos.*','handover_to.name as hand_to_name', 'handover_to.lastname as hand_to_lastname', 'handover_from.name as hand_from_name', 'handover_from.lastname as hand_from_lastname', 
                       'handover_to.image as hand_to_image', 'handover_from.image as hand_from_image', 'handover_tos.handover_by' )        
                       ->distinct('handover_from.name') 
                       ->get();

        $data['handovers']=DB::table('handover_tos')
        ->join('handovers', 'handovers.handover_id', '=', 'handover_tos.handover_id')
        ->join('employees as handover_from', 'handover_from.id', '=', 'handover_tos.handover_from')
        ->join('employees as handover_to', 'handover_to.id', '=', 'handover_tos.handover_to')
        ->join('assets as item', 'item.id', '=', 'handovers.item_id')
        ->select('handovers.*','handover_to.name as hand_to_name', 'handover_to.lastname as hand_to_lastname', 'handover_from.name as hand_from_name', 'handover_from.lastname as hand_from_lastname', 
        'item.item as item', 'item.image as image', 'handover_to.image as hand_to_image', 'handover_from.image as hand_from_image', 'item.serial_no', 'item.article_no', 'handover_tos.handover_by', 'handover_tos.purpose')
        ->distinct()
        ->paginate(20);
        return view('admin.product.handover.handover_details_multiple', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function handover()
    {
        $pervious = Route::getRoutes()->match(Request::create(URL::previous()))->getName();

        if($pervious=="handover.details"){
            session()->forget('handoverID');
            $data['asset']=Asset::where('quantity', '>', 0)->get();
            $data['employee']=Employee::all();
            $data['data']=DB::table('handovers')
                                ->join('assets', 'assets.id', '=', 'handovers.item_id')
                                ->select('handovers.*', 'assets.item')
                                ->where('handovers.handover_id', '=', Session('handoverID'))
                                ->get();
            return view('admin.product.handover.handover_create', $data);
        }
        else{
            $data['asset']=Asset::where('quantity', '>', 0)->get();
            $data['employee']=Employee::all();
            $data['data']=DB::table('handovers')
                                ->join('assets', 'assets.id', '=', 'handovers.item_id')
                                ->select('handovers.*', 'assets.item')
                                ->where('handovers.handover_id', '=', Session('handoverID'))
                                ->get();
            return view('admin.product.handover.handover_create', $data);
        }
        

    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $handoverID = Session::put('handoverID', $request->handover_id);
       $request->validate([
        'handover_id'       =>  'required',
        'item_id'       =>  'required',
        'quantity'       =>  'required',
        
       ],
    [
        'handover_id' => 'Übergabe-ID ist erforderlich',
        'item_id' => 'Bitte wählen Sie den Artikel aus',
        'quantity' => 'Menge des Übergabegegenstandes ist erforderlich',
    ]);

        $old_quantity=DB::table('assets')->where('id', '=', $request->item_id)->select('quantity')->value('quantity');
        $new_quantity= $old_quantity - $request->quantity;

        if($request->quantity > $old_quantity){
            return back()->with('delete_msg', 'Die Menge ist im Bestand nicht gültig');
        }
        else{
     
                Handover::create($request->all());
          

            Asset::where('id', '=', $request->item_id)->update(['quantity' => $new_quantity]);
    
            return redirect()->to('/handover')->with('save_msg', 'Artikel zur Übergabeliste hinzugefügt')->with($handoverID);
        }

 
    }
    public function next($id){
        $data['asset']=Asset::where('quantity', '>', 0)->get();
        $data['employee']=Employee::all();
        $data['data']=DB::table('handovers')
                            ->join('assets', 'assets.id', '=', 'handovers.item_id')
                            ->select('handovers.*', 'assets.item', 'assets.description')
                            ->where('handovers.handover_id', '=', $id)
                            ->get();
         return view('admin.product.handover.handover_create_next', $data);
    }

    public function print($id){
        $data['employee']=Employee::all();
        $data['handovers']=DB::table('handover_tos')
                        ->join('handovers', 'handovers.handover_id', '=', 'handover_tos.handover_id')
                        ->join('employees as handover_from', 'handover_from.id', '=', 'handover_tos.handover_from')
                        ->join('employees as handover_to', 'handover_to.id', '=', 'handover_tos.handover_to')
                        ->join('assets as item', 'item.id', '=', 'handovers.item_id')
                        ->leftJoin('branches', 'branches.id', '=', 'item.branch')
                        ->select('handovers.*','handover_to.name as hand_to_name', 'handover_to.lastname as hand_to_lastname','handover_to.email as hand_to_email','handover_to.phone as hand_to_phone', 
                        'handover_from.name as hand_from_name', 'handover_from.lastname as hand_from_lastname', 'handover_from.phone as hand_from_phone', 'handover_from.email as hand_from_email' ,
                        'item.item as item', 'handover_from.image as hand_from_image', 'item.serial_no', 'item.article_no', 'handover_tos.handover_by', 'handover_tos.purpose', 'item.description', 'branches.branch')
                        ->where('handover_tos.handover_id', '=', $id) 
                        ->distinct()  
                        ->get();

        $data['data']=DB::table('handover_tos')
        ->join('handovers', 'handovers.handover_id', '=', 'handover_tos.handover_id')
        ->join('employees as handover_from', 'handover_from.id', '=', 'handover_tos.handover_from')
        ->join('employees as handover_to', 'handover_to.id', '=', 'handover_tos.handover_to')
        ->join('assets as item', 'item.id', '=', 'handovers.item_id')
        ->leftJoin('branches', 'branches.id', '=', 'item.branch')
        ->select('handovers.*','handover_to.name as hand_to_name', 'handover_to.lastname as hand_to_lastname','handover_to.email as hand_to_email','handover_to.phone as hand_to_phone', 
        'handover_from.name as hand_from_name', 'handover_from.lastname as hand_from_lastname', 'handover_from.phone as hand_from_phone', 'handover_from.email as hand_from_email' ,
        'item.item as item', 'handover_from.image as hand_from_image', 'item.serial_no', 'item.article_no', 'handover_tos.handover_by', 'handover_tos.purpose', 'item.description', 'branches.branch')
        ->where('handover_tos.handover_id', '=', $id)
        ->first();
       
        return view('admin.product.handover.handover_print', $data);
    }
    /**
     * Display the specified resource.
     */
    public function show(Handover $handover)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Handover $handover)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
            $data=Handover::find($request->id);
            $data->remark=$request->remark;
            $data->status="New";
            $data->save();
    
            $handoverID = Session::put('handoverID', $request->handover_id);
            return redirect()->to('/handover')->with('save_msg', 'Artikel zur Übergabeliste hinzugefügt')->with($handoverID);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=Handover::find($id);
        $old_quantity=Asset::where('id', '=', $data->item_id)->select('quantity')->value('quantity');
        $new_quantity= $data->quantity + $old_quantity;
        Asset::where('id', '=', $data->item_id)->update(['quantity' => $new_quantity]);
        $data->delete();
        $handoverID = Session::put('handoverID', $data->handover_id);
        return redirect()->back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht')->with($handoverID);
    }


    public function old_update(Request $request)
    {
        
        $old_quantity=DB::table('assets')->where('id', '=', $request->item_id)->select('quantity')->value('quantity');
        $handover_quantity=DB::table('handovers')->where('id', '=', $request->id)->select('quantity')->value('quantity');

      
        
        if($handover_quantity < $request->quantity){
            $request_quantity = $handover_quantity + $request->quantity;
        }else{
            $request_quantity =  $handover_quantity - $request->quantity;
        }

        if($request->request_type=="return"){
            $new_quantity = $request_quantity + $old_quantity;

        }
        elseif($request->request_type="add"){
            $new_quantity = $old_quantity - $request_quantity;

        }
        
    //return 'handover: '.$handover_quantity.' requested: '.$request->quantity.' request per item: '.$request_quantity .' request type: '.$request->request_type.' stock quanitity: '.$old_quantity.' result:'.$new_quantity;
       
        if($old_quantity < $new_quantity){
            $handoverID = Session::put('handoverID', $request->handover_id);

            return back()->with('delete_msg', 'Die Menge ist im Bestand nicht gültig')->with($handoverID);

        }
        elseif($request->request_type=="add" && $old_quantity > $new_quantity){
            $data=Handover::find($request->id);
            $data->quantity=$request_quantity;
            $data->remark=$request->remark;
            $data->status="New";
            $data->save();
    
            Asset::where('id', '=', $request->item_id)->update(['quantity' => $new_quantity]);
            $handoverID = Session::put('handoverID', $request->handover_id);
    
            return redirect()->to('/handover')->with('save_msg', 'Artikel zur Übergabeliste hinzugefügt')->with($handoverID);
        }
        else{
            $data=Handover::find($request->id);
            $data->quantity=$request_quantity;
            $data->remark=$request->remark;
            $data->status="New";
            $data->save();
    
            Asset::where('id', '=', $request->item_id)->update(['quantity' => $new_quantity]);
            $handoverID = Session::put('handoverID', $request->handover_id);
            return redirect()->to('/handover')->with('save_msg', 'Artikel zur Übergabeliste hinzugefügt')->with($handoverID);
         
        }
       
        
        
       
    }
}
