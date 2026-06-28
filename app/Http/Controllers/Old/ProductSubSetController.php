<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSet;
use App\Models\ProductMasterSet;
use App\Models\ProductSubSet;
use Illuminate\Http\Request;
use DB;
use App\Models\Product;
use App\Models\Measure;

class ProductSubSetController extends Controller
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
    
        // Common data fetched regardless of search
        $data['product'] = Product::where('status', '=', 'Published')->get();
        $data['measure'] = Measure::all();
        $data['product_description'] = DB::table('product_set_descriptions')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('product_set');

    
        $data['title'] = DB::table('product_master_sets')
            ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->join('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
            ->select(
                'sub_article_groups.sub_article',
                'article_groups.article_group',
                'product_master_sets.setname',
                'article_groups.id as article_group_id',
                'sub_article_groups.id as sub_id',
                'product_master_sets.id as master_id'
            )
            ->where('product_master_sets.id', $master)
            ->first();
    
        $data['skills'] = DB::table('employee_sets')
            ->join('positions', 'positions.id', '=', 'employee_sets.position_id')
            ->join('skills', 'skills.id', '=', 'employee_sets.grade')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'skills.product_id')
            ->select(
                'skills.*',
                'positions.position',
                'article_groups.article_group',
                'employee_sets.*'
            )
            ->where('employee_sets.master_set_id', '=', $master)
            ->get();
    
        $data['paragraph'] = DB::table('set_paragraphs')
            ->where('master_id', '=', $master)
            ->get();
    
        // Optional images, only when not searching
        if (!$search) {
            $data['images'] = DB::table('add_image_to_sets')
                ->join('product_images', 'product_images.product_id', '=', 'add_image_to_sets.product_id')
                ->select('add_image_to_sets.*', 'product_images.name', 'product_images.image')
                ->where('add_image_to_sets.master_set_id', '=', $master)
                ->where('add_image_to_sets.status', '=', 'Selected')
                ->get();
        }
    
        // Main data: Sub products filtered by search (if present)
        $data['data'] = DB::table('product_sub_sets')
            ->join('add_product_to_sets', 'add_product_to_sets.id', '=', 'product_sub_sets.main_product')
            ->join('product_master_sets', 'product_master_sets.id', '=', 'add_product_to_sets.master_set_id')
            ->join('products', 'products.id', '=', 'add_product_to_sets.product_id')
            ->join('measures', 'measures.id', '=', 'add_product_to_sets.measure_unit')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('products.product', 'like', "%$search%")
                      ->orWhere('products.model', 'like', "%$search%");
                });
            })
            ->where('add_product_to_sets.master_set_id', $master)
            ->select(
                'product_sub_sets.*',
                'product_master_sets.setname',
                'products.product',
                'measures.measure'
            )
            ->paginate(20);
    
        return view('admin.offer.set.sub_products.product', $data);
    }
    

    public function create(Request $request, $master)
    {
        $search = $request->query('search');
    
        // Common reusable data
        $data['measure'] = Measure::all();
        $data['products'] = DB::table('products')->where('status', 'Published')->get();
    
        $data['main_products'] = DB::table('add_product_to_sets')
            ->join('products', 'products.id', '=', 'add_product_to_sets.product_id')
            ->where('add_product_to_sets.master_set_id', $master)
            ->select('products.product', 'add_product_to_sets.*')
            ->get();
    
        $data['title'] = DB::table('product_master_sets')
            ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->leftJoin('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
            ->select(
                'sub_article_groups.sub_article as subArticle',
                'article_groups.article_group as articleName',
                'product_master_sets.*'
            )
            ->where('product_master_sets.id', $master)
            ->first();
    
        // Distributor price logic
        $query = DB::table('distributors')
            ->join('distributor_prices', 'distributor_prices.distributor_id', '=', 'distributors.id')
            ->leftJoin('discount_groups', 'discount_groups.id', '=', 'distributor_prices.discount_price')
            ->rightJoin('products', 'products.id', '=', 'distributor_prices.product_id');
    
        // Apply conditional join if no search (add_product_to_sets only relevant when not searching)
        if (!$search) {
            $query->rightJoin('add_product_to_sets', 'add_product_to_sets.product_id', '=', 'distributor_prices.product_id');
        }
    
        $query->select(
            'distributors.name as distributor_name',
            'distributors.id',
            'distributor_prices.price',
            'distributor_prices.price_date',
            'products.id as product_id',
            'distributor_prices.status',
            'discount_groups.discount_group',
            'discount_groups.discount',
            'distributor_prices.discount_price',
            'distributor_prices.availability',
            'distributor_prices.distributor_id',
            'distributor_prices.purchase_price',
            'products.product',
            'products.article_no',
            'distributor_prices.id as price_id'
        )
        ->where('distributor_prices.status', '=', 'Published');
    
        if ($search) {
            $query->where('products.product', 'LIKE', "%$search%");
        }
    
        $query->orderBy('distributor_prices.price_date', 'desc');
        $data['distributor_price'] = $query->get();
    
        return view('admin.offer.set.sub_products.create_product', $data);
    }
    

    /**
     * Store a newly created resource in storage.
     */

     public function store(Request $request)
     {
         $request->validate([
             'product_id' => 'required',
             'master_set_id' => 'required',
             'main_product' => 'required',
             'product_count' => 'required|numeric|min:1',
             'measure_unit' => 'required',
             'distributor_id' => 'required',
             'price_id' => 'required'
         ]);
     
         // Get the price info
         $product = DB::table('products')
             ->join('distributor_prices', 'distributor_prices.product_id', '=', 'products.id')
             ->select(
                 'products.product',
                 'distributor_prices.price',
                 'distributor_prices.discount_price',
                 'distributor_prices.discount_percent',
                 'distributor_prices.purchase_price',
                 'distributor_prices.price_date',
                 'distributor_prices.distributor_id',
                 'distributor_prices.discount_group_id',
                 'products.id'
             )
             ->where('products.id', $request->product_id)
             ->where('distributor_prices.distributor_id', $request->distributor_id)
             ->where('distributor_prices.id', $request->price_id)
             ->first();
     
         if (!$product) {
             return redirect()->back()->with('error', 'Preis nicht gefunden.');
         }
     
         $purchase_price = $product->purchase_price ?? 0;
         $retail_price = $product->price ?? 0;
         $discount_price = $product->discount_price ?? 0;
         $product_count = $request->product_count;
         $main_product = $request->main_product;
     
         $total = $purchase_price * $product_count;
     
         // Create sub product
         ProductSubSet::create([
             'master_set_id'    => $request->master_set_id,
             'product_id'       => $request->product_id,
             'product_count'    => $product_count,
             'measure_unit'     => $request->measure_unit,
             'retail_price'     => $retail_price,
             'main_product'     => $main_product,
             'discount_group'   => $discount_price,
             'purchase_price'   => $purchase_price,
             'distributor_id'   => $request->distributor_id,
             'total'            => $total,
             'status'           => 'active'
         ]);
     
         // Summing up master set values
         $total_price = DB::table('add_product_to_sets')
             ->where('master_set_id', $request->master_set_id)
             ->sum('total');
     
         $sub_total_price = DB::table('product_sub_sets')
             ->where('master_set_id', $request->master_set_id)
             ->sum('total');
     
         $employee_hour = DB::table('employee_sets')
             ->where('master_set_id', $request->master_set_id)
             ->sum('work_hour');
     
         $employee_price = DB::table('employee_sets')
             ->where('master_set_id', $request->master_set_id)
             ->sum('buying_price');
     
         $employee_total = $employee_hour ? $employee_hour * $employee_price : 0;
         $sum = $total_price + $sub_total_price + $employee_total;
     
         // Update the total price in product master set
         ProductMasterSet::where('id', $request->master_set_id)->update([
             'price' => $sum,
             'material_price' => $total_price + $sub_total_price,
             'employee_price' => $employee_total
         ]);
     
         return redirect()->to('/sets/' . $request->master_set_id . '/' . $request->phase)
             ->with('save_msg', 'Das Produkt wird dem Set hinzugefügt');
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

    /**
     * Show the form for creating a new resource.
     */
    
}
