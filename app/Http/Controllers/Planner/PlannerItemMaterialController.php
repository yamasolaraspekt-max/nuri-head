<?php

namespace App\Http\Controllers\Planner;

use App\Events\PlannerRealtimeEvent;
use App\Http\Controllers\Controller;
use App\Models\PlannerItem;
use App\Notifications\PlannerToastNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PlannerItemMaterialController extends Controller
{
    private string $requestTable = 'planner_item_material_requests';

    /**
     * GET /api/planner/items/{item}/materials
     *
     * This returns all employee/Nuriva material requests for one planner item.
     */
    public function index(Request $request, PlannerItem $item): JsonResponse
    {
        $rows = $this->materialRequestRowsForItem((int) $item->id);

        return response()->json([
            'ok' => true,
            'item_id' => (int) $item->id,
            'materials' => $rows,
            'data' => $rows,
            'summary' => [
                'total' => count($rows),
                'requested_open' => collect($rows)
                    ->filter(fn($row) => in_array(strtolower((string) ($row['status'] ?? '')), ['requested', 'open', 'pending', 'new'], true))
                    ->count(),
                'accepted' => collect($rows)
                    ->filter(fn($row) => strtolower((string) ($row['status'] ?? '')) === 'accepted')
                    ->count(),
                'rejected' => collect($rows)
                    ->filter(fn($row) => strtolower((string) ($row['status'] ?? '')) === 'rejected')
                    ->count(),
            ],
        ]);
    }

    /**
     * POST /api/planner/items/{item}/materials
     *
     * Called from Nuriva when an employee requests:
     * - a custom material
     * - a Master Set as material request
     *
     * IMPORTANT:
     * The insert is filtered with Schema::hasColumn(), so optional/missing columns
     * like article_no, master_set_id, accepted_at, etc. can never break the request.
     */
    public function store(Request $request, PlannerItem $item): JsonResponse
    {
        if (!Schema::hasTable($this->requestTable)) {
            return response()->json([
                'ok' => false,
                'message' => 'Tabelle planner_item_material_requests fehlt. Bitte Migration ausführen.',
            ], 500);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'article_name' => ['nullable', 'string', 'max:255'],
            'article_no' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],

            'quantity' => ['nullable', 'numeric', 'min:0.001', 'max:999999'],
            'qty' => ['nullable', 'numeric', 'min:0.001', 'max:999999'],
            'unit' => ['nullable', 'string', 'max:50'],
            'measure' => ['nullable', 'string', 'max:50'],
            'measure_unit' => ['nullable', 'string', 'max:50'],

            'priority' => ['nullable', 'string', 'max:50'],
            'needed_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:5000'],
            'comment' => ['nullable', 'string', 'max:5000'],

            'origin_type' => ['nullable', 'string', 'max:100'],
            'source_type' => ['nullable', 'string', 'max:100'],
            'request_type' => ['nullable', 'string', 'max:100'],

            'master_set_id' => ['nullable', 'integer'],
            'requested_master_set_id' => ['nullable', 'integer'],

            'requested_by_employee_id' => ['nullable', 'integer'],
            'customer_id' => ['nullable', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'lead_product_list_id' => ['nullable', 'integer'],

            'active' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $name = trim((string) (
            $data['name']
            ?? $data['title']
            ?? $data['article_name']
            ?? ''
        ));

        if ($name === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Bitte Materialname eingeben.',
            ], 422);
        }

        $context = $this->resolveItemContext($item);

        $requestedBy = $this->normalizeNullableInt($data['requested_by_employee_id'] ?? null)
            ?? $this->authEmployeeId();

        $quantity = (float) ($data['quantity'] ?? $data['qty'] ?? 1);
        if ($quantity <= 0) {
            $quantity = 1;
        }

        $unit = trim((string) ($data['unit'] ?? $data['measure'] ?? $data['measure_unit'] ?? 'Stk'));
        if ($unit === '') {
            $unit = 'Stk';
        }

        $masterSetId = $this->normalizeNullableInt(
            $data['master_set_id']
            ?? $data['requested_master_set_id']
            ?? null
        );

        $requestType = trim((string) ($data['request_type'] ?? 'manual'));
        if ($requestType === '') {
            $requestType = $masterSetId ? 'master_set' : 'manual';
        }

        $sourceType = trim((string) ($data['source_type'] ?? 'manual_request'));
        if ($sourceType === '') {
            $sourceType = $masterSetId ? 'master_set_request' : 'manual_request';
        }

        $originType = trim((string) ($data['origin_type'] ?? 'employee_request'));
        if ($originType === '') {
            $originType = 'employee_request';
        }

        $trace = (string) Str::uuid();

        $insert = [
            'planner_item_id' => (int) $item->id,
            'planner_plan_id' => $context['plan_id'],
            'lead_product_list_id' => $this->normalizeNullableInt($data['lead_product_list_id'] ?? null) ?? $context['lead_product_list_id'],

            'customer_id' => $this->normalizeNullableInt($data['customer_id'] ?? null) ?? $context['customer_id'],
            'alternative_id' => $this->normalizeNullableInt($data['alternative_id'] ?? null) ?? $context['alternative_id'],
            'product_id' => $this->normalizeNullableInt($data['product_id'] ?? null) ?? $context['product_id'],

            'requested_by_employee_id' => $requestedBy,

            'name' => $name,
            'title' => $data['title'] ?? $name,
            'article_name' => $data['article_name'] ?? $name,
            'article_no' => $data['article_no'] ?? null,
            'description' => $data['description'] ?? null,

            'quantity' => $quantity,
            'qty' => $quantity,
            'unit' => $unit,
            'measure' => $unit,
            'measure_unit' => $unit,

            'priority' => $data['priority'] ?? 'normal',
            'needed_at' => $data['needed_at'] ?? null,

            'note' => $data['note'] ?? $data['comment'] ?? null,
            'comment' => $data['comment'] ?? $data['note'] ?? null,

            'status' => 'requested',
            'request_status' => 'requested',

            'origin_type' => $originType,
            'source_type' => $sourceType,
            'request_type' => $requestType,

            'master_set_id' => $masterSetId,
            'requested_master_set_id' => $masterSetId,

            'active' => 0,
            'is_active' => 0,

            'meta' => json_encode([
                'trace' => $trace,
                'source' => 'nuriva_mobile',
                'source_type' => $item->source_type,
                'source_id' => $item->source_id,
                'request_type' => $requestType,
                'origin_type' => $originType,
                'master_set_id' => $masterSetId,
                'requested_master_set_id' => $masterSetId,
                'sound' => true,
            ], JSON_UNESCAPED_UNICODE),

            'created_at' => now(),
            'updated_at' => now(),
        ];

        $insert = $this->filterExistingColumns($this->requestTable, $insert);

        if (!array_key_exists('planner_item_id', $insert)) {
            return response()->json([
                'ok' => false,
                'message' => 'Spalte planner_item_id fehlt in planner_item_material_requests.',
            ], 500);
        }

        $id = DB::table($this->requestTable)->insertGetId($insert);

        $material = $this->materialRequestRow($id);
        $this->pushMaterialRequestNotification($item, $material, $context, $trace, $requestedBy);

        return response()->json([
            'ok' => true,
            'message' => 'Materialanfrage wurde gespeichert.',
            'trace' => $trace,
            'material' => $material,
            'materials' => [$material],
            'event' => 'planner.material.requested',
        ]);
    }

    private function materialRequestRowsForItem(int $plannerItemId): array
    {
        if (!Schema::hasTable($this->requestTable)) {
            return [];
        }

        if (!$this->columnExists($this->requestTable, 'planner_item_id')) {
            return [];
        }

        $query = DB::table($this->requestTable . ' as pmr')
            ->where('pmr.planner_item_id', $plannerItemId);

        if ($this->columnExists($this->requestTable, 'deleted_at')) {
            $query->whereNull('pmr.deleted_at');
        }

        $query = $this->joinRequesterEmployee($query);

        return $query
            ->orderByDesc('pmr.id')
            ->get($this->materialRequestSelectColumns())
            ->map(fn($row) => $this->formatMaterialRequestRow($row))
            ->values()
            ->all();
    }

    private function materialRequestRow(int $id): array
    {
        if (!Schema::hasTable($this->requestTable)) {
            return [];
        }

        $query = DB::table($this->requestTable . ' as pmr')
            ->where('pmr.id', $id);

        $query = $this->joinRequesterEmployee($query);

        $row = $query->first($this->materialRequestSelectColumns());

        return $row ? $this->formatMaterialRequestRow($row) : [];
    }

    private function joinRequesterEmployee($query)
    {
        if (
            Schema::hasTable('employees')
            && $this->columnExists($this->requestTable, 'requested_by_employee_id')
            && $this->columnExists('employees', 'id')
        ) {
            return $query->leftJoin('employees as e', 'e.id', '=', 'pmr.requested_by_employee_id');
        }

        return $query;
    }

    private function materialRequestSelectColumns(): array
    {
        $columns = ['pmr.*'];

        if (
            Schema::hasTable('employees')
            && $this->columnExists($this->requestTable, 'requested_by_employee_id')
            && $this->columnExists('employees', 'id')
        ) {
            $columns[] = DB::raw($this->employeeFullNameSql('e') . ' as requested_by_name');
        } else {
            $columns[] = DB::raw('NULL as requested_by_name');
        }

        return $columns;
    }

    private function employeeFullNameSql(string $alias): string
    {
        $parts = [];

        foreach (['title', 'name', 'midname', 'lastname'] as $column) {
            if ($this->columnExists('employees', $column)) {
                $parts[] = "NULLIF(TRIM(COALESCE({$alias}.`{$column}`, '')), '')";
            }
        }

        if (empty($parts)) {
            return "NULL";
        }

        return 'TRIM(CONCAT_WS(" ", ' . implode(', ', $parts) . '))';
    }

    private function formatMaterialRequestRow(object $row): array
    {
        $rowArray = (array) $row;

        $meta = json_decode((string) ($rowArray['meta'] ?? ''), true);
        $meta = is_array($meta) ? $meta : [];

        $id = $this->normalizeNullableInt($rowArray['id'] ?? null) ?? 0;
        $plannerItemId = $this->normalizeNullableInt($rowArray['planner_item_id'] ?? null) ?? 0;
        $name = trim((string) (
            $rowArray['name']
            ?? $rowArray['title']
            ?? $rowArray['article_name']
            ?? 'Material Anfrage'
        ));

        $status = strtolower(trim((string) ($rowArray['status'] ?? $rowArray['request_status'] ?? 'requested')));
        if ($status === '') {
            $status = 'requested';
        }

        $quantity = (float) ($rowArray['quantity'] ?? $rowArray['qty'] ?? 1);
        if ($quantity <= 0) {
            $quantity = 1;
        }

        $unit = trim((string) ($rowArray['unit'] ?? $rowArray['measure_unit'] ?? $rowArray['measure'] ?? 'Stk'));
        if ($unit === '') {
            $unit = 'Stk';
        }

        $requestedById = $this->normalizeNullableInt($rowArray['requested_by_employee_id'] ?? null);
        $requestedByName = trim((string) ($rowArray['requested_by_name'] ?? ''));

        return [
            'id' => $id,
            'request_id' => $id,
            'request_key' => 'employee_request:' . $id,

            'type' => 'employee_request',
            'origin_type' => $rowArray['origin_type'] ?? $meta['origin_type'] ?? 'employee_request',
            'origin' => 'employee_request',
            'source' => $meta['source'] ?? 'nuriva_mobile',
            'source_type' => $rowArray['source_type'] ?? $meta['source_type'] ?? 'material_request',
            'request_type' => $rowArray['request_type'] ?? $meta['request_type'] ?? ($meta['master_set_id'] ?? null ? 'master_set' : 'manual'),

            'is_request' => true,
            'is_material_request' => true,
            'is_employee_request' => true,
            'is_active' => false,
            'active' => false,

            'planner_item_id' => $plannerItemId,
            'planner_plan_id' => $this->normalizeNullableInt($rowArray['planner_plan_id'] ?? null),
            'lead_product_list_id' => $this->normalizeNullableInt($rowArray['lead_product_list_id'] ?? null),
            'customer_id' => $this->normalizeNullableInt($rowArray['customer_id'] ?? null),
            'alternative_id' => $this->normalizeNullableInt($rowArray['alternative_id'] ?? null),
            'product_id' => $this->normalizeNullableInt($rowArray['product_id'] ?? null),

            'master_set_id' => $this->normalizeNullableInt($rowArray['master_set_id'] ?? $rowArray['requested_master_set_id'] ?? $meta['master_set_id'] ?? null),
            'requested_master_set_id' => $this->normalizeNullableInt($rowArray['requested_master_set_id'] ?? $rowArray['master_set_id'] ?? $meta['requested_master_set_id'] ?? $meta['master_set_id'] ?? null),

            'name' => $name,
            'title' => $name,
            'product' => $name,
            'article_name' => $rowArray['article_name'] ?? $name,
            'article_no' => $rowArray['article_no'] ?? null,
            'description' => $rowArray['description'] ?? null,

            'qty' => $quantity,
            'quantity' => $quantity,
            'unit' => $unit,
            'measure' => $unit,
            'measure_unit' => $unit,

            'priority' => $rowArray['priority'] ?? 'normal',
            'needed_at' => $rowArray['needed_at'] ?? null,
            'note' => $rowArray['note'] ?? $rowArray['comment'] ?? null,
            'comment' => $rowArray['comment'] ?? $rowArray['note'] ?? null,

            'status' => $status,
            'request_status' => $status,
            'status_label' => $this->requestStatusLabel($status),

            'requested_by_employee_id' => $requestedById,
            'requested_by_name' => $requestedByName !== '' ? $requestedByName : ($requestedById ? ('Mitarbeiter #' . $requestedById) : null),

            'accepted_at' => $rowArray['accepted_at'] ?? null,
            'accepted_by_employee_id' => $this->normalizeNullableInt($rowArray['accepted_by_employee_id'] ?? null),
            'rejected_at' => $rowArray['rejected_at'] ?? null,
            'rejected_by_employee_id' => $this->normalizeNullableInt($rowArray['rejected_by_employee_id'] ?? null),
            'rejection_note' => $rowArray['rejection_note'] ?? null,

            'created_at' => $rowArray['created_at'] ?? null,
            'updated_at' => $rowArray['updated_at'] ?? null,
            'meta' => $meta,
        ];
    }

    private function requestStatusLabel(string $status): string
    {
        return match (strtolower(trim($status))) {
            'accepted', 'approved', 'angenommen' => 'Angenommen',
            'rejected', 'declined', 'abgelehnt' => 'Abgelehnt',
            'done', 'completed' => 'Erledigt',
            default => 'Offen',
        };
    }

    private function resolveItemContext(PlannerItem $item): array
    {
        $plan = null;
        $project = null;

        if (!empty($item->plan_id) && Schema::hasTable('planner_plans')) {
            $plan = DB::table('planner_plans')
                ->where('id', (int) $item->plan_id)
                ->first();
        }

        if ($plan && !empty($plan->project_id) && Schema::hasTable('lead_product_lists')) {
            $projectQuery = DB::table('lead_product_lists')
                ->where('id', (int) $plan->project_id);

            if ($this->columnExists('lead_product_lists', 'deleted_at')) {
                $projectQuery->whereNull('deleted_at');
            }

            $project = $projectQuery->first();
        }

        return [
            'plan_id' => $item->plan_id ? (int) $item->plan_id : null,
            'lead_product_list_id' => $project?->id ? (int) $project->id : null,
            'customer_id' => $project?->customer_id ? (int) $project->customer_id : ($plan?->customer_id ? (int) $plan->customer_id : null),
            'alternative_id' => $project?->alternative_id ? (int) $project->alternative_id : null,
            'product_id' => $project?->product_id ? (int) $project->product_id : null,
            'pm_employee_id' => $project?->employee_id ? (int) $project->employee_id : null,
        ];
    }

    private function pushMaterialRequestNotification(PlannerItem $item, array $material, array $context, string $trace, ?int $requestedBy = null): void
    {
        $employeeIds = [];

        if (!empty($context['pm_employee_id'])) {
            $employeeIds[] = (int) $context['pm_employee_id'];
        }

        if (Schema::hasTable('planner_item_employees') && Schema::hasColumn('planner_item_employees', 'planner_item_id')) {
            $employeeIds = array_merge($employeeIds, DB::table('planner_item_employees')
                ->where('planner_item_id', (int) $item->id)
                ->pluck('employee_id')
                ->map(fn($id) => (int) $id)
                ->all());
        }

        foreach (['created_by_employee_id', 'scheduled_by_employee_id', 'employee_id'] as $column) {
            if (isset($item->{$column}) && is_numeric($item->{$column}) && (int) $item->{$column} > 0) {
                $employeeIds[] = (int) $item->{$column};
            }
        }

        $requestedBy = $requestedBy ?: ($material['requested_by_employee_id'] ?? null);

        /*
        |--------------------------------------------------------------------------
        | Do not send a personal notification back to the employee who requested it.
        | The public planner.materials and planner.plan channels still update open pages.
        |--------------------------------------------------------------------------
        */
        if ($requestedBy) {
            $employeeIds = array_values(array_filter(
                $employeeIds,
                fn($id) => (int) $id !== (int) $requestedBy
            ));
        }

        $employeeIds = array_values(array_unique(array_filter(array_map('intval', $employeeIds))));

        $employeeName = $material['requested_by_name']
            ?? ($requestedBy ? ('Mitarbeiter #' . (int) $requestedBy) : 'Nuriva');

        $materialName = $material['name'] ?? $material['article_name'] ?? 'Material';
        $quantityLabel = trim(($material['quantity'] ?? $material['qty'] ?? '') . ' ' . ($material['unit'] ?? ''));

        $payload = [
            'trace' => $trace,
            'type' => 'planner_material_request',
            'event' => 'planner.material.requested',
            'title' => 'Neue Materialanfrage',
            'message' => $employeeName . ' hat ' . $materialName . ($quantityLabel !== '' ? (' (' . $quantityLabel . ')') : '') . ' angefragt.',

            'planner_item_id' => (int) $item->id,
            'planner_plan_id' => $context['plan_id'],
            'project_id' => $context['lead_product_list_id'],
            'customer_id' => $context['customer_id'],

            'requested_by_employee_id' => $requestedBy,
            'requested_by_employee_name' => $employeeName,

            'material' => $material,
            'sound' => true,
            'url' => $context['plan_id']
                ? url('/planner/projects?plan_id=' . (int) $context['plan_id'])
                : url('/planner/projects'),
            'created_at' => now()->toDateTimeString(),
        ];

        $channels = ['planner.materials'];

        if (!empty($context['plan_id'])) {
            $channels[] = 'planner.plan.' . (int) $context['plan_id'];
        }

        foreach ($employeeIds as $employeeId) {
            $channels[] = 'planner.employee.' . (int) $employeeId;
        }

        event(new PlannerRealtimeEvent(
            array_values(array_unique($channels)),
            'planner.material.requested',
            $payload
        ));

        if (!empty($employeeIds) && class_exists(PlannerToastNotification::class)) {
            $users = \App\Models\User::query()
                ->whereIn('name', array_map('strval', $employeeIds))
                ->get();

            if ($users->isNotEmpty()) {
                Notification::send($users, new PlannerToastNotification($payload));
            }
        }
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

    private function normalizeNullableInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function columnExists(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    private function filterExistingColumns(string $table, array $data): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        return collect($data)
            ->filter(fn($value, $column) => Schema::hasColumn($table, (string) $column))
            ->all();
    }
}
