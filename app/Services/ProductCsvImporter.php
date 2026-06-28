<?php

namespace App\Services;

use App\Models\DistributorPrice;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ProductCsvImporter
{
    public function preview(
        string $filePath,
        string $delimiter = ',',
        bool $hasHeader = true,
        int $limit = 20
    ): array {
        $rows = [];
        $headers = [];
        $rowNumber = 0;

        if (!file_exists($filePath)) {
            return [
                'headers' => [],
                'rows' => [],
                'meta' => [
                    'error' => 'CSV file not found.',
                    'delimiter' => $delimiter,
                    'has_header' => $hasHeader,
                ],
            ];
        }

        $handle = fopen($filePath, 'r');

        if (!$handle) {
            return [
                'headers' => [],
                'rows' => [],
                'meta' => [
                    'error' => 'Could not open CSV file.',
                    'delimiter' => $delimiter,
                    'has_header' => $hasHeader,
                ],
            ];
        }

        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;

                if ($rowNumber === 1 && $hasHeader) {
                    $headers = $this->normalizeHeaders($row);
                    continue;
                }

                if (!$hasHeader && empty($headers)) {
                    $headers = [
                        'artikelnummer',
                        'herstellernummer',
                        'bezeichnung',
                        'beschreibung',
                        'bild',
                        'source_url',
                        'svg_content',
                        'ean',
                        'price',
                        'purchase_price',
                    ];
                }

                $mapped = $this->mapRow($headers, $row);

                $imageUrl = $this->detectImageCandidate($mapped);

                $rows[] = [
                    'row_number' => $rowNumber,
                    'raw' => $mapped,
                    'article_no' => $this->clean($mapped['artikelnummer'] ?? null),
                    'manufacturer_no' => $this->clean($mapped['herstellernummer'] ?? null),
                    'product' => $this->clean($mapped['bezeichnung'] ?? null),
                    'description' => $this->clean($mapped['beschreibung'] ?? null),
                    'image_raw' => $this->clean($mapped['bild'] ?? null),
                    'source_url' => $this->clean($mapped['source_url'] ?? null),
                    'detected_image_url' => $imageUrl,
                    'image_preview_url' => $imageUrl,
                    'svg_loaded' => !empty($this->clean($mapped['svg_content'] ?? null)),
                    'price' => $this->decimalOrNull($mapped['price'] ?? $mapped['einzelpreis'] ?? $mapped['retail_price'] ?? null),
                ];

                if (count($rows) >= $limit) {
                    break;
                }
            }
        } finally {
            fclose($handle);
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
            'meta' => [
                'delimiter' => $delimiter,
                'has_header' => $hasHeader,
                'preview_count' => count($rows),
            ],
        ];
    }

    public function import(
        string $filePath,
        string $delimiter = ',',
        ?int $brandId = null,
        ?int $distributorId = null,
        bool $hasHeader = true
    ): array {
        $summary = [
            'created' => 0,
            'updated' => 0,
            'images_downloaded' => 0,
            'prices_created' => 0,
            'failed_rows' => 0,
            'messages' => [],
        ];

        $productIds = [];

        if (!file_exists($filePath)) {
            $summary['messages'][] = 'CSV file not found.';
            return [
                'summary' => $summary,
                'product_ids' => [],
            ];
        }

        $handle = fopen($filePath, 'r');

        if (!$handle) {
            $summary['messages'][] = 'Could not open CSV file.';
            return [
                'summary' => $summary,
                'product_ids' => [],
            ];
        }

        $headers = [];
        $rowNumber = 0;

        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;

                if ($rowNumber === 1 && $hasHeader) {
                    $headers = $this->normalizeHeaders($row);
                    continue;
                }

                if (!$hasHeader && empty($headers)) {
                    $headers = [
                        'artikelnummer',
                        'herstellernummer',
                        'bezeichnung',
                        'beschreibung',
                        'bild',
                        'source_url',
                        'svg_content',
                        'ean',
                        'retail_price',
                        'discount_price',
                        'purchase_price',
                        'price',
                        'availability',
                        'discount_percent',
                        'category',
                        'color',
                        'price_unit',
                        'package_unit',
                        'type',
                        'shape',
                    ];
                }

                try {
                    $mapped = $this->mapRow($headers, $row);

                    $result = $this->importRow(
                        row: $mapped,
                        rowNumber: $rowNumber,
                        brandId: $brandId,
                        distributorId: $distributorId
                    );

                    if (!empty($result['product_id'])) {
                        $productIds[] = $result['product_id'];
                    }

                    if (!empty($result['created'])) {
                        $summary['created']++;
                    }

                    if (!empty($result['updated'])) {
                        $summary['updated']++;
                    }

                    if (!empty($result['image_downloaded'])) {
                        $summary['images_downloaded']++;
                    }

                    if (!empty($result['price_created'])) {
                        $summary['prices_created']++;
                    }
                } catch (\Throwable $e) {
                    $summary['failed_rows']++;
                    $summary['messages'][] = "Row {$rowNumber}: " . $e->getMessage();
                    report($e);
                }
            }
        } finally {
            fclose($handle);
        }

        return [
            'summary' => $summary,
            'product_ids' => array_values(array_unique($productIds)),
        ];
    }

    protected function normalizeHeaders(array $headers): array
    {
        return array_map(function ($value) {
            $value = trim(mb_strtolower((string) $value));

            $map = [
                'positionsnummer des gh' => 'position_no',
                'artikelnummer' => 'artikelnummer',
                'article_no' => 'artikelnummer',

                'herstellernummer' => 'herstellernummer',
                'manufacturer_no' => 'herstellernummer',

                'menge' => 'menge',

                'bezeichnung' => 'bezeichnung',
                'product' => 'bezeichnung',

                'einzelpreis' => 'price',
                'mengeneinheit' => 'package_unit',
                'preiseinheit' => 'price_unit',
                'netto-positionswert' => 'net_total',

                'rabatt' => 'discount_price',
                'beschreibung' => 'beschreibung',
                'beschreibung / description' => 'beschreibung',
                'beschreibung/description' => 'beschreibung',
                'description' => 'beschreibung',

                'quelle / source url' => 'source_url',
                'quelle' => 'source_url',
                'source url' => 'source_url',

                'bild' => 'bild',
                'image' => 'bild',
                'image_url' => 'bild',

                'svg' => 'svg_content',
                'svg_content' => 'svg_content',

                'ean' => 'ean',
                'retail_price' => 'retail_price',
                'discount_price' => 'discount_price',
                'purchase_price' => 'purchase_price',
                'discount_percent' => 'discount_percent',
                'price' => 'price',
                'availability' => 'availability',
                'type' => 'type',
                'shape' => 'shape',
                'category' => 'category',
                'color' => 'color',
                'price_unit' => 'price_unit',
                'package_unit' => 'package_unit',
            ];

            return $map[$value] ?? Str::snake($value);
        }, $headers);
    }

    protected function mapRow(array $headers, array $row): array
    {
        $mapped = [];

        foreach ($headers as $index => $header) {
            if ($header !== '') {
                $mapped[$header] = $row[$index] ?? null;
            }
        }

        return $mapped;
    }

   protected function importRow(array $row, int $rowNumber, ?int $brandId, ?int $distributorId): array
    {
        $articleNo = $this->clean($row['artikelnummer'] ?? null);
        $manufacturerNo = $this->clean($row['herstellernummer'] ?? null);
        $productName = $this->clean($row['bezeichnung'] ?? null);
        $description = $this->clean($row['beschreibung'] ?? null);
        $ean = $this->clean($row['ean'] ?? null);

        if ($articleNo === null && $productName === null) {
            return [
                'product_id' => null,
                'created' => false,
                'updated' => false,
                'image_downloaded' => false,
                'price_created' => false,
            ];
        }

        $existing = null;

        if ($articleNo) {
            $existing = Product::where('article_no', $articleNo)->first();
        }

        if (!$existing && $manufacturerNo && $productName) {
            $existing = Product::where('model', $manufacturerNo)
                ->where('product', $productName)
                ->first();
        }

        // ONLY columns that really exist in your products migration
        $payload = [
            'brand_id' => $brandId,
            'ean' => $ean,
            'product' => $productName ?: 'Produkt ' . $rowNumber,
            'model' => $manufacturerNo,
            'category' => $this->clean($row['category'] ?? 'Produkt') ?: 'Produkt',
            'roof_type' => 'none',
            'color' => $this->clean($row['color'] ?? null),
            'price_unit' => $this->clean($row['price_unit'] ?? null),
            'package_unit' => $this->clean($row['package_unit'] ?? null),
            'short_description' => $description,
            'status' => 'Published',
        ];

        if ($existing) {
            $existing->fill($payload);

            if (!$existing->article_no && $articleNo) {
                $existing->article_no = $articleNo;
            }

            $existing->save();
            $product = $existing;
            $wasCreated = false;
        } else {
            $product = Product::create(array_merge($payload, [
                'article_no' => $articleNo,
            ]));
            $wasCreated = true;
        }

        $imageDownloaded = false;
        $imageUrl = $this->detectImageCandidate($row);

        if ($imageUrl) {
            $fileName = $this->downloadImage($imageUrl, $product);

            if ($fileName) {
                // DO NOT save image_path because your products table does not have image_path
                ProductImage::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'image' => $fileName,
                    ],
                    [
                        'name' => $product->product ?: $product->article_no,
                    ]
                );

                $imageDownloaded = true;
            }
        }

        $priceCreated = false;

        if ($distributorId) {
            DistributorPrice::updateOrCreate(
                [
                    'distributor_id' => $distributorId,
                    'product_id' => $product->id,
                ],
                [
                    'discount_group_id' => null,
                    'article_no' => $articleNo,
                    'discount_price' => $this->decimalOrNull($row['discount_price'] ?? null),
                    'discount_percent' => $this->decimalOrNull($row['discount_percent'] ?? null),
                    'price' => $this->decimalOrNull($row['price'] ?? $row['retail_price'] ?? null),
                    'purchase_price' => $this->decimalOrNull($row['purchase_price'] ?? null),
                    'price_date' => now()->toDateString(),
                    'availability' => $this->clean($row['availability'] ?? null),
                    'status' => 'Published',
                ]
            );

            $priceCreated = true;
        }

        return [
            'product_id' => $product->id,
            'created' => $wasCreated,
            'updated' => !$wasCreated,
            'image_downloaded' => $imageDownloaded,
            'price_created' => $priceCreated,
        ];
    }
    protected function detectImageCandidate(array $row): ?string
    {
        $imageValue = $this->clean($row['bild'] ?? null);
        $sourceUrl = $this->clean($row['source_url'] ?? null);

        $imageUrl = $this->extractImageUrl($imageValue);

        if ($imageUrl) {
            return $imageUrl;
        }

        if ($sourceUrl && filter_var($sourceUrl, FILTER_VALIDATE_URL)) {
            $extension = strtolower(pathinfo(parse_url($sourceUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
                return $sourceUrl;
            }
        }

        return null;
    }

    protected function extractImageUrl(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);

        if (preg_match('/@?=IMAGE\("([^"]+)"/i', $value, $matches)) {
            return $matches[1];
        }

        if (preg_match('/https?:\/\/[^\s",]+/i', $value, $matches)) {
            return $matches[0];
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        return null;
    }

    protected function downloadImage(string $url, Product $product): ?string
    {
        try {
            $directory = public_path('images/products');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $path = parse_url($url, PHP_URL_PATH);
            $extension = strtolower(pathinfo($path ?: '', PATHINFO_EXTENSION));
            $extension = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']) ? $extension : 'jpg';

            $baseName = Str::slug($product->product ?: $product->article_no ?: 'product');
            $fileName = $baseName . '-' . $product->id . '.' . $extension;

            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 Product CSV Importer',
                ])
                ->get($url);

            if (!$response->successful()) {
                return null;
            }

            File::put($directory . '/' . $fileName, $response->body());

            return $fileName;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    protected function clean($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function decimalOrNull($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = str_replace(['€', ' '], '', (string) $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : null;
    }

    protected function integerOrNull($value): ?int
    {
        $number = $this->decimalOrNull($value);

        return $number !== null ? (int) round($number) : null;
    }

    protected function enumOrNull($value, array $allowed): ?string
    {
        $value = $this->clean($value);

        if (!$value) {
            return null;
        }

        return in_array($value, $allowed, true) ? $value : null;
    }
}