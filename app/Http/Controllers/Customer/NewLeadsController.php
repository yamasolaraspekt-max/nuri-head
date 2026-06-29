<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use App\Models\NewLeads;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ArticleGroup;
use App\Models\NewLeadImage;
use App\Models\LeadProductList;
use App\Models\LeadAlternativeAdd;
use App\Models\CustomerAlternativeAdd;
use App\Models\Leave;
use App\Models\JobRepresentative;
use App\Models\CustomerResponsible;
use App\Models\NewLeadResponsibility;
use App\Models\Image;
use App\Models\CustomerProductList;
use App\Models\Department;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\ImageCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Inquiry;
use App\Models\Planing;
use Illuminate\Support\Facades\Validator;
use App\Models\TaskPhase;
use App\Models\PhaseActivities;
use App\Models\RadiatorInstallation;
use App\Models\CustomerHeatingCircuit;
use App\Models\CustomerRoomDimension;
use App\Notifications\LeadNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\PersonalTask;
use App\Models\PersonalTaskKey;
use App\Notifications\PersonalTaskNotification;
use App\Notifications\LeadResponsibleChange;
use App\Models\EmployeesPersonalTask;
use App\Models\HeatingType;
use App\Models\SubArticleGroup;
use App\Models\CustomerCart;
use App\Models\PVChecklist;
use App\Models\BegFundings;
use App\Models\PVRoof;
use App\Models\CustomerHistory;
use App\Models\OfferFolder;
use App\Models\CustomerStage;
use App\Models\PhaseSection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use App\Models\Stage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\TimeSummary;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\Offer;
use App\Models\LeadAlternativePvWpDetail;
use App\Models\Invoice;
use App\Models\Problem;
use App\Models\MainAppointment;
use App\Models\CustomerProductInfo;
use App\Models\InvoiceItem;
use App\Models\CustomerReview;
use App\Models\Deal;
use App\Models\DealNote;
use App\Models\DealMeasurement;
use App\Models\DeliveryNote;
use Symfony\Component\HttpFoundation\Response;
use App\Models\LeadStage;
use Illuminate\Support\Arr;
class NewLeadsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function new_object($id)
    {
        $data['leads'] = NewLeads::find($id);
        $data['employee'] = Employee::all();
        $data['product'] = Product::all();
        $data['articles'] = ArticleGroup::all();
        $data['category'] = DB::table('image_categories')->select('*')->get();
        $data['branch'] = DB::table('branches')->where('status', '=', 'Published')->get();

        $data['positions'] = DB::table('department_positions')
            ->join('departments', 'departments.id', '=', 'department_positions.department_id')
            ->join('positions', 'positions.id', '=', 'department_positions.position_id')
            ->select(
                'positions.position',
                'departments.department_name',
                'positions.id as position_id',
                'departments.id as dept_id'
            )
            ->get();

        $employees = DB::table('employees')
            ->select('employees.id', 'employees.name', 'employees.lastname', 'employees.image', 'employees.gender')
            ->get();

        // Process employees on leave and their representatives
        $employeeIds = $employees->pluck('id')->toArray();
        $leave_check = DB::table('leaves')
            ->join('employees', 'employees.id', '=', 'leaves.emp_id')
            ->whereIn('employees.id', $employeeIds)
            ->where('leaves.approved', '=', 'Yes')
            ->where(function ($query) {
                $query->where('leaves.start_date', '<=', Carbon::now())
                    ->where('leaves.end_date', '>=', Carbon::now());
            })
            ->pluck('emp_id')
            ->toArray();

        $representer_check = [];

        if (!empty($leave_check)) {
            $representer_check = DB::table('job_representatives')
                ->join('employees', 'employees.id', '=', 'job_representatives.employee_id')
                ->join('departments', 'departments.id', '=', 'job_representatives.department_id')
                ->join('positions', 'positions.id', '=', 'job_representatives.position_id')
                ->join('employees as representer', 'representer.id', '=', 'job_representatives.representer_id')
                ->join('employees as current', 'current.id', '=', 'job_representatives.current_representer')
                ->select(
                    'job_representatives.*',
                    'departments.department_name',
                    'positions.position',
                    'employees.name',
                    'employees.lastname',
                    'representer.name as represent_name',
                    'representer.lastname as represent_lastname',
                    'current.name as current_name',
                    'current.lastname as current_lastname'
                )
                ->whereIn('job_representatives.employee_id', $leave_check)
                ->get();
        }

        $availableEmployees = $employees->map(function ($employee) use ($leave_check, $representer_check) {
            if (in_array($employee->id, $leave_check)) {
                $representative = $representer_check->firstWhere('employee_id', $employee->id);
                if ($representative) {
                    $employee->id = $representative->representer_id;
                    $employee->name = $representative->represent_name;
                    $employee->lastname = $representative->represent_lastname;
                } else {
                    return null;
                }
            }
            return $employee;
        })->filter();

        $data['employees'] = $availableEmployees->toArray();


        $data['employees'] = Employee::select('id', 'name', 'lastname', 'image', 'gender')->get();
        $data['departments'] = Department::where('status', '=', 'published')->get();
        $data['products'] = DB::table('article_groups')->get();
        $data['services'] = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section')
            ->whereNull('deleted_at')
            ->get();


        return view('admin.new_leads.object', $data);
    }


    public function create(Request $request)
    {
        // Check for flash data passed from previous redirect
        $flashData = session('data'); // The 'data' key sent with ->with('data', $data)
        $saveMessage = session('save_msg'); // The 'save_msg' key sent with ->with('save_msg', 'Message')

        // Add the flashed data to $data if it exists
        $data = [];
        $data['inquiry'] = $flashData ?? [];
        $data['save_msg'] = $saveMessage;


        // Populate other data as required
        $data['employee'] = Employee::all();
        $data['product'] = Product::all();
        $data['articles'] = ArticleGroup::all();
        $data['category'] = DB::table('image_categories')->select('*')->get();
        $data['branch'] = DB::table('branches')->where('status', '=', 'Published')->get();

        $data['positions'] = DB::table('department_positions')
            ->join('departments', 'departments.id', '=', 'department_positions.department_id')
            ->join('positions', 'positions.id', '=', 'department_positions.position_id')
            ->select(
                'positions.position',
                'departments.department_name',
                'positions.id as position_id',
                'departments.id as dept_id'
            )
            ->get();

        $data['employees'] = Employee::select('id', 'name', 'lastname', 'image')->get();
        $data['departments'] = Department::where('status', '=', 'published')->get();
        $data['products'] = DB::table('article_groups')->get();
        $data['services'] = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section')
            ->whereNull('deleted_at')
            ->get();


        return view('admin.new_leads.customer', $data);
    }


    public function edit($id, $alternative)
    {
        $data['employee'] = Employee::all();
        $data['product'] = Product::all();
        $data['articles'] = ArticleGroup::all();
        $data['category'] = DB::table('image_categories')->select('*')->get();
        $data['branch'] = DB::table('branches')->where('status', '=', 'Published')->get();



        $data['positions'] = DB::table('department_positions')
            ->join('departments', 'departments.id', '=', 'department_positions.department_id')
            ->join('positions', 'positions.id', '=', 'department_positions.position_id')
            ->select('positions.position', 'departments.department_name', 'positions.id as position_id', 'departments.id as dept_id')
            ->get();


        $data['leads'] = NewLeads::find($id);
        $data['alternative'] = DB::table('lead_alternative_adds')->where('lead_id', $id)->where('id', $alternative)->first();

        $data['responsible'] = DB::table('new_lead_responsibilities')
            ->select('new_lead_responsibilities.new_lead_id', 'new_lead_responsibilities.current_employee', 'new_lead_responsibilities.product_id')
            ->where('new_lead_responsibilities.new_lead_id', $id)
            ->get();
        $data['product_list'] = DB::table('lead_product_lists')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('employees', 'employees.id', '=', 'lead_product_lists.employee_id')
            ->leftJoin('employees as fe', 'fe.id', '=', 'lead_product_lists.field_employee') // 👈 added
            ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->select(
                'departments.department_name',
                'employees.name as ename',
                'employees.lastname as elastname',
                'employees.image as eimage',
                'fe.name as fename',
                'fe.lastname as felastname',
                'fe.image as feimage',
                'article_groups.article_group',
                'article_groups.image as pimage',
                'phase_sections.phase_section',
                'lead_product_lists.*'
            )
            ->where('lead_product_lists.customer_id', $id)
            ->where('lead_product_lists.alternative_id', $alternative)
            ->get();

        $this->hydrateLeadProductTeams($data['product_list']);


        $data['employees'] = Employee::select('id', 'name', 'lastname', 'image', 'gender')->get();
        $data['departments'] = Department::where('status', '=', 'published')->get();
        $data['products'] = DB::table('article_groups')->get();
        $data['services'] = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section')
            ->whereNull('deleted_at')
            ->get();




        return view('admin.new_leads.customer_edit', $data)->with('id', $id);
    }

    public function details_edit($id)
    {
        $data = NewLeads::find($id);

        return view('admin.new_leads.customer_details_edit')->with('id', $id)->with('data', $data);
    }

    public function getResponsible($customer, $alternative)
    {
        // Fetch the responsible data
        $responsible = DB::table('new_lead_responsibilities')
            ->join('article_groups', 'article_groups.id', '=', 'new_lead_responsibilities.product_id')
            ->join('employees', 'employees.id', '=', 'new_lead_responsibilities.employee_id')
            ->select(
                'new_lead_responsibilities.new_lead_id',
                'new_lead_responsibilities.current_employee',
                'new_lead_responsibilities.product_id',
                'employees.image as employee_image',
                'employees.name',
                'employees.lastname',
                'article_groups.article_group',
                'article_groups.initial',
                'article_groups.image as product_image'
            )
            ->where('new_lead_responsibilities.new_lead_id', $customer)
            ->where('new_lead_responsibilities.alternative_id', $alternative)
            ->get();

        // Fetch the product list
        $product_list = DB::table('lead_product_lists')
            ->where('customer_id', $customer)
            ->where('alternative_id', $alternative)
            ->where('status', '!=', 'plan')
            ->get();

        $this->hydrateLeadProductTeams($product_list);
        // Merge the data
        $mergedData = $product_list->map(function ($product) use ($responsible) {
            // Find the matching responsible entry for this product
            $responsibility = $responsible->firstWhere('product_id', $product->product_id);

            return [
                'id' => $product->id,
                'customer_id' => $product->customer_id,
                'alternative_id' => $product->alternative_id,
                'product_id' => $product->product_id,
                'service' => $product->service,
                'status' => $product->status,

                'teams' => $product->teams ?? [],
                'team_members' => $product->team_members ?? [],
                'team_assignments' => $product->team_assignments ?? [],

                'current_employee' => $responsibility->current_employee ?? null,
                'employee_image' => $responsibility->employee_image ?? null,
                'employee_name' => $responsibility ? $responsibility->name . ' ' . $responsibility->lastname : null,
                'product_image' => $responsibility->product_image ?? null,
                'article_group' => $responsibility->article_group ?? null,
            ];
        });

        // Log the query results for debugging
        \Log::info('Merged Data:', $mergedData->toArray());

        return response()->json($mergedData, 200);
    }



    public function product_list(Request $request)
    {
        // Retrieve product_id from the request
        $product_id = $request->product_id;

        // Fetch the product details based on the product_id
        $product_list = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select('lead_product_lists.product_id', 'article_groups.article_group', 'article_groups.image')
            ->where('article_groups.id', '=', $product_id)
            ->get();

        // Fetch employees associated with the specific product
        $selectedEmployees = DB::table('new_lead_responsibilities')
            ->join('new_leads', 'new_leads.id', '=', 'new_lead_responsibilities.new_lead_id')
            ->join('employees', 'employees.id', '=', 'new_lead_responsibilities.employee_id')
            // FIX P1: Positions-Joins korrigiert - 'positions' hat keine Spalte department_id und
            // 'product_positions' keine position_id; Mitarbeiter->Funktion laeuft ueber department_positions.
            // (Vorher: leftJoin auf positions.department_id + product_positions.position_id -> Unknown column.)
            ->leftJoin('department_positions', 'department_positions.employee_id', '=', 'employees.id')
            ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
            ->join('article_groups', 'article_groups.id', '=', 'new_lead_responsibilities.product_id')
            ->select('employees.id', 'employees.name', 'employees.lastname', 'employees.image', 'positions.position')
            ->where('new_lead_responsibilities.product_id', '=', $product_id)
            ->get();

        // Fetch all employees
        $allEmployees = DB::table('employees')
            // FIX P1: 'position' ist keine employees-Spalte (Funktion via department_positions) -> leerer Platzhalter,
            // damit die View ($employee->position) nicht bricht.
            ->select('id', 'name', 'lastname', 'image', DB::raw("'' as position"))
            ->get();

        // Filter out selected employees from all employees
        $unselectedEmployees = $allEmployees->filter(function ($employee) use ($selectedEmployees) {
            return !$selectedEmployees->contains('id', $employee->id);
        });

        return view('admin.new_leads.customer_edit', [
            'product_list' => $product_list,
            'selectedEmployees' => $selectedEmployees,
            'unselectedEmployees' => $unselectedEmployees,
            'allEmployees' => $allEmployees, // Ensure this is passed to the view
        ]);
    }


    public function searchEmployees(Request $request)
    {
        $query = $request->input('query');

        $employeesQuery = DB::table('employee_departments')
            ->join('employees', 'employees.id', '=', 'employee_departments.employee_id')
            ->join('departments', 'departments.id', '=', 'employee_departments.department_id')
            ->join('department_positions', 'departments.id', '=', 'department_positions.department_id')
            ->join('positions', 'positions.id', '=', 'department_positions.position_id')
            ->select('employees.id as emp_id', 'employees.name', 'employees.lastname', 'employees.image', 'departments.department_name', 'positions.position')
            ->where('employees.status', '=', 'Active')
            ->where('departments.status', '=', 'Published');

        if (!empty($query)) {
            $employeesQuery->where(function ($queryBuilder) use ($query) {
                $queryBuilder->where('employees.name', 'like', '%' . $query . '%')
                    ->orWhere('employees.lastname', 'like', '%' . $query . '%')
                    ->orWhere('positions.position', 'like', '%' . $query . '%')
                    ->orWhere('departments.department_name', 'like', '%' . $query . '%');
            });
        }

        $employees = $employeesQuery->distinct()->get();


        return response()->json($employees);
    }


    public function checkEmployeeAvailability(Request $request)
    {
        $productId = $request->input('product_id');
        \Log::info('Received product ID: ' . $productId);

        // Fetch the article group for the card title
        $card_title = ArticleGroup::where('id', $productId)->select('article_group')->first();
        $card_title = $card_title ? $card_title->article_group : 'Unknown Group';



        // Find employees in the same department and position
        $employees = DB::table('employees')
            ->where('status', 'active')
            ->get();


        // Check if employees are on leave
        $employeeIds = $employees->pluck('id')->toArray();
        $leave_check = DB::table('leaves')
            ->join('employees', 'employees.id', '=', 'leaves.emp_id')
            ->whereIn('employees.id', $employeeIds)
            ->where('leaves.approved', '=', 'Yes')
            ->where(function ($query) {
                $query->where('leaves.start_date', '<=', Carbon::now())
                    ->where('leaves.end_date', '>=', Carbon::now());
            })
            ->pluck('emp_id')
            ->toArray();

        $representer_check = [];

        if (!empty($leave_check)) {
            $leave_employee_id = $leave_check;
            \Log::debug('Leave Employee IDs: ' . json_encode($leave_employee_id));

            // Fetch representatives for employees on leave
            $representer_check = DB::table('job_representatives')
                ->join('employees', 'employees.id', '=', 'job_representatives.employee_id')
                ->join('departments', 'departments.id', '=', 'job_representatives.department_id')
                ->join('positions', 'positions.id', '=', 'job_representatives.position_id')
                ->join('employees as representer', 'representer.id', '=', 'job_representatives.representer_id')
                ->join('employees as current', 'current.id', '=', 'job_representatives.current_representer')
                ->select(
                    'job_representatives.*',
                    'departments.department_name',
                    'positions.position',
                    'employees.name',
                    'employees.lastname',
                    'representer.name as represent_name',
                    'representer.lastname as represent_lastname',
                    'current.name as current_name',
                    'current.lastname as current_lastname'
                )
                ->whereIn('job_representatives.employee_id', $leave_employee_id)
                ->get();

            \Log::debug('Representer Check: ' . json_encode($representer_check));
        }

        // Filter out employees who are on leave and replace them with representatives if available
        $availableEmployees = $employees->map(function ($employee) use ($leave_check, $representer_check) {
            if (in_array($employee->id, $leave_check)) {
                $representative = $representer_check->firstWhere('employee_id', $employee->id);
                if ($representative) {
                    // Replace employee details with representative details
                    $employee->id = $representative->representer_id;
                    $employee->name = $representative->represent_name;
                    $employee->lastname = $representative->represent_lastname;
                } else {
                    // If no representative, remove from the list
                    return null;
                }
            }
            return $employee;
        })->filter();



        // Check if available employees list is empty
        $useFallbackEmployees = $availableEmployees->isEmpty();
        $employeesToShow = $useFallbackEmployees ? $employees_inCase : $availableEmployees;
        $message = $useFallbackEmployees ? 'No available employees. Showing all employees as a fallback.' : '';

        return response()->json([
            'availableEmployees' => $employeesToShow,
        ]);
    }

    public function store(Request $request)
    {
        try {
            Log::info('lead Request: ', [$request->all()]);

            $validated = $request->validate([
                'from' => 'nullable|string',
                'customer_type' => 'required|string',
                'firma' => 'nullable|string',
                'title' => 'nullable|string',
                'academic_title' => 'nullable|string',
                'name' => 'nullable|string',
                'lastname' => 'nullable|string',
                'source' => 'nullable|string',

                'full_address' => 'nullable|string',
                'street' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'elevation' => 'nullable|numeric',
                'postcode' => 'nullable|string',
                'city' => 'nullable|string',

                'phone' => 'nullable|string',
                'telephone' => 'nullable|string',
                'email' => 'nullable|email',

                'branch_id' => 'required|numeric|exists:branches,id',

                'alternative_address' => 'nullable|string',
                'full_address2' => 'nullable|string',
                'street2' => 'nullable|string',
                'latitude2' => 'nullable|numeric',
                'longitude2' => 'nullable|numeric',
                'elevation2' => 'nullable|numeric',
                'postcode2' => 'nullable|string',
                'city2' => 'nullable|string',

                'info' => 'nullable|string',
                'document' => 'nullable|string',
                'contact_person' => 'nullable|numeric|exists:employees,id',
                'periority' => 'nullable|string',
                'appointment' => 'nullable|date',
                'note' => 'nullable|string',
                'object_name' => 'nullable|string',
                'request_date' => 'nullable|date',

                // Product arrays (optional)
                'product_id' => 'nullable|array',
                'product_id.*' => 'nullable|integer',
                'service_id' => 'nullable|array',
                'service_id.*' => 'nullable|integer',
                'department_id' => 'nullable|array',
                'department_id.*' => 'nullable|integer',
                'employee_id' => 'nullable|array',
                'employee_id.*' => 'nullable|integer',
                'field_employee' => 'nullable|array',
                'field_employee.*' => 'nullable|integer',
                'interest' => 'nullable|array',
                'interest.*' => 'nullable|string',
                'realization_time' => 'nullable|array',
                'realization_time.*' => 'nullable|string',

                // Roofs (optional)
                'roofs' => 'nullable|array',
                'roofs.*.designation' => 'nullable|string',
                'roofs.*.roof' => 'nullable|string',
                'roofs.*.roof_covering_name' => 'nullable|string',
                'roofs.*.roof_age' => 'nullable|string',
                'roofs.*.thickness_roof_insulation' => 'nullable|string',
                'roofs.*.roof_renovation' => 'nullable|string',
                'roofs.*.pv_existing' => 'nullable|string',
                'roofs.*.construction_year' => 'nullable|string',
                'roofs.*.module_count' => 'nullable|numeric',
                'roofs.*.module_power' => 'nullable|numeric',
                'roofs.*.kwp_size' => 'nullable|numeric',
                'roofs.*.intention' => 'nullable|string',
                'roofs.*.notes' => 'nullable|string',

                // Screenshot (optional)
                'screenshot_file' => 'nullable|file',
            ]);

            $newLead = null;

            DB::transaction(function () use (&$newLead, $request, $validated) {
                // Generate a customer number (try to avoid collisions)
                $year = date('Y');
                do {
                    $customerNo = $year . '-' . mt_rand(10000, 999999);
                } while (NewLeads::where('customer_no', $customerNo)->exists());

                $addressNo = mt_rand(100000, 9999999);
                $datevId = substr($customerNo, -5);

                Log::info('datev Number:', ['value' => $datevId]);
                Log::info('Kunde Number:', ['value' => $customerNo]);

                // Qualification / traffic-light message
                $street = $validated['street'] ?? null;
                $postcode = $validated['postcode'] ?? null;
                $city = $validated['city'] ?? null;

                $email = $validated['email'] ?? null;
                $phone = $validated['phone'] ?? null;
                $telephone = $validated['telephone'] ?? null;

                $statusMsg = 'QUALIFIZIERT';
                if (!$street || !$postcode || !$city) {
                    $statusMsg = 'KEINE KONTAKTDATEN';
                } elseif (!$email && !$phone && !$telephone) {
                    $statusMsg = 'um zu qualifizieren, bitte per Brief Kontakt aufnehmen';
                } elseif (!$email) {
                    $statusMsg = 'um zu qualifizieren, bitte telefonisch Kontakt aufnehmen';
                } elseif (!$phone && !$telephone) {
                    $statusMsg = 'um zu qualifizieren, bitte per E-Mail Kontakt aufnehmen';
                }

                // Create lead
                $newLead = NewLeads::create([
                    'customer_no' => $customerNo,
                    'customer_type' => $validated['customer_type'],
                    'firma' => $validated['firma'] ?? null,
                    'title' => $validated['title'] ?? null,
                    'name' => $validated['name'] ?? null,
                    'lastname' => $validated['lastname'] ?? null,
                    'full_address' => $request->input('full_address'),
                    'street' => $street,
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                    'elevation' => $validated['elevation'] ?? null,
                    'postcode' => $postcode,
                    'city' => $city,
                    'telephone' => $telephone,
                    'phone' => $phone,
                    'email' => $email,
                    'source' => $validated['source'] ?? null,
                    'contact_person' => auth()->user()->name, // creator (kept as in your code)
                    'branch' => $validated['branch_id'],
                    'info' => $validated['info'] ?? null,
                    'status_msg' => $statusMsg,
                    'status' => 'Published',
                ]);

                $this->logActivity(
                    'created',
                    NewLeads::class,
                    $newLead->id,
                    $newLead->id,
                    null,
                    null,
                    ['info' => 'Neuer Kunde angelegt']
                );

                // Main/alternative object (LeadAlternativeAdd)
                $appointmentBy = $request->filled('appointment') ? auth()->user()->name : null;
                $isMain = filter_var($request->input('alternative_address'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

                $alternative = LeadAlternativeAdd::create([
                    'lead_id' => $newLead->id,
                    'full_address' => $request->input('full_address2'),
                    'street' => $validated['street2'] ?? null,
                    'postcode' => $validated['postcode2'] ?? null,
                    'city' => $validated['city2'] ?? null,
                    'lat' => $validated['latitude2'] ?? null,
                    'lon' => $validated['longitude2'] ?? null,
                    'elevation' => $validated['elevation2'] ?? null,
                    'main' => $isMain,
                    'address_no' => $addressNo,

                    'object_name' => $request->input('object_name', 'Privathaus'),
                    'periority' => $request->input('periority', 'Normal'),
                    'request_date' => $request->input('request_date', now()),
                    'document' => $validated['document'] ?? null,
                    'note' => $validated['note'] ?? null,
                    'appointment' => $validated['appointment'] ?? null,
                    'appointment_by' => $appointmentBy,

                    // Everything else optional (kept from your original, but safely pulled)
                    'object_type' => $request->input('object_type'),
                    'building_condition' => $request->input('building_condition'),
                    'usage_type' => $request->input('usage_type'),
                    'owner_count' => $request->input('owner_count'),
                    'number_we' => $request->input('number_we'),
                    'number_people' => $request->input('number_people'),
                    'house_year' => $request->input('house_year'),
                    'number_stories' => $request->input('number_stories'),
                    'living_space' => $request->input('living_space'),
                    'external_insulation_thickness' => $request->input('external_insulation_thickness'),
                    'masonry' => $request->input('masonry'),
                    'window_glazing' => $request->input('window_glazing'),
                    'window_frame' => $request->input('window_frame'),
                    'window_year' => $request->input('window_year'),
                    'door_year' => $request->input('door_year'),
                    'door_condition' => $request->input('door_condition'),
                    'objective' => $request->input('objective'),
                    'unusable_space' => $request->input('unusable_space'),
                    'installation_location' => $request->input('installation_location'),
                    'installation_location_extra' => $request->input('installation_location_extra'),
                    'annual_consumption' => $request->input('annual_consumption'),
                    'tile_name' => $request->input('tile_name'),
                    'roof_type' => $request->input('roof_type'),
                    'roof_age' => $request->input('roof_age'),
                    'heating_system_age' => $request->input('heating_system_age'),
                    'heating_system_year' => $request->input('heating_system_year'),
                    'heating_type' => $request->input('heating_type'),
                    'heating_circuits_count' => $request->input('heating_circuits_count'),
                    'heating_system_type' => $request->input('heating_system_type'),
                    'annual_heating_energy_consumption' => $request->input('total_heat_consumption'),
                    'annual_heating_energy_consumption_kwh' => $request->input('annual_heating_energy_consumption_kwh'),

                    'electric_car' => $request->input('electric_car'),
                    'electric_car_plan' => $request->input('electric_car_plan'),
                    'electric_car_count' => $request->input('electric_car_count'),
                    'wallbox_count' => $request->input('wallbox_count'),

                    'status' => 'Published',
                    'total_number' => $request->input('total_number_input'),
                    'answered_number' => $request->input('answer_input'),
                    'roof_covering' => $request->input('roof_covering', 0),
                    'roof_pitch' => $request->input('roof_pitch'),
                    'roof_direction' => $request->input('roof_direction'),

                    'fireplace' => $request->input('fireplace'),
                    'wood_consumption' => $request->input('wood_consumption'),
                    'fireplace_value' => $request->input('fireplace_value'),

                    'car_kilo' => $request->input('car_kilo'),
                    'stage' => 'lead',
                    'project_date' => $request->input('project_date'),

                    'object_remark' => $request->input('object_remark'),
                    'heating_remark' => $request->input('heating_remark'),
                    'roof_remark' => $request->input('roof_remark'),
                    'energy_remark' => $request->input('energy_remark'),
                    'car_remark' => $request->input('car_remark'),

                    'wallbox_location' => $request->input('wallbox_location'),

                    'is_owner' => $request->input('is_owner', 'Ja'),
                    'is_living_inside' => $request->input('is_living_inside', 'Ja'),
                    'income' => $request->input('income', 40000),

                    'insolation' => $request->input('insolation', 0),
                    'insolation_thickness' => $request->input('external_insulation_thickness'),
                    'insolation_type' => $request->input('insolation_type'),
                    'insolation_matarial' => $request->input('insolation_matarial'),
                    'insolation_age' => $request->input('insolation_age'),

                    'income_taxed' => $request->input('income_taxed'),
                    'heating_age_group' => $request->input('heating_age_group'),
                    'natural_refrigerant' => $request->input('natural_refrigerant'),

                    'investment_costs' => $request->input('investment_costs'),
                    'calculated_subsidy' => $request->input('calculated_subsidy'),
                    'calculated_credit_need' => $request->input('calculated_credit_need'),
                    'calculated_rate' => $request->input('calculated_rate'),
                    'recommended_program' => $request->input('recommended_program'),
                    'subsidy_quote' => $request->input('subsidy_quote'),

                    'number_self_used' => $request->input('number_self_used'),
                    'solar_module_kwp' => $request->input('solar_module_kwp'),
                    'solar_tile_kwp' => $request->input('solar_tile_kwp'),
                    'battery_kwh' => $request->input('battery_kwh'),
                    'balcony_modules' => $request->input('balcony_modules', 0),

                    'has_pump_upgrade' => $request->input('has_pump_upgrade', false),
                    'hydraulic_only' => $request->input('hydraulic_only'),

                    'solar_thermal' => $request->input('solar_thermal'),
                    'solar_thermal_area' => $request->input('solar_thermal_area'),
                    'solar_thermal_simulation' => $request->input('solar_thermal_simulation', false),

                    'pipe_system_count' => $request->input('pipe_system_count'),
                    'quantity' => $request->input('quantity'),
                    'pipe_system_material' => $request->input('pipe_system_material'),
                    'consumption' => $request->input('consumption'),

                    'bathroom_count' => $request->input('bathroom_count'),
                    'hot_water_generation' => $request->input('hot_water_generation'),
                    'bathtub_count' => $request->input('bathtub_count'),

                    'income_level' => $request->input('income_level'),
                    'total_heat_consumption' => $request->input('total_heat_consumption'),

                    'heating_load_calculation' => $request->input('heating_load_calculation'),
                    'total_electricity_consumption' => $request->input('total_electricity_consumption'),

                    'heavy_current_cable' => $request->input('heavy_current_cable'),
                    'network_cable' => $request->input('network_cable'),
                    'groundwork' => $request->input('groundwork'),
                    'company_vehicle' => $request->input('company_vehicle'),
                    'bidirectional_car' => $request->input('bidirectional_car'),

                    'power_household' => $request->input('power_household'),
                    'power_heatpump' => $request->input('power_heatpump'),
                    'power_electric_car' => $request->input('power_electric_car'),
                    'power_other' => $request->input('power_other'),
                    'power_total' => $request->input('power_total'),

                    'meter_cabinet' => $request->input('meter_cabinet'),
                    'tenant_model' => $request->input('tenant_model'),
                    'installation_location_power' => $request->input('installation_location_power'),
                    'network_wlan' => $request->input('network_wlan'),
                    'meter_count' => $request->input('meter_count'),
                ]);

                $this->logActivity(
                    'created',
                    LeadAlternativeAdd::class,
                    $alternative->id,
                    $newLead->id,
                    $alternative->id,
                    null,
                    ['info' => 'Hauptobjekt erstellt']
                );

                // Roofs
                $roofs = $request->input('roofs', []);
                if (is_array($roofs) && count($roofs)) {
                    foreach ($roofs as $roof) {
                        try {
                            PVRoof::create([
                                'customer_id' => $newLead->id,
                                'alternative_id' => $alternative->id,
                                'designation' => $roof['designation'] ?? null,
                                'roof' => $roof['roof'] ?? null,
                                'roof_covering_name' => $roof['roof_covering_name'] ?? null,
                                'roof_age' => $roof['roof_age'] ?? null,
                                'thickness_roof_insulation' => $roof['thickness_roof_insulation'] ?? null,
                                'roof_renovation' => $roof['roof_renovation'] ?? null,
                                'pv_existing' => $roof['pv_existing'] ?? null,
                                'construction_year' => $roof['construction_year'] ?? null,
                                'module_count' => $roof['module_count'] ?? null,
                                'module_power' => $roof['module_power'] ?? null,
                                'kwp_size' => $roof['kwp_size'] ?? 0,
                                'intention' => $roof['intention'] ?? null,
                                'notes' => $roof['notes'] ?? null,
                            ]);
                        } catch (\Throwable $e) {
                            Log::error('Fehler beim Speichern eines Dachs:', [
                                'roof' => $roof,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }

                // Products (FIXED: $prod defined)
                $productIds = $request->input('product_id', []);
                if (is_array($productIds) && count($productIds)) {
                    foreach ($productIds as $index => $productId) {
                        $serviceId = $request->input("service_id.$index");
                        $serviceName = null;
                        if ($serviceId) {
                            $serviceName = optional(PhaseSection::find($serviceId))->phase_section;
                        }

                        $prod = LeadProductList::create([
                            'customer_id' => $newLead->id,
                            'product_id' => $productId,
                            'alternative_id' => $alternative->id,
                            'department_id' => $request->input("department_id.$index"),
                            'employee_id' => $request->input("employee_id.$index"),
                            'field_employee' => $request->input("field_employee.$index"),
                            'service_id' => $serviceId,
                            'realization_time' => $request->input("realization_time.$index"),
                            'service' => $serviceName,
                            'interest' => $request->input("interest.$index", 'intent'),
                            'status' => 'Lead',
                        ]);

                        $this->logActivity(
                            'created',
                            LeadProductList::class,
                            $prod->id,
                            $newLead->id,
                            $alternative->id,
                            $productId,
                            ['info' => 'Produkt initial hinzugefügt']
                        );
                    }
                }

                // If data came from inquiry, publish it back
                $from = $request->input('from');
                if ($from && $from !== 'normal') {
                    Inquiry::whereKey($from)->update(['status' => 'Published']);
                }

                // Screenshot file
                if ($request->hasFile('screenshot_file')) {
                    $file = $request->file('screenshot_file');
                    $imageName = time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('/uploads'), $imageName);

                    Image::create([
                        'customer_id' => $newLead->id,
                        'alternative_id' => $alternative->id,
                        'image_name' => $imageName,
                        'image' => $imageName,
                        'article_group' => null,
                        'stage' => 'customer',
                        'status' => 'screenshot',
                        'file_type' => $file->getClientOriginalExtension(),
                        'created_by' => auth()->user()->name,
                    ]);
                }
            });

            $redirectUrl = url('new_lead_view') . '?highlight_id=' . $newLead->id . '&created=1';

            if (!$request->expectsJson() && !$request->ajax()) {
                return redirect($redirectUrl)
                    ->with('highlight_lead_id', $newLead->id)
                    ->with('save_msg', 'Lead erfolgreich gespeichert!');
            }

            return response()->json([
                'success' => true,
                'message' => 'Lead erfolgreich gespeichert!',
                'redirect' => $redirectUrl,
                'lead_id' => $newLead->id,
                'highlight_id' => $newLead->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error storing lead: ', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
        }
    }



    private function make_task($cname, $by, $note, $appointment, $customer_no, $city, $email, $phone)
    {
        $start_time = '09:00:00';
        $end_time = '10:00:00';
        // Create the main task
        $task = PersonalTask::create([
            'task_id' => mt_rand(1000000, 9999999),
            'task_title' => 'Kontakt mit ' . $cname . ' (Kunde) aus ' . $city . ' per [' . $by . ']',
            'description' => $note ?? '',
            'priority' => 'normal',
            'color' => '#8fc73e',
            'assigned_by' => auth()->user()->name,
            'progress' => '40',
            'task_status' => 'on_going',
            'start_date' => now(),
            'end_date' => $appointment,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'total_time' => round(\Carbon\Carbon::parse($start_time)->diffInMinutes(\Carbon\Carbon::parse($end_time)) / 60, 2),
        ]);

        // Assign task to an employee
        EmployeesPersonalTask::create([
            'employee_id' => auth()->user()->name,
            'task_id' => $task->id,
            'status' => 'accept',
        ]);

        // Create task key
        $tasks = [
            'Gespräch mit Kunde Nummer ' . $customer_no . ' über den letzten Kauf',
            'E-Mail: ' . $email . '& Telefon: ' . $phone,
        ];

        foreach ($tasks as $taskDescription) {
            PersonalTaskKey::create([
                'personal_task_id' => $task->id,
                'task' => $taskDescription,
                'done_by' => null,
            ]);
        }


        // Send notification
        $emp = DB::table('employees')
            ->select('name', 'lastname')
            ->where('id', auth()->user()->name)
            ->first();

        $emp_name = $emp->name . ' ' . $emp->lastname;

        Notification::send(auth()->user(), new PersonalTaskNotification([
            'title' => 'Aufgabe erstellt',
            'message' => 'Eine neue Aufgabe wurde von ' . $emp_name . ' erstellt',
            'task_id' => $task->id,
        ]));
    }



    public function details_update(Request $request)
    {
        Log::info('details_update', $request->all());

        $validated = $request->validate([
            'id' => 'required|integer|exists:new_leads,id',
            'customer_type' => 'nullable|string',
            'title' => 'nullable|string',
            'academic_title' => 'nullable|string',
            'firma' => 'nullable|string',
            'lastname' => 'nullable|string',
            'name' => 'nullable|string',

            'full_address' => 'nullable|string',
            'street' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'polygon_height' => 'nullable|numeric',
            'polygon_width' => 'nullable|numeric',
            'polygon_area' => 'nullable|numeric',
            'elevation' => 'nullable|numeric',
            'postcode' => 'nullable|string',
            'city' => 'nullable|string',

            'phone' => 'nullable|string',
            'telephone' => 'nullable|string',
            'email' => 'nullable|email',
            'source' => 'nullable|string',
            'info' => 'nullable|string',

            'contact_person' => 'nullable|integer',
            'interest_rating' => 'nullable|integer',
            'seriousness_rating' => 'nullable|integer',
            'price_information' => 'nullable|integer',
        ], [
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
        ]);

        try {
            $street = $validated['street'] ?? null;
            $postcode = $validated['postcode'] ?? null;
            $city = $validated['city'] ?? null;
            $email = $validated['email'] ?? null;
            $phone = $validated['phone'] ?? null;
            $telephone = $validated['telephone'] ?? null;

            // status_msg logic (same as your intent, but deterministic)
            $statusMsg = 'QUALIFIZIERT';
            $stage = 'new';

            if (!$street || !$postcode || !$city) {
                $statusMsg = 'KEINE KONTAKTDATEN';
                $stage = 'new';
            } elseif (!$email && !$phone && !$telephone) {
                $statusMsg = 'um zu qualifizieren, bitte per Brief  Kontakt aufnehmen';
                $stage = 'new';
            } elseif (!$email) {
                $statusMsg = 'um zu qualifizieren, bitte telefonisch  Kontakt aufnehmen';
                $stage = 'new';
            } elseif (!$phone && !$telephone) {
                $statusMsg = 'um zu qualifizieren, bitte per E-Mail  Kontakt aufnehmen';
                $stage = 'new';
            }

            // keep existing behavior: build full_address from components
            $fullAddress = trim(($street ?? '') . ' ' . ($postcode ?? '') . ' ,' . ($city ?? ''));

            $lead = NewLeads::findOrFail($validated['id']);

            $lead->fill([
                'customer_type' => $validated['customer_type'] ?? null,
                'title' => $validated['title'] ?? null,
                'academic_title' => $validated['academic_title'] ?? null,
                'firma' => $validated['firma'] ?? null,
                'lastname' => $validated['lastname'] ?? null,
                'name' => $validated['name'] ?? null,

                'full_address' => $fullAddress ?: ($validated['full_address'] ?? null),
                'street' => $street,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'polygon_height' => $validated['polygon_height'] ?? null,
                'polygon_width' => $validated['polygon_width'] ?? null,
                'polygon_area' => $validated['polygon_area'] ?? null,
                'elevation' => $validated['elevation'] ?? null,
                'postcode' => $postcode,
                'city' => $city,

                'phone' => $phone,
                'telephone' => $telephone,
                'email' => $email,

                'source' => $validated['source'] ?? null,
                'interest_rating' => $validated['interest_rating'] ?? null,
                'seriousness_rating' => $validated['seriousness_rating'] ?? null,
                'price_information' => $validated['price_information'] ?? null,
                'info' => $validated['info'] ?? null,

                'status_msg' => $statusMsg,
                'status' => 'Published',
                // if you actually store stage on lead, uncomment:
                // 'stage'          => $stage,
            ])->save();

            $this->logActivity(
                'updated',
                NewLeads::class,
                $lead->id,
                $lead->id,
                null,
                null,
                ['info' => 'Kundenstammdaten aktualisiert']
            );

            return response()->json([
                'success' => true,
                'message' => 'Der Kunde wurde erfolgreich gespeichert.',
                'redirect' => url('new_lead_view'),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saving lead:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the lead.',
            ], 500);
        }
    }

    public function object_store(Request $request)
    {
        try {
            Log::info('object_store Request:', [$request->all()]);

            $validated = $request->validate([
                'lead_id' => 'required|integer|exists:new_leads,id',

                'full_address2' => 'nullable|string',
                'street' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'elevation' => 'nullable|numeric',
                'postcode' => 'nullable|string',
                'city' => 'nullable|string',
                'alternative_address' => 'nullable|string',

                'document' => 'nullable|string',
                'note' => 'nullable|string',
                'appointment' => 'nullable|date',

                // roofs
                'roofs' => 'nullable|array',
                'roofs.*.designation' => 'nullable|string',
                'roofs.*.roof' => 'nullable|string',
                'roofs.*.roof_covering_name' => 'nullable|string',
                'roofs.*.roof_age' => 'nullable|string',
                'roofs.*.thickness_roof_insulation' => 'nullable|string',
                'roofs.*.roof_renovation' => 'nullable|string',
                'roofs.*.pv_existing' => 'nullable|string',
                'roofs.*.construction_year' => 'nullable|string',
                'roofs.*.module_count' => 'nullable|numeric',
                'roofs.*.module_power' => 'nullable|numeric',
                'roofs.*.kwp_size' => 'nullable|numeric',
                'roofs.*.intention' => 'nullable|string',
                'roofs.*.notes' => 'nullable|string',

                // product arrays (flat, as in your form)
                'product_id' => 'nullable|array',
                'product_id.*' => 'nullable|integer',
                'service_id' => 'nullable|array',
                'service_id.*' => 'nullable|integer',
                'department_id' => 'nullable|array',
                'department_id.*' => 'nullable|integer',
                'employee_id' => 'nullable|array',
                'employee_id.*' => 'nullable|integer',
                'field_employee' => 'nullable|array',
                'field_employee.*' => 'nullable|integer',
                'interest' => 'nullable|array',
                'interest.*' => 'nullable|string',
                'realization_time' => 'nullable|array',
                'realization_time.*' => 'nullable|string',

                // screenshot
                'screenshot_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp',
                'from' => 'nullable|string',
            ]);

            DB::transaction(function () use ($request, $validated) {
                $addressNo = mt_rand(100000, 9999999);
                $isMain = filter_var($request->input('alternative_address'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

                $alternative = LeadAlternativeAdd::create([
                    'lead_id' => $validated['lead_id'],
                    'full_address' => $request->input('full_address2'),
                    'street' => $validated['street'] ?? null,
                    'postcode' => $validated['postcode'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'lat' => $validated['latitude'] ?? null,
                    'lon' => $validated['longitude'] ?? null,
                    'elevation' => $validated['elevation'] ?? null,
                    'main' => $isMain,
                    'address_no' => $addressNo,

                    'object_name' => $request->input('object_name', 'Privathaus'),
                    'periority' => $request->input('periority', 'Normal'),
                    'request_date' => $request->input('request_date', now()),
                    'document' => $validated['document'] ?? null,
                    'note' => $validated['note'] ?? null,
                    'appointment' => $validated['appointment'] ?? null,

                    // Keep your many optional fields
                    'object_type' => $request->input('object_type'),
                    'building_condition' => $request->input('building_condition'),
                    'usage_type' => $request->input('usage_type'),
                    'owner_count' => $request->input('owner_count'),
                    'number_we' => $request->input('number_we'),
                    'number_people' => $request->input('number_people'),
                    'house_year' => $request->input('house_year'),
                    'number_stories' => $request->input('number_stories'),
                    'living_space' => $request->input('living_space'),
                    'external_insulation_thickness' => $request->input('external_insulation_thickness'),
                    'masonry' => $request->input('masonry'),
                    'window_glazing' => $request->input('window_glazing'),
                    'window_frame' => $request->input('window_frame'),
                    'window_year' => $request->input('window_year'),
                    'door_year' => $request->input('door_year'),
                    'door_condition' => $request->input('door_condition'),
                    'objective' => $request->input('objective'),
                    'unusable_space' => $request->input('unusable_space'),
                    'installation_location' => $request->input('installation_location'),
                    'installation_location_extra' => $request->input('installation_location_extra'),
                    'annual_consumption' => $request->input('annual_consumption'),
                    'tile_name' => $request->input('tile_name'),
                    'roof_type' => $request->input('roof_type'),
                    'roof_age' => $request->input('roof_age'),
                    'heating_system_age' => $request->input('heating_system_age'),
                    'heating_system_year' => $request->input('heating_system_year'),
                    'heating_type' => $request->input('heating_type'),
                    'heating_circuits_count' => $request->input('heating_circuits_count'),
                    'heating_system_type' => $request->input('heating_system_type'),
                    'annual_heating_energy_consumption' => $request->input('total_heat_consumption'),
                    'annual_heating_energy_consumption_kwh' => $request->input('annual_heating_energy_consumption_kwh'),
                    'electric_car' => $request->input('electric_car'),
                    'electric_car_plan' => $request->input('electric_car_plan'),
                    'electric_car_count' => $request->input('electric_car_count'),
                    'wallbox_count' => $request->input('wallbox_count'),
                    'status' => 'Published',
                    'total_number' => $request->input('total_number_input'),
                    'answered_number' => $request->input('answer_input'),
                    'roof_covering' => $request->input('roof_covering', 0),
                    'roof_pitch' => $request->input('roof_pitch'),
                    'roof_direction' => $request->input('roof_direction'),
                    'fireplace' => $request->input('fireplace'),
                    'wood_consumption' => $request->input('wood_consumption'),
                    'fireplace_value' => $request->input('fireplace_value'),
                    'car_kilo' => $request->input('car_kilo'),
                    'stage' => 'lead',
                    'project_date' => $request->input('project_date'),
                    'object_remark' => $request->input('object_remark'),
                    'heating_remark' => $request->input('heating_remark'),
                    'roof_remark' => $request->input('roof_remark'),
                    'energy_remark' => $request->input('energy_remark'),
                    'car_remark' => $request->input('car_remark'),
                    'wallbox_location' => $request->input('wallbox_location'),
                    'is_owner' => $request->input('is_owner', 'Ja'),
                    'is_living_inside' => $request->input('is_living_inside', 'Ja'),
                    'income' => $request->input('income', 40000),
                    'insolation' => $request->input('insolation', 0),
                    'insolation_thickness' => $request->input('external_insulation_thickness'),
                    'insolation_type' => $request->input('insolation_type'),
                    'insolation_matarial' => $request->input('insolation_matarial'),
                    'insolation_age' => $request->input('insolation_age'),
                    'income_taxed' => $request->input('income_taxed'),
                    'heating_age_group' => $request->input('heating_age_group'),
                    'natural_refrigerant' => $request->input('natural_refrigerant'),
                    'investment_costs' => $request->input('investment_costs'),
                    'calculated_subsidy' => $request->input('calculated_subsidy'),
                    'calculated_credit_need' => $request->input('calculated_credit_need'),
                    'calculated_rate' => $request->input('calculated_rate'),
                    'recommended_program' => $request->input('recommended_program'),
                    'subsidy_quote' => $request->input('subsidy_quote'),
                    'number_self_used' => $request->input('number_self_used'),
                    'solar_module_kwp' => $request->input('solar_module_kwp'),
                    'solar_tile_kwp' => $request->input('solar_tile_kwp'),
                    'battery_kwh' => $request->input('battery_kwh'),
                    'balcony_modules' => $request->input('balcony_modules', 0),
                    'has_pump_upgrade' => $request->input('has_pump_upgrade', false),
                    'hydraulic_only' => $request->input('hydraulic_only'),
                    'solar_thermal' => $request->input('solar_thermal'),
                    'solar_thermal_area' => $request->input('solar_thermal_area'),
                    'solar_thermal_simulation' => $request->input('solar_thermal_simulation', false),
                    'pipe_system_count' => $request->input('pipe_system_count'),
                    'quantity' => $request->input('quantity'),
                    'pipe_system_material' => $request->input('pipe_system_material'),
                    'consumption' => $request->input('consumption'),
                    'bathroom_count' => $request->input('bathroom_count'),
                    'hot_water_generation' => $request->input('hot_water_generation'),
                    'bathtub_count' => $request->input('bathtub_count'),
                    'income_level' => $request->input('income_level'),
                    'total_heat_consumption' => $request->input('total_heat_consumption'),
                    'heating_load_calculation' => $request->input('heating_load_calculation'),
                    'total_electricity_consumption' => $request->input('total_electricity_consumption'),
                    'heavy_current_cable' => $request->input('heavy_current_cable'),
                    'network_cable' => $request->input('network_cable'),
                    'groundwork' => $request->input('groundwork'),
                    'company_vehicle' => $request->input('company_vehicle'),
                    'bidirectional_car' => $request->input('bidirectional_car'),
                    'power_household' => $request->input('power_household'),
                    'power_heatpump' => $request->input('power_heatpump'),
                    'power_electric_car' => $request->input('power_electric_car'),
                    'power_other' => $request->input('power_other'),
                    'power_total' => $request->input('power_total'),
                    'meter_cabinet' => $request->input('meter_cabinet'),
                    'tenant_model' => $request->input('tenant_model'),
                    'installation_location_power' => $request->input('installation_location_power'),
                    'network_wlan' => $request->input('network_wlan'),
                    'meter_count' => $request->input('meter_count'),
                ]);

                $this->logActivity(
                    'created',
                    LeadAlternativeAdd::class,
                    $alternative->id,
                    $validated['lead_id'],
                    $alternative->id,
                    null,
                    ['info' => 'Neues Objekt hinzugefügt']
                );

                // Roofs
                foreach (($request->input('roofs', []) ?? []) as $roof) {
                    try {
                        PVRoof::create([
                            'customer_id' => $validated['lead_id'],
                            'alternative_id' => $alternative->id,
                            'designation' => $roof['designation'] ?? null,
                            'roof' => $roof['roof'] ?? null,
                            'roof_covering_name' => $roof['roof_covering_name'] ?? null,
                            'roof_age' => $roof['roof_age'] ?? null,
                            'thickness_roof_insulation' => $roof['thickness_roof_insulation'] ?? null,
                            'roof_renovation' => $roof['roof_renovation'] ?? null,
                            'pv_existing' => $roof['pv_existing'] ?? null,
                            'construction_year' => $roof['construction_year'] ?? null,
                            'module_count' => $roof['module_count'] ?? null,
                            'module_power' => $roof['module_power'] ?? null,
                            'kwp_size' => $roof['kwp_size'] ?? 0,
                            'intention' => $roof['intention'] ?? null,
                            'notes' => $roof['notes'] ?? null,
                        ]);
                    } catch (\Throwable $e) {
                        Log::error('Fehler beim Speichern eines Dachs:', [
                            'roof' => $roof,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                // Products
                // FIX: Empty Select2 rows submit product_id[]=null. Never insert null product_id.
                $productIds = collect($request->input('product_id', []))
                    ->map(fn($value) => is_numeric($value) ? (int) $value : null)
                    ->filter(fn($value) => !empty($value))
                    ->values();

                foreach ($productIds as $index => $productId) {
                    $serviceId = $request->input("service_id.$index");
                    $serviceId = is_numeric($serviceId) ? (int) $serviceId : null;

                    $departmentId = $request->input("department_id.$index");
                    $departmentId = is_numeric($departmentId) ? (int) $departmentId : null;

                    $employeeId = $request->input("employee_id.$index");
                    $employeeId = is_numeric($employeeId) ? (int) $employeeId : null;

                    $fieldEmployeeId = $request->input("field_employee.$index");
                    $fieldEmployeeId = is_numeric($fieldEmployeeId) ? (int) $fieldEmployeeId : null;

                    $serviceName = $serviceId ? optional(PhaseSection::find($serviceId))->phase_section : null;

                    $prod = LeadProductList::create([
                        'customer_id' => $validated['lead_id'],
                        'alternative_id' => $alternative->id,
                        'product_id' => $productId,
                        'service_id' => $serviceId,
                        'department_id' => $departmentId,
                        'employee_id' => $employeeId,
                        'field_employee' => $fieldEmployeeId,
                        'interest' => $request->input("interest.$index", 'intent') ?: 'intent',
                        'realization_time' => $request->input("realization_time.$index") ?: null,
                        'service' => $serviceName,
                        'status' => 'Lead',
                    ]);

                    $this->logActivity(
                        'created',
                        LeadProductList::class,
                        $prod->id,
                        $validated['lead_id'],
                        $alternative->id,
                        $productId,
                        ['info' => 'Produkt zum Objekt hinzugefügt']
                    );
                }

                // Update inquiry status if linked
                $from = $request->input('from');
                if ($from && $from !== 'normal') {
                    Inquiry::whereKey($from)->update(['status' => 'Published']);
                }

                // Screenshot upload
                if ($request->hasFile('screenshot_file')) {
                    $file = $request->file('screenshot_file');
                    $imageName = time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads'), $imageName);

                    Image::create([
                        'customer_id' => $validated['lead_id'],
                        'alternative_id' => $alternative->id,
                        'image_name' => $imageName,
                        'image' => $imageName,
                        'stage' => 'customer',
                        'status' => 'screenshot',
                        'file_type' => $file->getClientOriginalExtension(),
                        'created_by' => auth()->user()->name,
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Lead erfolgreich gespeichert!',
                'redirect' => url('new_lead_view'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Error storing lead:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Fehler beim Speichern. Bitte erneut versuchen.'], 500);
        }
    }
    public function update(Request $request)
    {
        Log::info('Updating lead with data: ', $request->all());

        // Legacy flat arrays -> normalize to product[]
        if (!$request->has('product') && $request->has('product_id')) {
            $products = [];
            foreach (($request->product_id ?? []) as $i => $productId) {
                $products[] = [
                    'product_id' => $productId,
                    'service_id' => $request->service_id[$i] ?? null,
                    'department_id' => $request->department_id[$i] ?? null,
                    'employee_id' => $request->employee_id[$i] ?? null,
                    'field_employee' => $request->field_employee[$i] ?? null,
                    'realization_time' => $request->realization_time[$i] ?? null,
                    'interest' => $request->interest[$i] ?? null,
                ];
            }
            $request->merge(['product' => $products]);
        }

        $validated = $request->validate([
            'id' => 'required|integer|exists:new_leads,id',
            'alternative_id' => 'required|integer|exists:lead_alternative_adds,id',

            'title' => 'nullable|string|max:20',
            'academic_title' => 'nullable|string|max:50',
            'full_address' => 'nullable|string',
            'street' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'elevation' => 'nullable|numeric',
            'postcode' => 'nullable|string',
            'city' => 'nullable|string',
            'email' => 'nullable|email',
            'appointment' => 'nullable|date',
            'branch_id' => 'required|integer|exists:branches,id',

            // New normalized products
            'product' => 'required|array|min:1',
            'product.*.product_id' => 'required|integer', // keep as-is (you had article_groups)
            'product.*.service_id' => 'nullable|integer|exists:phase_sections,id',
            'product.*.department_id' => 'nullable|integer|exists:departments,id',
            'product.*.employee_id' => 'nullable|integer|exists:employees,id',
            'product.*.field_employee' => 'nullable|integer|exists:employees,id',
            'product.*.interest' => 'nullable|string|in:intent,interest,option',
            'product.*.realization_time' => 'nullable|string',

            'roofs' => 'nullable|array',
            'screenshot_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp',
        ]);

        try {
            DB::beginTransaction();

            $lead = NewLeads::findOrFail($validated['id']);
            $lead->fill($request->except(['product', 'screenshot_file', '_token', 'roofs', 'request_date']))->save();

            $alternative = LeadAlternativeAdd::findOrFail($validated['alternative_id']);

            // Build safe map for alternative fields (only those present)
            $altFields = [
                'full_address',
                'street',
                'city',
                'postcode',
                'latitude',
                'longitude',
                'elevation',
                'request_date',
                'document',
                'periority',
                'note',
                'appointment',
                'annual_consumption',
                'roof_type',
                'roof_age',
                'roof_covering',
                'roof_pitch',
                'roof_direction',
                'roof_remark',
                'object_remark',
                'house_year',
                'heating_system_age',
                'heating_system_year',
                'heating_system_type',
                'heating_type',
                'heating_remark',
                'annual_heating_energy_consumption',
                'annual_heating_energy_consumption_kwh',
                'fireplace',
                'wood_consumption',
                'fireplace_value',
                'energy_remark',
                'electric_car',
                'electric_car_plan',
                'electric_car_count',
                'car_kilo',
                'car_remark',
                'total_number_input',
                'answer_input',
                'objective',
                'building_condition',
                'number_we',
                'number_stories',
                'living_space',
                'unusable_space',
                'number_people',
                'installation_location',
                'installation_location_extra',
                'info',
                'wallbox_location',
                'is_owner',
                'is_living_inside',
                'income',
                'insolation',
                'insolation_thickness',
                'insolation_type',
                'insolation_matarial',
                'insolation_age',
                'usage_type',
                'income_taxed',
                'heating_age_group',
                'natural_refrigerant',
                'investment_costs',
                'calculated_subsidy',
                'calculated_credit_need',
                'calculated_rate',
                'recommended_program',
                'subsidy_quote',
                'number_self_used',
                'tile_name',
                'appointment_by',
                'owner_count',
                'external_insulation_thickness',
                'masonry',
                'window_glazing',
                'window_frame',
                'window_year',
                'door_year',
                'door_condition',
                'project_date',
                'stage',
                'status',
                'solar_module_kwp',
                'has_pump_upgrade',
                'balcony_modules',
                'hydraulic_only',
                'object_name',
                'solar_thermal',
                'solar_thermal_area',
                'solar_thermal_simulation',
                'address_no',
                'main',
                'lat',
                'lon',
                'pipe_system_count',
                'total_electricity_consumption',
                'wallbox_count',
                'heavy_current_cable',
                'network_cable',
                'groundwork',
                'company_vehicle',
                'bidirectional_car',
                'power_household',
                'power_heatpump',
                'power_electric_car',
                'power_other',
                'power_total',
                'meter_cabinet',
                'meter_count',
                'tenant_model',
                'installation_location_power',
                'network_wlan',
            ];

            $alternative->fill($request->only($altFields))->save();

            // Products: update or create, and do not reference undefined variables
            foreach ($validated['product'] as $item) {
                $serviceName = null;
                if (!empty($item['service_id'])) {
                    $serviceName = optional(PhaseSection::find($item['service_id']))->phase_section;
                }

                $prod = LeadProductList::updateOrCreate(
                    [
                        'customer_id' => $validated['id'],
                        'alternative_id' => $validated['alternative_id'],
                        'product_id' => $item['product_id'],
                    ],
                    [
                        'service_id' => $item['service_id'] ?? null,
                        'department_id' => $item['department_id'] ?? null,
                        'employee_id' => $item['employee_id'] ?? null,
                        'field_employee' => $item['field_employee'] ?? null,
                        'interest' => $item['interest'] ?? null,
                        'realization_time' => $item['realization_time'] ?? null,
                        'service' => $serviceName,
                        'status' => 'open',
                    ]
                );

                // If you want activity logs here, use $prod->id (never $prod undefined)
                // $this->logActivity('updated', LeadProductList::class, $prod->id, $validated['id'], $validated['alternative_id'], $item['product_id'], ['info' => 'Produkt aktualisiert']);
            }

            // Screenshot
            if ($request->hasFile('screenshot_file')) {
                $file = $request->file('screenshot_file');
                $imageName = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('/uploads'), $imageName);

                Image::create([
                    'customer_id' => $validated['id'],
                    'alternative_id' => $validated['alternative_id'],
                    'image_name' => $imageName,
                    'image' => $imageName,
                    'stage' => 'customer',
                    'status' => 'screenshot',
                    'file_type' => $file->getClientOriginalExtension(),
                    'created_by' => auth()->user()->name ?? 'system',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Der Kunde wurde erfolgreich aktualisiert.',
                'redirect' => url('new_lead_profile/' . $validated['id']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Fehler beim Speichern des Kunden:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Speichern des Kunden.',
            ], 500);
        }
    }
    private function alternativeAddress()
    {
        do {
            $address_no = mt_rand(1000000, 9999999);
        } while (DB::table('lead_alternative_adds')->where('address_no', $address_no)->exists());
        return $address_no;
    }

    private function customerAlternativeAddress()
    {
        do {
            $address_no = mt_rand(1000000, 9999999);
        } while (DB::table('customer_alternative_adds')->where('address_no', $address_no)->exists());
        return $address_no;
    }

    public function product($id)
    {

        $data['articles'] = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select('article_groups.article_group', 'article_groups.image', 'article_groups.initial', 'new_leads.*')
            ->where('new_leads.id', $id)
            ->get();

        $data['employees'] = DB::table('employee_departments')
            ->join('employees', 'employees.id', '=', 'employee_departments.employee_id')
            ->join('departments', 'departments.id', '=', 'employee_departments.department_id')
            ->select('employees.id as emp_id', 'employees.name', 'employees.lastname', 'employees.image', 'departments.department_name', 'departments.id as dept_id')
            ->where('employees.status', '=', 'Active')
            ->where('departments.status', '=', 'Published')
            ->get();
        $data['positions'] = DB::table('department_positions')
            ->join('departments', 'departments.id', '=', 'department_positions.department_id')
            ->join('positions', 'positions.id', '=', 'department_positions.position_id')
            ->select('positions.position', 'departments.department_name', 'positions.id as position_id', 'departments.id as dept_id')
            ->get();

        return view('admin.new_leads.lead_product', $data);
    }

    public function delete_photo($photo)
    {

        if (!empty($photo)) {
            $photo_path = 'images/leads/home/' . $photo;

            if (file_exists($photo_path)) {
                unlink($photo_path);
            }
        }
    }


    public function qualified($id)
    {
        $data = NewLeads::find($id);

        if (!$data) {
            return redirect()->back()->with('delete_msg', 'Kunde nicht gefunden.');
        }

        $productList = DB::table('lead_product_lists')
            ->where('customer_id', '=', $id)
            ->first();

        if (!$productList) {
            $data->status = "KEIN PRODUKT AUSGEWÄHLT, BITTE PRODUKTAUSWAHL ERMITTELN";
            $data->save();
            return redirect()->back()->with('delete_msg', 'Der Kunde hat das Produkt nicht. Bitte wählen Sie das Produkt vor der Qualifizierung aus.');
        }

        $phone = $data->phone;
        $telephone = $data->telephone;
        $email = $data->email;
        $address = $data->street;
        $postcode = $data->postcode;
        $city = $data->city;

        if (!$address || !$postcode || !$city) {
            $data->status = "KEINE KONTAKTDATEN";
            $data->save();
            return redirect()->back()->with('delete_msg', 'Der Kunde hat keine vollständige Adresse, bitte geben Sie die Adresse ein.');
        }

        if (!$email && !$phone && !$telephone) {
            $data->status = "um zu qualifizieren, bitte per Brief  Kontakt aufnehmen";
            $data->save();
            return redirect()->back()->with('delete_msg', 'Um zu qualifizieren, bitte per Brief  Kontakt aufnehmen.');
        } elseif (!$email) {
            $data->status = "um zu qualifizieren, bitte telefonisch  Kontakt aufnehmen";
            $data->save();
            return redirect()->back()->with('delete_msg', 'Um zu qualifizieren, bitte telefonisch  Kontakt aufnehmen.');
        } elseif (!$phone && !$telephone) {
            $data->status = "um zu qualifizieren, bitte per E-Mail  Kontakt aufnehmen";
            $data->save();
            return redirect()->back()->with('delete_msg', 'Um zu qualifizieren, bitte per E-Mail Kontakt aufnehmen.');
        }

        $data->status = 'QUALIFIZIERT';
        $data->save();
        return redirect()->back()->with('save_msg', 'Der Kunde ist qualifiziert.');
    }


    public function destroy($id)
    {

        $data = NewLeads::find($id);
        $data->delete();
        return back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }

    public function delete_alternative($id)
    {
        $data = LeadAlternativeAdd::find($id); // Find the record by ID

        if ($data) {
            $data->delete(); // Delete the record
            return back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
        }

        return back()->with('delete_msg', 'Datensatz nicht gefunden');
    }

    public function oldcheck(Request $request)
    {
        $productId = $request->input('product_id');

        // Log the product ID to verify it's being received
        \Log::info('Received product ID: ' . $productId);

        // Get the product's department and position
        $productPosition = DB::table('product_positions')
            ->where('product_id', $productId)
            ->first();

        if (!$productPosition) {
            \Log::warning('Product position not found for product ID: ' . $productId);
            return response()->json(['employees' => []]);
        }

        \Log::info('Product position found:', ['position_id' => $productPosition->position_id]);

        // Find employees in the same department and position
        $employees = DB::table('employee_departments')
            ->join('employees', 'employees.id', '=', 'employee_departments.employee_id')
            ->join('departments', 'departments.id', '=', 'employee_departments.department_id')
            ->LeftJoin('positions', 'departments.id', '=', 'positions.department_id')
            ->leftJoin('product_positions', 'product_positions.position_id', '=', 'positions.id')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'product_positions.product_id')
            ->leftJoin('task_phases', 'article_groups.id', '=', 'task_phases.product_id')
            ->select('employees.id', 'employees.name', 'employees.lastname', 'employees.image', 'task_phases.phase_name', 'positions.position', 'departments.department_name', 'article_groups.article_group')
            ->where('article_groups.id', $productId)
            ->where('task_phases.phase_name', '=', 'Anfrage')
            ->get();

        // Check if employees are on leave
        $employeeIds = $employees->pluck('id')->toArray();
        \Log::debug('Employee IDs: ' . json_encode($employeeIds));

        $leave_check = DB::table('leaves')
            ->join('employees', 'employees.id', '=', 'leaves.emp_id')
            ->whereIn('employees.id', $employeeIds)
            ->where('leaves.approved', '=', 'Yes')
            ->where(function ($query) {
                $query->where('leaves.start_date', '<=', Carbon::now())
                    ->where('leaves.end_date', '>=', Carbon::now());
            })
            ->select('leaves.id', 'leaves.start_date', 'leaves.end_date', 'employees.name', 'employees.id as employee_id')
            ->get();

        \Log::debug('Leave Check: ' . json_encode($leave_check));


        $availableEmployees = $employees->filter(function ($employee) use ($leave_check) {
            return !$leave_check->pluck('employee_id')->contains($employee->id);
        });

        $representer_check = [];

        if ($leave_check->isNotEmpty()) {
            $leave_employee_id = $leave_check->pluck('employee_id')->toArray();
            \Log::debug('Leave Employee IDs: ' . json_encode($leave_employee_id));

            $representer_check = DB::table('job_representatives')
                ->join('employees', 'employees.id', '=', 'job_representatives.employee_id')
                ->join('departments', 'departments.id', '=', 'job_representatives.department_id')
                ->join('positions', 'positions.id', '=', 'job_representatives.position_id')
                ->join('employees as representer', 'representer.id', '=', 'job_representatives.representer_id')
                ->join('employees as current', 'current.id', '=', 'job_representatives.current_representer')
                ->select(
                    'job_representatives.*',
                    'departments.department_name',
                    'positions.position',
                    'employees.name',
                    'employees.lastname',
                    'representer.name as represent_name',
                    'representer.lastname as represent_lastname',
                    'current.name as current_name',
                    'current.lastname as current_lastname'
                )
                ->whereIn('job_representatives.employee_id', $leave_employee_id)
                ->get();

            \Log::debug('Representer Check: ' . json_encode($representer_check));
        }

        \Log::info('Found employees:', $employees->toArray());
        \Log::info('Available employees:', $availableEmployees->toArray());

        return response()->json(['employees' => $availableEmployees, 'representatives' => $representer_check]);
    }

    public function view($id)
    {
        /*
        |--------------------------------------------------------------------------
        | Small Helpers
        |--------------------------------------------------------------------------
        */
        $hasTable = function (string $table): bool {
            try {
                return \Illuminate\Support\Facades\Schema::hasTable($table);
            } catch (\Throwable $e) {
                return false;
            }
        };

        $hasColumn = function (string $table, string $column): bool {
            try {
                return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
            } catch (\Throwable $e) {
                return false;
            }
        };

        /*
        |--------------------------------------------------------------------------
        | 1. Customer / Lead
        |--------------------------------------------------------------------------
        */
        /** @var \App\Models\NewLeads|null $customer */
        $customer = \App\Models\NewLeads::query()->find($id);

        if (!$customer) {
            return redirect()
                ->back()
                ->with('delete_msg', 'Der Kunde ist nicht im System');
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Recalculate Total Purchase
        |--------------------------------------------------------------------------
        */
        $leadProductTotalQuery = DB::table('lead_product_lists')
            ->where('customer_id', $customer->id);

        if ($hasColumn('lead_product_lists', 'deleted_at')) {
            $leadProductTotalQuery->whereNull('deleted_at');
        }

        $totalFromProducts = (float) $leadProductTotalQuery->sum('price');

        if ((float) ($customer->total_purchase ?? 0) !== $totalFromProducts) {
            $customer->total_purchase = $totalFromProducts;

            if ($totalFromProducts > 0 && empty($customer->purchase_date)) {
                $customer->purchase_date = now()->toDateString();
            }

            $customer->save();
            $customer->refresh();
        }

        $item = [];
        $item['customer'] = $customer;

        /*
        |--------------------------------------------------------------------------
        | 3. Employees / Current User
        |--------------------------------------------------------------------------
        */
        $item['employees'] = DB::table('employees')
            ->where('status', 'Active')
            ->get();

        $item['current_user'] = DB::table('employees')
            ->where('id', auth()->user()->name)
            ->select('id', 'name', 'lastname', 'image')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | 4. ToDos
        |--------------------------------------------------------------------------
        */
        $item['to_does'] = DB::table('task_to_dos')
            ->leftJoin('employees as responsible', 'responsible.id', '=', 'task_to_dos.responsible_person')
            ->leftJoin('employees as contact', 'contact.id', '=', 'task_to_dos.contact_person')
            ->leftJoin('employees as outside_s', 'outside_s.id', '=', 'task_to_dos.outside_service')
            ->leftJoin('external_personals as outside_c', 'outside_c.id', '=', 'task_to_dos.outside_company')
            ->where('task_to_dos.customer_id', $customer->id)
            ->select(
                'task_to_dos.*',
                'responsible.name as rname',
                'responsible.lastname as rlastname',
                'responsible.image as rimage',
                'contact.name as cname',
                'contact.lastname as clastname',
                'contact.image as cimage',
                'outside_s.name as osname',
                'outside_s.lastname as oslastname',
                'outside_s.image as osimage',
                'outside_c.admin_name',
                'outside_c.company_name'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 5. Products for Sidebar / Profile
        |--------------------------------------------------------------------------
        */
        $productsQuery = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->leftJoin('employees', 'employees.id', '=', 'lead_product_lists.employee_id')
            ->select(
                'article_groups.image',
                'article_groups.article_group',
                'article_groups.initial',
                'lead_product_lists.id as p_id',
                'lead_product_lists.id as p_list_id',
                'lead_product_lists.alternative_id',
                'lead_product_lists.customer_id',
                'lead_product_lists.product_id',
                'lead_product_lists.service_id',
                'lead_product_lists.department_id',
                'lead_product_lists.employee_id',
                'lead_product_lists.service',
                'lead_product_lists.status',
                'lead_product_lists.stage',
                'lead_product_lists.price',
                'departments.department_name',
                'phase_sections.phase_section',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image'
            )
            ->where('new_leads.id', $customer->id);

        if ($hasColumn('lead_product_lists', 'deleted_at')) {
            $productsQuery->whereNull('lead_product_lists.deleted_at');
        }

        $item['products'] = $productsQuery->get();

        /*
        |--------------------------------------------------------------------------
        | 6. Product List Unique
        |--------------------------------------------------------------------------
        */
        $productListQuery = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->leftJoin('new_lead_responsibilities as res', function ($join) {
                $join->on('res.product_id', '=', 'lead_product_lists.product_id')
                    ->on('res.new_lead_id', '=', 'new_leads.id');
            })
            ->select(
                'article_groups.initial',
                'new_leads.id as customer_id',
                'article_groups.id as product_id',
                'lead_product_lists.status',
                'lead_product_lists.stage',
                'lead_product_lists.service',
                'article_groups.article_group',
                'res.current_employee',
                'res.status as res_status',
                'lead_product_lists.price',
                'res.reason',
                'res.id as responsible_id',
                'lead_product_lists.id as p_list_id',
                'lead_product_lists.alternative_id'
            )
            ->where('new_leads.id', $customer->id);

        if ($hasColumn('lead_product_lists', 'deleted_at')) {
            $productListQuery->whereNull('lead_product_lists.deleted_at');
        }

        $productList = $productListQuery->get();

        $item['productList'] = $productList
            ->unique(function ($row) {
                return ($row->alternative_id ?? 0) . '_' . ($row->product_id ?? 0);
            })
            ->values();

        $item['productEmployees'] = DB::table('employees')
            ->select('id', 'name', 'lastname', 'image', 'status', 'gender')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 7. Alternative Objects
        |--------------------------------------------------------------------------
        */
        $search = request()->query('search');

        $altQuery = DB::table('lead_alternative_adds')
            ->where('lead_id', $customer->id);

        if ($hasColumn('lead_alternative_adds', 'deleted_at')) {
            $altQuery->whereNull('deleted_at');
        }

        if ($search) {
            $altQuery->where(function ($q) use ($search) {
                $q->where('object_name', 'LIKE', "%{$search}%")
                    ->orWhere('street', 'LIKE', "%{$search}%")
                    ->orWhere('postcode', 'LIKE', "%{$search}%")
                    ->orWhere('city', 'LIKE', "%{$search}%");
            });
        }

        $item['alternative'] = $altQuery->get();

        /*
        |--------------------------------------------------------------------------
        | 8. Tickets / Problems
        |--------------------------------------------------------------------------
        */
        $item['tickets'] = DB::table('problems')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'problems.product_id')
            ->select('problems.*', 'article_groups.initial')
            ->where('problems.customer_id', $customer->id)
            ->get();

        $item['problems'] = DB::table('error_problem')
            ->join('errors', 'errors.id', '=', 'error_problem.error_id')
            ->select('error_problem.*', 'errors.problem_types')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 9. Images / Documents
        |--------------------------------------------------------------------------
        */
        $item['images'] = \App\Models\Image::query()
            ->where('customer_id', $customer->id)
            ->get();

        $images = DB::table('images')
            ->leftJoin('article_groups', 'images.article_group', '=', 'article_groups.id')
            ->where('images.customer_id', $customer->id)
            ->select('images.*', 'article_groups.article_group')
            ->get();

        $item['image_p_sort'] = $images
            ->groupBy('article_group')
            ->map(function ($group) {
                return $group->toArray();
            })
            ->toArray();

        $item['image_c_sort'] = [
            'customer' => DB::table('images')
                ->where('customer_id', $customer->id)
                ->where('stage', 'customer')
                ->get()
                ->toArray(),
        ];

        $item['image_m_sort'] = [
            'montage' => DB::table('images')
                ->where('customer_id', $customer->id)
                ->where('stage', 'montage')
                ->get()
                ->toArray(),
        ];

        $item['image_e_sort'] = [
            'end' => DB::table('images')
                ->where('customer_id', $customer->id)
                ->where('stage', 'end')
                ->get()
                ->toArray(),
        ];

        $item['screenshots'] = DB::table('images')
            ->where('customer_id', $customer->id)
            ->where('status', 'screenshot')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 10. Other Modules
        |--------------------------------------------------------------------------
        */
        $item['rediators'] = class_exists(\App\Models\RadiatorInstallation::class)
            ? \App\Models\RadiatorInstallation::where('customer_id', $customer->id)->get()
            : collect();

        $item['tiles'] = DB::table('product_images')
            ->join('products', 'products.id', '=', 'product_images.product_id')
            ->select('product_images.*', 'products.category', 'products.product', 'products.roof_type')
            ->where('products.category', 'Dachziegel')
            ->get();

        $item['electro'] = DB::table('brands')
            ->where('purpose', 'ELEKTRO')
            ->get();

        $item['heating_types'] = class_exists(\App\Models\HeatingType::class)
            ? \App\Models\HeatingType::where('status', 'Published')->get()
            : collect();

        $item['wp'] = DB::table('w_p_checklists')
            ->where('customer_id', $customer->id)
            ->first();

        $item['cold_water'] = DB::table('customer_w_p_cables')
            ->where('customer_id', $customer->id)
            ->where('system', 'Kalt-Wasser')
            ->first();

        $item['warm_water'] = DB::table('customer_w_p_cables')
            ->where('customer_id', $customer->id)
            ->where('system', 'Warm-Wasser')
            ->first();

        $item['circulation'] = DB::table('customer_w_p_cables')
            ->where('customer_id', $customer->id)
            ->where('system', 'Zirkulation')
            ->first();

        $item['heating'] = DB::table('customer_w_p_cables')
            ->where('customer_id', $customer->id)
            ->where('system', 'Heizung')
            ->first();

        $item['meter_cabinet'] = DB::table('customer_meter_cabinets')
            ->where('customer_id', $customer->id)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | 11. Product Count
        |--------------------------------------------------------------------------
        */
        $productCountQuery = DB::table('lead_product_lists')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'lead_product_lists.alternative_id')
            ->select('lead_product_lists.*', 'article_groups.initial');

        if ($hasColumn('lead_product_lists', 'deleted_at')) {
            $productCountQuery->whereNull('lead_product_lists.deleted_at');
        }

        if ($hasColumn('lead_alternative_adds', 'deleted_at')) {
            $productCountQuery->whereNull('lead_alternative_adds.deleted_at');
        }

        $item['productcount'] = $productCountQuery->get();

        /*
        |--------------------------------------------------------------------------
        | 12. Contact People
        |--------------------------------------------------------------------------
        */
        $contactPeopleQuery = DB::table('customer_contact_people')
            ->where('customer_id', $customer->id);

        if ($hasColumn('customer_contact_people', 'deleted_at')) {
            $contactPeopleQuery->whereNull('deleted_at');
        }

        $item['contactPeople'] = $contactPeopleQuery
            ->select(
                'customer_id',
                'alternative_id',
                'relation',
                'name',
                'lastname',
                'phone',
                'office',
                'home',
                'email'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 13. Roofs / Departments / New Data
        |--------------------------------------------------------------------------
        */
        $item['roofs'] = class_exists(\App\Models\PVRoof::class)
            ? \App\Models\PVRoof::where('customer_id', $customer->id)->get()
            : collect();

        $item['departments'] = \App\Models\Department::query()
            ->where('status', 'published')
            ->orderBy('department_name')
            ->get(['id', 'department_name']);

        $item['new_employees'] = \App\Models\Employee::query()
            ->orderBy('name')
            ->get(['id', 'name', 'lastname', 'image', 'gender']);

        $item['new_products'] = DB::table('article_groups')
            ->select('id', 'article_group', 'image')
            ->orderBy('article_group')
            ->get();

        $newServicesQuery = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section');

        if ($hasColumn('phase_sections', 'deleted_at')) {
            $newServicesQuery->whereNull('deleted_at');
        }

        $item['new_services'] = $newServicesQuery
            ->orderBy('phase_section')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 14. Review Summary
        |--------------------------------------------------------------------------
        */
        $reviewSummary = null;

        if ($hasTable('customer_reviews')) {
            $reviewQuery = \App\Models\CustomerReview::query()
                ->where('customer_id', $customer->id);

            if ($hasColumn('customer_reviews', 'deleted_at')) {
                $reviewQuery->whereNull('deleted_at');
            }

            $reviewSummary = $reviewQuery
                ->selectRaw('COUNT(*) as total_reviews')
                ->selectRaw($hasColumn('customer_reviews', 'stars') ? 'AVG(stars) as avg_stars' : '0 as avg_stars')
                ->selectRaw($hasColumn('customer_reviews', 'is_critical') ? 'SUM(CASE WHEN is_critical = 1 THEN 1 ELSE 0 END) as critical_reviews' : '0 as critical_reviews')
                ->first();
        }

        $item['customerReviewSummary'] = [
            'total' => (int) ($reviewSummary->total_reviews ?? 0),
            'avg' => round((float) ($reviewSummary->avg_stars ?? 0), 1),
            'critical' => (int) ($reviewSummary->critical_reviews ?? 0),
        ];

        /*
        |--------------------------------------------------------------------------
        | 15. KPI Analytics
        |--------------------------------------------------------------------------
        */
        $allImages = \App\Models\Image::query()
            ->where('customer_id', $customer->id)
            ->get();

        $allOffers = collect();

        if (class_exists(\App\Models\Offer::class)) {
            $offerQuery = \App\Models\Offer::query()
                ->where('customer_id', $customer->id);

            try {
                $offerQuery->with('folderAttachments');
            } catch (\Throwable $e) {
                // Relation may not exist in some installs.
            }

            $allOffers = $offerQuery->get();
        }

        $allInvoices = collect();

        try {
            if (method_exists($customer, 'invoices')) {
                $allInvoices = $customer->invoices()->get();
            } elseif ($hasTable('invoices')) {
                $allInvoices = DB::table('invoices')
                    ->where('customer_id', $customer->id)
                    ->get();
            }
        } catch (\Throwable $e) {
            $allInvoices = collect();
        }

        $allTickets = DB::table('problems')
            ->where('customer_id', $customer->id)
            ->get();

        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        $totalPhotos = 0;
        $totalDocs = 0;

        foreach ($allImages as $img) {
            $fileType = strtolower((string) ($img->file_type ?? ''));
            $imageName = strtolower((string) ($img->image ?? ''));
            $ext = pathinfo($imageName, PATHINFO_EXTENSION);

            if (in_array($fileType, $imageExtensions, true) || in_array($ext, $imageExtensions, true)) {
                $totalPhotos++;
            } else {
                $totalDocs++;
            }
        }

        $totalOfferDocs = $allOffers->sum(function ($offer) {
            try {
                return $offer->folderAttachments ? $offer->folderAttachments->count() : 0;
            } catch (\Throwable $e) {
                return 0;
            }
        });

        $totalDocs += (int) $totalOfferDocs;

        $item['kpi_analytics'] = [
            'photos' => (int) $totalPhotos,
            'documents' => (int) $totalDocs,
            'rechnung' => (int) $allInvoices->count(),
            'angebot' => (int) $allOffers->count(),
            'ticket' => (int) $allTickets->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | 16. KPI Details Map Per Object + Product
        |--------------------------------------------------------------------------
        */
        $kpiDetailsMap = [];

        foreach ($item['productList'] as $pl) {
            $aid = (int) ($pl->alternative_id ?? 0);
            $pid = (int) ($pl->product_id ?? 0);
            $key = $aid . '_' . $pid;

            $pImgs = $allImages
                ->where('alternative_id', $aid)
                ->where('article_group', $pid);

            $pPhotos = $pImgs->filter(function ($img) use ($imageExtensions) {
                $fileType = strtolower((string) ($img->file_type ?? ''));
                $imageName = strtolower((string) ($img->image ?? ''));
                $ext = pathinfo($imageName, PATHINFO_EXTENSION);

                return in_array($fileType, $imageExtensions, true) || in_array($ext, $imageExtensions, true);
            })->count();

            $pDocs = $pImgs->count() - $pPhotos;

            $pOfferList = $allOffers->filter(function ($offer) use ($aid, $pid) {
                return (int) ($offer->alternative_id ?? 0) === $aid
                    && (int) ($offer->product_id ?? 0) === $pid;
            });

            $pOffers = $pOfferList->count();

            $pOfferDocs = $pOfferList->sum(function ($offer) {
                try {
                    return $offer->folderAttachments ? $offer->folderAttachments->count() : 0;
                } catch (\Throwable $e) {
                    return 0;
                }
            });

            $pDocs += (int) $pOfferDocs;

            $pInvoices = $allInvoices->filter(function ($inv) use ($aid, $pid) {
                $objectId = (int) ($inv->object_id ?? 0);
                $alternativeId = (int) ($inv->alternative_id ?? 0);
                $productId = (int) ($inv->product_id ?? 0);

                return ($objectId === $aid || $alternativeId === $aid) && $productId === $pid;
            })->count();

            $pTickets = $allTickets
                ->where('alternative_id', $aid)
                ->where('product_id', $pid)
                ->count();

            $kpiDetailsMap[$key] = [
                'photos' => (int) $pPhotos,
                'documents' => (int) $pDocs,
                'rechnung' => (int) $pInvoices,
                'angebot' => (int) $pOffers,
                'ticket' => (int) $pTickets,
            ];
        }

        $item['kpiDetailsMap'] = $kpiDetailsMap;

        /*
        |--------------------------------------------------------------------------
        | 17. Sidebar Counts Default Map
        |--------------------------------------------------------------------------
        | This prevents undefined $sidebarCounts errors in Blade.
        |--------------------------------------------------------------------------
        */
        $item['sidebarCounts'] = [];

        foreach ($item['products'] as $product) {
            $cid = (int) ($product->customer_id ?? $customer->id);
            $aid = (int) ($product->alternative_id ?? 0);
            $pid = (int) ($product->product_id ?? 0);
            $key = $cid . '_' . $aid . '_' . $pid;
            $kpiKey = $aid . '_' . $pid;

            $item['sidebarCounts'][$key] = [
                'documents' => (int) (($kpiDetailsMap[$kpiKey]['photos'] ?? 0) + ($kpiDetailsMap[$kpiKey]['documents'] ?? 0)),
                'checklist' => 0,
                'tasks' => 0,
                'offers' => (int) ($kpiDetailsMap[$kpiKey]['angebot'] ?? 0),
                'orders' => 0,
                'projects' => 0,
                'invoices' => (int) ($kpiDetailsMap[$kpiKey]['rechnung'] ?? 0),
                'invoice_total_amount' => 0,
                'customer_product' => 0,
                'appointments' => 0,
                'tickets' => (int) ($kpiDetailsMap[$kpiKey]['ticket'] ?? 0),
                'reviews' => 0,
                'critical_reviews' => 0,
                'reviews_avg_stars' => 0,
                'history' => 0,
                'stages' => 0,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Render View
        |--------------------------------------------------------------------------
        */
        return view('admin.new_leads.customer_profile', $item);
    }

    public function customerProfileFeed(Request $request, $id)
    {
        // simply reuse existing logic
        return $this->customerFeed($request, $id);
    }




    public function object_profile($id, $alternative)
    {
        // 1. Validate Customer
        $customer = NewLeads::find($id);
        if (!$customer) {
            return redirect()->back()->with('delete_msg', 'Der Kunde ist nicht im System');
        }

        $item = [];
        $item['customer'] = $customer;

        // 2. Base Data (Employees, User, Externals)
        $item['employees'] = DB::table('employees')->where('status', 'Active')->get();
        $item['current_user'] = DB::table('employees')->where('id', Auth::user()->name)->select('id', 'name', 'lastname', 'image')->first();
        $item['outside'] = DB::table('external_personals')->where('status', 'Published')->get();

        // 3. To-Dos
        $item['to_does'] = DB::table('task_to_dos')
            ->leftJoin('employees as responsible', 'responsible.id', '=', 'task_to_dos.responsible_person')
            ->leftJoin('employees as contact', 'contact.id', '=', 'task_to_dos.contact_person')
            ->leftJoin('employees as outside_s', 'outside_s.id', '=', 'task_to_dos.outside_service')
            ->leftJoin('external_personals as outside_c', 'outside_c.id', '=', 'task_to_dos.outside_company')
            ->where('task_to_dos.customer_id', $id)
            ->select(
                'task_to_dos.*',
                'responsible.name as rname',
                'responsible.lastname as rlastname',
                'responsible.image as rimage',
                'contact.name as cname',
                'contact.lastname as clastname',
                'contact.image as cimage',
                'outside_s.name as osname',
                'outside_s.lastname as oslastname',
                'outside_s.image as osimage',
                'outside_c.admin_name',
                'outside_c.company_name'
            )
            ->get();

        // 4. Tasks & Activities (Global lists)
        $item['tasks'] = DB::table('phase_activities')->orderBy('sort_order')->get();
        $item['activities'] = DB::table('task_sub_tasks')->where('status', 'published')->get();

        // 5. The Specific Alternative (Object)
        $item['alternative'] = DB::table('lead_alternative_adds')
            ->where('lead_id', $id)
            ->where('id', $alternative)
            ->whereNull('deleted_at')
            ->first();

        if (!$item['alternative']) {
            return redirect()->back()->with('delete_msg', 'Objekt nicht gefunden');
        }

        // 6. Products associated with this Object
        $item['products'] = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->where('lead_product_lists.customer_id', $id)
            ->where('lead_product_lists.alternative_id', $alternative)
            ->whereNull('lead_product_lists.deleted_at')
            ->select('article_groups.*')
            ->distinct()
            ->get();

        // 7. Detailed Product List with Responsibilities
        $productList = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('new_lead_responsibilities as res', function ($join) use ($id) {
                $join->on('res.product_id', '=', 'lead_product_lists.product_id')
                    ->where('res.new_lead_id', '=', $id);
            })
            ->where('lead_product_lists.customer_id', $id)
            ->where('lead_product_lists.alternative_id', $alternative)
            ->whereNull('lead_product_lists.deleted_at')
            ->select(
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id',
                'lead_product_lists.id as p_list_id',
                'lead_product_lists.customer_id',
                'lead_product_lists.alternative_id',
                'lead_product_lists.status',
                'lead_product_lists.service',
                'lead_product_lists.price',
                'res.current_employee',
                'res.status as res_status',
                'res.reason',
                'res.id as responsible_id'
            )
            ->get();

        // Filter unique by product_id if necessary (logic from your code)
        $item['productList'] = $productList->unique('product_id')->values();

        $item['productEmployees'] = DB::table('employees')
            ->select('id', 'name', 'lastname', 'image', 'status', 'gender')
            ->get();

        // 8. Tickets & Problems
        $item['tickets'] = DB::table('problems')
            ->join('article_groups', 'article_groups.id', '=', 'problems.product_id')
            ->select('problems.*', 'article_groups.initial')
            ->where('customer_id', $id)
            ->get();

        $item['problems'] = DB::table('error_problem')
            ->join('errors', 'errors.id', '=', 'error_problem.error_id')
            ->select('error_problem.*', 'errors.problem_types')
            ->get();

        // 9. Images (Sorted by Group and Stage)
        $allImages = Image::where('customer_id', $id)->get();
        $item['images'] = $allImages;

        // Group by Article Group
        $imagesWithProduct = DB::table('images')
            ->join('article_groups', 'images.article_group', '=', 'article_groups.id')
            ->where('images.customer_id', $id)
            ->select('images.*', 'article_groups.article_group')
            ->get();

        $item['image_p_sort'] = $imagesWithProduct->groupBy('article_group')->toArray();

        // Group by Stage
        $item['image_c_sort'] = ['customer' => $allImages->where('stage', 'customer')->values()->toArray()];
        $item['image_m_sort'] = ['montage' => $allImages->where('stage', 'montage')->values()->toArray()];
        $item['image_e_sort'] = ['end' => $allImages->where('stage', 'end')->values()->toArray()];

        // 10. Checklists & Technical Data
        $item['rediators'] = RadiatorInstallation::where('customer_id', $id)->get();

        $item['tiles'] = DB::table('product_images')
            ->join('products', 'products.id', '=', 'product_images.product_id')
            ->select('product_images.*', 'products.category', 'products.product', 'products.roof_type')
            ->where('products.category', 'Dachziegel')
            ->get();

        $item['electro'] = DB::table('brands')->where('purpose', 'ELEKTRO')->get();
        $item['heating_types'] = HeatingType::where('status', 'Published')->get();

        // Specific Checklists for this Customer/Alternative
        $item['wp_checklist'] = DB::table('w_p_checklists')
            ->where('customer_id', $id)
            // ->where('alternative_id', $alternative) // Uncomment if WP checklist is per object
            ->first();

        $item['pv_checklist'] = DB::table('p_v_checklists')->where('customer_id', $id)->first();

        $item['pv_roof'] = PVRoof::where('customer_id', $id)
            ->where('alternative_id', $alternative)
            ->get();

        // Cables & Meter
        $cableQuery = DB::table('customer_w_p_cables')->where('customer_id', $id); // Add alternative_id check if needed

        $item['cold_water'] = (clone $cableQuery)->where('system', 'Kalt-Wasser')->first();
        $item['warm_water'] = (clone $cableQuery)->where('system', 'Warm-Wasser')->first();
        $item['circulation'] = (clone $cableQuery)->where('system', 'Zirkulation')->first();
        $item['heating'] = (clone $cableQuery)->where('system', 'Heizung')->first();

        $item['meter_cabinet'] = DB::table('customer_meter_cabinets')->where('customer_id', $id)->first();

        // 11. Screenshots
        $item['screenshots'] = Image::where('customer_id', $id)
            ->where('status', 'screenshot')
            ->get();

        // 12. Product Count Summary
        $item['productcount'] = DB::table('lead_product_lists')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'lead_product_lists.alternative_id')
            ->select('lead_product_lists.*', 'article_groups.initial')
            ->whereNull('lead_alternative_adds.deleted_at')
            ->whereNull('lead_product_lists.deleted_at')
            ->where('lead_product_lists.customer_id', $id)
            ->get();

        // 13. Contact People
        $item['contactPeople'] = DB::table('customer_contact_people')
            ->where('customer_id', $id)
            ->whereNull('deleted_at')
            ->get();

        return view('admin.new_leads.customer_object_profile', $item);
    }


    public function deleteResponsible(Request $request)
    {
        try {
            $id = $request->input('new_lead_id');
            $employee_id = $request->input('employee_id');
            $product_id = $request->input('product_id');

            $data = DB::table('new_lead_responsibilities')
                ->where('new_lead_id', $id)
                ->where('employee_id', $employee_id)
                ->where('product_id', $product_id)
                ->first();

            if ($data) {
                // LOG
                $this->logActivity('deleted', 'NewLeadResponsibility', 0, $id, null, $product_id, [
                    'info' => 'Verantwortlicher Mitarbeiter entfernt',
                    'employee_id' => $employee_id
                ]);

                DB::table('new_lead_responsibilities')
                    ->where('new_lead_id', $id)
                    ->where('employee_id', $employee_id)
                    ->where('product_id', $product_id)
                    ->delete();

                return response()->json(['save_msg' => true, 'message' => 'Mitarbeiter erfolgreich gelöscht']);
            } else {
                return response()->json(['delete_msg' => false, 'message' => 'Mitarbeiter nicht gefunden']);
            }
        } catch (\Exception $e) {
            return response()->json(['delete_msg' => false, 'message' => 'Fehler aufgetreten']);
        }
    }
    public function updateFieldPV(Request $request)
    {
        \Log::info('checklist PV;', [$request->all()]);
        $request->validate([
            'customer_id' => 'required|exists:new_leads,id',
            'alternative_id' => 'required|exists:lead_alternative_adds,id',
            'field' => 'required|string',
            'value' => 'nullable|string'
        ]);

        $pvChecklist = PVChecklist::firstOrCreate([
            'customer_id' => $request->customer_id,
            'alternative_id' => $request->alternative_id
        ]);



        $pvChecklist->update([$request->field => $request->value]);


        $customer = LeadAlternativeAdd::find($request->alternative_id);
        $customer->update([$request->field => $request->value]);

        return response()->json(['success' => true, 'message' => 'Field updated successfully', 'data' => $pvChecklist]);
    }

    public function pv_edit($customer, $alternative)
    {
        $id = $customer;
        $item['customer'] = NewLeads::find($id);

        $item['employees'] = DB::table('employees')->where('status', 'Active')->get();

        $item['current_user'] = DB::table('employees')->where('id', '=', auth()->user()->name)->select('id', 'name', 'lastname', 'image')->first();
        $item['to_does'] = DB::table('task_to_dos')
            ->leftJoin('employees as responsible', 'responsible.id', 'task_to_dos.responsible_person')
            ->leftJoin('employees as contact', 'contact.id', 'task_to_dos.contact_person')
            ->leftJoin('employees as outside_s', 'outside_s.id', 'task_to_dos.outside_service')
            ->leftJoin('external_personals as outside_c', 'outside_c.id', 'task_to_dos.outside_company')
            ->where('task_to_dos.customer_id', '=', $id)
            ->select(
                'task_to_dos.*',
                'responsible.name as rname',
                'responsible.lastname as rlastname',
                'responsible.image as rimage',
                'contact.name as cname',
                'contact.lastname as clastname',
                'contact.image as cimage',
                'outside_s.name as osname',
                'outside_s.lastname as oslastname',
                'outside_s.image as osimage',
                'outside_c.admin_name',
                'outside_c.company_name'

            )
            ->get();
        $item['outside'] = DB::table('external_personals')->where('status', '=', 'Published')->get();
        $item['tasks'] = DB::table('phase_activities')->get(); //Needs where of product


        $item['activities'] = DB::table('task_sub_tasks')->where('status', '=', 'published')->get();
        $item['current_user'] = DB::table('employees')->where('id', '=', auth()->user()->name)->select('id', 'name', 'lastname', 'image')->first();


        $item['products'] = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select('article_groups.*')
            ->where('new_leads.id', $id)
            ->where('lead_product_lists.alternative_id', $alternative)
            ->get();


        // Product List
        $productList = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->leftJoin('new_lead_responsibilities as res', function ($join) {
                $join->on('res.product_id', '=', 'lead_product_lists.product_id')
                    ->on('res.new_lead_id', '=', 'new_leads.id');
            })
            ->select(
                'article_groups.initial',
                'new_leads.id as customer_id',
                'article_groups.id as product_id',
                'lead_product_lists.status',
                'lead_product_lists.service',
                'article_groups.article_group',
                'res.current_employee',
                'res.status as res_status',
                'res.reason',
                'res.id as responsible_id',
                'lead_product_lists.id as p_list_id',
                'lead_product_lists.alternative_id'
            )
            ->get();


        // Filter unique products
        $productListArray = $productList->toArray();
        $uniqueRecords = array_unique($productListArray, SORT_REGULAR);
        $item['productList'] = $uniqueRecords;


        $item['productEmployees'] = DB::table('employees')
            ->select('id', 'name', 'lastname', 'image', 'status', 'gender')
            ->get();
        $customer_product_count = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select('new_leads.name', 'new_leads.lastname', 'article_groups.article_group', 'lead_product_lists.*')
            ->get();



        $images = DB::table('new_lead_images')
            ->join('image_categories', 'image_categories.id', '=', 'new_lead_images.category_id')
            ->select('image_categories.category', 'new_lead_images.*')
            ->where('new_lead_images.lead_id', $id)
            ->first();



        $search = request()->query('search');

        $item['alternative'] = DB::table('lead_alternative_adds')
            ->where('lead_id', $id)
            ->where('id', $alternative)
            ->whereNull('deleted_at')
            ->first();

        $item['tickets'] = DB::table('problems')
            ->join('article_groups', 'article_groups.id', '=', 'problems.product_id')
            ->select('problems.*', 'article_groups.initial')
            ->where('customer_id', $id)
            ->get();
        $item['problems'] = DB::table('error_problem')
            ->join('errors', 'errors.id', '=', 'error_problem.error_id')
            ->select('error_problem.*', 'errors.problem_types')
            ->get();

        $item['images'] = Image::where('customer_id', $id)->get();
        // Fetch the images and join with article_groups
        $images = DB::table('article_groups')
            ->join('images', 'images.article_group', '=', 'article_groups.id')
            ->where('images.customer_id', $id)
            ->select('images.*', 'article_groups.article_group')
            ->get();
        // Group images by article_group
        $groupedImages = $images->groupBy('article_group');
        // Convert the grouped data into an array format (if needed for JSON response or further processing)
        $groupedImagesArray = $groupedImages->map(function ($group) {
            return $group->toArray();
        })->toArray();

        // Now $groupedImagesArray is structured by article_group with associated image data
        $item['image_p_sort'] = $groupedImagesArray;


        // Fetch images where stage is 'customer'
        $customerImages = DB::table('images')
            ->where('customer_id', $id)
            ->where('stage', 'customer')
            ->select('images.*')
            ->get();

        // Organize images under the 'customer' key
        $item['image_c_sort'] = [
            'customer' => $customerImages->toArray() // Convert to array for easy handling in Blade
        ];


        // Fetch images where stage is 'montage'
        $customerImages = DB::table('images')
            ->where('customer_id', $id)
            ->where('stage', 'montage')
            ->select('images.*')
            ->get();

        // Organize images under the 'customer' key
        $item['image_m_sort'] = [
            'montage' => $customerImages->toArray() // Convert to array for easy handling in Blade
        ];

        // Fetch images where stage is 'montage'
        $customerImages = DB::table('images')
            ->where('customer_id', $id)
            ->where('stage', 'end')
            ->select('images.*')
            ->get();

        // Organize images under the 'customer' key
        $item['image_e_sort'] = [
            'end' => $customerImages->toArray() // Convert to array for easy handling in Blade
        ];

        Image::where('customer_id', $id)->get();

        // Product checklist items 
        $item['rediators'] = RadiatorInstallation::where('customer_id', $id)->get();

        $item['tiles'] = DB::table('product_images')
            ->join('products', 'products.id', '=', 'product_images.product_id')
            ->select('product_images.*', 'products.id as product_id', 'products.category', 'products.product', 'products.roof_type')
            ->where('products.category', 'Dachziegel')
            ->get();

        $item['electro'] = DB::table('brands')
            ->where('purpose', 'ELEKTRO')
            ->get();
        $item['heating_types'] = HeatingType::where('status', 'Published')->get();


        $item['wp_checklist'] = DB::table('w_p_checklists')
            ->where('customer_id', $id)
            ->first();



        $item['pv_checklist'] = DB::table('p_v_checklists')
            ->where('customer_id', '=', $id)
            ->first();

        $item['pv_roof'] = DB::table('p_v_roofs')
            ->select('p_v_roofs.*')
            ->get();



        $item['cold_water'] = DB::table('customer_w_p_cables')
            ->where('customer_id', $id)
            ->where('system', '=', 'Kalt-Wasser')
            ->first();

        $item['warm_water'] = DB::table('customer_w_p_cables')
            ->where('customer_id', $id)
            ->where('system', '=', 'Warm-Wasser')
            ->first();

        $item['circulation'] = DB::table('customer_w_p_cables')
            ->where('customer_id', $id)
            ->where('system', '=', 'Zirkulation')
            ->first();

        $item['heating'] = DB::table('customer_w_p_cables')
            ->where('customer_id', $id)
            ->where('system', '=', 'Heizung')
            ->first();

        $item['meter_cabinet'] = DB::table('customer_meter_cabinets')
            ->where('customer_id', $id)
            ->first();

        $search = request()->query('search');
        $item['roofs'] = DB::table('p_v_roofs')
            ->leftJoin('products', 'products.id', '=', 'p_v_roofs.roof_covering')
            ->leftJoin('product_images', 'product_images.id', '=', 'p_v_roofs.roof_covering')
            ->select('p_v_roofs.*', 'products.product', 'product_images.image')
            ->where('p_v_roofs.customer_id', $id)
            ->where('p_v_roofs.alternative_id', $alternative)
            ->whereNull('deleted_at')
            ->when($search, function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('p_v_roofs.roof', 'LIKE', "%$search%")
                        ->orWhere('p_v_roofs.property_type', 'LIKE', "%$search%")
                        ->orWhere('products.product', 'LIKE', "%$search%")
                        ->orWhere('p_v_roofs.designation', 'LIKE', "%$search%");
                });
            })
            ->get();


        //Screenshot of customers

        $item['screenshots'] = DB::table('images')
            ->where('customer_id', $id)
            ->where('status', 'screenshot')
            ->get();
        $item['productcount'] = DB::table('lead_product_lists')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'lead_product_lists.alternative_id')
            ->select('lead_product_lists.*', 'article_groups.initial')
            ->whereNull('lead_alternative_adds.deleted_at') // Ensure soft-deleted leads are filtered
            ->get();

        return view('admin.new_leads.checklists.pv_edit', $item);
    }


    public function getPV($customer, $alternative)
    {
        $data = PVChecklist::where('customer_id', $customer)
            ->where('alternative_id', $alternative)
            ->first();

        return response()->json($data, 200);
    }
    // Adding new Employees responsible for products 
    public function saveSelectedEmployees(Request $request)
    {
        // Validate the request
        $validate = $request->validate([
            'lead_id' => 'required|exists:new_leads,id',
            'employee_id' => 'required|exists:employees,id',
            'product_id' => 'required|exists:article_groups,id',
            'employees' => 'required|exists:employees,id',
            'alternative_id' => 'required|exists:lead_alternative_adds,id',
            'id' => 'required|exists:new_lead_responsibilities,id',
        ]);

        $newLeadId = $request->lead_id;
        $productId = $request->product_id;
        $currentEmployeeId = $request->employee_id;
        $selectedEmployeeId = $request->employees;
        $alternativeId = $request->alternative_id;

        \Log::info('requested employees: ', [$request]);
        try {
            $data = NewLeadResponsibility::find($request->id);
            $data->employee_id = $selectedEmployeeId;
            $data->current_employee = $selectedEmployeeId;
            $data->status = 'send';
            $data->save();

            // Fetch employee details for notification
            $notificationEmp = DB::table('employees')->select('id', 'name', 'lastname')->get();

            // Get the names for notification
            $byName = $notificationEmp->where('id', auth()->user()->name)->first();
            $changedFrom = $notificationEmp->where('id', $currentEmployeeId)->first();
            $changedTo = $notificationEmp->where('id', $selectedEmployeeId)->first();

            // Validate fetched employees
            $byNameStr = $byName ? "{$byName->name} {$byName->lastname}" : 'Unbekannt';
            $changedFromStr = $changedFrom ? "{$changedFrom->name} {$changedFrom->lastname}" : 'Unbekannt';
            $changedToStr = $changedTo ? "{$changedTo->name} {$changedTo->lastname}" : 'Unbekannt';

            // Send Notification
            Notification::send(auth()->user(), new LeadResponsibleChange([
                'title' => 'Neuen Mitarbeiter anfragen',
                'message' => "{$byNameStr} hat den Verantwortlichen für diesen Kunden von {$changedFromStr} auf {$changedToStr} geändert",
                'type' => 'responsible',
                'lead_id' => $newLeadId,
                'responsible_id' => $request->id,
            ]));

            return redirect()->back()->with('save_msg', 'Der Mitarbeiterwechsel für diesen Kunden wurde erfolgreich durchgeführt.');
        } catch (\Exception $e) {
            \Log::error('Error saving employees', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'lead_id' => $newLeadId,
                'product_id' => $productId,
                'employee_id' => $currentEmployeeId,
                'alternative_id' => $alternativeId,
            ]);

            return redirect()->back()->with('delete_msg', 'Es gab ein Problem beim Speichern der Mitarbeiter.');
        }

    }

    public function saveSelectedEmployee(Request $request)
    {
        // Validate the request
        $validate = $request->validate([
            'lead_id' => 'required|exists:new_leads,id',
            'employee_id' => 'required|exists:employees,id',
            'product_id' => 'required|exists:article_groups,id',
            'employees' => 'required|exists:employees,id',
            'alternative_id' => 'required|exists:lead_alternative_adds,id',
        ]);

        $newLeadId = $request->lead_id;
        $productId = $request->product_id;
        $currentEmployeeId = $request->employee_id;
        $selectedEmployeeId = $request->employees;
        $alternativeId = $request->alternative_id;

        \Log::info('requested employees: ', [$request]);
        try {
            $data = NewLeadResponsibility::where('new_lead_id', $newLeadId)
                ->where('alternative_id', $alternativeId)
                ->where('current_employee', $currentEmployeeId)
                ->first();
            $data->employee_id = $selectedEmployeeId;
            $data->current_employee = $selectedEmployeeId;
            $data->status = 'send';
            $data->save();

            // Fetch employee details for notification
            $notificationEmp = DB::table('employees')->select('id', 'name', 'lastname')->get();

            // Get the names for notification
            $byName = $notificationEmp->where('id', auth()->user()->name)->first();
            $changedFrom = $notificationEmp->where('id', $currentEmployeeId)->first();
            $changedTo = $notificationEmp->where('id', $selectedEmployeeId)->first();

            // Validate fetched employees
            $byNameStr = $byName ? "{$byName->name} {$byName->lastname}" : 'Unbekannt';
            $changedFromStr = $changedFrom ? "{$changedFrom->name} {$changedFrom->lastname}" : 'Unbekannt';
            $changedToStr = $changedTo ? "{$changedTo->name} {$changedTo->lastname}" : 'Unbekannt';

            // Send Notification
            Notification::send(auth()->user(), new LeadResponsibleChange([
                'title' => 'Neuen Mitarbeiter anfragen',
                'message' => "{$byNameStr} hat den Verantwortlichen für diesen Kunden von {$changedFromStr} auf {$changedToStr} geändert",
                'type' => 'responsible',
                'lead_id' => $newLeadId,
                'responsible_id' => $request->id,
            ]));

            return redirect()->back()->with('save_msg', 'Der Mitarbeiterwechsel für diesen Kunden wurde erfolgreich durchgeführt.');
        } catch (\Exception $e) {
            \Log::error('Error saving employees', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'lead_id' => $newLeadId,
                'product_id' => $productId,
                'employee_id' => $currentEmployeeId,
                'alternative_id' => $alternativeId,
            ]);

            return redirect()->back()->with('delete_msg', 'Es gab ein Problem beim Speichern der Mitarbeiter.');
        }

    }


    public function getTimelineNotifications($leadId, $responsibleId)
    {
        try {
            $notifications = DatabaseNotification::whereJsonContains('data->lead_id', $leadId)
                ->whereJsonContains('data->responsible_id', $responsibleId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'notifications' => $notifications,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching notifications: ' . $e->getMessage(), [
                'lead_id' => $leadId,
                'responsible_id' => $responsibleId,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Ein Fehler ist aufgetreten.',
            ], 500);
        }
    }

    public function qualified_sort()
    {
        $query = DB::table('new_leads')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.lead_id', '=', 'new_leads.id')
            ->join('employees as contact_person', 'contact_person.id', '=', 'new_leads.contact_person')
            ->select('new_leads.*', 'contact_person.name as c_name', 'contact_person.lastname as c_lastname', 'contact_person.image as c_image', 'lead_alternative_adds.street', 'lead_alternative_adds.postcode', 'lead_alternative_adds.lat', 'lead_alternative_adds.lon', 'lead_alternative_adds.main', 'lead_alternative_adds.address_no')
            ->where('new_leads.status', '=', 'QUALIFIZIERT')
            ->where('new_leads.status', '!=', "Junk")

            ->orderBy('new_leads.id', 'desc');

        $data['data'] = $query->paginate(20);
        $this->setCommonData($data);

        return view('admin.new_leads.customer_view', $data);
    }

    public function not_qualified_sort()
    {
        $query = DB::table('new_leads')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.lead_id', '=', 'new_leads.id')
            ->join('employees as contact_person', 'contact_person.id', '=', 'new_leads.contact_person')
            ->select('new_leads.*', 'contact_person.name as c_name', 'contact_person.lastname as c_lastname', 'contact_person.image as c_image', 'lead_alternative_adds.street', 'lead_alternative_adds.postcode', 'lead_alternative_adds.lat', 'lead_alternative_adds.lon', 'lead_alternative_adds.main', 'lead_alternative_adds.address_no')
            ->where('new_leads.status', '=', "um zu qualifizieren, bitte telefonisch  Kontakt aufnehmen")
            ->orWhere('new_leads.status', '=', "um zu qualifizieren, bitte per E-Mail  Kontakt aufnehmen")
            ->where('new_leads.status', '!=', "Junk")
            ->orderBy('new_leads.id', 'desc');

        $data['data'] = $query->paginate(20);
        $this->setCommonData($data);

        return view('admin.new_leads.customer_view', $data);
    }

    public function incomplete_sort()
    {
        $query = DB::table('new_leads')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.lead_id', '=', 'new_leads.id')
            ->join('employees as contact_person', 'contact_person.id', '=', 'new_leads.contact_person')
            ->select('new_leads.*', 'contact_person.name as c_name', 'contact_person.lastname as c_lastname', 'contact_person.image as c_image', 'lead_alternative_adds.street', 'lead_alternative_adds.postcode', 'lead_alternative_adds.lat', 'lead_alternative_adds.lon', 'lead_alternative_adds.main', 'lead_alternative_adds.address_no')
            ->where('new_leads.status', '=', "um zu qualifizieren, bitte per Brief  Kontakt aufnehmen")
            ->where('new_leads.status', '!=', "Junk")

            ->orderBy('new_leads.id', 'desc');

        $data['data'] = $query->paginate(20);
        $this->setCommonData($data);

        return view('admin.new_leads.customer_view', $data);
    }
    public function junk_sort()
    {
        $query = DB::table('new_leads')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.lead_id', '=', 'new_leads.id')
            ->join('employees as contact_person', 'contact_person.id', '=', 'new_leads.contact_person')
            ->select('new_leads.*', 'contact_person.name as c_name', 'contact_person.lastname as c_lastname', 'contact_person.image as c_image', 'lead_alternative_adds.street', 'lead_alternative_adds.postcode', 'lead_alternative_adds.lat', 'lead_alternative_adds.lon', 'lead_alternative_adds.main', 'lead_alternative_adds.address_no')
            ->where('new_leads.status', '=', "Junk")
            ->orderBy('new_leads.id', 'desc');
        $data['data'] = $query->paginate(20);
        $this->setCommonData($data);

        return view('admin.new_leads.customer_view', $data);
    }

    private function setCommonData(&$data)
    {
        $data['article'] = ArticleGroup::all();

        $data['product_list'] = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->select('article_groups.initial', 'new_leads.id as customer_id', 'article_groups.id as product_id', 'lead_product_lists.status')
            ->get();

        $customer_product_count = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select('new_leads.name', 'new_leads.lastname', 'article_groups.article_group', 'lead_product_lists.*')
            ->get();

        $data['customer_product_count'] = $customer_product_count;
        $open = $customer_product_count->where('status', 'open')->count();
        $active = $customer_product_count->where('status', 'active')->count();
        $inactive = $customer_product_count->where('status', 'inactive')->count();
        $ended = $customer_product_count->where('status', 'ended')->count();
        $cancel = $customer_product_count->where('status', 'cancel')->count();
        $all = $customer_product_count->count();

        $data['counts'] = [
            'open' => $open,
            'active' => $active,
            'inactive' => $inactive,
            'ended' => $ended,
            'cancel' => $cancel,
            'all' => $all,
            'open_per' => $all > 0 ? ($open / $all) * 100 : 0,
            'active_per' => $all > 0 ? ($active / $all) * 100 : 0,
            'inactive_per' => $all > 0 ? ($inactive / $all) * 100 : 0,
            'end_per' => $all > 0 ? ($ended / $all) * 100 : 0,
            'cancel_per' => $all > 0 ? ($cancel / $all) * 100 : 0,
        ];
    }


    public function destroyWithReason(Request $request, $id)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:2000'],
        ], [
            'reason.required' => 'Bitte geben Sie einen Löschgrund ein.',
            'reason.min' => 'Der Grund muss mindestens 3 Zeichen lang sein.',
        ]);

        $lead = NewLeads::findOrFail($id);

        $lead->delete_reason = $request->reason;
        $lead->deleted_by = auth()->user()?->name ?? auth()->id();
        $lead->deleted_reason_at = now();
        $lead->save();

        $lead->delete();

        return redirect()
            ->back()
            ->with('delete_msg', trim($lead->name . ' ' . $lead->lastname) . ': Kunde wurde gelöscht.');
    }

    public function junk(Request $request, $id)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:2000'],
        ], [
            'reason.required' => 'Bitte geben Sie einen Junk-Grund ein.',
            'reason.min' => 'Der Grund muss mindestens 3 Zeichen lang sein.',
        ]);

        $lead = NewLeads::findOrFail($id);

        $lead->status = 'Junk';
        $lead->status_msg = $request->reason;
        $lead->junk_reason = $request->reason;
        $lead->junked_by = auth()->user()?->name ?? auth()->id();
        $lead->junked_at = now();
        $lead->save();

        return redirect()
            ->back()
            ->with('delete_msg', trim($lead->name . ' ' . $lead->lastname) . ': Dieser Kunde wurde als Junk-Lead markiert.');
    }

    public function unjunk(Request $request, $id)
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $lead = NewLeads::findOrFail($id);

        $lead->status = 'Von Junk wiederhergestellt';
        $lead->status_msg = $request->reason ?: 'Von Junk wiederhergestellt';
        $lead->unjunk_reason = $request->reason;
        $lead->unjunked_by = auth()->user()?->name ?? auth()->id();
        $lead->unjunked_at = now();
        $lead->save();

        return redirect()
            ->back()
            ->with('delete_msg', trim($lead->name . ' ' . $lead->lastname) . ': Kunde wurde von Junk wiederhergestellt.');
    }

    public function junk_alternative($id)
    {
        $data = LeadAlternativeAdd::find($id);
        $data->status = "Junk";
        $data->save();
        return redirect()->back()->with('delete_msg', $data->name . $data->lastname . ':Dieser Kunde zählte als Junk-Lead');
    }

    public function unjunk_alternative($id)
    {
        $data = LeadAlternativeAdd::find($id);
        $data->status = "Von Junk wiederhergestellt";
        $data->save();
        return redirect()->back()->with('delete_msg', $data->name . $data->lastname . ':Dieser Kunde Von Junk wiederhergestellt');
    }



    public function index(Request $request)
    {
        // 1. Basic Parameters
        $search = $request->query('search');
        $sortBy = $request->get('sort_by', 'new_leads.id');
        $sortOrder = $request->get('sort_order', 'desc');
        $selectedProducts = (array) $request->input('products', []);

        // 2. Sorting Whitelist
        $allowedSorts = [
            'new_leads.id',
            'new_leads.name',
            'new_leads.customer_no',
            'new_leads.source',
            'new_leads.quelle',
            'new_leads.lastname',
            'new_leads.email',
            'new_leads.phone',
            'c_name',
            'new_leads.contact_person',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'new_leads.id';
        }

        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
        $orderByColumn = $sortBy === 'new_leads.quelle' ? 'new_leads.source' : $sortBy;

        // 3. Current Employee
        // In your app: auth()->user()->name = employees.id
        $empId = (int) auth()->user()->name;

        // 4. "Meine Leads" Counts
        // IMPORTANT:
        // Meine Leads = employee exists in lead_product_lists.
        // It does NOT mean contact_person.
        // It does NOT mean created_by.
        $myCounts = [
            'contact' => 0,
            'inner' => 0,
            'field' => 0,
            'total_unique' => 0,
        ];

        if ($empId > 0) {
            // Innendienst / assigned employee
            $myCounts['inner'] = DB::table('lead_product_lists')
                ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
                ->where('lead_product_lists.employee_id', $empId)
                ->whereNull('lead_product_lists.deleted_at')
                ->whereNull('new_leads.deleted_at')
                ->whereNotIn('new_leads.status', ['Junk', 'plan'])
                ->distinct('lead_product_lists.customer_id')
                ->count('lead_product_lists.customer_id');

            // Außendienst / field employee
            $myCounts['field'] = DB::table('lead_product_lists')
                ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
                ->where('lead_product_lists.field_employee', $empId)
                ->whereNull('lead_product_lists.deleted_at')
                ->whereNull('new_leads.deleted_at')
                ->whereNotIn('new_leads.status', ['Junk', 'plan'])
                ->distinct('lead_product_lists.customer_id')
                ->count('lead_product_lists.customer_id');

            // Unique leads where current employee is assigned in lead_product_lists
            $myCounts['total_unique'] = DB::table('lead_product_lists')
                ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
                ->whereNull('lead_product_lists.deleted_at')
                ->whereNull('new_leads.deleted_at')
                ->whereNotIn('new_leads.status', ['Junk', 'plan'])
                ->where(function ($q) use ($empId) {
                    $q->where('lead_product_lists.employee_id', $empId)
                        ->orWhere('lead_product_lists.field_employee', $empId);
                })
                ->distinct('lead_product_lists.customer_id')
                ->count('lead_product_lists.customer_id');
        }

        // 5. Main Lead Query
        $leadQuery = DB::table('new_leads')
            ->leftJoin('employees as contact_person', 'contact_person.id', '=', 'new_leads.contact_person')
            ->select(
                'new_leads.*',
                'contact_person.name as c_name',
                'contact_person.lastname as c_lastname',
                'contact_person.image as c_image'
            )
            ->whereNotIn('new_leads.status', ['Junk', 'plan'])
            ->whereNull('new_leads.deleted_at');

        // 6. Search Filter
        if (!empty($search)) {
            $leadQuery->where(function ($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.id', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.customer_no', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.lastname', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.email', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.phone', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.source', 'LIKE', "%{$search}%");
            });
        }

        // 7. Product Filter
        if (!empty($selectedProducts)) {
            $leadQuery->join('lead_product_lists as prod_filter', function ($join) {
                $join->on('prod_filter.customer_id', '=', 'new_leads.id')
                    ->whereNull('prod_filter.deleted_at');
            })
                ->whereIn('prod_filter.product_id', $selectedProducts)
                ->distinct();
        }

        // 8. Meine Leads Filter
        // Shows only leads where current employee is assigned in lead_product_lists.
        // It does NOT use creator/contact_person.
        if ($request->input('my_customers') == '1' && $empId > 0) {
            $leadQuery
                ->join('lead_product_lists as my_lpl', function ($join) use ($empId) {
                    $join->on('my_lpl.customer_id', '=', 'new_leads.id')
                        ->whereNull('my_lpl.deleted_at')
                        ->where(function ($q) use ($empId) {
                            $q->where('my_lpl.employee_id', $empId)
                                ->orWhere('my_lpl.field_employee', $empId);
                        });
                })
                ->distinct('new_leads.id');
        }

        // 9. Sorting
        if ($orderByColumn === 'new_leads.customer_no') {
            $leadQuery->orderByRaw('CAST(new_leads.customer_no AS UNSIGNED) ' . $sortOrder);
        } elseif ($orderByColumn === 'c_name') {
            $leadQuery->orderBy('contact_person.name', $sortOrder);
        } else {
            $leadQuery->orderBy($orderByColumn, $sortOrder);
        }

        // 10. Pagination
        $leads = $leadQuery->paginate(20)->appends($request->query());

        // 11. Article Groups
        $article = ArticleGroup::orderBy('article_group')->get();

        // 12. Product Counts / Overview Stats
        $customerProductCountQuery = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select(
                'new_leads.id as customer_id',
                'new_leads.name',
                'new_leads.lastname',
                'article_groups.article_group',
                'lead_product_lists.status',
                'lead_product_lists.id',
                'lead_product_lists.employee_id',
                'lead_product_lists.field_employee'
            )
            ->whereNull('new_leads.deleted_at')
            ->whereNull('lead_product_lists.deleted_at');

        // Optional:
        // When Meine Leads is active, overview stats should also show only my assigned product rows.
        if ($request->has('my_customers') && $request->my_customers == '1' && $empId > 0) {
            $customerProductCountQuery->where(function ($q) use ($empId) {
                $q->where('lead_product_lists.employee_id', $empId)
                    ->orWhere('lead_product_lists.field_employee', $empId);
            });
        }

        $customer_product_count = $customerProductCountQuery->get();

        $statusKeys = ['open', 'active', 'inactive', 'ended', 'cancel'];
        $counts = [];

        foreach ($statusKeys as $status) {
            $counts[$status] = $customer_product_count->where('status', $status)->count();
        }

        $all = $customer_product_count->count();

        $counts['all'] = $all;
        $counts['open_per'] = $all ? ($counts['open'] / $all) * 100 : 0;
        $counts['active_per'] = $all ? ($counts['active'] / $all) * 100 : 0;
        $counts['inactive_per'] = $all ? ($counts['inactive'] / $all) * 100 : 0;
        $counts['end_per'] = $all ? ($counts['ended'] / $all) * 100 : 0;
        $counts['cancel_per'] = $all ? ($counts['cancel'] / $all) * 100 : 0;

        $statusCounts = [
            'open' => $counts['open'],
            'active' => $counts['active'],
            'inactive' => $counts['inactive'],
            'ended' => $counts['ended'],
            'cancel' => $counts['cancel'],
        ];

        // 13. Product Count For Table Rows
        $productcount = DB::table('lead_product_lists')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'lead_product_lists.alternative_id')
            ->select(
                'lead_product_lists.*',
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id'
            )
            ->whereNull('lead_alternative_adds.deleted_at')
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        // 14. Product Employees
        $productEmployees = DB::table('employees')
            ->select('id', 'name', 'lastname', 'image', 'status', 'gender')
            ->get();

        // 15. Customer Product Lists
        $customer_product_lists = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->select(
                'lead_product_lists.*',
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id',
                'new_leads.id as customer_id',
                'departments.department_name',
                'phase_sections.phase_section as service_phase_section',
                'lead_product_lists.id as p_list_id',
                'lead_product_lists.created_at as product_created'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        // 16. Current Request
        $current_request = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select(
                'article_groups.article_group',
                'article_groups.initial',
                'lead_product_lists.*'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        $this->hydrateLeadProductTeams($customer_product_lists);
        $this->hydrateLeadProductTeams($current_request);

        // 17. Alternatives
        $alternative = DB::table('lead_alternative_adds')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'junk')
            ->orderByDesc('id')
            ->get();

        // 18. Support Data
        $employees = Employee::select('id', 'name', 'lastname', 'image')->get();
        $departments = Department::where('status', 'published')->get();
        $productInfo = DB::table('article_groups')->get();

        $serviceList = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section')
            ->whereNull('deleted_at')
            ->get();

        $stage = 'lead';

        $companyStages = LeadStage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn(LeadStage $stage) => [
                'type' => 'company',
                'id' => $stage->id,
                'key' => $stage->key,
                'name' => $stage->name,
                'color' => $stage->color ?: '#93c21c',
                'icon' => $stage->icon ?: 'circle',
                'sort_order' => $stage->sort_order,
                'is_closed' => (bool) $stage->is_closed,
            ])
            ->values();

        return view('admin.new_leads.customer_view', [
            'data' => $leads,
            'myCounts' => $myCounts,

            'article' => $article,
            'productcount' => $productcount,
            'productEmployees' => $productEmployees,
            'customer_product_lists' => $customer_product_lists,
            'customer_product_count' => $customer_product_count,

            'counts' => $counts,
            'statusCounts' => $statusCounts,
            'current_request' => $current_request,
            'alternative' => $alternative,

            'employees' => $employees,
            'products' => $productInfo,
            'services' => $serviceList,
            'departments' => $departments,
            'productInfo' => $productInfo,
            'serviceList' => $serviceList,
            'companyStages' => $companyStages,


            'selectedProducts' => $selectedProducts,
            'stage' => $stage,
            'search' => $search,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }
    public function waiting_leads(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $allowedSorts = [
            'lead_product_lists.id',
            'new_leads.id',
            'new_leads.name',
            'new_leads.lastname',
            'new_leads.customer_no',
            'new_leads.email',
            'new_leads.phone',
            'lead_product_lists.created_at',
            'article_groups.article_group',
            'alt.city',
            'alt.postcode',
            'status',
        ];

        $sortBy = $request->get('sort_by', 'lead_product_lists.created_at');
        $sortOrder = strtolower($request->get('sort_order', 'desc'));

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'lead_product_lists.created_at';
        }

        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        /*
        |--------------------------------------------------------------------------
        | Base query
        |--------------------------------------------------------------------------
        | Shows all waiting lead product rows where Innendienst employee_id is NULL.
        | No contact_person/user filter.
        */
        $baseQuery = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->leftJoin('lead_alternative_adds as alt', 'alt.id', '=', 'lead_product_lists.alternative_id')
            ->leftJoin('employees as assigned_employee', 'assigned_employee.id', '=', 'lead_product_lists.employee_id')
            ->leftJoin('employees as contact_employee', 'contact_employee.id', '=', 'new_leads.contact_person')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->select(
                'new_leads.id as lead_id',
                'new_leads.customer_no',
                'new_leads.title',
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.contact_person',
                'new_leads.email',
                'new_leads.phone',
                'new_leads.telephone',
                'new_leads.status as lead_status',
                'new_leads.source',
                'new_leads.firma',

                'lead_product_lists.id as id',
                'lead_product_lists.created_at',
                'lead_product_lists.product_id',
                'lead_product_lists.employee_id',
                'lead_product_lists.field_employee',
                'lead_product_lists.service',
                'lead_product_lists.service_id',
                'lead_product_lists.department_id',
                'lead_product_lists.status as status',

                'alt.id as alternative_id',
                'alt.postcode',
                'alt.city',
                'alt.street',
                'alt.object_name',
                'alt.request_date',

                'assigned_employee.name as emp_name',
                'assigned_employee.lastname as emp_lastname',
                'assigned_employee.image as emp_image',
                'assigned_employee.gender as emp_gender',

                'contact_employee.name as contact_name',
                'contact_employee.lastname as contact_lastname',
                'contact_employee.image as contact_image',
                'contact_employee.gender as contact_gender',

                'article_groups.article_group',
                'article_groups.initial',

                'departments.department_name',
                'phase_sections.phase_section'
            )
            ->whereNull('lead_product_lists.employee_id')
            ->whereNull('lead_product_lists.deleted_at')
            ->whereNull('new_leads.deleted_at')
            ->where(function ($q) {
                $q->whereNull('new_leads.status')
                    ->orWhere('new_leads.status', '!=', 'Junk');
            });

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.lastname', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.customer_no', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.email', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.phone', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.telephone', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.firma', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.source', 'LIKE', "%{$search}%")
                    ->orWhere('alt.postcode', 'LIKE', "%{$search}%")
                    ->orWhere('alt.city', 'LIKE', "%{$search}%")
                    ->orWhere('alt.street', 'LIKE', "%{$search}%")
                    ->orWhere('alt.object_name', 'LIKE', "%{$search}%")
                    ->orWhere('article_groups.article_group', 'LIKE', "%{$search}%")
                    ->orWhere('departments.department_name', 'LIKE', "%{$search}%")
                    ->orWhere('phase_sections.phase_section', 'LIKE', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Analytics before pagination
        |--------------------------------------------------------------------------
        */
        $analyticsRows = (clone $baseQuery)->get();

        $now = now();

        $waitingTotal = $analyticsRows->count();

        $waitingOver48 = $analyticsRows
            ->filter(function ($row) use ($now) {
                return !empty($row->created_at)
                    && \Carbon\Carbon::parse($row->created_at)->diffInHours($now) > 48;
            })
            ->count();

        $waitingToday = $analyticsRows
            ->filter(function ($row) {
                return !empty($row->created_at)
                    && \Carbon\Carbon::parse($row->created_at)->isToday();
            })
            ->count();

        $uniqueCustomers = $analyticsRows
            ->pluck('lead_id')
            ->filter()
            ->unique()
            ->count();

        $uniqueProducts = $analyticsRows
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->count();

        $cityCount = $analyticsRows
            ->pluck('city')
            ->filter()
            ->map(fn($city) => trim((string) $city))
            ->filter()
            ->unique()
            ->count();

        $statusSummary = [
            'waiting' => $analyticsRows
                ->filter(fn($row) => strtolower((string) $row->status) !== 'reject')
                ->count(),

            'reject' => $analyticsRows
                ->filter(fn($row) => strtolower((string) $row->status) === 'reject')
                ->count(),
        ];

        $topProducts = $analyticsRows
            ->groupBy(fn($row) => $row->article_group ?: 'Unbekannt')
            ->map(fn($rows, $name) => [
                'name' => $name,
                'count' => $rows->count(),
            ])
            ->sortByDesc('count')
            ->take(8)
            ->values();

        $topCities = $analyticsRows
            ->groupBy(fn($row) => $row->city ?: 'Unbekannt')
            ->map(fn($rows, $name) => [
                'name' => $name,
                'count' => $rows->count(),
            ])
            ->sortByDesc('count')
            ->take(8)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */
        $query = clone $baseQuery;

        if ($sortBy === 'status') {
            $query->orderBy('lead_product_lists.status', $sortOrder);
        } elseif ($sortBy === 'new_leads.customer_no') {
            $query->orderByRaw('CAST(new_leads.customer_no AS UNSIGNED) ' . $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $leads = $query
            ->paginate(19)
            ->appends($request->query());

        /*
        |--------------------------------------------------------------------------
        | Fix empty page problem
        |--------------------------------------------------------------------------
        | Example: /wating_leads?page=5 while only 3 pages exist.
        */
        if ($leads->isEmpty() && $leads->currentPage() > 1) {
            return redirect()->to($request->fullUrlWithQuery(['page' => 1]));
        }

        /*
        |--------------------------------------------------------------------------
        | Supporting data
        |--------------------------------------------------------------------------
        */
        $service = DB::table('lead_product_lists')
            ->select('customer_id', 'alternative_id', 'service')
            ->whereNull('deleted_at')
            ->get();

        $employees = DB::table('employees')
            ->where('status', 'active')
            ->select('id', 'name', 'lastname', 'image', 'gender')
            ->orderBy('name')
            ->get();

        $current_request = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select(
                'article_groups.article_group',
                'article_groups.initial',
                'lead_product_lists.*'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        $customer_product_lists = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->select(
                'lead_product_lists.*',
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id',
                'new_leads.id as customer_id',
                'departments.department_name',
                'phase_sections.phase_section',
                'lead_product_lists.id as p_list_id',
                'lead_product_lists.created_at as product_created'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        return view('admin.new_leads.waiting_loops', [
            'data' => $leads,
            'service' => $service,
            'employees' => $employees,
            'current_request' => $current_request,
            'customer_product_lists' => $customer_product_lists,

            'waitingTotal' => $waitingTotal,
            'waitingOver48' => $waitingOver48,
            'waitingToday' => $waitingToday,
            'uniqueCustomers' => $uniqueCustomers,
            'uniqueProducts' => $uniqueProducts,
            'cityCount' => $cityCount,
            'statusSummary' => $statusSummary,
            'topProducts' => $topProducts,
            'topCities' => $topCities,

            'search' => $search,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }
    public function customerFeed(Request $request, $customerId)
    {
        $limit = (int) $request->query('limit', 10);
        $limit = max(1, min($limit, 50));
        $debug = (bool) $request->query('debug', false);

        // ---------------------------------------------------------------------
        // Status → German
        // ---------------------------------------------------------------------
        $statusTranslations = [
            'pending' => 'Ausstehend',
            'lead' => 'Lead',
            'inquiry' => 'Anfrage',
            'inqurity' => 'Anfrage',
            'deal' => 'Auftrag',
            'project' => 'Projekt',
            'ticket' => 'Ticket',

            'playing' => 'Laufend',
            'stop' => 'Gestoppt',
            'stopped' => 'Gestoppt',

            'cancel' => 'Abgesagt',
            'canceled' => 'Abgesagt',
            'cancelled' => 'Abgesagt',

            'completed' => 'Abgeschlossen',
            'complete' => 'Abgeschlossen',
            'done' => 'Abgeschlossen',
            'finished' => 'Abgeschlossen',

            'paused' => 'Pausiert',
            'pause' => 'Pausiert',

            'feedback' => 'Rückmeldung',

            'archive' => 'Archiviert',
            'archived' => 'Archiviert',

            // product-level
            'open' => 'Offen',
            'active' => 'Aktiv',
            'inactive' => 'Inaktiv',
            'ended' => 'Beendet',
        ];

        $translateStatus = function (?string $status) use ($statusTranslations): string {
            if (!$status)
                return 'Status';
            $key = strtolower(trim($status));
            return $statusTranslations[$key] ?? ucfirst($status);
        };

        // ---------------------------------------------------------------------
        // Icons per kind
        // ---------------------------------------------------------------------
        $kindIcons = [
            'history' => 'icon-activity',
            'product' => 'icon-package',
            'appointment' => 'icon-calendar',
            'task' => 'icon-check-square',
            'ticket' => 'icon-life-buoy',
        ];

        // ---------------------------------------------------------------------
        // Avatar helper (employee images)
        // ---------------------------------------------------------------------
        $fallbackMale = asset('images/gender/male.png');
        $fallbackFemale = asset('images/gender/female.png');

        $avatarForEmployeeRow = function ($row) use ($fallbackMale, $fallbackFemale) {
            $gender = $row->emp_gender ?? 'Male';
            $img = $row->emp_image ?? null;

            if ($img && file_exists(public_path('images/employee/' . $img))) {
                return asset('images/employee/' . $img);
            }

            return strtolower($gender) === 'female' ? $fallbackFemale : $fallbackMale;
        };

        $activities = collect();
        $debugCounts = [
            'customer_id' => (int) $customerId,
            'history_count' => 0,
            'product_count' => 0,
            'appointment_count' => 0,
            'task_count' => 0,
            'ticket_count' => 0,
        ];

        // ---------------------------------------------------------------------
        // 1) customer_histories
        // ---------------------------------------------------------------------
        $historyRows = DB::table('customer_histories')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'customer_histories.product_id')
            ->leftJoin('task_phases', 'task_phases.id', '=', 'customer_histories.phase_id')
            ->leftJoin('phase_activities', 'phase_activities.id', '=', 'customer_histories.activity_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'customer_histories.section_id')
            ->leftJoin('employees', 'employees.id', '=', 'customer_histories.done_by')
            ->leftJoin('stages', 'stages.id', '=', 'task_phases.stage_id')
            ->select(
                'customer_histories.*',
                'article_groups.article_group',
                'article_groups.initial as product_initial',
                'task_phases.phase_name',
                'task_phases.stage as task_phase_stage',
                'phase_activities.title as activity_title',
                'phase_activities.initial as activity_initial',
                'phase_sections.phase_section',
                'employees.name as employee_name',
                'employees.lastname as employee_lastname',
                'stages.stage as stage_name'
            )
            ->where('customer_histories.customer_id', $customerId)
            ->orderByDesc('customer_histories.done_date')
            ->orderByDesc('customer_histories.d_time')
            ->orderByDesc('customer_histories.created_at')
            ->limit($limit)
            ->get();

        $debugCounts['history_count'] = $historyRows->count();

        foreach ($historyRows as $row) {
            $stageRaw = $row->old_stage ?: $row->stage_name ?: $row->task_phase_stage;
            $pillLabel = $translateStatus($stageRaw ?: 'project');

            $titleParts = [];
            if (!empty($row->article_group))
                $titleParts[] = $row->article_group;
            if (!empty($row->phase_name))
                $titleParts[] = $row->phase_name;
            $title = $titleParts ? implode(' – ', $titleParts) : 'Aktivität';

            $textParts = [];
            if (!empty($row->activity_title))
                $textParts[] = $row->activity_title;
            if (!empty($row->phase_section))
                $textParts[] = $row->phase_section;
            if (!empty($row->employee_name) || !empty($row->employee_lastname)) {
                $textParts[] = trim(($row->employee_name ?? '') . ' ' . ($row->employee_lastname ?? ''));
            }
            if (!empty($row->notes))
                $textParts[] = strip_tags($row->notes);

            $text = $textParts ? implode(' · ', $textParts) : $pillLabel;

            $stamp = null;
            if (!empty($row->done_date)) {
                $stamp = $row->done_date . ' ' . ($row->d_time ?? '00:00:00');
            } elseif (!empty($row->updated_at)) {
                $stamp = $row->updated_at;
            } else {
                $stamp = $row->created_at;
            }
            $dt = $stamp ? Carbon::parse($stamp) : null;

            $activities->push([
                'sort_at' => $dt ? $dt->timestamp : 0,
                'title' => $title,
                'text' => $text,
                'pill' => $pillLabel,
                'time' => $dt ? $dt->format('d.m.Y H:i') : null,
                'kind' => 'history',
                'icon' => $kindIcons['history'],
                'employees' => [], // optional
            ]);
        }

        // ---------------------------------------------------------------------
        // 2) lead_product_lists (status + work_status)
        // ---------------------------------------------------------------------
        $services = [
            'complete' => 'Komplettlösung',
            'montage' => 'Montage',
            'product' => 'Produkt',
            'plan' => 'Planung',
            'maintenance' => 'Wartung',
            'repair' => 'Reparatur',
            'emergency' => 'Notdienst',
            'others' => 'Sonstiges',
        ];

        $productRows = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->select(
                'lead_product_lists.*',
                'article_groups.article_group',
                'departments.department_name',
                'phase_sections.phase_section as service_phase_section'
            )
            ->where('lead_product_lists.customer_id', $customerId)
            ->whereNull('lead_product_lists.deleted_at')
            ->orderByDesc('lead_product_lists.updated_at')
            ->limit($limit)
            ->get();

        $debugCounts['product_count'] = $productRows->count();

        foreach ($productRows as $row) {
            $serviceLabel = $services[$row->service] ?? ($row->service ?? 'Service');

            $statusLabel = $translateStatus(
                $row->status
                ?? $row->stage
                ?? 'lead'
            );
            $workLabel = !empty($row->work_status)
                ? $translateStatus($row->work_status)
                : null;

            $dept = $row->department_name ?: null;
            $phase = $row->service_phase_section ?: null;
            $article = $row->article_group ?: 'Produkt';

            $stamp = $row->updated_at ?? $row->created_at ?? null;
            $dt = $stamp ? Carbon::parse($stamp) : null;

            $parts = array_filter([
                $serviceLabel,
                $dept,
                $phase,
                $workLabel ? 'Arbeitsstatus: ' . $workLabel : null,
            ]);

            $activities->push([
                'sort_at' => $dt ? $dt->timestamp : 0,
                'title' => $article,
                'text' => $parts ? implode(' · ', $parts) : $statusLabel,
                'pill' => $statusLabel,
                'time' => $dt ? $dt->format('d.m.Y H:i') : null,
                'kind' => 'product',
                'icon' => $kindIcons['product'],
                'employees' => [],
            ]);
        }

        // ---------------------------------------------------------------------
        // 3) Termine (main_appointments + main_appointment_employees)
        // ---------------------------------------------------------------------
        $appointmentRowsRaw = DB::table('main_appointments')
            ->leftJoin('main_appointment_employees', 'main_appointment_employees.appointment_id', '=', 'main_appointments.id')
            ->leftJoin('employees', 'employees.id', '=', 'main_appointment_employees.employee_id')
            ->select(
                'main_appointments.*',
                'main_appointments.id as appointment_id',
                'employees.id as emp_id',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image',
                'employees.gender as emp_gender',
                'main_appointment_employees.status as emp_status'
            )
            ->where('main_appointments.customer_id', $customerId)
            ->whereNull('main_appointments.deleted_at')
            ->orderByDesc('main_appointments.start_date')
            ->orderByDesc('main_appointments.start_time')
            ->limit($limit * 5) // etwas mehr, da wir gruppieren
            ->get();

        $appointmentsGrouped = $appointmentRowsRaw->groupBy('appointment_id');
        $debugCounts['appointment_count'] = $appointmentsGrouped->count();

        foreach ($appointmentsGrouped as $appointmentId => $rows) {
            /** @var \stdClass $main */
            $main = $rows->first();

            // Status + Titel
            $statusLabel = $translateStatus($main->status ?? 'appointment');
            $title = $main->name ?? $main->appointment_type ?? 'Termin';

            // Zeitpunkt
            $stamp = null;
            if (!empty($main->start_date)) {
                $stamp = $main->start_date . ' ' . ($main->start_time ?? '00:00:00');
            } else {
                $stamp = $main->updated_at ?? $main->created_at ?? null;
            }
            $dt = $stamp ? Carbon::parse($stamp) : null;

            // Text
            $parts = [];
            if (!empty($main->full_address)) {
                $parts[] = $main->full_address;
            } elseif (!empty($main->street) || !empty($main->city)) {
                $addr = trim(($main->street ?? '') . ' ' . ($main->postcode ?? '') . ' ' . ($main->city ?? ''));
                if ($addr !== '')
                    $parts[] = $addr;
            }
            if (!empty($main->note)) {
                $parts[] = strip_tags($main->note);
            }
            if (!empty($main->appointment_type)) {
                $parts[] = 'Art: ' . $main->appointment_type;
            }
            if (!empty($main->execution_type)) {
                $parts[] = 'Ausführung: ' . $main->execution_type;
            }
            $text = $parts ? implode(' · ', $parts) : 'Termin';

            // Mitarbeiter (Kreise)
            $employees = $rows
                ->filter(fn($r) => !empty($r->emp_id))
                ->map(function ($r) use ($avatarForEmployeeRow, $translateStatus) {
                    return [
                        'id' => (int) $r->emp_id,
                        'name' => trim(($r->emp_name ?? '') . ' ' . ($r->emp_lastname ?? '')),
                        'avatar' => $avatarForEmployeeRow($r),
                        'status' => $translateStatus($r->emp_status ?? ''),
                    ];
                })
                ->values()
                ->all();

            $activities->push([
                'sort_at' => $dt ? $dt->timestamp : 0,
                'title' => $title,
                'text' => $text,
                'pill' => $statusLabel,
                'time' => $dt ? $dt->format('d.m.Y H:i') : null,
                'kind' => 'appointment',
                'icon' => $kindIcons['appointment'],
                'employees' => $employees,
            ]);
        }

        // ---------------------------------------------------------------------
        // 4) Aufgaben (personal_tasks + employees_personal_tasks)
        // ---------------------------------------------------------------------
        $taskRowsRaw = DB::table('personal_tasks')
            ->leftJoin('employees_personal_tasks', 'employees_personal_tasks.task_id', '=', 'personal_tasks.id')
            ->leftJoin('employees', 'employees.id', '=', 'employees_personal_tasks.employee_id')
            ->select(
                'personal_tasks.*',
                'personal_tasks.id as task_id',
                'employees.id as emp_id',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image',
                'employees.gender as emp_gender',
                'employees_personal_tasks.status as emp_status'
            )
            ->where('personal_tasks.customer_id', $customerId)
            ->whereNull('personal_tasks.deleted_at')
            ->orderByDesc('personal_tasks.due_date')
            ->orderByDesc('personal_tasks.due_time')
            ->limit($limit * 5)
            ->get();

        $tasksGrouped = $taskRowsRaw->groupBy('task_id');
        $debugCounts['task_count'] = $tasksGrouped->count();

        foreach ($tasksGrouped as $taskId => $rows) {
            /** @var \stdClass $main */
            $main = $rows->first();

            $statusLabel = $translateStatus($main->task_status ?? 'task');
            $title = $main->task_title ?? 'Aufgabe';

            $stamp = null;
            if (!empty($main->due_date)) {
                $stamp = $main->due_date . ' ' . ($main->due_time ?? '00:00:00');
            } else {
                $stamp = $main->updated_at ?? $main->created_at ?? null;
            }
            $dt = $stamp ? Carbon::parse($stamp) : null;

            $parts = [];
            if (!empty($main->priority)) {
                $parts[] = 'Priorität: ' . ucfirst($main->priority);
            }
            if (!empty($main->progress)) {
                $parts[] = 'Fortschritt: ' . $main->progress;
            }
            if (!empty($main->description)) {
                $parts[] = strip_tags($main->description);
            }
            $text = $parts ? implode(' · ', $parts) : 'Aufgabe';

            $employees = $rows
                ->filter(fn($r) => !empty($r->emp_id))
                ->map(function ($r) use ($avatarForEmployeeRow, $translateStatus) {
                    return [
                        'id' => (int) $r->emp_id,
                        'name' => trim(($r->emp_name ?? '') . ' ' . ($r->emp_lastname ?? '')),
                        'avatar' => $avatarForEmployeeRow($r),
                        'status' => $translateStatus($r->emp_status ?? ''),
                    ];
                })
                ->values()
                ->all();

            $activities->push([
                'sort_at' => $dt ? $dt->timestamp : 0,
                'title' => $title,
                'text' => $text,
                'pill' => $statusLabel,
                'time' => $dt ? $dt->format('d.m.Y H:i') : null,
                'kind' => 'task',
                'icon' => $kindIcons['task'],
                'employees' => $employees,
            ]);
        }

        // ---------------------------------------------------------------------
        // 5) Tickets (falls vorhanden)
        // ---------------------------------------------------------------------
        $ticketRows = collect();

        if (Schema::hasTable('tickets')) {
            try {
                $ticketQuery = DB::table('tickets')
                    ->where('customer_id', $customerId);

                if (Schema::hasColumn('tickets', 'deleted_at')) {
                    $ticketQuery->whereNull('deleted_at');
                }

                $ticketRows = $ticketQuery
                    ->orderByDesc('updated_at')
                    ->limit($limit)
                    ->get();
            } catch (\Throwable $e) {
                Log::error('CustomerFeed tickets error', [
                    'customer_id' => $customerId,
                    'error' => $e->getMessage(),
                ]);
                $ticketRows = collect();
            }
        }

        $debugCounts['ticket_count'] = $ticketRows->count();

        foreach ($ticketRows as $row) {
            $statusLabel = $translateStatus($row->status ?? 'ticket');
            $title = $row->subject ?? 'Ticket';

            $stamp = $row->updated_at ?? $row->created_at ?? null;
            $dt = $stamp ? Carbon::parse($stamp) : null;

            $parts = [];
            if (!empty($row->category))
                $parts[] = $row->category;
            if (!empty($row->priority))
                $parts[] = 'Priorität: ' . ucfirst($row->priority);
            $text = $parts ? implode(' · ', $parts) : 'Ticket';

            $activities->push([
                'sort_at' => $dt ? $dt->timestamp : 0,
                'title' => $title,
                'text' => $text,
                'pill' => $statusLabel,
                'time' => $dt ? $dt->format('d.m.Y H:i') : null,
                'kind' => 'ticket',
                'icon' => $kindIcons['ticket'],
                'employees' => [],
            ]);
        }

        // ---------------------------------------------------------------------
        // Merge, sort, limit
        // ---------------------------------------------------------------------
        $items = $activities
            ->sortByDesc('sort_at')
            ->take($limit)
            ->values()
            ->map(function (array $row) {
                return [
                    'title' => $row['title'],
                    'text' => $row['text'],
                    'pill' => $row['pill'],
                    'time' => $row['time'],
                    'kind' => $row['kind'],
                    'icon' => $row['icon'],
                    'employees' => $row['employees'] ?? [],
                ];
            });

        if ($debug) {
            Log::debug('Customer feed debug', [
                'customer_id' => $customerId,
                'counts' => $debugCounts,
                'items_count' => $items->count(),
            ]);
        }

        return response()->json([
            'success' => true,
            'items' => $items,
            'debug' => $debug ? $debugCounts : null,
        ]);
    }

    public function my_lead(Request $request)
    {
        $search = $request->query('search');
        $sortBy = $request->get('sort_by', 'new_leads.id');
        $sortOrder = $request->get('sort_order', 'desc');
        $selectedProducts = (array) $request->input('products', []);

        $allowedSorts = [
            'new_leads.id',
            'new_leads.name',
            'new_leads.customer_no',
            'new_leads.source',
            'new_leads.quelle',
            'new_leads.lastname',
            'new_leads.email',
            'new_leads.phone',
            'c_name',
            'new_leads.contact_person',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'new_leads.id';
        }

        $orderByColumn = $sortBy === 'new_leads.quelle'
            ? 'new_leads.source'
            : $sortBy;

        // Prefer an employee id if you have it; otherwise stay with name
        $currentName = auth()->user()->name;

        /*
        |--------------------------------------------------------------------------
        | Base lead query: leads where
        |  - I am contact_person   OR
        |  - I am involved in lead_product_lists
        |--------------------------------------------------------------------------
        */
        $leadQuery = DB::table('new_leads')
            ->leftJoin('employees as contact_person', 'contact_person.id', '=', 'new_leads.contact_person')
            ->select(
                'new_leads.*',
                'contact_person.name as c_name',
                'contact_person.lastname as c_lastname',
                'contact_person.image as c_image'
            )
            // only "Lead" status, not junk/plan, not soft-deleted
            ->where('new_leads.status', 'Lead')
            ->whereNotIn('new_leads.status', ['Junk', 'plan'])
            ->whereNull('new_leads.deleted_at')
            // only leads where this user is contact person OR involved in lead_product_lists
            ->where(function ($q) use ($currentName) {
                $q->where('contact_person.name', $currentName)
                    // if contact_person is stored as plain text name, keep this line;
                    // if it's an employee_id, you can remove it.
                    ->orWhere('new_leads.contact_person', $currentName)
                    // involvement in lead_product_lists
                    ->orWhereExists(function ($sub) use ($currentName) {
                        $sub->select(DB::raw(1))
                            ->from('lead_product_lists')
                            // IMPORTANT: adjust employee columns here
                            ->leftJoin('employees as lp_inner', 'lp_inner.id', '=', 'lead_product_lists.employee_id')
                            ->leftJoin('employees as lp_outer', 'lp_outer.id', '=', 'lead_product_lists.field_employee')
                            ->whereColumn('lead_product_lists.customer_id', 'new_leads.id')
                            ->whereNull('lead_product_lists.deleted_at')
                            ->where(function ($q2) use ($currentName) {
                                $q2->where('lp_inner.name', $currentName)
                                    ->orWhere('lp_outer.name', $currentName);
                            });
                    });
            });

        /*
        |--------------------------------------------------------------------------
        | Text search
        |--------------------------------------------------------------------------
        */
        if (!empty($search)) {
            $leadQuery->where(function ($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.id', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.customer_no', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.lastname', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.email', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.phone', 'LIKE', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Product filter (same as index, but applied on "my leads")
        |--------------------------------------------------------------------------
        */
        if (!empty($selectedProducts)) {
            $leadQuery
                ->join('lead_product_lists', function ($join) {
                    $join->on('lead_product_lists.customer_id', '=', 'new_leads.id')
                        ->whereNull('lead_product_lists.deleted_at');
                })
                ->whereIn('lead_product_lists.product_id', $selectedProducts)
                ->distinct();
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */
        if ($orderByColumn === 'new_leads.customer_no') {
            $leadQuery->orderByRaw('CAST(new_leads.customer_no AS UNSIGNED) ' . $sortOrder);
        } elseif ($orderByColumn === 'c_name') {
            $leadQuery->orderBy('contact_person.name', $sortOrder);
        } else {
            $leadQuery->orderBy($orderByColumn, $sortOrder);
        }

        /*
        |--------------------------------------------------------------------------
        | Paginate "my" leads
        |--------------------------------------------------------------------------
        */
        $leads = $leadQuery
            ->paginate(20)
            ->appends($request->query());

        /*
        |--------------------------------------------------------------------------
        | Article groups
        |--------------------------------------------------------------------------
        */
        $article = ArticleGroup::orderBy('article_group')->get();

        /*
        |--------------------------------------------------------------------------
        | Product circles per customer row
        |--------------------------------------------------------------------------
        */
        $productcount = DB::table('lead_product_lists')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'lead_product_lists.alternative_id')
            ->select(
                'lead_product_lists.*',
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id'
            )
            ->whereNull('lead_alternative_adds.deleted_at')
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Product employees
        |--------------------------------------------------------------------------
        */
        $productEmployees = DB::table('employees')
            ->select('id', 'name', 'lastname', 'image', 'status', 'gender')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Full customer product list (nested table)
        |--------------------------------------------------------------------------
        */
        $customer_product_lists = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->select(
                'lead_product_lists.*',
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id',
                'new_leads.id as customer_id',
                'departments.department_name',
                'phase_sections.phase_section as service_phase_section',
                'lead_product_lists.id as p_list_id',
                'lead_product_lists.created_at as product_created'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Customer/product summary – only for MY leads (including involvement)
        |--------------------------------------------------------------------------
        */
        $customer_product_count = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('employees as contact_person', 'contact_person.id', '=', 'new_leads.contact_person')
            // same involvement logic, but here we already are on lead_product_lists, so no subquery
            // IMPORTANT: adjust employee columns (inner_employee_id / outer_employee_id) here as well
            ->leftJoin('employees as lp_inner', 'lp_inner.id', '=', 'lead_product_lists.employee_id')
            ->leftJoin('employees as lp_outer', 'lp_outer.id', '=', 'lead_product_lists.field_employee')
            ->select(
                'new_leads.id as customer_id',
                'new_leads.name',
                'new_leads.lastname',
                'article_groups.article_group',
                'lead_product_lists.status',
                'lead_product_lists.id'
            )
            ->whereNull('new_leads.deleted_at')
            ->whereNull('lead_product_lists.deleted_at')
            ->where('new_leads.status', 'Lead')
            ->where(function ($q) use ($currentName) {
                $q->where('contact_person.name', $currentName)
                    ->orWhere('new_leads.contact_person', $currentName)
                    ->orWhere('lp_inner.name', $currentName)
                    ->orWhere('lp_outer.name', $currentName);
            })
            ->get();

        // Status totals for KPI cards
        $statusKeys = ['open', 'active', 'inactive', 'ended', 'cancel'];
        $counts = [];

        foreach ($statusKeys as $status) {
            $counts[$status] = $customer_product_count
                ->where('status', $status)
                ->count();
        }

        $all = $customer_product_count->count();

        $counts['all'] = $all;
        $counts['open_per'] = $all ? ($counts['open'] / $all) * 100 : 0;
        $counts['active_per'] = $all ? ($counts['active'] / $all) * 100 : 0;
        $counts['inactive_per'] = $all ? ($counts['inactive'] / $all) * 100 : 0;
        $counts['end_per'] = $all ? ($counts['ended'] / $all) * 100 : 0;
        $counts['cancel_per'] = $all ? ($counts['cancel'] / $all) * 100 : 0;

        $statusCounts = [
            'open' => $customer_product_count->where('status', 'open')->count(),
            'active' => $customer_product_count->where('status', 'active')->count(),
            'inactive' => $customer_product_count->where('status', 'inactive')->count(),
            'ended' => $customer_product_count->where('status', 'ended')->count(),
            'cancel' => $customer_product_count->where('status', 'cancel')->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Current requests
        |--------------------------------------------------------------------------
        */
        $current_request = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select(
                'article_groups.article_group',
                'article_groups.initial',
                'lead_product_lists.*'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Alternatives, employees, departments, products, services
        |--------------------------------------------------------------------------
        */
        $alternative = DB::table('lead_alternative_adds')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'junk')
            ->orderByDesc('id')
            ->get();

        $employees = Employee::select('id', 'name', 'lastname', 'image')->get();
        $departments = Department::where('status', 'published')->get();
        $productInfo = DB::table('article_groups')->get();
        $serviceList = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section')
            ->whereNull('deleted_at')
            ->get();

        $stage = 'lead';

        // if your Blade (same as index) also needs $products and $services, add:
        // $products = DB::table('article_groups')->get();
        // $services = DB::table('phase_sections')->select('id','product_id','phase_section')->whereNull('deleted_at')->get();

        return view('admin.new_leads.customer_view', [
            'data' => $leads,
            'article' => $article,
            'productcount' => $productcount,
            'productEmployees' => $productEmployees,
            'customer_product_lists' => $customer_product_lists,
            'customer_product_count' => $customer_product_count,
            'counts' => $counts,
            'statusCounts' => $statusCounts,
            'current_request' => $current_request,
            'alternative' => $alternative,
            'employees' => $employees,
            'departments' => $departments,
            'productInfo' => $productInfo,
            'serviceList' => $serviceList,
            'selectedProducts' => $selectedProducts,
            'stage' => $stage,
            'search' => $search,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            // 'products' => $products,
            // 'services' => $services,
        ]);
    }


    public function new_lead(Request $request)
    {
        $search = $request->query('search');
        $sortBy = $request->get('sort_by', 'new_leads.id');
        $sortOrder = $request->get('sort_order', 'desc');
        $selectedProducts = (array) $request->input('products', []);

        $allowedSorts = [
            'new_leads.id',
            'new_leads.name',
            'new_leads.customer_no',
            'new_leads.source',
            'new_leads.quelle',
            'new_leads.lastname',
            'new_leads.email',
            'new_leads.phone',
            'c_name',
            'new_leads.contact_person',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'new_leads.id';
        }

        $orderByColumn = $sortBy === 'new_leads.quelle'
            ? 'new_leads.source'
            : $sortBy;

        $currentName = auth()->user()->name;
        $since = Carbon::now()->subHours(48);

        /*
        |--------------------------------------------------------------------------
        | Base lead query: ONLY my leads from last 48 hours
        |--------------------------------------------------------------------------
        */
        $leadQuery = DB::table('new_leads')
            ->leftJoin('employees as contact_person', 'contact_person.id', '=', 'new_leads.contact_person')
            ->select(
                'new_leads.*',
                'contact_person.name as c_name',
                'contact_person.lastname as c_lastname',
                'contact_person.image as c_image'
            )
            ->where('new_leads.status', 'Lead')
            ->whereNotIn('new_leads.status', ['Junk', 'plan'])
            ->whereNull('new_leads.deleted_at')
            // only last 48 hours
            ->where('new_leads.created_at', '>=', $since)
            // only leads for this contact person
            ->where(function ($q) use ($currentName) {
                $q->where('new_leads.contact_person', $currentName)
                    ->orWhere('contact_person.name', $currentName);
            });

        /*
        |--------------------------------------------------------------------------
        | Text search
        |--------------------------------------------------------------------------
        */
        if (!empty($search)) {
            $leadQuery->where(function ($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.id', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.customer_no', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.lastname', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.email', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.phone', 'LIKE', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Product / article filter
        |--------------------------------------------------------------------------
        */
        if (!empty($selectedProducts)) {
            $leadQuery
                ->join('lead_product_lists', function ($join) {
                    $join->on('lead_product_lists.customer_id', '=', 'new_leads.id')
                        ->whereNull('lead_product_lists.deleted_at');
                })
                ->whereIn('lead_product_lists.product_id', $selectedProducts)
                ->distinct();
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */
        if ($orderByColumn === 'new_leads.customer_no') {
            $leadQuery->orderByRaw('CAST(new_leads.customer_no AS UNSIGNED) ' . $sortOrder);
        } elseif ($orderByColumn === 'c_name') {
            $leadQuery->orderBy('contact_person.name', $sortOrder);
        } else {
            $leadQuery->orderBy($orderByColumn, $sortOrder);
        }

        /*
        |--------------------------------------------------------------------------
        | Paginate
        |--------------------------------------------------------------------------
        */
        $leads = $leadQuery
            ->paginate(20)
            ->appends($request->query());

        /*
        |--------------------------------------------------------------------------
        | Article groups
        |--------------------------------------------------------------------------
        */
        $article = ArticleGroup::orderBy('article_group')->get();

        /*
        |--------------------------------------------------------------------------
        | Product circles per customer row
        |--------------------------------------------------------------------------
        */
        $productcount = DB::table('lead_product_lists')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'lead_product_lists.alternative_id')
            ->select(
                'lead_product_lists.*',
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id'
            )
            ->whereNull('lead_alternative_adds.deleted_at')
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Product employees
        |--------------------------------------------------------------------------
        */
        $productEmployees = DB::table('employees')
            ->select('id', 'name', 'lastname', 'image', 'status', 'gender')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Full customer product list
        |--------------------------------------------------------------------------
        */
        $customer_product_lists = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->select(
                'lead_product_lists.*',
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id',
                'new_leads.id as customer_id',
                'departments.department_name',
                'phase_sections.phase_section as service_phase_section',
                'lead_product_lists.id as p_list_id',
                'lead_product_lists.created_at as product_created'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Customer/product summary – only my leads from last 48h
        |--------------------------------------------------------------------------
        */
        $customer_product_count = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('employees as contact_person', 'contact_person.id', '=', 'new_leads.contact_person')
            ->select(
                'new_leads.id as customer_id',
                'new_leads.name',
                'new_leads.lastname',
                'article_groups.article_group',
                'lead_product_lists.status',
                'lead_product_lists.id'
            )
            ->whereNull('new_leads.deleted_at')
            ->whereNull('lead_product_lists.deleted_at')
            ->where('new_leads.status', 'Lead')
            ->where('new_leads.created_at', '>=', $since)
            ->where(function ($q) use ($currentName) {
                $q->where('new_leads.contact_person', $currentName)
                    ->orWhere('contact_person.name', $currentName);
            })
            ->get();

        $statusKeys = ['open', 'active', 'inactive', 'ended', 'cancel'];
        $counts = [];

        foreach ($statusKeys as $status) {
            $counts[$status] = $customer_product_count
                ->where('status', $status)
                ->count();
        }

        $all = $customer_product_count->count();

        $counts['all'] = $all;
        $counts['open_per'] = $all ? ($counts['open'] / $all) * 100 : 0;
        $counts['active_per'] = $all ? ($counts['active'] / $all) * 100 : 0;
        $counts['inactive_per'] = $all ? ($counts['inactive'] / $all) * 100 : 0;
        $counts['end_per'] = $all ? ($counts['ended'] / $all) * 100 : 0;
        $counts['cancel_per'] = $all ? ($counts['cancel'] / $all) * 100 : 0;

        $statusCounts = [
            'open' => $customer_product_count->where('status', 'open')->count(),
            'active' => $customer_product_count->where('status', 'active')->count(),
            'inactive' => $customer_product_count->where('status', 'inactive')->count(),
            'ended' => $customer_product_count->where('status', 'ended')->count(),
            'cancel' => $customer_product_count->where('status', 'cancel')->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Current requests
        |--------------------------------------------------------------------------
        */
        $current_request = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select(
                'article_groups.article_group',
                'article_groups.initial',
                'lead_product_lists.*'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        $alternative = DB::table('lead_alternative_adds')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'junk')
            ->orderByDesc('id')
            ->get();

        $employees = Employee::select('id', 'name', 'lastname', 'image')->get();
        $departments = Department::where('status', 'published')->get();
        $productInfo = DB::table('article_groups')->get();
        $serviceList = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section')
            ->whereNull('deleted_at')
            ->get();

        $stage = 'new';

        return view('admin.new_leads.customer_view', [
            'data' => $leads,
            'article' => $article,
            'productcount' => $productcount,
            'productEmployees' => $productEmployees,
            'customer_product_lists' => $customer_product_lists,
            'customer_product_count' => $customer_product_count,
            'counts' => $counts,
            'statusCounts' => $statusCounts,
            'current_request' => $current_request,
            'alternative' => $alternative,
            'employees' => $employees,
            'departments' => $departments,
            'productInfo' => $productInfo,
            'serviceList' => $serviceList,
            'selectedProducts' => $selectedProducts,
            'stage' => $stage,
            'search' => $search,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function restore($id)
    {
        $data = NewLeads::withTrashed()->find($id);

        if ($data) {
            $data->restore(); // Restores the soft-deleted record
            return redirect()->back()->with('save_msg', 'Anfrage erfolgreich wiederhergestellt');
        }

        return redirect()->back()->with('error', 'Anfrage nicht gefunden');
    }



    public function getnameSuggestions(Request $request)
    {
        $query = $request->get('query');
        $suggestions = NewLeads::where('name', 'LIKE', "%{$query}%")
            ->limit(10)
            ->pluck('name');
        return response()->json($suggestions);
    }

    public function getLastnameSuggestions(Request $request)
    {
        $query = $request->get('query');
        $suggestions = NewLeads::where('lastname', 'LIKE', "%{$query}%")
            ->limit(10)
            ->pluck('lastname');
        return response()->json($suggestions);
    }
    public function checkCustomer(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $name = $this->normalizeLeadText($data['name'] ?? '');
        $lastname = $this->normalizeLeadText($data['lastname'] ?? '');
        $streetRaw = $data['street'] ?? '';
        $street = $this->normalizeLeadStreet($streetRaw);
        $streetOnly = $this->streetWithoutHouseNumber($streetRaw);
        $postcode = trim((string) ($data['postcode'] ?? ''));
        $city = $this->normalizeLeadText($data['city'] ?? '');
        $telephone = $this->normalizePhone($data['telephone'] ?? '');
        $phone = $this->normalizePhone($data['phone'] ?? '');
        $email = $this->normalizeLeadText($data['email'] ?? '');
        $latitude = (float) $data['latitude'];
        $longitude = (float) $data['longitude'];

        /*
        |--------------------------------------------------------------------------
        | 1. Duplicate check: main address from new_leads
        |--------------------------------------------------------------------------
        */
        $mainDuplicates = DB::table('new_leads')
            ->select([
                'new_leads.id',
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.street',
                'new_leads.postcode',
                'new_leads.city',
                'new_leads.telephone',
                'new_leads.phone',
                'new_leads.email',
                DB::raw('NULL as address_no'),
                'new_leads.latitude',
                'new_leads.longitude',
                DB::raw("'main' as address_type"),
                DB::raw('NULL as alternative_id'),
            ])
            ->whereNull('new_leads.deleted_at')
            ->whereRaw('LOWER(TRIM(new_leads.name)) = ?', [$name])
            ->whereRaw('LOWER(TRIM(new_leads.lastname)) = ?', [$lastname])
            ->where(function ($query) use ($street, $streetOnly) {
                $query->whereRaw('LOWER(TRIM(new_leads.street)) = ?', [$street]);

                if ($streetOnly && $streetOnly !== $street) {
                    $query->orWhereRaw('LOWER(TRIM(new_leads.street)) = ?', [$streetOnly]);
                }
            })
            ->whereRaw('TRIM(new_leads.postcode) = ?', [$postcode])
            ->when($city, function ($query) use ($city) {
                $query->whereRaw('LOWER(TRIM(new_leads.city)) = ?', [$city]);
            })
            ->when($telephone || $phone || $email, function ($query) use ($telephone, $phone, $email) {
                $query->where(function ($q) use ($telephone, $phone, $email) {
                    if ($telephone) {
                        $q->orWhereRaw(
                            "REGEXP_REPLACE(COALESCE(new_leads.telephone, ''), '[^0-9]', '') = ?",
                            [$telephone]
                        );
                    }

                    if ($phone) {
                        $q->orWhereRaw(
                            "REGEXP_REPLACE(COALESCE(new_leads.phone, ''), '[^0-9]', '') = ?",
                            [$phone]
                        );
                    }

                    if ($email) {
                        $q->orWhereRaw('LOWER(TRIM(new_leads.email)) = ?', [$email]);
                    }
                });
            })
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 2. Duplicate check: object/alternative address
        |--------------------------------------------------------------------------
        */
        $alternativeDuplicates = DB::table('lead_alternative_adds')
            ->join('new_leads', 'new_leads.id', '=', 'lead_alternative_adds.lead_id')
            ->select([
                'new_leads.id',
                'new_leads.name',
                'new_leads.lastname',
                'lead_alternative_adds.street',
                'lead_alternative_adds.postcode',
                'lead_alternative_adds.city',
                'new_leads.telephone',
                'new_leads.phone',
                'new_leads.email',
                'lead_alternative_adds.address_no',
                DB::raw('lead_alternative_adds.lat as latitude'),
                DB::raw('lead_alternative_adds.lon as longitude'),
                DB::raw("'object' as address_type"),
                DB::raw('lead_alternative_adds.id as alternative_id'),
            ])
            ->whereNull('new_leads.deleted_at')
            ->whereNull('lead_alternative_adds.deleted_at')
            ->whereRaw('LOWER(TRIM(new_leads.name)) = ?', [$name])
            ->whereRaw('LOWER(TRIM(new_leads.lastname)) = ?', [$lastname])
            ->where(function ($query) use ($street, $streetOnly) {
                $query->whereRaw('LOWER(TRIM(lead_alternative_adds.street)) = ?', [$street]);

                if ($streetOnly && $streetOnly !== $street) {
                    $query->orWhereRaw('LOWER(TRIM(lead_alternative_adds.street)) = ?', [$streetOnly]);
                }
            })
            ->whereRaw('TRIM(lead_alternative_adds.postcode) = ?', [$postcode])
            ->when($city, function ($query) use ($city) {
                $query->whereRaw('LOWER(TRIM(lead_alternative_adds.city)) = ?', [$city]);
            })
            ->when($telephone || $phone || $email, function ($query) use ($telephone, $phone, $email) {
                $query->where(function ($q) use ($telephone, $phone, $email) {
                    if ($telephone) {
                        $q->orWhereRaw(
                            "REGEXP_REPLACE(COALESCE(new_leads.telephone, ''), '[^0-9]', '') = ?",
                            [$telephone]
                        );
                    }

                    if ($phone) {
                        $q->orWhereRaw(
                            "REGEXP_REPLACE(COALESCE(new_leads.phone, ''), '[^0-9]', '') = ?",
                            [$phone]
                        );
                    }

                    if ($email) {
                        $q->orWhereRaw('LOWER(TRIM(new_leads.email)) = ?', [$email]);
                    }
                });
            })
            ->get();

        $duplicates = $mainDuplicates
            ->merge($alternativeDuplicates)
            ->unique(function ($item) {
                return $item->id . '-' . $item->address_type . '-' . ($item->alternative_id ?? 'main');
            })
            ->values()
            ->map(function ($item) use ($latitude, $longitude) {
                $item->distance = $this->calculateCustomerDistance(
                    $latitude,
                    $longitude,
                    $item->latitude,
                    $item->longitude
                );

                return $item;
            });

        $duplicateKeys = $duplicates
            ->map(function ($item) {
                return $item->id . '-' . $item->address_type . '-' . ($item->alternative_id ?? 'main');
            })
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | 3. Neighbor check: main address
        |--------------------------------------------------------------------------
        */
        $radius = 1; // km

        $mainNeighbors = DB::table('new_leads')
            ->select([
                'new_leads.id',
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.street',
                'new_leads.postcode',
                'new_leads.city',
                'new_leads.telephone',
                'new_leads.phone',
                'new_leads.email',
                DB::raw('NULL as address_no'),
                'new_leads.latitude',
                'new_leads.longitude',
                DB::raw("'main' as address_type"),
                DB::raw('NULL as alternative_id'),
            ])
            ->selectRaw("
            (
                6371 * acos(
                    LEAST(1, GREATEST(-1,
                        cos(radians(?)) *
                        cos(radians(new_leads.latitude)) *
                        cos(radians(new_leads.longitude) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(new_leads.latitude))
                    ))
                )
            ) AS distance
        ", [$latitude, $longitude, $latitude])
            ->whereNull('new_leads.deleted_at')
            ->whereNotNull('new_leads.latitude')
            ->whereNotNull('new_leads.longitude')
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->limit(30)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 4. Neighbor check: alternative/object address
        |--------------------------------------------------------------------------
        */
        $alternativeNeighbors = DB::table('lead_alternative_adds')
            ->join('new_leads', 'new_leads.id', '=', 'lead_alternative_adds.lead_id')
            ->select([
                'new_leads.id',
                'new_leads.name',
                'new_leads.lastname',
                'lead_alternative_adds.street',
                'lead_alternative_adds.postcode',
                'lead_alternative_adds.city',
                'new_leads.telephone',
                'new_leads.phone',
                'new_leads.email',
                'lead_alternative_adds.address_no',
                DB::raw('lead_alternative_adds.lat as latitude'),
                DB::raw('lead_alternative_adds.lon as longitude'),
                DB::raw("'object' as address_type"),
                DB::raw('lead_alternative_adds.id as alternative_id'),
            ])
            ->selectRaw("
            (
                6371 * acos(
                    LEAST(1, GREATEST(-1,
                        cos(radians(?)) *
                        cos(radians(lead_alternative_adds.lat)) *
                        cos(radians(lead_alternative_adds.lon) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(lead_alternative_adds.lat))
                    ))
                )
            ) AS distance
        ", [$latitude, $longitude, $latitude])
            ->whereNull('new_leads.deleted_at')
            ->whereNull('lead_alternative_adds.deleted_at')
            ->whereNotNull('lead_alternative_adds.lat')
            ->whereNotNull('lead_alternative_adds.lon')
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->limit(30)
            ->get();

        $neighbors = $mainNeighbors
            ->merge($alternativeNeighbors)
            ->reject(function ($item) use ($duplicateKeys) {
                $key = $item->id . '-' . $item->address_type . '-' . ($item->alternative_id ?? 'main');

                return in_array($key, $duplicateKeys, true);
            })
            ->unique(function ($item) {
                return $item->id . '-' . $item->address_type . '-' . ($item->alternative_id ?? 'main');
            })
            ->sortBy('distance')
            ->values();

        return response()->json([
            'status' => $duplicates->isNotEmpty()
                ? 'duplicate'
                : ($neighbors->isNotEmpty() ? 'neighbor' : 'unique'),

            'duplicates' => $duplicates,
            'neighbors' => $neighbors,

            // old JS compatibility
            'customer' => $duplicates->first(),
            'customers' => $neighbors,

            'debug' => [
                'input' => [
                    'name' => $name,
                    'lastname' => $lastname,
                    'street' => $street,
                    'street_only' => $streetOnly,
                    'postcode' => $postcode,
                    'city' => $city,
                    'telephone' => $telephone,
                    'phone' => $phone,
                    'email' => $email,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ],
                'counts' => [
                    'duplicates' => $duplicates->count(),
                    'neighbors' => $neighbors->count(),
                ],
            ],

            'message' => $duplicates->isEmpty() && $neighbors->isEmpty()
                ? 'No duplicate or neighbors found.'
                : null,
        ]);
    }

    private function normalizeLeadText(?string $value): string
    {
        $value = mb_strtolower(trim((string) $value));
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }

    private function normalizeLeadStreet(?string $value): string
    {
        $value = $this->normalizeLeadText($value);
        $value = str_replace(',', ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }

    private function streetWithoutHouseNumber(?string $value): string
    {
        $street = $this->normalizeLeadStreet($value);

        return trim(preg_replace('/\s+\d+[a-z]?(?:\s*-\s*\d+[a-z]?)?$/iu', '', $street));
    }

    private function normalizePhone(?string $value): string
    {
        return preg_replace('/[^0-9]/', '', (string) $value);
    }

    private function calculateCustomerDistance($lat1, $lng1, $lat2, $lng2): ?float
    {
        if (!$lat1 || !$lng1 || !$lat2 || !$lng2) {
            return null;
        }

        $earthRadius = 6371;

        $latFrom = deg2rad((float) $lat1);
        $lngFrom = deg2rad((float) $lng1);
        $latTo = deg2rad((float) $lat2);
        $lngTo = deg2rad((float) $lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)
        ));

        return round($angle * $earthRadius, 4);
    }
    function matchCustomerWithLeadAndAttachImage()
    {
        // Fetch all customers with necessary fields
        $customers = DB::table('new_leads')
            ->select('id', 'name', 'lastname', 'street', 'postcode', 'city', 'email')
            ->get();

        // Fetch all leads and map by unique key for fast lookup
        $leadsByUniqueKey = DB::table('new_leads')
            ->select('id', 'name', 'lastname', 'street', 'postcode', 'city', 'email')
            ->get()
            ->keyBy(function ($lead) {
                return strtolower($lead->name . '|' . $lead->lastname . '|' . $lead->street . '|' . $lead->postcode . '|' . $lead->city . '|' . $lead->email);
            });

        // Fetch all lead images and map by lead_id
        $imagesByLeadId = DB::table('new_lead_images')
            ->select('lead_id', 'image')
            ->pluck('image', 'lead_id');

        // Loop through each customer and find matching lead and image
        foreach ($customers as $customer) {
            // Create a unique key for the customer to check for matching leads
            $uniqueKey = strtolower($customer->name . '|' . $customer->lastname . '|' . $customer->street . '|' . $customer->postcode . '|' . $customer->city . '|' . $customer->email);

            // Check if there's a matching lead by unique key
            if (isset($leadsByUniqueKey[$uniqueKey])) {
                $lead = $leadsByUniqueKey[$uniqueKey];

                // Find the image link for this lead, if it exists
                $imageLink = $imagesByLeadId[$lead->id] ?? null;

                // Update the customer's inquiry_screenshot if an image link is found
                if ($imageLink) {
                    DB::table('customers')
                        ->where('id', $customer->id)
                        ->update(['inquiry_screenshot' => $imageLink]);
                }
            }
        }

    }

    public function accept(Request $request)
    {

        // Validate incoming request data
        $validate = $request->validate([
            'customer_id' => 'required|exists:new_leads,id',
            'product_id' => 'required|exists:article_groups,id',
            'employee_id' => 'required|exists:employees,id',
            'response' => 'required|in:accept,reject',
            'reason' => 'nullable|string',
            'product_list' => 'required|exists:lead_product_lists,id',
            'alternative_id' => 'required|exists:lead_alternative_adds,id'
        ]);

        // Fetch the responsibility record
        $data = NewLeadResponsibility::where('new_lead_id', $request->customer_id)
            ->where('current_employee', $request->employee_id)
            ->where('product_id', $request->product_id)
            ->where('alternative_id', $request->alternative_id)
            ->first();

        if (!$data) {
            return redirect()->back()->with('error_msg', 'Die Anfrage konnte nicht gefunden werden.');
        }

        // Handle rejection with reason validation
        if ($request->response === 'reject') {
            if (!$request->reason) {
                return redirect()->back()->with('error_msg', 'Bitte geben Sie einen Grund für die Ablehnung.');
            }

            $data->status = 'reject';
            $data->reason = $request->reason;
            $data->save();

            return redirect()->back()->with('save_msg', 'Ihre Anfrage wurde abgelehnt.');
        }

        // Handle acceptance
        if ($request->response === 'accept') {
            $data->status = 'accept';
            $data->reason = $request->reason ?? null;
            $data->save();

            // Save new planning record
            Planing::create([
                'customer_id' => $request->customer_id,
                'employee_id' => $request->employee_id,
                'product_id' => $request->product_id,
                'alternative_id' => $request->alternative_id,
                'service' => $request->service ?? null,
                'status_msg' => 'Nicht qualifiziert',
                'status' => 'new'
            ]);

            // Update product list status
            $productList = LeadProductList::find($request->product_list);
            if ($productList) {
                $productList->status = "plan";
                $productList->save();
            }

            // Check if all records are planned
            $totalRecords = LeadProductList::where('customer_id', $request->customer_id)->count();
            $plannedRecords = LeadProductList::where('customer_id', $request->customer_id)
                ->where('status', 'plan')->count();

            if ($totalRecords > 0 && $plannedRecords === $totalRecords) {
                $lead = LeadAlternativeAdd::find($request->alternative_id);
                if ($lead) {
                    $lead->status = 'plan';
                    $lead->stage = 'plan';
                    $lead->save();
                }
            }

            return redirect()->back()->with('save_msg', 'Ihre Anfrage wurde akzeptiert.');
        }

        return redirect()->back()->with('error_msg', 'Ungültige Antwort.');
    }

    public function delete_responsible($id)
    {

        $data = NewLeadResponsibility::find($id);
        if (!$data) {
            return redirect()->back()->with('error_msg', 'Die Anfrage konnte nicht gefunden werden');
        }
        $data->delete();
        return redirect()->back()->with('save_msg', 'Der Mitarbeiter wird aus dem Kunden gelöscht');
    }

    public function delete_product($id)
    {

        // Fetch the data from the database
        $data = DB::table('lead_product_lists')
            ->where('id', $id)
            ->first();

        // Check if the record exists
        if (!$data) {
            return redirect()->back()->with('error_msg', 'Die Anfrage konnte nicht gefunden werden');
        }

        // Delete the record
        DB::table('lead_product_lists')->where('id', $id)->delete();

        return redirect()->back()->with('save_msg', 'Das Produkt wurde erfolgreich gelöscht');
    }

    public function updatedata(Request $request)
    {
        // Log the incoming request data for debugging purposes
        Log::info('Request data:', $request->all());

        // Define validation rules
        $rules = [
            'customer_id' => 'required|exists:new_leads,id',
            'alternative_id' => 'required|exists:lead_alternative_adds,id',
            'objective' => 'nullable|string',
            'house_year' => 'nullable|integer',
            'number_we' => 'nullable|integer',
            'number_stories' => 'nullable|integer',
            'living_space' => 'nullable|numeric',
            'unusable_space' => 'nullable|numeric',
            'number_people' => 'nullable|integer',
            'roof_type' => 'nullable|string',
            'roof_age' => 'nullable|integer',
            'tile_name' => 'nullable|string',
            'heating_system_type' => 'nullable|string',
            'heating_system_age' => 'nullable|integer',
            'heating_system_year' => 'nullable|integer',
            'heating_type' => 'nullable|string',
            'installation_location' => 'nullable|string',
            'installation_location_extra' => 'nullable|string',
            'annual_consumption' => 'nullable|numeric',
            'annual_heating_energy_consumption' => 'nullable|numeric',
            'annual_heating_energy_consumption_kwh' => 'nullable|numeric',
            'electric_car' => 'nullable|string',
            'electric_car_plan' => 'nullable|string',
            'roof_pitch' => 'nullable|string',
            'roof_direction' => 'nullable|string', // Changed to string to match form data
            'car_kilo' => 'nullable|integer',
        ];

        // Create a validator instance
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // Return validation errors as JSON
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Find the record to update
            $data = LeadAlternativeAdd::findOrFail($request->alternative_id);

            // Update the record with validated data
            $data->update($validator->validated());
            $this->logActivity('updated', LeadAlternativeAdd::class, $data->id, $request->customer_id, $request->alternative_id, null, [
                'info' => 'Objektdaten über Sidebar aktualisiert'
            ]);
            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Die Daten wurden erfolgreich gespeichert.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Error updating data:', ['error' => $e->getMessage()]);

            // Return an error response
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Speichern der Daten. Bitte versuchen Sie es erneut.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function product_lists($id, $alternative)
    {
        $data = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select('article_groups.article_group', 'article_groups.id')
            ->where('new_leads.id', '=', $id)
            ->where('lead_product_lists.alternative_id', $alternative)
            ->get();

        return response()->json($data, 200);
    }

    //Profile CRUD

    public function getObject($customer, $alternative, $product)
    {
        $data = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'lead_product_lists.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select('lead_alternative_adds.*', 'article_groups.id as product_id', 'article_groups.article_group')
            ->where('lead_alternative_adds.id', $alternative)
            ->where('lead_alternative_adds.lead_id', $customer)
            ->where('article_groups.id', $product)
            ->first();

        if ($data) {
            return response()->json($data, 200);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function get_phase($customer, $alternative, $product)
    {

        $data = DB::table('customer_phase_lists')
            ->join('task_phases', 'task_phases.id', '=', 'customer_phase_lists.phase_id')
            ->where('customer_phase_lists.customer', $customer)
            ->where('customer_phase_lists.product', $product)
            ->where('customer_phase_lists.alternative', $alternative)
            ->select(
                'customer_phase_lists.id',
                'customer_phase_lists.customer',
                'customer_phase_lists.phase_id',
                'customer_phase_lists.product',
                'customer_phase_lists.alternative',
                'customer_phase_lists.service',
                'customer_phase_lists.status',
                'customer_phase_lists.color',
                'customer_phase_lists.active_by',
                'customer_phase_lists.jump_steps',
                'customer_phase_lists.jump_steps_by',
                'task_phases.phase_name'
            )
            ->get();


        return response()->json($data, 200);
    }

    public function getTaskNotifications(): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized access'], 401);
        }

        $notifications = DatabaseNotification::whereNull('read_at')
            ->where('data->to', $user->name) // Match the 'to' field in notification data
            ->where('data->type', 'lead') // Match the 'to' field in notification data
            ->orderBy('created_at', 'desc')
            ->get();

        $transformedNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Notification',
                'message' => $notification->data['message'] ?? '',
                'lead_id' => $notification->data['lead_id'] ?? null,
                'type' => $notification->data['type'] ?? null,
                'performed_at' => isset($notification->data['performed_at'])
                    ? Carbon::parse($notification->data['performed_at'])->toDateTimeString()
                    : $notification->created_at->toDateTimeString(),
            ];
        });

        return response()->json(['data' => $transformedNotifications]);
    }

    // Mark a notification as read
    public function markAsRead($id): JsonResponse
    {
        $notification = DatabaseNotification::findOrFail($id);

        if ($notification->read_at) {
            return response()->json(['message' => 'Notification already marked as read'], 200);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read'], 200);
    }

    public function getCustomer($id)
    {
        $customer = DB::table('new_leads')->where('id', $id)->first();

        if ($customer) {
            return response()->json([
                'name' => $customer->name,
                'lastname' => $customer->lastname,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'full_address' => $customer->full_address,
            ]);
        }
        return response()->json(null);
    }

    public function getEmployee(Request $request)
    {

        \Log::info('This is the Employee');
        $onLeave = \DB::table('leaves')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->pluck('emp_id');

        $onSick = \DB::table('employee_sicks')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->pluck('emp_id');

        $unavailable = $onLeave->merge($onSick)->unique();

        $employees = Employee::where('status', 'Active')
            ->whereNotIn('id', $unavailable)
            ->select('id', 'name', 'lastname', 'image')
            ->get();

        return response()->json($employees);
    }


    public function updateLeadEmployee(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'alternative_id' => 'required',
            'customer_id' => 'required',
        ]);

        $employeeId = $request->employee_id !== '' ? $request->employee_id : null;

        $products = LeadProductList::where('product_id', $validated['product_id'])
            ->where('alternative_id', $validated['alternative_id'])
            ->where('customer_id', $validated['customer_id'])
            ->get();

        foreach ($products as $item) {
            $oldEmp = $item->employee_id;
            $item->update(['employee_id' => $employeeId]);

            // LOG
            $this->logActivity('updated', LeadProductList::class, $item->id, $validated['customer_id'], $validated['alternative_id'], $validated['product_id'], [
                'employee_id' => ['from' => $oldEmp, 'to' => $employeeId],
                'info' => 'Mitarbeiter für Produkt aktualisiert'
            ]);
        }

        return response()->json(['success' => true]);
    }
    public function getCustomerProduct($id, $alternative_id)
    {
        $data = DB::table('lead_product_lists as ip')
            // employees
            ->leftJoin('employees as e', 'e.id', '=', 'ip.employee_id')       // Innendienst
            ->leftJoin('employees as fe', 'fe.id', '=', 'ip.field_employee')    // Außendienst

            // product, department
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'ip.product_id')
            ->leftJoin('departments as d', 'd.id', '=', 'ip.department_id')

            // service_id -> phase_sections (per your FK)
            ->leftJoin('phase_sections as ps', 'ps.id', '=', 'ip.service_id')

            ->where('ip.customer_id', $id)
            ->where('ip.alternative_id', $alternative_id)
            ->whereNull('ip.deleted_at')

            ->select(
                // ids
                'ip.id',
                'ip.customer_id',
                'ip.alternative_id',

                // product / service / department
                'ip.product_id',
                'ip.service_id',
                'ip.department_id',
                'ag.article_group',
                'd.department_name',

                // service meta
                DB::raw("COALESCE(ps.phase_section, '') as phase_section"), // this is what your translateService likely needs
                'ip.service', // your free-text service column too (default 'complete')

                // employees
                'ip.employee_id',
                DB::raw('ip.field_employee as field_employee_id'),

                DB::raw("COALESCE(e.name,'') as name"),
                DB::raw("COALESCE(e.lastname,'') as lastname"),
                DB::raw("COALESCE(e.image,'') as image"),

                DB::raw("COALESCE(fe.name,'') as fename"),
                DB::raw("COALESCE(fe.lastname,'') as felastname"),
                DB::raw("COALESCE(fe.image,'') as feimage"),

                // the UI fields you’re missing
                'ip.interest',
                'ip.realization_time',

                'ip.status'
            )
            ->orderBy('ip.id')
            ->get();

        return response()->json($data, 200);
    }

    public function deleteProduct(Request $request)
    {
        // Prefer the exact row id if provided, else require the tuple.
        $request->validate([
            'row_id' => 'nullable|integer|exists:lead_product_lists,id',
            'customer_id' => 'required_without:row_id|integer|exists:new_leads,id',
            'alternative_id' => 'required_without:row_id|integer|exists:lead_alternative_adds,id',
            'product_id' => 'required_without:row_id|integer|exists:article_groups,id',
        ]);

        DB::beginTransaction();
        try {
            if ($request->filled('row_id')) {
                // Delete exact row
                $row = DB::table('lead_product_lists')->where('id', $request->row_id)->first();

                if ($row) {
                    $this->logActivity('deleted', LeadProductList::class, $request->row_id, $row->customer_id, $row->alternative_id, $row->product_id, ['info' => 'Produkt entfernt']);
                    DB::table('lead_product_lists')->where('id', $request->row_id)->delete();

                    DB::table('new_lead_responsibilities')
                        ->where('new_lead_id', $row->customer_id)
                        ->where('product_id', $row->product_id)
                        ->where('alternative_id', $row->alternative_id)
                        ->delete();
                }
            } else {
                // Delete by tuple
                $this->logActivity('deleted', LeadProductList::class, 0, $request->customer_id, $request->alternative_id, $request->product_id, ['info' => 'Produkt via Massenlöschung entfernt']);
                DB::table('lead_product_lists')
                    ->where('customer_id', $request->customer_id)
                    ->where('product_id', $request->product_id)
                    ->where('alternative_id', $request->alternative_id)
                    ->delete();

                DB::table('new_lead_responsibilities')
                    ->where('new_lead_id', $request->customer_id)
                    ->where('product_id', $request->product_id)
                    ->where('alternative_id', $request->alternative_id)
                    ->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Das Produkt und zugehörige Verantwortliche wurden erfolgreich gelöscht.'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Löschen fehlgeschlagen.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLeadEmployee(Request $request)
    {
        $product_id = $request->input('product_id');
        $department_id = $request->input('department_id');
        $service_id = $request->input('service_id');

        // Try to get matching product_position
        $positions = DB::table('product_positions')
            ->where('article_group_id', $product_id)
            ->where('department_id', $department_id)
            ->where('service_id', $service_id)
            ->pluck('position_ids');

        $suggestedPositionIds = collect($positions)
            ->filter()
            ->map(fn($json) => json_decode($json, true) ?: [])
            ->flatten()
            ->unique()
            ->filter()
            ->values()
            ->toArray();

        if (!empty($suggestedPositionIds)) {
            $employees = DB::table('department_positions')
                ->join('employees', 'employees.id', '=', 'department_positions.employee_id')
                ->join('positions', 'positions.id', '=', 'department_positions.position_id')
                ->where('department_positions.department_id', $department_id)
                ->whereIn('department_positions.position_id', $suggestedPositionIds)
                ->select('employees.id as employee_id', 'employees.name', 'employees.lastname', 'employees.image', 'positions.position')
                ->get();
        } else {
            $employees = DB::table('department_positions')
                ->join('employees', 'employees.id', '=', 'department_positions.employee_id')
                ->join('positions', 'positions.id', '=', 'department_positions.position_id')
                ->where('department_positions.department_id', $department_id)
                ->select('employees.id as employee_id', 'employees.name', 'employees.lastname', 'employees.image', 'positions.position')
                ->get();
        }

        // Group employees by ID and map positions
        $grouped = $employees->groupBy('employee_id')->map(function ($items) {
            $first = $items->first();
            return [
                'id' => $first->employee_id,
                'name' => $first->name,
                'lastname' => $first->lastname,
                'image' => $first->image,
                'positions' => $items->pluck('position')->unique()->values()->toArray(),
            ];
        });

        return response()->json($grouped->values());
    }

    public function saveProduct(Request $request)
    {
        \Log::info('Lead products: incoming', $request->all());

        $request->validate([
            'customer_id' => 'required|exists:new_leads,id',
            'alternative_id' => 'required|exists:lead_alternative_adds,id',
            'product_id' => 'required|array',
            'service_id' => 'required|array',
            'department_id' => 'required|array',
            'employee_id' => 'required|array',
            'field_employee' => 'nullable|array',
            'interest' => 'nullable|array',
            'realization_time' => 'nullable|array',
        ]);

        $P = $request->input('product_id', []);
        $S = $request->input('service_id', []);
        $D = $request->input('department_id', []);
        $E = $request->input('employee_id', []);
        $FE = $request->input('field_employee', []);
        $I = $request->input('interest', []);
        $R = $request->input('realization_time', []);

        $n = count($P);

        $rows = [];
        for ($i = 0; $i < $n; $i++) {
            $pid = $P[$i] ?? null;
            $sid = $S[$i] ?? null;
            $did = $D[$i] ?? null;
            $eid = $E[$i] ?? null;
            $fe = $FE[$i] ?? null;
            $it = $I[$i] ?? null;
            $rt = $R[$i] ?? null;

            if (!$pid && !$sid && !$did && !$eid && !$fe && !$it && !$rt)
                continue;

            $errors = [];
            if (!$pid)
                $errors["product_id.$i"] = 'Produkt ist erforderlich.';
            if (!$sid)
                $errors["service_id.$i"] = 'Dienstleistung ist erforderlich.';
            if (!$did)
                $errors["department_id.$i"] = 'Abteilung ist erforderlich.';
            if (!$eid)
                $errors["employee_id.$i"] = 'Innendienst ist erforderlich.';
            if (!$it)
                $errors["interest.$i"] = 'Interesse ist erforderlich.';
            if (!$rt)
                $errors["realization_time.$i"] = 'Realisierungszeit ist erforderlich.';

            if ($errors) {
                throw ValidationException::withMessages($errors);
            }

            $rows[] = [
                'customer_id' => (int) $request->customer_id,
                'alternative_id' => (int) $request->alternative_id,
                'product_id' => (int) $pid,
                'service_id' => (int) $sid,
                'department_id' => (int) $did,
                'employee_id' => (int) $eid,
                'field_employee' => $fe ? (int) $fe : null,
                'interest' => $it,
                'realization_time' => $rt,
                'status' => 'Lead',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($rows)) {
            throw ValidationException::withMessages([
                'rows' => 'Keine gültigen Zeilen. Bitte füllen Sie mindestens eine vollständige Zeile aus.',
            ]);
        }

        try {
            DB::transaction(function () use ($rows, $request) {
                DB::table('lead_product_lists')->insert($rows);

                $this->logActivity(
                    'created',
                    LeadProductList::class,
                    0,
                    $request->customer_id,
                    $request->alternative_id,
                    null,
                    ['info' => count($rows) . ' Produkte hinzugefügt']
                );
            });
        } catch (\Throwable $e) {
            \Log::error('Lead products: insert failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Speichern fehlgeschlagen.',
            ], 500);
        }

        // ...
        return response()->json([
            'success' => true,
            'message' => 'Produkte erfolgreich gespeichert.',
            'inserted' => count($rows),
        ]);
    }
    public function updateProduct(Request $request)
    {
        \Log::info('Product update request: ', $request->all());

        $request->validate([
            'id' => 'required|exists:lead_product_lists,id',
            'product_id' => 'required|exists:article_groups,id',
            'service_id' => 'nullable|exists:phase_sections,id',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id' => 'required|exists:employees,id',
            'field_employee' => 'nullable|exists:employees,id',
            'interest' => 'nullable|string|in:intent,interest,option',
            'realization_time' => 'nullable|string',
        ]);

        DB::table('lead_product_lists')->where('id', $request->id)->update([
            'product_id' => $request->product_id,
            'service_id' => $request->service_id,
            'department_id' => $request->department_id,
            'employee_id' => $request->employee_id,
            'field_employee' => $request->field_employee,
            'interest' => $request->interest,
            'realization_time' => $request->realization_time,
            'updated_at' => now()
        ]);

        $this->logActivity('updated', LeadProductList::class, $request->id, $oldRow->customer_id, $oldRow->alternative_id, $request->product_id, [
            'info' => 'Produktdetails aktualisiert'
        ]);

        return redirect()->back()->with('success', 'Produkt erfolgreich aktualisiert');
    }

    public function showSidebar($leadId, $altId, $productId)
    {
        try {
            $lead = NewLeads::with('branch', 'contact')->findOrFail($leadId);
            $alternative = LeadAlternativeAdd::findOrFail($altId);
            $product = ArticleGroup::findOrFail($productId);

            \Log::info('❌ City: ' . $alternative->city);

            return response()->json([
                'success' => true,
                'lead' => [
                    'id' => $lead->id,
                    'name' => $lead->name,
                    'lastname' => $lead->lastname,
                    'full_address' => $lead->full_address,
                ],
                'alternative' => [
                    'id' => $alternative->id,
                    'title' => $alternative->title,
                    'full_address' => $alternative->full_address,
                    'street' => $alternative->street,
                    'postcode' => $alternative->postcode,
                    'city' => $alternative->city,
                    'lat' => $alternative->lat,
                    'lon' => $alternative->lon,
                    'elevation' => $alternative->elevation,
                    'main' => $alternative->main,
                    'address_no' => $alternative->address_no,
                    'object_name' => $alternative->object_name,
                    'request_date' => $alternative->request_date,
                    'periority' => $alternative->periority,
                    'document' => $alternative->document,
                    'note' => $alternative->note,
                    'appointment' => $alternative->appointment,
                    'appointment_by' => $alternative->appointment_by,
                    'objective' => $alternative->objective,
                    'living_space' => $alternative->living_space,
                    'unusable_space' => $alternative->unusable_space,
                    'number_people' => $alternative->number_people,
                    'number_we' => $alternative->number_we,
                    'number_stories' => $alternative->number_stories,
                    'installation_location' => $alternative->installation_location,
                    'installation_location_extra' => $alternative->installation_location_extra,
                    'annual_consumption' => $alternative->annual_consumption,
                    'tile_name' => $alternative->tile_name,
                    'roof_type' => $alternative->roof_type,
                    'roof_age' => $alternative->roof_age,
                    'house_year' => $alternative->house_year,
                    'heating_system_age' => $alternative->heating_system_age,
                    'heating_system_year' => $alternative->heating_system_year,
                    'heating_type' => $alternative->heating_type,
                    'heating_system_type' => $alternative->heating_system_type,
                    'annual_heating_energy_consumption' => $alternative->annual_heating_energy_consumption,
                    'annual_heating_energy_consumption_kwh' => $alternative->annual_heating_energy_consumption_kwh,
                    'electric_car' => $alternative->electric_car,
                    'electric_car_plan' => $alternative->electric_car_plan,
                    'status' => $alternative->status,
                    'total_number' => $alternative->total_number,
                    'answered_number' => $alternative->answered_number,
                    'roof_covering' => $alternative->roof_covering,
                    'roof_pitch' => $alternative->roof_pitch,
                    'roof_direction' => $alternative->roof_direction,
                    'fireplace' => $alternative->fireplace,
                    'wood_consumption' => $alternative->wood_consumption,
                    'fireplace_value' => $alternative->fireplace_value,
                    'car_kilo' => $alternative->car_kilo,
                    'stage' => $alternative->stage,
                    'project_date' => $alternative->project_date,
                    'object_remark' => $alternative->object_remark,
                    'heating_remark' => $alternative->heating_remark,
                    'roof_remark' => $alternative->roof_remark,
                    'energy_remark' => $alternative->energy_remark,
                    'car_remark' => $alternative->car_remark,
                    'is_owner' => $alternative->is_owner == 1 ? 'Ja' : 'Nein',
                    'is_living_inside' => $alternative->is_living_inside == 1 ? 'Ja' : 'Nein',
                    'income' => $alternative->income,
                    'insolation' => $alternative->insolation,
                    'insolation_thickness' => $alternative->insolation_tickness,
                    'insolation_type' => $alternative->insolation_type,
                    'insolation_matarial' => $alternative->insolation_matarial,
                    'insolation_age' => $alternative->insolation_age,

                    // BEG Förderrechner
                    'usage_type' => $alternative->usage_type,
                    'income_taxed' => $alternative->income_taxed,
                    'heating_age_group' => $alternative->heating_age_group,
                    'natural_refrigerant' => $alternative->natural_refrigerant,
                    'investment_costs' => $alternative->investment_costs,
                    'calculated_subsidy' => $alternative->calculated_subsidy,
                    'calculated_credit_need' => $alternative->calculated_credit_need,
                    'calculated_rate' => $alternative->calculated_rate,
                    'recommended_program' => $alternative->recommended_program,
                    'subsidy_quote' => $alternative->subsidy_quote,

                    'solar_module_kwp' => $alternative->solar_module_kwp,
                    'solar_tile_kwp' => $alternative->solar_tile_kwp,
                    'battery_kwh' => $alternative->battery_kwh,
                    'balcony_modules' => $alternative->balcony_modules,
                    'has_pump_upgrade' => $alternative->has_pump_upgrade,
                    'hydraulic_only' => $alternative->hydraulic_only,
                ],
                'product' => [
                    'id' => $product->id,
                    'article_group' => $product->article_group,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('❌ Sidebar Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Daten konnten nicht geladen werden.'
            ], 500);
        }
    }

    public function updateField(Request $request)
    {
        $request->validate([
            'field' => 'required|string',
            'value' => 'required',
            'lead_id' => 'required|integer',
            'alternative_id' => 'required|integer',
            'product_id' => 'required|integer',
        ]);

        \Log::info('📥 Alternative-only update', $request->all());

        $field = $request->field;
        $value = $request->value;

        // Convert "Ja"/"Nein" to 1/0
        if (in_array($field, ['is_owner', 'is_living_inside', 'natural_refrigerant'])) {
            $value = $value === 'Ja' ? 1 : 0;
        }

        // Convert to numeric where needed
        if (in_array($field, ['income', 'investment_costs', 'income_taxed'])) {
            $value = is_numeric($value) ? floatval($value) : null;
        }

        // Mapping: Frontend field → database column
        $alternativeFieldMap = [
            'house_type' => 'objective',
            'heating_age' => 'heating_system_age',
            'installation_year' => 'heating_system_year',
            'number_we' => 'number_we',
            'status' => 'status',
            'is_owner' => 'is_owner',
            'is_living_inside' => 'is_living_inside',
            'income' => 'income',
            'living_space' => 'living_space',
            // Förderdaten
            'usage_type' => 'usage_type',
            'income_taxed' => 'income_taxed',
            'heating_age_group' => 'heating_age_group',
            'natural_refrigerant' => 'natural_refrigerant',
            'investment_costs' => 'investment_costs',


            'solar_module_kwp' => 'solar_module_kwp',
            'solar_tile_kwp' => 'solar_tile_kwp',
            'battery_kwh' => 'battery_kwh',
            'balcony_modules' => 'balcony_modules',
            'has_pump_upgrade' => 'has_pump_upgrade',
            'hydraulic_only' => 'hydraulic_only',
            'solar_thermal' => 'solar_thermal',
            'solar_thermal_area' => 'solar_thermal_area',
            'solar_thermal_simulation' => 'solar_thermal_simulation',
        ];

        // Load Alternative
        $alternative = LeadAlternativeAdd::find($request->alternative_id);

        if (!$alternative) {
            \Log::warning('❌ Alternative nicht gefunden', [
                'lead_id' => $request->lead_id,
                'alternative_id' => $request->alternative_id,
            ]);
            return response()->json(['success' => false, 'message' => 'Alternative nicht gefunden']);
        }

        // Update allowed fields
        if (array_key_exists($field, $alternativeFieldMap)) {
            $dbField = $alternativeFieldMap[$field];
            $alternative->$dbField = $value;
            $alternative->save();

            \Log::info('✅ Alternative aktualisiert', [
                'field' => $dbField,
                'value' => $value,
            ]);

            $this->logActivity('updated', LeadAlternativeAdd::class, $alternative->id, $request->lead_id, $alternative->id, $request->product_id, [
                $dbField => ['from' => $oldValue, 'to' => $value]
            ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Feld nicht erlaubt für Update']);
    }

    public function saveFunding(Request $request, $customer_id, $alternative_id, $product_id)
    {
        \Log::info('✅ Saving BEG funding input:', $request->all());

        $alt = LeadAlternativeAdd::where('id', $alternative_id)->firstOrFail();

        // Input values
        $data = $request->input('funding_data', []);

        $nutzung = $data['nutzung'] ?? null;
        $wohnheiten = (int) ($data['wohnheiten'] ?? 0);
        $selbst_anzahl = (int) ($data['selbst_anzahl'] ?? 0);
        $einkommen = (float) ($data['einkommen'] ?? 0);
        $heizungsalter = $data['heizungsalter'] ?? null;
        $kaeltemittel = !empty($data['kaeltemittel']);
        $invest = (float) ($data['invest'] ?? 0);

        // New optional values
        $alt->solar_module_kwp = isset($data['solar_module_kwp']) ? (float) $data['solar_module_kwp'] : null;
        $alt->has_pump_upgrade = isset($data['has_pump_upgrade']) ? (bool) $data['has_pump_upgrade'] : null;
        $alt->balcony_modules = isset($data['balcony_modules']) ? (int) $data['balcony_modules'] : null;
        $alt->hydraulic_only = isset($data['hydraulic_only']) ? (bool) $data['hydraulic_only'] : null;
        $alt->solar_thermal = isset($data['solar_thermal']) ? (bool) $data['solar_thermal'] : null;
        $alt->solar_thermal_area = isset($data['solar_thermal_area']) ? (float) $data['solar_thermal_area'] : null;
        $alt->solar_thermal_simulation = isset($data['solar_thermal_simulation']) ? (bool) $data['solar_thermal_simulation'] : null;


        // Zuschuss-Berechnung
        $maxKosten = 30000 + max(0, min($wohnheiten - 1, 5) * 15000) + max(0, $wohnheiten - 6) * 8000;
        $anteilSelbst = $wohnheiten > 0 ? ($selbst_anzahl / $wohnheiten) : 0;
        $anteilVermietet = 1 - $anteilSelbst;

        $quoteSelbst = 30;
        if ($heizungsalter === '20 Jahre oder älter')
            $quoteSelbst += 20;
        if ($kaeltemittel)
            $quoteSelbst += 5;
        if ($einkommen <= 40000)
            $quoteSelbst += 30;
        $quoteSelbst = min($quoteSelbst, 70);

        $quoteVermietet = 30 + ($kaeltemittel ? 5 : 0);

        $foerderbareKosten = min($invest, $maxKosten);
        $zuschuss = round(
            ($quoteSelbst / 100) * $foerderbareKosten * $anteilSelbst +
            ($quoteVermietet / 100) * $foerderbareKosten * $anteilVermietet
        );

        $quoteGesamt = round($anteilSelbst * $quoteSelbst + $anteilVermietet * $quoteVermietet, 2);

        $kreditbedarf = max(0, $invest - $zuschuss);
        $zinssatz = 0.0088;
        $laufzeitJahre = 10;
        $zinsMonat = $zinssatz / 12;
        $monate = $laufzeitJahre * 12;
        $rate = ($kreditbedarf > 0)
            ? ($kreditbedarf * ($zinsMonat * pow(1 + $zinsMonat, $monate)) / (pow(1 + $zinsMonat, $monate) - 1))
            : 0;

        $programm = ($nutzung !== 'vermietet' && $einkommen <= 90000) ? 'KfW 358' : 'KfW 359';

        // Save main funding fields
        $alt->usage_type = $nutzung;
        $alt->number_we = $wohnheiten;
        $alt->number_self_used = $selbst_anzahl;
        $alt->income_taxed = $einkommen;
        $alt->heating_age_group = $heizungsalter;
        $alt->natural_refrigerant = $kaeltemittel;
        $alt->investment_costs = $invest;
        $alt->calculated_subsidy = $zuschuss;
        $alt->calculated_credit_need = $kreditbedarf;
        $alt->calculated_rate = round($rate, 2);
        $alt->recommended_program = $programm;
        $alt->subsidy_quote = $quoteGesamt;

        $alt->save();
        $this->logActivity('updated', LeadAlternativeAdd::class, $alt->id, $customer_id, $alternative_id, $product_id, ['info' => 'Fördermittel-Daten aktualisiert']);

        return response()->json(['status' => 'success']);
    }
    public function loadSectionPartial($customer_id, $alternative_id, $product, $section)
    {
        // Load your standard base data
        $customer = NewLeads::findOrFail($customer_id);
        $alternative = LeadAlternativeAdd::findOrFail($alternative_id);
        $productData = LeadProductList::where('customer_id', $customer_id)
            ->where('alternative_id', $alternative_id)
            ->where('product_id', $product)
            ->firstOrFail();

        $data = $alternative;

        // 🌟 ADDED: Intercept the 'angebote' section to inject offers and analytics
        if ($section === 'angebote') {
            $offers = Offer::with(['creator', 'folders.creator', 'folders.detail'])
                ->where('customer_id', $customer_id)
                ->where('alternative_id', $alternative_id)
                ->where('product_id', $product)
                ->orderBy('created_at', 'desc')
                ->get();

            $analytics = [
                'total_offers' => $offers->count(),
                'total_folders' => 0,
                'total_gross' => 0,
                'total_net' => 0,
            ];

            foreach ($offers as $offer) {
                $analytics['total_folders'] += $offer->folders->count();

                foreach ($offer->folders as $folder) {
                    if ($folder->detail) {
                        $analytics['total_gross'] += (float) $folder->detail->total_gross;
                        $analytics['total_net'] += (float) $folder->detail->total_net;
                    }
                }
            }



            // Pass the required variables AND your base variables so the view doesn't break
            return view("admin.new_leads.layouts.$section", compact(
                'data',
                'customer',
                'productData',
                'alternative',
                'offers',
                'analytics'
            ));
        }

        if ($section === 'auftraege') {
            $deals = Deal::with([
                'customer',
                'alternative',
                'product',
                'department',
                'author',
                'checkedBy',
                'reviewer',
                'offer',
                'folder.detail',
                'latestMeasurement.creator',
                'measurements.creator',
                'latestDeliveryNote.handoverEmployee',
                'deliveryNotes.handoverEmployee',
            ])
                ->where('customer_id', $customer_id)
                ->where('alternative_id', $alternative_id)
                ->where('product_id', $product)
                ->latest()
                ->get();

            $dealIds = $deals->pluck('id');

            $notes = DealNote::query()
                ->whereIn('deal_id', $dealIds)
                ->with(['children'])
                ->latest()
                ->get()
                ->groupBy('deal_id');

            $analytics = [
                'total_deals' => $deals->count(),
                'open_deals' => $deals->filter(function ($deal) {
                    return !in_array(strtolower((string) $deal->status), [
                        'done',
                        'complete',
                        'completed',
                        'geschlossen',
                        'closed',
                        'end',
                        'cancel',
                        'cancelled',
                        'canceled',
                        'storniert',
                    ], true);
                })->count(),
                'completed_deals' => $deals->filter(function ($deal) {
                    return in_array(strtolower((string) $deal->status), [
                        'done',
                        'complete',
                        'completed',
                        'geschlossen',
                        'closed',
                        'end',
                    ], true);
                })->count(),
                'cancelled_deals' => $deals->filter(function ($deal) {
                    return in_array(strtolower((string) $deal->status), [
                        'cancel',
                        'cancelled',
                        'canceled',
                        'storniert',
                    ], true);
                })->count(),
                'total_price' => $deals->sum(fn($deal) => (float) ($deal->price ?? 0)),
                'measurements' => $deals->sum(fn($deal) => $deal->measurements->count()),
                'delivery_notes' => $deals->sum(fn($deal) => $deal->deliveryNotes->count()),
            ];

            return view("admin.new_leads.layouts.$section", compact(
                'data',
                'customer',
                'productData',
                'alternative',
                'deals',
                'notes',
                'analytics'
            ));
        }

        // Default fallback for all other sections (objekt_data, etc.)
        return view("admin.new_leads.layouts.$section", compact('data', 'customer', 'productData', 'alternative'));
    }
    public function loadAlternativePartials($customer_id, $alternative_id, $product_id, $section)
    {
        $alternative = LeadAlternativeAdd::findOrFail($alternative_id);
        $productData = LeadProductList::where('customer_id', $customer_id)
            ->where('alternative_id', $alternative_id)
            ->where('product_id', $product_id)
            ->firstOrFail();
        $roofs = PVRoof::where('alternative_id', $alternative_id)->get(); // or collect() if you expect empty

        // This will load the full wizard-style objekt.blade.php
        return view('admin.new_leads.layouts.objekt', compact('alternative', 'productData', 'roofs'))->render();
    }

    public function saveObjectData(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_alternative_adds,id',
        ]);

        $alternative = LeadAlternativeAdd::findOrFail($request->id);

        /*
        |--------------------------------------------------------------------------
        | 1. Save fields that belong to lead_alternative_adds
        |--------------------------------------------------------------------------
        */
        $alternativeFields = [
            // Object data
            'object_type',
            'building_type',
            'building_condition',
            'usage_type',
            'owner_count',
            'number_we',
            'number_people',
            'person_count',
            'building_year',
            'house_year',
            'story_count',
            'number_stories',
            'heated_area',
            'living_space',
            'unusable_space',
            'external_insulation_thickness',
            'masonry',
            'window_glazing',
            'window_frame',
            'window_year',
            'door_year',
            'door_condition',
            'object_remark',

            // Heating data
            'heating_system_type',
            'heating_type',
            'chimney',
            'fireplace',
            'old_heating_power',
            'heating_circuits_count',
            'wood_consumption',
            'pipe_system_count',
            'pipe_system_material',
            'heating_pipe_dimension',
            'water_pipe_dimension',
            'circulation_pipe_dimension',
            'heating_system_age',
            'quantity',
            'consumption',
            'solar_thermal',
            'solar_thermal_area',
            'bathroom_count',
            'installation_location',
            'installation_location_extra',
            'hot_water_generation',
            'hot_water_tank_liters',
            'bathtub_count',
            'income_level',
            'total_heat_consumption',
            'total_electricity_consumption',
            'heating_load_calculation',
            'flow_temperature',
            'heating_notes',
            'heating_remark',
            'heat_pump_pipe_length',
            'door_width_for_installation',
            'ventilation_type',

            // E-mobility
            'electric_car',
            'electric_car_count',
            'car_kilo',
            'wallbox_count',
            'wallbox_location',
            'heavy_current_cable',
            'network_cable',
            'groundwork',
            'company_vehicle',
            'bidirectional_car',
            'car_remark',

            // Energy usage
            'power_household',
            'power_heatpump',
            'power_electric_car',
            'power_other',
            'power_total',
            'meter_cabinet',
            'meter_count',
            'tenant_model',
            'installation_location_power',
            'network_wlan',
            'energy_remark',

            // PV/WP shared fields that already exist on lead_alternative_adds
            'objective',
            'request_date',
            'kwp_size',
            'module_count',
            'storage_preference',
            'wallbox_desired',
            'meter_cabinet_action',
            'cabinet_size',
            'sls_switch',
            'ac_surge_protection',
            'enwg_14a_ready',
        ];

        foreach ($alternativeFields as $field) {
            if ($request->has($field) && Schema::hasColumn('lead_alternative_adds', $field)) {
                $alternative->{$field} = $this->normalizeValueForAlternative($request, $field);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Checkbox fields on lead_alternative_adds
        |--------------------------------------------------------------------------
        | Only process these when the field belongs to the current submitted form.
        */
        $alternativeCheckboxes = [
            'sls_switch',
            'ac_surge_protection',
            'enwg_14a_ready',
            'wallbox_desired',
        ];

        foreach ($alternativeCheckboxes as $field) {
            if ($request->has($field) && Schema::hasColumn('lead_alternative_adds', $field)) {
                $alternative->{$field} = $this->toBool($request->input($field));
            }
        }

        $alternative->save();

        /*
        |--------------------------------------------------------------------------
        | 2. Save PV/WP extended details into lead_alternative_pv_wp_details
        |--------------------------------------------------------------------------
        */
        $this->savePvWpDetails($request, $alternative);

        /*
        |--------------------------------------------------------------------------
        | 3. Save roofs only when roofs are submitted
        |--------------------------------------------------------------------------
        | Important: this prevents deleting roofs when saving object/heating/energy tabs.
        */
        if ($request->has('roofs')) {
            $this->syncAlternativeRoofs($request, $alternative);
        }
        return response()->json([
            'success' => true,
            'message' => 'Daten wurden gespeichert.',
        ]);
    }

    private function savePvWpDetails(Request $request, LeadAlternativeAdd $alternative): void
    {
        /*
        |--------------------------------------------------------------------------
        | Mapping:
        | request field name => database column in lead_alternative_pv_wp_details
        |--------------------------------------------------------------------------
        */
        $map = [
            // PV - customer gets / components
            'note_demontageVerbleib' => 'demolition_module_destination',
            'note_kabelAusreichend' => 'cables_sufficient',
            'note_battery_type' => 'battery_type',
            'note_battery_size' => 'battery_size',
            'note_battery_location' => 'battery_location',
            'note_batteryDistWrZs' => 'battery_dist_inverter_meter',
            'note_batteryDistBaWr' => 'battery_dist_battery_inverter',

            // WP integration in PV section
            'note_wp_integration' => 'wp_integration',
            'note_wp_type' => 'wp_type',
            'note_wpStatus' => 'wp_status',
            'note_wp_heizstab' => 'wp_heating_rod',

            // Wallbox PV section
            'note_wallbox_distance' => 'wallbox_distance_meter',
            'note_wallboxKernbohrung' => 'wallbox_core_drilling',
            'note_wbErdarbeiten' => 'earthworks_required',
            'note_wbErdarbeitenLaenge' => 'earthworks_length',
            'note_wbErdarbeitenDurch' => 'earthworks_by',
            'note_sonstigeWunsche' => 'customer_special_requests',

            // PV safety / scaffold / lift / crane
            'note_fangschutz' => 'fall_protection_status',
            'note_fangschutz_reason' => 'fall_protection_reason',
            'note_geruestMachbar' => 'scaffold_feasibility',
            'note_scaffold_reason' => 'scaffold_reason',
            'note_geruestMachbar_reason' => 'scaffold_feasibility_reason',
            'note_aufzugMuss' => 'lift_required',
            'note_aufzugMachbar' => 'lift_feasibility',
            'note_aufzug_reason' => 'lift_reason',
            'note_aufzugMachbar_reason' => 'lift_feasibility_reason',
            'note_kranMuss' => 'crane_required',
            'note_kranMachbar' => 'crane_feasibility',
            'note_kran_reason' => 'crane_reason',
            'note_kranMachbar_reason' => 'crane_feasibility_reason',

            // PV / energy cabinet
            'note_zwischenzaehler' => 'meter_cabinet_submeter_required',
            'note_zwischenzaehler_count' => 'submeter_count',
            'note_zwischenzaehlerWp' => 'meter_cabinet_wp_submeter_required',
            'note_zwischenzaehlerWpCount' => 'wp_submeter_count',
            'note_internetSteckdose' => 'internet_socket_required',
            'note_internetSteckdoseDist' => 'internet_socket_distance',

            // WP building
            'note_bathtub' => 'has_bathtub',
            'note_bathtubDim' => 'bathtub_dimensions',
            'note_pool' => 'has_pool',
            'note_poolVolume' => 'pool_volume',

            // WP heating pipes
            'note_einRohr' => 'single_pipe_system',

            // WP floors / heating circuits
            'note_kgHeiz' => 'kg_heating_status',
            'note_egHeiz' => 'eg_heating_status',
            'note_ogHeiz' => 'og_heating_status',
            'note_dgHeiz' => 'dg_heating_status',

            'note_kgFbh' => 'kg_underfloor',
            'note_egFbh' => 'eg_underfloor',
            'note_ogFbh' => 'og_underfloor',
            'note_dgFbh' => 'dg_underfloor',

            'note_kgHk' => 'kg_radiator',
            'note_egHk' => 'eg_radiator',
            'note_ogHk' => 'og_radiator',
            'note_dgHk' => 'dg_radiator',

            'note_flow_temperature_2' => 'hk2_flow_temp',
            'note_reglerKuehlung' => 'controller_cooling_suitable',
            'note_hkvAbgleich' => 'hkv_balancing_suitable',
            'note_stellantriebAbgleich' => 'actuator_balancing_suitable',

            // WP new system
            'note_passivKuehlung' => 'passive_cooling_interest',
            'note_platzVvm500' => 'space_vvm500',
            'note_platzWm320' => 'space_wm320',
            'note_einzelKomponenten' => 'individual_components_required',

            // WP access / installation
            'note_zuwegungHeizraum' => 'heating_room_access_floor',
            'note_treppen' => 'stairs_present',
            'note_t1Breite' => 'door1_width',
            'note_t1Hoehe' => 'door1_height',
            'note_t2Breite' => 'door2_width',
            'note_t2Hoehe' => 'door2_height',
            'note_t3Breite' => 'door3_width',
            'note_t3Hoehe' => 'door3_height',
            'note_t4Breite' => 'door4_width',
            'note_t4Hoehe' => 'door4_height',
            'note_treppenArt' => 'stairs_type',
            'note_treppenBreite' => 'stairs_width',
            'note_anschlussAussen' => 'outdoor_connection_type',

            // WP alternative placement
            'note_alternativeAufstellung' => 'alternative_placement_possible',
            'note_altBreite' => 'alt_access_width',
            'note_altHoehe' => 'alt_access_height',
            'note_altT1Breite' => 'alt_door1_width',
            'note_altT1Hoehe' => 'alt_door1_height',
            'note_altT2Breite' => 'alt_door2_width',
            'note_altT2Hoehe' => 'alt_door2_height',
            'note_altTreppen' => 'alt_stairs_present',
            'note_altTreppenArt' => 'alt_stairs_type',
            'note_altTreppenBreite' => 'alt_stairs_width',

            // WP other / sound
            'note_kondenswasser' => 'condensate_drainage',
            'note_schallGebiet' => 'noise_area_type',
            'note_schallOrt' => 'noise_installation_position',
            'note_schallAbschirmung' => 'noise_shielding',
            'note_schallImmissionOrt' => 'noise_immission_distance',
        ];

        /*
        |--------------------------------------------------------------------------
        | Direct fields already named like the detail-table columns
        |--------------------------------------------------------------------------
        */
        $directFields = [
            'length_ae_zs',
            'length_ae_ie',
            'length_ie_zs',
            'trace_heating_cable_length',
            'wp_meter_present',
            'wp_tariff_planned',
            'internet_repeater_required',
            'meter_cabinet_old_to_subdistribution',
            'meter_cabinet_additional_subdistribution',
        ];

        $data = [];

        foreach ($map as $requestField => $detailColumn) {
            if (!$request->has($requestField)) {
                continue;
            }

            if (!Schema::hasColumn('lead_alternative_pv_wp_details', $detailColumn)) {
                continue;
            }

            $data[$detailColumn] = $this->normalizeDetailValue($detailColumn, $request->input($requestField));
        }

        foreach ($directFields as $field) {
            if (!$request->has($field)) {
                continue;
            }

            if (!Schema::hasColumn('lead_alternative_pv_wp_details', $field)) {
                continue;
            }

            $data[$field] = $this->normalizeDetailValue($field, $request->input($field));
        }

        /*
        |--------------------------------------------------------------------------
        | Also map main-form values into detail columns where useful
        |--------------------------------------------------------------------------
        */
        if ($request->has('flow_temperature') && Schema::hasColumn('lead_alternative_pv_wp_details', 'hk1_flow_temp')) {
            $data['hk1_flow_temp'] = $request->input('flow_temperature');
        }

        if ($request->has('heat_pump_pipe_length') && Schema::hasColumn('lead_alternative_pv_wp_details', 'length_ae_ie')) {
            $data['length_ae_ie'] = $request->input('heat_pump_pipe_length');
        }

        if ($request->has('door_width_for_installation') && Schema::hasColumn('lead_alternative_pv_wp_details', 'access_width')) {
            $data['access_width'] = $request->input('door_width_for_installation');
        }

        if (empty($data)) {
            return;
        }

        LeadAlternativePvWpDetail::updateOrCreate(
            ['lead_alternative_add_id' => $alternative->id],
            $data
        );
    }

    private function syncAlternativeRoofs(Request $request, LeadAlternativeAdd $alternative): void
    {
        $requestRoofs = $request->input('roofs', []);

        /*
        |--------------------------------------------------------------------------
        | Delete removed roofs only when the roof form submitted roofs[]
        |--------------------------------------------------------------------------
        */
        $existingIdsInRequest = collect($requestRoofs)
            ->pluck('id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->values()
            ->toArray();

        PVRoof::where('alternative_id', $alternative->id)
            ->when(count($existingIdsInRequest) > 0, function ($query) use ($existingIdsInRequest) {
                $query->whereNotIn('id', $existingIdsInRequest);
            }, function ($query) {
                /*
                 * If roofs[] exists but is empty, delete all roofs for this alternative.
                 * This only runs on the roof form now because syncRoofs() only runs when request has roofs.
                 */
            })
            ->delete();

        foreach ($requestRoofs as $roof) {
            $roofData = [
                'customer_id' => $alternative->lead_id,
                'alternative_id' => $alternative->id,

                'designation' => $roof['designation'] ?? null,
                'type' => $roof['type'] ?? null,
                'roof_type' => $roof['roof_type'] ?? null,
                'roof_covering_name' => $roof['roof_covering_name'] ?? null,
                'roof_covering_company' => $roof['roof_covering_company'] ?? null,
                'roof_covering_model' => $roof['roof_covering_model'] ?? null,
                'roof_covering_dimensions_cm' => $roof['roof_covering_dimensions_cm'] ?? null,

                'roof_orientation' => $roof['roof_orientation'] ?? null,
                'roof_pitch' => $roof['roof_pitch'] ?? null,
                'roof_area' => $roof['roof_area'] ?? null,
                'roof_height' => $roof['roof_height'] ?? null,
                'roof_age' => $roof['roof_age'] ?? null,

                'roof_insulation' => $roof['roof_insulation'] ?? null,
                'thickness_roof_insulation' => $roof['thickness_roof_insulation'] ?? null,
                'between_rafter_insulation' => $roof['between_rafter_insulation'] ?? null,
                'thickness_between_rafter' => $roof['thickness_between_rafter'] ?? null,
                'insulation_material' => $roof['insulation_material'] ?? null,

                'asbestos' => $this->toBool($roof['asbestos'] ?? null),
                'roof_renovation' => $roof['roof_renovation'] ?? null,
                'structural_analysis_available' => $this->toBool($roof['structural_analysis_available'] ?? null),
                'rafter_overhang_left' => $roof['rafter_overhang_left'] ?? null,
                'rafter_overhang_right' => $roof['rafter_overhang_right'] ?? null,
                'rafter_thickness' => $roof['rafter_thickness'] ?? null,
                'rafter_reinforcement_needed' => $this->toBool($roof['rafter_reinforcement_needed'] ?? null),
                'scaffold_usage' => $this->toBool($roof['scaffold_usage'] ?? null),
                'roof_structures' => $roof['roof_structures'] ?? null,
                'roofer' => $this->toBool($roof['roofer'] ?? null),

                'pv_existing' => $this->toBoolOrNull($roof['pv_existing'] ?? null),
                'construction_year' => $roof['construction_year'] ?? null,
                'module_count' => $roof['module_count'] ?? null,
                'module_power' => $roof['module_power'] ?? null,
                'kwp_size' => $roof['kwp_size'] ?? null,
                'intention' => $roof['intention'] ?? 'Interesse',
                'objective' => $roof['objective'] ?? null,

                'shading' => $roof['shading'] ?? null,
                'dc_cable_route' => $roof['dc_cable_route'] ?? null,
                'storage_preference' => $roof['storage_preference'] ?? null,
                'backup_power' => $roof['backup_power'] ?? null,
                'pv_investment_costs' => $roof['pv_investment_costs'] ?? null,

                'notes' => $roof['notes'] ?? null,
            ];

            /*
            |--------------------------------------------------------------------------
            | Keep only columns that really exist in pv_roofs table
            |--------------------------------------------------------------------------
            */
            $roofData = collect($roofData)
                ->filter(function ($value, $column) {
                    return Schema::hasColumn('p_v_roofs', $column)
                        || Schema::hasColumn('pv_roofs', $column);
                })
                ->toArray();

            if (!empty($roof['id'])) {
                $existingRoof = PVRoof::where('id', $roof['id'])
                    ->where('alternative_id', $alternative->id)
                    ->first();

                if ($existingRoof) {
                    $existingRoof->fill($roofData);
                    $existingRoof->save();
                }

                continue;
            }

            PVRoof::create($roofData);
        }
    }

    private function normalizeValueForAlternative(Request $request, string $field): mixed
    {
        $value = $request->input($field);

        $booleanFields = [
            'sls_switch',
            'ac_surge_protection',
            'enwg_14a_ready',
            'wallbox_desired',
            'tenant_model',
            'load_management',
        ];

        if (in_array($field, $booleanFields, true)) {
            return $this->toBool($value);
        }

        return $value;
    }

    private function normalizeDetailValue(string $column, mixed $value): mixed
    {
        $booleanColumns = [
            'cables_sufficient',
            'wp_integration',
            'wp_heating_rod',
            'wallbox_core_drilling',
            'earthworks_required',

            'meter_cabinet_old_to_subdistribution',
            'meter_cabinet_additional_subdistribution',
            'meter_cabinet_submeter_required',
            'meter_cabinet_wp_submeter_required',

            'internet_repeater_required',
            'internet_socket_required',

            'two_units_present',
            'has_bathtub',
            'has_pool',

            'single_pipe_system',
            'solar_thermal_keep',

            'dhw_electric_dle',
            'dhw_electric_boiler',
            'dhw_electric_ut',

            'kg_underfloor',
            'eg_underfloor',
            'og_underfloor',
            'dg_underfloor',

            'kg_radiator',
            'eg_radiator',
            'og_radiator',
            'dg_radiator',

            'controller_cooling_suitable',
            'hkv_balancing_suitable',
            'actuator_balancing_suitable',

            'passive_cooling_interest',
            'space_vvm500',
            'space_wm320',
            'individual_components_required',

            'stairs_present',
            'alternative_placement_possible',
            'alt_stairs_present',

            'wp_meter_present',
            'wp_tariff_planned',

            'lift_required',
            'crane_required',
        ];

        if (in_array($column, $booleanColumns, true)) {
            return $this->toBool($value);
        }

        return $value;
    }

    private function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if ($value === null) {
            return false;
        }

        return in_array(strtolower((string) $value), [
            '1',
            'true',
            'yes',
            'ja',
            'on',
            'vorhanden',
            'möglich',
        ], true);
    }

    private function toBoolOrNull(mixed $value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $this->toBool($value);
    }


    public function getRoofPartial($index)
    {
        return view('admin.new_leads.partials.roof-fields', compact('index'));
    }

    public function getRoofPartialEdit($index)
    {
        // optionally pass $roof = new PVRoof or an empty array if needed
        return view('admin.new_leads.partials.edit.roof-fields', compact('index'));
    }


    public function getRoofPartialEditProfile($index)
    {
        $roof = null; // create empty row, not from DB
        return view('admin.new_leads.layouts.partials.roof-fields', [
            'index' => $index,
            'roof' => $roof
        ]);
    }


    public function reference(Request $request)
    {
        $leadId = (int) $request->input('customer_id');
        $alternativeId = $request->input('alternative_id');

        $radius = (float) $request->input('radius', 10);
        $selectedStatus = $request->input('status', '');
        $selectedProduct = $request->input('product_id', '');

        $lead = DB::table('new_leads')->where('id', $leadId)->first();

        if (!$lead) {
            abort(404, 'Kunde wurde nicht gefunden.');
        }

        $baseAlternativeQuery = DB::table('lead_alternative_adds')
            ->where('lead_id', $leadId);

        if ($alternativeId) {
            $baseAlternativeQuery->where('id', $alternativeId);
        }

        $baseAlternative = $baseAlternativeQuery
            ->whereNotNull('lat')
            ->whereNotNull('lon')
            ->first();

        if (!$baseAlternative) {
            $baseAlternative = DB::table('lead_alternative_adds')
                ->where('lead_id', $leadId)
                ->first();
        }

        $baseLat = $baseAlternative && $baseAlternative->lat !== null
            ? (float) $baseAlternative->lat
            : null;

        $baseLng = $baseAlternative && $baseAlternative->lon !== null
            ? (float) $baseAlternative->lon
            : null;

        $hasCoords = $baseLat !== null && $baseLng !== null;

        $totals = [
            'customers' => DB::table('new_leads')->count(),

            'offers' => DB::table('lead_product_lists')
                ->where('status', 'offer')
                ->distinct('customer_id')
                ->count('customer_id'),

            'deals' => DB::table('lead_product_lists')
                ->where('status', 'deal')
                ->distinct('customer_id')
                ->count('customer_id'),

            'projects' => DB::table('lead_product_lists')
                ->where('status', 'project')
                ->distinct('customer_id')
                ->count('customer_id'),

            'products' => DB::table('article_groups')->count(),

            'tickets' => $this->safeCount('tickets'),
        ];

        $productOptions = DB::table('article_groups')
            ->select('id', 'article_group as name')
            ->orderBy('article_group')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->name,
            ])
            ->values()
            ->toArray();

        $neighbors = collect();

        if ($hasCoords) {
            $query = DB::table('lead_alternative_adds as laa')
                ->join('new_leads as nl', 'laa.lead_id', '=', 'nl.id')
                ->leftJoin('lead_product_lists as lpl', 'lpl.customer_id', '=', 'nl.id')
                ->leftJoin('article_groups as ag', 'lpl.product_id', '=', 'ag.id')
                ->select(
                    'laa.id as alternative_id',
                    'laa.lead_id',
                    'laa.lat',
                    'laa.lon',
                    'laa.full_address',
                    'laa.street',
                    'laa.postcode',
                    'laa.city',
                    'nl.id as customer_id',
                    'nl.name as customer_name',
                    'nl.lastname as customer_lastname',
                    'nl.firma',
                    DB::raw("
                    GROUP_CONCAT(
                        DISTINCT CONCAT(
                            COALESCE(ag.article_group, 'Unbekannt'),
                            ' (',
                            COALESCE(lpl.status, 'other'),
                            ')'
                        )
                        SEPARATOR ', '
                    ) as product_statuses
                ")
                )
                ->whereNotNull('laa.lat')
                ->whereNotNull('laa.lon')
                ->groupBy(
                    'laa.id',
                    'laa.lead_id',
                    'laa.lat',
                    'laa.lon',
                    'laa.full_address',
                    'laa.street',
                    'laa.postcode',
                    'laa.city',
                    'nl.id',
                    'nl.name',
                    'nl.lastname',
                    'nl.firma'
                );

            if ($selectedStatus) {
                $query->where('lpl.status', $selectedStatus);
            }

            if ($selectedProduct) {
                $query->where('lpl.product_id', $selectedProduct);
            }

            $rows = $query->get();

            $neighbors = $rows
                ->map(function ($item) use ($baseLat, $baseLng, $leadId, $alternativeId) {
                    $item->distance_km = $this->haversineDistance(
                        $baseLat,
                        $baseLng,
                        (float) $item->lat,
                        (float) $item->lon
                    );

                    $item->is_current =
                        (int) $item->customer_id === (int) $leadId &&
                        (!$alternativeId || (int) $item->alternative_id === (int) $alternativeId);

                    $item->display_name = trim(($item->customer_name ?? '') . ' ' . ($item->customer_lastname ?? ''));

                    $item->product_rows = $this->getNeighborProductRows((int) $item->customer_id);

                    return $item;
                })
                ->filter(fn($item) => (float) $item->distance_km <= $radius)
                ->sortBy('distance_km')
                ->values();
        }

        $neighborsForJs = $neighbors->map(function ($item) {
            return [
                'customer_id' => $item->customer_id,
                'alternative_id' => $item->alternative_id,
                'name' => trim(($item->customer_name ?? '') . ' ' . ($item->customer_lastname ?? '')),
                'address' => $item->full_address,
                'lat' => (float) $item->lat,
                'lng' => (float) $item->lon,
                'distance_km' => round((float) $item->distance_km, 2),
                'product_statuses' => $item->product_statuses,
                'is_current' => !empty($item->is_current),
                'url' => url('/new_lead_profile/' . $item->customer_id),
            ];
        })->values();

        return view('admin.new_leads.layouts.neighbor', compact(
            'totals',
            'lead',
            'baseAlternative',
            'baseLat',
            'baseLng',
            'hasCoords',
            'radius',
            'neighbors',
            'neighborsForJs',
            'selectedStatus',
            'selectedProduct',
            'productOptions'
        ));
    }
    public function references(Request $request)
    {
        $totals = [
            'customers' => DB::table('new_leads')->count(),

            'offers' => DB::table('lead_product_lists')
                ->where('status', 'offer')
                ->distinct('customer_id')
                ->count('customer_id'),

            'deals' => DB::table('lead_product_lists')
                ->where('status', 'deal')
                ->distinct('customer_id')
                ->count('customer_id'),

            'projects' => DB::table('lead_product_lists')
                ->where('status', 'project')
                ->distinct('customer_id')
                ->count('customer_id'),

            'products' => DB::table('article_groups')->count(),

            'tickets' => $this->safeCount('tickets'),
        ];

        return view('admin.new_leads.reference.reference', compact('totals'));
    }
    
    private function getNeighborProductRows(int $customerId): array
    {
        $leadProducts = DB::table('lead_product_lists as lpl')
            ->leftJoin('article_groups as ag', 'lpl.product_id', '=', 'ag.id')
            ->where('lpl.customer_id', $customerId)
            ->select(
                'lpl.product_id',
                'lpl.status',
                'ag.article_group as product_name'
            )
            ->get()
            ->map(function ($item) {
                return [
                    'source' => 'lead_product_list',
                    'product_id' => $item->product_id,
                    'product_name' => $item->product_name ?: 'Unbekannt',
                    'stage_key' => $item->status ?: 'other',
                    'stage_label' => $this->stageLabel($item->status ?: 'other'),
                ];
            });

        $customerProductInfos = collect();

        if (Schema::hasTable('customer_product_infos')) {
            $customerProductInfos = DB::table('customer_product_infos as cpi')
                ->leftJoin('article_groups as ag', 'cpi.product_id', '=', 'ag.id')
                ->where('cpi.customer_id', $customerId)
                ->select(
                    'cpi.product_id',
                    'cpi.product_count',
                    'cpi.serial_number',
                    'cpi.notes',
                    'ag.article_group as product_name'
                )
                ->get()
                ->map(function ($item) {
                    return [
                        'source' => 'customer_product_info',
                        'product_id' => $item->product_id,
                        'product_name' => $item->product_name ?: 'Unbekannt',
                        'stage_key' => 'customer_product',
                        'stage_label' => 'Kundenprodukt',
                        'product_count' => $item->product_count,
                        'serial_number' => $item->serial_number,
                        'notes' => $item->notes,
                    ];
                });
        }

        return $leadProducts
            ->merge($customerProductInfos)
            ->values()
            ->toArray();
    }

    private function stageLabel(?string $status): string
    {
        return [
            'lead' => 'Anfrage',
            'offer' => 'Angebot',
            'deal' => 'Auftrag',
            'project' => 'Projekt',
            'completed' => 'Abgeschlossen',
            'complete' => 'Abgeschlossen',
            'archive' => 'Archiv',
            'ticket' => 'Ticket',
            'pause' => 'Pausiert',
            'cancel' => 'Storniert',
            'other' => 'Sonstiges',
        ][$status] ?? ucfirst((string) $status);
    }
    private function safeCount(string $table): int
    {
        try {
            return DB::table($table)->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function nearby(Request $request)
    {
        $lat = $request->filled('lat') ? (float) $request->lat : null;
        $lon = $request->filled('lon') ? (float) $request->lon : null;
        $radius = $request->filled('radius') ? (float) $request->radius : null;

        $query = DB::table('lead_alternative_adds')
            ->join('new_leads', 'lead_alternative_adds.lead_id', '=', 'new_leads.id')
            ->leftJoin('lead_product_lists', 'lead_product_lists.customer_id', '=', 'new_leads.id')
            ->leftJoin('article_groups', 'lead_product_lists.product_id', '=', 'article_groups.id')
            ->select(
                'lead_alternative_adds.lat',
                'lead_alternative_adds.lon',
                'lead_alternative_adds.full_address',
                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',
                'new_leads.id as customer_id',
                DB::raw("
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        COALESCE(article_groups.article_group, 'Unbekannt'),
                        ' (',
                        COALESCE(lead_product_lists.status, 'other'),
                        ')'
                    )
                    SEPARATOR ', '
                ) as product_statuses
            ")
            )
            ->whereNotNull('lead_alternative_adds.lat')
            ->whereNotNull('lead_alternative_adds.lon')
            ->groupBy(
                'lead_alternative_adds.lat',
                'lead_alternative_adds.lon',
                'lead_alternative_adds.full_address',
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.id'
            );

        $rows = $query->get();

        if ($lat !== null && $lon !== null && $radius !== null) {
            $rows = $rows->filter(function ($item) use ($lat, $lon, $radius) {
                return $this->haversineDistance(
                    $lat,
                    $lon,
                    (float) $item->lat,
                    (float) $item->lon
                ) <= $radius;
            })->values();
        }

        return response()->json($rows);
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function dashboardLoad(Request $request)
    {
        $customerId = $request->get('customer_id');
        $productId = $request->get('product_id');
        $serviceId = $request->get('service_id');
        $stage = $request->get('stage');
        $alternativeId = $request->get('alternative_id');
        $onlyBlocked = $request->boolean('only_blocked'); // 🔍 Optional toggle

        $objects = LeadAlternativeAdd::query()
            ->where('lead_id', $customerId)
            ->when($alternativeId, fn($q) => $q->where('id', $alternativeId))
            ->with([
                'latestScreenshot',
                'cardNotes',
                'products' => function ($query) use ($productId, $serviceId, $stage) {
                    $query->with(['employee', 'fieldEmployee', 'product', 'department'])
                        ->when($productId, fn($q) => $q->where('product_id', $productId))
                        ->when($serviceId, fn($q) => $q->where('service_id', $serviceId))
                        ->when($stage, fn($q) => $q->where('stage', $stage));
                }
            ])
            ->get();

        $objects->each(function ($object) {
            if (!$object->products) {
                return;
            }

            $object->products->each(function ($product) {
                $teams = $this->decodeTeamAssignments($product->teams ?? []);

                $product->teams = $teams;
                $product->team_assignments = $this->decorateTeamAssignments($teams);
                $product->team_members = collect($product->team_assignments)
                    ->pluck('member')
                    ->filter()
                    ->values()
                    ->all();
            });
        });

        // ————————————————————————————————————————————————————————————————
        // Batch-load all time_summaries for these alternatives/products/sections
        // ————————————————————————————————————————————————————————————————
        $altIds = $objects->pluck('id')->unique()->values();

        // Optional: further narrow by requested product/service if present
        $productIds = $objects->flatMap(fn($o) => $o->products->pluck('product_id'))->unique()->values();
        $sectionIds = $objects->flatMap(fn($o) => $o->products->pluck('service_id'))->unique()->values();

        $timeSummaries = TimeSummary::query()
            ->where('customer_id', $customerId)
            ->whereIn('alternative_id', $altIds)
            ->when($productIds->isNotEmpty(), fn($q) => $q->whereIn('product_id', $productIds))
            ->when($sectionIds->isNotEmpty(), fn($q) => $q->whereIn('section_id', $sectionIds))
            ->get();

        // Index: alt|product|section  => Collection<TimeSummary>
        $tsIndex = $timeSummaries->groupBy(fn($ts) => "{$ts->alternative_id}|{$ts->product_id}|{$ts->section_id}");

        // Helper to map TimeSummary → clean array for JSON
        $mapSummary = function (?TimeSummary $ts) {
            if (!$ts)
                return null;

            $plan = (int) $ts->plan_minutes;
            $actual = (int) $ts->actual_minutes;
            $diff = (int) $ts->diff_minutes;
            $pct = $ts->weighted_percent ?? ($plan > 0 ? (int) round(($diff / max(1, $plan)) * 100) : null);

            // handy HH:MM strings for UI (keep signed for diff)
            $fmtHM = function (int $m, bool $signed = false) {
                $sign = $signed && $m > 0 ? '+' : ($m < 0 ? '-' : '');
                $mm = abs($m);
                return $sign . sprintf('%02d:%02d', intdiv($mm, 60), $mm % 60);
            };

            // Choose a Feather icon based on diff
            $icon = is_null($pct) ? 'minus-circle' : ($diff > 0 ? 'thumbs-down' : ($diff < 0 ? 'thumbs-up' : 'check-circle'));
            $icolor = is_null($pct) ? 'text-muted' : ($diff > 0 ? 'text-danger' : ($diff < 0 ? 'text-success' : 'text-secondary'));

            return [
                'plan_minutes' => $plan,
                'actual_minutes' => $actual,
                'diff_minutes' => $diff,
                'weighted_percent' => $pct,
                'completed_cap_minutes' => (int) $ts->completed_cap_minutes,
                'activities_count' => (int) $ts->activities_count,
                'done_activities_count' => (int) $ts->done_activities_count,
                'half_activities_count' => (int) $ts->half_activities_count,
                'overruns_count' => (int) $ts->overruns_count,
                'latest_done_date' => $ts->latest_done_date,
                // formatted strings for direct display in UI headers/badges
                'plan_hm' => $fmtHM($plan),
                'actual_hm' => $fmtHM($actual),
                'diff_hm' => $fmtHM($diff, true),
                'percent_str' => is_null($pct) ? '--' : ($pct > 0 ? "+{$pct}%" : "{$pct}%"),
                // feather status icon hint
                'status_icon' => $icon,       // e.g. 'thumbs-down'
                'status_icon_class' => $icolor,     // e.g. 'text-danger'
            ];
        };

        $result = $objects->map(function ($object) use ($onlyBlocked, $tsIndex, $mapSummary) {
            return [
                'id' => $object->id,
                'object_name' => $object->object_name,
                'street' => $object->street,
                'postcode' => $object->postcode,
                'city' => $object->city,
                'customer_id' => $object->lead_id,
                'screenshot_image' => ($object->latestScreenshot && $object->latestScreenshot->image)
                    ? [
                        'src' => route('secure.image.byFilename', ['filename' => $object->latestScreenshot->image]),
                        'alt' => $object->latestScreenshot->image_name ?? '',
                        'customer_id' => $object->lead_id,
                        'alternative_id' => $object->id,
                        'address' => trim("{$object->street} {$object->postcode} {$object->city}"),
                    ]
                    : null,

                'card_notes' => $object->cardNotes->map(fn($note) => [
                    'id' => $note->id,
                    'title' => $note->title,
                    'description' => $note->description,
                    'customer_id' => $note->customer_id,
                    'alternative_id' => $note->alternative_id,
                    'product_id' => $note->product_id,
                    'created_at' => $note->created_at->format('d.m.Y H:i'),
                ]),

                'products' => $object->products->filter(function ($product) use ($onlyBlocked) {
                    $currentStage = $product->stage ?? $product->status ?? 'unbekannt';
                    $isBlocked = in_array(strtolower($currentStage), ['junk', 'cancel', 'pause', 'absage']);
                    return !$onlyBlocked || $isBlocked;
                })->map(function ($product) use ($object, $tsIndex, $mapSummary) {
                    $employee = $product->employee;
                    $fieldEmployee = $product->fieldEmployee;
                    $productId = $product->product_id;
                    $initial = $product->product->initial ?? 'NA';

                    $version = CustomerStage::where([
                        'customer_id' => $object->lead_id,
                        'alternative_id' => $object->id,
                        'product_id' => $productId,
                    ])->value('version') ?? '001';

                    // stage_history may already be cast to array; make it robust
                    $rawStageHistory = $product->stage_history;

                    if (is_array($rawStageHistory)) {
                        $stageHistoryArr = $rawStageHistory;
                    } elseif (is_string($rawStageHistory)) {
                        $stageHistoryArr = json_decode($rawStageHistory ?: '[]', true) ?: [];
                    } else {
                        $stageHistoryArr = [];
                    }

                    $stageHistory = collect($stageHistoryArr);

                    $currentStage = $product->stage ?? $product->status ?? 'unbekannt';
                    $oldStage = $stageHistory->count() > 1
                        ? ($stageHistory[$stageHistory->count() - 2]['stage'] ?? null)
                        : null;


                    $blockedStages = ['junk', 'cancel', 'pause', 'absage'];
                    $isBlocked = in_array(strtolower($currentStage), $blockedStages);

                    $blockReasonEntry = null;
                    $blockReasonData = null;
                    if ($isBlocked) {
                        $blockReasonEntry = $stageHistory->where('stage', $currentStage)
                            ->sortByDesc('changed_at')
                            ->firstWhere('description', '!=', null);

                        if ($blockReasonEntry) {
                            $changer = Employee::find($blockReasonEntry['changed_by'] ?? null);
                            $blockReasonData = [
                                'description' => $blockReasonEntry['description'],
                                'changed_at' => \Carbon\Carbon::parse($blockReasonEntry['changed_at'])->format('d.m.Y H:i'),
                                'changed_by' => $changer ? [
                                    'name' => $changer->name . ' ' . $changer->lastname,
                                    'image' => $changer->image,
                                ] : null,
                            ];
                        }
                    }

                    $customerHistory = CustomerHistory::where([
                        'customer_id' => $object->lead_id,
                        'alternative_id' => $object->id,
                        'product_id' => $productId,
                        'section_id' => $product->service_id,
                    ])->latest('updated_at')->first();

                    $phase = $customerHistory?->phase_id ? TaskPhase::find($customerHistory->phase_id) : null;
                    $activity = $customerHistory?->activity_id ? PhaseActivities::find($customerHistory->activity_id) : null;

                    $totalActivities = $phase ? PhaseActivities::where('phase_id', $phase->id)->count() : 0;
                    $doneActivities = $phase ? CustomerHistory::where([
                        'customer_id' => $object->lead_id,
                        'alternative_id' => $object->id,
                        'product_id' => $productId,
                        'section_id' => $product->service_id,
                        'is_done' => 1,
                        'phase_id' => $phase->id,
                    ])->count() : 0;

                    $progress = $totalActivities > 0 ? floor(($doneActivities / $totalActivities) * 100) : 0;

                    $doneBy = $customerHistory?->done_by ? Employee::find($customerHistory->done_by) : null;
                    $markedBy = $customerHistory?->marked_by ? Employee::find($customerHistory->marked_by) : null;

                    // ——————————————————————————————————————————————
                    // Attach time_summaries (total + per-phase) for this product
                    // ——————————————————————————————————————————————
                    $key = "{$object->id}|{$productId}|" . ($product->section_id ?? $product->service_id);
                    $bucket = $tsIndex->get($key, collect());

                    $totalSummary = $mapSummary($bucket->firstWhere('scope', 'total'));
                    // phases keyed by phase_id for convenient lookup
                    $phaseSummaries = $bucket->where('scope', 'phase')
                        ->mapWithKeys(function ($ts) use ($mapSummary) {
                        return [$ts->phase_id => $mapSummary($ts)];
                    });

                    return [
                        'id' => $product->id,
                        'product_id' => $productId,
                        'section_id' => $product->section_id ?? $product->service_id,
                        'service_id' => $product->service_id,
                        'department_id' => $product->department_id,
                        'employee_id' => $product->employee_id,
                        'field_employee_id' => $product->fieldEmployee,
                        'service' => $product->service,
                        'status' => $product->status,
                        'stage' => $currentStage,
                        'stageText' => ucfirst($currentStage),
                        'old_stage' => $oldStage,
                        'is_blocked' => $isBlocked,
                        'block_reason' => $blockReasonData,
                        'interest' => $product->interest,
                        'realization_time' => $product->realization_time,
                        'initial' => $initial,
                        'version' => $version,
                        'initial_with_version' => "$initial-$version",

                        'employee' => $employee ? [
                            'name' => $employee->name,
                            'lastname' => $employee->lastname,
                            'image' => $employee->image,
                        ] : null,

                        'field_employee' => $fieldEmployee ? [
                            'name' => $fieldEmployee->name,
                            'lastname' => $fieldEmployee->lastname,
                            'image' => $fieldEmployee->image,
                        ] : null,

                        'progress' => [
                            'done' => $doneActivities,
                            'total' => $totalActivities,
                            'value' => $progress,
                        ],

                        'department' => $product->department ? [
                            'name' => $product->department->department_name,
                            'icon' => $product->department->icon,
                        ] : null,

                        'history' => $customerHistory ? [
                            'done_by' => $customerHistory->done_by,
                            'done_by_name' => $doneBy?->name . ' ' . $doneBy?->lastname,
                            'done_by_image' => $doneBy?->image,
                            'marked_by' => $customerHistory->marked_by,
                            'marked_by_name' => $markedBy?->name . ' ' . $markedBy?->lastname,
                            'marked_by_image' => $markedBy?->image,
                            'is_done' => $customerHistory->is_done,
                            'done_date' => $customerHistory->done_date,
                            'notes' => $customerHistory->notes,
                            'phase_id' => $customerHistory->phase_id,
                            'phase_name' => $phase?->phase_name,
                            'activity_id' => $customerHistory->activity_id,
                            'activity_title' => $activity?->title,
                            'changed_at' => $customerHistory->updated_at?->format('d.m.Y H:i'),
                        ] : null,

                        // ✨ NEW: time summary payload for dashboards
                        'time_summary' => [
                            'total' => $totalSummary,          // or null
                            'phases' => $phaseSummaries,        // { phase_id: {...}, ... }
                        ],

                        // Stage/team workflow payload used by customer profile cards and SweetAlert history modal.
                        // Without these fields the profile shows "Kein Team gespeichert" although Kanban saved the teams.
                        'teams' => $product->teams ?? [],
                        'team_assignments' => $product->team_assignments ?? [],
                        'team_members' => $product->team_members ?? [],

                        'updated_at' => $product->updated_at,
                    ];
                }),
            ];
        });

        \Log::info('✅ Loaded Dashboard with History:', [$result]);

        return response()->json($result);
    }


    public function dashboard($customerId, $alternativeId)
    {
        $customer = NewLeads::find($customerId);

        if (!$customer) {
            return redirect()->back()->with('delete_msg', 'Der Kunde ist nicht im System');
        }

        return view('admin.new_leads.layouts.dashboard', compact('customer', 'customerId', 'alternativeId'));
    }

    public function loadHistoryModal(Request $request)
    {
        \Log::info('🔍 Load History Modal Request', $request->all());

        $customerId = (int) $request->input('customer_id');
        $alternativeId = (int) $request->input('alternative_id');
        $productId = (int) $request->input('product_id');
        $serviceId = (int) $request->input('service_id');
        $requestedStageKey = $request->input('stage');

        /*
        |--------------------------------------------------------------------------
        | Small local helpers
        |--------------------------------------------------------------------------
        */
        $hasColumn = function (string $table, string $column): bool {
            try {
                return Schema::hasColumn($table, $column);
            } catch (\Throwable $e) {
                return false;
            }
        };

        $stageOrderSql = $hasColumn('stages', 'sort_order')
            ? 'COALESCE(sort_order, id)'
            : 'id';

        $phaseOrderSql = $hasColumn('task_phases', 'sort_order')
            ? 'COALESCE(sort_order, id)'
            : ($hasColumn('task_phases', 'order') ? '`order`' : 'id');

        $activityOrderSql = $hasColumn('phase_activities', 'sort_order')
            ? 'COALESCE(sort_order, id)'
            : 'id';

        /*
        |--------------------------------------------------------------------------
        | Base entities
        |--------------------------------------------------------------------------
        */
        $customer = NewLeads::find($customerId);

        $alternative = DB::table('lead_alternative_adds')
            ->where('id', $alternativeId)
            ->where('lead_id', $customerId)
            ->when($hasColumn('lead_alternative_adds', 'deleted_at'), function ($q) {
                $q->whereNull('deleted_at');
            })
            ->first();

        $note = DB::table('customer_card_notes')
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->first();

        $productList = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections as section', 'section.id', '=', 'lead_product_lists.service_id')
            ->leftJoin('employees', 'employees.id', '=', 'lead_product_lists.employee_id')
            ->select(
                'employees.name',
                'employees.lastname',
                'employees.image',
                'departments.department_name',
                'section.phase_section',
                'article_groups.initial',
                'article_groups.article_group',
                'lead_product_lists.id as lead_product_list_id',
                'lead_product_lists.interest',
                'lead_product_lists.realization_time',
                'lead_product_lists.status',
                'lead_product_lists.stage',
                'lead_product_lists.stage_history',
                'lead_product_lists.service_id'
            )
            ->where('lead_product_lists.customer_id', $customerId)
            ->where('lead_product_lists.alternative_id', $alternativeId)
            ->where('lead_product_lists.product_id', $productId)
            ->when($hasColumn('lead_product_lists', 'deleted_at'), function ($q) {
                $q->whereNull('lead_product_lists.deleted_at');
            })
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Find active/saved workflow version from customer_stages
        |--------------------------------------------------------------------------
        | customer_stages contains many rows for one saved version.
        | Therefore we group by version and take the latest active version.
        |--------------------------------------------------------------------------
        */
        $activeVersionRow = CustomerStage::query()
            ->select(
                'version',
                DB::raw('MAX(updated_at) as latest_updated_at'),
                DB::raw('MAX(id) as latest_id')
            )
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->where('section_id', $serviceId)
            ->whereNotNull('version')
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereIn('status', [
                        'active',
                        'Aktiv',
                        'published',
                        'Published',
                    ]);
            })
            ->groupBy('version')
            ->orderByDesc('latest_updated_at')
            ->orderByDesc('latest_id')
            ->first();

        $usedVersion = $activeVersionRow?->version;

        /*
        |--------------------------------------------------------------------------
        | Fallback 1: same customer/object/product without section
        |--------------------------------------------------------------------------
        */
        if (!$usedVersion) {
            $activeVersionRow = CustomerStage::query()
                ->select(
                    'version',
                    DB::raw('MAX(updated_at) as latest_updated_at'),
                    DB::raw('MAX(id) as latest_id')
                )
                ->where('customer_id', $customerId)
                ->where('alternative_id', $alternativeId)
                ->where('product_id', $productId)
                ->whereNotNull('version')
                ->where(function ($q) {
                    $q->whereNull('status')
                        ->orWhereIn('status', [
                            'active',
                            'Aktiv',
                            'published',
                            'Published',
                        ]);
                })
                ->groupBy('version')
                ->orderByDesc('latest_updated_at')
                ->orderByDesc('latest_id')
                ->first();

            $usedVersion = $activeVersionRow?->version;
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback 2: product default version from stages
        | IMPORTANT: do not load soft-deleted stages
        |--------------------------------------------------------------------------
        */
        if (!$usedVersion) {
            $defaultStageQuery = Stage::query()
                ->where('product_id', $productId)
                ->where('default', 'yes');

            if ($hasColumn('stages', 'deleted_at')) {
                $defaultStageQuery->whereNull('deleted_at');
            }

            $usedVersion = $defaultStageQuery->value('version');
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback 3: first available version from non-deleted stages
        |--------------------------------------------------------------------------
        */
        if (!$usedVersion) {
            $firstVersionQuery = Stage::query()
                ->where('product_id', $productId);

            if ($hasColumn('stages', 'deleted_at')) {
                $firstVersionQuery->whereNull('deleted_at');
            }

            $usedVersion = $firstVersionQuery
                ->orderBy('version')
                ->value('version');
        }

        /*
        |--------------------------------------------------------------------------
        | If still no version, return empty modal
        |--------------------------------------------------------------------------
        */
        if (!$usedVersion) {
            \Log::error('❌ No active stage version found.', [
                'customer_id' => $customerId,
                'alternative_id' => $alternativeId,
                'product_id' => $productId,
                'section_id' => $serviceId,
            ]);

            return view('admin.new_leads.layouts.history_modal', [
                'allActivities' => collect(),
                'phaseActs' => collect(),
                'groupedPhases' => [],
                'stage' => $requestedStageKey,
                'customer_id' => $customerId,
                'alternative_id' => $alternativeId,
                'productId' => $productId,
                'serviceId' => $serviceId,
                'firstItem' => null,
                'phaseGroup' => collect(),
                'usedVersion' => null,
                'currentActivityId' => null,
                'currentPhaseId' => null,
                'currentStageKey' => null,
                'customer' => $customer,
                'alternative' => $alternative,
                'productList' => $productList,
                'note' => $note,
                'overallPercent' => 0,
                'doneActivities' => 0,
                'totalActivities' => 0,
                'timeSummaryTotal' => null,
                'timeSummariesPhase' => collect(),
            ]);
        }

        \Log::info('📦 Using active customer stage version', [
            'version' => $usedVersion,
            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product_id' => $productId,
            'section_id' => $serviceId,
        ]);

        /*
        |--------------------------------------------------------------------------
        | History per phase/activity
        |--------------------------------------------------------------------------
        */
        $historyList = CustomerHistory::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->where('section_id', $serviceId)
            ->get()
            ->keyBy(function ($row) {
                return "{$row->phase_id}_{$row->activity_id}";
            });

        /*
        |--------------------------------------------------------------------------
        | Load stages for active version
        | IMPORTANT: no soft-deleted stages
        |--------------------------------------------------------------------------
        */
        $stageQuery = Stage::query()
            ->where('product_id', $productId)
            ->where('version', $usedVersion);

        if ($hasColumn('stages', 'deleted_at')) {
            $stageQuery->whereNull('deleted_at');
        }

        $stages = $stageQuery
            ->orderByRaw($stageOrderSql)
            ->get();

        $stagesById = $stages->keyBy('id');

        /*
        |--------------------------------------------------------------------------
        | Group stage tabs in pipeline order
        |--------------------------------------------------------------------------
        */
        $pipelineOrder = [
            'offer',
            'deal',
            'project',
            'completed',
            'complete',
            'evaluation',
            'review',
            'archive',
        ];

        $groupedPhases = [];

        foreach ($pipelineOrder as $code) {
            $stageModel = $stages->firstWhere('stage', $code);

            if ($stageModel) {
                $groupedPhases[$stageModel->stage] = collect();
            }
        }

        foreach ($stages as $stageModel) {
            if (!array_key_exists($stageModel->stage, $groupedPhases)) {
                $groupedPhases[$stageModel->stage] = collect();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Load task phases
        | IMPORTANT: no soft-deleted task phases
        |--------------------------------------------------------------------------
        */
        $taskPhaseQuery = TaskPhase::query()
            ->where('product_id', $productId)
            ->where('section_id', $serviceId);

        if ($hasColumn('task_phases', 'version')) {
            $taskPhaseQuery->where('version', $usedVersion);
        }

        if ($hasColumn('task_phases', 'deleted_at')) {
            $taskPhaseQuery->whereNull('deleted_at');
        }

        $taskPhases = $taskPhaseQuery
            ->orderByRaw($phaseOrderSql)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Fallback: old task phases without version
        |--------------------------------------------------------------------------
        */
        if ($taskPhases->isEmpty()) {
            \Log::warning("⚠️ No task phases found for version [{$usedVersion}], loading section phases without version.");

            $fallbackTaskPhaseQuery = TaskPhase::query()
                ->where('product_id', $productId)
                ->where('section_id', $serviceId);

            if ($hasColumn('task_phases', 'deleted_at')) {
                $fallbackTaskPhaseQuery->whereNull('deleted_at');
            }

            $taskPhases = $fallbackTaskPhaseQuery
                ->orderByRaw($phaseOrderSql)
                ->get();
        }

        $phaseIds = $taskPhases
            ->pluck('id')
            ->filter()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Load phase activities
        | IMPORTANT: no soft-deleted activities
        |--------------------------------------------------------------------------
        */
        $phaseActivities = collect();

        if ($phaseIds->isNotEmpty()) {
            $phaseActivitiesQuery = PhaseActivities::query()
                ->whereIn('phase_id', $phaseIds);

            if ($hasColumn('phase_activities', 'version')) {
                $phaseActivitiesQuery->where('version', $usedVersion);
            }

            if ($hasColumn('phase_activities', 'deleted_at')) {
                $phaseActivitiesQuery->whereNull('deleted_at');
            }

            $phaseActivities = $phaseActivitiesQuery
                ->orderByRaw($activityOrderSql)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Fallback: old activities without version
            |--------------------------------------------------------------------------
            */
            if ($phaseActivities->isEmpty()) {
                \Log::warning("⚠️ No phase activities found for version [{$usedVersion}], loading all non-deleted activities for phases.");

                $fallbackActivityQuery = PhaseActivities::query()
                    ->whereIn('phase_id', $phaseIds);

                if ($hasColumn('phase_activities', 'deleted_at')) {
                    $fallbackActivityQuery->whereNull('deleted_at');
                }

                $phaseActivities = $fallbackActivityQuery
                    ->orderByRaw($activityOrderSql)
                    ->get();
            }
        }

        \Log::info('🧠 Workflow data loaded', [
            'used_version' => $usedVersion,
            'stages' => $stages->count(),
            'task_phases' => $taskPhases->count(),
            'phase_activities' => $phaseActivities->count(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Build activity rows grouped by stage
        |--------------------------------------------------------------------------
        */
        $activityRows = collect();

        $currentActivityId = null;
        $currentPhaseId = null;
        $currentStageKey = null;

        foreach ($taskPhases as $phase) {
            $stageId = $phase->stage_id;
            $stageModel = $stagesById->get($stageId);
            $stageName = $stageModel?->stage ?? 'Unassigned';

            if (!array_key_exists($stageName, $groupedPhases)) {
                $groupedPhases[$stageName] = collect();
            }

            $activities = $phaseActivities->where('phase_id', $phase->id);

            if ($activities->isEmpty()) {
                continue;
            }

            foreach ($activities as $activity) {
                $historyKey = "{$phase->id}_{$activity->id}";
                $history = $historyList->get($historyKey);

                $doneReason = $history->done_reason ?? [];

                if (is_string($doneReason)) {
                    $decodedReason = json_decode($doneReason, true);
                    $doneReason = is_array($decodedReason) ? $decodedReason : [];
                }

                $notes = $history->notes ?? [];

                if (is_string($notes)) {
                    $decodedNotes = json_decode($notes, true);
                    $notes = is_array($decodedNotes) ? $decodedNotes : $notes;
                }

                $isDone = (int) ($history->is_done ?? 0) === 1;

                $row = (object) [
                    'phase' => $phase,
                    'activity' => $activity,

                    'is_done' => $history->is_done ?? null,
                    'done_by' => $history->done_by ?? null,
                    'marked_by' => $history->marked_by ?? null,
                    'done_date' => $history->done_date ?? null,
                    'has_document' => $history->has_document ?? null,
                    'notes' => $notes,
                    'done_reason' => $doneReason,
                    'plan_time' => $history->plan_time ?? null,
                    'is_time' => $history->is_time ?? null,
                    'd_time' => $history->d_time ?? null,

                    'product_id' => $productId,
                    'service_id' => $serviceId,
                    'service' => null,
                    'employee_id' => 0,
                    'department_id' => 0,

                    'stage_id' => $stageId,
                    'stage_name' => $stageName,
                    'version' => $usedVersion,
                ];

                /*
                |--------------------------------------------------------------------------
                | First unfinished activity becomes current.
                | No history = unfinished.
                |--------------------------------------------------------------------------
                */
                if (!$isDone && !$currentActivityId && !$currentPhaseId) {
                    $currentActivityId = $activity->id;
                    $currentPhaseId = $phase->id;
                    $currentStageKey = $stageName;
                }

                $activityRows->push($row);
                $groupedPhases[$stageName]->push($row);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback current activity from latest unfinished history
        |--------------------------------------------------------------------------
        */
        $latestUnfinished = $historyList
            ->filter(function ($row) {
                return (int) ($row->is_done ?? 0) !== 1;
            })
            ->sortByDesc('updated_at')
            ->first();

        if ($latestUnfinished && !$currentActivityId) {
            $currentActivityId = $latestUnfinished->activity_id;
            $currentPhaseId = $latestUnfinished->phase_id;

            $phaseForCurrent = $taskPhases->firstWhere('id', $currentPhaseId);
            $stageForCurrent = $phaseForCurrent
                ? $stagesById->get($phaseForCurrent->stage_id)
                : null;

            $currentStageKey = $stageForCurrent?->stage;
        }

        /*
        |--------------------------------------------------------------------------
        | Global progress
        |--------------------------------------------------------------------------
        */
        $totalActivities = $activityRows
            ->filter(fn($row) => !empty($row->activity))
            ->count();

        $doneActivities = $activityRows
            ->filter(fn($row) => (int) ($row->is_done ?? 0) === 1)
            ->count();

        $overallPercent = $totalActivities > 0
            ? round(($doneActivities / $totalActivities) * 100)
            : 0;

        \Log::info('📊 Workflow progress', [
            'done' => $doneActivities,
            'total' => $totalActivities,
            'percent' => $overallPercent,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Determine active stage tab
        |--------------------------------------------------------------------------
        */
        $targetStage = null;

        if ($requestedStageKey && array_key_exists($requestedStageKey, $groupedPhases)) {
            $targetStage = $requestedStageKey;
        } elseif ($currentStageKey && array_key_exists($currentStageKey, $groupedPhases)) {
            $targetStage = $currentStageKey;
        } else {
            $targetStage = array_key_first($groupedPhases);
        }

        $targetStageGroup = $groupedPhases[$targetStage] ?? collect();
        $firstItem = $targetStageGroup->first();

        if (!$firstItem) {
            \Log::warning('⚠️ No first item found for selected stage.', [
                'target_stage' => $targetStage,
                'used_version' => $usedVersion,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Phase history for diff/time summaries
        |--------------------------------------------------------------------------
        */
        $phaseActs = CustomerHistory::with('activity', 'phase')
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->where('section_id', $serviceId)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Cached time summaries
        |--------------------------------------------------------------------------
        */
        $tsQuery = TimeSummary::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->where('section_id', $serviceId);

        $timeSummaryTotal = (clone $tsQuery)
            ->where('scope', 'total')
            ->whereNull('phase_id')
            ->first();

        $timeSummariesPhase = (clone $tsQuery)
            ->where('scope', 'phase')
            ->get()
            ->keyBy('phase_id');

        /*
        |--------------------------------------------------------------------------
        | Render
        |--------------------------------------------------------------------------
        */
        return view('admin.new_leads.layouts.history_modal', [
            'allActivities' => $activityRows,
            'phaseActs' => $phaseActs,
            'groupedPhases' => $groupedPhases,
            'stage' => $targetStage,

            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'productId' => $productId,
            'serviceId' => $serviceId,

            'firstItem' => $firstItem,
            'phaseGroup' => $targetStageGroup,

            'usedVersion' => $usedVersion,
            'currentActivityId' => $currentActivityId,
            'currentPhaseId' => $currentPhaseId,
            'currentStageKey' => $currentStageKey,

            'customer' => $customer,
            'alternative' => $alternative,
            'productList' => $productList,
            'note' => $note,

            'overallPercent' => $overallPercent,
            'doneActivities' => $doneActivities,
            'totalActivities' => $totalActivities,

            'timeSummaryTotal' => $timeSummaryTotal,
            'timeSummariesPhase' => $timeSummariesPhase,
        ]);
    }


    public function showActivityDocument(string $filename)
    {
        $filename = basename($filename);
        $path = 'uploads/customers/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(storage_path('app/' . $path), [
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
    public function loadTaskView(Request $request)
    {
        $customerId = $request->customer_id;
        $alternativeId = $request->alternative_id;
        $productId = $request->product_id;
        $productListId = $request->product_list_id;

        // Load all matching tasks with correct relationships
        $tasks = \App\Models\PersonalTask::with([
            'taskKeys',
            'employees',
            'comments.author', // Include comments and their authors
            'comments.replies.author' // Include nested replies
        ])
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->get();

        $employees = DB::table('employees')
            ->select('id', 'name', 'lastname', 'image')
            ->where('status', 'Active')
            ->get();
        $employeeOptions = $employees->map(function ($emp) {
            return [
                'id' => $emp->id,
                'name' => $emp->name,
                'lastname' => $emp->lastname,
                'image' => asset('images/employee/' . $emp->image),
            ];
        });


        return view('admin.new_leads.layouts.task', compact(
            'tasks',
            'customerId',
            'alternativeId',
            'productId',
            'productListId',
            'employees',
            'employeeOptions'
        ));
    }

    public function nextStep(Request $request)
    {
        $phaseId = $request->get('phase_id');
        $currentActivityId = $request->get('activity_id');
        $productId = $request->get('product_id');

        if (!$phaseId || !$currentActivityId || !$productId) {
            return response()->json(['error' => 'Missing data'], 422);
        }

        $activities = PhaseActivities::where('phase_id', $phaseId)
            ->orderBy('sort_order')
            ->get();

        if ($activities->isEmpty()) {
            return response('<div class="text-muted">Keine Aktivitäten gefunden.</div>', 200);
        }

        $currentIndex = $activities->search(fn($a) => $a->id == $currentActivityId);

        return view('admin.new_leads.layouts.partials.activity_carousel', [
            'activities' => $activities,
            'currentIndex' => $currentIndex,
            'productId' => $productId
        ]);
    }

    public function nextSteps(Request $request)
    {
        $phaseId = $request->phase_id;
        $activityId = $request->activity_id;
        $productId = $request->product_id;

        $phase = Phase::with('activities')->findOrFail($phaseId);
        $activities = $phase->activities->sortBy('sort')->values();
        $currentIndex = $activities->search(fn($a) => $a->id == $activityId);

        return view('admin.new_leads.layouts.nextStep', compact('activities', 'currentIndex', 'productId'));
    }


    public function verifyUnlock(Request $request)
    {
        $password = $request->input('password');
        $requiredRole = $request->input('required_role');

        $user = auth()->user();

        if (!Hash::check($password, $user->password)) {
            return response()->json(['success' => false, 'message' => '❌ Passwort ist falsch.'], 401);
        }

        $hasRole = DB::table('user_rolls')
            ->where('user_id', $user->name) // user_id = auth()->user()->name
            ->where('item_id', $requiredRole)
            ->exists();

        if (!$hasRole) {
            return response()->json(['success' => false, 'message' => '⛔ Kein Zugriff: Rolle fehlt.']);
        }

        return response()->json(['success' => true]);
    }


    public function loadProductCard(Request $request)
    {
        return view('admin.new_leads.partials.product_card', $request->all());
    }


    public function loadStages(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['required', 'integer'],
            'product_id' => ['required', 'integer'],
            'section_id' => ['required', 'integer'],
            'version' => ['nullable', 'string'],
        ]);

        $customerId = (int) $data['customer_id'];
        $alternativeId = (int) $data['alternative_id'];
        $productId = (int) $data['product_id'];
        $sectionId = (int) $data['section_id'];
        $requestedVersion = isset($data['version'])
            ? trim((string) $data['version'])
            : null;

        /*
        |--------------------------------------------------------------------------
        | Saved customer stage/version
        |--------------------------------------------------------------------------
        */
        $saved = \App\Models\CustomerStage::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->where('section_id', $sectionId)
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Column guards
        |--------------------------------------------------------------------------
        */
        $hasStageDeleted = \Illuminate\Support\Facades\Schema::hasColumn('stages', 'deleted_at');
        $hasStageSection = \Illuminate\Support\Facades\Schema::hasColumn('stages', 'phase_section_id');
        $hasStageDefault = \Illuminate\Support\Facades\Schema::hasColumn('stages', 'default');
        $hasStageSort = \Illuminate\Support\Facades\Schema::hasColumn('stages', 'sort_order');

        $stageSortColumn = $hasStageSort ? 'sort_order' : 'id';

        /*
        |--------------------------------------------------------------------------
        | Default version
        |--------------------------------------------------------------------------
        */
        $defaultVersionQuery = \Illuminate\Support\Facades\DB::table('stages')
            ->where('product_id', $productId);

        if ($hasStageDeleted) {
            $defaultVersionQuery->whereNull('deleted_at');
        }

        if ($hasStageSection) {
            $defaultVersionQuery->where(function ($q) use ($sectionId) {
                $q->where('phase_section_id', $sectionId)
                    ->orWhereNull('phase_section_id');
            });
        }

        if ($hasStageDefault) {
            $defaultVersionQuery->where('default', 'yes');
        }

        $defaultVersion = $defaultVersionQuery->value('version');
        $defaultVersion = trim((string) ($defaultVersion ?: 'Standard'));

        /*
        |--------------------------------------------------------------------------
        | Load stages for product.
        | First try with section filter. If empty, fallback to product-only stages.
        |--------------------------------------------------------------------------
        */
        $stageQuery = \Illuminate\Support\Facades\DB::table('stages')
            ->where('product_id', $productId);

        if ($hasStageDeleted) {
            $stageQuery->whereNull('deleted_at');
        }

        if ($hasStageSection) {
            $stageQuery->where(function ($q) use ($sectionId) {
                $q->where('phase_section_id', $sectionId)
                    ->orWhereNull('phase_section_id');
            });
        }

        $stages = $stageQuery
            ->orderBy('version')
            ->orderByRaw("COALESCE({$stageSortColumn}, id)")
            ->get();

        if ($stages->isEmpty() && $hasStageSection) {
            $fallbackStageQuery = \Illuminate\Support\Facades\DB::table('stages')
                ->where('product_id', $productId);

            if ($hasStageDeleted) {
                $fallbackStageQuery->whereNull('deleted_at');
            }

            $stages = $fallbackStageQuery
                ->orderBy('version')
                ->orderByRaw("COALESCE({$stageSortColumn}, id)")
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Group versions safely.
        | NULL / empty version becomes "Standard".
        |--------------------------------------------------------------------------
        */
        $groupedStages = $stages->groupBy(function ($stage) {
            $version = trim((string) ($stage->version ?? ''));

            return $version !== '' ? $version : 'Standard';
        });

        /*
        |--------------------------------------------------------------------------
        | Decide which version should be shown.
        |--------------------------------------------------------------------------
        */
        $savedVersion = trim((string) ($saved->version ?? ''));
        $savedVersion = $savedVersion !== '' ? $savedVersion : null;

        $usedVersion = trim((string) (
            $requestedVersion
            ?: ($savedVersion ?: $defaultVersion)
        ));

        if (
            (!$usedVersion || !$groupedStages->has($usedVersion))
            && $groupedStages->isNotEmpty()
        ) {
            $usedVersion = (string) $groupedStages->keys()->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Saved rows count for this context.
        |--------------------------------------------------------------------------
        */
        $savedRowsCount = \App\Models\CustomerStage::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->where('section_id', $sectionId)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Debug info for Blade if needed.
        |--------------------------------------------------------------------------
        */
        $availableVersions = $groupedStages->keys()->values();

        return view('admin.new_leads.layouts.stage', [
            'groupedStages' => $groupedStages,
            'availableVersions' => $availableVersions,

            'saved' => $saved,
            'savedVersion' => $savedVersion,
            'usedVersion' => $usedVersion,
            'defaultVersion' => $defaultVersion,
            'savedRowsCount' => $savedRowsCount,

            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product_id' => $productId,
            'section_id' => $sectionId,
        ]);
    }
    public function saveCustomerStage(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['required', 'integer'],
            'product_id' => ['required', 'integer'],
            'section_id' => ['required', 'integer'],
            'version' => ['required', 'string'],
            'status' => ['nullable', 'string'],
        ]);

        $data['customer_id'] = (int) $data['customer_id'];
        $data['alternative_id'] = (int) $data['alternative_id'];
        $data['product_id'] = (int) $data['product_id'];
        $data['section_id'] = (int) $data['section_id'];
        $data['version'] = trim((string) $data['version']);

        $status = $data['status'] ?? 'active';

        $hasStageDeleted = Schema::hasColumn('stages', 'deleted_at');
        $hasStageSection = Schema::hasColumn('stages', 'phase_section_id');

        $hasPhaseDeleted = Schema::hasColumn('task_phases', 'deleted_at');
        $hasPhaseSection = Schema::hasColumn('task_phases', 'section_id');

        $hasActDeleted = Schema::hasColumn('phase_activities', 'deleted_at');

        $stageSort = Schema::hasColumn('stages', 'sort_order') ? 'sort_order' : 'id';

        $phaseSort = Schema::hasColumn('task_phases', 'order')
            ? 'order'
            : (Schema::hasColumn('task_phases', 'sort_order') ? 'sort_order' : 'id');

        $actSort = Schema::hasColumn('phase_activities', 'sort_order')
            ? 'sort_order'
            : 'id';

        return DB::transaction(function () use ($data, $status, $hasStageDeleted, $hasStageSection, $hasPhaseDeleted, $hasPhaseSection, $hasActDeleted, $stageSort, $phaseSort, $actSort) {
            /*
            |--------------------------------------------------------------------------
            | Load stages safely
            |--------------------------------------------------------------------------
            */
            $stageQ = DB::table('stages')
                ->where('product_id', $data['product_id'])
                ->orderBy($stageSort);

            if ($hasStageDeleted) {
                $stageQ->whereNull('deleted_at');
            }

            /*
            |--------------------------------------------------------------------------
            | Version handling
            | Important:
            | Blade may send "Standard" for NULL/empty versions.
            |--------------------------------------------------------------------------
            */
            if ($data['version'] === 'Standard' || $data['version'] === '') {
                $stageQ->where(function ($q) {
                    $q->whereNull('version')
                        ->orWhere('version', '')
                        ->orWhere('version', 'Standard');
                });
            } else {
                $stageQ->where(function ($q) use ($data) {
                    $q->where('version', $data['version'])
                        ->orWhereRaw('TRIM(version) = ?', [$data['version']]);
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Section filter if your stages are section-based
            |--------------------------------------------------------------------------
            */
            if ($hasStageSection) {
                $stageQ->where(function ($q) use ($data) {
                    $q->where('phase_section_id', $data['section_id'])
                        ->orWhereNull('phase_section_id');
                });
            }

            $stages = $stageQ->get();

            /*
            |--------------------------------------------------------------------------
            | Fallback:
            | If no stages found with section filter, try product + version only.
            |--------------------------------------------------------------------------
            */
            if ($stages->isEmpty() && $hasStageSection) {
                $fallbackStageQ = DB::table('stages')
                    ->where('product_id', $data['product_id'])
                    ->orderBy($stageSort);

                if ($hasStageDeleted) {
                    $fallbackStageQ->whereNull('deleted_at');
                }

                if ($data['version'] === 'Standard' || $data['version'] === '') {
                    $fallbackStageQ->where(function ($q) {
                        $q->whereNull('version')
                            ->orWhere('version', '')
                            ->orWhere('version', 'Standard');
                    });
                } else {
                    $fallbackStageQ->where(function ($q) use ($data) {
                        $q->where('version', $data['version'])
                            ->orWhereRaw('TRIM(version) = ?', [$data['version']]);
                    });
                }

                $stages = $fallbackStageQ->get();
            }

            if ($stages->isEmpty()) {
                $availableVersions = DB::table('stages')
                    ->where('product_id', $data['product_id'])
                    ->when($hasStageDeleted, function ($q) {
                        $q->whereNull('deleted_at');
                    })
                    ->select('version')
                    ->distinct()
                    ->pluck('version')
                    ->map(fn($v) => $v === null || $v === '' ? 'Standard' : $v)
                    ->values()
                    ->all();

                return response()->json([
                    'success' => false,
                    'message' => 'Keine Stufen für diese Version gefunden.',
                    'debug' => [
                        'product_id' => $data['product_id'],
                        'section_id' => $data['section_id'],
                        'requested_version' => $data['version'],
                        'available_versions_for_product' => $availableVersions,
                    ],
                ], 422);
            }

            /*
            |--------------------------------------------------------------------------
            | Delete old rows only for this exact customer/object/product/section
            |--------------------------------------------------------------------------
            */
            \App\Models\CustomerStage::query()
                ->where('customer_id', $data['customer_id'])
                ->where('alternative_id', $data['alternative_id'])
                ->where('product_id', $data['product_id'])
                ->where('section_id', $data['section_id'])
                ->delete();

            $now = now();
            $rows = [];

            foreach ($stages as $stage) {
                /*
                |--------------------------------------------------------------------------
                | Load phases
                |--------------------------------------------------------------------------
                */
                $phaseQ = DB::table('task_phases')
                    ->where('stage_id', $stage->id)
                    ->where('product_id', $data['product_id'])
                    ->orderBy($phaseSort);

                if ($hasPhaseSection) {
                    $phaseQ->where('section_id', $data['section_id']);
                }

                if (Schema::hasColumn('task_phases', 'version')) {
                    if ($data['version'] === 'Standard' || $data['version'] === '') {
                        $phaseQ->where(function ($q) {
                            $q->whereNull('version')
                                ->orWhere('version', '')
                                ->orWhere('version', 'Standard');
                        });
                    } else {
                        $phaseQ->where(function ($q) use ($data) {
                            $q->where('version', $data['version'])
                                ->orWhereRaw('TRIM(version) = ?', [$data['version']]);
                        });
                    }
                }

                if ($hasPhaseDeleted) {
                    $phaseQ->whereNull('deleted_at');
                }

                $phases = $phaseQ->get();

                /*
                |--------------------------------------------------------------------------
                | Save stage even if there are no phases
                |--------------------------------------------------------------------------
                */
                if ($phases->isEmpty()) {
                    $rows[] = [
                        'customer_id' => $data['customer_id'],
                        'alternative_id' => $data['alternative_id'],
                        'product_id' => $data['product_id'],
                        'section_id' => $data['section_id'],
                        'stage_id' => $stage->id,
                        'phase_id' => null,
                        'task_id' => null,
                        'version' => (string) ($stage->version ?: $data['version']),
                        'status' => $status,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    continue;
                }

                foreach ($phases as $phase) {
                    $actQ = DB::table('phase_activities')
                        ->where('phase_id', $phase->id)
                        ->orderBy($actSort);

                    if ($hasActDeleted) {
                        $actQ->whereNull('deleted_at');
                    }

                    $firstActivity = $actQ->first();

                    $rows[] = [
                        'customer_id' => $data['customer_id'],
                        'alternative_id' => $data['alternative_id'],
                        'product_id' => $data['product_id'],
                        'section_id' => $data['section_id'],
                        'stage_id' => $stage->id,
                        'phase_id' => $phase->id,
                        'task_id' => $firstActivity->id ?? null,
                        'version' => (string) ($stage->version ?: $data['version']),
                        'status' => $status,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($rows)) {
                \App\Models\CustomerStage::insert($rows);
            }

            return response()->json([
                'success' => true,
                'message' => 'Arbeitsprozess wurde gespeichert.',
                'saved_rows' => count($rows),
                'version_set' => $data['version'],
            ]);
        });
    }
    public function loadVersionStages(Request $request)
    {
        $version = $request->version;
        $product_id = $request->product_id;

        $stages = \App\Models\Stage::where('product_id', $product_id)
            ->where('version', $version)
            ->orderBy('sort_order')
            ->get();

        return view('admin.new_leads.layouts.partial_stage_list', compact('stages'));
    }


    public function calendarView(Request $request)
    {
        $cid = (string) $request->input('cid', '');
        $aid = (string) $request->input('aid', '');
        $pid = (string) $request->input('pid', '');

        Log::info('calendarView called', [
            'cid' => $cid,
            'aid' => $aid,
            'pid' => $pid,
            'query' => $request->query(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Product / Article Group Name
        |--------------------------------------------------------------------------
        */

        $articleGroup = DB::table('article_groups')
            ->where('id', $pid)
            ->value('article_group') ?? 'General';

        /*
        |--------------------------------------------------------------------------
        | Employees for Create Appointment Modal
        |--------------------------------------------------------------------------
        */

        $calenderEmployees = DB::table('employees')
            ->select('id as emp_id', 'name', 'lastname', 'image', 'gender', 'color')
            ->where('status', 'Active')
            ->orderBy('name')
            ->orderBy('lastname')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Load Appointments with All Needed Relations
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | Use Eloquent here, not DB::table(), because the Blade needs:
        | - $appointment->employees
        | - $appointment->reports
        | - $report->employee / author / reporter
        |--------------------------------------------------------------------------
        */

        $rawAppointments = MainAppointment::query()
            ->with([
                'employees' => function ($query) {
                    $query->select(
                        'employees.id',
                        'employees.name',
                        'employees.lastname',
                        'employees.image',
                        'employees.gender',
                        'employees.color'
                    );
                },

                'createdBy:id,name,lastname,image,color',
                'changedBy:id,name,lastname,image,color',
                'reporter:id,name,lastname,image,color',

                'appointmentEmployees' => function ($query) {
                    $query->with([
                        'employee:id,name,lastname,image,color',
                    ]);
                },

                'reports' => function ($query) {
                    $query
                        ->with([
                            'employee:id,name,lastname,image,color',
                            'author:id,name,lastname,image,color',
                            'reporter:id,name,lastname,image,color',
                        ])
                        ->latest('report_date')
                        ->latest('created_at')
                        ->latest('id');
                },

                'problem:id,problem',
                'problemTask:id,title',
            ])
            ->where('customer_id', $cid)
            ->orderByDesc('id')
            ->get();

        Log::info('calendarView rawAppointments loaded', [
            'customer_id' => $cid,
            'count' => $rawAppointments->count(),
            'sample' => $rawAppointments->take(10)->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'customer_id' => (string) $appointment->customer_id,
                    'start_date' => $appointment->start_date
                        ? Carbon::parse($appointment->start_date)->format('Y-m-d')
                        : null,
                    'products' => $appointment->products,
                    'employees_count' => $appointment->employees?->count() ?? 0,
                    'reports_count' => $appointment->reports?->count() ?? 0,
                ];
            })->toArray(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Decode Products Helper
        |--------------------------------------------------------------------------
        | Supports:
        | 1. Normal JSON string:
        |    {"Photovoltaik":[6505,12,"161"]}
        |
        | 2. Double encoded JSON:
        |    "{\"Photovoltaik\":[6505,12,\"161\"]}"
        |
        | 3. Already array-casted products
        |--------------------------------------------------------------------------
        */

        $decodeProducts = function ($raw): ?array {
            if ($raw === null || $raw === '') {
                return null;
            }

            if (is_array($raw)) {
                return $raw;
            }

            if (!is_string($raw)) {
                return null;
            }

            $decoded = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            if (is_string($decoded)) {
                $decodedAgain = json_decode($decoded, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedAgain)) {
                    return $decodedAgain;
                }

                return null;
            }

            return is_array($decoded) ? $decoded : null;
        };

        /*
        |--------------------------------------------------------------------------
        | Product Matching Helper
        |--------------------------------------------------------------------------
        | Supports different old/new formats:
        |
        | Numeric:
        | [CID, PID, AID]
        | [AID, PID, CID]
        |
        | Associative:
        | {"cid":6505,"pid":12,"aid":"161"}
        | {"customer_id":6505,"product_id":12,"alternative_id":"161"}
        |
        | UID fallback:
        | {"uid":"PHOTOVOLTAIK_161","product_id":12}
        |--------------------------------------------------------------------------
        */

        $matchesProductRow = function ($row) use ($cid, $pid, $aid): bool {
            if (!is_array($row) || empty($row)) {
                return false;
            }

            if (array_is_list($row)) {
                if (count($row) < 3) {
                    return false;
                }

                $r0 = (string) ($row[0] ?? '');
                $r1 = (string) ($row[1] ?? '');
                $r2 = (string) ($row[2] ?? '');

                $formatCustomerProductAlternative = $r0 === $cid && $r1 === $pid && $r2 === $aid;
                $formatAlternativeProductCustomer = $r0 === $aid && $r1 === $pid && $r2 === $cid;

                return $formatCustomerProductAlternative || $formatAlternativeProductCustomer;
            }

            $rowCid = (string) (
                $row['cid']
                ?? $row['customer_id']
                ?? $row['customerId']
                ?? ''
            );

            $rowPid = (string) (
                $row['pid']
                ?? $row['product_id']
                ?? $row['productId']
                ?? ''
            );

            $rowAid = (string) (
                $row['aid']
                ?? $row['alternative_id']
                ?? $row['alternativeId']
                ?? ''
            );

            if ($rowAid === '' && !empty($row['uid'])) {
                $uidParts = explode('_', (string) $row['uid']);
                $rowAid = (string) end($uidParts);
            }

            $pidMatches = $rowPid === $pid;
            $aidMatches = $rowAid === $aid;

            /*
             | Since the main query already filters customer_id = $cid,
             | we allow missing customer_id inside products JSON.
             */
            $cidMatches = $rowCid === '' || $rowCid === $cid;

            return $pidMatches && $aidMatches && $cidMatches;
        };

        /*
        |--------------------------------------------------------------------------
        | Filter Appointments by Selected Product Context
        |--------------------------------------------------------------------------
        */

        $appointments = $rawAppointments
            ->filter(function ($appointment) use ($decodeProducts, $matchesProductRow, $cid, $pid, $aid) {
                if (empty($appointment->products)) {
                    return false;
                }

                $products = $decodeProducts($appointment->products);

                if (!is_array($products) || empty($products)) {
                    Log::debug('calendarView products could not be decoded', [
                        'appointment_id' => $appointment->id,
                        'products_raw' => $appointment->products,
                    ]);

                    return false;
                }

                /*
                 | Object-map format:
                 | {"Photovoltaik":[6505,12,"161"]}
                 */
                foreach ($products as $value) {
                    if ($matchesProductRow($value)) {
                        return true;
                    }
                }

                /*
                 | List format:
                 | [[6505,12,"161"], {...}]
                 */
                if (array_is_list($products)) {
                    foreach ($products as $row) {
                        if ($matchesProductRow($row)) {
                            return true;
                        }
                    }
                }

                Log::debug('calendarView no product match for appointment', [
                    'appointment_id' => $appointment->id,
                    'cid' => $cid,
                    'pid' => $pid,
                    'aid' => $aid,
                    'decoded_products' => $products,
                ]);

                return false;
            })
            ->values();

        Log::info('calendarView filtered appointments', [
            'cid' => $cid,
            'pid' => $pid,
            'aid' => $aid,
            'filtered_count' => $appointments->count(),
            'filtered_ids' => $appointments->pluck('id')->take(50)->toArray(),
            'reports_total' => $appointments->sum(function ($appointment) {
                return $appointment->reports?->count() ?? 0;
            }),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Return Calendar Partial
        |--------------------------------------------------------------------------
        */

        return view('admin.new_leads.layouts.calendar', [
            'appointments' => $appointments,
            'calenderEmployees' => $calenderEmployees,
            'cid' => (int) $cid,
            'aid' => (int) $aid,
            'pid' => (int) $pid,
            'product_name' => $articleGroup,
        ]);
    }
    public function bulkStore(Request $request)
    {
        $rows = $request->input('rows', []);

        foreach ($rows as $row) {
            $phaseSection = optional(PhaseSection::find($row['service_id'] ?? null))->phase_section;

            LeadProductList::create([
                'customer_id' => $row['customer_id'],
                'alternative_id' => $row['alternative_id'],
                'product_id' => $row['product_id'],
                'service_id' => $row['service_id'],
                'department_id' => $row['department_id'],
                'employee_id' => $row['employee_id'],
                'interest' => $row['interest'],
                'realization_time' => $row['realization_time'],
                'service' => $phaseSection, // dynamically resolved
                'status' => 'Lead',
                'stage' => 'lead',
            ]);
        }

        return response()->json(['success' => true]);
    }


    public function productDelete($id)
    {
        $product = LeadProductList::find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produkt nicht gefunden.'], 404);
        }

        $product->delete();

        return response()->json(['success' => true, 'message' => 'Produkt erfolgreich gelöscht.']);
    }



    public function purchaseSummary(NewLeads $customer) // route-model binding
    {
        // group counts by status for this customer’s products
        $byStatus = $customer->leadProductLists()
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->orderBy('status')
            ->pluck('cnt', 'status'); // e.g. ['open' => 3, 'won' => 2]

        $totalProducts = $byStatus->sum();

        return view('partials.customer_purchase_summary', [
            'customer' => $customer,
            'byStatus' => $byStatus,
            'totalProducts' => $totalProducts,
        ]);
    }

    public function updateTotalPurchase(Request $request, NewLeads $customer)
    {
        $request->validate([
            'total_purchase' => ['required', 'string', 'max:50'],
        ]);

        // Normalize: strip currency/space, remove thousand sep, normalize decimal
        $raw = trim($request->input('total_purchase', ''));
        $norm = preg_replace('/[^\d.,-]/', '', $raw); // keep digits . , -
        $norm = str_replace('.', '', $norm);          // remove thousand dots
        $norm = str_replace(',', '.', $norm);         // comma -> dot

        if (!is_numeric($norm)) {
            return response()->json([
                'ok' => false,
                'message' => 'Ungültiger Betrag.',
            ], 422);
        }

        $amount = round((float) $norm, 2);
        if ($amount < 0) {
            return response()->json([
                'ok' => false,
                'message' => 'Betrag darf nicht negativ sein.',
            ], 422);
        }

        $customer->total_purchase = $amount;
        $customer->purchase_status = 'customer';
        $customer->save();

        return response()->json([
            'ok' => true,
            'amount' => $amount,
            'formatted' => number_format($amount, 2, ',', '.'),
        ]);
    }

    public function neighbor(Request $request)
    {
        $leadId = (int) $request->get('customer_id');
        $altId = $request->get('alternative_id');
        $radius = (float) $request->get('radius', 10);
        $status = trim((string) $request->get('status', ''));
        $productId = $request->get('product_id');

        $lead = NewLeads::findOrFail($leadId);

        $baseAlternative = null;

        if ($altId) {
            $baseAlternative = LeadAlternativeAdd::where('lead_id', $leadId)
                ->where('id', $altId)
                ->first();
        }

        if (!$baseAlternative) {
            $baseAlternative = LeadAlternativeAdd::where('lead_id', $leadId)
                ->orderByDesc('main')
                ->orderBy('id')
                ->first();
        }

        if ($baseAlternative && $baseAlternative->lat && $baseAlternative->lon) {
            $baseLat = (float) $baseAlternative->lat;
            $baseLng = (float) $baseAlternative->lon;
        } else {
            $baseLat = (float) $lead->latitude;
            $baseLng = (float) $lead->longitude;
        }

        if (!$baseLat || !$baseLng) {
            return view('admin.new_leads.layouts.neighbor', [
                'lead' => $lead,
                'baseAlternative' => $baseAlternative,
                'neighbors' => collect(),
                'neighborsForJs' => [],
                'radius' => $radius,
                'baseLat' => null,
                'baseLng' => null,
                'hasCoords' => false,
                'productOptions' => [],
                'totals' => [
                    'customers' => 0,
                    'offers' => 0,
                    'deals' => 0,
                    'projects' => 0,
                    'tickets' => 0,
                    'products' => 0,
                ],
                'selectedStatus' => $status,
                'selectedProduct' => $productId,
            ]);
        }

        [$neighbors, $neighborsForJs, $totals] = $this->findNeighbors(
            $baseLat,
            $baseLng,
            $radius,
            $baseAlternative?->id,
            $status,
            $productId
        );

        $productOptions = $this->getAllReferenceProductOptions();

        return view('admin.new_leads.layouts.neighbor', [
            'lead' => $lead,
            'baseAlternative' => $baseAlternative,
            'neighbors' => $neighbors,
            'neighborsForJs' => $neighborsForJs,
            'radius' => $radius,
            'baseLat' => $baseLat,
            'baseLng' => $baseLng,
            'hasCoords' => true,
            'productOptions' => $productOptions,
            'totals' => $totals,
            'selectedStatus' => $status,
            'selectedProduct' => $productId,
        ]);
    }

    public function neighborData(Request $request)
    {
        $leadId = (int) $request->get('customer_id');
        $altId = $request->get('alternative_id');
        $radius = (float) $request->get('radius', 10);
        $status = trim((string) $request->get('status', ''));
        $productId = $request->get('product_id');

        $lead = NewLeads::findOrFail($leadId);

        $baseAlternative = null;

        if ($altId) {
            $baseAlternative = LeadAlternativeAdd::where('lead_id', $leadId)
                ->where('id', $altId)
                ->first();
        }

        if (!$baseAlternative) {
            $baseAlternative = LeadAlternativeAdd::where('lead_id', $leadId)
                ->orderByDesc('main')
                ->orderBy('id')
                ->first();
        }

        if ($baseAlternative && $baseAlternative->lat && $baseAlternative->lon) {
            $baseLat = (float) $baseAlternative->lat;
            $baseLng = (float) $baseAlternative->lon;
        } else {
            $baseLat = (float) $lead->latitude;
            $baseLng = (float) $lead->longitude;
        }

        if (!$baseLat || !$baseLng) {
            return response()->json([
                'neighbors' => [],
                'html_list' => '<div class="text-center p-4 text-muted">Keine Koordinaten vorhanden.</div>',
                'totals' => [
                    'customers' => 0,
                    'offers' => 0,
                    'deals' => 0,
                    'projects' => 0,
                    'tickets' => 0,
                    'products' => 0,
                ],
                'product_options' => [],
            ]);
        }

        [$neighbors, $neighborsForJs, $totals] = $this->findNeighbors(
            $baseLat,
            $baseLng,
            $radius,
            $baseAlternative?->id,
            $status,
            $productId
        );

        $productOptions = $this->getAllReferenceProductOptions();
        $htmlList = view('admin.new_leads.layouts.neighbor_list', [
            'neighbors' => $neighbors,
        ])->render();

        return response()->json([
            'neighbors' => $neighborsForJs,
            'html_list' => $htmlList,
            'totals' => $totals,
            'product_options' => $productOptions,
        ]);
    }

    protected function getAllReferenceProductOptions(): array
    {
        return \App\Models\ArticleGroup::query()
            ->select('id', 'article_group')
            ->whereNull('deleted_at') // only if article_groups has soft deletes; remove if not
            ->whereNotNull('article_group')
            ->orderBy('article_group')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => (int) $product->id,
                    'name' => $product->article_group,
                ];
            })
            ->values()
            ->all();
    }

    protected function findNeighbors(
        float $baseLat,
        float $baseLng,
        float $radiusKm,
        ?int $currentAltId = null,
        ?string $statusFilter = null,
        $productIdFilter = null
    ): array {
        $statusFilter = strtolower(trim((string) $statusFilter));
        $productIdFilter = $productIdFilter !== null && $productIdFilter !== '' ? (int) $productIdFilter : null;

        $haversine = "
            (
                6371 * acos(
                    cos(radians(?)) *
                    cos(radians(lead_alternative_adds.lat)) *
                    cos(radians(lead_alternative_adds.lon) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(lead_alternative_adds.lat))
                )
            )
        ";

        $baseQuery = LeadAlternativeAdd::query()
            ->select([
                'lead_alternative_adds.*',
                'new_leads.firma',
                'new_leads.lastname',
                'new_leads.name',
                'new_leads.city as lead_city',
                'new_leads.id as customer_id',
            ])
            ->selectRaw("$haversine as distance_km", [$baseLat, $baseLng, $baseLat])
            ->join('new_leads', 'new_leads.id', '=', 'lead_alternative_adds.lead_id')
            ->whereNotNull('lead_alternative_adds.lat')
            ->whereNotNull('lead_alternative_adds.lon')
            ->whereNull('lead_alternative_adds.deleted_at')
            ->whereNull('new_leads.deleted_at');

        $rawItems = DB::query()
            ->fromSub($baseQuery, 'neighbor_rows')
            ->where('distance_km', '<=', $radiusKm)
            ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$currentAltId ?: 0])
            ->orderBy('distance_km')
            ->get();

        $alternativeIds = $rawItems->pluck('id')->values()->all();

        $alternatives = LeadAlternativeAdd::query()
            ->with([
                'productLists' => function ($q) {
                    $q->whereNull('deleted_at')
                        ->with([
                            'product:id,article_group',
                            'department:id,department_name',
                            'employee:id,name,lastname',
                            'fieldEmployee:id,name,lastname',
                        ]);
                },
                'customerProductInfos' => function ($q) {
                    $q->with([
                        'product:id,article_group',
                        'department:id,department_name',
                    ]);
                },
            ])
            ->whereIn('id', $alternativeIds)
            ->get()
            ->keyBy('id');

        $neighbors = collect($rawItems)->map(function ($row) use ($alternatives, $statusFilter, $productIdFilter, $currentAltId) {
            $model = $alternatives->get($row->id);
            if (!$model) {
                return null;
            }

            $model->firma = $row->firma;
            $model->lastname = $row->lastname;
            $model->name = $row->name;
            $model->lead_city = $row->lead_city;
            $model->customer_id = $row->customer_id;
            $model->distance_km = round((float) $row->distance_km, 2);

            $displayName = $row->firma
                ?: trim(($row->lastname ?? '') . ' ' . ($row->name ?? ''))
                ?: 'Lead #' . $row->lead_id;

            $productListRows = collect($model->productLists ?? [])->map(function ($pl) {
                $stage = strtolower((string) ($pl->stage ?: $pl->status ?: ''));

                $stageKey = match ($stage) {
                    'offer', 'angebot' => 'offer',
                    'deal', 'auftrag' => 'deal',
                    'project', 'projekt' => 'project',
                    'archive', 'Archiv' => 'archiv',
                    default => 'other',
                };

                $stageLabel = match ($stageKey) {
                    'offer' => 'Angebot',
                    'deal' => 'Auftrag',
                    'project' => 'Projekt',
                    'archive' => 'Archiv',
                    default => 'Leads',
                };

                $responsible = null;
                if ($pl->fieldEmployee) {
                    $responsible = trim(($pl->fieldEmployee->name ?? '') . ' ' . ($pl->fieldEmployee->lastname ?? ''));
                } elseif ($pl->employee) {
                    $responsible = trim(($pl->employee->name ?? '') . ' ' . ($pl->employee->lastname ?? ''));
                }

                return [
                    'source' => 'lead_product_list',
                    'lead_product_list_id' => $pl->id,
                    'customer_product_info_id' => null,
                    'product_id' => (int) $pl->product_id,
                    'product_name' => optional($pl->product)->article_group ?: ('Produkt #' . $pl->product_id),
                    'stage_key' => $stageKey,
                    'stage_label' => $stageLabel,
                    'department_name' => optional($pl->department)->department_name,
                    'responsible_person' => $responsible,
                    'manufacturer' => null,
                    'serial_number' => null,
                    'purchase_date' => null,
                    'installation_date' => null,
                    'notes' => null,
                    'product_count' => null,
                    'installation_location' => null,
                    'invoice_reference' => null,
                    'warranty_until' => null,
                    'guarantee_until' => null,
                    'purchased_from_us' => null,
                ];
            });

            $productInfoRows = collect($model->customerProductInfos ?? [])->map(function ($cpi) {
                $productName = $cpi->product_name
                    ?: optional($cpi->product)->article_group
                    ?: ('Produkt #' . $cpi->product_id);

                return [
                    'source' => 'customer_product_info',
                    'lead_product_list_id' => null,
                    'customer_product_info_id' => $cpi->id,
                    'product_id' => (int) $cpi->product_id,
                    'product_name' => $productName,
                    'stage_key' => 'completed',
                    'stage_label' => 'Installiert',
                    'department_name' => optional($cpi->department)->department_name,
                    'responsible_person' => optional($cpi->employee)->name
                        ? trim(($cpi->employee->name ?? '') . ' ' . ($cpi->employee->lastname ?? ''))
                        : null,
                    'manufacturer' => $cpi->manufacturer,
                    'serial_number' => $cpi->serial_number,
                    'purchase_date' => $cpi->purchase_date ? \Carbon\Carbon::parse($cpi->purchase_date)->format('d.m.Y') : null,
                    'installation_date' => $cpi->installation_date ? \Carbon\Carbon::parse($cpi->installation_date)->format('d.m.Y') : null,
                    'notes' => $cpi->notes,
                    'product_count' => $cpi->product_count,
                    'installation_location' => $cpi->installation_location,
                    'invoice_reference' => $cpi->invoice_reference,
                    'warranty_until' => $cpi->warranty_until ? \Carbon\Carbon::parse($cpi->warranty_until)->format('d.m.Y') : null,
                    'guarantee_until' => $cpi->guarantee_until ? \Carbon\Carbon::parse($cpi->guarantee_until)->format('d.m.Y') : null,
                    'purchased_from_us' => $cpi->purchased_from_us,
                ];
            });

            $rows = $productListRows
                ->concat($productInfoRows)
                ->values();

            if ($statusFilter !== '') {
                $rows = $rows->filter(fn($row) => $row['stage_key'] === $statusFilter);
            }

            if ($productIdFilter) {
                $rows = $rows->filter(fn($row) => (int) $row['product_id'] === $productIdFilter);
            }

            $rows = $rows->values();

            $model->display_name = $displayName;
            $model->is_current = (int) $model->id === (int) $currentAltId;
            $model->product_rows = $rows;
            $cpiRows = $rows->where('source', 'customer_product_info')->values();

            $model->cpi_product_names = $cpiRows->pluck('product_name')->filter()->values();
            $model->cpi_manufacturers = $cpiRows->pluck('manufacturer')->filter()->values();
            $model->cpi_serial_numbers = $cpiRows->pluck('serial_number')->filter()->values();
            $model->cpi_purchase_dates = $cpiRows->pluck('purchase_date')->filter()->values();
            $model->cpi_installation_dates = $cpiRows->pluck('installation_date')->filter()->values();
            $model->cpi_notes = $cpiRows->pluck('notes')->filter()->values();
            $model->cpi_product_counts = $cpiRows->pluck('product_count')->filter(function ($v) {
                return $v !== null && $v !== '';
            })->values();
            $model->cpi_installation_locations = $cpiRows->pluck('installation_location')->filter()->values();
            $model->cpi_invoice_references = $cpiRows->pluck('invoice_reference')->filter()->values();
            $model->products_summary = $rows
                ->map(fn($r) => $r['product_name'] . ' (' . $r['stage_label'] . ')')
                ->implode(', ');
            $model->products_count = $rows->count();

            return $model;
        })->filter()->values();

        $neighbors = $neighbors
            ->filter(fn($n) => $n->product_rows->count() > 0)
            ->values();

        $neighborsForJs = $neighbors->map(function ($n) {
            return [
                'id' => $n->id,
                'customer_id' => $n->customer_id,
                'lead_id' => $n->lead_id,
                'customer_name' => $n->name,
                'customer_lastname' => $n->lastname,
                'display_name' => $n->display_name,
                'lat' => (float) $n->lat,
                'lon' => (float) $n->lon,
                'lng' => (float) $n->lon,
                'distance_km' => round((float) $n->distance_km, 2),
                'street' => $n->street,
                'postcode' => $n->postcode,
                'city' => $n->city ?? $n->lead_city,
                'full_address' => $n->full_address ?: trim(($n->street ?? '') . ' ' . ($n->postcode ?? '') . ' ' . ($n->city ?? $n->lead_city ?? '')),
                'object_name' => $n->object_name,
                'is_current' => $n->is_current,
                'products' => $n->product_rows->values()->all(),
                'product_statuses' => $n->products_summary,
                'cpi_product_names' => $n->cpi_product_names ?? [],
                'cpi_manufacturers' => $n->cpi_manufacturers ?? [],
                'cpi_serial_numbers' => $n->cpi_serial_numbers ?? [],
                'cpi_purchase_dates' => $n->cpi_purchase_dates ?? [],
                'cpi_installation_dates' => $n->cpi_installation_dates ?? [],
                'cpi_notes' => $n->cpi_notes ?? [],
                'cpi_product_counts' => $n->cpi_product_counts ?? [],
                'cpi_installation_locations' => $n->cpi_installation_locations ?? [],
                'invoice_references' => $n->cpi_invoice_references ?? [],
            ];
        })->values()->all();

        $allProductRows = $neighbors->flatMap(fn($n) => $n->product_rows);

        $totals = [
            'customers' => $neighbors->count(),
            'offers' => $allProductRows->where('stage_key', 'offer')->count(),
            'deals' => $allProductRows->where('stage_key', 'deal')->count(),
            'projects' => $allProductRows->where('stage_key', 'project')->count(),
            'tickets' => 0,
            'products' => $allProductRows->count(),
        ];

        return [$neighbors, $neighborsForJs, $totals];
    }

    public function ajaxLoadBasic($id)
    {
        $lead = NewLeads::with(['contactPerson', 'branchRelation'])->findOrFail($id);

        $employees = Employee::query()
            ->select('id', 'title', 'name', 'lastname')
            ->orderBy('lastname')
            ->orderBy('name')
            ->get();

        $branches = Branch::query()
            ->select('id', 'branch', 'city')
            ->orderBy('branch')
            ->get();

        return response()->json([
            'customer' => $lead,
            'employees' => $employees,
            'branches' => $branches,
        ]);
    }


    public function ajaxUpdateBasic(Request $request, $id)
    {
        $lead = NewLeads::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'academic_title' => 'nullable|string|max:255',

            'name' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'firma' => 'nullable|string|max:255',

            'street' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',

            'phone' => 'nullable|string|max:50',
            'telephone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',

            'source' => 'nullable|string|max:255',

            // FIX: branch was not validated/saved before
            'branch' => 'nullable|integer|exists:branches,id',
        ]);

        $lead->fill($validated);

        // full_address: combine street, postcode, city, lat, lng
        $parts = [];

        if (!empty($validated['street'])) {
            $parts[] = $validated['street'];
        }

        if (!empty($validated['postcode']) || !empty($validated['city'])) {
            $parts[] = trim(($validated['postcode'] ?? '') . ' ' . ($validated['city'] ?? ''));
        }

        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            $parts[] = 'Lat: ' . $validated['latitude'] . ' / Lng: ' . $validated['longitude'];
        }

        $lead->full_address = implode(' | ', $parts);

        $lead->save();

        return response()->json([
            'status' => 'ok',
            'customer' => $lead,
            'full_address' => $lead->full_address,
        ]);
    }


    public function updateProductPrice(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer|exists:lead_product_lists,id',
            'price' => 'required|numeric|min:0',
        ]);

        $leadProduct = LeadProductList::findOrFail($data['id']);

        $oldPrice = (float) ($leadProduct->price ?? 0);
        $newPrice = (float) $data['price'];

        $now = now();
        $userId = auth()->user()->name ?? 0;

        /*
         * --- STAGE HISTORY (weiterhin) ---
         */
        $stageHistoryRaw = $leadProduct->stage_history;

        if (is_array($stageHistoryRaw)) {
            $stageHistory = $stageHistoryRaw;
        } elseif (is_string($stageHistoryRaw)) {
            $stageHistory = json_decode($stageHistoryRaw ?: '[]', true) ?: [];
        } else {
            $stageHistory = [];
        }

        $stageHistory[] = [
            'stage' => 'price_update',
            'changed_at' => $now->format('Y-m-d H:i:s'),
            'changed_by' => (string) $userId,
            'old_price' => (string) $oldPrice,
            'new_price' => (string) $newPrice,
        ];

        /*
         * --- PRICE HISTORY (neu, mit IDs) ---
         */
        $priceHistoryRaw = $leadProduct->price_history;

        if (is_array($priceHistoryRaw)) {
            $priceHistory = $priceHistoryRaw;
        } elseif (is_string($priceHistoryRaw)) {
            $priceHistory = json_decode($priceHistoryRaw ?: '[]', true) ?: [];
        } else {
            $priceHistory = [];
        }

        $priceHistory[] = [
            'changed_at' => $now->format('Y-m-d H:i:s'),
            'changed_by' => (string) $userId,
            'old_price' => (string) $oldPrice,
            'new_price' => (string) $newPrice,

            // extra context
            'customer_id' => (int) $leadProduct->customer_id,
            'alternative_id' => (int) $leadProduct->alternative_id,
            'product_id' => (int) $leadProduct->product_id,
        ];

        /*
         * --- LeadProduct updaten ---
         */
        $leadProduct->stage_history = $stageHistory;
        $leadProduct->price_history = $priceHistory;
        $leadProduct->price = $newPrice;
        $leadProduct->price_latest = $now->toDateString();
        $leadProduct->save();
        $this->logActivity('updated', LeadProductList::class, $leadProduct->id, $leadProduct->customer_id, $leadProduct->alternative_id, $leadProduct->product_id, [
            'price' => ['from' => $oldPrice, 'to' => $newPrice],
            'info' => 'Preis manuell aktualisiert'
        ]);

        /*
         * --- Gesamtsumme und Purchase-Felder auf new_leads ---
         */
        $total = LeadProductList::where('customer_id', $leadProduct->customer_id)
            ->whereNull('deleted_at')
            ->sum('price');

        $lead = NewLeads::find($leadProduct->customer_id);

        $purchaseStatus = null;
        $purchaseDate = null;

        if ($lead) {
            $purchaseStatus = $lead->purchase_status;
            $purchaseDate = $lead->purchase_date;

            if ($total <= 0) {
                $purchaseStatus = 'unknown';
                $purchaseDate = null;
            } else {
                if (!$purchaseStatus) {
                    $purchaseStatus = 'on_progress';
                }
                if (!$purchaseDate) {
                    $purchaseDate = $now->toDateString();
                }
            }

            $lead->update([
                'total_purchase' => $total,
                'purchase_status' => $purchaseStatus,
                'purchase_date' => $purchaseDate,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Preis aktualisiert.',
            'price_raw' => (float) $leadProduct->price,
            'price_formatted' => number_format($leadProduct->price, 2, ',', '.') . ' €',
            'price_latest' => $leadProduct->price_latest,

            'total_purchase_raw' => (float) $total,
            'total_purchase_formatted' => number_format($total, 2, ',', '.') . ' €',

            'purchase_status' => $purchaseStatus,
            'purchase_date' => $purchaseDate,
        ]);
    }



    public function priceHistoryForCustomer(NewLeads $customer)
    {
        $products = LeadProductList::with([
            'product:id,article_group,initial',
            'alternative:id,object_name,street,postcode,city',
        ])
            ->where('customer_id', $customer->id)
            ->whereNotNull('price_history')
            ->get();

        $entries = [];

        foreach ($products as $pl) {
            $history = $pl->price_history;

            if (is_string($history)) {
                $history = json_decode($history ?: '[]', true) ?: [];
            } elseif (!is_array($history)) {
                $history = [];
            }

            foreach ($history as $row) {
                $product = $pl->product;
                $alt = $pl->alternative;

                // product text
                $productName = $product->article_group ?? null;   // full name
                $productInit = $product->initial ?? null;         // short code
                $productLabel = trim(
                    ($productInit ? $productInit . ' · ' : '') .
                    ($productName ?? '')
                );

                // alternative: name + address
                $altName = $alt->object_name ?? null;
                $addr = null;
                if ($alt) {
                    $addr = trim(
                        ($alt->street ?? '') . ', ' .
                        trim(($alt->postcode ?? '') . ' ' . ($alt->city ?? ''))
                    );
                    if ($addr === ',' || trim($addr) === '') {
                        $addr = null;
                    }
                }
                $altLabelParts = [];
                if ($altName)
                    $altLabelParts[] = $altName;
                if ($addr)
                    $altLabelParts[] = $addr;
                $altLabel = implode(' • ', $altLabelParts);

                $entries[] = [
                    'changed_at' => $row['changed_at'] ?? null,
                    'old_price' => isset($row['old_price']) ? (float) $row['old_price'] : null,
                    'new_price' => isset($row['new_price']) ? (float) $row['new_price'] : null,
                    'user_id' => isset($row['changed_by']) ? (int) $row['changed_by'] : null,

                    'customer_id' => $row['customer_id'] ?? $pl->customer_id,
                    'alternative_id' => $row['alternative_id'] ?? $pl->alternative_id,
                    'product_id' => $row['product_id'] ?? $pl->product_id,

                    // labels for UI
                    'product_label' => $productLabel,
                    'product_name' => $productName,
                    'product_initial' => $productInit,
                    'alternative_label' => $altLabel,
                    'alternative_name' => $altName,
                    'alternative_address' => $addr,
                ];
            }
        }

        // newest first
        usort($entries, function ($a, $b) {
            return strcmp($b['changed_at'] ?? '', $a['changed_at'] ?? '');
        });

        // employees (changed_by = employee_id)
        $userIds = collect($entries)->pluck('user_id')->filter()->unique()->all();
        $employees = Employee::whereIn('id', $userIds)->get()->keyBy('id');

        foreach ($entries as &$e) {
            $emp = $e['user_id'] ? ($employees[$e['user_id']] ?? null) : null;
            $e['user_name'] = $emp ? trim(($emp->name ?? '') . ' ' . ($emp->lastname ?? '')) : null;

            if ($e['old_price'] !== null) {
                $e['old_price_formatted'] = number_format($e['old_price'], 2, ',', '.') . ' €';
            }
            if ($e['new_price'] !== null) {
                $e['new_price_formatted'] = number_format($e['new_price'], 2, ',', '.') . ' €';
            }
        }
        unset($e);

        $purchaseDateDisplay = $customer->purchase_date
            ? Carbon::parse($customer->purchase_date)->format('d.m.Y')
            : null;

        return response()->json([
            'entries' => $entries,
            'purchase_date' => $purchaseDateDisplay,
            'total_purchase_raw' => (float) $customer->total_purchase,
            'total_purchase_formatted' => number_format($customer->total_purchase, 2, ',', '.') . ' €',
        ]);
    }

    public function duplicates()
    {
        // Base scope: only "active" leads (same filters as index) – NOTE: qualified columns
        $base = DB::table('new_leads')
            ->whereNull('new_leads.deleted_at')
            ->whereNotIn('new_leads.status', ['Junk', 'plan']);

        // 1) Duplicate emails (only among active leads)
        $dupEmails = (clone $base)
            ->whereNotNull('new_leads.email')
            ->where('new_leads.email', '!=', '')
            ->select('new_leads.email', DB::raw('COUNT(*) as total'))
            ->groupBy('new_leads.email')
            ->having('total', '>', 1)
            ->pluck('email'); // ->pluck('new_leads.email') would also work

        // 2) Duplicate phones (only among active leads)
        $dupPhones = (clone $base)
            ->whereNotNull('new_leads.phone')
            ->where('new_leads.phone', '!=', '')
            ->select('new_leads.phone', DB::raw('COUNT(*) as total'))
            ->groupBy('new_leads.phone')
            ->having('total', '>', 1)
            ->pluck('phone');

        if ($dupEmails->isEmpty() && $dupPhones->isEmpty()) {
            return response()->json(['groups' => []]);
        }

        // 3) Load all leads that are part of these duplicate email/phone sets
        $baseDuplicates = $base
            ->leftJoin('customer_histories', 'customer_histories.customer_id', '=', 'new_leads.id')
            ->leftJoin('lead_alternative_adds', function ($join) {
                $join->on('lead_alternative_adds.lead_id', '=', 'new_leads.id')
                    ->whereNull('lead_alternative_adds.deleted_at');
            })
            ->leftJoin('lead_product_lists', function ($join) {
                $join->on('lead_product_lists.customer_id', '=', 'new_leads.id')
                    ->whereNull('lead_product_lists.deleted_at');
            })
            ->where(function ($q) use ($dupEmails, $dupPhones) {
                if ($dupEmails->count()) {
                    $q->orWhereIn('new_leads.email', $dupEmails);
                }
                if ($dupPhones->count()) {
                    $q->orWhereIn('new_leads.phone', $dupPhones);
                }
            })
            ->groupBy(
                'new_leads.id',
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.customer_no',
                'new_leads.email',
                'new_leads.phone',
                'new_leads.city',
                'new_leads.postcode'
            )
            ->select(
                'new_leads.id',
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.customer_no',
                'new_leads.email',
                'new_leads.phone',
                'new_leads.city',
                'new_leads.postcode',
                DB::raw('COUNT(DISTINCT lead_alternative_adds.id) as alternatives_count'),
                DB::raw('COUNT(DISTINCT lead_product_lists.id) as products_count'),
                DB::raw('COUNT(DISTINCT customer_histories.id) as histories_count')
            )
            ->orderBy('new_leads.lastname')
            ->orderBy('new_leads.name')
            ->get();

        if ($baseDuplicates->isEmpty()) {
            return response()->json(['groups' => []]);
        }

        $leadIds = $baseDuplicates->pluck('id');

        // 4) Alternatives (objects) – ignore soft-deleted alternatives
        $alternatives = DB::table('lead_alternative_adds')
            ->whereIn('lead_alternative_adds.lead_id', $leadIds)
            ->whereNull('lead_alternative_adds.deleted_at')
            ->select('lead_alternative_adds.id', 'lead_alternative_adds.lead_id', 'lead_alternative_adds.object_name')
            ->orderBy('lead_alternative_adds.id')
            ->get()
            ->groupBy('lead_id');

        // 5) Products per customer + alternative (with stage) – ignore soft-deleted products
        $products = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->whereIn('lead_product_lists.customer_id', $leadIds)
            ->whereNull('lead_product_lists.deleted_at')
            ->select(
                'lead_product_lists.customer_id',
                'lead_product_lists.alternative_id',
                'article_groups.article_group',
                'lead_product_lists.stage'
            )
            ->get();

        // 6) Stage distribution per customer – ignore soft-deleted products
        $stageStatsRaw = DB::table('lead_product_lists')
            ->whereIn('lead_product_lists.customer_id', $leadIds)
            ->whereNull('lead_product_lists.deleted_at')
            ->select('lead_product_lists.customer_id', 'lead_product_lists.stage', DB::raw('COUNT(*) as total'))
            ->groupBy('lead_product_lists.customer_id', 'lead_product_lists.stage')
            ->get()
            ->groupBy('customer_id');

        $stageOrder = [
            'lead',
            'offer',
            'deal',
            'project',
            'ticket',
            'pause',
            'cancel',
            'junk',
            'archive',
            'feedback',
        ];

        $byLead = [];

        foreach ($baseDuplicates as $lead) {
            $leadAlternatives = $alternatives->get($lead->id, collect());

            $objects = [];
            foreach ($leadAlternatives as $alt) {
                $altProducts = $products
                    ->where('customer_id', $lead->id)
                    ->where('alternative_id', $alt->id)
                    ->pluck('article_group')
                    ->unique()
                    ->values()
                    ->all();

                $objects[] = [
                    'id' => $alt->id,
                    'object_name' => $alt->object_name,
                    'products' => $altProducts,
                ];
            }

            // stage stats
            $leadStagesRaw = $stageStatsRaw->get($lead->id, collect());
            $stages = [];
            foreach ($stageOrder as $st) {
                $entry = $leadStagesRaw->firstWhere('stage', $st);
                if ($entry && $entry->total > 0) {
                    $stages[] = [
                        'stage' => $st,
                        'total' => (int) $entry->total,
                    ];
                }
            }

            // simple progress score
            $steps = 3;
            $done = 0;
            if ($lead->alternatives_count > 0)
                $done++;
            if ($lead->products_count > 0)
                $done++;
            if ($lead->histories_count > 0)
                $done++;
            $progress = $steps > 0 ? round($done / $steps * 100) : 0;

            // key = email or phone, fallback name (for grouping)
            if (!empty($lead->email)) {
                $dupKey = 'email:' . strtolower(trim($lead->email));
            } elseif (!empty($lead->phone)) {
                $dupKey = 'phone:' . preg_replace('/\s+/', '', $lead->phone);
            } else {
                $dupKey = 'name:' . strtolower(trim(($lead->lastname ?? '') . ' ' . ($lead->name ?? '')));
            }

            $fullName = trim(($lead->lastname ?? '') . ' ' . ($lead->name ?? ''));

            $byLead[] = [
                'id' => $lead->id,
                'duplicate_key' => $dupKey,
                'customer_no' => $lead->customer_no,
                'name' => $lead->name,
                'lastname' => $lead->lastname,
                'full_name' => $fullName,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'city' => $lead->city,
                'postcode' => $lead->postcode,
                'alternatives_count' => (int) $lead->alternatives_count,
                'products_count' => (int) $lead->products_count,
                'histories_count' => (int) $lead->histories_count,
                'progress' => $progress,
                'objects' => $objects,
                'stages' => $stages,
            ];
        }

        // 7) Group by duplicate_key
        $groupsMap = [];

        foreach ($byLead as $lead) {
            $key = $lead['duplicate_key'];

            if (!isset($groupsMap[$key])) {
                $label = $lead['full_name'] ?: ($lead['email'] ?: $lead['phone'] ?: 'Gruppe');
                $groupsMap[$key] = [
                    'key' => $key,
                    'label' => $label,
                    'email' => $lead['email'],
                    'phone' => $lead['phone'],
                    'count' => 0,
                    'customers' => [],
                ];
            }

            $groupsMap[$key]['customers'][] = $lead;
            $groupsMap[$key]['count']++;
        }

        // 8) Only keep real duplicates (groups with >= 2 customers)
        $groups = collect($groupsMap)
            ->filter(function ($g) {
                return ($g['count'] ?? 0) >= 2;
            })
            ->values()
            ->all();

        return response()->json([
            'groups' => $groups,
        ]);
    }


    /**
     * Delete a duplicate customer (soft delete)
     */
    public function destroyDuplicate($id)
    {
        $lead = NewLeads::findOrFail($id);
        $lead->delete(); // softDeletes + cascade on alternatives/products via FK

        return response()->json(['status' => 'ok']);
    }

    public function mergeDuplicate(Request $request)
    {
        $data = $request->validate([
            'main_id' => 'required|integer|exists:new_leads,id',
            'duplicate_id' => 'required|integer|exists:new_leads,id|different:main_id',
        ]);

        DB::transaction(function () use ($data) {
            $mainId = $data['main_id'];
            $duplicateId = $data['duplicate_id'];

            DB::table('lead_alternative_adds')
                ->where('lead_id', $duplicateId)
                ->update(['lead_id' => $mainId]);

            DB::table('lead_product_lists')
                ->where('customer_id', $duplicateId)
                ->update(['customer_id' => $mainId]);

            DB::table('customer_histories')
                ->where('customer_id', $duplicateId)
                ->update(['customer_id' => $mainId]);

            DB::table('images')
                ->where('customer_id', $duplicateId)
                ->update(['customer_id' => $mainId]);

            NewLeads::whereKey($duplicateId)->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Duplikat wurde erfolgreich zusammengeführt.',
        ]);
    }

    // In NewLeadsController.php

    public function loadHistoryFeed(Request $request, $id)
    {
        $query = DB::table('lead_activity_logs')
            ->where('new_leads_id', $id);

        /*
        |--------------------------------------------------------------------------
        | Context Filters
        |--------------------------------------------------------------------------
        */
        if ($request->filled('alternative_id')) {
            $query->where('alternative_id', (int) $request->alternative_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', (int) $request->product_id);
        }

        /*
        |--------------------------------------------------------------------------
        | Search Text
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search_text')) {
            $term = trim((string) $request->search_text);

            $query->where(function ($q) use ($term) {
                $q->where('user_name', 'like', "%{$term}%")
                    ->orWhere('event_type', 'like', "%{$term}%")
                    ->orWhere('model_type', 'like', "%{$term}%")
                    ->orWhere('changes', 'like', "%{$term}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Event Type Filter
        |--------------------------------------------------------------------------
        */
        if ($request->filled('event_type')) {
            $eventType = strtolower((string) $request->event_type);

            $eventMap = [
                'created' => ['created', 'create'],
                'updated' => ['updated', 'update'],
                'deleted' => ['deleted', 'delete'],
                'restored' => ['restored', 'restore'],
            ];

            if (isset($eventMap[$eventType])) {
                $query->whereIn('event_type', $eventMap[$eventType]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Model Type Filter
        |--------------------------------------------------------------------------
        */
        if ($request->filled('model_type')) {
            $modelType = (string) $request->model_type;

            $modelMap = [
                'customer' => 'NewLeads',
                'object' => 'LeadAlternativeAdd',
                'product' => 'LeadProductList',
                'note' => 'CustomerNote',
                'comment' => 'OfferComment',
                'ticket' => 'Problem',
                'invoice' => 'Invoice',
                'appointment' => 'MainAppointment',
            ];

            if (isset($modelMap[$modelType])) {
                $query->where('model_type', 'like', '%' . $modelMap[$modelType] . '%');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Date + Time Range Filter
        |--------------------------------------------------------------------------
        | Supports:
        | - date_from: YYYY-MM-DD
        | - date_to: YYYY-MM-DD
        | - time_from: HH:MM
        | - time_to: HH:MM
        |--------------------------------------------------------------------------
        */
        if ($request->filled('date_from')) {
            $dateFrom = $request->date_from;
            $timeFrom = $request->filled('time_from') ? $request->time_from : '00:00';

            $query->where('created_at', '>=', $dateFrom . ' ' . $timeFrom . ':00');
        }

        if ($request->filled('date_to')) {
            $dateTo = $request->date_to;
            $timeTo = $request->filled('time_to') ? $request->time_to : '23:59';

            $query->where('created_at', '<=', $dateTo . ' ' . $timeTo . ':59');
        }

        /*
        |--------------------------------------------------------------------------
        | Single Date Fallback
        |--------------------------------------------------------------------------
        */
        if (!$request->filled('date_from') && !$request->filled('date_to') && $request->filled('search_date')) {
            $query->whereDate('created_at', $request->search_date);
        }

        /*
        |--------------------------------------------------------------------------
        | Clone before fetch for analytics
        |--------------------------------------------------------------------------
        */
        $analyticsQuery = clone $query;

        /*
        |--------------------------------------------------------------------------
        | Fetch Logs
        |--------------------------------------------------------------------------
        */
        $logs = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(500)
            ->get()
            ->map(function ($log) {
                $decodedChanges = json_decode($log->changes, true);

                $log->raw_changes = is_array($decodedChanges) ? $decodedChanges : [];
                $log->display_changes = $this->formatLeadActivityChanges($log->raw_changes);
                $log->event_label = $this->formatLeadActivityEventLabel($log->event_type);
                $log->model_label = $this->formatLeadActivityModelLabel($log->model_type);
                $log->icon_data = $this->getLeadActivityIconData($log->event_type);
                $log->display_user_name = $this->formatLeadActivityUserName($log->user_name, $log->user_id);

                return $log;
            });

        /*
        |--------------------------------------------------------------------------
        | Analytics
        |--------------------------------------------------------------------------
        */
        $analyticsRows = $analyticsQuery
            ->select([
                'id',
                'event_type',
                'model_type',
                'user_id',
                'user_name',
                'created_at',
            ])
            ->get();

        $firstLogDate = $analyticsRows->min('created_at');
        $lastLogDate = $analyticsRows->max('created_at');

        $totalDays = 0;

        if ($firstLogDate && $lastLogDate) {
            $totalDays = \Carbon\Carbon::parse($firstLogDate)
                ->startOfDay()
                ->diffInDays(\Carbon\Carbon::parse($lastLogDate)->startOfDay()) + 1;
        }

        $eventCounts = [
            'created' => $analyticsRows->filter(function ($row) {
                return in_array(strtolower((string) $row->event_type), ['created', 'create'], true);
            })->count(),

            'updated' => $analyticsRows->filter(function ($row) {
                return in_array(strtolower((string) $row->event_type), ['updated', 'update'], true);
            })->count(),

            'deleted' => $analyticsRows->filter(function ($row) {
                return in_array(strtolower((string) $row->event_type), ['deleted', 'delete'], true);
            })->count(),

            'restored' => $analyticsRows->filter(function ($row) {
                return in_array(strtolower((string) $row->event_type), ['restored', 'restore'], true);
            })->count(),
        ];

        $modelCounts = $analyticsRows
            ->groupBy(function ($row) {
                return $this->formatLeadActivityModelLabel($row->model_type);
            })
            ->map(fn($items) => $items->count())
            ->sortDesc();

        $activeUsersCount = $analyticsRows
            ->map(function ($row) {
                return $row->user_name ?: $row->user_id;
            })
            ->filter()
            ->unique()
            ->count();

        $analytics = [
            'total' => $analyticsRows->count(),
            'total_days' => $totalDays,
            'first_log_date' => $firstLogDate ? \Carbon\Carbon::parse($firstLogDate)->format('d.m.Y') : null,
            'last_log_date' => $lastLogDate ? \Carbon\Carbon::parse($lastLogDate)->format('d.m.Y') : null,
            'created' => $eventCounts['created'],
            'updated' => $eventCounts['updated'],
            'deleted' => $eventCounts['deleted'],
            'restored' => $eventCounts['restored'],
            'active_users' => $activeUsersCount,
            'model_counts' => $modelCounts,
        ];

        /*
        |--------------------------------------------------------------------------
        | Group logs by date for nicer timeline
        |--------------------------------------------------------------------------
        */
        $groupedLogs = $logs->groupBy(function ($log) {
            return \Carbon\Carbon::parse($log->created_at)->format('Y-m-d');
        });

        return view('admin.new_leads.layouts.history', [
            'logs' => $logs,
            'groupedLogs' => $groupedLogs,
            'analytics' => $analytics,
            'filters' => $request->all(),
        ]);
    }
    private function formatLeadActivityChanges(array $changes): array
    {
        if (empty($changes)) {
            return [
                [
                    'field' => 'Info',
                    'type' => 'text',
                    'value' => 'Keine Details gespeichert.',
                ],
            ];
        }

        $rows = [];

        foreach ($changes as $field => $value) {
            /*
            |--------------------------------------------------------------------------
            | Skip huge/noisy audit fields or summarize them
            |--------------------------------------------------------------------------
            */
            if (
                in_array($field, [
                    'updated_at',
                    'created_at',
                    'deleted_at',
                ], true)
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Special: stage_history / price_history
            |--------------------------------------------------------------------------
            */
            if (in_array($field, ['stage_history', 'price_history'], true)) {
                $rows[] = $this->formatHistoryArrayField($field, $value);
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Format: "price": {"from": 400, "to": 400}
            |--------------------------------------------------------------------------
            */
            if (is_array($value) && (array_key_exists('from', $value) || array_key_exists('to', $value))) {
                $from = $value['from'] ?? null;
                $to = $value['to'] ?? null;

                /*
                |--------------------------------------------------------------------------
                | Do not hide same-value changes if info exists.
                | Example: price 400 -> 400 because invoice sync happened.
                |--------------------------------------------------------------------------
                */
                $rows[] = [
                    'field' => $this->formatLeadActivityFieldName($field),
                    'type' => 'change',
                    'from' => $this->formatLeadActivityValue($from),
                    'to' => $this->formatLeadActivityValue($to),
                ];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Format: "info": "Preis automatisch ..."
            |--------------------------------------------------------------------------
            */
            if (is_string($value) || is_numeric($value) || is_bool($value) || is_null($value)) {
                $rows[] = [
                    'field' => $this->formatLeadActivityFieldName($field),
                    'type' => 'text',
                    'value' => $this->formatLeadActivityValue($value),
                ];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Format: "attributes": {...}
            |--------------------------------------------------------------------------
            */
            if ($field === 'attributes' && is_array($value)) {
                foreach ($value as $attributeField => $attributeValue) {
                    if (in_array($attributeField, ['created_at', 'updated_at', 'deleted_at'], true)) {
                        continue;
                    }

                    $rows[] = [
                        'field' => $this->formatLeadActivityFieldName($attributeField),
                        'type' => 'text',
                        'value' => $this->formatLeadActivityValue($attributeValue),
                    ];
                }

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Format: nested array without from/to
            |--------------------------------------------------------------------------
            */
            if (is_array($value)) {
                $rows[] = [
                    'field' => $this->formatLeadActivityFieldName($field),
                    'type' => 'text',
                    'value' => $this->summarizeLeadActivityArray($value),
                ];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback
            |--------------------------------------------------------------------------
            */
            $rows[] = [
                'field' => $this->formatLeadActivityFieldName($field),
                'type' => 'text',
                'value' => $this->formatLeadActivityValue($value),
            ];
        }

        if (empty($rows)) {
            return [
                [
                    'field' => 'Info',
                    'type' => 'text',
                    'value' => 'Keine sichtbaren Änderungen.',
                ],
            ];
        }

        return $rows;
    }

    private function formatHistoryArrayField(string $field, mixed $value): array
    {
        $decoded = $value;

        if (is_string($decoded)) {
            $decodedJson = json_decode($decoded, true);
            $decoded = is_array($decodedJson) ? $decodedJson : [];
        }

        if (!is_array($decoded)) {
            return [
                'field' => $this->formatLeadActivityFieldName($field),
                'type' => 'text',
                'value' => 'Keine Historie.',
            ];
        }

        $count = count($decoded);
        $latest = $count > 0 ? end($decoded) : null;

        if (is_array($latest)) {
            $parts = [];

            if (!empty($latest['stage'])) {
                $parts[] = 'Status: ' . $latest['stage'];
            }

            if (!empty($latest['old_price']) || !empty($latest['new_price'])) {
                $parts[] = 'Preis: ' . ($latest['old_price'] ?? '-') . ' → ' . ($latest['new_price'] ?? '-');
            }

            if (!empty($latest['source'])) {
                $parts[] = 'Quelle: ' . $latest['source'];
            }

            if (!empty($latest['invoice_count'])) {
                $parts[] = 'Rechnungen: ' . $latest['invoice_count'];
            }

            if (!empty($latest['changed_at'])) {
                $parts[] = 'Zeit: ' . $latest['changed_at'];
            }

            if (!empty($latest['description'])) {
                $parts[] = 'Grund: ' . $latest['description'];
            }

            return [
                'field' => $this->formatLeadActivityFieldName($field),
                'type' => 'text',
                'value' => $count . ' Einträge' . (!empty($parts) ? ' · Letzter: ' . implode(' · ', $parts) : ''),
            ];
        }

        return [
            'field' => $this->formatLeadActivityFieldName($field),
            'type' => 'text',
            'value' => $count . ' Einträge',
        ];
    }

    private function summarizeLeadActivityArray(array $value): string
    {
        if (empty($value)) {
            return '-';
        }

        /*
        |--------------------------------------------------------------------------
        | Sequential array
        |--------------------------------------------------------------------------
        */
        if (array_is_list($value)) {
            $count = count($value);

            if ($count === 0) {
                return '-';
            }

            $latest = end($value);

            if (is_array($latest)) {
                $important = collect($latest)
                    ->only([
                        'stage',
                        'status',
                        'old_price',
                        'new_price',
                        'source',
                        'invoice_count',
                        'changed_at',
                        'description',
                    ])
                    ->filter(fn($item) => $item !== null && $item !== '')
                    ->map(fn($item, $key) => $this->formatLeadActivityFieldName($key) . ': ' . $this->formatLeadActivityValue($item))
                    ->values()
                    ->implode(' · ');

                return $count . ' Einträge' . ($important ? ' · Letzter: ' . $important : '');
            }

            return $count . ' Einträge';
        }

        /*
        |--------------------------------------------------------------------------
        | Associative array
        |--------------------------------------------------------------------------
        */
        return collect($value)
            ->take(6)
            ->map(function ($item, $key) {
                return $this->formatLeadActivityFieldName($key) . ': ' . $this->formatLeadActivityValue($item);
            })
            ->implode(' · ');
    }

    private function formatLeadActivityValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if (is_bool($value)) {
            return $value ? 'Ja' : 'Nein';
        }

        if (is_array($value)) {
            return $this->summarizeLeadActivityArray($value);
        }

        if (is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $value = (string) $value;

        /*
        |--------------------------------------------------------------------------
        | If value itself is JSON, summarize it instead of printing giant JSON.
        |--------------------------------------------------------------------------
        */
        $trimmed = trim($value);

        if (
            (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) ||
            (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}'))
        ) {
            $decoded = json_decode($trimmed, true);

            if (is_array($decoded)) {
                return $this->summarizeLeadActivityArray($decoded);
            }
        }

        return $value;
    }

    private function formatLeadActivityFieldName(string $field): string
    {
        $labels = [
            'price' => 'Preis',
            'price_latest' => 'Preisdatum',
            'price_history' => 'Preishistorie',
            'stage' => 'Status',
            'old_stage' => 'Alter Status',
            'stage_history' => 'Statushistorie',
            'work_status' => 'Arbeitsstatus',
            'status' => 'Status',
            'status_msg' => 'Statusmeldung',
            'info' => 'Info',
            'net_amount' => 'Netto',
            'gross_amount' => 'Brutto',
            'tax_amount' => 'Steuer',
            'receipt_date' => 'Belegdatum',
            'receipt_reference' => 'Belegreferenz',
            'teams_count' => 'Teams',
            'product_name' => 'Produkt',
            'content_snippet' => 'Inhalt',
            'parent_note_id' => 'Antwort auf Notiz',
            'offer_no' => 'Angebotsnummer',
            'service' => 'Service',
        ];

        if (isset($labels[$field])) {
            return $labels[$field];
        }

        return str($field)
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    private function formatLeadActivityEventLabel(?string $eventType): string
    {
        return match (strtolower((string) $eventType)) {
            'created', 'create' => 'Erstellt',
            'updated', 'update' => 'Aktualisiert',
            'deleted', 'delete' => 'Gelöscht',
            'restored', 'restore' => 'Wiederhergestellt',
            default => ucfirst((string) $eventType),
        };
    }

    private function formatLeadActivityModelLabel(?string $modelType): string
    {
        $modelType = (string) $modelType;

        return match (true) {
            str_contains($modelType, 'LeadProductList') => 'Produkt',
            str_contains($modelType, 'CustomerNote') => 'Notiz',
            str_contains($modelType, 'OfferComment') => 'Kommentar',
            str_contains($modelType, 'Problem') => 'Ticket',
            str_contains($modelType, 'Invoice') => 'Rechnung',
            str_contains($modelType, 'MainAppointment') => 'Termin',
            str_contains($modelType, 'NewLeads') => 'Kunde',
            str_contains($modelType, 'LeadAlternativeAdd') => 'Objekt',
            default => 'Datensatz',
        };
    }

    private function getLeadActivityIconData(?string $eventType): array
    {
        return match (strtolower((string) $eventType)) {
            'created', 'create' => [
                'icon' => 'plus',
                'color' => '#10b981',
            ],
            'deleted', 'delete' => [
                'icon' => 'trash-2',
                'color' => '#ef4444',
            ],
            'restored', 'restore' => [
                'icon' => 'rotate-ccw',
                'color' => '#74b2d4',
            ],
            default => [
                'icon' => 'edit-2',
                'color' => '#f59e0b',
            ],
        };
    }

    private function formatLeadActivityUserName(?string $userName, mixed $userId = null): string
    {
        if ($userName && !is_numeric($userName)) {
            return $userName;
        }

        /*
        |--------------------------------------------------------------------------
        | Your app stores employee id in auth()->user()->name.
        | Older logs may contain only "7", so try to resolve employee name.
        |--------------------------------------------------------------------------
        */
        $employeeId = null;

        if (is_numeric($userName)) {
            $employeeId = (int) $userName;
        } elseif (is_numeric($userId)) {
            $employeeId = (int) $userId;
        }

        if ($employeeId) {
            $employee = \App\Models\Employee::query()
                ->select('id', 'name', 'lastname')
                ->find($employeeId);

            if ($employee) {
                $full = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

                return $full ?: '#' . $employee->id;
            }

            return '#' . $employeeId;
        }

        return 'Unbekannt';
    }
    /**
     * Helper to log activity manually.
     * Use this when DB::table is used or when specific context is needed.
     */
    /**
     * Helper function to log activity.
     * Resolves the Employee Name from the ID stored in auth()->user()->name.
     */
    private function logActivity($event, $modelType, $modelId, $leadId, $altId = null, $prodId = null, $changes = [])
    {
        $userName = 'System';

        if (Auth::check()) {
            // auth()->user()->name is the Employee ID
            $employeeId = Auth::user()->name;

            // Find the employee to get the real name
            $employee = DB::table('employees')->where('id', $employeeId)->first();

            if ($employee) {
                $userName = trim($employee->name . ' ' . $employee->lastname);
            } else {
                $userName = 'Mitarbeiter #' . $employeeId; // Fallback
            }
        }

        DB::table('lead_activity_logs')->insert([
            'new_leads_id' => $leadId,
            'alternative_id' => $altId,
            'product_id' => $prodId,
            'user_id' => Auth::id(), // Login User ID
            'user_name' => $userName,  // Resolved Employee Name
            'event_type' => $event,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'changes' => json_encode($changes),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function loadPartial($customer_id, $alternative_id, $product_id, $section)
    {
        // 1. Handle the 'angebote' section
        if ($section === 'angebote') {

            // Fetch Offers with their Folders and Details (for pricing)
            $offers = Offer::with(['creator', 'folders.creator', 'folders.detail'])
                ->where('customer_id', $customer_id)
                ->where('alternative_id', $alternative_id)
                ->where('product_id', $product_id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculate Analytics for the top bar
            $analytics = [
                'total_offers' => $offers->count(),
                'total_folders' => 0,
                'total_gross' => 0,
                'total_net' => 0,
            ];

            foreach ($offers as $offer) {
                $analytics['total_folders'] += $offer->folders->count();

                foreach ($offer->folders as $folder) {
                    if ($folder->detail) {
                        $analytics['total_gross'] += (float) $folder->detail->total_gross;
                        $analytics['total_net'] += (float) $folder->detail->total_net;
                    }
                }
            }

            // Return the view and PASS the $analytics variable!
            return view('admin.new_leads.layouts.angebote', compact(
                'offers',
                'customer_id',
                'alternative_id',
                'product_id',
                'analytics' // <--- This fixes the Undefined variable error!
            ));
        }


        return response("<div class='alert alert-warning p-3'>Der Abschnitt '{$section}' ist noch nicht verfügbar.</div>", 200);
    }


    public function getQuickSidebarInfo($id)
    {
        // Kunden laden mit Notizen, Reports und deren jeweiligen Relationen (Objekt, Produkt, Ersteller)
        $customer = \App\Models\NewLeads::with([
            'customerNotes.creator',
            'customerNotes.alternative', // Lädt das Objekt (LeadAlternativeAdd)
            'customerNotes.product',     // Lädt das Produkt (ArticleGroup)
            'reports.reporter',          // Lädt den Mitarbeiter, der den Report geschrieben hat (report_by)
            'reports.alternative',       // Lädt das Objekt
            'reports.product'            // Lädt das Produkt
        ])->findOrFail($id);

        // Termine laden inkl. der AppointmentReports
        $appointments = \App\Models\MainAppointment::with([
            'reports.reporter', // Termin-Reports (AppointmentReport) mit Autor laden
            'reports.employee',
            'creator'
        ])
            ->where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.new_leads.partials.quick_sidebar_content', compact('customer', 'appointments'));
    }

    public function junks(Request $request)
    {
        $search = $request->query('search');
        $sortBy = $request->get('sort_by', 'new_leads.id');
        $sortOrder = strtolower($request->get('sort_order', 'desc'));

        $allowedSorts = [
            'new_leads.id',
            'new_leads.name',
            'new_leads.customer_no',
            'new_leads.source',
            'new_leads.quelle',
            'new_leads.lastname',
            'new_leads.email',
            'new_leads.phone',
            'c_name',
            'new_leads.contact_person',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'new_leads.id';
        }

        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        $orderByColumn = $sortBy === 'new_leads.quelle'
            ? 'new_leads.source'
            : $sortBy;

        $leadQuery = DB::table('new_leads')
            ->leftJoin('employees as contact_person', 'contact_person.id', '=', 'new_leads.contact_person')
            ->leftJoin('employees as junk_actor', 'junk_actor.id', '=', 'new_leads.junked_by')
            ->select(
                'new_leads.*',

                'contact_person.name as c_name',
                'contact_person.lastname as c_lastname',
                'contact_person.image as c_image',

                'junk_actor.name as reason_actor_name',
                'junk_actor.lastname as reason_actor_lastname',
                'junk_actor.image as reason_actor_image'
            )
            ->where('new_leads.status', 'Junk')
            ->whereNull('new_leads.deleted_at');

        if (!empty($search)) {
            $leadQuery->where(function ($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.lastname', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.id', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.customer_no', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.source', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.email', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.phone', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.telephone', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.firma', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.city', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.postcode', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.junk_reason', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.status_msg', 'LIKE', "%{$search}%");
            });
        }

        if ($orderByColumn === 'new_leads.customer_no') {
            $leadQuery->orderByRaw('CAST(new_leads.customer_no AS UNSIGNED) ' . $sortOrder);
        } elseif ($orderByColumn === 'c_name') {
            $leadQuery->orderBy('contact_person.name', $sortOrder);
        } else {
            $leadQuery->orderBy($orderByColumn, $sortOrder);
        }

        $leads = $leadQuery
            ->paginate(20)
            ->appends($request->query());

        $article = ArticleGroup::orderBy('article_group')->get();

        $productcount = DB::table('lead_product_lists')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'lead_product_lists.alternative_id')
            ->select(
                'lead_product_lists.*',
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id'
            )
            ->whereNull('lead_alternative_adds.deleted_at')
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        $productEmployees = DB::table('employees')
            ->select('id', 'name', 'lastname', 'image', 'status', 'gender')
            ->get();

        $customer_product_lists = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->leftJoin('employees as inner_employee', 'inner_employee.id', '=', 'lead_product_lists.employee_id')
            ->leftJoin('employees as field_employee', 'field_employee.id', '=', 'lead_product_lists.field_employee')
            ->select(
                'lead_product_lists.*',
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id',
                'new_leads.id as customer_id',
                'departments.department_name',
                'phase_sections.phase_section as service_phase_section',
                'lead_product_lists.id as p_list_id',
                'lead_product_lists.created_at as product_created',

                'inner_employee.name as ename',
                'inner_employee.lastname as elastname',
                'inner_employee.image as eimage',

                'field_employee.name as fname',
                'field_employee.lastname as flastname',
                'field_employee.image as fimage'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();
        $this->hydrateLeadProductTeams($customer_product_lists);

        $customer_product_count = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select(
                'new_leads.id as customer_id',
                'new_leads.name',
                'new_leads.lastname',
                'article_groups.article_group',
                'lead_product_lists.status',
                'lead_product_lists.id'
            )
            ->where('new_leads.status', 'Junk')
            ->whereNull('new_leads.deleted_at')
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        $statusKeys = ['open', 'active', 'inactive', 'ended', 'cancel'];
        $counts = [];

        foreach ($statusKeys as $status) {
            $counts[$status] = $customer_product_count
                ->where('status', $status)
                ->count();
        }

        $all = $customer_product_count->count();

        $counts['all'] = $all;
        $counts['open_per'] = $all ? ($counts['open'] / $all) * 100 : 0;
        $counts['active_per'] = $all ? ($counts['active'] / $all) * 100 : 0;
        $counts['inactive_per'] = $all ? ($counts['inactive'] / $all) * 100 : 0;
        $counts['end_per'] = $all ? ($counts['ended'] / $all) * 100 : 0;
        $counts['cancel_per'] = $all ? ($counts['cancel'] / $all) * 100 : 0;

        $statusCounts = [
            'open' => $customer_product_count->where('status', 'open')->count(),
            'active' => $customer_product_count->where('status', 'active')->count(),
            'inactive' => $customer_product_count->where('status', 'inactive')->count(),
            'ended' => $customer_product_count->where('status', 'ended')->count(),
            'cancel' => $customer_product_count->where('status', 'cancel')->count(),
        ];

        $current_request = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select(
                'article_groups.article_group',
                'article_groups.initial',
                'lead_product_lists.*'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        $alternative = DB::table('lead_alternative_adds')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'junk')
            ->orderByDesc('id')
            ->get();

        $employees = Employee::select('id', 'name', 'lastname', 'image')->get();

        $departments = Department::where('status', 'published')->get();

        $productInfo = DB::table('article_groups')->get();

        $serviceList = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section')
            ->whereNull('deleted_at')
            ->get();

        return view('admin.new_leads.reason_list', [
            'data' => $leads,
            'article' => $article,
            'productcount' => $productcount,
            'productEmployees' => $productEmployees,
            'customer_product_lists' => $customer_product_lists,
            'customer_product_count' => $customer_product_count,
            'counts' => $counts,
            'statusCounts' => $statusCounts,
            'current_request' => $current_request,
            'alternative' => $alternative,
            'employees' => $employees,
            'departments' => $departments,
            'productInfo' => $productInfo,
            'serviceList' => $serviceList,
            'selectedProducts' => [],
            'stage' => 'junk',
            'search' => $search,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function deleted_lead(Request $request)
    {
        $search = $request->query('search');
        $sortBy = $request->get('sort_by', 'new_leads.id');
        $sortOrder = strtolower($request->get('sort_order', 'desc'));

        $allowedSorts = [
            'new_leads.id',
            'new_leads.name',
            'new_leads.customer_no',
            'new_leads.source',
            'new_leads.quelle',
            'new_leads.lastname',
            'new_leads.email',
            'new_leads.phone',
            'c_name',
            'new_leads.contact_person',
            'new_leads.deleted_at',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'new_leads.id';
        }

        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        $orderByColumn = $sortBy === 'new_leads.quelle'
            ? 'new_leads.source'
            : $sortBy;

        $leadQuery = DB::table('new_leads')
            ->leftJoin('employees as contact_person', 'contact_person.id', '=', 'new_leads.contact_person')
            ->leftJoin('employees as delete_actor', 'delete_actor.id', '=', 'new_leads.deleted_by')
            ->select(
                'new_leads.*',

                'contact_person.name as c_name',
                'contact_person.lastname as c_lastname',
                'contact_person.image as c_image',

                'delete_actor.name as reason_actor_name',
                'delete_actor.lastname as reason_actor_lastname',
                'delete_actor.image as reason_actor_image'
            )
            ->whereNotNull('new_leads.deleted_at');

        if (!empty($search)) {
            $leadQuery->where(function ($q) use ($search) {
                $q->where('new_leads.name', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.lastname', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.id', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.customer_no', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.source', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.email', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.phone', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.telephone', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.firma', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.city', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.postcode', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.delete_reason', 'LIKE', "%{$search}%")
                    ->orWhere('new_leads.status_msg', 'LIKE', "%{$search}%");
            });
        }

        if ($orderByColumn === 'new_leads.customer_no') {
            $leadQuery->orderByRaw('CAST(new_leads.customer_no AS UNSIGNED) ' . $sortOrder);
        } elseif ($orderByColumn === 'c_name') {
            $leadQuery->orderBy('contact_person.name', $sortOrder);
        } else {
            $leadQuery->orderBy($orderByColumn, $sortOrder);
        }

        $leads = $leadQuery
            ->paginate(20)
            ->appends($request->query());

        $article = ArticleGroup::orderBy('article_group')->get();

        $productcount = DB::table('lead_product_lists')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('lead_alternative_adds', 'lead_alternative_adds.id', '=', 'lead_product_lists.alternative_id')
            ->select(
                'lead_product_lists.*',
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id'
            )
            ->whereNull('lead_alternative_adds.deleted_at')
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        $productEmployees = DB::table('employees')
            ->select('id', 'name', 'lastname', 'image', 'status', 'gender')
            ->get();

        $customer_product_lists = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->leftJoin('employees as inner_employee', 'inner_employee.id', '=', 'lead_product_lists.employee_id')
            ->leftJoin('employees as field_employee', 'field_employee.id', '=', 'lead_product_lists.field_employee')
            ->select(
                'lead_product_lists.*',
                'article_groups.initial',
                'article_groups.article_group',
                'article_groups.id as product_id',
                'new_leads.id as customer_id',
                'departments.department_name',
                'phase_sections.phase_section as service_phase_section',
                'lead_product_lists.id as p_list_id',
                'lead_product_lists.created_at as product_created',

                'inner_employee.name as ename',
                'inner_employee.lastname as elastname',
                'inner_employee.image as eimage',

                'field_employee.name as fname',
                'field_employee.lastname as flastname',
                'field_employee.image as fimage'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        $customer_product_count = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select(
                'new_leads.id as customer_id',
                'new_leads.name',
                'new_leads.lastname',
                'article_groups.article_group',
                'lead_product_lists.status',
                'lead_product_lists.id'
            )
            ->whereNotNull('new_leads.deleted_at')
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        $statusKeys = ['open', 'active', 'inactive', 'ended', 'cancel'];
        $counts = [];

        foreach ($statusKeys as $status) {
            $counts[$status] = $customer_product_count
                ->where('status', $status)
                ->count();
        }

        $all = $customer_product_count->count();

        $counts['all'] = $all;
        $counts['open_per'] = $all ? ($counts['open'] / $all) * 100 : 0;
        $counts['active_per'] = $all ? ($counts['active'] / $all) * 100 : 0;
        $counts['inactive_per'] = $all ? ($counts['inactive'] / $all) * 100 : 0;
        $counts['end_per'] = $all ? ($counts['ended'] / $all) * 100 : 0;
        $counts['cancel_per'] = $all ? ($counts['cancel'] / $all) * 100 : 0;

        $statusCounts = [
            'open' => $customer_product_count->where('status', 'open')->count(),
            'active' => $customer_product_count->where('status', 'active')->count(),
            'inactive' => $customer_product_count->where('status', 'inactive')->count(),
            'ended' => $customer_product_count->where('status', 'ended')->count(),
            'cancel' => $customer_product_count->where('status', 'cancel')->count(),
        ];

        $current_request = DB::table('lead_product_lists')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->select(
                'article_groups.article_group',
                'article_groups.initial',
                'lead_product_lists.*'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->get();

        $alternative = DB::table('lead_alternative_adds')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'junk')
            ->orderByDesc('id')
            ->get();

        $employees = Employee::select('id', 'name', 'lastname', 'image')->get();

        $departments = Department::where('status', 'published')->get();

        $productInfo = DB::table('article_groups')->get();

        $serviceList = DB::table('phase_sections')
            ->select('id', 'product_id', 'phase_section')
            ->whereNull('deleted_at')
            ->get();

        return view('admin.new_leads.reason_list', [
            'data' => $leads,
            'article' => $article,
            'productcount' => $productcount,
            'productEmployees' => $productEmployees,
            'customer_product_lists' => $customer_product_lists,
            'customer_product_count' => $customer_product_count,
            'counts' => $counts,
            'statusCounts' => $statusCounts,
            'current_request' => $current_request,
            'alternative' => $alternative,
            'employees' => $employees,
            'departments' => $departments,
            'productInfo' => $productInfo,
            'serviceList' => $serviceList,
            'selectedProducts' => [],
            'stage' => 'deleted',
            'search' => $search,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function updateReason(Request $request, $lead)
    {
        $request->validate([
            'stage' => 'required|in:junk,deleted',
            'reason' => 'required|string|max:1000',
        ], [
            'stage.required' => 'Der Typ ist erforderlich.',
            'stage.in' => 'Ungültiger Typ.',
            'reason.required' => 'Bitte einen Grund eingeben.',
            'reason.max' => 'Der Grund darf maximal 1000 Zeichen lang sein.',
        ]);

        $stage = $request->input('stage');

        if ($stage === 'deleted') {
            $data = NewLeads::withTrashed()->findOrFail($lead);

            $data->delete_reason = $request->input('reason');
            $data->deleted_reason_at = now();

            if (Schema::hasColumn('new_leads', 'deleted_by')) {
                $data->deleted_by = auth()->user()?->name;
            }

            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Löschgrund wurde erfolgreich aktualisiert.',
                'reason' => $data->delete_reason,
                'date' => optional($data->deleted_reason_at)->format('d.m.Y H:i'),
            ]);
        }

        $data = NewLeads::findOrFail($lead);

        $data->junk_reason = $request->input('reason');
        $data->junked_at = now();

        if (Schema::hasColumn('new_leads', 'junked_by')) {
            $data->junked_by = auth()->user()?->name;
        }

        $data->save();

        return response()->json([
            'success' => true,
            'message' => 'Junk-Grund wurde erfolgreich aktualisiert.',
            'reason' => $data->junk_reason,
            'date' => optional($data->junked_at)->format('d.m.Y H:i'),
        ]);
    }

    public function panel(Request $request, int $customer, int $alternative, int $product)
    {
        $lead = NewLeads::query()->findOrFail($customer);

        $base = Invoice::query()
            ->with(['items', 'files'])
            ->where('customer_id', $lead->id);

        // Alternative/Objekt-Filter
        if ($alternative > 0) {
            $base->where('object_id', $alternative);
        }

        // Wenn Produkt gewählt:
        // - "Produkt-Rechnungen" = hat mindestens eine Position mit product_id
        // - "Allgemeine Rechnungen" = hat KEINE Position mit product_id (inkl. 0 Positionen)
        if ($product > 0) {
            $productInvoices = (clone $base)
                ->whereHas('items', fn($iq) => $iq->where('product_id', $product))
                ->orderByDesc('issue_date')
                ->limit(250)
                ->get();

            $generalInvoices = (clone $base)
                ->whereDoesntHave('items', fn($iq) => $iq->where('product_id', $product))
                ->orderByDesc('issue_date')
                ->limit(250)
                ->get();
        } else {
            // Kein Produkt gewählt -> alles in "Produkt-Rechnungen", "Allgemein" leer
            $productInvoices = (clone $base)
                ->orderByDesc('issue_date')
                ->limit(250)
                ->get();

            $generalInvoices = collect();
        }

        $html = view('admin.new_leads.layouts.invoice', [
            'customer' => $lead,
            'alternative_id' => $alternative,
            'product_id' => $product,
            'product_invoices' => $productInvoices,
            'general_invoices' => $generalInvoices,
        ])->render();

        return response()->json(['ok' => true, 'html' => $html]);
    }

    public function sidebarCounts(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['required', 'integer', 'exists:lead_alternative_adds,id'],
            'product_id' => ['required', 'integer', 'exists:article_groups,id'],
        ]);

        $customerId = (int) $data['customer_id'];
        $alternativeId = (int) $data['alternative_id'];
        $productId = (int) $data['product_id'];

        /*
        |--------------------------------------------------------------------------
        | Bilder & Dokumente
        |--------------------------------------------------------------------------
        */
        $documentsCount = Image::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('article_group', $productId)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Aufgaben
        |--------------------------------------------------------------------------
        */
        $tasksBaseQuery = PersonalTask::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId);

        $tasksCount = (clone $tasksBaseQuery)->count();

        $openTasksCount = (clone $tasksBaseQuery)
            ->where(function ($q) {
                $q->whereNull('task_status')
                    ->orWhereNotIn('task_status', [
                        'done',
                        'complete',
                        'completed',
                        'erledigt',
                        'geschlossen',
                        'end',
                    ]);
            })
            ->count();


        /*
|--------------------------------------------------------------------------
| Real Deals / Aufträge
|--------------------------------------------------------------------------
*/
        $dealsBaseQuery = Deal::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId);

        $realDealsCount = (clone $dealsBaseQuery)->count();

        $openDealsCount = (clone $dealsBaseQuery)
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereNotIn('status', [
                        'done',
                        'complete',
                        'completed',
                        'geschlossen',
                        'closed',
                        'end',
                        'cancel',
                        'cancelled',
                        'canceled',
                        'storniert',
                    ]);
            })
            ->count();

        $cancelledDealsCount = (clone $dealsBaseQuery)
            ->whereIn('status', [
                'cancel',
                'cancelled',
                'canceled',
                'storniert',
            ])
            ->count();

        $completedDealsCount = (clone $dealsBaseQuery)
            ->whereIn('status', [
                'done',
                'complete',
                'completed',
                'geschlossen',
                'closed',
                'end',
            ])
            ->count();

        $dealIds = (clone $dealsBaseQuery)->pluck('id');

        $dealNotesCount = 0;
        $dealMeasurementsCount = 0;
        $dealDeliveryNotesCount = 0;

        if ($dealIds->isNotEmpty()) {
            $dealNotesCount = DealNote::query()
                ->whereIn('deal_id', $dealIds)
                ->count();

            $dealMeasurementsCount = DealMeasurement::query()
                ->whereIn('deal_id', $dealIds)
                ->count();

            $dealDeliveryNotesCount = DeliveryNote::query()
                ->whereIn('deal_id', $dealIds)
                ->count();
        }

        $dealsTotalPrice = (clone $dealsBaseQuery)->sum('price');
        /*
        |--------------------------------------------------------------------------
        | Termine
        |--------------------------------------------------------------------------
        */
        $appointmentsQuery = MainAppointment::query()
            ->where('customer_id', $customerId)
            ->whereNull('problem_id');

        $appointmentsQuery->where(function ($q) use ($alternativeId, $productId) {
            $q->whereJsonContains('products', $productId)
                ->orWhereJsonContains('products', (string) $productId)
                ->orWhere('products', 'like', '%"' . $productId . '"%')
                ->orWhere('products', 'like', '%[' . $productId . ']%')
                ->orWhere('products', 'like', '%[' . $productId . ',%')
                ->orWhere('products', 'like', '%,' . $productId . ',%')
                ->orWhere('products', 'like', '%,' . $productId . ']%')
                ->orWhere('products', 'like', '%:' . $productId . ']%')
                ->orWhere('products', 'like', '%:' . $productId . ',%');

            $q->orWhere(function ($sub) use ($alternativeId, $productId) {
                $sub->where('products', 'like', '%' . $productId . '%')
                    ->where('products', 'like', '%' . $alternativeId . '%');
            });
        });

        $appointmentsCount = $appointmentsQuery->count();

        if ($appointmentsCount === 0) {
            $appointmentsCount = MainAppointment::query()
                ->where('customer_id', $customerId)
                ->whereNull('problem_id')
                ->where('products', 'like', '%' . $productId . '%')
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | Tickets / Probleme
        |--------------------------------------------------------------------------
        */
        $ticketsBaseQuery = Problem::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId);

        $ticketsCount = (clone $ticketsBaseQuery)->count();

        $openTicketsCount = (clone $ticketsBaseQuery)
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereNotIn('status', [
                        'end',
                        'done',
                        'complete',
                        'completed',
                        'geschlossen',
                        'closed',
                    ]);
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Produktdaten
        |--------------------------------------------------------------------------
        */
        $customerProductsCount = CustomerProductInfo::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Rechnungen
        |--------------------------------------------------------------------------
        */
        $invoiceItemTable = (new InvoiceItem())->getTable();

        $possibleInvoiceProductColumns = [
            'product_id',
            'article_group',
            'article_group_id',
            'article_id',
        ];

        $existingInvoiceProductColumns = collect($possibleInvoiceProductColumns)
            ->filter(fn($column) => Schema::hasColumn($invoiceItemTable, $column))
            ->values()
            ->all();

        $invoicesBaseQuery = Invoice::query()
            ->where('customer_id', $customerId)
            ->where('object_id', $alternativeId);

        if (!empty($existingInvoiceProductColumns)) {
            $invoicesBaseQuery->whereHas('items', function ($itemQuery) use ($existingInvoiceProductColumns, $productId) {
                $itemQuery->where(function ($sub) use ($existingInvoiceProductColumns, $productId) {
                    foreach ($existingInvoiceProductColumns as $index => $column) {
                        if ($index === 0) {
                            $sub->where($column, $productId);
                        } else {
                            $sub->orWhere($column, $productId);
                        }
                    }
                });
            });
        }

        $invoicesCount = (clone $invoicesBaseQuery)->count();

        $invoicePaidCount = (clone $invoicesBaseQuery)
            ->where('status', 'paid')
            ->count();

        $invoiceOpenCount = (clone $invoicesBaseQuery)
            ->whereNotIn('status', ['paid', 'cancelled', 'draft'])
            ->count();

        $invoiceDraftCount = (clone $invoicesBaseQuery)
            ->where('status', 'draft')
            ->count();

        $invoiceCancelledCount = (clone $invoicesBaseQuery)
            ->where('status', 'cancelled')
            ->count();

        $invoiceRowsForTotal = (clone $invoicesBaseQuery)
            ->with(['items:id,invoice_id,line_total'])
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhere('status', '!=', 'cancelled');
            })
            ->get();

        $invoiceTotalAmount = $invoiceRowsForTotal->sum(function ($invoice) {
            $invoiceTotal = (float) ($invoice->total_amount ?? 0);

            if ($invoiceTotal > 0) {
                return $invoiceTotal;
            }

            return (float) $invoice->items->sum(function ($item) {
                return (float) ($item->line_total ?? 0);
            });
        });

        $invoicePaidAmount = $invoiceRowsForTotal->sum(function ($invoice) {
            return (float) ($invoice->paid_amount ?? 0);
        });

        $invoiceOpenAmount = max(0, round($invoiceTotalAmount - $invoicePaidAmount, 2));

        /*
        |--------------------------------------------------------------------------
        | Real Offers / Offer Folders
        |--------------------------------------------------------------------------
        | This is the real count for your "Angebote" sidebar item.
        |--------------------------------------------------------------------------
        */
        $offersBaseQuery = Offer::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId);

        $realOffersCount = (clone $offersBaseQuery)->count();

        $openOffersCount = (clone $offersBaseQuery)
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereNotIn('status', [
                        'cancel',
                        'cancelled',
                        'canceled',
                        'storniert',
                        'rejected',
                        'expired',
                    ]);
            })
            ->count();

        $cancelledOffersCount = (clone $offersBaseQuery)
            ->whereIn('status', [
                'cancel',
                'cancelled',
                'canceled',
                'storniert',
                'rejected',
                'expired',
            ])
            ->count();

        $offerFoldersBaseQuery = OfferFolder::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId);

        $offerFoldersCount = (clone $offerFoldersBaseQuery)->count();

        $activeOfferFoldersCount = (clone $offerFoldersBaseQuery)
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereNotIn('status', [
                        'cancel',
                        'cancelled',
                        'canceled',
                        'storniert',
                        'rejected',
                        'expired',
                    ]);
            })
            ->where(function ($q) {
                $q->whereNull('offer_status')
                    ->orWhereNotIn('offer_status', [
                        'cancel',
                        'cancelled',
                        'canceled',
                        'rejected',
                        'expired',
                    ]);
            })
            ->count();

        $cancelledOfferFoldersCount = (clone $offerFoldersBaseQuery)
            ->where(function ($q) {
                $q->whereIn('status', [
                    'cancel',
                    'cancelled',
                    'canceled',
                    'storniert',
                ])
                    ->orWhereIn('offer_status', [
                        'cancel',
                        'cancelled',
                        'canceled',
                        'rejected',
                        'expired',
                    ]);
            })
            ->count();

        $offerFoldersForTotal = (clone $offerFoldersBaseQuery)
            ->with('detail')
            ->get();

        $offerTotalGross = $offerFoldersForTotal->sum(function ($folder) {
            return (float) ($folder->detail?->total_gross ?? 0);
        });

        $offerTotalNet = $offerFoldersForTotal->sum(function ($folder) {
            return (float) ($folder->detail?->total_net ?? 0);
        });

        /*
        |--------------------------------------------------------------------------
        | LeadProductList / Auftrag, Montage + Old Lead Offer Stage
        |--------------------------------------------------------------------------
        | lead_product_offers is kept separately so it does not overwrite real offers.
        |--------------------------------------------------------------------------
        */
        $leadProducts = LeadProductList::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->get();

        $leadProductOffersCount = $leadProducts->filter(function ($item) {
            $stage = strtolower((string) ($item->stage ?: $item->status));

            return in_array($stage, [
                'offer',
                'angebot',
            ], true);
        })->count();

        $ordersCount = $leadProducts->filter(function ($item) {
            $stage = strtolower((string) ($item->stage ?: $item->status));

            return in_array($stage, [
                'deal',
                'auftrag',
                'accept',
                'accepted',
            ], true);
        })->count();

        $projectsCount = $leadProducts->filter(function ($item) {
            $stage = strtolower((string) ($item->stage ?: $item->status));

            return in_array($stage, [
                'project',
                'montage',
            ], true);
        })->count();

        /*
        |--------------------------------------------------------------------------
        | Checklist
        |--------------------------------------------------------------------------
        */
        $checklistCount = 0;

        if (class_exists(\App\Models\LeadProductChecklistValue::class)) {
            $checklistModel = new \App\Models\LeadProductChecklistValue();
            $checklistTable = $checklistModel->getTable();

            $checklistQuery = \App\Models\LeadProductChecklistValue::query();

            if (Schema::hasColumn($checklistTable, 'customer_id')) {
                $checklistQuery->where('customer_id', $customerId);
            }

            if (Schema::hasColumn($checklistTable, 'alternative_id')) {
                $checklistQuery->where('alternative_id', $alternativeId);
            }

            if (Schema::hasColumn($checklistTable, 'product_id')) {
                $checklistQuery->where('product_id', $productId);
            }

            $checklistCount = $checklistQuery->count();
        }

        /*
        |--------------------------------------------------------------------------
        | History
        |--------------------------------------------------------------------------
        */
        $historyCount = $leadProducts->sum(function ($item) {
            $history = $item->stage_history;

            if (is_array($history)) {
                return count($history);
            }

            if (is_string($history) && trim($history) !== '') {
                $decoded = json_decode($history, true);

                return is_array($decoded) ? count($decoded) : 0;
            }

            return 0;
        });

        /*
        |--------------------------------------------------------------------------
        | Arbeitsprozess / Stages
        |--------------------------------------------------------------------------
        */
        $stagesCount = 0;

        if (class_exists(\App\Models\TaskPhase::class)) {
            $taskPhaseTable = (new \App\Models\TaskPhase())->getTable();

            if (Schema::hasColumn($taskPhaseTable, 'product_id')) {
                $stagesCount = \App\Models\TaskPhase::query()
                    ->where('product_id', $productId)
                    ->count();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Bewertungen / Customer Reviews
        |--------------------------------------------------------------------------
        */
        $reviewsBaseQuery = CustomerReview::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId);

        $reviewsCount = (clone $reviewsBaseQuery)->count();

        $criticalReviewsCount = (clone $reviewsBaseQuery)
            ->where('is_critical', true)
            ->count();

        $reviewsAvgStars = round((float) (clone $reviewsBaseQuery)->avg('stars'), 1);

        /*
        |--------------------------------------------------------------------------
        | Final response
        |--------------------------------------------------------------------------
        */
        return response()->json([
            'success' => true,

            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product_id' => $productId,

            'counts' => [
                /*
                |--------------------------------------------------------------------------
                | Main sidebar counts
                |--------------------------------------------------------------------------
                */
                'documents' => (int) $documentsCount,
                'checklist' => (int) $checklistCount,

                'tasks' => (int) $tasksCount,
                'open_tasks' => (int) $openTasksCount,

                /*
                |--------------------------------------------------------------------------
                | Angebote
                |--------------------------------------------------------------------------
                | "offers" is now real offers table count.
                |--------------------------------------------------------------------------
                */
                'offers' => (int) $realOffersCount,
                'open_offers' => (int) $openOffersCount,
                'cancelled_offers' => (int) $cancelledOffersCount,

                'offer_folders' => (int) $offerFoldersCount,
                'active_offer_folders' => (int) $activeOfferFoldersCount,
                'cancelled_offer_folders' => (int) $cancelledOfferFoldersCount,

                'offer_total_gross' => round((float) $offerTotalGross, 2),
                'offer_total_net' => round((float) $offerTotalNet, 2),

                /*
                |--------------------------------------------------------------------------
                | LeadProductList status counts
                |--------------------------------------------------------------------------
                */
                'lead_product_offers' => (int) $leadProductOffersCount,
                'orders' => (int) $realDealsCount,
                'open_orders' => (int) $openDealsCount,
                'completed_orders' => (int) $completedDealsCount,
                'cancelled_orders' => (int) $cancelledDealsCount,

                'deal_notes' => (int) $dealNotesCount,
                'deal_measurements' => (int) $dealMeasurementsCount,
                'deal_delivery_notes' => (int) $dealDeliveryNotesCount,
                'deal_total_price' => round((float) $dealsTotalPrice, 2),

                'lead_product_orders' => (int) $ordersCount,
                'projects' => (int) $projectsCount,

                /*
                |--------------------------------------------------------------------------
                | Rechnungen
                |--------------------------------------------------------------------------
                */
                'invoices' => (int) $invoicesCount,
                'invoice_paid_count' => (int) $invoicePaidCount,
                'invoice_open_count' => (int) $invoiceOpenCount,
                'invoice_draft_count' => (int) $invoiceDraftCount,
                'invoice_cancelled_count' => (int) $invoiceCancelledCount,

                'invoice_total_amount' => round((float) $invoiceTotalAmount, 2),
                'invoice_paid_amount' => round((float) $invoicePaidAmount, 2),
                'invoice_open_amount' => round((float) $invoiceOpenAmount, 2),

                /*
                |--------------------------------------------------------------------------
                | Other sections
                |--------------------------------------------------------------------------
                */
                'customer_product' => (int) $customerProductsCount,

                'appointments' => (int) $appointmentsCount,

                'tickets' => (int) $ticketsCount,
                'open_tickets' => (int) $openTicketsCount,

                'reviews' => (int) $reviewsCount,
                'critical_reviews' => (int) $criticalReviewsCount,
                'reviews_avg_stars' => (float) $reviewsAvgStars,

                'history' => (int) $historyCount,
                'stages' => (int) $stagesCount,
            ],
        ]);
    }
    public function syncProductInvoicePrice(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['required', 'integer', 'exists:lead_alternative_adds,id'],
            'product_id' => ['required', 'integer', 'exists:article_groups,id'],
        ]);

        $customerId = (int) $data['customer_id'];
        $alternativeId = (int) $data['alternative_id'];
        $productId = (int) $data['product_id'];

        $now = now();
        $userId = auth()->user()->name ?? auth()->id() ?? 0;

        $result = DB::transaction(function () use ($customerId, $alternativeId, $productId, $now, $userId) {
            $invoiceTable = (new Invoice())->getTable();
            $invoiceItemTable = (new InvoiceItem())->getTable();
            $leadProductTable = (new LeadProductList())->getTable();

            /*
            |--------------------------------------------------------------------------
            | Find product column inside invoice_items
            |--------------------------------------------------------------------------
            */
            $possibleProductColumns = [
                'product_id',
                'article_group',
                'article_group_id',
                'article_id',
            ];

            $productColumns = collect($possibleProductColumns)
                ->filter(fn($column) => Schema::hasColumn($invoiceItemTable, $column))
                ->values()
                ->all();

            /*
            |--------------------------------------------------------------------------
            | Find usable amount column inside invoice_items
            |--------------------------------------------------------------------------
            | Important:
            | We calculate from invoice_items first, because this is product-specific.
            |--------------------------------------------------------------------------
            */
            $possibleItemAmountColumns = [
                'total_amount',
                'total_price',
                'gross_amount',
                'amount',
                'line_total',
                'price_total',
                'subtotal',
                'net_amount',
            ];

            $itemAmountColumn = collect($possibleItemAmountColumns)
                ->first(fn($column) => Schema::hasColumn($invoiceItemTable, $column));

            /*
            |--------------------------------------------------------------------------
            | Build invoice item query
            |--------------------------------------------------------------------------
            */
            $itemQuery = DB::table($invoiceItemTable)
                ->join($invoiceTable, $invoiceTable . '.id', '=', $invoiceItemTable . '.invoice_id')
                ->where($invoiceTable . '.customer_id', $customerId)
                ->where($invoiceTable . '.object_id', $alternativeId);

            if (Schema::hasColumn($invoiceTable, 'deleted_at')) {
                $itemQuery->whereNull($invoiceTable . '.deleted_at');
            }

            if (Schema::hasColumn($invoiceItemTable, 'deleted_at')) {
                $itemQuery->whereNull($invoiceItemTable . '.deleted_at');
            }

            if (!empty($productColumns)) {
                $itemQuery->where(function ($q) use ($invoiceItemTable, $productColumns, $productId) {
                    foreach ($productColumns as $index => $column) {
                        $fullColumn = $invoiceItemTable . '.' . $column;

                        if ($index === 0) {
                            $q->where($fullColumn, $productId);
                        } else {
                            $q->orWhere($fullColumn, $productId);
                        }
                    }
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Count matching invoices
            |--------------------------------------------------------------------------
            */
            $invoiceIdsQuery = clone $itemQuery;

            $invoiceIds = $invoiceIdsQuery
                ->distinct()
                ->pluck($invoiceTable . '.id')
                ->filter()
                ->values();

            $invoicesCount = $invoiceIds->count();

            /*
            |--------------------------------------------------------------------------
            | Calculate product invoice total
            |--------------------------------------------------------------------------
            */
            $invoiceTotalAmount = 0.0;

            if ($itemAmountColumn) {
                $invoiceTotalAmount = (float) (clone $itemQuery)
                    ->sum($invoiceItemTable . '.' . $itemAmountColumn);
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback 1:
            | If invoice_items has quantity/unit columns but no total column.
            |--------------------------------------------------------------------------
            */
            if ($invoiceTotalAmount <= 0) {
                $qtyColumn = collect(['qty', 'quantity', 'amount_qty'])
                    ->first(fn($column) => Schema::hasColumn($invoiceItemTable, $column));

                $unitPriceColumn = collect(['unit_price', 'price', 'single_price'])
                    ->first(fn($column) => Schema::hasColumn($invoiceItemTable, $column));

                if ($qtyColumn && $unitPriceColumn) {
                    $rows = (clone $itemQuery)
                        ->select([
                            $invoiceItemTable . '.' . $qtyColumn . ' as qty',
                            $invoiceItemTable . '.' . $unitPriceColumn . ' as unit_price',
                        ])
                        ->get();

                    $invoiceTotalAmount = (float) $rows->sum(function ($row) {
                        return ((float) $row->qty) * ((float) $row->unit_price);
                    });
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback 2:
            | If no item amount was found, use invoice total_amount.
            | This is less exact if one invoice contains multiple products.
            |--------------------------------------------------------------------------
            */
            if ($invoiceTotalAmount <= 0 && $invoiceIds->isNotEmpty()) {
                $invoiceAmountColumn = collect([
                    'total_amount',
                    'gross_amount',
                    'amount',
                    'subtotal',
                    'net_amount',
                ])->first(fn($column) => Schema::hasColumn($invoiceTable, $column));

                if ($invoiceAmountColumn) {
                    $invoiceTotalAmount = (float) DB::table($invoiceTable)
                        ->whereIn('id', $invoiceIds)
                        ->sum($invoiceAmountColumn);
                }
            }

            $invoiceTotalAmount = round((float) $invoiceTotalAmount, 2);

            /*
            |--------------------------------------------------------------------------
            | Find lead_product_lists row
            |--------------------------------------------------------------------------
            */
            $leadProduct = LeadProductList::query()
                ->where('customer_id', $customerId)
                ->where('alternative_id', $alternativeId)
                ->where('product_id', $productId)
                ->first();

            if (!$leadProduct) {
                return [
                    'success' => false,
                    'message' => 'Kein passender Produkteintrag in lead_product_lists gefunden.',
                    'invoice_total_amount' => $invoiceTotalAmount,
                    'invoices_count' => $invoicesCount,
                ];
            }

            $oldPrice = (float) ($leadProduct->price ?? 0);
            $newPrice = $invoiceTotalAmount;

            /*
            |--------------------------------------------------------------------------
            | Stage history
            |--------------------------------------------------------------------------
            */
            $stageHistoryRaw = $leadProduct->stage_history;

            if (is_array($stageHistoryRaw)) {
                $stageHistory = $stageHistoryRaw;
            } elseif (is_string($stageHistoryRaw)) {
                $stageHistory = json_decode($stageHistoryRaw ?: '[]', true) ?: [];
            } else {
                $stageHistory = [];
            }

            $stageHistory[] = [
                'stage' => 'invoice_price_sync',
                'changed_at' => $now->format('Y-m-d H:i:s'),
                'changed_by' => (string) $userId,
                'old_price' => (string) $oldPrice,
                'new_price' => (string) $newPrice,
                'source' => 'invoice_items',
                'invoice_count' => (int) $invoicesCount,
            ];

            /*
            |--------------------------------------------------------------------------
            | Price history
            |--------------------------------------------------------------------------
            */
            $priceHistoryRaw = $leadProduct->price_history;

            if (is_array($priceHistoryRaw)) {
                $priceHistory = $priceHistoryRaw;
            } elseif (is_string($priceHistoryRaw)) {
                $priceHistory = json_decode($priceHistoryRaw ?: '[]', true) ?: [];
            } else {
                $priceHistory = [];
            }

            $priceHistory[] = [
                'changed_at' => $now->format('Y-m-d H:i:s'),
                'changed_by' => (string) $userId,
                'old_price' => (string) $oldPrice,
                'new_price' => (string) $newPrice,
                'source' => 'invoice_sync',
                'invoice_count' => (int) $invoicesCount,
                'customer_id' => $customerId,
                'alternative_id' => $alternativeId,
                'product_id' => $productId,
            ];

            /*
            |--------------------------------------------------------------------------
            | Update lead_product_lists
            |--------------------------------------------------------------------------
            */
            $leadProduct->stage_history = $stageHistory;
            $leadProduct->price_history = $priceHistory;
            $leadProduct->price = (string) $newPrice;
            $leadProduct->price_latest = $now->toDateString();

            if (Schema::hasColumn($leadProductTable, 'gross_amount')) {
                $leadProduct->gross_amount = $newPrice;
            }

            if (Schema::hasColumn($leadProductTable, 'net_amount')) {
                $leadProduct->net_amount = $newPrice;
            }

            if (Schema::hasColumn($leadProductTable, 'tax_amount')) {
                $leadProduct->tax_amount = 0;
            }

            if (Schema::hasColumn($leadProductTable, 'receipt_date')) {
                $leadProduct->receipt_date = $now->toDateString();
            }

            if (Schema::hasColumn($leadProductTable, 'receipt_reference')) {
                $leadProduct->receipt_reference = 'invoice_sync';
            }

            $leadProduct->save();

            /*
            |--------------------------------------------------------------------------
            | Log activity if available
            |--------------------------------------------------------------------------
            */
            if (method_exists($this, 'logActivity')) {
                $this->logActivity(
                    'updated',
                    LeadProductList::class,
                    $leadProduct->id,
                    $leadProduct->customer_id,
                    $leadProduct->alternative_id,
                    $leadProduct->product_id,
                    [
                        'price' => [
                            'from' => $oldPrice,
                            'to' => $newPrice,
                        ],
                        'info' => 'Preis automatisch aus Rechnungssumme aktualisiert.',
                    ]
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Update customer total_purchase
            |--------------------------------------------------------------------------
            */
            $customerTotal = LeadProductList::query()
                ->where('customer_id', $customerId)
                ->when(Schema::hasColumn($leadProductTable, 'deleted_at'), function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->get()
                ->sum(function ($row) {
                    return (float) ($row->price ?? 0);
                });

            $lead = NewLeads::find($customerId);

            $purchaseStatus = null;
            $purchaseDate = null;

            if ($lead) {
                $purchaseStatus = $lead->purchase_status;
                $purchaseDate = $lead->purchase_date;

                if ($customerTotal <= 0) {
                    $purchaseStatus = 'unknown';
                    $purchaseDate = null;
                } else {
                    $purchaseStatus = $purchaseStatus ?: 'on_progress';
                    $purchaseDate = $purchaseDate ?: $now->toDateString();
                }

                $lead->update([
                    'total_purchase' => $customerTotal,
                    'purchase_status' => $purchaseStatus,
                    'purchase_date' => $purchaseDate,
                ]);
            }

            return [
                'success' => true,
                'message' => 'Rechnungssumme wurde aktualisiert.',
                'lead_product_id' => (int) $leadProduct->id,
                'customer_id' => $customerId,
                'alternative_id' => $alternativeId,
                'product_id' => $productId,
                'invoices_count' => (int) $invoicesCount,
                'old_price' => round($oldPrice, 2),
                'price_raw' => round($newPrice, 2),
                'price_formatted' => number_format($newPrice, 2, ',', '.') . ' €',
                'invoice_total_amount' => round($invoiceTotalAmount, 2),
                'invoice_total_amount_formatted' => number_format($invoiceTotalAmount, 2, ',', '.') . ' €',
                'price_latest' => $leadProduct->price_latest,
                'total_purchase_raw' => round((float) $customerTotal, 2),
                'total_purchase_formatted' => number_format((float) $customerTotal, 2, ',', '.') . ' €',
                'purchase_status' => $purchaseStatus,
                'purchase_date' => $purchaseDate,
            ];
        });

        return response()->json($result, !empty($result['success']) ? 200 : 404);
    }

    public function quickOpen(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['required', 'integer', 'exists:lead_alternative_adds,id'],
            'product_id' => ['required', 'integer', 'exists:article_groups,id'],
        ]);

        $customerId = (int) $validated['customer_id'];
        $alternativeId = (int) $validated['alternative_id'];
        $productId = (int) $validated['product_id'];

        $result = DB::transaction(function () use ($customerId, $alternativeId, $productId) {
            $customer = NewLeads::query()->findOrFail($customerId);

            $alternative = LeadAlternativeAdd::query()
                ->where('id', $alternativeId)
                ->where('lead_id', $customerId)
                ->firstOrFail();

            $product = ArticleGroup::query()->findOrFail($productId);

            $leadProduct = LeadProductList::query()
                ->where('customer_id', $customerId)
                ->where('alternative_id', $alternativeId)
                ->where('product_id', $productId)
                ->first();

            if (!$leadProduct) {
                throw ValidationException::withMessages([
                    'product_id' => 'Dieses Produkt ist für diesen Kunden und dieses Objekt nicht verbunden.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 1. Check existing active folder for same customer/object/product
            |--------------------------------------------------------------------------
            */
            $existingFolder = OfferFolder::query()
                ->with('offer')
                ->where('customer_id', $customerId)
                ->where('alternative_id', $alternativeId)
                ->where('product_id', $productId)
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhereNotIn('status', [
                            'cancel',
                            'cancelled',
                            'canceled',
                            'storniert',
                        ]);
                })
                ->where(function ($query) {
                    $query->whereNull('offer_status')
                        ->orWhereNotIn('offer_status', [
                            OfferFolder::OFFER_STATUS_REJECTED,
                            OfferFolder::OFFER_STATUS_EXPIRED,
                            OfferFolder::OFFER_STATUS_CANCELLED,
                        ]);
                })
                ->latest('id')
                ->first();

            if ($existingFolder && $existingFolder->offer) {
                return [
                    'created' => false,
                    'offer' => $existingFolder->offer,
                    'folder' => $existingFolder,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Create offer
            |--------------------------------------------------------------------------
            | Offer model already auto-generates offer_no when blank.
            |--------------------------------------------------------------------------
            */
            $offer = Offer::query()->create([
                'customer_id' => $customerId,
                'alternative_id' => $alternativeId,
                'product_id' => $productId,
                'service_id' => $leadProduct->service_id,
                'department_id' => $leadProduct->department_id,
                'service' => $leadProduct->service,
                'created_by' => auth()->user()?->name,
                'created_for' => $leadProduct->employee_id ?: auth()->user()?->name,
                'status' => 'draft',
                'status_msg' => 'Automatisch aus Kundenkarte erstellt.',
            ]);

            /*
            |--------------------------------------------------------------------------
            | 3. Create offer folder
            |--------------------------------------------------------------------------
            */
            $folderName = $this->makeFolderName($customer, $alternative, $product, $offer);

            $folder = OfferFolder::query()->create([
                'offer_id' => $offer->id,
                'customer_id' => $customerId,
                'alternative_id' => $alternativeId,
                'product_id' => $productId,
                'created_by' => auth()->user()?->name,
                'name' => $folderName,
                'color' => '#93c21c',
                'status' => 'draft',
                'document_status' => OfferFolder::DOCUMENT_STATUS_OFFER,
                'offer_status' => OfferFolder::OFFER_STATUS_DRAFT,
                'deal_status' => OfferFolder::DEAL_STATUS_OPEN,
                'history' => [
                    [
                        'type' => 'created',
                        'label' => 'Angebotsordner automatisch erstellt',
                        'user_id' => auth()->id(),
                        'employee_id' => auth()->user()?->name,
                        'created_at' => now()->toDateTimeString(),
                    ],
                ],
            ]);

            return [
                'created' => true,
                'offer' => $offer,
                'folder' => $folder,
            ];
        });

        $folder = $result['folder'];
        $offer = $result['offer'];

        return response()->json([
            'success' => true,
            'created' => (bool) $result['created'],
            'message' => $result['created']
                ? 'Neues Angebot wurde erstellt.'
                : 'Bestehender Angebotsordner wurde geöffnet.',
            'offer_id' => $offer->id,
            'offer_no' => $offer->offer_no,
            'folder_id' => $folder->id,
            'folder_name' => $folder->name,
            'redirect_url' => url('admin/offers/folders/' . $folder->id . '?new_offer=1'),
        ]);
    }

    private function makeFolderName(NewLeads $customer, LeadAlternativeAdd $alternative, ArticleGroup $product, Offer $offer): string
    {
        $customerName = $customer->display_name ?? ('Kunde #' . $customer->id);
        $objectName = $alternative->object_name ?: $alternative->full_address ?: ('Objekt #' . $alternative->id);
        $productName = $product->article_group ?: ('Produkt #' . $product->id);
        $offerNo = $offer->offer_no ?: ('Angebot #' . $offer->id);

        return $offerNo . ' · ' . $productName . ' · ' . $objectName . ' · ' . $customerName;
    }

    private function normalizeCompanyStage(?string $stage, string $fallback = 'lead'): string
    {
        $s = strtolower(trim((string) $stage));

        if ($s === '' || $s === 'open' || $s === 'new' || $s === 'neu' || $s === 'neue') {
            return $fallback;
        }

        $aliases = [
            'angebot' => 'offer',
            'verkauf' => 'offer',
            'nachfassen' => 'follow_up',
            'followup' => 'follow_up',
            'annehmen' => 'accepted',
            'angenommen' => 'accepted',
            'auftrag' => 'deal',
            'montage' => 'project',
            'projekt' => 'project',
            'abschluss' => 'completed',
            'complete' => 'completed',
            'archiv' => 'archive',
            'reject' => 'junk',
            'rejeck' => 'junk',
            'absage' => 'junk',
        ];

        return $aliases[$s] ?? $s;
    }

    private function companyStageExists(string $stage): bool
    {
        return LeadStage::query()
            ->where('key', $stage)
            ->where('is_active', true)
            ->exists();
    }

    private function currentEmployeeIdForWorkflow(): int|string|null
    {
        return auth()->user()->name ?? auth()->id();
    }

    private function decodeTeamAssignments(mixed $teams): array
    {
        if (is_string($teams)) {
            $decoded = json_decode($teams, true);
            $teams = is_array($decoded) ? $decoded : [];
        }

        if ($teams instanceof \Illuminate\Support\Collection) {
            $teams = $teams->toArray();
        }

        if (!is_array($teams)) {
            return [];
        }

        return collect($teams)
            ->map(function ($row) {
                // Support old saved format: [12, 18, 25]
                if (is_numeric($row)) {
                    $employeeId = (int) $row;

                    return $employeeId > 0 ? [
                        'employee_id' => $employeeId,
                        'scope' => 'company',
                        'stage' => null,
                        'old_stage' => null,
                        'assigned_by' => null,
                        'assigned_at' => null,
                    ] : null;
                }

                if (!is_array($row)) {
                    return null;
                }

                $employeeId = (int) ($row['employee_id'] ?? $row['id'] ?? 0);

                if ($employeeId <= 0) {
                    return null;
                }

                return [
                    'employee_id' => $employeeId,
                    'scope' => (string) ($row['scope'] ?? 'company'),
                    'stage' => isset($row['stage']) && $row['stage'] !== '' ? (string) $row['stage'] : null,
                    'old_stage' => isset($row['old_stage']) && $row['old_stage'] !== '' ? (string) $row['old_stage'] : null,
                    'assigned_by' => !empty($row['assigned_by']) ? (int) $row['assigned_by'] : null,
                    'assigned_at' => $row['assigned_at'] ?? null,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function buildTeamAssignments(array $teamIds, string $scope, string|int $stageKey, string|int|null $oldStage, mixed $assignedBy): array
    {
        return collect($teamIds)
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->map(fn($id) => [
                'employee_id' => $id,
                'scope' => $scope, // company | product
                'stage' => (string) $stageKey,
                'old_stage' => $oldStage ? (string) $oldStage : null,
                'assigned_by' => $assignedBy,
                'assigned_at' => now()->toDateTimeString(),
            ])
            ->values()
            ->all();
    }

    private function mergeTeamAssignments(array $existing, array $new, string $scope, string|int $stageKey): array
    {
        $filtered = collect($existing)
            ->reject(function ($row) use ($scope, $stageKey) {
                return (string) ($row['scope'] ?? 'company') === $scope
                    && (string) ($row['stage'] ?? '') === (string) $stageKey;
            })
            ->values()
            ->all();

        return array_values(array_merge($filtered, $new));
    }

    private function decorateTeamAssignments(array $assignments): array
    {
        $employeeIds = collect($assignments)
            ->pluck('employee_id')
            ->merge(collect($assignments)->pluck('assigned_by'))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $employees = Employee::query()
            ->whereIn('id', $employeeIds)
            ->get(['id', 'name', 'lastname', 'image'])
            ->keyBy('id');

        return collect($assignments)
            ->map(function ($row) use ($employees) {
                $member = $employees->get((int) ($row['employee_id'] ?? 0));
                $assigner = $employees->get((int) ($row['assigned_by'] ?? 0));

                return [
                    'employee_id' => (int) ($row['employee_id'] ?? 0),
                    'scope' => $row['scope'] ?? 'company',
                    'stage' => $row['stage'] ?? null,
                    'old_stage' => $row['old_stage'] ?? null,
                    'assigned_by' => $row['assigned_by'] ?? null,
                    'assigned_at' => $row['assigned_at'] ?? null,
                    'member' => $member,
                    'assigned_by_user' => $assigner,
                ];
            })
            ->filter(fn($row) => !empty($row['member']))
            ->values()
            ->all();
    }

    public function customerProfileWorkflowConfig(Request $request): JsonResponse
    {
        $mode = $request->input('mode', 'company');
        $productId = (int) $request->input('product_id');

        if ($mode === 'product') {
            if ($productId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bitte Produkt auswählen.',
                    'stages' => [],
                ], 422);
            }

            $stages = Stage::query()
                ->with(['section:id,phase_section', 'phases:id,stage_id,phase_name,sort_order'])
                ->where('product_id', $productId)
                ->where('status', 'Published')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn(Stage $stage) => [
                    'type' => 'product',
                    'id' => $stage->id,
                    'key' => 'product_stage_' . $stage->id,
                    'name' => $stage->stage,
                    'product_id' => $stage->product_id,
                    'phase_section_id' => $stage->phase_section_id,
                    'section_name' => $stage->section?->phase_section,
                    'sort_order' => $stage->sort_order,
                    'color' => '#93c21c',
                    'icon' => 'layers',
                    'phases' => $stage->phases
                        ->sortBy('sort_order')
                        ->map(fn($phase) => [
                            'id' => $phase->id,
                            'name' => $phase->phase_name,
                            'sort_order' => $phase->sort_order,
                        ])
                        ->values(),
                ])
                ->values();

            return response()->json([
                'success' => true,
                'mode' => 'product',
                'product_id' => $productId,
                'stages' => $stages,
            ]);
        }

        $stages = LeadStage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn(LeadStage $stage) => [
                'type' => 'company',
                'id' => $stage->id,
                'key' => $stage->key,
                'name' => $stage->name,
                'color' => $stage->color ?: '#93c21c',
                'icon' => $stage->icon ?: 'circle',
                'sort_order' => $stage->sort_order,
                'is_closed' => (bool) $stage->is_closed,
            ])
            ->values();

        return response()->json([
            'success' => true,
            'mode' => 'company',
            'product_id' => null,
            'stages' => $stages,
        ]);
    }

    public function customerProfileMoveStage(Request $request, LeadProductList $leadProduct): JsonResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'string', 'in:company,product'],
            'company_stage_key' => ['nullable', 'string', 'max:100'],
            'product_stage_id' => ['nullable', 'integer', 'exists:stages,id'],
            'product_task_phase_id' => ['nullable', 'integer', 'exists:task_phases,id'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'teams' => ['nullable', 'array'],
            'teams.*' => ['integer', 'exists:employees,id'],
        ]);

        $mode = $data['mode'];
        $reason = trim(strip_tags((string) ($data['reason'] ?? '')));

        $teamIds = collect($data['teams'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $actor = $this->currentEmployeeIdForWorkflow();

        try {
            DB::beginTransaction();

            $lead = LeadProductList::query()
                ->whereKey($leadProduct->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldCompanyStage = $this->normalizeCompanyStage($lead->status);
            $oldProductStageId = $lead->product_stage_id;

            $history = is_array($lead->stage_history) ? $lead->stage_history : [];
            $teams = $this->decodeTeamAssignments($lead->teams);

            if ($mode === 'company') {
                $newStage = $this->normalizeCompanyStage($data['company_stage_key'] ?? null);

                if (!$this->companyStageExists($newStage)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Diese Unternehmensphase existiert nicht oder ist deaktiviert.',
                    ], 422);
                }

                $newTeams = $this->buildTeamAssignments(
                    $teamIds,
                    'company',
                    $newStage,
                    $oldCompanyStage,
                    $actor
                );

                $teams = $this->mergeTeamAssignments($teams, $newTeams, 'company', $newStage);

                $history[] = [
                    'scope' => 'company',
                    'from' => $oldCompanyStage,
                    'to' => $newStage,
                    'stage' => $newStage,
                    'team_ids' => $teamIds,
                    'changed_by' => $actor,
                    'changed_at' => now()->toDateTimeString(),
                    'description' => $reason,
                ];

                $lead->status = $newStage;
            }

            if ($mode === 'product') {
                $productStageId = (int) ($data['product_stage_id'] ?? 0);

                $productStage = Stage::query()
                    ->where('id', $productStageId)
                    ->where('product_id', $lead->product_id)
                    ->where('status', 'Published')
                    ->first();

                if (!$productStage) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Diese Produktphase gehört nicht zu diesem Produkt oder ist nicht veröffentlicht.',
                    ], 422);
                }

                $taskPhaseId = $data['product_task_phase_id'] ?? null;

                if ($taskPhaseId) {
                    $validTaskPhase = TaskPhase::query()
                        ->where('id', $taskPhaseId)
                        ->where('stage_id', $productStage->id)
                        ->exists();

                    if (!$validTaskPhase) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Diese Aufgabe/Phase gehört nicht zur gewählten Produktphase.',
                        ], 422);
                    }
                }

                $newTeams = $this->buildTeamAssignments(
                    $teamIds,
                    'product',
                    $productStage->id,
                    $oldProductStageId,
                    $actor
                );

                $teams = $this->mergeTeamAssignments($teams, $newTeams, 'product', $productStage->id);

                $history[] = [
                    'scope' => 'product',
                    'from_product_stage_id' => $oldProductStageId,
                    'to_product_stage_id' => $productStage->id,
                    'product_stage_name' => $productStage->stage,
                    'product_task_phase_id' => $taskPhaseId,
                    'team_ids' => $teamIds,
                    'changed_by' => $actor,
                    'changed_at' => now()->toDateTimeString(),
                    'description' => $reason,
                ];

                $lead->product_stage_id = $productStage->id;
                $lead->product_task_phase_id = $taskPhaseId;
            }

            $lead->teams = $teams;
            $lead->stage_history = $history;
            $lead->updated_at = now();
            $lead->save();

            DB::commit();

            $decoratedTeams = $this->decorateTeamAssignments($teams);

            return response()->json([
                'success' => true,
                'message' => 'Phase wurde aktualisiert.',

                'lead_product' => [
                    'id' => $lead->id,
                    'status' => $lead->status,
                    'product_stage_id' => $lead->product_stage_id,
                    'product_task_phase_id' => $lead->product_task_phase_id,
                    'teams' => $teams,
                    'team_assignments' => $decoratedTeams,
                ],

                // JS compatibility for Kanban/customer profile scripts
                'lead' => [
                    'id' => $lead->id,
                    'status' => $lead->status,
                    'product_stage_id' => $lead->product_stage_id,
                    'product_task_phase_id' => $lead->product_task_phase_id,
                ],

                'final' => [
                    'team_ids' => $teamIds,
                    'teams' => $teams,
                    'team_assignments' => $decoratedTeams,
                ],

                'stage' => $lead->status,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('customerProfileMoveStage failed', [
                'lead_product_id' => $leadProduct->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Serverfehler beim Ändern der Phase.',
            ], 500);
        }
    }

    public function customerProfileMoveProductStageForward(Request $request, LeadProductList $leadProduct): JsonResponse
    {
        $currentStageId = (int) $leadProduct->product_stage_id;

        $stages = Stage::query()
            ->where('product_id', $leadProduct->product_id)
            ->where('status', 'Published')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($stages->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Produktphasen für dieses Produkt gefunden.',
            ], 404);
        }

        $currentIndex = $stages->search(fn($stage) => (int) $stage->id === $currentStageId);
        $nextStage = $currentIndex === false
            ? $stages->first()
            : $stages->get($currentIndex + 1);

        if (!$nextStage) {
            return response()->json([
                'success' => false,
                'message' => 'Dieses Produkt ist bereits in der letzten Produktphase.',
            ], 422);
        }

        $request->merge([
            'mode' => 'product',
            'product_stage_id' => $nextStage->id,
        ]);

        return $this->customerProfileMoveStage($request, $leadProduct);
    }

    public function stageWorkflowConfig(Request $request): JsonResponse
    {
        $mode = strtolower((string) $request->query('mode', 'company'));

        if ($mode === 'product') {
            $productId = (int) $request->query('product_id');

            if ($productId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'product_id fehlt.',
                    'stages' => [],
                ], 422);
            }

            if (!Schema::hasTable('stages')) {
                return response()->json([
                    'success' => true,
                    'mode' => 'product',
                    'product_id' => $productId,
                    'stages' => [],
                ]);
            }

            $stageColumns = Schema::getColumnListing('stages');
            $hasStageSort = in_array('sort_order', $stageColumns, true);
            $hasStageStatus = in_array('status', $stageColumns, true);
            $hasStageDefault = in_array('default', $stageColumns, true);
            $hasPhaseSection = in_array('phase_section_id', $stageColumns, true);

            $q = DB::table('stages as s')
                ->whereNull('s.deleted_at')
                ->where(function ($w) use ($productId) {
                    $w->where('s.product_id', $productId)
                        ->orWhereNull('s.product_id');
                });

            if ($hasStageStatus) {
                $q->where(function ($w) {
                    $w->whereNull('s.status')
                        ->orWhereIn('s.status', ['Published', 'published', 'Active', 'active']);
                });
            }

            if ($hasPhaseSection && Schema::hasTable('phase_sections')) {
                $q->leftJoin('phase_sections as ps', 'ps.id', '=', 's.phase_section_id');
            }

            $select = [
                's.id',
                's.stage as name',
                's.product_id',
            ];

            $select[] = $hasStageSort
                ? DB::raw('COALESCE(s.sort_order, s.id * 10) as sort_order')
                : DB::raw('s.id * 10 as sort_order');

            $select[] = $hasStageStatus
                ? DB::raw('s.status as status')
                : DB::raw("'Published' as status");

            $select[] = $hasStageDefault
                ? DB::raw('s.`default` as is_default')
                : DB::raw('0 as is_default');

            if ($hasPhaseSection && Schema::hasTable('phase_sections')) {
                $select[] = 's.phase_section_id';
                $select[] = DB::raw('ps.phase_section as section_name');
            } else {
                $select[] = DB::raw('NULL as phase_section_id');
                $select[] = DB::raw('NULL as section_name');
            }

            $q->select($select);

            if ($hasStageSort) {
                $q->orderBy('s.sort_order')->orderBy('s.id');
            } else {
                $q->orderBy('s.id');
            }

            $stages = $q->get()->map(function ($stage) use ($productId) {
                $phases = collect();

                if (Schema::hasTable('task_phases')) {
                    $phaseColumns = Schema::getColumnListing('task_phases');

                    $phaseQ = DB::table('task_phases as tp');

                    if (in_array('deleted_at', $phaseColumns, true)) {
                        $phaseQ->whereNull('tp.deleted_at');
                    }

                    if (in_array('stage_id', $phaseColumns, true)) {
                        $phaseQ->where('tp.stage_id', $stage->id);
                    } elseif (in_array('product_id', $phaseColumns, true)) {
                        $phaseQ->where('tp.product_id', $productId);
                    }

                    $phaseSelect = ['tp.id'];

                    if (in_array('phase_name', $phaseColumns, true)) {
                        $phaseSelect[] = DB::raw('tp.phase_name as name');
                    } elseif (in_array('name', $phaseColumns, true)) {
                        $phaseSelect[] = DB::raw('tp.name as name');
                    } else {
                        $phaseSelect[] = DB::raw("CONCAT('Phase #', tp.id) as name");
                    }

                    if (in_array('sort_order', $phaseColumns, true)) {
                        $phaseSelect[] = DB::raw('COALESCE(tp.sort_order, tp.id * 10) as sort_order');
                        $phaseQ->orderBy('tp.sort_order')->orderBy('tp.id');
                    } else {
                        $phaseSelect[] = DB::raw('tp.id * 10 as sort_order');
                        $phaseQ->orderBy('tp.id');
                    }

                    $phases = $phaseQ->get($phaseSelect);
                }

                return [
                    'id' => (int) $stage->id,
                    'key' => 'product_stage_' . (int) $stage->id,
                    'name' => (string) ($stage->name ?: ('Produktphase #' . $stage->id)),
                    'color' => '#93c21c',
                    'icon' => 'layers',
                    'sort_order' => (int) ($stage->sort_order ?? ($stage->id * 10)),
                    'product_id' => $stage->product_id ? (int) $stage->product_id : null,
                    'phase_section_id' => $stage->phase_section_id ? (int) $stage->phase_section_id : null,
                    'section_name' => $stage->section_name,
                    'phases' => $phases->values(),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'mode' => 'product',
                'product_id' => $productId,
                'stages' => $stages,
            ]);
        }

        $stages = LeadStage::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'key', 'name', 'color', 'icon', 'sort_order', 'is_closed', 'is_active']);

        return response()->json([
            'success' => true,
            'mode' => 'company',
            'stages' => $stages,
        ]);
    }

    /**
     * Saves a company-stage or product-stage move.
     *
     * POST /admin/kanban-stage-workflow/move/{leadProduct}
     */
    public function moveStageWorkflow(Request $request, int $leadProduct): JsonResponse
    {
        $lead = LeadProductList::query()->find($leadProduct);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead-Produkt wurde nicht gefunden.',
            ], 404);
        }

        $mode = strtolower((string) $request->input('mode', $request->input('stage_mode', 'company')));
        $mode = $mode === 'product' ? 'product' : 'company';

        $actorEmployeeId = auth()->user()->name ?? null;
        $reason = trim((string) $request->input('reason', $request->input('description', '')));

        $teamIds = $request->input('teams', []);
        if (!is_array($teamIds)) {
            $teamIds = [];
        }

        $teamIds = array_values(array_unique(array_filter(array_map('intval', $teamIds))));

        $history = is_array($lead->stage_history) ? $lead->stage_history : [];
        $teams = is_array($lead->teams) ? $lead->teams : [];

        if ($mode === 'product') {
            $productStageId = (int) $request->input('product_stage_id');
            $productTaskPhaseId = $request->input('product_task_phase_id') ? (int) $request->input('product_task_phase_id') : null;

            if ($productStageId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bitte Produktphase wählen.',
                ], 422);
            }

            $stageExists = DB::table('stages')
                ->where('id', $productStageId)
                ->whereNull('deleted_at')
                ->exists();

            if (!$stageExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produktphase existiert nicht.',
                ], 422);
            }

            $oldProductStageId = $lead->product_stage_id;

            $history[] = [
                'type' => 'product_stage',
                'from_product_stage_id' => $oldProductStageId,
                'to_product_stage_id' => $productStageId,
                'product_task_phase_id' => $productTaskPhaseId,
                'changed_by' => $actorEmployeeId,
                'changed_user_id' => auth()->id(),
                'changed_employee_id' => $actorEmployeeId,
                'changed_at' => now()->toDateTimeString(),
                'description' => $reason,
            ];

            foreach ($teamIds as $employeeId) {
                $teams[] = [
                    'employee_id' => $employeeId,
                    'stage_mode' => 'product',
                    'stage' => 'product_stage_' . $productStageId,
                    'product_stage_id' => $productStageId,
                    'product_task_phase_id' => $productTaskPhaseId,
                    'old_stage' => $oldProductStageId ? ('product_stage_' . $oldProductStageId) : null,
                    'assigned_by' => $actorEmployeeId,
                    'assigned_at' => now()->toDateTimeString(),
                ];
            }

            $lead->stage_mode = 'product';
            $lead->product_stage_id = $productStageId;
            $lead->product_task_phase_id = $productTaskPhaseId;
            $lead->stage_history = $history;
            $lead->teams = $teams;
            $lead->save();

            return response()->json([
                'success' => true,
                'message' => 'Produktphase wurde gespeichert.',
                'lead_product' => $lead->fresh(),
            ]);
        }

        $stage = strtolower(trim((string) $request->input('company_stage_key', $request->input('stage', 'lead'))));
        $stage = $this->normalizeStage($stage);

        if (!$this->stageExists($stage)) {
            return response()->json([
                'success' => false,
                'message' => 'Unternehmensphase existiert nicht oder ist deaktiviert.',
            ], 422);
        }

        $oldStage = $this->normalizeStage($lead->status);

        $history[] = [
            'type' => 'company_stage',
            'from' => $oldStage,
            'to' => $stage,
            'stage' => $stage,
            'changed_by' => $actorEmployeeId,
            'changed_user_id' => auth()->id(),
            'changed_employee_id' => $actorEmployeeId,
            'changed_at' => now()->toDateTimeString(),
            'description' => $reason,
        ];

        foreach ($teamIds as $employeeId) {
            $teams[] = [
                'employee_id' => $employeeId,
                'stage_mode' => 'company',
                'stage' => $stage,
                'old_stage' => $oldStage,
                'assigned_by' => $actorEmployeeId,
                'assigned_at' => now()->toDateTimeString(),
            ];
        }

        $lead->stage_mode = 'company';
        $lead->old_stage = $oldStage;
        $lead->stage = $stage;
        $lead->status = $stage;
        $lead->stage_history = $history;
        $lead->teams = $teams;
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Unternehmensphase wurde gespeichert.',
            'lead_product' => $lead->fresh(),
        ]);
    }

    /**
     * Moves a lead product one product stage forward.
     */
    public function moveToNextProductStage(Request $request, int $leadProduct): JsonResponse
    {
        $lead = LeadProductList::query()->find($leadProduct);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead-Produkt wurde nicht gefunden.',
            ], 404);
        }

        $stagesResponse = $this->stageWorkflowConfig(new Request([
            'mode' => 'product',
            'product_id' => $lead->product_id,
        ]));

        $data = $stagesResponse->getData(true);
        $stages = collect($data['stages'] ?? []);

        if ($stages->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Produktphasen gefunden.',
            ], 422);
        }

        $currentId = (int) ($lead->product_stage_id ?? 0);
        $ids = $stages->pluck('id')->map(fn($id) => (int) $id)->values()->all();

        $currentIndex = array_search($currentId, $ids, true);
        $nextId = $currentIndex === false
            ? ($ids[0] ?? null)
            : ($ids[$currentIndex + 1] ?? null);

        if (!$nextId) {
            return response()->json([
                'success' => false,
                'message' => 'Dieses Produkt ist bereits in der letzten Produktphase.',
            ], 422);
        }

        $request->merge([
            'mode' => 'product',
            'stage_mode' => 'product',
            'product_stage_id' => $nextId,
        ]);

        return $this->moveStageWorkflow($request, $leadProduct);
    }

    private function hydrateLeadProductTeams($products)
    {
        if (!$products) {
            return $products;
        }

        $collection = $products instanceof \Illuminate\Support\Collection
            ? $products
            : collect($products);

        $collection->each(function ($product) {
            $teams = $this->decodeTeamAssignments($product->teams ?? []);

            $product->teams = $teams;
            $product->team_assignments = $this->decorateTeamAssignments($teams);
            $product->team_members = collect($product->team_assignments)
                ->pluck('member')
                ->filter()
                ->unique('id')
                ->values()
                ->all();
        });

        return $products;
    }

    public function junkObject(Request $request, LeadAlternativeAdd $object): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            DB::transaction(function () use ($object, $validated) {
                $oldStatus = $object->status ?? null;
                $oldStage = $object->stage ?? null;

                $update = [
                    'status' => 'junk',
                    'stage' => 'junk',
                ];

                if (Schema::hasColumn('lead_alternative_adds', 'junk_reason')) {
                    $update['junk_reason'] = $validated['reason'] ?? null;
                }

                if (Schema::hasColumn('lead_alternative_adds', 'junked_by')) {
                    $update['junked_by'] = auth()->user()->name ?? auth()->id();
                }

                if (Schema::hasColumn('lead_alternative_adds', 'junked_at')) {
                    $update['junked_at'] = now();
                }

                $object->update($update);

                LeadProductList::where('customer_id', $object->lead_id)
                    ->where('alternative_id', $object->id)
                    ->update([
                        'stage' => 'junk',
                        'status' => 'junk',
                    ]);

                $this->logActivity(
                    'junk',
                    LeadAlternativeAdd::class,
                    $object->id,
                    $object->lead_id,
                    $object->id,
                    null,
                    [
                        'info' => 'Objekt als Junk markiert',
                        'old_status' => $oldStatus,
                        'old_stage' => $oldStage,
                        'reason' => $validated['reason'] ?? null,
                    ]
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Objekt wurde als Junk markiert.',
                'object_id' => $object->id,
                'customer_id' => $object->lead_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Object junk failed', [
                'object_id' => $object->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Objekt konnte nicht als Junk markiert werden.',
            ], 500);
        }
    }

    public function restoreJunkObject(Request $request, LeadAlternativeAdd $object): JsonResponse
    {
        try {
            DB::transaction(function () use ($object) {
                $object->update([
                    'status' => 'Published',
                    'stage' => 'lead',
                ]);

                LeadProductList::where('customer_id', $object->lead_id)
                    ->where('alternative_id', $object->id)
                    ->where(function ($query) {
                        $query->where('stage', 'junk')
                            ->orWhere('status', 'junk');
                    })
                    ->update([
                        'stage' => 'lead',
                        'status' => 'Lead',
                    ]);

                $this->logActivity(
                    'restore_junk',
                    LeadAlternativeAdd::class,
                    $object->id,
                    $object->lead_id,
                    $object->id,
                    null,
                    ['info' => 'Objekt aus Junk wiederhergestellt']
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Objekt wurde wiederhergestellt.',
                'object_id' => $object->id,
                'customer_id' => $object->lead_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Object restore junk failed', [
                'object_id' => $object->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Objekt konnte nicht wiederhergestellt werden.',
            ], 500);
        }
    }

    public function deleteObject(Request $request, LeadAlternativeAdd $object): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            DB::transaction(function () use ($object, $validated) {
                $customerId = $object->lead_id;
                $objectId = $object->id;

                $this->logActivity(
                    'deleted',
                    LeadAlternativeAdd::class,
                    $objectId,
                    $customerId,
                    $objectId,
                    null,
                    [
                        'info' => 'Objekt gelöscht',
                        'reason' => $validated['reason'] ?? null,
                    ]
                );

                LeadProductList::where('customer_id', $customerId)
                    ->where('alternative_id', $objectId)
                    ->delete();

                NewLeadResponsibility::where('new_lead_id', $customerId)
                    ->where('alternative_id', $objectId)
                    ->delete();

                PVRoof::where('customer_id', $customerId)
                    ->where('alternative_id', $objectId)
                    ->delete();

                Image::where('customer_id', $customerId)
                    ->where('alternative_id', $objectId)
                    ->delete();

                CustomerStage::where('customer_id', $customerId)
                    ->where('alternative_id', $objectId)
                    ->delete();

                if (Schema::hasTable('customer_histories')) {
                    CustomerHistory::where('customer_id', $customerId)
                        ->where('alternative_id', $objectId)
                        ->delete();
                }

                $object->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Objekt wurde gelöscht.',
                'object_id' => $object->id,
                'customer_id' => $object->lead_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Object delete failed', [
                'object_id' => $object->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Objekt konnte nicht gelöscht werden.',
            ], 500);
        }
    }

    public function restoreDeletedObject(Request $request, $object): JsonResponse
    {
        try {
            // Soft-deleted records are excluded from normal queries / route-model-binding,
            // therefore resolve the object explicitly including trashed ones.
            $model = LeadAlternativeAdd::withTrashed()->findOrFail($object);

            DB::transaction(function () use ($model) {
                if ($model->trashed()) {
                    $model->restore();
                }

                $this->logActivity(
                    'restore_deleted',
                    LeadAlternativeAdd::class,
                    $model->id,
                    $model->lead_id,
                    $model->id,
                    null,
                    ['info' => 'Gelöschtes Objekt wiederhergestellt']
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Objekt wurde wiederhergestellt.',
                'object_id' => $model->id,
                'customer_id' => $model->lead_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Object restore deleted failed', [
                'object_id' => $object,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Objekt konnte nicht wiederhergestellt werden.',
            ], 500);
        }
    }

}