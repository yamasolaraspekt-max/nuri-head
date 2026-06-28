<?php

namespace App\Http\Controllers\Product\Distributor;

use App\Http\Controllers\Controller;
use App\Models\DiscountGroup;
use App\Models\Distributor;
use App\Models\DistributorPrice;
use App\Models\Product;
use App\Models\ProductImage;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DistributorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
    |--------------------------------------------------------------------------
    | Distributor overview
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Distributor::query()
            ->withCount([
                'supplierConnections',
                'activeSupplierConnections',
                'prices as distributor_prices_count',
            ])
            ->with([
                'supplierConnections' => function ($q) {
                    $q->select([
                        'id',
                        'distributor_id',
                        'name',
                        'supplier_key',
                        'connector_type',
                        'is_active',
                        'last_test_status',
                        'last_tested_at',
                    ])
                        ->latest()
                        ->limit(20);
                },
            ]);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%")
                    ->orWhere('street', 'like', "%{$search}%")
                    ->orWhere('postal_code', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhere('payment_terms', 'like', "%{$search}%")
                    ->orWhereHas('supplierConnections', function ($connectorQuery) use ($search) {
                        $connectorQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('supplier_key', 'like', "%{$search}%")
                            ->orWhere('connector_type', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('connector_type')) {
            $query->whereHas('supplierConnections', function ($connectorQuery) use ($request) {
                $connectorQuery->where('connector_type', $request->connector_type);
            });
        }

        if ($request->filled('connector_status')) {
            if ($request->connector_status === 'with_connector') {
                $query->has('supplierConnections');
            }

            if ($request->connector_status === 'without_connector') {
                $query->doesntHave('supplierConnections');
            }

            if ($request->connector_status === 'active_connector') {
                $query->has('activeSupplierConnections');
            }
        }

        $allowedSorts = ['id', 'name', 'created_at', 'city', 'status'];

        $sortBy = in_array($request->get('sort_by'), $allowedSorts, true)
            ? $request->get('sort_by')
            : 'id';

        $sortDir = strtolower((string) $request->get('sort_dir', 'desc')) === 'asc'
            ? 'asc'
            : 'desc';

        $data = $query
            ->orderBy($sortBy, $sortDir)
            ->paginate(10)
            ->withQueryString();

        return view('admin.product.distributor.distributor', compact('data'));
    }

    /*
    |--------------------------------------------------------------------------
    | NEW: Distributor products modal
    |--------------------------------------------------------------------------
    */

    public function products(Request $request, Distributor $distributor)
    {
        $search = trim((string) $request->query('search', ''));

        $prices = DistributorPrice::query()
            ->with([
                'product.brand',
                'product.articleGroup',
                'product.firstImage',
                'distributor:id,name,short_name',
            ])
            ->where('distributor_id', $distributor->id)
            ->whereHas('product', function ($q) use ($search) {
                if ($search !== '') {
                    $q->where(function ($qq) use ($search) {
                        $qq->where('product', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('article_no', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhere('ean', 'like', "%{$search}%");
                    });
                }
            })
            ->orderByRaw('price_date IS NULL ASC')
            ->orderByDesc('price_date')
            ->orderByDesc('id')
            ->get()
            ->filter(fn (DistributorPrice $row) => $row->product !== null)
            ->unique(function (DistributorPrice $row) {
                return $row->product_id . '|' . ($row->article_no ?? '');
            })
            ->values();

        $priceValues = $prices
            ->map(fn (DistributorPrice $row) => $this->moneyValue($row->purchase_price ?? $row->price))
            ->filter(fn ($value) => $value !== null)
            ->values();

        $min = $priceValues->count() ? round((float) $priceValues->min(), 2) : null;
        $max = $priceValues->count() ? round((float) $priceValues->max(), 2) : null;

        return response()->json([
            'distributor' => [
                'id' => $distributor->id,
                'name' => $this->distributorName($distributor),
            ],
            'summary' => [
                'total_products' => $prices->count(),
                'min_price' => $min,
                'max_price' => $max,
                'avg_price' => $priceValues->count() ? round((float) $priceValues->avg(), 2) : null,
                'difference' => $min !== null && $max !== null ? round($max - $min, 2) : null,
            ],
            'products' => $prices->map(function (DistributorPrice $price) {
                $product = $price->product;

                return [
                    'price_id' => $price->id,
                    'product_id' => $product?->id,
                    'product_name' => $product?->product ?? 'Unbenannter Artikel',
                    'model' => $product?->model,
                    'brand' => optional($product?->brand)->name,
                    'article_group' => optional($product?->articleGroup)->article_group,

                    // products.article_no = Hersteller-Artikelnummer
                    'manufacturer_article_no' => $product?->article_no,

                    // distributor_prices.article_no = Lieferanten-Artikelnummer
                    'supplier_article_no' => $price->article_no,

                    'sku' => $product?->sku,
                    'ean' => $product?->ean,
                    'image_url' => $this->productImageUrl($product),
                    'product_url' => $product ? url('product_details/' . $product->id) : null,

                    'price' => $this->moneyValue($price->price),
                    'purchase_price' => $this->moneyValue($price->purchase_price),
                    'selected_price' => $this->moneyValue($price->purchase_price ?? $price->price),
                    'discount_price' => $this->moneyValue($price->discount_price),
                    'discount_percent' => $this->moneyValue($price->discount_percent),
                    'availability' => $price->availability,
                    'price_date' => $this->safeDate($price->price_date),
                    'status' => $price->status,
                    'notice' => $price->notice,
                ];
            })->values(),
        ]);
    }

    public function productPriceDifference(Distributor $distributor, Product $product)
    {
        $prices = DistributorPrice::query()
            ->with('distributor:id,name,short_name')
            ->where('product_id', $product->id)
            ->orderByRaw('price_date IS NULL ASC')
            ->orderByDesc('price_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy('distributor_id')
            ->map(fn ($group) => $group->first())
            ->values();

        $values = $prices
            ->map(fn (DistributorPrice $row) => $this->moneyValue($row->purchase_price ?? $row->price))
            ->filter(fn ($value) => $value !== null)
            ->values();

        $min = $values->count() ? round((float) $values->min(), 2) : null;
        $max = $values->count() ? round((float) $values->max(), 2) : null;

        return response()->json([
            'selected_distributor' => [
                'id' => $distributor->id,
                'name' => $this->distributorName($distributor),
            ],
            'product' => [
                'id' => $product->id,
                'name' => $product->product,
                'image_url' => $this->productImageUrl($product),
                'url' => url('product_details/' . $product->id),
            ],
            'summary' => [
                'min_price' => $min,
                'max_price' => $max,
                'difference' => $min !== null && $max !== null ? round($max - $min, 2) : null,
                'percent_difference' => $min && $max ? round((($max - $min) / $min) * 100, 2) : null,
                'price_count' => $values->count(),
            ],
            'prices' => $prices->map(function (DistributorPrice $row) use ($min, $distributor) {
                $selected = $this->moneyValue($row->purchase_price ?? $row->price);

                return [
                    'distributor_id' => $row->distributor_id,
                    'distributor_name' => optional($row->distributor)->name
                        ?: optional($row->distributor)->short_name
                        ?: ('Lieferant #' . $row->distributor_id),

                    'is_current_distributor' => (int) $row->distributor_id === (int) $distributor->id,

                    'supplier_article_no' => $row->article_no,
                    'price' => $this->moneyValue($row->price),
                    'purchase_price' => $this->moneyValue($row->purchase_price),
                    'selected_price' => $selected,
                    'discount_price' => $this->moneyValue($row->discount_price),
                    'discount_percent' => $this->moneyValue($row->discount_percent),
                    'availability' => $row->availability,
                    'price_date' => $this->safeDate($row->price_date),
                    'status' => $row->status,
                    'is_best' => $selected !== null && $min !== null && (float) $selected === (float) $min,
                ];
            })->values(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CSV import
    |--------------------------------------------------------------------------
    */

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $real = $request->file('csv_file')->getRealPath();
        $raw = file_get_contents($real);

        $enc = mb_detect_encoding($raw, ['UTF-8', 'Windows-1252', 'ISO-8859-1'], true) ?: 'Windows-1252';

        if ($enc !== 'UTF-8') {
            $raw = mb_convert_encoding($raw, 'UTF-8', $enc);
        }

        $raw = str_replace(["\r\n", "\r"], "\n", $raw);

        $firstLine = strtok($raw, "\n") ?: '';
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        $stream = fopen('php://temp', 'rw');
        fwrite($stream, $raw);
        rewind($stream);

        $rawHeader = fgetcsv($stream, 0, $delimiter);

        if (!$rawHeader) {
            return back()->with('delete_msg', 'Leere oder ungültige CSV.');
        }

        $canonMap = [
            'kurzname' => 'short_name',
            'erstanlagedatum' => 'created_date',
            'typ' => 'type',
            'anrede' => 'salutation',
            'name' => 'name',
            'namenszusatz' => 'name_suffix',
            'strasse' => 'street',
            'straße' => 'street',
            'plz' => 'postal_code',
            'ort' => 'city',
            'telefon' => 'phone',
            'kommunikation' => 'email',
            'mobiltelefon' => 'mobile',
            'konto' => 'account_number',
            'ausgeblendet' => 'is_hidden',
            'anderungsdatum' => 'last_changed_at',
            'änderungsdatum' => 'last_changed_at',
            'bearbeiter' => 'editor',
            'eigentuemer' => 'owner',
            'eigentumer' => 'owner',
            'skonto' => 'cash_discount',
            'zahlungsziel' => 'payment_terms',
            'zahlungszeil' => 'payment_terms',
            'paymentterms' => 'payment_terms',
            'cashdiscount' => 'cash_discount',
        ];

        $norm = fn ($s) => preg_replace('/[^\w]+/u', '', mb_strtolower((string) $s, 'UTF-8'));
        $headerNorm = array_map($norm, $rawHeader);
        $header = array_map(fn ($n) => $canonMap[$n] ?? $n, $headerNorm);

        $imported = 0;
        $updated = 0;

        while (($row = fgetcsv($stream, 0, $delimiter)) !== false) {
            if (count(array_filter($row, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), null);
            } elseif (count($row) > count($header)) {
                $row = array_slice($row, 0, count($header));
            }

            $assoc = @array_combine($header, $row);

            if ($assoc === false) {
                continue;
            }

            $cashDiscount = isset($assoc['cash_discount']) && $assoc['cash_discount'] !== ''
                ? str_replace(',', '.', $assoc['cash_discount'])
                : null;

            $distributor = Distributor::where('short_name', $assoc['short_name'] ?? null)
                ->orWhere('name', $assoc['name'] ?? null)
                ->first();

            if (!$distributor) {
                Distributor::create([
                    'short_name' => $assoc['short_name'] ?? null,
                    'created_date' => $assoc['created_date'] ?? null,
                    'type' => $assoc['type'] ?? null,
                    'salutation' => $assoc['salutation'] ?? null,
                    'name' => $assoc['name'] ?? null,
                    'name_suffix' => $assoc['name_suffix'] ?? null,
                    'street' => $assoc['street'] ?? null,
                    'postal_code' => $assoc['postal_code'] ?? null,
                    'city' => $assoc['city'] ?? null,
                    'phone' => $assoc['phone'] ?? null,
                    'email' => $assoc['email'] ?? null,
                    'mobile' => $assoc['mobile'] ?? null,
                    'account_number' => $assoc['account_number'] ?? null,
                    'cash_discount' => $cashDiscount,
                    'payment_terms' => $assoc['payment_terms'] ?? null,
                    'is_hidden' => !empty($assoc['is_hidden']),
                    'last_changed_at' => $assoc['last_changed_at'] ?? null,
                    'editor' => $assoc['editor'] ?? null,
                    'owner' => $assoc['owner'] ?? null,
                    'status' => 'active',
                ]);

                $imported++;
            } else {
                $distributor->update([
                    'short_name' => $assoc['short_name'] ?? $distributor->short_name,
                    'created_date' => $assoc['created_date'] ?? $distributor->created_date,
                    'type' => $assoc['type'] ?? $distributor->type,
                    'salutation' => $assoc['salutation'] ?? $distributor->salutation,
                    'name' => $assoc['name'] ?? $distributor->name,
                    'name_suffix' => $assoc['name_suffix'] ?? $distributor->name_suffix,
                    'street' => $assoc['street'] ?? $distributor->street,
                    'postal_code' => $assoc['postal_code'] ?? $distributor->postal_code,
                    'city' => $assoc['city'] ?? $distributor->city,
                    'phone' => $assoc['phone'] ?? $distributor->phone,
                    'email' => $assoc['email'] ?? $distributor->email,
                    'mobile' => $assoc['mobile'] ?? $distributor->mobile,
                    'account_number' => $assoc['account_number'] ?? $distributor->account_number,
                    'cash_discount' => $cashDiscount ?? $distributor->cash_discount,
                    'payment_terms' => $assoc['payment_terms'] ?? $distributor->payment_terms,
                    'last_changed_at' => $assoc['last_changed_at'] ?? $distributor->last_changed_at,
                    'editor' => $assoc['editor'] ?? $distributor->editor,
                    'owner' => $assoc['owner'] ?? $distributor->owner,
                ]);

                $updated++;
            }
        }

        fclose($stream);

        return back()->with('save_msg', "CSV Import abgeschlossen: {$imported} neue Lieferanten, {$updated} vorhandene aktualisiert.");
    }

    /*
    |--------------------------------------------------------------------------
    | Distributor CRUD
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $this->validateDistributor($request);

        $distributor = new Distributor();
        $this->fillDistributor($distributor, $validated);

        if ($request->hasFile('image')) {
            $distributor->image = $this->storeDistributorImage($request);
        }

        $distributor->save();

        return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert');
    }

    public function update(Request $request, Distributor $distributor)
    {
        $validated = $this->validateDistributor($request);

        $this->fillDistributor($distributor, $validated);

        if ($request->hasFile('image')) {
            $this->delete_photo($distributor->image);
            $distributor->image = $this->storeDistributorImage($request);
        }

        $distributor->save();

        return redirect()->back()->with('update_msg', 'Der Datensatz wurde erfolgreich aktualisiert');
    }

    public function destroy(Distributor $distributor)
    {
        $this->delete_photo($distributor->image);
        $distributor->delete();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }

    public function publish($id)
    {
        $data = Distributor::findOrFail($id);
        $data->status = 'Published';
        $data->save();

        return redirect()->back()->with('save_msg', 'Das Unternehmen ist jetzt veröffentlicht');
    }

    public function unpublish($id)
    {
        $data = Distributor::findOrFail($id);
        $data->status = 'Unpublished';
        $data->save();

        return redirect()->back()->with('delete_msg', 'Der Artikel wurde nun erfolgreich unveröffentlicht!');
    }

    public function delete_photo($photo)
    {
        if (!empty($photo)) {
            $photoPath = public_path('images/distributor/' . $photo);

            if (file_exists($photoPath)) {
                @unlink($photoPath);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Product price management from product details
    |--------------------------------------------------------------------------
    */

    public function data(Product $product)
    {
        $distributors = Distributor::query()
            ->whereNotIn('status', ['inactive', 'Unpublished', 'inaktiv'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $discountGroups = DiscountGroup::query()
            ->orderBy('discount_group')
            ->get(['id', 'discount_group', 'discount']);

        $prices = DistributorPrice::query()
            ->with('distributor:id,name')
            ->where('product_id', $product->id)
            ->orderByRaw('price_date IS NULL ASC')
            ->orderByDesc('price_date')
            ->orderByDesc('id')
            ->get()
            ->map(fn (DistributorPrice $row) => $this->pricePayload($row));

        return response()->json([
            'distributors' => $distributors,
            'discountGroups' => $discountGroups,
            'prices' => $prices,
        ]);
    }

    public function save(Product $product, Request $request)
    {
        $data = $this->validatePricePayload($request);

        [$price, $discountPrice, $discountPercent, $purchasePrice] = $this->preparePriceValues($data);

        $model = new DistributorPrice();
        $model->fill([
            'product_id' => $product->id,
            'distributor_id' => $data['distributor_id'],
            'discount_group_id' => $data['discount_group_id'] ?? null,
            'article_no' => $data['article_no'] ?? null,
            'price' => $price,
            'discount_price' => $discountPrice,
            'discount_percent' => $discountPercent,
            'purchase_price' => $purchasePrice,
            'price_date' => $data['price_date'] ?? null,
            'availability' => $data['availability'] ?? null,
            'status' => $data['status'] ?? 'Published',
        ]);

        $model->save();
        $model->load('distributor:id,name');

        return response()->json([
            'message' => 'Lieferantenpreis gespeichert.',
            'price' => $this->pricePayload($model),
        ]);
    }

    public function updatePrice(Product $product, DistributorPrice $price, Request $request)
    {
        if ((int) $price->product_id !== (int) $product->id) {
            abort(404);
        }

        $data = $this->validatePricePayload($request, true);

        [$priceVal, $discountEuroVal, $discountPercVal, $purchaseVal] = $this->preparePriceValues($data);

        $price->fill([
            'distributor_id' => (int) $data['distributor_id'],
            'discount_group_id' => $data['discount_group_id'] ?? null,
            'article_no' => $data['article_no'] ?? null,
            'price' => $priceVal,
            'discount_price' => $discountEuroVal,
            'discount_percent' => $discountPercVal,
            'purchase_price' => $purchaseVal,
            'price_date' => $data['price_date'] ?? null,
            'availability' => $data['availability'] ?? null,
            'status' => $data['status'] ?? 'Published',
        ]);

        $price->save();
        $price->load('distributor:id,name');

        return response()->json([
            'message' => 'Lieferantenpreis aktualisiert.',
            'price' => $this->pricePayload($price),
        ]);
    }

    public function delete(DistributorPrice $price)
    {
        $price->delete();

        return response()->json([
            'message' => 'Lieferantenpreis gelöscht.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Legacy distinct page
    |--------------------------------------------------------------------------
    */

    public function distinct()
    {
        $search = request()->query('search');
        $max = Product::where('product', '=', $search)->max('purchase_price');
        $min = Product::where('product', '=', $search)->min('purchase_price');

        if ($search) {
            $data['product'] = Product::select('product')->distinct('product')->get();
            $data['image'] = ProductImage::all();

            $data['content'] = DB::table('products')
                ->join('distributors', 'distributors.id', '=', 'products.distributor_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->select('products.*', 'brands.name as brand', 'distributors.name as distributor')
                ->where('products.product', '=', $search)
                ->get();

            $data['best'] = DB::table('products')
                ->join('distributors', 'distributors.id', '=', 'products.distributor_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->select('products.*', 'brands.name as brand', 'distributors.name as distributor')
                ->where('products.purchase_price', $min)
                ->where('products.product', '=', $search)
                ->get();

            $data['high'] = DB::table('products')
                ->join('distributors', 'distributors.id', '=', 'products.distributor_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->select('products.*', 'brands.name as brand', 'distributors.name as distributor')
                ->where('products.purchase_price', $max)
                ->where('products.product', '=', $search)
                ->get();

            return view('admin.product.distinct.distinct', $data);
        }

        $data['product'] = Product::select('product')->distinct('product')->get();
        $data['image'] = ProductImage::all();

        $data['content'] = DB::table('products')
            ->join('distributors', 'distributors.id', '=', 'products.distributor_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->select('products.*', 'brands.name as brand', 'distributors.name as distributor')
            ->get();

        $data['best'] = DB::table('products')
            ->join('distributors', 'distributors.id', '=', 'products.distributor_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->select('products.*', 'brands.name as brand', 'distributors.name as distributor')
            ->where('products.purchase_price', $min)
            ->take(1)
            ->get();

        $data['high'] = DB::table('products')
            ->join('distributors', 'distributors.id', '=', 'products.distributor_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->select('products.*', 'brands.name as brand', 'distributors.name as distributor')
            ->where('products.purchase_price', $max)
            ->take(1)
            ->get();

        return view('admin.product.distinct.distinct', $data);
    }

    public function destroyLegacy($id)
    {
        $distributor = Distributor::findOrFail($id);
        $this->delete_photo($distributor->image);
        $distributor->delete();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }

    public function publishLegacy($id)
    {
        Distributor::findOrFail($id)->update(['status' => 'Published']);

        return redirect()->back();
    }

    public function unpublishLegacy($id)
    {
        Distributor::findOrFail($id)->update(['status' => 'Unpublished']);

        return redirect()->back();
    }

    /*
    |--------------------------------------------------------------------------
    | Internal helpers
    |--------------------------------------------------------------------------
    */

    private function validateDistributor(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'cash_discount' => 'nullable|numeric|min:0|max:100',
            'payment_terms' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,Published,Unpublished,inactive',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:4096',
        ]);

        if (isset($validated['cash_discount'])) {
            $validated['cash_discount'] = str_replace(',', '.', (string) $validated['cash_discount']);
        }

        return $validated;
    }

    private function fillDistributor(Distributor $distributor, array $validated): void
    {
        $distributor->name = $validated['name'];
        $distributor->short_name = $validated['short_name'] ?? null;
        $distributor->street = $validated['street'] ?? null;
        $distributor->postal_code = $validated['postal_code'] ?? null;
        $distributor->city = $validated['city'] ?? null;
        $distributor->phone = $validated['phone'] ?? null;
        $distributor->email = $validated['email'] ?? null;
        $distributor->mobile = $validated['mobile'] ?? null;
        $distributor->account_number = $validated['account_number'] ?? null;
        $distributor->cash_discount = $validated['cash_discount'] ?? null;
        $distributor->payment_terms = $validated['payment_terms'] ?? null;
        $distributor->status = $validated['status'] ?? ($distributor->status ?: 'Unpublished');
    }

    private function storeDistributorImage(Request $request): string
    {
        $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move(public_path('images/distributor'), $imageName);

        return $imageName;
    }

    private function validatePricePayload(Request $request, bool $isUpdate = false): array
    {
        $payload = $request->all();

        $toNull = static function ($value) {
            if ($value === null) {
                return null;
            }

            $value = trim((string) $value);

            return $value === '' ? null : $value;
        };

        foreach ([
            'article_no',
            'price',
            'discount_price',
            'discount_percent',
            'purchase_price',
            'price_date',
            'availability',
            'discount_group_id',
            'status',
        ] as $key) {
            if (array_key_exists($key, $payload)) {
                $payload[$key] = $toNull($payload[$key]);
            }
        }

        foreach (['price', 'discount_price', 'discount_percent', 'purchase_price'] as $key) {
            if (array_key_exists($key, $payload)) {
                $payload[$key] = $this->decimalInput($payload[$key]);
            }
        }

        return validator($payload, [
            'distributor_id' => 'required|integer|exists:distributors,id',
            'article_no' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'purchase_price' => 'nullable|numeric|min:0',
            'price_date' => 'nullable|date',
            'availability' => 'nullable|string|max:255',
            'discount_group_id' => 'nullable|integer|exists:discount_groups,id',
            'status' => $isUpdate ? 'required|in:Published,Unpublished' : 'nullable|in:Published,Unpublished',
        ])->validate();
    }

    private function preparePriceValues(array $data): array
    {
        if (
            (!array_key_exists('discount_percent', $data) || $data['discount_percent'] === null)
            && !empty($data['discount_group_id'])
        ) {
            $group = DiscountGroup::find($data['discount_group_id']);

            if ($group && $group->discount !== null) {
                $data['discount_percent'] = (float) $group->discount;
            }
        }

        $hasPrice = isset($data['price']) && $data['price'] !== null && (float) $data['price'] > 0;
        $hasDiscE = array_key_exists('discount_price', $data) && $data['discount_price'] !== null;
        $hasDiscP = array_key_exists('discount_percent', $data) && $data['discount_percent'] !== null;
        $hasEk = isset($data['purchase_price']) && $data['purchase_price'] !== null && (float) $data['purchase_price'] > 0;

        if ($hasEk && !$hasPrice && !$hasDiscE && !$hasDiscP) {
            return [
                null,
                null,
                null,
                (float) $data['purchase_price'],
            ];
        }

        return $this->normalizePrices(
            $data['price'] ?? null,
            $data['discount_price'] ?? null,
            $data['discount_percent'] ?? null,
            $data['purchase_price'] ?? null
        );
    }

    protected function normalizePrices($price, $discountPrice, $discountPercent, $purchasePrice): array
    {
        $price = $price !== null ? (float) $price : null;
        $discountPrice = $discountPrice !== null ? (float) $discountPrice : null;
        $discountPercent = $discountPercent !== null ? (float) $discountPercent : null;
        $purchasePrice = $purchasePrice !== null ? (float) $purchasePrice : null;

        $hasPrice = $price !== null && $price > 0;
        $hasDiscEuro = $discountPrice !== null && $discountPrice >= 0;
        $hasDiscPct = $discountPercent !== null && $discountPercent >= 0;
        $hasPurchase = $purchasePrice !== null && $purchasePrice > 0;

        if (!($hasPrice || $hasDiscEuro || $hasDiscPct || $hasPurchase)) {
            throw ValidationException::withMessages([
                'price' => ['Bitte geben Sie mindestens einen Preis / Rabattwert an.'],
            ]);
        }

        if ($hasPrice && $hasDiscPct) {
            $discountPrice = $price * $discountPercent / 100;
            $purchasePrice = $price - $discountPrice;
        } elseif ($hasPrice && $hasDiscEuro) {
            if ($discountPrice > $price) {
                throw ValidationException::withMessages([
                    'discount_price' => ['Rabatt darf nicht größer als der Preis sein.'],
                ]);
            }

            $discountPercent = $price > 0 ? ($discountPrice / $price) * 100 : 0;
            $purchasePrice = $price - $discountPrice;
        } elseif ($hasPrice && $hasPurchase) {
            if ($purchasePrice > $price) {
                throw ValidationException::withMessages([
                    'purchase_price' => ['Einkaufspreis darf nicht größer als der UVP sein.'],
                ]);
            }

            $discountPrice = $price - $purchasePrice;
            $discountPercent = $price > 0 ? ($discountPrice / $price) * 100 : 0;
        } elseif ($hasPurchase && $hasDiscPct) {
            if ($discountPercent >= 100) {
                throw ValidationException::withMessages([
                    'discount_percent' => ['Rabatt in % muss kleiner als 100 sein.'],
                ]);
            }

            $price = $purchasePrice / (1 - $discountPercent / 100);
            $discountPrice = $price - $purchasePrice;
        } elseif ($hasPurchase && $hasDiscEuro) {
            $price = $purchasePrice + $discountPrice;
            $discountPercent = $price > 0 ? ($discountPrice / $price) * 100 : 0;
        } else {
            throw ValidationException::withMessages([
                'price' => ['Kombination der Werte reicht nicht aus, um die Preise eindeutig zu berechnen.'],
            ]);
        }

        if ($discountPercent !== null) {
            $discountPercent = round($discountPercent);
        }

        return [
            round((float) $price, 2),
            round((float) $discountPrice, 2),
            $discountPercent,
            round((float) $purchasePrice, 2),
        ];
    }

    private function pricePayload(DistributorPrice $row): array
    {
        return [
            'id' => $row->id,
            'distributor_id' => $row->distributor_id,
            'distributor_name' => optional($row->distributor)->name,
            'article_no' => $row->article_no,
            'price' => $this->moneyValue($row->price),
            'discount_price' => $this->moneyValue($row->discount_price),
            'discount_percent' => $this->moneyValue($row->discount_percent),
            'purchase_price' => $this->moneyValue($row->purchase_price),
            'price_date' => $this->safeDateForInput($row->price_date),
            'availability' => $row->availability,
            'status' => $row->status,
            'discount_group_id' => $row->discount_group_id,
        ];
    }

    private function decimalInput(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = str_replace(["\u{00A0}", ' '], '', (string) $value);
        $value = str_replace(',', '.', $value);

        return $value;
    }

    private function moneyValue(mixed $value): ?float
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

    private function safeDate(mixed $value): ?string
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

    private function safeDateForInput(mixed $value): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof CarbonInterface) {
            return $value->format('Y-m-d');
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function productImageUrl(?Product $product): ?string
    {
        if (!$product) {
            return null;
        }

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

    public function byBrand(Request $request)
    {
        $brandId = (int) $request->query('brand_id', 0);
        $search = trim((string) $request->query('q', ''));

        $query = Distributor::query()
            ->select('distributors.id', 'distributors.name', 'distributors.short_name')
            ->whereNotIn('distributors.status', ['inactive', 'Unpublished', 'inaktiv']);

        if ($brandId > 0) {
            $query->where(function ($q) use ($brandId) {
                $q->whereExists(function ($sub) use ($brandId) {
                    $sub->select(DB::raw(1))
                        ->from('distributor_prices')
                        ->join('products', 'products.id', '=', 'distributor_prices.product_id')
                        ->whereColumn('distributor_prices.distributor_id', 'distributors.id')
                        ->where('products.brand_id', $brandId);
                });

                // Legacy fallback: if old products still store distributor_id directly
                $q->orWhereExists(function ($sub) use ($brandId) {
                    $sub->select(DB::raw(1))
                        ->from('products')
                        ->whereColumn('products.distributor_id', 'distributors.id')
                        ->where('products.brand_id', $brandId);
                });
            });
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('distributors.name', 'like', "%{$search}%")
                    ->orWhere('distributors.short_name', 'like', "%{$search}%");
            });
        }

        $items = $query
            ->orderByRaw('COALESCE(distributors.name, distributors.short_name) asc')
            ->limit(100)
            ->get()
            ->map(function (Distributor $distributor) {
                return [
                    'id' => $distributor->id,
                    'name' => $distributor->name
                        ?: $distributor->short_name
                        ?: ('Lieferant #' . $distributor->id),
                ];
            })
            ->values();

        return response()->json([
            'items' => $items,
        ]);
    }
}