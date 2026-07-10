<?php

namespace App\Http\Controllers\Product\Brand;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['update', 'publish', 'unpublish']);
        $this->middleware('permission:Product,delete')->only(['destroy']);
        $this->middleware('auth');
    }

    protected function searchAndPaginate($query, ?string $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('initial', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhere('purpose', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%');
            });
        }

        return $query->orderByDesc('id')->paginate(20)->appends(request()->query());
    }

    protected function renderBrandList($query, ?string $search = null)
    {
        $data = $this->searchAndPaginate($query, $search);

        return view('admin.product.brand.brand', [
            'data'  => $data,
            'brand' => null, // safe fallback so blade never crashes
        ]);
    }

    public function index()
    {
        return $this->renderBrandList(Brand::query(), request('search'));
    }

    public function sub_contractor()
    {
        return $this->renderBrandList(
            Brand::where('type', 'sub_contractor'),
            request('search')
        );
    }

    public function architect()
    {
        return $this->renderBrandList(
            Brand::where('type', 'architect'),
            request('search')
        );
    }

    public function bank()
    {
        return $this->renderBrandList(
            Brand::where('type', 'bank'),
            request('search')
        );
    }

    public function insurance()
    {
        return $this->renderBrandList(
            Brand::where('type', 'insurance'),
            request('search')
        );
    }

    public function contractor()
    {
        return $this->renderBrandList(
            Brand::where('type', 'contractor'),
            request('search')
        );
    }

    public function other()
    {
        return $this->renderBrandList(
            Brand::whereNotIn('type', ['sub_contractor', 'contractor', 'brand', 'bank', 'insurance']),
            request('search')
        );
    }

    protected function deletePhoto(?string $photo): void
    {
        if (!$photo) {
            return;
        }

        $photoPath = public_path('images/brand/' . $photo);

        if (file_exists($photoPath)) {
            @unlink($photoPath);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'initial' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'image'   => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
        ]);

        $brand = new Brand();
        $brand->type = $request->type ?? 'brand';
        $brand->name = $request->name;
        $brand->initial = $request->initial;
        $brand->purpose = $request->purpose;
        $brand->address = $request->address;
        $brand->status = 'Published';

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('images/brand'), $imageName);
            $brand->image = $imageName;
        }

        $brand->save();

        return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'      => 'required|integer|exists:brands,id',
            'name'    => 'required|string|max:255',
            'initial' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'image'   => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:2048',
        ]);

        $brand = Brand::findOrFail($request->id);
        $brand->type = $request->type ?? $brand->type ?? 'brand';
        $brand->name = $request->name;
        $brand->initial = $request->initial;
        $brand->purpose = $request->purpose;
        $brand->address = $request->address;
        $brand->status = 'Published';

        if ($request->hasFile('image')) {
            $this->deletePhoto($brand->image);

            $imageName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('images/brand'), $imageName);
            $brand->image = $imageName;
        }

        $brand->save();

        return redirect()->back()->with('update_msg', 'Der Datensatz wurde erfolgreich aktualisiert');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $this->deletePhoto($brand->image);
        $brand->delete();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }

    public function publish($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->status = 'Published';
        $brand->save();

        return redirect()->back()->with('save_msg', 'Das Unternehmen ist jetzt veröffentlicht');
    }

    public function unpublish($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->status = 'Unpublished';
        $brand->save();

        return redirect()->back()->with('delete_msg', 'Der Artikel wurde nun erfolgreich unveröffentlicht!');
    }
}