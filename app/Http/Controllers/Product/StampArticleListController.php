<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\StampArticleList;
use App\Models\StampArticleListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StampArticleListController extends Controller
{
    /**
     * Main UI
     * GET /admin/stamp-articles/lists  (stamp.lists.index)
     */
    public function index()
    {
        return view('admin.product.product.stamp_lists');
    }

    /**
     * AJAX: load lists + stats
     * GET /admin/ajax/stamp-articles/lists  (ajax.stamp.lists)
     */
    public function ajaxLists(Request $request)
    {
        $user       = Auth::user();
        $employeeId = $user?->name;    // adjust if stamp_article_lists.employee_id stores something else

        // --- MODE 1: JSON for <select> in product modal ---
        if ($request->get('as') === 'select') {
            $listsQuery = StampArticleList::query()
                ->when($employeeId, function ($q) use ($employeeId) {
                    $q->where('employee_id', $employeeId);
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

        // --- MODE 2: existing HTML response for stamp_lists UI ---
        $search  = trim($request->get('search', ''));
        $sortBy  = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        $sortBy  = in_array($sortBy, ['name', 'created_at'], true) ? $sortBy : 'created_at';
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';

        // Meine Listen
        $myListsQuery = StampArticleList::withCount('items')
            ->with('owner')
            ->when($employeeId, function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            });

        // Freigegebene Listen anderer
        $otherListsQuery = StampArticleList::withCount('items')
            ->with('owner')
            ->where('is_shared', true)
            ->when($employeeId, function ($q) use ($employeeId) {
                $q->where('employee_id', '!=', $employeeId);
            });

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
            'my_count'        => $myLists->count(),
            'other_count'     => $otherLists->count(),
            'my_items_sum'    => $myLists->sum('items_count'),
            'other_items_sum' => $otherLists->sum('items_count'),
        ];

        $listsHtml = View::make('admin.product.product.partials.stamp_lists_sidebar', [
            'myLists'    => $myLists,
            'otherLists' => $otherLists,
        ])->render();

        $statsHtml = View::make('admin.product.product.partials.stamp_lists_stats', [
            'stats' => $stats,
        ])->render();

        return response()->json([
            'lists_html' => $listsHtml,
            'stats_html' => $statsHtml,
        ]);
    }


    /**
     * AJAX: create list
     * POST /admin/ajax/stamp-articles/lists  (ajax.stamp.lists.store)
     */
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

        $list = StampArticleList::create([
            'employee_id' => $employeeId,
            'name'        => $data['name'],
            'slug'        => Str::slug($data['name']) . '-' . Str::random(5),
            'color'       => $data['color'] ?? null,
            'description' => $data['description'] ?? null,
            'is_shared'   => !empty($data['is_shared']),
        ]);

        return response()->json([
            'message' => 'Ordner erstellt.',
            'list_id' => $list->id,
        ]);
    }

    /**
     * AJAX: update list
     * PUT /admin/ajax/stamp-articles/lists/{list}  (ajax.stamp.lists.update)
     */
    public function update(StampArticleList $list, Request $request)
    {
        $this->authorizeOwner($list);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:20',
            'is_shared'   => 'nullable|boolean',
        ]);

        $list->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'color'       => $data['color'] ?? null,
            'is_shared'   => !empty($data['is_shared']),
        ]);

        return response()->json([
            'message' => 'Ordner aktualisiert.',
        ]);
    }

    /**
     * AJAX: delete list
     * DELETE /admin/ajax/stamp-articles/lists/{list}  (ajax.stamp.lists.destroy)
     */
    public function destroy(StampArticleList $list)
    {
        $this->authorizeOwner($list);

        $list->delete();

        return response()->json([
            'message' => 'Ordner gelöscht.',
        ]);
    }

    // ------------------------------------------------
    // Helpers / Authorization
    // ------------------------------------------------

    protected function authorizeOwner(StampArticleList $list): void
    {
        $user       = Auth::user();
        $employeeId = $user->name ?? null;

        if (!$employeeId || (int) $list->employee_id !== (int) $employeeId) {
            abort(403, 'Keine Berechtigung für diesen Ordner.');
        }
    }

    protected function authorizeView(StampArticleList $list): void
    {
        $user       = Auth::user();
        $employeeId = $user->name ?? null;

        if ((int) $list->employee_id !== (int) $employeeId && !$list->is_shared) {
            abort(403, 'Keine Berechtigung, diesen Ordner zu sehen.');
        }
    }

    // ------------------------------------------------
    // Items inside a list
    // ------------------------------------------------

    /**
     * AJAX: list items in a given list
     * GET /admin/ajax/stamp-articles/lists/{list}/items  (ajax.stamp.lists.items)
     */
   public function listStampArticles(StampArticleList $list, Request $request)
    {
        $this->authorizeView($list);

        $search  = trim($request->get('search', ''));
        $sortBy  = $request->get('sort_by', 'products.product');
        $sortDir = $request->get('sort_dir', 'asc');

        // Map the values coming from the <select> to real DB columns
        $sortMap = [
            'stamp_articles.name'       => 'products.product',     // legacy from UI
            'stamp_articles.article_no' => 'products.article_no',  // legacy from UI
            'products.product'          => 'products.product',
            'products.article_no'       => 'products.article_no',
            'added_at'                  => 'added_at',
        ];

        $orderColumn = $sortMap[$sortBy] ?? 'products.product';
        $sortDir     = $sortDir === 'desc' ? 'desc' : 'asc';

        $itemsQuery = StampArticleListItem::query()
            ->where('stamp_article_list_id', $list->id)
            ->join('products', 'products.id', '=', 'stamp_article_list_items.stamp_article_id')
            ->leftJoin('employees', 'employees.id', '=', 'stamp_article_list_items.employee_id')
            ->select(
                // item meta
                'stamp_article_list_items.id',
                'stamp_article_list_items.created_at as added_at',
                'stamp_article_list_items.employee_id',

                // product fields – names MUST match the Blade
                'products.id         as product_id',
                'products.article_no as article_no',
                'products.product    as product',

                // placeholders so Blade columns work
                DB::raw('NULL as brand_name'),
                DB::raw('NULL as category'),
                DB::raw('NULL as status'),

                // employee who added
                'employees.name     as emp_name',
                'employees.lastname as emp_lastname'
            );

        if ($search !== '') {
            $itemsQuery->where(function ($q) use ($search) {
                $q->where('products.product', 'like', "%{$search}%")
                ->orWhere('products.article_no', 'like', "%{$search}%");
            });
        }

        $items = $itemsQuery
            ->orderBy($orderColumn, $sortDir)
            ->paginate(15);

        $html = View::make('admin.product.product.partials.stamp_list_items', [
            'items' => $items,
        ])->render();

        $pagination = $items->links()->render();

        return response()->json([
            'html'       => $html,
            'pagination' => $pagination,
        ]);
    }

    /**
     * AJAX: attach a product to a list
     * POST /admin/ajax/stamp-articles/lists/{list}/attach  (ajax.stamp.lists.attach)
     */
    public function attachStampArticle(StampArticleList $list, Request $request)
    {
        $this->authorizeOwner($list);

        $request->validate([
            'stamp_article_id' => 'required|exists:products,id',
        ]);

        $productId  = (int) $request->input('stamp_article_id');
        $employeeId = Auth::user()->name ?? null;

        $exists = StampArticleListItem::where('stamp_article_list_id', $list->id)
            ->where('stamp_article_id', $productId)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Dieses Produkt ist bereits in diesem Ordner.',
            ], 409);
        }

        StampArticleListItem::create([
            'stamp_article_list_id' => $list->id,
            'stamp_article_id'      => $productId,
            'employee_id'           => $employeeId,
        ]);

        return response()->json([
            'message' => 'Produkt zur Stempel-Liste hinzugefügt.',
        ]);
    }

    /**
     * AJAX: detach item from list
     * DELETE /admin/ajax/stamp-articles/lists/{list}/detach/{stampArticle}  (ajax.stamp.lists.detach)
     */
    public function detachStampArticle(StampArticleList $list, StampArticleListItem $stampArticle)
    {
        $this->authorizeOwner($list);

        if ((int) $stampArticle->stamp_article_list_id !== (int) $list->id) {
            return response()->json([
                'message' => 'Dieser Eintrag gehört nicht zu dieser Liste.',
            ], 403);
        }

        $stampArticle->delete();

        return response()->json([
            'message' => 'Eintrag aus der Liste entfernt.',
        ]);
    }

    /**
     * Optional: own search for stamp list UI
     * GET /admin/ajax/stamp-articles/search  (ajax.stamp.articles.search)
     */
    public function ajaxSearch(Request $request)
    {
        $q = trim($request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $products = Product::query()
            ->where(function ($query) use ($q) {
                $query->where('product', 'like', "%{$q}%")
                      ->orWhere('article_no', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get([
                'id',
                'article_no',
                'product',
            ]);

        return response()->json($products);
    }



    public function detachStampArticleByProduct(StampArticleList $list, Product $product)
    {
        $this->authorizeOwner($list);

        $item = StampArticleListItem::where('stamp_article_list_id', $list->id)
            ->where('stamp_article_id', $product->id)
            ->first();

        if (! $item) {
            return response()->json([
                'message' => 'Dieses Produkt ist nicht in diesem Ordner.',
            ], 404);
        }

        $item->delete();

        return response()->json([
            'message' => 'Eintrag aus der Liste entfernt.',
        ]);
    }
}
