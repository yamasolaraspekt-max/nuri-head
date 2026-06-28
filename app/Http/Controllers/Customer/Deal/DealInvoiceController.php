<?php

namespace App\Http\Controllers\Customer\Deal;
use App\Http\Controllers\Controller;

use App\Models\DealInvoice;
use Illuminate\Http\Request;
use DB;
use App\Models\Deal;

use Illuminate\Support\Facades\Validator;

class DealInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
        public function index()
    {
        $search   = request('search');
        $status   = request('status');
        $filter   = request('filter', 'my');
        $sortBy   = request('sort_by', 'deal_invoices.created_at');
        $sortDir  = request('sort_dir', 'desc');
        $authId   = auth()->user()->name;

        $query = DB::table('deal_invoices')
            ->join('deals', 'deal_invoices.deal_id', '=', 'deals.id')
            ->join('new_leads', 'new_leads.id', '=', 'deals.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'deals.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'deals.product_id')
            ->join('employees', 'employees.id', '=', 'deals.employee_id')
            ->leftJoin('employees as creator', 'creator.id', '=', 'deal_invoices.created_by')
            ->leftJoin('employees as reviewer', 'reviewer.id', '=', 'deal_invoices.reviewed_by')
            ->leftJoin('employees as checker', 'checker.id', '=', 'deal_invoices.checked_by')
            ->select(
                'deal_invoices.*',
                'deals.status as deal_status',
                'deals.sign_date',
                'deals.service',
                'deals.department_id',
                'new_leads.title',
                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',
                'new_leads.contact_person',
                'new_leads.email',
                'new_leads.phone',
                'new_leads.telephone',
                'alt.full_address',
                'alt.street',
                'alt.postcode',
                'alt.city',
                'alt.lat',
                'alt.lon',
                'article_groups.article_group',
                'article_groups.initial',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image',
                'employees.gender',
                'creator.name as created_by_name',
                'reviewer.name as reviewed_by_name',
                'checker.name as checked_by_name'
            )
            ->whereNull('deals.deleted_at')
            ->whereNotIn('deals.status', ['complete', 'Junk']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('new_leads.name', 'like', "%$search%")
                ->orWhere('new_leads.lastname', 'like', "%$search%")
                ->orWhere('alt.postcode', 'like', "%$search%")
                ->orWhere('alt.city', 'like', "%$search%")
                ->orWhere('article_groups.article_group', 'like', "%$search%")
                ->orWhere('deal_invoices.invoice_number', 'like', "%$search%");
            });
        }

        if ($status) {
            $query->where('deal_invoices.status', $status);
        }

        if ($filter === 'my') {
            $query->where(function ($q) use ($authId) {
                $q->where('deals.employee_id', $authId)
                ->orWhere('deals.checked_by', $authId)
                ->orWhere('deals.reviewer_id', $authId);
            });
        }

        $data = $query->orderBy($sortBy, $sortDir)
                    ->paginate(19)
                    ->appends(request()->only(['search', 'status', 'sort_by', 'sort_dir', 'filter']));

        $customers = DB::table('new_leads')
                ->join('deals', 'deals.customer_id', '=', 'new_leads.id')
                ->select('new_leads.id', 'new_leads.name', 'new_leads.lastname')
                ->get();
        $products = DB::table('article_groups')->select('id', 'article_group')->get();

        return view('admin.deal.invoice.invoice', compact('data', 'customers', 'products'));
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
        $messages = [
            'deal_id.required'        => 'Der Auftrag ist erforderlich.',
            'deal_id.exists'          => 'Der ausgewählte Auftrag ist ungültig.',
            'invoice_number.required' => 'Die Rechnungsnummer ist erforderlich.',
            'invoice_number.unique'   => 'Diese Rechnungsnummer existiert bereits.',
            'invoice_type.required'   => 'Der Rechnungstyp ist erforderlich.',
            'invoice_amount.required' => 'Der Rechnungsbetrag ist erforderlich.',
            'invoice_amount.numeric'  => 'Der Rechnungsbetrag muss eine Zahl sein.',
            'issued_at.required'      => 'Das Ausstellungsdatum ist erforderlich.',
            'due_date.required'       => 'Das Fälligkeitsdatum ist erforderlich.',
            'due_date.after_or_equal' => 'Das Fälligkeitsdatum muss nach dem Ausstellungsdatum liegen.',
        ];
    
        $validator = Validator::make($request->all(), [
            'deal_id'        => 'required|exists:deals,id',
            'invoice_number' => 'required|string|max:255|unique:deal_invoices,invoice_number',
            'invoice_type'   => 'required|string|max:255',
            'invoice_amount' => 'required|numeric|min:0',
            'issued_at'      => 'required|date',
            'due_date'       => 'required|date|after_or_equal:issued_at',
        ], $messages);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $duplicate = DealInvoice::where('deal_id', $request->deal_id)
            ->where('invoice_type', $request->invoice_type)
            ->where('invoice_number', $request->invoice_number)
            ->first();
    
        if ($duplicate) {
            return response()->json(['errors' => ['duplicate' => ['Diese Rechnung existiert bereits für diesen Auftragstyp.']]], 422);
        }
    
        $deal = Deal::findOrFail($request->deal_id);
    
        DealInvoice::create([
            'deal_id'        => $deal->id,
            'created_by'     => auth()->user()->name,
            'product_id'     => $request->product_id,
            'alternative_id' => $request->alternative_id,
            'department_id'  => $request->department_id,
            'employee_id'    => $request->employee_id,
            'service_id'     => $request->service_id,
            'invoice_number' => $request->invoice_number,
            'invoice_type'   => $request->invoice_type,
            'invoice_amount' => $request->invoice_amount,
            'paid_amount'    => 0,
            'open_amount'    => $request->invoice_amount,
            'issued_at'      => $request->issued_at,
            'due_date'       => $request->due_date,
            'status'         => 'open',
            'is_storno'      => $request->boolean('is_storno', false),
            'reminder_level' => $request->input('reminder_level', 0),
        ]);
    
        return response()->json(['success' => true, 'msg' => 'Rechnung erfolgreich erstellt.']);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(DealInvoice $dealInvoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DealInvoice $dealInvoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DealInvoice $dealInvoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DealInvoice $dealInvoice)
    {
        //
    }
}
