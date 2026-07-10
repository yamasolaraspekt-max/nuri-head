<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\OfferDetail;
use App\Models\OfferFolder;
use App\Models\OfferFolderLibraryPage;
use App\Models\OfferPageLibraryItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OfferPageLibraryController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,update')->only(['reorder']);
        $this->middleware('permission:Customer,delete')->only(['destroyPage']);
        $this->middleware('auth');
    }

    public function context(Request $request, OfferFolder $folder): JsonResponse
    {
        $folder->loadMissing(['offer', 'detail']);

        $articleGroupId = $this->positiveInt($request->input('article_group_id'))
            ?: $this->positiveInt($folder->product_id);

        $productId = $this->positiveInt($request->input('product_id'));

        // The library itself is global. These filters only limit the visible result list.
        $filterArticleGroupId = $this->positiveInt($request->input('filter_article_group_id'));
        $filterProductId = $this->positiveInt($request->input('filter_product_id'));
        $search = trim((string) $request->input('q', ''));
        $status = strtolower(trim((string) $request->input('status', 'active')));
        if (!in_array($status, ['active', 'all', 'inactive'], true)) {
            $status = 'active';
        }

        $withInactive = $request->boolean('with_inactive') || in_array($status, ['all', 'inactive'], true);

        $itemsQuery = $this->libraryItemsQuery(null, null, $withInactive)
            ->when($status === 'inactive', fn($q) => $q->where('is_active', false))
            ->when($filterArticleGroupId, function ($q) use ($filterArticleGroupId) {
                $q->where(function ($w) use ($filterArticleGroupId) {
                    $w->where('article_group_id', $filterArticleGroupId)
                        ->orWhereNull('article_group_id');
                });
            })
            ->when($filterProductId, function ($q) use ($filterProductId) {
                $q->where(function ($w) use ($filterProductId) {
                    $w->where('product_id', $filterProductId)
                        ->orWhereNull('product_id');
                });
            })
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $search) . '%';
                $q->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('original_name', 'like', $like)
                        ->orWhereHas('articleGroup', fn($ag) => $ag->where('article_group', 'like', $like)->orWhere('initial', 'like', $like))
                        ->orWhereHas('product', function ($product) use ($like) {
                            $product->where('id', 'like', $like);

                            foreach (['name', 'product', 'title', 'product_name', 'article_name', 'short_name'] as $field) {
                                if (Schema::hasColumn('products', $field)) {
                                    $product->orWhere($field, 'like', $like);
                                }
                            }
                        })
                        ->orWhereHas('creator', function ($creator) use ($like) {
                            foreach (['name', 'lastname', 'email'] as $field) {
                                if (Schema::hasColumn('employees', $field)) {
                                    $creator->orWhere($field, 'like', $like);
                                }
                            }
                        });
                });
            });

        return response()->json([
            'success' => true,
            'context' => [
                'offer_id' => $folder->offer_id,
                'offer_folder_id' => $folder->id,
                'offer_detail_id' => $this->resolveDetail($folder)?->id,
                'article_group_id' => $articleGroupId,
                'product_id' => $productId,
                'global_library' => true,
            ],
            'article_groups' => $this->articleGroupOptions(),
            'products' => $this->productOptions($articleGroupId),
            'filter_products' => $filterArticleGroupId ? $this->productOptions($filterArticleGroupId) : [],
            'items' => $itemsQuery
                ->get()
                ->map(fn(OfferPageLibraryItem $item) => $this->itemPayload($item))
                ->values(),
            'pages' => $this->selectedPages($folder),
            'positions' => $this->positionOptions(),
        ]);
    }

    public function articleGroups(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'items' => $this->articleGroupOptions(),
        ]);
    }

    public function products(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'items' => $this->productOptions($this->positiveInt($request->input('article_group_id'))),
        ]);
    }

    public function storeLibraryItem(Request $request, OfferFolder $folder): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'article_group_id' => ['required', 'integer', 'exists:article_groups,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:30720'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $articleGroupId = (int) $request->input('article_group_id');
        $productId = $this->positiveInt($request->input('product_id'));
        $created = [];

        foreach ($request->file('files', []) as $file) {
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            $path = 'offer-page-library/' . now()->format('Y/m') . '/' . Str::uuid() . '.' . $ext;

            Storage::disk('public')->putFileAs(dirname($path), $file, basename($path));

            [$width, $height] = $this->imageSize($file->getRealPath());

            $item = OfferPageLibraryItem::create([
                'article_group_id' => $articleGroupId,
                'product_id' => $productId,
                'created_by' => $this->currentEmployeeId(),
                'title' => $request->filled('title')
                    ? (string) $request->input('title')
                    : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'description' => $request->input('description'),
                'file_disk' => 'public',
                'file_path' => $path,
                'file_url' => Storage::disk('public')->url($path),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize() ?: 0,
                'width' => $width,
                'height' => $height,
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $this->nextLibrarySort($articleGroupId, $productId),
                'meta' => [
                    'uploaded_from_offer_folder_id' => $folder->id,
                    'uploaded_from_offer_id' => $folder->offer_id,
                    'library_scope' => 'global_shared',
                ],
            ]);

            $created[] = $this->itemPayload($item->fresh(['articleGroup', 'product', 'creator']));
        }

        return response()->json([
            'success' => true,
            'message' => count($created) . ' Seite(n) in die Bibliothek geladen.',
            'items' => $created,
        ]);
    }

    public function updateLibraryItem(Request $request, OfferFolder $folder, OfferPageLibraryItem $item): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'article_group_id' => ['nullable', 'integer', 'exists:article_groups,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = (bool) $data['is_active'];
        }

        $item->fill($data)->save();

        return response()->json([
            'success' => true,
            'item' => $this->itemPayload($item->fresh(['articleGroup', 'product', 'creator'])),
        ]);
    }

    public function attach(Request $request, OfferFolder $folder, OfferPageLibraryItem $item): JsonResponse
    {
        if (!$item->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Diese Bibliotheksseite ist inaktiv.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'page_position' => ['nullable', 'string', Rule::in(OfferFolderLibraryPage::POSITIONS)],
            'is_enabled' => ['nullable', 'boolean'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $detail = $this->resolveDetail($folder);
        $position = (string) ($request->input('page_position') ?: OfferFolderLibraryPage::POSITION_AFTER_ROOF);

        $page = OfferFolderLibraryPage::create([
            'offer_id' => $folder->offer_id,
            'offer_folder_id' => $folder->id,
            'offer_detail_id' => $detail?->id,
            'offer_page_library_item_id' => $item->id,
            'article_group_id' => $item->article_group_id ?: $folder->product_id,
            'product_id' => $item->product_id,
            'created_by' => $this->currentEmployeeId(),
            'title' => $request->filled('title') ? $request->input('title') : $item->title,
            'file_url' => $item->file_url,
            'page_position' => $position,
            'sort_order' => $this->nextSelectedSort($folder, $position),
            'is_enabled' => $request->boolean('is_enabled', true),
            'meta' => [
                'library_file_path' => $item->file_path,
                'library_original_name' => $item->original_name,
                'library_mime_type' => $item->mime_type,
                'library_width' => $item->width,
                'library_height' => $item->height,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seite wurde dem Angebot hinzugefügt.',
            'page' => $this->pagePayload($page->fresh(['libraryItem'])),
            'pages' => $this->selectedPages($folder),
        ]);
    }

    public function pages(OfferFolder $folder): JsonResponse
    {
        return response()->json([
            'success' => true,
            'pages' => $this->selectedPages($folder),
        ]);
    }

    public function updatePage(Request $request, OfferFolder $folder, OfferFolderLibraryPage $page): JsonResponse
    {
        $this->assertPageBelongsToFolder($folder, $page);

        $validator = Validator::make($request->all(), [
            'title' => ['nullable', 'string', 'max:255'],
            'page_position' => ['nullable', 'string', Rule::in(OfferFolderLibraryPage::POSITIONS)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $validator->validated();

        if (array_key_exists('is_enabled', $data)) {
            $data['is_enabled'] = (bool) $data['is_enabled'];
        }

        $page->fill($data)->save();

        return response()->json([
            'success' => true,
            'page' => $this->pagePayload($page->fresh(['libraryItem'])),
            'pages' => $this->selectedPages($folder),
        ]);
    }

    public function reorder(Request $request, OfferFolder $folder): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pages' => ['required', 'array'],
            'pages.*.id' => ['required', 'integer', 'exists:offer_folder_library_pages,id'],
            'pages.*.page_position' => ['nullable', 'string', Rule::in(OfferFolderLibraryPage::POSITIONS)],
            'pages.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'pages.*.is_enabled' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        DB::transaction(function () use ($request, $folder) {
            foreach ($request->input('pages', []) as $index => $row) {
                $page = OfferFolderLibraryPage::query()
                    ->where('offer_folder_id', $folder->id)
                    ->where('id', (int) $row['id'])
                    ->first();

                if (!$page) {
                    continue;
                }

                $page->page_position = $row['page_position'] ?? $page->page_position;
                $page->sort_order = array_key_exists('sort_order', $row) ? (int) $row['sort_order'] : $index;

                if (array_key_exists('is_enabled', $row)) {
                    $page->is_enabled = (bool) $row['is_enabled'];
                }

                $page->save();
            }
        });

        return response()->json([
            'success' => true,
            'pages' => $this->selectedPages($folder),
        ]);
    }

    public function destroyPage(OfferFolder $folder, OfferFolderLibraryPage $page): JsonResponse
    {
        $this->assertPageBelongsToFolder($folder, $page);

        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Seite wurde aus diesem Angebot entfernt.',
            'pages' => $this->selectedPages($folder),
        ]);
    }

    protected function libraryItemsQuery(?int $articleGroupId, ?int $productId, bool $withInactive = false)
    {
        return OfferPageLibraryItem::query()
            ->with(['articleGroup:id,article_group,initial', 'product', 'creator'])
            ->when(!$withInactive, fn($q) => $q->where('is_active', true))
            ->when($articleGroupId, function ($q) use ($articleGroupId) {
                $q->where(function ($w) use ($articleGroupId) {
                    $w->where('article_group_id', $articleGroupId)
                        ->orWhereNull('article_group_id');
                });
            })
            ->when($productId, function ($q) use ($productId) {
                $q->where(function ($w) use ($productId) {
                    $w->where('product_id', $productId)
                        ->orWhereNull('product_id');
                });
            })
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }

    protected function selectedPages(OfferFolder $folder)
    {
        return OfferFolderLibraryPage::query()
            ->with(['libraryItem:id,title,file_url,file_path,file_disk,mime_type,width,height,is_active'])
            ->where('offer_folder_id', $folder->id)
            ->orderByRaw("FIELD(page_position, 'after_cover', 'after_roof', 'before_positions', 'after_positions', 'before_final', 'end')")
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn(OfferFolderLibraryPage $page) => $this->pagePayload($page))
            ->values();
    }

    protected function itemPayload(OfferPageLibraryItem $item): array
    {
        $item->loadMissing(['articleGroup:id,article_group,initial', 'product', 'creator']);

        return [
            'id' => (int) $item->id,
            'article_group_id' => $item->article_group_id,
            'article_group_name' => $item->articleGroup?->article_group,
            'product_id' => $item->product_id,
            'product_name' => $this->productDisplayName($item->product),
            'created_by' => $item->created_by,
            'creator_name' => trim((string) (($item->creator?->name ?? '') . ' ' . ($item->creator?->lastname ?? ''))) ?: null,
            'title' => $item->title,
            'description' => $item->description,
            'file_url' => $item->file_url ?: Storage::disk($item->file_disk ?: 'public')->url($item->file_path),
            'mime_type' => $item->mime_type,
            'size_bytes' => $item->size_bytes,
            'width' => $item->width,
            'height' => $item->height,
            'is_active' => (bool) $item->is_active,
            'sort_order' => (int) $item->sort_order,
            'created_at' => optional($item->created_at)->toDateTimeString(),
            'updated_at' => optional($item->updated_at)->toDateTimeString(),
        ];
    }

    protected function pagePayload(OfferFolderLibraryPage $page): array
    {
        $page->loadMissing(['libraryItem:id,title,file_url,file_path,file_disk,mime_type,width,height,is_active']);
        $item = $page->libraryItem;

        return [
            'id' => (int) $page->id,
            'offer_id' => $page->offer_id,
            'offer_folder_id' => $page->offer_folder_id,
            'offer_detail_id' => $page->offer_detail_id,
            'library_item_id' => $page->offer_page_library_item_id,
            'article_group_id' => $page->article_group_id,
            'product_id' => $page->product_id,
            'created_by' => $page->created_by,
            'title' => $page->title ?: $item?->title,
            'file_url' => $page->file_url ?: $item?->file_url,
            'page_position' => $page->page_position ?: OfferFolderLibraryPage::POSITION_AFTER_ROOF,
            'sort_order' => (int) $page->sort_order,
            'is_enabled' => (bool) $page->is_enabled,
            'library_item_active' => $item ? (bool) $item->is_active : true,
            'mime_type' => $item?->mime_type,
            'width' => $item?->width ?? ($page->meta['library_width'] ?? null),
            'height' => $item?->height ?? ($page->meta['library_height'] ?? null),
            'created_at' => optional($page->created_at)->toDateTimeString(),
            'updated_at' => optional($page->updated_at)->toDateTimeString(),
        ];
    }

    protected function resolveDetail(OfferFolder $folder): ?OfferDetail
    {
        $folder->loadMissing(['detail', 'offer.detail']);

        if ($folder->detail) {
            return $folder->detail;
        }

        if ($folder->offer?->detail) {
            return $folder->offer->detail;
        }

        return OfferDetail::query()
            ->where('offer_folder_id', $folder->id)
            ->orWhere('offer_id', $folder->offer_id)
            ->latest('id')
            ->first();
    }

    protected function articleGroupOptions()
    {
        return ArticleGroup::query()
            ->select(['id', 'article_group', 'initial'])
            ->whereNull('deleted_at')
            ->orderBy('article_group')
            ->get()
            ->map(fn(ArticleGroup $row) => [
                'id' => (int) $row->id,
                'name' => trim((string) ($row->article_group ?? '')) ?: ('Gewerk #' . $row->id),
                'initial' => $row->initial,
            ])
            ->values();
    }

    protected function productOptions(?int $articleGroupId = null)
    {
        if (!Schema::hasTable('products')) {
            return collect();
        }

        $columns = Schema::getColumnListing('products');
        $query = Product::query()->limit(200);

        foreach (['article_group_id', 'product_id', 'group_id'] as $column) {
            if ($articleGroupId && in_array($column, $columns, true)) {
                $query->where($column, $articleGroupId);
                break;
            }
        }

        return $query->get()->map(function ($product) {
            return [
                'id' => (int) $product->id,
                'name' => $this->productDisplayName($product),
            ];
        })->values();
    }

    protected function productDisplayName($product): ?string
    {
        if (!$product) {
            return null;
        }

        foreach (['name', 'product', 'title', 'product_name', 'article_name', 'short_name'] as $field) {
            if (isset($product->{$field}) && trim((string) $product->{$field}) !== '') {
                return trim((string) $product->{$field});
            }
        }

        return 'Produkt #' . $product->id;
    }

    protected function positionOptions(): array
    {
        return [
            ['key' => OfferFolderLibraryPage::POSITION_AFTER_COVER, 'label' => 'Nach Deckblatt'],
            ['key' => OfferFolderLibraryPage::POSITION_AFTER_ROOF, 'label' => 'Nach Dachbelegung'],
            ['key' => OfferFolderLibraryPage::POSITION_BEFORE_POSITIONS, 'label' => 'Vor Positionen'],
            ['key' => OfferFolderLibraryPage::POSITION_AFTER_POSITIONS, 'label' => 'Nach Positionen'],
            ['key' => OfferFolderLibraryPage::POSITION_BEFORE_FINAL, 'label' => 'Vor Schlusstext'],
            ['key' => OfferFolderLibraryPage::POSITION_END, 'label' => 'Ganz am Ende'],
        ];
    }

    protected function nextLibrarySort(int $articleGroupId, ?int $productId): int
    {
        return ((int) OfferPageLibraryItem::query()
            ->where('article_group_id', $articleGroupId)
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->max('sort_order')) + 1;
    }

    protected function nextSelectedSort(OfferFolder $folder, string $position): int
    {
        return ((int) OfferFolderLibraryPage::query()
            ->where('offer_folder_id', $folder->id)
            ->where('page_position', $position)
            ->max('sort_order')) + 1;
    }

    protected function currentEmployeeId(): ?int
    {
        $value = auth()->user()?->name;
        return is_numeric($value) ? (int) $value : null;
    }

    protected function imageSize(?string $path): array
    {
        if (!$path || !is_file($path)) {
            return [null, null];
        }

        try {
            $size = @getimagesize($path);
            return $size ? [(int) $size[0], (int) $size[1]] : [null, null];
        } catch (\Throwable $e) {
            return [null, null];
        }
    }

    protected function positiveInt($value): ?int
    {
        return is_numeric($value) && (int) $value > 0 ? (int) $value : null;
    }

    protected function assertPageBelongsToFolder(OfferFolder $folder, OfferFolderLibraryPage $page): void
    {
        abort_if((int) $page->offer_folder_id !== (int) $folder->id, 404);
    }
}
