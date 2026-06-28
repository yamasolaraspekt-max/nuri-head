<?php

namespace App\Http\Controllers;

use App\Models\AddProductToSet;
use App\Models\ArticleGroup;
use App\Models\ProductMasterSet;
use App\Models\SubArticleGroup;
use DB;
use Illuminate\Http\Request;
use App\Models\Product; 
use App\Models\Measure; 
use App\Models\TaskPhase; 
use App\Models\EmployeeSet; 
use App\Models\ProductSubSet; 
use App\Models\SetParagraph;  


class ProductMasterSetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($article, $sub_article)
{
    $search = request()->query('search');

    // Title
    $data['title'] = DB::table('sub_article_groups')
        ->join('article_groups', 'article_groups.id', '=', 'sub_article_groups.article_group_id')
        ->select('sub_article_groups.sub_article', 'article_groups.article_group')
        ->where('sub_article_groups.id', $sub_article)
        ->first();

    // Lists (unchanged)
    $data['phase'] = TaskPhase::where('product_id', $article)->get();
    $data['sub_article'] = SubArticleGroup::where([
        ['article_group_id', '=', $article],
        ['id', '=', $sub_article],
    ])->get();

    // Base query for master sets
    $query = DB::table('product_master_sets')
        ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
        ->join('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
        ->join('task_phases', 'task_phases.id', '=', 'product_master_sets.phase_id')
        ->select(
            'product_master_sets.*',
            'article_groups.article_group',
            'sub_article_groups.sub_article',
            'task_phases.phase_name',
            'article_groups.id as article_group_id',
            'sub_article_groups.id as sub_article_id'
        )
        ->where('product_master_sets.article_group', $article)
        ->where('product_master_sets.sub_article', $sub_article);

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('product_master_sets.setname', 'LIKE', "%{$search}%")
              ->orWhere('article_groups.article_group', 'LIKE', "%{$search}%");
        });
    }

    // --- NEW: auto-refresh aggregates for ALL sets in this filter before display ---
    $allIds = (clone $query)->pluck('product_master_sets.id')->all();
    if (!empty($allIds)) {
        $this->syncMasterAggregates($allIds);
    }
    // ------------------------------------------------------------------------------

    // Now paginate AFTER sync so values are current
    $data['data'] = $query->paginate(20);

    return view('admin.offer.set.main.master', $data);
}

/**
 * Bulk recompute price + percentages (employee, material, asset) for the given master set IDs.
 */
private function syncMasterAggregates(array $ids): void
{
    // sums grouped by master id
    $mat = DB::table('add_product_to_sets')
        ->select('master_set_id as mid', DB::raw('COALESCE(SUM(total),0) as s'))
        ->whereIn('master_set_id', $ids)
        ->groupBy('master_set_id')
        ->pluck('s','mid');

    // If your employee_sets has a "total" column, use that; otherwise fallback to work_hour * buying_price
    $emp = DB::table('employee_sets')
        ->select('master_set_id as mid', DB::raw('COALESCE(SUM(total),0) as s'))
        ->whereIn('master_set_id', $ids)
        ->groupBy('master_set_id')
        ->pluck('s','mid');

    $ast = DB::table('asset_sets')
        ->select('master_id as mid', DB::raw('COALESCE(SUM(total_price),0) as s'))
        ->whereIn('master_id', $ids)
        ->groupBy('master_id')
        ->pluck('s','mid');

    DB::transaction(function() use ($ids, $mat, $emp, $ast) {
        foreach ($ids as $id) {
            $m = (float)($mat[$id] ?? 0);
            $e = (float)($emp[$id] ?? 0);
            $a = (float)($ast[$id] ?? 0);
            $total = $m + $e + $a;

            $mp = $total > 0 ? ($m / $total) * 100 : 0;
            $ep = $total > 0 ? ($e / $total) * 100 : 0;
            $ap = $total > 0 ? ($a / $total) * 100 : 0;

            DB::table('product_master_sets')->where('id', $id)->update([
                'material_price'   => round($m, 2),
                'employee_price'   => round($e, 2),
                'asset_price'      => round($a, 2),
                'price'            => round($total, 2),
                'material_percent' => round($mp, 2),
                'employee_percent' => round($ep, 2),
                'asset_percent'    => round($ap, 2),
                'updated_at'       => now(),
            ]);
        }
    });
}

    /**
     * Show the form for creating a new resource.
     */
    public function article(Request $request)
    {
        $search = $request->input('search');
    
        $articleQuery = \App\Models\ArticleGroup::query()
            ->with(['subGroups' => function ($q) {
                $q->withCount('masterSets');
            }]);
    
        if ($search) {
            $articleQuery->where('article_group', 'like', "%{$search}%")
                ->orWhereHas('subGroups', function ($q) use ($search) {
                    $q->where('sub_article', 'like', "%{$search}%");
                });
        }
    
        $data['article'] = $articleQuery->get();
        $data['sub_article'] = \App\Models\SubArticleGroup::all();
    
        return view('admin.offer.set.article_group_sets.article_groups', $data);
    }
    
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         
        $validate = $request->validate([
            'setname' => 'required',
            'article_group' => 'required|exists:article_groups,id',
            'sub_article' => 'required|exists:sub_article_groups,id',
            'phase_id' => 'required|exists:task_phases,id',
            'price' => 'nullable|numeric', // Use 'numeric' instead of 'number'
        ]);

        // Create the new ProductMasterSet record
        ProductMasterSet::create($validate);

        // Redirect back with success message
        return redirect()->back()->with('save_msg', 'Der Master-Satz wurde erfolgreich generiert');
    }


    /**
     * Display the specified resource.
     */
    public function refresh($id)
    {
        $employee = (float) DB::table('employee_sets')
            ->where('master_set_id', $id)
            ->sum('total'); // or DB::raw('work_hour * buying_price') if you don't persist "total"

        $material = (float) DB::table('add_product_to_sets')
            ->where('master_set_id', $id)
            ->sum('total');

        $asset = (float) DB::table('asset_sets')
            ->where('master_id', $id)
            ->sum('total_price');

        $total = $employee + $material + $asset;

        $employee_percentage = $total > 0 ? ($employee / $total) * 100 : 0;
        $material_percentage = $total > 0 ? ($material / $total) * 100 : 0;
        $asset_percentage    = $total > 0 ? ($asset    / $total) * 100 : 0;

        DB::table('product_master_sets')->where('id', $id)->update([
            'price'            => round($total, 2),
            'employee_price'   => round($employee, 2),
            'material_price'   => round($material, 2),
            'asset_price'      => round($asset, 2),
            'employee_percent' => round($employee_percentage, 2),
            'material_percent' => round($material_percentage, 2),
            'asset_percent'    => round($asset_percentage, 2),
            'updated_at'       => now(),
        ]);

        return redirect()->back()->with('save_msg', 'Die Daten wurden aktualisiert');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductMasterSet $productMasterSet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Validation
        $validatedData = $request->validate([
            'id' => 'required|exists:product_master_sets,id',
            'setname' => 'required',
            'price' => 'nullable|numeric',
            'article_group' => 'required|exists:article_groups,id',
            'sub_article' => 'required|exists:sub_article_groups,id',
            'phase_id' => 'required|exists:task_phases,id',
        ]);

      
        // Retrieve the ProductMasterSet by ID
        $data = ProductMasterSet::findOrFail($validatedData['id']);
        
        // Update the ProductMasterSet
        $data->setname = $validatedData['setname'];
        $data->price = $validatedData['price'];
        $data->article_group = $validatedData['article_group'];
        $data->phase_id = $validatedData['phase_id'];
        $data->status = 'Published';
        
        // Save the updated data
        $data->save();

        // Redirect back with a success message
        return redirect()->back()->with('save_msg', 'Der Master-Satz wurde erfolgreich generiert');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = ProductMasterSet::find($id);
        $data->delete();

        AddProductToSet::where('master_set_id', $id)->delete();

        return redirect()->back()->with('save_msg', 'Der Master-Satz wurde erfolgreich gelöscht');
    }


    public function set($id, $phase)
{
    $search = request()->query('search');

    $data['product'] = Product::where('status', 'Published')->get();
    $data['measure'] = Measure::all();

    // Produkt-Set Titelinformationen laden
    $data['title'] = DB::table('product_master_sets')
        ->leftJoin('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
        ->leftJoin('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
        ->select(
            'product_master_sets.id as master_id',
            'sub_article_groups.sub_article',
            'article_groups.article_group',
            'product_master_sets.setname',
            'article_groups.id as article_group_id',
            'sub_article_groups.id as sub_id'
        )
        ->where('product_master_sets.id', $id)
        ->first();

   
    // Skills laden
    $data['employees'] = DB::table('employee_sets')
        ->join('positions', 'positions.id', '=', 'employee_sets.position_id')  
        ->leftJoin('article_groups', 'article_groups.id', '=', 'employee_sets.product_id')
        ->select('positions.position', 'article_groups.article_group', 'employee_sets.*')
        ->where('employee_sets.master_set_id', $id)
        ->get();

    // Produktbeschreibung
    $data['product_description'] = DB::table('product_set_descriptions')
        ->orderBy('id', 'asc')
        ->get();

    // Main Product Data
        $mainProductQuery = DB::table('add_product_to_sets')
        ->join('product_master_sets', 'product_master_sets.id', '=', 'add_product_to_sets.master_set_id')
        ->join('products', 'products.id', '=', 'add_product_to_sets.product_id')
        ->leftJoin('measures', 'measures.id', '=', 'add_product_to_sets.measure_unit')
        ->leftJoin('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
        ->where('add_product_to_sets.master_set_id', $id)
        ->select(
            'add_product_to_sets.*',
            'product_master_sets.setname',
            'products.product',
            'measures.measure',
            'article_groups.article_group',
            DB::raw('(add_product_to_sets.product_count * add_product_to_sets.purchase_price) as subtotal'),
            DB::raw('(add_product_to_sets.product_count * (add_product_to_sets.retail_price - add_product_to_sets.purchase_price)) as total_discount'),
            DB::raw('(add_product_to_sets.product_count * add_product_to_sets.retail_price) as total_value')
        );


        $data['data'] = $search
        ? $mainProductQuery->where('products.product', 'LIKE', "%$search%")->paginate(10)
        : $mainProductQuery->get();
 
 
    
    // Sub-Produkt-Zuordnungen nur bei leerer Suche
    if (!$search) {
        $data['subProducts'] = DB::table('product_sub_sets')
            ->join('add_product_to_sets', 'add_product_to_sets.id', '=', 'product_sub_sets.main_product')
            ->join('product_master_sets', 'product_master_sets.id', '=', 'add_product_to_sets.master_set_id')
            ->join('products as main_products', 'main_products.id', '=', 'add_product_to_sets.product_id')
            ->join('products as sub_products', 'sub_products.id', '=', 'product_sub_sets.product_id')
            ->leftJoin('measures', 'measures.id', '=', 'product_sub_sets.measure_unit')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->where('add_product_to_sets.master_set_id', $id)
            ->select(
                'product_sub_sets.*',
                'product_master_sets.setname',
                'main_products.product as main_product_name',
                'sub_products.product as sub_product_name',
                'measures.measure',
                'article_groups.article_group',
                DB::raw('(product_sub_sets.product_count * product_sub_sets.purchase_price) as subtotal'),
                DB::raw('(product_sub_sets.product_count * (product_sub_sets.retail_price - product_sub_sets.purchase_price)) as total_discount'),
                DB::raw('(product_sub_sets.product_count * product_sub_sets.retail_price) as total_value')
            )
            ->get();

    }

    // Aktivitäten
    $data['activity'] = DB::table('employee_activity_sets')
        ->join('employee_sets', 'employee_sets.id', '=', 'employee_activity_sets.employee_set_id')
        ->join('product_master_sets', 'product_master_sets.id', '=', 'employee_activity_sets.master_set_id')
        ->join('task_phases', 'task_phases.id', '=', 'employee_activity_sets.phase_id')
        ->join('phase_activities', 'phase_activities.id', '=', 'employee_activity_sets.activity_id')
        ->select('employee_activity_sets.*', 'phase_activities.title', 'phase_activities.description', 'phase_activities.status')
        ->where('phase_activities.status', 'Published')
        ->get();

    // Paragraphen
    $data['paragraph'] = DB::table('set_paragraphs')
        ->where('master_id', $id)
        ->get();


       // === Percentages from master set (add asset fields) ===
        $percentage = DB::table('product_master_sets')
            ->where('id', $id)
            ->select('employee_percent', 'material_percent', 'asset_percent', 'asset_price')
            ->first();

        // === Totals you already had ===
        $productTotal   = DB::table('add_product_to_sets')->where('master_set_id', $id)->sum('total');
        $subProductTotal= DB::table('product_sub_sets')->where('master_set_id', $id)->sum('total');
        $employeeTotal  = DB::table('employee_sets')->where('master_set_id', $id)->sum(DB::raw('work_hour * buying_price'));

        // === NEW: Asset total from asset_sets (decimal-safe) ===
        $assetTotal = DB::table('asset_sets')->where('master_id', $id)->sum('total_price');

        // === Expose to blade ===
        $data['employee_percent'] = $percentage->employee_percent ?? 0;
        $data['material_percent'] = $percentage->material_percent ?? 0;
        $data['asset_total']      = $assetTotal;

        // If asset_percent not stored, derive it from current totals (rounded, integer):
        $grand = ($productTotal + $subProductTotal + $employeeTotal + $assetTotal);
        $data['asset_percent'] = !is_null($percentage->asset_percent)
            ? (int) $percentage->asset_percent
            : ($grand > 0 ? (int) round(($assetTotal / $grand) * 100) : 0);

        // (Optional) keep these if you need them in JS for live % updates
        $data['main_product_total'] = $productTotal;
        $data['sub_product_total']  = $subProductTotal;
        $data['employee_total']     = $employeeTotal;


    // Bilder
    $imageQuery = DB::table('add_image_to_sets')
        ->join('product_images', 'product_images.product_id', '=', 'add_image_to_sets.product_id')
        ->select('add_image_to_sets.*', 'product_images.name', 'product_images.image');

    if ($search) {
        $data['images'] = $imageQuery
            ->join('products', 'products.id', '=', 'add_image_to_sets.product_id')
            ->where(function ($q) use ($search) {
                $q->where('products.product', 'LIKE', "%$search%")
                  ->orWhere('products.model', 'LIKE', "%$search%");
            })
            ->get();

        return view('admin.offer.set.products.product', $data);
    } else {
        $data['images'] = $imageQuery
            ->where('add_image_to_sets.master_set_id', $id)
            ->get();

        return view('admin.offer.set.sets.sets', $data);
    }
}


  
    public function clone($master_id)
{
    DB::beginTransaction(); // Start a transaction for rollback in case of failure

    try {
        // Step 1: Find the master set to clone
        $masterSet = ProductMasterSet::findOrFail($master_id);

        // Step 2: Clone the master set only once
        $clonedMasterSet = $masterSet->replicate();  // Clone the main set

        // Check if the setname already contains "(Copy)" to avoid appending it multiple times
        if (strpos($masterSet->setname, '(Copy)') === false) {
            $clonedMasterSet->setname = $masterSet->setname . ' (Copy)';
            $clonedMasterSet->product_parent_id = $master_id;
            $clonedMasterSet->product_parent_name = $masterSet->setname;  // Append "(Copy)" only once
        } else {
            $clonedMasterSet->setname = $masterSet->setname;  // Keep the name as is
        }

        $clonedMasterSet->save();  // Save the cloned master set

        // Log the cloning action just once
        \Log::info('ProductMasterSet cloned', [
            'original_master_set_id' => $masterSet->id,
            'cloned_master_set_id' => $clonedMasterSet->id,
        ]);

        // Step 3: Clone the related AddProductToSet records
        $productsInSet = AddProductToSet::where('master_set_id', $masterSet->id)->get();
        foreach ($productsInSet as $product) {
            $clonedProduct = $product->replicate();
            $clonedProduct->master_set_id = $clonedMasterSet->id;  // Assign new master set ID
            $clonedProduct->save();
        }

        // Step 4: Clone the related ProductSubSet records
        $subProducts = ProductSubSet::where('master_set_id', $masterSet->id)->get();
        foreach ($subProducts as $subProduct) {
            $clonedSubProduct = $subProduct->replicate();
            $clonedSubProduct->master_set_id = $clonedMasterSet->id;  // Assign new master set ID
            $clonedSubProduct->save();
        }

        // Step 5: Clone the related EmployeeSet records
        $employeeSets = EmployeeSet::where('master_set_id', $masterSet->id)->get();
        foreach ($employeeSets as $employeeSet) {
            $clonedEmployeeSet = $employeeSet->replicate();
            $clonedEmployeeSet->master_set_id = $clonedMasterSet->id;  // Assign new master set ID
            $clonedEmployeeSet->save();
        }

        // Step 6: Clone the related SetParagraph records
        $paragraphs = SetParagraph::where('master_id', $masterSet->id)->get();
        foreach ($paragraphs as $paragraph) {
            $clonedParagraph = $paragraph->replicate();
            $clonedParagraph->master_id = $clonedMasterSet->id;  // Assign new master set ID
            $clonedParagraph->save();
        }

        DB::commit(); // Commit the transaction

        return redirect()->route('master.set.view', $clonedMasterSet->id)->with('success', 'Set cloned successfully!');
    } catch (\Exception $e) {
        DB::rollback();  // Rollback transaction if something goes wrong
        return back()->with('error', 'Error cloning set: ' . $e->getMessage());
    }
}
 


public function delete_all(){
    $masterSet = ProductMasterSet::where('setname', 'LIKE', "%(Copy)%");
    $masterSet->delete();
    return redirect()->back()->with('delete_msg', 'the data is deleted');
}


public function refreshs($id)
{
    $master_set = ProductMasterSet::where('article_group', $id)->first();

    $employee = DB::table('employee_sets')->where('master_set_id', $id)->sum('total');
    $material = DB::table('add_product_to_sets')->where('master_set_id', $id)->sum('total');
    $total = $employee + $material;

    $employee_percentage = $total > 0 ? ($employee / $total) * 100 : 0;
    $material_percentage = $total > 0 ? ($material / $total) * 100 : 0;

    $master_set->price = $total;
    $master_set->employee_price = $employee;
    $master_set->material_price = $material;
    $master_set->employee_percent = $employee_percentage;
    $master_set->material_percent = $material_percentage;
    $master_set->save();

    return response()->json([
        'success' => true,
        'total' => number_format($total, 2),
        'employee_price' => number_format($employee, 2),
        'material_price' => number_format($material, 2),
        'employee_percent' => number_format($employee_percentage, 2),
        'material_percent' => number_format($material_percentage, 2),
    ]);
}

}
