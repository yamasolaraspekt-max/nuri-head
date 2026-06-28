<?php

namespace App\Http\Controllers;

use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getCustomerPhase($customer_id, $alternative_id, $product_id)
    {
        $tasks = DB::table('project_tasks')
            ->join('projects', 'projects.id', '=', 'project_tasks.project_id')
            ->join('new_leads', 'new_leads.id', '=', 'project_tasks.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'project_tasks.alternative_id')
            ->join('project_montage_checklists as checklist', 'checklist.id', '=', 'project_tasks.checklist_id')
            ->leftJoin('task_phases', 'task_phases.id', '=', 'project_tasks.phase_id')
            ->leftJoin('phase_activities as activity', 'activity.id', '=', 'project_tasks.activities_id')
            ->leftJoin('activity_positions', 'activity_positions.id', '=', 'project_tasks.activities_id')
            ->where('project_tasks.customer_id', $customer_id)
            ->where('project_tasks.alternative_id', $alternative_id)
            ->where('project_tasks.product_id', $product_id)
            ->select(
                'checklist.id as checklist_id',
                'checklist.product_id',
                'checklist.employee_id',
                'checklist.list_name',
                'checklist.plan_montage',
                'checklist.supplier_section',
                'checklist.cran_section',
                'checklist.old_facility',
                'checklist.photo_section',
                'checklist.commission',
                'checklist.default_stage',
                'task_phases.phase_name',
                'task_phases.id as phase_id',
                'task_phases.section_id',
                'task_phases.section_name',
                'activity.title as title_activity',
                'activity.duration',
                'activity.description',
                'activity_positions.position_id',
                'activity.status',
                'activity.photo',
                'activity.answered_by',
                'project_tasks.service',
                'project_tasks.color',
                'project_tasks.active_by',
                'project_tasks.jump_steps',
                'project_tasks.jump_steps_by',
                'project_tasks.done',
                'project_tasks.type',
                'project_tasks.main_id',
                'project_tasks.outside_type',
                'project_tasks.done_date',
                'project_tasks.start_date',
                'project_tasks.due_date',
                'project_tasks.reason',
                'project_tasks.done_status',
                'project_tasks.status as task_status',
                'project_tasks.work_progress',
                'project_tasks.more_time',
                'project_tasks.total_time',
                'project_tasks.activities_id'
            )
            ->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No checklist found for this customer, alternative, and product.'], 404);
        }

        $first = $tasks->first();

        $phases = [];
        foreach ($tasks as $row) {
            if (!$row->phase_id) continue;

            $phaseKey = $row->phase_id;

            if (!isset($phases[$phaseKey])) {
                $phases[$phaseKey] = [
                    'phase_id' => $row->phase_id,
                    'phase_name' => $row->phase_name,
                    'section_id' => $row->section_id,
                    'section_name' => $row->section_name,
                    'activities' => []
                ];
            }

            $phases[$phaseKey]['activities'][] = [
                'activity_id'     => $row->activities_id,
                'title_activity'  => $row->title_activity,
                'duration'        => $row->duration,
                'description'     => $row->description,
                'position_id'     => $row->position_id,
                'status'          => $row->status,
                'photo'           => $row->photo,
                'answered_by'     => $row->answered_by,
                'start_date'      => $row->start_date,
                'due_date'        => $row->due_date
            ];
        }

        return response()->json([
            'product_id'      => $first->product_id,
            'employee_id'     => $first->employee_id,
            'list_name'       => $first->list_name,
            'plan_montage'    => $first->plan_montage,
            'supplier_section'=> $first->supplier_section,
            'cran_section'    => $first->cran_section,
            'old_facility'    => $first->old_facility,
            'photo_section'   => $first->photo_section,
            'commission'      => $first->commission,
            'default_stage'   => $first->default_stage,
            'project_montage_id' => $first->checklist_id,
            'service'         => $first->service,
            'color'           => $first->color,
            'active_by'       => $first->active_by,
            'jump_steps'      => $first->jump_steps,
            'jump_steps_by'   => $first->jump_steps_by,
            'done'            => $first->done,
            'type'            => $first->type,
            'main_id'         => $first->main_id,
            'outside_type'    => $first->outside_type,
            'done_date'       => $first->done_date,
            'reason'          => $first->reason,
            'done_status'     => $first->done_status,
            'status'          => $first->task_status,
            'work_progress'   => $first->work_progress,
            'more_time'       => $first->more_time,
            'total_time'      => $first->total_time,
            'phases'          => array_values($phases)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
  
public function store(Request $request)
{
    // 🔍 Log incoming payload
    Log::info('✅ Received payload', $request->all());

    try {
        // ✅ Validate input
            $validated = $request->validate([
                'checklist_id' => 'required|exists:project_montage_checklists,id',
                'customer_id' => 'required|exists:new_leads,id',
                'alternative_id' => 'required|exists:lead_alternative_adds,id',
                'product_id' => 'required|exists:article_groups,id',
                'phases' => 'required|array',
                'phases.*.phase_id' => 'required|exists:task_phases,id',
                'phases.*.activities' => 'required|array',
                'phases.*.activities.*.id' => 'required|exists:phase_activities,id',
                'project_id' => 'nullable|exists:projects,id', // ✅ allow optional project_id
                'service' => 'nullable|string|max:255' // ✅ optional service name
            ]);


    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('❌ Validation failed', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $e->errors()
        ], 422);
    }

    try {
        // ✅ Save each activity under each phase
        foreach ($validated['phases'] as $phase) {
            foreach ($phase['activities'] as $activity) {
                DB::table('project_tasks')->insert([
                'checklist_id' => $validated['checklist_id'],
                'customer_id' => $validated['customer_id'],
                'alternative_id' => $validated['alternative_id'],
                'product_id' => $validated['product_id'],
                'phase_id' => $phase['phase_id'],
                'activities_id' => $activity['id'] ?? null,
                'project_id' => $validated['project_id'] ?? null,
                'service' => $validated['service'] ?? null,  
                'type' => 'sub',
                'done' => $activity['done'] ?? 'false',
                'status' => $activity['status'] ?? 'published',
                'color' => null,
                'active_by' => null,
                'jump_steps' => null,
                'jump_steps_by' => null,
                'main_id' => null,
                'parent_id' => null,
                'contact_person' => null,
                'responsible_person' => null,
                'outside_service' => null,
                'outside_company' => null,
                'outside_type' => 'internal',
                'done_date' => null,
                'reason' => null,
                'done_status' => null,
                'work_progress' => null,
                'more_time' => null,
                'total_time' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            }
        }

        Log::info('✅ Project tasks successfully saved.');
        return response()->json([
            'success' => true,
            'message' => 'Project tasks saved successfully.'
        ]);
    } catch (\Exception $e) {
        Log::error('❌ Save failed:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Save failed.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function verifyPhase(Request $request)
{
    $request->validate([
        'project_id' => 'required|integer',
        'phase_id' => 'required|integer',
    ]);

    $employeeId = auth()->user()->name;  

    // ✅ Check if user is controller
    $isController = DB::table('project_control_people')
        ->where('project_id', $request->project_id)
        ->where('phase_id', $request->phase_id)
        ->where('employee_id', $employeeId)
        ->exists();

    if (!$isController) {
        return response()->json([
            'status' => 'forbidden',
            'message' => 'Sie sind nicht als Controller für diese Phase eingetragen.'
        ], 403);
    }

    DB::table('project_tasks')
        ->where('project_id', $request->project_id)
        ->where('phase_id', $request->phase_id)
        ->update([
            'verify' => 'verified',
            'verify_by' => $employeeId,
            'updated_at' => now(),
        ]);

    $employee = \App\Models\Employee::find($employeeId);

    return response()->json([
        'status' => 'success',
        'verified_by' => $employee?->name . ' ' . $employee?->lastname,
        'verified_at' => now()->format('Y-m-d H:i')
    ]);
}



public function getProjectVerifyStatus($project_id, $phase_id)
{
    $data = DB::table('project_tasks')
        ->leftJoin('employees', 'employees.id', '=', 'project_tasks.verify_by')
        ->where('project_tasks.project_id', $project_id)
        ->where('project_tasks.phase_id', $phase_id)
        ->select(
            'project_tasks.phase_id',
            'project_tasks.verify',
            'project_tasks.verify_by',
            'project_tasks.updated_at',
            'employees.name',
            'employees.lastname',
            'employees.gender',
            'employees.image'
        )
        ->get();

    return response()->json($data);
}



public function unverifyPhase(Request $request)
{
    $request->validate([
        'project_id' => 'required|integer',
        'phase_id' => 'required|integer',
    ]);

    DB::table('project_tasks')
        ->where('project_id', $request->project_id)
        ->where('phase_id', $request->phase_id)
        ->update([
            'verify' => null,
            'verify_by' => null,
            'updated_at' => now(),
        ]);

    return response()->json(['status' => 'success']);
}


}
