<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\Product;
use DB; 
use Illuminate\Support\Str;   
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Validator;
class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
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
            $data['description']=DB::table('product_images')
                                    ->join('products', 'products.id', '=', 'product_images.product_id')
                                    ->where('products.id', '=', $id)
                                    ->where('product_images.name', 'like', "%$search%")
                                    ->orWhere('products.product', 'LIKE', "%$search%")
                                    ->orWhere('products.model', 'LIKE', "%$search%")
                                    ->select('product_images.*', 'products.product', 'products.model')
                                    ->paginate(20);
            return view('admin.product.image.product_image', $data);
        }
        else{
            $data['data']=Product::find($id);
            $data['brand']=DB::table('products')->join('brands', 'brands.id', '=', 'products.brand_id')->where('products.id', '=', $id)->select('brands.name', 'brands.id')->first();
            $data['description']=DB::table('product_images')
                                    ->join('products', 'products.id', '=', 'product_images.product_id')
                                    ->where('products.id', '=', $id)
                                    ->select('product_images.*', 'products.product', 'products.model')
                                    ->paginate(20);
            return view('admin.product.image.product_image', $data);
        }
      
    }


    public function index(Product $product)
    {
        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->product,
                'image_url' => $product->product_image
                    ? asset('images/products/' . $product->product_image)
                    : null,
            ],
            'images' => $product->images()
                ->latest()
                ->get()
                ->map(function ($img) {
                    return [
                        'id' => $img->id,
                        'name' => $img->name,
                        'image' => $img->image,
                        'url' => asset('images/products/' . $img->image),
                    ];
                }),
        ]);
    }

    public function updateMain(Request $request, Product $product)
        {


            if ($request->hasFile('image')) {
                $file = $request->file('image');

                if (!$file->isValid()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Datei-Upload ungültig.',
                        'upload_error_code' => $file->getError(),
                        'upload_error_message' => $file->getErrorMessage(),
                    ], 422);
                }
            }

            $validator = Validator::make($request->all(), [
                'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,avif', 'max:4096'],
                'name'  => ['nullable', 'string', 'max:255'],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validierung fehlgeschlagen.',
                    'errors' => $validator->errors(),
                    'all' => $request->all(),
                    'has_file_image' => $request->hasFile('image'),
                ], 422);
            }

            $directory = public_path('images/products');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $file = $request->file('image');
            $filename = time() . '_' . Str::random(12) . '.' . $file->getClientOriginalExtension();
            $file->move($directory, $filename);

            if (!empty($product->product_image)) {
                $oldPath = $directory . '/' . $product->product_image;
                if (File::exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $product->update([
                'product_image' => $filename,
            ]);

            $galleryImage = ProductImage::create([
                'product_id' => $product->id,
                'name'       => $request->name ?: $product->product,
                'image'      => $filename,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produktbild wurde erfolgreich aktualisiert.',
                'image_url' => asset('images/products/' . $filename),
                'gallery_image' => [
                    'id' => $galleryImage->id,
                    'name' => $galleryImage->name,
                    'url' => asset('images/products/' . $galleryImage->image),
                ],
            ]);
        }
    public function destroyImage(ProductImage $image)
    {
        $filePath = public_path('images/products/' . $image->image);

        if (File::exists($filePath)) {
            @unlink($filePath);
        }

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bild wurde gelöscht.',
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product.*.product_id' => 'required',
            'product.*.image' => 'required|image',  // Add image validation rule
            // Add validation rules for 'title' if needed
        ]);
    
        foreach ($request->product as $value) {
            $data = new ProductImage;
    
            if ($file = $value['image']) {
                try {
                    $filename = uniqid().'.'.$file->getClientOriginalExtension();  // Use uniqid for filename
                    $destinationPath = 'images/products/';
                    $file->move($destinationPath, $filename);
    
                    $data->image = $filename;
                    $data->name = $value['title'];
                    $data->product_id = $value['product_id'];  // Assuming you have a 'product_id' field
                    $data->save();  // Save the data using the save method on the model
                } catch (\Exception $e) {
                    // Handle any exceptions that occur during file upload or data saving
                    return redirect()->back()
                        ->with('delete_msg', 'Error uploading file: '.$e->getMessage());
                }
            } else {
                return redirect()->back()
                    ->with('delete_msg', 'Please Upload a file');
            }
        }
    
        return redirect()->back()
            ->with('success_msg', 'Products and images saved successfully');
    }
    

    


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,)
    {
        $id=request()->input('id');

        $data=ProductImage::find($id);
        $data->name=$request->name;
        if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/products/', $image_name);
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
        $data=ProductImage::find($id);
        $data->delete();
        $this->delete_photo($data->image);

        return back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }

    public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='images/products/'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }


    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:5120',
            'product_id' => 'required|exists:products,id'
        ]);
    
        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('images/products'), $filename);
    
        ProductImage::create([
            'product_id' => $request->product_id,
            'image' => $filename
        ]);
    
        return response()->json(['success' => true]);
    }
    

    public function list($productId)
    {
        return ProductImage::where('product_id', $productId)->get();
    }

    public function updateName(Request $request, $id)
    {
        ProductImage::where('id', $id)->update(['name' => $request->name]);
        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $image = ProductImage::findOrFail($id);
        $path = public_path('images/products/' . $image->image);
        if (file_exists($path)) {
            unlink($path);
        }
        $image->delete();
        return response()->json(['success' => true]);
    }
}
