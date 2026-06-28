<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\Error;
use App\Models\LeadStage;
use App\Models\Problem;
use App\Models\TicketTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TicketTaskController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | IMPORTANT
    |--------------------------------------------------------------------------
    | ticket_tasks table uses ticket_id, not problem_id.
    | This controller accepts old requests with problem_id, but internally it
    | always saves/queries ticket_id.
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $this->normalizeTicketId($request);

        $tasks = TicketTask::query()
            ->with([
                'ticket.customer',
                'employee',
                'error',
                'appointment',
                'leadProductList',
                'leadStage',
                'leadStageSubStage',
            ])
            ->when($request->filled('ticket_id'), fn($q) => $q->where('ticket_id', $request->integer('ticket_id')))
            ->when($request->filled('employee_id'), fn($q) => $q->where('employee_id', $request->integer('employee_id')))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('priority'), fn($q) => $q->where('priority', $request->input('priority')))
            ->when($request->filled('lead_stage_id'), fn($q) => $q->where('lead_stage_id', $request->integer('lead_stage_id')))
            ->when($request->filled('lead_stage_sub_stage_id'), fn($q) => $q->where('lead_stage_sub_stage_id', $request->integer('lead_stage_sub_stage_id')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim((string) $request->input('search'));

                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('solution', 'like', "%{$search}%")
                        ->orWhereHas('ticket', fn($ticket) => $ticket->where('ticket_no', 'like', "%{$search}%"));
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.ticket_tasks.index', array_merge($this->formData(), [
            'tasks' => $tasks,
        ]));
    }

    /*
    |--------------------------------------------------------------------------
    | Load tasks for ticket profile
    |--------------------------------------------------------------------------
    | Route example:
    | GET /ticket-tasks/load/{ticket}
    |--------------------------------------------------------------------------
    */
    public function load(Request $request, int $ticket): JsonResponse
    {
        $problem = Problem::findOrFail($ticket);

        $tasks = TicketTask::query()
            ->with([
                'employee:id,name,lastname,image',
                'error',
                'appointment:id,name,start_date,start_time,end_time',
                'leadStage:id,name,color',
                'leadStageSubStage:id,name,color',
            ])
            ->where('ticket_id', $problem->id)
            ->orderByRaw('is_done ASC')
            ->orderByRaw('COALESCE(due_date, created_at) ASC')
            ->get()
            ->map(fn(TicketTask $task) => $this->taskJson($task));

        return response()->json([
            'success' => true,
            'tasks' => $tasks,
            'count' => $tasks->count(),
            'done' => $tasks->where('is_done', true)->count(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->normalizeTicketId($request);

        $problem = null;

        if ($request->filled('ticket_id')) {
            $problem = Problem::with(['leadStage', 'leadStageSubStage'])->find($request->integer('ticket_id'));
        }

        return view('admin.ticket_tasks.create', array_merge($this->formData($problem), [
            'problem' => $problem,
            'ticketTask' => new TicketTask(),
        ]));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $this->normalizeTicketId($request);
        $this->normalizeTicketStageInputs($request);
        $this->normalizeDescriptionInput($request);

        $validated = $request->validate($this->taskRules());

        $problem = Problem::findOrFail($validated['ticket_id']);

        $stageContext = $this->resolveTicketStageContext($request, $problem);
        $stageContext = $this->inheritStageFromProblem($stageContext, $problem);

        DB::beginTransaction();

        try {
            $task = TicketTask::create($this->taskPayload($validated, $stageContext));

            DB::commit();

            $task->load([
                'ticket',
                'employee',
                'error',
                'appointment',
                'leadStage',
                'leadStageSubStage',
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket-Aufgabe wurde gespeichert.',
                    'task' => $this->taskJson($task),
                ]);
            }

            return redirect()
                ->route('ticketTasks.load', $task->ticket_id)
                ->with('success', 'Ticket-Aufgabe wurde gespeichert.');
        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket-Aufgabe konnte nicht gespeichert werden.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Ticket-Aufgabe konnte nicht gespeichert werden: ' . $e->getMessage());
        }
    }

    public function show(TicketTask $ticketTask): View
    {
        $ticketTask->load([
            'ticket.customer',
            'ticket.alternative',
            'ticket.product',
            'employee',
            'error',
            'appointment',
            'leadProductList',
            'leadStage',
            'leadStageSubStage',
        ]);

        return view('admin.ticket_tasks.show', array_merge($this->formData($ticketTask->ticket), [
            'ticketTask' => $ticketTask,
            'problem' => $ticketTask->ticket,
            'stageContext' => $this->buildTaskStageContext($ticketTask),
        ]));
    }

    public function edit(TicketTask $ticketTask): View
    {
        $ticketTask->load([
            'ticket',
            'employee',
            'error',
            'appointment',
            'leadStage',
            'leadStageSubStage',
        ]);

        return view('admin.ticket_tasks.edit', array_merge($this->formData($ticketTask->ticket), [
            'ticketTask' => $ticketTask,
            'problem' => $ticketTask->ticket,
            'stageContext' => $this->buildTaskStageContext($ticketTask),
        ]));
    }

    public function update(Request $request, TicketTask $ticketTask): RedirectResponse|JsonResponse
    {
        $this->normalizeTicketId($request);
        $this->normalizeTicketStageInputs($request);
        $this->normalizeDescriptionInput($request);

        $validated = $request->validate($this->taskRules($ticketTask));

        $problem = Problem::findOrFail($validated['ticket_id']);

        $stageContext = $this->resolveTicketStageContext($request, $problem);
        $stageContext = $this->inheritStageFromProblem($stageContext, $problem);

        DB::beginTransaction();

        try {
            $ticketTask->update($this->taskPayload($validated, $stageContext));

            DB::commit();

            $ticketTask->load([
                'ticket',
                'employee',
                'error',
                'appointment',
                'leadStage',
                'leadStageSubStage',
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket-Aufgabe wurde aktualisiert.',
                    'task' => $this->taskJson($ticketTask),
                ]);
            }

            return redirect()
                ->route('ticketTasks.load', $ticketTask->ticket_id)
                ->with('success', 'Ticket-Aufgabe wurde aktualisiert.');
        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket-Aufgabe konnte nicht aktualisiert werden.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Ticket-Aufgabe konnte nicht aktualisiert werden: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, TicketTask $ticketTask): RedirectResponse|JsonResponse
    {
        try {
            $ticketId = $ticketTask->ticket_id;
            $ticketTask->delete();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ticket-Aufgabe wurde gelöscht.',
                ]);
            }

            return redirect()
                ->route('ticketTasks.load', $ticketId)
                ->with('success', 'Ticket-Aufgabe wurde gelöscht.');
        } catch (\Throwable $e) {
            report($e);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket-Aufgabe konnte nicht gelöscht werden.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Ticket-Aufgabe konnte nicht gelöscht werden.');
        }
    }

    public function updateStatus(Request $request, TicketTask $ticketTask): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|max:255',
        ]);

        $ticketTask->update([
            'status' => $validated['status'],
            'is_done' => in_array($validated['status'], ['done', 'erledigt', 'closed', 'geschlossen'], true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status wurde aktualisiert.',
            'task' => $this->taskJson($ticketTask->fresh(['leadStage', 'leadStageSubStage', 'employee', 'error'])),
        ]);
    }

    public function toggleDone(Request $request, TicketTask $ticketTask): JsonResponse
    {
        $newDone = !$ticketTask->is_done;

        $ticketTask->update([
            'is_done' => $newDone,
            'status' => $newDone ? 'done' : 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Aufgabenstatus wurde geändert.',
            'task' => $this->taskJson($ticketTask->fresh(['leadStage', 'leadStageSubStage', 'employee', 'error'])),
        ]);
    }

    public function ticketLeadStageContext(Request $request): JsonResponse
    {
        $this->normalizeTicketId($request);
        $this->normalizeTicketStageInputs($request);

        $problem = null;

        if ($request->filled('ticket_id')) {
            $problem = Problem::find($request->integer('ticket_id'));
        }

        $context = $this->resolveTicketStageContext($request, $problem);

        if ($problem) {
            $context = $this->inheritStageFromProblem($context, $problem);
        }

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

    protected function taskRules(?TicketTask $ticketTask = null): array
    {
        return [
            'ticket_id' => 'required|integer|exists:problems,id',
            'employee_id' => 'nullable|integer|exists:employees,id',
            'error_id' => 'nullable|integer|exists:errors,id',
            'appointment_id' => 'nullable|integer|exists:main_appointments,id',

            'lead_product_list_id' => 'nullable|integer|exists:lead_product_lists,id',
            'lead_stage_id' => 'nullable|integer|exists:lead_stages,id',
            'lead_stage_sub_stage_id' => 'nullable|integer|exists:lead_stage_sub_stages,id',

            'title' => 'required|string|max:255',
            'priority' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'difference' => 'nullable|string|max:255',
            'solution' => 'nullable|string',
            'description' => 'nullable|string',
            'is_done' => 'nullable|boolean',
            'teams' => 'nullable',
        ];
    }

    protected function taskPayload(array $validated, array $stageContext): array
    {
        $payload = Arr::only($validated, [
            'ticket_id',
            'employee_id',
            'error_id',
            'appointment_id',
            'title',
            'priority',
            'status',
            'start_date',
            'due_date',
            'difference',
            'solution',
            'is_done',
            'teams',
        ]);

        $payload['lead_product_list_id'] = $stageContext['lead_product_list_id'];
        $payload['lead_stage_id'] = $stageContext['lead_stage_id'];
        $payload['lead_stage_sub_stage_id'] = $stageContext['lead_stage_sub_stage_id'];

        $payload['status'] = $payload['status'] ?? 'open';
        $payload['is_done'] = filter_var($payload['is_done'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $payload['teams'] = $this->normalizeTeams($payload['teams'] ?? null);

        return $payload;
    }

    /*
    |--------------------------------------------------------------------------
    | Compatibility helpers
    |--------------------------------------------------------------------------
    */
    protected function normalizeTicketId(Request $request): void
    {
        /*
        |--------------------------------------------------------------------------
        | OLD frontend sometimes sends problem_id.
        | DB column is ticket_tasks.ticket_id.
        |--------------------------------------------------------------------------
        */
        $ticketId = $this->nullableInteger($request->input('ticket_id'));

        if (!$ticketId) {
            $ticketId = $this->nullableInteger($request->input('problem_id'));
        }

        if ($ticketId) {
            $request->merge([
                'ticket_id' => $ticketId,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Do not query ticket_tasks.problem_id anywhere.
        |--------------------------------------------------------------------------
        */
    }

    protected function normalizeDescriptionInput(Request $request): void
    {
        if (!$request->filled('solution') && $request->filled('description')) {
            $request->merge([
                'solution' => $request->input('description'),
            ]);
        }
    }

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
        $request->merge([
            'ticket_id' => $this->nullableInteger($request->input('ticket_id')),
            'customer_id' => $this->nullableInteger($request->input('customer_id')),
            'alternative_id' => $this->nullableInteger($request->input('alternative_id')),
            'product_id' => $this->nullableInteger($request->input('product_id')),
            'lead_product_list_id' => $this->nullableInteger($request->input('lead_product_list_id')),
            'lead_stage_id' => $this->nullableInteger($request->input('lead_stage_id')),
            'lead_stage_sub_stage_id' => $this->nullableInteger($request->input('lead_stage_sub_stage_id')),
        ]);
    }

    protected function resolveTicketStageContext(Request $request, ?Problem $problem = null): array
    {
        $leadProductListId = $this->nullableInteger($request->input('lead_product_list_id'));
        $leadStageId = $this->nullableInteger($request->input('lead_stage_id'));
        $leadSubStageId = $this->nullableInteger($request->input('lead_stage_sub_stage_id'));

        if (!$leadProductListId) {
            $customerId = $this->nullableInteger($request->input('customer_id')) ?: ($problem->customer_id ?? null);
            $alternativeId = $this->nullableInteger($request->input('alternative_id')) ?: ($problem->alternative_id ?? null);
            $productId = $this->nullableInteger($request->input('product_id')) ?: ($problem->product_id ?? null);

            if ($customerId && $alternativeId && $productId && Schema::hasTable('lead_product_lists')) {
                $leadProductListId = DB::table('lead_product_lists')
                    ->where('customer_id', $customerId)
                    ->where('alternative_id', $alternativeId)
                    ->where('product_id', $productId)
                    ->value('id');
            }
        }

        if ($leadProductListId && (!$leadStageId || !$leadSubStageId) && Schema::hasTable('lead_product_lists')) {
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

        if (!$leadStageId && $leadSubStageId && Schema::hasTable('lead_stage_sub_stages')) {
            $leadStageId = DB::table('lead_stage_sub_stages')
                ->where('id', $leadSubStageId)
                ->value('lead_stage_id');
        }

        if ($leadStageId && $leadSubStageId && Schema::hasTable('lead_stage_sub_stages')) {
            $validSubStage = DB::table('lead_stage_sub_stages')
                ->where('id', $leadSubStageId)
                ->where('lead_stage_id', $leadStageId)
                ->exists();

            if (!$validSubStage) {
                $leadSubStageId = null;
            }
        }

        return [
            'lead_product_list_id' => $leadProductListId ? (int) $leadProductListId : null,
            'lead_stage_id' => $leadStageId ? (int) $leadStageId : null,
            'lead_stage_sub_stage_id' => $leadSubStageId ? (int) $leadSubStageId : null,
        ];
    }

    protected function inheritStageFromProblem(array $stageContext, Problem $problem): array
    {
        if (empty($stageContext['lead_product_list_id'])) {
            $stageContext['lead_product_list_id'] = $problem->lead_product_list_id ? (int) $problem->lead_product_list_id : null;
        }

        if (empty($stageContext['lead_stage_id'])) {
            $stageContext['lead_stage_id'] = $problem->lead_stage_id ? (int) $problem->lead_stage_id : null;
        }

        if (empty($stageContext['lead_stage_sub_stage_id'])) {
            $stageContext['lead_stage_sub_stage_id'] = $problem->lead_stage_sub_stage_id ? (int) $problem->lead_stage_sub_stage_id : null;
        }

        return $stageContext;
    }

    protected function buildTaskStageContext(TicketTask $ticketTask): array
    {
        $ticketTask->loadMissing(['leadStage', 'leadStageSubStage', 'ticket.leadStage', 'ticket.leadStageSubStage']);

        $problem = $ticketTask->ticket;

        return [
            'lead_product_list_id' => $ticketTask->lead_product_list_id ?: $problem?->lead_product_list_id,
            'lead_stage_id' => $ticketTask->lead_stage_id ?: $problem?->lead_stage_id,
            'lead_stage_name' => $ticketTask->leadStage?->name ?: $problem?->leadStage?->name,
            'lead_stage_color' => $ticketTask->leadStage?->color ?: $problem?->leadStage?->color ?: '#74b2d4',
            'lead_stage_sub_stage_id' => $ticketTask->lead_stage_sub_stage_id ?: $problem?->lead_stage_sub_stage_id,
            'lead_stage_sub_stage_name' => $ticketTask->leadStageSubStage?->name ?: $problem?->leadStageSubStage?->name,
            'lead_stage_sub_stage_color' => $ticketTask->leadStageSubStage?->color ?: $problem?->leadStageSubStage?->color ?: '#93c21c',
        ];
    }

    protected function formData(?Problem $problem = null): array
    {
        return [
            'problems' => Schema::hasTable('problems')
                ? Problem::query()->latest('id')->limit(500)->get(['id', 'ticket_no', 'problem', 'customer_id'])
                : collect(),

            'employees' => Schema::hasTable('employees')
                ? Employee::query()->where('status', 'Active')->orderBy('name')->get(['id', 'name', 'lastname', 'image'])
                : collect(),

            'errors' => Schema::hasTable('errors')
                ? Error::query()->orderBy('id', 'desc')->get()
                : collect(),

            'leadStages' => Schema::hasTable('lead_stages')
                ? LeadStage::query()
                    ->when(Schema::hasColumn('lead_stages', 'is_active'), fn($q) => $q->where('is_active', true))
                    ->when(Schema::hasColumn('lead_stages', 'sort_order'), fn($q) => $q->orderBy('sort_order'))
                    ->orderBy('id')
                    ->get()
                : collect(),
        ];
    }

    protected function normalizeTeams($teams): ?string
    {
        if ($teams === null || $teams === '') {
            return null;
        }

        if (is_string($teams)) {
            $decoded = json_decode($teams, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return json_encode($decoded, JSON_UNESCAPED_UNICODE);
            }

            return $teams;
        }

        return json_encode($teams, JSON_UNESCAPED_UNICODE);
    }

    protected function taskJson(TicketTask $task): array
    {
        return [
            'id' => $task->id,
            'ticket_id' => $task->ticket_id,
            'employee_id' => $task->employee_id,
            'employee_name' => trim(($task->employee?->name ?? '') . ' ' . ($task->employee?->lastname ?? '')),
            'error_id' => $task->error_id,
            'appointment_id' => $task->appointment_id,
            'title' => $task->title,
            'priority' => $task->priority ?: 'normal',
            'status' => $task->status ?: 'open',
            'start_date' => optional($task->start_date)->format('Y-m-d'),
            'due_date' => optional($task->due_date)->format('Y-m-d'),
            'difference' => $task->difference,
            'solution' => $task->solution,
            'is_done' => (bool) $task->is_done,
            'lead_product_list_id' => $task->lead_product_list_id,
            'lead_stage_id' => $task->lead_stage_id,
            'lead_stage_name' => $task->leadStage?->name,
            'lead_stage_color' => $task->leadStage?->color,
            'lead_stage_sub_stage_id' => $task->lead_stage_sub_stage_id,
            'lead_stage_sub_stage_name' => $task->leadStageSubStage?->name,
            'lead_stage_sub_stage_color' => $task->leadStageSubStage?->color,
        ];
    }
}
