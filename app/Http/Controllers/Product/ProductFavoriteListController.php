<?php
 
namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductFavoriteList;
use App\Models\ProductFavoriteListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class ProductFavoriteListController extends Controller
{
    public function index()
    {
        return view('admin.product.product.favorite_lists');
    }

  public function ajaxLists(Request $request)
    {
        $user = Auth::user();

        /**
         * Decide what your employee key really is.
         * If product_favorite_lists.employee_id stores:
         *  - users.name  → use $user->name
         *  - users.id    → use $user->id
         *  - employees.id → use $user->employee_id
         */
        $employeeKey = $user?->name; // adjust if needed

        // --- MODE 1: JSON for <select> in product modal ---
        if ($request->get('as') === 'select') {
            $listsQuery = ProductFavoriteList::query()
                ->when($employeeKey, function ($q) use ($employeeKey) {
                    $q->where('employee_id', $employeeKey);
                })
                ->orderBy('name');

            $lists = $listsQuery->get(['id', 'name']);

            return response()->json([
                'lists' => $lists->map(function ($list) {
                    return [
                        'id'   => $list->id,
                        'name' => $list->name,
                    ];
                }),
            ]);
        }

        // --- MODE 2: existing HTML response for favorite_lists UI ---
        $search  = trim($request->get('search', ''));
        $sortBy  = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        $sortBy  = in_array($sortBy, ['name','created_at']) ? $sortBy : 'created_at';
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        // Meine Listen
        $myListsQuery = ProductFavoriteList::withCount('items')
            ->with('owner')
            ->when($employeeKey, function ($q) use ($employeeKey) {
                $q->where('employee_id', $employeeKey);
            });

        // Geteilte Listen anderer
        $otherListsQuery = ProductFavoriteList::withCount('items')
            ->with('owner')
            ->when($employeeKey, function ($q) use ($employeeKey) {
                $q->where('employee_id', '!=', $employeeKey);
            })
            ->where('is_shared', true);

        if ($search !== '') {
            $myListsQuery->where('name', 'like', "%{$search}%");
            $otherListsQuery->where('name', 'like', "%{$search}%");
        }

        $myLists = $myListsQuery
            ->orderBy($sortBy, $sortDir)
            ->get();

        $otherLists = $otherListsQuery
            ->orderBy($sortBy, $sortDir)
            ->get();

        $stats = [
            'my_count'         => $myLists->count(),
            'other_count'      => $otherLists->count(),
            'my_items_sum'     => $myLists->sum('items_count'),
            'other_items_sum'  => $otherLists->sum('items_count'),
        ];

        $listsHtml = View::make('admin.product.product.partials.favorite_lists_sidebar', [
            'myLists'    => $myLists,
            'otherLists' => $otherLists,
        ])->render();

        $statsHtml = View::make('admin.product.product.partials.favorite_lists_stats', [
            'stats' => $stats,
        ])->render();

        return response()->json([
            'lists_html' => $listsHtml,
            'stats_html' => $statsHtml,
        ]);
    }


    public function store(Request $request)
    {
        $user       = Auth::user();
        $employeeId = $user->name ?? null;

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:20',
            'is_shared'   => 'nullable|boolean',
        ]);

        $list = ProductFavoriteList::create([
            'employee_id' => $employeeId,
            'name'        => $data['name'],
            'slug'        => Str::slug($data['name']) . '-' . Str::random(5),
            'color'       => $data['color'] ?? null,
            'description' => $data['description'] ?? null,
            'is_shared'   => $data['is_shared'] ?? false,
        ]);

        return response()->json([
            'message' => 'Liste erstellt.',
            'list_id' => $list->id,
        ]);
    }

    public function update(ProductFavoriteList $list, Request $request)
    {
        $this->authorizeOwner($list);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:20',
            'is_shared'   => 'nullable|boolean',
        ]);

        $list->update($data);

        return response()->json([
            'message' => 'Liste aktualisiert.',
        ]);
    }

    public function destroy(ProductFavoriteList $list)
    {
        $this->authorizeOwner($list);
        $list->delete();

        return response()->json([
            'message' => 'Liste gelöscht.',
        ]);
    }

        public function listProducts(ProductFavoriteList $list, Request $request)
        {
            $search  = trim($request->get('search', ''));
            $sortBy  = $request->get('sort_by', 'products.product');
            $sortDir = $request->get('sort_dir', 'asc');

            $allowedSort = [
                'products.product',
                'products.article_no',
                'brand_name',
                'added_at',
            ];

            if (! in_array($sortBy, $allowedSort, true)) {
                $sortBy = 'products.product';
            }
            $sortDir = $sortDir === 'desc' ? 'desc' : 'asc';

            $itemsQuery = ProductFavoriteListItem::query()
                ->where('product_favorite_list_id', $list->id)
                ->join('products', 'products.id', '=', 'product_favorite_list_items.product_id')
                ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                ->leftJoin('employees', 'employees.id', '=', 'product_favorite_list_items.employee_id')
                ->select(
                    'product_favorite_list_items.id',
                    'product_favorite_list_items.created_at as added_at',
                    'product_favorite_list_items.employee_id',
                    'products.id as product_id',
                    'products.article_no',
                    'products.product',
                    'products.category',
                    'products.status',
                    'brands.name as brand_name',
                    'employees.name as emp_name',
                    'employees.lastname as emp_lastname'
                );

            if ($search !== '') {
                $itemsQuery->where(function ($q) use ($search) {
                    $q->where('products.product', 'like', "%{$search}%")
                    ->orWhere('products.article_no', 'like', "%{$search}%")
                    ->orWhere('brands.name', 'like', "%{$search}%")
                    ->orWhere('products.category', 'like', "%{$search}%");
                });
            }

            $items = $itemsQuery
                ->orderBy($sortBy, $sortDir)
                ->paginate(15);

            // Wichtig: $items an die View geben
            $html = View::make('admin.product.product.partials.favorite_list_products', [
                'items' => $items,
            ])->render();

            $pagination = $items->links()->render();

            return response()->json([
                'html'       => $html,
                'pagination' => $pagination,
            ]);
        }
    public function attachProduct(ProductFavoriteList $list, Request $request)
    {
        $this->authorizeView($list);
        $employeeId = Auth::user()->employee_id ?? null;

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'note'       => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($list, $employeeId, $data) {
            ProductFavoriteListItem::updateOrCreate(
                [
                    'product_favorite_list_id' => $list->id,
                    'product_id'               => $data['product_id'],
                ],
                [
                    'employee_id' => $employeeId,
                    'note'        => $data['note'] ?? null,
                ]
            );
        });

        return response()->json([
            'message' => 'Produkt zur Liste hinzugefügt.',
        ]);
    }

    public function detachProduct(ProductFavoriteList $list, Product $product)
    {
        $this->authorizeView($list);

        ProductFavoriteListItem::where('product_favorite_list_id', $list->id)
            ->where('product_id', $product->id)
            ->delete();

        return response()->json([
            'message' => 'Produkt aus der Liste entfernt.',
        ]);
    }

    // Helpers
    protected function authorizeOwner(ProductFavoriteList $list)
    {
        $employeeId = Auth::user()->employee_id ?? null;
        if ($list->employee_id !== $employeeId) {
            abort(403);
        }
    }

    protected function authorizeView(ProductFavoriteList $list)
    {
        $employeeId = Auth::user()->employee_id ?? null;
        if ($list->employee_id !== $employeeId && !$list->is_shared) {
            abort(403);
        }
    }


    public function ajaxProducts(Request $request, ProductFavoriteList $list)
{
    $search  = trim($request->get('search', ''));
    $sortBy  = $request->get('sort_by', 'products.product');
    $sortDir = $request->get('sort_dir', 'asc');
    $perPage = (int) $request->get('per_page', 20);

    $allowedSort = [
        'products.product',
        'products.article_no',
        'brand_name',
        'added_at',
    ];

    if (! in_array($sortBy, $allowedSort)) {
        $sortBy = 'products.product';
    }
    $sortDir = $sortDir === 'desc' ? 'desc' : 'asc';

    $query = \DB::table('product_favorite_list_items')
        ->join('products', 'products.id', '=', 'product_favorite_list_items.product_id')
        ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
        ->leftJoin('employees', 'employees.id', '=', 'product_favorite_list_items.employee_id')
        ->where('product_favorite_list_items.product_favorite_list_id', $list->id)
        ->select(
            'product_favorite_list_items.id',
            'product_favorite_list_items.created_at as added_at',
            'product_favorite_list_items.note',
            'products.id as product_id',
            'products.product',
            'products.article_no',
            'products.category',
            'products.status',
            'brands.name as brand_name',
            'employees.name as emp_name',
            'employees.lastname as emp_lastname'
        );

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('products.product', 'like', "%{$search}%")
              ->orWhere('products.article_no', 'like', "%{$search}%")
              ->orWhere('brands.name', 'like', "%{$search}%");
        });
    }

    $query->orderBy($sortBy, $sortDir);

    $items = $query->paginate($perPage)->appends($request->except('page'));

    $html       = View::make('admin.product.product.partials.favorite_list_products', compact('items'))->render();
    $pagination = $items->links()->render();

    return response()->json([
        'html'       => $html,
        'pagination' => $pagination,
    ]);
}

public function storeItem(Request $request, ProductFavoriteList $list)
{
    $request->validate([
        'product_id' => 'required|integer|exists:products,id',
        'note'       => 'nullable|string|max:255',
    ]);

    $user       = Auth::user();
    $employeeId = $user ? $user->name : null; // you store employee_id in users.name

    try {
        $item = ProductFavoriteListItem::firstOrCreate(
            [
                'product_favorite_list_id' => $list->id,
                'product_id'               => $request->product_id,
            ],
            [
                'employee_id' => $employeeId,
                'note'        => $request->note,
            ]
        );

        $isNew = $item->wasRecentlyCreated;

        return response()->json([
            'message' => $isNew
                ? 'Produkt zur Favoritenliste hinzugefügt.'
                : 'Dieses Produkt ist bereits in diesem Ordner.',
            'created' => $isNew,
        ], $isNew ? 201 : 200);
    } catch (\Illuminate\Database\QueryException $e) {
        // Fallback if unique index greift
        if ($e->getCode() === '23000') {
            return response()->json([
                'message' => 'Dieses Produkt ist bereits in diesem Ordner.',
            ], 409);
        }
        throw $e;
    }
}
public function destroyItem(ProductFavoriteList $list, ProductFavoriteListItem $item)
{
    if ($item->product_favorite_list_id !== $list->id) {
        abort(403);
    }

    $item->delete();

    return response()->json([
        'message' => 'Produkt aus der Liste entfernt.',
    ]);
}

}
