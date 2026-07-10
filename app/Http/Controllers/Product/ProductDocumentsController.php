<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\ProductDocuments;
use Illuminate\Http\Request;
use DB, Auth;
use App\Models\Product;


class ProductDocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['update', 'updateName']);
        $this->middleware('permission:Product,delete')->only(['destroy', 'delete_photo', 'delete']);
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $search=request()->query('search');

        if($search){
            $data['data']=Product::find($id);
            $data['brand']=DB::table('products')->join('brands', 'brands.id', '=', 'products.brand_id')->where('products.id', '=', $id)->select('brands.name', 'brands.id')->first();
            $data['description']=DB::table('product_documents')
                                    ->join('products', 'products.id', '=', 'product_documents.product_id')
                                    ->where('products.id', '=', $id)
                                    ->where('product_documents.name', 'like', "%$search%")
                                    ->orWhere('products.product', 'LIKE', "%$search%")
                                    ->orWhere('products.model', 'LIKE', "%$search%")
                                    ->select('product_documents.*', 'products.product', 'products.model')
                                    ->paginate(20);
            return view('admin.product.document.product_document', $data);
        }
        else{
            $data['data']=Product::find($id);
            $data['brand']=DB::table('products')->join('brands', 'brands.id', '=', 'products.brand_id')->where('products.id', '=', $id)->select('brands.name', 'brands.id')->first();
            $data['description']=DB::table('product_documents')
                                    ->join('products', 'products.id', '=', 'product_documents.product_id')
                                    ->where('products.id', '=', $id)
                                    ->select('product_documents.*', 'products.product', 'products.model')
                                    ->paginate(20);
                                    return view('admin.product.document.product_document', $data);
             }
}

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $request->validate([
        'product.*.product_id' => ['required'],
        'product.*.document'   => ['required','file'],
        'product.*.title'      => ['nullable','string','max:255'],
    ]);

    $destinationPath = public_path('images/products/document');

    if (!is_dir($destinationPath)) {
        mkdir($destinationPath, 0775, true);
    }

    foreach ($request->product as $value) {
        $data = new ProductDocuments;

        /** @var \Illuminate\Http\UploadedFile|null $file */
        $file = $value['document'] ?? null;

        if (!$file) {
            return redirect()->back()->with('delete_msg', 'Please Upload a file');
        }

        try {
            // 1) keep original name
            $original = $file->getClientOriginalName(); // e.g. "datasheet v1.pdf"

            // 2) sanitize filename (avoid weird chars / paths)
            $original = str_replace(['\\', '/'], '-', $original);
            $original = preg_replace('/[^\pL\pN\.\-\_\s\(\)]+/u', '', $original);
            $original = preg_replace('/\s+/', ' ', trim($original));

            // 3) split name/ext
            $ext  = $file->getClientOriginalExtension();
            $base = $ext ? preg_replace('/\.' . preg_quote($ext, '/') . '$/i', '', $original) : pathinfo($original, PATHINFO_FILENAME);
            $ext  = $ext ?: pathinfo($original, PATHINFO_EXTENSION);

            // 4) ensure unique on disk (only if needed)
            $filename = $base . ($ext ? '.' . $ext : '');
            $counter  = 1;

            while (file_exists($destinationPath . DIRECTORY_SEPARATOR . $filename)) {
                $filename = $base . ' (' . $counter . ')' . ($ext ? '.' . $ext : '');
                $counter++;
            }

            // 5) move file
            $file->move($destinationPath, $filename);

            // 6) save record
            $data->document   = $filename;
            $data->title      = $value['title'] ?? null;
            $data->product_id = $value['product_id'];
            $data->save();

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('delete_msg', 'Error uploading file: ' . $e->getMessage());
        }
    }

    return redirect()->back()->with('success_msg', 'Products and documents saved successfully');
}

 

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id=request()->input('id');

        $data=ProductDocuments::find($id);
        $data->title=$request->title;
        $data->product_id=$request->product_id;
        if($request->hasFile('document')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('document')->getClientOriginalExtension();
            $request->file('document')->move('images/products/document', $image_name);
            $data->image=$image_name;
            $data->save();
            
            return redirect()->back()->with('save_msg', 'Products and images saved successfully');

        }
        else{
            $data->save();
            return redirect()->back()->with('not_save', 'The   upload is not completed!');

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=ProductDocuments::find($id);
        $data->delete();
        $this->delete_photo($data->document);

        return back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }
    public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='images/products/document'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }


    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,xls,xlsx,txt|max:10240',
            'product_id' => 'required|exists:products,id',
        ]);

        $file = $request->file('file');
        $fileName = time().'_'.$file->getClientOriginalName();
        $file->move(public_path('documents/products'), $fileName);

        ProductDocuments::create([
            'product_id' => $request->product_id,
            'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'document' => $fileName,
        ]);

        return response()->json(['message' => 'Dokument erfolgreich hochgeladen']);
    }

    public function list($product_id)
    {
        return ProductDocuments::where('product_id', $product_id)->get();
    }

    public function delete($id)
    {
        $doc = ProductDocuments::findOrFail($id);
        $path = public_path('documents/products/'.$doc->document);
        if (file_exists($path)) unlink($path);

        $doc->delete();

        return response()->json(['message' => 'Dokument gelöscht']);
    }

    public function updateName(Request $request, $id)
    {
        // ✅ Validate input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
    
        // ✅ Find the document or fail
        $document = ProductDocuments::findOrFail($id);
    
        // ✅ Update and save the new title
        $document->title = $request->name;
        $document->save();
    
        // ✅ Return JSON response
        return response()->json([
            'message' => 'Dokumentname erfolgreich aktualisiert',
            'title' => $document->title,
            'id' => $document->id
        ]);
    }
    

}
