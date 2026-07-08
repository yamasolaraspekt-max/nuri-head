<?php

namespace App\Http\Controllers\Employee;
use App\Http\Controllers\Controller;

use App\Models\ArticleGroup;
use App\Models\Branch;
use App\Models\ContractType;
use App\Models\Country;
use App\Models\Department;
use App\Models\EmergencyContact;
use App\Models\Employee;
use App\Models\EmployeeAddress;
use App\Models\FurtherEducation;
use App\Models\Languages;
use App\Models\LeaveDay;
use App\Models\LicenseType;
use App\Models\OtherSkill;
use App\Models\Position;
use App\Models\Product;
use App\Models\Qualification;
use App\Models\Leave;
use App\Models\Skill;
use App\Models\EmployeeDepartment;
use App\Models\DepartmentPosition;
use App\Models\Tax;
use Illuminate\Http\Request; 
use DB;
use Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Notifications\EmployeeProfileNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;
 use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\EmployeeSick;
use Illuminate\Support\Carbon;
 use App\Models\EmployeePostcodeList;
 use App\Models\PersonalTask;
use App\Models\MainAppointment;
use App\Models\Problem;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\CarbonPeriod;
           
use App\Models\EmployeeLicense;
use App\Models\EmployeeCloth;
use App\Models\User;  
use App\Models\TimeManagementPlan;
use Illuminate\Support\Facades\Hash;
use App\Models\PublicHoliday; 

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        $this->middleware('auth');
    }
  
    public function index(Request $request)
    {
        $search     = $request->query('search');
        $sort       = $request->query('sort', 'lastname');   // default sort
        $direction  = $request->query('direction', 'asc');   // default asc
        $statusTab  = $request->query('status_tab', 'active'); // 'active' or 'inactive'

        // Base query for employees + branch
        $employeeQuery = DB::table('employees')
            ->leftJoin('branches', 'branches.id', '=', 'employees.branch')
            ->select('employees.*', 'branches.branch');

        // ----------------- Status tab filter -----------------
        if ($statusTab === 'active') {
            // Show only active employees
            $employeeQuery->where('employees.status', 'Active');
        } elseif ($statusTab === 'inactive') {
            // Show all non-active / null
            $employeeQuery->where(function ($q) {
                $q->where('employees.status', '!=', 'Active')
                ->orWhereNull('employees.status');
            });
        }

        // ----------------- Search -----------------
        if ($search) {
            $employeeQuery->where(function ($q) use ($search) {
                $q->where('employees.name', 'LIKE', "%{$search}%")
                ->orWhere('employees.lastname', 'LIKE', "%{$search}%")
                ->orWhere('branches.branch', 'LIKE', "%{$search}%");
            });
        }

        // ----------------- Sorting -----------------
        if (in_array($sort, ['name', 'lastname', 'status'])) {
            $employeeQuery->orderBy("employees.$sort", $direction);
        } elseif ($sort === 'department') {
            // Join with departments for sorting
            $employeeQuery
                ->leftJoin('employee_departments', 'employee_departments.employee_id', '=', 'employees.id')
                ->leftJoin('departments', 'departments.id', '=', 'employee_departments.department_id')
                ->addSelect('departments.department_name')
                ->orderBy('departments.department_name', $direction);
        } else {
            // fallback
            $employeeQuery->orderBy('employees.lastname', 'asc');
        }

        // Paginate results
        $data['data'] = $employeeQuery
            ->paginate($request->query('perPage', 25))
            ->appends($request->query()); // keep query params (search, sort, status_tab, ...)

        // Counts for tabs
        $data['activeCount'] = DB::table('employees')
            ->where('status', 'Active')
            ->count();

        $data['inactiveCount'] = DB::table('employees')
            ->where(function ($q) {
                $q->where('status', '!=', 'Active')
                ->orWhereNull('status');
            })
            ->count();

        // Other shared data
        $data['branches']       = Branch::all();
        $data['contracts']      = ContractType::all();
        $data['departments']    = Department::all();
        $data['qualifications'] = Qualification::all();
        $data['postcodeLists']  = EmployeePostcodeList::orderBy('postcode_from', 'asc')->get();

        // Employee positions grouped by department
        $data['positions'] = DB::table('department_positions')
            ->join('departments', 'departments.id', '=', 'department_positions.department_id')
            ->join('positions', 'positions.id', '=', 'department_positions.position_id')
            ->select('positions.position', 'departments.department_name', 'department_positions.employee_id', 'department_positions.main')
            ->get()
            ->groupBy('department_name')
            ->map(function ($departmentPositions) {
                return $departmentPositions->map(function ($item) {
                    return [
                        'position'    => $item->position,
                        'employee_id' => $item->employee_id,
                        'main'        => $item->main,
                    ];
                });
            })
            ->toArray();

        $data['statusTab'] = $statusTab;

        return view('admin.employee.employee', $data);
    }


   public function generateAllPasscodes()
    {
        // 1. Get all active employees
        $employees = \App\Models\Employee::where('status', 'Active')->get();
        
        // 2. Array to hold the plain text codes
        $generatedCodes = [];

        foreach ($employees as $emp) {
            // Generate random 4 digits
            $plainCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            
            // Save the HASH to the database
            $emp->passcode = \Illuminate\Support\Facades\Hash::make($plainCode);
            $emp->save();

            // Add to list for display
            $generatedCodes[] = [
                'name' => $emp->name . ' ' . $emp->lastname,
                'branch' => $emp->branch,
                'code' => $plainCode
            ];
        }

        // 3. Redirect to index with the codes in session
        return redirect()->route('emp.info')
            ->with('save_msg', 'Alle Passcodes wurden erfolgreich neu generiert.')
            ->with('generated_codes', $generatedCodes); // <--- Flash data here
    }
    public function updatePasscode(Request $request, $id)
    {
        abort_unless((bool) (auth()->user()->is_admin ?? false), 403, 'Nur Administratoren duerfen Personaldaten aendern (MASTER-01 P0-3; HR-Rolle folgt).');
        // Validate the input (e.g., must be 4 digits)
        $request->validate([
            'passcode' => 'required|digits:4',
        ]);

        $employee = Employee::findOrFail($id);

        // Hash the passcode before saving
        $employee->passcode = Hash::make($request->passcode);
        $employee->save();

        return redirect()->back()->with('save_msg', 'Passcode successfully updated.');
    }

     public function updateColor(Request $request, Employee $employee)
    {
        // if you used {id} instead of {employee} in route, change the signature to:
        // public function updateColor(Request $request, $id)

        $request->validate([
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        // if using route model binding:
        $employee->color = $request->color;
        $employee->save();

        // if using $id:
        // $emp = Employee::findOrFail($id);
        // $emp->color = $request->color;
        // $emp->save();

        return back()->with('update_msg', 'Farbe gespeichert.');
    }


 

    public function create(){
        $data['branches']=Branch::all();
        $data['supervisor']=Employee::where('status', '=', 'active')->get();
        $data['contracts']=ContractType::all();
        $data['departments']=Department::where('status', '=', 'Published')->get();
        $data['positions']=Position::all();
        $data['qualifications']=Qualification::all();
        $data['languages']=Languages::all();
        $data['countries']=Country::all();
        $data['taxes']=Tax::all();
        $data['leave_day']=LeaveDay::where('status', '=', 'Published')->select('leave_day')->pluck('leave_day')->first();
        $data['data']=DB::table('employees')
                         ->join('branches', 'branches.id', 'employees.branch')
                    ->select('employees.*', 'branches.branch')                             
                    ->paginate(10);
                    return view('admin.employee.employee.employee_add',$data);
    }

    public function delete_photo($photo){
        
        if(!empty($photo)){
            $photo_path='images/employee/'.$photo;

            if(file_exists($photo_path)){
                unlink($photo_path);
            }
        }
    }

public function add(Request $request)
{
    // Validation
    $request->validate([
        'title'           => 'required',
        'name'            => 'required',
        'lastname'        => 'required',
        'nationality'     => 'required',
        'contract_date'   => 'required|date',
        'working_hour'    => 'required|integer',
        'working_type'    => 'nullable',
        'branch'          => 'required',
        'dob'             => 'nullable|date',
        'country_id'      => 'required',
        'phone'           => 'required|unique:employees',
        'email'           => 'required|unique:employees|email',
        'salary_per_hour' => 'required|numeric',
    ], [
        'title.required'           => 'Der Titel ist wichtig',
        'name.required'            => 'Name ist wichtig',
        'lastname.required'        => 'Der Nachname ist wichtig',
        'nationality.required'     => 'Nationalität ist wichtig',
        'contract_date.required'   => 'Vertragsdatum ist wichtig',
        'working_hour.required'    => 'Arbeitszeit ist wichtig',
        'branch.required'          => 'Branche und Ort der Verantwortung sind wichtig',
        'country_id.required'      => 'Land ist wichtig',
        'phone.required'           => 'Telefon ist wichtig',
        'email.required'           => 'E-Mail ist wichtig',
        'salary_per_hour.required' => 'Stundenlohn ist wichtig',
    ]);

    $data = new Employee();
    $data->title                  = $request->title;
    $data->name                   = $request->name;
    $data->midname                = $request->middle_name;
    $data->lastname               = $request->lastname;
    $data->nationality            = $request->nationality;
    $data->religion               = $request->religion;
    $data->mother_tongue          = $request->mother_tongue;
    $data->country_id             = $request->country_id;     // Place of birth
    $data->contract_type_id       = $request->contract_type_id;
    $data->contract_date          = $request->contract_date;
    $data->working_hour           = $request->working_hour;
    $data->working_type           = $request->working_type;
    $data->daily_start_time       = $request->daily_start_time ?: '07:30:00';
    $data->daily_end_time         = $request->daily_end_time ?: '16:00:00';
    $data->color                  = $request->color;
    $data->branch                 = $request->branch;
    $data->leave                  = $request->leave;
    $data->iban                   = $request->iban;
    $data->insurance_id           = $request->insurance_id;
    $data->remaining_day          = $request->leave;
    $data->sick_leave             = $request->sick_leave;
    $data->sick_leave_remaining   = $request->sick_leave;
    $data->salary_per_hour        = $request->salary_per_hour;
    $data->dob                    = $request->dob;
    $data->resident_permit        = $request->resident_permit;
    $data->resident_permit_end_date = $request->resident_permit_end_date;
    $data->work_permit            = $request->work_permit;
    $data->work_permit_end_date   = $request->work_permit_end_date;
    $data->tax_class              = $request->tax_class;
    $data->tax_id                 = $request->tax_id;
    $data->pension_no             = $request->pension_no;
    $data->trainee                = $request->trainee;
    $data->trainee_start_date     = $request->trainee_start_date;
    $data->trainee_end_date       = $request->trainee_end_date;
    $data->health_insurance       = $request->health_insurance;
    $data->bank_name              = $request->bank_name;
    $data->kids                   = $request->kids;
    $data->bio                    = $request->bio;
    $data->phone                  = $request->phone;
    $data->email                  = $request->email;
    $data->home_phone             = $request->home_phone;
    $data->work_phone             = $request->work_phone;
    $data->gender                 = $request->gender;
    $data->marital_status         = $request->marital_status;
    $data->supervisor             = $request->supervisor;

    // Image upload
    if ($request->hasFile('image')) {
        if (!empty($data->image)) {
            $this->delete_photo($data->image);
        }
        $image_name = time() . '.' . $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move(public_path('images/employee/'), $image_name);
        $data->image = $image_name;
    }

    $data->save();

    // Languages (pivot)
    if ($request->filled('language')) {
        $data->language()->sync($request->language);
    }

    // Departments & Positions
    if ($request->filled('department')) {
        foreach ($request->department as $deptIndex => $departmentId) {
            if (empty($departmentId)) {
                continue;
            }

            EmployeeDepartment::create([
                'employee_id'   => $data->id,
                'department_id' => $departmentId,
            ]);

            if (!empty($request->position[$deptIndex])) {
                foreach ($request->position[$deptIndex] as $positionId) {
                    DepartmentPosition::create([
                        'employee_id'   => $data->id,
                        'department_id' => $departmentId,
                        'position_id'   => $positionId,
                    ]);
                }
            }
        }
    }

    /**
     * Time Management Plan
     * Save the default plan into `time_management_plans`
     * instead of the old `employee_time_schedules`.
     */
    $contractDate = Carbon::parse($data->contract_date ?? now());

    TimeManagementPlan::firstOrCreate(
        [
            'employee_id' => $data->id,
            'year'        => $contractDate->year,
            'month'       => $contractDate->month,
        ],
        [
            'working_type'    => $data->working_type,
            'hourly_rate'     => $data->salary_per_hour,
            'target_hours'    => $data->working_hour, // weekly hours snapshot
            'scheduled_hours' => 0,
            'status'          => 'draft',
            'comment'         => 'Automatisch erstellt beim Anlegen des Mitarbeiters.',
        ]
    );

    // If in Zukunft gewünscht:
    // Hier könntest du im Plan automatische tägliche Einträge 07:30–16:00 erzeugen
    // über das Model TimeManagementEntry, z.B. für alle Werktage im Monat.

    return Redirect::route('emp.next', ['id' => $data->id])
        ->with('data', $data)
        ->with('save_msg', 'Mitarbeiter erfolgreich angelegt.');
}

 
public function next_employee($id){

        $data['branches']=Branch::all();
        $data['contracts']=ContractType::all();
        $data['all_departments'] = Department::whereNull('deleted_at')->get();
        $data['all_positions'] = DB::table('positions')
            ->where('status', 'Published')
            ->get();
 
        $data['taxes']=Tax::all();  
        $data['supervisor']=Employee::where('status', '=', 'active')->get();

        $data['qualifications']=Qualification::all();
        $data['languages']=Languages::all();
        $data['emp_language']=DB::table('employee_languages')
                                        ->join('employees', 'employees.id', '=', 'employee_languages.employee_id')
                                        ->join('languages', 'languages.id', '=', 'employee_languages.languages_id')
                                        ->select('employee_languages.*', 'languages.language')
                                        ->where('employees.id', $id)
                                        ->get();
        $data['countries']=Country::all();
        $data['data'] = DB::table('employees')
            ->leftJoin('branches', 'branches.id', '=', 'employees.branch')
            ->leftJoin('contract_types', 'contract_types.id', '=', 'employees.contract_type_id')
            ->leftJoin('countries', 'countries.id', '=', 'employees.country_id')
            ->where('employees.id', '=', $id)
            ->select(
                'employees.*',
                'employees.branch as branch_id',
                'branches.branch as branch_name',
                'contract_types.contract_type',
                'countries.country',
                'countries.nationality'
            )
            ->first();

        
       
        $data['representatives'] = DB::table('job_representatives')
                                        ->join('employees', 'employees.id', '=', 'job_representatives.employee_id')
                                        ->join('departments', 'departments.id', '=', 'job_representatives.department_id')
                                        ->join('positions', 'positions.id', '=', 'job_representatives.position_id')
                                        ->join('employees as representer', 'representer.id', '=', 'job_representatives.representer_id')
                                        ->join('employees as current', 'current.id', '=','job_representatives.current_representer' )
                                        ->select('job_representatives.*', 'departments.department_name', 'positions.position', 'employees.name', 'employees.lastname'
                                                ,'representer.name as represent_name', 'representer.lastname as represent_lastname', 'current.name as current_name', 'current.lastname as current_lastname'
                                                )
                                        ->where('employees.id', $id)
                                        ->get();

        $data['delegation'] = DB::table('job_representatives')
                        ->join('employees', 'employees.id', '=', 'job_representatives.employee_id')
                        ->join('departments', 'departments.id', '=', 'job_representatives.department_id')
                        ->join('positions', 'positions.id', '=', 'job_representatives.position_id')
                        ->join('employees as representer', 'representer.id', '=', 'job_representatives.representer_id')
                        ->join('employees as current', 'current.id', '=','job_representatives.current_representer' )
                        ->select('job_representatives.*', 'departments.department_name', 'positions.position', 'employees.name', 'employees.lastname'
                                ,'representer.name as represent_name', 'representer.lastname as represent_lastname', 'current.name as current_name', 'current.lastname as current_lastname'
                                )
                        ->where('representer.id', $id)
                        ->get();
 
   
    $data['qualifications']=Qualification::where('emp_id','=', $id)->get();
    $data['feducation']=FurtherEducation::where('emp_id','=', $id)->get();
    $data['otherskill']=OtherSkill::where('emp_id','=', $id)->get();

    $data['skills']=DB::table('skills')
                    ->join('article_groups', 'article_groups.id', '=', 'skills.product_id')
                    ->where('skills.emp_id', '=', $id)
                    ->select('skills.*', 'article_groups.article_group')
                    ->get();
   

    $data['addresses']=EmployeeAddress::where('emp_id','=', $id)->get();
    $data['emergency']=EmergencyContact::where('emp_id','=', $id)->get();
    $data['leaves']=Leave::where('emp_id','=', $id)->where('leave_type','=','Personal')->get();
    $data['sicks'] = EmployeeSick::with('employee')->where('emp_id', $id)->latest()->get();

    $data['all_leaves'] = DB::table('employees')
                                ->join('leaves', 'leaves.emp_id', '=', 'employees.id')
                                ->select('employees.name' ,'employees.lastname',
                                        'leaves.leave_type', 'leaves.leave_day', 'leaves.remaining_day', 'leaves.duration', 'leaves.start_date as leave_s_date',
                                        'leaves.end_date as leave_e_date', 'leaves.approved'
                                        )
                                ->where('leaves.approved', '=', 'Yes')
                                ->get();

                                $data['representer_department'] = DB::table('employees')
                                    ->leftJoin('employee_departments', 'employee_departments.employee_id', '=', 'employees.id')
                                    ->leftJoin('departments', 'departments.id', '=', 'employee_departments.department_id')
                                    ->leftJoin('leaves', 'leaves.emp_id', '=', 'employees.id') 
                                    ->select(
                                        'employees.id',
                                        'employees.name',
                                        'employees.lastname',
                                        'employees.status',
                                        'employees.image',
                                        'departments.department_name',
                                        'departments.id as dept_id',
                                        'leaves.leave_type',
                                        'leaves.leave_day',
                                        'leaves.remaining_day',
                                        'leaves.duration',
                                        'leaves.start_date as leave_s_date',
                                        'leaves.end_date as leave_e_date',
                                        'leaves.approved'
                                    )
                                    ->where(function($query) {
                                        $query->where('leaves.approved', '=', 'Yes')
                                            ->orWhereNull('leaves.approved');
                                    })
                                    ->where(function($query) {
                                        $query->where('departments.status', '=', 'Published')
                                            ->orWhereNull('departments.status');
                                    })
                                    ->where('employees.status', '=', 'Active')
                                    ->where('employees.id', '!=', $id)
                                    ->distinct()
                                    ->get();
                                
                               
                                    
                                        $data['products']=ArticleGroup::all();

                                        //Driving License
                                        $data['license']=DB::table('employee_licenses')
                                                                    ->join('employees', 'employees.id', '=', 'employee_licenses.emp_id')
                                                                    ->select('employee_licenses.*', 'employees.name', 'employees.lastname')
                                                                    ->where('employees.id', $id)
                                                                    ->get();
                                        //License Type
                                        $data['license_type']=LicenseType::all(); 

                                        // Employee License: 
                                            $data['employee_license']=DB::table('employee_license_types')
                                            ->join('employee_licenses', 'employee_licenses.id', 'employee_license_types.employee_license_id') 
                                        ->select('employee_license_types.*')
                                        ->where('employee_licenses.emp_id', $id)
                                        ->get();
                                        

                    //Cloths type
                        $data['cloths']=DB::table('employee_cloths')
                                        ->join('employees', 'employees.id', '=', 'employee_cloths.emp_id')
                                        ->select('employee_cloths.*', 'employees.name', 'employees.lastname')
                                        ->where('employees.id', $id)
                                        ->get();
                        //Handover Items
                        $data['handover']=DB::table('handovers')
                        ->join('handover_tos', 'handover_tos.handover_id', '=', 'handovers.handover_id')
                        ->join('employees as handover_from', 'handover_from.id', '=', 'handover_tos.handover_from')
                        ->join('employees as handover_to', 'handover_to.id', '=', 'handover_tos.handover_to')
                        ->join('assets as item', 'item.id', '=', 'handovers.item_id')
                        ->where('handover_from.id', '=', $id)
                        ->select('handovers.*','handover_to.name as hand_to_name', 'handover_to.lastname as hand_to_lastname', 'handover_from.name as hand_from_name', 'handover_from.lastname as hand_from_lastname', 
                        'item.item as item', 'item.image as image', 'handover_to.image as hand_to_image', 'handover_from.image as hand_from_image', 'item.serial_no', 'item.article_no',  'handover_tos.handover_by'
                        , 'handover_tos.purpose')
                        ->orderBy('id', 'desc')
                        ->get();
                        $data['employee']=Employee::all();

                        $data['leave_duration']=LeaveDay::where('status', '=', 'Published')->first();

                        $data['lists'] = EmployeePostcodeList::where('employee_id', $id)
                                                        ->latest()
                                                        ->paginate(10);
                        $tab="general";

                        $employee_id = $id;

 
 
  
      return view('admin.employee.employee.employee_create', $data)->with('tab', $tab)->with('employee_id', $employee_id);

    }

    // Use as API to get the Department Leader for Leave 

    public function getEmployeeLeader($department_id){


        $employee = DB::table('departments')
                    ->join('employees', 'employees.id', '=', 'departments.department_head')
                    ->select('employees.id as emp_id', 'employees.name', 
                                'employees.lastname', 'employees.image', 'employees.gender', 'department_name', 'departments.id as dept_id')
                    ->where('departments.id', $department_id)
                    ->whereNotNull('departments.department_head')
                    ->get();

         return response()->json($employee, 200);
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'lastname'  =>  'required',
            'branch'    =>  'required',

        ],[
            'name'  =>  'Das Namensfeld ist erforderlich.',
            'lastname'  =>  'Das Vornamensfeld ist erforderlich.',
            'branch'  =>  'Das Branchfeld ist erforderlich.'
        ]);

       
        $data=new Employee;
        $data->name=$request->name;
        $data->lastname=$request->lastname;
        $data->branch=$request->branch;
        if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/employee/', $image_name);
            $data->image=$image_name;
            $data->save();
            
            return redirect()->back()->with('save_msg', 'The Employee has added successfully!');

        }
        else{
            $data->save();
            return redirect()->back()->with('not_save', 'The  Employee upload is not completed!');

        }
    }


    public function update(Request $request, Employee $employee)
    {
        abort_unless((bool) (auth()->user()->is_admin ?? false), 403, 'Nur Administratoren duerfen Personaldaten aendern (MASTER-01 P0-3; HR-Rolle folgt).');
        $this->validate($request, [
            'name' => 'required',
            'lastname'  =>  'required',
            'branch'    =>  'required',

        ],[
            'name'  =>  'Das Namensfeld ist erforderlich.',
            'lastname'  =>  'Das Vornamensfeld ist erforderlich.',
            'branch'  =>  'Das Branchfeld ist erforderlich.'
        ]);

       $id=$_POST['id'];
        $data=Employee::find($id);
        $data->name=$request->name;
        $data->lastname=$request->lastname;
        $data->branch=$request->branch;
        if($request->hasFile('image')){
            $this->delete_photo($data->image);
            $image_name=time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->move('images/employee/', $image_name);
            $data->image=$image_name;
            $data->save();
            
            return redirect()->back()->with('save_msg', 'The Employee has added successfully!');

        }
        else{
            $data->save();
            return redirect()->back()->with('not_save', 'The  Employee upload is not completed!');

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        abort_unless((bool) (auth()->user()->is_admin ?? false), 403, 'Nur Administratoren duerfen Personaldaten aendern (MASTER-01 P0-3; HR-Rolle folgt).');
        $data=Employee::find($id);
        $data->delete();
        $this->delete_photo($data->image);

        return back()->with('delete_msg', 'Der Datensatz wurde erfolgreich gelöscht');
    }

    public function profile_update(Request $request)
    {
        abort_unless((bool) (auth()->user()->is_admin ?? false), 403, 'Nur Administratoren duerfen Personaldaten aendern (MASTER-01 P0-3; HR-Rolle folgt).');
        try {
            /*
            |--------------------------------------------------------------------------
            | Normalize empty values
            |--------------------------------------------------------------------------
            | Important: supervisor, tax_class, mother_tongue, dates etc. must become NULL,
            | not empty string or text.
            */
            $request->merge([
                'supervisor' => $request->filled('supervisor') ? $request->input('supervisor') : null,
                'contract_type_id' => $request->filled('contract_type_id') ? $request->input('contract_type_id') : null,
                'tax_class' => $request->filled('tax_class') ? $request->input('tax_class') : null,
                'mother_tongue' => $request->filled('mother_tongue') ? $request->input('mother_tongue') : null,
                'resident_permit_end_date' => $request->filled('resident_permit_end_date') ? $request->input('resident_permit_end_date') : null,
                'work_permit_end_date' => $request->filled('work_permit_end_date') ? $request->input('work_permit_end_date') : null,
                'trainee_start_date' => $request->filled('trainee_start_date') ? $request->input('trainee_start_date') : null,
                'trainee_end_date' => $request->filled('trainee_end_date') ? $request->input('trainee_end_date') : null,
                'dob' => $request->filled('dob') ? $request->input('dob') : null,
            ]);

            $validated = $request->validate([
                'id' => 'required|integer|exists:employees,id',

                'title' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'midname' => 'nullable|string|max:255',
                'lastname' => 'required|string|max:255',
                'gender' => 'nullable|string|max:50',
                'marital_status' => 'nullable|string|max:100',
                'kids' => 'nullable|string|max:20',
                'nationality' => 'required',
                'religion' => 'nullable|string|max:100',
                'dob' => 'nullable|date',
                'country_id' => 'required',
                'mother_tongue' => 'nullable|integer|exists:languages,id',
                'language' => 'nullable|array',
                'language.*' => 'integer|exists:languages,id',
                'bio' => 'nullable|string',

                'branch' => 'required|integer|exists:branches,id',
                'contract_type_id' => 'nullable|integer|exists:contract_types,id',
                'contract_date' => 'required|date',
                'working_hour' => 'required|numeric',
                'daily_start_time' => 'nullable',
                'daily_end_time' => 'nullable',
                'working_type' => 'nullable|string|max:100',
                'salary_per_hour' => 'required|numeric',
                'leave' => 'nullable|numeric',
                'sick_leave' => 'nullable|numeric',
                'supervisor' => 'nullable|integer|exists:employees,id',

                'resident_permit' => 'nullable|string|max:20',
                'resident_permit_end_date' => 'nullable|date',
                'work_permit' => 'nullable|string|max:20',
                'work_permit_end_date' => 'nullable|date',

                'tax_class' => 'nullable|integer',
                'tax_id' => 'nullable|string|max:255',
                'pension_no' => 'nullable|string|max:255',
                'health_insurance' => 'nullable|string|max:255',
                'insurance_id' => 'nullable|string|max:255',
                'bank_name' => 'nullable|string|max:255',
                'iban' => 'nullable|string|max:255',

                'trainee' => 'nullable|string|max:20',
                'trainee_start_date' => 'nullable|date',
                'trainee_end_date' => 'nullable|date|after_or_equal:trainee_start_date',

                'phone' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'home_phone' => 'nullable|string|max:255',
                'work_phone' => 'nullable|string|max:255',

                'color' => 'nullable|string|max:20',
            ], [
                'id.required' => 'Mitarbeiter-ID fehlt.',
                'id.exists' => 'Mitarbeiter nicht gefunden.',

                'title.required' => 'Der Titel ist wichtig',
                'name.required' => 'Name ist wichtig',
                'lastname.required' => 'Der Nachname ist wichtig',
                'nationality.required' => 'Nationalität ist wichtig',
                'contract_date.required' => 'Vertragsdatum ist wichtig',
                'contract_date.date' => 'Vertragsdatum muss ein gültiges Datum sein',
                'working_hour.required' => 'Arbeitszeit ist wichtig',
                'working_hour.numeric' => 'Arbeitszeit muss eine Zahl sein',
                'branch.required' => 'Branche und Ort der Verantwortung sind wichtig',
                'branch.exists' => 'Die ausgewählte Branche existiert nicht',
                'dob.date' => 'Geburtsdatum muss ein gültiges Datum sein',
                'country_id.required' => 'Land ist wichtig',
                'phone.required' => 'Telefon ist wichtig',
                'email.required' => 'E-Mail ist wichtig',
                'email.email' => 'E-Mail muss gültig sein',
                'salary_per_hour.required' => 'Stundenlohn ist wichtig',
                'salary_per_hour.numeric' => 'Stundenlohn muss eine Zahl sein',
                'supervisor.integer' => 'Supervisor muss ein gültiger Mitarbeiter sein.',
                'supervisor.exists' => 'Der ausgewählte Supervisor existiert nicht.',
                'trainee_end_date.after_or_equal' => 'Das Enddatum darf nicht vor dem Startdatum liegen.',
            ]);

            DB::transaction(function () use ($request) {
                $employee = Employee::findOrFail($request->input('id'));

                /*
                |--------------------------------------------------------------------------
                | Personal data
                |--------------------------------------------------------------------------
                */
                $employee->title = $request->input('title');
                $employee->name = $request->input('name');
                $employee->midname = $request->input('midname');
                $employee->lastname = $request->input('lastname');
                $employee->gender = $request->input('gender');
                $employee->marital_status = $request->input('marital_status');
                $employee->kids = $request->input('kids');
                $employee->nationality = $request->input('nationality');
                $employee->religion = $request->input('religion');
                $employee->dob = $request->input('dob');
                $employee->country_id = $request->input('country_id');
                $employee->mother_tongue = $request->input('mother_tongue');
                $employee->bio = $request->input('bio');

                /*
                |--------------------------------------------------------------------------
                | Work / contract
                |--------------------------------------------------------------------------
                */
                $employee->branch = $request->input('branch');
                $employee->contract_type_id = $request->input('contract_type_id');
                $employee->contract_date = $request->input('contract_date');
                $employee->working_hour = $request->input('working_hour');
                $employee->daily_start_time = $request->input('daily_start_time');
                $employee->daily_end_time = $request->input('daily_end_time');
                $employee->working_type = $request->input('working_type');
                $employee->salary_per_hour = $request->input('salary_per_hour');
                $employee->leave = $request->input('leave');
                // FIX (Paket 2 / Punkt 3 / Bug 1): remaining_day und sick_leave_remaining sind AUFGELAUFENE
                // Salden und gehoeren NICHT in die allgemeine Profilbearbeitung. Sie werden ausschliesslich
                // ueber Urlaubs-/Krankheitsantraege (approve/destroy) veraendert. Frueher wurden sie hier bei
                // JEDER Profilaenderung auf den vollen Jahresanspruch zurueckgesetzt -> aufgelaufener Resturlaub
                // ging verloren. Bewusste Korrektur eines Saldos ist ein separater Vorgang, nicht das Profil-Update.
                $employee->sick_leave = $request->input('sick_leave');
                $employee->supervisor = $request->input('supervisor');

                /*
                |--------------------------------------------------------------------------
                | Residence / work permit
                |--------------------------------------------------------------------------
                */
                $employee->resident_permit = $request->input('resident_permit');
                $employee->resident_permit_end_date = $request->input('resident_permit_end_date');
                $employee->work_permit = $request->input('work_permit');
                $employee->work_permit_end_date = $request->input('work_permit_end_date');

                /*
                |--------------------------------------------------------------------------
                | Tax / insurance / bank
                |--------------------------------------------------------------------------
                */
                $employee->tax_class = $request->input('tax_class');
                $employee->tax_id = $request->input('tax_id');
                $employee->pension_no = $request->input('pension_no');
                $employee->health_insurance = $request->input('health_insurance');
                $employee->insurance_id = $request->input('insurance_id');
                $employee->bank_name = $request->input('bank_name');
                $employee->iban = $request->input('iban');

                /*
                |--------------------------------------------------------------------------
                | Trainee
                |--------------------------------------------------------------------------
                */
                $employee->trainee = $request->input('trainee');
                $employee->trainee_start_date = $request->input('trainee_start_date');
                $employee->trainee_end_date = $request->input('trainee_end_date');

                /*
                |--------------------------------------------------------------------------
                | Contact
                |--------------------------------------------------------------------------
                */
                $employee->phone = $request->input('phone');
                $employee->email = $request->input('email');
                $employee->home_phone = $request->input('home_phone');
                $employee->work_phone = $request->input('work_phone');

                /*
                |--------------------------------------------------------------------------
                | Color
                |--------------------------------------------------------------------------
                */
                $employee->color = $request->input('color');

                $employee->save();

                /*
                |--------------------------------------------------------------------------
                | Languages
                |--------------------------------------------------------------------------
                */
                $employee->language()->sync($request->input('language', []));
            });

            return redirect()
                ->back()
                ->withInput()
                ->with('save_msg', 'Mitarbeiterdatensatz erfolgreich aktualisiert');

        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Throwable $e) {
            Log::error('Error updating employee profile', [
                'employee_id' => $request->input('id'),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('delete_msg', 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
        }
    }


    public function active($id){

        $data=Employee::find($id);
        $data->status="Active";
        $data->save();

        return back()->with('save_msg', 'Employee activated successfully');

    }

    public function deactive($id){
        $data=Employee::find($id);
        $data->status="Deactive";
        $data->save();
        return back()->with('delete_msg', 'Employee deactivated successfully');

    }

    public function profile($id)
    {
        // BASIC EMPLOYEE + BRANCH + CONTRACT + COUNTRY + TAX
        $employee = DB::table('employees')
            ->leftJoin('branches', 'branches.id', '=', 'employees.branch')
            ->leftJoin('contract_types', 'contract_types.id', '=', 'employees.contract_type_id')
            ->leftJoin('countries', 'countries.id', '=', 'employees.country_id')
            ->leftJoin('taxes', 'taxes.id', '=', 'employees.tax_class')
            ->where('employees.id', '=', $id)
            ->select(
                'employees.*',
                'employees.branch as branch_id',
                'branches.branch as branch_name',
                'contract_types.contract_type',
                'countries.country',
                'taxes.tax',
                'taxes.class',
                'taxes.remark'
            )
            ->first();

        if (!$employee) {
            return redirect()->back()->with('save_msg', 'The employee is not found');
        }

        $data['data'] = $employee;

        // EDUCATION + SKILLS
        $data['qualifications'] = Qualification::where('emp_id', $id)->get();
        $data['feducation']      = FurtherEducation::where('emp_id', $id)->get();
        $data['otherskill']      = OtherSkill::where('emp_id', $id)->get();
        $data['languages']       = Languages::all();

        $data['skills'] = DB::table('skills')
            ->join('article_groups', 'article_groups.id', '=', 'skills.product_id')
            ->where('skills.emp_id', '=', $id)
            ->select('skills.*', 'article_groups.article_group')
            ->get();

        $data['taxes'] = Tax::all();

        // DRIVING LICENSE
        $data['license'] = DB::table('employee_licenses')
            ->join('employees', 'employees.id', '=', 'employee_licenses.emp_id')
            ->select('employee_licenses.*', 'employees.name', 'employees.lastname')
            ->where('employees.id', $id)
            ->get();

        $data['license_type'] = LicenseType::all();

        $data['employee_license'] = DB::table('employee_license_types')
            ->join('employee_licenses', 'employee_licenses.id', 'employee_license_types.employee_license_id')
            ->select('employee_license_types.*')
            ->where('employee_licenses.emp_id', $id)
            ->get();

        // CLOTHES
        $data['cloths'] = DB::table('employee_cloths')
            ->join('employees', 'employees.id', '=', 'employee_cloths.emp_id')
            ->select('employee_cloths.*', 'employees.name', 'employees.lastname')
            ->where('employees.id', $id)
            ->get();

        // ADDRESSES + EMERGENCY CONTACTS
        $data['addresses'] = EmployeeAddress::where('emp_id', $id)->get();
        $data['emergency'] = EmergencyContact::where('emp_id', $id)->get();

        // LEAVE STATISTICS
        $leaveBaseQuery = Leave::where('emp_id', $id)
            ->where('approved', 'Yes');

        $totalLeaveDays = (clone $leaveBaseQuery)->sum('duration');
        $totalSickDays  = (clone $leaveBaseQuery)
            ->where(function ($q) {
                $q->where('leave_type', 'LIKE', '%sick%')
                ->orWhere('leave_type', 'LIKE', '%krank%');
            })
            ->sum('duration');

        $upcomingLeavesQuery = (clone $leaveBaseQuery)
            ->whereDate('start_date', '>=', Carbon::today());

        $upcomingLeavesCount = (clone $upcomingLeavesQuery)->count();
        $upcomingLeaveDays   = (clone $upcomingLeavesQuery)->sum('duration');

        $data['leaveStats'] = [
            'total_leave_days'     => $totalLeaveDays,
            'total_sick_days'      => $totalSickDays,
            'upcoming_count'       => $upcomingLeavesCount,
            'upcoming_leave_days'  => $upcomingLeaveDays,
        ];

        $data['latestLeave'] = (clone $leaveBaseQuery)
            ->whereDate('start_date', '<=', Carbon::today())
            ->orderBy('start_date', 'desc')
            ->first();

        $data['nextLeave'] = (clone $leaveBaseQuery)
            ->whereDate('start_date', '>=', Carbon::today())
            ->orderBy('start_date', 'asc')
            ->first();

        // ACTIVE RECURRING LEAVES
        $data['recurringLeaves'] = DB::table('employee_recurring_leaves')
            ->where('employee_id', $id)
            ->where('is_active', 1)
            ->orderBy('start_date')
            ->get();

        // LANGUAGES (EMPLOYEE-LANG PIVOT)
        $data['language'] = DB::table('employee_languages')
            ->join('employees', 'employees.id', '=', 'employee_languages.employee_id')
            ->join('languages', 'languages.id', '=', 'employee_languages.languages_id')
            ->select('languages.language')
            ->where('employee_languages.employee_id', '=', $id)
            ->get();

        // PRODUCTS (IF YOU NEED IN PROFILE)
        $data['products'] = Product::where('status', 'Published')->get();

        // DEPARTMENTS (OLD TABLE)
        $data['department'] = DB::table('employee_departments')
            ->join('employees', 'employees.id', 'employee_departments.employee_id')
            ->join('departments', 'departments.id', 'employee_departments.department_id')
            ->select('departments.department_name')
            ->where('employee_departments.employee_id', '=', $id)
            ->get();

        // DEPARTMENT / POSITION ALLOCATIONS
        $data['roleAllocations'] = DB::table('department_positions')
            ->leftJoin('departments', 'departments.id', '=', 'department_positions.department_id')
            ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
            ->select(
                'department_positions.*',
                'departments.department_name',
                'positions.position'
            )
            ->where('department_positions.employee_id', $id)
            ->orderByDesc('department_positions.main')
            ->orderBy('departments.department_name')
            ->get();

        // HANDOVER LIST
        $data['handover'] = DB::table('handovers')
            ->join('handover_tos', 'handover_tos.handover_id', '=', 'handovers.handover_id')
            ->join('employees as handover_from', 'handover_from.id', '=', 'handover_tos.handover_from')
            ->join('employees as handover_to', 'handover_to.id', '=', 'handover_tos.handover_to')
            ->join('assets as item', 'item.id', '=', 'handovers.item_id')
            ->where('handover_from.id', '=', $id)
            ->select(
                'handovers.*',
                'handover_to.name as hand_to_name',
                'handover_to.lastname as hand_to_lastname',
                'handover_from.name as hand_from_name',
                'handover_from.lastname as hand_from_lastname',
                'item.item as item',
                'item.image as image',
                'handover_to.image as hand_to_image',
                'handover_from.image as hand_from_image',
                'item.serial_no',
                'item.article_no',
                'handover_tos.handover_by',
                'handover_tos.purpose',
                'handover_tos.handover_date'
            )
            ->orderBy('handovers.id', 'desc')
            ->get();

        // ALL EMPLOYEES
        $data['employee'] = Employee::all();

        // CUSTOMER INVOLVEMENT (placeholder)
        $data['customerInvolvementCount'] = 0;

        /*
        * ROLES + PERMISSIONS
        * - employee id is stored in users.name
        * - user_rolls.user_id -> users.id
        * - user_rolls.item_id = module name
        */
        $userRoles = collect();
        $permissionsByModule = collect();

        $user = User::where('name', $id)->first();

        if ($user) {
            // very simple roles from is_admin
            if ((int) $user->is_admin === 1) {
                $userRoles->push('Admin');
            } else {
                $userRoles->push('Mitarbeiter');
            }

            $userRolls = DB::table('user_rolls')
                ->where('user_id', $user->id)
                ->get()
                ->groupBy('item_id');

            $permissionsByModule = $userRolls->map(function ($rows, $module) {
                $row   = $rows->first();
                $perms = [];

                if (!empty($row->is_read)) {
                    $perms[] = 'Lesen';
                }
                if (!empty($row->is_add)) {
                    $perms[] = 'Anlegen';
                }
                if (!empty($row->is_update)) {
                    $perms[] = 'Bearbeiten';
                }
                if (!empty($row->is_delete)) {
                    $perms[] = 'Löschen';
                }

                return collect($perms);
            });
        }

        $data['userRoles']               = $userRoles;
        $data['userPermissionsByModule'] = $permissionsByModule;

        /*
        * PERSONAL NOTES (sticky notes for this employee)
        * personal_notes.user_id and note_categories.user both reference employees.id
        */
        $data['personalNotes'] = DB::table('personal_notes')
            ->leftJoin('note_categories', 'note_categories.id', '=', 'personal_notes.category_id')
            ->where('personal_notes.user_id', $id)
            ->whereNull('personal_notes.deleted_at')
            ->select(
                'personal_notes.*',
                'note_categories.category_name',
                'note_categories.color as category_color',
                'note_categories.icon as category_icon'
            )
            ->orderByRaw('ISNULL(personal_notes.order_by), personal_notes.order_by ASC')
            ->orderBy('personal_notes.created_at', 'desc')
            ->get();

        $data['noteCategories'] = DB::table('note_categories')
            ->whereNull('note_categories.deleted_at')
            ->where(function ($q) use ($id) {
                $q->where('note_categories.user', $id)
                ->orWhereNull('note_categories.user'); // global categories
            })
            ->orderBy('note_categories.category_name')
            ->get();

        $tab = "general";

        return view('admin.employee.profile.employee_profile', $data)->with('tab', $tab);
    }


    public function efficiency(Request $request, int $employeeId)
    {
        $days = (int) $request->get('days', 30);
        if ($days < 7) {
            $days = 7;
        }
        if ($days > 90) {
            $days = 90;
        }

        $end   = Carbon::today();
        $start = (clone $end)->subDays($days - 1);

        // ---------------------------------------------
        // PERSONAL TASKS (employees_personal_tasks)
        // ---------------------------------------------
        $tasksBase = DB::table('employees_personal_tasks as ept')
            ->join('personal_tasks as t', 't.id', '=', 'ept.task_id')
            ->where('ept.employee_id', $employeeId)
            ->whereNull('t.deleted_at')
            ->whereBetween('t.start_date', [$start->toDateString(), $end->toDateString()]);

        $totalTasks = (clone $tasksBase)->count();

        // "done" statuses – adapt to your real values
        $taskDoneStatuses = ['done', 'completed', 'finished', 'erledigt', 'closed'];

        $completedTasks = (clone $tasksBase)
            ->whereIn('t.task_status', $taskDoneStatuses)
            ->count();

        $inProgressTasks = (clone $tasksBase)
            ->whereIn('t.task_status', ['open', 'in_progress', 'running', 'neu', 'new'])
            ->count();

        // Tasks with objections (status on pivot) – adapt values if needed
        $taskObjectionStatuses = ['objection', 'rejected', 'returned', 'widerspruch'];

        $tasksWithObjections = DB::table('employees_personal_tasks as ept')
            ->join('personal_tasks as t', 't.id', '=', 'ept.task_id')
            ->where('ept.employee_id', $employeeId)
            ->whereIn('ept.status', $taskObjectionStatuses)
            ->whereBetween('t.start_date', [$start->toDateString(), $end->toDateString()])
            ->count();

        // ---------------------------------------------
        // APPOINTMENTS (main_appointment_employees)
        // ---------------------------------------------
        $appointmentsBase = DB::table('main_appointment_employees as mae')
            ->join('main_appointments as a', 'a.id', '=', 'mae.appointment_id')
            ->where('mae.employee_id', $employeeId)
            ->whereNull('a.deleted_at')
            ->whereBetween('a.start_date', [$start->toDateString(), $end->toDateString()]);

        $totalAppointments = (clone $appointmentsBase)->count();

        $appointmentDoneStatuses = ['done', 'completed', 'finished', 'erledigt', 'closed'];

        $completedAppointments = (clone $appointmentsBase)
            ->whereIn('a.status', $appointmentDoneStatuses)
            ->count();

        $inProgressAppointments = (clone $appointmentsBase)
            ->whereIn('a.status', ['open', 'in_progress', 'running', 'neu', 'new', 'planned'])
            ->count();

        // ---------------------------------------------
        // TICKET TASKS (ticket_tasks)
        // ---------------------------------------------
        $ticketBase = DB::table('ticket_tasks as tt')
            ->join('problems as p', 'p.id', '=', 'tt.ticket_id')
            ->where('tt.employee_id', $employeeId)
            ->whereBetween('tt.start_date', [$start->toDateString(), $end->toDateString()]);

        $totalTicketTasks = (clone $ticketBase)->count();

        $ticketDoneStatuses = ['done', 'completed', 'finished', 'erledigt', 'closed', 'resolved'];

        $completedTicketTasks = (clone $ticketBase)
            ->whereIn('tt.status', $ticketDoneStatuses)
            ->count();

        $inProgressTicketTasks = (clone $ticketBase)
            ->whereIn('tt.status', ['open', 'in_progress', 'running', 'neu', 'new'])
            ->count();

        $ticketObjectionStatuses = ['rejected', 'returned', 'on_hold', 'blocked'];

        $ticketsWithObjections = (clone $ticketBase)
            ->whereIn('tt.status', $ticketObjectionStatuses)
            ->count();

        // ---------------------------------------------
        // DAILY REGULARITY: days with any activity
        // ---------------------------------------------
        $taskDays = (clone $tasksBase)->pluck('t.start_date')->filter();
        $apptDays = (clone $appointmentsBase)->pluck('a.start_date')->filter();
        $ticketDays = (clone $ticketBase)->pluck('tt.start_date')->filter();

        $activeDays = $taskDays
            ->merge($apptDays)
            ->merge($ticketDays)
            ->map(function ($d) {
                return Carbon::parse($d)->toDateString();
            })
            ->unique()
            ->count();

        $regularityRatio = $days > 0 ? $activeDays / $days : 0;

        // ---------------------------------------------
        // GLOBAL EFFICIENCY SCORE
        // ---------------------------------------------
        $totalItems     = $totalTasks + $totalAppointments + $totalTicketTasks;
        $completedItems = $completedTasks + $completedAppointments + $completedTicketTasks;

        $activityScore   = $totalItems > 0 ? ($completedItems / $totalItems) * 70 : 0;
        $regularityScore = $regularityRatio * 30;

        $efficiency = (int) round(min(100, $activityScore + $regularityScore));

        // for the three tiles
        $totalInProgress = $inProgressTasks + $inProgressAppointments + $inProgressTicketTasks;
        $totalObjections = $tasksWithObjections + $ticketsWithObjections;

        return response()->json([
            'employee_id'        => $employeeId,
            'range_days'         => $days,
            'efficiency'         => $efficiency,
            'active_days'        => $activeDays,
            'total_items'        => $totalItems,
            'completed_items'    => $completedItems,

            'tiles' => [
                'total_in_progress'   => $totalInProgress,
                'tasks_completed'     => $completedItems,
                'tasks_with_objection'=> $totalObjections,
            ],
        ]);
    }


    public function profile_picture(Request $request)
    {
        $id = $request->id;
        $data = Employee::find($id);

        if ($request->hasFile('image')) {
            $this->delete_photo($data->image);

            $image_name = time().'.'.$request->file('image')->getClientOriginalExtension();

            // ✅ ensure it's saved inside public/images/employee/
            $request->file('image')->move(public_path('images/employee'), $image_name);

            $data->image = $image_name;
            $data->save();

            return redirect()->back()->with('save_msg', 'Das Mitarbeiterprofil wurde erfolgreich hinzugefügt!');
        } else {
            return redirect()->back()->with('not_save', 'Der Mitarbeiter-Upload ist nicht abgeschlossen!');
        }
    }


    public function getPositions()
    {
        $positions = Position::all();
        return response()->json($positions);
    }

     public function getPosition($departmentId, $empId)
    {
       $positions = DB::table('department_positions')
                   ->join('positions', 'positions.id', '=', 'department_positions.position_id')
                    ->join('departments', 'departments.id', 'department_positions.department_id')
                    ->where('departments.id', $departmentId)
                    ->where('department_positions.employee_id', $empId)
                   ->select('positions.id', 'positions.position') 
                   ->get(); 
    return response()->json($positions);
    }

  public function cv($id){
        $data['data']=DB::table('employees')
                ->leftJoin('branches', 'branches.id', '=', 'employees.branch')
                ->leftJoin('contract_types', 'contract_types.id', '=', 'employees.contract_type_id')
                ->leftJoin('countries', 'countries.id', '=', 'employees.country_id')
                ->leftJoin('taxes', 'taxes.id', '=', 'employees.tax_class' )
                ->where('employees.id', '=', $id)
                ->select('employees.*', 'branches.branch', 'contract_types.contract_type',  'countries.country', 'taxes.tax', 'taxes.class', 'taxes.remark')
                ->first();
 
      
    $data['qualifications']=Qualification::where('emp_id','=', $id)->get();
    $data['feducation']=FurtherEducation::where('emp_id','=', $id)->get();
    $data['otherskill']=OtherSkill::where('emp_id','=', $id)->get();
    $data['languages']=Languages::all();
        $data['skills'] = DB::table('skills as s')
            ->join('article_groups as ag', 'ag.id', '=', 's.product_id')
            ->where('s.emp_id', '=', $id)
            ->select(
                's.id', // Including all non-aggregate columns in the select statement
                's.emp_id',
                's.product_id',
                's.advice',
                's.plan',
                's.calculation',
                's.montage',
                's.project_planing',
                's.site_management',
                'ag.article_group', // Including the joined column
                DB::raw('sum(s.advice + s.plan + s.calculation + s.montage + s.project_planing + s.site_management) as result') // Aggregate function
            )
            ->groupBy(
                's.id', 
                's.emp_id', 
                's.product_id', 
                's.advice', 
                's.plan', 
                's.calculation', 
                's.montage', 
                's.project_planing', 
                's.site_management', 
                'ag.article_group' // Grouping by all selected columns to match the SELECT clause
            )
            ->get();


        $data['taxes']=Tax::all();

    //Driving License
    $data['license']=DB::table('employee_licenses')
                                ->join('employees', 'employees.id', '=', 'employee_licenses.emp_id')
                                ->select('employee_licenses.*', 'employees.name', 'employees.lastname')
                                ->where('employees.id', $id)
                                ->get();
    //License Type
    $data['license_type']=LicenseType::all();

      // Employee License: 
    $data['employee_license']=DB::table('employee_license_types')
                                    ->join('employee_licenses', 'employee_licenses.id', 'employee_license_types.employee_license_id')
                                    ->join('license_types', 'license_types.id', '=', 'employee_license_types.employee_license_id')
                                    ->select('employee_license_types.*', 'license_types.initials', 'license_types.de_name', 'license_types.svg_data')
                                    ->where('employee_licenses.emp_id', $id)
                                    ->get();
      
    //Cloths type
    $data['cloths']=DB::table('employee_cloths')
                    ->join('employees', 'employees.id', '=', 'employee_cloths.emp_id')
                    ->select('employee_cloths.*', 'employees.name', 'employees.lastname')
                    ->where('employees.id', $id)
                    ->get();
    $data['addresses']=EmployeeAddress::where('emp_id','=', $id)->get();
    $data['emergency']=EmergencyContact::where('emp_id','=', $id)->get();
    $data['leaves']=Leave::where('emp_id','=', $id)->get(); 
    $data['language']=DB::table('employee_languages')
                                ->join('employees', 'employees.id', '=', 'employee_languages.employee_id')
                                ->join('languages', 'languages.id', '=', 'employee_languages.languages_id')
                                ->select('language')
                                ->where('employee_languages.employee_id', '=', $id)
                                ->get();
    
    $data['products']=Product::where('status','=', 'Published')->get();

    $data['department']=DB::table('employee_departments')
                            ->join('employees', 'employees.id', 'employee_departments.employee_id')
                            ->join('departments', 'departments.id', 'employee_departments.department_id')
                            ->select('departments.department_name')
                            ->where('employee_departments.employee_id', '=', $id)
                            ->get();

    $data['positions'] = DB::table('department_positions')
                                ->join('departments', 'departments.id', '=', 'department_positions.department_id')
                                ->join('positions', 'positions.id', '=', 'department_positions.position_id') 
                                ->join('employees', 'employees.id', '=', 'department_positions.employee_id')
                                ->select('positions.*', 'departments.department_name', 'department_positions.id as dp_id')
                                ->where('employees.id', $id)
                                ->get();
    $data['handover']=DB::table('handovers')
                        ->join('handover_tos', 'handover_tos.handover_id', '=', 'handovers.handover_id')
                        ->join('employees as handover_from', 'handover_from.id', '=', 'handover_tos.handover_from')
                        ->join('employees as handover_to', 'handover_to.id', '=', 'handover_tos.handover_to')
                        ->join('assets as item', 'item.id', '=', 'handovers.item_id')
                        ->where('handover_from.id', '=', $id)
                        ->select('handovers.*','handover_to.name as hand_to_name', 'handover_to.lastname as hand_to_lastname', 'handover_from.name as hand_from_name', 'handover_from.lastname as hand_from_lastname', 
                        'item.item as item', 'item.image as image', 'handover_to.image as hand_to_image', 'handover_from.image as hand_from_image', 'item.serial_no', 'item.article_no',  'handover_tos.handover_by'
                        , 'handover_tos.purpose', 'handover_tos.handover_date')
                        ->orderBy('id', 'desc')
                        ->get();
    $data['employee']=Employee::all();

    $tab="general";
      return view('admin.employee.profile.cv', $data)->with('tab', $tab);
    }
    

    public function Remaining_percentage($id)
    {
        $assigned_percentage = DB::table('department_positions')
            ->where('employee_id', $id)
            ->sum('percent');

        $remaining_percentage = max(100 - $assigned_percentage, 0); // Ensure it doesn't go negative

        return response()->json([
            'remaining_percentage' => $remaining_percentage
        ]);
    }

public function add_department(Request $request): JsonResponse
{
    \Log::info('add_department request:', $request->all());

    $request->validate([
        'id'              => 'required|exists:employees,id',
        'department'      => 'required|exists:departments,id',
        'position'        => 'required|exists:positions,id',
        'percent'         => 'required|numeric|min:1|max:100',
        'montage_percent' => 'required|numeric|min:0|max:100',
        'office_percent'  => 'required|numeric|min:0|max:100',
    ]);

    $employeeId     = (int) $request->input('id');
    $departmentId   = (int) $request->input('department');
    $positionId     = (int) $request->input('position');
    $percent        = (float) $request->input('percent');
    $montagePercent = (float) $request->input('montage_percent');
    $officePercent  = (float) $request->input('office_percent');

    if ((int)($montagePercent + $officePercent) !== (int)$percent) {
        return response()->json([
            'success' => false,
            'message' => 'Die Summe von Montage und Büroanteil muss der Gesamtprozentzahl entsprechen!',
        ], 422);
    }

    DB::beginTransaction();

    try {
        // 1) ensure EmployeeDepartment exists
        EmployeeDepartment::updateOrCreate(
            [
                'employee_id'   => $employeeId,
                'department_id' => $departmentId,
            ]
        );

        // 2) create/update department_positions row
        $dp = DepartmentPosition::updateOrCreate(
            [
                'employee_id'   => $employeeId,
                'department_id' => $departmentId,
                'position_id'   => $positionId,
            ],
            [
                'percent'         => $percent,
                'montage_percent' => $montagePercent,
                'office_percent'  => $officePercent,
            ]
        );

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Neue Abteilung und Position erfolgreich gespeichert!',
            'data'    => $dp,
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('add_department error: '.$e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Serverfehler beim Speichern.',
        ], 500);
    }
} 

public function departmentTable($employeeId): JsonResponse
{
    $positions = DB::table('department_positions')
        ->join('departments', 'departments.id', '=', 'department_positions.department_id')
        ->join('positions', 'positions.id', '=', 'department_positions.position_id')
        ->select(
            'department_positions.id as dp_id',
            'department_positions.employee_id',
            'department_positions.department_id',
            'department_positions.position_id',
            'department_positions.percent',
            'department_positions.montage_percent',
            'department_positions.office_percent',
            'department_positions.main',
            'departments.department_name',
            'positions.position'
        )
        ->where('department_positions.employee_id', $employeeId)
        ->orderBy('departments.department_name')
        ->orderBy('positions.position')
        ->get();

    return response()->json([
        'success'   => true,
        'positions' => $positions,
    ]);
}


// make position main
// make position main
public function mainPositionActive($id, $employee_id)
{
    DB::beginTransaction();

    try {
        // 1) Remove "main" from any existing main position of this employee
        DB::table('department_positions')
            ->where('employee_id', $employee_id)
            ->where('main', 'active')
            ->update(['main' => null]); // or 'inactive' if you prefer

        // 2) Set this position to main
        $updated = DB::table('department_positions')
            ->where('id', $id)
            ->where('employee_id', $employee_id)
            ->update(['main' => 'active']);

        if ($updated === 0) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Position für diesen Mitarbeiter wurde nicht gefunden.',
            ], 404);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Für diesen Mitarbeiter wurde die Hauptplanstelle aktualisiert.',
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Fehler beim Aktualisieren der Hauptplanstelle.',
        ], 500);
    }
}

// remove main flag
public function mainPositionDeactive($id, $employee_id)
{
    DB::table('department_positions')
        ->where('id', $id)
        ->where('employee_id', $employee_id)
        ->update(['main' => null]);

    return response()->json([
        'success' => true,
        'message' => 'Die Hauptplanstelle wurde entfernt',
    ]);
}


public function getDepartmentsAndPositions(Request $request)
{
    try {
        $employeeId = $request->employee_id;

        if (!$employeeId) {
            return response()->json([
                'success' => false,
                'message' => 'Kein Mitarbeiter ausgewählt!'
            ], 400);
        }

        $employeeDepartment = EmployeeDepartment::where('employee_id', $employeeId)->first();
        $employeePosition = DepartmentPosition::where('employee_id', $employeeId)->first();

        $departments = Department::select('id', 'department_name as name')->get(); // Ensure correct field names
        $positions = Position::select('id', 'position as name')->get(); // Ensure correct field names

        return response()->json([
            'success' => true,
            'departments' => $departments,
            'positions' => $positions,
            'selected_department' => $employeeDepartment ? $employeeDepartment->department_id : null,
            'selected_position' => $employeePosition ? $employeePosition->position_id : null
        ]);
    } catch (\Exception $e) {
        \Log::error('Error fetching departments & positions: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Serverfehler beim Abrufen der Daten.'
        ], 500);
    }
}

public function edit_department(Request $request): JsonResponse
{
    \Log::info('edit_department request:', $request->all());

    $request->validate([
        'dp_id'           => 'required|exists:department_positions,id',
        'percent'         => 'required|numeric|min:1|max:100',
        'montage_percent' => 'required|numeric|min:0|max:100',
        'office_percent'  => 'required|numeric|min:0|max:100',
    ]);

    $dpId           = (int) $request->input('dp_id');
    $percent        = (float) $request->input('percent');
    $montagePercent = (float) $request->input('montage_percent');
    $officePercent  = (float) $request->input('office_percent');

    if ((int)($montagePercent + $officePercent) !== (int)$percent) {
        return response()->json([
            'success' => false,
            'message' => 'Die Summe von Montage und Büroanteil muss der Gesamtprozentzahl entsprechen!',
        ], 422);
    }

    $dp = DepartmentPosition::findOrFail($dpId);

    $dp->percent         = $percent;
    $dp->montage_percent = $montagePercent;
    $dp->office_percent  = $officePercent;
    $dp->save();

    return response()->json([
        'success' => true,
        'message' => 'Abteilung / Position erfolgreich aktualisiert!',
        'data'    => $dp,
    ]);
}

    
public function delete_department($dpId): JsonResponse
{
    \Log::info("delete_department called for dp_id={$dpId}");

    $deleted = DepartmentPosition::where('id', $dpId)->delete();

    if ($deleted) {
        return response()->json([
            'status'  => 'success',
            'message' => 'Die Abteilung / Position wurde erfolgreich gelöscht!',
        ]);
    }

    return response()->json([
        'status'  => 'error',
        'message' => 'Kein passender Datensatz gefunden oder bereits gelöscht.',
    ], 404);
}


    // I use this APi for Celendar 

    public function getEmployees(): JsonResponse
    {
        Log::info('Calendar getEmployees called');

        try {
            $search = trim((string) request()->query('search', ''));
            $type = request()->query('filter', 'employee');

            /*
            |--------------------------------------------------------------------------
            | Employees
            |--------------------------------------------------------------------------
            | Shows employees from ALL branches.
            | Removes duplicate employees globally by employee ID.
            |--------------------------------------------------------------------------
            */
            if ($type === 'employee') {
                $departments = DB::table('departments')
                    ->leftJoin('employees as head', 'head.id', '=', 'departments.department_head')
                    ->select([
                        'departments.id as department_id',
                        'departments.department_name',
                        'departments.department_head',

                        'head.id as head_id',
                        'head.name as head_name',
                        'head.lastname as head_lastname',
                        'head.image as head_image',
                        'head.gender as head_gender',
                        'head.color as head_color',
                        'head.status as head_status',
                        'head.branch as head_branch',
                    ])
                    ->where(function ($query) {
                        $query->whereNull('departments.status')
                            ->orWhere('departments.status', 'Published');
                    })
                    ->orderBy('departments.department_name', 'asc')
                    ->get();

                $rows = collect();

                foreach ($departments as $department) {
                    $departmentName = trim((string) ($department->department_name ?: 'Ohne Abteilung'));
                    $departmentGroupKey = mb_strtolower($departmentName);

                    /*
                    |--------------------------------------------------------------------------
                    | Department head / CEO
                    |--------------------------------------------------------------------------
                    */
                    if ($department->head_id && $department->head_status === 'Active') {
                        $rows->push((object) [
                            'id' => $department->head_id,
                            'name' => $department->head_name,
                            'lastname' => $department->head_lastname,
                            'image' => $department->head_image,
                            'gender' => $department->head_gender,
                            'color' => $department->head_color,
                            'branch_id' => $department->head_branch,

                            'department_id' => $department->department_id,
                            'department_group_key' => $departmentGroupKey,
                            'department_name' => $departmentName,

                            'position_id' => null,
                            'position_name' => 'CEO',

                            'department_position_id' => null,
                            'main' => null,
                            'percent' => null,
                            'montage_percent' => null,
                            'office_percent' => null,
                            'working_hours' => null,

                            'is_ceo' => true,
                            'is_department_head' => true,
                            'is_department_representative' => false,
                            'role_sort' => 0,
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Department employees from all branches
                    |--------------------------------------------------------------------------
                    */
                    $departmentEmployees = DB::table('department_positions')
                        ->join('employees', 'employees.id', '=', 'department_positions.employee_id')
                        ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
                        ->select([
                            'employees.id',
                            'employees.name',
                            'employees.lastname',
                            'employees.image',
                            'employees.gender',
                            'employees.color',
                            'employees.status',
                            'employees.branch as branch_id',

                            'positions.id as position_id',
                            'positions.position as position_name',

                            'department_positions.id as department_position_id',
                            'department_positions.main',
                            'department_positions.percent',
                            'department_positions.montage_percent',
                            'department_positions.office_percent',
                            'department_positions.working_hours',
                        ])
                        ->where('department_positions.department_id', $department->department_id)
                        ->where('employees.status', 'Active')
                        ->get();

                    foreach ($departmentEmployees as $employee) {
                        $position = mb_strtolower(trim((string) $employee->position_name));

                        $isMain = in_array((string) $employee->main, [
                            'Yes',
                            'yes',
                            'active',
                            'Active',
                            '1',
                            'true',
                            'on',
                        ], true);

                        $roleSort = 90;

                        if (
                            str_contains($position, 'abteilungsleiter') ||
                            str_contains($position, 'department lead') ||
                            str_contains($position, 'leiter')
                        ) {
                            $roleSort = 10;
                        } elseif ($isMain) {
                            $roleSort = 20;
                        } elseif (
                            str_contains($position, 'manager') ||
                            str_contains($position, 'leitung')
                        ) {
                            $roleSort = 30;
                        }

                        $rows->push((object) [
                            'id' => $employee->id,
                            'name' => $employee->name,
                            'lastname' => $employee->lastname,
                            'image' => $employee->image,
                            'gender' => $employee->gender,
                            'color' => $employee->color,
                            'branch_id' => $employee->branch_id,

                            'department_id' => $department->department_id,
                            'department_group_key' => $departmentGroupKey,
                            'department_name' => $departmentName,

                            'position_id' => $employee->position_id,
                            'position_name' => $employee->position_name ?: 'Mitarbeiter',

                            'department_position_id' => $employee->department_position_id,
                            'main' => $employee->main,
                            'percent' => $employee->percent,
                            'montage_percent' => $employee->montage_percent,
                            'office_percent' => $employee->office_percent,
                            'working_hours' => $employee->working_hours,

                            'is_ceo' => false,
                            'is_department_head' => false,
                            'is_department_representative' => $isMain,
                            'role_sort' => $roleSort,
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Search filter after merging all employees
                |--------------------------------------------------------------------------
                */
                if ($search !== '') {
                    $needle = mb_strtolower($search);

                    $rows = $rows->filter(function ($row) use ($needle) {
                        $haystack = mb_strtolower(trim(
                            ($row->name ?? '') . ' ' .
                            ($row->lastname ?? '') . ' ' .
                            ($row->department_name ?? '') . ' ' .
                            ($row->position_name ?? '')
                        ));

                        return str_contains($haystack, $needle);
                    })->values();
                }

                /*
                |--------------------------------------------------------------------------
                | Remove duplicates globally
                |--------------------------------------------------------------------------
                | If one employee exists multiple times, keep the highest priority:
                | CEO > department leader > main representative > manager > normal employee
                |--------------------------------------------------------------------------
                */
                $rows = $rows
                    ->groupBy('id')
                    ->map(function ($employeeRows) {
                        return $employeeRows
                            ->sortBy([
                                ['role_sort', 'asc'],
                                ['department_group_key', 'asc'],
                                ['name', 'asc'],
                                ['lastname', 'asc'],
                            ])
                            ->first();
                    })
                    ->values();

                /*
                |--------------------------------------------------------------------------
                | Group employees by normalized department name
                |--------------------------------------------------------------------------
                */
                $groups = $rows
                    ->groupBy(function ($row) {
                        return $row->department_group_key ?: 'ohne abteilung';
                    })
                    ->map(function ($items) {
                        $first = $items->first();

                        $employees = $items
                            ->sortBy([
                                ['role_sort', 'asc'],
                                ['name', 'asc'],
                                ['lastname', 'asc'],
                            ])
                            ->map(function ($row) {
                                return [
                                    'id' => $row->id,
                                    'name' => $row->name,
                                    'lastname' => $row->lastname,
                                    'full_name' => trim(($row->name ?? '') . ' ' . ($row->lastname ?? '')),
                                    'image' => $row->image ?: 'default-avatar.png',
                                    'gender' => $row->gender,
                                    'color' => $row->color ?: '#8fc73e',
                                    'branch_id' => $row->branch_id,

                                    'department_id' => $row->department_id,
                                    'department_name' => $row->department_name ?: 'Ohne Abteilung',
                                    'department_group_key' => $row->department_group_key,

                                    'position_id' => $row->position_id,
                                    'position_name' => $row->position_name ?: 'Mitarbeiter',

                                    'department_position_id' => $row->department_position_id,

                                    'main' => $row->main,
                                    'is_ceo' => (bool) $row->is_ceo,
                                    'is_department_head' => (bool) $row->is_department_head,
                                    'is_department_representative' => (bool) $row->is_department_representative,

                                    'percent' => $row->percent,
                                    'montage_percent' => $row->montage_percent,
                                    'office_percent' => $row->office_percent,
                                    'working_hours' => $row->working_hours,

                                    'role_sort' => $row->role_sort,
                                ];
                            })
                            ->values();

                        return [
                            'department_id' => $first->department_id,
                            'department_name' => $first->department_name ?: 'Ohne Abteilung',
                            'department_group_key' => $first->department_group_key,
                            'employees' => $employees,
                        ];
                    })
                    ->sortBy('department_name')
                    ->values();

                /*
                |--------------------------------------------------------------------------
                | Flat employee list
                |--------------------------------------------------------------------------
                */
                $data = $rows
                    ->sortBy([
                        ['role_sort', 'asc'],
                        ['name', 'asc'],
                        ['lastname', 'asc'],
                    ])
                    ->map(function ($row) {
                        return [
                            'id' => $row->id,
                            'name' => $row->name,
                            'lastname' => $row->lastname,
                            'full_name' => trim(($row->name ?? '') . ' ' . ($row->lastname ?? '')),
                            'image' => $row->image ?: 'default-avatar.png',
                            'gender' => $row->gender,
                            'color' => $row->color ?: '#8fc73e',
                            'branch_id' => $row->branch_id,

                            'department_id' => $row->department_id,
                            'department_name' => $row->department_name ?: 'Ohne Abteilung',
                            'department_group_key' => $row->department_group_key,

                            'position_id' => $row->position_id,
                            'position_name' => $row->position_name ?: 'Mitarbeiter',

                            'department_position_id' => $row->department_position_id,

                            'main' => $row->main,
                            'is_ceo' => (bool) $row->is_ceo,
                            'is_department_head' => (bool) $row->is_department_head,
                            'is_department_representative' => (bool) $row->is_department_representative,

                            'percent' => $row->percent,
                            'montage_percent' => $row->montage_percent,
                            'office_percent' => $row->office_percent,
                            'working_hours' => $row->working_hours,

                            'role_sort' => $row->role_sort,
                        ];
                    })
                    ->unique('id')
                    ->values();

                Log::info('Fetched calendar employees from all branches without duplicates', [
                    'search' => $search,
                    'total_employees' => $data->count(),
                    'total_groups' => $groups->count(),
                    'heads' => $data->where('is_department_head', true)->pluck('full_name')->values(),
                ]);

                return response()->json([
                    'data' => $data,
                    'groups' => $groups,
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Appointments
            |--------------------------------------------------------------------------
            */
            if ($type === 'appointment') {
                $query = DB::table('main_appointment_employees')
                    ->join(
                        'main_appointments',
                        'main_appointments.id',
                        '=',
                        'main_appointment_employees.appointment_id'
                    )
                    ->select([
                        'main_appointments.id',
                        'main_appointments.name',
                        'main_appointments.start_date',
                        'main_appointments.end_date',
                    ])
                    ->orderBy('main_appointments.name', 'asc');

                if ($search !== '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('main_appointments.name', 'LIKE', "%{$search}%")
                            ->orWhere('main_appointments.start_date', 'LIKE', "%{$search}%")
                            ->orWhere('main_appointments.end_date', 'LIKE', "%{$search}%");
                    });
                }

                $data = $query
                    ->distinct()
                    ->get();

                return response()->json([
                    'data' => $data,
                ], 200);
            }

            /*
            |--------------------------------------------------------------------------
            | Tasks
            |--------------------------------------------------------------------------
            */
            if ($type === 'task') {
                $query = DB::table('employees_personal_tasks')
                    ->join(
                        'personal_tasks',
                        'personal_tasks.id',
                        '=',
                        'employees_personal_tasks.task_id'
                    )
                    ->select([
                        'personal_tasks.id',
                        'employees_personal_tasks.same_id',
                        'personal_tasks.task_title',
                        'employees_personal_tasks.start_date',
                        'employees_personal_tasks.end_date',
                    ])
                    ->orderBy('personal_tasks.task_title', 'asc');

                if ($search !== '') {
                    $query->where(function ($q) use ($search) {
                        $q->where('personal_tasks.task_title', 'LIKE', "%{$search}%")
                            ->orWhere('employees_personal_tasks.start_date', 'LIKE', "%{$search}%")
                            ->orWhere('employees_personal_tasks.end_date', 'LIKE', "%{$search}%");
                    });
                }

                $data = $query
                    ->distinct()
                    ->get();

                return response()->json([
                    'data' => $data,
                ], 200);
            }

            return response()->json([
                'error' => 'Invalid filter type',
            ], 400);

        } catch (\Throwable $e) {
            Log::error('Calendar getEmployees failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
public function getRemainingLeave(Request $request, $emp_id)
{
    // Validate year parameter
    $year = $request->query('year', date('Y')); // Default to current year if not provided
    $previousYear = $year - 1;

    // Find the employee
    $employee = Employee::find($emp_id);
    if (!$employee) {
        return response()->json(['error' => 'Employee not found'], 404);
    }

    // ✅ Sum up the total duration of leave taken in the selected year
    $leaveTakenCurrentYear = DB::table('leaves')
        ->where('emp_id', $emp_id)
        ->where('year', $year)
        ->sum('duration');

    // ✅ Fetch last year's remaining leave
    $lastYearLeaveTaken = DB::table('leaves')
        ->where('emp_id', $emp_id)
        ->where('year', $previousYear)
        ->sum('duration');

    // ✅ Calculate last year's remaining days
    $defaultLeavePerYear = 30; // Default leave allocation for every year
    $lastYearRemainingDays = max($defaultLeavePerYear - $lastYearLeaveTaken, 0);

    // ✅ If selecting the next year, carry over the remaining leave
    if ($year > date('Y')) {
        $currentYearLeave = $defaultLeavePerYear + $lastYearRemainingDays; // Add last year's remaining leave
    } else {
        $currentYearLeave = $defaultLeavePerYear;
    }

    // ✅ Calculate total leave days (including last year's remaining leave if applicable)
    $totalLeaveDays = $currentYearLeave;

    // ✅ Calculate remaining leave days (Total leave days - leave taken in the selected year)
    $remainingDays = max($totalLeaveDays - $leaveTakenCurrentYear, 0);

   return response()->json([
        'last_year_remainings' => $lastYearRemainingDays,
        'current_year_leave' => $currentYearLeave,
        'total_leave_days' => $totalLeaveDays,
        'remaining_days' => $remainingDays,
        'selected_year' => $year,
        'debug' => [
            'leaveTakenCurrentYear' => $leaveTakenCurrentYear,
            'leaveTakenLastYear' => $lastYearLeaveTaken,
            'defaultLeavePerYear' => $defaultLeavePerYear
        ]
    ], 200);

}

    public function holidayStatus()
    {
        $today = Carbon::today();

        // Only leaves that START today and are approved
        $leaves = DB::table('leaves')
            ->select('id', 'emp_id', 'start_date', 'end_date')
            ->where('approved', 'Yes')
            ->whereDate('start_date', $today)
            ->get();

        if ($leaves->isEmpty()) {
            return response()->json(['message' => 'No employees have holidays starting today']);
        }

        // recipient (keep as your current behavior)
        $recipient = auth()->user();
        if (!$recipient) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        foreach ($leaves as $leave) {
            $leaveId = (int)$leave->id;
            $empId   = (int)$leave->emp_id;

            // Ensure leave still exists + approved (protect against stale data)
            $stillValid = DB::table('leaves')
                ->where('id', $leaveId)
                ->where('emp_id', $empId)
                ->where('approved', 'Yes')
                ->exists();

            if (!$stillValid) {
                continue; // do nothing if leave removed/unapproved
            }

            // Update employee status message (idempotent update is fine)
            Employee::where('id', $empId)->update([
                'status'     => 'Active',
                'status_msg' => 'Urlaub von: ' . $leave->start_date . ' bis ' . $leave->end_date,
            ]);

            $employee = Employee::find($empId);
            if (!$employee) continue;

            // ------------------------------------------------------------
            // 1) "Starts today" notification ONCE per leave
            // We use reminders as a sent-log: entity_type='leave_start' entity_id=leaveId
            // ------------------------------------------------------------
            $sentStartKeyType = 'leave_start';
            $sentStartExists = DB::table('reminders')
                ->whereNull('deleted_at')
                ->where('employee_id', $employee->id)     // target employee scope (or recipient scope; see note below)
                ->where('entity_type', $sentStartKeyType)
                ->where('entity_id', $leaveId)
                ->exists();

            if (!$sentStartExists) {
                // create sent marker
                $rid = DB::table('reminders')->insertGetId([
                    'employee_id'     => $employee->id,
                    'entity_type'     => $sentStartKeyType,
                    'entity_id'       => $leaveId,
                    'status'          => 'done',
                    'next_remind_at'  => null,
                    'last_reminded_at'=> now(),
                    'created_by'      => (int)($recipient->id ?? null),
                    'meta'            => json_encode([
                        'source' => 'holidayStatus',
                        'leave_id' => $leaveId,
                        'emp_id' => $empId,
                        'kind' => 'starts_today',
                    ]),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                    'deleted_at'      => null,
                ]);

                // optional event log
                if (Schema::hasTable('reminder_events')) {
                    DB::table('reminder_events')->insert([
                        'reminder_id'        => $rid,
                        'event'              => 'notified',
                        'old_next_remind_at' => null,
                        'new_next_remind_at' => null,
                        'note'               => 'Leave starts today notification sent',
                        'actor_employee_id'  => (int)($recipient->name ?? 0) ?: null,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }
 
            }

            // ------------------------------------------------------------
            // 2) "1 day remaining" notification ONCE
            // Trigger only when today is exactly 1 day before end_date.
            // Show count of remaining days (here: 1)
            // ------------------------------------------------------------
            $endDate = Carbon::parse($leave->end_date)->startOfDay();
            $daysRemaining = $today->diffInDays($endDate, false); // can be negative if already ended

            if ($daysRemaining === 1) {
                $sentRemainKeyType = 'leave_remaining_1d';

                $sentRemainExists = DB::table('reminders')
                    ->whereNull('deleted_at')
                    ->where('employee_id', $employee->id)
                    ->where('entity_type', $sentRemainKeyType)
                    ->where('entity_id', $leaveId)
                    ->exists();

                if (!$sentRemainExists) {
                    $rid2 = DB::table('reminders')->insertGetId([
                        'employee_id'     => $employee->id,
                        'entity_type'     => $sentRemainKeyType,
                        'entity_id'       => $leaveId,
                        'status'          => 'done',
                        'next_remind_at'  => null,
                        'last_reminded_at'=> now(),
                        'created_by'      => (int)($recipient->id ?? null),
                        'meta'            => json_encode([
                            'source' => 'holidayStatus',
                            'leave_id' => $leaveId,
                            'emp_id' => $empId,
                            'kind' => 'remaining_1_day',
                            'days_remaining' => 1,
                        ]),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                        'deleted_at'      => null,
                    ]);

                    if (Schema::hasTable('reminder_events')) {
                        DB::table('reminder_events')->insert([
                            'reminder_id'        => $rid2,
                            'event'              => 'notified',
                            'old_next_remind_at' => null,
                            'new_next_remind_at' => null,
                            'note'               => 'Leave remaining (1 day) notification sent',
                            'actor_employee_id'  => (int)($recipient->name ?? 0) ?: null,
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ]);
                    }

                    Notification::send($recipient, new EmployeeProfileNotification([
                        'title'   => 'Urlaub endet bald',
                        'message' => 'Der Urlaub von ' . $employee->name . ' ' . $employee->lastname . ' endet morgen. Verbleibend: 1 Tag.',
                        'emp_id'  => $employee->id,
                        'type'    => 'leave',
                    ]));
                }
            }
        }

        return response()->json(['success' => true]);
    }
    


public function holidayEndStatus()
{
    $current_date = today();

    // Get all employees who have approved leave starting today
    $employees_leave = DB::table('leaves')
        ->select('emp_id', 'start_date', 'end_date')
        ->where('approved', 'Yes')
        ->where('end_date', $current_date)
        ->get();

    if ($employees_leave->isEmpty()) {
        return response()->json(['message' => 'No employees have holidays ends today']);
    }

    // Extract employee IDs
    $employee_ids = $employees_leave->pluck('emp_id')->toArray();

    // Update employees' status
    Employee::whereIn('id', $employee_ids)->update([
        'status'     => 'Active',
        'status_msg' => null,
    ]);

    // Get employees for notification
    $employees = DB::table('employees')->whereIn('id', $employee_ids)->get();

     
    return response()->json(['success' => 'The holidays Ends']);
}

public function EmployeeListStatus(Request $request)
{
    $query = DB::table('employees')
        ->select('id', 'name', 'lastname', 'image', 'status', 'status_msg')
        ->where('status', 'Active');

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('lastname', 'LIKE', "%{$search}%");
        });
    }

    return response()->json($query->get(), 200);
}

public function EmployeeListStatusInactive(Request $request)
{
    $query = DB::table('employees')
        ->select('id', 'name', 'lastname', 'image', 'status', 'status_msg')
        ->where('status', '!=', 'Active');

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('lastname', 'LIKE', "%{$search}%");
        });
    }

    return response()->json($query->get(), 200);
}


 
public function employeeStatus()
{
    $current_date = Carbon::today();

    // Get active and inactive employees
    $employees = DB::table('employees')->select('id', 'status')->get();
    $active = $employees->where('status', 'Active')->count();
    $inactive = $employees->where('status', '!=', 'Active')->count();

    // Get distinct employees on holiday (approved leaves)
    $holiday = DB::table('leaves')
        ->join('employees', 'employees.id', '=', 'leaves.emp_id')
        ->where('leaves.approved', 'Yes')
        ->whereDate('leaves.start_date', '<=', $current_date)
        ->whereDate('leaves.end_date', '>=', $current_date)
        ->distinct('leaves.emp_id') // Ensures no duplicates
        ->count('leaves.emp_id');

    // Get distinct employees who are currently sick
    $sick = DB::table('employee_sicks')
        ->join('employees', 'employees.id', '=', 'employee_sicks.emp_id')
        ->whereDate('employee_sicks.start_date', '<=', $current_date)
        ->whereDate('employee_sicks.end_date', '>=', $current_date)
        ->distinct('employee_sicks.emp_id') // Ensures no duplicates
        ->count('employee_sicks.emp_id');

    return response()->json([
        'active_emp'    => $active,
        'inactive_emp'  => $inactive,
        'holiday_count' => $holiday,
        'sick_count'    => $sick
    ]);
}



public function calendar($emp_id)
{
    \Log::info("requested Employee", [$emp_id]);
  
    $allEvents = collect();

    // If no employee data is provided, default to the authenticated user
    if (empty($employeeData)) {
        $employeeData[] = [
            'employee_id' => $emp_id,
            'tasks_only' => 0,
            'appointments_only' => 1,
        ];
    }
     foreach ($employeeData as $employee) {
        $employeeId = $employee['employee_id'];
        $tasksOnly = $employee['tasks_only'];
        $appointmentsOnly = $employee['appointments_only'];

        // Base task query
        if ($tasksOnly) {
            $tasks = DB::table('employees_personal_tasks')
                ->leftJoin('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
                ->leftJoin('employees as emp', 'emp.id', '=', 'employees_personal_tasks.employee_id')
                ->select(
                    'personal_tasks.id',
                    'personal_tasks.task_title as title',
                    'personal_tasks.priority',
                    'emp.color as backgroundColor',
                    'personal_tasks.task_status',
                    'personal_tasks.public',
                    'personal_tasks.description',
                    'personal_tasks.due_date as start_date',
                    'personal_tasks.due_date as end_date',
                    'personal_tasks.due_time as start_time',
                    'personal_tasks.due_time as end_time', 
                    'employees_personal_tasks.id as emp_personal_id', 
                    'emp.id as employee_id',
                    'emp.name',
                    'emp.lastname',
                    'emp.image',
                    'emp.gender',
                    DB::raw('NULL as customer_id'),
                    DB::raw('"task" as type'), 
                    DB::raw('NULL as phone'),
                    DB::raw('NULL as email'),
                    DB::raw('NULL as city'),
                    DB::raw('NULL as postcode'),
                    DB::raw('NULL as street'),
                    DB::raw('NULL as contact_id'),
                    DB::raw('NULL as contact_type'),

                )
                ->whereIn('emp.id', [$employeeId])
                ->whereNull('personal_tasks.deleted_at')
                ->get();

            $allEvents = $allEvents->merge($tasks);
            \Log::info('tasks', [$allEvents]);

        }

        // Appointment query
        if ($appointmentsOnly) {
                $appointments = DB::table('main_appointment_employees')
                    ->leftJoin('main_appointments', 'main_appointments.id', '=', 'main_appointment_employees.appointment_id')
                    ->leftJoin('employees as emp', 'emp.id', '=', 'main_appointment_employees.employee_id')
                    ->select(
                        'main_appointments.id',
                        'main_appointments.name as title',
                        'main_appointments.priority',
                        'main_appointments.note as description',
                        'emp.color as backgroundColor',
                        'main_appointments.status as task_status',
                        'main_appointments.public',
                        'main_appointments.start_date',
                        'main_appointments.end_date',
                        DB::raw("COALESCE(main_appointments.start_time, '00:00:00') as start_time"),
                        DB::raw("COALESCE(main_appointments.end_time, '23:59:59') as end_time"),
                        'emp.id as employee_id',
                        'emp.name',
                        'emp.lastname',
                        'emp.image',
                        'emp.gender',
                        DB::raw('NULL as emp_personal_id'),
                        'main_appointments.customer_id',
                        'main_appointments.phone',
                        'main_appointments.email',
                        'main_appointments.street',
                        'main_appointments.postcode',
                        'main_appointments.city',
                        'main_appointments.contact_id',
                        'main_appointments.contact_type',

                        DB::raw('"appointment" as type')
                    )
                    ->where('emp.id', $employeeId)
                    ->whereNull('main_appointments.deleted_at')
                    ->get();
 
            $allEvents = $allEvents->merge($appointments);

           $holidays = DB::table('leaves')
                ->where('leaves.approved', 'Yes') 
                
                ->leftJoin('employees as emp', 'emp.id', '=', 'leaves.emp_id') // Corrected join
                ->select(
                    'leaves.id', 
                    DB::raw('"Urlaub" as title'),
                    'leaves.description as description',
                    'emp.color as backgroundColor',
                    'leaves.status as task_status', 
                    'leaves.start_date',
                    'leaves.end_date',
                    DB::raw("'00:00:00' as start_time"),
                    DB::raw("'23:59:59' as end_time"),
                    'emp.id as employee_id',
                    'emp.name',
                    'emp.lastname',
                    'emp.image',
                    'emp.gender',
                    DB::raw('NULL as emp_personal_id'),
                    DB::raw('NULL as public'),
                    DB::raw('NULL as priority'),
                    DB::raw('NULL as customer_id'),
                    DB::raw('NULL as phone'),
                    DB::raw('NULL as email'),
                    DB::raw('NULL as street'),    
                    DB::raw('NULL as city'),    
                    DB::raw('NULL as postcode'),    
                    DB::raw('NULL as contact_id'),    
                    DB::raw('NULL as contact_type'),  
                    DB::raw('"holiday" as type') // Changed from 'appointment' to 'holiday'
                )
                ->where('leaves.emp_id', $employeeId)
                ->get();


                if ($holidays->isNotEmpty()) {
                    $allEvents = $allEvents->merge($holidays);
                }

            $sicks = DB::table('employee_sicks') 
                ->leftJoin('employees as emp', 'emp.id', '=', 'employee_sicks.emp_id') // Corrected join
                ->select(
                    'employee_sicks.id', 
                    DB::raw('"Krank" as title'),
                    'employee_sicks.status_msg as description',
                    'emp.color as backgroundColor', 
                    'employee_sicks.start_date',
                    'employee_sicks.end_date',
                    DB::raw("'00:00:00' as start_time"),
                    DB::raw("'23:59:59' as end_time"),
                    'emp.id as employee_id',
                    'emp.name',
                    'emp.lastname',
                    'emp.image',
                    'emp.gender',
                    DB::raw('NULL as task_status'),
                    DB::raw('NULL as emp_personal_id'),
                    DB::raw('NULL as public'),
                    DB::raw('NULL as priority'),
                    DB::raw('NULL as customer_id'),
                    DB::raw('NULL as phone'),
                    DB::raw('NULL as email'),
                    DB::raw('NULL as street'),    
                    DB::raw('NULL as city'),    
                    DB::raw('NULL as postcode'),    
                    DB::raw('NULL as contact_id'),    
                    DB::raw('NULL as contact_type'),  
                    DB::raw('"sick" as type'), // Changed from 'appointment' to 'holiday'
                )
                ->where('employee_sicks.emp_id', $employeeId)
                ->get();


                if ($sicks->isNotEmpty()) {
                    $allEvents = $allEvents->merge($sicks);
                }


        }
    }

    // Group and process events to remove duplicates if needed
      $groupedEvents = $allEvents->groupBy(function ($event) {
            return $event->id . '-' . $event->start_date . '-' . $event->start_time;
        })->map(function ($group) {
            $event = $group->first();

            $employees = $group->map(function ($item) {
                return [
                    'employee_id' => $item->employee_id,
                    'name'        => $item->name,
                    'lastname'    => $item->lastname,
                    'image'       => $item->image,
                    'gender'      => $item->gender,
                ];
            });

            return [
                'id'               => $event->id,
                'emp_personal_id'  => $event->emp_personal_id, // Ensure it is included
                'customer_id'       => $event->customer_id, // Ensure it is included
                'title'            => $event->title,
                'start_date'       => $event->start_date,
                'end_date'         => $event->end_date,
                'start_time'       => $event->start_time,
                'end_time'         => $event->end_time, 
                'description'      => $event->description,
                'status'           => $event->task_status,
                'taskColor'        => $event->backgroundColor,
                'priority'         => $event->priority,
                'public_view'      => $event->public,
                'type'             => $event->type,
                'street'           => $event->street,
                'postcode'         => $event->postcode,
                'city'             => $event->city,
                'phone'             => $event->phone,
                'email'             => $event->email,
                'contact_id'             => $event->contact_id,
                'contact_type'             => $event->contact_type,
                'employees'        => $employees->unique('employee_id')->values(),
            ];
        });



        return response()->json([
            'data' => $groupedEvents->values(),
        ]);
}


    public function activeTab(Request $request)
    {
        $request->validate([
            'active_tab' => ['required', 'string', 'max:80'],
        ]);

        session(['active_tab' => $request->active_tab]);

        return response()->json([
            'success' => true,
            'active_tab' => $request->active_tab,
        ]);
    }
 
public function checkDepartmentHolidays($employee_id, $start_date, $end_date)
{
    // Step 1: Get department IDs of the current employee
    $departmentIds = DB::table('department_positions')
        ->where('employee_id', $employee_id)
        ->pluck('department_id')
        ->toArray();

    if (empty($departmentIds)) {
        return response()->json([
            'conflicts' => [],
            'present' => [],
            'conflict_count' => 0,
            'present_count' => 0,
        ]);
    }

    // Step 2: Get all employees in the same departments except current one (can contain duplicates)
    $allDeptEmployees = DB::table('department_positions')
        ->whereIn('department_id', $departmentIds)
        ->where('employee_id', '!=', $employee_id)
        ->pluck('employee_id')
        ->toArray();

    // Step 3: Get conflicting employees (with leave overlap)
    $conflictLeaves = DB::table('leaves')
        ->whereIn('emp_id', $allDeptEmployees)
        ->where(function ($query) use ($start_date, $end_date) {
            $query->whereBetween('start_date', [$start_date, $end_date])
                  ->orWhereBetween('end_date', [$start_date, $end_date])
                  ->orWhere(function ($query) use ($start_date, $end_date) {
                      $query->where('start_date', '<=', $start_date)
                            ->where('end_date', '>=', $end_date);
                  });
        })
        ->select('emp_id', 'start_date', 'end_date', 'status')
        ->get();

    $conflictEmpIds = $conflictLeaves->pluck('emp_id')->unique()->toArray();

    // Step 4: Get full conflict details
    $conflicts = DB::table('employees')
        ->join('leaves', 'leaves.emp_id', '=', 'employees.id')
        ->leftJoin('department_positions', 'department_positions.employee_id', '=', 'employees.id')
        ->leftJoin('departments', 'departments.id', '=', 'department_positions.department_id')
        ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
        ->whereIn('employees.id', $conflictEmpIds)
        ->where(function ($query) use ($start_date, $end_date) {
            $query->whereBetween('leaves.start_date', [$start_date, $end_date])
                  ->orWhereBetween('leaves.end_date', [$start_date, $end_date])
                  ->orWhere(function ($query) use ($start_date, $end_date) {
                      $query->where('leaves.start_date', '<=', $start_date)
                            ->where('leaves.end_date', '>=', $end_date);
                  });
        })
        ->select(
            'employees.id', 'employees.name', 'employees.lastname', 'employees.image',
            'positions.position', 'departments.department_name',
            'leaves.start_date', 'leaves.end_date', 'leaves.status'
        )
        ->get();

    // Step 5: Unique present employee IDs
    $presentEmpIds = array_unique(array_diff($allDeptEmployees, $conflictEmpIds));

    // Step 6: Get department/position per present employee
    $presentDeptPositions = DB::table('department_positions')
        ->leftJoin('departments', 'departments.id', '=', 'department_positions.department_id')
        ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
        ->whereIn('employee_id', $presentEmpIds)
        ->select('employee_id', 'departments.department_name', 'positions.position')
        ->get()
        ->groupBy('employee_id');

    // Step 7: Get base present employees (no duplicates)
    $present = DB::table('employees')
        ->whereIn('id', $presentEmpIds)
        ->select('id', 'name', 'lastname', 'image')
        ->get()
        ->map(function ($emp) use ($presentDeptPositions) {
            $emp->departments = $presentDeptPositions[$emp->id] ?? collect();
            return $emp;
        });

    return response()->json([
        'conflicts' => $conflicts,
        'present' => $present,
        'conflict_count' => $conflicts->pluck('id')->unique()->count(),
        'present_count' => $present->count(),
    ]);
}

public function getMainDepartment($id)
{
    $department = DB::table('department_positions')
        ->where('employee_id', $id)
        ->where('main', 'active')
        ->pluck('department_id')
        ->first();

    return response()->json(['department_id' => $department]);
}

public function availabilityView(Request $request)
{
    $today = Carbon::today();
    $startDate = $today;
    $endDate = $today->copy()->addDays(90);
    $workingHours = collect(range(6, 16))
        ->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00');

    // Only active employees
    $allEmployees = Employee::with(['mainDepartmentPosition.department', 'mainDepartmentPosition.position'])
        ->where('status', 'Active')
        ->get()
        ->map(function ($employee) use ($today, $startDate, $endDate, $workingHours) {
            $isAvailableToday = (
                $employee->appointments()->whereDate('start_date', $today)->count() +
                $employee->personalTasks()->whereDate('due_date', $today)->count() +
                $employee->problems()->whereDate('date', $today)->count()
            ) < 10;

            $employee->setAttribute('is_available_today', $isAvailableToday);

            $existing = [];

            $appointments = $employee->appointments()
                ->whereBetween('start_date', [$startDate, $endDate])
                ->get(['start_date', 'start_time']);
            foreach ($appointments as $a) {
                $existing[$a->start_date->toDateString()][] = $a->start_time;
            }

            $tasks = $employee->personalTasks()
                ->whereBetween('due_date', [$startDate, $endDate])
                ->get(['due_date']);
            foreach ($tasks as $t) {
                $date = \Carbon\Carbon::parse($t->due_date)->toDateString();
                $existing[$date][] = '08:00';
            }

            $problems = $employee->problems()
                ->whereBetween('date', [$startDate, $endDate])
                ->get(['date']);
            foreach ($problems as $p) {
                $date = \Carbon\Carbon::parse($p->date)->toDateString();
                $existing[$date][] = '10:00';
            }

            $bestSlots = [];
            $found = 0;

            foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
                if ($found >= 3) break;

                $dateStr = $date->toDateString();
                $booked = $existing[$dateStr] ?? [];
                $freeTimes = $workingHours->diff($booked)->values();

                foreach ($freeTimes as $time) {
                    $bestSlots[] = "$dateStr - $time";
                    $found++;
                    if ($found >= 3) break;
                }
            }

            $employee->setAttribute('best_slots', $bestSlots);

            return $employee;
        });

    // Pagination
    $perPage = 10;
    $page = $request->get('page', 1);
    $data['employees'] = new LengthAwarePaginator(
        $allEmployees->forPage($page, $perPage),
        $allEmployees->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    // Additional data
    $data['branches'] = DB::table('branches')
        ->select('id', 'branch')
        ->where('status', 'Published')
        ->orderBy('branch', 'asc')
        ->get();

    $data['task_employee'] = DB::table('main_appointment_employees')
        ->join('employees', 'employees.id', '=', 'main_appointment_employees.employee_id')
        ->select(
            'employees.id as employee_id',
            'employees.name',
            'employees.lastname',
            'employees.image',
            'employees.gender',
            'main_appointment_employees.appointment_id',
            'main_appointment_employees.status',
            'main_appointment_employees.reason'
        )
        ->get();

    $data['branch_addresses'] = DB::table('branch_addresses')
        ->join('branches', 'branches.id', '=', 'branch_addresses.branch_id')
        ->select('branch_addresses.*', 'branches.initial as branch_initial')
        ->whereNull('branch_addresses.deleted_at')
        ->get();

    $data['employeesDrop'] = DB::table('employees')
        ->select('id', 'name', 'lastname', 'image', 'gender')
        ->where('status', 'Active')
        ->orderBy('name', 'asc')
        ->get();

    $data['public_holidays'] = DB::table('public_holidays')
        ->select('id', 'name', 'start_date', 'end_date', 'city')
        ->orderBy('name', 'asc')
        ->get();

    return view('admin.employee.availability.availability', $data);
}
public function getAvailability(Request $request, Employee $employee)
{
    $date = $request->input('date') ?? now()->format('Y-m-d');

    $start = Carbon::parse($date)->startOfDay();
    $end = (clone $start)->addDays(3)->endOfDay();

    // Fixed working hours (6 AM to 4 PM)
    $workingHours = collect(range(6, 16))->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00');

    // Collect all busy times from tasks/appointments/problems
    $taskTimes = PersonalTask::whereBetween('due_date', [$start, $end])
        ->whereHas('employees', fn($q) => $q->where('employee_id', $employee->id))
        ->pluck('due_date', 'due_time');

    $appointmentTimes = MainAppointment::whereBetween('start_date', [$start, $end])
        ->whereHas('employees', fn($q) => $q->where('employee_id', $employee->id))
        ->pluck('start_date', 'start_time');

    $busyTimes = collect([...$taskTimes, ...$appointmentTimes])->groupBy(function ($time, $date) {
        return Carbon::parse($date)->format('Y-m-d');
    });

    $availability = [];

    for ($i = 0; $i < 3; $i++) {
        $currentDate = $start->copy()->addDays($i)->format('Y-m-d');
        $busy = $busyTimes->get($currentDate, collect())->pluck(0)->toArray();

        $free = $workingHours->diff($busy);
        $availability[$currentDate] = $free->values();
    }

    return response()->json([
        'employee' => $employee->name,
        'availability' => $availability
    ]);
}
public function bookAppointment(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'date'        => 'required|date',
        'time'        => 'required',
        'name'        => 'required|string|max:255',
        'full_address'=> 'required|string',
    ]);

    $appointment = \App\Models\MainAppointment::create([
        'created_by'   => auth()->user()->name ?? 1,
        'name'         => $request->name,
        'start_date'   => $request->date,
        'start_time'   => $request->time,
        'total_time'   => 1,
        'appointment_type' => 'online',
        'type'         => 'appointment',
        'color'        => '#28a745',
        'full_address' => $request->full_address,
        'street'       => $request->street,
        'postcode'     => $request->postcode,
        'city'         => $request->city,
        'latitude'     => $request->latitude,
        'longitude'    => $request->longitude
    ]);

    $appointment->employees()->attach($request->employee_id);

    return response()->json(['success' => true, 'message' => 'Termin erfolgreich gebucht.']);
}

public function getAllEmployee()
{
    return DB::table('employees')
        ->select('id as emp_id', 'name', 'lastname', 'image', 'gender', 'color')
        ->where('status', 'Active')
        ->get();
}

    public function sicknessHolidayAnalyser(Request $request)
    {
        $year = (int) $request->query('year', now()->year);
        $month = $request->query('month');
        $branchId = $request->query('branch');
        $department = $request->query('department');
        $search = trim((string) $request->query('search'));

        if ($year < 1990 || $year > now()->year + 5) {
            $year = now()->year;
        }

        $month = $month !== null && $month !== '' ? (int) $month : null;

        if ($month && ($month < 1 || $month > 12)) {
            $month = null;
        }

        $periodStart = $month
            ? Carbon::create($year, $month, 1)->startOfDay()
            : Carbon::create($year, 1, 1)->startOfDay();

        $periodEnd = $month
            ? $periodStart->copy()->endOfMonth()->endOfDay()
            : Carbon::create($year, 12, 31)->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Public holidays from database
        |--------------------------------------------------------------------------
        */
        $publicHolidayRecords = $this->publicHolidayRecordsForPeriod(
            $periodStart,
            $periodEnd,
            $branchId
        );

        $publicHolidays = $this->expandPublicHolidayDates(
            $publicHolidayRecords,
            $periodStart,
            $periodEnd
        );

        $periodWorkingDays = $this->workingDaysBetween($periodStart, $periodEnd, $publicHolidays);

        /*
        |--------------------------------------------------------------------------
        | Dynamic year dropdown range from real database records
        |--------------------------------------------------------------------------
        */
        $minLeaveYear = Leave::whereNotNull('start_date')->min(DB::raw('YEAR(start_date)'));
        $minSickYear = EmployeeSick::whereNotNull('start_date')->min(DB::raw('YEAR(start_date)'));
        $minHolidayYear = PublicHoliday::whereNotNull('start_date')->min(DB::raw('YEAR(start_date)'));

        $minYear = collect([$minLeaveYear, $minSickYear, $minHolidayYear, now()->year])
            ->filter()
            ->min();

        $maxYear = now()->year + 1;

        /*
        |--------------------------------------------------------------------------
        | Employees query
        |--------------------------------------------------------------------------
        */
        $employeesQuery = Employee::query()
            ->with([
                'departmentPositions.department',
                'departmentPositions.position',

                'leaves' => function ($q) use ($periodStart, $periodEnd) {
                    $q->whereDate('start_date', '<=', $periodEnd)
                        ->where(function ($sub) use ($periodStart) {
                            $sub->whereDate('end_date', '>=', $periodStart)
                                ->orWhereNull('end_date');
                        });
                },
                'leaves.creator',
                'leaves.approver',

                'sickRecords' => function ($q) use ($periodStart, $periodEnd) {
                    $q->whereDate('start_date', '<=', $periodEnd)
                        ->where(function ($sub) use ($periodStart) {
                            $sub->whereDate('end_date', '>=', $periodStart)
                                ->orWhereNull('end_date');
                        });
                },
                'sickRecords.creator',
            ])
            ->where('status', 'Active');

        if ($branchId) {
            $employeesQuery->where('branch', $branchId);
        }

        if ($search !== '') {
            $employeesQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('lastname', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if ($department) {
            $employeesQuery->whereHas('departmentPositions', function ($q) use ($department) {
                $q->where('department_id', $department);
            });
        }

        $employees = $employeesQuery
            ->orderBy('lastname')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Row calculation
        |--------------------------------------------------------------------------
        */
        $rows = $employees->map(function ($employee) use ($periodStart, $periodEnd, $publicHolidays, $periodWorkingDays) {
            $weeklyHours = (float) ($employee->working_hour ?: 40);
            $dailyHours = $weeklyHours > 0 ? round($weeklyHours / 5, 2) : 8;

            $targetWorkingDays = $periodWorkingDays;
            $targetWorkingHours = round($targetWorkingDays * $dailyHours, 2);

            $validLeaves = $employee->leaves
                ->filter(function ($leave) {
                    $approved = strtolower((string) $leave->approved);
                    $status = strtolower((string) $leave->status);

                    return in_array($approved, ['yes', 'ja', 'approved', 'accepted'], true)
                        || in_array($status, ['approved', 'accepted', 'genehmigt'], true);
                });

            $holidayLeaves = $validLeaves->filter(function ($leave) {
                $type = strtolower((string) $leave->leave_type);

                return !str_contains($type, 'sick')
                    && !str_contains($type, 'krank')
                    && !str_contains($type, 'ill');
            });

            $holidayDays = $holidayLeaves->sum(function ($leave) use ($periodStart, $periodEnd, $publicHolidays) {
                return $this->absenceWorkingDays(
                    $leave->start_date,
                    $leave->end_date,
                    $periodStart,
                    $periodEnd,
                    $publicHolidays
                );
            });

            $sickDays = $employee->sickRecords
                ->filter(function ($sick) {
                    $status = strtolower((string) $sick->status);

                    return !in_array($status, ['rejected', 'declined', 'cancelled', 'deleted', 'abgelehnt'], true);
                })
                ->sum(function ($sick) use ($periodStart, $periodEnd, $publicHolidays) {
                    return $this->absenceWorkingDays(
                        $sick->start_date,
                        $sick->end_date,
                        $periodStart,
                        $periodEnd,
                        $publicHolidays
                    );
                });

            $absenceDays = $holidayDays + $sickDays;
            $workedDays = max(0, $targetWorkingDays - $absenceDays);

            $holidayHours = round($holidayDays * $dailyHours, 2);
            $sickHours = round($sickDays * $dailyHours, 2);
            $workedHours = round($workedDays * $dailyHours, 2);

            $absenceRate = $targetWorkingDays > 0
                ? round(($absenceDays / $targetWorkingDays) * 100, 1)
                : 0;

            $efficiency = $targetWorkingDays > 0
                ? round(($workedDays / $targetWorkingDays) * 100, 1)
                : 0;

            $mainRole = $employee->departmentPositions
                ->sortByDesc(fn($item) => strtolower((string) $item->main) === 'yes' ? 1 : 0)
                ->first();

            $departments = $employee->departmentPositions
                ->pluck('department.department_name')
                ->filter()
                ->unique()
                ->values()
                ->implode(', ');

            $positions = $employee->departmentPositions
                ->pluck('position.position')
                ->filter()
                ->unique()
                ->values()
                ->implode(', ');

            $leaveDetails = $validLeaves
                ->filter(function ($leave) use ($periodStart, $periodEnd) {
                    return $this->recordOverlapsPeriod(
                        $leave->start_date,
                        $leave->end_date,
                        $periodStart,
                        $periodEnd
                    );
                })
                ->map(function ($leave) use ($periodStart, $periodEnd, $publicHolidays) {
                    return [
                        'type' => $leave->leave_type ?: 'Urlaub',
                        'start' => optional($leave->start_date)->format('d.m.Y'),
                        'end' => optional($leave->end_date ?: $leave->start_date)->format('d.m.Y'),
                        'days' => $this->absenceWorkingDays($leave->start_date, $leave->end_date, $periodStart, $periodEnd, $publicHolidays),
                        'approved' => $leave->approved ?: $leave->status,
                        'created_by' => trim(optional($leave->creator)->name . ' ' . optional($leave->creator)->lastname),
                        'accepted_by' => trim(optional($leave->approver)->name . ' ' . optional($leave->approver)->lastname),
                        'reason' => $leave->reason ?: $leave->description,
                    ];
                })
                ->values();

            $sickDetails = $employee->sickRecords
                ->filter(function ($sick) use ($periodStart, $periodEnd) {
                    return $this->recordOverlapsPeriod(
                        $sick->start_date,
                        $sick->end_date,
                        $periodStart,
                        $periodEnd
                    );
                })
                ->map(function ($sick) use ($periodStart, $periodEnd, $publicHolidays) {
                    return [
                        'type' => 'Krankheit',
                        'start' => optional($sick->start_date)->format('d.m.Y'),
                        'end' => optional($sick->end_date ?: $sick->start_date)->format('d.m.Y'),
                        'days' => $this->absenceWorkingDays($sick->start_date, $sick->end_date, $periodStart, $periodEnd, $publicHolidays),
                        'status' => $sick->status,
                        'created_by' => trim(optional($sick->creator)->name . ' ' . optional($sick->creator)->lastname),
                        'message' => $sick->status_msg,
                        'documents' => $sick->document_urls ?? [],
                    ];
                })
                ->values();

            return [
                'id' => $employee->id,
                'employee' => $employee,
                'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                'image' => $employee->image ? asset('images/employee/' . $employee->image) : null,
                'department' => $departments ?: optional(optional($mainRole)->department)->department_name,
                'position' => $positions ?: optional(optional($mainRole)->position)->position,
                'weekly_hours' => $weeklyHours,
                'daily_hours' => $dailyHours,
                'target_working_days' => $targetWorkingDays,
                'target_hours' => $targetWorkingHours,
                'holiday_days' => $holidayDays,
                'holiday_hours' => $holidayHours,
                'sick_days' => $sickDays,
                'sick_hours' => $sickHours,
                'absence_days' => $absenceDays,
                'worked_days' => $workedDays,
                'worked_hours' => $workedHours,
                'absence_rate' => $absenceRate,
                'efficiency' => $efficiency,
                'leave_details' => $leaveDetails,
                'sick_details' => $sickDetails,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */
        $summary = [
            'employees' => $rows->count(),
            'working_days' => $periodWorkingDays,
            'holiday_days' => $rows->sum('holiday_days'),
            'sick_days' => $rows->sum('sick_days'),
            'absence_days' => $rows->sum('absence_days'),
            'target_hours' => round($rows->sum('target_hours'), 2),
            'worked_hours' => round($rows->sum('worked_hours'), 2),
            'avg_efficiency' => $rows->count() ? round($rows->avg('efficiency'), 1) : 0,
            'avg_absence_rate' => $rows->count() ? round($rows->avg('absence_rate'), 1) : 0,
            'public_holiday_count' => count($publicHolidays),
        ];

        $branches = Branch::orderBy('branch')->get();
        $departments = Department::orderBy('department_name')->get();

        return view('admin.employee.employee.sickness_holiday_analyser', [
            'rows' => $rows,
            'summary' => $summary,
            'branches' => $branches,
            'departments' => $departments,
            'year' => $year,
            'month' => $month,
            'minYear' => $minYear,
            'maxYear' => $maxYear,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'publicHolidays' => $publicHolidays,
            'publicHolidayRecords' => $publicHolidayRecords,
        ]);
    }
    private function publicHolidayRecordsForPeriod(Carbon $periodStart, Carbon $periodEnd, $branchId = null)
    {
        $branch = null;

        if ($branchId) {
            $branch = Branch::find($branchId);
        }

        return PublicHoliday::query()
            ->whereDate('start_date', '<=', $periodEnd)
            ->where(function ($q) use ($periodStart) {
                $q->whereDate('end_date', '>=', $periodStart)
                    ->orWhereNull('end_date');
            })
            ->when($branch, function ($q) use ($branch) {
                $q->where(function ($scope) use ($branch) {
                    $scope->whereNull('country')
                        ->orWhere('country', '')
                        ->orWhere('country', $branch->country ?? 'Germany')
                        ->orWhere('country', 'Deutschland')
                        ->orWhere('country', 'Germany');
                });

                $q->where(function ($scope) use ($branch) {
                    $scope->whereNull('state')
                        ->orWhere('state', '')
                        ->orWhere('state', $branch->state ?? null);
                });

                $q->where(function ($scope) use ($branch) {
                    $scope->whereNull('city')
                        ->orWhere('city', '')
                        ->orWhere('city', $branch->city ?? null);
                });
            })
            ->orderBy('start_date')
            ->get();
    }

    private function expandPublicHolidayDates($holidayRecords, Carbon $periodStart, Carbon $periodEnd): array
    {
        $dates = [];

        foreach ($holidayRecords as $holiday) {
            if (!$holiday->start_date) {
                continue;
            }

            $start = Carbon::parse($holiday->start_date)->startOfDay();
            $end = $holiday->end_date
                ? Carbon::parse($holiday->end_date)->startOfDay()
                : $start->copy();

            if ($end->lt($periodStart) || $start->gt($periodEnd)) {
                continue;
            }

            $from = $start->greaterThan($periodStart) ? $start : $periodStart;
            $to = $end->lessThan($periodEnd) ? $end : $periodEnd;

            foreach (CarbonPeriod::create($from, $to) as $date) {
                $dates[] = $date->toDateString();
            }
        }

        return collect($dates)
            ->unique()
            ->values()
            ->toArray();
    }

   
    private function workingDaysBetween(Carbon $start, Carbon $end, array $publicHolidays = []): int
    {
        if ($end->lt($start)) {
            return 0;
        }

        $holidaySet = collect($publicHolidays)
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $days = 0;

        foreach (CarbonPeriod::create($start->copy()->startOfDay(), $end->copy()->startOfDay()) as $date) {
            if ($date->isWeekend()) {
                continue;
            }

            if ($holidaySet->has($date->toDateString())) {
                continue;
            }

            $days++;
        }

        return $days;
    }

    private function recordOverlapsPeriod($startDate, $endDate, Carbon $periodStart, Carbon $periodEnd): bool
    {
        if (!$startDate) {
            return false;
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = $endDate
            ? Carbon::parse($endDate)->endOfDay()
            : $start->copy()->endOfDay();

        return $start->lte($periodEnd) && $end->gte($periodStart);
    }
    private function absenceWorkingDays($startDate, $endDate, Carbon $periodStart, Carbon $periodEnd, array $publicHolidays): int
    {
        if (!$startDate) {
            return 0;
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : $start->copy()->endOfDay();

        if ($end->lt($periodStart) || $start->gt($periodEnd)) {
            return 0;
        }

        $from = $start->greaterThan($periodStart) ? $start : $periodStart;
        $to = $end->lessThan($periodEnd) ? $end : $periodEnd;

        return $this->workingDaysBetween($from, $to, $publicHolidays);
    }
 

    private function germanPublicHolidays(int $year): array
    {
        $easter = Carbon::createFromTimestamp(easter_date($year))->startOfDay();

        return collect([
            Carbon::create($year, 1, 1),        // Neujahr
            Carbon::create($year, 5, 1),        // Tag der Arbeit
            Carbon::create($year, 10, 3),       // Tag der Deutschen Einheit
            Carbon::create($year, 12, 25),      // 1. Weihnachtstag
            Carbon::create($year, 12, 26),      // 2. Weihnachtstag
            $easter->copy()->subDays(2),        // Karfreitag
            $easter->copy()->addDay(),          // Ostermontag
            $easter->copy()->addDays(39),       // Christi Himmelfahrt
            $easter->copy()->addDays(50),       // Pfingstmontag
        ])
            ->map(fn($date) => $date->toDateString())
            ->unique()
            ->values()
            ->toArray();
    }

}



