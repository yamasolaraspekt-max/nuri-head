<?php

namespace App\Http\Controllers;

use App\Models\MainAppointment;
use Illuminate\Http\Request;
use DB;
use App\Models\MainAppointmentEmployee;
use App\Models\Employee;
use App\Models\NewLeads;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Notifications\AppointmentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;
 use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\DatabaseNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator; 
use App\Models\Inquiry; 
use App\Models\PersonalTask;
use App\Models\PersonalTaskKey;
use App\Models\EmployeesPersonalTask;
use App\Models\Problem;
use App\Models\TicketTask; 

class MainAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function __construct(){
    $this->middleware('auth');
}

 public function index(Request $request)
{
    $search = $request->input('search');
    $dataType = $request->input('data_type', 'general'); // Default to 'general'

    // Base query using Eloquent
    $query = MainAppointment::with(['createdBy', 'branch', 'employees'])
        ->select('main_appointments.*', 'employees.name as cname', 'employees.lastname as clastname', 'employees.image as cimage', 'branches.branch')
        ->join('employees', 'employees.id', '=', 'main_appointments.created_by')
        ->leftJoin('branches', 'branches.id', '=', 'main_appointments.branch_id')
        ->leftJoin('main_appointment_employees', 'main_appointment_employees.appointment_id', '=', 'main_appointments.id')
        ->distinct();

    \Log::info('Received Query', [$query->toSql()]);

    // Apply filters based on data_type
    $query->where(function ($q) {
        $q->where('main_appointments.created_by', auth()->user()->name)
          ->orWhere('main_appointment_employees.employee_id', auth()->user()->name)
          ->whereDate('main_appointments.start_date', '<=', now())
          ->whereDate('main_appointments.end_date', '<=', now());
    })
 
     ->when($dataType === 'general', function ($q) {
        $q->whereNotIn('main_appointments.status', ['cancel','confirm', 'deleted', 'junk']);
    })
    ->when($dataType === 'created', function ($q) {
        $q->where('main_appointments.created_by', auth()->user()->name);
    })
    ->when($dataType === 'participant', function ($q) {
        $q->where('main_appointment_employees.employee_id', auth()->user()->name);
    })
    ->when($dataType === 'cancel', function ($q) {
        $q->where('main_appointments.status', 'cancel');
    })
    ->when($dataType === 'expired', function ($q) {
        $q->whereDate('main_appointments.start_date', '<=', now())
          ->whereDate('main_appointments.end_date', '<=', now());
    })
    ->when($dataType === 'confirm', function ($q) {
        $q->where('main_appointments.status', 'confirm')
        ->whereDate('main_appointments.start_date', '>=', now()); // Only today and future
    })

    ->when($dataType === 'deleted', function ($q) {
        $q->withTrashed()->whereNotNull('main_appointments.deleted_at'); // Include soft-deleted records
    });

    // Apply search filter
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('main_appointments.name', 'LIKE', "%{$search}%")
              ->orWhere('main_appointments.priority', 'LIKE', "%{$search}%");
        });
    }

    // Get paginated tasks
    $data['data'] = $query->orderBy('main_appointments.id', 'desc')->paginate(6);

    // Load additional data (static data)
    $data['employees'] = Employee::where('status','!=', 'Deactive')->orderBy('name', 'asc')->get(['id', 'name', 'lastname', 'image', 'gender']);
    $data['customers'] = NewLeads::whereNull('deleted_at')->where('status', '!=', 'Junk')->orderBy('name', 'asc')->get(['id', 'name', 'lastname', 'street', 'postcode', 'city', 'latitude', 'longitude', 'phone']);
    $data['branches'] = Branch::where('status', 'Published')->orderBy('branch', 'asc')->get(['id', 'branch']);
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

    $data['branch_addresses'] = DB::table('branch_addresses')
                                    ->join('branches', 'branches.id', '=', 'branch_addresses.branch_id')
                                    ->select('branch_addresses.*', 'branches.initial as branch_initial')
                                    ->whereNull('branch_addresses.deleted_at')
                                    ->get();

    $data['contact_list'] = json_decode($this->contactListIndex($request)->getContent(), true); // Convert JSON to array

    // Return view for AJAX or non-AJAX requests
    if ($request->ajax()) {
        return view('admin.todo.appointment.partials.appointment_table', $data)->render();
    }

    return view('admin.todo.appointment.appointment_view', $data);
}


public function contactListIndex(Request $request)
{
    $search = $request->input('search'); // Get search term

    $customerQuery = DB::table('new_leads')
        ->select(
            'id as main_id',
            'id as sub_id',
            'name',
            'lastname',
            'phone',
            'email',
            'street', 
            'city', 
            'postcode', 
            'longitude',
            'latitude',
            DB::raw('"customer" as type')
        );

    $companyQuery = DB::table('brands')
        ->join('brand_departments', 'brand_departments.brand_id', '=', 'brands.id') // ✅ Fixed join
        ->select(
            'brands.id as main_id',
            'brand_departments.id as sub_id',
            'brand_departments.name',
            'brand_departments.phone',
            'brand_departments.email',
            'brands.name as lastname',
            'brands.type',
            DB::raw('NULL as street'),
            DB::raw('NULL as city'),
            DB::raw('NULL as postcode'),
            DB::raw('NULL as longitude'),
            DB::raw('NULL as latitude')
        );

    $distributorQuery = DB::table('distributors')
        ->join('distributor_departments', 'distributor_departments.d_department', '=', 'distributors.id') // ✅ Fixed join
        ->select(
            'distributors.id as main_id',
            'distributor_departments.id as sub_id',
            'distributor_departments.name',
            'distributor_departments.phone',
            'distributor_departments.email',
            'distributors.name as lastname',
            DB::raw('"distributor" as type'),
            DB::raw('NULL as street'),
            DB::raw('NULL as city'),
            DB::raw('NULL as postcode'),
            DB::raw('NULL as longitude'),
            DB::raw('NULL as latitude')
        );

    // ✅ Merge All Contacts
    $contacts = collect()->merge($customerQuery->get())->merge($companyQuery->get())->merge($distributorQuery->get());

    return response()->json($contacts);
}

public function duplicate(Request $request)
{
    Log::info('DUPLICATE REQUEST:', $request->all());

    $validator = Validator::make($request->all(), [
        'appointment_id' => 'required|exists:main_appointments,id',
        'new_date' => 'required|date|after_or_equal:today',
    ]);

    if ($validator->fails()) {
        Log::error('VALIDATION FAILED:', $validator->errors()->toArray());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors()
        ], 422);
    }
    $original = MainAppointment::findOrFail($request->appointment_id);

    DB::beginTransaction();

    try {
        $newAppointment = $original->replicate(); // Clone all fields
        $newAppointment->start_date = $request->new_date;
        $newAppointment->end_date = $request->new_date;
        $newAppointment->created_by = auth()->user()->name;
        $newAppointment->status = 'confirm';
        $newAppointment->save();

        // Also copy assigned employees
        foreach ($original->employees as $employee) {
            MainAppointmentEmployee::create([
                'appointment_id' => $newAppointment->id,
                'employee_id' => $employee->id,
                'status' => 'accept',
            ]);
        }

        DB::commit();

        return response()->json(['success' => true, 'message' => 'Termin erfolgreich dupliziert.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Fehler beim Duplizieren.', 'error' => $e->getMessage()]);
    }
}

 
 

public function store(Request $request)
{
    \Log::info('Appointment Request:', [$request->all()]);

    // ---------- Validation ----------
    $rules = [
        'name'            => 'required|string|max:255',
        'description'     => 'nullable|string',
        'color'           => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
        'start_date'      => 'required|date',
        'end_date'        => 'nullable|date|after_or_equal:start_date',
        'start_time'      => 'nullable|date_format:H:i',
        'end_time'        => 'nullable|date_format:H:i|after_or_equal:start_time',
        'full_address'    => 'nullable|string|max:255',
        'street'          => 'nullable|string|max:255',
        'city'            => 'nullable|string|max:100',
        'postcode'        => 'nullable|string|max:20',
        'latitude'        => 'nullable|string|max:100',
        'longitude'       => 'nullable|string|max:100',
        'appointment_type'=> 'nullable|string|max:100',
        'execution_type'  => 'nullable|string|max:100',
        'date_type'       => 'nullable|string|max:100',
        'priority'        => 'required|string|in:normal,medium,high,very high',
        'employee'        => 'required|array',
        'employee.*'      => 'exists:employees,id',
        'from_day'        => 'nullable|string',
        'to_day'          => 'nullable|string',
        'from_month'      => 'nullable|string',
        'to_month'        => 'nullable|string',
        'reminder_date'   => 'nullable|date',

        // Ticket mode
        'contact_mode'    => 'nullable|in:new,select,ticket',
        'problem_id'      => 'nullable|exists:problems,id',
        'problem_task_id' => 'nullable' // may be "NEW_FROM_APPOINTMENT" or numeric id
    ];

    // Only require next_step + report_responsible if reminder_date is set
    if ($request->filled('reminder_date')) {
        $rules['next_step'] = 'required|string|max:255';
        $rules['report_responsible'] = 'required|array';
        $rules['report_responsible.*'] = 'exists:employees,id';
    }

    // In ticket mode, problem is required
    if ($request->contact_mode === 'ticket') {
        $rules['problem_id'] = 'required|exists:problems,id';
    }

    $validated = $request->validate($rules);

    // ---------- Transaction ----------
    DB::beginTransaction();

    try {
        // Auth/actor ids
        $createdByEmployeeId = auth()->user()->name;

        // Defaults
        $startDate = Carbon::parse($validated['start_date']);
        $endDate   = Carbon::parse($validated['end_date'] ?? $validated['start_date']);
        $startTime = $validated['start_time'] ?? '08:00';
        $endTime   = $validated['end_time']   ?? '16:00';

        // ---------- Build one or many appointment slices ----------
        $appointments = []; // each: ['start_date','end_date','start_time','end_time']

        switch (strtolower($request->date_type ?? '')) {
            case 'day':
                $appointments[] = [
                    'start_date' => $startDate->toDateString(),
                    'end_date'   => $endDate->toDateString(),
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                ];
                break;

            case 'week':
                for ($i = 0; $i < 7; $i++) {
                    $d = $startDate->copy()->addDays($i)->toDateString();
                    $appointments[] = [
                        'start_date' => $d,
                        'end_date'   => $d,
                        'start_time' => $startTime,
                        'end_time'   => $endTime,
                    ];
                }
                break;

            case 'daily':
                $map = ['monday'=>1,'tuesday'=>2,'wednesday'=>3,'thursday'=>4,'friday'=>5,'saturday'=>6,'sunday'=>7];
                if (isset($map[$request->from_day], $map[$request->to_day])) {
                    $loopStart = Carbon::now()->startOfWeek()->addDays($map[$request->from_day]-1);
                    $loopEnd   = Carbon::now()->startOfWeek()->addDays($map[$request->to_day]-1);
                    while ($loopStart->lte($loopEnd)) {
                        if ($loopStart->isWeekday()) {
                            $d = $loopStart->toDateString();
                            $appointments[] = [
                                'start_date' => $d,
                                'end_date'   => $d,
                                'start_time' => $startTime,
                                'end_time'   => $endTime,
                            ];
                        }
                        $loopStart->addDay();
                    }
                } else {
                    // Fallback to single
                    $appointments[] = [
                        'start_date' => $startDate->toDateString(),
                        'end_date'   => $endDate->toDateString(),
                        'start_time' => $startTime,
                        'end_time'   => $endTime,
                    ];
                }
                break;

            case 'monthly':
                $months = ['january'=>1,'february'=>2,'march'=>3,'april'=>4,'may'=>5,'june'=>6,'july'=>7,'august'=>8,'september'=>9,'october'=>10,'november'=>11,'december'=>12];
                if (isset($months[$request->from_month], $months[$request->to_month])) {
                    for ($m = $months[$request->from_month]; $m <= $months[$request->to_month]; $m++) {
                        try {
                            $date = Carbon::create($startDate->year, $m, $startDate->day)->toDateString();
                            $appointments[] = [
                                'start_date' => $date,
                                'end_date'   => $date,
                                'start_time' => $startTime,
                                'end_time'   => $endTime,
                            ];
                        } catch (\Throwable $e) {
                            // invalid day in month; skip
                            continue;
                        }
                    }
                } else {
                    $appointments[] = [
                        'start_date' => $startDate->toDateString(),
                        'end_date'   => $endDate->toDateString(),
                        'start_time' => $startTime,
                        'end_time'   => $endTime,
                    ];
                }
                break;

            case 'weekly':
                $weeks = (array) ($request->week_select ?? []);
                if (count($weeks)) {
                    foreach ($weeks as $weekNum) {
                        try {
                            $date = Carbon::now()->setISODate($startDate->year, (int)$weekNum, $startDate->dayOfWeekIso)->toDateString();
                            $appointments[] = [
                                'start_date' => $date,
                                'end_date'   => $date,
                                'start_time' => $startTime,
                                'end_time'   => $endTime,
                            ];
                        } catch (\Throwable $e) {
                            continue;
                        }
                    }
                } else {
                    $appointments[] = [
                        'start_date' => $startDate->toDateString(),
                        'end_date'   => $endDate->toDateString(),
                        'start_time' => $startTime,
                        'end_time'   => $endTime,
                    ];
                }
                break;

            default:
                $appointments[] = [
                    'start_date' => $startDate->toDateString(),
                    'end_date'   => $endDate->toDateString(),
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                ];
        }

        // ---------- Contact resolution ----------
        $finalName       = $request->name;
        $finalCustomerId = null;
        $finalOtherId    = null;
        $finalContactId  = $request->customer_id; // original selected ID (if "select" mode)
        $contactType     = $request->contact_type;

        if ($request->contact_mode === 'select') {
            if (!in_array($contactType, ['Marke', 'Lieferant', 'Brand', 'Distributor']) && $request->customer_id) {
                $customer = NewLeads::find($request->customer_id);
                if ($customer) {
                    $type = $customer->type ?? 'customer';
                    $finalName = trim(($customer->name ?? '').' '.($customer->lastname ?? ''));
                    $finalName = trim($finalName).' ('.$type.')';
                    $finalCustomerId = $customer->id;
                } else {
                    $finalOtherId = $request->customer_id;
                }
            } else {
                $finalOtherId = $request->customer_id;
            }
        }

        // ---------- Optional personal task from reminder ----------
        $personalTask = null;
        if ($request->filled('reminder_date') && $request->next_step && $request->report_responsible) {
            $personalTask = PersonalTask::create([
                'task_title'     => $finalName,
                'description'    => $validated['description'] ?? null,
                'priority'       => $validated['priority'],
                'color'          => $validated['color'] ?? '#8fc73e',
                'assigned_by'    => auth()->user()->name,
                'public'         => $request->public == 'on' ? '1' : '0',
                'start_date'     => $validated['start_date'],
                'due_date'       => $request->reminder_date,
                'task_status'    => 'pending',
                'type'           => 'personal_task',
                'is_notified'    => false,
                'controller_id'  => json_encode($request->report_responsible),
            ]);

            PersonalTaskKey::create([
                'personal_task_id' => $personalTask->id,
                'task'             => $request->next_step,
                'status'           => 'open',
                'employee_id'      => json_encode($validated['employee']),
            ]);

            foreach ($validated['employee'] as $empId) {
                EmployeesPersonalTask::create([
                    'employee_id' => $empId,
                    'task_id'     => $personalTask->id,
                    'status'      => 'accept',
                ]);
            }
        }

        // ---------- Ticket mode: link/create task + sync problem ----------
        $linkedProblemId = null;
        $linkedTaskId    = null;

        // We’ll use the very first slice as the anchor for task dates
        $firstSliceStart = $appointments[0]['start_date'];
        $firstSliceEnd   = $appointments[0]['end_date'];

        if ($request->contact_mode === 'ticket') {
            $selectedProblemId = (int) $request->problem_id;
            $linkedProblemId   = $selectedProblemId;

            $selectedTaskIdRaw = $request->problem_task_id;
            $createNewFromTitle = ($selectedTaskIdRaw === 'NEW_FROM_APPOINTMENT');

            if ($createNewFromTitle) {
                $ownerEmpId = (int) ($validated['employee'][0] ?? null);

                $task = TicketTask::create([
                    'ticket_id'      => $linkedProblemId,
                    'employee_id'    => $ownerEmpId ?: ($createdByEmployeeId ?: null),
                    'title'          => $finalName,
                    'priority'       => $validated['priority'],
                    'status'         => 'open',
                    'start_date'     => $firstSliceStart,
                    'due_date'       => $firstSliceEnd,
                    'is_done'        => false,
                ]);
                $linkedTaskId = $task->id;

                // Gentle sync: set initial problem dates if missing
                if ($problem = Problem::find($linkedProblemId)) {
                    $problem->date          = $problem->date ?: $firstSliceStart;
                    $problem->progress_date = $problem->progress_date ?: $firstSliceStart;
                    $problem->save();
                }
            } elseif (!empty($selectedTaskIdRaw) && ctype_digit((string)$selectedTaskIdRaw)) {
                $linkedTaskId = (int) $selectedTaskIdRaw;

                // Optional: align task dates to appointment anchor
                TicketTask::where('id', $linkedTaskId)->update([
                    'start_date' => $firstSliceStart,
                    'due_date'   => $firstSliceEnd,
                    'priority'   => $validated['priority'],
                ]);

                // Nudge progress_date
                Problem::where('id', $linkedProblemId)->update([
                    'progress_date' => $firstSliceStart,
                ]);
            }
        }

        // ---------- Create appointments ----------
        $firstAppointmentIdForBacklink = null;

        foreach ($appointments as $app) {
            $appointment = MainAppointment::create([
                'created_by'        => $createdByEmployeeId, // FK to employees.id
                'name'              => $finalName,
                'note'              => $validated['description'] ?? null,
                'color'             => $validated['color'] ?? '#8fc73e',
                'start_date'        => $app['start_date'],
                'end_date'          => $app['end_date'],
                'start_time'        => $app['start_time'],
                'end_time'          => $app['end_time'],
                'priority'          => $validated['priority'],

                // Contact block
                'customer_id'       => $finalCustomerId,
                'other_id'          => $finalOtherId,
                'contact_id'        => $finalContactId,
                'contact_type'      => $contactType,
                'contact_mode'      => $request->contact_mode,

                // Reminder / next-step
                'reminder_date'     => $request->reminder_date,
                'reminder_time'     => $request->reminder_time,
                'next_step'         => $request->next_step,
                'report_responsible'=> json_encode($request->report_responsible ?? []),

                // General
                'appointment_type'  => $request->appointment_type,
                'execution_type'    => $request->execution_type,
                'full_address'      => $request->full_address,
                'street'            => $request->street,
                'city'              => $request->city,
                'postcode'          => $request->postcode,
                'latitude'          => $request->latitude,
                'longitude'         => $request->longitude,
                'phone'             => $request->phone,
                'email'             => $request->email,
                'link'              => $request->link,
                'date_type'         => $request->date_type,
                'from_day'          => $request->from_day,
                'to_day'            => $request->to_day,
                'from_month'        => $request->from_month,
                'to_month'          => $request->to_month,
                'public'            => $request->public == 'on' ? '1' : '0',
                'branch_id'         => $request->branch_id,
                'branch_address_id' => $request->branch_address_id,
                'pre_type'          => $request->pre_type,
                'source'            => $request->source,
                'task_id'           => $personalTask->id ?? null,
                'is_notified'       => false,
                'is_report'         => $request->is_report == 'on' ? '1' : '0',
                'type'              => 'appointment',
                'products'          => $request->products,

                // Ticket links
                'problem_id'        => $linkedProblemId,
                'problem_task_id'   => $linkedTaskId,
            ]);

            // Employees pivot
            foreach ($validated['employee'] as $empId) {
                MainAppointmentEmployee::create([
                    'appointment_id' => $appointment->id,
                    'employee_id'    => $empId,
                    'status'         => 'accept',
                ]);
            }

            // Remember first appointment id to back-link on TicketTask (optional)
            $firstAppointmentIdForBacklink = $firstAppointmentIdForBacklink ?? $appointment->id;
        }

        // Optional: back-link the ticket task to the first appointment
        if ($linkedTaskId && $firstAppointmentIdForBacklink) {
            TicketTask::where('id', $linkedTaskId)->update([
                'appointment_id' => $firstAppointmentIdForBacklink,
            ]);
        }

        // ---------- Inquiry on contact switch ----------
        if ($request->is_contact === 'on') {
            Inquiry::create([
                'pre_type'         => $request->pre_type,
                'source'           => $request->source,
                'title'            => $finalName,
                'type'             => $request->appointment_type,
                'type_extra'       => $request->execution_type,
                'lastname'         => $request->lastname,
                'street'           => $request->street,
                'latitude'         => $request->latitude,
                'longitude'        => $request->longitude,
                'postcode'         => $request->postcode,
                'full_address'     => $request->full_address,
                'city'             => $request->city,
                'phone'            => $request->phone,
                'email'            => $request->email,
                'note'             => $request->description,
                'status'           => 'Unpublished',
                'periority'        => $request->priority,
                'next_step'        => $request->next_step,
                'branch_id'        => $request->branch_id ?? 1,
                'contact_person'   => auth()->user()->name,
                'personal_task_id' => $personalTask->id ?? null,
            ]);
        }

        DB::commit();
        return redirect()->route('main.appointment')->with('success', 'Termin erfolgreich gespeichert.');
    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('❌ Store failed', ['error' => $e->getMessage(), 'line' => $e->getLine()]);
        return response()->json([
            'success' => false,
            'message' => 'Fehler beim Speichern.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


public function add_employee(Request $request)
{
    Log::info('Incoming Request:', $request->all());

    DB::beginTransaction();

    try {
        // Step 1: Validate the request
        $request->validate([
            'appointment_id' => 'required|exists:main_appointments,id',
            'employee_id'    => 'required|exists:employees,id',
            'old_employee'   => 'required|exists:employees,id',
        ]);

        // Step 2: Check for duplicate assignment of the new employee
        $isDuplicate = MainAppointmentEmployee::where('appointment_id', $request->appointment_id)
                                              ->where('employee_id', $request->employee_id)
                                              ->exists();

        if ($isDuplicate) {
            return response()->json(['success' => false, 'message' => 'Mitarbeiter ist bereits dieser Aufgabe zugewiesen.'], 200);
        }

        // Step 3: Check if the task is assigned to the old employee
        $task = MainAppointmentEmployee::where('appointment_id', $request->appointment_id)
                                       ->where('employee_id', $request->old_employee)
                                       ->first();

        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Die aktuelle Mitarbeiterzuweisung wurde nicht gefunden.'], 404);
        }

        // Step 4: Update the task assignment
        $task->update([
            'employee_id' => $request->employee_id,
            'status'      => 'accept',
        ]);

        // Step 5: Fetch the new employee’s details
        $newEmployee = DB::table('employees')
                         ->select('name', 'lastname')
                         ->where('id', $request->employee_id)
                         ->first();
                         
        $newEmployeeName = $newEmployee->name . ' ' . $newEmployee->lastname;

        // Step 6: Send a notification about the assignment
        Notification::send(auth()->user(), new AppointmentNotification([
            'title'   => 'Neuer Mitarbeiter hinzugefügt',
            'message' => $newEmployeeName . ' wurde dieser Aufgabe hinzugefügt',
            'appointment_id' => $request->appointment_id,
        ]));

        DB::commit();

        return response()->json(['success' => true, 'message' => 'Neuer Mitarbeiter erfolgreich hinzugefügt'], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Exception Occurred:', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => 'Ein unerwarteter Fehler ist aufgetreten: ' . $e->getMessage()], 500);
    }
}



public function status(Request $request)
{
  
    \Log::info('request of status', [$request->all()]);
    $task = MainAppointment::find($request->id);
    if ($task) {
        // Define progress mapping based on project status 
        $task->update([
            'status' => $request->project_status, 
        ]);

          $emp = DB::table('employees')->select('name', 'lastname', 'id')->where('id', auth()->user()->name)->first();
            $name = $emp->name.' '.$emp->lastname; 
                Notification::send(auth()->user(), new AppointmentNotification([
                        'title'   => 'Terminstatus',
                        'message' => 'Der Status wurde von '.$name. ' geändert',
                        'appointment_id' => $request->id,
                    ]));

        return response()->json(['success' => true, 'message' => 'Terminstatus geändert']);
    }

    return response()->json(['success' => false, 'message' => 'Terminstatusnotiz geändert'], 404);
}

  public function restore($id)
    {
        $data = MainAppointment::withTrashed()->find($id);
        $data->status = 'start';
        $data->save();
        if ($data) {
            $data->restore(); // Restores the soft-deleted record
            return redirect()->back()->with('save_msg', 'Termin erfolgreich wiederhergestellt');
        }

        return redirect()->back()->with('error', 'Termin nicht gefunden');
    }



   public function accept_request(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'appointment_id'     => 'required|exists:main_appointments,id',
            'employee_id' => 'required|exists:employees,id',
            'response'    => 'required|in:accept,reject',
            'reason'      => 'nullable|string|max:500',
        ]);

        // Update task assignment status
        $updateStatus = MainAppointmentEmployee::where('employee_id', $request->employee_id)
            ->where('appointment_id', $request->appointment_id)
            ->update([
                'status' => $request->response,
                'reason' => $request->reason,
            ]);

        $emp = DB::table('employees')->select('name', 'lastname', 'id')->where('id', auth()->user()->name)->first();
        $name = $emp->name.' '.$emp->lastname;
        // Send Notification
        Notification::send(auth()->user(), new AppointmentNotification([
            'title'   => 'Stellenanfrage',
            'message' => 'Ernennung wurde von '.$name.' abgelehnt',
            'appointment_id' => $request->appointment_id,
        ]));
        // Check if the update was successful
        if ($updateStatus) {
            return response()->json(['message' => 'The data was sent successfully.'], 200);
        } else {
            return response()->json(['error' => 'Failed to update the task assignment.'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */

      public function edit($id)
    {
        
        $data['data']=DB::table('main_appointments')
                ->join('employees', 'employees.id', '=', 'main_appointments.created_by')
                ->leftJoin('branches', 'branches.id', '=', 'main_appointments.branch_id')
                ->leftJoin('main_appointment_employees', 'main_appointment_employees.appointment_id', '=', 'main_appointments.id')
                ->select(
                    'main_appointments.*',
                    'employees.name as cname',
                    'employees.lastname as clastname',
                    'employees.image as cimage',
                    'branches.branch'
                )
                ->where('main_appointments.id', $id)
                ->first(); // Ensure no duplicate rows
 
      

    // Load additional data (static data)
    $data['employees'] = DB::table('employees')
                                ->select('id', 'name', 'lastname', 'image', 'gender')
                                ->orderBy('name', 'asc')
                                ->where('status', 'Active')
                                ->get();

        $data['customers'] = DB::table('new_leads')
                                ->select('id', 'name', 'lastname', 'street', 'postcode', 'city', 'latitude', 'longitude', 'phone')
                                ->whereNull('deleted_at')
                                ->where('status', '!=', 'Junk')
                                ->orderBy('name', 'asc')
                                ->get();

        $data['branches'] = DB::table('branches')
                                ->select('id', 'branch')
                                ->orderBy('branch', 'asc')
                                ->where('status', 'Published')
                                ->get();
        $data['task_employee'] = DB::table('main_appointment_employees')
                                            ->join('employees', 'employees.id', '=', 'main_appointment_employees.employee_id')
                                            ->select('employees.id as employee_id', 'employees.name', 'employees.lastname',
                                             'employees.image', 'employees.gender', 'main_appointment_employees.appointment_id', 'main_appointment_employees.status', 'main_appointment_employees.reason'
                                             )
                                    ->get();

        $data['branch_addresses'] = DB::table('branch_addresses')
                                ->join('branches', 'branches.id', '=', 'branch_addresses.branch_id')
                                ->select('branch_addresses.*', 'branches.initial as branch_initial')
                                ->whereNull('branch_addresses.deleted_at')
                                ->get();
        $newEmployee = DB::table('employees')
                ->select('name', 'lastname')
                ->where('id', auth()->user()->name)
                ->first();
                         
        $newEmployeeName = $newEmployee->name . ' ' . $newEmployee->lastname;

        // Step 6: Send a notification about the assignment
        Notification::send(auth()->user(), new AppointmentNotification([
            'title'   => 'Termin aktualisiert',
            'message' => $newEmployeeName . ' hat diesen Termin geändert und aktualisiert',
            'appointment_id' => $id,
        ]));

            return view('admin.todo.appointment.appointment_edit', $data);


    }

   public function editCalendar(Request $request, $id)
    {
        // Get event type from AJAX request
        $eventType = $request->input('type'); // 'appointment' or 'task'

        if ($eventType === 'task') {
            return response()->json(['success' => false, 'message' => 'Task editing is in progress!'], 200);
        }

        // Fetch appointment details
        $appointment = DB::table('main_appointments')
            ->join('employees', 'employees.id', '=', 'main_appointments.created_by')  
            ->leftJoin('branches', 'branches.id', '=', 'main_appointments.branch_id')
            ->leftJoin('main_appointment_employees', 'main_appointment_employees.appointment_id', '=', 'main_appointments.id') 
            ->select(
                'main_appointments.*',
                'employees.name as cname',
                'employees.lastname as clastname',
                'employees.image as cimage',
                'branches.branch'
            )
            ->where('main_appointments.id', $id)
            ->first();

        // If no appointment found, return an error response
        if (!$appointment) {
            return response()->json(['success' => false, 'message' => 'Appointment not found.'], 404);
        }

        // Fetch selected employees as an array
        $employees = DB::table('main_appointment_employees')
            ->where('appointment_id', $id)
            ->pluck('employee_id') // Fetch only employee IDs
            ->toArray(); // Convert to array for easier selection

        // Get contacts list
        $contacts = $this->contactList($id);

        // Fetch selected branch
        $branches = DB::table('branches')
            ->select('id', 'branch')
            ->where('id', $appointment->branch_id)
            ->get();

        // Prepare response
        return response()->json([
            'success' => true,
            'appointment' => $appointment,
            'employees' => $employees, // Only IDs to select in the dropdown
            'contacts' => $contacts,
            'branches' => $branches,
        ]);
    }



    public function contactList($id)
{
    

    $customerQuery = DB::table('new_leads')
        ->select(
            'id as main_id',
            'id as sub_id',
            'name',
            'lastname',
            'phone',
            'email',
            'street', 
            'city', 
            'postcode', 
            'longitude',
            'latitude',
            DB::raw('"customer" as type')
        );

    $companyQuery = DB::table('brands')
        ->join('brand_departments', 'brand_departments.id', '=', 'brands.id')
        ->select(
            'brands.id as main_id',
            'brand_departments.id as sub_id',
            'brand_departments.name',
            'brand_departments.phone',
            'brand_departments.email',
            'brands.name as lastname',
            'brands.type',
            DB::raw('NULL as street'),
            DB::raw('NULL as city'),
            DB::raw('NULL as postcode'),
            DB::raw('NULL as longitude'),
            DB::raw('NULL as latitude')
        );

    $distributorQuery = DB::table('distributors')
        ->join('distributor_departments', 'distributor_departments.d_department', '=', 'distributors.id')
        ->select(
            'distributors.id as main_id',
            'distributor_departments.id as sub_id',
            'distributor_departments.name',
            'distributor_departments.phone',
            'distributor_departments.email',
            'distributors.name as lastname',
            DB::raw('"distributor" as type'),
            DB::raw('NULL as street'),
            DB::raw('NULL as city'),
            DB::raw('NULL as postcode'),
            DB::raw('NULL as longitude'),
            DB::raw('NULL as latitude')
        );

    // ✅ Apply Search Query If Provided
    if (!empty($search)) {
        $customerQuery->where(function ($query) use ($search) {
            $query->where('main_id', '=', $id);
        });

        $companyQuery->where(function ($query) use ($search) {
            $query->Where('brands.main_id', '=', $id);
        });

        $distributorQuery->where(function ($query) use ($search) {
            $query->where('distributor_departments.main_id', '=', $id);
        });
    }

    // ✅ Fetch Data
    $customer = $customerQuery->get();
    $company = $companyQuery->get();
    $distributor = $distributorQuery->get();

    // ✅ Merge All Contacts
    $contacts = collect()->merge($customer)->merge($company)->merge($distributor);

    return response()->json($contacts);
}

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request)
{
    Log::info('Appointment Update Request:', [$request->all()]);

    $validated = $request->validate([
        'id'               => 'required|exists:main_appointments,id',
        'name'             => 'required|string|max:255',
        'description'      => 'nullable|string',
        'color'            => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
        'start_date'       => 'required|date',
        'end_date'         => 'nullable|date|after_or_equal:start_date',
        'start_time'       => 'nullable|date_format:H:i',
        'end_time'         => 'nullable|date_format:H:i|after_or_equal:start_time',
        'priority'         => 'required|string|in:normal,medium,high,very high',
        'employee'         => 'required|array',
        'employee.*'       => 'exists:employees,id',
    ]);

    DB::beginTransaction();
    try {
        $appointment = MainAppointment::findOrFail($validated['id']);
        MainAppointmentEmployee::where('appointment_id', $appointment->id)->delete();

        $finalName       = $request->name;
        $finalCustomerId = null;
        $finalOtherId    = null;
        $finalContactId  = $request->contact_id ?? $request->customer_id;

        if ($request->contact_mode === 'select' && $request->customer_id) {
            $customer = \App\Models\NewLeads::find($request->customer_id);
            if ($customer) {
                $type      = strtolower($customer->type ?? 'kunde');
                $typeMap   = [
                    'customer'   => 'Kunde',
                    'brand'      => 'Marke',
                    'lieferant'  => 'Lieferant',
                    'supplier'   => 'Lieferant',
                    'distributor'=> 'Lieferant'
                ];
                $typeGerman = $typeMap[$type] ?? ucfirst($type);

                if (in_array($type, ['marke', 'lieferant', 'brand', 'supplier', 'distributor'])) {
                    $finalName   = $customer->name . " ({$typeGerman})";
                    $finalOtherId = $request->customer_id;
                } else {
                    $finalName       = trim($customer->name . ' ' . $customer->lastname) . " ({$typeGerman})";
                    $finalCustomerId = $customer->id;
                }
            }
        }

        // Build data
        $commonData = [
            'created_by'        => auth()->user()->name,
            'changed_by'        => auth()->user()->name,
            'name'              => $finalName,
            'note'              => $request->description,
            'color'             => $request->color ?? '#8fc73e',
            'full_address'      => $request->full_address,
            'street'            => $request->street,
            'postcode'          => $request->postcode,
            'city'              => $request->city,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'priority'          => $request->priority,
            'appointment_type'  => $request->appointment_type,
            'execution_type'    => $request->execution_type,
            'branch_id'         => $request->branch_id,
            'branch_address_id' => $request->branch_address_id,
            'contact_type'      => $request->contact_type,
            'contact_id'        => $finalContactId,
            'customer_id'       => $finalCustomerId,
            'other_id'          => $finalOtherId,
            'status'            => 'confirm',
            'public'            => $request->public === 'on' ? 1 : 0,
            'is_contact'        => $request->is_contact === 'on' ? 1 : 0,
            'is_report'         => $request->is_report === 'on' ? 1 : 0,
            'pre_type'          => $request->pre_type,
            'phone'             => $request->phone,
            'email'             => $request->email,
            'contact_mode'      => $request->contact_mode,
            'reminder_date'     => $request->reminder_date,
            'reminder_time'     => $request->reminder_time,
            'next_step'         => $request->next_step,
            'report_responsible'=> is_array($request->report_responsible)
                                    ? json_encode($request->report_responsible)
                                    : $request->report_responsible,
        ];

        $startDate = Carbon::parse($validated['start_date']);
        $endDate   = Carbon::parse($validated['end_date'] ?? $validated['start_date']);
        $startTime = $validated['start_time'] ?? '08:00';
        $endTime   = $validated['end_time'] ?? '16:00';

        $appointment->update(array_merge($commonData, [
            'start_date' => $startDate->toDateString(),
            'end_date'   => $endDate->toDateString(),
            'start_time' => $startTime,
            'end_time'   => $endTime,
        ]));

        foreach ($validated['employee'] as $empId) {
            MainAppointmentEmployee::create([
                'appointment_id' => $appointment->id,
                'employee_id'    => $empId,
                'status'         => 'accept',
            ]);
        }

        // 🧠 Update or create personal task
        if ($request->reminder_date && $request->next_step && $request->report_responsible) {
            if ($appointment->task_id) {
                // UPDATE existing task
                $task = \App\Models\PersonalTask::find($appointment->task_id);
                if ($task) {
                    $task->update([
                        'task_title'     => $finalName,
                        'description'    => $request->description,
                        'priority'       => $validated['priority'],
                        'color'          => $request->color ?? '#8fc73e',
                        'public'         => $request->public === 'on' ? '1' : '0',
                        'start_date'     => $startDate->toDateString(),
                        'due_date'       => $request->reminder_date,
                        'reminder_date'  => $request->reminder_date,
                        'controller_id'  => is_array($request->report_responsible)
                                            ? json_encode($request->report_responsible)
                                            : $request->report_responsible,
                    ]);

                    \App\Models\PersonalTaskKey::updateOrCreate(
                        ['personal_task_id' => $task->id],
                        [
                            'task'        => $request->next_step,
                            'status'      => 'open',
                            'employee_id' => json_encode($validated['employee']),
                        ]
                    );

                    \App\Models\EmployeesPersonalTask::where('task_id', $task->id)->delete();
                    foreach ($validated['employee'] as $empId) {
                        \App\Models\EmployeesPersonalTask::create([
                            'employee_id' => $empId,
                            'task_id'     => $task->id,
                            'status'      => 'send',
                        ]);
                    }
                }
            } else {
                // CREATE new task
                $task = \App\Models\PersonalTask::create([
                    'task_title'     => $finalName,
                    'description'    => $request->description,
                    'priority'       => $validated['priority'],
                    'color'          => $request->color ?? '#8fc73e',
                    'assigned_by'    => auth()->user()->name,
                    'public'         => $request->public === 'on' ? '1' : '0',
                    'start_date'     => $startDate->toDateString(),
                    'due_date'       => $request->reminder_date,
                    'reminder_date'  => $request->reminder_date,
                    'type'           => 'personal_task',
                    'is_notified'    => false,
                    'controller_id'  => json_encode($request->report_responsible),
                ]);

                \App\Models\PersonalTaskKey::create([
                    'personal_task_id' => $task->id,
                    'task'             => $request->next_step,
                    'status'           => 'open',
                    'employee_id'      => json_encode($validated['employee']),
                ]);

                foreach ($validated['employee'] as $empId) {
                    \App\Models\EmployeesPersonalTask::create([
                        'employee_id' => $empId,
                        'task_id'     => $task->id,
                        'status'      => 'send',
                    ]);
                }

                $appointment->update(['task_id' => $task->id]);
            }
        }

        // 🔑 Inquiry handling
        if ($request->is_contact === 'on' || $request->is_contact == 1) {
            $inquiryData = [
                'pre_type'         => $request->pre_type,
                'source'           => $request->source,
                'title'            => $finalName,
                'type'             => $request->appointment_type,
                'type_extra'       => $request->execution_type,
                'lastname'         => $request->lastname,
                'street'           => $request->street,
                'latitude'         => $request->latitude,
                'longitude'        => $request->longitude,
                'postcode'         => $request->postcode,
                'full_address'     => $request->full_address,
                'city'             => $request->city,
                'phone'            => $request->phone,
                'email'            => $request->email,
                'note'             => $request->description,
                'status'           => 'Unpublished',
                'periority'        => $request->priority,
                'next_step'        => $request->next_step,
                'branch_id'        => $request->branch_id ?? 1,
                'contact_person'   => auth()->user()->name,
                'personal_task_id' => $appointment->task_id ?? null,
            ];

            // 🔍 Check for existing Anfrage with same name/email/phone
            $existing = \App\Models\Inquiry::query()
                ->when($request->name, fn($q) => $q->where('name', $request->name))
                ->when($request->email, fn($q) => $q->where('email', $request->email))
                ->when($request->phone, fn($q) => $q->where('phone', $request->phone))
                ->first();

            if ($existing) {
                Log::info("⚠️ Inquiry already exists – skipped", [
                    'existing_id' => $existing->id,
                    'name'        => $request->name,
                    'email'       => $request->email,
                    'phone'       => $request->phone,
                ]);
            } else {
                $inquiry = \App\Models\Inquiry::create($inquiryData);
                Log::info("🆕 Inquiry created", ['id' => $inquiry->id]);
                // link back if you want
                $appointment->contact_id = $inquiry->id;
                $appointment->is_contact = 1;
                $appointment->save();
            }
        } else {
            // ❌ If not a contact → clear fields
            $appointment->update([
                'pre_type'    => null,
                'source'      => null,
                'is_contact'  => 0,
                'contact_id'  => null,
            ]);

            Log::info("🗑️ Contact mode off → appointment contact fields cleared", [
                'appointment_id' => $appointment->id
            ]);
        }



        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully.',
            'task_id' => $appointment->task_id,
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ Update failed', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Update failed.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}




public function details(Request $request, $id)
{ 
    // 2. Fetch task details along with the assigning employee
    $appointment = DB::table('main_appointments')
        ->join('employees', 'employees.id', '=', 'main_appointments.created_by')
        ->leftJoin('branches', 'branches.id', '=', 'main_appointments.branch_id') 
        ->select(
            'main_appointments.*', 
            'employees.name as cname', 
            'employees.lastname as clastname', 
            'employees.image as cimage',
            'branches.branch',
        )
        ->whereNull('main_appointments.deleted_at')  // Exclude soft-deleted tasks
        ->where('main_appointments.id', $id)
        ->first();

    // Ensure that the task exists after fetching
    if (!$appointment) {
        abort(404, 'Task not found.');
    }
    else{
        $data['data'] = $appointment;
    }

    // 3. Fetch employees assigned to the task
    $data['task_employee'] = DB::table('main_appointment_employees')
        ->join('employees as emp', 'emp.id', '=', 'main_appointment_employees.employee_id')
        ->select(
            'main_appointment_employees.*', 
            'emp.name', 
            'emp.lastname', 
            'emp.image', 
            'emp.gender', 
        )
        ->where('main_appointment_employees.appointment_id', $id)
        ->get()
        ->groupBy('same_id'); // Group employees by same_id

           

        $data['data_emp'] = DB::table('employees')
                            ->select('id', 'name', 'lastname', 'image')
                            ->where('status', 'Active')
                            ->get();

 
            $public_check = ($appointment->public == 1);
              Log::info('Public check' , [$public_check]);
         
          
            if (!$public_check) {
                return view('error.notAuth'); // Ensure the correct path to the view
            }
    


     
    $data['selectedEmployees'] = DB::table('main_appointment_employees')
                                ->where('appointment_id', $id)
                                ->select('employee_id')
                                ->get();
                    
    $data['group_emp'] = DB::table('main_appointment_employees')
                ->join('employees as emp', 'emp.id', '=', 'main_appointment_employees.employee_id')
                ->select(
                    'main_appointment_employees.*', 
                    'emp.name', 
                    'emp.lastname', 
                    'emp.image', 
                    'emp.gender'
                )
                ->where('main_appointment_employees.appointment_id', $id)
                ->get(); // This returns a collection
  

    // 7. Fetch comments related to the current task
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
        ->get();

    // 8. (Optional) Fetch total comments count separately if needed
    $data['comments_count'] = $data['comments_list']->count();

    // 9. Return the view with the collected data
    return view('admin.todo.appointment.appointment_details', $data);
}


    /**
     * Remove the specified resource from storage.
     */
       public function destroy($id)
    {
        $data = MainAppointment::findOrFail($id); 
        $data->delete();

        $data->update([
            'status'    =>  'GELÖSCHT'
        ]);
        return redirect()->to('appointments')->with('save_msg', 'Die Aufgabe wurde in den Papierkorb verschoben.');
    }

          
    public function calendar_destroy($id)
    {
        $data = MainAppointment::findOrFail($id); 
        $data->delete();
        // Update status instead of deleting
        $data->update([
            'status' => 'GELÖSCHT'
        ]);

        // Return a success response (no redirect)
        return response()->json(['status' => 'success', 'message' => 'Deleted successfully', 'event_id' => $id]);
    }





public function getAppointmentNotifications($appointment_id): JsonResponse
{
    // Ensure the task_id is cast to a string
    $appointmentId = (string) $appointment_id;

    // Fetch notifications where the task_id in the notification data matches the requested task_id
    $notifications = DatabaseNotification::where('data->appointment_id', $appointmentId)
        ->where('data->type', 'appointment')
        ->orderBy('created_at', 'desc')
        ->get();

    // Transform the notifications to a simplified JSON structure
    $transformedNotifications = $notifications->map(function ($notification) {
        return [
            'id' => $notification->id,
            'title' => $notification->data['title'] ?? 'Notification',
            'message' => $notification->data['message'] ?? '',
            'appointment_id' => $notification->data['appointment_id'] ?? null,
            'performed_at' => $notification->data['performed_at'] ?? $notification->created_at->toDateTimeString(),
        ];
    });

    // Return the notifications in JSON format
    return response()->json([
        'data' => $transformedNotifications,
    ]);
}



public function no_reminder(Request $request)
{
    $data = MainAppointment::find($request->id);
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
    $data = MainAppointment::find($request->id);
    if ($data) {
        $data->repeat = $request->repeat_date;
        $data->save();
        return response()->json(['success' => true, 'message' => 'Wiederholen wurde erfolgreich aktualisiert']);
    }

    return response()->json(['success' => false, 'message' => 'Aufgabe nicht gefunden'], 404);
}

public function getMap($id)
{
    $appointment = MainAppointment::find($id);

    if (!$appointment) {
        return response()->json(['success' => false, 'message' => 'Location not found'], 404);
    }

    if (!$appointment->latitude || !$appointment->longitude) {
        return response()->json(['success' => false, 'message' => 'Coordinates not available'], 404);
    }

    return response()->json([
        'success' => true,
        'latitude' => $appointment->latitude,
        'longitude' => $appointment->longitude,
        'title' => $appointment->name ?? "Destination", // Include title if available
    ]);
}

 

 
public function fetch($id)
{
    $appointment = DB::table('main_appointments')
        ->leftJoin('branches', 'branches.id', '=', 'main_appointments.branch_id')
        ->leftJoin('branch_addresses', 'branch_addresses.id', '=', 'main_appointments.branch_address_id')
        ->leftJoin('new_leads', 'new_leads.id', '=', 'main_appointments.contact_id')
        ->leftJoin('employees as creator', 'creator.id', '=', 'main_appointments.created_by')
        ->leftJoin('employees as changer', 'changer.id', '=', 'main_appointments.changed_by')
        ->select(
            'main_appointments.*',
            'branches.branch as branch_name',
            'branch_addresses.name as address_name',
            'branch_addresses.street as address_street',
            'branch_addresses.city as address_city',
            'branch_addresses.latitude as address_latitude',
            'branch_addresses.longitude as address_longitude',
            'branch_addresses.postcode as address_postcode',
            'new_leads.name as contact_name',
            'new_leads.lastname as contact_lastname',
            'creator.name as created_by_name',
            'creator.lastname as created_by_lastname',
            'creator.image as created_by_image',
            'changer.name as changed_by_name',
            'changer.lastname as changed_by_lastname',
            'changer.image as changed_by_image'
        )
        ->where('main_appointments.id', $id)
        ->first();

    if (!$appointment) {
        return response()->json(['error' => 'Appointment not found.'], 404);
    }

    $employees = DB::table('main_appointment_employees')
        ->join('employees', 'employees.id', '=', 'main_appointment_employees.employee_id')
        ->where('main_appointment_employees.appointment_id', $id)
        ->select('employees.id', 'employees.name', 'employees.lastname', 'employees.image')
        ->get();

    $employeeIds = $employees->pluck('id')->toArray();

    $responsibleReport = [];
    try {
        $responsibleReport = json_decode($appointment->report_responsible ?? '[]', true) ?: [];
    } catch (\Exception $e) {
        \Log::warning('responsible_report decode failed', ['id' => $id, 'value' => $appointment->report_responsible]);
    }

    $productJson = json_decode($appointment->product ?? '{}', true);
    $productOptions = [];

    if ($appointment->contact_type === 'Kunde' && $appointment->customer_id) {
        $objects = DB::table('lead_alternative_adds')
            ->where('lead_id', $appointment->customer_id)
            ->select('id', 'object_name', 'city')
            ->get();

        foreach ($objects as $object) {
            $products = DB::table('lead_product_lists')
                ->where('customer_id', $appointment->customer_id)
                ->where('alternative_id', $object->id)
                ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
                ->select('lead_product_lists.product_id', 'article_groups.article_group')
                ->get();

            $children = [];
            foreach ($products as $p) {
                $children[] = [
                    'id' => $p->article_group . '_' . $object->id,
                    'text' => $p->article_group,
                    'product_name' => $p->article_group,
                    'product_id' => $p->product_id,
                    'alternative_id' => $object->id,
                    'customer_id' => $appointment->customer_id,
                    'city' => $object->city,
                    'object_name' => $object->object_name ?? 'Objekt ' . $object->id
                ];
            }

            if (!empty($children)) {
                $productOptions[] = [
                    'text' => $object->object_name ?? 'Objekt ' . $object->id,
                    'children' => $children
                ];
            }
        }
    }

    return response()->json([
        'id' => $appointment->id,
        'name' => $appointment->name,
        'appointment_type' => $appointment->appointment_type,
        'execution_type' => $appointment->execution_type,
        'contact_mode' => $appointment->contact_mode,
        'contact_type' => $appointment->contact_type,
        'contact_id' => $appointment->contact_id,
        'customer_id' => $appointment->customer_id,
        'other_id' => $appointment->other_id,
        'note' => $appointment->note,
        'color' => $appointment->color,
        'priority' => $appointment->priority,
        'status' => $appointment->status,
        'type' => $appointment->type,
        'is_notified' => (bool) $appointment->is_notified,
        'public' => $appointment->public,
        'start_date' => $appointment->start_date,
        'end_date' => $appointment->end_date,
        'start_time' => $appointment->start_time ? date('H:i', strtotime($appointment->start_time)) : null,
        'end_time' => $appointment->end_time ? date('H:i', strtotime($appointment->end_time)) : null,
        'total_time' => $appointment->total_time,
        'date_type' => $appointment->date_type,
        'repeat' => $appointment->repeat,
        'from_day' => $appointment->from_day,
        'to_day' => $appointment->to_day,
        'from_month' => $appointment->from_month,
        'to_month' => $appointment->to_month,
        'reminder_date' => $appointment->reminder_date,
        'reminder_time' => $appointment->reminder_time,
        'next_step' => $appointment->next_step,
        'responsible_report' => $responsibleReport,
        'phone' => $appointment->phone,
        'email' => $appointment->email,
        'link' => $appointment->link,
        'description' => $appointment->note,
        'is_contact' => $appointment->is_contact,
        'is_report' =>  $appointment->is_report,
        'pre_type' => $appointment->is_contact === '1' ? $appointment->pre_type : null,
        'source' => $appointment->is_contact === '1' ? $appointment->source : null,
        'full_address' => $appointment->full_address,
        'street' => $appointment->street,
        'city' => $appointment->city,
        'postcode' => $appointment->postcode,
        'latitude' => $appointment->latitude,
        'longitude' => $appointment->longitude,
        'branch_id' => $appointment->branch_id,
        'branch_name' => $appointment->branch_name,
        'branch_address_id' => $appointment->branch_address_id,
        'address_details' => [
            'name' => $appointment->address_name,
            'street' => $appointment->address_street,
            'city' => $appointment->address_city,
            'latitude' => $appointment->address_latitude,
            'longitude' => $appointment->address_longitude,
            'postcode' => $appointment->address_postcode,
        ],
        'employee_ids' => $employeeIds,
        'employees' => $employees,
        'change_date' => $appointment->change_date,
        'change_reason' => $appointment->change_reason,
        'created_by' => $appointment->created_by,
        'created_by_name' => trim($appointment->created_by_name . ' ' . $appointment->created_by_lastname),
        'created_by_image' => $appointment->created_by_image,
        'changed_by' => $appointment->changed_by,
        'changed_by_name' => trim($appointment->changed_by_name . ' ' . $appointment->changed_by_lastname),
        'changed_by_image' => $appointment->changed_by_image,
        'created_at' => $appointment->created_at,
        'updated_at' => $appointment->updated_at,
        'deleted_at' => $appointment->deleted_at,
        'contact_name' => $appointment->contact_name,
        'contact_lastname' => $appointment->contact_lastname,
        'product_options' => $productOptions,
        'product_json' => $productJson,
    ]);
}


public function updateAjax(Request $request, $id)
{
    Log::info('🔄 Ajax Update Request:', [$request->all()]);

    $request->validate([
        'id'         => 'required|exists:main_appointments,id',
        'name'       => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date'   => 'nullable|date|after_or_equal:start_date',
        'start_time' => 'nullable|date_format:H:i',
        'end_time'   => 'nullable|date_format:H:i|after_or_equal:start_time',
        'employee'   => 'required|array',
        'employee.*' => 'exists:employees,id',
        'priority'   => 'required|string|in:normal,medium,high,very high',
        'reminder_date'      => 'nullable|date',
        'reminder_time'      => 'nullable|date_format:H:i',
        'report_responsible' => 'nullable',
        'next_step'          => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();

    try {
        $appointment = MainAppointment::findOrFail($id);

        $finalName       = $request->name;
        $finalCustomerId = null;
        $finalOtherId    = null;
        $finalContactId  = $request->contact_id ?? $request->customer_id;

        // 🔎 Handle Customer/Other selection
        if ($request->contact_mode === 'select' && $request->customer_id) {
            $customer = \App\Models\NewLeads::find($request->customer_id);
            if ($customer) {
                $type = strtolower($customer->type ?? 'kunde');
                $germanTypes = [
                    'customer' => 'Kunde', 'kunde' => 'Kunde',
                    'brand' => 'Marke', 'marke' => 'Marke',
                    'lieferant' => 'Lieferant', 'supplier' => 'Lieferant', 'distributor' => 'Lieferant',
                ];
                $typeGerman = $germanTypes[$type] ?? ucfirst($type);

                if (in_array($type, ['marke','lieferant','brand','supplier','distributor'])) {
                    $finalName    = $customer->name . " ({$typeGerman})";
                    $finalOtherId = $customer->id;
                } else {
                    $finalName       = trim($customer->name . ' ' . $customer->lastname) . " ({$typeGerman})";
                    $finalCustomerId = $customer->id;
                }
            } else {
                $finalOtherId = $request->customer_id;
            }
        }

        // 📝 Handle Inquiry Sync
        if ($request->is_contact === 'on' || $request->is_contact == 1) {
            $inquiryData = [
                'pre_type'         => $request->pre_type,
                'source'           => $request->source,
                'title'            => $finalName,
                'type'             => $request->appointment_type,
                'type_extra'       => $request->execution_type,
                'name'             => $request->name,
                'street'           => $request->street,
                'latitude'         => $request->latitude,
                'longitude'        => $request->longitude,
                'postcode'         => $request->postcode,
                'full_address'     => $request->full_address,
                'city'             => $request->city,
                'phone'            => $request->phone,
                'email'            => $request->email,
                'note'             => $request->description,
                'status'           => 'Unpublished',
                'periority'        => $request->priority,
                'next_step'        => $request->next_step,
                'branch_id'        => $request->branch_id ?? 1,
                'contact_person'   => auth()->user()->name,
                'personal_task_id' => $appointment->task_id ?? null,
            ];

            // Check for existing Inquiry by name+email+phone
            $existing = \App\Models\Inquiry::query()
                ->when($request->name, fn($q) => $q->where('name', $request->name))
                ->when($request->email, fn($q) => $q->where('email', $request->email))
                ->when($request->phone, fn($q) => $q->where('phone', $request->phone))
                ->first();

            if ($existing) {
                $existing->update($inquiryData);
                $appointment->contact_id = $existing->id;
                Log::info("⚡ Inquiry updated", ['id' => $existing->id]);
            } else {
                $inquiry = \App\Models\Inquiry::create($inquiryData);
                $appointment->contact_id = $inquiry->id;
                Log::info("🆕 Inquiry created", ['id' => $inquiry->id]);
            }

            $appointment->is_contact = 1;
        } else {
            // ❌ If not a contact, clear Inquiry-related fields
            $appointment->update([
                'is_contact' => 0,
                'contact_id' => null,
                'pre_type'   => null,
                'source'     => null,
            ]);
            Log::info("🗑️ Contact mode off → Inquiry link cleared", ['appointment_id' => $appointment->id]);
        }

        // 📌 Handle Personal Task
        if ($request->reminder_date && $request->next_step && $request->report_responsible) {
            $responsible = is_array($request->report_responsible)
                ? json_encode($request->report_responsible)
                : $request->report_responsible;

            if ($appointment->task_id) {
                $task = \App\Models\PersonalTask::find($appointment->task_id);
                if ($task) {
                    $task->update([
                        'task_title'     => $finalName,
                        'description'    => $request->description,
                        'priority'       => $request->priority,
                        'color'          => $request->color ?? '#8fc73e',
                        'public'         => $request->public === 'on' ? '1' : '0',
                        'start_date'     => $request->start_date,
                        'due_date'       => $request->reminder_date,
                        'reminder_date'  => $request->reminder_date,
                        'controller_id'  => $responsible,
                    ]);

                    \App\Models\PersonalTaskKey::updateOrCreate(
                        ['personal_task_id' => $task->id],
                        [
                            'task'        => $request->next_step,
                            'status'      => 'open',
                            'employee_id' => json_encode($request->employee),
                        ]
                    );

                    \App\Models\EmployeesPersonalTask::where('task_id', $task->id)->delete();
                    foreach ($request->employee as $empId) {
                        \App\Models\EmployeesPersonalTask::create([
                            'employee_id' => $empId,
                            'task_id'     => $task->id,
                            'status'      => 'send',
                        ]);
                    }
                }
            } else {
                $newTask = \App\Models\PersonalTask::create([
                    'task_title'     => $finalName,
                    'description'    => $request->description,
                    'priority'       => $request->priority,
                    'color'          => $request->color ?? '#8fc73e',
                    'assigned_by'    => auth()->user()->name,
                    'public'         => $request->public === 'on' ? '1' : '0',
                    'start_date'     => $request->start_date,
                    'due_date'       => $request->reminder_date,
                    'reminder_date'  => $request->reminder_date,
                    'type'           => 'personal_task',
                    'is_notified'    => false,
                    'controller_id'  => $responsible,
                ]);

                \App\Models\PersonalTaskKey::create([
                    'personal_task_id' => $newTask->id,
                    'task'             => $request->next_step,
                    'status'           => 'open',
                    'employee_id'      => json_encode($request->employee),
                ]);

                foreach ($request->employee as $empId) {
                    \App\Models\EmployeesPersonalTask::create([
                        'employee_id' => $empId,
                        'task_id'     => $newTask->id,
                        'status'      => 'send',
                    ]);
                }

                $appointment->task_id = $newTask->id;
            }
        }

        // 📌 Update Appointment core fields
        $appointment->update([
            'name'               => $finalName,
            'note'               => $request->description,
            'color'              => $request->color,
            'start_date'         => $request->start_date,
            'end_date'           => $request->end_date,
            'start_time'         => $request->start_time,
            'end_time'           => $request->end_time,
            'total_time'         => $request->total_time,
            'full_address'       => $request->full_address,
            'street'             => $request->street,
            'postcode'           => $request->postcode,
            'city'               => $request->city,
            'latitude'           => $request->latitude,
            'longitude'          => $request->longitude,
            'phone'              => $request->phone,
            'email'              => $request->email,
            'appointment_type'   => $request->appointment_type,
            'execution_type'     => $request->execution_type,
            'date_type'          => $request->date_type,
            'priority'           => $request->priority,
            'branch_id'          => $request->branch_id,
            'branch_address_id'  => $request->branch_address_id,
            'contact_type'       => $request->contact_type,
            'contact_id'         => $finalContactId,
            'customer_id'        => $finalCustomerId,
            'other_id'           => $finalOtherId,
            'is_report'          => $request->is_report === 'on' ? '1' : '0',
            'public'             => $request->public === 'on' ? '1' : '0',
            'contact_mode'       => $request->contact_mode,
            'reminder_date'      => $request->reminder_date,
            'reminder_time'      => $request->reminder_time,
            'next_step'          => $request->next_step,
            'report_responsible' => is_array($request->report_responsible)
                                        ? json_encode($request->report_responsible)
                                        : $request->report_responsible,
        ]);

        // 🔄 Reassign employees
        MainAppointmentEmployee::where('appointment_id', $appointment->id)->delete();
        foreach ($request->employee as $empId) {
            MainAppointmentEmployee::create([
                'appointment_id' => $appointment->id,
                'employee_id'    => $empId,
                'status'         => 'accept',
            ]);
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Appointment updated successfully.']);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ Appointment Update Failed', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to update appointment.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

public function toggleReport($id, Request $request)
{
    $appointment = MainAppointment::findOrFail($id);
    $appointment->is_report = $request->is_report;
    if ($request->is_report === '1') {
        $appointment->report_date = now();
        $appointment->report_by = auth()->user()->name;
    } else {
        $appointment->report = null;
        $appointment->report_date = null;
        $appointment->report_by = null;
    }
    $appointment->save();
    return response()->json(['success' => true]);
}

public function saveReport($id, Request $request)
{
    $appointment = MainAppointment::findOrFail($id);
    $appointment->report = $request->report;
    $appointment->report_date = now();
    $appointment->report_by = auth()->user()->name;
    $appointment->save();
    return response()->json(['success' => true]);
}

public function loadReport($id)
{
    $appointment = MainAppointment::with('reporter')->findOrFail($id);
    return response()->json([
        'report' => $appointment->report,
        'report_date' => $appointment->report_date,
        'creator' => $appointment->reporter ? [
            'name' => $appointment->reporter->name,
            'lastname' => $appointment->reporter->lastname,
            'image' => $appointment->reporter->image
        ] : null
    ]);
}

public function deleteReport($id)
{
    $appointment = MainAppointment::findOrFail($id);
    $appointment->report = null;
    $appointment->report_date = null;
    $appointment->report_by = null;
    $appointment->save();
    return response()->json(['success' => true]);
}

public function mobileStore(Request $request)
{
    \Log::info('requested Date', [$request->all()]);
    $request->validate([
        'name' => 'required|string',
        'phone' => 'nullable',
        'email' => 'nullable|email',
        'street' => 'nullable|string',
        'city' => 'nullable|string',
        'postcode' => 'nullable|string',
        'latitude' => 'nullable',
        'longitude' => 'nullable',
        'employees' => 'array|nullable',
        'contact' => 'nullable|string',
        'product' => 'nullable|string'
    ]);

    $contactMode = 'new';
    $customerId = null;
    $otherId = null;

    if ($request->contact) {
        $contactMode = 'select';
        [$type, $mainId, $subId] = explode(':', $request->contact);
        if ($type === 'Kunde') {
            $customerId = $mainId;
        } else {
            $otherId = $mainId;
        }
    }

    $productJson = null;
    if ($customerId && $request->product) {
        [$cId, $altId, $productId] = explode('_', $request->product);
        $productJson = json_encode(["Produkt" => [(int)$altId, (int)$productId, (int)$cId]]);
    }

    $appointment = MainAppointment::create([
        'name' => $request->name,
        'created_by' => auth()->user()->name,
        'phone' => $request->phone,
        'email' => $request->email,
        'street' => $request->street,
        'city' => $request->city,
        'postcode' => $request->postcode,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'customer_id' => $customerId,
        'other_id' => $otherId,
        'contact_mode' => $contactMode,
        'products' => $productJson,

        // 👇 Add missing required fields
        'start_date' => $request->start_date ?? now(),
        'end_date' => $request->end_date ?? now(),
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'priority' => $request->priority,
    ]);


    $employeeIds = $request->filled('employees') ? $request->employees : [auth()->user()->name];

    foreach ($employeeIds as $eid) {
        $appointment->appointmentEmployees()->create(['employee_id' => $eid]);
    }


    return response()->json(['success' => true, 'message' => 'Termin erstellt']);
}

public function customers(Request $r)
    {
        $q = trim($r->get('q',''));
        $rows = NewLeads::query()
            ->when($q, fn($qq)=>$qq->where(function($w) use($q){
                $w->where('name', 'like', "%$q%")
                  ->orWhere('lastname', 'like', "%$q%");
            }))
            ->limit(20)->get(['id','name','lastname']);

        return response()->json(['results'=>$rows]);
    }

    public function problemsByCustomer(Request $r)
    {
        $cid = (int) $r->get('customer_id');
        $q   = trim($r->get('q',''));
        $rows = Problem::where('customer_id', $cid)
            ->when($q, fn($qq)=>$qq->where(function($w) use($q){
                $w->where('ticket_no', 'like', "%$q%")
                  ->orWhere('status', 'like', "%$q%")
                  ->orWhere('problem', 'like', "%$q%");
            }))
            ->orderByDesc('id')
            ->limit(20)
            ->get(['id','ticket_no','status']);

        $results = $rows->map(fn($p)=>[
            'id'=>$p->id,
            'text'=> sprintf('#%s — %s', $p->ticket_no, $p->status ?? 'ohne Status')
        ]);

        return response()->json(['results'=>$results]);
    }

    public function tasksByProblem(Request $r)
    {
        $pid = (int) $r->get('problem_id');
        $q   = trim($r->get('q',''));
        $rows = TicketTask::where('ticket_id', $pid)
            ->when($q, fn($qq)=>$qq->where('title','like',"%$q%"))
            ->orderByDesc('id')
            ->limit(30)
            ->get(['id','title']);

        return response()->json(['results'=>$rows]);
    }
}
