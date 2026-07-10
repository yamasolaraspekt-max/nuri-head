<?php

namespace App\Http\Controllers\Customer\Offer;
use App\Http\Controllers\Controller;

use App\Models\OfferDetail;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\OfferFolder;
use App\Models\OfferEmployeeList;
use App\Models\OfferAssetList;
use App\Models\OfferProductList;
use App\Models\ProductMasterSet;
use Illuminate\Support\Facades\Schema;

class OfferDetailsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,update')->only(['update', 'updateAssets', 'updateEmployees', 'updateProduct']);
    }





    /*
    |--------------------------------------------------------------------------
    | Legacy row calculation helpers
    |--------------------------------------------------------------------------
    | Proof marker: OFFER_DETAILS_CALC_FIX_V2
    */
    protected function moneyValue($value, float $default = 0.0): float
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $value = trim($value);
            $value = str_replace(["\xc2\xa0", '€', 'EUR', ' '], '', $value);

            if (str_contains($value, ',') && str_contains($value, '.')) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } elseif (str_contains($value, ',')) {
                $value = str_replace(',', '.', $value);
            }

            return is_numeric($value) ? (float) $value : $default;
        }

        return $default;
    }

    protected function positiveUnitValue($value): float
    {
        $unit = $this->moneyValue($value, 1.0);
        return $unit > 0 ? $unit : 1.0;
    }

    protected function lineNetFromRow(array $row): float
    {
        $qty = $this->moneyValue($row['quantity'] ?? $row['qty'] ?? 1, 1.0);
        $unitPrice = $this->moneyValue($row['unit_price'] ?? $row['price'] ?? $row['sale_price'] ?? 0, 0.0);
        $priceUnit = $this->positiveUnitValue($row['price_unit_value'] ?? 1);
        $discountPct = max(0.0, min(100.0, $this->moneyValue($row['discount_pct'] ?? 0, 0.0)));

        return round(($qty / $priceUnit) * $unitPrice * (1 - ($discountPct / 100)), 2);
    }

    protected function lineCostFromRow(array $row): float
    {
        $qty = $this->moneyValue($row['quantity'] ?? $row['qty'] ?? 1, 1.0);
        $cost = $this->moneyValue($row['cost'] ?? $row['ek'] ?? $row['buying_price'] ?? 0, 0.0);
        $priceUnit = $this->positiveUnitValue($row['cost_price_unit_value'] ?? $row['price_unit_value'] ?? 1);

        return round(($qty / $priceUnit) * $cost, 2);
    }

    protected function putIfColumnExists(array $payload, string $table, string $column, $value): array
    {
        if (Schema::hasColumn($table, $column)) {
            $payload[$column] = $value;
        }

        return $payload;
    }

    public function update(Request $request, Offer $offer, OfferFolder $folder)
    {
        \Log::info('OfferDetail update payload', $request->all());

        // --- 1) Update OfferDetail meta ---
        $detail = OfferDetail::firstOrCreate([
            'offer_id' => $offer->id,
            'folder_id' => $folder->id,
        ]);

        $data = $request->only($detail->getFillable());

        // Delta automatisch berechnen
        if ($request->has(['vl', 'rl'])) {
            $data['delta'] = $request->vl - $request->rl;
        }

        $detail->fill($data)->save();

        // --- 2) Update Offer Product List if provided ---
        if ($request->has('items') && is_array($request->items)) {
            $items = $request->validate([
                'items' => ['array'],
                'items.*.sku' => ['nullable', 'string'],
                'items.*.name' => ['required', 'string'],
                'items.*.type' => ['nullable', 'in:Material,Lohn,Tools,Service,Logistik,Fremdleistung'],
                'items.*.master_set_id' => ['nullable', 'integer'],
                'items.*.notes' => ['nullable', 'string'],
                'items.*.quantity' => ['required', 'numeric', 'min:0'],
                'items.*.unit_price' => ['required', 'numeric', 'min:0'],
                'items.*.price_unit_value' => ['nullable', 'numeric', 'min:0.0001'],
                'items.*.discount_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'items.*.cost' => ['nullable', 'numeric', 'min:0'],
                'items.*.cost_price_unit_value' => ['nullable', 'numeric', 'min:0.0001'],
                'items.*.measure_unit' => ['nullable', 'string'],   // 👈 added
                'items.*.image_name' => ['nullable', 'string'],
            ])['items'];

            // wipe old rows for this folder
            $folder->offerProductLists()->delete();

            foreach ($items as $i => $row) {
                $lineNet = $this->lineNetFromRow($row);
                $lineCost = $this->lineCostFromRow($row);

                $payload = [
                    'offer_id' => $offer->id,
                    'master_set_id' => $row['master_set_id'] ?? null,
                    'sku' => $row['sku'] ?? null,
                    'name' => $row['name'],
                    'type' => $row['type'] ?? 'Material',
                    'notes' => $row['notes'] ?? '',
                    'quantity' => $this->moneyValue($row['quantity'] ?? 1, 1.0),
                    'unit_price' => $this->moneyValue($row['unit_price'] ?? 0, 0.0),
                    'line_net' => $lineNet,
                    'cost' => $this->moneyValue($row['cost'] ?? 0, 0.0),
                    'sort_order' => $i,
                ];

                $payload = $this->putIfColumnExists($payload, 'offer_product_lists', 'price_unit_value', $this->positiveUnitValue($row['price_unit_value'] ?? 1));
                $payload = $this->putIfColumnExists($payload, 'offer_product_lists', 'cost_price_unit_value', $this->positiveUnitValue($row['cost_price_unit_value'] ?? $row['price_unit_value'] ?? 1));
                $payload = $this->putIfColumnExists($payload, 'offer_product_lists', 'discount_pct', $this->moneyValue($row['discount_pct'] ?? 0, 0.0));
                $payload = $this->putIfColumnExists($payload, 'offer_product_lists', 'line_cost', $lineCost);
                $payload = $this->putIfColumnExists($payload, 'offer_product_lists', 'measure_unit', $row['measure_unit'] ?? null);
                $payload = $this->putIfColumnExists($payload, 'offer_product_lists', 'image_name', $row['image_name'] ?? null);

                $folder->offerProductLists()->create($payload);
            }
        }

        // --- 3) Update Offer Employee List if provided ---
        if ($request->has('employees') && is_array($request->employees)) {
            $rows = $request->validate([
                'employees' => ['array'],
                'employees.*.position_id' => ['nullable', 'integer', 'exists:positions,id'],
                'employees.*.role' => ['nullable', 'string'],
                'employees.*.rate' => ['numeric'],
                'employees.*.qty' => ['numeric', 'min:0'],
                'employees.*.hours_per_person' => ['numeric', 'min:0'],
                'employees.*.hours_total' => ['numeric', 'min:0'],
                'employees.*.sum_total' => ['numeric'],
                'employees.*.master_set_id' => ['nullable', 'integer', 'exists:product_master_sets,id'],
            ])['employees'];

            \App\Models\OfferEmployeeList::where('offer_id', $offer->id)
                ->where('offer_folder_id', $folder->id)
                ->delete();

            foreach ($rows as $row) {
                $positionId = $row['position_id'];

                // Auto-create new position if user typed in new role
                if (!$positionId && !empty($row['role'])) {
                    $pos = \App\Models\Position::create([
                        'position' => $row['role'],
                        'status' => 'Published',
                    ]);
                    $positionId = $pos->id;
                }

                \App\Models\OfferEmployeeList::create([
                    'offer_id' => $offer->id,
                    'offer_folder_id' => $folder->id,
                    'master_set_id' => $row['master_set_id'] ?? null,
                    'position_id' => $positionId,
                    'role' => $row['role'] ?? null,
                    'rate' => $row['rate'] ?? 0,
                    'qty' => $row['qty'] ?? 1,
                    'hours_per_person' => $row['hours_per_person'] ?? 0,
                    'hours_total' => $row['hours_total'] ?? 0,
                    'sum_total' => $row['sum_total'] ?? 0,
                ]);
            }
        }

        return response()->json([
            'detail' => $detail,
            'ok' => true,
        ]);
    }


    public function loadProducts(Offer $offer, OfferFolder $folder)
    {
        $items = $folder->offerProductLists()
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'items' => $items,
        ]);
    }


    public function updates(Request $request, Offer $offer, $folder_id)
    {

        \Log::info('requested file:', [$request->all()]);
        $detail = OfferDetail::firstOrCreate(
            ['offer_id' => $offer->id, 'folder_id' => $folder_id],
            []
        );

        // Validierung – du kannst rules pro Tab differenzieren
        $data = $request->validate([
            // Step A
            'property_type' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:16',
            'city' => 'nullable|string|max:120',
            'year_built' => 'nullable|integer',
            'insulation' => 'nullable|string|max:255',
            'area_m2' => 'nullable|numeric',
            'ceiling_m' => 'nullable|numeric',
            'occupants' => 'nullable|integer',

            // Step B
            'current_source' => 'nullable|string|max:255',
            'emitters' => 'nullable|string|max:255',
            'lowest_flow_c' => 'nullable|numeric',
            'dhw_cyl' => 'nullable|integer',

            // Step C
            'annual_kwh' => 'nullable|integer',
            'age_years' => 'nullable|integer',
            'heating_tech' => 'nullable|string|max:255',
            'persons' => 'nullable|integer',
            'dhw_per_person' => 'nullable|integer',

            // Step D
            'vl' => 'nullable|numeric',
            'rl' => 'nullable|numeric',
            'distribution' => 'nullable|string|max:255',
            'nat' => 'nullable|integer',
            'biv' => 'nullable|numeric',
            'ti' => 'nullable|numeric',
            'wp_share_pct' => 'nullable|numeric',
            'flh' => 'nullable|integer',
            'peak_factor' => 'nullable|numeric',
            'dist_mult' => 'nullable|numeric',
            'vl_mult' => 'nullable|numeric',
        ]);

        // Delta automatisch setzen
        if (isset($data['vl'], $data['rl'])) {
            $data['delta'] = $data['vl'] - $data['rl'];
        }

        $detail->fill($data)->save();

        return response()->json([
            'ok' => true,
            'detail' => $detail->fresh(),
        ]);
    }

    public function masterSetDetails($masterSetId)
    {
        $set = ProductMasterSet::with([
            'employees.position',
            'assets.asset',
        ])->findOrFail($masterSetId);

        return response()->json([
            'id' => $set->id,
            'name' => $set->setname,
            'totals' => [
                'material' => $set->material_price,
                'lohn' => $set->employee_price,
                'tools' => $set->asset_price,
            ],
            'positions' => $set->employees->map(function ($e) {
                return [
                    'id' => $e->id,
                    'position' => optional($e->position)->position,
                    'hours' => $e->work_hour,
                    'buying' => $e->buying_price,
                    'sale' => $e->sale_price,
                    'total' => $e->total,
                ];
            }),
            'assets' => $set->assets->map(function ($a) {
                return [
                    'id' => $a->id,
                    'name' => $a->name ?? optional($a->asset)->item,
                    'model' => optional($a->asset)->model,
                    'count' => $a->count,
                    'price' => $a->total_price,
                ];
            }),
        ]);
    }


    public function loadEmployeeList(Offer $offer, OfferFolder $folder)
    {
        // First: check if we already saved employees for this offer+folder
        $existing = \App\Models\OfferEmployeeList::with('position')
            ->where('offer_id', $offer->id)
            ->where('offer_folder_id', $folder->id)
            ->get();

        if ($existing->count() > 0) {
            return response()->json(['employees' => $existing]);
        }

        // Otherwise: auto-bootstrap from MasterSet(s) linked in product list
        $masterSetIds = $folder->productLists()->pluck('master_set_id')->filter()->unique();

        if ($masterSetIds->isEmpty()) {
            return response()->json(['employees' => []]); // no sets, nothing to show
        }

        $seed = \App\Models\EmployeeSet::with('position')
            ->whereIn('master_set_id', $masterSetIds)
            ->get();

        // Map into same shape as OfferEmployeeList rows
        $rows = $seed->map(function ($e) use ($offer, $folder) {
            return [
                'id' => null,
                'offer_id' => $offer->id,
                'offer_folder_id' => $folder->id,
                'master_set_id' => $e->master_set_id,
                'position_id' => $e->position_id,
                'role' => optional($e->position)->position,
                'rate' => $e->buying_price ?? 0,
                'qty' => 1,
                'hours_per_person' => $e->work_hour ?? 0,
                'hours_total' => $e->work_hour ?? 0,
                'sum_total' => $e->total ?? 0,
            ];
        });

        return response()->json(['employees' => $rows]);
    }


    public function saveEmployeeList(Request $request, Offer $offer, OfferFolder $folder)
    {
        $data = $request->validate([
            'rows' => ['required', 'array'],
            'rows.*.position_id' => ['nullable', 'integer', 'exists:positions,id'],
            'rows.*.role' => ['nullable', 'string'],
            'rows.*.rate' => ['numeric'],
            'rows.*.qty' => ['numeric', 'min:0'],
            'rows.*.hours_per_person' => ['numeric', 'min:0'],
            'rows.*.hours_total' => ['numeric', 'min:0'],
            'rows.*.sum_total' => ['numeric'],
            'rows.*.master_set_id' => ['nullable', 'integer', 'exists:product_master_sets,id'],
        ]);

        // wipe & insert
        OfferEmployeeList::where('offer_id', $offer->id)
            ->where('offer_folder_id', $folder->id)
            ->delete();

        foreach ($data['rows'] as $row) {
            OfferEmployeeList::create(array_merge($row, [
                'offer_id' => $offer->id,
                'offer_folder_id' => $folder->id,
            ]));
        }

        return response()->json(['ok' => true]);
    }


    public function updateEmployees(Request $request, Offer $offer, OfferFolder $folder)
    {
        $data = $request->validate([
            'rows' => ['required', 'array'],
            'rows.*.position_id' => ['nullable', 'integer', 'exists:positions,id'],
            'rows.*.role' => ['required', 'string'],
            'rows.*.rate' => ['required', 'numeric'],
            'rows.*.qty' => ['required', 'numeric', 'min:0'],
            'rows.*.hours_per_person' => ['required', 'numeric'],
            'rows.*.hours_total' => ['nullable', 'numeric'],
            'rows.*.sum_total' => ['nullable', 'numeric'],
            'rows.*.master_set_id' => ['nullable', 'integer', 'exists:product_master_sets,id'],
        ]);

        // wipe old list for this offer+folder
        \App\Models\OfferEmployeeList::where('offer_id', $offer->id)
            ->where('offer_folder_id', $folder->id)
            ->delete();

        // insert new rows
        foreach ($data['rows'] as $i => $row) {
            \App\Models\OfferEmployeeList::create([
                'offer_id' => $offer->id,
                'offer_folder_id' => $folder->id,
                'master_set_id' => $row['master_set_id'] ?? null,
                'position_id' => $row['position_id'],
                'role' => $row['role'],
                'rate' => $row['rate'],
                'qty' => $row['qty'],
                'hours_per_person' => $row['hours_per_person'],
                'hours_total' => $row['hours_total'] ?? ($row['qty'] * $row['hours_per_person']),
                'sum_total' => $row['sum_total'] ?? ($row['rate'] * $row['qty'] * $row['hours_per_person']),
                'sort_order' => $i,
            ]);
        }

        return response()->json(['ok' => true]);
    }


    public function loadAssets(Offer $offer, OfferFolder $folder)
    {
        // 1) Already saved offer assets
        $saved = \App\Models\OfferAssetList::where('offer_id', $offer->id)
            ->where('offer_folder_id', $folder->id)
            ->get();

        // 2) If nothing saved yet → fallback to master_set assets
        if ($saved->isEmpty()) {
            // Find master_set_ids from product list
            $masterIds = \App\Models\OfferProductList::where('offer_id', $offer->id)
                ->where('offer_folder_id', $folder->id)
                ->whereNotNull('master_set_id')
                ->pluck('master_set_id')
                ->unique()
                ->all();

            $assets = [];
            if (!empty($masterIds)) {
                $assets = \App\Models\AssetSet::with('asset')
                    ->whereIn('master_id', $masterIds)
                    ->get()
                    ->map(function ($row) {
                        return [
                            'asset_id' => $row->asset_id,
                            'name' => $row->name ?? optional($row->asset)->item,
                            'rate' => optional($row->asset)->purchase_price ?? 0,
                            'qty' => $row->count ?? 1,
                            'sum_total' => $row->total_price ?? 0,
                            'master_set_id' => $row->master_id,
                        ];
                    });
            }

            return response()->json([
                'assets' => $assets,
                'source' => 'master',
            ]);
        }

        return response()->json([
            'assets' => $saved,
            'source' => 'saved',
        ]);
    }

    public function updateAssets(Request $request, Offer $offer, OfferFolder $folder)
    {
        $data = $request->validate([
            'rows' => ['required', 'array'],
            'rows.*.asset_id' => ['nullable', 'integer', 'exists:assets,id'],
            'rows.*.name' => ['required', 'string'],
            'rows.*.rate' => ['required', 'numeric'],
            'rows.*.qty' => ['required', 'numeric', 'min:0'],
            'rows.*.sum_total' => ['nullable', 'numeric'],
            'rows.*.master_set_id' => ['nullable', 'integer', 'exists:product_master_sets,id'],
        ]);

        OfferAssetList::where('offer_id', $offer->id)
            ->where('offer_folder_id', $folder->id)
            ->delete();

        foreach ($data['rows'] as $i => $row) {
            OfferAssetList::create([
                'offer_id' => $offer->id,
                'offer_folder_id' => $folder->id,
                'master_set_id' => $row['master_set_id'] ?? null,
                'asset_id' => $row['asset_id'] ?? null,
                'name' => $row['name'],
                'rate' => $row['rate'],
                'qty' => $row['qty'],
                'sum_total' => $row['sum_total'] ?? ($row['rate'] * $row['qty']),
                'sort_order' => $i,
            ]);
        }

        return response()->json(['ok' => true]);
    }


    public function loadProductList(Offer $offer, OfferFolder $folder)
    {
        $products = \App\Models\OfferProductList::query()
            ->where('offer_id', $offer->id)
            ->where('offer_folder_id', $folder->id)
            ->where('type', 'Material')   // only Material
            ->get();

        return response()->json($products);
    }

    public function updateProduct(Request $request, Offer $offer, OfferFolder $folder, OfferProductList $productList)
    {
        $data = $request->validate([
            'quantity' => 'sometimes|numeric|min:0',
            'measure_unit' => 'nullable|string',
            'unit_price' => 'sometimes|numeric|min:0',
            'price_unit_value' => 'nullable|numeric|min:0.0001',
            'discount_pct' => 'nullable|numeric|min:0|max:100',
            'cost' => 'nullable|numeric|min:0',
            'cost_price_unit_value' => 'nullable|numeric|min:0.0001',
            'image_name' => 'nullable|string',
        ]);

        $row = array_merge($productList->toArray(), $data);
        $data['line_net'] = $this->lineNetFromRow($row);

        if (Schema::hasColumn('offer_product_lists', 'line_cost')) {
            $data['line_cost'] = $this->lineCostFromRow($row);
        }

        foreach (['price_unit_value', 'cost_price_unit_value', 'measure_unit', 'image_name'] as $optionalColumn) {
            if (array_key_exists($optionalColumn, $data) && !Schema::hasColumn('offer_product_lists', $optionalColumn)) {
                unset($data[$optionalColumn]);
            }
        }

        $productList->update($data);

        return response()->json(['ok' => true, 'item' => $productList->fresh()]);
    }
}
