<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CostingSet;
use App\Models\CostingSetRole;
use App\Models\PositionQualification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostingSetController extends Controller
{
    public function __construct() {
        // MASTER-01 P1-IDOR Finance: Rollen-Gate (permission:Finance)
        $this->middleware('permission:Finance,update')->only(['update', 'makeDefault', 'rolesApplyDefaults', 'rolesSyncFromQualifications', 'rolesBulkUpdate']);
        $this->middleware('permission:Finance,delete')->only(['destroy']); $this->middleware('auth'); }

    /**
     * We embed tab into your existing Position page.
     * So this endpoint is optional if you want a dedicated page.
     */
    public function index()
    {
        return view('admin.settings.costing_sets.index');
    }

    // ----------------------------
    // TAB: SETS (AJAX list)
    // ----------------------------
    public function list(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $rows = CostingSet::query()
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $html = view('admin.employee.position.partials._sets_table', [
            'data' => $rows
        ])->render();

        return response()->json(['ok' => true, 'html' => $html]);
    }

   public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],

            'aw_minutes' => ['required','numeric','min:1','max:60'],

            'material_overhead_percent' => ['nullable','numeric','min:0','max:500'],
            'labor_overhead_percent'    => ['nullable','numeric','min:0','max:500'],

            // accept BOTH names (frontend might send either)
            'site_overhead_percent'     => ['nullable','numeric','min:0','max:500'],
            'site_overhead_fixed'       => ['nullable','numeric','min:0'],
            'project_overhead_percent'  => ['nullable','numeric','min:0','max:500'],
            'project_overhead_fixed'    => ['nullable','numeric','min:0'],

            'risk_percent'   => ['nullable','numeric','min:0','max:500'],
            'profit_percent' => ['nullable','numeric','min:0','max:500'],

            // Purchasing rules
            'small_parts_percent' => ['nullable','numeric','min:0','max:500'],
            'freight_fixed'       => ['nullable','numeric','min:0'],
            'handling_fixed'      => ['nullable','numeric','min:0'],
            'disposal_fixed'      => ['nullable','numeric','min:0'],

            // Defaults for matrix
            'default_payroll_overhead_percent' => ['nullable','numeric','min:0','max:500'],
            'default_company_overhead_percent' => ['nullable','numeric','min:0','max:500'],
            'default_sell_markup_percent'      => ['nullable','numeric','min:0','max:500'],

            'commission_mode'  => ['required','in:revenue_percent,fixed,db_percent'],
            'commission_value' => ['nullable','numeric','min:0','max:500'],

            // keep exactly what your controller expects
            'rounding_mode' => ['required','in:none,0.05,0.10,1.00'],

            'is_active'  => ['nullable','boolean'],
            'is_default' => ['nullable','boolean'],
        ]);

        return DB::transaction(function () use ($data) {

            if (!empty($data['is_default'])) {
                CostingSet::query()->update(['is_default' => 0]);
            }

            // map project_* -> site_* (or prefer explicit site_* if provided)
            $sitePct = $data['site_overhead_percent']
                ?? $data['project_overhead_percent']
                ?? 0;

            $siteFix = $data['site_overhead_fixed']
                ?? $data['project_overhead_fixed']
                ?? 0;

            $set = CostingSet::create([
                'name' => $data['name'],

                'is_active'  => (int)($data['is_active'] ?? 1),
                'is_default' => (int)($data['is_default'] ?? 0),

                'aw_minutes' => (float)$data['aw_minutes'],

                'material_overhead_percent' => (float)($data['material_overhead_percent'] ?? 0),
                'labor_overhead_percent'    => (float)($data['labor_overhead_percent'] ?? 0),

                'site_overhead_percent' => (float)$sitePct,
                'site_overhead_fixed'   => (float)$siteFix,

                'risk_percent'   => (float)($data['risk_percent'] ?? 0),
                'profit_percent' => (float)($data['profit_percent'] ?? 0),

                'small_parts_percent' => (float)($data['small_parts_percent'] ?? 0),
                'freight_fixed'       => (float)($data['freight_fixed'] ?? 0),
                'handling_fixed'      => (float)($data['handling_fixed'] ?? 0),
                'disposal_fixed'      => (float)($data['disposal_fixed'] ?? 0),

                'default_payroll_overhead_percent' => (float)($data['default_payroll_overhead_percent'] ?? 0),
                'default_company_overhead_percent' => (float)($data['default_company_overhead_percent'] ?? 0),
                'default_sell_markup_percent'      => (float)($data['default_sell_markup_percent'] ?? 0),

                'commission_mode'  => $data['commission_mode'],
                'commission_value' => (float)($data['commission_value'] ?? 0),

                'rounding_mode' => $data['rounding_mode'],
            ]);

            $this->syncRoles($set->id);

            return response()->json(['ok' => true, 'id' => $set->id]);
        });
    }


   public function update(Request $request, CostingSet $set)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],

            'aw_minutes' => ['required','numeric','min:1','max:60'],

            'material_overhead_percent' => ['nullable','numeric','min:0','max:500'],
            'labor_overhead_percent'    => ['nullable','numeric','min:0','max:500'],

            'site_overhead_percent'     => ['nullable','numeric','min:0','max:500'],
            'site_overhead_fixed'       => ['nullable','numeric','min:0'],
            'project_overhead_percent'  => ['nullable','numeric','min:0','max:500'],
            'project_overhead_fixed'    => ['nullable','numeric','min:0'],

            'risk_percent'   => ['nullable','numeric','min:0','max:500'],
            'profit_percent' => ['nullable','numeric','min:0','max:500'],

            'small_parts_percent' => ['nullable','numeric','min:0','max:500'],
            'freight_fixed'       => ['nullable','numeric','min:0'],
            'handling_fixed'      => ['nullable','numeric','min:0'],
            'disposal_fixed'      => ['nullable','numeric','min:0'],

            'default_payroll_overhead_percent' => ['nullable','numeric','min:0','max:500'],
            'default_company_overhead_percent' => ['nullable','numeric','min:0','max:500'],
            'default_sell_markup_percent'      => ['nullable','numeric','min:0','max:500'],

            'commission_mode'  => ['required','in:revenue_percent,fixed,db_percent'],
            'commission_value' => ['nullable','numeric','min:0','max:500'],

            'rounding_mode' => ['required','in:none,0.05,0.10,1.00'],

            'is_active'  => ['nullable','boolean'],
            'is_default' => ['nullable','boolean'],
        ]);

        return DB::transaction(function () use ($data, $set) {

            if (!empty($data['is_default'])) {
                CostingSet::query()->where('id','!=',$set->id)->update(['is_default' => 0]);
            }

            $sitePct = $data['site_overhead_percent']
                ?? $data['project_overhead_percent']
                ?? $set->site_overhead_percent
                ?? 0;

            $siteFix = $data['site_overhead_fixed']
                ?? $data['project_overhead_fixed']
                ?? $set->site_overhead_fixed
                ?? 0;

            $set->fill([
                'name' => $data['name'],

                'is_active'  => (int)($data['is_active'] ?? 1),
                'is_default' => (int)($data['is_default'] ?? 0),

                'aw_minutes' => (float)$data['aw_minutes'],

                'material_overhead_percent' => (float)($data['material_overhead_percent'] ?? 0),
                'labor_overhead_percent'    => (float)($data['labor_overhead_percent'] ?? 0),

                'site_overhead_percent' => (float)$sitePct,
                'site_overhead_fixed'   => (float)$siteFix,

                'risk_percent'   => (float)($data['risk_percent'] ?? 0),
                'profit_percent' => (float)($data['profit_percent'] ?? 0),

                'small_parts_percent' => (float)($data['small_parts_percent'] ?? 0),
                'freight_fixed'       => (float)($data['freight_fixed'] ?? 0),
                'handling_fixed'      => (float)($data['handling_fixed'] ?? 0),
                'disposal_fixed'      => (float)($data['disposal_fixed'] ?? 0),

                'default_payroll_overhead_percent' => (float)($data['default_payroll_overhead_percent'] ?? 0),
                'default_company_overhead_percent' => (float)($data['default_company_overhead_percent'] ?? 0),
                'default_sell_markup_percent'      => (float)($data['default_sell_markup_percent'] ?? 0),

                'commission_mode'  => $data['commission_mode'],
                'commission_value' => (float)($data['commission_value'] ?? 0),

                'rounding_mode' => $data['rounding_mode'],
            ])->save();

            return response()->json(['ok' => true]);
        });
    }
    public function destroy(CostingSet $set)
    {
        // protect default set
        if ((int)$set->is_default === 1) {
            return response()->json(['ok'=>false,'message'=>'Default Costing Set kann nicht gelöscht werden.'], 422);
        }

        DB::transaction(function () use ($set) {
            CostingSetRole::where('costing_set_id', $set->id)->delete();
            $set->delete();
        });

        return response()->json(['ok' => true]);
    }

    public function makeDefault(CostingSet $set)
    {
        DB::transaction(function () use ($set) {
            CostingSet::query()->update(['is_default' => 0]);
            $set->is_default = 1;
            $set->is_active = 1;
            $set->save();
        });

        return response()->json(['ok' => true]);
    }

    // ----------------------------
    // TAB: ROLES MATRIX
    // ----------------------------
    public function roles(Request $request, CostingSet $set)
    {
        $this->syncRoles($set->id);

        // ✅ ensure values are not zero when opening
        $this->rolesApplyDefaults($set);

        $roles = CostingSetRole::query()
            ->where('costing_set_id', $set->id)
            ->with(['qualification:id,name,default_price'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $html = view('admin.employee.position.partials._roles_matrix', [
            'set' => $set,
            'roles' => $roles,
        ])->render();

        return response()->json(['ok'=>true,'html'=>$html]);
    }

public function rolesApplyDefaults(CostingSet $set)
{
    $this->syncRoles($set->id);

    DB::transaction(function () use ($set) {
        $roles = CostingSetRole::where('costing_set_id', $set->id)->get();

        foreach ($roles as $r) {
            // Always take wage from qualification table if role is empty
            if ((float)$r->wage_cost_per_hour <= 0) {
                $qualWage = (float) optional($r->qualification)->default_price;
                if ($qualWage > 0) $r->wage_cost_per_hour = $qualWage;
            }

            $wage = (float) $r->wage_cost_per_hour;

            $r->payroll_overhead_percent = (float) ($set->default_payroll_overhead_percent ?? 0);
            $r->company_overhead_percent = (float) ($set->default_company_overhead_percent ?? 0);

            $full = $wage * (1 + ($r->payroll_overhead_percent / 100) + ($r->company_overhead_percent / 100));
            $r->full_cost_rate_per_hour = $full;

            $markup = (float) ($set->default_sell_markup_percent ?? 0);
            $r->sell_rate_per_hour = $markup > 0 ? ($full * (1 + $markup/100)) : $full;

            $r->save();
        }
    });

    return response()->json(['ok'=>true]);
}

public function options()
    {
        $rows = CostingSet::query()
            ->where('is_active', 1)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id','name','aw_minutes','is_default']);

        return response()->json([
            'status' => 'ok',
            'data' => $rows->map(fn($x) => [
                'id' => (int) $x->id,
                'name' => (string) $x->name,
                'aw_minutes' => (int) ($x->aw_minutes ?? 6),
                'is_default' => (bool) ($x->is_default ?? false),
            ])->values(),
        ]);
    }

    public function show(CostingSet $costingSet)
{
    abort_unless($costingSet->is_active, 404);

    return response()->json([
        'status' => 'ok',
        'data' => [
            'id' => (int) $costingSet->id,
            'name' => (string) $costingSet->name,
            'status' => (string) $costingSet->status,

            'aw_minutes' => (int) ($costingSet->aw_minutes ?? 6),

            'material_overhead_percent' => (float) ($costingSet->material_overhead_percent ?? 0),
            'labor_overhead_percent'    => (float) ($costingSet->labor_overhead_percent ?? 0),
            'site_overhead_percent'     => (float) ($costingSet->site_overhead_percent ?? 0),
            'site_overhead_fixed'       => (float) ($costingSet->site_overhead_fixed ?? 0),

            'risk_percent'   => (float) ($costingSet->risk_percent ?? 0),
            'profit_percent' => (float) ($costingSet->profit_percent ?? 0),

            'small_parts_percent' => (float) ($costingSet->small_parts_percent ?? 0),
            'freight_fixed'       => (float) ($costingSet->freight_fixed ?? 0),
            'handling_fixed'      => (float) ($costingSet->handling_fixed ?? 0),
            'disposal_fixed'      => (float) ($costingSet->disposal_fixed ?? 0),

            'rounding_step' => (float) ($costingSet->rounding_step ?? 0.05),
            'rounding_mode' => (string) ($costingSet->rounding_mode ?? 'nearest'),

            'commission_mode'  => (string) ($costingSet->commission_mode ?? 'revenue_percent'),
            'commission_value' => (float) ($costingSet->commission_value ?? 0),
        ],
    ]);
}

    
    public function rolesSyncFromQualifications(CostingSet $set)
    {
        $this->syncRoles($set->id);
        return response()->json(['ok'=>true]);
    }

    public function rolesBulkUpdate(Request $request, CostingSet $set)
    {
        $data = $request->validate([
            'rows' => ['required','array'],
            'rows.*.id' => ['required','integer','exists:costing_set_roles,id'],
            'rows.*.wage_cost_per_hour' => ['nullable','numeric','min:0','max:9999'],
            'rows.*.payroll_overhead_percent' => ['nullable','numeric','min:0','max:500'],
            'rows.*.company_overhead_percent' => ['nullable','numeric','min:0','max:500'],
            'rows.*.full_cost_rate_per_hour' => ['nullable','numeric','min:0','max:9999'],
            'rows.*.sell_rate_per_hour' => ['nullable','numeric','min:0','max:9999'],
        ]);

        DB::transaction(function () use ($data, $set) {
            foreach ($data['rows'] as $r) {
                $row = CostingSetRole::where('id', $r['id'])
                    ->where('costing_set_id', $set->id)
                    ->firstOrFail();

                $wage = (float)($r['wage_cost_per_hour'] ?? 0);
                $po   = (float)($r['payroll_overhead_percent'] ?? 0);
                $co   = (float)($r['company_overhead_percent'] ?? 0);

                // if full_cost_rate_per_hour not provided, compute
                $full = array_key_exists('full_cost_rate_per_hour', $r) && $r['full_cost_rate_per_hour'] !== null
                    ? (float)$r['full_cost_rate_per_hour']
                    : ($wage * (1 + ($po/100) + ($co/100)));

                $row->fill([
                    'wage_cost_per_hour' => $wage,
                    'payroll_overhead_percent' => $po,
                    'company_overhead_percent' => $co,
                    'full_cost_rate_per_hour' => $full,
                    'sell_rate_per_hour' => (float)($r['sell_rate_per_hour'] ?? 0),
                ])->save();
            }
        });

        return response()->json(['ok'=>true]);
    }

    // ----------------------------
    // Internal helper
    // ----------------------------
 private function syncRoles(int $costingSetId): void
{
    $set = CostingSet::findOrFail($costingSetId);

    $quals = PositionQualification::query()
        ->orderBy('sort_order')->orderBy('id')
        ->get(['id','default_price']);

    foreach ($quals as $i => $q) {
        $wage = (float) ($q->default_price ?? 0);
        $nk   = (float) ($set->default_payroll_overhead_percent ?? 0);
        $gk   = (float) ($set->default_company_overhead_percent ?? 0);

        $full = $wage * (1 + ($nk / 100) + ($gk / 100));

        $markup = (float) ($set->default_sell_markup_percent ?? 0);
        $vk = $markup > 0 ? ($full * (1 + ($markup / 100))) : $full;

        CostingSetRole::firstOrCreate(
            ['costing_set_id' => $costingSetId, 'qualification_id' => $q->id],
            [
                'wage_cost_per_hour' => $wage,
                'payroll_overhead_percent' => $nk,
                'company_overhead_percent' => $gk,
                'full_cost_rate_per_hour' => $full,
                'sell_rate_per_hour' => $vk,
                'sort_order' => $i + 1,
                'status' => 'active',
            ]
        );
    }
}
}