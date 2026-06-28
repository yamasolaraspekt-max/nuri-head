<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Models\ProductWP;
use Illuminate\Http\Request;
use DB;
use App\Models\Product;
class ProductWPController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct(){
        $this->middleware('auth');
     }
    public function index($id)
    {
        $search = request()->query('search');

        if($search){
            $data['product_id']=$id;
            $data['product']=Product::find($id);
            $data['data']=DB::table('product_w_p_s')
                      ->join('products', 'products.id', 'product_w_p_s.product_id')
                      ->where('products.id', $id)
                      ->where('type','like',"%$search%")        
                      ->orWhere('max_kw','like',"%$search%")        
                      ->orWhere('min_kw','like',"%$search%") 
                      ->select('product_w_p_s.*', 'products.product')
                      ->paginate(30);
                      
            return view('admin.product.product.pages.wp', $data);
        }
        else{
            $data['product_id']=$id;
            $data['product']=Product::find($id);
            $data['data']=DB::table('product_w_p_s')
                ->join('products', 'products.id', 'product_w_p_s.product_id')
                ->where('products.id', $id)
                ->select('product_w_p_s.*', 'products.product')
                ->paginate(30);
            return view('admin.product.product.pages.wp', $data);
        }
    }


    public function analytic($id){

         $data['product_id']=$id;
            $data['product']=Product::find($id);
            $data['data']=DB::table('product_w_p_s')
                ->join('products', 'products.id', 'product_w_p_s.product_id')
                ->where('products.id', $id)
                ->select('product_w_p_s.*', 'products.product')
                ->paginate(30);
            return view('admin.product.product.pages.wp_analytic', $data);

    }
    // Get the data dynamically via AJAX
    public function get($id)
    {
        $data = DB::table('product_w_p_s')
            ->join('products', 'products.id', '=', 'product_w_p_s.product_id')
            ->where('products.id', $id)
            ->select('product_w_p_s.*', 'products.product')
            ->get();

        return response()->json($data);
    }

    // Update product details
    public function update(Request $request, $id)
    {
        $product_wp = ProductWP::find($id);
        $product_wp->update($request->all());

        return response()->json(['success' => 'Record updated successfully!']);
    }

    // Delete product
    public function destroy($id)
    {
        ProductWP::find($id)->delete();

        return response()->json(['success' => 'Record deleted successfully!']);
    }

 
  public function store(Request $request)
{
    $request->validate([
        'd.*.temp_celsius' => 'required|numeric',
        'd.*.max_kw' => 'required|numeric',
        'd.*.min_kw' => 'required|numeric',
        'd.*.type' => 'required|string',
    ], [
        'd.*.temp_celsius.required' => 'Außen Temp. in Celsius: Dieses Feld ist erforderlich',
        'd.*.max_kw.required' => 'Maximale Leistung in kW: Dieses Feld ist erforderlich',
        'd.*.min_kw.required' => 'Minimale Leistung in kW: Dieses Feld ist erforderlich',
        'd.*.type.required' => 'Typ: Dieses Feld ist erforderlich',
    ]);

    // Process the data
    foreach ($request->input('d') as $data) {
        // Sanitize the input
        $data['temp_celsius'] = str_replace(',', '.', $data['temp_celsius']);
        $data['max_kw'] = str_replace(',', '.', $data['max_kw']);
        $data['min_kw'] = str_replace(',', '.', $data['min_kw']);

        // Store the data in the database
        ProductWP::create($data);
    }

    // Return a JSON response for AJAX
    return response()->json(['success' => 'Record has been saved successfully!'], 200);
}


    /**
     * Display the specified resource.
     */
    public function show(ProductWP $productWP)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductWP $productWP)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    
}
