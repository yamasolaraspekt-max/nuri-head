<?php

namespace App\Http\Controllers\Appointment;
use App\Http\Controllers\Controller;

use App\Models\MainAppointment;
use App\Models\MainAppointmentEmployee;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\BranchAddress;
use App\Models\NewLeads;
use App\Models\Department;
use App\Notifications\AppointmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\AppointmentReport;
use App\Models\AppointmentComment;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use App\Models\Inquiry;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class CustomerMainAppointmentController extends Controller
{
    public function index()
    {
        // better: use employee_id, not name
        $employeeId = $this->currentEmployeeId(); // instead of auth()->user()->name

        $calendarSettings = DB::table('personal_settings')
            ->where('employee_id', $employeeId)
            ->value('calendar_settings');

        $favoriteEmployeeIds = [];

        if ($calendarSettings) {
            $parsed = json_decode($calendarSettings, true);
            if (is_array($parsed) && isset($parsed['favorite_employees']) && is_array($parsed['favorite_employees'])) {
                $favoriteEmployeeIds = array_map('intval', $parsed['favorite_employees']);
            }
        }

        $data = [];

        $data['favorite_employee_ids'] = $favoriteEmployeeIds;

        $data['employees'] = Employee::select('id', 'name', 'lastname', 'image', 'gender')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        $data['customers'] = NewLeads::whereNull('deleted_at')
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
                'phone'
            ]);

        $data['branches'] = Branch::where('status', 'Published')
            ->orderBy('branch', 'asc')
            ->get(['id', 'branch']);

        $data['departments'] = Department::where('status', 'published')
            ->orderBy('department_name', 'asc')
            ->get(['id', 'department_name', 'status']);

        $data['products'] = DB::table('article_groups')
            ->select('id', 'article_group')
            ->orderBy('article_group', 'asc')
            ->get();

        $data['services'] = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section', 'status', 'deleted_at')
            ->whereNull('deleted_at')
            ->orderBy('phase_section', 'asc')
            ->get();

        $data['task_employee'] = MainAppointmentEmployee::join(
            'employees',
            'employees.id',
            '=',
            'main_appointment_employees.employee_id'
        )
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

        $data['allEmployees'] = Employee::select('id', 'name', 'lastname', 'image')
            ->where('status', '!=', 'Inactive')
            ->orderBy('name', 'asc')
            ->get();

        $data['branch_addresses'] = DB::table('branch_addresses')
            ->join('branches', 'branches.id', '=', 'branch_addresses.branch_id')
            ->select('branch_addresses.*', 'branches.initial as branch_initial')
            ->whereNull('branch_addresses.deleted_at')
            ->get();

        return view('admin.appointments.index', $data);
    }

    /**
     * AJAX data provider for Kanban + List
     */
    public function data(Request $request)
    {
        $employeeId = $this->currentEmployeeId();

        $query = MainAppointment::query()
            ->with([
                'employees:id,name,lastname,image,gender',
                'customer:id,name,lastname,street,postcode,city,latitude,longitude,phone,email',
                'createdBy:id,name,lastname,image',
            ])
            ->withCount([
                'reports as reports_count',
                'comments as comments_count',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Ownership scope
        |--------------------------------------------------------------------------
        | assigned_to_me = Termine, die für mich/mit mir erstellt wurden
        | created_by_me = Termine, die ich selbst erstellt habe
        | all_mine       = beide Gruppen zusammen
        |--------------------------------------------------------------------------
        */
        $ownerScope = $request->get('owner_scope', 'assigned_to_me');

        if ($employeeId) {
            switch ($ownerScope) {
                case 'created_by_me':
                    $query->where('created_by', $employeeId);
                    break;

                case 'all_mine':
                case 'all_related':
                    $query->where(function ($q) use ($employeeId) {
                        $q->where('created_by', $employeeId)
                            ->orWhereHas('employees', function ($qq) use ($employeeId) {
                                $qq->where('employees.id', $employeeId);
                            });
                    });
                    break;

                case 'assigned_to_me':
                default:
                    $query->whereHas('employees', function ($q) use ($employeeId) {
                        $q->where('employees.id', $employeeId);
                    });
                    break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Status tab filter
        |--------------------------------------------------------------------------
        | This is separated from owner_scope. So the user can first choose:
        | "Für mich erstellt" / "Von mir erstellt", and then filter by status.
        |--------------------------------------------------------------------------
        */
        if ($tab = $request->get('tab')) {
            $today = Carbon::today()->toDateString();

            switch ($tab) {
                case 'mine':
                case 'open':
                    $query->whereNotIn('status', ['archived', 'junk', 'deleted']);
                    break;

                case 'delayed':
                    $query->whereNotIn('status', ['done', 'archived', 'canceled', 'junk', 'deleted'])
                        ->whereDate('start_date', '<=', $today)
                        ->whereNotNull('end_date')
                        ->whereDate('end_date', '<', $today);
                    break;

                case 'due':
                    $query->whereNotIn('status', ['done', 'archived', 'canceled', 'junk', 'deleted'])
                        ->whereDate('start_date', $today);
                    break;

                case 'archived':
                    $query->where('status', 'archived');
                    break;

                case 'junk':
                    $query->where('status', 'junk');
                    break;

                case 'deleted':
                    $query->where('status', 'deleted');
                    break;

                case 'all':
                default:
                    break;
            }
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('appointment_type', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('createdBy', function ($q3) use ($search) {
                        $q3->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%");
                    });
            });
        }

        if ($from = $request->get('from_date')) {
            $query->whereDate('start_date', '>=', $from);
        }

        if ($to = $request->get('to_date')) {
            $query->whereDate('start_date', '<=', $to);
        }

        /*
        |--------------------------------------------------------------------------
        | Additional employee filter
        |--------------------------------------------------------------------------
        | This filters by participant employee, but it does NOT replace owner_scope.
        |--------------------------------------------------------------------------
        */
        if ($employeeFilterId = $request->get('employee_id')) {
            $query->whereHas('employees', function ($q) use ($employeeFilterId) {
                $q->where('employees.id', $employeeFilterId);
            });
        }

        if ($branchId = $request->get('branch_id')) {
            $query->where('branch_id', $branchId);
        }

        $sort = $request->get('sort', 'start_date');
        $direction = $request->get('direction', 'asc');

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }

        if (in_array($sort, ['name', 'start_date', 'end_date', 'status', 'priority', 'city', 'created_by'], true)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('start_date', 'asc');
        }

        $appointments = $query->get()->map(function (MainAppointment $a) use ($employeeId) {
            $customer = $a->customer;
            $createdBy = $a->createdBy;

            $isAssignedToMe = $employeeId
                ? $a->employees->contains(fn($e) => (int) $e->id === (int) $employeeId)
                : false;

            $isCreatedByMe = $employeeId
                ? ((int) $a->created_by === (int) $employeeId)
                : false;

            $ownerLabel = null;

            if ($isCreatedByMe && $isAssignedToMe) {
                $ownerLabel = 'Von mir & für mich';
            } elseif ($isCreatedByMe) {
                $ownerLabel = 'Von mir erstellt';
            } elseif ($isAssignedToMe) {
                $ownerLabel = 'Für mich erstellt';
            }

            return [
                'id' => $a->id,
                'name' => $a->name,
                'appointment_type' => $a->appointment_type,
                'customer_id' => $a->customer_id,
                'customer_name' => $customer ? trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? '')) : null,
                'street' => $a->street ?? ($customer->street ?? null),
                'postcode' => $a->postcode ?? ($customer->postcode ?? null),
                'city' => $a->city ?? ($customer->city ?? null),
                'latitude' => $a->latitude ?? ($customer->latitude ?? null),
                'longitude' => $a->longitude ?? ($customer->longitude ?? null),
                'phone' => $a->phone ?? ($customer->phone ?? null),
                'email' => $a->email ?? ($customer->email ?? null),
                'start_date' => optional($a->start_date)->toDateString(),
                'end_date' => optional($a->end_date)->toDateString(),
                'start_time' => $a->start_time,
                'end_time' => $a->end_time,
                'total_time' => $a->total_time,
                'status' => $a->status,
                'priority' => $a->priority,
                'color' => $a->color,
                'execution_type' => $a->execution_type,
                'link' => $a->link,
                'description' => $a->description,
                'branch_id' => $a->branch_id,
                'branch_address_id' => $a->branch_address_id,

                'created_by' => $a->created_by,
                'created_by_name' => $createdBy
                    ? trim(($createdBy->name ?? '') . ' ' . ($createdBy->lastname ?? ''))
                    : null,
                'is_created_by_me' => $isCreatedByMe,
                'is_assigned_to_me' => $isAssignedToMe,
                'owner_label' => $ownerLabel,

                'reports_count' => (int) $a->reports_count,
                'comments_count' => (int) $a->comments_count,

                'employees' => $a->employees->map(function ($e) {
                    return [
                        'id' => $e->id,
                        'name' => $e->name,
                        'lastname' => $e->lastname,
                        'full_name' => trim(($e->name ?? '') . ' ' . ($e->lastname ?? '')),
                        'image' => $e->image,
                        'gender' => $e->gender,
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'data' => $appointments,
        ]);
    }



    public function store(Request $request)
    {
        $validated = $this->validateAppointment($request);

        $employeeId = $this->currentEmployeeId();
        if ($employeeId) {
            $validated['created_by'] = $employeeId;
        }

        $appointment = MainAppointment::create($validated);

        if ($request->filled('employee')) {
            $ids = (array) $request->input('employee');
            foreach ($ids as $empId) {
                MainAppointmentEmployee::firstOrCreate([
                    'employee_id' => $empId,
                    'appointment_id' => $appointment->id,
                ], [
                    'status' => 'send',
                ]);
            }
        }

        $this->sendAppointmentNotification(
            $appointment,
            'Neuer Termin erstellt',
            'Der Termin „' . $appointment->name . '“ wurde erstellt.',
            'created'
        );

        return response()->json(['success' => true, 'id' => $appointment->id]);
    }

    public function getEvents(Request $request)
    {
        $cid = $request->customer_id;

        // Optional: Use start/end dates from request to optimize query
        // $start = $request->start; 
        // $end = $request->end;

        $appointments = DB::table('main_appointments')
            ->where('customer_id', $cid)
            // ->whereBetween('start_date', [$start, $end]) // Optional optimization
            ->get();

        $events = $appointments->map(function ($app) {
            return [
                'id' => $app->id,
                'title' => $app->name,
                'start' => $app->start_date . ($app->start_time ? 'T' . $app->start_time : ''),
                'end' => $app->end_date . ($app->end_time ? 'T' . $app->end_time : ''),
                'color' => $app->color ?? '#0d6efd',
                'allDay' => !$app->start_time,
                'extendedProps' => [
                    'note' => $app->note
                ]
            ];
        });

        return response()->json($events);
    }

    public function update(Request $request, MainAppointment $appointment)
    {
        $validated = $this->validateAppointment($request, $appointment->id);
        $appointment->update($validated);

        $this->sendAppointmentNotification(
            $appointment,
            'Termin bearbeitet',
            'Der Termin „' . $appointment->name . '“ wurde bearbeitet.',
            'updated'
        );

        return response()->json(['success' => true]);
    }

    public function destroy(MainAppointment $appointment)
    {
        $appointment->delete();
        return response()->json(['success' => true]);
    }

    public function restore($id)
    {
        $appointment = MainAppointment::onlyTrashed()->findOrFail($id);
        $appointment->restore();

        return response()->json(['success' => true]);
    }

    public function forceDelete($id)
    {
        $appointment = MainAppointment::onlyTrashed()->findOrFail($id);
        $appointment->forceDelete();

        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, MainAppointment $appointment)
    {
        $request->validate([
            'status' => 'required|string|max:64',
        ]);

        $oldStatus = $appointment->status;
        $appointment->status = $request->input('status');

        if ($appointment->status === 'done' && $request->boolean('archive')) {
            $appointment->status = 'archived';
        }

        $appointment->save();

        $this->sendAppointmentNotification(
            $appointment,
            'Termin-Status geändert',
            'Status von „' . $oldStatus . '“ zu „' . $appointment->status . '“ geändert (Termin „' . $appointment->name . '“).',
            'status'
        );

        return response()->json(['success' => true]);
    }

    /**
     * Example: notify all “today due” appointments – call via scheduler or route.
     */
    public function notifyDueToday()
    {
        $today = now()->toDateString();

        $appointments = MainAppointment::whereDate('start_date', $today)
            ->whereNotIn('status', ['done', 'archived', 'canceled', 'junk'])
            ->get();

        foreach ($appointments as $appointment) {
            $this->sendAppointmentNotification(
                $appointment,
                'Termin heute fällig',
                'Der Termin „' . $appointment->name . '“ ist heute fällig.',
                'due'
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * AJAX: last notifications for current user (for ticker)
     */
    public function notifications(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['data' => []]);
        }

        $limit = (int) $request->get('limit', 10);

        $notifications = $user->notifications()
            ->where('type', AppointmentNotification::class)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        $data = $notifications->map(function ($n) {
            $d = $n->data ?? [];

            return [
                'id' => $n->id,
                'title' => $d['title'] ?? 'Terminbenachrichtigung',
                'message' => $d['message'] ?? '',
                'appointment_id' => $d['appointment_id'] ?? null,
                'kind' => $d['kind'] ?? 'generic',
                'is_read' => $n->read_at !== null,
                'created_at' => $n->created_at->diffForHumans(),
                'created_at_raw' => $n->created_at->toDateTimeString(),
            ];
        });

        return response()->json(['data' => $data]);
    }


    protected function validateAppointment(Request $request, $id = null)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'execution_type' => 'nullable|string|max:255',
            'appointment_type' => 'nullable|string|max:255',
            'contact_mode' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'color' => 'nullable|string|max:32',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'total_time' => 'nullable|numeric',
            'date_type' => 'nullable|string|max:64',
            'from_day' => 'nullable|string|max:32',
            'to_day' => 'nullable|string|max:32',
            'from_month' => 'nullable|string|max:32',
            'to_month' => 'nullable|string|max:32',
            'repeat' => 'nullable|string|max:64',
            'reminder_date' => 'nullable|date',
            'reminder_time' => 'nullable',
            'next_step' => 'nullable|string|max:255',
            'change_date' => 'nullable|date',
            'change_reason' => 'nullable|string',
            'full_address' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'postcode' => 'nullable|string|max:32',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:64',
            'email' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:64',
            'link' => 'nullable|string|max:255',
            'priority' => 'nullable|string|max:64',
            'public' => 'nullable|string|max:64',
            'branch_id' => 'nullable|exists:branches,id',
            'branch_address_id' => 'nullable|exists:branch_addresses,id',
            'customer_id' => 'nullable|exists:new_leads,id',
            'task_id' => 'nullable|exists:personal_tasks,id',
            'problem_id' => 'nullable|exists:problems,id',
            'problem_task_id' => 'nullable|exists:ticket_tasks,id',
            'report_responsible' => 'nullable|json',
            'products' => 'nullable|json',
            'product_inquiry' => 'nullable|json',
        ]);
    }

    protected function currentEmployeeId(): ?int
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        // Prefer real column if it exists
        if (!empty($user->name)) {
            return (int) $user->name;
        }

        // Fallback: numeric user->name (old variant)
        if (is_numeric($user->name)) {
            return (int) $user->name;
        }

        return null;
    }

    /**
     * Send notification to creator + assigned employees (mapped to User via name = employee_id).
     */
    protected function sendAppointmentNotification(MainAppointment $appointment, string $title, string $message, string $kind = 'generic'): void
    {
        $payload = [
            'title' => $title,
            'message' => $message,
            'appointment_id' => $appointment->id,
            'kind' => $kind,
        ];

        $notifiables = collect();

        if ($u = Auth::user()) {
            $notifiables->push($u);
        }

        $employeeIds = $appointment->employees()->pluck('employees.id')->all();
        if (!empty($employeeIds)) {
            $employeeUsers = User::whereIn('name', $employeeIds)->get();
            $notifiables = $notifiables->merge($employeeUsers);
        }

        $notifiables->unique('id')->each(function ($user) use ($payload) {
            $user->notify(new AppointmentNotification($payload));
        });
    }


    public function addReportCommentItem(
        Request $request,
        MainAppointment $appointment,
        AppointmentReport $report
    ) {
        $validated = $request->validate([
            'text' => 'required|string|max:10000',
        ]);

        $user = Auth::user();
        $employeeId = $user->name;

        // Add raw item to JSON column
        $report->addCommentItem($employeeId, $validated['text']);

        // Enrich all items with employee name for the response
        $rawItems = $report->comment_items ?? [];

        $items = collect($rawItems)->map(function ($item) {
            // depending on how you store it (by / employee_id)
            $idKey = $item['employee_id'] ?? ($item['by'] ?? null);
            $employee = $idKey ? Employee::find($idKey) : null;
            $fullName = $employee
                ? trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))
                : ('Mitarbeiter #' . ($idKey ?? '?'));

            return [
                'employee_id' => $idKey,
                'employee_name' => $fullName,
                'text' => $item['text'] ?? '',
                'created_at' => $item['created_at'] ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'commentItems' => $items,
        ]);
    }


    /**
     * Store a new comment for this appointment (AJAX).
     */
    public function storeComment(Request $request, MainAppointment $appointment)
    {
        $request->validate([
            'comment' => 'required|string',
            'parent_id' => 'nullable|exists:appointment_comments,id',
        ]);

        $employeeId = $this->currentEmployeeId();

        $comment = AppointmentComment::create([
            'appointment_id' => $appointment->id,
            'comment_by' => $employeeId,
            'comment' => $request->input('comment'),
            'parent_id' => $request->input('parent_id'),
        ]);

        $comment->load('employee:id,name,lastname,image');

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at->diffForHumans(),
                'employee' => [
                    'id' => $comment->employee->id,
                    'name' => $comment->employee->name,
                    'lastname' => $comment->employee->lastname,
                    'image' => $comment->employee->image,
                ],
            ],
        ]);
    }

    /**
     * Notifications list for this appointment (AJAX).
     */
    public function appointmentNotifications(MainAppointment $appointment)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['data' => []]);
        }

        $items = $user->notifications()
            ->where('type', AppointmentNotification::class)
            ->where('data->appointment_id', $appointment->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function ($n) {
                $d = $n->data ?? [];
                return [
                    'id' => $n->id,
                    'title' => $d['title'] ?? 'Terminbenachrichtigung',
                    'message' => $d['message'] ?? '',
                    'kind' => $d['kind'] ?? 'generic',
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json(['data' => $items]);
    }

    public function addEmployee(Request $request, MainAppointment $appointment)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $pivot = MainAppointmentEmployee::firstOrCreate(
            [
                'appointment_id' => $appointment->id,
                'employee_id' => $request->employee_id,
            ],
            [
                'status' => 'send',
            ]
        );

        // optional: eager-load just added employee to return for UI
        $employee = Employee::select('id', 'name', 'lastname', 'image')
            ->find($request->employee_id);

        return response()->json([
            'success' => true,
            'message' => 'Mitarbeiter hinzugefügt.',
            'pivot_id' => $pivot->id,
            'employee' => $employee ? [
                'id' => $employee->id,
                'name' => $employee->name,
                'lastname' => $employee->lastname,
                'full_name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                'avatar_url' => $employee->image
                    ? asset('images/employee/' . $employee->image)
                    : asset('images/gender/male.png'),
            ] : null,
        ]);
    }

    public function removeEmployee(MainAppointment $appointment, Employee $employee)
    {
        MainAppointmentEmployee::where('appointment_id', $appointment->id)
            ->where('employee_id', $employee->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mitarbeiter entfernt.',
        ]);
    }

    public function show(MainAppointment $appointment)
    {
        /*
        |--------------------------------------------------------------------------
        | Main appointment relations
        |--------------------------------------------------------------------------
        | Keep relation loading limited to relations that already exist on MainAppointment.
        */
        $appointment->load([
            'customer',
            'employees',
            'branch',
            'branchAddress',
            'createdBy:id,name,lastname,image',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Small helper: select only existing columns
        |--------------------------------------------------------------------------
        | This prevents errors like:
        | SQLSTATE[42S22]: Column not found: 1054 Unknown column 'type'
        */
        $existingSelects = function (string $table, array $columns): array {
            return collect($columns)
                ->filter(function ($column) use ($table) {
                    $columnName = $column;

                    if (str_contains($columnName, ' as ')) {
                        $columnName = trim(str($columnName)->before(' as ')->toString());
                    }

                    if (str_contains($columnName, '.')) {
                        $columnName = trim(str($columnName)->afterLast('.')->toString());
                    }

                    return \Illuminate\Support\Facades\Schema::hasColumn($table, $columnName);
                })
                ->values()
                ->all();
        };

        /*
        |--------------------------------------------------------------------------
        | Safe customer/contact context for profile
        |--------------------------------------------------------------------------
        | Priority:
        | 1. main_appointments.customer_id -> new_leads
        | 2. main_appointments.contact_id  -> new_leads
        | 3. main_appointments.contact_id  -> inquiries
        |--------------------------------------------------------------------------
        */
        $appointmentCustomer = null;

        $newLeadColumns = $existingSelects('new_leads', [
            'id',
            'name',
            'lastname',
            'customer_type',
            'phone',
            'telephone',
            'email',
            'street',
            'postcode',
            'city',
            'full_address',
            'latitude',
            'longitude',
        ]);

        if (!in_array('id', $newLeadColumns, true)) {
            $newLeadColumns[] = 'id';
        }

        if (!empty($appointment->customer_id) && \Illuminate\Support\Facades\Schema::hasTable('new_leads')) {
            $query = DB::table('new_leads')->where('id', $appointment->customer_id);

            if (\Illuminate\Support\Facades\Schema::hasColumn('new_leads', 'deleted_at')) {
                $query->whereNull('deleted_at');
            }

            $appointmentCustomer = $query->select($newLeadColumns)->first();
        }

        if (
            !$appointmentCustomer
            && !empty($appointment->contact_id)
            && \Illuminate\Support\Facades\Schema::hasTable('new_leads')
        ) {
            $query = DB::table('new_leads')->where('id', $appointment->contact_id);

            if (\Illuminate\Support\Facades\Schema::hasColumn('new_leads', 'deleted_at')) {
                $query->whereNull('deleted_at');
            }

            $appointmentCustomer = $query->select($newLeadColumns)->first();
        }

        if (
            !$appointmentCustomer
            && !empty($appointment->contact_id)
            && \Illuminate\Support\Facades\Schema::hasTable('inquiries')
        ) {
            $inquiryColumns = $existingSelects('inquiries', [
                'id',
                'title as name',
                'lastname',
                'type',
                'phone',
                'telephone',
                'email',
                'street',
                'postcode',
                'city',
                'full_address',
                'latitude',
                'longitude',
            ]);

            if (!in_array('id', $inquiryColumns, true)) {
                $inquiryColumns[] = 'id';
            }

            $appointmentCustomer = DB::table('inquiries')
                ->where('id', $appointment->contact_id)
                ->select($inquiryColumns)
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | CRM Stage/Sub Stage context
        |--------------------------------------------------------------------------
        | Priority:
        | 1. main_appointments columns
        | 2. linked personal_tasks columns
        | 3. linked lead_product_lists status/sub-stage
        |--------------------------------------------------------------------------
        */
        $appointmentStageContext = [
            'lead_product_list_id' => $appointment->lead_product_list_id ? (int) $appointment->lead_product_list_id : null,
            'lead_stage_id' => $appointment->lead_stage_id ? (int) $appointment->lead_stage_id : null,
            'lead_stage_name' => null,
            'lead_stage_color' => null,
            'lead_stage_key' => null,

            'lead_stage_sub_stage_id' => $appointment->lead_stage_sub_stage_id ? (int) $appointment->lead_stage_sub_stage_id : null,
            'lead_stage_sub_stage_name' => null,
            'lead_stage_sub_stage_color' => null,
            'lead_stage_sub_stage_key' => null,

            'product_id' => null,
            'product_name' => null,
            'article_group' => null,

            'alternative_id' => null,
            'object_name' => null,
            'object_city' => null,

            'customer_id' => $appointment->customer_id ? (int) $appointment->customer_id : null,
        ];

        if (!empty($appointmentStageContext['lead_stage_id']) && \Illuminate\Support\Facades\Schema::hasTable('lead_stages')) {
            $stage = DB::table('lead_stages')
                ->where('id', $appointmentStageContext['lead_stage_id'])
                ->select(['id', 'key', 'name', 'color'])
                ->first();

            if ($stage) {
                $appointmentStageContext['lead_stage_name'] = $stage->name;
                $appointmentStageContext['lead_stage_color'] = $stage->color;
                $appointmentStageContext['lead_stage_key'] = $stage->key;
            }
        }

        if (!empty($appointmentStageContext['lead_stage_sub_stage_id']) && \Illuminate\Support\Facades\Schema::hasTable('lead_stage_sub_stages')) {
            $subStage = DB::table('lead_stage_sub_stages')
                ->where('id', $appointmentStageContext['lead_stage_sub_stage_id'])
                ->select(['id', 'key', 'name', 'color', 'lead_stage_id'])
                ->first();

            if ($subStage) {
                $appointmentStageContext['lead_stage_sub_stage_name'] = $subStage->name;
                $appointmentStageContext['lead_stage_sub_stage_color'] = $subStage->color;
                $appointmentStageContext['lead_stage_sub_stage_key'] = $subStage->key;

                if (empty($appointmentStageContext['lead_stage_id'])) {
                    $appointmentStageContext['lead_stage_id'] = $subStage->lead_stage_id ? (int) $subStage->lead_stage_id : null;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback from linked PersonalTask
        |--------------------------------------------------------------------------
        */
        if (
            (empty($appointmentStageContext['lead_product_list_id']) || empty($appointmentStageContext['lead_stage_id']))
            && !empty($appointment->task_id)
            && \Illuminate\Support\Facades\Schema::hasTable('personal_tasks')
        ) {
            $linkedTaskStage = DB::table('personal_tasks')
                ->leftJoin('lead_stages as task_stage', 'task_stage.id', '=', 'personal_tasks.lead_stage_id')
                ->leftJoin('lead_stage_sub_stages as task_sub_stage', 'task_sub_stage.id', '=', 'personal_tasks.lead_stage_sub_stage_id')
                ->where('personal_tasks.id', $appointment->task_id)
                ->select([
                    'personal_tasks.lead_product_list_id',
                    'personal_tasks.lead_stage_id',
                    'task_stage.name as lead_stage_name',
                    'task_stage.color as lead_stage_color',
                    'task_stage.key as lead_stage_key',
                    'personal_tasks.lead_stage_sub_stage_id',
                    'task_sub_stage.name as lead_stage_sub_stage_name',
                    'task_sub_stage.color as lead_stage_sub_stage_color',
                    'task_sub_stage.key as lead_stage_sub_stage_key',
                ])
                ->first();

            if ($linkedTaskStage) {
                $appointmentStageContext['lead_product_list_id'] = $appointmentStageContext['lead_product_list_id']
                    ?: ($linkedTaskStage->lead_product_list_id ? (int) $linkedTaskStage->lead_product_list_id : null);

                $appointmentStageContext['lead_stage_id'] = $appointmentStageContext['lead_stage_id']
                    ?: ($linkedTaskStage->lead_stage_id ? (int) $linkedTaskStage->lead_stage_id : null);

                $appointmentStageContext['lead_stage_name'] = $appointmentStageContext['lead_stage_name'] ?: $linkedTaskStage->lead_stage_name;
                $appointmentStageContext['lead_stage_color'] = $appointmentStageContext['lead_stage_color'] ?: $linkedTaskStage->lead_stage_color;
                $appointmentStageContext['lead_stage_key'] = $appointmentStageContext['lead_stage_key'] ?: $linkedTaskStage->lead_stage_key;

                $appointmentStageContext['lead_stage_sub_stage_id'] = $appointmentStageContext['lead_stage_sub_stage_id']
                    ?: ($linkedTaskStage->lead_stage_sub_stage_id ? (int) $linkedTaskStage->lead_stage_sub_stage_id : null);

                $appointmentStageContext['lead_stage_sub_stage_name'] = $appointmentStageContext['lead_stage_sub_stage_name'] ?: $linkedTaskStage->lead_stage_sub_stage_name;
                $appointmentStageContext['lead_stage_sub_stage_color'] = $appointmentStageContext['lead_stage_sub_stage_color'] ?: $linkedTaskStage->lead_stage_sub_stage_color;
                $appointmentStageContext['lead_stage_sub_stage_key'] = $appointmentStageContext['lead_stage_sub_stage_key'] ?: $linkedTaskStage->lead_stage_sub_stage_key;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback/enrichment from LeadProductList
        |--------------------------------------------------------------------------
        */
        if (!empty($appointmentStageContext['lead_product_list_id']) && \Illuminate\Support\Facades\Schema::hasTable('lead_product_lists')) {
            $leadProduct = DB::table('lead_product_lists')
                ->leftJoin('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
                ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'lead_product_lists.alternative_id')
                ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
                ->leftJoin('lead_stages', 'lead_stages.key', '=', 'lead_product_lists.status')
                ->leftJoin('lead_stage_sub_stages', 'lead_stage_sub_stages.id', '=', 'lead_product_lists.lead_stage_sub_stage_id')
                ->where('lead_product_lists.id', $appointmentStageContext['lead_product_list_id'])
                ->select([
                    'lead_product_lists.id as lead_product_list_id',
                    'lead_product_lists.customer_id',
                    'lead_product_lists.alternative_id',
                    'lead_product_lists.product_id',
                    'lead_product_lists.status as lead_stage_key',
                    'lead_product_lists.lead_stage_sub_stage_id',

                    'new_leads.name as customer_name',
                    'new_leads.lastname as customer_lastname',
                    'new_leads.phone as customer_phone',
                    'new_leads.telephone as customer_telephone',
                    'new_leads.email as customer_email',
                    'new_leads.street as customer_street',
                    'new_leads.postcode as customer_postcode',
                    'new_leads.city as customer_city',
                    'new_leads.full_address as customer_full_address',

                    'lead_alternative_adds.object_name',
                    'lead_alternative_adds.city as object_city',

                    'article_groups.article_group as product_name',

                    'lead_stages.id as product_lead_stage_id',
                    'lead_stages.name as product_lead_stage_name',
                    'lead_stages.color as product_lead_stage_color',
                    'lead_stages.key as product_stage_key',

                    'lead_stage_sub_stages.name as product_lead_stage_sub_stage_name',
                    'lead_stage_sub_stages.color as product_lead_stage_sub_stage_color',
                    'lead_stage_sub_stages.key as product_lead_stage_sub_stage_key',
                ])
                ->first();

            if ($leadProduct) {
                $appointmentStageContext['customer_id'] = $appointmentStageContext['customer_id']
                    ?: ($leadProduct->customer_id ? (int) $leadProduct->customer_id : null);

                $appointmentStageContext['alternative_id'] = $leadProduct->alternative_id ? (int) $leadProduct->alternative_id : null;
                $appointmentStageContext['product_id'] = $leadProduct->product_id ? (int) $leadProduct->product_id : null;
                $appointmentStageContext['product_name'] = $leadProduct->product_name;
                $appointmentStageContext['article_group'] = $leadProduct->product_name;
                $appointmentStageContext['object_name'] = $leadProduct->object_name;
                $appointmentStageContext['object_city'] = $leadProduct->object_city;

                $appointmentStageContext['lead_stage_id'] = $appointmentStageContext['lead_stage_id']
                    ?: ($leadProduct->product_lead_stage_id ? (int) $leadProduct->product_lead_stage_id : null);

                $appointmentStageContext['lead_stage_name'] = $appointmentStageContext['lead_stage_name'] ?: $leadProduct->product_lead_stage_name;
                $appointmentStageContext['lead_stage_color'] = $appointmentStageContext['lead_stage_color'] ?: $leadProduct->product_lead_stage_color;
                $appointmentStageContext['lead_stage_key'] = $appointmentStageContext['lead_stage_key'] ?: ($leadProduct->product_stage_key ?: $leadProduct->lead_stage_key);

                $appointmentStageContext['lead_stage_sub_stage_id'] = $appointmentStageContext['lead_stage_sub_stage_id']
                    ?: ($leadProduct->lead_stage_sub_stage_id ? (int) $leadProduct->lead_stage_sub_stage_id : null);

                $appointmentStageContext['lead_stage_sub_stage_name'] = $appointmentStageContext['lead_stage_sub_stage_name'] ?: $leadProduct->product_lead_stage_sub_stage_name;
                $appointmentStageContext['lead_stage_sub_stage_color'] = $appointmentStageContext['lead_stage_sub_stage_color'] ?: $leadProduct->product_lead_stage_sub_stage_color;
                $appointmentStageContext['lead_stage_sub_stage_key'] = $appointmentStageContext['lead_stage_sub_stage_key'] ?: $leadProduct->product_lead_stage_sub_stage_key;

                if (!$appointmentCustomer && !empty($leadProduct->customer_id)) {
                    $appointmentCustomer = (object) [
                        'id' => $leadProduct->customer_id,
                        'name' => $leadProduct->customer_name,
                        'lastname' => $leadProduct->customer_lastname,
                        'phone' => $leadProduct->customer_phone,
                        'telephone' => $leadProduct->customer_telephone,
                        'email' => $leadProduct->customer_email,
                        'street' => $leadProduct->customer_street,
                        'postcode' => $leadProduct->customer_postcode,
                        'city' => $leadProduct->customer_city,
                        'full_address' => $leadProduct->customer_full_address,
                    ];
                }
            }
        }

        $appointmentStageContext['lead_stage_color'] = $appointmentStageContext['lead_stage_color'] ?: '#74b2d4';
        $appointmentStageContext['lead_stage_sub_stage_color'] = $appointmentStageContext['lead_stage_sub_stage_color'] ?: '#93c21c';

        /*
        |--------------------------------------------------------------------------
        | All employees for reassignment
        |--------------------------------------------------------------------------
        */
        $allEmployees = Employee::where('status', 'Active')
            ->orderBy('name')
            ->get(['id', 'name', 'lastname', 'image']);

        /*
        |--------------------------------------------------------------------------
        | Time badge
        |--------------------------------------------------------------------------
        */
        $startDateTime = null;

        if ($appointment->start_date) {
            $datePart = $appointment->start_date instanceof \Carbon\Carbon
                ? $appointment->start_date->toDateString()
                : substr((string) $appointment->start_date, 0, 10);

            $timePart = $appointment->start_time ?: '00:00:00';

            try {
                $startDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $datePart . ' ' . $timePart);
            } catch (\Throwable $e) {
                $startDateTime = \Carbon\Carbon::parse($datePart . ' ' . $timePart);
            }
        }

        $timeBadgeLabel = null;

        if ($startDateTime) {
            $diff = $startDateTime->diffForHumans(null, [
                'parts' => 2,
                'short' => true,
                'syntax' => \Carbon\Carbon::DIFF_ABSOLUTE,
            ]);

            $timeBadgeLabel = $startDateTime->isFuture()
                ? 'Start in ' . $diff
                : 'Vor ' . $diff;
        }

        /*
        |--------------------------------------------------------------------------
        | Reports, comments, notifications
        |--------------------------------------------------------------------------
        */
        $reports = AppointmentReport::with('employee:id,name,lastname,image')
            ->where('appointment_id', $appointment->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $comments = AppointmentComment::with('employee:id,name,lastname,image')
            ->where('appointment_id', $appointment->id)
            ->orderByDesc('created_at')
            ->get();

        $notificationItems = collect();
        $user = Auth::user();

        if ($user) {
            $notificationItems = $user->notifications()
                ->where('type', AppointmentNotification::class)
                ->where('data->appointment_id', $appointment->id)
                ->orderByDesc('created_at')
                ->limit(15)
                ->get()
                ->map(function ($n) {
                    $d = $n->data ?? [];

                    return [
                        'id' => $n->id,
                        'title' => $d['title'] ?? 'Terminbenachrichtigung',
                        'message' => $d['message'] ?? '',
                        'kind' => $d['kind'] ?? 'generic',
                        'created_at' => $n->created_at->diffForHumans(),
                    ];
                });
        }

        return view('admin.appointments.show', [
            'appointment' => $appointment,
            'appointmentCustomer' => $appointmentCustomer,
            'appointmentStageContext' => $appointmentStageContext,
            'allEmployees' => $allEmployees,
            'reports' => $reports,
            'comments' => $comments,
            'timeBadgeLabel' => $timeBadgeLabel,
            'notificationItems' => $notificationItems,
        ]);
    }


    public function reports(MainAppointment $appointment)
    {
        $reports = $appointment->reports()
            ->with('employee:id,name,lastname')
            ->orderByDesc('report_date')
            ->get()
            ->map(function (AppointmentReport $r) {
                return [
                    'id' => $r->id,
                    'report' => $r->report,
                    'report_html' => nl2br(e($r->report)),
                    'report_date' => optional($r->report_date)->toDateString(),
                    'next_step' => $r->next_step,
                    'due_date' => optional($r->due_date)->toDateString(),
                    'created_at' => $r->created_at?->format('d.m.Y H:i'),
                    'created_at_human' => $r->created_at?->diffForHumans(),

                    'employee' => $r->employee ? [
                        'id' => $r->employee->id,
                        'name' => $r->employee->name,
                        'lastname' => $r->employee->lastname,
                        'full_name' => trim(($r->employee->name ?? '') . ' ' . ($r->employee->lastname ?? '')),
                    ] : null,
                ];
            });

        $comments = $appointment->comments()
            ->with('employee:id,name,lastname')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (AppointmentComment $c) {
                return [
                    'id' => $c->id,
                    'comment' => $c->comment,
                    'created_at' => $c->created_at?->format('d.m.Y H:i'),
                    'created_at_human' => $c->created_at?->diffForHumans(),
                    'employee' => $c->employee ? [
                        'id' => $c->employee->id,
                        'name' => $c->employee->name,
                        'lastname' => $c->employee->lastname,
                        'full_name' => trim(($c->employee->name ?? '') . ' ' . ($c->employee->lastname ?? '')),
                    ] : null,
                ];
            });

        return response()->json([
            'reports' => $reports,
            'comments' => $comments,
        ]);
    }


    public function storeReport(Request $request, MainAppointment $appointment)
    {
        $data = $request->validate([
            'report' => ['required', 'string'],
            'report_date' => ['nullable', 'date'],
            'next_step' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
        ]);

        $data['employee_id'] = auth()->user()->name; // wichtig
        $data['appointment_id'] = $appointment->id;

        $report = AppointmentReport::create($data);

        return response()->json([
            'success' => true,
            'report' => $report->load('employee:id,name,lastname'),
        ]);
    }

    public function toggleReportReaction(
        Request $request,
        MainAppointment $appointment,
        AppointmentReport $report
    ) {
        $employeeId = $this->currentEmployeeId();
        if (!$employeeId) {
            return response()->json(['success' => false, 'message' => 'Kein Mitarbeiter zugeordnet.'], 403);
        }

        $data = $request->validate([
            'reaction' => 'required|in:like,dislike',
            'text' => 'nullable|string',
        ]);

        // init structure
        $meta = $report->meta ?? [];
        $meta['items'] = $meta['items'] ?? [];
        $meta['likes'] = $meta['likes'] ?? [];
        $meta['dislikes'] = $meta['dislikes'] ?? [];

        // optional log entry in items[]
        if (!empty($data['text'])) {
            $meta['items'][] = [
                'employee_id' => $employeeId,
                'text' => $data['text'],
                'created_at' => now()->toIso8601String(),
            ];
        }

        if ($data['reaction'] === 'like') {
            // remove from dislikes
            $meta['dislikes'] = array_values(array_filter(
                $meta['dislikes'],
                fn($id) => (int) $id !== (int) $employeeId
            ));

            // toggle in likes
            if (in_array($employeeId, $meta['likes'])) {
                $meta['likes'] = array_values(array_filter(
                    $meta['likes'],
                    fn($id) => (int) $id !== (int) $employeeId
                ));
            } else {
                $meta['likes'][] = $employeeId;
            }
        } else {
            // reaction = dislike
            $meta['likes'] = array_values(array_filter(
                $meta['likes'],
                fn($id) => (int) $id !== (int) $employeeId
            ));

            if (in_array($employeeId, $meta['dislikes'])) {
                $meta['dislikes'] = array_values(array_filter(
                    $meta['dislikes'],
                    fn($id) => (int) $id !== (int) $employeeId
                ));
            } else {
                $meta['dislikes'][] = $employeeId;
            }
        }

        // update counts
        $report->meta = $meta;
        $report->likes = count($meta['likes']);
        $report->dislikes = count($meta['dislikes']);
        $report->updated_at = now();
        $report->save();

        return response()->json([
            'success' => true,
            'likes' => $report->likes,
            'dislikes' => $report->dislikes,
            'meta' => $meta,
        ]);
    }

    public function storeReportComment(
        Request $request,
        MainAppointment $appointment,
        AppointmentReport $report
    ) {
        $employeeId = $this->currentEmployeeId();
        if (!$employeeId) {
            return response()->json(['success' => false, 'message' => 'Kein Mitarbeiter zugeordnet.'], 403);
        }

        $data = $request->validate([
            'text' => 'required|string',
        ]);

        $comments = $report->comments ?? ['items' => []];
        $comments['items'] ??= [];

        $item = [
            'employee_id' => $employeeId,
            'text' => $data['text'],
            'created_at' => now()->toIso8601String(),
        ];

        $comments['items'][] = $item;
        $report->comments = $comments;
        $report->save();

        return response()->json([
            'success' => true,
            'item' => $item,
            'count' => count($comments['items']),
        ]);
    }

    public function reactReport(
        Request $request,
        MainAppointment $appointment,
        AppointmentReport $report
    ) {
        $data = $request->validate([
            'reaction' => 'required|in:like,dislike,none',
        ]);

        $user = Auth::user();
        $employeeId = $user->name;

        $meta = $report->meta;
        if (!is_array($meta)) {
            $meta = [];
        }

        $likes = isset($meta['likes']) && is_array($meta['likes']) ? $meta['likes'] : [];
        $dislikes = isset($meta['dislikes']) && is_array($meta['dislikes']) ? $meta['dislikes'] : [];

        // remove any previous reaction
        $likes = array_values(array_diff($likes, [$employeeId]));
        $dislikes = array_values(array_diff($dislikes, [$employeeId]));

        // apply new reaction
        if ($data['reaction'] === 'like') {
            $likes[] = $employeeId;
        } elseif ($data['reaction'] === 'dislike') {
            $dislikes[] = $employeeId;
        }

        $meta['likes'] = array_values(array_unique($likes));
        $meta['dislikes'] = array_values(array_unique($dislikes));

        $report->meta = $meta;
        $report->save();

        return response()->json([
            'success' => true,
            'likes' => count($meta['likes']),
            'dislikes' => count($meta['dislikes']),
            'my_reaction' => $report->reactionOf($employeeId), // method on AppointmentReport
        ]);
    }

    public function customers(Request $request)
    {
        $q = trim($request->get('q', ''));
        $items = NewLeads::query()
            ->when($q, function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('firma', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('lastname', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('telephone', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->limit(20)
            ->get(['id', 'firma', 'name', 'lastname', 'city', 'postcode']);

        return response()->json([
            'results' => $items->map(function ($c) {
                $label = trim(($c->firma ? $c->firma . ' — ' : '') . $c->name . ' ' . $c->lastname);
                $loc = trim(($c->postcode ? $c->postcode . ' ' : '') . ($c->city ?? ''));
                return ['id' => $c->id, 'text' => $loc ? "{$label} ({$loc})" : $label];
            })
        ]);
    }

    public function alternatives(NewLeads $lead)
    {
        $alts = LeadAlternativeAdd::where('lead_id', $lead->id)
            ->orderByDesc(DB::raw('COALESCE(main,0)'))
            ->orderByDesc('id')
            ->get(['id', 'full_address', 'object_name', 'main']);

        return response()->json(['alternatives' => $alts]);
    }

    public function leadProducts(Request $request, NewLeads $lead)
    {
        $altId = $request->get('alternative_id');

        $q = LeadProductList::query()
            ->where('customer_id', $lead->id)
            ->when($altId, fn($qq) => $qq->where('alternative_id', $altId))
            ->with([
                'product:id,article_group',
                'service:id,phase_section',
                'department:id,department_name',
                'employee:id,name,lastname',
                'fieldEmployee:id,name,lastname',
            ])
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        return response()->json([
            'products' => $q->map(function ($x) {
                return [
                    'id' => $x->id,
                    'product_id' => $x->product_id,
                    'product_name' => optional($x->product)->article_group,
                    'service_name' => optional($x->service)->phase_section,
                    'department_name' => optional($x->department)->department_name,
                    'employee_name' => $x->employee ? ($x->employee->name . ' ' . $x->employee->lastname) : null,
                    'field_employee_name' => $x->fieldEmployee ? ($x->fieldEmployee->name . ' ' . $x->fieldEmployee->lastname) : null,
                ];
            })
        ]);
    }

    public function storeFromModal(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'appointment_type' => 'nullable|string|max:255',
            'execution_type' => 'nullable|string|max:255',
            'branch_id' => 'nullable|integer',
            'priority' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'note' => 'nullable|string',

            'start' => 'required|date_format:Y-m-d\TH:i',
            'end' => 'nullable|date_format:Y-m-d\TH:i',

            'customer_id' => 'nullable|integer',
            'alternative_id' => 'nullable|integer',
            'lead_product_list_ids' => 'array',
            'lead_product_list_ids.*' => 'integer',

            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'integer',

            'link_inquiry' => 'nullable|boolean',
            'inquiry_id' => 'nullable|integer',
        ]);

        // resolve created_by (your schema uses employees)
        $createdBy = auth()->user()->name ?? auth()->id(); // adjust to your auth structure

        // parse start/end -> date + time (your table uses start_date, start_time etc)
        $start = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $data['start']);
        $end = $data['end'] ? \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $data['end']) : null;

        $inquiryId = (int) ($data['inquiry_id'] ?? 0);
        $linkInquiry = (bool) ($data['link_inquiry'] ?? false);

        // pull inquiry details (optional)
        $inquiry = null;
        if ($linkInquiry && $inquiryId) {
            $inquiry = Inquiry::find($inquiryId);
        }

        // store products json:
        // - lead selected product list ids
        // - plus inquiry products (if you store it as json in inquiry)
        $productsPayload = [
            'lead_product_list_ids' => $data['lead_product_list_ids'] ?? [],
        ];
        $productInquiryPayload = $inquiry?->products ?? null;

        DB::beginTransaction();
        try {
            $ap = new MainAppointment();
            $ap->created_by = $createdBy;
            $ap->name = $data['name'];
            $ap->execution_type = $data['execution_type'] ?? null;
            $ap->appointment_type = $data['appointment_type'] ?? null;
            $ap->note = $data['note'] ?? null;
            $ap->color = $data['color'] ?? null;

            $ap->start_date = $start->toDateString();
            $ap->start_time = $start->format('H:i:s');

            if ($end) {
                $ap->end_date = $end->toDateString();
                $ap->end_time = $end->format('H:i:s');
            }

            $ap->priority = $data['priority'] ?? null;
            $ap->status = $data['status'] ?? null;

            $ap->branch_id = $data['branch_id'] ?? null;
            $ap->customer_id = $data['customer_id'] ?? null;

            // LINKING:
            // "is_contact" should store inquiry id (as you requested)
            // if no inquiry => keep null
            $ap->is_contact = ($linkInquiry && $inquiry) ? (string) $inquiry->id : null;

            // If you want also customer contact_id stored in appointments:
            // contact_id column exists in main_appointments
            $ap->contact_id = $inquiry?->contact_id ?? ($data['customer_id'] ?? null);

            $ap->products = json_encode($productsPayload);
            $ap->product_inquiry = $productInquiryPayload ? (is_string($productInquiryPayload) ? $productInquiryPayload : json_encode($productInquiryPayload)) : null;

            $ap->save();

            // pivot employees
            foreach (array_unique($data['employee_ids']) as $eid) {
                MainAppointmentEmployee::create([
                    'employee_id' => $eid,
                    'appointment_id' => $ap->id,
                    'status' => 'send',
                ]);
            }

            DB::commit();

            // return event for calendar
            return response()->json([
                'appointment_id' => $ap->id,
                'event' => [
                    'id' => $ap->id,
                    'title' => $ap->name,
                    'start' => $start->toIso8601String(),
                    'end' => $end ? $end->toIso8601String() : null,
                    'backgroundColor' => $ap->color ?: null,
                    'borderColor' => $ap->color ?: null,
                ]
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function CustomerCalendarStore(Request $request)
    {
        // ✅ DEBUG: capture raw incoming employee fields BEFORE validation
        $incomingEmployee = $request->input('employee');
        $incomingEmployeeIdArr = $request->input('employee_id'); // if modal still sends employee_id[]
        Log::info('CustomerCalendarStore incoming', [
            'employee' => $incomingEmployee,
            'employee_id' => $incomingEmployeeIdArr,
            'keys' => array_keys($request->all()),
        ]);

        // 1) Validate modal payload
        $data = $request->validate([
            'name' => 'required|string|max:255',

            'start_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_date' => 'nullable|date',
            'end_time' => 'nullable|date_format:H:i',

            // accept BOTH names during transition
            'employee' => 'nullable|array',
            'employee.*' => 'nullable|integer',
            'employee_id' => 'nullable|array',
            'employee_id.*' => 'nullable|integer',

            'execution_type' => 'nullable|string|max:255',
            'appointment_type' => 'nullable|string|max:255',
            'contact_mode' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'color' => 'nullable|string|max:32',
            'priority' => 'nullable|string|max:64',
            'public' => 'nullable|string|max:64',

            'branch_id' => 'nullable|integer',
            'branch_address_id' => 'nullable|integer',

            'customer_id' => 'nullable|integer',
            'alternative_id' => 'nullable|integer',

            'type' => 'nullable|string|max:64',

            'products' => 'nullable|json',
            'product_inquiry' => 'nullable|json',
            'report_responsible' => 'nullable|json',
        ]);

        Log::info('CustomerCalendarStore validated', [
            'employee' => $data['employee'] ?? null,
            'employee_id' => $data['employee_id'] ?? null,
        ]);

        // 2) Normalize start/end
        $startTime = $data['start_time'] ?? '09:00';
        $startDT = Carbon::parse($data['start_date'] . ' ' . $startTime);

        $endDT = null;
        if (!empty($data['end_date']) || !empty($data['end_time'])) {
            $endDate = $data['end_date'] ?? $data['start_date'];
            $endTime = $data['end_time'] ?? $startTime;
            $endDT = Carbon::parse($endDate . ' ' . $endTime);
            if ($endDT->lt($startDT))
                $endDT = $startDT->copy();
        }

        // 3) Build payload
        $payload = [
            'name' => $data['name'],
            'execution_type' => $data['execution_type'] ?? null,
            'appointment_type' => $data['appointment_type'] ?? null,
            'contact_mode' => $data['contact_mode'] ?? null,
            'note' => $data['note'] ?? null,
            'color' => $data['color'] ?? null,
            'priority' => $data['priority'] ?? null,
            'public' => $data['public'] ?? null,
            'type' => $data['type'] ?? 'appointment',

            'branch_id' => $data['branch_id'] ?? null,
            'branch_address_id' => $data['branch_address_id'] ?? null,

            'customer_id' => $data['customer_id'] ?? null,
            'alternative_id' => $data['alternative_id'] ?? null,

            'products' => $data['products'] ?? null,
            'product_inquiry' => $data['product_inquiry'] ?? null,
            'report_responsible' => $data['report_responsible'] ?? null,

            'start_date' => $data['start_date'],
            'start_time' => $data['start_time'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'end_time' => $data['end_time'] ?? null,

            // these are optional columns only if they exist in your table
            'start' => $startDT->toDateTimeString(),
            'end' => $endDT ? $endDT->toDateTimeString() : null,
        ];

        /**
         * ⚠️ IMPORTANT:
         * Your schema expects created_by = employees.id (bigint FK).
         * If you set a string name here, inserts can fail.
         * Put the correct employee id. Adapt this to your auth setup.
         */
        $payload['created_by'] = auth()->user()->name; // adjust if your user is not an employee

        // 4) Create appointment
        $appointment = MainAppointment::create($payload);

        // 5) Resolve employees (supports employee[] OR employee_id[])
        $employeeIds = Arr::wrap($data['employee'] ?? ($data['employee_id'] ?? []));
        $employeeIds = array_values(array_unique(array_map('intval', array_filter($employeeIds, fn($v) => $v !== null && $v !== ''))));

        Log::info('CustomerCalendarStore resolved employeeIds', [
            'appointment_id' => $appointment->id,
            'employeeIds' => $employeeIds,
            'count' => count($employeeIds),
        ]);

        // 6) Attach employees in pivot
        foreach ($employeeIds as $empId) {
            MainAppointmentEmployee::firstOrCreate([
                'employee_id' => $empId,
                'appointment_id' => $appointment->id,
            ], [
                'status' => 'send',
            ]);
        }

        $pivotCount = MainAppointmentEmployee::where('appointment_id', $appointment->id)->count();

        Log::info('CustomerCalendarStore done', [
            'appointment_id' => $appointment->id,
            'pivotCount' => $pivotCount,
        ]);

        return response()->json([
            'success' => true,
            'id' => $appointment->id,
            // ✅ TEMP: return debug so you can see immediately in browser network tab
            'debug' => [
                'employee_in_request' => $incomingEmployee,
                'employee_id_in_request' => $incomingEmployeeIdArr,
                'employeeIds_resolved' => $employeeIds,
                'pivotCount' => $pivotCount,
            ],
        ]);
    }

}

