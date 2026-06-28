<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\NewLeads;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $products = Product::query()
            ->with([
                'brand:id,name',
                'firstImage' => function ($query) {
                    $query->select([
                        'product_images.id',
                        'product_images.product_id',
                        'product_images.image',
                    ]);
                },
            ])
            ->whereIn('products.status', ['Published', 'active'])
            ->select([
                'products.id',
                'products.product',
                'products.model',
                'products.article_no',
                'products.ean',
                'products.brand_id',
            ])
            ->orderBy('products.product')
            ->get()
            ->map(function ($product) {
                return (object) [
                    'id' => $product->id,
                    'product' => $product->product,
                    'model' => $product->model,
                    'article_no' => $product->article_no,
                    'ean' => $product->ean,
                    'brand_id' => $product->brand_id,
                    'brand_name' => $product->brand?->name,
                    'image_url' => $product->firstImage?->image
                        ? asset('images/products/' . ltrim($product->firstImage->image, '/'))
                        : null,
                ];
            });

        $responsible = Employee::query()
            ->select('id', 'name', 'lastname')
            ->orderBy('name')
            ->orderBy('lastname')
            ->get();

        $customers = NewLeads::query()
            ->select('id', 'name', 'lastname', 'firma')
            ->orderBy('name')
            ->orderBy('lastname')
            ->get();

        $branches = Branch::query()
            ->select('id', 'branch')
            ->orderBy('branch')
            ->get();

        $quantityUnits = collect([
            'Stk',
            'Pcs',
            'm',
            'cm',
            'mm',
            'm²',
            'm³',
            'kg',
            'g',
            't',
            'l',
            'ml',
            'Pack',
            'Set',
            'Rolle',
            'Karton',
            'Palette',
        ]);


        return view('admin.product.inventory.inventory', [
            'products' => $products,
            'responsible' => $responsible,
            'branches' => $branches,
            'customers' => $customers,
            'quantityUnits' => $quantityUnits,
            
        ]);
    }

    public function analytics(): JsonResponse
    {
        $base = Inventory::query();

        $totalEntries = (clone $base)->count();
        $totalQuantity = (clone $base)->sum('quantity');
        $uniqueProducts = (clone $base)->distinct('product_id')->count('product_id');
        $lowStockCount = (clone $base)->where('quantity', '<=', 5)->count();
        $noResponsibleCount = (clone $base)->whereNull('responsible_id')->count();

        return response()->json([
            'success' => true,
            'analytics' => [
                'total_entries' => (int) $totalEntries,
                'total_quantity' => (float) $totalQuantity,
                'unique_products' => (int) $uniqueProducts,
                'low_stock_count' => (int) $lowStockCount,
                'no_responsible_count' => (int) $noResponsibleCount,
            ],
        ]);
    }

    public function listAjax(Request $request): JsonResponse
    {
        $perPage = max(6, min(100, (int) $request->input('per_page', 20)));
        $page = max(1, (int) $request->input('page', 1));
        $search = trim((string) $request->input('search', ''));
        $productId = $request->input('product_id');
        $branchId = $request->input('branch_id');
        $responsibleId = $request->input('responsible_id');
        $stockFilter = $request->input('stock_filter');

        $allowedSorts = [
            'id' => 'inventories.id',
            'product' => 'products.product',
            'model' => 'products.model',
            'article_no' => 'inventories.article_no',
            'serial_no' => 'inventories.serial_no',
            'location' => 'inventories.location',
            'quantity' => 'inventories.quantity',
            'responsible' => 'employees.name',
            'brand' => 'brands.name',
            'created_at' => 'inventories.created_at',
        ];

        $sort = $request->input('sort', 'id');
        $direction = strtolower((string) $request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $sortColumn = $allowedSorts[$sort] ?? $allowedSorts['id'];

        $imageSub = DB::table('product_images')
            ->select('product_id', DB::raw('MIN(id) as min_id'))
            ->groupBy('product_id');

        $query = Inventory::query()
            ->leftJoin('products', 'products.id', '=', 'inventories.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('employees', 'employees.id', '=', 'inventories.responsible_id')
            ->leftJoin('branches', 'branches.id', '=', 'inventories.location')
            ->leftJoinSub($imageSub, 'first_product_image', function ($join) {
                $join->on('first_product_image.product_id', '=', 'inventories.product_id');
            })
            ->leftJoin('product_images', 'product_images.id', '=', 'first_product_image.min_id')
            ->select([
                'inventories.*',
                'products.product as product_name',
                'products.model as product_model',
                'products.article_no as product_article_no',
                'products.ean as product_ean',
                'brands.name as brand_name',
                'employees.name as responsible_name',
                'employees.lastname as responsible_lastname',
                'branches.branch as branch_name',
                'product_images.image as product_image',
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('products.product', 'like', "%{$search}%")
                    ->orWhere('products.model', 'like', "%{$search}%")
                    ->orWhere('products.article_no', 'like', "%{$search}%")
                    ->orWhere('products.ean', 'like', "%{$search}%")
                    ->orWhere('brands.name', 'like', "%{$search}%")
                    ->orWhere('inventories.serial_no', 'like', "%{$search}%")
                    ->orWhere('inventories.article_no', 'like', "%{$search}%")
                    ->orWhere('inventories.ean', 'like', "%{$search}%")
                    ->orWhere('inventories.manual_no', 'like', "%{$search}%")
                    ->orWhere('inventories.location_label', 'like', "%{$search}%")
                    ->orWhere('inventories.inventory_category', 'like', "%{$search}%")
                    ->orWhere('inventories.room_name', 'like', "%{$search}%")
                    ->orWhere('inventories.room_number', 'like', "%{$search}%")
                    ->orWhere('inventories.rack_name', 'like', "%{$search}%")
                    ->orWhere('inventories.shelf', 'like', "%{$search}%")
                    ->orWhere('inventories.row', 'like', "%{$search}%")
                    ->orWhere('inventories.column', 'like', "%{$search}%")
                    ->orWhere('branches.branch', 'like', "%{$search}%")
                    ->orWhere('employees.name', 'like', "%{$search}%")
                    ->orWhere('employees.lastname', 'like', "%{$search}%");
            });
        }

        if (!empty($productId)) {
            $query->where('inventories.product_id', $productId);
        }

        if (!empty($branchId)) {
            $query->where('inventories.location', $branchId);
        }

        if (!empty($responsibleId)) {
            $query->where('inventories.responsible_id', $responsibleId);
        }

        if ($stockFilter === 'low') {
            $query->where('inventories.quantity', '<=', 5);
        } elseif ($stockFilter === 'zero') {
            $query->where('inventories.quantity', '<=', 0);
        } elseif ($stockFilter === 'available') {
            $query->where('inventories.quantity', '>', 0);
        }

        $query->orderBy($sortColumn, $direction);

        $items = $query->paginate($perPage, ['*'], 'page', $page);

        $data = collect($items->items())->map(function ($row) {
            $imageUrl = null;

            if (!empty($row->product_image)) {
                $imageUrl = asset('images/products/' . ltrim($row->product_image, '/'));
            }

            return [
                'id' => $row->id,
                'product_id' => $row->product_id,
                'product_name' => $row->product_name,
                'product_model' => $row->product_model,
                'product_article_no' => $row->product_article_no,
                'product_ean' => $row->product_ean,
                'brand_name' => $row->brand_name,
                'serial_no' => $row->serial_no,
                'article_no' => $row->article_no,
                'ean' => $row->ean,
                'manual_no' => $row->manual_no,
                'location' => $row->location,
                'location_label' => $row->location_label,
                'branch_name' => $row->branch_name,
                'inventory_category' => $row->inventory_category,
                'room_name' => $row->room_name,
                'room_number' => $row->room_number,
                'rack_name' => $row->rack_name,
                'shelf' => $row->shelf,
                'row' => $row->row,
                'column' => $row->column,
                'latitude' => $row->latitude,
                'longitude' => $row->longitude,
                'quantity' => (float) ($row->quantity ?? 0),
                'responsible_id' => $row->responsible_id,
                'responsible_name' => trim(($row->responsible_name ?? '') . ' ' . ($row->responsible_lastname ?? '')),
                'product_image_url' => $imageUrl,
                'created_at' => optional($row->created_at)->format('d.m.Y H:i'),
                'updated_at' => optional($row->updated_at)->format('d.m.Y H:i'),
                'quantity_unit' => $row->quantity_unit,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ],
        ]);
    }

    public function storeAjax(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'serial_no' => 'nullable|string|max:255',
            'article_no' => 'nullable|string|max:255',
            'ean' => 'nullable|string|max:255',
            'manual_no' => 'nullable|string|max:255',
            'location' => 'required',
            'location_label' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'inventory_category' => 'nullable|string|max:255',
            'room_name' => 'nullable|string|max:255',
            'room_number' => 'nullable|string|max:255',
            'rack_name' => 'nullable|string|max:255',
            'shelf' => 'nullable|string|max:255',
            'row' => 'required|string|max:255',
            'column' => 'nullable|string|max:255',
            'quantity' => 'nullable|numeric|min:0',
            'responsible_id' => 'nullable|exists:employees,id',
            'quantity_unit' => 'nullable|string|max:100',
        ], [
            'product_id.required' => 'Produkt ist erforderlich.',
            'location.required' => 'Wo wird das Produkt gelagert?',
            'row.required' => 'Zeile ist erforderlich.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $inventory = new Inventory();
        $inventory->product_id = $request->product_id;
        $inventory->serial_no = $request->serial_no;
        $inventory->article_no = $request->article_no;
        $inventory->ean = $request->ean;
        $inventory->manual_no = $request->manual_no;
        $inventory->location = $request->location;
        $inventory->location_label = $request->location_label;
        $inventory->latitude = $request->latitude;
        $inventory->longitude = $request->longitude;
        $inventory->inventory_category = $request->inventory_category;
        $inventory->room_name = $request->room_name;
        $inventory->room_number = $request->room_number;
        $inventory->rack_name = $request->rack_name;
        $inventory->shelf = $request->shelf;
        $inventory->row = $request->row;
        $inventory->column = $request->column;
        $inventory->quantity = $request->quantity ?? 0;
        $inventory->responsible_id = $request->responsible_id;
        $inventory->add_by = auth()->user()->name;
        $inventory->add_date = now();
        $inventory->quantity_unit = $request->quantity_unit;
        $inventory->save();

        return response()->json([
            'success' => true,
            'message' => 'Inventareintrag wurde erfolgreich gespeichert.',
        ]);
    }

    public function updateAjax(Request $request, int $id): JsonResponse
    {
        $inventory = Inventory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'serial_no' => 'nullable|string|max:255',
            'article_no' => 'nullable|string|max:255',
            'ean' => 'nullable|string|max:255',
            'manual_no' => 'nullable|string|max:255',
            'location' => 'required',
            'location_label' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'inventory_category' => 'nullable|string|max:255',
            'room_name' => 'nullable|string|max:255',
            'room_number' => 'nullable|string|max:255',
            'rack_name' => 'nullable|string|max:255',
            'shelf' => 'nullable|string|max:255',
            'row' => 'required|string|max:255',
            'column' => 'nullable|string|max:255',
            'quantity' => 'nullable|numeric|min:0',
            'responsible_id' => 'nullable|exists:employees,id',
            'quantity_unit' => 'nullable|string|max:100',
        ], [
            'product_id.required' => 'Produkt ist erforderlich.',
            'location.required' => 'Wo wird das Produkt gelagert?',
            'row.required' => 'Zeile ist erforderlich.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $inventory->product_id = $request->product_id;
        $inventory->serial_no = $request->serial_no;
        $inventory->article_no = $request->article_no;
        $inventory->ean = $request->ean;
        $inventory->manual_no = $request->manual_no;
        $inventory->location = $request->location;
        $inventory->location_label = $request->location_label;
        $inventory->latitude = $request->latitude;
        $inventory->longitude = $request->longitude;
        $inventory->inventory_category = $request->inventory_category;
        $inventory->room_name = $request->room_name;
        $inventory->room_number = $request->room_number;
        $inventory->rack_name = $request->rack_name;
        $inventory->shelf = $request->shelf;
        $inventory->row = $request->row;
        $inventory->column = $request->column;
        $inventory->quantity = $request->quantity ?? 0;
        $inventory->responsible_id = $request->responsible_id;
        $inventory->quantity_unit = $request->quantity_unit;
        $inventory->save();

        return response()->json([
            'success' => true,
            'message' => 'Inventareintrag wurde erfolgreich aktualisiert.',
        ]);
    }

    public function destroyAjax(int $id): JsonResponse
    {
        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inventareintrag wurde erfolgreich gelöscht.',
        ]);
    }

    public function fetchProductData(int $product_id): JsonResponse
    {
        $product = Product::query()
            ->with([
                'brand:id,name',
                'firstImage' => function ($query) {
                    $query->select([
                        'product_images.id',
                        'product_images.product_id',
                        'product_images.image',
                    ]);
                },
            ])
            ->select([
                'products.id',
                'products.article_no',
                'products.ean',
                'products.product',
                'products.model',
                'products.brand_id',
            ])
            ->find($product_id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produkt nicht gefunden.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'product' => $product->product,
                'model' => $product->model,
                'article_no' => $product->article_no,
                'ean' => $product->ean,
                'brand_name' => $product->brand?->name,
                'image_url' => $product->firstImage?->image
                    ? asset('images/products/' . ltrim($product->firstImage->image, '/'))
                    : null,
            ],
        ]);
    }

    public function historyAjax(Request $request): JsonResponse
    {
        $perPage = max(10, min(100, (int) $request->input('per_page', 20)));
        $page = max(1, (int) $request->input('page', 1));
        $search = trim((string) $request->input('search', ''));

        $query = InventoryHistory::query()
            ->with([
                'inventory:id,product_id,serial_no,article_no,ean,quantity',
                'product:id,product,model,article_no,ean',
                'customer:id,name,lastname,firma',
                'employee:id,name,lastname',
            ])
            ->latest('used_at')
            ->latest('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('usage_location', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($qq) use ($search) {
                        $qq->where('product', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('article_no', 'like', "%{$search}%")
                            ->orWhere('ean', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('firma', 'like', "%{$search}%");
                    })
                    ->orWhereHas('employee', function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%");
                    });
            });
        }

        $items = $query->paginate($perPage, ['*'], 'page', $page);

        $data = collect($items->items())->map(function ($row) {
            $customerName = trim(
                ($row->customer?->firma ?? '') . ' ' .
                ($row->customer?->name ?? '') . ' ' .
                ($row->customer?->lastname ?? '')
            );

            $employeeName = trim(
                ($row->employee?->name ?? '') . ' ' .
                ($row->employee?->lastname ?? '')
            );

            return [
                'id' => $row->id,
                'type' => $row->type,
                'inventory_id' => $row->inventory_id,
                'product_name' => $row->product?->product,
                'product_model' => $row->product?->model,
                'article_no' => $row->product?->article_no ?? $row->inventory?->article_no,
                'ean' => $row->product?->ean ?? $row->inventory?->ean,
                'customer_name' => $customerName ?: '-',
                'employee_name' => $employeeName ?: '-',
                'usage_location' => $row->usage_location ?: '-',
                'quantity_before' => (float) $row->quantity_before,
                'quantity_used' => (float) $row->quantity_used,
                'quantity_after' => (float) $row->quantity_after,
                'note' => $row->note,
                'used_at' => optional($row->used_at)->format('d.m.Y H:i'),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
            ],
        ]);
    }

    public function useProductAjax(Request $request, int $id): JsonResponse
    {
        $inventory = Inventory::with('product')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:new_leads,id',
            'usage_location' => 'required|string|max:255',
            'quantity_used' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:5000',
            'used_at' => 'nullable|date',
        ], [
            'usage_location.required' => 'Bitte den Einsatzort angeben.',
            'quantity_used.required' => 'Bitte die verwendete Menge angeben.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $usedQty = (float) $request->quantity_used;
        $beforeQty = (float) ($inventory->quantity ?? 0);

        if ($usedQty <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Die verwendete Menge muss größer als 0 sein.',
            ], 422);
        }

        if ($usedQty > $beforeQty) {
            return response()->json([
                'success' => false,
                'message' => 'Verwendete Menge ist größer als aktueller Bestand.',
            ], 422);
        }

        DB::transaction(function () use ($request, $inventory, $usedQty, $beforeQty) {
            $afterQty = $beforeQty - $usedQty;

            $inventory->quantity = $afterQty;
            $inventory->save();

            InventoryHistory::create([
                'inventory_id' => $inventory->id,
                'product_id' => $inventory->product_id,
                'customer_id' => $request->customer_id,
                'used_by' => auth()->user()->name,
                'type' => 'used',
                'quantity_before' => $beforeQty,
                'quantity_used' => $usedQty,
                'quantity_after' => $afterQty,
                'usage_location' => $request->usage_location,
                'note' => $request->note,
                'used_at' => $request->filled('used_at')
                    ? Carbon::parse($request->used_at)
                    : now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Produktverbrauch wurde gespeichert und Bestand reduziert.',
        ]);
    }

    public function findByProductAjax(int $productId): JsonResponse
{
    $imageSub = DB::table('product_images')
        ->select('product_id', DB::raw('MIN(id) as min_id'))
        ->groupBy('product_id');

    $item = Inventory::query()
        ->leftJoin('products', 'products.id', '=', 'inventories.product_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
        ->leftJoin('employees', 'employees.id', '=', 'inventories.responsible_id')
        ->leftJoin('branches', 'branches.id', '=', 'inventories.location')
        ->leftJoinSub($imageSub, 'first_product_image', function ($join) {
            $join->on('first_product_image.product_id', '=', 'inventories.product_id');
        })
        ->leftJoin('product_images', 'product_images.id', '=', 'first_product_image.min_id')
        ->where('inventories.product_id', $productId)
        ->select([
            'inventories.*',
            'products.product as product_name',
            'products.model as product_model',
            'products.article_no as product_article_no',
            'products.ean as product_ean',
            'brands.name as brand_name',
            'employees.name as responsible_name',
            'employees.lastname as responsible_lastname',
            'branches.branch as branch_name',
            'product_images.image as product_image',
        ])
        ->orderByDesc('inventories.id')
        ->first();

    if (!$item) {
        return response()->json([
            'success' => true,
            'exists' => false,
            'inventory' => null,
        ]);
    }

    return response()->json([
        'success' => true,
        'exists' => true,
        'inventory' => [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'product_name' => $item->product_name,
            'product_model' => $item->product_model,
            'article_no' => $item->article_no ?: $item->product_article_no,
            'ean' => $item->ean ?: $item->product_ean,
            'brand_name' => $item->brand_name,
            'serial_no' => $item->serial_no,
            'manual_no' => $item->manual_no,
            'location' => $item->location,
            'branch_name' => $item->branch_name,
            'shelf' => $item->shelf,
            'row' => $item->row,
            'quantity' => (float) ($item->quantity ?? 0),
            'responsible_id' => $item->responsible_id,
            'responsible_name' => trim(($item->responsible_name ?? '') . ' ' . ($item->responsible_lastname ?? '')),
            'latitude' => $item->latitude,
            'longitude' => $item->longitude,
            'product_image_url' => !empty($item->product_image)
                ? asset('images/products/' . ltrim($item->product_image, '/'))
                : null,
        ],
    ]);
}

 public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id'          => ['required', 'integer', 'exists:products,id'],
            'serial_no'           => ['nullable', 'string', 'max:255'],
            'article_no'          => ['nullable', 'string', 'max:255'],
            'ean'                 => ['nullable', 'string', 'max:255'],
            'manual_no'           => ['nullable', 'string', 'max:255'],
            'location'            => ['nullable', 'string', 'max:255'],
            'location_label'      => ['nullable', 'string', 'max:255'],
            'latitude'            => ['nullable', 'numeric'],
            'longitude'           => ['nullable', 'numeric'],
            'inventory_category'  => ['nullable', 'string', 'max:255'],
            'room_name'           => ['nullable', 'string', 'max:255'],
            'room_number'         => ['nullable', 'string', 'max:255'],
            'rack_name'           => ['nullable', 'string', 'max:255'],
            'row'                 => ['nullable', 'string', 'max:255'],
            'column'              => ['nullable', 'string', 'max:255'],
            'shelf'               => ['nullable', 'string', 'max:255'],
            'quantity'            => ['required', 'numeric', 'min:0'],
            'responsible_id'      => ['nullable', 'integer', 'exists:employees,id'],
            'add_date'            => ['nullable', 'date'],
        ], [
            'product_id.required' => 'Produkt ist erforderlich.',
            'product_id.exists'   => 'Das gewählte Produkt existiert nicht.',
            'quantity.required'   => 'Menge ist erforderlich.',
            'quantity.numeric'    => 'Menge muss eine Zahl sein.',
            'quantity.min'        => 'Menge darf nicht negativ sein.',
            'responsible_id.exists' => 'Der Verantwortliche wurde nicht gefunden.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validierungsfehler.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $product = Product::query()->find($data['product_id']);

        $inventory = Inventory::create([
            'product_id'         => $data['product_id'],
            'serial_no'          => $data['serial_no'] ?? null,
            'article_no'         => $data['article_no'] ?? ($product->article_no ?? null),
            'ean'                => $data['ean'] ?? ($product->ean ?? null),
            'manual_no'          => $data['manual_no'] ?? null,
            'location'           => $data['location'] ?? null,
            'location_label'     => $data['location_label'] ?? null,
            'latitude'           => $data['latitude'] ?? null,
            'longitude'          => $data['longitude'] ?? null,
            'inventory_category' => $data['inventory_category'] ?? null,
            'room_name'          => $data['room_name'] ?? null,
            'room_number'        => $data['room_number'] ?? null,
            'rack_name'          => $data['rack_name'] ?? null,
            'row'                => $data['row'] ?? null,
            'column'             => $data['column'] ?? null,
            'shelf'              => $data['shelf'] ?? null,
            'quantity'           => $data['quantity'],
            'responsible_id'     => $data['responsible_id'] ?? null,
            'add_by'             => auth()->user()->name,
            'add_date'           => $data['add_date'] ?? now(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Inventar erfolgreich gespeichert.',
            'data'    => $inventory->load(['product', 'responsible']),
        ]);
    }
}