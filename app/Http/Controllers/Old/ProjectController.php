<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Employee;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Notifications\ProjectTaskNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;
 use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\ProjectTimelineDoneDate;
use App\Models\ProjectTimeline; 
use App\Models\NewLeads; 

class ProjectController extends Controller
{
  public function __construct(){
        $this->middleware('auth');
    }

    public function index()
        {
            $search = request()->query('search');

            $query = DB::table('projects')
                ->join('new_leads', 'new_leads.id', '=', 'projects.customer_id')
                ->join('lead_alternative_adds as alt', 'alt.id', '=', 'projects.alternative_id')
                ->join('article_groups', 'article_groups.id', '=', 'projects.product_id')
                ->join('employees', 'employees.id', '=', 'projects.employee_id')
                ->select(
                    'new_leads.title', 
                    'new_leads.name', 
                    'new_leads.lastname', 
                    'new_leads.contact_person',
                    'new_leads.email',
                    'new_leads.phone', 
                    'new_leads.telephone', 
                    'new_leads.customer_no', 
                    'alt.object_name', 
                    'alt.street', 
                    'alt.postcode', 
                    'alt.lat', 
                    'alt.lon', 
                    'alt.address_no', 
                    'alt.city',
                    'article_groups.article_group', 
                    'article_groups.initial', 
                    'projects.*',
                    'employees.name as emp_name',
                    'employees.lastname as emp_lastname',
                    'employees.image as emp_image', 
                    'employees.gender', 
                )
                ->where('projects.status', '!=', 'junk')
                ->whereNull('projects.deleted_at')
                ->where('projects.status', '!=', 'complete');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('new_leads.name', 'LIKE', "%$search%")
                    ->orWhere('new_leads.lastname', 'LIKE', "%$search%")
                    ->orWhere('alt.postcode', 'LIKE', "%$search%")
                    ->orWhere('alt.city', 'LIKE', "%$search%")
                    ->orWhere('article_groups.article_group', 'LIKE', "%$search%");
                });
            }

            $data['data'] = $query->paginate(19);

            $data['project_employees'] = DB::table('add_employee_to_projects')
                ->join('employees', 'employees.id', 'add_employee_to_projects.employee_id')
                ->select('add_employee_to_projects.*', 'employees.name','employees.lastname', 'employees.gender', 'employees.image')
                ->get();

            $data['tasks'] = DB::table('task_to_dos')
                ->join('new_leads', 'new_leads.id', '=', 'task_to_dos.customer_id')
                ->join('task_phases', 'task_phases.id', '=', 'task_to_dos.phase_id')
                ->join('phase_activities', 'phase_activities.id', '=', 'task_to_dos.activities_id')
                ->select('task_to_dos.*', 'phase_activities.title as task_title')
                ->where('task_to_dos.done', '=', 'true')
                ->where('task_to_dos.type', '=', 'main')
                ->whereIn('task_to_dos.id', function($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('task_to_dos')
                        ->where('done', '=', 'true')
                        ->groupBy('customer_id');
                })
                ->get();

            $data['timelines'] = DB::table('project_timelines')
                ->join('phase_activities', 'phase_activities.id', '=', 'project_timelines.activity_id')
                ->select(
                    'project_timelines.*',
                    'phase_activities.title as task_title'
                )
                ->whereNotNull('start_date')
                ->whereNotNull('due_date')
                ->get();

            $data['employees'] = DB::table('employees')->where('status', 'Active')->get();

            $highlightId = request()->query('highlight');
            $data['highlightId'] = $highlightId;


            return view('admin.project.customer_view', $data);
        }
    
    public function getProject()
    {
        // Stage mapping
        $stageMap = [
            'new'        => 'new',
            'plan'       => 'plan',
            'process'    => 'process',
            'completed'  => 'completed',
            'junk'       => 'junk',
            'pause'      => 'pause',
        ];
    
        // Service translation map (EN → DE)
        $serviceTranslationMap = [
            'complete'    => 'Komplettlösung',
            'plan'        => 'Planung',
            'offer'       => 'Angebot',
            'project'     => 'Projekt',
            'maintenance' => 'Wartung',
            'repair'      => 'Reparatur',
            'montage'     => 'Montage',
            'product'     => 'Produkt',
        ];
    
        $projects = DB::table('projects')
            ->join('new_leads', 'new_leads.id', '=', 'projects.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'projects.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'projects.product_id')
            ->leftJoin('departments', 'departments.id', '=', 'projects.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'projects.service_id')
            ->leftJoin('employees', 'employees.id', '=', 'projects.employee_id')
    
            // ✅ Progress from average done_range
            ->leftJoin(DB::raw('(
                SELECT 
                    pt.project_id,
                    ROUND(AVG(COALESCE(pt.done_range, 0))) as progress
                FROM project_timelines pt
                INNER JOIN project_tasks task 
                    ON task.project_id = pt.project_id 
                    AND task.phase_id = pt.phase_id 
                    AND task.activities_id = pt.activity_id
                GROUP BY pt.project_id
            ) AS timeline_progress'), 'timeline_progress.project_id', '=', 'projects.id')
            
    
            // ✅ Latest is_done status from project_timelines
            ->leftJoin(DB::raw('(
                SELECT t.project_id, t.is_done
                FROM project_timelines t
                INNER JOIN (
                    SELECT project_id, MAX(updated_at) as latest
                    FROM project_timelines
                    GROUP BY project_id
                ) latest_t ON t.project_id = latest_t.project_id AND t.updated_at = latest_t.latest
            ) AS timeline_status'), 'timeline_status.project_id', '=', 'projects.id')
            
    
            // SELECT
            ->select(
                'projects.id as project_id',
                'projects.status as stage',
                'projects.service',
                'projects.service_id',
                'projects.department_id',
                'projects.employee_id',
                'projects.priority',
                'projects.color',
                'projects.montage_start',
                'projects.end_date',
                'projects.created_at',
                'projects.updated_at',
    
                'new_leads.id as customer_id',
                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',
                'new_leads.phone',
                'new_leads.email',
    
                'alt.id as alternative_id',
                'alt.street',
                'alt.postcode',
                'alt.city',
                'alt.object_name',
    
                'article_groups.id as product_id',
                'article_groups.initial',
    
                'departments.department_name',
                'phase_sections.phase_section',
    
                'employees.name as employee_name',
                'employees.lastname as employee_lastname',
                'employees.image as employee_image',
    
                DB::raw('COALESCE(timeline_progress.progress, 0) as progress'),
                'timeline_status.is_done as project_status'
            )
            ->whereNull('alt.deleted_at')
            ->get();
    
        // Format & return
        $projects = $projects->map(function ($project) use ($stageMap, $serviceTranslationMap) {
            $project->stage = $stageMap[strtolower(trim($project->stage ?? 'new'))] ?? 'new';
    
            $project->employee = [
                'name'        => $project->employee_name,
                'lastname'    => $project->employee_lastname,
                'image'       => $project->employee_image,
                'employee_id' => $project->employee_id,
            ];
    
            $project->department_name = $project->department_name ?? '';
            $project->service_name    = $serviceTranslationMap[strtolower($project->service)] ?? $project->service;
            $project->progress        = (int)($project->progress ?? 0);
            $project->project_status  = $project->project_status ?? '-';
            $project->priority        = $project->priority ?? '-';
            $project->montage_start   = $project->montage_start ?? '';
            $project->end_date        = $project->end_date ?? '';
            $project->color           = $project->color ?? '#f0f0f0';
    
            unset($project->employee_name, $project->employee_lastname, $project->employee_image, $project->phase_section);
    
            return $project;
        });
    
        return response()->json($projects, 200);
    }
    
    
    

    public function getTaskNotifications($project_id, $phase_id): JsonResponse
    {
        // Ensure the project_id and phase_id are cast to strings
        $project_id = (string) $project_id;
        $phase_id = (string) $phase_id;

        // Fetch notifications where the project_id and phase_id match
        $notifications = DatabaseNotification::where('data->project_id', $project_id)
            ->where('data->phase_id', $phase_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Transform the notifications to a simplified JSON structure
        $transformedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Notification',
                'message' => $notification->data['message'] ?? '',
                'project_id' => $notification->data['project_id'] ?? null,
                'phase_id' => $notification->data['phase_id'] ?? null,
                'type' => $notification->data['type'] ?? null,
                'performed_at' => $notification->data['performed_at'] ?? $notification->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'data' => $transformedNotifications,
        ]);
    }

    public function kanban(){

    }


    public function my_projects()
    {
        $search = request()->query('search');

        $query = DB::table('projects')
            ->join('new_leads', 'new_leads.id', '=', 'projects.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'projects.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'projects.product_id')
            ->join('employees', 'employees.id', '=', 'projects.employee_id') 
            ->select(
                'new_leads.title', 
                'new_leads.name', 
                'new_leads.lastname', 
                'new_leads.contact_person',
                'new_leads.email',
                'new_leads.phone', 
                'new_leads.telephone', 
                'new_leads.customer_no', 
                'alt.object_name', 
                'alt.street', 
                'alt.postcode', 
                'alt.lat', 
                'alt.lon', 
                'alt.address_no', 
                'alt.city',
                'article_groups.article_group', 
                'article_groups.initial', 
                'projects.*',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image', 
                'employees.gender', 
            )
            ->where('projects.status', '!=', 'complete') 
            ->where('projects.status', '!=', 'junk')
             ->whereNull('projects.deleted_at')
            ->where('projects.employee_id', '=', auth()->user()->name);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%$search%")
                ->orWhere('new_leads.lastname', 'LIKE', "%$search%")
                ->orWhere('alt.postcode', 'LIKE', "%$search%")
                ->orWhere('alt.city', 'LIKE', "%$search%")
                ->orWhere('article_groups.article_group', 'LIKE', "%$search%");
            });
        }

        $data['data'] = $query->paginate(19);

        $data['project_employees'] = DB::table('add_employee_to_projects')
                                    ->join('employees', 'employees.id', 'add_employee_to_projects.employee_id')
                                    ->select('add_employee_to_projects.*', 'employees.name','employees.lastname', 'employees.gender', 'employees.image')
                                    ->get();
    $data['phases'] = DB::table('customer_phase_lists')
        ->join('task_phases', 'task_phases.id', '=', 'customer_phase_lists.phase_id')
        ->leftJoin('task_to_dos', function($join) {
            $join->on('task_to_dos.phase_id', '=', 'customer_phase_lists.phase_id')
                ->on('task_to_dos.customer_id', '=', 'customer_phase_lists.customer')
                ->on('task_to_dos.product_id', '=', 'customer_phase_lists.product');
        })
        ->select(
            'task_phases.phase_name',
            'customer_phase_lists.customer',
            'customer_phase_lists.product',
            'customer_phase_lists.color',
            'customer_phase_lists.id',
            'customer_phase_lists.alternative',
            'customer_phase_lists.service',
            'task_phases.order', // Include order column
            DB::raw('IFNULL(task_to_dos.done, 0) as done')
        )
        ->distinct()
        ->orderBy('task_phases.order', 'asc')
        ->get();

 
  

   $data['tasks'] = DB::table('task_to_dos')
        ->join('new_leads', 'new_leads.id', '=', 'task_to_dos.customer_id')
        ->join('task_phases', 'task_phases.id', '=', 'task_to_dos.phase_id')
        ->join('phase_activities', 'phase_activities.id', '=', 'task_to_dos.activities_id')
        ->select('task_to_dos.*', 'phase_activities.title as task_title')
        ->where('task_to_dos.done', '=', 'true')
        ->where('task_to_dos.type', '=', 'main')
        ->whereIn('task_to_dos.id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('task_to_dos')
                ->where('done', '=', 'true')
                ->groupBy('customer_id');
        })
        ->get();

        $data['employees'] = DB::table('employees')->where('status', 'Active')->get();


        return view('admin.project.customer_view', $data);
    }
  
     public function list()
    {
        $search = request()->query('search');

        $query = DB::table('projects')
            ->join('new_leads', 'new_leads.id', '=', 'projects.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'projects.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'projects.product_id')
            ->join('employees', 'employees.id', '=', 'projects.employee_id') 
            ->select(
                'new_leads.title', 
                'new_leads.name', 
                'new_leads.lastname', 
                'new_leads.contact_person',
                'new_leads.email',
                'new_leads.phone', 
                'new_leads.telephone', 
                'new_leads.customer_no', 
                'alt.object_name', 
                'alt.street', 
                'alt.postcode', 
                'alt.lat', 
                'alt.lon', 
                'alt.address_no', 
                'alt.city',
                'article_groups.article_group', 
                'article_groups.initial', 
                'projects.*',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image', 
                'employees.gender', 
            )
            ->where('projects.status', '!=', 'complete')
            ->where('projects.status', '!=', 'junk')
               ->whereNull('projects.deleted_at')
            ->where('projects.status', '!=', 'new');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%$search%")
                ->orWhere('new_leads.lastname', 'LIKE', "%$search%")
                ->orWhere('alt.postcode', 'LIKE', "%$search%")
                ->orWhere('alt.city', 'LIKE', "%$search%")
                ->orWhere('article_groups.article_group', 'LIKE', "%$search%");
            });
        }

        $data['data'] = $query->paginate(19);

        $data['project_employees'] = DB::table('add_employee_to_projects')
                                    ->join('employees', 'employees.id', 'add_employee_to_projects.employee_id')
                                    ->select('add_employee_to_projects.*', 'employees.name','employees.lastname', 'employees.gender', 'employees.image')
                                    ->get();
    $data['phases'] = DB::table('customer_phase_lists')
        ->join('task_phases', 'task_phases.id', '=', 'customer_phase_lists.phase_id')
        ->leftJoin('task_to_dos', function($join) {
            $join->on('task_to_dos.phase_id', '=', 'customer_phase_lists.phase_id')
                ->on('task_to_dos.customer_id', '=', 'customer_phase_lists.customer')
                ->on('task_to_dos.product_id', '=', 'customer_phase_lists.product');
        })
        ->select(
            'task_phases.phase_name',
            'customer_phase_lists.customer',
            'customer_phase_lists.product',
            'customer_phase_lists.color',
            'customer_phase_lists.id',
            'customer_phase_lists.alternative',
            'customer_phase_lists.service',
            'task_phases.order', // Include order column
            DB::raw('IFNULL(task_to_dos.done, 0) as done')
        )
        ->distinct()
        ->orderBy('task_phases.order', 'asc')
        ->get();

 
  

   $data['tasks'] = DB::table('task_to_dos')
        ->join('new_leads', 'new_leads.id', '=', 'task_to_dos.customer_id')
        ->join('task_phases', 'task_phases.id', '=', 'task_to_dos.phase_id')
        ->join('phase_activities', 'phase_activities.id', '=', 'task_to_dos.activities_id')
        ->select('task_to_dos.*', 'phase_activities.title as task_title')
        ->where('task_to_dos.done', '=', 'true')
        ->where('task_to_dos.type', '=', 'main')
        ->whereIn('task_to_dos.id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('task_to_dos')
                ->where('done', '=', 'true')
                ->groupBy('customer_id');
        })
        ->get();

        $data['employees'] = DB::table('employees')->where('status', 'Active')->get();


        return view('admin.project.customer_view', $data);
    }

 

    /**
     * Display the specified resource.
     */
    public function junk($id)
    {
        $data = Project::find($id);
        $data->status = 'junk';
        $data->save();

        return redirect()->back()->with('save_msg', 'Das Projekt befindet sich auf der Junk-Liste');
    }

        public function unjunk($id)
    {
        $data = Project::find($id);
        $data->status = 'new';
        $data->save();

        return redirect()->back()->with('save_msg', 'Das Projekt befindet sich in der Liste Neue Projekte');
    }

     public function junk_list()
    {
        $search = request()->query('search');

        $query = DB::table('projects')
            ->join('new_leads', 'new_leads.id', '=', 'projects.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'projects.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'projects.product_id')
            ->join('employees', 'employees.id', '=', 'projects.employee_id') 
            ->select(
                'new_leads.title', 
                'new_leads.name', 
                'new_leads.lastname', 
                'new_leads.contact_person',
                'new_leads.email',
                'new_leads.phone', 
                'new_leads.telephone', 
                'new_leads.customer_no', 
                'alt.object_name', 
                'alt.street', 
                'alt.postcode', 
                'alt.lat', 
                'alt.lon', 
                'alt.address_no', 
                'alt.city',
                'article_groups.article_group', 
                'article_groups.initial', 
                'projects.*',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image', 
                'employees.gender', 
            ) 
            ->where('projects.status', '=', 'junk')
               ->whereNull('projects.deleted_at');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%$search%")
                ->orWhere('new_leads.lastname', 'LIKE', "%$search%")
                ->orWhere('alt.postcode', 'LIKE', "%$search%")
                ->orWhere('alt.city', 'LIKE', "%$search%")
                ->orWhere('article_groups.article_group', 'LIKE', "%$search%");
            });
        }

        $data['data'] = $query->paginate(19);

        $data['project_employees'] = DB::table('add_employee_to_projects')
                                    ->join('employees', 'employees.id', 'add_employee_to_projects.employee_id')
                                    ->select('add_employee_to_projects.*', 'employees.name','employees.lastname', 'employees.gender', 'employees.image')
                                    ->get();
    $data['phases'] = DB::table('customer_phase_lists')
        ->join('task_phases', 'task_phases.id', '=', 'customer_phase_lists.phase_id')
        ->leftJoin('task_to_dos', function($join) {
            $join->on('task_to_dos.phase_id', '=', 'customer_phase_lists.phase_id')
                ->on('task_to_dos.customer_id', '=', 'customer_phase_lists.customer')
                ->on('task_to_dos.product_id', '=', 'customer_phase_lists.product');
        })
        ->select(
            'task_phases.phase_name',
            'customer_phase_lists.customer',
            'customer_phase_lists.product',
            'customer_phase_lists.color',
            'customer_phase_lists.id',
            'customer_phase_lists.alternative',
            'customer_phase_lists.service',
            'task_phases.order', // Include order column
            DB::raw('IFNULL(task_to_dos.done, 0) as done')
        )
        ->distinct()
        ->orderBy('task_phases.order', 'asc')
        ->get();

 
  

   $data['tasks'] = DB::table('task_to_dos')
        ->join('new_leads', 'new_leads.id', '=', 'task_to_dos.customer_id')
        ->join('task_phases', 'task_phases.id', '=', 'task_to_dos.phase_id')
        ->join('phase_activities', 'phase_activities.id', '=', 'task_to_dos.activities_id')
        ->select('task_to_dos.*', 'phase_activities.title as task_title')
        ->where('task_to_dos.done', '=', 'true')
        ->where('task_to_dos.type', '=', 'main')
        ->whereIn('task_to_dos.id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('task_to_dos')
                ->where('done', '=', 'true')
                ->groupBy('customer_id');
        })
        ->get();

        $data['employees'] = DB::table('employees')->where('status', 'Active')->get();


        return view('admin.project.customer_view', $data);
    }

  

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Project::find($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'Das Projekt wurde erfolgreich gelöscht');
    }
      

        public function restore($id)
    {
        $data = Project::withTrashed()->find($id);

        if ($data) {
            $data->restore(); // Restores the soft-deleted record
            return redirect()->back()->with('save_msg', 'Anfrage erfolgreich wiederhergestellt');
        }

        return redirect()->back()->with('error', 'Anfrage nicht gefunden');
    }

      public function delete_lists()
    {
        $search = request()->query('search');

        $query = DB::table('projects')
            ->join('new_leads', 'new_leads.id', '=', 'projects.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'projects.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'projects.product_id')
            ->join('employees', 'employees.id', '=', 'projects.employee_id') 
            ->select(
                'new_leads.title', 
                'new_leads.name', 
                'new_leads.lastname', 
                'new_leads.contact_person',
                'new_leads.email',
                'new_leads.phone', 
                'new_leads.telephone', 
                'new_leads.customer_no', 
                'alt.object_name', 
                'alt.street', 
                'alt.postcode', 
                'alt.lat', 
                'alt.lon', 
                'alt.address_no', 
                'alt.city',
                'article_groups.article_group', 
                'article_groups.initial', 
                'projects.*',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image', 
                'employees.gender', 
            )  
               ->whereNotNull('projects.deleted_at');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%$search%")
                ->orWhere('new_leads.lastname', 'LIKE', "%$search%")
                ->orWhere('alt.postcode', 'LIKE', "%$search%")
                ->orWhere('alt.city', 'LIKE', "%$search%")
                ->orWhere('article_groups.article_group', 'LIKE', "%$search%");
            });
        }

        $data['data'] = $query->paginate(19);

        $data['project_employees'] = DB::table('add_employee_to_projects')
                                    ->join('employees', 'employees.id', 'add_employee_to_projects.employee_id')
                                    ->select('add_employee_to_projects.*', 'employees.name','employees.lastname', 'employees.gender', 'employees.image')
                                    ->get();
    $data['phases'] = DB::table('customer_phase_lists')
        ->join('task_phases', 'task_phases.id', '=', 'customer_phase_lists.phase_id')
        ->leftJoin('task_to_dos', function($join) {
            $join->on('task_to_dos.phase_id', '=', 'customer_phase_lists.phase_id')
                ->on('task_to_dos.customer_id', '=', 'customer_phase_lists.customer')
                ->on('task_to_dos.product_id', '=', 'customer_phase_lists.product');
        })
        ->select(
            'task_phases.phase_name',
            'customer_phase_lists.customer',
            'customer_phase_lists.product',
            'customer_phase_lists.color',
            'customer_phase_lists.id',
            'customer_phase_lists.alternative',
            'customer_phase_lists.service',
            'task_phases.order', // Include order column
            DB::raw('IFNULL(task_to_dos.done, 0) as done')
        )
        ->distinct()
        ->orderBy('task_phases.order', 'asc')
        ->get();
 

   $data['tasks'] = DB::table('task_to_dos')
        ->join('new_leads', 'new_leads.id', '=', 'task_to_dos.customer_id')
        ->join('task_phases', 'task_phases.id', '=', 'task_to_dos.phase_id')
        ->join('phase_activities', 'phase_activities.id', '=', 'task_to_dos.activities_id')
        ->select('task_to_dos.*', 'phase_activities.title as task_title')
        ->where('task_to_dos.done', '=', 'true')
        ->where('task_to_dos.type', '=', 'main')
        ->whereIn('task_to_dos.id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('task_to_dos')
                ->where('done', '=', 'true')
                ->groupBy('customer_id');
        })
        ->get();

        $data['employees'] = DB::table('employees')->where('status', 'Active')->get();


        return view('admin.project.customer_view', $data);
    }



 public function changeStatus(Request $request)
{
    $request->validate([
        'project_id' => 'required|exists:projects,id',
        'new_status' => 'required|string|in:new,plan,process,completed,junk,pause'
    ]);

    $project = Project::findOrFail($request->project_id);
    $project->status = $request->new_status;
    $project->save();

    return response()->json(['success' => true]);
}


public function assignLeader(Request $request)
{
    \Log::info('emplloyee:', [$request->all()]);
    $request->validate([
        'project_id' => 'required|exists:projects,id',
        'project_leader_id' => 'required|exists:employees,id'
    ]);

    $project = Project::find($request->project_id);
    $project->project_leader = $request->project_leader_id;
    $project->save();

    $leader = Employee::find($request->project_leader_id);

    return response()->json([
        'success' => true,
        'leader_name' => $leader->name . ' ' . $leader->lastname,
        'leader_image' => $leader->image
    ]);

}

public function employeeList()
{
    return Employee::select('id', 'name', 'lastname', 'image')->get();
}


public function getLeader($projectId)
{
    $project = DB::table('projects')
        ->join('employees', 'employees.id', '=', 'projects.project_leader')
        ->select('employees.name', 'employees.lastname', 'employees.image', 'employees.gender')
        ->where('projects.id', '=', $projectId)
        ->first();

    return response()->json([
        'firstname' => $project->name,
        'lastname'  => $project->lastname,
        'image'     => $project->image ?? ($project->gender === 'female' ? 'female.png' : 'male.png'),
    ]);
}


public function deleteEmployee(Request $request) {
    $request->validate([
        'project_id' => 'required|integer',
        'phase_id' => 'nullable|integer',
        'employee_id' => 'required|integer',
    ]);

    DB::table('add_employee_to_projects')
        ->where('project_id', $request->project_id)
        ->where('phase_id', $request->phase_id)
        ->where('employee_id', $request->employee_id)
        ->delete();

    return response()->json(['success' => true, 'message' => 'Mitarbeiter entfernt.']);
}

public function fetchTimelineLogs(Request $request, $projectId, $phaseId, $activityId)
{
    $query = DB::table('project_timeline_done_dates as done')
        ->leftJoin('employees as emp', 'done.done_by', '=', 'emp.id')
        ->leftJoin('project_timelines as tl', 'done.timeline_id', '=', 'tl.id')
        ->leftJoin('task_phases as phase', 'tl.phase_id', '=', 'phase.id')
        ->leftJoin('phase_activities as activity', 'tl.activity_id', '=', 'activity.id')
        ->leftJoin('projects as proj', 'tl.project_id', '=', 'proj.id')
        ->select(
            'done.*',
            'emp.name as employee_name',
            'tl.start_date as timeline_start',
            'tl.due_date as timeline_due',
            'tl.is_done as timeline_status',
            'tl.done_range as timeline_done_range',
            'tl.phase_id',
            'tl.activity_id',
            'phase.phase_name',
            'activity.title as activity_title',
            'proj.service as project_service'
        )
        ->where('done.project_id', $projectId)
        ->where('tl.phase_id', $phaseId)
        ->where('tl.activity_id', $activityId);

    if ($request->emp_id) {
        $query->where('done.done_by', $request->emp_id);
    }

    if ($request->date) {
        $query->whereDate('done.done_date', $request->date);
    }

    $logs = $query->orderByDesc('done.done_date')->get();

    // Get timeline info for header section (optional)
    $timeline = DB::table('project_timelines as tl')
        ->leftJoin('task_phases as phase', 'tl.phase_id', '=', 'phase.id')
        ->leftJoin('phase_activities as activity', 'tl.activity_id', '=', 'activity.id')
        ->leftJoin('projects as proj', 'tl.project_id', '=', 'proj.id')
        ->select(
            'tl.*',
            'phase.phase_name',
            'activity.title as activity_title',
            'proj.service as project_service'
        )
        ->where('tl.project_id', $projectId)
        ->where('tl.phase_id', $phaseId)
        ->where('tl.activity_id', $activityId)
        ->first();

    $expected = $timeline?->done_range ?? 1;
    $completed = $logs->count();
    $progress = $expected > 0 ? round(($completed / $expected) * 100) : 0;

    $html = view('admin.project.logs.timeline_log_entries', compact('logs', 'timeline'))->render();

    return response()->json(['html' => $html, 'progress' => $progress]);
}



public function storeTimelineLog(Request $request)
{
    $request->validate([
        'project_id' => 'required|exists:projects,id',
        'timeline_id' => 'required|exists:project_timelines,id',
        'done_by' => 'required|exists:employees,id',
        'done_date' => 'required|date',
    ]);

    ProjectTimelineDoneDate::create([
        'project_id' => $request->project_id,
        'timeline_id' => $request->timeline_id,
        'done_by' => $request->done_by,
        'done_date' => $request->done_date,
        'timeline_range' => ProjectTimeline::find($request->timeline_id)?->timeline_range,
    ]);

    return response()->json(['success' => true]);
}


public function getEmployees($projectId)
{
    $employees = DB::table('add_employee_to_projects')
        ->join('employees', 'employees.id', '=', 'add_employee_to_projects.employee_id')
        ->where('add_employee_to_projects.project_id', $projectId) 
        ->select('employees.id', 'employees.name', 'employees.lastname', 'employees.image')
        ->distinct()
        ->get();

    return response()->json($employees);
}


public function getProductsByCustomer($customerId)
{
    $products = DB::table('lead_product_lists')
        ->join('article_groups', 'lead_product_lists.product_id', '=', 'article_groups.id')
        ->where('lead_product_lists.customer_id', $customerId) // ✅ FIXED
        ->select('article_groups.id', 'article_groups.article_group')
        ->distinct()
        ->get();

    return response()->json($products);
}


public function create()
{
    $customers = NewLeads::select('id', 'name', 'lastname')->get();
    return view('admin.project.new_project', compact('customers'));
}

public function store(Request $request)
{
    $request->validate([
        'customer_id' => 'required|exists:new_leads,id',
        'product_list_id' => 'required|exists:lead_product_lists,id',
    ]);

    $productList = DB::table('lead_product_lists')->where('id', $request->product_list_id)->first();

    if (!$productList) {
        return back()->with('delete_msg', 'Produktliste nicht gefunden.');
    }

    // 🔍 Check for duplicate project
    $duplicate = DB::table('projects')
        ->where('customer_id', $request->customer_id)
        ->where('product_id', $productList->product_id)
        ->where('alternative_id', $productList->alternative_id)
        ->where('service_id', $productList->service_id)
        ->where('department_id', $productList->department_id)
        ->where('employee_id', $productList->employee_id)
        ->whereNull('deleted_at')
        ->first();

    if ($duplicate) {
        return back()->with('delete_msg', 'Ein Projekt mit denselben Daten existiert bereits.');
    }

    // ✅ Create new project
    $project = new Project();
    $project->customer_id = $request->customer_id;
    $project->product_id = $productList->product_id;
    $project->alternative_id = $productList->alternative_id;
    $project->department_id = $productList->department_id;
    $project->employee_id = $productList->employee_id ?? auth()->id();
    $project->service_id = $productList->service_id;
    $project->service = $productList->service ?? 'default';
    $project->status = 'new';
    $project->save();

    return redirect()->route('project.details', ['highlight' => $project->id])
    ->with('save_msg', 'Projekt erfolgreich erstellt!');
}




public function getProductLists($customerId)
{
    $products = DB::table('lead_product_lists')
        ->join('article_groups', 'lead_product_lists.product_id', '=', 'article_groups.id')
        ->where('lead_product_lists.customer_id', $customerId)
        ->select(
            'lead_product_lists.id',
            'lead_product_lists.product_id',
            'lead_product_lists.alternative_id',
            'lead_product_lists.department_id',
            'lead_product_lists.service_id',
            'lead_product_lists.employee_id',
            'lead_product_lists.service',
            'article_groups.article_group'
        )
        ->get();

    return response()->json($products);
}


}
