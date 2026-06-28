<?php

namespace App\Http\Controllers;

use App\Models\AddImageToSet;
use Illuminate\Http\Request;
use App\Models\Product;
use DB;
 
class AddImageToSetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($master)
    {
        $search = request()->query('search');

        if($search){
            $data['product']=Product::where('status', '=', 'Published')->get();
            $data['title']=DB::table('product_master_sets')
            ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->join('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
            ->select('product_master_sets.id as master_id','sub_article_groups.sub_article', 'article_groups.article_group','product_master_sets.setname', 'article_groups.id as article_group_id', 'sub_article_groups.id as sub_id')
            ->where('product_master_sets.id', $master)
            ->first();
             

    

                            return view('admin.offer.set.image.image', $data); 
                    
        }
        else{
            $data['product']=Product::where('status', '=', 'Published')->get();
            $data['title']=DB::table('product_master_sets')
            ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->join('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
            ->select('product_master_sets.id as master_id','sub_article_groups.sub_article', 'article_groups.article_group','product_master_sets.setname', 'article_groups.id as article_group_id', 'sub_article_groups.id as sub_id')
            ->where('product_master_sets.id', $master)
            ->first();

            $data['data'] = DB::table('add_image_to_sets')
            ->join('product_master_sets', 'product_master_sets.id', '=', 'add_image_to_sets.master_set_id')
            ->join('products', 'products.id', '=', 'add_image_to_sets.product_id')
            ->leftJoin('product_images', 'product_images.product_id', '=', 'add_image_to_sets.product_id')
            ->where('add_image_to_sets.master_set_id', $master)
            ->select('add_image_to_sets.*', 'product_master_sets.setname', 'products.product as product_name','product_images.image', 'product_images.name as image_name')
            ->paginate(10);


     
    
            return view('admin.offer.set.image.image', $data); 
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(request $request, $master)
    {
        $search = request()->query('search');
        if($search){
       
         
          
          $data['products']=DB::table('products')->where('status', '=', 'Published')->get();
            $data['data']=DB::table('products')
                                ->join('product_images', 'product_images.product_id', '=', 'products.id')
                                ->select('products.*', 'product_images.name', 'product_images.image', 'product_images.id as image_id')
                                ->where('products.product', 'like', "%$search%")
                                ->orWhere('products.model', 'like', "%$search%")
                                ->get();
        
              return view('admin.offer.set.image.create_image', $data);
        }
        else {
         
          $data['products']=DB::table('products')->where('status', '=', 'Published')->get();
          $data['data']=DB::table('products')
          ->join('product_images', 'product_images.product_id', '=', 'products.id')
          ->select('products.*', 'product_images.name', 'product_images.image', 'product_images.id as image_id')

          ->get();
          return view('admin.offer.set.image.create_image', $data);

    }
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'product_id' => 'required', 
        ]);
       
       $data= AddImageToSet::create([
            "master_set_id" => $request->master_set_id,
            'product_id'    =>  $request->product_id,
            'image' =>  $request->image,
            'name'  =>  $request->name,
            'status'  => 'Unpublished',
        ]);

        return redirect()->to('/sets/'.$request->master_set_id)->with('save_msg', 'Das Produkt wird dem Set hinzugefügt');

    }

    /**
     * Display the specified resource.
     */
    public function show(AddImageToSet $addImageToSet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AddImageToSet $addImageToSet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AddImageToSet $addImageToSet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = AddImageToSet::find($id);
        $data->delete();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurde ergfulreisch  gelöscht');
    }
}
