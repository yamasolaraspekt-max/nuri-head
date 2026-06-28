<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRoll;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserRollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = $this->usersQuery()->get()->map(fn($user) => $this->formatUser($user));
        $modules = $this->permissionModules();

        return view('admin.user.user_roll', compact('users', 'modules'));
    }

    public function ajaxIndex(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));
        $module = trim((string) $request->query('module', ''));
        $userId = $request->query('user_id');
        $role = trim((string) $request->query('role', ''));
        $perPage = (int) $request->query('per_page', 10);

        $perPage = in_array($perPage, [10, 15, 25, 50, 100], true) ? $perPage : 10;
        $modules = $this->permissionModules();

        $usersQuery = $this->usersQuery()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('users.email', 'LIKE', "%{$search}%")
                        ->orWhere('employees.name', 'LIKE', "%{$search}%")
                        ->orWhere('employees.lastname', 'LIKE', "%{$search}%")
                        ->orWhereExists(function ($sq) use ($search) {
                            $sq->select(DB::raw(1))
                                ->from('user_rolls')
                                ->whereColumn('user_rolls.user_id', 'users.id')
                                ->where('user_rolls.item_id', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->when($module !== '', function ($q) use ($module) {
                $q->whereExists(function ($sq) use ($module) {
                    $sq->select(DB::raw(1))
                        ->from('user_rolls')
                        ->whereColumn('user_rolls.user_id', 'users.id')
                        ->where('user_rolls.item_id', $module);
                });
            })
            ->when($userId !== null && $userId !== '', function ($q) use ($userId) {
                $q->where('users.id', (int) $userId);
            })
            ->when($role !== '', function ($q) use ($role) {
                if ($role === 'admin') {
                    $q->where('users.is_admin', true);
                }

                if ($role === 'user') {
                    $q->where(function ($sub) {
                        $sub->whereNull('users.is_admin')->orWhere('users.is_admin', false);
                    });
                }
            })
            ->orderBy('employees.name')
            ->orderBy('users.email');

        $users = $usersQuery->paginate($perPage)->withQueryString();
        $userIds = collect($users->items())->pluck('id')->filter()->values();

        $rolls = UserRoll::query()
            ->whereIn('user_id', $userIds)
            ->when($module !== '', fn($q) => $q->where('item_id', $module))
            ->orderBy('item_id')
            ->get()
            ->groupBy('user_id');

        $rows = collect($users->items())->map(function ($user) use ($rolls, $modules) {
            $formattedUser = $this->formatUser($user);
            $userRolls = $rolls->get($formattedUser->id, collect());

            $permissions = $userRolls->map(function ($roll) use ($modules) {
                return [
                    'id' => $roll->id,
                    'item_id' => $roll->item_id,
                    'module_label' => $modules[$roll->item_id] ?? $roll->item_id,
                    'is_read' => (bool) $roll->is_read,
                    'is_add' => (bool) $roll->is_add,
                    'is_update' => (bool) $roll->is_update,
                    'is_delete' => (bool) $roll->is_delete,
                    'created_at' => optional($roll->created_at)->format('d.m.Y H:i'),
                    'updated_at' => optional($roll->updated_at)->format('d.m.Y H:i'),
                ];
            })->values();

            return [
                'user_id' => $formattedUser->id,
                'user_name' => $formattedUser->display_name,
                'email' => $formattedUser->email,
                'role' => (bool) $formattedUser->is_admin ? 'Admin' : 'User',
                'is_active' => (bool) $formattedUser->is_active,
                'permission_count' => $permissions->count(),
                'permissions' => $permissions,
                'matrix' => $this->matrixForUser((int) $formattedUser->id, $modules),
            ];
        });

        return response()->json([
            'success' => true,
            'analytics' => $this->analytics(),
            'rows' => $rows,
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
                'has_more_pages' => $users->hasMorePages(),
            ],
            'can' => [
                'add' => auth()->user()->hasPermission('Users', 'add'),
                'update' => auth()->user()->hasPermission('Users', 'update'),
                'delete' => auth()->user()->hasPermission('Users', 'delete'),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->has('permissions')) {
            return $this->bulkSave($request);
        }

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'item_id' => ['required', 'string', 'max:100', Rule::in(array_keys($this->permissionModules()))],
            'is_read' => ['nullable'],
            'is_update' => ['nullable'],
            'is_add' => ['nullable'],
            'is_delete' => ['nullable'],
        ]);

        $roll = UserRoll::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'item_id' => $validated['item_id'],
            ],
            [
                'is_read' => $request->boolean('is_read'),
                'is_update' => $request->boolean('is_update'),
                'is_add' => $request->boolean('is_add'),
                'is_delete' => $request->boolean('is_delete'),
            ]
        );

        if (!$roll->is_read && !$roll->is_update && !$roll->is_add && !$roll->is_delete) {
            $roll->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Berechtigung wurde erfolgreich gespeichert.',
            'id' => $roll->id ?? null,
        ]);
    }

    public function update(Request $request, UserRoll $userRoll): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'item_id' => [
                'required',
                'string',
                'max:100',
                Rule::in(array_keys($this->permissionModules())),
                Rule::unique('user_rolls', 'item_id')
                    ->where(fn($q) => $q->where('user_id', $request->integer('user_id')))
                    ->ignore($userRoll->id),
            ],
            'is_read' => ['nullable'],
            'is_update' => ['nullable'],
            'is_add' => ['nullable'],
            'is_delete' => ['nullable'],
        ]);

        $userRoll->update([
            'user_id' => $validated['user_id'],
            'item_id' => $validated['item_id'],
            'is_read' => $request->boolean('is_read'),
            'is_update' => $request->boolean('is_update'),
            'is_add' => $request->boolean('is_add'),
            'is_delete' => $request->boolean('is_delete'),
        ]);

        if (!$userRoll->is_read && !$userRoll->is_update && !$userRoll->is_add && !$userRoll->is_delete) {
            $userRoll->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Berechtigung wurde erfolgreich aktualisiert.',
        ]);
    }

    public function destroy(UserRoll $userRoll): JsonResponse
    {
        $userRoll->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berechtigung wurde gelöscht.',
        ]);
    }

    private function bulkSave(Request $request): JsonResponse
    {
        $modules = $this->permissionModules();

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'permissions' => ['required', 'array'],
            'permissions.*.item_id' => ['required', 'string', Rule::in(array_keys($modules))],
            'permissions.*.is_read' => ['nullable'],
            'permissions.*.is_add' => ['nullable'],
            'permissions.*.is_update' => ['nullable'],
            'permissions.*.is_delete' => ['nullable'],
        ]);

        $userId = (int) $validated['user_id'];
        $saved = 0;
        $deleted = 0;

        DB::transaction(function () use ($request, $userId, &$saved, &$deleted) {
            foreach ((array) $request->input('permissions', []) as $permission) {
                $itemId = (string) ($permission['item_id'] ?? '');
                $values = [
                    'is_read' => filter_var($permission['is_read'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'is_add' => filter_var($permission['is_add'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'is_update' => filter_var($permission['is_update'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'is_delete' => filter_var($permission['is_delete'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ];

                $hasAnyPermission = $values['is_read'] || $values['is_add'] || $values['is_update'] || $values['is_delete'];

                if (!$hasAnyPermission) {
                    $deleted += UserRoll::query()
                        ->where('user_id', $userId)
                        ->where('item_id', $itemId)
                        ->delete();
                    continue;
                }

                UserRoll::updateOrCreate(
                    ['user_id' => $userId, 'item_id' => $itemId],
                    $values
                );

                $saved++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Berechtigungen wurden gespeichert. Gespeichert: {$saved}. Entfernt: {$deleted}.",
            'saved' => $saved,
            'deleted' => $deleted,
        ]);
    }

    private function usersQuery()
    {
        return User::query()
            ->leftJoin('employees', 'employees.id', '=', 'users.name')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.is_admin',
                'users.is_active',
                'employees.name as employee_name',
                'employees.lastname as employee_lastname',
            ]);
    }

    private function formatUser($user)
    {
        $fullName = trim(($user->employee_name ?? '') . ' ' . ($user->employee_lastname ?? ''));
        $user->display_name = $fullName !== '' ? $fullName : ($user->email ?? ('User #' . $user->id));

        return $user;
    }

    private function matrixForUser(int $userId, array $modules): array
    {
        $existing = UserRoll::query()
            ->where('user_id', $userId)
            ->get()
            ->keyBy('item_id');

        return collect($modules)->map(function ($label, $key) use ($existing) {
            $roll = $existing->get($key);

            return [
                'item_id' => $key,
                'module_label' => $label,
                'is_read' => $roll ? (bool) $roll->is_read : false,
                'is_add' => $roll ? (bool) $roll->is_add : false,
                'is_update' => $roll ? (bool) $roll->is_update : false,
                'is_delete' => $roll ? (bool) $roll->is_delete : false,
            ];
        })->values()->all();
    }

    private function permissionModules(): array
    {
        return config('permissions', [
            'Administrator' => 'Administrator',
            'Employee' => 'Personal',
            'Customer' => 'Kunden',
            'Problem' => 'Problem',
            'Product' => 'Artikelverwaltung',
            'Error' => 'Fehler',
            'Users' => 'Benutzerverwaltung',
            'Comment' => 'Kommentare',
            'Problems' => 'Alle Probleme',
            'Invoice' => 'Rechnung',
            'Programmer' => 'Programmierer',
            'Partner' => 'Kontakte',
            'Admin' => 'Admin',
            'Email' => 'Kommunikation',
            'Inquiry' => 'Anfragen',
            'Service' => 'Service',
            'Maintenance' => 'Wartung',
            'Organization' => 'Organisation',
            'Finance' => 'Finanzen',
            'Super' => 'Super',
            'Projects' => 'Projekte',
        ]);
    }

    private function analytics(): array
    {
        $base = UserRoll::query()
            ->leftJoin('users', 'users.id', '=', 'user_rolls.user_id')
            ->leftJoin('employees', 'employees.id', '=', 'users.name');

        return [
            'total' => (clone $base)->count(),
            'users_with_permissions' => (clone $base)->distinct('user_rolls.user_id')->count('user_rolls.user_id'),
            'modules_used' => (clone $base)->distinct('user_rolls.item_id')->count('user_rolls.item_id'),
            'admins' => (clone $base)->where('users.is_admin', true)->distinct('user_rolls.user_id')->count('user_rolls.user_id'),
            'read_count' => (clone $base)->where('user_rolls.is_read', true)->count(),
            'create_count' => (clone $base)->where('user_rolls.is_add', true)->count(),
            'update_count' => (clone $base)->where('user_rolls.is_update', true)->count(),
            'delete_count' => (clone $base)->where('user_rolls.is_delete', true)->count(),
        ];
    }
}
