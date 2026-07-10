<?php

namespace App\Http\Controllers\Appointment;
use App\Http\Controllers\Controller;

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
use App\Models\InquiryProductList;  
use Illuminate\Support\Facades\Schema;
 use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use App\Models\AppointmentReport;
use App\Models\LeadProductList;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage; 

class MainAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function __construct(){
        // MASTER-01 P1-IDOR Customer-Rest: Belegkette-Gate (permission:Customer)
        $this->middleware('permission:Customer,delete')->only(['destroy', 'calendar_destroy', 'forceDeleteAppointment']);
        $this->middleware('permission:Customer,update')->only(['update', 'status']);
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
        Log::info('Appointment Request:', [$request->all()]);


        // CRM stage fields can come from hidden inputs. Normalize before validation
        // so values like product UID / JSON / "[object Object]" do not trigger
        // "The lead product list id field must be an integer.".
        $nullableInteger = static function ($value): ?int {
            if (is_array($value)) {
                $value = reset($value);
            }

            $value = trim((string) ($value ?? ''));

            return preg_match('/^\d+$/', $value) ? (int) $value : null;
        };

        $request->merge([
            'lead_product_list_id' => $nullableInteger($request->input('lead_product_list_id')),
            'lead_stage_id' => $nullableInteger($request->input('lead_stage_id')),
            'lead_stage_sub_stage_id' => $nullableInteger($request->input('lead_stage_sub_stage_id')),
        ]);

        // -----------------------------
        // 1) Validate
        // -----------------------------
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'full_address' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'latitude' => 'nullable|string|max:100',
            'longitude' => 'nullable|string|max:100',
            'appointment_type' => 'nullable|string|max:100',
            'execution_type' => 'nullable|string|max:100',
            'date_type' => 'nullable|string|max:100',
            'priority' => 'required|string|in:normal,medium,high,very high',

            // allow employee to be nullable when inquiry mode is on
            'employee' => $request->boolean('is_contact') ? 'nullable|array' : 'required|array|min:1',
            'employee.*' => 'exists:employees,id',

            'from_day' => 'nullable|string',
            'to_day' => 'nullable|string',
            'from_month' => 'nullable|string',
            'to_month' => 'nullable|string',
            'reminder_date' => 'nullable|date',

            'contact_mode' => 'nullable|in:new,select,ticket',
            'problem_id' => 'nullable|exists:problems,id',
            'problem_task_id' => 'nullable',

            'products' => 'nullable',
            'inquiries' => 'nullable|array',
            'lead_product_list_id' => 'nullable|integer|exists:lead_product_lists,id',
            'lead_stage_id' => 'nullable|integer|exists:lead_stages,id',
            'lead_stage_sub_stage_id' => 'nullable|integer|exists:lead_stage_sub_stages,id',
        ];

        if ($request->filled('reminder_date')) {
            $rules['next_step'] = 'required|string|max:255';
            $rules['report_responsible'] = 'required|array';
            $rules['report_responsible.*'] = 'exists:employees,id';
        }
        if ($request->contact_mode === 'ticket') {
            $rules['problem_id'] = 'required|exists:problems,id';
        }

        $validated = $request->validate($rules);

        // -----------------------------
        // 2) Normalize inputs
        // -----------------------------
        $productsJson = $this->normalizeProductsToJson($request->input('products'));

        // inquiries array (snapshot rows: product/department/service + assigned in/out employees)
        $inquiriesArr = collect($request->input('inquiries', []))
            ->map(fn($row) => [
                'product_id' => $row['product_id'] ?? null,
                'department_id' => $row['department_id'] ?? null,
                'service_id' => $row['service_id'] ?? null,
                'employee_id' => $row['employee_id'] ?? null, // Innendienst
                'field_employee_id' => $row['field_employee_id'] ?? null, // Außendienst
            ])
            ->filter(fn($r) => $r['product_id'] || $r['department_id'] || $r['service_id'] || $r['employee_id'] || $r['field_employee_id'])
            ->values();

        $inquiriesJson = $inquiriesArr->toJson(JSON_UNESCAPED_UNICODE);

        // override employees from inquiry rows in inquiry mode
        if ($request->boolean('is_contact')) {
            $fromInquiry = $inquiriesArr->pluck('employee_id')->filter()->unique()->values()->all();
            if (!empty($fromInquiry)) {
                $validated['employee'] = $fromInquiry;
            }
        }

        // normalize report_responsible (avoid [null])
        $reportResponsible = collect((array) $request->input('report_responsible', []))
            ->filter(fn($v) => filled($v))
            ->unique()
            ->values()
            ->all();

        $stageContext = $this->resolveAppointmentStageContext($request);

        // -----------------------------
        // 3) Date/Time & title fallback
        // -----------------------------
        $startDate = CarbonImmutable::parse($validated['start_date']);
        $endDate = CarbonImmutable::parse($validated['end_date'] ?? $validated['start_date']);
        $startTime = $validated['start_time'] ?? '08:00';
        $endTime = $validated['end_time'] ?? '16:00';

        $finalName = trim((string) ($validated['name'] ?? ''));
        if ($finalName === '') {
            $address = $validated['full_address']
                ?? trim(implode(' ', array_filter([
                    $validated['street'] ?? null,
                    $validated['postcode'] ?? null,
                    $validated['city'] ?? null,
                ]))) ?: null;

            $pieces = array_filter([
                $validated['appointment_type'] ?? null,
                $validated['execution_type'] ?? null,
                $address,
            ]);

            $finalName = $pieces ? implode(' — ', $pieces) : 'Termin';
        }
        $finalName = Str::limit($finalName, 255, '');

        // -----------------------------
        // 4) Build appointment slices (daily across date range)
        // -----------------------------
        $appointments = [];
        $cursor = $startDate;
        while ($cursor->lte($endDate)) {
            $appointments[] = [
                'start_date' => $cursor->toDateString(),
                'end_date' => $cursor->toDateString(),
                'start_time' => $startTime,
                'end_time' => $endTime,
            ];
            $cursor = $cursor->addDay();
        }
        if (empty($appointments)) {
            $appointments = [
                [
                    'start_date' => $startDate->toDateString(),
                    'end_date' => $endDate->toDateString(),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]
            ];
        }

        // -----------------------------
        // 5) Contact resolution
        // -----------------------------
        $contactMode = $request->contact_mode ?: 'new';
        $contactType = $request->input('contact_type'); // "Kunde" | "Kontakt" | custom
        $finalCustomerId = null;
        $finalContactId = null;
        $finalOtherId = null;

        if ($contactMode === 'select') {
            $selCustomerId = $request->input('customer_id');
            $selContactId = $request->input('contact_id');

            if ($selCustomerId)
                $finalCustomerId = $selCustomerId;
            if ($selContactId)
                $finalContactId = $selContactId;

            if (!$contactType) {
                $contactType = $finalCustomerId ? 'Kunde' : ($finalContactId ? 'Kontakt' : null);
            }
        } elseif ($contactMode === 'ticket') {
            $finalCustomerId = $request->input('customer_id') ?: null;
            $finalContactId = $request->input('contact_id') ?: null;
            if (!$contactType) {
                $contactType = $finalCustomerId ? 'Kunde' : ($finalContactId ? 'Kontakt' : null);
            }
        }

        // -----------------------------
        // 6) Ticket linkage
        // -----------------------------
        $linkedProblemId = null;
        $linkedTaskId = null;

        if ($contactMode === 'ticket') {
            $linkedProblemId = $request->input('problem_id');
            $linkedTaskId = $request->input('problem_task_id');

            if (!$linkedTaskId && (int) $request->input('ticket_auto_create', 0) === 1 && $linkedProblemId) {
                $taskTitle = $finalName;
                $ticketTask = \App\Models\TicketTask::create([
                    'problem_id' => $linkedProblemId,
                    'title' => $taskTitle,
                    'status' => 'open',
                    'description' => $validated['description'] ?? null,
                    'due_date' => $startDate->toDateString(),
                    'priority' => $validated['priority'],
                ]);
                $linkedTaskId = $ticketTask->id ?? null;
            }
        }

        // -----------------------------
        // 7) Prepare employees
        // -----------------------------
        $createdByEmployeeId = auth()->user()->name; // in your project created_by is employee name/string
        $employeeIds = collect($validated['employee'] ?? [])->filter()->unique()->values()->all();

        // -----------------------------
        // 8) Transaction: create inquiry first (if is_contact) then appointments
        // -----------------------------
        DB::beginTransaction();
        try {
            $firstAppointmentId = null;
            $createdInquiry = null;

            if ($request->boolean('is_contact')) {
                Log::info('STORE inquiry branch entered (before appointments)', [
                    'is_contact' => $request->is_contact,
                    'products_json' => $productsJson,
                    'inquiries_raw' => $request->inquiries,
                ]);

                $officeEmployeeId = $createdByEmployeeId;

                $createdInquiry = \App\Models\Inquiry::create([
                    'pre_type' => $request->pre_type,
                    'source' => $request->source,
                    'title' => $request->name,
                    'lastname' => $request->name,
                    'type' => $request->appointment_type,
                    'type_extra' => $request->execution_type,
                    'firma' => null,
                    'name' => null,
                    'street' => $request->street,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'elevation' => null,
                    'postcode' => $request->postcode,
                    'full_address' => $request->full_address,
                    'city' => $request->city,
                    'phone' => $request->phone,
                    'telephone' => null,
                    'email' => $request->email,
                    'note' => $request->description,
                    'reason' => null,
                    'status' => 'Unpublished',
                    'periority' => $request->priority,
                    'next_step' => $request->next_step,
                    'personal_task_id' => null,                 // no personal task
                    'branch_id' => $request->branch_id ?? 1,
                    'contact_person' => $officeEmployeeId,
                    'verify_by' => null,
                    'department_id' => null,
                    'direct_to' => null,
                    'verify_date' => null,
                ]);

                if ($createdInquiry) {
                    // use the new inquiry as contact for the appointment
                    $finalContactId = $createdInquiry->id;
                    if (!$contactType) {
                        $contactType = 'inquiry';
                    }

                    // Sync inquiry product lines based on inquiry rows snapshot
                    $this->syncInquiryProductsFromAppointment(
                        $createdInquiry,
                        $inquiriesJson,
                        $employeeIds,
                        $appointments[0]['start_date'] ?? $validated['start_date'],
                        $appointments[0]['start_time'] ?? ($validated['start_time'] ?? '08:00')
                    );
                }
            }

            // -----------------------------
            // 9) Create appointments + employees
            // -----------------------------
            $baseAppointmentData = [
                'created_by' => $createdByEmployeeId,
                'name' => $finalName,
                'note' => $validated['description'] ?? null,
                'color' => $validated['color'] ?? '#8fc73e',
                'priority' => $validated['priority'],
                'lead_product_list_id' => $stageContext['lead_product_list_id'],
                'lead_stage_id' => $stageContext['lead_stage_id'],
                'lead_stage_sub_stage_id' => $stageContext['lead_stage_sub_stage_id'],

                'customer_id' => $finalCustomerId,
                'other_id' => $finalOtherId,
                'contact_id' => $finalContactId,   // inquiry id when is_contact = 1
                'contact_type' => $contactType,
                'contact_mode' => $contactMode,
                'is_contact' => $request->boolean('is_contact') ? '1' : '0',

                'reminder_date' => $request->reminder_date,
                'reminder_time' => $request->reminder_time,
                'next_step' => $request->next_step,
                'report_responsible' => json_encode($reportResponsible, JSON_UNESCAPED_UNICODE),

                'appointment_type' => $request->appointment_type,
                'execution_type' => $request->execution_type,
                'full_address' => $request->full_address,
                'street' => $request->street,
                'city' => $request->city,
                'postcode' => $request->postcode,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'phone' => $request->phone,
                'email' => $request->email,
                'link' => $request->link,
                'date_type' => $request->date_type,
                'from_day' => $request->from_day,
                'to_day' => $request->to_day,
                'from_month' => $request->from_month,
                'to_month' => $request->to_month,
                'public' => $request->boolean('public') ? '1' : '0',
                'branch_id' => $request->branch_id,
                'branch_address_id' => $request->branch_address_id,
                'pre_type' => $request->pre_type,
                'source' => $request->source,
                'task_id' => null,                  // no personal task
                'is_notified' => false,
                'is_report' => $request->boolean('is_report') ? '1' : '0',
                'type' => 'appointment',

                'products' => $productsJson,
                'problem_id' => $linkedProblemId,
                'problem_task_id' => $linkedTaskId,
                'product_inquiry' => $inquiriesJson,
            ];

            foreach ($appointments as $slice) {
                $appointment = \App\Models\MainAppointment::create(array_merge($baseAppointmentData, [
                    'start_date' => $slice['start_date'],
                    'end_date' => $slice['end_date'],
                    'start_time' => $slice['start_time'],
                    'end_time' => $slice['end_time'],
                ]));

                // If appointment reminder/next-step is filled, create/link the personal task and save the selected CRM stage there too.
                $this->syncAppointmentPersonalTask($appointment, $request, $finalName, $employeeIds, $stageContext);

                foreach ($employeeIds as $empId) {
                    \App\Models\MainAppointmentEmployee::create([
                        'appointment_id' => $appointment->id,
                        'employee_id' => $empId,
                        'status' => 'accept',
                    ]);
                }

                $firstAppointmentId = $firstAppointmentId ?? $appointment->id;
            }

            if ($linkedTaskId && $firstAppointmentId) {
                \App\Models\TicketTask::where('id', $linkedTaskId)->update([
                    'appointment_id' => $firstAppointmentId,
                ]);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Termin erfolgreich gespeichert.',
                ]);
            }

            return redirect()->route('main.appointment')->with('success', 'Termin erfolgreich gespeichert.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Store failed', ['error' => $e->getMessage(), 'line' => $e->getLine()]);
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Speichern.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Accepts array|JSON|string|null and returns a compact JSON string.
     * Guarantees valid JSON for DB storage.
     */
private function normalizeProductsToJson($input): string
{
    if (is_null($input) || $input === '') {
        return '{}';
    }
    if (is_array($input)) {
        return json_encode($input, JSON_UNESCAPED_UNICODE);
    }
    if (is_string($input)) {
        // If already JSON, return compacted; if not, wrap as opaque string value.
        $decoded = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
            return json_encode($decoded, JSON_UNESCAPED_UNICODE);
        }
        // last resort: store as {"_raw":"..."} so you never break storage
        return json_encode(['_raw' => $input], JSON_UNESCAPED_UNICODE);
    }
    // unexpected type
    return '{}';
}

    /**
     * Create inquiry_product_lists rows from an appointment's selected products.
     *
     * - $productsJson is what you already store in main_appointments.products
     *   (JSON from #product, built by loadCustomerProducts()).
     *   Structure: { "ProductName": [alternative_id, product_id, customer_id], ... }
     *
     * - $appointmentEmployeeIds is the array from the appointment form (field "employee[]").
     *   We use:
     *     employee_id    = current user (office / contact_person)
     *     field_employee = first selected participant from the appointment
     */

    protected function syncInquiryProductsFromAppointment(
        Inquiry $inquiry,
        ?string $jsonPayload,
        array $appointmentEmployeeIds,
        ?string $anchorDate,
        ?string $anchorTime
        ): void {
        if (!$jsonPayload || trim($jsonPayload) === '' || $jsonPayload === '{}' || $jsonPayload === '[]') {
            return;
        }

        $decoded = json_decode($jsonPayload, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded) || empty($decoded)) {
            return;
        }

        // Fallback employees if a row doesn’t specify them
        $fallbackIndoorId = !empty($appointmentEmployeeIds) ? (int) $appointmentEmployeeIds[0] : null;
        $fallbackFieldId  = !empty($appointmentEmployeeIds) ? (int) $appointmentEmployeeIds[0] : null;

        // Build a Carbon for appointment_date on the product list
        $appointmentDateTime = null;
        if ($anchorDate) {
            $time = $anchorTime ?: '08:00';
            try { $appointmentDateTime = Carbon::parse("$anchorDate $time"); } catch (\Throwable $e) {}
        }

        /**
         * Helper: try to resolve department/service for a product (article_group_id)
         */
        $resolveDeptAndService = function (?int $articleGroupId): array {
            if (!$articleGroupId) return [null, null];
            $rows = DB::table('product_positions')
                ->where('article_group_id', $articleGroupId)
                ->select('department_id','service_id')
                ->get();

            $departmentId = optional($rows->pluck('department_id')->filter()->first()) ?? null;
            $serviceId    = optional($rows->pluck('service_id')->filter()->first()) ?? null;
            return [$departmentId, $serviceId];
        };

        /**
         * Two supported shapes:
         * A) New inquiry rows     : [ ['product_id'=>..,'department_id'=>..,'service_id'=>..,'employee_id'=>..,'field_employee_id'=>..], ... ]
         * B) Old products mapping  : { "Name": [alt_id, product_id, customer_id], ... }
         */
        $isAssocList = array_is_list($decoded) === false; // true for old products-json (object), false for inquiry rows (list)

        if ($isAssocList) {
            // Shape B: old products JSON
            foreach ($decoded as $productName => $payload) {
                if (!is_array($payload) || count($payload) < 2) {
                    continue;
                }
                // [alternative_id, product_id, customer_id]
                $articleGroupId = (int) $payload[1];

                [$departmentId, $serviceId] = $resolveDeptAndService($articleGroupId);

                InquiryProductList::create([
                    'inquiry_id'       => $inquiry->id,
                    'department_id'    => $departmentId,
                    'employee_id'      => $fallbackIndoorId,   // Innendienst (fallback)
                    'field_employee'   => $fallbackFieldId,    // Außendienst (fallback)
                    'product_id'       => $articleGroupId,
                    'service_id'       => $serviceId,
                    'status'           => 'open',
                    'appointment_date' => $appointmentDateTime,
                ]);
            }
            return;
        }

        // Shape A: inquiry rows (preferred)
        foreach ($decoded as $row) {
            if (!is_array($row)) continue;

            $articleGroupId = isset($row['product_id']) ? (int) $row['product_id'] : null;
            $departmentId   = isset($row['department_id']) ? (int) $row['department_id'] : null;
            $serviceId      = isset($row['service_id']) ? (int) $row['service_id'] : null;

            // Allow auto-resolution when not provided
            if (!$departmentId || !$serviceId) {
                [$autoDept, $autoSvc] = $resolveDeptAndService($articleGroupId);
                $departmentId = $departmentId ?: $autoDept;
                $serviceId    = $serviceId ?: $autoSvc;
            }

            $indoorId = isset($row['employee_id']) ? (int) $row['employee_id'] : $fallbackIndoorId;
            $fieldId  = isset($row['field_employee_id']) ? (int) $row['field_employee_id'] : $fallbackFieldId;

            // Require at least a product_id to create a line
            if (!$articleGroupId) continue;

            InquiryProductList::create([
                'inquiry_id'       => $inquiry->id,
                'department_id'    => $departmentId,
                'employee_id'      => $indoorId,     // Innendienst (explicit per row if provided)
                'field_employee'   => $fieldId,      // Außendienst (explicit per row if provided)
                'product_id'       => $articleGroupId,
                'service_id'       => $serviceId,
                'status'           => 'open',
                'appointment_date' => $appointmentDateTime,
            ]);
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

        // 🧠 Follow-up (Termin-Konvergenz F4/Ansatz A): EIN Follow-up ueber den geteilten Service,
        // statt der Legacy-Doppel-Erzeugung. UPSERT by (source_type='main_appointment',
        // source_id=appointment.id) -> Update erzeugt KEIN zweites Follow-up, sondern aktualisiert.
        // type='follow_up', art=NULL; next_step->task_title, reminder_date->due_date,
        // report_responsible->Verantwortliche (1:1).
        if ($request->reminder_date && $request->next_step && $request->report_responsible) {
            $followUpTask = app(\App\Services\FollowUp\FollowUpCreator::class)->sync(
                'main_appointment',
                (int) $appointment->id,
                [
                    'customer_id' => $appointment->customer_id ?: $request->input('customer_id'),
                    'description' => 'Aus Termin: ' . $finalName,
                ],
                [
                    'follow_up_art' => null,
                    'next_step'     => $request->next_step,
                    'due_date'      => $request->reminder_date,
                    'responsible'   => (array) $request->report_responsible,
                ],
                (int) auth()->user()->name
            );

            if ((int) $appointment->task_id !== (int) $followUpTask->id) {
                $appointment->forceFill(['task_id' => $followUpTask->id])->save();
            }
        }

        // 🔑 Inquiry handling
        if ($request->contact_mode !== null) {
           $officeEmployeeId = auth()->user()->name;

            $inquiryData = [
                'pre_type'         => $request->pre_type,
                'source'           => $request->source,

                // calendar title → title + lastname
                'title'            => $request->name,
                'lastname'         => $request->name,

                'type'             => $request->appointment_type,
                'type_extra'       => $request->execution_type,

                'firma'            => null,
                'name'             => null,

                'street'           => $request->street,
                'latitude'         => $request->latitude,
                'longitude'        => $request->longitude,
                'elevation'        => null,
                'postcode'         => $request->postcode,
                'full_address'     => $request->full_address,
                'city'             => $request->city,

                'phone'            => $request->phone,
                'telephone'        => null,
                'email'            => $request->email,

                'note'             => $request->description,
                'reason'           => null,
                'status'           => 'Unpublished',
                'periority'        => $request->priority,
                'next_step'        => $request->next_step,

                'personal_task_id' => $appointment->task_id ?? null,
                'branch_id'        => $request->branch_id ?? 1,
                'contact_person'   => $officeEmployeeId,
                'verify_by'        => null,
                'department_id'    => null,
                'direct_to'        => null,
                'verify_date'      => null,
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


    public function fetch(int $id)
    {
        \Log::info('MainAppointment::fetch hit', [
            'id' => $id,
            'url' => request()->fullUrl(),
            'ip' => request()->ip(),
            'route' => optional(request()->route())->getName(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 1) Base row + joins
        |--------------------------------------------------------------------------
        | Important:
        | - Appointment stage is read from main_appointments.
        | - If old appointment has no stage yet, fallback is read from linked PersonalTask.
        |--------------------------------------------------------------------------
        */
        $appointment = DB::table('main_appointments')
            ->leftJoin('branches', 'branches.id', '=', 'main_appointments.branch_id')
            ->leftJoin('branch_addresses', 'branch_addresses.id', '=', 'main_appointments.branch_address_id')
            ->leftJoin('new_leads', 'new_leads.id', '=', 'main_appointments.contact_id')
            ->leftJoin('employees as creator', 'creator.id', '=', 'main_appointments.created_by')
            ->leftJoin('employees as changer', 'changer.id', '=', 'main_appointments.changed_by')

            // Linked personal task fallback
            ->leftJoin('personal_tasks as linked_task', 'linked_task.id', '=', 'main_appointments.task_id')

            // Appointment CRM Stage/Sub Stage
            ->leftJoin('lead_stages as appointment_stage', 'appointment_stage.id', '=', 'main_appointments.lead_stage_id')
            ->leftJoin('lead_stage_sub_stages as appointment_sub_stage', 'appointment_sub_stage.id', '=', 'main_appointments.lead_stage_sub_stage_id')

            // Linked task CRM Stage/Sub Stage fallback
            ->leftJoin('lead_stages as task_stage', 'task_stage.id', '=', 'linked_task.lead_stage_id')
            ->leftJoin('lead_stage_sub_stages as task_sub_stage', 'task_sub_stage.id', '=', 'linked_task.lead_stage_sub_stage_id')

            ->where('main_appointments.id', $id)
            ->whereNull('main_appointments.deleted_at')
            ->select([
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
                'changer.image as changed_by_image',

                // Appointment stage aliases
                'main_appointments.lead_product_list_id as appointment_lead_product_list_id',
                'main_appointments.lead_stage_id as appointment_lead_stage_id',
                'appointment_stage.name as appointment_lead_stage_name',
                'appointment_stage.color as appointment_lead_stage_color',
                'appointment_stage.key as appointment_lead_stage_key',

                'main_appointments.lead_stage_sub_stage_id as appointment_lead_stage_sub_stage_id',
                'appointment_sub_stage.name as appointment_lead_stage_sub_stage_name',
                'appointment_sub_stage.color as appointment_lead_stage_sub_stage_color',
                'appointment_sub_stage.key as appointment_lead_stage_sub_stage_key',

                // Linked personal task fallback aliases
                'linked_task.lead_product_list_id as task_lead_product_list_id',
                'linked_task.lead_stage_id as task_lead_stage_id',
                'task_stage.name as task_lead_stage_name',
                'task_stage.color as task_lead_stage_color',
                'task_stage.key as task_lead_stage_key',

                'linked_task.lead_stage_sub_stage_id as task_lead_stage_sub_stage_id',
                'task_sub_stage.name as task_lead_stage_sub_stage_name',
                'task_sub_stage.color as task_lead_stage_sub_stage_color',
                'task_sub_stage.key as task_lead_stage_sub_stage_key',
            ])
            ->first();

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found.'], 404);
        }

        \Log::info('Fetched Appointment:', [$appointment]);

        /*
        |--------------------------------------------------------------------------
        | 2) Employees
        |--------------------------------------------------------------------------
        */
        $employees = DB::table('main_appointment_employees')
            ->join('employees', 'employees.id', '=', 'main_appointment_employees.employee_id')
            ->where('main_appointment_employees.appointment_id', $id)
            ->select('employees.id', 'employees.name', 'employees.lastname', 'employees.image')
            ->get();

        $employeeIds = $employees
            ->pluck('id')
            ->map(fn($value) => (int) $value)
            ->values()
            ->all();

        \Log::info('Fetched Employees:', [$employees]);

        /*
        |--------------------------------------------------------------------------
        | 3) Safe JSON decodes
        |--------------------------------------------------------------------------
        */
        $responsibleReport = [];
        try {
            $responsibleReport = json_decode($appointment->report_responsible ?? '[]', true) ?: [];
            if (!is_array($responsibleReport)) {
                $responsibleReport = [];
            }
        } catch (\Throwable $e) {
            \Log::warning('report_responsible decode failed', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            $responsibleReport = [];
        }

        $productJson = [];
        try {
            $productJsonRaw = $appointment->products ?? '{}';
            $productJson = json_decode($productJsonRaw, true);

            if (is_string($productJson)) {
                $productJson = json_decode($productJson, true);
            }

            if (!is_array($productJson)) {
                $productJson = [];
            }
        } catch (\Throwable $e) {
            \Log::warning('products decode failed', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            $productJson = [];
        }

        $productInquiry = [];
        if (\Illuminate\Support\Facades\Schema::hasColumn('main_appointments', 'product_inquiry')) {
            try {
                $productInquiry = json_decode($appointment->product_inquiry ?? '[]', true) ?: [];
                if (!is_array($productInquiry)) {
                    $productInquiry = [];
                }
            } catch (\Throwable $e) {
                \Log::warning('product_inquiry decode failed', [
                    'id' => $id,
                    'error' => $e->getMessage(),
                ]);
                $productInquiry = [];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4) CRM Stage context for edit modal
        |--------------------------------------------------------------------------
        | Priority:
        | 1. main_appointments columns
        | 2. linked personal_tasks columns
        | 3. linked lead_product_lists stage/sub-stage
        |--------------------------------------------------------------------------
        */
        $stageContext = [
            'lead_product_list_id' => !empty($appointment->appointment_lead_product_list_id)
                ? (int) $appointment->appointment_lead_product_list_id
                : (!empty($appointment->task_lead_product_list_id) ? (int) $appointment->task_lead_product_list_id : null),

            'lead_stage_id' => !empty($appointment->appointment_lead_stage_id)
                ? (int) $appointment->appointment_lead_stage_id
                : (!empty($appointment->task_lead_stage_id) ? (int) $appointment->task_lead_stage_id : null),

            'lead_stage_name' => $appointment->appointment_lead_stage_name
                ?: $appointment->task_lead_stage_name,

            'lead_stage_color' => $appointment->appointment_lead_stage_color
                ?: $appointment->task_lead_stage_color,

            'lead_stage_key' => $appointment->appointment_lead_stage_key
                ?: $appointment->task_lead_stage_key,

            'lead_stage_sub_stage_id' => !empty($appointment->appointment_lead_stage_sub_stage_id)
                ? (int) $appointment->appointment_lead_stage_sub_stage_id
                : (!empty($appointment->task_lead_stage_sub_stage_id) ? (int) $appointment->task_lead_stage_sub_stage_id : null),

            'lead_stage_sub_stage_name' => $appointment->appointment_lead_stage_sub_stage_name
                ?: $appointment->task_lead_stage_sub_stage_name,

            'lead_stage_sub_stage_color' => $appointment->appointment_lead_stage_sub_stage_color
                ?: $appointment->task_lead_stage_sub_stage_color,

            'lead_stage_sub_stage_key' => $appointment->appointment_lead_stage_sub_stage_key
                ?: $appointment->task_lead_stage_sub_stage_key,
        ];

        /*
        |--------------------------------------------------------------------------
        | 4.1) Fallback from LeadProductList if appointment/task only has LPL ID
        |--------------------------------------------------------------------------
        */
        if (!empty($stageContext['lead_product_list_id']) && empty($stageContext['lead_stage_id'])) {
            $productStage = DB::table('lead_product_lists')
                ->leftJoin('lead_stages', 'lead_stages.key', '=', 'lead_product_lists.status')
                ->leftJoin('lead_stage_sub_stages', 'lead_stage_sub_stages.id', '=', 'lead_product_lists.lead_stage_sub_stage_id')
                ->where('lead_product_lists.id', $stageContext['lead_product_list_id'])
                ->select([
                    'lead_product_lists.id as lead_product_list_id',
                    'lead_product_lists.status as lead_stage_key',
                    'lead_product_lists.lead_stage_sub_stage_id',

                    'lead_stages.id as lead_stage_id',
                    'lead_stages.name as lead_stage_name',
                    'lead_stages.color as lead_stage_color',
                    'lead_stages.key as stage_key',

                    'lead_stage_sub_stages.name as lead_stage_sub_stage_name',
                    'lead_stage_sub_stages.color as lead_stage_sub_stage_color',
                    'lead_stage_sub_stages.key as lead_stage_sub_stage_key',
                ])
                ->first();

            if ($productStage) {
                $stageContext['lead_stage_id'] = $productStage->lead_stage_id ? (int) $productStage->lead_stage_id : null;
                $stageContext['lead_stage_name'] = $productStage->lead_stage_name;
                $stageContext['lead_stage_color'] = $productStage->lead_stage_color;
                $stageContext['lead_stage_key'] = $productStage->stage_key ?: $productStage->lead_stage_key;

                $stageContext['lead_stage_sub_stage_id'] = $productStage->lead_stage_sub_stage_id ? (int) $productStage->lead_stage_sub_stage_id : null;
                $stageContext['lead_stage_sub_stage_name'] = $productStage->lead_stage_sub_stage_name;
                $stageContext['lead_stage_sub_stage_color'] = $productStage->lead_stage_sub_stage_color;
                $stageContext['lead_stage_sub_stage_key'] = $productStage->lead_stage_sub_stage_key;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5) Product options for #productSelect by customer
        |--------------------------------------------------------------------------
        | Important:
        | - "id" stays compatible with your old Select2 value.
        | - "lead_product_list_id" is the real integer value for hidden input.
        |--------------------------------------------------------------------------
        */
        $productOptions = [];

        if (!empty($appointment->customer_id)) {
            $objects = DB::table('lead_alternative_adds')
                ->where('lead_id', $appointment->customer_id)
                ->select('id', 'object_name', 'city')
                ->get();

            if ($objects->isNotEmpty()) {
                $altIds = $objects
                    ->pluck('id')
                    ->filter()
                    ->map(fn($value) => (int) $value)
                    ->values()
                    ->all();

                $products = DB::table('lead_product_lists')
                    ->where('lead_product_lists.customer_id', $appointment->customer_id)
                    ->whereIn('lead_product_lists.alternative_id', $altIds)
                    ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
                    ->leftJoin('lead_stages', 'lead_stages.key', '=', 'lead_product_lists.status')
                    ->leftJoin('lead_stage_sub_stages', 'lead_stage_sub_stages.id', '=', 'lead_product_lists.lead_stage_sub_stage_id')
                    ->select([
                        'lead_product_lists.id as lead_product_list_id',
                        'lead_product_lists.customer_id',
                        'lead_product_lists.alternative_id',
                        'lead_product_lists.product_id',
                        'lead_product_lists.status as lead_stage_key',
                        'lead_product_lists.lead_stage_sub_stage_id',

                        'article_groups.article_group',

                        'lead_stages.id as lead_stage_id',
                        'lead_stages.name as lead_stage_name',
                        'lead_stages.color as lead_stage_color',
                        'lead_stages.key as stage_key',

                        'lead_stage_sub_stages.name as lead_stage_sub_stage_name',
                        'lead_stage_sub_stages.color as lead_stage_sub_stage_color',
                        'lead_stage_sub_stages.key as lead_stage_sub_stage_key',
                    ])
                    ->get()
                    ->groupBy('alternative_id');

                foreach ($objects as $object) {
                    $rows = $products->get($object->id, collect());

                    if ($rows->isEmpty()) {
                        continue;
                    }

                    $children = $rows->map(function ($p) use ($appointment, $object, $stageContext) {
                        $leadProductListId = !empty($p->lead_product_list_id) ? (int) $p->lead_product_list_id : null;
                        $leadStageId = !empty($p->lead_stage_id) ? (int) $p->lead_stage_id : null;
                        $leadSubStageId = !empty($p->lead_stage_sub_stage_id) ? (int) $p->lead_stage_sub_stage_id : null;

                        return [
                            /*
                            |--------------------------------------------------------------------------
                            | Keep old Select2 value compatible
                            |--------------------------------------------------------------------------
                            */
                            'id' => ($p->article_group ?: 'product') . '_' . $object->id,

                            'text' => $p->article_group,
                            'product_name' => $p->article_group,

                            /*
                            |--------------------------------------------------------------------------
                            | Real IDs used by appointment stage logic
                            |--------------------------------------------------------------------------
                            */
                            'lead_product_list_id' => $leadProductListId,
                            'product_id' => !empty($p->product_id) ? (int) $p->product_id : null,
                            'alternative_id' => !empty($object->id) ? (int) $object->id : null,
                            'customer_id' => !empty($appointment->customer_id) ? (int) $appointment->customer_id : null,

                            /*
                            |--------------------------------------------------------------------------
                            | CRM Stage/Sub Stage context for auto-fill
                            |--------------------------------------------------------------------------
                            */
                            'lead_stage_id' => $leadStageId,
                            'lead_stage_key' => $p->stage_key ?: $p->lead_stage_key,
                            'lead_stage_name' => $p->lead_stage_name,
                            'lead_stage_color' => $p->lead_stage_color,

                            'lead_stage_sub_stage_id' => $leadSubStageId,
                            'lead_stage_sub_stage_name' => $p->lead_stage_sub_stage_name,
                            'lead_stage_sub_stage_color' => $p->lead_stage_sub_stage_color,
                            'lead_stage_sub_stage_key' => $p->lead_stage_sub_stage_key,

                            'lead_stage_context' => [
                                'lead_product_list_id' => $leadProductListId,
                                'lead_stage_id' => $leadStageId,
                                'lead_stage_key' => $p->stage_key ?: $p->lead_stage_key,
                                'lead_stage_name' => $p->lead_stage_name,
                                'lead_stage_color' => $p->lead_stage_color,

                                'lead_stage_sub_stage_id' => $leadSubStageId,
                                'lead_stage_sub_stage_name' => $p->lead_stage_sub_stage_name,
                                'lead_stage_sub_stage_color' => $p->lead_stage_sub_stage_color,
                                'lead_stage_sub_stage_key' => $p->lead_stage_sub_stage_key,
                            ],

                            /*
                            |--------------------------------------------------------------------------
                            | UI metadata
                            |--------------------------------------------------------------------------
                            */
                            'city' => $object->city,
                            'object_name' => $object->object_name ?: ('Objekt ' . $object->id),

                            /*
                            |--------------------------------------------------------------------------
                            | Mark current appointment product if possible
                            |--------------------------------------------------------------------------
                            */
                            'selected' => $leadProductListId
                                && !empty($stageContext['lead_product_list_id'])
                                && (int) $stageContext['lead_product_list_id'] === (int) $leadProductListId,
                        ];
                    })->values()->all();

                    $productOptions[] = [
                        'text' => $object->object_name ?: ('Objekt ' . $object->id),
                        'children' => $children,
                    ];
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6) Normalize times
        |--------------------------------------------------------------------------
        */
        $startTime = $appointment->start_time
            ? date('H:i', strtotime((string) $appointment->start_time))
            : null;

        $endTime = $appointment->end_time
            ? date('H:i', strtotime((string) $appointment->end_time))
            : null;

        $startAt = ($appointment->start_date && $startTime)
            ? date('c', strtotime($appointment->start_date . ' ' . $startTime))
            : null;

        $endAt = ($appointment->end_date && $endTime)
            ? date('c', strtotime($appointment->end_date . ' ' . $endTime))
            : null;

        \Log::info('Fetched Product Inquiry:', [$productInquiry]);
        \Log::info('Fetched Stage Context:', [$stageContext]);

        /*
        |--------------------------------------------------------------------------
        | 7) Response
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'id' => (int) $appointment->id,
            'name' => $appointment->name,
            'appointment_type' => $appointment->appointment_type,
            'execution_type' => $appointment->execution_type,
            'contact_mode' => $appointment->contact_mode,
            'contact_type' => $appointment->contact_type,
            'contact_id' => $appointment->contact_id ? (int) $appointment->contact_id : null,
            'customer_id' => $appointment->customer_id ? (int) $appointment->customer_id : null,
            'other_id' => $appointment->other_id ? (int) $appointment->other_id : null,
            'note' => $appointment->note,
            'description' => $appointment->note,
            'color' => $appointment->color,
            'priority' => $appointment->priority,
            'status' => $appointment->status,
            'type' => $appointment->type,
            'is_notified' => (bool) $appointment->is_notified,
            'public' => (int) ($appointment->public ?? 0),

            'start_date' => $appointment->start_date,
            'end_date' => $appointment->end_date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'start_at' => $startAt,
            'end_at' => $endAt,
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
            'is_contact' => $appointment->is_contact,
            'is_report' => $appointment->is_report,
            'pre_type' => (string) $appointment->is_contact === '1' ? $appointment->pre_type : null,
            'source' => (string) $appointment->is_contact === '1' ? $appointment->source : null,

            'full_address' => $appointment->full_address,
            'street' => $appointment->street,
            'city' => $appointment->city,
            'postcode' => $appointment->postcode,
            'latitude' => $appointment->latitude,
            'longitude' => $appointment->longitude,

            'branch_id' => $appointment->branch_id ? (int) $appointment->branch_id : null,
            'branch_name' => $appointment->branch_name,
            'branch_address_id' => $appointment->branch_address_id ? (int) $appointment->branch_address_id : null,
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

            /*
            |--------------------------------------------------------------------------
            | CRM Stage/Sub Stage for edit modal
            |--------------------------------------------------------------------------
            */
            'lead_product_list_id' => $stageContext['lead_product_list_id'],
            'lead_stage_id' => $stageContext['lead_stage_id'],
            'lead_stage_name' => $stageContext['lead_stage_name'],
            'lead_stage_color' => $stageContext['lead_stage_color'],
            'lead_stage_key' => $stageContext['lead_stage_key'],

            'lead_stage_sub_stage_id' => $stageContext['lead_stage_sub_stage_id'],
            'lead_stage_sub_stage_name' => $stageContext['lead_stage_sub_stage_name'],
            'lead_stage_sub_stage_color' => $stageContext['lead_stage_sub_stage_color'],
            'lead_stage_sub_stage_key' => $stageContext['lead_stage_sub_stage_key'],

            'lead_stage_context' => $stageContext,

            'change_date' => $appointment->change_date,
            'change_reason' => $appointment->change_reason,
            'created_by' => $appointment->created_by ? (int) $appointment->created_by : null,
            'created_by_name' => trim(($appointment->created_by_name ?? '') . ' ' . ($appointment->created_by_lastname ?? '')),
            'created_by_image' => $appointment->created_by_image,
            'changed_by' => $appointment->changed_by ? (int) $appointment->changed_by : null,
            'changed_by_name' => trim(($appointment->changed_by_name ?? '') . ' ' . ($appointment->changed_by_lastname ?? '')),
            'changed_by_image' => $appointment->changed_by_image,
            'created_at' => $appointment->created_at,
            'updated_at' => $appointment->updated_at,
            'deleted_at' => $appointment->deleted_at,

            'contact_name' => $appointment->contact_name,
            'contact_lastname' => $appointment->contact_lastname,

            'product_options' => $productOptions,
            'product_json' => $productJson,
            'product_inquiry' => $productInquiry,
        ]);
    }

    public function updateAjax(Request $request, $id)
    {
        Log::info('Ajax Update Request:', [$request->all()]);

        $request->validate([
            'id' => 'required|exists:main_appointments,id',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after_or_equal:start_time',
            'employee' => 'required|array|min:1',
            'employee.*' => 'exists:employees,id',
            'priority' => 'required|string|in:normal,medium,high,very high',
            'reminder_date' => 'nullable|date',
            'reminder_time' => 'nullable|date_format:H:i',
            'report_responsible' => 'nullable',
            'next_step' => 'nullable|string|max:255',

            // products + inquiry snapshot (like in store)
            'products' => 'nullable',
            'inquiries' => 'nullable|array',
            'lead_product_list_id' => 'nullable|integer|exists:lead_product_lists,id',
            'lead_stage_id' => 'nullable|integer|exists:lead_stages,id',
            'lead_stage_sub_stage_id' => 'nullable|integer|exists:lead_stage_sub_stages,id',
        ]);

        if ((string) $id !== (string) $request->id) {
            return response()->json([
                'success' => false,
                'message' => 'Route id and payload id mismatch.',
            ], 422);
        }

        // build inquiries snapshot (same shape as in store())
        $inquiriesArr = collect($request->input('inquiries', []))
            ->map(fn($row) => [
                'product_id' => $row['product_id'] ?? null,
                'department_id' => $row['department_id'] ?? null,
                'service_id' => $row['service_id'] ?? null,
                'employee_id' => $row['employee_id'] ?? null, // Innendienst
                'field_employee_id' => $row['field_employee_id'] ?? null, // Außendienst
            ])
            ->filter(
                fn($r) =>
                $r['product_id'] ||
                $r['department_id'] ||
                $r['service_id'] ||
                $r['employee_id'] ||
                $r['field_employee_id']
            )
            ->values();

        $inquiriesJson = $inquiriesArr->toJson(JSON_UNESCAPED_UNICODE);
        $stageContext = $this->resolveAppointmentStageContext($request);

        DB::beginTransaction();
        try {
            /** @var \App\Models\MainAppointment $appointment */
            $appointment = MainAppointment::findOrFail($id);

            // ---------- products: keep existing when nothing sent ----------
            // raw value from hidden input #products (JSON string or null)
            $rawProducts = $request->input('products');

            if ($rawProducts === null || $rawProducts === '') {
                // nothing sent -> keep current DB value
                $productsJson = $appointment->products;
            } else {
                // normalize (handles JSON string or array)
                $productsJson = $this->normalizeProductsToJson($rawProducts);
            }

            // keep the previous snapshot for comparison (for inquiry product sync)
            $oldInquirySnapshot = [];
            if (!empty($appointment->product_inquiry)) {
                try {
                    $oldInquirySnapshot = json_decode($appointment->product_inquiry, true) ?: [];
                    if (!is_array($oldInquirySnapshot)) {
                        $oldInquirySnapshot = [];
                    }
                } catch (\Throwable $e) {
                    $oldInquirySnapshot = [];
                }
            }

            // ---------- contact resolution ----------
            $finalName = $request->name;
            $finalCustomerId = null;
            $finalOtherId = null;
            $finalContactId = $request->contact_id ?? $request->customer_id ?? $appointment->contact_id;
            $contactType = $request->contact_type ?: $appointment->contact_type;

            if ($request->contact_mode === 'select' && $request->customer_id) {
                $customer = \App\Models\NewLeads::find($request->customer_id);
                if ($customer) {
                    $type = strtolower($customer->type ?? 'kunde');
                    $germanTypes = [
                        'customer' => 'Kunde',
                        'kunde' => 'Kunde',
                        'brand' => 'Marke',
                        'marke' => 'Marke',
                        'lieferant' => 'Lieferant',
                        'supplier' => 'Lieferant',
                        'distributor' => 'Lieferant',
                    ];
                    $typeGerman = $germanTypes[$type] ?? ucfirst($type);

                    if (in_array($type, ['marke', 'lieferant', 'brand', 'supplier', 'distributor'], true)) {
                        $finalName = ($customer->name ?? '') . " ({$typeGerman})";
                        $finalOtherId = $customer->id;
                    } else {
                        $finalName = trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? '')) . " ({$typeGerman})";
                        $finalCustomerId = $customer->id;
                    }
                } else {
                    $finalOtherId = $request->customer_id;
                }
            }

            // employees as array for product sync
            $employeeIds = is_array($request->employee) ? $request->employee : [];

            // ---------- inquiry sync logic (unchanged) ----------
            $inquiry = null;
            $inquiryIsPublished = false;
            $shouldHandleInquiry =
                !is_null($request->contact_mode)      // form in contact mode
                || (int) $appointment->is_contact === 1; // or appointment is already in inquiry-mode

            if ($shouldHandleInquiry) {
                $baseInquiryData = [
                    'pre_type' => $request->pre_type,
                    'source' => $request->source,
                    'title' => $finalName,
                    'type' => $request->appointment_type,
                    'type_extra' => $request->execution_type,
                    'name' => $request->name,
                    'street' => $request->street,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'postcode' => $request->postcode,
                    'full_address' => $request->full_address,
                    'city' => $request->city,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'note' => $request->description,
                    'periority' => $request->priority,
                    'next_step' => $request->next_step,
                    'branch_id' => $request->branch_id ?? 1,
                    'contact_person' => auth()->user()->name,
                    'personal_task_id' => $appointment->task_id ?: null,
                ];

                if ($appointment->contact_id) {
                    $inquiry = \App\Models\Inquiry::find($appointment->contact_id);
                }

                if (!$inquiry) {
                    $existing = \App\Models\Inquiry::query()
                        ->when($request->name, fn($q) => $q->where('name', $request->name))
                        ->when($request->email, fn($q) => $q->where('email', $request->email))
                        ->when($request->phone, fn($q) => $q->where('phone', $request->phone))
                        ->first();

                    if ($existing) {
                        $inquiry = $existing;
                        Log::info("Inquiry matched for updateAjax", ['id' => $existing->id]);
                    }
                }

                if ($inquiry) {
                    $inquiryIsPublished = (strcasecmp($inquiry->status, 'Published') === 0);

                    if ($inquiryIsPublished) {
                        Log::info("Inquiry is Published, skipping update & product sync", [
                            'inquiry_id' => $inquiry->id,
                        ]);
                    } else {
                        $updateData = array_merge($baseInquiryData, [
                            'status' => $inquiry->status ?? 'Unpublished',
                        ]);
                        $inquiry->update($updateData);
                        Log::info("Inquiry updated via appointment update", ['id' => $inquiry->id]);
                    }
                } else {
                    $createData = array_merge($baseInquiryData, [
                        'status' => 'Unpublished',
                    ]);
                    $inquiry = \App\Models\Inquiry::create($createData);
                    $inquiryIsPublished = false;
                    Log::info("Inquiry created from appointment update", ['id' => $inquiry->id]);
                }

                if ($inquiry) {
                    $appointment->contact_id = $inquiry->id;
                    $appointment->is_contact = 1;
                    $finalContactId = $inquiry->id;

                    if (!$inquiryIsPublished) {
                        $newSnapshot = [];
                        try {
                            $newSnapshot = json_decode($inquiriesJson, true) ?: [];
                            if (!is_array($newSnapshot)) {
                                $newSnapshot = [];
                            }
                        } catch (\Throwable $e) {
                            $newSnapshot = [];
                        }

                        $oldJson = json_encode($oldInquirySnapshot);
                        $newJson = json_encode($newSnapshot);

                        if ($oldJson !== $newJson) {
                            Log::info("Inquiry products changed, syncing inquiry_product_lists", [
                                'inquiry_id' => $inquiry->id,
                                'old' => $oldInquirySnapshot,
                                'new' => $newSnapshot,
                            ]);

                            $this->syncInquiryProductsFromAppointment(
                                $inquiry,
                                $inquiriesJson,
                                $employeeIds,
                                $request->start_date,
                                $request->start_time
                            );

                            if (\Illuminate\Support\Facades\Schema::hasColumn('main_appointments', 'product_inquiry')) {
                                \DB::table('main_appointments')
                                    ->where('id', $appointment->id)
                                    ->update(['product_inquiry' => $inquiriesJson]);
                                $appointment->product_inquiry = $inquiriesJson;
                            }
                        } else {
                            Log::info("Inquiry products unchanged, skipping product sync", [
                                'inquiry_id' => $inquiry->id,
                            ]);
                        }
                    } else {
                        Log::info("Published inquiry: product list remains untouched", [
                            'inquiry_id' => $inquiry->id,
                        ]);
                    }
                }
            } else {
                $appointment->is_contact = 0;
                $appointment->contact_id = null;
                $appointment->pre_type = null;
                $appointment->source = null;
                Log::info("Contact mode off. Inquiry link cleared", ['appointment_id' => $appointment->id]);
            }

            // ---------- personal task logic with CRM stage/sub-stage ----------
            $this->syncAppointmentPersonalTask($appointment, $request, $finalName, $employeeIds, $stageContext);

            // ---------- update appointment ----------
            $appointment->update([
                'name' => $finalName,
                'note' => $request->description,
                'color' => $request->color,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'total_time' => $request->total_time,
                'full_address' => $request->full_address,
                'street' => $request->street,
                'postcode' => $request->postcode,
                'city' => $request->city,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'phone' => $request->phone,
                'email' => $request->email,
                'appointment_type' => $request->appointment_type,
                'execution_type' => $request->execution_type,
                'date_type' => $request->date_type,
                'priority' => $request->priority,
                'lead_product_list_id' => $stageContext['lead_product_list_id'],
                'lead_stage_id' => $stageContext['lead_stage_id'],
                'lead_stage_sub_stage_id' => $stageContext['lead_stage_sub_stage_id'],
                'branch_id' => $request->branch_id,
                'branch_address_id' => $request->branch_address_id,
                'contact_type' => $contactType,
                'contact_id' => $finalContactId,
                'customer_id' => $finalCustomerId,
                'other_id' => $finalOtherId,
                'is_report' => $request->is_report === 'on' ? '1' : '0',
                'public' => $request->public === 'on' ? '1' : '0',
                'contact_mode' => $request->contact_mode,
                'reminder_date' => $request->reminder_date,
                'reminder_time' => $request->reminder_time,
                'next_step' => $request->next_step,
                'report_responsible' => is_array($request->report_responsible)
                    ? json_encode($request->report_responsible)
                    : $request->report_responsible,
                'products' => $productsJson,
            ]);

            // ---------- reassign employees ----------
            MainAppointmentEmployee::where('appointment_id', $appointment->id)->delete();
            foreach ($request->employee as $empId) {
                MainAppointmentEmployee::create([
                    'appointment_id' => $appointment->id,
                    'employee_id' => $empId,
                    'status' => 'accept',
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Appointment updated successfully.',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Appointment Update Failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update appointment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    protected function leadStageOptionPayload(LeadStage $stage): array
    {
        $stage->loadMissing('activeSubStages');

        return [
            'id' => (int) $stage->id,
            'key' => $stage->key,
            'name' => $stage->name,
            'color' => $stage->color ?: '#74b2d4',
            'icon' => $stage->icon,
            'sub_stages' => $stage->activeSubStages
                ->map(fn(LeadStageSubStage $subStage) => [
                    'id' => (int) $subStage->id,
                    'lead_stage_id' => (int) $subStage->lead_stage_id,
                    'key' => $subStage->key,
                    'name' => $subStage->name,
                    'color' => $subStage->color ?: '#93c21c',
                    'icon' => $subStage->icon,
                ])
                ->values()
                ->all(),
        ];
    }

    protected function resolveAppointmentStageContext(Request $request): array
    {
        $leadProductList = null;

        if ($request->filled('lead_product_list_id')) {
            $leadProductList = LeadProductList::query()
                ->with(['companyStage', 'leadStageSubStage'])
                ->find((int) $request->input('lead_product_list_id'));
        }

        $stage = null;

        if ($request->filled('lead_stage_id')) {
            $stage = LeadStage::query()
                ->active()
                ->with('activeSubStages')
                ->find((int) $request->input('lead_stage_id'));
        }

        if (!$stage && $leadProductList?->companyStage) {
            $stage = LeadStage::query()
                ->active()
                ->with('activeSubStages')
                ->whereKey($leadProductList->companyStage->id)
                ->first();
        }

        if (!$stage && $leadProductList && filled($leadProductList->status)) {
            $stage = LeadStage::query()
                ->active()
                ->with('activeSubStages')
                ->where('key', $leadProductList->status)
                ->first();
        }

        $subStage = null;

        if ($stage && $request->filled('lead_stage_sub_stage_id')) {
            $subStage = LeadStageSubStage::query()
                ->active()
                ->where('lead_stage_id', $stage->id)
                ->find((int) $request->input('lead_stage_sub_stage_id'));
        }

        if (!$subStage && $stage && $leadProductList?->lead_stage_sub_stage_id) {
            $subStage = LeadStageSubStage::query()
                ->active()
                ->where('lead_stage_id', $stage->id)
                ->find((int) $leadProductList->lead_stage_sub_stage_id);
        }

        return [
            'lead_product_list_id' => $leadProductList?->id,
            'lead_stage_id' => $stage?->id,
            'lead_stage_name' => $stage?->name,
            'lead_stage_color' => $stage?->color ?: '#74b2d4',
            'lead_stage_sub_stage_id' => $subStage?->id,
            'lead_stage_sub_stage_name' => $subStage?->name,
            'lead_stage_sub_stage_color' => $subStage?->color ?: '#93c21c',
        ];
    }

    public function leadStageContext(Request $request): JsonResponse
    {
        $request->validate([
            'lead_product_list_id' => ['nullable', 'integer', 'exists:lead_product_lists,id'],
            'lead_stage_id' => ['nullable', 'integer', 'exists:lead_stages,id'],
            'lead_stage_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
        ]);

        $context = $this->resolveAppointmentStageContext($request);

        $stages = LeadStage::query()
            ->active()
            ->with('activeSubStages')
            ->ordered()
            ->get();

        $selectedStage = $context['lead_stage_id']
            ? $stages->firstWhere('id', (int) $context['lead_stage_id'])
            : null;

        return response()->json([
            'success' => true,
            'context' => $context,
            'stage_options' => $stages->map(fn(LeadStage $stage) => $this->leadStageOptionPayload($stage))->values(),
            'sub_stage_options' => $selectedStage
                ? $this->leadStageOptionPayload($selectedStage)['sub_stages']
                : [],
        ]);
    }

    protected function syncAppointmentPersonalTask(
        MainAppointment $appointment,
        Request $request,
        string $finalName,
        array $employeeIds,
        array $stageContext
    ): ?PersonalTask {
        if (!$request->filled('reminder_date') || !$request->filled('next_step') || empty($request->input('report_responsible'))) {
            return null;
        }

        // report_responsible = Verantwortliche des Follow-ups (sehen es auf /home).
        $responsibles = collect((array) $request->input('report_responsible', []))
            ->filter(fn($value) => filled($value))
            ->map(fn($value) => (int) $value)
            ->unique()
            ->values()
            ->all();

        // F4 (Termin-Konvergenz, Ansatz A): EIN Follow-up ueber den geteilten Service statt der
        // Legacy-Erzeugung (type='personal_task' + PersonalTaskKey + Attendee-Pivot). UPSERT by
        // (source_type='main_appointment', source_id=appointment.id) -> Termin-Update erzeugt KEIN
        // zweites Follow-up, sondern aktualisiert. type='follow_up', follow_up_art=NULL (kein
        // Outcome-Dialog beim Termin-Sync). Mapping 1:1: next_step->task_title, reminder_date->
        // due_date, report_responsible->Verantwortliche (Pivot). Legacy-Kosmetik (color/priority/
        // start_date/reminder_time/sub-stage/PersonalTaskKey) entfaellt - Follow-up != Termin-Kachel.
        $task = app(\App\Services\FollowUp\FollowUpCreator::class)->sync(
            'main_appointment',
            (int) $appointment->id,
            [
                'customer_id'          => $appointment->customer_id ?: $request->input('customer_id'),
                'lead_product_list_id' => $stageContext['lead_product_list_id'] ?? null,
                'description'          => 'Aus Termin: ' . $finalName,
            ],
            [
                'follow_up_art' => null,
                'next_step'     => $request->input('next_step'),
                'due_date'      => $request->input('reminder_date'),
                'responsible'   => $responsibles,
            ],
            (int) auth()->user()->name
        );

        // Bestehende appointment.task_id-Verankerung beibehalten.
        if ((int) $appointment->task_id !== (int) $task->id) {
            $appointment->forceFill(['task_id' => $task->id])->save();
        }

        return $task;
    }



/**
 * Sync inquiry_product_lists from the request.
 *
 * Priority:
 *   1. If "inquiries" array is present, use that.
 *   2. Otherwise, fall back to products JSON and article_group mapping.
 */
protected function syncInquiryProductsFromRequest(
    Inquiry $inquiry,
    Request $request,
    array $appointmentEmployeeIds,
    ?string $anchorDate,
    ?string $anchorTime
): void {
    $rows = $request->input('inquiries', []);

    // always clear old rows first
    InquiryProductList::where('inquiry_id', $inquiry->id)->delete();

    if (is_array($rows) && count($rows)) {
        $appointmentDateTime = null;
        if ($anchorDate) {
            $time = $anchorTime ?: '08:00';
            $appointmentDateTime = Carbon::parse($anchorDate . ' ' . $time);
        }

        foreach ($rows as $row) {
            InquiryProductList::create([
                'inquiry_id'       => $inquiry->id,
                'department_id'    => $row['department_id'] ?? null,
                'employee_id'      => $row['employee_id'] ?? null,
                'field_employee'   => $row['field_employee_id'] ?? null,
                'product_id'       => $row['product_id'] ?? null,
                'service_id'       => $row['service_id'] ?? null,
                'status'           => 'open',
                'appointment_date' => $appointmentDateTime,
            ]);
        }

        Log::info('Inquiry products synced from inquiries[]', [
            'inquiry_id' => $inquiry->id,
            'rows'       => $rows,
        ]);

        return;
    }

    // fallback: use products JSON as before
    $this->syncInquiryProductsFromAppointment(
        $inquiry,
        $request->products,
        $appointmentEmployeeIds,
        $anchorDate,
        $anchorTime
    );
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



    public function datasets() {
        // Keep it light: id + localized_name fields
        return response()->json([
            'departments' => Department::select('id','department_name as localized_name')->orderBy('department_name')->get(),
            'products'    => ArticleGroup::select('id','article_group as localized_name')->orderBy('article_group')->get(),
            'services'    => Service::select('id', 'name as localized_name')->orderBy('name')->get(),
        ]);
    }


 
public function reports(Request $request)
{
    $customerId    = $request->integer('customer_id');
    $alternativeId = $request->integer('alternative_id'); // optional, not used in filter
    $productId     = $request->integer('product_id');     // optional, not used in filter

    if (!$customerId) {
        return response()->json([
            'html' => '<div class="text-muted small p-2">Kein Kunde gewählt.</div>',
        ]);
    }

    // 1) Alle Termine dieses Kunden
    $appointments = MainAppointment::query()
        ->where('customer_id', $customerId)
        ->orderByDesc('start_date')
        ->get();

    if ($appointments->isEmpty()) {
        return response()->json([
            'html' => '<div class="text-muted small p-2">Keine Termine für diesen Kunden.</div>',
        ]);
    }

    // 2) Alle Reports zu diesen Terminen (über customer_id in der Relation appointment)
    $reports = AppointmentReport::query()
        ->with(['author', 'appointment'])
        ->whereHas('appointment', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })
        ->orderByDesc('report_date')
        ->orderByDesc('created_at')
        ->get();

    // 3) Mitarbeiter aus meta["items"] sammeln
    $commentEmployeeIds = [];

    foreach ($reports as $report) {
        $meta = $report->meta;

        if (!is_array($meta)) {
            continue;
        }

        $items = $meta['items'] ?? [];

        if (!is_array($items)) {
            continue;
        }

        foreach ($items as $item) {
            if (!empty($item['employee_id'])) {
                $commentEmployeeIds[] = (int) $item['employee_id'];
            }
        }
    }

    $commentEmployeeIds = array_unique($commentEmployeeIds);

    $commentEmployees = !empty($commentEmployeeIds)
        ? Employee::whereIn('id', $commentEmployeeIds)->get()->keyBy('id')
        : collect();

    // Aktueller Mitarbeiter (versuche zuerst Employee-Relation, sonst User selbst)
    $user     = auth()->user();
    $employee = $user->employee ?? $user; // passe das an deine Struktur an
    $currentEmployeeId = $employee->id ?? null;

    // 4) View rendern
    $html = view('admin.kanban.partials.report', [
        'appointments'      => $appointments,
        'reports'           => $reports,
        'commentEmployees'  => $commentEmployees,
        'currentEmployeeId' => $currentEmployeeId,
        'customerId'        => $customerId,
        'alternativeId'     => $alternativeId,
        'productId'         => $productId,
    ])->render();

    return response()->json(['html' => $html]);
}

public function reactReport(Request $request, AppointmentReport $report)
{
    $user     = auth()->user();
    $employee = $user->employee ?? null; // ggf. anpassen

    if (!$employee) {
        return response()->json([
            'message' => 'Kein Mitarbeiter gefunden.',
        ], 422);
    }

    $data = $request->validate([
        'reaction' => 'required|in:like,dislike,none',
    ]);

    $meta = $report->meta;
    if (!is_array($meta)) {
        $meta = [];
    }

    $likes    = isset($meta['likes'])    && is_array($meta['likes'])    ? $meta['likes']    : [];
    $dislikes = isset($meta['dislikes']) && is_array($meta['dislikes']) ? $meta['dislikes'] : [];

    $employeeId = (int) $employee->id;

    // vorhandene Reaktion entfernen
    $likes    = array_values(array_diff($likes,    [$employeeId]));
    $dislikes = array_values(array_diff($dislikes, [$employeeId]));

    // neue Reaktion setzen
    if ($data['reaction'] === 'like') {
        $likes[] = $employeeId;
    } elseif ($data['reaction'] === 'dislike') {
        $dislikes[] = $employeeId;
    }

    $meta['likes']    = array_values(array_unique($likes));
    $meta['dislikes'] = array_values(array_unique($dislikes));

    $report->meta = $meta;
    $report->save();

    return response()->json([
        'likes'       => count($meta['likes']),
        'dislikes'    => count($meta['dislikes']),
        'my_reaction' => $report->reactionOf($employeeId), // Methode im Model anpassen
    ]);
}


public function commentReport(Request $request, AppointmentReport $report)
{
    $user     = auth()->user();
    $employee = $user->name; // ggf. anpassen

    if (!$employee || !$employee->id) {
        return response()->json(['message' => 'Kein Mitarbeiter gefunden.'], 422);
    }

    $data = $request->validate([
        'comment' => 'required|string|max:2000',
    ]);

    $meta = $report->meta;
    if (!is_array($meta)) {
        $meta = [];
    }

    $items = $meta['items'] ?? [];
    $items = is_array($items) ? $items : [];

    $newItem = [
        'employee_id' => $employee->id,
        'text'        => $data['comment'],
        'created_at'  => now()->toIso8601String(),
    ];

    $items[]        = $newItem;
    $meta['items']  = $items;

    // wichtig: in meta speichern, nicht comments
    $report->meta = $meta;
    $report->save();

    // einzelne Kommentar-Zeile rendern
    $html = view('admin.kanban.partials._report_comment', [
        'item'     => $newItem,
        'employee' => $employee,
    ])->render();

    return response()->json([
        'html' => $html,
    ]);
}

 


    public function show(MainAppointment $appointment)
    {
        $appointment->load([
            'customer',
            'employees',
            'branch',
            'branchAddress',
            'createdBy',
        ]);

        $allEmployees = Employee::where('status', 'Active')
            ->orderBy('name')
            ->get(['id', 'name', 'lastname', 'image']);

        // Time badge (start date + time)
        $startDateTime = null;

        if ($appointment->start_date) {
            if ($appointment->start_date instanceof Carbon) {
                $datePart = $appointment->start_date->toDateString();
            } else {
                $datePart = substr((string) $appointment->start_date, 0, 10);
            }

            $timePart = $appointment->start_time ?? '00:00:00';

            $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $datePart . ' ' . $timePart);
        }

        $timeBadgeLabel = null;
        if ($startDateTime) {
            $diff = $startDateTime->diffForHumans(null, [
                'parts'  => 2,
                'short'  => true,
                'syntax' => Carbon::DIFF_ABSOLUTE,
            ]);

            if ($startDateTime->isFuture()) {
                $timeBadgeLabel = 'Start in ' . $diff;
            } else {
                $timeBadgeLabel = 'Vor ' . $diff;
            }
        }

        // Reports for this appointment
        $reports = AppointmentReport::with('employee:id,name,lastname,image')
            ->where('appointment_id', $appointment->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Plain appointment comments (separate from report-internal comments)
        $comments = AppointmentComment::with('employee:id,name,lastname,image')
            ->where('appointment_id', $appointment->id)
            ->orderByDesc('created_at')
            ->get();

        // Notifications for this appointment + current user
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
                        'id'         => $n->id,
                        'title'      => $d['title'] ?? 'Terminbenachrichtigung',
                        'message'    => $d['message'] ?? '',
                        'kind'       => $d['kind'] ?? 'generic',
                        'created_at' => $n->created_at->diffForHumans(),
                    ];
                });
        }

        return view('admin.appointments.show', [
            'appointment'       => $appointment,
            'allEmployees'      => $allEmployees,
            'reports'           => $reports,
            'comments'          => $comments,
            'timeBadgeLabel'    => $timeBadgeLabel,
            'notificationItems' => $notificationItems,
        ]);
    }

    /**
     * UPDATE STATUS (from profile page)
     */
    public function updateStatus(Request $request, MainAppointment $appointment)
    {
        $data = $request->validate([
            'status' => 'required|string|max:50',
        ]);

        $appointment->status     = $data['status'];
        $appointment->changed_by = Auth::user()->employee_id ?? null;
        $appointment->change_date = now();
        $appointment->save();

        return response()->json([
            'success' => true,
            'status'  => $appointment->status,
        ]);
    }

    /**
     * ADD EMPLOYEE TO APPOINTMENT (profile page)
     */
    public function addEmployee(Request $request, MainAppointment $appointment)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employeeId = (int) $validated['employee_id'];

        // attach if not already attached
        $appointment->employees()->syncWithoutDetaching([
            $employeeId => ['status' => 'send', 'reason' => null],
        ]);

        $employee = Employee::select('id', 'name', 'lastname', 'image')
            ->findOrFail($employeeId);

        return response()->json([
            'success'  => true,
            'employee' => [
                'id'       => $employee->id,
                'name'     => $employee->name,
                'lastname' => $employee->lastname,
                'image'    => $employee->image,
            ],
        ]);
    }

    /**
     * REMOVE EMPLOYEE FROM APPOINTMENT (profile page)
     */
    public function removeEmployee(MainAppointment $appointment, Employee $employee)
    {
        $appointment->employees()->detach($employee->id);

        return response()->json([
            'success'      => true,
            'employee_id'  => $employee->id,
        ]);
    }

    /**
     * STORE REPORT (with next_step + due_date, Quill content)
     * Route: POST customer/appointments/{appointment}/reports
     */
    public function storeReport(Request $request, MainAppointment $appointment)
    {
        $validated = $request->validate([
            'report'      => 'required|string',
            'report_date' => 'nullable|date',
            'next_step'   => 'nullable|string',
            'due_date'    => 'nullable|date',
        ]);

        $user       = Auth::user();
        $employeeId = $user->name;

        $report = new AppointmentReport();
        $report->appointment_id = $appointment->id;
        $report->employee_id    = $employeeId;
        $report->report_by      = $employeeId;
        $report->type           = 'appointment';

        // Quill HTML or plain text – we store as-is
        $report->report      = $validated['report'];
        $report->report_date = $validated['report_date'] ?? now()->toDateString();

        $report->next_step = $validated['next_step'] ?? null;
        $report->due_date  = $validated['due_date'] ?? null;

        // initialize JSON structures
        $report->comments = ['items' => []];
        $report->meta     = [
            'items'    => [],
            'likes'    => [],
            'dislikes' => [],
        ];

        $report->save();
        $report->load('employee:id,name,lastname,image');

        return response()->json([
            'success' => true,
            'report'  => [
                'id'          => $report->id,
                'report'      => $report->report,
                'report_date' => optional($report->report_date)->format('Y-m-d'),
                'next_step'   => $report->next_step,
                'due_date'    => optional($report->due_date)->format('Y-m-d'),
                'created_at'  => $report->created_at->diffForHumans(),
                'employee'    => [
                    'id'       => $report->employeeId,
                    'name'     => $report->employee->name,
                    'lastname' => $report->employee->lastname,
                    'image'    => $report->employee->image,
                ],
                'likes_count'    => $report->likes_count,
                'dislikes_count' => $report->dislikes_count,
                'reaction'       => $report->reactionOf($employeeId),
            ],
        ]);
    }

    /**
     * STORE GLOBAL APPOINTMENT COMMENT
     * Route: POST customer/appointments/{appointment}/comments
     */
    public function storeComment(Request $request, MainAppointment $appointment)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:10000',
        ]);

        $user       = Auth::user();
        $employeeId = $user->name ?? $user->id;

        $comment = new AppointmentComment();
        $comment->appointment_id = $appointment->id;
        $comment->comment_by     = $employeeId;
        $comment->comment        = $validated['comment'];
        $comment->status         = 'Published';
        $comment->save();

        $comment->load('employee:id,name,lastname,image');

        return response()->json([
            'success' => true,
            'comment' => [
                'id'        => $comment->id,
                'comment'   => $comment->comment,
                'created_at'=> $comment->created_at->diffForHumans(),
                'employee'  => [
                    'id'       => $comment->employee->id,
                    'name'     => $comment->employee->name,
                    'lastname' => $comment->employee->lastname,
                    'image'    => $comment->employee->image,
                ],
            ],
        ]);
    }

    /**
     * APPOINTMENT-SPECIFIC NOTIFICATIONS (timeline on profile)
     * Route: GET customer/appointments/{appointment}/notifications
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
                    'id'         => $n->id,
                    'title'      => $d['title'] ?? 'Terminbenachrichtigung',
                    'message'    => $d['message'] ?? '',
                    'kind'       => $d['kind'] ?? 'generic',
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'data' => $items,
        ]);
    }

    /**
     * OPTIONAL: REACT TO A REPORT (LIKE / DISLIKE)
     * Example route:
     *   POST customer/appointments/{appointment}/reports/{report}/react
     *   body: { "reaction": "like"|"dislike" }
     */
    public function reactToReport(
        Request $request,
        MainAppointment $appointment,
        AppointmentReport $report
    ) {
        $validated = $request->validate([
            'reaction' => 'required|in:like,dislike',
        ]);

        $user       = Auth::user();
        $employeeId = $user->employee_id ?? $user->id;

        if ($validated['reaction'] === 'like') {
            $report->toggleLike($employeeId);
        } else {
            $report->toggleDislike($employeeId);
        }

        return response()->json([
            'success'        => true,
            'likes_count'    => $report->likes_count,
            'dislikes_count' => $report->dislikes_count,
            'reaction'       => $report->reactionOf($employeeId),
        ]);
    }

    /**
     * OPTIONAL: ADD COMMENT ITEM TO A REPORT (JSON comments field)
     * Example route:
     *   POST customer/appointments/{appointment}/reports/{report}/comment-item
     *   body: { "text": "..." }
     */
    public function addReportCommentItem(
        Request $request,
        MainAppointment $appointment,
        AppointmentReport $report
    ) {
        $validated = $request->validate([
            'text' => 'required|string|max:10000',
        ]);

        $user       = Auth::user();
        $employeeId = $user->employee_id ?? $user->id;

        $report->addCommentItem($employeeId, $validated['text']);

        return response()->json([
            'success'      => true,
            'commentItems' => $report->comment_items,
        ]);
    }

 
    public function restoreDeletedAppointment($id)
    {
        $appointment = MainAppointment::query()
            ->when(method_exists(MainAppointment::class, 'bootSoftDeletes'), fn($q) => $q->withTrashed())
            ->findOrFail($id);

        if (Schema::hasColumn($appointment->getTable(), 'deleted_at') && method_exists($appointment, 'restore')) {
            $appointment->restore();
        }

        if (Schema::hasColumn($appointment->getTable(), 'is_deleted')) {
            $appointment->is_deleted = 0;
        }

        if (Schema::hasColumn($appointment->getTable(), 'deleted_by')) {
            $appointment->deleted_by = null;
        }

        if (Schema::hasColumn($appointment->getTable(), 'deleted_reason')) {
            $appointment->deleted_reason = null;
        }

        $appointment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Der Termin wurde wiederhergestellt.',
        ]);
    }

    public function forceDeleteAppointment($id)
    {
        $appointment = MainAppointment::query()
            ->when(method_exists(MainAppointment::class, 'bootSoftDeletes'), fn($q) => $q->withTrashed())
            ->findOrFail($id);

        if (method_exists($appointment, 'forceDelete')) {
            $appointment->forceDelete();
        } else {
            $appointment->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Der Termin wurde endgültig gelöscht.',
        ]);
    }

}
