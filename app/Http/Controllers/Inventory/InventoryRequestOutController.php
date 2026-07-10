<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\Inventory;
use App\Models\InventoryRequestOut;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class InventoryRequestOutController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['update']);
        $this->middleware('permission:Product,delete')->only(['destroy']);
    }

    public function index()
    {
        $products = Product::query()
            ->select('id', 'product')
            ->orderBy('product')
            ->get();

        $employees = Employee::query()
            ->select('id', 'name', 'lastname')
            ->orderBy('name')
            ->get();

        return view('admin.product.inventory.request_out.index', compact('products', 'employees'));
    }

    public function analytics(): JsonResponse
    {
        $totalProducts = Product::whereHas('inventory')->count();

        $availableProducts = Inventory::where('quantity', '>', 0)->count();

        $emptyProducts = Inventory::where('quantity', '<=', 0)->count();

        $totalRequests = InventoryRequestOut::count();

        $todayRequests = InventoryRequestOut::whereDate('created_at', today())->count();

        return response()->json([
            'status' => true,
            'analytics' => [
                'total_products' => $totalProducts,
                'available_products' => $availableProducts,
                'empty_products' => $emptyProducts,
                'total_requests' => $totalRequests,
                'today_requests' => $todayRequests,
            ],
        ]);
    }

  public function products(Request $request): JsonResponse
    {
        $search = trim((string) $request->get('search'));
        $productId = $request->get('product_id');
        $stock = $request->get('stock');
        $sort = $request->get('sort', 'product');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = [
            'id' => 'products.id',
            'product' => 'products.product',
            'model' => 'products.model',
            'brand' => 'brands.name',
            'distributor' => 'distributors.name',
            'quantity' => 'inventories.quantity',
            'purchase_price' => 'distributor_prices.purchase_price',
        ];

        $sortColumn = $allowedSorts[$sort] ?? 'products.product';
        $direction = in_array(strtolower($direction), ['asc', 'desc'], true) ? $direction : 'asc';

        $query = Product::query()
            ->join('inventories', 'inventories.product_id', '=', 'products.id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('distributor_prices', function ($join) {
                $join->on('distributor_prices.product_id', '=', 'products.id')
                    ->where('distributor_prices.status', '=', 'Published');
            })
            ->leftJoin('distributors', 'distributors.id', '=', 'distributor_prices.distributor_id')
            ->select([
                'products.id',
                'products.product',
                'products.model',
                'products.brand_id',

                'inventories.quantity',
                'inventories.location',
                'inventories.row',
                'inventories.shelf',
                'inventories.serial_no',
                'inventories.responsible_id',

                'brands.name as brandname',

                'distributor_prices.id as distributor_price_id',
                'distributor_prices.article_no as distributor_article_no',
                'distributor_prices.purchase_price',
                'distributor_prices.price',
                'distributor_prices.availability',

                'distributors.id as distributor_id',
                'distributors.name as distributor',
            ]);

        if ($productId) {
            $query->where('products.id', $productId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product', 'LIKE', "%{$search}%")
                    ->orWhere('products.model', 'LIKE', "%{$search}%")
                    ->orWhere('products.article_no', 'LIKE', "%{$search}%")
                    ->orWhere('products.sku', 'LIKE', "%{$search}%")
                    ->orWhere('brands.name', 'LIKE', "%{$search}%")
                    ->orWhere('distributors.name', 'LIKE', "%{$search}%")
                    ->orWhere('distributor_prices.article_no', 'LIKE', "%{$search}%");
            });
        }

        if ($stock === 'available') {
            $query->where('inventories.quantity', '>', 0);
        }

        if ($stock === 'empty') {
            $query->where('inventories.quantity', '<=', 0);
        }

        $products = $query
            ->orderBy($sortColumn, $direction)
            ->paginate((int) $request->get('per_page', 10));

        return response()->json([
            'status' => true,
            'data' => $products,
        ]);
    }
    public function requests(Request $request): JsonResponse
    {
        $search = trim((string) $request->get('search'));
        $status = $request->get('status');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = [
            'id' => 'inventory_request_outs.id',
            'product' => 'products.product',
            'quantity' => 'inventory_request_outs.quantity',
            'status' => 'inventory_request_outs.status',
            'add_date' => 'inventory_request_outs.add_date',
            'created_at' => 'inventory_request_outs.created_at',
        ];

        $sortColumn = $allowedSorts[$sort] ?? 'inventory_request_outs.created_at';
        $direction = in_array(strtolower($direction), ['asc', 'desc'], true) ? $direction : 'desc';

        $query = InventoryRequestOut::query()
            ->leftJoin('products', 'products.id', '=', 'inventory_request_outs.product_id')
            ->leftJoin('employees as responsible', 'responsible.id', '=', 'inventory_request_outs.responsible_id')
            ->leftJoin('employees as requester', 'requester.id', '=', 'inventory_request_outs.requester_id')
            ->select([
                'inventory_request_outs.*',
                'products.product',
                'products.model',
                'responsible.name as rname',
                'responsible.lastname as rlastname',
                'requester.name as requestname',
                'requester.lastname as requestlastname',
            ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product', 'LIKE', "%{$search}%")
                    ->orWhere('products.model', 'LIKE', "%{$search}%")
                    ->orWhere('inventory_request_outs.reason', 'LIKE', "%{$search}%")
                    ->orWhere('inventory_request_outs.status', 'LIKE', "%{$search}%")
                    ->orWhere('inventory_request_outs.add_date', 'LIKE', "%{$search}%")
                    ->orWhere('responsible.name', 'LIKE', "%{$search}%")
                    ->orWhere('responsible.lastname', 'LIKE', "%{$search}%")
                    ->orWhere('requester.name', 'LIKE', "%{$search}%")
                    ->orWhere('requester.lastname', 'LIKE', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('inventory_request_outs.status', $status);
        }

        $data = $query
            ->orderBy($sortColumn, $direction)
            ->paginate((int) $request->get('per_page', 10));

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'responsible_id' => ['nullable', 'exists:employees,id'],
            'requester_id' => ['required', 'exists:employees,id'],
            'reason' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Bitte prüfen Sie Ihre Eingaben.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                $inventory = Inventory::where('product_id', $request->product_id)
                    ->lockForUpdate()
                    ->first();

                if (!$inventory) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Kein Lagerbestand gefunden.',
                    ], 404);
                }

                if ((int) $request->quantity > (int) $inventory->quantity) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Die angefragte Menge ist höher als der Lagerbestand.',
                    ], 422);
                }

                $item = InventoryRequestOut::create([
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                    'responsible_id' => $request->responsible_id,
                    'requester_id' => $request->requester_id,
                    'reason' => $request->reason,
                    'status' => 'Unpublished',
                    'add_by' => auth()->user()->name ?? 'System',
                    'add_date' => Carbon::now(),
                ]);

                $inventory->quantity = (int) $inventory->quantity - (int) $request->quantity;
                $inventory->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Die Anfrage wurde erfolgreich gespeichert.',
                    'item' => $item,
                ]);
            });
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Fehler beim Speichern.',
            ], 500);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:inventory_request_outs,id'],
            'status' => ['required', 'string', 'max:100'],
            'reason' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Bitte prüfen Sie Ihre Eingaben.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $item = InventoryRequestOut::findOrFail($request->id);

        $item->status = $request->status;
        $item->reason = $request->reason;
        $item->edit_by = auth()->user()->name ?? 'System';
        $item->edit_date = Carbon::now();
        $item->save();

        return response()->json([
            'status' => true,
            'message' => 'Datensatz wurde aktualisiert.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = InventoryRequestOut::find($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Datensatz wurde nicht gefunden.',
            ], 404);
        }

        try {
            return DB::transaction(function () use ($item) {
                // FIX P2-30: store() zieht den Bestand bei Anlage ab, destroy() buchte
                // ihn bisher NICHT zurück -> Inventarschwund. Beim Löschen (Storno) den
                // quantity des Postens wieder auf inventories.quantity addieren.
                // Hard-Delete + find() oben => der Posten ist danach nicht mehr
                // auffindbar => kein zweites destroy() => keine Doppel-Rückbuchung.
                $inventory = Inventory::where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($inventory) {
                    $inventory->quantity = (int) $inventory->quantity + (int) $item->quantity;
                    $inventory->save();
                }

                $item->delete_by = auth()->user()->name ?? 'System';
                $item->delete_date = Carbon::now();
                $item->save();

                $item->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Datensatz erfolgreich gelöscht.',
                ]);
            });
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Fehler beim Löschen.',
            ], 500);
        }
    }
}