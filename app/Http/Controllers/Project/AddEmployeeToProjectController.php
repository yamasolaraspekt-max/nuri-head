<?php

namespace App\Http\Controllers\Project;
use App\Http\Controllers\Controller;

use App\Models\AddEmployeeToProject;
use Illuminate\Http\Request;
use App\Notifications\ProjectTaskNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;
 use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\DatabaseNotification;
use DB;
class AddEmployeeToProjectController extends Controller
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
  

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'employee_id'   => 'required|array',
            'employee_id.*' => 'exists:employees,id',
            'employee_roll' => 'required|string',
            'phase_id'      => 'required|exists:task_phases,id',
            'activity_id'   => 'nullable|exists:phase_activities,id'
        ]);

        $added = [];
        $exists = [];

        foreach ($request->employee_id as $empId) {
            $already = AddEmployeeToProject::where('project_id', $request->project_id)
                ->where('phase_id', $request->phase_id)
                ->where('employee_id', $empId)
                ->exists();

            if (!$already) {
                AddEmployeeToProject::create([
                    'project_id'   => $request->project_id,
                    'employee_id'  => $empId,
                    'phase_id'     => $request->phase_id,
                    'activity_id'  => $request->activity_id,
                    'member_type'  => $request->employee_roll,
                    'status'       => 'send'
                ]);
                $added[] = $empId;
            } else {
                $exists[] = $empId;
            }
        }

        $msg = match (true) {
            count($added) && count($exists) => 'Einige Mitarbeiter wurden hinzugefügt, andere waren schon vorhanden.',
            count($added)                   => 'Mitarbeiter erfolgreich hinzugefügt.',
            default                         => 'Keine neuen Mitarbeiter hinzugefügt.',
        };

        return response()->json([
            'message' => $msg,
            'added' => $added,
            'already_exists' => $exists
        ]);
    }
public function update(Request $request)
{
    $request->validate([
        'project_id'    => 'required|exists:projects,id',
        'employee_id'   => 'required|array',
        'employee_id.*' => 'exists:employees,id',
        'employee_roll' => 'required|string',
        'phase_id'      => 'required|exists:task_phases,id',
    ]);

    // Delete old assignments for this phase
    AddEmployeeToProject::where('project_id', $request->project_id)
        ->where('phase_id', $request->phase_id)
        ->delete();

    // Reinsert updated list
    foreach ($request->employee_id as $empId) {
        AddEmployeeToProject::create([
            'project_id'   => $request->project_id,
            'employee_id'  => $empId,
            'phase_id'     => $request->phase_id,
            'member_type'  => $request->employee_roll,
            'status'       => 'updated'
        ]);
    }

    return response()->json([
        'message' => 'Mitarbeiterliste erfolgreich aktualisiert.',
    ]);
}

 
    /**
     * Show the form for editing the specified resource.
     */
    public function getEmployees($project_id, $phase_id)
    {
        \Log::info("Fetching employees for project: $project_id, phase: $phase_id");

        $data = DB::table('add_employee_to_projects')
            ->join('employees', 'employees.id', '=', 'add_employee_to_projects.employee_id')
            ->select(
                'employees.id',
                'employees.name',
                'employees.lastname',
                'employees.image',
                'add_employee_to_projects.status',
                'add_employee_to_projects.member_type'
            )
            ->where('add_employee_to_projects.project_id', $project_id)
            ->where('add_employee_to_projects.phase_id', $phase_id)
            ->get();

        \Log::info($data);

        return response()->json($data);
    }



    /**
     * Update the specified resource in storage.
     */
   
 
}
