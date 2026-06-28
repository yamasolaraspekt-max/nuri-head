<?php

namespace App\Services\Suppliers;

use App\Models\Brand;
use App\Models\Distributor;
use App\Models\DistributorPrice;
use App\Models\Product;
use App\Models\SupplierConnection;
use Illuminate\Support\Facades\DB;

class SupplierProductImportService
{
    public function importMappedItem(SupplierConnection $connection, array $mappedData, array $rawItem = []): Product
    {
        return DB::transaction(function () use ($connection, $mappedData, $rawItem) {
            $productData = $mappedData['products'] ?? [];
            $brandData = $mappedData['brands'] ?? [];
            $distributorData = $mappedData['distributors'] ?? [];
            $priceData = $mappedData['distributor_prices'] ?? [];

            $this->fallbackFromRawItem($productData, $brandData, $priceData, $rawItem);

            $distributor = $this->resolveDistributor($connection, $distributorData);
            $brand = $this->resolveBrand($brandData);

            if ($brand) {
                $productData['brand_id'] = $brand->id;
            }

            $product = $this->resolveProduct($productData, $priceData, $distributor);

            if (!$product) {
                $product = Product::create(
                    $this->prepareProductCreateData($productData, $priceData)
                );
            } else {
                $this->updateProductIfAllowed($connection, $product, $productData);
            }

            $this->upsertDistributorPrice($connection, $distributor, $product, $priceData);

            return $product;
        });
    }

    private function fallbackFromRawItem(
        array &$productData,
        array &$brandData,
        array &$priceData,
        array $rawItem
    ): void {
        $item = $rawItem['OrderItem'] ?? $rawItem;

        if (isset($item[0]) && is_array($item[0])) {
            $item = $item[0];
        }

        if (!is_array($item)) {
            $item = [];
        }

        $productData['product'] = $productData['product']
            ?? $item['Kurztext']
            ?? $item['NEW_ITEM-DESCRIPTION']
            ?? $item['description']
            ?? $item['name']
            ?? $item['Bezeichnung']
            ?? $item['BEZEICHNUNG']
            ?? $item['Artikeltext']
            ?? $item['ARTIKELTEXT']
            ?? null;

        $productData['short_description'] = $productData['short_description']
            ?? $item['Langtext']
            ?? $item['LongText']
            ?? $item['NEW_ITEM-LONGTEXT']
            ?? $item['longtext']
            ?? null;

        $productData['ean'] = $productData['ean']
            ?? $item['EAN']
            ?? $item['Ean']
            ?? $item['NEW_ITEM-EAN']
            ?? $item['ean']
            ?? null;

        $productData['measure_unit'] = $productData['measure_unit']
            ?? $item['QU']
            ?? $item['Unit']
            ?? $item['NEW_ITEM-UNIT']
            ?? $item['unit']
            ?? null;

        $productData['model'] = $productData['model']
            ?? $item['NEW_ITEM-MANUFACTMAT']
            ?? $item['manufacturer_material']
            ?? $item['HerstellerArtikelnummer']
            ?? null;

        $productData['article_no'] = $productData['article_no']
            ?? $item['ManufacturerArtNo']
            ?? $item['HerstellerArtikelnummer']
            ?? $item['NEW_ITEM-MANUFACTMAT']
            ?? 'Not filled';

        $productData['vat_percent'] = $productData['vat_percent']
            ?? $item['VAT']
            ?? $item['Vat']
            ?? 19;

        $brandData['name'] = $brandData['name']
            ?? $item['NEW_ITEM-MANUFACTCODE']
            ?? $item['manufacturer']
            ?? $item['brand']
            ?? $item['Hersteller']
            ?? null;

        $priceData['article_no'] = $priceData['article_no']
            ?? $item['ArtNo']
            ?? $item['ARTNO']
            ?? $item['NEW_ITEM-VENDORMAT']
            ?? $item['supplier_article_no']
            ?? $item['article_no']
            ?? $item['Artikelnummer']
            ?? $item['Bestellnummer']
            ?? null;

        $priceData['price'] = $priceData['price']
            ?? $item['OfferPrice']
            ?? $item['Price']
            ?? $item['NEW_ITEM-PRICE']
            ?? $item['price']
            ?? $item['Preis']
            ?? null;

        $priceData['purchase_price'] = $priceData['purchase_price']
            ?? $item['NetPrice']
            ?? $item['PurchasePrice']
            ?? $item['EK']
            ?? $item['NEW_ITEM-PRICE']
            ?? $item['purchase_price']
            ?? $item['price']
            ?? null;

        $priceData['availability'] = $priceData['availability']
            ?? $item['Availability']
            ?? $item['Verfuegbarkeit']
            ?? $item['NEW_ITEM-AVAILABILITY']
            ?? null;

        $priceData['measure_unit'] = $priceData['measure_unit']
            ?? $productData['measure_unit']
            ?? null;

        $priceData['price'] = $this->toDecimal($priceData['price'] ?? null);
        $priceData['purchase_price'] = $this->toDecimal($priceData['purchase_price'] ?? null);
    }

    private function resolveDistributor(SupplierConnection $connection, array $distributorData = []): Distributor
    {
        if (!empty($distributorData['id'])) {
            $distributor = Distributor::find($distributorData['id']);

            if ($distributor) {
                return $distributor;
            }
        }

        if ($connection->distributor) {
            return $connection->distributor;
        }

        $name = $distributorData['name'] ?? $connection->name;
        $shortName = $distributorData['short_name'] ?? $connection->supplier_key;

        return Distributor::firstOrCreate(
            ['short_name' => $shortName],
            [
                'name' => $name,
                'type' => 'supplier',
                'status' => 1,
                'created_date' => now(),
            ]
        );
    }

    private function resolveBrand(array $brandData): ?Brand
    {
        if (!empty($brandData['id'])) {
            return Brand::find($brandData['id']);
        }

        $brandName = $brandData['name'] ?? null;

        if (!$brandName || trim((string) $brandName) === '') {
            return null;
        }

        return Brand::firstOrCreate(
            ['name' => trim((string) $brandName)],
            [
                'status' => 1,
                'type' => 'manufacturer',
            ]
        );
    }

    private function resolveProduct(array $productData, array $priceData, Distributor $distributor): ?Product
    {
        if (!empty($productData['ean'])) {
            $product = Product::where('ean', $productData['ean'])->first();

            if ($product) {
                return $product;
            }
        }

        if (!empty($productData['article_no']) && $productData['article_no'] !== 'Not filled') {
            $product = Product::where('article_no', $productData['article_no'])->first();

            if ($product) {
                return $product;
            }
        }

        if (!empty($priceData['article_no'])) {
            $price = DistributorPrice::where('distributor_id', $distributor->id)
                ->where('article_no', $priceData['article_no'])
                ->with('product')
                ->first();

            if ($price && $price->product) {
                return $price->product;
            }
        }

        return null;
    }

    private function prepareProductCreateData(array $productData, array $priceData): array
    {
        $distributorArticleNo = $priceData['article_no'] ?? null;
        $manufacturerArticleNo = $productData['article_no'] ?? 'Not filled';

        if (!$manufacturerArticleNo || trim((string) $manufacturerArticleNo) === '') {
            $manufacturerArticleNo = 'Not filled';
        }

        return [
            // products.article_no = Hersteller-Artikelnummer
            'article_no' => $manufacturerArticleNo,

            // sku may use supplier/distributor article number
            'sku' => $productData['sku'] ?? $distributorArticleNo,

            'ean' => $productData['ean'] ?? null,
            'brand_id' => $productData['brand_id'] ?? null,

            // Correct product table columns
            'article_group' => $productData['article_group']
                ?? $productData['article_group_id']
                ?? null,

            'sub_article' => $productData['sub_article']
                ?? $productData['sub_article_group_id']
                ?? null,

            'product' => $productData['product']
                ?? $productData['name']
                ?? $distributorArticleNo
                ?? 'Unbenannter Artikel',

            'model' => $productData['model'] ?? null,
            'category' => $productData['category'] ?? 'product',
            'color' => $productData['color'] ?? null,
            'measure_unit' => $this->normalizeMeasureUnitForProduct($productData['measure_unit'] ?? null),
            'price_unit' => $productData['price_unit'] ?? null,
            'package_unit' => $productData['package_unit'] ?? null,
            'vat_percent' => $this->toDecimal($productData['vat_percent'] ?? 19),

            'short_description' => $productData['short_description']
                ?? $productData['description']
                ?? null,

            'status' => $productData['status'] ?? 1,
        ];
    }

    private function updateProductIfAllowed(
        SupplierConnection $connection,
        Product $product,
        array $productData
    ): void {
        $importConfig = $connection->import_config ?? [];

        if (!($importConfig['update_existing'] ?? true)) {
            return;
        }

        $productData['category'] = $productData['category'] ?? 'product';

        if (array_key_exists('measure_unit', $productData)) {
            $productData['measure_unit'] = $this->normalizeMeasureUnitForProduct($productData['measure_unit']);
        }

        if (isset($productData['article_group_id']) && !isset($productData['article_group'])) {
            $productData['article_group'] = $productData['article_group_id'];
        }

        if (isset($productData['sub_article_group_id']) && !isset($productData['sub_article'])) {
            $productData['sub_article'] = $productData['sub_article_group_id'];
        }

        $allowedFields = [
            'article_no',
            'sku',
            'ean',
            'brand_id',
            'article_group',
            'sub_article',
            'product',
            'model',
            'category',
            'color',
            'measure_unit',
            'price_unit',
            'package_unit',
            'vat_percent',
            'short_description',
            'status',
        ];

        $updateData = [];

        foreach ($allowedFields as $field) {
            if (!array_key_exists($field, $productData)) {
                continue;
            }

            if ($productData[$field] === null || $productData[$field] === '') {
                continue;
            }

            if ($field === 'vat_percent') {
                $updateData[$field] = $this->toDecimal($productData[$field]);
            } else {
                $updateData[$field] = $productData[$field];
            }
        }

        if (!empty($updateData)) {
            $product->update($updateData);
        }
    }

    private function upsertDistributorPrice(
        SupplierConnection $connection,
        Distributor $distributor,
        Product $product,
        array $data
    ): void {
        $articleNo = $data['article_no']
            ?? $product->sku
            ?? null;

        if (!$articleNo) {
            return;
        }

        $unit = $data['measure_unit'] ?? null;

        DistributorPrice::updateOrCreate(
            [
                'distributor_id' => $distributor->id,
                'product_id' => $product->id,
                'article_no' => $articleNo,
            ],
            [
                'discount_group_id' => $data['discount_group_id'] ?? null,
                'discount_price' => $this->toDecimal($data['discount_price'] ?? null),
                'discount_percent' => $this->toDecimal($data['discount_percent'] ?? null),
                'price' => $this->toDecimal($data['price'] ?? null),
                'purchase_price' => $this->toDecimal($data['purchase_price'] ?? $data['price'] ?? null),
                'price_date' => $data['price_date'] ?? now(),
                'availability' => $data['availability'] ?? null,
                'status' => $data['status'] ?? 1,
                'creator_id' => $data['creator_id'] ?? auth()->id(),
                'notice' => $data['notice']
                    ?? trim('Importiert über ' . $connection->name . ($unit ? ' | Einheit: ' . $unit : '')),
            ]
        );
    }

    private function normalizeMeasureUnitForProduct(mixed $value): ?int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function toDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);
        $value = str_replace(['€', 'EUR', ' '], '', $value);

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? round((float) $value, 2) : null;
    }
}