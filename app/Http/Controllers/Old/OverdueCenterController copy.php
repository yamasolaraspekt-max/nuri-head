<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InquiryReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class OverdueCenterController extends Controller
{
    private const HOURS = 48;

    /**
     * Supported item types.
     * NOTE: "lead" items are rows in lead_product_lists.
     */
    private const TYPES = ['inquiry', 'task', 'appointment', 'ticket', 'lead'];

    private const SKIP_TEXT = 'Bericht übersprungen';

    // Soft caps (merge happens in-memory)
    private const LIMIT_INQUIRY     = 800;
    private const LIMIT_TASK        = 800;
    private const LIMIT_APPOINTMENT = 800;
    private const LIMIT_TICKET      = 800;
    private const LIMIT_LEAD        = 900;

    private const MAX_PER_PAGE     = 50;
    private const MIN_PER_PAGE     = 6;
    private const DEFAULT_PER_PAGE = 12;

    /**
     * Local cache for Schema checks.
     */
    private static array $schemaCache = [
        'hasTable'  => [],
        'hasColumn' => [],
    ];

    /* =========================================================================
       PAGES
       ========================================================================= */

    public function index()
    {
        $employeeId = $this->currentEmployeeId();

        $employee = null;
        if ($employeeId > 0 && $this->hasTable('employees')) {
            $employee = DB::table('employees')
                ->where('id', $employeeId)
                ->first(['id', 'name', 'lastname']);
        }

        return view('admin.dashboard.employee.partials.overdue48h', compact('employeeId', 'employee'));
    }

    /**
     * Keep as-is: shows all employees (if admin) and all reports center.
        */
    public function recentReportsIndex()
    {
        // ✅ Always show all employees in the dropdown (no restriction)
        $employees = collect();

        if ($this->hasTable('employees')) {
            $employees = DB::table('employees')
                ->select('id', 'name', 'lastname')
                ->orderBy('name')
                ->orderBy('lastname')
                ->get();
        }

        return view('admin.dashboard.employee.partials.recent_reports_center', [
            'employees'  => $employees,
            // ✅ default to "Alle" in UI
            'employeeId' => 0,
            // ✅ UI can show "Alle" option
            'canViewAll' => true,
        ]);
    }


  
    /* =========================================================================
       OVERDUE FETCH (48H) — ONLY ITEMS ASSIGNED TO ME (TO REPORT), NOT CREATED BY ME
       - Uses assignment tables:
         * inquiries: inquiry_product_lists (employee_id/field_employee)
         * tasks: employees_personal_tasks (employee_id)
         * appointments: main_appointment_employees (employee_id)
         * tickets: employee_problem (employee_id)
         * leads: lead_product_lists (employee_id/field_employee) + JSON teams[*].employee_id
       - Excludes items already reported by ME (report_by/employee_id = current employee)
       ========================================================================= */

    public function fetch(Request $request)
    {
        $threshold  = now()->subHours(self::HOURS);
        $employeeId = $this->currentEmployeeId();

        if ($employeeId <= 0) {
            return response()->json([
                'items' => [],
                'stats' => ['total' => 0, 'inquiry' => 0, 'task' => 0, 'appointment' => 0, 'ticket' => 0, 'lead' => 0],
                'pagination' => ['page' => 1, 'per_page' => self::DEFAULT_PER_PAGE, 'total' => 0, 'has_more' => false],
            ]);
        }

        $q      = trim((string) $request->get('q', ''));
        $types  = $this->normalizeTypes($request->input('types', self::TYPES));
        $status = $request->get('status');
        $prio   = $request->get('priority');
        $branch = $request->get('branch_id');

        $sort    = (string) $request->get('sort', 'oldest');
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = min(self::MAX_PER_PAGE, max(self::MIN_PER_PAGE, (int) $request->get('per_page', self::DEFAULT_PER_PAGE)));

        $items = collect();

        if (in_array('inquiry', $types, true)) {
            $items = $items->merge($this->overdueInquiries($threshold, $q, $status, $prio, $branch, $employeeId));
        }
        if (in_array('task', $types, true)) {
            $items = $items->merge($this->overdueTasks($threshold, $q, $status, $prio, $branch, $employeeId));
        }
        if (in_array('appointment', $types, true)) {
            $items = $items->merge($this->overdueAppointments($threshold, $q, $status, $prio, $branch, $employeeId));
        }
        if (in_array('ticket', $types, true)) {
            $items = $items->merge($this->overdueTickets($threshold, $q, $status, $prio, $branch, $employeeId));
        }
        if (in_array('lead', $types, true)) {
            $items = $items->merge($this->overdueLeadProducts($threshold, $q, $status, $prio, $branch, $employeeId));
        }

        $items = $items->filter()->values();

        $items = match ($sort) {
            'newest'       => $items->sortByDesc('last_activity_at')->values(),
            'most_overdue' => $items->sortByDesc('overdue_hours')->values(),
            default        => $items->sortBy('last_activity_at')->values(),
        };

        $total = $items->count();
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        $stats = [
            'total'       => $total,
            'inquiry'     => $items->where('type', 'inquiry')->count(),
            'task'        => $items->where('type', 'task')->count(),
            'appointment' => $items->where('type', 'appointment')->count(),
            'ticket'      => $items->where('type', 'ticket')->count(),
            'lead'        => $items->where('type', 'lead')->count(),
        ];

        return response()->json([
            'items' => $slice,
            'stats' => $stats,
            'pagination' => [
                'page'     => $page,
                'per_page' => $perPage,
                'total'    => $total,
                'has_more' => ($page * $perPage) < $total,
            ],
        ]);
    }

    /* =========================================================================
       HISTORY + REPORTS LIST
       ========================================================================= */

    public function history(Request $request)
    {
        $type = $request->string('type')->toString();
        $id   = (int) $request->get('id');

        if (!in_array($type, self::TYPES, true)) {
            return response()->json(['message' => 'Unknown type'], 422);
        }

        $this->abortIfNotInvolved($type, $id);

        return match ($type) {
            'lead'        => $this->historyLeadProduct($id),
            'task'        => $this->historyTask($id),
            'appointment' => $this->historyAppointment($id),
            'inquiry'     => $this->historyInquiry($id),
            'ticket'      => $this->historyTicket($id),
            default       => response()->json(['message' => 'Unknown type'], 422),
        };
    }

    public function reportsList(Request $request)
    {
        $type = $request->string('type')->toString();
        $id   = (int) $request->get('id');

        if (!in_array($type, self::TYPES, true)) {
            return response()->json(['message' => 'Unknown type'], 422);
        }

        $this->abortIfNotInvolved($type, $id);

        // ONLY MY reports
        return match ($type) {
            'appointment' => $this->reportsAppointment($id),
            'task'        => $this->reportsTask($id),
            'ticket'      => $this->reportsTicket($id),
            'inquiry'     => $this->reportsInquiry($id),
            'lead'        => $this->reportsLeadProduct($id),
            default       => response()->json(['rows' => []]),
        };
    }

    /* =========================================================================
       REPORT STORE / SKIP
       ========================================================================= */

    public function reportStore(Request $request)
    {
        $request->validate([
            'type'        => ['required', 'string', Rule::in(self::TYPES)],
            'id'          => ['required', 'integer', 'min:1'],
            'report'      => ['required', 'string', 'min:3'],
            'due_date'    => ['nullable', 'date'],
            'report_date' => ['nullable', 'date'],
        ]);

        $type = $request->string('type')->toString();
        $id   = (int) $request->get('id');

        $employeeId = $this->currentEmployeeId();
        if ($employeeId <= 0) abort(403);

        $this->abortIfNotInvolved($type, $id);

        return match ($type) {
            'appointment' => $this->storeReportAppointment($id, $employeeId, $request, false),
            'task'        => $this->storeReportTask($id, $employeeId, $request, false),
            'ticket'      => $this->storeReportTicket($id, $employeeId, $request, false),
            'inquiry'     => $this->storeReportInquiry($id, $employeeId, $request, false),
            'lead'        => $this->storeReportLeadProduct($id, $employeeId, $request, false),
            default       => response()->json(['message' => 'Unknown type'], 422),
        };
    }

    public function reportSkip(Request $request)
    {
        $request->validate([
            'type'        => ['required', 'string', Rule::in(self::TYPES)],
            'id'          => ['required', 'integer', 'min:1'],
            'skip_reason' => ['required', 'string', 'min:3', 'max:1000'],
            'due_date'    => ['nullable', 'date'],
            'report_date' => ['nullable', 'date'],
        ]);

        $type = $request->string('type')->toString();
        $id   = (int) $request->get('id');

        $employeeId = $this->currentEmployeeId();
        if ($employeeId <= 0) abort(403);

        $this->abortIfNotInvolved($type, $id);

        $request->merge(['report' => self::SKIP_TEXT]);

        return match ($type) {
            'appointment' => $this->storeReportAppointment($id, $employeeId, $request, true),
            'task'        => $this->storeReportTask($id, $employeeId, $request, true),
            'ticket'      => $this->storeReportTicket($id, $employeeId, $request, true),
            'inquiry'     => $this->storeReportInquiry($id, $employeeId, $request, true),
            'lead'        => $this->storeReportLeadProduct($id, $employeeId, $request, true),
            default       => response()->json(['message' => 'Unknown type'], 422),
        };
    }

    /* =========================================================================
       OVERDUE QUERIES (ASSIGNED TO ME + NOT YET REPORTED BY ME)
       ========================================================================= */

    private function overdueInquiries(Carbon $threshold, string $q, $status, $prio, $branch, int $emp)
    {
        if (!$this->hasTable('inquiries') || !$this->hasTable('inquiry_product_lists')) return collect();

        // last report by ME per inquiry
        $irByCol = $this->hasColumn('inquiry_reports', 'report_by')
            ? 'report_by'
            : ($this->hasColumn('inquiry_reports', 'employee_id') ? 'employee_id' : null);

        $irSub = null;
        if ($this->hasTable('inquiry_reports') && $irByCol) {
            $irSub = DB::table('inquiry_reports as ir')
                ->when($this->hasColumn('inquiry_reports', 'deleted_at'), fn($x) => $x->whereNull('ir.deleted_at'))
                ->where("ir.$irByCol", $emp)
                ->groupBy('ir.inquiry_id')
                ->select([
                    'ir.inquiry_id',
                    DB::raw('MAX(ir.created_at) as my_last_report_at'),
                ]);
        }

        // last assignment/product change for MY product rows
        $iplSub = DB::table('inquiry_product_lists as ipl')
            ->when($this->hasColumn('inquiry_product_lists', 'deleted_at'), fn($x) => $x->whereNull('ipl.deleted_at'))
            ->where(function ($w) use ($emp) {
                $any = false;
                if ($this->hasColumn('inquiry_product_lists', 'employee_id')) {
                    $w->orWhere('ipl.employee_id', $emp);
                    $any = true;
                }
                if ($this->hasColumn('inquiry_product_lists', 'field_employee')) {
                    $w->orWhere('ipl.field_employee', $emp);
                    $any = true;
                }
                if (!$any) $w->whereRaw('1=0');
            })
            ->groupBy('ipl.inquiry_id')
            ->select([
                'ipl.inquiry_id',
                DB::raw('MAX(ipl.updated_at) as my_last_product_at'),
            ]);

        $base = DB::table('inquiries as i')
            ->when($this->hasColumn('inquiries', 'deleted_at'), fn($qq) => $qq->whereNull('i.deleted_at'))
            ->joinSub($iplSub, 'iplx', fn($j) => $j->on('iplx.inquiry_id', '=', 'i.id'))
            ->select([
                DB::raw("'inquiry' as type"),
                'i.id',
                DB::raw("COALESCE(NULLIF(i.title,''), NULLIF(i.firma,''), TRIM(CONCAT(COALESCE(i.name,''),' ',COALESCE(i.lastname,''))), 'Anfrage') as title"),
                DB::raw("COALESCE(i.next_step,'') as subtitle"),
                DB::raw("COALESCE(i.status,'open') as status"),
                DB::raw("COALESCE(i.periority,'normal') as priority"),
                'i.updated_at',
                'i.created_at',
                $this->hasColumn('inquiries', 'branch_id') ? 'i.branch_id' : DB::raw("NULL as branch_id"),
                DB::raw("iplx.my_last_product_at as my_last_product_at"),
                $irSub ? DB::raw("irx.my_last_report_at as my_last_report_at") : DB::raw("NULL as my_last_report_at"),
            ]);

        if ($irSub) {
            $base->leftJoinSub($irSub, 'irx', fn($j) => $j->on('irx.inquiry_id', '=', 'i.id'));
            // exclude already reported by ME
            $base->whereNull('irx.my_last_report_at');
        }

        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                foreach (['title', 'firma', 'name', 'lastname', 'next_step', 'note'] as $col) {
                    if ($this->hasColumn('inquiries', $col)) $w->orWhere("i.$col", 'like', "%{$q}%");
                }
            });
        }

        if ($status && $this->hasColumn('inquiries', 'status'))      $base->where('i.status', $status);
        if ($prio   && $this->hasColumn('inquiries', 'periority'))   $base->where('i.periority', $prio);
        if ($branch && $this->hasColumn('inquiries', 'branch_id'))   $base->where('i.branch_id', $branch);

        $rows = $base->limit(self::LIMIT_INQUIRY)->get();

        // preload wanted products (max 3) for this inquiry (all products)
        $wantedByInquiryId = [];
        if ($this->hasTable('article_groups') && $this->hasColumn('inquiry_product_lists', 'product_id')) {
            $inqIds = $rows->pluck('id')->map(fn($x) => (int)$x)->filter()->values()->all();
            if (!empty($inqIds)) {
                $prodRows = DB::table('inquiry_product_lists as ipl')
                    ->leftJoin('article_groups as ag', 'ag.id', '=', 'ipl.product_id')
                    ->whereIn('ipl.inquiry_id', $inqIds)
                    ->when($this->hasColumn('inquiry_product_lists', 'deleted_at'), fn($x) => $x->whereNull('ipl.deleted_at'))
                    ->get([
                        'ipl.inquiry_id',
                        $this->hasColumn('article_groups', 'article_group') ? 'ag.article_group' : DB::raw("NULL as article_group"),
                    ]);

                foreach ($prodRows as $pr) {
                    $iid   = (int)($pr->inquiry_id ?? 0);
                    $pname = trim((string)($pr->article_group ?? ''));
                    if ($iid <= 0 || $pname === '') continue;
                    $wantedByInquiryId[$iid] ??= [];
                    if (count($wantedByInquiryId[$iid]) < 3 && !in_array($pname, $wantedByInquiryId[$iid], true)) {
                        $wantedByInquiryId[$iid][] = $pname;
                    }
                }
            }
        }

        return $rows->map(function ($r) use ($threshold, $wantedByInquiryId) {
            $last = collect([$r->updated_at, $r->my_last_product_at])->filter()->max() ?? $r->updated_at;
            $lastAt = Carbon::parse($last);

            if ($lastAt->gt($threshold)) return null;

            $dueAt        = $lastAt->copy()->addHours(self::HOURS);
            $overdueHours = max(0, $dueAt->diffInHours(now(), false));

            $products = $wantedByInquiryId[(int)$r->id] ?? [];
            $prodLine = !empty($products) ? implode(', ', $products) : '';

            $subtitleParts = [];
            if ($prodLine !== '') $subtitleParts[] = $prodLine;

            $sub = $this->cleanText($r->subtitle ?? '');
            if ($sub !== '') $subtitleParts[] = Str::limit($sub, 120);

            return [
                'type'             => 'inquiry',
                'id'               => (int) $r->id,
                'title'            => trim((string) $r->title) ?: 'Anfrage',
                'subtitle'         => implode(' • ', $subtitleParts),
                'status'           => (string) ($r->status ?? 'open'),
                'priority'         => (string) ($r->priority ?? 'normal'),
                'due_at'           => $dueAt->toDateTimeString(),
                'last_activity_at' => $lastAt->toDateTimeString(),
                'overdue_hours'    => (int) $overdueHours,
                'progress_pct'     => 25,
                'changed_summary'  => 'Keine Änderung / kein Bericht seit 48h',
                'link'             => url("/inquiry_show/{$r->id}"),
                'meta'             => [],
            ];
        })->filter()->values();
    }

    private function overdueTasks(Carbon $threshold, string $q, $status, $prio, $branch, int $emp)
    {
        if (!$this->hasTable('personal_tasks') || !$this->hasTable('employees_personal_tasks')) return collect();

        // report table used for tasks is appointment_reports
        $byCol = $this->hasColumn('appointment_reports', 'report_by')
            ? 'report_by'
            : ($this->hasColumn('appointment_reports', 'employee_id') ? 'employee_id' : null);

        $taskIdCol = $this->hasColumn('appointment_reports', 'task_id')
            ? 'task_id'
            : ($this->hasColumn('appointment_reports', 'personal_task_id') ? 'personal_task_id' : null);

        $myReportSub = null;
        if ($this->hasTable('appointment_reports') && $byCol && $taskIdCol) {
            $myReportSub = DB::table('appointment_reports as ar')
                ->when($this->hasColumn('appointment_reports', 'deleted_at'), fn($x) => $x->whereNull('ar.deleted_at'))
                ->where("ar.$byCol", $emp)
                ->groupBy("ar.$taskIdCol")
                ->select([
                    DB::raw("ar.$taskIdCol as task_id"),
                    DB::raw('MAX(ar.created_at) as my_last_report_at'),
                ]);
        }

        $eptSub = DB::table('employees_personal_tasks as ept')
            ->where('ept.employee_id', $emp)
            ->when($this->hasColumn('employees_personal_tasks', 'deleted_at'), fn($x) => $x->whereNull('ept.deleted_at'))
            ->groupBy('ept.task_id')
            ->select([
                'ept.task_id',
                DB::raw('MAX(ept.created_at) as my_last_change_at'),
            ]);

        $titleCandidates = [];
        foreach (['task_title', 'title', 'task', 'name', 'subject'] as $col) {
            if ($this->hasColumn('personal_tasks', $col)) $titleCandidates[] = "NULLIF(t.$col,'')";
        }
        $titleExpr = $titleCandidates
            ? "COALESCE(" . implode(',', $titleCandidates) . ", 'Aufgabe')"
            : "'Aufgabe'";

        $subCandidates = [];
        foreach (['description', 'note', 'details'] as $col) {
            if ($this->hasColumn('personal_tasks', $col)) $subCandidates[] = "NULLIF(t.$col,'')";
        }
        $subExpr = $subCandidates ? "COALESCE(" . implode(',', $subCandidates) . ", '')" : "''";

        $statusCol = $this->hasColumn('personal_tasks', 'task_status') ? 't.task_status' : ($this->hasColumn('personal_tasks', 'status') ? 't.status' : null);
        $priorityCol = $this->hasColumn('personal_tasks', 'priority') ? 't.priority' : ($this->hasColumn('personal_tasks', 'periority') ? 't.periority' : null);
        $updatedCol = $this->hasColumn('personal_tasks', 'updated_at') ? 't.updated_at' : 't.created_at';

        $base = DB::table('personal_tasks as t')
            ->when($this->hasColumn('personal_tasks', 'deleted_at'), fn($qq) => $qq->whereNull('t.deleted_at'))
            ->joinSub($eptSub, 'eptx', fn($j) => $j->on('eptx.task_id', '=', 't.id'))
            ->select([
                DB::raw("'task' as type"),
                't.id',
                DB::raw("$titleExpr as title"),
                DB::raw("$subExpr as subtitle"),
                DB::raw(($statusCol ? "COALESCE($statusCol,'open')" : "'open'") . " as status"),
                DB::raw(($priorityCol ? "COALESCE($priorityCol,'normal')" : "'normal'") . " as priority"),
                DB::raw("$updatedCol as updated_at"),
                't.created_at',
                // ✅ Fetch Start Date, Due Date, and Due Time
                't.start_date',
                't.due_date',
                't.due_time', 
                $this->hasColumn('personal_tasks', 'branch_id') ? 't.branch_id' : DB::raw("NULL as branch_id"),
                DB::raw("eptx.my_last_change_at as my_last_change_at"),
                $myReportSub ? DB::raw("arx.my_last_report_at as my_last_report_at") : DB::raw("NULL as my_last_report_at"),
            ]);

        if ($myReportSub) {
            $base->leftJoinSub($myReportSub, 'arx', fn($j) => $j->on('arx.task_id', '=', 't.id'));
            $base->whereNull('arx.my_last_report_at');
        }

        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                foreach (['task_title', 'title', 'task', 'name', 'description', 'note', 'details'] as $col) {
                    if ($this->hasColumn('personal_tasks', $col)) $w->orWhere("t.$col", 'like', "%{$q}%");
                }
            });
        }

        if ($status && $statusCol) $base->where($statusCol, $status);
        if ($prio && $priorityCol) $base->where($priorityCol, $prio);
        if ($branch && $this->hasColumn('personal_tasks', 'branch_id')) $base->where('t.branch_id', $branch);

        $rows = $base->limit(self::LIMIT_TASK)->get();

        return $rows->map(function ($r) use ($threshold) {
            
            // 1. Calculate the exact "Due Point"
            // If due_date exists, combine with due_time (or end of day).
            // If no due_date, use start_date.
            $point = null;

            if ($r->due_date) {
                $time = $r->due_time ?? '23:59:59';
                $point = Carbon::parse($r->due_date . ' ' . $time);
            } elseif ($r->start_date) {
                $point = Carbon::parse($r->start_date)->startOfDay();
            } else {
                // Fallback to update time if no dates set
                $point = Carbon::parse($r->updated_at);
            }

            // 2. Logic Check: Has it been 48 hours since that point?
            // "gt" means "greater than". 
            // If $point is later than $threshold (48h ago), it means < 48h have passed.
            if ($point->gt($threshold)) {
                return null; // HIDE IT (Not 48h yet, or is in future)
            }

            // If we are here, it IS overdue.
            // Calculate display data
            $dueAt        = $point->copy()->addHours(self::HOURS);
            $overdueHours = max(0, $dueAt->diffInHours(now(), false));

            $lastActivity = collect([$r->updated_at, $r->my_last_change_at])->filter()->max() ?? $r->updated_at;

            return [
                'type'             => 'task',
                'id'               => (int) $r->id,
                'title'            => trim((string) $r->title) ?: 'Aufgabe',
                'subtitle'         => Str::limit($this->cleanText($r->subtitle ?? ''), 140),
                'status'           => (string) ($r->status ?? 'open'),
                'priority'         => (string) ($r->priority ?? 'normal'),
                'due_at'           => $dueAt->toDateTimeString(),
                'last_activity_at' => Carbon::parse($lastActivity)->toDateTimeString(),
                'overdue_hours'    => (int) $overdueHours,
                'progress_pct'     => 30,
                'changed_summary'  => 'Keine Änderung / kein Bericht seit 48h',
                'link'             => url("/personal-tasks/{$r->id}/profile"),
                'meta'             => [],
            ];
        })->filter()->values();
    }

      private function overdueAppointments(Carbon $threshold, string $q, $status, $prio, $branch, int $emp)
        {
            if (!$this->hasTable('main_appointments') || !$this->hasTable('main_appointment_employees')) return collect();

            $byCol = $this->hasColumn('appointment_reports', 'report_by')
                ? 'report_by'
                : ($this->hasColumn('appointment_reports', 'employee_id') ? 'employee_id' : null);

            $appointmentIdCol = $this->hasColumn('appointment_reports', 'appointment_id')
                ? 'appointment_id'
                : ($this->hasColumn('appointment_reports', 'main_appointment_id') ? 'main_appointment_id' : null);

            $myReportSub = null;
            if ($this->hasTable('appointment_reports') && $byCol && $appointmentIdCol) {
                $myReportSub = DB::table('appointment_reports as ar')
                    ->when($this->hasColumn('appointment_reports', 'deleted_at'), fn($x) => $x->whereNull('ar.deleted_at'))
                    ->where("ar.$byCol", $emp)
                    ->groupBy("ar.$appointmentIdCol")
                    ->select([
                        DB::raw("ar.$appointmentIdCol as appointment_id"),
                        DB::raw('MAX(ar.created_at) as my_last_report_at'),
                    ]);
            }

            $maeSub = DB::table('main_appointment_employees as mae')
                ->where('mae.employee_id', $emp)
                ->groupBy('mae.appointment_id')
                ->select([
                    'mae.appointment_id',
                    DB::raw('MAX(mae.updated_at) as my_last_assign_at'),
                ]);

            $titleCandidates = [];
            foreach (['name', 'title', 'subject'] as $col) {
                if ($this->hasColumn('main_appointments', $col)) $titleCandidates[] = "NULLIF(a.$col,'')";
            }
            $titleExpr = $titleCandidates
                ? "COALESCE(" . implode(',', $titleCandidates) . ", 'Termin')"
                : "'Termin'";

            $subCandidates = [];
            foreach (['full_address', 'street', 'postcode', 'city', 'phone', 'email', 'appointment_type', 'execution_type', 'contact_mode'] as $col) {
                if ($this->hasColumn('main_appointments', $col)) $subCandidates[] = "NULLIF(a.$col,'')";
            }
            $subtitleExpr = $subCandidates ? "TRIM(CONCAT_WS(' • ', " . implode(',', $subCandidates) . "))" : "''";

            $statusCol   = $this->hasColumn('main_appointments', 'status') ? 'a.status' : null;
            $priorityCol = $this->hasColumn('main_appointments', 'priority') ? 'a.priority' : ($this->hasColumn('main_appointments', 'periority') ? 'a.periority' : null);
            $updatedCol  = $this->hasColumn('main_appointments', 'updated_at') ? 'a.updated_at' : 'a.created_at';

            $base = DB::table('main_appointments as a')
                ->when($this->hasColumn('main_appointments', 'deleted_at'), fn($qq) => $qq->whereNull('a.deleted_at'))
                ->joinSub($maeSub, 'maex', fn($j) => $j->on('maex.appointment_id', '=', 'a.id'))
                ->select([
                    DB::raw("'appointment' as type"),
                    'a.id',
                    DB::raw("$titleExpr as title"),
                    DB::raw("$subtitleExpr as subtitle"),
                    DB::raw(($statusCol ? "COALESCE($statusCol,'open')" : "'open'") . " as status"),
                    DB::raw(($priorityCol ? "COALESCE($priorityCol,'normal')" : "'normal'") . " as priority"),
                    DB::raw("$updatedCol as updated_at"),
                    'a.created_at',
                    // ✅ Fetch Dates and Times
                    'a.start_date', 'a.end_date', 'a.start_time', 'a.end_time',
                    $this->hasColumn('main_appointments', 'branch_id') ? 'a.branch_id' : DB::raw("NULL as branch_id"),
                    DB::raw("maex.my_last_assign_at as my_last_assign_at"),
                    $myReportSub ? DB::raw("arx.my_last_report_at as my_last_report_at") : DB::raw("NULL as my_last_report_at"),
                ]);

            if ($myReportSub) {
                $base->leftJoinSub($myReportSub, 'arx', fn($j) => $j->on('arx.appointment_id', '=', 'a.id'));
                $base->whereNull('arx.my_last_report_at');
            }

            if ($q !== '') {
                $base->where(function ($w) use ($q) {
                    foreach ([
                        'name','title','subject','note','full_address','street','postcode','city',
                        'phone','email','appointment_type','execution_type','contact_mode'
                    ] as $col) {
                        if ($this->hasColumn('main_appointments', $col)) $w->orWhere("a.$col", 'like', "%{$q}%");
                    }
                });
            }

            if ($status && $statusCol) $base->where($statusCol, $status);
            if ($prio && $priorityCol) $base->where($priorityCol, $prio);
            if ($branch && $this->hasColumn('main_appointments', 'branch_id')) $base->where('a.branch_id', $branch);

            $rows = $base->limit(self::LIMIT_APPOINTMENT)->get();

            return $rows->map(function ($r) use ($threshold) {
                
                // 1. Calculate "End Point" (When the appointment finishes)
                $endPoint = null;

                if ($r->end_date) {
                    $time = $r->end_time ?? '23:59:59';
                    $endPoint = Carbon::parse($r->end_date . ' ' . $time);
                } elseif ($r->start_date) {
                    // If no end date, we count from the Start
                    $time = $r->start_time ?? '00:00:00';
                    $endPoint = Carbon::parse($r->start_date . ' ' . $time);
                } else {
                    $endPoint = Carbon::parse($r->updated_at);
                }

                // 2. Logic Check: Has it been 48h since the appointment ended?
                // "gt" means "greater than". 
                // If $endPoint is later than $threshold (48h ago), it means < 48h have passed.
                if ($endPoint->gt($threshold)) {
                    return null; // HIDE IT (Not 48h yet, or is in future)
                }

                // If here, it is overdue.
                $dueAt        = $endPoint->copy()->addHours(self::HOURS);
                $overdueHours = max(0, $dueAt->diffInHours(now(), false));
                
                $lastActivity = collect([$r->updated_at, $r->my_last_assign_at])->filter()->max() ?? $r->updated_at;

                return [
                    'type'             => 'appointment',
                    'id'               => (int) $r->id,
                    'title'            => trim((string) $r->title) ?: 'Termin',
                    'subtitle'         => Str::limit($this->cleanText($r->subtitle ?? ''), 160),
                    'status'           => (string) ($r->status ?? 'open'),
                    'priority'         => (string) ($r->priority ?? 'normal'),
                    'due_at'           => $dueAt->toDateTimeString(),
                    'last_activity_at' => Carbon::parse($lastActivity)->toDateTimeString(),
                    'overdue_hours'    => (int) $overdueHours,
                    'progress_pct'     => 40,
                    'changed_summary'  => 'Keine Änderung / kein Bericht seit 48h',
                    'link'             => url("/customer/appointments/{$r->id}"),
                    'meta'             => [],
                ];
            })->filter()->values();
        }
    private function overdueTickets(Carbon $threshold, string $q, $status, $prio, $branch, int $emp)
    {
        if (!$this->hasTable('problems') || !$this->hasTable('employee_problem')) return collect();

        $titleCandidates = [];
        foreach (['article_name', 'title', 'subject', 'ticket_no'] as $col) {
            if ($this->hasColumn('problems', $col)) $titleCandidates[] = "NULLIF(p.$col,'')";
        }
        $titleExpr = $titleCandidates
            ? "COALESCE(" . implode(',', $titleCandidates) . ", 'Ticket')"
            : "'Ticket'";

        $subCandidates = [];
        foreach (['problem', 'progress', 'solution'] as $col) {
            if ($this->hasColumn('problems', $col)) $subCandidates[] = "NULLIF(p.$col,'')";
        }
        $subExpr = $subCandidates
            ? "COALESCE(" . implode(',', $subCandidates) . ", '')"
            : "''";

        $statusSql   = $this->hasColumn('problems', 'status')     ? "COALESCE(p.status,'open')"     : "'open'";
        $prioritySql = $this->hasColumn('problems', 'priority')   ? "COALESCE(p.priority,'normal')" : "'normal'";
        $updatedSql  = $this->hasColumn('problems', 'updated_at') ? "p.updated_at" : "p.created_at";
        $branchSql   = $this->hasColumn('problems', 'branch_id')  ? "p.branch_id"  : "NULL";

        $base = DB::table('problems as p')
            ->when($this->hasColumn('problems', 'deleted_at'), fn($qq) => $qq->whereNull('p.deleted_at'))
            ->whereExists(function ($x) use ($emp) {
                $x->select(DB::raw(1))
                    ->from('employee_problem as ep')
                    ->whereColumn('ep.problem_id', 'p.id')
                    ->where('ep.employee_id', $emp);
            })
            // exclude already reported by ME (ticket_reports.employee_id = emp)
            ->when($this->hasTable('ticket_reports') && $this->hasColumn('ticket_reports', 'employee_id'), function ($qb) use ($emp) {
                $qb->whereNotExists(function ($x) use ($emp) {
                    $x->select(DB::raw(1))
                        ->from('ticket_reports as tr')
                        ->when($this->hasColumn('ticket_reports', 'deleted_at'), fn($t) => $t->whereNull('tr.deleted_at'))
                        ->whereColumn('tr.ticket_id', 'p.id')
                        ->where('tr.employee_id', $emp);
                });
            })
            ->selectRaw("'ticket' as type")
            ->addSelect('p.id')
            ->selectRaw("$titleExpr as title")
            ->selectRaw("$subExpr as subtitle")
            ->selectRaw("$statusSql as status")
            ->selectRaw("$prioritySql as priority")
            ->selectRaw("$updatedSql as updated_at")
            ->addSelect('p.created_at')
            ->selectRaw("$branchSql as branch_id");

        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                foreach (['ticket_no', 'article_name', 'problem', 'solution', 'title', 'subject'] as $col) {
                    if ($this->hasColumn('problems', $col)) $w->orWhere("p.$col", 'like', "%{$q}%");
                }
            });
        }

        if ($status && $this->hasColumn('problems', 'status'))    $base->where('p.status', $status);
        if ($prio   && $this->hasColumn('problems', 'priority'))  $base->where('p.priority', $prio);
        if ($branch && $this->hasColumn('problems', 'branch_id')) $base->where('p.branch_id', $branch);

        $rows = $base->limit(self::LIMIT_TICKET)->get();

        return $rows->map(function ($r) use ($threshold) {
            $lastAt = Carbon::parse($r->updated_at ?? $r->created_at);
            if ($lastAt->gt($threshold)) return null;

            $dueAt        = $lastAt->copy()->addHours(self::HOURS);
            $overdueHours = max(0, $dueAt->diffInHours(now(), false));

            return [
                'type'             => 'ticket',
                'id'               => (int) $r->id,
                'title'            => trim((string) ($r->title ?? '')) ?: 'Ticket',
                'subtitle'         => Str::limit($this->cleanText($r->subtitle ?? ''), 140),
                'status'           => (string) ($r->status ?? 'open'),
                'priority'         => (string) ($r->priority ?? 'normal'),
                'due_at'           => $dueAt->toDateTimeString(),
                'last_activity_at' => $lastAt->toDateTimeString(),
                'overdue_hours'    => (int) $overdueHours,
                'progress_pct'     => 35,
                'changed_summary'  => 'Kein Bericht / keine Änderung seit 48h',
                'link'             => url("/problem/profile/{$r->id}"),
                'meta'             => [],
            ];
        })->filter()->values();
    }

    private function overdueLeadProducts(Carbon $threshold, string $q, $status, $prio, $branch, int $emp)
    {
        if (!$this->hasTable('lead_product_lists')) return collect();

        // last report by ME per (customer_id, alternative_id, product_id)
        $myReportSub = null;
        if ($this->hasTable('customer_reports')) {
            $byCol = $this->hasColumn('customer_reports', 'report_by')
                ? 'report_by'
                : ($this->hasColumn('customer_reports', 'employee_id') ? 'employee_id' : null);

            if ($byCol) {
                $myReportSub = DB::table('customer_reports as cr')
                    ->when($this->hasColumn('customer_reports', 'deleted_at'), fn($x) => $x->whereNull('cr.deleted_at'))
                    ->where("cr.$byCol", $emp)
                    ->groupBy('cr.customer_id', 'cr.alternative_id', 'cr.product_id')
                    ->select([
                        'cr.customer_id',
                        'cr.alternative_id',
                        'cr.product_id',
                        DB::raw('MAX(cr.created_at) as my_last_report_at'),
                    ]);
            }
        }

        $base = DB::table('lead_product_lists as lpl')
            ->leftJoin('new_leads as nl', 'nl.id', '=', 'lpl.customer_id')
            ->leftJoin('lead_alternative_adds as laa', 'laa.id', '=', 'lpl.alternative_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
            ->when($this->hasColumn('lead_product_lists', 'deleted_at'), fn($qq) => $qq->whereNull('lpl.deleted_at'))
            ->where(function ($w) {
                if (Schema::hasTable('new_leads') && Schema::hasColumn('new_leads', 'deleted_at')) {
                    $w->whereNull('nl.deleted_at');
                }
            })
            // assignment to me (innendienst/außendienst or teams JSON)
            ->where(function ($w) use ($emp) {
                $has = false;

                if ($this->hasColumn('lead_product_lists', 'employee_id')) {
                    $w->orWhere('lpl.employee_id', $emp);
                    $has = true;
                }
                if ($this->hasColumn('lead_product_lists', 'field_employee')) {
                    $w->orWhere('lpl.field_employee', $emp);
                    $has = true;
                }

                // teams JSON: [{"employee_id":7,...}, ...]
                if ($this->hasColumn('lead_product_lists', 'teams') && $this->supportsJsonSearch()) {
                    // MySQL: JSON_SEARCH(json_doc, 'one', '7', NULL, '$[*].employee_id') IS NOT NULL
                    $w->orWhereRaw("JSON_SEARCH(lpl.teams, 'one', ?, NULL, '$[*].employee_id') IS NOT NULL", [(string)$emp]);
                    $has = true;
                }

                if (!$has) $w->whereRaw('1=0');
            })
            ->select([
                DB::raw("'lead' as type"),
                'lpl.id',
                'lpl.customer_id',
                'lpl.alternative_id',
                'lpl.product_id',
                $this->hasColumn('lead_product_lists', 'employee_id')      ? 'lpl.employee_id'      : DB::raw("NULL as employee_id"),
                $this->hasColumn('lead_product_lists', 'field_employee')  ? 'lpl.field_employee'  : DB::raw("NULL as field_employee"),
                $this->hasColumn('lead_product_lists', 'teams')           ? 'lpl.teams'           : DB::raw("NULL as teams"),
                $this->hasColumn('lead_product_lists', 'stage_history')   ? 'lpl.stage_history'   : DB::raw("NULL as stage_history"),
                $this->hasColumn('lead_product_lists', 'price_history')   ? 'lpl.price_history'   : DB::raw("NULL as price_history"),
                'lpl.updated_at',
                'lpl.created_at',
                $this->hasColumn('lead_product_lists', 'status')          ? 'lpl.status'          : DB::raw("NULL as status"),
                $this->hasColumn('lead_product_lists', 'work_status')     ? 'lpl.work_status'     : DB::raw("NULL as work_status"),
                $this->hasColumn('lead_product_lists', 'stage')           ? 'lpl.stage'           : DB::raw("NULL as stage"),
                (Schema::hasTable('new_leads') && Schema::hasColumn('new_leads', 'branch_id')) ? 'nl.branch_id' : DB::raw("NULL as branch_id"),
                DB::raw("COALESCE(ag.article_group, '') as product_name"),
                DB::raw("COALESCE(laa.object_name, '') as object_name"),
                DB::raw("COALESCE(nl.firma, TRIM(CONCAT(COALESCE(nl.lastname,''),' ',COALESCE(nl.name,''))), '') as customer_name"),
            ]);

        if ($myReportSub) {
            $base->leftJoinSub($myReportSub, 'crx', function ($j) {
                $j->on('crx.customer_id', '=', 'lpl.customer_id')
                  ->on('crx.alternative_id', '=', 'lpl.alternative_id')
                  ->on('crx.product_id', '=', 'lpl.product_id');
            });
            $base->whereNull('crx.my_last_report_at');
        }

        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $w->orWhere('ag.article_group', 'like', "%{$q}%")
                    ->orWhere('nl.firma', 'like', "%{$q}%")
                    ->orWhere('nl.lastname', 'like', "%{$q}%")
                    ->orWhere('nl.name', 'like', "%{$q}%")
                    ->orWhere('laa.object_name', 'like', "%{$q}%");
            });
        }

        if ($status && $this->hasColumn('lead_product_lists', 'status')) $base->where('lpl.status', $status);
        if ($branch && Schema::hasTable('new_leads') && Schema::hasColumn('new_leads', 'branch_id')) $base->where('nl.branch_id', $branch);

        $rows = $base->limit(self::LIMIT_LEAD)->get();

        // If DB can't JSON_SEARCH, we must additionally filter in PHP by teams
        if ($this->hasColumn('lead_product_lists', 'teams') && !$this->supportsJsonSearch()) {
            $rows = $rows->filter(function ($r) use ($emp) {
                $teams = $this->parseJsonHistory($r->teams ?? null);
                foreach ($teams as $t) {
                    if ((int)($t['employee_id'] ?? 0) === (int)$emp) return true;
                }
                // keep also if employee_id/field_employee matched
                return ((int)($r->employee_id ?? 0) === (int)$emp) || ((int)($r->field_employee ?? 0) === (int)$emp);
            })->values();
        }

        return $rows->map(function ($r) use ($threshold, $emp) {
            $stageHist = $this->parseJsonHistory($r->stage_history ?? null);
            $teamsHist = $this->parseJsonHistory($r->teams ?? null);

            $lastStageAt = collect($stageHist)
                ->pluck('changed_at')
                ->filter()
                ->map(fn($d) => Carbon::parse($d))
                ->max();

            // last assignment event for ME from teams
            $lastTeamAssignAt = null;
            foreach ($teamsHist as $t) {
                if ((int)($t['employee_id'] ?? 0) !== (int)$emp) continue;
                $at = $t['assigned_at'] ?? null;
                if (!$at) continue;
                try {
                    $dt = Carbon::parse($at);
                    if (!$lastTeamAssignAt || $dt->gt($lastTeamAssignAt)) $lastTeamAssignAt = $dt;
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            $last = collect([
                $r->updated_at,
                $lastStageAt?->toDateTimeString(),
                $lastTeamAssignAt?->toDateTimeString(),
            ])->filter()->max() ?? $r->updated_at;

            $lastAt = Carbon::parse($last);
            if ($lastAt->gt($threshold)) return null;

            $dueAt        = $lastAt->copy()->addHours(self::HOURS);
            $overdueHours = max(0, $dueAt->diffInHours(now(), false));

            $latestStage = collect($stageHist)->sortByDesc(fn($x) => $x['changed_at'] ?? '')->first();
            $summary     = $latestStage ? ('Letzte Stage: ' . ($latestStage['stage'] ?? 'n/a')) : 'Keine Stage-Aktivität seit 48h';

            $customerTitle = trim((string)$r->customer_name) ?: 'Lead';

            $subtitleParts = [];
            $product = trim((string)$r->product_name);
            $object  = trim((string)$r->object_name);
            if ($product !== '') $subtitleParts[] = $product;
            if ($object !== '')  $subtitleParts[] = "Objekt: {$object}";

            return [
                'type'             => 'lead',
                'id'               => (int) $r->id,
                'title'            => $customerTitle,
                'subtitle'         => implode(' • ', $subtitleParts),
                'status'           => (string)($r->status ?? '') . ($r->work_status ? " • {$r->work_status}" : ''),
                'priority'         => 'normal',
                'due_at'           => $dueAt->toDateTimeString(),
                'last_activity_at' => $lastAt->toDateTimeString(),
                'overdue_hours'    => (int) $overdueHours,
                'progress_pct'     => 35,
                'changed_summary'  => $summary,
                'link'             => url("/new_lead_profile/{$r->customer_id}"),
                'meta'             => [
                    'customer_id'    => (int)$r->customer_id,
                    'alternative_id' => (int)$r->alternative_id,
                    'product_id'     => (int)$r->product_id,
                    'branch_id'      => $r->branch_id,
                ],
            ];
        })->filter()->values();
    }

    /* =========================================================================
       HISTORY
       ========================================================================= */

    private function historyLeadProduct(int $id)
    {
        $row = DB::table('lead_product_lists as lpl')
            ->where('lpl.id', $id)
            ->select('lpl.id', 'lpl.stage_history', 'lpl.price_history', 'lpl.teams', 'lpl.status', 'lpl.work_status', 'lpl.updated_at')
            ->first();

        return response()->json([
            'type' => 'lead',
            'id' => $id,
            'history' => [
                'teams'         => $this->parseJsonHistory($row?->teams),
                'stage_history' => $this->parseJsonHistory($row?->stage_history),
                'price_history' => $this->parseJsonHistory($row?->price_history),
                'status'        => $row?->status,
                'work_status'   => $row?->work_status,
                'updated_at'    => $row?->updated_at,
            ],
        ]);
    }

    private function historyTask(int $id)
    {
        $changes = $this->hasTable('employees_personal_tasks')
            ? DB::table('employees_personal_tasks as ept')
                ->leftJoin('employees as e', 'e.id', '=', 'ept.changed_by')
                ->where('ept.task_id', $id)
                ->when($this->hasColumn('employees_personal_tasks', 'deleted_at'), fn($x) => $x->whereNull('ept.deleted_at'))
                ->orderByDesc('ept.created_at')
                ->limit(60)
                ->get([
                    'ept.status','ept.reason','ept.note','ept.change_date','ept.change_reason',
                    'e.name as changed_by_name','ept.created_at',
                ])
            : collect();

        $keys = $this->hasTable('personal_task_keys')
            ? DB::table('personal_task_keys as ptk')
                ->where('ptk.personal_task_id', $id)
                ->when($this->hasColumn('personal_task_keys', 'deleted_at'), fn($x) => $x->whereNull('ptk.deleted_at'))
                ->orderByDesc('ptk.created_at')
                ->limit(60)
                ->get([
                    'ptk.task','ptk.status','ptk.is_completed','ptk.done_by','ptk.done_date',
                    'ptk.work_progress','ptk.total_time','ptk.reason','ptk.created_at',
                ])
            : collect();


        return response()->json([
            'type' => 'task',
            'id' => $id,
            'history' => [
                'changes' => $changes,
                'keys'    => $keys,
            ],
        ]);
    }

    private function historyAppointment(int $id)
    {
        $ap = DB::table('main_appointments')
            ->where('id', $id)
            ->first([
                'id', 'name', 'status', 'change_date', 'change_reason', 'changed_by', 'updated_at',
                'is_report', 'report', 'report_date',
            ]);

        $assignees = $this->hasTable('main_appointment_employees')
            ? DB::table('main_appointment_employees as mae')
                ->leftJoin('employees as e', 'e.id', '=', 'mae.employee_id')
                ->where('mae.appointment_id', $id)
                ->when($this->hasColumn('main_appointment_employees', 'deleted_at'), fn($x) => $x->whereNull('mae.deleted_at'))
                ->orderByDesc('mae.updated_at')
                ->limit(80)
                ->get(['mae.employee_id','e.name','e.lastname','mae.status','mae.reason','mae.updated_at'])
            : collect();


        return response()->json(['type' => 'appointment', 'id' => $id, 'history' => ['appointment' => $ap, 'assignees' => $assignees]]);
    }

    private function historyInquiry(int $id)
    {
        $inq = DB::table('inquiries')
            ->where('id', $id)
            ->first([
                'id', 'title', 'status', 'periority', 'next_step', 'verify_by', 'verify_date',
                'updated_at', 'created_at', 'note',
            ]);

        $products = $this->hasTable('inquiry_product_lists')
            ? DB::table('inquiry_product_lists as ipl')
                ->where('ipl.inquiry_id', $id)
                ->when($this->hasColumn('inquiry_product_lists', 'deleted_at'), fn($x) => $x->whereNull('ipl.deleted_at'))
                ->orderByDesc('ipl.updated_at')
                ->limit(60)
                ->get([
                    'ipl.id','ipl.status','ipl.appointment_date','ipl.employee_id','ipl.field_employee',
                    'ipl.department_id','ipl.product_id','ipl.service_id','ipl.updated_at',
                ])
            : collect();


        return response()->json(['type' => 'inquiry', 'id' => $id, 'history' => ['inquiry' => $inq, 'products' => $products]]);
    }

    private function historyTicket(int $id)
    {
        $p = DB::table('problems')
            ->where('id', $id)
            ->first([
                'id', 'ticket_no', 'status', 'date', 'progress_date', 'end_date', 'edit_date',
                'updated_at', 'problem', 'progress', 'solution', 'priority',
            ]);

        $assignees = $this->hasTable('employee_problem')
            ? DB::table('employee_problem as ep')
                ->leftJoin('employees as e', 'e.id', '=', 'ep.employee_id')
                ->where('ep.problem_id', $id)
                ->when($this->hasColumn('employee_problem', 'deleted_at'), fn($x) => $x->whereNull('ep.deleted_at'))
                ->orderByDesc('ep.updated_at')
                ->limit(80)
                ->get(['ep.employee_id','e.name','e.lastname','ep.created_at'])
            : collect();


        return response()->json(['type' => 'ticket', 'id' => $id, 'history' => ['problem' => $p, 'assignees' => $assignees]]);
    }

    /* =========================================================================
       REPORTS LIST (ONLY MY REPORTS)
       ========================================================================= */

    private function reportsAppointment(int $id)
    {
        $emp = $this->currentEmployeeId();
        if ($emp <= 0) abort(403);

        $byCol = $this->hasColumn('appointment_reports', 'report_by')
            ? 'report_by'
            : ($this->hasColumn('appointment_reports', 'employee_id') ? 'employee_id' : null);

        $appointmentIdCol = $this->hasColumn('appointment_reports', 'appointment_id')
            ? 'appointment_id'
            : ($this->hasColumn('appointment_reports', 'main_appointment_id') ? 'main_appointment_id' : null);

        if (!$this->hasTable('appointment_reports') || !$byCol || !$appointmentIdCol) {
            return response()->json(['rows' => []]);
        }

        $rows = DB::table('appointment_reports as ar')
            ->leftJoin('employees as e', 'e.id', '=', "ar.$byCol")
            ->where("ar.$appointmentIdCol", $id)
            ->where("ar.$byCol", $emp)
            ->when($this->hasColumn('appointment_reports', 'deleted_at'), fn($q) => $q->whereNull('ar.deleted_at'))
            ->orderByDesc('ar.created_at')
            ->limit(80)
            ->get(['ar.id', 'ar.report', 'ar.report_date', 'ar.next_step', 'ar.due_date', DB::raw("e.name as report_by_name"), 'ar.created_at']);

        return response()->json(['rows' => $rows]);
    }

    private function reportsTask(int $id)
    {
        $emp = $this->currentEmployeeId();
        if ($emp <= 0) abort(403);

        $byCol = $this->hasColumn('appointment_reports', 'report_by')
            ? 'report_by'
            : ($this->hasColumn('appointment_reports', 'employee_id') ? 'employee_id' : null);

        $taskIdCol = $this->hasColumn('appointment_reports', 'task_id')
            ? 'task_id'
            : ($this->hasColumn('appointment_reports', 'personal_task_id') ? 'personal_task_id' : null);

        if (!$this->hasTable('appointment_reports') || !$byCol || !$taskIdCol) {
            return response()->json(['rows' => []]);
        }

        $rows = DB::table('appointment_reports as ar')
            ->leftJoin('employees as e', 'e.id', '=', "ar.$byCol")
            ->where("ar.$taskIdCol", $id)
            ->where("ar.$byCol", $emp)
            ->when($this->hasColumn('appointment_reports', 'deleted_at'), fn($q) => $q->whereNull('ar.deleted_at'))
            ->orderByDesc('ar.created_at')
            ->limit(80)
            ->get(['ar.id', 'ar.report', 'ar.report_date', 'ar.next_step', 'ar.due_date', DB::raw("e.name as report_by_name"), 'ar.created_at']);

        return response()->json(['rows' => $rows]);
    }

    private function reportsTicket(int $id)
    {
        $emp = $this->currentEmployeeId();
        if ($emp <= 0) abort(403);

        if (!$this->hasTable('ticket_reports')) return response()->json(['rows' => []]);

        $byCol = $this->hasColumn('ticket_reports', 'employee_id')
            ? 'employee_id'
            : ($this->hasColumn('ticket_reports', 'report_by') ? 'report_by' : null);

        if (!$byCol) return response()->json(['rows' => []]);

        $rows = DB::table('ticket_reports as tr')
            ->leftJoin('employees as e', 'e.id', '=', "tr.$byCol")
            ->where('tr.ticket_id', $id)
            ->where("tr.$byCol", $emp)
            ->when($this->hasColumn('ticket_reports', 'deleted_at'), fn($q) => $q->whereNull('tr.deleted_at'))
            ->orderByDesc('tr.created_at')
            ->limit(80)
            ->get([
                'tr.id',
                'tr.title',
                'tr.report',
                'tr.language',
                'tr.report_date',
                'tr.likes',
                DB::raw("e.name as report_by_name"),
                'tr.created_at',
            ]);

        return response()->json(['rows' => $rows]);
    }

    private function reportsInquiry(int $id)
    {
        $emp = $this->currentEmployeeId();
        if ($emp <= 0) abort(403);

        $byCol = $this->hasColumn('inquiry_reports', 'report_by')
            ? 'report_by'
            : ($this->hasColumn('inquiry_reports', 'employee_id') ? 'employee_id' : null);

        if (!$this->hasTable('inquiry_reports') || !$byCol) return response()->json(['rows' => []]);

        $rows = DB::table('inquiry_reports as ir')
            ->leftJoin('employees as e', 'e.id', '=', "ir.$byCol")
            ->where('ir.inquiry_id', $id)
            ->where("ir.$byCol", $emp)
            ->when($this->hasColumn('inquiry_reports', 'deleted_at'), fn($q) => $q->whereNull('ir.deleted_at'))
            ->orderByDesc('ir.created_at')
            ->limit(80)
            ->get(['ir.id', 'ir.report', 'ir.report_date', 'ir.due_date', DB::raw("e.name as report_by_name"), 'ir.created_at']);

        return response()->json(['rows' => $rows]);
    }

    private function reportsLeadProduct(int $id)
    {
        $emp = $this->currentEmployeeId();
        if ($emp <= 0) abort(403);

        $meta = DB::table('lead_product_lists')
            ->where('id', $id)
            ->first(['customer_id', 'alternative_id', 'product_id']);

        if (!$meta || !$this->hasTable('customer_reports')) return response()->json(['rows' => []]);

        $byCol = $this->hasColumn('customer_reports', 'report_by')
            ? 'report_by'
            : ($this->hasColumn('customer_reports', 'employee_id') ? 'employee_id' : null);

        if (!$byCol) return response()->json(['rows' => []]);

        $rows = DB::table('customer_reports as cr')
            ->leftJoin('employees as e', 'e.id', '=', "cr.$byCol")
            ->where('cr.customer_id', $meta->customer_id)
            ->where('cr.alternative_id', $meta->alternative_id)
            ->where('cr.product_id', $meta->product_id)
            ->where("cr.$byCol", $emp)
            ->when($this->hasColumn('customer_reports', 'deleted_at'), fn($qq) => $qq->whereNull('cr.deleted_at'))
            ->orderByDesc('cr.created_at')
            ->limit(80)
            ->get(['cr.id', 'cr.stage', 'cr.report', 'cr.report_details', DB::raw("e.name as report_by_name"), 'cr.created_at']);

        return response()->json(['rows' => $rows]);
    }

    /* =========================================================================
       REPORT STORE
       ========================================================================= */

    private function storeReportAppointment(int $id, int $employeeId, Request $request, bool $isSkipped)
    {
        if (!$this->hasTable('appointment_reports')) {
            return response()->json(['message' => 'Not available'], 422);
        }

        $text = trim((string)$request->input('report', ''));
        if ($isSkipped) $text = $this->buildSkipReportText('appointment', $id, $employeeId, $request);

        $appointmentIdCol = $this->hasColumn('appointment_reports', 'appointment_id')
            ? 'appointment_id'
            : ($this->hasColumn('appointment_reports', 'main_appointment_id') ? 'main_appointment_id' : null);

        if (!$appointmentIdCol) return response()->json(['message' => 'Missing appointment id column on appointment_reports'], 422);

        DB::table('appointment_reports')->insert([
            'employee_id'     => $employeeId,
            $appointmentIdCol => $id,
            'type'            => 'appointment',
            'report'          => $text,
            'report_date'     => $request->input('report_date') ?? now()->toDateString(),
            'due_date'        => $request->input('due_date'),
            'report_by'       => $employeeId,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // keep your existing "report columns" in main_appointments updated
        if ($this->hasTable('main_appointments')) {
            $updates = ['updated_at' => now()];

            if ($this->hasColumn('main_appointments', 'is_report'))   $updates['is_report']   = true;
            if ($this->hasColumn('main_appointments', 'report_date')) $updates['report_date'] = now()->toDateString();
            if ($this->hasColumn('main_appointments', 'report_by'))   $updates['report_by']   = $employeeId;

            DB::table('main_appointments')->where('id', $id)->update($updates);
        }

        return response()->json(['ok' => true]);
    }

    private function storeReportTask(int $id, int $employeeId, Request $request, bool $isSkipped)
    {
        if (!$this->hasTable('appointment_reports')) {
            return response()->json(['message' => 'Not available'], 422);
        }

        $text = trim((string)$request->input('report', ''));
        if ($isSkipped) $text = $this->buildSkipReportText('task', $id, $employeeId, $request);

        $taskIdCol = $this->hasColumn('appointment_reports', 'task_id')
            ? 'task_id'
            : ($this->hasColumn('appointment_reports', 'personal_task_id') ? 'personal_task_id' : null);

        if (!$taskIdCol) return response()->json(['message' => 'Missing task id column on appointment_reports'], 422);

        DB::table('appointment_reports')->insert([
            'employee_id' => $employeeId,
            $taskIdCol    => $id,
            'type'        => 'task',
            'report'      => $text,
            'report_date' => $request->input('report_date') ?? now()->toDateString(),
            'due_date'    => $request->input('due_date'),
            'report_by'   => $employeeId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    private function storeReportTicket(int $id, int $employeeId, Request $request, bool $isSkipped)
    {
        if (!$this->hasTable('ticket_reports')) {
            return response()->json(['message' => 'Not available'], 422);
        }

        $p = $this->hasTable('problems') ? DB::table('problems')->where('id', $id)->first() : null;

        $customerId    = 0;
        $alternativeId = 0;
        $productId     = 0;

        if ($p) {
            if ($this->hasColumn('problems', 'customer_id'))    $customerId    = (int)($p->customer_id ?? 0);
            if ($this->hasColumn('problems', 'alternative_id')) $alternativeId = (int)($p->alternative_id ?? 0);
            if ($this->hasColumn('problems', 'product_id'))     $productId     = (int)($p->product_id ?? 0);
        }

        $customerId    = $customerId    ?: (int)$request->input('customer_id', 0);
        $alternativeId = $alternativeId ?: (int)$request->input('alternative_id', 0);
        $productId     = $productId     ?: (int)$request->input('product_id', 0);

        if ($customerId <= 0 || $alternativeId <= 0 || $productId <= 0) {
            return response()->json([
                'message' => 'Missing ticket linkage (customer_id / alternative_id / product_id). Add these columns to problems or send them from the UI.',
            ], 422);
        }

        $reportText = trim((string)$request->input('report', ''));
        if ($isSkipped) $reportText = $this->buildSkipReportText('ticket', $id, $employeeId, $request);

        $title = trim((string)$request->input('title', ''));
        if ($title === '') {
            $title = $this->getRecordTitle('ticket', $id) ?: '';
            if ($title === '') {
                $plain = trim(preg_replace('/\s+/', ' ', $reportText));
                $title = mb_substr($plain, 0, 80) ?: ($isSkipped ? self::SKIP_TEXT : 'Ticket Report');
            }
        }

        $language = $request->input('language') ?? app()->getLocale();

        DB::table('ticket_reports')->insert([
            'ticket_id'      => $id,
            'employee_id'    => $employeeId,
            'customer_id'    => $customerId,
            'alternative_id' => $alternativeId,
            'product_id'     => $productId,
            'title'          => $title,
            'report'         => $reportText,
            'language'       => $language,
            'report_date'    => $request->input('report_date') ?? now()->toDateString(),
            'likes'          => 0,
            'created_at'     => now(),
            'updated_at'     => now(),
            'deleted_at'     => null,
        ]);

        return response()->json(['ok' => true]);
    }

    private function storeReportInquiry(int $id, int $employeeId, Request $request, bool $isSkipped)
    {
        $text = trim((string)$request->input('report', ''));
        if ($isSkipped) $text = $this->buildSkipReportText('inquiry', $id, $employeeId, $request);

        $meta = $isSkipped ? json_encode([
            'skipped'     => true,
            'skip_reason' => (string)$request->input('skip_reason', ''),
            'skipped_at'  => now()->toDateTimeString(),
            'skipped_by'  => $employeeId,
        ]) : null;

        InquiryReport::create([
            'inquiry_id'   => $id,
            'report_by'    => $employeeId,
            'report'       => $text,
            'report_date'  => $request->input('report_date') ?? now(),
            'due_date'     => $request->input('due_date'),
            'meta'         => $meta,
        ]);

        return response()->json(['ok' => true]);
    }

    private function storeReportLeadProduct(int $id, int $employeeId, Request $request, bool $isSkipped)
    {
        if (!$this->hasTable('lead_product_lists') || !$this->hasTable('customer_reports')) {
            return response()->json(['message' => 'Not available'], 422);
        }

        $meta = DB::table('lead_product_lists')
            ->where('id', $id)
            ->first(['customer_id', 'alternative_id', 'product_id', 'stage', 'status']);

        if (!$meta) return response()->json(['message' => 'Not found'], 404);

        $text = trim((string)$request->input('report', ''));
        if ($isSkipped) $text = $this->buildSkipReportText('lead', $id, $employeeId, $request);

        $details = $isSkipped ? json_encode([
            'skipped'     => true,
            'skip_reason' => (string)$request->input('skip_reason', ''),
            'skipped_at'  => now()->toDateTimeString(),
            'skipped_by'  => $employeeId,
        ]) : null;

        DB::table('customer_reports')->insert([
            'customer_id'     => $meta->customer_id,
            'alternative_id'  => $meta->alternative_id,
            'product_id'      => $meta->product_id,
            'report_by'       => $employeeId,
            'stage'           => $meta->stage ?: $meta->status,
            'report'          => $text,
            'report_details'  => $details,
            'created_at'      => now(),
            'updated_at'      => now(),
            'deleted_at'      => null,
        ]);

        return response()->json(['ok' => true]);
    }

    /* =========================================================================
       SKIP TEXT BUILDERS
       ========================================================================= */

    private function typeLabelDe(string $type): string
    {
        return match ($type) {
            'inquiry'     => 'Anfrage',
            'task'        => 'Aufgabe',
            'appointment' => 'Termin',
            'ticket'      => 'Ticket',
            'lead'        => 'Lead',
            default       => 'Eintrag',
        };
    }

    private function formatDateDe($date): string
    {
        try {
            return Carbon::parse($date)->format('d.m.Y');
        } catch (\Throwable $e) {
            return now()->format('d.m.Y');
        }
    }

    private function getRecordTitle(string $type, int $id): ?string
    {
        try {
            return match ($type) {
                'inquiry' => $this->hasTable('inquiries')
                    ? (string) DB::table('inquiries')->where('id', $id)->value(
                        $this->firstExistingColumn('inquiries', ['title', 'subject', 'firma', 'name']) ?? 'title'
                    )
                    : null,

                'task' => $this->hasTable('personal_tasks')
                    ? (string) DB::table('personal_tasks')->where('id', $id)->value(
                        $this->firstExistingColumn('personal_tasks', ['task_title', 'title', 'task', 'name', 'subject']) ?? 'title'
                    )
                    : null,

                'appointment' => $this->hasTable('main_appointments')
                    ? (string) DB::table('main_appointments')->where('id', $id)->value(
                        $this->firstExistingColumn('main_appointments', ['name', 'title', 'subject']) ?? 'name'
                    )
                    : null,

                'ticket' => $this->hasTable('problems')
                    ? (string) DB::table('problems')->where('id', $id)->value(
                        $this->firstExistingColumn('problems', ['article_name', 'title', 'subject', 'ticket_no']) ?? 'title'
                    )
                    : null,

                'lead' => 'Lead',
                default => null,
            };
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function buildSkipReportText(string $type, int $id, int $employeeId, Request $request): string
    {
        $name = trim((string)(auth()->user()->name ?? ''));
        if ($name === '') $name = "MA #{$employeeId}";

        $date   = $this->formatDateDe($request->input('report_date') ?? now());
        $reason = trim((string)$request->input('skip_reason', ''));
        $reason = preg_replace('/\s+/', ' ', $reason) ?: '—';

        $label = $this->typeLabelDe($type);

        $title = trim((string)($this->getRecordTitle($type, $id) ?? ''));
        $ref   = $title !== '' ? "„{$title}“" : "#{$id}";

        return "Am {$date} hat {$name} den Bericht für {$label} {$ref} übersprungen. Grund: {$reason}.";
    }

    /* =========================================================================
       ACCESS CONTROL (assignment-based, not creator-based)
       ========================================================================= */

    private function abortIfNotInvolved(string $type, int $id): void
    {
        $emp = $this->currentEmployeeId();
        if ($emp <= 0) abort(403);

        $ok = match ($type) {
            'inquiry'     => $this->isEmployeeInvolvedInInquiry($id, $emp),
            'task'        => $this->isEmployeeInvolvedInTask($id, $emp),
            'appointment' => $this->isEmployeeInvolvedInAppointment($id, $emp),
            'ticket'      => $this->isEmployeeInvolvedInTicket($id, $emp),
            'lead'        => $this->isEmployeeInvolvedInLeadProduct($id, $emp),
            default       => false,
        };

        if (!$ok) abort(403);
    }

    private function isEmployeeInvolvedInInquiry(int $id, int $emp): bool
    {
        if (!$this->hasTable('inquiry_product_lists')) return false;

        return DB::table('inquiry_product_lists as ipl')
            ->where('ipl.inquiry_id', $id)
            ->when($this->hasColumn('inquiry_product_lists', 'deleted_at'), fn($x) => $x->whereNull('ipl.deleted_at'))
            ->where(function ($w) use ($emp) {
                $any = false;
                if ($this->hasColumn('inquiry_product_lists', 'employee_id')) {
                    $w->orWhere('ipl.employee_id', $emp);
                    $any = true;
                }
                if ($this->hasColumn('inquiry_product_lists', 'field_employee')) {
                    $w->orWhere('ipl.field_employee', $emp);
                    $any = true;
                }
                if (!$any) $w->whereRaw('1=0');
            })
            ->exists();
    }

    private function isEmployeeInvolvedInTask(int $id, int $emp): bool
    {
        if (!$this->hasTable('employees_personal_tasks')) return false;

        return DB::table('employees_personal_tasks as ept')
            ->where('ept.task_id', $id)
            ->where('ept.employee_id', $emp)
            ->when($this->hasColumn('employees_personal_tasks', 'deleted_at'), fn($x) => $x->whereNull('ept.deleted_at'))
            ->exists();
    }

    private function isEmployeeInvolvedInAppointment(int $id, int $emp): bool
    {
        if (!$this->hasTable('main_appointment_employees')) return false;

        return DB::table('main_appointment_employees as mae')
            ->where('mae.appointment_id', $id)
            ->where('mae.employee_id', $emp)
            ->exists();
    }

    private function isEmployeeInvolvedInTicket(int $id, int $emp): bool
    {
        if (!$this->hasTable('employee_problem')) return false;

        return DB::table('employee_problem as ep')
            ->where('ep.problem_id', $id)
            ->where('ep.employee_id', $emp)
            ->exists();
    }

    private function isEmployeeInvolvedInLeadProduct(int $id, int $emp): bool
    {
        if (!$this->hasTable('lead_product_lists')) return false;

        $row = DB::table('lead_product_lists as lpl')
            ->where('lpl.id', $id)
            ->when($this->hasColumn('lead_product_lists', 'deleted_at'), fn($x) => $x->whereNull('lpl.deleted_at'))
            ->first([
                'id',
                $this->hasColumn('lead_product_lists', 'employee_id') ? 'employee_id' : DB::raw('NULL as employee_id'),
                $this->hasColumn('lead_product_lists', 'field_employee') ? 'field_employee' : DB::raw('NULL as field_employee'),
                $this->hasColumn('lead_product_lists', 'teams') ? 'teams' : DB::raw('NULL as teams'),
            ]);

        if (!$row) return false;

        if ((int)($row->employee_id ?? 0) === $emp) return true;
        if ((int)($row->field_employee ?? 0) === $emp) return true;

        $teams = $this->parseJsonHistory($row->teams ?? null);
        foreach ($teams as $t) {
            if ((int)($t['employee_id'] ?? 0) === $emp) return true;
        }

        return false;
    }

    /**
     * FIXED: determine employee id correctly.
     * You store employee_id in auth()->user()->name (string).
     */
    private function currentEmployeeId(): int
    {
        $user = auth()->user();
        if (!$user) return 0;

        $raw = trim((string)($user->name ?? ''));

        if ($raw === '' || !preg_match('/^\d+$/', $raw)) {
            return 0;
        }

        $empId = (int) $raw;
        if ($empId <= 0) return 0;

        if ($this->hasTable('employees')) {
            $exists = DB::table('employees')->where('id', $empId)->exists();
            if (!$exists) return 0;
        }

        return $empId;
    }

    private function canViewAllEmployees(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if (isset($user->is_admin) && (bool)$user->is_admin === true) return true;
        if (isset($user->admin) && (bool)$user->admin === true) return true;
        if (isset($user->role) && is_string($user->role) && in_array(strtolower($user->role), ['admin', 'superadmin', 'owner'], true)) return true;

        if (method_exists($user, 'hasRole')) {
            try {
                if ($user->hasRole('admin') || $user->hasRole('superadmin') || $user->hasRole('owner')) return true;
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return false;
    }

    /* =========================================================================
       RECENT REPORTS (unchanged behavior; you said this part is correct)
       ========================================================================= */

       public function recentReportsFetch(Request $request)
        {
            $currentEmployeeId = $this->currentEmployeeId();
            $canViewAll        = $this->canViewAllEmployees();

            $sort    = (string) $request->get('sort', 'newest'); // newest|oldest
            $page    = max(1, (int) $request->get('page', 1));
            $perPage = min(self::MAX_PER_PAGE, max(self::MIN_PER_PAGE, (int) $request->get('per_page', self::DEFAULT_PER_PAGE)));

            $q     = trim((string) $request->get('q', ''));
            $types = $this->normalizeTypes($request->input('types', self::TYPES));

            // employee filter: non-admin always forced to current employee
            $employeeId = (int) $request->get('employee_id', 0);
            if (!$canViewAll) {
                $employeeId = $currentEmployeeId;
            } else {
                // admin: allow 0/all or specific id
                if ($employeeId < 0) $employeeId = 0;
            }

            // date range (default: last 30 days)
            try {
                $from = $request->get('date_from')
                    ? Carbon::parse($request->get('date_from'))->startOfDay()
                    : now()->subDays(30)->startOfDay();
            } catch (\Throwable $e) {
                $from = now()->subDays(30)->startOfDay();
            }

            try {
                $to = $request->get('date_to')
                    ? Carbon::parse($request->get('date_to'))->endOfDay()
                    : now()->endOfDay();
            } catch (\Throwable $e) {
                $to = now()->endOfDay();
            }

            $items = collect();

            // helper: return BOTH the old frontend keys and your new keys
            $pack = function (
                string $type,
                int $entityId,
                int $reportId,
                string $title,
                string $report,
                ?string $createdAt,
                ?string $reportDate,
                ?string $dueDate,
                string $nextStep,
                int $reportById,
                string $reportByName,
                ?string $link
            ) {
                $title = trim($title) !== '' ? trim($title) : ucfirst($type);
                $createdAt = (string)($createdAt ?? '');

                return [
                    // OLD keys expected by recent_reports_center frontend
                    'type'          => $type,
                    'entity_id'     => $entityId,
                    'ref_title'     => $title,
                    'employee_name' => trim($reportByName) !== '' ? trim($reportByName) : '—',
                    'report_text'   => (string) $report,
                    'created_at'    => $createdAt,
                    'report_id'     => $reportId,
                    'link'          => $link,

                    // NEW keys (keep for other UIs / consistency)
                    'id'            => $entityId,
                    'title'         => $title,
                    'report'        => (string) $report,
                    'report_date'   => $reportDate,
                    'due_date'      => $dueDate,
                    'next_step'     => (string) $nextStep,
                    'report_by'     => $reportById,
                    'report_by_name'=> trim((string) $reportByName),
                ];
            };

            /* =========================================================================
            APPOINTMENT_REPORTS (appointments + tasks)
            ========================================================================= */
            if ($this->hasTable('appointment_reports')) {
                $byCol = $this->hasColumn('appointment_reports', 'report_by')
                    ? 'report_by'
                    : ($this->hasColumn('appointment_reports', 'employee_id') ? 'employee_id' : null);

                $appointmentIdCol = $this->hasColumn('appointment_reports', 'appointment_id')
                    ? 'appointment_id'
                    : ($this->hasColumn('appointment_reports', 'main_appointment_id') ? 'main_appointment_id' : null);

                $taskIdCol = $this->hasColumn('appointment_reports', 'task_id')
                    ? 'task_id'
                    : ($this->hasColumn('appointment_reports', 'personal_task_id') ? 'personal_task_id' : null);

                if ($byCol) {
                    // -------------------------
                    // appointments
                    // -------------------------
                    if ($appointmentIdCol && in_array('appointment', $types, true)) {
                        $qr = DB::table('appointment_reports as ar')
                            ->when($this->hasColumn('appointment_reports', 'deleted_at'), fn($x) => $x->whereNull('ar.deleted_at'))
                            ->whereBetween('ar.created_at', [$from, $to])
                            ->whereNotNull("ar.$appointmentIdCol");

                        if ($employeeId > 0) $qr->where("ar.$byCol", $employeeId);

                        if ($q !== '') {
                            $qr->where(function ($w) use ($q) {
                                $w->orWhere('ar.report', 'like', "%{$q}%")
                                ->orWhere('ar.next_step', 'like', "%{$q}%");
                            });
                        }

                        $qr->leftJoin('employees as e', 'e.id', '=', "ar.$byCol");

                        if ($this->hasTable('main_appointments')) {
                            $qr->leftJoin('main_appointments as a', 'a.id', '=', "ar.$appointmentIdCol");
                            if ($this->hasColumn('main_appointments', 'deleted_at')) $qr->whereNull('a.deleted_at');
                        }

                        $titleExpr = "'Termin'";
                        if ($this->hasTable('main_appointments')) {
                            $cands = [];
                            foreach (['name', 'title', 'subject'] as $c) {
                                if ($this->hasColumn('main_appointments', $c)) $cands[] = "NULLIF(a.$c,'')";
                            }
                            if ($cands) $titleExpr = "COALESCE(" . implode(',', $cands) . ", 'Termin')";
                        }

                        $rows = $qr->orderByDesc('ar.created_at')
                            ->limit(2000)
                            ->get([
                                'ar.id as report_id',
                                DB::raw("ar.$appointmentIdCol as item_id"),
                                DB::raw("$titleExpr as title"),
                                'ar.report',
                                'ar.report_date',
                                'ar.due_date',
                                'ar.next_step',
                                DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as report_by_name"),
                                DB::raw("ar.$byCol as report_by_id"),
                                'ar.created_at',
                            ]);

                        $items = $items->merge($rows->map(function ($r) use ($pack) {
                            $itemId = (int)($r->item_id ?? 0);
                            return $pack(
                                'appointment',
                                $itemId,
                                (int)($r->report_id ?? 0),
                                (string)($r->title ?? 'Termin'),
                                (string)($r->report ?? ''),
                                (string)($r->created_at ?? ''),
                                $r->report_date ? (string)$r->report_date : null,
                                $r->due_date ? (string)$r->due_date : null,
                                (string)($r->next_step ?? ''),
                                (int)($r->report_by_id ?? 0),
                                (string)($r->report_by_name ?? ''),
                                $itemId > 0 ? url("/customer/appointments/{$itemId}") : null
                            );
                        }));
                    }

                    // -------------------------
                    // tasks
                    // -------------------------
                    if ($taskIdCol && in_array('task', $types, true)) {
                        $qr = DB::table('appointment_reports as ar')
                            ->when($this->hasColumn('appointment_reports', 'deleted_at'), fn($x) => $x->whereNull('ar.deleted_at'))
                            ->whereBetween('ar.created_at', [$from, $to])
                            ->whereNotNull("ar.$taskIdCol");

                        if ($employeeId > 0) $qr->where("ar.$byCol", $employeeId);

                        if ($q !== '') {
                            $qr->where(function ($w) use ($q) {
                                $w->orWhere('ar.report', 'like', "%{$q}%")
                                ->orWhere('ar.next_step', 'like', "%{$q}%");
                            });
                        }

                        $qr->leftJoin('employees as e', 'e.id', '=', "ar.$byCol");

                        if ($this->hasTable('personal_tasks')) {
                            $qr->leftJoin('personal_tasks as t', 't.id', '=', "ar.$taskIdCol");
                            if ($this->hasColumn('personal_tasks', 'deleted_at')) $qr->whereNull('t.deleted_at');
                        }

                        $titleExpr = "'Aufgabe'";
                        if ($this->hasTable('personal_tasks')) {
                            $cands = [];
                            foreach (['task_title', 'title', 'task', 'name', 'subject'] as $c) {
                                if ($this->hasColumn('personal_tasks', $c)) $cands[] = "NULLIF(t.$c,'')";
                            }
                            if ($cands) $titleExpr = "COALESCE(" . implode(',', $cands) . ", 'Aufgabe')";
                        }

                        $rows = $qr->orderByDesc('ar.created_at')
                            ->limit(2000)
                            ->get([
                                'ar.id as report_id',
                                DB::raw("ar.$taskIdCol as item_id"),
                                DB::raw("$titleExpr as title"),
                                'ar.report',
                                'ar.report_date',
                                'ar.due_date',
                                'ar.next_step',
                                DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as report_by_name"),
                                DB::raw("ar.$byCol as report_by_id"),
                                'ar.created_at',
                            ]);

                        $items = $items->merge($rows->map(function ($r) use ($pack) {
                            $itemId = (int)($r->item_id ?? 0);
                            return $pack(
                                'task',
                                $itemId,
                                (int)($r->report_id ?? 0),
                                (string)($r->title ?? 'Aufgabe'),
                                (string)($r->report ?? ''),
                                (string)($r->created_at ?? ''),
                                $r->report_date ? (string)$r->report_date : null,
                                $r->due_date ? (string)$r->due_date : null,
                                (string)($r->next_step ?? ''),
                                (int)($r->report_by_id ?? 0),
                                (string)($r->report_by_name ?? ''),
                                $itemId > 0 ? url("/personal-tasks/{$itemId}/profile") : null
                            );
                        }));
                    }
                }
            }

            /* =========================================================================
            INQUIRY_REPORTS
            ========================================================================= */
            if ($this->hasTable('inquiry_reports') && in_array('inquiry', $types, true)) {
                $byCol = $this->hasColumn('inquiry_reports', 'report_by')
                    ? 'report_by'
                    : ($this->hasColumn('inquiry_reports', 'employee_id') ? 'employee_id' : null);

                if ($byCol) {
                    $qr = DB::table('inquiry_reports as ir')
                        ->when($this->hasColumn('inquiry_reports', 'deleted_at'), fn($x) => $x->whereNull('ir.deleted_at'))
                        ->whereBetween('ir.created_at', [$from, $to]);

                    if ($employeeId > 0) $qr->where("ir.$byCol", $employeeId);
                    if ($q !== '') $qr->where('ir.report', 'like', "%{$q}%");

                    $qr->leftJoin('employees as e', 'e.id', '=', "ir.$byCol");

                    $titleExpr = "'Anfrage'";
                    if ($this->hasTable('inquiries')) {
                        $qr->leftJoin('inquiries as i', 'i.id', '=', 'ir.inquiry_id');
                        if ($this->hasColumn('inquiries', 'deleted_at')) $qr->whereNull('i.deleted_at');

                        $cands = [];
                        foreach (['title', 'subject', 'firma'] as $c) {
                            if ($this->hasColumn('inquiries', $c)) $cands[] = "NULLIF(i.$c,'')";
                        }
                        if ($this->hasColumn('inquiries', 'name')) {
                            $cands[] = "NULLIF(TRIM(CONCAT(COALESCE(i.lastname,''),' ',COALESCE(i.name,''))), '')";
                        }
                        if ($cands) $titleExpr = "COALESCE(" . implode(',', $cands) . ", 'Anfrage')";
                    }

                    $rows = $qr->orderByDesc('ir.created_at')
                        ->limit(2000)
                        ->get([
                            'ir.id as report_id',
                            'ir.inquiry_id as item_id',
                            DB::raw("$titleExpr as title"),
                            'ir.report',
                            'ir.report_date',
                            'ir.due_date',
                            DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as report_by_name"),
                            DB::raw("ir.$byCol as report_by_id"),
                            'ir.created_at',
                        ]);

                    $items = $items->merge($rows->map(function ($r) use ($pack) {
                        $itemId = (int)($r->item_id ?? 0);
                        return $pack(
                            'inquiry',
                            $itemId,
                            (int)($r->report_id ?? 0),
                            (string)($r->title ?? 'Anfrage'),
                            (string)($r->report ?? ''),
                            (string)($r->created_at ?? ''),
                            $r->report_date ? (string)$r->report_date : null,
                            $r->due_date ? (string)$r->due_date : null,
                            '',
                            (int)($r->report_by_id ?? 0),
                            (string)($r->report_by_name ?? ''),
                            $itemId > 0 ? url("/inquiry_show/{$itemId}") : null
                        );
                    }));
                }
            }

            /* =========================================================================
            TICKET_REPORTS
            ========================================================================= */
            if ($this->hasTable('ticket_reports') && in_array('ticket', $types, true)) {
                $byCol = $this->hasColumn('ticket_reports', 'employee_id')
                    ? 'employee_id'
                    : ($this->hasColumn('ticket_reports', 'report_by') ? 'report_by' : null);

                if ($byCol) {
                    $qr = DB::table('ticket_reports as tr')
                        ->when($this->hasColumn('ticket_reports', 'deleted_at'), fn($x) => $x->whereNull('tr.deleted_at'))
                        ->whereBetween('tr.created_at', [$from, $to]);

                    if ($employeeId > 0) $qr->where("tr.$byCol", $employeeId);

                    if ($q !== '') {
                        $qr->where(function ($w) use ($q) {
                            $w->orWhere('tr.title', 'like', "%{$q}%")
                            ->orWhere('tr.report', 'like', "%{$q}%");
                        });
                    }

                    $qr->leftJoin('employees as e', 'e.id', '=', "tr.$byCol");

                    $titleExpr = "COALESCE(NULLIF(tr.title,''), 'Ticket')";
                    if ($this->hasTable('problems')) {
                        $qr->leftJoin('problems as p', 'p.id', '=', 'tr.ticket_id');
                        if ($this->hasColumn('problems', 'deleted_at')) $qr->whereNull('p.deleted_at');

                        $cands = [];
                        foreach (['article_name', 'title', 'subject', 'ticket_no'] as $c) {
                            if ($this->hasColumn('problems', $c)) $cands[] = "NULLIF(p.$c,'')";
                        }
                        if ($cands) $titleExpr = "COALESCE(" . implode(',', $cands) . ", NULLIF(tr.title,''), 'Ticket')";
                    }

                    $rows = $qr->orderByDesc('tr.created_at')
                        ->limit(2000)
                        ->get([
                            'tr.id as report_id',
                            'tr.ticket_id as item_id',
                            DB::raw("$titleExpr as title"),
                            'tr.report',
                            'tr.report_date',
                            DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as report_by_name"),
                            DB::raw("tr.$byCol as report_by_id"),
                            'tr.created_at',
                        ]);

                    $items = $items->merge($rows->map(function ($r) use ($pack) {
                        $itemId = (int)($r->item_id ?? 0);
                        return $pack(
                            'ticket',
                            $itemId,
                            (int)($r->report_id ?? 0),
                            (string)($r->title ?? 'Ticket'),
                            (string)($r->report ?? ''),
                            (string)($r->created_at ?? ''),
                            $r->report_date ? (string)$r->report_date : null,
                            null,
                            '',
                            (int)($r->report_by_id ?? 0),
                            (string)($r->report_by_name ?? ''),
                            $itemId > 0 ? url("/problem/profile/{$itemId}") : null
                        );
                    }));
                }
            }

            /* =========================================================================
            CUSTOMER_REPORTS (LEADS)
            ========================================================================= */
            if ($this->hasTable('customer_reports') && in_array('lead', $types, true)) {
                $byCol = $this->hasColumn('customer_reports', 'report_by')
                    ? 'report_by'
                    : ($this->hasColumn('customer_reports', 'employee_id') ? 'employee_id' : null);

                if ($byCol) {
                    $qr = DB::table('customer_reports as cr')
                        ->when($this->hasColumn('customer_reports', 'deleted_at'), fn($x) => $x->whereNull('cr.deleted_at'))
                        ->whereBetween('cr.created_at', [$from, $to]);

                    if ($employeeId > 0) $qr->where("cr.$byCol", $employeeId);

                    if ($q !== '') {
                        $qr->where(function ($w) use ($q) {
                            $w->orWhere('cr.report', 'like', "%{$q}%")
                            ->orWhere('cr.stage', 'like', "%{$q}%");
                        });
                    }

                    $qr->leftJoin('employees as e', 'e.id', '=', "cr.$byCol");

                    $titleExpr = "'Lead'";
                    if ($this->hasTable('new_leads')) {
                        $qr->leftJoin('new_leads as nl', 'nl.id', '=', 'cr.customer_id');
                        if ($this->hasColumn('new_leads', 'deleted_at')) $qr->whereNull('nl.deleted_at');

                        $titleExpr = "COALESCE(NULLIF(nl.firma,''), NULLIF(TRIM(CONCAT(COALESCE(nl.lastname,''),' ',COALESCE(nl.name,''))), ''), 'Lead')";
                    }

                    if ($this->hasTable('article_groups') && $this->hasColumn('customer_reports', 'product_id')) {
                        $qr->leftJoin('article_groups as ag', 'ag.id', '=', 'cr.product_id');
                        if ($this->hasColumn('article_groups', 'deleted_at')) $qr->whereNull('ag.deleted_at');
                        $titleExpr = "TRIM(CONCAT($titleExpr, CASE WHEN ag.article_group IS NULL OR ag.article_group='' THEN '' ELSE CONCAT(' • ', ag.article_group) END))";
                    }

                    $rows = $qr->orderByDesc('cr.created_at')
                        ->limit(2000)
                        ->get([
                            'cr.id as report_id',
                            'cr.customer_id as item_id',
                            DB::raw("$titleExpr as title"),
                            'cr.report',
                            DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as report_by_name"),
                            DB::raw("cr.$byCol as report_by_id"),
                            'cr.created_at',
                        ]);

                    $items = $items->merge($rows->map(function ($r) use ($pack) {
                        $customerId = (int)($r->item_id ?? 0);
                        return $pack(
                            'lead',
                            $customerId,
                            (int)($r->report_id ?? 0),
                            (string)($r->title ?? 'Lead'),
                            (string)($r->report ?? ''),
                            (string)($r->created_at ?? ''),
                            null,
                            null,
                            '',
                            (int)($r->report_by_id ?? 0),
                            (string)($r->report_by_name ?? ''),
                            $customerId > 0 ? url("/new_lead_profile/{$customerId}") : null
                        );
                    }));
                }
            }

            $items = $items->filter()->values();

            // sort
            $items = match ($sort) {
                'oldest' => $items->sortBy('created_at')->values(),
                default  => $items->sortByDesc('created_at')->values(),
            };

            $total = $items->count();
            $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

            return response()->json([
                // keep both keys
                'items' => $slice,
                'rows'  => $slice,

                'pagination' => [
                    'page'     => $page,
                    'per_page' => $perPage,
                    'total'    => $total,
                    'has_more' => ($page * $perPage) < $total,
                ],

                'meta' => [
                    'can_view_all' => $canViewAll,
                    'employee_id'  => $employeeId,
                    'date_from'    => $from->toDateString(),
                    'date_to'      => $to->toDateString(),
                ],
            ]);
        }

    public function recentReportsExport(Request $request)
    {
        $request->merge(['page' => 1, 'per_page' => 1000]);
        return $this->recentReportsFetch($request);
    }

    /* =========================================================================
       HELPERS
       ========================================================================= */

    private function normalizeTypes($types): array
    {
        if (is_array($types)) {
            $types = array_values(array_filter($types, fn($v) => is_string($v) && trim($v) !== ''));
            $out = array_values(array_intersect($types, self::TYPES));
            return $out ?: self::TYPES;
        }

        if (is_string($types)) {
            $try = json_decode($types, true);
            if (is_array($try)) {
                $try = array_values(array_filter($try, fn($v) => is_string($v) && trim($v) !== ''));
                $out = array_values(array_intersect($try, self::TYPES));
                return $out ?: self::TYPES;
            }
            $parts = array_filter(array_map('trim', explode(',', $types)));
            $out = array_values(array_intersect($parts, self::TYPES));
            return $out ?: self::TYPES;
        }

        return self::TYPES;
    }

    private function parseJsonHistory($raw): array
    {
        if (!$raw) return [];

        $arr = is_string($raw) ? json_decode($raw, true) : $raw;
        if (!is_array($arr)) return [];

        // remove "deleted" entries (common keys across your histories)
        return array_values(array_filter($arr, function ($x) {
            if (!is_array($x)) return true;

            // any of these means "this entry is deleted"
            $flags = [
                'deleted', 'is_deleted', 'isDeleted', 'removed', 'is_removed',
                'deleted_at', 'deletedAt',
                'is_archived', 'archived', 'archived_at',
                'action', 'event', 'type',
            ];

            // explicit boolean flags
            foreach (['deleted','is_deleted','isDeleted','removed','is_removed'] as $k) {
                if (array_key_exists($k, $x) && (bool)$x[$k] === true) return false;
            }

            // deleted timestamp
            foreach (['deleted_at','deletedAt','archived_at'] as $k) {
                if (!empty($x[$k])) return false;
            }

            // action/type markers
            foreach (['action','event','type'] as $k) {
                if (!empty($x[$k])) {
                    $v = strtolower(trim((string)$x[$k]));
                    if (in_array($v, ['delete','deleted','remove','removed','archive','archived'], true)) return false;
                }
            }

            return true;
        }));
    }

    private function firstExistingColumn(string $table, array $candidates): ?string
    {
        if (!$this->hasTable($table)) return null;
        foreach ($candidates as $c) {
            if ($this->hasColumn($table, $c)) return $c;
        }
        return null;
    }

    private function cleanText($value): string
    {
        $s = (string)($value ?? '');
        if ($s === '') return '';
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $s = preg_replace('/<\s*br\s*\/?>/i', "\n", $s) ?? $s;
        $s = strip_tags($s);
        $s = preg_replace("/[ \t]+/", ' ', $s) ?? $s;
        $s = preg_replace("/\n{3,}/", "\n\n", $s) ?? $s;
        return trim($s);
    }

    private function hasTable(string $table): bool
    {
        if (array_key_exists($table, self::$schemaCache['hasTable'])) {
            return (bool) self::$schemaCache['hasTable'][$table];
        }
        return (bool) (self::$schemaCache['hasTable'][$table] = Schema::hasTable($table));
    }

    private function hasColumn(string $table, string $column): bool
    {
        $k = $table . ':' . $column;
        if (array_key_exists($k, self::$schemaCache['hasColumn'])) {
            return (bool) self::$schemaCache['hasColumn'][$k];
        }
        $ok = $this->hasTable($table) && Schema::hasColumn($table, $column);
        return (bool) (self::$schemaCache['hasColumn'][$k] = $ok);
    }

    private function supportsJsonSearch(): bool
    {
        try {
            return DB::connection()->getDriverName() === 'mysql';
        } catch (\Throwable $e) {
            return false;
        }
    }
}
