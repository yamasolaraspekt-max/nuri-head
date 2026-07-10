<?php

namespace App\Http\Controllers\Employee\Position;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DepartmentPosition;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class EmployeeOrganizationController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR: Personal-Rollen-Gate (permission:Employee)
        $this->middleware('permission:Employee,delete')->only(['remove']);
        $this->middleware('permission:Employee,update')->only(['update']);
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.employee.organization.employee_organization');
    }

    /**
     * IMPORTANT:
     * Positions are loaded from the positions table for every department.
     * Existing employee assignments are loaded from department_positions and
     * merged into the matching department_id + position_id box.
     */
    public function data(Request $request): JsonResponse
    {
        $employees = $this->activeEmployeesQuery()
            ->with(['departmentPositions.department:id,department_name', 'departmentPositions.position:id,position'])
            ->orderBy('name')
            ->orderBy('lastname')
            ->get($this->employeeColumns())
            ->map(fn(Employee $employee) => $this->employeePayload($employee));

        $positions = $this->positionsQuery()
            ->orderBy('position')
            ->get($this->positionColumns())
            ->map(fn(Position $position) => $this->positionPayload($position))
            ->values();

        $departments = $this->departmentsQuery()
            ->get($this->departmentColumns());

        $assignmentRows = DepartmentPosition::query()
            ->with([
                'employee' => function ($query) {
                    $query->select($this->employeeRelationColumns());
                },
                'department:id,department_name',
                'position:id,position',
            ])
            ->whereNotNull('department_id')
            ->whereNotNull('position_id')
            ->whereNotNull('employee_id')
            ->get();

        $assignments = $assignmentRows->groupBy(fn(DepartmentPosition $row) => $row->department_id . ':' . $row->position_id);

        $departmentPayload = $departments->map(function (Department $department) use ($positions, $assignments) {
            $departmentPositions = $positions->map(function (array $position) use ($department, $assignments) {
                $key = $department->id . ':' . $position['id'];
                $positionAssignments = ($assignments->get($key) ?? collect())
                    ->filter(fn(DepartmentPosition $assignment) => $assignment->employee)
                    ->values()
                    ->map(fn(DepartmentPosition $assignment) => $this->assignmentPayload($assignment))
                    ->values();

                return array_merge($position, [
                    'assignments' => $positionAssignments,
                    'assignment_count' => $positionAssignments->count(),
                ]);
            })->values();

            return [
                'id' => $department->id,
                'name' => $department->department_name,
                'parent_id' => $department->parent_id ?? null,
                'branch_id' => $department->branch_id ?? null,
                'order' => $department->order ?? null,
                'positions' => $departmentPositions,
                'position_count' => $departmentPositions->count(),
                'assignment_count' => $departmentPositions->sum('assignment_count'),
            ];
        })->values();

        return response()->json([
            'employees' => $employees,
            'positions' => $positions,
            'departments' => $departmentPayload,
            'stats' => [
                'employees' => $employees->count(),
                'departments' => $departmentPayload->count(),
                'positions' => $positions->count(),
                'assignments' => $assignmentRows->count(),
            ],
        ]);
    }

    public function assign(Request $request): JsonResponse
    {
        $data = $this->validateSingleAssignment($request);

        $assignments = $this->saveAssignments([
            (int) $data['employee_id'],
        ], (int) $data['department_id'], (int) $data['position_id'], $data);

        return response()->json([
            'message' => 'Mitarbeiter wurde zugeordnet.',
            'assignments' => $assignments,
            'assignment' => $assignments[0] ?? null,
        ]);
    }

    public function assignMultiple(Request $request): JsonResponse
    {
        $data = $this->validateMultipleAssignment($request);

        $assignments = $this->saveAssignments(
            array_map('intval', $data['employee_ids']),
            (int) $data['department_id'],
            (int) $data['position_id'],
            $data
        );

        return response()->json([
            'message' => count($assignments) . ' Mitarbeiter wurden zugeordnet.',
            'assignments' => $assignments,
            'count' => count($assignments),
        ]);
    }

    public function bulkAssign(Request $request): JsonResponse
    {
        $data = $request->validate([
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'mode' => ['required', Rule::in(['all_departments_same_position', 'all_department_positions'])],
            'position_id' => ['nullable', 'integer', 'exists:positions,id'],
            'position_ids' => ['nullable', 'array'],
            'position_ids.*' => ['integer', 'exists:positions,id'],
            'department_ids' => ['nullable', 'array'],
            'department_ids.*' => ['integer', 'exists:departments,id'],
            'percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'montage_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'office_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'working_hours' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'main' => ['nullable', Rule::in(['Yes', 'No'])],
        ]);

        $employeeIds = $data['employee_ids'] ?? [];
        if (empty($employeeIds) && !empty($data['employee_id'])) {
            $employeeIds = [(int) $data['employee_id']];
        }
        $employeeIds = array_values(array_unique(array_map('intval', $employeeIds)));

        if (empty($employeeIds)) {
            return response()->json(['message' => 'Bitte wählen Sie mindestens einen Mitarbeiter.'], 422);
        }

        if ($data['mode'] === 'all_departments_same_position' && empty($data['position_id'])) {
            return response()->json(['message' => 'Bitte wählen Sie eine Position.'], 422);
        }

        $departmentIds = $data['department_ids'] ?? $this->departmentsQuery()->pluck('id')->toArray();
        $departmentIds = array_values(array_unique(array_map('intval', $departmentIds)));

        if ($data['mode'] === 'all_departments_same_position') {
            $positionIds = [(int) $data['position_id']];
        } else {
            $positionIds = $data['position_ids'] ?? $this->positionsQuery()->pluck('id')->toArray();
            $positionIds = array_values(array_unique(array_map('intval', $positionIds)));
        }

        if (empty($departmentIds) || empty($positionIds)) {
            return response()->json(['message' => 'Keine Abteilung oder Position gefunden.'], 422);
        }

        $count = DB::transaction(function () use ($employeeIds, $departmentIds, $positionIds, $data) {
            $count = 0;

            foreach ($departmentIds as $departmentId) {
                foreach ($positionIds as $positionId) {
                    foreach ($employeeIds as $employeeId) {
                        $this->saveOneAssignment($employeeId, $departmentId, $positionId, $data);
                        $count++;
                    }
                }
            }

            return $count;
        });

        return response()->json([
            'message' => $count . ' Zuordnungen wurden gespeichert.',
            'count' => $count,
        ]);
    }

    public function update(Request $request, DepartmentPosition $departmentPosition): JsonResponse
    {
        $data = $request->validate([
            'percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'montage_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'office_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'working_hours' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'main' => ['nullable', Rule::in(['Yes', 'No'])],
        ]);

        DB::transaction(function () use ($departmentPosition, $data) {
            if (($data['main'] ?? 'No') === 'Yes') {
                DepartmentPosition::where('employee_id', $departmentPosition->employee_id)
                    ->where('id', '!=', $departmentPosition->id)
                    ->update(['main' => 'No']);
            }

            $this->applyAssignmentValues($departmentPosition, $data);
            $departmentPosition->save();
        });

        $fresh = $departmentPosition->fresh([
            'employee' => function ($query) {
                $query->select($this->employeeRelationColumns());
            },
            'department:id,department_name',
            'position:id,position',
        ]);

        return response()->json([
            'message' => 'Zuordnung wurde aktualisiert.',
            'assignment' => $this->assignmentPayload($fresh),
        ]);
    }

    public function remove(DepartmentPosition $departmentPosition): JsonResponse
    {
        $departmentPosition->delete();

        return response()->json(['message' => 'Zuordnung wurde entfernt.']);
    }

    private function validateSingleAssignment(Request $request): array
    {
        return $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'position_id' => ['required', 'integer', 'exists:positions,id'],
            'percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'montage_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'office_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'working_hours' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'main' => ['nullable', Rule::in(['Yes', 'No'])],
        ]);
    }

    private function validateMultipleAssignment(Request $request): array
    {
        return $request->validate([
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'position_id' => ['required', 'integer', 'exists:positions,id'],
            'percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'montage_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'office_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'working_hours' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'main' => ['nullable', Rule::in(['Yes', 'No'])],
        ]);
    }

    private function saveAssignments(array $employeeIds, int $departmentId, int $positionId, array $data): array
    {
        return DB::transaction(function () use ($employeeIds, $departmentId, $positionId, $data) {
            $saved = [];

            foreach (array_values(array_unique($employeeIds)) as $employeeId) {
                $assignment = $this->saveOneAssignment((int) $employeeId, $departmentId, $positionId, $data);
                $saved[] = $this->assignmentPayload($assignment->load([
                    'employee' => function ($query) {
                        $query->select($this->employeeRelationColumns());
                    },
                    'department:id,department_name',
                    'position:id,position',
                ]));
            }

            return $saved;
        });
    }

    private function saveOneAssignment(int $employeeId, int $departmentId, int $positionId, array $data): DepartmentPosition
    {
        if (($data['main'] ?? 'No') === 'Yes') {
            DepartmentPosition::where('employee_id', $employeeId)->update(['main' => 'No']);
        }

        $assignment = DepartmentPosition::firstOrNew([
            'employee_id' => $employeeId,
            'department_id' => $departmentId,
            'position_id' => $positionId,
        ]);

        $this->applyAssignmentValues($assignment, $data);
        $assignment->save();

        return $assignment;
    }

    private function applyAssignmentValues(DepartmentPosition $assignment, array $data): void
    {
        $assignment->percent = $data['percent'] ?? 100;
        $assignment->montage_percent = $data['montage_percent'] ?? 0;
        $assignment->office_percent = $data['office_percent'] ?? 100;
        $assignment->main = $data['main'] ?? 'No';

        if (Schema::hasColumn('department_positions', 'working_hours')) {
            $assignment->working_hours = array_key_exists('working_hours', $data) ? $data['working_hours'] : null;
        }
    }

    private function activeEmployeesQuery()
    {
        $query = Employee::query();

        if (Schema::hasColumn('employees', 'status')) {
            $query->where(function ($statusQuery) {
                $statusQuery->whereIn('status', ['Active', 'active', 'Published', 'published', 'Aktiv', 'aktiv', '1', 1])
                    ->orWhereNull('status');
            });
        }

        /*
         * IMPORTANT:
         * Do NOT compare DATE column termination with empty string.
         * MySQL strict mode throws: Incorrect DATE value: ''.
         *
         * Active employee = no termination date OR termination date is today/future.
         */
        if (Schema::hasColumn('employees', 'termination')) {
            $query->where(function ($terminationQuery) {
                $terminationQuery->whereNull('termination')
                    ->orWhereDate('termination', '>=', now()->toDateString());
            });
        }

        return $query;
    }    private function departmentsQuery()
    {
        $query = Department::query();

        if (Schema::hasColumn('departments', 'status')) {
            $query->where(function ($statusQuery) {
                $statusQuery->whereIn('status', ['Published', 'published', 'Active', 'active', 'Aktiv', 'aktiv', '1', 1])
                    ->orWhereNull('status');
            });
        }

        if (Schema::hasColumn('departments', 'order')) {
            $query->orderBy('order');
        }

        return $query->orderBy('department_name');
    }

    private function positionsQuery()
    {
        $query = Position::query();

        if (Schema::hasColumn('positions', 'status')) {
            $query->where(function ($statusQuery) {
                $statusQuery->whereIn('status', ['Published', 'published', 'Active', 'active', 'Aktiv', 'aktiv', '1', 1])
                    ->orWhereNull('status');
            });
        }

        return $query;
    }

    private function employeePayload(Employee $employee): array
    {
        $displayName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
        $main = $employee->departmentPositions->firstWhere('main', 'Yes');

        return [
            'id' => $employee->id,
            'name' => $employee->name,
            'lastname' => $employee->lastname,
            'display_name' => $displayName ?: ('#' . $employee->id),
            'image' => $employee->image ?? null,
            'color' => $employee->color ?? null,
            'assignment_count' => $employee->departmentPositions->count(),
            'main_assignment' => $main ? [
                'department' => optional($main->department)->department_name,
                'position' => optional($main->position)->position,
            ] : null,
        ];
    }

    private function positionPayload(Position $position): array
    {
        return [
            'id' => $position->id,
            'name' => $position->position,
            'description' => $position->description ?? null,
            'status' => $position->status ?? null,
        ];
    }

    private function assignmentPayload(DepartmentPosition $assignment): array
    {
        $employee = $assignment->employee;
        $displayName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

        return [
            'id' => $assignment->id,
            'employee_id' => $assignment->employee_id,
            'department_id' => $assignment->department_id,
            'position_id' => $assignment->position_id,
            'employee_name' => $displayName ?: ('#' . $assignment->employee_id),
            'employee_image' => $employee->image ?? null,
            'employee_color' => $employee->color ?? null,
            'department_name' => optional($assignment->department)->department_name,
            'position_name' => optional($assignment->position)->position,
            'percent' => (float) ($assignment->percent ?? 0),
            'montage_percent' => (float) ($assignment->montage_percent ?? 0),
            'office_percent' => (float) ($assignment->office_percent ?? 0),
            'working_hours' => Schema::hasColumn('department_positions', 'working_hours') ? ($assignment->working_hours ?? null) : null,
            'main' => $assignment->main ?? 'No',
        ];
    }

    private function employeeColumns(): array
    {
        return array_values(array_filter([
            'id',
            Schema::hasColumn('employees', 'name') ? 'name' : null,
            Schema::hasColumn('employees', 'lastname') ? 'lastname' : null,
            Schema::hasColumn('employees', 'image') ? 'image' : null,
            Schema::hasColumn('employees', 'color') ? 'color' : null,
        ]));
    }

    private function employeeRelationColumns(): array
    {
        return $this->employeeColumns();
    }

    private function departmentColumns(): array
    {
        return array_values(array_filter([
            'id',
            'department_name',
            Schema::hasColumn('departments', 'parent_id') ? 'parent_id' : null,
            Schema::hasColumn('departments', 'branch_id') ? 'branch_id' : null,
            Schema::hasColumn('departments', 'order') ? 'order' : null,
        ]));
    }

    private function positionColumns(): array
    {
        return array_values(array_filter([
            'id',
            'position',
            Schema::hasColumn('positions', 'description') ? 'description' : null,
            Schema::hasColumn('positions', 'status') ? 'status' : null,
        ]));
    }
}
