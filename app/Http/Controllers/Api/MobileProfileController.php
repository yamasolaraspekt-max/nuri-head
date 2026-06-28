<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MobileProfileController extends Controller
{
    public function show(Request $request)
    {
        $auth = $request->user();

        if (!$auth) {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | Never use users.id as employee id.
        |
        | Correct rules:
        | - If token belongs to Employee: employee id = auth()->id()
        | - If token belongs to User: employee id = auth()->user()->name
        |--------------------------------------------------------------------------
        */
        $employeeId = $this->resolveEmployeeId($auth);

        if (!$employeeId) {
            return response()->json([
                'ok' => false,
                'message' => 'Employee not found.',
                'debug' => [
                    'auth_model' => get_class($auth),
                    'auth_id' => $auth->id ?? null,
                    'auth_name' => $auth->name ?? null,
                    'auth_email' => $auth->email ?? null,
                    'auth_employee_id' => $auth->employee_id ?? null,
                ],
            ], 403);
        }

        $employee = DB::table('employees')
            ->where('id', $employeeId)
            ->when(
                Schema::hasColumn('employees', 'deleted_at'),
                fn($query) => $query->whereNull('deleted_at')
            )
            ->first();

        if (!$employee) {
            return response()->json([
                'ok' => false,
                'message' => 'Employee not found.',
                'debug' => [
                    'resolved_employee_id' => $employeeId,
                    'auth_model' => get_class($auth),
                    'auth_id' => $auth->id ?? null,
                    'auth_name' => $auth->name ?? null,
                ],
            ], 404);
        }

        $departments = $this->loadDepartments((int) $employeeId);

        $employeePayload = $this->employeePayload($employee);

        return response()->json([
            'ok' => true,

            'auth' => [
                'model' => get_class($auth),
                'id' => $auth->id ?? null,
                'name' => $auth->name ?? null,
                'email' => $auth->email ?? null,
            ],

            'employee_id' => (int) $employeeId,
            'employee' => $employeePayload,

            'data' => [
                'employee' => $employeePayload,
                'departments' => $departments,
            ],
        ]);
    }

    private function resolveEmployeeId($auth): ?int
    {
        /*
        |--------------------------------------------------------------------------
        | Correct mobile token case
        |--------------------------------------------------------------------------
        | If MobileAuthController creates token on Employee, this is the correct ID.
        |--------------------------------------------------------------------------
        */
        if ($auth instanceof Employee) {
            return (int) $auth->id;
        }

        /*
        |--------------------------------------------------------------------------
        | Correct User token fallback
        |--------------------------------------------------------------------------
        | In nuri-head users.name = employees.id.
        |--------------------------------------------------------------------------
        */
        if ($auth instanceof User) {
            if (!empty($auth->name) && is_numeric($auth->name)) {
                return (int) $auth->name;
            }

            if (!empty($auth->employee_id) && is_numeric($auth->employee_id)) {
                return (int) $auth->employee_id;
            }

            if ($auth->employee?->id) {
                return (int) $auth->employee->id;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Generic fallback for old auth objects.
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | We still prefer name before id, because users.name is employee id.
        |--------------------------------------------------------------------------
        */
        if (!empty($auth->name) && is_numeric($auth->name)) {
            return (int) $auth->name;
        }

        if (!empty($auth->employee_id) && is_numeric($auth->employee_id)) {
            return (int) $auth->employee_id;
        }

        /*
        |--------------------------------------------------------------------------
        | Last fallback by email.
        |--------------------------------------------------------------------------
        */
        if (
            !empty($auth->email)
            && Schema::hasTable('employees')
            && Schema::hasColumn('employees', 'email')
        ) {
            $employeeId = DB::table('employees')
                ->where('email', $auth->email)
                ->value('id');

            if ($employeeId) {
                return (int) $employeeId;
            }
        }

        return null;
    }

    private function loadDepartments(int $employeeId)
    {
        $deptPositions = collect();

        if (
            Schema::hasTable('department_positions')
            && Schema::hasTable('departments')
            && Schema::hasTable('positions')
        ) {
            $deptPositions = DB::table('department_positions as dp')
                ->leftJoin('departments as d', 'd.id', '=', 'dp.department_id')
                ->leftJoin('positions as p', 'p.id', '=', 'dp.position_id')
                ->where('dp.employee_id', $employeeId)
                ->select([
                    'dp.id as pivot_id',
                    'dp.employee_id',
                    'dp.department_id',
                    'dp.position_id',
                    'dp.percent',
                    'dp.montage_percent',
                    'dp.office_percent',
                    'dp.working_hours',
                    'dp.main',

                    'd.department_name',
                    'd.parent_id',
                    'd.branch_id',
                    'd.department_head',
                    'd.head_representative',
                    'd.status as department_status',

                    'p.position as position_name',
                    'p.status as position_status',
                ])
                ->orderByRaw("CASE WHEN LOWER(COALESCE(dp.main, '')) IN ('1','true','yes','main') THEN 0 ELSE 1 END")
                ->orderBy('d.department_name')
                ->get();
        }

        $deptOnly = collect();

        if (
            Schema::hasTable('employee_departments')
            && Schema::hasTable('departments')
        ) {
            $deptOnly = DB::table('employee_departments as ed')
                ->leftJoin('departments as d', 'd.id', '=', 'ed.department_id')
                ->where('ed.employee_id', $employeeId)
                ->select([
                    DB::raw('NULL as pivot_id'),
                    'ed.employee_id',
                    'ed.department_id',
                    DB::raw('NULL as position_id'),
                    DB::raw('NULL as percent'),
                    DB::raw('NULL as montage_percent'),
                    DB::raw('NULL as office_percent'),
                    DB::raw('NULL as working_hours'),
                    DB::raw('NULL as main'),

                    'd.department_name',
                    'd.parent_id',
                    'd.branch_id',
                    'd.department_head',
                    'd.head_representative',
                    'd.status as department_status',

                    DB::raw('NULL as position_name'),
                    DB::raw('NULL as position_status'),
                ])
                ->get();
        }

        $all = $deptPositions
            ->concat($deptOnly)
            ->unique(fn($row) => (string) ($row->department_id ?? '') . ':' . (string) ($row->position_id ?? ''))
            ->values();

        $headIds = $all->pluck('department_head')->filter()->unique()->values()->all();
        $repIds = $all->pluck('head_representative')->filter()->unique()->values()->all();
        $needIds = array_values(array_unique(array_merge($headIds, $repIds)));

        $headsMap = [];

        if (!empty($needIds) && Schema::hasTable('employees')) {
            $headsMap = DB::table('employees')
                ->whereIn('id', $needIds)
                ->select(['id', 'name', 'lastname'])
                ->get()
                ->keyBy('id')
                ->map(fn($employee) => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')))
                ->toArray();
        }

        return $all->map(function ($row) use ($headsMap) {
            return [
                'department_id' => $row->department_id,
                'department_name' => $row->department_name,
                'department_status' => $row->department_status,
                'branch_id' => $row->branch_id,
                'parent_id' => $row->parent_id,

                'position_id' => $row->position_id,
                'position_name' => $row->position_name,
                'position_status' => $row->position_status,

                'percent' => $row->percent,
                'montage_percent' => $row->montage_percent,
                'office_percent' => $row->office_percent,
                'working_hours' => $row->working_hours,
                'main' => $row->main,

                'department_head_id' => $row->department_head,
                'department_head_name' => $row->department_head
                    ? ($headsMap[$row->department_head] ?? null)
                    : null,

                'head_representative_id' => $row->head_representative,
                'head_representative_name' => $row->head_representative
                    ? ($headsMap[$row->head_representative] ?? null)
                    : null,
            ];
        })->values();
    }

    private function employeePayload($employee): array
    {
        $fullName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

        return [
            /*
            |--------------------------------------------------------------------------
            | IMPORTANT:
            | These are employees.id, never users.id.
            |--------------------------------------------------------------------------
            */
            'id' => (int) $employee->id,
            'employee_id' => (int) $employee->id,

            'title' => $employee->title ?? null,
            'name' => $employee->name ?? null,
            'lastname' => $employee->lastname ?? null,
            'full_name' => $fullName !== '' ? $fullName : ('Mitarbeiter #' . $employee->id),

            'email' => $employee->email ?? null,
            'phone' => $employee->phone ?? null,
            'status' => $employee->status ?? null,
            'role' => $employee->status ?? null,
            'branch' => $employee->branch ?? null,
            'color' => $employee->color ?? null,

            'image' => $this->resolvePhotoUrl($employee->image ?? null),
            'img' => $this->resolvePhotoUrl($employee->image ?? null),
            'photo_url' => $this->resolvePhotoUrl($employee->image ?? null),

            /*
            |--------------------------------------------------------------------------
            | Raw columns for profile page if needed.
            |--------------------------------------------------------------------------
            */
            'raw' => $employee,
        ];
    }

    private function resolvePhotoUrl($imageName): ?string
    {
        if (!$imageName) {
            return null;
        }

        $imageName = trim((string) $imageName);

        if ($imageName === '') {
            return null;
        }

        if (Str::startsWith($imageName, ['http://', 'https://'])) {
            return $imageName;
        }

        if (Str::startsWith($imageName, ['/'])) {
            return url($imageName);
        }

        $clean = ltrim($imageName, '/');

        if (Str::contains($clean, 'images/employee')) {
            return url($clean);
        }

        return url('images/employee/' . $clean);
    }
}