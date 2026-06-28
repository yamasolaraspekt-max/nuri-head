<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;


use App\Models\Branch;
use App\Models\Problem;
use App\Models\Product;
use App\Models\UserRoll;
use Illuminate\Http\Request;
use App\Models\Invoice;
use DB; 



class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {
  
        $data['premission']=UserRoll::all();
        $data['branch']=Branch::all();
        $data['problems_no']=Problem::where('status', '=', 'offen')->count();
        $users=DB::table('users')->select('name')->pluck('name');
        $data['employees']=DB::table('employees')
                    ->join('branches', 'branches.id', 'employees.branch')
                    ->select('employees.*', 'branches.branch')
                    ->get();
        $data['errors']=DB::table('error_problem')
        ->join('errors', 'errors.id', '=', 'error_problem.error_id')
        ->join('problems', 'problems.id', '=', 'error_problem.problem_id')
        ->select('error_problem.*','errors.problem_types','problems.id')
        ->get();
        $data['products']=Product::all();
        $data['responsible']=DB::table('employee_problem')
        ->join('employees', 'employees.id', '=', 'employee_problem.employee_id')
        ->join('problems', 'problems.id', '=', 'employee_problem.problem_id')
        ->whereIn('employees.id', $users)
        ->select('employee_problem.*','employees.name as rname', 'employees.lastname as rlastname', 'employees.image as rimage')
        ->get();
        $data['problems']=DB::table('employee_problem')
        ->join('problems', 'problems.id', '=', 'employee_problem.problem_id')
        ->leftJoin('employees as fcontact', 'fcontact.id', '=', 'problems.first_contact')
        ->join('employees as responsible', 'responsible.id', '=', 'employee_problem.employee_id')
        ->leftJoin('customers', 'problems.customer_id', '=', 'customers.id')
        ->where('responsible.id', '=', auth()->user()->name)
        ->select('customers.name as cname', 'customers.lastname as clastname', 'responsible.name as rname', 'responsible.lastname as rlastname', 'responsible.image as rimage', 'problems.*',
                'fcontact.name as fname', 'fcontact.lastname as flastname', 'fcontact.image as fimage')
        ->get();

        $data['all_problems']=DB::table('employee_problem')
        ->join('problems', 'problems.id', '=', 'employee_problem.problem_id')
        ->leftJoin('employees as fcontact', 'fcontact.id', '=', 'problems.first_contact')
        ->join('employees as responsible', 'responsible.id', '=', 'employee_problem.employee_id')
        ->leftJoin('customers', 'problems.customer_id', '=', 'customers.id')
        ->select('customers.name as cname', 'customers.lastname as clastname', 'responsible.name as rname', 'responsible.lastname as rlastname', 'responsible.image as rimage', 'problems.*',
                'fcontact.name as fname', 'fcontact.lastname as flastname', 'fcontact.image as fimage')
        ->get();


        $data['invoices']=DB::table('invoices')
        ->join('brands', 'brands.id', 'invoices.company')
        ->join('employees as purchaser', 'purchaser.id', '=','invoices.purchased_by')
        ->join('employees as editor', 'editor.id', '=','invoices.edited_by')
        ->where('purchaser.id', '=', auth()->user()->name)
        ->select('invoices.*', 'brands.name', 'brands.id as bid', 'purchaser.name as pname', 'purchaser.lastname as plastname', 'purchaser.image as pimage', 'editor.name as ename', 'editor.lastname as elastname', 'editor.image as eimage')
        ->paginate(10);

        $data['customer']=DB::table('customers')
        ->join('invoices', 'invoices.customer_id', '=', 'customers.id')
        ->select('customers.name as customer_name', 'customers.lastname as customer_lastname', 'customers.id as cid', 'invoices.invoice_no as invoice_id')
        ->get();
        $data['employee']=DB::table('employees')
            ->join('invoices', 'invoices.employee_id', '=', 'employees.id')
            ->select('employees.name as employee_name', 'employees.lastname as employee_lastname', 'employees.id as emp_id' , 'invoices.invoice_no as invoice_id')
            ->get();

        $data['note_category'] = DB::table('note_categories')
                                ->where('user', auth()->user()->name)
                                ->whereNull('deleted_at')
                                ->get();


        return view('admin.dashbaord.dashbaord', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function draf_view($id)
    {
        $data=Invoice::find($id);
        return view('admin.invoice.invoice_draft_view')->with('data', $data);

    }

    public function showDetails(Request $request)
    {
              // Fetch IP Address
        $ip = $request->ip(); 

        // Fetch IP Location Data
        $locationResponse = Http::get("https://api.iplocation.net/?ip=".$ip);
        $locationData = $locationResponse->json();

        // Collect Browser and System Info
        $browserInfo = [
            'IP Address'        => $ip,
            'Country'           => $locationData['country_name'] ?? 'Not Available',
            'Region'            => $locationData['region'] ?? 'Not Available',
            'City'              => $locationData['city'] ?? 'Not Available',
            'Platform'          => php_uname('s'),
            'User Agent'        => $request->header('User-Agent'),
            'Language'          => $request->header('Accept-Language'),
            'Cookies Enabled'   => isset($_COOKIE) ? 'Yes' : 'No',
            'Screen Width'      => "<script>document.write(window.innerWidth)</script>",
            'Screen Height'     => "<script>document.write(window.innerHeight)</script>",
        ];

        // Return Response (As JSON for Testing)
        return response()->json($browserInfo);
    }
    function getWeatherData()
    {
        $apiKey = config('services.openweather.key');

        // Get user's IP address
        $ip = request()->ip();

        // Geolocation API to determine city
        $geoResponse = Http::get("https://ipapi.co/{$ip}/json/");
        $geoData = $geoResponse->json();

        // Use default coordinates if geolocation fails
        $latitude = $geoData['latitude'] ?? 50.2841; // Default latitude for Wehrheim
        $longitude = $geoData['longitude'] ?? 8.5658; // Default longitude for Wehrheim

        // Fetch weather data from OpenWeatherMap
        $weatherResponse = Http::get("https://api.openweathermap.org/data/2.5/onecall", [
            'lat' => $latitude,
            'lon' => $longitude,
            'exclude' => 'minutely,alerts',
            'units' => 'metric',
            'appid' => $apiKey,
        ]);

        return $weatherResponse->json();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function notAdmin()
    {     
   
        return view('error.notAuth');
    }

    public function demo(){

        return view('admin.product.product');
    }

public function timeline(){
    $tasks = [
        (object)[
            'type' => 'customer',
            'date' => '2.2.224',
            'title' => 'Anfrage from Mathias',
            'description' => 'Customer has inst... in 3 products. ...'
        ],
        (object)[
            'type' => 'customer',
            'date' => '20.25.24',
            'title' => 'Angebot send',
            'description' => 'First Draft of Angebot'
        ], (object)[
            'type' => 'customer',
            'date' => '20.25.24',
            'title' => 'Mr.Yama Talked with Customer',
            'description' => 'Anfrage: 234as;dfja sdlfasdfasdf asdf asd'
        ],
           (object)[
            'type' => 'customer',
            'date' => '20.25.24',
            'title' => 'Ms Kathrin',
            'description' => 'File Report'
        ],
        (object)[
            'type' => 'seller',
            'date' => 'Done',
            'title' => 'Question 2',
            'description' => 'Customer inquired about return policy.'
        ],

        (object)[
            'type' => 'saller',
            'date' => '2024',
            'title' => 'Question 4',
            'description' => 'Customer inquired about return policy. Customer inquired about return policy. Customer inquired about return policy. Customer inquired about return policy. Customer inquired about return policy.'
        ],
        (object)[
            'type' => 'seller',
            'date' => '2007',
            'title' => 'Answer 1',
            'description' => 'Seller responded with shipping details.'
        ],

           (object)[
            'type' => 'customer',
            'date' => '2402',
            'title' => 'Answer 1',
            'description' => 'The response of customer'
        ],
          (object)[
            'type' => 'seller',
            'date' => '456465',
            'title' => 'Answer 2',
            'description' => 'The response of customer'
        ],
         (object)[
            'type' => 'customer',
            'date' => '20.25.24',
            'title' => 'Angebot send',
            'description' => 'First Draft of Angebot'
        ],
        (object)[
            'type' => 'seller',
            'date' => '2012',
            'title' => 'Question 2',
            'description' => 'Customer inquired about return policy.'
        ],

        (object)[
            'type' => 'saller',
            'date' => '2024',
            'title' => 'Question 4',
            'description' => 'Customer inquired about return policy. Customer inquired about return policy. Customer inquired about return policy. Customer inquired about return policy. Customer inquired about return policy.'
        ],
        (object)[
            'type' => 'seller',
            'date' => '2007',
            'title' => 'Answer 1',
            'description' => 'Seller responded with shipping details.'
        ],

           (object)[
            'type' => 'customer',
            'date' => '2402',
            'title' => 'Answer 1',
            'description' => 'The response of customer'
        ],
          (object)[
            'type' => 'customer',
            'date' => '456465',
            'title' => 'Answer 2',
            'description' => 'The response of customer'
        ],
    ];

   return view('admin.timeline.project.timeline', ['tasks' => $tasks]);

}


}
