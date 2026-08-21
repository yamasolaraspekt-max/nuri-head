<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class PlannerApiAuthController extends Controller
{
    /**
     * POST /api/planner/auth/token
     *
     * Body:
     * - login: email OR users.name employee id
     * - password: user password
     * - device_name: optional name for the external app/token
     * - include_kanban_tasks: optional boolean, default true
     * - date: optional date for kanban tasks, default today
     * - mode: day|week|month|all, default day
     * - include_done: optional boolean, default true
     * - include_unscheduled: optional boolean, default true
     * - only_montage: optional boolean, default true
     */
    public function token(Request $request): JsonResponse
    {
        $data = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:120'],
            'include_kanban_tasks' => ['nullable', 'boolean'],
            'date' => ['nullable', 'date'],
            'mode' => ['nullable', 'string', 'max:20'],
            'include_done' => ['nullable', 'boolean'],
            'include_unscheduled' => ['nullable', 'boolean'],
            'only_montage' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $login = trim((string) $data['login']);

        $user = User::query()
            ->where('email', $login)
            ->orWhere('name', $login)
            ->first();

        if (!$user || !Hash::check((string) $data['password'], (string) $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Die Zugangsdaten sind ungültig.'],
            ]);
        }

        // Z2-W0-9: ein deaktiviertes Konto bekommt keinen Token — und verliert die vorhandenen.
        // Ohne diese Zeile haette ein gesperrter Nutzer sich per Mobile-Token weiter angemeldet,
        // waehrend ihn die Weboberflaeche bereits als „Deactivated" fuehrt.
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'disabled_at') && $user->disabled_at !== null) {
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            throw ValidationException::withMessages([
                'login' => ['Dieses Konto ist deaktiviert.'],
            ]);
        }

        if (!method_exists($user, 'createToken')) {
            return response()->json([
                'ok' => false,
                'message' => 'Laravel Sanctum ist nicht im User Model aktiviert. Bitte HasApiTokens in App\\Models\\User hinzufügen.',
            ], 500);
        }

        $deviceName = trim((string) ($data['device_name'] ?? 'planner-external-app'));
        $deviceName = $deviceName !== '' ? $deviceName : 'planner-external-app';

        if (method_exists($user, 'tokens')) {
            $user->tokens()->where('name', $deviceName)->delete();
        }

        $token = $user->createToken($deviceName, [
            'planner:read',
            'planner:write',
            'planner:attendance',
            'planner:kanban',
        ])->plainTextToken;

        return response()->json([
            'ok' => true,
            'token_type' => 'Bearer',
            'token' => $token,
            'user' => $this->userPayload($user),
            'employee' => $this->employeePayload($user),
            'kanban_tasks' => $this->kanbanTasksPayload($request, $user),
        ]);
    }

    /**
     * GET /api/planner/auth/me
     *
     * Query:
     * - include_kanban_tasks: optional boolean, default true
     * - date: optional date for kanban tasks, default today
     * - mode: day|week|month|all, default day
     * - include_done: optional boolean, default true
     * - include_unscheduled: optional boolean, default true
     * - only_montage: optional boolean, default true
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'ok' => true,
            'user' => $this->userPayload($user),
            'employee' => $this->employeePayload($user),
            'kanban_tasks' => $this->kanbanTasksPayload($request, $user),
        ]);
    }

    /**
     * POST /api/planner/auth/logout
     * Deletes only the current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'ok' => true,
            'message' => 'Token wurde gelöscht.',
        ]);
    }

    /**
     * POST /api/planner/auth/logout-all
     * Deletes all API tokens of this user.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        return response()->json([
            'ok' => true,
            'message' => 'Alle Tokens wurden gelöscht.',
        ]);
    }

    private function userPayload($user): ?array
    {
        if (!$user) {
            return null;
        }

        return [
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    private function employeePayload($user): ?array
    {
        if (!$user) {
            return null;
        }

        $employeeId = $this->employeeIdFromUser($user);

        if (!$employeeId || !Schema::hasTable('employees')) {
            return $employeeId ? ['id' => $employeeId] : null;
        }

        $columns = array_values(array_filter([
            'id',
            Schema::hasColumn('employees', 'name') ? 'name' : null,
            Schema::hasColumn('employees', 'lastname') ? 'lastname' : null,
            Schema::hasColumn('employees', 'title') ? 'title' : null,
            Schema::hasColumn('employees', 'email') ? 'email' : null,
            Schema::hasColumn('employees', 'phone') ? 'phone' : null,
            Schema::hasColumn('employees', 'image') ? 'image' : null,
            Schema::hasColumn('employees', 'photo') ? 'photo' : null,
            Schema::hasColumn('employees', 'status') ? 'status' : null,
        ]));

        $employee = Employee::query()
            ->select($columns)
            ->find($employeeId);

        if (!$employee) {
            return ['id' => $employeeId];
        }

        return [
            'id' => (int) $employee->id,
            'name' => $employee->name ?? null,
            'lastname' => $employee->lastname ?? null,
            'full_name' => $this->employeeFullName($employee),
            'email' => $employee->email ?? null,
            'phone' => $employee->phone ?? null,
            'image' => $employee->image ?? $employee->photo ?? null,
            'photo_url' => $this->assetUrl($employee->image ?? $employee->photo ?? null, 'images/employee'),
            'status' => $employee->status ?? null,
        ];
    }

    private function kanbanTasksPayload(Request $request, $user): array
    {
        $include = $request->boolean('include_kanban_tasks', true);

        if (!$include) {
            return [
                'included' => false,
                'items' => [],
                'summary' => [
                    'total' => 0,
                    'done' => 0,
                    'open' => 0,
                    'in_progress' => 0,
                    'planned' => 0,
                ],
            ];
        }

        $employeeId = $this->employeeIdFromUser($user);

        if (!$employeeId) {
            return [
                'included' => true,
                'ok' => false,
                'message' => 'Kein Mitarbeiter wurde für den Benutzer gefunden.',
                'items' => [],
                'summary' => [
                    'total' => 0,
                    'done' => 0,
                    'open' => 0,
                    'in_progress' => 0,
                    'planned' => 0,
                ],
            ];
        }

        if (!Schema::hasTable('kanban_lead_tasks')) {
            return [
                'included' => true,
                'ok' => false,
                'message' => 'Tabelle kanban_lead_tasks wurde nicht gefunden.',
                'items' => [],
                'summary' => [
                    'total' => 0,
                    'done' => 0,
                    'open' => 0,
                    'in_progress' => 0,
                    'planned' => 0,
                ],
            ];
        }

        $mode = $this->normalizeMode((string) $request->query('mode', $request->input('mode', 'day')));
        [$from, $to, $periodLabel] = $this->periodRange(
            (string) $request->query('date', $request->input('date', now()->toDateString())),
            $mode
        );

        $includeDone = $request->boolean('include_done', true);
        $includeUnscheduled = $request->boolean('include_unscheduled', true);
        $onlyMontage = $request->boolean('only_montage', true);
        $limit = min(max((int) $request->query('limit', $request->input('limit', 250)), 1), 1000);

        $q = DB::table('kanban_lead_tasks as klt');

        if ($this->safeColumn('kanban_lead_tasks', 'deleted_at')) {
            $q->whereNull('klt.deleted_at');
        }

        $q->where(function ($assigned) use ($employeeId) {
            $hasAnyCondition = false;

            if ($this->safeColumn('kanban_lead_tasks', 'performer_employee_id')) {
                $assigned->where('klt.performer_employee_id', $employeeId);
                $hasAnyCondition = true;
            }

            if (Schema::hasTable('kanban_lead_task_employees')) {
                $method = $hasAnyCondition ? 'orWhereExists' : 'whereExists';
                $assigned->{$method}(function ($exists) use ($employeeId) {
                    $exists->select(DB::raw(1))
                        ->from('kanban_lead_task_employees as klte')
                        ->whereColumn('klte.kanban_lead_task_id', 'klt.id')
                        ->where('klte.employee_id', $employeeId);
                });
                $hasAnyCondition = true;
            }

            if (!$hasAnyCondition) {
                $assigned->whereRaw('1 = 0');
            }
        });

        if (!$includeDone && $this->safeColumn('kanban_lead_tasks', 'status')) {
            $q->whereRaw('LOWER(COALESCE(klt.status, "")) NOT IN (?, ?, ?, ?, ?, ?)', [
                'done',
                'completed',
                'closed',
                'finished',
                'ended',
                'cancelled',
            ]);
        }

        if ($mode !== 'all') {
            $q->where(function ($date) use ($from, $to, $includeUnscheduled) {
                $date->where(function ($overlap) use ($from, $to) {
                    $overlap->whereNotNull('klt.planned_start_at')
                        ->where('klt.planned_start_at', '<=', $to->toDateTimeString())
                        ->where(function ($end) use ($from) {
                            $end->whereNull('klt.planned_end_at')
                                ->orWhere('klt.planned_end_at', '>=', $from->toDateTimeString());
                        });
                });

                if ($includeUnscheduled) {
                    $date->orWhereNull('klt.planned_start_at');
                }
            });
        }

        $select = [
            'klt.id',
            'klt.lead_product_list_id',
            'klt.customer_id',
            'klt.alternative_id',
            'klt.product_id',
            'klt.lead_stage_id',
            'klt.lead_sub_stage_id',
            'klt.task_phase_id',
            'klt.phase_activity_id',
            'klt.title',
            'klt.description',
            'klt.internal_note',
            'klt.is_manual',
            'klt.is_scheduled',
            'klt.photo_required',
            'klt.status',
            'klt.estimated_minutes',
            'klt.planned_start_at',
            'klt.planned_end_at',
            'klt.done_at',
            'klt.created_by_employee_id',
            'klt.performer_employee_id',
            'klt.scheduled_by_employee_id',
            'klt.done_by_employee_id',
            'klt.meta',
            'klt.created_at',
            'klt.updated_at',
        ];

        if (Schema::hasTable('lead_product_lists')) {
            $q->leftJoin('lead_product_lists as lpl', 'lpl.id', '=', 'klt.lead_product_list_id');

            $select = array_merge($select, [
                'lpl.status as project_status',
                'lpl.stage as project_stage',
                'lpl.lead_stage_sub_stage_id as project_sub_stage_id',
                'lpl.employee_id as project_manager_employee_id',
                'lpl.field_employee as project_field_employee_id',
            ]);

            if ($onlyMontage) {
                $q->where(function ($montage) {
                    $montage->whereIn(DB::raw('LOWER(COALESCE(lpl.status, ""))'), [
                        'project',
                        'projekt',
                        'montage',
                    ]);

                    if (Schema::hasTable('lead_stages')) {
                        $montage->orWhereExists(function ($stage) {
                            $stage->select(DB::raw(1))
                                ->from('lead_stages as ls_project')
                                ->whereColumn('ls_project.key', 'lpl.status')
                                ->where(function ($stageName) {
                                    $stageName->whereIn(DB::raw('LOWER(COALESCE(ls_project.key, ""))'), [
                                        'project',
                                        'projekt',
                                        'montage',
                                    ])->orWhereIn(DB::raw('LOWER(COALESCE(ls_project.name, ""))'), [
                                                'project',
                                                'projekt',
                                                'montage',
                                            ]);
                                });
                        });
                    }
                });
            }
        }

        if (Schema::hasTable('new_leads')) {
            $q->leftJoin('new_leads as nl', 'nl.id', '=', DB::raw('COALESCE(klt.customer_id, lpl.customer_id)'));
            $select = array_merge($select, [
                'nl.customer_no',
                'nl.name as customer_name',
                'nl.lastname as customer_lastname',
                'nl.firma as customer_company',
            ]);
        }

        if (Schema::hasTable('lead_alternative_adds')) {
            $q->leftJoin('lead_alternative_adds as laa', 'laa.id', '=', DB::raw('COALESCE(klt.alternative_id, lpl.alternative_id)'));
            $select = array_merge($select, [
                'laa.object_name as object_name',
                'laa.street as object_street',
                'laa.address_no as object_street_number',
                'laa.postcode as object_postcode',
                'laa.city as object_city',
            ]);
        }

        if (Schema::hasTable('article_groups')) {
            $q->leftJoin('article_groups as ag', 'ag.id', '=', DB::raw('COALESCE(klt.product_id, lpl.product_id)'));
            $select = array_merge($select, [
                'ag.article_group as product_name',
                'ag.initial as product_initial',
                'ag.image as product_image',
            ]);
        }

        if (Schema::hasTable('task_phases')) {
            $q->leftJoin('task_phases as tp', 'tp.id', '=', 'klt.task_phase_id');
            $select = array_merge($select, [
                'tp.phase_name',
                'tp.section_name as phase_section_name',
            ]);
        }

        if (Schema::hasTable('phase_activities')) {
            $q->leftJoin('phase_activities as pa', 'pa.id', '=', 'klt.phase_activity_id');
            $select = array_merge($select, [
                'pa.title as activity_title',
                'pa.initial as activity_initial',
                'pa.duration as activity_duration',
                'pa.duration_type as activity_duration_type',
                'pa.priority as activity_priority',
                'pa.photo as activity_photo',
                'pa.link as activity_link',
            ]);
        }

        if (Schema::hasTable('lead_stages')) {
            $q->leftJoin('lead_stages as ls', 'ls.id', '=', 'klt.lead_stage_id');
            $select = array_merge($select, [
                'ls.key as lead_stage_key',
                'ls.name as lead_stage_name',
                'ls.color as lead_stage_color',
            ]);
        }

        if (Schema::hasTable('lead_stage_sub_stages')) {
            $q->leftJoin('lead_stage_sub_stages as lsss', 'lsss.id', '=', 'klt.lead_sub_stage_id');
            $select = array_merge($select, [
                'lsss.name as lead_sub_stage_name',
                'lsss.key as lead_sub_stage_key',
                'lsss.color as lead_sub_stage_color',
            ]);
        }

        $rows = $q->select($select)
            ->orderByRaw('COALESCE(klt.planned_start_at, klt.created_at) ASC')
            ->orderBy('klt.id')
            ->limit($limit)
            ->get();

        $taskIds = $rows->pluck('id')->map(fn($id) => (int) $id)->filter()->values()->all();
        $taskEmployees = $this->kanbanTaskEmployees($taskIds);

        $items = $rows->map(function ($row) use ($taskEmployees) {
            $status = $this->normalizePlannerStatus((string) ($row->status ?? 'open'));

            return [
                'id' => (int) $row->id,
                'kanban_lead_task_id' => (int) $row->id,
                'source_type' => 'kanban_task',
                'source_id' => (int) $row->id,
                'lead_product_list_id' => $row->lead_product_list_id ? (int) $row->lead_product_list_id : null,
                'customer_id' => $row->customer_id ? (int) $row->customer_id : null,
                'alternative_id' => $row->alternative_id ? (int) $row->alternative_id : null,
                'product_id' => $row->product_id ? (int) $row->product_id : null,
                'lead_stage_id' => $row->lead_stage_id ? (int) $row->lead_stage_id : null,
                'lead_sub_stage_id' => $row->lead_sub_stage_id ? (int) $row->lead_sub_stage_id : null,
                'task_phase_id' => $row->task_phase_id ? (int) $row->task_phase_id : null,
                'phase_activity_id' => $row->phase_activity_id ? (int) $row->phase_activity_id : null,
                'title' => $row->title ?: ($row->activity_title ?: ($row->phase_name ?: ('Kanban Aufgabe #' . $row->id))),
                'description' => $row->description,
                'internal_note' => $row->internal_note ?? null,
                'status' => $status,
                'status_label' => $this->statusLabel($status),
                'raw_status' => $row->status,
                'is_done' => $status === 'done',
                'is_manual' => (bool) ($row->is_manual ?? false),
                'is_scheduled' => (bool) ($row->is_scheduled ?? false),
                'photo_required' => (bool) ($row->photo_required ?? false),
                'estimated_minutes' => $row->estimated_minutes ? (int) $row->estimated_minutes : null,
                'duration_minutes' => $row->estimated_minutes ? (int) $row->estimated_minutes : ((int) ($row->activity_duration ?? 60)),
                'planned_start_at' => $row->planned_start_at,
                'planned_end_at' => $row->planned_end_at,
                'schedule_date_key' => $this->dateKey($row->planned_start_at ?: $row->created_at),
                'schedule_date_label' => $this->dateLabel($row->planned_start_at ?: $row->created_at),
                'schedule_time_label' => $this->timeLabel($row->planned_start_at),
                'schedule_range_label' => $this->timeRangeLabel($row->planned_start_at, $row->planned_end_at),
                'done_at' => $row->done_at,
                'done_at_label' => $this->dateTimeLabel($row->done_at),
                'done_by_employee_id' => $row->done_by_employee_id ? (int) $row->done_by_employee_id : null,
                'done_by_employee' => $row->done_by_employee_id ? $this->employeeMini((int) $row->done_by_employee_id) : null,
                'created_by_employee_id' => $row->created_by_employee_id ? (int) $row->created_by_employee_id : null,
                'performer_employee_id' => $row->performer_employee_id ? (int) $row->performer_employee_id : null,
                'scheduled_by_employee_id' => $row->scheduled_by_employee_id ? (int) $row->scheduled_by_employee_id : null,
                'performer' => $row->performer_employee_id ? $this->employeeMini((int) $row->performer_employee_id) : null,
                'team' => $taskEmployees->get((int) $row->id, collect())->values()->all(),
                'customer' => [
                    'number' => $row->customer_no ?? null,
                    'name' => $this->customerName($row),
                ],
                'object' => [
                    'name' => $row->object_name ?? null,
                    'address' => $this->objectAddress($row),
                    'street' => $row->object_street ?? null,
                    'street_number' => $row->object_street_number ?? null,
                    'postcode' => $row->object_postcode ?? null,
                    'city' => $row->object_city ?? null,
                ],
                'product' => [
                    'name' => $row->product_name ?? null,
                    'initial' => $row->product_initial ?? null,
                    'image' => $row->product_image ?? null,
                    'image_url' => $this->assetUrl($row->product_image ?? null, 'images/article_groups'),
                ],
                'phase' => [
                    'name' => $row->phase_name ?? null,
                    'section_name' => $row->phase_section_name ?? null,
                ],
                'activity' => [
                    'title' => $row->activity_title ?? null,
                    'initial' => $row->activity_initial ?? null,
                    'duration' => $row->activity_duration ?? null,
                    'duration_type' => $row->activity_duration_type ?? null,
                    'priority' => $row->activity_priority ?? null,
                    'photo' => $row->activity_photo ?? null,
                    'photo_url' => $this->assetUrl($row->activity_photo ?? null, 'images/activities'),
                    'link' => $row->activity_link ?? null,
                ],
                'stage' => [
                    'key' => $row->lead_stage_key ?? null,
                    'name' => $row->lead_stage_name ?? null,
                    'color' => $row->lead_stage_color ?? null,
                ],
                'sub_stage' => [
                    'key' => $row->lead_sub_stage_key ?? null,
                    'name' => $row->lead_sub_stage_name ?? null,
                    'color' => $row->lead_sub_stage_color ?? null,
                ],
                'project' => [
                    'status' => $row->project_status ?? null,
                    'stage' => $row->project_stage ?? null,
                    'sub_stage_id' => $row->project_sub_stage_id ? (int) $row->project_sub_stage_id : null,
                    'manager_employee_id' => $row->project_manager_employee_id ? (int) $row->project_manager_employee_id : null,
                    'field_employee_id' => $row->project_field_employee_id ? (int) $row->project_field_employee_id : null,
                ],
                'meta' => $this->decodeJson($row->meta ?? null),
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ];
        })->values();

        return [
            'included' => true,
            'ok' => true,
            'meta' => [
                'employee_id' => $employeeId,
                'date' => $from->toDateString(),
                'mode' => $mode,
                'from' => $from->toDateTimeString(),
                'to' => $to->toDateTimeString(),
                'period_label' => $periodLabel,
                'include_done' => $includeDone,
                'include_unscheduled' => $includeUnscheduled,
                'only_montage' => $onlyMontage,
                'limit' => $limit,
            ],
            'summary' => [
                'total' => $items->count(),
                'done' => $items->where('is_done', true)->count(),
                'open' => $items->where('status', 'open')->count(),
                'planned' => $items->where('status', 'planned')->count(),
                'in_progress' => $items->where('status', 'in_progress')->count(),
                'cancelled' => $items->where('status', 'cancelled')->count(),
            ],
            'items' => $items->all(),
            'grouped' => [
                'by_date' => $this->groupKanbanTasksByDate($items),
                'by_project' => $this->groupKanbanTasksByProject($items),
            ],
        ];
    }

    private function kanbanTaskEmployees(array $taskIds): Collection
    {
        if (empty($taskIds) || !Schema::hasTable('kanban_lead_task_employees') || !Schema::hasTable('employees')) {
            return collect();
        }

        $rows = DB::table('kanban_lead_task_employees as piv')
            ->join('employees as e', 'e.id', '=', 'piv.employee_id')
            ->whereIn('piv.kanban_lead_task_id', $taskIds)
            ->select([
                'piv.kanban_lead_task_id',
                'piv.employee_id',
                'piv.role',
                'piv.status',
                'piv.assigned_at',
                'piv.done_at',
                'e.id',
                'e.name',
                'e.lastname',
                'e.title',
                'e.image',
                'e.email',
                'e.phone',
            ])
            ->get()
            ->map(function ($row) {
                return [
                    'kanban_lead_task_id' => (int) $row->kanban_lead_task_id,
                    'id' => (int) $row->employee_id,
                    'employee_id' => (int) $row->employee_id,
                    'role' => $row->role ?? 'member',
                    'status' => $row->status ?? null,
                    'assigned_at' => $row->assigned_at ?? null,
                    'done_at' => $row->done_at ?? null,
                    'name' => $row->name ?? null,
                    'lastname' => $row->lastname ?? null,
                    'full_name' => $this->employeeFullName($row),
                    'photo_url' => $this->assetUrl($row->image ?? null, 'images/employee'),
                    'email' => $row->email ?? null,
                    'phone' => $row->phone ?? null,
                ];
            });

        return $rows->groupBy('kanban_lead_task_id');
    }

    private function groupKanbanTasksByDate(Collection $items): array
    {
        return $items
            ->groupBy(fn($item) => $item['schedule_date_key'] ?: 'without_date')
            ->map(function ($rows, $date) {
                return [
                    'date' => $date,
                    'label' => $date === 'without_date' ? 'Ohne Datum' : Carbon::parse($date)->format('d.m.Y'),
                    'count' => $rows->count(),
                    'done_count' => $rows->where('is_done', true)->count(),
                    'open_count' => $rows->where('is_done', false)->count(),
                    'items' => $rows->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function groupKanbanTasksByProject(Collection $items): array
    {
        return $items
            ->groupBy(fn($item) => $item['lead_product_list_id'] ?: 'without_project')
            ->map(function ($rows, $projectId) {
                $first = $rows->first();

                return [
                    'lead_product_list_id' => $projectId === 'without_project' ? null : (int) $projectId,
                    'customer' => $first['customer'] ?? null,
                    'object' => $first['object'] ?? null,
                    'product' => $first['product'] ?? null,
                    'count' => $rows->count(),
                    'done_count' => $rows->where('is_done', true)->count(),
                    'open_count' => $rows->where('is_done', false)->count(),
                    'items' => $rows->values()->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function employeeIdFromUser($user): ?int
    {
        if (!$user) {
            return null;
        }

        $id = $user->employee_id ?? $user->employee?->id ?? $user->name ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    private function periodRange(string $date, string $mode): array
    {
        try {
            $base = Carbon::parse($date);
        } catch (\Throwable $e) {
            $base = now();
        }

        return match ($this->normalizeMode($mode)) {
            'week' => [
                $base->copy()->startOfWeek()->startOfDay(),
                $base->copy()->endOfWeek()->endOfDay(),
                $base->copy()->startOfWeek()->format('d.m.Y') . ' - ' . $base->copy()->endOfWeek()->format('d.m.Y'),
            ],
            'month' => [
                $base->copy()->startOfMonth()->startOfDay(),
                $base->copy()->endOfMonth()->endOfDay(),
                $base->copy()->translatedFormat('F Y'),
            ],
            'all' => [
                Carbon::create(1970, 1, 1)->startOfDay(),
                now()->copy()->addYears(10)->endOfDay(),
                'Alle Zeiten',
            ],
            default => [
                $base->copy()->startOfDay(),
                $base->copy()->endOfDay(),
                $base->copy()->format('d.m.Y'),
            ],
        };
    }

    private function normalizeMode(string $mode): string
    {
        $mode = strtolower(trim($mode));

        return in_array($mode, ['day', 'week', 'month', 'all'], true) ? $mode : 'day';
    }

    private function normalizePlannerStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));
        $status = str_replace(['-', ' '], '_', $status);

        return match ($status) {
            'completed', 'complete', 'finished', 'finish', 'closed', 'close', 'ended', 'end', 'done', 'erledigt', 'beendet', 'geschlossen' => 'done',
            'in_progress', 'progress', 'process', 'in_bearbeitung', 'bearbeitung', 'on_progress', 'on_going', 'working', 'arbeit', 'in_arbeit', 'inarbeit' => 'in_progress',
            'scheduled', 'schedule', 'planned', 'confirmed', 'geplant', 'terminiert' => 'planned',
            'pause', 'paused', 'pausiert', 'mittag', 'mittagessen' => 'paused',
            'blocked', 'blockiert', 'reject', 'rejected' => 'blocked',
            'cancel', 'canceled', 'cancelled', 'storniert', 'junk' => 'cancelled',
            default => in_array($status, ['open', 'planned', 'in_progress', 'done', 'blocked', 'paused', 'cancelled'], true) ? $status : 'open',
        };
    }

    private function statusLabel(?string $status): string
    {
        return match ($this->normalizePlannerStatus($status)) {
            'done' => 'Erledigt',
            'in_progress' => 'In Arbeit',
            'planned' => 'Geplant',
            'blocked' => 'Blockiert',
            'paused' => 'Pausiert',
            'cancelled' => 'Storniert',
            default => 'Offen',
        };
    }

    private function safeColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    private function decodeJson($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null || trim((string) $value) === '') {
            return [];
        }

        $decoded = json_decode((string) $value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function customerName(object $row): string
    {
        $company = trim((string) ($row->customer_company ?? ''));
        $name = trim((string) (($row->customer_name ?? '') . ' ' . ($row->customer_lastname ?? '')));

        return $company !== '' ? $company : ($name !== '' ? $name : 'Kunde');
    }

    private function objectAddress(object $row): string
    {
        return trim(implode(' ', array_filter([
            $row->object_street ?? null,
            $row->object_street_number ?? null,
            $row->object_postcode ?? null,
            $row->object_city ?? null,
        ])));
    }

    private function employeeFullName(object $employee): string
    {
        $fullName = trim((($employee->title ?? '') ? ($employee->title . ' ') : '') . (($employee->name ?? '') . ' ' . ($employee->lastname ?? '')));

        return $fullName !== '' ? $fullName : ('Mitarbeiter #' . ($employee->id ?? $employee->employee_id ?? ''));
    }

    private function employeeMini(?int $employeeId): ?array
    {
        $employeeId = $employeeId ? (int) $employeeId : 0;

        if ($employeeId <= 0 || !Schema::hasTable('employees')) {
            return null;
        }

        $employee = DB::table('employees')->where('id', $employeeId)->first();

        if (!$employee) {
            return null;
        }

        return [
            'id' => (int) $employee->id,
            'employee_id' => (int) $employee->id,
            'name' => $employee->name ?? null,
            'lastname' => $employee->lastname ?? null,
            'full_name' => $this->employeeFullName($employee),
            'photo_url' => $this->assetUrl($employee->image ?? $employee->photo ?? null, 'images/employee'),
            'email' => $employee->email ?? null,
            'phone' => $employee->phone ?? null,
        ];
    }

    private function dateKey($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function dateLabel($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('d.m.Y');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function timeLabel($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function timeRangeLabel($start, $end): ?string
    {
        $startLabel = $this->timeLabel($start);
        $endLabel = $this->timeLabel($end);

        if (!$startLabel) {
            return null;
        }

        return $endLabel ? ($startLabel . ' - ' . $endLabel) : $startLabel;
    }

    private function dateTimeLabel($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('d.m.Y H:i');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function assetUrl(?string $path, string $fallbackFolder = ''): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        if (str_starts_with($path, 'storage/') || str_starts_with($path, 'uploads/') || str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return asset(trim($fallbackFolder, '/') . '/' . ltrim($path, '/'));
    }
}
