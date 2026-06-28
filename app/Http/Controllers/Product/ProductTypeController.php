<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\Brand;
use App\Models\Distributor;
use App\Models\ProductDescription;
use App\Models\ProductType;
use DB;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {

        $search=request()->query('search');

        if($search){ 
            $data['brand']=Brand::find($id);
            $data['types']=DB::table('product_types')
                                ->join('brands', 'brands.id', '=', 'product_types.product_id')
                                ->where('brands.id', '=', $id)
                                ->where('product_types.type', 'LIKE', "%$search%")
                                ->orWhere('product_types.ean', 'LIKE', "%$search%")
                                ->orWhere('product_types.article', 'LIKE', "%$search%")
                                ->orWhere('product_types.serial', 'LIKE', "%$search%")
                                ->select('product_types.*', 'brands.name as uname')
                                ->paginate(10);
                             return view('admin.product.type.type', $data);
            }   
            else{
                $data['brand']=Brand::find($id);
                $data['types']=DB::table('product_types')
                                    ->join('brands', 'brands.id', '=', 'product_types.product_id')
                                    ->where('brands.id', '=', $id)
                                    ->select('product_types.*', 'brands.name as uname')
                                    ->paginate(10);
                                 return view('admin.product.type.type', $data);
            }
       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['brand']=Brand::all();
        $data['distributor']=Distributor::all();
        return view('admin.product.product.product_create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product.*.article' =>  'required',
            'product.*.serial' =>  'required',
            'product.*.ean' =>  'required',
            'product.*.type' =>  'required',

        ],[
            'product.*.article' =>  'Article-nummer ist erforderlich',
            'product.*.serial' =>  'Produkt Serial ist erforderlich',
            'product.*.ean' =>  'Produkt EAN-Nummer ist erforderlich',
            'product.*.type' =>  'Produkt-typ ist erforderlich',
        ]
    );

        foreach ($request->product as $key => $value) {
            ProductType::create($value);
        }

        return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert');
    }

    public function description($id){
        $data['data']=ProductType::find($id);
        $data['brand']=DB::table('product_types')->join('brands', 'brands.id', '=', 'product_types.brand_id')->where('product_types.id', '=', $id)->select('brands.name', 'brands.id')->first();
        $data['description']=ProductDescription::orderBy('id', 'desc')->paginate(20);
        return view('admin.product.product.product_create_description', $data);
    }


    public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='images/type/'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }
    public function save_image(Request $request){

        $request->validate(
            ['image' =>'required'], 
            ['image'=>'Produkt foto ist erforderlich']
        );

        $id=$_POST['id'];
        $data=ProductType::find($id);
        if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/type/', $image_name);
            $data->image=$image_name;
            $data->save();

             return back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert');

        }
        else{
            return back()->with('delete_msg', 'Das Bild wurde nicht erfolgreich gespeichert');

        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
      

        $id=$_POST['id'];
        ProductType::find($id)->update($request->all());
         return back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductType $productType)
    {
        //
    }
}
