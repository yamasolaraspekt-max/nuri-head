<?php

namespace App\Http\Controllers\Employee\Profile;
use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
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
            'skill.*.product_id'   =>   'required',
            'skill.*.advice' => 'required',
            'skill.*.plan'  =>  'required',
            'skill.*.calculation'    =>  'required',
            'skill.*.montage'   =>  'required',
            'skill.*.project_planing'   =>  'required',
            'skill.*.site_management'   =>  'required',

        ],
        [
            'skill.*.product_id' => 'Produkt: Das feld ist erforderlich ',
            'skill.*.advice' => 'Das Beratungsfeld ist erforderlich ',
            'skill.*.plan' => 'Planfeld ist erforderlich',
            'skill.*.calculation' => 'Berechnungsfeld ist erforderlich',
            'skill.*.montage' => 'Montagefeld ist erforderlich',
            'skill.*.project_planing' => 'Projektplanungsfeld ist erforderlich',
            'skill.*.site_management' => 'Site-Management-Feld ist erforderlich',
        ]
       
    );
    
        foreach ($request->skill as $key => $value) {
            Skill::create($value);
        }

    return back()->with('save_msg', 'Die Mitarbeiterfähigkeiten wurden erfolgreich hinzugefügt');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Skill $skill)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Skill $skill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=Skill::findOrFail($id);
        $data->delete();
        $emp=Employee::where('id', '=', $data->emp_id)->pluck('id')->first();
        
        $tab="Skill";
      
        return redirect()->to('/next_employee/'.$emp)->with('tab', $tab)->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }
}
