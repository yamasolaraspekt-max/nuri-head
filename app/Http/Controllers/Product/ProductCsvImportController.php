<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\Brand;
use App\Models\Distributor;
use App\Models\Product;
use App\Services\ProductCsvImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductCsvImportController extends Controller
{
    public function index()
    {
        $importedIds = session('csv_import.imported_product_ids', []);
        $preview = session('csv_import.preview', []);
        $config = session('csv_import.config', []);

        $products = Product::with(['brand', 'images', 'distributorPrices'])
            ->when(
                !empty($importedIds),
                fn ($q) => $q->whereIn('id', $importedIds),
                fn ($q) => $q->whereRaw('1 = 0')
            )
            ->latest('id')
            ->get();

        $brands = Brand::orderBy('name')->get();
        $distributors = Distributor::orderBy('name')->get();

        return view('admin.product.csv-import', [
            'products' => $products,
            'brands' => $brands,
            'distributors' => $distributors,
            'previewRows' => $preview['rows'] ?? [],
            'previewHeaders' => $preview['headers'] ?? [],
            'previewMeta' => $preview['meta'] ?? [],
            'config' => $config ?? [],
        ]);
    }

    public function preview(Request $request, ProductCsvImporter $importer)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
            'delimiter' => ['nullable', 'string', 'max:1'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'distributor_id' => ['nullable', 'integer', 'exists:distributors,id'],
            'has_header' => ['nullable', 'in:0,1'],
        ]);

        $delimiter = $request->input('delimiter', ',');
        $hasHeader = (bool) $request->input('has_header', true);

        $preview = $importer->preview(
            filePath: $request->file('file')->getRealPath(),
            delimiter: $delimiter,
            hasHeader: $hasHeader
        );

        $storedPath = $request->file('file')->store('temp/csv-imports');

        session([
            'csv_import.preview' => $preview,
            'csv_import.file_path' => storage_path('app/' . $storedPath),
            'csv_import.config' => [
                'delimiter' => $delimiter,
                'has_header' => $hasHeader,
                'brand_id' => $request->filled('brand_id') ? (int) $request->brand_id : null,
                'distributor_id' => $request->filled('distributor_id') ? (int) $request->distributor_id : null,
                'original_name' => $request->file('file')->getClientOriginalName(),
            ],
            'csv_import.imported_product_ids' => [],
        ]);

        return redirect()
            ->route('admin.products.csv-import.index')
            ->with('success', 'CSV file loaded successfully. Please review the preview below before importing.');
    }

  public function confirm(Request $request, ProductCsvImporter $importer)
    {
        $filePath = session('csv_import.file_path');
        $config = session('csv_import.config', []);

        if (!$filePath || !file_exists($filePath)) {
            return redirect()
                ->route('admin.products.csv-import.index')
                ->withErrors(['file' => 'Temporary CSV file not found. Please upload the file again.']);
        }

        $result = $importer->import(
            filePath: $filePath,
            delimiter: $config['delimiter'] ?? ',',
            brandId: $config['brand_id'] ?? null,
            distributorId: $config['distributor_id'] ?? null,
            hasHeader: (bool) ($config['has_header'] ?? true),
        );

        session([
            'csv_import.imported_product_ids' => $result['product_ids'],
        ]);

        $summary = $result['summary'];
        $processed = (int)($summary['created'] ?? 0) + (int)($summary['updated'] ?? 0);

        if ($processed === 0) {
            return redirect()
                ->route('admin.products.csv-import.index')
                ->with('import_summary', $summary)
                ->withErrors([
                    'file' => 'CSV was processed, but no records were saved. Check the import messages below.',
                ]);
        }

        return redirect()
            ->route('admin.products.csv-import.index')
            ->with('success', 'CSV import completed successfully.')
            ->with('import_summary', $summary);
    }
    public function resetPreview()
    {
        $filePath = session('csv_import.file_path');

        if ($filePath && file_exists($filePath)) {
            @unlink($filePath);
        }

        session()->forget([
            'csv_import.preview',
            'csv_import.file_path',
            'csv_import.config',
            'csv_import.imported_product_ids',
        ]);

        return redirect()
            ->route('admin.products.csv-import.index')
            ->with('success', 'Preview data has been cleared.');
    }
}