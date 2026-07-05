<?php

namespace App\Http\Controllers\Customer\Offer;


use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\DealMeasurement;
use App\Models\DealMeasurementItem;
use App\Models\Distributor;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\MasterSetComponent;
use App\Models\OfferDetail;
use Illuminate\Support\Facades\Gate;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DealMaterialListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request, OfferDetail $offerDetail)
    {
        $employeeId = (int) auth()->user()->name;

        $offerDetail->load([
            'offer.customer.branchRel',
            'offer.alternative',
            'offer.productGroup',
            'offer.department',
            'offer.creator',
            'offer.assignee',
            'folder',
            'measurement',
        ]);

        $measurement = $this->latestMeasurement($offerDetail);

        $source = $request->get('source', 'compare');

        if (!in_array($source, ['offer', 'feinaufmass', 'compare'], true)) {
            $source = 'compare';
        }

        $offerMaterials = $this->extractMaterialsFromSections(
            $offerDetail->sections ?? [],
            $offerDetail->material_history ?? [],
            'offer'
        );

        $feinaufmassMaterials = $this->buildFeinaufmassMaterials($measurement);

        \Log::info('FEINAUFMASS MATERIAL SOURCE DEBUG', [
            'measurement_id' => $measurement?->id,
            'materials_snapshot_count' => is_array($measurement?->materials_snapshot)
                ? count($measurement->materials_snapshot)
                : 0,
            'sections_snapshot_count' => is_array($measurement?->sections_snapshot)
                ? count($measurement->sections_snapshot)
                : 0,
            'deal_measurement_items_count' => $measurement
                ? DealMeasurementItem::where('deal_measurement_id', $measurement->id)->count()
                : 0,
            'feinaufmass_count' => $feinaufmassMaterials->count(),
            'sample' => $feinaufmassMaterials->take(10)->map(fn($row) => [
                'source' => $row['source'] ?? null,
                'article_no' => $row['article_no'] ?? null,
                'name' => $row['name'] ?? null,
                'plan_qty' => $row['plan_qty'] ?? null,
                'verbrauch_qty' => $row['verbrauch_qty'] ?? null,
                'qty' => $row['qty'] ?? null,
                'delta_qty' => $row['delta_qty'] ?? null,
            ])->values()->all(),
        ]);

        if ($source === 'offer') {
            $materials = $offerMaterials;
        } elseif ($source === 'feinaufmass') {
            $materials = $feinaufmassMaterials;
        } else {
            $materials = $this->compareOfferAndMeasurementMaterials($offerMaterials, $feinaufmassMaterials);
        }

        $materials = $this->attachInventoryData($materials);

        $filtered = $this->filterMaterials($materials, $request);

        $analytics = [
            'total_positions' => $filtered->count(),
            'unique_articles' => $filtered->pluck('item_key')->unique()->count(),
            'total_qty' => (float) $filtered->sum('qty'),
            'lager_count' => (int) $filtered->where('stock_status', 'lager')->count(),
            'bestellen_count' => (int) $filtered->whereIn('stock_status', ['bestellen', 'teilweise'])->count(),
            'teilweise_count' => (int) $filtered->where('stock_status', 'teilweise')->count(),
            'unknown_count' => (int) $filtered->where('stock_status', 'unbekannt')->count(),
            'changed_count' => (int) $filtered->where('change_type', 'changed')->count(),
            'added_count' => (int) $filtered->where('change_type', 'added')->count(),
            'removed_count' => (int) $filtered->where('change_type', 'removed')->count(),
            'same_count' => (int) $filtered->where('change_type', 'same')->count(),
        ];

        $suppliers = $materials
            ->pluck('distributor_name')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $measurementSource = $this->resolveMeasurementMaterialSource($measurement);

        return view('admin.deal.material-list', [
            'offerDetail' => $offerDetail,
            'offer' => $offerDetail->offer,
            'customer' => $offerDetail->offer?->customer,
            'alternative' => $offerDetail->offer?->alternative,
            'product' => $offerDetail->offer?->productGroup,
            'department' => $offerDetail->offer?->department,
            'folder' => $offerDetail->folder,
            'employeeId' => $employeeId,

            'materials' => $filtered,
            'analytics' => $analytics,
            'suppliers' => $suppliers,

            'measurement' => $measurement,
            'source' => $source,

            'hasFeinaufmassData' => $feinaufmassMaterials->isNotEmpty(),
            'offerMaterialsCount' => $offerMaterials->count(),
            'measurementMaterialsCount' => $feinaufmassMaterials->count(),

            'measurementSnapshotSource' => $measurementSource['source'],
            'measurementSnapshotReason' => $measurementSource['reason'],

            'materialDocumentTitle' => $this->buildMaterialDocumentTitle($offerDetail, $measurement),
            'materialDocumentSubtitle' => $this->buildMaterialDocumentSubtitle($offerDetail, $measurement),
            'materialPrintModes' => [
                'normal' => 'Normaldruck: Artikelbild, Name, Beschreibung und Menge',
                'lager' => 'Lagerdruck: zusätzlich Lagerort, Raum, Regal, Reihe, Fach',
                'order' => 'Bestelldruck: zusätzlich Lieferant/Quelle, Bestellstatus und Liefertermin',
            ],

            'employees' => Employee::query()
                ->select('id', 'name', 'lastname', 'email')
                ->orderBy('name')
                ->orderBy('lastname')
                ->get(),

            'distributors' => Distributor::query()
                ->select('id', 'name', 'short_name', 'city')
                ->where(function ($query) {
                    $query->whereNull('is_hidden')
                        ->orWhere('is_hidden', false);
                })
                ->orderBy('name')
                ->get(),

            'brands' => Brand::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    protected function latestMeasurement(OfferDetail $offerDetail): ?DealMeasurement
    {
        return DealMeasurement::query()
            ->where('offer_detail_id', $offerDetail->id)
            ->latest('updated_at')
            ->latest('id')
            ->first();
    }

    /** S-1a Ownership: schreibende Material-Aktionen gegen das zugehörige Aufmaß absichern. */
    protected function authorizeMeasurementWrite(OfferDetail $offerDetail): void
    {
        $measurement = $this->latestMeasurement($offerDetail);
        if ($measurement) {
            Gate::authorize('write', $measurement);
        }
    }

    protected function buildFeinaufmassMaterials(?DealMeasurement $measurement): Collection
    {
        if (!$measurement) {
            return collect();
        }

        /*
        |--------------------------------------------------------------------------
        | Your real Feinaufmaß material list is saved in materials_snapshot.
        | Use it first because it contains plan_qty, verbrauch_qty and delta_qty.
        |--------------------------------------------------------------------------
        */
        $materialsSnapshot = is_array($measurement->materials_snapshot)
            ? $measurement->materials_snapshot
            : [];

        if (!empty($materialsSnapshot)) {
            $materials = $this->extractMaterialsFromAnySnapshot(
                $materialsSnapshot,
                'materials_snapshot'
            );

            if ($materials->isNotEmpty()) {
                return $materials;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback: deal_measurement_items
        |--------------------------------------------------------------------------
        */
        $itemMaterials = $this->extractMaterialsFromDealMeasurementItems($measurement);

        if ($itemMaterials->isNotEmpty()) {
            return $itemMaterials;
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback: sections_snapshot
        |--------------------------------------------------------------------------
        */
        $sectionsSnapshot = is_array($measurement->sections_snapshot)
            ? $measurement->sections_snapshot
            : [];

        if (!empty($sectionsSnapshot)) {
            return $this->extractMaterialsFromAnySnapshot(
                $sectionsSnapshot,
                'sections_snapshot'
            );
        }

        return collect();
    }
    protected function extractMaterialsFromAnySnapshot(array $snapshot, string $sourceName): Collection
    {
        $snapshotCollection = collect($snapshot);

        if ($snapshotCollection->isEmpty()) {
            return collect();
        }

        $first = $snapshotCollection->first();

        if (is_array($first) && array_key_exists('items', $first)) {
            return $this->extractMaterialsFromMeasurementSnapshot($snapshot)
                ->map(function (array $row) use ($sourceName) {
                    $row['source'] = $sourceName;
                    return $row;
                });
        }

        $rows = collect();

        foreach ($snapshot as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $kind = $item['kind'] ?? null;
            $itemType = $item['item_type'] ?? null;

            if ($kind === 'labor' || $itemType === 'labor') {
                continue;
            }

            $hasMaterialIdentity =
                !empty($item['article_no'])
                || !empty($item['distributor_article_no'])
                || !empty($item['distributor_no'])
                || !empty($item['product_id'])
                || !empty($item['productId'])
                || !empty($item['component_id'])
                || !empty($item['master_set_component_id'])
                || !empty($item['name']);

            if (!$hasMaterialIdentity) {
                continue;
            }

            $rows->push(
                $this->mapArrayItemToMaterialRow(
                    item: $item,
                    sectionTitle: $item['section_title'] ?? $item['section'] ?? 'Feinaufmaß',
                    sectionIndex: (int) ($item['section_index'] ?? 0),
                    itemIndex: (int) ($item['item_index'] ?? $index),
                    parentMasterSetName: $item['master_set_name'] ?? null,
                    source: 'feinaufmass'
                )
            );
        }

        return $rows
            ->groupBy('item_key')
            ->map(function (Collection $group) use ($sourceName) {
                $first = $group->first();

                $qty = (float) $group->sum('qty');
                $planQty = (float) $group->sum('plan_qty');
                $verbrauchQty = (float) $group->sum('verbrauch_qty');

                $first['qty'] = $verbrauchQty > 0 ? $verbrauchQty : $qty;
                $first['plan_qty'] = $planQty;
                $first['verbrauch_qty'] = $verbrauchQty > 0 ? $verbrauchQty : $qty;
                $first['delta_qty'] = $first['verbrauch_qty'] - $first['plan_qty'];

                $first['positions_count'] = $group->count();
                $first['raw_rows'] = $group->values();
                $first['source'] = $sourceName;

                return $first;
            })
            ->values();
    }

    protected function extractMaterialsFromMeasurementSnapshot(array $snapshot): Collection
    {
        $rows = collect();

        foreach ($snapshot as $sectionIndex => $section) {
            if (!is_array($section)) {
                continue;
            }

            $sectionTitle = $section['title'] ?? ('Abschnitt ' . ($sectionIndex + 1));

            foreach (($section['items'] ?? []) as $itemIndex => $item) {
                if (!is_array($item)) {
                    continue;
                }

                $rows = $rows->merge(
                    $this->flattenMeasurementSnapshotItem(
                        item: $item,
                        sectionTitle: $sectionTitle,
                        sectionIndex: $sectionIndex,
                        itemIndex: $itemIndex,
                        parentMasterSetName: ($item['item_type'] ?? null) === 'master_set'
                        ? ($item['name'] ?? null)
                        : null,
                        depth: (int) ($item['depth'] ?? 0)
                    )
                );
            }
        }

        return $rows
            ->groupBy('item_key')
            ->map(function (Collection $group) {
                $first = $group->first();

                return [
                    'item_key' => $first['item_key'],
                    'name' => $first['name'],
                    'article_no' => $first['article_no'],
                    'component_id' => $first['component_id'],
                    'product_id' => $first['product_id'],
                    'item_type' => $first['item_type'],

                    'section_title' => $first['section_title'],
                    'section_index' => $first['section_index'] ?? 0,
                    'item_index' => $first['item_index'] ?? 0,
                    'master_set_name' => $first['master_set_name'],

                    'qty' => (float) $group->sum('qty'),
                    'plan_qty' => (float) $group->sum('plan_qty'),
                    'verbrauch_qty' => (float) $group->sum('verbrauch_qty'),
                    'delta_qty' => (float) $group->sum('delta_qty'),

                    'unit' => $first['unit'],
                    'measure' => $first['measure'],

                    'price' => $first['price'],
                    'ek' => $first['ek'],

                    'availability' => $first['availability'],
                    'distributor_name' => $first['distributor_name'],
                    'distributor_id' => $first['distributor_id'],
                    'distributor_article_no' => $first['distributor_article_no'],
                    'supplier_name' => $first['supplier_name'],
                    'brand_name' => $first['brand_name'],
                    'img' => $first['img'],

                    'approved' => $group->every(fn($row) => (bool) ($row['approved'] ?? false)),
                    'delta_reason' => $group->pluck('delta_reason')->filter()->unique()->implode(' | '),

                    'stock_status' => $first['stock_status'] ?? 'unbekannt',
                    'stock_checked' => (bool) ($first['stock_checked'] ?? false),
                    'required_qty' => $first['required_qty'] ?? null,
                    'stock_qty' => $first['stock_qty'] ?? null,
                    'found_qty' => $first['found_qty'] ?? null,
                    'missing_qty' => $first['missing_qty'] ?? null,
                    'order_qty' => $first['order_qty'] ?? null,
                    'found_unit' => $first['found_unit'] ?? null,

                    'order_status' => $first['order_status'] ?? null,
                    'purchase_status' => $first['purchase_status'] ?? null,
                    'order_details' => $first['order_details'] ?? null,
                    'purchase_order' => $first['purchase_order'] ?? null,

                    'location' => $first['location'] ?? null,
                    'location_details' => $first['location_details'] ?? null,

                    'checked_by' => $first['checked_by'] ?? null,
                    'checked_at' => $first['checked_at'] ?? null,

                    'updated_by' => $first['updated_by'] ?? null,
                    'updated_by_name' => $first['updated_by_name'] ?? null,
                    'updated_by_data' => $first['updated_by_data'] ?? null,
                    'updated_at' => $first['updated_at'] ?? null,

                    'note' => $first['note'] ?? null,
                    'lager_note' => $first['lager_note'] ?? null,

                    'stock_allocation' => $first['stock_allocation'] ?? null,
                    'material_history' => $first['material_history'] ?? [],

                    'positions_count' => $group->count(),
                    'raw_rows' => $group->values(),
                    'source' => 'feinaufmass',
                ];
            })
            ->sortBy([
                ['section_title', 'asc'],
                ['master_set_name', 'asc'],
                ['name', 'asc'],
            ])
            ->values();
    }

    protected function extractMaterialsFromDealMeasurementItems(?DealMeasurement $measurement): Collection
    {
        if (!$measurement) {
            return collect();
        }

        $items = DealMeasurementItem::query()
            ->where('deal_measurement_id', $measurement->id)
            ->orderBy('section_title')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            return collect();
        }

        return $items
            ->filter(function (DealMeasurementItem $item) {
                $itemType = $item->item_type;
                $kind = $item->kind;

                if ($kind === 'labor' || $itemType === 'labor') {
                    return false;
                }

                return !empty($item->product_id)
                    || !empty($item->component_id)
                    || !empty($item->master_set_component_id)
                    || !empty($item->article_no)
                    || !empty($item->distributor_article_no)
                    || !empty($item->name);
            })
            ->map(fn(DealMeasurementItem $item) => $this->mapDealMeasurementItemToMaterialRow($item))
            ->groupBy('item_key')
            ->map(function (Collection $group) {
                $first = $group->first();

                return [
                    'item_key' => $first['item_key'],
                    'name' => $first['name'],
                    'article_no' => $first['article_no'],
                    'component_id' => $first['component_id'],
                    'product_id' => $first['product_id'],
                    'item_type' => $first['item_type'],

                    'section_title' => $first['section_title'],
                    'section_index' => $first['section_index'],
                    'item_index' => $first['item_index'],
                    'master_set_name' => $first['master_set_name'],

                    'qty' => (float) $group->sum('qty'),
                    'plan_qty' => (float) $group->sum('plan_qty'),
                    'verbrauch_qty' => (float) $group->sum('verbrauch_qty'),
                    'delta_qty' => (float) $group->sum('delta_qty'),

                    'unit' => $first['unit'],
                    'measure' => $first['measure'],

                    'price' => $first['price'],
                    'ek' => $first['ek'],

                    'availability' => $first['availability'],
                    'distributor_name' => $first['distributor_name'],
                    'distributor_id' => $first['distributor_id'],
                    'distributor_article_no' => $first['distributor_article_no'],
                    'supplier_name' => $first['supplier_name'],
                    'brand_name' => $first['brand_name'],
                    'img' => $first['img'],

                    'approved' => $group->every(fn($row) => (bool) ($row['approved'] ?? false)),
                    'delta_reason' => $group->pluck('delta_reason')->filter()->unique()->implode(' | '),

                    'stock_status' => $first['stock_status'],
                    'stock_checked' => $first['stock_checked'],
                    'required_qty' => $first['required_qty'],
                    'stock_qty' => $first['stock_qty'],
                    'found_qty' => $first['found_qty'],
                    'missing_qty' => $first['missing_qty'],
                    'order_qty' => $first['order_qty'],
                    'found_unit' => $first['found_unit'],

                    'order_status' => $first['order_status'],
                    'purchase_status' => $first['purchase_status'],
                    'order_details' => $first['order_details'],
                    'purchase_order' => $first['purchase_order'],

                    'location' => $first['location'],
                    'location_details' => $first['location_details'],

                    'checked_by' => $first['checked_by'],
                    'checked_at' => $first['checked_at'],

                    'updated_by' => $first['updated_by'],
                    'updated_by_name' => $first['updated_by_name'],
                    'updated_by_data' => $first['updated_by_data'],
                    'updated_at' => $first['updated_at'],

                    'note' => $first['note'],
                    'lager_note' => $first['lager_note'],

                    'stock_allocation' => $first['stock_allocation'],
                    'material_history' => $first['material_history'],

                    'positions_count' => $group->count(),
                    'raw_rows' => $group->values(),
                    'source' => 'deal_measurement_items',
                ];
            })
            ->values();
    }

    protected function mapDealMeasurementItemToMaterialRow(DealMeasurementItem $item): array
    {
        $raw = is_array($item->raw_snapshot) ? $item->raw_snapshot : [];

        $productId = $item->product_id
            ?? $raw['product_id']
            ?? $raw['productId']
            ?? null;

        $componentId = $item->component_id
            ?? $item->master_set_component_id
            ?? $raw['component_id']
            ?? $raw['master_set_component_id']
            ?? null;

        $articleNo = trim((string) (
            $item->article_no
            ?? $raw['article_no']
            ?? $item->distributor_article_no
            ?? $raw['distributor_article_no']
            ?? $raw['distributor_no']
            ?? ''
        ));

        $name = trim((string) (
            $item->name
            ?? $raw['name']
            ?? 'Unbekanntes Material'
        ));

        $qtyOffer = $this->offerQtyFromMeasurementItem($item, $raw);
        $qtyFinal = $this->materialQtyFromMeasurementItem($item, $raw);

        $unit = $item->unit ?? $raw['unit'] ?? null;

        $stockAllocation = is_array($item->stock_allocation)
            ? $item->stock_allocation
            : ($raw['stock_allocation'] ?? null);

        $updatedByName = null;
        $updatedByData = null;

        if (!empty($item->updated_by)) {
            $employee = Employee::find((int) $item->updated_by);

            $updatedByName = trim(($employee?->name ?? '') . ' ' . ($employee?->lastname ?? '')) ?: null;

            $updatedByData = [
                'employee_id' => (int) $item->updated_by,
                'name' => $updatedByName,
            ];
        }

        $itemKey = $this->buildItemKey(
            articleNo: $articleNo,
            componentId: $componentId,
            productId: $productId,
            name: $name
        );

        return [
            'item_key' => $itemKey,
            'name' => $name,
            'article_no' => $articleNo,
            'component_id' => $componentId,
            'product_id' => $productId,
            'item_type' => $item->item_type ?? $raw['item_type'] ?? null,

            'section_title' => $item->section_title ?? $raw['section_title'] ?? 'Feinaufmaß',
            'section_index' => 0,
            'item_index' => (int) ($item->sort_order ?? $item->id),
            'master_set_name' => $raw['master_set_name'] ?? null,

            'qty' => $qtyFinal,
            'plan_qty' => $qtyOffer,
            'verbrauch_qty' => $qtyFinal,
            'delta_qty' => (float) ($raw['delta_qty'] ?? ($qtyFinal - $qtyOffer)),

            'unit' => $unit,
            'measure' => $item->measure ?? $raw['measure'] ?? $unit,

            'price' => (float) ($item->unit_price ?? $raw['price'] ?? 0),
            'ek' => (float) ($item->purchase_price ?? $raw['ek'] ?? 0),

            'availability' => (bool) ($raw['availability'] ?? false),

            'distributor_name' => $item->distributor_name ?? $raw['distributor_name'] ?? $raw['distributor'] ?? null,
            'distributor_id' => $item->distributor_id ?? $raw['distributor_id'] ?? null,
            'distributor_article_no' => $item->distributor_article_no ?? $raw['distributor_article_no'] ?? $raw['distributor_no'] ?? null,
            'supplier_name' => $item->distributor_name ?? $raw['supplier_name'] ?? $raw['distributor_name'] ?? null,
            'brand_name' => $raw['brand_name'] ?? null,

            'img' => $item->image ?? $raw['img'] ?? $raw['image'] ?? null,

            'approved' => (bool) ($raw['approved'] ?? $item->is_checked ?? false),
            'delta_reason' => $item->note ?? $raw['delta_reason'] ?? '',

            'stock_status' => $raw['stock_status'] ?? $item->order_status ?? $raw['order_status'] ?? 'unbekannt',
            'stock_checked' => (bool) ($raw['stock_checked'] ?? $item->is_checked ?? false),
            'required_qty' => $raw['required_qty'] ?? null,

            'stock_qty' => $raw['stock_qty'] ?? data_get($stockAllocation, 'lager'),
            'found_qty' => $raw['found_qty'] ?? data_get($stockAllocation, 'lager'),
            'missing_qty' => $raw['missing_qty'] ?? data_get($stockAllocation, 'bestellen'),
            'order_qty' => $raw['order_qty'] ?? data_get($stockAllocation, 'bestellen'),
            'found_unit' => $raw['found_unit'] ?? $unit,

            'order_status' => $raw['order_status'] ?? $item->order_status ?? null,
            'purchase_status' => $raw['purchase_status'] ?? null,
            'order_details' => $raw['order_details'] ?? $raw['purchase_order'] ?? null,
            'purchase_order' => $raw['purchase_order'] ?? $raw['order_details'] ?? null,

            'location' => $raw['location'] ?? null,
            'location_details' => $raw['location_details'] ?? null,

            'checked_by' => $raw['checked_by'] ?? null,
            'checked_at' => $raw['checked_at'] ?? optional($item->updated_at)->toDateTimeString(),

            'updated_by' => $item->updated_by ?? $raw['updated_by'] ?? null,
            'updated_by_name' => $raw['updated_by_name'] ?? $updatedByName,
            'updated_by_data' => $raw['updated_by_data'] ?? $updatedByData,
            'updated_at' => optional($item->updated_at)->toDateTimeString(),

            'note' => $item->note ?? $raw['note'] ?? null,
            'lager_note' => $raw['lager_note'] ?? $item->note ?? null,

            'stock_allocation' => $stockAllocation,
            'material_history' => $raw['material_history'] ?? [],

            'source' => 'deal_measurement_items',
        ];
    }

    protected function mergeAllFeinaufmassSources(Collection $rows): Collection
    {
        return $rows
            ->groupBy(fn(array $row) => $this->bestMaterialGroupKey($row))
            ->map(function (Collection $group) {
                $dealRows = $group->filter(fn($row) => ($row['source'] ?? null) === 'deal_measurement_items');
                $sectionRows = $group->filter(fn($row) => ($row['source'] ?? null) === 'sections_snapshot');
                $materialRows = $group->filter(fn($row) => ($row['source'] ?? null) === 'materials_snapshot');

                $dealQty = (float) $dealRows->sum('qty');
                $sectionQty = (float) $sectionRows->sum('qty');
                $materialQty = (float) $materialRows->sum('qty');

                if ($dealRows->isNotEmpty() && $dealQty > 0) {
                    $preferredRows = $dealRows;
                    $preferred = $dealRows->first();
                    $qty = $dealQty;
                } elseif ($sectionRows->isNotEmpty()) {
                    $preferredRows = $sectionRows;
                    $preferred = $sectionRows->first();
                    $qty = $sectionQty;
                } elseif ($materialRows->isNotEmpty()) {
                    $preferredRows = $materialRows;
                    $preferred = $materialRows->first();
                    $qty = $materialQty;
                } else {
                    $preferredRows = $group;
                    $preferred = $group->first();
                    $qty = (float) $group->sum('qty');
                }

                $preferred['qty'] = $qty;
                $preferred['verbrauch_qty'] = $qty;
                $preferred['positions_count'] = $preferredRows->count();
                $preferred['raw_rows'] = $group->values();

                return $preferred;
            })
            ->values();
    }

    protected function extractMaterialsFromSections(array $sections, array $history = [], string $source = 'offer'): Collection
    {
        $rows = collect();

        foreach ($sections as $sectionIndex => $section) {
            if (!is_array($section)) {
                continue;
            }

            $sectionTitle = $section['title'] ?? ('Abschnitt ' . ($sectionIndex + 1));

            foreach (($section['items'] ?? []) as $itemIndex => $item) {
                if (!is_array($item)) {
                    continue;
                }

                $rows = $rows->merge(
                    $this->flattenItem(
                        item: $item,
                        sectionTitle: $sectionTitle,
                        sectionIndex: $sectionIndex,
                        itemIndex: $itemIndex,
                        parentMasterSetName: ($item['item_type'] ?? null) === 'master_set'
                        ? ($item['name'] ?? null)
                        : null,
                        source: $source
                    )
                );
            }
        }

        return $rows
            ->groupBy('item_key')
            ->map(function (Collection $group) use ($history, $source) {
                $first = $group->first();

                $saved = $history[$first['item_key']] ?? [];

                if (!is_array($saved)) {
                    $saved = [];
                }

                $qty = (float) $group->sum('qty');

                return [
                    'item_key' => $first['item_key'],
                    'name' => $first['name'],
                    'article_no' => $first['article_no'],
                    'component_id' => $first['component_id'],
                    'product_id' => $first['product_id'],
                    'item_type' => $first['item_type'],

                    'section_title' => $first['section_title'],
                    'section_index' => $first['section_index'],
                    'item_index' => $first['item_index'],
                    'master_set_name' => $first['master_set_name'],

                    'qty' => $qty,
                    'plan_qty' => (float) ($first['plan_qty'] ?? $qty),
                    'verbrauch_qty' => (float) ($first['verbrauch_qty'] ?? $qty),
                    'delta_qty' => (float) ($first['delta_qty'] ?? 0),

                    'unit' => $first['unit'],
                    'measure' => $first['measure'],

                    'price' => $first['price'],
                    'ek' => $first['ek'],

                    'availability' => $first['availability'],
                    'distributor_name' => $first['distributor_name'],
                    'distributor_id' => $first['distributor_id'],
                    'distributor_article_no' => $first['distributor_article_no'],
                    'supplier_name' => $first['supplier_name'],
                    'brand_name' => $first['brand_name'],
                    'img' => $first['img'],

                    'approved' => (bool) ($first['approved'] ?? false),
                    'delta_reason' => $first['delta_reason'] ?? null,

                    'stock_status' => $saved['stock_status']
                        ?? $first['stock_status']
                        ?? ($first['availability'] ? 'lager' : 'unbekannt'),

                    'stock_checked' => (bool) (
                        $saved['stock_checked']
                        ?? $first['stock_checked']
                        ?? false
                    ),

                    'required_qty' => $saved['required_qty'] ?? $first['required_qty'] ?? $qty,

                    'stock_qty' => array_key_exists('stock_qty', $saved)
                        ? $saved['stock_qty']
                        : ($first['stock_qty'] ?? null),

                    'found_qty' => array_key_exists('found_qty', $saved)
                        ? $saved['found_qty']
                        : ($first['found_qty'] ?? null),

                    'missing_qty' => array_key_exists('missing_qty', $saved)
                        ? $saved['missing_qty']
                        : ($first['missing_qty'] ?? null),

                    'order_qty' => array_key_exists('order_qty', $saved)
                        ? $saved['order_qty']
                        : ($first['order_qty'] ?? null),

                    'found_unit' => $saved['found_unit'] ?? $first['found_unit'] ?? null,

                    'order_status' => $saved['order_status'] ?? $first['order_status'] ?? null,
                    'purchase_status' => $saved['purchase_status'] ?? $first['purchase_status'] ?? null,
                    'order_details' => $saved['order_details'] ?? $first['order_details'] ?? null,
                    'purchase_order' => $saved['purchase_order'] ?? $first['purchase_order'] ?? null,

                    'note' => $saved['note'] ?? $first['note'] ?? null,
                    'lager_note' => $saved['lager_note'] ?? $first['lager_note'] ?? null,

                    'location' => $saved['location'] ?? $first['location'] ?? null,
                    'location_details' => $saved['location_details'] ?? $first['location_details'] ?? null,

                    'updated_by' => $saved['updated_by'] ?? $first['updated_by'] ?? null,
                    'updated_by_name' => $saved['updated_by_name'] ?? $first['updated_by_name'] ?? null,
                    'updated_by_data' => $saved['updated_by_data'] ?? $first['updated_by_data'] ?? null,
                    'updated_at' => $saved['updated_at'] ?? $first['updated_at'] ?? null,

                    'checked_by' => $saved['checked_by'] ?? $first['checked_by'] ?? null,
                    'checked_at' => $saved['checked_at'] ?? $first['checked_at'] ?? null,

                    'stock_allocation' => $saved['stock_allocation'] ?? $first['stock_allocation'] ?? null,
                    'material_history' => array_values(array_filter(array_merge(
                        is_array($first['material_history'] ?? null) ? $first['material_history'] : [],
                        is_array($saved['_events'] ?? null) ? $saved['_events'] : []
                    ))),

                    'positions_count' => $group->count(),
                    'raw_rows' => $group->values(),
                    'source' => $source,
                ];
            })
            ->sortBy([
                ['stock_status', 'asc'],
                ['section_title', 'asc'],
                ['master_set_name', 'asc'],
                ['name', 'asc'],
            ])
            ->values();
    }

    protected function flattenItem(
        array $item,
        string $sectionTitle,
        int $sectionIndex,
        int $itemIndex,
        ?string $parentMasterSetName = null,
        string $source = 'offer',
        float $qtyMultiplier = 1.0
    ): Collection {
        $rows = collect();

        $itemType = $item['item_type'] ?? null;
        $kind = $item['kind'] ?? null;
        $subItems = collect($item['subItems'] ?? []);

        $isLabor = $kind === 'labor' || $itemType === 'labor';
        $isMasterSet = $itemType === 'master_set';

        $isRealMaterial = !$isLabor && in_array($itemType, [
            'master_set_component',
            'article',
            'product',
            'material',
            'stammartikel',
        ], true);

        if (!$isRealMaterial && !$isLabor && !$isMasterSet) {
            if (
                !empty($item['product_id'])
                || !empty($item['productId'])
                || !empty($item['component_id'])
                || !empty($item['article_no'])
                || !empty($item['distributor_article_no'])
                || !empty($item['distributor_no'])
            ) {
                $isRealMaterial = true;
            }
        }

        if ($isRealMaterial) {
            $rows->push(
                $this->mapArrayItemToMaterialRow(
                    item: $item,
                    sectionTitle: $sectionTitle,
                    sectionIndex: $sectionIndex,
                    itemIndex: $itemIndex,
                    parentMasterSetName: $parentMasterSetName,
                    source: $source,
                    qtyMultiplier: $qtyMultiplier
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | If this is a master set, all child components must be multiplied by
        | the master set quantity.
        |--------------------------------------------------------------------------
        */
        $currentQty = (float) ($item['qty'] ?? 1);

        $childMultiplier = $isMasterSet
            ? $qtyMultiplier * max($currentQty, 1)
            : $qtyMultiplier;

        foreach ($subItems as $subIndex => $subItem) {
            if (!is_array($subItem)) {
                continue;
            }

            $rows = $rows->merge(
                $this->flattenItem(
                    item: $subItem,
                    sectionTitle: $sectionTitle,
                    sectionIndex: $sectionIndex,
                    itemIndex: $subIndex,
                    parentMasterSetName: $isMasterSet
                    ? ($item['name'] ?? $parentMasterSetName)
                    : $parentMasterSetName,
                    source: $source,
                    qtyMultiplier: $childMultiplier
                )
            );
        }

        return $rows;
    }
    protected function flattenMeasurementSnapshotItem(
        array $item,
        string $sectionTitle,
        int $sectionIndex,
        int $itemIndex,
        ?string $parentMasterSetName = null,
        int $depth = 0
    ): Collection {
        $rows = collect();

        $itemType = $item['item_type'] ?? null;
        $kind = $item['kind'] ?? null;

        $isLabor = $kind === 'labor' || $itemType === 'labor';
        $isMasterSet = $itemType === 'master_set';

        $isRealMaterial = !$isLabor && in_array($itemType, [
            'master_set_component',
            'article',
            'product',
            'material',
            'stammartikel',
        ], true);

        if (!$isRealMaterial && !$isLabor && !$isMasterSet) {
            if (!empty($item['product_id']) || !empty($item['productId']) || !empty($item['component_id'])) {
                $isRealMaterial = true;
            }
        }

        if ($isRealMaterial) {
            $rows->push(
                $this->mapArrayItemToMaterialRow(
                    item: $item,
                    sectionTitle: $sectionTitle,
                    sectionIndex: $sectionIndex,
                    itemIndex: $itemIndex,
                    parentMasterSetName: $parentMasterSetName,
                    source: 'feinaufmass'
                )
            );
        }

        foreach (($item['subItems'] ?? []) as $subIndex => $subItem) {
            if (!is_array($subItem)) {
                continue;
            }

            $rows = $rows->merge(
                $this->flattenMeasurementSnapshotItem(
                    item: $subItem,
                    sectionTitle: $sectionTitle,
                    sectionIndex: $sectionIndex,
                    itemIndex: $subIndex,
                    parentMasterSetName: $isMasterSet
                    ? ($item['name'] ?? $parentMasterSetName)
                    : $parentMasterSetName,
                    depth: $depth + 1
                )
            );
        }

        return $rows;
    }

    protected function mapArrayItemToMaterialRow(
        array $item,
        string $sectionTitle,
        int $sectionIndex,
        int $itemIndex,
        ?string $parentMasterSetName = null,
        string $source = 'offer',
        float $qtyMultiplier = 1.0
    ): array {
        $productId = $item['productId'] ?? $item['product_id'] ?? null;
        $componentId = $item['component_id'] ?? $item['master_set_component_id'] ?? null;

        if (!$componentId && !empty($item['id']) && is_numeric($item['id'])) {
            $componentId = (int) $item['id'];
        }

        $articleNo = trim((string) (
            $item['article_no']
            ?? $item['distributor_article_no']
            ?? $item['distributor_no']
            ?? ''
        ));

        $name = trim((string) ($item['name'] ?? 'Unbekanntes Material'));

        $brandName = $item['brand_name'] ?? null;
        $supplierName = $item['supplier_name'] ?? null;
        $distributorName = $item['distributor_name'] ?? $item['distributor'] ?? null;
        $img = $item['img'] ?? $item['image'] ?? null;

        if ($productId) {
            $product = Product::with(['brand', 'measure', 'firstImage'])->find($productId);

            if ($product) {
                $brandName = $brandName ?: $product->brand?->name;
                $articleNo = $articleNo !== '' ? $articleNo : (string) ($product->article_no ?? '');
                $img = $img ?: $product->main_image_url;
            }
        }

        if ($componentId) {
            $component = MasterSetComponent::with(['product.brand', 'distributor'])->find($componentId);

            if ($component) {
                $articleNo = $articleNo !== '' ? $articleNo : (string) ($component->article_no ?? '');
                $productId = $productId ?: $component->product_id;
                $distributorName = $distributorName ?: $component->distributor?->name;
                $supplierName = $supplierName ?: $component->distributor?->name;

                if (!$brandName && $component->product?->brand) {
                    $brandName = $component->product->brand->name;
                }
            }
        }

        if (!empty($item['distributor_id']) && !$supplierName) {
            $distributor = Distributor::find($item['distributor_id']);
            $supplierName = $distributor?->name;
            $distributorName = $distributorName ?: $distributor?->name;
        }

        $planQty = $this->planQtyFromArrayItem($item);
        $qty = $this->materialQtyFromArrayItem($item, $source);

        /*
        |--------------------------------------------------------------------------
        | Offer sections store component qty per master set.
        | Feinaufmaß materials_snapshot already stores total plan_qty / verbrauch_qty.
        |--------------------------------------------------------------------------
        */
        if ($source === 'offer') {
            $planQty = $planQty * $qtyMultiplier;
            $qty = $qty * $qtyMultiplier;
        }

        $itemKey = $this->buildItemKey(
            articleNo: $articleNo,
            componentId: $componentId,
            productId: $productId,
            name: $name
        );

        return [
            'item_key' => $itemKey,
            'name' => $name,
            'article_no' => $articleNo,
            'component_id' => $componentId,
            'product_id' => $productId,
            'item_type' => $item['item_type'] ?? null,

            'section_title' => $sectionTitle,
            'section_index' => $sectionIndex,
            'item_index' => $itemIndex,
            'master_set_name' => $parentMasterSetName,

            'qty' => $qty,
            'plan_qty' => $planQty,
            'verbrauch_qty' => $qty,
            'delta_qty' => (float) ($item['delta_qty'] ?? ($qty - $planQty)),

            'unit' => $item['unit'] ?? null,
            'measure' => $item['measure'] ?? null,

            'price' => (float) ($item['price'] ?? $item['unit_price'] ?? 0),
            'ek' => (float) ($item['ek'] ?? $item['purchase_price'] ?? 0),

            'availability' => (bool) ($item['availability'] ?? false),
            'distributor_name' => $distributorName,
            'distributor_id' => $item['distributor_id'] ?? null,
            'distributor_article_no' => $item['distributor_article_no'] ?? $item['distributor_no'] ?? null,
            'supplier_name' => $supplierName,
            'brand_name' => $brandName,
            'img' => $img,

            'approved' => (bool) ($item['approved'] ?? false),
            'delta_reason' => $item['delta_reason'] ?? null,

            'stock_status' => $item['stock_status'] ?? null,
            'stock_checked' => (bool) ($item['stock_checked'] ?? false),
            'required_qty' => $item['required_qty'] ?? null,
            'stock_qty' => $item['stock_qty'] ?? null,
            'found_qty' => $item['found_qty'] ?? null,
            'missing_qty' => $item['missing_qty'] ?? null,
            'order_qty' => $item['order_qty'] ?? null,
            'found_unit' => $item['found_unit'] ?? null,

            'order_status' => $item['order_status'] ?? null,
            'purchase_status' => $item['purchase_status'] ?? null,
            'order_details' => $item['order_details'] ?? $item['purchase_order'] ?? null,
            'purchase_order' => $item['purchase_order'] ?? $item['order_details'] ?? null,

            'location' => $item['location'] ?? null,
            'location_details' => $item['location_details'] ?? null,

            'checked_by' => $item['checked_by'] ?? null,
            'checked_at' => $item['checked_at'] ?? null,

            'updated_by' => $item['updated_by'] ?? null,
            'updated_by_name' => $item['updated_by_name'] ?? null,
            'updated_by_data' => $item['updated_by_data'] ?? null,
            'updated_at' => $item['updated_at'] ?? null,

            'note' => $item['note'] ?? $item['lager_note'] ?? null,
            'lager_note' => $item['lager_note'] ?? null,

            'stock_allocation' => $item['stock_allocation'] ?? null,
            'material_history' => $item['material_history'] ?? [],

            'source' => $source,
        ];
    }

    protected function compareOfferAndMeasurementMaterials(Collection $offerMaterials, Collection $measurementMaterials): Collection
    {
        $oldRows = $offerMaterials->values();
        $newRows = $measurementMaterials->values();

        $usedNewIndexes = [];
        $result = collect();

        foreach ($oldRows as $old) {
            $matchedIndex = null;
            $matchedNew = null;

            foreach ($newRows as $index => $new) {
                if (in_array($index, $usedNewIndexes, true)) {
                    continue;
                }

                if ($this->materialsAreSame($old, $new)) {
                    $matchedIndex = $index;
                    $matchedNew = $new;
                    break;
                }
            }

            if ($matchedNew) {
                $usedNewIndexes[] = $matchedIndex;

                $oldQty = (float) ($old['qty'] ?? 0);
                $newQty = (float) (
                    $matchedNew['qty']
                    ?? $matchedNew['verbrauch_qty']
                    ?? $matchedNew['qty_final']
                    ?? $matchedNew['qty_measurement']
                    ?? 0
                );

                $deltaQty = $newQty - $oldQty;

                $changedFields = [];

                if (abs($deltaQty) > 0.0001) {
                    $changedFields[] = 'qty';
                }

                $oldUnit = $old['unit'] ?? $old['measure'] ?? null;
                $newUnit = $matchedNew['unit'] ?? $matchedNew['measure'] ?? null;

                if ((string) $oldUnit !== (string) $newUnit) {
                    $changedFields[] = 'unit';
                }

                $oldArticle = $this->normalizeMaterialString($old['article_no'] ?? '');
                $newArticle = $this->normalizeMaterialString($matchedNew['article_no'] ?? '');

                if ($oldArticle !== '' && $newArticle !== '' && $oldArticle !== $newArticle) {
                    $changedFields[] = 'article_no';
                }

                $changeType = empty($changedFields) ? 'same' : 'changed';

                $result->push(array_merge($matchedNew, [
                    'old_qty' => $oldQty,
                    'new_qty' => $newQty,
                    'qty' => $newQty,
                    'verbrauch_qty' => $newQty,
                    'plan_qty' => $oldQty,
                    'delta_qty' => $deltaQty,

                    'change_type' => $changeType,
                    'changed_fields' => $changedFields,

                    'old_row' => $old,
                    'new_row' => $matchedNew,
                    'source' => 'compare',
                ]));

                continue;
            }

            $oldQty = (float) ($old['qty'] ?? 0);

            $result->push(array_merge($old, [
                'old_qty' => $oldQty,
                'new_qty' => 0,
                'qty' => 0,
                'verbrauch_qty' => 0,
                'plan_qty' => $oldQty,
                'delta_qty' => -$oldQty,

                'approved' => false,
                'delta_reason' => '',

                'change_type' => 'removed',
                'changed_fields' => ['removed'],

                'old_row' => $old,
                'new_row' => null,
                'source' => 'compare',
            ]));
        }

        foreach ($newRows as $index => $new) {
            if (in_array($index, $usedNewIndexes, true)) {
                continue;
            }

            $newQty = (float) (
                $new['qty']
                ?? $new['verbrauch_qty']
                ?? $new['qty_final']
                ?? $new['qty_measurement']
                ?? 0
            );

            $result->push(array_merge($new, [
                'old_qty' => 0,
                'new_qty' => $newQty,
                'qty' => $newQty,
                'verbrauch_qty' => $newQty,
                'plan_qty' => 0,
                'delta_qty' => $newQty,

                'change_type' => 'added',
                'changed_fields' => ['added'],

                'old_row' => null,
                'new_row' => $new,
                'source' => 'compare',
            ]));
        }

        return $result
            ->sortBy([
                ['change_type', 'asc'],
                ['section_title', 'asc'],
                ['master_set_name', 'asc'],
                ['name', 'asc'],
            ])
            ->values();
    }

    protected function attachInventoryData(Collection $materials): Collection
    {
        $productIds = $materials
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();

        $articleNos = $materials
            ->pluck('article_no')
            ->filter()
            ->map(fn($value) => mb_strtolower(trim((string) $value)))
            ->unique()
            ->values();

        if ($productIds->isEmpty() && $articleNos->isEmpty()) {
            return $materials->map(function (array $row) {
                $row['inventory_found'] = false;
                $row['inventory_qty'] = 0;
                $row['inventory_rows'] = 0;
                $row['inventory_match_type'] = null;
                $row['inventory_missing_qty'] = (float) ($row['qty'] ?? 0);
                $row['inventory_details'] = [];

                if (empty($row['stock_status'])) {
                    $row['stock_status'] = 'bestellen';
                }

                return $row;
            });
        }

        $inventoryRows = Inventory::query()
            ->with(['responsible', 'branch'])
            ->where(function ($query) use ($productIds, $articleNos) {
                if ($productIds->isNotEmpty()) {
                    $query->orWhereIn('product_id', $productIds);
                }

                if ($articleNos->isNotEmpty()) {
                    $query->orWhereIn(DB::raw('LOWER(TRIM(article_no))'), $articleNos);
                }
            })
            ->get();

        $inventoryByProductId = $inventoryRows
            ->filter(fn($row) => !empty($row->product_id))
            ->groupBy('product_id');

        $inventoryByArticleNo = $inventoryRows
            ->filter(fn($row) => !empty($row->article_no))
            ->groupBy(fn($row) => mb_strtolower(trim((string) $row->article_no)));

        return $materials->map(function (array $row) use ($inventoryByProductId, $inventoryByArticleNo) {
            $productId = $row['product_id'] ?? null;
            $articleKey = mb_strtolower(trim((string) ($row['article_no'] ?? '')));

            $matchedRows = collect();
            $matchType = null;

            if ($productId && $inventoryByProductId->has($productId)) {
                $matchedRows = $inventoryByProductId->get($productId);
                $matchType = 'product_id';
            } elseif ($articleKey !== '' && $inventoryByArticleNo->has($articleKey)) {
                $matchedRows = $inventoryByArticleNo->get($articleKey);
                $matchType = 'article_no';
            }

            $inventoryQty = (float) $matchedRows->sum('quantity');

            $requiredQty = (float) (
                $row['required_qty']
                ?? $row['qty']
                ?? $row['new_qty']
                ?? $row['verbrauch_qty']
                ?? 0
            );

            $row['inventory_found'] = $matchedRows->isNotEmpty();
            $row['inventory_qty'] = $inventoryQty;
            $row['inventory_rows'] = $matchedRows->count();
            $row['inventory_match_type'] = $matchType;
            $row['inventory_missing_qty'] = max($requiredQty - $inventoryQty, 0);

            $row['inventory_details'] = $matchedRows->map(function ($inventory) {
                return [
                    'id' => $inventory->id,
                    'quantity' => (float) $inventory->quantity,
                    'location_label' => $inventory->location_label,
                    'branch_name' => $inventory->branch?->name ?? $inventory->branch?->branch_name ?? null,
                    'room_name' => $inventory->room_name,
                    'room_number' => $inventory->room_number,
                    'rack_name' => $inventory->rack_name,
                    'row' => $inventory->row,
                    'column' => $inventory->column,
                    'shelf' => $inventory->shelf,
                    'responsible_name' => trim(
                        ($inventory->responsible?->name ?? '') . ' ' . ($inventory->responsible?->lastname ?? '')
                    ),
                    'serial_no' => $inventory->serial_no,
                    'ean' => $inventory->ean,
                    'manual_no' => $inventory->manual_no,
                    'updated_at' => optional($inventory->updated_at)->format('d.m.Y H:i'),
                ];
            })->values()->all();

            $hasManualStock = array_key_exists('stock_checked', $row) && (bool) $row['stock_checked'];

            $hasStockQty = array_key_exists('stock_qty', $row) && $row['stock_qty'] !== null && $row['stock_qty'] !== '';
            $hasFoundQty = array_key_exists('found_qty', $row) && $row['found_qty'] !== null && $row['found_qty'] !== '';
            $hasOrderQty = array_key_exists('order_qty', $row) && $row['order_qty'] !== null && $row['order_qty'] !== '';

            if (!$hasManualStock && !$hasStockQty) {
                $row['stock_qty'] = $inventoryQty > 0 ? $inventoryQty : null;
            }

            if (!$hasManualStock && !$hasFoundQty) {
                $row['found_qty'] = $row['stock_qty'] ?? null;
            }

            if (!$hasManualStock && !$hasOrderQty) {
                $row['order_qty'] = $row['inventory_missing_qty'] > 0
                    ? $row['inventory_missing_qty']
                    : null;
            }

            if (!$hasManualStock && (empty($row['stock_status']) || $row['stock_status'] === 'unbekannt')) {
                if ($inventoryQty >= $requiredQty && $requiredQty > 0) {
                    $row['stock_status'] = 'lager';
                } elseif ($inventoryQty > 0 && $inventoryQty < $requiredQty) {
                    $row['stock_status'] = 'teilweise';
                } else {
                    $row['stock_status'] = 'bestellen';
                }
            }

            return $row;
        });
    }

    public function updateMaterialStatus(Request $request, OfferDetail $offerDetail): JsonResponse
    {
        $this->authorizeMeasurementWrite($offerDetail); // S-1a Ownership

        $validated = $request->validate([
            'item_key' => ['required', 'string'],
            'source' => ['nullable', 'string', 'in:offer,feinaufmass,compare'],

            'required_qty' => ['nullable', 'numeric', 'min:0'],
            'found_qty' => ['nullable', 'numeric', 'min:0'],
            'found_unit' => ['nullable', 'string', 'max:50'],

            'stock_status' => ['nullable', 'in:lager,bestellen,teilweise,unbekannt'],
            'stock_qty' => ['nullable', 'numeric', 'min:0'],
            'order_qty' => ['nullable', 'numeric', 'min:0'],

            'location_label' => ['nullable', 'string', 'max:255'],
            'room_name' => ['nullable', 'string', 'max:255'],
            'room_number' => ['nullable', 'string', 'max:255'],
            'rack_name' => ['nullable', 'string', 'max:255'],
            'row' => ['nullable', 'string', 'max:255'],
            'column' => ['nullable', 'string', 'max:255'],
            'shelf' => ['nullable', 'string', 'max:255'],

            'note' => ['nullable', 'string'],
        ]);

        $requiredQty = round((float) ($validated['required_qty'] ?? 0), 4);
        $foundQty = round((float) ($validated['found_qty'] ?? ($validated['stock_qty'] ?? 0)), 4);

        if ($requiredQty <= 0 && $foundQty > 0) {
            $requiredQty = $foundQty;
        }

        $missingQty = max($requiredQty - $foundQty, 0);

        if ($requiredQty > 0) {
            if ($foundQty >= $requiredQty) {
                $stockStatus = 'lager';
            } elseif ($foundQty > 0) {
                $stockStatus = 'teilweise';
            } else {
                $stockStatus = 'bestellen';
            }
        } else {
            $stockStatus = $validated['stock_status'] ?? 'unbekannt';
        }

        $savedBy = $this->currentEmployeePayload();

        $locationData = [
            'location_label' => $validated['location_label'] ?? null,
            'room_name' => $validated['room_name'] ?? null,
            'room_number' => $validated['room_number'] ?? null,
            'rack_name' => $validated['rack_name'] ?? null,
            'row' => $validated['row'] ?? null,
            'column' => $validated['column'] ?? null,
            'shelf' => $validated['shelf'] ?? null,
        ];

        $payload = [
            'stock_status' => $stockStatus,
            'stock_checked' => true,

            'required_qty' => $requiredQty,
            'stock_qty' => $foundQty,
            'found_qty' => $foundQty,
            'missing_qty' => $missingQty,
            'order_qty' => $missingQty,
            'found_unit' => $validated['found_unit'] ?? null,

            'location' => $locationData,
            'location_details' => $locationData,

            'checked_by' => $savedBy,
            'checked_at' => now()->toDateTimeString(),

            'updated_by' => $savedBy['employee_id'],
            'updated_by_name' => $savedBy['name'],
            'updated_by_data' => $savedBy,
            'updated_at' => now()->toDateTimeString(),

            'lager_note' => $validated['note'] ?? null,
            'note' => $validated['note'] ?? null,

            'stock_allocation' => [
                'lager' => $foundQty,
                'bestellen' => $missingQty,
                'offen' => 0,
                'final' => 0,
            ],

            'material_history_entry' => [
                'type' => 'lager_check',
                'qty' => $foundQty,
                'missing' => $missingQty,
                'status' => $stockStatus,
                'location' => $locationData,
                'reason' => $validated['note'] ?? 'Lagerprüfung gespeichert',
                'changed_by' => $savedBy,
                'created_at' => now()->toDateTimeString(),
            ],
        ];

        $this->saveMaterialPayloadToAllSources($offerDetail, $validated['item_key'], $payload);

        return response()->json([
            'success' => true,
            'message' => 'Lagerprüfung wurde gespeichert.',
            'data' => [
                'stock_status' => $stockStatus,
                'found_qty' => $foundQty,
                'missing_qty' => $missingQty,
                'order_qty' => $missingQty,
                'updated_by' => $savedBy,
            ],
        ]);
    }

    public function moveMaterialAllocation(Request $request, OfferDetail $offerDetail): JsonResponse
    {
        $this->authorizeMeasurementWrite($offerDetail); // S-1a Ownership

        $validated = $request->validate([
            'item_key' => ['required', 'string'],
            'action' => ['required', 'string', 'in:found_in_lager,move_to_order,reset_allocation'],

            'required_qty' => ['required', 'numeric', 'min:0'],
            'move_qty' => ['nullable', 'numeric', 'min:0'],
            'found_qty' => ['nullable', 'numeric', 'min:0'],
            'found_unit' => ['nullable', 'string', 'max:50'],

            'location_label' => ['nullable', 'string', 'max:255'],
            'room_name' => ['nullable', 'string', 'max:255'],
            'room_number' => ['nullable', 'string', 'max:255'],
            'rack_name' => ['nullable', 'string', 'max:255'],
            'row' => ['nullable', 'string', 'max:255'],
            'column' => ['nullable', 'string', 'max:255'],
            'shelf' => ['nullable', 'string', 'max:255'],

            'note' => ['nullable', 'string'],
        ]);

        $requiredQty = round((float) $validated['required_qty'], 4);
        $foundQty = round((float) ($validated['found_qty'] ?? 0), 4);
        $moveQty = round((float) ($validated['move_qty'] ?? 0), 4);

        $currentLagerQty = round((float) ($request->input('current_lager_qty', $foundQty)), 4);
        $currentOrderQty = round((float) ($request->input('current_order_qty', max($requiredQty - $currentLagerQty, 0))), 4);

        if ($requiredQty <= 0) {
            $requiredQty = max($foundQty, $currentLagerQty, $currentOrderQty, $moveQty);
        }

        $changedBy = $this->currentEmployeePayload();

        $locationData = [
            'location_label' => $validated['location_label'] ?? null,
            'room_name' => $validated['room_name'] ?? null,
            'room_number' => $validated['room_number'] ?? null,
            'rack_name' => $validated['rack_name'] ?? null,
            'row' => $validated['row'] ?? null,
            'column' => $validated['column'] ?? null,
            'shelf' => $validated['shelf'] ?? null,
        ];

        if ($validated['action'] === 'found_in_lager') {
            $lagerQty = min($foundQty > 0 ? $foundQty : $moveQty, $requiredQty);
            $orderQty = max($requiredQty - $lagerQty, 0);

            $from = 'offen';
            $to = 'lager';
            $historyQty = $lagerQty;
        } elseif ($validated['action'] === 'move_to_order') {
            $qtyToMove = $moveQty > 0 ? $moveQty : $currentLagerQty;
            $qtyToMove = min($qtyToMove, $currentLagerQty);

            $lagerQty = max($currentLagerQty - $qtyToMove, 0);
            $orderQty = max($requiredQty - $lagerQty, 0);

            $from = 'lager';
            $to = 'bestellen';
            $historyQty = $qtyToMove;
        } else {
            $lagerQty = 0;
            $orderQty = $requiredQty;

            $from = 'lager';
            $to = 'bestellen';
            $historyQty = $currentLagerQty;
        }

        if ($lagerQty >= $requiredQty && $requiredQty > 0) {
            $stockStatus = 'lager';
        } elseif ($lagerQty > 0 && $lagerQty < $requiredQty) {
            $stockStatus = 'teilweise';
        } else {
            $stockStatus = 'bestellen';
        }

        $allocation = [
            'offen' => 0,
            'lager' => $lagerQty,
            'bestellen' => $orderQty,
            'final' => 0,
        ];

        $historyEntry = [
            'type' => 'allocation_move',
            'action' => $validated['action'],
            'from' => $from,
            'to' => $to,
            'qty' => $historyQty,
            'required_qty' => $requiredQty,
            'lager_qty' => $lagerQty,
            'order_qty' => $orderQty,
            'unit' => $validated['found_unit'] ?? null,
            'location' => $locationData,
            'reason' => $validated['note'] ?? 'Materialstatus geändert',
            'changed_by' => $changedBy,
            'created_at' => now()->toDateTimeString(),
        ];

        $payload = [
            'stock_status' => $stockStatus,
            'stock_checked' => true,

            'required_qty' => $requiredQty,
            'stock_qty' => $lagerQty,
            'found_qty' => $lagerQty,
            'missing_qty' => $orderQty,
            'order_qty' => $orderQty,
            'found_unit' => $validated['found_unit'] ?? null,

            'stock_allocation' => $allocation,

            'location' => $locationData,
            'location_details' => $locationData,

            'checked_by' => $changedBy,
            'checked_at' => now()->toDateTimeString(),

            'updated_by' => $changedBy['employee_id'],
            'updated_by_name' => $changedBy['name'],
            'updated_by_data' => $changedBy,
            'updated_at' => now()->toDateTimeString(),

            'note' => $validated['note'] ?? null,
            'lager_note' => $validated['note'] ?? null,

            'material_history_entry' => $historyEntry,
        ];

        $this->saveMaterialPayloadToAllSources($offerDetail, $validated['item_key'], $payload);

        return response()->json([
            'success' => true,
            'message' => 'Materialbewegung wurde gespeichert.',
            'data' => [
                'stock_status' => $stockStatus,
                'stock_allocation' => $allocation,
                'lager_qty' => $lagerQty,
                'order_qty' => $orderQty,
                'missing_qty' => $orderQty,
                'updated_by' => $changedBy,
            ],
        ]);
    }

    public function updateOrderDetails(Request $request, OfferDetail $offerDetail): JsonResponse
    {
        $this->authorizeMeasurementWrite($offerDetail); // S-1a Ownership

        $validated = $request->validate([
            'item_key' => ['required', 'string'],

            'order_qty' => ['nullable', 'numeric', 'min:0'],
            'order_status' => ['nullable', 'string', 'in:open,ordered,delivered,cancelled,bestellen,bestellt,geliefert,storniert'],

            'source_type' => ['nullable', 'string', 'in:distributor,brand,manual'],
            'distributor_id' => ['nullable', 'integer', 'exists:distributors,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'manual_source_name' => ['nullable', 'string', 'max:255'],

            'ordered_by_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'ordered_at' => ['nullable', 'date'],
            'expected_delivery_at' => ['nullable', 'date'],

            'delivery_target' => ['nullable', 'string', 'in:customer,company,warehouse,firma,lager,kunde'],
            'delivery_address' => ['nullable', 'string', 'max:1000'],

            'order_no' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        $changedBy = $this->currentEmployeePayload();

        $orderedBy = null;

        if (!empty($validated['ordered_by_employee_id'])) {
            $orderedByEmployee = Employee::find((int) $validated['ordered_by_employee_id']);

            if ($orderedByEmployee) {
                $orderedBy = [
                    'employee_id' => $orderedByEmployee->id,
                    'name' => trim(($orderedByEmployee->name ?? '') . ' ' . ($orderedByEmployee->lastname ?? '')),
                    'email' => $orderedByEmployee->email,
                ];
            }
        }

        $sourceType = $validated['source_type'] ?? 'manual';
        $sourceName = $validated['manual_source_name'] ?? null;

        if ($sourceType === 'distributor' && !empty($validated['distributor_id'])) {
            $distributor = Distributor::find((int) $validated['distributor_id']);
            $sourceName = $distributor?->name ?? $distributor?->short_name;
        }

        if ($sourceType === 'brand' && !empty($validated['brand_id'])) {
            $brand = Brand::find((int) $validated['brand_id']);
            $sourceName = $brand?->name;
        }

        $normalizedOrderStatus = match ($validated['order_status'] ?? 'open') {
            'bestellt', 'ordered' => 'ordered',
            'geliefert', 'delivered' => 'delivered',
            'storniert', 'cancelled' => 'cancelled',
            'bestellen', 'open' => 'open',
            default => 'open',
        };

        $orderDetails = [
            'order_qty' => (float) ($validated['order_qty'] ?? 0),
            'order_status' => $normalizedOrderStatus,

            'source_type' => $sourceType,
            'source_name' => $sourceName,
            'distributor_id' => $validated['distributor_id'] ?? null,
            'brand_id' => $validated['brand_id'] ?? null,
            'manual_source_name' => $validated['manual_source_name'] ?? null,

            'ordered_by_employee_id' => $validated['ordered_by_employee_id'] ?? null,
            'ordered_by_name' => $orderedBy['name'] ?? null,
            'ordered_by_data' => $orderedBy,

            'ordered_at' => $validated['ordered_at'] ?? null,
            'expected_delivery_at' => $validated['expected_delivery_at'] ?? null,

            'delivery_target' => $validated['delivery_target'] ?? null,
            'delivery_address' => $validated['delivery_address'] ?? null,

            'order_no' => $validated['order_no'] ?? null,
            'note' => $validated['note'] ?? null,

            'updated_by' => $changedBy['employee_id'],
            'updated_by_name' => $changedBy['name'],
            'updated_by_data' => $changedBy,
            'updated_at' => now()->toDateTimeString(),
        ];

        $payload = [
            'order_qty' => $orderDetails['order_qty'],
            'missing_qty' => $orderDetails['order_qty'],

            'order_status' => $normalizedOrderStatus,
            'purchase_status' => $normalizedOrderStatus,

            'order_details' => $orderDetails,
            'purchase_order' => $orderDetails,

            'note' => $validated['note'] ?? null,
            'lager_note' => $validated['note'] ?? null,

            'updated_by' => $changedBy['employee_id'],
            'updated_by_name' => $changedBy['name'],
            'updated_by_data' => $changedBy,
            'updated_at' => now()->toDateTimeString(),

            'material_history_entry' => [
                'type' => 'order_details_update',
                'order_details' => $orderDetails,
                'changed_by' => $changedBy,
                'created_at' => now()->toDateTimeString(),
            ],
        ];

        $this->saveMaterialPayloadToAllSources($offerDetail, $validated['item_key'], $payload);

        return response()->json([
            'success' => true,
            'message' => 'Bestelldetails wurden gespeichert.',
            'data' => [
                'order_details' => $orderDetails,
            ],
        ]);
    }

    protected function saveMaterialPayloadToAllSources(OfferDetail $offerDetail, string $itemKey, array $payload): void
    {
        DB::transaction(function () use ($offerDetail, $itemKey, $payload) {
            $history = $offerDetail->material_history ?? [];

            if (!is_array($history)) {
                $history = [];
            }

            $oldEntry = $history[$itemKey] ?? [];

            if (!is_array($oldEntry)) {
                $oldEntry = [];
            }

            $historyPayload = $this->payloadForHistory($payload);

            $events = $oldEntry['_events'] ?? [];

            if (!is_array($events)) {
                $events = [];
            }

            if (!empty($payload['material_history_entry']) && is_array($payload['material_history_entry'])) {
                $events[] = $payload['material_history_entry'];
            }

            $history[$itemKey] = array_merge($oldEntry, $historyPayload, [
                '_events' => $events,
            ]);

            $offerDetail->material_history = $history;

            $offerDetail->sections = $this->syncSectionsMaterialStockData(
                sections: $offerDetail->sections ?? [],
                itemKey: $itemKey,
                payload: $payload
            );

            $offerDetail->save();

            $measurement = $this->latestMeasurement($offerDetail);

            if ($measurement) {
                if (is_array($measurement->materials_snapshot) && !empty($measurement->materials_snapshot)) {
                    $measurement->materials_snapshot = $this->syncMeasurementSnapshotStockData(
                        snapshot: $measurement->materials_snapshot,
                        itemKey: $itemKey,
                        payload: $payload
                    );
                }

                if (is_array($measurement->sections_snapshot) && !empty($measurement->sections_snapshot)) {
                    $measurement->sections_snapshot = $this->syncMeasurementSnapshotStockData(
                        snapshot: $measurement->sections_snapshot,
                        itemKey: $itemKey,
                        payload: $payload
                    );
                }

                $this->syncDealMeasurementItemsStockData(
                    measurement: $measurement,
                    itemKey: $itemKey,
                    payload: $payload
                );

                $summary = $measurement->material_summary ?? [];

                if (!is_array($summary)) {
                    $summary = [];
                }

                $summary['last_material_update'] = $payload['material_history_entry'] ?? [
                    'type' => 'material_update',
                    'created_at' => now()->toDateTimeString(),
                ];

                $measurement->material_summary = $summary;
                $measurement->materials_saved_at = now();
                $measurement->save();
            }
        });
    }

    protected function payloadForHistory(array $payload): array
    {
        $blockedKeys = [
            'material_history_entry',
        ];

        $result = [];

        foreach ($payload as $key => $value) {
            if (in_array($key, $blockedKeys, true)) {
                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    protected function syncDealMeasurementItemsStockData(DealMeasurement $measurement, string $itemKey, array $payload): void
    {
        $items = DealMeasurementItem::query()
            ->where('deal_measurement_id', $measurement->id)
            ->get();

        $hasUpdatedByColumn = Schema::hasColumn('deal_measurement_items', 'updated_by');

        foreach ($items as $item) {
            $row = $this->mapDealMeasurementItemToMaterialRow($item);

            if (($row['item_key'] ?? null) !== $itemKey) {
                continue;
            }

            $raw = is_array($item->raw_snapshot) ? $item->raw_snapshot : [];

            foreach ($payload as $key => $value) {
                if ($key === 'material_history_entry') {
                    continue;
                }

                $raw[$key] = $value;
            }

            $history = $raw['material_history'] ?? [];

            if (!is_array($history)) {
                $history = [];
            }

            $history[] = $payload['material_history_entry'] ?? [
                'type' => 'material_update',
                'created_at' => now()->toDateTimeString(),
            ];

            $raw['material_history'] = $history;

            $item->raw_snapshot = $raw;

            if (array_key_exists('order_status', $payload)) {
                $item->order_status = $payload['order_status'];
            } elseif (array_key_exists('stock_status', $payload)) {
                $item->order_status = $payload['stock_status'];
            }

            if (array_key_exists('stock_allocation', $payload)) {
                $item->stock_allocation = $payload['stock_allocation'];
            }

            if (array_key_exists('note', $payload)) {
                $item->note = $payload['note'];
            }

            if (array_key_exists('stock_checked', $payload)) {
                $item->is_checked = (bool) $payload['stock_checked'];
            }

            if ($hasUpdatedByColumn && array_key_exists('updated_by', $payload)) {
                $item->updated_by = $payload['updated_by'];
            }

            $item->save();
        }
    }

    protected function syncSectionsMaterialStockData(array $sections, string $itemKey, array $payload): array
    {
        foreach ($sections as $sectionIndex => $section) {
            if (empty($section['items']) || !is_array($section['items'])) {
                continue;
            }

            foreach ($section['items'] as $itemIndex => $item) {
                if (!is_array($item)) {
                    continue;
                }

                $sections[$sectionIndex]['items'][$itemIndex] = $this->syncSingleItemStockData(
                    item: $item,
                    itemKey: $itemKey,
                    payload: $payload
                );
            }
        }

        return $sections;
    }

    protected function syncMeasurementSnapshotStockData(array $snapshot, string $itemKey, array $payload): array
    {
        foreach ($snapshot as $sectionIndex => $section) {
            if (empty($section['items']) || !is_array($section['items'])) {
                continue;
            }

            foreach ($section['items'] as $itemIndex => $item) {
                if (!is_array($item)) {
                    continue;
                }

                $snapshot[$sectionIndex]['items'][$itemIndex] = $this->syncSingleItemStockData(
                    item: $item,
                    itemKey: $itemKey,
                    payload: $payload
                );
            }
        }

        return $snapshot;
    }

    protected function syncSingleItemStockData(array $item, string $itemKey, array $payload): array
    {
        $currentKey = $this->buildItemKeyFromAnyItem($item);

        if ($currentKey === $itemKey) {
            foreach ($payload as $key => $value) {
                if ($key === 'material_history_entry') {
                    continue;
                }

                $item[$key] = $value;
            }

            $history = $item['material_history'] ?? [];

            if (!is_array($history)) {
                $history = [];
            }

            $history[] = $payload['material_history_entry'] ?? [
                'type' => 'material_update',
                'created_at' => now()->toDateTimeString(),
            ];

            $item['material_history'] = $history;
        }

        if (!empty($item['subItems']) && is_array($item['subItems'])) {
            foreach ($item['subItems'] as $subIndex => $subItem) {
                if (!is_array($subItem)) {
                    continue;
                }

                $item['subItems'][$subIndex] = $this->syncSingleItemStockData(
                    item: $subItem,
                    itemKey: $itemKey,
                    payload: $payload
                );
            }
        }

        return $item;
    }

    public function applyFeinaufmassToOfferDetail(Request $request, OfferDetail $offerDetail): JsonResponse
    {
        $measurement = $this->latestMeasurement($offerDetail);
        if ($measurement) {
            Gate::authorize('write', $measurement); // S-1a Ownership
        }

        if (!$measurement) {
            return response()->json([
                'success' => false,
                'message' => 'Kein Feinaufmaß gefunden.',
            ], 404);
        }

        $feinaufmassMaterials = $this->buildFeinaufmassMaterials($measurement);

        if ($feinaufmassMaterials->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Feinaufmaß-Materialien gefunden.',
            ], 422);
        }

        $oldSections = $offerDetail->sections ?? [];

        if (!is_array($oldSections)) {
            $oldSections = [];
        }

        DB::transaction(function () use ($offerDetail, $measurement, $feinaufmassMaterials, $oldSections) {
            $offerDetail->angebot_snapshot_sections = $oldSections;
            $offerDetail->angebot_snapshot_at = now();

            $offerDetail->sections = $this->convertMaterialRowsToOfferSections($feinaufmassMaterials);

            $offerDetail->document_status = OfferDetail::DOCUMENT_STATUS_AUFTRAG;
            $offerDetail->save();

            $summary = $measurement->material_summary ?? [];

            if (!is_array($summary)) {
                $summary = [];
            }

            $summary['applied_to_offer_detail'] = true;
            $summary['applied_to_offer_detail_at'] = now()->toDateTimeString();
            $summary['applied_to_offer_detail_by'] = [
                'user_id' => auth()->id(),
                'employee_id' => (int) auth()->user()->name,
                'email' => auth()->user()?->email,
            ];
            $summary['applied_source'] = 'merged_feinaufmass_materials';

            $measurement->material_summary = $summary;
            $measurement->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Feinaufmaß-Materialien wurden in den Auftrag übernommen.',
            'source' => 'merged_feinaufmass_materials',
        ]);
    }

    protected function convertMaterialRowsToOfferSections(Collection $materials): array
    {
        return $materials
            ->filter(fn($row) => ($row['change_type'] ?? null) !== 'removed')
            ->groupBy(fn($row) => $row['section_title'] ?: '1. Hauptpositionen')
            ->map(function (Collection $rows, string $sectionTitle) {
                return [
                    'id' => 's' . now()->timestamp . '_' . md5($sectionTitle),
                    'title' => $sectionTitle,
                    'description' => 'Beschreibung',
                    'config' => [
                        'mode' => 'standard',
                        'pauschalPrice' => 0,
                        'type' => 'standard',
                        'hidePrices' => false,
                        'margin' => [
                            'value' => 0,
                            'type' => 'fixed',
                        ],
                        'qty' => 1,
                        'unit' => null,
                    ],
                    'items' => $rows->map(fn($row) => $this->convertMaterialRowToOfferItem($row))->values()->all(),
                    'isLaborSection' => false,
                ];
            })
            ->values()
            ->all();
    }

    protected function convertMaterialRowToOfferItem(array $row): array
    {
        return [
            'item_type' => $row['item_type'] ?? 'article',
            'productId' => $row['product_id'] ?? null,
            'product_id' => $row['product_id'] ?? null,
            'component_id' => $row['component_id'] ?? null,

            'name' => $row['name'] ?? 'Unbekanntes Material',
            'desc_html' => $row['description'] ?? $row['desc_html'] ?? $row['desc'] ?? null,
            'desc' => $row['description'] ?? $row['desc'] ?? $row['desc_html'] ?? null,
            'img' => $row['img'] ?? null,
            'showImage' => true,

            'price' => (float) ($row['price'] ?? 0),
            'ek' => (float) ($row['ek'] ?? 0),
            'purchase_price' => (float) ($row['ek'] ?? 0),

            'rate' => 0,
            'margin' => 0,
            'marginPercent' => 0,

            'supplier' => $row['supplier_name'] ?? $row['distributor_name'] ?? null,
            'distributor_name' => $row['distributor_name'] ?? null,
            'distributor_article_no' => $row['distributor_article_no'] ?? null,
            'distributor_id' => $row['distributor_id'] ?? null,
            'distributor_price_id' => null,

            'skonto' => 0,
            'payment_terms' => 0,
            'availability' => (bool) ($row['availability'] ?? false),

            'componentType' => 'haupt',
            'kind' => 'article',
            'lineType' => 'standard',
            'status' => 'normal',

            'is_stammartikel' => false,
            'is_favorite' => false,

            'qty' => (float) ($row['verbrauch_qty'] ?? $row['qty'] ?? 0),
            'unit' => $row['unit'] ?? 'Stk',
            'measure' => $row['measure'] ?? $row['unit'] ?? 'Stk',

            'price_unit_value' => 1,
            'price_unit_label' => $row['unit'] ?? 'Stk.',
            'price_unit_text' => '1 ' . ($row['unit'] ?? 'Stk.'),
            'vpe' => 1,

            'active' => true,
            'hidePrices' => false,
            'hideImage' => false,
            'hideNumbering' => false,
            'isPauschal' => false,
            'print_hidden' => false,
            'print_hidden_labor' => true,

            'creator_id' => null,
            'creator_name' => null,
            'count_copy' => 0,
            'count_offer' => 0,
            'is_locked' => 1,

            'article_no' => $row['article_no'] ?? null,

            'approved' => (bool) ($row['approved'] ?? false),
            'delta_reason' => $row['delta_reason'] ?? null,
            'plan_qty' => $row['plan_qty'] ?? null,
            'verbrauch_qty' => $row['verbrauch_qty'] ?? null,
            'delta_qty' => $row['delta_qty'] ?? null,

            'stock_checked' => (bool) ($row['stock_checked'] ?? false),
            'required_qty' => $row['required_qty'] ?? null,
            'stock_qty' => $row['stock_qty'] ?? null,
            'found_qty' => $row['found_qty'] ?? null,
            'missing_qty' => $row['missing_qty'] ?? null,
            'order_qty' => $row['order_qty'] ?? null,
            'found_unit' => $row['found_unit'] ?? null,
            'stock_status' => $row['stock_status'] ?? null,
            'stock_allocation' => $row['stock_allocation'] ?? null,

            'order_status' => $row['order_status'] ?? null,
            'purchase_status' => $row['purchase_status'] ?? null,
            'order_details' => $row['order_details'] ?? null,
            'purchase_order' => $row['purchase_order'] ?? null,

            'location' => $row['location'] ?? null,
            'location_details' => $row['location_details'] ?? null,
            'checked_by' => $row['checked_by'] ?? null,
            'checked_at' => $row['checked_at'] ?? null,
            'updated_by' => $row['updated_by'] ?? null,
            'updated_by_name' => $row['updated_by_name'] ?? null,
            'updated_by_data' => $row['updated_by_data'] ?? null,
            'updated_at' => $row['updated_at'] ?? null,
            'lager_note' => $row['lager_note'] ?? null,
            'note' => $row['note'] ?? $row['lager_note'] ?? null,
            'material_history' => $row['material_history'] ?? [],

            'subItems' => [],
        ];
    }

    protected function filterMaterials(Collection $materials, Request $request): Collection
    {
        $search = trim((string) $request->get('search', ''));
        $supplier = trim((string) $request->get('supplier', ''));
        $status = trim((string) $request->get('status', ''));
        $onlyOrder = (string) $request->get('only_order', '');
        $changeType = trim((string) $request->get('change_type', ''));

        return $materials->filter(function (array $row) use ($search, $supplier, $status, $onlyOrder, $changeType) {
            if ($search !== '') {
                $haystack = mb_strtolower(implode(' ', array_filter([
                    $row['name'] ?? '',
                    $row['article_no'] ?? '',
                    $row['distributor_article_no'] ?? '',
                    $row['distributor_name'] ?? '',
                    $row['supplier_name'] ?? '',
                    $row['brand_name'] ?? '',
                    $row['master_set_name'] ?? '',
                    $row['section_title'] ?? '',
                    $row['change_type'] ?? '',
                    $row['delta_reason'] ?? '',
                    $row['note'] ?? '',
                    $row['lager_note'] ?? '',
                    data_get($row, 'order_details.source_name', ''),
                    data_get($row, 'order_details.order_no', ''),
                    data_get($row, 'order_details.ordered_by_name', ''),
                    data_get($row, 'order_details.delivery_address', ''),
                    $row['source'] ?? '',
                ])));

                if (!str_contains($haystack, mb_strtolower($search))) {
                    return false;
                }
            }

            if ($supplier !== '' && ($row['distributor_name'] ?? '') !== $supplier) {
                return false;
            }

            if ($status !== '' && ($row['stock_status'] ?? 'unbekannt') !== $status) {
                return false;
            }

            if ($onlyOrder === '1' && !in_array(($row['stock_status'] ?? 'unbekannt'), ['bestellen', 'teilweise'], true)) {
                return false;
            }

            if ($changeType !== '' && ($row['change_type'] ?? '') !== $changeType) {
                return false;
            }

            return true;
        })->values();
    }

    protected function resolveMeasurementMaterialSource(?DealMeasurement $measurement): array
    {
        if (!$measurement) {
            return [
                'snapshot' => [],
                'source' => null,
                'reason' => 'no_measurement',
            ];
        }

        $itemsCount = DealMeasurementItem::query()
            ->where('deal_measurement_id', $measurement->id)
            ->count();

        if ($itemsCount > 0) {
            return [
                'snapshot' => [],
                'source' => 'deal_measurement_items',
                'reason' => 'deal_measurement_items_available',
            ];
        }

        $materialsSnapshot = is_array($measurement->materials_snapshot)
            ? $measurement->materials_snapshot
            : [];

        $sectionsSnapshot = is_array($measurement->sections_snapshot)
            ? $measurement->sections_snapshot
            : [];

        $hasMaterials = !empty($materialsSnapshot);
        $hasSections = !empty($sectionsSnapshot);

        if (!$hasMaterials && !$hasSections) {
            return [
                'snapshot' => [],
                'source' => null,
                'reason' => 'empty',
            ];
        }

        if ($hasMaterials && $hasSections) {
            if (
                $measurement->materials_saved_at
                && $measurement->updated_at
                && $measurement->updated_at->gt($measurement->materials_saved_at)
            ) {
                return [
                    'snapshot' => $sectionsSnapshot,
                    'source' => 'sections_snapshot',
                    'reason' => 'sections_newer_than_materials_snapshot',
                ];
            }

            return [
                'snapshot' => $materialsSnapshot,
                'source' => 'materials_snapshot',
                'reason' => 'materials_snapshot_available',
            ];
        }

        if ($hasSections) {
            return [
                'snapshot' => $sectionsSnapshot,
                'source' => 'sections_snapshot',
                'reason' => 'only_sections_snapshot_available',
            ];
        }

        return [
            'snapshot' => $materialsSnapshot,
            'source' => 'materials_snapshot',
            'reason' => 'only_materials_snapshot_available',
        ];
    }

    protected function materialsAreSame(array $old, array $new): bool
    {
        if (
            !empty($old['item_key'])
            && !empty($new['item_key'])
            && (string) $old['item_key'] === (string) $new['item_key']
        ) {
            return true;
        }

        $oldArticle = $this->normalizeMaterialString($old['article_no'] ?? '');
        $newArticle = $this->normalizeMaterialString($new['article_no'] ?? '');

        if ($oldArticle !== '' && $newArticle !== '' && $oldArticle === $newArticle) {
            return true;
        }

        $oldDistributorArticle = $this->normalizeMaterialString($old['distributor_article_no'] ?? '');
        $newDistributorArticle = $this->normalizeMaterialString($new['distributor_article_no'] ?? '');

        if (
            $oldDistributorArticle !== ''
            && $newDistributorArticle !== ''
            && $oldDistributorArticle === $newDistributorArticle
        ) {
            return true;
        }

        if (
            !empty($old['product_id'])
            && !empty($new['product_id'])
            && (int) $old['product_id'] === (int) $new['product_id']
        ) {
            return true;
        }

        if (
            !empty($old['component_id'])
            && !empty($new['component_id'])
            && (int) $old['component_id'] === (int) $new['component_id']
        ) {
            return true;
        }

        $oldName = $this->normalizeMaterialString($old['name'] ?? '');
        $newName = $this->normalizeMaterialString($new['name'] ?? '');

        return $oldName !== '' && $newName !== '' && $oldName === $newName;
    }

    protected function bestMaterialGroupKey(array $row): string
    {
        $articleNo = $this->normalizeMaterialString($row['article_no'] ?? '');

        if ($articleNo !== '') {
            return 'article:' . $articleNo;
        }

        $distributorArticleNo = $this->normalizeMaterialString($row['distributor_article_no'] ?? '');

        if ($distributorArticleNo !== '') {
            return 'dist_article:' . $distributorArticleNo;
        }

        if (!empty($row['product_id'])) {
            return 'product:' . (int) $row['product_id'];
        }

        if (!empty($row['component_id'])) {
            return 'component:' . (int) $row['component_id'];
        }

        $name = $this->normalizeMaterialString($row['name'] ?? '');

        if ($name !== '') {
            return 'name:' . md5($name);
        }

        return 'unknown:' . md5(json_encode($row));
    }

    protected function normalizeMaterialString(?string $value): string
    {
        $value = mb_strtolower(trim((string) $value));

        return str_replace([' ', '-', '_', '.', '/', '\\', '"', "'"], '', $value);
    }

    protected function buildItemKeyFromAnyItem(array $item): string
    {
        $articleNo = trim((string) (
            $item['article_no']
            ?? $item['distributor_article_no']
            ?? $item['distributor_no']
            ?? ''
        ));

        $componentId = $item['component_id']
            ?? $item['master_set_component_id']
            ?? null;

        if (!$componentId && !empty($item['id']) && is_numeric($item['id'])) {
            $componentId = (int) $item['id'];
        }

        $productId = $item['product_id']
            ?? $item['productId']
            ?? null;

        $name = trim((string) ($item['name'] ?? 'Unbekanntes Material'));

        return $this->buildItemKey(
            articleNo: $articleNo,
            componentId: $componentId,
            productId: $productId,
            name: $name
        );
    }

    protected function buildItemKey(?string $articleNo, $componentId, $productId, string $name): string
    {
        if (!empty($articleNo)) {
            return 'article:' . mb_strtolower(trim($articleNo));
        }

        if (!empty($componentId)) {
            return 'component:' . $componentId;
        }

        if (!empty($productId)) {
            return 'product:' . $productId;
        }

        return 'name:' . md5(mb_strtolower(trim($name)));
    }

    protected function materialQtyFromArrayItem(array $item, string $source): float
    {
        if ($source === 'offer') {
            return (float) ($item['qty'] ?? 0);
        }

        foreach ([
            'verbrauch_qty',
            'qty_final',
            'qty_measurement',
            'measurement_qty',
            'new_qty',
        ] as $field) {
            if (
                array_key_exists($field, $item)
                && $item[$field] !== null
                && $item[$field] !== ''
            ) {
                return (float) $item[$field];
            }
        }

        return (float) ($item['qty'] ?? 0);
    }
    protected function planQtyFromArrayItem(array $item): float
    {
        return (float) (
            $item['plan_qty']
            ?? $item['qty_offer']
            ?? $item['offer_qty']
            ?? $item['qty']
            ?? 0
        );
    }

    protected function materialQtyFromMeasurementItem(DealMeasurementItem $item, array $raw): float
    {
        /*
        |--------------------------------------------------------------------------
        | qty_final has highest priority.
        | Important: 0 is a valid value. Do NOT skip 0.
        |--------------------------------------------------------------------------
        */
        $fields = [
            $item->qty_final,
            $item->qty_measurement,
            $raw['qty_final'] ?? null,
            $raw['qty_measurement'] ?? null,
            $raw['measurement_qty'] ?? null,
            $raw['verbrauch_qty'] ?? null,
            $raw['new_qty'] ?? null,
        ];

        foreach ($fields as $value) {
            if ($value !== null && $value !== '') {
                return (float) $value;
            }
        }

        return (float) (
            $raw['qty']
            ?? $item->qty_offer
            ?? 0
        );
    }

    protected function offerQtyFromMeasurementItem(DealMeasurementItem $item, array $raw): float
    {
        foreach ([
            $item->qty_offer,
            $raw['plan_qty'] ?? null,
            $raw['qty_offer'] ?? null,
            $raw['offer_qty'] ?? null,
            $raw['original_qty'] ?? null,
            $raw['qty'] ?? null,
        ] as $value) {
            if ($value !== null && $value !== '') {
                return (float) $value;
            }
        }

        return 0.0;
    }

    protected function buildMaterialDocumentTitle(OfferDetail $offerDetail, ?DealMeasurement $measurement = null): string
    {
        $offer = $offerDetail->offer;
        $customer = $offer?->customer;

        $customerName = trim(
            ($customer?->firma ?? '') . ' ' .
            ($customer?->name ?? '') . ' ' .
            ($customer?->lastname ?? '')
        );

        $documentNo = $measurement?->measurement_no
            ?? $offerDetail->offer_no
            ?? ('#' . $offerDetail->id);

        return 'Materialliste ' . $documentNo . ($customerName !== '' ? ' · ' . $customerName : '');
    }

    protected function buildMaterialDocumentSubtitle(OfferDetail $offerDetail, ?DealMeasurement $measurement = null): string
    {
        $offer = $offerDetail->offer;
        $alternative = $offer?->alternative;
        $product = $offer?->productGroup ?? $offer?->product ?? null;

        $parts = [];

        if (!empty($offerDetail->offer_no)) {
            $parts[] = 'Angebot ' . $offerDetail->offer_no;
        }

        if (!empty($measurement?->measurement_no)) {
            $parts[] = 'Feinaufmaß ' . $measurement->measurement_no;
        }

        $address = trim(
            ($alternative?->object_name ?? '') . ' ' .
            ($alternative?->street ?? '') . ' ' .
            ($alternative?->postcode ?? '') . ' ' .
            ($alternative?->city ?? '')
        );

        if ($address !== '') {
            $parts[] = $address;
        }

        $productName = $product?->article_group
            ?? $product?->name
            ?? $product?->product
            ?? $product?->title
            ?? null;

        if (!empty($productName)) {
            $parts[] = $productName;
        }

        return implode(' · ', array_filter($parts));
    }

    protected function currentEmployeePayload(): array
    {
        $employeeId = (int) auth()->user()->name;
        $employee = Employee::find($employeeId);

        return [
            'user_id' => auth()->id(),
            'employee_id' => $employeeId,
            'name' => trim(($employee?->name ?? '') . ' ' . ($employee?->lastname ?? '')) ?: auth()->user()?->name,
            'email' => auth()->user()?->email,
        ];
    }
}