<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerSuggestEmployee;
use App\Models\Employee;
use DB;

class CustomerSuggestEmployeeController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'customer_id'     => 'required|integer|exists:new_leads,id',
        'alternative_id'  => 'required|integer|exists:lead_alternative_adds,id',
        'product_id'      => 'required|integer|exists:article_groups,id',
        'phase_id'        => 'required|integer|exists:task_phases,id',
        'suggestions'     => 'required|array|min:1',
        'suggestions.*.employee_id'   => 'required|exists:employees,id',
        'suggestions.*.department_id' => 'required|exists:departments,id',
        'suggestions.*.role'          => 'required|string|in:team,leader,representative,monteur,obermonteur,helper,innendienst,aussendienst,bauleiter,buchhaltung,techniker,controller',
    ]);

    $customerId    = $request->customer_id;
    $alternativeId = $request->alternative_id;
    $productId     = $request->product_id;
    $phaseId       = $request->phase_id;

    foreach ($request->suggestions as $item) {
        CustomerSuggestEmployee::updateOrCreate([
            'customer_id'    => $customerId,
            'alternative_id' => $alternativeId,
            'product_id'     => $productId,
            'phase_id'       => $phaseId,
            'employee_id'    => $item['employee_id'],
            'department_id'  => $item['department_id'],
        ], [
            'role' => $item['role'],
        ]);
    }

    return response()->json(['status' => 'success', 'message' => 'Mitarbeiter gespeichert.']);
}



    public function get(Request $request)
    {
        $record = CustomerSuggestEmployee::where([
            'customer_id' => $request->customer_id,
            'alternative_id' => $request->alternative_id,
            'product_id' => $request->product_id,
            'phase_id' => $request->phase_id,
        ])->first();

        $employees = collect();

        if ($record && is_array($record->employee_id)) {
            $employees = \App\Models\Employee::whereIn('id', $record->employee_id)->get();
        }

        return response()->json($employees);
    }

public function update(Request $request)
{
    $request->validate([
        'suggestion_id'   => 'required|exists:customer_suggest_employees,id',
        'employee_id'     => 'required|exists:employees,id',
        'department_id'   => 'required|exists:departments,id',
        'role'            => 'required|string',
    ]);

    \App\Models\CustomerSuggestEmployee::where('id', $request->suggestion_id)->update([
        'employee_id'   => $request->employee_id,
        'department_id' => $request->department_id,
        'role'          => $request->role,
    ]);

    return response()->json(['status' => 'updated']);
}

    public function destroy($id)
    {
        \App\Models\CustomerSuggestEmployee::where('id', $id)->delete();

        return response()->json(['status' => 'deleted']);
    }

public function loadDepartment($id)
{
    $departments = DB::table('employee_departments')
        ->where('employee_departments.employee_id', $id) // ✅ explicitly name the table here
        ->join('departments', 'employee_departments.department_id', '=', 'departments.id')
        ->leftJoin('department_positions', function ($join) use ($id) {
            $join->on('department_positions.department_id', '=', 'departments.id')
                ->where('department_positions.employee_id', $id); // ✅ already scoped correctly here
        })
        ->leftJoin('positions', 'department_positions.position_id', '=', 'positions.id')
        ->select(
            'departments.id as department_id',
            'departments.department_name',
            'department_positions.main',
            'positions.position'
        )
        ->get();

    return response()->json($departments);
}

}
