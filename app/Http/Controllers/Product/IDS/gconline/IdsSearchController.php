<?php

namespace App\Http\Controllers\Product\IDS\gconline;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IdsSearchController extends Controller
{
    // ONLY search UI must require login
    public function __construct()
    {
        $this->middleware('auth')->except('back');
    }


    // User search form
    public function form()
    {
        return view('ids.search_form');
    }

    public function requestPriceForMaterial(Request $request, $folder)
{
    $folder = \App\Models\OfferFolder::with([
        'offer.customer',
        'offer.product',
        'detail',
    ])->findOrFail($folder);

    $items = collect($request->input('items', []))
        ->map(function ($item) {
            return [
                'product_id'             => $item['product_id'] ?? null,
                'component_id'           => $item['component_id'] ?? null,
                'article_no'             => $item['article_no'] ?? null,
                'distributor_article_no' => $item['distributor_article_no'] ?? null,
                'name'                   => $item['name'] ?? null,
                'qty'                    => (float) ($item['qty'] ?? 0),
                'unit'                   => $item['unit'] ?? null,
            ];
        })
        ->filter(fn ($item) => filled($item['name']) || filled($item['article_no']) || filled($item['distributor_article_no']))
        ->values();

    /*
     |--------------------------------------------------------------------------
     | Fallback: if no selected items are passed from the Materialliste tab,
     | collect material rows from offer detail sections.
     |--------------------------------------------------------------------------
     */
    if ($items->isEmpty()) {
        $sections = is_array($folder->detail?->sections)
            ? $folder->detail->sections
            : [];

        $items = collect($this->extractMaterialItemsForIds($sections));
    }

    return view('ids.material-request', [
        'folder' => $folder,
        'offer'  => $folder->offer,
        'detail' => $folder->detail,
        'items'  => $items,
    ]);
}


private function extractMaterialItemsForIds(array $sections): array
{
    $rows = [];

    foreach ($sections as $section) {
        $sectionQty = (float) data_get($section, 'config.qty', 1);
        $sectionUnit = strtolower((string) data_get($section, 'config.unit', ''));
        $sectionMultiplier = $sectionUnit === 'set' ? $sectionQty : 1;

        foreach ((array) data_get($section, 'items', []) as $item) {
            $this->pushIdsMaterialRow($rows, $item, $sectionMultiplier);

            foreach ((array) data_get($item, 'subItems', []) as $subItem) {
                $parentQty = (float) data_get($item, 'qty', 1);
                $this->pushIdsMaterialRow($rows, $subItem, max($parentQty, 1) * $sectionMultiplier);

                foreach ((array) data_get($subItem, 'subItems', []) as $childItem) {
                    $subQty = (float) data_get($subItem, 'qty', 1);
                    $this->pushIdsMaterialRow($rows, $childItem, max($parentQty, 1) * max($subQty, 1) * $sectionMultiplier);
                }
            }
        }
    }

    return $rows;
}

private function pushIdsMaterialRow(array &$rows, array $item, float $multiplier = 1): void
{
    if (($item['kind'] ?? null) === 'labor') {
        return;
    }

    $itemType = strtolower((string) ($item['item_type'] ?? ''));
    $hasChildren = !empty($item['subItems']);

    // Skip master sets / parent containers
    if ($itemType === 'master_set' || $hasChildren) {
        return;
    }

    $qty = (float) ($item['qty'] ?? 0);
    $totalQty = $qty * $multiplier;

    $name = $item['name']
        ?? $item['title']
        ?? null;

    $articleNo = $item['article_no']
        ?? null;

    $distributorArticleNo = $item['distributor_article_no']
        ?? null;

    if (!filled($name) && !filled($articleNo) && !filled($distributorArticleNo)) {
        return;
    }

    $rows[] = [
        'product_id'             => $item['product_id'] ?? $item['productId'] ?? data_get($item, 'product.id'),
        'component_id'           => $item['component_id'] ?? null,
        'article_no'             => $articleNo,
        'distributor_article_no' => $distributorArticleNo,
        'name'                   => $name,
        'qty'                    => $totalQty,
        'unit'                   => $item['unit'] ?? $item['measure'] ?? null,
    ];
}

    // Forward user to GC Online with auto-posting form
    public function forwardToShop(Request $request)
    {
        $query = trim($request->input('query', ''));

        if ($query === '') {
            return back()->withErrors(['query' => 'Bitte einen Suchbegriff eingeben.']);
        }

        $shopUrl = config('services.ids.shop_url');

        $credentials = [
            'kndnr'      => config('services.ids.kndnr'),
            'name_kunde' => config('services.ids.username'),
            'pw_kunde'   => config('services.ids.password'),
        ];

        // XML callback → server
        $callbackUrl = route('ids.callback');

        // Browser return → user
        $returnUrl = route('ids.back');

        return response()->view('ids.forward_to_shop', [
            'shopUrl'     => $shopUrl,
            'credentials' => $credentials,
            'callbackUrl' => $callbackUrl,
            'returnUrl'   => $returnUrl,
            'searchterm'  => $query,
        ]);
    }


    // Browser returns here after GC Online basket selection
    public function back()
    {
        return redirect()->route('ids.search.form');
    }

     public function forms()
    {
        return view('ids.search_form_inline'); // new blade below
    }

    public function inlineBack()
    {
        // Very small view that runs inside the iframe and tells parent “done”
        return view('ids.inline_back');
    }
}
