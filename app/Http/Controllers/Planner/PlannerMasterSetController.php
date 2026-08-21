<?php

namespace App\Http\Controllers\Planner;
use App\Http\Controllers\Controller;


use App\Models\MasterSet;
use App\Support\Planner\PlannerZustaendigkeit;
use App\Models\PlannerItem;
use App\Models\PlannerPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\PlannerItemMasterSet;  
class PlannerMasterSetController extends Controller
{
    use PlannerZustaendigkeit;

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $rows = MasterSet::query()
            ->when($q, function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name', 'description']);

        return response()->json(['ok' => true, 'items' => $rows]);
    }
    public function search(Request $request)
    {
        $data = $request->validate([
            'q'          => ['nullable','string','max:200'],
            'product_id' => ['nullable','integer','min:1'], // your article_group_id target
            'plan_id'    => ['nullable','integer','min:1'],
            'item_id'    => ['nullable','integer','min:1'],
            'limit'      => ['nullable','integer','min:1','max:200'],
        ]);

        $q       = trim((string)($data['q'] ?? ''));
        $product = (int)($data['product_id'] ?? 0);
        $planId  = (int)($data['plan_id'] ?? 0);
        $itemId  = (int)($data['item_id'] ?? 0);
        $limit   = (int)($data['limit'] ?? 50);

        $base = MasterSet::query()
            ->whereNull('deleted_at')
            ->when($product > 0, fn($x) => $x->where('article_group_id', $product))
            ->when($q !== '', function ($x) use ($q) {
                $like = '%'.mb_strtolower($q).'%';
                $x->where(function($w) use ($like) {
                    $w->whereRaw('LOWER(COALESCE(name,"")) LIKE ?', [$like])
                      ->orWhereRaw('LOWER(COALESCE(description,"")) LIKE ?', [$like]);
                });
            })
            ->orderByDesc('id');

        $rows = $base->limit($limit)->get(['id','name','description','article_group_id']);

        // linked flags
        $linkedSetIds = [];
        if ($itemId > 0 && DB::table('planner_item_master_sets')->where('planner_item_id', $itemId)->exists()) {
            $linkedSetIds = DB::table('planner_item_master_sets')
                ->where('planner_item_id', $itemId)
                ->pluck('master_set_id')
                ->map(fn($v)=>(int)$v)
                ->toArray();
        }

        $addedSetIds = [];
        if ($planId > 0) {
            $addedSetIds = DB::table('planner_items')
                ->where('plan_id', $planId)
                ->whereNull('deleted_at')
                ->where('source_type', 'master_set')
                ->pluck('source_id')
                ->map(fn($v)=>(int)$v)
                ->toArray();
        }

        // light aggregates (counts + labor hours) without heavy eager loads
        $ids = $rows->pluck('id')->all();

        $compCounts = [];
        if (!empty($ids)) {
            $compCounts = DB::table('master_set_components')
                ->select('master_set_id', DB::raw('COUNT(*) as c'))
                ->whereIn('master_set_id', $ids)
                ->groupBy('master_set_id')
                ->pluck('c','master_set_id')
                ->toArray();
        }

        $laborHours = [];
        if (!empty($ids)) {
            $laborHours = DB::table('master_set_labor')
                ->select('master_set_id', DB::raw('COALESCE(SUM(hours),0) as h'))
                ->whereIn('master_set_id', $ids)
                ->groupBy('master_set_id')
                ->pluck('h','master_set_id')
                ->toArray();
        }

        $out = $rows->map(function($s) use ($linkedSetIds, $addedSetIds, $compCounts, $laborHours) {
            $id = (int)$s->id;
            return [
                'id'               => $id,
                'name'             => $s->name ?? ('Master Set #'.$id),
                'description'      => $s->description,
                'components_count' => (int)($compCounts[$id] ?? 0),
                'labor_hours'      => (float)($laborHours[$id] ?? 0),
                'is_linked'        => in_array($id, $linkedSetIds, true),
                'is_added_to_plan' => in_array($id, $addedSetIds, true),
            ];
        })->values();

        return response()->json(['ok' => true, 'data' => $out]);
    }

    public function show(MasterSet $masterSet)
    {
        $masterSet->load([
            'components' => function ($q) {
                $q->orderBy('sort_order')->orderBy('id')
                ->with([
                    // ✅ product name field is "product"
                    'product:id,product',
                ]);
            },

            'labor' => function ($q) {
                $q->orderBy('sort_order')->orderBy('id')
                ->with([
                    // ✅ positions.position (NOT name)
                    'position:id,position',

                    // ✅ departments.department_name
                    'department:id,department_name',

                    // ✅ employees full name parts
                    'employee:id,title,name,midname,lastname',
                ]);
            },

            // optional (if you want it in response too)
            'responsibleDepartmentPosition' => function ($q) {
                $q->with([
                    'position:id,position',
                    'department:id,department_name',
                    'employee:id,title,name,midname,lastname',
                ]);
            },
        ]);

        return response()->json(['ok' => true, 'data' => $masterSet]);
    }


    public function linked(Request $request, int $plannerItemId)
    {
        $rows = \App\Models\PlannerItemMasterSet::query()
            ->where('planner_item_id', $plannerItemId)
            ->join('master_sets', 'master_sets.id', '=', 'planner_item_master_sets.master_set_id')
            ->orderBy('master_sets.name')
            ->get([
                'master_sets.id',
                'master_sets.name',
                'master_sets.description',
            ]);

        return response()->json(['ok' => true, 'items' => $rows]);
    }


    public function link(PlannerItem $item, MasterSet $masterSet)
    {
        // Z2-W0-5 / A-3: Route-Model-Binding ohne Scope — das Modell wird geladen, aber
        // niemand fragt, ob es mich etwas angeht.
        $this->verlangeZustaendigkeitFuerItem((int) $item->id);

        DB::table('planner_item_master_sets')->updateOrInsert(
            ['planner_item_id' => $item->id, 'master_set_id' => $masterSet->id],
            ['updated_at' => now(), 'created_at' => now()]
        );

        return response()->json(['ok' => true]);
    }

    public function unlink(PlannerItem $item, MasterSet $masterSet)
    {
        // Z2-W0-5 / A-3: loeschender Pfad, dieselbe Bindung.
        $this->verlangeZustaendigkeitFuerItem((int) $item->id);

        DB::table('planner_item_master_sets')
            ->where('planner_item_id', $item->id)
            ->where('master_set_id', $masterSet->id)
            ->delete();

        return response()->json(['ok' => true]);
    }

    public function addToPlan(Request $request, PlannerPlan $plan, MasterSet $masterSet)
    {
        // Z2-W0-5 / A-3, der schwerste der drei: hier entstehen Auftragspunkte in einem
        // fremden Plan. Bindung an den Plan, nicht an den Punkt — der Punkt entsteht erst.
        $this->verlangeZustaendigkeitFuerPlan((int) $plan->id);

        // reuse your existing approach (soft delete restore safe)
        $item = PlannerItem::withTrashed()
            ->where('plan_id', $plan->id)
            ->where('source_type', 'master_set')
            ->where('source_id', $masterSet->id)
            ->first();

        if ($item) {
            if ($item->trashed()) $item->restore();

            $item->update([
                'title'       => $masterSet->name ?? ('Master Set #'.$masterSet->id),
                'description' => $masterSet->description,
            ]);

            return response()->json(['ok' => true, 'item_id' => $item->id, 'restored' => true]);
        }

        $new = PlannerItem::create([
            'plan_id'          => $plan->id,
            'client_uid'       => (string) Str::uuid(),
            'source_type'      => 'master_set',
            'source_id'        => $masterSet->id,
            'title'            => $masterSet->name ?? ('Master Set #'.$masterSet->id),
            'description'      => $masterSet->description,
            'duration_minutes' => 60,
            'status'           => 'open',
            'sort_order'       => 9999,
        ]);

        return response()->json(['ok' => true, 'item_id' => $new->id, 'created' => true]);
    }
}
