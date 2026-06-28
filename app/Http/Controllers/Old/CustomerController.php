<?php

namespace App\Http\Controllers;

use App\Models\BuildingType;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\HeatingType;
use App\Models\Product;
use App\Models\Temperature;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\ArticleGroup;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

public function index()
{
    $search = request()->query('search'); 

    $query = DB::table('customers')
        ->leftJoin('customer_alternative_adds', 'customer_alternative_adds.customer_id', '=', 'customers.id')
        ->join('employees as contact_person', 'contact_person.id', '=', 'customers.contact_person')
        ->join('customer_product_lists as plist', 'plist.customer_id', '=', 'customers.id')
        ->select('customers.*','contact_person.name as c_name', 'contact_person.lastname as c_lastname', 
        'contact_person.image as c_image', 'customer_alternative_adds.street', 'customer_alternative_adds.postcode', 
        'customer_alternative_adds.lat', 'customer_alternative_adds.lon', 'customer_alternative_adds.main', 
        'customer_alternative_adds.address_no', 'plist.product_id')
        ->where('customers.status', '=', 'open');

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('customers.name', 'LIKE', "%$search%")
              ->orWhere('customers.id', 'LIKE', "%$search%")
              ->orWhere('customers.lastname', 'LIKE', "%$search%")
              ->orWhere('customers.date', 'LIKE', "%$search%")
              ->orWhere('customers.street', 'LIKE', "%$search%")
              ->orWhere('customers.postcode', 'LIKE', "%$search%")
              ->orWhere('customers.city', 'LIKE', "%$search%")
              ->orWhere('customers.email', 'LIKE', "%$search%")
              ->orWhere('customers.phone', 'LIKE', "%$search%");
        });
    }

    $data['data'] = $query->paginate(20);
   
    $data['article'] = ArticleGroup::all();
    $data['product_list'] = DB::table('customer_product_lists')
        ->join('article_groups', 'article_groups.id', 'customer_product_lists.product_id')
        ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
        ->select('article_groups.initial', 'customers.id as customer_id', 'article_groups.id as product_id', 'customer_product_lists.status') 
        ->get();

    $customer_product_count = DB::table('customer_product_lists')
        ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
        ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
        ->select('customers.name', 'customers.lastname', 'article_groups.article_group', 'customer_product_lists.*')
        ->get();


    $data['customer_product_count'] = $customer_product_count;
    $open = $customer_product_count->where('status', 'open')->count(); 
    $active = $customer_product_count->where('status', 'active')->count();
    $inactive = $customer_product_count->where('status', 'inactive')->count();
    $ended = $customer_product_count->where('status', 'ended')->count();
    $cancel = $customer_product_count->where('status', 'cancel')->count(); 
    $all = $customer_product_count->count(); 
    $open_per = ($all > 0) ? ($open / $all) * 100 : 0;
    $active_per = ($all > 0) ? ($active / $all) * 100 : 0;
    $inactive_per = ($all > 0) ? ($inactive / $all) * 100 : 0;
    $end_per = ($all > 0) ? ($ended / $all) * 100 : 0;
    $cancel_per = ($all > 0) ? ($cancel / $all) * 100 : 0; 

    $data['counts'] = [
        'open' => $open,
        'active' => $active,
        'inactive' => $inactive,
        'ended' => $ended,
        'cancel' => $cancel,
        'all' => $all,
        'open_per' => $open_per,
        'active_per' => $active_per,
        'inactive_per' => $inactive_per,
        'end_per' => $end_per,
        'cancel_per' => $cancel_per,
    ];

     
  

   $data['tasks'] = DB::table('task_to_dos')
        ->join('customers', 'customers.id', '=', 'task_to_dos.customer_id')
        ->join('task_phases', 'task_phases.id', '=', 'task_to_dos.phase_id')
        ->join('phase_activities', 'phase_activities.id', '=', 'task_to_dos.activities_id')
        ->select('task_to_dos.*', 'phase_activities.title as task_title')
        ->where('task_to_dos.done', '=', 'true')
        ->where('task_to_dos.type', '=', 'main')
        ->whereIn('task_to_dos.id', function($query) {
            $query->select(DB::raw('MAX(id)'))
                ->from('task_to_dos')
                ->where('done', '=', 'true')
                ->groupBy('customer_id');
        })
        ->get();

 
 
  
    return view('admin.customer.customer_details', $data);
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['employee'] = Employee::all();
        $data['product'] = Product::all();

        return view('admin.customer.customer_create', $data);
    }

    public function edit($id)
    {
        $data['data'] = DB::table('customers')
            ->join('employees', 'employees.id', '=', 'customers.contact_person')
            ->select('customers.*', 'employees.name as empname', 'employees.lastname as emplastname')
            ->where('customers.id', '=', $id)
            ->take(1)
            ->get();
        $data['products'] = DB::table('customer_product')
            ->join('customers', 'customers.id', '=', 'customer_product.customer_id')
            ->join('products', 'products.id', '=', 'customer_product.product_id')
            ->where('customer_product.customer_id', '=', $id)
            ->select('customer_product.*', 'products.product')
            ->get();
        $data['product'] = Product::all();
        $data['employee'] = Employee::all();

        return view('admin.customer.customer_edit', $data);

    }
    public function updateinfo(Request $request)
    {

        $validate = $request->validate([
            'name' => 'required',
            'lastname' => 'required',
            'email' => 'nullable',
            'phone' => 'nullable|integer',
            'street' => 'nullable', 
            'postcode' => 'nullable', 
            'city' => 'nullable', 
        ]);
        $id = $_POST['id'];
        $data = Customer::find($id);
        $data->firma = $request->firma;
        $data->title = $request->title;
        $data->name = $request->name;
        $data->lastname = $request->lastname;
        $data->street = $request->street;
        $data->postcode = $request->postcode;
        $data->city = $request->city; 
        $data->phone = $request->phone;
        $data->email = $request->email; 
        $data->lat = $request->latitude;
        $data->lon = $request->longitude; 
        $data->save();
 

        return redirect()->back()->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert!!');

    }

    public function update(Request $request)
    {

        $id = $_POST['id'];
        $data = Customer::find($id);
        $data->firma = $request->firma;
        $data->title = $request->title;
        $data->name = $request->name;
        $data->lastname = $request->lastname;
        $data->street = $request->street;
        $data->postcode = $request->postcode;
        $data->city = $request->city;
        $data->street2 = $request->street2;
        $data->postcode2 = $request->postcode2;
        $data->city2 = $request->city2;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->contact_person = $request->contact_person;
        $data->lat = $request->latitude;
        $data->lon = $request->longitude;
        $data->elevation = $request->elevation;
        $data->source = $request->source;
        $data->save();

        $data->product()->sync($request->input('product_id'), false);

        return redirect()->to('/customer_details')->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert!!');

    }

    public function updatedaten(Request $request){
        
      $id = $request->id;

      $data = Customer::find($id);
      $data->source=$request->source;
      $data->request_date=$request->request_date;
      $data->source_info=$request->source_info;
      $data->document=$request->document;
      $data->date=$request->date;
      $data->consultation = $request->consultation;
      $data->interest_rating = $request->interest_rating;
      $data->seriousness_rating = $request->seriousness_rating;
      $data->price_information_rating = $request->price_information_rating;
      $data->note = $request->note;
      $data->periority = $request->periority;

      $data->save();

      return redirect()->back()->with('save_msg', 'Die Daten wurden erfolgreich gespeichert');

    }

    public function updatedata(Request $request)
{
    // Validate incoming data
    $request->validate([
        'id' => 'required|exists:customers,id',
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
        'roof_direction' => 'nullable|integer',
        'car_kilo' => 'nullable|integer',
    ]);

    // Find the customer record
    $data = Customer::find($request->id);

    // Update customer data using mass assignment
    $data->fill([
        'objective' => $request->objective,
        'house_year' => $request->house_year,
        'number_we' => $request->number_we,
        'number_stories' => $request->number_stories,
        'living_space' => $request->living_space,
        'unusable_space' => $request->unusable_space,
        'number_people' => $request->number_people,
        'roof_type' => $request->roof_type,
        'roof_age' => $request->roof_age,
        'tile_name' => $request->tile_name,
        'heating_system_type' => $request->heating_system_type,
        'heating_system_age' => $request->heating_system_age,
        'heating_system_year' => $request->heating_system_year,
        'heating_type' => $request->heating_type,
        'installation_location' => $request->installation_location,
        'installation_location_extra' => $request->installation_location_extra,
        'annual_consumption' => $request->annual_consumption,
        'annual_heating_energy_consumption' => $request->annual_heating_energy_consumption,
        'annual_heating_energy_consumption_kwh' => $request->annual_heating_energy_consumption_kwh,
        'electric_car' => $request->electric_car,
        'electric_car_plan' => $request->electric_car_plan, 
        'roof_pitch' => $request->roof_pitch,
        'roof_direction' => $request->roof_direction,
        'car_kilo' => $request->car_kilo,
    ]);

    // Save data and handle response
    if ($data->save()) {
        return redirect()->back()->with('save_msg', 'Die Daten wurden erfolgreich gespeichert');
    } else {
        return redirect()->back()->with('error_msg', 'Fehler beim Speichern der Daten. Bitte versuchen Sie es erneut.');
    }
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


      
        $this->validate($request, [
            'name' => 'required',
            'lastname' => 'required',
            'street' => 'required',
            'postcode' => 'required',
            'city' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'contact_person' => 'required',
            'elevation' =>  'required',
            'customer_type' =>  'required',
            'source'   =>   'required'
        ],
            [

                'name' => 'Der Name ist erforderlich',
                'lastname' => 'Der Nachname ist erforderlich',
                'street' => 'Straße ist erforderlich',
                'postcode' => 'Postleitzahl ist erforderlich',
                'city' => 'Die Stadt ist erforderlich',
                'phone' => 'Das Telefon ist erforderlich',
                'email' => 'Die E-Mail ist erforderlich',
                'contact_person' => 'Der Ansprechpartner ist erforderlich',
                'elevation' => 'Bitte geben Sie die richtige Adresse ein',
                'customer_type' => 'Der Kunden Art ist erforderlich',
                'source' => 'Der Qualle ist erforderlich',

            ]);

        $data = new Customer;
        $data->firma = $request->firma;
        $data->customer_type=$request->customer_type;
        $data->title = $request->title;
        $data->name = $request->name;
        $data->lastname = $request->lastname;
        $data->street = $request->street;
        $data->postcode = $request->postcode;
        $data->city = $request->city;
        $data->phone = $request->phone;
        $data->telephone = $request->telephone;
        $data->email = $request->email;
        $data->contact_person = $request->contact_person;
        $data->lat = $request->latitude;
        $data->lon = $request->longitude;
        $data->elevation = $request->elevation;
        $data->polygon_height=$request->polygon_height;
        $data->polygon_width=$request->polygon_width;
        $data->polygon_area=$request->polygon_area;
        $data->request_date=$request->request_date;
        $data->document=$request->document;
        $data->date=$request->date;
        $data->consultation=$request->consultation;
        $data->source = $request->source;
        $data->source_info = $request->inof;
        $data->interest_rating = $request->interest_rating;
        $data->seriousness_rating=$request->seriousness_rating;
        $data->price_information_rating=$request->price_information_rating;
        $data->periority=$request->periority;
        $data->initial_consultation=$request->initial_consultation;
        $data->note=$request->note;
        $data->save();

        $id = Customer::select('id', 'postcode')->latest()->first();
        $result = $id->id;

        return redirect()->to('/customer_product_create/'.$result.'/'.$id->postcode.'/'.'new')->with('save_msg', 'Der Datensatz wurde erfolgreich gespeichert!!');

    }

    /**
     * Display the specified resource.
     */

     public function customer_lists()
    {




    }
    public function show_oldFunction($id, $postcode, $address_no)
    {

        $data['error'] = DB::table('error_problem')
            ->join('errors', 'errors.id', '=', 'error_problem.error_id')
            ->join('problems', 'problems.id', '=', 'error_problem.problem_id')
            ->select('error_problem.*', 'errors.problem_types', 'problems.id')
            ->get();
        $data['problems'] = DB::table('problems')
            ->join('customers', 'customers.id', '=', 'problems.customer_id')
            ->join('products', 'products.id', '=', 'problems.product_id')
            ->join('employees as first', 'first.id', '=', 'problems.first_contact')
            ->where('customers.id', '=', $id)
            ->select('problems.*', 'customers.name', 'customers.lastname', 'customers.city', 'first.name as fname', 'first.lastname as flastname', 'products.product', 'customers.city')
            ->paginate(10);

        $data['building_types'] = BuildingType::where('status', '=', 'Published')->get();
        $data['heating_types'] = HeatingType::where('status', '=', 'Published')->get();

        $data['data'] = DB::table('customers')
            ->join('employees', 'employees.id', '=', 'customers.contact_person')
            ->select('customers.*', 'employees.name as empname', 'employees.lastname as emplastname')
            ->where('customers.id', '=', $id)
            ->first();

        $data['emails'] = DB::table('leads')
            ->join('customers', 'customers.email', '=', 'leads.sender_email')
            ->select('leads.*', 'customers.name', 'customers.lastname')
            ->get();

        // $data['product'] = DB::table('customer_products')
        //     ->join('customers', 'customers.id', '=', 'customer_products.customer_id')
        //     ->join('article_groups', 'article_groups.id', '=', 'customer_products.product_id')
        //     ->select('article_groups.article_group' , 'customer_products.*')
        //     ->where('customers.id', '=', $id)
        //     ->get();

        return view('admin.customer.customer', $data);

    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Customer::find($id);
        $data->delete();

        return back()->with('delete_msg', 'Der Datensatz von '.$data->name.' '.$data->lastname.' wurde erfolgreich gelöscht');
    }

    public function product_list($id)
    {
        $data = DB::table('customer_product_lists')
            ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
            ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
            ->select('article_groups.article_group', 'article_groups.id')
            ->where('customers.id', '=', $id)
            ->get();

         return response()->json($data, 200);
    }



    public function details_update(request $request)
    {

        $id = request()->input('id');
        $postcode = Customer::find($id)->select('postcode')->value('postcode');
        $nat = Temperature::where('postcode', '=', $postcode)->select('outside_temp')->value('outside_temp');

                // Calculating the Specific efficiency
        $current_year = \Carbon\Carbon::parse(now())->isoFormat('YYYY');

        $constraction = $current_year - $request->construction_year;
        $constraction_heating = $current_year - $request->heating_manufacture_year;
        $constructionYear = $request->construction_year;
        $living_space = $request->living_space;
        $consumption= $request->consumption;
        $wwb_pro_person = 40; // WWB am Tag pro Person Liter
        $wwb = ($wwb_pro_person*365*40*4.182/1000)/3.6;
        $hl_wwb_pro_person = $wwb/365;

        $story = 4; //Geschoße Livable Story - 1 :
        if($story == 0){
            $story = 1;
        }
        else{
            $story = 4;
        }


        $floor_height = 3; //Etagne Höhe
        $window_share = 0.25; //FensterAnteil
        $scope =  sqrt($living_space / ($story - 1)) * 4;  //Umfang: =SQRT(living_space/(story-1))*4
        $width =  $scope/4; // Breite
        $wall_surface = $scope * $floor_height * ($story -1);  //Wandfläche:
        $window_area = $living_space * $window_share; //Fensterfläch
        $floor_area = $living_space / $story; //Bodenfläche
        $roof_area = ($width / 2) / cos(30 * pi() / 180) * $width * 2; //Dachfläche: (K19/2)/COS(30*3,14/180)*K19*2

        // Matrix of building Data;
        $buildingData = DB::table('building_data')->get();

        // Convert the collection to an array
        $building_data = $buildingData->map(function ($item) {
            return (array) $item;
        })->toArray();
        $nearestBuildingData = $this->findNearestYear($constructionYear, $building_data);

       // Vlookup of Roof area based on the Building Matrix
       $roof_area_lookup = collect([$nearestBuildingData])->map(function($building) {
            $decodedValue = json_decode($building['u_dach'], true);
                return $decodedValue;
        })->first();


        // Vlookup of Floor area based on the Building Matrix
        $floor_area_lookup = collect([$nearestBuildingData])->map(function($building) {
            $decodedValue = json_decode($building['u_boden'], true);
                return $decodedValue;
        })->first();

           // Vlookup of Fenster U based on the Building Matrix
        $window_u_lookup = collect([$nearestBuildingData])->map(function($building) {
            $decodedValue = json_decode($building['u_fenster'], true);
                return $decodedValue;
        })->first();

             // Vlookup of Wand U based on the Building Matrix
        $wall_u_lookup = collect([$nearestBuildingData])->map(function($building) {
            $decodedValue = json_decode($building['u_wand'], true);
                return $decodedValue;
        })->first();


    // Heizlast R
    $heating_load_right = (
                            ($wall_surface * $wall_u_lookup) +
                            ($window_area * $window_u_lookup) +
                            ($roof_area * $roof_area_lookup) +
                            ($floor_area * $floor_area_lookup) ) * (20 - $nat) / 1000;
    $heating_load_right_r = round($heating_load_right,1);
 // Heizlast Left
    $heating_load_left = (13.25 * $living_space)/1000;
    $heating_load_left_r = round($heating_load_left,2);
// Total Heizlast
    $heating_load_c = round($heating_load_right_r + $heating_load_left_r + $hl_wwb_pro_person,2);


        //Calculating the heating Load
            $buildings = DB::table('building_types')
                ->join('building_type_values', 'building_type_values.building_type_id', '=', 'building_types.id')
                ->orderByRaw('ABS(building_types.start_year - ?)', [$constructionYear])
                ->select('building_type_values.size', 'building_type_values.value', 'building_types.*')
                ->get();

            // Calculate the nearest building according the size of Living Space
             $nearestBuilding = $buildings->sortBy(function ($building) use ($living_space) {
                            return abs($building->size - $living_space);
                            })->first();

           // Calculate efficiency automatically based on construction year
        $efficiency = $this->calculateEfficiency($nearestBuilding->start_year);

        // Amers Calculation
        // HBL with Worm Water,
        // Value KW * Living Space * 1 / 1000 (Convert to KW) * 20% Warm Wather * number of people
        $heating_load = $nearestBuilding->value * $request->living_space * 1 / 1000 + 0.2 * $request->number_people;
            // Calculate the total heating consumption

        $heating_load_2 =$nearestBuilding->size * $nearestBuilding->value /1000;


       // if Number 1
        $heating_load_3 = $consumption  * $efficiency / 2000;
    // Calculate the total heating consumption
        $totalConsumption =$nearestBuilding->size * $nearestBuilding->value;

    // Adjust for efficiency
        $adjustedConsumption = $totalConsumption / $efficiency;

    // Assuming annual heating hours to be around 2000-2500
        $annualHeatingHours = 2000;

    // Calculate the heating load
        $heatingLoad = $adjustedConsumption / $annualHeatingHours;
        $heating_type_view = DB::table('heating_types')->where('id', $request->heating_type)->select('value')->value('value');

        $efficiency_heating_type = $heating_type_view / -30;

        // Calculating the Tank Size

        $total_efficiency = $efficiency_heating_type * $constraction + 0.9;
        $total_efficiency_hlb = $efficiency_heating_type * $constraction_heating + 0.9;

        //Calcuation of Heating OUtput HLB
        $hlb = $total_efficiency_hlb * $request->consumption / 2000;

        $data = Customer::find($id);
        $data->number_people = $request->number_people;
        $data->building_type = $request->building_type;
        $data->living_space = $request->living_space;
        $data->construction_year = $request->construction_year;
        $data->heating_type = $request->heating_type;
        $data->consumption = $request->consumption;
        if ($request->underfloor_heating == 'Yes') {
            $data->underfloor_heating = 1;
        } else {
            $data->underfloor_heating = 0;
        }

        if ($request->radiator == 'Yes') {
            $data->radiator = 1;
        } else {
            $data->radiator = 0;
        }
        $data->heating_manufacture_year = $request->heating_manufacture_year;
        $data->heating_load = $heating_load;
        $data->efficiency = $total_efficiency;
        $data->heating_output = $hlb;

        $data->save();

        return redirect()->back()->with('save_msg', 'The record saved');

    }

    private function calculateEfficiency($year)
    {
        if ($year < 1977) {
            return 0.6; // older buildings have lower efficiency
        } elseif ($year <= 1983) {
            return 0.7;
        } elseif ($year <= 1994) {
            return 0.75;
        } elseif ($year <= 2001) {
            return 0.8;
        } elseif ($year <= 2012) {
            return 0.85;
        } else {
            return 0.9; // newer buildings have higher efficiency
        }
    }


    function findNearestYear($targetYear, $data) {
            $closest = null;
            $closestDiff = PHP_INT_MAX;

            foreach ($data as $row) {
                $year = (int)$row['year'];
                $diff = abs($targetYear - $year);

                if ($diff < $closestDiff) {
                    $closest = $row;
                    $closestDiff = $diff;
                }
            }

            return $closest;
        }

        public function screenshot(Request $request)
    {
        // Decode the image data
        $imageData = $request->input('image');
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $image = base64_decode($imageData);

        // Generate a unique filename
        $fileName = Str::random(10) . '.png';

        // Define the path to save the image
        $path = public_path('images/customer/home/' . $fileName);

        // Ensure the directory exists
        if (!File::exists(public_path('images/customer/home'))) {
            File::makeDirectory(public_path('images/customer/home'), 0755, true);
        }

        // Save the image to the path
        File::put($path, $image);

        return response()->json(['success' => true, 'file' => $fileName]);
    }


    public function myLeads(){

        $search = request()->query('search');
            $isAjax = request()->query('is_ajax', false);

            $query = DB::table('customers')
                ->join('customer_alternative_adds', 'customer_alternative_adds.customer_id', '=', 'customers.id')
                ->where('customers.contact_person', '=', )
                ->select('customers.*', 'customer_alternative_adds.street', 'customer_alternative_adds.postcode', 'customer_alternative_adds.lat', 'customer_alternative_adds.lon', 'customer_alternative_adds.main', 'customer_alternative_adds.address_no');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('customers.name', 'LIKE', "%$search%")
                    ->orWhere('customers.lastname', 'LIKE', "%$search%")
                    ->orWhere('customers.date', 'LIKE', "%$search%")
                    ->orWhere('customers.street', 'LIKE', "%$search%")
                    ->orWhere('customers.city', 'LIKE', "%$search%")
                    ->orWhere('customers.email', 'LIKE', "%$search%")
                    ->orWhere('customers.phone', 'LIKE', "%$search%");
                });
            }

            $data['data'] = $query->paginate(20);

            $data['article'] = ArticleGroup::all();
            $data['product_list'] = DB::table('customer_product_lists')
                ->join('article_groups', 'article_groups.id', 'customer_product_lists.product_id')
                ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
                ->select('article_groups.initial', 'customers.id as customer_id', 'article_groups.id as product_id', 'customer_product_lists.status')
                ->get();

            $customer_product_count = DB::table('customer_product_lists')
                ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
                ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
                ->select('customers.name', 'customers.lastname', 'article_groups.article_group', 'customer_product_lists.*')
                ->get();


            $data['customer_product_count'] = $customer_product_count;
            $open = $customer_product_count->where('status', 'open')->count(); 
            $active = $customer_product_count->where('status', 'active')->count();
            $inactive = $customer_product_count->where('status', 'inactive')->count();
            $ended = $customer_product_count->where('status', 'ended')->count();
            $cancel = $customer_product_count->where('status', 'cancel')->count(); 
            $all = $customer_product_count->count(); 
            $open_per = ($all > 0) ? ($open / $all) * 100 : 0;
            $active_per = ($all > 0) ? ($active / $all) * 100 : 0;
            $inactive_per = ($all > 0) ? ($inactive / $all) * 100 : 0;
            $end_per = ($all > 0) ? ($ended / $all) * 100 : 0;
            $cancel_per = ($all > 0) ? ($cancel / $all) * 100 : 0; 

            $data['counts'] = [
                'open' => $open,
                'active' => $active,
                'inactive' => $inactive,
                'ended' => $ended,
                'cancel' => $cancel,
                'all' => $all,
                'open_per' => $open_per,
                'active_per' => $active_per,
                'inactive_per' => $inactive_per,
                'end_per' => $end_per,
                'cancel_per' => $cancel_per,
            ];

            if ($isAjax) {
                return view('admin.customer.partials.customer_details_results', $data)->render();
            }

            return view('admin.customer.my_leads', $data);

    }


   public function my_customer($user)
{
     $search = request()->query('search');
    $isAjax = request()->query('is_ajax', false);

    $query = DB::table('customers')
        ->leftJoin('customer_alternative_adds', 'customer_alternative_adds.customer_id', '=', 'customers.id')
        ->join('employees as contact_person', 'contact_person.id', '=', 'customers.contact_person')
        ->select('customers.*','contact_person.name as c_name', 'contact_person.lastname as c_lastname', 'contact_person.image as c_image', 'customer_alternative_adds.street', 'customer_alternative_adds.postcode', 'customer_alternative_adds.lat', 'customer_alternative_adds.lon', 'customer_alternative_adds.main', 'customer_alternative_adds.address_no')
        ->where('customers.contact_person', '=', $user);

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('customers.name', 'LIKE', "%$search%")
              ->orWhere('customers.id', 'LIKE', "%$search%")
              ->orWhere('customers.lastname', 'LIKE', "%$search%")
              ->orWhere('customers.date', 'LIKE', "%$search%")
              ->orWhere('customers.street', 'LIKE', "%$search%")
              ->orWhere('customers.city', 'LIKE', "%$search%")
              ->orWhere('customers.email', 'LIKE', "%$search%")
              ->orWhere('customers.phone', 'LIKE', "%$search%");
        });
    }

    $data['data'] = $query->paginate(20);
    
    $data['article'] = ArticleGroup::all();
    $data['product_list'] = DB::table('customer_product_lists')
        ->join('article_groups', 'article_groups.id', 'customer_product_lists.product_id')
        ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
        ->select('article_groups.initial', 'customers.id as customer_id', 'article_groups.id as product_id', 'customer_product_lists.status')
        ->get();

    $customer_product_count = DB::table('customer_product_lists')
        ->join('customers', 'customers.id', '=', 'customer_product_lists.customer_id')
        ->join('article_groups', 'article_groups.id', '=', 'customer_product_lists.product_id')
        ->select('customers.name', 'customers.lastname', 'article_groups.article_group', 'customer_product_lists.*')
        ->get();


    $data['customer_product_count'] = $customer_product_count;
    $open = $customer_product_count->where('status', 'open')->count(); 
    $active = $customer_product_count->where('status', 'active')->count();
    $inactive = $customer_product_count->where('status', 'inactive')->count();
    $ended = $customer_product_count->where('status', 'ended')->count();
    $cancel = $customer_product_count->where('status', 'cancel')->count(); 
    $all = $customer_product_count->count(); 
    $open_per = ($all > 0) ? ($open / $all) * 100 : 0;
    $active_per = ($all > 0) ? ($active / $all) * 100 : 0;
    $inactive_per = ($all > 0) ? ($inactive / $all) * 100 : 0;
    $end_per = ($all > 0) ? ($ended / $all) * 100 : 0;
    $cancel_per = ($all > 0) ? ($cancel / $all) * 100 : 0; 

    $data['counts'] = [
        'open' => $open,
        'active' => $active,
        'inactive' => $inactive,
        'ended' => $ended,
        'cancel' => $cancel,
        'all' => $all,
        'open_per' => $open_per,
        'active_per' => $active_per,
        'inactive_per' => $inactive_per,
        'end_per' => $end_per,
        'cancel_per' => $cancel_per,
    ];

    if ($isAjax) {
        return view('admin.customer.partials.customer_details_results', $data)->render();
    }

  
    return view('admin.customer.customer_details', $data);
}



}
