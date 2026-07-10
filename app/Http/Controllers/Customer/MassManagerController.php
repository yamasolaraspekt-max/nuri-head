<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewLeads;
use App\Models\LeadProductList;
use App\Models\ArticleGroup;
use App\Models\Department;
use App\Models\Employee;
use App\Models\PhaseSection;

class MassManagerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,delete')->only(['delete']);
    }

    // 1. Load Data
   public function load(Request $request)
    {
        // FIX: Use 'alternativeAddresses' (matches your Model) instead of 'alternatives'
        $query = NewLeads::query()->with([
            'alternativeAddresses' => function($q) {
                // Eager load nested relationships
                $q->with([
                    'products.articleGroup',
                    'products.department',
                    'products.employee',
                    'products.fieldEmployee',
                    'products.service'
                ]);
            }
        ]);

        // --- FILTERS ---
        if ($request->source) {
            $query->where('source', 'LIKE', "%{$request->source}%");
        }

        if ($request->search) {
            $term = $request->search;
            $query->where(function($q) use ($term) {
                $q->where('name', 'LIKE', "%$term%")
                  ->orWhere('lastname', 'LIKE', "%$term%")
                  ->orWhere('city', 'LIKE', "%$term%");
            });
        }

        // Filter by Product presence inside the objects
        if ($request->product_id) {
            $query->whereHas('alternativeAddresses.products', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        // --- COUNT & LIMIT ---
        // 1. Get total number of matching customers (e.g., 901)
        $totalCount = $query->count(); 

        // 2. Get the actual data (Limit to 50 to prevent browser crash)
        $customers = $query->latest()->limit(50)->get();

        // Pass Data to View
        $data = [
            'total_count' => $totalCount,
            'customers'   => $customers,
            'products'    => ArticleGroup::select('id', 'article_group', 'image')->get(), // Added image
            'departments' => Department::select('id', 'department_name')->get(),
            // Pass ALL services for JS filtering
            'services'    => PhaseSection::select('id', 'phase_section', 'product_id')->get(), 
            // We don't strictly need to pass all employees here anymore, 
            // because they will be loaded via AJAX based on the product.
        ];

        return view('admin.new_leads.partials.mass_manager_rows', $data);
    }
    // 2. Auto Suggest Logic
    public function suggest(Request $request)
    {
        $pid = $request->product_id;

        // Logic: Find related service/department for this product
        $service = PhaseSection::where('product_id', $pid)->first();
        // You can add more complex logic here (e.g. check ProductPositions table)
        
        // Find default employees if applicable, or department head
        $dept = null;
        if($service) {
             // Example: find dept based on logic
             // $dept = ...
        }

        return response()->json([
            'service_id' => $service ? $service->id : null,
            'service_name' => $service ? $service->phase_section : null,
            // Add more auto-fills here
        ]);
    }

    // 3. Store
    public function store(Request $request)
    {
        // Simple Validation
        if(!$request->product_id || !$request->alternative_id) {
            return response()->json(['error' => 'Fehlende Daten'], 400);
        }

        $item = new LeadProductList();
        if($request->id) {
            $item = LeadProductList::find($request->id); // Update mode
        }

        $item->customer_id = $request->customer_id;
        $item->alternative_id = $request->alternative_id;
        $item->product_id = $request->product_id;
        $item->department_id = $request->department_id;
        $item->service_id = $request->service_id;
        $item->employee_id = $request->employee_id;
        $item->field_employee = $request->field_employee;
        $item->interest = $request->interest;
        $item->realization_time = $request->realization_time;
        $item->status = 'archive';
        $item->save();

        return response()->json(['success' => true, 'id' => $item->id]);
    }

    // 4. Delete
    public function delete($id)
    {
        LeadProductList::destroy($id);
        return response()->json(['success' => true]);
    }
}