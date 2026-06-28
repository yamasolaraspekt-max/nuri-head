<?php

namespace App\Http\Controllers\Employee\Position;
use App\Http\Controllers\Controller;

use App\Models\PositionDescription;
use Illuminate\Http\Request;

class PositionDescriptionController extends Controller
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
            'title' =>  'required', 
            'position_id'   =>  'required|exists:positions,id',
            'department_id'   =>  'required|exists:departments,id',
            'job_description'   =>  'required|string'
        ]);

        PositionDescription::create($request->all());

        return redirect()->back()->with('save_msg', 'Stellenbeschreibung wurde erfolgreich gespeichert');
    }

    /**
     * Display the specified resource.
     */
    public function show(PositionDescription $positionDescription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PositionDescription $positionDescription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

    
         $request->validate([
            'id'    =>  'required|exists:position_descriptions,id',
            'title' =>  'required', 
            'position_id'   =>  'required|exists:positions,id',
            'department_id'   =>  'required|exists:departments,id',
            'job_description'   =>  'required|string'
        ]);

        $id=$request->id;
        PositionDescription::find($id)->update($request->all());
        return redirect()->back()->with('save_msg', 'Stellenbeschreibung wurde erfolgreich gespeichert');
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=PositionDescription::find($id);
        $data->delete();
        return redirect()->back()->with('save_msg', 'Stellenbeschreibung wurde erfolgreich gelöscht');

    }
}
