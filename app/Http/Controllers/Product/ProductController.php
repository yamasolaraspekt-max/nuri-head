<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\ArticleGroup;
use App\Models\Brand;
use App\Models\BrandDepartment;
use App\Models\Category;
use App\Models\DiscountGroup;
use App\Models\Distributor;
use App\Models\Measure;
use App\Models\Product;
use App\Models\ProductDescription;
use App\Models\ProductDocuments;
use App\Models\ProductImage;
use App\Models\ProductType;
use App\Models\DistributorDepartment;
use App\Models\SubArticleGroup;
use App\Models\DistributorPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductFavoriteList;
use App\Models\StampArticleList;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductInstallationCase;
use App\Models\Inventory;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function currentEmployeeId(): ?int
    {
        $employeeId = Auth::user()?->name;

        return is_numeric($employeeId) ? (int) $employeeId : null;
    }

    private function getProductHistorySnapshot(Product $product): array
    {
        return [
            'article_no' => $product->article_no,
            'sku' => $product->sku,
            'ean' => $product->ean,
            'brand_id' => $product->brand_id,
            'product' => $product->product,
            'model' => $product->model,
            'category' => $product->category,
            'heatpump_type' => $product->heatpump_type,
            'construction_type' => $product->construction_type,
            'refrigerant' => $product->refrigerant,
            'phase_count' => $product->phase_count,
            'scop' => $product->scop,
            'noise_level_db' => $product->noise_level_db,
            'roof_type' => $product->roof_type,
            'distributor_id' => $product->distributor_id,
            'color' => $product->color,
            'measure_unit' => $product->measure_unit,
            'price_unit' => $product->price_unit,
            'package_unit' => $product->package_unit,
            'discount_group' => $product->discount_group,
            'article_group' => $product->article_group,
            'sub_article' => $product->sub_article,
            'retail_price' => $product->retail_price,
            'discount_price' => $product->discount_price,
            'purchase_price' => $product->purchase_price,
            'vat_percent' => $product->vat_percent,
            'short_description' => $product->short_description,
            'status' => $product->status,
        ];
    }

    private function recordProductHistory(
        int $productId,
        string $action,
        array $oldValues = [],
        array $newValues = [],
        ?int $changedBy = null
    ): void {
        $changedBy = $changedBy ?? $this->currentEmployeeId();

        $ignoredFields = [
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];

        $keys = collect(array_unique(array_merge(array_keys($oldValues), array_keys($newValues))))
            ->reject(fn($field) => in_array($field, $ignoredFields, true))
            ->values();

        if ($action === 'updated') {
            $keys = $keys->filter(function ($field) use ($oldValues, $newValues) {
                return ($oldValues[$field] ?? null) != ($newValues[$field] ?? null);
            })->values();
        }

        if ($action === 'updated' && $keys->isEmpty()) {
            return;
        }

        DB::table('product_histories')->insert([
            'product_id' => $productId,
            'changed_by' => $changedBy,
            'action' => $action,
            'changed_fields' => json_encode($keys->all(), JSON_UNESCAPED_UNICODE),
            'old_values' => json_encode($oldValues, JSON_UNESCAPED_UNICODE),
            'new_values' => json_encode($newValues, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function saveProductWithHistory(Product $product, array $payload, string $actionMessage = 'updated'): Product
    {
        $employeeId = $this->currentEmployeeId();
        $isNew = !$product->exists;

        $oldSnapshot = $isNew ? [] : $this->getProductHistorySnapshot($product);

        $product->fill($payload);

        if ($isNew) {
            $product->created_by = $employeeId;
        }

        $product->updated_by = $employeeId;
        $product->save();

        $newSnapshot = $this->getProductHistorySnapshot($product);

        $this->recordProductHistory(
            $product->id,
            $isNew ? 'created' : $actionMessage,
            $oldSnapshot,
            $newSnapshot,
            $employeeId
        );

        return $product;
    }

    private function buildProductListQuery(Request $request)
    {
        $query = Product::query()
            ->with(['firstImage', 'images', 'articleGroup'])
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'products.article_group')
            ->select([
                'products.*',
                'brands.name as brand_name',
                'article_groups.article_group as article_group_name',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('products.product', 'like', "%{$search}%")
                    ->orWhere('products.article_no', 'like', "%{$search}%")
                    ->orWhere('products.model', 'like', "%{$search}%")
                    ->orWhere('brands.name', 'like', "%{$search}%")
                    ->orWhere('article_groups.article_group', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('products.brand_id', $request->brand_id);
        }

        if ($request->filled('article_group_id')) {
            $query->where('products.article_group', $request->article_group_id);
        }

        if ($request->filled('status')) {
            $query->where('products.status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('products.category', $request->category);
        }

        if ($request->filled('distributor_id')) {
            $distributorId = $request->distributor_id;

            $query->where(function ($q) use ($distributorId) {
                $q->whereExists(function ($sub) use ($distributorId) {
                    $sub->select(DB::raw(1))
                        ->from('distributor_prices')
                        ->whereColumn('distributor_prices.product_id', 'products.id')
                        ->where('distributor_prices.distributor_id', $distributorId);
                })
                    ->orWhereExists(function ($sub) use ($distributorId) {
                        $sub->select(DB::raw(1))
                            ->from('distributor_product')
                            ->whereColumn('distributor_product.product_id', 'products.id')
                            ->where('distributor_product.distributor_id', $distributorId);
                    });
            });
        }

        if ($request->boolean('no_image')) {
            $query->whereDoesntHave('images');
        }

        return $query;
    }

    public function exportNoImageProductsCsv(Request $request)
    {
        $query = Product::query()
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'products.article_group')
            ->select([
                'products.id',
                'products.article_no',
                'products.product',
                'products.brand_id',
                'products.article_group',
                'products.category',
                'products.status',
                'brands.name as brand_name',
                'article_groups.article_group as article_group_name',
            ])
            ->whereDoesntHave('images');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where('products.product', 'like', "%{$search}%")
                    ->orWhere('products.article_no', 'like', "%{$search}%")
                    ->orWhere('products.model', 'like', "%{$search}%")
                    ->orWhere('brands.name', 'like', "%{$search}%")
                    ->orWhere('article_groups.article_group', 'like', "%{$search}%");
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('products.brand_id', $request->brand_id);
        }

        if ($request->filled('article_group_id')) {
            $query->where('products.article_group', $request->article_group_id);
        }

        if ($request->filled('status')) {
            $query->where('products.status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('products.category', $request->category);
        }

        if ($request->filled('distributor_id')) {
            $distributorId = $request->distributor_id;

            $query->where(function ($q) use ($distributorId) {
                $q->whereExists(function ($sub) use ($distributorId) {
                    $sub->select(DB::raw(1))
                        ->from('distributor_prices')
                        ->whereColumn('distributor_prices.product_id', 'products.id')
                        ->where('distributor_prices.distributor_id', $distributorId);
                })
                    ->orWhereExists(function ($sub) use ($distributorId) {
                        $sub->select(DB::raw(1))
                            ->from('distributor_product')
                            ->whereColumn('distributor_product.product_id', 'products.id')
                            ->where('distributor_product.distributor_id', $distributorId);
                    });
            });
        }

        $products = $query
            ->orderBy('products.article_no')
            ->get();

        $productIds = $products->pluck('id')->values();

        $distributorsByProduct = DB::table('distributor_prices')
            ->leftJoin('distributors', 'distributors.id', '=', 'distributor_prices.distributor_id')
            ->whereIn('distributor_prices.product_id', $productIds)
            ->select([
                'distributor_prices.product_id',
                'distributors.name as distributor_name',
            ])
            ->get()
            ->groupBy('product_id')
            ->map(function ($rows) {
                return $rows
                    ->pluck('distributor_name')
                    ->filter()
                    ->unique()
                    ->values()
                    ->implode(', ');
            });

        $fileName = 'produkte-ohne-bilder-' . now()->format('Y-m-d-H-i') . '.csv';

        return new StreamedResponse(function () use ($products, $distributorsByProduct) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Artikelnummer',
                'Produktname',
                'Hersteller',
                'Artikelgruppe',
                'Kategorie',
                'Lieferanten',
            ], ';');

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->article_no ?? '',
                    $product->product ?? '',
                    $product->brand_name ?? '',
                    $product->article_group_name ?? $product->article_group ?? '',
                    $product->category ?? '',
                    $distributorsByProduct[$product->id] ?? '',
                ], ';');
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    public function index(Request $request)
    {
        $brands = Brand::orderBy('name')->get();
        $articleGroups = ArticleGroup::orderBy('article_group')->get();
        $discountGroups = DiscountGroup::orderBy('discount_group')->get();

        $distributors = Distributor::orderBy('name')->get();

        $employeeId = Auth::user()?->name;

        $favoriteLists = ProductFavoriteList::query()
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->orderBy('name')
            ->get();

        $stampLists = StampArticleList::query()
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->orderBy('name')
            ->get();

        return view('admin.product.product.product', compact(
            'brands',
            'articleGroups',
            'discountGroups',
            'favoriteLists',
            'stampLists',
            'distributors'
        ));
    }

    public function ajaxList(Request $request)
    {
        $perPage = (int) $request->input('per_page', 12);
        $perPage = in_array($perPage, [12, 24, 48], true) ? $perPage : 12;

        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = [
            'created_at',
            'product',
            'article_no',
            'brand',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }

        $query = $this->buildProductListQuery($request);

        if ($sortBy === 'brand') {
            $query->orderBy('brands.name', $sortDir);
        } else {
            $query->orderBy('products.' . $sortBy, $sortDir);
        }

        $products = $query->paginate($perPage)->appends($request->query());

        $productIds = $products->pluck('id')->values();

        $distributorsByProduct = DB::table('distributor_prices')
            ->leftJoin('distributors', 'distributors.id', '=', 'distributor_prices.distributor_id')
            ->whereIn('distributor_prices.product_id', $productIds)
            ->select([
                'distributor_prices.id',
                'distributor_prices.product_id',
                'distributor_prices.distributor_id',
                'distributors.name as distributor_name',
                'distributor_prices.article_no',
                'distributor_prices.price',
                'distributor_prices.purchase_price',
                'distributor_prices.discount_price',
                'distributor_prices.availability',
            ])
            ->orderBy('distributors.name')
            ->get()
            ->groupBy('product_id')
            ->map(function ($rows) {
                return $rows->map(function ($row) {
                    $displayPrice = $row->purchase_price ?? $row->discount_price ?? $row->price;

                    $row->display_price = $displayPrice;
                    $row->display_price_label = $row->purchase_price ? 'EK' : 'Preis';
                    $row->price_badge = null;

                    return $row;
                });
            });

        $favCountsByProduct = [];
        $stampCountsByProduct = [];

        $view = $request->input('view_type') === 'card'
            ? 'admin.product.product.partials.product_cards'
            : 'admin.product.product.partials.product_list';

        return response()->json([
            'html' => view($view, compact(
                'products',
                'distributorsByProduct',
                'favCountsByProduct',
                'stampCountsByProduct'
            ))->render(),

            'pagination' => $products->links()->render(),
            'total' => $products->total(),
        ]);
    }

    public function history(Product $product)
    {
        /*
        |--------------------------------------------------------------------------
        | Deutsche Feldnamen für die Historie
        |--------------------------------------------------------------------------
        */
        $fieldLabels = [
            'article_no' => 'Hersteller-Nr.',
            'sku' => 'SKU',
            'ean' => 'EAN',
            'brand_id' => 'Hersteller',
            'product' => 'Produktname',
            'model' => 'Modell',
            'category' => 'Kategorie',
            'heatpump_type' => 'Wärmepumpen-Typ',
            'construction_type' => 'Bauart',
            'refrigerant' => 'Kältemittel',
            'phase_count' => 'Phasenanzahl',
            'scop' => 'SCOP',
            'noise_level_db' => 'Lautstärke',
            'roof_type' => 'Dachtyp',
            'distributor_id' => 'Lieferant',
            'color' => 'Farbe',
            'measure_unit' => 'Maßeinheit',
            'price_unit' => 'Preiseinheit',
            'package_unit' => 'Verpackungseinheit',
            'discount_group' => 'Rabattgruppe',
            'article_group' => 'Artikelgruppe',
            'sub_article' => 'Unterartikel',
            'retail_price' => 'VK-Preis',
            'discount_price' => 'Rabattpreis',
            'purchase_price' => 'EK-Preis',
            'vat_percent' => 'MwSt.',
            'short_description' => 'Kurzbeschreibung',
            'status' => 'Status',
            'Lieferanten/Preise' => 'Lieferanten / Preise',
        ];

        /*
        |--------------------------------------------------------------------------
        | Deutsche Aktionsnamen
        |--------------------------------------------------------------------------
        */
        $actionLabels = [
            'created' => 'Erstellt',
            'updated' => 'Geändert',
            'deleted' => 'Gelöscht',
            'restored' => 'Wiederhergestellt',
        ];

        /*
        |--------------------------------------------------------------------------
        | Lookup-Tabellen laden, damit IDs lesbar werden
        |--------------------------------------------------------------------------
        */
        $brandNames = DB::table('brands')
            ->pluck('name', 'id')
            ->toArray();

        $articleGroupNames = DB::table('article_groups')
            ->pluck('article_group', 'id')
            ->toArray();

        $measureNames = DB::table('measures')
            ->pluck('measure', 'id')
            ->toArray();

        $discountGroupNames = DB::table('discount_groups')
            ->pluck('discount_group', 'id')
            ->toArray();

        $subArticleNames = DB::table('sub_article_groups')
            ->pluck('sub_article', 'id')
            ->toArray();

        $distributorNames = DB::table('distributors')
            ->pluck('name', 'id')
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Mitarbeiter laden
        |--------------------------------------------------------------------------
        */
        $creator = DB::table('employees')
            ->where('id', $product->created_by)
            ->select('id', 'name', 'lastname', 'image')
            ->first();

        $editor = DB::table('employees')
            ->where('id', $product->updated_by)
            ->select('id', 'name', 'lastname', 'image')
            ->first();

        $creatorName = $creator
            ? trim(($creator->name ?? '') . ' ' . ($creator->lastname ?? ''))
            : null;

        $editorName = $editor
            ? trim(($editor->name ?? '') . ' ' . ($editor->lastname ?? ''))
            : null;

        /*
        |--------------------------------------------------------------------------
        | Wert für Anzeige formatieren
        |--------------------------------------------------------------------------
        */
        $formatValue = function ($field, $value) use ($brandNames, $articleGroupNames, $measureNames, $discountGroupNames, $subArticleNames, $distributorNames) {
            if ($value === null || $value === '' || $value === []) {
                return '–';
            }

            if ($field === 'brand_id') {
                return $brandNames[$value] ?? 'Hersteller #' . $value;
            }

            if ($field === 'article_group') {
                return $articleGroupNames[$value] ?? 'Artikelgruppe #' . $value;
            }

            if ($field === 'measure_unit') {
                return $measureNames[$value] ?? 'Maßeinheit #' . $value;
            }

            if ($field === 'discount_group') {
                return $discountGroupNames[$value] ?? 'Rabattgruppe #' . $value;
            }

            if ($field === 'sub_article') {
                return $subArticleNames[$value] ?? 'Unterartikel #' . $value;
            }

            if ($field === 'distributor_id') {
                return $distributorNames[$value] ?? 'Lieferant #' . $value;
            }

            if ($field === 'status') {
                return match ((string) $value) {
                    'Published' => 'Aktiv',
                    'Unpublished' => 'Inaktiv',
                    default => (string) $value,
                };
            }

            if (in_array($field, ['retail_price', 'discount_price', 'purchase_price'], true) && is_numeric($value)) {
                return number_format((float) $value, 2, ',', '.') . ' €';
            }

            if ($field === 'vat_percent' && is_numeric($value)) {
                return number_format((float) $value, 2, ',', '.') . ' %';
            }

            if ($field === 'noise_level_db' && is_numeric($value)) {
                return number_format((float) $value, 1, ',', '.') . ' dB';
            }

            if (is_array($value) || is_object($value)) {
                return 'Mehrere Einträge geändert';
            }

            if (is_string($value)) {
                $value = trim($value);

                $emptyHtml = strtolower(str_replace([' ', "\n", "\r", "\t"], '', $value));

                if (in_array($emptyHtml, ['<p><br></p>', '<p></p>', '<br>', '<div><br></div>', '&nbsp;'], true)) {
                    return '–';
                }

                $plain = trim(strip_tags($value));

                return $plain !== '' ? $plain : '–';
            }

            return (string) $value;
        };

        /*
        |--------------------------------------------------------------------------
        | Historie laden und komplett deutsch formatieren
        |--------------------------------------------------------------------------
        */
        $histories = DB::table('product_histories')
            ->leftJoin('employees', 'employees.id', '=', 'product_histories.changed_by')
            ->where('product_histories.product_id', $product->id)
            ->orderByDesc('product_histories.created_at')
            ->select([
                'product_histories.*',
                'employees.name as employee_name',
                'employees.lastname as employee_lastname',
                'employees.image as employee_image',
            ])
            ->limit(80)
            ->get()
            ->map(function ($history) use ($fieldLabels, $actionLabels, $formatValue) {
                $employeeName = trim(
                    ($history->employee_name ?? '') . ' ' . ($history->employee_lastname ?? '')
                );

                $changedFields = json_decode($history->changed_fields ?? '[]', true) ?: [];
                $oldValues = json_decode($history->old_values ?? '{}', true) ?: [];
                $newValues = json_decode($history->new_values ?? '{}', true) ?: [];

                $changes = [];

                foreach ($changedFields as $field) {
                    $changes[] = [
                        'field' => $field,
                        'label' => $fieldLabels[$field] ?? $field,
                        'old_value' => $formatValue($field, $oldValues[$field] ?? null),
                        'new_value' => $formatValue($field, $newValues[$field] ?? null),
                    ];
                }

                return [
                    'id' => $history->id,
                    'action' => $history->action,
                    'action_label' => $actionLabels[$history->action] ?? 'Geändert',
                    'changed_by' => $history->changed_by,
                    'changed_by_name' => $employeeName ?: ($history->changed_by ? 'Mitarbeiter #' . $history->changed_by : 'System'),
                    'changed_fields' => collect($changes)->pluck('label')->values()->all(),
                    'changes' => $changes,
                    'created_at' => optional($history->created_at ? \Carbon\Carbon::parse($history->created_at) : null)->format('d.m.Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->product ?: 'Ohne Name',
                'article_no' => $product->article_no ?: '–',
                'created_at' => optional($product->created_at)->format('d.m.Y H:i'),
                'updated_at' => optional($product->updated_at)->format('d.m.Y H:i'),
                'created_by' => $creatorName ?: ($product->created_by ? 'Mitarbeiter #' . $product->created_by : '–'),
                'updated_by' => $editorName ?: ($product->updated_by ? 'Mitarbeiter #' . $product->updated_by : '–'),
            ],
            'histories' => $histories,
        ]);
    }
    public function ajaxSearch(Request $request)
    {
        $term = trim((string) $request->get('q', ''));

        $query = DB::table('products')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->select(
                'products.id',
                'products.product',
                'products.article_no',
                'products.category',
                'brands.name as brand_name'
            );

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('products.product', 'like', "%{$term}%")
                    ->orWhere('products.article_no', 'like', "%{$term}%")
                    ->orWhere('brands.name', 'like', "%{$term}%");
            });
        }

        return response()->json(
            $query->orderBy('products.product')->limit(20)->get()
        );
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Keine Produkte ausgewählt.',
            ], 422);
        }

        $employeeId = $this->currentEmployeeId();
        $products = Product::whereIn('id', $ids)->get();

        switch ($action) {
            case 'publish':
                foreach ($products as $product) {
                    $old = $this->getProductHistorySnapshot($product);

                    $product->status = 'Published';
                    $product->updated_by = $employeeId;
                    $product->save();

                    $this->recordProductHistory(
                        $product->id,
                        'updated',
                        $old,
                        $this->getProductHistorySnapshot($product),
                        $employeeId
                    );
                }

                $msg = 'Ausgewählte Produkte wurden veröffentlicht.';
                break;

            case 'unpublish':
                foreach ($products as $product) {
                    $old = $this->getProductHistorySnapshot($product);

                    $product->status = 'Unpublished';
                    $product->updated_by = $employeeId;
                    $product->save();

                    $this->recordProductHistory(
                        $product->id,
                        'updated',
                        $old,
                        $this->getProductHistorySnapshot($product),
                        $employeeId
                    );
                }

                $msg = 'Ausgewählte Produkte wurden deaktiviert.';
                break;

            case 'delete':
                foreach ($products as $product) {
                    $this->recordProductHistory(
                        $product->id,
                        'deleted',
                        $this->getProductHistorySnapshot($product),
                        [],
                        $employeeId
                    );

                    $product->delete();
                }

                $msg = 'Ausgewählte Produkte wurden gelöscht.';
                break;

            default:
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ungültige Aktion.',
                ], 422);
        }

        return response()->json([
            'status' => 'success',
            'message' => $msg,
        ]);
    }

    public function create()
    {
        $data['brands'] = Brand::where('status', 'Published')->orderBy('id', 'desc')->get();
        $data['distributor'] = Distributor::all();
        $data['article_groups'] = ArticleGroup::all();
        $data['discount_group'] = DiscountGroup::all();
        $data['sub_article'] = SubArticleGroup::all();
        $data['measures'] = Measure::all();
        $data['employees'] = DB::table('employees')->select('id', 'name', 'lastname', 'image')->get();

        return view('admin.product.product.product_create', $data);
    }

    public function getBrand()
    {
        $brands = Brand::where('status', 'Published')->orderBy('id', 'desc')->get();
        return response()->json($brands, 200);
    }

    public function storeBrand(Request $request)
    {
        Log::info('Logging request data', $request->all());

        $this->validate($request, [
            'name' => 'required|string|max:255',
            'initial' => 'required|string|max:10',
            'purpose' => 'required|string',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
        ], [
            'name.required' => 'Der Firmenname ist erforderlich',
            'initial.required' => 'Der Anfangsbuchstabe ist erforderlich',
            'purpose.required' => 'Die Firmenartikelgruppe ist erforderlich',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->initial = $request->initial;
        $brand->purpose = $request->purpose;
        $brand->status = 'Published';

        if ($request->hasFile('image')) {
            $image_name = time() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/brand/', $image_name);
            $brand->image = $image_name;
        }

        $brand->save();

        if (!empty($request->brand) && is_array($request->brand)) {
            $this->validate($request, [
                'brand' => 'nullable|array',
                'brand.*.brand_department' => 'nullable|string',
                'brand.*.email' => 'nullable|email',
                'brand.*.phone' => 'nullable|string',
            ], [
                'brand.*.brand_department.required' => 'Abteilung: Dieser Eintrag ist erforderlich',
            ]);

            foreach ($request->brand as $key => $value) {
                if (!empty($value['brand_department'])) {
                    $brandDepartment = new BrandDepartment();
                    $brandDepartment->brand_id = $brand->id;
                    $brandDepartment->brand_department = $value['brand_department'];
                    $brandDepartment->email = $value['email'] ?? null;
                    $brandDepartment->phone = $value['phone'] ?? null;
                    $brandDepartment->office = $value['office'] ?? null;
                    $brandDepartment->home = $value['home'] ?? null;
                    $brandDepartment->status = $value['status'] ?? 'active';
                    $brandDepartment->save();
                }
            }
        }

        return response()->json([
            'save_msg' => 'Der Datensatz wurde erfolgreich gespeichert',
            'brand' => $brand,
        ], 200);
    }

    public function allDistributor(Request $request)
    {
        $id = $request->id;
        $all = Distributor::whereIn('id', [$id])->get();

        return response()->json($all, 200);
    }

    public function getDistributor()
    {
        $distributor = Distributor::all();
        return response()->json($distributor, 200);
    }

    public function delete_photo($photo)
    {
        if (!empty($photo)) {
            $photo_path = 'images/product/' . $photo;

            if (file_exists($photo_path)) {
                unlink($photo_path);
            }
        }
    }

    public function store(Request $request)
    {
        $stage = (string) $request->input('stage', '0');

        $logCtx = function (string $tag, array $ctx = []) use ($request, $stage) {
            Log::info("[Product.store] {$tag}", array_merge([
                'stage' => $stage,
                'product_id' => $request->input('product_id'),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ], $ctx));
        };

        $toDec = function ($v) {
            if ($v === null)
                return null;

            $v = trim((string) $v);

            if ($v === '')
                return null;

            $v = str_replace([' ', "\u{00A0}"], '', $v);
            $v = str_replace(',', '.', $v);

            return is_numeric($v) ? $v : null;
        };

        $logCtx('incoming', [
            'content_type' => $request->header('Content-Type'),
            'has_price' => $request->has('price'),
            'price_type' => gettype($request->input('price')),
            'price_keys' => is_array($request->input('price')) ? array_keys($request->input('price')) : null,
            'has_dist' => $request->has('distributor'),
            'dist' => $request->input('distributor'),
        ]);

        $rules = [
            'brand_id' => 'nullable|integer|exists:brands,id',
            'article_no' => 'nullable|string',
            'ean' => 'nullable|string',
            'roof_type' => 'nullable|string',
            'color' => 'nullable|string',
            'measure_unit' => 'nullable|string',
            'price_unit' => 'nullable|string',
            'package_unit' => 'nullable|string',
            'discount_group' => 'nullable|integer',
            'article_group' => 'nullable|integer',
            'sub_article' => 'nullable|integer',
            'short_description' => 'nullable|string',
            'distributor' => 'nullable|array',
            'price' => 'nullable|array',
        ];

        if ($stage === '0') {
            $rules['model'] = 'required|string';
            $rules['product'] = 'required|string';
            $rules['category'] = 'required|string';
        } else {
            $rules['model'] = 'sometimes|nullable|string';
            $rules['product'] = 'sometimes|nullable|string';
            $rules['category'] = 'sometimes|nullable|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $logCtx('validation_failed', ['errors' => $validator->errors()->toArray()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $productId = $request->input('product_id');
            $employeeId = $this->currentEmployeeId();

            if ($stage === '0') {
                $payload = [
                    'article_no' => $request->input('article_no'),
                    'ean' => $request->input('ean'),
                    'brand_id' => $request->input('brand_id'),
                    'product' => $request->input('product'),
                    'model' => $request->input('model'),
                    'category' => $request->input('category'),
                    'roof_type' => $request->input('roof_type'),
                    'color' => $request->input('color'),
                    'measure_unit' => $request->input('measure_unit'),
                    'price_unit' => $request->input('price_unit'),
                    'package_unit' => $request->input('package_unit'),
                    'discount_group' => $request->input('discount_group'),
                    'article_group' => $request->input('article_group'),
                    'sub_article' => $request->input('sub_article'),
                    'short_description' => $request->input('short_description'),
                ];

                if ($productId) {
                    $product = Product::find($productId);

                    if (!$product) {
                        DB::rollBack();
                        return response()->json(['error' => 'Produkt nicht gefunden.'], 404);
                    }

                    $this->saveProductWithHistory($product, $payload, 'updated');

                    DB::commit();

                    return response()->json([
                        'message' => 'Produkt aktualisiert.',
                        'product_id' => $product->id,
                    ], 200);
                }

                $existing = Product::query()
                    ->where('product', $request->input('product'))
                    ->where('model', $request->input('model'))
                    ->where('brand_id', $request->input('brand_id'))
                    ->first();

                if ($existing) {
                    DB::commit();

                    return response()->json([
                        'message' => 'Produkt existiert bereits.',
                        'product_id' => $existing->id,
                    ], 200);
                }

                $payload['status'] = 'Published';

                $product = new Product();
                $this->saveProductWithHistory($product, $payload, 'created');

                DB::commit();

                return response()->json([
                    'message' => 'Produkt erstellt.',
                    'product_id' => $product->id,
                ], 200);
            }

            if (!$productId) {
                $logCtx('missing_product_id_for_stage');
                DB::rollBack();

                return response()->json(['error' => 'Produkt-ID fehlt.'], 422);
            }

            $product = Product::find($productId);

            if (!$product) {
                $logCtx('product_not_found', ['product_id' => $productId]);
                DB::rollBack();

                return response()->json(['error' => 'Produkt nicht gefunden.'], 404);
            }

            if ($stage === '1') {
                $old = $this->getProductHistorySnapshot($product);

                $product->updated_by = $employeeId;
                $product->save();

                $this->recordProductHistory(
                    $product->id,
                    'updated',
                    $old,
                    $this->getProductHistorySnapshot($product),
                    $employeeId
                );

                DB::commit();
                $logCtx('stage1_saved');

                return response()->json([
                    'message' => 'Technische Daten gespeichert.',
                    'product_id' => $product->id,
                ], 200);
            }

            if ($stage === '2') {
                $prices = $request->input('price', []);
                $dist = $request->input('distributor', []);

                $oldPrices = DistributorPrice::where('product_id', $product->id)->get()->toArray();

                $logCtx('stage2_payload', [
                    'distributor_count' => is_array($dist) ? count($dist) : null,
                    'price_count' => is_array($prices) ? count($prices) : null,
                    'price_first' => is_array($prices) ? array_slice($prices, 0, 1) : null,
                ]);

                if (is_array($dist)) {
                    try {
                        if (method_exists($product, 'distributors')) {
                            $product->distributors()->sync($dist);
                            $logCtx('stage2_distributors_synced', ['relationship' => 'distributors']);
                        } elseif (method_exists($product, 'distributor')) {
                            $product->distributor()->sync($dist);
                            $logCtx('stage2_distributors_synced', ['relationship' => 'distributor']);
                        } else {
                            $logCtx('stage2_distributors_relation_missing', [
                                'hint' => 'No distributors()/distributor() relation on Product model.',
                            ]);
                        }
                    } catch (\Throwable $e) {
                        $logCtx('stage2_distributor_sync_error', ['error' => $e->getMessage()]);
                        throw $e;
                    }
                }

                if (!is_array($prices) || count($prices) === 0) {
                    $product->updated_by = $employeeId;
                    $product->save();

                    $this->recordProductHistory(
                        $product->id,
                        'updated',
                        ['Lieferanten/Preise' => $oldPrices],
                        ['Lieferanten/Preise' => 'Lieferanten gespeichert, keine Preiszeilen empfangen.'],
                        $employeeId
                    );

                    $logCtx('stage2_no_prices_received', [
                        'hint' => 'price[] empty: check that table inputs are inside the wizard <form> and not disabled.',
                    ]);

                    DB::commit();

                    return response()->json([
                        'message' => 'Lieferanten gespeichert (keine Preiszeilen empfangen).',
                        'product_id' => $product->id,
                    ], 200);
                }

                $inserted = 0;
                $updated = 0;
                $skipped = 0;

                foreach ($prices as $rowKey => $priceData) {
                    if (!is_array($priceData)) {
                        $skipped++;
                        continue;
                    }

                    $distributorId = $priceData['distributor_id'] ?? null;

                    if (!$distributorId) {
                        $skipped++;
                        continue;
                    }

                    $discountGroupId = $priceData['discount_group_id'] ?? null;
                    $discountGroupId = $discountGroupId ?: null;

                    $payload = [
                        'product_id' => $product->id,
                        'distributor_id' => $distributorId,
                        'discount_group_id' => $discountGroupId,
                        'article_no' => $priceData['article_no'] ?? null,
                        'price' => $toDec($priceData['price'] ?? null),
                        'purchase_price' => $toDec($priceData['purchase_price'] ?? null),
                        'discount_price' => $toDec($priceData['discount_price'] ?? null),
                        'discount_percent' => $toDec($priceData['discount_percent'] ?? null),
                        'price_date' => $priceData['price_date'] ?? null,
                        'availability' => $priceData['availability'] ?? null,
                        'note' => $priceData['note'] ?? null,
                        'status' => 'Published',
                    ];

                    $hasAnything =
                        ($payload['article_no'] ?? null) ||
                        ($payload['availability'] ?? null) ||
                        ($payload['note'] ?? null) ||
                        ($payload['price'] !== null) ||
                        ($payload['purchase_price'] !== null) ||
                        ($payload['discount_price'] !== null) ||
                        ($payload['discount_percent'] !== null);

                    if (!$hasAnything) {
                        $skipped++;
                        continue;
                    }

                    try {
                        $rowId = $priceData['id'] ?? null;

                        if ($rowId) {
                            $affected = DistributorPrice::query()
                                ->where('id', $rowId)
                                ->where('product_id', $product->id)
                                ->update($payload);

                            if ($affected) {
                                $updated++;
                            } else {
                                DistributorPrice::create($payload);
                                $inserted++;
                            }
                        } else {
                            $articleNoKey = (string) ($payload['article_no'] ?? '');

                            DistributorPrice::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'distributor_id' => $distributorId,
                                    'article_no' => $articleNoKey,
                                ],
                                $payload
                            );

                            $updated++;
                        }
                    } catch (\Throwable $e) {
                        $logCtx('stage2_price_error', [
                            'rowKey' => $rowKey,
                            'error' => $e->getMessage(),
                            'row' => $priceData,
                        ]);

                        throw $e;
                    }
                }

                $newPrices = DistributorPrice::where('product_id', $product->id)->get()->toArray();

                $product->updated_by = $employeeId;
                $product->save();

                $this->recordProductHistory(
                    $product->id,
                    'updated',
                    ['Lieferanten/Preise' => $oldPrices],
                    ['Lieferanten/Preise' => $newPrices],
                    $employeeId
                );

                DB::commit();

                $logCtx('stage2_done', [
                    'inserted' => $inserted,
                    'updated' => $updated,
                    'skipped' => $skipped,
                ]);

                return response()->json([
                    'message' => "Lieferant & Preise gespeichert. (inserted: {$inserted}, updated: {$updated}, skipped: {$skipped})",
                    'product_id' => $product->id,
                ], 200);
            }

            $old = $this->getProductHistorySnapshot($product);

            $product->updated_by = $employeeId;
            $product->save();

            $this->recordProductHistory(
                $product->id,
                'updated',
                $old,
                $this->getProductHistorySnapshot($product),
                $employeeId
            );

            DB::commit();
            $logCtx('stage_other_saved');

            return response()->json([
                'message' => 'Gespeichert',
                'product_id' => $product->id,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[Product.store] exception', [
                'stage' => $request->input('stage'),
                'product_id' => $request->input('product_id'),
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 5000),
            ]);

            return response()->json([
                'error' => 'Ein Fehler ist aufgetreten: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function distributorStore(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'product' => 'required|string',
                'model' => 'required|string',
                'price' => 'nullable|array',
                'price.*.distributor_id' => 'required|integer|exists:distributors,id',
                'price.*.article_no' => 'nullable|string',
                'price.*.price' => 'required|numeric',
                'price.*.purchase_price' => 'required|numeric',
                'price.*.discount_price' => 'required|numeric',
                'price.*.discount_percent' => 'required|numeric',
                'price.*.discount_group_id' => 'nullable|integer',
                'price.*.price_date' => 'required|date',
                'price.*.availability' => 'nullable|string',
            ]);

            $product = Product::updateOrCreate(
                ['id' => $request->product_id],
                $request->only('product', 'model', 'brand_id')
            );

            $old = $this->getProductHistorySnapshot($product);

            $product->created_by = $product->created_by ?: $this->currentEmployeeId();
            $product->updated_by = $this->currentEmployeeId();
            $product->save();

            if ($request->has('distributor')) {
                $product->distributor()->sync($request->distributor);
            }

            if ($request->has('price')) {
                DistributorPrice::where('product_id', $product->id)->delete();

                foreach ($request->price as $price) {
                    $price['product_id'] = $product->id;
                    DistributorPrice::create($price);
                }
            }

            $this->recordProductHistory(
                $product->id,
                'updated',
                $old,
                $this->getProductHistorySnapshot($product),
                $this->currentEmployeeId()
            );

            DB::commit();

            return response()->json([
                'message' => 'Produkt gespeichert',
                'product_id' => $product->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Fehler: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $id = (int) $id;

        $product = DB::table('products')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'products.article_group')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('measures', 'measures.id', '=', 'products.measure_unit')
            ->select([
                'products.*',
                'article_groups.article_group as article_group_name',
                'brands.name as brandname',
                'measures.measure as measurement',
            ])
            ->where('products.id', $id)
            ->first();

        if (!$product) {
            return redirect()->back()->with('error_msg', 'Product not found.');
        }

        $proImages = ProductImage::where('product_id', $id)->get();
        $descriptions = ProductDescription::where('product_id', $id)->get();
        $proDescription = $descriptions;

        $productPv = DB::table('product_p_v_s')
            ->where('product_p_v_s.product_id', $id)
            ->select('product_p_v_s.*')
            ->first();

        $productRadiator = DB::table('radiators')
            ->where('radiators.product_id', $id)
            ->select('radiators.*')
            ->first();

        $technicalPerson = DB::table('skills')
            ->join('employees', 'employees.id', '=', 'skills.emp_id')
            ->where('skills.product_id', '=', $product->article_group)
            ->select([
                'skills.*',
                'employees.name as empname',
                'employees.lastname',
                'employees.image',
                'employees.id as empid',
            ])
            ->get();

        $installation = DB::table('product_installation_cases')
            ->where('product_installation_cases.product_id', $id)
            ->orderBy('product_installation_cases.rate', 'asc')
            ->get();

        $documents = DB::table('product_documents')
            ->where('product_documents.product_id', $id)
            ->select('product_documents.*')
            ->get();

        $productOffer = DB::table('products')
            ->leftJoin('product_images', 'product_images.product_id', '=', 'products.id')
            ->select([
                'products.id',
                'products.product',
                'products.short_description',
                'product_images.image',
            ])
            ->where('products.article_group', $product->article_group)
            ->where('products.id', '!=', $id)
            ->get();

        return view('admin.product.product.product_details', [
            'data' => $product,
            'pro_images' => $proImages,
            'pro_description' => $proDescription,
            'descriptions' => $descriptions,
            'documents' => $documents,
            'installation' => $installation,
            'technical_person' => $technicalPerson,
            'product_offer' => $productOffer,
            'product_pvs' => $productPv,
            'product_pv' => $productPv,
            'radiators' => $productRadiator,
            'product_radiator' => $productRadiator,
        ]);
    }

    public function edit($id)
    {
        $product = Product::with([
            'distributors',
            'prices' => function ($query) {
                $query->with('distributor', 'discountGroup');
            },
        ])->findOrFail($id);

        $distributors = Distributor::all();
        $discount_group = DiscountGroup::all();
        $article_groups = ArticleGroup::all();
        $subArticles = SubArticleGroup::all();
        $measures = Measure::all();

        Log::info('products', [$product]);

        return view('admin.product.product.product_edit', compact(
            'product',
            'distributors',
            'discount_group',
            'article_groups',
            'subArticles',
            'measures'
        ));
    }

    public function ajaxDistributorsByBrand(Request $request)
    {
        $brandId = $request->get('brand_id');

        $q = DB::table('distributors')
            ->select('distributors.id', 'distributors.name')
            ->where('distributors.status', 'Published')
            ->orderBy('distributors.name');

        if (!empty($brandId)) {
            $q->join('distributor_product', 'distributor_product.distributor_id', '=', 'distributors.id')
                ->join('products', 'products.id', '=', 'distributor_product.product_id')
                ->where('products.brand_id', $brandId)
                ->distinct();
        }

        return response()->json([
            'items' => $q->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        Log::info('Request Data:', $request->all());

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);

            $payload = [
                'article_no' => $request->article_no,
                'ean' => $request->ean,
                'brand_id' => $request->brand_id,
                'product' => $request->product,
                'model' => $request->model,
                'category' => $request->category,
                'roof_type' => $request->roof_type,
                'color' => $request->color,
                'measure_unit' => $request->measure_unit,
                'price_unit' => $request->price_unit,
                'package_unit' => $request->package_unit,
                'discount_group' => $request->discount_group,
                'article_group' => $request->article_group,
                'sub_article' => $request->sub_article,
                'short_description' => $request->input('short_description'),
            ];

            $this->saveProductWithHistory($product, $payload, 'updated');

            DB::commit();

            return response()->json(['message' => 'Produkt erfolgreich aktualisiert!']);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Product Update Error: ' . $e->getMessage());

            return response()->json(['error' => 'Fehler: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $data = Product::where('id', '=', $id)->first();

        if (!$data) {
            return redirect()->back()->with('delete_msg', 'Produkt nicht gefunden.');
        }

        $this->recordProductHistory(
            $data->id,
            'deleted',
            $this->getProductHistorySnapshot($data),
            [],
            $this->currentEmployeeId()
        );

        $data->delete();

        $this->delete_photo($data->image ?? null);

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }

    public function publish($id)
    {
        $data = Product::findOrFail($id);
        $old = $this->getProductHistorySnapshot($data);

        $data->status = 'Published';
        $data->updated_by = $this->currentEmployeeId();
        $data->save();

        $this->recordProductHistory(
            $data->id,
            'updated',
            $old,
            $this->getProductHistorySnapshot($data),
            $this->currentEmployeeId()
        );

        return redirect()->back()->with('save_msg', 'Das Produkt (' . $data->product . ') wurde nun erfolgreich veröffentlicht!');
    }

    public function unpublish($id)
    {
        $data = Product::findOrFail($id);
        $old = $this->getProductHistorySnapshot($data);

        $data->status = 'Unpublished';
        $data->updated_by = $this->currentEmployeeId();
        $data->save();

        $this->recordProductHistory(
            $data->id,
            'updated',
            $old,
            $this->getProductHistorySnapshot($data),
            $this->currentEmployeeId()
        );

        return redirect()->back()->with('delete_msg', 'Das Produkt (' . $data->product . ') wurde nun erfolgreich aufgehoben!');
    }

    public function getSubArticle(Request $request)
    {
        $articleGroupId = $request->article;

        $data = DB::table('sub_article_groups')
            ->where('article_group_id', $articleGroupId)
            ->get();

        return response()->json($data, 200);
    }

    public function getDistributorPrices($productId)
    {
        $prices = DistributorPrice::with('distributor')
            ->where('product_id', $productId)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'distributor_id' => $item->distributor_id,
                    'distributor_name' => $item->distributor->name ?? 'N/A',
                    'distributor_logo' => asset('images/distributor/' . $item->distributor->image),
                    'article_no' => $item->article_no,
                    'price' => $item->price,
                    'purchase_price' => $item->purchase_price,
                    'discount_price' => $item->discount_price,
                    'discount_percent' => $item->discount_percent,
                    'discount_group_id' => $item->discount_group_id,
                    'price_date' => $item->price_date,
                    'availability' => $item->availability,
                ];
            });

        return response()->json($prices);
    }

    public function getProductDistributor($product_id)
    {
        $distributor = DB::table('distributor_product')
            ->join('distributors', 'distributors.id', '=', 'distributor_product.distributor_id')
            ->select('distributor_product.*', 'distributors.name')
            ->where('distributor_product.product_id', $product_id)
            ->get();

        return response()->json($distributor, 200);
    }

    public function storeDistributor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048',
            'd' => 'nullable|array',
            'd.*.d_department' => 'nullable|string|max:255',
            'd.*.email' => 'nullable|email',
            'd.*.phone' => 'nullable|string',
        ], [
            'd.*.d_department.required' => 'Abteilung: Dieser Eintrag ist erforderlich',
            'd.*.email.required' => 'Email: Dieser Eintrag ist erforderlich',
            'd.*.phone.required' => 'Phone: Dieser Eintrag ist erforderlich',
        ]);

        $data = new Distributor();
        $data->name = $request->name;
        $data->address = $request->address;
        $data->status = "Published";

        if ($request->hasFile('image')) {
            $image_name = time() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/distributor/', $image_name);
            $data->image = $image_name;
        }

        $data->save();

        if (!empty($request->d) && is_array($request->d)) {
            foreach ($request->d as $key => $value) {
                if (!empty($value['d_department'])) {
                    DistributorDepartment::create([
                        'distributor_id' => $data->id,
                        'd_department' => $value['d_department'],
                        'email' => $value['email'] ?? null,
                        'phone' => $value['phone'] ?? null,
                        'status' => 'active',
                    ]);
                }
            }
        }

        return response()->json([
            'save_msg' => 'Der Datensatz wurde erfolgreich gespeichert',
            'distributor' => $data,
        ], 200);
    }

    public function saveDistributorData(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'distributor' => 'array',
            'distributor.*' => 'integer|exists:distributors,id',
            'price' => 'array',
            'price.*.distributor_id' => 'required|integer|exists:distributors,id',
            'price.*.article_no' => 'nullable|string',
            'price.*.price' => 'required|numeric',
            'price.*.purchase_price' => 'required|numeric',
            'price.*.discount_group_id' => 'nullable|integer',
            'price.*.discount_price' => 'required|numeric',
            'price.*.discount_percent' => 'required|numeric',
            'price.*.price_date' => 'required|date',
            'price.*.availability' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($request->product_id);
            $oldPrices = DistributorPrice::where('product_id', $product->id)->get()->toArray();

            if ($request->has('distributor')) {
                $product->distributor()->sync($request->distributor);
            }

            if ($request->has('price')) {
                foreach ($request->price as $price) {
                    $price['product_id'] = $product->id;
                    DistributorPrice::create($price);
                }
            }

            $newPrices = DistributorPrice::where('product_id', $product->id)->get()->toArray();

            $product->updated_by = $this->currentEmployeeId();
            $product->save();

            $this->recordProductHistory(
                $product->id,
                'updated',
                ['Lieferanten/Preise' => $oldPrices],
                ['Lieferanten/Preise' => $newPrices],
                $this->currentEmployeeId()
            );

            DB::commit();

            return response()->json(['message' => 'Lieferanten und Preise gespeichert']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Fehler: ' . $e->getMessage()], 500);
        }
    }

    public function finalSummary($id)
    {
        $product = Product::with('brand')->findOrFail($id);
        $descriptions = ProductDescription::where('product_id', $id)->get();
        $image = ProductImage::where('product_id', $id)->latest()->first();

        return response()->json([
            'product' => $product,
            'descriptions' => $descriptions,
            'image' => $image?->image,
        ]);
    }

    public function getImage(Request $request)
    {
        $q = $request->get('q', '');
        $query = Product::with('images');

        if ($q) {
            $query->where('product', 'like', "%$q%")
                ->orWhere('model', 'like', "%$q%")
                ->orWhere('article_no', 'like', "%$q%");
        }

        return response()->json(
            $query->limit(50)->get()->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->product . ' ' . ($p->model ?? ''),
                    'image' => asset('images/products/' . $p->main_image),
                    'images' => $p->images->map(fn($img) => [
                        'id' => $img->id,
                        'name' => $img->name,
                        'url' => $img->url,
                    ]),
                    'price' => $p->retail_price ?? 0,
                ];
            })
        );
    }

    public function allImages()
    {
        $images = ProductImage::with('product:id,product,model')
            ->get()
            ->map(function ($img) {
                return [
                    'id' => $img->id,
                    'product_id' => $img->product_id,
                    'name' => $img->name,
                    'image' => $img->image,
                    'product' => $img->product?->product,
                    'model' => $img->product?->model,
                    'url' => asset('images/products/' . $img->image),
                ];
            });

        return response()->json($images);
    }

    public function ajaxInfo(Product $product)
    {
        $product->load(['brand', 'images']);

        $primaryImage = optional($product->images->first())->image;

        return response()->json([
            'product' => [
                'id' => $product->id,
                'product' => $product->product,
                'name' => $product->product,
                'article_no' => $product->article_no,
                'ean' => $product->ean,
                'category' => $product->category,
                'color' => $product->color,
                'model' => $product->model,
                'heatpump_type' => $product->heatpump_type,
                'vat' => $product->vat,
                'short_description' => $product->short_description,
                'brand_name' => optional($product->brand)->name,
                'brand' => optional($product->brand)->name,
            ],
            'brand' => $product->brand ? [
                'id' => $product->brand->id,
                'name' => $product->brand->name,
            ] : null,
            'image' => $primaryImage,
        ]);
    }

    public function duplicateSingle(Product $product)
    {
        $newProduct = $this->duplicateProductWithRelations($product);

        return response()->json([
            'success' => true,
            'message' => 'Produkt wurde dupliziert.',
            'new_product' => [
                'id' => $newProduct->id,
                'product' => $newProduct->product,
            ],
        ]);
    }

    public function bulkDuplicate(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:products,id'],
        ]);

        $newIds = [];

        DB::transaction(function () use (&$newIds, $data) {
            $products = Product::whereIn('id', $data['ids'])->get();

            foreach ($products as $product) {
                $new = $this->duplicateProductWithRelations($product);
                $newIds[] = $new->id;
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Ausgewählte Produkte wurden dupliziert.',
            'new_ids' => $newIds,
        ]);
    }

    protected function duplicateProductWithRelations(Product $product): Product
    {
        return DB::transaction(function () use ($product) {
            $new = $product->replicate();

            $new->product = $product->product . ' (Kopie)';
            $new->article_no = $product->article_no ? $product->article_no . '-COPY' : null;
            $new->sku = $product->sku ? $product->sku . '-COPY' : null;
            $new->created_by = $this->currentEmployeeId();
            $new->updated_by = $this->currentEmployeeId();

            $new->push();

            $this->recordProductHistory(
                $new->id,
                'created',
                [],
                $this->getProductHistorySnapshot($new),
                $this->currentEmployeeId()
            );

            ProductImage::where('product_id', $product->id)
                ->get()
                ->each(function (ProductImage $img) use ($new) {
                    $clone = $img->replicate();
                    $clone->product_id = $new->id;
                    $clone->save();
                });

            ProductInstallationCase::where('product_id', $product->id)
                ->get()
                ->each(function (ProductInstallationCase $case) use ($new) {
                    $clone = $case->replicate();
                    $clone->product_id = $new->id;
                    $clone->save();
                });

            ProductDocuments::where('product_id', $product->id)
                ->get()
                ->each(function (ProductDocuments $doc) use ($new) {
                    $clone = $doc->replicate();
                    $clone->product_id = $new->id;
                    $clone->save();
                });

            Inventory::where('product_id', $product->id)
                ->get()
                ->each(function (Inventory $inv) use ($new) {
                    $clone = $inv->replicate();
                    $clone->product_id = $new->id;
                    $clone->save();
                });

            ProductDescription::where('product_id', $product->id)
                ->get()
                ->each(function (ProductDescription $desc) use ($new) {
                    $clone = $desc->replicate();
                    $clone->product_id = $new->id;
                    $clone->save();
                });

            $distRows = DB::table('distributor_product')
                ->where('product_id', $product->id)
                ->get();

            foreach ($distRows as $row) {
                DB::table('distributor_product')->insert([
                    'distributor_id' => $row->distributor_id,
                    'product_id' => $new->id,
                ]);
            }

            $priceRows = DistributorPrice::where('product_id', $product->id)->get();

            foreach ($priceRows as $price) {
                $clone = $price->replicate();
                $clone->product_id = $new->id;
                $clone->save();
            }

            return $new;
        });
    }
}