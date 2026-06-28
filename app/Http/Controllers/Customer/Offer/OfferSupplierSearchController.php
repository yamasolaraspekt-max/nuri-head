<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Events\OfferSupplierProductsReturned;
use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\Brand;
use App\Models\Distributor;
use App\Models\DistributorPrice;
use App\Models\Measure;
use App\Models\OfferDetail;
use App\Models\OfferFolder;
use App\Models\Product;
use App\Models\SubArticleGroup;
use App\Models\SupplierConnection;
use App\Models\SupplierImportLog;
use App\Services\Suppliers\SupplierConnectorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class OfferSupplierSearchController extends Controller
{
    public function connections(OfferFolder $folder): JsonResponse
    {
        $connections = SupplierConnection::query()
            ->with('distributor:id,name,short_name')
            ->where('is_active', true)
            ->whereRaw('LOWER(TRIM(connector_type)) IN (?, ?)', ['ids', 'oci'])
            ->orderBy('name')
            ->get()
            ->map(function (SupplierConnection $connection) {
                $connectorType = strtolower(trim((string) $connection->connector_type));

                $distributorName = $connection->distributor
                    ? trim((string) ($connection->distributor->short_name ?: $connection->distributor->name))
                    : null;

                $hasEndpoint = filled($connection->endpoint_url);
                $hasDistributor = filled($connection->distributor_id);

                return [
                    'id' => (int) $connection->id,
                    'name' => (string) $connection->name,
                    'supplier_key' => (string) $connection->supplier_key,
                    'connector_type' => strtoupper($connectorType),
                    'distributor_id' => $connection->distributor_id ? (int) $connection->distributor_id : null,
                    'distributor_name' => $distributorName,
                    'ready' => $hasEndpoint,
                    'has_endpoint' => $hasEndpoint,
                    'has_distributor' => $hasDistributor,
                    'warning' => !$hasDistributor
                        ? 'Kein Distributor verbunden. Du kannst ihn auf der Rückgabe-Seite auswählen.'
                        : null,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'connections' => $connections,
        ]);
    }

    public function forward(
        Request $request,
        OfferFolder $folder,
        SupplierConnection $supplierConnection,
        SupplierConnectorService $service
    ) {
        abort_unless($supplierConnection->is_active, 404);

        $validated = $request->validate([
            'query' => ['required', 'string', 'max:255'],
            'target_section_index' => ['nullable', 'integer'],
        ]);

        $connectorType = strtolower(trim((string) $supplierConnection->connector_type));

        if (!in_array($connectorType, ['ids', 'oci'], true)) {
            return back()->with('error', 'Dieser Lieferant kann nicht als Shop geöffnet werden.');
        }

        if (!$supplierConnection->endpoint_url) {
            return back()->with('error', 'Endpoint URL fehlt.');
        }

        $targetSectionIndex = $request->filled('target_section_index')
            ? (int) $request->integer('target_section_index')
            : null;

        /*
         * Supplier returns the selected basket to this URL.
         * This page will show the review/mapping form first.
         */
        $hookUrl = route('admin.offers.folders.supplier.return', [
            'folder' => $folder,
            'supplierConnection' => $supplierConnection,
        ]);

        $hookUrl .= '?' . http_build_query(array_filter([
            'uid' => auth()->id(),
            'target_section_index' => $targetSectionIndex,
        ], fn($value) => $value !== null && $value !== ''));

        /*
         * Supplier's "back" URL can be the offer editor.
         */
        $returnUrl = route('admin.offers.folders.show', $folder);

        $params = $service->buildOpenParams($supplierConnection, [
            'searchterm' => $validated['query'],
            'query' => $validated['query'],
            'auto' => false,
            'uid' => auth()->id(),

            'hook_url' => $hookUrl,
            'hookurl' => $hookUrl,
            'callback_url' => $hookUrl,
            'return_url' => $returnUrl,
        ]);

        return response()->view('admin.supplier-connectors.forward-to-shop', [
            'connection' => $supplierConnection,
            'shopUrl' => $supplierConnection->endpoint_url,
            'params' => $params,
        ]);
    }

    public function handleReturn(
        Request $request,
        OfferFolder $folder,
        SupplierConnection $supplierConnection,
        SupplierConnectorService $service
    ) {
        if (!$supplierConnection->is_active) {
            return response('Diese Lieferanten-Schnittstelle ist deaktiviert.', 403);
        }

        /*
         * Important:
         * autoImport=false, because the user must first review brand, distributor,
         * article group, sub article group, unit, prices, etc.
         */
        $log = $service->handleReturn(
            $request,
            $supplierConnection,
            false
        );

        $targetSectionIndex = $request->filled('target_section_index')
            ? (int) $request->integer('target_section_index')
            : null;

        $payload = is_array($log->payload ?? null) ? $log->payload : [];
        $payload['offer_folder_id'] = (int) $folder->id;
        $payload['offer_id'] = (int) ($folder->offer_id ?: $folder->offer?->id);
        $payload['target_section_index'] = $targetSectionIndex;
        $log->payload = $payload;
        $log->save();

        return redirect()->route('admin.offers.folders.supplier.logs.review', [
            'folder' => $folder,
            'supplierConnection' => $supplierConnection,
            'log' => $log,
        ]);
    }

    public function reviewReturn(
        Request $request,
        OfferFolder $folder,
        SupplierConnection $supplierConnection,
        SupplierImportLog $log
    ) {
        abort_unless((int) $log->supplier_connection_id === (int) $supplierConnection->id, 404);

        $payload = is_array($log->payload ?? null) ? $log->payload : [];
        $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];

        return view('admin.offer.supplier-return-review', [
            'folder' => $folder,
            'connection' => $supplierConnection,
            'log' => $log,
            'items' => $items,
            'selectedDistributor' => $supplierConnection->distributor_id
                ? Distributor::find($supplierConnection->distributor_id)
                : null,
            'brands' => Brand::query()->orderBy('name')->limit(300)->get(),
            'distributors' => Distributor::query()->orderBy('name')->limit(300)->get(),
            'articleGroups' => ArticleGroup::query()->orderBy('article_group')->get(),
            'subArticleGroups' => SubArticleGroup::query()->orderBy('sub_article')->get(),
            'measures' => class_exists(Measure::class) && Schema::hasTable('measures')
                ? Measure::query()->orderBy('measure')->get()
                : collect(),
            'targetSectionIndex' => $payload['target_section_index'] ?? null,
        ]);
    }

    public function importReviewedToOffer(
        Request $request,
        OfferFolder $folder,
        SupplierConnection $supplierConnection,
        SupplierImportLog $log,
        SupplierConnectorService $service
    ) {
        abort_unless((int) $log->supplier_connection_id === (int) $supplierConnection->id, 404);

        $validated = $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
            'default_brand_id' => ['nullable', 'exists:brands,id'],
            'default_article_group_id' => ['nullable', 'exists:article_groups,id'],
            'default_sub_article_group_id' => ['nullable', 'exists:sub_article_groups,id'],
            'target_section_index' => ['nullable', 'integer'],
            'update_price_only_if_exists' => ['nullable', 'boolean'],

            'items' => ['required', 'array'],
            'items.*.import' => ['nullable', 'boolean'],
            'items.*.product_title' => ['nullable', 'string', 'max:255'],
            'items.*.ean' => ['nullable', 'string', 'max:255'],
            'items.*.brand_id' => ['nullable', 'exists:brands,id'],
            'items.*.article_group_id' => ['nullable', 'exists:article_groups,id'],
            'items.*.sub_article_group_id' => ['nullable', 'exists:sub_article_groups,id'],
            'items.*.manufacturer_article_no' => ['nullable', 'string', 'max:255'],
            'items.*.distributor_article_no' => ['nullable', 'string', 'max:255'],
            'items.*.price' => ['nullable'],
            'items.*.purchase_price' => ['nullable'],
            'items.*.discount_price' => ['nullable'],
            'items.*.discount_percent' => ['nullable'],
            'items.*.availability' => ['nullable', 'string', 'max:255'],
            'items.*.measure_unit' => ['nullable', 'string', 'max:255'],
            'items.*.short_description' => ['nullable', 'string'],
            'items.*.image_url' => ['nullable', 'string', 'max:2000'],
            'items.*.vat_percent' => ['nullable'],
        ]);

        $result = $service->importReviewedItems(
            $supplierConnection,
            $log,
            (int) $validated['distributor_id'],
            !empty($validated['default_brand_id']) ? (int) $validated['default_brand_id'] : null,
            $validated['items'] ?? [],
            !empty($validated['default_article_group_id']) ? (int) $validated['default_article_group_id'] : null,
            !empty($validated['default_sub_article_group_id']) ? (int) $validated['default_sub_article_group_id'] : null,
            $request->boolean('update_price_only_if_exists')
        );

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['message']);
        }

        $targetSectionIndex = $request->filled('target_section_index')
            ? (int) $request->integer('target_section_index')
            : null;

        $items = $this->appendImportedProductsToOffer(
            folder: $folder,
            connection: $supplierConnection->fresh('distributor'),
            log: $log->fresh(),
            targetSectionIndex: $targetSectionIndex
        );

        broadcast(new OfferSupplierProductsReturned(
            folder: $folder->fresh(['offer']),
            log: $log->fresh(),
            items: $items,
            targetSectionIndex: $targetSectionIndex
        ))->toOthers();

        return view('admin.offer.supplier-return-done', [
            'folder' => $folder,
            'connection' => $supplierConnection,
            'log' => $log->fresh(),
            'items' => $items,
            'targetSectionIndex' => $targetSectionIndex,
        ]);
    }

    protected function appendImportedProductsToOffer(
        OfferFolder $folder,
        SupplierConnection $connection,
        SupplierImportLog $log,
        ?int $targetSectionIndex = null
    ): array {
        $folder->loadMissing(['offer', 'detail', 'offer.detail']);

        $payload = is_array($log->payload ?? null) ? $log->payload : [];
        $savedProducts = collect($payload['saved_products'] ?? []);

        if ($savedProducts->isEmpty()) {
            return [];
        }

        $detail = $this->resolveOrCreateDetail($folder);

        $sections = is_array($detail->sections ?? null)
            ? $detail->sections
            : [];

        [$sectionIndex, $sections] = $this->resolveTargetSection($sections, $targetSectionIndex);

        $offerItems = [];

        foreach ($savedProducts as $saved) {
            $productId = (int) ($saved['product_id'] ?? $saved['id'] ?? 0);

            if ($productId <= 0) {
                continue;
            }

            $supplierArticleNo = trim((string) (
                $saved['article_no']
                ?? $saved['distributor_article_no']
                ?? ''
            ));

            $product = Product::query()
                ->with(['brand', 'firstImage', 'measure'])
                ->find($productId);

            if (!$product) {
                continue;
            }

            $priceQuery = DistributorPrice::query()
                ->with('distributor')
                ->where('product_id', $product->id)
                ->where('distributor_id', $connection->distributor_id ?: ($saved['distributor_id'] ?? null));

            if ($supplierArticleNo !== '') {
                $priceQuery->where('article_no', $supplierArticleNo);
            }

            $distributorPrice = $priceQuery
                ->latest('id')
                ->first();

            $offerItem = $this->buildOfferItemPayload(
                product: $product,
                distributorPrice: $distributorPrice,
                connection: $connection,
                saved: $saved,
                log: $log
            );

            $sections[$sectionIndex]['items'][] = $offerItem;
            $offerItems[] = $offerItem;
        }

        $detail->sections = $sections;

        $taxRate = (float) ($detail->tax_rate ?? 19);
        $totalNet = $this->sumSectionsNet($sections);

        if (Schema::hasColumn($detail->getTable(), 'total_net')) {
            $detail->total_net = $totalNet;
        }

        if (Schema::hasColumn($detail->getTable(), 'total_gross')) {
            $detail->total_gross = $totalNet * (1 + ($taxRate / 100));
        }

        if (Schema::hasColumn($detail->getTable(), 'biography_data')) {
            $bio = is_array($detail->biography_data ?? null) ? $detail->biography_data : [];
            $bio[] = [
                'type' => 'supplier_products_imported',
                'title' => 'Lieferantenartikel übernommen',
                'message' => count($offerItems) . ' Artikel von ' . $connection->name . ' übernommen.',
                'supplier_connection_id' => $connection->id,
                'supplier_import_log_id' => $log->id,
                'created_at' => now()->toDateTimeString(),
            ];
            $detail->biography_data = $bio;
        }

        $detail->save();

        $payload['offer_folder_id'] = (int) $folder->id;
        $payload['offer_id'] = (int) ($folder->offer_id ?: $folder->offer?->id);
        $payload['target_section_index'] = $sectionIndex;
        $payload['appended_offer_items'] = $offerItems;

        $log->payload = $payload;
        $log->save();

        return $offerItems;
    }

    protected function resolveOrCreateDetail(OfferFolder $folder): OfferDetail
    {
        if ($folder->detail) {
            return $folder->detail;
        }

        if ($folder->offer?->detail) {
            return $folder->offer->detail;
        }

        $detail = new OfferDetail();
        $detail->offer_id = (int) ($folder->offer_id ?: $folder->offer?->id);
        $detail->offer_folder_id = $folder->id;

        if (Schema::hasColumn($detail->getTable(), 'sections')) {
            $detail->sections = [];
        }

        if (Schema::hasColumn($detail->getTable(), 'tax_rate')) {
            $detail->tax_rate = 19;
        }

        if (Schema::hasColumn($detail->getTable(), 'placed_images')) {
            $detail->placed_images = [];
        }

        if (Schema::hasColumn($detail->getTable(), 'biography_data')) {
            $detail->biography_data = [];
        }

        if (Schema::hasColumn($detail->getTable(), 'document_status')) {
            $detail->document_status = 'offer';
        }

        $detail->save();

        return $detail;
    }

    protected function resolveTargetSection(array $sections, ?int $targetSectionIndex): array
    {
        if (
            $targetSectionIndex !== null
            && isset($sections[$targetSectionIndex])
            && empty($sections[$targetSectionIndex]['_pageBreak'])
            && empty($sections[$targetSectionIndex]['_virtualSection'])
            && empty($sections[$targetSectionIndex]['isLocked'])
        ) {
            if (!isset($sections[$targetSectionIndex]['items']) || !is_array($sections[$targetSectionIndex]['items'])) {
                $sections[$targetSectionIndex]['items'] = [];
            }

            return [$targetSectionIndex, $sections];
        }

        foreach ($sections as $index => $section) {
            if (
                empty($section['_pageBreak'])
                && empty($section['_virtualSection'])
                && empty($section['isLocked'])
            ) {
                if (!isset($sections[$index]['items']) || !is_array($sections[$index]['items'])) {
                    $sections[$index]['items'] = [];
                }

                return [$index, $sections];
            }
        }

        $sections[] = [
            'id' => 'supplier_section_' . Str::uuid(),
            'title' => 'Lieferantenartikel',
            'name' => 'Lieferantenartikel',
            'items' => [],
            'config' => [
                'hidePrices' => false,
            ],
        ];

        return [count($sections) - 1, $sections];
    }

    protected function buildOfferItemPayload(
        Product $product,
        ?DistributorPrice $distributorPrice,
        SupplierConnection $connection,
        array $saved,
        SupplierImportLog $log
    ): array {
        $distributor = $distributorPrice?->distributor ?: $connection->distributor;
        $distributorName = $distributor
            ? trim((string) ($distributor->short_name ?: $distributor->name ?: ('Lieferant #' . $distributor->id)))
            : $connection->name;

        $purchasePrice = (float) (
            $distributorPrice?->purchase_price
            ?? $saved['purchase_price']
            ?? $saved['price']
            ?? 0
        );

        $unitPrice = (float) (
            $distributorPrice?->price
            ?? $saved['price']
            ?? $purchasePrice
        );

        $marginPercent = $purchasePrice > 0
            ? (($unitPrice - $purchasePrice) / $purchasePrice) * 100
            : 20;

        $unit = $this->resolveProductUnit($product);

        return [
            'id' => 'supplier_' . $connection->id . '_' . $product->id . '_' . now()->timestamp . '_' . Str::random(5),

            'item_type' => 'product',
            'kind' => 'article',
            'status' => 'normal',
            'active' => true,

            'product_id' => (int) $product->id,
            'productId' => (int) $product->id,

            'name' => $product->product ?: ('Produkt #' . $product->id),
            'desc_html' => $product->short_description ?: '',
            'description' => $product->short_description ?: '',

            'article_no' => $product->article_no ?: ($saved['manufacturer_article_no'] ?? ''),
            'manufacturer_article_no' => $product->article_no ?: ($saved['manufacturer_article_no'] ?? ''),

            'distributor_article_no' => $distributorPrice?->article_no
                ?: ($saved['article_no'] ?? $saved['distributor_article_no'] ?? ''),

            'distributor_id' => $distributor?->id ? (int) $distributor->id : ($connection->distributor_id ?: null),
            'distributor_price_id' => $distributorPrice?->id ? (int) $distributorPrice->id : null,
            'distributor_name' => $distributorName,
            'supplier' => $distributorName,

            'qty' => 1,
            'unit' => $unit,
            'measure' => $unit,

            'price' => $unitPrice,
            'unit_price' => $unitPrice,
            'ek' => $purchasePrice,
            'purchase_price' => $purchasePrice,

            'margin' => round($marginPercent, 2),
            'marginPercent' => round($marginPercent, 2),

            'availability' => $distributorPrice?->availability,
            'skonto' => (float) ($distributor?->cash_discount ?? 0),
            'payment_terms' => $distributor?->payment_terms,

            'price_unit_value' => 1,
            'price_unit_label' => $unit,
            'price_unit_text' => '1 ' . $unit,

            'img' => $product->main_image_url,
            'image_url' => $product->main_image_url,

            'subItems' => [],
            'labor_rows' => [],
            'hideImage' => false,
            'hidePrices' => false,
            'hideNumbering' => false,

            '_source' => 'supplier_import',
            '_supplier_connection_id' => (int) $connection->id,
            '_supplier_import_log_id' => (int) $log->id,
            '_freshFromSupplier' => true,
        ];
    }

    protected function resolveProductUnit(Product $product): string
    {
        $unit = trim((string) (
            $product->measure_unit
            ?: $product->price_unit
            ?: $product->measure?->measure
            ?: ''
        ));

        return $unit !== '' ? $unit : 'Stk';
    }

    protected function sumSectionsNet(array $sections): float
    {
        $sum = 0;

        foreach ($sections as $section) {
            foreach (($section['items'] ?? []) as $item) {
                if (is_array($item)) {
                    $sum += $this->sumItemNet($item);
                }
            }
        }

        return round($sum, 2);
    }

    protected function sumItemNet(array $item): float
    {
        $qty = (float) ($item['qty'] ?? 1);
        $price = (float) ($item['price'] ?? $item['unit_price'] ?? 0);
        $total = $qty * $price;

        foreach (($item['subItems'] ?? []) as $child) {
            if (is_array($child)) {
                $total += $this->sumItemNet($child);
            }
        }

        return $total;
    }
}
