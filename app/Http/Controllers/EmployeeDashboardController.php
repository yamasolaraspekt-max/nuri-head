<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; 
use App\Models\Branch;
use App\Models\Problem;
use App\Models\Product;
use App\Models\UserRoll; 
use App\Models\Invoice;
use App\Models\WeatherStation;
use App\Models\DashboardIcon;
use App\Models\DailyReport;
use App\Models\PersonalTask;
use App\Models\MainAppointment;
use App\Models\Project;
use App\Models\Deal;
use App\Models\Offers;
use App\Models\PersonalNote;
use Illuminate\Validation\Rule;
use App\Models\LeadAlternativeAdd; 
use DB;
use App\Models\DailyReportTime;
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;  
use App\Models\DepartmentPosition;
use App\Models\Leave;
use App\Models\EmployeeSick;
use App\Models\TimeManagementPlan;
use App\Models\TimeManagementEntry;  
use App\Models\Department;
use App\Models\Employee; 
use App\Models\EmployeeRecurringLeave; 

class EmployeeDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }

    private $defaultOrder = [
        'my_inquiries',
        'my_leads',
        'my_tasks',
        'my_plannings',
        'my_offers',
        'my_orders',
        'my_projects',
        'my_tickets',
        'my_chats'
    ];
    


  public function index()
    {
        $employeeId = auth()->user()->name;
        $today      = Carbon::today()->toDateString();
        $year       = Carbon::now()->year;
        $month      = Carbon::now()->month;

        // === TASKS TODAY ===
        $tasks = PersonalTask::with(['employees', 'taskKeys'])
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
            ->whereDate('due_date', $today)
            ->get()
            ->map(function ($task) {
                return [
                    'task_title'       => $task->task_title,
                    'task_description' => $task->description,
                    'employee_count'   => $task->employees->count(),
                    'task_keys'        => $task->taskKeys,
                    'start_date'       => $task->start_date,
                    'due_date'         => $task->due_date,
                    'priority'         => $task->priority,
                    'status'           => $task->task_status,
                ];
            });

        // === APPOINTMENTS TODAY ===
        $appointments = MainAppointment::whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            })
            ->whereDate('end_date', $today)
            ->get()
            ->map(fn($appt) => [
                'title'  => $appt->name,
                'date'   => $appt->start_date,
                'time'   => $appt->start_time,
                'status' => $appt->status,
            ]);

        // === DEPARTMENT POSITIONS ===
        $positions = DepartmentPosition::with('department')
            ->where('employee_id', $employeeId)
            ->get();

        $departmentPositionsLabel = $positions
            ->map(function ($pos) {
                $name = $pos->department->department_name ?? '–';
                if (!empty($pos->main) && $pos->main === 'active') {
                    $name .= ' (Haupt)';
                }
                return $name;
            })
            ->implode(', ');

        $departmentPositionsCount = $positions->count();

        // === LEAVES (Urlaub) ===
        // Adjust leave_type values to your real types if different
        $approvedLeavesQuery = Leave::where('emp_id', $employeeId)
            ->where('year', $year)
            ->where(function ($q) {
                $q->whereIn('status', ['approved', 'genehmigt'])
                ->orWhereIn('approved', ['approved', 'yes', 'ja', '1', 1]);
            });

        $vacationLeavesQuery = (clone $approvedLeavesQuery)
            ->whereIn('leave_type', ['Urlaub', 'Vacation', 'Annual', 'annual_leave']);

        // days used this year (sum of leave_day)
        $vacationDaysUsed = (clone $vacationLeavesQuery)->sum('leave_day');

        // remaining_day is stored per request; take the newest record as "current remaining"
        $lastLeaveRow = (clone $vacationLeavesQuery)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->first();

        $vacationDaysRemain = $lastLeaveRow?->remaining_day ?? 0;
        $annualLeaveTotal   = $vacationDaysUsed + $vacationDaysRemain;

        // next upcoming vacation (incoming holiday)
        $nextLeave = (clone $vacationLeavesQuery)
            ->whereDate('start_date', '>=', $today)
            ->orderBy('start_date')
            ->first();

        $nextLeaveStart = $nextLeave?->start_date;

        // === SICK DAYS (employee_sicks) ===
        $sickEntries = EmployeeSick::where('emp_id', $employeeId)
            ->whereYear('start_date', $year)
            ->get();

        $sickDays = $sickEntries->sum(function ($row) {
            if (!is_null($row->total_days)) {
                return (int) $row->total_days;
            }

            $start = Carbon::parse($row->start_date);
            $end   = Carbon::parse($row->end_date ?? $row->start_date);

            return $start->diffInDays($end) + 1; // inkl. Starttag
        });

        // === TIME MANAGEMENT (current month) ===
        $currentPlan = TimeManagementPlan::where('employee_id', $employeeId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        $planTargetHours    = $currentPlan?->target_hours ?? 0;
        $planScheduledHours = $currentPlan?->scheduled_hours ?? 0;

        $planLoggedHours = 0;
        if ($currentPlan) {
            $planLoggedHours = TimeManagementEntry::where('plan_id', $currentPlan->id)
                ->sum('hours');
        }

        $activeDepartments = \App\Models\Department::where('status', 'Published')
            ->with(['departmentPositions.employee' => function($q) {
                // Select only necessary columns to keep it fast
                $q->select('id', 'name', 'lastname', 'image');
            }])
            ->get()
            ->map(function ($dept) {
                // Extract unique employees from positions (in case one person has 2 roles in same dept)
                $uniqueEmployees = $dept->departmentPositions
                    ->map(fn($pos) => $pos->employee)
                    ->filter() // remove nulls (if employee deleted)
                    ->unique('id')
                    ->values(); // reset keys
                
                $dept->setRelation('staff', $uniqueEmployees);
                return $dept;
            });


            $employeeId = Auth::user()->name; 

    // 2. Count My Customers (Contact Person)
        $myCustomerCount = DB::table('new_leads')
            ->where('contact_person', $employeeId)
            ->whereNull('deleted_at')
            ->count();

        // 3. Count My Projects (Innendienst OR Außendienst)
        $myProjectCount = DB::table('lead_product_lists')
            ->whereNull('deleted_at')
            ->where(function($query) use ($employeeId) {
                $query->where('employee_id', $employeeId)
                    ->orWhere('field_employee', $employeeId);
            })
            ->count();
      

        // PL-Prüfliste: Montage-Karten, die bei MIR zur Prüfung liegen (status='reported',
        // reviewer_employee_id=ich); Admin sieht zusätzlich die prüferlosen (orWhereNull).
        $isAdmin = (bool) (auth()->user()->is_admin ?? false);
        $reviews = \App\Models\KanbanLeadTask::query()
            ->with(['customer', 'product', 'phaseActivity', 'reviewer'])
            ->where('status', 'reported')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($employeeId, $isAdmin) {
                $q->where('reviewer_employee_id', (int) $employeeId);
                if ($isAdmin) {
                    $q->orWhereNull('reviewer_employee_id');
                }
            })
            ->orderByDesc('updated_at')
            ->get();

        // Melder = jüngstes erledigtes planner_item, das auf die Karte zeigt (1b-Rückweg).
        // Ein Query, kein N+1; bei mehreren Items je Karte gewinnt das jüngste done_at.
        $reviewMelder = [];
        $reviewCardIds = $reviews->pluck('id')->all();
        if (!empty($reviewCardIds)) {
            $melderByCard = [];
            $melderRows = DB::table('planner_items')
                ->whereIn('kanban_lead_task_id', $reviewCardIds)
                ->whereNotNull('done_at')
                ->whereNotNull('done_by_employee_id')
                ->orderByDesc('done_at')
                ->get(['kanban_lead_task_id', 'done_by_employee_id']);
            foreach ($melderRows as $row) {
                $cid = (int) $row->kanban_lead_task_id;
                if (!isset($melderByCard[$cid])) {
                    $melderByCard[$cid] = (int) $row->done_by_employee_id; // erster Treffer = jüngstes done
                }
            }
            $melderNames = DB::table('employees')
                ->whereIn('id', array_values($melderByCard) ?: [0])
                ->get(['id', 'name', 'lastname'])
                ->keyBy('id');
            foreach ($melderByCard as $cid => $eid) {
                $e = $melderNames->get($eid);
                $reviewMelder[$cid] = $e ? trim(($e->lastname ?? '') . ' ' . ($e->name ?? '')) : null;
            }
        }

        return view('admin.dashboard.employee.mobile', compact(
            'tasks',
            'appointments',
            'departmentPositionsLabel',
            'departmentPositionsCount',
            'vacationDaysUsed',
            'vacationDaysRemain',
            'annualLeaveTotal',
            'sickDays',
            'nextLeaveStart',
            'planTargetHours',
            'planScheduledHours',
            'planLoggedHours',
            'activeDepartments',
            'myCustomerCount',
            'myProjectCount',
            'reviews',
            'reviewMelder',
        ));
    }


    public function mobile()
    {
        $employeeId = auth()->user()->name;
        $today      = Carbon::today()->toDateString();
        $year       = Carbon::now()->year;
        $month      = Carbon::now()->month;

        // === TASKS TODAY ===
        $tasks = PersonalTask::with(['employees', 'taskKeys'])
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
            ->whereDate('due_date', $today)
            ->get()
            ->map(function ($task) {
                return [
                    'task_title'       => $task->task_title,
                    'task_description' => $task->description,
                    'employee_count'   => $task->employees->count(),
                    'task_keys'        => $task->taskKeys,
                    'start_date'       => $task->start_date,
                    'due_date'         => $task->due_date,
                    'priority'         => $task->priority,
                    'status'           => $task->task_status,
                ];
            });

        // === APPOINTMENTS TODAY ===
        $appointments = MainAppointment::whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            })
            ->whereDate('end_date', $today)
            ->get()
            ->map(fn($appt) => [
                'title'  => $appt->name,
                'date'   => $appt->start_date,
                'time'   => $appt->start_time,
                'status' => $appt->status,
            ]);

        // === DEPARTMENT POSITIONS ===
        $positions = DepartmentPosition::with('department')
            ->where('employee_id', $employeeId)
            ->get();

        $departmentPositionsLabel = $positions
            ->map(function ($pos) {
                $name = $pos->department->department_name ?? '–';
                if (!empty($pos->main) && $pos->main === 'active') {
                    $name .= ' (Haupt)';
                }
                return $name;
            })
            ->implode(', ');

        $departmentPositionsCount = $positions->count();

        // === LEAVES (Urlaub) ===
        // Adjust leave_type values to your real types if different
        $approvedLeavesQuery = Leave::where('emp_id', $employeeId)
            ->where('year', $year)
            ->where(function ($q) {
                $q->whereIn('status', ['approved', 'genehmigt'])
                ->orWhereIn('approved', ['approved', 'yes', 'ja', '1', 1]);
            });

        $vacationLeavesQuery = (clone $approvedLeavesQuery)
            ->whereIn('leave_type', ['Urlaub', 'Vacation', 'Annual', 'annual_leave']);

        // days used this year (sum of leave_day)
        $vacationDaysUsed = (clone $vacationLeavesQuery)->sum('leave_day');

        // remaining_day is stored per request; take the newest record as "current remaining"
        $lastLeaveRow = (clone $vacationLeavesQuery)
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->first();

        $vacationDaysRemain = $lastLeaveRow?->remaining_day ?? 0;
        $annualLeaveTotal   = $vacationDaysUsed + $vacationDaysRemain;

        // next upcoming vacation (incoming holiday)
        $nextLeave = (clone $vacationLeavesQuery)
            ->whereDate('start_date', '>=', $today)
            ->orderBy('start_date')
            ->first();

        $nextLeaveStart = $nextLeave?->start_date;

        // === SICK DAYS (employee_sicks) ===
        $sickEntries = EmployeeSick::where('emp_id', $employeeId)
            ->whereYear('start_date', $year)
            ->get();

        $sickDays = $sickEntries->sum(function ($row) {
            if (!is_null($row->total_days)) {
                return (int) $row->total_days;
            }

            $start = Carbon::parse($row->start_date);
            $end   = Carbon::parse($row->end_date ?? $row->start_date);

            return $start->diffInDays($end) + 1; // inkl. Starttag
        });

        // === TIME MANAGEMENT (current month) ===
        $currentPlan = TimeManagementPlan::where('employee_id', $employeeId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        $planTargetHours    = $currentPlan?->target_hours ?? 0;
        $planScheduledHours = $currentPlan?->scheduled_hours ?? 0;

        $planLoggedHours = 0;
        if ($currentPlan) {
            $planLoggedHours = TimeManagementEntry::where('plan_id', $currentPlan->id)
                ->sum('hours');
        }

        return view('admin.dashboard.employee.mobile', compact(
            'tasks',
            'appointments',
            'departmentPositionsLabel',
            'departmentPositionsCount',
            'vacationDaysUsed',
            'vacationDaysRemain',
            'annualLeaveTotal',
            'sickDays',
            'nextLeaveStart',
            'planTargetHours',
            'planScheduledHours',
            'planLoggedHours'
        ));
    }

    
    
    public function loadTabContent(Request $request)
    {
        $tab = $request->input('tab', 'all');
        $search = $request->input('search', '');
        $date = $request->input('date', now()->format('Y-m-d'));
        $range = $request->input('range', 'today');
    
        // Force 'today' range for full overview tab
        if ($tab === 'all') {
            $range = 'today';
        }
    
        $employeeId = auth()->user()->name; // Replace with auth()->user()->name if needed
    
        // ✅ Closure for dynamic date filtering
        $applyDateFilter = function ($column) use ($date, $range) {
            return function ($query) use ($column, $date, $range) {
                if (empty($date)) return $query;
    
                $carbonDate = \Carbon\Carbon::parse($date);
    
                if ($range === 'week') {
                    return $query->whereBetween($column, [
                        $carbonDate->copy()->startOfWeek(),
                        $carbonDate->copy()->endOfWeek()
                    ]);
                } elseif ($range === 'month') {
                    return $query->whereMonth($column, $carbonDate->month)
                                 ->whereYear($column, $carbonDate->year);
                } else {
                    // default to today
                    return $query->whereDate($column, $carbonDate);
                }
            };
        };
    
        \Log::info('Filters received', [
            'tab' => $tab,
            'search' => $search,
            'date' => $date,
            'range' => $range,
            'employeeId' => $employeeId
        ]);
    
        // ✅ Tab loader
        // Sicherheitsnetz: einige Tab-Partials existieren nicht mehr (nach Old/ verschoben).
        // Statt eines 500ers liefern wir einen leeren, geschützten Platzhalter aus.
        try {
            if ($tab === 'tasks') {
                return $this->getTasks($employeeId, $search, $applyDateFilter, $date);
            } elseif ($tab === 'appointments') {
                return $this->getAppointments($employeeId, $search, $applyDateFilter, $date);
            } elseif ($tab === 'notes') {
                return view('admin.dashboard.employee.partials.notes');
            } elseif ($tab === 'calendar') {
                return view('admin.dashboard.employee.partials.calendar');
            } elseif ($tab === 'admin') {
                return $this->getAdminContent($search, $applyDateFilter, $date);
            } else {
                return $this->getAllContent($employeeId, $search, $applyDateFilter, $date);
            }
        } catch (\Throwable $e) {
            \Log::warning('loadTabContent: Tab-Inhalt nicht verfuegbar', [
                'tab'   => $tab,
                'error' => $e->getMessage(),
            ]);

            return response(
                '<div class="p-4 text-sm text-gray-500">Dieser Tab-Inhalt ist derzeit nicht verfügbar.</div>',
                200
            );
        }
    }
    
    
    
    private function getTasks($employeeId, $search, $applyDateFilter, $date)
    {
        $query = PersonalTask::with(['employees:id,name,image', 'taskKeys.doneBy:id,name'])
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId));
    
        if (!empty($search)) {
            $query->where('task_title', 'like', "%{$search}%");
        }
    
        // ✅ Only include tasks due today
        $query->whereDate('due_date', \Carbon\Carbon::today());
    
        $tasks = $query->get()->map(function ($task) {
            return [
                'id'              => $task->id,
                'task_title'      => $task->task_title,
                'task_description'=> $task->description,
                'employee_count'  => $task->employees->count(),
                'employees'       => $task->employees->map(fn($e) => [
                    'name' => $e->name,
                    'photo' => $e->image
                        ? asset('images/employee/' . $e->image)
                        : asset('images/gender/male.png'),
                ])->toArray(),
                'task_keys'       => $task->taskKeys,
                'start_date'      => $task->start_date,
                'due_date'        => $task->due_date,
                'priority'        => $task->priority,
                'status'          => $task->task_status,
            ];
        });
    
        return view('admin.dashboard.employee.partials.tasks', compact('tasks'));
    }
    
    private function getAppointments($employeeId, $search, $applyDateFilter, $date)
    {
        $query = MainAppointment::with([
            'employees:id,name,image',
            'customer:id,name,lastname,phone,email',
            'branch:id,branch',
            'branchAddress:id,full_address,city,postcode'
        ])
        ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId));
    
        if (!empty($search)) {
            $query->where('name', 'like', "%$search%");
        }
    
        // ✅ Only appointments where end_date = today
        $query->whereDate('end_date', \Carbon\Carbon::today());
    
        $appointments = $query->get();
    
        return view('admin.dashboard.employee.partials.appointments', compact('appointments'));
    }
    
 

    private function getAllContent($employeeId, $search, $applyDateFilter, $date)
    {
        $tasks = $this->fetchTasks($employeeId, $search, $applyDateFilter, $date);
        $appointments = $this->fetchAppointments($employeeId, $search, $applyDateFilter, $date);
        $problems = $this->fetchProblem($employeeId, $search, $applyDateFilter, $date);
    
        return view('admin.dashboard.employee.partials.all', compact('tasks', 'appointments', 'problems'));
    }
    
    
    public function fetchTasks($employeeId, $search, $applyDateFilter, $date)
    {
        return PersonalTask::with(['employees' => function($q) {
            $q->select('employees.id', 'name', 'image');
        }])
        ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
        ->when($search, fn($q) => $q->where('task_title', 'like', "%$search%"))
        ->when($applyDateFilter, function ($q) use ($date) {
            return $q->whereDate('due_date', \Carbon\Carbon::parse($date));
        })
        ->get()
        ->map(function ($task) {
            return [
                'id' => $task->id,
                'task_title' => $task->task_title,
                'start_date' => $task->start_date,
                'due_date' => $task->due_date,
                'priority' => $task->priority,
                'status' => $task->task_status,
                'color' => $task->color ?? '#3b82f6', // ✅ Default fallback
                'employee_count' => $task->employees->count(),
                'employees' => $task->employees->map(fn($e) => [
                    'name' => $e->name,
                    'photo' => $e->image
                        ? asset('images/employee/' . $e->image)
                        : asset('images/gender/male.png'),
                ])->toArray()
            ];
        });
    }
    
    private function fetchAppointments($employeeId, $search, $applyDateFilter, $date)
    {
        $query = MainAppointment::with('employees:id,name,image')
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId));
    
        if (!empty($search)) {
            $query->where('name', 'like', "%$search%");
        }
    
        $dateFilter = $applyDateFilter('start_date');
        $query = $dateFilter($query);
    
        // 🔍 Log SQL and bindings
        \Log::info('Appointment SQL:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
    
        $results = $query->get();
    
        // 🔍 Log count and debug info
        \Log::info('Appointments fetched', [
            'count' => $results->count(),
            'date' => $date,
            'employeeId' => $employeeId,
        ]);
    
        return $results->map(function ($appointment) {
            return [
                'id'         => $appointment->id,
                'name'       => $appointment->name,
                'start_date' => $appointment->start_date,
                'end_date'   => $appointment->end_date,
                'date'       => $appointment->start_date,
                'time'       => $appointment->start_time,
                'status'     => $appointment->status,
                'color'      => $appointment->color ?? '#10b981',
                'employees'  => $appointment->employees->map(fn($e) => [
                    'name' => $e->name,
                    'photo' => $e->image
                        ? asset('images/employee/' . $e->image)
                        : asset('images/gender/male.png'),
                ])->toArray(),
                'address'    => trim("{$appointment->street} {$appointment->postcode} {$appointment->city}"),
                'latitude'   => $appointment->latitude,
                'longitude'  => $appointment->longitude,
            ];
        });
    }
    

    private function fetchProblem($employeeId, $search, $applyDateFilter, $date)
    {
        return Problem::with(['employees:id,name,image'])
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
            // ->whereNotIn('status', ['beendet', 'end']) // ✅ exclude closed
            ->when($search, fn($q) => $q->where('problem', 'like', "%$search%"))
            ->get()
            ->map(function ($problem) {
                return [
                    'id'        => $problem->id,
                    'ticket_no' => $problem->ticket_no,
                    'article'   => $problem->article_name,
                    'priority'  => $problem->priority,
                    'status'    => $problem->status,
                    'date'      => $problem->date,
                    'employees' => $problem->employees->map(fn($e) => [
                        'name'  => $e->name,
                        'photo' => $e->image
                            ? asset('images/employee/' . $e->image)
                            : asset('images/gender/male.png'),
                    ])->toArray(),
                ];
            });
    } 

  
    private function getAdminContent($search, $applyDateFilter, $date)
    {
        $tasks = PersonalTask::with('employees:id,name,image')
            ->when($search, fn($q) => $q->where('task_title', 'like', "%$search%"))
            ->whereDate('due_date', \Carbon\Carbon::parse($date))
            ->get()
            ->map(function ($task) {
                return [
                    'id'         => $task->id,
                    'task_title' => $task->task_title,
                    'start_date' => $task->start_date,
                    'due_date'   => $task->due_date,
                    'priority'   => $task->priority,
                    'status'     => $task->task_status,
                    'color'      => $task->color ?? '#3b82f6', // ✅ added
                    'employees'  => $task->employees->map(fn($e) => [
                        'name'  => $e->name,
                        'photo' => $e->image ? asset('images/employee/' . $e->image) : asset('images/gender/male.png'),
                    ])->toArray(),
                ];
            });

        $appointments = MainAppointment::with('employees:id,name,image')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->whereDate('start_date', \Carbon\Carbon::parse($date))
            ->get()
            ->map(function ($a) {
                return [
                    'id'        => $a->id,
                    'name'      => $a->name,
                    'date'      => $a->start_date,
                    'time'      => $a->start_time,
                    'status'    => $a->status,
                    'color'     => $a->color ?? '#10b981',
                    'address'   => trim("{$a->street} {$a->postcode} {$a->city}"),
                    'latitude'  => $a->latitude,
                    'longitude' => $a->longitude,
                    'employees' => $a->employees->map(fn($e) => [
                        'name' => $e->name,
                        'photo' => $e->image ? asset('images/employee/' . $e->image) : asset('images/gender/male.png'),
                    ])->toArray(),
                ];
            });

        $problems = Problem::with('employees:id,name,image')
            ->when($search, fn($q) => $q->where('problem', 'like', "%$search%"))
            ->get()
            ->map(function ($p) {
                return [
                    'id'        => $p->id,
                    'ticket_no' => $p->ticket_no,
                    'article'   => $p->article_name,
                    'priority'  => $p->priority,
                    'status'    => $p->status,
                    'date'      => $p->date,
                    'color'     => '#f59e0b', // ✅ default color
                    'employees' => $p->employees->map(fn($e) => [
                        'name'  => $e->name,
                        'photo' => $e->image ? asset('images/employee/' . $e->image) : asset('images/gender/male.png'),
                    ])->toArray(),
                ];
            });

        return view('admin.dashboard.employee.partials.admin', compact('tasks', 'appointments', 'problems'));
    }
 
     public function getTabCounts()
     {
         return response()->json([
             'tasks' => PersonalTask::count(),
             'appointments' => MainAppointment::count(),
         ]);
     }

    /**
     * Show the form for creating a new resource.
     */
    
     public function getWeatherData(Request $request)
    {
        try {
            // Get the user's current location using IP
            $location = $this->getCurrentLocation();

            if (!isset($location['loc'])) {
                return response()->json(['error' => 'Unable to fetch location data'], 400);
            }

            // Extract latitude and longitude
            [$latitude, $longitude] = explode(',', $location['loc']);

            // Call the Tomorrow.io API with the location data
            $weatherData = $this->fetchWeatherData($latitude, $longitude);

            if (!$weatherData) {
                return response()->json(['error' => 'Unable to fetch weather data'], 500);
            }

            // Return weather data to the frontend
            return response()->json($weatherData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getCurrentLocation()
    {
        try {
            $response = Http::get('https://ipinfo.io/json');
            if ($response->failed()) {
                throw new \Exception('Failed to retrieve current location.');
            }
            return $response->json();
        } catch (\Exception $e) {
            throw new \Exception('Failed to fetch location data: ' . $e->getMessage());
        }
    }

    private function fetchWeatherData($latitude, $longitude)
    {
        try {
            $apiKey = config('services.tomorrowio.key');
            $url = "https://api.tomorrow.io/v4/weather/forecast?location={$latitude},{$longitude}&apikey={$apiKey}";

            $response = Http::get($url);

            if ($response->failed()) {
                throw new \Exception('Failed to fetch weather data from Tomorrow.io');
            }

            return $response->json();
        } catch (\Exception $e) {
            throw new \Exception('Failed to fetch weather data: ' . $e->getMessage());
        }
    }


    public function getDueToday(Request $request)
    {
        $employeeId = auth()->user()->name; // employees.id stored in users.name
        $userKey = auth()->id();

        $selectedDate = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : Carbon::today();

        $doneStatuses = ['completed', 'complete', 'done', 'end', 'pause', 'rejected', 'reject'];
        $doneLower = array_map('strtolower', $doneStatuses);

        $hasAdminAccess = DB::table('user_rolls')
            ->where('user_id', $userKey)
            ->where('item_id', 'Administrator')
            ->where('is_read', 'on')
            ->where('is_update', 'on')
            ->where('is_delete', 'on')
            ->where('is_add', 'on')
            ->exists();

        /*
        |--------------------------------------------------------------------------
        | 1) PERSONAL TASKS
        |--------------------------------------------------------------------------
        */
        $personalTasks = DB::table('employees_personal_tasks as ept')
            ->join('personal_tasks as pt', 'ept.task_id', '=', 'pt.id')
            ->where('ept.employee_id', $employeeId)
            ->whereNull('pt.deleted_at')
            ->whereNotIn(DB::raw('LOWER(COALESCE(ept.status,""))'), $doneLower)
            ->whereNotIn(DB::raw('LOWER(COALESCE(pt.task_status,""))'), $doneLower)
            ->whereDate('pt.due_date', $selectedDate)
            ->select(
                'pt.id',
                'pt.task_title as title',
                'pt.due_date',
                DB::raw('NULL as due_time'),
                'pt.priority',
                'pt.description',
                DB::raw("'personal_task' as type"),
                DB::raw("'Aufgabe' as label"),
                DB::raw("0 as has_report"),
                DB::raw("NULL as report_badge_text"),
                DB::raw("NULL as customer_name"),
                DB::raw("NULL as customer_id"),
                DB::raw("NULL as product_info"),
                DB::raw("NULL as full_address"),
                DB::raw("NULL as map_url"),
                \Illuminate\Support\Facades\Schema::hasColumn('personal_tasks', 'deal_measurement_id')
                ? 'pt.deal_measurement_id'
                : DB::raw('NULL as deal_measurement_id'),
                DB::raw("CONCAT('/personal-tasks/', pt.id, '/profile') as detail_url")
            )
            ->get()
            ->map(function ($item) {
                $measurementId = (int) ($item->deal_measurement_id ?? 0);

                $item->feinaufmass_id = $measurementId ?: null;
                $item->feinaufmass_url = $measurementId
                    ? (\Illuminate\Support\Facades\Route::has('deal.measurements.show')
                        ? route('deal.measurements.show', $measurementId)
                        : url('/deal-measurements/' . $measurementId))
                    : null;
                $item->feinaufmass_label = $measurementId ? ('Feinaufmaß #' . $measurementId) : null;

                return $item;
            });

        /*
        |--------------------------------------------------------------------------
        | 2) APPOINTMENTS
        | - load appointments on selected date
        | - include customer + products + address + report info
        |--------------------------------------------------------------------------
        */
        $appointments = DB::table('main_appointment_employees as mae')
            ->join('main_appointments as a', 'mae.appointment_id', '=', 'a.id')
            ->leftJoin('new_leads as nl', 'a.customer_id', '=', 'nl.id')
            ->where('mae.employee_id', $employeeId)
            ->whereNull('a.deleted_at')
            ->where(function ($q) use ($doneStatuses) {
                $q->whereNull('mae.status')
                    ->orWhereNotIn(DB::raw('LOWER(COALESCE(mae.status,""))'), array_merge($doneStatuses, ['confirm']));
            })
            ->where(function ($q) use ($selectedDate) {
                $q->whereDate('a.start_date', $selectedDate)
                    ->orWhereDate('a.end_date', $selectedDate);
            })
            ->select(
                'a.id',
                \Illuminate\Support\Facades\Schema::hasColumn('main_appointments', 'deal_measurement_id')
                ? DB::raw("COALESCE(a.deal_measurement_id, CASE WHEN a.source = 'deal_measurement' THEN a.other_id ELSE NULL END) as deal_measurement_id")
                : DB::raw("CASE WHEN a.source = 'deal_measurement' THEN a.other_id ELSE NULL END as deal_measurement_id"),
                'a.name as title',
                DB::raw('COALESCE(a.start_date, a.end_date) as due_date'),
                DB::raw('COALESCE(a.start_time, a.end_time) as due_time'),
                'a.priority',
                'a.note as description',
                'a.is_report',
                'a.report',
                'a.products',
                'a.full_address',
                'a.street',
                'a.postcode',
                'a.city',
                'a.customer_id',
                'nl.firma',
                'nl.lastname',
                'nl.name',
                DB::raw("'appointment' as type"),
                DB::raw("'Termin' as label")
            )
            ->get()
            ->map(function ($item) {
                $customerName = trim(($item->firma ?? '') ?: trim(($item->lastname ?? '') . ' ' . ($item->name ?? '')));
                $item->customer_name = $customerName ?: null;

                $products = $item->products;
                if (is_string($products)) {
                    $decoded = json_decode($products, true);
                    $products = json_last_error() === JSON_ERROR_NONE ? $decoded : $products;
                }

                if (is_array($products)) {
                    $productNames = collect($products)
                        ->map(function ($p) {
                            if (is_array($p)) {
                                return $p['name'] ?? $p['title'] ?? $p['article_group'] ?? null;
                            }
                            return is_string($p) ? $p : null;
                        })
                        ->filter()
                        ->values()
                        ->all();

                    $item->product_info = !empty($productNames) ? implode(', ', $productNames) : null;
                } else {
                    $item->product_info = !empty($products) ? (string) $products : null;
                }

                $fullAddress = $item->full_address ?: trim(
                    collect([$item->street, $item->postcode, $item->city])
                        ->filter()
                        ->implode(' ')
                );

                $item->full_address = $fullAddress ?: null;
                $item->map_url = $fullAddress
                    ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($fullAddress)
                    : null;

                $measurementId = (int) ($item->deal_measurement_id ?? 0);
                $item->feinaufmass_id = $measurementId ?: null;
                $item->feinaufmass_url = $measurementId
                    ? (\Illuminate\Support\Facades\Route::has('deal.measurements.show')
                        ? route('deal.measurements.show', $measurementId)
                        : url('/deal-measurements/' . $measurementId))
                    : null;
                $item->feinaufmass_label = $measurementId ? ('Feinaufmaß #' . $measurementId) : null;

                $hasReport = !empty($item->report);
                $reportRequired = filter_var($item->is_report, FILTER_VALIDATE_BOOLEAN);

                $item->has_report = $hasReport ? 1 : 0;
                $item->report_required = $reportRequired ? 1 : 0;
                $item->report_badge_text = $hasReport
                    ? 'Bericht vorhanden'
                    : ($reportRequired ? 'Bericht nötig' : null);

                $item->detail_url = url('/customer/appointments/' . $item->id);

                return $item;
            });

        /*
        |--------------------------------------------------------------------------
        | 3) PROBLEMS
        |--------------------------------------------------------------------------
        */
        $problems = DB::table('employee_problem as ep')
            ->join('problems as p', 'p.id', '=', 'ep.problem_id')
            ->join('new_leads as nl', 'p.customer_id', '=', 'nl.id')
            ->leftJoin('lead_alternative_adds as la', 'p.alternative_id', '=', 'la.id')
            ->leftJoin('article_groups as ag', 'p.product_id', '=', 'ag.id')
            ->where('ep.employee_id', $employeeId)
            ->where('p.status', '!=', 'end')
            ->whereNull('p.deleted_at')
            ->whereDate(DB::raw('COALESCE(p.end_date, p.progress_date, p.date)'), $selectedDate)
            ->select(
                'p.id',
                'p.priority',
                'p.ticket_no',
                'p.date',
                'p.progress_date',
                'p.end_date',
                'p.problem',
                'nl.firma',
                'nl.lastname',
                'nl.name',
                'nl.id as customer_id',
                'la.object_name',
                'ag.article_group'
            )
            ->get()
            ->map(function ($item) {
                $customerName = $item->firma ?: trim(($item->lastname ?? '') . ' ' . ($item->name ?? ''));
                $item->title = $customerName !== '' ? $customerName . ' – Ticket #' . $item->ticket_no : 'Ticket #' . $item->ticket_no;

                $parts = [];
                if (!empty($item->object_name))
                    $parts[] = $item->object_name;
                if (!empty($item->article_group))
                    $parts[] = $item->article_group;
                if (!empty($item->problem)) {
                    $plain = strip_tags($item->problem);
                    $plain = mb_substr($plain, 0, 120) . (mb_strlen($plain) > 120 ? '…' : '');
                    $parts[] = $plain;
                }

                $item->description = $parts ? implode(' – ', $parts) : null;
                $item->due_date = $item->end_date ?? $item->progress_date ?? $item->date;
                $item->due_time = null;
                $item->type = 'problem';
                $item->label = 'Ticket';
                $item->has_report = 0;
                $item->report_badge_text = null;
                $item->customer_name = $customerName ?: null;
                $item->product_info = $item->article_group ?? null;
                $item->full_address = null;
                $item->map_url = null;
                $item->detail_url = url('/problem/profile/' . $item->id);

                return $item;
            });

        /*
 |--------------------------------------------------------------------------
 | 4) TICKET TASKS
 |--------------------------------------------------------------------------
 */
        $ticketTasks = DB::table('ticket_tasks as tt')
            ->leftJoin('problems as p', 'p.id', '=', 'tt.ticket_id')
            ->leftJoin('new_leads as nl', 'p.customer_id', '=', 'nl.id')
            ->where('tt.employee_id', $employeeId)
            ->whereNotIn(DB::raw('LOWER(COALESCE(tt.status,""))'), $doneLower)
            ->whereDate('tt.due_date', $selectedDate)
            ->select(
                'tt.id',
                'tt.ticket_id',
                'tt.title',
                'tt.due_date',
                DB::raw('NULL as due_time'),
                'tt.priority',
                'tt.solution as description',
                'tt.status',
                'tt.appointment_id',

                'p.ticket_no',
                'p.problem',
                'p.priority as ticket_priority',

                'nl.firma',
                'nl.lastname',
                'nl.name',
                'nl.id as customer_id',

                DB::raw("'ticket_task' as type"),
                DB::raw("'Ticket-Task' as label"),
                DB::raw("0 as has_report"),
                DB::raw("NULL as report_badge_text"),
                DB::raw("NULL as product_info"),
                DB::raw("NULL as full_address"),
                DB::raw("NULL as map_url")
            )
            ->get()
            ->map(function ($item) {
                $customerName = trim(($item->firma ?? '') ?: trim(($item->lastname ?? '') . ' ' . ($item->name ?? '')));

                $item->customer_name = $customerName ?: null;

                if (!empty($item->ticket_no)) {
                    $item->title = 'Ticket #' . $item->ticket_no . ' – ' . ($item->title ?: 'Ticket Aufgabe');
                }

                $descriptionParts = [];

                if (!empty($item->description)) {
                    $descriptionParts[] = strip_tags($item->description);
                }

                if (!empty($item->problem)) {
                    $plainProblem = strip_tags($item->problem);
                    $descriptionParts[] = mb_substr($plainProblem, 0, 120) . (mb_strlen($plainProblem) > 120 ? '…' : '');
                }

                $item->description = $descriptionParts
                    ? implode(' – ', $descriptionParts)
                    : null;

                $item->priority = $item->priority ?: ($item->ticket_priority ?: 'normal');

                $item->detail_url = !empty($item->ticket_id)
                    ? url('/problem/profile/' . $item->ticket_id)
                    : '#';

                return $item;
            });
        /*
        |--------------------------------------------------------------------------
        | 5a) LEADS
        |--------------------------------------------------------------------------
        */
        $leads = DB::table('lead_alternative_adds as la')
            ->join('new_leads as nl', 'la.lead_id', '=', 'nl.id')
            ->join('lead_product_lists as lpl', function ($join) {
                $join->on('lpl.customer_id', '=', 'nl.id')
                    ->on('lpl.alternative_id', '=', 'la.id');
            })
            ->where('la.status', '!=', 'Published')
            ->whereIn('lpl.status', ['lead', 'new'])
            ->whereNull('lpl.deleted_at')
            ->where(function ($q) use ($employeeId) {
                $q->where('lpl.employee_id', $employeeId)
                    ->orWhere('lpl.field_employee', $employeeId);
            })
            ->whereDate('la.request_date', $selectedDate)
            ->select([
                'la.id',
                'la.request_date as due_date',
                DB::raw('NULL as due_time'),
                DB::raw("COALESCE(la.object_name, '') as object_name"),
                DB::raw("COALESCE(la.city, '') as city"),
                DB::raw("COALESCE(nl.lastname, '') as lastname"),
                DB::raw("COALESCE(nl.name, '') as name"),
                DB::raw("COALESCE(nl.firma, '') as firma"),
                DB::raw("nl.id as customer_id"),
                DB::raw("'normal' as priority"),
            ])
            ->get()
            ->map(function ($item) {
                $fullName = trim($item->lastname . ' ' . $item->name);
                $customerName = $item->firma ?: ($fullName !== '' ? $fullName : 'Lead');

                $item->type = 'lead';
                $item->label = 'Lead';
                $item->title = $customerName;

                $parts = array_filter([$item->object_name ?: null, $item->city ?: null]);
                $item->description = implode(' – ', $parts);

                $item->has_report = 0;
                $item->report_badge_text = null;
                $item->customer_name = $customerName;
                $item->product_info = $item->object_name ?: null;
                $item->full_address = null;
                $item->map_url = null;
                $item->detail_url = url('/lead/product/' . $item->id);

                return $item;
            });

        /*
        |--------------------------------------------------------------------------
        | 5b) INQUIRIES
        |--------------------------------------------------------------------------
        */
        $inquiries = DB::table('inquiry_product_lists as ipl')
            ->join('inquiries as i', 'ipl.inquiry_id', '=', 'i.id')
            ->whereNull('i.deleted_at')
            ->where(function ($q) use ($employeeId) {
                $q->where('ipl.employee_id', $employeeId)
                    ->orWhere('ipl.field_employee', $employeeId);
            })
            ->whereNotNull('ipl.appointment_date')
            ->whereDate('ipl.appointment_date', $selectedDate)
            ->whereNotIn(DB::raw('LOWER(COALESCE(ipl.status,""))'), $doneLower)
            ->whereRaw('LOWER(COALESCE(i.status, "")) != ?', ['published'])
            ->select([
                'ipl.id as id',
                'i.id as inquiry_id',
                'ipl.appointment_date as due_date',
                DB::raw('TIME(ipl.appointment_date) as due_time'),
                DB::raw("COALESCE(i.periority, 'normal') as priority"),
                'i.firma',
                'i.lastname',
                'i.name',
                'i.city',
                'i.note as description',
                'i.status as inquiry_status',
                'ipl.status',
            ])
            ->get()
            ->map(function ($item) {
                $customer = trim(($item->lastname ?? '') . ' ' . ($item->name ?? ''));
                $title = $item->firma ?: ($customer !== '' ? $customer : 'Anfrage');

                $parts = array_filter([
                    $item->city ?: null,
                    $item->status ?: null,
                ]);

                $suffix = $parts ? (' – ' . implode(' – ', $parts)) : '';
                $desc = trim((string) ($item->description ?? '') . $suffix);

                $item->type = 'inquiry';
                $item->label = 'Anfrage';
                $item->title = $title;
                $item->description = $desc;
                $item->has_report = 0;
                $item->report_badge_text = null;
                $item->customer_name = $title;
                $item->product_info = null;
                $item->full_address = null;
                $item->map_url = null;
                $item->detail_url = url('/inquiry_show/' . $item->id);

                return $item;
            });

        /*
        |--------------------------------------------------------------------------
        | 6) LEAVES
        |--------------------------------------------------------------------------
        */
        $leavesQuery = DB::table('leaves as l')->join('employees as e', 'l.emp_id', '=', 'e.id');
        if (!$hasAdminAccess) {
            $leavesQuery->where('l.request_to', $employeeId);
        }

        $leaves = $leavesQuery
            ->whereNull('l.approved')
            ->where(function ($q) {
                $q->whereNull('l.status')->orWhereNotIn('l.status', ['reject', 'not_responsible']);
            })
            ->whereDate('l.start_date', $selectedDate)
            ->select(
                'l.id',
                'l.start_date',
                'l.end_date',
                'l.reason',
                'l.leave_type',
                'e.name as employee_name'
            )
            ->get()
            ->map(function ($item) {
                $item->type = 'leave';
                $item->label = 'Urlaub / Antrag';
                $item->title = $item->employee_name . ' – ' . ($item->leave_type ?? 'Urlaub');
                $item->due_date = $item->start_date;
                $item->due_time = null;

                $from = Carbon::parse($item->start_date)->format('d.m.Y');
                $to = Carbon::parse($item->end_date)->format('d.m.Y');
                $item->description = "Von {$from} bis {$to}" . ($item->reason ? ' – ' . $item->reason : '');
                $item->priority = 'normal';

                $item->has_report = 0;
                $item->report_badge_text = null;
                $item->customer_name = null;
                $item->product_info = null;
                $item->full_address = null;
                $item->map_url = null;
                $item->detail_url = '#';

                return $item;
            });

        $combined = $personalTasks
            ->merge($appointments)
            ->merge($ticketTasks)
            ->merge($problems)
            ->merge($leads)
            ->merge($inquiries)
            ->merge($leaves);

        $totalWeight = 0;
        $todayWeight = 0;

        $items = $combined->map(function ($item) use ($selectedDate, &$totalWeight, &$todayWeight) {
            $priority = strtolower($item->priority ?? 'normal');

            $weight = match (true) {
                str_contains($priority, 'very high'),
                str_contains($priority, 'sehr') => 3,
                str_contains($priority, 'high'),
                str_contains($priority, 'dring') => 2,
                str_contains($priority, 'low'),
                str_contains($priority, 'niedrig') => 0.5,
                default => 1,
            };

            $hasDueDate = !empty($item->due_date);
            $isSelectedDay = $hasDueDate && Carbon::parse($item->due_date)->isSameDay($selectedDate);

            $item->is_today = $isSelectedDay;
            $item->is_remaining = !$isSelectedDay;
            $item->weight = $weight;

            $measurementId = (int) ($item->feinaufmass_id ?? $item->deal_measurement_id ?? 0);
            $item->feinaufmass_id = $measurementId ?: null;
            $item->deal_measurement_id = $measurementId ?: null;
            $item->feinaufmass_url = $item->feinaufmass_url ?? ($measurementId
                ? (\Illuminate\Support\Facades\Route::has('deal.measurements.show')
                    ? route('deal.measurements.show', $measurementId)
                    : url('/deal-measurements/' . $measurementId))
                : null);
            $item->feinaufmass_label = $item->feinaufmass_label ?? ($measurementId ? ('Feinaufmaß #' . $measurementId) : null);

            $totalWeight += $weight;
            if ($isSelectedDay) {
                $todayWeight += $weight;
            }

            return $item;
        })->values();

        $percent = $totalWeight > 0 ? round(($todayWeight / $totalWeight) * 100) : 0;

        $aufgabeCount = $items->where('type', 'personal_task')->count();
        $anfrageCount = $items->where('type', 'inquiry')->count();
        $appointmentCount = $items->where('type', 'appointment')->count();
        $leadCount = $items->where('type', 'lead')->count();
        $restCount = $items->filter(fn($i) => !in_array($i->type, ['personal_task', 'inquiry', 'appointment', 'lead'], true))->count();

        $counters = [
            'total' => ['count' => $items->count()],
            'lead' => ['count' => $leadCount],
            'anfrage' => ['count' => $anfrageCount],
            'aufgabe' => ['count' => $aufgabeCount],
            'appointment' => [
                'count' => $appointmentCount,
                'unreported' => $appointments->where('report_required', 1)->where('has_report', 0)->count(),
            ],
            'rest' => ['count' => $restCount],
        ];

        $typeStats = $items
            ->groupBy('type')
            ->map(function ($group) {
                $tw = $group->sum('weight');
                $td = $group->filter(fn($item) => $item->is_today)->sum('weight');
                $todayPercent = $tw > 0 ? round(($td / $tw) * 100) : 0;

                return [
                    'type' => $group->first()->type,
                    'label' => $group->first()->label,
                    'today' => $todayPercent,
                    'remaining' => $tw > 0 ? 100 - $todayPercent : 0,
                ];
            })
            ->values();

        return response()->json([
            'date' => $selectedDate->toDateString(),
            'percent' => $percent,
            'items' => $items,
            'type_stats' => $typeStats,
            'counters' => $counters,
            'unreported_appointments_count' => $appointments->where('report_required', 1)->where('has_report', 0)->count(),
        ]);
    }

    public function markAsDone(Request $request)
    {
        $id = $request->input('id');           // item id coming from UI
        $type = $request->input('type');         // personal_task | appointment | lead | inquiry | ...
        $action = $request->input('action', 'done');

        // employees.id in your system (stored in users.name)
        $employeeKey = auth()->user()->name;
        $userId = auth()->id();
        $user = auth()->user();

        // Optional report payload
        $report = trim((string) $request->input('report', ''));
        $meta = $request->input('meta');              // can be array/json
        $dueDateIn = $request->input('due_date');          // date or datetime string
        $nextStep = trim((string) $request->input('next_step', ''));
        $stage = trim((string) $request->input('stage', ''));

        // Admin check (unchanged)
        $hasAdminAccess = DB::table('user_rolls')
            ->where('user_id', $userId)
            ->where('item_id', 'Administrator')
            ->where('is_read', 'on')
            ->where('is_update', 'on')
            ->where('is_delete', 'on')
            ->where('is_add', 'on')
            ->exists();

        try {
            DB::beginTransaction();

            switch ($type) {

                // =========================================================
                // PERSONAL TASK: mark done + store comment in personal_task_comments
                // =========================================================
                case 'personal_task':
                    DB::table('employees_personal_tasks')
                        ->where('task_id', $id)
                        ->where('employee_id', $employeeKey)
                        ->update(['status' => 'completed']);

                    if ($report !== '') {
                        DB::table('personal_task_comments')->insert([
                            'task_id' => $id,
                            'comment_by' => $employeeKey, // employees.id
                            'comment' => $report,
                            'status' => 'Published',
                            'parent_id' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'deleted_at' => null,
                        ]);
                    }
                    break;

                // =========================================================
                // TICKET TASK (unchanged)
                // =========================================================
                case 'ticket_task':
                    $ticketTask = DB::table('ticket_tasks')
                        ->where('id', $id)
                        ->where('employee_id', $employeeKey)
                        ->first(['id', 'solution']);

                    if (!$ticketTask) {
                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => 'Ticket-Aufgabe nicht gefunden.',
                        ], 404);
                    }

                    $oldSolution = trim((string) ($ticketTask->solution ?? ''));

                    $newReportBlock = '';
                    if ($report !== '') {
                        $newReportBlock =
                            "\n\n--- Abschlussbericht vom " . now()->format('d.m.Y H:i') . " ---\n" .
                            $report;
                    }

                    DB::table('ticket_tasks')
                        ->where('id', $id)
                        ->where('employee_id', $employeeKey)
                        ->update([
                            'status' => 'Done',
                            'is_done' => 1,
                            'solution' => trim($oldSolution . $newReportBlock),
                            'updated_at' => now(),
                        ]);

                    break;

                // =========================================================
                // PROBLEM / TICKET (unchanged)
                // =========================================================
                case 'problem':
                    $problem = DB::table('problems')
                        ->where('id', $id)
                        ->first(['id', 'solution']);

                    if (!$problem) {
                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => 'Ticket nicht gefunden.',
                        ], 404);
                    }

                    $oldSolution = trim((string) ($problem->solution ?? ''));

                    $newReportBlock = '';
                    if ($report !== '') {
                        $newReportBlock =
                            "\n\n--- Abschlussbericht vom " . now()->format('d.m.Y H:i') . " ---\n" .
                            $report;
                    }

                    DB::table('problems')
                        ->where('id', $id)
                        ->update([
                            'status' => 'end',
                            'solution' => trim($oldSolution . $newReportBlock),
                            'end_user' => $userId,
                            'end_date' => now()->toDateString(),
                            'edit_user' => $userId,
                            'edit_date' => now()->toDateString(),
                            'updated_at' => now(),
                        ]);

                    break;

                // =========================================================
                // APPOINTMENT: confirm + insert appointment_reports
                // =========================================================
                case 'appointment':
                    $appointment = DB::table('main_appointments')
                        ->where('id', $id)
                        ->first(['id', 'start_date', 'start_time', 'end_date', 'end_time']);

                    if (!$appointment) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Termin nicht gefunden.',
                        ], 404);
                    }

                    $endDate = $appointment->end_date ?: $appointment->start_date;
                    $endTime = $appointment->end_time ?: $appointment->start_time ?: '23:59:59';

                    $endAt = Carbon::parse(trim($endDate . ' ' . $endTime));

                    if ($endAt->isFuture()) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Termin ist noch nicht beendet.',
                        ], 422);
                    }

                    // confirm for this employee
                    DB::table('main_appointment_employees')
                        ->where('appointment_id', $id)
                        ->where('employee_id', $employeeKey)
                        ->update(['status' => 'confirm']);

                    // store report (if provided)
                    if ($report !== '' || $nextStep !== '' || !empty($meta) || $dueDateIn) {
                        $dueDate = $dueDateIn ? Carbon::parse($dueDateIn)->toDateString() : null;

                        DB::table('appointment_reports')->insert([
                            'employee_id' => $employeeKey,       // employees.id
                            'appointment_id' => $id,
                            'task_id' => null,
                            'type' => 'appointment',
                            'report' => ($report !== '' ? $report : null),
                            'report_date' => now()->toDateString(),
                            'next_step' => ($nextStep !== '' ? $nextStep : null),
                            'due_date' => $dueDate,
                            'report_by' => $employeeKey,       // employees.id (you said report_by exists)
                            'comments' => null,
                            'likes' => 0,
                            'dislikes' => 0,
                            'meta' => !empty($meta) ? json_encode($meta) : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    break;

                // =========================================================
                // LEAD: insert customer_reports + mark lead_product_lists row done
                // UI should ideally send list_id (lead_product_lists.id). If not, we fallback by alt id.
                // =========================================================
                case 'lead':
                    // id is la.id (lead_alternative_adds.id)
                    $la = DB::table('lead_alternative_adds')
                        ->where('id', $id)
                        ->first(['id', 'lead_id']);

                    if (!$la) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => 'Lead alternative not found.'], 404);
                    }

                    $listId = $request->input('list_id'); // lead_product_lists.id (recommended)
                    $productId = $request->input('product_id'); // optional override

                    if (!$productId && $listId) {
                        $productId = DB::table('lead_product_lists')
                            ->where('id', $listId)
                            ->value('product_id');
                    }

                    // Save report
                    if ($report !== '' || $stage !== '' || !empty($meta) || $productId) {
                        DB::table('customer_reports')->insert([
                            'customer_id' => $la->lead_id,     // new_leads.id
                            'alternative_id' => $la->id,          // lead_alternative_adds.id
                            'product_id' => $productId ?: null,
                            'report_by' => $employeeKey,     // employees.id
                            'stage' => ($stage !== '' ? $stage : null),
                            'report' => ($report !== '' ? $report : null),
                            'report_details' => !empty($meta) ? json_encode($meta) : null,
                            'deleted_at' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Mark lead_product_lists as done/end
                    if ($listId) {
                        DB::table('lead_product_lists')
                            ->where('id', $listId)
                            ->update([
                                'status' => 'done',
                                'updated_at' => now(),
                            ]);
                    } else {
                        // fallback: update matching rows for this alternative + this employee
                        DB::table('lead_product_lists')
                            ->where('alternative_id', $la->id)
                            ->where(function ($q) use ($employeeKey) {
                                $q->where('employee_id', $employeeKey)
                                    ->orWhere('field_employee', $employeeKey);
                            })
                            ->whereIn('status', ['lead', 'new'])
                            ->update([
                                'status' => 'done',
                                'updated_at' => now(),
                            ]);
                    }
                    break;

                // =========================================================
                // INQUIRY: insert inquiry_reports + mark inquiry_product_lists row done
                // Here $id should be inquiry_product_lists.id (recommended).
                // If UI sends inquiry_id directly, we accept it as fallback.
                // =========================================================
                case 'inquiry':
                    $inquiryListId = $id; // inquiry_product_lists.id
                    $ipl = DB::table('inquiry_product_lists')
                        ->where('id', $inquiryListId)
                        ->first(['id', 'inquiry_id', 'appointment_date', 'status']);

                    $inquiryId = $ipl->inquiry_id ?? (int) $request->input('inquiry_id');

                    if (!$inquiryId) {
                        DB::rollBack();
                        return response()->json(['success' => false, 'message' => 'Inquiry not found.'], 404);
                    }

                    // mark list row done
                    if ($ipl) {
                        DB::table('inquiry_product_lists')
                            ->where('id', $inquiryListId)
                            ->update([
                                'status' => 'done',
                                'updated_at' => now(),
                            ]);
                    }

                    // store report
                    if ($report !== '' || !empty($meta) || $dueDateIn) {
                        $dueTs = null;
                        if ($dueDateIn) {
                            $dueTs = Carbon::parse($dueDateIn);
                        } elseif ($ipl && $ipl->appointment_date) {
                            $dueTs = Carbon::parse($ipl->appointment_date);
                        }

                        DB::table('inquiry_reports')->insert([
                            'inquiry_id' => $inquiryId,
                            'report_by' => $employeeKey, // employees.id
                            'report' => ($report !== '' ? $report : null),
                            'meta' => !empty($meta) ? json_encode($meta) : null,
                            'report_date' => now(),
                            'due_date' => $dueTs ? $dueTs : null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    break;

                // =========================================================
                // LEAVES (your original logic unchanged)
                // =========================================================
                case 'leave':
                    $conditions = ['id' => $id];
                    if (!$hasAdminAccess) {
                        $conditions['request_to'] = $employeeKey;
                    }

                    switch ($action) {
                        case 'approve':
                            DB::table('leaves')
                                ->where($conditions)
                                ->update([
                                    'approved' => 'Yes',
                                    'status' => 'accept',
                                    'changed_by' => $employeeKey,
                                ]);
                            break;

                        case 'reject':
                            $reason = trim((string) $request->input('reason', ''));

                            $existingNotesJson = DB::table('leaves')
                                ->where($conditions)
                                ->value('notes');

                            $notes = [];
                            if ($existingNotesJson) {
                                $decoded = json_decode($existingNotesJson, true);
                                if (is_array($decoded))
                                    $notes = $decoded;
                            }

                            if ($reason !== '') {
                                $notes[] = [
                                    'employee_id' => $userId,
                                    'employee' => $user->name ?? 'Unknown',
                                    'image' => $user->image ?? null,
                                    'date' => now()->format('Y-m-d H:i'),
                                    'text' => $reason,
                                ];
                            }

                            DB::table('leaves')
                                ->where($conditions)
                                ->update([
                                    'approved' => 'No',
                                    'status' => 'reject',
                                    'changed_by' => $employeeKey,
                                    'notes' => json_encode($notes),
                                ]);
                            break;

                        case 'not_responsible':
                            DB::table('leaves')
                                ->where($conditions)
                                ->update([
                                    'status' => 'not_responsible',
                                    'changed_by' => $employeeKey,
                                ]);
                            break;

                        default:
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' => 'Invalid action for leave.',
                            ], 400);
                    }
                    break;

                default:
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid type.',
                    ], 400);
            }

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

 


public function saveAppointmentReport(Request $request)
{
    \Log::info('requested', [$request->all()]);

    // Validate the incoming data
    $request->validate([
        'id'     => ['required','integer'],
        'report' => ['required','string']
    ]);

    // Load the appointment (ignore soft-deleted rows)
    $appointment = MainAppointment::where('id', $request->id)
        ->whereNull('deleted_at')  // skip trashed
        ->first();

    if (!$appointment) {
        return response()->json([
            'success' => false,
            'message' => 'Termin nicht gefunden oder gelöscht.',
        ], 404);
    }

    // Save report
    $appointment->report       = $request->report;
    $appointment->report_date  = now();
    $appointment->report_by    = auth()->user()->name;
    $appointment->is_report    = 'true'; // if you want to toggle this too
    $appointment->save();

    return response()->json(['success' => true]);
}

public function quickDepartments()
{
    if (!request()->ajax()) {
        abort(404);
    }

    $departments = Department::query()
        ->with(['staff' => function ($q) {
            $q->orderBy('name');
        }])
        ->where('status', 'Published')
        ->orderBy('order')
        ->get();

    $defaultAvatar = asset('images/gender/male.png');

    $payload = $departments->map(function ($dept) use ($defaultAvatar) {
        $staff = $dept->staff ?? collect();

        return [
            'id'   => $dept->id,
            'name' => $dept->department_name,
            'url'  => url('department/profile/' . $dept->id),

            'staff' => $staff->take(5)->map(function ($emp) use ($defaultAvatar) {
                return [
                    'id'     => $emp->id,
                    'name'   => $emp->name,
                    'avatar' => $emp->image
                        ? asset('images/employee/' . $emp->image)
                        : $defaultAvatar,
                ];
            })->values(),

            'more_count' => max($staff->count() - 5, 0),
        ];
    })->values();

    return response()->json($payload);
}
    

 public function getMyData(Request $request)
    {
        // 1. Get the logged-in Employee ID
        $employeeId = Auth::user()->name; 
        $type = $request->input('type'); 
        $search = $request->input('search'); // Get search term

        if ($type === 'customers') {
            // --- MY CUSTOMERS ---
            $query = DB::table('new_leads')
                ->where('contact_person', $employeeId)
                ->whereNull('deleted_at');

            // Apply Search
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('lastname', 'like', "%{$search}%")
                      ->orWhere('firma', 'like', "%{$search}%")
                      ->orWhere('customer_no', 'like', "%{$search}%");
                });
            }

            $customers = $query->select(
                    'id', 'customer_no', 'name', 'lastname', 'firma', 
                    'street', 'postcode', 'city', 'email', 'phone'
                )
                ->get();

            // Populate Objects & Products (Same logic as before)
            foreach ($customers as $customer) {
                $customer->objects = DB::table('lead_alternative_adds')
                    ->where('lead_id', $customer->id)
                    ->whereNull('deleted_at')
                    ->select('id', 'object_name', 'street', 'postcode', 'city', 'main')
                    ->get();

                foreach ($customer->objects as $object) {
                    $object->products = DB::table('lead_product_lists')
                        ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
                        ->where('lead_product_lists.customer_id', $customer->id)
                        ->where('lead_product_lists.alternative_id', $object->id)
                        ->whereNull('lead_product_lists.deleted_at')
                        ->select(
                            'lead_product_lists.id',
                            'lead_product_lists.status',
                            'lead_product_lists.stage',
                            'lead_product_lists.stage_history',
                            'article_groups.article_group as product_name'
                        )
                        ->get()
                        ->map(function($prod) {
                            $history = json_decode($prod->stage_history, true);
                            $prod->last_history = (is_array($history) && count($history) > 0) ? end($history) : null;
                            return $prod;
                        });
                }
            }

            return response()->json(['html' => view('admin.dashboard.employee.partials.my_customers_modal', compact('customers'))->render()]);
        }

        if ($type === 'projects') {
            // --- MY PROJECTS ---
            $query = DB::table('lead_product_lists')
                ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
                ->join('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'lead_product_lists.alternative_id')
                ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
                ->where(function($q) use ($employeeId) {
                    $q->where('lead_product_lists.employee_id', $employeeId)
                      ->orWhere('lead_product_lists.field_employee', $employeeId);
                })
                ->whereNull('lead_product_lists.deleted_at');

            // Apply Search
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('new_leads.name', 'like', "%{$search}%")
                      ->orWhere('new_leads.lastname', 'like', "%{$search}%")
                      // FIX: changed 'company_name' to 'firma'
                      ->orWhere('new_leads.firma', 'like', "%{$search}%") 
                      ->orWhere('article_groups.article_group', 'like', "%{$search}%")
                      ->orWhere('lead_alternative_adds.street', 'like', "%{$search}%");
                });
            }

            $projects = $query->select(
                    'lead_product_lists.id as project_id',
                    'lead_product_lists.status',
                    'lead_product_lists.stage',
                    'lead_product_lists.stage_history',
                    'article_groups.article_group as product_name',
                    'new_leads.name as customer_name',
                    'new_leads.lastname as customer_lastname',
                    // FIX: changed 'company_name' to 'firma'
                    'new_leads.firma as customer_company', 
                    'lead_alternative_adds.street',
                    'lead_alternative_adds.city'
                )
                ->get()
                ->map(function($proj) {
                    $history = json_decode($proj->stage_history, true);
                    $proj->last_history = (is_array($history) && count($history) > 0) ? end($history) : null;
                    return $proj;
                });

            return response()->json(['html' => view('admin.dashboard.employee.partials.my_projects_modal', compact('projects'))->render()]);
        }

        return response()->json(['error' => 'Invalid type'], 400);
    }

    public function overdue48hPartial()
    {
        return view('admin.dashboard.employee.partials.overdue48h');
    }

    public function personalHoursChart(Request $request)
    {
        $employeeId = auth()->user()->name;

        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        $planIds = TimeManagementPlan::where('employee_id', $employeeId)
            ->where(function ($q) use ($startOfWeek, $endOfWeek) {
                $q->where(function ($qq) use ($startOfWeek) {
                    $qq->where('year', $startOfWeek->year)
                        ->where('month', $startOfWeek->month);
                })
                    ->orWhere(function ($qq) use ($endOfWeek) {
                        $qq->where('year', $endOfWeek->year)
                            ->where('month', $endOfWeek->month);
                    });
            })
            ->pluck('id');

        $entries = TimeManagementEntry::whereIn('plan_id', $planIds)
            ->whereBetween('work_date', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString(),
            ])
            ->get();

        $days = collect();

        for ($day = $startOfWeek->copy(); $day->lte($endOfWeek); $day->addDay()) {
            $dayKey = $day->toDateString();

            $dayEntries = $entries->filter(function ($entry) use ($dayKey) {
                return Carbon::parse($entry->work_date)->toDateString() === $dayKey;
            });

            $officeHours = round((float) $dayEntries->sum('hours'), 2);
            $montageHours = 0;

            $days->push([
                'date' => $dayKey,
                'label' => $day->locale('de')->isoFormat('dd'),
                'office' => $officeHours,
                'montage' => $montageHours,
                'total' => $officeHours + $montageHours,
            ]);
        }

        return response()->json([
            'success' => true,
            'range' => [
                'start' => $startOfWeek->toDateString(),
                'end' => $endOfWeek->toDateString(),
            ],
            'labels' => $days->pluck('label')->values(),
            'office' => $days->pluck('office')->values(),
            'montage' => $days->pluck('montage')->values(),
            'total' => $days->pluck('total')->values(),
            'summary' => [
                'office' => round($days->sum('office'), 2),
                'montage' => round($days->sum('montage'), 2),
                'total' => round($days->sum('total'), 2),
            ],
        ]);
    }
    public function miniAnalyticsChart(Request $request)
    {
        $employeeId = auth()->user()->name;

        $date = $request->filled('date')
            ? Carbon::parse($request->date)->startOfDay()
            : Carbon::today();

        $doneLower = ['completed', 'complete', 'done', 'end', 'pause', 'rejected', 'reject', 'confirm'];

        $tasksCount = DB::table('employees_personal_tasks as ept')
            ->join('personal_tasks as pt', 'ept.task_id', '=', 'pt.id')
            ->where('ept.employee_id', $employeeId)
            ->whereNull('pt.deleted_at')
            ->whereDate('pt.due_date', $date)
            ->whereNotIn(DB::raw('LOWER(COALESCE(ept.status,""))'), $doneLower)
            ->whereNotIn(DB::raw('LOWER(COALESCE(pt.task_status,""))'), $doneLower)
            ->count();

        $appointmentsCount = DB::table('main_appointment_employees as mae')
            ->join('main_appointments as a', 'mae.appointment_id', '=', 'a.id')
            ->where('mae.employee_id', $employeeId)
            ->whereNull('a.deleted_at')
            ->where(function ($q) use ($date) {
                $q->whereDate('a.start_date', $date)
                    ->orWhereDate('a.end_date', $date);
            })
            ->where(function ($q) use ($doneLower) {
                $q->whereNull('mae.status')
                    ->orWhereNotIn(DB::raw('LOWER(COALESCE(mae.status,""))'), $doneLower);
            })
            ->count();

        $ticketsCount = DB::table('employee_problem as ep')
            ->join('problems as p', 'p.id', '=', 'ep.problem_id')
            ->where('ep.employee_id', $employeeId)
            ->whereNull('p.deleted_at')
            ->whereNotIn(DB::raw('LOWER(COALESCE(p.status,""))'), ['end', 'done', 'completed', 'complete'])
            ->whereDate(DB::raw('COALESCE(p.end_date, p.progress_date, p.date)'), $date)
            ->count();

        $ticketTasksCount = DB::table('ticket_tasks as tt')
            ->where('tt.employee_id', $employeeId)
            ->whereDate('tt.due_date', $date)
            ->whereNotIn(DB::raw('LOWER(COALESCE(tt.status,""))'), ['done', 'completed', 'complete', 'end'])
            ->count();

        $leadsCount = DB::table('lead_alternative_adds as la')
            ->join('new_leads as nl', 'la.lead_id', '=', 'nl.id')
            ->join('lead_product_lists as lpl', function ($join) {
                $join->on('lpl.customer_id', '=', 'nl.id')
                    ->on('lpl.alternative_id', '=', 'la.id');
            })
            ->where(function ($q) use ($employeeId) {
                $q->where('lpl.employee_id', $employeeId)
                    ->orWhere('lpl.field_employee', $employeeId);
            })
            ->whereNull('lpl.deleted_at')
            ->whereDate('la.request_date', $date)
            ->whereIn('lpl.status', ['lead', 'new'])
            ->count();

        $inquiriesCount = DB::table('inquiry_product_lists as ipl')
            ->join('inquiries as i', 'ipl.inquiry_id', '=', 'i.id')
            ->whereNull('i.deleted_at')
            ->where(function ($q) use ($employeeId) {
                $q->where('ipl.employee_id', $employeeId)
                    ->orWhere('ipl.field_employee', $employeeId);
            })
            ->whereDate('ipl.appointment_date', $date)
            ->whereNotIn(DB::raw('LOWER(COALESCE(ipl.status,""))'), $doneLower)
            ->count();

        return response()->json([
            'success' => true,
            'date' => $date->toDateString(),
            'labels' => [
                'Aufgaben',
                'Termine',
                'Tickets',
                'Ticket-Tasks',
                'Leads',
                'Anfragen',
            ],
            'values' => [
                $tasksCount,
                $appointmentsCount,
                $ticketsCount,
                $ticketTasksCount,
                $leadsCount,
                $inquiriesCount,
            ],
            'summary' => [
                'total' => $tasksCount + $appointmentsCount + $ticketsCount + $ticketTasksCount + $leadsCount + $inquiriesCount,
                'tasks' => $tasksCount,
                'appointments' => $appointmentsCount,
                'tickets' => $ticketsCount,
                'ticket_tasks' => $ticketTasksCount,
                'leads' => $leadsCount,
                'inquiries' => $inquiriesCount,
            ],
        ]);
    }

    public function hrWidget(Request $request)
    {
        $employeeId = auth()->user()->name;
        $year = (int) $request->get('year', now()->year);

        $employee = Employee::query()
            ->select([
                'id',
                'name',
                'lastname',
                'remaining_day',
                'leave',
                'sick_leave',
                'sick_leave_remaining',
                'trainee',
            ])
            ->where('id', $employeeId)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Mitarbeiter wurde nicht gefunden.',
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Urlaub
        |--------------------------------------------------------------------------
        */

        $approvedLeaves = Leave::query()
            ->where('emp_id', $employeeId)
            ->where('year', $year)
            ->where(function ($query) {
                $query->whereIn('status', ['approved', 'accepted', 'accept'])
                    ->orWhere('approved', 1)
                    ->orWhere('approved', '1')
                    ->orWhere('approved', 'Yes')
                    ->orWhere('approved', 'yes');
            })
            ->get();

        $usedVacationDays = $approvedLeaves->sum(function ($leave) {
            if (!empty($leave->duration)) {
                return (float) $leave->duration;
            }

            if (!empty($leave->leave_day)) {
                return (float) $leave->leave_day;
            }

            if (!empty($leave->start_date) && !empty($leave->end_date)) {
                return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
            }

            return 0;
        });

        $annualLeaveTotal = (float) ($employee->leave ?? 0);
        $remainingVacationDays = $employee->remaining_day !== null
            ? (float) $employee->remaining_day
            : max(0, $annualLeaveTotal - $usedVacationDays);

        $nextLeave = Leave::query()
            ->where('emp_id', $employeeId)
            ->whereDate('start_date', '>=', now()->toDateString())
            ->where(function ($query) {
                $query->whereIn('status', ['approved', 'accepted', 'accept', 'pending'])
                    ->orWhere('approved', 1)
                    ->orWhere('approved', '1')
                    ->orWhereNull('status');
            })
            ->orderBy('start_date')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Krankheit
        |--------------------------------------------------------------------------
        */

        $sickRows = EmployeeSick::query()
            ->where('emp_id', $employeeId)
            ->where('year', $year)
            ->get();

        $sickDays = $sickRows->sum(function ($sick) {
            if (!empty($sick->total_days)) {
                return (float) $sick->total_days;
            }

            if (!empty($sick->start_date) && !empty($sick->end_date)) {
                return Carbon::parse($sick->start_date)->diffInDays(Carbon::parse($sick->end_date)) + 1;
            }

            return 0;
        });

        $latestSick = $sickRows
            ->sortByDesc(function ($sick) {
                return $sick->created_at ?? $sick->start_date;
            })
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Wiederkehrende Abwesenheiten
        |--------------------------------------------------------------------------
        */

        $today = now()->startOfDay();
        $rangeStart = now()->startOfMonth();
        $rangeEnd = now()->endOfMonth();

        $recurringLeaves = EmployeeRecurringLeave::query()
            ->where('employee_id', $employeeId)
            ->where('is_active', true)
            ->with(['exdates', 'overrides'])
            ->get();

        $recurringOccurrences = [];

        foreach ($recurringLeaves as $recurringLeave) {
            $items = $recurringLeave->generateOccurrences(
                $rangeStart->copy(),
                $rangeEnd->copy()
            );

            foreach ($items as $item) {
                $recurringOccurrences[] = [
                    'id' => $recurringLeave->id,
                    'title' => $item['title'] ?? $recurringLeave->title ?? 'Wiederkehrend',
                    'type' => $recurringLeave->type,
                    'date' => $item['date'],
                    'start_time' => $item['start_time'] ?? null,
                    'end_time' => $item['end_time'] ?? null,
                    'all_day' => (bool) ($item['all_day'] ?? true),
                    'description' => $item['description'] ?? null,
                ];
            }
        }

        $recurringOccurrences = collect($recurringOccurrences)
            ->sortBy('date')
            ->values()
            ->take(6)
            ->values();

        $todayRecurring = $recurringOccurrences
            ->filter(fn($item) => $item['date'] === $today->toDateString())
            ->values();

        $recurringSummary = $recurringOccurrences
            ->groupBy('title')
            ->map(function ($items, $title) {
                $first = $items->first();

                return [
                    'title' => $title,
                    'type' => $first['type'] ?? null,
                    'next_date' => $first['date'] ?? null,
                    'count_this_month' => $items->count(),
                ];
            })
            ->values()
            ->take(3)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | HR Hinweise / Dokumentstatus
        |--------------------------------------------------------------------------
        | If you have HR document tables, add them here.
        | For now we create a useful message based on available data.
        */

        $messages = [];

        if ($nextLeave) {
            $messages[] = [
                'type' => 'leave',
                'label' => 'Nächster Urlaub',
                'message' => 'Dein nächster Urlaub beginnt am ' . Carbon::parse($nextLeave->start_date)->format('d.m.Y') . '.',
            ];
        }

        if ($latestSick && !empty($latestSick->document)) {
            $messages[] = [
                'type' => 'sick_document',
                'label' => 'Krankmeldung',
                'message' => 'Letzte Krankmeldung wurde mit Dokument gespeichert.',
            ];
        }

        if ($todayRecurring->count() > 0) {
            $messages[] = [
                'type' => 'recurring',
                'label' => 'Heute',
                'message' => $todayRecurring->pluck('title')->unique()->implode(', ') . ' ist heute eingetragen.',
            ];
        }

        if (empty($messages)) {
            $messages[] = [
                'type' => 'info',
                'label' => 'Info',
                'message' => 'Keine neuen HR-Dokumente vorhanden.',
            ];
        }

        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
            ],
            'year' => $year,
            'vacation' => [
                'annual_total' => $annualLeaveTotal,
                'used' => $usedVacationDays,
                'remaining' => $remainingVacationDays,
                'next_leave' => $nextLeave ? [
                    'id' => $nextLeave->id,
                    'start_date' => optional($nextLeave->start_date)->format('Y-m-d') ?: $nextLeave->start_date,
                    'end_date' => optional($nextLeave->end_date)->format('Y-m-d') ?: $nextLeave->end_date,
                    'duration' => $nextLeave->duration,
                    'status' => $nextLeave->status,
                    'reason' => $nextLeave->reason,
                ] : null,
            ],
            'sick' => [
                'days' => $sickDays,
                'remaining' => $employee->sick_leave_remaining,
                'total_allowed' => $employee->sick_leave,
                'latest' => $latestSick ? [
                    'id' => $latestSick->id,
                    'start_date' => $latestSick->start_date,
                    'end_date' => $latestSick->end_date,
                    'status' => $latestSick->status,
                    'document' => $latestSick->document,
                ] : null,
            ],
            'recurring' => [
                'today' => $todayRecurring,
                'summary' => $recurringSummary,
                'month_items' => $recurringOccurrences,
            ],
            'messages' => $messages,
        ]);
    }
}
