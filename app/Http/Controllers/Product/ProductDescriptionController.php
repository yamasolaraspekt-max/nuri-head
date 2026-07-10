<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\ProductDescription;
use Illuminate\Http\Request;
use App\Models\Product;
use DB;

class ProductDescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct(){
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['update', 'updateAjax', 'updateDescription']);
        $this->middleware('permission:Product,delete')->only(['destroy', 'deleteDescription', 'destroyDescription']);
        $this->middleware('auth');
    }
    public function index($id)
    {
        $search = trim((string) request()->query('search', ''));

        $data['data'] = Product::findOrFail($id);

        $data['brand'] = DB::table('products')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->where('products.id', '=', $id)
            ->select('brands.name', 'brands.id')
            ->first();

        $data['description'] = DB::table('product_descriptions')
            ->join('products', 'products.id', '=', 'product_descriptions.product_id')
            ->where('products.id', '=', $id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_descriptions.field', 'LIKE', "%{$search}%")
                        ->orWhere('product_descriptions.description', 'LIKE', "%{$search}%")
                        ->orWhere('product_descriptions.remark', 'LIKE', "%{$search}%")
                        ->orWhere('products.product', 'LIKE', "%{$search}%")
                        ->orWhere('products.model', 'LIKE', "%{$search}%");
                });
            })
            ->select(
                'product_descriptions.*',
                'products.product',
                'products.model'
            )
            ->orderByDesc('product_descriptions.id')
            ->paginate(20)
            ->appends(request()->query());

        return view('admin.product.product.product_create_description', $data);
    }


    public function store(Request $request)
    {
        $request->validate([
            'product.*.product_id' => 'required',
            'product.*.field'  =>  'required',
            'product.*.description'    =>  'required',

        ],
        [
            'product.*.product_id' => 'Product ID is required',
            'product.*.field'  =>  'Product Heading  is required',
            'product.*.description'    =>  'Product item Description is required',
        ]
       
    );
    
        foreach ($request->product as $key => $value) {
            ProductDescription::create($value);

        }
        return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert');
    }

   
    public function update(Request $request)
    {
        $id=request()->input('id');

        $data=ProductDescription::find($id);
        $data->field=$request->field;
        $data->description=$request->description;
        $data->remark=$request->remark;

        $data->save();
        return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=ProductDescription::find($id);
        $data->delete();
        return redirect()->back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht ');
    }



public function storeAjax(Request $request)
{
    $request->validate([
        'product.*.field' => 'required',
        'product.*.description' => 'required',
    ]);

    foreach ($request->product as $row) {
        ProductDescription::create([
            'product_id' => $request->product_id,
            'field' => $row['field'],
            'description' => $row['description'],
            'remark' => $row['remark'] ?? null,
        ]);
    }

    return response()->json(['message' => 'Datensätze gespeichert']);
}

public function getDescriptions($product_id)
{
    $data = ProductDescription::where('product_id', $product_id)->get();
    return response()->json($data);
}

public function deleteDescription($id)
{
    ProductDescription::findOrFail($id)->delete();
    return response()->json(['message' => 'Eintrag gelöscht']);
}

public function updateAjax(Request $request, $id)
{
    $request->validate([
        'field' => 'required|string',
        'description' => 'required|string',
    ]);

    $desc = ProductDescription::findOrFail($id);
    $desc->update([
        'field' => $request->field,
        'description' => $request->description,
        'remark' => $request->remark,
    ]);

    return response()->json(['message' => 'Eintrag aktualisiert']);
}


 // POST /products/{product}/descriptions/bulk-store
    public function bulkStore(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $data = $request->validate([
            'field'        => ['required', 'array', 'min:1'],
            'field.*'      => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'array'],
            'description.*'=> ['nullable', 'string', 'max:255'],
            'remark'       => ['nullable', 'array'],
            'remark.*'     => ['nullable', 'string', 'max:255'],
            'status'       => ['nullable', 'array'],
            'status.*'     => ['nullable', 'string', 'max:255'],
        ]);

        $created = [];

        foreach ($data['field'] as $index => $fieldValue) {
            if (!trim($fieldValue)) {
                continue;
            }

            $created[] = ProductDescription::create([
                'product_id'  => $product->id,
                'field'       => $fieldValue,
                'description' => $data['description'][$index] ?? null,
                'remark'      => $data['remark'][$index] ?? null,
                'status'      => $data['status'][$index] ?? null,
            ]);
        }

        return response()->json([
            'success'       => true,
            'descriptions'  => $created,
            'message'       => 'Technische Daten angelegt.',
        ]);
    }

    // PUT /products/descriptions/{description}
    public function updateDescription(Request $request, ProductDescription $description)
    {
        $data = $request->validate([
            'field'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'remark'      => ['nullable', 'string', 'max:255'],
            'status'      => ['nullable', 'string', 'max:255'],
        ]);

        $description->update($data);

        return response()->json([
            'success'     => true,
            'description' => $description,
            'message'     => 'Technische Beschreibung aktualisiert.',
        ]);
    }

    // DELETE /products/descriptions/{description}
    public function destroyDescription(ProductDescription $description)
    {
        $description->delete();

        return response()->json([
            'success' => true,
            'message' => 'Technische Beschreibung gelöscht.',
        ]);
    }
}
