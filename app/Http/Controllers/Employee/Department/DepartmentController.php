<?php

namespace App\Http\Controllers\Employee\Department;
use App\Http\Controllers\Controller;

use App\Models\Department;
use App\Models\Branch;
use App\Models\Position;
use App\Models\Employee;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Problem;
use App\Models\DepartmentPosition;
use Illuminate\Support\Carbon; 

use Illuminate\Support\Facades\Validator;
 

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct(){
        // MASTER-01 P1-IDOR: Personal-Rollen-Gate (permission:Employee)
        $this->middleware('permission:Employee,delete')->only(['destroy', 'delete_leader', 'delete_representative', 'unassignPosition', 'employeeChange']);
        $this->middleware('permission:Employee,update')->only(['update', 'updateOrder', 'updateEmployeeAllocations', 'description_update', 'storeHead', 'storeRepresentative']);
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search   = $request->query('search');
        $branchId = $request->query('branch_id');
        $status   = $request->query('status', 'all');       // all | active | inactive
        $head     = $request->query('head', 'all');         // all | with_head | without_head
        $sortBy   = $request->query('sort_by', 'order');    // order | name | status | created_at
        $sortDir  = $request->query('sort_dir', 'asc');     // asc | desc

        // NEW: date filter for customers (lead_product_lists.created_at)
        $dateFrom = $request->query('date_from'); // YYYY-MM-DD or null
        $dateTo   = $request->query('date_to');   // YYYY-MM-DD or null

        if (!$branchId) {
            $branchId = Branch::orderBy('id')->value('id');
        }

        // ---------- Base query per branch (for analytics) ----------
        $baseBranchQuery = Department::query()
            ->where('branch_id', $branchId);

        // ---------- Analytics ----------
        $total      = (clone $baseBranchQuery)->count();
        $active     = (clone $baseBranchQuery)->where('status', 'Published')->count();
        $inactive   = (clone $baseBranchQuery)->where('status', '!=', 'Published')->count();
        $withHead   = (clone $baseBranchQuery)->whereNotNull('department_head')->count();
        $withoutHead= (clone $baseBranchQuery)->whereNull('department_head')->count();

        $employeesInBranch = DB::table('employee_departments')
            ->join('departments', 'departments.id', '=', 'employee_departments.department_id')
            ->where('departments.branch_id', $branchId)
            ->distinct('employee_departments.employee_id')
            ->count('employee_departments.employee_id');

        $analytics = [
            'total'        => $total,
            'active'       => $active,
            'inactive'     => $inactive,
            'with_head'    => $withHead,
            'without_head' => $withoutHead,
            'employees'    => $employeesInBranch,
        ];

        // ---------- Main listing query ----------
        $departmentsQuery = Department::query()
            ->where('departments.branch_id', $branchId)
            ->when($search, function ($q) use ($search) {
                $q->where('departments.department_name', 'like', '%' . $search . '%');
            })
            ->when($status !== 'all', function ($q) use ($status) {
                if ($status === 'active') {
                    $q->where('departments.status', 'Published');
                } elseif ($status === 'inactive') {
                    $q->where('departments.status', 'Unpublished');
                }
            })
            ->when($head !== 'all', function ($q) use ($head) {
                if ($head === 'with_head') {
                    $q->whereNotNull('departments.department_head');
                } elseif ($head === 'without_head') {
                    $q->whereNull('departments.department_head');
                }
            });

        // ---------- Sorting ----------
        switch ($sortBy) {
            case 'name':
                $departmentsQuery
                    ->orderBy('departments.parent_id')
                    ->orderBy('departments.department_name', $sortDir);
                break;

            case 'status':
                $departmentsQuery
                    ->orderBy('departments.parent_id')
                    ->orderBy('departments.status', $sortDir)
                    ->orderBy('departments.department_name');
                break;

            case 'created_at':
                $departmentsQuery
                    ->orderBy('departments.parent_id')
                    ->orderBy('departments.created_at', $sortDir);
                break;

            case 'order':
            default:
                $departmentsQuery
                    ->orderBy('departments.parent_id')
                    ->orderBy('departments.order')
                    ->orderBy('departments.department_name');
                break;
        }

        $departments = $departmentsQuery->get();
        $structuredDepartments = $departments->groupBy('parent_id');

        $headIds = $departments->pluck('department_head')->filter();
        $repIds  = $departments->pluck('head_representative')->filter();

        $employeeData = Employee::whereIn('id', $headIds->merge($repIds)->unique())
            ->select('id', 'name', 'lastname', 'image')
            ->get()
            ->keyBy('id');

        $branches = Branch::orderBy('branch')->get();

        $positions = Position::where('status', 'Published')
            ->orderBy('position')
            ->get(['id', 'position']);

        // =========================================================
        // NEW: Department-level statistics: customers + salary cost
        // =========================================================
        $departmentIds = $departments->pluck('id')->all();

        $departmentStats = [];

        if (!empty($departmentIds)) {

            // ---- Customers per department (lead_product_lists) ----
            // Map status → bucket:
            //  open:   status IN (lead, open, angebot)
            //  zusage: status IN (deal, project, complete)
            //  ticket: status = ticket
            //  absage: status IN (stop, cancel, pause) OR work_status IN (pause, stop)
            $customerStats = DB::table('lead_product_lists as lpl')
                ->whereIn('lpl.department_id', $departmentIds)
                ->whereNull('lpl.deleted_at')
                ->when($dateFrom, function ($q) use ($dateFrom) {
                    $q->whereDate('lpl.created_at', '>=', $dateFrom);
                })
                ->when($dateTo, function ($q) use ($dateTo) {
                    $q->whereDate('lpl.created_at', '<=', $dateTo);
                })
                ->groupBy('lpl.department_id')
                ->select(
                    'lpl.department_id',
                    DB::raw('COUNT(DISTINCT lpl.customer_id) as customers_total'),
                    DB::raw("COUNT(DISTINCT CASE 
                        WHEN lpl.status IN ('lead','open','angebot') 
                            OR (lpl.status IS NULL AND lpl.work_status = 'playing')
                        THEN lpl.customer_id END) as customers_open"),
                    DB::raw("COUNT(DISTINCT CASE 
                        WHEN lpl.status IN ('deal','project','complete') 
                        THEN lpl.customer_id END) as customers_zusage"),
                    DB::raw("COUNT(DISTINCT CASE 
                        WHEN lpl.status = 'ticket'
                        THEN lpl.customer_id END) as customers_ticket"),
                    DB::raw("COUNT(DISTINCT CASE 
                        WHEN lpl.status IN ('stop','cancel','pause')
                            OR lpl.work_status IN ('pause','stop')
                        THEN lpl.customer_id END) as customers_absage")
                )
                ->get()
                ->keyBy('department_id');

            // ---- Salary cost per department (department_positions × salaries) ----
            // We use the active salary sheet per employee and distribute by dp.percent.
            // monthly_cost = COALESCE(employer_total_monthly, total_monthly_salary, gross_monthly)
            $salaryBase = DB::table('salaries')
                ->where('status', 'active')
                ->select(
                    'emp_id',
                    DB::raw('COALESCE(employer_total_monthly, total_monthly_salary, gross_monthly, 0) as monthly_cost')
                );

            $salaryStats = DB::table('department_positions as dp')
                ->joinSub($salaryBase, 's', function ($join) {
                    $join->on('dp.employee_id', '=', 's.emp_id');
                })
                ->whereIn('dp.department_id', $departmentIds)
                ->groupBy('dp.department_id')
                ->select(
                    'dp.department_id',
                    DB::raw('SUM(s.monthly_cost * (dp.percent / 100.0)) as salary_cost')
                )
                ->get()
                ->keyBy('department_id');

            foreach ($departmentIds as $deptId) {
                $c = $customerStats->get($deptId);
                $s = $salaryStats->get($deptId);

                $departmentStats[$deptId] = [
                    'customers_total'   => $c->customers_total   ?? 0,
                    'customers_open'    => $c->customers_open    ?? 0,
                    'customers_zusage'  => $c->customers_zusage  ?? 0,
                    'customers_ticket'  => $c->customers_ticket  ?? 0,
                    'customers_absage'  => $c->customers_absage  ?? 0,
                    'salary_cost'       => $s->salary_cost       ?? 0.0,
                ];
            }
        }

        // ---------- AJAX response ----------
        if ($request->ajax()) {
            $html = view('admin.employee.department.partials.table_rows', [
                'structuredDepartments' => $structuredDepartments,
                'employeeData'          => $employeeData,
                'departmentStats'       => $departmentStats,
            ])->render();

            return response()->json([
                'success'        => true,
                'html'           => $html,
                'selectedBranch' => $branchId,
                'analytics'      => $analytics,
            ]);
        }

        // ---------- Normal view ----------
        return view('admin.employee.department.department', [
            'structuredDepartments' => $structuredDepartments,
            'employeeData'          => $employeeData,
            'branch'                => $branches,
            'selectedBranch'        => $branchId,
            'positions'             => $positions,
            'analytics'             => $analytics,
            'statusFilter'          => $status,
            'headFilter'            => $head,
            'sortBy'                => $sortBy,
            'sortDir'               => $sortDir,
            'departmentStats'       => $departmentStats,
            'dateFrom'              => $dateFrom,
            'dateTo'                => $dateTo,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function publish($id)
    {
        $data = Department::find($id);
        $data->status="Published";
        $data->save();
        return redirect()->to('/department_view')->with('save_msg', 'Abteilung erfolgreich aktualisiert!!');

    }

     public function unpublish($id)
    {
        $data = Department::find($id);
        $data->status="Unpublished";
        $data->save();
        return redirect()->to('/department_view')->with('delete_msg', 'Abteilung erfolgreich aktualisiert!!');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
         $this->validate($request,[
            'department_name'=>'required|unique:departments',
            'branch_id'=>'required|exists:branches,id',
         ]);

       $data=new Department;
       $data->department_name=$request->department_name;
       $data->branch_id=$request->branch_id;
       $data->parent_id = $request->parent_id;
       $data->save();
        return back()->with('save_msg', 'Die Abteilung wurde erfolgreich hinzugefügt');
    }

 
 
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->validate($request,[
            'department_name'    =>  'required'
        ],
        );
        $id=$_POST['id'];
        $data=Department::find($id);
        $data->department_name=$request->department_name;
        $data->save();

        return redirect()->to('/department_view')->with('save_msg', 'Abteilung erfolgreich aktualisiert!!');

    }

    /**
     * Remove the specified resource from storage.
     */
   
 public function description_update(Request $request){
      $request->validate([
            'id'    =>  'required|exists:departments,id',  
            'job_description'   =>  'required|string'
        ]);

        $id=$request->id;
      
    try{
        $department = Department::find($request->id);
           if (!$department) {
            return redirect()->back()->with('error_msg', 'Abteilung nicht gefunden');
        }
         Department::find($id)->update([
            'description'   =>  $request->job_description
        ]);
        return redirect()->back()->with('save_msg', 'Stellenbeschreibung wurde erfolgreich gespeichert');
    }
   catch (\Exception $e) {
        Log::error('Fehler beim Löschen der Abteilung: ' . $e->getMessage());

        return redirect()->back()->with('error_msg', 'Fehler: ' . $e->getMessage());
    }
 }

public function destroy($id)
{
    try {
        // Find the parent department
        $department = Department::find($id);

        if (!$department) {
            return redirect()->back()->with('error_msg', 'Abteilung nicht gefunden');
        }

        // Delete all child departments recursively
        $this->deleteChildren($id);

        // Now delete the parent department
        $department->delete();

        return redirect()->back()->with('delete_msg', 'Abteilung und Unterabteilungen erfolgreich gelöscht');
    } catch (\Exception $e) {
        Log::error('Fehler beim Löschen der Abteilung: ' . $e->getMessage());

        return redirect()->back()->with('error_msg', 'Fehler: ' . $e->getMessage());
    }
}

/**
 * Recursively delete all child departments.
 */
private function deleteChildren($parentId)
{
    $children = Department::where('parent_id', $parentId)->get();

    foreach ($children as $child) {
        $this->deleteChildren($child->id); // Recursively delete its children
        $child->delete();
    }
}



    public function organize()
    {
        $positions = Position::where('status', 'Published')
            ->orderBy('position')
            ->get(['id', 'position']);

        return view('admin.employee.department.organization', compact('positions'));
    }

     public function chart_store(Request $request)
    {
        $request->validate(['name' => 'required|string', 'parent_id' => 'nullable|exists:departments,id']);

        $department = Department::create(['department_name' => $request->name, 'parent_id' => $request->parent_id]);

        return response()->json(['success' => true, 'message' => 'Department added successfully!', 'id' => $department->id]);
    }

     public function chart_destroy($id)
    {
        $department = Department::find($id);
        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found!'], 404);
        }

        $department->delete();
        return response()->json(['success' => true, 'message' => 'Department deleted successfully!']);
    }

    public function getEmployee(){
        $data = Employee::where('status', 'active')->get();

         return response()->json($data, 200);
    }

    public function storeHead(Request $request){

        $validate = $request->validate([
            'employee_id'  =>   'required|exists:employees,id',
            'department_id' =>  'required|exists:departments,id'
        ]);

        Department::find($request->department_id)
                        ->update([
                            'department_head'   => $request->employee_id,
                        ]);

          return response()->json(['success', 'The head of department is assigned']);
    }

    public function getHead($id){
        
        $data = DB::table('departments')
                    ->join('employees', 'employees.id', '=', 'departments.department_head')
                    ->select('employees.name', 'employees.lastname', 'employees.image')
                    ->where('departments.id', $id)
                    ->first();
         return response()->json($data, 200);
        
    }

    public function Employees($parent_id){
        $data = DB::table('employee_departments')
                    ->join('employees', 'employees.id', '=', 'employee_departments.employee_id')
                    ->where('department_id', $parent_id)
                    ->select('employees.id', 'employees.name', 'employees.lastname', 'employees.image', 'employees.working_hour')
                    ->get();

        return response()->json($data, 200);
    }

    public function employeeChange(Request $request){
    $validate = $request->validate([
        'employee_id'   => 'required|exists:employees,id',
        'department_id' => 'required|exists:departments,id' 
    ]);

    // ✅ Remove the employee from their **previous department only** (not all departments)
    DB::table('employee_departments')
        ->where('employee_id', '=', $request->employee_id)
        ->whereNotIn('department_id', [$request->department_id]) // ✅ Keep existing assignments except the new one
        ->delete();

    // ✅ Assign employee to the new department
    DB::table('employee_departments')
        ->insert([
            'employee_id'   => $request->employee_id,
            'department_id' => $request->department_id
        ]);

    return response()->json(['success' => true, 'message' => 'The employee has been reassigned.']);
}


public function updateOrder(Request $request)
{
    $validate = $request->validate([
        'departments' => 'required|array'
    ]);

    foreach ($request->departments as $index => $department) {
        Department::where('id', $department['id'])->update([
            'parent_id' => isset($department['parent_id']) && $department['parent_id'] !== "" ? $department['parent_id'] : null, // ✅ Ensure `NULL`
            'order' => $index + 1
        ]);
    }

    return response()->json(['success' => true, 'message' => 'Department order updated successfully']);
}


public function filterByBranch(Request $request)
{
    $branchId = $request->query('branch_id');

    // Fetch only departments that belong to the selected branch
    $departments = Department::where('branch_id', $branchId)->select('id', 'department_name')->get();

    // Return departments as JSON
    return response()->json(['departments' => $departments]);
}

public function delete_leader($department_id)
{
    $department = Department::find($department_id);

    if (!$department) {
        return response()->json(['error' => 'Department not found.'], 404);
    }

    $department->update([
        'department_head' => null,
    ]);

    return response()->json(['success' => true, 'message' => 'The Department Leader has been removed successfully.']);
} 


public function calculateDepartmentCost($department)
{
    // Fetch salary details for employees in the department
    $department_costs = DB::table('department_positions')
        ->join('employees', 'employees.id', '=', 'department_positions.employee_id')
        ->select(
            'department_positions.employee_id',
            'department_positions.percent',
            'employees.working_hour',
            'employees.salary_per_hour'
        )
        ->where('department_positions.department_id', $department->id)
        ->get();

    // Calculate department cost based on percentage and working hours
    $department_total_cost = 0;
    foreach ($department_costs as $item) {
        $department_total_cost += ($item->salary_per_hour * $item->working_hour * ($item->percent / 100));
    }

    // Assign cost to department object
    $department->total_cost = $department_total_cost;

    // Initialize total including child departments
    $total_cost_with_children = $department_total_cost;

    // Check if the department has children and calculate their costs
    if (!empty($department->children)) {
        foreach ($department->children as $child) {
            $total_cost_with_children += $this->calculateDepartmentCost($child);
        }
    }

    // Assign total cost including children
    $department->total_cost_with_children = $total_cost_with_children;

    return $total_cost_with_children;
}
public function profile($id)
{
    // Fetch the selected department with branch and head info
    $department = DB::table('departments')
        ->join('branches', 'branches.id', '=', 'departments.branch_id')
        ->leftJoin('employees', 'employees.id', '=', 'departments.department_head')
        ->select(
            'departments.*',
            'branches.branch',
            'employees.name as emp_name',
            'employees.lastname as emp_lastname',
            'employees.image as emp_image'
        )
        ->where('departments.id', $id)
        ->first();

    if (!$department) {
        abort(404, 'Department not found');
    }

    // Fetch all departments (for hierarchy)
    $allDepartments = DB::table('departments')
        ->join('branches', 'branches.id', '=', 'departments.branch_id')
        ->leftJoin('employees', 'employees.id', '=', 'departments.department_head')
        ->select(
            'departments.*',
            'branches.branch',
            'employees.name as emp_name',
            'employees.lastname as emp_lastname',
            'employees.image as emp_image'
        )
        ->whereNull('departments.deleted_at')
        ->get()
        ->toArray();

    // Fetch employees assigned to this department
    $employees = DB::table('employees')
        ->leftJoin('department_positions', 'department_positions.employee_id', '=', 'employees.id')
        ->where('department_positions.department_id', $id)
        ->where('employees.status', 'Active')
        ->select(
            'employees.id',
            'employees.name',
            'employees.lastname',
            'employees.image',
            'employees.working_hour',
            'employees.salary_per_hour'
        )
        ->distinct()
        ->get();

    // Fetch positions for employees in this department
    $positions = DB::table('department_positions')
        ->join('employees', 'employees.id', '=', 'department_positions.employee_id')
        ->join('departments', 'departments.id', '=', 'department_positions.department_id')
        ->join('positions', 'positions.id', '=', 'department_positions.position_id')
        ->select(
            'department_positions.*',
            'positions.position',
            'departments.department_name',
            'employees.name as emp_name',
            'employees.lastname as emp_lastname',
            'employees.image as emp_image'
        )
        ->where('departments.id', $id)
        ->get();

    // Recursive department tree builder
    $buildTree = function (array $elements, $parentId = null) use (&$buildTree) {
        $branch = [];
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    };

    $treeDepartments = $buildTree($allDepartments);

    // Optionally calculate cost per tree node
    foreach ($treeDepartments as $treeDepartment) {
        $this->calculateDepartmentCost($treeDepartment);
    }

    // Pass all to view
    return view('admin.employee.department.profile.department_profile', [
        'department'      => $department,
        'employees'       => $employees,
        'positions'       => $positions,
        'treeDepartments' => $treeDepartments,
        'others'          => $allDepartments,
    ]);
}


public function profileJson($id)
{
    $departmentId = (int) $id;
    $currentYear  = (int) now()->year;

    // ===== 0) Basis-Team der Abteilung (analog Blade) =====
    $employees = DB::table('employees')
        ->leftJoin('department_positions', 'department_positions.employee_id', '=', 'employees.id')
        ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
        ->where('department_positions.department_id', $departmentId)
        ->where('employees.status', 'Active')
        ->select(
            'employees.id',
            'employees.name',
            'employees.lastname',
            'employees.email',
            'employees.image',
            'employees.status',
            DB::raw('COALESCE(positions.position, "") as position')
        )
        ->distinct()
        ->get();

    Log::info('profileJson: base employees', [
        'department_id' => $departmentId,
        'employees'     => $employees->toArray(),
    ]);

    if ($employees->isEmpty()) {
        return response()->json([
            'employees' => [],
            'debug' => [
                'message' => 'No employees found for department',
                'department_id' => $departmentId,
            ],
        ]);
    }

    $employeeIds = $employees->pluck('id')->all();

    // ===== 1) Abteilungen pro Mitarbeiter (über department_positions) =====
    $departmentsPerEmployee = DB::table('department_positions')
        ->select(
            'employee_id',
            DB::raw('COUNT(DISTINCT department_id) as department_count')
        )
        ->whereIn('employee_id', $employeeIds)
        ->groupBy('employee_id')
        ->pluck('department_count', 'employee_id');

    Log::info('profileJson: departmentsPerEmployee', [
        'employee_ids'            => $employeeIds,
        'departmentsPerEmployee'  => $departmentsPerEmployee->toArray(),
    ]);

    // ===== 2) Positionen pro Mitarbeiter =====
    $positionsPerEmployee = DB::table('department_positions')
        ->select(
            'employee_id',
            DB::raw('COUNT(DISTINCT position_id) as position_count')
        )
        ->whereIn('employee_id', $employeeIds)
        ->groupBy('employee_id')
        ->pluck('position_count', 'employee_id');

    Log::info('profileJson: positionsPerEmployee', [
        'positionsPerEmployee' => $positionsPerEmployee->toArray(),
    ]);

    // ===== 3) Urlaubstage im aktuellen Jahr =====
    $leavesPerEmployee = DB::table('leaves')
        ->select(
            'emp_id',
            DB::raw('SUM(leave_day) as total_leave_days')
        )
        ->whereIn('emp_id', $employeeIds)
        ->whereIn('approved', ['Approved', 'approved'])
        ->whereYear('start_date', $currentYear)
        ->groupBy('emp_id')
        ->pluck('total_leave_days', 'emp_id');

    Log::info('profileJson: leavesPerEmployee', [
        'current_year'      => $currentYear,
        'leavesPerEmployee' => $leavesPerEmployee->toArray(),
    ]);

    // ===== 4) Krankheitstage im aktuellen Jahr =====
    $sicksPerEmployee = DB::table('employee_sicks')
        ->select(
            'emp_id',
            DB::raw('SUM(
                COALESCE(
                    total_days,
                    DATEDIFF(COALESCE(end_date, start_date), start_date) + 1
                )
            ) as total_sick_days')
        )
        ->whereIn('emp_id', $employeeIds)
        ->whereYear('start_date', $currentYear)
        ->groupBy('emp_id')
        ->pluck('total_sick_days', 'emp_id');

    Log::info('profileJson: sicksPerEmployee', [
        'current_year'       => $currentYear,
        'sicksPerEmployee'   => $sicksPerEmployee->toArray(),
    ]);

    // ===== 5) Wiederkehrende Abwesenheiten =====
    $recurringRows = DB::table('employee_recurring_leaves')
        ->whereIn('employee_id', $employeeIds)
        ->where('is_active', true)
        ->get()
        ->groupBy('employee_id');

    $recurringSummary = [];

    foreach ($recurringRows as $empId => $rows) {
        $rulesCount = $rows->count();
        $weeklyDays = 0;

        foreach ($rows as $row) {
            if ($row->type === 'weekly') {
                $weekdays = $row->weekdays ? json_decode($row->weekdays, true) : [];
                if (!is_array($weekdays)) {
                    $weekdays = [];
                }

                $duration   = (int) ($row->duration_days ?? 1);
                $duration   = max(1, $duration);
                $weeklyDays += count($weekdays) * $duration;
            }
        }

        $recurringSummary[$empId] = [
            'rules_count' => $rulesCount,
            'weekly_days' => $weeklyDays,
        ];
    }

    Log::info('profileJson: recurringSummary', [
        'recurringSummary' => $recurringSummary,
    ]);

    // ===== 6) Response-Daten pro Mitarbeiter =====
    $employeesData = $employees->map(function ($e) use (
        $departmentsPerEmployee,
        $positionsPerEmployee,
        $leavesPerEmployee,
        $sicksPerEmployee,
        $recurringSummary
    ) {
        $id  = (int) $e->id;
        $rec = $recurringSummary[$id] ?? ['rules_count' => 0, 'weekly_days' => 0];

        return [
            'id'                     => $id,
            'name'                   => $e->name,
            'lastname'               => $e->lastname,
            'email'                  => $e->email,
            'image'                  => $e->image,
            'status'                 => $e->status,
            'position'               => $e->position,

            'department_count'       => (int) ($departmentsPerEmployee[$id] ?? 0),
            'position_count'         => (int) ($positionsPerEmployee[$id] ?? 0),
            'leave_days'             => (int) ($leavesPerEmployee[$id] ?? 0),
            'sick_days'              => (int) ($sicksPerEmployee[$id] ?? 0),
            'recurring_rules'        => (int) ($rec['rules_count'] ?? 0),
            'recurring_weekly_days'  => (int) ($rec['weekly_days'] ?? 0),
        ];
    })->values();

    // ===== 7) Debug-Block in der JSON-Antwort =====
    $debug = [
        'department_id'        => $departmentId,
        'employee_ids'         => $employeeIds,
        'departments_raw'      => $departmentsPerEmployee,
        'positions_raw'        => $positionsPerEmployee,
        'leaves_raw'           => $leavesPerEmployee,
        'sicks_raw'            => $sicksPerEmployee,
        'recurring_raw'        => $recurringSummary,
    ];

    return response()->json([
        'employees' => $employeesData,
        'debug'     => $debug,   // <- hier siehst du alles direkt im Browser
    ]);
}

public function profileApi($id)
{
    $department = Department::with('employees')->findOrFail($id);

    $employees = DB::table('employees')
        ->leftJoin('department_positions', 'department_positions.employee_id', '=', 'employees.id')
        ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
        ->where('department_positions.department_id', $id)
        ->where('employees.status', 'Active')
        ->select(
            'employees.id',
            'employees.name',
            'employees.lastname',
            'employees.email',
            'employees.image',
            'positions.position',
            'employees.status'
        )
        ->distinct()
        ->get();

    return response()->json([
        'department' => $department->department_name,
        'employees'  => $employees
    ]);
}

 public function calendar($department_id)
{
    $employees = DB::table('department_positions')
        ->where('department_id', $department_id)
        ->pluck('employee_id')
        ->toArray();

    if (empty($employees)) {
        return response()->json(['data' => []]);
    }

    $allEvents = collect();

    foreach ($employees as $employeeId) {
        // Personal Tasks (via pivot table)
        $assignedTasks = DB::table('employees_personal_tasks')
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
                DB::raw('"task" as type')
            )
            ->where('employees_personal_tasks.employee_id', $employeeId)
            ->whereNull('personal_tasks.deleted_at')
            ->get();

        $allEvents = $allEvents->merge($assignedTasks);

        // Direct Personal Tasks (assigned_by)
        $directTasks = DB::table('personal_tasks')
            ->leftJoin('employees as emp', 'emp.id', '=', 'personal_tasks.assigned_by')
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
                DB::raw('NULL as emp_personal_id'),
                'emp.id as employee_id',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                DB::raw('"task" as type')
            )
            ->where('personal_tasks.assigned_by', $employeeId)
            ->whereNull('personal_tasks.deleted_at')
            ->get();

        $allEvents = $allEvents->merge($directTasks);

        // Appointments
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
                DB::raw('"appointment" as type')
            )
            ->where('main_appointment_employees.employee_id', $employeeId)
            ->whereNull('main_appointments.deleted_at')
            ->get();

        $allEvents = $allEvents->merge($appointments);

        // Holidays
        $holidays = DB::table('leaves')
            ->leftJoin('employees as emp', 'emp.id', '=', 'leaves.emp_id')
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
                DB::raw('"holiday" as type')
            )
            ->where('leaves.emp_id', $employeeId)
            ->where('leaves.approved', 'Yes')
            ->get();

        $allEvents = $allEvents->merge($holidays);

        // Sick Leaves
        $sicks = DB::table('employee_sicks')
            ->leftJoin('employees as emp', 'emp.id', '=', 'employee_sicks.emp_id')
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
                DB::raw('"sick" as type')
            )
            ->where('employee_sicks.emp_id', $employeeId)
            ->get();

        $allEvents = $allEvents->merge($sicks);
    }

    // Group and format for frontend
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
            'id'             => $event->id,
            'title'          => $event->title ?? '',
            'start_date'     => $event->start_date ?? '',
            'end_date'       => $event->end_date ?? '',
            'start_time'     => $event->start_time ?? '',
            'end_time'       => $event->end_time ?? '',
            'description'    => $event->description ?? '',
            'status'         => $event->task_status ?? '',
            'taskColor'      => $event->backgroundColor,
            'priority'       => $event->priority ?? '',  
            'type'           => $event->type ?? '',
            'employees'      => $employees->unique('employee_id')->values(),
        ];
    });

    return response()->json([
        'data' => $groupedEvents->values(),
    ]);
}


public function departmentTickets($departmentId)
{
    // Load department with employees
    $department = Department::with('employees')->findOrFail($departmentId);
    $employeeIds = $department->employees->pluck('id');

    // Get all problems where at least one employee from this department is assigned
    $problems = Problem::whereHas('employees', function ($q) use ($employeeIds) {
            $q->whereIn('employees.id', $employeeIds);
        })
        ->with(['employees']) // load all employees for each problem
        ->get();

    return response()->json([
        'department' => $department->department_name,
        'tickets'    => $problems->map(function ($p) use ($employeeIds) {
            // Employees from this department only
            $deptEmployees = $p->employees->whereIn('id', $employeeIds);

            // ✅ SLA progress based on status
            $progress = match(strtolower($p->status)) {
                'offen'    => 10,   // just started
                'process'  => 50,   // in progress
                'end'      => 100,  // finished
                default    => 0,    // unknown status
            };

            return [
                'id'            => $p->id,
                'ticket_no'     => $p->ticket_no,
                'title'         => $p->article_name,
                'product'       => optional($p->product)->name,
                'priority'      => $p->priority,
                'status'        => $p->status,
                'sla'           => $p->sla ?? '—',
                'sla_progress'  => $progress,
                'updated_at'    => optional($p->updated_at)->toDateString(),

                // 👇 Avatars of employees from THIS department
                'dept_employees'=> $deptEmployees->map(fn($e) => [
                    'id'    => $e->id,
                    'name'  => $e->name . ' ' . $e->lastname,
                    'image' => $e->image
                        ? asset('images/employee/' . $e->image)
                        : asset('images/default-avatar.svg'),
                ])->values(),

                // 👇 Count how many employees from this department are in this problem
                'dept_employee_count' => $deptEmployees->count(),
            ];
        })->values(),
    ]);
}




public function tasksApi($departmentId)
{
    $tasks = DB::table('personal_tasks')
        ->join('employees_personal_tasks', 'employees_personal_tasks.task_id', '=', 'personal_tasks.id')
        ->join('employees', 'employees.id', '=', 'employees_personal_tasks.employee_id')
        ->join('department_positions', 'department_positions.employee_id', '=', 'employees.id')
        ->where('department_positions.department_id', $departmentId)
        ->select(
            'personal_tasks.id',
            'personal_tasks.task_title as title',
            'personal_tasks.description',  
            'personal_tasks.task_status as status',
            'personal_tasks.priority',
            'personal_tasks.start_date',
            'personal_tasks.due_date',
            'employees.id as employee_id',
            'employees.name',
            'employees.lastname',
            'employees.image'
        )
        ->distinct()
        ->get();

    // Group by task
    $formatted = $tasks->groupBy('id')->map(function ($group) {
        $first = $group->first();

        return [
            'id'          => $first->id,
            'title'       => $first->title ?? '(No Title)',
            'description' => $first->description ?? '',   // ✅ fixed
            'status'      => $first->status ?? 'open',
            'priority'    => $first->priority ?? 'Medium',
            'start'       => $first->start_date,
            'due'         => $first->due_date,
            'assignees'   => $group->map(fn($e) => [
                'id'     => $e->employee_id,
                'name'   => $e->name.' '.$e->lastname,
                'avatar' => $e->image 
                            ? asset('images/employee/'.$e->image) 
                            : asset('images/default-avatar.svg')
            ])->values(),
        ];
    })->values();

    return response()->json(['tasks' => $formatted]);
}

public function expensesApi($departmentId)
{
    $deptId = (int) $departmentId;

    // === 1) Salary sheets: active rows, employer-side monthly total ===
    // monthly_cost = employer_total_monthly (preferred) 
    //             or total_monthly_salary 
    //             or gross_monthly
    $salaryBase = DB::table('salaries')
        ->where('status', 'active')
        ->select(
            'emp_id',
            DB::raw('COALESCE(employer_total_monthly, total_monthly_salary, gross_monthly, 0) as monthly_cost')
        );

    // Join with department_positions to distribute by percent
    $monthly = DB::table('department_positions as dp')
        ->joinSub($salaryBase, 's', function ($join) {
            $join->on('dp.employee_id', '=', 's.emp_id');
        })
        ->where('dp.department_id', $deptId)
        ->selectRaw('COALESCE(SUM(s.monthly_cost * (dp.percent / 100.0)), 0) as total')
        ->value('total');

    // === 2) Fallback if there are no salary sheets yet ===
    // Use employees.salary_per_hour * working_hours * percent/100
    // Assume employees.working_hour is the monthly target hours (as in your time management).
    if (!$monthly) {
        $monthly = DB::table('department_positions as dp')
            ->join('employees as e', 'e.id', '=', 'dp.employee_id')
            ->where('dp.department_id', $deptId)
            ->selectRaw('COALESCE(SUM(
                COALESCE(e.salary_per_hour, 0)
                * COALESCE(dp.working_hours, e.working_hour, 0)
                * (dp.percent / 100.0)
            ), 0) as total')
            ->value('total');
    }

    $monthly   = round((float) $monthly, 2);
    $quarterly = round($monthly * 3, 2);
    $yearly    = round($monthly * 12, 2);

    return response()->json([
        'expenses' => [
            'monthly'   => $monthly,
            'quarterly' => $quarterly,
            'yearly'    => $yearly,
        ],
    ]);
}


public function storeRepresentative(Request $request)
{
    $request->validate([
        'employee_id'   => 'required|exists:employees,id',
        'department_id' => 'required|exists:departments,id',
    ]);

    Department::findOrFail($request->department_id)
        ->update([
            'head_representative' => $request->employee_id,
        ]);

    return response()->json(['success' => true, 'message' => 'The representative has been assigned']);
}

public function getRepresentative($id)
{
    $data = DB::table('departments')
        ->join('employees', 'employees.id', '=', 'departments.head_representative')
        ->select('employees.name', 'employees.lastname', 'employees.image')
        ->where('departments.id', $id)
        ->first();

    return response()->json($data, 200);
}
public function delete_representative($department_id)
{
    $department = Department::find($department_id);

    if (!$department) {
        return response()->json(['error' => 'Department not found.'], 404);
    }

    $department->update([
        'head_representative' => null,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'The Department Representative has been removed successfully.'
    ]);
}
 
public function assignPosition(Request $request)
{
    $data = $request->validate([
        'employee_id'      => 'required|exists:employees,id',
        'department_id'    => 'required|exists:departments,id',
        'position_id'      => 'required|exists:positions,id',
        'percent'          => 'required|numeric|min:0|max:100',
        'montage_percent'  => 'required|numeric|min:0|max:100',
        'office_percent'   => 'required|numeric|min:0|max:100',
        'working_hours'    => 'nullable|numeric|min:0',
    ]);

    // 1) Montage + Office = 100 
    if (abs(($data['montage_percent'] + $data['office_percent']) - $data['percent']) > 0.0001) {
        return response()->json([
            'success' => false,
            'message' => 'Montage- und Office-Anteil müssen zusammen dem Gesamt-Prozent entsprechen (z.B. 25 + 25 = 50).'
        ], 422);
    }


    $employee   = Employee::findOrFail($data['employee_id']);
    $baseHours  = $employee->working_hour ?? 0;

    // 2) Calculate working_hours
    $workingHours = $baseHours * ($data['percent'] / 100);
    if (!is_null($data['working_hours']) && $data['working_hours'] > 0) {
        $workingHours = $data['working_hours'];
    }

    // 3) 🔥 Check total percent for that employee across ALL departments
    $employeeId   = $data['employee_id'];
    $departmentId = $data['department_id'];
    $positionId   = $data['position_id'];

    // existing row for this exact combination?
    $existing = DB::table('department_positions')
        ->where('employee_id', $employeeId)
        ->where('department_id', $departmentId)
        ->where('position_id', $positionId)
        ->first();

    // Sum of all percents for this employee
    $usedPercent = DB::table('department_positions')
        ->where('employee_id', $employeeId)
        ->sum('percent');

    // if we are updating existing row, remove its current percent from "used"
    if ($existing) {
        $usedPercent -= (float) $existing->percent;
    }

    $remaining = 100 - $usedPercent;

    if ($data['percent'] > $remaining + 0.0001) {
        return response()->json([
            'success'   => false,
            'message'   => 'Der Mitarbeiter ist bereits zu ' . round($usedPercent, 2) . '% verplant. 
                            Verfügbar sind nur noch ' . max(0, round($remaining, 2)) . '%.',
            'used'      => round($usedPercent, 2),
            'remaining' => max(0, round($remaining, 2)),
        ], 422);
    }

    // 4) Save everything
    DB::transaction(function () use ($data, $workingHours) {

        DB::table('department_positions')->updateOrInsert(
            [
                'employee_id'   => $data['employee_id'],
                'department_id' => $data['department_id'],
                'position_id'   => $data['position_id'],
            ],
            [
                'percent'         => $data['percent'],
                'montage_percent' => $data['montage_percent'],
                'office_percent'  => $data['office_percent'],
                'working_hours'   => $workingHours,
                'main'            => null,
                'updated_at'      => now(),
                'created_at'      => now(),
            ]
        );

        DB::table('employee_departments')->updateOrInsert(
            [
                'employee_id'   => $data['employee_id'],
                'department_id' => $data['department_id'],
            ],
            [
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    });

    return response()->json([
        'success' => true,
        'message' => 'Mitarbeiter-Position in der Abteilung wurde gespeichert.'
    ]);
}


public function remainingPercent(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
    ]);

    $employeeId = $request->employee_id;

    $usedPercent = DB::table('department_positions')
        ->where('employee_id', $employeeId)
        ->sum('percent');

    $remaining = max(0, 100 - $usedPercent);

    return response()->json([
        'success'   => true,
        'used'      => round($usedPercent, 2),
        'remaining' => round($remaining, 2),
    ]);
}

public function unassignPosition(Request $request)
{
    $data = $request->validate([
        'employee_id'   => 'required|exists:employees,id',
        'department_id' => 'required|exists:departments,id',
    ]);

    // 🔥 remove from department_positions
    DB::table('department_positions')
        ->where('employee_id', $data['employee_id'])
        ->where('department_id', $data['department_id'])
        ->delete();

    // Optional: if you also mirror that in employee_departments, clean that too
    DB::table('employee_departments')
        ->where('employee_id', $data['employee_id'])
        ->where('department_id', $data['department_id'])
        ->delete();

    return response()->json([
        'success' => true,
        'message' => 'Die Zuordnung des Mitarbeiters zur Abteilung wurde entfernt.'
    ]);
}

public function employeeAllocations($employeeId)
{
    $employee = Employee::findOrFail($employeeId);

    $rows = DB::table('department_positions')
        ->join('departments', 'departments.id', '=', 'department_positions.department_id')
        ->join('positions', 'positions.id', '=', 'department_positions.position_id')
        ->select(
            'department_positions.id',
            'department_positions.department_id',
            'department_positions.position_id',
            'department_positions.percent',
            'department_positions.montage_percent',
            'department_positions.office_percent',
            'department_positions.working_hours',
            'departments.department_name',
            'positions.position'
        )
        ->where('department_positions.employee_id', $employeeId)
        ->orderBy('departments.department_name')
        ->get();

    $total = $rows->sum('percent');

    return response()->json([
        'success'        => true,
        'employee'       => [
            'id'           => $employee->id,
            'name'         => $employee->name,
            'lastname'     => $employee->lastname,
            'working_hour' => $employee->working_hour,
        ],
        'allocations'    => $rows,
        'total_percent'  => round($total, 2),
        'remaining'      => max(0, round(100 - $total, 2)),
    ]);
}

public function updateEmployeeAllocations(Request $request, $employeeId)
{
    $employee = Employee::findOrFail($employeeId);

    $validator = Validator::make($request->all(), [
        'allocations'                      => 'required|array',
        'allocations.*.id'                 => 'required|exists:department_positions,id',
        'allocations.*.percent'            => 'required|numeric|min:0|max:100',
        'allocations.*.montage_percent'    => 'required|numeric|min:0|max:100',
        'allocations.*.office_percent'     => 'required|numeric|min:0|max:100',
        'allocations.*.working_hours'      => 'nullable|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first()
        ], 422);
    }

    $data = $validator->validated();
    $rows = $data['allocations'];

    // Check total percent & montage+office rules
    $sumPercent = 0;
    foreach ($rows as $row) {
        $sumPercent += $row['percent'];

        if (abs(($row['montage_percent'] + $row['office_percent']) - $row['percent']) > 0.0001) {
            return response()->json([
                'success' => false,
                'message' => 'Montage- und Office-Anteil müssen je Zeile zusammen dem Gesamt-Prozent dieser Zeile entsprechen.'
            ], 422);
        }

    }

    if ($sumPercent > 100.0001) {
        return response()->json([
            'success' => false,
            'message' => 'Gesamtsumme aller Prozentwerte darf 100% nicht überschreiten (aktuell: ' . round($sumPercent, 2) . '%).'
        ], 422);
    }

    $baseHours = $employee->working_hour ?? 0;

    DB::transaction(function () use ($rows, $baseHours) {
        foreach ($rows as $row) {
            $workingHours = $row['working_hours'] ?? ($baseHours * ($row['percent'] / 100));

            DB::table('department_positions')
                ->where('id', $row['id'])
                ->update([
                    'percent'         => $row['percent'],
                    'montage_percent' => $row['montage_percent'],
                    'office_percent'  => $row['office_percent'],
                    'working_hours'   => $workingHours,
                    'updated_at'      => now(),
                ]);
        }
    });

    return response()->json([
        'success' => true,
        'message' => 'Auslastung des Mitarbeiters wurde aktualisiert.'
    ]);
}

public function employeesWithPositions(Department $department)
{
    $rows = \App\Models\DepartmentPosition::query()
        ->where('department_positions.department_id', $department->id) // << important
        ->join('employees as e', 'e.id', '=', 'department_positions.employee_id')
        ->join('positions as p', 'p.id', '=', 'department_positions.position_id')
        ->select(
            'department_positions.id',
            'department_positions.percent',
            'department_positions.montage_percent',
            'department_positions.office_percent',
            'department_positions.working_hours',
            'department_positions.position_id',
            'e.id as employee_id',
            'e.name',
            'e.lastname',
            'e.working_hour as base_working_hour',
            'p.position'
        )
        ->orderBy('e.lastname')
        ->orderBy('e.name')
        ->get();

    return response()->json([
        'success'    => true,
        'department' => [
            'id'   => $department->id,
            'name' => $department->department_name,
        ],
        'data'       => $rows,
    ]);
}

public function departmentUpdate(Request $request)
{
    $dp = DepartmentPosition::findOrFail($request->id);

    $validated = $request->validate([
        'percent'         => ['required','numeric','min:0','max:100'],
        'montage_percent' => ['required','numeric','min:0','max:100'],
        'office_percent'  => ['required','numeric','min:0','max:100'],
        'working_hours'   => ['nullable','numeric','min:0'],
        'position_id'     => ['required','exists:positions,id'],
    ]);

    // total percent for this employee across all departments (excluding this row)
    $sumOther = DepartmentPosition::where('employee_id', $dp->employee_id)
        ->where('id', '!=', $dp->id)
        ->sum('percent');

    if ($sumOther + $validated['percent'] > 100 + 0.0001) {
        return response()->json([
            'success' => false,
            'message' => 'Für diese Änderung würde die Gesamt-Auslastung des Mitarbeiters über 100% steigen.',
        ], 422);
    }

    $dp->fill($validated)->save();

    return response()->json([
        'success' => true,
        'message' => 'Zuordnung aktualisiert.',
    ]);
}

}
