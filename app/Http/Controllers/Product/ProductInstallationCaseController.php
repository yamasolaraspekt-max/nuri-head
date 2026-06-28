<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\ProductInstallationCase;
use Illuminate\Http\Request;
use App\Models\Product;
use DB;
class ProductInstallationCaseController extends Controller
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

        $data = DB::table('product_installation_cases')
            ->join('products', 'products.id', '=', 'product_installation_cases.product_id')
            ->where('product_installation_cases.product_id', '=', $id)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_installation_cases.case', 'LIKE', "%{$search}%")
                        ->orWhere('product_installation_cases.description', 'LIKE', "%{$search}%");
                });
            })
            ->select(
                'product_installation_cases.*',
                'products.product',
                'products.id as productid'
            )
            ->orderBy('product_installation_cases.case')
            ->paginate(10)
            ->appends(request()->query());

        return view('admin.product.installation.product_installation')->with('data', $data);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'case'  =>  'required',
            'description'   => 'required',
            'rate'  =>  'required'
        ]);
        $id=request()->input('id');
        $data=new ProductInstallationCase;
        $data->product_id=$id;
        if($request->case=="Benutzerdefiniert"){
            $data->case=$request->custom;
        }else{
            $data->case=$request->case;
        }
        $data->description=$request->description;
        $data->rate=$request->rate;

        $data->save();

        return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert');
       
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductInstallationCase $productInstallationCase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductInstallationCase $productInstallationCase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductInstallationCase $productInstallationCase)
    {
        $product_id=request()->input('product_id');
        $id=request()->input('id');
        $data=ProductInstallationCase::find($id);
        $data->product_id=$product_id;
        if($request->case=="Benutzerdefiniert"){
            $data->case=$request->custom;
        }else{
            $data->case=$request->case;
        }
        $data->description=$request->description;
        $data->rate=$request->rate;

        $data->save();

        return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data=ProductInstallationCase::find($id);
        $data->delete();

        return back()->with('save_msg', 'Der Datensatz wurde erfolgreich gelöscht ');
    }
}
