<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'passcode' => ['required', 'string', 'min:2', 'max:32'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $passcode = trim((string) $data['passcode']);

        /*
        |--------------------------------------------------------------------------
        | 1) Find employee by passcode
        |--------------------------------------------------------------------------
        | Passcode is stored on employees table.
        |--------------------------------------------------------------------------
        */
        $passcodeEmployee = $this->findEmployeeByPasscode($passcode);

        if (!$passcodeEmployee) {
            throw ValidationException::withMessages([
                'passcode' => ['Passcode ist ungültig.'],
            ]);
        }

        if (Hash::needsRehash((string) $passcodeEmployee->passcode)) {
            $passcodeEmployee->passcode = Hash::make($passcode);
            $passcodeEmployee->save();
        }

        /*
        |--------------------------------------------------------------------------
        | 2) Find User by users.name = employees.id
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | In this project users.name stores the real employee ID.
        |--------------------------------------------------------------------------
        */
        $user = User::query()
            ->where('name', (string) $passcodeEmployee->id)
            ->first();

        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'Für diesen Mitarbeiter wurde kein Benutzer gefunden.',
                'debug' => [
                    'passcode_employee_id' => $passcodeEmployee->id,
                    'expected_users_name' => (string) $passcodeEmployee->id,
                ],
            ], 403);
        }

        if (Schema::hasColumn('users', 'is_active') && isset($user->is_active) && !$user->is_active) {
            return response()->json([
                'ok' => false,
                'message' => 'Benutzer ist nicht aktiv.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | 3) Real employee ID comes from users.name
        |--------------------------------------------------------------------------
        | Do NOT use users.id as employee_id.
        |--------------------------------------------------------------------------
        */
        $employeeId = is_numeric($user->name) ? (int) $user->name : null;

        if (!$employeeId) {
            return response()->json([
                'ok' => false,
                'message' => 'Benutzer ist keinem Mitarbeiter zugeordnet.',
                'debug' => [
                    'user_id' => $user->id,
                    'users_name' => $user->name,
                ],
            ], 403);
        }

        $employee = Employee::query()
            ->where('id', $employeeId)
            ->first();

        if (!$employee) {
            return response()->json([
                'ok' => false,
                'message' => 'Mitarbeiter wurde nicht gefunden.',
                'debug' => [
                    'user_id' => $user->id,
                    'employee_id_from_users_name' => $employeeId,
                ],
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | 4) Token must belong to User
        |--------------------------------------------------------------------------
        | This prevents Employee::getAuthIdentifier() throttle/auth errors.
        |--------------------------------------------------------------------------
        */
        $tokenName = 'mobile' . (!empty($data['device_name']) ? ' - ' . $data['device_name'] : '');
        $token = $user->createToken(substr($tokenName, 0, 255))->plainTextToken;

        return response()->json([
            'ok' => true,
            'token' => $token,

            'user' => [
                'id' => (int) $user->id,          // user id
                'employee_id' => $employeeId,     // real employee id
                'name' => (string) $user->name,   // employee id stored as string
                'email' => $user->email,
            ],

            'employee' => $this->employeePayload($employee),
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Correct nuri-head rule:
        | users.name = employees.id
        |--------------------------------------------------------------------------
        */
        $employeeId = is_numeric($user->name ?? null) ? (int) $user->name : null;
        $employee = $employeeId ? Employee::find($employeeId) : null;

        return response()->json([
            'ok' => true,

            'user' => [
                'id' => (int) $user->id,
                'employee_id' => $employeeId,
                'name' => $user->name,
                'email' => $user->email,
            ],

            'employee' => $employee ? $this->employeePayload($employee) : null,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'ok' => true,
        ]);
    }

    private function findEmployeeByPasscode(string $passcode): ?Employee
    {
        $query = Employee::query()
            ->whereNotNull('passcode');

        if (Schema::hasColumn('employees', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $candidates = $query
            ->select([
                'id',
                'title',
                'name',
                'lastname',
                'status',
                'email',
                'phone',
                'image',
                'branch',
                'passcode',
            ])
            ->get();

        return $candidates->first(function ($employee) use ($passcode) {
            return is_string($employee->passcode)
                && $employee->passcode !== ''
                && Hash::check($passcode, $employee->passcode);
        });
    }

    private function employeePayload(Employee $employee): array
    {
        $fullName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

        return [
            /*
            |--------------------------------------------------------------------------
            | IMPORTANT:
            | id and employee_id are employees.id, not users.id.
            |--------------------------------------------------------------------------
            */
            'id' => (int) $employee->id,
            'employee_id' => (int) $employee->id,

            'name' => $employee->name,
            'lastname' => $employee->lastname,
            'full_name' => $fullName !== '' ? $fullName : ('Mitarbeiter #' . $employee->id),

            'title' => $employee->title,
            'role' => $employee->status ?? null,
            'status' => $employee->status ?? null,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'branch' => $employee->branch,

            'image' => $this->employeeImageUrl($employee->image ?? null),
            'img' => $this->employeeImageUrl($employee->image ?? null),
            'photo_url' => $this->employeeImageUrl($employee->image ?? null),
        ];
    }

    private function employeeImageUrl(?string $image): ?string
    {
        if (!$image) {
            return null;
        }

        $image = trim($image);

        if ($image === '') {
            return null;
        }

        if (Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        if (Str::startsWith($image, ['/'])) {
            return url($image);
        }

        if (Str::contains($image, 'images/employee/')) {
            return url($image);
        }

        return url('images/employee/' . ltrim($image, '/'));
    }
}