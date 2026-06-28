<?php

namespace App\Http\Controllers;

use App\Models\Planing;
use Illuminate\Http\Request;
use DB;
use App\Models\LeadProductList;
use App\Models\Leads;
use App\Models\Project;
use App\Models\Deal;
use Illuminate\Support\Facades\Log;


class PlaningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {
        $search = request()->query('search');

        $query = DB::table('planings')
            ->join('new_leads', 'new_leads.id', '=', 'planings.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.lead_id', '=', 'planings.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'planings.product_id')
            ->join('employees', 'employees.id', '=', 'planings.employee_id') 
            ->select(
                'new_leads.title', 
                'new_leads.name', 
                'new_leads.lastname', 
                'new_leads.contact_person',
                'new_leads.email',
                'new_leads.phone', 
                'new_leads.telephone', 
                'alt.street', 
                'alt.postcode', 
                'alt.lat', 
                'alt.lon', 
                'alt.address_no', 
                'alt.city',
                'article_groups.article_group', 
                'article_groups.initial', 
                'planings.*',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image', 
                'employees.gender', 
            )
            ->where('planings.status', '!=', 'complete');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%$search%")
                ->orWhere('new_leads.lastname', 'LIKE', "%$search%")
                ->orWhere('alt.postcode', 'LIKE', "%$search%")
                ->orWhere('alt.city', 'LIKE', "%$search%")
                ->orWhere('article_groups.article_group', 'LIKE', "%$search%");
            });
        }

        $data['data'] = $query->paginate(19);

        return view('admin.planing.customer_view', $data);
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
    try {
        // Validate incoming request data
        $request->validate([
            'customer_id'  => 'required|exists:new_leads,id',
            'product_id'   => 'required|exists:article_groups,id',
            'employee_id'  => 'required|exists:employees,id',
            'service'      => 'nullable|string',
            'product_list' => 'required|exists:lead_product_lists,id'
        ]);

        // Save new planning record
        Planing::create([
            'customer_id'  => $request->customer_id,
            'employee_id'  => $request->employee_id,
            'product_id'   => $request->product_id,
            'service'      => $request->service ?? null,
            'status_msg'   => 'Nicht qualifiziert',
            'status'       => 'new'
        ]);

        // Update product list status
        $productList = LeadProductList::find($request->product_list);
        if ($productList) {
            $productList->status = "plan";
            $productList->save();
        }

        // Fetch all records for the customer
        $records = LeadProductList::where('customer_id', $request->customer_id)->get();

        if ($records->isNotEmpty()) {
            // Count records where status is 'plan'
            $plannedCount = $records->where('status', 'plan')->count();

            // Check if all records have status 'plan'
            $count_status = $plannedCount === $records->count() ? 'complete' : 'incomplete';

            if ($count_status === 'complete') {
                $lead = NewLead::find($request->customer_id); // Correct model name
                if ($lead) {
                    $lead->status = 'plan';
                    $lead->stage = 'plan';
                    $lead->save();
                }
            }
        }

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Der Kunde wurde erfolgreich zur Planung gesendet.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error'   => $e->getMessage()
        ], 500);
    }
}
 

    /**
     * Display the specified resource.
     */
    public function show(Planing $planing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Planing $planing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Planing $planing)
    {
        //
    }

   public function jump(Request $request)
    {
       
        // Validate incoming request
        $validate = $request->validate([
            'customer_id'   => 'required|exists:new_leads,id',
            'product_id'    => 'required|exists:article_groups,id',
            'employee_id'   => 'required|exists:employees,id',
            'alternative_id'=> 'required|exists:lead_alternative_adds,id',
            'service'       => 'required|string',
            'project_status'=> 'required|string|in:project,deals,offer' // Validate project_status
        ]);

        // Initialize $data based on project_status
        if ($request->project_status == 'project') {
            $data = new Project;
        } 
        else if($request->project_status == 'deals') {
            $data = new Deal;
        }
        else {
            // Handle other statuses if needed or throw an exception
            return redirect()->back()->with('delete_msg', 'Ungültiger Projektstatus.');
        }

        // Populate model fields
        $data->customer_id = $request->customer_id;
        $data->product_id = $request->product_id;
        $data->employee_id = $request->employee_id;
        $data->alternative_id = $request->alternative_id;
        $data->service = $request->service;
        $data->project_status = 'new';
        $data->status = 'new';
        $data->status_msg = 'Nicht qualifiziert';

        // Save the model and handle errors
        try {
            $data->save();
            return redirect()->back()->with(
                'save_msg', 
                __('Der Sprung wurde erfolgreich in der :phase phase gespeichert', [
                    'phase' => $request->project_status
                ])
            );
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error saving project:', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return redirect()->back()->with('delete_msg', 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
      public function destroy($id)
        { 
            $data=Planing::find($id);
            $data->delete(); 
            return back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
        }
        
           public function restore($id)
    {
        $data = Planing::withTrashed()->find($id);

        if ($data) {
            $data->restore(); // Restores the soft-deleted record
            return redirect()->back()->with('save_msg', 'Anfrage erfolgreich wiederhergestellt');
        }

        return redirect()->back()->with('error', 'Anfrage nicht gefunden');
    }
}
