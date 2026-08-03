<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\ArticleGroup;
use App\Models\Brand;
use App\Models\Distributor;
use App\Models\DistributorPrice;
use App\Models\Measure;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubArticleGroup as SubArticle;
use App\Services\Product\Identity\IdentityMatch;
use App\Services\Product\Identity\ProductIdentity;
use App\Services\Product\Identity\ProductIdentityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductImportController extends Controller
{
    public function index()
    {
        return view('admin.product.import', [
            'distributors'  => Distributor::orderBy('name')->get(),
            'brands'        => Brand::orderBy('name')->get(),
            'articleGroups' => ArticleGroup::orderBy('article_group')->get(),
            'subArticles'   => SubArticle::orderBy('id')->get(),
            'measures'      => Measure::orderBy('measure')->get(),
        ]);
    }

    public function preview(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
            'has_header' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed.',
            ], 422);
        }

        $rows = $this->parseCsvFile(
            $request->file('csv_file')->getRealPath(),
            (bool) $request->boolean('has_header')
        );

        return response()->json([
            'ok' => true,
            'rows' => array_slice($rows, 0, 50),
            'count' => count($rows),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
            'default_brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'default_distributor_id' => ['nullable', 'integer', 'exists:distributors,id'],
            'default_article_group_id' => ['nullable', 'integer', 'exists:article_groups,id'],
            'default_sub_article_id' => ['nullable', 'integer', 'exists:sub_articles,id'],
            'default_measure_unit_id' => ['nullable', 'integer', 'exists:measures,id'],
            'default_availability' => ['nullable', 'string', 'max:255'],
            'default_status' => ['nullable', 'string', 'max:255'],
            'price_target' => ['required', 'in:price,purchase_price,discount_price,retail_price'],
            'has_header' => ['nullable', 'boolean'],
            'skip_existing_images' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed.',
            ], 422);
        }

        $rows = $this->parseCsvFile(
            $request->file('csv_file')->getRealPath(),
            (bool) $request->boolean('has_header')
        );

        if (empty($rows)) {
            return response()->json([
                'ok' => false,
                'message' => 'No valid rows found in CSV.',
            ], 422);
        }

        $created = 0;
        $updated = 0;
        $pricesSaved = 0;
        $imagesSaved = 0;
        $errors = [];

        $defaultBrandId = $request->input('default_brand_id');
        $defaultDistributorId = $request->input('default_distributor_id');
        $defaultArticleGroupId = $request->input('default_article_group_id');
        $defaultSubArticleId = $request->input('default_sub_article_id');
        $defaultMeasureUnitId = $request->input('default_measure_unit_id');
        $defaultAvailability = $request->input('default_availability');
        $defaultStatus = $request->input('default_status', 'Published');
        $priceTarget = $request->input('price_target', 'purchase_price');
        $skipExistingImages = (bool) $request->boolean('skip_existing_images');

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                try {
                    $articleNo = trim((string) ($row['article_no'] ?? ''));
                    $productName = trim((string) ($row['product'] ?? ''));
                    $priceValue = $this->normalizeMoney($row['price'] ?? null);
                    $imageUrl = trim((string) ($row['image_url'] ?? ''));

                    if ($articleNo === '' && $productName === '') {
                        continue;
                    }

                    if (config('produkt.identitaet.aktiv')) {
                        // AUF-P1-S4 §8 Zeile 5: resolve() — der Namensvergleich
                        // (article_no -> Produktname) ist ersatzlos gestrichen.
                        $dienst = app(ProductIdentityService::class);

                        $identitaet = new ProductIdentity(
                            manufacturerArticleNo: $articleNo !== '' ? $articleNo : null,
                            brandId: $defaultBrandId ? (int) $defaultBrandId : null,
                            distributorId: $defaultDistributorId ? (int) $defaultDistributorId : null,
                            name: $productName !== '' ? $productName : null,
                            channel: 'import:produkt-csv',
                        );

                        $match = $dienst->resolve($identitaet);

                        if ($match->ergebnis === IdentityMatch::KONFLIKT) {
                            throw new \RuntimeException('Identitätskonflikt — Zeile übersprungen: ' . $match->begruendung);
                        }

                        if ($match->ergebnis === IdentityMatch::VORSCHLAG) {
                            $dienst->vorschlagSpeichern($identitaet, $match);

                            throw new \RuntimeException(
                                'Identität nicht eindeutig — Vorschlag zur Prüfung angelegt (Artikel #'
                                . $match->product->id . '), Zeile nicht importiert.'
                            );
                        }

                        $product = $match->product;
                        $isNew = false;

                        if (! $product) {
                            $product = $dienst->createFrom($identitaet, [
                                'product' => $productName ?: 'Unbenanntes Produkt',
                                'brand_id' => $defaultBrandId ?: null,
                                'article_group' => $defaultArticleGroupId ?: null,
                                'sub_article' => $defaultSubArticleId ?: null,
                                'measure_unit' => $defaultMeasureUnitId ?: null,
                                'status' => 'active',
                                'category' => 'Produkt',
                                'roof_type' => 'none',
                            ]);
                            $isNew = true;
                        } else {
                            $product->product = $productName ?: ($product->product ?: 'Unbenanntes Produkt');
                            $product->brand_id = $defaultBrandId ?: $product->brand_id;
                            $product->article_group = $defaultArticleGroupId ?: $product->article_group;
                            $product->sub_article = $defaultSubArticleId ?: $product->sub_article;
                            $product->measure_unit = $defaultMeasureUnitId ?: $product->measure_unit;
                            $product->status = $product->status ?: 'active';
                            $product->category = $product->category ?: 'Produkt';
                            $product->roof_type = $product->roof_type ?: 'none';
                        }
                    } else {
                        // Alt-Verhalten, byte-gleich (Kante 15).
                        $product = null;

                        if ($articleNo !== '') {
                            $product = Product::where('article_no', $articleNo)->first();
                        }

                        if (! $product && $productName !== '') {
                            $product = Product::where('product', $productName)->first();
                        }

                        $isNew = false;

                        if (! $product) {
                            $product = app(ProductIdentityService::class)->newLegacy();
                            $isNew = true;
                        }

                        $product->article_no = $articleNo ?: $product->article_no;
                        $product->product = $productName ?: ($product->product ?: 'Unbenanntes Produkt');
                        $product->brand_id = $defaultBrandId ?: $product->brand_id;
                        $product->article_group = $defaultArticleGroupId ?: $product->article_group;
                        $product->sub_article = $defaultSubArticleId ?: $product->sub_article;
                        $product->measure_unit = $defaultMeasureUnitId ?: $product->measure_unit;
                        $product->status = $product->status ?: 'active';
                        $product->category = $product->category ?: 'Produkt';
                        $product->roof_type = $product->roof_type ?: 'none';
                    }

                    if ($priceValue !== null && $priceTarget === 'retail_price') {
                        $product->retail_price = $priceValue;
                    }
                    if ($priceValue !== null && $priceTarget === 'purchase_price' && empty($defaultDistributorId)) {
                        $product->purchase_price = $priceValue;
                    }
                    if ($priceValue !== null && $priceTarget === 'discount_price' && empty($defaultDistributorId)) {
                        $product->discount_price = $priceValue;
                    }

                    $product->save();

                    if ($isNew) {
                        $created++;
                    } else {
                        $updated++;
                    }

                    if ($defaultDistributorId && $priceValue !== null) {
                        $priceData = [
                            'article_no' => $articleNo ?: $product->article_no,
                            'availability' => $defaultAvailability,
                            'status' => $defaultStatus,
                            'price_date' => now()->toDateString(),
                        ];

                        if ($priceTarget === 'price' || $priceTarget === 'retail_price') {
                            $priceData['price'] = $priceValue;
                        } elseif ($priceTarget === 'purchase_price') {
                            $priceData['purchase_price'] = $priceValue;
                        } elseif ($priceTarget === 'discount_price') {
                            $priceData['discount_price'] = $priceValue;
                        }

                        DistributorPrice::updateOrCreate(
                            [
                                'distributor_id' => $defaultDistributorId,
                                'product_id' => $product->id,
                            ],
                            $priceData
                        );

                        $pricesSaved++;
                    }

                    if ($defaultDistributorId) {
                        DB::table('distributor_product')->updateOrInsert(
                            [
                                'product_id' => $product->id,
                                'distributor_id' => $defaultDistributorId,
                            ],
                            [
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }

                    if ($imageUrl !== '' && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        $hasImageAlready = $product->images()->exists();

                        if (!($skipExistingImages && $hasImageAlready)) {
                            $savedFile = $this->downloadAndStoreProductImage($imageUrl, $product);

                            if ($savedFile) {
                                ProductImage::firstOrCreate(
                                    [
                                        'product_id' => $product->id,
                                        'image' => $savedFile,
                                    ],
                                    [
                                        'name' => $product->product,
                                    ]
                                );

                                $imagesSaved++;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $errors[] = [
                        'row' => $index + 1,
                        'message' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'ok' => true,
                'message' => 'Import completed.',
                'summary' => [
                    'created_products' => $created,
                    'updated_products' => $updated,
                    'prices_saved' => $pricesSaved,
                    'images_saved' => $imagesSaved,
                    'errors_count' => count($errors),
                    'errors' => $errors,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'ok' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function parseCsvFile(string $path, bool $hasHeader = false): array
    {
        $rows = [];

        $content = file_get_contents($path);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $tempPath = storage_path('app/temp_import_' . Str::random(10) . '.csv');
        file_put_contents($tempPath, $content);

        $delimiter = $this->detectDelimiter($tempPath);
        $handle = fopen($tempPath, 'r');

        if (! $handle) {
            throw new \RuntimeException('CSV file could not be opened.');
        }

        $line = 0;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $line++;

            if ($line === 1 && $hasHeader) {
                continue;
            }

            $data = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $data);

            if (count(array_filter($data, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }

            $rows[] = [
                'article_no' => $data[0] ?? null,
                'supplier_no' => $data[1] ?? null,
                'product' => $data[2] ?? null,
                'qty' => $data[3] ?? null,
                'price' => $data[4] ?? null,
                'total' => $data[5] ?? null,
                'image_url' => $data[6] ?? null,
            ];
        }

        fclose($handle);
        @unlink($tempPath);

        return $rows;
    }

    protected function detectDelimiter(string $path): string
    {
        $delimiters = [',', ';', "\t", '|'];

        $firstLine = '';
        $handle = fopen($path, 'r');
        if ($handle) {
            $firstLine = fgets($handle) ?: '';
            fclose($handle);
        }

        $bestDelimiter = ',';
        $bestCount = 0;

        foreach ($delimiters as $delimiter) {
            $count = count(str_getcsv($firstLine, $delimiter));
            if ($count > $bestCount) {
                $bestCount = $count;
                $bestDelimiter = $delimiter;
            }
        }

        return $bestDelimiter;
    }

    protected function normalizeMoney($value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d+\.\d+$/', $value)) {
            return (float) $value;
        }

        $value = str_replace(['€', 'EUR', ' '], '', $value);
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);
        $value = preg_replace('/[^0-9.\-]/', '', $value);

        return is_numeric($value) ? (float) $value : null;
    }

    protected function downloadAndStoreProductImage(string $url, Product $product): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 Laravel Product Importer',
                ])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $contentType = strtolower((string) $response->header('Content-Type'));

            if ($contentType !== '' && ! Str::contains($contentType, 'image')) {
                return null;
            }

            $extension = $this->resolveImageExtensionFromUrlOrContentType($url, $contentType);

            $baseName = $product->article_no ?: Str::slug($product->product ?: 'product');
            $fileName = Str::slug($baseName) . '-' . Str::random(8) . '.' . $extension;

            $directory = public_path('images/products');

            if (! File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            File::put($directory . '/' . $fileName, $response->body());

            return $fileName;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function resolveImageExtensionFromUrlOrContentType(string $url, string $contentType): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            return $ext === 'jpeg' ? 'jpg' : $ext;
        }

        if (Str::contains($contentType, 'png')) {
            return 'png';
        }
        if (Str::contains($contentType, 'webp')) {
            return 'webp';
        }
        if (Str::contains($contentType, 'gif')) {
            return 'gif';
        }

        return 'jpg';
    }
}