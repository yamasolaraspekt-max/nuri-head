<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class ProductImageCsvImportController extends Controller
{
    public function index()
    {
        return view('admin.product.image.csv-import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
            'replace_existing' => ['nullable', 'boolean'],
        ]);

        $replaceExisting = $request->boolean('replace_existing');
        $file = $request->file('csv_file');

        $rows = $this->readCsv($file->getRealPath());

        $stats = [
            'total' => count($rows),
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
        ];

        $results = [];

        foreach ($rows as $index => $row) {
            $line = $index + 2;

            $articleNo = trim((string) (
                $row['Artikelnummer']
                ?? $row['article_no']
                ?? $row['Article No']
                ?? $row['article_number']
                ?? ''
            ));

            $productName = trim((string) (
                $row['Produktname']
                ?? $row['product']
                ?? $row['Productname']
                ?? $row['Product Name']
                ?? $row['name']
                ?? ''
            ));

            $imageUrl = $this->extractImageUrl($row);

            if (!$imageUrl) {
                $stats['skipped']++;

                $results[] = [
                    'line' => $line,
                    'status' => 'skipped',
                    'message' => 'Keine direkte Bild-URL gefunden.',
                    'article_no' => $articleNo,
                    'product' => $productName,
                    'url' => null,
                ];

                continue;
            }

            if (!$this->isDirectImageUrl($imageUrl)) {
                $stats['skipped']++;

                $results[] = [
                    'line' => $line,
                    'status' => 'skipped',
                    'message' => 'URL ist keine direkte Bild-URL.',
                    'article_no' => $articleNo,
                    'product' => $productName,
                    'url' => $imageUrl,
                ];

                continue;
            }

            $product = $this->findProduct($articleNo, $productName);

            if (!$product) {
                $stats['failed']++;

                $results[] = [
                    'line' => $line,
                    'status' => 'failed',
                    'message' => 'Produkt wurde nicht gefunden.',
                    'article_no' => $articleNo,
                    'product' => $productName,
                    'url' => $imageUrl,
                ];

                continue;
            }

            if (!$replaceExisting && $product->images()->exists()) {
                $stats['skipped']++;

                $results[] = [
                    'line' => $line,
                    'status' => 'skipped',
                    'message' => 'Produkt hat bereits ein Bild.',
                    'product_id' => $product->id,
                    'article_no' => $product->article_no,
                    'product' => $product->product,
                    'url' => $imageUrl,
                ];

                continue;
            }

            try {
                $downloadedFileName = $this->downloadImage($imageUrl, $product);

                if ($replaceExisting) {
                    $oldImages = $product->images()->get();

                    foreach ($oldImages as $oldImage) {
                        $oldPath = public_path('images/products/' . ltrim($oldImage->image, '/'));

                        if (File::exists($oldPath)) {
                            File::delete($oldPath);
                        }

                        $oldImage->delete();
                    }

                    ProductImage::create([
                        'product_id' => $product->id,
                        'name' => $product->product,
                        'image' => $downloadedFileName,
                    ]);

                    $stats['updated']++;
                    $status = 'updated';
                    $message = 'Bild ersetzt.';
                } else {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'name' => $product->product,
                        'image' => $downloadedFileName,
                    ]);

                    $stats['created']++;
                    $status = 'created';
                    $message = 'Bild importiert.';
                }

                $results[] = [
                    'line' => $line,
                    'status' => $status,
                    'message' => $message,
                    'product_id' => $product->id,
                    'article_no' => $product->article_no,
                    'product' => $product->product,
                    'image' => $downloadedFileName,
                    'url' => $imageUrl,
                ];
            } catch (Throwable $e) {
                $stats['failed']++;

                $results[] = [
                    'line' => $line,
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                    'product_id' => $product->id,
                    'article_no' => $product->article_no,
                    'product' => $product->product,
                    'url' => $imageUrl,
                ];
            }
        }

        return back()->with([
            'import_stats' => $stats,
            'import_results' => $results,
        ]);
    }
    private function readCsv(string $path): array
    {
        $content = file_get_contents($path);

        if ($content === false) {
            return [];
        }

        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        $firstLine = strtok($content, "\n") ?: '';
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';

        $handle = fopen($path, 'r');

        if (!$handle) {
            return [];
        }

        $headers = fgetcsv($handle, 0, $delimiter);

        if (!$headers) {
            fclose($handle);
            return [];
        }

        $headers = array_map(function ($header) {
            return trim((string) preg_replace('/^\xEF\xBB\xBF/', '', $header));
        }, $headers);

        $rows = [];

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count(array_filter($data, fn ($value) => trim((string) $value) !== '')) === 0) {
                continue;
            }

            $row = [];

            foreach ($headers as $key => $header) {
                $row[$header] = trim((string) ($data[$key] ?? ''));
            }

            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function extractImageUrl(array $row): ?string
    {
        $possibleColumns = [
            'Direkte_Bild_URL',
            'Bild_URL_Klickbar',
            'image_url',
            'Image URL',
            'Bild URL',
            'bild_url',
            'url',
        ];

        foreach ($possibleColumns as $column) {
            $value = trim((string) ($row[$column] ?? ''));

            if ($value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function isDirectImageUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        if (str_contains($url, 'google.com/search')) {
            return false;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '';

        if (preg_match('/\.(jpg|jpeg|png|webp|gif|avif)$/i', $path)) {
            return true;
        }

        // Some suppliers use image URLs without extension.
        return str_contains($url, 'assetstore')
            || str_contains($url, 'image')
            || str_contains($url, 'media')
            || str_contains($url, 'wp-content/uploads');
    }

    private function findProduct(?string $articleNo, ?string $productName): ?Product
    {
        $articleNo = trim((string) $articleNo);
        $productName = trim((string) $productName);

        if ($articleNo !== '') {
            $product = Product::query()
                ->where('article_no', $articleNo)
                ->orWhere('sku', $articleNo)
                ->orWhere('ean', $articleNo)
                ->first();

            if ($product) {
                return $product;
            }
        }

        if ($productName !== '') {
            $product = Product::query()
                ->where('product', $productName)
                ->first();

            if ($product) {
                return $product;
            }

            return Product::query()
                ->where('product', 'LIKE', '%' . $this->cleanSearchValue($productName) . '%')
                ->first();
        }

        return null;
    }

    private function cleanSearchValue(string $value): string
    {
        $value = str_replace('(Kopie)', '', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }

    private function downloadImage(string $url, Product $product): string
    {
        $response = Http::timeout(25)
            ->retry(2, 500)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 ProductImageImporter/1.0',
                'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
            ])
            ->get($url);

        if (!$response->successful()) {
            throw new \Exception('Bild konnte nicht heruntergeladen werden. HTTP: ' . $response->status());
        }

        $contentType = strtolower((string) $response->header('Content-Type'));

        $extension = $this->extensionFromContentType($contentType)
            ?: $this->extensionFromUrl($url)
            ?: 'jpg';

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];

        if (!in_array($extension, $allowedExtensions, true)) {
            throw new \Exception('Nicht unterstütztes Bildformat: ' . $extension);
        }

        $directory = public_path('images/products');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $baseName = Str::slug(
            ($product->article_no ?: 'product-' . $product->id) . '-' . Str::limit($product->product, 60, '')
        );

        $fileName = $baseName . '-' . time() . '-' . Str::random(6) . '.' . $extension;

        $fullPath = $directory . DIRECTORY_SEPARATOR . $fileName;

        File::put($fullPath, $response->body());

        if (!File::exists($fullPath) || File::size($fullPath) < 500) {
            throw new \Exception('Heruntergeladene Datei ist ungültig oder zu klein.');
        }

        return $fileName;
    }

    private function extensionFromContentType(?string $contentType): ?string
    {
        return match (true) {
            str_contains($contentType, 'image/jpeg') => 'jpg',
            str_contains($contentType, 'image/jpg') => 'jpg',
            str_contains($contentType, 'image/png') => 'png',
            str_contains($contentType, 'image/webp') => 'webp',
            str_contains($contentType, 'image/gif') => 'gif',
            str_contains($contentType, 'image/avif') => 'avif',
            default => null,
        };
    }

    private function extensionFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return $extension ?: null;
    }
}