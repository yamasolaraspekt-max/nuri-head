<?php

namespace App\Http\Controllers\Product\MasterSet;

use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\DepartmentPosition;
use App\Models\DistributorPrice;
use App\Models\MasterSet;
use App\Models\MasterSetComponent;
use App\Models\MasterSetLabor;
use App\Models\MasterSetTask;
use App\Models\TaskPhase;
use App\Models\Product;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\MaintenanceChecklist;
use App\Models\MasterSetChecklist;
use App\Models\MasterSetTaskLabor;
use App\Models\MasterSetComponentDescription;
use App\Models\CostingSet;
use App\Models\CostingSetRole;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;


class MasterSetController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['update', 'duplicate', 'duplicateOptions', 'saveCostingSettings']);
        $this->middleware('permission:Product,delete')->only(['destroy']);
    }

    // =========================================================================
    // Views
    // =========================================================================

    public function index()
    {
        return view('admin.master_sets.index');
    }

    // =========================================================================
    // Sidebar: Article Groups
    // =========================================================================

    public function groups(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $groups = ArticleGroup::query()
            ->select(['id', 'article_group', 'initial', 'image'])
            ->when($q !== '', fn($qq) => $qq->where('article_group', 'like', "%{$q}%"))
            ->withCount([
                'masterSets as master_sets_count' => function ($qq) {
                    $qq->whereNull('deleted_at');
                }
            ])
            ->orderBy('article_group')
            ->get();

        return response()->json(['data' => $groups]);
    }

    // =========================================================================
    // Grid: Master Sets list with stats
    // =========================================================================

    public function data(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $articleGroupId = $request->integer('article_group_id') ?: null;
        $sort = (string) $request->get('sort', 'updated_desc');

        $query = MasterSet::query()
            ->with(['articleGroup:id,article_group', 'responsibleDepartmentPosition'])
            ->when($articleGroupId, fn($qq) => $qq->where('article_group_id', $articleGroupId))
            ->withCount('tasks')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            });

        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name');
                break;
            case 'name_desc':
                $query->orderByDesc('name');
                break;
            case 'created_desc':
                $query->orderByDesc('created_at');
                break;
            case 'created_asc':
                $query->orderBy('created_at');
                break;
            default:
                $query->orderByDesc('updated_at');
                break;
        }

        $sets = $query->paginate(12);

        $ids = $sets->getCollection()->pluck('id')->all();
        if (empty($ids)) {
            return response()->json($sets);
        }

        // Costs
        $mainCost = MasterSetComponent::query()
            ->select('master_set_id', DB::raw('COALESCE(SUM(COALESCE(unit_price,0) * COALESCE(qty,0) / COALESCE(NULLIF(price_unit, 0), 1)),0) as cost'))
            ->whereIn('master_set_id', $ids)
            ->whereNull('parent_id')
            ->groupBy('master_set_id')
            ->pluck('cost', 'master_set_id');

        $subCost = MasterSetComponent::query()
            ->select('master_set_id', DB::raw('COALESCE(SUM(COALESCE(unit_price,0) * COALESCE(qty,0) / COALESCE(NULLIF(price_unit, 0), 1)),0) as cost'))
            ->whereIn('master_set_id', $ids)
            ->whereNotNull('parent_id')
            ->groupBy('master_set_id')
            ->pluck('cost', 'master_set_id');

        $laborCost = MasterSetLabor::query()
            ->select('master_set_id', DB::raw('COALESCE(SUM(COALESCE(hourly_rate,0) * COALESCE(hours,0)),0) as labor'))
            ->whereIn('master_set_id', $ids)
            ->groupBy('master_set_id')
            ->pluck('labor', 'master_set_id');

        // Counts
        $mainCount = MasterSetComponent::query()
            ->select('master_set_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('master_set_id', $ids)
            ->whereNull('parent_id')
            ->groupBy('master_set_id')
            ->pluck('cnt', 'master_set_id');

        $subCount = MasterSetComponent::query()
            ->select('master_set_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('master_set_id', $ids)
            ->whereNotNull('parent_id')
            ->groupBy('master_set_id')
            ->pluck('cnt', 'master_set_id');

        $laborCount = MasterSetLabor::query()
            ->select('master_set_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('master_set_id', $ids)
            ->groupBy('master_set_id')
            ->pluck('cnt', 'master_set_id');

        $checklistsCount = DB::table('master_set_checklists')
            ->select('master_set_id', DB::raw('COUNT(*) as cnt'))
            ->whereIn('master_set_id', $ids)
            ->groupBy('master_set_id')
            ->pluck('cnt', 'master_set_id');


        $sets->getCollection()->transform(function (MasterSet $s) use ($mainCost, $subCost, $laborCost, $mainCount, $subCount, $laborCount, $checklistsCount) {
            $m = (float) ($mainCost[$s->id] ?? 0);
            $sub = (float) ($subCost[$s->id] ?? 0);
            $l = (float) ($laborCost[$s->id] ?? 0);

            $s->stats = [
                'mainCost' => (float) ($s->main_total ?? 0),
                'subCost' => (float) ($s->sub_total ?? 0),
                'labor' => (float) ($s->labor_total ?? 0),
                'total' => (float) ($s->total ?? 0),
                'mainCount' => (int) ($mainCount[$s->id] ?? 0),
                'subCount' => (int) ($subCount[$s->id] ?? 0),
                'laborCount' => (int) ($laborCount[$s->id] ?? 0),
                'checklistsCount' => (int) ($checklistsCount[$s->id] ?? 0),

            ];

            return $s;
        });

        return response()->json($sets);
    }

    // =========================================================================
    // Show one set (editor payload)
    // =========================================================================
    public function show(MasterSet $masterSet)
    {
        $masterSet->load([
            'articleGroup:id,article_group',
            'creator:id,name,lastname',

            'responsibleDepartmentPosition.employee:id,name,lastname,image,salary_per_hour',
            'responsibleDepartmentPosition.position:id,position',
            'responsibleDepartmentPosition.department:id,department_name',

            'components' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'components.product:id,product,article_no,brand_id,article_group,measure_unit,price_unit,package_unit,short_description',
            'components.distributor:id,name',
            'components.product.measure:id,measure',
            'components.product.firstImage',

            'labor' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'labor.employee:id,name,lastname,image,salary_per_hour',
            'labor.position:id,position',
            'labor.department:id,department_name',
            'labor.qualification:id,name,default_price',

            'tasks' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'tasks.labor.qualification:id,name,default_price',
            'tasks.leadStage:id,key,name,color,icon,sort_order',
            'tasks.leadSubStage:id,lead_stage_id,key,name,color,icon,sort_order',

            'checklistLinks' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'checklistLinks.checklist:id,title,slug,type,status,logo_path,description',
            'checklistLinks.checklist.items:id,maintenance_checklist_id,label,field_name,field_type,options,is_required,help_text,placeholder,file_accept,sort_order',
        ]);

        $globalGemeinkosten = (float) ($masterSet->global_gemeinkosten ?? 0);
        $globalWagnis = (float) ($masterSet->global_wagnis ?? 0);
        $globalMatMargin = (float) ($masterSet->global_mat_margin ?? 0);
        $minMatMargin = (float) ($masterSet->min_mat_margin ?? 0);

        $taskPhaseIds = $masterSet->tasks->pluck('task_phase_id')->filter()->unique()->values()->all();
        $phaseMap = empty($taskPhaseIds)
            ? collect()
            : DB::table('task_phases')
                ->whereNull('deleted_at')
                ->whereIn('id', $taskPhaseIds)
                ->pluck('phase_name', 'id');

        $flatComponents = $masterSet->components->map(function (MasterSetComponent $c) {
            $measureString = $c->measure ?? $c->product?->measure?->measure ?? 'Stk';

            $pe = (float) ($c->price_unit ?? $c->product?->price_unit ?? 1);
            if ($pe <= 0) {
                $pe = 1;
            }

            $purchasePrice = (float) ($c->purchase_price ?? 0);
            $qty = (float) ($c->qty ?? 0);
            $margin = (float) ($c->margin ?? 50);
            $skonto = (float) ($c->skonto ?? 0);
            $paymentTerms = (int) ($c->payment_terms ?? 14);

            return [
                'id' => (int) $c->id,
                'parent_id' => $c->parent_id ? (int) $c->parent_id : null,
                'image_url' => $c->product?->main_image_url,

                'product_id' => $c->product_id ? (int) $c->product_id : null,
                'product_name' => $c->product?->product,
                'productTitle' => $c->product?->product,
                'article_no' => $c->article_no ?: $c->product?->article_no,
                'articleNumber' => $c->article_no ?: $c->product?->article_no,

                'distributor_article_no' => $c->distributor_article_no,
                'product_short_description' => $c->product?->short_description,

                'measure' => $measureString,
                'unit' => $measureString,
                'price_unit' => $pe,
                'vpe' => (float) ($c->vpe ?? 1),

                'distributor_id' => $c->distributor_id ? (int) $c->distributor_id : null,
                'distributor_name' => $c->distributor?->name,
                'supplier' => $c->distributor?->name,
                'distributor_price_id' => $c->distributor_price_id ? (int) $c->distributor_price_id : null,

                'unit_price' => (float) ($c->unit_price ?? 0),
                'qty' => $qty,
                'quantity' => $qty,

                'description' => $c->description,
                'sort_order' => (int) ($c->sort_order ?? 0),
                'subComponents' => [],

                'purchase_price' => $purchasePrice,
                'purchasePrice' => $purchasePrice,
                'margin' => $margin,
                'skonto' => $skonto,
                'payment_terms' => $paymentTerms,
                'paymentTerms' => $paymentTerms,
                'availability' => (bool) ($c->availability ?? true),
                'type' => (string) ($c->type ?? 'haupt'),
                'is_stammartikel' => (bool) $c->is_stammartikel,
                'isStammartikel' => (bool) $c->is_stammartikel,
                'is_favorite' => (bool) $c->is_favorite,
                'isFavorite' => (bool) $c->is_favorite,

                'docs' => [],
                'isExpanded' => false,
                'isEditingProps' => false,
            ];
        })->values()->all();

        $byId = [];
        foreach ($flatComponents as $row) {
            $byId[$row['id']] = $row;
        }

        foreach ($byId as $id => $row) {
            $pid = $row['parent_id'];
            if ($pid && isset($byId[$pid])) {
                $byId[$pid]['subComponents'][] = $row;
            }
        }

        $tree = [];
        foreach ($byId as $id => $row) {
            if (empty($row['parent_id'])) {
                $tree[] = $row;
            }
        }

        $sortTree = function (array &$nodes) use (&$sortTree) {
            usort($nodes, fn($a, $b) => ($a['sort_order'] <=> $b['sort_order']) ?: ($a['id'] <=> $b['id']));
            foreach ($nodes as &$n) {
                if (!empty($n['subComponents'])) {
                    $sortTree($n['subComponents']);
                }
            }
        };
        $sortTree($tree);

        $labor = $masterSet->labor->map(function (MasterSetLabor $l) {
            $qualName = $l->qualification ? $l->qualification->name : null;
            $employeeName = $l->employee
                ? trim(($l->employee->name ?? '') . ' ' . ($l->employee->lastname ?? ''))
                : null;

            return [
                'id' => (int) $l->id,
                'qualification_id' => $l->qualification_id ? (int) $l->qualification_id : null,
                'name' => $qualName ?? $employeeName ?? 'Unbekannt',
                'department_id' => $l->department_id ? (int) $l->department_id : null,
                'department_name' => $l->department?->department_name,
                'position_id' => $l->position_id ? (int) $l->position_id : null,
                'position_name' => $l->position?->position,
                'employee_id' => $l->employee_id ? (int) $l->employee_id : null,
                'employee_name' => $employeeName,
                'avatar' => ($l->employee && $l->employee->image)
                    ? asset('images/employee/' . $l->employee->image)
                    : null,
                'rate' => (float) ($l->hourly_rate ?? 0),
                'hourly_rate' => (float) ($l->hourly_rate ?? 0),
                'hours' => (float) ($l->hours ?? 0),
                'source' => $l->source ?: 'manual',
                'is_manual' => ($l->source ?: 'manual') === 'manual',
                'is_auto' => ($l->source ?: 'manual') === 'tasks',
                'sort_order' => (int) ($l->sort_order ?? 0),
            ];
        })->values();

        $tasks = $masterSet->tasks->map(function (MasterSetTask $t) use ($phaseMap) {
            $leadStageId = $t->lead_stage_id ?: $t->stage_id;
            $stageName = $t->stage_name ?: ($t->leadStage?->name);
            $phaseName = $t->phase_name ?: ($t->task_phase_id ? ($phaseMap[$t->task_phase_id] ?? null) : null);

            return [
                'id' => (int) $t->id,

                // Compatibility: old frontend still reads stage_id.
                'stage_id' => $leadStageId ? (int) $leadStageId : null,
                'lead_stage_id' => $leadStageId ? (int) $leadStageId : null,
                'lead_sub_stage_id' => $t->lead_sub_stage_id ? (int) $t->lead_sub_stage_id : null,

                'stage_name' => $stageName,
                'lead_stage_name' => $stageName,
                'lead_sub_stage_name' => $t->leadSubStage?->name,
                'stage_color' => $t->leadStage?->color,
                'stage_icon' => $t->leadStage?->icon,
                'sub_stage_color' => $t->leadSubStage?->color,
                'sub_stage_icon' => $t->leadSubStage?->icon,

                'task_phase_id' => $t->task_phase_id ? (int) $t->task_phase_id : null,
                'phase_name' => $phaseName,
                'phase_activity_id' => $t->phase_activity_id ? (int) $t->phase_activity_id : null,
                'title' => $t->title,
                'description' => $t->description,
                'duration' => $t->duration,
                'duration_type' => $t->duration_type,
                'notes' => $t->notes,
                'priority' => $t->priority,
                'percent' => $t->percent !== null ? (float) $t->percent : null,
                'hours' => (float) ($t->hours ?? 0),
                'sort_order' => (int) ($t->sort_order ?? 0),

                'task_labor' => $t->labor->map(fn($tl) => [
                    'qualification_id' => (int) $tl->qualification_id,
                    'name' => $tl->qualification->name ?? 'Unknown',
                    'hours' => (float) $tl->hours,
                    'rate' => (float) ($tl->rate ?? $tl->qualification->default_price ?? 0),
                ])->values(),
            ];
        })->values();

        $checklists = $masterSet->checklistLinks->map(function ($x) {
            $items = collect($x->checklist?->items ?? [])->map(function ($it) {
                return [
                    'id' => (int) $it->id,
                    'label' => (string) ($it->label ?? ''),
                    'field_name' => (string) ($it->field_name ?? ''),
                    'field_type' => (string) ($it->field_type ?? ''),
                    'options' => $it->options ? (array) $it->options : null,
                    'is_required' => (bool) $it->is_required,
                    'help_text' => $it->help_text,
                    'placeholder' => $it->placeholder,
                    'file_accept' => $it->file_accept,
                    'sort_order' => (int) ($it->sort_order ?? 0),
                ];
            })->values();

            return [
                'id' => (int) $x->id,
                'maintenance_checklist_id' => (int) $x->maintenance_checklist_id,
                'title' => $x->checklist_title_snapshot ?: ($x->checklist?->title),
                'type' => $x->checklist_type_snapshot ?: ($x->checklist?->type),
                'slug' => $x->checklist?->slug,
                'status' => $x->checklist?->status,
                'logo_path' => $x->checklist?->logo_path,
                'description' => $x->checklist?->description,
                'trigger' => (string) ($x->trigger ?? 'start'),
                'is_required' => (bool) $x->is_required,
                'sort_order' => (int) ($x->sort_order ?? 0),
                'items_count' => (int) $items->count(),
                'items' => $items,
            ];
        })->values();

        $mainEk = 0.0;
        $subEk = 0.0;
        $mainVk = 0.0;
        $subVk = 0.0;
        $mainDb = 0.0;
        $subDb = 0.0;

        foreach ($flatComponents as $x) {
            $purchasePrice = (float) ($x['purchasePrice'] ?? 0);
            $qty = (float) ($x['quantity'] ?? 0);
            $priceUnit = (float) ($x['price_unit'] ?? 1);
            $margin = (float) ($x['margin'] ?? 0);

            if ($priceUnit <= 0) {
                $priceUnit = 1;
            }

            $gkPerPiece = $purchasePrice * ($globalGemeinkosten / 100);
            $wagnisPerPiece = $purchasePrice * ($globalWagnis / 100);
            $dbPerPiece = $purchasePrice * ($margin / 100);
            $vkPerPiece = $purchasePrice + $gkPerPiece + $wagnisPerPiece + $dbPerPiece;

            $ekLine = ($purchasePrice / $priceUnit) * $qty;
            $vkLine = ($vkPerPiece / $priceUnit) * $qty;
            $dbLine = $vkLine - $ekLine;

            if (empty($x['parent_id'])) {
                $mainEk += $ekLine;
                $mainVk += $vkLine;
                $mainDb += $dbLine;
            } else {
                $subEk += $ekLine;
                $subVk += $vkLine;
                $subDb += $dbLine;
            }
        }

        $laborSum = (float) $labor->sum(fn($x) => ((float) ($x['rate'] ?? 0)) * ((float) ($x['hours'] ?? 0)));

        return response()->json([
            'data' => [
                'id' => (int) $masterSet->id,
                'article_group_id' => (int) $masterSet->article_group_id,
                'name' => (string) ($masterSet->name ?? ''),
                'count_copy' => (int) ($masterSet->count_copy ?? 0),
                'count_offer' => (int) ($masterSet->count_offer ?? 0),
                'creator_name' => $masterSet->creator
                    ? trim(($masterSet->creator->name ?? '') . ' ' . ($masterSet->creator->lastname ?? ''))
                    : 'Unbekannt',
                'description' => (string) ($masterSet->description ?? ''),
                'responsible_department_position_id' => $masterSet->responsible_department_position_id
                    ? (int) $masterSet->responsible_department_position_id
                    : null,
                'status' => $masterSet->status,
                'is_locked' => (int) ($masterSet->is_locked ?? 0),

                'globalGemeinkosten' => $globalGemeinkosten,
                'globalWagnis' => $globalWagnis,
                'globalMatMargin' => $globalMatMargin,
                'minMatMargin' => $minMatMargin,

                'main_total' => (float) ($masterSet->main_total ?? 0),
                'sub_total' => (float) ($masterSet->sub_total ?? 0),
                'labor_total' => (float) ($masterSet->labor_total ?? 0),
                'total' => (float) ($masterSet->total ?? 0),

                'components' => array_values($tree),
                'labor' => $labor,
                'tasks' => $tasks,
                'checklists' => $checklists,

                'stats' => [
                    'mainCost' => (float) $mainEk,
                    'subCost' => (float) $subEk,
                    'ekTotal' => (float) ($mainEk + $subEk),
                    'mainVk' => (float) $mainVk,
                    'subVk' => (float) $subVk,
                    'vkTotal' => (float) ($mainVk + $subVk),
                    'mainDb' => (float) $mainDb,
                    'subDb' => (float) $subDb,
                    'dbTotal' => (float) ($mainDb + $subDb),
                    'labor' => (float) $laborSum,
                    'total' => (float) (($mainEk + $subEk) + $laborSum),
                ],
            ],
        ]);
    }

    public function items(\App\Models\MaintenanceChecklist $checklist)
    {
        $checklist->load([
            'items' => function ($q) {
                $q->orderBy('sort_order');
            }
        ]);

        return response()->json([
            'status' => 'ok',
            'data' => $checklist->items->map(fn($it) => [
                'id' => $it->id,
                'label' => $it->label,
                'field_name' => $it->field_name,
                'field_type' => $it->field_type,
                'help_text' => $it->help_text,
                'is_required' => (bool) $it->is_required,
                'sort_order' => (int) $it->sort_order,
            ])->values(),
        ]);
    }


    // =========================================================================
    // Create / Update / Delete
    // =========================================================================

    public function store(Request $request)
    {
        Log::info('MasterSet store request received', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'payload' => $request->all(),
        ]);

        try {
            $data = $this->validatePayload($request);

            Log::info('MasterSet store validation passed', [
                'validated' => $data,
            ]);

            return DB::transaction(function () use ($data) {
                Log::info('MasterSet store transaction start', [
                    'article_group_id' => $data['article_group_id'] ?? null,
                    'name' => $data['name'] ?? null,
                    'components_count' => count($data['components'] ?? []),
                    'labor_count' => count($data['labor'] ?? []),
                    'tasks_count' => count($data['tasks'] ?? []),
                    'checklists_count' => count($data['checklists'] ?? []),
                ]);

                $set = MasterSet::create([
                    'article_group_id' => $data['article_group_id'],
                    'name' => $data['name'] ?? null,
                    'description' => $data['description'] ?? null,
                    'responsible_department_position_id' => $data['responsible_department_position_id'] ?? null,
                    'status' => $data['status'] ?? 'Published',
                    'creator_id' => auth()->id(),
                    'count_copy' => $data['count_copy'] ?? 0,
                    'count_offer' => $data['count_offer'] ?? 0,
                    'is_locked' => $data['is_locked'] ?? 1,
                    'main_total' => $data['main_total'] ?? 0,
                    'sub_total' => $data['sub_total'] ?? 0,
                    'labor_total' => $data['labor_total'] ?? 0,
                    'total' => $data['total'] ?? 0,
                ]);

                Log::info('MasterSet created', [
                    'master_set_id' => $set->id,
                ]);

                $this->syncComponents($set, $data['components'] ?? []);
                Log::info('MasterSet components synced', ['master_set_id' => $set->id]);

                $this->syncLabor($set, $data['labor'] ?? []);
                Log::info('MasterSet labor synced', ['master_set_id' => $set->id]);

                $this->syncTasks($set, $data['tasks'] ?? []);
                Log::info('MasterSet tasks synced', ['master_set_id' => $set->id]);

                $this->syncChecklists($set, $data['checklists'] ?? []);
                Log::info('MasterSet checklists synced', ['master_set_id' => $set->id]);

                return response()->json(['status' => 'ok', 'id' => $set->id]);
            });
        } catch (ValidationException $e) {
            Log::error('MasterSet store validation failed', [
                'errors' => $e->errors(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            Log::error('MasterSet store crashed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function update(Request $request, MasterSet $masterSet)
    {
        Log::info('MasterSet update request received', [
            'master_set_id' => $masterSet->id,
            'payload' => $request->all(),
        ]);

        try {
            $data = $this->validatePayload($request);

            Log::info('MasterSet update validation passed', [
                'master_set_id' => $masterSet->id,
                'validated' => $data,
            ]);

            return DB::transaction(function () use ($masterSet, $data) {
                $masterSet->update([
                    'article_group_id' => $data['article_group_id'],
                    'name' => $data['name'] ?? null,
                    'description' => $data['description'] ?? null,
                    'responsible_department_position_id' => $data['responsible_department_position_id'] ?? null,
                    'status' => $data['status'] ?? $masterSet->status,
                    'count_copy' => $data['count_copy'] ?? $masterSet->count_copy,
                    'count_offer' => $data['count_offer'] ?? $masterSet->count_offer,
                    'is_locked' => $data['is_locked'] ?? $masterSet->is_locked,
                    'creator_id' => $masterSet->creator_id ?? auth()->id(),
                    'main_total' => $data['main_total'] ?? $masterSet->main_total,
                    'sub_total' => $data['sub_total'] ?? $masterSet->sub_total,
                    'labor_total' => $data['labor_total'] ?? $masterSet->labor_total,
                    'total' => $data['total'] ?? $masterSet->total,
                ]);

                $this->syncComponents($masterSet, $data['components'] ?? []);
                $this->syncLabor($masterSet, $data['labor'] ?? []);
                $this->syncTasks($masterSet, $data['tasks'] ?? []);
                $this->syncChecklists($masterSet, $data['checklists'] ?? []);

                return response()->json(['status' => 'ok']);
            });
        } catch (ValidationException $e) {
            Log::error('MasterSet update validation failed', [
                'master_set_id' => $masterSet->id,
                'errors' => $e->errors(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            Log::error('MasterSet update crashed', [
                'master_set_id' => $masterSet->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
    public function destroy(MasterSet $masterSet)
    {
        $masterSet->delete();
        return response()->json(['status' => 'ok']);
    }

    // =========================================================================
    // Catalog: Products + suppliers (latest prices)
    // =========================================================================

    public function catalog(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $defaultDistributor = \App\Models\Distributor::query()->firstOrCreate(
            ['name' => 'Standard Distributor'],
            ['status' => 'Published']
        );

        $productsQuery = Product::query()
            ->from('products as p')
            ->select(
                'p.id',
                'p.product',
                'p.article_no',
                'p.short_description',
                'p.brand_id',
                'p.article_group',
                'p.price_unit',
                'p.package_unit',
                'm.measure'
            )
            ->leftJoin('brands as b', 'b.id', '=', 'p.brand_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'p.article_group')
            ->leftJoin('measures as m', 'm.id', '=', 'p.measure_unit')
            ->leftJoin('distributor_prices as dp', 'dp.product_id', '=', 'p.id')
            ->leftJoin('distributors as d', 'd.id', '=', 'dp.distributor_id')
            ->when($q !== '', function ($qq) use ($q) {
                $like = '%' . $q . '%';
                $qq->where(function ($w) use ($like) {
                    $w->where('p.product', 'like', $like)
                        ->orWhere('p.article_no', 'like', $like)
                        ->orWhere('p.ean', 'like', $like)
                        ->orWhere('p.model', 'like', $like)
                        ->orWhere('p.short_description', 'like', $like)
                        ->orWhere('b.name', 'like', $like)
                        ->orWhere('ag.article_group', 'like', $like)
                        ->orWhere('d.name', 'like', $like)
                        ->orWhere('dp.article_no', 'like', $like);
                });
            })
            ->orderBy('p.product')
            ->distinct();

        $products = $productsQuery->get();
        $productIds = $products->pluck('id')->all();

        if (empty($productIds)) {
            return response()->json(['data' => []]);
        }

        // Prices logic remains same...
        $priceCandidates = ['distributor_price', 'purchase_price', 'price', 'net_price', 'ek_price'];
        $priceColumn = null;
        foreach ($priceCandidates as $col) {
            if (Schema::hasColumn('distributor_prices', $col)) {
                $priceColumn = $col;
                break;
            }
        }
        $priceExpr = $priceColumn ? "dp.$priceColumn" : "0";

        $latestIds = DB::table('distributor_prices')
            ->select(DB::raw('MAX(id) as id'))
            ->whereIn('product_id', $productIds)
            ->groupBy('product_id', 'distributor_id');

        $pricesQuery = DB::table('distributor_prices as dp')
            ->joinSub($latestIds, 'x', fn($j) => $j->on('x.id', '=', 'dp.id'))
            ->join('distributors as d', 'd.id', '=', 'dp.distributor_id')
            ->select(
                'dp.id as distributor_price_id',
                'dp.product_id',
                'dp.distributor_id',
                'dp.article_no as distributor_article_no',
                'd.name as distributor_name',
                DB::raw("COALESCE($priceExpr, 0) as distributor_price")
            )
            ->orderBy('d.name');

        if (Schema::hasColumn('distributor_prices', 'deleted_at')) {
            $pricesQuery->whereNull('dp.deleted_at');
        }

        $prices = $pricesQuery->get()->groupBy('product_id');

        $out = $products->map(function ($p) use ($prices, $defaultDistributor) {
            $suppliers = collect($prices[$p->id] ?? [])->map(function ($r) {
                return [
                    'distributor_price_id' => (int) $r->distributor_price_id,
                    'distributor_id' => (int) $r->distributor_id,
                    'distributor_name' => (string) $r->distributor_name,
                    'distributor_article_no' => (string) ($r->distributor_article_no ?? ''),
                    'distributor_price' => (float) $r->distributor_price,
                ];
            })->values();

            if ($suppliers->isEmpty()) {
                $suppliers = collect([
                    [
                        'distributor_price_id' => 0,
                        'distributor_id' => (int) $defaultDistributor->id,
                        'distributor_name' => (string) $defaultDistributor->name,
                        'distributor_price' => 0.0,
                    ]
                ]);
            }

            return [
                'id' => (int) $p->id,
                'name' => (string) $p->product,
                'article_no' => (string) ($p->article_no ?? ''),
                'short_description' => (string) ($p->short_description ?? ''),
                'measure' => (string) ($p->measure ?? 'Stk'),
                'price_unit' => (float) ($p->price_unit ?: 1),
                'suppliers' => $suppliers,
            ];
        })->values();

        return response()->json(['data' => $out]);
    }

    // =========================================================================
    // Labor Options: grouped by position
    // =========================================================================


    public function laborOptions()
    {
        // Fetch all published qualifications sorted by order
        $qualifications = \App\Models\PositionQualification::query() // Assuming you have this model
            ->where('status', 'Published')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function ($q) {
                return [
                    'id' => $q->id,
                    'name' => $q->name,
                    'default_price' => (float) $q->default_price,
                ];
            });

        return response()->json(['data' => $qualifications]);
    }

    // =========================================================================
    // Tasks Options: stages -> phases -> activities
    // =========================================================================
    public function taskOptions(Request $request)
    {
        $articleGroupId = $request->integer('article_group_id');
        $q = trim((string) $request->get('q', ''));
        $section = trim((string) $request->get('section', ''));

        // Frontend can still send stage_id. Here it is treated as lead_stage_id.
        $leadStageIdFilter = $request->integer('lead_stage_id') ?: $request->integer('stage_id');

        if (!$articleGroupId) {
            return response()->json(['data' => []]);
        }

        $stages = LeadStage::query()
            ->select(['id', 'key', 'name', 'color', 'icon', 'sort_order', 'is_default'])
            ->where('is_active', true)
            ->when($leadStageIdFilter, fn($w) => $w->whereKey($leadStageIdFilter))
            ->ordered()
            ->get();

        if ($stages->isEmpty()) {
            return response()->json(['data' => []]);
        }

        $leadStageIds = $stages->pluck('id')->map(fn($id) => (int) $id)->all();

        // If old phases have NULL lead_stage_id, show them under the selected/default LeadStage
        // instead of returning an empty Aufgabe list.
        $fallbackLeadStageId = $leadStageIdFilter
            ?: (int) ($stages->firstWhere('is_default', true)?->id ?? $stages->first()?->id);

        $phases = DB::table('task_phases')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'task_phases.section_id')
            ->select(
                'task_phases.id',
                'task_phases.product_id',
                'task_phases.phase_name',
                'task_phases.lead_stage_id',
                'task_phases.lead_sub_stage_id',
                'task_phases.order',
                'task_phases.section_id',
                'task_phases.section_name',
                'phase_sections.phase_section'
            )
            ->whereNull('task_phases.deleted_at')
            ->where('task_phases.product_id', $articleGroupId)
            ->where(function ($query) use ($leadStageIds, $leadStageIdFilter) {
                if ($leadStageIdFilter) {
                    $query->where('task_phases.lead_stage_id', $leadStageIdFilter)
                        ->orWhereNull('task_phases.lead_stage_id');
                } else {
                    $query->whereIn('task_phases.lead_stage_id', $leadStageIds)
                        ->orWhereNull('task_phases.lead_stage_id');
                }
            })
            ->when($section !== '', function ($w) use ($section) {
                $w->where(function ($x) use ($section) {
                    $x->where('task_phases.section_name', $section)
                        ->orWhere('phase_sections.phase_section', $section);
                });
            })
            ->orderByRaw('COALESCE(task_phases.`order`, 999999) asc')
            ->orderBy('task_phases.phase_name')
            ->get()
            ->map(function ($phase) use ($fallbackLeadStageId) {
                $phase->resolved_lead_stage_id = $phase->lead_stage_id ?: $fallbackLeadStageId;
                return $phase;
            });

        $phaseIds = $phases->pluck('id')->all();

        if (empty($phaseIds)) {
            return response()->json(['data' => []]);
        }

        $activities = DB::table('phase_activities')
            ->join('task_phases', 'task_phases.id', '=', 'phase_activities.phase_id')
            ->select(
                'phase_activities.id',
                'phase_activities.phase_id',
                'phase_activities.lead_stage_id',
                'phase_activities.lead_sub_stage_id',
                'phase_activities.title',
                'phase_activities.description',
                'phase_activities.duration',
                'phase_activities.duration_type',
                'phase_activities.notes',
                'phase_activities.priority',
                'phase_activities.percent',
                'phase_activities.sort_order'
            )
            ->whereNull('phase_activities.deleted_at')
            ->where('phase_activities.product_id', $articleGroupId)
            ->whereIn('phase_activities.phase_id', $phaseIds)
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($x) use ($q) {
                    $x->where('phase_activities.title', 'like', "%{$q}%")
                        ->orWhere('phase_activities.description', 'like', "%{$q}%")
                        ->orWhere('task_phases.phase_name', 'like', "%{$q}%");
                });
            })
            ->orderByRaw('COALESCE(phase_activities.sort_order, 999999) asc')
            ->orderBy('phase_activities.title')
            ->get()
            ->groupBy('phase_id');

        $subStages = LeadStageSubStage::query()
            ->select(['id', 'lead_stage_id', 'key', 'name', 'color', 'icon', 'sort_order', 'is_default'])
            ->where('is_active', true)
            ->whereIn('lead_stage_id', $leadStageIds)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('lead_stage_id');

        $phasesByStage = $phases->groupBy('resolved_lead_stage_id');

        $out = $stages->map(function (LeadStage $stage) use ($phasesByStage, $activities, $subStages) {
            $phaseList = ($phasesByStage[$stage->id] ?? collect())->map(function ($phase) use ($activities, $stage) {
                $actsRaw = $activities[$phase->id] ?? collect();

                if ($actsRaw->isEmpty()) {
                    return null;
                }

                $phaseLeadStageId = $phase->lead_stage_id ?: $phase->resolved_lead_stage_id ?: $stage->id;

                $acts = $actsRaw->map(function ($activity) use ($phase, $phaseLeadStageId) {
                    $leadStageId = $activity->lead_stage_id ?: $phaseLeadStageId;
                    $leadSubStageId = $activity->lead_sub_stage_id ?: $phase->lead_sub_stage_id;

                    return [
                        'id' => (int) $activity->id,
                        'title' => $activity->title,
                        'description' => $activity->description,
                        'duration' => $activity->duration,
                        'duration_type' => $activity->duration_type,
                        'notes' => $activity->notes,
                        'priority' => $activity->priority,
                        'percent' => $activity->percent !== null ? (float) $activity->percent : null,
                        'sort_order' => (int) ($activity->sort_order ?? 0),

                        // Compatibility aliases for old JS.
                        'stage_id' => $leadStageId ? (int) $leadStageId : null,
                        'lead_stage_id' => $leadStageId ? (int) $leadStageId : null,
                        'lead_sub_stage_id' => $leadSubStageId ? (int) $leadSubStageId : null,
                        'phase_id' => (int) $activity->phase_id,
                    ];
                })->values();

                return [
                    'id' => (int) $phase->id,
                    'name' => $phase->phase_name,
                    'phase_name' => $phase->phase_name,
                    'section_id' => $phase->section_id ? (int) $phase->section_id : null,
                    'section_name' => $phase->section_name ?: $phase->phase_section,
                    'order' => $phase->order !== null ? (int) $phase->order : null,

                    // Compatibility aliases for old JS.
                    'stage_id' => $phaseLeadStageId ? (int) $phaseLeadStageId : null,
                    'lead_stage_id' => $phaseLeadStageId ? (int) $phaseLeadStageId : null,
                    'lead_sub_stage_id' => $phase->lead_sub_stage_id ? (int) $phase->lead_sub_stage_id : null,
                    'activities' => $acts,
                ];
            })->filter()->values();

            if ($phaseList->isEmpty()) {
                return null;
            }

            return [
                'id' => (int) $stage->id,
                'stage_id' => (int) $stage->id,
                'lead_stage_id' => (int) $stage->id,
                'name' => $stage->name,
                'stage' => $stage->name,
                'key' => $stage->key,
                'color' => $stage->color,
                'icon' => $stage->icon,
                'sort_order' => $stage->sort_order !== null ? (int) $stage->sort_order : null,
                'sub_stages' => ($subStages[$stage->id] ?? collect())->map(fn($subStage) => [
                    'id' => (int) $subStage->id,
                    'lead_stage_id' => (int) $subStage->lead_stage_id,
                    'key' => $subStage->key,
                    'name' => $subStage->name,
                    'color' => $subStage->color,
                    'icon' => $subStage->icon,
                    'sort_order' => (int) ($subStage->sort_order ?? 0),
                    'is_default' => (bool) $subStage->is_default,
                ])->values(),
                'phases' => $phaseList,
            ];
        })->filter()->values();

        return response()->json(['data' => $out]);
    }


    // =========================================================================
    // Validation
    // =========================================================================

    private function validatePayload(Request $request): array
    {
        Log::info('validatePayload called', [
            'payload_keys' => array_keys($request->all()),
            'article_group_id' => $request->input('article_group_id'),
            'components_count' => count($request->input('components', [])),
            'labor_count' => count($request->input('labor', [])),
            'tasks_count' => count($request->input('tasks', [])),
            'checklists_count' => count($request->input('checklists', [])),
            'raw_payload' => $request->all(),
        ]);

        return $request->validate([
            'article_group_id' => ['required', 'integer', 'exists:article_groups,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'responsible_department_position_id' => ['nullable', 'integer', 'exists:department_positions,id'],
            'status' => ['nullable', 'string', 'max:50'],
            'count_copy' => ['nullable', 'integer'],
            'count_offer' => ['nullable', 'integer'],
            'is_locked' => ['nullable', 'integer', 'in:0,1'],
            'main_total' => ['nullable', 'numeric'],
            'sub_total' => ['nullable', 'numeric'],
            'labor_total' => ['nullable', 'numeric'],
            'total' => ['nullable', 'numeric'],

            'components' => ['nullable', 'array'],
            'components.*.id' => ['nullable'],
            'components.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'components.*.distributor_price_id' => ['nullable', 'integer', 'exists:distributor_prices,id'],
            'components.*.qty' => ['nullable', 'numeric', 'min:0'],
            'components.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'components.*.description' => ['nullable', 'string'],
            'components.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'components.*.purchase_price' => ['nullable', 'numeric', 'min:0'],
            'components.*.margin' => ['nullable', 'numeric', 'min:0'],
            'components.*.skonto' => ['nullable', 'numeric', 'min:0'],
            'components.*.payment_terms' => ['nullable', 'integer', 'min:0'],
            'components.*.availability' => ['nullable', 'boolean'],
            'components.*.type' => ['nullable', 'string', 'max:50'],
            'components.*.is_stammartikel' => ['nullable', 'boolean'],
            'components.*.is_favorite' => ['nullable', 'boolean'],
            'components.*.measure' => ['nullable', 'string', 'max:50'],
            'components.*.price_unit' => ['nullable', 'numeric', 'min:0'],
            'components.*.vpe' => ['nullable', 'numeric', 'min:0'],
            'components.*.article_no' => ['nullable', 'string', 'max:255'],
            'components.*.subComponents.*.article_no' => ['nullable', 'string', 'max:255'],
            'components.*.distributor_article_no' => ['nullable', 'string', 'max:255'],

            'components.*.subComponents' => ['nullable', 'array'],
            'components.*.subComponents.*.id' => ['nullable'],
            'components.*.subComponents.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'components.*.subComponents.*.distributor_price_id' => ['nullable', 'integer', 'exists:distributor_prices,id'],
            'components.*.subComponents.*.qty' => ['nullable', 'numeric', 'min:0'],
            'components.*.subComponents.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'components.*.subComponents.*.description' => ['nullable', 'string'],
            'components.*.subComponents.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'components.*.subComponents.*.purchase_price' => ['nullable', 'numeric', 'min:0'],
            'components.*.subComponents.*.margin' => ['nullable', 'numeric', 'min:0'],
            'components.*.subComponents.*.skonto' => ['nullable', 'numeric', 'min:0'],
            'components.*.subComponents.*.payment_terms' => ['nullable', 'integer', 'min:0'],
            'components.*.subComponents.*.availability' => ['nullable', 'boolean'],
            'components.*.subComponents.*.type' => ['nullable', 'string', 'max:50'],
            'components.*.subComponents.*.is_stammartikel' => ['nullable', 'boolean'],
            'components.*.subComponents.*.is_favorite' => ['nullable', 'boolean'],
            'components.*.subComponents.*.measure' => ['nullable', 'string', 'max:50'],
            'components.*.subComponents.*.price_unit' => ['nullable', 'numeric', 'min:0'],
            'components.*.subComponents.*.vpe' => ['nullable', 'numeric', 'min:0'],
            'components.*.subComponents.*.distributor_article_no' => ['nullable', 'string', 'max:255'],

            'labor' => ['nullable', 'array'],
            'labor.*.id' => ['nullable', 'integer'],
            'labor.*.qualification_id' => ['nullable', 'integer', 'exists:position_qualifications,id'],
            'labor.*.department_position_id' => ['nullable', 'integer', 'exists:department_positions,id'],
            'labor.*.hours' => ['nullable', 'numeric', 'min:0'],
            'labor.*.hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'labor.*.source' => ['nullable', 'string', 'in:manual,tasks'],
            'labor.*.is_manual' => ['nullable', 'boolean'],
            'labor.*.sort_order' => ['nullable', 'integer', 'min:0'],

            'tasks' => ['nullable', 'array'],
            'tasks.*.id' => ['nullable', 'integer'],
            'tasks.*.stage_id' => ['nullable', 'integer', 'exists:lead_stages,id'], // frontend alias for lead_stage_id
            'tasks.*.lead_stage_id' => ['nullable', 'integer', 'exists:lead_stages,id'],
            'tasks.*.lead_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
            'tasks.*.task_phase_id' => ['nullable', 'integer', 'exists:task_phases,id'],
            'tasks.*.phase_activity_id' => ['required', 'integer', 'exists:phase_activities,id'],
            'tasks.*.hours' => ['nullable', 'numeric', 'min:0'],
            'tasks.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'tasks.*.stage_name' => ['nullable', 'string', 'max:255'],
            'tasks.*.phase_name' => ['nullable', 'string', 'max:255'],
            'tasks.*.title' => ['nullable', 'string', 'max:255'],
            'tasks.*.description' => ['nullable', 'string', 'max:255'],
            'tasks.*.duration' => ['nullable'],
            'tasks.*.duration_type' => ['nullable', 'string', 'max:50'],
            'tasks.*.notes' => ['nullable', 'string'],
            'tasks.*.priority' => ['nullable', 'string', 'max:50'],
            'tasks.*.percent' => ['nullable', 'numeric'],
            'tasks.*.task_labor' => ['nullable', 'array'],
            'tasks.*.task_labor.*.qualification_id' => ['required', 'integer'],
            'tasks.*.task_labor.*.hours' => ['required', 'numeric'],
            'tasks.*.task_labor.*.rate' => ['nullable', 'numeric'],

            'checklists' => ['nullable', 'array'],
            'checklists.*.id' => ['nullable', 'integer', 'exists:master_set_checklists,id'],
            'checklists.*.maintenance_checklist_id' => ['required', 'integer', 'exists:maintenance_checklists,id'],
            'checklists.*.trigger' => ['nullable', 'string', 'max:50'],
            'checklists.*.is_required' => ['nullable', 'boolean'],
            'checklists.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'checklists.*.checklist_title_snapshot' => ['nullable', 'string', 'max:255'],
            'checklists.*.checklist_type_snapshot' => ['nullable', 'string', 'max:255'],
        ]);
    }

    // =========================================================================
    // Components Sync Helpers
    // =========================================================================
    private function resolveDistributorPrice(?int $distributorPriceId): array
    {
        $defaultDistributor = \App\Models\Distributor::query()->firstOrCreate(
            ['name' => 'Standard Distributor'],
            ['status' => 'Published']
        );

        if (!$distributorPriceId) {
            return [
                (int) $defaultDistributor->id, // distributor_id
                null,                          // distributor_price_id
                null,                          // product_id
                0.0,                           // unit_price
                null,                          // distributor_article_no
            ];
        }

        $dp = DistributorPrice::query()->find($distributorPriceId);
        if (!$dp) {
            return [
                (int) $defaultDistributor->id,
                null,
                null,
                0.0,
                null,
            ];
        }

        $unit = (float) (
            $dp->distributor_price
            ?? $dp->purchase_price
            ?? $dp->unit_price
            ?? $dp->price
            ?? $dp->net_price
            ?? $dp->ek_price
            ?? 0
        );

        $distId = $dp->distributor_id ? (int) $dp->distributor_id : (int) $defaultDistributor->id;

        return [
            $distId,
            (int) $dp->id,
            (int) $dp->product_id,
            $unit,
            $dp->article_no, // important
        ];
    }

    /**
     * Helper to prepare component data array
     */
    private function prepareComponentData($setId, $data, $parentId, $sortOrder)
    {
        [$distId, $dpId, $dpProductId, $defaultPrice, $dpArticleNo] =
            $this->resolveDistributorPrice($data['distributor_price_id'] ?? null);

        $finalPrice = array_key_exists('unit_price', $data) ? (float) $data['unit_price'] : $defaultPrice;

        $productId = (int) $data['product_id'];
        $product = Product::query()->select('id', 'article_no', 'short_description')->find($productId);

        return [
            'master_set_id' => $setId,
            'parent_id' => $parentId,
            'product_id' => $productId,
            'article_no' => $data['article_no'] ?? $product?->article_no,
            'distributor_article_no' => $data['distributor_article_no'] ?? $dpArticleNo,
            'distributor_id' => $distId,
            'distributor_price_id' => $dpId,
            'unit_price' => $finalPrice,
            'qty' => (float) ($data['qty'] ?? 0),
            'description' => $data['description'] ?? $product?->short_description,
            'sort_order' => $sortOrder,
            'purchase_price' => (float) ($data['purchase_price'] ?? $data['purchasePrice'] ?? 0),
            'margin' => (float) ($data['margin'] ?? 50),
            'skonto' => (float) ($data['skonto'] ?? 0),
            'payment_terms' => (int) ($data['payment_terms'] ?? $data['paymentTerms'] ?? 14),
            'availability' => (bool) ($data['availability'] ?? true),
            'type' => (string) ($data['type'] ?? 'haupt'),
            'is_stammartikel' => (bool) ($data['is_stammartikel'] ?? $data['isStammartikel'] ?? false),
            'is_favorite' => (bool) ($data['is_favorite'] ?? $data['isFavorite'] ?? false),
            'measure' => (string) ($data['measure'] ?? $data['unit'] ?? 'Stk.'),
            'price_unit' => max(1, (float) ($data['price_unit'] ?? 1)),
            'vpe' => (float) ($data['vpe'] ?? 1),
        ];
    }

    private function syncComponents(MasterSet $set, array $components): void
    {
        Log::info('syncComponents start', [
            'master_set_id' => $set->id,
            'components' => $components,
        ]);

        $keepIds = [];

        foreach ($components as $cIndex => $c) {
            Log::info('syncComponents main component row', [
                'index' => $cIndex,
                'row' => $c,
            ]);

            if (isset($c['id']) && is_numeric($c['id'])) {
                $keepIds[] = (int) $c['id'];
            }

            foreach (($c['subComponents'] ?? []) as $sIndex => $s) {
                Log::info('syncComponents sub component row', [
                    'main_index' => $cIndex,
                    'sub_index' => $sIndex,
                    'row' => $s,
                ]);

                if (isset($s['id']) && is_numeric($s['id'])) {
                    $keepIds[] = (int) $s['id'];
                }
            }
        }

        $keepIds = array_values(array_unique($keepIds));

        $deleteQuery = MasterSetComponent::query()
            ->where('master_set_id', $set->id);

        if (!empty($keepIds)) {
            $deleteQuery->whereNotIn('id', $keepIds);
        }

        $deleteQuery->delete();

        $mainOrder = 0;

        foreach ($components as $cIndex => $c) {
            $mainData = $this->prepareComponentData($set->id, $c, null, $mainOrder++);

            Log::info('Prepared main component data', [
                'index' => $cIndex,
                'prepared' => $mainData,
            ]);

            $mainComp = null;

            if (isset($c['id']) && is_numeric($c['id'])) {
                $mainComp = MasterSetComponent::where('id', (int) $c['id'])
                    ->where('master_set_id', $set->id)
                    ->first();
            }

            if ($mainComp) {
                $mainComp->update($mainData);
            } else {
                $mainComp = MasterSetComponent::create($mainData);
            }

            $subOrder = 0;

            foreach (($c['subComponents'] ?? []) as $sIndex => $s) {
                $subData = $this->prepareComponentData($set->id, $s, $mainComp->id, $subOrder++);

                Log::info('Prepared sub component data', [
                    'main_index' => $cIndex,
                    'sub_index' => $sIndex,
                    'prepared' => $subData,
                ]);

                $subComp = null;

                if (isset($s['id']) && is_numeric($s['id'])) {
                    $subComp = MasterSetComponent::where('id', (int) $s['id'])
                        ->where('master_set_id', $set->id)
                        ->first();
                }

                if ($subComp) {
                    $subComp->update($subData);
                } else {
                    MasterSetComponent::create($subData);
                }
            }
        }

        Log::info('syncComponents done', [
            'master_set_id' => $set->id,
            'keep_ids' => $keepIds,
        ]);
    }
    // =========================================================================
    // Labor Sync Helpers
    // =========================================================================

    private function syncLabor(MasterSet $set, array $labor): void
    {
        MasterSetLabor::query()->where('master_set_id', $set->id)->delete();

        $order = 0;

        foreach ($labor as $l) {
            // 1. Get IDs
            $qualificationId = $l['qualification_id'] ?? null;
            $dpId = $l['department_position_id'] ?? null;

            $departmentId = null;
            $positionId = null;
            $employeeId = null;

            // 2. Determine Rate: Use editable rate first, fallback to 0
            $rate = (float) ($l['hourly_rate'] ?? 0);

            // 3. Handle Legacy Logic (DepartmentPosition) ONLY if no qualification is set
            if (!$qualificationId && $dpId) {
                $dp = DepartmentPosition::query()
                    ->with(['employee:id,salary_per_hour'])
                    ->find($dpId);

                if ($dp) {
                    $departmentId = $dp->department_id;
                    $positionId = $dp->position_id;
                    $employeeId = $dp->employee_id;
                    // Fallback to employee salary if rate is missing/zero
                    if ($rate <= 0) {
                        $rate = (float) ($dp->employee?->salary_per_hour ?? 0);
                    }
                }
            }

            // 4. Create Record
            $laborSource = in_array(($l['source'] ?? null), ['manual', 'tasks'], true)
                ? $l['source']
                : (!empty($l['is_manual']) ? 'manual' : 'manual');

            $createdLabor = MasterSetLabor::create([
                'master_set_id' => $set->id,
                'qualification_id' => $qualificationId, // Save this!
                'department_id' => $departmentId,
                'position_id' => $positionId,
                'employee_id' => $employeeId,
                'hourly_rate' => $rate,
                'hours' => (float) ($l['hours'] ?? 0),
                'sort_order' => $order++,
            ]);

            if (Schema::hasColumn('master_set_labors', 'source')) {
                $createdLabor->forceFill(['source' => $laborSource])->saveQuietly();
            }
        }
    }
    // =========================================================================
    // Tasks Sync Helpers
    // =========================================================================
    private function syncTasks(MasterSet $set, array $tasks): void
    {
        MasterSetTask::query()->where('master_set_id', $set->id)->delete();

        if (empty($tasks)) {
            return;
        }

        $activityIds = collect($tasks)
            ->pluck('phase_activity_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $tpl = DB::table('phase_activities')
            ->whereIn('id', $activityIds)
            ->select(
                'id',
                'title',
                'description',
                'duration',
                'duration_type',
                'notes',
                'priority',
                'percent',
                'phase_id',
                'lead_stage_id',
                'lead_sub_stage_id'
            )
            ->get()
            ->keyBy('id');

        $phaseIds = collect($tasks)
            ->pluck('task_phase_id')
            ->merge($tpl->pluck('phase_id'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $phaseMap = empty($phaseIds)
            ? collect()
            : DB::table('task_phases')
                ->whereIn('id', $phaseIds)
                ->select('id', 'phase_name', 'lead_stage_id', 'lead_sub_stage_id')
                ->get()
                ->keyBy('id');

        $leadStageIds = collect($tasks)
            ->pluck('lead_stage_id')
            ->merge(collect($tasks)->pluck('stage_id'))
            ->merge($tpl->pluck('lead_stage_id'))
            ->merge($phaseMap->pluck('lead_stage_id'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $leadStageMap = empty($leadStageIds)
            ? collect()
            : LeadStage::query()
                ->whereIn('id', $leadStageIds)
                ->get(['id', 'name', 'key'])
                ->keyBy('id');

        $order = 0;

        foreach ($tasks as $t) {
            $aid = (int) ($t['phase_activity_id'] ?? 0);
            if ($aid <= 0) {
                continue;
            }

            $a = $tpl[$aid] ?? null;
            $phaseId = (int) ($t['task_phase_id'] ?? ($a->phase_id ?? 0));
            $phase = $phaseId > 0 ? ($phaseMap[$phaseId] ?? null) : null;

            $leadStageId = $t['lead_stage_id']
                ?? $t['stage_id']
                ?? ($a->lead_stage_id ?? null)
                ?? ($phase->lead_stage_id ?? null);

            $leadSubStageId = $t['lead_sub_stage_id']
                ?? ($a->lead_sub_stage_id ?? null)
                ?? ($phase->lead_sub_stage_id ?? null);

            $leadStage = $leadStageId ? ($leadStageMap[$leadStageId] ?? null) : null;

            $createdTask = MasterSetTask::create([
                'master_set_id' => $set->id,

                // Legacy column is kept nullable. New code stores LeadStage here:
                'stage_id' => null,
                'lead_stage_id' => $leadStageId ? (int) $leadStageId : null,
                'lead_sub_stage_id' => $leadSubStageId ? (int) $leadSubStageId : null,

                'task_phase_id' => $phaseId ?: null,
                'phase_activity_id' => $aid,

                'stage_name' => $t['stage_name'] ?? ($leadStage?->name),
                'phase_name' => $t['phase_name'] ?? ($phase->phase_name ?? null),

                'title' => $t['title'] ?? ($a->title ?? null),
                'description' => $t['description'] ?? ($a->description ?? null),
                'duration' => $t['duration'] ?? ($a->duration ?? null),
                'duration_type' => $t['duration_type'] ?? ($a->duration_type ?? null),
                'notes' => $t['notes'] ?? ($a->notes ?? null),
                'priority' => $t['priority'] ?? ($a->priority ?? null),
                'percent' => array_key_exists('percent', $t) ? $t['percent'] : ($a->percent ?? null),

                'hours' => (float) ($t['hours'] ?? 0),
                'sort_order' => (int) ($t['sort_order'] ?? $order++),
            ]);

            if (!empty($t['task_labor']) && is_array($t['task_labor'])) {
                $laborInserts = [];
                $now = now();

                foreach ($t['task_labor'] as $tl) {
                    if (!empty($tl['qualification_id'])) {
                        $laborInserts[] = [
                            'master_set_task_id' => $createdTask->id,
                            'qualification_id' => (int) $tl['qualification_id'],
                            'rate' => (float) ($tl['rate'] ?? 0),
                            'hours' => (float) ($tl['hours'] ?? 0),
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if (!empty($laborInserts)) {
                    DB::table('master_set_task_labors')->insert($laborInserts);
                }
            }
        }
    }

    private function syncChecklists(MasterSet $set, array $items): void
    {
        $rows = collect($items)->map(function ($x, $index) {
            $cid = (int) ($x['maintenance_checklist_id'] ?? 0);
            $trigger = trim((string) ($x['trigger'] ?? 'start'));
            if ($trigger === '')
                $trigger = 'start';

            return [
                'maintenance_checklist_id' => $cid,
                'trigger' => $trigger,
                'is_required' => array_key_exists('is_required', $x) ? (bool) $x['is_required'] : true,
                'sort_order' => array_key_exists('sort_order', $x) ? (int) $x['sort_order'] : $index,
            ];
        })->filter(fn($r) => $r['maintenance_checklist_id'] > 0)->values();

        // remove duplicates: same checklist + trigger
        $rows = $rows->unique(fn($r) => $r['maintenance_checklist_id'] . '|' . $r['trigger'])->values();

        $ids = $rows->pluck('maintenance_checklist_id')->unique()->values()->all();

        $map = MaintenanceChecklist::query()
            ->whereNull('deleted_at')
            ->whereIn('id', $ids)
            ->get(['id', 'title', 'type'])
            ->keyBy('id');

        $rows = $rows->filter(fn($r) => isset($map[$r['maintenance_checklist_id']]))->values();

        MasterSetChecklist::query()
            ->where('master_set_id', $set->id)
            ->delete();

        if ($rows->isEmpty()) {
            return;
        }

        $now = now();

        $insert = $rows->map(function ($r) use ($set, $map, $now) {
            $c = $map[$r['maintenance_checklist_id']];

            return [
                'master_set_id' => (int) $set->id,
                'maintenance_checklist_id' => (int) $r['maintenance_checklist_id'],
                'trigger' => (string) $r['trigger'],
                'is_required' => (bool) $r['is_required'],
                'sort_order' => (int) $r['sort_order'],
                'checklist_title_snapshot' => (string) ($c->title ?? ''),
                'checklist_type_snapshot' => (string) ($c->type ?? ''),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        MasterSetChecklist::insert($insert);
    }

    public function distributorPrice($id)
    {
        $dp = DistributorPrice::with('distributor')->findOrFail($id);

        $price =
            $dp->distributor_price
            ?? $dp->unit_price
            ?? $dp->price
            ?? 0;

        return response()->json([
            'status' => 'ok',
            'data' => [
                'distributor_price_id' => $dp->id,
                'unit_price' => (float) $price,
                'distributor_name' => optional($dp->distributor)->name,
                'updated_at' => optional($dp->updated_at)->toDateTimeString(),
            ],
        ]);
    }

    public function checklistOptions(Request $request)
    {
        $data = $request->validate([
            'article_group_id' => ['required', 'integer'], // keep if your UI sends it
            'q' => ['nullable', 'string', 'max:200'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'status' => ['nullable', 'string', 'max:50'], // optional: filter by status
            'type' => ['nullable', 'string', 'max:50'],   // optional: filter by type
        ]);

        $q = trim((string) ($data['q'] ?? ''));
        $limit = (int) ($data['limit'] ?? 500);

        // If you want only active by default:
        $status = $data['status'] ?? 'active';
        $type = isset($data['type']) ? trim((string) $data['type']) : null;

        $rows = \App\Models\MaintenanceChecklist::query()
            ->select(['id', 'title', 'slug', 'description', 'logo_path', 'type', 'status', 'is_global'])
            ->whereNull('deleted_at')
            ->when($status !== '', fn($qry) => $qry->where('status', $status))
            ->when($type, fn($qry) => $qry->where('type', $type))
            ->when($q !== '', function ($qry) use ($q) {
                $qry->where(function ($x) use ($q) {
                    $x->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->withCount([
                // this uses MaintenanceChecklist::items() that you already have ✅
                'items as items_count' => fn($qq) => $qq->whereNull('deleted_at'),

            ])
            ->orderBy('title')
            ->limit($limit)
            ->get()
            ->map(function (\App\Models\MaintenanceChecklist $c) {
                return [
                    'id' => (int) $c->id,
                    'title' => (string) ($c->title ?? ''),
                    'slug' => (string) ($c->slug ?? ''),
                    'type' => (string) ($c->type ?? ''),
                    'status' => (string) ($c->status ?? ''),
                    'is_global' => (bool) ($c->is_global ?? false),
                    'logo_path' => $c->logo_path,
                    'description' => $c->description,
                    'items_count' => (int) ($c->items_count ?? 0),
                ];
            })
            ->values();

        return response()->json(['status' => 'ok', 'data' => $rows]);
    }


    public function validateChecklistAttach(Request $request)
    {
        $data = $request->validate([
            'master_set_id' => ['required', 'integer', 'exists:master_sets,id'],
            'maintenance_checklist_id' => ['required', 'integer', 'exists:maintenance_checklists,id'],
            'trigger' => ['nullable', 'string', 'max:50'], // optional if you allow multiple per trigger
        ]);

        $trigger = trim((string) ($data['trigger'] ?? ''));
        if ($trigger === '')
            $trigger = null;

        $qry = DB::table('master_set_checklists')
            ->where('master_set_id', $data['master_set_id'])
            ->where('maintenance_checklist_id', $data['maintenance_checklist_id']);

        // If you consider trigger part of uniqueness:
        if ($trigger !== null) {
            $qry->where('trigger', $trigger);
        }

        $exists = $qry->exists();

        return response()->json([
            'status' => 'ok',
            'ok' => !$exists,
            'reason' => $exists ? 'already_attached' : null,
        ]);
    }

    public function duplicate(Request $request, MasterSet $masterSet)
    {
        $data = $request->validate([
            'copy_material' => ['nullable', 'boolean'],
            'copy_tasks' => ['nullable', 'boolean'],
            'copy_labor' => ['nullable', 'boolean'],
            'copy_checklists' => ['nullable', 'boolean'],

            'target_mode' => ['nullable', 'in:clone,existing'],
            'target_article_group_id' => ['nullable', 'integer', 'exists:article_groups,id'],
            'target_master_set_id' => ['nullable', 'integer', 'exists:master_sets,id'],
            'new_name' => ['nullable', 'string', 'max:255'],
        ]);

        $copyMaterial = array_key_exists('copy_material', $data) ? (bool) $data['copy_material'] : true;
        $copyTasks = array_key_exists('copy_tasks', $data) ? (bool) $data['copy_tasks'] : true;
        $copyLabor = array_key_exists('copy_labor', $data) ? (bool) $data['copy_labor'] : true;
        $copyChecklists = array_key_exists('copy_checklists', $data) ? (bool) $data['copy_checklists'] : true;

        $targetMode = $data['target_mode'] ?? 'clone';
        $targetArticleGroupId = $data['target_article_group_id'] ?? $masterSet->article_group_id;
        $targetMasterSetId = $data['target_master_set_id'] ?? null;
        $newName = trim((string) ($data['new_name'] ?? ''));

        if (!$copyMaterial && !$copyTasks && !$copyLabor && !$copyChecklists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nothing selected to duplicate.',
            ], 422);
        }

        if ($targetMode === 'existing' && !$targetMasterSetId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please select a target MasterSet.',
            ], 422);
        }

        $masterSet->load([
            'components' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'labor' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'tasks' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'tasks.labor',
            'checklistLinks' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
        ]);

        return DB::transaction(function () use ($masterSet, $copyMaterial, $copyTasks, $copyLabor, $copyChecklists, $targetMode, $targetArticleGroupId, $targetMasterSetId, $newName) {
            if ($targetMode === 'clone') {
                $newSet = MasterSet::create([
                    'article_group_id' => $targetArticleGroupId,
                    'name' => $newName !== '' ? $newName : (($masterSet->name ?: 'MasterSet') . ' (Kopie)'),
                    'description' => $masterSet->description,
                    'responsible_department_position_id' => $masterSet->responsible_department_position_id,
                    'status' => $masterSet->status ?? 'Published',
                ]);

                if ($copyMaterial) {
                    $this->duplicateComponentsToTarget($masterSet, $newSet);
                }

                if ($copyLabor) {
                    $this->duplicateLaborToTarget($masterSet, $newSet);
                }

                if ($copyTasks) {
                    $this->duplicateTasksToTarget($masterSet, $newSet);
                }

                if ($copyChecklists) {
                    $this->duplicateChecklistsToTarget($masterSet, $newSet);
                }

                return response()->json([
                    'status' => 'ok',
                    'id' => $newSet->id,
                    'target_mode' => 'clone',
                    'message' => 'MasterSet duplicated successfully.',
                ]);
            }

            $targetSet = MasterSet::query()->findOrFail($targetMasterSetId);

            if ((int) $targetSet->id === (int) $masterSet->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Source and target MasterSet cannot be the same.',
                ], 422);
            }

            if ((int) $targetSet->article_group_id !== (int) $targetArticleGroupId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The selected target MasterSet does not belong to the selected article group.',
                ], 422);
            }

            if ($copyMaterial) {
                $this->duplicateComponentsToTarget($masterSet, $targetSet);
            }

            if ($copyLabor) {
                $this->duplicateLaborToTarget($masterSet, $targetSet);
            }

            if ($copyTasks) {
                $this->duplicateTasksToTarget($masterSet, $targetSet);
            }

            if ($copyChecklists) {
                $this->duplicateChecklistsToTarget($masterSet, $targetSet);
            }

            return response()->json([
                'status' => 'ok',
                'id' => $targetSet->id,
                'target_mode' => 'existing',
                'message' => 'Data copied into existing MasterSet successfully.',
            ]);
        });
    }

    public function duplicateOptions(Request $request, MasterSet $masterSet)
    {
        $selectedArticleGroupId = $request->integer('article_group_id') ?: $masterSet->article_group_id;

        $articleGroups = ArticleGroup::query()
            ->select('id', 'article_group')
            ->orderBy('article_group')
            ->get()
            ->map(fn($group) => [
                'id' => (int) $group->id,
                'name' => (string) $group->article_group,
            ])
            ->values();

        $targetSets = MasterSet::query()
            ->select('id', 'article_group_id', 'name')
            ->where('article_group_id', $selectedArticleGroupId)
            ->where('id', '!=', $masterSet->id)
            ->orderBy('name')
            ->get()
            ->map(fn($set) => [
                'id' => (int) $set->id,
                'article_group_id' => (int) $set->article_group_id,
                'name' => (string) $set->name,
            ])
            ->values();

        return response()->json([
            'status' => 'ok',
            'data' => [
                'source' => [
                    'id' => (int) $masterSet->id,
                    'name' => (string) $masterSet->name,
                    'article_group_id' => (int) $masterSet->article_group_id,
                ],
                'article_groups' => $articleGroups,
                'selected_article_group_id' => (int) $selectedArticleGroupId,
                'target_sets' => $targetSets,
            ],
        ]);
    }

    private function duplicateComponentsToTarget(MasterSet $sourceSet, MasterSet $targetSet): void
    {
        $maxSort = MasterSetComponent::query()
            ->where('master_set_id', $targetSet->id)
            ->whereNull('parent_id')
            ->max('sort_order');

        $nextRootSort = $maxSort !== null ? ((int) $maxSort + 1) : 0;

        $rootComponents = $sourceSet->components
            ->whereNull('parent_id')
            ->sortBy('sort_order')
            ->values();

        foreach ($rootComponents as $root) {
            $newRoot = MasterSetComponent::create([
                'master_set_id' => $targetSet->id,
                'parent_id' => null,
                'product_id' => $root->product_id,
                'article_no' => $root->article_no,
                'distributor_id' => $root->distributor_id,
                'distributor_price_id' => $root->distributor_price_id,
                'distributor_article_no' => $root->distributor_article_no,
                'unit_price' => $root->unit_price,
                'qty' => $root->qty,
                'description' => $root->description,
                'sort_order' => $nextRootSort++,
                'purchase_price' => $root->purchase_price,
                'margin' => $root->margin,
                'skonto' => $root->skonto,
                'payment_terms' => $root->payment_terms,
                'availability' => $root->availability,
                'type' => $root->type,
                'is_stammartikel' => $root->is_stammartikel,
                'is_favorite' => $root->is_favorite,
                'measure' => $root->measure,
                // ✅ ADDED EXTRACTION HERE:
                'price_unit' => $root->price_unit,
                'vpe' => $root->vpe,
            ]);

            $this->duplicateComponentDescriptions($root->id, $newRoot->id);

            $children = $sourceSet->components
                ->where('parent_id', $root->id)
                ->sortBy('sort_order')
                ->values();

            $childSort = 0;

            foreach ($children as $child) {
                $newChild = MasterSetComponent::create([
                    'master_set_id' => $targetSet->id,
                    'parent_id' => $newRoot->id,
                    'product_id' => $child->product_id,
                    'article_no' => $child->article_no,
                    'distributor_id' => $child->distributor_id,
                    'distributor_price_id' => $child->distributor_price_id,
                    'unit_price' => $child->unit_price,
                    'qty' => $child->qty,
                    'description' => $child->description,
                    'sort_order' => $childSort++,
                    'distributor_article_no' => $child->distributor_article_no,
                    // ✅ FIX: The original code accidentally copied the $root's data for the child. Changed it to $child.
                    'purchase_price' => $child->purchase_price,
                    'margin' => $child->margin,
                    'skonto' => $child->skonto,
                    'payment_terms' => $child->payment_terms,
                    'availability' => $child->availability,
                    'type' => $child->type,
                    'is_stammartikel' => $child->is_stammartikel,
                    'is_favorite' => $child->is_favorite,
                    'measure' => $child->measure,
                    // ✅ ADDED EXTRACTION HERE:
                    'price_unit' => $child->price_unit,
                    'vpe' => $child->vpe,
                ]);

                $this->duplicateComponentDescriptions($child->id, $newChild->id);
            }
        }
    }

    private function duplicateComponentDescriptions(int $sourceComponentId, int $targetComponentId): void
    {
        $variants = MasterSetComponentDescription::query()
            ->where('master_set_component_id', $sourceComponentId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($variants as $variant) {
            MasterSetComponentDescription::create([
                'master_set_component_id' => $targetComponentId,
                'context' => $variant->context,
                'title' => $variant->title,
                'sort_order' => $variant->sort_order,
                'delta' => $variant->delta,
                'html' => $variant->html,
                'text' => $variant->text,
            ]);
        }
    }

    private function duplicateLaborToTarget(MasterSet $sourceSet, MasterSet $targetSet): void
    {
        $maxSort = MasterSetLabor::query()
            ->where('master_set_id', $targetSet->id)
            ->max('sort_order');

        $nextSort = $maxSort !== null ? ((int) $maxSort + 1) : 0;

        foreach ($sourceSet->labor->sortBy('sort_order')->values() as $row) {
            $createdLabor = MasterSetLabor::create([
                'master_set_id' => $targetSet->id,
                'department_id' => $row->department_id,
                'qualification_id' => $row->qualification_id,
                'position_id' => $row->position_id,
                'employee_id' => $row->employee_id,
                'hourly_rate' => $row->hourly_rate,
                'hours' => $row->hours,
                'sort_order' => $nextSort++,
            ]);

            if (Schema::hasColumn('master_set_labors', 'source')) {
                $createdLabor->forceFill(['source' => $row->source ?: 'manual'])->saveQuietly();
            }
        }
    }

    private function duplicateTasksToTarget(MasterSet $sourceSet, MasterSet $targetSet): void
    {
        $maxSort = MasterSetTask::query()
            ->where('master_set_id', $targetSet->id)
            ->max('sort_order');

        $nextSort = $maxSort !== null ? ((int) $maxSort + 1) : 0;

        $existingPhaseActivityIds = MasterSetTask::query()
            ->where('master_set_id', $targetSet->id)
            ->whereNotNull('phase_activity_id')
            ->pluck('phase_activity_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $existingLookup = array_fill_keys($existingPhaseActivityIds, true);

        foreach ($sourceSet->tasks->sortBy('sort_order')->values() as $task) {
            $phaseActivityId = $task->phase_activity_id !== null
                ? (int) $task->phase_activity_id
                : null;

            if ($phaseActivityId !== null && isset($existingLookup[$phaseActivityId])) {
                continue;
            }

            $newTask = MasterSetTask::create([
                'master_set_id' => $targetSet->id,

                // Keep legacy column empty for new copies.
                'stage_id' => null,
                'lead_stage_id' => $task->lead_stage_id ?: $task->stage_id,
                'lead_sub_stage_id' => $task->lead_sub_stage_id,

                'task_phase_id' => $task->task_phase_id,
                'phase_activity_id' => $task->phase_activity_id,
                'stage_name' => $task->stage_name,
                'phase_name' => $task->phase_name,
                'title' => $task->title,
                'description' => $task->description,
                'duration' => $task->duration,
                'duration_type' => $task->duration_type,
                'notes' => $task->notes,
                'priority' => $task->priority,
                'percent' => $task->percent,
                'hours' => $task->hours,
                'sort_order' => $nextSort++,
            ]);

            foreach ($task->labor as $taskLabor) {
                MasterSetTaskLabor::create([
                    'master_set_task_id' => $newTask->id,
                    'qualification_id' => $taskLabor->qualification_id,
                    'hours' => $taskLabor->hours,
                    'rate' => $taskLabor->rate,
                ]);
            }

            if ($phaseActivityId !== null) {
                $existingLookup[$phaseActivityId] = true;
            }
        }
    }

    private function duplicateChecklistsToTarget(MasterSet $sourceSet, MasterSet $targetSet): void
    {
        $maxSort = MasterSetChecklist::query()
            ->where('master_set_id', $targetSet->id)
            ->max('sort_order');

        $nextSort = $maxSort !== null ? ((int) $maxSort + 1) : 0;

        $existingChecklistPairs = MasterSetChecklist::query()
            ->where('master_set_id', $targetSet->id)
            ->get(['maintenance_checklist_id', 'trigger'])
            ->mapWithKeys(function ($row) {
                $key = (int) $row->maintenance_checklist_id . '|' . (string) ($row->trigger ?: 'start');
                return [$key => true];
            })
            ->all();

        foreach ($sourceSet->checklistLinks->sortBy('sort_order')->values() as $link) {
            $trigger = $link->trigger ?: 'start';
            $uniqueKey = (int) $link->maintenance_checklist_id . '|' . $trigger;

            // Skip duplicate checklist + trigger in same target set
            if (isset($existingChecklistPairs[$uniqueKey])) {
                continue;
            }

            MasterSetChecklist::create([
                'master_set_id' => $targetSet->id,
                'maintenance_checklist_id' => $link->maintenance_checklist_id,
                'trigger' => $trigger,
                'is_required' => (bool) $link->is_required,
                'sort_order' => $nextSort++,
                'checklist_title_snapshot' => $link->checklist_title_snapshot,
                'checklist_type_snapshot' => $link->checklist_type_snapshot,
            ]);

            $existingChecklistPairs[$uniqueKey] = true;
        }
    }

    public function saveCostingSettings(Request $request, MasterSet $masterSet)
    {
        $data = $request->validate([
            'costing_set_id' => ['nullable', 'integer', 'exists:costing_sets,id'],
            'costing_rate_mode' => ['nullable', 'in:full_cost,sell_rate,wage_only'],
            'costing_fallback' => ['nullable', 'in:task_rate,qualification_default,zero'],
        ]);

        $masterSet->update([
            'costing_set_id' => $data['costing_set_id'] ?? null,
            'costing_rate_mode' => $data['costing_rate_mode'] ?? ($masterSet->costing_rate_mode ?? 'full_cost'),
            'costing_fallback' => $data['costing_fallback'] ?? ($masterSet->costing_fallback ?? 'task_rate'),
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function taskCostingPayload(Request $request, MasterSet $masterSet)
    {
        $data = $request->validate([
            'costing_set_id' => ['nullable', 'integer', 'exists:costing_sets,id'],
            'rate_mode' => ['nullable', 'in:full_cost,sell_rate,wage_only'],
            'fallback' => ['nullable', 'in:task_rate,qualification_default,zero'],
        ]);

        $rateMode = $data['rate_mode'] ?? ($masterSet->costing_rate_mode ?? 'full_cost');
        $fallback = $data['fallback'] ?? ($masterSet->costing_fallback ?? 'task_rate');

        $costingSet = null;

        if (!empty($data['costing_set_id'])) {
            $costingSet = CostingSet::query()
                ->where('is_active', 1)
                ->find($data['costing_set_id']);
        }

        if (!$costingSet && $masterSet->costing_set_id) {
            $costingSet = CostingSet::query()
                ->where('is_active', 1)
                ->find($masterSet->costing_set_id);
        }

        if (!$costingSet) {
            $costingSet = CostingSet::query()
                ->where('is_active', 1)
                ->orderByDesc('is_default')
                ->orderByDesc('id')
                ->first();
        }

        $awMinutes = (int) ($costingSet?->aw_minutes ?? 6);
        if ($awMinutes <= 0) {
            $awMinutes = 6;
        }

        $roles = collect();

        if ($costingSet) {
            $roles = CostingSetRole::query()
                ->with('qualification:id,name,default_price')
                ->where('costing_set_id', $costingSet->id)
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->get();
        }

        $roleMap = $roles->keyBy('qualification_id');

        $masterSet->load([
            'tasks' => fn($q) => $q->orderBy('sort_order')->orderBy('id'),
            'tasks.labor.qualification:id,name,default_price',
        ]);

        $pickRate = function (int $qid, ?float $taskRate, ?float $qualDefault) use ($roleMap, $rateMode, $fallback): float {
            $role = $roleMap->get($qid);

            $roleRate = 0.0;

            if ($role) {
                if ($rateMode === 'sell_rate') {
                    $roleRate = (float) ($role->sell_rate_per_hour ?? 0);
                } elseif ($rateMode === 'wage_only') {
                    $roleRate = (float) ($role->wage_cost_per_hour ?? 0);
                } else {
                    $roleRate = (float) ($role->full_cost_rate_per_hour ?? 0);
                }
            }

            if ($roleRate > 0) {
                return $roleRate;
            }

            if ($fallback === 'task_rate') {
                return (float) ($taskRate ?? 0);
            }

            if ($fallback === 'qualification_default') {
                return (float) ($qualDefault ?? 0);
            }

            return 0.0;
        };

        $rows = [];
        $totalHours = 0.0;
        $totalCost = 0.0;

        foreach ($masterSet->tasks as $t) {
            foreach (($t->labor ?? collect()) as $tl) {
                $qid = (int) $tl->qualification_id;
                $hours = (float) ($tl->hours ?? 0);

                $taskRate = (float) ($tl->rate ?? 0);
                $qualDefault = (float) ($tl->qualification?->default_price ?? 0);

                $rate = $pickRate($qid, $taskRate, $qualDefault);
                $cost = $hours * $rate;

                $aw = ($hours * 60.0) / $awMinutes;

                $role = $roleMap->get($qid);

                $rows[] = [
                    'task_id' => (int) $t->id,
                    'sort_order' => (int) ($t->sort_order ?? 0),
                    'task_title' => (string) ($t->title ?? ''),
                    'stage_name' => (string) ($t->stage_name ?? ''),
                    'phase_name' => (string) ($t->phase_name ?? ''),

                    'qualification_id' => $qid,
                    'qualification' => (string) ($tl->qualification?->name ?? 'Unknown'),

                    'hours' => $hours,
                    'rate_per_hour' => $rate,
                    'cost' => $cost,

                    'aw' => (float) $aw,
                    'eur_per_aw' => $aw > 0 ? (float) ($cost / $aw) : 0.0,

                    'task_rate_raw' => $taskRate,
                    'qual_default_raw' => $qualDefault,
                    'role_rate_raw' => $role ? (
                        $rateMode === 'sell_rate'
                        ? (float) ($role->sell_rate_per_hour ?? 0)
                        : ($rateMode === 'wage_only'
                            ? (float) ($role->wage_cost_per_hour ?? 0)
                            : (float) ($role->full_cost_rate_per_hour ?? 0))
                    ) : 0.0,
                ];

                $totalHours += $hours;
                $totalCost += $cost;
            }
        }

        $taskTotals = collect($rows)
            ->groupBy('task_id')
            ->map(function ($g) use ($awMinutes) {
                $hours = (float) $g->sum('hours');
                $cost = (float) $g->sum('cost');
                $aw = ($hours * 60.0) / $awMinutes;

                return [
                    'task_id' => (int) $g->first()['task_id'],
                    'task_title' => (string) $g->first()['task_title'],
                    'hours' => $hours,
                    'cost' => $cost,
                    'aw' => (float) $aw,
                    'eur_per_aw' => $aw > 0 ? (float) ($cost / $aw) : 0.0,
                ];
            })
            ->values();

        return response()->json([
            'status' => 'ok',
            'data' => [
                'master_set_id' => (int) $masterSet->id,
                'settings' => [
                    'rate_mode' => $rateMode,
                    'fallback' => $fallback,
                ],
                'costing_set' => $costingSet ? [
                    'id' => (int) $costingSet->id,
                    'name' => (string) $costingSet->name,
                    'aw_minutes' => (int) $awMinutes,
                ] : null,
                'roles' => $roles->map(fn($r) => [
                    'qualification_id' => (int) $r->qualification_id,
                    'qualification' => (string) ($r->qualification?->name ?? ''),
                    'full_cost_rate_per_hour' => (float) ($r->full_cost_rate_per_hour ?? 0),
                    'sell_rate_per_hour' => (float) ($r->sell_rate_per_hour ?? 0),
                    'wage_cost_per_hour' => (float) ($r->wage_cost_per_hour ?? 0),
                ])->values(),
                'table' => [
                    'rows' => $rows,
                    'task_totals' => $taskTotals,
                    'totals' => [
                        'hours' => (float) $totalHours,
                        'cost' => (float) $totalCost,
                        'aw' => (float) (($totalHours * 60.0) / $awMinutes),
                    ],
                ],
            ],
        ]);
    }

    public function hydrateGroupComponents(\App\Models\ArticleGroup $articleGroup)
    {
        $sets = \App\Models\MasterSet::query()
            ->where('article_group_id', $articleGroup->id)
            ->with([
                'components.product',
                'components.distributor',
                'components.distributorPrice.distributor',
            ])
            ->get();

        $updatedSets = 0;
        $updatedComponents = 0;

        foreach ($sets as $masterSet) {
            $setChanged = false;

            foreach ($masterSet->components as $component) {
                $product = $component->product;
                $dp = $component->distributorPrice;

                $dirty = false;

                // 1. Existing Description Check
                if (empty($component->description)) {
                    $desc = $product?->short_description ?? $product?->description ?? $product?->product_description ?? null;
                    if (!empty($desc)) {
                        $component->description = $desc;
                        $dirty = true;
                    }
                }

                // 2. Existing Unit Price Check
                if ((is_null($component->unit_price) || (float) $component->unit_price <= 0) && !empty($dp?->purchase_price)) {
                    $component->unit_price = (float) $dp->purchase_price;
                    $dirty = true;
                }

                // 3. Existing Distributor ID Check
                if (empty($component->distributor_id) && !empty($dp?->distributor_id)) {
                    $component->distributor_id = (int) $dp->distributor_id;
                    $dirty = true;
                }

                // 🟢 NEW: Sync Distributor Article Number from distributor_prices
                if (empty($component->distributor_article_no) && !empty($dp?->article_no)) {
                    $component->distributor_article_no = $dp->article_no;
                    $dirty = true;
                }

                if ($dirty) {
                    $component->save();
                    $updatedComponents++;
                    $setChanged = true;
                }
            }

            if ($setChanged) {
                $updatedSets++;
            }
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Alle Sets aktualisiert.',
            'updated_sets' => $updatedSets,
            'updated_components' => $updatedComponents,
        ]);
    }

    public function hydrateSetComponents(MasterSet $masterSet)
    {
        $masterSet->load([
            'components.product',
            'components.distributor',
            'components.distributorPrice.distributor',
        ]);

        $updatedComponents = 0;

        foreach ($masterSet->components as $component) {
            $product = $component->product;
            $dp = $component->distributorPrice;
            $dirty = false;

            if (empty($component->description)) {
                $desc = $product?->short_description ?? $product?->description ?? $product?->product_description ?? null;
                if (!empty($desc)) {
                    $component->description = $desc;
                    $dirty = true;
                }
            }

            if ((is_null($component->unit_price) || (float) $component->unit_price <= 0) && !empty($dp?->purchase_price)) {
                $component->unit_price = (float) $dp->purchase_price;
                $dirty = true;
            }

            if (empty($component->distributor_id) && !empty($dp?->distributor_id)) {
                $component->distributor_id = (int) $dp->distributor_id;
                $dirty = true;
            }

            // 🟢 NEW: Sync Distributor Article Number
            if (empty($component->distributor_article_no) && !empty($dp?->article_no)) {
                $component->distributor_article_no = $dp->article_no;
                $dirty = true;
            }

            if ($dirty) {
                $component->save();
                $updatedComponents++;
            }
        }
        // Refresh relationships so the UI gets the updated data back instantly
        return response()->json([
            'status' => 'ok',
            'message' => 'Set aktualisiert.',
            'data' => $masterSet->fresh([
                'components.product',
                'components.distributor',
                'components.distributorPrice.distributor',
            ])
        ]);
    }




}