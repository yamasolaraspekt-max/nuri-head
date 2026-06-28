<?php

namespace App\Http\Controllers;

use App\Models\ProjectMontageChecklist;
use Illuminate\Http\Request;
use App\Models\ArticleGroup;
use App\Models\Employee;
use App\Models\PhaseActivities;
use DB;

class ProjectMontageChecklistController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }
   

   public function index(Request $request)
{
    $search = $request->search;
    $sortBy = $request->get('sort_by', 'id');
    $sortDirection = $request->get('sort_direction', 'asc');

    $data = ProjectMontageChecklist::with(['articleGroup', 'employee'])
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('project_montage_checklists.list_name', 'like', "%$search%")
                ->orWhere('project_montage_checklists.status', 'like', "%$search%")
                ->orWhereHas('articleGroup', function ($q2) use ($search) {
                    $q2->where('article_group', 'like', "%$search%");
                })
                ->orWhereHas('employee', function ($q3) use ($search) {
                    $q3->where('name', 'like', "%$search%")
                        ->orWhere('lastname', 'like', "%$search%");
                });
            });
        })
        ->leftJoin('article_groups', 'article_groups.id', '=', 'project_montage_checklists.product_id')
        ->leftJoin('employees', 'employees.id', '=', 'project_montage_checklists.employee_id')
        ->select('project_montage_checklists.*', 'article_groups.article_group as product_name', 'employees.name as employee_name')
        ->when($sortBy === 'article_group', fn($q) => $q->orderBy('article_groups.article_group', $sortDirection))
        ->when($sortBy === 'employee_id', fn($q) => $q->orderBy('employees.name', $sortDirection))
        ->when(!in_array($sortBy, ['article_group', 'employee_id']), fn($q) => $q->orderBy("project_montage_checklists.$sortBy", $sortDirection))
        ->paginate(10);


        

    if ($request->ajax()) {
        return response()->json([
            'html' => view('admin.checklist.partial.index', compact('data', 'sortBy', 'sortDirection'))->render(),
        ]);
    }

    $articleGroups = ArticleGroup::all();
    $employees = Employee::all();

    return view('admin.checklist.checklist', compact('data', 'articleGroups', 'employees', 'sortBy', 'sortDirection'));
}



    // Store checklist via AJAX
   public function store(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:article_groups,id',
        'employee_id' => 'required|exists:employees,id',
        'list_name' => 'required',
        'activities' => 'required|array',
        'activities.*.phase_id' => 'required|exists:task_phases,id',
        'activities.*.activity_id' => 'required|exists:phase_activities,id',
        'plan_montage' => 'nullable|integer',
        'supplier_section' => 'nullable|integer',
        'cran_section' => 'nullable|integer',
        'old_facility' => 'nullable|integer',
        'photo_section' => 'nullable|integer',
        'commission' => 'nullable|integer',
        'status' => 'nullable|string|max:255',
    ]);

    // Save checklist
    $checklist = ProjectMontageChecklist::create([
        'product_id' => $validated['product_id'],
        'employee_id' => auth()->user()->name,
        'list_name' => $validated['list_name'] ?? 'Neue Checkliste',
        'plan_montage' => $validated['plan_montage'] ?? null,
        'supplier_section' => $validated['supplier_section'] ?? null,
        'cran_section' => $validated['cran_section'] ?? null,
        'old_facility' => $validated['old_facility'] ?? null,
        'photo_section' => $validated['photo_section'] ?? null,
        'commission' => $validated['commission'] ?? null,
        'status' => $validated['status'] ?? null,
    ]);

    // Store checklist phases
    foreach ($validated['activities'] as $activity) {
        $checklist->phaseLists()->create([
            'phase_id' => $activity['phase_id'],
            'activity_id' => $activity['activity_id'],
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Checkliste erfolgreich gespeichert!',
    ]);
}

    // Delete checklist
    public function destroy($id)
    {
        $checklist = ProjectMontageChecklist::findOrFail($id);
        $checklist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Checklist deleted successfully!',
        ]);
    }


    public function create(){

       
        $data['article'] = DB::table('article_groups')->get();
        
        return view('admin.checklist.checklist_create', $data);
    }

    public function getPhase($product_id){

         $data = DB::table('task_phases')
                ->join('phase_activities as active', 'active.phase_id', '=', 'task_phases.id')
                ->select('task_phases.id as phase_id', 'active.id as active_id',
                            'task_phases.phase_name', 
                            'active.product_id', 
                            'active.section_id', 
                            'active.parent_id', 
                            'active.title',
                            'active.description',
                            'active.duration',
                            'active.answered_by',
                            'active.status'
                    )
                    ->where('active.status', '=', 'published')
                    ->where('active.product_id', $product_id)
                    ->whereNull('task_phases.deleted_at')
                    ->orderBy('task_phases.order')
                    ->get();
          return response()->json($data, 200);
    }

    public function edit($id)
{
    $checklist = ProjectMontageChecklist::with(['phaseLists', 'articleGroup'])->findOrFail($id);
    $article = ArticleGroup::all();

    // Collect selected activity IDs for pre-check
    $selectedActivities = $checklist->phaseLists->map(function ($item) {
        return $item->activity_id; // keep it simple, just activity IDs
    });


    return view('admin.checklist.checklist_edit', compact('checklist', 'article', 'selectedActivities'));
}


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:article_groups,id',
            'employee_id' => 'required|exists:employees,id',
            'list_name' => 'required|string|max:255',
            'activities' => 'required|array',
            'activities.*.phase_id' => 'required|exists:task_phases,id',
            'activities.*.activity_id' => 'required|exists:phase_activities,id',
            'plan_montage' => 'nullable|integer',
            'supplier_section' => 'nullable|integer',
            'cran_section' => 'nullable|integer',
            'old_facility' => 'nullable|integer',
            'photo_section' => 'nullable|integer',
            'commission' => 'nullable|integer',
            'status' => 'nullable|string|max:255',
        ]);

        $checklist = ProjectMontageChecklist::findOrFail($id);

        // Update checklist data
        $checklist->update([
            'product_id' => $validated['product_id'],
            'employee_id' => $validated['employee_id'],
            'list_name' => $validated['list_name'],
            'plan_montage' => $validated['plan_montage'] ?? 0,
            'supplier_section' => $validated['supplier_section'] ?? 0,
            'cran_section' => $validated['cran_section'] ?? 0,
            'old_facility' => $validated['old_facility'] ?? 0,
            'photo_section' => $validated['photo_section'] ?? 0,
            'commission' => $validated['commission'] ?? null,
            'status' => $validated['status'] ?? null,
        ]);

        // Delete old activities and re-attach
        $checklist->phaseLists()->delete();

        foreach ($validated['activities'] as $activity) {
            $checklist->phaseLists()->create([
                'phase_id' => $activity['phase_id'],
                'activity_id' => $activity['activity_id'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Checkliste erfolgreich aktualisiert!',
        ]);
    }



    public function setDefaultChecklist($id)
    {
        $checklist = ProjectMontageChecklist::findOrFail($id);

        // Reset default_stage for all checklists of the same product
        ProjectMontageChecklist::where('product_id', $checklist->product_id)
            ->update(['default_stage' => null]);

        // Set selected checklist as default
        $checklist->default_stage = 'yes';
        $checklist->save();

        return redirect()->back()->with('success', 'Default checklist updated for this product.');
    }


 

    public function getChecklistById($id)
    {
        $checklist = DB::table('project_montage_checklists')->where('id', $id)->first();

        if (!$checklist) {
            return response()->json(['message' => 'Checklist not found'], 404);
        }

        // Load tasks related to this checklist
        $phasesRaw = DB::table('project_montage_phase_lists as list')
            ->join('task_phases', 'task_phases.id', '=', 'list.phase_id')
            ->join('phase_activities as activity', 'activity.id', '=', 'list.activity_id') 
            ->leftJoin('activity_positions', 'activity_positions.id', '=', 'activity_positions.position_id') 
            ->where('list.project_montage_id', $id)
            ->select(
                'task_phases.phase_name',
                'task_phases.id as phase_id',
                'task_phases.section_id',
                'task_phases.section_name',
                'activity.id as activity_id', 
                'activity.title as title_activity', 
                'activity.duration', 
                'activity.description', 
                'activity_positions.position_id',
                'activity.status',
                'activity.photo',
                'activity.answered_by'
            )
            ->orderBy('task_phases.order')
            ->get();

        // Group tasks under phases
        $phases = [];
        foreach ($phasesRaw as $row) {
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
                 'activity_id' => $row->activity_id,   
                'title_activity' => $row->title_activity,
                'duration' => $row->duration,
                'description' => $row->description,
                'position_id' => $row->position_id,
                'status' => $row->status,
                'photo' => $row->photo,
                'answered_by' => $row->answered_by,
            ];
        }

        return response()->json([
            'phases' => array_values($phases)
        ]);
    }


 
public function getChecklistTask($product)
{
    // Step 1: Get checklist
    $checklist = DB::table('project_montage_checklists')
                    ->where('product_id', $product)
                    ->where('default_stage', 'yes')
                    ->whereNull('deleted_at')
                    ->first();

    if (!$checklist) {
        return response()->json(['message' => 'No checklist found for this product.'], 404);
    }

    // Step 2: Get all activity-phase relationships
    $phaseActivities = DB::table('project_montage_checklists')
        ->join('project_montage_phase_lists as list', 'list.project_montage_id', '=', 'project_montage_checklists.id')
        ->join('task_phases', 'task_phases.id', '=', 'list.phase_id')
        ->join('phase_activities as activity', 'activity.id', '=', 'list.activity_id')
        ->select(
            'task_phases.phase_name',
            'task_phases.id as main_phase_id',
            'task_phases.section_id',
            'task_phases.section_name',
            'activity.id as activity_id',
            'activity.title as title_activity',
            'activity.duration',
            'activity.description',
            'activity.status',
            'activity.photo',
            'activity.answered_by'
        )
        ->where('project_montage_checklists.product_id', $product)
        ->where('project_montage_checklists.default_stage', 'yes')
        ->orderBy('task_phases.order')
        ->get();

    $activityIds = $phaseActivities->pluck('activity_id')->unique();

    // Step 3: Get related positions, departments, articles
        $positions = DB::table('activity_positions')
            ->whereIn('activity_id', $activityIds)
            ->get()
            ->groupBy('activity_id')
            ->map(function ($items) {
                return $items->pluck('position_id');
            });


        $departments = DB::table('activity_departments')
        ->whereIn('activity_id', $activityIds)
        ->get()
        ->groupBy('activity_id')
        ->map(function ($items) {
            return $items->pluck('department_id');
        });

    $articles = DB::table('activity_articles')
        ->whereIn('activity_id', $activityIds)
        ->get()
        ->groupBy('activity_id')
        ->map(function ($items) {
            return $items->pluck('article_id');
        });


    // Step 4: Group into phases
    $phases = [];
    foreach ($phaseActivities as $row) {
        $phaseKey = $row->main_phase_id;

        if (!isset($phases[$phaseKey])) {
            $phases[$phaseKey] = [
                'phase_id' => $row->main_phase_id,
                'phase_name' => $row->phase_name,
                'section_id' => $row->section_id,
                'section_name' => $row->section_name,
                'activities' => []
            ];
        }

        $activityId = $row->activity_id;

       $phases[$phaseKey]['activities'][] = [
        'activity_id'    => $activityId,
        'title_activity' => $row->title_activity,
        'duration'       => $row->duration,
        'description'    => $row->description,
        'status'         => $row->status,
        'photo'          => $row->photo,
        'answered_by'    => $row->answered_by,
        'positions'      => $positions->get($activityId)?->values() ?? [],
        'departments'    => $departments->get($activityId)?->values() ?? [],
        'articles'       => $articles->get($activityId)?->values() ?? [],
    ];

    }

    return [
        'product_id' => $checklist->product_id,
        'employee_id' => $checklist->employee_id,
        'list_name' => $checklist->list_name,
        'plan_montage' => $checklist->plan_montage,
        'supplier_section' => $checklist->supplier_section,
        'cran_section' => $checklist->cran_section,
        'old_facility' => $checklist->old_facility,
        'photo_section' => $checklist->photo_section,
        'commission' => $checklist->commission,
        'default_stage' => $checklist->default_stage,
        'phase_id' => $checklist->phase_id ?? null,
        'project_montage_id' => $checklist->id,
        'activity_id' => $checklist->activity_id ?? null,
        'phases' => array_values($phases)
    ];
}


public function getAllChecklistTask($product)
{
    $checklists = DB::table('project_montage_checklists')
                    ->where('product_id', $product)
                    ->whereNull('deleted_at')
                    ->select('id', 'list_name', 'default_stage')
                    ->get();

    if ($checklists->isEmpty()) {
        return response()->json(['message' => 'No checklist found for this product.'], 404);
    }

    return response()->json([
        'checklists' => $checklists,
        'default' => $checklists->firstWhere('default_stage', 'yes')
    ]);
}



public function getCustomer($id, $alternative)
{
    $customer = DB::table('projects')
        ->join('new_leads as customer', 'customer.id', '=', 'projects.customer_id')
        ->join('article_groups', 'article_groups.id', '=', 'projects.product_id')
        ->join('lead_alternative_adds as alt', 'alt.id', '=', 'projects.alternative_id')
        ->leftJoin('employees as assigned', 'assigned.id', '=', 'projects.employee_id')
        ->leftJoin('employees as leader', 'leader.id', '=', 'projects.project_leader')
        ->select(
            'projects.id as project_id',
            'customer.name',
            'customer.lastname',
            'article_groups.article_group as product_name',
            'alt.object_name',
            'alt.street',
            'alt.postcode',
            'alt.city',
            'alt.lat',
            'alt.lon',
            'alt.stage',
            'alt.request_date',
            'assigned.name as emp_name',
            'assigned.lastname as emp_lastname',
            'assigned.image as emp_image',
            'leader.id as leader_id',
            'leader.name as leader_name',
            'leader.lastname as leader_lastname',
            'leader.image as leader_image',
            'projects.product_id',
            'projects.service'
        )
        ->where('customer.id', $id)
        ->where('alt.id', $alternative)
        ->first();

    if (!$customer) {
        return response()->json(['message' => 'Projekt nicht gefunden'], 404);
    }

    $employees = DB::table('add_employee_to_projects')
        ->join('employees', 'employees.id', '=', 'add_employee_to_projects.employee_id')
        ->where('add_employee_to_projects.project_id', $customer->project_id)
        ->select('employees.name', 'employees.lastname', 'employees.image')
        ->get();

    return response()->json([
        ...((array) $customer),
        'employees' => $employees
    ]);
}


    
}
