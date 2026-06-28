<?php

namespace App\Http\Controllers;

use App\Models\HandoverTo;
use Illuminate\Http\Request;

class HandoverToController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'handover_id'   =>  'required',
            'handover_to'   => 'required',
            'handover_by'   =>  'required'
        ]);

        $data=new HandoverTo;
        $data->handover_id=$request->handover_id;
        $data->handover_to=$request->handover_to;
        $data->handover_from=$request->handover_from;
        $data->handover_by=$request->handover_by;
        $data->handover_date=$request->handover_date;
        $data->purpose=$request->editor_text;
        $data->status="Übergeben";
        $data->save();
        return redirect()->to('/handover_details')->with('save_msg', 'The record saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(HandoverTo $handoverTo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HandoverTo $handoverTo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HandoverTo $handoverTo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HandoverTo $handoverTo)
    {
        //
    }
}
