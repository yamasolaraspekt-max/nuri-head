<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\OtherSkill;
use Illuminate\Http\Request;

class OtherSkillController extends Controller
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
            'oskill.*.skills'   =>   'required',
            'oskill.*.proficiency' => 'required',
            'oskill.*.year_experience'  =>  'required',

        ],
        [
            'oskill.*.skills' => 'Skill: Fähigkeit ist erforderlich',
            'oskill.*.proficiency' => 'Profeciency: Kenntnisse sind erforderlich',
            'oskill.*.year_experience' => 'Jahre Erfahrung sind erforderlich',
        ]
       
    );
    
        foreach ($request->oskill as $key => $value) {
            OtherSkill::create($value);
        }

    return back()->with('save_msg', 'Die Mitarbeiterfähigkeiten wurden erfolgreich hinzugefügt');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(OtherSkill $otherSkill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OtherSkill $otherSkill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OtherSkill $otherSkill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=OtherSkill::findOrFail($id);
        $data->delete();
        $emp=Employee::where('id', '=', $data->emp_id)->pluck('id')->first();
        
        $tab="Skill";
      
        return redirect()->to('/next_employee/'.$emp)->with('tab', $tab)->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }
}
