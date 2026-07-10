<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use App\Models\CustomerProductInfo;
use App\Models\CustomerProductInfoMedia;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ArticleGroup;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomerProductInfoController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,update')->only(['updateProduct']);
        $this->middleware('permission:Customer,delete')->only(['deleteProduct', 'mediaDelete']);
        $this->middleware('auth');
    }

    // =========================================================================
    // Serial Numbers (multi-serial support)
    // =========================================================================
    private function normalizeSerialNumbers($serialNumbers, int $count, string $productName = null): array
    {
        // accept: array | json-string | null
        if (is_string($serialNumbers)) {
            $decoded = json_decode($serialNumbers, true);
            $serialNumbers = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($serialNumbers)) $serialNumbers = [];

        // normalize to array of {pos, serial, product_name}
        $out = [];
        $pos = 1;

        foreach ($serialNumbers as $row) {
            $serial = null;

            if (is_array($row)) {
                // accept "serial" or "serial_number" from frontend
                $serial = trim((string)($row['serial'] ?? ($row['serial_number'] ?? '')));
                $p  = (int)($row['pos'] ?? $pos);
                $pn = (string)($row['product_name'] ?? ($productName ?? ''));
            } else {
                $serial = trim((string)$row);
                $p  = $pos;
                $pn = (string)($productName ?? '');
            }

            if ($serial !== '') {
                $out[] = [
                    'pos'          => $p,
                    'serial'       => $serial,
                    'product_name' => $pn,
                ];
            }

            $pos++;
            if (count($out) >= $count) break;
        }

        // ensure pos 1..count exists
        $final = [];
        for ($i = 1; $i <= $count; $i++) {
            $existing = collect($out)->firstWhere('pos', $i);
            $final[] = [
                'pos'          => $i,
                'serial'       => trim((string)($existing['serial'] ?? '')),
                'product_name' => (string)($existing['product_name'] ?? ($productName ?? '')),
            ];
        }

        return $final;
    }

    // =========================================================================
    // Blade loader
    // =========================================================================
    public function loadProductBlade($cid, $aid, $pid)
    {
        $products = DB::table('customer_product_infos')
            ->leftJoin('departments', 'departments.id', '=', 'customer_product_infos.department_id')
            ->leftJoin('products', 'products.id', '=', 'customer_product_infos.products')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->select(
                'customer_product_infos.*',
                'departments.department_name',
                DB::raw('COALESCE(products.product, customer_product_infos.product_name) as product_name'),
                DB::raw('COALESCE(brands.name, customer_product_infos.manufacturer) as manufacturer'),
                'products.short_description as master_description'
            )
            ->where('customer_product_infos.customer_id', $cid)
            ->where('customer_product_infos.alternative_id', $aid)
            ->where('customer_product_infos.product_id', $pid)
            ->orderBy('customer_product_infos.created_at', 'desc')
            ->get();

        $article_group = ArticleGroup::find($pid);
        $brands        = Brand::select('id', 'name')->get();
        $departments   = Department::select('id', 'department_name')->get();
        $employees     = Employee::where('status', 'Active')->get();

        return view('admin.new_leads.layouts.product', compact(
            'products',
            'brands',
            'departments',
            'cid',
            'aid',
            'pid',
            'article_group',
            'employees'
        ));
    }

    // =========================================================================
    // Store
    // =========================================================================
    public function storeProduct(Request $request)
    {
        $count       = max(1, (int) $request->product_count);
        $productName = (string) $request->product_name;

        // accept both keys (your JS uses serial_numbers_json)
        $incomingSerials = $request->serial_numbers ?? $request->serial_numbers_json ?? null;

        $serialNumbers = null;
        if ($count > 1) {
            $serialNumbers = $this->normalizeSerialNumbers($incomingSerials, $count, $productName);
        }

        // legacy single serial for preview/backward compat
        $firstSerial = (string) ($request->serial_number ?? '');
        if ($count > 1 && is_array($serialNumbers)) {
            $firstSerial = (string)($serialNumbers[0]['serial'] ?? '');
        }

        $data = CustomerProductInfo::create([
            'customer_id'           => $request->customer_id,
            'alternative_id'        => $request->alternative_id,
            'department_id'         => $request->department_id,
            'product_id'            => $request->product_id,
            'products'              => $request->products,

            'product_name'          => $productName,
            'product_count'         => $count,
            'manufacturer'          => $request->manufacturer,

            'serial_number'         => $firstSerial,
            'serial_numbers'        => $serialNumbers,

            'installation_date'     => $request->installation_date,
            'installation_location' => $request->installation_location,
            'purchased_from_us'     => (bool) $request->purchased_from_us,
            'purchase_date'         => $request->purchase_date,
            'invoice_reference'     => $request->invoice_reference,
            'warranty_until'        => $request->warranty_until,
            'guarantee_until'       => $request->guarantee_until,
            'image_available'       => (bool) $request->image_available,
            'installed_by'          => $request->installed_by,
            'notes'                 => $request->notes,
        ]);

        $data->load('department');

        $masterProduct = Product::find($request->products);
        $finalName     = $masterProduct ? $masterProduct->product : $data->product_name;

        $this->logProductActivity('created', $data, $request->customer_id, $request->alternative_id, $request->product_id, [
            'info'         => 'Kundenprodukt hinzugefügt',
            'product_name' => $finalName
        ]);

        return response()->json($this->productRowResponse($data, $finalName));
    }

    // =========================================================================
    // Show
    // =========================================================================
    public function showProduct($id)
    {
        $data = CustomerProductInfo::query()->where('id', $id)->firstOrFail();

        // keep compatibility with your JS (it reads serial_numbers_json sometimes)
        $data->serial_numbers_json = $data->serial_numbers ? json_encode($data->serial_numbers) : '';

        return response()->json($data);
    }

    // =========================================================================
    // Update
    // =========================================================================
    public function updateProduct(Request $request, $id)
    {
        $row = CustomerProductInfo::findOrFail($id);

        $count       = max(1, (int) $request->product_count);
        $productName = (string) $request->product_name;

        // Fill safe fields (avoid overriding booleans & json incorrectly)
        $row->fill($request->except([
            '_token',
            'purchased_from_us',
            'image_available',
            'employee_id',
            'serial_numbers',
            'serial_numbers_json',
        ]));

        $row->purchased_from_us = (bool) $request->purchased_from_us;
        $row->image_available   = (bool) $request->image_available;

        if ($request->has('products')) {
            $row->products = $request->products;
        }

        // accept both keys
        $incomingSerials = $request->serial_numbers ?? $request->serial_numbers_json ?? null;

        $serialNumbers = null;
        if ($count > 1) {
            $serialNumbers = $this->normalizeSerialNumbers($incomingSerials, $count, $productName);
        }

        $row->product_count   = $count;
        $row->serial_numbers  = $serialNumbers;

        // keep legacy single serial column
        if ($count > 1 && is_array($serialNumbers)) {
            $row->serial_number = (string)($serialNumbers[0]['serial'] ?? '');
        } else {
            $row->serial_number = (string) ($request->serial_number ?? '');
        }

        $row->save();
        $row->load('department');

        $masterProduct = Product::find($row->products);
        $finalName     = $masterProduct ? $masterProduct->product : $row->product_name;

        $this->logProductActivity('updated', $row, $row->customer_id, $row->alternative_id, $row->product_id, [
            'info'    => 'Kundenprodukt Details aktualisiert',
            'product' => $finalName
        ]);

        return response()->json($this->productRowResponse($row, $finalName));
    }

    // =========================================================================
    // Delete product
    // =========================================================================
    public function deleteProduct($id)
    {
        $product = CustomerProductInfo::find($id);

        if ($product) {
            $this->logProductActivity('deleted', $product, $product->customer_id, $product->alternative_id, $product->product_id, [
                'info' => 'Kundenprodukt entfernt',
                'name' => $product->product_name
            ]);

            // media rows will cascade-delete (FK). We also try to delete files.
            $media = CustomerProductInfoMedia::where('customer_product_info_id', $product->id)->get();
            foreach ($media as $m) {
                try {
                    Storage::disk($m->disk ?? 'public')->delete($m->path);
                } catch (\Throwable $e) {
                    // ignore disk errors
                }
            }

            $product->delete();
        }

        return response()->json(['success' => true]);
    }

    // =========================================================================
    // Products list endpoint
    // =========================================================================
    public function getProduct($pid)
    {
        $products = Product::with([
            'brand',
            'images' => function ($q) {
                $q->limit(1);
            }
        ])->where('article_group', $pid)->get();

        $articleGroup = ArticleGroup::find($pid);
        $brands       = Brand::select('id', 'name')->get();
        $departments  = Department::select('id', 'department_name')->get();

        return response()->json([
            'products'     => $products,
            'articleGroup' => $articleGroup,
            'brands'       => $brands,
            'departments'  => $departments,
        ], 200);
    }

    // =========================================================================
    // Gallery API
    // =========================================================================
    public function mediaIndex($productInfoId)
    {
        $info = CustomerProductInfo::query()->findOrFail($productInfoId);

        $media = $info->media()
            ->orderByDesc('id')
            ->get()
            ->map(function ($m) {
                return [
                    'id'            => $m->id,
                    'type'          => $m->type,
                    'original_name' => $m->original_name,
                    'mime'          => $m->mime,
                    'size'          => $m->size,
                    'url'           => Storage::disk($m->disk)->url($m->path),
                    'created_at'    => optional($m->created_at)->toDateTimeString(),
                ];
            });

        return response()->json(['media' => $media]);
    }

    public function mediaUpload(Request $request, $productInfoId)
    {
        $info = CustomerProductInfo::query()->findOrFail($productInfoId);

        $request->validate([
            'files'   => ['required'],
            'files.*' => ['file', 'max:20480', 'mimetypes:application/pdf,image/jpeg,image/png,image/webp'],
        ]);

        $out = [];
        foreach ((array) $request->file('files', []) as $file) {
            $orig = $file->getClientOriginalName();
            $mime = $file->getMimeType();
            $size = $file->getSize();
            $ext  = $file->getClientOriginalExtension();

            $stored = (string) Str::uuid() . '.' . $ext;
            $dir    = "customer-products/{$info->id}";
            $path   = $file->storeAs($dir, $stored, 'public');

            $type = 'file';
            if (is_string($mime) && str_starts_with($mime, 'image/')) $type = 'image';
            if ($mime === 'application/pdf') $type = 'pdf';

            $m = $info->media()->create([
                'disk'          => 'public',
                'path'          => $path,
                'stored_name'   => $stored,
                'original_name' => $orig,
                'mime'          => $mime,
                'size'          => $size,
                'type'          => $type,
                'uploaded_by'   => Auth::id(),
                'sort_order'    => 0,
            ]);

            $out[] = [
                'id'            => $m->id,
                'type'          => $m->type,
                'original_name' => $m->original_name,
                'mime'          => $m->mime,
                'size'          => $m->size,
                'url'           => Storage::disk($m->disk)->url($m->path),
                'created_at'    => optional($m->created_at)->toDateTimeString(),
            ];
        }

        return response()->json(['media' => $out], 201);
    }

    public function mediaDelete($mediaId)
    {
        $m = CustomerProductInfoMedia::query()->findOrFail($mediaId);

        // delete file from disk first
        try {
            Storage::disk($m->disk ?? 'public')->delete($m->path);
        } catch (\Throwable $e) {
            // ignore disk errors
        }

        $m->delete();

        return response()->json(['success' => true]);
    }

    // =========================================================================
    // Internal helpers
    // =========================================================================
    private function productRowResponse(CustomerProductInfo $row, string $finalName): array
    {
        return [
            'id'                    => $row->id,
            'product_name'          => $finalName,
            'product_count'         => $row->product_count,
            'manufacturer'          => $row->manufacturer,
            'serial_number'         => $row->serial_number,
            'serial_numbers'        => $row->serial_numbers,
            'installation_date'     => $row->installation_date,
            'installation_location' => $row->installation_location,
            'purchased_from_us'     => (bool) $row->purchased_from_us,
            'purchase_date'         => $row->purchase_date,
            'invoice_reference'     => $row->invoice_reference,
            'warranty_until'        => $row->warranty_until,
            'guarantee_until'       => $row->guarantee_until,
            'image_available'       => (bool) $row->image_available,
            'installed_by'          => $row->installed_by,
            'department_name'       => optional($row->department)->department_name,
            'notes'                 => $row->notes,
        ];
    }

    /**
     * Helper to log activities for CustomerProductInfo.
     */
    private function logProductActivity($event, $productInfo, $leadId, $altId, $prodId, $changes = [])
    {
        $userName = 'System';

        if (Auth::check()) {
            // NOTE: your app stores employee id in auth()->user()->name (unusual but keep as-is)
            $employeeId = Auth::user()->name;
            $employee   = DB::table('employees')->where('id', $employeeId)->first();
            if ($employee) {
                $userName = trim($employee->name . ' ' . $employee->lastname);
            }
        }

        DB::table('lead_activity_logs')->insert([
            'new_leads_id'   => $leadId,
            'alternative_id' => $altId,
            'product_id'     => $prodId,
            'user_id'        => Auth::id(),
            'user_name'      => $userName,
            'event_type'     => $event,
            'model_type'     => 'App\Models\CustomerProductInfo',
            'model_id'       => $productInfo->id,
            'changes'        => json_encode($changes),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}
