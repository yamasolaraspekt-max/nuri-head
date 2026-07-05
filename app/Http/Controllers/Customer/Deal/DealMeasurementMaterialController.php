<?php

namespace App\Http\Controllers\Customer\Deal;
use App\Http\Controllers\Controller;

use App\Models\DealMeasurement;
use App\Models\DealMeasurementDetail;
use App\Support\DealMeasurementHistoryLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class DealMeasurementMaterialController extends Controller
{
    public function saveMaterials(Request $request, DealMeasurement $measurement)
    {
        Gate::authorize('write', $measurement); // S-1a Ownership

        $validated = $request->validate([
            'materials' => ['required', 'array'],
            'history_action' => ['nullable', 'string', 'max:500'],
        ]);

        if ($measurement->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Dieses Aufmaß ist abgeschlossen und gesperrt. Bitte zuerst entsperren.',
            ], 423);
        }

        return DB::transaction(function () use ($validated, $measurement) {
            $oldMaterials = $this->arrayValue($measurement->materials_snapshot);

            $materials = $this->normalizeMaterials($validated['materials']);
            $stats = $this->calculateMaterialStats($materials);

            $action = $validated['history_action'] ?? 'Material geändert';

            $changeCount = DealMeasurementHistoryLogger::logChanges(
                measurement: $measurement,
                action: $action,
                section: 'Material',
                oldData: ['materials' => $oldMaterials],
                newData: ['materials' => $materials]
            );

            $measurement->update([
                'materials_snapshot' => $materials,
                'materials_total_count' => $stats['total'],
                'materials_approved_count' => $stats['approved'],
                'materials_saved_at' => now(),
                'updated_by' => auth()->user()->name ?? auth()->id(),
            ]);

            if ($changeCount === 0) {
                DealMeasurementHistoryLogger::log(
                    measurement: $measurement,
                    action: 'Material gespeichert ohne Änderung',
                    section: 'Material'
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Material wurde gespeichert.',
                'materials' => $materials,
                'materials_total_count' => $stats['total'],
                'materials_approved_count' => $stats['approved'],
                'materials_open_count' => $stats['open'],
                'materials_plan_total' => $stats['plan'],
                'materials_consumption_total' => $stats['verbrauch'],
                'materials_delta_total' => $stats['delta'],
                'history' => $this->formatHistories($measurement),
            ]);
        });
    }

    public function saveDetails(Request $request, DealMeasurement $measurement)
    {
        Gate::authorize('write', $measurement); // S-1a Ownership

        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:PV,WP,OTHER'],
            'data' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Formulardaten sind ungültig.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $type = $request->input('type');
        $requestData = $request->input('data', []);

        $roofData = $requestData['roofs'] ?? [];
        unset($requestData['roofs']);

        $pvData = $type === 'PV' ? $requestData : null;
        $wpData = $type === 'WP' ? $requestData : null;

        return DB::transaction(function () use ($measurement, $type, $requestData, $roofData, $pvData, $wpData) {
            $detail = DealMeasurementDetail::query()
                ->where('deal_measurement_id', $measurement->id)
                ->first();

            $oldFormData = $detail ? $this->arrayValue($detail->form_data) : [];
            $oldRoofData = $detail ? $this->arrayValue($detail->roof_data) : [];

            $formChangeCount = DealMeasurementHistoryLogger::logChanges(
                measurement: $measurement,
                action: $type . ' Formular geändert',
                section: $type,
                oldData: $oldFormData,
                newData: $requestData
            );

            $roofChangeCount = 0;

            if ($type === 'PV') {
                $roofChangeCount = DealMeasurementHistoryLogger::logChanges(
                    measurement: $measurement,
                    action: 'Dachflächen geändert',
                    section: 'Dachflächen',
                    oldData: ['roofs' => $oldRoofData],
                    newData: ['roofs' => $roofData]
                );
            }

            DealMeasurementDetail::updateOrCreate(
                [
                    'deal_measurement_id' => $measurement->id,
                ],
                [
                    'deal_id' => $measurement->deal_id,
                    'offer_id' => $measurement->offer_id,
                    'offer_detail_id' => $measurement->offer_detail_id,
                    'type' => $type,
                    'form_data' => $requestData,
                    'roof_data' => $roofData,
                    'pv_data' => $pvData,
                    'wp_data' => $wpData,
                    'raw_snapshot' => [
                        'type' => $type,
                        'data' => array_merge($requestData, [
                            'roofs' => $roofData,
                        ]),
                    ],
                    'updated_by' => auth()->user()->name ?? auth()->id(),
                    'saved_at' => now(),
                ]
            );

            if ($formChangeCount === 0 && $roofChangeCount === 0) {
                DealMeasurementHistoryLogger::log(
                    measurement: $measurement,
                    action: $type . ' Formular gespeichert ohne Änderung',
                    section: $type
                );
            }

            return response()->json([
                'success' => true,
                'message' => $type . ' Aufmaß wurde gespeichert.',
                'measurement_id' => $measurement->id,
                'saved_at' => now()->toDateTimeString(),
                'history' => $this->formatHistories($measurement),
            ]);
        });
    }

    public function history(DealMeasurement $measurement)
    {
        return response()->json([
            'success' => true,
            'history' => $this->formatHistories($measurement),
        ]);
    }

    private function normalizeMaterials(array $sections): array
    {
        return collect($sections)
            ->map(function ($section, $sectionIndex) {
                $items = collect($section['items'] ?? [])
                    ->map(function ($item, $itemIndex) {
                        return $this->normalizeMaterialItem($item, 0, $itemIndex);
                    })
                    ->filter(fn($item) => !empty($item))
                    ->values()
                    ->toArray();

                return [
                    'id' => (string) ($section['id'] ?? 'section_' . ($sectionIndex + 1)),
                    'title' => $section['title'] ?? 'Ohne Bereich',
                    'items' => $items,
                ];
            })
            ->filter(fn($section) => !empty($section['items']))
            ->values()
            ->toArray();
    }

    private function normalizeMaterialItem(array $item, int $depth = 0, int $sortOrder = 0): array
    {
        if (($item['kind'] ?? null) === 'labor' || ($item['item_type'] ?? null) === 'labor') {
            return [];
        }

        $planQty = $this->toFloat($item['plan_qty'] ?? $item['qty'] ?? $item['qty_offer'] ?? 0);
        $verbrauchQty = $this->toFloat($item['verbrauch_qty'] ?? $item['qty_measurement'] ?? $item['qty_final'] ?? $planQty);
        $delta = $verbrauchQty - $planQty;

        $subItems = collect($item['subItems'] ?? [])
            ->map(function ($subItem, $subIndex) {
                return $this->normalizeMaterialItem($subItem, 1, $subIndex);
            })
            ->filter(fn($subItem) => !empty($subItem))
            ->values()
            ->toArray();

        return [
            'id' => (string) ($item['id'] ?? uniqid('mat_', true)),
            'product_id' => $item['product_id'] ?? null,
            'component_id' => $item['component_id'] ?? null,

            'item_type' => $item['item_type'] ?? null,
            'kind' => $item['kind'] ?? null,

            'name' => $item['name'] ?? 'Unbenannt',
            'description' => $item['description'] ?? null,
            'img' => $item['img'] ?? $item['image'] ?? null,

            'plan_qty' => $planQty,
            'verbrauch_qty' => $verbrauchQty,
            'qty' => $planQty,
            'delta_qty' => $delta,

            'unit' => $item['unit'] ?? 'Stk',
            'article_no' => $item['article_no'] ?? null,
            'distributor' => $item['distributor'] ?? null,
            'distributor_no' => $item['distributor_no'] ?? null,

            'stock_qty' => $item['stock_qty'] ?? null,
            'stock_checked' => (bool) ($item['stock_checked'] ?? false),

            'approved' => (bool) ($item['approved'] ?? false),
            'delta_reason' => $item['delta_reason'] ?? '',

            'depth' => $depth,
            'sort_order' => $sortOrder,

            'subItems' => $subItems,
        ];
    }

    private function calculateMaterialStats(array $sections): array
    {
        $total = 0;
        $approved = 0;
        $plan = 0;
        $verbrauch = 0;

        foreach ($sections as $section) {
            foreach (($section['items'] ?? []) as $item) {
                $this->countMaterialItem($item, $total, $approved, $plan, $verbrauch);

                foreach (($item['subItems'] ?? []) as $subItem) {
                    $this->countMaterialItem($subItem, $total, $approved, $plan, $verbrauch);
                }
            }
        }

        return [
            'total' => $total,
            'approved' => $approved,
            'open' => max(0, $total - $approved),
            'plan' => $plan,
            'verbrauch' => $verbrauch,
            'delta' => $verbrauch - $plan,
        ];
    }

    private function countMaterialItem(array $item, int &$total, int &$approved, float &$plan, float &$verbrauch): void
    {
        $total++;

        if (!empty($item['approved'])) {
            $approved++;
        }

        $plan += $this->toFloat($item['plan_qty'] ?? $item['qty'] ?? 0);
        $verbrauch += $this->toFloat($item['verbrauch_qty'] ?? 0);
    }

    private function formatHistories(DealMeasurement $measurement): array
    {
        return $measurement->histories()
            ->latest()
            ->limit(150)
            ->get()
            ->map(function ($history) {
                return [
                    'id' => $history->id,
                    'action' => $history->action,
                    'section' => $history->section,
                    'field' => $history->field,
                    'oldValue' => $history->old_value,
                    'newValue' => $history->new_value,
                    'changes' => $history->changes ?: [],
                    'user' => $history->created_by ?: 'System',
                    'date' => optional($history->created_at)->toISOString(),
                ];
            })
            ->values()
            ->toArray();
    }

    private function arrayValue($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->toArray();
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function toFloat($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (float) str_replace(',', '.', (string) $value);
    }
}