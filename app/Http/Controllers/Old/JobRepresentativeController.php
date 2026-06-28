<?php

namespace App\Http\Controllers;

use App\Models\JobRepresentative;
use Illuminate\Http\Request;

class JobRepresentativeController extends Controller
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
    
        
       $validate = $request->validateWithBag('representative', [
            'employee_id'   =>  'required',
            'department_id'   =>  'required',
            'position_id'   =>  'required',
            'representer_id'   =>  'required',
            'description'   =>  'nullable|string',
            'start_date'   =>  'required|date',
            'end_date'   =>  'required|date',
            'status'   =>  'nullable|string',
        ], [
            'employee_id.required' => 'Mitarbeiter-ID ist erforderlich',
            'department_id.required' => 'Abteilungs-ID ist erforderlich',
            'position_id.required' => 'Positions-ID ist erforderlich',
            'representer_id.required' => 'Vertreter-ID ist erforderlich',
            'description.string' => 'Beschreibung muss ein String sein',
            'start_date.required' => 'Startdatum ist erforderlich',
            'start_date.date' => 'Startdatum muss ein gültiges Datum sein',
            'end_date.required' => 'Enddatum ist erforderlich',
            'end_date.date' => 'Enddatum muss ein gültiges Datum sein',
            'status.string' => 'Status muss ein String sein',
        ]);


        $data = new JobRepresentative;
        $data->employee_id = $request->employee_id;
        $data->department_id = $request->department_id;
        $data->position_id = $request->position_id;
        $data->representer_id = $request->representer_id;
        $data->current_representer = $request->representer_id;
        $data->description = $request->description;
        $data->start_date = $request->start_date;
        $data->end_date = $request->end_date;
        $data->status = 'Published';
        $data->save();


        return redirect()->back()->with('save_msg', 'Der Datensatz wird erfolgreich gespeichert')->withInput($request->all())
                 ->with('active_tab', 'department');
        


    }

    /**
     * Display the specified resource.
     */
    public function show(JobRepresentative $jobRepresentative)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobRepresentative $jobRepresentative)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobRepresentative $jobRepresentative)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy($id)
{
    $data = JobRepresentative::find($id);

    if ($data) {
        $data->delete();
        return redirect()->back()->with('delete_msg', 'Kleidung wird gelöscht')->withInput()
                 ->with('active_tab', 'department');
    } else {
        return redirect()->back()->with('delete_msg', 'Kleidung nicht gefunden')->withInput()
                 ->with('active_tab', 'department');
    }
}

}
