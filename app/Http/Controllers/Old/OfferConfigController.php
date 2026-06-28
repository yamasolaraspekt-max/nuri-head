<?php
namespace App\Http\Controllers\Customer\Offer;
use App\Http\Controllers\Controller;

use App\Models\Offer;
use App\Models\ProductMasterSet;
use App\Models\ProductWP;
use Illuminate\Http\Request;

class OfferConfigController extends Controller
{
    public function wp_config($offer_id) {
        $offer = Offer::with([
            'customer','alternative',
            'productGroup',     // article_groups via product_id
            'service','department',
            'createdBy','createdFor'
        ])->findOrFail($offer_id);

        // Minimal boot data for the blade (article_group id drives set search)
        $boot = [
            'offer' => [
                'id' => $offer->id,
                'article_group_id' => $offer->product_id,   // FK to article_groups
                'service_id' => $offer->service_id,
                'department_id' => $offer->department_id,
            ],
        ];

        return view('admin.offer.offer.configuration.wp.index', compact('offer','boot'));
    }

    // Suggest master sets for this offer + target kW (from Step 5)
    public function masterSets(Request $request, Offer $offer)
    {
        $kwTarget = (float) $request->query('kw', 0);           // recommended kW
        $phaseId  = $request->query('phase');                   // optional
        $groupId  = $offer->product_id;                         // article_groups.id

        $q = ProductMasterSet::withCount(['products','employees','assets'])
            ->with(['articleGroup'])
            ->where('article_group', $groupId)
            ->when($phaseId, fn($qq)=>$qq->where('phase_id',$phaseId))
            ->where(function($qq){ $qq->whereNull('status')->orWhere('status','Published'); })
            ->limit(50);

        $sets = $q->get();

        // Optional: score sets by how well they match kwTarget using their main product WP data
        $scored = $sets->map(function($set) use ($kwTarget) {
            $score = 0;
            if ($kwTarget > 0 && $set->product_parent_id) {
                $wp = ProductWP::where('product_id', $set->product_parent_id)
                      ->whereIn('type',['A-7/W35','A-7_W35','A-7'])  // be flexible
                      ->orderByDesc('max_kw')->first();
                if ($wp) {
                    $diff = abs(($wp->max_kw ?? 0) - $kwTarget);
                    $score = 100 - min(100, $diff * 10); // crude proximity score
                }
            }
            return [
                'id' => $set->id,
                'setname' => $set->setname,
                'article_group' => $set->article_group,
                'phase_id' => $set->phase_id,
                'product_parent_id' => $set->product_parent_id,
                'items' => [
                    'products' => $set->products_count,
                    'employees'=> $set->employees_count,
                    'assets'   => $set->assets_count,
                ],
                'defaults' => [
                    'material_price' => $set->material_price,
                    'employee_price' => $set->employee_price,
                    'asset_price'    => $set->asset_price,
                ],
                'score' => $score,
            ];
        })->sortByDesc('score')->values();

        return response()->json(['sets' => $scored]);
    }

    // Full breakdown of a master set (to auto-fill Step G and push items to Step 7)
    public function masterSetBreakdown(ProductMasterSet $set)
    {
        $set->load([
            'products.product',
            'subProducts.product',
            'employees.position',
            'assets.asset',
        ]);

        // compute EK totals (purchase side). Use stored totals if present, else compute.
        $matEK = 0;
        foreach ($set->products as $p)     { $matEK += (float)($p->purchase_price ?? 0) * (int)($p->product_count ?? 1); }
        foreach ($set->subProducts as $sp) { $matEK += (float)($sp->purchase_price ?? 0) * (int)($sp->product_count ?? 1); }

        $lohnEK = 0;
        $empRows = [];
        foreach ($set->employees as $e) {
            $lohnEK += (float)($e->buying_price ?? 0) * max(1,(int)($e->work_hour ?? 1));
            $empRows[] = [
                'position' => optional($e->position)->name ?? 'Mitarbeiter',
                'hours' => (int)($e->work_hour ?? 0),
                'rate_buy' => (float)($e->buying_price ?? 0),
                'rate_sale'=> (float)($e->sale_price ?? 0),
                'line_buy' => (float)($e->buying_price ?? 0) * max(1,(int)($e->work_hour ?? 1)),
            ];
        }

        $toolsEK = 0;
        $toolRows = [];
        foreach ($set->assets as $a) {
            $row = [
                'name' => $a->name ?? optional($a->asset)->item ?? 'Werkzeug',
                'count'=> (int)($a->count ?? 1),
                'total_price' => (float)($a->total_price ?? 0)
            ];
            $toolRows[] = $row;
            $toolsEK += $row['total_price'];
        }

        // allow using set stored defaults as fallback
        $matEK = $matEK ?: (float)($set->material_price ?? 0);
        $lohnEK = $lohnEK ?: (float)($set->employee_price ?? 0);
        $toolsEK = $toolsEK ?: (float)($set->asset_price ?? 0);

        return response()->json([
            'set' => [
                'id' => $set->id,
                'setname' => $set->setname,
            ],
            'totals' => [
                'material_ek' => round($matEK,2),
                'lohn_ek'     => round($lohnEK,2),
                'tools_ek'    => round($toolsEK,2),
            ],
            'lines' => [
                'products'   => $set->products->map(fn($p)=>[
                    'sku'   => $p->product?->article_no,
                    'name'  => $p->product?->product,
                    'qty'   => (int)$p->product_count,
                    'unit_purchase' => (float)($p->purchase_price ?? 0),
                    'unit_retail'   => (float)($p->retail_price ?? 0),
                ])->values(),
                'sub_products'=> $set->subProducts->map(fn($p)=>[
                    'sku'   => $p->product?->article_no,
                    'name'  => $p->product?->product,
                    'qty'   => (int)$p->product_count,
                    'unit_purchase' => (float)($p->purchase_price ?? 0),
                    'unit_retail'   => (float)($p->retail_price ?? 0),
                ])->values(),
                'employees'  => $empRows,
                'tools'      => $toolRows,
            ]
        ]);
    }
}
