<?php


namespace App\Http\Controllers\Task;
use App\Http\Controllers\Controller;

use App\Models\PersonalTask;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Jobs\ScheduleTaskReminder;
use App\Models\PersonalTaskKey;
use App\Models\TaskActivity;
use App\Models\TaskReport;
use App\Models\TaskReportComment;
use App\Models\PersonalSubTask;
use App\Models\NewLeads;
use App\Models\Branch;
use App\Models\MainAppointment;
use App\Models\MainAppointmentEmployee;
use App\Models\EmployeesPersonalTask;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Notifications\PersonalTaskNotification;
use App\Notifications\AppointmentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
use App\Models\ArticleGroup;
use App\Models\EmployeeRecurringLeave;
use App\Models\EmployeeRecurringLeaveOverride;
use App\Models\Team;
use Illuminate\Support\Collection;
use App\Models\Department;

use App\Models\PersonalTaskComment;
use App\Models\PersonalTaskAttachment;


use Illuminate\Support\Str;
class PersonalTaskBoardController extends Controller
{
    protected function currentEmployeeId(): ?int
    {
        $user = Auth::user();

        if (!$user || empty($user->name)) {
            return null;
        }

        return Employee::query()
            ->where('id', $user->name)
            ->value('id');
    }

    /**
     * These are FILTERS, not board columns.
     * The board columns must stay: Offen / In Bearbeitung / Erledigt.
     */
    protected function taskDueFilterOptions(): array
    {
        return [
            'all' => 'Alle Fälligkeiten',
            'overdue' => 'Überfällig',
            'overdue_week' => 'Überfällig diese Woche',
            'overdue_month' => 'Überfällig diesen Monat',
            'today' => 'Heute fällig',
            'this_week' => 'Diese Woche fällig',
            'next_14_days' => 'Nächste 14 Tage',
            'this_month' => 'Diesen Monat fällig',
            'older_month' => 'Länger als 1 Monat überfällig',
            'no_due' => 'Ohne Fälligkeit',
        ];
    }

    protected function boardColumnMeta(): array
    {
        return [
            'open' => [
                'label' => 'Offen',
                'color' => '#93c21c',
            ],
            'in_progress' => [
                'label' => 'In Bearbeitung',
                'color' => '#f97316',
            ],
            'completed' => [
                'label' => 'Erledigt',
                'color' => '#74b2d4',
            ],
        ];
    }

    protected function applyDueDateFilter($query, ?string $dueFilter): void
    {
        $dueFilter = $dueFilter ?: 'all';

        if (!array_key_exists($dueFilter, $this->taskDueFilterOptions())) {
            $dueFilter = 'all';
        }

        $today = now()->startOfDay();
        $yesterday = now()->subDay()->endOfDay();
        $startOfWeek = now()->startOfWeek()->startOfDay();
        $endOfWeek = now()->endOfWeek()->endOfDay();
        $startOfMonth = now()->startOfMonth()->startOfDay();
        $endOfMonth = now()->endOfMonth()->endOfDay();
        $next14Days = now()->addDays(14)->endOfDay();
        $olderThanOneMonth = now()->subMonth()->startOfDay();

        switch ($dueFilter) {
            case 'overdue':
                $query->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $today->toDateString());
                break;

            case 'overdue_week':
                if ($yesterday->lt($startOfWeek)) {
                    $query->whereRaw('1 = 0');
                    break;
                }

                $query->whereNotNull('due_date')
                    ->whereBetween('due_date', [
                        $startOfWeek->toDateString(),
                        $yesterday->toDateString(),
                    ]);
                break;

            case 'overdue_month':
                if ($yesterday->lt($startOfMonth)) {
                    $query->whereRaw('1 = 0');
                    break;
                }

                $query->whereNotNull('due_date')
                    ->whereBetween('due_date', [
                        $startOfMonth->toDateString(),
                        $yesterday->toDateString(),
                    ]);
                break;

            case 'today':
                $query->whereDate('due_date', $today->toDateString());
                break;

            case 'this_week':
                $query->whereNotNull('due_date')
                    ->whereBetween('due_date', [
                        $today->toDateString(),
                        $endOfWeek->toDateString(),
                    ]);
                break;

            case 'next_14_days':
                $query->whereNotNull('due_date')
                    ->whereBetween('due_date', [
                        $today->toDateString(),
                        $next14Days->toDateString(),
                    ]);
                break;

            case 'this_month':
                $query->whereNotNull('due_date')
                    ->whereBetween('due_date', [
                        $today->toDateString(),
                        $endOfMonth->toDateString(),
                    ]);
                break;

            case 'older_month':
                $query->whereNotNull('due_date')
                    ->whereDate('due_date', '<', $olderThanOneMonth->toDateString());
                break;

            case 'no_due':
                $query->whereNull('due_date');
                break;

            case 'all':
            default:
                break;
        }
    }

    protected function applyLeadStageFilters($query, $leadStageId = null, $leadSubStageId = null): void
    {
        $leadStageId = filled($leadStageId) ? (int) $leadStageId : null;
        $leadSubStageId = filled($leadSubStageId) ? (int) $leadSubStageId : null;

        if ($leadStageId) {
            $stageKey = LeadStage::query()
                ->whereKey($leadStageId)
                ->value('key');

            $query->where(function ($q) use ($leadStageId, $stageKey) {
                $q->where('personal_tasks.lead_stage_id', $leadStageId)
                    ->orWhereHas('leadProductList.companyStage', function ($stageQuery) use ($leadStageId) {
                        $stageQuery->where('lead_stages.id', $leadStageId);
                    });

                if (filled($stageKey)) {
                    $q->orWhereHas('leadProductList', function ($leadProductQuery) use ($stageKey) {
                        $leadProductQuery->where('status', $stageKey);
                    });
                }
            });
        }

        if ($leadSubStageId) {
            $query->where(function ($q) use ($leadSubStageId) {
                $q->where('personal_tasks.lead_stage_sub_stage_id', $leadSubStageId)
                    ->orWhereHas('leadProductList', function ($leadProductQuery) use ($leadSubStageId) {
                        $leadProductQuery->where('lead_stage_sub_stage_id', $leadSubStageId);
                    });
            });
        }
    }

    protected function normalizeBoardColumnFromStatus(?string $status): string
    {
        if (in_array($status, ['completed'], true)) {
            return 'completed';
        }

        if (in_array($status, ['on_progress', 'on_going', 'working'], true)) {
            return 'in_progress';
        }

        return 'open';
    }

    protected function taskIdsForCurrentUser(?int $employeeId, ?string $userName): array
    {
        $assignedIds = collect();
        $createdIds = collect();

        if ($employeeId) {
            $assignedIds = DB::table('employees_personal_tasks')
                ->where('employee_id', $employeeId)
                ->pluck('task_id');
        }

        if ($userName !== null && $userName !== '') {
            $createdIds = DB::table('personal_tasks')
                ->where('assigned_by', $userName)
                ->pluck('id');
        }

        return $assignedIds
            ->merge($createdIds)
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }


    /**
     * Duplicate key for visual task cards.
     * If the same task was stored twice as two different database rows, id-based
     * unique checks will not help. In that case task_id is the business key.
     */
    protected function taskDuplicateKey($task): string
    {
        $taskNo = trim((string) ($task->task_id ?? ''));

        if ($taskNo !== '') {
            return 'task_id:' . mb_strtolower($taskNo);
        }

        $title = mb_strtolower(trim((string) ($task->task_title ?? '')));
        $due = (string) ($task->due_date ?? '');
        $assignedBy = (string) ($task->assigned_by ?? '');

        if ($title !== '') {
            return 'signature:' . md5($title . '|' . $due . '|' . $assignedBy);
        }

        return 'id:' . (int) ($task->id ?? 0);
    }

    /**
     * Keep only one visual card per task. Prefer the newest updated row.
     */
    protected function dedupeTaskCollection($tasks)
    {
        return collect($tasks)
            ->groupBy(fn($task) => $this->taskDuplicateKey($task))
            ->map(function ($group) {
                return $group
                    ->sortByDesc(function ($task) {
                        if (!empty($task->updated_at) && method_exists($task->updated_at, 'timestamp')) {
                            return $task->updated_at->timestamp;
                        }

                        return !empty($task->updated_at) ? strtotime((string) $task->updated_at) : 0;
                    })
                    ->first();
            })
            ->values();
    }

    /**
     * Extract one database ID per real task from an already filtered query.
     * This removes duplicates caused by duplicate pivot rows AND duplicate
     * personal_tasks rows with the same task_id.
     */
    protected function duplicateSafeTaskIds($query): array
    {
        $rows = (clone $query)
            ->toBase()
            ->select([
                'personal_tasks.id',
                'personal_tasks.task_id',
                'personal_tasks.task_title',
                'personal_tasks.due_date',
                'personal_tasks.assigned_by',
                'personal_tasks.updated_at',
            ])
            ->get();

        return $rows
            ->groupBy(function ($row) {
                $taskNo = trim((string) ($row->task_id ?? ''));

                if ($taskNo !== '') {
                    return 'task_id:' . mb_strtolower($taskNo);
                }

                $title = mb_strtolower(trim((string) ($row->task_title ?? '')));
                $due = (string) ($row->due_date ?? '');
                $assignedBy = (string) ($row->assigned_by ?? '');

                if ($title !== '') {
                    return 'signature:' . md5($title . '|' . $due . '|' . $assignedBy);
                }

                return 'id:' . (int) $row->id;
            })
            ->map(function ($group) {
                return $group
                    ->sortByDesc(fn($row) => !empty($row->updated_at) ? strtotime((string) $row->updated_at) : 0)
                    ->first();
            })
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter()
            ->values()
            ->all();
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();

        if (!$authUser) {
            abort(403);
        }

        $employeeId = $this->currentEmployeeId();
        $userName = (string) ($authUser->name ?? '');
        $byYouTaskIds = $this->taskIdsForCurrentUser($employeeId, $userName);

        /*
        |--------------------------------------------------------------------------
        | Stats
        |--------------------------------------------------------------------------
        | All counts use distinct task IDs, so duplicate pivot rows cannot double
        | the numbers.
        */
        $myAssignedTaskIds = $employeeId
            ? DB::table('employees_personal_tasks')
                ->where('employee_id', $employeeId)
                ->pluck('task_id')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->all()
            : [];

        $createdTaskIds = $userName !== ''
            ? DB::table('personal_tasks')
                ->where('assigned_by', $userName)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->all()
            : [];

        $open = DB::table('personal_tasks as pt')
            ->whereIn('pt.id', $myAssignedTaskIds ?: [0])
            ->whereExists(function ($q) use ($employeeId) {
                $q->select(DB::raw(1))
                    ->from('employees_personal_tasks as ept')
                    ->whereColumn('ept.task_id', 'pt.id')
                    ->where('ept.employee_id', $employeeId)
                    ->whereNotIn('ept.status', ['reject', 'rejected']);
            })
            ->whereNotIn('pt.task_status', ['pause', 'cancel', 'junk', 'rejected'])
            ->whereNull('pt.archived_at')
            ->whereNull('pt.deleted_at')
            ->distinct('pt.id')
            ->count('pt.id');

        $newCount = DB::table('personal_tasks as pt')
            ->where('pt.assigned_by', $userName)
            ->whereNotIn('pt.task_status', ['pause', 'cancel', 'junk', 'rejected'])
            ->whereNull('pt.archived_at')
            ->whereNull('pt.deleted_at')
            ->distinct('pt.id')
            ->count('pt.id');

        $complete = DB::table('personal_tasks as pt')
            ->whereIn('pt.id', $byYouTaskIds ?: [0])
            ->where('pt.task_status', 'completed')
            ->whereNull('pt.deleted_at')
            ->distinct('pt.id')
            ->count('pt.id');

        $pause = DB::table('personal_tasks as pt')
            ->whereIn('pt.id', $byYouTaskIds ?: [0])
            ->where('pt.task_status', 'pause')
            ->whereNull('pt.deleted_at')
            ->distinct('pt.id')
            ->count('pt.id');

        $cancel = DB::table('personal_tasks as pt')
            ->whereIn('pt.id', $byYouTaskIds ?: [0])
            ->where('pt.task_status', 'cancel')
            ->whereNull('pt.deleted_at')
            ->distinct('pt.id')
            ->count('pt.id');

        $archived = DB::table('personal_tasks as pt')
            ->whereIn('pt.id', $byYouTaskIds ?: [0])
            ->whereNotNull('pt.archived_at')
            ->whereNull('pt.deleted_at')
            ->distinct('pt.id')
            ->count('pt.id');

        $rejected = DB::table('personal_tasks as pt')
            ->whereIn('pt.id', $byYouTaskIds ?: [0])
            ->whereExists(function ($q) use ($employeeId) {
                $q->select(DB::raw(1))
                    ->from('employees_personal_tasks as ept')
                    ->whereColumn('ept.task_id', 'pt.id')
                    ->where('ept.employee_id', $employeeId)
                    ->whereIn('ept.status', ['reject', 'rejected']);
            })
            ->whereNull('pt.deleted_at')
            ->distinct('pt.id')
            ->count('pt.id');

        $totalMyTasks = DB::table('personal_tasks as pt')
            ->whereIn('pt.id', $byYouTaskIds ?: [0])
            ->whereNull('pt.deleted_at')
            ->distinct('pt.id')
            ->count('pt.id');

        $doneShare = $totalMyTasks > 0 ? round(($complete / $totalMyTasks) * 100) : 0;
        $openShare = $totalMyTasks > 0 ? round(($open / $totalMyTasks) * 100) : 0;

        $stats = [
            'total' => $totalMyTasks,
            'assigned_by_me' => $newCount,
            'assigned_to_me' => count($myAssignedTaskIds),
            'completed' => $complete,
            'paused' => $pause,
            'cancel' => $cancel,
            'archived' => $archived,
            'rejected' => $rejected,
            'shares' => [
                'done' => $doneShare,
                'open' => $openShare,
            ],
        ];

        $employees = Employee::select('id', 'name', 'lastname', 'image')
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhere('status', '!=', 'Deactive');
            })
            ->orderBy('lastname')
            ->orderBy('name')
            ->get();

        $employeeOptions = $employees->map(function (Employee $e) {
            return [
                'id' => $e->id,
                'name' => $e->name,
                'lastname' => $e->lastname,
                'image' => $e->image,
            ];
        })->values()->all();

        $activityFeed = DB::table('personal_tasks as pt')
            ->whereIn('pt.id', $byYouTaskIds ?: [0])
            ->whereNull('pt.deleted_at')
            ->leftJoin('employees_personal_tasks as ept', function ($join) use ($employeeId) {
                $join->on('ept.task_id', '=', 'pt.id')
                    ->where('ept.employee_id', '=', $employeeId);
            })
            ->leftJoin('employees as e', 'e.id', '=', 'ept.employee_id')
            ->where(function ($q) {
                $q->whereIn('pt.task_status', ['completed', 'pause', 'cancel'])
                    ->orWhereIn('ept.status', ['reject', 'rejected']);
            })
            ->selectRaw('
                pt.id,
                pt.task_title,
                pt.task_status,
                COALESCE(ept.status, pt.task_status)    as activity_status,
                COALESCE(ept.updated_at, pt.updated_at) as activity_at,
                COALESCE(e.name, pt.assigned_by)        as employee_name,
                e.lastname                              as employee_lastname,
                e.image                                 as employee_image
            ')
            ->groupBy('pt.id', 'pt.task_title', 'pt.task_status', 'ept.status', 'ept.updated_at', 'pt.updated_at', 'e.name', 'pt.assigned_by', 'e.lastname', 'e.image')
            ->orderBy('activity_at', 'desc')
            ->limit(40)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
        $view = $request->input('view', 'board');
        $tab = $request->input('tab', 'my');

        $search = $request->input('search');
        $status = $request->input('status');
        $priority = $request->input('priority');
        $public = $request->input('public');
        $isReport = $request->input('is_report');
        $dueFilter = $request->input('due_filter', 'all');
        $sort = $request->input('sort', 'created_at');
        $dir = $request->input('dir', 'desc');
        $filterEmployeeId = $request->input('employee_id');
        $employeeFilterState = $request->input('employee_filter_state', 'assigned');
        $user = $userName;

        $base = PersonalTask::query()
            ->select('personal_tasks.*')
            ->with([
                'employees:id,name,lastname,image',
                'assignedBy:id,name,lastname,image',
                'customer:id,customer_no,lastname,name,city',
            ])
            ->withTrashed();

        if (empty($byYouTaskIds)) {
            $base->whereRaw('1 = 0');
        } else {
            $base->whereIn('personal_tasks.id', $byYouTaskIds);
        }

        switch ($tab) {
            case 'my':
                if ($employeeId) {
                    $base->whereHas('employees', function ($q) use ($employeeId) {
                        $q->where('employees.id', $employeeId)
                            ->whereNotIn('employees_personal_tasks.status', ['reject', 'rejected']);
                    });
                }

                $base->whereNull('archived_at')
                    ->whereNull('deleted_at')
                    ->whereNotIn('task_status', ['pause', 'cancel', 'junk', 'rejected']);
                break;

            case 'created':
                $base->where('assigned_by', $user)
                    ->whereNull('archived_at')
                    ->whereNull('deleted_at')
                    ->whereNotIn('task_status', ['pause', 'cancel', 'junk', 'rejected']);
                break;

            case 'completed':
                $base->where('task_status', 'completed')
                    ->whereNull('archived_at')
                    ->whereNull('deleted_at');
                break;

            case 'paused':
                $base->where('task_status', 'pause')
                    ->whereNull('archived_at')
                    ->whereNull('deleted_at');
                break;

            case 'rejected':
                $base->whereNull('deleted_at')
                    ->where(function ($q) use ($employeeId, $user) {
                        if ($employeeId) {
                            $q->whereHas('employees', function ($qq) use ($employeeId) {
                                $qq->where('employees.id', $employeeId)
                                    ->whereIn('employees_personal_tasks.status', ['reject', 'rejected']);
                            });
                        }

                        $q->orWhere(function ($qq) use ($user) {
                            $qq->where('assigned_by', $user)
                                ->whereHas('employees', function ($qqq) {
                                    $qqq->whereIn('employees_personal_tasks.status', ['reject', 'rejected']);
                                });
                        });
                    });
                break;

            case 'archived':
                $base->whereNotNull('archived_at')
                    ->whereNull('deleted_at');
                break;

            case 'deleted':
                $base->onlyTrashed();
                break;

            default:
                if ($employeeId) {
                    $base->whereHas('employees', function ($q) use ($employeeId) {
                        $q->where('employees.id', $employeeId)
                            ->whereNotIn('employees_personal_tasks.status', ['reject', 'rejected']);
                    });
                }

                $base->whereNull('archived_at')
                    ->whereNull('deleted_at')
                    ->whereNotIn('task_status', ['pause', 'cancel', 'junk', 'rejected']);
                break;
        }

        if ($search) {
            $base->where(function ($q) use ($search) {
                $q->where('task_title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('task_id', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $base->where('task_status', $status);
        }

        if ($priority) {
            $base->where('priority', $priority);
        }

        if ($public !== null && $public !== '') {
            $base->where('public', (bool) $public);
        }

        if ($isReport !== null && $isReport !== '') {
            $base->where('is_report', (bool) $isReport);
        }

        $this->applyDueDateFilter($base, $dueFilter);

        if ($filterEmployeeId !== null && $filterEmployeeId !== '') {
            $filterEmployeeId = (int) $filterEmployeeId;

            if (!in_array($employeeFilterState, ['assigned', 'accepted', 'created', 'controller', 'rejected'], true)) {
                $employeeFilterState = 'assigned';
            }

            switch ($employeeFilterState) {
                case 'created':
                    $base->where('assigned_by', (string) $filterEmployeeId);
                    break;

                case 'controller':
                    $base->where(function ($q) use ($filterEmployeeId) {
                        $q->whereJsonContains('controller_id', $filterEmployeeId)
                            ->orWhereJsonContains('controller_id', (string) $filterEmployeeId);
                    });
                    break;

                case 'rejected':
                    $base->whereHas('employees', function ($q) use ($filterEmployeeId) {
                        $q->where('employees.id', $filterEmployeeId)
                            ->whereIn('employees_personal_tasks.status', ['reject', 'rejected']);
                    });
                    break;

                case 'accepted':
                    $base->whereHas('employees', function ($q) use ($filterEmployeeId) {
                        $q->where('employees.id', $filterEmployeeId)
                            ->where('employees_personal_tasks.status', 'accepted');
                    });
                    break;

                case 'assigned':
                default:
                    $base->whereHas('employees', function ($q) use ($filterEmployeeId) {
                        $q->where('employees.id', $filterEmployeeId)
                            ->whereNotIn('employees_personal_tasks.status', ['reject', 'rejected']);
                    });
                    break;
            }
        }

        if (!in_array($sort, ['created_at', 'due_date', 'priority', 'task_title'], true)) {
            $sort = 'created_at';
        }

        if (!in_array($dir, ['asc', 'desc'], true)) {
            $dir = 'desc';
        }

        $base->orderBy($sort, $dir)
            ->orderBy('personal_tasks.id', 'desc');

        /*
        |--------------------------------------------------------------------------
        | Duplicate-safe result set
        |--------------------------------------------------------------------------
        | Important: duplicate cards can come from two different sources:
        | 1) duplicated pivot rows in employees_personal_tasks
        | 2) duplicated personal_tasks rows with the same task_id/business number
        |
        | Therefore we do NOT only unique by database id. We reduce the filtered
        | query to one id per real task first, then load only those models.
        */
        $filteredTaskIds = $this->duplicateSafeTaskIds($base);

        $boardTasks = empty($filteredTaskIds)
            ? collect()
            : PersonalTask::query()
                ->with([
                    'employees:id,name,lastname,image',
                    'assignedBy:id,name,lastname,image',
                    'customer:id,customer_no,lastname,name,city,firma',
                    'product:id,article_group,initial',
                    'leadProductList.companyStage',
                    'leadProductList.leadStageSubStage',
                    'leadStage',
                    'leadStageSubStage',
                ])
                ->withTrashed()
                ->whereIn('personal_tasks.id', $filteredTaskIds)
                ->orderByRaw('FIELD(personal_tasks.id, ' . implode(',', array_map('intval', $filteredTaskIds)) . ')')
                ->get();

        $boardTasks = $this->dedupeTaskCollection($boardTasks);

        $listTasks = empty($filteredTaskIds)
            ? PersonalTask::query()
                ->whereRaw('1 = 0')
                ->paginate(20)
                ->withQueryString()
            : PersonalTask::query()
                ->with([
                    'employees:id,name,lastname,image',
                    'assignedBy:id,name,lastname,image',
                    'customer:id,customer_no,lastname,name,city,firma',
                    'product:id,article_group,initial',
                    'leadProductList.companyStage',
                    'leadProductList.leadStageSubStage',
                    'leadStage',
                    'leadStageSubStage',
                ])
                ->withTrashed()
                ->whereIn('personal_tasks.id', $filteredTaskIds)
                ->orderByRaw('FIELD(personal_tasks.id, ' . implode(',', array_map('intval', $filteredTaskIds)) . ')')
                ->paginate(20)
                ->withQueryString();

        if ($tab === 'rejected') {
            $columnMeta = [
                'rejected' => ['label' => 'Abgelehnt', 'color' => '#ef4444'],
            ];

            $columns = [
                'rejected' => $boardTasks,
            ];
        } elseif ($tab === 'archived') {
            $columnMeta = [
                'archived' => ['label' => 'Archiv', 'color' => '#9ca3af'],
            ];

            $columns = [
                'archived' => $boardTasks,
            ];
        } elseif ($tab === 'deleted') {
            $columnMeta = [
                'deleted' => ['label' => 'Gelöscht', 'color' => '#4b5563'],
            ];

            $columns = [
                'deleted' => $boardTasks,
            ];
        } else {
            $columnMeta = $this->boardColumnMeta();

            $columns = [
                'open' => $this->dedupeTaskCollection(
                    $boardTasks->filter(function ($task) {
                        return $this->normalizeBoardColumnFromStatus($task->task_status) === 'open';
                    })
                ),

                'in_progress' => $this->dedupeTaskCollection(
                    $boardTasks->filter(function ($task) {
                        return $this->normalizeBoardColumnFromStatus($task->task_status) === 'in_progress';
                    })
                ),

                'completed' => $this->dedupeTaskCollection(
                    $boardTasks->filter(function ($task) {
                        return $this->normalizeBoardColumnFromStatus($task->task_status) === 'completed';
                    })
                ),
            ];
        }

        $dueFilterOptions = $this->taskDueFilterOptions();

        $leadStages = LeadStage::query()
            ->active()
            ->with('activeSubStages')
            ->ordered()
            ->get();

        return view('admin.todo.personal.task_view', [
            'view' => $view,
            'tab' => $tab,
            'columns' => $columns,
            'tasks' => $listTasks,
            'employeeId' => $employeeId,
            'user' => $user,
            'filters' => compact(
                'search',
                'status',
                'priority',
                'public',
                'isReport',
                'dueFilter',
                'sort',
                'dir',
                'filterEmployeeId',
                'employeeFilterState'
            ),
            'stats' => $this->personalTaskAjaxStatsPayload($request),
            'employees' => $employees,
            'employeeOptions' => $employeeOptions,
            'activityFeed' => $activityFeed,
            'columnMeta' => $columnMeta,
            'dueFilterOptions' => $dueFilterOptions,
            'leadStages' => $leadStages,
        ]);
    }


    public function ajaxTasks(Request $request): JsonResponse
    {
        $query = $this->personalTaskAjaxQuery($request);
        $filteredTaskIds = $this->duplicateSafeTaskIds($query);

        $tasks = empty($filteredTaskIds)
            ? collect()
            : PersonalTask::query()
                ->with([
                    'employees:id,name,lastname,image',
                    'assignedBy:id,name,lastname,image',
                    'customer:id,customer_no,name,lastname,city,firma',
                    'product:id,article_group,initial',
                    'leadProductList.companyStage',
                    'leadProductList.leadStageSubStage',
                    'leadStage',
                    'leadStageSubStage',
                    'keys' => fn($q) => $q->orderBy('id'),
                    'rootComments' => function ($q) {
                        $q->with(['author:id,name,lastname,image', 'replies.author:id,name,lastname,image'])
                            ->latest('created_at');
                    },
                ])
                ->withTrashed()
                ->whereIn('personal_tasks.id', $filteredTaskIds)
                ->orderByRaw('FIELD(personal_tasks.id, ' . implode(',', array_map('intval', $filteredTaskIds)) . ')')
                ->get();

        $tasks = $this->dedupeTaskCollection($tasks)
            ->map(fn(PersonalTask $task) => $this->personalTaskAjaxPayload($task))
            ->values();

        return response()->json([
            'success' => true,
            'tasks' => $tasks,
            'stats' => $this->personalTaskAjaxStatsPayload($request),
        ]);
    }

    public function ajaxStats(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'stats' => $this->personalTaskAjaxStatsPayload($request),
        ]);
    }

    protected function personalTaskAjaxQuery(Request $request)
    {
        $authUser = Auth::user();
        if (!$authUser)
            abort(403);

        $employeeId = $this->currentEmployeeId();
        $userName = (string) ($authUser->name ?? '');
        $scope = (string) $request->input('scope', 'my');
        $state = (string) $request->input('state', 'active');
        $search = trim((string) $request->input('search', ''));
        $priority = trim((string) $request->input('priority', ''));
        $dueFilter = (string) $request->input('due_filter', $request->input('due', 'all'));
        $filterEmployeeId = $request->input('employee_id', $request->input('employee'));
        $leadStageId = $request->input('lead_stage_id', $request->input('stage_id'));
        $leadSubStageId = $request->input('lead_stage_sub_stage_id', $request->input('sub_stage_id'));
        $byYouTaskIds = $this->taskIdsForCurrentUser($employeeId, $userName);

        $query = PersonalTask::query()
            ->select('personal_tasks.*')
            ->withTrashed();

        if (empty($byYouTaskIds)) {
            return $query->whereRaw('1 = 0');
        }

        $query->whereIn('personal_tasks.id', $byYouTaskIds);

        if ($scope === 'created') {
            $query->where('assigned_by', $userName);
        } elseif ($employeeId) {
            $query->whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employees.id', $employeeId)
                    ->whereNotIn('employees_personal_tasks.status', ['reject', 'rejected']);
            });
        }

        match ($state) {
            'pause' => $query->where('task_status', 'pause')->whereNull('personal_tasks.deleted_at')->whereNull('archived_at'),
            'cancel' => $query->where('task_status', 'cancel')->whereNull('personal_tasks.deleted_at')->whereNull('archived_at'),
            'archived' => $query->whereNotNull('archived_at')->whereNull('personal_tasks.deleted_at'),
            'deleted' => $query->onlyTrashed(),
            'rejected' => $query->whereNull('personal_tasks.deleted_at')->where(function ($q) use ($employeeId, $userName) {
                    if ($employeeId) {
                        $q->whereHas('employees', function ($qq) use ($employeeId) {
                            $qq->where('employees.id', $employeeId)->whereIn('employees_personal_tasks.status', ['reject', 'rejected']);
                        });
                    }
                    $q->orWhere(function ($qq) use ($userName) {
                        $qq->where('assigned_by', $userName)->whereHas('employees', fn($qqq) => $qqq->whereIn('employees_personal_tasks.status', ['reject', 'rejected']));
                    });
                }),
            default => $query->whereNull('archived_at')->whereNull('personal_tasks.deleted_at')->whereNotIn('task_status', ['pause', 'cancel', 'junk', 'rejected']),
        };

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('task_title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('task_id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('firma', 'like', "%{$search}%")
                            ->orWhere('customer_no', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%");
                    });
            });
        }

        if ($priority !== '')
            $query->where('priority', $priority);

        if ($filterEmployeeId !== null && $filterEmployeeId !== '') {
            $filterEmployeeId = (int) $filterEmployeeId;
            $query->whereHas('employees', function ($q) use ($filterEmployeeId) {
                $q->where('employees.id', $filterEmployeeId)->whereNotIn('employees_personal_tasks.status', ['reject', 'rejected']);
            });
        }

        $this->applyLeadStageFilters($query, $leadStageId, $leadSubStageId);
        $this->applyDueDateFilter($query, $dueFilter);

        return $query->orderByRaw("CASE WHEN task_status IN ('open','new','send','accepted') THEN 1 WHEN task_status IN ('on_progress','on_going','working','in_progress') THEN 2 WHEN task_status='completed' THEN 3 ELSE 4 END")
            ->orderByRaw('COALESCE(due_date, start_date, created_at) ASC')
            ->orderByDesc('personal_tasks.id');
    }

    protected function personalTaskAjaxStatsPayload(Request $request): array
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return $this->emptyPersonalTaskStats();
        }

        $employeeId = $this->currentEmployeeId();
        $userName = (string) ($authUser->name ?? '');
        $byYouTaskIds = $this->taskIdsForCurrentUser($employeeId, $userName);

        if (empty($byYouTaskIds)) {
            return $this->emptyPersonalTaskStats();
        }

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | Stats must NOT use personalTaskAjaxQuery() because that query applies the
        | currently selected state. Example: when the user opens "Archiviert", the
        | old stats were calculated from archived rows only, so the other tab counts
        | became 0 or wrong.
        |
        | This base query keeps the same global filters (search, priority, due,
        | employee), but it does NOT apply pause/cancel/archive/deleted/rejected
        | state filtering. Every counter below applies its own state condition.
        */
        $base = PersonalTask::query()
            ->select('personal_tasks.*')
            ->withTrashed()
            ->whereIn('personal_tasks.id', $byYouTaskIds);

        $search = trim((string) $request->input('search', ''));
        $priority = trim((string) $request->input('priority', ''));
        $dueFilter = (string) $request->input('due_filter', $request->input('due', 'all'));
        $filterEmployeeId = $request->input('employee_id', $request->input('employee'));
        $leadStageId = $request->input('lead_stage_id', $request->input('stage_id'));
        $leadSubStageId = $request->input('lead_stage_sub_stage_id', $request->input('sub_stage_id'));

        if ($search !== '') {
            $base->where(function ($q) use ($search) {
                $q->where('task_title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('task_id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('firma', 'like', "%{$search}%")
                            ->orWhere('customer_no', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%");
                    });
            });
        }

        if ($priority !== '') {
            $base->where('priority', $this->normalizePriority($priority));
        }

        if ($filterEmployeeId !== null && $filterEmployeeId !== '') {
            $filterEmployeeId = (int) $filterEmployeeId;
            $base->whereHas('employees', function ($q) use ($filterEmployeeId) {
                $q->where('employees.id', $filterEmployeeId);
            });
        }

        $this->applyLeadStageFilters($base, $leadStageId, $leadSubStageId);
        $this->applyDueDateFilter($base, $dueFilter);

        $countSafe = function ($query): int {
            return count($this->duplicateSafeTaskIds($query));
        };

        $activeScope = function ($query) {
            $query->whereNull('personal_tasks.deleted_at')
                ->whereNull('archived_at')
                ->whereNotIn('task_status', ['pause', 'cancel', 'junk', 'rejected']);
        };

        $my = clone $base;
        if ($employeeId) {
            $my->whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employees.id', $employeeId)
                    ->whereNotIn('employees_personal_tasks.status', ['reject', 'rejected', 'cancel', 'cancelled']);
            });
        } else {
            $my->whereRaw('1 = 0');
        }
        $activeScope($my);

        $created = clone $base;
        $created->where('assigned_by', $userName);
        $activeScope($created);

        $active = clone $base;
        $activeScope($active);

        $open = clone $base;
        $activeScope($open);
        $open->where(function ($q) {
            $q->whereNull('task_status')
                ->orWhereIn('task_status', ['open', 'new', 'send', 'accepted']);
        });

        $inProgress = clone $base;
        $activeScope($inProgress);
        $inProgress->whereIn('task_status', ['on_progress', 'on_going', 'working', 'in_progress']);

        $completed = clone $base;
        $completed->whereNull('personal_tasks.deleted_at')
            ->whereNull('archived_at')
            ->where('task_status', 'completed');

        $pause = clone $base;
        $pause->whereNull('personal_tasks.deleted_at')
            ->whereNull('archived_at')
            ->where('task_status', 'pause');

        $cancel = clone $base;
        $cancel->whereNull('personal_tasks.deleted_at')
            ->whereNull('archived_at')
            ->where('task_status', 'cancel');

        $archived = clone $base;
        $archived->whereNull('personal_tasks.deleted_at')
            ->whereNotNull('archived_at');

        $deleted = clone $base;
        $deleted->whereNotNull('personal_tasks.deleted_at');

        $rejected = clone $base;
        $rejected->whereNull('personal_tasks.deleted_at')
            ->where(function ($q) use ($employeeId, $userName) {
                $q->where('task_status', 'rejected');

                if ($employeeId) {
                    $q->orWhereHas('employees', function ($employeeQuery) use ($employeeId) {
                        $employeeQuery->where('employees.id', $employeeId)
                            ->whereIn('employees_personal_tasks.status', ['reject', 'rejected']);
                    });
                }

                if ($userName !== '') {
                    $q->orWhere(function ($createdByMe) use ($userName) {
                        $createdByMe->where('assigned_by', $userName)
                            ->whereHas('employees', function ($employeeQuery) {
                                $employeeQuery->whereIn('employees_personal_tasks.status', ['reject', 'rejected']);
                            });
                    });
                }
            });

        $stats = [
            'my' => $countSafe($my),
            'created' => $countSafe($created),
            'active' => $countSafe($active),
            'open' => $countSafe($open),
            'in_progress' => $countSafe($inProgress),
            'completed' => $countSafe($completed),
            'pause' => $countSafe($pause),
            'paused' => $countSafe($pause),
            'cancel' => $countSafe($cancel),
            'archived' => $countSafe($archived),
            'deleted' => $countSafe($deleted),
            'rejected' => $countSafe($rejected),
        ];

        $stats['total'] = $stats['active'] + $stats['pause'] + $stats['cancel'] + $stats['archived'] + $stats['deleted'] + $stats['rejected'];

        return $stats;
    }

    protected function emptyPersonalTaskStats(): array
    {
        return [
            'total' => 0,
            'my' => 0,
            'created' => 0,
            'active' => 0,
            'open' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'pause' => 0,
            'paused' => 0,
            'cancel' => 0,
            'archived' => 0,
            'deleted' => 0,
            'rejected' => 0,
        ];
    }


    protected function findLeadProductListFromRequest(Request $request): ?LeadProductList
    {
        $query = LeadProductList::query()
            ->with([
                'customer:id,customer_no,name,lastname,firma,city',
                'alternative:id,lead_id,street,postcode,city,full_address,object_name,address_no,main',
                'product:id,article_group,initial',
                'companyStage',
                'leadStageSubStage',
            ]);

        if ($request->filled('lead_product_list_id')) {
            return (clone $query)
                ->whereKey((int) $request->input('lead_product_list_id'))
                ->first();
        }

        if (!$request->filled('customer_id')) {
            return null;
        }

        $query->where('customer_id', (int) $request->input('customer_id'));

        if ($request->filled('alternative_id')) {
            $query->where('alternative_id', (int) $request->input('alternative_id'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', (int) $request->input('product_id'));
        }

        return $query->orderByDesc('id')->first();
    }

    protected function leadStageOptionPayload(LeadStage $stage): array
    {
        $stage->loadMissing('activeSubStages');

        return [
            'id' => $stage->id,
            'key' => $stage->key,
            'name' => $stage->name,
            'color' => $stage->color ?: '#74b2d4',
            'icon' => $stage->icon,
            'sub_stages' => $stage->activeSubStages
                ->map(fn(LeadStageSubStage $subStage) => [
                    'id' => $subStage->id,
                    'lead_stage_id' => $subStage->lead_stage_id,
                    'key' => $subStage->key,
                    'name' => $subStage->name,
                    'color' => $subStage->color ?: '#93c21c',
                    'icon' => $subStage->icon,
                ])
                ->values()
                ->all(),
        ];
    }

    protected function resolveLeadStageContext(Request $request, ?LeadProductList $leadProductList = null): array
    {
        $stage = null;

        if ($request->filled('lead_stage_id')) {
            $stage = LeadStage::query()
                ->active()
                ->with('activeSubStages')
                ->find((int) $request->input('lead_stage_id'));
        }

        if (!$stage && $leadProductList?->companyStage) {
            $stage = LeadStage::query()
                ->active()
                ->with('activeSubStages')
                ->whereKey($leadProductList->companyStage->id)
                ->first();
        }

        if (!$stage && $leadProductList && filled($leadProductList->status)) {
            $stage = LeadStage::query()
                ->active()
                ->with('activeSubStages')
                ->where('key', $leadProductList->status)
                ->first();
        }

        if (!$stage) {
            $stage = LeadStage::query()
                ->active()
                ->with('activeSubStages')
                ->where('is_default', true)
                ->ordered()
                ->first();
        }

        if (!$stage) {
            $stage = LeadStage::query()
                ->active()
                ->with('activeSubStages')
                ->ordered()
                ->first();
        }

        $subStage = null;

        if ($stage && $request->filled('lead_stage_sub_stage_id')) {
            $subStage = LeadStageSubStage::query()
                ->active()
                ->where('lead_stage_id', $stage->id)
                ->find((int) $request->input('lead_stage_sub_stage_id'));
        }

        if (!$subStage && $stage && $leadProductList?->lead_stage_sub_stage_id) {
            $subStage = LeadStageSubStage::query()
                ->active()
                ->where('lead_stage_id', $stage->id)
                ->find((int) $leadProductList->lead_stage_sub_stage_id);
        }

        if (!$subStage && $stage) {
            $subStage = $stage->defaultSubStage()->first()
                ?: $stage->activeSubStages()->first();
        }

        return [
            'lead_product_list_id' => $leadProductList?->id,
            'lead_stage_id' => $stage?->id,
            'lead_stage_key' => $stage?->key,
            'lead_stage_name' => $stage?->name,
            'lead_stage_color' => $stage?->color ?: '#74b2d4',
            'lead_stage_icon' => $stage?->icon,
            'lead_stage_sub_stage_id' => $subStage?->id,
            'lead_stage_sub_stage_key' => $subStage?->key,
            'lead_stage_sub_stage_name' => $subStage?->name,
            'lead_stage_sub_stage_color' => $subStage?->color ?: '#93c21c',
            'lead_stage_sub_stage_icon' => $subStage?->icon,
        ];
    }

    public function leadStageContext(Request $request): JsonResponse
    {
        $request->validate([
            'lead_product_list_id' => ['nullable', 'integer', 'exists:lead_product_lists,id'],
            'customer_id' => ['nullable', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'integer', 'exists:lead_alternative_adds,id'],
            'product_id' => ['nullable', 'integer', 'exists:article_groups,id'],
            'lead_stage_id' => ['nullable', 'integer', 'exists:lead_stages,id'],
            'lead_stage_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
        ]);

        $leadProductList = $this->findLeadProductListFromRequest($request);
        $context = $this->resolveLeadStageContext($request, $leadProductList);

        $stages = LeadStage::query()
            ->active()
            ->with('activeSubStages')
            ->ordered()
            ->get();

        $selectedStage = $context['lead_stage_id']
            ? $stages->firstWhere('id', (int) $context['lead_stage_id'])
            : null;

        return response()->json([
            'success' => true,
            'context' => $context,
            'stage_options' => $stages->map(fn(LeadStage $stage) => $this->leadStageOptionPayload($stage))->values(),
            'sub_stage_options' => $selectedStage
                ? $this->leadStageOptionPayload($selectedStage)['sub_stages']
                : [],
        ]);
    }


    public function updateLeadStage(Request $request, PersonalTask $task): JsonResponse
    {
        $request->validate([
            'lead_stage_id' => ['nullable', 'integer', 'exists:lead_stages,id'],
            'lead_stage_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
        ]);

        $leadProductList = $task->leadProductList;

        if (!$leadProductList && ($task->customer_id || $task->alternative_id || $task->product_id)) {
            $leadProductList = $this->findLeadProductListFromRequest(new Request([
                'customer_id' => $task->customer_id,
                'alternative_id' => $task->alternative_id,
                'product_id' => $task->product_id,
            ]));
        }

        $stageContext = $this->resolveLeadStageContext($request, $leadProductList);

        $selectedLeadStageId = $request->filled('lead_stage_id')
            ? (int) $request->input('lead_stage_id')
            : ($stageContext['lead_stage_id'] ? (int) $stageContext['lead_stage_id'] : null);

        $selectedLeadSubStageId = $request->filled('lead_stage_sub_stage_id')
            ? (int) $request->input('lead_stage_sub_stage_id')
            : ($stageContext['lead_stage_sub_stage_id'] ? (int) $stageContext['lead_stage_sub_stage_id'] : null);

        if (!$selectedLeadStageId && $selectedLeadSubStageId) {
            $selectedLeadStageId = LeadStageSubStage::query()
                ->whereKey($selectedLeadSubStageId)
                ->value('lead_stage_id');
        }

        if ($selectedLeadStageId && $selectedLeadSubStageId) {
            $subStageBelongsToStage = LeadStageSubStage::query()
                ->whereKey($selectedLeadSubStageId)
                ->where('lead_stage_id', $selectedLeadStageId)
                ->exists();

            if (!$subStageBelongsToStage) {
                $selectedLeadSubStageId = null;
            }
        }

        $task->forceFill([
            'lead_product_list_id' => $stageContext['lead_product_list_id'] ?: $task->lead_product_list_id,
            'lead_stage_id' => $selectedLeadStageId,
            'lead_stage_sub_stage_id' => $selectedLeadSubStageId,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'CRM Stage wurde aktualisiert.',
            'context' => $stageContext,
            'task' => $this->personalTaskAjaxPayload($task->fresh()),
        ]);
    }

    protected function personalTaskAjaxPayload(PersonalTask $task): array
    {
        $task->loadMissing([
            'employees:id,name,lastname,image',
            'assignedBy:id,name,lastname,image',
            'customer:id,customer_no,name,lastname,city,firma',
            'product:id,article_group,initial',
            'keys' => fn($q) => $q->orderBy('id'),
            'rootComments.author:id,name,lastname,image',
            'rootComments.replies.author:id,name,lastname,image',
            'leadProductList.companyStage',
            'leadProductList.leadStageSubStage',
            'leadStage',
            'leadStageSubStage',
        ]);

        $customer = $task->customer;
        $product = $task->product;
        $alternative = $task->alternative_id ? LeadAlternativeAdd::find($task->alternative_id) : null;
        $leadProductList = $task->leadProductList;

        $stageContext = $this->resolveLeadStageContext(
            new Request([
                'lead_product_list_id' => $task->lead_product_list_id,
                'lead_stage_id' => $task->lead_stage_id,
                'lead_stage_sub_stage_id' => $task->lead_stage_sub_stage_id,
                'customer_id' => $task->customer_id,
                'alternative_id' => $task->alternative_id,
                'product_id' => $task->product_id,
            ]),
            $leadProductList
        );

        $employees = $task->employees
            ? $task->employees->map(fn($employee) => $this->employeePayload($employee))->values()->all()
            : [];

        $controllerIds = collect($task->controller_id ?? [])->map(fn($id) => (int) $id)->filter()->unique()->values();
        $controllers = $controllerIds->isEmpty() ? [] : Employee::whereIn('id', $controllerIds)->get(['id', 'name', 'lastname', 'image'])->map(fn($e) => $this->employeePayload($e))->values()->all();
        $keys = ($task->keys ?? collect())->map(fn($key) => $this->keyPayload($key))->values()->all();
        $comments = ($task->rootComments ?? collect())->map(fn($comment) => $this->commentPayload($comment))->values()->all();

        return [
            'id' => $task->id,
            'task_id' => $task->task_id,
            'task_title' => $task->task_title,
            'description' => $task->description,
            'task_status' => $task->task_status ?: 'open',
            'board_column' => $this->normalizeBoardColumnFromStatus($task->task_status),
            'priority' => $this->normalizePriority($task->priority ?: 'medium'),
            'color' => $task->color ?: '#93c21c',
            'progress' => (int) ($task->progress ?? 0),
            'public' => (int) ($task->public ?? 0),
            'is_customer' => (int) ($task->is_customer ?? 0),
            'total_day' => $task->total_day,
            'total_time' => $task->total_time,
            'start_date' => optional($task->start_date)->toDateString(),
            'due_date' => optional($task->due_date)->toDateString(),
            'due_time' => $task->due_time,
            'reminder_date' => optional($task->reminder_date)->toDateString(),
            'reminder_time' => $task->reminder_time,
            'customer_id' => $task->customer_id,
            'alternative_id' => $task->alternative_id,
            'product_id' => $task->product_id,
            'lead_product_list_id' => $stageContext['lead_product_list_id'] ?: $task->lead_product_list_id,
            'lead_stage_id' => $stageContext['lead_stage_id'],
            'lead_stage_name' => $stageContext['lead_stage_name'],
            'lead_stage_color' => $stageContext['lead_stage_color'],
            'lead_stage_sub_stage_id' => $stageContext['lead_stage_sub_stage_id'],
            'lead_stage_sub_stage_name' => $stageContext['lead_stage_sub_stage_name'],
            'lead_stage_sub_stage_color' => $stageContext['lead_stage_sub_stage_color'],
            'lead_stage_context' => $stageContext,
            'customer_name' => $customer ? trim(($customer->firma ?: '') ?: (($customer->name ?? '') . ' ' . ($customer->lastname ?? ''))) : null,
            'object_name' => $alternative ? ($alternative->object_name ?: $alternative->full_address ?: trim(($alternative->street ?? '') . ', ' . ($alternative->postcode ?? '') . ' ' . ($alternative->city ?? ''))) : null,
            'product_name' => $product?->article_group,
            'customer' => $customer ? ['id' => $customer->id, 'customer_no' => $customer->customer_no ?? null, 'name' => $customer->name, 'lastname' => $customer->lastname, 'city' => $customer->city ?? null] : null,
            'employees' => $employees,
            'controllers' => $controllers,
            'keys' => $keys,
            'keys_count' => count($keys),
            'comments' => $comments,
            'comments_count' => count($comments),
            'assigned_by' => $task->assigned_by,
            'created_at' => optional($task->created_at)->toDateTimeString(),
            'updated_at' => optional($task->updated_at)->toDateTimeString(),
            'archived_at' => optional($task->archived_at)->toDateTimeString(),
            'deleted_at' => optional($task->deleted_at)->toDateTimeString(),
        ];
    }

    protected function employeePayload(?Employee $employee): array
    {
        if (!$employee)
            return [];
        return [
            'id' => $employee->id,
            'name' => $employee->name,
            'lastname' => $employee->lastname,
            'image' => $employee->image,
            'image_url' => $this->employeeImageUrl($employee->image),
        ];
    }

    protected function employeeImageUrl(?string $image): ?string
    {
        $image = trim((string) $image);
        if ($image === '')
            return null;
        if (Str::startsWith($image, ['http://', 'https://', '/']))
            return $image;
        $clean = ltrim($image, '/');
        if (Str::contains($clean, 'images/employee/'))
            return asset($clean);
        return asset('images/employee/' . $clean);
    }

    protected function keyPayload(PersonalTaskKey $key): array
    {
        $employeeIds = collect($key->employee_id ?? [])->map(fn($id) => (int) $id)->filter()->unique()->values();
        $employees = $employeeIds->isEmpty() ? [] : Employee::whereIn('id', $employeeIds)->get(['id', 'name', 'lastname', 'image'])->map(fn($e) => $this->employeePayload($e))->values()->all();
        return [
            'id' => $key->id,
            'task' => $key->task,
            'duration' => $key->duration,
            'key_description' => $key->key_description,
            'status' => $key->status,
            'is_completed' => (bool) $key->is_completed,
            'employee_id' => $employeeIds->all(),
            'employees' => $employees,
        ];
    }

    protected function commentPayload(PersonalTaskComment $comment): array
    {
        $author = $comment->author ?: $comment->employee;
        return [
            'id' => $comment->id,
            'task_id' => $comment->task_id,
            'comment' => $comment->comment,
            'created_at' => optional($comment->created_at)->format('d.m.Y H:i'),
            'author' => $author ? $this->employeePayload($author) : null,
            'replies' => ($comment->replies ?? collect())->map(fn($reply) => $this->commentPayload($reply))->values()->all(),
        ];
    }

    public function searchCustomers(Request $request): JsonResponse
    {
        $term = trim((string) $request->input('q', ''));
        $page = max((int) $request->input('page', 1), 1);
        $perPage = min(max((int) $request->input('per_page', 20), 10), 50);

        $query = LeadProductList::query()
            ->with([
                'customer:id,customer_no,name,lastname,firma,street,postcode,city',
                'alternative:id,lead_id,street,postcode,city,full_address,object_name,address_no,main',
                'product:id,article_group,initial',
                'companyStage',
                'leadStageSubStage',
            ])
            ->whereHas('customer')
            ->orderByDesc('id');

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->whereHas('customer', function ($cq) use ($term) {
                    $cq->where('customer_no', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('lastname', 'like', "%{$term}%")
                        ->orWhere('firma', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%");
                })->orWhereHas('alternative', function ($aq) use ($term) {
                    $aq->where('street', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%")
                        ->orWhere('object_name', 'like', "%{$term}%")
                        ->orWhere('full_address', 'like', "%{$term}%");
                })->orWhereHas('product', function ($pq) use ($term) {
                    $pq->where('article_group', 'like', "%{$term}%")
                        ->orWhere('initial', 'like', "%{$term}%");
                });
            });
        }

        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $results = $paginator->getCollection()->map(function (LeadProductList $lpl) {
            $customer = $lpl->customer;
            $alt = $lpl->alternative;
            $product = $lpl->product;

            $customerName = trim(($customer->firma ?: '') ?: (($customer->name ?? '') . ' ' . ($customer->lastname ?? ''))) ?: ('Kunde #' . $customer->id);
            $objectName = $alt ? ($alt->object_name ?: $alt->full_address ?: trim(($alt->street ?? '') . ', ' . ($alt->postcode ?? '') . ' ' . ($alt->city ?? ''))) : 'Kein Objekt';
            $productName = $product?->article_group ?: 'Kein Produkt';
            $text = trim(($customer->customer_no ? $customer->customer_no . ' / ' : '') . $customerName . ' · ' . $objectName . ' · ' . $productName);

            $stageContext = $this->resolveLeadStageContext(
                new Request(['lead_product_list_id' => $lpl->id]),
                $lpl
            );

            $stageLine = trim(($stageContext['lead_stage_name'] ?: '-') . ($stageContext['lead_stage_sub_stage_name'] ? ' / ' . $stageContext['lead_stage_sub_stage_name'] : ''));

            return [
                'id' => $customer->id,
                'text' => $text,
                'customer_id' => $customer->id,
                'customer_name' => $customerName,
                'alternative_id' => $alt?->id,
                'object_name' => $objectName,
                'object_address' => $objectName,
                'product_id' => $product?->id,
                'product_name' => $productName,
                'lead_product_list_id' => $lpl->id,
                'lead_stage_context' => $stageContext,
                'lead_stage_id' => $stageContext['lead_stage_id'],
                'lead_stage_name' => $stageContext['lead_stage_name'],
                'lead_stage_color' => $stageContext['lead_stage_color'],
                'lead_stage_sub_stage_id' => $stageContext['lead_stage_sub_stage_id'],
                'lead_stage_sub_stage_name' => $stageContext['lead_stage_sub_stage_name'],
                'lead_stage_sub_stage_color' => $stageContext['lead_stage_sub_stage_color'],
                'html' => '<div class="nt-customer-product-row">'
                    . '<div class="nt-customer-product-title">' . e($customerName) . '</div>'
                    . '<div class="nt-customer-product-meta">'
                    . 'Kunde: ' . e($customer->customer_no ?? '#' . $customer->id) . '<br>'
                    . 'Objekt: ' . e($objectName) . '<br>'
                    . 'Produkt: ' . e($productName) . '<br>'
                    . 'Stage: ' . e($stageLine)
                    . '</div></div>',
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => $paginator->hasMorePages()],
        ]);
    }

    public function appointmentConflicts(Request $request): JsonResponse
    {
        $data = $request->validate([
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'date' => ['required', 'date'],
            'time' => ['nullable', 'date_format:H:i'],
        ]);

        $date = Carbon::parse($data['date'])->toDateString();
        $time = $data['time'] ?? null;
        $employeeIds = collect($data['employee_ids'])->map(fn($id) => (int) $id)->unique()->values()->all();

        $appointments = MainAppointment::query()
            ->with(['employees:id,name,lastname,image'])
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->whereHas('employees', fn($q) => $q->whereIn('employees.id', $employeeIds))
            ->whereNull('deleted_at')
            ->get();

        $conflicts = [];
        foreach ($appointments as $appointment) {
            foreach ($appointment->employees as $employee) {
                if (!in_array((int) $employee->id, $employeeIds, true))
                    continue;
                $conflict = true;
                if ($time && $appointment->start_time && $appointment->end_time) {
                    $conflict = $time >= substr($appointment->start_time, 0, 5) && $time <= substr($appointment->end_time, 0, 5);
                }
                if ($conflict) {
                    $conflicts[] = [
                        'appointment_id' => $appointment->id,
                        'employee_id' => $employee->id,
                        'employee_name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                        'title' => $appointment->name ?: 'Termin',
                        'start' => $date . ' ' . ($appointment->start_time ?: ''),
                        'end' => $date . ' ' . ($appointment->end_time ?: ''),
                    ];
                }
            }
        }

        return response()->json(['success' => true, 'conflicts' => $conflicts]);
    }

    public function updateStatus(Request $request, PersonalTask $task)
    {
        $request->validate([
            'status' => 'required|string|in:open,new,send,accepted,on_progress,on_going,working,completed,pause,cancel,junk,rejected',
        ]);

        $status = $request->input('status');

        $task->task_status = $status;

        if ($status === 'completed') {
            $task->progress = 100;
            $task->archived_at = now();

            $task->keys()->update([
                'status' => 'completed',
                'is_completed' => 1,
                'done_status' => 'completed',
                'done_date' => now(),
            ]);
        }

        if (in_array($status, ['open', 'new', 'send', 'accepted'], true)) {
            $task->progress = $task->progress ?? 0;
        }

        if (in_array($status, ['on_progress', 'on_going', 'working'], true)) {
            $task->progress = $task->progress > 0 ? $task->progress : 1;
        }

        if (in_array($status, ['pause', 'cancel', 'junk'], true)) {
            $task->progress = $task->progress ?? 0;
        }

        if (in_array($status, ['completed', 'cancel', 'junk', 'rejected'], true)) {
            $task->next_reminder_at = null;
            $task->next_repeat_at = null;
        }

        $task->save();

        return response()->json([
            'success' => true,
            'task_id' => $task->id,
            'status' => $task->task_status,
        ]);
    }

    public function accept(Request $request, PersonalTask $task)
    {
        $employeeId = $this->currentEmployeeId();
        if (!$employeeId) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        // pivot status -> accepted
        $task->employees()->updateExistingPivot($employeeId, [
            'status' => 'accepted',
            'change_date' => now()->toDateString(),
            'changed_by' => $employeeId,
            'change_reason' => 'Job accepted',
        ]);

        if (!$task->task_status || $task->task_status === 'send') {
            $task->task_status = 'open';
            $task->save();
        }

        return response()->json(['success' => true]);
    }

    public function reject(Request $request, PersonalTask $task)
    {
        $request->validate([
            'reason' => 'required|string|max:2000',
        ]);

        $employeeId = $this->currentEmployeeId();
        if (!$employeeId) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        $reason = $request->input('reason');

        $task->employees()->updateExistingPivot($employeeId, [
            'status' => 'rejected',
            'reason' => $reason,
            'change_date' => now()->toDateString(),
            'changed_by' => $employeeId,
            'change_reason' => $reason,
        ]);

        // task itself gets "rejected" if nobody else is active
        if ($task->employees()->wherePivot('status', '!=', 'rejected')->count() === 0) {
            $task->task_status = 'rejected';
            $task->save();
        }

        return response()->json(['success' => true]);
    }

    public function pause(Request $request, PersonalTask $task): JsonResponse
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $reason = trim((string) $request->input('reason', ''));
        $employeeId = $this->currentEmployeeId();

        DB::beginTransaction();
        try {
            $task->task_status = 'pause';
            $task->next_reminder_at = null;
            $task->next_repeat_at = null;
            $task->save();

            if ($employeeId) {
                DB::table('employees_personal_tasks')
                    ->where('task_id', $task->id)
                    ->where('employee_id', $employeeId)
                    ->update([
                        'status' => 'pause',
                        'reason' => $reason ?: 'Aufgabe pausiert',
                        'change_reason' => $reason ?: 'Aufgabe pausiert',
                        'changed_by' => $employeeId,
                        'change_date' => now()->toDateString(),
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aufgabe wurde pausiert und aus dem Kanban entfernt.',
                'task_id' => $task->id,
                'status' => $task->task_status,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Task pause failed', ['task_id' => $task->id, 'message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Aufgabe konnte nicht pausiert werden.', 'error' => $e->getMessage()], 500);
        }
    }

    public function resume(Request $request, PersonalTask $task): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', 'string', 'in:open,on_progress,in_progress,working,completed'],
        ]);

        $status = $request->input('status', 'open');
        if (in_array($status, ['in_progress', 'working'], true)) {
            $status = 'on_progress';
        }

        $employeeId = $this->currentEmployeeId();

        DB::beginTransaction();
        try {
            $task->task_status = $status;
            $task->archived_at = null;
            if ($status === 'open') {
                $task->progress = min((int) ($task->progress ?? 0), 99);
            }
            if ($status === 'on_progress') {
                $task->progress = max((int) ($task->progress ?? 0), 1);
            }
            if ($status === 'completed') {
                $task->progress = 100;
                $task->archived_at = now();
            }
            $task->save();

            if ($employeeId) {
                DB::table('employees_personal_tasks')
                    ->where('task_id', $task->id)
                    ->where('employee_id', $employeeId)
                    ->update([
                        'status' => 'accepted',
                        'reason' => null,
                        'change_reason' => 'Aufgabe wiederhergestellt',
                        'changed_by' => $employeeId,
                        'change_date' => now()->toDateString(),
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aufgabe wurde wieder in die aktive Liste verschoben.',
                'task_id' => $task->id,
                'status' => $task->task_status,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Task resume failed', ['task_id' => $task->id, 'message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Aufgabe konnte nicht wiederhergestellt werden.', 'error' => $e->getMessage()], 500);
        }
    }

    public function cancel(Request $request, PersonalTask $task): JsonResponse
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $reason = trim((string) $request->input('reason', ''));
        $employeeId = $this->currentEmployeeId();

        DB::beginTransaction();
        try {
            $task->task_status = 'cancel';
            $task->next_reminder_at = null;
            $task->next_repeat_at = null;
            $task->save();

            if ($employeeId) {
                DB::table('employees_personal_tasks')
                    ->where('task_id', $task->id)
                    ->where('employee_id', $employeeId)
                    ->update([
                        'status' => 'cancel',
                        'reason' => $reason ?: 'Aufgabe abgebrochen',
                        'change_reason' => $reason ?: 'Aufgabe abgebrochen',
                        'changed_by' => $employeeId,
                        'change_date' => now()->toDateString(),
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aufgabe wurde abgebrochen und aus dem Kanban entfernt.',
                'task_id' => $task->id,
                'status' => $task->task_status,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Task cancel failed', ['task_id' => $task->id, 'message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Aufgabe konnte nicht abgebrochen werden.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateColor(Request $request, PersonalTask $task)
    {
        $request->validate([
            'color' => 'required|string|max:20',
        ]);

        $task->color = $request->input('color');
        $task->save();

        return response()->json(['success' => true]);
    }

    public function updateVisibility(Request $request, PersonalTask $task)
    {
        $request->validate([
            'public' => 'required|boolean',
        ]);

        $task->public = $request->boolean('public');
        $task->save();

        return response()->json(['success' => true]);
    }

    public function archive(Request $request, PersonalTask $task): JsonResponse
    {
        $task->archived_at = now();
        $task->next_reminder_at = null;
        $task->next_repeat_at = null;
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Aufgabe wurde archiviert und aus dem Kanban entfernt.',
            'task_id' => $task->id,
            'archived_at' => optional($task->archived_at)->toDateTimeString(),
        ]);
    }

    public function syncEmployees(Request $request, PersonalTask $task)
    {
        $request->validate([
            'employee_ids' => 'array',
            'employee_ids.*' => 'integer|exists:employees,id',
        ]);

        $ids = $request->input('employee_ids', []);
        $pivotData = [];
        $now = now()->toDateString();

        foreach ($ids as $id) {
            $pivotData[$id] = [
                'status' => 'accepted',
                'change_date' => $now,
            ];
        }

        $task->employees()->sync($pivotData);

        return response()->json(['success' => true]);
    }

    public function detachEmployee(PersonalTask $task, Employee $employee)
    {
        $task->employees()->detach($employee->id);

        return response()->json(['success' => true]);
    }


    protected function validationJsonOrRedirect(Request $request, $validator, string $message = 'Validierungsfehler')
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $validator->errors(),
            ], 422);
        }

        return redirect()->back()->withErrors($validator)->withInput();
    }

    public function store(Request $request)
    {
        return $this->savePersonalTaskFromRequest($request, null);
    }

    protected function savePersonalTaskFromRequest(Request $request, ?PersonalTask $task = null)
    {
        $isEdit = (bool) $task;
        $rawKeys = $request->input('key', []);
        $stepEmployeeMode = $request->input('step_employee_mode', 'all') === 'per_step' ? 'per_step' : 'all';

        /*
        |--------------------------------------------------------------------------
        | Normalize steps before validation
        |--------------------------------------------------------------------------
        | The old validation failed when the user entered only duration/employee
        | but left the step title empty. That is a normal workflow in this drawer.
        | Here we clean empty rows, accept decimal durations, and auto-name empty
        | steps from the main task title.
        */
        $normalizedKeys = [];
        foreach ($rawKeys as $row) {
            $taskText = trim((string) ($row['task'] ?? ''));
            $keyDescription = trim((string) ($row['key_description'] ?? ''));
            $duration = isset($row['duration']) && $row['duration'] !== ''
                ? max(0, round((float) str_replace(',', '.', (string) $row['duration']), 2))
                : 0;
            $employeeIds = collect($row['employee_id'] ?? [])
                ->filter(fn($id) => $id !== null && $id !== '')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            $hasStepData = $taskText !== '' || $keyDescription !== '' || $duration > 0 || !empty($employeeIds) || !empty($row['id']);
            if (!$hasStepData) {
                continue;
            }

            if ($taskText === '') {
                $taskText = trim((string) $request->input('task_title', 'Aufgabenschritt'));
            }

            $normalizedKeys[] = [
                'id' => !empty($row['id']) ? (int) $row['id'] : null,
                'task' => $taskText,
                'duration' => $duration,
                'key_description' => $keyDescription,
                'status' => $row['status'] ?? 'accepted',
                'employee_id' => $employeeIds,
            ];
        }
        $request->merge([
            'key' => $normalizedKeys,
            'priority' => $this->normalizePriority($request->input('priority', 'medium')),
        ]);

        if ($isEdit && $task) {
            $request->merge([
                'lead_product_list_id' => $request->filled('lead_product_list_id') ? $request->input('lead_product_list_id') : $task->lead_product_list_id,
                'customer_id' => $request->filled('customer_id') ? $request->input('customer_id') : $task->customer_id,
                'alternative_id' => $request->filled('alternative_id') ? $request->input('alternative_id') : $task->alternative_id,
                'product_id' => $request->filled('product_id') ? $request->input('product_id') : $task->product_id,
                'lead_stage_id' => $request->filled('lead_stage_id') ? $request->input('lead_stage_id') : $task->lead_stage_id,
                'lead_stage_sub_stage_id' => $request->filled('lead_stage_sub_stage_id') ? $request->input('lead_stage_sub_stage_id') : $task->lead_stage_sub_stage_id,
            ]);
        }

        $leadProductList = $this->findLeadProductListFromRequest($request);

        if ($leadProductList) {
            $request->merge([
                'lead_product_list_id' => $leadProductList->id,
                'customer_id' => $request->input('customer_id') ?: $leadProductList->customer_id,
                'alternative_id' => $request->input('alternative_id') ?: $leadProductList->alternative_id,
                'product_id' => $request->input('product_id') ?: $leadProductList->product_id,
            ]);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'task_title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'task_status' => ['nullable', 'string', 'in:open,new,send,accepted,on_progress,on_going,working,in_progress,completed,pause,cancel,junk,rejected'],
                'due_date' => ['nullable', 'date'],
                'due_time' => ['nullable', 'date_format:H:i'],
                'start_date' => ['nullable', 'date'],
                'total_day' => ['nullable', 'numeric', 'min:0'],
                'total_time' => ['nullable', 'numeric', 'min:0'],
                'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
                'priority' => ['nullable', 'string', 'max:80'],
                'color' => ['nullable', 'string', 'max:30'],
                'type' => ['nullable', 'string', 'max:60'],
                'repeat' => ['nullable', 'string', 'max:120'],
                'reminder_date' => ['nullable', 'date'],
                'reminder_time' => ['nullable', 'date_format:H:i'],

                'employee' => ['nullable', 'array'],
                'employee.*' => ['nullable', 'integer', 'exists:employees,id'],
                'controller' => ['nullable', 'array'],
                'controller.*' => ['nullable', 'integer', 'exists:employees,id'],

                'step_employee_mode' => ['nullable', 'string', 'in:all,per_step'],
                'key' => ['nullable', 'array'],
                'key.*.id' => ['nullable', 'integer', 'exists:personal_task_keys,id'],
                'key.*.task' => ['nullable', 'string', 'max:255'],
                'key.*.duration' => ['nullable', 'numeric', 'min:0'],
                'key.*.key_description' => ['nullable', 'string'],
                'key.*.status' => ['nullable', 'string', 'max:60'],
                'key.*.employee_id' => ['nullable', 'array'],
                'key.*.employee_id.*' => ['nullable', 'integer', 'exists:employees,id'],

                'is_customer' => ['nullable'],
                'customer_id' => ['nullable', 'integer', 'exists:new_leads,id'],
                'alternative_id' => ['nullable', 'integer', 'exists:lead_alternative_adds,id'],
                'product_id' => ['nullable', 'integer', 'exists:article_groups,id'],
                'lead_product_list_id' => ['nullable', 'integer', 'exists:lead_product_lists,id'],
                'lead_stage_id' => ['nullable', 'integer', 'exists:lead_stages,id'],
                'lead_stage_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
                'public' => ['nullable'],
                'is_report' => ['nullable'],
            ],
            [
                'task_title.required' => 'Bitte geben Sie einen Aufgabentitel ein.',
                'task_title.max' => 'Der Aufgabentitel darf maximal 255 Zeichen lang sein.',
                'due_date.date' => 'Das Fälligkeitsdatum ist ungültig.',
                'due_time.date_format' => 'Die Fälligkeitsuhrzeit muss im Format HH:MM sein.',
                'start_date.date' => 'Das Startdatum ist ungültig.',
                'priority.string' => 'Die Priorität muss als Text gesendet werden.',
                'progress.max' => 'Der Fortschritt darf maximal 100 sein.',
                'employee.*.exists' => 'Ein ausgewählter Mitarbeiter existiert nicht mehr.',
                'controller.*.exists' => 'Ein ausgewählter Controller existiert nicht mehr.',
                'key.*.duration.numeric' => 'Die Dauer eines Schritts muss eine Zahl sein, z.B. 0.5 oder 1.25.',
                'key.*.duration.min' => 'Die Dauer eines Schritts darf nicht negativ sein.',
                'key.*.employee_id.*.exists' => 'Ein Mitarbeiter in einem Schritt existiert nicht mehr.',
                'customer_id.exists' => 'Der ausgewählte Kunde existiert nicht mehr.',
                'alternative_id.exists' => 'Das ausgewählte Objekt existiert nicht mehr.',
                'product_id.exists' => 'Das ausgewählte Produkt existiert nicht mehr.',
                'lead_product_list_id.exists' => 'Die ausgewählte Lead-Produkt-Verknüpfung existiert nicht mehr.',
                'lead_stage_id.exists' => 'Die ausgewählte Lead Stage existiert nicht mehr.',
                'lead_stage_sub_stage_id.exists' => 'Die ausgewählte Sub Stage existiert nicht mehr.',
            ],
            [
                'task_title' => 'Aufgabentitel',
                'due_date' => 'Fälligkeitsdatum',
                'due_time' => 'Fälligkeitsuhrzeit',
                'start_date' => 'Startdatum',
                'total_day' => 'Gesamttage',
                'total_time' => 'Gesamtstunden',
                'priority' => 'Priorität',
                'employee' => 'Mitarbeiter',
                'controller' => 'Controller',
                'key.*.task' => 'Schritt-Titel',
                'key.*.duration' => 'Schritt-Dauer',
                'key.*.employee_id' => 'Schritt-Mitarbeiter',
                'customer_id' => 'Kunde',
                'alternative_id' => 'Objekt',
                'product_id' => 'Produkt',
                'lead_product_list_id' => 'Lead Produkt',
                'lead_stage_id' => 'Lead Stage',
                'lead_stage_sub_stage_id' => 'Sub Stage',
            ]
        );

        if ($validator->fails()) {
            return $this->validationJsonOrRedirect($request, $validator, 'Bitte prüfen Sie die rot markierten Felder.');
        }

        $topEmployees = collect($request->input('employee', []))->filter()->map(fn($id) => (int) $id)->unique()->values()->all();
        $keyEmployees = collect($normalizedKeys)->pluck('employee_id')->flatten()->filter()->map(fn($id) => (int) $id)->unique()->values()->all();
        $strictEmployeeIds = $stepEmployeeMode === 'per_step' ? $keyEmployees : $topEmployees;
        if (empty($strictEmployeeIds))
            $strictEmployeeIds = $keyEmployees ?: $topEmployees;

        if (empty($strictEmployeeIds)) {
            $field = $stepEmployeeMode === 'per_step' ? 'key.0.employee_id' : 'employee';
            return response()->json([
                'success' => false,
                'message' => 'Bitte prüfen Sie die rot markierten Felder.',
                'errors' => [
                    $field => ['Bitte wählen Sie mindestens einen Mitarbeiter aus.'],
                ],
            ], 422);
        }

        $controllerIds = collect($request->input('controller', []))->filter()->map(fn($id) => (int) $id)->unique()->values()->all();
        $stageContext = $this->resolveLeadStageContext($request, $leadProductList);

        /*
        |--------------------------------------------------------------------------
        | Persist the exact Select2 values in personal_tasks
        |--------------------------------------------------------------------------
        | leadStageContext can fall back to the CRM lead_product_lists status.
        | For the personal task save, the selected values from the drawer/profile
        | must win, otherwise the table columns stay null or old.
        */
        $selectedLeadStageId = $request->filled('lead_stage_id')
            ? (int) $request->input('lead_stage_id')
            : ($stageContext['lead_stage_id'] ? (int) $stageContext['lead_stage_id'] : null);

        $selectedLeadSubStageId = $request->filled('lead_stage_sub_stage_id')
            ? (int) $request->input('lead_stage_sub_stage_id')
            : ($stageContext['lead_stage_sub_stage_id'] ? (int) $stageContext['lead_stage_sub_stage_id'] : null);

        if (!$selectedLeadStageId && $selectedLeadSubStageId) {
            $selectedLeadStageId = LeadStageSubStage::query()
                ->whereKey($selectedLeadSubStageId)
                ->value('lead_stage_id');
        }

        if ($selectedLeadStageId && $selectedLeadSubStageId) {
            $subStageBelongsToStage = LeadStageSubStage::query()
                ->whereKey($selectedLeadSubStageId)
                ->where('lead_stage_id', $selectedLeadStageId)
                ->exists();

            if (!$subStageBelongsToStage) {
                $selectedLeadSubStageId = null;
            }
        }

        // Always calculate total hours/days on the backend too. Frontend values are only hints.
        $totals = $this->calculatePersonalTaskTotals($request, $normalizedKeys);

        DB::beginTransaction();
        try {
            $task = $task ?: new PersonalTask();
            if (!$isEdit) {
                $task->assigned_by = (string) (Auth::user()->name ?? Auth::id());
                $task->task_id = $task->task_id ?: 'PT-' . now()->format('Ymd-His');
            }
            $task->fill([
                'task_title' => $request->task_title,
                'description' => $request->description,
                'task_status' => $request->task_status ?: ($task->task_status ?: 'open'),
                'due_date' => $request->due_date,
                'due_time' => $request->due_time,
                'start_date' => $request->start_date,
                'total_day' => $totals['total_day'],
                'total_time' => $totals['total_time'],
                'progress' => (int) ($request->progress ?? 0),
                'priority' => $this->normalizePriority($request->priority ?? 'medium'),
                'color' => $request->color ?: '#93c21c',
                'public' => $request->has('public') ? 1 : 0,
                'type' => $request->type ?: 'task',
                'repeat' => $this->normalizeRepeatValue($request->repeat),
                'reminder_date' => $request->reminder_date ?: null,
                'reminder_time' => $request->reminder_time ?: null,
                'is_customer' => $request->has('is_customer') ? 1 : 0,
                'customer_id' => $request->customer_id ?: null,
                'alternative_id' => $request->alternative_id ?: null,
                'product_id' => $request->product_id ?: null,
                'lead_product_list_id' => $stageContext['lead_product_list_id'] ?: $request->input('lead_product_list_id'),
                'lead_stage_id' => $selectedLeadStageId,
                'lead_stage_sub_stage_id' => $selectedLeadSubStageId,
                'is_report' => $request->has('is_report') ? 1 : 0,
                'controller_id' => !empty($controllerIds) ? $controllerIds : $strictEmployeeIds,
            ]);
            $task->save();
            $this->syncTaskSchedulerFields($task);

            EmployeesPersonalTask::where('task_id', $task->id)->delete();
            foreach ($strictEmployeeIds as $empId) {
                EmployeesPersonalTask::create(['task_id' => $task->id, 'employee_id' => $empId, 'status' => 'accepted']);
            }

            $handled = [];
            foreach ($normalizedKeys as $row) {
                $rowEmployees = $stepEmployeeMode === 'per_step'
                    ? ($row['employee_id'] ?: [])
                    : $strictEmployeeIds;

                $payload = [
                    'task' => $row['task'],
                    'duration' => $row['duration'],
                    'total_time' => $row['duration'],
                    'key_description' => $row['key_description'],
                    'status' => $row['status'] ?: 'accepted',
                    'employee_id' => array_values($rowEmployees),
                ];

                if (!empty($row['id'])) {
                    $key = PersonalTaskKey::where('id', $row['id'])->where('personal_task_id', $task->id)->first();
                    if ($key) {
                        $key->update($payload);
                        $handled[] = $key->id;
                        continue;
                    }
                }

                $key = PersonalTaskKey::create(array_merge($payload, ['personal_task_id' => $task->id]));
                $handled[] = $key->id;
            }

            if (!empty($handled)) {
                PersonalTaskKey::where('personal_task_id', $task->id)->whereNotIn('id', $handled)->delete();
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Aufgabe gespeichert', 'task' => $this->personalTaskAjaxPayload($task->fresh(['employees', 'customer', 'product', 'keys', 'rootComments.author', 'leadProductList.companyStage', 'leadProductList.leadStageSubStage', 'leadStage', 'leadStageSubStage']))]);
            }
            return redirect()->route('personal-tasks.profile', $task)->with('success', 'Aufgabe gespeichert.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Task save failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $data['data'] = PersonalTask::findOrFail($id);
        $data['employees'] = DB::table('employees')
            ->where('status', '!=', 'Deactive')
            ->select('id', 'name', 'lastname', 'image')
            ->get();

        $data['task_employee'] = DB::table('employees_personal_tasks')
            ->join('employees as emp', 'emp.id', '=', 'employees_personal_tasks.employee_id')
            ->join('personal_tasks as task', 'task.id', '=', 'employees_personal_tasks.task_id')
            ->select(
                'employees_personal_tasks.*',
                'emp.name',
                'emp.lastname',
                'emp.image',
                'emp.gender',
                'task.task_title'
            )
            ->where('employees_personal_tasks.task_id', $id)
            ->get();

        $data['key_task'] = DB::table('personal_task_keys')
            ->where('personal_task_id', $id)
            ->get();

        $data['sub_task'] = DB::table('personal_sub_tasks')
            ->where('task_id', $id)
            ->get();

        $data['leadStages'] = LeadStage::query()
            ->active()
            ->with('activeSubStages')
            ->ordered()
            ->get();

        return view('admin.todo.personal.personal_edit', $data);

    }
    public function update(Request $request)
    {
        $id = (int) $request->input('id');
        $task = PersonalTask::findOrFail($id);
        return $this->savePersonalTaskFromRequest($request, $task);
    }


    protected function priorityValues(): array
    {
        // Exact values sent by resources/views/admin/todo/personal/task_view.blade.php
        return ['normal', 'medium', 'high', 'very high'];
    }

    protected function normalizePriority(?string $priority): string
    {
        $raw = trim((string) $priority);

        if ($raw === '') {
            return 'medium';
        }

        $key = mb_strtolower($raw);
        $key = str_replace(['-', '.', '/', '\\'], ' ', $key);
        $key = preg_replace('/\s+/', ' ', $key);
        $key = trim($key);

        $map = [
            'normal' => 'normal',
            'keiner' => 'normal',
            'keine' => 'normal',
            'niedrig' => 'normal',
            'low' => 'normal',

            'medium' => 'medium',
            'mittel' => 'medium',

            'high' => 'high',
            'hoch' => 'high',

            'very high' => 'very high',
            'veryhigh' => 'very high',
            'very_high' => 'very high',
            'sehr hoch' => 'very high',
            'sehr wichtig' => 'very high',
            'urgent' => 'very high',
            'dringend' => 'very high',
        ];

        return $map[$key] ?? 'medium';
    }

    /**
     * Show task profile with:
     * - meta data
     * - keys (steps) + assigned employees
     * - simple derived history (created, keys, comments)
     * - comments (reports) + replies
     */
    public function show(PersonalTask $task)
    {
        $authUser = Auth::user();
        $employeeId = (int) ($authUser->name ?? 0);   // your convention

        // Eager-load relations you need
        $task->load([
            'assignedBy',
            'employees',
            'customer',
            'product',
            'leadProductList.companyStage',
            'leadProductList.leadStageSubStage',
            'leadStage',
            'leadStageSubStage',
            'keys.doneBy',              // make sure you have relation doneBy() on PersonalTaskKey
            'attachments',
            'comments.employee',
        ]);

        // Controllers from JSON
        if (is_array($task->controller_id)) {
            $controllerEmployees = Employee::whereIn('id', $task->controller_id)->get();
        } elseif (is_string($task->controller_id)) {
            $ids = json_decode($task->controller_id, true) ?: [];
            $controllerEmployees = Employee::whereIn('id', $ids)->get();
        } else {
            $controllerEmployees = collect();
        }

        // All employees for dropdowns etc.
        $allEmployees = Employee::select('id', 'name', 'lastname', 'image')
            ->where('status', 'Active')
            ->get();

        $leadStages = LeadStage::query()
            ->active()
            ->with('activeSubStages')
            ->ordered()
            ->get();

        $leadStageContext = $this->resolveLeadStageContext(new Request([
            'lead_product_list_id' => $task->lead_product_list_id,
            'lead_stage_id' => $task->lead_stage_id,
            'lead_stage_sub_stage_id' => $task->lead_stage_sub_stage_id,
            'customer_id' => $task->customer_id,
            'alternative_id' => $task->alternative_id,
            'product_id' => $task->product_id,
        ]), $task->leadProductList);

        // Steps / keys
        $keys = $task->keys()->orderBy('id')->get();

        // Comments / reports
        $comments = $task->comments()
            ->with('employee')
            ->orderBy('created_at', 'asc')
            ->get();

        // -------------------------------------------------
        // Build history
        // -------------------------------------------------
        $history = [];

        // 0) Task created
        $creatorName = $task->assignedBy
            ? ($task->assignedBy->name . ' ' . $task->assignedBy->lastname)
            : 'Unbekannt';

        $history[] = [
            'at' => $task->created_at,     // Carbon
            'type' => 'task_created',
            'title' => 'Aufgabe erstellt',
            'details' => $task->task_title,
            'by' => $creatorName,
        ];

        // 1) Keys done
        foreach ($keys as $key) {
            if ($key->is_completed && $key->done_by) {
                $emp = $key->doneBy;
                $name = $emp ? ($emp->name . ' ' . $emp->lastname) : 'Unbekannt';

                // done_date is DATE or DATETIME, submit_time is TIME
                $doneDate = $key->done_date ? Carbon::parse($key->done_date) : null;
                $submitTime = $key->submit_time; // e.g. "13:30:00" or null

                if ($doneDate && $submitTime) {
                    try {
                        $at = $doneDate->copy()->setTimeFromTimeString($submitTime);
                    } catch (\Throwable $e) {
                        // Fallback if submit_time is malformed
                        $at = $doneDate;
                    }
                } elseif ($doneDate) {
                    $at = $doneDate;
                } else {
                    $at = $key->created_at ?? $task->created_at;
                }

                $history[] = [
                    'at' => $at,                // Carbon, NOT concatenated string
                    'type' => 'key_done',
                    'title' => 'Schritt abgeschlossen',
                    'details' => $key->task,
                    'by' => $name,
                ];
            }
        }

        // 2) Comments (reports)
        foreach ($comments as $comment) {
            $emp = $comment->employee;
            $name = $emp ? ($emp->name . ' ' . $emp->lastname) : 'Unbekannt';

            $history[] = [
                'at' => $comment->created_at,    // Carbon
                'type' => $comment->parent_id ? 'comment_reply' : 'comment',
                'title' => $comment->parent_id ? 'Antwort auf Bericht' : 'Bericht erstellt',
                'details' => $comment->comment,
                'by' => $name,
            ];
        }

        // 3) Sort history chronologically
        usort($history, function ($a, $b) {
            $aAt = $a['at'];
            $bAt = $b['at'];

            // both should be Carbon, but be defensive
            if ($aAt instanceof Carbon) {
                $aTs = $aAt->getTimestamp();
            } else {
                $aTs = strtotime((string) $aAt);
            }

            if ($bAt instanceof Carbon) {
                $bTs = $bAt->getTimestamp();
            } else {
                $bTs = strtotime((string) $bAt);
            }

            return $aTs <=> $bTs;
        });

        return view('admin.todo.personal.profile', [
            'task' => $task,
            'employeeId' => $employeeId,
            'controllerEmployees' => $controllerEmployees,
            'allEmployees' => $allEmployees,
            'keys' => $keys,
            'comments' => $comments,
            'history' => $history,
            'leadStages' => $leadStages,
            'leadStageContext' => $leadStageContext,
        ]);
    }
    public function toggleKey(Request $request, PersonalTaskKey $key)
    {
        $data = $request->validate([
            'mode' => ['required', 'in:complete,undo'],
            'done_status' => ['nullable', 'in:complete,part'],
            'work_progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'submit_time' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($data['mode'] === 'undo') {
            $key->update([
                'is_completed' => 0,
                'done_status' => null,
                'work_progress' => null,
                'submit_time' => null,
                'done_by' => null,
                'done_date' => null,
            ]);

            return response()->json(['success' => true]);
        }

        // complete / partial
        $doneStatus = $data['done_status'] ?? 'complete';
        $workProgress = $doneStatus === 'complete'
            ? 100
            : ($data['work_progress'] ?? 50);

        $key->update([
            'is_completed' => 1,
            'done_status' => $doneStatus,
            'work_progress' => $workProgress,
            'submit_time' => $data['submit_time'] ?? null,
            'done_by' => auth()->user()->name ?? auth()->id(), // anpassen, je nach deiner Logik
            'done_date' => now()->toDateString(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Store a new top-level comment (report)
     */
    public function storeComment(Request $request, PersonalTask $task)
    {
        $data = $request->validate([
            'comment' => ['required', 'string'],
        ]);

        $employeeId = (int) (Auth::user()->name ?? 0);

        $comment = PersonalTaskComment::create([
            'task_id' => $task->id,
            'comment_by' => $employeeId,
            'comment' => $data['comment'],
            'status' => 'Published',
            'parent_id' => null,
        ]);

        $comment->load('employee');

        return response()->json([
            'success' => true,
            'comment' => $comment,
        ]);
    }

    /**
     * Store a reply to an existing comment
     */
    public function storeReply(Request $request, PersonalTaskComment $comment)
    {
        $data = $request->validate([
            'comment' => ['required', 'string'],
        ]);

        $employeeId = (int) (Auth::user()->name ?? 0);

        $reply = PersonalTaskComment::create([
            'task_id' => $comment->task_id,
            'comment_by' => $employeeId,
            'comment' => $data['comment'],
            'status' => 'Published',
            'parent_id' => $comment->id,
        ]);

        $reply->load('employee');

        return response()->json([
            'success' => true,
            'reply' => $reply,
        ]);
    }

    /**
     * Helper: convert decimal hours -> "HH:MM:SS"
     * e.g. 1.5 -> "01:30:00"
     */
    protected function decimalToTime($decimal)
    {
        $minutes = (int) round($decimal * 60);
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return sprintf('%02d:%02d:00', $h, $m);
    }

    public function updateControllers(Request $request, PersonalTask $task)
    {
        $data = $request->validate([
            'controllers' => ['nullable', 'array'],
            'controllers.*' => ['integer', 'exists:employees,id'],
        ]);

        $task->controller_id = $data['controllers'] ?? [];
        $task->save();

        return response()->json([
            'success' => true,
            'controllers' => $task->controller_id,
        ]);
    }

    /**
     * Add employees for the whole task (employees_personal_tasks pivot).
     * Does NOT touch per-key assignments.
     */
    public function addTaskEmployees(Request $request, PersonalTask $task)
    {
        $data = $request->validate([
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
        ]);

        $employeeIds = $data['employee_ids'];

        // Attach without removing existing; keep pivot attributes as-is.
        $task->employees()->syncWithoutDetaching($employeeIds);

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Add employees for specific keys of this task (per-key assignment).
     * Writes into personal_task_keys.employee_id (JSON array).
     */
    public function addKeyEmployeesForTask(Request $request, PersonalTask $task)
    {
        $data = $request->validate([
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
            'key_ids' => ['required', 'array', 'min:1'],
            'key_ids.*' => ['integer'],
        ]);

        $employeeIds = $data['employee_ids'];
        $keyIds = $data['key_ids'];

        $keys = PersonalTaskKey::where('personal_task_id', $task->id)
            ->whereIn('id', $keyIds)
            ->get();

        foreach ($keys as $key) {
            $current = [];

            if (is_array($key->employee_id)) {
                $current = $key->employee_id;
            } elseif (is_string($key->employee_id) && $key->employee_id !== '') {
                $current = json_decode($key->employee_id, true) ?: [];
            }

            $merged = array_values(array_unique(array_merge($current, $employeeIds)));
            $key->employee_id = $merged;
            $key->save();
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function storeAttachment(Request $request, PersonalTask $task)
    {
        $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'max:51200'],
        ]);

        $stored = [];

        $customerId = null;

        if (!empty($task->customer_id)) {
            $customerExists = NewLeads::where('id', $task->customer_id)->exists();

            if ($customerExists) {
                $customerId = $task->customer_id;
            }
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $targetDir = public_path('images/task/personal/document');

                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0775, true);
                }

                $fileName = time()
                    . '_'
                    . Str::slug($original)
                    . '_'
                    . Str::random(5)
                    . '.'
                    . $ext;

                $file->move($targetDir, $fileName);

                $attachment = PersonalTaskAttachment::create([
                    'task_id' => $task->id,
                    'customer_id' => $customerId,
                    'image_name' => $original,
                    'image' => $fileName,
                    'file_type' => $ext,
                ]);

                $stored[] = [
                    'id' => $attachment->id,
                    'image_name' => $attachment->image_name,
                    'file_type' => $attachment->file_type,
                    'url' => asset('images/task/personal/document/' . $attachment->image),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'files' => $stored,
        ]);
    }
    /**
     * Delete a single attachment.
     */
    public function destroyAttachment(PersonalTaskAttachment $attachment)
    {
        $path = public_path('images/task/personal/document/' . $attachment->image);
        if (is_file($path)) {
            @unlink($path);
        }

        $attachment->delete();

        return response()->json([
            'success' => true,
        ]);
    }


    /**
     * Calculate total hours and total days for the task.
     * Priority:
     * 1) Sum all Schritt durations.
     * 2) If no Schritt duration exists, calculate from start_date -> due_date/due_time.
     * 3) Fallback to manually submitted total_time/total_day.
     *
     * One working day = 8 hours.
     */
    protected function calculatePersonalTaskTotals(Request $request, array $normalizedKeys): array
    {
        $hoursFromSteps = collect($normalizedKeys)->sum(function ($row) {
            return (float) ($row['duration'] ?? 0);
        });

        $totalHours = round((float) $hoursFromSteps, 2);

        if ($totalHours <= 0 && $request->filled('start_date') && $request->filled('due_date')) {
            try {
                $start = Carbon::parse($request->input('start_date') . ' 00:00:00');
                $endTime = $request->input('due_time') ?: '23:59:59';
                $end = Carbon::parse($request->input('due_date') . ' ' . $endTime);

                if ($end->greaterThan($start)) {
                    $totalHours = round($start->floatDiffInHours($end), 2);
                }
            } catch (\Throwable $e) {
                $totalHours = 0;
            }
        }

        if ($totalHours <= 0) {
            $totalHours = round((float) str_replace(',', '.', (string) $request->input('total_time', 0)), 2);
        }

        $totalDays = $totalHours > 0
            ? round($totalHours / 8, 2)
            : round((float) str_replace(',', '.', (string) $request->input('total_day', 0)), 2);

        return [
            'total_time' => max(0, $totalHours),
            'total_day' => max(0, $totalDays),
        ];
    }

    /**
     * Keep scheduler columns in sync with repeat/reminder form fields.
     * This method is safe to call after store() and update().
     */
    protected function syncTaskSchedulerFields(PersonalTask $task): void
    {
        $nextReminderAt = null;

        if ($task->reminder_date && $task->reminder_time) {
            $nextReminderAt = $this->buildTaskDateTime($task->reminder_date, $task->reminder_time);
        }

        $nextRepeatAt = null;
        $repeat = $this->normalizeRepeatValue($task->repeat);

        if ($repeat && $task->due_date) {
            $nextRepeatAt = $this->buildTaskDateTime($task->due_date, $task->due_time ?: '09:00:00');
        }

        $task->forceFill([
            'repeat' => $repeat,
            'next_reminder_at' => $nextReminderAt,
            'next_repeat_at' => $nextRepeatAt,
        ])->save();
    }

    protected function buildTaskDateTime($date, $time = null): ?Carbon
    {
        if (!$date) {
            return null;
        }

        $dateValue = $date instanceof Carbon
            ? $date->toDateString()
            : Carbon::parse($date)->toDateString();

        $timeValue = $time ?: '09:00:00';

        return Carbon::parse($dateValue . ' ' . $timeValue);
    }

    protected function normalizeRepeatValue(?string $repeat): ?string
    {
        $repeat = strtolower(trim((string) $repeat));

        return match ($repeat) {
            'daily', 'day', 'tag', 'täglich', 'taeglich' => 'daily',
            'weekly', 'week', 'woche', 'wöchentlich', 'woechentlich' => 'weekly',
            'monthly', 'month', 'monat', 'monatlich' => 'monthly',
            'yearly', 'year', 'jahr', 'jährlich', 'jaehrlich' => 'yearly',
            default => null,
        };
    }

    protected function formatTaskLabel($taskId): string
    {
        return 'PT-' . str_pad((string) $taskId, 5, '0', STR_PAD_LEFT);
    }


    public function items(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'items' => [],
            ]);
        }

        /** @var \Illuminate\Support\Collection|\Illuminate\Notifications\DatabaseNotification[] $notifications */
        $notifications = $user->notifications()
            ->where('type', PersonalTaskNotification::class)
            ->latest()            // by created_at
            ->take(30)
            ->get();

        $items = $notifications->map(function (DatabaseNotification $n) {
            $data = $n->data ?? [];
            $taskId = $data['task_id'] ?? null;

            // data structure from PersonalTaskNotification::toDatabase()
            $title = $data['title'] ?? 'Benachrichtigung';
            $message = $data['message'] ?? '';
            $performedAt = $data['performed_at'] ?? $n->created_at?->toIso8601String();

            return [
                'id' => $n->id,
                'type' => 'notification',
                'badge' => 'Aufgabe',                  // pill label in feed
                'title' => $title,
                'message' => $message,
                'performed_at' => $performedAt,
                'url' => $taskId
                    ? route('personal-tasks.profile', $taskId)
                    : null,
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'items' => $items,
        ]);
    }

    public function destroy(PersonalTask $task)
    {
        // soft delete
        $task->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('personal-tasks.index', ['tab' => 'deleted'])
            ->with('status', 'Aufgabe wurde gelöscht.');
    }

    public function restore(Request $request, $id)
    {
        $request->validate([
            'status' => ['nullable', 'string', 'in:open,on_progress,in_progress,working,completed'],
        ]);

        $task = PersonalTask::withTrashed()->findOrFail($id);

        if (method_exists($task, 'trashed') && $task->trashed()) {
            $task->restore();
        }

        $status = $request->input('status', 'open');
        if (in_array($status, ['in_progress', 'working'], true)) {
            $status = 'on_progress';
        }

        $task->archived_at = null;
        $task->task_status = $status;

        if ($status === 'open') {
            $task->progress = min((int) ($task->progress ?? 0), 99);
        }
        if ($status === 'on_progress') {
            $task->progress = max((int) ($task->progress ?? 0), 1);
        }
        if ($status === 'completed') {
            $task->progress = 100;
            $task->archived_at = now();
        }

        $task->save();

        $employeeId = $this->currentEmployeeId();
        if ($employeeId) {
            DB::table('employees_personal_tasks')
                ->where('task_id', $task->id)
                ->where('employee_id', $employeeId)
                ->update([
                    'status' => 'accepted',
                    'reason' => null,
                    'change_reason' => 'Aufgabe wiederhergestellt',
                    'changed_by' => $employeeId,
                    'change_date' => now()->toDateString(),
                    'updated_at' => now(),
                ]);
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Aufgabe wurde wiederhergestellt.',
                'task_id' => $task->id,
                'status' => $task->task_status,
            ]);
        }

        return redirect()
            ->route('personal-tasks.index')
            ->with('status', 'Aufgabe wurde wiederhergestellt.');
    }

    // They support deleting employees from controllers and from specific Schritte.

    public function removeController(Request $request, PersonalTask $task, Employee $employee): \Illuminate\Http\JsonResponse
    {
        $controllers = collect($task->controller_id ?? [])
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0 && $id !== (int) $employee->id)
            ->unique()
            ->values()
            ->all();

        $task->controller_id = $controllers;
        $task->save();

        return response()->json([
            'success' => true,
            'controllers' => $controllers,
        ]);
    }

    public function removeKeyEmployeeFromTask(Request $request, PersonalTask $task, PersonalTaskKey $key, Employee $employee): \Illuminate\Http\JsonResponse
    {
        if ((int) $key->personal_task_id !== (int) $task->id) {
            return response()->json([
                'success' => false,
                'message' => 'Dieser Schritt gehört nicht zu dieser Aufgabe.',
            ], 422);
        }

        $current = [];

        if (is_array($key->employee_id)) {
            $current = $key->employee_id;
        } elseif (is_string($key->employee_id) && $key->employee_id !== '') {
            $current = json_decode($key->employee_id, true) ?: [];
        }

        $key->employee_id = collect($current)
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0 && $id !== (int) $employee->id)
            ->unique()
            ->values()
            ->all();

        $key->save();

        return response()->json([
            'success' => true,
            'employee_id' => $key->employee_id,
        ]);
    }

}
