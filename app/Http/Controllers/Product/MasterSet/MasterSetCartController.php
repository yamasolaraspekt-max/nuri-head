<?php

namespace App\Http\Controllers\Product\MasterSet;
use App\Http\Controllers\Controller; 
use App\Models\MasterSet;
use App\Models\MasterSetCart;
use App\Models\MasterSetCartItem;
use App\Models\MasterSetCartSection;
use App\Models\MasterSetComponent;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\ProductImage;
use App\Models\DistributorPrice;
class MasterSetCartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return redirect()->route('product.index');
    }

    public function builder()
    {
        return redirect()->route('product.index');
    }

    public function articleGroupMasterSets(Request $request)
    {
        $request->validate([
            'article_group_id' => ['required', 'integer', 'exists:article_groups,id'],
        ]);

        $items = MasterSet::query()
            ->where('article_group_id', $request->integer('article_group_id'))
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'description',
                'status',
                'article_group_id',
            ])
            ->map(function ($set) {
                return [
                    'id'               => $set->id,
                    'text'             => $set->name ?: ('Set #' . $set->id),
                    'name'             => $set->name,
                    'description'      => $set->description,
                    'status'           => $set->status,
                    'article_group_id' => $set->article_group_id,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'items'   => $items,
        ]);
    }

    public function searchProducts(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $products = Product::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('product', 'like', "%{$q}%")
                        ->orWhere('article_no', 'like', "%{$q}%")
                        ->orWhere('model', 'like', "%{$q}%");
                });
            })
            ->orderBy('product')
            ->limit(30)
            ->get([
                'id',
                'product',
                'article_no',
                'model',
                'short_description',
            ])
            ->map(function ($product) {
                return [
                    'id'                => $product->id,
                    'text'              => trim(($product->product ?? 'Produkt') . ' | ' . ($product->article_no ?? '–') . ' | ' . ($product->model ?? '–')),
                    'product'           => $product->product,
                    'article_no'        => $product->article_no,
                    'model'             => $product->model,
                    'short_description' => $product->short_description,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'items'   => $products,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'article_group_id'     => ['nullable', 'integer', 'exists:article_groups,id'],
            'mode'                 => ['nullable', Rule::in(['new', 'existing'])],
            'name'                 => ['nullable', 'string', 'max:255'],
            'description'          => ['nullable', 'string'],
            'target_master_set_id' => ['nullable', 'integer', 'exists:master_sets,id'],
        ]);

        $mode = $data['mode'] ?? 'new';

        if ($mode === 'existing' && empty($data['article_group_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte zuerst eine Artikelgruppe wählen.',
            ], 422);
        }

        if ($mode === 'existing' && empty($data['target_master_set_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte einen bestehenden Master Set wählen.',
            ], 422);
        }

        if (!empty($data['target_master_set_id'])) {
            $targetSet = MasterSet::find($data['target_master_set_id']);

            if (!$targetSet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Der gewählte Master Set wurde nicht gefunden.',
                ], 422);
            }

            if (!empty($data['article_group_id']) && (int) $targetSet->article_group_id !== (int) $data['article_group_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Der gewählte Master Set gehört nicht zur gewählten Artikelgruppe.',
                ], 422);
            }
        }

        $cart = MasterSetCart::create([
            'creator_id'           => $this->resolveEmployeeId(),
            'article_group_id'     => $data['article_group_id'] ?? null,
            'target_master_set_id' => $mode === 'existing' ? ($data['target_master_set_id'] ?? null) : null,
            'mode'                 => $mode,
            'name'                 => $mode === 'new' ? ($data['name'] ?? null) : null,
            'description'          => $data['description'] ?? null,
            'status'               => 'draft',
            'main_total'           => 0,
            'sub_total'            => 0,
            'labor_total'          => 0,
            'total'                => 0,
            'is_locked'            => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart wurde erstellt.',
            'cart_id' => $cart->id,
            'cart'    => $this->transformCart($cart->fresh()),
        ]);
    }

   public function show(MasterSetCart $cart)
    {
        $this->authorizeCart($cart);

        $cart->load([
            'articleGroup:id,article_group',
            'targetMasterSet:id,name,article_group_id',
            'sections' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            'items'    => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            'items.product:id,product,article_no,model,short_description',
            'items.product.images:id,product_id,name,image',
            'items.distributor:id,name,image',
            'items.distributorPrice:id,product_id,distributor_id,price,purchase_price,discount_price,discount_percent,availability,article_no',
        ]);

        return response()->json([
            'success'  => true,
            'cart'     => $this->transformCart($cart),
            'sections' => $cart->sections->map(fn ($section) => $this->transformSection($section))->values(),
            'items'    => $cart->items->map(fn ($item) => $this->transformItem($item))->values(),
        ]);
    }
    public function update(Request $request, MasterSetCart $cart)
    {
        $this->authorizeCart($cart);

        $data = $request->validate([
            'article_group_id'     => ['nullable', 'integer', 'exists:article_groups,id'],
            'mode'                 => ['nullable', Rule::in(['new', 'existing'])],
            'name'                 => ['nullable', 'string', 'max:255'],
            'description'          => ['nullable', 'string'],
            'target_master_set_id' => ['nullable', 'integer', 'exists:master_sets,id'],
        ]);

        $mode = $data['mode'] ?? ($cart->mode ?: 'new');
        $articleGroupId = $data['article_group_id'] ?? $cart->article_group_id;

        if (empty($articleGroupId)) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte zuerst eine Artikelgruppe wählen.',
            ], 422);
        }

        if ($mode === 'new' && blank($data['name'] ?? $cart->name)) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte einen Namen für den neuen Master Set eingeben.',
            ], 422);
        }

        if ($mode === 'existing' && empty($data['target_master_set_id']) && empty($cart->target_master_set_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte einen bestehenden Master Set wählen.',
            ], 422);
        }

        $targetMasterSetId = $mode === 'existing'
            ? ($data['target_master_set_id'] ?? $cart->target_master_set_id)
            : null;

        if (!empty($targetMasterSetId)) {
            $targetSet = MasterSet::find($targetMasterSetId);

            if (!$targetSet || (int) $targetSet->article_group_id !== (int) $articleGroupId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Der gewählte Master Set gehört nicht zur gewählten Artikelgruppe.',
                ], 422);
            }
        }

        $cart->update([
            'article_group_id'     => $articleGroupId,
            'mode'                 => $mode,
            'name'                 => $mode === 'new' ? ($data['name'] ?? $cart->name) : null,
            'description'          => $data['description'] ?? $cart->description,
            'target_master_set_id' => $targetMasterSetId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cart wurde aktualisiert.',
            'cart'    => $this->transformCart($cart->fresh()),
        ]);
    }

    public function destroy(MasterSetCart $cart)
    {
        $this->authorizeCart($cart);

        DB::transaction(function () use ($cart) {
            MasterSetCartItem::where('cart_id', $cart->id)->delete();
            MasterSetCartSection::where('cart_id', $cart->id)->delete();
            $cart->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Cart wurde gelöscht.',
        ]);
    }

    public function storeSection(Request $request, MasterSetCart $cart)
    {
        $this->authorizeCart($cart);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:20'],
        ]);

        $nextSort = ((int) MasterSetCartSection::where('cart_id', $cart->id)->max('sort_order')) + 1;

        $section = MasterSetCartSection::create([
            'cart_id'       => $cart->id,
            'name'          => $data['name'],
            'color'         => $data['color'] ?: '#93c21c',
            'sort_order'    => $nextSort,
            'is_collapsed'  => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sektion wurde erstellt.',
            'section' => $this->transformSection($section),
        ]);
    }

    public function updateSection(Request $request, MasterSetCartSection $section)
    {
        $section->loadMissing('cart');
        $this->authorizeCart($section->cart);

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'color'        => ['nullable', 'string', 'max:20'],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
            'is_collapsed' => ['nullable', 'boolean'],
        ]);

        $section->update([
            'name'         => $data['name'],
            'color'        => $data['color'] ?: $section->color,
            'sort_order'   => $data['sort_order'] ?? $section->sort_order,
            'is_collapsed' => array_key_exists('is_collapsed', $data) ? (bool) $data['is_collapsed'] : $section->is_collapsed,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sektion wurde aktualisiert.',
            'section' => $this->transformSection($section->fresh()),
        ]);
    }

    public function destroySection(MasterSetCartSection $section)
    {
        $section->loadMissing('cart');
        $this->authorizeCart($section->cart);

        $cartId = $section->cart_id;

        DB::transaction(function () use ($section) {
            $itemIds = MasterSetCartItem::where('section_id', $section->id)->pluck('id')->all();

            if (!empty($itemIds)) {
                MasterSetCartItem::whereIn('parent_id', $itemIds)->delete();
                MasterSetCartItem::whereIn('id', $itemIds)->delete();
            }

            $section->delete();
        });

        $this->recalculateCartTotals($cartId);

        return response()->json([
            'success' => true,
            'message' => 'Sektion wurde gelöscht.',
        ]);
    }

   public function storeItem(Request $request, MasterSetCart $cart)
    {
        $this->authorizeCart($cart);

        Log::info('MasterSetCartController@storeItem - incoming request', [
            'cart_id' => $cart->id,
            'user_id' => Auth::id(),
            'raw_request' => $request->all(),
        ]);

        $data = $request->validate([
            'product_id'           => ['required', 'integer', 'exists:products,id'],
            'section_id'           => ['nullable', 'integer', 'exists:master_set_cart_sections,id'],
            'parent_id'            => ['nullable', 'integer', 'exists:master_set_cart_items,id'],
            'distributor_id'       => ['nullable', 'integer', 'exists:distributors,id'],
            'distributor_price_id' => ['nullable', 'integer', 'exists:distributor_prices,id'],
            'source_type'          => ['nullable', 'string', 'max:30'],
            'node_type'            => ['nullable', Rule::in(['main', 'sub'])],
            'qty'                  => ['nullable', 'numeric', 'min:0'],
            'unit_price'           => ['nullable', 'numeric', 'min:0'],
            'purchase_price'       => ['nullable', 'numeric', 'min:0'],
            'margin'               => ['nullable', 'numeric'],
            'skonto'               => ['nullable', 'numeric'],
            'description'          => ['nullable', 'string'],
            'measure'              => ['nullable', 'string', 'max:255'],
            'vpe'                  => ['nullable', 'string', 'max:255'],
            'price_unit'           => ['nullable', 'string', 'max:255'],
            'payment_terms'        => ['nullable', 'string', 'max:255'],
            'availability'         => ['nullable', 'string', 'max:255'],
            'is_stammartikel'      => ['nullable', 'boolean'],
            'is_favorite'          => ['nullable', 'boolean'],
        ]);

        Log::info('MasterSetCartController@storeItem - validated data', [
            'cart_id' => $cart->id,
            'validated' => $data,
        ]);

        $product = Product::findOrFail($data['product_id']);
        $bestDistributorPrice = DistributorPrice::query()
            ->where('product_id', $product->id)
            ->where('status', 'Published')
            ->get()
            ->map(function ($row) {
                $row->effective_price = $row->discount_price ?? $row->purchase_price ?? $row->price;
                return $row;
            })
            ->filter(fn ($row) => $row->effective_price !== null)
            ->sortBy('effective_price')
            ->first();

        Log::info('MasterSetCartController@storeItem - resolved product', [
            'cart_id' => $cart->id,
            'product' => [
                'id' => $product->id,
                'product' => $product->product ?? null,
                'article_no' => $product->article_no ?? null,
                'short_description' => $product->short_description ?? null,
            ],
        ]);

        $parent = null;
        $sectionId = $data['section_id'] ?? null;
        $nodeType = $data['node_type'] ?? 'main';

        if (!empty($data['parent_id'])) {
            $parent = MasterSetCartItem::findOrFail($data['parent_id']);

            Log::info('MasterSetCartController@storeItem - resolved parent', [
                'cart_id' => $cart->id,
                'parent_id' => $parent->id,
                'parent_cart_id' => $parent->cart_id,
                'parent_section_id' => $parent->section_id,
                'parent_depth' => $parent->depth,
            ]);

            if ((int) $parent->cart_id !== (int) $cart->id) {
                Log::warning('MasterSetCartController@storeItem - invalid parent cart mismatch', [
                    'cart_id' => $cart->id,
                    'parent_id' => $parent->id,
                    'parent_cart_id' => $parent->cart_id,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ungültige Parent-Position.',
                ], 422);
            }

            $sectionId = $parent->section_id;
            $nodeType = 'sub';
        }

        if (empty($sectionId)) {
                $section = MasterSetCartSection::firstOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'name'    => 'Importierte Produkte',
                    ],
                    [
                        'color'      => '#93c21c',
                        'sort_order' => 1,
                    ]
                );

                $sectionId = $section->id;
            } else {
                $section = MasterSetCartSection::findOrFail($sectionId);

                if ((int) $section->cart_id !== (int) $cart->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Die Sektion gehört nicht zum gewählten Cart.',
                    ], 422);
                }
            }

        $section = MasterSetCartSection::findOrFail($sectionId);

        Log::info('MasterSetCartController@storeItem - resolved section', [
            'cart_id' => $cart->id,
            'section' => [
                'id' => $section->id,
                'cart_id' => $section->cart_id,
                'name' => $section->name,
            ],
        ]);

        if ((int) $section->cart_id !== (int) $cart->id) {
            Log::warning('MasterSetCartController@storeItem - section cart mismatch', [
                'cart_id' => $cart->id,
                'section_id' => $section->id,
                'section_cart_id' => $section->cart_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Die Sektion gehört nicht zum gewählten Cart.',
            ], 422);
        }

        $nextSort = ((int) MasterSetCartItem::where('cart_id', $cart->id)
            ->where('section_id', $sectionId)
            ->where('parent_id', $parent?->id)
            ->max('sort_order')) + 1;

      $payload = [
        'cart_id'              => $cart->id,
        'section_id'           => $sectionId,
        'parent_id'            => $parent?->id,
        'product_id'           => $product->id,

        'distributor_id'       => $data['distributor_id']
            ?? $bestDistributorPrice?->distributor_id,

        'distributor_price_id' => $data['distributor_price_id']
            ?? $bestDistributorPrice?->id,

        'source_type'          => $data['source_type'] ?? 'product',
        'node_type'            => $nodeType,
        'title'                => $product->product ?: ('Produkt #' . $product->id),
        'description'          => $data['description'] ?? $product->short_description ?? null,

        'article_no'           => $bestDistributorPrice?->article_no
            ?? $product->article_no,

        'measure'              => $data['measure'] ?? null,
        'vpe'                  => $data['vpe'] ?? null,
        'price_unit'           => $data['price_unit'] ?? null,
        'qty'                  => $data['qty'] ?? 1,

        'unit_price'           => $data['unit_price']
            ?? $bestDistributorPrice?->discount_price
            ?? $bestDistributorPrice?->purchase_price
            ?? $bestDistributorPrice?->price
            ?? 0,

        'purchase_price'       => $data['purchase_price']
            ?? $bestDistributorPrice?->purchase_price
            ?? 0,

        'margin'               => $data['margin'] ?? 0,
        'skonto'               => $data['skonto'] ?? 0,
        'payment_terms'        => $data['payment_terms'] ?? null,

        'availability'         => $data['availability']
            ?? $bestDistributorPrice?->availability,

        'sort_order'           => $nextSort,
        'depth'                => $parent ? ((int) $parent->depth + 1) : 0,
        'is_stammartikel'      => (bool) ($data['is_stammartikel'] ?? false),
        'is_favorite'          => (bool) ($data['is_favorite'] ?? false),
        'is_collapsed'         => false,
    ];
        Log::info('MasterSetCartController@storeItem - payload before save', [
            'cart_id' => $cart->id,
            'payload' => $payload,
        ]);

        try {
            $item = MasterSetCartItem::create($payload);

            Log::info('MasterSetCartController@storeItem - saved item', [
                'cart_id' => $cart->id,
                'item_id' => $item->id,
                'saved' => $item->fresh()->toArray(),
            ]);

            $this->recalculateCartTotals($cart->id);

            return response()->json([
                'success' => true,
                'message' => 'Produkt wurde zum Cart hinzugefügt.',
                'item'    => $this->transformItem($item->fresh('product')),
            ]);
        } catch (\Throwable $e) {
            Log::error('MasterSetCartController@storeItem - save failed', [
                'cart_id' => $cart->id,
                'payload' => $payload,
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Speichern fehlgeschlagen: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function updateItem(Request $request, MasterSetCartItem $item)
    {
        $item->loadMissing('cart');
        $this->authorizeCart($item->cart);

        $data = $request->validate([
            'title'                => ['sometimes', 'nullable', 'string', 'max:255'],
            'description'          => ['sometimes', 'nullable', 'string'],
            'qty'                  => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'unit_price'           => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'purchase_price'       => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'margin'               => ['sometimes', 'nullable', 'numeric'],
            'skonto'               => ['sometimes', 'nullable', 'numeric'],
            'measure'              => ['sometimes', 'nullable', 'string', 'max:255'],
            'vpe'                  => ['sometimes', 'nullable', 'string', 'max:255'],
            'price_unit'           => ['sometimes', 'nullable', 'string', 'max:255'],
            'payment_terms'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'availability'         => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort_order'           => ['sometimes', 'nullable', 'integer', 'min:0'],
            'is_stammartikel'      => ['sometimes', 'boolean'],
            'is_favorite'          => ['sometimes', 'boolean'],
            'is_collapsed'         => ['sometimes', 'boolean'],
        ]);

        $item->update($data);
        $this->recalculateCartTotals($item->cart_id);

        return response()->json([
            'success' => true,
            'message' => 'Cart-Item wurde aktualisiert.',
            'item'    => $this->transformItem($item->fresh('product')),
        ]);
    }

    public function destroyItem(MasterSetCartItem $item)
    {
        $item->loadMissing('cart');
        $this->authorizeCart($item->cart);

        $cartId = $item->cart_id;

        DB::transaction(function () use ($item) {
            $childrenIds = MasterSetCartItem::where('parent_id', $item->id)->pluck('id')->all();

            if (!empty($childrenIds)) {
                MasterSetCartItem::whereIn('parent_id', $childrenIds)->delete();
                MasterSetCartItem::whereIn('id', $childrenIds)->delete();
            }

            $item->delete();
        });

        $this->recalculateCartTotals($cartId);

        return response()->json([
            'success' => true,
            'message' => 'Item wurde gelöscht.',
        ]);
    }

    public function moveItem(Request $request, MasterSetCartItem $item)
    {
        $item->loadMissing('cart');
        $this->authorizeCart($item->cart);

        $data = $request->validate([
            'section_id'  => ['nullable', 'integer', 'exists:master_set_cart_sections,id'],
            'parent_id'   => ['nullable', 'integer', 'exists:master_set_cart_items,id'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $sectionId = $data['section_id'] ?? $item->section_id;
        $parentId = $data['parent_id'] ?? null;
        $depth = 0;

        if ($parentId) {
            $parent = MasterSetCartItem::findOrFail($parentId);

            if ((int) $parent->cart_id !== (int) $item->cart_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ungültiger Ziel-Parent.',
                ], 422);
            }

            $sectionId = $parent->section_id;
            $depth = ((int) $parent->depth) + 1;
        }

        if ($sectionId) {
            $section = MasterSetCartSection::findOrFail($sectionId);

            if ((int) $section->cart_id !== (int) $item->cart_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ungültige Ziel-Sektion.',
                ], 422);
            }
        }

        $item->update([
            'section_id' => $sectionId,
            'parent_id'  => $parentId,
            'node_type'  => $parentId ? 'sub' : 'main',
            'depth'      => $depth,
            'sort_order' => $data['sort_order'] ?? $item->sort_order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item wurde verschoben.',
            'item'    => $this->transformItem($item->fresh('product')),
        ]);
    }

    public function syncOrder(Request $request, MasterSetCart $cart)
    {
        $this->authorizeCart($cart);

        $data = $request->validate([
            'items'              => ['required', 'array'],
            'items.*.id'         => ['required', 'integer', 'exists:master_set_cart_items,id'],
            'items.*.section_id' => ['nullable', 'integer', 'exists:master_set_cart_sections,id'],
            'items.*.parent_id'  => ['nullable', 'integer'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($data, $cart) {
            foreach ($data['items'] as $row) {
                $item = MasterSetCartItem::findOrFail($row['id']);

                if ((int) $item->cart_id !== (int) $cart->id) {
                    continue;
                }

                $parentId = $row['parent_id'] ?? null;
                $sectionId = $row['section_id'] ?? $item->section_id;
                $depth = 0;

                if ($parentId) {
                    $parent = MasterSetCartItem::find($parentId);

                    if ($parent && (int) $parent->cart_id === (int) $cart->id) {
                        $sectionId = $parent->section_id;
                        $depth = ((int) $parent->depth) + 1;
                    } else {
                        $parentId = null;
                    }
                }

                $item->update([
                    'section_id' => $sectionId,
                    'parent_id'  => $parentId,
                    'node_type'  => $parentId ? 'sub' : 'main',
                    'depth'      => $depth,
                    'sort_order' => $row['sort_order'] ?? 0,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Reihenfolge wurde gespeichert.',
        ]);
    }

    public function convert(MasterSetCart $cart)
    {
        $this->authorizeCart($cart);

        $cart->load([
            'sections' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
            'items'    => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
            'items.product',
            'items.distributor',
            'items.distributorPrice',
        ]);

        Log::info('MasterSetCart convert START', [
            'cart_id' => $cart->id,
            'mode' => $cart->mode,
            'article_group_id' => $cart->article_group_id,
            'target_master_set_id' => $cart->target_master_set_id,
            'items_count' => $cart->items->count(),
        ]);

        if ($cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Der Cart enthält keine Produkte.',
            ], 422);
        }

        if (!$cart->article_group_id) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte zuerst Artikelgruppe und Master-Set-Konfiguration speichern.',
            ], 422);
        }

        if ($cart->mode === 'new' && blank($cart->name)) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte zuerst einen Namen für den neuen Master Set eingeben.',
            ], 422);
        }

        if ($cart->mode === 'existing' && !$cart->target_master_set_id) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte zuerst einen bestehenden Master Set wählen.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            if ($cart->mode === 'existing') {
                $masterSet = MasterSet::findOrFail($cart->target_master_set_id);
            } else {
                $masterSet = MasterSet::create([
                    'article_group_id' => $cart->article_group_id,
                    'name'             => $cart->name ?: ('Master Set ' . now()->format('Y-m-d H:i')),
                    'description'      => $cart->description,
                    'creator_id'       => $cart->creator_id,
                    'status'           => 'active',
                    'count_copy'       => 0,
                    'count_offer'      => 0,
                    'is_locked'        => 0,
                    'main_total'       => $cart->main_total ?? 0,
                    'sub_total'        => $cart->sub_total ?? 0,
                    'labor_total'      => $cart->labor_total ?? 0,
                    'total'            => $cart->total ?? 0,
                ]);
            }

            $idMap = [];

            $sortedItems = $cart->items
                ->sortBy([
                    ['depth', 'asc'],
                    ['parent_id', 'asc'],
                    ['section_id', 'asc'],
                    ['sort_order', 'asc'],
                    ['id', 'asc'],
                ])
                ->values();

            foreach ($sortedItems as $item) {
                $payload = [
                    'master_set_id'          => $masterSet->id,
                    'parent_id'              => $item->parent_id ? ($idMap[$item->parent_id] ?? null) : null,
                    'product_id'             => $item->product_id,
                    'article_no'             => $item->product?->article_no ?? $item->article_no,
                    'distributor_article_no' => $item->article_no ?? $item->product?->article_no,
                    'distributor_id'         => $item->distributor_id,
                    'distributor_price_id'   => $item->distributor_price_id,
                    'unit_price'             => $item->unit_price ?? 0,
                    'purchase_price'         => $item->purchase_price ?? 0,
                    'margin'                 => $item->margin ?? 0,
                    'skonto'                 => $item->skonto ?? 0,
                    'payment_terms'          => is_null($item->payment_terms) ? 14 : (int) $item->payment_terms,
                    'availability'           => is_null($item->availability) ? 1 : (int) $item->availability,
                    'type'                   => $item->node_type === 'sub' ? 'sub' : 'main',
                    'is_stammartikel'        => (bool) ($item->is_stammartikel ?? false),
                    'is_favorite'            => (bool) ($item->is_favorite ?? false),
                    'measure'                => $item->measure ?? 'Stk',
                    'vpe'                    => $item->vpe ?? 1,
                    'price_unit'             => $item->price_unit ?? 1,
                    'qty'                    => $item->qty ?? 1,
                    'description'            => $item->description ?? ($item->product->short_description ?? ''),
                    'sort_order'             => $item->sort_order ?? 0,
                ];

                Log::info('MasterSetCart convert component payload', $payload);

                $component = MasterSetComponent::create($payload);

                $idMap[$item->id] = $component->id;
            }

            $cart->update([
                'status'               => 'converted',
                'target_master_set_id' => $masterSet->id,
                'converted_at'         => now(),
            ]);

            DB::commit();

            Log::info('MasterSetCart convert SUCCESS', [
                'cart_id' => $cart->id,
                'master_set_id' => $masterSet->id,
                'master_set_name' => $masterSet->name,
            ]);

            return response()->json([
                'success'         => true,
                'message'         => 'Cart wurde erfolgreich in Master Set umgewandelt.',
                'master_set_id'   => $masterSet->id,
                'master_set_name' => $masterSet->name,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('MasterSetCart convert FAILED', [
                'cart_id' => $cart->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Umwandlung fehlgeschlagen: ' . $e->getMessage(),
            ], 500);
        }
    }
    protected function authorizeCart(MasterSetCart $cart): void
    {
        $creatorId = $this->resolveEmployeeId();

        if ($creatorId && (int) $cart->creator_id === (int) $creatorId) {
            return;
        }

        if (Auth::check()) {
            return;
        }

        abort(403);
    }

    protected function resolveEmployeeId(): ?int
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        if (isset($user->employee) && $user->employee && isset($user->employee->id)) {
            return (int) $user->employee->id;
        }

        if (isset($user->employee_id) && $user->employee_id) {
            return (int) $user->employee_id;
        }

        return null;
    }

    protected function recalculateCartTotals(int $cartId): void
    {
        $items = MasterSetCartItem::where('cart_id', $cartId)->get();

        $mainTotal = 0;
        $subTotal = 0;

        foreach ($items as $item) {
            $line = (float) ($item->qty ?? 0) * (float) ($item->unit_price ?? 0);

            if ($item->parent_id) {
                $subTotal += $line;
            } else {
                $mainTotal += $line;
            }
        }

        MasterSetCart::where('id', $cartId)->update([
            'main_total' => round($mainTotal, 2),
            'sub_total'  => round($subTotal, 2),
            'total'      => round($mainTotal + $subTotal, 2),
        ]);
    }

    protected function transformCart(MasterSetCart $cart): array
    {
        return [
            'id'                   => $cart->id,
            'creator_id'           => $cart->creator_id,
            'article_group_id'     => $cart->article_group_id,
            'target_master_set_id' => $cart->target_master_set_id,
            'mode'                 => $cart->mode,
            'name'                 => $cart->name,
            'description'          => $cart->description,
            'status'               => $cart->status,
            'main_total'           => (float) ($cart->main_total ?? 0),
            'sub_total'            => (float) ($cart->sub_total ?? 0),
            'labor_total'          => (float) ($cart->labor_total ?? 0),
            'total'                => (float) ($cart->total ?? 0),
            'is_locked'            => (bool) ($cart->is_locked ?? false),
            'converted_at'         => optional($cart->converted_at)?->toDateTimeString(),
            'created_at'           => optional($cart->created_at)?->toDateTimeString(),
            'updated_at'           => optional($cart->updated_at)?->toDateTimeString(),
        ];
    }

    protected function transformSection(MasterSetCartSection $section): array
    {
        return [
            'id'           => $section->id,
            'cart_id'      => $section->cart_id,
            'name'         => $section->name,
            'color'        => $section->color,
            'sort_order'   => (int) ($section->sort_order ?? 0),
            'is_collapsed' => (bool) ($section->is_collapsed ?? false),
            'created_at'   => optional($section->created_at)?->toDateTimeString(),
            'updated_at'   => optional($section->updated_at)?->toDateTimeString(),
        ];
    }

 
    protected function transformItem(MasterSetCartItem $item): array
{
    $image = null;

    if (
        $item->relationLoaded('product') &&
        $item->product &&
        $item->product->relationLoaded('images') &&
        $item->product->images->count()
    ) {
        $image = $item->product->images->first()->image;
    }

    return [
    'id'                  => $item->id,
    'cart_id'             => $item->cart_id,
    'section_id'          => $item->section_id,
    'parent_id'           => $item->parent_id,
    'product_id'          => $item->product_id,
    'distributor_id'      => $item->distributor_id,
    'distributor_price_id'=> $item->distributor_price_id,
    'source_type'         => $item->source_type,
    'node_type'           => $item->node_type,
    'title'               => $item->title,
    'article_no'          => $item->article_no,
    'description'         => $item->description,
    'availability'        => $item->availability,
    'qty'                 => (float) ($item->qty ?? 0),
    'unit_price'          => (float) ($item->unit_price ?? 0),
    'purchase_price'      => (float) ($item->purchase_price ?? 0),
    'sort_order'          => (int) ($item->sort_order ?? 0),
    'product_image_url'   => $image ? asset('images/products/' . $image) : null,

    'distributor' => $item->relationLoaded('distributor') && $item->distributor ? [
        'id'    => $item->distributor->id,
        'name'  => $item->distributor->name,
        'image' => $item->distributor->image ?? null,
    ] : null,

    'product' => $item->relationLoaded('product') && $item->product ? [
        'id'                => $item->product->id,
        'product'           => $item->product->product,
        'article_no'        => $item->product->article_no,
            'model'             => $item->product->model ?? null,
            'short_description' => $item->product->short_description ?? null,
            'image'             => $image,
        ] : null,

        'created_at' => optional($item->created_at)?->toDateTimeString(),
        'updated_at' => optional($item->updated_at)?->toDateTimeString(),
    ];
}

}