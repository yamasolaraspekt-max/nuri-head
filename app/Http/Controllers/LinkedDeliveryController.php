<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Models\LinkedDelivery;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;

class LinkedDeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $data['delivery_note']=DB::table('delivery_notes')
                            ->where('id', '=', $id)    
                            ->select('id', 'delivery_note')
                            ->first();
        $data['data']=DB::table('delivery_notes')
                            ->join('branches', 'branches.id', '=', 'delivery_notes.to')
                            ->join('employees', 'employees.id', '=', 'delivery_notes.handover_by')
                            ->select('delivery_notes.*', 'employees.name', 'employees.lastname', 'branches.branch')
                            ->get();
        return view('admin.product.delivery.linked', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function linked($id)
    {
       $data['delivery_note']=DB::table('delivery_notes')
                                    ->join('branches', 'branches.id', '=', 'delivery_notes.to')
                                     ->join('employees', 'employees.id', '=', 'delivery_notes.handover_by')
                                    ->where('delivery_notes.id', '=', $id)
                                    ->select('delivery_notes.*', 'employees.name', 'employees.lastname', 'branches.branch')
                                    ->distinct()
                                    ->first();

        $data['linked']=DB::table('linked_deliveries')
                            ->join('delivery_notes as link_to', 'link_to.id', '=', 'linked_deliveries.linked_to')
                            ->join('delivery_notes as delivery', 'delivery.id', '=', 'linked_deliveries.delivery_note')
                            ->leftJoin('branches', 'branches.id', '=', 'delivery.to')
                            ->leftJoin('employees', 'employees.id', '=', 'delivery.handover_by')
                            ->select('linked_deliveries.*', 'link_to.delivery_note as link_to', 'delivery.delivery_note as delivery',  'employees.name', 'employees.lastname', 'branches.branch'
                            , 'delivery.from', 'delivery.handover_date', 'delivery.status', 'delivery.progress')
                            ->where('linked_to', '=', $id)
                            ->get();

    return view('admin.product.delivery.delivery_note_linked', $data);


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'delivery_note' =>  'required',
            'linked_to'     =>  'required'
        ]);

        if($request->delivery_note == $request->linked_to){
            return redirect()->back()->with('delete_msg', 'Sie können nicht denselben Lieferschein verknüpfen');
        }
        else{

        $data= new LinkedDelivery;
        $data->delivery_note=$request->delivery_note;
        $data->linked_to=$request->linked_to;
        $data->reason=$request->editor_text;
        $data->linked_by= auth()->user()->name;
        $data->linked_date= Carbon::now();

        $data->save();

        DeliveryNote::find($data->delivery_note)->update(['linked'  =>  'linked', 'level'   =>  2]);
        DeliveryNote::find($data->linked_to)->update(['linked'  =>  'Linked to', 'level'  => 1]);
        

        return redirect()->to('delivery_note_details?search='.$data->delivery_note)->with('save_msg',  'Der Lieferschein '.$data->delivery_note.' ist mit '.$data->linked_to.' verknüpft');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LinkedDelivery $linkedDelivery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LinkedDelivery $linkedDelivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LinkedDelivery $linkedDelivery)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LinkedDelivery $linkedDelivery)
    {
        //
    }
}
