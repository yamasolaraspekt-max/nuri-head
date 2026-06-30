<?php

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;

use App\Models\Brand;
use App\Models\Customer;
use App\Models\NewLeads;
use App\Models\Distributor;
use App\Models\Employee;
use App\Models\Measure;
use App\Models\Problem;
use App\Models\PurchaseRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $brands = Brand::select('id', 'name', 'image', 'status')
            ->orderBy('name')
            ->get();

        $distributors = Distributor::select('id', 'name', 'image', 'status')
            ->orderBy('name')
            ->get();

        $measures = Measure::select('id', 'measure')
            ->orderBy('measure')
            ->get();

        $employees = Employee::select('id', 'name', 'lastname')
            ->orderBy('name')
            ->get();

        $customers = NewLeads::select('id', 'name', 'lastname')
            ->orderBy('name')
            ->get();

        $problems = Problem::query()
            ->leftJoin('new_leads', 'new_leads.id', '=', 'problems.customer_id')
            ->leftJoin('products', 'products.id', '=', 'problems.product_id')
            ->select([
                'problems.id',
                'problems.ticket_no',
                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',
                'products.product as product_name',
            ])
            ->orderByDesc('problems.id')
            ->get();

        return view('admin.product.inventory.purchase_request.index', compact(
            'brands',
            'distributors',
            'measures',
            'employees',
            'customers',
            'problems'
        ));
    }

    public function create()
    {
        return redirect()->route('purchase.request');
    }

    public function analytics(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'analytics' => [
                'total' => PurchaseRequest::count(),
                'today' => PurchaseRequest::whereDate('created_at', today())->count(),
                'published' => PurchaseRequest::where('status', 'Published')->count(),
                'unpublished' => PurchaseRequest::where('status', 'Unpublished')->count(),
                'with_image' => PurchaseRequest::whereNotNull('image')
                    ->where('image', '!=', '')
                    ->count(),
            ],
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $search = trim((string) $request->get('search'));
        $status = $request->get('status');
        $used = $request->get('used');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = [
            'id' => 'purchase_requests.id',
            'product' => 'purchase_requests.product',
            'model' => 'purchase_requests.model',
            'quantity' => 'purchase_requests.quantity',
            'status' => 'purchase_requests.status',
            'created_at' => 'purchase_requests.created_at',
            'add_date' => 'purchase_requests.add_date',
        ];

        $sortColumn = $allowedSorts[$sort] ?? 'purchase_requests.created_at';
        $direction = in_array(strtolower($direction), ['asc', 'desc'], true) ? $direction : 'desc';

        $query = PurchaseRequest::query()
            ->leftJoin('brands', 'brands.id', '=', 'purchase_requests.brand')
            ->leftJoin('distributors', 'distributors.id', '=', 'purchase_requests.distributor_id')
            ->leftJoin('employees as requestf', 'requestf.id', '=', 'purchase_requests.request_from')
            ->leftJoin('employees as requestt', 'requestt.id', '=', 'purchase_requests.request_to')
            ->leftJoin('new_leads', 'new_leads.id', '=', 'purchase_requests.customer_id')
            ->leftJoin('employees as used_employee', 'used_employee.id', '=', 'purchase_requests.employee_id')
            ->select([
                'purchase_requests.*',

                DB::raw('COALESCE(purchase_requests.new_brand, brands.name) as brand_name'),
                DB::raw('COALESCE(purchase_requests.new_distributor, distributors.name) as distributor_name'),

                'requestf.name as requestf_name',
                'requestf.lastname as requestf_lastname',
                'requestt.name as requestt_name',
                'requestt.lastname as requestt_lastname',

                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',

                'used_employee.name as used_employee_name',
                'used_employee.lastname as used_employee_lastname',
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_requests.product', 'LIKE', "%{$search}%")
                    ->orWhere('purchase_requests.model', 'LIKE', "%{$search}%")
                    ->orWhere('purchase_requests.new_brand', 'LIKE', "%{$search}%")
                    ->orWhere('purchase_requests.new_distributor', 'LIKE', "%{$search}%")
                    ->orWhere('brands.name', 'LIKE', "%{$search}%")
                    ->orWhere('distributors.name', 'LIKE', "%{$search}%")
                    ->orWhere('purchase_requests.status', 'LIKE', "%{$search}%")
                    ->orWhere('purchase_requests.used', 'LIKE', "%{$search}%")
                    ->orWhere('requestf.name', 'LIKE', "%{$search}%")
                    ->orWhere('requestf.lastname', 'LIKE', "%{$search}%")
                    ->orWhere('requestt.name', 'LIKE', "%{$search}%")
                    ->orWhere('requestt.lastname', 'LIKE', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('purchase_requests.status', $status);
        }

        if ($used) {
            $query->where('purchase_requests.used', $used);
        }

        $data = $query
            ->orderBy($sortColumn, $direction)
            ->paginate((int) $request->get('per_page', 10));

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'brand_id' => ['required'],
            'new_brand' => ['nullable', 'string', 'max:255'],
            'distributor' => ['required'],
            'new_distributor' => ['nullable', 'string', 'max:255'],

            'product' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],

            'used' => ['required', 'in:Kunden,Mitarbeiter,Problem'],
            'customer_id' => ['nullable'],
            'employee_id' => ['nullable'],
            'problem_id' => ['nullable'],

            'measure_unit' => ['nullable'],
            'price_unit' => ['nullable', 'string', 'max:255'],

            'retail_price' => ['nullable', 'numeric'],
            'retail_discount_type' => ['nullable', 'in:Percent,Euro'],
            'retail_discount_p' => ['nullable', 'numeric'],
            'retail_discount_e' => ['nullable', 'numeric'],
            'purchase_price' => ['nullable', 'numeric'],
            'quantity' => ['nullable', 'numeric'],

            'request_from' => ['nullable', 'exists:employees,id'],
            'request_to' => ['nullable', 'exists:employees,id'],

            'link' => ['nullable', 'string', 'max:2000'],
            'editor_text' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Bitte prüfen Sie Ihre Eingaben.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $brand = $request->brand_id === 'new' ? null : $request->brand_id;
            $newBrand = $request->brand_id === 'new' ? $request->new_brand : null;

            $distributor = $request->distributor === 'new' ? null : $request->distributor;
            $newDistributor = $request->distributor === 'new' ? $request->new_distributor : null;

            $customer = null;
            $employee = null;
            $problem = null;

            if ($request->used === 'Kunden') {
                $customer = $request->customer_id ?: null;
            }

            if ($request->used === 'Mitarbeiter') {
                $employee = $request->employee_id ?: null;
            }

            if ($request->used === 'Problem') {
                $problem = $request->problem_id ?: null;
            }

            $retailDiscount = $request->retail_discount_type === 'Percent'
                ? $request->retail_discount_p
                : $request->retail_discount_e;

            $imageName = null;

            if ($request->hasFile('image')) {
                $imageName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();

                if (!is_dir(public_path('images/purchase'))) {
                    mkdir(public_path('images/purchase'), 0775, true);
                }

                $request->file('image')->move(public_path('images/purchase'), $imageName);
            }

            $item = PurchaseRequest::create([
                'brand' => $brand,
                'new_brand' => $newBrand,
                'distributor_id' => $distributor,
                'new_distributor' => $newDistributor,
                'product' => $request->product,
                'model' => $request->model,
                'color' => $request->color,
                'customer_id' => $customer,
                'problem_id' => $problem,
                'employee_id' => $employee,
                'quantity' => $request->quantity,
                'measure_unit' => $request->measure_unit,
                'price_unit' => $request->price_unit,
                'retail_price' => $request->retail_price,
                'retail_discount_type' => $request->retail_discount_type,
                'retail_discount' => $retailDiscount,
                'purchase_price' => $request->purchase_price,
                'request_from' => $request->request_from,
                'request_to' => $request->request_to,
                'short_description' => $request->editor_text,
                'used' => $request->used,
                'link' => $request->link,
                'image' => $imageName,
                'status' => 'Unpublished',
                'add_by' => auth()->user()->name ?? 'System',
                'add_date' => Carbon::now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Kaufanfrage wurde erfolgreich gespeichert.',
                'item' => $item,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'status' => false,
                'message' => 'Fehler beim Speichern der Kaufanfrage.',
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        $item = PurchaseRequest::query()
            ->leftJoin('brands', 'brands.id', '=', 'purchase_requests.brand')
            ->leftJoin('distributors', 'distributors.id', '=', 'purchase_requests.distributor_id')
            ->leftJoin('employees as requestf', 'requestf.id', '=', 'purchase_requests.request_from')
            ->leftJoin('employees as requestt', 'requestt.id', '=', 'purchase_requests.request_to')
            ->leftJoin('new_leads', 'new_leads.id', '=', 'purchase_requests.customer_id')
            ->leftJoin('employees as used_employee', 'used_employee.id', '=', 'purchase_requests.employee_id')
            ->select([
                'purchase_requests.*',

                DB::raw('COALESCE(purchase_requests.new_brand, brands.name) as brand_name'),
                DB::raw('COALESCE(purchase_requests.new_distributor, distributors.name) as distributor_name'),

                'requestf.name as requestf_name',
                'requestf.lastname as requestf_lastname',
                'requestt.name as requestt_name',
                'requestt.lastname as requestt_lastname',

                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',

                'used_employee.name as used_employee_name',
                'used_employee.lastname as used_employee_lastname',
            ])
            ->where('purchase_requests.id', $id)
            ->first();

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Kaufanfrage wurde nicht gefunden.',
            ], 404);
        }

        $item->image_url = $item->image ? asset('images/purchase/' . $item->image) : null;

        return response()->json([
            'status' => true,
            'item' => $item,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $item = PurchaseRequest::find($id);

        if (!$item) {
            return response()->json([
                'status' => false,
                'message' => 'Datensatz wurde nicht gefunden.',
            ], 404);
        }

        if ($item->image) {
            $path = public_path('images/purchase/' . $item->image);

            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $item->delete();

        return response()->json([
            'status' => true,
            'message' => 'Kaufanfrage wurde gelöscht.',
        ]);
    }
}