<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\Brand;
use App\Models\Distributor;
use App\Models\DistributorPrice;
use App\Models\Product;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;

class ProductDifferenceController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $brandId = $request->query('brand_id');
        $articleGroupId = $request->query('article_group_id');
        $distributorId = $request->query('distributor_id');

        $query = Product::query()
            ->with([
                'brand',
                'articleGroup',
                'measureUnit',
                'firstImage',
            ])
            ->withCount('distributorPrices')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('product', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('article_no', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('ean', 'like', "%{$search}%");
                });
            })
            ->when($brandId, fn($q) => $q->where('brand_id', $brandId))
            ->when($articleGroupId, fn($q) => $q->where('article_group', $articleGroupId));

        if ($distributorId) {
            $query->whereHas('distributorPrices', function ($q) use ($distributorId) {
                $q->where('distributor_id', $distributorId);
            });
        }

        $products = $query
            ->orderBy('product')
            ->paginate(25)
            ->withQueryString();

        $brands = Brand::query()
            ->orderBy('name')
            ->get();

        $articleGroups = ArticleGroup::query()
            ->orderBy('article_group')
            ->get();

        $distributors = Distributor::query()
            ->orderByRaw('COALESCE(NULLIF(name, ""), short_name) ASC')
            ->get();

        return view('admin.product.product.difference', compact(
            'products',
            'brands',
            'articleGroups',
            'distributors',
            'search',
            'brandId',
            'articleGroupId',
            'distributorId'
        ));
    }

    public function compare(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'distributor_id' => ['nullable', 'integer', 'exists:distributors,id'],
            'price_field' => ['nullable', 'string', 'in:price,purchase_price'],
        ]);

        $productIds = collect($validated['product_ids'])
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $distributorId = $validated['distributor_id'] ?? null;
        $priceField = $validated['price_field'] ?? 'purchase_price';

        $products = Product::query()
            ->with([
                'brand',
                'articleGroup',
                'measureUnit',
                'firstImage',
            ])
            ->whereIn('id', $productIds)
            ->orderBy('product')
            ->get();

        if ($products->isEmpty()) {
            return response()->json($this->emptyCompareResponse($priceField));
        }

        $pricesQuery = DistributorPrice::query()
            ->with('distributor')
            ->whereIn('product_id', $productIds)
            ->where(function ($q) use ($priceField) {
                $q->whereNotNull($priceField)
                    ->orWhereNotNull('price')
                    ->orWhereNotNull('purchase_price');
            });

        if ($distributorId) {
            $pricesQuery->where('distributor_id', $distributorId);
        }

        $prices = $pricesQuery
            ->orderByRaw('price_date IS NULL ASC')
            ->orderByDesc('price_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn($price) => $price->distributor_id . '_' . $price->product_id)
            ->map(fn($group) => $group->first());

        $distributors = Distributor::query()
            ->whereHas('prices', function ($q) use ($productIds, $distributorId) {
                $q->whereIn('product_id', $productIds);

                if ($distributorId) {
                    $q->where('distributor_id', $distributorId);
                }
            })
            ->orderByRaw('COALESCE(NULLIF(name, ""), short_name) ASC')
            ->get();

        $productPayload = $products->map(function (Product $product) {
            return $this->productPayload($product);
        })->values();

        $grid = [];
        $allPriceValues = [];
        $cheapest = null;
        $highest = null;

        foreach ($distributors as $distributor) {
            $rowPrices = [];
            $rowProducts = [];

            foreach ($products as $product) {
                $key = $distributor->id . '_' . $product->id;
                $price = $prices->get($key);

                $selectedPrice = $this->resolvePriceValue($price, $priceField);

                if ($selectedPrice !== null) {
                    $rowPrices[] = $selectedPrice;

                    $info = [
                        'distributor_id' => $distributor->id,
                        'distributor' => $this->distributorName($distributor),
                        'product_id' => $product->id,
                        'product' => $product->product,
                        'image_url' => $this->productImageUrl($product),
                        'price' => $selectedPrice,
                        'field' => $priceField,
                    ];

                    $allPriceValues[] = $info;

                    if (!$cheapest || $selectedPrice < $cheapest['price']) {
                        $cheapest = $info;
                    }

                    if (!$highest || $selectedPrice > $highest['price']) {
                        $highest = $info;
                    }
                }

                $rowProducts[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->product,
                    'image_url' => $this->productImageUrl($product),
                    'product_url' => url('product_details/' . $product->id),

                    // products.article_no = Hersteller-Artikelnummer
                    'manufacturer_article_no' => $product->article_no,

                    // distributor_prices.article_no = Lieferanten-Artikelnummer
                    'supplier_article_no' => $price?->article_no,

                    'price' => $this->decimalOrNull($price?->price),
                    'purchase_price' => $this->decimalOrNull($price?->purchase_price),
                    'selected_price' => $selectedPrice,
                    'discount_price' => $this->decimalOrNull($price?->discount_price),
                    'discount_percent' => $this->decimalOrNull($price?->discount_percent),
                    'availability' => $price?->availability,
                    'price_date' => $this->formatDate($price?->price_date),
                    'status' => $price?->status,
                    'notice' => $price?->notice,
                ];
            }

            $grid[] = [
                'distributor' => [
                    'id' => $distributor->id,
                    'name' => $this->distributorName($distributor),
                    'short_name' => $distributor->short_name,
                ],
                'products' => $rowProducts,
                'price_diff' => $this->calculateDifference($rowPrices),
            ];
        }

        $summary = $this->buildSummary($allPriceValues, $cheapest, $highest);

        return response()->json([
            'products' => $productPayload,
            'grid' => $grid,
            'summary' => $summary,
            'charts' => [
                'bar' => $this->buildBarChart($grid, $priceField),
                'pie' => $this->buildPieChart($allPriceValues),
                'spread' => $this->buildSpreadChart($grid),
            ],
            'price_field' => $priceField,
        ]);
    }

    private function productPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->product,
            'model' => $product->model,
            'article_no' => $product->article_no,
            'sku' => $product->sku,
            'ean' => $product->ean,
            'brand' => optional($product->brand)->name,
            'article_group' => optional($product->articleGroup)->article_group,
            'measure_unit' => optional($product->measureUnit)->measure,
            'color' => $product->color,
            'package_unit' => $product->package_unit,
            'price_unit' => $product->price_unit,
            'image_url' => $this->productImageUrl($product),
            'url' => url('product_details/' . $product->id),
        ];
    }

    private function emptyCompareResponse(string $priceField = 'purchase_price'): array
    {
        return [
            'products' => [],
            'grid' => [],
            'summary' => [
                'cheapest' => null,
                'highest' => null,
                'average' => null,
                'difference' => null,
                'percent_difference' => null,
                'price_count' => 0,
            ],
            'charts' => [
                'bar' => ['labels' => [], 'datasets' => []],
                'pie' => ['labels' => [], 'data' => []],
                'spread' => ['labels' => [], 'min' => [], 'max' => []],
            ],
            'price_field' => $priceField,
        ];
    }

    private function resolvePriceValue(?DistributorPrice $price, string $priceField): ?float
    {
        if (!$price) {
            return null;
        }

        $value = $price->{$priceField};

        if ($value === null && $priceField === 'purchase_price') {
            $value = $price->price;
        }

        if ($value === null && $priceField === 'price') {
            $value = $price->purchase_price;
        }

        return $this->decimalOrNull($value);
    }

    private function calculateDifference(array $prices): array
    {
        $prices = collect($prices)
            ->filter(fn($price) => $price !== null)
            ->map(fn($price) => (float) $price)
            ->values();

        if ($prices->isEmpty()) {
            return [
                'min_price' => null,
                'max_price' => null,
                'difference' => null,
                'percent_diff' => null,
            ];
        }

        if ($prices->count() < 2) {
            $single = round((float) $prices->first(), 2);

            return [
                'min_price' => $single,
                'max_price' => $single,
                'difference' => null,
                'percent_diff' => null,
            ];
        }

        $min = (float) $prices->min();
        $max = (float) $prices->max();
        $diff = round($max - $min, 2);
        $percent = $min > 0 ? round(($diff / $min) * 100, 2) : null;

        return [
            'min_price' => round($min, 2),
            'max_price' => round($max, 2),
            'difference' => $diff,
            'percent_diff' => $percent,
        ];
    }

    private function buildSummary(array $allPriceValues, ?array $cheapest, ?array $highest): array
    {
        $prices = collect($allPriceValues)
            ->pluck('price')
            ->filter(fn($price) => $price !== null)
            ->map(fn($price) => (float) $price)
            ->values();

        if ($prices->isEmpty()) {
            return [
                'cheapest' => null,
                'highest' => null,
                'average' => null,
                'difference' => null,
                'percent_difference' => null,
                'price_count' => 0,
            ];
        }

        $min = (float) $prices->min();
        $max = (float) $prices->max();
        $diff = round($max - $min, 2);

        return [
            'cheapest' => $cheapest,
            'highest' => $highest,
            'average' => round((float) $prices->avg(), 2),
            'difference' => $diff,
            'percent_difference' => $min > 0 ? round(($diff / $min) * 100, 2) : null,
            'price_count' => $prices->count(),
        ];
    }

    private function buildBarChart(array $grid, string $priceField): array
    {
        $labels = [];
        $dataset = [];

        foreach ($grid as $row) {
            foreach ($row['products'] as $product) {
                if ($product['selected_price'] === null) {
                    continue;
                }

                $labels[] = $row['distributor']['name'] . ' · ' . $product['product_name'];
                $dataset[] = $product['selected_price'];
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $priceField === 'purchase_price'
                        ? 'Einkaufspreis'
                        : 'Verkaufspreis',
                    'data' => $dataset,
                ],
            ],
        ];
    }

    private function buildPieChart(array $allPriceValues): array
    {
        $grouped = collect($allPriceValues)
            ->groupBy('distributor')
            ->map(fn($items) => round((float) collect($items)->avg('price'), 2));

        return [
            'labels' => $grouped->keys()->values(),
            'data' => $grouped->values(),
        ];
    }

    private function buildSpreadChart(array $grid): array
    {
        $labels = [];
        $min = [];
        $max = [];

        foreach ($grid as $row) {
            $diff = $row['price_diff'];

            if ($diff['min_price'] === null) {
                continue;
            }

            $labels[] = $row['distributor']['name'];
            $min[] = $diff['min_price'];
            $max[] = $diff['max_price'];
        }

        return [
            'labels' => $labels,
            'min' => $min,
            'max' => $max,
        ];
    }

    private function formatDate(mixed $value): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof CarbonInterface) {
            return $value->format('d.m.Y');
        }

        try {
            return Carbon::parse($value)->format('d.m.Y');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function decimalOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = str_replace(['€', 'EUR', ' '], '', (string) $value);

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? round((float) $value, 2) : null;
    }

    private function productImageUrl(Product $product): ?string
    {
        if (!empty($product->main_image_url)) {
            return $product->main_image_url;
        }

        if ($product->relationLoaded('firstImage') && $product->firstImage && !empty($product->firstImage->image)) {
            return asset('images/products/' . ltrim($product->firstImage->image, '/'));
        }

        return null;
    }

    private function distributorName(Distributor $distributor): string
    {
        return $distributor->name
            ?: $distributor->short_name
            ?: ('Lieferant #' . $distributor->id);
    }
}