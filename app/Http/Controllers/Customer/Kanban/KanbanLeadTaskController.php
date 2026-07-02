<?php

namespace App\Http\Controllers\Customer\Kanban;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\KanbanLeadTask;
use App\Models\LeadProductList;
use App\Models\LeadStage;
use App\Models\PhaseActivities;
use App\Models\TaskPhase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Models\PersonalTask;
use App\Models\PersonalTaskKey;
use App\Models\MainAppointment;

class KanbanLeadTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function context(Request $request, LeadProductList $leadProduct): JsonResponse
    {
        $leadProduct->load([
            'customer:id,name,lastname,firma',
            'alternative:id,street,postcode,city',
            'product:id,article_group,initial',
            'leadStageSubStage:id,lead_stage_id,name,key,color',
        ]);

        $stageId = $this->resolveLeadStageId($leadProduct);
        $subStageId = $leadProduct->lead_stage_sub_stage_id;

        $templates = $this->loadTemplateTasks(
            productId: (int) $leadProduct->product_id,
            leadStageId: $stageId,
            leadSubStageId: $subStageId
        );

        $savedTasks = KanbanLeadTask::query()
            ->with([
                'performer:id,name,lastname,image',
                'employees:id,name,lastname,image',
                'taskPhase:id,phase_name',
                'phaseActivity:id,title',
            ])
            ->where('lead_product_list_id', $leadProduct->id)
            ->whereNull('deleted_at')
            ->orderByRaw("
                CASE status
                    WHEN 'open' THEN 1
                    WHEN 'scheduled' THEN 2
                    WHEN 'in_progress' THEN 3
                    WHEN 'done' THEN 4
                    ELSE 5
                END
            ")
            ->orderByRaw('COALESCE(planned_start_at, created_at) ASC')
            ->get()
            ->map(fn($task) => $this->taskCard($task))
            ->values();

        $employees = Employee::query()
            ->select('id', 'name', 'lastname', 'image')
            ->where('status', 'Active')
            ->orderBy('lastname')
            ->orderBy('name')
            ->get()
            ->map(fn($employee) => [
                'id' => (int) $employee->id,
                'text' => trim(($employee->lastname ?? '') . ' ' . ($employee->name ?? '')) ?: ('#' . $employee->id),
                'name' => $employee->name,
                'lastname' => $employee->lastname,
                'image' => $employee->image,
            ])
            ->values();

        return response()->json([
            'success' => true,
            'context' => [
                'lead_product_list_id' => (int) $leadProduct->id,
                'customer_id' => (int) $leadProduct->customer_id,
                'alternative_id' => $leadProduct->alternative_id ? (int) $leadProduct->alternative_id : null,
                'product_id' => (int) $leadProduct->product_id,
                'lead_stage_id' => $stageId,
                'lead_sub_stage_id' => $subStageId ? (int) $subStageId : null,
                'stage_label' => $this->stageLabel($stageId),
                'sub_stage_label' => optional($leadProduct->leadStageSubStage)->name,
                'customer_name' => trim(
                    (($leadProduct->customer->lastname ?? '') . ' ' . ($leadProduct->customer->name ?? ''))
                ) ?: ($leadProduct->customer->firma ?? ('#' . $leadProduct->customer_id)),
                'product_name' => $leadProduct->product->article_group ?? ('#' . $leadProduct->product_id),
            ],
            'templates' => $templates,
            'tasks' => $savedTasks,
            'field_progress' => $this->montageFieldProgress($leadProduct),
            'employees' => $employees,
            'auth_employee_id' => (int) auth()->user()->name,
        ]);
    }

    /**
     * Montage-Fortschritt nach ANZAHL (bewusst NICHT zeitgewichtet — Weiche 6:
     * duration-Daten sind unbrauchbar). Quelle: planner_items (Feld-Ausfuehrung,
     * Nuriva-angebunden) des Montage-Plans dieses Gewerks — NICHT kanban_lead_tasks.
     * Nur source_type='phase_activity' (die Arbeits-Schritte); cancel-Klasse raus.
     * Additiv: die tasks-Liste (kanban) bleibt unveraendert.
     */
    private function montageFieldProgress(LeadProductList $leadProduct): array
    {
        $empty = ['gesamt' => 0, 'erledigt' => 0, 'offen' => 0, 'percent' => 0];

        if (!Schema::hasTable('planner_plans') || !Schema::hasTable('planner_items')) {
            return $empty;
        }

        $montagePlanIds = DB::table('planner_plans')
            ->where('project_id', $leadProduct->id)   // planner_plans.project_id -> lead_product_lists.id
            ->where('stage', 'montage')
            ->pluck('id');

        if ($montagePlanIds->isEmpty()) {
            return $empty;                             // Gewerk ohne Montage-Plan -> 0 %, kein Crash
        }

        $query = DB::table('planner_items')
            ->whereIn('plan_id', $montagePlanIds)
            ->where('source_type', 'phase_activity');

        if (Schema::hasColumn('planner_items', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $gesamt = 0;
        $erledigt = 0;

        foreach ($query->pluck('status') as $rawStatus) {
            $normalized = $this->normalizePlannerItemStatus((string) $rawStatus);

            if ($normalized === 'cancelled') {
                continue;                              // wie pmoNormalizePlannerStatus: cancel-Klasse nicht in gesamt
            }

            $gesamt++;

            if ($normalized === 'done') {
                $erledigt++;
            }
        }

        $offen = max(0, $gesamt - $erledigt);
        $percent = $gesamt > 0 ? (int) round(($erledigt / $gesamt) * 100) : 0;

        return compact('gesamt', 'erledigt', 'offen', 'percent');
    }

    /**
     * Spiegelt die done-/cancel-Aliasse aus PlannerPlanController::pmoNormalizePlannerStatus,
     * damit ein roh geschriebener Status (z.B. 'storniert') die Zaehlung nicht verfaelscht.
     */
    private function normalizePlannerItemStatus(string $status): string
    {
        $status = str_replace(['-', ' '], '_', mb_strtolower(trim($status)));

        return match ($status) {
            'completed', 'complete', 'finished', 'finish', 'closed', 'close', 'ended', 'end',
            'done', 'erledigt', 'beendet', 'geschlossen' => 'done',

            'cancel', 'canceled', 'cancelled', 'storniert', 'junk' => 'cancelled',

            default => 'open',
        };
    }


    /**
     * Bulk summary endpoint for Kanban cards/list rows.
     *
     * Loads compact next-step data for all visible lead_product_lists in one request,
     * so cards do not need to wait until the task modal is opened.
     */
    public function summaries(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lead_product_list_ids' => ['required', 'array'],
            'lead_product_list_ids.*' => ['integer', 'exists:lead_product_lists,id'],
        ]);

        $ids = collect($data['lead_product_list_ids'])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return response()->json([
                'success' => true,
                'summaries' => [],
            ]);
        }

        $leadProducts = LeadProductList::query()
            ->with([
                'customer:id,name,lastname,firma',
                'product:id,article_group,initial',
                'leadStageSubStage:id,lead_stage_id,name,key,color',
            ])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $savedByLeadProduct = KanbanLeadTask::query()
            ->with([
                'performer:id,name,lastname,image',
                'employees:id,name,lastname,image',
                'taskPhase:id,phase_name',
                'phaseActivity:id,title',
            ])
            ->whereIn('lead_product_list_id', $ids)
            ->whereNull('deleted_at')
            ->orderByRaw("
                CASE status
                    WHEN 'in_progress' THEN 1
                    WHEN 'scheduled' THEN 2
                    WHEN 'open' THEN 3
                    WHEN 'done' THEN 4
                    ELSE 5
                END
            ")
            ->orderByRaw('COALESCE(planned_start_at, created_at) ASC')
            ->get()
            ->groupBy('lead_product_list_id');

        $summaries = [];

        foreach ($ids as $id) {
            $leadProduct = $leadProducts->get($id);

            if (!$leadProduct) {
                continue;
            }

            $stageId = $this->resolveLeadStageId($leadProduct);
            $subStageId = $leadProduct->lead_stage_sub_stage_id;

            $templates = $this->loadTemplateTasks(
                productId: (int) $leadProduct->product_id,
                leadStageId: $stageId,
                leadSubStageId: $subStageId
            );

            $tasks = $savedByLeadProduct
                ->get($id, collect())
                ->map(fn($task) => $this->taskCard($task))
                ->values();

            $summaries[$id] = $this->summaryPayload($leadProduct, $tasks, $templates, $stageId, $subStageId);
        }

        return response()->json([
            'success' => true,
            'summaries' => $summaries,
        ]);
    }

    private function summaryPayload(LeadProductList $leadProduct, $tasks, array $templates, ?int $stageId, ?int $subStageId): array
    {
        $taskCollection = collect($tasks);

        $doneTasks = $taskCollection
            ->filter(fn($task) => strtolower((string) ($task['status'] ?? '')) === 'done')
            ->values();

        $openTasks = $taskCollection
            ->reject(fn($task) => in_array(strtolower((string) ($task['status'] ?? '')), ['done', 'cancelled'], true))
            ->values();

        $previous = $doneTasks
            ->sortByDesc(fn($task) => $task['done_at'] ?? $task['planned_end_at'] ?? $task['planned_start_at'] ?? '')
            ->first();

        $current = $openTasks->first();

        $flatTemplates = collect($templates)->flatMap(function ($phase) {
            $activities = collect($phase['activities'] ?? []);

            if ($activities->isNotEmpty()) {
                return $activities->map(function ($activity) use ($phase) {
                    return [
                        'title' => $activity['title'] ?? ($phase['phase_name'] ?? null),
                        'description' => $activity['description'] ?? ($phase['description'] ?? null),
                        'estimated_minutes' => $activity['estimated_minutes'] ?? null,
                        'photo_required' => (bool) ($activity['photo_required'] ?? false),
                    ];
                });
            }

            return [
                [
                    'title' => $phase['phase_name'] ?? null,
                    'description' => $phase['description'] ?? null,
                    'estimated_minutes' => null,
                    'photo_required' => false,
                ]
            ];
        })->filter(fn($row) => !blank($row['title'] ?? null))->values();

        $templateCurrent = $flatTemplates->first();
        $templateNext = $flatTemplates->skip($openTasks->count() > 0 ? 0 : 1)->first();

        $currentTitle = $current['title'] ?? ($templateCurrent['title'] ?? null);
        $nextTitle = $openTasks->count() > 1
            ? ($openTasks->get(1)['title'] ?? null)
            : ($templateNext['title'] ?? null);

        $overdueTasks = $openTasks
            ->filter(fn($task) => (bool) ($task['is_overdue'] ?? false))
            ->values();

        $firstOverdue = $overdueTasks->first();

        return [
            'lead_product_list_id' => (int) $leadProduct->id,
            'customer_id' => (int) $leadProduct->customer_id,
            'alternative_id' => $leadProduct->alternative_id ? (int) $leadProduct->alternative_id : null,
            'product_id' => (int) $leadProduct->product_id,
            'lead_stage_id' => $stageId,
            'lead_sub_stage_id' => $subStageId ? (int) $subStageId : null,
            'stage_label' => $this->stageLabel($stageId),
            'sub_stage_label' => optional($leadProduct->leadStageSubStage)->name,
            'customer_name' => trim((($leadProduct->customer->lastname ?? '') . ' ' . ($leadProduct->customer->name ?? ''))) ?: ($leadProduct->customer->firma ?? ('#' . $leadProduct->customer_id)),
            'product_name' => $leadProduct->product->article_group ?? ('#' . $leadProduct->product_id),

            'stage_landed_at' => optional($leadProduct->updated_at)->format('Y-m-d H:i:s'),
            'previous_title' => $previous['title'] ?? null,
            'current_title' => $currentTitle,
            'next_title' => $nextTitle,
            'description' => $current['description'] ?? ($templateCurrent['description'] ?? null),

            'open_count' => $openTasks->count(),
            'done_count' => $doneTasks->count(),
            'overdue_count' => $overdueTasks->count(),
            'is_overdue' => $overdueTasks->isNotEmpty(),
            'overdue_title' => $firstOverdue['title'] ?? null,
            'overdue_at' => $firstOverdue['planned_end_at'] ?? null,

            'source' => $current ? 'saved_task' : ($templateCurrent ? 'task_phase' : null),
            'tasks' => $taskCollection->values(),
        ];
    }

    public function storeManual(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lead_product_list_id' => ['required', 'integer', 'exists:lead_product_lists,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'internal_note' => ['nullable', 'string'],
            'is_scheduled' => ['nullable', 'boolean'],
            'planned_start_at' => ['nullable', 'date'],
            'planned_end_at' => ['nullable', 'date'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'performer_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'create_personal_task' => ['nullable', 'boolean'],
            'create_appointment' => ['nullable', 'boolean'],
            'appointment_type' => ['nullable', 'string', 'max:100'],
            'appointment_contact_mode' => ['nullable', 'string', 'max:100'],
            'appointment_priority' => ['nullable', 'string', 'max:50'],
        ]);

        $leadProduct = LeadProductList::query()->findOrFail((int) $data['lead_product_list_id']);

        $task = DB::transaction(function () use ($data, $leadProduct) {
            $employeeId = $this->authEmployeeId();

            $task = KanbanLeadTask::query()->create([
                'lead_product_list_id' => $leadProduct->id,
                'customer_id' => $leadProduct->customer_id,
                'alternative_id' => $leadProduct->alternative_id,
                'product_id' => $leadProduct->product_id,
                'lead_stage_id' => $this->resolveLeadStageId($leadProduct),
                'lead_sub_stage_id' => $leadProduct->lead_stage_sub_stage_id,
                'task_phase_id' => null,
                'phase_activity_id' => null,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'internal_note' => $data['internal_note'] ?? null,
                'is_manual' => true,
                'is_scheduled' => (bool) ($data['is_scheduled'] ?? false),
                'photo_required' => false,
                'status' => !empty($data['is_scheduled']) ? 'scheduled' : 'open',
                'estimated_minutes' => $data['estimated_minutes'] ?? null,
                'planned_start_at' => $data['planned_start_at'] ?? null,
                'planned_end_at' => $data['planned_end_at'] ?? null,
                'created_by_employee_id' => $employeeId,
                'performer_employee_id' => $data['performer_employee_id'] ?? $employeeId,
                'scheduled_by_employee_id' => !empty($data['is_scheduled']) ? $employeeId : null,
            ]);

            $this->syncTaskEmployees($task, $data['employee_ids'] ?? []);
            $this->createExternalTaskLinks($task, $data, $employeeId);

            return $task->fresh([
                'performer:id,name,lastname,image',
                'employees:id,name,lastname,image',
                'taskPhase:id,phase_name',
                'phaseActivity:id,title',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Manuelle Aufgabe wurde erstellt.',
            'task' => $this->taskCard($task),
        ]);
    }

    public function storeFromTemplate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lead_product_list_id' => ['required', 'integer', 'exists:lead_product_lists,id'],
            'task_phase_id' => ['required', 'integer', 'exists:task_phases,id'],
            'phase_activity_id' => ['nullable', 'integer', 'exists:phase_activities,id'],
            'is_scheduled' => ['nullable', 'boolean'],
            'planned_start_at' => ['nullable', 'date'],
            'planned_end_at' => ['nullable', 'date'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'performer_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'internal_note' => ['nullable', 'string'],
            'create_personal_task' => ['nullable', 'boolean'],
            'create_appointment' => ['nullable', 'boolean'],
            'appointment_type' => ['nullable', 'string', 'max:100'],
            'appointment_contact_mode' => ['nullable', 'string', 'max:100'],
            'appointment_priority' => ['nullable', 'string', 'max:50'],
        ]);

        $leadProduct = LeadProductList::query()->findOrFail((int) $data['lead_product_list_id']);

        $phase = TaskPhase::query()
            ->where('id', (int) $data['task_phase_id'])
            ->where('product_id', (int) $leadProduct->product_id)
            ->firstOrFail();

        $activity = null;

        if (!empty($data['phase_activity_id'])) {
            $activity = PhaseActivities::query()
                ->where('id', (int) $data['phase_activity_id'])
                ->where('phase_id', (int) $phase->id)
                ->firstOrFail();
        }

        $task = DB::transaction(function () use ($data, $leadProduct, $phase, $activity) {
            $employeeId = $this->authEmployeeId();

            $estimatedMinutes = $data['estimated_minutes'] ?? $this->activityMinutes($activity);

            // Idempotenz (Rueckfluss Stufe 1a): pro (Gewerk, Aktivitaet) hoechstens EINE lebende Karte,
            // damit der Sync-Karten-Lookup (Stufe 1b) eindeutig ist. Soft-Delete-bewusst statt DB-Unique-Index
            // (kanban_lead_tasks nutzt SoftDeletes): bestehende - auch soft-geloeschte - Template-Karte
            // wiederverwenden bzw. restaurieren statt ein Duplikat anzulegen. Nur wenn eine Aktivitaet gesetzt
            // ist (Phasen-/Manuell-Karten mit phase_activity_id = null bleiben bewusst mehrfach moeglich).
            if ($activity) {
                $existing = KanbanLeadTask::withTrashed()
                    ->where('lead_product_list_id', $leadProduct->id)
                    ->where('phase_activity_id', $activity->id)
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    return $existing->fresh([
                        'performer:id,name,lastname,image',
                        'employees:id,name,lastname,image',
                        'taskPhase:id,phase_name',
                        'phaseActivity:id,title',
                    ]);
                }
            }

            $task = KanbanLeadTask::query()->create([
                'lead_product_list_id' => $leadProduct->id,
                'customer_id' => $leadProduct->customer_id,
                'alternative_id' => $leadProduct->alternative_id,
                'product_id' => $leadProduct->product_id,
                'lead_stage_id' => $this->resolveLeadStageId($leadProduct),
                'lead_sub_stage_id' => $leadProduct->lead_stage_sub_stage_id,
                'task_phase_id' => $phase->id,
                'phase_activity_id' => $activity?->id,
                'title' => $activity?->title ?: $phase->phase_name,
                'description' => $activity?->description ?: $phase->description,
                'internal_note' => $data['internal_note'] ?? null,
                'is_manual' => false,
                'is_scheduled' => (bool) ($data['is_scheduled'] ?? false),
                'photo_required' => ($activity?->photo === 'needed'),
                'status' => !empty($data['is_scheduled']) ? 'scheduled' : 'open',
                'estimated_minutes' => $estimatedMinutes,
                'planned_start_at' => $data['planned_start_at'] ?? null,
                'planned_end_at' => $data['planned_end_at'] ?? null,
                'created_by_employee_id' => $employeeId,
                'performer_employee_id' => $data['performer_employee_id'] ?? $employeeId,
                'scheduled_by_employee_id' => !empty($data['is_scheduled']) ? $employeeId : null,
                'meta' => [
                    'source' => 'task_phase_template',
                    'phase_name' => $phase->phase_name,
                    'activity_title' => $activity?->title,
                ],
            ]);

            $this->syncTaskEmployees($task, $data['employee_ids'] ?? []);
            $this->createExternalTaskLinks($task, $data, $employeeId);

            return $task->fresh([
                'performer:id,name,lastname,image',
                'employees:id,name,lastname,image',
                'taskPhase:id,phase_name',
                'phaseActivity:id,title',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Aufgabe wurde übernommen.',
            'task' => $this->taskCard($task),
        ]);
    }

    public function updateStatus(Request $request, KanbanLeadTask $task): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['open', 'scheduled', 'in_progress', 'done', 'cancelled'])],
            'planned_start_at' => ['nullable', 'date'],
            'planned_end_at' => ['nullable', 'date'],
            'performer_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'internal_note' => ['nullable', 'string'],
            'create_personal_task' => ['nullable', 'boolean'],
            'create_appointment' => ['nullable', 'boolean'],
            'appointment_type' => ['nullable', 'string', 'max:100'],
            'appointment_contact_mode' => ['nullable', 'string', 'max:100'],
            'appointment_priority' => ['nullable', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($task, $data) {
            $employeeId = $this->authEmployeeId();

            $task->status = $data['status'];

            if (array_key_exists('planned_start_at', $data)) {
                $task->planned_start_at = $data['planned_start_at'];
            }

            if (array_key_exists('planned_end_at', $data)) {
                $task->planned_end_at = $data['planned_end_at'];
            }

            if (!empty($data['performer_employee_id'])) {
                $task->performer_employee_id = $data['performer_employee_id'];
            }

            if (array_key_exists('internal_note', $data)) {
                $task->internal_note = $data['internal_note'];
            }

            if ($data['status'] === 'done') {
                $task->done_at = now();
                $task->done_by_employee_id = $employeeId;
            }

            if ($data['status'] === 'scheduled') {
                $task->is_scheduled = true;
                $task->scheduled_by_employee_id = $employeeId;
            }

            $task->save();

            if (array_key_exists('employee_ids', $data)) {
                $this->syncTaskEmployees($task, $data['employee_ids'] ?? []);
            }

            $this->createExternalTaskLinks($task, $data, $employeeId);
        });

        return response()->json([
            'success' => true,
            'message' => 'Aufgabe wurde aktualisiert.',
            'task' => $this->taskCard(
                $task->fresh([
                    'performer:id,name,lastname,image',
                    'employees:id,name,lastname,image',
                    'taskPhase:id,phase_name',
                    'phaseActivity:id,title',
                ])
            ),
        ]);
    }

    public function destroy(KanbanLeadTask $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aufgabe wurde gelöscht.',
        ]);
    }

    private function loadTemplateTasks(int $productId, ?int $leadStageId, ?int $leadSubStageId): array
    {
        if (!$leadStageId) {
            return [];
        }

        $phases = TaskPhase::query()
            ->with([
                'activities' => function ($query) {
                    $query->whereNull('deleted_at')
                        ->where(function ($q) {
                            $q->whereNull('status')
                                ->orWhere('status', 'Published');
                        })
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
                'section:id,phase_section',
            ])
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($leadStageId) {
                $query->where('lead_stage_id', $leadStageId)
                    ->orWhere('stage_id', $leadStageId);
            })
            ->where(function ($query) use ($leadSubStageId) {
                if ($leadSubStageId) {
                    $query->where('lead_sub_stage_id', $leadSubStageId)
                        ->orWhereNull('lead_sub_stage_id');
                } else {
                    $query->whereNull('lead_sub_stage_id');
                }
            })
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', 'Published');
            })
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        return $phases->map(function (TaskPhase $phase) {
            return [
                'id' => (int) $phase->id,
                'phase_name' => $phase->phase_name,
                'description' => $phase->description,
                'section_name' => $phase->section?->phase_section ?: $phase->section_name,
                'activities' => $phase->activities->map(function (PhaseActivities $activity) {
                    return [
                        'id' => (int) $activity->id,
                        'title' => $activity->title,
                        'description' => $activity->description,
                        'duration' => $activity->duration,
                        'duration_type' => $activity->duration_type ?: 'minutes',
                        'estimated_minutes' => $this->activityMinutes($activity),
                        'photo_required' => $activity->photo === 'needed',
                    ];
                })->values(),
            ];
        })->values()->all();
    }

    private function taskCard(KanbanLeadTask $task): array
    {
        return [
            'id' => (int) $task->id,
            'lead_product_list_id' => (int) $task->lead_product_list_id,
            'customer_id' => (int) $task->customer_id,
            'alternative_id' => $task->alternative_id ? (int) $task->alternative_id : null,
            'product_id' => (int) $task->product_id,
            'lead_stage_id' => $task->lead_stage_id ? (int) $task->lead_stage_id : null,
            'lead_sub_stage_id' => $task->lead_sub_stage_id ? (int) $task->lead_sub_stage_id : null,
            'task_phase_id' => $task->task_phase_id ? (int) $task->task_phase_id : null,
            'phase_activity_id' => $task->phase_activity_id ? (int) $task->phase_activity_id : null,
            'title' => $task->title,
            'description' => $task->description,
            'internal_note' => $task->internal_note,
            'is_manual' => (bool) $task->is_manual,
            'is_scheduled' => (bool) $task->is_scheduled,
            'photo_required' => (bool) $task->photo_required,
            'status' => $task->status,
            'estimated_minutes' => $task->estimated_minutes,
            'planned_start_at' => optional($task->planned_start_at)->format('Y-m-d\TH:i'),
            'planned_end_at' => optional($task->planned_end_at)->format('Y-m-d\TH:i'),
            'done_at' => optional($task->done_at)->format('Y-m-d H:i'),
            'is_overdue' => $task->is_overdue,
            'performer' => $task->performer ? [
                'id' => (int) $task->performer->id,
                'name' => $task->performer->name,
                'lastname' => $task->performer->lastname,
                'image' => $task->performer->image,
                'display_name' => trim(($task->performer->lastname ?? '') . ' ' . ($task->performer->name ?? '')),
            ] : null,
            'external_links' => $task->meta['external_links'] ?? null,
            'employees' => $task->employees->map(fn($employee) => [
                'id' => (int) $employee->id,
                'name' => $employee->name,
                'lastname' => $employee->lastname,
                'image' => $employee->image,
                'display_name' => trim(($employee->lastname ?? '') . ' ' . ($employee->name ?? '')),
                'role' => $employee->pivot->role ?? null,
                'status' => $employee->pivot->status ?? null,
            ])->values(),
        ];
    }


    private function createExternalTaskLinks(KanbanLeadTask $task, array $data, int $employeeId): array
    {
        $created = [
            'personal_task_id' => null,
            'appointment_id' => null,
        ];

        $personalTask = null;

        if ($this->shouldCreatePersonalTask($data)) {
            $alreadyLinkedPersonalTaskId = data_get($task->meta, 'external_links.personal_task_id');

            if ($alreadyLinkedPersonalTaskId) {
                $personalTask = PersonalTask::query()->find($alreadyLinkedPersonalTaskId);
                $created['personal_task_id'] = $personalTask?->id;
            }

            if (!$personalTask) {
                $personalTask = $this->createLinkedPersonalTask($task, $data, $employeeId);
                $created['personal_task_id'] = $personalTask->id;
            }
        }

        if ($this->shouldCreateAppointment($data)) {
            $alreadyLinkedAppointmentId = data_get($task->meta, 'external_links.appointment_id');

            if ($alreadyLinkedAppointmentId && MainAppointment::query()->whereKey($alreadyLinkedAppointmentId)->exists()) {
                $created['appointment_id'] = (int) $alreadyLinkedAppointmentId;
            } else {
                $appointment = $this->createLinkedAppointment($task, $data, $employeeId, $personalTask);
                $created['appointment_id'] = $appointment->id;
            }
        }

        if ($created['personal_task_id'] || $created['appointment_id']) {
            $meta = $task->meta ?: [];
            $existingLinks = $meta['external_links'] ?? [];

            $meta['external_links'] = array_filter([
                'personal_task_id' => $created['personal_task_id'] ?: ($existingLinks['personal_task_id'] ?? null),
                'appointment_id' => $created['appointment_id'] ?: ($existingLinks['appointment_id'] ?? null),
                'created_at' => $existingLinks['created_at'] ?? now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
                'created_by_employee_id' => $existingLinks['created_by_employee_id'] ?? $employeeId,
                'updated_by_employee_id' => $employeeId,
            ]);

            $task->meta = $meta;
            $task->save();
        }

        return $created;
    }

    private function shouldCreatePersonalTask(array $data): bool
    {
        return filter_var($data['create_personal_task'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    private function shouldCreateAppointment(array $data): bool
    {
        return filter_var($data['create_appointment'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    private function normalizeEmployeeIds(array $data, int $fallbackEmployeeId): array
    {
        $ids = collect($data['employee_ids'] ?? [])
            ->push($data['performer_employee_id'] ?? null)
            ->filter()
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            $ids->push($fallbackEmployeeId);
        }

        return $ids->values()->all();
    }

    private function splitDateTime(?string $value): array
    {
        if (!$value) {
            return [null, null];
        }

        $dateTime = Carbon::parse($value);

        return [
            $dateTime->format('Y-m-d'),
            $dateTime->format('H:i'),
        ];
    }

    private function createLinkedPersonalTask(KanbanLeadTask $kanbanTask, array $data, int $employeeId): PersonalTask
    {
        $employeeIds = $this->normalizeEmployeeIds($data, $employeeId);

        [$dueDate, $dueTime] = $this->splitDateTime(
            optional($kanbanTask->planned_end_at)->format('Y-m-d H:i:s') ?: ($data['planned_end_at'] ?? null)
        );

        [$startDate] = $this->splitDateTime(
            optional($kanbanTask->planned_start_at)->format('Y-m-d H:i:s') ?: ($data['planned_start_at'] ?? null)
        );

        $personalTask = PersonalTask::query()->create([
            'task_id' => (string) $kanbanTask->id,
            'task_title' => $kanbanTask->title,
            'description' => $kanbanTask->description ?: $kanbanTask->internal_note,
            'assigned_by' => $employeeId,
            'task_status' => 'pending',
            'board_column' => $dueDate ? null : 'no_due',
            'priority' => $data['appointment_priority'] ?? 'normal',
            'color' => '#93c21c',
            'repeat' => 'no',
            'reminder_date' => $dueDate,
            'reminder_time' => $dueTime,
            'progress' => 0,
            'public' => false,
            'type' => 'kanban',
            'due_date' => $dueDate,
            'due_time' => $dueTime,
            'start_date' => $startDate,
            'controller_id' => $employeeIds,
            'is_customer' => true,
            'customer_id' => $kanbanTask->customer_id,
            'alternative_id' => $kanbanTask->alternative_id,
            'product_id' => $kanbanTask->product_id,
            'is_report' => false,
        ]);

        $personalTask->employees()->sync(
            collect($employeeIds)
                ->mapWithKeys(fn($id) => [
                    $id => [
                        'status' => 'pending',
                        'change_date' => now(),
                        'changed_by' => $employeeId,
                    ],
                ])
                ->all()
        );

        PersonalTaskKey::query()->create([
            'personal_task_id' => $personalTask->id,
            'same_id' => $kanbanTask->id,
            'task' => $kanbanTask->title,
            'duration' => $kanbanTask->estimated_minutes
                ? round(((int) $kanbanTask->estimated_minutes) / 60, 2)
                : null,
            'key_description' => $kanbanTask->description ?: $kanbanTask->internal_note,
            'status' => 'pending',
            'is_completed' => false,
            'work_progress' => 0,
            'employee_id' => $employeeIds,
        ]);

        return $personalTask;
    }

    private function createLinkedAppointment(
        KanbanLeadTask $kanbanTask,
        array $data,
        int $employeeId,
        ?PersonalTask $personalTask = null
    ): MainAppointment {
        $employeeIds = $this->normalizeEmployeeIds($data, $employeeId);

        $start = $kanbanTask->planned_start_at
            ? Carbon::parse($kanbanTask->planned_start_at)
            : (!empty($data['planned_start_at']) ? Carbon::parse($data['planned_start_at']) : now());

        $end = $kanbanTask->planned_end_at
            ? Carbon::parse($kanbanTask->planned_end_at)
            : (!empty($data['planned_end_at'])
                ? Carbon::parse($data['planned_end_at'])
                : $start->copy()->addMinutes((int) ($kanbanTask->estimated_minutes ?: 60)));

        $appointment = MainAppointment::query()->create([
            'created_by' => $employeeId,
            'name' => $kanbanTask->title,
            'note' => $kanbanTask->description ?: $kanbanTask->internal_note,
            'color' => '#93c21c',
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'appointment_type' => $data['appointment_type'] ?? 'kanban_task',
            'contact_mode' => $data['appointment_contact_mode'] ?? null,
            'priority' => $data['appointment_priority'] ?? 'normal',
            'public' => false,
            'status' => 'open',
            'source' => 'kanban_task',
            'task_id' => $personalTask?->id,
            'customer_id' => $kanbanTask->customer_id,
            'products' => $kanbanTask->product_id ? [(int) $kanbanTask->product_id] : [],
            'type' => 'kanban',
            'next_step' => $kanbanTask->internal_note,
        ]);

        $appointment->employees()->sync(
            collect($employeeIds)
                ->mapWithKeys(fn($id) => [
                    $id => [
                        'status' => 'pending',
                        'reason' => null,
                    ],
                ])
                ->all()
        );

        return $appointment;
    }

    private function syncTaskEmployees(KanbanLeadTask $task, array $employeeIds): void
    {
        $employeeIds = collect($employeeIds)
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        if ($employeeIds->isEmpty()) {
            $task->employees()->detach();
            return;
        }

        $sync = [];

        foreach ($employeeIds as $employeeId) {
            $sync[$employeeId] = [
                'role' => 'scheduled_candidate',
                'status' => 'open',
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $task->employees()->sync($sync);
    }

    private function resolveLeadStageId(LeadProductList $leadProduct): ?int
    {
        $status = strtolower(trim((string) ($leadProduct->status ?: $leadProduct->stage ?: '')));

        if ($status !== '') {
            $id = LeadStage::query()
                ->where('key', $status)
                ->value('id');

            if ($id) {
                return (int) $id;
            }
        }

        return LeadStage::query()
            ->where('is_default', true)
            ->value('id');
    }

    private function stageLabel(?int $stageId): ?string
    {
        if (!$stageId) {
            return null;
        }

        return LeadStage::query()
            ->whereKey($stageId)
            ->value('name');
    }

    private function authEmployeeId(): int
    {
        return (int) auth()->user()->name;
    }

    private function activityMinutes(?PhaseActivities $activity): ?int
    {
        if (!$activity || !$activity->duration) {
            return null;
        }

        $duration = (int) $activity->duration;
        $type = strtolower((string) ($activity->duration_type ?: 'minutes'));

        return match ($type) {
            'hour', 'hours', 'stunde', 'stunden' => $duration * 60,
            'day', 'days', 'tag', 'tage' => $duration * 60 * 8,
            default => $duration,
        };
    }
}