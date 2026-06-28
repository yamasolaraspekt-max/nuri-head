<?php

namespace App\Http\Controllers\Product\MasterSet;
use App\Http\Controllers\Controller;
use App\Models\MasterSet;
use App\Models\MasterSetGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterSetGroupController extends Controller
{
     
    // GET /admin/master-sets/groups/list?article_group_id=..&q=..
        public function list(Request $request)
        {
            $data = $request->validate([
                'article_group_id' => ['required', 'integer'],
                'q' => ['nullable', 'string'],
            ]);

            $articleGroupId = (int) $data['article_group_id'];
            $q = trim((string) ($data['q'] ?? ''));

            // 1. Get Groups
            $groups = MasterSetGroup::query()
                ->where('article_group_id', $articleGroupId)
                ->when($q !== '', fn ($qq) => $qq->where('name', 'like', "%{$q}%"))
                ->orderBy('name')
                ->get(['id', 'article_group_id', 'name', 'description', 'color']);

            $groupIds = $groups->pluck('id')->all();

            if (empty($groupIds)) {
                return response()->json(['status' => 'ok', 'data' => []]);
            }

            // 2. Get Set IDs per Group to map them later
            $groupSetMap = DB::table('master_set_group_master_set')
                ->whereIn('master_set_group_id', $groupIds)
                ->get()
                ->groupBy('master_set_group_id')
                ->map(fn($rows) => $rows->pluck('master_set_id')->all());

            // Flatten all Set IDs to query aggregates efficiently in one go
            $allSetIds = $groupSetMap->flatten()->unique()->all();

            // 3. Pre-calculate Aggregates for ALL involved Sets
            $setStats = [];
            if (!empty($allSetIds)) {
                // Components (Main vs Sub)
                $comps = DB::table('master_set_components')
                    ->whereIn('master_set_id', $allSetIds)
                    ->selectRaw("master_set_id, 
                        SUM(CASE WHEN parent_id IS NULL THEN 1 ELSE 0 END) as main_cnt,
                        SUM(CASE WHEN parent_id IS NOT NULL THEN 1 ELSE 0 END) as sub_cnt")
                    ->groupBy('master_set_id')
                    ->get()->keyBy('master_set_id');

                // Labor
                $labor = DB::table('master_set_labor')
                    ->whereIn('master_set_id', $allSetIds)
                    ->selectRaw("master_set_id, COUNT(*) as cnt")
                    ->groupBy('master_set_id')
                    ->pluck('cnt', 'master_set_id');

                // Tasks
                $tasks = DB::table('master_set_tasks')
                    ->whereIn('master_set_id', $allSetIds)
                    ->selectRaw("master_set_id, COUNT(*) as cnt")
                    ->groupBy('master_set_id')
                    ->pluck('cnt', 'master_set_id');

                // Protocols
                $protos = DB::table('master_set_checklists')
                    ->whereIn('master_set_id', $allSetIds)
                    ->selectRaw("master_set_id, COUNT(*) as cnt")
                    ->groupBy('master_set_id')
                    ->pluck('cnt', 'master_set_id');

                // Map back to array for easy lookup
                foreach ($allSetIds as $sid) {
                    $setStats[$sid] = [
                        'main' => $comps[$sid]->main_cnt ?? 0,
                        'sub' => $comps[$sid]->sub_cnt ?? 0,
                        'labor' => $labor[$sid] ?? 0,
                        'task' => $tasks[$sid] ?? 0,
                        'proto' => $protos[$sid] ?? 0,
                    ];
                }
            }

            // 4. Map Group -> Sum of Set Stats
            $payload = $groups->map(function (MasterSetGroup $g) use ($groupSetMap, $setStats) {
                $mySetIds = $groupSetMap[$g->id] ?? [];
                
                // Initialize totals
                $stats = [
                    'sets_count' => count($mySetIds),
                    'mainCount' => 0, 'subCount' => 0, 'laborCount' => 0, 
                    'taskCount' => 0, 'protocolCount' => 0
                ];

                // Sum up stats from all sets inside this group
                foreach ($mySetIds as $sid) {
                    if (isset($setStats[$sid])) {
                        $stats['mainCount'] += $setStats[$sid]['main'];
                        $stats['subCount'] += $setStats[$sid]['sub'];
                        $stats['laborCount'] += $setStats[$sid]['labor'];
                        $stats['taskCount'] += $setStats[$sid]['task'];
                        $stats['protocolCount'] += $setStats[$sid]['proto'];
                    }
                }

                return [
                    'id' => $g->id,
                    'article_group_id' => $g->article_group_id,
                    'name' => $g->name,
                    'description' => $g->description,
                    'color' => $g->color ?? '#74b2d4',
                    'stats' => $stats
                ];
            });

            return response()->json(['status' => 'ok', 'data' => $payload]);
        }

    // GET /admin/master-sets/groups/sets?article_group_id=..&q=..
    public function sets(Request $request)
    {
        $data = $request->validate([
            'article_group_id' => ['required', 'integer'],
            'q' => ['nullable', 'string'],
        ]);

        $articleGroupId = (int) $data['article_group_id'];
        $q = trim((string) ($data['q'] ?? ''));

        $sets = MasterSet::query()
            ->where('article_group_id', $articleGroupId)
            ->when($q !== '', fn ($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->limit(400)
            ->get(['id', 'article_group_id', 'name', 'description']);

        $pivotRows = DB::table('master_set_group_master_set')
            ->join('master_set_groups', 'master_set_groups.id', '=', 'master_set_group_master_set.master_set_group_id')
            ->where('master_set_groups.article_group_id', $articleGroupId)
            ->get([
                'master_set_group_master_set.master_set_id',
                'master_set_group_master_set.master_set_group_id',
            ]);

        $map = [];
        foreach ($pivotRows as $row) {
            $sid = (int) $row->master_set_id;
            $gid = (int) $row->master_set_group_id;
            $map[$sid][] = $gid;
        }

        $payload = $sets->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'description' => $s->description,
            'in_groups' => $map[$s->id] ?? [],
        ]);

        return response()->json(['status' => 'ok', 'data' => $payload]);
    }

    // POST /admin/master-sets/groups
    public function store(Request $request)
    {
        $data = $request->validate([
            'article_group_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'master_set_ids' => ['nullable', 'array'],
            'master_set_ids.*' => ['integer'],
        ]);

        $articleGroupId = (int) $data['article_group_id'];

        $group = DB::transaction(function () use ($data, $articleGroupId) {
            $group = MasterSetGroup::create([
                'article_group_id' => $articleGroupId,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? null,
            ]);

            $ids = collect($data['master_set_ids'] ?? [])
                ->filter()
                ->unique()
                ->values();

            if ($ids->isNotEmpty()) {
                $validSetIds = MasterSet::query()
                    ->where('article_group_id', $articleGroupId)
                    ->whereIn('id', $ids)
                    ->pluck('id')
                    ->all();

                $group->masterSets()->sync($validSetIds);
            }

            return $group->fresh();
        });

        return response()->json([
            'status' => 'ok',
            'data' => [
                'id' => $group->id,
                'article_group_id' => $group->article_group_id,
                'name' => $group->name,
                'description' => $group->description,
                'color' => $group->color,
            ],
        ]);
    }

    // PUT /admin/master-sets/groups/{group}
    public function update(Request $request, MasterSetGroup $group)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'master_set_ids' => ['nullable', 'array'],
            'master_set_ids.*' => ['integer'],
        ]);

        DB::transaction(function () use ($group, $data) {
            $group->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? null,
            ]);

            if (array_key_exists('master_set_ids', $data)) {
                $ids = collect($data['master_set_ids'] ?? [])
                    ->filter()
                    ->unique()
                    ->values();

                $validSetIds = MasterSet::query()
                    ->where('article_group_id', $group->article_group_id)
                    ->whereIn('id', $ids)
                    ->pluck('id')
                    ->all();

                $group->masterSets()->sync($validSetIds);
            }
        });

        $group->refresh();

        return response()->json([
            'status' => 'ok',
            'data' => [
                'id' => $group->id,
                'article_group_id' => $group->article_group_id,
                'name' => $group->name,
                'description' => $group->description,
                'color' => $group->color,
            ],
        ]);
    }

    // DELETE /admin/master-sets/groups/{group}
    public function destroy(MasterSetGroup $group)
    {
        $group->delete();
        return response()->json(['status' => 'ok']);
    }

    // GET /admin/master-sets/groups/{group}/stats
    public function stats(MasterSetGroup $group)
    {
        $setIds = DB::table('master_set_group_master_set')
            ->where('master_set_group_id', $group->id)
            ->pluck('master_set_id')
            ->all();

        if (empty($setIds)) {
            return response()->json(['status' => 'ok', 'data' => [
                'group_id' => $group->id,
                'sets_count' => 0,
                'mainCount' => 0,
                'subCount' => 0,
                'laborCount' => 0,
                'taskCount' => 0,
                'protocolCount' => 0,
                'mainCost' => 0,
                'subCost' => 0,
                'laborCost' => 0,
                'total' => 0,
                'mainPct' => 0,
                'subPct' => 0,
                'laborPct' => 0,
            ]]);
        }

        $mainCount = (int) DB::table('master_set_components')
            ->whereIn('master_set_id', $setIds)
            ->whereNull('parent_id')
            ->count();

        $subCount = (int) DB::table('master_set_components')
            ->whereIn('master_set_id', $setIds)
            ->whereNotNull('parent_id')
            ->count();

        $laborCount = (int) DB::table('master_set_labor')
            ->whereIn('master_set_id', $setIds)
            ->count();

        $taskCount = (int) DB::table('master_set_tasks')
            ->whereIn('master_set_id', $setIds)
            ->count();

        $protocolCount = (int) DB::table('master_set_checklists')
            ->whereIn('master_set_id', $setIds)
            ->count();

        $mainCost = (float) DB::table('master_set_components')
            ->whereIn('master_set_id', $setIds)
            ->whereNull('parent_id')
            ->selectRaw('COALESCE(SUM(COALESCE(unit_price,0) * COALESCE(qty,0)),0) as sum')
            ->value('sum');

        $subCost = (float) DB::table('master_set_components')
            ->whereIn('master_set_id', $setIds)
            ->whereNotNull('parent_id')
            ->selectRaw('COALESCE(SUM(COALESCE(unit_price,0) * COALESCE(qty,0)),0) as sum')
            ->value('sum');

        $laborCost = (float) DB::table('master_set_labor')
            ->whereIn('master_set_id', $setIds)
            ->selectRaw('COALESCE(SUM(COALESCE(hourly_rate,0) * COALESCE(hours,0)),0) as sum')
            ->value('sum');

        $total = $mainCost + $subCost + $laborCost;

        $mainPct = $total > 0 ? round(($mainCost / $total) * 100, 2) : 0;
        $subPct = $total > 0 ? round(($subCost / $total) * 100, 2) : 0;
        $laborPct = $total > 0 ? round(($laborCost / $total) * 100, 2) : 0;

        return response()->json(['status' => 'ok', 'data' => [
            'group_id' => $group->id,
            'sets_count' => count($setIds),

            'mainCount' => $mainCount,
            'subCount' => $subCount,
            'laborCount' => $laborCount,
            'taskCount' => $taskCount,
            'protocolCount' => $protocolCount,

            'mainCost' => $mainCost,
            'subCost' => $subCost,
            'laborCost' => $laborCost,
            'total' => $total,

            'mainPct' => $mainPct,
            'subPct' => $subPct,
            'laborPct' => $laborPct,
        ]]);
    }

    // GET /admin/master-sets/groups/{group}/master-sets
    public function groupMasterSets(MasterSetGroup $group)
    {
        $setIds = DB::table('master_set_group_master_set')
            ->where('master_set_group_id', $group->id)
            ->pluck('master_set_id')
            ->all();

        if (empty($setIds)) {
            return response()->json(['status' => 'ok', 'data' => []]);
        }

        $sets = MasterSet::query()
            ->whereIn('id', $setIds)
            ->orderBy('name')
            ->get(['id', 'article_group_id', 'name', 'description']);

        $compAgg = DB::table('master_set_components')
            ->whereIn('master_set_id', $setIds)
            ->selectRaw("
                master_set_id,
                SUM(CASE WHEN parent_id IS NULL THEN 1 ELSE 0 END) as mainCount,
                SUM(CASE WHEN parent_id IS NOT NULL THEN 1 ELSE 0 END) as subCount,
                COALESCE(SUM(CASE WHEN parent_id IS NULL THEN COALESCE(unit_price,0)*COALESCE(qty,0) ELSE 0 END),0) as mainCost,
                COALESCE(SUM(CASE WHEN parent_id IS NOT NULL THEN COALESCE(unit_price,0)*COALESCE(qty,0) ELSE 0 END),0) as subCost
            ")
            ->groupBy('master_set_id')
            ->get()
            ->keyBy('master_set_id');

        $laborAgg = DB::table('master_set_labor')
            ->whereIn('master_set_id', $setIds)
            ->selectRaw("
                master_set_id,
                COUNT(*) as laborCount,
                COALESCE(SUM(COALESCE(hourly_rate,0)*COALESCE(hours,0)),0) as laborCost
            ")
            ->groupBy('master_set_id')
            ->get()
            ->keyBy('master_set_id');

        $taskAgg = DB::table('master_set_tasks')
            ->whereIn('master_set_id', $setIds)
            ->selectRaw("master_set_id, COUNT(*) as taskCount")
            ->groupBy('master_set_id')
            ->get()
            ->keyBy('master_set_id');

        $protAgg = DB::table('master_set_checklists')
            ->whereIn('master_set_id', $setIds)
            ->selectRaw("master_set_id, COUNT(*) as protocolCount")
            ->groupBy('master_set_id')
            ->get()
            ->keyBy('master_set_id');

        $payload = $sets->map(function ($s) use ($compAgg, $laborAgg, $taskAgg, $protAgg) {
            $c = $compAgg[$s->id] ?? null;
            $l = $laborAgg[$s->id] ?? null;
            $t = $taskAgg[$s->id] ?? null;
            $p = $protAgg[$s->id] ?? null;

            $mainCost = (float) ($c->mainCost ?? 0);
            $subCost = (float) ($c->subCost ?? 0);
            $laborCost = (float) ($l->laborCost ?? 0);
            $total = $mainCost + $subCost + $laborCost;

            return [
                'id' => $s->id,
                'article_group_id' => $s->article_group_id,
                'name' => $s->name,
                'description' => $s->description,
                'stats' => [
                    'mainCount' => (int) ($c->mainCount ?? 0),
                    'subCount' => (int) ($c->subCount ?? 0),
                    'laborCount' => (int) ($l->laborCount ?? 0),
                    'taskCount' => (int) ($t->taskCount ?? 0),
                    'protocolCount' => (int) ($p->protocolCount ?? 0),

                    'mainCost' => $mainCost,
                    'subCost' => $subCost,
                    'laborCost' => $laborCost,
                    'total' => $total,

                    'mainPct' => $total > 0 ? round(($mainCost / $total) * 100, 2) : 0,
                    'subPct' => $total > 0 ? round(($subCost / $total) * 100, 2) : 0,
                    'laborPct' => $total > 0 ? round(($laborCost / $total) * 100, 2) : 0,
                ],
            ];
        });

        return response()->json(['status' => 'ok', 'data' => $payload]);
    }

    // ============================================================
    // LEGACY "GROUP SETS" ROUTES -> map to MasterSetGroup
    // ============================================================

    // GET /admin/master-sets/group-sets?article_group_id=..&q=..
    public function groupSetsIndex(Request $request)
    {
        return $this->list($request);
    }

    // GET /admin/master-sets/group-sets/{groupSet}
    public function groupSetsShow(Request $request, MasterSetGroup $groupSet)
    {
        $setIds = DB::table('master_set_group_master_set')
            ->where('master_set_group_id', $groupSet->id)
            ->orderBy('master_set_id')
            ->pluck('master_set_id')
            ->all();

        return response()->json(['status' => 'ok', 'data' => [
            'id' => $groupSet->id,
            'article_group_id' => $groupSet->article_group_id,
            'name' => $groupSet->name,
            'description' => $groupSet->description,
            'color' => $groupSet->color,
            'set_ids' => $setIds,
        ]]);
    }

    // POST /admin/master-sets/group-sets
    public function groupSetsStore(Request $request)
    {
        $data = $request->validate([
            'article_group_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'set_ids' => ['nullable', 'array'],
            'set_ids.*' => ['integer'],
            'master_set_ids' => ['nullable', 'array'],
            'master_set_ids.*' => ['integer'],
        ]);

        $masterSetIds = $data['master_set_ids'] ?? $data['set_ids'] ?? [];
        $request->merge(['master_set_ids' => $masterSetIds]);

        return $this->store($request);
    }

    // PUT /admin/master-sets/group-sets/{groupSet}
    public function groupSetsUpdate(Request $request, MasterSetGroup $groupSet)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'set_ids' => ['nullable', 'array'],
            'set_ids.*' => ['integer'],
            'master_set_ids' => ['nullable', 'array'],
            'master_set_ids.*' => ['integer'],
        ]);

        $incoming = $data['master_set_ids'] ?? $data['set_ids'] ?? null;
        if ($incoming !== null) {
            $request->merge(['master_set_ids' => $incoming]);
        }

        return $this->update($request, $groupSet);
    }

    // DELETE /admin/master-sets/group-sets/{groupSet}
    public function groupSetsDestroy(MasterSetGroup $groupSet)
    {
        return $this->destroy($groupSet);
    }
}
