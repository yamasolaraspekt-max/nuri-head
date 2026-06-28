<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSet;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Models\ProductMasterSet;
use App\Models\EmployeeActivitySet;
use DB;

class EmployeeSetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public function index($master, $phase)
    {
        $search = request()->query('search');
    
        // Common data used in both cases
        $data['title'] = DB::table('product_master_sets')
            ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->join('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
            ->select(
                'product_master_sets.id as master_id',
                'sub_article_groups.sub_article',
                'article_groups.article_group',
                'product_master_sets.setname',
                'article_groups.id as article_group_id',
                'sub_article_groups.id as sub_id'
            )
            ->where('product_master_sets.id', $master)
            ->first();
    
        $data['skills'] = DB::table('employees')
            ->join('department_positions', 'department_positions.employee_id', '=', 'employees.id')
            ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
            ->join('skills', 'skills.emp_id', '=', 'employees.id')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'skills.product_id')
            ->select(
                'skills.*',
                'employees.name as emp_lastname',
                'employees.lastname as emp_lastname',
                'positions.position',
                'employees.id as emp_id',
                'article_groups.article_group'
            )
            ->get();
    
        $data['activity'] = DB::table('employee_activity_sets')
            ->join('employee_sets', 'employee_sets.id', '=', 'employee_activity_sets.employee_set_id')
            ->join('product_master_sets', 'product_master_sets.id', '=', 'employee_activity_sets.master_set_id')
            ->join('task_phases', 'task_phases.id', '=', 'employee_activity_sets.phase_id')
            ->join('phase_activities', 'phase_activities.id', '=', 'employee_activity_sets.activity_id')
            ->select(
                'employee_activity_sets.*',
                'phase_activities.title',
                'phase_activities.description',
                'phase_activities.status'
            )
            ->where('phase_activities.status', '=', 'Published')
            ->get();

        $data['positions']= Position::all(); 
     
        // Main list with optional search filter
        $query = DB::table('employee_sets')
            ->join('positions', 'positions.id', '=', 'employee_sets.position_id')
            ->where('employee_sets.master_set_id', $master)
            ->select('employee_sets.*', 'positions.position');
    
        if ($search) {
            $query->where('positions.position', 'LIKE', "%{$search}%");
        }
    
        $data['data'] = $query->paginate(10);
    
        return view('admin.offer.set.employee.employee', $data);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $master, $phase)
    {
        $search = request()->query('search');

        if($search){
            $data['title']=DB::table('product_master_sets')
            ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->join('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
            ->select('product_master_sets.id as master_id','sub_article_groups.sub_article', 'article_groups.article_group','product_master_sets.setname', 'article_groups.id as article_group_id', 'sub_article_groups.id as sub_id')
            ->where('product_master_sets.id', $master)
            ->first();
            $data['position']= Position::all(); 


            $data['skills'] = DB::table('employees')
                ->leftJoin('department_positions', 'department_positions.employee_id', '=', 'employees.id')
                ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
                ->leftJoin('skills', 'skills.emp_id', '=', 'employees.id')
                ->leftJoin('article_groups', 'article_groups.id', '=', 'skills.product_id')
                ->select('skills.*', 'employees.name as emp_lastname', 'employees.salary_per_hour','positions.id as position_id','employees.lastname as emp_lastname', 
                        'positions.position', 'employees.id as emp_id', 'article_groups.article_group')
             
                ->paginate(20);
            $data['activity'] = DB::table('phase_activities')
            ->join('task_phases', 'task_phases.id', '=', 'phase_activities.phase_id') 
            ->where('task_phases.id', $phase)
            ->select('phase_activities.*')
            ->get();
            return view('admin.offer.set.employee.create_employee_set', $data);
                    
        }
        else{
            $data['title']=DB::table('product_master_sets')
            ->join('article_groups', 'article_groups.id', '=', 'product_master_sets.article_group')
            ->join('sub_article_groups', 'sub_article_groups.id', '=', 'product_master_sets.sub_article')
            ->select('product_master_sets.id as master_id','sub_article_groups.sub_article', 'article_groups.article_group','product_master_sets.setname', 'article_groups.id as article_group_id', 'sub_article_groups.id as sub_id')
            ->where('product_master_sets.id', $master)
            ->first();

            $data['position']= Position::all();

            $data['activity'] = DB::table('phase_activities')
            ->join('task_phases', 'task_phases.id', '=', 'phase_activities.phase_id') 
            ->where('task_phases.id', $phase)
            ->select('phase_activities.*')
            ->get();
            $data['skills'] = DB::table('employees')
                ->join('department_positions', 'department_positions.employee_id', '=', 'employees.id')
                ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
                ->join('skills', 'skills.emp_id', '=', 'employees.id')
                ->leftJoin('article_groups', 'article_groups.id', '=', 'skills.product_id')
               ->select('skills.*', 'employees.name as emp_lastname', 'employees.salary_per_hour','positions.id as position_id','employees.lastname as emp_lastname', 
                        'positions.position', 'employees.id as emp_id', 'article_groups.article_group')
                ->paginate(20);
 
           
            return view('admin.offer.set.employee.create_employee_set', $data);
        }
    }

    /**
     * Store a newly created resource in storage.
     */

     public function store(Request $request)
    {
        // Step 1: Validate the request
        $validatedData = $request->validate([
            'master_set_id'   => 'required|exists:product_master_sets,id',
            'product_id'      => 'required|exists:article_groups,id',
            'position_id'     => 'required|exists:positions,id',
            'activity_id'     => 'nullable|array',
            'activity_id.*'   => 'nullable|exists:phase_activities,id',
            'work_hour'       => 'nullable|integer|min:1|max:24',
            'sale_price'      => 'nullable|numeric',
            'phase_id'        => 'required|exists:task_phases,id',
        ]);
        

        // Step 2: Calculate buying_price
        $workHour  = $validatedData['work_hour'] ?? 0;
        $salePrice = $validatedData['sale_price'] ?? 0;
        $buyingPrice = $salePrice * $workHour;

        // Step 3: Insert into employee_sets
        $employeeSet = EmployeeSet::create([
            'master_set_id' => $validatedData['master_set_id'],
            'product_id'    => $validatedData['product_id'],
            'position_id'   => $validatedData['position_id'],
            'work_hour'     => $workHour,
            'sale_price'    => $salePrice,
            'buying_price'  => $buyingPrice,
            'total'         => $buyingPrice, // optional, depends if you want it same as buying_price
        ]);

        // Step 4: Insert into employee_activity_sets
        if (!empty($validatedData['activity_id'])) {
                foreach ($validatedData['activity_id'] as $activityId) {
                    if ($activityId) {
                        EmployeeActivitySet::create([
                            'master_set_id'    => $validatedData['master_set_id'],
                            'employee_set_id'  => $employeeSet->id,
                            'phase_id'         => $validatedData['phase_id'],
                            'activity_id'      => $activityId,
                        ]);
                    }
                }
            }
        

        return redirect()
            ->to('/add_employee_set/' . $validatedData['master_set_id'] . '/' . $validatedData['phase_id'])
            ->with('save_msg', '✅ Mitarbeiterposition erfolgreich hinzugefügt!');
    }

     
    /**
     * Display the specified resource.
     */
    public function buying_price(Request $request)
    {
       
        // Get the employee set by ID
        $id = $request->input('id');
        $data = EmployeeSet::find($id);
    
        // Check if the checkbox for buying_price is checked
        if ( $request->input('has_salary') == 'on') { 
            $total_price = $data->work_hour * $request->buying_price;
            $data->buying_price = $request->buying_price;
            $total_hour = $request->buying_price * $data->work_hour;
        } else { 
            $total_price = $data->work_hour * $request->sale_price;
            $data->sale_price = $request->sale_price;
            $total_hour = $request->sale_price * $data->work_hour;
            $data->buying_price=0;
        }

        // Update the total based on either buying or sale price
        $data->total = $total_hour;
        $data->save();
        
        // Fetch the previous master set price
        $previous_price = ProductMasterSet::find($request->master_id);
        $price = $previous_price->price;

        // Update the total price for the master set
        $total_price = $price + $data->total;

        // Calculate material and employee costs
        $material = DB::table('add_product_to_sets')
                        ->where('master_set_id', '=', $request->master_id)
                        ->sum('total');
        $employee = DB::table('employee_sets')
                        ->where('master_set_id', '=', $request->master_id)
                        ->sum('total');

        // Update the master set with the new price details
        ProductMasterSet::where('id', '=', $request->master_id)
        ->update([
            'price' => $total_price,
            'material_price'   => $material,
            'employee_price'   => $employee
        ]);

        return redirect()->back()->with('save_msg', 'Der Preis wurde zum Set hinzugefügt');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmployeeSet $employeeSet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id=request()->input('id');
        $data = EmployeeSet::find($id);
        $data->buying_price = $request->buying_price;
        $data->work_hour = $request->work_hour;
        $data->save();

        $pervious_price = ProductMasterSet::find($request->master_id);
        $price = $pervious_price->price;
        $total_price = $data->work_hour * $request->buying_price;
        $total = $price + $total_price;

        ProductMasterSet::where('id', '=', $request->master_id)
        ->update([
                'price' => $total_price,
        ]);

        return redirect()->to('add_employee_set/'.$request->master_id)->with('save_msg', 'Die Gehalt wurde zum Set hinzugefügt');
    }

    /**
     * Remove the specified resource from storage.
     */
  public function destroy($id)
{
    // Step 1: Find the EmployeeSet by ID
    $data = EmployeeSet::find($id);

    // Check if EmployeeSet exists
    if (!$data) {
        return redirect()->back()->with('error_msg', 'Der Datensatz wurde nicht gefunden');
    }

    // Step 2: Get the necessary values from the EmployeeSet
    $master_id = $data->master_set_id;
    $work_hour = $data->work_hour;
    $buying_price = $data->buying_price;

    // Step 3: Calculate the data price
    $data_price = $work_hour * $buying_price;

    // Step 4: Find the ProductMasterSet
    $master_set = ProductMasterSet::find($master_id);

    // Check if ProductMasterSet exists
    if ($master_set) {
        $previous_price = $master_set->price ?? 0; // Handle null price by defaulting to 0

        // Step 5: Calculate the new price
        $sum = $previous_price - $data_price;
    } else {
        $sum = 0; // If no master set exists, assume sum is 0 or handle as per your business logic
    }

    // Step 6: Delete the EmployeeSet record
    $data->delete();

    // Step 7: Sum the remaining employee totals
    $employee_total = DB::table('employee_sets')
        ->where('master_set_id', '=', $master_id)
        ->sum('total');

    // Step 8: Update the ProductMasterSet with the new price and employee total
    ProductMasterSet::where('id', '=', $master_id)
        ->update([
            'price' => $sum,
            'employee_price' => $employee_total,
        ]);

    // Step 9: Return success message
    return redirect()->back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
}

public function loadActivities($product_id, $phase_id)
{
    return DB::table('phase_activities')
        ->where('product_id', $product_id)
        ->where('phase_id', $phase_id)
        ->where('status', 'Published')
        ->select('id', 'title', 'description')
        ->get();
}

public function getSalaryByPosition($id)
{
    \Log::info('🔍 Position ID received:', ['id' => $id]);

    $employee = DB::table('employees')
        ->join('department_positions', 'employees.id', '=', 'department_positions.employee_id')
        ->where('department_positions.position_id', $id)
        ->whereNotNull('employees.salary_per_hour')
        ->select('employees.salary_per_hour')
        ->orderByDesc('employees.salary_per_hour')
        ->first();

    \Log::info('🧾 Matching Employee:', ['employee' => $employee]);

    if ($employee && $employee->salary_per_hour !== null) {
        return response()->json(['success' => true, 'salary' => $employee->salary_per_hour]);
    }

    return response()->json(['success' => false]);
}


}
