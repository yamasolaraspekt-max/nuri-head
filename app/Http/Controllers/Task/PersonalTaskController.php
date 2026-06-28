<?php


namespace App\Http\Controllers\Task;
use App\Http\Controllers\Controller;

use App\Models\PersonalTask;
use Illuminate\Http\Request;
use DB; 
 use App\Models\PersonalTaskKey;
use App\Models\PersonalSubTask;
use App\Models\NewLeads;
use App\Models\Branch;
use App\Models\MainAppointment;
use App\Models\MainAppointmentEmployee;
use App\Models\EmployeesPersonalTask;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Notifications\PersonalTaskNotification;
use App\Notifications\AppointmentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;
 use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use App\Models\ArticleGroup; 
use Carbon\Carbon;   
use App\Models\Employee;
use App\Models\EmployeeRecurringLeave;
use App\Models\EmployeeRecurringLeaveOverride;
use App\Models\Team;
 use Illuminate\Support\Collection; 
 use App\Models\Department;    
 use App\Models\ProductPosition;
 use App\Models\PhaseSection;
 use App\Models\PVRoof;
use App\Models\LeadObjectRoom;
 use Illuminate\Support\Str;
 use Throwable;
 class PersonalTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function __construct(){
    $this->middleware('auth');
}

    public function index(Request $request)
    {
        $employeeId = (int) auth()->user()->name;

        $tab = $request->get('tab', 'my');
        $view = $request->get('view', 'board');

        $filters = [
            'search' => $request->get('search'),
            'priority' => $request->get('priority'),
            'public' => $request->get('public'),
            'isReport' => $request->get('is_report'),
            'sort' => $request->get('sort', 'created_at'),
            'dir' => $request->get('dir', 'desc'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | - personal_tasks has assigned_by
        | - assigned employees are in employees_personal_tasks
        | - do NOT use personal_tasks.employee_id
        |--------------------------------------------------------------------------
        */
        $base = PersonalTask::query()
            ->with([
                'employees',
                'customer',
                'assignedBy',
                'product',
            ])
            ->withTrashed();

        /*
        |--------------------------------------------------------------------------
        | Only tasks where current employee is involved
        |--------------------------------------------------------------------------
        */
        $base->where(function ($q) use ($employeeId) {
            $q->where('personal_tasks.assigned_by', $employeeId)
                ->orWhereExists(function ($sub) use ($employeeId) {
                    $sub->selectRaw('1')
                        ->from('employees_personal_tasks')
                        ->whereColumn('employees_personal_tasks.task_id', 'personal_tasks.id')
                        ->where('employees_personal_tasks.employee_id', $employeeId);
                });
        });

        /*
        |--------------------------------------------------------------------------
        | Tabs
        |--------------------------------------------------------------------------
        */
        switch ($tab) {
            case 'my':
                $base->whereNull('personal_tasks.deleted_at')
                    ->whereNull('personal_tasks.archived_at')
                    ->whereIn('personal_tasks.task_status', ['open', 'on_progress'])
                    ->where('personal_tasks.assigned_by', '!=', $employeeId)
                    ->whereExists(function ($sub) use ($employeeId) {
                        $sub->selectRaw('1')
                            ->from('employees_personal_tasks')
                            ->whereColumn('employees_personal_tasks.task_id', 'personal_tasks.id')
                            ->where('employees_personal_tasks.employee_id', $employeeId);
                    });
                break;

            case 'created':
                $base->whereNull('personal_tasks.deleted_at')
                    ->whereNull('personal_tasks.archived_at')
                    ->where('personal_tasks.assigned_by', $employeeId)
                    ->whereIn('personal_tasks.task_status', ['open', 'on_progress']);
                break;

            case 'completed':
                $base->whereNull('personal_tasks.deleted_at')
                    ->whereNull('personal_tasks.archived_at')
                    ->where('personal_tasks.task_status', 'completed');
                break;

            case 'paused':
                $base->whereNull('personal_tasks.deleted_at')
                    ->whereNull('personal_tasks.archived_at')
                    ->where('personal_tasks.task_status', 'pause');
                break;

            case 'rejected':
                $base->whereNull('personal_tasks.deleted_at')
                    ->whereNull('personal_tasks.archived_at')
                    ->whereExists(function ($sub) use ($employeeId) {
                        $sub->selectRaw('1')
                            ->from('employees_personal_tasks')
                            ->whereColumn('employees_personal_tasks.task_id', 'personal_tasks.id')
                            ->where('employees_personal_tasks.employee_id', $employeeId)
                            ->where('employees_personal_tasks.status', 'rejected');
                    });
                break;

            case 'archived':
                $base->whereNull('personal_tasks.deleted_at')
                    ->whereNotNull('personal_tasks.archived_at');
                break;

            case 'deleted':
                $base->onlyTrashed();
                break;

            default:
                $base->whereNull('personal_tasks.deleted_at')
                    ->whereNull('personal_tasks.archived_at');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
        if (!empty($filters['priority'])) {
            $base->where('personal_tasks.priority', $filters['priority']);
        }

        if ($filters['public'] !== null && $filters['public'] !== '') {
            $base->where('personal_tasks.public', (int) $filters['public']);
        }

        if ($filters['isReport'] !== null && $filters['isReport'] !== '') {
            $base->where('personal_tasks.is_report', (int) $filters['isReport']);
        }

        if (!empty($filters['search'])) {
            $s = trim($filters['search']);

            $base->where(function ($q) use ($s) {
                $q->where('personal_tasks.task_title', 'like', "%{$s}%")
                    ->orWhere('personal_tasks.description', 'like', "%{$s}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */
        $allowedSorts = [
            'created_at',
            'due_date',
            'priority',
            'task_title',
        ];

        $sort = in_array($filters['sort'], $allowedSorts, true)
            ? $filters['sort']
            : 'created_at';

        $dir = $filters['dir'] === 'asc' ? 'asc' : 'desc';

        /*
        |--------------------------------------------------------------------------
        | List View
        |--------------------------------------------------------------------------
        */
        $tasks = (clone $base)
            ->orderBy("personal_tasks.{$sort}", $dir)
            ->paginate(20)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Board Columns
        |--------------------------------------------------------------------------
        */
        $columns = [];

        if ($tab === 'deleted') {
            $columns['deleted'] = (clone $base)
                ->orderBy("personal_tasks.{$sort}", $dir)
                ->get();

        } elseif ($tab === 'archived') {
            $columns['archived'] = (clone $base)
                ->orderBy("personal_tasks.{$sort}", $dir)
                ->get();

        } elseif ($tab === 'rejected') {
            $columns['rejected'] = (clone $base)
                ->orderBy("personal_tasks.{$sort}", $dir)
                ->get();

        } else {
            $columns['open'] = (clone $base)
                ->where('personal_tasks.task_status', 'open')
                ->whereNull('personal_tasks.deleted_at')
                ->orderBy("personal_tasks.{$sort}", $dir)
                ->get();

            $columns['in_progress'] = (clone $base)
                ->where('personal_tasks.task_status', 'on_progress')
                ->whereNull('personal_tasks.deleted_at')
                ->orderBy("personal_tasks.{$sort}", $dir)
                ->get();

            $columns['completed'] = (clone $base)
                ->where('personal_tasks.task_status', 'completed')
                ->whereNull('personal_tasks.deleted_at')
                ->orderBy("personal_tasks.{$sort}", $dir)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Employees for Form
        |--------------------------------------------------------------------------
        */
        $employees = Employee::where('status', '!=', 'Deactive')
            ->orderBy('name')
            ->get(['id', 'name', 'lastname', 'image', 'gender']);

        $employeeOptions = $employees->map(function ($emp) {
            return [
                'id' => $emp->id,
                'name' => $emp->name,
                'lastname' => $emp->lastname,
                'image' => $emp->image
                    ? asset('images/employee/' . $emp->image)
                    : null,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Stats / Activity
        |--------------------------------------------------------------------------
        | Make sure these methods also do NOT use personal_tasks.employee_id.
        |--------------------------------------------------------------------------
        */
        $stats = method_exists($this, 'buildStatsForEmployee')
            ? $this->buildStatsForEmployee($employeeId)
            : [];

        $activityFeed = method_exists($this, 'buildActivityFeed')
            ? $this->buildActivityFeed($employeeId)
            : collect();

        return view('admin.todo.personal.task_view', [
            'tasks' => $tasks,
            'columns' => $columns,
            'tab' => $tab,
            'view' => $view,
            'filters' => $filters,
            'employeeId' => $employeeId,
            'employees' => $employees,
            'employeeOptions' => $employeeOptions,
            'stats' => $stats,
            'activityFeed' => $activityFeed,
        ]);
    }

public function search(Request $request)
{
    $search = trim($request->get('search', ''));

    // Tab kommt aus Blade
    $tab = $request->get('tab', 'my');

    $tabToDataType = [
        'my'        => 'general',
        'created'   => 'by-you',
        'completed' => 'complete',
        'paused'    => 'pause',
        'rejected'  => 'rejected',
        'archived'  => 'archived',
        'deleted'   => 'delete',
    ];

    $dataType = $tabToDataType[$tab] ?? 'general';

    $userName = auth()->user()->name;

    $myAssignedTaskIds = DB::table('employees_personal_tasks')
        ->where('employee_id', $userName)
        ->pluck('task_id')
        ->unique()
        ->toArray();

    $createdTaskIds = DB::table('personal_tasks')
        ->where('assigned_by', $userName)
        ->pluck('id')
        ->unique()
        ->toArray();

    $byYouTaskIds = array_values(array_unique(array_merge($myAssignedTaskIds, $createdTaskIds)));

    $query = PersonalTask::query()
        ->with(['comments', 'attachments']);

    if ($dataType === 'general') {
        $query->whereIn('id', $byYouTaskIds)
              ->whereNotIn('task_status', ['completed', 'pause', 'cancel'])
              ->whereNull('deleted_at');

    } elseif ($dataType === 'by-you') {
        $query->where('assigned_by', $userName)
              ->whereNotIn('task_status', ['completed', 'pause', 'cancel'])
              ->whereNull('deleted_at');

    } elseif ($dataType === 'pause') {
        $query->whereIn('id', $byYouTaskIds)
              ->where('task_status', 'pause')
              ->whereNull('deleted_at');

    } elseif ($dataType === 'cancel') {
        $query->whereIn('id', $byYouTaskIds)
              ->where('task_status', 'cancel')
              ->whereNull('deleted_at');

    } elseif ($dataType === 'complete') {
        $query->whereIn('id', $byYouTaskIds)
              ->where('task_status', 'completed')
              ->whereNull('deleted_at');

    } elseif ($dataType === 'rejected') {
        $query->whereIn('id', function ($sub) use ($userName) {
                $sub->from('employees_personal_tasks')
                    ->select('task_id')
                    ->where('employee_id', $userName)
                    ->where('status', 'reject');
            })
            ->whereNull('deleted_at');

    } elseif ($dataType === 'archived') {
        $query->withTrashed()
              ->whereNotNull('archived_at')
              ->whereNull('deleted_at')
              ->whereIn('id', $byYouTaskIds);

    } elseif ($dataType === 'delete') {      // tab: deleted
        $query->onlyTrashed()
              ->whereIn('id', $byYouTaskIds);
    }

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('task_title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    $tasks = $query
        ->orderByRaw('ISNULL(due_date), due_date ASC')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('admin.personal_tasks.partials.list', [
        'tasks'    => $tasks,
        'dataType' => $dataType,
        'tab'      => $tab,
        'search'   => $search,
    ])->render();
}
 
public function repeat_list()
{
    $data = DB::table('personal_tasks')
        ->leftJoin('employees_personal_tasks', 'employees_personal_tasks.task_id', '=', 'personal_tasks.id')
        ->where('employees_personal_tasks.employee_id', auth()->user()->name)
        ->whereNotNull('personal_tasks.repeat') // Check if repeat is not null
        ->select('personal_tasks.*')
        ->distinct() // Ensure unique records
        ->get();

    return response()->json($data, 200);
}
 
public function details(Request $request, $id)
{
    $user = auth()->user();
    if (!$user) {
        abort(403, 'Unauthenticated.');
    }

    // 1) Load main task + creator
    $personal = DB::table('personal_tasks')
        ->leftJoin('employees', 'employees.id', '=', 'personal_tasks.assigned_by')
        ->select(
            'personal_tasks.*',
            'employees.name as cname',
            'employees.lastname as clastname',
            'employees.image as cimage'
        )
        ->whereNull('personal_tasks.deleted_at')
        ->where('personal_tasks.id', $id)
        ->first();

    if (!$personal) {
        abort(404, 'Task not found.');
    }

    $data = [];
    $data['data'] = $personal;

    // 2) Assigned employees (collections, never null)
    $data['task_employee'] = DB::table('employees_personal_tasks')
        ->join('employees as emp', 'emp.id', '=', 'employees_personal_tasks.employee_id')
        ->select(
            'employees_personal_tasks.*',
            'emp.name',
            'emp.lastname',
            'emp.image',
            'emp.gender'
        )
        ->where('employees_personal_tasks.task_id', $id)
        ->get() ?? collect();

    $data['group_emp'] = DB::table('employees_personal_tasks')
        ->join('employees as emp', 'emp.id', '=', 'employees_personal_tasks.employee_id')
        ->select(
            'employees_personal_tasks.*',
            'emp.name',
            'emp.lastname',
            'emp.image',
            'emp.gender'
        )
        ->where('employees_personal_tasks.task_id', $id)
        ->get() ?? collect();

    $data['data_emp'] = DB::table('employees')
        ->select('id', 'name', 'lastname', 'image')
        ->where('status', '!=', 'Deactive')
        ->get() ?? collect();

    // 3) Controller IDs – make this 100% array or empty
    $controllerIds = [];

    if (!empty($personal->controller_id)) {
        if (is_array($personal->controller_id)) {
            $controllerIds = $personal->controller_id;
        } else {
            $decoded = json_decode($personal->controller_id, true);

            if (is_array($decoded)) {
                $controllerIds = $decoded;
            } elseif (is_string($personal->controller_id)) {
                $parts = array_filter(array_map('trim', explode(',', $personal->controller_id)));
                $controllerIds = array_map('intval', $parts);
            } elseif (is_numeric($personal->controller_id)) {
                $controllerIds = [(int) $personal->controller_id];
            }
        }
    }

    if (!is_array($controllerIds)) {
        $controllerIds = [];
    }

    $data['controllers'] = !empty($controllerIds)
        ? Employee::whereIn('id', $controllerIds)->get()
        : collect();

    // 4) Authorization
    $currentEmployeeKey = $user->name; // ⚠ if you use employee_id, swap this

    $isAssignedByUser = ($personal->assigned_by == $currentEmployeeKey);

    $check = DB::table('employees_personal_tasks')
        ->where('task_id', $id)
        ->where('employee_id', $currentEmployeeKey)
        ->first();

    $isAuthorized = !is_null($check);
    $public_check = ($personal->public === 'on');

    Log::info('Personal task details auth', [
        'task_id'          => $id,
        'current_user'     => $currentEmployeeKey,
        'assigned_by'      => $personal->assigned_by,
        'isAssignedByUser' => $isAssignedByUser,
        'isAuthorized'     => $isAuthorized,
        'public'           => $public_check,
    ]);

    if (!$isAssignedByUser && !$isAuthorized && !$public_check) {
        return view('error.notAuth');
    }

    // 5) Selected employees (IDs only)
    $data['selectedEmployees'] = DB::table('employees_personal_tasks')
        ->where('task_id', $id)
        ->select('employee_id')
        ->get() ?? collect();

    // 6) Key tasks – THIS is what you want to inspect
    $data['key_task'] = DB::table('personal_task_keys')
        ->leftJoin('employees', 'employees.id', '=', 'personal_task_keys.done_by')
        ->select(
            'personal_task_keys.*',
            'employees.name',
            'employees.lastname',
            'employees.gender',
            'employees.image'
        )
        ->where('personal_task_keys.personal_task_id', $id)
        ->get() ?? collect();

    // 🔍 Log detailed info about personal_task_keys
    Log::info('Personal task details :: key_task snapshot', [
        'task_id'      => $id,
        'key_count'    => $data['key_task']->count(),
        'raw_rows'     => $data['key_task']->map(function ($row) {
            return [
                'id'               => $row->id ?? null,
                'personal_task_id' => $row->personal_task_id ?? null,
                'task'             => $row->task ?? null,
                'key_description'  => $row->key_description ?? null,
                'duration'         => $row->duration ?? null,
                'submit_time'      => $row->submit_time ?? null,
                'is_completed'     => $row->is_completed ?? null,
                'done_status'      => $row->done_status ?? null,
                'done_by'          => $row->done_by ?? null,
                'done_date'        => $row->done_date ?? null,
                'employee_id_raw'  => $row->employee_id ?? null, // this is often JSON
                'employee_id_decoded' => (function ($v) {
                    $decoded = json_decode($v, true);
                    return is_array($decoded) ? $decoded : $v;
                })($row->employee_id ?? null),
            ];
        })->toArray(),
    ]);

    // 7) Sub tasks
    $data['sub_task'] = DB::table('personal_sub_tasks')
        ->where('task_id', $id)
        ->get() ?? collect();

    Log::info('Personal task details :: sub_task snapshot', [
        'task_id'   => $id,
        'sub_count' => $data['sub_task']->count(),
        'rows'      => $data['sub_task']->toArray(),
    ]);

    // 8) Comments
    $data['comments_list'] = DB::table('personal_task_comments')
        ->join('employees', 'employees.id', '=', 'personal_task_comments.comment_by')
        ->select(
            'personal_task_comments.*',
            'employees.name',
            'employees.lastname',
            'employees.gender',
            'employees.image'
        )
        ->where('personal_task_comments.task_id', $id)
        ->get() ?? collect();

    $data['comments_count'] = $data['comments_list']->count();

    Log::info('Personal task details :: comments snapshot', [
        'task_id'        => $id,
        'comments_count' => $data['comments_count'],
        'rows'           => $data['comments_list']->toArray(),
    ]);

    // 9) Employee list for modals/select2
    $data['employees'] = DB::table('employees')
        ->select('id', 'name', 'lastname', 'image')
        ->where('status', '!=', 'Deactive')
        ->get() ?? collect();

    Log::info('Personal task details :: final payload overview', [
        'task_id'          => $id,
        'has_task_employee'=> $data['task_employee']->count(),
        'has_group_emp'    => $data['group_emp']->count(),
        'has_employees'    => $data['employees']->count(),
        'has_controllers'  => $data['controllers']->count(),
    ]);

    // 🧪 OPTIONAL: temporarily dump the whole payload as JSON instead of the view
    // return response()->json($data);

    return view('admin.todo.personal.task_details', $data);
}
   
public function availability($emp_id, $start_date, $end_date)
{
    $appointments = DB::table('main_appointments')
        ->join('main_appointment_employees', 'main_appointment_employees.appointment_id', '=', 'main_appointments.id')
        ->leftJoin('employees', 'employees.id', '=', 'main_appointment_employees.employee_id')
        ->select(
            'employees.name',
            'employees.lastname',
            'main_appointments.start_date',
            'main_appointments.end_date',
            DB::raw("'Termin' as type") // Adding type column
        )
        ->where('main_appointment_employees.employee_id', $emp_id)
        ->where(function ($query) use ($start_date, $end_date) {
            $query->whereBetween('main_appointments.start_date', [$start_date, $end_date])
                ->orWhereBetween('main_appointments.end_date', [$start_date, $end_date])
                ->orWhere(function ($q) use ($start_date, $end_date) {
                    $q->where('main_appointments.start_date', '<=', $start_date)
                        ->where('main_appointments.end_date', '>=', $end_date);
                });
        });

    $tasks = DB::table('employees_personal_tasks')
        ->join('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
        ->join('employees', 'employees.id', '=', 'employees_personal_tasks.employee_id')
        ->select(
            'employees.name',
            'employees.lastname',
            'personal_tasks.start_date',
            'personal_tasks.due_date',
            DB::raw("'Aufgabe' as type") // Adding type column
        )
        ->where('employees_personal_tasks.employee_id', $emp_id)
        ->where(function ($query) use ($start_date, $end_date) {
            $query->whereBetween('personal_tasks.start_date', [$start_date, $end_date])
                ->orWhereBetween('personal_tasks.due_date', [$start_date, $end_date])
                ->orWhere(function ($q) use ($start_date, $end_date) {
                    $q->where('personal_tasks.start_date', '<=', $start_date)
                        ->where('personal_tasks.due_date', '>=', $end_date);
                });
        });

    // Combine results using union
    $result = $appointments->union($tasks)->get();

    return response()->json($result, 200);
}


public function store(Request $request)
{
    try {
        Log::info('[TASK::STORE] Received Task Data (raw)', $request->all());

        foreach (['personal_task_id','id','created_at','updated_at'] as $forbid) {
            $request->request->remove($forbid);
        }

        // --- Clean keys: keep employee_id (normalize), task, duration, key_description, link
        $cleanKeys = collect($request->input('key', []))
            ->map(function ($k) {
                unset($k['id'], $k['personal_task_id'], $k['created_at'], $k['updated_at']);

                $k['task']            = isset($k['task']) ? trim((string)$k['task']) : null;
                $k['key_description'] = isset($k['key_description']) ? trim((string)$k['key_description']) : null;
                $k['duration']        = isset($k['duration']) && $k['duration'] !== '' ? (int)$k['duration'] : null;
                if (isset($k['link'])) {
                    $k['link'] = trim((string)$k['link']);
                }

                // normalize per-key employee list
                $k['employee_id'] = collect($k['employee_id'] ?? [])
                    ->filter(fn($id) => $id !== null && $id !== '')
                    ->map(fn($id) => (int)$id)
                    ->unique()
                    ->values()
                    ->all();

                return $k;
            })
            ->filter(fn($k) =>
                filled($k['task'] ?? null) ||
                filled($k['duration'] ?? null) ||
                filled($k['key_description'] ?? null)
            )
            ->unique(fn($k) => json_encode([
                't' => $k['task'] ?? null,
                'd' => $k['duration'] ?? null,
                'k' => $k['key_description'] ?? null,
                'e' => $k['employee_id'] ?? [],
            ]))
            ->values()
            ->all();

        if (empty($cleanKeys)) {
            $request->request->remove('key');
            Log::info('[TASK::STORE] Cleaned Keys => EMPTY (will create a default key)');
        } else {
            $request->merge(['key' => $cleanKeys]);
            Log::info('[TASK::STORE] Cleaned Keys (to be used)', ['keys' => $cleanKeys]);
        }

        // --- Validation
        $hasKeys = !empty($cleanKeys);

        $validator = Validator::make($request->all(), [
            'task_title' => 'required|string|max:255',
            'due_date'   => 'required|date',
            'due_time'   => 'nullable',
            'start_date' => 'nullable|date',
            'total_day'  => 'required|numeric|min:0',
            'total_time' => 'required|numeric|min:0',
            'priority'   => 'nullable|string|in:low,normal,high,urgent',
            'color'      => 'nullable|string|max:20',
            'public'     => 'nullable',
            'repeat'     => 'nullable|string|max:255',
            'reminder_date' => 'nullable|date',
            'reminder_time' => 'nullable',

            // top-level assignees
            'employee'      => 'nullable|array',
            'employee.*'    => 'exists:employees,id',

            // controllers: stored on task only
            'controller'    => 'nullable|array',
            'controller.*'  => 'exists:employees,id',

            // keys & per-key assignees
            'key'                    => 'nullable|array',
            'key.*.duration'         => 'nullable|integer|min:0',
            'key.*.employee_id'      => 'nullable|array',
            'key.*.employee_id.*'    => 'exists:employees,id',
        ]);

        $validator->sometimes('key.*.task', 'required|string|max:255', fn() => $hasKeys);

        if ($validator->fails()) {
            Log::warning('[TASK::STORE] Validation failed', ['errors' => $validator->errors()->toArray()]);
            throw new \Exception(json_encode($validator->errors()), 422);
        }

        // --- Resolve employees:

        // 1) Preferred: top-level employee[]
        $topLevelEmployees = collect($request->input('employee', []))
            ->filter(fn($id) => $id !== null && $id !== '')
            ->map(fn($id) => (int)$id)
            ->unique()
            ->values()
            ->all();

        // 2) Fallback: union of per-key employee_id if top-level is empty
        $unionKeyEmployees = empty($topLevelEmployees)
            ? collect($cleanKeys)->pluck('employee_id')->flatten()
                ->filter(fn($id) => $id !== null && $id !== '')
                ->map(fn($id) => (int)$id)
                ->unique()
                ->values()
                ->all()
            : [];

        // strictEmployeeIds = what we write to pivots (either top-level or union from keys)
        $strictEmployeeIds = !empty($topLevelEmployees) ? $topLevelEmployees : $unionKeyEmployees;

        // controllers stored on task only
        $controllerIds = collect($request->input('controller', []))
            ->filter(fn($id) => $id !== null && $id !== '')
            ->map(fn($id) => (int)$id)
            ->unique()
            ->values()
            ->all();

        Log::info('[TASK::STORE] Top-level employees', ['employees' => $topLevelEmployees]);
        Log::info('[TASK::STORE] Union key employees (fallback)', ['employees' => $unionKeyEmployees]);
        Log::info('[TASK::STORE] Strict Employees USED (pivots/keys fallback)', ['employees' => $strictEmployeeIds]);
        Log::info('[TASK::STORE] Controllers (stored on task only)', ['controllers' => $controllerIds]);

        // --- Transaction
        $taskId = DB::transaction(function () use ($request, $strictEmployeeIds, $controllerIds, $cleanKeys, $topLevelEmployees) {

            // Create task
            $task = new PersonalTask();
            $task->task_title     = $request->task_title;
            $task->description    = $request->description;
            $task->due_date       = $request->due_date;
            $task->due_time       = $request->due_time;
            $task->start_date     = $request->start_date;
            $task->total_day      = $request->total_day;
            $task->total_time     = $request->total_time;
            $task->priority       = $request->priority ?? 'normal';
            $task->color          = $request->color;
            $task->public         = $request->boolean('public') ? 1 : 0;
            $task->repeat         = $request->repeat;
            $task->reminder_date  = $request->reminder_date;
            $task->reminder_time  = $request->reminder_time;
            $task->assigned_by    = auth()->user()->name; // your existing logic
            $task->task_status    = 'open';
            $task->progress       = 0;
            $task->is_notified    = 0;
            $task->is_customer    = $request->has('is_customer') ? (int)$request->is_customer : 0;
            $task->customer_id    = $request->customer_id ?? null;
            $task->alternative_id = $request->alternative_id ?? null;
            $task->product_id     = $request->product_id ?? null;
            $task->controller_id  = json_encode(!empty($controllerIds) ? $controllerIds : $strictEmployeeIds);
            $task->save();

            Log::info('[TASK::STORE] Saved Task (Eloquent->toArray)', $task->toArray());

            // Purge legacy children (defensive)
            $legacyKeys   = DB::table('personal_task_keys')->where('personal_task_id', $task->id)->count();
            $legacyPivots = DB::table('employees_personal_tasks')->where('task_id', $task->id)->count();

            if ($legacyKeys || $legacyPivots) {
                Log::warning('[TASK::STORE] Detected LEGACY child rows referencing task id '.$task->id, [
                    'legacy_keys' => $legacyKeys,
                    'legacy_pivots' => $legacyPivots,
                ]);
                DB::table('personal_task_keys')->where('personal_task_id', $task->id)->delete();
                DB::table('employees_personal_tasks')->where('task_id', $task->id)->delete();
                Log::warning('[TASK::STORE] Purged legacy child rows for task id '.$task->id);
            }

            // Save pivot rows from strictEmployeeIds (top-level or union of keys)
            $pivotRows = [];
            foreach ($strictEmployeeIds as $eid) {
                $pivot = EmployeesPersonalTask::create([
                    'task_id'     => $task->id,
                    'employee_id' => $eid,
                    'status'      => 'accepted',
                ]);
                $pivotRows[] = $pivot->toArray();
            }
            Log::info('[TASK::STORE] Saved Pivot Rows', $pivotRows);

            // Build keys payload:
            // IMPORTANT CHANGE:
            // - Keys always keep their own per-key employee_id list (from UI).
            // - If a key has no employees, optional fallback to strictEmployeeIds.
            $now          = now();
            $rowsToInsert = [];

            if (!empty($cleanKeys)) {
                foreach ($cleanKeys as $k) {
                    // keep per-step employees exactly as cleaned
                    $perKeyEmployees = $k['employee_id'] ?? [];

                    // if you want no fallback, keep only $perKeyEmployees and allow null:
                    $finalEmployees = !empty($perKeyEmployees)
                        ? $perKeyEmployees
                        : $strictEmployeeIds; // or [] if you want truly empty

                    $rowsToInsert[] = [
                        'personal_task_id' => $task->id,
                        'task'             => $k['task'],
                        'duration'         => $k['duration'],
                        'link'             => $k['link'] ?? null,
                        'key_description'  => $k['key_description'] ?? null,
                        'status'           => 'accept',
                        'is_completed'     => 0,
                        'work_progress'    => 0,
                        'submit_time'      => null,
                        'total_time'       => $k['duration'],
                        'reason'           => null,
                        'employee_id'      => !empty($finalEmployees)
                            ? json_encode(array_values($finalEmployees))
                            : null,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ];
                }
            } else {
                // no keys in request -> create 1 default key with all strict employees
                $rowsToInsert[] = [
                    'personal_task_id' => $task->id,
                    'task'             => $task->task_title,
                    'duration'         => (int)$task->total_time,
                    'link'             => null,
                    'key_description'  => $task->description,
                    'status'           => 'accept',
                    'is_completed'     => 0,
                    'work_progress'    => 0,
                    'submit_time'      => null,
                    'total_time'       => (int)$task->total_time,
                    'reason'           => null,
                    'employee_id'      => !empty($strictEmployeeIds)
                        ? json_encode(array_values($strictEmployeeIds))
                        : null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            Log::info('[TASK::STORE] Keys payload to INSERT (query builder, bypass observers)', ['rows' => $rowsToInsert]);

            if (!empty($rowsToInsert)) {
                DB::table('personal_task_keys')->insert($rowsToInsert);
            }

            // Read-back
            $savedTask   = PersonalTask::query()->find($task->id)?->toArray();
            $savedKeys   = DB::table('personal_task_keys')
                ->where('personal_task_id', $task->id)
                ->get()
                ->map(fn($r) => (array)$r)
                ->all();
            $savedPivots = DB::table('employees_personal_tasks')
                ->where('task_id', $task->id)
                ->get()
                ->map(fn($r) => (array)$r)
                ->all();

            Log::info('[TASK::STORE] READ-BACK Task', $savedTask ?? []);
            Log::info('[TASK::STORE] READ-BACK Keys', ['rows' => $savedKeys]);
            Log::info('[TASK::STORE] READ-BACK Pivots', ['rows' => $savedPivots]);

            return $task->id;
        });

        return response()->json([
            'message' => 'Task successfully created!',
            'task_id' => $taskId,
        ], 200);

    } catch (\Exception $e) {
        Log::error('[TASK::STORE] Error saving task', ['error' => $e->getMessage()]);
        $errorMessage = json_decode($e->getMessage(), true);

        return response()->json([
            'message' => 'Validation failed or an error occurred.',
            'errors'  => $errorMessage ?? ['error' => $e->getMessage()],
        ], 422);
    }
}

public function updateEmployee(Request $request)
{

    Log::info('Received Task Data:', $request->all());

    try {
        // ✅ Validate incoming data
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->start_time && $value < $request->start_time) {
                        $fail('The end time must be after the start time.');
                    }
                }
            ],
            'total_time' => 'nullable|numeric',
            'appointment_time' => 'nullable|numeric',
            'employee' => 'required|array|min:1',
            'employee.*' => 'exists:employees,id',
            'same_id' => 'required',
            'task_id' => 'required|exists:personal_tasks,id',
            'note' => 'nullable',
            'key' => 'nullable|array',
            'key.*.employee_id' => 'nullable|array',
            'key.*.employee_id.*' => 'exists:employees,id',
        ]);

        \Log::info('Updating employee assignment:', $validated);

        $task = PersonalTask::findOrFail($validated['task_id']);

        // ✅ Delete all existing assignments with same same_id
        EmployeesPersonalTask::where('same_id', $validated['same_id'])->delete();

        // ✅ Combine employee[] and all key[x][employee_id][] values into one unique set
        $allEmployeeIds = collect($validated['employee']);

        if (!empty($request->key)) {
            foreach ($request->key as $key) {
                if (!empty($key['employee_id']) && is_array($key['employee_id'])) {
                    $allEmployeeIds = $allEmployeeIds->merge($key['employee_id']);
                }
            }
        }

        $allEmployeeIds = $allEmployeeIds->unique()->values();

        // ✅ Insert employee tasks
        foreach ($allEmployeeIds as $employeeId) {
            EmployeesPersonalTask::create([
                'employee_id' => $employeeId,
                'task_id' => $task->id,
                'status' => 'send',
                'reason' => $request->reason ?? null,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'total_time' => $validated['total_time'] ?? null,
                'appointment_time' => $validated['appointment_time'] ?? null,
                'same_id' => $validated['same_id'],
                'note' => $validated['note'] ?? null,
            ]);
        }

        // ✅ Update task appointment/time stats
        if ($validated['appointment_time']) {
            $task->all_appointment = ($task->all_appointment ?? 0) + $validated['appointment_time'];
        }

        if ($validated['total_time']) {
            $task->all_time = ($task->all_time ?? 0) + $validated['total_time'];
        }

        $task->save();

        return response()->json(['message' => 'Aufgaben erfolgreich aktualisiert!'], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'message' => 'Validierungsfehler',
            'errors' => $e->errors()
        ], 422);
    } catch (\Throwable $e) {
        \Log::error('Fehler beim Aktualisieren der Aufgabe:', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Fehler beim Aktualisieren der Aufgabe.', 'error' => $e->getMessage()], 500);
    }
}
 
public function edit($id)
{
    $data['data'] = PersonalTask::findOrFail($id);
    $data['employees'] = DB::table('employees')
    ->where('status', '!=', 'Deactive') 
    ->select('id', 'name', 'lastname', 'image')
    ->get();

        $data['task_employee'] = DB::table('employees_personal_tasks')
        ->join('employees as emp', 'emp.id', '=', 'employees_personal_tasks.employee_id')
        ->join('personal_tasks as task', 'task.id', '=', 'employees_personal_tasks.task_id') 
        ->select(
            'employees_personal_tasks.*', 
            'emp.name', 
            'emp.lastname', 
            'emp.image', 
            'emp.gender',
            'task.task_title'
        )
        ->where('employees_personal_tasks.task_id', $id)
        ->get();

    $data['key_task'] = DB::table('personal_task_keys')
                            ->where('personal_task_id', $id)
                            ->get();

    $data['sub_task'] = DB::table('personal_sub_tasks')
        ->where('task_id', $id)
        ->get();

    return view('admin.todo.personal.personal_edit', $data);
        
}

public function getEmployeeTask($id)
{
    // Fetch all task data grouped by `same_id`
    $data = DB::table('employees_personal_tasks')
        ->select(
            'same_id',
            'task_id',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
            'total_time',
            'reason',
            'note'
        )
        ->where('task_id', $id)
        ->groupBy('same_id', 'task_id', 'start_date', 'end_date', 'start_time', 'end_time', 'total_time', 'reason', 'note')
        ->get();

    // Fetch all employee details grouped by `same_id` and `task_id`
    $employee = DB::table('employees_personal_tasks')
        ->select(
            'task_id',
            'same_id',
            'employee_id',
            'status'
        )
        ->where('task_id', $id)
            ->where('status', 'accepted')
        ->get()
        ->groupBy('same_id') // Group employees by `same_id`
        ->map(function ($group) {
            return $group->map(function ($employee) {
                return [
                    'employee_id' => $employee->employee_id,
                    'status'      => $employee->status,
                ];
            });
        });

    // Return both datasets
    return response()->json([
        'data' => $data,
        'employees' => $employee
    ], 200);
}
 
public function taskDuration(Request $request)
{
    $validated = $request->validate([
        'id'       => 'required|exists:personal_task_keys,id',
        'task_id'  => 'required|exists:personal_tasks,id',
        'duration' => 'required'
    ]);

    $taskKey = PersonalTaskKey::find($validated['id']);

    $emp = DB::table('employees')
        ->select('name', 'lastname', 'id')
        ->where('id', auth()->user()->name)
        ->first();

    $name        = $emp->name.' '.$emp->lastname;
    $taskDetails = DB::table('personal_task_keys')->where('id', $request->id)->first();

    // 🔹 NEW: label with task id + title
    $taskLabel = $this->formatTaskLabel($request->task_id);

    Notification::send(auth()->user(), new PersonalTaskNotification([
        'title'   => 'Aufgabenplanzeit geändert',
        'message' =>
            'Die Planzeit für '.$taskLabel.
            ' (Schritt ['.$taskDetails->task.']) wurde von '.$name.
            ' auf '.$request->duration.' geändert.',
        'task_id' => $request->task_id,
    ]));

    if (!$taskKey) {
        return response()->json([
            'success' => false,
            'message' => 'Task Key not found!'
        ], 404);
    }

    $taskKey->update([
        'duration' => $validated['duration']
    ]);

    return response()->json([
        'success'          => true,
        'message'          => 'Duration updated successfully!',
        'updated_duration' => $taskKey->duration
    ], 200);
}
 
public function destroy($id)
{
    $data = PersonalTask::findOrFail($id);
    $data->delete();
    return redirect()->to('personal/task/'.auth()->user()->name)->with('save_msg', 'Die Aufgabe wurde in den Papierkorb verschoben.');
}
 
public function restore($id)
{
    $data = PersonalTask::withTrashed()->find($id);
    $data->task_status = 'start';
    $data->save();
    if ($data) {
        $data->restore(); // Restores the soft-deleted record
        return redirect()->back()->with('save_msg', 'Aufgabe erfolgreich wiederhergestellt');
    }

    return redirect()->back()->with('error', 'Aufgabe nicht gefunden');
}

public function calendar_destroy($id)
{
$data = PersonalTask::findOrFail($id); 
$data->delete();
// Update status instead of deleting
$data->update([
    'status' => 'GELÖSCHT'
]);

// Return a success response (no redirect)
return response()->json(['status' => 'success', 'message' => 'Deleted successfully', 'event_id' => $id]);
}
 
// Delete Task Key
public function delete_task($id)
{
    \Log::info("Attempting to delete Task Key with ID: $id");

    $data = DB::table('personal_task_keys')
                    ->where('id', $id)
                    ->delete(); 
    return response()->json(['success' => true, 'message' => 'Task deleted successfully.']);


}

    // Delete Sub Task
public function delete_sub_task($id)
{ 
$data = DB::table('personal_sub_tasks')
                    ->where('id', $id)
                    ->delete();  
        return response()->json(['success' => true, 'message' => 'Sub-task deleted successfully.']);

}

public function update(Request $request)
{
    Log::info('🔁 Updating Task (RAW):', $request->all());

    /**
     * 1) Normalize and clean keys
     */
    $rawKeys        = $request->input('key', []);
    $normalizedKeys = [];

    foreach ($rawKeys as $keyId => $keyData) {
        $taskText       = isset($keyData['task']) ? trim((string) $keyData['task']) : null;
        $keyDescription = isset($keyData['key_description']) ? trim((string) $keyData['key_description']) : null;
        $duration       = isset($keyData['duration']) && $keyData['duration'] !== ''
            ? (int) $keyData['duration']
            : null;

        // Normalize employee_id array for this key
        $employeeIds = collect($keyData['employee_id'] ?? [])
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $link = isset($keyData['link']) ? trim((string) $keyData['link']) : null;

        // Skip completely empty rows
        if (!filled($taskText) && !filled($duration) && !filled($keyDescription)) {
            continue;
        }

        $normalizedKeys[] = [
            'id'              => is_numeric($keyId) ? (int) $keyId : null,
            'task'            => $taskText,
            'duration'        => $duration,
            'key_description' => $keyDescription,
            'link'            => $link,
            'status'          => $keyData['status']        ?? 'pending',
            'is_completed'    => $keyData['is_completed']  ?? 0,
            'work_progress'   => $keyData['work_progress'] ?? 0,
            'submit_time'     => $keyData['submit_time']   ?? null,
            'reason'          => $keyData['reason']        ?? null,
            'employee_id'     => $employeeIds,
        ];
    }

    // override incoming key[] with normalized structure
    $request->merge(['key' => $normalizedKeys]);

    /**
     * 2) Validation
     */
    $validator = Validator::make($request->all(), [
        'id'         => 'required|exists:personal_tasks,id',
        'task_title' => 'required|string|max:255',
        'due_date'   => 'required|date',
        'due_time'   => 'nullable',
        'total_day'  => 'nullable|numeric|min:0',
        'total_time' => 'nullable|numeric|min:0',

        // top-level employees
        'employee'   => 'nullable|array',
        'employee.*' => 'exists:employees,id',

        // controllers
        'controller'   => 'nullable|array',
        'controller.*' => 'exists:employees,id',

        // keys
        'key'                 => 'nullable|array',
        'key.*.task'          => 'required|string|max:255',
        'key.*.duration'      => 'nullable|numeric|min:0',
        'key.*.employee_id'   => 'nullable|array',
        'key.*.employee_id.*' => 'exists:employees,id',
    ]);

    if ($validator->fails()) {
        return redirect()
            ->back()
            ->withErrors($validator)
            ->withInput()
            ->with('validation_failed', true);
    }

    try {
        DB::beginTransaction();

        /**
         * 3) Load task
         */
        $task = PersonalTask::findOrFail($request->id);

        /**
         * 4) Resolve employees
         *
         * If there are any employees on keys:
         *    -> task team = union of all key employees
         * otherwise:
         *    -> task team = top-level employees (employee[])
         */

        // Top-level employees from "employee[]"
        $topLevelEmployees = collect($request->input('employee', []))
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        // Union of employees from all keys
        $unionKeyEmployees = collect($normalizedKeys)
            ->pluck('employee_id')
            ->flatten()
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (!empty($unionKeyEmployees)) {
            // Keys define the team
            $strictEmployeeIds = $unionKeyEmployees;
        } else {
            // No employees on keys → use top-level employees
            $strictEmployeeIds = $topLevelEmployees;
        }

        // Controllers
        $controllerIds = collect($request->input('controller', []))
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        Log::info('[TASK::UPDATE] Top-level employees', ['employees' => $topLevelEmployees]);
        Log::info('[TASK::UPDATE] Union key employees', ['employees' => $unionKeyEmployees]);
        Log::info('[TASK::UPDATE] Strict employees used (pivot / key fallback)', ['employees' => $strictEmployeeIds]);
        Log::info('[TASK::UPDATE] Controllers', ['controllers' => $controllerIds]);

        /**
         * 5) Update main task
         */
        $task->fill([
            'task_title'     => $request->task_title,
            'description'    => $request->description,
            'due_date'       => $request->due_date,
            'due_time'       => $request->due_time,
            'start_date'     => $request->start_date,
            'total_day'      => $request->total_day ?? 1,
            'total_time'     => $request->total_time ?? 1,
            'priority'       => $request->priority ?? 'normal',
            'color'          => $request->color,
            'public'         => $request->has('public') ? 1 : 0,
            'repeat'         => $request->repeat,
            'reminder_date'  => $request->reminder_date,
            'reminder_time'  => $request->reminder_time,
            'customer_id'    => $request->customer_id ?? null,
            'is_customer'    => $request->has('is_customer') ? (int) $request->is_customer : 0,
            'alternative_id' => $request->alternative_id ?? null,
            'product_id'     => $request->product_id ?? null,
            // controllers or fallback to team
            'controller_id'  => json_encode(!empty($controllerIds) ? $controllerIds : $strictEmployeeIds),
        ])->save();

        /**
         * 6) Sync task employees pivot (EmployeesPersonalTask)
         *    -> employees = strictEmployeeIds
         */
        EmployeesPersonalTask::where('task_id', $task->id)->delete();

        foreach ($strictEmployeeIds as $empId) {
            EmployeesPersonalTask::create([
                'task_id'     => $task->id,
                'employee_id' => $empId,
                'status'      => 'accepted',
            ]);
        }

        /**
         * 7) Update / create keys
         *    - If a key has its own employees → use them
         *    - Otherwise → fall back to strictEmployeeIds (team)
         */
        $handledKeyIds = [];

        foreach ($normalizedKeys as $keyData) {
            $perKeyEmployees = $keyData['employee_id'] ?? [];

            $finalEmployees = !empty($perKeyEmployees)
                ? $perKeyEmployees
                : $strictEmployeeIds;

            $data = [
                'task'            => $keyData['task'],
                'duration'        => $keyData['duration'],
                'link'            => $keyData['link'] ?? null,
                'key_description' => $keyData['key_description'] ?? null,
                'status'          => $keyData['status'] ?? 'pending',
                'is_completed'    => $keyData['is_completed'] ?? 0,
                'work_progress'   => $keyData['work_progress'] ?? 0,
                'submit_time'     => $keyData['submit_time'] ?? null,
                'reason'          => $keyData['reason'] ?? null,
                'total_time'      => $keyData['duration'],
                'employee_id'     => !empty($finalEmployees)
                    ? json_encode(array_values($finalEmployees))
                    : null,
            ];

            if (!empty($keyData['id'])) {
                // Update existing key
                $key = PersonalTaskKey::where('id', $keyData['id'])
                    ->where('personal_task_id', $task->id)
                    ->first();

                if ($key) {
                    $key->update($data);
                    $handledKeyIds[] = $key->id;
                    continue;
                }
            }

            // Create new key
            $newKey = PersonalTaskKey::create(array_merge($data, [
                'personal_task_id' => $task->id,
            ]));
            $handledKeyIds[] = $newKey->id;
        }

        // Optional: delete keys that were removed in the form
        // PersonalTaskKey::where('personal_task_id', $task->id)
        //     ->whereNotIn('id', $handledKeyIds)
        //     ->delete();

        /**
         * 8) Notification
         */
        $emp = DB::table('employees')
            ->where('id', auth()->user()->name)
            ->first();

        $name = $emp ? $emp->name . ' ' . $emp->lastname : 'Unbekannt';

        $taskLabel = $this->formatTaskLabel($task->id);

        Notification::send(auth()->user(), new PersonalTaskNotification([
            'title'   => 'Aufgabe bearbeitet',
            'message' => 'Die Aufgabe ' . $taskLabel . ' wurde von ' . $name . ' bearbeitet.',
            'task_id' => $task->id,
        ]));

        DB::commit();

        return redirect('personal-tasks/' . $task->id . '/profile')
            ->with('success', 'Aufgabe erfolgreich aktualisiert!');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ Task Update Error:', ['error' => $e->getMessage()]);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Fehler beim Aktualisieren: ' . $e->getMessage());
    }
}

 
public function contactList(Request $request)
{
    $search = trim((string) $request->input('search'));

    $query = DB::table('new_leads')
        ->whereNull('new_leads.deleted_at')
        ->where(function ($q) {
            $q->whereNull('new_leads.status')
                ->orWhere('new_leads.status', '!=', 'Junk');
        })
        ->select([
            'new_leads.id',
            'new_leads.customer_no',
            'new_leads.firma',
            'new_leads.name',
            'new_leads.lastname',
            'new_leads.phone',
            'new_leads.telephone',
            'new_leads.email',
            'new_leads.street',
            'new_leads.postcode',
            'new_leads.city',
            'new_leads.latitude',
            'new_leads.longitude',
            'new_leads.full_address',
        ]);

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('new_leads.name', 'like', "%{$search}%")
                ->orWhere('new_leads.lastname', 'like', "%{$search}%")
                ->orWhere('new_leads.firma', 'like', "%{$search}%")
                ->orWhere('new_leads.customer_no', 'like', "%{$search}%")
                ->orWhereRaw("CONCAT_WS(' ', new_leads.name, new_leads.lastname) LIKE ?", ["%{$search}%"])
                ->orWhere('new_leads.phone', 'like', "%{$search}%")
                ->orWhere('new_leads.telephone', 'like', "%{$search}%")
                ->orWhere('new_leads.email', 'like', "%{$search}%")
                ->orWhere('new_leads.street', 'like', "%{$search}%")
                ->orWhere('new_leads.postcode', 'like', "%{$search}%")
                ->orWhere('new_leads.city', 'like', "%{$search}%");
        });
    }

    $customers = $query
        ->orderBy('new_leads.name')
        ->orderBy('new_leads.lastname')
        ->limit(50)
        ->get()
        ->map(function ($customer) {
            $displayName = trim((string) ($customer->firma ?: trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? ''))));

            if ($displayName === '') {
                $displayName = $customer->customer_no ? ('#' . $customer->customer_no) : ('#' . $customer->id);
            }

            $address = $this->calendarAddressPayload(
                $customer->street,
                $customer->postcode,
                $customer->city,
                $customer->latitude,
                $customer->longitude,
                $customer->full_address
            );

            return [
                'main_id'        => (int) $customer->id,
                'sub_id'         => null,
                'alternative_id' => null,

                'id'             => (int) $customer->id,
                'name'           => $displayName,
                'text'           => $displayName,
                'lastname'       => '',
                'type'           => 'Kunde',

                'phone'          => $customer->phone ?: $customer->telephone,
                'email'          => $customer->email,

                'street'         => $address['street'],
                'postcode'       => $address['postcode'],
                'city'           => $address['city'],
                'latitude'       => $address['latitude'],
                'longitude'      => $address['longitude'],
                'full_address'   => $address['full_address'],

                'customer_no'    => $customer->customer_no,
                'firma'          => $customer->firma,
                'object_name'    => null,
            ];
        })
        ->values();

    return response()->json($customers);
}

public function getProductsByCustomer(Request $request)
{
    try {
        $customerId = (int) $request->get('customer_id');

        if (!$customerId) {
            return response()->json([]);
        }

        $customer = DB::table('new_leads')
            ->where('id', $customerId)
            ->whereNull('deleted_at')
            ->first();

        if (!$customer) {
            return response()->json([]);
        }

        $customerAddress = $this->calendarAddressPayload(
            $customer->street ?? null,
            $customer->postcode ?? null,
            $customer->city ?? null,
            $customer->latitude ?? null,
            $customer->longitude ?? null,
            $customer->full_address ?? null
        );

        $rows = DB::table('lead_product_lists as lpl')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
            ->leftJoin('lead_alternative_adds as obj', function ($join) {
                $join->on('obj.id', '=', 'lpl.alternative_id')
                    ->whereNull('obj.deleted_at');
            })
            ->where('lpl.customer_id', $customerId)
            ->whereNull('lpl.deleted_at')
            ->select([
                'lpl.id as lead_product_list_id',
                'lpl.customer_id',
                'lpl.alternative_id',
                'lpl.product_id',
                'lpl.service',
                'lpl.status',
                'lpl.work_status',

                'ag.article_group as product_name',

                'obj.id as object_id',
                'obj.object_name',
                'obj.street as object_street',
                'obj.postcode as object_postcode',
                'obj.city as object_city',
                'obj.lat as object_latitude',
                'obj.lon as object_longitude',
                'obj.full_address as object_full_address',
                'obj.main as object_main',
            ])
            ->orderBy('obj.object_name')
            ->orderBy('ag.article_group')
            ->get();

        $objects = DB::table('lead_alternative_adds')
            ->where('lead_id', $customerId)
            ->whereNull('deleted_at')
            ->select([
                'id',
                'object_name',
                'street',
                'postcode',
                'city',
                'lat',
                'lon',
                'full_address',
                'main',
            ])
            ->orderByDesc('main')
            ->orderBy('object_name')
            ->get();

        $groups = [];

        $productsWithoutObject = $rows
            ->filter(fn ($row) => empty($row->alternative_id))
            ->values();

        $groups[] = [
            'text'           => 'Kundenadresse',
            'type'           => 'customer',
            'alternative_id' => null,
            'address'        => array_merge($customerAddress, [
                'source_type'    => 'customer',
                'source_id'      => (int) $customerId,
                'alternative_id' => null,
            ]),
            'children'       => $productsWithoutObject->map(function ($row) use ($customerAddress) {
                $productName = $row->product_name ?: 'Ohne Artikelgruppe';

                return [
                    'uid'                  => 'lpl_' . $row->lead_product_list_id,
                    'id'                   => 'lpl_' . $row->lead_product_list_id,
                    'lead_product_list_id' => (int) $row->lead_product_list_id,
                    'text'                 => $productName,
                    'product_name'         => $productName,
                    'product_id'           => $row->product_id ? (int) $row->product_id : null,
                    'alternative_id'       => null,
                    'customer_id'          => (int) $row->customer_id,
                    'city'                 => $customerAddress['city'],
                    'address'              => array_merge($customerAddress, [
                        'source_type'    => 'lead_product_list',
                        'source_id'      => (int) $row->lead_product_list_id,
                        'alternative_id' => null,
                    ]),
                ];
            })->values()->all(),
        ];

        foreach ($objects as $object) {
            $objectAddress = $this->calendarAddressPayload(
                $object->street ?? null,
                $object->postcode ?? null,
                $object->city ?? null,
                $object->lat ?? null,
                $object->lon ?? null,
                $object->full_address ?? null
            );

            $objectProducts = $rows
                ->filter(fn ($row) => (int) $row->alternative_id === (int) $object->id)
                ->values();

            $groups[] = [
                'text'           => $object->object_name ?: ('Objekt ' . $object->id),
                'type'           => 'object',
                'alternative_id' => (int) $object->id,
                'address'        => array_merge($objectAddress, [
                    'source_type'    => 'object',
                    'source_id'      => (int) $object->id,
                    'alternative_id' => (int) $object->id,
                ]),
                'children'       => $objectProducts->map(function ($row) use ($object, $objectAddress) {
                    $productName = $row->product_name ?: 'Ohne Artikelgruppe';

                    return [
                        'uid'                  => 'lpl_' . $row->lead_product_list_id,
                        'id'                   => 'lpl_' . $row->lead_product_list_id,
                        'lead_product_list_id' => (int) $row->lead_product_list_id,
                        'text'                 => $productName,
                        'product_name'         => $productName,
                        'product_id'           => $row->product_id ? (int) $row->product_id : null,
                        'alternative_id'       => (int) $object->id,
                        'customer_id'          => (int) $row->customer_id,
                        'city'                 => $objectAddress['city'],
                        'object_name'          => $object->object_name,
                        'address'              => array_merge($objectAddress, [
                            'source_type'    => 'lead_product_list',
                            'source_id'      => (int) $row->lead_product_list_id,
                            'alternative_id' => (int) $object->id,
                        ]),
                    ];
                })->values()->all(),
            ];
        }

        $groups = collect($groups)
            ->filter(function ($group) {
                $address = $group['address'] ?? [];

                return !empty($group['children'])
                    || !empty($address['street'])
                    || !empty($address['postcode'])
                    || !empty($address['city'])
                    || !empty($address['full_address']);
            })
            ->values();

        return response()->json($groups);
    } catch (Throwable $e) {
        Log::error('getProductsByCustomer failed', [
            'customer_id' => $request->get('customer_id'),
            'message'     => $e->getMessage(),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
        ]);

        return response()->json([
            'message' => 'Server error while loading customer products.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

private function calendarAddressPayload($street, $postcode, $city, $latitude = null, $longitude = null, $fullAddress = null): array
{
    $street = trim((string) $street);
    $postcode = trim((string) $postcode);
    $city = trim((string) $city);

    $cleanFull = $this->cleanVisibleCalendarAddress($fullAddress);

    if ($cleanFull !== '') {
        $parts = collect(preg_split('/\s*[|,]\s*/u', $cleanFull))
            ->map(fn ($part) => trim((string) $part))
            ->filter()
            ->values();

        foreach ($parts as $part) {
            if ($street === '' && !preg_match('/^\d{4,5}\s+/u', $part)) {
                $street = $part;
                continue;
            }

            if (($postcode === '' || $city === '') && preg_match('/^(\d{4,5})\s+(.+)$/u', $part, $m)) {
                $postcode = $postcode ?: $m[1];
                $city = $city ?: trim($m[2]);
            }
        }
    }

    $postcodeCity = trim(trim($postcode) . ' ' . trim($city));

    $visibleAddress = trim(implode(', ', array_filter([
        $street,
        $postcodeCity,
    ])));

    if ($visibleAddress === '') {
        $visibleAddress = $cleanFull;
    }

    return [
        'street'       => $street,
        'postcode'     => $postcode,
        'city'         => $city,
        'latitude'     => $latitude,
        'longitude'    => $longitude,
        'full_address' => $visibleAddress,
    ];
}

private function cleanVisibleCalendarAddress($value): string
{
    $address = trim((string) $value);

    if ($address === '') {
        return '';
    }

    $address = preg_replace('/\s*\|\s*Lat\s*:.*$/iu', '', $address);
    $address = preg_replace('/\s*\|\s*Latitude\s*:.*$/iu', '', $address);
    $address = preg_replace('/\s*Lat\s*:\s*[-0-9.,]+\s*\/\s*Lng\s*:\s*[-0-9.,]+/iu', '', $address);
    $address = preg_replace('/\s*Latitude\s*:\s*[-0-9.,]+\s*\/\s*Longitude\s*:\s*[-0-9.,]+/iu', '', $address);

    return trim($address, " \t\n\r\0\x0B|,");
}


public function repairMissingPhaseSections()
{
    $defaults = [
        'complete',
        'montage',
        'product',
        'plan',
        'maintenance',
        'repair',
        'reclaim',
        'others',
    ];

    $products = ArticleGroup::all();

    foreach ($products as $product) {
        $existing = PhaseSection::query()
            ->where('product_id', $product->id)
            ->pluck('phase_section')
            ->map(fn ($v) => strtolower(trim($v)))
            ->toArray();

        foreach ($defaults as $serviceName) {
            if (!in_array(strtolower($serviceName), $existing, true)) {
                PhaseSection::create([
                    'product_id'    => $product->id,
                    'phase_section' => $serviceName,
                    'status'        => 'Published',
                ]);
            }
        }
    }

    return 'done';
}

    public function myCalender()
    {
        $authUser = Auth::user();
        $userId = $authUser->id;
        $userName = $authUser->name; // users.name = employees.id in your app

        $today = now()->toDateString();

        $data = [];

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */
        $data['hasAdminAccess'] = DB::table('user_rolls')
            ->where('user_id', $userId)
            ->where('item_id', 'Administrator')
            ->where('is_read', 'on')
            ->where('is_update', 'on')
            ->where('is_delete', 'on')
            ->where('is_add', 'on')
            ->exists();

        /*
        |--------------------------------------------------------------------------
        | Personal calendar settings
        |--------------------------------------------------------------------------
        */
        $settings = DB::table('personal_settings')
            ->where('employee_id', $userName)
            ->value('calendar_settings');

        $favoriteEmployeeIds = [];

        if ($settings) {
            $parsed = json_decode($settings, true);
            $favoriteEmployeeIds = is_array($parsed)
                ? ($parsed['favorite_employees'] ?? [])
                : [];
        }

        $data['favorite_employee_ids'] = $favoriteEmployeeIds;

        /*
        |--------------------------------------------------------------------------
        | Customers / branches / departments
        |--------------------------------------------------------------------------
        */
        $data['customers'] = NewLeads::query()
            ->whereNull('deleted_at')
            ->where('status', '!=', 'Junk')
            ->orderBy('name', 'asc')
            ->get([
                'id',
                'name',
                'lastname',
                'street',
                'postcode',
                'city',
                'latitude',
                'longitude',
                'phone',
            ]);

        $data['branches'] = Branch::query()
            ->where('status', 'Published')
            ->orderBy('branch', 'asc')
            ->get(['id', 'branch']);

        $data['departments'] = Department::query()
            ->where('status', 'published')
            ->orderBy('department_name', 'asc')
            ->get(['id', 'department_name']);

        /*
        |--------------------------------------------------------------------------
        | Employees with current Urlaub / Krank status
        |--------------------------------------------------------------------------
        | Used by:
        | - create appointment modal Select2
        | - edit appointment modal Select2
        | - allEmployees
        | - employeesForWizard
        |--------------------------------------------------------------------------
        */
        $data['employees'] = Employee::query()
            ->whereNull('employees.deleted_at')
            ->where('employees.status', 'Active')
            ->leftJoin('leaves as current_leave', function ($join) use ($today) {
                $join->on('current_leave.emp_id', '=', 'employees.id')
                    ->where('current_leave.approved', 'Yes')
                    ->whereDate('current_leave.start_date', '<=', $today)
                    ->whereDate('current_leave.end_date', '>=', $today);
            })
            ->leftJoin('employee_sicks as current_sick', function ($join) use ($today) {
                $join->on('current_sick.emp_id', '=', 'employees.id')
                    ->whereDate('current_sick.start_date', '<=', $today)
                    ->whereDate('current_sick.end_date', '>=', $today);
            })
            ->select([
                'employees.id',
                'employees.name',
                'employees.lastname',
                'employees.image',
                'employees.gender',
                'employees.status',

                'current_leave.start_date as current_leave_from',
                'current_leave.end_date as current_leave_to',
                'current_leave.leave_type as current_leave_type',
                'current_leave.reason as current_leave_reason',
                'current_leave.status as current_leave_status',

                'current_sick.start_date as current_sick_from',
                'current_sick.end_date as current_sick_to',
                'current_sick.status as current_sick_status',
                'current_sick.status_msg as current_sick_msg',
            ])
            ->orderBy('employees.name')
            ->get()
            ->unique('id')
            ->values();

        $data['allEmployees'] = $data['employees'];

        /*
        |--------------------------------------------------------------------------
        | Products / Services base data
        |--------------------------------------------------------------------------
        */
        $articleGroups = ArticleGroup::query()
            ->orderBy('article_group', 'asc')
            ->get([
                'id',
                'article_group',
            ]);

        $phaseSections = PhaseSection::query()
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhere('status', 'Published')
                    ->orWhere('status', 'published');
            })
            ->orderBy('id', 'asc')
            ->get([
                'id',
                'product_id',
                'phase_section',
                'status',
            ]);

        $data['products'] = $articleGroups
            ->map(function ($group) {
                return [
                    'id' => (int) $group->id,
                    'article_group' => $group->article_group,
                ];
            })
            ->values();

        $data['services'] = $phaseSections
            ->map(function ($service) {
                return [
                    'id' => (int) $service->id,
                    'product_id' => (int) $service->product_id,
                    'phase_section' => $service->phase_section,
                    'status' => $service->status,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Appointment employee pivot data
        |--------------------------------------------------------------------------
        */
        $data['task_employee'] = MainAppointmentEmployee::query()
            ->join('employees', 'employees.id', '=', 'main_appointment_employees.employee_id')
            ->get([
                'employees.id as employee_id',
                'employees.name',
                'employees.lastname',
                'employees.image',
                'employees.gender',
                'main_appointment_employees.appointment_id',
                'main_appointment_employees.status',
                'main_appointment_employees.reason',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Branch addresses
        |--------------------------------------------------------------------------
        */
        $data['branch_addresses'] = DB::table('branch_addresses')
            ->join('branches', 'branches.id', '=', 'branch_addresses.branch_id')
            ->select('branch_addresses.*', 'branches.initial as branch_initial')
            ->whereNull('branch_addresses.deleted_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Product position mappings
        |--------------------------------------------------------------------------
        */
        $productPositions = ProductPosition::query()
            ->with([
                'department:id,department_name',
                'service:id,product_id,phase_section',
                'articleGroup:id,article_group',
            ])
            ->get([
                'id',
                'stage',
                'article_group_id',
                'service_id',
                'department_id',
                'position_ids',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Department employees with position data + current Urlaub/Krank
        |--------------------------------------------------------------------------
        | Used by wizard auto employee matching.
        |--------------------------------------------------------------------------
        */
        $departmentPositions = DB::table('department_positions')
            ->join('employees', 'employees.id', '=', 'department_positions.employee_id')
            ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
            ->leftJoin('leaves as current_leave', function ($join) use ($today) {
                $join->on('current_leave.emp_id', '=', 'employees.id')
                    ->where('current_leave.approved', 'Yes')
                    ->whereDate('current_leave.start_date', '<=', $today)
                    ->whereDate('current_leave.end_date', '>=', $today);
            })
            ->leftJoin('employee_sicks as current_sick', function ($join) use ($today) {
                $join->on('current_sick.emp_id', '=', 'employees.id')
                    ->whereDate('current_sick.start_date', '<=', $today)
                    ->whereDate('current_sick.end_date', '>=', $today);
            })
            ->select([
                'department_positions.employee_id',
                'department_positions.department_id',
                'department_positions.position_id',
                'department_positions.main',
                'department_positions.percent',
                'department_positions.montage_percent',
                'department_positions.office_percent',
                'department_positions.working_hours',

                'employees.name',
                'employees.lastname',
                'employees.image',
                'employees.gender',
                'employees.status',

                'positions.position as position_name',

                'current_leave.start_date as current_leave_from',
                'current_leave.end_date as current_leave_to',
                'current_leave.leave_type as current_leave_type',
                'current_leave.reason as current_leave_reason',
                'current_leave.status as current_leave_status',

                'current_sick.start_date as current_sick_from',
                'current_sick.end_date as current_sick_to',
                'current_sick.status as current_sick_status',
                'current_sick.status_msg as current_sick_msg',
            ])
            ->whereNull('employees.deleted_at')
            ->where('employees.status', 'Active')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */
        $formatEmployeeRow = function ($row) {
            return [
                'id' => (int) $row->employee_id,
                'name' => trim(($row->name ?? '') . ' ' . ($row->lastname ?? '')),
                'firstname' => $row->name,
                'lastname' => $row->lastname,
                'image' => $row->image,
                'gender' => $row->gender,
                'position_id' => $row->position_id ? (int) $row->position_id : null,
                'position_name' => $row->position_name,
                'main' => $row->main,
                'percent' => $row->percent,
                'montage_percent' => $row->montage_percent,
                'office_percent' => $row->office_percent,
                'working_hours' => $row->working_hours,
                'role_type' => $this->mapMainToRoleType($row->main),

                /*
                |--------------------------------------------------------------------------
                | Current absence info for wizard / picker UI
                |--------------------------------------------------------------------------
                */
                'current_leave_from' => $row->current_leave_from,
                'current_leave_to' => $row->current_leave_to,
                'current_leave_type' => $row->current_leave_type,
                'current_leave_reason' => $row->current_leave_reason,
                'current_leave_status' => $row->current_leave_status,

                'current_sick_from' => $row->current_sick_from,
                'current_sick_to' => $row->current_sick_to,
                'current_sick_status' => $row->current_sick_status,
                'current_sick_msg' => $row->current_sick_msg,
            ];
        };

        /*
        |--------------------------------------------------------------------------
        | All active employees formatted
        |--------------------------------------------------------------------------
        */
        $allActiveEmployeesFormatted = $departmentPositions
            ->map($formatEmployeeRow)
            ->unique('id')
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Employees indexed by department + position
        |--------------------------------------------------------------------------
        */
        $employeesByDepartmentAndPosition = [];

        foreach ($departmentPositions as $row) {
            $deptId = (int) $row->department_id;
            $posId = (int) $row->position_id;

            if (!$deptId || !$posId) {
                continue;
            }

            if (!isset($employeesByDepartmentAndPosition[$deptId])) {
                $employeesByDepartmentAndPosition[$deptId] = [];
            }

            if (!isset($employeesByDepartmentAndPosition[$deptId][$posId])) {
                $employeesByDepartmentAndPosition[$deptId][$posId] = [];
            }

            $employeesByDepartmentAndPosition[$deptId][$posId][] = $formatEmployeeRow($row);
        }

        /*
        |--------------------------------------------------------------------------
        | Index product positions by article group + service
        |--------------------------------------------------------------------------
        */
        $productPositionIndex = [];

        foreach ($productPositions as $pp) {
            $articleGroupId = (int) $pp->article_group_id;
            $serviceId = (int) $pp->service_id;

            if (!$articleGroupId || !$serviceId) {
                continue;
            }

            if (!isset($productPositionIndex[$articleGroupId])) {
                $productPositionIndex[$articleGroupId] = [];
            }

            $productPositionIndex[$articleGroupId][$serviceId] = [
                'id' => (int) $pp->id,
                'stage' => $pp->stage,
                'department_id' => $pp->department_id ? (int) $pp->department_id : null,
                'department_name' => optional($pp->department)->department_name,
                'position_ids' => [
                    'internal' => array_map('intval', array_values($pp->internalPositionIds())),
                    'external' => array_map('intval', array_values($pp->externalPositionIds())),
                ],
                'position_names' => $pp->position_names,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Wizard products
        | Each article group shows only its own services
        |--------------------------------------------------------------------------
        */
        $wizardProducts = [];

        foreach ($articleGroups as $group) {
            $groupId = (int) $group->id;

            $servicesForGroup = $phaseSections
                ->where('product_id', $groupId)
                ->values();

            $preparedServices = [];

            foreach ($servicesForGroup as $service) {
                $serviceId = (int) $service->id;
                $mapping = $productPositionIndex[$groupId][$serviceId] ?? null;

                $departmentId = $mapping['department_id'] ?? null;
                $departmentName = $mapping['department_name'] ?? null;

                $internalEmployees = [];
                $externalEmployees = [];

                $internalPositionIds = $mapping['position_ids']['internal'] ?? [];
                $externalPositionIds = $mapping['position_ids']['external'] ?? [];

                $internalPositions = $mapping['position_names']['internal'] ?? [];
                $externalPositions = $mapping['position_names']['external'] ?? [];

                if ($departmentId) {
                    foreach ($internalPositionIds as $positionId) {
                        $internalEmployees = array_merge(
                            $internalEmployees,
                            $employeesByDepartmentAndPosition[$departmentId][$positionId] ?? []
                        );
                    }

                    foreach ($externalPositionIds as $positionId) {
                        $externalEmployees = array_merge(
                            $externalEmployees,
                            $employeesByDepartmentAndPosition[$departmentId][$positionId] ?? []
                        );
                    }
                }

                $internalEmployees = collect($internalEmployees)
                    ->unique('id')
                    ->values()
                    ->all();

                $externalEmployees = collect($externalEmployees)
                    ->unique('id')
                    ->values()
                    ->all();

                if (empty($internalEmployees)) {
                    $internalEmployees = $allActiveEmployeesFormatted;
                }

                if (empty($externalEmployees)) {
                    $externalEmployees = $allActiveEmployeesFormatted;
                }

                $preparedServices[] = [
                    'id' => $serviceId,
                    'product_id' => $groupId,
                    'name' => $service->phase_section,
                    'name_de' => $service->german_name ?? ucfirst($service->phase_section),
                    'department_id' => $departmentId,
                    'department_name' => $departmentName,
                    'internal_positions' => $internalPositions,
                    'external_positions' => $externalPositions,
                    'internal_employees' => $internalEmployees,
                    'external_employees' => $externalEmployees,
                    'auto_internal_employee_id' => count($internalEmployees) === 1 ? (int) $internalEmployees[0]['id'] : null,
                    'auto_external_employee_id' => count($externalEmployees) === 1 ? (int) $externalEmployees[0]['id'] : null,
                    'has_mapping' => (bool) $mapping,
                ];
            }

            $wizardProducts[] = [
                'id' => $groupId,
                'name' => $group->article_group,
                'services' => array_values($preparedServices),
            ];
        }

        $data['wizardProducts'] = array_values($wizardProducts);

        /*
        |--------------------------------------------------------------------------
        | Employees for wizard JS
        |--------------------------------------------------------------------------
        */
        $data['employeesForWizard'] = collect($data['employees'])
            ->map(function ($employee) {
                return [
                    'id' => (int) $employee->id,
                    'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    'firstname' => $employee->name,
                    'lastname' => $employee->lastname,
                    'image' => $employee->image,
                    'gender' => $employee->gender,

                    'current_leave_from' => $employee->current_leave_from,
                    'current_leave_to' => $employee->current_leave_to,
                    'current_leave_type' => $employee->current_leave_type,
                    'current_leave_reason' => $employee->current_leave_reason,
                    'current_leave_status' => $employee->current_leave_status,

                    'current_sick_from' => $employee->current_sick_from,
                    'current_sick_to' => $employee->current_sick_to,
                    'current_sick_status' => $employee->current_sick_status,
                    'current_sick_msg' => $employee->current_sick_msg,
                ];
            })
            ->values();


        /*
        |--------------------------------------------------------------------------
        | CRM Lead Stages for appointment popup + task creation
        |--------------------------------------------------------------------------
        */
        $data['leadStages'] = \App\Models\LeadStage::query()
            ->active()
            ->with('activeSubStages')
            ->ordered()
            ->get();

        $data['leadStageOptions'] = $data['leadStages']->map(function ($stage) {
            return [
                'id' => (int) $stage->id,
                'key' => $stage->key,
                'name' => $stage->name,
                'color' => $stage->color ?: '#74b2d4',
                'sub_stages' => $stage->activeSubStages->map(function ($subStage) {
                    return [
                        'id' => (int) $subStage->id,
                        'lead_stage_id' => (int) $subStage->lead_stage_id,
                        'key' => $subStage->key,
                        'name' => $subStage->name,
                        'color' => $subStage->color ?: '#93c21c',
                    ];
                })->values(),
            ];
        })->values();

        return view('admin.todo.personal.calendar', $data);
    }
public function productServices(ArticleGroup $product)
{
    $defaultMap = [
        'complete'    => 'Komplettlösung',
        'montage'     => 'Montage',
        'product'     => 'Produkt',
        'plan'        => 'Planung',
        'maintenance' => 'Wartung',
        'repair'      => 'Reparatur',
        'reclaim'     => 'Reklamation',
        'others'      => 'Sonstiges',
    ];

    $existingServices = PhaseSection::query()
        ->where('product_id', $product->id)
        ->whereNull('deleted_at')
        ->where(function ($q) {
            $q->whereNull('status')
              ->orWhere('status', 'Published')
              ->orWhere('status', 'published');
        })
        ->orderBy('id', 'asc')
        ->get([
            'id',
            'product_id',
            'phase_section',
            'status',
        ]);

    $existingNormalized = $existingServices
        ->map(fn ($service) => strtolower(trim($service->phase_section)))
        ->toArray();

    foreach ($defaultMap as $key => $label) {
        if (!in_array($key, $existingNormalized, true)) {
            $newService = PhaseSection::create([
                'product_id'    => $product->id,
                'phase_section' => $key,
                'status'        => 'Published',
            ]);

            $existingServices->push($newService);
        }
    }

    $services = $existingServices
        ->sortBy('id')
        ->map(function ($service) use ($defaultMap) {
            $key = strtolower(trim($service->phase_section));

            return [
                'id'         => (int) $service->id,
                'product_id' => (int) $service->product_id,
                'name'       => $service->phase_section,
                'name_de'    => $defaultMap[$key] ?? ucfirst($service->phase_section),
                'status'     => $service->status,
            ];
        })
        ->values();

    return response()->json([
        'success'  => true,
        'product'  => [
            'id'   => (int) $product->id,
            'name' => $product->article_group,
        ],
        'services' => $services,
    ]);
}

public static function ensureDefaultPhaseSectionsFor(ArticleGroup $group): void
{
    $defaults = [
        'complete',
        'montage',
        'product',
        'plan',
        'maintenance',
        'repair',
        'reclaim',
        'others',
    ];

    $existing = $group->phaseSections()
        ->pluck('phase_section')
        ->map(fn ($v) => strtolower(trim($v)))
        ->toArray();

    foreach ($defaults as $name) {
        if (!in_array($name, $existing, true)) {
            $group->phaseSections()->create([
                'phase_section' => $name,
                'status' => 'Published',
            ]);
        }
    }
}

public function serviceEmployees(ArticleGroup $product, PhaseSection $service)
{
    if ((int) $service->product_id !== (int) $product->id) {
        return response()->json([
            'success' => false,
            'message' => 'Der Service gehört nicht zu diesem Produkt.',
        ], 422);
    }

    $productPosition = ProductPosition::query()
        ->with(['department:id,department_name'])
        ->where('article_group_id', $product->id)
        ->where('service_id', $service->id)
        ->first([
            'id',
            'article_group_id',
            'service_id',
            'department_id',
            'position_ids',
        ]);

    $departmentPositions = DB::table('department_positions')
        ->join('employees', 'employees.id', '=', 'department_positions.employee_id')
        ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
        ->select([
            'department_positions.employee_id',
            'department_positions.department_id',
            'department_positions.position_id',
            'department_positions.main',
            'department_positions.percent',
            'department_positions.montage_percent',
            'department_positions.office_percent',
            'department_positions.working_hours',
            'employees.name',
            'employees.lastname',
            'employees.image',
            'employees.gender',
            'employees.status',
            'positions.position as position_name',
        ])
        ->whereNull('employees.deleted_at')
        ->where('employees.status', 'Active')
        ->get();

    $formatEmployee = function ($row) {
        return [
            'id'               => (int) $row->employee_id,
            'name'             => trim(($row->name ?? '') . ' ' . ($row->lastname ?? '')),
            'firstname'        => $row->name,
            'lastname'         => $row->lastname,
            'image'            => $row->image,
            'gender'           => $row->gender,
            'position_id'      => $row->position_id ? (int) $row->position_id : null,
            'position_name'    => $row->position_name,
            'main'             => $row->main,
            'percent'          => $row->percent,
            'montage_percent'  => $row->montage_percent,
            'office_percent'   => $row->office_percent,
            'working_hours'    => $row->working_hours,
        ];
    };

    $allEmployees = collect($departmentPositions)
        ->map($formatEmployee)
        ->unique('id')
        ->values()
        ->all();

    $employeesByDepartmentAndPosition = [];

    foreach ($departmentPositions as $row) {
        $deptId = (int) $row->department_id;
        $posId  = (int) $row->position_id;

        if (!$deptId || !$posId) {
            continue;
        }

        if (!isset($employeesByDepartmentAndPosition[$deptId])) {
            $employeesByDepartmentAndPosition[$deptId] = [];
        }

        if (!isset($employeesByDepartmentAndPosition[$deptId][$posId])) {
            $employeesByDepartmentAndPosition[$deptId][$posId] = [];
        }

        $employeesByDepartmentAndPosition[$deptId][$posId][] = $formatEmployee($row);
    }

    $internalEmployees = [];
    $externalEmployees = [];
    $internalPositions = [];
    $externalPositions = [];
    $departmentId      = null;
    $departmentName    = null;
    $hasMapping        = false;

    if ($productPosition) {
        $hasMapping     = true;
        $departmentId   = $productPosition->department_id ? (int) $productPosition->department_id : null;
        $departmentName = optional($productPosition->department)->department_name;

        $internalIds = array_map('intval', array_values($productPosition->internalPositionIds()));
        $externalIds = array_map('intval', array_values($productPosition->externalPositionIds()));

        $internalPositions = $productPosition->position_names['internal'] ?? [];
        $externalPositions = $productPosition->position_names['external'] ?? [];

        if ($departmentId) {
            foreach ($internalIds as $positionId) {
                $internalEmployees = array_merge(
                    $internalEmployees,
                    $employeesByDepartmentAndPosition[$departmentId][$positionId] ?? []
                );
            }

            foreach ($externalIds as $positionId) {
                $externalEmployees = array_merge(
                    $externalEmployees,
                    $employeesByDepartmentAndPosition[$departmentId][$positionId] ?? []
                );
            }
        }
    }

    $internalEmployees = collect($internalEmployees)->unique('id')->values()->all();
    $externalEmployees = collect($externalEmployees)->unique('id')->values()->all();

    if (empty($internalEmployees)) {
        $internalEmployees = $allEmployees;
    }

    if (empty($externalEmployees)) {
        $externalEmployees = $allEmployees;
    }

    return response()->json([
        'success' => true,
        'product' => [
            'id'   => (int) $product->id,
            'name' => $product->article_group,
        ],
        'service' => [
            'id'   => (int) $service->id,
            'name' => $service->phase_section,
        ],
        'department_id'   => $departmentId,
        'department_name' => $departmentName,
        'internal_positions' => array_values(array_unique($internalPositions)),
        'external_positions' => array_values(array_unique($externalPositions)),
        'internal_employees' => $internalEmployees,
        'external_employees' => $externalEmployees,
        'auto_internal_employee_id' => !empty($internalEmployees) ? (int) $internalEmployees[0]['id'] : null,
        'auto_external_employee_id' => !empty($externalEmployees) ? (int) $externalEmployees[0]['id'] : null,
        'has_mapping' => $hasMapping,
    ]);
}

private function mapMainToRoleType($main): ?string
{
    $value = strtolower(trim((string) $main));

    return match ($value) {
        'internal', 'innendienst', 'inside', 'office' => 'innendienst',
        'external', 'aussendienst', 'außendienst', 'field', 'montage' => 'aussendienst',
        default => null,
    };
}
public function mobile() {
    $user = auth()->user()->name;
 

    $settings = DB::table('personal_settings')
            ->where('employee_id', $user)
            ->value('calendar_settings'); // use value() instead of pluck() to get single row

        $favoriteEmployeeIds = [];

        if ($settings) {
            $parsed = json_decode($settings, true);
            if (isset($parsed['favorite_employees'])) {
                $favoriteEmployeeIds = $parsed['favorite_employees'];
            }
        }

        $data['favorite_employee_ids'] = $favoriteEmployeeIds;

    // Your existing data...
    $data['employees'] = DB::table('employees')
        ->where('status', '!=', 'Deactive')
        ->select('id', 'name', 'lastname', 'image', 'gender')
        ->orderBy('name', 'asc')
        ->get();

    $data['customers'] = NewLeads::whereNull('deleted_at')
        ->where('status', '!=', 'Junk')
        ->orderBy('name', 'asc')
        ->get([
            'id', 'name', 'lastname', 'street', 'postcode', 'city', 'latitude', 'longitude', 'phone'
        ]);

    $data['branches'] = Branch::where('status', 'Published')
        ->orderBy('branch', 'asc')
        ->get(['id', 'branch']);

    $data['task_employee'] = MainAppointmentEmployee::join('employees', 'employees.id', '=', 'main_appointment_employees.employee_id')
        ->get([
            'employees.id as employee_id',
            'employees.name',
            'employees.lastname',
            'employees.image',
            'employees.gender',
            'main_appointment_employees.appointment_id',
            'main_appointment_employees.status',
            'main_appointment_employees.reason'
        ]);

    $data['allEmployees'] = DB::table('employees')
        ->select('employees.id','employees.name', 'employees.lastname', 'employees.image')
        ->where('status', '!=', 'Inactive')
        ->get();

    $data['branch_addresses'] = DB::table('branch_addresses')
        ->join('branches', 'branches.id', '=', 'branch_addresses.branch_id')
        ->select('branch_addresses.*', 'branches.initial as branch_initial')
        ->whereNull('branch_addresses.deleted_at')
        ->get();

    return view('admin.todo.personal.mobile_calendar', $data);
}

public function getAppointments(Request $request)
{
    $date = $request->input('date', Carbon::today()->toDateString());
    $employeeIds = $request->input('employees', []);
    $keyword = $request->input('keyword');

 

    $query = MainAppointment::with([
        'employees' => function ($q) {
            $q->where('employees.status', '!=', 'Inactive')  
              ->select('employees.id', 'employees.name', 'employees.color', 'employees.image', 'employees.status');
        }
    ])
    ->whereDate('start_date', $date)
    ->whereNull('deleted_at');

    $weekDates = $request->input('week_dates', []);
        if (!empty($weekDates)) {
            $query->whereIn(DB::raw('DATE(start_date)'), $weekDates);
        } else {
            $date = $request->input('date', Carbon::today()->toDateString());
            $query->whereDate('start_date', $date);
        }

    // 🔒 Default: restrict to current user if no employeeIds provided
    if (empty($employeeIds)) {
         \Log::info('🛡️ No employee filter → restrict to logged-in employee', ['user' => auth()->user()->name]);
        $query->whereHas('appointmentEmployees', function ($q) {
            $q->where('employee_id', (string) auth()->user()->name); // 🛡️ Force string
        });

    } else {
        // 🧯 If employee filter is passed, use that
        $query->whereHas('appointmentEmployees', function ($q) use ($employeeIds) {
            $q->whereIn('employee_id', $employeeIds);
        });
    }

    // 🔍 Keyword search
    if ($keyword) {
        $query->where(function ($q) use ($keyword) {
            $q->where('name', 'LIKE', "%$keyword%")
              ->orWhere('note', 'LIKE', "%$keyword%")
              ->orWhere('full_address', 'LIKE', "%$keyword%")
              ->orWhere('appointment_type', 'LIKE', "%$keyword%")
              ->orWhere('status', 'LIKE', "%$keyword%")
              ->orWhere('id', $keyword);
        });
    }

   

    // 📦 Transform appointments
    $appointments = $query->get()->map(function ($a) {
        $employee = $a->employees->first();
        $productData = json_decode($a->products, true);
        $productInfo = collect($productData)->first();
        $altId = $productInfo[0] ?? null;
        $productId = $productInfo[1] ?? null;
        $customerId = $productInfo[2] ?? null;

        $customer = NewLeads::find($customerId);
        $object = LeadAlternativeAdd::find($altId);

        $images = \App\Models\Image::where('status', 'screenshot')
            ->where('customer_id', $customerId)
            ->where('alternative_id', $altId)
            ->pluck('image')
            ->map(fn($path) => asset('uploads/' . $path))
            ->toArray();

        return [
            'id' => $a->id,
            'name' => $a->name,
            'start_date' => $a->start_date,
            'end_date' => $a->end_date,
            'start_time' => $a->start_time,
            'end_time' => $a->end_time,
            'street' => $a->street ?? '',
            'city' => $a->city ?? '',
            'postcode' => $a->postcode ?? '',
            'latitude' => $a->latitude ?? '',
            'longitude' => $a->longitude ?? '',
            'is_report' => $a->is_report,
            'status' => $a->status,
            'description' => $a->note,
            'code' => 'DE' . str_pad($a->id, 6, '0', STR_PAD_LEFT),

            'employee_name' => $employee->name ?? 'Unbekannt',
            'employee_color' => $employee->color ?? '#8fc73e',
            'employee_image' => $employee->image ? asset('images/employee/' . $employee->image) : null,

            'customer_name' => $customer ? trim($customer->name . ' ' . $customer->lastname) : null,
            'object_name' => $object->object_name ?? null,
            'object_city' => $object->city ?? null,
            'object_street' => $object->street ?? null,
            'object_note' => $object->note ?? null,

            'images' => $images,
        ];
    });


     \Log::info('Appointment request', [
        'date' => $date,
        'employees' => $employeeIds,
        'user' => auth()->user()->name,
        'appointment'   => $appointments,
    ]);
    return response()->json($appointments);
}
 
public function getMonthlyAppointments(Request $request)
{
    $year = $request->input('year');
    $month = $request->input('month');
    $employeeIds = $request->input('employees', []);

    if (!$year || !$month) {
        return response()->json([]);
    }

    $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
    $end = (clone $start)->endOfMonth();

    $query = MainAppointment::with('employees')
        ->whereBetween('start_date', [$start, $end])
        ->whereNull('deleted_at');

    if (empty($employeeIds)) {
        $query->whereHas('appointmentEmployees', function ($q) {
            $q->where('employee_id', auth()->user()->name);
        });
    } else {
        $query->whereHas('appointmentEmployees', function ($q) use ($employeeIds) {
            $q->whereIn('employee_id', $employeeIds);
        });
    }

    $appointments = $query->get(['id', 'start_date']);

    return response()->json($appointments);
}

    public function calendar(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Inputs
        |--------------------------------------------------------------------------
        */
        $employeeData = json_decode($request->input('employee_data'), true) ?? [];
        $filterDate = $request->input('filter_date');

        /*
        |--------------------------------------------------------------------------
        | FullCalendar visible range / default upcoming range
        |--------------------------------------------------------------------------
        | The old bug happened because startDate/endDate were NULL when filter_date
        | was not sent. Then old leaves/sicks/holidays were loaded.
        |--------------------------------------------------------------------------
        */
        $today = Carbon::today();

        $rangeStart = $request->input('start');
        $rangeEnd = $request->input('end');

        if ($filterDate) {
            $startDate = Carbon::parse($filterDate)->startOfDay();
            $endDate = Carbon::parse($filterDate)->endOfDay();
        } else {
            $startDate = $rangeStart
                ? Carbon::parse($rangeStart)->startOfDay()
                : $today->copy()->startOfDay();

            $endDate = $rangeEnd
                ? Carbon::parse($rangeEnd)->endOfDay()
                : $today->copy()->addMonths(3)->endOfDay();
        }

        /*
        |--------------------------------------------------------------------------
        | When true:
        | Urlaub / Krank / recurring leaves are loaded for all employees.
        | Tasks and appointments still follow selected employees only.
        |--------------------------------------------------------------------------
        */
        $includeAllAbsences = $request->boolean('include_all_absences');

        /*
        |--------------------------------------------------------------------------
        | Papierkorb-Modus
        |--------------------------------------------------------------------------
        | When true, the calendar feed returns ONLY soft-deleted appointments.
        |--------------------------------------------------------------------------
        */
        $onlyDeletedAppointments = $request->boolean('only_deleted_appointments');

        if (empty($employeeData)) {
            $employeeData[] = [
                'employee_id' => auth()->user()->name, // users.name = employees.id in your app
                'tasks_only' => 0,
                'appointments_only' => 1,
            ];
        }

        $selectedEmployeeIds = collect($employeeData)
            ->pluck('employee_id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        \Log::info('Employee Checkbox status:', [
            'employee_data' => $employeeData,
            'only_deleted_appointments' => $onlyDeletedAppointments,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ]);

        $all = collect();

        /*
        |--------------------------------------------------------------------------
        | Helper: date overlap filter
        |--------------------------------------------------------------------------
        | Always active now. This prevents old holidays/leaves/sicks from loading.
        |--------------------------------------------------------------------------
        */
        $applyDateOverlap = function ($q, string $startColumn, string $endColumn) use ($startDate, $endDate) {
            $q->whereDate($startColumn, '<=', $endDate->toDateString())
                ->whereDate($endColumn, '>=', $startDate->toDateString());
        };

        /*
        |--------------------------------------------------------------------------
        | Per selected employee: Personal Tasks + Appointments only
        |--------------------------------------------------------------------------
        */
        foreach ($employeeData as $row) {
            if (empty($row['employee_id'])) {
                continue;
            }

            $employeeId = (int) $row['employee_id'];
            $tasksOnly = (int) ($row['tasks_only'] ?? 0);
            $appointmentsOnly = (int) ($row['appointments_only'] ?? 0);

            /*
            |--------------------------------------------------------------------------
            | Personal Tasks
            |--------------------------------------------------------------------------
            */
            if (!$onlyDeletedAppointments && $tasksOnly) {
                $tasks = DB::table('employees_personal_tasks')
                    ->leftJoin('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
                    ->leftJoin('employees as emp', 'emp.id', '=', 'employees_personal_tasks.employee_id')
                    ->leftJoin('lead_stages as task_stage', 'task_stage.id', '=', 'personal_tasks.lead_stage_id')
                    ->leftJoin('lead_stage_sub_stages as task_sub_stage', 'task_sub_stage.id', '=', 'personal_tasks.lead_stage_sub_stage_id')
                    ->selectRaw('
                    personal_tasks.id,
                    personal_tasks.task_title as title,
                    personal_tasks.priority,
                    emp.color as backgroundColor,
                    personal_tasks.task_status,
                    personal_tasks.public,
                    personal_tasks.description,
                    personal_tasks.lead_product_list_id,
                    personal_tasks.lead_stage_id,
                    task_stage.name as lead_stage_name,
                    task_stage.color as lead_stage_color,
                    personal_tasks.lead_stage_sub_stage_id,
                    task_sub_stage.name as lead_stage_sub_stage_name,
                    task_sub_stage.color as lead_stage_sub_stage_color,
                    personal_tasks.due_date as start_date,
                    personal_tasks.due_date as end_date,
                    COALESCE(personal_tasks.due_time, "00:00:00") as start_time,
                    COALESCE(personal_tasks.due_time, "23:59:59") as end_time,
                    personal_tasks.deleted_at,

                    employees_personal_tasks.id as emp_personal_id,
                    emp.id as employee_id,
                    emp.name,
                    emp.lastname,
                    emp.image,
                    emp.gender,

                    NULL as created_by,
                    NULL as creator_name,
                    NULL as creator_lastname,
                    NULL as creator_image,
                    NULL as creator_gender,

                    NULL as customer_id,
                    NULL as phone,
                    NULL as email,
                    NULL as is_report,
                    NULL as full_address,
                    NULL as street,
                    NULL as postcode,
                    NULL as city,
                    NULL as latitude,
                    NULL as longitude,
                    NULL as contact_id,
                    NULL as contact_type,
                    NULL as appointment_type,
                    NULL as execution_type,
                    NULL as next_step,
                    NULL as responsible_report,
                    NULL as problem_id,
                    NULL as problem_task_id,
                    NULL as leave_type,
                    NULL as reason,
                    NULL as status,

                    "task" as type
                ')
                    ->where('emp.id', $employeeId)
                    ->whereNull('personal_tasks.deleted_at')
                    ->whereNotNull('personal_tasks.due_date')
                    ->where(function ($q) use ($applyDateOverlap) {
                        $applyDateOverlap($q, 'personal_tasks.due_date', 'personal_tasks.due_date');
                    })
                    ->get();

                $all = $all->merge($tasks);
            }

            /*
            |--------------------------------------------------------------------------
            | Appointments
            |--------------------------------------------------------------------------
            */
            if ($appointmentsOnly) {
                $appointments = DB::table('main_appointment_employees')
                    ->leftJoin('main_appointments', 'main_appointments.id', '=', 'main_appointment_employees.appointment_id')
                    ->leftJoin('employees as emp', 'emp.id', '=', 'main_appointment_employees.employee_id')
                    ->leftJoin('employees as creator', 'creator.id', '=', 'main_appointments.created_by')
                    ->leftJoin('lead_stages as appointment_stage', 'appointment_stage.id', '=', 'main_appointments.lead_stage_id')
                    ->leftJoin('lead_stage_sub_stages as appointment_sub_stage', 'appointment_sub_stage.id', '=', 'main_appointments.lead_stage_sub_stage_id')
                    ->selectRaw('
                    main_appointments.id,
                    main_appointments.name as title,
                    emp.color as backgroundColor,
                    main_appointments.priority,
                    main_appointments.status as task_status,
                    main_appointments.public,
                    main_appointments.note as description,
                    main_appointments.lead_product_list_id,
                    main_appointments.lead_stage_id,
                    appointment_stage.name as lead_stage_name,
                    appointment_stage.color as lead_stage_color,
                    main_appointments.lead_stage_sub_stage_id,
                    appointment_sub_stage.name as lead_stage_sub_stage_name,
                    appointment_sub_stage.color as lead_stage_sub_stage_color,
                    main_appointments.start_date,
                    main_appointments.end_date,
                    COALESCE(main_appointments.start_time, "00:00:00") as start_time,
                    COALESCE(main_appointments.end_time, "23:59:59") as end_time,
                    main_appointments.deleted_at,

                    NULL as emp_personal_id,
                    emp.id as employee_id,
                    emp.name,
                    emp.lastname,
                    emp.image,
                    emp.gender,

                    main_appointments.created_by,
                    creator.name as creator_name,
                    creator.lastname as creator_lastname,
                    creator.image as creator_image,
                    creator.gender as creator_gender,

                    main_appointments.customer_id,
                    main_appointments.phone,
                    main_appointments.email,
                    main_appointments.is_report,
                    main_appointments.full_address,
                    main_appointments.street,
                    main_appointments.postcode,
                    main_appointments.city,
                    main_appointments.latitude,
                    main_appointments.longitude,
                    main_appointments.contact_id,
                    main_appointments.contact_type,
                    main_appointments.appointment_type,
                    main_appointments.execution_type,
                    main_appointments.next_step,
                    main_appointments.report_responsible as responsible_report,
                    main_appointments.problem_id,
                    main_appointments.problem_task_id,

                    NULL as leave_type,
                    NULL as reason,
                    NULL as status,

                    "appointment" as type
                ')
                    ->where('emp.id', $employeeId)
                    ->when($onlyDeletedAppointments, function ($q) {
                        $q->whereNotNull('main_appointments.deleted_at');
                    }, function ($q) {
                        $q->whereNull('main_appointments.deleted_at');
                    })
                    ->whereNotNull('main_appointments.start_date')
                    ->where(function ($q) use ($applyDateOverlap) {
                        $applyDateOverlap($q, 'main_appointments.start_date', 'main_appointments.end_date');
                    })
                    ->get();

                $all = $all->merge($appointments);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Everything below is hidden in Papierkorb mode.
        |--------------------------------------------------------------------------
        */
        if (!$onlyDeletedAppointments) {
            /*
            |--------------------------------------------------------------------------
            | Absences: Approved Holidays
            |--------------------------------------------------------------------------
            */
            $holidays = DB::table('leaves')
                ->leftJoin('employees as emp', 'emp.id', '=', 'leaves.emp_id')
                ->selectRaw('
                leaves.id,
                CONCAT(emp.name, " ", emp.lastname, " – Urlaub") as title,
                emp.color as backgroundColor,
                leaves.status as task_status,
                leaves.description,
                leaves.start_date,
                leaves.end_date,
                "00:00:00" as start_time,
                "23:59:59" as end_time,
                NULL as deleted_at,

                NULL as emp_personal_id,
                emp.id as employee_id,
                emp.name,
                emp.lastname,
                emp.image,
                emp.gender,

                NULL as created_by,
                NULL as creator_name,
                NULL as creator_lastname,
                NULL as creator_image,
                NULL as creator_gender,

                NULL as priority,
                NULL as public,
                NULL as customer_id,
                NULL as phone,
                NULL as email,
                NULL as is_report,
                NULL as full_address,
                NULL as street,
                NULL as postcode,
                NULL as city,
                NULL as latitude,
                NULL as longitude,
                NULL as contact_id,
                NULL as contact_type,
                NULL as appointment_type,
                NULL as execution_type,
                NULL as next_step,
                NULL as responsible_report,
                NULL as problem_id,
                NULL as problem_task_id,
                leaves.leave_type,
                leaves.reason,
                leaves.status,

                "holiday" as type
            ')
                ->where('leaves.approved', 'Yes')
                ->whereNotNull('leaves.start_date')
                ->where(function ($q) use ($applyDateOverlap) {
                    $applyDateOverlap($q, 'leaves.start_date', 'leaves.end_date');
                })
                ->when(!$includeAllAbsences, function ($q) use ($selectedEmployeeIds) {
                    $q->whereIn('leaves.emp_id', $selectedEmployeeIds);
                })
                ->get();

            $all = $all->merge($holidays);

            /*
            |--------------------------------------------------------------------------
            | Absences: Sick Days
            |--------------------------------------------------------------------------
            */
            $sicks = DB::table('employee_sicks')
                ->leftJoin('employees as emp', 'emp.id', '=', 'employee_sicks.emp_id')
                ->selectRaw('
                employee_sicks.id,
                CONCAT(emp.name, " ", emp.lastname, " – Krank") as title,
                emp.color as backgroundColor,
                NULL as task_status,
                employee_sicks.status_msg as description,
                employee_sicks.start_date,
                employee_sicks.end_date,
                "00:00:00" as start_time,
                "23:59:59" as end_time,
                NULL as deleted_at,

                NULL as emp_personal_id,
                emp.id as employee_id,
                emp.name,
                emp.lastname,
                emp.image,
                emp.gender,

                NULL as created_by,
                NULL as creator_name,
                NULL as creator_lastname,
                NULL as creator_image,
                NULL as creator_gender,

                NULL as priority,
                NULL as public,
                NULL as customer_id,
                NULL as phone,
                NULL as email,
                NULL as is_report,
                NULL as full_address,
                NULL as street,
                NULL as postcode,
                NULL as city,
                NULL as latitude,
                NULL as longitude,
                NULL as contact_id,
                NULL as contact_type,
                NULL as appointment_type,
                NULL as execution_type,
                NULL as next_step,
                NULL as responsible_report,
                NULL as problem_id,
                NULL as problem_task_id,
                NULL as leave_type,
                NULL as reason,
                NULL as status,

                "sick" as type
            ')
                ->whereNotNull('employee_sicks.start_date')
                ->where(function ($q) use ($applyDateOverlap) {
                    $applyDateOverlap($q, 'employee_sicks.start_date', 'employee_sicks.end_date');
                })
                ->when(!$includeAllAbsences, function ($q) use ($selectedEmployeeIds) {
                    $q->whereIn('employee_sicks.emp_id', $selectedEmployeeIds);
                })
                ->get();

            $all = $all->merge($sicks);

            /*
            |--------------------------------------------------------------------------
            | Absences: Recurring Leaves
            |--------------------------------------------------------------------------
            */
            $winFrom = $startDate->copy();
            $winTo = $endDate->copy();

            $recurringLeaves = EmployeeRecurringLeave::with(['exdates', 'overrides'])
                ->where('is_active', 1)
                ->when(!$includeAllAbsences, function ($q) use ($selectedEmployeeIds) {
                    $q->whereIn('employee_id', $selectedEmployeeIds);
                })
                ->whereDate('start_date', '<=', $winTo->toDateString())
                ->where(function ($q) use ($winFrom) {
                    $q->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $winFrom->toDateString());
                })
                ->get();

            $recurringEmployees = Employee::whereIn(
                'id',
                $recurringLeaves->pluck('employee_id')->filter()->unique()->values()
            )
                ->get()
                ->keyBy('id');

            foreach ($recurringLeaves as $leave) {
                $emp = $recurringEmployees->get($leave->employee_id);

                foreach ($leave->generateOccurrences($winFrom, $winTo) as $occ) {
                    if ($occ['date'] < $winFrom->toDateString() || $occ['date'] > $winTo->toDateString()) {
                        continue;
                    }

                    $currentDateStr = $occ['date'];

                    $override = $leave->overrides->first(function ($val) use ($currentDateStr) {
                        $dbDate = $val->original_date instanceof Carbon
                            ? $val->original_date->toDateString()
                            : substr((string) $val->original_date, 0, 10);

                        return $dbDate === $currentDateStr;
                    });

                    $isCancelled = $override && $override->is_cancelled;

                    $finalDate = ($override && $override->new_date)
                        ? $override->new_date
                        : $occ['date'];

                    $finalTitle = ($override && $override->new_title)
                        ? $override->new_title
                        : $occ['title'];

                    $finalDesc = ($override && $override->new_description)
                        ? $override->new_description
                        : $occ['description'];

                    $finalTitle = trim((string) ($finalTitle ?: $leave->title ?: 'Wiederkehrender Termin'));

                    $isAllDay = $override
                        ? ($override->new_all_day ?? $occ['all_day'])
                        : $occ['all_day'];

                    $finalStartTime = ($override && $override->new_start_time)
                        ? $override->new_start_time
                        : ($occ['start_time'] ?? '00:00:00');

                    $finalEndTime = ($override && $override->new_end_time)
                        ? $override->new_end_time
                        : ($occ['end_time'] ?? '23:59:59');

                    if ($isAllDay) {
                        $finalStartTime = '00:00:00';
                        $finalEndTime = '23:59:59';
                    }

                    if ($isCancelled && !str_contains($finalTitle, 'ABGESAGT')) {
                        $finalTitle .= ' (ABGESAGT)';
                    }

                    $all->push((object) [
                        'id' => 'recurring-' . $leave->id . '-' . $currentDateStr,
                        'title' => $finalTitle,
                        'absence_title' => $finalTitle,
                        'recurring_title' => $finalTitle,
                        'original_title' => $leave->title,
                        'backgroundColor' => $isCancelled ? '#ff9999' : ($emp?->color ?? '#6c757d'),
                        'task_status' => null,
                        'description' => $finalDesc,
                        'start_date' => $finalDate,
                        'end_date' => $finalDate,
                        'start_time' => $finalStartTime,
                        'end_time' => $finalEndTime,
                        'deleted_at' => null,

                        'emp_personal_id' => null,
                        'employee_id' => $emp?->id,
                        'name' => $emp?->name,
                        'lastname' => $emp?->lastname,
                        'image' => $emp?->image,
                        'gender' => $emp?->gender,

                        'created_by' => null,
                        'creator_name' => null,
                        'creator_lastname' => null,
                        'creator_image' => null,
                        'creator_gender' => null,

                        'priority' => null,
                        'public' => null,
                        'customer_id' => null,
                        'phone' => null,
                        'email' => null,
                        'is_report' => null,
                        'full_address' => null,
                        'street' => null,
                        'postcode' => null,
                        'city' => null,
                        'latitude' => null,
                        'longitude' => null,
                        'contact_id' => null,
                        'contact_type' => null,
                        'appointment_type' => null,
                        'execution_type' => null,
                        'next_step' => null,
                        'responsible_report' => null,
                        'problem_id' => null,
                        'problem_task_id' => null,
                        'leave_type' => null,
                        'reason' => null,
                        'status' => $isCancelled ? 'cancelled' : null,
                        'is_cancelled' => (bool) $isCancelled,

                        'recurring_event_kind' => $leave->event_kind ?? 'absence',
                        'can_create_appointment' => ($leave->event_kind ?? null) === 'home_office',

                        'recurring_leave_id' => $leave->id,
                        'recurring_rule_type' => $leave->type,
                        'recurring_frequency' => $leave->frequency,
                        'recurring_interval' => $leave->interval ?: $leave->week_interval ?: $leave->month_interval,
                        'recurring_weekdays' => $leave->weekdays ?: $leave->day_of_week,
                        'recurring_day_of_month' => $leave->day_of_month,
                        'recurring_duration_days' => $leave->duration_days,
                        'recurring_start_date' => optional($leave->start_date)->toDateString(),
                        'recurring_end_date' => optional($leave->end_date)->toDateString(),
                        'recurring_original_date' => $currentDateStr,
                        'recurring_final_date' => $finalDate instanceof Carbon
                            ? $finalDate->toDateString()
                            : substr((string) $finalDate, 0, 10),
                        'has_override' => (bool) $override,
                        'override_id' => $override?->id,

                        'type' => 'recurring_leave',
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Pending Leave Requests - own calendar
            |--------------------------------------------------------------------------
            */
            $leaveRequests = DB::table('leaves')
                ->leftJoin('employees as emp', 'emp.id', '=', 'leaves.emp_id')
                ->selectRaw('
                leaves.id,
                CONCAT(emp.name, " ", emp.lastname, " – Urlaubsantrag") as title,
                emp.color as backgroundColor,
                NULL as task_status,
                leaves.reason as description,
                leaves.start_date,
                leaves.end_date,
                "00:00:00" as start_time,
                "23:59:59" as end_time,
                NULL as deleted_at,

                NULL as emp_personal_id,
                emp.id as employee_id,
                emp.name,
                emp.lastname,
                emp.image,
                emp.gender,

                NULL as created_by,
                NULL as creator_name,
                NULL as creator_lastname,
                NULL as creator_image,
                NULL as creator_gender,

                NULL as priority,
                NULL as public,
                NULL as customer_id,
                NULL as phone,
                NULL as email,
                NULL as is_report,
                NULL as full_address,
                NULL as street,
                NULL as postcode,
                NULL as city,
                NULL as latitude,
                NULL as longitude,
                NULL as contact_id,
                NULL as contact_type,
                NULL as appointment_type,
                NULL as execution_type,
                NULL as next_step,
                NULL as responsible_report,
                NULL as problem_id,
                NULL as problem_task_id,
                leaves.leave_type,
                leaves.reason,
                leaves.status,

                "leave_request" as type
            ')
                ->whereIn('leaves.emp_id', $selectedEmployeeIds)
                ->whereNull('leaves.approved')
                ->whereNotIn('leaves.status', ['reject', 'not_responsible', 'accept'])
                ->whereNotNull('leaves.start_date')
                ->where(function ($q) use ($applyDateOverlap) {
                    $applyDateOverlap($q, 'leaves.start_date', 'leaves.end_date');
                })
                ->get();

            $all = $all->merge($leaveRequests);

            /*
            |--------------------------------------------------------------------------
            | Pending Leave Requests - approver calendar
            |--------------------------------------------------------------------------
            */
            $pendingLeaves = DB::table('leaves as l')
                ->leftJoin('employees as emp', 'emp.id', '=', 'l.emp_id')
                ->selectRaw('
                l.id,
                CONCAT(emp.name, " ", emp.lastname, " – Urlaubsantrag") as title,
                emp.color as backgroundColor,
                NULL as task_status,
                l.reason as description,
                l.start_date,
                l.end_date,
                "00:00:00" as start_time,
                "23:59:59" as end_time,
                NULL as deleted_at,

                NULL as emp_personal_id,
                l.request_to as employee_id,
                emp.name,
                emp.lastname,
                emp.image,
                emp.gender,

                NULL as created_by,
                NULL as creator_name,
                NULL as creator_lastname,
                NULL as creator_image,
                NULL as creator_gender,

                NULL as priority,
                NULL as public,
                NULL as customer_id,
                NULL as phone,
                NULL as email,
                NULL as is_report,
                NULL as full_address,
                NULL as street,
                NULL as postcode,
                NULL as city,
                NULL as latitude,
                NULL as longitude,
                NULL as contact_id,
                NULL as contact_type,
                NULL as appointment_type,
                NULL as execution_type,
                NULL as next_step,
                NULL as responsible_report,
                NULL as problem_id,
                NULL as problem_task_id,
                l.leave_type,
                l.reason,
                l.status,

                "leave_request" as type
            ')
                ->whereIn('l.request_to', $selectedEmployeeIds)
                ->whereNull('l.approved')
                ->where(function ($q) {
                    $q->whereNull('l.status')
                        ->orWhereNotIn('l.status', ['reject', 'not_responsible', 'accept']);
                })
                ->whereNotNull('l.start_date')
                ->where(function ($q) use ($applyDateOverlap) {
                    $applyDateOverlap($q, 'l.start_date', 'l.end_date');
                })
                ->get();

            $all = $all->merge($pendingLeaves);

            /*
            |--------------------------------------------------------------------------
            | Public Holidays
            |--------------------------------------------------------------------------
            */
            $public = DB::table('public_holidays')
                ->selectRaw('
                id,
                name as title,
                "#FF5733" as backgroundColor,
                NULL as task_status,
                NULL as description,
                start_date,
                end_date,
                "00:00:00" as start_time,
                "23:59:59" as end_time,
                NULL as deleted_at,

                NULL as emp_personal_id,
                NULL as employee_id,
                NULL as name,
                NULL as lastname,
                NULL as image,
                NULL as gender,

                NULL as created_by,
                NULL as creator_name,
                NULL as creator_lastname,
                NULL as creator_image,
                NULL as creator_gender,

                NULL as priority,
                NULL as public,
                NULL as customer_id,
                NULL as phone,
                NULL as email,
                NULL as is_report,
                NULL as full_address,
                NULL as street,
                NULL as postcode,
                city,
                NULL as latitude,
                NULL as longitude,
                NULL as contact_id,
                NULL as contact_type,
                NULL as appointment_type,
                NULL as execution_type,
                NULL as next_step,
                NULL as responsible_report,
                NULL as problem_id,
                NULL as problem_task_id,
                NULL as leave_type,
                NULL as reason,
                NULL as status,

                "public_holiday" as type
            ')
                ->whereNotNull('start_date')
                ->where(function ($q) use ($applyDateOverlap) {
                    $applyDateOverlap($q, 'start_date', 'end_date');
                })
                ->get();

            $all = $all->merge($public);
        }

        /*
        |--------------------------------------------------------------------------
        | Build full employee rosters
        |--------------------------------------------------------------------------
        */
        $appointmentIds = $all
            ->where('type', 'appointment')
            ->pluck('id')
            ->unique()
            ->values();

        $taskIds = $all
            ->where('type', 'task')
            ->pluck('id')
            ->unique()
            ->values();

        $appointmentEmployees = collect();

        if ($appointmentIds->isNotEmpty()) {
            $appointmentEmployees = DB::table('main_appointment_employees as mae')
                ->join('employees as emp', 'emp.id', '=', 'mae.employee_id')
                ->whereIn('mae.appointment_id', $appointmentIds)
                ->selectRaw('
                mae.appointment_id as id,
                emp.id as employee_id,
                emp.name,
                emp.lastname,
                emp.image,
                emp.gender
            ')
                ->get()
                ->groupBy('id')
                ->map(function ($rows) {
                    return $rows->map(fn($r) => [
                        'employee_id' => (int) $r->employee_id,
                        'name' => $r->name,
                        'lastname' => $r->lastname,
                        'image' => $r->image,
                        'gender' => $r->gender,
                    ])->values();
                });
        }

        $taskEmployees = collect();

        if ($taskIds->isNotEmpty()) {
            $taskEmployees = DB::table('employees_personal_tasks as ept')
                ->join('employees as emp', 'emp.id', '=', 'ept.employee_id')
                ->whereIn('ept.task_id', $taskIds)
                ->selectRaw('
                ept.task_id as id,
                emp.id as employee_id,
                emp.name,
                emp.lastname,
                emp.image,
                emp.gender
            ')
                ->get()
                ->groupBy('id')
                ->map(function ($rows) {
                    return $rows->map(fn($r) => [
                        'employee_id' => (int) $r->employee_id,
                        'name' => $r->name,
                        'lastname' => $r->lastname,
                        'image' => $r->image,
                        'gender' => $r->gender,
                    ])->values();
                });
        }

        /*
        |--------------------------------------------------------------------------
        | Final grouped response
        |--------------------------------------------------------------------------
        */
        $ticketSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="14" height="14" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h5l1 2h3a2 2 0 1 0 0 4h-3l-1 2H5a2 2 0 0 1-2-2V7zM14 5h5a2 2 0 0 1 2 2v2a3 3 0 0 0 0 6v2a2 2 0 0 1-2 2h-5l-2-4 2-4z"/></svg>';

        $grouped = $all
            ->groupBy(fn($e) => "{$e->id}-{$e->start_date}-{$e->start_time}-{$e->type}")
            ->map(function ($group) use ($appointmentEmployees, $taskEmployees, $ticketSvg) {
                $e = $group->first();

                $employees = match ($e->type) {
                    'appointment' => $appointmentEmployees->get($e->id, collect()),
                    'task' => $taskEmployees->get($e->id, collect()),
                    default => $group->map(fn($i) => [
                        'employee_id' => $i->employee_id,
                        'name' => $i->name,
                        'lastname' => $i->lastname,
                        'image' => $i->image,
                        'gender' => $i->gender,
                    ]),
                };

                $hasTicket = !empty($e->problem_id);

                $creator = !empty($e->created_by)
                    ? [
                        'id' => (int) $e->created_by,
                        'name' => $e->creator_name ?? null,
                        'lastname' => $e->creator_lastname ?? null,
                        'image' => $e->creator_image ?? null,
                        'gender' => $e->creator_gender ?? null,
                    ]
                    : null;

                return [
                    'id' => $e->id,
                    'emp_personal_id' => $e->emp_personal_id ?? null,
                    'customer_id' => $e->customer_id ?? null,
                    'title' => $e->title,
                    'absence_title' => $e->absence_title ?? $e->title,
                    'recurring_title' => $e->recurring_title ?? null,
                    'original_title' => $e->original_title ?? null,
                    'start_date' => $e->start_date,
                    'end_date' => $e->end_date,
                    'start_time' => $e->start_time,
                    'end_time' => $e->end_time,
                    'description' => $e->description ?? null,
                    'status' => $e->task_status ?? null,
                    'taskColor' => $e->backgroundColor ?? '#cccccc',
                    'priority' => $e->priority ?? null,
                    'public_view' => $e->public ?? null,
                    'lead_product_list_id' => $e->lead_product_list_id ?? null,
                    'lead_stage_id' => $e->lead_stage_id ?? null,
                    'lead_stage_name' => $e->lead_stage_name ?? null,
                    'lead_stage_color' => $e->lead_stage_color ?? null,
                    'lead_stage_sub_stage_id' => $e->lead_stage_sub_stage_id ?? null,
                    'lead_stage_sub_stage_name' => $e->lead_stage_sub_stage_name ?? null,
                    'lead_stage_sub_stage_color' => $e->lead_stage_sub_stage_color ?? null,
                    'lead_stage_context' => [
                        'lead_product_list_id' => $e->lead_product_list_id ?? null,
                        'lead_stage_id' => $e->lead_stage_id ?? null,
                        'lead_stage_name' => $e->lead_stage_name ?? null,
                        'lead_stage_color' => $e->lead_stage_color ?? null,
                        'lead_stage_sub_stage_id' => $e->lead_stage_sub_stage_id ?? null,
                        'lead_stage_sub_stage_name' => $e->lead_stage_sub_stage_name ?? null,
                        'lead_stage_sub_stage_color' => $e->lead_stage_sub_stage_color ?? null,
                    ],
                    'type' => $e->type,

                    /*
                    |--------------------------------------------------------------------------
                    | Deleted / Papierkorb meta
                    |--------------------------------------------------------------------------
                    */
                    'is_deleted' => !empty($e->deleted_at),
                    'deleted_at' => $e->deleted_at ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | Creator meta
                    |--------------------------------------------------------------------------
                    */
                    'created_by' => $e->created_by ?? null,
                    'creator' => $creator,

                    /*
                    |--------------------------------------------------------------------------
                    | Appointment meta
                    |--------------------------------------------------------------------------
                    */
                    'appointment_type' => $e->appointment_type ?? null,
                    'execution_type' => $e->execution_type ?? null,
                    'full_address' => $e->full_address ?? null,
                    'street' => $e->street ?? null,
                    'postcode' => $e->postcode ?? null,
                    'city' => $e->city ?? null,
                    'latitude' => $e->latitude ?? null,
                    'longitude' => $e->longitude ?? null,
                    'phone' => $e->phone ?? null,
                    'email' => $e->email ?? null,
                    'contact_id' => $e->contact_id ?? null,
                    'contact_type' => $e->contact_type ?? null,
                    'next_step' => $e->next_step ?? null,
                    'responsible_report' => $e->responsible_report ?? null,
                    'is_report' => $e->is_report ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | Leave meta
                    |--------------------------------------------------------------------------
                    */
                    'leave_type' => $e->leave_type ?? null,
                    'leave_reason' => $e->reason ?? null,
                    'leave_status' => $e->status ?? null,
                    'is_cancelled' => property_exists($e, 'is_cancelled') ? (bool) $e->is_cancelled : false,
                    'recurring_event_kind' => $e->recurring_event_kind ?? 'absence',
                    'can_create_appointment' => property_exists($e, 'can_create_appointment')
                        ? (bool) $e->can_create_appointment
                        : false,

                    /*
                    |--------------------------------------------------------------------------
                    | Recurring leave meta
                    |--------------------------------------------------------------------------
                    */
                    'recurring_leave_id' => $e->recurring_leave_id ?? null,
                    'recurring_rule_type' => $e->recurring_rule_type ?? null,
                    'recurring_frequency' => $e->recurring_frequency ?? null,
                    'recurring_interval' => $e->recurring_interval ?? null,
                    'recurring_weekdays' => $e->recurring_weekdays ?? null,
                    'recurring_day_of_month' => $e->recurring_day_of_month ?? null,
                    'recurring_duration_days' => $e->recurring_duration_days ?? null,
                    'recurring_start_date' => $e->recurring_start_date ?? null,
                    'recurring_end_date' => $e->recurring_end_date ?? null,
                    'recurring_original_date' => $e->recurring_original_date ?? null,
                    'recurring_final_date' => $e->recurring_final_date ?? null,
                    'has_override' => property_exists($e, 'has_override') ? (bool) $e->has_override : false,
                    'override_id' => $e->override_id ?? null,

                    /*
                    |--------------------------------------------------------------------------
                    | Ticket meta
                    |--------------------------------------------------------------------------
                    */
                    'has_ticket' => $hasTicket,
                    'ticket_problem_id' => $e->problem_id ?? null,
                    'ticket_svg' => $hasTicket ? $ticketSvg : null,

                    /*
                    |--------------------------------------------------------------------------
                    | Full roster
                    |--------------------------------------------------------------------------
                    */
                    'employees' => collect($employees)
                        ->filter(fn($x) => !empty($x['employee_id']))
                        ->unique('employee_id')
                        ->values(),
                ];
            })
            ->values();

        return response()->json([
            'data' => $grouped,
        ]);
    }
    public function fetchEvents(Request $request)
{
    // 1. Decode Filters
    $employeeData = json_decode($request->input('employee_data'), true) ?? [];
    $viewStart    = $request->input('filter_date'); // The date currently viewed in calendar

    // Default to current user if filter is empty
    if (empty($employeeData)) {
        $employeeData[] = ['employee_id' => auth()->user()->name];
    }

    // 2. Define Date Range (Fetch a bit extra to handle month transitions smoothly)
    $startDate = $viewStart ? Carbon::parse($viewStart)->subDays(7)->toDateString() : Carbon::now()->startOfMonth()->subDays(7)->toDateString();
    $endDate   = $viewStart ? Carbon::parse($viewStart)->addDays(45)->toDateString() : Carbon::now()->endOfMonth()->addDays(14)->toDateString();

    $events = collect();

    // 3. Iterate through requested employees
    foreach ($employeeData as $filter) {
        $empId = $filter['employee_id'] ?? auth()->user()->name;

        // --- Build Query ---
        $appointments = DB::table('main_appointments')
            // Join Customer to get names
            ->leftJoin('new_leads', 'new_leads.id', '=', 'main_appointments.customer_id')
            // Join Creator to get default color if needed
            ->leftJoin('employees as creator', 'creator.id', '=', 'main_appointments.created_by')
            ->select(
                'main_appointments.id',
                'main_appointments.name as title',
                'main_appointments.note as description',
                'main_appointments.start_date',
                'main_appointments.end_date',
                'main_appointments.start_time',
                'main_appointments.end_time',
                'main_appointments.color',
                'main_appointments.appointment_type',
                'main_appointments.created_by',
                'main_appointments.report_by',
                'main_appointments.status',
                
                // Address fields for Google Map
                'main_appointments.full_address',
                'main_appointments.street',
                'main_appointments.city',
                'main_appointments.postcode',

                // Customer Info
                'new_leads.name as cust_name',
                'new_leads.lastname as cust_lastname',
                'new_leads.firma as cust_company',

                // Fallback color from creator if appointment has no color
                DB::raw('COALESCE(main_appointments.color, creator.color, "#3b82f6") as computed_color')
            )
            // Logic: Show appointments created by OR reported by the employee
            ->where(function($q) use ($empId) {
                $q->where('main_appointments.created_by', $empId)
                  ->orWhere('main_appointments.report_by', $empId);
            })
            ->whereNull('main_appointments.deleted_at')
            // Date Filter
            ->where(function($q) use ($startDate, $endDate) {
                 $q->whereBetween('main_appointments.start_date', [$startDate, $endDate])
                   ->orWhereBetween('main_appointments.end_date', [$startDate, $endDate]);
            })
            ->get();

        // 4. Format Data for Frontend
        foreach ($appointments as $appt) {
            
            // --- A. Fetch Involved Employees (Avatars) ---
            // We include the creator and the person responsible for the report
            $userIds = array_unique(array_filter([$appt->created_by, $appt->report_by]));
            
            $employees = DB::table('employees')
                ->whereIn('id', $userIds)
                ->select('id', 'name', 'lastname', 'image', 'gender')
                ->get()
                ->map(function($e) {
                    // Build full image URL
                    $imgUrl = $e->image 
                        ? asset('images/employee/' . $e->image) 
                        : asset('images/default-user.png');
                        
                    return [
                        'employee_id' => $e->id,
                        'name' => $e->name . ' ' . $e->lastname,
                        'image' => $imgUrl // Helper function handles the path
                    ];
                });

            // --- B. Format Customer Name ---
            $custName = trim(($appt->cust_company ?? '') . ' ' . ($appt->cust_name ?? '') . ' ' . ($appt->cust_lastname ?? ''));
            if (empty($custName)) $custName = "Kein Kunde";

            // --- C. Push to Collection ---
            $events->push([
                'id'                => $appt->id,
                'title'             => $appt->title,
                'description'       => $appt->description,
                
                // FullCalendar Date Format
                'start_date'        => $appt->start_date,
                'start_time'        => $appt->start_time ?? '00:00:00',
                'end_date'          => $appt->end_date ?? $appt->start_date,
                'end_time'          => $appt->end_time ?? '23:59:59',

                // Styling
                'taskColor'         => $appt->computed_color,
                'type'              => 'appointment', // Identifier for JS Modal
                'appointment_type'  => $appt->appointment_type ?? 'Termin',
                
                // Location Data (For Map)
                'full_address'      => $appt->full_address,
                'street'            => $appt->street,
                'city'              => $appt->city,
                'postcode'          => $appt->postcode,

                // Meta Data
                'customer_name'     => $custName,
                'employees'         => $employees,
                'status'            => $appt->status
            ]);
        }
    }

    return response()->json([
        'success' => true, 
        'data'    => $events
    ]);
}

 public function pickerEmployees(Request $request)
    {
        $q = trim((string) $request->get('search', ''));

        $employees = Employee::query()
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('lastname', 'like', "%{$q}%");
                });
            })
            ->select('id', 'name', 'lastname', 'image')
            ->orderBy('lastname')
            ->orderBy('name')
            ->limit(300)
            ->get();

        return response()->json(['data' => $employees]);
    }

    /**
     * GET /picker/teams
     * ?search= (optional)
     * Returns: { data: [ {id, name} ] }
     */
    public function pickerTeams(Request $request)
    {
        $q = trim((string) $request->get('search', ''));

        $teams = Team::query()
            ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $teams]);
    }

    /**
     * GET /picker/teams/{team}
     * Returns: { id, name, members: [ {id, name, lastname, image, position} ] }
     *
     * Includes leader, members, and reserves (deduped). Position label is:
     *  - leader.position->position or "Leitung"
     *  - member.position->position
     *  - reserve.position->position or "Reserve"
     */
    public function pickerTeam(Team $team)
    {
        $team->load([
            'leader.employee:id,name,lastname,image',
            'leader.position:id,position',
            'members.employee:id,name,lastname,image',
            'members.position:id,position',
            'reserves.employee:id,name,lastname,image',
            'reserves.position:id,position',
        ]);

        $collect = collect();

        // Leader (optional)
        if ($team->leader && $team->leader->employee) {
            $collect->push([
                'id'       => $team->leader->employee_id,
                'name'     => $team->leader->employee->name,
                'lastname' => $team->leader->employee->lastname,
                'image'    => $team->leader->employee->image,
                'position' => optional($team->leader->position)->position ?? 'Leitung',
            ]);
        }

        // Members
        foreach ($team->members as $m) {
            if (!$m->employee) continue;
            $collect->push([
                'id'       => $m->employee_id,
                'name'     => $m->employee->name,
                'lastname' => $m->employee->lastname,
                'image'    => $m->employee->image,
                'position' => optional($m->position)->position,
            ]);
        }

        // Reserves
        foreach ($team->reserves as $r) {
            if (!$r->employee) continue;
            $collect->push([
                'id'       => $r->employee_id,
                'name'     => $r->employee->name,
                'lastname' => $r->employee->lastname,
                'image'    => $r->employee->image,
                'position' => optional($r->position)->position ?? 'Reserve',
            ]);
        }

        $members = $collect->unique('id')->values();

        return response()->json([
            'id'      => $team->id,
            'name'    => $team->name,
            'members' => $members,
        ]);
    }


public function searchSuggest(Request $request)
{
    $q = trim((string)$request->get('q', ''));
    $employeeIds = $request->get('employee_ids', []);

    if (!empty($employeeIds) && !is_array($employeeIds)) {
        $employeeIds = [$employeeIds];
    }
    $employeeIds = array_filter($employeeIds);

    if ($q === '') {
        return response()->json(['results' => []]);
    }

    // --- DATE PARSER ---
    $dateQuery = $q;
    if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $q, $matches)) {
        $dateQuery = sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
    }

    $limit = 10;

    // 1. Appointments Query (Added ->withTrashed() and deleted_at to selectRaw)
    $apptsQuery = \App\Models\MainAppointment::withTrashed()
        ->selectRaw('
            id, name as label, start_date as date, "appointment" as type, deleted_at,
            (SELECT GROUP_CONCAT(employee_id) FROM main_appointment_employees WHERE appointment_id = main_appointments.id) as emp_ids
        ')
        ->where(function($qq) use ($q, $dateQuery) {
            $qq->where('name', 'like', "%{$q}%")
               ->orWhere('city', 'like', "%{$q}%")
               ->orWhere('street', 'like', "%{$q}%")
               ->orWhere('postcode', 'like', "%{$q}%")
               ->orWhere('full_address', 'like', "%{$q}%")
               ->orWhere('appointment_type', 'like', "%{$q}%")
               ->orWhere('start_date', 'like', "%{$dateQuery}%")
               ->orWhere('end_date', 'like', "%{$dateQuery}%")
               ->orWhereHas('employees', function ($empQuery) use ($q) {
                   $empQuery->where(\DB::raw("CONCAT(name, ' ', lastname)"), 'like', "%{$q}%")
                            ->orWhere('name', 'like', "%{$q}%")
                            ->orWhere('lastname', 'like', "%{$q}%");
               });
        });

    // 2. Personal tasks Query (Removed whereNull('deleted_at') and added deleted_at to selectRaw)
    $tasksQuery = DB::table('personal_tasks')
        ->selectRaw('
            id, task_title as label, due_date as date, "task" as type, deleted_at,
            (SELECT GROUP_CONCAT(employee_id) FROM employees_personal_tasks WHERE task_id = personal_tasks.id) as emp_ids
        ')
        ->where(function($qq) use ($q, $dateQuery) {
            $qq->where('task_title', 'like', "%{$q}%")
               ->orWhere('description', 'like', "%{$q}%")
               ->orWhere('due_date', 'like', "%{$dateQuery}%")
               ->orWhereExists(function ($sub) use ($q) {
                   $sub->select(DB::raw(1))
                       ->from('employees_personal_tasks')
                       ->join('employees', 'employees.id', '=', 'employees_personal_tasks.employee_id')
                       ->whereColumn('employees_personal_tasks.task_id', 'personal_tasks.id')
                       ->where(function($empQ) use ($q) {
                           $empQ->where(\DB::raw("CONCAT(employees.name, ' ', employees.lastname)"), 'like', "%{$q}%")
                                ->orWhere('employees.name', 'like', "%{$q}%")
                                ->orWhere('employees.lastname', 'like', "%{$q}%");
                       });
               });
        });

    // 3. APPLY ACTIVE EMPLOYEE FILTER FROM SIDEBAR
    if (!empty($employeeIds)) {
        $apptsQuery->whereHas('employees', function ($query) use ($employeeIds) {
            $query->whereIn('main_appointment_employees.employee_id', $employeeIds);
        });

        $tasksQuery->whereExists(function ($query) use ($employeeIds) {
            $query->select(DB::raw(1))
                  ->from('employees_personal_tasks')
                  ->whereColumn('employees_personal_tasks.task_id', 'personal_tasks.id')
                  ->whereIn('employees_personal_tasks.employee_id', $employeeIds);
        }); 
    }

    $appts = $apptsQuery->limit($limit)->get();
    $tasks = $tasksQuery->limit($limit)->get();

    // 4. Employees Query
    $empsQuery = DB::table('employees')
        ->selectRaw('id, CONCAT(name," ",lastname) as label, NULL as date, "employee" as type, NULL as deleted_at, id as emp_ids')
        ->whereNull('deleted_at')
        ->where(function($qq) use ($q) {
            $qq->where(\DB::raw("CONCAT(name, ' ', lastname)"), 'like', "%{$q}%")
               ->orWhere('name', 'like', "%{$q}%")
               ->orWhere('lastname', 'like', "%{$q}%");
        });

    if (!empty($employeeIds)) {
        $empsQuery->whereIn('id', $employeeIds);
    }
    
    $emps = $empsQuery->limit($limit)->get();

    // 5. Cities Query
    $citiesQuery = DB::table('main_appointments')
        ->selectRaw('NULL as id, city as label, MIN(start_date) as date, "city" as type, NULL as deleted_at, NULL as emp_ids')
        ->whereNull('deleted_at')
        ->whereNotNull('city')
        ->where('city', 'like', "%{$q}%");

    if (!empty($employeeIds)) {
        $citiesQuery->whereExists(function ($query) use ($employeeIds) {
            $query->select(DB::raw(1))
                  ->from('main_appointment_employees')
                  ->whereColumn('main_appointment_employees.appointment_id', 'main_appointments.id')
                  ->whereIn('main_appointment_employees.employee_id', $employeeIds);
        });
    }

    $cities = $citiesQuery->groupBy('city')->limit($limit)->get();

    // Merge & Output
    $results = collect()->merge($appts)->merge($tasks)->merge($emps)->merge($cities)
        ->map(function($r) {
            return [
                'id'           => (string)($r->id ?? uniqid("x_")),
                'label'        => $r->label,
                'type'         => $r->type,
                'date'         => empty($r->date) ? null : \Carbon\Carbon::parse($r->date)->format('d.m.Y'),
                'employee_ids' => !empty($r->emp_ids) ? explode(',', $r->emp_ids) : [],
                'is_deleted'   => !empty($r->deleted_at) // <-- Boolean flag for Javascript
            ];
        })
        ->values();

    return response()->json(['results' => $results]);
}

    protected function buildStatsForEmployee($employee): array
    {
        $employeeId = is_object($employee)
            ? ($employee->id ?? null)
            : $employee;

        $employeeId = (int) $employeeId;

        if (!$employeeId) {
            return [
                'total' => 0,
                'open' => 0,
                'in_progress' => 0,
                'done' => 0,
                'overdue' => 0,
                'today' => 0,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Base query for personal tasks
        |--------------------------------------------------------------------------
        | personal_tasks does NOT have employee_id.
        |
        | Employee relation is:
        | employees_personal_tasks.task_id     = personal_tasks.id
        | employees_personal_tasks.employee_id = employees.id
        |--------------------------------------------------------------------------
        */
        $query = PersonalTask::query()
            ->whereNull('personal_tasks.deleted_at')
            ->where(function ($q) use ($employeeId) {
                $q->where('personal_tasks.assigned_by', $employeeId)
                    ->orWhereExists(function ($sub) use ($employeeId) {
                        $sub->selectRaw('1')
                            ->from('employees_personal_tasks')
                            ->whereColumn('employees_personal_tasks.task_id', 'personal_tasks.id')
                            ->where('employees_personal_tasks.employee_id', $employeeId);
                    });
            });

        $today = now()->toDateString();

        $doneStatuses = [
            'completed',
            'done',
            'closed',
        ];

        return [
            'total' => (clone $query)->count(),

            'open' => (clone $query)
                ->whereIn('personal_tasks.task_status', [
                    'open',
                    'new',
                    'pending',
                    'start',
                ])
                ->count(),

            'in_progress' => (clone $query)
                ->whereIn('personal_tasks.task_status', [
                    'on_progress',
                    'in_progress',
                    'processing',
                    'on_going',
                    'on_review',
                ])
                ->count(),

            'done' => (clone $query)
                ->whereIn('personal_tasks.task_status', $doneStatuses)
                ->count(),

            'overdue' => (clone $query)
                ->whereNotNull('personal_tasks.due_date')
                ->whereDate('personal_tasks.due_date', '<', $today)
                ->whereNotIn('personal_tasks.task_status', $doneStatuses)
                ->count(),

            'today' => (clone $query)
                ->whereDate('personal_tasks.due_date', $today)
                ->count(),
        ];
    }
public function MiniCalendar(Request $request)
{
    $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
    $endDate   = $request->input('end_date')   ? Carbon::parse($request->input('end_date'))->endOfDay()   : null;

    $employeeData = json_decode($request->input('employee_data'), true) ?? [];

    if (empty($employeeData)) {
        $employeeData[] = [
            'employee_id'       => auth()->user()->name, // FIX: use ID not name
            'tasks_only'        => 0,
            'appointments_only' => 1,
        ];
    }

    $allEvents = collect();

    foreach ($employeeData as $employee) {
        $employeeId = $employee['employee_id'] ?? null;
        if (!$employeeId) continue;

        $tasksOnly        = (int) ($employee['tasks_only'] ?? 0);
        $appointmentsOnly = (int) ($employee['appointments_only'] ?? 0);

        /* ========== PERSONAL TASKS ========== */
        if ($tasksOnly) {
            $tasks = DB::table('employees_personal_tasks')
                ->leftJoin('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
                ->leftJoin('employees as emp', 'emp.id', '=', 'employees_personal_tasks.employee_id')
                ->selectRaw("
                    personal_tasks.id,
                    personal_tasks.task_title as title,
                    emp.color as backgroundColor,
                    personal_tasks.priority,
                    personal_tasks.task_status,
                    personal_tasks.public,
                    personal_tasks.description,
                    personal_tasks.due_date as start_date,
                    personal_tasks.due_date as end_date,
                    personal_tasks.due_time as start_time,
                    personal_tasks.due_time as end_time,
                    employees_personal_tasks.id as emp_personal_id,
                    emp.id as employee_id,
                    emp.name, emp.lastname, emp.image, emp.gender,
                    NULL as customer_id,
                    'task' as type,
                    NULL as phone, NULL as email, NULL as city, NULL as postcode,
                    NULL as street, NULL as full_address, NULL as contact_id,
                    NULL as contact_type, NULL as appointment_type
                ")
                ->where('emp.id', $employeeId)
                ->whereNull('personal_tasks.deleted_at')
                ->when($startDate && $endDate, fn($q) => $q->whereBetween('personal_tasks.due_date', [$startDate, $endDate]))
                ->get();

            $allEvents = $allEvents->merge($tasks);
        }

        /* ========== APPOINTMENTS + LEAVES + SICKS + RECURRING ========== */
        if ($appointmentsOnly) {
            // Appointments
                $appointments = DB::table('main_appointment_employees')
                    ->leftJoin('main_appointments', 'main_appointments.id', '=', 'main_appointment_employees.appointment_id')
                    ->leftJoin('employees as emp', 'emp.id', '=', 'main_appointment_employees.employee_id')
                    ->leftJoin('employees as creator', 'creator.id', '=', 'main_appointments.created_by')
                ->selectRaw("
                    main_appointments.id,
                    main_appointments.name as title,
                    emp.color as backgroundColor,
                    main_appointments.priority,
                    main_appointments.status as task_status,
                    main_appointments.public,
                    main_appointments.note as description,
                    main_appointments.start_date,
                    main_appointments.end_date,
                    COALESCE(main_appointments.start_time, '00:00:00') as start_time,
                    COALESCE(main_appointments.end_time, '23:59:59') as end_time,
                    emp.id as employee_id,
                    emp.name, emp.lastname, emp.image, emp.gender,
                    NULL as emp_personal_id,
                    main_appointments.customer_id,
                    main_appointments.phone,
                    main_appointments.email,
                    main_appointments.full_address,
                    main_appointments.street,
                    main_appointments.postcode,
                    main_appointments.city,
                    main_appointments.latitude,
                    main_appointments.longitude,
                    main_appointments.contact_id,
                    main_appointments.contact_type,
                    main_appointments.appointment_type,
                    'appointment' as type
                ")
                ->where('emp.id', $employeeId)
                ->whereNull('main_appointments.deleted_at')
                ->when($startDate && $endDate, fn($q) => $q->whereBetween('main_appointments.start_date', [$startDate, $endDate]))
                ->get();
            $allEvents = $allEvents->merge($appointments);

            // Holidays
            $holidays = DB::table('leaves')
                ->leftJoin('employees as emp', 'emp.id', '=', 'leaves.emp_id')
                ->selectRaw("
                    leaves.id,
                    'Urlaub' as title,
                    leaves.description,
                    emp.color as backgroundColor,
                    leaves.status as task_status,
                    leaves.start_date,
                    leaves.end_date,
                    '00:00:00' as start_time,
                    '23:59:59' as end_time,
                    emp.id as employee_id,
                    emp.name, emp.lastname, emp.image, emp.gender,
                    NULL as emp_personal_id, NULL as public, NULL as priority,
                    NULL as customer_id, NULL as phone, NULL as email,
                    NULL as full_address, NULL as street, NULL as city, NULL as postcode,
                    NULL as contact_id, NULL as contact_type, NULL as appointment_type,
                    'holiday' as type
                ")
                ->where('leaves.emp_id', $employeeId)
                ->where('leaves.approved', 'Yes')
                ->when($startDate && $endDate, fn($q) => $q->whereBetween('leaves.start_date', [$startDate, $endDate]))
                ->get();
            $allEvents = $allEvents->merge($holidays);

            // Sicks
            $sicks = DB::table('employee_sicks')
                ->leftJoin('employees as emp', 'emp.id', '=', 'employee_sicks.emp_id')
                ->selectRaw("
                    employee_sicks.id,
                    'Krank' as title,
                    employee_sicks.status_msg as description,
                    emp.color as backgroundColor,
                    employee_sicks.start_date,
                    employee_sicks.end_date,
                    '00:00:00' as start_time,
                    '23:59:59' as end_time,
                    emp.id as employee_id,
                    emp.name, emp.lastname, emp.image, emp.gender,
                    NULL as task_status, NULL as emp_personal_id, NULL as public,
                    NULL as priority, NULL as customer_id, NULL as phone,
                    NULL as email, NULL as full_address, NULL as street,
                    NULL as city, NULL as postcode, NULL as contact_id,
                    NULL as contact_type, NULL as appointment_type,
                    'sick' as type
                ")
                ->where('employee_sicks.emp_id', $employeeId)
                ->when($startDate && $endDate, fn($q) => $q->whereBetween('employee_sicks.start_date', [$startDate, $endDate]))
                ->get();
            $allEvents = $allEvents->merge($sicks);

            // Recurring Leaves
            $recurringLeaves = \App\Models\EmployeeRecurringLeave::where('employee_id', $employeeId)
                ->where('is_active', 1)
                ->get();

            $emp = \App\Models\Employee::find($employeeId);

            foreach ($recurringLeaves as $leave) {
                $from = $startDate ?? Carbon::now()->startOfMonth();
                $to   = $endDate   ?? Carbon::now()->addMonths(3)->endOfMonth();

                foreach ($leave->generateOccurrences($from, $to) as $occ) {
                    $allEvents->push((object)[
                        'id'          => 'recurring-'.$leave->id.'-'.$occ['date'],
                        'title'       => $occ['title'],
                        'description' => $occ['description'],
                        'backgroundColor' => $emp?->color ?? '#6c757d',
                        'start_date'  => $occ['date'],
                        'end_date'    => $occ['date'],
                        'start_time'  => $occ['all_day'] ? '00:00:00' : ($occ['start_time'] ?? '00:00:00'),
                        'end_time'    => $occ['all_day'] ? '23:59:59' : ($occ['end_time'] ?? '23:59:59'),
                        'employee_id' => $employeeId,
                        'name'        => $emp?->name,
                        'lastname'    => $emp?->lastname,
                        'image'       => $emp?->image,
                        'gender'      => $emp?->gender,
                        'type'        => 'recurring_leave',
                    ]);
                }
            }
        }
    }

    /* ========== PUBLIC HOLIDAYS ========== */
    $publicHolidays = DB::table('public_holidays')
        ->selectRaw("
            id,
            name as title,
            NULL as description,
            '#FF5733' as backgroundColor,
            NULL as task_status,
            start_date,
            end_date,
            '00:00:00' as start_time,
            '23:59:59' as end_time,
            NULL as employee_id,
            NULL as name,
            NULL as lastname,
            NULL as image,
            NULL as gender,
            NULL as emp_personal_id,
            NULL as public,
            NULL as priority,
            NULL as customer_id,
            NULL as phone,
            NULL as email,
            NULL as full_address,
            NULL as street,
            city,
            NULL as postcode,
            NULL as contact_id,
            NULL as contact_type,
            NULL as appointment_type,
            'public_holiday' as type
        ")
        ->when($startDate && $endDate, fn($q) => $q->whereBetween('start_date', [$startDate, $endDate]))
        ->get();
    $allEvents = $allEvents->merge($publicHolidays);

    /* ========== GROUPING ========== */
    $groupedEvents = $allEvents->groupBy(fn($e) => "{$e->id}-{$e->start_date}-{$e->start_time}")
        ->map(function ($group) {
            $e = $group->first();
            return [
                'id'             => $e->id,
                'emp_personal_id'=> $e->emp_personal_id ?? null,
                'customer_id'    => $e->customer_id ?? null,
                'title'          => $e->title,
                'start_date'     => $e->start_date,
                'end_date'       => $e->end_date,
                'start_time'     => $e->start_time,
                'end_time'       => $e->end_time,
                'description'    => $e->description,
                'status'         => $e->task_status ?? null,
                'taskColor'      => $e->backgroundColor,
                'priority'       => $e->priority ?? null,
                'public_view'    => $e->public ?? null,
                'type'           => $e->type ?? null,
                'full_address'   => $e->full_address ?? null,
                'street'         => $e->street ?? null,
                'postcode'       => $e->postcode ?? null,
                'city'           => $e->city ?? null,
                'phone'          => $e->phone ?? null,
                'email'          => $e->email ?? null,
                'contact_id'     => $e->contact_id ?? null,
                'contact_type'   => $e->contact_type ?? null,
                'appointment_type'=> $e->appointment_type ?? null,
                'employees'      => $group->map(fn($i) => [
                    'employee_id' => $i->employee_id,
                    'name'        => $i->name,
                    'lastname'    => $i->lastname,
                    'image'       => $i->image,
                    'gender'      => $i->gender,
                ])->unique('employee_id')->values(),
            ];
        });

    return response()->json(['data' => $groupedEvents->values()]);
}


private function formatEventResponse($event, $employees)
{
    return [
        'id'            => $event->id,
        'title'         => $event->title,
        'start_date'    => $event->start_date,
        'end_date'      => $event->end_date, 
        'end_time'      => $event->end_time,
        'status'        => $event->task_status,
        'taskColor'     => $event->backgroundColor,
        'priority'      => $event->priority,
        'public_view'   => $event->public,
        'type'          => $event->type,
        'employees'     => $employees->unique('employee_id')->values(),
    ];
} 
public function accept_request(Request $request)
{
    $request->validate([
        'task_id'     => 'required|exists:personal_tasks,id',
        'employee_id' => 'required|exists:employees,id',
        'response'    => 'required|in:accepted,reject',
        'reason'      => 'nullable|string|max:500',
    ]);

    $updateStatus = EmployeesPersonalTask::where('employee_id', $request->employee_id)
        ->where('task_id', $request->task_id)
        ->update([
            'status' => $request->response,
            'reason' => $request->reason,
        ]);

    $emp = DB::table('employees')
        ->select('name', 'lastname', 'id')
        ->where('id', auth()->user()->name)
        ->first();

    $name      = $emp->name.' '.$emp->lastname;
    $taskLabel = $this->formatTaskLabel($request->task_id);

    $verb = $request->response === 'accepted'
        ? 'angenommen'
        : 'abgelehnt';

    Notification::send(auth()->user(), new PersonalTaskNotification([
        'title'   => 'Stellenanfrage',
        'message' => 'Die Aufgabe '.$taskLabel.' wurde von '.$name.' '.$verb.'.',
        'task_id' => $request->task_id,
    ]));

    if ($updateStatus) {
        return response()->json(['message' => 'The data was sent successfully.'], 200);
    } else {
        return response()->json(['error' => 'Failed to update the task assignment.'], 500);
    }
}


public function add_employee(Request $request)
{
    Log::info('Incoming Request:', $request->all());

    DB::beginTransaction();

    try {
        // Validate incoming request
        $request->validate([
            'task_id'     => 'required|exists:employees_personal_tasks,task_id',
            'employee_id' => 'required|exists:employees,id',
            'old_employee' => 'required|exists:employees_personal_tasks,employee_id',
        ]);

        // Check if the task with the old employee exists
        $task = EmployeesPersonalTask::where('task_id', $request->task_id)
            ->where('employee_id', $request->old_employee)
            ->first();

        if (!$task) {
            throw new \Exception('Task or employee assignment not found.');
        }

        // Update task assignment for the specific task and employee
        $task->update([
            'employee_id' => $request->employee_id,
            'status'      => 'send', // Set the status to "send"
        ]);

        // Fetch the new employee's name for the notification
        // Send Notification
        $emp = DB::table('employees')
            ->select('name', 'lastname')
            ->where('id', $request->employee_id)
            ->first();

        $name      = $emp->name . ' ' . $emp->lastname;
        $taskLabel = $this->formatTaskLabel($request->task_id);

        Notification::send(auth()->user(), new PersonalTaskNotification([
            'title'   => 'Neuer Mitarbeiter hinzugefügt',
            'message' => $name.' wurde der Aufgabe '.$taskLabel.' hinzugefügt.',
            'task_id' => $request->task_id,
        ]));


        DB::commit();

        Log::info('Task Assignment Updated:', [
            'task_id'     => $request->task_id,
            'old_employee' => $request->old_employee,
            'new_employee' => $request->employee_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Neuer Mitarbeiter erfolgreich hinzugefügt'], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Exception Occurred:', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => 'Ein unerwarteter Fehler ist aufgetreten: ' . $e->getMessage()], 500);
    }
}


public function AddNewEmployee(Request $request)
{
    Log::info('Incoming Request:', $request->all());

    DB::beginTransaction();

    try {
        // Validate incoming request
        $request->validate([
            'task_id'     => 'required|',
            'employee_id' => 'required|exists:employees,id', 
        ]);

        // Insert new task assignment into the database
        DB::table('employees_personal_tasks')->insert([
            'task_id'     => $request->task_id,
            'employee_id' => $request->employee_id,
            'status'      => 'send', // Set the status to "send"
            'created_at'  => now(),  // Optional: Add timestamp
            'updated_at'  => now(),
        ]);

        // Fetch the new employee's name for the notification
        $emp = DB::table('employees')
            ->select('name', 'lastname')
            ->where('id', $request->employee_id)
            ->first();

        if ($emp) {
                $name      = $emp->name . ' ' . $emp->lastname;
                $taskLabel = $this->formatTaskLabel($request->task_id);

                Notification::send(auth()->user(), new PersonalTaskNotification([
                    'title'   => 'Neuer Mitarbeiter hinzugefügt',
                    'message' => $name.' wurde der Aufgabe '.$taskLabel.' hinzugefügt.',
                    'task_id' => $request->task_id,
                ]));
            }


        DB::commit();

        return response()->json(['success' => true, 'message' => 'Neuer Mitarbeiter erfolgreich hinzugefügt'], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Exception Occurred:', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => 'Ein unerwarteter Fehler ist aufgetreten: ' . $e->getMessage()], 500);
    }
}


public function deleteEmployee(Request $request)
{
    $appointmentId = $request->id;

    // Fetch records with the same_id
    $data = DB::table('employees_personal_tasks')->where('id', $appointmentId)->get();

    \Log::info('Data received for deletion:', [$data]);

    if ($data->isNotEmpty()) {
        // Delete all records with the matching same_id
        DB::table('employees_personal_tasks')->where('id', $appointmentId)->delete();

        return response()->json(['message' => 'Mitarbeiter erfolgreich gelöscht.'], 200);
    }

    return response()->json(['message' => 'Mitarbeiter nicht gefunden.'], 404);
}



public function main_task(Request $request)
{
    $validate = $request->validate([
        'id'            => 'required|exists:personal_task_keys,id',
        'task_id'       => 'required|exists:personal_tasks,id',
        'done_status'   => 'required',
        'work_progress' => 'nullable|integer',
        'submit_time' => 'required|numeric|min:0',
        'reason'        => 'nullable|string',
    ], [
        'submit_time.required' => 'Bitte wählen Sie die Uhrzeit',
    ]);

    Log::info('Incoming Request:', $request->all());

    $statusMapping = [
        'complete'   => 1,
        'part'       => 2,
        'imposible'  => 3,
        'unable'     => 4,
    ];
    $is_completed = $statusMapping[$request->done_status] ?? 4;

    try {
        DB::transaction(function () use ($request, $is_completed) {
            $task = PersonalTaskKey::findOrFail($request->id);

            // Calculate total_time  
            $task->update([
                'status'         => 'completed',
                'done_by'        => auth()->user()->name,
                'is_completed'   => $is_completed,
                'done_date'      => now(),
                'done_status'    => $is_completed,
                'reason'         => $request->reason,
                'work_progress'  => $request->work_progress,
                'submit_time'      => $request->submit_time, 
            ]);

            $personal = PersonalTask::findOrFail($request->task_id);
            $complete_status = PersonalTaskKey::where('personal_task_id', $request->task_id)
                                    ->select('is_completed', 'id', 'personal_task_id')
                                    ->get();


              $emp = DB::table('employees')
                    ->select('name', 'lastname', 'id')
                    ->where('id', auth()->user()->name)
                    ->first();

                $name = $emp->name.' '.$emp->lastname;

                $completeMapping = [
                    'complete'   => 'vollständig',
                    'part'       => 'teilweise',
                    'imposible'  => 'nicht erledigt',
                    'unable'     => 'kann nicht erledigt werden',
                ];
                $complete   = $completeMapping[$request->done_status] ?? 'kann nicht erledigt werden';
                $taskLabel  = $this->formatTaskLabel($request->task_id);

                Notification::send(auth()->user(), new PersonalTaskNotification([
                    'title'   => 'Aufgabe geprüft',
                    'message' =>
                        'Die Aufgabe '.$taskLabel.' mit dem Status '.$complete.
                        ' wurde von '.$name.' mit einer Dauer von '.$request->more_time.' ausgeführt.'.
                        ($request->work_progress
                            ? ' Der Aufgabenfortschritt beträgt '.$request->work_progress.' %.'
                            : ''),
                    'task_id' => $request->task_id,
                ]));



            Log::info('Complete Task Status: ', [$complete_status]);
            $personal->update([
                'progress'    => 20,
                'task_status' => 'completed',
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Task is done']);
    } 
    catch (ModelNotFoundException $e) {
        Log::error('Task not found', ['task_id' => $request->id, 'error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Task not found'], 404);
    } catch (\Exception $e) {
        Log::error('Error updating task:', ['task_id' => $request->id, 'error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'An unexpected error occurred'], 500);
    }

}
 
public function main_task_uncheck(Request $request)
{
Log::info('Incoming Request:', $request->all());

// Determine if the reset checkbox was checked
$reset = $request->reset === 'on';

// Find the task by its ID
$task = PersonalTaskKey::find($request->id);

if ($task) {
    // Build the update data
    $updateData = [
        'status' => 'pending',
        'done_by' => auth()->user()->name,
        'is_completed' => 0,
        'done_date' => now(),
        'reason' => $request->reason,
    ];

    // If reset is true, add additional fields to reset
    if ($reset) {
        $updateData['work_progress'] = null;
        $updateData['more_time'] = null;
        $updateData['total_time'] = null;
    }

    // Update the task with the prepared data
    $task->update($updateData);

    // Fetch employee details 
    $emp = DB::table('employees')
        ->select('name', 'lastname', 'id')
        ->where('id', auth()->user()->name)
        ->first();

    $name      = $emp->name . ' ' . $emp->lastname;
    $taskLabel = $this->formatTaskLabel($task->personal_task_id);

    Notification::send(auth()->user(), new PersonalTaskNotification([
        'title'   => 'Ausgeführte Aufgabe wurde nicht markiert',
        'message' =>
            'Die Aufgabe '.$taskLabel.' wurde von '.$name.
            ($request->reason
                ? ' mit der Begründung nicht geprüft.'
                : ' ohne Grund nicht überprüft.'),
        'task_id' => $task->personal_task_id,
    ]));

    return response()->json(['success' => true, 'message' => 'Task is updated.']);
}

return response()->json(['success' => false, 'message' => 'Task not found'], 404);
}

public function project_status(Request $request)
{
    Log::info('Incoming project_status Request:', $request->all());

    $request->validate([
        'id'             => 'required|exists:personal_tasks,id',
        'project_status' => 'required|in:new,start,on_going,on_review,completed,pause,cancel',
    ]);

    $task = PersonalTask::find($request->id);
    if (!$task) {
        return response()->json(['success' => false, 'message' => 'Task not found'], 404);
    }

    $progressMapping = [
        'new'        => 0,
        'start'      => 20,
        'on_going'   => 40,
        'on_review'  => 80,
        'completed'  => 100,
        'pause'      => $task->progress ?? 0,
        'cancel'     => $task->progress ?? 0,
    ];

    $task->task_status = $request->project_status;
    $task->progress    = $progressMapping[$request->project_status] ?? 0;
    $task->save();

    $emp  = DB::table('employees')
        ->select('name', 'lastname', 'id')
        ->where('id', auth()->user()->name)
        ->first();

    $name        = $emp ? ($emp->name.' '.$emp->lastname) : 'Unbekannt';
    $progressStatus = [
        'new'        => 'Neu',
        'start'      => 'Gestartet',
        'on_going'   => 'Im Prozess',
        'on_review'  => 'Zur Prüfung',
        'completed'  => 'Abgeschlossen',
        'pause'      => 'Pausiert',
        'cancel'     => 'Storniert',
    ];

    $progressLabel = $progressStatus[$request->project_status] ?? $request->project_status;
    $taskLabel     = $this->formatTaskLabel($task->id);

    Notification::send(auth()->user(), new PersonalTaskNotification([
        'title'   => 'Aufgabenstatus geändert',
        'message' => 'Der Status der Aufgabe '.$taskLabel.' wurde von '.$name.' auf ['.$progressLabel.'] geändert.',
        'task_id' => $task->id,
    ]));
    return response()->json([
        'success'  => true,
        'message'  => 'Status erfolgreich aktualisiert.',
        'task_id'  => $task->id,
        'status'   => $task->task_status,
        'progress' => $task->progress,
    ]);
}



public function no_reminder(Request $request)
{
    $data = PersonalTask::find($request->id);
    if ($data) {
        $data->reminder_date = $request->reminder_date;
        $data->reminder_time = $request->reminder_time;
        $data->save();
        return response()->json(['success' => true, 'message' => 'Erinnerung wurde erfolgreich aktualisiert']);
    }

    return response()->json(['success' => false, 'message' => 'Aufgabe nicht gefunden'], 404);
}

public function no_repeat(Request $request)
{
    $data = PersonalTask::find($request->id);
    if ($data) {
        $data->repeat = $request->repeat_date;
        $data->save();
        return response()->json(['success' => true, 'message' => 'Wiederholen wurde erfolgreich aktualisiert']);
    }

    return response()->json(['success' => false, 'message' => 'Aufgabe nicht gefunden'], 404);
}
 
public function getTaskNotifications($task_id): JsonResponse
{
    // Ensure the task_id is cast to a string
    $taskId = (string) $task_id;

    // Fetch notifications where the task_id in the notification data matches the requested task_id
    $notifications = DatabaseNotification::where('data->task_id', $taskId)
        ->orderBy('created_at', 'desc')
        ->get();

    // Transform the notifications to a simplified JSON structure
    $transformedNotifications = $notifications->map(function ($notification) {
        return [
            'id' => $notification->id,
            'title' => $notification->data['title'] ?? 'Notification',
            'message' => $notification->data['message'] ?? '',
            'task_id' => $notification->data['task_id'] ?? null,
            'performed_at' => $notification->data['performed_at'] ?? $notification->created_at->toDateTimeString(),
        ];
    });

    // Return the notifications in JSON format
    return response()->json([
        'data' => $transformedNotifications,
    ]);
}


public function processRepeatingTasks()
{
    // Fetch tasks with valid `repeat` values
    $tasks = DB::table('personal_tasks')
        ->whereNotNull('repeat')
        ->whereIn('repeat', ['minute', 'hourly', 'daily', 'weekly', 'monthly', 'quarterly', 'yearly'])
        ->whereNull('deleted_at')
        ->get();

    $repeatedTaskDetails = [];

    foreach ($tasks as $task) {
        // Determine the next duplication time based on `repeat` value
        $nextTime = match ($task->repeat) {
            'minute' => now()->addMinute(),
            'hourly' => now()->addHour(),
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'yearly' => now()->addYear(),
            default => null
        };

        if ($nextTime) {
            // Duplicate the main task
            $newTaskId = DB::table('personal_tasks')->insertGetId([
                'task_title' => $task->task_title,
                'description' => $task->description,
                'priority' => $task->priority,
                'color' => $task->color,
                'repeat' => $task->repeat,
                'reminder_date' => $task->reminder_date,
                'reminder_time' => $task->reminder_time,
                'assigned_by' => $task->assigned_by,
                'progress' => 'new',
                'task_status' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Duplicate related employees_personal_tasks records
            $employeeTasks = DB::table('employees_personal_tasks')
                ->where('task_id', $task->id)
                ->get();

            foreach ($employeeTasks as $employeeTask) {
                DB::table('employees_personal_tasks')->insert([
                    'employee_id' => $employeeTask->employee_id,
                    'task_id' => $newTaskId,
                    'same_id' => $employeeTask->same_id,
                    'status' => $employeeTask->status,
                    'reason' => $employeeTask->reason,
                    'start_date' => $employeeTask->start_date,
                    'end_date' => $employeeTask->end_date,
                    'start_time' => $employeeTask->start_time,
                    'end_time' => $employeeTask->end_time,
                    'total_time' => $employeeTask->total_time,
                    'note' => $employeeTask->note,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Duplicate related personal_task_keys records
            $taskKeys = DB::table('personal_task_keys')
                ->where('personal_task_id', $task->id)
                ->get();

            foreach ($taskKeys as $taskKey) {
                DB::table('personal_task_keys')->insert([
                    'personal_task_id' => $newTaskId,
                    'same_id' => $taskKey->same_id,
                    'task' => $taskKey->task,
                    'status' => $taskKey->status,
                    'is_completed' => $taskKey->is_completed,
                    'done_by' => $taskKey->done_by,
                    'done_date' => $taskKey->done_date,
                    'done_status' => $taskKey->done_status,
                    'work_progress' => $taskKey->work_progress,
                    'more_time' => $taskKey->more_time,
                    'total_time' => $taskKey->total_time,
                    'reason' => $taskKey->reason,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Duplicate related personal_sub_tasks records
            $subTasks = DB::table('personal_sub_tasks')
                ->where('task_id', $task->id)
                ->get();

            foreach ($subTasks as $subTask) {
                DB::table('personal_sub_tasks')->insert([
                    'task_id' => $newTaskId,
                    'sub_task_title' => $subTask->sub_task_title,
                    'description' => $subTask->description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $repeatedTaskDetails[] = [
                'id' => $task->id,
                'task_title' => $task->task_title,
                'next_repeat_time' => $nextTime->format('Y-m-d H:i:s'),
            ];
        }
    }

    return response()->json([
        'duplicated_tasks' => count($repeatedTaskDetails),
        'repeated_task_details' => $repeatedTaskDetails,
    ]);
}

public function stopRepeatingTasks(Request $request)
{
    $taskIds = $request->input('task_ids'); // Get task IDs from the request

    if (empty($taskIds)) {
        return response()->json(['message' => 'No tasks selected.'], 400);
    }

    DB::table('personal_tasks')
        ->whereIn('id', $taskIds)
        ->update(['repeat' => null]);

    return response()->json(['message' => 'Repeats stopped for selected tasks.']);
}


//Changeing the appointment date
public function change_appointment(Request $request)
{
    \Log::info('Change appointment request:', $request->all());

    $validatedData = $request->validate([
        'task_id' => 'required|integer',
        'emp_personal_id' => 'nullable',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i',
        'change_reason' => 'required|string',
        'type' => 'required|string',
    ]);

    try {
        $task_id = $request->task_id;
        $emp_personal_id = $request->emp_personal_id;
        $same_id = $request->same_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $start_time = $request->start_time;
        $end_time = $request->end_time;
        $type = $request->type;

        \Log::info("Updated Dates & Times: Start: $start_date $start_time, End: $end_date $end_time, Type: $type");

        if ($type === "task") {
            // Check if task exists
            $taskExists = DB::table('employees_personal_tasks')
                ->where('id', $emp_personal_id)
                ->exists();

            if (!$taskExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'The task with the given emp_personal_id does not exist.',
                ]);
            }

            // Update the task with new date and time
            $rowsAffected = DB::table('employees_personal_tasks')
                ->where('id', $emp_personal_id)
                ->update([
                    'start_date'    => $start_date,
                    'end_date'      => $end_date,
                    'start_time'    => $start_time,
                    'end_time'      => $end_time,
                    'change_date'   => now(),
                    'changed_by'    => auth()->user()->name,
                    'change_reason' => $request->change_reason,
                ]);

            // Update all related tasks with same_id
            if ($same_id) {
                DB::table('employees_personal_tasks')
                    ->where('same_id', $same_id)
                    ->update([
                        'start_date'    => $start_date,
                        'end_date'      => $end_date,
                        'start_time'    => $start_time,
                        'end_time'      => $end_time,
                        'change_date'   => now(),
                        'changed_by'    => auth()->user()->name,
                        'change_reason' => $request->change_reason,
                    ]);
            }

            $emp = DB::table('employees')
                ->select('name', 'lastname', 'id')
                ->where('id', auth()->user()->name)
                ->first();

            $name        = $emp->name.' '.$emp->lastname;
            $taskKey     = DB::table('personal_task_keys')->where('id', $request->task_id)->first();
            $parentLabel = $this->formatTaskLabel($taskKey->personal_task_id ?? $request->task_id);

            Notification::send(auth()->user(), new PersonalTaskNotification([
                'title'   => 'Aufgabenplanzeit geändert',
                'message' =>
                    'Die Planzeit für die Aufgabe '.$parentLabel.
                    ' (Schritt ['.$taskKey->task.']) wurde von '.$name.
                    ' auf '.$request->end_time.' geändert und das ist der Grund ['.$request->change_reason.']',
                'task_id' => $taskKey->personal_task_id ?? $request->task_id,
            ]));


        } else {
            // Find and update appointment
            $appointmentExists = DB::table('main_appointments')
                ->where('id', $task_id)
                ->exists();

            if (!$appointmentExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'The appointment with the given ID does not exist.',
                ]);
            }
                // Correctly concatenate the old date and time
                $old_date = $request->start_date.' - '.$request->start_time.' - '. $request->end_date.' - '.$request->end_time;

                // Update appointment with new date and time
                $rowsAffected = DB::table('main_appointments')
                    ->where('id', $task_id)
                    ->update([
                        'start_date'    => $start_date,
                        'end_date'      => $end_date,
                        'start_time'    => $start_time,
                        'end_time'      => $end_time,
                        'change_date'   => now(),
                        'changed_by'    => auth()->user()->name,
                        'change_reason' => $request->change_reason,
                    ]);

                // Correctly fetch the employee using `auth()->user()->id`
                $emp = DB::table('employees')
                    ->select('name', 'lastname')
                    ->where('id', auth()->user()->name) // Fetch by ID instead of name
                    ->first();

                // Ensure the employee exists before accessing properties
                $name = $emp ? $emp->name . ' ' . $emp->lastname : auth()->user()->name;

                // Send Notification
                Notification::send(auth()->user(), new AppointmentNotification([
                    'title'   => 'Aufgabenplanzeit geändert',
                    'message' => 'Das Ereignisdatum wurde von ' . $name . ' vom ' . $old_date . ' geändert und das ist der Grund ['.$request->change_reason.']',
                    'appointment_id' => $task_id, // Ensure this variable exists
                ]));

        }

        if ($rowsAffected > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment updated successfully.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No rows were updated. Please check the task ID and same ID.',
            ]);
        }
    } catch (\Exception $e) {
        \Log::error('Error updating appointment:', ['message' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while updating the appointment.',
            'error'   => $e->getMessage(),
        ]);
    }
}
  
public function getInfo($id, $type) {
    if ($type == 'task') {
        $data = DB::table('employees_personal_tasks')
                    ->join('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
                    ->where('employees_personal_tasks.task_id', $id)
                    ->select('task_title as title', 'description', 'employees_personal_tasks.start_date', 'employees_personal_tasks.end_date', 'employees_personal_tasks.start_time', 'employees_personal_tasks.end_time')
                    ->first();
    } else {
        $data = DB::table('main_appointments')->where('id', $id)
                    ->select('note as description', 'name as title', 'execution_type', 'start_date', 'end_date', 'start_time', 'end_time')
                    ->first();
    }

    if ($data) {
        return response()->json([
            'success' => true,
            'title' => $data->title ?? 'No Title',
            'description' => $data->description ?? 'No Description',
            'execution_type' => $data->execution_type ?? 'N/A',
            'start_date' => $data->start_date ?? 'N/A',
            'end_date' => $data->end_date ?? 'N/A',
            'start_time' => $data->start_time ?? 'N/A',
            'end_time' => $data->end_time ?? 'N/A',
        ]);
    } else {
        return response()->json(['success' => false, 'message' => 'No details found']);
    }
}
 
public function dueDateUpdate(Request $request)
{
    // Validate request
    $validate = $request->validate([
        'id'        => 'required|exists:personal_tasks,id',
        'due_date'  => 'nullable|date',
        'due_time'  => 'nullable|date_format:H:i'
    ]);

    // Fetch the existing task
    $task = PersonalTask::find($request->id);
    if (!$task) {
        return response()->json(['error' => 'Task not found'], 404);
    }

    // Use the old values if new values are not provided
    $startDate = $task->start_date;  // Use start_date instead of start_time
    $dueDate = $request->due_date ?? $task->due_date;
    $dueTime = $request->due_time ?? $task->due_time;

    // Calculate total time and total days
    $timeDifference = $this->calculateTimeDifference($startDate, $dueDate, $dueTime);

    // Update the task
    $task->update([
        'due_date'   => $dueDate,
        'due_time'   => $dueTime,
        'total_time' => $timeDifference['totalHours'],
        'total_day'  => $timeDifference['totalDays']
    ]);

    return response()->json([
        'success'    => 'The date and time are saved successfully',
        'total_time' => $timeDifference['totalHours'],
        'total_day'  => $timeDifference['totalDays']
    ]);
}

/**
 * Calculate the total time and total days between start_date and due_date/due_time
 */
private function calculateTimeDifference($startDate, $dueDate, $dueTime)
{
    if (!$startDate || !$dueDate) {
        return ['totalHours' => 0, 'totalDays' => 0];
    }

    // Convert to Carbon instances
    $start = \Carbon\Carbon::parse($startDate);
    $end = \Carbon\Carbon::parse($dueDate);

    // If there's a due time, adjust the end date
    if ($dueTime) {
        [$hour, $minute] = explode(':', $dueTime);
        $end->setTime($hour, $minute);
    }

    // Calculate the difference in hours and days
    $totalHours = $start->diffInHours($end);
    $totalDays = floor($totalHours / 24); // Convert to full days

    return ['totalHours' => $totalHours, 'totalDays' => $totalDays];
}
 
 public function ajaxList(Request $request)
{
    $statusLabels = [
        'lead'      => 'Lead',
        'offer'     => 'Angebot',
        'deal'      => 'Auftrag',
        'project'   => 'Montage',
        'completed' => 'Abgeschlossen',
        'junk'      => 'Junk',
        'open'      => 'Offen',
    ];

    $perPage = (int) ($request->get('per_page', 25));
    $page    = max(1, (int) $request->get('page', 1));

    $query = DB::table('lead_product_lists as lpl')
        ->join('new_leads as nl', 'nl.id', '=', 'lpl.customer_id')
        ->join('lead_alternative_adds as laa', 'laa.id', '=', 'lpl.alternative_id')
        ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
        ->leftJoin('departments as d', 'd.id', '=', 'lpl.department_id')
        ->leftJoin('employees as e', 'e.id', '=', 'lpl.employee_id')
        ->select(
            'lpl.id as lpl_id',
            'lpl.alternative_id',
            'lpl.product_id',
            'nl.id as customer_id',
            'nl.name as customer_name',
            'nl.lastname as customer_lastname',
            'laa.object_name',
            'ag.article_group',
            'd.department_name',
            'e.name as employee_name',
            'e.lastname as employee_lastname',
            'lpl.stage',
            'lpl.status'
        )
        ->orderByDesc('lpl.id');

    // ✅ Select2 sends "q"
    $term = trim((string) $request->get('q', ''));

    if ($term !== '') {
        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';

        $query->where(function ($q) use ($like) {
            $q->where('nl.name', 'like', $like)
              ->orWhere('nl.lastname', 'like', $like)
              ->orWhere('laa.object_name', 'like', $like)
              ->orWhere('ag.article_group', 'like', $like)
              ->orWhere('d.department_name', 'like', $like)
              ->orWhere('e.name', 'like', $like)
              ->orWhere('e.lastname', 'like', $like)
              ->orWhere('lpl.stage', 'like', $like)
              ->orWhere('lpl.status', 'like', $like);
        });
    }

    // ✅ paginate (Select2 infinite scroll)
    $total   = (clone $query)->count();
    $results = $query->forPage($page, $perPage)->get();

    $formatted = $results->map(function ($item) use ($statusLabels) {
        $status       = $statusLabels[$item->status] ?? ucfirst((string) $item->status);
        $customerName = trim("{$item->customer_name} {$item->customer_lastname}");
        $employeeName = trim("{$item->employee_name} {$item->employee_lastname}");

        $objectName   = $item->object_name ?? '';
        $articleGroup = $item->article_group ?? '';
        $deptName     = $item->department_name ?? '';

        $html = <<<HTML
<div style="line-height:1.2">
  <strong style="font-size:15px;">{$customerName}</strong><br>
  <span style="color:#555;"><i class="fa fa-home"></i> {$objectName}</span><br>
  <span style="font-size:12px;color:#777;">
    <i class="fa fa-tag"></i> {$articleGroup} &nbsp;|&nbsp;
    <i class="fa fa-cubes"></i> {$deptName} &nbsp;|&nbsp;
    <i class="fa fa-user"></i> {$employeeName} &nbsp;|&nbsp;
    <i class="fa fa-flag"></i> <strong>{$status}</strong>
  </span>
</div>
HTML;

        return [
            // keep your existing behavior:
            'id'             => $item->customer_id,
            'text'           => trim($customerName),
            'html'           => $html,
            'alternative_id' => $item->alternative_id,
            'product_id'     => $item->product_id,
        ];
    })->values();

    return response()->json([
        'results'     => $formatted,
        'pagination'  => ['more' => ($page * $perPage) < $total],
        'total'       => $total,
    ]);
}

public function updateStatus(Request $request, $id)
{
    $task = \App\Models\PersonalTask::findOrFail($id);
    $task->task_status = $request->status;
    $task->save();

    return response()->json(['success' => true, 'status' => $task->task_status]);
}


public function storeAjax(Request $request)
{
    try {
        Log::info('Received Task Data:', $request->all());

        // Remove empty key rows before validation
        $filteredKeys = collect($request->key ?? [])->filter(function ($key) {
            return !empty($key['task']) || !empty($key['duration']) || !empty($key['key_description']);
        })->values()->all();
        $request->merge(['key' => $filteredKeys]);

        // Validation
        $validator = Validator::make($request->all(), [
            'task_title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'due_time' => 'nullable',
            'total_day' => 'required|numeric|min:0',
            'total_time' => 'required|numeric|min:0',
            'employee' => 'nullable|array',
            'employee.*' => 'exists:employees,id',
            'key' => 'sometimes|array',
            'key.*.task' => 'required_with:key|string|max:255',
            'key.*.duration' => 'nullable|integer|min:0',
            'key.*.employee_id' => 'nullable|array',
            'key.*.employee_id.*' => 'exists:employees,id',
        ]);

 

        
        // Save the main task
        $task = new PersonalTask();
        $task->task_title = $request->task_title;
        $task->description = $request->description;
        $task->due_date = $request->due_date;
        $task->due_time = $request->due_time;
        $task->start_date = $request->start_date;
        $task->total_day = $request->total_day;
        $task->total_time = $request->total_time;
        $task->priority = $request->priority ?? 'normal';
        $task->color = $request->color;
        $task->public = $request->has('public') ? 1 : 0;
        $task->repeat = $request->repeat;
        $task->reminder_date = $request->reminder_date;
        $task->reminder_time = $request->reminder_time;
        $task->assigned_by = auth()->user()->name;
        $task->task_status = 'open';
        $task->progress = 0;
        $task->is_notified = 0;
        $task->is_customer = $request->is_customer;
        $task->customer_id = $request->customer_id ?? null;
        $task->alternative_id = $request->alternative_id ?? null;
        $task->product_id = $request->product_id ?? null;

        // Store employee IDs as JSON in controller_id
        $assignedEmployeeIds = [];

        if (!empty($request->employee)) {
            $assignedEmployeeIds = $request->employee;
        } else {
            foreach ($request->key ?? [] as $key) {
                if (!empty($key['employee_id']) && is_array($key['employee_id'])) {
                    $assignedEmployeeIds = array_merge($assignedEmployeeIds, $key['employee_id']);
                }
            }
            $assignedEmployeeIds = array_unique($assignedEmployeeIds);
        }

        $task->controller_id = json_encode(array_values($assignedEmployeeIds));
        $task->save();

        foreach ($assignedEmployeeIds as $employeeId) {
            EmployeesPersonalTask::create([
                'task_id' => $task->id,
                'employee_id' => $employeeId,
                'status' => 'accepted',
            ]);
        }

        if (!empty($request->key)) {
            foreach ($request->key as $key) {
                PersonalTaskKey::create([
                    'personal_task_id' => $task->id,
                    'task' => $key['task'],
                    'duration' => $key['duration'],
                    'link' => $key['link'] ?? null,
                    'key_description' => $key['key_description'] ?? null,
                    'status' => 'open',
                    'is_completed' => 0,
                    'work_progress' => 0,
                    'submit_time' => null,
                    'total_time' => $key['duration'],
                    'reason' => null,
                    'employee_id' => isset($key['employee_id']) ? json_encode($key['employee_id']) : null,
                ]);
            }
        } else {
            PersonalTaskKey::create([
                'personal_task_id' => $task->id,
                'task' => $task->task_title,
                'duration' => $task->total_time,
                'link' => null,
                'key_description' => $task->description,
                'status' => 'open',
                'is_completed' => 0,
                'work_progress' => 0,
                'submit_time' => null,
                'total_time' => $task->total_time,
                'reason' => null,
                'employee_id' => json_encode(array_values($assignedEmployeeIds)),
            ]);
        }

        return response()->json([
            'message' => 'Task successfully created!',
            'task_id' => $task->id
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error saving task:', ['error' => $e->getMessage()]);
        $errorMessage = json_decode($e->getMessage(), true);

        return response()->json([
            'message' => 'Validation failed or an error occurred.',
            'errors' => $errorMessage ?? ['error' => $e->getMessage()],
        ], 422);
    }
} 

public function personalTasksIndex(Request $req)
{
    Log::info('[PT::INDEX] Raw params', $req->all());

    $req->validate([
        'customer_id'    => 'required|integer',
        'alternative_id' => 'required|integer',
        'product_id'     => 'nullable|integer',
    ]);

    $query = PersonalTask::with([
        'employees:id,name,lastname,image',
        'steps',
    ])->where('customer_id', $req->customer_id)
      ->where('alternative_id', $req->alternative_id);

    if ($req->filled('product_id')) {
        $query->where('product_id', $req->product_id);
    }

    $tasks = $query->orderByDesc('id')->get();
    Log::info('[PT::INDEX] Found tasks', [
        'count'   => $tasks->count(),
        'taskIds' => $tasks->pluck('id'),
    ]);

    $allIds = collect($tasks)
        ->flatMap(fn($t) => $t->steps)
        ->flatMap(fn($s) => collect($s->employee_id ?: []))
        ->filter()->unique()->values();

    Log::info('[PT::INDEX] Step employee IDs (unique)', $allIds->all());

    $byId = $allIds->isEmpty()
        ? collect()
        : Employee::whereIn('id', $allIds)->get(['id','name','lastname','image'])->keyBy('id');

    foreach ($tasks as $t) {
        foreach ($t->steps as $s) {
            $ids = collect($s->employee_id ?: [])->filter();
            $s->setRelation('employees', $ids->map(fn($id) => $byId[$id] ?? null)->filter()->values());
        }
    }

    // Optional: sample serialize one task to ensure shape
    if ($tasks->isNotEmpty()) {
        Log::info('[PT::INDEX] Sample task payload', $tasks->first()->toArray());
    }

    return response()->json([
        'success' => true,
        'tasks'   => $tasks,
    ]);
}

public function personalTasksStore(Request $req)
{
    Log::info('[PT::STORE] Raw request (pre-strip)', $req->all());

    foreach (['id','created_at','updated_at','personal_task_id'] as $forbid) {
        if ($req->has($forbid)) Log::warning("[PT::STORE] Stripping forbidden field: {$forbid}");
        $req->request->remove($forbid);
    }

    $data = $req->validate([
        'customer_id'    => 'required|integer',
        'alternative_id' => 'required|integer',
        'product_id'     => 'nullable|integer',
        'task_title'     => 'required|string',
        'description'    => 'nullable|string',
        'priority'       => 'nullable|in:low,normal,high',
        'color'          => 'nullable|string|max:20',
        'controller_id'  => 'nullable|array',
        'controller_id.*'=> 'integer',
        'start_date'     => 'nullable|date',
        'due_date'       => 'nullable|date',
        'due_time'       => 'nullable|date_format:H:i',
        'employee_ids'   => 'nullable|array',
        'employee_ids.*' => 'integer',
    ]);

    $controllerIds = collect($data['controller_id'] ?? [])->map(fn($v)=>(int)$v)->filter()->unique()->values()->all();
    $employeeIds   = collect($data['employee_ids'] ?? [])->map(fn($v)=>(int)$v)->filter()->unique()->values()->all();
    $assignedBy    = (int) (auth()->user()->name ?? auth()->id());

    Log::info('[PT::STORE] Normalized', [
        'assigned_by'  => $assignedBy,
        'employee_ids' => $employeeIds,
        'controller'   => $controllerIds,
    ]);

    $task = DB::transaction(function () use ($data, $controllerIds, $employeeIds, $assignedBy) {
            $task = PersonalTask::create([
                'task_id'        => $this->makeTaskCode(),
                'customer_id'    => $data['customer_id'],
                'alternative_id' => $data['alternative_id'],
                'product_id'     => $data['product_id'] ?? null,
                'is_customer'    => true,
                'public'         => true,
                'task_title'     => $data['task_title'],
                'description'    => $data['description'] ?? null,
                'assigned_by'    => $assignedBy,
                'task_status'    => 'open',
                'priority'       => $data['priority'] ?? 'normal',
                'color'          => $data['color'] ?? '#8fc73e',
                'controller_id'  => array_values($controllerIds),
                'start_date'     => $data['start_date'] ?? null,
                'due_date'       => $data['due_date'] ?? null,
                'due_time'       => $data['due_time'] ?? null,
                'type'           => 'personal_task',
            ]);

            // 🔒 Defensive purge: remove any legacy rows that already reference this fresh id
            $legacyKeys   = DB::table('personal_task_keys')->where('personal_task_id', $task->id)->count();
            $legacyPivots = DB::table('employees_personal_tasks')->where('task_id', $task->id)->count();
            if ($legacyKeys || $legacyPivots) {
                \Log::warning('[PT::STORE] Detected LEGACY child rows for NEW task id '.$task->id, [
                    'legacy_keys' => $legacyKeys, 'legacy_pivots' => $legacyPivots
                ]);
                DB::table('personal_task_keys')->where('personal_task_id', $task->id)->delete();
                DB::table('employees_personal_tasks')->where('task_id', $task->id)->delete();
                \Log::warning('[PT::STORE] Purged legacy rows for task id '.$task->id);
            }

            // Now safely add current pivots
            if (!empty($employeeIds)) {
                $task->employees()->sync($employeeIds);
            }
            $this->upsertTaskPivots($task->id, $employeeIds);

            return $task;
        });

    // Read-back task + pivots
    $savedTask = PersonalTask::query()->with(['employees:id,name,lastname,image','steps'])->find($task->id);
    $pivots    = DB::table('employees_personal_tasks')->where('task_id', $task->id)->get();

    Log::info('[PT::STORE] READ-BACK task', $savedTask?->toArray() ?? []);
    Log::info('[PT::STORE] READ-BACK pivots (count='.count($pivots).')', [
        'rows' => $pivots->map(fn($r)=>(array)$r)->all()
    ]);

    $this->hydrateStepsEmployees(collect([$savedTask]));

    return response()->json(['success' => true, 'task' => $savedTask]);
}
public function personalTasksUpdate(Request $req, PersonalTask $task)
{
    $data = $req->validate([
        'task_title'     => 'sometimes|required|string',
        'description'    => 'nullable|string',
        'priority'       => 'nullable|in:low,normal,high',
        'color'          => 'nullable|string',
        'task_status'    => 'nullable|string',
        'controller_id'  => 'nullable|array',
        'controller_id.*'=> 'integer',
        'start_date'     => 'nullable|date',
        'due_date'       => 'nullable|date',
        'due_time'       => 'nullable|date_format:H:i',
    ]);

    // normalize array to keep cast happy
    if (array_key_exists('controller_id', $data)) {
        $data['controller_id'] = array_values($data['controller_id'] ?? []);
    }

    $task->fill($data)->save();
    $task->load(['employees:id,name,lastname,image', 'steps']);
    $this->hydrateStepsEmployees(collect([$task]));
    return response()->json(['success' => true, 'task' => $task]);
}

public function personalTasksDestroy(PersonalTask $task)
{
    $task->delete();
    return response()->json(['success' => true]);
}

public function personalTasksSyncEmployees(Request $req, PersonalTask $task)
{
    $data = $req->validate([
        'employee_ids'   => 'nullable|array',
        'employee_ids.*' => 'integer'
    ]);

    $task->employees()->sync($data['employee_ids'] ?? []);
    $task->load('employees:id,name,lastname,image');

    return response()->json(['success' => true, 'task' => $task]);
}
private function hydrateStepsEmployees(Collection $tasks): void
{
    $allIds = $tasks
        ->flatMap(fn($t) => $t->steps)
        ->flatMap(fn($s) => collect($s->employee_id ?: []))
        ->filter()->unique()->values();

    $byId = $allIds->isEmpty()
        ? collect()
        : Employee::whereIn('id', $allIds)->get(['id','name','lastname','image'])->keyBy('id');

    foreach ($tasks as $t) {
        foreach ($t->steps as $s) {
            $ids = collect($s->employee_id ?: [])->filter();
            $s->setRelation(
                'employees',
                $ids->map(fn($id) => $byId[$id] ?? null)->filter()->values()
            );
        }
    }
}

private function makeTaskCode(): string
{
    do {
        $code = (string) random_int(100000, 999999);
    } while (PersonalTask::where('task_id', $code)->exists());
    return $code;
}

private function upsertTaskPivots(int $taskId, array $employeeIds): void
{
    $clean = collect($employeeIds)->map(fn($v)=>(int)$v)->filter()->unique()->values();
    if ($clean->isEmpty()) return;

    $existing = DB::table('employees_personal_tasks')
        ->where('task_id', $taskId)
        ->pluck('employee_id')
        ->map(fn($v)=>(int)$v);

    $toInsert = $clean->diff($existing);

    if ($toInsert->isNotEmpty()) {
        DB::table('employees_personal_tasks')->insert(
            $toInsert->map(fn($eid)=>[
                'task_id'     => $taskId,
                'employee_id' => $eid,
                'status'      => 'accept',
                'created_at'  => now(),
                'updated_at'  => now(),
            ])->all()
        );
    }
}

public function report(PersonalTask $task)
{
    // All assignments (employees_personal_tasks)
    $assignments = EmployeesPersonalTask::with(['employee', 'changedBy'])
        ->where('task_id', $task->id)
        ->orderBy('created_at')
        ->get();

    // Rejections at the top
    $rejections = $assignments
        ->where('status', 'reject')
        ->map(function (EmployeesPersonalTask $row) {
            return [
                'employee_name'    => optional($row->employee)->name . ' ' . optional($row->employee)->lastname,
                'reason'           => $row->reason,
                'change_date'      => $row->change_date ? Carbon::parse($row->change_date)->format('d.m.Y') : null,
                'changed_by_name'  => $row->changed_by
                    ? optional($row->changedBy)->name . ' ' . optional($row->changedBy)->lastname
                    : null,
            ];
        })
        ->values()
        ->all();

    // Status history
    $history = $assignments->map(function (EmployeesPersonalTask $row) {
        $status = $row->status;

        $labels = [
            'send'     => 'Gesendet',
            'accept'   => 'Akzeptiert',
            'reject'   => 'Abgelehnt',
            'completed'=> 'Erledigt',
            'pause'    => 'Pausiert',
            'cancel'   => 'Storniert',
        ];

        return [
            'status'          => $status,
            'status_label'    => $labels[$status] ?? ucfirst($status),
            'employee_name'   => optional($row->employee)->name . ' ' . optional($row->employee)->lastname,
            'changed_by_name' => $row->changed_by
                ? optional($row->changedBy)->name . ' ' . optional($row->changedBy)->lastname
                : null,
            'change_reason'   => $row->change_reason,
            'change_date'     => $row->change_date
                ? Carbon::parse($row->change_date)->format('d.m.Y')
                : null,
            'created_at'      => $row->created_at
                ? $row->created_at->format('d.m.Y H:i')
                : null,
        ];
    })->values()->all();

    // Steps (personal_task_keys)
    $keys = PersonalTaskKey::where('personal_task_id', $task->id)->get();

    $steps = $keys->map(function (PersonalTaskKey $key) {
        $labels = [
            'open'       => 'Offen',
            'in_progress'=> 'In Bearbeitung',
            'done'       => 'Erledigt',
        ];

        $employeeNames = [];
        if ($key->employee_id) {
            $ids = is_array($key->employee_id)
                ? $key->employee_id
                : json_decode($key->employee_id, true);

            $ids = array_filter((array) $ids);
            if ($ids) {
                $employees = Employee::whereIn('id', $ids)->get(['id', 'name', 'lastname']);
                $employeeNames = $employees
                    ->map(fn ($e) => trim($e->name . ' ' . $e->lastname))
                    ->values()
                    ->all();
            }
        }

        return [
            'id'             => $key->id,
            'task'           => $key->task,
            'duration'       => (float) $key->duration,
            'status'         => $key->status,
            'status_label'   => $labels[$key->status] ?? ($key->status ?: '–'),
            'work_progress'  => (int) ($key->work_progress ?? 0),
            'key_description'=> $key->key_description,
            'employees'      => $employeeNames,
        ];
    })->values()->all();

    // Meta counts
    $commentsCount    = PersonalTaskComment::where('task_id', $task->id)->count();
    $attachmentsCount = PersonalTaskAttachment::where('task_id', $task->id)->count();

    // Labels for task header
    $statusLabels = [
        'send'       => 'Gesendet',
        'accept'     => 'Akzeptiert',
        'reject'     => 'Abgelehnt',
        'completed'  => 'Erledigt',
        'pause'      => 'Pausiert',
        'cancel'     => 'Storniert',
    ];

    $taskStatusLabel = $task->task_status
        ? ($statusLabels[$task->task_status] ?? ucfirst($task->task_status))
        : '–';

    $priorityLabels = [
        'normal'    => 'Keine',
        'medium'    => 'Medium',
        'high'      => 'Hoch',
        'very high' => 'Sehr wichtig',
    ];

    $priorityLabel = $task->priority
        ? ($priorityLabels[$task->priority] ?? ucfirst($task->priority))
        : 'Keine';

    $dueLabel = null;
    if ($task->due_date) {
        $date = Carbon::parse($task->due_date)->format('d.m.Y');
        $time = $task->due_time ? Carbon::parse($task->due_time)->format('H:i') : null;
        $dueLabel = $time ? "{$date} – {$time} Uhr" : $date;
    }

    return response()->json([
        'success'           => true,
        'task'              => [
            'id'                 => $task->id,
            'task_title'         => $task->task_title,
            'task_status'        => $task->task_status,
            'task_status_label'  => $taskStatusLabel,
            'priority'           => $task->priority,
            'priority_label'     => $priorityLabel,
            'color'              => $task->color,
            'due_label'          => $dueLabel,
        ],
        'rejections'        => $rejections,
        'history'           => $history,
        'steps'             => $steps,
        'comments_count'    => $commentsCount,
        'attachments_count' => $attachmentsCount,
    ]);
}
// ------------------------
// Helper: Task label
// ------------------------
private function formatTaskLabel($taskId): string
{
    $task = PersonalTask::select('id', 'task_title')->find($taskId);

    if (!$task) {
        return 'Task #'.$taskId;
    }

    return 'Task #'.$task->id.' – '.$task->task_title;
}


public function leadStore(Request $request)
{
    DB::beginTransaction();

    try {
        $productAssignments = collect($request->input('product_assignments', []))
            ->filter(fn ($item) => !empty($item['product_id']))
            ->values();

        $roofs = collect($request->input('roofs', []))
            ->filter(function ($roof) {
                return !empty($roof['designation'])
                    || !empty($roof['roof_orientation'])
                    || !empty($roof['roof_pitch'])
                    || !empty($roof['roof_area'])
                    || !empty($roof['roof_type'])
                    || !empty($roof['roof_covering_name'])
                    || !empty($roof['shading'])
                    || !empty($roof['notes']);
            })
            ->values();

        $rooms = collect($request->input('rooms', []))
            ->filter(function ($room) {
                return !empty($room['name'])
                    || !empty($room['area'])
                    || !empty($room['heating'])
                    || !empty($room['windows'])
                    || !empty($room['outer_wall'])
                    || !empty($room['target_temp'])
                    || !empty($room['door'])
                    || !empty($room['note']);
            })
            ->values();

        $street    = trim((string) $request->input('street'));
        $addressNo = trim((string) $request->input('address_no'));
        $postcode  = trim((string) $request->input('postcode'));
        $city      = trim((string) $request->input('city'));

        $streetLine = trim(collect([$street, $addressNo])->filter()->implode(' '));
        $fullAddress = trim(collect([$streetLine, $postcode, $city])->filter()->implode(', '));

        $fullName = trim(
            collect([
                $request->input('first_name'),
                $request->input('last_name'),
            ])->filter()->implode(' ')
        );

        $leadInfoNote = collect([
            $request->input('note'),
            $request->input('notice_contact_person')
                ? 'Kontaktperson: ' . $request->input('notice_contact_person')
                : null,
            $request->input('notice_documents_completion')
                ? 'Unterlagen & Abschluss: ' . $request->input('notice_documents_completion')
                : null,
        ])->filter()->implode("\n\n");

        $objectRemark = collect([
            $request->input('notice_object_location')
                ? 'Objektstandort: ' . $request->input('notice_object_location')
                : null,
            $request->input('notice_building_profile')
                ? 'Gebäudeprofil: ' . $request->input('notice_building_profile')
                : null,
            $request->input('notice_building_envelope')
                ? 'Gebäudehülle: ' . $request->input('notice_building_envelope')
                : null,
        ])->filter()->implode("\n\n");

        $energyRemark = collect([
            $request->input('notice_consumption_prices')
                ? 'Verbrauchsdaten & Preise: ' . $request->input('notice_consumption_prices')
                : null,
            $request->input('notice_roof_pv')
                ? 'Dach & PV: ' . $request->input('notice_roof_pv')
                : null,
            $request->input('notice_electrical_grid')
                ? 'Elektro & Netz: ' . $request->input('notice_electrical_grid')
                : null,
        ])->filter()->implode("\n\n");

        $heatingNotes = collect([
            $request->input('notice_heating_heatpump')
                ? 'Bestandsheizung & Wärmepumpe: ' . $request->input('notice_heating_heatpump')
                : null,
            $request->input('notice_room_heatingload_windows_doors')
                ? 'Raumdaten / Heizlast / Fenster / Türen: ' . $request->input('notice_room_heatingload_windows_doors')
                : null,
        ])->filter()->implode("\n\n");

        $carRemark = collect([
            $request->input('notice_emobility_wallbox')
                ? 'E-Mobilität & Wallbox: ' . $request->input('notice_emobility_wallbox')
                : null,
        ])->filter()->implode("\n\n");

        $generalAltNote = collect([
            $request->input('note'),
            $request->input('notice_customer_interest_products')
                ? 'Kundeninteresse / Produktzuweisung: ' . $request->input('notice_customer_interest_products')
                : null,
        ])->filter()->implode("\n\n");

        /*
        |--------------------------------------------------------------------------
        | 1. CUSTOMER -> new_leads
        |--------------------------------------------------------------------------
        */
        $lead = NewLeads::create([
            'customer_type'  => $request->input('building_type') === 'Gewerbe' ? 'B2B' : 'B2C',
            'title'          => $request->input('salutation'),
            'academic_title' => $request->input('salutation'),
            'firma'          => $request->input('building_type') === 'Gewerbe'
                ? $request->input('object_name')
                : null,
            'name'           => $request->input('first_name'),
            'lastname'       => $request->input('last_name'),
            'street'         => $streetLine ?: null,
            'latitude'       => $request->input('latitude'),
            'longitude'      => $request->input('longitude'),
            'postcode'       => $postcode ?: null,
            'city'           => $city ?: null,
            'phone'          => $request->input('phone'),
            'email'          => $request->input('email'),
            'source'         => 'Kalendar',
            'request_date'   => now(),
            'info'           => $leadInfoNote ?: null,
            'status'         => 'Published',
            'full_address'   => $fullAddress ?: null,
            'contact_person' => auth()->user()->name
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. OBJECT -> lead_alternative_adds
        |--------------------------------------------------------------------------
        */
        $alt = LeadAlternativeAdd::create([
            'lead_id'                           => $lead->id,
            'main'                              => 1,
            'street'                            => $street ?: null,
            'address_no'                        => $addressNo ?: null,
            'postcode'                          => $postcode ?: null,
            'city'                              => $city ?: null,
            'lat'                               => $request->input('latitude'),
            'lon'                               => $request->input('longitude'),
            'full_address'                      => $fullAddress ?: null,

            'object_name'                       => $request->input('object_name') ?: $fullName,
            'building_type'                     => $request->input('building_type'),
            'periority'                         => $request->input('periority') ?? 'normal',
            'appointment'                       => $request->input('appointment'),
            'appointment_confirmed'             => $request->input('appointment_confirmed'),
            'request_date'                      => now(),
            'status'                            => 'Published',

            'house_year'                        => $request->input('house_year'),
            'number_we'                         => $request->input('number_we'),
            'number_stories'                    => $request->input('number_stories'),
            'living_space'                      => $request->input('living_space'),
            'building_condition'                => $request->input('building_condition'),
            'usage_type'                        => $request->input('usage_type'),

            'owner_count'                       => $request->input('owner_count'),
            'owner_occupied_units'              => $request->input('owner_occupied_units'),
            'rented_units'                      => $request->input('rented_units'),
            'owners_below_40k'                  => $request->input('owners_below_40k'),
            'owners_above_40k'                  => $request->input('owners_above_40k'),
            'owner_occupied_below_40k'          => $request->input('owner_occupied_below_40k'),
            'owner_occupied_above_40k'          => $request->input('owner_occupied_above_40k'),
            'rented_below_40k'                  => $request->input('rented_below_40k'),
            'rented_above_40k'                  => $request->input('rented_above_40k'),

            'annual_heating_energy_consumption' => $request->input('annual_heating_energy_consumption'),
            'heating_energy_unit'               => $request->input('heating_energy_unit'),
            'total_electricity_consumption'     => $request->input('total_electricity_consumption'),
            'electricity_price'                 => $request->input('electricity_price'),
            'feed_in_tariff'                    => $request->input('feed_in_tariff'),
            'old_heating_price'                 => $request->input('old_heating_price'),

            'building_length'                   => $request->input('building_length'),
            'building_width'                    => $request->input('building_width'),
            'facade_height'                     => $request->input('facade_height'),
            'total_window_area'                 => $request->input('total_window_area'),

            'masonry'                           => $request->input('masonry'),
            'masonry_thickness'                 => $request->input('masonry_thickness'),
            'external_insulation_thickness'     => $request->input('external_insulation_thickness'),
            'insolation_type'                   => $request->input('insolation_type'),
            'insolation_age'                    => $request->input('insolation_age'),

            'roof_insulation_type'              => $request->input('roof_insulation_type'),
            'roof_insulation_thickness'         => $request->input('roof_insulation_thickness'),
            'roof_insulation_year'              => $request->input('roof_insulation_year'),

            'basement_insulation_type'          => $request->input('basement_insulation_type'),
            'basement_insulation_thickness'     => $request->input('basement_insulation_thickness'),
            'basement_insulation_year'          => $request->input('basement_insulation_year'),

            'window_glazing'                    => $request->input('window_glazing'),
            'window_frame'                      => $request->input('window_frame'),
            'window_year'                       => $request->input('window_year'),
            'ventilation_type'                  => $request->input('ventilation_type'),

            'heating_system_type'               => $request->input('heating_system_type'),
            'old_heating_power'                 => $request->input('old_heating_power'),
            'heat_distribution'                 => $request->input('heat_distribution'),
            'flow_temperature'                  => $request->input('flow_temperature'),
            'hot_water_generation'              => $request->input('hot_water_generation'),
            'hot_water_tank_liters'             => $request->input('hot_water_tank_liters'),
            'installation_location'             => $request->input('installation_location'),
            'groundwork'                        => $request->input('groundwork'),
            'heat_pump_pipe_length'             => $request->input('heat_pump_pipe_length'),
            'basement_ceiling_height'           => $request->input('basement_ceiling_height'),
            'door_width_for_installation'       => $request->input('door_width_for_installation'),
            'heat_pump_investment_costs'        => $request->input('heat_pump_investment_costs'),
            'heat_pump_subsidy_percent'         => $request->input('heat_pump_subsidy_percent'),

            'pipe_system_material'              => $request->input('pipe_system_material'),
            'circulation_line'                  => $request->input('circulation_line'),
            'heating_pipe_dimension'            => $request->input('heating_pipe_dimension'),
            'water_pipe_dimension'              => $request->input('water_pipe_dimension'),
            'circulation_pipe_dimension'        => $request->input('circulation_pipe_dimension'),

            'meter_cabinet_action'              => $request->input('meter_cabinet_action'),
            'meter_cabinet'                     => $request->input('meter_cabinet_action'),
            'cabinet_size'                      => $request->input('cabinet_size'),
            'sls_switch'                        => $request->input('sls_switch'),
            'apz_field'                         => $request->input('apz_field'),
            'ac_surge_protection'               => $request->input('ac_surge_protection'),
            'enwg_14a_ready'                    => $request->input('enwg_14a_ready'),
            'meter_count'                       => $request->input('meter_count'),
            'grid_reserve'                      => $request->input('grid_reserve'),
            'installation_location_power'       => $request->input('installation_location_power'),
            'network_wlan'                      => $request->input('network_wlan'),
            'tenant_model'                      => $request->boolean('tenant_model'),
            'load_management'                   => $request->boolean('load_management'),

            'electric_car'                      => $request->input('electric_car'),
            'electric_car_plan'                 => $request->input('electric_car_plan'),
            'electric_car_count'                => $request->input('electric_car_count'),
            'wallbox_count'                     => $request->input('wallbox_count'),
            'wallbox_location'                  => $request->input('wallbox_location'),
            'charging_power'                    => $request->input('charging_power'),
            'access_control'                    => $request->input('access_control'),
            'heavy_current_cable'               => $request->input('heavy_current_cable'),
            'network_cable'                     => $request->input('network_cable'),
            'car_kilo'                          => $request->input('car_kilo'),
            'bidirectional_car'                 => $request->boolean('bidirectional_car'),

            'documents_invoices'                => $request->boolean('documents_invoices'),
            'documents_roof_images'             => $request->boolean('documents_roof_images'),
            'documents_meter_images'            => $request->boolean('documents_meter_images'),
            'documents_window_images'           => $request->boolean('documents_window_images'),
            'documents_heating_images'          => $request->boolean('documents_heating_images'),
            'documents_facade_images'           => $request->boolean('documents_facade_images'),
            'site_visit_needed'                 => $request->boolean('site_visit_needed'),
            'ready_for_offer'                   => $request->boolean('ready_for_offer'),

            'bathroom_count'                    => $request->input('bathroom_count'),
            'bathtub_count'                     => $request->input('bathtub_count'),

            'object_remark'                     => $objectRemark ?: null,
            'energy_remark'                     => $energyRemark ?: null,
            'heating_notes'                     => $heatingNotes ?: null,
            'car_remark'                        => $carRemark ?: null,
            'note'                              => $generalAltNote ?: null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. PRODUCTS & EMPLOYEES
        |--------------------------------------------------------------------------
        */
        $savedProducts = [];
        $employeeIds = [auth()->user()->name];

        foreach ($productAssignments as $prod) {
            $product = LeadProductList::create([
                'customer_id'    => $lead->id,
                'alternative_id' => $alt->id,
                'product_id'     => $prod['product_id'] ?? null,
                'service_id'     => $prod['service_id'] ?? null,
                'employee_id'    => $prod['employee_id'] ?? null,
                'field_employee' => $prod['field_employee'] ?? null,
                'status'         => 'Neu',
                'work_status'    => 'playing',
                'interest'       => $request->input('periority') ?: 'intent',
                'stage'          => 'Lead',
                'service'        => 'complete',
            ]);

            if (!empty($prod['employee_id'])) {
                $employeeIds[] = $prod['employee_id'];
            }

            if (!empty($prod['field_employee'])) {
                $employeeIds[] = $prod['field_employee'];
            }

            $productLabel = 'Produkt #' . ($prod['product_id'] ?? '');

            $articleGroup = \App\Models\ArticleGroup::find($prod['product_id']);
            if ($articleGroup) {
                $productLabel =
                    $articleGroup->article_group
                    ?? $articleGroup->name
                    ?? $articleGroup->title
                    ?? $articleGroup->product
                    ?? $productLabel;
            }

            $savedProducts[] = [
                'uid'            => $productLabel . '_' . $alt->id,
                'name'           => $productLabel,
                'product_id'     => $product->product_id,
                'service_id'     => $product->service_id,
                'employee_id'    => $product->employee_id,
                'field_employee' => $product->field_employee,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 4. ROOFS -> pv_roofs
        |--------------------------------------------------------------------------
        */
        $savedRoofs = [];

        foreach ($roofs as $roof) {
            $roofRow = PVRoof::create([
                'customer_id'             => $lead->id,
                'alternative_id'          => $alt->id,
                'designation'             => $roof['designation'] ?? null,
                'roof_orientation'        => $roof['roof_orientation'] ?? null,
                'roof_pitch'              => $roof['roof_pitch'] ?? null,
                'roof_area'               => $roof['roof_area'] ?? null,
                'roof_type'               => $roof['roof_type'] ?? null,
                'roof_covering_name'      => $roof['roof_covering_name'] ?? null,
                'shading'                 => $roof['shading'] ?? null,
                'notes'                   => $roof['notes'] ?? null,
                'roof_height'             => $request->input('roof_height'),
                'dc_cable_route'          => $request->input('dc_cable_route'),
                'pv_existing'             => $request->input('pv_existing'),
                'storage_preference'      => $request->input('storage_preference'),
                'backup_power'            => $request->input('backup_power'),
                'pv_investment_costs'     => $request->input('pv_investment_costs'),
                'meter_cabinet'           => $request->input('meter_cabinet_action'),
                'cabinet_size'            => $request->input('cabinet_size'),
                'electric_car'            => $request->input('electric_car'),
                'number_of_electric_cars' => $request->input('electric_car_count'),
                'number_of_wallboxes'     => $request->input('wallbox_count'),
            ]);

            $savedRoofs[] = $roofRow;
        }

        /*
        |--------------------------------------------------------------------------
        | 5. ROOMS -> lead_object_rooms
        |--------------------------------------------------------------------------
        */
        $savedRooms = [];

        foreach ($rooms as $room) {
            $roomRow = LeadObjectRoom::create([
                'customer_id'    => $lead->id,
                'alternative_id' => $alt->id,
                'name'           => $room['name'] ?? null,
                'area'           => $room['area'] ?? null,
                'heating'        => $room['heating'] ?? null,
                'windows'        => $room['windows'] ?? null,
                'outer_wall'     => $room['outer_wall'] ?? null,
                'target_temp'    => $room['target_temp'] ?? null,
                'door'           => $room['door'] ?? null,
                'note'           => $room['note'] ?? null,
            ]);

            $savedRooms[] = $roomRow;
        }

        DB::commit();

        return response()->json([
            'success'   => true,
            'message'   => 'Wizard-Daten wurden erfolgreich gespeichert.',
            'lead'      => [
                'id'       => $lead->id,
                'name'     => $lead->name,
                'lastname' => $lead->lastname,
                'phone'    => $lead->phone,
                'email'    => $lead->email,
                'status'   => $lead->status,
            ],
            'address'   => [
                'street'       => $street,
                'address_no'   => $addressNo,
                'postcode'     => $postcode,
                'city'         => $city,
                'full_address' => $fullAddress,
                'latitude'     => $request->input('latitude'),
                'longitude'    => $request->input('longitude'),
            ],
            'products'  => $savedProducts,
            'employees' => array_values(array_unique(array_filter($employeeIds))),
            'alt'       => $alt,
            'roofs'     => $savedRoofs,
            'rooms'     => $savedRooms,
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'line'    => $e->getLine(),
            'file'    => $e->getFile(),
        ], 500);
    }
}

}
