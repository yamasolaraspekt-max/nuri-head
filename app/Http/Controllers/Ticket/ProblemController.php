<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\Employee;
use App\Models\EmployeeRecurringLeave;
use App\Models\EmployeeSick;
use App\Models\Error;
use App\Models\Image;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
use App\Models\Leave;
use App\Models\MainAppointment;
use App\Models\MainAppointmentEmployee;
use App\Models\NewLeads;
use App\Models\Problem;
use App\Models\ProblemImage;
use App\Models\Product;
use App\Models\TicketTask;
use App\Models\User;
use App\Notifications\TicketNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProblemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
    |--------------------------------------------------------------------------
    | Board / index
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $employeeId = (int) auth()->user()->name;

        $baseAll = DB::table('problems')->whereNull('deleted_at');

        $minePivotOnly = function ($q) use ($employeeId) {
            $q->whereExists(function ($sub) use ($employeeId) {
                $sub->select(DB::raw(1))
                    ->from('employee_problem')
                    ->whereColumn('employee_problem.problem_id', 'problems.id')
                    ->where('employee_problem.employee_id', $employeeId);
            });
        };

        $createdByMeOnly = function ($q) use ($employeeId) {
            $q->where('problems.first_contact', $employeeId);
        };

        $counts = [
            'mine' => (clone $baseAll)->where(fn($q) => $minePivotOnly($q))->count(),
            'created' => (clone $baseAll)->where(fn($q) => $createdByMeOnly($q))->count(),
            'progress' => (clone $baseAll)
                ->where('status', 'process')
                ->where(function ($q) use ($minePivotOnly, $createdByMeOnly) {
                    $q->where(fn($x) => $minePivotOnly($x))
                        ->orWhere(fn($x) => $createdByMeOnly($x));
                })
                ->count(),
            'completed' => (clone $baseAll)
                ->whereIn('status', $this->archiveStatuses())
                ->where(function ($q) use ($minePivotOnly, $createdByMeOnly) {
                    $q->where(fn($x) => $minePivotOnly($x))
                        ->orWhere(fn($x) => $createdByMeOnly($x));
                })
                ->count(),
            'junk' => (clone $baseAll)
                ->where('status', 'junk')
                ->where(function ($q) use ($minePivotOnly, $createdByMeOnly) {
                    $q->where(fn($x) => $minePivotOnly($x))
                        ->orWhere(fn($x) => $createdByMeOnly($x));
                })
                ->count(),
            'all' => (clone $baseAll)->count(),
        ];

        $viewMode = $request->get('view', 'list');

        return view('admin.problem.problem_board', [
            'counts' => $counts,
            'viewMode' => $viewMode,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $data = [];

        $data['customer'] = DB::table('new_leads')
            ->join('lead_product_lists', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->select('new_leads.*')
            ->whereNull('new_leads.deleted_at')
            ->distinct()
            ->get();

        $data['product'] = ArticleGroup::query()->orderBy('article_group')->get();

        $data['responsible'] = Employee::query()
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['employee_id'] = auth()->user()->name;

        $data['error_code'] = DB::table('errors')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'errors.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'errors.brand_id')
            ->select(
                'errors.id',
                'errors.article_name',
                'errors.error_code',
                'brands.name as brand_name',
                'article_groups.article_group',
                'errors.problem_types',
                'errors.reason',
                'errors.solution'
            )
            ->get();

        [$leadStages, $leadStageOptions] = $this->leadStageFormOptions();

        $data['leadStages'] = $leadStages;
        $data['leadStageOptions'] = $leadStageOptions;

        return view('admin.problem.problem', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $this->normalizeTicketStageInputs($request);

        $validator = Validator::make($request->all(), [
            'error_code' => ['required', 'array'],
            'error_code.*' => ['nullable', 'integer', 'exists:errors,id'],
            'source' => ['required', 'string', 'max:255'],
            'error_type' => ['required', 'string', 'max:255'],

            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'product_id' => ['required', 'integer', 'exists:article_groups,id'],
            'alternative_id' => ['nullable', 'integer', 'exists:lead_alternative_adds,id'],

            'lead_product_list_id' => ['nullable', 'integer', 'exists:lead_product_lists,id'],
            'lead_stage_id' => ['nullable', 'integer', 'exists:lead_stages,id'],
            'lead_stage_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
            'nuriva' => ['nullable', 'boolean'],

            'responsible' => ['required', 'array', 'min:1'],
            'responsible.*' => ['required', 'integer', 'exists:employees,id'],
            'first_contact' => ['required', 'integer', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'priority' => ['nullable', 'string', 'max:255'],
            'repeated' => ['nullable'],
            'editor_text' => ['required', 'string'],

            'article_name' => ['nullable', 'string', 'max:255'],
            'article_sn' => ['nullable', 'string', 'max:255'],
            'installation_date' => ['nullable', 'date'],
            'warranty_type' => ['nullable', 'string', 'max:255'],
            'warranty_duration' => ['nullable', 'string', 'max:255'],
            'warranty_remaining' => ['nullable', 'integer'],
            'finance_to' => ['nullable', 'string', 'max:255'],

            'create_appointment' => ['nullable', 'boolean'],
            'appointment_date' => ['nullable', 'date'],
            'appointment_start_time' => ['nullable', 'date_format:H:i'],
            'appointment_end_time' => ['nullable', 'date_format:H:i'],
            'appointment_duration_minutes' => ['nullable', 'integer', 'min:15', 'max:480'],
            'appointment_force' => ['nullable', 'boolean'],
        ], [
            'customer_id.required' => 'Customer Name: Dieses Feld muss ausgefüllt werden',
            'product_id.required' => 'Product: Dieses Feld muss ausgefüllt werden',
            'date.required' => 'Date: Dieses Feld muss ausgefüllt werden',
            'responsible.required' => 'Verantwortlich: Dieses Feld muss ausgefüllt werden',
            'first_contact.required' => 'Erster Kontakt: Dieses Feld muss ausgefüllt werden',
            'editor_text.required' => 'Bitte geben Sie das Problem an',
            'lead_product_list_id.integer' => 'Lead Produkt muss eine gültige Zahl sein.',
            'lead_stage_id.integer' => 'Lead Stage muss eine gültige Zahl sein.',
            'lead_stage_sub_stage_id.integer' => 'Sub Stage muss eine gültige Zahl sein.',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($request, $validator);
        }

        try {
            DB::beginTransaction();

            $responsibleIds = $this->cleanIntegerArray($request->input('responsible', []));
            $mainResponsible = $responsibleIds[0] ?? null;
            $stageContext = $this->resolveTicketStageContext($request);
            $errorIds = $this->cleanIntegerArray($request->input('error_code', []));

            /*
            |--------------------------------------------------------------------------
            | Appointment availability check
            |--------------------------------------------------------------------------
            | Important:
            | We only check availability to show warning/message.
            | We DO NOT block ticket saving anymore if the calendar is full.
            |--------------------------------------------------------------------------
            */
            $appointmentOverbooked = false;
            $appointmentAvailabilityMessage = null;

            if ($request->boolean('create_appointment')) {
                try {
                    $availability = $this->buildTicketAppointmentAvailability(
                        $responsibleIds,
                        $request->input('appointment_date') ?: $request->input('date'),
                        $request->input('appointment_start_time') ?: '09:00',
                        $request->input('appointment_end_time') ?: '10:00',
                        (int) ($request->input('appointment_duration_minutes') ?: 60)
                    );

                    if (!($availability['available'] ?? true)) {
                        $appointmentOverbooked = true;
                        $appointmentAvailabilityMessage = $availability['message']
                            ?? 'Termin wurde trotz voller Kalender gespeichert.';
                    }
                } catch (\Throwable $availabilityException) {
                    /*
                    |--------------------------------------------------------------------------
                    | Do not block ticket creation if availability check fails
                    |--------------------------------------------------------------------------
                    */
                    Log::warning('Ticket appointment availability check failed, saving anyway.', [
                        'message' => $availabilityException->getMessage(),
                        'responsible_ids' => $responsibleIds,
                        'appointment_date' => $request->input('appointment_date') ?: $request->input('date'),
                        'appointment_start_time' => $request->input('appointment_start_time') ?: '09:00',
                        'appointment_end_time' => $request->input('appointment_end_time') ?: '10:00',
                    ]);
                }
            }

            $problem = Problem::create([
                'planner_id' => $request->input('planner_id'),
                'ticket_no' => $this->generateTicketNo(),
                'error_code' => implode(',', $errorIds),
                'error_type' => $request->input('error_type'),
                'customer_id' => $request->integer('customer_id'),
                'alternative_id' => $request->integer('alternative_id') ?: null,
                'product_id' => $request->integer('product_id'),

                'lead_product_list_id' => $stageContext['lead_product_list_id'],
                'lead_stage_id' => $stageContext['lead_stage_id'],
                'lead_stage_sub_stage_id' => $stageContext['lead_stage_sub_stage_id'],

                'start_user' => auth()->user()->name,
                'responsible' => $mainResponsible,
                'first_contact' => $request->integer('first_contact'),
                'date' => $request->input('date'),
                'problem' => $request->input('editor_text'),
                'status' => 'offen',
                'repeated' => $request->boolean('repeated') ? 1 : 0,
                'source' => $request->input('source'),

                'article_name' => $request->input('article_name'),
                'article_sn' => $request->input('article_sn'),
                'installation_date' => $request->input('installation_date'),
                'warranty_type' => $request->input('warranty_type'),
                'warranty_duration' => $request->input('warranty_duration'),
                'warranty_remaining' => $request->input('warranty_remaining'),
                'finance_to' => $request->input('finance_to'),
                'priority' => $request->input('priority', 'normal'),
            ]);

            if (method_exists($problem, 'error')) {
                $problem->error()->sync($errorIds);
            } elseif (method_exists($problem, 'errors')) {
                $problem->errors()->sync($errorIds);
            }

            if (method_exists($problem, 'employee')) {
                $problem->employee()->sync($responsibleIds);
            } elseif (method_exists($problem, 'employees')) {
                $problem->employees()->sync($responsibleIds);
            }

            if ($request->boolean('create_appointment')) {
                /*
                |--------------------------------------------------------------------------
                | Always create appointment
                |--------------------------------------------------------------------------
                | Even if calendar is overbooked/full.
                |--------------------------------------------------------------------------
                */
                $this->createAppointmentFromProblem($problem, $request, $responsibleIds);
            }

            DB::commit();

            $successMessage = $appointmentOverbooked
                ? 'Ticket wurde erfolgreich erstellt. Termin wurde trotz voller Kalender gespeichert.'
                : 'Ticket wurde erfolgreich erstellt.';

            if ($appointmentOverbooked && $appointmentAvailabilityMessage) {
                $successMessage .= ' Hinweis: ' . $appointmentAvailabilityMessage;
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'appointment_overbooked' => $appointmentOverbooked,
                    'problem_id' => $problem->id,
                    'profile_url' => route('problem.profile', $problem->id),
                ]);
            }

            return redirect()
                ->route('problem.profile', $problem->id)
                ->with('success', $successMessage);
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ProblemController@store failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'payload' => $request->all(),
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket konnte nicht erstellt werden.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Ticket konnte nicht erstellt werden: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $problem = Problem::with(['customer', 'alternative', 'product', 'employees', 'errors', 'leadStage', 'leadStageSubStage'])
            ->findOrFail($id);

        $data = $this->editCreateData($problem);

        return view('admin.problem.problem_edit', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $this->normalizeTicketStageInputs($request);

        $validator = Validator::make($request->all(), [
            'error_code' => ['nullable', 'array'],
            'error_code.*' => ['nullable', 'integer', 'exists:errors,id'],
            'source' => ['nullable', 'string', 'max:255'],
            'error_type' => ['required', 'string', 'max:255'],

            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'product_id' => ['required', 'integer', 'exists:article_groups,id'],
            'alternative_id' => ['nullable', 'integer', 'exists:lead_alternative_adds,id'],

            'lead_product_list_id' => ['nullable', 'integer', 'exists:lead_product_lists,id'],
            'lead_stage_id' => ['nullable', 'integer', 'exists:lead_stages,id'],
            'lead_stage_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
            'nuriva' => ['nullable', 'boolean'],

            'responsible' => ['required', 'array', 'min:1'],
            'responsible.*' => ['required', 'integer', 'exists:employees,id'],
            'first_contact' => ['required', 'integer', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'priority' => ['nullable', 'string', 'max:255'],
            'repeated' => ['nullable'],
            'editor_text' => ['required', 'string'],

            'article_name' => ['nullable', 'string', 'max:255'],
            'article_sn' => ['nullable', 'string', 'max:255'],
            'installation_date' => ['nullable', 'date'],
            'warranty_type' => ['nullable', 'string', 'max:255'],
            'warranty_duration' => ['nullable', 'string', 'max:255'],
            'warranty_remaining' => ['nullable', 'integer'],
            'finance_to' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
        ], [
            'customer_id.required' => 'Customer Name: Dieses Feld muss ausgefüllt werden',
            'product_id.required' => 'Product: Dieses Feld muss ausgefüllt werden',
            'date.required' => 'Date: Dieses Feld muss ausgefüllt werden',
            'responsible.required' => 'Verantwortlich: Dieses Feld muss ausgefüllt werden',
            'first_contact.required' => 'Erster Kontakt: Dieses Feld muss ausgefüllt werden',
            'editor_text.required' => 'Bitte geben Sie das Problem an',
        ]);

        if ($validator->fails()) {
            return $this->validationResponse($request, $validator);
        }

        try {
            DB::beginTransaction();

            $problem = Problem::findOrFail($id);
            $responsibleIds = $this->cleanIntegerArray($request->input('responsible', []));
            $errorIds = $this->cleanIntegerArray($request->input('error_code', []));
            $stageContext = $this->resolveTicketStageContext($request);

            $problem->ticket_no = $problem->ticket_no ?: $this->generateTicketNo();
            $problem->error_code = implode(',', $errorIds);
            $problem->customer_id = $request->integer('customer_id');
            $problem->product_id = $request->integer('product_id');
            $problem->alternative_id = $request->integer('alternative_id') ?: null;

            $problem->lead_product_list_id = $stageContext['lead_product_list_id'];
            $problem->lead_stage_id = $stageContext['lead_stage_id'];
            $problem->lead_stage_sub_stage_id = $stageContext['lead_stage_sub_stage_id'];

            $problem->date = $request->input('date');
            $problem->first_contact = $request->integer('first_contact');
            $problem->responsible = $responsibleIds[0] ?? null;
            $problem->problem = $request->input('editor_text');
            $problem->start_user = $request->input('start_user', $problem->start_user);
            $problem->status = $request->input('status', $problem->status ?: 'offen');
            $problem->error_type = $request->input('error_type');
            $problem->source = $request->input('source');
            $problem->repeated = $request->boolean('repeated') ? 1 : 0;
            $problem->article_name = $request->input('article_name');
            $problem->article_sn = $request->input('article_sn');
            $problem->installation_date = $request->input('installation_date');
            $problem->warranty_type = $request->input('warranty_type');
            $problem->warranty_duration = $request->input('warranty_duration');
            $problem->warranty_remaining = $request->input('warranty_remaining');
            $problem->finance_to = $request->input('finance_to');
            $problem->priority = $request->input('priority');
            $problem->edit_user = auth()->user()->name;
            $problem->edit_date = now()->toDateString();
            $problem->save();

            if (method_exists($problem, 'error')) {
                $problem->error()->sync($errorIds);
            } elseif (method_exists($problem, 'errors')) {
                $problem->errors()->sync($errorIds);
            }

            if (method_exists($problem, 'employee')) {
                $problem->employee()->sync($responsibleIds);
            } elseif (method_exists($problem, 'employees')) {
                $problem->employees()->sync($responsibleIds);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ticket aktualisiert!',
                'problem_id' => $problem->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ProblemController@update failed', [
                'id' => $id,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ticket konnte nicht aktualisiert werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $data = Problem::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wird gelöscht');
    }

    /*
    |--------------------------------------------------------------------------
    | Photo/image endpoints
    |--------------------------------------------------------------------------
    */
    public function image(Request $request)
    {
        $request->validate([
            'problem_id' => ['nullable', 'integer', 'exists:problems,id'],
            'ticket_id' => ['nullable', 'integer', 'exists:problems,id'],
            'image' => ['nullable'],
            'file' => ['nullable'],
        ]);

        $problemId = $request->integer('problem_id') ?: $request->integer('ticket_id');

        if (!$problemId) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket-ID fehlt.',
            ], 422);
        }

        $paths = [];

        foreach (['image', 'file', 'document'] as $field) {
            if ($request->hasFile($field)) {
                foreach ((array) $request->file($field) as $file) {
                    if (!$file) {
                        continue;
                    }

                    $paths[] = $file->store('ticket-files', 'public');
                }
            }
        }

        if (empty($paths) && $request->filled('image')) {
            $paths[] = $request->input('image');
        }

        $saved = [];
        foreach ($paths as $path) {
            if (class_exists(ProblemImage::class)) {
                $saved[] = ProblemImage::create([
                    'problem_id' => $problemId,
                    'image' => $path,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'files' => $saved,
        ]);
    }

    public function photos($id)
    {
        $data['data'] = DB::table('problems')
            ->leftJoin('new_leads', 'new_leads.id', '=', 'problems.customer_id')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'problems.product_id')
            ->leftJoin('employees as first', 'first.id', '=', 'problems.first_contact')
            ->where('problems.id', '=', $id)
            ->select(
                'problems.*',
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.city',
                'article_groups.article_group as product',
                'new_leads.id as cid',
                'first.name as fname',
                'first.lastname as flastname'
            )
            ->get();

        $data['error'] = DB::table('error_problem')
            ->join('errors', 'errors.id', '=', 'error_problem.error_id')
            ->join('problems', 'problems.id', '=', 'error_problem.problem_id')
            ->where('problems.id', $id)
            ->select('error_problem.*', 'errors.problem_types', 'problems.id')
            ->get();

        $data['responsible'] = DB::table('employee_problem')
            ->join('employees', 'employees.id', '=', 'employee_problem.employee_id')
            ->join('problems', 'problems.id', '=', 'employee_problem.problem_id')
            ->where('problems.id', $id)
            ->select(
                'employee_problem.*',
                'employees.name as rname',
                'employees.lastname as rlastname',
                'employees.image as rimage',
                'problems.id as pid'
            )
            ->get();

        $data['images'] = DB::table('problem_images')
            ->where('problem_id', '=', $id)
            ->first();

        return view('admin.problem.problem_image', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | Ticket lifecycle
    |--------------------------------------------------------------------------
    */
    public function open($id)
    {
        return $this->setTicketStatus($id, 'offen');
    }

    public function progress($id)
    {
        return $this->setTicketStatus($id, 'process');
    }

    public function close($id)
    {
        $problem = Problem::findOrFail($id);

        return view('admin.problem.problem_close', [
            'problem' => $problem,
        ]);
    }

    public function closeSave(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:problems,id',
            'end_date' => 'required|date',
            'editor_text' => 'required|string',
        ]);

        $problem = Problem::findOrFail($request->id);
        $empId = (int) auth()->user()->name;

        $problem->status = 'end';
        $problem->end_date = $request->input('end_date');
        $problem->end_user = $empId;
        $problem->solution = $request->input('editor_text');
        $problem->total_time = $request->input('total_time', $problem->total_time);
        $problem->save();

        return response()->json([
            'success' => true,
            'message' => 'Ticket wurde geschlossen.',
            'problem_id' => $problem->id,
        ]);
    }

    protected function setTicketStatus(int $id, string $status): RedirectResponse
    {
        $problem = Problem::findOrFail($id);
        $empId = (int) auth()->user()->name;

        $problem->status = $this->normalizeTicketStatus($status);
        $problem->edit_user = $empId;
        $problem->edit_date = now()->toDateString();

        if ($problem->status === 'process') {
            $problem->progress_user = $empId;
            $problem->progress_date = now()->toDateString();
        }

        if ($problem->status === 'end') {
            $problem->end_user = $empId;
            $problem->end_date = now()->toDateString();
        }

        $problem->save();

        return redirect()->back()->with('success', 'Status aktualisiert.');
    }

    /*
    |--------------------------------------------------------------------------
    | Customer / product AJAX
    |--------------------------------------------------------------------------
    */
    public function getProduct($customer_id)
    {
        $data = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'lead_product_lists.alternative_id')
            ->leftJoin('lead_stages', 'lead_stages.key', '=', 'lead_product_lists.status')
            ->leftJoin('lead_stage_sub_stages', 'lead_stage_sub_stages.id', '=', 'lead_product_lists.lead_stage_sub_stage_id')
            ->select(
                'lead_product_lists.id as lp_id',
                'lead_product_lists.id as lead_product_list_id',
                'article_groups.article_group',
                'article_groups.id as product_id',
                'lead_product_lists.status as stage',
                'lead_product_lists.status',
                'lead_stages.id as lead_stage_id',
                'lead_stages.name as lead_stage_name',
                'lead_stages.color as lead_stage_color',
                'lead_product_lists.lead_stage_sub_stage_id',
                'lead_stage_sub_stages.name as lead_stage_sub_stage_name',
                'lead_stage_sub_stages.color as lead_stage_sub_stage_color',
                'alt.street',
                'alt.city',
                'alt.postcode',
                'alt.object_name',
                'alt.id as alternative_id'
            )
            ->where('new_leads.id', $customer_id)
            ->whereNull('lead_product_lists.deleted_at')
            ->get()
            ->map(function ($item) {
                $item->stage_label = $this->stageLabel($item->stage);
                return $item;
            });

        return response()->json($data, 200);
    }

    public function getCustomer($customer_id)
    {
        $selected = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'lead_product_lists.alternative_id')
            ->leftJoin('lead_stages', 'lead_stages.key', '=', 'lead_product_lists.status')
            ->leftJoin('lead_stage_sub_stages', 'lead_stage_sub_stages.id', '=', 'lead_product_lists.lead_stage_sub_stage_id')
            ->select(
                'new_leads.id as customer_id',
                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',
                'new_leads.customer_no',
                'alt.object_name',
                'alt.street',
                'alt.postcode',
                'alt.city',
                'alt.lat',
                'alt.lon',
                'lead_product_lists.id as lead_product_list_id',
                'lead_product_lists.product_id',
                'lead_product_lists.alternative_id',
                'lead_product_lists.service',
                'lead_product_lists.status as stage',
                'lead_stages.id as lead_stage_id',
                'lead_stages.name as lead_stage_name',
                'lead_stages.color as lead_stage_color',
                'lead_product_lists.lead_stage_sub_stage_id',
                'lead_stage_sub_stages.name as lead_stage_sub_stage_name',
                'lead_stage_sub_stages.color as lead_stage_sub_stage_color'
            )
            ->where('new_leads.id', $customer_id)
            ->first();

        $all = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'lead_product_lists.alternative_id')
            ->leftJoin('lead_stages', 'lead_stages.key', '=', 'lead_product_lists.status')
            ->leftJoin('lead_stage_sub_stages', 'lead_stage_sub_stages.id', '=', 'lead_product_lists.lead_stage_sub_stage_id')
            ->select(
                'new_leads.id as customer_id',
                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',
                'new_leads.customer_no',
                'alt.object_name',
                'alt.street',
                'alt.postcode',
                'alt.city',
                'alt.lat',
                'alt.lon',
                'lead_product_lists.id as lead_product_list_id',
                'lead_product_lists.product_id',
                'lead_product_lists.alternative_id',
                'lead_product_lists.service',
                'lead_product_lists.status as stage',
                'lead_stages.id as lead_stage_id',
                'lead_stages.name as lead_stage_name',
                'lead_stages.color as lead_stage_color',
                'lead_product_lists.lead_stage_sub_stage_id',
                'lead_stage_sub_stages.name as lead_stage_sub_stage_name',
                'lead_stage_sub_stages.color as lead_stage_sub_stage_color'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        return response()->json([
            'selected_customer' => $selected,
            'all_customers' => $all,
        ]);
    }

    public function getAllCustomer(Request $request)
    {
        $search = trim((string) ($request->get('q') ?: $request->get('term') ?: ''));

        $query = DB::table('new_leads')
            ->leftJoin('lead_product_lists', 'lead_product_lists.customer_id', '=', 'new_leads.id')
            ->leftJoin('lead_alternative_adds as alt', 'alt.id', '=', 'lead_product_lists.alternative_id')
            ->whereNull('new_leads.deleted_at')
            ->select(
                'new_leads.id',
                'new_leads.id as customer_id',
                'new_leads.customer_no',
                'new_leads.firma',
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.city',
                'new_leads.phone',
                'new_leads.email',
                'alt.city as object_city',
                'alt.street as object_street'
            )
            ->distinct();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('new_leads.name', 'like', "%{$search}%")
                    ->orWhere('new_leads.lastname', 'like', "%{$search}%")
                    ->orWhere('new_leads.firma', 'like', "%{$search}%")
                    ->orWhere('new_leads.customer_no', 'like', "%{$search}%")
                    ->orWhere('new_leads.email', 'like', "%{$search}%")
                    ->orWhere('new_leads.phone', 'like', "%{$search}%");
            });
        }

        $customers = $query
            ->orderByDesc('new_leads.id')
            ->limit(30)
            ->get();

        if ($customers->isEmpty()) {
            return response()->json(['status' => 'empty']);
        }

        return response()->json($customers);
    }

    public function getResponsible(Request $request)
    {
        $targetDate = $request->date ?: now()->format('Y-m-d');
        $search = $request->input('term');

        $raw = DB::table('employees')
            ->leftJoin('employee_departments', 'employees.id', '=', 'employee_departments.employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'employee_departments.department_id')
            ->leftJoin('department_positions', 'employees.id', '=', 'department_positions.employee_id')
            ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
            ->where('employees.status', 'Active')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('employees.name', 'like', "%{$search}%")
                        ->orWhere('employees.lastname', 'like', "%{$search}%")
                        ->orWhere('employees.email', 'like', "%{$search}%")
                        ->orWhere('departments.department_name', 'like', "%{$search}%")
                        ->orWhere('positions.position', 'like', "%{$search}%");
                });
            })
            ->select(
                'employees.id',
                'employees.name',
                'employees.lastname',
                'employees.email',
                'departments.department_name',
                'positions.position',
                'employees.image'
            )
            ->get();

        $employeeIds = $raw->pluck('id')->unique()->values()->all();
        $availabilityMap = $this->buildEmployeeDayAvailabilityMap($employeeIds, $targetDate);

        $grouped = $raw->groupBy('id')->map(function ($items) use ($availabilityMap) {
            $first = $items->first();

            $availability = $availabilityMap[$first->id] ?? [
                'available' => true,
                'badges' => [['label' => 'Verfügbar', 'level' => 'ok']],
                'reasons' => [],
                'date' => null,
            ];

            return [
                'id' => $first->id,
                'name' => $first->name,
                'lastname' => $first->lastname,
                'email' => $first->email,
                'departments' => $items->pluck('department_name')->filter()->unique()->values(),
                'positions' => $items->pluck('position')->filter()->unique()->values(),

                'on_leave' => !$availability['available'],
                'leave_info' => !empty($availability['reasons'])
                    ? [
                        'from' => $availability['date'] ?? null,
                        'to' => $availability['date'] ?? null,
                        'reason' => implode(', ', $availability['reasons']),
                    ]
                    : null,

                'is_available' => $availability['available'],
                'availability_badges' => $availability['badges'],
                'availability_reasons' => $availability['reasons'],

                'image' => $first->image ? asset('images/employee/' . $first->image) : null,
            ];
        })->values();

        return response()->json($grouped);
    }

    /*
    |--------------------------------------------------------------------------
    | Old ticket/product checks
    |--------------------------------------------------------------------------
    */
    public function checkTicket(Request $request)
    {
        $customerId = $request->input('customer_id');

        $tickets = DB::table('problems')
            ->leftJoin('new_leads', 'new_leads.id', '=', 'problems.customer_id')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'problems.product_id')
            ->where('problems.customer_id', $customerId)
            ->whereNull('problems.deleted_at')
            ->select(
                'problems.id',
                'problems.ticket_no',
                'problems.status',
                'problems.priority',
                'problems.date',
                'new_leads.name',
                'new_leads.lastname',
                'article_groups.article_group'
            )
            ->orderByDesc('problems.id')
            ->limit(20)
            ->get();

        return response()->json($tickets);
    }

    public function getProducts(Request $request)
    {
        $customerId = $request->customer_id;

        $data = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'lead_product_lists.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('lead_stages', 'lead_stages.key', '=', 'lead_product_lists.status')
            ->leftJoin('lead_stage_sub_stages', 'lead_stage_sub_stages.id', '=', 'lead_product_lists.lead_stage_sub_stage_id')
            ->select(
                'lead_product_lists.id as lp_id',
                'lead_product_lists.id as lead_product_list_id',
                'article_groups.article_group',
                'alt.street',
                'alt.city',
                'alt.postcode',
                'alt.object_name',
                'lead_product_lists.customer_id',
                'lead_product_lists.product_id',
                'lead_product_lists.alternative_id',
                'lead_product_lists.service',
                'lead_product_lists.status',
                'lead_product_lists.lead_stage_sub_stage_id',
                'lead_stages.id as lead_stage_id',
                'lead_stages.name as lead_stage_name',
                'lead_stages.color as lead_stage_color',
                'lead_stage_sub_stages.name as lead_stage_sub_stage_name',
                'lead_stage_sub_stages.color as lead_stage_sub_stage_color',
                'new_leads.name',
                'new_leads.lastname'
            )
            ->where('new_leads.id', $customerId)
            ->whereNull('lead_product_lists.deleted_at')
            ->whereNull('new_leads.deleted_at')
            ->whereNotIn(DB::raw('LOWER(lead_product_lists.status)'), ['junk', 'archived', 'reject', 'rejected', 'deleted'])
            ->orderByDesc('lead_product_lists.id')
            ->get();

        return response()->json($data, 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    public function profile($ticket_id)
    {
        $problem = Problem::with([
            'customer',
            'alternative',
            'product',
            'employees',
            'errors',
            'ticketTasks.employee',
            'ticketTasks.error',
            'appointments.employees',
            'leadStage',
            'leadStageSubStage',
            'leadProductList',
            'firstContact',
        ])->findOrFail($ticket_id);

        /*
        |--------------------------------------------------------------------------
        | Ticket number repair
        |--------------------------------------------------------------------------
        | Old tickets sometimes have only a random numeric number. From now on the
        | ticket number is always T-YYYYMM-##. When an old ticket is opened and the
        | format is missing, it receives the new format once.
        */
        if (!$this->ticketNoHasCurrentFormat((string) $problem->ticket_no)) {
            DB::transaction(function () use ($problem) {
                $problem->ticket_no = $this->generateTicketNo(
                    $problem->created_at ? Carbon::parse($problem->created_at) : now()
                );
                $problem->save();
            });

            $problem->refresh()->load([
                'customer',
                'alternative',
                'product',
                'employees',
                'errors',
                'ticketTasks.employee',
                'ticketTasks.error',
                'appointments.employees',
                'leadStage',
                'leadStageSubStage',
                'leadProductList',
                'firstContact',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Backward-compatible attributes for the Blade
        |--------------------------------------------------------------------------
        | The profile Blade also supports old DB-query rows, so we expose the same
        | customer/product/address aliases on the Eloquent Problem model.
        */
        $customer = $problem->customer;
        $alternative = $problem->alternative;
        $product = $problem->product;
        $firstContact = $problem->firstContact;

        if ($customer) {
            $problem->setAttribute('firma', $customer->firma);
            $problem->setAttribute('name', $customer->name);
            $problem->setAttribute('lastname', $customer->lastname);
            $problem->setAttribute('customer_no', $customer->customer_no);
            $problem->setAttribute('email', $customer->email);
            $problem->setAttribute('phone', $customer->phone ?: $customer->telephone);
        }

        if ($product) {
            $problem->setAttribute('article_group', $product->article_group);
            $problem->setAttribute('product', $product->article_group);
        }

        $problem->setAttribute('street', $alternative?->street ?: ($customer?->street ?? null));
        $problem->setAttribute('postcode', $alternative?->postcode ?: ($customer?->postcode ?? null));
        $problem->setAttribute('alt_city', $alternative?->city ?: ($customer?->city ?? null));
        $problem->setAttribute('city', $alternative?->city ?: ($customer?->city ?? null));

        if ($firstContact) {
            $problem->setAttribute('fname', $firstContact->name);
            $problem->setAttribute('flastname', $firstContact->lastname);
        }

        $reports = $this->ticketReportsForProblem($problem);

        $comments = class_exists(\App\Models\ProblemComment::class)
            ? \App\Models\ProblemComment::where('ticket_id', $problem->id)->latest()->get()
            : collect();

        return view('admin.problem.profile', [
            'problem' => $problem,
            'ticket' => $problem,
            'reports' => $reports,
            'comments' => $comments,
            'stageContext' => $this->problemStageContext($problem),
        ]);
    }


    private function ticketReportsForProblem(Problem $problem)
    {
        if (!class_exists(\App\Models\TicketReport::class)) {
            return collect();
        }

        $model = new \App\Models\TicketReport();
        $table = $model->getTable();

        if (!Schema::hasTable($table)) {
            return collect();
        }

        $query = \App\Models\TicketReport::query();

        if (Schema::hasColumn($table, 'ticket_id')) {
            $query->where('ticket_id', $problem->id);
        } elseif (Schema::hasColumn($table, 'problem_id')) {
            $query->where('problem_id', $problem->id);
        } elseif (Schema::hasColumn($table, 'appointment_id')) {
            $appointmentIds = MainAppointment::query()
                ->where('problem_id', $problem->id)
                ->pluck('id');

            if ($appointmentIds->isEmpty()) {
                return collect();
            }

            $query->whereIn('appointment_id', $appointmentIds);
        } else {
            return collect();
        }

        if (Schema::hasColumn($table, 'report_date')) {
            $query->orderByDesc('report_date');
        } elseif (Schema::hasColumn($table, 'created_at')) {
            $query->latest();
        } else {
            $query->orderByDesc('id');
        }

        return $query->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Fetch list/cards
    |--------------------------------------------------------------------------
    */
    public function fetch(Request $request)
    {
        try {
            $employeeId = (int) auth()->user()->name;

            $search = trim((string) $request->get('search', ''));
            $filter = (string) $request->get('filter', 'active');
            $priority = trim((string) $request->get('priority', ''));
            $productId = $request->integer('product_id') ?: null;

            $sortField = (string) $request->get('sort_field', 'created_at');
            $sortOrder = strtolower((string) $request->get('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';
            $perPage = max(6, min(50, (int) $request->get('per_page', 12)));
            $mode = (string) $request->get('mode', 'list');

            $activeStatuses = $this->activeStatuses();
            $archiveStatuses = $this->archiveStatuses();

            $minePivotOnly = function ($q) use ($employeeId) {
                $q->whereExists(function ($sub) use ($employeeId) {
                    $sub->select(DB::raw(1))
                        ->from('employee_problem')
                        ->whereColumn('employee_problem.problem_id', 'problems.id')
                        ->where('employee_problem.employee_id', $employeeId);
                });
            };

            $createdByMeOnly = function ($q) use ($employeeId) {
                $q->where('problems.first_contact', $employeeId);
            };

            $q = $this->baseTicketQuery();

            switch ($filter) {
                case 'active':
                    $q->whereIn('problems.status', $activeStatuses)
                        ->where(function ($qq) use ($minePivotOnly, $createdByMeOnly) {
                            $qq->where(fn($x) => $minePivotOnly($x))
                                ->orWhere(fn($x) => $createdByMeOnly($x));
                        });
                    break;

                case 'mine':
                    $q->whereIn('problems.status', $activeStatuses)
                        ->where(fn($qq) => $minePivotOnly($qq));
                    break;

                case 'created':
                    $q->whereIn('problems.status', $activeStatuses)
                        ->where(fn($qq) => $createdByMeOnly($qq));
                    break;

                case 'progress':
                    $q->whereIn('problems.status', ['process', 'in_bearbeitung'])
                        ->where(function ($qq) use ($minePivotOnly, $createdByMeOnly) {
                            $qq->where(fn($x) => $minePivotOnly($x))
                                ->orWhere(fn($x) => $createdByMeOnly($x));
                        });
                    break;

                case 'archive':
                case 'completed':
                    $q->whereIn('problems.status', $archiveStatuses)
                        ->where(function ($qq) use ($minePivotOnly, $createdByMeOnly) {
                            $qq->where(fn($x) => $minePivotOnly($x))
                                ->orWhere(fn($x) => $createdByMeOnly($x));
                        });
                    break;

                case 'junk':
                    $q->where('problems.status', 'junk')
                        ->where(function ($qq) use ($minePivotOnly, $createdByMeOnly) {
                            $qq->where(fn($x) => $minePivotOnly($x))
                                ->orWhere(fn($x) => $createdByMeOnly($x));
                        });
                    break;

                case 'all':
                    break;

                default:
                    $q->whereIn('problems.status', $activeStatuses)
                        ->where(function ($qq) use ($minePivotOnly, $createdByMeOnly) {
                            $qq->where(fn($x) => $minePivotOnly($x))
                                ->orWhere(fn($x) => $createdByMeOnly($x));
                        });
                    break;
            }

            if ($priority !== '') {
                $normalizedPriority = strtolower($priority);
                $priorityMap = [
                    'high' => ['high', 'dringend', 'sehr dringend', 'sehr_dringend'],
                    'medium' => ['medium', 'mittel'],
                    'low' => ['low', 'normal'],
                ];

                if (isset($priorityMap[$normalizedPriority])) {
                    $q->whereIn(DB::raw('LOWER(problems.priority)'), $priorityMap[$normalizedPriority]);
                } else {
                    $q->where('problems.priority', $priority);
                }
            }

            if ($productId) {
                $q->where('problems.product_id', $productId);
            }

            $this->applyTicketSearch($q, $search);

            $allowed = [
                'ticket_no' => 'problems.ticket_no',
                'status' => 'problems.status',
                'priority' => 'problems.priority',
                'error_type' => 'problems.error_type',
                'start_date' => 'problems.date',
                'updated_at' => 'problems.updated_at',
                'customer' => 'new_leads.lastname',
                'product' => 'article_groups.article_group',
                'created_at' => 'problems.created_at',
                'stage' => 'lead_stages.sort_order',
            ];

            $orderCol = $allowed[$sortField] ?? 'problems.created_at';
            $q->orderBy($orderCol, $sortOrder);

            $tickets = $q->paginate($perPage)->appends($request->all());

            $this->hydrateTickets($tickets->getCollection());

            $html = view(
                $mode === 'cards'
                ? 'admin.problem.partials._cards'
                : 'admin.problem.partials._list',
                ['tickets' => $tickets]
            )->render();

            return response()->json([
                'html' => $html,
                'items' => $tickets->getCollection()->pluck('ticket_json')->values(),
                'pagination' => (string) $tickets->links(),
                'total' => $tickets->total(),
                'analytics' => $this->ticketAnalytics($employeeId),
            ]);
        } catch (\Throwable $e) {
            Log::error('ProblemController@fetch failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'query' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Loading failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Kanban
    |--------------------------------------------------------------------------
    */
    public function kanban(Request $request)
    {
        try {
            $employeeId = (int) auth()->user()->name;

            $search = trim((string) $request->get('search', ''));
            $filter = (string) $request->get('filter', 'all');

            $minePivotOnly = function ($q) use ($employeeId) {
                $q->whereExists(function ($sub) use ($employeeId) {
                    $sub->select(DB::raw(1))
                        ->from('employee_problem')
                        ->whereColumn('employee_problem.problem_id', 'problems.id')
                        ->where('employee_problem.employee_id', $employeeId);
                });
            };

            $createdByMeOnly = function ($q) use ($employeeId) {
                $q->where('problems.first_contact', $employeeId);
            };

            $q = $this->baseTicketQuery();

            switch ($filter) {
                case 'active':
                    $q->whereIn('problems.status', $this->activeStatuses())
                        ->where(function ($qq) use ($minePivotOnly, $createdByMeOnly) {
                            $qq->where(fn($x) => $minePivotOnly($x))
                                ->orWhere(fn($x) => $createdByMeOnly($x));
                        });
                    break;

                case 'mine':
                    $q->whereIn('problems.status', $this->activeStatuses())
                        ->where(fn($qq) => $minePivotOnly($qq));
                    break;

                case 'created':
                    $q->whereIn('problems.status', $this->activeStatuses())
                        ->where(fn($qq) => $createdByMeOnly($qq));
                    break;

                case 'progress':
                    $q->whereIn('problems.status', ['process', 'in_bearbeitung'])
                        ->where(function ($qq) use ($minePivotOnly, $createdByMeOnly) {
                            $qq->where(fn($x) => $minePivotOnly($x))
                                ->orWhere(fn($x) => $createdByMeOnly($x));
                        });
                    break;

                case 'completed':
                case 'archive':
                    $q->whereIn('problems.status', $this->archiveStatuses())
                        ->where(function ($qq) use ($minePivotOnly, $createdByMeOnly) {
                            $qq->where(fn($x) => $minePivotOnly($x))
                                ->orWhere(fn($x) => $createdByMeOnly($x));
                        });
                    break;

                case 'junk':
                    $q->where('problems.status', 'junk')
                        ->where(function ($qq) use ($minePivotOnly, $createdByMeOnly) {
                            $qq->where(fn($x) => $minePivotOnly($x))
                                ->orWhere(fn($x) => $createdByMeOnly($x));
                        });
                    break;

                case 'all':
                    break;

                default:
                    $q->whereIn('problems.status', $this->activeStatuses())
                        ->where(function ($qq) use ($minePivotOnly, $createdByMeOnly) {
                            $qq->where(fn($x) => $minePivotOnly($x))
                                ->orWhere(fn($x) => $createdByMeOnly($x));
                        });
                    break;
            }

            $this->applyTicketSearch($q, $search);

            $tickets = $q->orderByDesc('problems.updated_at')->get();

            $this->hydrateTickets($tickets);

            return response()->json($tickets->pluck('ticket_json')->values());
        } catch (\Throwable $e) {
            Log::error('ProblemController@kanban failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'query' => $request->all(),
            ]);

            return response()->json([
                'message' => 'Kanban loading failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTicket(Request $request)
    {
        return $this->kanban($request);
    }

    public function getKanban(Request $request)
    {
        return $this->kanban($request);
    }

    public function searchKanban(Request $request)
    {
        return $this->kanban($request);
    }

    public function updateStage(Request $request, $ticket_id, $stage = null)
    {
        $status = $request->input('status', $stage);

        $validator = Validator::make(
            ['status' => $status],
            ['status' => 'required|string|in:offen,open,process,end,junk']
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ungültiger Status.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if ($status === 'open') {
            $status = 'offen';
        }

        $ticket = Problem::whereNull('deleted_at')->find($ticket_id);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket nicht gefunden.',
            ], 404);
        }

        $empId = (int) auth()->user()->name;
        $now = now();

        $ticket->status = $status;
        $ticket->edit_user = $empId;
        $ticket->edit_date = $now->toDateString();

        if ($status === 'offen') {
            $ticket->date = $ticket->date ?: $now->toDateString();
            $ticket->start_user = $ticket->start_user ?: $empId;
            $ticket->progress_date = null;
            $ticket->progress_user = null;
            $ticket->end_date = null;
            $ticket->end_user = null;
        }

        if ($status === 'process') {
            $ticket->progress_date = $now->toDateString();
            $ticket->progress_user = $empId;
            $ticket->end_date = null;
            $ticket->end_user = null;
        }

        if ($status === 'end') {
            $ticket->end_date = $now->toDateString();
            $ticket->end_user = $empId;
        }

        if ($status === 'junk') {
            $ticket->end_date = null;
            $ticket->end_user = null;
        }

        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Status aktualisiert.',
            'status' => $ticket->status,
        ]);
    }

    public function getProgress($id)
    {
        $ticket = Problem::findOrFail($id);

        return response()->json([
            'success' => true,
            'ticket_id' => $ticket->id,
            'status' => $ticket->status,
            'progress' => $ticket->progress,
            'progress_date' => $ticket->progress_date,
            'progress_user' => $ticket->progress_user,
        ]);
    }

    public function updateType(Request $request, $id)
    {
        $request->validate([
            'error_type' => ['required', 'string', 'max:255'],
        ]);

        $ticket = Problem::findOrFail($id);
        $ticket->error_type = $request->input('error_type');
        $ticket->edit_user = auth()->user()->name;
        $ticket->edit_date = now()->toDateString();
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Tickettyp aktualisiert.',
            'error_type' => $ticket->error_type,
        ]);
    }

    public function loadTickets(Request $request)
    {
        return $this->fetch($request);
    }

    public function assignTicket(Request $request, $id)
    {
        $ticket = Problem::findOrFail($id);

        $employeeIds = $this->cleanIntegerArray(
            $request->input('employee_ids', $request->input('employees', $request->input('responsible', [])))
        );

        if (empty($employeeIds) && $request->filled('employee_id')) {
            $employeeIds = [(int) $request->input('employee_id')];
        }

        if (empty($employeeIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte mindestens einen Mitarbeiter wählen.',
            ], 422);
        }

        $ticket->responsible = $employeeIds[0] ?? null;
        $ticket->edit_user = auth()->user()->name;
        $ticket->edit_date = now()->toDateString();
        $ticket->save();

        if (method_exists($ticket, 'employee')) {
            $ticket->employee()->sync($employeeIds);
        } elseif (method_exists($ticket, 'employees')) {
            $ticket->employees()->sync($employeeIds);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket wurde zugewiesen.',
            'employees' => $employeeIds,
        ]);
    }

    public function updateStatus(Request $request, Problem $problem)
    {
        $request->validate([
            'status' => ['required', 'string', 'max:255'],
        ]);

        $problem->status = $this->normalizeTicketStatus($request->input('status'));
        $problem->edit_user = auth()->user()->name;
        $problem->edit_date = now()->toDateString();
        $problem->save();

        return response()->json([
            'success' => true,
            'message' => 'Status aktualisiert.',
            'status' => $problem->status,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Stage/Sub Stage endpoints
    |--------------------------------------------------------------------------
    */
    public function ticketLeadStageContext(Request $request)
    {
        $this->normalizeTicketStageInputs($request);
        $context = $this->resolveTicketStageContext($request);

        $stage = null;
        $subStages = collect();
        $selectedSubStage = null;

        if (!empty($context['lead_stage_id'])) {
            $stage = DB::table('lead_stages')
                ->where('id', $context['lead_stage_id'])
                ->select(['id', 'key', 'name', 'color'])
                ->first();

            $subStageQuery = DB::table('lead_stage_sub_stages')
                ->where('lead_stage_id', $context['lead_stage_id']);

            if (Schema::hasColumn('lead_stage_sub_stages', 'is_active')) {
                $subStageQuery->where('is_active', true);
            }

            if (Schema::hasColumn('lead_stage_sub_stages', 'sort_order')) {
                $subStageQuery->orderBy('sort_order');
            }

            $subStages = $subStageQuery
                ->orderBy('id')
                ->get(['id', 'lead_stage_id', 'key', 'name', 'color']);
        }

        if (!empty($context['lead_stage_sub_stage_id'])) {
            $selectedSubStage = DB::table('lead_stage_sub_stages')
                ->where('id', $context['lead_stage_sub_stage_id'])
                ->select(['id', 'lead_stage_id', 'key', 'name', 'color'])
                ->first();
        }

        return response()->json([
            'success' => true,
            'context' => $context,
            'stage' => $stage,
            'sub_stages' => $subStages,
            'selected_sub_stage' => $selectedSubStage,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Appointment availability for ticket create
    |--------------------------------------------------------------------------
    */
    public function checkTicketAppointmentAvailability(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:480'],
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['required', 'integer', 'exists:employees,id'],
        ]);

        $availability = $this->buildTicketAppointmentAvailability(
            $validated['employee_ids'],
            $validated['date'],
            $validated['start_time'] ?? null,
            $validated['end_time'] ?? null,
            (int) ($validated['duration_minutes'] ?? 60)
        );

        return response()->json($availability);
    }

    private function createAppointmentFromProblem(Problem $problem, Request $request, array $responsibleIds = []): MainAppointment
    {
        $customer = NewLeads::findOrFail($problem->customer_id);
        $product = ArticleGroup::findOrFail($problem->product_id);

        $alternative = $problem->alternative_id
            ? LeadAlternativeAdd::find($problem->alternative_id)
            : null;

        $customerName = trim(
            ($customer->firma ? $customer->firma . ' - ' : '') .
            trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? ''))
        );

        if ($customerName === '') {
            $customerName = 'Kunde #' . $customer->id;
        }

        $ticketNo = $problem->ticket_no ?: ('#' . $problem->id);

        $appointmentProducts = [
            $product->article_group => [
                $problem->alternative_id ? (int) $problem->alternative_id : null,
                (int) $problem->product_id,
                (string) $problem->customer_id,
            ],
        ];

        $appointmentDate = $request->input('appointment_date')
            ?: ($problem->date ? Carbon::parse($problem->date)->format('Y-m-d') : now()->toDateString());

        $startTime = $request->input('appointment_start_time') ?: '09:00';
        $endTime = $request->input('appointment_end_time') ?: '10:00';

        $start = Carbon::parse($appointmentDate . ' ' . $startTime);
        $end = Carbon::parse($appointmentDate . ' ' . $endTime);

        if ($end->lessThanOrEqualTo($start)) {
            $end = $start->copy()->addMinutes((int) ($request->input('appointment_duration_minutes') ?: 60));
            $endTime = $end->format('H:i');
        }

        $totalMinutes = $start->diffInMinutes($end);

        $street = $alternative?->street ?: $customer->street;
        $postcode = $alternative?->postcode ?: $customer->postcode;
        $city = $alternative?->city ?: $customer->city;

        $fullAddress = $alternative?->full_address;

        if (!$fullAddress) {
            $fullAddress = trim(
                ($street ? $street . ', ' : '') .
                ($postcode ? $postcode . ' ' : '') .
                ($city ?: '')
            );
        }

        $appointment = MainAppointment::create([
            'created_by' => auth()->user()->name,
            'name' => 'Ticket - ' . $customerName . ' - ' . $ticketNo,
            'note' => $problem->problem,
            'color' => '#f8ac00',

            'start_date' => $appointmentDate,
            'end_date' => $appointmentDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_time' => $totalMinutes,

            'appointment_type' => $this->translateProblemTypeToGerman($problem->error_type),
            'execution_type' => 'Ticket',
            'source' => $problem->source,
            'contact_mode' => 'select',

            'customer_id' => $problem->customer_id,
            'problem_id' => $problem->id,

            'lead_product_list_id' => $problem->lead_product_list_id,
            'lead_stage_id' => $problem->lead_stage_id,
            'lead_stage_sub_stage_id' => $problem->lead_stage_sub_stage_id,

            'products' => $appointmentProducts,

            'street' => $street,
            'postcode' => $postcode,
            'city' => $city,
            'full_address' => $fullAddress,
            'latitude' => $alternative?->lat ?: $customer->latitude,
            'longitude' => $alternative?->lon ?: $customer->longitude,

            'email' => $customer->email,
            'phone' => $customer->phone ?: $customer->telephone,

            'status' => 'offen',
            'priority' => $problem->priority ?: 'normal',
            'public' => 0,

            'type' => 'Problem',
            'pre_type' => 'Ticket',
        ]);

        foreach ($responsibleIds as $employeeId) {
            MainAppointmentEmployee::updateOrCreate(
                [
                    'appointment_id' => $appointment->id,
                    'employee_id' => (int) $employeeId,
                ],
                [
                    'status' => 'pending',
                    'reason' => null,
                ]
            );
        }

        return $appointment;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers: ticket query
    |--------------------------------------------------------------------------
    */
    private function baseTicketQuery()
    {
        return DB::table('problems')
            ->join('new_leads', 'new_leads.id', '=', 'problems.customer_id')
            ->leftJoin('lead_alternative_adds as alt', 'alt.id', '=', 'problems.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'problems.product_id')
            ->leftJoin('employees as first', 'first.id', '=', 'problems.first_contact')
            ->leftJoin('lead_stages', 'lead_stages.id', '=', 'problems.lead_stage_id')
            ->leftJoin('lead_stage_sub_stages', 'lead_stage_sub_stages.id', '=', 'problems.lead_stage_sub_stage_id')
            ->select(
                'problems.id',
                'problems.ticket_no',
                'problems.status',
                'problems.priority',
                'problems.error_type',
                'problems.error_code',
                'problems.problem',
                'problems.solution',
                'problems.date as start_date',
                'problems.progress_date',
                'problems.end_date',
                'problems.created_at',
                'problems.updated_at',
                'problems.edit_date',
                'problems.edit_user',
                'problems.end_user',
                'problems.start_user',
                'problems.progress_user',
                'problems.first_contact',
                'problems.lead_product_list_id',
                'problems.lead_stage_id',
                'lead_stages.name as lead_stage_name',
                'lead_stages.color as lead_stage_color',
                'problems.lead_stage_sub_stage_id',
                'lead_stage_sub_stages.name as lead_stage_sub_stage_name',
                'lead_stage_sub_stages.color as lead_stage_sub_stage_color',

                'new_leads.firma',
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.email',
                'new_leads.phone',

                'alt.street',
                'alt.postcode',
                'alt.city as alt_city',

                'article_groups.id as product_id',
                'article_groups.article_group as product',

                'first.name as first_name',
                'first.lastname as first_lastname'
            )
            ->whereNull('problems.deleted_at');
    }

    private function applyTicketSearch($q, string $search): void
    {
        if ($search === '') {
            return;
        }

        $q->where(function ($qq) use ($search) {
            $qq->where('problems.ticket_no', 'like', "%{$search}%")
                ->orWhere('problems.problem', 'like', "%{$search}%")
                ->orWhere('problems.solution', 'like', "%{$search}%")
                ->orWhere('problems.error_type', 'like', "%{$search}%")
                ->orWhere('problems.error_code', 'like', "%{$search}%")
                ->orWhere('new_leads.name', 'like', "%{$search}%")
                ->orWhere('new_leads.lastname', 'like', "%{$search}%")
                ->orWhere('new_leads.firma', 'like', "%{$search}%")
                ->orWhere('article_groups.article_group', 'like', "%{$search}%")
                ->orWhere('alt.street', 'like', "%{$search}%")
                ->orWhere('alt.postcode', 'like', "%{$search}%")
                ->orWhere('alt.city', 'like', "%{$search}%");
        });
    }

    private function hydrateTickets($tickets): void
    {
        $ids = $tickets->pluck('id')->filter()->unique()->values();

        $emp = DB::table('employee_problem')
            ->join('employees', 'employees.id', '=', 'employee_problem.employee_id')
            ->whereIn('employee_problem.problem_id', $ids)
            ->select(
                'employee_problem.problem_id',
                'employees.id as employee_id',
                'employees.name',
                'employees.lastname',
                'employees.image'
            )
            ->get()
            ->groupBy('problem_id');

        $errs = DB::table('error_problem')
            ->join('errors', 'errors.id', '=', 'error_problem.error_id')
            ->whereIn('error_problem.problem_id', $ids)
            ->select(
                'error_problem.problem_id',
                'errors.id as error_id',
                'errors.error_code',
                'errors.problem_types',
                'errors.reason',
                'errors.solution',
                'errors.article_name'
            )
            ->get()
            ->groupBy('problem_id');

        $userIds = $tickets
            ->flatMap(function ($t) use ($emp) {
                $assignedIds = $emp->get($t->id, collect())->pluck('employee_id')->all();

                return array_filter(array_merge([
                    $t->first_contact ?? null,
                    $t->start_user ?? null,
                    $t->edit_user ?? null,
                    $t->progress_user ?? null,
                    $t->end_user ?? null,
                ], $assignedIds));
            })
            ->unique()
            ->values();

        $userMap = collect();

        if ($userIds->isNotEmpty()) {
            $userMap = DB::table('employees')
                ->whereIn('id', $userIds)
                ->select('id', 'name', 'lastname', 'image')
                ->get()
                ->keyBy('id');
        }

        $currentEmployee = DB::table('employees')
            ->where('id', (int) auth()->user()->name)
            ->select('id', 'name', 'lastname', 'image')
            ->first();

        $mkUser = function ($id) use ($userMap) {
            if (!$id) {
                return null;
            }

            $u = $userMap->get($id);

            if (!$u) {
                return null;
            }

            return [
                'id' => $u->id,
                'name' => trim(($u->name ?? '') . ' ' . ($u->lastname ?? '')),
                'avatar_url' => !empty($u->image)
                    ? asset('images/employee/' . $u->image)
                    : asset('images/gender/male.png'),
            ];
        };

        $tickets->transform(function ($t) use ($emp, $errs, $mkUser, $currentEmployee) {
            $t->employees = ($emp->get($t->id, collect()))->map(fn($e) => [
                'id' => $e->employee_id,
                'name' => trim(($e->name ?? '') . ' ' . ($e->lastname ?? '')),
                'firstname' => $e->name,
                'lastname' => $e->lastname,
                'avatar_url' => !empty($e->image)
                    ? asset('images/employee/' . $e->image)
                    : asset('images/gender/male.png'),
            ])->values()->all();

            $t->errors = ($errs->get($t->id, collect()))->map(fn($er) => [
                'id' => $er->error_id,
                'error_code' => $er->error_code,
                'problem_types' => $er->problem_types,
                'reason' => $er->reason,
                'solution' => $er->solution,
                'article_name' => $er->article_name,
            ])->values()->all();

            $t->creator_user = $mkUser($t->first_contact ?? null);
            $t->created_by_user = $mkUser($t->start_user ?? null);
            $t->updated_by_user = $mkUser($t->edit_user ?? null);
            $t->progress_by_user = $mkUser($t->progress_user ?? null);
            $t->ended_by_user = $mkUser($t->end_user ?? null);

            $t->current_user = $currentEmployee ? [
                'id' => $currentEmployee->id,
                'name' => trim(($currentEmployee->name ?? '') . ' ' . ($currentEmployee->lastname ?? '')),
                'avatar_url' => !empty($currentEmployee->image)
                    ? asset('images/employee/' . $currentEmployee->image)
                    : asset('images/gender/male.png'),
            ] : null;

            $t->history = array_values(array_filter([
                $t->start_date ? [
                    'type' => 'created',
                    'label' => 'Erstellt',
                    'date' => $t->start_date,
                    'user' => $t->created_by_user ?: $t->creator_user,
                ] : null,

                $t->progress_date ? [
                    'type' => 'progress',
                    'label' => 'In Bearbeitung',
                    'date' => $t->progress_date,
                    'user' => $t->progress_by_user,
                ] : null,

                $t->edit_date ? [
                    'type' => 'edited',
                    'label' => 'Bearbeitet',
                    'date' => $t->edit_date,
                    'user' => $t->updated_by_user,
                ] : null,

                $t->end_date ? [
                    'type' => 'ended',
                    'label' => 'Beendet',
                    'date' => $t->end_date,
                    'user' => $t->ended_by_user,
                ] : null,
            ]));

            $t->ticket_json = [
                'id' => $t->id,
                'ticket_no' => $t->ticket_no,
                'status' => $t->status ?? 'offen',
                'priority' => $t->priority,

                'error_type' => $t->error_type,
                'error_type_label' => $this->errorTypeLabel($t->error_type),

                'lead_product_list_id' => $t->lead_product_list_id,
                'lead_stage_id' => $t->lead_stage_id,
                'lead_stage_name' => $t->lead_stage_name,
                'lead_stage_color' => $t->lead_stage_color,
                'lead_stage_sub_stage_id' => $t->lead_stage_sub_stage_id,
                'lead_stage_sub_stage_name' => $t->lead_stage_sub_stage_name,
                'lead_stage_sub_stage_color' => $t->lead_stage_sub_stage_color,

                'created_at' => $t->created_at,
                'updated_at' => $t->updated_at,
                'start_date' => $t->start_date,
                'progress_date' => $t->progress_date,
                'edit_date' => $t->edit_date,
                'end_date' => $t->end_date,

                'customer' => trim(($t->firma ?: '') . ' ' . ($t->name ?: '') . ' ' . ($t->lastname ?: '')),
                'product' => $t->product ?? '',
                'address' => trim(($t->street ?: '') . ' · ' . ($t->postcode ?: '') . ' ' . ($t->alt_city ?: '')),

                'employees' => $t->employees,
                'errors' => $t->errors,

                'creator_user' => $t->creator_user,
                'created_by_user' => $t->created_by_user,
                'updated_by_user' => $t->updated_by_user,
                'progress_by_user' => $t->progress_by_user,
                'ended_by_user' => $t->ended_by_user,

                'history' => $t->history,
                'current_user' => $t->current_user,

                'profile_url' => route('problem.profile', $t->id),
                'edit_url' => url('problem_edit/' . $t->id),
                'delete_url' => url('problem_destroy/' . $t->id),
            ];

            return $t;
        });
    }

    private function ticketAnalytics(int $employeeId): array
    {
        $baseAll = DB::table('problems')->whereNull('deleted_at');

        $minePivotOnly = function ($q) use ($employeeId) {
            $q->whereExists(function ($sub) use ($employeeId) {
                $sub->select(DB::raw(1))
                    ->from('employee_problem')
                    ->whereColumn('employee_problem.problem_id', 'problems.id')
                    ->where('employee_problem.employee_id', $employeeId);
            });
        };

        $createdByMeOnly = function ($q) use ($employeeId) {
            $q->where('first_contact', $employeeId);
        };

        return [
            'active' => (clone $baseAll)
                ->whereIn('status', $this->activeStatuses())
                ->where(function ($q) use ($minePivotOnly, $createdByMeOnly) {
                    $q->where(fn($x) => $minePivotOnly($x))
                        ->orWhere(fn($x) => $createdByMeOnly($x));
                })
                ->count(),

            'mine' => (clone $baseAll)
                ->whereIn('status', $this->activeStatuses())
                ->where(fn($q) => $minePivotOnly($q))
                ->count(),

            'created' => (clone $baseAll)
                ->whereIn('status', $this->activeStatuses())
                ->where('first_contact', $employeeId)
                ->count(),

            'process' => (clone $baseAll)
                ->whereIn('status', ['process', 'in_bearbeitung'])
                ->count(),

            'archive' => (clone $baseAll)
                ->whereIn('status', $this->archiveStatuses())
                ->count(),

            'end' => (clone $baseAll)
                ->whereIn('status', $this->archiveStatuses())
                ->count(),

            'junk' => (clone $baseAll)
                ->where('status', 'junk')
                ->count(),

            'all' => (clone $baseAll)->count(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers: stage
    |--------------------------------------------------------------------------
    */
    protected function nullableInteger($value): ?int
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        $value = trim((string) ($value ?? ''));

        return preg_match('/^\d+$/', $value) ? (int) $value : null;
    }

    protected function normalizeTicketStageInputs(Request $request): void
    {
        if ($request->boolean('nuriva')) {
            $montageStage = DB::table('lead_stages')
                ->where(function ($query) {
                    $query->whereRaw('LOWER(`key`) = ?', ['montage'])
                        ->orWhereRaw('LOWER(`name`) = ?', ['montage']);
                })
                ->orderBy('id')
                ->first();

            if ($montageStage) {
                $request->merge([
                    'lead_stage_id' => (int) $montageStage->id,
                    'lead_stage_sub_stage_id' => null,
                ]);
            }
        }

        $request->merge([
            'customer_id' => $this->nullableInteger($request->input('customer_id')),
            'alternative_id' => $this->nullableInteger($request->input('alternative_id')),
            'product_id' => $this->nullableInteger($request->input('product_id')),
            'lead_product_list_id' => $this->nullableInteger($request->input('lead_product_list_id')),
            'lead_stage_id' => $this->nullableInteger($request->input('lead_stage_id')),
            'lead_stage_sub_stage_id' => $this->nullableInteger($request->input('lead_stage_sub_stage_id')),
        ]);
    }

    protected function resolveTicketStageContext(Request $request): array
    {
        $leadProductListId = $this->nullableInteger($request->input('lead_product_list_id'));
        $leadStageId = $this->nullableInteger($request->input('lead_stage_id'));
        $leadSubStageId = $this->nullableInteger($request->input('lead_stage_sub_stage_id'));

        if (!$leadProductListId) {
            $customerId = $this->nullableInteger($request->input('customer_id'));
            $alternativeId = $this->nullableInteger($request->input('alternative_id'));
            $productId = $this->nullableInteger($request->input('product_id'));

            if ($customerId && $alternativeId && $productId) {
                $leadProductListId = DB::table('lead_product_lists')
                    ->where('customer_id', $customerId)
                    ->where('alternative_id', $alternativeId)
                    ->where('product_id', $productId)
                    ->value('id');
            }
        }

        if ($leadProductListId && (!$leadStageId || !$leadSubStageId)) {
            $leadProduct = DB::table('lead_product_lists')
                ->leftJoin('lead_stages', 'lead_stages.key', '=', 'lead_product_lists.status')
                ->where('lead_product_lists.id', $leadProductListId)
                ->select([
                    'lead_product_lists.id',
                    'lead_product_lists.lead_stage_sub_stage_id',
                    'lead_stages.id as lead_stage_id',
                ])
                ->first();

            if ($leadProduct) {
                $leadStageId = $leadStageId ?: ($leadProduct->lead_stage_id ? (int) $leadProduct->lead_stage_id : null);
                $leadSubStageId = $leadSubStageId ?: ($leadProduct->lead_stage_sub_stage_id ? (int) $leadProduct->lead_stage_sub_stage_id : null);
            }
        }

        if (!$leadStageId && $leadSubStageId) {
            $leadStageId = DB::table('lead_stage_sub_stages')
                ->where('id', $leadSubStageId)
                ->value('lead_stage_id');
        }

        if ($leadStageId && $leadSubStageId) {
            $valid = DB::table('lead_stage_sub_stages')
                ->where('id', $leadSubStageId)
                ->where('lead_stage_id', $leadStageId)
                ->exists();

            if (!$valid) {
                $leadSubStageId = null;
            }
        }

        return [
            'lead_product_list_id' => $leadProductListId ? (int) $leadProductListId : null,
            'lead_stage_id' => $leadStageId ? (int) $leadStageId : null,
            'lead_stage_sub_stage_id' => $leadSubStageId ? (int) $leadSubStageId : null,
        ];
    }

    protected function leadStageFormOptions(): array
    {
        $leadStages = DB::table('lead_stages')
            ->when(Schema::hasColumn('lead_stages', 'is_active'), fn($q) => $q->where('is_active', true))
            ->when(Schema::hasColumn('lead_stages', 'sort_order'), fn($q) => $q->orderBy('sort_order'))
            ->orderBy('id')
            ->get(['id', 'key', 'name', 'color']);

        $leadSubStages = DB::table('lead_stage_sub_stages')
            ->when(Schema::hasColumn('lead_stage_sub_stages', 'is_active'), fn($q) => $q->where('is_active', true))
            ->when(Schema::hasColumn('lead_stage_sub_stages', 'sort_order'), fn($q) => $q->orderBy('sort_order'))
            ->orderBy('id')
            ->get(['id', 'lead_stage_id', 'key', 'name', 'color'])
            ->groupBy('lead_stage_id');

        $leadStageOptions = $leadStages->map(function ($stage) use ($leadSubStages) {
            return [
                'id' => (int) $stage->id,
                'key' => $stage->key,
                'name' => $stage->name,
                'color' => $stage->color ?: '#74b2d4',
                'sub_stages' => ($leadSubStages->get($stage->id, collect()))->map(function ($subStage) {
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

        return [$leadStages, $leadStageOptions];
    }

    private function problemStageContext(Problem $problem): array
    {
        $problem->loadMissing(['leadStage', 'leadStageSubStage', 'leadProductList']);

        return [
            'lead_product_list_id' => $problem->lead_product_list_id,
            'lead_stage_id' => $problem->lead_stage_id,
            'lead_stage_name' => $problem->leadStage?->name,
            'lead_stage_color' => $problem->leadStage?->color ?: '#74b2d4',
            'lead_stage_sub_stage_id' => $problem->lead_stage_sub_stage_id,
            'lead_stage_sub_stage_name' => $problem->leadStageSubStage?->name,
            'lead_stage_sub_stage_color' => $problem->leadStageSubStage?->color ?: '#93c21c',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers: availability
    |--------------------------------------------------------------------------
    */
    protected function buildTicketAppointmentAvailability(array $employeeIds, string $date, ?string $startTime = null, ?string $endTime = null, int $durationMinutes = 60): array
    {
        $employeeIds = collect($employeeIds)
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $date = Carbon::parse($date)->toDateString();
        $durationMinutes = max(15, min(480, $durationMinutes));

        $employees = Employee::whereIn('id', $employeeIds)
            ->get(['id', 'name', 'lastname', 'image'])
            ->keyBy('id');

        $dayMap = $this->buildEmployeeDayAvailabilityMap($employeeIds, $date);
        $appointmentsMap = $this->buildEmployeeAppointmentMap($employeeIds, $date);

        $requestedAvailable = true;

        if ($startTime && $endTime) {
            $requestedStart = Carbon::parse($date . ' ' . $startTime);
            $requestedEnd = Carbon::parse($date . ' ' . $endTime);

            if ($requestedEnd->lessThanOrEqualTo($requestedStart)) {
                $requestedEnd = $requestedStart->copy()->addMinutes($durationMinutes);
                $endTime = $requestedEnd->format('H:i');
            }

            foreach ($employeeIds as $employeeId) {
                if (!($dayMap[$employeeId]['available'] ?? true)) {
                    $requestedAvailable = false;
                    continue;
                }

                foreach (($appointmentsMap[$employeeId] ?? []) as $appointment) {
                    if ($this->timeRangesOverlap($requestedStart, $requestedEnd, $appointment['start'], $appointment['end'])) {
                        $requestedAvailable = false;
                        $dayMap[$employeeId]['available'] = false;
                        $dayMap[$employeeId]['reasons'][] = 'Termin ' . $appointment['start']->format('H:i') . '–' . $appointment['end']->format('H:i');
                        $dayMap[$employeeId]['badges'][] = [
                            'label' => 'Termin ' . $appointment['start']->format('H:i') . '–' . $appointment['end']->format('H:i'),
                            'level' => 'warning',
                        ];
                        break;
                    }
                }
            }
        }

        $slots = $this->generateAvailableSlots($employeeIds, $date, $durationMinutes, $dayMap, $appointmentsMap);

        $employeePayload = collect($employeeIds)->map(function ($employeeId) use ($employees, $dayMap) {
            $employee = $employees->get($employeeId);

            $availability = $dayMap[$employeeId] ?? [
                'available' => true,
                'badges' => [['label' => 'Verfügbar', 'level' => 'ok']],
                'reasons' => [],
            ];

            return [
                'id' => $employeeId,
                'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) ?: ('Mitarbeiter #' . $employeeId),
                'available' => (bool) ($availability['available'] ?? true),
                'reasons' => array_values(array_unique($availability['reasons'] ?? [])),
                'badges' => $availability['badges'] ?? [],
            ];
        })->values();

        $allDayAvailable = $employeePayload->every(fn($employee) => $employee['available']);

        $available = $startTime && $endTime
            ? ($requestedAvailable && $allDayAvailable)
            : ($allDayAvailable && count($slots) > 0);

        return [
            'available' => $available,
            'message' => $available
                ? 'Alle ausgewählten Mitarbeiter sind verfügbar.'
                : 'Mindestens ein Mitarbeiter ist abwesend oder hat in diesem Zeitraum bereits einen Termin.',
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_minutes' => $durationMinutes,
            'employees' => $employeePayload,
            'slots' => $slots,
        ];
    }

    protected function buildEmployeeDayAvailabilityMap(array $employeeIds, string $date): array
    {
        $date = Carbon::parse($date)->toDateString();

        $map = [];

        foreach ($employeeIds as $employeeId) {
            $map[(int) $employeeId] = [
                'date' => $date,
                'available' => true,
                'reasons' => [],
                'badges' => [['label' => 'Verfügbar', 'level' => 'ok']],
            ];
        }

        $removeOkBadge = function (&$row) {
            $row['badges'] = collect($row['badges'] ?? [])
                ->reject(fn($badge) => ($badge['label'] ?? '') === 'Verfügbar')
                ->values()
                ->all();
        };

        $leaves = DB::table('leaves')
            ->whereIn('emp_id', $employeeIds)
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->where(function ($q) {
                $q->where('approved', 'Yes')
                    ->orWhere('approved', 1)
                    ->orWhere('status', 'Approved')
                    ->orWhere('status', 'accept');
            })
            ->get();

        foreach ($leaves as $leave) {
            $employeeId = (int) $leave->emp_id;

            if (!isset($map[$employeeId])) {
                continue;
            }

            $removeOkBadge($map[$employeeId]);

            $map[$employeeId]['available'] = false;
            $map[$employeeId]['reasons'][] = 'Urlaub';
            $map[$employeeId]['badges'][] = [
                'label' => 'Urlaub ' . $leave->start_date . '–' . ($leave->end_date ?: $leave->start_date),
                'level' => 'danger',
            ];
        }

        $sicks = DB::table('employee_sicks')
            ->whereIn('emp_id', $employeeIds)
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->get();

        foreach ($sicks as $sick) {
            $employeeId = (int) $sick->emp_id;

            if (!isset($map[$employeeId])) {
                continue;
            }

            $removeOkBadge($map[$employeeId]);

            $map[$employeeId]['available'] = false;
            $map[$employeeId]['reasons'][] = 'Krank';
            $map[$employeeId]['badges'][] = [
                'label' => 'Krank ' . $sick->start_date . '–' . ($sick->end_date ?: $sick->start_date),
                'level' => 'danger',
            ];
        }

        $from = Carbon::parse($date)->startOfDay();
        $to = Carbon::parse($date)->endOfDay();

        $recurringLeaves = EmployeeRecurringLeave::with(['exdates', 'overrides'])
            ->whereIn('employee_id', $employeeIds)
            ->where('is_active', true)
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            })
            ->get();

        foreach ($recurringLeaves as $leave) {
            foreach ($leave->generateOccurrences($from, $to) as $occurrence) {
                if (($occurrence['date'] ?? null) !== $date) {
                    continue;
                }

                if (($occurrence['is_cancelled'] ?? false) === true) {
                    continue;
                }

                if (($occurrence['event_kind'] ?? null) === 'home_office') {
                    continue;
                }

                $employeeId = (int) $leave->employee_id;

                if (!isset($map[$employeeId])) {
                    continue;
                }

                $removeOkBadge($map[$employeeId]);

                $map[$employeeId]['available'] = false;
                $map[$employeeId]['reasons'][] = 'Wiederkehrende Abwesenheit';
                $map[$employeeId]['badges'][] = [
                    'label' => $occurrence['title'] ?? 'Wiederkehrende Abwesenheit',
                    'level' => 'danger',
                ];
            }
        }

        return $map;
    }

    protected function buildEmployeeAppointmentMap(array $employeeIds, string $date): array
    {
        $date = Carbon::parse($date)->toDateString();

        $map = [];

        foreach ($employeeIds as $employeeId) {
            $map[(int) $employeeId] = [];
        }

        $rows = DB::table('main_appointment_employees as mae')
            ->join('main_appointments as ma', 'ma.id', '=', 'mae.appointment_id')
            ->whereIn('mae.employee_id', $employeeIds)
            ->whereNull('ma.deleted_at')
            ->whereDate('ma.start_date', '<=', $date)
            ->whereDate('ma.end_date', '>=', $date)
            ->select([
                'mae.employee_id',
                'ma.id',
                'ma.name',
                'ma.start_date',
                'ma.end_date',
                'ma.start_time',
                'ma.end_time',
            ])
            ->get();

        foreach ($rows as $row) {
            $employeeId = (int) $row->employee_id;

            $start = Carbon::parse($date . ' ' . ($row->start_time ?: '00:00'));
            $end = Carbon::parse($date . ' ' . ($row->end_time ?: '23:59'));

            if ($end->lessThanOrEqualTo($start)) {
                $end = $start->copy()->addHour();
            }

            $map[$employeeId][] = [
                'id' => $row->id,
                'name' => $row->name,
                'start' => $start,
                'end' => $end,
            ];
        }

        return $map;
    }

    protected function generateAvailableSlots(array $employeeIds, string $date, int $durationMinutes, array $dayMap, array $appointmentsMap): array
    {
        $date = Carbon::parse($date)->toDateString();
        $durationMinutes = max(15, min(480, $durationMinutes));

        $windowStart = Carbon::parse($date . ' 08:00');
        $windowEnd = Carbon::parse($date . ' 17:00');

        $cursor = $windowStart->copy();
        $slots = [];

        while ($cursor->copy()->addMinutes($durationMinutes)->lessThanOrEqualTo($windowEnd)) {
            $slotStart = $cursor->copy();
            $slotEnd = $cursor->copy()->addMinutes($durationMinutes);

            $ok = true;

            foreach ($employeeIds as $employeeId) {
                $employeeId = (int) $employeeId;

                if (!($dayMap[$employeeId]['available'] ?? true)) {
                    $ok = false;
                    break;
                }

                foreach (($appointmentsMap[$employeeId] ?? []) as $appointment) {
                    if ($this->timeRangesOverlap($slotStart, $slotEnd, $appointment['start'], $appointment['end'])) {
                        $ok = false;
                        break 2;
                    }
                }
            }

            if ($ok) {
                $slots[] = [
                    'start' => $slotStart->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Required: 20 minutes space between each offered slot
            |--------------------------------------------------------------------------
            */
            $cursor = $slotEnd->copy()->addMinutes(20);
        }

        return $slots;
    }

    protected function timeRangesOverlap(Carbon $aStart, Carbon $aEnd, Carbon $bStart, Carbon $bEnd): bool
    {
        return $aStart->lt($bEnd) && $aEnd->gt($bStart);
    }

    /*
    |--------------------------------------------------------------------------
    | General helpers
    |--------------------------------------------------------------------------
    */
    private function editCreateData(?Problem $problem = null): array
    {
        $data = [];

        if ($problem) {
            $problem->loadMissing([
                'customer',
                'alternative',
                'product',
                'employees',
                'errors',
                'leadStage',
                'leadStageSubStage',
                'leadProductList',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Main ticket object
        |--------------------------------------------------------------------------
        | New Blade uses $problem. Old edit Blade used $problems, so both are passed
        | to stop "Undefined variable $problems" immediately while you replace the
        | Blade with the new create-style layout.
        */
        $data['problem'] = $problem;
        $data['problems'] = $problem;
        $data['ticket'] = $problem;

        $data['customer'] = DB::table('new_leads')
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->limit(500)
            ->get();

        $data['product'] = ArticleGroup::query()
            ->orderBy('article_group')
            ->get();

        $data['responsible'] = Employee::query()
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['employee_id'] = auth()->user()->name;

        $data['error_code'] = DB::table('errors')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'errors.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'errors.brand_id')
            ->select(
                'errors.id',
                'errors.id as error_id',
                'errors.article_name',
                'errors.error_code',
                'brands.name as brand_name',
                'article_groups.article_group',
                'errors.problem_types',
                'errors.reason',
                'errors.solution'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Backward compatible variables for the old edit Blade
        |--------------------------------------------------------------------------
        */
        $data['error_codes'] = $problem
            ? DB::table('error_problem')
                ->join('errors', 'errors.id', '=', 'error_problem.error_id')
                ->where('error_problem.problem_id', $problem->id)
                ->select(
                    'errors.id',
                    'errors.id as error_id',
                    'errors.error_code',
                    'errors.problem_types',
                    'errors.reason',
                    'errors.solution'
                )
                ->get()
            : collect();

        $data['responsibles'] = $problem
            ? DB::table('employee_problem')
                ->join('employees', 'employees.id', '=', 'employee_problem.employee_id')
                ->where('employee_problem.problem_id', $problem->id)
                ->select(
                    'employee_problem.employee_id',
                    'employees.name as rname',
                    'employees.lastname as rlastname',
                    'employees.image as rimage'
                )
                ->get()
            : collect();

        [$leadStages, $leadStageOptions] = $this->leadStageFormOptions();

        $data['leadStages'] = $leadStages;
        $data['leadStageOptions'] = $leadStageOptions;

        return $data;
    }

    private function archiveStatuses(): array
    {
        return ['end', 'beendet', 'ended', 'done'];
    }

    private function activeStatuses(): array
    {
        return ['offen', 'open', 'process', 'in_bearbeitung'];
    }

    private function normalizeTicketStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));

        return match ($status) {
            'open' => 'offen',
            'in_bearbeitung' => 'process',
            'done', 'beendet', 'ended' => 'end',
            default => $status ?: 'offen',
        };
    }

    private function errorTypeLabel(?string $type): string
    {
        $type = strtolower(trim((string) $type));

        return match ($type) {
            'complaint' => 'Reklamation',
            'emergency_service' => 'Notdienst',
            'repair' => 'Reparatur',
            'maintenance' => 'Wartung',
            'malfunction' => 'Störung',
            'installation' => 'Installation',
            'configuration_error' => 'Konfiguration',
            'system_outage' => 'Systemausfall',
            'security_issue' => 'Sicherheitsproblem',
            'user_error' => 'Bedienungsfehler',
            'network_problem' => 'Netzwerkfehler',
            'software_bug' => 'Softwarefehler',
            'hardware_defect' => 'Hardwarefehler',
            'spare_part_request' => 'Ersatzteilanfrage',
            'timeout' => 'Zeitüberschreitung',
            'communication_failure' => 'Kommunikationsproblem',
            'power_outage' => 'Energieausfall',
            'update_failure' => 'Updatefehler',
            'access_issue' => 'Zugriffsproblem',
            'other' => 'Sonstiges',
            default => $type ? ucfirst(str_replace(['_', '-'], ' ', $type)) : 'Kein Fehlertyp',
        };
    }

    private function translateProblemTypeToGerman(?string $type): string
    {
        return $this->errorTypeLabel($type);
    }

    private function stageLabel(?string $stage): string
    {
        $stage = strtolower(trim((string) $stage));

        $translations = [
            'lead' => 'Lead',
            'plan' => 'Planung',
            'offer' => 'Angebot',
            'deal' => 'Auftrag',
            'project' => 'Projekt',
            'completed' => 'Abgeschlossen',
            'junk' => 'Müll',
            'ticket' => 'Ticket',
            'open' => 'Interessent',
        ];

        return $translations[$stage] ?? ucfirst((string) $stage);
    }

    private function generateTicketNo(?Carbon $date = null): string
    {
        $date = $date ?: now();
        $prefix = 'T-' . $date->format('Ym') . '-';

        $existingNumbers = Problem::withTrashed()
            ->where('ticket_no', 'like', $prefix . '%')
            ->lockForUpdate()
            ->pluck('ticket_no')
            ->map(function ($ticketNo) use ($prefix) {
                $ticketNo = (string) $ticketNo;

                if (!str_starts_with($ticketNo, $prefix)) {
                    return 0;
                }

                $number = substr($ticketNo, strlen($prefix));

                return ctype_digit($number) ? (int) $number : 0;
            });

        $next = max(0, (int) $existingNumbers->max()) + 1;

        do {
            $ticketNo = $prefix . str_pad((string) $next, 2, '0', STR_PAD_LEFT);
            $next++;
        } while (Problem::withTrashed()->where('ticket_no', $ticketNo)->exists());

        return $ticketNo;
    }

    private function ticketNoHasCurrentFormat(?string $ticketNo): bool
    {
        return is_string($ticketNo) && preg_match('/^T-\d{6}-\d{2,}$/', $ticketNo) === 1;
    }

    private function cleanIntegerArray($values): array
    {
        return collect((array) $values)
            ->map(fn($value) => $this->nullableInteger($value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function validationResponse(Request $request, $validator)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        return back()
            ->withErrors($validator)
            ->withInput();
    }
}
