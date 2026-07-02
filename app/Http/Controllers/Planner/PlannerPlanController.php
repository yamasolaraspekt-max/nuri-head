<?php
namespace App\Http\Controllers\Planner;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

use App\Models\PlannerPlan;
use App\Models\PlannerItem;
use App\Models\MasterSet; // Import Model
use App\Events\PlannerRealtimeEvent;
use App\Notifications\PlannerToastNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use App\Models\LeadProductList;
use App\Models\Employee;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
use App\Models\PlannerItemDependency;
class PlannerPlanController extends Controller
{
    // =========================================================================
    // Realtime & Notifications Helpers
    // =========================================================================
    private function rtPlan(int $planId): string
    {
        return "planner.plan.$planId";
    }
    private function rtEmployee(int $employeeId): string
    {
        return "planner.employee.$employeeId";
    }
    private function broadcast(array $channels, string $name, array $payload = []): void
    {
        event(new PlannerRealtimeEvent($channels, $name, $payload));
    }
    private function notifyEmployees(array $employeeIds, array $data): void
    {
        $employeeIds = array_values(array_unique(array_filter(array_map('intval', $employeeIds))));
        if (empty($employeeIds))
            return;
        $stringIds = array_map('strval', $employeeIds);
        $users = \App\Models\User::query()->whereIn('name', $stringIds)->get();
        if ($users->isNotEmpty()) {
            Notification::send($users, new PlannerToastNotification($data));
        }
    }
    private function resolvePhotoUrl($emp): ?string
    {
        $photo = $emp->photo ?? $emp->image ?? $emp->avatar ?? null;
        $photo = trim((string) $photo);

        if ($photo === '') {
            return null;
        }

        if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
            return $photo;
        }

        if (str_starts_with($photo, '/')) {
            return asset(ltrim($photo, '/'));
        }

        if (str_starts_with($photo, 'images/') || str_starts_with($photo, 'storage/') || str_starts_with($photo, 'uploads/')) {
            return asset($photo);
        }

        return asset('images/employee/' . ltrim($photo, '/'));
    }

    private function employeeInitials($employee): string
    {
        $name = trim((string) ($employee->name ?? ''));
        $lastname = trim((string) ($employee->lastname ?? ''));

        $first = $name !== '' ? mb_substr($name, 0, 1) : '';
        $last = $lastname !== '' ? mb_substr($lastname, 0, 1) : '';

        $initials = strtoupper($first . $last);

        return $initials !== '' ? $initials : 'MA';
    }

    private function articleGroupInitial($product): string
    {
        $initial = trim((string) ($product->initial ?? ''));

        if ($initial !== '') {
            return strtoupper(mb_substr($initial, 0, 4));
        }

        $name = trim((string) ($product->article_group ?? $product->name ?? ''));

        if ($name !== '') {
            $parts = preg_split('/\s+/', $name);
            $letters = collect($parts)
                ->filter()
                ->take(2)
                ->map(fn($part) => mb_substr($part, 0, 1))
                ->implode('');

            return strtoupper($letters ?: mb_substr($name, 0, 2));
        }

        return 'AG';
    }

    private function articleGroupImageUrl($product): ?string
    {
        $image = trim((string) ($product->image ?? ''));

        if ($image === '') {
            return null;
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        if (str_starts_with($image, '/')) {
            return asset(ltrim($image, '/'));
        }

        if (str_starts_with($image, 'images/') || str_starts_with($image, 'storage/') || str_starts_with($image, 'uploads/')) {
            return asset($image);
        }

        return asset('images/article_groups/' . ltrim($image, '/'));
    }
    private function decodeJson($value): array
    {
        if ($value === null)
            return [];
        if (is_array($value))
            return $value;
        $s = trim((string) $value);
        if ($s === '')
            return [];
        $j = json_decode($s, true);
        return is_array($j) ? $j : [];
    }

    private function authEmployeeId(): ?int
    {
        $user = auth()->user();

        $id = $user?->employee_id
            ?? $user?->employee?->id
            ?? $user?->name
            ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    private function activeEmployeeQuery()
    {
        return DB::table('employees')
            ->whereNull('deleted_at')
            ->whereRaw("LOWER(COALESCE(status, '')) = 'active'");
    }

    private function planProject(PlannerPlan $plan): ?object
    {
        if (!$plan->project_id) {
            return null;
        }

        return DB::table('lead_product_lists')
            ->whereNull('deleted_at')
            ->where('id', $plan->project_id)
            ->first();
    }

    private function safeColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    private function normalizeTime(?string $time, string $fallback = '08:00:00'): string
    {
        $time = trim((string) $time);

        if ($time === '') {
            return $fallback;
        }

        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return $time . ':00';
        }

        return $time;
    }

    private function projectScopedQuery(string $table, object $project)
    {
        $q = DB::table($table);

        if ($this->safeColumn($table, 'deleted_at')) {
            $q->whereNull('deleted_at');
        }

        if ($this->safeColumn($table, 'customer_id')) {
            $q->where('customer_id', (int) $project->customer_id);
        }

        $q->where(function ($scope) use ($table, $project) {
            $hasDirectProject = $this->safeColumn($table, 'lead_product_list_id');

            if ($hasDirectProject) {
                $scope->where('lead_product_list_id', (int) $project->id);
            }

            $scope->{$hasDirectProject ? 'orWhere' : 'where'}(function ($fallback) use ($table, $project) {
                if ($this->safeColumn($table, 'customer_id')) {
                    $fallback->where('customer_id', (int) $project->customer_id);
                }

                if (!empty($project->alternative_id) && $this->safeColumn($table, 'alternative_id')) {
                    $fallback->where('alternative_id', (int) $project->alternative_id);
                }

                if (!empty($project->product_id)) {
                    if ($this->safeColumn($table, 'product_id')) {
                        $fallback->where('product_id', (int) $project->product_id);
                    } elseif ($table === 'main_appointments' && $this->safeColumn($table, 'products')) {
                        $pid = (int) $project->product_id;
                        $fallback->where(function ($p) use ($pid) {
                            $p->where('products', 'like', '%"' . $pid . '"%')
                                ->orWhere('products', 'like', '%' . $pid . '%');
                        });
                    }
                }
            });
        });

        return $q;
    }

    private function updateSourceProjectContext(string $table, int $id, object $project, ?int $plannerItemId = null, ?int $plannerPlanId = null): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $updates = [];

        if ($plannerItemId && $this->safeColumn($table, 'planner_item_id')) {
            $updates['planner_item_id'] = $plannerItemId;
        }

        if ($plannerPlanId && $this->safeColumn($table, 'planner_id')) {
            $updates['planner_id'] = $plannerPlanId;
        }

        if ($this->safeColumn($table, 'lead_product_list_id')) {
            $updates['lead_product_list_id'] = (int) $project->id;
        }

        if ($this->safeColumn($table, 'lead_stage_id')) {
            $updates['lead_stage_id'] = !empty($project->product_stage_id) ? (int) $project->product_stage_id : null;
        }

        if ($this->safeColumn($table, 'lead_stage_sub_stage_id')) {
            $updates['lead_stage_sub_stage_id'] = !empty($project->lead_stage_sub_stage_id) ? (int) $project->lead_stage_sub_stage_id : null;
        }

        if (!empty($updates)) {
            DB::table($table)->where('id', $id)->update($updates);
        }
    }

    private function syncPlannerItemEmployees(int $plannerItemId, array $employeeIds): void
    {
        $employeeIds = array_values(array_unique(array_filter(array_map('intval', $employeeIds))));

        DB::table('planner_item_employees')
            ->where('planner_item_id', $plannerItemId)
            ->delete();

        foreach ($employeeIds as $index => $employeeId) {
            DB::table('planner_item_employees')->insert([
                'planner_item_id' => $plannerItemId,
                'employee_id' => $employeeId,
                'role' => $index === 0 ? 'lead' : 'member',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }



    public function customerStagePhases(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'min:1'],
            'alternative_id' => ['nullable', 'integer', 'min:1'],
            'product_id' => ['required', 'integer', 'min:1'],
        ]);
        $customerId = (int) $data['customer_id'];
        $alternativeId = $data['alternative_id'] ? (int) $data['alternative_id'] : null;
        $productId = (int) $data['product_id'];
        // 1) Your existing builder (example) — keep your current query here
        $stages = $this->buildStagesWithPhases($customerId, $alternativeId, $productId); // returns array/collection like your JSON
        // 2) Collect phase ids that are "staffed" (has suggested employee)
        $phaseIds = collect($stages)
            ->flatMap(fn($stage) => collect($stage['phases'] ?? []))
            ->filter(fn($phase) => !empty($phase['suggested_employees'])) // staffed
            ->pluck('phase_id')
            ->filter()
            ->unique()
            ->values();
        if ($phaseIds->isEmpty()) {
            // Still return stages, but phases won’t have extra lists
            return response()->json(['ok' => true, 'data' => $stages]);
        }
        // 3) Load all open items for these phases in ONE shot per module
        // Replace model names + status logic to match your project
        $tasksByPhase = \App\Models\Task::query()
            ->where('customer_id', $customerId)
            ->when($alternativeId !== null, fn($q) => $q->where('alternative_id', $alternativeId))
            ->where('product_id', $productId)
            ->whereIn('phase_id', $phaseIds)
            ->where(function ($q) {
                $q->whereNull('is_done')->orWhere('is_done', false);
            })
            ->whereNotIn('status', ['done', 'closed', 'completed']) // adjust to your enums
            ->latest('id')
            ->get()
            ->groupBy('phase_id');
        $appointmentsByPhase = \App\Models\Appointment::query()
            ->where('customer_id', $customerId)
            ->when($alternativeId !== null, fn($q) => $q->where('alternative_id', $alternativeId))
            ->where('product_id', $productId)
            ->whereIn('phase_id', $phaseIds)
            ->whereNotIn('status', ['done', 'cancelled', 'canceled'])
            ->latest('id')
            ->get()
            ->groupBy('phase_id');
        $ticketsByPhase = \App\Models\Problem::query() // or Ticket model
            ->where('customer_id', $customerId)
            ->when($alternativeId !== null, fn($q) => $q->where('alternative_id', $alternativeId))
            ->where('product_id', $productId)
            ->whereIn('phase_id', $phaseIds)
            ->whereNotIn('status', ['done', 'closed', 'resolved'])
            ->latest('id')
            ->get()
            ->groupBy('phase_id');
        // Optional: customer_phase rows themselves (if you want to show assignments/status)
        $customerPhaseByPhase = \App\Models\CustomerPhase::query()
            ->where('customer_id', $customerId)
            ->when($alternativeId !== null, fn($q) => $q->where('alternative_id', $alternativeId))
            ->where('product_id', $productId)
            ->whereIn('phase_id', $phaseIds)
            ->whereNotIn('status', ['done', 'closed'])
            ->get()
            ->groupBy('phase_id');
        // 4) Attach to phases + filter activities to "not done" (because staffed)
        $stages = collect($stages)->map(function ($stage) use ($tasksByPhase, $appointmentsByPhase, $ticketsByPhase, $customerPhaseByPhase) {
            $stage['phases'] = collect($stage['phases'] ?? [])->map(function ($phase) use ($tasksByPhase, $appointmentsByPhase, $ticketsByPhase, $customerPhaseByPhase) {
                $phaseId = $phase['phase_id'] ?? null;
                $isStaffed = !empty($phase['suggested_employees']);
                if ($isStaffed) {
                    // keep only open activities
                    $phase['activities'] = collect($phase['activities'] ?? [])
                        ->filter(fn($a) => empty($a['is_done']) && strtolower((string) ($a['status'] ?? 'open')) !== 'done')
                        ->values()
                        ->all();
                    // attach grouped open work items
                    $phase['open_items'] = [
                        'tasks' => ($phaseId && isset($tasksByPhase[$phaseId])) ? $tasksByPhase[$phaseId]->values() : [],
                        'appointments' => ($phaseId && isset($appointmentsByPhase[$phaseId])) ? $appointmentsByPhase[$phaseId]->values() : [],
                        'tickets' => ($phaseId && isset($ticketsByPhase[$phaseId])) ? $ticketsByPhase[$phaseId]->values() : [],
                        'customer_phase' => ($phaseId && isset($customerPhaseByPhase[$phaseId])) ? $customerPhaseByPhase[$phaseId]->values() : [],
                    ];
                } else {
                    // if not staffed, you can hide items entirely
                    $phase['open_items'] = [
                        'tasks' => [],
                        'appointments' => [],
                        'tickets' => [],
                        'customer_phase' => []
                    ];
                }
                return $phase;
            })->values()->all();
            return $stage;
        })->values()->all();
        return response()->json(['ok' => true, 'data' => $stages]);
    }

    // =========================================================================
    // Core Logic: Sync & Load
    // =========================================================================

    public function syncAndLoad(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'min:1'],
            'project_id' => ['required', 'integer', 'min:1'],
            'product_id' => ['nullable', 'integer'],
        ]);

        $project = DB::table('lead_product_lists')
            ->whereNull('deleted_at')
            ->where('id', (int) $data['project_id'])
            ->first();

        if (!$project) {
            return response()->json(['ok' => false, 'message' => 'Projekt wurde nicht gefunden.'], 404);
        }

        return DB::transaction(function () use ($project) {
            $plan = PlannerPlan::firstOrCreate(
                [
                    'customer_id' => (int) $project->customer_id,
                    'project_id' => (int) $project->id,
                ],
                [
                    'title' => 'Projektplan #' . $project->id,
                    'stage' => $project->status ?: 'project',
                    'status' => 'active',
                    'created_by' => auth()->id(),
                    'meta' => [
                        'product_id' => $project->product_id,
                        'alternative_id' => $project->alternative_id,
                        'lead_stage_sub_stage_id' => $project->lead_stage_sub_stage_id ?? null,
                    ],
                ]
            );

            $this->syncAppointments($plan);
            $this->syncTickets($plan);
            $this->syncPersonalTasks($plan);
            $this->syncPhaseActivities($plan);
            $this->syncMasterSets($plan);

            $debugCounts = DB::table('planner_items')
                ->where('plan_id', $plan->id)
                ->whereNull('deleted_at')
                ->select('source_type', DB::raw('COUNT(*) as total'))
                ->groupBy('source_type')
                ->pluck('total', 'source_type');

            Log::info('Planner sync completed', [
                'plan_id' => $plan->id,
                'project_id' => $project->id,
                'customer_id' => $project->customer_id,
                'counts' => $debugCounts,
            ]);

            return response()->json([
                'ok' => true,
                'data' => $this->buildPlanPayloadWithRequestedMaterials($plan->id),
            ]);
        });
    }


    private function syncPhaseActivities(PlannerPlan $plan)
    {
        $project = $this->planProject($plan);

        if (!$project || empty($project->product_id) || !Schema::hasTable('task_phases')) {
            return;
        }

        $stageId = $this->pmoResolveProjectLeadStageId($project);
        $subStageId = !empty($project->lead_stage_sub_stage_id) ? (int) $project->lead_stage_sub_stage_id : null;
        $phaseRows = $this->pmoLoadMatchingTaskPhaseRows($project, $stageId, $subStageId);

        if ($phaseRows->isEmpty()) {
            return;
        }

        $phaseIds = $phaseRows->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $activityRows = $this->pmoLoadMatchingPhaseActivityRows($project, $phaseIds, $stageId, $subStageId);
        $historyByActivity = $this->pmoCustomerActivityHistoryMap($project);
        $activitiesByPhase = $activityRows->groupBy(fn($row) => (int) ($row->phase_id ?? 0));

        // Rueckfluss 1b: Karten dieses Gewerks einmalig laden (kein N+1) -> [phase_activity_id => card_id], nur lebende.
        $cardMap = [];
        if (Schema::hasTable('kanban_lead_tasks')) {
            $cardMap = DB::table('kanban_lead_tasks')
                ->where('lead_product_list_id', (int) $project->id)
                ->whereNotNull('phase_activity_id')
                ->when($this->safeColumn('kanban_lead_tasks', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->pluck('id', 'phase_activity_id')->all();
        }

        foreach ($phaseRows as $phase) {
            $phaseId = (int) $phase->id;
            $phaseActivities = $activitiesByPhase->get($phaseId, collect());

            /*
            |--------------------------------------------------------------------------
            | Phase without activities
            |--------------------------------------------------------------------------
            | If the admin created only a TaskPhase but no child PhaseActivities yet,
            | the planner still needs a visible work item. This item is saved as a
            | planner-only task_phase so status/history/attendance can be tracked per
            | project without changing the global TaskPhase template.
            */
            if ($phaseActivities->isEmpty()) {
                $employeeIds = $this->pmoEmployeeIdsForTemplatePhase($project, $phaseId);

                $this->pmoUpsertTemplatePlannerItem($plan, 'task_phase', $phaseId, [
                    'title' => $phase->phase_name ?: ('Phase #' . $phaseId),
                    'description' => $phase->description ?? null,
                    'duration_minutes' => max(1, (int) ($phase->duration_minutes ?? $phase->count ?? 60)),
                    'status' => $this->pmoNormalizePlannerStatus((string) ($phase->status ?? 'open')),
                    'planned_start_at' => null,
                    'planned_end_at' => null,
                    'sort_order' => (int) ($phase->order ?? $phase->sort_order ?? 9999),
                ], $employeeIds);

                continue;
            }

            foreach ($phaseActivities as $activity) {
                $activityId = (int) $activity->id;
                $history = $historyByActivity->get($activityId);
                $isDone = $this->pmoActivityHistoryIsDone($history);
                $employeeIds = $this->pmoEmployeeIdsForTemplatePhase($project, $phaseId, $activity, $history);

                $title = trim((string) ($activity->title ?? ''));
                if ($title === '') {
                    $title = $phase->phase_name ?: ('Aktivität #' . $activityId);
                }

                $duration = (int) ($activity->duration ?? 0);
                if ($duration <= 0) {
                    $duration = (int) ($phase->count ?? 0);
                }
                if ($duration <= 0) {
                    $duration = 60;
                }

                $status = $isDone
                    ? 'done'
                    : $this->pmoNormalizePlannerStatus((string) ($activity->status ?? $phase->status ?? 'open'));

                $this->pmoUpsertTemplatePlannerItem($plan, 'phase_activity', $activityId, [
                    'title' => $title,
                    'description' => $activity->description ?? $activity->notes ?? $phase->description ?? null,
                    'duration_minutes' => $duration,
                    'status' => $status,
                    'planned_start_at' => null,
                    'planned_end_at' => null,
                    'sort_order' => (int) ($activity->sort_order ?? $phase->order ?? 9999),
                    'done_at' => $isDone ? $this->pmoSourceDoneAt($history) : null,
                    'done_by_employee_id' => $isDone ? $this->pmoSourceDoneByEmployeeId($history) : null,
                    'kanban_lead_task_id' => $cardMap[$activityId] ?? null,
                ], $employeeIds);
            }
        }
    }

    private function pmoResolveProjectLeadStageId(object $project): ?int
    {
        if (!Schema::hasTable('lead_stages')) {
            return null;
        }

        $statusKey = strtolower(trim((string) ($project->status ?? '')));

        if ($statusKey !== '') {
            $stageId = DB::table('lead_stages')
                ->whereNull('deleted_at')
                ->whereRaw('LOWER(`key`) = ?', [$statusKey])
                ->value('id');

            if ($stageId) {
                return (int) $stageId;
            }
        }

        $montageStage = $this->pmoMontageStage();

        return $montageStage?->id ? (int) $montageStage->id : null;
    }

    private function pmoLoadMatchingTaskPhaseRows(object $project, ?int $stageId = null, ?int $subStageId = null)
    {
        $q = DB::table('task_phases as tp')
            ->where('tp.product_id', (int) $project->product_id);

        if ($this->safeColumn('task_phases', 'deleted_at')) {
            $q->whereNull('tp.deleted_at');
        }

        if ($this->safeColumn('task_phases', 'status')) {
            $q->where(function ($statusQuery) {
                $statusQuery->whereNull('tp.status')
                    ->orWhere('tp.status', '')
                    ->orWhereRaw('LOWER(tp.status) NOT IN (?, ?, ?, ?)', ['inactive', 'disabled', 'deleted', 'archived']);
            });
        }

        $this->pmoApplyTemplateStageScope($q, 'task_phases', 'tp', $stageId, $subStageId);

        return $q->select('tp.*')
            ->orderBy($this->safeColumn('task_phases', 'order') ? 'tp.order' : 'tp.id')
            ->orderBy('tp.id')
            ->limit(500)
            ->get();
    }

    private function pmoLoadMatchingPhaseActivityRows(object $project, array $phaseIds, ?int $stageId = null, ?int $subStageId = null)
    {
        if (!Schema::hasTable('phase_activities')) {
            return collect();
        }

        $q = DB::table('phase_activities as pa')
            ->leftJoin('task_phases as tp', 'pa.phase_id', '=', 'tp.id')
            ->where(function ($scope) use ($project, $phaseIds) {
                if (!empty($phaseIds)) {
                    $scope->whereIn('pa.phase_id', $phaseIds);
                }

                if ($this->safeColumn('phase_activities', 'product_id')) {
                    $method = !empty($phaseIds) ? 'orWhere' : 'where';
                    $scope->{$method}('pa.product_id', (int) $project->product_id);
                }
            });

        if ($this->safeColumn('phase_activities', 'deleted_at')) {
            $q->whereNull('pa.deleted_at');
        }

        if ($this->safeColumn('phase_activities', 'status')) {
            $q->where(function ($statusQuery) {
                $statusQuery->whereNull('pa.status')
                    ->orWhere('pa.status', '')
                    ->orWhereRaw('LOWER(pa.status) NOT IN (?, ?, ?, ?)', ['inactive', 'disabled', 'deleted', 'archived']);
            });
        }

        $this->pmoApplyTemplateStageScope($q, 'phase_activities', 'pa', $stageId, $subStageId);

        return $q->select([
            'pa.*',
            DB::raw('tp.phase_name as template_phase_name'),
            DB::raw('tp.description as template_phase_description'),
            DB::raw('tp.`order` as template_phase_order'),
        ])
            ->orderBy($this->safeColumn('phase_activities', 'sort_order') ? 'pa.sort_order' : 'pa.id')
            ->orderBy('pa.id')
            ->limit(1000)
            ->get();
    }

    private function pmoApplyTemplateStageScope($q, string $table, string $alias, ?int $stageId = null, ?int $subStageId = null): void
    {
        if ($stageId && $this->safeColumn($table, 'lead_stage_id')) {
            $q->where(function ($stageQuery) use ($alias, $stageId) {
                $stageQuery->whereNull($alias . '.lead_stage_id')
                    ->orWhere($alias . '.lead_stage_id', 0)
                    ->orWhere($alias . '.lead_stage_id', (int) $stageId);
            });
        }

        if ($subStageId && $this->safeColumn($table, 'lead_sub_stage_id')) {
            $q->where(function ($subStageQuery) use ($alias, $subStageId) {
                $subStageQuery->whereNull($alias . '.lead_sub_stage_id')
                    ->orWhere($alias . '.lead_sub_stage_id', 0)
                    ->orWhere($alias . '.lead_sub_stage_id', (int) $subStageId);
            });
        }
    }

    private function pmoCustomerActivityHistoryMap(object $project)
    {
        if (!Schema::hasTable('customer_histories') || !$this->safeColumn('customer_histories', 'activity_id')) {
            return collect();
        }

        $q = DB::table('customer_histories')
            ->where('customer_id', (int) $project->customer_id)
            ->where('product_id', (int) $project->product_id);

        if (!empty($project->alternative_id) && $this->safeColumn('customer_histories', 'alternative_id')) {
            $q->where('alternative_id', (int) $project->alternative_id);
        }

        return $q->get()->keyBy(fn($row) => (int) ($row->activity_id ?? 0));
    }

    private function pmoActivityHistoryIsDone($history): bool
    {
        if (!$history) {
            return false;
        }

        foreach (['is_done', 'is_completed', 'completed'] as $column) {
            if (isset($history->{$column}) && ((int) $history->{$column} === 1 || strtolower((string) $history->{$column}) === 'done')) {
                return true;
            }
        }

        return false;
    }

    private function pmoEmployeeIdsForTemplatePhase(object $project, ?int $phaseId = null, $activity = null, $history = null): array
    {
        $ids = [];

        $historyEmployee = $this->pmoSourceDoneByEmployeeId($history);
        if ($historyEmployee) {
            $ids[] = $historyEmployee;
        }

        if ($activity) {
            $ids = array_merge($ids, $this->pmoEmployeeIdsFromRow($activity, [
                'contact_person',
                'responsible_person',
                'inside_service',
                'outside_service',
                'answered_by',
                'employee_id',
                'responsible',
            ]));
        }

        if (Schema::hasTable('activity_employees')) {
            $pivot = DB::table('activity_employees');

            if ($activity && isset($activity->id) && $this->safeColumn('activity_employees', 'activity_id')) {
                $pivot->where('activity_id', (int) $activity->id);
            } elseif ($phaseId && $this->safeColumn('activity_employees', 'phase_id')) {
                $pivot->where('phase_id', (int) $phaseId);
            }

            if ($this->safeColumn('activity_employees', 'employee_id')) {
                $ids = array_merge($ids, $pivot->pluck('employee_id')->map(fn($id) => (int) $id)->all());
            }
        }

        if ($phaseId && Schema::hasTable('customer_suggest_employees')) {
            $suggestions = DB::table('customer_suggest_employees');

            if ($this->safeColumn('customer_suggest_employees', 'customer_id')) {
                $suggestions->where('customer_id', (int) $project->customer_id);
            }

            if ($this->safeColumn('customer_suggest_employees', 'product_id')) {
                $suggestions->where('product_id', (int) $project->product_id);
            }

            if (!empty($project->alternative_id) && $this->safeColumn('customer_suggest_employees', 'alternative_id')) {
                $suggestions->where('alternative_id', (int) $project->alternative_id);
            }

            if ($this->safeColumn('customer_suggest_employees', 'phase_id')) {
                $suggestions->where('phase_id', (int) $phaseId);
            }

            if ($this->safeColumn('customer_suggest_employees', 'employee_id')) {
                $ids = array_merge($ids, $suggestions->pluck('employee_id')->map(fn($id) => (int) $id)->all());
            }
        }

        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }

    private function pmoUpsertTemplatePlannerItem(PlannerPlan $plan, string $sourceType, int $sourceId, array $payload, array $employeeIds = []): void
    {
        if (!Schema::hasTable('planner_items') || $sourceId <= 0) {
            return;
        }

        $item = PlannerItem::query()->firstOrNew([
            'plan_id' => (int) $plan->id,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);

        if (!$item->exists && empty($item->client_uid)) {
            $item->client_uid = (string) Str::uuid();
        }

        $item->title = $payload['title'] ?? $item->title ?? ('Arbeit #' . $sourceId);
        $item->description = $payload['description'] ?? $item->description;
        $item->duration_minutes = max(1, (int) ($payload['duration_minutes'] ?? $item->duration_minutes ?? 60));
        $item->sort_order = (int) ($payload['sort_order'] ?? $item->sort_order ?? 9999);

        $incomingStatus = $this->pmoNormalizePlannerStatus((string) ($payload['status'] ?? 'open'));
        if (!$item->exists || $this->pmoIsDoneStatus($incomingStatus)) {
            $item->status = $incomingStatus;
        } elseif (empty($item->status)) {
            $item->status = 'open';
        }

        if (!$item->exists && !empty($payload['planned_start_at'])) {
            $item->planned_start_at = $payload['planned_start_at'];
        }

        if (!$item->exists && !empty($payload['planned_end_at'])) {
            $item->planned_end_at = $payload['planned_end_at'];
        }

        if ($this->safeColumn('planner_items', 'done_at') && !empty($payload['done_at'])) {
            $item->done_at = $payload['done_at'];
        }

        if ($this->safeColumn('planner_items', 'done_by_employee_id') && !empty($payload['done_by_employee_id'])) {
            $item->done_by_employee_id = (int) $payload['done_by_employee_id'];
        }

        // Rueckfluss 1b: Link zur Buero-Karte frisch halten. array_key_exists (nicht !empty) -> setzt auch null,
        // wenn keine lebende Karte (mehr) existiert. Fuer task_phase-Items ohne den Key wird nichts angefasst.
        if ($this->safeColumn('planner_items', 'kanban_lead_task_id') && array_key_exists('kanban_lead_task_id', $payload)) {
            $item->kanban_lead_task_id = $payload['kanban_lead_task_id'] ? (int) $payload['kanban_lead_task_id'] : null;
        }

        $item->save();

        $employeeIds = array_values(array_unique(array_filter(array_map('intval', $employeeIds))));
        if (!empty($employeeIds) && Schema::hasTable('planner_item_employees')) {
            $this->syncPlannerItemEmployees((int) $item->id, $employeeIds);
        }
    }

    /**
     * NEW: Sync Master Sets that are NOT attached to an activity (Standalone)
     */
    private function syncMasterSets(PlannerPlan $plan)
    {
        $projectId = $plan->project_id;
        $project = DB::table('lead_product_lists')->where('id', $projectId)->first();
        if (!$project)
            return;
        $productId = $project->product_id;

        // 1. Find Master Sets that belong to this ArticleGroup (Product)
        // BUT exclude ones that are linked to a specific phase_activity (those are handled via relationship)
        $masterSets = MasterSet::where('article_group_id', $productId)
            ->whereNull('phase_activity_id') // Only standalone sets
            ->whereNull('deleted_at')
            ->get();

        foreach ($masterSets as $set) {
            // Check if this set is "done" (Logic can be customized, currently just checking if item exists and is done)
            // Ideally you'd have a 'customer_master_set_status' table, but we'll use PlannerItem status

            $item = PlannerItem::firstOrCreate(
                [
                    'plan_id' => $plan->id,
                    'source_type' => 'master_set',
                    'source_id' => $set->id
                ],
                [
                    'client_uid' => (string) Str::uuid(),
                    'title' => $set->name ?? 'Master Set #' . $set->id,
                    'description' => $set->description ?? 'Material/Labor Set',
                    'duration_minutes' => 60, // Default or calculate from labor
                    'status' => 'open',
                    'sort_order' => 9999,
                ]
            );
        }
    }


    private function syncAppointments(PlannerPlan $plan)
    {
        $project = $this->planProject($plan);

        if (!$project || !Schema::hasTable('main_appointments')) {
            return;
        }

        $appointments = $this->projectScopedQuery('main_appointments', $project)
            ->get();

        foreach ($appointments as $appointment) {
            $startStr = !empty($appointment->start_date)
                ? $appointment->start_date . ' ' . $this->normalizeTime($appointment->start_time ?? null, '08:00:00')
                : null;

            $endStr = !empty($appointment->end_date)
                ? $appointment->end_date . ' ' . $this->normalizeTime($appointment->end_time ?? null, '17:00:00')
                : null;

            $duration = 60;

            if ($startStr && $endStr) {
                try {
                    $duration = max(1, Carbon::parse($startStr)->diffInMinutes(Carbon::parse($endStr), false));
                } catch (\Throwable $e) {
                    $duration = 60;
                }
            }

            $employeeIds = [];

            if (Schema::hasTable('main_appointment_employees')) {
                $employeeIds = DB::table('main_appointment_employees')
                    ->where('appointment_id', $appointment->id)
                    ->pluck('employee_id')
                    ->toArray();
            }

            if (empty($employeeIds) && isset($appointment->employee_id) && !empty($appointment->employee_id)) {
                $employeeIds[] = $appointment->employee_id;
            }

            $employeeIds = array_values(array_unique(array_filter(array_map('intval', $employeeIds))));
            $status = ($startStr && !empty($employeeIds)) ? 'planned' : 'open';

            $item = PlannerItem::updateOrCreate(
                [
                    'plan_id' => $plan->id,
                    'source_type' => 'appointment',
                    'source_id' => $appointment->id,
                ],
                [
                    'title' => $appointment->name ?: 'Termin #' . $appointment->id,
                    'description' => $appointment->note ?? null,
                    'duration_minutes' => $duration,
                    'status' => $status,
                    'planned_start_at' => $startStr,
                    'planned_end_at' => $endStr,
                ]
            );

            if (!$item->client_uid) {
                $item->update(['client_uid' => (string) Str::uuid()]);
            }

            $this->syncPlannerItemEmployees($item->id, $employeeIds);
            $this->updateSourceProjectContext('main_appointments', (int) $appointment->id, $project, $item->id, $plan->id);
        }
    }


    private function syncTickets(PlannerPlan $plan)
    {
        $project = $this->planProject($plan);

        if (!$project || !Schema::hasTable('problems')) {
            return;
        }

        $tickets = $this->projectScopedQuery('problems', $project)
            ->where(function ($q) {
                if (Schema::hasColumn('problems', 'status')) {
                    $q->whereRaw('LOWER(COALESCE(status, "")) NOT IN (?, ?, ?, ?, ?, ?)', [
                        'completed',
                        'ended',
                        'done',
                        'closed',
                        'cancel',
                        'canceled'
                    ]);
                }
            })
            ->get();

        foreach ($tickets as $ticket) {
            $startStr = null;

            if (isset($ticket->date) && !empty($ticket->date)) {
                $startStr = $ticket->date . ' 08:00:00';
            } elseif (isset($ticket->created_at) && !empty($ticket->created_at)) {
                $startStr = Carbon::parse($ticket->created_at)->format('Y-m-d') . ' 08:00:00';
            }

            $employeeIds = [];

            if (isset($ticket->responsible) && !empty($ticket->responsible)) {
                $employeeIds[] = $ticket->responsible;
            }

            if (Schema::hasTable('employee_problem')) {
                $employeeIds = array_merge($employeeIds, DB::table('employee_problem')
                    ->where('problem_id', $ticket->id)
                    ->pluck('employee_id')
                    ->toArray());
            }

            $employeeIds = array_values(array_unique(array_filter(array_map('intval', $employeeIds))));
            $status = ($startStr && !empty($employeeIds)) ? 'planned' : 'open';

            $ticketTitle = 'Ticket #' . $ticket->id;

            if (isset($ticket->ticket_no) && trim((string) $ticket->ticket_no) !== '') {
                $ticketTitle = 'Ticket ' . $ticket->ticket_no;
            }

            $item = PlannerItem::updateOrCreate(
                [
                    'plan_id' => $plan->id,
                    'source_type' => 'ticket',
                    'source_id' => $ticket->id,
                ],
                [
                    'title' => $ticketTitle,
                    'description' => $ticket->problem ?? $ticket->description ?? null,
                    'duration_minutes' => 60,
                    'status' => $status,
                    'planned_start_at' => $startStr,
                    'sort_order' => 9999,
                ]
            );

            if (!$item->client_uid) {
                $item->update(['client_uid' => (string) Str::uuid()]);
            }

            if (Schema::hasTable('ticket_tasks')) {
                $ticketFk = Schema::hasColumn('ticket_tasks', 'ticket_id') ? 'ticket_id' : (Schema::hasColumn('ticket_tasks', 'problem_id') ? 'problem_id' : null);

                if ($ticketFk) {
                    $select = ['id'];
                    $select[] = Schema::hasColumn('ticket_tasks', 'title') ? DB::raw('title as task') : DB::raw("CONCAT('Teilaufgabe #', id) as task");
                    if (Schema::hasColumn('ticket_tasks', 'status'))
                        $select[] = 'status';
                    if (Schema::hasColumn('ticket_tasks', 'is_done'))
                        $select[] = 'is_done';

                    $subtasks = DB::table('ticket_tasks')
                        ->where($ticketFk, $ticket->id)
                        ->select($select)
                        ->get()
                        ->map(function ($task) {
                            $task->is_completed = (bool) ($task->is_done ?? false)
                                || in_array(strtolower((string) ($task->status ?? '')), ['done', 'completed', 'closed'], true);
                            return $task;
                        });

                    $this->syncChecklistsToItem($item, $subtasks);
                }
            }

            $this->syncPlannerItemEmployees($item->id, $employeeIds);
            $this->updateSourceProjectContext('problems', (int) $ticket->id, $project, $item->id, $plan->id);
        }
    }



    private function syncPersonalTasks(PlannerPlan $plan)
    {
        $project = $this->planProject($plan);

        if (!$project || !Schema::hasTable('personal_tasks')) {
            return;
        }

        $tasks = $this->projectScopedQuery('personal_tasks', $project)
            ->when($this->safeColumn('personal_tasks', 'archived_at'), fn($q) => $q->whereNull('archived_at'))
            ->when($this->safeColumn('personal_tasks', 'task_status'), function ($q) {
                $q->whereRaw('LOWER(COALESCE(task_status, "")) NOT IN (?, ?, ?, ?, ?, ?)', [
                    'completed',
                    'done',
                    'deleted',
                    'cancel',
                    'canceled',
                    'junk'
                ]);
            })
            ->get();

        foreach ($tasks as $task) {
            $dueStr = !empty($task->due_date)
                ? $task->due_date . ' ' . $this->normalizeTime($task->due_time ?? null, '12:00:00')
                : null;

            $item = PlannerItem::updateOrCreate(
                [
                    'plan_id' => $plan->id,
                    'source_type' => 'personal_task',
                    'source_id' => $task->id,
                ],
                [
                    'title' => $task->task_title ?: 'Aufgabe #' . $task->id,
                    'description' => $task->description ?? null,
                    'duration_minutes' => !empty($task->total_time) ? (int) $task->total_time : 60,
                    'status' => $dueStr ? 'planned' : 'open',
                    'planned_start_at' => $dueStr,
                    'sort_order' => 9999,
                ]
            );

            if (!$item->client_uid) {
                $item->update(['client_uid' => (string) Str::uuid()]);
            }

            if (Schema::hasTable('personal_task_keys')) {
                $keys = DB::table('personal_task_keys')
                    ->where('personal_task_id', $task->id)
                    ->whereNull('deleted_at')
                    ->get();

                $this->syncChecklistsToItem($item, $keys);
            }

            $employeeIds = [];

            if (Schema::hasTable('employees_personal_tasks')) {
                $employeeIds = DB::table('employees_personal_tasks')
                    ->where('task_id', $task->id)
                    ->pluck('employee_id')
                    ->toArray();
            }

            if (!empty($task->assigned_by)) {
                array_unshift($employeeIds, $task->assigned_by);
            }

            $this->syncPlannerItemEmployees($item->id, $employeeIds);
            $this->updateSourceProjectContext('personal_tasks', (int) $task->id, $project, $item->id, $plan->id);
        }
    }


    public function toggleTaskKey(Request $request, $id)
    {
        $data = $request->validate(['is_completed' => 'required|boolean']);

        DB::table('personal_task_keys')
            ->where('id', $id)
            ->update([
                'is_completed' => $data['is_completed'] ? 1 : 0,
                'updated_at' => now()
            ]);
        return response()->json(['ok' => true]);
    }

    private function buildPlanPayloadWithRequestedMaterials(int $planId): ?array
    {
        $payload = $this->buildPlanPayload($planId);

        if (!$payload) {
            return null;
        }

        return $this->attachRequestedMaterialsToPayload($payload);
    }

    private function buildPlanPayload(int $planId): ?array
    {
        // 1. Load Plan with all relationships, INCLUDING the new 'materials' table
        $plan = PlannerPlan::with([
            'items.employees',
            'items.checklists',
            'items.dependencies',
            'items.dependents',
        ])->find($planId);

        if (!$plan)
            return null;

        // --- Manager / Project Loading (Standard) ---
        $project = DB::table('lead_product_lists')->where('id', $plan->project_id)->first();
        $meta = $this->decodeJson($plan->meta);
        $extraMgrIds = $meta['extra_manager_ids'] ?? [];
        $pmId = $project->employee_id ?? null;

        // Collect IDs from assigned leads
        $itemLeadIds = $plan->items->map(function ($i) {
            return $i->employees->where('pivot.role', 'lead')->first()?->id;
        })->filter()->unique()->toArray();

        // Merge all relevant employee IDs
        $allManagerIds = array_unique(array_merge($pmId ? [$pmId] : [], $extraMgrIds, $itemLeadIds));
        $employees = DB::table('employees')->whereIn('id', $allManagerIds)->get()->keyBy('id');

        $managersOut = [];
        foreach ($allManagerIds as $mid) {
            if (isset($employees[$mid])) {
                $e = $employees[$mid];
                $managersOut[] = [
                    'id' => $e->id,
                    'employee_id' => $e->id,
                    'name' => $e->name,
                    'lastname' => $e->lastname,
                    'full_name' => trim("$e->title $e->name $e->lastname"),
                    'photo_url' => $this->resolvePhotoUrl($e),
                    'role' => ($pmId && $e->id == $pmId) ? 'PM' : 'Manager'
                ];
            }
        }

        // --- Build Items Payload ---
        $itemsOut = $plan->items->map(function ($item) {
            $lead = $item->employees->first(fn($e) => $e->pivot->role === 'lead');
            $members = $item->employees->filter(fn($e) => $e->pivot->role !== 'lead');

            // Map Checklists
            $checklist = $item->checklists->map(function ($c) {
                return [
                    'id' => $c->id,
                    'title' => $c->title,
                    'is_completed' => (bool) $c->is_completed
                ];
            })->values()->toArray();

            // Map Materials (From planner_item_materials).
            // This works for ANY item type (Ticket, Appointment, Personal Task, etc.)
            $materials = $this->pmoLoadPlannerMaterials((int) $item->id);
            $materialSummary = $this->pmoPlannerMaterialSummary($materials);

            // Planner specific steps created in the montage drawer.
            $plannerSteps = $this->pmoLoadPlannerItemSteps((int) $item->id);

            return [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'status' => $item->status,
                'planned_start_at' => $item->planned_start_at,
                'planned_end_at' => $item->planned_end_at,
                'dependencies' => $item->dependencies->map(fn($dep) => [
                    'id' => (int) $dep->id,
                    'dependency_id' => (int) ($dep->pivot->id ?? 0),
                    'reason' => $dep->pivot->reason,
                    'title' => $dep->title,
                ])->values(),
                'dependents' => $item->dependents->map(fn($dep) => [
                    'id' => (int) $dep->id,
                    'dependency_id' => (int) ($dep->pivot->id ?? 0),
                    'reason' => $dep->pivot->reason,
                    'title' => $dep->title,
                ])->values(),
                'duration_minutes' => $item->duration_minutes,
                'source_type' => $item->source_type,
                'source_id' => $item->source_id,
                'sort_order' => $item->sort_order,

                // New Data for Frontend Card
                'materials' => $materials,
                'material_summary' => $materialSummary,
                'steps' => !empty($plannerSteps) ? $plannerSteps : $checklist,
                'master_set_id' => $item->master_set_id, // If you want to show "Master Set #ID" in UI

                'checklist' => $checklist,
                'lead' => $lead ? [
                    'id' => $lead->id,
                    'full_name' => "$lead->name $lead->lastname",
                    'photo_url' => $this->resolvePhotoUrl($lead)
                ] : null,
                'members' => $members->map(fn($m) => [
                    'id' => $m->id,
                    'full_name' => "$m->name $m->lastname",
                    'photo_url' => $this->resolvePhotoUrl($m)
                ])->values(),

                'planned_date' => $item->planned_start_at ? Carbon::parse($item->planned_start_at)->format('Y-m-d') : null,
                'planned_time' => $item->planned_start_at ? Carbon::parse($item->planned_start_at)->format('H:i') : null,
            ];
        });

        // --- Active Employees List (For Dropdowns) ---
        $employeesActive = DB::table('employees')
            ->whereRaw("LOWER(COALESCE(status, '')) = 'active'")
            ->select('id', 'name', 'lastname', 'title', 'email', 'phone', 'image')
            ->orderBy('lastname')
            ->get()
            ->map(function ($e) {
                $e->full_name = trim("$e->title $e->name $e->lastname");
                $e->photo_url = $this->resolvePhotoUrl($e);
                return $e;
            });

        return [
            'plan' => [
                'id' => $plan->id,
                'title' => $plan->title,
                'project_id' => $plan->project_id,
                'customer_id' => $plan->customer_id,
                'meta' => $meta
            ],
            'items' => $itemsOut,
            'managers' => $managersOut,
            'project_manager' => isset($employees[$pmId]) ? ['id' => $pmId, 'full_name' => $employees[$pmId]->name . ' ' . $employees[$pmId]->lastname] : null,
            'employees_active' => $employeesActive
        ];
    }

    public function toggleChecklistStatus(Request $request, $id)
    {
        $data = $request->validate(['is_completed' => 'required|boolean']);

        \App\Models\PlannerItemChecklist::where('id', $id)->update([
            'is_completed' => $data['is_completed'],
            'updated_at' => now()
        ]);
        return response()->json(['ok' => true]);
    }

    // =========================================================================
    // Actions
    // =========================================================================
    public function updateItem(Request $request, PlannerPlan $plan, PlannerItem $item)
    {
        if ($item->plan_id !== $plan->id)
            abort(404);
        $data = $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'crew_ids' => 'nullable|array',
            'lead_id' => 'nullable|integer',
            'planned_start_at' => 'nullable|date',
            'status' => 'nullable|string'
        ]);
        $item->update(array_filter($data, fn($v) => !is_null($v)));
        if (isset($data['crew_ids']) || isset($data['lead_id'])) {
            $syncData = [];
            if (!empty($data['lead_id'])) {
                $syncData[$data['lead_id']] = ['role' => 'lead'];
            }
            if (!empty($data['crew_ids'])) {
                foreach ($data['crew_ids'] as $cid) {
                    if ($cid != ($data['lead_id'] ?? null)) {
                        $syncData[$cid] = ['role' => 'member'];
                    }
                }
            }
            $item->employees()->sync($syncData);
        }
        if (!empty($data['lead_id'])) {
            $this->notifyEmployees([$data['lead_id']], [
                'type' => 'item_updated',
                'title' => 'Aufgabe aktualisiert',
                'message' => $item->title,
                'plan_id' => $plan->id,
                'item_id' => $item->id
            ]);
        }
        return response()->json(['ok' => true, 'item' => $item->fresh('employees')]);
    }


    public function updateItemStatus(Request $request, PlannerPlan $plan, PlannerItem $item)
    {
        if ((int) $item->plan_id !== (int) $plan->id) {
            abort(404);
        }

        $data = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in([
                    'open',
                    'planned',
                    'in_progress',
                    'done',
                    'completed',
                    'blocked',
                    'paused',
                    'cancelled',
                    'canceled',
                ])
            ],
            'note' => ['nullable', 'string', 'max:2000'],
            'report' => ['nullable', 'string', 'max:20000'],
            'report_text' => ['nullable', 'string', 'max:20000'],
            'report_next_step' => ['nullable', 'string', 'max:255'],
            'report_due_date' => ['nullable', 'date'],
            'skip_report' => ['nullable', 'boolean'],
        ]);

        $status = $this->pmoNormalizePlannerStatus((string) $data['status']);
        $oldStatus = $this->pmoNormalizePlannerStatus((string) ($item->status ?? 'open'));
        $employeeId = $this->authEmployeeId();
        $now = now();

        $reportText = trim((string) ($data['report'] ?? $data['report_text'] ?? ''));
        $completionReport = [
            'text' => $reportText,
            'next_step' => trim((string) ($data['report_next_step'] ?? '')),
            'due_date' => $data['report_due_date'] ?? null,
            'skip_report' => filter_var($data['skip_report'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];

        $reportSaved = false;

        DB::transaction(function () use ($item, $plan, $status, $oldStatus, $employeeId, $now, $data, $completionReport, &$reportSaved) {
            $updates = [
                'status' => $status,
                'updated_at' => $now,
            ];

            if ($this->safeColumn('planner_items', 'done_at')) {
                $updates['done_at'] = $this->pmoIsDoneStatus($status) ? $now : null;
            }

            if ($this->safeColumn('planner_items', 'done_by_employee_id')) {
                $updates['done_by_employee_id'] = $this->pmoIsDoneStatus($status) ? $employeeId : null;
            }

            if ($this->safeColumn('planner_items', 'done_by')) {
                $updates['done_by'] = $this->pmoIsDoneStatus($status) ? $employeeId : null;
            }

            $item->update($updates);

            $this->pmoSyncPlannerItemStatusToSource($item, $plan, $status, $employeeId, $data['note'] ?? null);
            $this->pmoStorePlannerStatusHistory($item->fresh(), $plan, $oldStatus, $status, $employeeId, $data['note'] ?? null, $now);

            if ($this->pmoIsDoneStatus($status) && trim((string) ($completionReport['text'] ?? '')) !== '') {
                $reportSaved = $this->pmoStoreCompletionReport($item->fresh(), $plan, $employeeId, $completionReport, $now);
            }
        });

        $fresh = $item->fresh();
        $doneByEmployeeId = $this->pmoPlannerDoneByEmployeeId($fresh);
        $doneByEmployee = $this->pmoEmployeeMini($doneByEmployeeId);

        return response()->json([
            'ok' => true,
            'message' => 'Status wurde aktualisiert.',
            'item' => [
                'id' => (int) $fresh->id,
                'planner_item_id' => (int) $fresh->id,
                'source_type' => $fresh->source_type,
                'source_id' => (int) $fresh->source_id,
                'status' => $fresh->status,
                'status_label' => $this->pmoStatusLabel($fresh->status),
                'is_done' => $this->pmoIsDoneStatus($fresh->status),
                'done_at' => $this->pmoPlannerDoneAt($fresh),
                'done_by_employee_id' => $doneByEmployeeId,
                'done_by_employee' => $doneByEmployee,
                'done_by_name' => $doneByEmployee['full_name'] ?? null,
                'status_history' => $this->pmoLoadPlannerStatusHistory((int) $fresh->id),
                'comments' => $this->pmoLoadPlannerComments((int) $fresh->id),
                'reports' => $this->pmoLoadPlannerComments((int) $fresh->id),
            ],
            'report_saved' => $reportSaved,
        ]);
    }

    private function pmoNormalizePlannerStatus(string $status): string
    {
        $status = mb_strtolower(trim($status));
        $status = str_replace(['-', ' '], '_', $status);

        return match ($status) {
            /*
             |--------------------------------------------------------------------------
             | Planner canonical statuses
             |--------------------------------------------------------------------------
             | The planner UI only needs these few statuses. Every source controller
             | can still keep its own real database value through the mapping methods
             | below.
             */
            'completed', 'complete', 'finished', 'finish', 'closed', 'close', 'ended', 'end',
            'done', 'erledigt', 'beendet', 'geschlossen' => 'done',

            'in_progress', 'progress', 'process', 'in_bearbeitung', 'bearbeitung',
            'on_progress', 'on_going', 'working', 'arbeit', 'in_arbeit', 'inarbeit' => 'in_progress',

            'scheduled', 'schedule', 'planned', 'confirmed', 'geplant', 'terminiert' => 'planned',

            'pause', 'paused', 'pausiert', 'mittag', 'mittagessen' => 'paused',

            'blocked', 'blockiert', 'reject', 'rejected' => 'blocked',

            'cancel', 'canceled', 'cancelled', 'storniert', 'junk' => 'cancelled',

            'offen', 'open', 'new', 'send', 'accepted' => 'open',

            default => in_array($status, ['open', 'planned', 'in_progress', 'done', 'blocked', 'paused', 'cancelled'], true)
            ? $status
            : 'open',
        };
    }

    private function pmoIsDoneStatus(?string $status): bool
    {
        return $this->pmoNormalizePlannerStatus((string) $status) === 'done';
    }

    private function pmoStatusLabel(?string $status): string
    {
        return match ($this->pmoNormalizePlannerStatus((string) $status)) {
            'done' => 'Erledigt',
            'in_progress' => 'In Arbeit',
            'planned' => 'Geplant',
            'blocked' => 'Blockiert',
            'paused' => 'Pausiert',
            'cancelled' => 'Storniert',
            default => 'Offen',
        };
    }

    private function pmoEmployeeMini(?int $employeeId): ?array
    {
        $employeeId = $employeeId ? (int) $employeeId : 0;

        if ($employeeId <= 0 || !Schema::hasTable('employees')) {
            return null;
        }

        $columns = ['id'];

        foreach (['name', 'lastname', 'title', 'image', 'photo', 'email', 'phone'] as $column) {
            if ($this->safeColumn('employees', $column)) {
                $columns[] = $column;
            }
        }

        $employee = DB::table('employees')->select($columns)->where('id', $employeeId)->first();

        if (!$employee) {
            return null;
        }

        $fullName = trim((($employee->title ?? '') ? ($employee->title . ' ') : '') . (($employee->name ?? '') . ' ' . ($employee->lastname ?? '')));

        return [
            'id' => (int) $employee->id,
            'name' => $employee->name ?? null,
            'lastname' => $employee->lastname ?? null,
            'full_name' => $fullName !== '' ? $fullName : ('Mitarbeiter #' . $employee->id),
            'photo_url' => $this->resolvePhotoUrl($employee),
            'email' => $employee->email ?? null,
            'phone' => $employee->phone ?? null,
        ];
    }

    private function pmoPlannerDoneAt($plannerItem): ?string
    {
        foreach (['done_at', 'completed_at', 'finished_at', 'closed_at'] as $column) {
            if (isset($plannerItem->{$column}) && !empty($plannerItem->{$column})) {
                return Carbon::parse($plannerItem->{$column})->toDateTimeString();
            }
        }

        if ($this->pmoIsDoneStatus((string) ($plannerItem->status ?? '')) && !empty($plannerItem->updated_at)) {
            return Carbon::parse($plannerItem->updated_at)->toDateTimeString();
        }

        return null;
    }

    private function pmoPlannerDoneByEmployeeId($plannerItem): ?int
    {
        foreach (['done_by_employee_id', 'completed_by_employee_id', 'done_by', 'completed_by'] as $column) {
            if (isset($plannerItem->{$column}) && is_numeric($plannerItem->{$column}) && (int) $plannerItem->{$column} > 0) {
                return (int) $plannerItem->{$column};
            }
        }

        return null;
    }

    private function pmoStorePlannerStatusHistory(PlannerItem $item, PlannerPlan $plan, string $oldStatus, string $newStatus, ?int $employeeId = null, ?string $note = null, $changedAt = null): void
    {
        if (!Schema::hasTable('planner_item_status_histories')) {
            return;
        }

        $changedAt = $changedAt ? Carbon::parse($changedAt) : now();
        $oldStatus = $this->pmoNormalizePlannerStatus($oldStatus);
        $newStatus = $this->pmoNormalizePlannerStatus($newStatus);

        DB::table('planner_item_status_histories')->insert(array_filter([
            'planner_item_id' => (int) $item->id,
            'planner_plan_id' => (int) $plan->id,
            'project_id' => $plan->project_id ? (int) $plan->project_id : null,
            'source_type' => $item->source_type,
            'source_id' => $item->source_id ? (int) $item->source_id : null,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'status_label' => $this->pmoStatusLabel($newStatus),
            'changed_by_employee_id' => $employeeId,
            'note' => $note,
            'changed_at' => $changedAt->toDateTimeString(),
            'meta' => json_encode([
                'old_label' => $this->pmoStatusLabel($oldStatus),
                'new_label' => $this->pmoStatusLabel($newStatus),
                'auth_user_id' => auth()->id(),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ], fn($value) => $value !== null));
    }

    private function pmoLoadPlannerStatusHistory(int $plannerItemId, int $limit = 20): array
    {
        if ($plannerItemId <= 0 || !Schema::hasTable('planner_item_status_histories')) {
            return [];
        }

        $rows = DB::table('planner_item_status_histories')
            ->where('planner_item_id', $plannerItemId)
            ->orderByDesc('changed_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        $employeeIds = $rows->pluck('changed_by_employee_id')->filter()->map(fn($id) => (int) $id)->unique()->values();
        $employees = $employeeIds->isNotEmpty() && Schema::hasTable('employees')
            ? DB::table('employees')->whereIn('id', $employeeIds)->get()->keyBy('id')
            : collect();

        return $rows->map(function ($row) use ($employees) {
            $employee = $row->changed_by_employee_id ? $employees->get((int) $row->changed_by_employee_id) : null;
            $employeeName = $employee ? trim((($employee->title ?? '') ? ($employee->title . ' ') : '') . (($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))) : null;

            return [
                'id' => (int) $row->id,
                'old_status' => $row->old_status,
                'old_status_label' => $this->pmoStatusLabel($row->old_status),
                'new_status' => $row->new_status,
                'new_status_label' => $this->pmoStatusLabel($row->new_status),
                'status_label' => $row->status_label ?: $this->pmoStatusLabel($row->new_status),
                'changed_at' => $row->changed_at,
                'changed_at_label' => $row->changed_at ? Carbon::parse($row->changed_at)->format('d.m.Y H:i') : null,
                'changed_by_employee_id' => $row->changed_by_employee_id ? (int) $row->changed_by_employee_id : null,
                'changed_by_name' => $employeeName ?: ($row->changed_by_employee_id ? ('Mitarbeiter #' . $row->changed_by_employee_id) : null),
                'note' => $row->note,
            ];
        })->values()->all();
    }

    private function pmoSourceDoneAt($row): ?string
    {
        foreach (['done_at', 'completed_at', 'finished_at', 'closed_at', 'done_date'] as $column) {
            if (isset($row->{$column}) && !empty($row->{$column})) {
                try {
                    return Carbon::parse($row->{$column})->toDateTimeString();
                } catch (\Throwable $e) {
                    return (string) $row->{$column};
                }
            }
        }

        return null;
    }

    private function pmoSourceDoneByEmployeeId($row): ?int
    {
        foreach (['done_by_employee_id', 'completed_by_employee_id', 'done_by', 'completed_by'] as $column) {
            if (isset($row->{$column}) && is_numeric($row->{$column}) && (int) $row->{$column} > 0) {
                return (int) $row->{$column};
            }
        }

        return null;
    }

    private function pmoReadSourceStatusValue(object $row, string $sourceType = ''): string
    {
        /*
         |--------------------------------------------------------------------------
         | IMPORTANT
         |--------------------------------------------------------------------------
         | Every source controller uses different status values:
         |
         | personal_tasks      => task_status: completed / on_progress / pause / cancel
         | problems            => status: offen / process / end / junk
         | ticket_tasks        => status + is_done: done / open / is_done = 1
         | kanban_lead_tasks   => status: open / scheduled / in_progress / done
         | customer_histories  => is_done + done_date / done_by
         |
         | This method reads those real values and converts them only for the planner UI.
         */
        $type = mb_strtolower(trim($sourceType));

        if ($type === 'personal_task') {
            $value = mb_strtolower(trim((string) ($row->task_status ?? $row->status ?? '')));

            return match ($value) {
                'completed' => 'done',
                'on_progress', 'on_going', 'working' => 'in_progress',
                'pause' => 'paused',
                'cancel', 'junk' => 'cancelled',
                'rejected', 'reject' => 'blocked',
                'send', 'new', 'accepted', 'open' => 'open',
                default => $this->pmoNormalizePlannerStatus($value),
            };
        }

        if ($type === 'ticket') {
            $value = mb_strtolower(trim((string) ($row->status ?? $row->problem_status ?? $row->ticket_status ?? '')));

            return match ($value) {
                'end', 'ended', 'beendet', 'done' => 'done',
                'process', 'in_bearbeitung' => 'in_progress',
                'offen', 'open' => 'open',
                'junk', 'cancel', 'cancelled', 'canceled' => 'cancelled',
                default => $this->pmoNormalizePlannerStatus($value),
            };
        }

        if ($type === 'ticket_task') {
            if (isset($row->is_done) && (int) $row->is_done === 1) {
                return 'done';
            }

            $value = mb_strtolower(trim((string) ($row->status ?? '')));

            return match ($value) {
                'done', 'erledigt', 'closed', 'geschlossen' => 'done',
                'process', 'in_bearbeitung', 'in_progress', 'working' => 'in_progress',
                'pause', 'paused' => 'paused',
                'cancel', 'cancelled', 'canceled' => 'cancelled',
                'blocked', 'blockiert' => 'blocked',
                default => $this->pmoNormalizePlannerStatus($value),
            };
        }

        if ($type === 'kanban_task') {
            $value = mb_strtolower(trim((string) ($row->status ?? '')));

            return match ($value) {
                'done' => 'done',
                'in_progress' => 'in_progress',
                'scheduled' => 'planned',
                'cancelled', 'canceled' => 'cancelled',
                default => $this->pmoNormalizePlannerStatus($value),
            };
        }

        if ($type === 'appointment') {
            $value = mb_strtolower(trim((string) ($row->status ?? $row->appointment_status ?? $row->work_status ?? '')));

            return match ($value) {
                'done', 'completed', 'finished', 'closed', 'ended' => 'done',
                'process', 'in_progress', 'started', 'working' => 'in_progress',
                'planned', 'scheduled', 'confirmed' => 'planned',
                'cancel', 'cancelled', 'canceled' => 'cancelled',
                default => $this->pmoNormalizePlannerStatus($value),
            };
        }

        if ($type === 'task_phase') {
            return $this->pmoNormalizePlannerStatus((string) ($row->status ?? 'open'));
        }

        if ($type === 'phase_activity') {
            if (isset($row->is_done) && ((int) $row->is_done === 1 || strtolower((string) $row->is_done) === 'done')) {
                return 'done';
            }

            if (isset($row->is_completed) && (int) $row->is_completed === 1) {
                return 'done';
            }

            return $this->pmoNormalizePlannerStatus((string) ($row->status ?? 'open'));
        }

        foreach (['status', 'task_status', 'work_status', 'problem_status', 'ticket_status', 'appointment_status', 'board_column'] as $column) {
            if (isset($row->{$column}) && trim((string) $row->{$column}) !== '') {
                return $this->pmoNormalizePlannerStatus((string) $row->{$column});
            }
        }

        if (isset($row->is_done) && (int) $row->is_done === 1) {
            return 'done';
        }

        if (isset($row->is_completed) && (int) $row->is_completed === 1) {
            return 'done';
        }

        return 'open';
    }

    private function pmoSyncPlannerItemStatusToSource(PlannerItem $item, PlannerPlan $plan, string $status, ?int $employeeId = null, ?string $note = null): void
    {
        $sourceType = mb_strtolower(trim((string) $item->source_type));
        $sourceId = (int) $item->source_id;
        $status = $this->pmoNormalizePlannerStatus($status);

        if ($sourceId <= 0) {
            return;
        }

        match ($sourceType) {
            'personal_task' => $this->pmoUpdatePersonalTaskSourceStatus($sourceId, $status, $employeeId, $note),
            'ticket' => $this->pmoUpdateProblemSourceStatus($sourceId, $status, $employeeId, $note),
            'ticket_task' => $this->pmoUpdateTicketTaskSourceStatus($sourceId, $status, $employeeId, $note),
            'kanban_task' => $this->pmoUpdateKanbanTaskSourceStatus($sourceId, $status, $employeeId, $note),
            'appointment' => $this->pmoUpdateAppointmentSourceStatus($sourceId, $status, $employeeId, $note),
            'phase_activity' => $this->pmoUpdatePhaseActivityStatus($plan, $sourceId, $status, $employeeId, $note),
            default => null,
        };
    }

    private function pmoStoreCompletionReport(PlannerItem $item, PlannerPlan $plan, ?int $employeeId, array $payload, $now): bool
    {
        $text = trim((string) ($payload['text'] ?? ''));

        if ($text === '') {
            return false;
        }

        $employeeId = $employeeId ?: $this->authEmployeeId();
        $sourceType = mb_strtolower(trim((string) ($item->source_type ?? '')));
        $sourceId = (int) ($item->source_id ?? 0);
        $saved = false;

        /*
        |--------------------------------------------------------------------------
        | Always save a planner mirror.
        |--------------------------------------------------------------------------
        | The source tables below keep the report in the correct business module,
        | while this mirror makes the report immediately readable inside the
        | planner drawer/mobile task card comments tab.
        */
        $saved = $this->pmoStorePlannerItemReportMirror($item, $plan, $employeeId, $payload, $now) || $saved;

        if ($sourceId <= 0) {
            return $saved;
        }

        $saved = match ($sourceType) {
            'personal_task' => $this->pmoStorePersonalTaskCompletionReport($sourceId, $employeeId, $payload, $now) || $saved,
            'appointment' => $this->pmoStoreAppointmentCompletionReport($sourceId, $item, $employeeId, $payload, $now) || $saved,
            'ticket' => $this->pmoStoreTicketCompletionReport($sourceId, $item, $plan, $employeeId, $payload, $now) || $saved,
            'ticket_task' => $this->pmoStoreTicketTaskCompletionReport($sourceId, $item, $plan, $employeeId, $payload, $now) || $saved,
            'kanban_task', 'phase_activity', 'task_phase', 'master_set' => $this->pmoStoreCustomerCompletionReport($item, $plan, $employeeId, $payload, $now) || $saved,
            default => $saved,
        };

        return $saved;
    }

    private function pmoCompletionReportBody(array $payload): string
    {
        $text = trim((string) ($payload['text'] ?? ''));
        $nextStep = trim((string) ($payload['next_step'] ?? ''));
        $dueDate = trim((string) ($payload['due_date'] ?? ''));

        $parts = [$text];

        if ($nextStep !== '') {
            $parts[] = 'Nächster Schritt: ' . $nextStep;
        }

        if ($dueDate !== '') {
            $parts[] = 'Fällig am: ' . $dueDate;
        }

        return trim(implode("\n\n", array_filter($parts)));
    }

    private function pmoStorePlannerItemReportMirror(PlannerItem $item, PlannerPlan $plan, ?int $employeeId, array $payload, $now): bool
    {
        $table = $this->plannerCommentTable();

        if (!$table) {
            return false;
        }

        $body = $this->pmoCompletionReportBody($payload);

        if ($body === '') {
            return false;
        }

        $context = $this->plannerContextColumns($plan, $item);

        $this->plannerInsertRow($table, array_merge($context, [
            'planner_item_id' => (int) $item->id,
            'source_type' => $item->source_type ?? 'planner_item',
            'source_id' => $item->source_id ?? $item->id,
            'title' => 'Fertig-Bericht',
            'subject' => 'Fertig-Bericht',
            'body' => $body,
            'comment' => $body,
            'report' => $body,
            'description' => $body,
            'author_name' => auth()->user()?->name ?? null,
            'created_by_employee_id' => $employeeId,
            'employee_id' => $employeeId,
            'created_by' => $employeeId ?? auth()->id(),
            'created_at' => $now,
            'updated_at' => $now,
        ]));

        return true;
    }

    private function pmoStorePersonalTaskCompletionReport(int $taskId, ?int $employeeId, array $payload, $now): bool
    {
        if (!Schema::hasTable('personal_task_comments')) {
            return false;
        }

        $body = $this->pmoCompletionReportBody($payload);

        if ($body === '') {
            return false;
        }

        $this->plannerInsertRow('personal_task_comments', [
            'task_id' => $taskId,
            'comment_by' => $employeeId,
            'comment' => $body,
            'status' => 'report',
            'parent_id' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return true;
    }

    private function pmoStoreAppointmentCompletionReport(int $appointmentId, PlannerItem $item, ?int $employeeId, array $payload, $now): bool
    {
        if (!Schema::hasTable('appointment_reports')) {
            return false;
        }

        $body = $this->pmoCompletionReportBody($payload);

        if ($body === '') {
            return false;
        }

        $this->plannerInsertRow('appointment_reports', [
            'employee_id' => $employeeId,
            'appointment_id' => $appointmentId,
            'task_id' => (int) $item->id,
            'type' => 'planner_done',
            'report' => $body,
            'report_date' => $now->toDateString(),
            'next_step' => $payload['next_step'] ?: null,
            'due_date' => $payload['due_date'] ?: null,
            'report_by' => $employeeId,
            'comments' => json_encode(['items' => []], JSON_UNESCAPED_UNICODE),
            'meta' => json_encode([
                'planner_item_id' => (int) $item->id,
                'source_type' => $item->source_type,
                'source_id' => (int) $item->source_id,
            ], JSON_UNESCAPED_UNICODE),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return true;
    }

    private function pmoStoreTicketCompletionReport(int $ticketId, PlannerItem $item, PlannerPlan $plan, ?int $employeeId, array $payload, $now): bool
    {
        if (!Schema::hasTable('ticket_reports')) {
            return false;
        }

        $body = $this->pmoCompletionReportBody($payload);

        if ($body === '') {
            return false;
        }

        $ctx = $this->plannerItemCustomerContext($plan, $item);

        $this->plannerInsertRow('ticket_reports', [
            'ticket_id' => $ticketId,
            'employee_id' => $employeeId,
            'customer_id' => $ctx['customer_id'],
            'alternative_id' => $ctx['alternative_id'],
            'product_id' => $ctx['product_id'],
            'title' => 'Fertig-Bericht',
            'report' => $body,
            'language' => 'de',
            'report_date' => $now,
            'likes' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return true;
    }

    private function pmoStoreTicketTaskCompletionReport(int $ticketTaskId, PlannerItem $item, PlannerPlan $plan, ?int $employeeId, array $payload, $now): bool
    {
        if (!Schema::hasTable('ticket_tasks')) {
            return false;
        }

        $row = DB::table('ticket_tasks')->where('id', $ticketTaskId)->first();

        if (!$row) {
            return false;
        }

        $ticketId = null;

        foreach (['problem_id', 'ticket_id', 'problem', 'ticket'] as $column) {
            if (isset($row->{$column}) && is_numeric($row->{$column}) && (int) $row->{$column} > 0) {
                $ticketId = (int) $row->{$column};
                break;
            }
        }

        if (!$ticketId) {
            return $this->pmoStoreCustomerCompletionReport($item, $plan, $employeeId, $payload, $now);
        }

        return $this->pmoStoreTicketCompletionReport($ticketId, $item, $plan, $employeeId, $payload, $now);
    }

    private function pmoStoreCustomerCompletionReport(PlannerItem $item, PlannerPlan $plan, ?int $employeeId, array $payload, $now): bool
    {
        if (!Schema::hasTable('customer_reports')) {
            return false;
        }

        $body = $this->pmoCompletionReportBody($payload);

        if ($body === '') {
            return false;
        }

        $ctx = $this->plannerItemCustomerContext($plan, $item);

        if (empty($ctx['customer_id'])) {
            return false;
        }

        $this->plannerInsertRow('customer_reports', [
            'customer_id' => $ctx['customer_id'],
            'alternative_id' => $ctx['alternative_id'],
            'product_id' => $ctx['product_id'],
            'report_by' => $employeeId,
            'stage' => 'planner_done',
            'report' => 'Fertig-Bericht',
            'report_details' => json_encode([
                'text' => $body,
                'planner_item_id' => (int) $item->id,
                'source_type' => $item->source_type,
                'source_id' => (int) $item->source_id,
                'next_step' => $payload['next_step'] ?: null,
                'due_date' => $payload['due_date'] ?: null,
                'created_at' => $now->toIso8601String(),
            ], JSON_UNESCAPED_UNICODE),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return true;
    }

    private function pmoSourceStatusForPersonalTask(string $plannerStatus): string
    {
        return match ($this->pmoNormalizePlannerStatus($plannerStatus)) {
            'done' => 'completed',
            'in_progress' => 'on_progress',
            'paused', 'blocked' => 'pause',
            'cancelled' => 'cancel',
            default => 'open',
        };
    }

    private function pmoSourceStatusForProblem(string $plannerStatus): string
    {
        return match ($this->pmoNormalizePlannerStatus($plannerStatus)) {
            'done' => 'end',
            'in_progress' => 'process',
            'cancelled' => 'junk',
            default => 'offen',
        };
    }

    private function pmoSourceStatusForTicketTask(string $plannerStatus): string
    {
        return match ($this->pmoNormalizePlannerStatus($plannerStatus)) {
            'done' => 'done',
            'in_progress' => 'in_progress',
            'paused' => 'paused',
            'blocked' => 'blocked',
            'cancelled' => 'cancelled',
            default => 'open',
        };
    }

    private function pmoSourceStatusForKanbanTask(string $plannerStatus): string
    {
        return match ($this->pmoNormalizePlannerStatus($plannerStatus)) {
            'done' => 'done',
            'in_progress' => 'in_progress',
            'planned' => 'scheduled',
            'cancelled' => 'cancelled',
            default => 'open',
        };
    }

    private function pmoSourceStatusForAppointment(string $plannerStatus): string
    {
        return match ($this->pmoNormalizePlannerStatus($plannerStatus)) {
            'done' => 'done',
            'in_progress' => 'in_progress',
            'planned' => 'planned',
            'paused' => 'paused',
            'cancelled' => 'cancelled',
            default => 'open',
        };
    }

    private function pmoUpdatePersonalTaskSourceStatus(int $sourceId, string $plannerStatus, ?int $employeeId = null, ?string $note = null): void
    {
        if (!Schema::hasTable('personal_tasks')) {
            return;
        }

        $now = now();
        $done = $this->pmoIsDoneStatus($plannerStatus);
        $sourceStatus = $this->pmoSourceStatusForPersonalTask($plannerStatus);
        $updates = [];

        if ($this->safeColumn('personal_tasks', 'task_status')) {
            $updates['task_status'] = $sourceStatus;
        } elseif ($this->safeColumn('personal_tasks', 'status')) {
            $updates['status'] = $sourceStatus;
        }

        if ($this->safeColumn('personal_tasks', 'progress')) {
            if ($done) {
                $updates['progress'] = 100;
            } elseif ($this->pmoNormalizePlannerStatus($plannerStatus) === 'in_progress') {
                $updates['progress'] = DB::raw('CASE WHEN COALESCE(progress, 0) > 0 THEN progress ELSE 1 END');
            }
        }

        if ($this->safeColumn('personal_tasks', 'archived_at')) {
            $updates['archived_at'] = $done ? $now : null;
        }

        if (in_array($sourceStatus, ['completed', 'cancel', 'junk', 'rejected'], true)) {
            foreach (['next_reminder_at', 'next_repeat_at'] as $column) {
                if ($this->safeColumn('personal_tasks', $column)) {
                    $updates[$column] = null;
                }
            }
        }

        if ($note !== null && $this->safeColumn('personal_tasks', 'status_note')) {
            $updates['status_note'] = $note;
        }

        if ($this->safeColumn('personal_tasks', 'updated_at')) {
            $updates['updated_at'] = $now;
        }

        if (!empty($updates)) {
            DB::table('personal_tasks')->where('id', $sourceId)->update($updates);
        }

        if ($done && Schema::hasTable('personal_task_keys')) {
            $keyUpdates = [];

            foreach ([
                'status' => 'completed',
                'is_completed' => 1,
                'done_status' => 'completed',
                'done_date' => $now,
            ] as $column => $value) {
                if ($this->safeColumn('personal_task_keys', $column)) {
                    $keyUpdates[$column] = $value;
                }
            }

            if ($this->safeColumn('personal_task_keys', 'done_by')) {
                $keyUpdates['done_by'] = $employeeId;
            }

            if ($this->safeColumn('personal_task_keys', 'updated_at')) {
                $keyUpdates['updated_at'] = $now;
            }

            if (!empty($keyUpdates)) {
                DB::table('personal_task_keys')
                    ->where('personal_task_id', $sourceId)
                    ->update($keyUpdates);
            }
        }

        if ($employeeId && Schema::hasTable('employees_personal_tasks')) {
            $pivotStatus = match ($this->pmoNormalizePlannerStatus($plannerStatus)) {
                'done' => 'completed',
                'in_progress' => 'on_progress',
                'paused', 'blocked' => 'pause',
                'cancelled' => 'cancel',
                default => 'accepted',
            };

            $pivotUpdates = [];

            foreach ([
                'status' => $pivotStatus,
                'change_date' => $now->toDateString(),
                'changed_by' => $employeeId,
                'change_reason' => $note ?: ('Planner status: ' . $this->pmoStatusLabel($plannerStatus)),
            ] as $column => $value) {
                if ($this->safeColumn('employees_personal_tasks', $column)) {
                    $pivotUpdates[$column] = $value;
                }
            }

            if ($this->safeColumn('employees_personal_tasks', 'updated_at')) {
                $pivotUpdates['updated_at'] = $now;
            }

            if (!empty($pivotUpdates)) {
                DB::table('employees_personal_tasks')
                    ->where('task_id', $sourceId)
                    ->where('employee_id', $employeeId)
                    ->update($pivotUpdates);
            }
        }
    }

    private function pmoUpdateProblemSourceStatus(int $sourceId, string $plannerStatus, ?int $employeeId = null, ?string $note = null): void
    {
        if (!Schema::hasTable('problems')) {
            return;
        }

        $now = now();
        $done = $this->pmoIsDoneStatus($plannerStatus);
        $sourceStatus = $this->pmoSourceStatusForProblem($plannerStatus);
        $updates = [];

        if ($this->safeColumn('problems', 'status')) {
            $updates['status'] = $sourceStatus;
        }

        if ($this->safeColumn('problems', 'edit_user')) {
            $updates['edit_user'] = $employeeId;
        }

        if ($this->safeColumn('problems', 'edit_date')) {
            $updates['edit_date'] = $now->toDateString();
        }

        foreach (['is_done', 'is_completed', 'completed'] as $column) {
            if ($this->safeColumn('problems', $column)) {
                $updates[$column] = $done ? 1 : 0;
            }
        }

        foreach (['done_at', 'completed_at', 'finished_at', 'closed_at'] as $column) {
            if ($this->safeColumn('problems', $column)) {
                $updates[$column] = $done ? $now : null;
            }
        }

        foreach (['done_by_employee_id', 'completed_by_employee_id', 'done_by', 'completed_by'] as $column) {
            if ($this->safeColumn('problems', $column)) {
                $updates[$column] = $done ? $employeeId : null;
            }
        }

        if ($note !== null && $this->safeColumn('problems', 'status_note')) {
            $updates['status_note'] = $note;
        }

        if ($this->safeColumn('problems', 'updated_at')) {
            $updates['updated_at'] = $now;
        }

        if (!empty($updates)) {
            DB::table('problems')->where('id', $sourceId)->update($updates);
        }
    }

    private function pmoUpdateTicketTaskSourceStatus(int $sourceId, string $plannerStatus, ?int $employeeId = null, ?string $note = null): void
    {
        if (!Schema::hasTable('ticket_tasks')) {
            return;
        }

        $now = now();
        $done = $this->pmoIsDoneStatus($plannerStatus);
        $sourceStatus = $this->pmoSourceStatusForTicketTask($plannerStatus);
        $updates = [];

        if ($this->safeColumn('ticket_tasks', 'status')) {
            $updates['status'] = $sourceStatus;
        }

        if ($this->safeColumn('ticket_tasks', 'is_done')) {
            $updates['is_done'] = $done ? 1 : 0;
        }

        foreach (['done_at', 'completed_at', 'finished_at', 'closed_at'] as $column) {
            if ($this->safeColumn('ticket_tasks', $column)) {
                $updates[$column] = $done ? $now : null;
            }
        }

        foreach (['done_by_employee_id', 'completed_by_employee_id', 'done_by', 'completed_by'] as $column) {
            if ($this->safeColumn('ticket_tasks', $column)) {
                $updates[$column] = $done ? $employeeId : null;
            }
        }

        if ($note !== null && $this->safeColumn('ticket_tasks', 'status_note')) {
            $updates['status_note'] = $note;
        }

        if ($this->safeColumn('ticket_tasks', 'updated_at')) {
            $updates['updated_at'] = $now;
        }

        if (!empty($updates)) {
            DB::table('ticket_tasks')->where('id', $sourceId)->update($updates);
        }
    }

    private function pmoUpdateKanbanTaskSourceStatus(int $sourceId, string $plannerStatus, ?int $employeeId = null, ?string $note = null): void
    {
        if (!Schema::hasTable('kanban_lead_tasks')) {
            return;
        }

        $now = now();
        $done = $this->pmoIsDoneStatus($plannerStatus);
        $sourceStatus = $this->pmoSourceStatusForKanbanTask($plannerStatus);
        $updates = [];

        if ($this->safeColumn('kanban_lead_tasks', 'status')) {
            $updates['status'] = $sourceStatus;
        }

        if ($sourceStatus === 'scheduled' && $this->safeColumn('kanban_lead_tasks', 'is_scheduled')) {
            $updates['is_scheduled'] = 1;
        }

        if ($sourceStatus === 'scheduled' && $employeeId && $this->safeColumn('kanban_lead_tasks', 'scheduled_by_employee_id')) {
            $updates['scheduled_by_employee_id'] = $employeeId;
        }

        if ($done) {
            if ($this->safeColumn('kanban_lead_tasks', 'done_at')) {
                $updates['done_at'] = $now;
            }

            if ($employeeId && $this->safeColumn('kanban_lead_tasks', 'done_by_employee_id')) {
                $updates['done_by_employee_id'] = $employeeId;
            }
        } else {
            foreach (['done_at', 'done_by_employee_id'] as $column) {
                if ($this->safeColumn('kanban_lead_tasks', $column)) {
                    $updates[$column] = null;
                }
            }
        }

        if ($note !== null && $this->safeColumn('kanban_lead_tasks', 'internal_note')) {
            $updates['internal_note'] = $note;
        }

        if ($this->safeColumn('kanban_lead_tasks', 'updated_at')) {
            $updates['updated_at'] = $now;
        }

        if (!empty($updates)) {
            DB::table('kanban_lead_tasks')->where('id', $sourceId)->update($updates);
        }
    }

    private function pmoUpdateAppointmentSourceStatus(int $sourceId, string $plannerStatus, ?int $employeeId = null, ?string $note = null): void
    {
        if (!Schema::hasTable('main_appointments')) {
            return;
        }

        $now = now();
        $done = $this->pmoIsDoneStatus($plannerStatus);
        $sourceStatus = $this->pmoSourceStatusForAppointment($plannerStatus);
        $updates = [];

        foreach (['status', 'appointment_status', 'work_status'] as $column) {
            if ($this->safeColumn('main_appointments', $column)) {
                $updates[$column] = $sourceStatus;
                break;
            }
        }

        foreach (['is_done', 'is_completed', 'completed'] as $column) {
            if ($this->safeColumn('main_appointments', $column)) {
                $updates[$column] = $done ? 1 : 0;
            }
        }

        foreach (['done_at', 'completed_at', 'finished_at', 'closed_at'] as $column) {
            if ($this->safeColumn('main_appointments', $column)) {
                $updates[$column] = $done ? $now : null;
            }
        }

        foreach (['done_by_employee_id', 'completed_by_employee_id', 'done_by', 'completed_by'] as $column) {
            if ($this->safeColumn('main_appointments', $column)) {
                $updates[$column] = $done ? $employeeId : null;
            }
        }

        if ($note !== null && $this->safeColumn('main_appointments', 'note')) {
            $updates['note'] = trim((string) (($note ? $note . "\n\n" : '') . 'Planner Status: ' . $this->pmoStatusLabel($plannerStatus)));
        }

        if ($this->safeColumn('main_appointments', 'updated_at')) {
            $updates['updated_at'] = $now;
        }

        if (!empty($updates)) {
            DB::table('main_appointments')->where('id', $sourceId)->update($updates);
        }
    }

    private function pmoUpdatePhaseActivityStatus(PlannerPlan $plan, int $activityId, string $status, ?int $employeeId = null, ?string $note = null): void
    {
        if (!Schema::hasTable('customer_histories')) {
            return;
        }

        $project = $this->planProject($plan);
        $now = now();
        $done = $this->pmoIsDoneStatus($status);

        $where = [
            'customer_id' => (int) ($plan->customer_id ?? $project?->customer_id ?? 0),
            'activity_id' => $activityId,
        ];

        if (($project?->product_id ?? null) && $this->safeColumn('customer_histories', 'product_id')) {
            $where['product_id'] = (int) $project->product_id;
        }

        if (($project?->alternative_id ?? null) && $this->safeColumn('customer_histories', 'alternative_id')) {
            $where['alternative_id'] = (int) $project->alternative_id;
        }

        $sourceStatus = $done
            ? 'completed'
            : match ($this->pmoNormalizePlannerStatus($status)) {
                'in_progress' => 'in_progress',
                'paused' => 'pause',
                'cancelled' => 'cancel',
                default => 'open',
            };

        $updates = [];

        if ($this->safeColumn('customer_histories', 'is_done')) {
            $updates['is_done'] = $done ? 1 : 0;
        }

        if ($this->safeColumn('customer_histories', 'status')) {
            $updates['status'] = $sourceStatus;
        }

        if ($this->safeColumn('customer_histories', 'done_by')) {
            $updates['done_by'] = $done ? $employeeId : null;
        }

        if ($this->safeColumn('customer_histories', 'done_date')) {
            $updates['done_date'] = $done ? $now : null;
        }

        if ($note !== null && $this->safeColumn('customer_histories', 'note')) {
            $updates['note'] = $note;
        }

        if ($this->safeColumn('customer_histories', 'updated_at')) {
            $updates['updated_at'] = $now;
        }

        if (!empty($updates)) {
            $values = $updates;

            if ($this->safeColumn('customer_histories', 'created_at')) {
                $values['created_at'] = $now;
            }

            DB::table('customer_histories')->updateOrInsert($where, $values);
        }
    }

    public function destroyItem(PlannerPlan $plan, PlannerItem $item)
    {
        if ($item->plan_id !== $plan->id)
            abort(404);
        $item->delete();

        if ($item->source_type === 'appointment' && $item->source_id) {
            if (Schema::hasColumn('main_appointments', 'planner_item_id')) {
                DB::table('main_appointments')->where('id', $item->source_id)->update(['planner_item_id' => null]);
            }
        }
        if ($item->source_type === 'ticket' && $item->source_id) {
            if (Schema::hasColumn('problems', 'planner_item_id')) {
                DB::table('problems')->where('id', $item->source_id)->update(['planner_item_id' => null]);
            }
        }

        return response()->json(['ok' => true]);
    }

    public function move(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|integer',
            'item_id' => 'required|integer',
            'to_manager_id' => 'nullable|integer',
            'position' => 'nullable|integer'
        ]);
        $item = PlannerItem::findOrFail($data['item_id']);
        if ($item->plan_id != $data['plan_id'])
            abort(403);
        DB::table('planner_item_employees')->where('planner_item_id', $item->id)->where('role', 'lead')->delete();

        if ($data['to_manager_id']) {
            DB::table('planner_item_employees')->insert([
                'planner_item_id' => $item->id,
                'employee_id' => $data['to_manager_id'],
                'role' => 'lead',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $item->status = 'planned';
        } else {
            $item->status = 'open';
        }
        if (isset($data['position'])) {
            $item->sort_order = $data['position'];
        }

        $item->save();
        return response()->json(['ok' => true]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|integer',
            'source_type' => 'required|string',
            'source_id' => 'nullable|integer',
            'to_manager_id' => 'nullable|integer',
            'title' => 'nullable|string',
            'pm_id' => 'nullable|integer',
            'crew_ids' => 'nullable|array',
            'planned_date' => 'nullable|string',
            'planned_time' => 'nullable|string',
            'master_set_id' => 'nullable|integer'
        ]);

        $plan = PlannerPlan::findOrFail($data['plan_id']);

        $startAt = null;
        if ($data['planned_date']) {
            $startAt = $data['planned_date'] . ' ' . ($data['planned_time'] ?: '08:00');
        }

        // --- 1. Find or Create Item ---

        // Check if item already exists (even if deleted) to restore it
        $item = PlannerItem::withTrashed()
            ->where('plan_id', $plan->id)
            ->where('source_type', $data['source_type'])
            ->where('source_id', $data['source_id'])
            ->first();

        if ($item) {
            // A. Restore existing
            if ($item->trashed()) {
                $item->restore();
            }

            // Prepare update data
            $updateData = [
                'status' => $startAt ? 'planned' : 'open',
                'planned_start_at' => $startAt,
            ];

            if (!empty($data['master_set_id']) && Schema::hasColumn('planner_items', 'master_set_id')) {
                $updateData['master_set_id'] = $data['master_set_id'];
            }

            $item->update($updateData);

            // Clear old employees to re-assign below
            DB::table('planner_item_employees')->where('planner_item_id', $item->id)->delete();

        } else {
            // B. Create New
            $createData = [
                'plan_id' => $plan->id,
                'client_uid' => (string) Str::uuid(),
                'source_type' => $data['source_type'],
                'source_id' => $data['source_id'],
                'title' => $data['title'] ?? 'Neue Aufgabe',
                'status' => $startAt ? 'planned' : 'open',
                'planned_start_at' => $startAt,
                'sort_order' => 9999
            ];

            if (!empty($data['master_set_id']) && Schema::hasColumn('planner_items', 'master_set_id')) {
                $createData['master_set_id'] = $data['master_set_id'];
            }

            $item = PlannerItem::create($createData);
        }

        // --- 2. Assign Employees ---

        $leadId = $data['to_manager_id'] ?? $data['pm_id'];

        if ($leadId) {
            DB::table('planner_item_employees')->insert([
                'planner_item_id' => $item->id,
                'employee_id' => $leadId,
                'role' => 'lead',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        if (!empty($data['crew_ids'])) {
            foreach ($data['crew_ids'] as $cid) {
                if ($cid == $leadId)
                    continue;
                DB::table('planner_item_employees')->insert([
                    'planner_item_id' => $item->id,
                    'employee_id' => $cid,
                    'role' => 'member',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // --- 3. Return Payload ---

        // Reload relationships for frontend response
        $item->load('employees');

        // If you want to return the linked Master Set data immediately to the frontend
        // so the card updates without a full refresh, load it here:
        if ($item->master_set_id) {
            // Optional: Load basic master set info if needed by frontend immediately
            // $item->load('masterSet'); 
        }

        return response()->json(['ok' => true, 'item' => $item]);
    }
    public function order(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|integer',
            'ordered_item_ids' => 'required|array'
        ]);
        $order = 1;
        foreach ($data['ordered_item_ids'] as $id) {
            PlannerItem::where('id', $id)->where('plan_id', $data['plan_id'])->update(['sort_order' => $order++]);
        }
        return response()->json(['ok' => true]);
    }

    public function managersSave(Request $request, $planId)
    {
        $plan = PlannerPlan::findOrFail($planId);
        $meta = $this->decodeJson($plan->meta);
        $meta['extra_manager_ids'] = $request->input('manager_ids', []);
        $plan->update(['meta' => json_encode($meta)]);
        return response()->json(['ok' => true]);
    }
    // =========================================================================
    // Views & Listings
    // =========================================================================

    public function index(Request $request)
    {
        $projectId = (int) $request->query('project_id', 0);
        $planId = (int) $request->query('plan_id', 0);
        $customerId = (int) $request->query('customer_id', 0);
        $productId = (int) $request->query('product_id', 0);

        /*
        |--------------------------------------------------------------------------
        | Resolve project automatically when the page is opened from old links
        |--------------------------------------------------------------------------
        | The Montage Blade needs config.initial.projectId. If it is 0, the JS does
        | not call /planner/projects/{project}/montage-work and nothing loads.
        */
        if ($projectId <= 0 && $planId > 0) {
            $plan = PlannerPlan::query()->find($planId);

            if ($plan) {
                $projectId = (int) ($plan->project_id ?? 0);
                $customerId = $customerId > 0 ? $customerId : (int) ($plan->customer_id ?? 0);
            }
        }

        if ($projectId <= 0 && $customerId > 0) {
            $projectQuery = LeadProductList::query()
                ->when($productId > 0, fn($q) => $q->where('product_id', $productId))
                ->where('customer_id', $customerId)
                ->whereIn(DB::raw('LOWER(COALESCE(status, ""))'), ['project', 'montage'])
                ->orderByDesc('id');

            $projectId = (int) ($projectQuery->value('id') ?? 0);
        }

        if ($planId <= 0 && $projectId > 0) {
            $planId = (int) (PlannerPlan::query()
                ->where('project_id', $projectId)
                ->orderByDesc('id')
                ->value('id') ?? 0);
        }

        if ($customerId <= 0 && $projectId > 0) {
            $project = LeadProductList::query()->find($projectId);

            if ($project) {
                $customerId = (int) ($project->customer_id ?? 0);
                $productId = $productId > 0 ? $productId : (int) ($project->product_id ?? 0);
            }
        }

        return view('admin.planner.index', [
            'plannerConfig' => [
                'initial' => [
                    'customerId' => $customerId,
                    'projectId' => $projectId,
                    'planId' => $planId,
                    'productId' => $productId,
                    'date' => (string) $request->query('date', now()->toDateString()),
                    'mode' => (string) $request->query('mode', 'day'),
                ],
                'endpoints' => [
                    'customers' => route('planner.customers.index'),
                    'leadProducts' => route('planner.customers.lead_products', ['customerId' => '___ID___']),
                    'phases' => route('planner.phases.activities'),
                    'employees' => route('planner.employees.active'),
                    'syncAndLoad' => route('planner.plans.sync'),

                    /*
                    |--------------------------------------------------------------------------
                    | Montage page endpoints used by resources/views/admin/planner/index.blade.php
                    |--------------------------------------------------------------------------
                    */
                    'montageWorkPayload' => route('planner.projects.montage_work', ['project' => '___PROJECT___']),
                    'projectTeamMember' => route('planner.projects.team.member', ['project' => '___PROJECT___']),
                    'projectWorkItemStore' => route('planner.projects.work_items.store', ['project' => '___PROJECT___']),

                    'objectData' => route('planner.object.data'),

                    'dnd' => [
                        'add' => route('planner.dnd.add'),
                        'move' => route('planner.dnd.move'),
                        'order' => route('planner.dnd.order'),
                    ],

                    'planItemUpdate' => route('planner.planItems.update', [
                        'plan' => '___PLAN___',
                        'item' => '___ITEM___',
                    ]),
                    'planItemDelete' => route('planner.planItems.destroy', [
                        'plan' => '___PLAN___',
                        'item' => '___ITEM___',
                    ]),

                    'dependencyStore' => route('planner.plans.dependencies.store', [
                        'plan' => '___PLAN___',
                    ]),

                    'dependencyDestroy' => route('planner.plans.dependencies.destroy', [
                        'plan' => '___PLAN___',
                    ]),

                    /*
                    |--------------------------------------------------------------------------
                    | Steps + Material endpoints used by the Montage drawer
                    |--------------------------------------------------------------------------
                    | These are URL based to avoid route-helper crashes while the routes are
                    | being cached/edited. The matching routes must still exist in web.php.
                    */
                    'itemStepStore' => url('/planner/plans/___PLAN___/items/___ITEM___/steps'),
                    'itemStepUpdate' => url('/planner/plans/___PLAN___/items/___ITEM___/steps/___STEP___'),
                    'itemStepDestroy' => url('/planner/plans/___PLAN___/items/___ITEM___/steps/___STEP___'),

                    'itemMaterialSources' => url('/planner/plans/___PLAN___/items/___ITEM___/materials/sources'),
                    'itemMaterialImportDeal' => url('/planner/plans/___PLAN___/items/___ITEM___/materials/import-deal'),
                    'itemMaterialStore' => url('/planner/plans/___PLAN___/items/___ITEM___/materials'),
                    'itemMaterialUpdate' => url('/planner/plans/___PLAN___/items/___ITEM___/materials/___MATERIAL___'),
                    'itemMaterialDestroy' => url('/planner/plans/___PLAN___/items/___ITEM___/materials/___MATERIAL___'),
                    'itemMaterialProducts' => url('/planner/plans/___PLAN___/items/___ITEM___/materials/products'),
                    'planGroupMaterialStore' => url('/planner/plans/___PLAN___/group-materials'),

                    'itemCommentStore' => url('/planner/plans/___PLAN___/items/___ITEM___/comments'),
                    'itemCommentDestroy' => url('/planner/plans/___PLAN___/items/___ITEM___/comments/___COMMENT___'),

                    'itemGalleryUpload' => url('/planner/plans/___PLAN___/items/___ITEM___/gallery'),
                    'itemGalleryDestroy' => url('/planner/plans/___PLAN___/items/___ITEM___/gallery/___IMAGE___'),
                ],
            ],
        ]);
    }

    public function show(int $planId)
    {
        $payload = $this->buildPlanPayloadWithRequestedMaterials($planId);
        if (!$payload) {
            return response()->json(['ok' => false, 'message' => 'Plan not found'], 404);
        }
        return response()->json(['ok' => true, 'data' => $payload]);
    }

    public function listView(Request $request)
    {
        $search = trim($request->input('q'));
        $status = trim($request->input('status'));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $query = PlannerPlan::query()
            ->select(
                'planner_plans.*',
                'new_leads.firma',
                'new_leads.name as cust_name',
                'new_leads.lastname as cust_last',
                'new_leads.customer_no',
                'ag.article_group as product_name',
                'ag.initial as product_initial',
                'ag.image as product_image',
                'lpl.employee_id as pm_id',
                'lpl.teams as project_teams'
            )
            ->leftJoin('new_leads', 'new_leads.id', '=', 'planner_plans.customer_id')
            ->leftJoin('lead_product_lists as lpl', 'lpl.id', '=', 'planner_plans.project_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
            ->withCount(['items as total_items'])
            ->withCount([
                'items as done_items' => function ($q) {
                    $q->whereIn('status', ['done', 'completed', 'finished']);
                }
            ]);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('planner_plans.title', 'like', "%{$search}%")
                    ->orWhere('new_leads.firma', 'like', "%{$search}%")
                    ->orWhere('new_leads.lastname', 'like', "%{$search}%")
                    ->orWhere('new_leads.customer_no', 'like', "%{$search}%");
            });
        }
        if ($status)
            $query->where('planner_plans.status', $status);
        if ($dateFrom)
            $query->whereDate('planner_plans.created_at', '>=', $dateFrom);
        if ($dateTo)
            $query->whereDate('planner_plans.created_at', '<=', $dateTo);
        $plans = $query->orderByDesc('planner_plans.updated_at')->paginate(15);
        $stats = [
            'total_active' => PlannerPlan::whereNotIn('status', ['archived', 'deleted'])->count(),
            'total_tasks' => PlannerItem::count(),
            'open_tasks' => PlannerItem::whereNotIn('status', ['done', 'completed'])->count(),
            'completion_rate' => 0
        ];

        $totalForCalc = $stats['total_tasks'];
        $doneForCalc = PlannerItem::whereIn('status', ['done', 'completed'])->count();
        if ($totalForCalc > 0) {
            $stats['completion_rate'] = round(($doneForCalc / $totalForCalc) * 100, 1);
        }
        $empIds = [];
        foreach ($plans as $p) {
            if ($p->pm_id)
                $empIds[] = $p->pm_id;
            if (!empty($p->project_teams)) {
                $decoded = json_decode($p->project_teams, true);
                if (is_array($decoded)) {
                    $list = array_is_list($decoded) ? $decoded : ($decoded['ids'] ?? $decoded['team'] ?? []);
                    foreach ($list as $val) {
                        $id = is_array($val) ? ($val['id'] ?? $val['employee_id'] ?? 0) : (int) $val;
                        if ($id > 0)
                            $empIds[] = $id;
                    }
                }
            }
        }

        $employees = DB::table('employees')
            ->whereIn('id', array_unique($empIds))
            ->get(['id', 'title', 'name', 'lastname'])
            ->keyBy('id');
        return view('admin.planner.list', compact('plans', 'stats', 'employees', 'search', 'status'));
    }

    public function itemsSearch(Request $request, $planId)
    {
        $search = trim($request->input('q'));

        $query = PlannerItem::where('plan_id', $planId)
            ->with([
                'employees' => function ($q) {
                    $q->select('employees.id', 'employees.name', 'employees.lastname');
                },
                'assets' => function ($q) {
                    // FIX: Disable global scopes here too
                    $q->select('assets.id', 'assets.item', 'assets.model')->withoutGlobalScopes();
                },
                'dependencies' => function ($q) {
                    $q->select('planner_items.id', 'planner_items.title');
                }
            ]);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
            });
        }
        $items = $query->orderBy('sort_order', 'asc')->orderBy('planned_start_at', 'asc')->paginate(5);
        $items->getCollection()->transform(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'status' => $item->status,
                'sort_order' => $item->sort_order,
                'planned_start' => $item->planned_start_at ? Carbon::parse($item->planned_start_at)->format('d.m.Y H:i') : null,
                'duration' => $item->duration_minutes,
                'lead' => $item->employees->first(fn($e) => $e->pivot->role === 'lead'),
                'team' => $item->employees->filter(fn($e) => $e->pivot->role !== 'lead')->values(),
                'assets' => $item->assets->map(fn($a) => ['name' => $a->item . ' ' . $a->model, 'qty' => $a->pivot->qty ?? 1]),
                'dependencies' => $item->dependencies,
            ];
        });
        return response()->json($items);
    }

    public function plansAjax(Request $request)
    {
        $customerId = (int) $request->query('customer_id');
        $projectId = (int) $request->query('project_id');
        $stage = trim((string) $request->query('stage', ''));
        $status = trim((string) $request->query('status', ''));
        $accountId = (int) $request->query('account_id');
        $q = \App\Models\PlannerPlan::query()
            ->when($accountId > 0, fn($x) => $x->where('account_id', $accountId))
            ->when($customerId > 0, fn($x) => $x->where('customer_id', $customerId))
            ->when($projectId > 0, fn($x) => $x->where('project_id', $projectId))
            ->when($stage !== '', fn($x) => $x->where('stage', $stage))
            ->when($status !== '', fn($x) => $x->where('status', $status))
            ->orderByDesc('id')
            ->get(['id', 'title', 'stage', 'status', 'published_at', 'created_at', 'project_id', 'customer_id']);
        return response()->json([
            'ok' => true,
            'plans' => $q,
        ]);
    }
    // =========================================================================
    // Resources & Lists (Original Logic Preserved)
    // =========================================================================
    public function employeesActive(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $employees = DB::table('employees')
            ->select(['id', 'title', 'name', 'lastname', 'email', 'phone', 'branch', 'status', 'image'])
            ->whereNull('deleted_at')
            ->whereRaw("LOWER(COALESCE(status,'')) = 'active'")
            ->when($q !== '', function ($qq) use ($q) {
                $like = '%' . mb_strtolower($q) . '%';
                $qq->where(function ($w) use ($like) {
                    $w->whereRaw('LOWER(COALESCE(name,"")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(lastname,"")) LIKE ?', [$like]);
                });
            })
            ->orderBy('lastname')
            ->get()
            ->map(function ($e) {
                $e->photo_url = $this->resolvePhotoUrl($e);
                return $e;
            });
        return response()->json([
            'ok' => true,
            'count' => $employees->count(),
            'data' => $employees,
        ]);
    }
    public function customersIndex(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $junkStatuses = ['junk', 'junks', 'trash', 'deleted', 'inactive'];
        $customers = DB::table('new_leads')
            ->select(['id', 'customer_no', 'customer_type', 'firma', 'title', 'academic_title', 'lastname', 'name', 'full_address', 'street', 'postcode', 'city', 'phone', 'telephone', 'email', 'status', 'purchase_status', 'created_at'])
            ->whereNull('deleted_at')
            ->whereRaw('LOWER(COALESCE(status,"")) NOT IN (' . collect($junkStatuses)->map(fn() => '?')->implode(',') . ')', $junkStatuses)
            ->when($q !== '', function ($qq) use ($q) {
                $like = '%' . mb_strtolower($q) . '%';
                $qq->where(function ($w) use ($like) {
                    $w->whereRaw('LOWER(COALESCE(firma,"")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(name,"")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(lastname,"")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(full_address,"")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(street,"")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(city,"")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(postcode,"")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(customer_no,"")) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(COALESCE(email,"")) LIKE ?', [$like]);
                });
            })
            ->orderByDesc('id')
            ->limit(200)
            ->get();
        return response()->json([
            'ok' => true,
            'count' => $customers->count(),
            'data' => $customers,
        ]);
    }
    public function customerLeadProducts(Request $request, int $customerId)
    {
        $alternativeId = $request->query('alternative_id');
        $customer = DB::table('new_leads')->whereNull('deleted_at')->where('id', $customerId)->first();
        if (!$customer)
            return response()->json(['ok' => false, 'message' => 'Customer not found.'], 404);
        $alternatives = DB::table('lead_alternative_adds')
            ->select(['id', 'lead_id', 'full_address', 'street', 'postcode', 'city', 'lat', 'lon', 'elevation', 'main', 'address_no', 'object_name', 'request_date', 'periority', 'status', 'stage', 'project_date', 'object_type'])
            ->whereNull('deleted_at')->where('lead_id', $customerId)->orderByDesc('id')->get();
        $hasArticleGroups = Schema::hasTable('article_groups');
        $q = DB::table('lead_product_lists as lpl')
            ->whereNull('lpl.deleted_at')
            ->where('lpl.customer_id', $customerId)
            ->when($alternativeId !== null && $alternativeId !== '', function ($qq) use ($alternativeId) {
                $qq->where('lpl.alternative_id', (int) $alternativeId);
            });
        if ($hasArticleGroups)
            $q->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id');
        $select = [
            'lpl.id',
            'lpl.customer_id',
            'lpl.alternative_id',
            'lpl.product_id',
            'lpl.status as lead_product_status',
            'lpl.work_status',
            'lpl.stage',
            'lpl.price',
            'lpl.project_minutes',
            'lpl.created_at',
        ];
        if ($hasArticleGroups) {
            $select[] = 'ag.id as article_group_id';
            $select[] = 'ag.article_group as article_group_name';
            $select[] = 'ag.initial as article_group_initial';
            $select[] = 'ag.image as article_group_image';
            $select[] = DB::raw("ag.article_group as product_name");
            $select[] = DB::raw("ag.article_group as product_title");
        } else {
            $select[] = DB::raw("lpl.product_id as article_group_id");
            $select[] = DB::raw("CONCAT('Artikelgruppe #', lpl.product_id) as article_group_name");
            $select[] = DB::raw("NULL as article_group_initial");
            $select[] = DB::raw("NULL as article_group_image");
            $select[] = DB::raw("CONCAT('Artikelgruppe #', lpl.product_id) as product_name");
            $select[] = DB::raw("CONCAT('Artikelgruppe #', lpl.product_id) as product_title");
        }
        $rows = $q->select($select)->orderByDesc('lpl.id')->get();
        $statusDE = [
            'open' => 'Offen',
            'planned' => 'Geplant',
            'in_progress' => 'In Arbeit',
            'completed' => 'Abgeschlossen',
            'done' => 'Erledigt',
            'cancel' => 'Storniert',
            'canceled' => 'Storniert',
            'archive' => 'Archiviert',
            'archived' => 'Archiviert',
        ];
        $rows = $rows->map(function ($r) use ($statusDE) {
            $st = strtolower((string) ($r->lead_product_status ?? ''));
            $r->lead_product_status_de = $st !== '' ? ($statusDE[$st] ?? ucfirst($st)) : null;
            if (trim((string) ($r->product_name ?? '')) === '') {
                $fallback = trim((string) ($r->article_group_name ?? ''));
                if ($fallback === '' && !empty($r->product_id))
                    $fallback = 'Artikelgruppe #' . $r->product_id;
                $r->product_name = $fallback;
                $r->product_title = $fallback;
            }
            return $r;
        });
        return response()->json(['ok' => true, 'customer' => $customer, 'alternatives' => $alternatives, 'lead_product_lists' => $rows]);
    }


    public function phasesAndActivities(Request $request)
    {
        // 1. Validation
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'min:1'],
            'alternative_id' => ['nullable', 'integer', 'min:1'],
            'product_id' => ['required', 'integer', 'min:1'],
            'stage' => ['nullable', 'string', 'max:255'],
            'stage_id' => ['nullable', 'integer', 'min:1'],
            'project_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $customerId = (int) $data['customer_id'];
        $alternativeId = isset($data['alternative_id']) ? (int) $data['alternative_id'] : null;
        $productId = (int) $data['product_id'];
        $projectId = isset($data['project_id']) ? (int) $data['project_id'] : null;

        // Helper: "truthy" flags
        $isTruthy = function ($v): bool {
            if (is_bool($v))
                return $v;
            if (is_numeric($v))
                return ((int) $v) === 1;
            $s = mb_strtolower(trim((string) $v));
            return in_array($s, ['1', 'true', 'yes', 'done', 'completed'], true);
        };

        // 2. Resolve Project ID
        if (!$projectId) {
            $projectId = DB::table('lead_product_lists')
                ->whereNull('deleted_at')
                ->where('customer_id', $customerId)
                ->where('product_id', $productId)
                ->when($alternativeId, fn($q) => $q->where('alternative_id', $alternativeId))
                ->orderByDesc('id')
                ->value('id');
        }

        // 3. Load Stages
        $stagesQ = DB::table('stages')
            ->select(['id', 'stage', 'product_id', 'status', 'sort_order', 'default'])
            ->whereNull('deleted_at')
            ->where('product_id', $productId)
            ->orderByRaw("COALESCE(sort_order, 999999) ASC")
            ->orderBy('id');

        if (!empty($data['stage_id'])) {
            $stagesQ->where('id', (int) $data['stage_id']);
        } elseif (!empty($data['stage'])) {
            $stagesQ->whereRaw('LOWER(stage) = ?', [mb_strtolower($data['stage'])]);
        }

        $stages = $stagesQ->get();
        $stageIds = $stages->pluck('id')->all();

        // 4. Load Phases
        $phasesQ = DB::table('task_phases')
            ->select(['id', 'product_id', 'section_id', 'section_name', 'phase_name', 'stage_id', 'status', 'count', 'order'])
            ->whereNull('deleted_at')
            ->where('product_id', $productId)
            ->where(fn($q) => $q->whereNull('status')
                ->orWhereRaw("LOWER(COALESCE(status,'')) IN ('published','active','1','true','yes')"));

        if (!empty($stageIds))
            $phasesQ->whereIn('stage_id', $stageIds);

        $phases = $phasesQ->orderByRaw("COALESCE(`order`, 999999) ASC")->orderBy('id')->get();
        $phaseIds = $phases->pluck('id')->all();

        // 5. Load Activities
        $activities = collect();
        if (!empty($phaseIds)) {
            $activities = DB::table('phase_activities')
                ->select([
                    'id',
                    'phase_id',
                    'product_id',
                    'section_id',
                    'stage_id',
                    DB::raw("COALESCE(title, description, 'Unbenannt') as title"),
                    'description',
                    'notes',
                    'duration',
                    'duration_type',
                    'status',
                    'priority',
                    'percent',
                    'usage_count',
                    'sort_order'
                ])
                ->whereNull('deleted_at')
                ->whereIn('phase_id', $phaseIds)
                ->where(fn($q) => $q->whereNull('status')->orWhereRaw("LOWER(COALESCE(status,'')) IN ('published','active','1','true','yes')"))
                ->orderByRaw("COALESCE(sort_order, 999999) ASC")
                ->orderBy('id')
                ->get();
        }
        $activityIds = $activities->pluck('id')->all();

        // =================================================================
        // 6. Master Set Data (Detailed Loading)
        // =================================================================
        $linkedMasterSets = [];

        if (!empty($activityIds)) {
            // Find Links: master_set_tasks table connects activities to sets
            $links = DB::table('master_set_tasks as mst')
                ->join('master_sets as ms', 'ms.id', '=', 'mst.master_set_id')
                ->where('ms.article_group_id', $productId)
                ->whereIn('mst.phase_activity_id', $activityIds)
                ->whereNull('ms.deleted_at')
                ->select('mst.phase_activity_id', 'ms.id as master_set_id')
                ->get();

            $setIds = $links->pluck('master_set_id')->unique()->toArray();

            if (!empty($setIds)) {
                // Eager Load with specific relationships to get Position Names and Product details
                $sets = \App\Models\MasterSet::with([
                    'components' => function ($q) {
                        $q->orderBy('sort_order');
                        // Assuming you have a 'product' relationship on MasterSetComponent model
                        // $q->with('product'); 
                    },
                    'labor' => function ($q) {
                        $q->orderBy('sort_order')->with([
                            'employee',
                            'position',   // <--- Important: Load the Position model
                            'department'
                        ]);
                    }
                ])
                    ->whereIn('id', $setIds)
                    ->get()
                    ->keyBy('id');

                // Map back to Activity ID
                foreach ($links as $link) {
                    if (isset($sets[$link->master_set_id])) {
                        $linkedMasterSets[$link->phase_activity_id] = $sets[$link->master_set_id];
                    }
                }
            }
        }

        // 7. History Map
        $historyMap = [];
        $historyEmployeeIds = [];
        if (Schema::hasTable('customer_histories') && !empty($activityIds)) {
            $histRows = DB::table('customer_histories')
                ->select([
                    'id',
                    'activity_id',
                    'is_done',
                    'done_reason',
                    'plan_time',
                    'is_time',
                    'd_time',
                    'done_date',
                    'notes',
                    'done_by',
                    'marked_by',
                    'updated_at',
                    'created_at'
                ])
                ->where('customer_id', $customerId)
                ->when($alternativeId, fn($q) => $q->where('alternative_id', $alternativeId))
                ->where('product_id', $productId)
                ->whereIn('activity_id', $activityIds)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->get();

            foreach ($histRows as $row) {
                $aid = (int) $row->activity_id;
                if (!isset($historyMap[$aid])) {
                    $historyMap[$aid] = $row;
                    if (!empty($row->done_by))
                        $historyEmployeeIds[] = (int) $row->done_by;
                    if (!empty($row->marked_by))
                        $historyEmployeeIds[] = (int) $row->marked_by;
                }
            }
        }

        // 8. Planned Map
        $plannedMap = [];
        $plannedEmployeeIds = [];
        if ($projectId && !empty($activityIds) && Schema::hasTable('planner_plans') && Schema::hasTable('planner_items')) {
            $planIds = DB::table('planner_plans')
                ->whereNull('deleted_at')
                ->where('customer_id', $customerId)
                ->where('project_id', $projectId)
                ->pluck('id');

            if ($planIds->isNotEmpty()) {
                $select = ['id', 'source_id', 'status'];
                if (Schema::hasColumn('planner_items', 'planned_start_at'))
                    $select[] = 'planned_start_at';
                if (Schema::hasColumn('planner_items', 'planned_end_at'))
                    $select[] = 'planned_end_at';

                $managerCol = null;
                foreach (['to_manager_id', 'manager_id', 'employee_id', 'assigned_to'] as $col) {
                    if (Schema::hasColumn('planner_items', $col)) {
                        $managerCol = $col;
                        $select[] = $col;
                        break;
                    }
                }

                $items = DB::table('planner_items')
                    ->select($select)
                    ->whereNull('deleted_at')
                    ->whereIn('plan_id', $planIds)
                    ->where('source_type', 'phase_activity')
                    ->whereIn('source_id', $activityIds)
                    ->get();

                foreach ($items as $it) {
                    $aid = (int) $it->source_id;
                    if (!isset($plannedMap[$aid])) {
                        $plannedMap[$aid] = [
                            'plan_item_id' => (int) $it->id,
                            'status' => $it->status,
                            'planned_start_at' => $it->planned_start_at ?? null,
                            'planned_end_at' => $it->planned_end_at ?? null,
                            'employee_id' => $managerCol ? (isset($it->{$managerCol}) ? (int) $it->{$managerCol} : null) : null,
                        ];
                    }
                    if (isset($plannedMap[$aid]['employee_id']))
                        $plannedEmployeeIds[] = (int) $plannedMap[$aid]['employee_id'];
                }
            }
        }

        // 9. Suggested Employees
        $suggestMap = [];
        $suggestEmployeeIds = [];
        if (Schema::hasTable('customer_suggest_employees') && !empty($phaseIds)) {
            $suggestRows = DB::table('customer_suggest_employees')
                ->select(['id', 'phase_id', 'employee_id', 'department_id', 'role', 'created_at'])
                ->where('customer_id', $customerId)
                ->when($alternativeId, fn($q) => $q->where('alternative_id', $alternativeId))
                ->where('product_id', $productId)
                ->whereIn('phase_id', $phaseIds)
                ->orderBy('id')
                ->get();

            foreach ($suggestRows as $sr) {
                $pid = (int) $sr->phase_id;
                $suggestMap[$pid] = $suggestMap[$pid] ?? [];
                $suggestMap[$pid][] = $sr;
                if (!empty($sr->employee_id))
                    $suggestEmployeeIds[] = (int) $sr->employee_id;
            }
        }

        // 10. Employees Lookup
        $employeeIds = array_values(array_unique(array_filter(array_merge(
            $historyEmployeeIds,
            $plannedEmployeeIds,
            $suggestEmployeeIds
        ))));

        $employeesById = [];
        if (!empty($employeeIds) && Schema::hasTable('employees')) {
            $emps = DB::table('employees')
                ->select(['id', 'title', 'name', 'midname', 'lastname', 'email', 'phone', 'image', 'status'])
                ->whereIn('id', $employeeIds)
                ->get();

            foreach ($emps as $e) {
                $full = trim(implode(' ', array_filter([$e->title ?? null, $e->name ?? null, $e->midname ?? null, $e->lastname ?? null])));
                $employeesById[(int) $e->id] = [
                    'id' => (int) $e->id,
                    'name' => $full !== '' ? $full : ('#' . (int) $e->id),
                    'image' => $e->image ?? null,
                ];
            }
        }

        // 11. Build Response
        $phasesByStage = $phases->groupBy(fn($p) => (int) ($p->stage_id ?? 0));
        $activitiesByPhase = $activities->groupBy(fn($a) => (int) $a->phase_id);

        $stageOut = $stages->map(function ($s) use ($phasesByStage, $activitiesByPhase, $historyMap, $plannedMap, $employeesById, $suggestMap, $linkedMasterSets, $isTruthy) {

            $stagePhases = ($phasesByStage[(int) $s->id] ?? collect())->map(function ($p) use ($activitiesByPhase, $historyMap, $plannedMap, $employeesById, $suggestMap, $linkedMasterSets, $isTruthy) {

                $suggested = [];
                foreach (($suggestMap[(int) $p->id] ?? []) as $sr) {
                    $eid = !empty($sr->employee_id) ? (int) $sr->employee_id : null;
                    $suggested[] = [
                        'id' => (int) $sr->id,
                        'employee_id' => $eid,
                        'employee' => $eid && isset($employeesById[$eid]) ? $employeesById[$eid] : null,
                        'role' => $sr->role ?? null
                    ];
                }

                $acts = ($activitiesByPhase[(int) $p->id] ?? collect())->map(function ($a) use ($historyMap, $plannedMap, $employeesById, $linkedMasterSets, $isTruthy) {
                    $aid = (int) $a->id;
                    $hist = $historyMap[$aid] ?? null;
                    $done = $hist ? $isTruthy($hist->is_done ?? null) : false;
                    $plan = $plannedMap[$aid] ?? null;

                    $isPlanned = ($plan !== null) && ($plan['status'] !== 'open');
                    $computedStatus = $done ? 'done' : ($isPlanned ? 'planned' : 'open');
                    $doneById = ($hist && !empty($hist->done_by)) ? (int) $hist->done_by : null;
                    $plannedEmpId = ($plan && !empty($plan['employee_id'])) ? (int) $plan['employee_id'] : null;

                    // --- FORMAT MASTER SET DATA ---
                    $masterSetData = null;
                    if (isset($linkedMasterSets[$aid])) {
                        $ms = $linkedMasterSets[$aid];
                        $masterSetData = [
                            'id' => $ms->id,
                            'name' => $ms->name,

                            // Map Components: Name, Qty, Unit Price, Total Price
                            'components' => $ms->components->map(fn($c) => [
                                'id' => $c->id,
                                'qty' => (float) $c->qty,
                                // Assuming $c->product is available, otherwise fall back to description
                                'name' => $c->product ? $c->product->product : ($c->description ?? 'Material'),
                                'unit_price' => (float) $c->unit_price,
                                'total_price' => (float) ($c->qty * $c->unit_price)
                            ])->values(),

                            // Map Labor: Position Name, Employee Name, Hours
                            'labor' => $ms->labor->map(fn($l) => [
                                'id' => $l->id,
                                'hours' => (float) $l->hours,
                                // Prefer Position Name from relationship
                                'position_name' => $l->position ? $l->position->position : ($l->employee ? 'Mitarbeiter' : 'Arbeit'),
                                'employee_name' => $l->employee ? trim($l->employee->name . ' ' . $l->employee->lastname) : null,
                                'department_name' => $l->department ? $l->department->department_name : null,
                            ])->values()
                        ];
                    }

                    return [
                        'id' => $aid,
                        'title' => $a->title,
                        'description' => $a->description,
                        'duration' => $a->duration,
                        'status' => $computedStatus,
                        'is_done' => $done,
                        'is_planned' => $isPlanned,

                        'history' => $hist ? [
                            'is_done' => $hist->is_done,
                            'done_date' => $hist->done_date,
                            'done_by' => $doneById,
                            'done_by_employee' => $doneById && isset($employeesById[$doneById]) ? $employeesById[$doneById] : null,
                        ] : null,

                        'planned' => $plan ? [
                            'plan_item_id' => $plan['plan_item_id'] ?? null,
                            'planned_start_at' => $plan['planned_start_at'] ?? null,
                            'employee_id' => $plannedEmpId,
                            'employee' => $plannedEmpId && isset($employeesById[$plannedEmpId]) ? $employeesById[$plannedEmpId] : null,
                        ] : null,

                        // Attach Master Set
                        'linked_master_set' => $masterSetData
                    ];
                })->values();

                $total = $acts->count();
                $doneCount = $acts->where('is_done', true)->count();
                $phaseStatus = ($total > 0 && $doneCount === $total) ? 'done' : (($acts->where('is_planned', true)->count() > 0) ? 'in_progress' : 'open');

                return [
                    'id' => (int) $p->id,
                    'phase_name' => $p->phase_name,
                    'status' => $phaseStatus,
                    'suggested_employees' => $suggested,
                    'activities' => $acts,
                ];
            })->values();

            return [
                'id' => (int) $s->id,
                'stage' => $s->stage,
                'phases' => $stagePhases,
            ];
        })->values();

        return response()->json(['ok' => true, 'data' => $stageOut]);
    }


    // In PlannerPlanController.php

    public function linkMasterSet(Request $request, $planId, $itemId)
    {
        $request->validate([
            'master_set_id' => 'required|integer|exists:master_sets,id'
        ]);

        $item = PlannerItem::where('plan_id', $planId)->findOrFail($itemId);
        $masterSet = \App\Models\MasterSet::with('components.product')->findOrFail($request->master_set_id);

        // Clear existing materials if you want a replace behavior, 
        // OR skip this line to append materials.
        // DB::table('planner_item_materials')->where('planner_item_id', $item->id)->delete();

        foreach ($masterSet->components as $comp) {
            DB::table('planner_item_materials')->insert([
                'planner_item_id' => $item->id,
                'master_set_id' => $masterSet->id,
                'name' => $comp->product ? $comp->product->product : ($comp->description ?? 'Material'),
                'qty' => $comp->qty,
                'unit_price' => $comp->unit_price,
                'total_price' => $comp->qty * $comp->unit_price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Return the updated item with materials
        $item->load('materials'); // Assuming you add relationship to model

        return response()->json([
            'ok' => true,
            'message' => 'Materialien hinzugefügt',
            'materials' => $item->materials
        ]);
    }

    public function updateMaterial(Request $request, $id)
    {
        $request->validate(['qty' => 'required|numeric|min:0']);

        // Assuming you have a PlannerItemMaterial model
        $mat = \App\Models\PlannerItemMaterial::findOrFail($id);

        $mat->qty = $request->qty;
        // Optionally update total_price if you track it on the record
        if ($mat->unit_price) {
            $mat->total_price = $mat->qty * $mat->unit_price;
        }
        $mat->save();

        return response()->json(['ok' => true, 'material' => $mat]);
    }

    public function destroyMaterial($id)
    {
        $mat = \App\Models\PlannerItemMaterial::findOrFail($id);
        $mat->delete();

        return response()->json(['ok' => true]);
    }

    public function appointmentsIndex(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|integer',
            'limit' => 'nullable|integer|max:500'
        ]);
        $customerId = isset($data['customer_id']) ? (int) $data['customer_id'] : null;
        $limit = (int) ($data['limit'] ?? 200);
        $baseQ = DB::table('main_appointments as a')
            ->join('new_leads as c', 'c.id', '=', 'a.customer_id')
            ->whereNull('a.deleted_at')->whereNotNull('a.customer_id')
            ->select(['a.*', 'c.firma as customer_firma', 'c.name as customer_name', 'c.lastname as customer_lastname']);
        if ($customerId)
            $baseQ->where('a.customer_id', $customerId);
        $appointments = $baseQ->orderByDesc('a.start_date')->limit($limit)->get();

        // --- FETCH EMPLOYEES FOR APPOINTMENTS ---
        $apptIds = $appointments->pluck('id')->toArray();
        $employeesByAppt = [];

        if (!empty($apptIds)) {
            $empRows = DB::table('main_appointment_employees as mae')
                ->join('employees as e', 'e.id', '=', 'mae.employee_id')
                ->whereIn('mae.appointment_id', $apptIds)
                ->whereNull('e.deleted_at')
                ->select([
                    'mae.appointment_id',
                    'mae.employee_id',
                    'e.title',
                    'e.name',
                    'e.lastname',
                    'e.image'
                ])
                ->get();

            foreach ($empRows as $row) {
                $employeesByAppt[$row->appointment_id][] = [
                    'id' => $row->employee_id,
                    'full_name' => trim("$row->title $row->name $row->lastname"),
                    'photo_url' => $this->resolvePhotoUrl($row)
                ];
            }
        }
        $planned = [];
        $unplanned = [];
        foreach ($appointments as $a) {
            $isPlanned = !empty($a->planner_item_id);
            if (!$isPlanned && DB::table('planner_items')->where('source_type', 'appointment')->where('source_id', $a->id)->exists()) {
                $isPlanned = true;
            }
            $payload = (array) $a;
            $payload['is_planned'] = $isPlanned;
            $payload['employees'] = $employeesByAppt[$a->id] ?? [];

            if ($isPlanned)
                $planned[] = $payload;
            else
                $unplanned[] = $payload;
        }
        return response()->json([
            'ok' => true,
            'planned_appointments' => $planned,
            'unplanned_appointments' => $unplanned
        ]);
    }
    public function personalTasksIndex(Request $request)
    {
        $customerId = $request->input('customer_id');
        $tasks = DB::table('personal_tasks as t')
            ->leftJoin('employees as e', 'e.id', '=', 't.assigned_by')
            ->whereNull('t.deleted_at')
            ->when($customerId, fn($q) => $q->where('t.customer_id', $customerId))
            ->whereRaw('LOWER(t.task_status) NOT IN (?,?,?)', ['completed', 'done', 'deleted'])
            ->select('t.*', 'e.name as emp_name', 'e.lastname as emp_lastname', 'e.image as emp_image')
            ->get();
        $out = $tasks->map(function ($t) {
            $t->employees = $t->assigned_by ? [
                [
                    'id' => $t->assigned_by,
                    'full_name' => trim("$t->emp_name $t->emp_lastname"),
                    'photo_url' => $this->resolvePhotoUrl((object) ['image' => $t->emp_image])
                ]
            ] : [];
            return $t;
        });
        return response()->json(['ok' => true, 'data' => $out]);
    }
    public function problemsIndex(Request $request)
    {
        $customerId = $request->input('customer_id');
        $limit = $request->input('limit', 200);
        $tickets = DB::table('problems as p')
            ->leftJoin('employees as e', 'e.id', '=', 'p.responsible')
            ->whereNull('p.deleted_at')
            ->when($customerId, fn($q) => $q->where('p.customer_id', $customerId))
            ->whereRaw('LOWER(p.status) NOT IN (?,?,?)', ['completed', 'done', 'deleted'])
            ->select('p.*', 'e.name as emp_name', 'e.lastname as emp_lastname', 'e.image as emp_image')
            ->orderByDesc('p.id')
            ->limit($limit)
            ->get();
        $ticketIds = $tickets->pluck('id')->toArray();
        $pivotEmployees = [];

        if (!empty($ticketIds)) {
            $empRows = DB::table('employee_problem as ep')
                ->join('employees as e', 'e.id', '=', 'ep.employee_id')
                ->whereIn('ep.problem_id', $ticketIds)
                ->whereNull('e.deleted_at')
                ->select(['ep.problem_id', 'e.id', 'e.title', 'e.name', 'e.lastname', 'e.image'])
                ->get();

            foreach ($empRows as $row) {
                $pivotEmployees[$row->problem_id][] = [
                    'id' => $row->id,
                    'full_name' => trim("$row->title $row->name $row->lastname"),
                    'photo_url' => $this->resolvePhotoUrl($row)
                ];
            }
        }
        $out = $tickets->map(function ($t) use ($pivotEmployees) {
            $isPlanned = DB::table('planner_items')->where('source_type', 'ticket')->where('source_id', $t->id)->exists();
            $t->is_planned = $isPlanned;

            $emps = [];
            if ($t->responsible) {
                $emps[$t->responsible] = [
                    'id' => $t->responsible,
                    'full_name' => trim("$t->emp_name $t->emp_lastname"),
                    'photo_url' => $this->resolvePhotoUrl((object) ['image' => $t->emp_image])
                ];
            }
            if (isset($pivotEmployees[$t->id])) {
                foreach ($pivotEmployees[$t->id] as $pe) {
                    $emps[$pe['id']] = $pe;
                }
            }
            $t->employees = array_values($emps);
            return $t;
        });
        return response()->json(['ok' => true, 'data' => $out]);
    }
    public function ticketTasksIndex(Request $request)
    {
        return response()->json(['ok' => true, 'data' => []]);
    }
    public function getHistoryData(Request $request)
    {
        $planId = $request->input('plan_id');
        $date = $request->input('date', date('Y-m-d'));
        if (!$planId)
            return response()->json(['ok' => false, 'message' => 'Plan ID required'], 400);
        $planEmployees = DB::table('planner_items')
            ->join('planner_item_employees', 'planner_items.id', '=', 'planner_item_employees.planner_item_id')
            ->join('employees', 'employees.id', '=', 'planner_item_employees.employee_id')
            ->where('planner_items.plan_id', $planId)->whereNull('planner_items.deleted_at')
            ->select('employees.id', 'employees.name', 'employees.lastname', 'employees.photo', 'employees.title as role')
            ->distinct()->get()->keyBy('id');
        $attendanceRecords = DB::table('attendances')
            ->whereDate('date', $date)->whereIn('employee_id', $planEmployees->pluck('id'))
            ->orderByDesc('created_at')->get()->unique('employee_id')->keyBy('employee_id');
        $present = [];
        $absent = [];
        foreach ($planEmployees as $empId => $emp) {
            $record = $attendanceRecords->get($empId);
            $emp->full_name = $emp->name . ' ' . $emp->lastname;
            if ($record && $record->check_in && !$record->check_out) {
                $emp->check_in_time = Carbon::parse($record->check_in)->format('H:i');
                $present[] = $emp;
            } else {
                $emp->status_label = ($record && $record->check_out) ? 'Fertig (' . Carbon::parse($record->check_out)->format('H:i') . ')' : 'Nicht erschienen';
                $absent[] = $emp;
            }
        }
        return response()->json([
            'ok' => true,
            'attendance_lists' => ['present' => $present, 'absent' => $absent]
        ]);
    }
    public function notificationsList()
    {
        return response()->json(auth()->user()->notifications()->latest()->limit(50)->get());
    }
    public function markNotificationRead($id)
    {
        auth()->user()->notifications()->find($id)?->markAsRead();
        return response()->json(['ok' => true]);
    }
    public function markAllNotificationsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }

    public function crewGet(Request $request, int $planId)
    {
        $plan = PlannerPlan::find($planId);
        if (!$plan)
            return response()->json(['ok' => false], 404);

        $meta = $this->decodeJson($plan->meta);

        $extra = $meta['extra_manager_ids'] ?? [];

        $emps = DB::table('employees')->whereRaw("LOWER(COALESCE(status, '')) = 'active'")->select('id', 'name', 'lastname')->get()
            ->map(function ($e) use ($extra) {
                $e->is_selected = in_array($e->id, $extra);
                return $e;
            });

        return response()->json(['ok' => true, 'employees' => $emps]);
    }

    public function storeWizard(Request $request)
    {
        return response()->json(['ok' => false, 'message' => 'Use Auto-Sync instead.']);
    }
    public function storeDependency(Request $request, PlannerPlan $plan)
    {
        $data = $request->validate([
            'item_id' => ['required', 'integer', 'exists:planner_items,id'],
            'depends_on_id' => ['required', 'integer', 'exists:planner_items,id'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $itemId = (int) $data['item_id'];
        $dependsOnId = (int) $data['depends_on_id'];

        if ($itemId === $dependsOnId) {
            return response()->json([
                'ok' => false,
                'message' => 'Eine Aufgabe kann nicht von sich selbst abhängig sein.',
            ], 422);
        }

        $itemsCount = PlannerItem::query()
            ->where('plan_id', $plan->id)
            ->whereIn('id', [$itemId, $dependsOnId])
            ->count();

        if ($itemsCount !== 2) {
            return response()->json([
                'ok' => false,
                'message' => 'Beide Aufgaben müssen zum gleichen Projektplan gehören.',
            ], 422);
        }

        if ($this->dependencyWouldCreateCycle($plan->id, $itemId, $dependsOnId)) {
            return response()->json([
                'ok' => false,
                'message' => 'Diese Verbindung würde eine Kreis-Abhängigkeit erzeugen.',
            ], 422);
        }

        PlannerItemDependency::query()->updateOrCreate(
            [
                'planner_item_id' => $itemId,
                'depends_on_item_id' => $dependsOnId,
            ],
            [
                'reason' => $data['reason'] ?? null,
            ]
        );

        return response()->json([
            'ok' => true,
            'message' => 'Abhängigkeit wurde gespeichert.',
            'data' => $this->buildPlanPayloadWithRequestedMaterials($plan->id),
        ]);
    }

    public function destroyDependency(Request $request, PlannerPlan $plan)
    {
        $data = $request->validate([
            'item_id' => ['required', 'integer', 'exists:planner_items,id'],
            'depends_on_id' => ['required', 'integer', 'exists:planner_items,id'],
        ]);

        PlannerItemDependency::query()
            ->where('planner_item_id', (int) $data['item_id'])
            ->where('depends_on_item_id', (int) $data['depends_on_id'])
            ->whereHas('item', fn($q) => $q->where('plan_id', $plan->id))
            ->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Abhängigkeit wurde entfernt.',
            'data' => $this->buildPlanPayloadWithRequestedMaterials($plan->id),
        ]);
    }

    private function dependencyWouldCreateCycle(int $planId, int $itemId, int $dependsOnId): bool
    {
        /*
         * We want to save:
         * itemId depends on dependsOnId.
         *
         * This is invalid if dependsOnId already depends on itemId
         * directly or indirectly.
         */

        $visited = [];
        $stack = [$dependsOnId];

        while (!empty($stack)) {
            $currentId = array_pop($stack);

            if ($currentId === $itemId) {
                return true;
            }

            if (isset($visited[$currentId])) {
                continue;
            }

            $visited[$currentId] = true;

            $nextParents = DB::table('planner_item_dependencies as d')
                ->join('planner_items as child', 'child.id', '=', 'd.planner_item_id')
                ->join('planner_items as parent', 'parent.id', '=', 'd.depends_on_item_id')
                ->where('child.plan_id', $planId)
                ->where('parent.plan_id', $planId)
                ->where('d.planner_item_id', $currentId)
                ->pluck('d.depends_on_item_id')
                ->map(fn($id) => (int) $id)
                ->all();

            foreach ($nextParents as $nextId) {
                $stack[] = $nextId;
            }
        }

        return false;
    }
    // App/Http/Controllers/PlannerPlanController.php
    public function moveGanttItem(Request $request, PlannerPlan $plan, PlannerItem $item)
    {
        // Validate inputs
        $data = $request->validate([
            'planned_date' => 'required|date',
            'planned_time' => 'required|date_format:H:i',
        ]);
        // Construct new Carbon instance
        $newStart = Carbon::parse($data['planned_date'] . ' ' . $data['planned_time']);

        // Calculate end time based on existing duration (keep duration constant when moving)
        $duration = $item->duration_minutes ?? 60;
        $newEnd = $newStart->copy()->addMinutes($duration);
        // Update item
        $item->update([
            'planned_start_at' => $newStart,
            'planned_end_at' => $newEnd,
            // Status remains 'planned'
        ]);
        return response()->json([
            'ok' => true,
            'item' => $item
        ]);
    }
    public function storeManualTask(Request $request)
    {
        $data = $request->validate([
            'plan_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'due_time' => 'nullable|string', // HH:MM
            'duration' => 'nullable|numeric', // Minutes
            'priority' => 'nullable|string',
            'employees' => 'nullable|array',
            'subtasks' => 'nullable|array', // For Bulk Mode
            'subtasks.*.task' => 'required|string',
            'subtasks.*.duration' => 'nullable|numeric',
        ]);
        return DB::transaction(function () use ($data) {
            $user = auth()->user();

            // 1. Create the Personal Task (The Source)
            // Calculate total time based on bulk steps or single input
            $totalTime = $data['duration'] ?? 60;
            if (!empty($data['subtasks'])) {
                $totalTime = collect($data['subtasks'])->sum('duration');
            }
            $personalTaskId = DB::table('personal_tasks')->insertGetId([
                'task_id' => \Illuminate\Support\Str::uuid(),
                'task_title' => $data['title'],
                'description' => $data['description'],
                'start_date' => $data['start_date'],
                'due_date' => $data['start_date'], // Assuming single day for now
                'due_time' => $data['due_time'] ?? '08:00:00',
                'total_time' => $totalTime,
                'priority' => $data['priority'] ?? 'medium',
                'task_status' => 'open',
                'type' => !empty($data['subtasks']) ? 'bulk_task' : 'personal_task',
                'assigned_by' => $this->authEmployeeId(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // 2. Assign Employees (employees_personal_tasks)
            $employeeIds = $data['employees'] ?? [];
            foreach ($employeeIds as $empId) {
                DB::table('employees_personal_tasks')->insert([
                    'task_id' => $personalTaskId,
                    'employee_id' => $empId,
                    'status' => 'send',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            // 3. Handle Bulk Sub-tasks (personal_task_keys)
            if (!empty($data['subtasks'])) {
                foreach ($data['subtasks'] as $sub) {
                    DB::table('personal_task_keys')->insert([
                        'personal_task_id' => $personalTaskId,
                        'task' => $sub['task'],
                        'duration' => $sub['duration'] ?? 0,
                        'status' => 'open',
                        'is_completed' => 0,
                        'work_progress' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            // 4. Create Planner Item (The Unified View Wrapper)
            // This ensures it shows up on the Gantt/Board immediately
            $plannerItem = PlannerItem::create([
                'plan_id' => $data['plan_id'],
                'client_uid' => (string) \Illuminate\Support\Str::uuid(),
                'source_type' => 'personal_task',
                'source_id' => $personalTaskId,
                'title' => $data['title'],
                'description' => $data['description'],
                'duration_minutes' => $totalTime,
                'status' => 'planned',
                'planned_start_at' => $data['start_date'] . ' ' . ($data['due_time'] ?? '08:00:00'),
                'sort_order' => 9999,
            ]);
            // Sync Planner Employees
            foreach ($employeeIds as $index => $empId) {
                DB::table('planner_item_employees')->insert([
                    'planner_item_id' => $plannerItem->id,
                    'employee_id' => $empId,
                    'role' => $index === 0 ? 'lead' : 'member',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            // Return formatted item for frontend
            $item = PlannerItem::with('employees')->find($plannerItem->id);

            // Format for JS
            $payload = [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'status' => $item->status,
                'source_type' => 'personal_task',
                'source_id' => $personalTaskId,
                'planned_date' => $data['start_date'],
                'planned_time' => $data['due_time'] ?? '08:00',
                'duration_minutes' => $totalTime,
                'lead' => $item->employees->first() ? [
                    'id' => $item->employees->first()->id,
                    'full_name' => $item->employees->first()->name . ' ' . $item->employees->first()->lastname,
                    'photo_url' => asset('images/employee/' . ltrim($item->employees->first()->image, '/'))
                ] : null,
                'members' => $item->employees->map(function ($e) {
                    return [
                        'id' => $e->id,
                        'full_name' => $e->name . ' ' . $e->lastname,
                        'photo_url' => asset('images/employee/' . ltrim($e->image, '/'))
                    ];
                })
            ];
            return response()->json(['ok' => true, 'item' => $payload]);
        });
    }
    /**
     * Helper to sync external subtasks into the planner checklist table
     */
    private function syncChecklistsToItem(PlannerItem $item, $sourceSubtasks)
    {
        // $sourceSubtasks should be a collection of objects with { id, title, is_completed/status }

        foreach ($sourceSubtasks as $sub) {
            // Normalize status (some tables might use 'done', others 1/0, others 'completed')
            $isCompleted = false;
            if (isset($sub->is_completed))
                $isCompleted = (bool) $sub->is_completed;
            elseif (isset($sub->status))
                $isCompleted = in_array(strtolower($sub->status), ['done', 'completed']);
            // Update or Create based on the external ID (so we don't duplicate on every sync)
            \App\Models\PlannerItemChecklist::updateOrCreate(
                [
                    'planner_item_id' => $item->id,
                    'source_external_id' => $sub->id // Link to original ID (e.g., personal_task_key id)
                ],
                [
                    'title' => $sub->task ?? $sub->title ?? 'Checklist Item',
                    'is_completed' => $isCompleted,
                    'sort_order' => $sub->id // simple sort
                ]
            );
        }
    }

    public function getObjectData(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'project_id' => 'nullable|integer'
        ]);

        $customerId = $request->customer_id;
        $projectId = $request->project_id;

        // 1. Try to find the specific alternative linked to this project
        $altId = null;
        if ($projectId) {
            $project = DB::table('lead_product_lists')
                ->where('id', $projectId)
                ->first();
            $altId = $project->alternative_id ?? null;
        }

        // 2. Query the data
        $query = DB::table('lead_alternative_adds')
            ->where('lead_id', $customerId)
            ->whereNull('deleted_at');

        if ($altId) {
            $query->where('id', $altId);
        } else {
            // Fallback: Get the most recent entry for this customer
            $query->orderByDesc('id');
        }

        $data = $query->first();

        if (!$data) {
            return response()->json(['ok' => false, 'message' => 'Keine Objektdaten gefunden.']);
        }

        return response()->json(['ok' => true, 'data' => $data]);
    }


    public function projectCockpit(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | /planner/projects is always the Projektplanung page.
        |--------------------------------------------------------------------------
        | /planner/projects
        | /planner/projects?customer_id=161&project_id=34&plan_id=8
        |
        | Both must return admin.planner.projects.
        | Do NOT redirect or return $this->index($request) from here.
        |--------------------------------------------------------------------------
        */

        $projectId = (int) $request->query('project_id', 0);
        $customerId = (int) $request->query('customer_id', 0);
        $planId = (int) $request->query('plan_id', 0);
        $productId = (int) $request->query('product_id', 0);

        if ($projectId > 0 && $planId <= 0) {
            $planId = (int) (
                PlannerPlan::query()
                    ->where('project_id', $projectId)
                    ->orderByDesc('id')
                    ->value('id') ?? 0
            );
        }

        if ($projectId > 0 && $customerId <= 0) {
            $project = LeadProductList::query()->find($projectId);

            if ($project) {
                $customerId = (int) ($project->customer_id ?? 0);
                $productId = $productId > 0 ? $productId : (int) ($project->product_id ?? 0);
            }
        }

        if ($projectId > 0 && $planId <= 0 && $customerId > 0) {
            $project = LeadProductList::query()->find($projectId);

            if ($project) {
                $plan = PlannerPlan::firstOrCreate(
                    [
                        'customer_id' => (int) $project->customer_id,
                        'project_id' => (int) $project->id,
                    ],
                    [
                        'account_id' => null,
                        'stage' => $project->status ?: 'project',
                        'title' => 'Projektplan #' . $project->id,
                        'status' => 'active',
                        'created_by' => auth()->id(),
                        'meta' => [
                            'product_id' => $project->product_id,
                            'alternative_id' => $project->alternative_id,
                            'lead_stage_sub_stage_id' => $project->lead_stage_sub_stage_id,
                        ],
                    ]
                );

                $this->syncProjectScopedPlan($plan);

                $planId = (int) $plan->id;
            }
        }

        $stages = LeadStage::query()
            ->active()
            ->ordered()
            ->get(['id', 'key', 'name', 'color', 'icon']);

        $employeeColumns = ['id', 'name', 'lastname'];

        foreach (['title', 'image', 'photo', 'avatar'] as $column) {
            if (Schema::hasColumn('employees', $column)) {
                $employeeColumns[] = $column;
            }
        }

        $employees = Employee::query()
            ->whereNull('deleted_at')
            ->whereRaw("LOWER(COALESCE(status, '')) = 'active'")
            ->orderBy('name')
            ->orderBy('lastname')
            ->get($employeeColumns);

        return view('admin.planner.projects', [
            'stages' => $stages,
            'employees' => $employees,
            'config' => [
                'dataUrl' => route('planner.projects.data'),
                'kanbanUrl' => route('planner.projects.kanban'),
                'candidatesUrl' => route('planner.projects.candidates'),
                'storeProjectUrl' => route('planner.projects.store'),

                'ensurePlanUrlTemplate' => route('planner.projects.ensure_plan', ['project' => '___PROJECT___']),
                'moveProjectUrlTemplate' => route('planner.projects.move', ['project' => '___PROJECT___']),
                'historyUrlTemplate' => route('planner.projects.history', ['project' => '___PROJECT___']),
                'profileUrlTemplate' => route('planner.projects.profile', ['project' => '___PROJECT___']),
                'profileDataUrlTemplate' => route('planner.projects.profile.data', ['project' => '___PROJECT___']),
                'saveProjectTeamUrlTemplate' => route('planner.projects.team.save', ['project' => '___PROJECT___']),

                'montageWorkPayloadUrlTemplate' => route('planner.projects.montage_work', ['project' => '___PROJECT___']),
                'projectTeamMemberUrlTemplate' => route('planner.projects.team.member', ['project' => '___PROJECT___']),
                'projectWorkItemStoreUrlTemplate' => route('planner.projects.work_items.store', ['project' => '___PROJECT___']),

                'boardUrl' => route('planner.projects'),
                'syncUrl' => route('planner.plans.sync'),

                'initial' => [
                    'customerId' => $customerId,
                    'projectId' => $projectId,
                    'planId' => $planId,
                    'productId' => $productId,
                    'date' => (string) $request->query('date', now()->toDateString()),
                    'mode' => (string) $request->query('mode', 'day'),
                ],
            ],
        ]);
    }

    public function projectCockpitData(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $view = trim((string) $request->query('view', 'list'));
        $stage = trim((string) $request->query('stage', 'project'));
        $subStageId = (int) $request->query('sub_stage_id', 0);
        $employeeId = (int) $request->query('employee_id', 0);
        $myOnly = filter_var($request->query('my'), FILTER_VALIDATE_BOOLEAN);
        $currentEmployeeId = $this->authEmployeeId();

        $latestPlanSub = DB::table('planner_plans')
            ->whereNull('deleted_at')
            ->select('project_id', DB::raw('MAX(id) as planner_plan_id'))
            ->groupBy('project_id');

        $query = DB::table('lead_product_lists as lpl')
            ->whereNull('lpl.deleted_at')
            ->leftJoin('new_leads as c', 'c.id', '=', 'lpl.customer_id')
            ->leftJoin('lead_alternative_adds as a', 'a.id', '=', 'lpl.alternative_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
            ->leftJoin('phase_sections as ps', 'ps.id', '=', 'lpl.service_id')
            ->leftJoin('lead_stages as ls', 'ls.key', '=', 'lpl.status')
            ->leftJoin('lead_stage_sub_stages as lss', 'lss.id', '=', 'lpl.lead_stage_sub_stage_id')
            ->leftJoinSub($latestPlanSub, 'pp_latest', function ($join) {
                $join->on('pp_latest.project_id', '=', 'lpl.id');
            })
            ->select([
                'lpl.id',
                'lpl.customer_id',
                'lpl.alternative_id',
                'lpl.product_id',
                'lpl.service_id',
                'lpl.employee_id',
                'lpl.field_employee',
                'lpl.teams',
                'lpl.status',
                'lpl.work_status',
                'lpl.price',
                'lpl.price_latest',
                'lpl.project_minutes',
                'lpl.product_stage_id',
                'lpl.product_task_phase_id',
                'lpl.lead_stage_sub_stage_id',
                'lpl.updated_at',
                'c.customer_no',
                'c.firma',
                'c.name as customer_name',
                'c.lastname as customer_lastname',
                'c.phone as customer_phone',
                'c.email as customer_email',
                'a.object_name',
                'a.full_address as object_full_address',
                'a.street as object_street',
                'a.postcode as object_postcode',
                'a.city as object_city',
                'ag.article_group as product_name',
                'ag.initial as product_initial',
                'ag.image as product_image',
                'ps.phase_section as service_name',
                'ls.name as stage_name',
                'ls.color as stage_color',
                'ls.icon as stage_icon',
                'lss.name as sub_stage_name',
                'lss.color as sub_stage_color',
                'pp_latest.planner_plan_id',
            ]);

        if ($stage !== '') {
            $query->where(function ($q) use ($stage) {
                $q->where('lpl.status', $stage);

                if ($stage === 'project') {
                    $q->orWhere('lpl.status', 'montage');
                }
            });
        } else {
            $query->whereNotIn('lpl.status', ['completed', 'archive', 'junk']);
        }

        if ($subStageId > 0) {
            $query->where('lpl.lead_stage_sub_stage_id', $subStageId);
        }

        if ($search !== '') {
            $like = '%' . mb_strtolower($search) . '%';

            $query->where(function ($q) use ($like) {
                $q->whereRaw('LOWER(COALESCE(c.customer_no, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(c.firma, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(c.name, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(c.lastname, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(a.object_name, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(a.full_address, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(ag.article_group, "")) LIKE ?', [$like]);
            });
        }

        $teamFilterEmployeeId = $myOnly ? (int) $currentEmployeeId : $employeeId;

        if ($teamFilterEmployeeId > 0) {
            $query->where(function ($q) use ($teamFilterEmployeeId) {
                $q->where('lpl.employee_id', $teamFilterEmployeeId)
                    ->orWhere('lpl.field_employee', $teamFilterEmployeeId)
                    ->orWhere('lpl.teams', 'like', '%"employee_id":' . $teamFilterEmployeeId . '%')
                    ->orWhere('lpl.teams', 'like', '%"employee_id":"' . $teamFilterEmployeeId . '"%');
            });
        }

        $projects = $query
            ->orderByDesc('lpl.updated_at')
            ->orderByDesc('lpl.id')
            ->paginate((int) $request->query('per_page', 18));

        $counts = $this->projectCockpitCounts($projects->getCollection());
        $latestActivities = $this->ppLatestActivitiesForProjects(
            $projects->getCollection()
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->filter()
                ->values()
                ->all()
        );

        $employeeIds = [];

        foreach ($projects->getCollection() as $project) {
            $employeeIds = array_merge($employeeIds, $this->extractProjectTeamIds($project));
        }

        $employees = Employee::query()
            ->whereIn('id', array_values(array_unique(array_filter($employeeIds))))
            ->get(['id', 'name', 'lastname', 'image'])
            ->keyBy('id');

        $projects->getCollection()->transform(function ($project) use ($counts, $employees, $latestActivities) {
            $projectId = (int) $project->id;

            $moduleCounts = [
                'appointments' => (int) ($counts['appointments'][$projectId] ?? 0),
                'personal_tasks' => (int) ($counts['personal_tasks'][$projectId] ?? 0),
                'tickets' => (int) ($counts['tickets'][$projectId] ?? 0),
                'kanban_tasks' => (int) ($counts['kanban_tasks'][$projectId] ?? 0),
                'planner_items' => (int) ($counts['planner_items'][$projectId] ?? 0),
                'planner_done' => (int) ($counts['planner_done'][$projectId] ?? 0),
            ];

            $totalWork = $moduleCounts['planner_items'] > 0
                ? $moduleCounts['planner_items']
                : ($moduleCounts['appointments'] + $moduleCounts['personal_tasks'] + $moduleCounts['tickets'] + $moduleCounts['kanban_tasks']);

            $progress = $totalWork > 0 ? min(100, round(($moduleCounts['planner_done'] / $totalWork) * 100)) : 0;
            $teamIds = $this->extractProjectTeamIds($project);

            return [
                'id' => $projectId,
                'customer_id' => (int) $project->customer_id,
                'alternative_id' => $project->alternative_id ? (int) $project->alternative_id : null,
                'product_id' => $project->product_id ? (int) $project->product_id : null,
                'planner_plan_id' => $project->planner_plan_id ? (int) $project->planner_plan_id : null,
                'customer' => [
                    'no' => $project->customer_no,
                    'name' => $this->displayCustomerNameFromRow($project),
                    'phone' => $project->customer_phone,
                    'email' => $project->customer_email,
                ],
                'object' => [
                    'name' => $project->object_name ?: ($project->alternative_id ? 'Objekt #' . $project->alternative_id : 'Hauptobjekt'),
                    'address' => $project->object_full_address ?: trim(implode(' ', array_filter([
                        $project->object_street ?? null,
                        $project->object_postcode ?? null,
                        $project->object_city ?? null,
                    ]))),
                ],
                'product' => [
                    'name' => $project->product_name ?: '—',
                    'image' => $project->product_image ? asset('images/articles/' . ltrim($project->product_image, '/')) : null,
                    'service' => $project->service_name ?: '—',
                ],
                'stage' => [
                    'key' => $project->status,
                    'name' => $project->stage_name ?: $project->status,
                    'color' => $project->stage_color ?: '#74b2d4',
                    'icon' => $project->stage_icon ?: 'wrench',
                    'sub_stage_id' => $project->lead_stage_sub_stage_id,
                    'sub_stage_name' => $project->sub_stage_name,
                    'sub_stage_color' => $project->sub_stage_color ?: '#93c21c',
                ],
                'team' => collect($teamIds)
                    ->map(fn($id) => $employees[$id] ?? null)
                    ->filter()
                    ->map(fn($emp) => [
                        'id' => $emp->id,
                        'name' => trim(($emp->name ?? '') . ' ' . ($emp->lastname ?? '')),
                        'image' => $emp->image ? asset('images/employee/' . ltrim($emp->image, '/')) : null,
                    ])
                    ->values(),
                'counts' => $moduleCounts,
                'progress' => $progress,
                'latest_activity' => $latestActivities[$projectId] ?? null,
                'price' => $project->price,
                'price_latest' => $project->price_latest,
                'project_minutes' => $project->project_minutes,
                'updated_at' => $project->updated_at ? Carbon::parse($project->updated_at)->format('d.m.Y H:i') : null,
            ];
        });

        return response()->json([
            'ok' => true,
            'view' => $view,
            'data' => $projects,
        ]);
    }



    public function ensureProjectPlan(Request $request, LeadProductList $project)
    {
        $plan = PlannerPlan::firstOrCreate(
            [
                'customer_id' => (int) $project->customer_id,
                'project_id' => (int) $project->id,
            ],
            [
                'account_id' => null,
                'stage' => $project->status ?: 'project',
                'title' => 'Projektplan #' . $project->id,
                'status' => 'active',
                'created_by' => auth()->id(),
                'meta' => [
                    'product_id' => $project->product_id,
                    'alternative_id' => $project->alternative_id,
                    'lead_stage_sub_stage_id' => $project->lead_stage_sub_stage_id,
                ],
            ]
        );

        $this->syncProjectScopedPlan($plan);

        return response()->json([
            'ok' => true,
            'customer_id' => (int) $project->customer_id,
            'project_id' => (int) $project->id,
            'plan_id' => (int) $plan->id,
            'redirect_url' => route('planner.cockpit') . '?customer_id=' . $project->customer_id . '&project_id=' . $project->id . '&plan_id=' . $plan->id,
        ]);
    }


    private function syncProjectScopedPlan(PlannerPlan $plan): void
    {
        DB::transaction(function () use ($plan) {
            $this->syncAppointments($plan);
            $this->syncTickets($plan);
            $this->syncPersonalTasks($plan);
            $this->syncPhaseActivities($plan);
            $this->syncMasterSets($plan);
        });
    }


    private function projectCockpitCounts($projectRows): array
    {
        $projectRows = collect($projectRows);
        $projectIds = $projectRows->pluck('id')->map(fn($v) => (int) $v)->values()->all();

        if (empty($projectIds)) {
            return [
                'appointments' => [],
                'personal_tasks' => [],
                'tickets' => [],
                'kanban_tasks' => [],
                'planner_items' => [],
                'planner_done' => [],
            ];
        }

        return [
            'appointments' => $this->countModuleByProject('main_appointments', $projectRows, 'status', ['cancel', 'canceled', 'deleted']),
            'personal_tasks' => $this->countModuleByProject('personal_tasks', $projectRows, 'task_status', ['completed', 'done', 'deleted', 'cancel', 'canceled', 'junk']),
            'tickets' => $this->countModuleByProject('problems', $projectRows, 'status', ['completed', 'ended', 'done', 'closed', 'cancel', 'canceled']),
            'kanban_tasks' => $this->countModuleByProject('kanban_lead_tasks', $projectRows, 'status', ['completed', 'done', 'closed', 'cancel', 'canceled']),
            'planner_items' => $this->plannerItemCountsByProject($projectIds, false),
            'planner_done' => $this->plannerItemCountsByProject($projectIds, true),
        ];
    }

    private function countModuleByProject(string $table, $projectRows, ?string $statusColumn = null, array $closedStatuses = []): array
    {
        $projectRows = collect($projectRows);
        $projectIds = $projectRows->pluck('id')->map(fn($v) => (int) $v)->values()->all();

        if (!Schema::hasTable($table) || empty($projectIds)) {
            return [];
        }

        if ($this->safeColumn($table, 'lead_product_list_id')) {
            $q = DB::table($table)
                ->whereIn('lead_product_list_id', $projectIds);

            if ($this->safeColumn($table, 'deleted_at')) {
                $q->whereNull('deleted_at');
            }

            if ($table === 'personal_tasks' && $this->safeColumn($table, 'archived_at')) {
                $q->whereNull('archived_at');
            }

            if ($statusColumn && $this->safeColumn($table, $statusColumn) && !empty($closedStatuses)) {
                $q->whereRaw('LOWER(COALESCE(' . $statusColumn . ', "")) NOT IN (' . collect($closedStatuses)->map(fn() => '?')->implode(',') . ')', $closedStatuses);
            }

            return $q->select('lead_product_list_id', DB::raw('COUNT(*) as total'))
                ->groupBy('lead_product_list_id')
                ->pluck('total', 'lead_product_list_id')
                ->toArray();
        }

        if (!$this->safeColumn($table, 'customer_id')) {
            return [];
        }

        $rows = DB::table($table)
            ->whereIn('customer_id', $projectRows->pluck('customer_id')->filter()->unique()->values()->all())
            ->when($this->safeColumn($table, 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->when($table === 'personal_tasks' && $this->safeColumn($table, 'archived_at'), fn($q) => $q->whereNull('archived_at'))
            ->when($statusColumn && $this->safeColumn($table, $statusColumn) && !empty($closedStatuses), function ($q) use ($statusColumn, $closedStatuses) {
                $q->whereRaw('LOWER(COALESCE(' . $statusColumn . ', "")) NOT IN (' . collect($closedStatuses)->map(fn() => '?')->implode(',') . ')', $closedStatuses);
            })
            ->get();

        $counts = array_fill_keys($projectIds, 0);
        $hasAlternative = $this->safeColumn($table, 'alternative_id');
        $hasProduct = $this->safeColumn($table, 'product_id');

        foreach ($rows as $row) {
            foreach ($projectRows as $project) {
                if ((int) $row->customer_id !== (int) $project->customer_id) {
                    continue;
                }

                if ($hasAlternative && !empty($project->alternative_id) && (int) ($row->alternative_id ?? 0) !== (int) $project->alternative_id) {
                    continue;
                }

                if ($hasProduct && !empty($project->product_id) && (int) ($row->product_id ?? 0) !== (int) $project->product_id) {
                    continue;
                }

                $counts[(int) $project->id]++;
                break;
            }
        }

        return array_filter($counts, fn($value) => $value > 0);
    }

    private function plannerItemCountsByProject(array $projectIds, bool $doneOnly = false): array
    {
        if (!Schema::hasTable('planner_items') || !Schema::hasTable('planner_plans') || empty($projectIds)) {
            return [];
        }

        $q = DB::table('planner_items as pi')
            ->join('planner_plans as pp', 'pp.id', '=', 'pi.plan_id')
            ->whereNull('pi.deleted_at')
            ->whereNull('pp.deleted_at')
            ->whereIn('pp.project_id', $projectIds);

        if ($doneOnly) {
            $q->whereIn('pi.status', ['done', 'completed', 'finished']);
        }

        return $q->select('pp.project_id', DB::raw('COUNT(*) as total'))
            ->groupBy('pp.project_id')
            ->pluck('total', 'pp.project_id')
            ->toArray();
    }


    private function extractProjectTeamIds($project): array
    {
        $ids = [];

        if (!empty($project->employee_id)) {
            $ids[] = (int) $project->employee_id;
        }

        if (!empty($project->field_employee)) {
            $ids[] = (int) $project->field_employee;
        }

        $teams = $this->decodeJson($project->teams ?? null);

        foreach ($teams as $row) {
            if (is_numeric($row)) {
                $ids[] = (int) $row;
                continue;
            }

            if (!is_array($row)) {
                continue;
            }

            // Prefer the current product stage team, but do not hide older teams if no match.
            $employeeId = (int) ($row['employee_id'] ?? $row['id'] ?? 0);

            if ($employeeId > 0) {
                $ids[] = $employeeId;
            }
        }

        return array_values(array_unique(array_filter($ids)));
    }

    private function displayCustomerNameFromRow($row): string
    {
        if (!empty($row->firma)) {
            return $row->firma;
        }

        $full = trim(($row->customer_name ?? '') . ' ' . ($row->customer_lastname ?? ''));

        if ($full !== '') {
            return $full;
        }

        if (!empty($row->customer_no)) {
            return '#' . $row->customer_no;
        }

        return '#' . $row->customer_id;
    }

    public function montageWorkPayload(Request $request, int $project)
    {
        $date = (string) $request->query('date', now()->toDateString());
        $mode = (string) $request->query('mode', 'day');
        $planId = (int) $request->query('plan_id', 0);
        $showDone = $request->boolean('show_done', false);

        $payload = $this->pmoBuildMontagePayload($project, $date, $mode, $planId, $showDone);

        if (!$payload) {
            return response()->json([
                'ok' => false,
                'message' => 'Projekt wurde nicht gefunden.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => $payload,
        ]);
    }

    public function saveProjectTeamMember(Request $request, int $project)
    {
        $data = $request->validate([
            'action' => ['required', Rule::in(['add', 'remove'])],
            'employee_id' => ['required', 'integer', 'min:1'],
        ]);

        $row = LeadProductList::query()->findOrFail($project);

        $ids = $this->pmoTeamIds($row->teams ?? []);
        $employeeId = (int) $data['employee_id'];

        if ($data['action'] === 'add') {
            $ids[] = $employeeId;
        } else {
            $ids = array_values(array_filter($ids, fn($id) => (int) $id !== $employeeId));
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        $row->teams = $ids;
        $row->save();

        return response()->json([
            'ok' => true,
            'team_ids' => $ids,
        ]);
    }

    public function storeProjectWorkItem(Request $request, int $project)
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'min:1'],
            'type' => ['required', Rule::in(['kanban_task', 'personal_task', 'appointment', 'ticket'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'mode' => ['nullable', Rule::in(['single', 'bulk'])],
            'steps' => ['nullable', 'array'],
            'steps.*.title' => ['nullable', 'string', 'max:255'],
            'steps.*.description' => ['nullable', 'string'],
            'steps.*.employee_id' => ['nullable', 'integer', 'min:1'],
            'steps.*.due_date' => ['nullable', 'date'],
            'steps.*.due_time' => ['nullable', 'date_format:H:i'],
            'steps.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $projectRow = LeadProductList::query()->with(['companyStage', 'leadStageSubStage'])->findOrFail($project);
        $employeeId = (int) $data['employee_id'];
        $date = $data['date'] ?? now()->toDateString();
        $startTime = $data['start_time'] ?? '08:00';
        $endTime = $data['end_time'] ?? '09:00';
        $startsAt = $date . ' ' . $startTime . ':00';
        $endsAt = $date . ' ' . $endTime . ':00';

        $stage = $this->pmoMontageStage();
        $stageId = $stage?->id ?: ($projectRow->companyStage?->id ?? null);
        $subStageId = $projectRow->lead_stage_sub_stage_id ?: ($stage?->defaultSubStage?->id ?? null);

        $base = [
            'lead_product_list_id' => (int) $projectRow->id,
            'customer_id' => (int) $projectRow->customer_id,
            'alternative_id' => $projectRow->alternative_id ? (int) $projectRow->alternative_id : null,
            'product_id' => $projectRow->product_id ? (int) $projectRow->product_id : null,
            'lead_stage_id' => $stageId,
            'lead_stage_sub_stage_id' => $subStageId,
            'lead_sub_stage_id' => $subStageId,
            'title' => $data['title'],
            'task_title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'open',
            'task_status' => 'open',
            'created_by' => $this->authEmployeeId() ?? auth()->id(),
            'updated_by' => $this->authEmployeeId() ?? auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $created = null;

        DB::transaction(function () use ($data, $base, $employeeId, $startsAt, $endsAt, $date, $startTime, $endTime, &$created, $projectRow) {
            if ($data['type'] === 'kanban_task') {
                $table = 'kanban_lead_tasks';
                if (!Schema::hasTable($table)) {
                    throw new \RuntimeException('Tabelle kanban_lead_tasks wurde nicht gefunden.');
                }

                $id = $this->pmoInsertGetId($table, array_merge($base, [
                    'planned_start_at' => $startsAt,
                    'planned_end_at' => $endsAt,
                    'performer_employee_id' => $employeeId,
                ]));

                if (Schema::hasTable('kanban_lead_task_employees')) {
                    $fk = Schema::hasColumn('kanban_lead_task_employees', 'kanban_lead_task_id')
                        ? 'kanban_lead_task_id'
                        : (Schema::hasColumn('kanban_lead_task_employees', 'task_id') ? 'task_id' : null);

                    if ($fk) {
                        DB::table('kanban_lead_task_employees')->insertOrIgnore([
                            $fk => $id,
                            'employee_id' => $employeeId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                $created = ['id' => $id, 'type' => 'kanban_task'];
            }

            if ($data['type'] === 'personal_task') {
                $table = 'personal_tasks';
                if (!Schema::hasTable($table)) {
                    throw new \RuntimeException('Tabelle personal_tasks wurde nicht gefunden.');
                }

                $id = $this->pmoInsertGetId($table, array_merge($base, [
                    'due_date' => $date,
                    'due_time' => $startTime . ':00',
                    'board_column' => 'today',
                    'assigned_by' => $employeeId,
                ]));

                if (Schema::hasTable('employees_personal_tasks')) {
                    DB::table('employees_personal_tasks')->insertOrIgnore([
                        'task_id' => $id,
                        'employee_id' => $employeeId,
                        'status' => 'open',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $created = ['id' => $id, 'type' => 'personal_task'];
            }

            if ($data['type'] === 'appointment') {
                $table = 'main_appointments';
                if (!Schema::hasTable($table)) {
                    throw new \RuntimeException('Tabelle main_appointments wurde nicht gefunden.');
                }

                $id = $this->pmoInsertGetId($table, array_merge($base, [
                    'name' => $data['title'],
                    'event_date' => $date,
                    'event_time' => $startTime . ':00',
                    'event_end_time' => $endTime . ':00',
                    'start_date' => $date,
                    'start_time' => $startTime . ':00',
                    'end_date' => $date,
                    'end_time' => $endTime . ':00',
                    'employee_id' => $employeeId,
                    'duration_minutes' => max(1, Carbon::parse($startsAt)->diffInMinutes(Carbon::parse($endsAt), false)),
                ]));

                if (Schema::hasTable('main_appointment_employees')) {
                    DB::table('main_appointment_employees')->insertOrIgnore([
                        'appointment_id' => $id,
                        'employee_id' => $employeeId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $created = ['id' => $id, 'type' => 'appointment'];
            }

            if ($data['type'] === 'ticket') {
                $table = 'problems';
                if (!Schema::hasTable($table)) {
                    throw new \RuntimeException('Tabelle problems wurde nicht gefunden.');
                }

                $id = $this->pmoInsertGetId($table, array_merge($base, [
                    'problem' => $data['title'],
                    'date' => $date,
                    'responsible' => $employeeId,
                ]));

                if (Schema::hasTable('employee_problem')) {
                    DB::table('employee_problem')->insertOrIgnore([
                        'problem_id' => $id,
                        'employee_id' => $employeeId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $created = ['id' => $id, 'type' => 'ticket'];
            }

            if (($data['mode'] ?? 'single') === 'bulk' && !empty($data['steps']) && !empty($created['id'])) {
                $this->pmoCreateWorkSteps(
                    (string) $created['type'],
                    (int) $created['id'],
                    (array) $data['steps'],
                    $employeeId,
                    $projectRow,
                    $date,
                    $startTime
                );
            }

            $this->pmoAddEmployeesToProjectTeam((int) $projectRow->id, [$employeeId]);
        });

        return response()->json([
            'ok' => true,
            'created' => $created,
        ]);
    }

    private function pmoCreateWorkSteps(string $sourceType, int $sourceId, array $steps, int $defaultEmployeeId, LeadProductList $project, string $defaultDate, string $defaultTime): void
    {
        $cleanSteps = collect($steps)
            ->map(function ($step, $index) use ($defaultEmployeeId, $defaultDate, $defaultTime) {
                $title = trim((string) ($step['title'] ?? ''));

                if ($title === '') {
                    return null;
                }

                return [
                    'title' => $title,
                    'description' => $step['description'] ?? null,
                    'employee_id' => (int) ($step['employee_id'] ?? $defaultEmployeeId),
                    'due_date' => $step['due_date'] ?? $defaultDate,
                    'due_time' => $step['due_time'] ?? $defaultTime,
                    'sort_order' => (int) ($step['sort_order'] ?? ($index + 1)),
                ];
            })
            ->filter()
            ->values();

        if ($cleanSteps->isEmpty()) {
            return;
        }

        if ($sourceType === 'personal_task' && Schema::hasTable('personal_task_keys')) {
            foreach ($cleanSteps as $step) {
                $this->pmoInsertGetId('personal_task_keys', [
                    'personal_task_id' => $sourceId,
                    'task_id' => $sourceId,
                    'title' => $step['title'],
                    'key_name' => $step['title'],
                    'name' => $step['title'],
                    'description' => $step['description'],
                    'employee_id' => $step['employee_id'],
                    'assigned_to' => $step['employee_id'],
                    'employee_ids' => json_encode([$step['employee_id']]),
                    'due_date' => $step['due_date'],
                    'due_time' => $this->normalizeTime($step['due_time'], '08:00:00'),
                    'is_completed' => 0,
                    'status' => 'open',
                    'sort_order' => $step['sort_order'],
                    'created_by' => $this->authEmployeeId() ?? auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return;
        }

        $candidateTables = $sourceType === 'kanban_task'
            ? ['kanban_lead_task_steps', 'kanban_lead_task_keys', 'kanban_task_steps', 'kanban_task_keys']
            : ['planner_work_steps'];

        foreach ($candidateTables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($cleanSteps as $step) {
                $this->pmoInsertGetId($table, [
                    'kanban_lead_task_id' => $sourceId,
                    'task_id' => $sourceId,
                    'source_type' => $sourceType,
                    'source_id' => $sourceId,
                    'lead_product_list_id' => (int) $project->id,
                    'customer_id' => (int) $project->customer_id,
                    'alternative_id' => $project->alternative_id ? (int) $project->alternative_id : null,
                    'product_id' => $project->product_id ? (int) $project->product_id : null,
                    'title' => $step['title'],
                    'key_name' => $step['title'],
                    'name' => $step['title'],
                    'description' => $step['description'],
                    'employee_id' => $step['employee_id'],
                    'assigned_to' => $step['employee_id'],
                    'employee_ids' => json_encode([$step['employee_id']]),
                    'due_date' => $step['due_date'],
                    'due_time' => $this->normalizeTime($step['due_time'], '08:00:00'),
                    'is_completed' => 0,
                    'status' => 'open',
                    'sort_order' => $step['sort_order'],
                    'created_by' => $this->authEmployeeId() ?? auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return;
        }
    }

    private function pmoEnrichMontageItem(array $item, PlannerPlan $plan): array
    {
        $sourceType = (string) ($item['source_type'] ?? '');
        $sourceId = (int) ($item['source_id'] ?? $item['id'] ?? 0);

        $plannerItem = null;

        if (Schema::hasTable('planner_items') && $sourceType !== '' && $sourceId > 0) {
            $plannerItem = DB::table('planner_items')
                ->where('plan_id', (int) $plan->id)
                ->where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->when($this->safeColumn('planner_items', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->first();
        }

        if (!$plannerItem && Schema::hasTable('planner_items') && $sourceType !== '' && $sourceId > 0) {
            $duration = (int) ($item['duration_minutes'] ?? 0);

            if ($duration <= 0 && !empty($item['planned_start_at']) && !empty($item['planned_end_at'])) {
                try {
                    $duration = max(1, Carbon::parse($item['planned_start_at'])->diffInMinutes(Carbon::parse($item['planned_end_at']), false));
                } catch (\Throwable $e) {
                    $duration = 60;
                }
            }

            if ($duration <= 0) {
                $duration = 60;
            }

            $plannerItem = PlannerItem::query()->create([
                'plan_id' => (int) $plan->id,
                'client_uid' => (string) Str::uuid(),
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'title' => $item['title'] ?? ('Arbeit #' . $sourceId),
                'description' => $item['description'] ?? null,
                'duration_minutes' => $duration,
                'status' => $item['status'] ?? 'open',
                'planned_start_at' => $item['planned_start_at'] ?? $item['start_at'] ?? null,
                'planned_end_at' => $item['planned_end_at'] ?? $item['end_at'] ?? null,
                'sort_order' => (int) ($item['sort_order'] ?? 9999),
            ]);

            $employeeIds = array_values(array_unique(array_filter(array_map('intval', $item['employee_ids'] ?? []))));

            if (!empty($employeeIds) && Schema::hasTable('planner_item_employees')) {
                foreach ($employeeIds as $index => $employeeId) {
                    DB::table('planner_item_employees')->insertOrIgnore([
                        'planner_item_id' => (int) $plannerItem->id,
                        'employee_id' => (int) $employeeId,
                        'role' => $index === 0 ? 'lead' : 'member',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $plannerItemId = $plannerItem->id ?? null;
        $item['planner_item_id'] = $plannerItemId;

        if ($plannerItem) {
            $plannerStatus = $this->pmoNormalizePlannerStatus((string) ($plannerItem->status ?? ($item['status'] ?? 'open')));
            $doneById = $this->pmoPlannerDoneByEmployeeId($plannerItem) ?: ($item['done_by_employee_id'] ?? null);
            $doneByEmployee = $this->pmoEmployeeMini($doneById ? (int) $doneById : null);

            $item['status'] = $plannerStatus;
            $item['status_label'] = $this->pmoStatusLabel($plannerStatus);
            $item['is_done'] = $this->pmoIsDoneStatus($plannerStatus);
            $item['done_at'] = $this->pmoPlannerDoneAt($plannerItem) ?: ($item['done_at'] ?? null);
            $item['done_by_employee_id'] = $doneById ? (int) $doneById : null;
            $item['done_by_employee'] = $doneByEmployee;
            $item['done_by_name'] = $doneByEmployee['full_name'] ?? null;
            $item['status_history'] = $this->pmoLoadPlannerStatusHistory((int) $plannerItem->id);
        }

        $item['steps'] = array_values(array_merge(
            $this->pmoLoadSourceSteps($sourceType, $sourceId),
            $plannerItemId ? $this->pmoLoadPlannerItemSteps((int) $plannerItemId) : []
        ));
        $item['checklist'] = $plannerItemId ? $this->pmoLoadPlannerChecklists((int) $plannerItemId) : [];
        $item['materials'] = $plannerItemId ? $this->pmoLoadPlannerMaterials((int) $plannerItemId) : [];
        $item['material_summary'] = $this->pmoPlannerMaterialSummary($item['materials']);
        $item['shared_materials'] = $plannerItemId ? $this->pmoLoadPlannerGroupMaterialsForItem((int) $plannerItemId) : [];
        $item['shared_material_summary'] = $this->pmoPlannerMaterialSummary($item['shared_materials']);
        $item['assets'] = $plannerItemId ? $this->pmoLoadPlannerAssets((int) $plannerItemId, $plan) : [];
        $item['comments'] = $plannerItemId ? $this->pmoLoadPlannerComments((int) $plannerItemId) : [];
        $item['reports'] = $item['comments'];

        return $item;
    }

    private function pmoLoadSourceSteps(string $sourceType, int $sourceId): array
    {
        if ($sourceId <= 0) {
            return [];
        }

        $table = null;
        $fk = null;

        if ($sourceType === 'personal_task' && Schema::hasTable('personal_task_keys')) {
            $table = 'personal_task_keys';
            $fk = Schema::hasColumn($table, 'personal_task_id') ? 'personal_task_id' : (Schema::hasColumn($table, 'task_id') ? 'task_id' : null);
        }

        if ($sourceType === 'kanban_task') {
            foreach (['kanban_lead_task_steps', 'kanban_lead_task_keys', 'kanban_task_steps', 'kanban_task_keys'] as $candidate) {
                if (!Schema::hasTable($candidate)) {
                    continue;
                }

                $candidateFk = Schema::hasColumn($candidate, 'kanban_lead_task_id')
                    ? 'kanban_lead_task_id'
                    : (Schema::hasColumn($candidate, 'task_id') ? 'task_id' : null);

                if ($candidateFk) {
                    $table = $candidate;
                    $fk = $candidateFk;
                    break;
                }
            }
        }

        if (!$table || !$fk) {
            return [];
        }

        return DB::table($table)
            ->where($fk, $sourceId)
            ->when($this->safeColumn($table, 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->orderBy($this->safeColumn($table, 'sort_order') ? 'sort_order' : 'id')
            ->limit(100)
            ->get()
            ->map(fn($row) => [
                'id' => (int) $row->id,
                'title' => $row->title ?? $row->key_name ?? $row->name ?? $row->task_title ?? ('Schritt #' . $row->id),
                'description' => $row->description ?? $row->note ?? null,
                'employee_id' => $row->employee_id ?? $row->assigned_to ?? $row->done_by ?? null,
                'due_date' => $row->due_date ?? $row->date ?? null,
                'due_time' => $row->due_time ?? $row->time ?? null,
                'status' => $row->status ?? null,
                'is_completed' => (bool) ($row->is_completed ?? $row->is_done ?? false),
                'sort_order' => $row->sort_order ?? null,
            ])
            ->values()
            ->all();
    }

    private function pmoLoadPlannerChecklists(int $plannerItemId): array
    {
        if (!Schema::hasTable('planner_item_checklists')) {
            return [];
        }

        return DB::table('planner_item_checklists')
            ->where('planner_item_id', $plannerItemId)
            ->orderBy($this->safeColumn('planner_item_checklists', 'sort_order') ? 'sort_order' : 'id')
            ->get()
            ->map(fn($row) => [
                'id' => (int) $row->id,
                'title' => $row->title ?? $row->name ?? ('Check #' . $row->id),
                'is_completed' => (bool) ($row->is_completed ?? false),
            ])
            ->values()
            ->all();
    }

    private function pmoLoadPlannerMaterials(int $plannerItemId): array
    {
        $materials = [];

        if (Schema::hasTable('planner_item_materials')) {
            $materials = DB::table('planner_item_materials')
                ->where('planner_item_id', $plannerItemId)
                // Shared employee/day material must NOT be counted as material for every single task.
                // It is loaded separately through pmoLoadPlannerGroupMaterialsForItem().
                ->when($this->safeColumn('planner_item_materials', 'material_group_uuid'), function ($q) {
                    $q->where(function ($w) {
                        $w->whereNull('material_group_uuid')
                            ->orWhere('material_group_uuid', '');
                    });
                })
                ->when($this->safeColumn('planner_item_materials', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->orderBy($this->safeColumn('planner_item_materials', 'sort_order') ? 'sort_order' : 'id')
                ->orderBy('id')
                ->get()
                ->map(fn($row) => $this->plannerMaterialRowPayload($row))
                ->values()
                ->all();
        }

        /*
        |--------------------------------------------------------------------------
        | Nuriva mobile material requests
        |--------------------------------------------------------------------------
        | Employees can request material in Nuriva even when the article is not
        | available in Master Set / product catalog. Those requests are saved in
        | planner_item_material_requests and shown beside normal planner material.
        */
        $requests = $this->pmoLoadPlannerMaterialRequests($plannerItemId);

        return collect($materials)
            ->concat($requests)
            ->values()
            ->all();
    }

    private function pmoLoadPlannerMaterialRequests(int $plannerItemId): array
    {
        if (!Schema::hasTable('planner_item_material_requests')) {
            return [];
        }

        $q = DB::table('planner_item_material_requests as pmr')
            ->where('pmr.planner_item_id', $plannerItemId);

        if ($this->safeColumn('planner_item_material_requests', 'deleted_at')) {
            $q->whereNull('pmr.deleted_at');
        }

        if (Schema::hasTable('employees')) {
            $q->leftJoin('employees as e', 'e.id', '=', 'pmr.requested_by_employee_id')
                ->select([
                    'pmr.*',
                    DB::raw("TRIM(CONCAT(COALESCE(e.title,''), ' ', COALESCE(e.name,''), ' ', COALESCE(e.lastname,''))) as requested_by_name"),
                ]);
        } else {
            $q->select([
                'pmr.*',
                DB::raw('NULL as requested_by_name'),
            ]);
        }

        return $q->orderByDesc('pmr.id')
            ->get()
            ->map(fn($row) => $this->plannerMaterialRequestRowPayload($row))
            ->values()
            ->all();
    }

    private function plannerMaterialRequestRowPayload(object $row): array
    {
        $status = mb_strtolower(trim((string) ($row->status ?? 'requested')));
        $isResponded = in_array($status, ['approved', 'accepted', 'rejected', 'declined', 'ordered', 'received', 'done', 'completed', 'added'], true)
            || !empty($row->responded_at)
            || !empty($row->approved_at)
            || !empty($row->rejected_at)
            || !empty($row->ordered_at)
            || !empty($row->received_at);

        $meta = json_decode((string) ($row->meta ?? ''), true);
        $meta = is_array($meta) ? $meta : [];
        $qty = (float) ($row->quantity ?? $row->qty ?? 1);
        $unit = $row->unit ?? $row->measure_unit ?? 'Stk';
        $name = $row->name ?? $row->article_name ?? ('Materialanfrage #' . $row->id);

        return [
            'id' => 'request-' . (int) $row->id,
            'material_request_id' => (int) $row->id,
            'planner_item_id' => (int) ($row->planner_item_id ?? 0),
            'planner_plan_id' => $row->planner_plan_id ? (int) $row->planner_plan_id : null,
            'lead_product_list_id' => $row->lead_product_list_id ? (int) $row->lead_product_list_id : null,
            'customer_id' => $row->customer_id ? (int) $row->customer_id : null,
            'alternative_id' => $row->alternative_id ? (int) $row->alternative_id : null,
            'product_id' => $row->product_id ? (int) $row->product_id : null,
            'source_type' => 'nuriva_mobile',
            'source_id' => (int) $row->id,
            'origin_type' => 'employee_request',
            'origin' => 'employee_request',
            'source_origin' => 'employee_request',
            'type' => 'manual_request',
            'is_request' => true,
            'is_material_request' => true,
            'is_request_open' => !$isResponded,
            'is_added' => false,
            'active' => false,
            'is_active' => false,
            'name' => $name,
            'title' => $name,
            'material_name' => $name,
            'product' => $name,
            'article_name' => $row->article_name ?? $name,
            'article_no' => $row->article_no ?? null,
            'description' => $row->description ?? null,
            'qty' => $qty,
            'quantity' => $qty,
            'unit' => $unit,
            'measure' => $unit,
            'measure_unit' => $unit,
            'unit_price' => 0,
            'price' => 0,
            'purchase_price' => 0,
            'ek' => 0,
            'total_price' => 0,
            'distributor_id' => null,
            'distributor_price_id' => null,
            'distributor_name' => null,
            'supplier' => null,
            'priority' => $row->priority ?? 'normal',
            'needed_at' => $row->needed_at ?? null,
            'note' => $row->note ?? null,
            'status' => $row->status ?? 'requested',
            'request_status' => $row->status ?? 'requested',
            'requested_at' => $row->created_at ?? null,
            'requested_by_employee_id' => $row->requested_by_employee_id ? (int) $row->requested_by_employee_id : null,
            'requested_by_name' => trim((string) ($row->requested_by_name ?? '')) ?: null,
            'created_at' => $row->created_at ?? null,
            'updated_at' => $row->updated_at ?? null,
            'meta' => $meta,
        ];
    }

    private function pmoPlannerMaterialSummary(array $materials): array
    {
        $total = count($materials);
        $active = 0;
        $inactive = 0;
        $requestedTotal = 0;
        $requestedOpen = 0;
        $requestedResponded = 0;
        $added = 0;

        foreach ($materials as $material) {
            $isActive = (bool) ($material['active'] ?? $material['is_active'] ?? true);
            $isRequest = (bool) ($material['is_request'] ?? false);
            $isOpenRequest = (bool) ($material['is_request_open'] ?? false);
            $isAdded = (bool) ($material['is_added'] ?? ($isActive && !$isOpenRequest));

            if ($isActive) {
                $active++;
            } else {
                $inactive++;
            }

            if ($isRequest) {
                $requestedTotal++;
                if ($isOpenRequest) {
                    $requestedOpen++;
                } else {
                    $requestedResponded++;
                }
            }

            if ($isAdded) {
                $added++;
            }
        }

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'added' => $added,
            'requested_total' => $requestedTotal,
            'requested_open' => $requestedOpen,
            'requested_not_responded' => $requestedOpen,
            'requested_responded' => $requestedResponded,
        ];
    }

    private function pmoLoadPlannerAssets(int $plannerItemId, ?PlannerPlan $plan = null): array
    {
        $assets = [];

        if (Schema::hasTable('images')) {
            $q = DB::table('images')
                ->where(function ($query) use ($plannerItemId) {
                    if ($this->safeColumn('images', 'planner_item_id')) {
                        $query->where('planner_item_id', $plannerItemId)
                            ->orWhere('task_id', $plannerItemId);
                    } else {
                        $query->where('task_id', $plannerItemId);
                    }
                });

            if ($plan) {
                $ctx = $this->plannerPlanProjectContext($plan);

                if (!empty($ctx['customer_id'])) {
                    $q->where('customer_id', (int) $ctx['customer_id']);
                }
            }

            if ($this->safeColumn('images', 'deleted_at')) {
                $q->whereNull('deleted_at');
            }

            $assets = $q->orderByDesc('id')
                ->limit(80)
                ->get()
                ->map(function ($row) {
                    $url = $this->plannerCustomerFileUrl($row->image ?? $row->file_path ?? null);
                    $name = $row->image_name ?? $row->title ?? $row->image ?? ('Datei #' . $row->id);
                    $fileType = strtolower((string) ($row->file_type ?? pathinfo((string) ($row->image ?? ''), PATHINFO_EXTENSION)));

                    return [
                        'id' => (int) $row->id,
                        'image_id' => (int) $row->id,
                        'name' => $name,
                        'file_name' => $name,
                        'url' => $url,
                        'file_url' => $url,
                        'file_path' => $url,
                        'mime_type' => $fileType,
                        'file_type' => $fileType,
                        'customer_id' => $row->customer_id ?? null,
                        'alternative_id' => $row->alternative_id ?? null,
                        'product_id' => $row->article_group ?? null,
                        'article_group' => $row->article_group ?? null,
                        'stage' => $row->stage ?? null,
                        'created_at' => $row->created_at ?? null,
                    ];
                })
                ->values()
                ->all();
        }

        if (Schema::hasTable('planner_item_assets')) {
            $plannerAssets = DB::table('planner_item_assets')
                ->where('planner_item_id', $plannerItemId)
                ->orderByDesc('id')
                ->limit(80)
                ->get()
                ->map(function ($row) {
                    $path = $row->url ?? $row->file_url ?? $row->path ?? $row->file_path ?? $row->image ?? null;
                    $url = $path;

                    if ($url && !Str::startsWith($url, ['http://', 'https://', '/'])) {
                        $url = asset($url);
                    }

                    return [
                        'id' => (int) $row->id,
                        'name' => $row->name ?? $row->file_name ?? $row->title ?? ('Datei #' . $row->id),
                        'url' => $url,
                        'file_url' => $url,
                        'mime_type' => $row->mime_type ?? null,
                    ];
                })
                ->values()
                ->all();

            $assets = array_values(array_merge($assets, $plannerAssets));
        }

        return $assets;
    }

    private function pmoLoadPlannerComments(int $plannerItemId): array
    {
        $table = $this->plannerCommentTable();

        if (!$table) {
            return [];
        }

        $query = DB::table($table)
            ->where('planner_item_id', $plannerItemId);

        if ($this->safeColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return $query
            ->orderByDesc('id')
            ->limit(80)
            ->get()
            ->map(fn($row) => $this->plannerCommentRowPayload($row))
            ->values()
            ->all();
    }


    public function storeItemComment(Request $request, PlannerPlan $plan, PlannerItem $item)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        $table = $this->plannerCommentTable();

        if (!$table) {
            return response()->json([
                'ok' => false,
                'message' => 'planner_item_comments table does not exist. Run the supplied migration first.',
            ], 422);
        }

        $data = $request->validate([
            'body' => ['required', 'string'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $employeeId = $this->authEmployeeId();
        $context = $this->plannerContextColumns($plan, $item);

        $id = $this->plannerInsertRow($table, array_merge($context, [
            'planner_item_id' => (int) $item->id,
            'source_type' => $item->source_type ?? 'planner_item',
            'source_id' => $item->source_id ?? $item->id,
            'title' => $data['title'] ?? 'Kommentar',
            'subject' => $data['title'] ?? 'Kommentar',
            'body' => $data['body'],
            'comment' => $data['body'],
            'description' => $data['body'],
            'author_name' => auth()->user()?->name ?? null,
            'created_by_employee_id' => $employeeId,
            'created_by' => $employeeId ?? auth()->id(),
        ]));

        return response()->json([
            'ok' => true,
            'comment' => $this->plannerCommentRowPayload(DB::table($table)->where('id', $id)->first()),
            'comments' => $this->pmoLoadPlannerComments((int) $item->id),
        ]);
    }

    public function destroyItemComment(PlannerPlan $plan, PlannerItem $item, int $comment)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        $table = $this->plannerCommentTable();

        if (!$table) {
            return response()->json(['ok' => false, 'message' => 'planner_item_comments table does not exist.'], 422);
        }

        $row = DB::table($table)
            ->where('id', $comment)
            ->where('planner_item_id', (int) $item->id)
            ->first();

        if (!$row) {
            return response()->json(['ok' => false, 'message' => 'Kommentar wurde nicht gefunden.'], 404);
        }

        if ($this->safeColumn($table, 'deleted_at')) {
            DB::table($table)->where('id', $comment)->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table($table)->where('id', $comment)->delete();
        }

        return response()->json([
            'ok' => true,
            'comments' => $this->pmoLoadPlannerComments((int) $item->id),
        ]);
    }

    public function storeItemGallery(Request $request, PlannerPlan $plan, PlannerItem $item)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        if (!Schema::hasTable('images')) {
            return response()->json([
                'ok' => false,
                'message' => 'images table does not exist.',
            ], 422);
        }

        $request->validate([
            'files' => ['required'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx', 'max:51200'],
            'stage' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:80'],
        ]);

        $files = $request->file('files');

        if (!is_array($files)) {
            $files = [$files];
        }

        $context = $this->plannerItemCustomerContext($plan, $item);

        if (empty($context['customer_id'])) {
            return response()->json([
                'ok' => false,
                'message' => 'Kunde konnte für diese Planner-Aufgabe nicht ermittelt werden.',
            ], 422);
        }

        $employeeId = $this->authEmployeeId();
        $saved = [];

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }

            $originalName = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $file->getClientOriginalName());
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = time() . '_' . uniqid() . '_' . $originalName;

            $path = $file->storeAs('uploads/customers', $filename, 'public');

            if (!$path) {
                continue;
            }

            $insert = $this->plannerFilterTableData('images', [
                'customer_id' => (int) $context['customer_id'],
                'lead_product_list_id' => $context['lead_product_list_id'],
                'planner_item_id' => (int) $item->id,
                'article_group' => $context['product_id'],
                'alternative_id' => $context['alternative_id'],
                'phase_id' => null,
                'task_id' => (int) $item->id,
                'sub_task_id' => null,
                'created_by' => $employeeId ?? auth()->id(),
                'update_by' => null,
                'stage' => $request->input('stage', 'planner_task'),
                'image_name' => pathinfo($originalName, PATHINFO_FILENAME),
                'image' => $filename,
                'file_type' => $extension,
                'status' => $request->input('status', 'planner_gallery'),
            ], true);

            $id = DB::table('images')->insertGetId($insert);
            $saved[] = $id;
        }

        return response()->json([
            'ok' => true,
            'saved' => $saved,
            'assets' => $this->pmoLoadPlannerAssets((int) $item->id, $plan),
        ]);
    }

    public function destroyItemGallery(PlannerPlan $plan, PlannerItem $item, int $image)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        if (!Schema::hasTable('images')) {
            return response()->json(['ok' => false, 'message' => 'images table does not exist.'], 422);
        }

        $row = DB::table('images')
            ->where('id', $image)
            ->where(function ($query) use ($item) {
                if ($this->safeColumn('images', 'planner_item_id')) {
                    $query->where('planner_item_id', (int) $item->id)
                        ->orWhere('task_id', (int) $item->id);
                } else {
                    $query->where('task_id', (int) $item->id);
                }
            })
            ->first();

        if (!$row) {
            return response()->json(['ok' => false, 'message' => 'Bild wurde nicht gefunden.'], 404);
        }

        $file = trim((string) ($row->image ?? ''));

        if ($file !== '') {
            $path = 'uploads/customers/' . ltrim($file, '/');
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
        }

        if ($this->safeColumn('images', 'deleted_at')) {
            DB::table('images')->where('id', $image)->update([
                'deleted_at' => now(),
                'update_by' => $this->authEmployeeId() ?? auth()->id(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('images')->where('id', $image)->delete();
        }

        return response()->json([
            'ok' => true,
            'assets' => $this->pmoLoadPlannerAssets((int) $item->id, $plan),
        ]);
    }




    private function pmoBuildDependencyPayload(PlannerPlan $plan): array
    {
        $items = PlannerItem::query()
            ->with(['employees'])
            ->where('plan_id', (int) $plan->id)
            ->when($this->safeColumn('planner_items', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->orderByRaw('COALESCE(sort_order, 999999) ASC')
            ->orderByRaw('COALESCE(planned_start_at, created_at) ASC')
            ->orderBy('id')
            ->get();

        $itemsById = $items->keyBy('id');
        $childrenByParent = [];
        $parentsByChild = [];
        $edges = [];

        if ($items->isNotEmpty() && Schema::hasTable('planner_item_dependencies')) {
            $edgeRows = DB::table('planner_item_dependencies as d')
                ->join('planner_items as child', 'child.id', '=', 'd.planner_item_id')
                ->join('planner_items as parent', 'parent.id', '=', 'd.depends_on_item_id')
                ->where('child.plan_id', (int) $plan->id)
                ->where('parent.plan_id', (int) $plan->id)
                ->when($this->safeColumn('planner_items', 'deleted_at'), function ($q) {
                    $q->whereNull('child.deleted_at')->whereNull('parent.deleted_at');
                })
                ->select([
                    'd.id',
                    'd.planner_item_id',
                    'd.depends_on_item_id',
                    'd.reason',
                ])
                ->orderBy('d.id')
                ->get();

            foreach ($edgeRows as $edge) {
                $childId = (int) $edge->planner_item_id;
                $parentId = (int) $edge->depends_on_item_id;

                $child = $itemsById->get($childId);
                $parent = $itemsById->get($parentId);

                if (!$child || !$parent) {
                    continue;
                }

                $childrenByParent[$parentId][] = $childId;
                $parentsByChild[$childId][] = $parentId;

                $gapMinutes = $this->pmoDependencyGapMinutes($parent, $child);

                $edges[] = [
                    'id' => (int) $edge->id,
                    'from' => $parentId,
                    'to' => $childId,
                    'from_title' => $parent->title,
                    'to_title' => $child->title,
                    'reason' => $edge->reason,
                    'gap_minutes' => $gapMinutes,
                    'gap_label' => $this->pmoFormatMinutesHuman($gapMinutes),
                ];
            }
        }

        $sequence = $this->pmoDependencySequence($items, $parentsByChild, $childrenByParent);

        $roots = $items
            ->filter(fn($item) => empty($parentsByChild[(int) $item->id]))
            ->sortBy(fn($item) => $sequence[(int) $item->id] ?? 999999)
            ->values();

        $tree = $roots
            ->map(fn($item) => $this->pmoDependencyTreeNode($item, $itemsById, $childrenByParent, $sequence, []))
            ->values()
            ->all();

        $totalMinutes = (int) $items->sum(fn($item) => (int) ($item->duration_minutes ?? 0));
        $personMinutes = 0;
        $employeeMap = [];

        foreach ($items as $item) {
            $duration = max(0, (int) ($item->duration_minutes ?? 0));
            $employees = $item->employees ?? collect();

            foreach ($employees as $employee) {
                $employeeMap[(int) $employee->id] = trim(($employee->title ?? '') . ' ' . ($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
            }

            $personMinutes += max(1, $employees->count()) * $duration;
        }

        $starts = $items
            ->map(fn($item) => $item->planned_start_at ? Carbon::parse($item->planned_start_at) : null)
            ->filter();

        $ends = $items
            ->map(fn($item) => $this->pmoPlannerItemEndCarbon($item))
            ->filter();

        $spanMinutes = 0;

        if ($starts->isNotEmpty() && $ends->isNotEmpty()) {
            $spanMinutes = max(0, $starts->min()->diffInMinutes($ends->max(), false));
        }

        return [
            'edges' => $edges,
            'tree' => $tree,
            'sequence' => $sequence,
            'summary' => [
                'total_tasks' => $items->count(),
                'total_dependencies' => count($edges),
                'total_minutes' => $totalMinutes,
                'total_label' => $this->pmoFormatMinutesHuman($totalMinutes),
                'project_span_minutes' => $spanMinutes,
                'project_span_label' => $this->pmoFormatMinutesHuman($spanMinutes),
                'person_minutes' => $personMinutes,
                'person_label' => $this->pmoFormatMinutesHuman($personMinutes),
                'employee_count' => count($employeeMap),
                'employees' => array_values(array_filter($employeeMap)),
            ],
        ];
    }

    private function pmoDependencyTreeNode(PlannerItem $item, $itemsById, array $childrenByParent, array $sequence, array $visited): array
    {
        $itemId = (int) $item->id;

        if (isset($visited[$itemId])) {
            return [
                'id' => $itemId,
                'sequence' => $sequence[$itemId] ?? null,
                'title' => $item->title,
                'cycle' => true,
                'children' => [],
            ];
        }

        $visited[$itemId] = true;

        $children = collect($childrenByParent[$itemId] ?? [])
            ->map(fn($childId) => $itemsById->get((int) $childId))
            ->filter()
            ->sortBy(fn($child) => $sequence[(int) $child->id] ?? 999999)
            ->map(function ($child) use ($item, $itemsById, $childrenByParent, $sequence, $visited) {
                $node = $this->pmoDependencyTreeNode($child, $itemsById, $childrenByParent, $sequence, $visited);
                $gapMinutes = $this->pmoDependencyGapMinutes($item, $child);
                $node['gap_from_parent_minutes'] = $gapMinutes;
                $node['gap_from_parent_label'] = $this->pmoFormatMinutesHuman($gapMinutes);
                return $node;
            })
            ->values()
            ->all();

        return [
            'id' => $itemId,
            'sequence' => $sequence[$itemId] ?? null,
            'title' => $item->title,
            'status' => $item->status,
            'source_type' => $item->source_type,
            'duration_minutes' => (int) ($item->duration_minutes ?? 0),
            'duration_label' => $this->pmoFormatMinutesHuman((int) ($item->duration_minutes ?? 0)),
            'planned_start_at' => $item->planned_start_at ? Carbon::parse($item->planned_start_at)->toDateTimeString() : null,
            'planned_end_at' => $this->pmoPlannerItemEndCarbon($item)?->toDateTimeString(),
            'employees' => ($item->employees ?? collect())->map(fn($employee) => [
                'id' => (int) $employee->id,
                'name' => trim(($employee->title ?? '') . ' ' . ($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                'role' => $employee->pivot->role ?? null,
            ])->values()->all(),
            'children' => $children,
        ];
    }

    private function pmoDependencySequence($items, array $parentsByChild, array $childrenByParent): array
    {
        $inDegree = [];
        $sequence = [];

        foreach ($items as $item) {
            $id = (int) $item->id;
            $inDegree[$id] = count($parentsByChild[$id] ?? []);
        }

        $queue = $items
            ->filter(fn($item) => ($inDegree[(int) $item->id] ?? 0) === 0)
            ->sortBy(fn($item) => [
                (string) ($item->planned_start_at ?? ''),
                (int) ($item->sort_order ?? 999999),
                (int) $item->id,
            ])
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();

        $counter = 1;

        while (!empty($queue)) {
            $id = array_shift($queue);

            if (isset($sequence[$id])) {
                continue;
            }

            $sequence[$id] = $counter++;

            foreach ($childrenByParent[$id] ?? [] as $childId) {
                $childId = (int) $childId;
                $inDegree[$childId] = max(0, ($inDegree[$childId] ?? 0) - 1);

                if ($inDegree[$childId] === 0) {
                    $queue[] = $childId;
                }
            }
        }

        foreach ($items as $item) {
            $id = (int) $item->id;

            if (!isset($sequence[$id])) {
                $sequence[$id] = $counter++;
            }
        }

        return $sequence;
    }

    private function pmoDependencyGapMinutes(PlannerItem $parent, PlannerItem $child): ?int
    {
        $parentEnd = $this->pmoPlannerItemEndCarbon($parent);
        $childStart = $child->planned_start_at ? Carbon::parse($child->planned_start_at) : null;

        if (!$parentEnd || !$childStart) {
            return null;
        }

        return $parentEnd->diffInMinutes($childStart, false);
    }

    private function pmoPlannerItemEndCarbon(PlannerItem $item): ?Carbon
    {
        if ($item->planned_end_at) {
            return Carbon::parse($item->planned_end_at);
        }

        if ($item->planned_start_at) {
            return Carbon::parse($item->planned_start_at)->addMinutes(max(1, (int) ($item->duration_minutes ?? 60)));
        }

        return null;
    }

    private function pmoFormatMinutesHuman(?int $minutes): string
    {
        if ($minutes === null) {
            return '—';
        }

        $negative = $minutes < 0;
        $minutes = abs($minutes);
        $days = intdiv($minutes, 1440);
        $hours = intdiv($minutes % 1440, 60);
        $mins = $minutes % 60;
        $parts = [];

        if ($days > 0) {
            $parts[] = $days . ' Tag' . ($days === 1 ? '' : 'e');
        }

        if ($hours > 0) {
            $parts[] = $hours . ' Std.';
        }

        if ($mins > 0 || empty($parts)) {
            $parts[] = $mins . ' Min.';
        }

        return ($negative ? 'Überlappung ' : '') . implode(' ', $parts);
    }

    private function pmoBuildMontagePayload(int $projectId, string $date, string $mode = 'day', int $planId = 0, bool $includeDone = false): ?array
    {
        $project = LeadProductList::query()
            ->with([
                'customer',
                'alternative',
                'product',
                'articleGroup',
                'service',
                'companyStage',
                'leadStageSubStage',
                'employee',
                'fieldEmployee',
            ])
            ->find($projectId);

        if (!$project) {
            return null;
        }

        [$from, $to, $periodLabel] = $this->pmoPeriodRange($date, $mode);

        $plan = $planId > 0
            ? PlannerPlan::query()->where('project_id', $project->id)->whereKey($planId)->first()
            : null;

        if (!$plan) {
            $plan = PlannerPlan::firstOrCreate(
                [
                    'customer_id' => (int) $project->customer_id,
                    'project_id' => (int) $project->id,
                ],
                [
                    'title' => 'Projektplan #' . $project->id,
                    'stage' => $project->status ?: 'project',
                    'status' => 'active',
                    'created_by' => auth()->id(),
                    'meta' => [
                        'product_id' => $project->product_id,
                        'alternative_id' => $project->alternative_id,
                        'lead_stage_sub_stage_id' => $project->lead_stage_sub_stage_id,
                    ],
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Important sync before loading cockpit cards
        |--------------------------------------------------------------------------
        | The project cockpit reads tasks/tickets/appointments directly from the
        | project modules and also needs planner_items to be created/updated.
        | Without this sync, the selected project can open but the employee columns
        | stay empty.
        */
        $this->syncProjectScopedPlan($plan);

        $items = collect()
            ->merge($this->pmoLoadKanbanTasks($project, $from, $to, $includeDone))
            ->merge($this->pmoLoadPersonalTasks($project, $from, $to, $includeDone))
            ->merge($this->pmoLoadAppointments($project, $from, $to, $includeDone))
            ->merge($this->pmoLoadTickets($project, $from, $to, $includeDone))
            ->merge($this->pmoLoadPhaseTemplateTasks($project, $from, $to, $includeDone))
            ->values()
            ->map(fn($item) => $this->pmoEnrichMontageItem((array) $item, $plan))
            ->filter(fn($item) => $includeDone || !$this->pmoIsDoneStatus($item['status'] ?? null))
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Fallback assignment for visible employee columns
        |--------------------------------------------------------------------------
        | The frontend renders cards inside employee columns by checking
        | item.employee_ids. If a task/ticket/appointment has no direct employee,
        | we attach the project manager / field employee / project team as a safe
        | display fallback. This does not overwrite the original source tables.
        */
        $fallbackEmployeeIds = $this->pmoFallbackEmployeeIds($project);

        if (!empty($fallbackEmployeeIds)) {
            $items = $items->map(function ($item) use ($fallbackEmployeeIds) {
                $employeeIds = $item['employee_ids'] ?? [];

                if (empty($employeeIds)) {
                    $item['employee_ids'] = $fallbackEmployeeIds;
                    $item['auto_assigned_from_project_team'] = true;
                }

                return $item;
            })->values();
        }

        if (!$includeDone) {
            $items = $items
                ->filter(fn($item) => !$this->pmoIsDoneStatus($item['status'] ?? null))
                ->values();
        }

        $ganttItems = $this->pmoBuildPlannerGanttItems($plan);
        $employees = $this->pmoBuildEmployees($project, $items->merge($ganttItems));

        $summary = [
            'period_label' => $periodLabel,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'kanban_tasks' => $items->where('source_type', 'kanban_task')->count(),
            'personal_tasks' => $items->where('source_type', 'personal_task')->count(),
            'appointments' => $items->where('source_type', 'appointment')->count(),
            'tickets' => $items->where('source_type', 'ticket')->count(),
            'phase_tasks' => $items->whereIn('source_type', ['task_phase', 'phase_activity'])->count(),
            'total' => $items->count(),
        ];

        $dependencyPayload = $this->pmoBuildDependencyPayload($plan);

        return [
            'project' => $this->pmoProjectPayload($project),
            'plan' => [
                'id' => $plan->id,
                'title' => $plan->title,
                'status' => $plan->status,
                'project_id' => $plan->project_id,
                'customer_id' => $plan->customer_id,
            ],
            'summary' => $summary,
            'employees' => $employees,
            'items' => $items->all(),
            'gantt_items' => $ganttItems->all(),
            'history' => $this->pmoBuildHistory($project, $items, $plan),
            'employees_active' => $this->pmoActiveEmployees(),
            'sub_stages' => $this->pmoSubStages(),
            'dependency_edges' => $dependencyPayload['edges'],
            'dependency_tree' => $dependencyPayload['tree'],
            'dependency_summary' => $dependencyPayload['summary'],
            'dependency_sequence' => $dependencyPayload['sequence'],
            'group_materials' => $this->pmoLoadPlanGroupMaterials($plan, $from, $to),
            'debug_counts' => $this->pmoDebugCounts($project),
        ];
    }

    private function pmoProjectPayload(LeadProductList $project): array
    {
        $customer = $project->customer;
        $alternative = $project->alternative;
        $product = $project->product ?: $project->articleGroup;
        $service = $project->relationLoaded('service') ? $project->getRelation('service') : null;
        $stage = $project->companyStage;
        $subStage = $project->leadStageSubStage;

        $customerName = trim((string) (($customer->firma ?? '') ?: (($customer->name ?? '') . ' ' . ($customer->lastname ?? ''))));
        if ($customerName === '') {
            $customerName = 'Kunde #' . ($project->customer_id ?? '—');
        }

        $objectAddress = $alternative->full_address
            ?? trim(implode(' ', array_filter([
                $alternative->street ?? null,
                $alternative->postcode ?? null,
                $alternative->city ?? null,
            ])));

        return [
            'id' => (int) $project->id,
            'customer_id' => (int) $project->customer_id,
            'customer_no' => $customer->customer_no ?? null,
            'customer_name' => $customerName,
            'customer_display_name' => $customer->display_name ?? $customerName,
            'customer_phone' => $customer->phone ?? $customer->telephone ?? null,
            'customer_email' => $customer->email ?? null,
            'product_id' => $project->product_id,
            'product_name' => $product->article_group ?? $product->name ?? ('Produkt #' . $project->product_id),
            'product_initial' => $product ? $this->articleGroupInitial($product) : 'AG',
            'product_image_url' => $product ? $this->articleGroupImageUrl($product) : null,
            'service_id' => $project->service_id,
            'service_name' => $service->phase_section ?? null,
            'alternative_id' => $project->alternative_id,
            'object_name' => $alternative->object_name ?? ('Objekt #' . ($project->alternative_id ?: '—')),
            'object_full_address' => $objectAddress,
            'object_address' => $objectAddress,
            'status' => $project->status,
            'stage_name' => $stage->name ?? $project->status,
            'stage_color' => $stage->color ?? null,
            'sub_stage_id' => $project->lead_stage_sub_stage_id,
            'sub_stage_name' => $subStage->name ?? null,
            'project_minutes' => $project->project_minutes,
            'price' => $project->price,
            'employee_id' => $project->employee_id,
            'field_employee' => $project->field_employee,
            'team_ids' => $this->pmoTeamIds($project->teams ?? []),
        ];
    }

    private function pmoLoadKanbanTasks(LeadProductList $project, Carbon $from, Carbon $to, bool $includeDone = false)
    {
        if (!Schema::hasTable('kanban_lead_tasks')) {
            return collect();
        }

        $q = DB::table('kanban_lead_tasks');
        $this->pmoApplyTableSafety($q, 'kanban_lead_tasks');
        $this->pmoApplyProjectScope($q, 'kanban_lead_tasks', $project);
        if (!$includeDone) {
            $this->pmoApplyOpenStatusScope($q, 'kanban_lead_tasks', ['status', 'task_status', 'work_status']);
        }
        $this->pmoApplyMontageStageScope($q, 'kanban_lead_tasks');
        $this->pmoApplyDateScope($q, 'kanban_lead_tasks', $from, $to);

        return $q->orderByDesc('id')->limit(500)->get()->map(function ($row) {
            $employeeIds = $this->pmoEmployeeIdsFromRow($row, [
                'performer_employee_id',
                'employee_id',
                'assigned_to',
                'responsible',
                'created_by',
            ]);

            if (Schema::hasTable('kanban_lead_task_employees')) {
                $fk = Schema::hasColumn('kanban_lead_task_employees', 'kanban_lead_task_id')
                    ? 'kanban_lead_task_id'
                    : (Schema::hasColumn('kanban_lead_task_employees', 'task_id') ? 'task_id' : null);

                if ($fk) {
                    $employeeIds = array_merge($employeeIds, DB::table('kanban_lead_task_employees')
                        ->where($fk, $row->id)
                        ->pluck('employee_id')
                        ->map(fn($id) => (int) $id)
                        ->all());
                }
            }

            return $this->pmoItemPayload($row, 'kanban_task', $employeeIds);
        });
    }

    private function pmoLoadPersonalTasks(LeadProductList $project, Carbon $from, Carbon $to, bool $includeDone = false)
    {
        if (!Schema::hasTable('personal_tasks')) {
            return collect();
        }

        $q = DB::table('personal_tasks');
        $this->pmoApplyTableSafety($q, 'personal_tasks');
        $this->pmoApplyProjectScope($q, 'personal_tasks', $project);
        if (!$includeDone) {
            $this->pmoApplyOpenStatusScope($q, 'personal_tasks', ['task_status', 'status', 'board_column']);
        }
        $this->pmoApplyMontageStageScope($q, 'personal_tasks');
        $this->pmoApplyDateScope($q, 'personal_tasks', $from, $to);

        return $q->orderByDesc('id')->limit(500)->get()->map(function ($row) {
            $employeeIds = $this->pmoEmployeeIdsFromRow($row, [
                'employee_id',
                'assigned_by',
                'assigned_to',
                'performer_employee_id',
                'created_by',
            ]);

            if (Schema::hasTable('employees_personal_tasks')) {
                $employeeIds = array_merge($employeeIds, DB::table('employees_personal_tasks')
                    ->where('task_id', $row->id)
                    ->pluck('employee_id')
                    ->map(fn($id) => (int) $id)
                    ->all());
            }

            return $this->pmoItemPayload($row, 'personal_task', $employeeIds);
        });
    }

    private function pmoLoadAppointments(LeadProductList $project, Carbon $from, Carbon $to, bool $includeDone = false)
    {
        if (!Schema::hasTable('main_appointments')) {
            return collect();
        }

        $q = DB::table('main_appointments');
        $this->pmoApplyTableSafety($q, 'main_appointments');
        $this->pmoApplyProjectScope($q, 'main_appointments', $project);
        if (!$includeDone) {
            $this->pmoApplyOpenStatusScope($q, 'main_appointments', ['status', 'appointment_status']);
        }
        $this->pmoApplyMontageStageScope($q, 'main_appointments');
        $this->pmoApplyDateScope($q, 'main_appointments', $from, $to);

        return $q->orderByDesc('id')->limit(500)->get()->map(function ($row) {
            $employeeIds = $this->pmoEmployeeIdsFromRow($row, [
                'employee_id',
                'assigned_to',
                'responsible',
                'appointment_by',
                'created_by',
                'staff_profile_ids',
            ]);

            if (Schema::hasTable('main_appointment_employees')) {
                $fk = Schema::hasColumn('main_appointment_employees', 'appointment_id')
                    ? 'appointment_id'
                    : (Schema::hasColumn('main_appointment_employees', 'main_appointment_id') ? 'main_appointment_id' : null);

                if ($fk) {
                    $employeeIds = array_merge($employeeIds, DB::table('main_appointment_employees')
                        ->where($fk, $row->id)
                        ->pluck('employee_id')
                        ->map(fn($id) => (int) $id)
                        ->all());
                }
            }

            return $this->pmoItemPayload($row, 'appointment', $employeeIds);
        });
    }

    private function pmoLoadTickets(LeadProductList $project, Carbon $from, Carbon $to, bool $includeDone = false)
    {
        if (!Schema::hasTable('problems')) {
            return collect();
        }

        $q = DB::table('problems');
        $this->pmoApplyTableSafety($q, 'problems');
        $this->pmoApplyProjectScope($q, 'problems', $project);
        if (!$includeDone) {
            $this->pmoApplyOpenStatusScope($q, 'problems', ['status', 'problem_status', 'ticket_status']);
        }
        $this->pmoApplyMontageStageScope($q, 'problems');
        $this->pmoApplyDateScope($q, 'problems', $from, $to);

        return $q->orderByDesc('id')->limit(500)->get()->map(function ($row) {
            $employeeIds = $this->pmoEmployeeIdsFromRow($row, [
                'responsible',
                'employee_id',
                'assigned_to',
                'created_by',
            ]);

            if (Schema::hasTable('employee_problem')) {
                $fk = Schema::hasColumn('employee_problem', 'problem_id')
                    ? 'problem_id'
                    : (Schema::hasColumn('employee_problem', 'ticket_id') ? 'ticket_id' : null);

                if ($fk) {
                    $employeeIds = array_merge($employeeIds, DB::table('employee_problem')
                        ->where($fk, $row->id)
                        ->pluck('employee_id')
                        ->map(fn($id) => (int) $id)
                        ->all());
                }
            }

            return $this->pmoItemPayload($row, 'ticket', $employeeIds);
        });
    }

    private function pmoLoadPhaseTemplateTasks(LeadProductList $project, Carbon $from, Carbon $to, bool $includeDone = false)
    {
        if (!Schema::hasTable('task_phases')) {
            return collect();
        }

        $stageId = $this->pmoResolveProjectLeadStageId($project);
        $subStageId = !empty($project->lead_stage_sub_stage_id) ? (int) $project->lead_stage_sub_stage_id : null;
        $phaseRows = $this->pmoLoadMatchingTaskPhaseRows($project, $stageId, $subStageId);

        if ($phaseRows->isEmpty()) {
            return collect();
        }

        $phaseIds = $phaseRows->pluck('id')->map(fn($id) => (int) $id)->filter()->unique()->values()->all();
        $activityRows = $this->pmoLoadMatchingPhaseActivityRows($project, $phaseIds, $stageId, $subStageId);
        $historyByActivity = $this->pmoCustomerActivityHistoryMap($project);
        $activitiesByPhase = $activityRows->groupBy(fn($row) => (int) ($row->phase_id ?? 0));
        $out = collect();

        foreach ($phaseRows as $phase) {
            $phaseId = (int) $phase->id;
            $phaseActivities = $activitiesByPhase->get($phaseId, collect());

            if ($phaseActivities->isEmpty()) {
                $employeeIds = $this->pmoEmployeeIdsForTemplatePhase($project, $phaseId);
                $row = (object) array_merge((array) $phase, [
                    'title' => $phase->phase_name ?? ('Phase #' . $phaseId),
                    'description' => $phase->description ?? null,
                    'planned_start_at' => null,
                    'planned_end_at' => null,
                    'duration_minutes' => max(1, (int) ($phase->count ?? 60)),
                    'sub_stage_name' => null,
                ]);

                $payload = $this->pmoItemPayload($row, 'task_phase', $employeeIds);
                $payload['phase_id'] = $phaseId;
                $payload['phase_name'] = $phase->phase_name ?? null;
                $payload['template_only'] = true;

                if ($includeDone || !$this->pmoIsDoneStatus($payload['status'] ?? null)) {
                    $out->push($payload);
                }

                continue;
            }

            foreach ($phaseActivities as $activity) {
                $activityId = (int) $activity->id;
                $history = $historyByActivity->get($activityId);
                $isDone = $this->pmoActivityHistoryIsDone($history);
                $employeeIds = $this->pmoEmployeeIdsForTemplatePhase($project, $phaseId, $activity, $history);

                $row = clone $activity;
                $row->title = trim((string) ($row->title ?? '')) !== ''
                    ? $row->title
                    : ($activity->template_phase_name ?? ('Aktivität #' . $activityId));
                $row->description = $row->description ?? $row->notes ?? $activity->template_phase_description ?? null;
                $row->status = $isDone ? 'done' : ($row->status ?? 'open');
                $row->is_done = $isDone ? 1 : 0;
                $row->done_at = $isDone ? $this->pmoSourceDoneAt($history) : null;
                $row->done_by_employee_id = $isDone ? $this->pmoSourceDoneByEmployeeId($history) : null;
                $row->duration_minutes = max(1, (int) ($row->duration ?? 60));
                $row->sub_stage_name = null;

                $payload = $this->pmoItemPayload($row, 'phase_activity', $employeeIds);
                $payload['phase_id'] = $phaseId;
                $payload['phase_name'] = $activity->template_phase_name ?? null;
                $payload['template_only'] = true;

                if ($includeDone || !$this->pmoIsDoneStatus($payload['status'] ?? null)) {
                    $out->push($payload);
                }
            }
        }

        return $out->values();
    }

    private function pmoApplyTableSafety($q, string $table): void
    {
        if ($this->safeColumn($table, 'deleted_at')) {
            $q->whereNull('deleted_at');
        }
    }

    private function pmoApplyProjectScope($q, string $table, LeadProductList $project): void
    {
        $q->where(function ($scope) use ($table, $project) {
            $hasAny = false;

            if ($this->safeColumn($table, 'lead_product_list_id')) {
                $scope->where('lead_product_list_id', (int) $project->id);
                $hasAny = true;
            }

            if ($this->safeColumn($table, 'project_id')) {
                $method = $hasAny ? 'orWhere' : 'where';
                $scope->{$method}('project_id', (int) $project->id);
                $hasAny = true;
            }

            if ($this->safeColumn($table, 'customer_id')) {
                $method = $hasAny ? 'orWhere' : 'where';
                $scope->{$method}(function ($fallback) use ($table, $project) {
                    $fallback->where('customer_id', (int) $project->customer_id);

                    if ($project->alternative_id && $this->safeColumn($table, 'alternative_id')) {
                        $fallback->where(function ($alt) use ($project) {
                            $alt->whereNull('alternative_id')
                                ->orWhere('alternative_id', 0)
                                ->orWhere('alternative_id', (int) $project->alternative_id);
                        });
                    }

                    if ($project->product_id && $this->safeColumn($table, 'product_id')) {
                        $fallback->where(function ($prod) use ($project) {
                            $prod->whereNull('product_id')
                                ->orWhere('product_id', 0)
                                ->orWhere('product_id', (int) $project->product_id);
                        });
                    }
                });
                $hasAny = true;
            }

            if (!$hasAny) {
                $scope->whereRaw('1 = 0');
            }
        });
    }

    private function pmoApplyMontageStageScope($q, string $table): void
    {
        $stage = $this->pmoMontageStage();
        $stageId = $stage?->id;

        if (!$stageId || !$this->safeColumn($table, 'lead_stage_id')) {
            return;
        }

        $q->where(function ($stageQuery) use ($stageId) {
            $stageQuery->whereNull('lead_stage_id')
                ->orWhere('lead_stage_id', 0)
                ->orWhere('lead_stage_id', (int) $stageId);
        });
    }

    private function pmoApplyDateScope($q, string $table, Carbon $from, Carbon $to): void
    {
        $map = [
            'kanban_lead_tasks' => ['planned_start_at', 'planned_end_at', 'due_date', 'start_date', 'task_date', 'date'],
            'personal_tasks' => ['planned_start_at', 'planned_end_at', 'due_date', 'start_date', 'task_date', 'date'],
            'main_appointments' => ['planned_start_at', 'planned_end_at', 'start_date', 'end_date', 'event_date', 'date'],
            'problems' => ['planned_start_at', 'planned_end_at', 'due_date', 'start_date', 'ticket_date', 'date'],
        ];

        $columns = array_values(array_filter($map[$table] ?? [], fn($column) => $this->safeColumn($table, $column)));

        if (empty($columns)) {
            return;
        }

        $fromDate = $from->copy()->startOfDay()->toDateString();
        $toDate = $to->copy()->endOfDay()->toDateString();
        $fromDateTime = $from->copy()->startOfDay()->toDateTimeString();
        $toDateTime = $to->copy()->endOfDay()->toDateTimeString();

        $q->where(function ($dateQuery) use ($columns, $fromDate, $toDate, $fromDateTime, $toDateTime) {
            $used = false;

            if (in_array('planned_start_at', $columns, true)) {
                $dateQuery->where(function ($overlap) use ($columns, $fromDateTime, $toDateTime) {
                    $overlap->where('planned_start_at', '<=', $toDateTime);

                    if (in_array('planned_end_at', $columns, true)) {
                        $overlap->where(function ($end) use ($fromDateTime) {
                            $end->whereNull('planned_end_at')
                                ->orWhere('planned_end_at', '>=', $fromDateTime);
                        });
                    } else {
                        $overlap->where('planned_start_at', '>=', $fromDateTime);
                    }
                });
                $used = true;
            }

            foreach ($columns as $column) {
                if (in_array($column, ['planned_start_at', 'planned_end_at'], true)) {
                    continue;
                }

                $method = $used ? 'orWhere' : 'where';
                $safeColumn = str_replace('`', '', $column);

                $dateQuery->{$method}(function ($inner) use ($safeColumn, $fromDate, $toDate) {
                    $inner->whereNotNull($safeColumn)
                        ->whereBetween(DB::raw('DATE(`' . $safeColumn . '`)'), [$fromDate, $toDate]);
                });

                $used = true;
            }
        });
    }

    private function pmoApplyOpenStatusScope($q, string $table, array $columns): void
    {
        $existing = array_values(array_filter($columns, fn($column) => $this->safeColumn($table, $column)));

        if (empty($existing)) {
            return;
        }

        $closed = ['done', 'completed', 'complete', 'finished', 'closed', 'end', 'ended', 'cancel', 'canceled', 'cancelled', 'archive', 'archived', 'junk', 'deleted'];

        $q->where(function ($statusQuery) use ($existing, $closed) {
            foreach ($existing as $column) {
                $safeColumn = str_replace('`', '', $column);

                $statusQuery->where(function ($inner) use ($safeColumn, $closed) {
                    $inner->whereNull($safeColumn)
                        ->orWhere($safeColumn, '')
                        ->orWhereRaw('LOWER(COALESCE(`' . $safeColumn . '`, "")) NOT IN (' . collect($closed)->map(fn() => '?')->implode(',') . ')', $closed);
                });
            }
        });
    }

    private function pmoEmployeeIdsFromRow($row, array $columns = []): array
    {
        $ids = [];

        foreach ($columns as $column) {
            if (!isset($row->{$column}) || $row->{$column} === null || $row->{$column} === '') {
                continue;
            }

            if (is_numeric($row->{$column})) {
                $ids[] = (int) $row->{$column};
                continue;
            }

            $ids = array_merge($ids, $this->pmoTeamIds($row->{$column}));
        }

        foreach (['employee_ids', 'team_ids', 'teams', 'staff_profile_ids'] as $jsonColumn) {
            if (isset($row->{$jsonColumn}) && $row->{$jsonColumn} !== null && $row->{$jsonColumn} !== '') {
                $ids = array_merge($ids, $this->pmoTeamIds($row->{$jsonColumn}));
            }
        }

        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }

    private function pmoItemPayload($row, string $type, array $employeeIds = []): array
    {
        $title = $row->title
            ?? $row->task_title
            ?? $row->name
            ?? $row->subject
            ?? $row->ticket_no
            ?? $row->problem
            ?? ('Eintrag #' . ($row->id ?? ''));

        $description = $row->description
            ?? $row->note
            ?? $row->notes
            ?? $row->problem
            ?? null;

        $date = $row->planned_start_at
            ?? $row->due_date
            ?? $row->event_date
            ?? $row->start_date
            ?? $row->date
            ?? $row->created_at
            ?? null;

        $startAt = $row->planned_start_at ?? null;
        $endAt = $row->planned_end_at ?? null;

        if (!$startAt && !empty($date)) {
            $time = $row->event_time ?? $row->start_time ?? $row->due_time ?? null;
            if ($time) {
                $startAt = Carbon::parse(Carbon::parse($date)->toDateString() . ' ' . $this->normalizeTime((string) $time))->toDateTimeString();
            }
        }

        if (!$endAt && !empty($date)) {
            $time = $row->event_end_time ?? $row->end_time ?? null;
            if ($time) {
                $endAt = Carbon::parse(Carbon::parse($date)->toDateString() . ' ' . $this->normalizeTime((string) $time, '09:00:00'))->toDateTimeString();
            }
        }

        $employeeIds = array_values(array_unique(array_filter(array_map('intval', $employeeIds))));

        $scheduleDateKey = null;
        $scheduleDateLabel = null;
        $scheduleTimeLabel = null;
        $scheduleRangeLabel = null;

        try {
            $dateSource = $startAt ?: $date;
            if ($dateSource) {
                $scheduleDate = Carbon::parse($dateSource);
                $scheduleDateKey = $scheduleDate->toDateString();
                $scheduleDateLabel = $scheduleDate->format('d.m.Y');
            }

            if ($startAt) {
                $scheduleTimeLabel = Carbon::parse($startAt)->format('H:i');
            } elseif (!empty($row->event_time ?? $row->start_time ?? $row->due_time ?? null)) {
                $scheduleTimeLabel = substr($this->normalizeTime((string) ($row->event_time ?? $row->start_time ?? $row->due_time)), 0, 5);
            }

            if ($scheduleTimeLabel) {
                $endLabel = $endAt ? Carbon::parse($endAt)->format('H:i') : null;
                $scheduleRangeLabel = $endLabel ? ($scheduleTimeLabel . ' - ' . $endLabel) : $scheduleTimeLabel;
            }
        } catch (\Throwable $e) {
            // Keep schedule labels nullable if a legacy date value cannot be parsed.
        }

        $statusValue = $this->pmoReadSourceStatusValue($row, $type);
        $doneAt = $this->pmoSourceDoneAt($row);
        $doneById = $this->pmoSourceDoneByEmployeeId($row);
        $doneByEmployee = $this->pmoEmployeeMini($doneById);

        return [
            'id' => (int) $row->id,
            'source_id' => (int) $row->id,
            'source_type' => $type,
            'title' => $title,
            'description' => $description,
            'status' => $statusValue,
            'status_label' => $this->pmoStatusLabel($statusValue),
            'is_done' => $this->pmoIsDoneStatus($statusValue),
            'done_at' => $doneAt,
            'done_by_employee_id' => $doneById,
            'done_by_employee' => $doneByEmployee,
            'done_by_name' => $doneByEmployee['full_name'] ?? null,
            'status_history' => [],
            'date' => $date,
            'planned_date' => $date ? Carbon::parse($date)->toDateString() : null,
            'planned_start_at' => $startAt,
            'planned_end_at' => $endAt,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'start_time' => $row->event_time ?? $row->start_time ?? $row->due_time ?? null,
            'end_time' => $row->event_end_time ?? $row->end_time ?? null,
            'schedule_date_key' => $scheduleDateKey,
            'schedule_date_label' => $scheduleDateLabel,
            'schedule_time_label' => $scheduleTimeLabel,
            'schedule_range_label' => $scheduleRangeLabel,
            'employee_ids' => $employeeIds,
            'sub_stage_name' => $row->sub_stage_name ?? null,
        ];
    }

    private function pmoDebugCounts(LeadProductList $project): array
    {
        $out = [];

        foreach ([
            'kanban_lead_tasks' => 'lead_product_list_id',
            'personal_tasks' => 'lead_product_list_id',
            'main_appointments' => 'lead_product_list_id',
            'problems' => 'lead_product_list_id',
            'task_phases' => 'product_id',
            'phase_activities' => 'product_id',
        ] as $table => $projectColumn) {
            if (!Schema::hasTable($table)) {
                $out[$table] = ['table_exists' => false, 'project' => 0, 'customer' => 0];
                continue;
            }

            $projectCount = 0;
            if ($this->safeColumn($table, $projectColumn)) {
                $projectCount = (int) DB::table($table)->where($projectColumn, $project->id)->count();
            }

            $customerCount = 0;
            if ($this->safeColumn($table, 'customer_id')) {
                $customerCount = (int) DB::table($table)->where('customer_id', $project->customer_id)->count();
            }

            $out[$table] = [
                'table_exists' => true,
                'project' => $projectCount,
                'customer' => $customerCount,
            ];
        }

        return $out;
    }

    private function pmoFallbackEmployeeIds(LeadProductList $project): array
    {
        $ids = [];

        if (!empty($project->employee_id)) {
            $ids[] = (int) $project->employee_id;
        }

        if (!empty($project->field_employee)) {
            $ids[] = (int) $project->field_employee;
        }

        $ids = array_merge($ids, $this->pmoTeamIds($project->teams ?? []));

        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }

    private function pmoBuildEmployees(LeadProductList $project, \Illuminate\Support\Collection $items): array
    {
        $roleMap = [];

        if (!empty($project->employee_id)) {
            $roleMap[(int) $project->employee_id][] = 'Projektleiter';
        }

        if (!empty($project->field_employee)) {
            $roleMap[(int) $project->field_employee][] = 'Außendienst';
        }

        foreach ($this->pmoTeamIds($project->teams ?? []) as $id) {
            $roleMap[(int) $id][] = 'Team';
        }

        foreach ($items as $item) {
            foreach (($item['employee_ids'] ?? []) as $id) {
                $roleMap[(int) $id][] = 'Arbeit';
            }
        }

        $ids = array_keys($roleMap);
        if (empty($ids)) {
            return [];
        }

        return Employee::query()
            ->whereIn('id', $ids)
            ->get()
            ->map(function ($employee) use ($roleMap) {
                $roles = array_values(array_unique($roleMap[(int) $employee->id] ?? ['Team']));

                return [
                    'id' => (int) $employee->id,
                    'name' => $employee->name,
                    'lastname' => $employee->lastname,
                    'full_name' => trim(($employee->title ? $employee->title . ' ' : '') . ($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) ?: ('Mitarbeiter #' . $employee->id),
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'photo_url' => $this->resolvePhotoUrl($employee),
                    'initials' => $this->employeeInitials($employee),
                    'roles' => $roles,
                    'roles_clean' => $roles,
                ];
            })
            ->values()
            ->all();
    }

    private function pmoActiveEmployees(): array
    {
        $query = Employee::query();

        if ($this->safeColumn('employees', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        if ($this->safeColumn('employees', 'status')) {
            $query->where(function ($q) {
                $q->whereNull('status')
                    ->orWhere('status', '')
                    ->orWhereRaw("LOWER(status) IN ('active','aktiv','published','publiziert','1','yes','ja')");
            });
        }

        return $query->orderBy('lastname')
            ->orderBy('name')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => (int) $employee->id,
                    'name' => $employee->name,
                    'lastname' => $employee->lastname,
                    'full_name' => trim(($employee->title ? $employee->title . ' ' : '') . ($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) ?: ('Mitarbeiter #' . $employee->id),
                    'photo_url' => $this->resolvePhotoUrl($employee),
                    'initials' => $this->employeeInitials($employee),
                ];
            })
            ->values()
            ->all();
    }

    private function pmoBuildPlannerGanttItems(PlannerPlan $plan): \Illuminate\Support\Collection
    {
        if (!Schema::hasTable('planner_items')) {
            return collect();
        }

        $q = DB::table('planner_items')->where('plan_id', (int) $plan->id);

        if ($this->safeColumn('planner_items', 'deleted_at')) {
            $q->whereNull('deleted_at');
        }

        $items = $q->orderByRaw('COALESCE(planned_start_at, created_at) ASC')->orderBy('id')->get();

        $employeeMap = [];
        if (Schema::hasTable('planner_item_employees')) {
            DB::table('planner_item_employees')
                ->whereIn('planner_item_id', $items->pluck('id')->map(fn($id) => (int) $id)->values()->all() ?: [0])
                ->get()
                ->each(function ($row) use (&$employeeMap) {
                    $employeeMap[(int) $row->planner_item_id][] = (int) $row->employee_id;
                });
        }

        return $items->map(function ($item) use ($employeeMap) {
            $status = $this->pmoNormalizePlannerStatus((string) ($item->status ?? 'open'));
            $start = $item->planned_start_at ?? $item->start_at ?? $item->created_at ?? null;
            $end = $item->planned_end_at ?? $item->end_at ?? $this->pmoPlannerDoneAt($item) ?? $item->updated_at ?? null;

            if (!$end && $start) {
                try {
                    $end = Carbon::parse($start)->addMinutes((int) ($item->duration_minutes ?? 60))->toDateTimeString();
                } catch (\Throwable $e) {
                    $end = $start;
                }
            }

            $doneById = $this->pmoPlannerDoneByEmployeeId($item);
            $doneByEmployee = $this->pmoEmployeeMini($doneById);

            return [
                'id' => (int) $item->source_id ?: (int) $item->id,
                'planner_item_id' => (int) $item->id,
                'source_id' => (int) ($item->source_id ?? 0),
                'source_type' => $item->source_type ?: 'planner_item',
                'title' => $item->title ?: ('Arbeit #' . $item->id),
                'description' => $item->description,
                'status' => $status,
                'status_label' => $this->pmoStatusLabel($status),
                'is_done' => $this->pmoIsDoneStatus($status),
                'duration_minutes' => (int) ($item->duration_minutes ?? 60),
                'planned_start_at' => $item->planned_start_at ?? null,
                'planned_end_at' => $item->planned_end_at ?? null,
                'start_at' => $start,
                'end_at' => $end,
                'timeline_start_at' => $start,
                'timeline_end_at' => $end,
                'date' => $start,
                'planned_date' => $start ? Carbon::parse($start)->toDateString() : null,
                'employee_ids' => array_values(array_unique(array_filter(array_map('intval', $employeeMap[(int) $item->id] ?? [])))),
                'done_at' => $this->pmoPlannerDoneAt($item),
                'done_by_employee_id' => $doneById,
                'done_by_employee' => $doneByEmployee,
                'done_by_name' => $doneByEmployee['full_name'] ?? null,
                'status_history' => $this->pmoLoadPlannerStatusHistory((int) $item->id, 8),
                'created_at' => $item->created_at ?? null,
                'updated_at' => $item->updated_at ?? null,
            ];
        })->values();
    }

    private function pmoBuildHistory(LeadProductList $project, \Illuminate\Support\Collection $items, ?PlannerPlan $plan = null): array
    {
        $history = [];

        if ($plan && Schema::hasTable('planner_item_status_histories')) {
            $employeeColumns = ['id'];
            foreach (['name', 'lastname', 'title'] as $column) {
                if ($this->safeColumn('employees', $column)) {
                    $employeeColumns[] = $column;
                }
            }

            $statusRows = DB::table('planner_item_status_histories as h')
                ->leftJoin('planner_items as pi', 'pi.id', '=', 'h.planner_item_id')
                ->where('h.planner_plan_id', (int) $plan->id)
                ->orderByDesc('h.changed_at')
                ->orderByDesc('h.id')
                ->limit(80)
                ->get(['h.*', 'pi.title as item_title']);

            $employeeIds = $statusRows->pluck('changed_by_employee_id')->filter()->map(fn($id) => (int) $id)->unique()->values();
            $employees = ($employeeIds->isNotEmpty() && Schema::hasTable('employees'))
                ? DB::table('employees')->whereIn('id', $employeeIds)->get($employeeColumns)->keyBy('id')
                : collect();

            foreach ($statusRows as $row) {
                $employee = $row->changed_by_employee_id ? $employees->get((int) $row->changed_by_employee_id) : null;
                $employeeName = $employee ? trim((($employee->title ?? '') ? ($employee->title . ' ') : '') . (($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))) : null;

                $history[] = [
                    'title' => ($row->item_title ?: 'Arbeit') . ' · Status geändert',
                    'meta' => ($row->changed_at ? Carbon::parse($row->changed_at)->format('d.m.Y H:i') : '—') . ($employeeName ? (' · ' . $employeeName) : ''),
                    'reason' => $this->pmoStatusLabel($row->old_status) . ' → ' . $this->pmoStatusLabel($row->new_status) . ($row->note ? (' · ' . $row->note) : ''),
                ];
            }
        }

        foreach ($this->pmoDecode($project->stage_history ?? []) as $entry) {
            if (is_array($entry)) {
                $history[] = [
                    'title' => $entry['title'] ?? $entry['event'] ?? $entry['stage'] ?? 'Stage Änderung',
                    'meta' => $entry['date'] ?? $entry['created_at'] ?? $entry['time'] ?? null,
                    'reason' => $entry['reason'] ?? $entry['note'] ?? $entry['description'] ?? null,
                ];
            }
        }

        foreach ($items->take(20) as $item) {
            $history[] = [
                'title' => $item['title'] ?? 'Arbeit',
                'meta' => ($item['planned_date'] ?? $item['date'] ?? '—') . ' · ' . ($item['source_type'] ?? 'work'),
                'reason' => 'Status: ' . ($item['status'] ?? 'open'),
            ];
        }

        return array_values($history);
    }

    private function pmoSubStages(): array
    {
        $stage = $this->pmoMontageStage();

        if (!$stage) {
            return [];
        }

        return $stage->activeSubStages()
            ->get(['id', 'name', 'key', 'color', 'icon', 'sort_order'])
            ->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'key' => $s->key,
                'color' => $s->color,
                'icon' => $s->icon,
            ])
            ->values()
            ->all();
    }

    private function pmoMontageStage(): ?LeadStage
    {
        return LeadStage::query()
            ->with('defaultSubStage')
            ->where(function ($q) {
                $q->whereIn('key', ['project', 'montage'])
                    ->orWhereRaw("LOWER(name) LIKE '%montage%'")
                    ->orWhereRaw("LOWER(name) LIKE '%projekt%'");
            })
            ->orderByRaw("CASE WHEN `key` = 'project' THEN 0 WHEN `key` = 'montage' THEN 1 ELSE 2 END")
            ->first();
    }

    private function pmoPeriodRange(string $date, string $mode): array
    {
        $base = Carbon::parse($date);

        if ($mode === 'week') {
            $from = $base->copy()->startOfWeek();
            $to = $base->copy()->endOfWeek();
            return [$from, $to, $from->format('d.m.Y') . ' - ' . $to->format('d.m.Y')];
        }

        if ($mode === 'month') {
            $from = $base->copy()->startOfMonth();
            $to = $base->copy()->endOfMonth();
            return [$from, $to, $base->format('F Y')];
        }

        return [$base->copy()->startOfDay(), $base->copy()->endOfDay(), $base->format('d.m.Y')];
    }

    private function pmoTeamIds($raw): array
    {
        $decoded = $this->pmoDecode($raw);

        if (isset($decoded['ids']) && is_array($decoded['ids'])) {
            $decoded = $decoded['ids'];
        } elseif (isset($decoded['team']) && is_array($decoded['team'])) {
            $decoded = $decoded['team'];
        } elseif (isset($decoded['employees']) && is_array($decoded['employees'])) {
            $decoded = $decoded['employees'];
        }

        $ids = [];

        foreach ($decoded as $value) {
            if (is_array($value)) {
                $value = $value['id'] ?? $value['employee_id'] ?? null;
            }

            if (is_numeric($value) && (int) $value > 0) {
                $ids[] = (int) $value;
            }
        }

        return array_values(array_unique($ids));
    }

    private function pmoDecode($raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_array($raw)) {
            return $raw;
        }

        $decoded = json_decode((string) $raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function pmoAddEmployeesToProjectTeam(int $projectId, array $employeeIds): void
    {
        $project = LeadProductList::query()->find($projectId);

        if (!$project) {
            return;
        }

        $ids = array_merge($this->pmoTeamIds($project->teams ?? []), array_map('intval', $employeeIds));
        $ids = array_values(array_unique(array_filter($ids)));

        $project->teams = $ids;
        $project->save();
    }

    private function pmoInsertGetId(string $table, array $data): int
    {
        $filtered = [];

        foreach ($data as $column => $value) {
            if ($this->safeColumn($table, $column)) {
                $filtered[$column] = $value;
            }
        }

        if ($this->safeColumn($table, 'created_at') && !array_key_exists('created_at', $filtered)) {
            $filtered['created_at'] = now();
        }

        if ($this->safeColumn($table, 'updated_at') && !array_key_exists('updated_at', $filtered)) {
            $filtered['updated_at'] = now();
        }

        return (int) DB::table($table)->insertGetId($filtered);
    }


    public function projectCandidates(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('lead_product_lists as lpl')
            ->whereNull('lpl.deleted_at')
            ->leftJoin('new_leads as c', 'c.id', '=', 'lpl.customer_id')
            ->leftJoin('lead_alternative_adds as a', 'a.id', '=', 'lpl.alternative_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
            ->leftJoin('lead_stages as ls', 'ls.key', '=', 'lpl.status')
            ->select([
                'lpl.id',
                'lpl.customer_id',
                'lpl.alternative_id',
                'lpl.product_id',
                'lpl.status',
                'c.customer_no',
                'c.firma',
                'c.name as customer_name',
                'c.lastname as customer_lastname',
                'a.object_name',
                'a.full_address as object_full_address',
                'a.street as object_street',
                'a.postcode as object_postcode',
                'a.city as object_city',
                'ag.article_group as product_name',
                'ls.name as stage_name',
            ])
            ->whereNotIn(DB::raw('LOWER(COALESCE(lpl.status, ""))'), [
                'completed',
                'complete',
                'archive',
                'archiv',
                'junk',
                'deleted',
            ]);

        if ($q !== '') {
            $like = '%' . mb_strtolower($q) . '%';

            $query->where(function ($s) use ($like) {
                $s->whereRaw('LOWER(COALESCE(c.customer_no, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(c.firma, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(c.name, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(c.lastname, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(a.object_name, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(a.full_address, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(a.street, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(a.postcode, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(a.city, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(ag.article_group, "")) LIKE ?', [$like])
                    ->orWhere('lpl.id', is_numeric($like) ? (int) $like : 0);
            });
        }

        $rows = $query
            ->orderByRaw("CASE WHEN LOWER(COALESCE(lpl.status, '')) IN ('project', 'projekt', 'montage') THEN 0 ELSE 1 END")
            ->orderByDesc('lpl.updated_at')
            ->orderByDesc('lpl.id')
            ->limit(30)
            ->get();

        $results = $rows->map(function ($row) {
            $customer = $this->ppCustomerName($row);

            $object = trim((string) ($row->object_name ?? ''));
            if ($object === '') {
                $object = trim(collect([
                    $row->object_street ?? null,
                    $row->object_postcode ?? null,
                    $row->object_city ?? null,
                ])->filter()->implode(' '));
            }

            if ($object === '') {
                $object = $row->object_full_address ?: 'Objekt';
            }

            $product = $row->product_name ?: 'Produkt';
            $status = strtolower((string) ($row->status ?? ''));
            $isMontage = in_array($status, ['project', 'projekt', 'montage'], true);

            return [
                'id' => (int) $row->id,
                'text' => $customer . ' · ' . $product . ' · #' . $row->id,
                'customer' => $customer,
                'object' => $object,
                'product' => $product,
                'status' => $row->status,
                'stage_name' => $row->stage_name ?: $row->status,
                'is_montage' => $isMontage,
            ];
        })->values();

        return response()->json([
            'results' => $results,
        ]);
    }


    public function storeProjectFromLeadProduct(Request $request)
    {
        $data = $request->validate([
            'lead_product_list_id' => ['required', 'integer', 'min:1'],
            'force_montage' => ['nullable', 'boolean'],
        ]);

        $project = LeadProductList::query()
            ->whereNull('deleted_at')
            ->findOrFail((int) $data['lead_product_list_id']);

        $forceMontage = (bool) ($data['force_montage'] ?? false);
        $currentStatus = strtolower((string) ($project->status ?? ''));
        $isMontage = in_array($currentStatus, ['project', 'projekt', 'montage'], true);

        if (!$isMontage && !$forceMontage) {
            return response()->json([
                'ok' => false,
                'requires_confirmation' => true,
                'message' => 'Dieser Kunde / dieses Produkt ist noch nicht in Montage. Trotzdem als Montage-Projekt übernehmen?',
            ], 409);
        }

        return DB::transaction(function () use ($project, $isMontage) {
            if (!$isMontage) {
                $oldStatus = $project->status;

                $project->old_stage = $oldStatus;
                $project->status = 'project';

                if (empty($project->lead_stage_sub_stage_id)) {
                    $stage = LeadStage::query()->where('key', 'project')->first();

                    if ($stage) {
                        $defaultSubStage = LeadStageSubStage::query()
                            ->where('lead_stage_id', $stage->id)
                            ->where('is_default', true)
                            ->where('is_active', true)
                            ->first();

                        if (!$defaultSubStage) {
                            $defaultSubStage = LeadStageSubStage::query()
                                ->where('lead_stage_id', $stage->id)
                                ->where('is_active', true)
                                ->orderBy('sort_order')
                                ->orderBy('id')
                                ->first();
                        }

                        if ($defaultSubStage) {
                            $project->lead_stage_sub_stage_id = $defaultSubStage->id;
                        }
                    }
                }

                $history = $this->ppStageHistory($project->stage_history);
                $history[] = [
                    'event' => 'Projekt in Montage übernommen',
                    'title' => 'Projekt in Montage übernommen',
                    'from' => $oldStatus,
                    'to' => 'project',
                    'reason' => 'Manuell über Projektplanung erstellt.',
                    'created_at' => now()->toDateTimeString(),
                    'employee_id' => $this->authEmployeeId() ?? auth()->id(),
                    'user_id' => auth()->id(),
                ];

                $project->stage_history = $history;
                $project->save();
            }

            $plan = PlannerPlan::firstOrCreate(
                [
                    'customer_id' => (int) $project->customer_id,
                    'project_id' => (int) $project->id,
                ],
                [
                    'account_id' => null,
                    'stage' => $project->status ?: 'project',
                    'title' => 'Projektplan #' . $project->id,
                    'status' => 'active',
                    'created_by' => auth()->id(),
                    'meta' => [
                        'product_id' => $project->product_id,
                        'alternative_id' => $project->alternative_id,
                        'lead_stage_sub_stage_id' => $project->lead_stage_sub_stage_id,
                    ],
                ]
            );

            $this->syncProjectScopedPlan($plan);

            return response()->json([
                'ok' => true,
                'message' => 'Projekt wurde erstellt / geladen.',
                'project_id' => $project->id,
                'plan_id' => $plan->id,
                'redirect_url' => route('planner.cockpit') . '?customer_id=' . $project->customer_id . '&project_id=' . $project->id . '&plan_id=' . $plan->id,
            ]);
        });
    }


    public function projectKanbanData(Request $request)
    {
        $query = $this->ppProjectBaseQuery();
        $this->ppApplyProjectFilters($query, $request, 'project');

        $rows = $query
            ->orderByDesc('lpl.updated_at')
            ->orderByDesc('lpl.id')
            ->limit(500)
            ->get();

        $counts = $this->projectCockpitCounts($rows);
        $employees = $this->ppEmployeesForProjectRows($rows);

        $projects = $rows->map(function ($row) use ($counts, $employees) {
            return $this->ppProjectPayload($row, $counts, $employees);
        });

        $stage = LeadStage::query()
            ->whereIn('key', ['project', 'montage'])
            ->orderByRaw("CASE WHEN `key` = 'project' THEN 0 WHEN `key` = 'montage' THEN 1 ELSE 2 END")
            ->first();

        $subStages = collect();

        if ($stage) {
            $subStages = LeadStageSubStage::query()
                ->where('lead_stage_id', $stage->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['id', 'name', 'color', 'key']);
        }

        if ($subStages->isEmpty()) {
            return response()->json([
                'ok' => true,
                'columns' => [
                    [
                        'id' => 0,
                        'name' => 'Montage',
                        'key' => 'montage',
                        'color' => '#93c21c',
                        'items' => $projects->values(),
                    ],
                ],
            ]);
        }

        $columns = $subStages->map(function ($subStage) use ($projects) {
            return [
                'id' => (int) $subStage->id,
                'name' => $subStage->name,
                'key' => $subStage->key,
                'color' => $subStage->color ?: '#93c21c',
                'items' => $projects
                    ->filter(fn($project) => (int) ($project['sub_stage']['id'] ?? 0) === (int) $subStage->id)
                    ->values(),
            ];
        })->values();

        $withoutSubStage = $projects
            ->filter(fn($project) => empty($project['sub_stage']['id']))
            ->values();

        if ($withoutSubStage->isNotEmpty()) {
            $columns->push([
                'id' => 0,
                'name' => 'Ohne Unterphase',
                'key' => 'without_sub_stage',
                'color' => '#6b7280',
                'items' => $withoutSubStage,
            ]);
        }

        return response()->json([
            'ok' => true,
            'columns' => $columns,
        ]);
    }


    public function moveProjectKanban(Request $request, LeadProductList $project)
    {
        $data = $request->validate([
            'sub_stage_id' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $subStage = LeadStageSubStage::query()
            ->with('stage')
            ->where('is_active', true)
            ->findOrFail((int) $data['sub_stage_id']);

        $stage = $subStage->stage;
        $newStatus = $stage?->key ?: 'project';

        return DB::transaction(function () use ($project, $subStage, $newStatus, $data) {
            $oldStatus = $project->status;
            $oldSubStageId = $project->lead_stage_sub_stage_id;

            $history = $this->ppStageHistory($project->stage_history);
            $history[] = [
                'event' => 'Projekt verschoben',
                'title' => 'Projekt verschoben',
                'from' => $oldStatus,
                'to' => $newStatus,
                'from_sub_stage_id' => $oldSubStageId,
                'to_sub_stage_id' => (int) $subStage->id,
                'to_sub_stage_name' => $subStage->name,
                'reason' => $data['reason'],
                'created_at' => now()->toDateTimeString(),
                'employee_id' => $this->authEmployeeId() ?? auth()->id(),
                'user_id' => auth()->id(),
            ];

            $project->old_stage = $oldStatus;
            $project->status = $newStatus;
            $project->lead_stage_sub_stage_id = (int) $subStage->id;
            $project->stage_history = $history;
            $project->save();

            PlannerPlan::query()
                ->where('project_id', $project->id)
                ->whereNull('deleted_at')
                ->update([
                    'stage' => $newStatus,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'ok' => true,
                'message' => 'Projekt wurde verschoben.',
                'project_id' => $project->id,
                'status' => $newStatus,
                'sub_stage_id' => $subStage->id,
            ]);
        });
    }


    private function ppLatestActivitiesForProjects(array $projectIds): array
    {
        $projectIds = array_values(array_unique(array_filter(array_map('intval', $projectIds))));

        if (empty($projectIds)) {
            return [];
        }

        $latest = [];

        foreach ($projectIds as $projectId) {
            $activity = collect($this->ppProjectActivityTimeline($projectId, 1))->first();

            if ($activity) {
                $latest[$projectId] = $activity;
            }
        }

        return $latest;
    }

    private function ppProjectActivityTimeline(int $projectId, int $limit = 80): array
    {
        $projectId = (int) $projectId;

        if ($projectId <= 0) {
            return [];
        }

        $planIds = PlannerPlan::query()
            ->where('project_id', $projectId)
            ->whereNull('deleted_at')
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter()
            ->values()
            ->all();

        $activities = [];

        if (!empty($planIds)) {
            $activities = array_merge(
                $activities,
                $this->ppProjectStatusHistoryActivities($projectId, $planIds, $limit),
                $this->ppProjectCommentActivities($projectId, $planIds, $limit),
                $this->ppProjectMaterialActivities($projectId, $planIds, $limit),
                $this->ppProjectGalleryActivities($projectId, $planIds, $limit),
                $this->ppProjectPlannerItemActivities($projectId, $planIds, $limit),
                $this->ppProjectAttendanceActivities($projectId, $planIds, $limit)
            );
        }

        return collect($activities)
            ->filter(fn($item) => !empty($item['created_at']))
            ->sortByDesc('created_at')
            ->take($limit)
            ->values()
            ->all();
    }

    private function ppProjectStatusHistoryActivities(int $projectId, array $planIds, int $limit): array
    {
        if (!Schema::hasTable('planner_item_status_histories') || empty($planIds)) {
            return [];
        }

        $q = DB::table('planner_item_status_histories as h')
            ->leftJoin('planner_items as pi', 'pi.id', '=', 'h.planner_item_id');

        if ($this->safeColumn('planner_item_status_histories', 'planner_plan_id')) {
            $q->whereIn('h.planner_plan_id', $planIds);
        } elseif ($this->safeColumn('planner_item_status_histories', 'project_id')) {
            $q->where('h.project_id', $projectId);
        } else {
            return [];
        }

        $rows = $q->orderByDesc($this->safeColumn('planner_item_status_histories', 'changed_at') ? 'h.changed_at' : 'h.created_at')
            ->orderByDesc('h.id')
            ->limit($limit)
            ->get([
                'h.*',
                'pi.title as planner_item_title',
                'pi.source_type as planner_item_source_type',
                'pi.source_id as planner_item_source_id',
            ]);

        $employees = $this->ppActivityEmployees($rows->pluck('changed_by_employee_id')->filter()->all());

        return $rows->map(function ($row) use ($employees) {
            $employeeId = $row->changed_by_employee_id ? (int) $row->changed_by_employee_id : null;
            $employeeName = $employeeId && isset($employees[$employeeId]) ? $employees[$employeeId] : null;
            $oldLabel = $this->pmoStatusLabel($row->old_status ?? 'open');
            $newLabel = $row->status_label ?: $this->pmoStatusLabel($row->new_status ?? 'open');

            return [
                'type' => 'planner_status',
                'icon' => $this->pmoIsDoneStatus($row->new_status ?? '') ? 'check-circle-2' : 'activity',
                'title' => trim(($row->planner_item_title ?: 'Arbeit') . ' · ' . $newLabel),
                'event' => 'Status geändert',
                'description' => $oldLabel . ' → ' . $newLabel,
                'reason' => $row->note ?? null,
                'created_at' => $row->changed_at ?? $row->created_at ?? null,
                'date' => $row->changed_at ?? $row->created_at ?? null,
                'employee_id' => $employeeId,
                'employee_name' => $employeeName,
                'source_type' => $row->planner_item_source_type ?? null,
                'source_id' => $row->planner_item_source_id ?? null,
                'planner_item_id' => $row->planner_item_id ? (int) $row->planner_item_id : null,
            ];
        })->values()->all();
    }

    private function ppProjectCommentActivities(int $projectId, array $planIds, int $limit): array
    {
        if (empty($planIds)) {
            return [];
        }

        $table = null;
        foreach (['planner_item_comments', 'planner_item_reports'] as $candidate) {
            if (Schema::hasTable($candidate)) {
                $table = $candidate;
                break;
            }
        }

        if (!$table || !$this->safeColumn($table, 'planner_item_id')) {
            return [];
        }

        $q = DB::table($table . ' as c')
            ->join('planner_items as pi', 'pi.id', '=', 'c.planner_item_id')
            ->whereIn('pi.plan_id', $planIds)
            ->when($this->safeColumn($table, 'deleted_at'), fn($query) => $query->whereNull('c.deleted_at'));

        $rows = $q->orderByDesc($this->safeColumn($table, 'created_at') ? 'c.created_at' : 'c.id')
            ->limit($limit)
            ->get(['c.*', 'pi.title as planner_item_title', 'pi.source_type', 'pi.source_id']);

        $employeeIds = $rows->map(fn($row) => $row->employee_id ?? $row->created_by_employee_id ?? $row->created_by ?? null)->filter()->all();
        $employees = $this->ppActivityEmployees($employeeIds);

        return $rows->map(function ($row) use ($employees) {
            $employeeId = $row->employee_id ?? $row->created_by_employee_id ?? $row->created_by ?? null;
            $employeeId = is_numeric($employeeId) ? (int) $employeeId : null;
            $body = $row->body ?? $row->comment ?? $row->report ?? $row->description ?? null;

            return [
                'type' => 'comment',
                'icon' => 'message-square-text',
                'title' => ($row->planner_item_title ?: 'Arbeit') . ' · Kommentar/Bericht',
                'event' => 'Kommentar/Bericht',
                'description' => $body ? Str::limit(strip_tags((string) $body), 160) : null,
                'reason' => $row->title ?? $row->subject ?? null,
                'created_at' => $row->created_at ?? $row->updated_at ?? null,
                'date' => $row->created_at ?? $row->updated_at ?? null,
                'employee_id' => $employeeId,
                'employee_name' => $employeeId && isset($employees[$employeeId]) ? $employees[$employeeId] : ($row->author_name ?? $row->created_by_name ?? null),
                'source_type' => $row->source_type ?? null,
                'source_id' => $row->source_id ?? null,
                'planner_item_id' => $row->planner_item_id ? (int) $row->planner_item_id : null,
            ];
        })->values()->all();
    }

    private function ppProjectMaterialActivities(int $projectId, array $planIds, int $limit): array
    {
        $activities = [];

        if (!empty($planIds) && Schema::hasTable('planner_item_materials')) {
            $q = DB::table('planner_item_materials as m')
                ->join('planner_items as pi', 'pi.id', '=', 'm.planner_item_id')
                ->whereIn('pi.plan_id', $planIds)
                ->when($this->safeColumn('planner_item_materials', 'deleted_at'), fn($query) => $query->whereNull('m.deleted_at'));

            $rows = $q->orderByDesc($this->safeColumn('planner_item_materials', 'created_at') ? 'm.created_at' : 'm.id')
                ->limit($limit)
                ->get(['m.*', 'pi.title as planner_item_title', 'pi.source_type', 'pi.source_id']);

            $employeeIds = $rows->map(fn($row) => $row->created_by_employee_id ?? $row->employee_id ?? $row->created_by ?? null)->filter()->all();
            $employees = $this->ppActivityEmployees($employeeIds);

            foreach ($rows as $row) {
                $employeeId = $row->created_by_employee_id ?? $row->employee_id ?? $row->created_by ?? null;
                $employeeId = is_numeric($employeeId) ? (int) $employeeId : null;
                $name = $row->name ?? $row->material_name ?? $row->product_name ?? $row->article_name ?? $row->title ?? 'Material';
                $qty = $row->quantity ?? $row->qty ?? $row->amount ?? null;

                $activities[] = [
                    'type' => 'material',
                    'icon' => 'package-plus',
                    'title' => ($row->planner_item_title ?: 'Arbeit') . ' · Material',
                    'event' => 'Material hinzugefügt',
                    'description' => trim($name . ($qty ? (' · Menge: ' . $qty) : '')),
                    'reason' => $row->note ?? null,
                    'created_at' => $row->created_at ?? $row->updated_at ?? null,
                    'date' => $row->created_at ?? $row->updated_at ?? null,
                    'employee_id' => $employeeId,
                    'employee_name' => $employeeId && isset($employees[$employeeId]) ? $employees[$employeeId] : null,
                    'source_type' => $row->source_type ?? null,
                    'source_id' => $row->source_id ?? null,
                    'planner_item_id' => $row->planner_item_id ? (int) $row->planner_item_id : null,
                ];
            }
        }

        if (Schema::hasTable('planner_group_materials')) {
            $q = DB::table('planner_group_materials as gm')
                ->when($this->safeColumn('planner_group_materials', 'deleted_at'), fn($query) => $query->whereNull('gm.deleted_at'));

            if ($this->safeColumn('planner_group_materials', 'planner_plan_id') && !empty($planIds)) {
                $q->whereIn('gm.planner_plan_id', $planIds);
            } elseif ($this->safeColumn('planner_group_materials', 'plan_id') && !empty($planIds)) {
                $q->whereIn('gm.plan_id', $planIds);
            } elseif ($this->safeColumn('planner_group_materials', 'project_id')) {
                $q->where('gm.project_id', $projectId);
            } else {
                return $activities;
            }

            $rows = $q->orderByDesc($this->safeColumn('planner_group_materials', 'created_at') ? 'gm.created_at' : 'gm.id')
                ->limit($limit)
                ->get();

            $employeeIds = $rows->map(fn($row) => $row->created_by_employee_id ?? $row->employee_id ?? $row->created_by ?? null)->filter()->all();
            $employees = $this->ppActivityEmployees($employeeIds);

            foreach ($rows as $row) {
                $employeeId = $row->created_by_employee_id ?? $row->employee_id ?? $row->created_by ?? null;
                $employeeId = is_numeric($employeeId) ? (int) $employeeId : null;
                $name = $row->material_name ?? $row->name ?? $row->product_name ?? $row->title ?? $row->group_name ?? 'Gruppenmaterial';
                $qty = $row->quantity ?? $row->qty ?? $row->amount ?? null;

                $activities[] = [
                    'type' => 'group_material',
                    'icon' => 'boxes',
                    'title' => 'Gruppenmaterial',
                    'event' => 'Gruppenmaterial hinzugefügt',
                    'description' => trim($name . ($qty ? (' · Menge: ' . $qty) : '')),
                    'reason' => $row->group_name ?? null,
                    'created_at' => $row->created_at ?? $row->updated_at ?? null,
                    'date' => $row->created_at ?? $row->updated_at ?? null,
                    'employee_id' => $employeeId,
                    'employee_name' => $employeeId && isset($employees[$employeeId]) ? $employees[$employeeId] : null,
                    'source_type' => 'group_material',
                    'source_id' => $row->id ? (int) $row->id : null,
                    'planner_item_id' => null,
                ];
            }
        }

        return $activities;
    }

    private function ppProjectGalleryActivities(int $projectId, array $planIds, int $limit): array
    {
        if (!Schema::hasTable('images') || empty($planIds)) {
            return [];
        }

        $linkColumn = null;
        foreach (['planner_item_id', 'task_id'] as $column) {
            if ($this->safeColumn('images', $column)) {
                $linkColumn = $column;
                break;
            }
        }

        if (!$linkColumn) {
            return [];
        }

        $rows = DB::table('images as img')
            ->join('planner_items as pi', 'pi.id', '=', 'img.' . $linkColumn)
            ->whereIn('pi.plan_id', $planIds)
            ->when($this->safeColumn('images', 'deleted_at'), fn($query) => $query->whereNull('img.deleted_at'))
            ->orderByDesc($this->safeColumn('images', 'created_at') ? 'img.created_at' : 'img.id')
            ->limit($limit)
            ->get(['img.*', 'pi.title as planner_item_title', 'pi.source_type', 'pi.source_id']);

        $employeeIds = $rows->map(fn($row) => $row->created_by ?? $row->created_by_employee_id ?? $row->employee_id ?? null)->filter()->all();
        $employees = $this->ppActivityEmployees($employeeIds);

        return $rows->map(function ($row) use ($employees) {
            $employeeId = $row->created_by ?? $row->created_by_employee_id ?? $row->employee_id ?? null;
            $employeeId = is_numeric($employeeId) ? (int) $employeeId : null;
            $name = $row->image_name ?? $row->name ?? $row->file_name ?? 'Bild/Datei';

            return [
                'type' => 'gallery',
                'icon' => 'image-plus',
                'title' => ($row->planner_item_title ?: 'Arbeit') . ' · Galerie',
                'event' => 'Galerie aktualisiert',
                'description' => $name,
                'reason' => null,
                'created_at' => $row->created_at ?? $row->updated_at ?? null,
                'date' => $row->created_at ?? $row->updated_at ?? null,
                'employee_id' => $employeeId,
                'employee_name' => $employeeId && isset($employees[$employeeId]) ? $employees[$employeeId] : null,
                'source_type' => $row->source_type ?? null,
                'source_id' => $row->source_id ?? null,
                'planner_item_id' => isset($row->planner_item_id) && $row->planner_item_id ? (int) $row->planner_item_id : (isset($row->task_id) && $row->task_id ? (int) $row->task_id : null),
            ];
        })->values()->all();
    }

    private function ppProjectPlannerItemActivities(int $projectId, array $planIds, int $limit): array
    {
        if (!Schema::hasTable('planner_items') || empty($planIds)) {
            return [];
        }

        $rows = DB::table('planner_items as pi')
            ->whereIn('pi.plan_id', $planIds)
            ->when($this->safeColumn('planner_items', 'deleted_at'), fn($query) => $query->whereNull('pi.deleted_at'))
            ->orderByDesc($this->safeColumn('planner_items', 'updated_at') ? 'pi.updated_at' : 'pi.id')
            ->limit($limit)
            ->get(['pi.*']);

        $employeeIds = $rows->map(fn($row) => $row->done_by_employee_id ?? $row->done_by ?? $row->updated_by ?? $row->created_by_employee_id ?? $row->created_by ?? null)->filter()->all();
        $employees = $this->ppActivityEmployees($employeeIds);

        return $rows->map(function ($row) use ($employees) {
            $status = $this->pmoNormalizePlannerStatus((string) ($row->status ?? 'open'));
            $employeeId = $row->done_by_employee_id ?? $row->done_by ?? $row->updated_by ?? $row->created_by_employee_id ?? $row->created_by ?? null;
            $employeeId = is_numeric($employeeId) ? (int) $employeeId : null;
            $createdAt = $this->pmoIsDoneStatus($status)
                ? ($row->done_at ?? $row->completed_at ?? $row->updated_at ?? $row->created_at ?? null)
                : ($row->updated_at ?? $row->created_at ?? null);

            return [
                'type' => 'planner_item',
                'icon' => $this->pmoIsDoneStatus($status) ? 'badge-check' : 'clipboard-list',
                'title' => ($row->title ?: 'Arbeit') . ' · ' . $this->pmoStatusLabel($status),
                'event' => 'Arbeit aktualisiert',
                'description' => $row->description ? Str::limit(strip_tags((string) $row->description), 140) : null,
                'reason' => 'Status: ' . $this->pmoStatusLabel($status),
                'created_at' => $createdAt,
                'date' => $createdAt,
                'employee_id' => $employeeId,
                'employee_name' => $employeeId && isset($employees[$employeeId]) ? $employees[$employeeId] : null,
                'source_type' => $row->source_type ?? null,
                'source_id' => $row->source_id ?? null,
                'planner_item_id' => $row->id ? (int) $row->id : null,
            ];
        })->values()->all();
    }

    private function ppProjectAttendanceActivities(int $projectId, array $planIds, int $limit): array
    {
        if (!Schema::hasTable('attendances')) {
            return [];
        }

        $q = DB::table('attendances as a');

        if ($this->safeColumn('attendances', 'planner_plan_id') && !empty($planIds)) {
            $q->whereIn('a.planner_plan_id', $planIds);
        } elseif ($this->safeColumn('attendances', 'plan_id') && !empty($planIds)) {
            $q->whereIn('a.plan_id', $planIds);
        } elseif ($this->safeColumn('attendances', 'project_id')) {
            $q->where('a.project_id', $projectId);
        } else {
            return [];
        }

        $rows = $q->orderByDesc($this->safeColumn('attendances', 'updated_at') ? 'a.updated_at' : 'a.id')
            ->limit($limit)
            ->get();

        $employees = $this->ppActivityEmployees($rows->pluck('employee_id')->filter()->all());

        return $rows->map(function ($row) use ($employees) {
            $employeeId = $row->employee_id ? (int) $row->employee_id : null;
            $status = $row->status ?? $row->work_status ?? null;
            $createdAt = $row->updated_at ?? $row->check_out ?? $row->check_in ?? $row->created_at ?? null;

            return [
                'type' => 'attendance',
                'icon' => 'map-pinned',
                'title' => 'Anwesenheit / Fahrt',
                'event' => 'Anwesenheit aktualisiert',
                'description' => $status ? ('Status: ' . $status) : null,
                'reason' => $row->destination_name ?? $row->destination_address ?? null,
                'created_at' => $createdAt,
                'date' => $createdAt,
                'employee_id' => $employeeId,
                'employee_name' => $employeeId && isset($employees[$employeeId]) ? $employees[$employeeId] : null,
                'source_type' => 'attendance',
                'source_id' => $row->id ? (int) $row->id : null,
                'planner_item_id' => null,
            ];
        })->values()->all();
    }

    private function ppActivityEmployees(array $employeeIds): array
    {
        $employeeIds = array_values(array_unique(array_filter(array_map('intval', $employeeIds))));

        if (empty($employeeIds) || !Schema::hasTable('employees')) {
            return [];
        }

        $columns = ['id'];
        foreach (['name', 'lastname', 'title'] as $column) {
            if ($this->safeColumn('employees', $column)) {
                $columns[] = $column;
            }
        }

        return DB::table('employees')
            ->whereIn('id', $employeeIds)
            ->get($columns)
            ->mapWithKeys(function ($employee) {
                $fullName = trim((($employee->title ?? '') ? ($employee->title . ' ') : '') . (($employee->name ?? '') . ' ' . ($employee->lastname ?? '')));
                return [(int) $employee->id => $fullName !== '' ? $fullName : ('Mitarbeiter #' . $employee->id)];
            })
            ->all();
    }


    public function projectHistory(Request $request, LeadProductList $project)
    {
        $stageHistory = collect($this->ppStageHistory($project->stage_history))
            ->map(function ($item) {
                $date = $item['created_at']
                    ?? $item['date']
                    ?? $item['at']
                    ?? null;

                return [
                    'type' => 'stage',
                    'icon' => 'git-branch',
                    'title' => $item['title'] ?? $item['event'] ?? 'Stage-Aktivität',
                    'event' => $item['event'] ?? $item['title'] ?? 'Stage-Aktivität',
                    'description' => $item['description'] ?? $item['text'] ?? null,
                    'reason' => $item['reason'] ?? null,
                    'from' => $item['from'] ?? null,
                    'to' => $item['to'] ?? null,
                    'created_at' => $date,
                    'date' => $date,
                    'employee_id' => $item['employee_id'] ?? $item['user_id'] ?? null,
                    'employee_name' => null,
                ];
            });

        $plannerActivities = collect($this->ppProjectActivityTimeline((int) $project->id, 160));

        $plans = PlannerPlan::query()
            ->where('project_id', $project->id)
            ->whereNull('deleted_at')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(function ($plan) {
                return [
                    'type' => 'plan',
                    'icon' => 'calendar-check',
                    'title' => 'Planner-Plan #' . $plan->id,
                    'event' => 'Planner-Plan',
                    'description' => $plan->title,
                    'reason' => null,
                    'created_at' => optional($plan->created_at)->toDateTimeString(),
                    'date' => optional($plan->created_at)->toDateTimeString(),
                    'employee_id' => null,
                    'employee_name' => null,
                ];
            });

        $items = $plannerActivities
            ->merge($stageHistory)
            ->merge($plans)
            ->filter(fn($item) => !empty($item['created_at']))
            ->sortByDesc('created_at')
            ->values();

        $items = $items->map(function ($item, $index) use ($items) {
            try {
                $current = Carbon::parse($item['created_at']);
                $next = isset($items[$index + 1]) ? Carbon::parse($items[$index + 1]['created_at']) : null;

                $item['date'] = $current->toDateTimeString();
                $item['created_at_label'] = $current->format('d.m.Y H:i');
                $item['diff_from_now'] = $current->diffForHumans();
                $item['diff_from_previous'] = $next ? $next->diffForHumans($current, true) : null;
            } catch (\Throwable $e) {
                $item['diff_from_now'] = null;
                $item['diff_from_previous'] = null;
            }

            return $item;
        });

        $projectRow = $this->ppProjectBaseQuery()
            ->where('lpl.id', $project->id)
            ->first();

        return response()->json([
            'ok' => true,
            'project' => $projectRow ? $this->ppProjectPayload($projectRow, $this->projectCockpitCounts(collect([$projectRow])), $this->ppEmployeesForProjectRows(collect([$projectRow]))) : null,
            'latest' => $items->first(),
            'items' => $items,
        ]);
    }

    public function projectProfile(LeadProductList $project)
    {
        $plan = PlannerPlan::firstOrCreate(
            [
                'customer_id' => (int) $project->customer_id,
                'project_id' => (int) $project->id,
            ],
            [
                'account_id' => null,
                'stage' => $project->status ?: 'project',
                'title' => 'Projektplan #' . $project->id,
                'status' => 'active',
                'created_by' => auth()->id(),
                'meta' => [
                    'product_id' => $project->product_id,
                    'alternative_id' => $project->alternative_id,
                    'lead_stage_sub_stage_id' => $project->lead_stage_sub_stage_id,
                ],
            ]
        );

        $this->syncProjectScopedPlan($plan);

        return redirect()->route('planner.projects', [
            'customer_id' => $project->customer_id,
            'project_id' => $project->id,
            'plan_id' => $plan->id,
        ]);
    }
    public function projectProfileData(Request $request, LeadProductList $project)
    {
        $row = $this->ppProjectBaseQuery()
            ->where('lpl.id', $project->id)
            ->first();

        if (!$row) {
            return response()->json([
                'ok' => false,
                'message' => 'Projekt wurde nicht gefunden.',
            ], 404);
        }

        $counts = $this->projectCockpitCounts(collect([$row]));
        $employees = $this->ppEmployeesForProjectRows(collect([$row]));

        $plan = PlannerPlan::query()
            ->where('project_id', $project->id)
            ->whereNull('deleted_at')
            ->latest('id')
            ->first();

        return response()->json([
            'ok' => true,
            'project' => $this->ppProjectPayload($row, $counts, $employees),
            'plan' => $plan ? [
                'id' => $plan->id,
                'title' => $plan->title,
                'stage' => $plan->stage,
                'status' => $plan->status,
                'created_at' => optional($plan->created_at)->toDateTimeString(),
                'updated_at' => optional($plan->updated_at)->toDateTimeString(),
            ] : null,
            'debug_counts' => method_exists($this, 'pmoDebugCounts') ? $this->pmoDebugCounts($project) : [],
        ]);
    }


    public function saveProjectTeam(Request $request, LeadProductList $project)
    {
        $data = $request->validate([
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer', 'min:1'],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => ['integer', 'min:1'],
            'employees' => ['nullable', 'array'],
            'employees.*' => ['integer', 'min:1'],
            'employee_id' => ['nullable', 'integer', 'min:1'],
            'field_employee' => ['nullable', 'integer', 'min:1'],
        ]);

        $ids = [];

        foreach (['employee_ids', 'team_ids', 'employees'] as $key) {
            if (!empty($data[$key]) && is_array($data[$key])) {
                $ids = array_merge($ids, $data[$key]);
            }
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        $updates = [
            'teams' => $ids,
        ];

        if (array_key_exists('employee_id', $data)) {
            $updates['employee_id'] = $data['employee_id'];
        }

        if (array_key_exists('field_employee', $data)) {
            $updates['field_employee'] = $data['field_employee'];
        }

        $project->fill($updates);
        $project->save();

        return response()->json([
            'ok' => true,
            'message' => 'Projekt-Team wurde gespeichert.',
            'team_ids' => $ids,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Helper methods for project cockpit
    |--------------------------------------------------------------------------
    */

    private function ppProjectBaseQuery()
    {
        $latestPlanSub = DB::table('planner_plans')
            ->whereNull('deleted_at')
            ->select('project_id', DB::raw('MAX(id) as planner_plan_id'))
            ->groupBy('project_id');

        return DB::table('lead_product_lists as lpl')
            ->whereNull('lpl.deleted_at')
            ->leftJoin('new_leads as c', 'c.id', '=', 'lpl.customer_id')
            ->leftJoin('lead_alternative_adds as a', 'a.id', '=', 'lpl.alternative_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
            ->leftJoin('phase_sections as ps', 'ps.id', '=', 'lpl.service_id')
            ->leftJoin('lead_stages as ls', 'ls.key', '=', 'lpl.status')
            ->leftJoin('lead_stage_sub_stages as lss', 'lss.id', '=', 'lpl.lead_stage_sub_stage_id')
            ->leftJoinSub($latestPlanSub, 'pp_latest', function ($join) {
                $join->on('pp_latest.project_id', '=', 'lpl.id');
            })
            ->select([
                'lpl.id',
                'lpl.customer_id',
                'lpl.alternative_id',
                'lpl.product_id',
                'lpl.service_id',
                'lpl.employee_id',
                'lpl.field_employee',
                'lpl.teams',
                'lpl.status',
                'lpl.work_status',
                'lpl.price',
                'lpl.price_latest',
                'lpl.project_minutes',
                'lpl.product_stage_id',
                'lpl.product_task_phase_id',
                'lpl.lead_stage_sub_stage_id',
                'lpl.updated_at',

                'c.customer_no',
                'c.firma',
                'c.name as customer_name',
                'c.lastname as customer_lastname',
                'c.phone as customer_phone',
                'c.email as customer_email',

                'a.object_name',
                'a.full_address as object_full_address',
                'a.street as object_street',
                'a.postcode as object_postcode',
                'a.city as object_city',

                'ag.article_group as product_name',
                'ag.initial as product_initial',
                'ag.image as product_image',

                'ps.phase_section as service_name',

                'ls.name as stage_name',
                'ls.color as stage_color',
                'ls.icon as stage_icon',

                'lss.name as sub_stage_name',
                'lss.color as sub_stage_color',

                'pp_latest.planner_plan_id',
            ]);
    }


    private function ppApplyProjectFilters($query, Request $request, ?string $stage = 'project'): void
    {
        $search = trim((string) $request->query('q', ''));
        $employeeId = (int) $request->query('employee_id', 0);
        $myOnly = filter_var($request->query('my'), FILTER_VALIDATE_BOOLEAN);
        $currentEmployeeId = $this->authEmployeeId();
        $subStageId = (int) $request->query('sub_stage_id', 0);

        if ($stage !== null && $stage !== '') {
            $query->where(function ($q) use ($stage) {
                $q->where('lpl.status', $stage);

                if ($stage === 'project') {
                    $q->orWhere('lpl.status', 'montage')
                        ->orWhere('lpl.status', 'projekt');
                }
            });
        } else {
            $query->whereNotIn(DB::raw('LOWER(COALESCE(lpl.status, ""))'), [
                'completed',
                'complete',
                'archive',
                'archiv',
                'junk',
                'deleted',
            ]);
        }

        if ($subStageId > 0) {
            $query->where('lpl.lead_stage_sub_stage_id', $subStageId);
        }

        if ($search !== '') {
            $like = '%' . mb_strtolower($search) . '%';

            $query->where(function ($q) use ($like) {
                $q->whereRaw('LOWER(COALESCE(c.customer_no, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(c.firma, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(c.name, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(c.lastname, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(a.object_name, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(a.full_address, "")) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(COALESCE(ag.article_group, "")) LIKE ?', [$like]);
            });
        }

        $teamFilterEmployeeId = $myOnly ? (int) $currentEmployeeId : $employeeId;

        if ($teamFilterEmployeeId > 0) {
            $query->where(function ($q) use ($teamFilterEmployeeId) {
                $q->where('lpl.employee_id', $teamFilterEmployeeId)
                    ->orWhere('lpl.field_employee', $teamFilterEmployeeId)
                    ->orWhere('lpl.teams', 'like', '%"employee_id":' . $teamFilterEmployeeId . '%')
                    ->orWhere('lpl.teams', 'like', '%"employee_id":"' . $teamFilterEmployeeId . '"%')
                    ->orWhere('lpl.teams', 'like', '%"id":' . $teamFilterEmployeeId . '%')
                    ->orWhere('lpl.teams', 'like', '%"id":"' . $teamFilterEmployeeId . '"%')
                    ->orWhere('lpl.teams', 'like', '%[' . $teamFilterEmployeeId . ']%')
                    ->orWhere('lpl.teams', 'like', '%,' . $teamFilterEmployeeId . ',%');
            });
        }
    }


    private function ppProjectPayload($project, array $counts, $employees): array
    {
        $projectId = (int) $project->id;

        $moduleCounts = [
            'appointments' => (int) ($counts['appointments'][$projectId] ?? 0),
            'personal_tasks' => (int) ($counts['personal_tasks'][$projectId] ?? 0),
            'tickets' => (int) ($counts['tickets'][$projectId] ?? 0),
            'kanban_tasks' => (int) ($counts['kanban_tasks'][$projectId] ?? 0),
            'planner_items' => (int) ($counts['planner_items'][$projectId] ?? 0),
            'planner_total' => (int) ($counts['planner_items'][$projectId] ?? 0),
        ];

        $done = (int) ($counts['planner_done'][$projectId] ?? 0);
        $total = max(0, (int) ($counts['planner_items'][$projectId] ?? 0));
        $progress = $total > 0 ? (int) round(($done / $total) * 100) : 0;

        $teamIds = $this->ppProjectTeamIds($project);

        return [
            'id' => $projectId,

            'customer' => [
                'id' => (int) $project->customer_id,
                'number' => $project->customer_no,
                'name' => $this->ppCustomerName($project),
                'phone' => $project->customer_phone,
                'email' => $project->customer_email,
            ],

            'object' => [
                'id' => $project->alternative_id ? (int) $project->alternative_id : null,
                'name' => $project->object_name ?: 'Objekt',
                'address' => $project->object_full_address ?: trim(collect([
                    $project->object_street ?? null,
                    $project->object_postcode ?? null,
                    $project->object_city ?? null,
                ])->filter()->implode(' ')),
                'street' => $project->object_street,
                'postcode' => $project->object_postcode,
                'city' => $project->object_city,
            ],

            'product' => [
                'id' => $project->product_id ? (int) $project->product_id : null,
                'name' => $project->product_name ?: 'Produkt',
                'initial' => $this->ppArticleInitial($project->product_initial ?? null, $project->product_name ?? null),
                'image' => $this->ppImageUrl($project->product_image ?? null, 'images/article_groups'),
            ],

            'service_name' => $project->service_name ?: 'Montage',

            'stage' => [
                'key' => $project->status ?: 'project',
                'name' => $project->stage_name ?: $project->status ?: 'Montage',
                'color' => $project->stage_color ?: '#74b2d4',
                'icon' => $project->stage_icon ?: 'wrench',
            ],

            'sub_stage' => [
                'id' => $project->lead_stage_sub_stage_id ? (int) $project->lead_stage_sub_stage_id : null,
                'name' => $project->sub_stage_name,
                'color' => $project->sub_stage_color ?: '#93c21c',
            ],

            'team' => collect($teamIds)
                ->map(fn($id) => $employees[$id] ?? null)
                ->filter()
                ->map(fn($employee) => [
                    'id' => (int) $employee->id,
                    'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    'full_name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    'photo_url' => $this->ppEmployeePhotoUrl($employee),
                    'initials' => $this->ppEmployeeInitials($employee),
                ])
                ->values(),

            'counts' => $moduleCounts,
            'progress' => $progress,
            'latest_activity' => collect($this->ppProjectActivityTimeline($projectId, 1))->first(),

            'planner_plan_id' => $project->planner_plan_id ? (int) $project->planner_plan_id : null,
            'price' => $project->price,
            'price_latest' => $project->price_latest,
            'project_minutes' => $project->project_minutes,
            'updated_at' => $project->updated_at ? Carbon::parse($project->updated_at)->format('d.m.Y H:i') : null,
        ];
    }


    private function ppEmployeesForProjectRows($rows)
    {
        $ids = [];

        foreach ($rows as $row) {
            if (!empty($row->employee_id)) {
                $ids[] = (int) $row->employee_id;
            }

            if (!empty($row->field_employee)) {
                $ids[] = (int) $row->field_employee;
            }

            $ids = array_merge($ids, $this->ppTeamIds($row->teams ?? []));
        }

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (empty($ids)) {
            return collect();
        }

        $columns = ['id', 'name', 'lastname'];

        foreach (['title', 'image', 'photo', 'avatar'] as $column) {
            if (Schema::hasColumn('employees', $column)) {
                $columns[] = $column;
            }
        }

        return DB::table('employees')
            ->whereIn('id', $ids)
            ->select($columns)
            ->get()
            ->keyBy('id');
    }


    private function ppProjectTeamIds($project): array
    {
        $ids = [];

        if (!empty($project->employee_id)) {
            $ids[] = (int) $project->employee_id;
        }

        if (!empty($project->field_employee)) {
            $ids[] = (int) $project->field_employee;
        }

        $ids = array_merge($ids, $this->ppTeamIds($project->teams ?? []));

        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }


    private function ppTeamIds($raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($raw)) {
            return [];
        }

        if (isset($raw['ids']) && is_array($raw['ids'])) {
            $raw = $raw['ids'];
        } elseif (isset($raw['team']) && is_array($raw['team'])) {
            $raw = $raw['team'];
        } elseif (isset($raw['employees']) && is_array($raw['employees'])) {
            $raw = $raw['employees'];
        }

        $ids = [];

        foreach ($raw as $value) {
            if (is_array($value)) {
                $value = $value['employee_id'] ?? $value['id'] ?? null;
            }

            if (is_numeric($value) && (int) $value > 0) {
                $ids[] = (int) $value;
            }
        }

        return array_values(array_unique($ids));
    }


    private function ppCustomerName($row): string
    {
        if (!empty($row->firma)) {
            return $row->firma;
        }

        $full = trim(($row->customer_name ?? '') . ' ' . ($row->customer_lastname ?? ''));

        if ($full !== '') {
            return $full;
        }

        if (!empty($row->customer_no)) {
            return '#' . $row->customer_no;
        }

        return '#' . ($row->customer_id ?? 'Kunde');
    }


    private function ppArticleInitial($initial = null, $name = null): string
    {
        $initial = trim((string) $initial);

        if ($initial !== '') {
            return mb_strtoupper(mb_substr($initial, 0, 4));
        }

        $name = trim((string) $name);

        if ($name === '') {
            return 'AG';
        }

        $parts = preg_split('/\s+/', $name);

        $letters = collect($parts)
            ->filter()
            ->take(2)
            ->map(fn($part) => mb_substr($part, 0, 1))
            ->implode('');

        return mb_strtoupper($letters ?: mb_substr($name, 0, 2));
    }


    private function ppEmployeeInitials($employee): string
    {
        $name = trim((string) ($employee->name ?? ''));
        $lastname = trim((string) ($employee->lastname ?? ''));

        $initials = mb_substr($name, 0, 1) . mb_substr($lastname, 0, 1);
        $initials = mb_strtoupper(trim($initials));

        return $initials !== '' ? $initials : 'MA';
    }


    private function ppEmployeePhotoUrl($employee): ?string
    {
        $photo = $employee->photo
            ?? $employee->image
            ?? $employee->avatar
            ?? null;

        return $this->ppImageUrl($photo, 'images/employee');
    }


    private function ppImageUrl($path, string $folder): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        if (
            str_starts_with($path, 'images/') ||
            str_starts_with($path, 'storage/') ||
            str_starts_with($path, 'uploads/')
        ) {
            return asset($path);
        }

        return asset(trim($folder, '/') . '/' . ltrim($path, '/'));
    }


    private function ppStageHistory($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }

    // =========================================================================
    // Planner Item Steps + Materials
    // =========================================================================


    private function plannerPlanProjectContext(PlannerPlan $plan): array
    {
        $project = $this->planProject($plan);
        $meta = $this->decodeJson($plan->meta ?? null);

        $leadProductListId = (int) ($project->id ?? $plan->project_id ?? 0);
        $customerId = (int) ($project->customer_id ?? $plan->customer_id ?? 0);
        $alternativeId = $project && !empty($project->alternative_id)
            ? (int) $project->alternative_id
            : (!empty($meta['alternative_id']) ? (int) $meta['alternative_id'] : null);
        $productId = $project && !empty($project->product_id)
            ? (int) $project->product_id
            : (!empty($meta['product_id']) ? (int) $meta['product_id'] : null);

        return [
            'lead_product_list_id' => $leadProductListId > 0 ? $leadProductListId : null,
            'project_id' => $leadProductListId > 0 ? $leadProductListId : null,
            'customer_id' => $customerId > 0 ? $customerId : null,
            'alternative_id' => $alternativeId,
            'product_id' => $productId,
            'article_group' => $productId,
        ];
    }

    private function plannerItemCustomerContext(PlannerPlan $plan, PlannerItem $item): array
    {
        $ctx = $this->plannerPlanProjectContext($plan);

        if (!$ctx['customer_id'] && $item->source_type && $item->source_id) {
            $sourceTables = [
                'personal_task' => 'personal_tasks',
                'ticket' => 'problems',
                'appointment' => 'main_appointments',
                'kanban_task' => 'kanban_lead_tasks',
                'phase_activity' => 'phase_activities',
            ];

            $table = $sourceTables[$item->source_type] ?? null;

            if ($table && Schema::hasTable($table)) {
                $row = DB::table($table)->where('id', (int) $item->source_id)->first();

                if ($row) {
                    $ctx['customer_id'] = $ctx['customer_id'] ?: (!empty($row->customer_id) ? (int) $row->customer_id : null);
                    $ctx['alternative_id'] = $ctx['alternative_id'] ?: (!empty($row->alternative_id) ? (int) $row->alternative_id : null);
                    $ctx['product_id'] = $ctx['product_id'] ?: (!empty($row->product_id) ? (int) $row->product_id : null);
                    $ctx['article_group'] = $ctx['article_group'] ?: $ctx['product_id'];
                    $ctx['lead_product_list_id'] = $ctx['lead_product_list_id'] ?: (!empty($row->lead_product_list_id) ? (int) $row->lead_product_list_id : null);
                    $ctx['project_id'] = $ctx['lead_product_list_id'];
                }
            }
        }

        return $ctx;
    }

    private function plannerContextColumns(PlannerPlan $plan, PlannerItem $item): array
    {
        $ctx = $this->plannerItemCustomerContext($plan, $item);

        return [
            'plan_id' => (int) $plan->id,
            'planner_plan_id' => (int) $plan->id,
            'planner_item_id' => (int) $item->id,
            'lead_product_list_id' => $ctx['lead_product_list_id'],
            'project_id' => $ctx['lead_product_list_id'],
            'customer_id' => $ctx['customer_id'],
            'alternative_id' => $ctx['alternative_id'],
            'product_id' => $ctx['product_id'],
            'article_group' => $ctx['article_group'],
        ];
    }

    private function plannerCustomerFileUrl(?string $file): ?string
    {
        $file = trim((string) $file);

        if ($file === '') {
            return null;
        }

        if (Str::startsWith($file, ['http://', 'https://', '/'])) {
            return $file;
        }

        $clean = ltrim($file, '/');

        if (Str::startsWith($clean, ['storage/', 'uploads/', 'images/'])) {
            return asset($clean);
        }

        $path = 'uploads/customers/' . $clean;

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
        }

        return asset('storage/' . $path);
    }

    private function plannerCommentTable(): ?string
    {
        foreach (['planner_item_comments', 'planner_item_reports'] as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }

        return null;
    }

    private function plannerCommentRowPayload(object $row): array
    {
        return [
            'id' => (int) $row->id,
            'planner_item_id' => (int) ($row->planner_item_id ?? 0),
            'title' => $row->title ?? $row->subject ?? 'Kommentar',
            'body' => $row->body ?? $row->comment ?? $row->report ?? $row->description ?? null,
            'comment' => $row->comment ?? $row->body ?? $row->description ?? null,
            'author_name' => $row->author_name ?? $row->created_by_name ?? null,
            'created_by_employee_id' => $row->created_by_employee_id ?? $row->created_by ?? null,
            'created_at' => $row->created_at ?? null,
        ];
    }

    private function plannerEnsureItemBelongsToPlan(PlannerPlan $plan, PlannerItem $item): void
    {
        if ((int) $item->plan_id !== (int) $plan->id) {
            abort(404, 'Planner item does not belong to this plan.');
        }
    }

    private function plannerStepTable(): ?string
    {
        if (Schema::hasTable('planner_item_steps')) {
            return 'planner_item_steps';
        }

        if (Schema::hasTable('planner_item_checklists')) {
            return 'planner_item_checklists';
        }

        return null;
    }

    private function plannerFilterTableData(string $table, array $data, bool $forInsert = false): array
    {
        $filtered = [];

        foreach ($data as $column => $value) {
            if ($this->safeColumn($table, $column)) {
                $filtered[$column] = $value;
            }
        }

        if ($forInsert && $this->safeColumn($table, 'created_at') && !array_key_exists('created_at', $filtered)) {
            $filtered['created_at'] = now();
        }

        if ($this->safeColumn($table, 'updated_at') && !array_key_exists('updated_at', $filtered)) {
            $filtered['updated_at'] = now();
        }

        return $filtered;
    }

    private function plannerInsertRow(string $table, array $data): int
    {
        $filtered = $this->plannerFilterTableData($table, $data, true);

        if (empty($filtered)) {
            abort(422, "No writable columns found for {$table}.");
        }

        return (int) DB::table($table)->insertGetId($filtered);
    }

    private function plannerUpdateRow(string $table, int $id, array $data): void
    {
        $filtered = $this->plannerFilterTableData($table, $data, false);

        if (empty($filtered)) {
            return;
        }

        DB::table($table)->where('id', $id)->update($filtered);
    }

    private function plannerStepRowPayload(object $row): array
    {
        $isCompleted = (bool) (
            $row->is_completed
            ?? $row->is_done
            ?? in_array(strtolower((string) ($row->status ?? '')), ['done', 'completed', 'closed'], true)
        );

        return [
            'id' => (int) $row->id,
            'title' => $row->title ?? $row->key_name ?? $row->name ?? ('Schritt #' . $row->id),
            'description' => $row->description ?? $row->note ?? null,
            'status' => $row->status ?? ($isCompleted ? 'done' : 'open'),
            'is_completed' => $isCompleted,
            'is_required' => (bool) ($row->is_required ?? true),
            'sort_order' => $row->sort_order ?? null,
            'due_date' => $row->due_date ?? $row->date ?? null,
            'due_time' => $row->due_time ?? $row->time ?? null,
            'due_at' => $row->due_at ?? null,
            'origin_type' => $row->origin_type ?? $row->source_origin ?? 'manual',
            'response_status' => $row->response_status ?? null,
            'source_type' => $row->source_type ?? 'planner_item',
            'source_id' => $row->source_id ?? null,
            'requested_by_employee_id' => $row->requested_by_employee_id ?? $row->requested_by ?? null,
            'requested_at' => $row->requested_at ?? null,
            'created_by_employee_id' => $row->created_by_employee_id ?? $row->created_by ?? null,
            'created_at' => $row->created_at ?? null,
        ];
    }

    private function pmoLoadPlannerItemSteps(int $plannerItemId): array
    {
        $table = $this->plannerStepTable();

        if (!$table) {
            return [];
        }

        return DB::table($table)
            ->where('planner_item_id', $plannerItemId)
            ->when($this->safeColumn($table, 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->orderBy($this->safeColumn($table, 'sort_order') ? 'sort_order' : 'id')
            ->orderBy('id')
            ->get()
            ->map(fn($row) => $this->plannerStepRowPayload($row))
            ->values()
            ->all();
    }

    public function storeItemStep(Request $request, PlannerPlan $plan, PlannerItem $item)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        $table = $this->plannerStepTable();

        if (!$table) {
            return response()->json([
                'ok' => false,
                'message' => 'No step table exists. Create planner_item_steps or planner_item_checklists.',
            ], 422);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'due_time' => ['nullable', 'string', 'max:20'],
            'origin_type' => ['nullable', 'string', 'max:80'],
            'is_required' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $employeeId = $this->authEmployeeId();
        $context = $this->plannerContextColumns($plan, $item);

        $id = $this->plannerInsertRow($table, array_merge($context, [
            'planner_item_id' => (int) $item->id,
            'title' => $data['title'],
            'key_name' => $data['title'],
            'name' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'open',
            'is_completed' => 0,
            'is_done' => 0,
            'is_required' => (int) ($data['is_required'] ?? 1),
            'due_date' => $data['due_date'] ?? null,
            'due_time' => !empty($data['due_time']) ? $this->normalizeTime((string) $data['due_time'], '08:00:00') : null,
            'origin_type' => $data['origin_type'] ?? 'manual',
            'source_type' => 'planner_item',
            'source_id' => (int) $item->id,
            'requested_by_employee_id' => ($data['origin_type'] ?? '') === 'employee_request' ? $employeeId : null,
            'requested_at' => ($data['origin_type'] ?? '') === 'employee_request' ? now() : null,
            'created_by_employee_id' => $employeeId,
            'created_by' => $employeeId ?? auth()->id(),
            'sort_order' => (int) ($data['sort_order'] ?? ((DB::table($table)->where('planner_item_id', $item->id)->max('sort_order') ?? 0) + 1)),
        ]));

        $row = DB::table($table)->where('id', $id)->first();

        return response()->json([
            'ok' => true,
            'step' => $row ? $this->plannerStepRowPayload($row) : null,
            'data' => $this->pmoBuildMontagePayload((int) $plan->project_id, now()->toDateString(), 'day', (int) $plan->id),
        ]);
    }

    public function updateItemStep(Request $request, PlannerPlan $plan, PlannerItem $item, int $step)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        $table = $this->plannerStepTable();

        if (!$table) {
            return response()->json(['ok' => false, 'message' => 'No step table exists.'], 422);
        }

        $row = DB::table($table)
            ->where('id', $step)
            ->where('planner_item_id', (int) $item->id)
            ->first();

        if (!$row) {
            return response()->json(['ok' => false, 'message' => 'Schritt wurde nicht gefunden.'], 404);
        }

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'due_time' => ['nullable', 'string', 'max:20'],
            'origin_type' => ['nullable', 'string', 'max:80'],
            'is_required' => ['nullable', 'boolean'],
            'is_completed' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $updates = [];

        if (array_key_exists('title', $data)) {
            $updates['title'] = $data['title'];
            $updates['key_name'] = $data['title'];
            $updates['name'] = $data['title'];
        }

        if (array_key_exists('description', $data)) {
            $updates['description'] = $data['description'];
        }

        if (array_key_exists('due_date', $data)) {
            $updates['due_date'] = $data['due_date'];
        }

        if (array_key_exists('due_time', $data)) {
            $updates['due_time'] = $data['due_time'] ? $this->normalizeTime((string) $data['due_time'], '08:00:00') : null;
        }

        if (array_key_exists('origin_type', $data)) {
            $updates['origin_type'] = $data['origin_type'];
        }

        if (array_key_exists('is_required', $data)) {
            $updates['is_required'] = (int) $data['is_required'];
        }

        if (array_key_exists('is_completed', $data)) {
            $done = (bool) $data['is_completed'];
            $updates['is_completed'] = $done ? 1 : 0;
            $updates['is_done'] = $done ? 1 : 0;
            $updates['status'] = $done ? 'done' : 'open';
            $updates['done_by'] = $done ? ($this->authEmployeeId() ?? auth()->id()) : null;
            $updates['done_date'] = $done ? now() : null;
        }

        if (array_key_exists('status', $data)) {
            $updates['status'] = $data['status'];
        }

        if (array_key_exists('sort_order', $data)) {
            $updates['sort_order'] = (int) $data['sort_order'];
        }

        $this->plannerUpdateRow($table, $step, $updates);

        $fresh = DB::table($table)->where('id', $step)->first();

        return response()->json([
            'ok' => true,
            'step' => $fresh ? $this->plannerStepRowPayload($fresh) : null,
        ]);
    }

    public function destroyItemStep(PlannerPlan $plan, PlannerItem $item, int $step)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        $table = $this->plannerStepTable();

        if (!$table) {
            return response()->json(['ok' => false, 'message' => 'No step table exists.'], 422);
        }

        $q = DB::table($table)
            ->where('id', $step)
            ->where('planner_item_id', (int) $item->id);

        if ($this->safeColumn($table, 'deleted_at')) {
            $q->update(['deleted_at' => now(), 'updated_at' => now()]);
        } else {
            $q->delete();
        }

        return response()->json(['ok' => true]);
    }

    private function plannerMaterialRowPayload(object $row): array
    {
        $qty = (float) ($row->qty ?? $row->quantity ?? 1);
        $unitPrice = (float) ($row->unit_price ?? $row->price ?? $row->vk ?? 0);
        $purchasePrice = (float) ($row->purchase_price ?? $row->ek ?? 0);
        $originType = (string) ($row->origin_type ?? $row->source_origin ?? 'manual');
        $status = (string) ($row->status ?? $row->response_status ?? '');
        $statusKey = mb_strtolower(trim($status));
        $hasRequestedMeta = !empty($row->requested_at) || !empty($row->requested_by_employee_id);
        $isRequest = $hasRequestedMeta || in_array(mb_strtolower($originType), ['employee_request', 'asked_by_employee', 'material_request', 'request'], true);
        $hasResponseMeta = !empty($row->responded_at) || !empty($row->approved_at) || !empty($row->rejected_at) || !empty($row->ordered_at) || !empty($row->received_at);
        $isResponded = $hasResponseMeta || in_array($statusKey, ['approved', 'accepted', 'rejected', 'declined', 'responded', 'ordered', 'received', 'done', 'completed', 'added'], true);
        $active = (bool) ($row->active ?? $row->is_active ?? true);
        $isOpenRequest = $isRequest && !$isResponded;
        $isAdded = $active && !$isOpenRequest;

        return [
            'id' => (int) $row->id,
            'planner_item_id' => (int) ($row->planner_item_id ?? 0),
            'product_id' => $row->product_id ?? null,
            'source_type' => $row->source_type ?? null,
            'source_id' => $row->source_id ?? null,
            'origin_type' => $row->origin_type ?? $row->source_origin ?? 'manual',
            'name' => $row->name ?? $row->title ?? $row->material_name ?? ('Material #' . $row->id),
            'title' => $row->title ?? $row->name ?? null,
            'article_no' => $row->article_no ?? null,
            'distributor_article_no' => $row->distributor_article_no ?? null,
            'sku' => $row->sku ?? null,
            'qty' => $qty,
            'quantity' => $qty,
            'unit' => $row->unit ?? $row->measure ?? $row->measure_unit ?? 'Stk',
            'measure' => $row->measure ?? $row->unit ?? $row->measure_unit ?? 'Stk',
            'unit_price' => $unitPrice,
            'price' => $unitPrice,
            'purchase_price' => $purchasePrice,
            'ek' => $purchasePrice,
            'total_price' => (float) ($row->total_price ?? ($qty * $unitPrice)),
            'distributor_id' => $row->distributor_id ?? null,
            'distributor_price_id' => $row->distributor_price_id ?? null,
            'distributor_name' => $row->distributor_name ?? null,
            'supplier' => $row->supplier ?? $row->distributor_name ?? null,
            'image_url' => $row->image_url ?? $row->img ?? $row->image ?? null,
            'active' => $active,
            'is_active' => $active,
            'is_request' => $isRequest,
            'is_request_open' => $isOpenRequest,
            'is_added' => $isAdded,
            'requested_by_employee_id' => $row->requested_by_employee_id ?? null,
            'requested_at' => $row->requested_at ?? null,
            'responded_at' => $row->responded_at ?? null,
            'approved_at' => $row->approved_at ?? null,
            'rejected_at' => $row->rejected_at ?? null,
            'created_by_employee_id' => $row->created_by_employee_id ?? $row->created_by ?? null,
            'created_at' => $row->created_at ?? null,
        ];
    }

    private function plannerMaterialTable(): ?string
    {
        return Schema::hasTable('planner_item_materials') ? 'planner_item_materials' : null;
    }

    private function plannerProductImage(?string $image): ?string
    {
        $image = trim((string) $image);

        if ($image === '') {
            return null;
        }

        if (Str::startsWith($image, ['http://', 'https://', '/'])) {
            return $image;
        }

        if (Str::startsWith($image, ['images/', 'storage/', 'uploads/'])) {
            return asset($image);
        }

        return asset('images/products/' . ltrim($image, '/'));
    }

    private function plannerLoadCurrentMaterials(PlannerItem $item): array
    {
        return $this->pmoLoadPlannerMaterials((int) $item->id);
    }

    private function plannerFlattenOfferSections($sections, int $offerDetailId, string $originType): array
    {
        $out = [];

        $walk = function ($items, $sectionTitle = null) use (&$walk, &$out, $offerDetailId, $originType) {
            foreach ((array) $items as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $kind = strtolower((string) ($item['kind'] ?? $item['item_type'] ?? ''));
                $lineType = strtolower((string) ($item['lineType'] ?? $item['line_type'] ?? ''));

                if (in_array($kind, ['labor', 'lohn'], true) || in_array($lineType, ['labor', 'lohn'], true)) {
                    // Labor belongs to workforce planning, not material picking.
                } else {
                    $name = trim((string) ($item['name'] ?? $item['product'] ?? $item['title'] ?? ''));

                    if ($name !== '') {
                        $out[] = [
                            'source_type' => 'offer_detail',
                            'source_id' => $offerDetailId,
                            'origin_type' => $originType,
                            'product_id' => $item['product_id'] ?? $item['productId'] ?? null,
                            'component_id' => $item['component_id'] ?? null,
                            'item_type' => $item['item_type'] ?? null,
                            'name' => $name,
                            'article_no' => $item['article_no'] ?? null,
                            'distributor_article_no' => $item['distributor_article_no'] ?? null,
                            'qty' => (float) ($item['qty'] ?? $item['quantity'] ?? 1),
                            'unit' => $item['unit'] ?? $item['measure'] ?? 'Stk',
                            'measure' => $item['measure'] ?? $item['unit'] ?? 'Stk',
                            'unit_price' => (float) ($item['price'] ?? $item['unit_price'] ?? 0),
                            'purchase_price' => (float) ($item['purchase_price'] ?? $item['ek'] ?? 0),
                            'distributor_id' => $item['distributor_id'] ?? null,
                            'distributor_price_id' => $item['distributor_price_id'] ?? null,
                            'distributor_name' => $item['distributor_name'] ?? $item['supplier'] ?? null,
                            'image_url' => $item['img'] ?? $item['image_url'] ?? null,
                            'section_title' => $sectionTitle,
                            'active' => (bool) ($item['active'] ?? true),
                            'source_payload' => $item,
                        ];
                    }
                }

                if (!empty($item['subItems']) && is_array($item['subItems'])) {
                    $walk($item['subItems'], $sectionTitle);
                }
            }
        };

        foreach ((array) $sections as $section) {
            if (!is_array($section)) {
                continue;
            }

            $walk($section['items'] ?? [], $section['title'] ?? null);
        }

        return $out;
    }

    private function plannerDealMaterialSources(PlannerPlan $plan): array
    {
        $project = $this->planProject($plan);

        if (!Schema::hasTable('offer_details')) {
            return [];
        }

        $hasOffers = Schema::hasTable('offers') && $this->safeColumn('offer_details', 'offer_id');

        $base = DB::table('offer_details as od');

        if ($hasOffers) {
            $base->leftJoin('offers as o', 'o.id', '=', 'od.offer_id');
        }

        if ($this->safeColumn('offer_details', 'document_status')) {
            $base->whereIn(DB::raw('LOWER(COALESCE(od.document_status, ""))'), ['deal', 'auftrag']);
        }

        if ($this->safeColumn('offer_details', 'deleted_at')) {
            $base->whereNull('od.deleted_at');
        }

        // IMPORTANT: do NOT hard-filter by product_id / alternative_id here.
        // In your offer/deal data these fields can be missing or different although the offer belongs to the same customer.
        // We filter by customer when possible, then only ORDER exact product/alternative matches first.
        if ($project && $hasOffers && $this->safeColumn('offers', 'customer_id')) {
            $base->where('o.customer_id', (int) $project->customer_id);
        }

        if ($project && $hasOffers && $this->safeColumn('offers', 'product_id')) {
            $base->orderByRaw('CASE WHEN COALESCE(o.product_id, 0) = ? THEN 0 ELSE 1 END', [(int) ($project->product_id ?? 0)]);
        }

        if ($project && $hasOffers && $this->safeColumn('offers', 'alternative_id')) {
            $base->orderByRaw('CASE WHEN COALESCE(o.alternative_id, 0) = ? THEN 0 ELSE 1 END', [(int) ($project->alternative_id ?? 0)]);
        }

        $select = [
            'od.id',
            DB::raw($this->safeColumn('offer_details', 'document_status') ? 'od.document_status' : "'auftrag' as document_status"),
            DB::raw($this->safeColumn('offer_details', 'sections') ? 'od.sections' : "'[]' as sections"),
            DB::raw($this->safeColumn('offer_details', 'angebot_snapshot_sections') ? 'od.angebot_snapshot_sections' : "'[]' as angebot_snapshot_sections"),
            DB::raw($this->safeColumn('offer_details', 'offer_no') ? 'od.offer_no' : 'NULL as offer_no'),
            DB::raw($this->safeColumn('offer_details', 'offer_id') ? 'od.offer_id' : 'NULL as offer_id'),
            DB::raw($this->safeColumn('offer_details', 'offer_folder_id') ? 'od.offer_folder_id' : 'NULL as offer_folder_id'),
            DB::raw($this->safeColumn('offer_details', 'created_at') ? 'od.created_at' : 'NULL as created_at'),
            DB::raw($this->safeColumn('offer_details', 'updated_at') ? 'od.updated_at' : 'NULL as updated_at'),
        ];

        $details = (clone $base)
            ->select($select)
            ->orderByRaw("CASE WHEN LOWER(COALESCE(od.document_status, '')) = 'auftrag' THEN 0 ELSE 1 END")
            ->orderByDesc('od.id')
            ->limit(12)
            ->get();

        // Fallback: if no customer-linked document was found, show the newest Auftrag/Deal docs.
        // This prevents the material modal from looking broken while still preferring customer-specific matches above.
        if ($details->isEmpty()) {
            $fallback = DB::table('offer_details as od');

            if ($this->safeColumn('offer_details', 'document_status')) {
                $fallback->whereIn(DB::raw('LOWER(COALESCE(od.document_status, ""))'), ['deal', 'auftrag']);
            }

            if ($this->safeColumn('offer_details', 'deleted_at')) {
                $fallback->whereNull('od.deleted_at');
            }

            $details = $fallback
                ->select($select)
                ->orderByRaw("CASE WHEN LOWER(COALESCE(od.document_status, '')) = 'auftrag' THEN 0 ELSE 1 END")
                ->orderByDesc('od.id')
                ->limit(5)
                ->get();
        }

        $materials = [];

        foreach ($details as $detail) {
            $sections = $this->decodeJson($detail->sections ?? null);

            if (empty($sections)) {
                $sections = $this->decodeJson($detail->angebot_snapshot_sections ?? null);
            }

            $origin = strtolower((string) ($detail->document_status ?? 'auftrag')) === 'auftrag'
                ? 'auftrag_final'
                : 'deal_final';

            foreach ($this->plannerFlattenOfferSections($sections, (int) $detail->id, $origin) as $row) {
                $row['offer_no'] = $detail->offer_no ?? null;
                $row['offer_id'] = $detail->offer_id ?? null;
                $row['offer_folder_id'] = $detail->offer_folder_id ?? null;
                $row['document_status'] = $detail->document_status ?? null;
                $materials[] = $row;
            }
        }

        return $materials;
    }



    private function plannerMasterSetDetailsPayload(array $masterSetIds): array
    {
        $masterSetIds = array_values(array_unique(array_filter(array_map('intval', $masterSetIds))));

        if (empty($masterSetIds) || !Schema::hasTable('master_sets')) {
            return [];
        }

        $setSelect = [
            'ms.id',
            'ms.name',
            DB::raw($this->safeColumn('master_sets', 'description') ? 'ms.description' : 'NULL as description'),
            DB::raw($this->safeColumn('master_sets', 'article_group_id') ? 'ms.article_group_id' : 'NULL as article_group_id'),
            DB::raw(Schema::hasTable('article_groups') && $this->safeColumn('article_groups', 'article_group') ? 'ag.article_group as article_group_name' : 'NULL as article_group_name'),
        ];

        $sets = DB::table('master_sets as ms')
            ->when(Schema::hasTable('article_groups'), fn($q) => $q->leftJoin('article_groups as ag', 'ag.id', '=', 'ms.article_group_id'))
            ->whereIn('ms.id', $masterSetIds)
            ->when($this->safeColumn('master_sets', 'deleted_at'), fn($q) => $q->whereNull('ms.deleted_at'))
            ->select($setSelect)
            ->get()
            ->keyBy('id');

        $details = [];

        foreach ($sets as $set) {
            $details[(int) $set->id] = [
                'type' => 'master_set',
                'id' => (int) $set->id,
                'name' => $set->name ?: ('MasterSet #' . (int) $set->id),
                'description' => $set->description ?? null,
                'article_group_id' => $set->article_group_id ? (int) $set->article_group_id : null,
                'article_group_name' => $set->article_group_name ?? null,
                'components' => [],
                'components_tree' => [],
                'labor' => [],
                'tasks' => [],
                'groups' => [],
                'summary' => [
                    'components' => 0,
                    'main_components' => 0,
                    'sub_components' => 0,
                    'labor' => 0,
                    'tasks' => 0,
                ],
            ];
        }

        if (Schema::hasTable('master_set_group_master_set') && Schema::hasTable('master_set_groups')) {
            $groupRows = DB::table('master_set_group_master_set as pivot')
                ->join('master_set_groups as msg', 'msg.id', '=', 'pivot.master_set_group_id')
                ->whereIn('pivot.master_set_id', $masterSetIds)
                ->when($this->safeColumn('master_set_groups', 'deleted_at'), fn($q) => $q->whereNull('msg.deleted_at'))
                ->select([
                    'pivot.master_set_id',
                    'msg.id',
                    'msg.name',
                    DB::raw($this->safeColumn('master_set_groups', 'description') ? 'msg.description' : 'NULL as description'),
                    DB::raw($this->safeColumn('master_set_groups', 'color') ? 'msg.color' : 'NULL as color'),
                ])
                ->orderBy('msg.name')
                ->get();

            foreach ($groupRows as $row) {
                $sid = (int) $row->master_set_id;
                if (!isset($details[$sid])) {
                    continue;
                }

                $details[$sid]['groups'][] = [
                    'id' => (int) $row->id,
                    'name' => $row->name ?: ('Gruppe #' . (int) $row->id),
                    'description' => $row->description ?? null,
                    'color' => $row->color ?? null,
                ];
            }
        }

        if (Schema::hasTable('master_set_components')) {
            $componentSelect = [
                'c.id',
                'c.master_set_id',
                DB::raw($this->safeColumn('master_set_components', 'parent_id') ? 'c.parent_id' : 'NULL as parent_id'),
                DB::raw($this->safeColumn('master_set_components', 'product_id') ? 'c.product_id' : 'NULL as product_id'),
                DB::raw($this->safeColumn('master_set_components', 'article_no') ? 'c.article_no' : 'NULL as article_no'),
                DB::raw($this->safeColumn('master_set_components', 'distributor_article_no') ? 'c.distributor_article_no' : 'NULL as distributor_article_no'),
                DB::raw($this->safeColumn('master_set_components', 'description') ? 'c.description' : 'NULL as description'),
                DB::raw($this->safeColumn('master_set_components', 'qty') ? 'c.qty' : '1 as qty'),
                DB::raw($this->safeColumn('master_set_components', 'measure') ? 'c.measure' : 'NULL as measure'),
                DB::raw($this->safeColumn('master_set_components', 'price_unit') ? 'c.price_unit' : 'NULL as price_unit'),
                DB::raw($this->safeColumn('master_set_components', 'unit_price') ? 'c.unit_price' : '0 as unit_price'),
                DB::raw($this->safeColumn('master_set_components', 'purchase_price') ? 'c.purchase_price' : '0 as purchase_price'),
                DB::raw($this->safeColumn('master_set_components', 'type') ? 'c.type' : 'NULL as component_type'),
                DB::raw($this->safeColumn('master_set_components', 'sort_order') ? 'c.sort_order' : '0 as sort_order'),
                DB::raw($this->safeColumn('products', 'product') ? 'p.product as product_name' : 'NULL as product_name'),
                DB::raw($this->safeColumn('products', 'model') ? 'p.model as product_model' : 'NULL as product_model'),
                DB::raw($this->safeColumn('products', 'sku') ? 'p.sku' : 'NULL as sku'),
                DB::raw(Schema::hasTable('product_images') ? '(SELECT image FROM product_images WHERE product_images.product_id = c.product_id ORDER BY id ASC LIMIT 1) as image' : 'NULL as image'),
                DB::raw($this->safeColumn('distributors', 'name') ? 'd.name as distributor_name' : 'NULL as distributor_name'),
                DB::raw($this->safeColumn('distributors', 'short_name') ? 'd.short_name as distributor_short_name' : 'NULL as distributor_short_name'),
            ];

            $componentRows = DB::table('master_set_components as c')
                ->leftJoin('products as p', 'p.id', '=', 'c.product_id')
                ->leftJoin('distributors as d', 'd.id', '=', 'c.distributor_id')
                ->whereIn('c.master_set_id', $masterSetIds)
                ->when($this->safeColumn('master_set_components', 'deleted_at'), fn($q) => $q->whereNull('c.deleted_at'))
                ->select($componentSelect)
                ->orderBy('c.master_set_id')
                ->when($this->safeColumn('master_set_components', 'sort_order'), fn($q) => $q->orderBy('c.sort_order'))
                ->orderBy('c.id')
                ->get();

            $bySet = [];

            foreach ($componentRows as $row) {
                $sid = (int) $row->master_set_id;
                $name = trim((string) ($row->product_name ?? ''));

                if (!empty($row->product_model)) {
                    $name = trim($name . ' ' . $row->product_model);
                }

                if ($name === '') {
                    $name = trim((string) ($row->description ?? ''));
                }

                if ($name === '') {
                    $name = 'Komponente #' . (int) $row->id;
                }

                $component = [
                    'id' => (int) $row->id,
                    'master_set_id' => $sid,
                    'parent_id' => $row->parent_id ? (int) $row->parent_id : null,
                    'product_id' => $row->product_id ? (int) $row->product_id : null,
                    'name' => $name,
                    'description' => $row->description ?? null,
                    'article_no' => $row->article_no ?? null,
                    'distributor_article_no' => $row->distributor_article_no ?? null,
                    'sku' => $row->sku ?? null,
                    'qty' => (float) ($row->qty ?? 1),
                    'unit' => $row->measure ?? $row->price_unit ?? 'Stk',
                    'measure' => $row->measure ?? $row->price_unit ?? 'Stk',
                    'unit_price' => (float) ($row->unit_price ?? 0),
                    'purchase_price' => (float) ($row->purchase_price ?? 0),
                    'total_price' => (float) (($row->qty ?? 1) * ($row->unit_price ?? 0)),
                    'type' => $row->component_type ?? null,
                    'sort_order' => (int) ($row->sort_order ?? 0),
                    'distributor_name' => $row->distributor_short_name ?: $row->distributor_name,
                    'image_url' => $this->plannerProductImage($row->image),
                    'children' => [],
                ];

                $bySet[$sid][] = $component;
            }

            foreach ($bySet as $sid => $components) {
                if (!isset($details[$sid])) {
                    continue;
                }

                $byId = [];
                foreach ($components as $component) {
                    $byId[$component['id']] = $component;
                }

                $tree = [];
                foreach ($byId as $cid => &$component) {
                    $parentId = $component['parent_id'];
                    if ($parentId && isset($byId[$parentId])) {
                        $byId[$parentId]['children'][] = &$component;
                    } else {
                        $tree[] = &$component;
                    }
                }
                unset($component);

                $mainCount = count(array_filter($components, fn($c) => empty($c['parent_id'])));
                $subCount = max(0, count($components) - $mainCount);

                $details[$sid]['components'] = array_values($components);
                $details[$sid]['components_tree'] = array_values($tree);
                $details[$sid]['summary']['components'] = count($components);
                $details[$sid]['summary']['main_components'] = $mainCount;
                $details[$sid]['summary']['sub_components'] = $subCount;
            }
        }

        if (Schema::hasTable('master_set_labor')) {
            $laborRows = DB::table('master_set_labor as l')
                ->leftJoin('position_qualifications as q', 'q.id', '=', 'l.qualification_id')
                ->leftJoin('departments as dep', 'dep.id', '=', 'l.department_id')
                ->leftJoin('positions as pos', 'pos.id', '=', 'l.position_id')
                ->leftJoin('employees as e', 'e.id', '=', 'l.employee_id')
                ->whereIn('l.master_set_id', $masterSetIds)
                ->select([
                    'l.id',
                    'l.master_set_id',
                    DB::raw($this->safeColumn('master_set_labor', 'hours') ? 'l.hours' : '0 as hours'),
                    DB::raw($this->safeColumn('master_set_labor', 'hourly_rate') ? 'l.hourly_rate' : '0 as hourly_rate'),
                    DB::raw($this->safeColumn('position_qualifications', 'name') ? 'q.name as qualification_name' : 'NULL as qualification_name'),
                    DB::raw($this->safeColumn('departments', 'department') ? 'dep.department as department_name' : ($this->safeColumn('departments', 'department_name') ? 'dep.department_name as department_name' : 'NULL as department_name')),
                    DB::raw($this->safeColumn('positions', 'position') ? 'pos.position as position_name' : ($this->safeColumn('positions', 'name') ? 'pos.name as position_name' : 'NULL as position_name')),
                    DB::raw("TRIM(CONCAT(COALESCE(e.name, ''), ' ', COALESCE(e.lastname, ''))) as employee_name"),
                ])
                ->orderBy('l.master_set_id')
                ->when($this->safeColumn('master_set_labor', 'sort_order'), fn($q) => $q->orderBy('l.sort_order'))
                ->orderBy('l.id')
                ->get();

            foreach ($laborRows as $row) {
                $sid = (int) $row->master_set_id;
                if (!isset($details[$sid])) {
                    continue;
                }

                $details[$sid]['labor'][] = [
                    'id' => (int) $row->id,
                    'hours' => (float) ($row->hours ?? 0),
                    'hourly_rate' => (float) ($row->hourly_rate ?? 0),
                    'total' => (float) (($row->hours ?? 0) * ($row->hourly_rate ?? 0)),
                    'qualification_name' => $row->qualification_name ?? null,
                    'department_name' => $row->department_name ?? null,
                    'position_name' => $row->position_name ?? null,
                    'employee_name' => trim((string) ($row->employee_name ?? '')) ?: null,
                ];
            }

            foreach ($details as &$detail) {
                $detail['summary']['labor'] = count($detail['labor']);
            }
            unset($detail);
        }

        if (Schema::hasTable('master_set_tasks')) {
            $taskRows = DB::table('master_set_tasks as t')
                ->whereIn('t.master_set_id', $masterSetIds)
                ->select([
                    't.id',
                    't.master_set_id',
                    DB::raw($this->safeColumn('master_set_tasks', 'title') ? 't.title' : "CONCAT('Schritt #', t.id) as title"),
                    DB::raw($this->safeColumn('master_set_tasks', 'description') ? 't.description' : 'NULL as description'),
                    DB::raw($this->safeColumn('master_set_tasks', 'phase_name') ? 't.phase_name' : 'NULL as phase_name'),
                    DB::raw($this->safeColumn('master_set_tasks', 'stage_name') ? 't.stage_name' : 'NULL as stage_name'),
                    DB::raw($this->safeColumn('master_set_tasks', 'duration') ? 't.duration' : 'NULL as duration'),
                    DB::raw($this->safeColumn('master_set_tasks', 'duration_type') ? 't.duration_type' : 'NULL as duration_type'),
                    DB::raw($this->safeColumn('master_set_tasks', 'percent') ? 't.percent' : 'NULL as percent'),
                    DB::raw($this->safeColumn('master_set_tasks', 'hours') ? 't.hours' : 'NULL as hours'),
                    DB::raw($this->safeColumn('master_set_tasks', 'priority') ? 't.priority' : 'NULL as priority'),
                    DB::raw($this->safeColumn('master_set_tasks', 'sort_order') ? 't.sort_order' : '0 as sort_order'),
                ])
                ->orderBy('t.master_set_id')
                ->when($this->safeColumn('master_set_tasks', 'sort_order'), fn($q) => $q->orderBy('t.sort_order'))
                ->orderBy('t.id')
                ->get();

            $taskIds = $taskRows->pluck('id')->map(fn($v) => (int) $v)->values()->all();
            $taskLabors = [];

            if (!empty($taskIds) && Schema::hasTable('master_set_task_labors')) {
                $laborTaskRows = DB::table('master_set_task_labors as tl')
                    ->leftJoin('position_qualifications as q', 'q.id', '=', 'tl.qualification_id')
                    ->whereIn('tl.master_set_task_id', $taskIds)
                    ->select([
                        'tl.id',
                        'tl.master_set_task_id',
                        DB::raw($this->safeColumn('master_set_task_labors', 'hours') ? 'tl.hours' : '0 as hours'),
                        DB::raw($this->safeColumn('master_set_task_labors', 'rate') ? 'tl.rate' : '0 as rate'),
                        DB::raw($this->safeColumn('position_qualifications', 'name') ? 'q.name as qualification_name' : 'NULL as qualification_name'),
                    ])
                    ->orderBy('tl.id')
                    ->get();

                foreach ($laborTaskRows as $tl) {
                    $taskLabors[(int) $tl->master_set_task_id][] = [
                        'id' => (int) $tl->id,
                        'hours' => (float) ($tl->hours ?? 0),
                        'rate' => (float) ($tl->rate ?? 0),
                        'qualification_name' => $tl->qualification_name ?? null,
                    ];
                }
            }

            foreach ($taskRows as $row) {
                $sid = (int) $row->master_set_id;
                if (!isset($details[$sid])) {
                    continue;
                }

                $details[$sid]['tasks'][] = [
                    'id' => (int) $row->id,
                    'title' => $row->title ?: ('Schritt #' . (int) $row->id),
                    'description' => $row->description ?? null,
                    'phase_name' => $row->phase_name ?? null,
                    'stage_name' => $row->stage_name ?? null,
                    'duration' => $row->duration ?? null,
                    'duration_type' => $row->duration_type ?? null,
                    'percent' => $row->percent ?? null,
                    'hours' => $row->hours ?? null,
                    'priority' => $row->priority ?? null,
                    'sort_order' => (int) ($row->sort_order ?? 0),
                    'labor' => $taskLabors[(int) $row->id] ?? [],
                ];
            }

            foreach ($details as &$detail) {
                $detail['summary']['tasks'] = count($detail['tasks']);
            }
            unset($detail);
        }

        return $details;
    }

    private function plannerMasterSetGroupSources(PlannerPlan $plan, PlannerItem $item): array
    {
        if (!Schema::hasTable('master_set_groups') || !Schema::hasTable('master_set_group_master_set')) {
            return [];
        }

        $project = $this->planProject($plan);

        $makeQuery = function (bool $filtered) use ($project) {
            $q = DB::table('master_set_groups as msg')
                ->leftJoin('master_set_group_master_set as pivot', 'pivot.master_set_group_id', '=', 'msg.id')
                ->leftJoin('master_sets as ms', 'ms.id', '=', 'pivot.master_set_id')
                ->when($this->safeColumn('master_set_groups', 'deleted_at'), fn($qq) => $qq->whereNull('msg.deleted_at'))
                ->when($this->safeColumn('master_sets', 'deleted_at'), fn($qq) => $qq->where(function ($w) {
                    $w->whereNull('ms.deleted_at')->orWhereNull('ms.id');
                }));

            if ($filtered && $project && !empty($project->product_id) && $this->safeColumn('master_set_groups', 'article_group_id')) {
                $q->where('msg.article_group_id', (int) $project->product_id);
            }

            return $q;
        };

        $select = [
            'msg.id',
            'msg.name',
            DB::raw($this->safeColumn('master_set_groups', 'description') ? 'MAX(msg.description) as description' : 'NULL as description'),
            DB::raw($this->safeColumn('master_set_groups', 'color') ? 'MAX(msg.color) as color' : 'NULL as color'),
            DB::raw($this->safeColumn('master_set_groups', 'article_group_id') ? 'MAX(msg.article_group_id) as article_group_id' : 'NULL as article_group_id'),
            DB::raw('GROUP_CONCAT(DISTINCT ms.id ORDER BY ms.name SEPARATOR ",") as master_set_ids'),
            DB::raw('COUNT(DISTINCT ms.id) as master_set_count'),
        ];

        $groups = $makeQuery(true)
            ->select($select)
            ->groupBy('msg.id', 'msg.name')
            ->orderBy('msg.name')
            ->limit(120)
            ->get();

        if ($groups->isEmpty()) {
            $groups = $makeQuery(false)
                ->select($select)
                ->groupBy('msg.id', 'msg.name')
                ->orderBy('msg.name')
                ->limit(120)
                ->get();
        }

        $allMasterSetIds = [];
        foreach ($groups as $group) {
            foreach (array_filter(array_map('intval', explode(',', (string) ($group->master_set_ids ?? '')))) as $id) {
                $allMasterSetIds[] = $id;
            }
        }

        $detailsBySetId = $this->plannerMasterSetDetailsPayload($allMasterSetIds);

        return $groups->map(function ($group) use ($detailsBySetId) {
            $setIds = array_values(array_filter(array_map('intval', explode(',', (string) ($group->master_set_ids ?? '')))));
            $sets = [];
            foreach ($setIds as $id) {
                if (isset($detailsBySetId[$id])) {
                    $sets[] = $detailsBySetId[$id];
                }
            }

            $componentCount = collect($sets)->sum(fn($s) => (int) ($s['summary']['components'] ?? 0));
            $laborCount = collect($sets)->sum(fn($s) => (int) ($s['summary']['labor'] ?? 0));
            $taskCount = collect($sets)->sum(fn($s) => (int) ($s['summary']['tasks'] ?? 0));

            return [
                'source_type' => 'master_set_group',
                'source_id' => (int) $group->id,
                'origin_type' => 'master_set_group',
                'group_id' => (int) $group->id,
                'name' => $group->name ?: ('Gruppen Set #' . (int) $group->id),
                'description' => $group->description ?? null,
                'color' => $group->color ?? null,
                'article_group_id' => $group->article_group_id ? (int) $group->article_group_id : null,
                'master_set_count' => (int) ($group->master_set_count ?? count($sets)),
                'component_count' => $componentCount,
                'labor_count' => $laborCount,
                'task_count' => $taskCount,
                'qty' => 1,
                'unit' => 'Gruppe',
                'measure' => 'Gruppe',
                'unit_price' => 0,
                'purchase_price' => 0,
                'active' => true,
                'group_detail' => [
                    'type' => 'master_set_group',
                    'id' => (int) $group->id,
                    'name' => $group->name ?: ('Gruppen Set #' . (int) $group->id),
                    'description' => $group->description ?? null,
                    'color' => $group->color ?? null,
                    'summary' => [
                        'master_sets' => count($sets),
                        'components' => $componentCount,
                        'labor' => $laborCount,
                        'tasks' => $taskCount,
                    ],
                    'master_sets' => $sets,
                ],
            ];
        })->values()->all();
    }

    private function plannerMasterSetMaterialSources(PlannerPlan $plan, PlannerItem $item): array
    {
        if (!Schema::hasTable('master_sets')) {
            return [];
        }

        $project = $this->planProject($plan);
        $masterSetIds = [];

        if (($item->source_type ?? null) === 'master_set' && (int) ($item->source_id ?? 0) > 0) {
            $masterSetIds[] = (int) $item->source_id;
        }

        if (Schema::hasColumn('planner_items', 'master_set_id') && !empty($item->master_set_id)) {
            $masterSetIds[] = (int) $item->master_set_id;
        }

        $componentRows = collect();

        if (Schema::hasTable('master_set_components')) {
            $makeQuery = function (bool $filtered) use ($project, $item, $masterSetIds) {
                $q = DB::table('master_set_components as c')
                    ->join('master_sets as ms', 'ms.id', '=', 'c.master_set_id')
                    ->leftJoin('products as p', 'p.id', '=', 'c.product_id')
                    ->leftJoin('distributors as d', 'd.id', '=', 'c.distributor_id')
                    ->when($this->safeColumn('master_sets', 'deleted_at'), fn($qq) => $qq->whereNull('ms.deleted_at'))
                    ->when($this->safeColumn('master_set_components', 'deleted_at'), fn($qq) => $qq->whereNull('c.deleted_at'));

                if ($filtered) {
                    $q->where(function ($w) use ($project, $item, $masterSetIds) {
                        $hasAny = false;

                        if (!empty($masterSetIds)) {
                            $w->whereIn('c.master_set_id', array_values(array_unique($masterSetIds)));
                            $hasAny = true;
                        }

                        if ($project && !empty($project->product_id) && $this->safeColumn('master_sets', 'article_group_id')) {
                            $method = $hasAny ? 'orWhere' : 'where';
                            $w->{$method}('ms.article_group_id', (int) $project->product_id);
                            $hasAny = true;
                        }

                        if (($item->source_type ?? null) === 'phase_activity' && !empty($item->source_id) && $this->safeColumn('master_sets', 'phase_activity_id')) {
                            $method = $hasAny ? 'orWhere' : 'where';
                            $w->{$method}('ms.phase_activity_id', (int) $item->source_id);
                            $hasAny = true;
                        }

                        if (!$hasAny) {
                            $w->whereRaw('1 = 1');
                        }
                    });
                }

                return $q;
            };

            $select = [
                'c.id',
                'c.master_set_id',
                'c.product_id',
                DB::raw($this->safeColumn('master_set_components', 'article_no') ? 'c.article_no' : 'NULL as article_no'),
                DB::raw($this->safeColumn('master_set_components', 'distributor_article_no') ? 'c.distributor_article_no' : 'NULL as distributor_article_no'),
                DB::raw($this->safeColumn('master_set_components', 'distributor_id') ? 'c.distributor_id' : 'NULL as distributor_id'),
                DB::raw($this->safeColumn('master_set_components', 'distributor_price_id') ? 'c.distributor_price_id' : 'NULL as distributor_price_id'),
                DB::raw($this->safeColumn('master_set_components', 'unit_price') ? 'c.unit_price' : '0 as unit_price'),
                DB::raw($this->safeColumn('master_set_components', 'purchase_price') ? 'c.purchase_price' : '0 as purchase_price'),
                DB::raw($this->safeColumn('master_set_components', 'qty') ? 'c.qty' : '1 as qty'),
                DB::raw($this->safeColumn('master_set_components', 'measure') ? 'c.measure' : 'NULL as measure'),
                DB::raw($this->safeColumn('master_set_components', 'price_unit') ? 'c.price_unit' : 'NULL as price_unit'),
                DB::raw($this->safeColumn('master_set_components', 'availability') ? 'c.availability' : '1 as availability'),
                DB::raw($this->safeColumn('master_set_components', 'type') ? 'c.type' : 'NULL as type'),
                DB::raw($this->safeColumn('master_set_components', 'description') ? 'c.description' : 'NULL as description'),
                'ms.name as master_set_name',
                DB::raw($this->safeColumn('products', 'product') ? 'p.product as product_name' : 'NULL as product_name'),
                DB::raw($this->safeColumn('products', 'model') ? 'p.model as product_model' : 'NULL as product_model'),
                DB::raw($this->safeColumn('products', 'sku') ? 'p.sku' : 'NULL as sku'),
                DB::raw($this->safeColumn('distributors', 'name') ? 'd.name as distributor_name' : 'NULL as distributor_name'),
                DB::raw($this->safeColumn('distributors', 'short_name') ? 'd.short_name as distributor_short_name' : 'NULL as distributor_short_name'),
                DB::raw(Schema::hasTable('product_images') ? '(SELECT image FROM product_images WHERE product_images.product_id = c.product_id ORDER BY id ASC LIMIT 1) as image' : 'NULL as image'),
            ];

            $componentRows = $makeQuery(true)
                ->select($select)
                ->orderBy('ms.name')
                ->when($this->safeColumn('master_set_components', 'sort_order'), fn($qq) => $qq->orderBy('c.sort_order'))
                ->orderBy('c.id')
                ->limit(700)
                ->get();

            // If the current project filter finds nothing, show the full MasterSet library.
            if ($componentRows->isEmpty()) {
                $componentRows = $makeQuery(false)
                    ->select($select)
                    ->orderBy('ms.name')
                    ->when($this->safeColumn('master_set_components', 'sort_order'), fn($qq) => $qq->orderBy('c.sort_order'))
                    ->orderBy('c.id')
                    ->limit(700)
                    ->get();
            }
        }

        $mapped = $componentRows->map(function ($row) {
            $name = trim((string) ($row->product_name ?? ''));

            if ($name === '') {
                $name = trim((string) ($row->description ?? ''));
            }

            if ($name === '') {
                $name = 'MasterSet Material #' . $row->id;
            }

            return [
                'source_type' => 'master_set_component',
                'source_id' => (int) $row->id,
                'origin_type' => 'master_set_predefined',
                'master_set_id' => (int) $row->master_set_id,
                'master_set_name' => $row->master_set_name,
                'product_id' => $row->product_id ? (int) $row->product_id : null,
                'name' => $name,
                'article_no' => $row->article_no,
                'distributor_article_no' => $row->distributor_article_no,
                'sku' => $row->sku,
                'qty' => (float) ($row->qty ?? 1),
                'unit' => $row->measure ?? $row->price_unit ?? 'Stk',
                'measure' => $row->measure ?? $row->price_unit ?? 'Stk',
                'unit_price' => (float) ($row->unit_price ?? 0),
                'purchase_price' => (float) ($row->purchase_price ?? 0),
                'distributor_id' => $row->distributor_id ? (int) $row->distributor_id : null,
                'distributor_price_id' => $row->distributor_price_id ? (int) $row->distributor_price_id : null,
                'distributor_name' => $row->distributor_short_name ?: $row->distributor_name,
                'image_url' => $this->plannerProductImage($row->image),
                'active' => true,
            ];
        })->values();

        // If a MasterSet has no components yet, still show the set itself so the tab is not empty.
        if ($mapped->isEmpty()) {
            $q = DB::table('master_sets as ms')
                ->when($this->safeColumn('master_sets', 'deleted_at'), fn($qq) => $qq->whereNull('ms.deleted_at'));

            if ($project && !empty($project->product_id) && $this->safeColumn('master_sets', 'article_group_id')) {
                $q->where('ms.article_group_id', (int) $project->product_id);
            }

            $sets = $q->select(['ms.id', 'ms.name', DB::raw($this->safeColumn('master_sets', 'description') ? 'ms.description' : 'NULL as description')])
                ->orderBy('ms.name')
                ->limit(200)
                ->get();

            if ($sets->isEmpty()) {
                $sets = DB::table('master_sets as ms')
                    ->when($this->safeColumn('master_sets', 'deleted_at'), fn($qq) => $qq->whereNull('ms.deleted_at'))
                    ->select(['ms.id', 'ms.name', DB::raw($this->safeColumn('master_sets', 'description') ? 'ms.description' : 'NULL as description')])
                    ->orderBy('ms.name')
                    ->limit(200)
                    ->get();
            }

            $mapped = $sets->map(fn($set) => [
                'source_type' => 'master_set',
                'source_id' => (int) $set->id,
                'origin_type' => 'master_set_predefined',
                'master_set_id' => (int) $set->id,
                'master_set_name' => $set->name,
                'product_id' => null,
                'name' => $set->name ?: ('MasterSet #' . $set->id),
                'article_no' => null,
                'qty' => 1,
                'unit' => 'Set',
                'measure' => 'Set',
                'unit_price' => 0,
                'purchase_price' => 0,
                'distributor_name' => null,
                'image_url' => null,
                'active' => true,
            ])->values();
        }

        $detailIds = collect($mapped)->pluck('master_set_id')->filter()->unique()->values()->all();
        $detailsBySetId = $this->plannerMasterSetDetailsPayload($detailIds);

        $mapped = collect($mapped)->map(function ($row) use ($detailsBySetId) {
            $masterSetId = (int) ($row['master_set_id'] ?? 0);

            if ($masterSetId > 0 && isset($detailsBySetId[$masterSetId])) {
                $row['master_set_detail'] = $detailsBySetId[$masterSetId];
            }

            return $row;
        })->values();

        return $mapped->all();
    }

    private function plannerSearchProductsPayload(string $q = ''): array
    {
        if (!Schema::hasTable('products')) {
            return [];
        }

        $query = DB::table('products as p')
            ->select([
                'p.id',
                'p.product',
                'p.model',
                'p.article_no',
                'p.sku',
                'p.ean',
                'p.measure_unit',
                'p.price_unit',
                DB::raw("(SELECT image FROM product_images WHERE product_images.product_id = p.id ORDER BY id ASC LIMIT 1) as image"),
                DB::raw("(SELECT dp.id FROM distributor_prices dp WHERE dp.product_id = p.id ORDER BY COALESCE(dp.price_date, dp.created_at) DESC, dp.id DESC LIMIT 1) as distributor_price_id"),
                DB::raw("(SELECT dp.distributor_id FROM distributor_prices dp WHERE dp.product_id = p.id ORDER BY COALESCE(dp.price_date, dp.created_at) DESC, dp.id DESC LIMIT 1) as distributor_id"),
                DB::raw("(SELECT dp.article_no FROM distributor_prices dp WHERE dp.product_id = p.id ORDER BY COALESCE(dp.price_date, dp.created_at) DESC, dp.id DESC LIMIT 1) as distributor_article_no"),
                DB::raw("(SELECT dp.price FROM distributor_prices dp WHERE dp.product_id = p.id ORDER BY COALESCE(dp.price_date, dp.created_at) DESC, dp.id DESC LIMIT 1) as unit_price"),
                DB::raw("(SELECT dp.purchase_price FROM distributor_prices dp WHERE dp.product_id = p.id ORDER BY COALESCE(dp.price_date, dp.created_at) DESC, dp.id DESC LIMIT 1) as purchase_price"),
                DB::raw("(SELECT d.name FROM distributors d WHERE d.id = (SELECT dp.distributor_id FROM distributor_prices dp WHERE dp.product_id = p.id ORDER BY COALESCE(dp.price_date, dp.created_at) DESC, dp.id DESC LIMIT 1) LIMIT 1) as distributor_name"),
            ])
            ->when($this->safeColumn('products', 'status'), function ($qq) {
                $qq->where(function ($w) {
                    $w->whereNull('p.status')
                        ->orWhereRaw('LOWER(COALESCE(p.status, "")) NOT IN (?, ?, ?)', ['deleted', 'inactive', 'archived']);
                });
            });

        if ($q !== '') {
            $like = '%' . mb_strtolower($q) . '%';

            $query->where(function ($w) use ($like) {
                foreach (['product', 'model', 'article_no', 'sku', 'ean'] as $column) {
                    if ($this->safeColumn('products', $column)) {
                        $w->orWhereRaw('LOWER(COALESCE(p.' . $column . ', "")) LIKE ?', [$like]);
                    }
                }
            });
        }

        return $query
            ->orderBy('p.product')
            ->limit(60)
            ->get()
            ->map(fn($row) => [
                'source_type' => 'product',
                'source_id' => (int) $row->id,
                'origin_type' => 'product_library',
                'product_id' => (int) $row->id,
                'id' => (int) $row->id,
                'name' => trim((string) ($row->product ?? '') . ' ' . (string) ($row->model ?? '')) ?: ('Produkt #' . $row->id),
                'product' => $row->product,
                'model' => $row->model,
                'article_no' => $row->article_no,
                'distributor_article_no' => $row->distributor_article_no,
                'sku' => $row->sku,
                'ean' => $row->ean,
                'qty' => 1,
                'unit' => $row->price_unit ?? $row->measure_unit ?? 'Stk',
                'measure' => $row->measure_unit ?? $row->price_unit ?? 'Stk',
                'unit_price' => (float) ($row->unit_price ?? 0),
                'purchase_price' => (float) ($row->purchase_price ?? 0),
                'distributor_id' => $row->distributor_id ? (int) $row->distributor_id : null,
                'distributor_price_id' => $row->distributor_price_id ? (int) $row->distributor_price_id : null,
                'distributor_name' => $row->distributor_name,
                'image_url' => $this->plannerProductImage($row->image),
                'active' => true,
            ])
            ->values()
            ->all();
    }

    public function materialSources(Request $request, PlannerPlan $plan, PlannerItem $item)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        $deal = $this->plannerDealMaterialSources($plan);
        $masterSets = $this->plannerMasterSetMaterialSources($plan, $item);
        $masterGroups = $this->plannerMasterSetGroupSources($plan, $item);
        $products = $this->plannerSearchProductsPayload(trim((string) $request->query('q', '')));

        return response()->json([
            'ok' => true,
            'data' => [
                'current' => $this->plannerLoadCurrentMaterials($item),
                'current_summary' => $this->pmoPlannerMaterialSummary($this->plannerLoadCurrentMaterials($item)),
                'deal' => $deal,
                'master_sets' => $masterSets,
                'master_groups' => $masterGroups,
                'products' => $products,
                'has_deal' => !empty($deal),
                'meta' => [
                    'plan_id' => (int) $plan->id,
                    'planner_item_id' => (int) $item->id,
                    'project_id' => (int) ($plan->project_id ?? 0),
                    'counts' => [
                        'deal' => count($deal),
                        'master_sets' => count($masterSets),
                        'master_groups' => count($masterGroups),
                        'products' => count($products),
                    ],
                ],
            ],
        ]);
    }

    public function searchPlannerMaterialProducts(Request $request, PlannerPlan $plan, PlannerItem $item)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        $q = trim((string) $request->query('q', ''));

        return response()->json([
            'ok' => true,
            'data' => [
                'products' => $this->plannerSearchProductsPayload($q),
            ],
        ]);
    }

    private function plannerMaterialInsertPayload(PlannerItem $item, array $data, ?PlannerPlan $plan = null): array
    {
        $qty = (float) ($data['qty'] ?? $data['quantity'] ?? 1);
        $unitPrice = (float) ($data['unit_price'] ?? $data['price'] ?? 0);
        $purchasePrice = (float) ($data['purchase_price'] ?? $data['ek'] ?? 0);
        $originType = (string) ($data['origin_type'] ?? 'manual');
        $employeeId = $this->authEmployeeId();

        $plan = $plan ?: PlannerPlan::query()->find((int) $item->plan_id);
        $context = $plan ? $this->plannerContextColumns($plan, $item) : [
            'plan_id' => (int) ($item->plan_id ?? 0),
            'planner_plan_id' => (int) ($item->plan_id ?? 0),
            'planner_item_id' => (int) $item->id,
            'lead_product_list_id' => null,
            'project_id' => null,
            'customer_id' => null,
            'alternative_id' => null,
            'product_id' => null,
            'article_group' => null,
        ];

        return array_merge($context, [
            'planner_item_id' => (int) $item->id,
            'source_item_type' => $item->source_type ?? null,
            'source_item_id' => $item->source_id ?? null,
            'product_id' => !empty($data['product_id']) ? (int) $data['product_id'] : ($context['product_id'] ?? null),
            'article_group' => !empty($data['article_group']) ? (int) $data['article_group'] : (!empty($data['product_id']) ? (int) $data['product_id'] : ($context['article_group'] ?? null)),
            'source_type' => $data['source_type'] ?? 'manual',
            'source_id' => !empty($data['source_id']) ? (int) $data['source_id'] : null,
            'origin_type' => $originType,
            'name' => $data['name'] ?? $data['title'] ?? 'Material',
            'title' => $data['name'] ?? $data['title'] ?? 'Material',
            'material_name' => $data['name'] ?? $data['title'] ?? 'Material',
            'article_no' => $data['article_no'] ?? null,
            'distributor_article_no' => $data['distributor_article_no'] ?? null,
            'sku' => $data['sku'] ?? null,
            'qty' => $qty,
            'quantity' => $qty,
            'unit' => $data['unit'] ?? $data['measure'] ?? 'Stk',
            'measure' => $data['measure'] ?? $data['unit'] ?? 'Stk',
            'measure_unit' => $data['measure_unit'] ?? $data['measure'] ?? $data['unit'] ?? 'Stk',
            'unit_price' => $unitPrice,
            'price' => $unitPrice,
            'purchase_price' => $purchasePrice,
            'ek' => $purchasePrice,
            'total_price' => $qty * $unitPrice,
            'distributor_id' => !empty($data['distributor_id']) ? (int) $data['distributor_id'] : null,
            'distributor_price_id' => !empty($data['distributor_price_id']) ? (int) $data['distributor_price_id'] : null,
            'distributor_name' => $data['distributor_name'] ?? $data['supplier'] ?? null,
            'supplier' => $data['supplier'] ?? $data['distributor_name'] ?? null,
            'image_url' => $data['image_url'] ?? $data['img'] ?? null,
            'img' => $data['img'] ?? $data['image_url'] ?? null,
            'active' => (bool) ($data['active'] ?? true) ? 1 : 0,
            'is_active' => (bool) ($data['active'] ?? true) ? 1 : 0,
            'requested_by_employee_id' => in_array($originType, ['employee_request', 'asked_by_employee'], true) ? $employeeId : null,
            'requested_at' => in_array($originType, ['employee_request', 'asked_by_employee'], true) ? now() : null,
            'created_by_employee_id' => $employeeId,
            'created_by' => $employeeId ?? auth()->id(),
            'source_payload' => !empty($data['source_payload']) ? json_encode($data['source_payload']) : null,
            'material_group_uuid' => $data['material_group_uuid'] ?? null,
            'material_group_name' => $data['material_group_name'] ?? null,
            'material_scope' => $data['material_scope'] ?? null,
            'material_scope_employee_id' => !empty($data['material_scope_employee_id']) ? (int) $data['material_scope_employee_id'] : null,
            'group_uuid' => $data['material_group_uuid'] ?? null,
            'group_name' => $data['material_group_name'] ?? null,
            'group_scope' => $data['material_scope'] ?? null,
            'group_employee_id' => !empty($data['material_scope_employee_id']) ? (int) $data['material_scope_employee_id'] : null,
        ]);
    }

    private function plannerGroupMaterialTable(): ?string
    {
        return Schema::hasTable('planner_group_materials') ? 'planner_group_materials' : null;
    }

    private function plannerGroupMaterialItemIdsMatchQuery($query, int $itemId): void
    {
        $id = (string) $itemId;

        $query->where(function ($w) use ($itemId, $id) {
            try {
                $w->whereJsonContains('item_ids', $itemId);
            } catch (\Throwable $e) {
                // Some older MySQL/MariaDB setups do not support JSON_CONTAINS on text columns.
                $w->whereRaw('1 = 0');
            }

            $w->orWhere('item_ids', '[' . $id . ']')
                ->orWhere('item_ids', 'like', '[' . $id . ',%')
                ->orWhere('item_ids', 'like', '%,' . $id . ',%')
                ->orWhere('item_ids', 'like', '%,' . $id . ']')
                ->orWhere('item_ids', 'like', '%"' . $id . '"%');
        });
    }

    private function plannerGroupMaterialRowPayload(object $row): array
    {
        $qty = (float) ($row->qty ?? $row->quantity ?? 1);
        $unitPrice = (float) ($row->unit_price ?? $row->price ?? $row->vk ?? 0);
        $purchasePrice = (float) ($row->purchase_price ?? $row->ek ?? 0);
        $itemIds = $this->decodeJson($row->item_ids ?? null);

        if (!is_array($itemIds)) {
            $itemIds = [];
        }

        $itemIds = array_values(array_unique(array_filter(array_map('intval', $itemIds))));

        $payload = $this->plannerMaterialRowPayload($row);

        $payload['id'] = (int) $row->id;
        $payload['planner_item_id'] = null;
        $payload['item_ids'] = $itemIds;
        $payload['linked_item_ids'] = $itemIds;
        $payload['linked_item_count'] = count($itemIds);
        $payload['material_group_uuid'] = $row->material_group_uuid ?? $row->group_uuid ?? null;
        $payload['group_uuid'] = $row->material_group_uuid ?? $row->group_uuid ?? null;
        $payload['material_group_name'] = $row->material_group_name ?? $row->group_name ?? $row->name ?? 'Gruppenmaterial';
        $payload['group_name'] = $row->material_group_name ?? $row->group_name ?? $row->name ?? 'Gruppenmaterial';
        $payload['material_scope'] = $row->material_scope ?? $row->group_scope ?? 'employee_period';
        $payload['employee_id'] = $row->material_scope_employee_id ?? $row->group_employee_id ?? $row->employee_id ?? null;
        $payload['material_scope_employee_id'] = $payload['employee_id'];
        $payload['scope_date_from'] = $row->scope_date_from ?? null;
        $payload['scope_date_to'] = $row->scope_date_to ?? null;
        $payload['scope_mode'] = $row->scope_mode ?? null;
        $payload['period_label'] = $row->period_label ?? null;
        $payload['qty'] = $qty;
        $payload['quantity'] = $qty;
        $payload['unit_price'] = $unitPrice;
        $payload['purchase_price'] = $purchasePrice;
        $payload['total_price'] = (float) ($row->total_price ?? ($qty * $unitPrice));
        $payload['is_shared_group_material'] = true;
        $payload['is_added'] = true;
        $payload['is_request'] = false;
        $payload['is_request_open'] = false;

        return $payload;
    }

    private function pmoLoadPlanGroupMaterials(PlannerPlan $plan, Carbon $from, Carbon $to): array
    {
        $rows = collect();

        $table = $this->plannerGroupMaterialTable();

        if ($table) {
            $q = DB::table($table)
                ->where('plan_id', (int) $plan->id)
                ->when($this->safeColumn($table, 'deleted_at'), fn($qq) => $qq->whereNull('deleted_at'))
                ->when($this->safeColumn($table, 'active'), fn($qq) => $qq->where(function ($w) {
                    $w->whereNull('active')->orWhere('active', 1);
                }))
                ->when($this->safeColumn($table, 'scope_date_from'), fn($qq) => $qq->where(function ($w) use ($to) {
                    $w->whereNull('scope_date_from')->orWhereDate('scope_date_from', '<=', $to->toDateString());
                }))
                ->when($this->safeColumn($table, 'scope_date_to'), fn($qq) => $qq->where(function ($w) use ($from) {
                    $w->whereNull('scope_date_to')->orWhereDate('scope_date_to', '>=', $from->toDateString());
                }));

            $rows = $rows->merge($q->orderByDesc('id')->get()->map(fn($row) => $this->plannerGroupMaterialRowPayload($row)));
        }

        // Backward compatibility: older version duplicated group material into planner_item_materials.
        // We now treat those old rows as shared rows and de-duplicate them by material_group_uuid.
        if (Schema::hasTable('planner_item_materials') && $this->safeColumn('planner_item_materials', 'material_group_uuid')) {
            $legacyRows = DB::table('planner_item_materials as pim')
                ->join('planner_items as pi', 'pi.id', '=', 'pim.planner_item_id')
                ->where('pi.plan_id', (int) $plan->id)
                ->whereNotNull('pim.material_group_uuid')
                ->where('pim.material_group_uuid', '<>', '')
                ->when($this->safeColumn('planner_item_materials', 'deleted_at'), fn($q) => $q->whereNull('pim.deleted_at'))
                ->select('pim.*')
                ->get()
                ->groupBy('material_group_uuid')
                ->map(function ($group) {
                    $first = $group->first();
                    $first->item_ids = $group->pluck('planner_item_id')->map(fn($id) => (int) $id)->unique()->values()->all();
                    return $this->plannerGroupMaterialRowPayload($first);
                })
                ->values();

            $rows = $rows->merge($legacyRows);
        }

        return $rows
            ->unique(fn($row) => (string) ($row['material_group_uuid'] ?? $row['group_uuid'] ?? ('row-' . ($row['id'] ?? '0'))))
            ->values()
            ->all();
    }

    private function pmoLoadPlannerGroupMaterialsForItem(int $plannerItemId): array
    {
        $rows = collect();

        $table = $this->plannerGroupMaterialTable();

        if ($table) {
            $q = DB::table($table)
                ->when($this->safeColumn($table, 'deleted_at'), fn($qq) => $qq->whereNull('deleted_at'));

            if ($this->safeColumn($table, 'item_ids')) {
                $this->plannerGroupMaterialItemIdsMatchQuery($q, $plannerItemId);
            }

            $rows = $rows->merge($q->orderByDesc('id')->limit(80)->get()->map(fn($row) => $this->plannerGroupMaterialRowPayload($row)));
        }

        if (Schema::hasTable('planner_item_materials') && $this->safeColumn('planner_item_materials', 'material_group_uuid')) {
            $legacy = DB::table('planner_item_materials')
                ->where('planner_item_id', $plannerItemId)
                ->whereNotNull('material_group_uuid')
                ->where('material_group_uuid', '<>', '')
                ->when($this->safeColumn('planner_item_materials', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->orderByDesc('id')
                ->get()
                ->map(function ($row) {
                    $row->item_ids = [$row->planner_item_id];
                    return $this->plannerGroupMaterialRowPayload($row);
                });

            $rows = $rows->merge($legacy);
        }

        return $rows
            ->unique(fn($row) => (string) ($row['material_group_uuid'] ?? $row['group_uuid'] ?? ('row-' . ($row['id'] ?? '0'))))
            ->values()
            ->all();
    }

    public function storePlanGroupMaterial(Request $request, PlannerPlan $plan)
    {
        $table = $this->plannerGroupMaterialTable();

        if (!$table) {
            return response()->json([
                'ok' => false,
                'message' => 'planner_group_materials table does not exist. Run the supplied migration first. Group material must be saved once, not copied into every job.',
            ], 422);
        }

        $data = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer', 'min:1'],
            'employee_id' => ['nullable', 'integer', 'min:1'],
            'group_name' => ['nullable', 'string', 'max:255'],
            'scope_date_from' => ['nullable', 'date'],
            'scope_date_to' => ['nullable', 'date'],
            'scope_mode' => ['nullable', 'string', 'max:50'],
            'period_label' => ['nullable', 'string', 'max:255'],
            'product_id' => ['nullable', 'integer'],
            'source_type' => ['nullable', 'string', 'max:80'],
            'source_id' => ['nullable', 'integer'],
            'origin_type' => ['nullable', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:255'],
            'article_no' => ['nullable', 'string', 'max:255'],
            'distributor_article_no' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'qty' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:80'],
            'measure' => ['nullable', 'string', 'max:80'],
            'unit_price' => ['nullable', 'numeric'],
            'purchase_price' => ['nullable', 'numeric'],
            'distributor_id' => ['nullable', 'integer'],
            'distributor_price_id' => ['nullable', 'integer'],
            'distributor_name' => ['nullable', 'string', 'max:255'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'string'],
            'img' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);

        $itemIds = collect($data['item_ids'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $items = PlannerItem::query()
            ->where('plan_id', (int) $plan->id)
            ->whereIn('id', $itemIds)
            ->get();

        if ($items->isEmpty()) {
            return response()->json([
                'ok' => false,
                'message' => 'Keine gültigen Jobs für dieses Gruppenmaterial gefunden.',
            ], 422);
        }

        $firstItem = $items->first();
        $context = $this->plannerItemCustomerContext($plan, $firstItem);
        $employeeId = !empty($data['employee_id']) ? (int) $data['employee_id'] : null;
        $employeeName = null;

        if ($employeeId && Schema::hasTable('employees')) {
            $employee = DB::table('employees')->where('id', $employeeId)->first();
            $employeeName = $employee ? trim((string) (($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))) : null;
        }

        $qty = (float) ($data['qty'] ?? 1);
        $unitPrice = (float) ($data['unit_price'] ?? $data['price'] ?? 0);
        $purchasePrice = (float) ($data['purchase_price'] ?? $data['ek'] ?? 0);
        $groupUuid = (string) Str::uuid();
        $groupName = trim((string) ($data['group_name'] ?? '')) ?: ('Gruppenmaterial · ' . ($data['name'] ?? 'Material'));
        $originType = $data['origin_type'] ?? 'group_material';
        $sourceType = $data['source_type'] ?? 'group_material';

        $insertPayload = [
            'plan_id' => (int) $plan->id,
            'planner_plan_id' => (int) $plan->id,
            'lead_product_list_id' => $context['lead_product_list_id'] ?? $plan->project_id,
            'project_id' => $context['lead_product_list_id'] ?? $plan->project_id,
            'customer_id' => $context['customer_id'] ?? $plan->customer_id,
            'alternative_id' => $context['alternative_id'] ?? null,
            'product_id' => !empty($data['product_id']) ? (int) $data['product_id'] : ($context['product_id'] ?? null),
            'article_group' => !empty($data['product_id']) ? (int) $data['product_id'] : ($context['article_group'] ?? null),
            'employee_id' => $employeeId,
            'employee_name' => $employeeName,
            'item_ids' => json_encode(array_values($itemIds)),
            'linked_item_ids' => json_encode(array_values($itemIds)),
            'material_group_uuid' => $groupUuid,
            'group_uuid' => $groupUuid,
            'material_group_name' => $groupName,
            'group_name' => $groupName,
            'material_scope' => 'employee_period',
            'group_scope' => 'employee_period',
            'material_scope_employee_id' => $employeeId,
            'group_employee_id' => $employeeId,
            'scope_date_from' => $data['scope_date_from'] ?? null,
            'scope_date_to' => $data['scope_date_to'] ?? null,
            'scope_mode' => $data['scope_mode'] ?? null,
            'period_label' => $data['period_label'] ?? null,
            'source_type' => $sourceType,
            'source_id' => !empty($data['source_id']) ? (int) $data['source_id'] : null,
            'origin_type' => $originType,
            'name' => $data['name'] ?? 'Material',
            'title' => $data['name'] ?? 'Material',
            'material_name' => $data['name'] ?? 'Material',
            'article_no' => $data['article_no'] ?? null,
            'distributor_article_no' => $data['distributor_article_no'] ?? null,
            'sku' => $data['sku'] ?? null,
            'qty' => $qty,
            'quantity' => $qty,
            'unit' => $data['unit'] ?? $data['measure'] ?? 'Stk',
            'measure' => $data['measure'] ?? $data['unit'] ?? 'Stk',
            'measure_unit' => $data['measure'] ?? $data['unit'] ?? 'Stk',
            'unit_price' => $unitPrice,
            'price' => $unitPrice,
            'purchase_price' => $purchasePrice,
            'ek' => $purchasePrice,
            'total_price' => $qty * $unitPrice,
            'distributor_id' => !empty($data['distributor_id']) ? (int) $data['distributor_id'] : null,
            'distributor_price_id' => !empty($data['distributor_price_id']) ? (int) $data['distributor_price_id'] : null,
            'distributor_name' => $data['distributor_name'] ?? $data['supplier'] ?? null,
            'supplier' => $data['supplier'] ?? $data['distributor_name'] ?? null,
            'image_url' => $data['image_url'] ?? $data['img'] ?? null,
            'img' => $data['img'] ?? $data['image_url'] ?? null,
            'active' => (bool) ($data['active'] ?? true) ? 1 : 0,
            'is_active' => (bool) ($data['active'] ?? true) ? 1 : 0,
            'created_by_employee_id' => $this->authEmployeeId(),
            'created_by' => $this->authEmployeeId() ?? auth()->id(),
            'source_payload' => json_encode([
                'source_type' => $sourceType,
                'source_id' => $data['source_id'] ?? null,
                'linked_item_titles' => $items->pluck('title')->values()->all(),
            ]),
        ];

        $id = $this->plannerInsertRow($table, $insertPayload);
        $row = DB::table($table)->where('id', $id)->first();
        $groupMaterial = $row ? $this->plannerGroupMaterialRowPayload($row) : null;

        $updatedItems = $items->map(function ($targetItem) {
            $materials = $this->pmoLoadPlannerMaterials((int) $targetItem->id);
            $sharedMaterials = $this->pmoLoadPlannerGroupMaterialsForItem((int) $targetItem->id);

            return [
                'id' => (int) $targetItem->id,
                'planner_item_id' => (int) $targetItem->id,
                'materials' => $materials,
                'material_summary' => $this->pmoPlannerMaterialSummary($materials),
                'shared_materials' => $sharedMaterials,
                'shared_material_summary' => $this->pmoPlannerMaterialSummary($sharedMaterials),
            ];
        })->values()->all();

        return response()->json([
            'ok' => true,
            'message' => 'Gruppenmaterial wurde einmalig für diesen Zeitraum gespeichert und nur mit den Jobs verknüpft.',
            'group_material' => $groupMaterial,
            'group' => [
                'uuid' => $groupUuid,
                'name' => $groupName,
                'employee_id' => $employeeId,
                'item_count' => count($updatedItems),
                'inserted_count' => 1,
                'stored_once' => true,
            ],
            'items' => $updatedItems,
        ]);
    }

    public function storeItemMaterial(Request $request, PlannerPlan $plan, PlannerItem $item)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        $table = $this->plannerMaterialTable();

        if (!$table) {
            return response()->json([
                'ok' => false,
                'message' => 'planner_item_materials table does not exist.',
            ], 422);
        }

        $data = $request->validate([
            'product_id' => ['nullable', 'integer'],
            'source_type' => ['nullable', 'string', 'max:80'],
            'source_id' => ['nullable', 'integer'],
            'origin_type' => ['nullable', 'string', 'max:80'],
            'name' => ['required', 'string', 'max:255'],
            'article_no' => ['nullable', 'string', 'max:255'],
            'distributor_article_no' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'qty' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:80'],
            'measure' => ['nullable', 'string', 'max:80'],
            'unit_price' => ['nullable', 'numeric'],
            'purchase_price' => ['nullable', 'numeric'],
            'distributor_id' => ['nullable', 'integer'],
            'distributor_price_id' => ['nullable', 'integer'],
            'distributor_name' => ['nullable', 'string', 'max:255'],
            'supplier' => ['nullable', 'string', 'max:255'],
            'image_url' => ['nullable', 'string'],
            'img' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);

        $id = $this->plannerInsertRow($table, $this->plannerMaterialInsertPayload($item, $data, $plan));
        $row = DB::table($table)->where('id', $id)->first();

        return response()->json([
            'ok' => true,
            'material' => $row ? $this->plannerMaterialRowPayload($row) : null,
        ]);
    }

    public function updateItemMaterial(Request $request, PlannerPlan $plan, PlannerItem $item, int $material)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        $table = $this->plannerMaterialTable();

        if (!$table) {
            return response()->json(['ok' => false, 'message' => 'planner_item_materials table does not exist.'], 422);
        }

        $row = DB::table($table)
            ->where('id', $material)
            ->where('planner_item_id', (int) $item->id)
            ->first();

        if (!$row) {
            return response()->json(['ok' => false, 'message' => 'Material wurde nicht gefunden.'], 404);
        }

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'qty' => ['nullable', 'numeric'],
            'unit' => ['nullable', 'string', 'max:80'],
            'measure' => ['nullable', 'string', 'max:80'],
            'unit_price' => ['nullable', 'numeric'],
            'purchase_price' => ['nullable', 'numeric'],
            'active' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'origin_type' => ['nullable', 'string', 'max:80'],
        ]);

        $updates = [];

        foreach (['name', 'origin_type'] as $key) {
            if (array_key_exists($key, $data)) {
                $updates[$key] = $data[$key];
            }
        }

        if (array_key_exists('name', $data)) {
            $updates['title'] = $data['name'];
            $updates['material_name'] = $data['name'];
        }

        if (array_key_exists('qty', $data)) {
            $updates['qty'] = (float) $data['qty'];
            $updates['quantity'] = (float) $data['qty'];
            $updates['total_price'] = (float) $data['qty'] * (float) ($row->unit_price ?? $row->price ?? 0);
        }

        if (array_key_exists('unit', $data)) {
            $updates['unit'] = $data['unit'];
            $updates['measure'] = $data['unit'];
            $updates['measure_unit'] = $data['unit'];
        }

        if (array_key_exists('measure', $data)) {
            $updates['measure'] = $data['measure'];
            $updates['measure_unit'] = $data['measure'];
        }

        if (array_key_exists('unit_price', $data)) {
            $updates['unit_price'] = (float) $data['unit_price'];
            $updates['price'] = (float) $data['unit_price'];
            $updates['total_price'] = (float) ($row->qty ?? $row->quantity ?? 1) * (float) $data['unit_price'];
        }

        if (array_key_exists('purchase_price', $data)) {
            $updates['purchase_price'] = (float) $data['purchase_price'];
            $updates['ek'] = (float) $data['purchase_price'];
        }

        if (array_key_exists('active', $data) || array_key_exists('is_active', $data)) {
            $active = (bool) ($data['active'] ?? $data['is_active']);
            $updates['active'] = $active ? 1 : 0;
            $updates['is_active'] = $active ? 1 : 0;
        }

        $this->plannerUpdateRow($table, $material, $updates);
        $fresh = DB::table($table)->where('id', $material)->first();

        return response()->json([
            'ok' => true,
            'material' => $fresh ? $this->plannerMaterialRowPayload($fresh) : null,
        ]);
    }
    public function updateItemMaterialRequestStatus(Request $request, PlannerPlan $plan, PlannerItem $item, int $materialRequest)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        if (!Schema::hasTable('planner_item_material_requests')) {
            return response()->json([
                'ok' => false,
                'message' => 'planner_item_material_requests table does not exist.',
            ], 422);
        }

        $data = $request->validate([
            'status' => ['required', 'string', Rule::in(['accepted', 'approved', 'rejected', 'declined'])],
            'response_status' => ['nullable', 'string', 'max:50'],
            'response_note' => ['nullable', 'string', 'max:5000'],
            'note' => ['nullable', 'string', 'max:5000'],
            'rejection_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $status = in_array(strtolower((string) $data['status']), ['accepted', 'approved'], true)
            ? 'accepted'
            : 'rejected';

        $employeeId = $this->authEmployeeId();
        $now = now();

        $rowQuery = DB::table('planner_item_material_requests')
            ->where('id', (int) $materialRequest)
            ->where('planner_item_id', (int) $item->id);

        if ($this->safeColumn('planner_item_material_requests', 'deleted_at')) {
            $rowQuery->whereNull('deleted_at');
        }

        $row = $rowQuery->first();

        if (!$row) {
            return response()->json([
                'ok' => false,
                'message' => 'Material-Anfrage wurde nicht gefunden.',
            ], 404);
        }

        DB::transaction(function () use ($status, $employeeId, $row, $item, $plan, $data, $now) {
            $note = $data['response_note'] ?? $data['rejection_note'] ?? $data['note'] ?? null;

            $updates = [
                'status' => $status,
                'request_status' => $status,
                'response_status' => $status,
                'response_note' => $note,
                'responded_by_employee_id' => $employeeId,
                'responded_at' => $now,
                'updated_at' => $now,
            ];

            if ($status === 'accepted') {
                $updates['accepted_by_employee_id'] = $employeeId;
                $updates['accepted_at'] = $now;
                $updates['rejected_by_employee_id'] = null;
                $updates['rejected_at'] = null;
                $updates['rejection_note'] = null;
            } else {
                $updates['rejected_by_employee_id'] = $employeeId;
                $updates['rejected_at'] = $now;
                $updates['rejection_note'] = $note;
                $updates['accepted_by_employee_id'] = null;
                $updates['accepted_at'] = null;
            }

            $this->plannerUpdateRow(
                'planner_item_material_requests',
                (int) $row->id,
                $this->pmoFilterExistingColumns('planner_item_material_requests', $updates)
            );

            if ($status === 'accepted') {
                $this->pmoCreatePlannerMaterialFromRequest($plan, $item, $row, $employeeId);
            }
        });

        $fresh = DB::table('planner_item_material_requests')
            ->where('id', (int) $materialRequest)
            ->first();

        $payload = $fresh ? $this->plannerMaterialRequestRowPayload($fresh) : null;

        $eventPayload = [
            'type' => 'planner_material_request_status',
            'event' => 'planner.material.request.status',
            'title' => $status === 'accepted'
                ? 'Materialanfrage angenommen'
                : 'Materialanfrage abgelehnt',
            'message' => $status === 'accepted'
                ? 'Die Materialanfrage wurde angenommen.'
                : 'Die Materialanfrage wurde abgelehnt.',
            'planner_item_id' => (int) $item->id,
            'planner_plan_id' => (int) $plan->id,
            'material_request_id' => (int) $materialRequest,
            'status' => $status,
            'material' => $payload,
            'sound' => true,
            'created_at' => now()->toDateTimeString(),
        ];

        $channels = [$this->rtPlan((int) $plan->id), 'planner.materials'];

        if (!empty($fresh?->requested_by_employee_id)) {
            $channels[] = $this->rtEmployee((int) $fresh->requested_by_employee_id);
        }

        $this->broadcast(array_values(array_unique($channels)), 'planner.material.request.status', $eventPayload);

        return response()->json([
            'ok' => true,
            'message' => $status === 'accepted'
                ? 'Materialanfrage wurde angenommen.'
                : 'Materialanfrage wurde abgelehnt.',
            'status' => $status,
            'request' => $payload,
            'material_request' => $payload,
            'item' => [
                'id' => (int) $item->id,
                'planner_item_id' => (int) $item->id,
                'materials' => $this->pmoLoadPlannerMaterials((int) $item->id),
                'material_summary' => $this->pmoPlannerMaterialSummary(
                    $this->pmoLoadPlannerMaterials((int) $item->id)
                ),
            ],
            'event' => 'planner.material.request.status',
        ]);
    }


    public function destroyItemMaterial(PlannerPlan $plan, PlannerItem $item, int $material)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        $table = $this->plannerMaterialTable();

        if (!$table) {
            return response()->json(['ok' => false, 'message' => 'planner_item_materials table does not exist.'], 422);
        }

        $q = DB::table($table)
            ->where('id', $material)
            ->where('planner_item_id', (int) $item->id);

        if ($this->safeColumn($table, 'deleted_at')) {
            $q->update(['deleted_at' => now(), 'updated_at' => now()]);
        } else {
            $q->delete();
        }

        return response()->json(['ok' => true]);
    }

    public function importDealMaterials(Request $request, PlannerPlan $plan, PlannerItem $item)
    {
        $this->plannerEnsureItemBelongsToPlan($plan, $item);

        $table = $this->plannerMaterialTable();

        if (!$table) {
            return response()->json(['ok' => false, 'message' => 'planner_item_materials table does not exist.'], 422);
        }

        $materials = $this->plannerDealMaterialSources($plan);
        $inserted = 0;
        $skipped = 0;

        foreach ($materials as $row) {
            $exists = DB::table($table)
                ->where('planner_item_id', (int) $item->id)
                ->where('source_type', $row['source_type'] ?? 'offer_detail')
                ->where('source_id', $row['source_id'] ?? 0)
                ->where('name', $row['name'] ?? '')
                ->when(!empty($row['product_id']), fn($q) => $q->where('product_id', (int) $row['product_id']))
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $this->plannerInsertRow($table, $this->plannerMaterialInsertPayload($item, $row, $plan));
            $inserted++;
        }

        return response()->json([
            'ok' => true,
            'inserted' => $inserted,
            'skipped' => $skipped,
            'current' => $this->plannerLoadCurrentMaterials($item),
        ]);
    }



    private function attachRequestedMaterialsToItems(array $payload): array
    {
        if (!Schema::hasTable('planner_item_material_requests')) {
            return $payload;
        }

        $items = collect($payload['items'] ?? $payload['work_items'] ?? $payload['data']['items'] ?? [])
            ->map(fn($item) => (array) $item)
            ->values();

        if ($items->isEmpty()) {
            return $payload;
        }

        $itemIds = $items
            ->map(fn($item) => (int) ($item['id'] ?? $item['planner_item_id'] ?? 0))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($itemIds)) {
            return $payload;
        }

        $requestsByItem = DB::table('planner_item_material_requests as pmr')
            ->leftJoin('employees as e', 'e.id', '=', 'pmr.requested_by_employee_id')
            ->whereNull('pmr.deleted_at')
            ->whereIn('pmr.planner_item_id', $itemIds)
            ->orderByDesc('pmr.id')
            ->get([
                'pmr.*',
                DB::raw("TRIM(CONCAT(COALESCE(e.title,''), ' ', COALESCE(e.name,''), ' ', COALESCE(e.lastname,''))) as requested_by_name"),
            ])
            ->map(fn($row) => $this->formatRequestedMaterialForPlanner($row))
            ->groupBy('planner_item_id');

        $items = $items->map(function (array $item) use ($requestsByItem) {
            $itemId = (int) ($item['id'] ?? $item['planner_item_id'] ?? 0);
            $existing = collect($item['materials'] ?? $item['material_list'] ?? $item['planner_materials'] ?? [])
                ->map(fn($row) => (array) $row)
                ->values();

            $requests = collect($requestsByItem->get($itemId, []))->values();

            $item['materials'] = $existing->merge($requests)->values()->all();
            $item['planner_materials'] = $item['materials'];
            $item['materials_count'] = count($item['materials']);
            $item['material_request_count'] = $requests->count();
            $item['material_request_open_count'] = $requests
                ->filter(fn($row) => in_array(strtolower((string) ($row['status'] ?? 'requested')), ['requested', 'open', 'pending'], true))
                ->count();

            return $item;
        })->values()->all();

        if (array_key_exists('items', $payload)) {
            $payload['items'] = $items;
        } elseif (array_key_exists('work_items', $payload)) {
            $payload['work_items'] = $items;
        } elseif (isset($payload['data']) && is_array($payload['data'])) {
            $payload['data']['items'] = $items;
        }

        return $payload;
    }

    private function formatRequestedMaterialForPlanner(object $row): array
    {
        return [
            'id' => (int) $row->id,
            'planner_item_id' => (int) $row->planner_item_id,
            'type' => 'manual_request',
            'origin_type' => 'employee_request',
            'origin' => 'employee_request',
            'source' => 'nuriva_mobile',
            'is_request' => true,
            'is_material_request' => true,
            'is_active' => false,
            'name' => $row->name ?: $row->article_name,
            'title' => $row->name ?: $row->article_name,
            'product' => $row->name ?: $row->article_name,
            'article_name' => $row->article_name ?: $row->name,
            'article_no' => $row->article_no ?? null,
            'description' => $row->description,
            'qty' => (float) $row->quantity,
            'quantity' => (float) $row->quantity,
            'unit' => $row->unit ?: 'Stk',
            'measure_unit' => $row->unit ?: 'Stk',
            'priority' => $row->priority,
            'needed_at' => $row->needed_at,
            'note' => $row->note,
            'status' => $row->status ?: 'requested',
            'request_status' => $row->status ?: 'requested',
            'requested_by_employee_id' => $row->requested_by_employee_id ? (int) $row->requested_by_employee_id : null,
            'requested_by_name' => trim((string) ($row->requested_by_name ?? '')) ?: null,
            'created_at' => $row->created_at,
        ];
    }

    private function attachRequestedMaterialsToPayload(array $payload): array
    {
        if (!Schema::hasTable('planner_item_material_requests')) {
            return $payload;
        }

        $itemIds = [];

        $collectIds = function ($node) use (&$collectIds, &$itemIds) {
            if (!is_array($node)) {
                return;
            }

            $looksLikePlannerItem = array_key_exists('title', $node)
                && (
                    array_key_exists('source_type', $node)
                    || array_key_exists('materials', $node)
                    || array_key_exists('material_list', $node)
                    || array_key_exists('planner_materials', $node)
                    || array_key_exists('planned_start_at', $node)
                    || array_key_exists('duration_minutes', $node)
                );

            $id = $node['planner_item_id']
                ?? $node['item_id']
                ?? ($looksLikePlannerItem ? ($node['id'] ?? null) : null);

            if ($id && is_numeric($id)) {
                $itemIds[(int) $id] = true;
            }

            foreach ($node as $value) {
                if (is_array($value)) {
                    $collectIds($value);
                }
            }
        };

        $collectIds($payload);
        $itemIds = array_keys($itemIds);

        if (empty($itemIds)) {
            return $payload;
        }

        $requestQuery = DB::table('planner_item_material_requests')
            ->whereIn('planner_item_id', $itemIds);

        if ($this->safeColumn('planner_item_material_requests', 'deleted_at')) {
            $requestQuery->whereNull('deleted_at');
        }

        $requests = $requestQuery
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn($row) => (int) $row->planner_item_id)
            ->map(function ($rows) {
                return $rows->map(fn($row) => $this->mapRequestedMaterialRow($row))->values()->all();
            })
            ->all();

        if (empty($requests)) {
            return $payload;
        }

        $materialKey = function ($row): ?string {
            if (!is_array($row)) {
                return null;
            }

            if (!empty($row['request_key'])) {
                return (string) $row['request_key'];
            }

            foreach (['material_request_id', 'request_id'] as $field) {
                if (!empty($row[$field]) && is_numeric($row[$field])) {
                    return 'employee_request:' . (int) $row[$field];
                }
            }

            $id = (string) ($row['id'] ?? '');
            if (preg_match('/^request[-_](\d+)$/', $id, $m)) {
                return 'employee_request:' . (int) $m[1];
            }

            return null;
        };

        $isOpenRequest = function ($row): bool {
            if (!is_array($row)) {
                return false;
            }

            $isRequest = (bool) ($row['is_request'] ?? $row['is_material_request'] ?? $row['is_employee_request'] ?? false);
            $origin = strtolower((string) ($row['origin_type'] ?? $row['source_origin'] ?? $row['source_type'] ?? $row['type'] ?? ''));

            if (!$isRequest && !in_array($origin, ['employee_request', 'asked_by_employee', 'material_request', 'manual_request', 'nuriva_mobile'], true)) {
                return false;
            }

            $status = strtolower((string) ($row['status'] ?? $row['request_status'] ?? 'requested'));

            return in_array($status, ['requested', 'open', 'pending', 'new'], true)
                || (bool) ($row['is_request_open'] ?? false);
        };

        $mergeIntoItems = function (&$node) use (&$mergeIntoItems, $requests, $materialKey, $isOpenRequest) {
            if (!is_array($node)) {
                return;
            }

            $looksLikePlannerItem = array_key_exists('title', $node)
                && (
                    array_key_exists('source_type', $node)
                    || array_key_exists('materials', $node)
                    || array_key_exists('material_list', $node)
                    || array_key_exists('planner_materials', $node)
                    || array_key_exists('planned_start_at', $node)
                    || array_key_exists('duration_minutes', $node)
                );

            $id = $node['planner_item_id']
                ?? $node['item_id']
                ?? ($looksLikePlannerItem ? ($node['id'] ?? null) : null);

            if ($id && is_numeric($id) && isset($requests[(int) $id])) {
                $existing = [];

                foreach (['materials', 'material_list', 'planner_materials'] as $key) {
                    if (!empty($node[$key]) && is_array($node[$key])) {
                        $existing = $node[$key];
                        break;
                    }
                }

                $existingKeys = collect($existing)
                    ->map(fn($row) => $materialKey($row))
                    ->filter()
                    ->values()
                    ->all();

                foreach ($requests[(int) $id] as $requestRow) {
                    $key = $materialKey($requestRow);

                    if ($key && !in_array($key, $existingKeys, true)) {
                        $existing[] = $requestRow;
                        $existingKeys[] = $key;
                    }
                }

                $node['materials'] = array_values($existing);
                $node['material_list'] = array_values($existing);
                $node['planner_materials'] = array_values($existing);
                $node['materials_count'] = count($existing);

                $requestedOpen = collect($existing)
                    ->filter(fn($row) => $isOpenRequest($row))
                    ->count();

                $requestedTotal = collect($existing)
                    ->filter(function ($row) {
                        if (!is_array($row)) {
                            return false;
                        }

                        $origin = strtolower((string) ($row['origin_type'] ?? $row['source_origin'] ?? $row['source_type'] ?? $row['type'] ?? ''));

                        return (bool) ($row['is_request'] ?? $row['is_material_request'] ?? $row['is_employee_request'] ?? false)
                            || in_array($origin, ['employee_request', 'asked_by_employee', 'material_request', 'manual_request', 'nuriva_mobile'], true);
                    })
                    ->count();

                $node['material_summary'] = array_merge($node['material_summary'] ?? [], [
                    'total' => count($existing),
                    'requested_total' => $requestedTotal,
                    'requested_open' => $requestedOpen,
                    'requested_not_responded' => $requestedOpen,
                ]);
            }

            foreach ($node as &$value) {
                if (is_array($value)) {
                    $mergeIntoItems($value);
                }
            }
        };

        $mergeIntoItems($payload);

        return $payload;
    }

    private function mapRequestedMaterialRow(object $row): array
    {
        $name = trim((string) ($row->name ?: $row->article_name ?: 'Material Anfrage'));

        return [
            'id' => 'request_' . (int) $row->id,
            'request_id' => (int) $row->id,
            'material_request_id' => (int) $row->id,
            'request_key' => 'employee_request:' . (int) $row->id,

            'planner_item_id' => (int) $row->planner_item_id,
            'planner_plan_id' => $row->planner_plan_id ? (int) $row->planner_plan_id : null,
            'lead_product_list_id' => $row->lead_product_list_id ? (int) $row->lead_product_list_id : null,

            'type' => 'manual_request',
            'origin_type' => 'employee_request',
            'source_type' => 'employee_request',
            'source' => 'nuriva_mobile',

            'name' => $name,
            'title' => $name,
            'article_name' => $row->article_name ?: $name,
            'description' => $row->description,
            'note' => $row->note,

            'qty' => (float) ($row->quantity ?? 1),
            'quantity' => (float) ($row->quantity ?? 1),
            'unit' => $row->unit ?: 'Stk',

            'priority' => $row->priority ?: 'normal',
            'needed_at' => $row->needed_at,

            'status' => $row->status ?: 'requested',
            'active' => false,
            'is_request' => true,
            'is_material_request' => true,
            'is_employee_request' => true,
            'is_request_open' => in_array(strtolower((string) ($row->status ?: 'requested')), ['requested', 'open', 'pending', 'new'], true),
            'requested_by_employee_id' => $row->requested_by_employee_id ? (int) $row->requested_by_employee_id : null,

            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ];
    }
    private function pmoCreatePlannerMaterialFromRequest(PlannerPlan $plan, PlannerItem $item, object $requestRow, ?int $employeeId = null): void
    {
        $table = $this->plannerMaterialTable();

        if (!$table || !Schema::hasTable($table)) {
            return;
        }

        $meta = json_decode((string) ($requestRow->meta ?? ''), true);
        $meta = is_array($meta) ? $meta : [];

        $name = trim((string) ($requestRow->name ?? $requestRow->article_name ?? 'Material'));
        if ($name === '') {
            $name = 'Material';
        }

        $qty = (float) ($requestRow->quantity ?? $requestRow->qty ?? 1);
        if ($qty <= 0) {
            $qty = 1;
        }

        $unit = trim((string) ($requestRow->unit ?? $requestRow->measure_unit ?? $requestRow->measure ?? 'Stk'));
        if ($unit === '') {
            $unit = 'Stk';
        }

        /*
        |--------------------------------------------------------------------------
        | Safe duplicate check
        |--------------------------------------------------------------------------
        | Some installations do not have source_type/source_id on planner_item_materials.
        | Therefore those columns are used only when they really exist.
        */
        $existsQuery = DB::table($table)
            ->where('planner_item_id', (int) $item->id);

        if ($this->safeColumn($table, 'source_type') && $this->safeColumn($table, 'source_id')) {
            $existsQuery
                ->where('source_type', 'employee_request')
                ->where('source_id', (int) $requestRow->id);
        } else {
            $nameColumns = array_values(array_filter([
                $this->safeColumn($table, 'name') ? 'name' : null,
                $this->safeColumn($table, 'article_name') ? 'article_name' : null,
                $this->safeColumn($table, 'material_name') ? 'material_name' : null,
                $this->safeColumn($table, 'title') ? 'title' : null,
            ]));

            if (!empty($nameColumns)) {
                $existsQuery->where(function ($query) use ($nameColumns, $name) {
                    foreach ($nameColumns as $index => $column) {
                        $method = $index === 0 ? 'where' : 'orWhere';
                        $query->{$method}($column, $name);
                    }
                });
            }

            if ($this->safeColumn($table, 'quantity')) {
                $existsQuery->where('quantity', $qty);
            } elseif ($this->safeColumn($table, 'qty')) {
                $existsQuery->where('qty', $qty);
            }

            if ($this->safeColumn($table, 'unit')) {
                $existsQuery->where('unit', $unit);
            } elseif ($this->safeColumn($table, 'measure_unit')) {
                $existsQuery->where('measure_unit', $unit);
            } elseif ($this->safeColumn($table, 'measure')) {
                $existsQuery->where('measure', $unit);
            }
        }

        if ($this->safeColumn($table, 'deleted_at')) {
            $existsQuery->whereNull('deleted_at');
        }

        if ($existsQuery->exists()) {
            return;
        }

        $insert = [
            'planner_item_id' => (int) $item->id,
            'planner_plan_id' => (int) $plan->id,
            'plan_id' => (int) $plan->id,
            'lead_product_list_id' => $plan->project_id ? (int) $plan->project_id : null,
            'project_id' => $plan->project_id ? (int) $plan->project_id : null,

            'customer_id' => $requestRow->customer_id ?? $plan->customer_id ?? null,
            'alternative_id' => $requestRow->alternative_id ?? null,
            'product_id' => $requestRow->product_id ?? null,

            'name' => $name,
            'title' => $name,
            'material_name' => $name,
            'article_name' => $requestRow->article_name ?? $name,
            'article_no' => $requestRow->article_no ?? null,
            'description' => $requestRow->description ?? null,

            'quantity' => $qty,
            'qty' => $qty,
            'amount' => $qty,
            'unit' => $unit,
            'measure' => $unit,
            'measure_unit' => $unit,

            'purchase_price' => 0,
            'unit_price' => 0,
            'total_price' => 0,

            'note' => $requestRow->note ?? null,
            'comment' => $requestRow->note ?? null,

            'active' => 1,
            'is_active' => 1,
            'status' => 'accepted',

            'source_type' => 'employee_request',
            'source_id' => (int) $requestRow->id,
            'origin_type' => 'accepted_employee_request',
            'origin' => 'accepted_employee_request',

            'master_set_id' => $meta['master_set_id'] ?? $requestRow->master_set_id ?? null,
            'requested_master_set_id' => $meta['requested_master_set_id'] ?? $requestRow->requested_master_set_id ?? null,

            'created_by_employee_id' => $employeeId,
            'employee_id' => $employeeId,
            'created_by' => auth()->id(),

            'source_payload' => json_encode([
                'material_request_id' => (int) $requestRow->id,
                'requested_by_employee_id' => $requestRow->requested_by_employee_id ?? null,
                'accepted_by_employee_id' => $employeeId,
                'request_note' => $requestRow->note ?? null,
                'request_description' => $requestRow->description ?? null,
                'master_set_id' => $meta['master_set_id'] ?? $requestRow->master_set_id ?? null,
            ], JSON_UNESCAPED_UNICODE),

            'meta' => json_encode([
                'source' => 'material_request_accept',
                'material_request_id' => (int) $requestRow->id,
                'requested_by_employee_id' => $requestRow->requested_by_employee_id ?? null,
                'accepted_by_employee_id' => $employeeId,
                'master_set_id' => $meta['master_set_id'] ?? $requestRow->master_set_id ?? null,
            ], JSON_UNESCAPED_UNICODE),

            'created_at' => now(),
            'updated_at' => now(),
        ];

        $filtered = $this->pmoFilterExistingColumns($table, $insert);

        if (empty($filtered)) {
            return;
        }

        DB::table($table)->insert($filtered);
    }
    private function pmoFilterExistingColumns(string $table, array $data): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        return collect($data)
            ->filter(fn($value, $column) => Schema::hasColumn($table, (string) $column))
            ->all();
    }
}