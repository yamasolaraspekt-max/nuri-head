<?php

namespace App\Services\Suppliers;

use App\Models\Distributor;
use App\Models\DistributorPrice;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SupplierConnection;
use App\Models\SupplierImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SupplierConnectorService
{
    public function buildOpenParams(SupplierConnection $connection, array $runtimeParams = []): array
    {
        $requestConfig = is_array($connection->request_config)
            ? $connection->request_config
            : [];

        $params = [];

        $searchValue = $runtimeParams['searchterm']
            ?? $runtimeParams['query']
            ?? $runtimeParams['search']
            ?? 'test';

        /*
        |--------------------------------------------------------------------------
        | These keys are internal Laravel config keys.
        | They must NEVER be sent to the supplier shop.
        |--------------------------------------------------------------------------
        */
        $blockedKeys = [
            'method',
            'open_mode',
            'content_type',
            'timeout',
            'basket_param',
            'search_param',
            'auth_param_map',
            'extra_params',
            'match_by',
            'update_existing',
            'create_missing',
            'price_mode',
            'update_price_only_if_exists',
            'request_config',
            'import_config',
        ];

        /*
        |--------------------------------------------------------------------------
        | Extra params only.
        | Example allowed static supplier params can be action/version/target.
        |--------------------------------------------------------------------------
        */
        $extraParams = $requestConfig['extra_params'] ?? [];

        if (!is_array($extraParams)) {
            $extraParams = [];
        }

        foreach ($extraParams as $key => $value) {
            if ($key === null || $key === '') {
                continue;
            }

            if (in_array($key, $blockedKeys, true)) {
                continue;
            }

            if (is_array($value)) {
                continue;
            }

            $params[$key] = $value;
        }

        /*
        |--------------------------------------------------------------------------
        | Auth parameter map
        |--------------------------------------------------------------------------
        | FEGA format:
        | USERNAME => {username}
        | PASSWORD => {password}
        | KUNDENNR => {customer_number}
        |--------------------------------------------------------------------------
        */
        $authMap = $requestConfig['auth_param_map'] ?? [];

        if (!is_array($authMap)) {
            $authMap = [];
        }

        $values = [
            'username' => $connection->username,
            'password' => $connection->password,
            'customer_number' => $connection->customer_number,
            'token' => $connection->token,
            'searchterm' => $searchValue,
            'query' => $searchValue,
            'callback_url' => $this->resolveHookUrl($connection, $runtimeParams),
            'hook_url' => $this->resolveHookUrl($connection, $runtimeParams),
            'return_url' => $this->resolveReturnUrl($connection, $runtimeParams),
        ];

        foreach ($authMap as $key => $templateOrTarget) {
            if (!$key || !$templateOrTarget) {
                continue;
            }

            if (in_array($key, $blockedKeys, true)) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Old / standard IDS format:
            | "USERNAME": "{username}"
            |--------------------------------------------------------------------------
            */
            if (is_string($templateOrTarget) && str_contains($templateOrTarget, '{')) {
                $params[$key] = $this->replaceTemplateValue($templateOrTarget, $values);
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | New map format:
            | "username": "USERNAME"
            |--------------------------------------------------------------------------
            */
            if (array_key_exists($key, $values) && is_string($templateOrTarget)) {
                $params[$templateOrTarget] = $values[$key];
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Static values:
            | "action": "AS"
            |--------------------------------------------------------------------------
            */
            if (!is_array($templateOrTarget)) {
                $params[$key] = $templateOrTarget;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Search param
        |--------------------------------------------------------------------------
        */
        $searchParam = $requestConfig['search_param'] ?? 'SEARCHTERM';

        if ($searchParam && !in_array($searchParam, $blockedKeys, true)) {
            $params[$searchParam] = $searchValue;
        }

        /*
        |--------------------------------------------------------------------------
        | Runtime params.
        | Only uid/auto may be added to callback URL, not forwarded to supplier.
        |--------------------------------------------------------------------------
        */
        foreach ($runtimeParams as $key => $value) {
            if (in_array($key, ['query', 'search', 'searchterm', 'auto', 'uid'], true)) {
                continue;
            }

            if (in_array($key, $blockedKeys, true)) {
                continue;
            }

            if (is_array($value)) {
                continue;
            }

            if (!array_key_exists($key, $params)) {
                $params[$key] = $value;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | GC Online special normalization.
        |--------------------------------------------------------------------------
        */
        if ($this->shouldNormalizeToGcOnline($connection, $params)) {
            $params = $this->normalizeGcOnlineParams($connection, $params, $runtimeParams, $searchValue);
        }

        return collect($params)
            ->reject(fn($value, $key) => in_array((string) $key, $blockedKeys, true))
            ->filter(fn($value) => $value !== null && $value !== '')
            ->map(
                fn($value) => is_array($value)
                ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : $value
            )
            ->toArray();
    }
    private function shouldNormalizeToGcOnline(SupplierConnection $connection, array $params = []): bool
    {
        $supplierKey = strtolower((string) $connection->supplier_key);
        $name = strtolower((string) $connection->name);
        $url = strtolower((string) $connection->endpoint_url);

        if (
            str_contains($supplierKey, 'gc')
            || str_contains($supplierKey, 'gc_online')
            || str_contains($supplierKey, 'gconline')
            || str_contains($name, 'gc online')
            || str_contains($name, 'gconline')
            || str_contains($name, 'gc-online')
            || str_contains($url, 'gconline')
        ) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | Even if the supplier name/key is wrong, this tells us the mapping is
        | the old generic IDS mapping and must be converted for GC.
        |--------------------------------------------------------------------------
        */
        $keys = array_map('strtoupper', array_keys($params));

        return in_array('USERNAME', $keys, true)
            && in_array('KUNDENNR', $keys, true)
            && (
                in_array('RUECKURL', $keys, true)
                || in_array('RETURNURL', $keys, true)
            );
    }

    private function normalizeGcOnlineParams(
        SupplierConnection $connection,
        array $params,
        array $runtimeParams = [],
        string $searchValue = 'test'
    ): array {
        $normalized = [];

        $normalized['action'] = $params['action']
            ?? $params['ACTION']
            ?? 'AS';

        $normalized['version'] = $params['version']
            ?? $params['VERSION']
            ?? '2.5';

        $normalized['target'] = $params['target']
            ?? $params['TARGET']
            ?? 'TOP';

        $normalized['kndnr'] = $params['kndnr']
            ?? $params['KNDNR']
            ?? $params['KUNDENNR']
            ?? $params['customer_number']
            ?? $connection->customer_number;

        $normalized['name_kunde'] = $params['name_kunde']
            ?? $params['NAME_KUNDE']
            ?? $params['USERNAME']
            ?? $params['username']
            ?? $connection->username;

        $normalized['pw_kunde'] = $params['pw_kunde']
            ?? $params['PW_KUNDE']
            ?? $params['PASSWORD']
            ?? $params['password']
            ?? $connection->password;

        $normalized['searchterm'] = $params['searchterm']
            ?? $params['SEARCHTERM']
            ?? $runtimeParams['searchterm']
            ?? $runtimeParams['query']
            ?? $searchValue
            ?? 'test';

        $normalized['hookurl'] = $params['hookurl']
            ?? $params['HOOKURL']
            ?? $params['RETURNURL']
            ?? $params['callback_url']
            ?? $this->resolveHookUrl($connection, $runtimeParams);

        $normalized['rueckurl'] = $params['rueckurl']
            ?? $params['RUECKURL']
            ?? $params['RETURN_URL']
            ?? $params['return_url']
            ?? $this->resolveReturnUrl($connection, $runtimeParams);

        return $normalized;
    }
    public function buildOpenUrl(SupplierConnection $connection, array $runtimeParams = []): string
    {
        return rtrim((string) $connection->endpoint_url, '?') . '?' . http_build_query(
            $this->buildOpenParams($connection, $runtimeParams)
        );
    }

    public function handleReturn(
        Request $request,
        SupplierConnection $connection,
        bool $autoImport = false
    ): SupplierImportLog {
        $payload = $request->all();
        $items = $this->extractItemsFromPayload($request, $connection);

        $log = SupplierImportLog::create([
            'supplier_connection_id' => $connection->id,
            'status' => $autoImport ? 'pending' : 'received',
            'source_type' => $connection->connector_type . '_return',
            'total_items' => count($items),
            'success_items' => 0,
            'failed_items' => 0,
            'message' => $autoImport
                ? 'Rückgabe empfangen. Auto-Import wird ausgeführt.'
                : 'Rückgabe empfangen. Bitte Daten prüfen und Import starten.',
            'payload' => [
                'request' => $payload,
                'items' => $items,
                'errors' => [],
                'saved_products' => [],
            ],
            'started_at' => now(),
            'finished_at' => $autoImport ? null : now(),
        ]);

        if (empty($items)) {
            $log->update([
                'status' => 'failed',
                'message' => 'Keine Artikel im Rückgabe-Payload gefunden. Empfangene Felder: ' . implode(', ', array_keys($payload)),
                'payload' => [
                    'request' => $payload,
                    'items' => [],
                    'errors' => [
                        [
                            'error' => 'Keine Artikel im Rückgabe-Payload gefunden.',
                            'request_keys' => array_keys($payload),
                        ],
                    ],
                    'saved_products' => [],
                ],
                'finished_at' => now(),
            ]);

            return $log;
        }

        if (!$autoImport) {
            return $log;
        }

        $reviewedItems = $this->itemsToReviewedRows($items);

        $this->importReviewedItems(
            $connection,
            $log,
            $connection->distributor_id,
            null,
            $reviewedItems,
            null,
            null,
            true
        );

        return $log->fresh();
    }

    public function importReviewedItems(
        SupplierConnection $connection,
        SupplierImportLog $log,
        ?int $distributorId,
        ?int $defaultBrandId,
        array $reviewedItems,
        ?int $defaultArticleGroupId = null,
        ?int $defaultSubArticleGroupId = null,
        bool $updatePriceOnlyIfExists = true
    ): array {
        $payload = is_array($log->payload ?? null) ? $log->payload : [];
        $rawItems = $payload['items'] ?? [];

        if (!$distributorId) {
            return $this->markLogFailed($log, $payload, 'Bitte zuerst einen Lieferanten auswählen.');
        }

        $distributor = Distributor::find($distributorId);

        if (!$distributor) {
            return $this->markLogFailed($log, $payload, 'Der ausgewählte Lieferant wurde nicht gefunden.');
        }

        $success = 0;
        $failed = 0;
        $errors = [];
        $savedProducts = [];

        foreach ($reviewedItems as $index => $row) {
            if (!($row['import'] ?? false)) {
                continue;
            }

            try {
                $savedProduct = DB::transaction(function () use (
                    $connection,
                    $distributor,
                    $defaultBrandId,
                    $defaultArticleGroupId,
                    $defaultSubArticleGroupId,
                    $updatePriceOnlyIfExists,
                    $row,
                    $index
                ) {
                    return $this->importReviewedRow(
                        $connection,
                        $distributor,
                        $defaultBrandId,
                        $defaultArticleGroupId,
                        $defaultSubArticleGroupId,
                        $updatePriceOnlyIfExists,
                        $row,
                        $index
                    );
                });

                $success++;

                $savedProducts[] = [
                    'id' => $savedProduct['product_id'],
                    'product_id' => $savedProduct['product_id'],
                    'product' => $savedProduct['product_title'],
                    'article_no' => $savedProduct['distributor_article_no'],
                    'manufacturer_article_no' => $savedProduct['manufacturer_article_no'],
                    'price' => $savedProduct['price'],
                    'purchase_price' => $savedProduct['purchase_price'],
                    'price_only' => $savedProduct['price_only'],
                    'was_existing_product' => $savedProduct['was_existing_product'],
                    'url' => url('product_details/' . $savedProduct['product_id']),
                ];
            } catch (\Throwable $exception) {
                $failed++;

                $errors[] = [
                    'index' => $index + 1,
                    'error' => $exception->getMessage(),
                    'row' => $row,
                    'raw_item' => $rawItems[$index] ?? null,
                ];

                Log::error('Supplier reviewed import failed', [
                    'connection_id' => $connection->id,
                    'supplier' => $connection->supplier_key,
                    'index' => $index,
                    'error' => $exception->getMessage(),
                    'row' => $row,
                ]);
            }
        }

        $message = $failed > 0
            ? "{$success} erfolgreich, {$failed} fehlgeschlagen."
            : "{$success} Artikel erfolgreich importiert.";

        if ($success === 0 && $failed === 0) {
            $message = 'Keine Artikel ausgewählt.';
        }

        if (!empty($errors)) {
            $message .= ' Fehler: ' . ($errors[0]['error'] ?? 'Unbekannter Fehler');
        }

        $log->update([
            'status' => $failed > 0 ? 'failed' : ($success > 0 ? 'success' : 'received'),
            'success_items' => $success,
            'failed_items' => $failed,
            'total_items' => count($reviewedItems),
            'message' => $message,
            'payload' => [
                'request' => $payload['request'] ?? [],
                'items' => $rawItems,
                'reviewed_items' => $reviewedItems,
                'errors' => $errors,
                'saved_products' => $savedProducts,
                'selected' => [
                    'distributor_id' => $distributorId,
                    'brand_id' => $defaultBrandId,
                    'article_group_id' => $defaultArticleGroupId,
                    'sub_article_group_id' => $defaultSubArticleGroupId,
                    'update_price_only_if_exists' => $updatePriceOnlyIfExists,
                ],
            ],
            'finished_at' => now(),
        ]);

        return [
            'success' => $failed === 0 && $success > 0,
            'message' => $message,
        ];
    }

    private function importReviewedRow(
        SupplierConnection $connection,
        Distributor $distributor,
        ?int $defaultBrandId,
        ?int $defaultArticleGroupId,
        ?int $defaultSubArticleGroupId,
        bool $updatePriceOnlyIfExists,
        array $row,
        int $index
    ): array {
        $productTitle = trim((string) ($row['product_title'] ?? ''));
        $distributorArticleNo = trim((string) ($row['distributor_article_no'] ?? ''));
        $manufacturerArticleNo = trim((string) ($row['manufacturer_article_no'] ?? ''));
        $ean = trim((string) ($row['ean'] ?? ''));
        $measureUnit = trim((string) ($row['measure_unit'] ?? ''));
        $shortDescription = trim((string) ($row['short_description'] ?? ''));
        $imageUrl = trim((string) ($row['image_url'] ?? ''));

        if ($manufacturerArticleNo === '') {
            $manufacturerArticleNo = 'Not filled';
        }

        $brandId = !empty($row['brand_id']) ? (int) $row['brand_id'] : $defaultBrandId;
        $articleGroupId = !empty($row['article_group_id']) ? (int) $row['article_group_id'] : $defaultArticleGroupId;
        $subArticleGroupId = !empty($row['sub_article_group_id']) ? (int) $row['sub_article_group_id'] : $defaultSubArticleGroupId;

        $price = $this->toDecimal($row['price'] ?? null);
        $purchasePrice = $this->toDecimal($row['purchase_price'] ?? null);
        $discountPrice = $this->toDecimal($row['discount_price'] ?? null);
        $discountPercent = $this->toDecimal($row['discount_percent'] ?? null);
        $vatPercent = $this->toDecimal($row['vat_percent'] ?? 19);
        $availability = trim((string) ($row['availability'] ?? ''));

        if ($productTitle === '') {
            throw new \RuntimeException('Produkt-Titel fehlt.');
        }

        if ($distributorArticleNo === '') {
            throw new \RuntimeException('Lieferanten-Artikelnummer fehlt.');
        }

        $result = $this->findOrCreateReviewedProduct(
            $productTitle,
            $distributorArticleNo,
            $manufacturerArticleNo,
            $ean !== '' ? $ean : null,
            $brandId,
            $articleGroupId,
            $subArticleGroupId,
            $measureUnit,
            $shortDescription !== '' ? $shortDescription : null,
            $vatPercent,
            $updatePriceOnlyIfExists
        );

        /** @var Product $product */
        $product = $result['product'];
        $wasExistingProduct = $result['was_existing_product'];
        $priceOnly = $result['price_only'];

        DistributorPrice::updateOrCreate(
            [
                'distributor_id' => $distributor->id,
                'product_id' => $product->id,
                'article_no' => $distributorArticleNo,
            ],
            [
                'discount_group_id' => null,
                'discount_price' => $discountPrice,
                'discount_percent' => $discountPercent,
                'price' => $price,
                'purchase_price' => $purchasePrice ?? $price,
                'price_date' => now(),
                'availability' => $availability !== '' ? $availability : null,
                'status' => 1,
                'creator_id' => auth()->id(),
                'notice' => trim(
                    'Importiert über ' . $connection->name .
                    ($measureUnit !== '' ? ' | Einheit: ' . $measureUnit : '') .
                    ($priceOnly ? ' | Nur Preis aktualisiert' : '')
                ),
            ]
        );

        if ($imageUrl !== '' && !$priceOnly) {
            $this->saveProductImageIfAvailable($product, $imageUrl);
        }

        return [
            'product_id' => $product->id,
            'product_title' => $product->product,
            'distributor_article_no' => $distributorArticleNo,
            'manufacturer_article_no' => $manufacturerArticleNo,
            'price' => $price,
            'purchase_price' => $purchasePrice ?? $price,
            'price_only' => $priceOnly,
            'was_existing_product' => $wasExistingProduct,
        ];
    }

    private function findOrCreateReviewedProduct(
        string $productTitle,
        string $distributorArticleNo,
        string $manufacturerArticleNo,
        ?string $ean,
        ?int $brandId,
        ?int $articleGroupId,
        ?int $subArticleGroupId,
        ?string $measureUnit,
        ?string $shortDescription,
        ?float $vatPercent,
        bool $updatePriceOnlyIfExists = true
    ): array {
        $product = null;

        if ($ean) {
            $product = Product::where('ean', $ean)->first();
        }

        if (!$product && $manufacturerArticleNo !== '' && $manufacturerArticleNo !== 'Not filled') {
            $product = Product::where('article_no', $manufacturerArticleNo)->first();
        }

        if (!$product) {
            $existingPrice = DistributorPrice::where('article_no', $distributorArticleNo)
                ->with('product')
                ->first();

            if ($existingPrice && $existingPrice->product) {
                $product = $existingPrice->product;
            }
        }

        $wasExistingProduct = (bool) $product;

        if ($product && $updatePriceOnlyIfExists) {
            return [
                'product' => $product,
                'was_existing_product' => true,
                'price_only' => true,
            ];
        }

        $data = [
            'article_no' => $manufacturerArticleNo ?: 'Not filled',
            'sku' => $distributorArticleNo,
            'ean' => $ean ?: null,
            'brand_id' => $brandId,
            'article_group' => $articleGroupId,
            'sub_article' => $subArticleGroupId,
            'product' => $productTitle,
            'category' => 'product',
            'measure_unit' => $this->normalizeMeasureUnitForProduct($measureUnit),
            'vat_percent' => $vatPercent ?? 19,
            'short_description' => $shortDescription,
            'status' => 1,
        ];

        if ($product) {
            $product->update(array_filter($data, fn ($value) => $value !== null && $value !== ''));

            return [
                'product' => $product->fresh(),
                'was_existing_product' => true,
                'price_only' => false,
            ];
        }

        return [
            'product' => Product::create($data),
            'was_existing_product' => false,
            'price_only' => false,
        ];
    }

    private function normalizeMeasureUnitForProduct(?string $value): ?int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function itemsToReviewedRows(array $items): array
    {
        $rows = [];

        foreach ($items as $index => $rawItem) {
            $item = $this->extractOrderItem($rawItem);

            $rows[$index] = [
                'import' => 1,
                'product_title' => $this->stringValue(
                    $item['Kurztext']
                    ?? $item['Description']
                    ?? $item['Bezeichnung']
                    ?? $item['NEW_ITEM-DESCRIPTION']
                    ?? ''
                ),
                'distributor_article_no' => $this->stringValue(
                    $item['ArtNo']
                    ?? $item['ARTNO']
                    ?? $item['Artikelnummer']
                    ?? $item['NEW_ITEM-VENDORMAT']
                    ?? ''
                ),
                'manufacturer_article_no' => $this->stringValue(
                    $item['ManufacturerArtNo']
                    ?? $item['HerstellerArtikelnummer']
                    ?? $item['NEW_ITEM-MANUFACTMAT']
                    ?? 'Not filled'
                ),
                'ean' => $this->stringValue(
                    $item['EAN']
                    ?? $item['Ean']
                    ?? $item['NEW_ITEM-EAN']
                    ?? ''
                ),
                'price' => $this->stringValue(
                    $item['OfferPrice']
                    ?? $item['Price']
                    ?? $item['PREIS']
                    ?? ''
                ),
                'purchase_price' => $this->stringValue(
                    $item['NetPrice']
                    ?? $item['PurchasePrice']
                    ?? $item['EK']
                    ?? $item['NEW_ITEM-PRICE']
                    ?? ''
                ),
                'discount_price' => '',
                'discount_percent' => '',
                'availability' => $this->stringValue(
                    $item['Availability']
                    ?? $item['Verfuegbarkeit']
                    ?? ''
                ),
                'measure_unit' => $this->stringValue(
                    $item['QU']
                    ?? $item['Unit']
                    ?? $item['NEW_ITEM-UNIT']
                    ?? ''
                ),
                'short_description' => $this->stringValue(
                    $item['Langtext']
                    ?? $item['LongText']
                    ?? ''
                ),
                'image_url' => $this->stringValue(
                    $item['ImageUrl']
                    ?? $item['BildUrl']
                    ?? $item['Produktbild']
                    ?? $item['NEW_ITEM-IMAGE_URL']
                    ?? ''
                ),
                'vat_percent' => $this->stringValue(
                    $item['VAT']
                    ?? $item['Vat']
                    ?? 19
                ),
                'article_group_id' => '',
                'sub_article_group_id' => '',
            ];
        }

        return $rows;
    }

    public function mapItem(SupplierConnection $connection, array $rawItem): array
    {
        $mapped = [
            'products' => [],
            'brands' => [],
            'distributors' => [],
            'distributor_prices' => [],
        ];

        $item = $this->extractOrderItem($rawItem);
        $mappings = $connection->activeMappings()->get();

        foreach ($mappings as $mapping) {
            $value = Arr::get($item, $mapping->source_field)
                ?? Arr::get($rawItem, $mapping->source_field)
                ?? $this->getValueByFlexibleKey($item, $mapping->source_field)
                ?? $mapping->default_value;

            if ($mapping->is_required && ($value === null || $value === '')) {
                throw new \RuntimeException("Pflichtfeld fehlt: {$mapping->source_field}");
            }

            $value = $this->transformValue($value, $mapping->transformer);

            if (!array_key_exists($mapping->target_table, $mapped)) {
                continue;
            }

            $mapped[$mapping->target_table][$mapping->target_field] = $value;
        }

        return $mapped;
    }

    public function extractItemsFromPayload(Request $request, SupplierConnection $connection): array
    {
        $payload = $request->all();
        $xml = $this->getBasketPayload($request, $connection);

        if ($xml) {
            $items = $this->extractItemsFromXml($xml);

            if (!empty($items)) {
                return $items;
            }
        }

        return $this->extractItemsFromArrayPayload($payload);
    }

    private function extractItemsFromXml(string $xml): array
    {
        try {
            $xml = trim($xml);

            if ($xml === '') {
                return [];
            }

            $xmlObject = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

            if (!$xmlObject) {
                return [];
            }

            $array = json_decode(json_encode($xmlObject), true);

            if (!is_array($array)) {
                return [];
            }

            return $this->normalizeXmlItems($array);
        } catch (\Throwable $exception) {
            Log::error('Supplier XML parse failed', [
                'error' => $exception->getMessage(),
            ]);

            return [];
        }
    }

    private function normalizeXmlItems(array $data): array
    {
        $possiblePaths = [
            'Order.OrderItem',
            'Order.OrderItems.OrderItem',
            'Order.Items.Item',
            'Order.Item',
            'Order.Position',
            'Order.Positions.Position',
            'Warenkorb.Order.OrderItem',
            'Warenkorb.Order.OrderItems.OrderItem',
            'OrderItem',
            'item',
            'items.item',
            'warenkorb.item',
            'warenkorb.position',
            'warenkorb.items.item',
            'warenkorb.positions.position',
            'position',
            'positions.position',
            'ORDER_ITEM_LIST.ORDER_ITEM',
            'ORDER_ITEM_LIST.ITEM',
            'NEW_ITEM',
            'ARTICLE',
            'ARTIKEL',
            'DATA.ITEM',
            'DATA.POSITION',
        ];

        foreach ($possiblePaths as $path) {
            $items = Arr::get($data, $path);

            if (!$items) {
                continue;
            }

            if (isset($items[0]) && is_array($items[0])) {
                return array_values($items);
            }

            if (is_array($items)) {
                return [$items];
            }
        }

        if (isset($data['Order']) && is_array($data['Order'])) {
            return [$data['Order']];
        }

        $flatItems = $this->tryNormalizeIndexedXmlFields($data);

        if (!empty($flatItems)) {
            return $flatItems;
        }

        return [$data];
    }

    private function extractOrderItem(array $rawItem): array
    {
        $item = $rawItem['OrderItem'] ?? $rawItem;

        if (isset($item[0]) && is_array($item[0])) {
            return $item[0];
        }

        return is_array($item) ? $item : [];
    }

    private function tryNormalizeIndexedXmlFields(array $data): array
    {
        $itemLikeKeys = array_filter(array_keys($data), function ($key) {
            return str_starts_with((string) $key, 'NEW_ITEM')
                || str_starts_with((string) $key, 'ARTICLE')
                || str_starts_with((string) $key, 'ARTIKEL');
        });

        if (empty($itemLikeKeys)) {
            return [];
        }

        $max = 0;

        foreach ($itemLikeKeys as $key) {
            if (is_array($data[$key] ?? null)) {
                $max = max($max, count($data[$key]));
            }
        }

        if ($max <= 0) {
            return [$data];
        }

        $items = [];

        for ($i = 0; $i < $max; $i++) {
            foreach ($itemLikeKeys as $key) {
                $items[$i][$key] = $data[$key][$i] ?? null;
            }
        }

        return array_values($items);
    }

    private function extractItemsFromArrayPayload(array $payload): array
    {
        $items = [];

        foreach ($payload as $key => $value) {
            if (preg_match('/NEW_ITEM-DESCRIPTION(?:\[(\d+)\]|(\d+))?/', (string) $key, $matches)) {
                $index = $matches[1] ?? $matches[2] ?? 1;

                $items[$index]['NEW_ITEM-DESCRIPTION'] = $value;
                $items[$index]['NEW_ITEM-LONGTEXT'] = $this->payloadValue($payload, 'NEW_ITEM-LONGTEXT', $index);
                $items[$index]['NEW_ITEM-VENDORMAT'] = $this->payloadValue($payload, 'NEW_ITEM-VENDORMAT', $index);
                $items[$index]['NEW_ITEM-EAN'] = $this->payloadValue($payload, 'NEW_ITEM-EAN', $index);
                $items[$index]['NEW_ITEM-PRICE'] = $this->payloadValue($payload, 'NEW_ITEM-PRICE', $index);
                $items[$index]['NEW_ITEM-UNIT'] = $this->payloadValue($payload, 'NEW_ITEM-UNIT', $index);
                $items[$index]['NEW_ITEM-CURRENCY'] = $this->payloadValue($payload, 'NEW_ITEM-CURRENCY', $index);
                $items[$index]['NEW_ITEM-MANUFACTCODE'] = $this->payloadValue($payload, 'NEW_ITEM-MANUFACTCODE', $index);
                $items[$index]['NEW_ITEM-MANUFACTMAT'] = $this->payloadValue($payload, 'NEW_ITEM-MANUFACTMAT', $index);
                $items[$index]['NEW_ITEM-QUANTITY'] = $this->payloadValue($payload, 'NEW_ITEM-QUANTITY', $index);
                $items[$index]['NEW_ITEM-AVAILABILITY'] = $this->payloadValue($payload, 'NEW_ITEM-AVAILABILITY', $index);
            }
        }

        if (!empty($items)) {
            return array_values($items);
        }

        if (isset($payload['items']) && is_array($payload['items'])) {
            return $payload['items'];
        }

        return !empty($payload) ? [$payload] : [];
    }

    private function getBasketPayload(Request $request, SupplierConnection $connection): ?string
    {
        $requestConfig = is_array($connection->request_config ?? null)
            ? $connection->request_config
            : [];

        $basketParam = $requestConfig['basket_param'] ?? 'warenkorb';

        $value = $request->input($basketParam)
            ?? $request->input('warenkorb')
            ?? $request->input('cart')
            ?? $request->input('basket')
            ?? $request->input('xml')
            ?? $request->input('OCI_DATA')
            ?? $request->input('oci_data')
            ?? null;

        return is_string($value) && trim($value) !== '' ? $value : null;
    }

    private function defaultAuthParamMap(SupplierConnection $connection): array
    {
        if ($this->isGcOnline($connection)) {
            return [
                'action' => 'AS',
                'version' => '2.5',
                'target' => 'TOP',
                'kndnr' => '{customer_number}',
                'name_kunde' => '{username}',
                'pw_kunde' => '{password}',
                'searchterm' => '{searchterm}',
                'hookurl' => '{callback_url}',
                'rueckurl' => '{return_url}',
            ];
        }

        return [
            'USERNAME' => '{username}',
            'PASSWORD' => '{password}',
            'KUNDENNR' => '{customer_number}',
            'TOKEN' => '{token}',
            'RETURNURL' => '{callback_url}',
            'RUECKURL' => '{return_url}',
            'SEARCHTERM' => '{searchterm}',
        ];
    }

    private function buildMappedAuthParams(
        SupplierConnection $connection,
        array $authParamMap,
        array $runtimeParams = []
    ): array {
        $values = [
            'username' => $connection->username,
            'password' => $connection->password,
            'customer_number' => $connection->customer_number,
            'token' => $connection->token,
            'searchterm' => $runtimeParams['searchterm'] ?? $runtimeParams['query'] ?? null,
            'query' => $runtimeParams['query'] ?? $runtimeParams['searchterm'] ?? null,
            'callback_url' => $this->buildCallbackUrl($connection, $runtimeParams),
            'return_url' => $this->buildUserReturnUrl($connection),
        ];

        $params = [];

        foreach ($authParamMap as $paramName => $template) {
            $params[$paramName] = $this->replaceTemplateValue((string) $template, $values);
        }

        return $params;
    }

    private function replaceTemplateValue(string $template, array $values): string
    {
        $value = $template;

        foreach ($values as $key => $replacement) {
            $value = str_replace('{' . $key . '}', (string) $replacement, $value);
        }

        return $value;
    }

    private function buildCallbackUrl(SupplierConnection $connection, array $runtimeParams = []): string
    {
        if (!empty($connection->return_url)) {
            return $connection->return_url;
        }

        $params = [];

        if (!empty($runtimeParams['uid'])) {
            $params['uid'] = $runtimeParams['uid'];
        }

        if (!empty($runtimeParams['auto'])) {
            $params['auto'] = 1;
        }

        if (Route::has('admin.supplier-connectors.return')) {
            $url = route('admin.supplier-connectors.return', $connection);
        } else {
            $url = url('/admin/supplier-connectors/' . $connection->id . '/return');
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    private function buildUserReturnUrl(SupplierConnection $connection): string
    {
        if (Route::has('admin.supplier-connectors.search')) {
            return route('admin.supplier-connectors.search', $connection);
        }

        return url('/');
    }

    private function resolveReturnUrl(SupplierConnection $connection, array $runtimeParams = []): string
    {
        if (!empty($runtimeParams['return_url'])) {
            return $runtimeParams['return_url'];
        }

        if (!empty($connection->return_url)) {
            return $connection->return_url;
        }

        return $this->buildUserReturnUrl($connection);
    }

    private function resolveHookUrl(SupplierConnection $connection, array $runtimeParams = []): string
    {
        if (!empty($runtimeParams['hook_url'])) {
            return $runtimeParams['hook_url'];
        }

        if (!empty($runtimeParams['hookurl'])) {
            return $runtimeParams['hookurl'];
        }

        return $this->buildCallbackUrl($connection, $runtimeParams);
    }

    private function forceGcOnlineParams(SupplierConnection $connection, array $params, array $runtimeParams = []): array
    {
        $params['action'] = $params['action'] ?? 'AS';
        $params['version'] = $params['version'] ?? '2.5';
        $params['target'] = $params['target'] ?? 'TOP';

        $params['kndnr'] = $params['kndnr']
            ?? $params['KUNDENNR']
            ?? $params['customer_number']
            ?? $connection->customer_number;

        $params['name_kunde'] = $params['name_kunde']
            ?? $params['USERNAME']
            ?? $params['username']
            ?? $connection->username;

        $params['pw_kunde'] = $params['pw_kunde']
            ?? $params['PASSWORD']
            ?? $params['password']
            ?? $connection->password;

        $params['searchterm'] = $params['searchterm']
            ?? $params['SEARCHTERM']
            ?? $runtimeParams['searchterm']
            ?? $runtimeParams['query']
            ?? 'test';

        $params['hookurl'] = $params['hookurl']
            ?? $params['HOOKURL']
            ?? $this->resolveHookUrl($connection, $runtimeParams);

        $params['rueckurl'] = $params['rueckurl']
            ?? $params['RUECKURL']
            ?? $params['RETURNURL']
            ?? $this->resolveReturnUrl($connection, $runtimeParams);

        unset(
            $params['USERNAME'],
            $params['PASSWORD'],
            $params['KUNDENNR'],
            $params['RETURNURL'],
            $params['RUECKURL'],
            $params['SEARCHTERM'],
            $params['HOOKURL'],
            $params['customer_number'],
            $params['username'],
            $params['password']
        );

        return $params;
    }

    private function payloadValue(array $payload, string $field, string|int $index): mixed
    {
        return $payload["{$field}[{$index}]"]
            ?? $payload["{$field}{$index}"]
            ?? $payload["{$field}_{$index}"]
            ?? $payload[$field]
            ?? null;
    }

    private function getValueByFlexibleKey(array $item, string $wantedKey): mixed
    {
        if (array_key_exists($wantedKey, $item)) {
            return $item[$wantedKey];
        }

        $normalizedWanted = $this->normalizeKey($wantedKey);

        foreach ($item as $key => $value) {
            if ($this->normalizeKey((string) $key) === $normalizedWanted) {
                return $value;
            }
        }

        return null;
    }

    private function normalizeKey(string $key): string
    {
        return strtolower(str_replace(['-', '_', ' '], '', $key));
    }

    private function transformValue(mixed $value, ?string $transformer): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($transformer) {
            'text' => trim((string) $value),
            'html_strip' => trim(strip_tags((string) $value)),
            'decimal' => $this->toDecimal($value),
            'integer' => (int) $value,
            'uppercase' => mb_strtoupper((string) $value),
            'lowercase' => mb_strtolower((string) $value),
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => $this->stringValue($value),
        };
    }

    private function toDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = $this->stringValue($value);
        $value = str_replace(['€', 'EUR', ' '], '', $value);

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? round((float) $value, 2) : null;
    }

    private function stringValue(mixed $value): string
    {
        if (is_array($value)) {
            return '';
        }

        return trim((string) $value);
    }

    private function isGcOnline(SupplierConnection $connection): bool
    {
        $supplierKey = strtolower((string) $connection->supplier_key);
        $name = strtolower((string) $connection->name);
        $url = strtolower((string) $connection->endpoint_url);

        return str_contains($supplierKey, 'gc')
            || str_contains($supplierKey, 'gc_online')
            || str_contains($name, 'gc online')
            || str_contains($name, 'gc-online')
            || str_contains($url, 'gconline');
    }

    private function saveProductImageIfAvailable(Product $product, string $imageUrl): void
    {
        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return;
        }

        try {
            $response = Http::timeout(20)->get($imageUrl);

            if (!$response->successful()) {
                return;
            }

            $contentType = (string) $response->header('Content-Type');

            if (!str_starts_with($contentType, 'image/')) {
                return;
            }

            $extension = match (true) {
                str_contains($contentType, 'png') => 'png',
                str_contains($contentType, 'webp') => 'webp',
                str_contains($contentType, 'gif') => 'gif',
                default => 'jpg',
            };

            $directory = public_path('images/products');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $filename = 'supplier-' . $product->id . '-' . Str::random(12) . '.' . $extension;

            File::put($directory . '/' . $filename, $response->body());

            ProductImage::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'image' => $filename,
                ],
                [
                    'title' => $product->product,
                ]
            );
        } catch (\Throwable $exception) {
            Log::warning('Supplier product image could not be saved', [
                'product_id' => $product->id,
                'image_url' => $imageUrl,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function markLogFailed(SupplierImportLog $log, array $payload, string $message): array
    {
        $log->update([
            'status' => 'failed',
            'message' => $message,
            'payload' => array_merge($payload, [
                'errors' => array_merge($payload['errors'] ?? [], [
                    ['error' => $message],
                ]),
            ]),
            'finished_at' => now(),
        ]);

        return [
            'success' => false,
            'message' => $message,
        ];
    }
}