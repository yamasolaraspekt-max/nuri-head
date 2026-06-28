<?php

namespace App\Http\Controllers;

use App\Models\CustomerPhaseList;
use App\Models\Customer;
use App\Models\NewLeads;
use App\Models\Product;
use App\Models\ArticleGroup;
use Illuminate\Http\Request;
use DB;
use Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; 

class CustomerPhaseListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    
 
    public function create(Request $request){


        $validate = $request->validate([
            'alternative_id'  =>  'required|exists:lead_alternative_adds,id',
            'customer'  =>  'required|exists:new_leads,id',
            'product'  =>  'required|exists:article_groups,id',
            'service'  =>  'required|exists:lead_product_lists,service'

        ]);

        $check = DB::table('customer_phase_lists')
                            ->where('alternative', $request->alternative_id)
                            ->first();
     
        
           $item['customer'] = DB::table('new_leads')
                    ->join('lead_alternative_adds as alt', 'alt.lead_id', '=', 'new_leads.id')
                    ->select('new_leads.*',  
                            'alt.object_name',
                            'alt.postcode',
                            'alt.city',
                            'alt.id as alt_id'
                            )
                    ->where('alt.id', $request->alternative_id)
                    ->first();

                 $item['product'] = Product::all();
                 $product = $request->product;
                 $service = $request->service;


                $customer_id = $item['customer']->id;

                $productList = DB::table('lead_product_lists')
                            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
                            ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
                            ->select('lead_product_lists.*', 'article_groups.image', 'article_groups.article_group') 
                            ->where('new_leads.id', $request->customer) 
                            ->where('lead_product_lists.alternative_id', $request->alternative_id)
                            ->where('lead_product_lists.product_id', $product) 
                            ->first();
                
                $item['productList'] = $productList;  

                $item['task_phase'] = DB::table('task_phases')
                                    ->leftJoin('customer_phase_lists as list', function ($join) use ($product) {
                                        $join->on('list.phase_id', '=', 'task_phases.id')
                                            ->where('list.product', '=', $product);
                                    })
                                    ->where('task_phases.product_id', $product)
                                    ->where('task_phases.section_name', $service)
                                    ->whereNull('list.phase_id') // Ensures only task_phases with no entry in customer_phase_lists are selected
                                    ->select('task_phases.*')
                                    ->get();
    
     
            
        return view('admin.customer.phase_management.manage', $item);

    }

    /**
     * Store a newly created resource in storage.
     */
   
public function store(Request $request)
{ 
    Log::info('requested:', [$request->all()]);
    try {
        $request->validate([
            'customer' => 'required|integer|exists:new_leads,id',
            'phase_id' => 'required|array',
            'phase_id.*' => 'integer|exists:task_phases,id',
            'product' => 'required|integer|exists:article_groups,id',
            'alternative' => 'required|integer|exists:lead_alternative_adds,id',
            'service' => 'required|string|exists:task_phases,section_name',
            'status' => 'nullable|string',
            'color' => 'nullable|string',
            'active_by' => 'nullable|string',
            'jump_steps' => 'nullable|string',
            'jump_steps_by' => 'nullable|string',
        ]);

        foreach ($request->phase_id as $phaseId) {
            CustomerPhaseList::create([
                'customer' => $request->customer,
                'phase_id' => $phaseId,
                'product' => $request->product,
                'service' => $request->service,
                'alternative' => $request->alternative,
                'status' => 'Published',
                'color' => '#cfe09b',
                'active_by' => auth()->user()->name,
                'jump_steps' => null,
                'jump_steps_by' => null,
            ]);
        }

        return response()->json(['message' => 'Customer phase list saved successfully.'], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Format errors for Toastr
        $errors = $e->validator->errors()->all();
        return response()->json(['errors' => $errors], 422);
    }
}

public function getPhase($customer, $product, $service, $alternative)
{
    // Log incoming parameters
    Log::info('Request parameters:', compact('customer', 'product', 'service', 'alternative'));

    $query = DB::table('customer_phase_lists')
        ->join('new_leads', 'new_leads.id', '=', 'customer_phase_lists.customer')
        ->join('article_groups', 'article_groups.id', '=', 'customer_phase_lists.product')
        ->join('task_phases', 'task_phases.id', '=', 'customer_phase_lists.phase_id')
        ->select(
            'customer_phase_lists.id',
            'new_leads.name',
            'new_leads.lastname',
            'customer_phase_lists.service',
            'article_groups.article_group',
            'task_phases.phase_name',
            'customer_phase_lists.color'
        )
        ->where('customer_phase_lists.customer', $customer)
        ->where('customer_phase_lists.product', $product)
        ->where('customer_phase_lists.service', $service)
        ->where('customer_phase_lists.alternative', $alternative);

    $data = $query->get();

    return response()->json($data, 200);

}



    public function getPhaseNew($customer, $product, $service, $alternative)
    {
        // Exclude already saved phases for this customer, product, and service
        $savedPhases = DB::table('customer_phase_lists')
            ->where('customer', $customer)
            ->where('product', $product)
            ->where('service', $service)
            ->where('alternative', $alternative)
            ->pluck('phase_id');
     
        // Fetch unsaved phases
        $data = DB::table('task_phases')
            ->where('product_id', $product)
            ->where('section_name', $service) 
            ->whereNotIn('id', $savedPhases)
            ->select('id', 'phase_name')
            ->get();

        Log::info('remaining phases: ', [$data]);
        Log::info('saved phases: ', [$savedPhases]);
        return response()->json($data, 200);
    }

    public function deletePhase($id)
    {
        CustomerPhaseList::findOrFail($id)->delete();
        return response()->json(['message' => 'Customer phase list entry deleted successfully.'], 200);
    }

    public function color(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:customer_phase_lists,id',
            'color' => 'required|string',
        ]);

        $entry = CustomerPhaseList::findOrFail($request->id);
        $entry->color = $request->color;
        $entry->save();

        return response()->json(['message' => 'Color updated successfully.'], 200);
    }


    public function show(){

       $search = request()->query('search');

        // Base query with joins and select statement
        $query = DB::table('customer_phase_lists')
            ->join('new_leads', 'new_leads.id', '=', 'customer_phase_lists.customer')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'customer_phase_lists.alternative') // Correct join on `alt.id`
            ->join('article_groups', 'article_groups.id', '=', 'customer_phase_lists.product')
            ->select(
                'new_leads.id',
                'new_leads.name',
                'new_leads.lastname',
                'alt.street',
                'alt.postcode',
                'alt.address_no',
                'alt.city',
                'article_groups.article_group',
                'article_groups.id as product',
                'alt.id as alt_id', // Use the correct `alt_id` from `lead_alternative_adds`
                'customer_phase_lists.service',
                DB::raw('GROUP_CONCAT(customer_phase_lists.phase_id) as phases') // Aggregate `phase_id`
            )
            ->groupBy(
                'new_leads.id',
                'new_leads.name',
                'new_leads.lastname',
                'alt.street',
                'alt.postcode',
                'alt.address_no',
                'alt.city',
                'article_groups.article_group',
                'article_groups.id',
                'alt.id',
                'customer_phase_lists.service'
            );

        // Apply search filters if $search is present
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%$search%")
                    ->orWhere('new_leads.id', 'LIKE', "%$search%")
                    ->orWhere('new_leads.lastname', 'LIKE', "%$search%")
                    ->orWhere('alt.postcode', 'LIKE', "%$search%")
                    ->orWhere('article_groups.article_group', 'LIKE', "%$search%");
            });
        }

        // Execute the query with pagination
        $data['data'] = $query->paginate(30);



        $data['customers'] = DB::table('new_leads')
                                ->join('lead_alternative_adds as alt', 'alt.lead_id', '=', 'new_leads.id')
                                ->leftJoin('lead_product_lists as list', 'list.alternative_id', 'alt.id')
                                ->leftJoin('article_groups', 'article_groups.id', '=', 'list.product_id')
                                ->select('new_leads.*',  
                                        'alt.object_name',
                                        'alt.postcode',
                                        'alt.city',
                                        'alt.id as alt_id',
                                        'article_groups.article_group',
                                        'article_groups.id as product',
                                        'list.service as service',
                                        )
                                ->get();
  
         

        // Fetch task phases
        $data['task_phase'] = DB::table('task_phases')
            ->where('status', '=', 'Published')
            ->get();

        return view('admin.customer.phase_management.customer_phase', $data);

       
    }
}
