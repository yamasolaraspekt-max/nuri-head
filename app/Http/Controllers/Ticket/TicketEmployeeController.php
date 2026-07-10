<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Problem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketEmployeeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Problem: Rollen-Gate (permission:Problem)
        $this->middleware('permission:Problem,update')->only(['sync']);
    }

    public function index(int $problem): JsonResponse
    {
        $ticket = Problem::whereNull('deleted_at')->findOrFail($problem);

        return response()->json([
            'success' => true,
            'employees' => $this->assignedEmployees($ticket->id),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $search = trim((string) $request->get('q', ''));

        $employees = Employee::query()
            ->select('id', 'name', 'lastname', 'image', 'email', 'phone', 'status_msg')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($x) use ($search) {
                    $x->where('name', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(COALESCE(name,''),' ',COALESCE(lastname,'')) LIKE ?", ["%{$search}%"]);
                });
            })
            ->orderBy('name')
            ->limit(40)
            ->get()
            ->map(fn(Employee $employee) => $this->formatEmployee($employee))
            ->values();

        return response()->json([
            'results' => $employees,
        ]);
    }

    public function sync(Request $request, int $problem): JsonResponse
    {
        $ticket = Problem::whereNull('deleted_at')->findOrFail($problem);

        $data = $request->validate([
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
        ]);

        $employeeIds = collect($data['employee_ids'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        DB::transaction(function () use ($ticket, $employeeIds) {
            DB::table('employee_problem')
                ->where('problem_id', $ticket->id)
                ->delete();

            $now = now();

            foreach ($employeeIds as $employeeId) {
                DB::table('employee_problem')->insert([
                    'problem_id' => $ticket->id,
                    'employee_id' => $employeeId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Ticket-Team wurde gespeichert.',
            'employees' => $this->assignedEmployees($ticket->id),
        ]);
    }

    private function assignedEmployees(int $problemId)
    {
        return Employee::query()
            ->join('employee_problem', 'employee_problem.employee_id', '=', 'employees.id')
            ->where('employee_problem.problem_id', $problemId)
            ->select('employees.id', 'employees.name', 'employees.lastname', 'employees.image', 'employees.email', 'employees.phone')
            ->orderBy('employees.name')
            ->get()
            ->map(fn(Employee $employee) => $this->formatEmployee($employee))
            ->values();
    }

    private function formatEmployee(Employee $employee): array
    {
        $name = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

        return [
            'id' => (int) $employee->id,
            'text' => $name ?: ('#' . $employee->id),
            'name' => $name ?: ('#' . $employee->id),
            'email' => $employee->email,
            'phone' => $employee->phone,
            'status_msg' => $employee->status_msg,
            'image' => !empty($employee->image)
                ? asset('images/employee/' . $employee->image)
                : asset('images/gender/male.png'),
        ];
    }
}
