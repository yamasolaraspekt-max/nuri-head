<?php

namespace App\Http\Controllers\Product\Brand;
use App\Http\Controllers\Controller;

use App\Models\Brand;
use App\Models\BrandDepartment;
use Illuminate\Http\Request;

class BrandDepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $brandId)
    {
        $brand = Brand::findOrFail($brandId);

        $query = BrandDepartment::where('brand_id', $brand->id);

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand_department', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('home', 'like', "%{$search}%")
                  ->orWhere('office', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $department = $query
            ->orderByDesc('id')
            ->paginate(20)
            ->appends($request->query());

        // Produkte des Herstellers für die Profilansicht
        $products = \DB::table('products as p')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'p.article_group')
            ->where('p.brand_id', $brand->id)
            ->select('p.product', 'p.article_no', 'ag.article_group as gruppe', 'p.model', 'p.retail_price')
            ->orderBy('ag.article_group')
            ->orderBy('p.product')
            ->get();

        return view('admin.product.brand.brand_department', compact('brand', 'department', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'brand'                     => 'required|array|min:1',
                'brand.*.brand_id'         => 'required|integer|exists:brands,id',
                'brand.*.name'             => 'required|string|max:255',
                'brand.*.brand_department' => 'nullable|string|max:255',
                'brand.*.position'         => 'nullable|string|max:255',
                'brand.*.email'            => 'nullable|email|max:255',
                'brand.*.phone'            => 'nullable|string|max:100',
                'brand.*.home'             => 'nullable|string|max:100',
                'brand.*.office'           => 'nullable|string|max:100',
                'brand.*.address'          => 'nullable|string|max:255',
                'brand.*.status'           => 'nullable|string|max:50',
            ],
            [
                'brand.*.name.required' => 'Der Gesprächspartner ist erforderlich.',
                'brand.*.email.email'   => 'Bitte eine gültige E-Mail-Adresse eingeben.',
            ]
        );

        $brandId = null;

        foreach ($validated['brand'] as $row) {
            $brandId ??= (int) $row['brand_id'];

            BrandDepartment::create([
                'brand_id'         => $row['brand_id'],
                'name'             => $row['name'],
                'brand_department' => $row['brand_department'] ?? null,
                'position'         => $row['position'] ?? null,
                'email'            => $row['email'] ?? null,
                'phone'            => $row['phone'] ?? null,
                'home'             => $row['home'] ?? null,
                'office'           => $row['office'] ?? null,
                'address'          => $row['address'] ?? null,
                'status'           => $row['status'] ?? 'Unpublished',
            ]);
        }

        return redirect()
            ->route('brand.department.index', $brandId)
            ->with('save_msg', 'Datensatz erfolgreich gespeichert.');
    }

    public function update(Request $request, $id)
    {
        $department = BrandDepartment::findOrFail($id);

        $validated = $request->validate(
            [
                'brand_id'         => 'required|integer|exists:brands,id',
                'name'             => 'required|string|max:255',
                'brand_department' => 'nullable|string|max:255',
                'position'         => 'nullable|string|max:255',
                'email'            => 'nullable|email|max:255',
                'phone'            => 'nullable|string|max:100',
                'home'             => 'nullable|string|max:100',
                'office'           => 'nullable|string|max:100',
                'address'          => 'nullable|string|max:255',
                'status'           => 'nullable|string|max:50',
            ],
            [
                'name.required'  => 'Der Gesprächspartner ist erforderlich.',
                'email.email'    => 'Bitte eine gültige E-Mail-Adresse eingeben.',
            ]
        );

        $department->update([
            'brand_id'         => $validated['brand_id'],
            'name'             => $validated['name'],
            'brand_department' => $validated['brand_department'] ?? null,
            'position'         => $validated['position'] ?? null,
            'email'            => $validated['email'] ?? null,
            'phone'            => $validated['phone'] ?? null,
            'home'             => $validated['home'] ?? null,
            'office'           => $validated['office'] ?? null,
            'address'          => $validated['address'] ?? null,
            'status'           => $validated['status'] ?? ($department->status ?: 'Unpublished'),
        ]);

        return redirect()
            ->route('brand.department.index', $validated['brand_id'])
            ->with('update_msg', 'Datensatz erfolgreich aktualisiert.');
    }

    public function destroy($id)
    {
        $department = BrandDepartment::findOrFail($id);
        $brandId = $department->brand_id;
        $department->delete();

        return redirect()
            ->route('brand.department.index', $brandId)
            ->with('delete_msg', 'Datensatz erfolgreich gelöscht.');
    }
}