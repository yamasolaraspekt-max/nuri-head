<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlannerEmployeeApiController extends Controller
{
    /**
     * GET /planner/api/my-work
     * Returns the same planning context used by the planner index, but scoped to the authenticated employee.
     */
    public function myWork(Request $request): JsonResponse
    {
        $employeeId = $this->authEmployeeId();

        if (!$employeeId) {
            return response()->json([
                'ok' => false,
                'message' => 'Kein Mitarbeiter wurde für den angemeldeten Benutzer gefunden.',
            ], 403);
        }

        return $this->employeeWorkResponse($request, $employeeId);
    }

    /**
     * GET /planner/api/employees/{employee}/work
     * Manager/admin endpoint for loading another employee's planner work.
     */
    public function employeeWork(Request $request, int $employee): JsonResponse
    {
        return $this->employeeWorkResponse($request, $employee);
    }

    /**
     * GET /planner/api/my-day-report
     */
    public function myDayReport(Request $request): JsonResponse
    {
        $employeeId = $this->authEmployeeId();

        if (!$employeeId) {
            return response()->json([
                'ok' => false,
                'message' => 'Kein Mitarbeiter wurde für den angemeldeten Benutzer gefunden.',
            ], 403);
        }

        return $this->employeeDayReportResponse($request, $employeeId);
    }

    /**
     * GET /planner/api/employees/{employee}/day-report
     */
    public function employeeDayReport(Request $request, int $employee): JsonResponse
    {
        return $this->employeeDayReportResponse($request, $employee);
    }

    private function employeeWorkResponse(Request $request, int $employeeId): JsonResponse
    {
        if (!Schema::hasTable('planner_items') || !Schema::hasTable('planner_plans')) {
            return response()->json([
                'ok' => false,
                'message' => 'Planner Tabellen wurden nicht gefunden.',
            ], 500);
        }

        [$from, $to, $periodLabel] = $this->periodRange(
            (string) $request->query('date', now()->toDateString()),
            (string) $request->query('mode', 'day')
        );

        $limit = min(max((int) $request->query('limit', 500), 1), 1500);
        $includeDone = $request->boolean('include_done', false);
        $includeUnscheduled = $request->boolean('include_unscheduled', false);
        $includeTimeline = $request->boolean('include_project_timeline', true);
        $planId = $request->filled('plan_id') ? (int) $request->query('plan_id') : null;
        $projectId = $request->filled('project_id') ? (int) $request->query('project_id') : null;

        $employee = $this->employeeMini($employeeId);
        $items = $this->loadEmployeeItems(
            employeeId: $employeeId,
            from: $from,
            to: $to,
            includeDone: $includeDone,
            includeUnscheduled: $includeUnscheduled,
            planId: $planId,
            projectId: $projectId,
            limit: $limit
        );

        $itemIds = $items->pluck('id')->map(fn($id) => (int) $id)->filter()->unique()->values()->all();
        $planIds = $items->pluck('plan_id')->map(fn($id) => (int) $id)->filter()->unique()->values()->all();
        $projectIds = $items->pluck('project_id')->map(fn($id) => (int) $id)->filter()->unique()->values()->all();

        $relations = $this->loadItemRelations($itemIds, $planIds, $projectIds, $employeeId, $from, $to);

        $payloadItems = $items->map(function ($item) use ($relations) {
            return $this->itemPayload($item, $relations);
        })->values();

        $projects = $this->groupProjects($payloadItems);
        $byDate = $this->groupByDate($payloadItems);
        $attendance = $this->loadAttendance($employeeId, $from, $to);
        $latestActivity = $this->latestActivity($payloadItems, $relations['attendance_events'] ?? collect());
        $projectTimeline = $includeTimeline
            ? $this->loadEmployeeProjectTimeline($employeeId, $projectIds, $planIds)
            : [];

        return response()->json([
            'ok' => true,
            'meta' => [
                'employee_id' => $employeeId,
                'date' => $from->toDateString(),
                'mode' => $this->normalizeMode((string) $request->query('mode', 'day')),
                'from' => $from->toDateTimeString(),
                'to' => $to->toDateTimeString(),
                'period_label' => $periodLabel,
                'include_done' => $includeDone,
                'include_unscheduled' => $includeUnscheduled,
                'include_project_timeline' => $includeTimeline,
                'plan_id' => $planId,
                'project_id' => $projectId,
                'generated_at' => now()->toDateTimeString(),
            ],
            'employee' => $employee,
            'summary' => $this->summary($payloadItems, $attendance),
            'latest_activity' => $latestActivity,
            'attendance' => $attendance,
            'items' => $payloadItems,
            'grouped' => [
                'by_date' => $byDate,
                'by_project' => $projects,
            ],
            'project_timeline' => $projectTimeline,
        ]);
    }

    private function employeeDayReportResponse(Request $request, int $employeeId): JsonResponse
    {
        $request->merge([
            'mode' => 'day',
            'include_done' => true,
            'include_project_timeline' => true,
        ]);

        $workResponse = $this->employeeWorkResponse($request, $employeeId);
        $data = $workResponse->getData(true);

        if (!($data['ok'] ?? false)) {
            return $workResponse;
        }

        $items = collect($data['items'] ?? []);
        $doneItems = $items->filter(fn($item) => (bool) ($item['is_done'] ?? false))->values();
        $openItems = $items->reject(fn($item) => (bool) ($item['is_done'] ?? false))->values();

        $materials = $items
            ->flatMap(fn($item) => collect($item['materials'] ?? [])->map(function ($material) use ($item) {
                $material['planner_item_id'] = $item['id'] ?? null;
                $material['item_title'] = $item['title'] ?? null;
                $material['project_id'] = $item['project_id'] ?? null;
                return $material;
            }))
            ->values();

        $sharedMaterials = $items
            ->flatMap(fn($item) => collect($item['shared_group_materials'] ?? []))
            ->unique(fn($material) => ($material['id'] ?? '') . ':' . ($material['group_uuid'] ?? ''))
            ->values();

        $comments = $items
            ->flatMap(fn($item) => collect($item['comments'] ?? [])->map(function ($comment) use ($item) {
                $comment['planner_item_id'] = $item['id'] ?? null;
                $comment['item_title'] = $item['title'] ?? null;
                return $comment;
            }))
            ->values();

        $gallery = $items
            ->flatMap(fn($item) => collect($item['gallery'] ?? [])->map(function ($image) use ($item) {
                $image['planner_item_id'] = $item['id'] ?? null;
                $image['item_title'] = $item['title'] ?? null;
                return $image;
            }))
            ->values();

        $report = [
            'employee' => $data['employee'] ?? null,
            'date' => $data['meta']['date'] ?? now()->toDateString(),
            'period_label' => $data['meta']['period_label'] ?? null,
            'attendance' => $data['attendance'] ?? [],
            'summary' => $data['summary'] ?? [],
            'tasks' => [
                'all' => $items->values()->all(),
                'done' => $doneItems->values()->all(),
                'open' => $openItems->values()->all(),
            ],
            'materials' => [
                'task_materials' => $materials->values()->all(),
                'shared_group_materials' => $sharedMaterials->values()->all(),
            ],
            'comments' => $comments->values()->all(),
            'gallery' => $gallery->values()->all(),
            'latest_activity' => $data['latest_activity'] ?? [],
            'project_timeline' => $data['project_timeline'] ?? [],
        ];

        return response()->json([
            'ok' => true,
            'report' => $report,
        ]);
    }

    private function loadEmployeeItems(
        int $employeeId,
        Carbon $from,
        Carbon $to,
        bool $includeDone,
        bool $includeUnscheduled,
        ?int $planId,
        ?int $projectId,
        int $limit
    ): Collection {
        $q = DB::table('planner_items as pi')
            ->join('planner_plans as pp', 'pp.id', '=', 'pi.plan_id')
            ->whereExists(function ($exists) use ($employeeId) {
                $exists->select(DB::raw(1))
                    ->from('planner_item_employees as pie')
                    ->whereColumn('pie.planner_item_id', 'pi.id')
                    ->where('pie.employee_id', $employeeId);
            });

        if ($this->safeColumn('planner_items', 'deleted_at')) {
            $q->whereNull('pi.deleted_at');
        }

        if ($this->safeColumn('planner_plans', 'deleted_at')) {
            $q->whereNull('pp.deleted_at');
        }

        if ($planId) {
            $q->where('pi.plan_id', $planId);
        }

        if ($projectId) {
            $q->where('pp.project_id', $projectId);
        }

        if (!$includeDone && $this->safeColumn('planner_items', 'status')) {
            $q->whereRaw('LOWER(COALESCE(pi.status, "")) NOT IN (?, ?, ?, ?, ?)', [
                'done',
                'completed',
                'closed',
                'finished',
                'ended',
            ]);
        }

        $q->where(function ($date) use ($from, $to, $includeUnscheduled) {
            $date->where(function ($overlap) use ($from, $to) {
                $overlap->whereNotNull('pi.planned_start_at')
                    ->where('pi.planned_start_at', '<=', $to->toDateTimeString())
                    ->where(function ($end) use ($from) {
                        $end->whereNull('pi.planned_end_at')
                            ->orWhere('pi.planned_end_at', '>=', $from->toDateTimeString());
                    });
            });

            if ($includeUnscheduled) {
                $date->orWhereNull('pi.planned_start_at');
            }
        });

        $select = [
            'pi.*',
            'pp.id as planner_plan_id',
            'pp.title as planner_plan_title',
            'pp.project_id',
            'pp.customer_id as plan_customer_id',
        ];

        if (Schema::hasTable('lead_product_lists')) {
            $q->leftJoin('lead_product_lists as lpl', 'lpl.id', '=', 'pp.project_id');
            $select = array_merge($select, [
                'lpl.customer_id as project_customer_id',
                'lpl.alternative_id as project_alternative_id',
                'lpl.product_id as project_product_id',
                'lpl.status as project_status',
                'lpl.lead_stage_sub_stage_id as project_sub_stage_id',
            ]);
        }

        if (Schema::hasTable('new_leads')) {
            $q->leftJoin('new_leads as nl', 'nl.id', '=', DB::raw('COALESCE(lpl.customer_id, pp.customer_id)'));
            $select = array_merge($select, [
                'nl.customer_no as customer_no',
                'nl.name as customer_name',
                'nl.lastname as customer_lastname',
                'nl.firma as customer_company',
            ]);
        }

        if (Schema::hasTable('lead_alternative_adds')) {
            $q->leftJoin('lead_alternative_adds as laa', 'laa.id', '=', 'lpl.alternative_id');

            $select = array_merge($select, [
                'laa.id as object_id',

                Schema::hasColumn('lead_alternative_adds', 'street')
                ? DB::raw('laa.street as object_street')
                : DB::raw('NULL as object_street'),

                Schema::hasColumn('lead_alternative_adds', 'address_no')
                ? DB::raw('laa.address_no as object_street_number')
                : DB::raw('NULL as object_street_number'),

                Schema::hasColumn('lead_alternative_adds', 'postcode')
                ? DB::raw('laa.postcode as object_postcode')
                : DB::raw('NULL as object_postcode'),

                Schema::hasColumn('lead_alternative_adds', 'city')
                ? DB::raw('laa.city as object_city')
                : DB::raw('NULL as object_city'),

                Schema::hasColumn('lead_alternative_adds', 'object_name')
                ? DB::raw('laa.object_name as object_name')
                : DB::raw('NULL as object_name'),

                Schema::hasColumn('lead_alternative_adds', 'full_address')
                ? DB::raw('laa.full_address as object_full_address')
                : DB::raw('NULL as object_full_address'),

                Schema::hasColumn('lead_alternative_adds', 'lat')
                ? DB::raw('laa.lat as object_lat')
                : DB::raw('NULL as object_lat'),

                Schema::hasColumn('lead_alternative_adds', 'lon')
                ? DB::raw('laa.lon as object_lng')
                : DB::raw('NULL as object_lng'),
            ]);
        }

        if (Schema::hasTable('article_groups')) {
            $q->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id');
            $select = array_merge($select, [
                'ag.id as product_id',
                'ag.article_group as product_name',
                'ag.initial as product_initial',
                'ag.image as product_image',
            ]);
        }

        return $q->select($select)
            ->orderByRaw('COALESCE(pi.planned_start_at, pi.created_at) ASC')
            ->orderBy('pi.id')
            ->limit($limit)
            ->get();
    }

    private function loadItemRelations(array $itemIds, array $planIds, array $projectIds, int $employeeId, Carbon $from, Carbon $to): array
    {
        if (empty($itemIds)) {
            return [
                'employees' => collect(),
                'materials' => collect(),
                'shared_group_materials' => collect(),
                'steps' => collect(),
                'comments' => collect(),
                'gallery' => collect(),
                'status_history' => collect(),
                'dependencies' => collect(),
                'attendance_events' => collect(),
            ];
        }

        return [
            'employees' => $this->loadItemEmployees($itemIds),
            'materials' => $this->loadItemMaterials($itemIds),
            'shared_group_materials' => $this->loadSharedGroupMaterials($planIds, $itemIds, $employeeId, $from, $to),
            'steps' => $this->loadItemSteps($itemIds),
            'comments' => $this->loadItemComments($itemIds),
            'gallery' => $this->loadItemGallery($itemIds),
            'status_history' => $this->loadStatusHistory($itemIds),
            'dependencies' => $this->loadDependencies($itemIds),
            'attendance_events' => $this->loadAttendanceEvents($employeeId, $from, $to),
        ];
    }

    private function itemPayload(object $item, array $relations): array
    {
        $status = $this->normalizePlannerStatus((string) ($item->status ?? 'open'));
        $source = $this->sourceStatus((string) ($item->source_type ?? ''), (int) ($item->source_id ?? 0), $item);
        if (!empty($source['canonical_status'])) {
            $status = $source['canonical_status'];
        }

        $doneAt = $this->doneAt($item, $status, $source);
        $doneByEmployeeId = $this->doneByEmployeeId($item, $source);
        $doneByEmployee = $doneByEmployeeId ? $this->employeeMini($doneByEmployeeId) : null;

        $materials = $relations['materials']->get((int) $item->id, collect())->values()->all();
        $sharedGroupMaterials = $this->sharedGroupMaterialsForItem($relations['shared_group_materials'], (int) $item->id);
        $steps = $relations['steps']->get((int) $item->id, collect())->values()->all();
        $comments = $relations['comments']->get((int) $item->id, collect())->values()->all();
        $gallery = $relations['gallery']->get((int) $item->id, collect())->values()->all();
        $history = $relations['status_history']->get((int) $item->id, collect())->values()->all();
        $dependencies = $relations['dependencies']->get((int) $item->id, collect())->values()->all();
        $employees = $relations['employees']->get((int) $item->id, collect())->values()->all();

        return [
            'id' => (int) $item->id,
            'planner_item_id' => (int) $item->id,
            'plan_id' => (int) $item->plan_id,
            'planner_plan_id' => (int) ($item->planner_plan_id ?? $item->plan_id),
            'planner_plan_title' => $item->planner_plan_title ?? null,
            'project_id' => $item->project_id ? (int) $item->project_id : null,
            'source_type' => $item->source_type,
            'source_id' => $item->source_id ? (int) $item->source_id : null,
            'title' => $item->title,
            'description' => $item->description,
            'status' => $status,
            'status_label' => $this->statusLabel($status),
            'source_status' => $source,
            'is_done' => $status === 'done',
            'done_at' => $doneAt,
            'done_at_label' => $this->dateTimeLabel($doneAt),
            'done_by_employee_id' => $doneByEmployeeId,
            'done_by_employee' => $doneByEmployee,
            'done_by_name' => $doneByEmployee['full_name'] ?? null,
            'planned_start_at' => $item->planned_start_at,
            'planned_end_at' => $item->planned_end_at,
            'planned_date' => $this->dateKey($item->planned_start_at ?: $item->created_at),
            'schedule_date_key' => $this->dateKey($item->planned_start_at ?: $item->created_at),
            'schedule_date_label' => $this->dateLabel($item->planned_start_at ?: $item->created_at),
            'schedule_time_label' => $this->timeLabel($item->planned_start_at),
            'schedule_range_label' => $this->timeRangeLabel($item->planned_start_at, $item->planned_end_at),
            'duration_minutes' => isset($item->duration_minutes) ? (int) $item->duration_minutes : null,
            'sort_order' => isset($item->sort_order) ? (int) $item->sort_order : null,
            'customer' => [
                'id' => $item->project_customer_id ? (int) $item->project_customer_id : ($item->plan_customer_id ? (int) $item->plan_customer_id : null),
                'number' => $item->customer_no ?? null,
                'name' => $this->customerName($item),
            ],
            'object' => [
                'id' => $item->object_id ? (int) $item->object_id : null,
                'name' => $item->object_name ?? null,
                'address' => $item->object_full_address ?? $this->objectAddress($item),
                'street' => $item->object_street ?? null,
                'street_number' => $item->object_street_number ?? null,
                'postcode' => $item->object_postcode ?? null,
                'city' => $item->object_city ?? null,
                'lat' => isset($item->object_lat) ? (float) $item->object_lat : null,
                'lng' => isset($item->object_lng) ? (float) $item->object_lng : null,
            ],
            'product' => [
                'id' => $item->product_id ? (int) $item->product_id : ($item->project_product_id ? (int) $item->project_product_id : null),
                'name' => $item->product_name ?? null,
                'initial' => $item->product_initial ?? null,
                'image' => $item->product_image ?? null,
                'image_url' => $this->assetUrl($item->product_image ?? null, 'images/article_groups'),
            ],
            'team' => $employees,
            'steps' => $steps,
            'materials' => $materials,
            'material_summary' => $this->materialSummary($materials),
            'shared_group_materials' => $sharedGroupMaterials,
            'comments' => $comments,
            'gallery' => $gallery,
            'dependencies' => $dependencies,
            'status_history' => $history,
            'latest_activity' => $this->itemLatestActivity($history, $comments, $gallery, $materials),
        ];
    }

    private function loadItemEmployees(array $itemIds): Collection
    {
        if (!Schema::hasTable('planner_item_employees') || !Schema::hasTable('employees')) {
            return collect();
        }

        $rows = DB::table('planner_item_employees as pie')
            ->join('employees as e', 'e.id', '=', 'pie.employee_id')
            ->whereIn('pie.planner_item_id', $itemIds)
            ->select([
                'pie.planner_item_id',
                'pie.employee_id',

                Schema::hasColumn('planner_item_employees', 'role')
                ? DB::raw('pie.role as role')
                : DB::raw('NULL as role'),

                'e.id',

                Schema::hasColumn('employees', 'name')
                ? DB::raw('e.name as name')
                : DB::raw('NULL as name'),

                Schema::hasColumn('employees', 'lastname')
                ? DB::raw('e.lastname as lastname')
                : DB::raw('NULL as lastname'),

                Schema::hasColumn('employees', 'title')
                ? DB::raw('e.title as title')
                : DB::raw('NULL as title'),

                Schema::hasColumn('employees', 'image')
                ? DB::raw('e.image as image')
                : DB::raw('NULL as image'),

                DB::raw('NULL as photo'),

                Schema::hasColumn('employees', 'email')
                ? DB::raw('e.email as email')
                : DB::raw('NULL as email'),

                Schema::hasColumn('employees', 'phone')
                ? DB::raw('e.phone as phone')
                : DB::raw('NULL as phone'),
            ])
            ->get()
            ->map(function ($row) {
                return [
                    'planner_item_id' => (int) $row->planner_item_id,
                    'id' => (int) $row->employee_id,
                    'employee_id' => (int) $row->employee_id,
                    'role' => $row->role ?: 'member',
                    'name' => $row->name,
                    'lastname' => $row->lastname,
                    'full_name' => trim((($row->title ?? '') ? ($row->title . ' ') : '') . (($row->name ?? '') . ' ' . ($row->lastname ?? ''))),
                    'photo_url' => $this->assetUrl($row->image ?? $row->photo ?? null, 'images/employee'),
                    'email' => $row->email ?? null,
                    'phone' => $row->phone ?? null,
                ];
            });

        return $rows->groupBy('planner_item_id');
    }

    private function loadItemMaterials(array $itemIds): Collection
    {
        if (!Schema::hasTable('planner_item_materials')) {
            return collect();
        }

        $fk = $this->firstExistingColumn('planner_item_materials', ['planner_item_id', 'item_id']);

        if (!$fk) {
            return collect();
        }

        $rows = DB::table('planner_item_materials')
            ->whereIn($fk, $itemIds)
            ->when($this->safeColumn('planner_item_materials', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->orderBy('id')
            ->get()
            ->map(function ($row) use ($fk) {
                return $this->genericRowPayload($row, $fk, [
                    'id' => (int) $row->id,
                    'planner_item_id' => (int) ($row->{$fk} ?? 0),
                    'name' => $row->name ?? $row->title ?? $row->article_name ?? $row->product_name ?? ('Material #' . $row->id),
                    'quantity' => (float) ($row->quantity ?? $row->qty ?? $row->amount ?? 1),
                    'unit' => $row->unit ?? $row->unit_name ?? null,
                    'status' => $row->status ?? null,
                    'is_request' => (bool) ($row->is_request ?? false),
                    'is_added' => (bool) ($row->is_added ?? true),
                    'source' => $row->source ?? $row->origin_type ?? null,
                ]);
            });

        return $rows->groupBy('planner_item_id');
    }

    private function loadSharedGroupMaterials(array $planIds, array $itemIds, int $employeeId, Carbon $from, Carbon $to): Collection
    {
        if (empty($planIds) || !Schema::hasTable('planner_group_materials')) {
            return collect();
        }

        $planColumn = $this->firstExistingColumn('planner_group_materials', ['planner_plan_id', 'plan_id']);

        if (!$planColumn) {
            return collect();
        }

        $q = DB::table('planner_group_materials')
            ->whereIn($planColumn, $planIds)
            ->when($this->safeColumn('planner_group_materials', 'deleted_at'), fn($query) => $query->whereNull('deleted_at'));

        if ($this->safeColumn('planner_group_materials', 'employee_id')) {
            $q->where('employee_id', $employeeId);
        }

        if ($this->safeColumn('planner_group_materials', 'date')) {
            $q->whereBetween(DB::raw('DATE(`date`)'), [$from->toDateString(), $to->toDateString()]);
        } elseif ($this->safeColumn('planner_group_materials', 'scope_date_from') && $this->safeColumn('planner_group_materials', 'scope_date_to')) {
            $q->whereDate('scope_date_from', '<=', $to->toDateString())
                ->whereDate('scope_date_to', '>=', $from->toDateString());
        }

        return $q->orderByDesc('id')
            ->get()
            ->map(function ($row) {
                $itemIds = $this->decodeJson($row->item_ids ?? $row->planner_item_ids ?? null);
                $itemIds = collect($itemIds)->map(fn($id) => (int) $id)->filter()->values()->all();

                return [
                    'id' => (int) $row->id,
                    'group_uuid' => $row->group_uuid ?? $row->uuid ?? null,
                    'group_name' => $row->group_name ?? $row->name ?? 'Gruppenmaterial',
                    'name' => $row->name ?? $row->title ?? $row->article_name ?? $row->product_name ?? 'Gruppenmaterial',
                    'quantity' => (float) ($row->quantity ?? $row->qty ?? $row->amount ?? 1),
                    'unit' => $row->unit ?? $row->unit_name ?? null,
                    'employee_id' => isset($row->employee_id) ? (int) $row->employee_id : null,
                    'planner_plan_id' => isset($row->planner_plan_id) ? (int) $row->planner_plan_id : (isset($row->plan_id) ? (int) $row->plan_id : null),
                    'scope_date_from' => $row->scope_date_from ?? $row->date ?? null,
                    'scope_date_to' => $row->scope_date_to ?? $row->date ?? null,
                    'item_ids' => $itemIds,
                    'note' => $row->note ?? null,
                    'created_at' => $row->created_at ?? null,
                ];
            });
    }

    private function sharedGroupMaterialsForItem(Collection $sharedMaterials, int $itemId): array
    {
        return $sharedMaterials
            ->filter(function ($material) use ($itemId) {
                $ids = $material['item_ids'] ?? [];
                return in_array($itemId, array_map('intval', $ids), true);
            })
            ->values()
            ->all();
    }

    private function loadItemSteps(array $itemIds): Collection
    {
        $all = collect();

        foreach (['planner_item_steps', 'planner_item_checklists'] as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $fk = $this->firstExistingColumn($table, ['planner_item_id', 'item_id']);

            if (!$fk) {
                continue;
            }

            $rows = DB::table($table)
                ->whereIn($fk, $itemIds)
                ->when($this->safeColumn($table, 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->orderBy($this->safeColumn($table, 'sort_order') ? 'sort_order' : 'id')
                ->get()
                ->map(function ($row) use ($fk, $table) {
                    return $this->genericRowPayload($row, $fk, [
                        'id' => (int) $row->id,
                        'planner_item_id' => (int) ($row->{$fk} ?? 0),
                        'title' => $row->title ?? $row->name ?? $row->task ?? $row->description ?? ('Schritt #' . $row->id),
                        'description' => $row->description ?? null,
                        'status' => $row->status ?? null,
                        'is_completed' => (bool) ($row->is_completed ?? $row->completed ?? false),
                        'done_at' => $row->done_date ?? $row->done_at ?? null,
                        'done_by' => $row->done_by ?? $row->done_by_employee_id ?? null,
                        'source_table' => $table,
                    ]);
                });

            $all = $all->merge($rows);
        }

        return $all->groupBy('planner_item_id');
    }

    private function loadItemComments(array $itemIds): Collection
    {
        if (!Schema::hasTable('planner_item_comments')) {
            return collect();
        }

        $fk = $this->firstExistingColumn('planner_item_comments', ['planner_item_id', 'item_id']);

        if (!$fk) {
            return collect();
        }

        $rows = DB::table('planner_item_comments')
            ->whereIn($fk, $itemIds)
            ->when($this->safeColumn('planner_item_comments', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->orderByDesc('id')
            ->limit(1000)
            ->get()
            ->map(function ($row) use ($fk) {
                $employeeId = $row->employee_id ?? $row->created_by_employee_id ?? $row->created_by ?? null;
                return $this->genericRowPayload($row, $fk, [
                    'id' => (int) $row->id,
                    'planner_item_id' => (int) ($row->{$fk} ?? 0),
                    'comment' => $row->comment ?? $row->body ?? $row->note ?? $row->message ?? null,
                    'employee_id' => is_numeric($employeeId) ? (int) $employeeId : null,
                    'employee' => is_numeric($employeeId) ? $this->employeeMini((int) $employeeId) : null,
                    'created_at' => $row->created_at ?? null,
                    'created_at_label' => $this->dateTimeLabel($row->created_at ?? null),
                ]);
            });

        return $rows->groupBy('planner_item_id');
    }

    private function loadItemGallery(array $itemIds): Collection
    {
        if (!Schema::hasTable('images')) {
            return collect();
        }

        $q = DB::table('images');

        $q->where(function ($query) use ($itemIds) {
            $used = false;

            if ($this->safeColumn('images', 'planner_item_id')) {
                $query->whereIn('planner_item_id', $itemIds);
                $used = true;
            }

            if ($this->safeColumn('images', 'task_id')) {
                $method = $used ? 'orWhereIn' : 'whereIn';
                $query->{$method}('task_id', $itemIds);
            }
        });

        if ($this->safeColumn('images', 'deleted_at')) {
            $q->whereNull('deleted_at');
        }

        $rows = $q->orderByDesc('id')
            ->limit(1000)
            ->get()
            ->map(function ($row) {
                $plannerItemId = $row->planner_item_id ?? $row->task_id ?? null;
                $image = $row->image ?? $row->path ?? $row->file_path ?? null;

                return [
                    'id' => (int) $row->id,
                    'planner_item_id' => is_numeric($plannerItemId) ? (int) $plannerItemId : null,
                    'name' => $row->image_name ?? $row->name ?? $row->title ?? null,
                    'file_type' => $row->file_type ?? null,
                    'image' => $image,
                    'url' => $this->assetUrl($image, 'uploads/customers'),
                    'created_at' => $row->created_at ?? null,
                    'created_at_label' => $this->dateTimeLabel($row->created_at ?? null),
                ];
            })
            ->filter(fn($row) => !empty($row['planner_item_id']));

        return $rows->groupBy('planner_item_id');
    }

    private function loadStatusHistory(array $itemIds): Collection
    {
        if (!Schema::hasTable('planner_item_status_histories')) {
            return collect();
        }

        $rows = DB::table('planner_item_status_histories')
            ->whereIn('planner_item_id', $itemIds)
            ->orderByDesc('changed_at')
            ->orderByDesc('id')
            ->limit(2000)
            ->get()
            ->map(function ($row) {
                $employeeId = $row->changed_by_employee_id ?? null;
                return [
                    'id' => (int) $row->id,
                    'planner_item_id' => (int) $row->planner_item_id,
                    'old_status' => $row->old_status ?? null,
                    'old_status_label' => $this->statusLabel($row->old_status ?? null),
                    'new_status' => $row->new_status ?? null,
                    'new_status_label' => $this->statusLabel($row->new_status ?? null),
                    'status_label' => $row->status_label ?? $this->statusLabel($row->new_status ?? null),
                    'changed_by_employee_id' => is_numeric($employeeId) ? (int) $employeeId : null,
                    'changed_by_employee' => is_numeric($employeeId) ? $this->employeeMini((int) $employeeId) : null,
                    'changed_at' => $row->changed_at ?? $row->created_at ?? null,
                    'changed_at_label' => $this->dateTimeLabel($row->changed_at ?? $row->created_at ?? null),
                    'note' => $row->note ?? null,
                ];
            });

        return $rows->groupBy('planner_item_id');
    }

    private function loadDependencies(array $itemIds): Collection
    {
        if (!Schema::hasTable('planner_item_dependencies')) {
            return collect();
        }

        $fromColumn = $this->firstExistingColumn('planner_item_dependencies', ['planner_item_id', 'item_id']);
        $toColumn = $this->firstExistingColumn('planner_item_dependencies', ['depends_on_item_id', 'dependency_item_id', 'depends_on_id', 'parent_item_id']);

        if (!$fromColumn || !$toColumn) {
            return collect();
        }

        $rows = DB::table('planner_item_dependencies as d')
            ->leftJoin('planner_items as target', 'target.id', '=', 'd.' . $toColumn)
            ->whereIn('d.' . $fromColumn, $itemIds)
            ->select([
                'd.*',
                'target.title as dependency_title',
                'target.status as dependency_status',
            ])
            ->get()
            ->map(function ($row) use ($fromColumn, $toColumn) {
                return [
                    'id' => (int) $row->id,
                    'planner_item_id' => (int) ($row->{$fromColumn} ?? 0),
                    'depends_on_item_id' => (int) ($row->{$toColumn} ?? 0),
                    'title' => $row->dependency_title ?? ('Job #' . ($row->{$toColumn} ?? '')),
                    'status' => $row->dependency_status ?? null,
                    'reason' => $row->reason ?? $row->note ?? null,
                ];
            });

        return $rows->groupBy('planner_item_id');
    }

    private function loadAttendance(int $employeeId, Carbon $from, Carbon $to): array
    {
        if (!Schema::hasTable('attendances')) {
            return [
                'status' => 'unknown',
                'status_label' => 'Keine Anwesenheitstabelle',
                'records' => [],
                'events' => [],
                'latest_location' => null,
            ];
        }

        $records = DB::table('attendances')
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->map(fn($row) => $this->attendancePayload($row))
            ->values();

        $events = $this->loadAttendanceEvents($employeeId, $from, $to)->values();
        $latestLocation = $this->loadLatestLocation($employeeId, $from, $to);
        $latestRecord = $records->first();
        $latestEvent = $events->first();

        $status = $latestEvent['event_type'] ?? ($latestRecord['status'] ?? 'not_checked_in');

        return [
            'status' => $status,
            'status_label' => $this->attendanceStatusLabel($status),
            'latest_record' => $latestRecord,
            'latest_event' => $latestEvent,
            'latest_location' => $latestLocation,
            'records' => $records->all(),
            'events' => $events->all(),
        ];
    }

    private function loadAttendanceEvents(int $employeeId, Carbon $from, Carbon $to): Collection
    {
        if (!Schema::hasTable('attendance_events')) {
            return collect();
        }

        return DB::table('attendance_events')
            ->where('employee_id', $employeeId)
            ->whereBetween('created_at', [$from->toDateTimeString(), $to->toDateTimeString()])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(500)
            ->get()
            ->map(function ($row) {
                return [
                    'id' => (int) $row->id,
                    'employee_id' => (int) $row->employee_id,
                    'attendance_id' => isset($row->attendance_id) ? (int) $row->attendance_id : null,
                    'planner_plan_id' => isset($row->planner_plan_id) ? (int) $row->planner_plan_id : null,
                    'planner_item_id' => isset($row->planner_item_id) ? (int) $row->planner_item_id : null,
                    'event_type' => $row->event_type ?? $row->type ?? null,
                    'status_label' => $this->attendanceStatusLabel($row->event_type ?? $row->type ?? null),
                    'note' => $row->note ?? null,
                    'meta' => $this->decodeJson($row->meta ?? null),
                    'created_at' => $row->created_at ?? null,
                    'created_at_label' => $this->dateTimeLabel($row->created_at ?? null),
                ];
            });
    }

    private function loadLatestLocation(int $employeeId, Carbon $from, Carbon $to): ?array
    {
        if (!Schema::hasTable('attendance_locations')) {
            return null;
        }

        $row = DB::table('attendance_locations')
            ->where('employee_id', $employeeId)
            ->whereBetween('created_at', [$from->copy()->subDay()->toDateTimeString(), $to->toDateTimeString()])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        if (!$row) {
            return null;
        }

        return [
            'id' => (int) $row->id,
            'employee_id' => (int) $row->employee_id,
            'attendance_id' => isset($row->attendance_id) ? (int) $row->attendance_id : null,
            'lat' => isset($row->lat) ? (float) $row->lat : (isset($row->latitude) ? (float) $row->latitude : null),
            'lng' => isset($row->lng) ? (float) $row->lng : (isset($row->longitude) ? (float) $row->longitude : null),
            'accuracy' => isset($row->accuracy) ? (float) $row->accuracy : null,
            'speed' => isset($row->speed) ? (float) $row->speed : null,
            'heading' => isset($row->heading) ? (float) $row->heading : null,
            'created_at' => $row->created_at ?? null,
            'created_at_label' => $this->dateTimeLabel($row->created_at ?? null),
        ];
    }

    private function loadEmployeeProjectTimeline(int $employeeId, array $projectIds, array $planIds): array
    {
        if (empty($projectIds) && empty($planIds)) {
            return [];
        }

        $q = DB::table('planner_items as pi')
            ->join('planner_plans as pp', 'pp.id', '=', 'pi.plan_id')
            ->whereExists(function ($exists) use ($employeeId) {
                $exists->select(DB::raw(1))
                    ->from('planner_item_employees as pie')
                    ->whereColumn('pie.planner_item_id', 'pi.id')
                    ->where('pie.employee_id', $employeeId);
            });

        if (!empty($planIds)) {
            $q->whereIn('pi.plan_id', $planIds);
        }

        if (!empty($projectIds)) {
            $q->whereIn('pp.project_id', $projectIds);
        }

        if ($this->safeColumn('planner_items', 'deleted_at')) {
            $q->whereNull('pi.deleted_at');
        }

        $rows = $q->select([
            'pi.id',
            'pi.plan_id',
            'pp.project_id',
            'pi.source_type',
            'pi.source_id',
            'pi.title',
            'pi.status',
            'pi.planned_start_at',
            'pi.planned_end_at',
            'pi.duration_minutes',
            'pi.created_at',
            'pi.updated_at',
        ])
            ->orderByRaw('COALESCE(pi.planned_start_at, pi.created_at) ASC')
            ->orderBy('pi.id')
            ->limit(2000)
            ->get()
            ->groupBy('project_id');

        $out = [];

        foreach ($rows as $projectId => $items) {
            $previousEnd = null;
            $timeline = [];

            foreach ($items->values() as $row) {
                $start = $row->planned_start_at ?: $row->created_at;
                $end = $row->planned_end_at ?: $this->estimateEnd($start, (int) ($row->duration_minutes ?? 60));
                $gap = null;

                if ($previousEnd && $start) {
                    try {
                        $gap = Carbon::parse($previousEnd)->diffInMinutes(Carbon::parse($start), false);
                    } catch (\Throwable $e) {
                        $gap = null;
                    }
                }

                $timeline[] = [
                    'id' => (int) $row->id,
                    'planner_item_id' => (int) $row->id,
                    'plan_id' => (int) $row->plan_id,
                    'project_id' => $projectId ? (int) $projectId : null,
                    'source_type' => $row->source_type,
                    'source_id' => $row->source_id ? (int) $row->source_id : null,
                    'title' => $row->title,
                    'status' => $this->normalizePlannerStatus((string) $row->status),
                    'status_label' => $this->statusLabel($row->status),
                    'is_done' => $this->normalizePlannerStatus((string) $row->status) === 'done',
                    'start_at' => $start,
                    'end_at' => $end,
                    'start_label' => $this->dateTimeLabel($start),
                    'end_label' => $this->dateTimeLabel($end),
                    'duration_minutes' => (int) ($row->duration_minutes ?? 60),
                    'gap_from_previous_minutes' => $gap,
                    'gap_from_previous_label' => $this->minutesLabel($gap),
                ];

                $previousEnd = $end;
            }

            $out[] = [
                'project_id' => $projectId ? (int) $projectId : null,
                'first_at' => $timeline[0]['start_at'] ?? null,
                'newest_at' => !empty($timeline) ? ($timeline[count($timeline) - 1]['end_at'] ?? $timeline[count($timeline) - 1]['start_at']) : null,
                'items_count' => count($timeline),
                'done_count' => collect($timeline)->where('is_done', true)->count(),
                'open_count' => collect($timeline)->where('is_done', false)->count(),
                'timeline' => $timeline,
            ];
        }

        return $out;
    }

    private function sourceStatus(string $sourceType, int $sourceId, object $item): array
    {
        if ($sourceId <= 0) {
            return ['raw_status' => null, 'canonical_status' => null, 'done_at' => null, 'done_by_employee_id' => null];
        }

        $type = strtolower(trim($sourceType));
        $map = [
            'personal_task' => ['table' => 'personal_tasks', 'status' => ['task_status', 'status']],
            'ticket' => ['table' => 'problems', 'status' => ['status', 'problem_status', 'ticket_status']],
            'ticket_task' => ['table' => 'ticket_tasks', 'status' => ['status']],
            'kanban_task' => ['table' => 'kanban_lead_tasks', 'status' => ['status']],
            'appointment' => ['table' => 'main_appointments', 'status' => ['status', 'appointment_status', 'work_status']],
        ];

        if ($type === 'phase_activity') {
            return $this->phaseActivitySourceStatus($sourceId, $item);
        }

        if (!isset($map[$type]) || !Schema::hasTable($map[$type]['table'])) {
            return ['raw_status' => null, 'canonical_status' => null, 'done_at' => null, 'done_by_employee_id' => null];
        }

        $table = $map[$type]['table'];
        $row = DB::table($table)->where('id', $sourceId)->first();

        if (!$row) {
            return ['raw_status' => null, 'canonical_status' => null, 'done_at' => null, 'done_by_employee_id' => null];
        }

        $raw = null;
        foreach ($map[$type]['status'] as $column) {
            if (isset($row->{$column}) && trim((string) $row->{$column}) !== '') {
                $raw = (string) $row->{$column};
                break;
            }
        }

        if ($type === 'ticket_task' && isset($row->is_done) && (int) $row->is_done === 1) {
            $raw = 'done';
        }

        return [
            'table' => $table,
            'raw_status' => $raw,
            'canonical_status' => $this->readSourceStatusValue($row, $type),
            'done_at' => $this->sourceDoneAt($row),
            'done_by_employee_id' => $this->sourceDoneByEmployeeId($row),
        ];
    }

    private function phaseActivitySourceStatus(int $activityId, object $item): array
    {
        if (!Schema::hasTable('customer_histories')) {
            return ['raw_status' => null, 'canonical_status' => null, 'done_at' => null, 'done_by_employee_id' => null];
        }

        $q = DB::table('customer_histories')->where('activity_id', $activityId);

        if (!empty($item->plan_customer_id) && $this->safeColumn('customer_histories', 'customer_id')) {
            $q->where('customer_id', (int) $item->plan_customer_id);
        }

        if (!empty($item->project_product_id) && $this->safeColumn('customer_histories', 'product_id')) {
            $q->where('product_id', (int) $item->project_product_id);
        }

        $row = $q->orderByDesc('id')->first();

        if (!$row) {
            return ['raw_status' => null, 'canonical_status' => 'open', 'done_at' => null, 'done_by_employee_id' => null];
        }

        return [
            'table' => 'customer_histories',
            'raw_status' => $row->is_done ?? $row->status ?? null,
            'canonical_status' => $this->readSourceStatusValue($row, 'phase_activity'),
            'done_at' => $this->sourceDoneAt($row),
            'done_by_employee_id' => $this->sourceDoneByEmployeeId($row),
        ];
    }

    private function readSourceStatusValue(object $row, string $sourceType = ''): string
    {
        $type = strtolower(trim($sourceType));

        if ($type === 'personal_task') {
            $value = strtolower(trim((string) ($row->task_status ?? $row->status ?? '')));
            return match ($value) {
                'completed', 'done' => 'done',
                'on_progress', 'on_going', 'working', 'process', 'in_progress' => 'in_progress',
                'pause', 'paused' => 'paused',
                'cancel', 'junk', 'cancelled', 'canceled' => 'cancelled',
                'rejected', 'reject' => 'blocked',
                default => $this->normalizePlannerStatus($value),
            };
        }

        if ($type === 'ticket') {
            $value = strtolower(trim((string) ($row->status ?? $row->problem_status ?? $row->ticket_status ?? '')));
            return match ($value) {
                'end', 'ended', 'done', 'completed', 'closed' => 'done',
                'process', 'in_bearbeitung', 'in_progress' => 'in_progress',
                'offen', 'open' => 'open',
                'junk', 'cancel', 'cancelled', 'canceled' => 'cancelled',
                default => $this->normalizePlannerStatus($value),
            };
        }

        if ($type === 'ticket_task') {
            if (isset($row->is_done) && (int) $row->is_done === 1) {
                return 'done';
            }
            return $this->normalizePlannerStatus((string) ($row->status ?? 'open'));
        }

        if ($type === 'kanban_task') {
            $value = strtolower(trim((string) ($row->status ?? '')));
            return match ($value) {
                'done' => 'done',
                'in_progress' => 'in_progress',
                'scheduled' => 'planned',
                'cancelled', 'canceled' => 'cancelled',
                default => $this->normalizePlannerStatus($value),
            };
        }

        if ($type === 'appointment') {
            return $this->normalizePlannerStatus((string) ($row->status ?? $row->appointment_status ?? $row->work_status ?? 'open'));
        }

        if ($type === 'phase_activity') {
            if (isset($row->is_done) && ((int) $row->is_done === 1 || strtolower((string) $row->is_done) === 'done')) {
                return 'done';
            }
            if (isset($row->is_completed) && (int) $row->is_completed === 1) {
                return 'done';
            }
            return $this->normalizePlannerStatus((string) ($row->status ?? 'open'));
        }

        return $this->normalizePlannerStatus((string) ($row->status ?? $row->task_status ?? 'open'));
    }

    private function summary(Collection $items, array $attendance): array
    {
        $done = $items->where('is_done', true)->count();
        $total = $items->count();

        return [
            'total_items' => $total,
            'done_items' => $done,
            'open_items' => $items->where('is_done', false)->count(),
            'progress_percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
            'projects_count' => $items->pluck('project_id')->filter()->unique()->count(),
            'materials_count' => $items->sum(fn($item) => count($item['materials'] ?? [])),
            'shared_group_materials_count' => $items->flatMap(fn($item) => $item['shared_group_materials'] ?? [])->unique('id')->count(),
            'comments_count' => $items->sum(fn($item) => count($item['comments'] ?? [])),
            'gallery_count' => $items->sum(fn($item) => count($item['gallery'] ?? [])),
            'attendance_status' => $attendance['status'] ?? null,
            'attendance_status_label' => $attendance['status_label'] ?? null,
        ];
    }

    private function groupByDate(Collection $items): array
    {
        return $items
            ->groupBy(fn($item) => $item['schedule_date_key'] ?: 'without_date')
            ->map(function ($rows, $date) {
                return [
                    'date' => $date,
                    'label' => $date === 'without_date' ? 'Ohne Datum' : Carbon::parse($date)->format('d.m.Y'),
                    'count' => $rows->count(),
                    'done_count' => $rows->where('is_done', true)->count(),
                    'open_count' => $rows->where('is_done', false)->count(),
                    'items' => $rows->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function groupProjects(Collection $items): array
    {
        return $items
            ->groupBy(fn($item) => $item['project_id'] ?: 'without_project')
            ->map(function ($rows, $projectId) {
                $first = $rows->first();
                return [
                    'project_id' => $projectId === 'without_project' ? null : (int) $projectId,
                    'customer' => $first['customer'] ?? null,
                    'object' => $first['object'] ?? null,
                    'product' => $first['product'] ?? null,
                    'count' => $rows->count(),
                    'done_count' => $rows->where('is_done', true)->count(),
                    'open_count' => $rows->where('is_done', false)->count(),
                    'latest_activity' => $rows->pluck('latest_activity')->filter()->sortByDesc('at')->first(),
                    'items' => $rows->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function latestActivity(Collection $items, Collection $attendanceEvents): ?array
    {
        $activities = collect();

        foreach ($items as $item) {
            if (!empty($item['latest_activity'])) {
                $activities->push($item['latest_activity']);
            }
        }

        foreach ($attendanceEvents as $event) {
            $activities->push([
                'type' => 'attendance',
                'title' => $event['status_label'] ?? 'Anwesenheit',
                'text' => $event['note'] ?? null,
                'at' => $event['created_at'] ?? null,
                'at_label' => $event['created_at_label'] ?? null,
                'employee' => $this->employeeMini((int) ($event['employee_id'] ?? 0)),
            ]);
        }

        return $activities->filter(fn($activity) => !empty($activity['at']))->sortByDesc('at')->first();
    }

    private function itemLatestActivity(array $history, array $comments, array $gallery, array $materials): ?array
    {
        $activities = collect();

        foreach ($history as $row) {
            $activities->push([
                'type' => 'status',
                'title' => $row['status_label'] ?? $row['new_status_label'] ?? 'Status geändert',
                'text' => $row['note'] ?? null,
                'at' => $row['changed_at'] ?? null,
                'at_label' => $row['changed_at_label'] ?? null,
                'employee' => $row['changed_by_employee'] ?? null,
            ]);
        }

        foreach ($comments as $row) {
            $activities->push([
                'type' => 'comment',
                'title' => 'Kommentar',
                'text' => $row['comment'] ?? null,
                'at' => $row['created_at'] ?? null,
                'at_label' => $row['created_at_label'] ?? null,
                'employee' => $row['employee'] ?? null,
            ]);
        }

        foreach ($gallery as $row) {
            $activities->push([
                'type' => 'gallery',
                'title' => 'Galerie',
                'text' => $row['name'] ?? null,
                'at' => $row['created_at'] ?? null,
                'at_label' => $row['created_at_label'] ?? null,
            ]);
        }

        foreach ($materials as $row) {
            $activities->push([
                'type' => 'material',
                'title' => 'Material',
                'text' => $row['name'] ?? null,
                'at' => $row['created_at'] ?? null,
                'at_label' => $this->dateTimeLabel($row['created_at'] ?? null),
            ]);
        }

        return $activities->filter(fn($activity) => !empty($activity['at']))->sortByDesc('at')->first();
    }

    private function attendancePayload(object $row): array
    {
        return [
            'id' => (int) $row->id,
            'employee_id' => (int) $row->employee_id,
            'date' => isset($row->date) ? Carbon::parse($row->date)->toDateString() : null,
            'status' => $row->status ?? null,
            'status_label' => $this->attendanceStatusLabel($row->status ?? null),
            'check_in' => $row->check_in ?? null,
            'check_in_label' => $this->dateTimeLabel($row->check_in ?? null),
            'check_out' => $row->check_out ?? null,
            'check_out_label' => $this->dateTimeLabel($row->check_out ?? null),
            'travel_started_at' => $row->travel_started_at ?? null,
            'arrived_at' => $row->arrived_at ?? null,
            'work_started_at' => $row->work_started_at ?? null,
            'work_ended_at' => $row->work_ended_at ?? null,
            'pause_started_at' => $row->pause_started_at ?? null,
            'pause_ended_at' => $row->pause_ended_at ?? null,
            'travel_minutes' => isset($row->travel_minutes) ? (int) $row->travel_minutes : null,
            'work_minutes' => isset($row->work_minutes) ? (int) $row->work_minutes : null,
            'pause_minutes' => isset($row->pause_minutes) ? (int) $row->pause_minutes : null,
            'destination' => $row->destination ?? null,
            'destination_lat' => isset($row->destination_lat) ? (float) $row->destination_lat : null,
            'destination_lng' => isset($row->destination_lng) ? (float) $row->destination_lng : null,
            'meta' => $this->decodeJson($row->meta ?? null),
        ];
    }

    private function authEmployeeId(): ?int
    {
        $user = auth()->user();
        $id = $user?->employee_id ?? $user?->employee?->id ?? $user?->name ?? null;
        return is_numeric($id) ? (int) $id : null;
    }

    private function employeeMini(?int $employeeId): ?array
    {
        $employeeId = $employeeId ? (int) $employeeId : 0;

        if ($employeeId <= 0 || !Schema::hasTable('employees')) {
            return null;
        }

        $row = DB::table('employees')->where('id', $employeeId)->first();

        if (!$row) {
            return null;
        }

        $fullName = trim((($row->title ?? '') ? ($row->title . ' ') : '') . (($row->name ?? '') . ' ' . ($row->lastname ?? '')));
        $initials = mb_strtoupper(mb_substr((string) ($row->name ?? ''), 0, 1) . mb_substr((string) ($row->lastname ?? ''), 0, 1));

        return [
            'id' => (int) $row->id,
            'employee_id' => (int) $row->id,
            'name' => $row->name ?? null,
            'lastname' => $row->lastname ?? null,
            'full_name' => $fullName !== '' ? $fullName : ('Mitarbeiter #' . $row->id),
            'initials' => $initials !== '' ? $initials : 'MA',
            'photo_url' => $this->assetUrl($row->image ?? $row->photo ?? null, 'images/employee'),
            'email' => $row->email ?? null,
            'phone' => $row->phone ?? null,
        ];
    }

    private function periodRange(string $date, string $mode): array
    {
        try {
            $base = Carbon::parse($date);
        } catch (\Throwable $e) {
            $base = now();
        }

        return match ($this->normalizeMode($mode)) {
            'week' => [
                $base->copy()->startOfWeek()->startOfDay(),
                $base->copy()->endOfWeek()->endOfDay(),
                $base->copy()->startOfWeek()->format('d.m.Y') . ' - ' . $base->copy()->endOfWeek()->format('d.m.Y'),
            ],
            'month' => [
                $base->copy()->startOfMonth()->startOfDay(),
                $base->copy()->endOfMonth()->endOfDay(),
                $base->copy()->translatedFormat('F Y'),
            ],
            default => [
                $base->copy()->startOfDay(),
                $base->copy()->endOfDay(),
                $base->copy()->format('d.m.Y'),
            ],
        };
    }

    private function normalizeMode(string $mode): string
    {
        return in_array($mode, ['day', 'week', 'month'], true) ? $mode : 'day';
    }

    private function safeColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    private function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if ($this->safeColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }

    private function decodeJson($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || trim((string) $value) === '') {
            return [];
        }

        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function genericRowPayload(object $row, string $fk, array $base): array
    {
        $raw = json_decode(json_encode($row), true) ?: [];
        $base['raw'] = $raw;
        return $base;
    }

    private function materialSummary(array $materials): array
    {
        $total = count($materials);
        $requested = collect($materials)->where('is_request', true)->count();
        $added = collect($materials)->where('is_added', true)->count();

        return [
            'total' => $total,
            'added' => $added,
            'requested_total' => $requested,
            'requested_open' => collect($materials)->filter(fn($m) => ($m['is_request'] ?? false) && !($m['is_added'] ?? false))->count(),
        ];
    }

    private function normalizePlannerStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));
        $status = str_replace(['-', ' '], '_', $status);

        return match ($status) {
            'completed', 'complete', 'finished', 'finish', 'closed', 'close', 'ended', 'end', 'done', 'erledigt', 'beendet', 'geschlossen' => 'done',
            'in_progress', 'progress', 'process', 'in_bearbeitung', 'bearbeitung', 'on_progress', 'on_going', 'working', 'arbeit', 'in_arbeit', 'inarbeit' => 'in_progress',
            'scheduled', 'schedule', 'planned', 'confirmed', 'geplant', 'terminiert' => 'planned',
            'pause', 'paused', 'pausiert', 'mittag', 'mittagessen' => 'paused',
            'blocked', 'blockiert', 'reject', 'rejected' => 'blocked',
            'cancel', 'canceled', 'cancelled', 'storniert', 'junk' => 'cancelled',
            default => in_array($status, ['open', 'planned', 'in_progress', 'done', 'blocked', 'paused', 'cancelled'], true) ? $status : 'open',
        };
    }

    private function statusLabel(?string $status): string
    {
        return match ($this->normalizePlannerStatus($status)) {
            'done' => 'Erledigt',
            'in_progress' => 'In Arbeit',
            'planned' => 'Geplant',
            'blocked' => 'Blockiert',
            'paused' => 'Pausiert',
            'cancelled' => 'Storniert',
            default => 'Offen',
        };
    }

    private function attendanceStatusLabel(?string $status): string
    {
        $status = strtolower(trim((string) $status));
        return match ($status) {
            'check_in', 'present', 'checked_in' => 'Anwesend',
            'travel_start', 'traveling', 'in_travel' => 'In Fahrt',
            'arrived' => 'Angekommen',
            'work_start', 'working' => 'In Arbeit',
            'pause_start', 'pause', 'paused', 'lunch', 'mittag' => 'Pause',
            'pause_end' => 'Pause beendet',
            'work_end' => 'Arbeit beendet',
            'check_out', 'checked_out' => 'Feierabend',
            default => 'Nicht eingecheckt',
        };
    }

    private function doneAt(object $item, string $status, array $source): ?string
    {
        foreach (['done_at', 'completed_at', 'finished_at', 'closed_at'] as $column) {
            if (isset($item->{$column}) && !empty($item->{$column})) {
                return Carbon::parse($item->{$column})->toDateTimeString();
            }
        }

        if (!empty($source['done_at'])) {
            return Carbon::parse($source['done_at'])->toDateTimeString();
        }

        return $status === 'done' && !empty($item->updated_at) ? Carbon::parse($item->updated_at)->toDateTimeString() : null;
    }

    private function doneByEmployeeId(object $item, array $source): ?int
    {
        foreach (['done_by_employee_id', 'completed_by_employee_id', 'done_by', 'completed_by'] as $column) {
            if (isset($item->{$column}) && is_numeric($item->{$column}) && (int) $item->{$column} > 0) {
                return (int) $item->{$column};
            }
        }

        return !empty($source['done_by_employee_id']) ? (int) $source['done_by_employee_id'] : null;
    }

    private function sourceDoneAt(object $row): ?string
    {
        foreach (['done_at', 'completed_at', 'finished_at', 'closed_at', 'done_date'] as $column) {
            if (isset($row->{$column}) && !empty($row->{$column})) {
                return (string) $row->{$column};
            }
        }
        return null;
    }

    private function sourceDoneByEmployeeId(object $row): ?int
    {
        foreach (['done_by_employee_id', 'completed_by_employee_id', 'done_by', 'completed_by'] as $column) {
            if (isset($row->{$column}) && is_numeric($row->{$column}) && (int) $row->{$column} > 0) {
                return (int) $row->{$column};
            }
        }
        return null;
    }

    private function customerName(object $item): string
    {
        $company = trim((string) ($item->customer_company ?? ''));
        $name = trim((string) (($item->customer_name ?? '') . ' ' . ($item->customer_lastname ?? '')));
        return $company !== '' ? $company : ($name !== '' ? $name : 'Kunde');
    }

    private function objectAddress(object $item): string
    {
        return trim(implode(' ', array_filter([
            $item->object_street ?? null,
            $item->object_street_number ?? null,
            $item->object_postcode ?? null,
            $item->object_city ?? null,
        ])));
    }

    private function dateKey($value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function dateLabel($value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return Carbon::parse($value)->format('d.m.Y');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function timeLabel($value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return Carbon::parse($value)->format('H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function timeRangeLabel($start, $end): ?string
    {
        $startLabel = $this->timeLabel($start);
        $endLabel = $this->timeLabel($end);

        if (!$startLabel) {
            return null;
        }

        return $endLabel ? ($startLabel . ' - ' . $endLabel) : $startLabel;
    }

    private function dateTimeLabel($value): ?string
    {
        if (!$value) {
            return null;
        }
        try {
            return Carbon::parse($value)->format('d.m.Y H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function estimateEnd($start, int $minutes): ?string
    {
        if (!$start) {
            return null;
        }
        try {
            return Carbon::parse($start)->addMinutes(max(1, $minutes))->toDateTimeString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function minutesLabel($minutes): ?string
    {
        if ($minutes === null) {
            return null;
        }

        $negative = $minutes < 0;
        $minutes = abs((int) $minutes);
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;
        $label = $hours > 0 ? ($hours . 'h ' . $mins . 'm') : ($mins . 'm');

        return $negative ? ('Überlappung ' . $label) : $label;
    }

    private function assetUrl(?string $path, string $fallbackFolder = ''): ?string
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

        if (str_starts_with($path, 'storage/') || str_starts_with($path, 'uploads/') || str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return asset(trim($fallbackFolder, '/') . '/' . ltrim($path, '/'));
    }
 
    public function completeItemWithReport(Request $request, int $item): JsonResponse
    {
        $employeeId = $this->authEmployeeId();

        if (!$employeeId) {
            return response()->json([
                'ok' => false,
                'message' => 'Kein Mitarbeiter wurde für den angemeldeten Benutzer gefunden.',
            ], 403);
        }

        $data = $request->validate([
            'report' => ['nullable', 'string', 'max:20000'],
            'note' => ['nullable', 'string', 'max:20000'],
            'next_step' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date'],
            'skip_report' => ['nullable', 'boolean'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
        ]);

        $reportText = trim((string) ($data['report'] ?? ''));
        $noteText = trim((string) ($data['note'] ?? ''));
        $skipReport = (bool) ($data['skip_report'] ?? false);

        if ($reportText === '' && !$skipReport) {
            return response()->json([
                'ok' => false,
                'message' => 'Bitte Bericht schreiben oder "Ohne Bericht fertigstellen" wählen.',
            ], 422);
        }

        if (!Schema::hasTable('planner_items')) {
            return response()->json([
                'ok' => false,
                'message' => 'Planner Tabelle wurde nicht gefunden.',
            ], 500);
        }

        $plannerItem = DB::table('planner_items as pi')
            ->join('planner_plans as pp', 'pp.id', '=', 'pi.plan_id')
            ->leftJoin('lead_product_lists as lpl', 'lpl.id', '=', 'pp.project_id')
            ->where('pi.id', $item)
            ->whereExists(function ($exists) use ($employeeId) {
                $exists->select(DB::raw(1))
                    ->from('planner_item_employees as pie')
                    ->whereColumn('pie.planner_item_id', 'pi.id')
                    ->where('pie.employee_id', $employeeId);
            })
            ->select([
                'pi.*',
                'pp.project_id',
                'pp.customer_id as plan_customer_id',
                'lpl.customer_id as project_customer_id',
                'lpl.alternative_id as project_alternative_id',
                'lpl.product_id as project_product_id',
                'lpl.status as project_status',
            ])
            ->first();

        if (!$plannerItem) {
            return response()->json([
                'ok' => false,
                'message' => 'Planner Aufgabe wurde nicht gefunden oder ist diesem Mitarbeiter nicht zugeordnet.',
            ], 404);
        }

        $now = now();

        DB::transaction(function () use ($plannerItem, $employeeId, $data, $reportText, $noteText, $now) {
            /*
            |--------------------------------------------------------------------------
            | 1. Mark planner item done
            |--------------------------------------------------------------------------
            */
            $plannerUpdates = [
                'status' => 'done',
                'updated_at' => $now,
            ];

            if ($this->safeColumn('planner_items', 'done_at')) {
                $plannerUpdates['done_at'] = $now;
            }

            if ($this->safeColumn('planner_items', 'done_by_employee_id')) {
                $plannerUpdates['done_by_employee_id'] = $employeeId;
            }

            DB::table('planner_items')
                ->where('id', (int) $plannerItem->id)
                ->update($plannerUpdates);

            /*
            |--------------------------------------------------------------------------
            | 2. Save mirror report/comment in planner table
            |--------------------------------------------------------------------------
            | This is what Nuriva can read immediately when it reloads planner-work.
            |--------------------------------------------------------------------------
            */
            if ($reportText !== '') {
                $this->storePlannerItemReportMirror(
                    plannerItemId: (int) $plannerItem->id,
                    employeeId: $employeeId,
                    reportText: $reportText,
                    data: $data,
                    now: $now
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Save report into original source table
            |--------------------------------------------------------------------------
            */
            if ($reportText !== '') {
                $this->storePlannerSourceReport(
                    plannerItem: $plannerItem,
                    employeeId: $employeeId,
                    reportText: $reportText,
                    data: $data,
                    now: $now
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 4. Sync source status to done
            |--------------------------------------------------------------------------
            */
            $this->markPlannerSourceDone(
                plannerItem: $plannerItem,
                employeeId: $employeeId,
                reportText: $reportText,
                noteText: $noteText,
                now: $now
            );

            /*
            |--------------------------------------------------------------------------
            | 5. Status history
            |--------------------------------------------------------------------------
            */
            $this->storePlannerItemStatusHistory(
                plannerItemId: (int) $plannerItem->id,
                employeeId: $employeeId,
                oldStatus: (string) ($plannerItem->status ?? 'open'),
                newStatus: 'done',
                note: $noteText !== '' ? $noteText : ($reportText !== '' ? 'Mit Bericht abgeschlossen.' : 'Ohne Bericht abgeschlossen.'),
                now: $now
            );

            /*
            |--------------------------------------------------------------------------
            | 6. B3: Qualifikations-Rueckfluss auf die verlinkte Buero-Karte (1b-Link).
            |--------------------------------------------------------------------------
            | Additiv + feature-gesichert: greift NUR fuer phase_activity-Items mit
            | gesetztem kanban_lead_task_id UND einer Mindest-Qualifikation (B1) an der
            | Taetigkeit. Sonst passiert nichts (Verhalten exakt wie bisher).
            */
            $this->applyMontageQualificationRueckfluss($plannerItem, $employeeId, $now);
        });

        $fresh = DB::table('planner_items')
            ->where('id', (int) $plannerItem->id)
            ->first();

        return response()->json([
            'ok' => true,
            'message' => $reportText !== ''
                ? 'Aufgabe wurde mit Bericht abgeschlossen.'
                : 'Aufgabe wurde ohne Bericht abgeschlossen.',
            'item' => [
                'id' => (int) $fresh->id,
                'planner_item_id' => (int) $fresh->id,
                'source_type' => $fresh->source_type,
                'source_id' => $fresh->source_id ? (int) $fresh->source_id : null,
                'status' => 'done',
                'status_label' => 'Erledigt',
                'is_done' => true,
                'done_at' => $fresh->done_at ?? $fresh->updated_at ?? now()->toDateTimeString(),
                'done_by_employee_id' => $employeeId,
                'done_by_employee' => $this->employeeMini($employeeId),
            ],
        ]);
    }

    /**
     * B3: Qualifikations-Rueckfluss auf die verlinkte Buero-Karte.
     * Qualifiziert (person.sort_order <= required.sort_order) ODER keine Anforderung -> Karte 'done'.
     * Nicht qualifiziert -> 'reported' + ermittelter Pruefer (done_at bleibt null; PL bestaetigt spaeter).
     * Haertung: bereits done/cancelled ODER soft-geloeschte Karten werden NICHT angefasst.
     */
    private function applyMontageQualificationRueckfluss(object $plannerItem, int $employeeId, Carbon $now): void
    {
        $cardId = (int) ($plannerItem->kanban_lead_task_id ?? 0);
        $sourceType = strtolower(trim((string) ($plannerItem->source_type ?? '')));

        // Feature-Guard: nur verlinkte Montage-Items (phase_activity).
        if ($cardId <= 0 || $sourceType !== 'phase_activity' || !Schema::hasTable('kanban_lead_tasks')) {
            return;
        }

        // Karte lesen mit SoftDelete-Guard.
        $card = DB::table('kanban_lead_tasks')
            ->where('id', $cardId)
            ->when($this->safeColumn('kanban_lead_tasks', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->first();

        if (!$card) {
            return; // geloescht oder nicht vorhanden
        }

        // Zustands-Guard: vom Buero schon abgeschlossen/storniert -> Monteur-Report kippt nichts.
        $cardStatus = strtolower(trim((string) ($card->status ?? '')));
        if (in_array($cardStatus, ['done', 'cancelled', 'canceled'], true)) {
            return;
        }

        // B1-Anforderung der Taetigkeit (source_id = phase_activity_id).
        $requiredQualId = DB::table('phase_activities')
            ->where('id', (int) ($plannerItem->source_id ?? 0))
            ->value('required_qualification_id');

        if (empty($requiredQualId)) {
            $this->markKanbanCardDone($cardId, $employeeId, $now); // keine Anforderung -> done (wie heute)
            return;
        }

        $requiredSort = DB::table('position_qualifications')->where('id', (int) $requiredQualId)->value('sort_order');
        $performerSort = $this->employeeQualSort($employeeId);

        // Qualifiziert = performer.sort_order <= required.sort_order (niedriger sort_order = hoeher).
        // Null-Stufe (qualification_id leer) = NICHT qualifiziert (konservativ).
        if ($performerSort !== null && $requiredSort !== null && (int) $performerSort <= (int) $requiredSort) {
            $this->markKanbanCardDone($cardId, $employeeId, $now);
            return;
        }

        // NICHT qualifiziert -> reported + Pruefer ermitteln (done_at bleibt null).
        $reviewerId = $requiredSort !== null
            ? $this->resolveReviewer($employeeId, (int) $requiredSort)
            : $this->directSupervisor($employeeId);

        $updates = ['updated_at' => $now];
        if ($this->safeColumn('kanban_lead_tasks', 'status')) {
            $updates['status'] = 'reported';
        }
        if ($this->safeColumn('kanban_lead_tasks', 'reviewer_employee_id')) {
            $updates['reviewer_employee_id'] = $reviewerId;
        }

        DB::table('kanban_lead_tasks')
            ->where('id', $cardId)
            ->when($this->safeColumn('kanban_lead_tasks', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->update($updates);
    }

    /** Karte auf 'done' setzen (spiegelt den bestehenden kanban_task-Zweig in markPlannerSourceDone). */
    private function markKanbanCardDone(int $cardId, int $employeeId, Carbon $now): void
    {
        $updates = ['updated_at' => $now];
        if ($this->safeColumn('kanban_lead_tasks', 'status')) {
            $updates['status'] = 'done';
        }
        if ($this->safeColumn('kanban_lead_tasks', 'done_at')) {
            $updates['done_at'] = $now;
        }
        if ($this->safeColumn('kanban_lead_tasks', 'done_by_employee_id')) {
            $updates['done_by_employee_id'] = $employeeId;
        }

        DB::table('kanban_lead_tasks')
            ->where('id', $cardId)
            ->when($this->safeColumn('kanban_lead_tasks', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->update($updates);
    }

    /** Qualifikations-Stufe (position_qualifications.sort_order) einer Person; null = keine Stufe. */
    private function employeeQualSort(int $employeeId): ?int
    {
        if ($employeeId <= 0 || !Schema::hasTable('employees') || !Schema::hasTable('position_qualifications')) {
            return null;
        }

        $qualId = DB::table('employees')->where('id', $employeeId)->value('qualification_id');
        if (empty($qualId)) {
            return null; // keine Stufe -> NICHT qualifiziert (konservativ)
        }

        $sort = DB::table('position_qualifications')->where('id', (int) $qualId)->value('sort_order');

        return $sort === null ? null : (int) $sort;
    }

    /** Direkter Vorgesetzter (employees.supervisor) oder null. */
    private function directSupervisor(int $employeeId): ?int
    {
        if ($employeeId <= 0 || !Schema::hasTable('employees')) {
            return null;
        }

        $sup = (int) (DB::table('employees')->where('id', $employeeId)->value('supervisor') ?? 0);

        return $sup > 0 ? $sup : null;
    }

    /**
     * Pruefer ermitteln: supervisor-Kette aufsteigen, ERSTER der die Anforderung erfuellt.
     * Fallback: direkter Supervisor (auch unqualifiziert). Kein Supervisor -> null.
     * Zyklen-/Tiefen-Schutz (visited + max 15).
     */
    private function resolveReviewer(int $employeeId, int $requiredSort): ?int
    {
        $direct = $this->directSupervisor($employeeId);
        if ($direct === null) {
            return null; // kein Supervisor -> reported ohne Pruefer
        }

        $current = $direct;
        $visited = [$employeeId];
        $depth = 0;

        while ($current !== null && $current > 0 && !in_array($current, $visited, true) && $depth < 15) {
            $visited[] = $current;

            $sort = $this->employeeQualSort($current);
            if ($sort !== null && $sort <= $requiredSort) {
                return $current; // ERSTER der die Anforderung erfuellt
            }

            $current = $this->directSupervisor($current);
            $depth++;
        }

        return $direct; // Fallback: direkter Supervisor (auch unqualifiziert)
    }

    private function storePlannerItemReportMirror(
        int $plannerItemId,
        int $employeeId,
        string $reportText,
        array $data,
        Carbon $now
    ): void {
        if (!Schema::hasTable('planner_item_comments')) {
            return;
        }

        $fk = $this->firstExistingColumn('planner_item_comments', ['planner_item_id', 'item_id']);

        if (!$fk) {
            return;
        }

        $insert = [
            $fk => $plannerItemId,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if ($this->safeColumn('planner_item_comments', 'employee_id')) {
            $insert['employee_id'] = $employeeId;
        } elseif ($this->safeColumn('planner_item_comments', 'created_by_employee_id')) {
            $insert['created_by_employee_id'] = $employeeId;
        } elseif ($this->safeColumn('planner_item_comments', 'created_by')) {
            $insert['created_by'] = $employeeId;
        }

        if ($this->safeColumn('planner_item_comments', 'comment')) {
            $insert['comment'] = $reportText;
        } elseif ($this->safeColumn('planner_item_comments', 'body')) {
            $insert['body'] = $reportText;
        } elseif ($this->safeColumn('planner_item_comments', 'note')) {
            $insert['note'] = $reportText;
        } elseif ($this->safeColumn('planner_item_comments', 'message')) {
            $insert['message'] = $reportText;
        }

        if ($this->safeColumn('planner_item_comments', 'type')) {
            $insert['type'] = 'report';
        }

        if ($this->safeColumn('planner_item_comments', 'meta')) {
            $insert['meta'] = json_encode([
                'source' => 'nuriva_mobile',
                'next_step' => $data['next_step'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'lat' => $data['lat'] ?? null,
                'lng' => $data['lng'] ?? null,
                'accuracy' => $data['accuracy'] ?? null,
            ]);
        }

        DB::table('planner_item_comments')->insert($insert);
    }

    private function storePlannerSourceReport(
        object $plannerItem,
        int $employeeId,
        string $reportText,
        array $data,
        Carbon $now
    ): void {
        $sourceType = strtolower(trim((string) ($plannerItem->source_type ?? '')));
        $sourceId = (int) ($plannerItem->source_id ?? 0);

        if ($sourceId <= 0) {
            return;
        }

        if ($sourceType === 'personal_task' && Schema::hasTable('personal_task_comments')) {
            DB::table('personal_task_comments')->insert([
                'task_id' => $sourceId,
                'comment_by' => $employeeId,
                'comment' => $reportText,
                'status' => 'report',
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return;
        }

        if ($sourceType === 'appointment' && Schema::hasTable('appointment_reports')) {
            DB::table('appointment_reports')->insert([
                'employee_id' => $employeeId,
                'appointment_id' => $sourceId,
                'task_id' => (int) $plannerItem->id,
                'type' => 'nuriva_mobile',
                'report' => $reportText,
                'report_date' => $now->toDateString(),
                'next_step' => $data['next_step'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'report_by' => $employeeId,
                'comments' => json_encode(['items' => []]),
                'meta' => json_encode([
                    'source' => 'nuriva_mobile',
                    'planner_item_id' => (int) $plannerItem->id,
                    'lat' => $data['lat'] ?? null,
                    'lng' => $data['lng'] ?? null,
                    'accuracy' => $data['accuracy'] ?? null,
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return;
        }

        if (in_array($sourceType, ['ticket', 'ticket_task'], true) && Schema::hasTable('ticket_reports')) {
            $ticketId = $sourceId;

            if ($sourceType === 'ticket_task' && Schema::hasTable('ticket_tasks')) {
                $task = DB::table('ticket_tasks')->where('id', $sourceId)->first();
                $ticketId = (int) ($task->problem_id ?? $task->ticket_id ?? $task->problem_task_id ?? 0);
            }

            if ($ticketId > 0) {
                DB::table('ticket_reports')->insert([
                    'ticket_id' => $ticketId,
                    'employee_id' => $employeeId,
                    'customer_id' => $plannerItem->project_customer_id ?? $plannerItem->plan_customer_id ?? null,
                    'alternative_id' => $plannerItem->project_alternative_id ?? null,
                    'product_id' => $plannerItem->project_product_id ?? null,
                    'title' => 'Nuriva Bericht',
                    'report' => $reportText,
                    'language' => 'de',
                    'report_date' => $now,
                    'likes' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            return;
        }

        if (
            in_array($sourceType, ['kanban_task', 'phase_activity', 'task_phase', 'customer_phase', 'planner'], true)
            && Schema::hasTable('customer_reports')
        ) {
            DB::table('customer_reports')->insert([
                'customer_id' => $plannerItem->project_customer_id ?? $plannerItem->plan_customer_id ?? null,
                'alternative_id' => $plannerItem->project_alternative_id ?? null,
                'product_id' => $plannerItem->project_product_id ?? null,
                'report_by' => $employeeId,
                'stage' => $sourceType,
                'report' => $reportText,
                'report_details' => json_encode([
                    'source' => 'nuriva_mobile',
                    'planner_item_id' => (int) $plannerItem->id,
                    'source_type' => $sourceType,
                    'source_id' => $sourceId,
                    'next_step' => $data['next_step'] ?? null,
                    'due_date' => $data['due_date'] ?? null,
                    'lat' => $data['lat'] ?? null,
                    'lng' => $data['lng'] ?? null,
                    'accuracy' => $data['accuracy'] ?? null,
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function markPlannerSourceDone(
        object $plannerItem,
        int $employeeId,
        string $reportText,
        ?string $noteText,
        Carbon $now
    ): void {
        $sourceType = strtolower(trim((string) ($plannerItem->source_type ?? '')));
        $sourceId = (int) ($plannerItem->source_id ?? 0);

        if ($sourceId <= 0) {
            return;
        }

        if ($sourceType === 'personal_task' && Schema::hasTable('personal_tasks')) {
            $updates = ['updated_at' => $now];

            if ($this->safeColumn('personal_tasks', 'task_status')) {
                $updates['task_status'] = 'completed';
            }

            if ($this->safeColumn('personal_tasks', 'status')) {
                $updates['status'] = 'completed';
            }

            if ($this->safeColumn('personal_tasks', 'progress')) {
                $updates['progress'] = 100;
            }

            if ($this->safeColumn('personal_tasks', 'archived_at')) {
                $updates['archived_at'] = $now;
            }

            DB::table('personal_tasks')->where('id', $sourceId)->update($updates);
            return;
        }

        if ($sourceType === 'appointment' && Schema::hasTable('main_appointments')) {
            $updates = ['updated_at' => $now];

            if ($this->safeColumn('main_appointments', 'status')) {
                $updates['status'] = 'done';
            }

            if ($this->safeColumn('main_appointments', 'is_report')) {
                $updates['is_report'] = 1;
            }

            if ($this->safeColumn('main_appointments', 'report')) {
                $updates['report'] = $reportText;
            }

            if ($this->safeColumn('main_appointments', 'report_date')) {
                $updates['report_date'] = $now->toDateString();
            }

            if ($this->safeColumn('main_appointments', 'report_by')) {
                $updates['report_by'] = $employeeId;
            }

            DB::table('main_appointments')->where('id', $sourceId)->update($updates);
            return;
        }

        if ($sourceType === 'ticket' && Schema::hasTable('problems')) {
            $updates = ['updated_at' => $now];

            if ($this->safeColumn('problems', 'status')) {
                $updates['status'] = 'end';
            }

            DB::table('problems')->where('id', $sourceId)->update($updates);
            return;
        }

        if ($sourceType === 'ticket_task' && Schema::hasTable('ticket_tasks')) {
            $updates = ['updated_at' => $now];

            if ($this->safeColumn('ticket_tasks', 'status')) {
                $updates['status'] = 'done';
            }

            if ($this->safeColumn('ticket_tasks', 'is_done')) {
                $updates['is_done'] = 1;
            }

            if ($this->safeColumn('ticket_tasks', 'done_at')) {
                $updates['done_at'] = $now;
            }

            if ($this->safeColumn('ticket_tasks', 'done_by')) {
                $updates['done_by'] = $employeeId;
            }

            DB::table('ticket_tasks')->where('id', $sourceId)->update($updates);
            return;
        }

        if ($sourceType === 'kanban_task' && Schema::hasTable('kanban_lead_tasks')) {
            $updates = ['updated_at' => $now];

            if ($this->safeColumn('kanban_lead_tasks', 'status')) {
                $updates['status'] = 'done';
            }

            if ($this->safeColumn('kanban_lead_tasks', 'done_at')) {
                $updates['done_at'] = $now;
            }

            if ($this->safeColumn('kanban_lead_tasks', 'done_by_employee_id')) {
                $updates['done_by_employee_id'] = $employeeId;
            }

            DB::table('kanban_lead_tasks')->where('id', $sourceId)->update($updates);
            return;
        }

        if ($sourceType === 'phase_activity' && Schema::hasTable('customer_histories')) {
            $exists = DB::table('customer_histories')
                ->where('activity_id', $sourceId)
                ->where('customer_id', $plannerItem->project_customer_id ?? $plannerItem->plan_customer_id)
                ->when($plannerItem->project_product_id ?? null, fn($q) => $q->where('product_id', $plannerItem->project_product_id))
                ->first();

            if ($exists) {
                $updates = ['updated_at' => $now];

                if ($this->safeColumn('customer_histories', 'is_done')) {
                    $updates['is_done'] = 1;
                }

                if ($this->safeColumn('customer_histories', 'done_by')) {
                    $updates['done_by'] = $employeeId;
                }

                if ($this->safeColumn('customer_histories', 'done_date')) {
                    $updates['done_date'] = $now;
                }

                DB::table('customer_histories')->where('id', $exists->id)->update($updates);
            } else {
                $insert = [
                    'activity_id' => $sourceId,
                    'customer_id' => $plannerItem->project_customer_id ?? $plannerItem->plan_customer_id,
                    'product_id' => $plannerItem->project_product_id ?? null,
                    'is_done' => 1,
                    'done_by' => $employeeId,
                    'done_date' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if ($this->safeColumn('customer_histories', 'alternative_id')) {
                    $insert['alternative_id'] = $plannerItem->project_alternative_id ?? null;
                }

                DB::table('customer_histories')->insert($insert);
            }
        }
    }

    private function storePlannerItemStatusHistory(
        int $plannerItemId,
        int $employeeId,
        string $oldStatus,
        string $newStatus,
        ?string $note,
        Carbon $now
    ): void {
        if (!Schema::hasTable('planner_item_status_histories')) {
            return;
        }

        DB::table('planner_item_status_histories')->insert([
            'planner_item_id' => $plannerItemId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'status_label' => 'Erledigt',
            'changed_by_employee_id' => $employeeId,
            'note' => $note,
            'changed_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

}
