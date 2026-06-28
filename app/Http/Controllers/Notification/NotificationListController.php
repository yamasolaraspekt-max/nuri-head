<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * In this project users.name stores the employee id.
     */
    private function currentEmployeeId(): int
    {
        return (int) (auth()->user()->name ?? 0);
    }

    /**
     * Laravel user id is still needed for the notifications table.
     */
    private function currentLaravelUserId(): int
    {
        return (int) (auth()->id() ?? 0);
    }

    private function truthyValues(): array
    {
        return ['on', '1', 1, true, 'yes', 'Yes', 'YES'];
    }

    /**
     * Administrator role is stored in user_rolls.user_id = employee id.
     */
    private function hasAdminLeaveAccess(): bool
    {
        $employeeId = $this->currentEmployeeId();

        if ($employeeId <= 0) {
            return false;
        }

        return DB::table('user_rolls')
            ->where('user_id', $employeeId)
            ->where('item_id', 'Administrator')
            ->exists();
    }

    private function canCreateLeave(): bool
    {
        $employeeId = $this->currentEmployeeId();

        if ($employeeId <= 0) {
            return false;
        }

        $truthy = $this->truthyValues();

        return DB::table('user_rolls')
            ->where('user_id', $employeeId)
            ->where('item_id', 'Administrator')
            ->where(function ($q) use ($truthy) {
                $q->whereIn('is_add', $truthy);
            })
            ->exists();
    }

    private function normalizeRequestFilter(?string $filter): string
    {
        $filter = strtolower(trim((string) $filter));

        return in_array($filter, ['all', 'new', 'unapproved', 'approved'], true)
            ? $filter
            : 'all';
    }

    private function applyUnapprovedCondition($query): void
    {
        $query->where(function ($q) {
            $q->whereNull('leaves.approved')
                ->orWhere('leaves.approved', '')
                ->orWhere('leaves.approved', 'Pending')
                ->orWhere('leaves.approved', 'No')
                ->orWhere('leaves.approved', '!=', 'Yes');
        });
    }

    private function applyApprovedCondition($query): void
    {
        $query->where('leaves.approved', 'Yes');
    }

    /**
     * New request = not finally approved and not answered yet.
     */
    private function applyNewRequestCondition($query): void
    {
        $this->applyUnapprovedCondition($query);

        $query->where(function ($q) {
            $q->whereNull('leaves.request_answer')
                ->orWhere('leaves.request_answer', '')
                ->orWhereNotIn('leaves.request_answer', [
                    'accept',
                    'accepted',
                    'approved',
                    'reject',
                    'rejected',
                    'decline',
                    'declined',
                ]);
        });
    }

    private function applyRequestFilter($query, string $filter): void
    {
        if ($filter === 'new') {
            $this->applyNewRequestCondition($query);
            return;
        }

        if ($filter === 'unapproved') {
            $this->applyUnapprovedCondition($query);
            return;
        }

        if ($filter === 'approved') {
            $this->applyApprovedCondition($query);
            return;
        }
    }

    private function applyLeaveSearch($query, string $search)
    {
        if ($search === '') {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('employees.name', 'LIKE', "%{$search}%")
                ->orWhere('employees.lastname', 'LIKE', "%{$search}%")
                ->orWhere('request.name', 'LIKE', "%{$search}%")
                ->orWhere('request.lastname', 'LIKE', "%{$search}%")
                ->orWhere('created.name', 'LIKE', "%{$search}%")
                ->orWhere('created.lastname', 'LIKE', "%{$search}%")
                ->orWhere('change.name', 'LIKE', "%{$search}%")
                ->orWhere('change.lastname', 'LIKE', "%{$search}%")
                ->orWhere('leaves.id', 'LIKE', "%{$search}%")
                ->orWhere('leaves.start_date', 'LIKE', "%{$search}%")
                ->orWhere('leaves.end_date', 'LIKE', "%{$search}%")
                ->orWhere('leaves.year', 'LIKE', "%{$search}%")
                ->orWhere('leaves.status', 'LIKE', "%{$search}%")
                ->orWhere('leaves.reason', 'LIKE', "%{$search}%")
                ->orWhere('leaves.description', 'LIKE', "%{$search}%")
                ->orWhere('leaves.approved', 'LIKE', "%{$search}%")
                ->orWhere('leaves.request_answer', 'LIKE', "%{$search}%");
        });
    }

    private function baseLeaveSelectQuery()
    {
        return DB::table('leaves')
            ->leftJoin('employees', 'employees.id', '=', 'leaves.emp_id')
            ->leftJoin('employees as request', 'request.id', '=', 'leaves.request_to')
            ->leftJoin('employees as created', 'created.id', '=', 'leaves.created_by')
            ->leftJoin('employees as change', 'change.id', '=', 'leaves.changed_by')
            ->select(
                'leaves.id as leave_id',
                'leaves.start_date',
                'leaves.end_date',
                'leaves.duration',
                'leaves.status',
                'leaves.emp_id',
                'leaves.request_to',
                'leaves.created_by',
                'leaves.changed_by',
                'leaves.request_answer',
                'leaves.approved',
                'leaves.year',
                'leaves.request_back',
                'leaves.old_start',
                'leaves.old_end',
                'leaves.reason',
                'leaves.description',
                'leaves.remaining_day',
                'leaves.leave_day',
                'leaves.created_at',
                'leaves.updated_at',

                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image',

                'request.name as rname',
                'request.lastname as rlastname',
                'request.image as rimage',

                'change.name as chname',
                'change.lastname as chlastname',
                'change.image as chimage',

                'created.name as cname',
                'created.lastname as clastname',
                'created.image as cimage',

                DB::raw("
                    CASE
                        WHEN (
                            leaves.approved IS NULL
                            OR leaves.approved = ''
                            OR leaves.approved IN ('Pending', 'No')
                            OR leaves.approved != 'Yes'
                        )
                        THEN 1
                        ELSE 0
                    END as is_unapproved
                "),

                DB::raw("
                    CASE
                        WHEN (
                            (
                                leaves.approved IS NULL
                                OR leaves.approved = ''
                                OR leaves.approved IN ('Pending', 'No')
                                OR leaves.approved != 'Yes'
                            )
                            AND (
                                leaves.request_answer IS NULL
                                OR leaves.request_answer = ''
                                OR leaves.request_answer NOT IN (
                                    'accept',
                                    'accepted',
                                    'approved',
                                    'reject',
                                    'rejected',
                                    'decline',
                                    'declined'
                                )
                            )
                        )
                        THEN 1
                        ELSE 0
                    END as is_new_request
                ")
            );
    }

    private function visibleIncomingRequestQuery(int $employeeId, bool $hasAdminAccess)
    {
        $query = DB::table('leaves');

        if (!$hasAdminAccess) {
            $query->where('leaves.request_to', $employeeId);
        }

        return $query;
    }

    private function buildStats(int $employeeId, bool $hasAdminAccess): array
    {
        $today = Carbon::today();

        $myLeavesBase = DB::table('leaves')
            ->where('leaves.emp_id', $employeeId);

        $myOpenQuery = clone $myLeavesBase;
        $this->applyUnapprovedCondition($myOpenQuery);

        $incomingBase = $this->visibleIncomingRequestQuery($employeeId, $hasAdminAccess);

        $newIncomingQuery = clone $incomingBase;
        $this->applyNewRequestCondition($newIncomingQuery);

        $unapprovedIncomingQuery = clone $incomingBase;
        $this->applyUnapprovedCondition($unapprovedIncomingQuery);

        $approvedIncomingQuery = clone $incomingBase;
        $this->applyApprovedCondition($approvedIncomingQuery);

        return [
            'total' => (clone $myLeavesBase)->count(),

            'open' => $myOpenQuery->count(),

            'approved' => (clone $myLeavesBase)
                ->where('leaves.approved', 'Yes')
                ->count(),

            'today_on_leave' => (clone $myLeavesBase)
                ->whereDate('leaves.start_date', '<=', $today)
                ->whereDate('leaves.end_date', '>=', $today)
                ->where('leaves.approved', 'Yes')
                ->count(),

            /**
             * Backward-compatible old key.
             * For admin this means all unapproved requests.
             * For normal users this means unapproved requests directed to them.
             */
            'pending_to_me' => $unapprovedIncomingQuery->count(),

            'new_requests' => $newIncomingQuery->count(),
            'unapproved_requests' => (clone $incomingBase)->tap(function ($query) {
                $this->applyUnapprovedCondition($query);
            })->count(),

            'approved_requests' => $approvedIncomingQuery->count(),
            'all_requests' => (clone $incomingBase)->count(),
        ];
    }

    public function index(Request $request)
    {
        $employeeId = $this->currentEmployeeId();

        $hasAdminAccess = $this->hasAdminLeaveAccess();
        $canCreateLeave = $this->canCreateLeave();

        $search = trim((string) $request->get('search', ''));
        $sort = strtolower((string) $request->get('sort', 'desc')) === 'asc' ? 'asc' : 'desc';
        $filter = $this->normalizeRequestFilter($request->get('request_filter', 'all'));

        $stats = $this->buildStats($employeeId, $hasAdminAccess);

        /**
         * Meine Anträge
         */
        $leaveQuery = $this->baseLeaveSelectQuery()
            ->where('leaves.emp_id', $employeeId);

        $this->applyLeaveSearch($leaveQuery, $search);
        $this->applyRequestFilter($leaveQuery, $filter);

        $leave = $leaveQuery
            ->orderBy('leaves.id', $sort)
            ->paginate(20, ['*'], 'leave_page')
            ->appends($request->only(['search', 'sort', 'request_filter', 'active_tab']));

        /**
         * Anfragen / Admin: alle Anfragen
         */
        $responseQuery = $this->baseLeaveSelectQuery();

        if (!$hasAdminAccess) {
            $responseQuery->where('leaves.request_to', $employeeId);
        }

        $this->applyLeaveSearch($responseQuery, $search);
        $this->applyRequestFilter($responseQuery, $filter);

        $response = $responseQuery
            ->orderBy('leaves.id', $sort)
            ->paginate(20, ['*'], 'response_page')
            ->appends($request->only(['search', 'sort', 'request_filter', 'active_tab']));

        return view('admin.notifications.notifications', compact(
            'stats',
            'hasAdminAccess',
            'canCreateLeave',
            'leave',
            'response',
            'search',
            'sort',
            'filter'
        ));
    }
    /**
     * AJAX partial for "Meine Anträge".
     */
    public function view(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $sort = strtolower((string) $request->get('sort', 'desc')) === 'asc' ? 'asc' : 'desc';
        $filter = $this->normalizeRequestFilter($request->get('request_filter', 'all'));

        $employeeId = $this->currentEmployeeId();

        $query = $this->baseLeaveSelectQuery()
            ->where('leaves.emp_id', $employeeId);

        $this->applyLeaveSearch($query, $search);
        $this->applyRequestFilter($query, $filter);

        $leave = $query
            ->orderBy('leaves.id', $sort)
            ->paginate(20)
            ->appends($request->only(['search', 'sort', 'request_filter']));

        return view('admin.notifications.partials.notification', compact('leave', 'filter'));
    }

    /**
     * AJAX partial for "Anfragen an mich" / admin all incoming requests.
     */
    public function response(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $sort = strtolower((string) $request->get('sort', 'desc')) === 'asc' ? 'asc' : 'desc';
        $filter = $this->normalizeRequestFilter($request->get('request_filter', 'all'));

        $employeeId = $this->currentEmployeeId();
        $hasAdminAccess = $this->hasAdminLeaveAccess();

        $query = $this->baseLeaveSelectQuery();

        if (!$hasAdminAccess) {
            $query->where('leaves.request_to', $employeeId);
        }

        $this->applyLeaveSearch($query, $search);
        $this->applyRequestFilter($query, $filter);

        $response = $query
            ->orderBy('leaves.id', $sort)
            ->paginate(20)
            ->appends($request->only(['search', 'sort', 'request_filter']));

        return view('admin.notifications.partials.response', compact(
            'response',
            'filter',
            'hasAdminAccess'
        ));
    }

    /**
     * Optional JSON endpoint for refreshing counters after AJAX create/update/delete.
     */
    public function stats()
    {
        $employeeId = $this->currentEmployeeId();
        $hasAdminAccess = $this->hasAdminLeaveAccess();

        return response()->json([
            'success' => true,
            'stats' => $this->buildStats($employeeId, $hasAdminAccess),
        ]);
    }

    /**
     * Safe JSON decode for notifications.data.
     */
    private function decodeNotificationData($raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }

        if (is_object($raw)) {
            return json_decode(json_encode($raw), true) ?: [];
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Notification dropdown/list endpoint.
     */
    public function list(Request $request)
    {
        $typeFilter = $request->query('type');
        $onlyUnread = $request->boolean('unread');

        $notifications = DB::table('notifications')
            ->where('notifiable_id', $this->currentLaravelUserId())
            ->when($onlyUnread, fn($q) => $q->whereNull('read_at'))
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        $groups = [
            'lead' => [],
            'inquiry' => [],
            'responsible_change' => [],
            'appointment' => [],
            'ticket' => [],
            'employee' => [],
            'task' => [],
            'project_task' => [],
            'offer' => [],
            'leave' => [],
            'other' => [],
        ];

        foreach ($notifications as $notification) {
            $data = $this->decodeNotificationData($notification->data);
            $type = $data['type'] ?? 'other';
            $type = array_key_exists($type, $groups) ? $type : 'other';

            if ($typeFilter && $typeFilter !== $type) {
                continue;
            }

            $groups[$type][] = [
                'id' => $notification->id,
                'type' => $type,
                'title' => $data['title'] ?? '',
                'message' => $data['message'] ?? '',
                'performed_at' => $data['performed_at'] ?? $notification->created_at,
                'read_at' => $notification->read_at,
                'data' => $data,
            ];
        }

        return response()->json($groups);
    }
}