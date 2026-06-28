<?php

namespace App\Http\Controllers\Product\MasterSet;


use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Distributor;
use App\Models\DistributorPrice;
use Illuminate\Http\Request;

class MasterSetDistributorCompareController extends Controller
{
    public function compare(Product $product, Distributor $distributor)
    {
        $rows = DistributorPrice::query()
            ->with(['distributor:id,name,image'])
            ->where('product_id', $product->id)
            ->where('status', 'Published')
            ->orderByRaw('CASE WHEN distributor_id = ? THEN 0 ELSE 1 END', [$distributor->id])
            ->orderByRaw('COALESCE(purchase_price, price, 999999) asc')
            ->get();

        $items = $rows->map(function ($row) use ($distributor) {
            $effectivePrice = $this->resolveEffectivePrice($row);

            return [
                'id' => $row->id,
                'distributor_price_id' => $row->id,
                'distributor_id' => $row->distributor_id,
                'distributor_name' => optional($row->distributor)->name,
                'distributor_image' => optional($row->distributor)->image,
                'article_no' => $row->article_no,
                'price' => $row->price !== null ? (float) $row->price : null,
                'purchase_price' => $row->purchase_price !== null ? (float) $row->purchase_price : null,
                'discount_price' => $row->discount_price !== null ? (float) $row->discount_price : null,
                'discount_percent' => $row->discount_percent !== null ? (float) $row->discount_percent : null,
                'effective_price' => $effectivePrice,
                'availability' => $row->availability,
                'availability_score' => $this->availabilityScore($row->availability),
                'price_date' => optional($row->price_date)?->format('Y-m-d'),
                'is_current' => (int) $row->distributor_id === (int) $distributor->id,
            ];
        })->values();

        $current = $items->firstWhere('is_current', true);
        $currentPrice = $current['effective_price'] ?? null;

        $items = $items->map(function ($item) use ($currentPrice) {
            $diff = null;
            $diffPercent = null;

            if ($currentPrice !== null && $item['effective_price'] !== null) {
                $diff = round($item['effective_price'] - $currentPrice, 2);

                if ((float) $currentPrice > 0) {
                    $diffPercent = round(($diff / $currentPrice) * 100, 2);
                }
            }

            $item['difference_from_current'] = $diff;
            $item['difference_percent_from_current'] = $diffPercent;

            return $item;
        });

        $cheapest = $items
            ->filter(fn ($x) => $x['effective_price'] !== null)
            ->sortBy('effective_price')
            ->first();

        return response()->json([
            'status' => 'ok',
            'data' => [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->product,
                    'article_no' => $product->article_no,
                    'model' => $product->model,
                    'category' => $product->category,
                    'price_unit' => $product->price_unit,
                ],
                'current_distributor' => [
                    'id' => $distributor->id,
                    'name' => $distributor->name,
                ],
                'current_price' => $currentPrice,
                'cheapest' => $cheapest,
                'items' => $items,
            ],
        ]);
    }

    public function chart(Product $product, Distributor $distributor)
    {
        $rows = DistributorPrice::query()
            ->with('distributor:id,name')
            ->where('product_id', $product->id)
            ->where('status', 'Published')
            ->orderByRaw('COALESCE(purchase_price, price, 999999) asc')
            ->get();

        $labels = [];
        $prices = [];
        $availability = [];
        $currentDistributorId = (int) $distributor->id;

        foreach ($rows as $row) {
            $labels[] = optional($row->distributor)->name ?: ('Distributor #' . $row->distributor_id);
            $prices[] = $this->resolveEffectivePrice($row);
            $availability[] = $this->availabilityScore($row->availability);
        }

        return response()->json([
            'status' => 'ok',
            'data' => [
                'labels' => $labels,
                'prices' => $prices,
                'availability_scores' => $availability,
                'current_distributor_id' => $currentDistributorId,
            ],
        ]);
    }

    protected function resolveEffectivePrice(DistributorPrice $row): ?float
    {
        if ($row->purchase_price !== null) {
            return (float) $row->purchase_price;
        }

        if ($row->discount_price !== null) {
            return (float) $row->discount_price;
        }

        if ($row->price !== null) {
            return (float) $row->price;
        }

        return null;
    }

    protected function availabilityScore(?string $availability): int
    {
        $value = mb_strtolower(trim((string) $availability));

        return match (true) {
            $value === '', $value === 'unknown' => 0,
            str_contains($value, 'lager'), str_contains($value, 'stock'), str_contains($value, 'available') => 100,
            str_contains($value, 'wenig'), str_contains($value, 'limited') => 60,
            str_contains($value, 'bestell'), str_contains($value, 'order') => 35,
            str_contains($value, 'nicht'), str_contains($value, 'out') => 10,
            default => 40,
        };
    }
}