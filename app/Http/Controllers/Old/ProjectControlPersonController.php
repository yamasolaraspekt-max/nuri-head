<?php

namespace App\Http\Controllers;

use App\Models\ProjectControlPerson;
use Illuminate\Http\Request;

class ProjectControlPersonController extends Controller
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
    public function list($projectId, $phaseId)
    {
        $controllers = ProjectControlPerson::with('employee')
            ->where('project_id', $projectId)
            ->where('phase_id', $phaseId)
            ->get();
    
        return view('admin.project.partials.phase_controller', compact('controllers'));
    }
    
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|integer',
            'phase_id' => 'required|integer',
            'activity_id' => 'nullable|integer',
            'employee_id' => 'required|array',
        ]);
    
        foreach ($validated['employee_id'] as $empId) {
            ProjectControlPerson::updateOrCreate(
                [
                    'project_id' => $validated['project_id'],
                    'phase_id' => $validated['phase_id'],
                    'activity_id' => $validated['activity_id'],
                    'employee_id' => $empId,
                ],
                []
            );
        }
    
        return response()->json(['success' => true, 'message' => 'Mitarbeiter gespeichert.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectControlPerson $projectControlPerson)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectControlPerson $projectControlPerson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProjectControlPerson $projectControlPerson)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $entry = ProjectControlPerson::findOrFail($id);
        $entry->delete();
    
        return response()->json(['success' => true]);
    }
    
}
