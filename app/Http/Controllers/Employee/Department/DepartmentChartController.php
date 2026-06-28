<?php

namespace App\Http\Controllers\Employee\Department;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Branch;

use DB;


class DepartmentChartController extends Controller
{
    
    public function __construct(){
        $this->middleware('auth');
    }

      // Fetch all departments
    public function index(Request $request)
        {
            $branchId = $request->query('branch_id');

            $departments = DB::table('departments')
                            ->leftJoin('employees', 'employees.id', '=', 'departments.department_head')
                            ->where('branch_id', $branchId)
                            ->select('departments.*', 'employees.name as empName', 'employees.lastname as empLastname', 'employees.image as empImage')
                            ->whereNull('departments.deleted_at')
                            ->get();
            return response()->json(['success' => true, 'departments' => $departments]);
        }

    public function getBranches()
    {
        $branches = Branch::where('status', 'Published')->select('id', 'branch')->get();
        return response()->json(['success' => true, 'branches' => $branches]);
    }
    // Store a new department
   public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id',
            'branch_id' => 'required|exists:branches,id' // Ensure branch_id exists
        ]);

        $department = Department::create([
            'department_name' => $request->name,
            'parent_id' => $request->parent_id,
            'branch_id' => $request->branch_id,
            'status' => 'Published'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Department added successfully!',
            'department' => $department
        ]);
    }

  public function update_node(Request $request)
{
    \Log::info("Received Update Request:", $request->all()); // ✅ Debugging

    $department = Department::find($request->id);

    if (!$department) {
        return response()->json([
            'success' => false,
            'message' => 'Department not found'
        ], 404);
    }

    // ✅ Ensure both parent_id and order are updated
    $department->update([
        'parent_id' => $request->parent_id,
        'order' => $request->order  
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Hierarchy updated successfully!',
        'department' => $department
    ]);
}




    // Delete a department and its children recursively
    public function destroy($id)
    {
        $department = Department::find($id);
        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found!'], 404);
        }

        // Delete recursively
        $this->deleteChildren($department);

        return response()->json(['success' => true, 'message' => 'Department and its sub-departments deleted successfully!']);
    }

    // Recursive function to delete department and its children
    private function deleteChildren($department)
    {
        foreach ($department->children as $child) {
            $this->deleteChildren($child);
        }
        $department->delete();
    }

     public function destroy_branch($id)
    {
        $branch = Branch::find($id);
        if (!$branch) {
            return response()->json(['success' => false, 'message' => 'Branch not found!'], 404);
        }

        // Delete all departments associated with this branch
        Department::where('branch_id', $id)->delete();

       
        return response()->json(['success' => true, 'message' => 'Branch and all associated departments deleted successfully!']);
    }
}
