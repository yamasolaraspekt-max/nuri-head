<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MasterSetApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Manual API Auth
    |--------------------------------------------------------------------------
    | .env:
    | MASTER_SET_API_USER=master_api
    | MASTER_SET_API_PASSWORD=change_this_password
    |--------------------------------------------------------------------------
    */

    private function authApi(Request $request): array
    {
        $apiUser = $request->header('X-API-USER')
            ?: $request->input('api_user')
            ?: $request->getUser();

        $apiPassword = $request->header('X-API-PASSWORD')
            ?: $request->input('api_password')
            ?: $request->getPassword();

        $expectedUser = env('MASTER_SET_API_USER');
        $expectedPassword = env('MASTER_SET_API_PASSWORD');

        if (!$expectedUser || !$expectedPassword) {
            return [
                'ok' => false,
                'response' => response()->json([
                    'ok' => false,
                    'message' => 'API credentials are not configured.',
                    'debug' => config('app.debug') ? [
                        'has_MASTER_SET_API_USER' => !empty($expectedUser),
                        'has_MASTER_SET_API_PASSWORD' => !empty($expectedPassword),
                    ] : null,
                ], 500),
            ];
        }

        if (!$apiUser || !$apiPassword) {
            return [
                'ok' => false,
                'response' => response()->json([
                    'ok' => false,
                    'message' => 'API username and password are required.',
                ], 401),
            ];
        }

        if (
            !hash_equals((string) $expectedUser, (string) $apiUser) ||
            !hash_equals((string) $expectedPassword, (string) $apiPassword)
        ) {
            return [
                'ok' => false,
                'response' => response()->json([
                    'ok' => false,
                    'message' => 'Invalid API username or password.',
                ], 401),
            ];
        }

        return [
            'ok' => true,
            'api_user' => $apiUser,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/secure/master-sets
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $auth = $this->authApi($request);

        if (!$auth['ok']) {
            return $auth['response'];
        }

        $apiUser = $auth['api_user'];

        $validator = Validator::make($request->all(), [
            'article_group_id' => ['nullable', 'integer'],
            'group_id'         => ['nullable', 'integer'],
            'status'           => ['nullable', 'string', 'max:80'],
            'search'           => ['nullable', 'string', 'max:255'],
            'per_page'         => ['nullable', 'integer', 'min:1', 'max:100'],
            'page'             => ['nullable', 'integer', 'min:1'],
            'with_deleted'     => ['nullable', 'boolean'],
            'debug'            => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $filters = $validator->validated();

        try {
            $perPage = (int) ($filters['per_page'] ?? 25);
            $page = (int) ($filters['page'] ?? 1);
            $withDeleted = (bool) ($filters['with_deleted'] ?? false);

            $query = $this->baseMasterSetQuery($withDeleted);

            if (!empty($filters['article_group_id'])) {
                $query->where('ms.article_group_id', (int) $filters['article_group_id']);
            }

            if (!empty($filters['status'])) {
                $query->where('ms.status', $filters['status']);
            }

            if (!empty($filters['search'])) {
                $search = trim($filters['search']);

                $query->where(function ($q) use ($search) {
                    $q->where('ms.name', 'LIKE', "%{$search}%")
                        ->orWhere('ms.description', 'LIKE', "%{$search}%");

                    if (Schema::hasColumn('article_groups', 'article_group')) {
                        $q->orWhere('ag.article_group', 'LIKE', "%{$search}%");
                    }
                });
            }

            if (!empty($filters['group_id'])) {
                $groupId = (int) $filters['group_id'];

                $query->whereExists(function ($sub) use ($groupId) {
                    $sub->select(DB::raw(1))
                        ->from('master_set_group_master_set as pivot')
                        ->whereColumn('pivot.master_set_id', 'ms.id')
                        ->where('pivot.master_set_group_id', $groupId);
                });
            }

            $total = (clone $query)->count();

            $masterSets = $query
                ->orderBy('ms.article_group_id')
                ->orderBy('ms.name')
                ->forPage($page, $perPage)
                ->get();

            if ($request->boolean('debug')) {
                return response()->json([
                    'ok' => true,
                    'debug_step' => 'base_query_done',
                    'auth' => [
                        'type' => 'manual',
                        'api_user' => $apiUser,
                    ],
                    'total' => $total,
                    'count_current_page' => $masterSets->count(),
                    'ids' => $masterSets->pluck('id')->values(),
                    'rows' => $masterSets,
                ]);
            }

            if ($masterSets->isEmpty()) {
                return response()->json([
                    'ok' => true,
                    'data' => [],
                    'meta' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => $total,
                        'last_page' => max(1, (int) ceil($total / $perPage)),
                    ],
                ]);
            }

            $masterSetIds = $masterSets
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            $payload = $this->buildPayload($masterSets, $masterSetIds);

            return response()->json([
                'ok' => true,
                'data' => $payload,
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => max(1, (int) ceil($total / $perPage)),
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('MasterSet API index failed', [
                'auth_type' => 'manual',
                'api_user' => $apiUser ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => substr($e->getTraceAsString(), 0, 3000),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Master sets could not be loaded.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'line' => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/secure/master-sets/{id}
    |--------------------------------------------------------------------------
    */

    public function show(Request $request, $id)
    {
        $auth = $this->authApi($request);

        if (!$auth['ok']) {
            return $auth['response'];
        }

        $apiUser = $auth['api_user'];

        if (!is_numeric($id)) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid master set id.',
            ], 422);
        }

        try {
            $withDeleted = $request->boolean('with_deleted');

            $masterSet = $this->baseMasterSetQuery($withDeleted)
                ->where('ms.id', (int) $id)
                ->first();

            if (!$masterSet) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Master set not found.',
                ], 404);
            }

            $payload = $this->buildPayload(collect([$masterSet]), [(int) $masterSet->id]);

            return response()->json([
                'ok' => true,
                'data' => $payload[0] ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('MasterSet API show failed', [
                'auth_type' => 'manual',
                'api_user' => $apiUser ?? null,
                'master_set_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => substr($e->getTraceAsString(), 0, 3000),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Master set could not be loaded.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'line' => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Debug Endpoint
    |--------------------------------------------------------------------------
    */

    public function debug(Request $request)
    {
        $auth = $this->authApi($request);

        if (!$auth['ok']) {
            return $auth['response'];
        }

        $apiUser = $auth['api_user'];

        try {
            $debug = [
                'controller_reached' => true,

                'auth' => [
                    'type' => 'manual',
                    'api_user' => $apiUser,
                ],

                'table_exists' => [
                    'master_sets' => Schema::hasTable('master_sets'),
                    'article_groups' => Schema::hasTable('article_groups'),
                    'employees' => Schema::hasTable('employees'),
                    'master_set_components' => Schema::hasTable('master_set_components'),
                    'master_set_labor' => Schema::hasTable('master_set_labor'),
                    'master_set_tasks' => Schema::hasTable('master_set_tasks'),
                    'master_set_task_labors' => Schema::hasTable('master_set_task_labors'),
                    'master_set_checklists' => Schema::hasTable('master_set_checklists'),
                    'maintenance_checklists' => Schema::hasTable('maintenance_checklists'),
                    'maintenance_checklist_items' => Schema::hasTable('maintenance_checklist_items'),
                    'task_phases' => Schema::hasTable('task_phases'),
                    'phase_activities' => Schema::hasTable('phase_activities'),
                    'costing_sets' => Schema::hasTable('costing_sets'),
                ],

                'table_counts' => [
                    'master_sets_total' => Schema::hasTable('master_sets') ? DB::table('master_sets')->count() : null,
                    'master_sets_not_deleted' => Schema::hasTable('master_sets') && Schema::hasColumn('master_sets', 'deleted_at')
                        ? DB::table('master_sets')->whereNull('deleted_at')->count()
                        : null,
                    'article_groups_total' => Schema::hasTable('article_groups') ? DB::table('article_groups')->count() : null,
                    'components_total' => Schema::hasTable('master_set_components') ? DB::table('master_set_components')->count() : null,
                    'labor_total' => Schema::hasTable('master_set_labor') ? DB::table('master_set_labor')->count() : null,
                    'tasks_total' => Schema::hasTable('master_set_tasks') ? DB::table('master_set_tasks')->count() : null,
                    'checklists_total' => Schema::hasTable('master_set_checklists') ? DB::table('master_set_checklists')->count() : null,
                ],

                'columns' => [
                    'master_sets' => Schema::hasTable('master_sets') ? Schema::getColumnListing('master_sets') : [],
                    'master_set_components' => Schema::hasTable('master_set_components') ? Schema::getColumnListing('master_set_components') : [],
                    'departments' => Schema::hasTable('departments') ? Schema::getColumnListing('departments') : [],
                    'positions' => Schema::hasTable('positions') ? Schema::getColumnListing('positions') : [],
                    'position_qualifications' => Schema::hasTable('position_qualifications') ? Schema::getColumnListing('position_qualifications') : [],
                    'distributors' => Schema::hasTable('distributors') ? Schema::getColumnListing('distributors') : [],
                    'costing_sets' => Schema::hasTable('costing_sets') ? Schema::getColumnListing('costing_sets') : [],
                ],

                'first_master_sets_raw' => Schema::hasTable('master_sets')
                    ? DB::table('master_sets')->orderByDesc('id')->limit(10)->get()
                    : [],
            ];

            if (Schema::hasTable('master_sets')) {
                $debug['base_query_count_without_deleted_filter'] = $this->baseMasterSetQuery(true)->count();
                $debug['base_query_count_with_deleted_filter'] = $this->baseMasterSetQuery(false)->count();

                $debug['base_query_first_rows'] = $this->baseMasterSetQuery(false)
                    ->orderByDesc('ms.id')
                    ->limit(10)
                    ->get();
            }

            return response()->json([
                'ok' => true,
                'debug' => $debug,
            ]);

        } catch (\Throwable $e) {
            Log::error('MasterSet API debug failed', [
                'auth_type' => 'manual',
                'api_user' => $apiUser ?? null,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Debug failed.',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

    private function baseMasterSetQuery(bool $withDeleted = false)
    {
        $select = [
            'ms.id',
            'ms.article_group_id',
            'ms.name',
            'ms.description',
            'ms.responsible_department_position_id',
            'ms.status',
            'ms.creator_id',
            'ms.count_copy',
            'ms.count_offer',
            'ms.is_locked',
            'ms.main_total',
            'ms.sub_total',
            'ms.labor_total',
            'ms.total',
            'ms.costing_set_id',
            'ms.costing_rate_mode',
            'ms.costing_fallback',
            'ms.created_at',
            'ms.updated_at',
        ];

        $select[] = Schema::hasColumn('master_sets', 'task_phase_id')
            ? 'ms.task_phase_id'
            : DB::raw('NULL as task_phase_id');

        $select[] = Schema::hasColumn('master_sets', 'phase_activity_id')
            ? 'ms.phase_activity_id'
            : DB::raw('NULL as phase_activity_id');

        $select[] = Schema::hasColumn('article_groups', 'article_group')
            ? 'ag.article_group as article_group_name'
            : DB::raw('NULL as article_group_name');

        $select[] = Schema::hasColumn('article_groups', 'initial')
            ? 'ag.initial as article_group_initial'
            : DB::raw('NULL as article_group_initial');

        $select[] = Schema::hasColumn('article_groups', 'image')
            ? 'ag.image as article_group_image'
            : DB::raw('NULL as article_group_image');

        $select[] = Schema::hasColumn('article_groups', 'min_value')
            ? 'ag.min_value as article_group_min_value'
            : DB::raw('NULL as article_group_min_value');

        $select[] = Schema::hasColumn('article_groups', 'max_value')
            ? 'ag.max_value as article_group_max_value'
            : DB::raw('NULL as article_group_max_value');

        $select[] = Schema::hasColumn('employees', 'name')
            ? 'creator.name as creator_name'
            : DB::raw('NULL as creator_name');

        $select[] = Schema::hasColumn('employees', 'lastname')
            ? 'creator.lastname as creator_lastname'
            : DB::raw('NULL as creator_lastname');

        $select[] = Schema::hasColumn('employees', 'image')
            ? 'creator.image as creator_image'
            : DB::raw('NULL as creator_image');

        $query = DB::table('master_sets as ms')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'ms.article_group_id')
            ->leftJoin('employees as creator', 'creator.id', '=', 'ms.creator_id')
            ->select($select);

        if (!$withDeleted && Schema::hasColumn('master_sets', 'deleted_at')) {
            $query->whereNull('ms.deleted_at');
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Build Final Payload
    |--------------------------------------------------------------------------
    */

    private function buildPayload($masterSets, array $masterSetIds): array
    {
        $groupsByMasterSet = $this->loadGroups($masterSetIds);
        $componentsByMasterSet = $this->loadComponents($masterSetIds);
        $laborByMasterSet = $this->loadLabor($masterSetIds);
        $tasksByMasterSet = $this->loadTasks($masterSetIds);
        $checklistsByMasterSet = $this->loadChecklists($masterSetIds);

        $taskPhaseIds = $masterSets
            ->pluck('task_phase_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $activityIds = $masterSets
            ->pluck('phase_activity_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $costingSetIds = $masterSets
            ->pluck('costing_set_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $taskPhasesById = $this->loadTaskPhases($taskPhaseIds);
        $activitiesById = $this->loadPhaseActivities($activityIds);
        $costingSetsById = $this->loadCostingSets($costingSetIds);

        return $masterSets->map(function ($ms) use (
            $groupsByMasterSet,
            $componentsByMasterSet,
            $laborByMasterSet,
            $tasksByMasterSet,
            $checklistsByMasterSet,
            $taskPhasesById,
            $activitiesById,
            $costingSetsById
        ) {
            $id = (int) $ms->id;

            return [
                'id' => $id,

                'article_group_id' => $ms->article_group_id ? (int) $ms->article_group_id : null,
                'article_group' => $ms->article_group_id ? [
                    'id' => (int) $ms->article_group_id,
                    'name' => $ms->article_group_name ?? null,
                    'initial' => $ms->article_group_initial ?? null,
                    'image' => $this->resolveArticleGroupImageUrl($ms->article_group_image ?? null),
                    'min_value' => $this->decimal($ms->article_group_min_value ?? null),
                    'max_value' => $this->decimal($ms->article_group_max_value ?? null),
                ] : null,

                'name' => $ms->name,
                'description' => $ms->description,
                'status' => $ms->status,

                'is_locked' => (bool) ($ms->is_locked ?? false),
                'count_copy' => (int) ($ms->count_copy ?? 0),
                'count_offer' => (int) ($ms->count_offer ?? 0),

                'totals' => [
                    'main_total' => $this->decimal($ms->main_total ?? null),
                    'sub_total' => $this->decimal($ms->sub_total ?? null),
                    'labor_total' => $this->decimal($ms->labor_total ?? null),
                    'total' => $this->decimal($ms->total ?? null),
                ],

                'costing' => [
                    'costing_set_id' => $ms->costing_set_id ? (int) $ms->costing_set_id : null,
                    'costing_set' => $ms->costing_set_id && isset($costingSetsById[(int) $ms->costing_set_id])
                        ? $costingSetsById[(int) $ms->costing_set_id]
                        : null,
                    'costing_rate_mode' => $ms->costing_rate_mode,
                    'costing_fallback' => $ms->costing_fallback,
                ],

                'creator' => $ms->creator_id ? [
                    'id' => (int) $ms->creator_id,
                    'name' => trim(($ms->creator_name ?? '') . ' ' . ($ms->creator_lastname ?? '')),
                    'image' => $this->resolveEmployeePhotoUrl($ms->creator_image ?? null),
                ] : null,

                'task_phase' => !empty($ms->task_phase_id) && isset($taskPhasesById[(int) $ms->task_phase_id])
                    ? $taskPhasesById[(int) $ms->task_phase_id]
                    : null,

                'phase_activity' => !empty($ms->phase_activity_id) && isset($activitiesById[(int) $ms->phase_activity_id])
                    ? $activitiesById[(int) $ms->phase_activity_id]
                    : null,

                'groups' => $groupsByMasterSet[$id] ?? [],
                'components' => $componentsByMasterSet[$id] ?? [],
                'labor' => $laborByMasterSet[$id] ?? [],
                'tasks' => $tasksByMasterSet[$id] ?? [],
                'checklists' => $checklistsByMasterSet[$id] ?? [],

                'created_at' => $this->dateTime($ms->created_at ?? null),
                'updated_at' => $this->dateTime($ms->updated_at ?? null),
            ];
        })->values()->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Groups
    |--------------------------------------------------------------------------
    */

    private function loadGroups(array $masterSetIds): array
    {
        if (empty($masterSetIds) || !Schema::hasTable('master_set_group_master_set') || !Schema::hasTable('master_set_groups')) {
            return [];
        }

        $query = DB::table('master_set_group_master_set as pivot')
            ->join('master_set_groups as g', 'g.id', '=', 'pivot.master_set_group_id')
            ->whereIn('pivot.master_set_id', $masterSetIds);

        if (Schema::hasColumn('master_set_groups', 'deleted_at')) {
            $query->whereNull('g.deleted_at');
        }

        $rows = $query
            ->select([
                'pivot.master_set_id',
                'g.id',
                'g.article_group_id',
                'g.name',
                'g.description',
                'g.color',
            ])
            ->orderBy('g.name')
            ->get();

        $out = [];

        foreach ($rows as $row) {
            $out[(int) $row->master_set_id][] = [
                'id' => (int) $row->id,
                'article_group_id' => $row->article_group_id ? (int) $row->article_group_id : null,
                'name' => $row->name,
                'description' => $row->description,
                'color' => $row->color,
            ];
        }

        return $out;
    }

    /*
    |--------------------------------------------------------------------------
    | Components + Children + Product + Descriptions
    |--------------------------------------------------------------------------
    */

    private function loadComponents(array $masterSetIds): array
    {
        if (empty($masterSetIds) || !Schema::hasTable('master_set_components')) {
            return [];
        }

        $select = [
            'c.id',
            'c.master_set_id',
            'c.parent_id',
            'c.product_id',
            'c.article_no',
            'c.distributor_article_no',
            'c.distributor_id',
            'c.distributor_price_id',
            'c.unit_price',
            'c.qty',
            'c.description',
            'c.sort_order',
            'c.purchase_price',
            'c.margin',
            'c.skonto',
            'c.payment_terms',
            'c.availability',
            'c.type',
            'c.is_stammartikel',
            'c.is_favorite',
            'c.measure',
            'c.vpe',
            'c.price_unit',
        ];

        $productColumns = [
            'article_no' => 'product_article_no',
            'sku' => 'product_sku',
            'ean' => 'product_ean',
            'product' => 'product_name',
            'model' => 'product_model',
            'category' => 'product_category',
            'measure_unit' => 'product_measure_unit',
            'price_unit' => 'product_price_unit',
            'package_unit' => 'product_package_unit',
            'retail_price' => 'product_retail_price',
            'discount_price' => 'product_discount_price',
            'purchase_price' => 'product_purchase_price',
            'vat_percent' => 'product_vat_percent',
            'short_description' => 'product_short_description',
            'status' => 'product_status',
        ];

        foreach ($productColumns as $column => $alias) {
            $select[] = Schema::hasTable('products') && Schema::hasColumn('products', $column)
                ? "p.{$column} as {$alias}"
                : DB::raw("NULL as {$alias}");
        }

        $select[] = Schema::hasTable('distributors') && Schema::hasColumn('distributors', 'name')
            ? 'd.name as distributor_name'
            : DB::raw('NULL as distributor_name');

        $select[] = Schema::hasTable('distributor_prices') && Schema::hasColumn('distributor_prices', 'article_no')
            ? 'dp.article_no as distributor_price_article_no'
            : DB::raw('NULL as distributor_price_article_no');

        $select[] = Schema::hasTable('distributor_prices') && Schema::hasColumn('distributor_prices', 'price')
            ? 'dp.price as distributor_price_price'
            : DB::raw('NULL as distributor_price_price');

        $select[] = Schema::hasTable('distributor_prices') && Schema::hasColumn('distributor_prices', 'purchase_price')
            ? 'dp.purchase_price as distributor_price_purchase_price'
            : DB::raw('NULL as distributor_price_purchase_price');

        $select[] = Schema::hasTable('distributor_prices') && Schema::hasColumn('distributor_prices', 'availability')
            ? 'dp.availability as distributor_price_availability'
            : DB::raw('NULL as distributor_price_availability');

        $query = DB::table('master_set_components as c')
            ->whereIn('c.master_set_id', $masterSetIds);

        if (Schema::hasTable('products')) {
            $query->leftJoin('products as p', 'p.id', '=', 'c.product_id');
        }

        if (Schema::hasTable('distributors')) {
            $query->leftJoin('distributors as d', 'd.id', '=', 'c.distributor_id');
        }

        if (Schema::hasTable('distributor_prices')) {
            $query->leftJoin('distributor_prices as dp', 'dp.id', '=', 'c.distributor_price_id');
        }

        $components = $query
            ->select($select)
            ->orderBy('c.sort_order')
            ->orderBy('c.id')
            ->get();

        if ($components->isEmpty()) {
            return [];
        }

        $componentIds = $components
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $productIds = $components
            ->pluck('product_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $imagesByProduct = $this->loadProductImages($productIds);
        $descriptionsByComponent = $this->loadComponentDescriptions($componentIds);

        $nodes = [];

        foreach ($components as $c) {
            $componentId = (int) $c->id;
            $productId = $c->product_id ? (int) $c->product_id : null;

            $nodes[$componentId] = [
                'id' => $componentId,
                'master_set_id' => (int) $c->master_set_id,
                'parent_id' => $c->parent_id ? (int) $c->parent_id : null,

                'product_id' => $productId,
                'product' => $productId ? [
                    'id' => $productId,
                    'article_no' => $c->product_article_no ?? null,
                    'sku' => $c->product_sku ?? null,
                    'ean' => $c->product_ean ?? null,
                    'name' => $c->product_name ?? null,
                    'model' => $c->product_model ?? null,
                    'category' => $c->product_category ?? null,
                    'measure_unit' => $c->product_measure_unit ?? null,
                    'price_unit' => $c->product_price_unit ?? null,
                    'package_unit' => $c->product_package_unit ?? null,
                    'retail_price' => $this->decimal($c->product_retail_price ?? null),
                    'discount_price' => $this->decimal($c->product_discount_price ?? null),
                    'purchase_price' => $this->decimal($c->product_purchase_price ?? null),
                    'vat_percent' => $this->decimal($c->product_vat_percent ?? null),
                    'short_description' => $c->product_short_description ?? null,
                    'status' => $c->product_status ?? null,
                    'image' => $this->resolveProductImageUrl($imagesByProduct[$productId] ?? null),
                ] : null,

                'article_no' => $c->article_no,
                'distributor_article_no' => $c->distributor_article_no,

                'distributor_id' => $c->distributor_id ? (int) $c->distributor_id : null,
                'distributor' => $c->distributor_id ? [
                    'id' => (int) $c->distributor_id,
                    'name' => $c->distributor_name ?? null,
                ] : null,

                'distributor_price_id' => $c->distributor_price_id ? (int) $c->distributor_price_id : null,
                'distributor_price' => $c->distributor_price_id ? [
                    'id' => (int) $c->distributor_price_id,
                    'article_no' => $c->distributor_price_article_no ?? null,
                    'price' => $this->decimal($c->distributor_price_price ?? null),
                    'purchase_price' => $this->decimal($c->distributor_price_purchase_price ?? null),
                    'availability' => $c->distributor_price_availability ?? null,
                ] : null,

                'unit_price' => $this->decimal($c->unit_price),
                'qty' => $this->decimal($c->qty),
                'purchase_price' => $this->decimal($c->purchase_price),
                'margin' => $this->decimal($c->margin),
                'skonto' => $this->decimal($c->skonto),

                'description' => $c->description,
                'sort_order' => (int) ($c->sort_order ?? 0),

                'payment_terms' => $c->payment_terms,
                'availability' => $c->availability,
                'type' => $c->type,

                'is_stammartikel' => (bool) ($c->is_stammartikel ?? false),
                'is_favorite' => (bool) ($c->is_favorite ?? false),

                'measure' => $c->measure,
                'vpe' => $c->vpe,
                'price_unit' => $c->price_unit,

                'description_variants' => $descriptionsByComponent[$componentId] ?? [],
                'children' => [],
            ];
        }

        foreach ($nodes as $id => &$node) {
            if (!empty($node['parent_id']) && isset($nodes[$node['parent_id']])) {
                $nodes[$node['parent_id']]['children'][] = &$node;
            }
        }

        unset($node);

        $out = [];

        foreach ($nodes as $node) {
            if (empty($node['parent_id'])) {
                $out[(int) $node['master_set_id']][] = $node;
            }
        }

        return $out;
    }

    private function loadProductImages(array $productIds): array
    {
        if (empty($productIds) || !Schema::hasTable('product_images')) {
            return [];
        }

        $rows = DB::table('product_images')
            ->whereIn('product_id', $productIds)
            ->select(['id', 'product_id', 'image'])
            ->orderBy('id')
            ->get();

        $out = [];

        foreach ($rows as $row) {
            $productId = (int) $row->product_id;

            if (!isset($out[$productId])) {
                $out[$productId] = $row->image;
            }
        }

        return $out;
    }

    private function loadComponentDescriptions(array $componentIds): array
    {
        if (empty($componentIds) || !Schema::hasTable('master_set_component_descriptions')) {
            return [];
        }

        $rows = DB::table('master_set_component_descriptions')
            ->whereIn('master_set_component_id', $componentIds)
            ->select([
                'id',
                'master_set_component_id',
                'context',
                'title',
                'sort_order',
                'delta',
                'html',
                'text',
            ])
            ->orderBy('context')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $out = [];

        foreach ($rows as $row) {
            $out[(int) $row->master_set_component_id][] = [
                'id' => (int) $row->id,
                'context' => $row->context,
                'title' => $row->title,
                'sort_order' => (int) ($row->sort_order ?? 0),
                'delta' => $this->jsonDecode($row->delta),
                'html' => $row->html,
                'text' => $row->text,
            ];
        }

        return $out;
    }

    /*
    |--------------------------------------------------------------------------
    | Labor
    |--------------------------------------------------------------------------
    */

    private function loadLabor(array $masterSetIds): array
    {
        if (empty($masterSetIds) || !Schema::hasTable('master_set_labor')) {
            return [];
        }

        $query = DB::table('master_set_labor as l')
            ->whereIn('l.master_set_id', $masterSetIds);

        if (Schema::hasTable('departments')) {
            $query->leftJoin('departments as d', 'd.id', '=', 'l.department_id');
        }

        if (Schema::hasTable('positions')) {
            $query->leftJoin('positions as p', 'p.id', '=', 'l.position_id');
        }

        if (Schema::hasTable('position_qualifications')) {
            $query->leftJoin('position_qualifications as q', 'q.id', '=', 'l.qualification_id');
        }

        if (Schema::hasTable('employees')) {
            $query->leftJoin('employees as e', 'e.id', '=', 'l.employee_id');
        }

        $select = [
            'l.id',
            'l.master_set_id',
            'l.department_id',
            'l.qualification_id',
            'l.position_id',
            'l.employee_id',
            'l.hourly_rate',
            'l.hours',
            'l.sort_order',
        ];

        $select[] = Schema::hasTable('departments') && Schema::hasColumn('departments', 'department')
            ? 'd.department as department_name'
            : DB::raw('NULL as department_name');

        $select[] = Schema::hasTable('positions') && Schema::hasColumn('positions', 'position')
            ? 'p.position as position_name'
            : DB::raw('NULL as position_name');

        $select[] = Schema::hasTable('position_qualifications') && Schema::hasColumn('position_qualifications', 'name')
            ? 'q.name as qualification_name'
            : DB::raw('NULL as qualification_name');

        $select[] = Schema::hasTable('employees') && Schema::hasColumn('employees', 'name')
            ? 'e.name as employee_name'
            : DB::raw('NULL as employee_name');

        $select[] = Schema::hasTable('employees') && Schema::hasColumn('employees', 'lastname')
            ? 'e.lastname as employee_lastname'
            : DB::raw('NULL as employee_lastname');

        $select[] = Schema::hasTable('employees') && Schema::hasColumn('employees', 'image')
            ? 'e.image as employee_image'
            : DB::raw('NULL as employee_image');

        $rows = $query
            ->select($select)
            ->orderBy('l.sort_order')
            ->orderBy('l.id')
            ->get();

        $out = [];

        foreach ($rows as $row) {
            $out[(int) $row->master_set_id][] = [
                'id' => (int) $row->id,
                'master_set_id' => (int) $row->master_set_id,

                'department_id' => $row->department_id ? (int) $row->department_id : null,
                'department' => $row->department_id ? [
                    'id' => (int) $row->department_id,
                    'name' => $row->department_name,
                ] : null,

                'qualification_id' => $row->qualification_id ? (int) $row->qualification_id : null,
                'qualification' => $row->qualification_id ? [
                    'id' => (int) $row->qualification_id,
                    'name' => $row->qualification_name,
                ] : null,

                'position_id' => $row->position_id ? (int) $row->position_id : null,
                'position' => $row->position_id ? [
                    'id' => (int) $row->position_id,
                    'name' => $row->position_name,
                ] : null,

                'employee_id' => $row->employee_id ? (int) $row->employee_id : null,
                'employee' => $row->employee_id ? [
                    'id' => (int) $row->employee_id,
                    'name' => trim(($row->employee_name ?? '') . ' ' . ($row->employee_lastname ?? '')),
                    'image' => $this->resolveEmployeePhotoUrl($row->employee_image ?? null),
                ] : null,

                'hourly_rate' => $this->decimal($row->hourly_rate),
                'hours' => $this->decimal($row->hours),
                'sort_order' => (int) ($row->sort_order ?? 0),
            ];
        }

        return $out;
    }

    /*
    |--------------------------------------------------------------------------
    | Tasks + Task Labor
    |--------------------------------------------------------------------------
    */

    private function loadTasks(array $masterSetIds): array
    {
        if (empty($masterSetIds) || !Schema::hasTable('master_set_tasks')) {
            return [];
        }

        $tasks = DB::table('master_set_tasks')
            ->whereIn('master_set_id', $masterSetIds)
            ->select([
                'id',
                'master_set_id',
                'stage_id',
                'task_phase_id',
                'phase_activity_id',
                'stage_name',
                'phase_name',
                'title',
                'description',
                'duration',
                'duration_type',
                'notes',
                'priority',
                'percent',
                'hours',
                'sort_order',
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($tasks->isEmpty()) {
            return [];
        }

        $taskIds = $tasks
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $taskLaborByTask = $this->loadTaskLabor($taskIds);

        $out = [];

        foreach ($tasks as $task) {
            $out[(int) $task->master_set_id][] = [
                'id' => (int) $task->id,
                'master_set_id' => (int) $task->master_set_id,

                'stage_id' => $task->stage_id ? (int) $task->stage_id : null,
                'task_phase_id' => $task->task_phase_id ? (int) $task->task_phase_id : null,
                'phase_activity_id' => $task->phase_activity_id ? (int) $task->phase_activity_id : null,

                'stage_name' => $task->stage_name,
                'phase_name' => $task->phase_name,

                'title' => $task->title,
                'description' => $task->description,
                'duration' => $task->duration,
                'duration_type' => $task->duration_type,
                'notes' => $task->notes,
                'priority' => $task->priority,
                'percent' => $this->decimal($task->percent),
                'hours' => $this->decimal($task->hours),
                'sort_order' => (int) ($task->sort_order ?? 0),

                'labor' => $taskLaborByTask[(int) $task->id] ?? [],
            ];
        }

        return $out;
    }

    private function loadTaskLabor(array $taskIds): array
    {
        if (empty($taskIds) || !Schema::hasTable('master_set_task_labors')) {
            return [];
        }

        $query = DB::table('master_set_task_labors as tl')
            ->whereIn('tl.master_set_task_id', $taskIds);

        if (Schema::hasTable('position_qualifications')) {
            $query->leftJoin('position_qualifications as q', 'q.id', '=', 'tl.qualification_id');
        }

        $select = [
            'tl.id',
            'tl.master_set_task_id',
            'tl.qualification_id',
            'tl.hours',
            'tl.rate',
        ];

        $select[] = Schema::hasColumn('master_set_task_labors', 'auto_sum_id')
            ? 'tl.auto_sum_id'
            : DB::raw('NULL as auto_sum_id');

        $select[] = Schema::hasTable('position_qualifications') && Schema::hasColumn('position_qualifications', 'name')
            ? 'q.name as qualification_name'
            : DB::raw('NULL as qualification_name');

        $rows = $query
            ->select($select)
            ->orderBy('tl.id')
            ->get();

        $out = [];

        foreach ($rows as $row) {
            $out[(int) $row->master_set_task_id][] = [
                'id' => (int) $row->id,
                'master_set_task_id' => (int) $row->master_set_task_id,
                'qualification_id' => $row->qualification_id ? (int) $row->qualification_id : null,
                'qualification' => $row->qualification_id ? [
                    'id' => (int) $row->qualification_id,
                    'name' => $row->qualification_name,
                ] : null,
                'hours' => $this->decimal($row->hours),
                'rate' => $this->decimal($row->rate),
                'auto_sum_id' => $row->auto_sum_id ? (int) $row->auto_sum_id : null,
            ];
        }

        return $out;
    }

    /*
    |--------------------------------------------------------------------------
    | Checklists
    |--------------------------------------------------------------------------
    */

    private function loadChecklists(array $masterSetIds): array
    {
        if (empty($masterSetIds) || !Schema::hasTable('master_set_checklists')) {
            return [];
        }

        $query = DB::table('master_set_checklists as msc')
            ->whereIn('msc.master_set_id', $masterSetIds);

        if (Schema::hasTable('maintenance_checklists')) {
            $query->leftJoin('maintenance_checklists as mc', 'mc.id', '=', 'msc.maintenance_checklist_id');

            if (Schema::hasColumn('maintenance_checklists', 'deleted_at')) {
                $query->where(function ($q) {
                    $q->whereNull('mc.deleted_at')
                        ->orWhereNull('msc.maintenance_checklist_id');
                });
            }
        }

        $select = [
            'msc.id',
            'msc.master_set_id',
            'msc.maintenance_checklist_id',
            'msc.trigger',
            'msc.is_required',
            'msc.sort_order',
            'msc.checklist_title_snapshot',
            'msc.checklist_type_snapshot',
        ];

        foreach ([
            'title',
            'slug',
            'description',
            'logo_path',
            'type',
            'status',
            'is_global',
        ] as $col) {
            $select[] = Schema::hasTable('maintenance_checklists') && Schema::hasColumn('maintenance_checklists', $col)
                ? "mc.{$col} as checklist_{$col}"
                : DB::raw("NULL as checklist_{$col}");
        }

        $links = $query
            ->select($select)
            ->orderBy('msc.sort_order')
            ->orderBy('msc.id')
            ->get();

        if ($links->isEmpty()) {
            return [];
        }

        $checklistIds = $links
            ->pluck('maintenance_checklist_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $itemsByChecklist = $this->loadChecklistItems($checklistIds);

        $out = [];

        foreach ($links as $link) {
            $checklistId = $link->maintenance_checklist_id ? (int) $link->maintenance_checklist_id : null;

            $out[(int) $link->master_set_id][] = [
                'id' => (int) $link->id,
                'master_set_id' => (int) $link->master_set_id,
                'maintenance_checklist_id' => $checklistId,

                'trigger' => $link->trigger,
                'is_required' => (bool) ($link->is_required ?? false),
                'sort_order' => (int) ($link->sort_order ?? 0),

                'title_snapshot' => $link->checklist_title_snapshot,
                'type_snapshot' => $link->checklist_type_snapshot,

                'checklist' => $checklistId ? [
                    'id' => $checklistId,
                    'title' => $link->checklist_title,
                    'slug' => $link->checklist_slug,
                    'description' => $link->checklist_description,
                    'logo_path' => $this->resolveChecklistLogoUrl($link->checklist_logo_path),
                    'type' => $link->checklist_type,
                    'status' => $link->checklist_status,
                    'is_global' => (bool) ($link->checklist_is_global ?? false),
                    'items' => $itemsByChecklist[$checklistId] ?? [],
                ] : null,
            ];
        }

        return $out;
    }

    private function loadChecklistItems(array $checklistIds): array
    {
        if (empty($checklistIds) || !Schema::hasTable('maintenance_checklist_items')) {
            return [];
        }

        $query = DB::table('maintenance_checklist_items')
            ->whereIn('maintenance_checklist_id', $checklistIds);

        if (Schema::hasColumn('maintenance_checklist_items', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $rows = $query
            ->select([
                'id',
                'maintenance_checklist_id',
                'label',
                'field_name',
                'field_type',
                'options',
                'is_required',
                'help_text',
                'placeholder',
                'file_accept',
                'sort_order',
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $out = [];

        foreach ($rows as $row) {
            $out[(int) $row->maintenance_checklist_id][] = [
                'id' => (int) $row->id,
                'maintenance_checklist_id' => (int) $row->maintenance_checklist_id,
                'label' => $row->label,
                'field_name' => $row->field_name,
                'field_type' => $row->field_type,
                'options' => $this->jsonDecode($row->options),
                'is_required' => (bool) ($row->is_required ?? false),
                'help_text' => $row->help_text,
                'placeholder' => $row->placeholder,
                'file_accept' => $row->file_accept,
                'sort_order' => (int) ($row->sort_order ?? 0),
            ];
        }

        return $out;
    }

    /*
    |--------------------------------------------------------------------------
    | Task Phases / Activities / Costing
    |--------------------------------------------------------------------------
    */

    private function loadTaskPhases(array $ids): array
    {
        if (empty($ids) || !Schema::hasTable('task_phases')) {
            return [];
        }

        $query = DB::table('task_phases')
            ->whereIn('id', $ids);

        if (Schema::hasColumn('task_phases', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $rows = $query
            ->select([
                'id',
                'product_id',
                'section_id',
                'section_name',
                'phase_name',
                'stage',
                'stage_id',
                'version',
                'status',
                'count',
                'order',
            ])
            ->get();

        $out = [];

        foreach ($rows as $row) {
            $out[(int) $row->id] = [
                'id' => (int) $row->id,
                'product_id' => $row->product_id ? (int) $row->product_id : null,
                'section_id' => $row->section_id ? (int) $row->section_id : null,
                'section_name' => $row->section_name,
                'phase_name' => $row->phase_name,
                'stage' => $row->stage,
                'stage_id' => $row->stage_id ? (int) $row->stage_id : null,
                'version' => $row->version,
                'status' => $row->status,
                'count' => $row->count,
                'order' => $row->order,
            ];
        }

        return $out;
    }

    private function loadPhaseActivities(array $ids): array
    {
        if (empty($ids) || !Schema::hasTable('phase_activities')) {
            return [];
        }

        $query = DB::table('phase_activities')
            ->whereIn('id', $ids);

        if (Schema::hasColumn('phase_activities', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $rows = $query
            ->select([
                'id',
                'phase_id',
                'product_id',
                'section_id',
                'parent_id',
                'section_name',
                'initial',
                'title',
                'duration',
                'duration_type',
                'description',
                'notes',
                'status',
                'priority',
                'percent',
                'sort_order',
            ])
            ->get();

        $out = [];

        foreach ($rows as $row) {
            $out[(int) $row->id] = [
                'id' => (int) $row->id,
                'phase_id' => $row->phase_id ? (int) $row->phase_id : null,
                'product_id' => $row->product_id ? (int) $row->product_id : null,
                'section_id' => $row->section_id ? (int) $row->section_id : null,
                'parent_id' => $row->parent_id ? (int) $row->parent_id : null,
                'section_name' => $row->section_name,
                'initial' => $row->initial,
                'title' => $row->title,
                'duration' => $row->duration,
                'duration_type' => $row->duration_type,
                'description' => $row->description,
                'notes' => $row->notes,
                'status' => $row->status,
                'priority' => $row->priority,
                'percent' => $this->decimal($row->percent),
                'sort_order' => (int) ($row->sort_order ?? 0),
            ];
        }

        return $out;
    }

    private function loadCostingSets(array $ids): array
    {
        if (empty($ids) || !Schema::hasTable('costing_sets')) {
            return [];
        }

        $select = ['id'];

        $select[] = Schema::hasColumn('costing_sets', 'name')
            ? 'name'
            : DB::raw('NULL as name');

        $select[] = Schema::hasColumn('costing_sets', 'status')
            ? 'status'
            : DB::raw('NULL as status');

        $rows = DB::table('costing_sets')
            ->whereIn('id', $ids)
            ->select($select)
            ->get();

        $out = [];

        foreach ($rows as $row) {
            $out[(int) $row->id] = [
                'id' => (int) $row->id,
                'name' => $row->name,
                'status' => $row->status,
            ];
        }

        return $out;
    }

    /*
    |--------------------------------------------------------------------------
    | URL Helpers
    |--------------------------------------------------------------------------
    */

    private function resolveEmployeePhotoUrl($imageName): ?string
    {
        if (!$imageName) {
            return null;
        }

        if (Str::startsWith($imageName, ['http://', 'https://'])) {
            return $imageName;
        }

        $cleanPath = ltrim($imageName, '/');
        $domain = rtrim(config('app.url'), '/');

        if (Str::contains($cleanPath, 'images/employee')) {
            return $domain . '/' . $cleanPath;
        }

        return $domain . '/images/employee/' . $cleanPath;
    }

    private function resolveProductImageUrl($imageName): ?string
    {
        if (!$imageName) {
            return null;
        }

        if (Str::startsWith($imageName, ['http://', 'https://'])) {
            return $imageName;
        }

        $cleanPath = ltrim($imageName, '/');
        $domain = rtrim(config('app.url'), '/');

        if (Str::contains($cleanPath, 'images/products')) {
            return $domain . '/' . $cleanPath;
        }

        return $domain . '/images/products/' . $cleanPath;
    }

    private function resolveArticleGroupImageUrl($imageName): ?string
    {
        if (!$imageName) {
            return null;
        }

        if (Str::startsWith($imageName, ['http://', 'https://'])) {
            return $imageName;
        }

        $cleanPath = ltrim($imageName, '/');
        $domain = rtrim(config('app.url'), '/');

        if (Str::contains($cleanPath, 'images')) {
            return $domain . '/' . $cleanPath;
        }

        return $domain . '/images/article-groups/' . $cleanPath;
    }

    private function resolveChecklistLogoUrl($path): ?string
    {
        if (!$path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return rtrim(config('app.url'), '/') . '/' . ltrim($path, '/');
    }

    /*
    |--------------------------------------------------------------------------
    | Format Helpers
    |--------------------------------------------------------------------------
    */

    private function decimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }

    private function jsonDecode($value): array
    {
        if (!$value) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function dateTime($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->toIso8601String();
        } catch (\Throwable $e) {
            return null;
        }
    }
}