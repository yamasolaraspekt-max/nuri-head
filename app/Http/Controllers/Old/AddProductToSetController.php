<?php

namespace App\Http\Controllers;

use App\Models\AddProductToSet;
use App\Models\Measure;
use App\Models\ProductMasterSet;
use Illuminate\Http\Request;
use DB;
use App\Models\Product;

class AddProductToSetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index($master, $phase)
    {
        $search = request()->query('search');

        if($search){
            $data['product']=Product::where('status', '=', 'Published')->get();
            $data['measure']=Measure::all();
            $data['title']=DB::table('product_master_sets')
            ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->join('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
            ->select('product_master_sets.id as master_id','sub_article_groups.sub_article', 'article_groups.article_group','product_master_sets.setname', 'article_groups.id as article_group_id', 'sub_article_groups.id as sub_id')
            ->where('product_master_sets.id', $master)
            ->first();
            $data['product_description']=DB::table('product_set_descriptions')->orderBy('id', 'asc')->get();
            $data['data'] = DB::table('add_product_to_sets')
                            ->join('product_master_sets', 'product_master_sets.id', '=', 'add_product_to_sets.master_set_id')
                            ->join('products', 'products.id', '=', 'add_product_to_sets.product_id')
                            ->join('measures', 'measures.id', 'add_product_to_sets.measure_unit')
                            ->where('add_product_to_sets.master_set_id', $master)
                            ->select('add_product_to_sets.*', 'product_master_sets.setname', 'products.product', 'measures.measure')
                            ->where('products.product', 'LIKE', "%$search%")
                            ->paginate(10);

            return view('admin.offer.set.products.product', $data);
                    
        }
        else{
            $data['product']=Product::where('status', '=', 'Published')->get();
            $data['measure']=Measure::all();
            $data['title']=DB::table('product_master_sets')
            ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->join('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
            ->select('product_master_sets.id as master_id','sub_article_groups.sub_article', 'article_groups.article_group','product_master_sets.setname', 'article_groups.id as article_group_id', 'sub_article_groups.id as sub_id')
            ->where('product_master_sets.id', $master)
            ->first();
            $data['product_description']=DB::table('product_set_descriptions')->orderBy('id', 'asc')->get();
            $data['data'] = DB::table('add_product_to_sets')
            ->join('product_master_sets', 'product_master_sets.id', '=', 'add_product_to_sets.master_set_id')
            ->join('products', 'products.id', '=', 'add_product_to_sets.product_id')
            ->join('measures', 'measures.id', 'add_product_to_sets.measure_unit')
            ->where('add_product_to_sets.master_set_id', $master)
            ->select('add_product_to_sets.*', 'product_master_sets.setname', 'products.product', 'measures.measure')
            ->paginate(10);

            return view('admin.offer.set.products.product', $data);

        }
    }

    /**
     * Show the form for creating a new resource.
     */
     
       
     public function create(Request $request, $master, $phase)
     {
         $search = $request->query('search');
     

         // Main distributor query
         $distributorQuery = DB::table('products')
            ->leftJoin('distributor_prices', 'products.id', '=', 'distributor_prices.product_id')
            ->leftJoin('distributors', 'distributor_prices.distributor_id', '=', 'distributors.id')
            ->leftJoin('discount_groups', 'discount_groups.id', '=', 'distributor_prices.discount_price')
            ->select(
                'distributors.name as distributor_name',
                'distributors.id as distributor_id',
                'distributor_prices.price',
                'distributor_prices.price_date',
                'distributor_prices.product_id',
                'distributor_prices.status',
                'discount_groups.discount_group',
                'discount_groups.discount',
                'distributor_prices.discount_price',
                'distributor_prices.availability',
                'distributor_prices.purchase_price',
                'products.id as product_id',
                'products.product',
                'products.article_no',
                'distributor_prices.id as price_id'
            )
            // Filter if price exists and is published
            ->where(function ($q) {
                $q->whereNull('distributor_prices.status')
                ->orWhere('distributor_prices.status', 'Published');
            });
     
     
         // Optional product name filter
         if (!empty($search)) {
                $distributorQuery->where('products.product', 'LIKE', "%$search%");
            }
        
     
        $data['distributor_price'] = $distributorQuery
            ->orderBy('distributor_prices.price_date', 'desc')
            ->get();
    
     
         // Shared Data
         $data['measure'] = Measure::all();
         $data['products'] = DB::table('products')->where('status', 'Published')->get();
     
         $data['title']=DB::table('product_master_sets')
            ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->join('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
            ->select('product_master_sets.id as master_id','sub_article_groups.sub_article', 'article_groups.article_group','product_master_sets.setname', 'article_groups.id as article_group_id', 'sub_article_groups.id as sub_id')
            ->where('product_master_sets.id', $master)
            ->first();
        
         return view('admin.offer.set.products.create_product', $data);
     }
     

    /**
     * Store a newly created resource in storage.
     */

     public function store(Request $request)
     {
         // 🔒 Validierung zuerst
         $request->validate([
             'product_id'      => 'required|integer',
             'price_id'        => 'required|integer',
             'distributor_id'  => 'required|integer',
             'product_count'   => 'required|numeric|min:1',
             'measure_unit'    => 'required|integer',
             'master_set_id'   => 'required|integer',
             'phase'           => 'required',
         ]);
     
         // 📦 Produkt & Preisdaten abrufen
         $product = DB::table('products')
             ->join('distributor_prices', 'distributor_prices.product_id', '=', 'products.id')
             ->select(
                 'products.product', 
                 'distributor_prices.price', 
                 'distributor_prices.discount_price', 
                 'distributor_prices.purchase_price',
                 'distributor_prices.price_date',
                 'distributor_prices.distributor_id',
                 'products.id'
             )
             ->where('products.id', $request->product_id)
             ->where('distributor_prices.distributor_id', $request->distributor_id)
             ->where('distributor_prices.id', $request->price_id)
             ->first();
     
         // 🛑 Absicherung, falls Preis nicht gefunden wird
         if (!$product) {
             return back()->with('error_msg', 'Produktpreis nicht gefunden.');
         }
     
         $purchase_price  = $product->purchase_price;
         $retail_price    = $product->price;
         $discount_group  = $product->discount_price;
         $product_count   = $request->product_count;
         $total           = $purchase_price * $product_count;
     
         // ✅ Neues Produkt dem Set hinzufügen
         AddProductToSet::create([
             'master_set_id'   => $request->master_set_id,
             'product_id'      => $request->product_id,
             'product_count'   => $product_count,
             'measure_unit'    => $request->measure_unit,
             'retail_price'    => $retail_price,
             'discount_group'  => $discount_group,
             'purchase_price'  => $purchase_price,
             'distributor_id'  => $request->distributor_id,
             'total'           => $total,
         ]);
     
         // 📊 Neue Set-Gesamtkosten berechnen
         $total_price     = DB::table('add_product_to_sets')->where('master_set_id', $request->master_set_id)->sum('total');
         $employee_hour   = DB::table('employee_sets')->where('master_set_id', $request->master_set_id)->sum('work_hour');
         $employee_price  = DB::table('employee_sets')->where('master_set_id', $request->master_set_id)->sum('buying_price');
     
         $employee_total = $employee_hour ? $employee_hour * $employee_price : 0;
         $sum = $total_price + $employee_total;
     
         // 💾 Set-Preis aktualisieren
         ProductMasterSet::where('id', $request->master_set_id)->update([
             'price'           => $sum,
             'material_price'  => $total_price,
             'employee_price'  => $employee_total,
         ]);
     
         return redirect()->to('/sets/' . $request->master_set_id . '/' . $request->phase)
             ->with('save_msg', 'Das Produkt wurde erfolgreich dem Set hinzugefügt.');
     }
     

    /**
     * Display the specified resource.
     */
    public function add(Request $request)
    {
      
     
       $this->validate($request, [
        'title' => 'required', 
        'value'     =>  'required'
       ]);

       $data = DB::table('product_set_descriptions')->insert([ 
                            'master_set_id' => $request->master_set,
                            'title' => $request->title,
                            'value' => $request->value
                        ]);
    
        return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich hinzugefügt!');
    }

 

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=AddProductToSet::find($id);

        $master_id = $data->master_set_id;
        $data_price = $data->total;

        $master_set = ProductMasterSet::find($master_id);
        $pervious_price= $master_set->price;
        $material_pervious = AddProductToSet::where('master_set_id', '=', $master_id)
                                ->sum('total');
        

        $sum = $pervious_price - $data_price;
        $material = $material_pervious - $data_price;

        ProductMasterSet::where('id', '=', $master_id)
        ->update([
                'price' => $sum,
                'material_price' => $material,
        ]);

        $data->delete();

        return redirect()->back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }

    public function search(Request $request)
{
    $search = $request->input('search');

    try {
        if ($search) {
            $distributor_price = DB::table('products')
                ->leftJoin('distributor_prices', 'products.id', '=', 'distributor_prices.product_id')
                ->leftJoin('distributors', 'distributor_prices.distributor_id', '=', 'distributors.id')
                ->leftJoin('discount_groups', 'discount_groups.id', '=', 'distributor_prices.discount_price')
                ->select(
                    'products.id as product_id',
                    'products.product',
                    'products.article_no',
                    'distributor_prices.id as price_id',
                    'distributor_prices.price',
                    'distributor_prices.purchase_price',
                    'distributor_prices.availability',
                    'distributor_prices.price_date',
                    'distributor_prices.status',
                    'distributor_prices.distributor_id',
                    'distributors.name as distributor_name',
                    'discount_groups.discount_group',
                    'discount_groups.discount'
                )
                ->where('products.product', 'LIKE', "%{$search}%")
                ->orderBy('distributor_prices.price_date', 'desc')
                ->get();

            return response()->json(['distributor_price' => $distributor_price]);
        } else {
            return response()->json(['error' => 'Kein Produkt eingegeben.']);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Fehler bei der Suche.']);
    }
}


}
