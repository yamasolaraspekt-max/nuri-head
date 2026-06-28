<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\InquiryReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Events\OverdueReportCreated;
use App\Models\OverdueReportRead;
use App\Models\Employee;
use App\Models\AppointmentReport;
use App\Models\PersonalTaskComment;
use App\Models\MainAppointment;
use App\Models\PersonalTask;
use App\Models\Problem;
use App\Models\TicketReport;

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
    private const LIMIT_INQUIRY = 800;
    private const LIMIT_TASK = 800;
    private const LIMIT_APPOINTMENT = 800;
    private const LIMIT_TICKET = 800;
    private const LIMIT_LEAD = 900;

    private const MAX_PER_PAGE = 50;
    private const MIN_PER_PAGE = 6;
    private const DEFAULT_PER_PAGE = 12;

    /**
     * Local cache for Schema checks.
     */
    private static array $schemaCache = [
        'hasTable' => [],
        'hasColumn' => [],
    ];

    /* =========================================================================
       PAGES
       ========================================================================= */

    public function reportIndex()
    {
        $employeeId = $this->currentEmployeeId();

        $employee = null;

        if ($employeeId > 0 && $this->hasTable('employees')) {
            $employee = DB::table('employees')
                ->where('id', $employeeId)
                ->first(['id', 'name', 'lastname']);
        }

        $reportTarget = $this->reportTargetPerson($employeeId);

        return view('admin.report.index', compact(
            'employeeId',
            'employee',
            'reportTarget'
        ));
    }

    public function recentReportsEmployeeSummary(Request $request)
    {
        $threshold = now()->subHours(self::HOURS);

        if (!$this->hasTable('employees')) {
            return response()->json([
                'success' => true,
                'items' => [],
                'totals' => [
                    'total' => 0,
                    'ticket' => 0,
                    'task' => 0,
                    'lead' => 0,
                    'inquiry' => 0,
                    'appointment' => 0,
                ],
            ]);
        }

        $employees = DB::table('employees')
            ->select([
                'id',
                'name',
                'lastname',
                'image',
                'email',
                $this->hasColumn('employees', 'status') ? 'status' : DB::raw("'Active' as status"),
            ])
            ->when($this->hasColumn('employees', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->when($this->hasColumn('employees', 'status'), fn($q) => $q->where('status', 'Active'))
            ->orderBy('name')
            ->orderBy('lastname')
            ->get();

        $items = $employees->map(function ($employee) use ($threshold) {
            $employeeId = (int) $employee->id;

            $counts = [
                'ticket' => $this->remainingTicketCountForEmployee($employeeId, $threshold),
                'task' => $this->remainingTaskCountForEmployee($employeeId, $threshold),
                'lead' => $this->remainingLeadCountForEmployee($employeeId, $threshold),
                'inquiry' => $this->remainingInquiryCountForEmployee($employeeId, $threshold),
                'appointment' => $this->remainingAppointmentCountForEmployee($employeeId, $threshold),
            ];

            $total = array_sum($counts);

            return [
                'id' => $employeeId,
                'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) ?: 'Mitarbeiter #' . $employeeId,
                'email' => $employee->email ?? null,
                'image' => !empty($employee->image)
                    ? asset('images/employee/' . $employee->image)
                    : asset('images/gender/male.png'),
                'counts' => $counts,
                'total' => $total,
            ];
        })
            ->filter(fn($item) => (int) $item['total'] > 0)
            ->sortByDesc('total')
            ->values();

        return response()->json([
            'success' => true,
            'items' => $items,
            'totals' => [
                'total' => $items->sum('total'),
                'ticket' => $items->sum(fn($i) => $i['counts']['ticket']),
                'task' => $items->sum(fn($i) => $i['counts']['task']),
                'lead' => $items->sum(fn($i) => $i['counts']['lead']),
                'inquiry' => $items->sum(fn($i) => $i['counts']['inquiry']),
                'appointment' => $items->sum(fn($i) => $i['counts']['appointment']),
            ],
        ]);
    }

    private function reportTargetPerson(int $employeeId): array
    {
        $empty = [
            'department_id' => null,
            'department_name' => 'Keine Abteilung gefunden',
            'head_id' => null,
            'head_name' => 'Kein Abteilungsleiter hinterlegt',
            'head_email' => null,
            'head_image' => asset('images/gender/male.png'),
            'is_self_head' => false,
        ];

        if ($employeeId <= 0) {
            return $empty;
        }

        if (!$this->hasTable('department_positions') || !$this->hasTable('departments')) {
            return $empty;
        }

        $departmentPosition = DB::table('department_positions as dp')
            ->join('departments as d', 'd.id', '=', 'dp.department_id')
            ->where('dp.employee_id', $employeeId)
            ->when($this->hasColumn('departments', 'deleted_at'), fn($q) => $q->whereNull('d.deleted_at'))
            ->orderByRaw("
                CASE
                    WHEN dp.main = 'Yes' THEN 1
                    WHEN dp.main = 'yes' THEN 1
                    WHEN dp.main = '1' THEN 1
                    WHEN dp.main = 1 THEN 1
                    ELSE 0
                END DESC
            ")
            ->orderByDesc('dp.id')
            ->first([
                'dp.department_id',
                'd.department_name',
                'd.department_head',
                'd.head_representative',
            ]);

        if (!$departmentPosition) {
            return $empty;
        }

        $headId = (int) ($departmentPosition->department_head ?? 0);
        $representativeId = (int) ($departmentPosition->head_representative ?? 0);

        /*
         * If the employee is himself the head, use representative if available.
         */
        $isSelfHead = $headId === $employeeId;

        if ($isSelfHead && $representativeId > 0 && $representativeId !== $employeeId) {
            $headId = $representativeId;
        }

        if ($headId <= 0 && $representativeId > 0) {
            $headId = $representativeId;
        }

        $head = null;

        if ($headId > 0 && $this->hasTable('employees')) {
            $head = DB::table('employees')
                ->where('id', $headId)
                ->first([
                    'id',
                    'name',
                    'lastname',
                    'email',
                    'image',
                ]);
        }

        $headName = $head
            ? trim(($head->name ?? '') . ' ' . ($head->lastname ?? ''))
            : '';

        return [
            'department_id' => (int) $departmentPosition->department_id,
            'department_name' => $departmentPosition->department_name ?: 'Abteilung',
            'head_id' => $head ? (int) $head->id : null,
            'head_name' => $headName ?: 'Kein Abteilungsleiter hinterlegt',
            'head_email' => $head->email ?? null,
            'head_image' => !empty($head?->image)
                ? asset('images/employee/' . $head->image)
                : asset('images/gender/male.png'),
            'is_self_head' => $isSelfHead,
        ];
    }
    public function reportNotifications(Request $request)
    {
        $employeeId = $this->currentEmployeeId();

        if ($employeeId <= 0) {
            return response()->json([
                'count' => 0,
                'items' => [],
            ]);
        }

        $rows = DB::table('overdue_reports as r')
            ->leftJoin('overdue_report_reads as rr', function ($join) use ($employeeId) {
                $join->on('rr.report_id', '=', 'r.id')
                    ->where('rr.employee_id', '=', $employeeId);
            })
            ->leftJoin('employees as e', 'e.id', '=', 'r.employee_id')
            ->whereNull('r.deleted_at')
            ->select([
                'r.id',
                'r.type',
                'r.target_id',
                'r.report',
                'r.employee_id',
                'r.created_at',
                'rr.read_at',
                'e.name as employee_name',
                'e.lastname as employee_lastname',
                'e.image as employee_image',
            ])
            ->orderByDesc('r.created_at')
            ->limit(15)
            ->get()
            ->map(function ($row) {
                $employeeName = trim(($row->employee_name ?? '') . ' ' . ($row->employee_lastname ?? ''));

                $target = $this->reportTargetDetails(
                    (string) $row->type,
                    (int) $row->target_id
                );

                return [
                    'id' => (int) $row->id,
                    'type' => (string) $row->type,
                    'target_id' => (int) $row->target_id,

                    'title' => $target['title'],
                    'subtitle' => $target['subtitle'],
                    'target_label' => $target['target_label'],
                    'target_no' => $target['target_no'],
                    'target_status' => $target['status'],
                    'target_priority' => $target['priority'],
                    'target_customer' => $target['customer'],
                    'target_date' => $target['date'],
                    'target_link' => $target['link'],
                    'target_exists' => $target['exists'],

                    'report' => $row->report,
                    'employee_id' => (int) $row->employee_id,
                    'employee' => $employeeName ?: 'Unbekannt',
                    'employee_image' => $row->employee_image
                        ? asset('images/employee/' . $row->employee_image)
                        : asset('images/gender/male.png'),

                    'created_at' => $row->created_at,
                    'created_human' => $row->created_at
                        ? Carbon::parse($row->created_at)->diffForHumans()
                        : 'gerade eben',

                    'read_at' => $row->read_at,
                    'is_unread' => empty($row->read_at),
                ];
            });

        return response()->json([
            'count' => $rows->where('is_unread', true)->count(),
            'items' => $rows,
        ]);
    }


    private function reportTargetDetails(?string $type, int $id): array
    {
        $type = strtolower(trim((string) $type));

        $fallback = [
            'exists' => false,
            'title' => $this->reportTargetTitle($type, $id),
            'subtitle' => '',
            'target_label' => $this->typeLabelDe($type ?: 'report'),
            'target_no' => $id > 0 ? '#' . $id : '',
            'status' => '',
            'priority' => '',
            'customer' => '',
            'date' => '',
            'link' => null,
        ];

        if (!$type || $id <= 0) {
            return $fallback;
        }

        try {
            return match ($type) {
                'appointment' => $this->reportTargetAppointmentDetails($id, $fallback),
                'task' => $this->reportTargetTaskDetails($id, $fallback),
                'ticket',
                'problem' => $this->reportTargetTicketDetails($id, $fallback),
                'inquiry' => $this->reportTargetInquiryDetails($id, $fallback),
                'lead' => $this->reportTargetLeadDetails($id, $fallback),
                default => $fallback,
            };
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    private function reportTargetAppointmentDetails(int $id, array $fallback): array
    {
        if (!$this->hasTable('main_appointments')) {
            return $fallback;
        }

        $row = DB::table('main_appointments as a')
            ->where('a.id', $id)
            ->when($this->hasColumn('main_appointments', 'deleted_at'), fn($q) => $q->whereNull('a.deleted_at'))
            ->first([
                'a.id',
                $this->hasColumn('main_appointments', 'name') ? 'a.name' : DB::raw("NULL as name"),
                $this->hasColumn('main_appointments', 'status') ? 'a.status' : DB::raw("NULL as status"),
                $this->hasColumn('main_appointments', 'priority') ? 'a.priority' : DB::raw("NULL as priority"),
                $this->hasColumn('main_appointments', 'start_date') ? 'a.start_date' : DB::raw("NULL as start_date"),
                $this->hasColumn('main_appointments', 'start_time') ? 'a.start_time' : DB::raw("NULL as start_time"),
                $this->hasColumn('main_appointments', 'end_date') ? 'a.end_date' : DB::raw("NULL as end_date"),
                $this->hasColumn('main_appointments', 'full_address') ? 'a.full_address' : DB::raw("NULL as full_address"),
            ]);

        if (!$row) {
            return $fallback;
        }

        $date = trim(($row->start_date ?? '') . ' ' . ($row->start_time ?? ''));

        return array_merge($fallback, [
            'exists' => true,
            'title' => trim((string) $row->name) ?: 'Termin #' . $id,
            'subtitle' => trim((string) $row->full_address),
            'target_label' => 'Termin',
            'target_no' => '#' . $id,
            'status' => $this->statusLabelDe($row->status ?? ''),
            'priority' => $this->priorityLabelDe($row->priority ?? ''),
            'customer' => '',
            'date' => $date,
            'link' => url("/customer/appointments/{$id}"),
        ]);
    }

    private function reportTargetTaskDetails(int $id, array $fallback): array
    {
        if (!$this->hasTable('personal_tasks')) {
            return $fallback;
        }

        $row = DB::table('personal_tasks as t')
            ->where('t.id', $id)
            ->when($this->hasColumn('personal_tasks', 'deleted_at'), fn($q) => $q->whereNull('t.deleted_at'))
            ->first([
                't.id',
                $this->hasColumn('personal_tasks', 'task_title') ? 't.task_title' : DB::raw("NULL as task_title"),
                $this->hasColumn('personal_tasks', 'title') ? 't.title' : DB::raw("NULL as title"),
                $this->hasColumn('personal_tasks', 'description') ? 't.description' : DB::raw("NULL as description"),
                $this->hasColumn('personal_tasks', 'task_status') ? 't.task_status' : DB::raw("NULL as task_status"),
                $this->hasColumn('personal_tasks', 'status') ? 't.status' : DB::raw("NULL as status"),
                $this->hasColumn('personal_tasks', 'priority') ? 't.priority' : DB::raw("NULL as priority"),
                $this->hasColumn('personal_tasks', 'due_date') ? 't.due_date' : DB::raw("NULL as due_date"),
                $this->hasColumn('personal_tasks', 'due_time') ? 't.due_time' : DB::raw("NULL as due_time"),
            ]);

        if (!$row) {
            return $fallback;
        }

        $title = trim((string) ($row->task_title ?? $row->title ?? ''));

        return array_merge($fallback, [
            'exists' => true,
            'title' => $title ?: 'Aufgabe #' . $id,
            'subtitle' => Str::limit($this->cleanText($row->description ?? ''), 140),
            'target_label' => 'Aufgabe',
            'target_no' => '#' . $id,
            'status' => $this->statusLabelDe($row->task_status ?? $row->status ?? ''),
            'priority' => $this->priorityLabelDe($row->priority ?? ''),
            'customer' => '',
            'date' => trim(($row->due_date ?? '') . ' ' . ($row->due_time ?? '')),
            'link' => url("/personal-tasks/{$id}/profile"),
        ]);
    }

    private function reportTargetTicketDetails(int $id, array $fallback): array
    {
        if (!$this->hasTable('problems')) {
            return $fallback;
        }

        $row = DB::table('problems as p')
            ->where('p.id', $id)
            ->when($this->hasColumn('problems', 'deleted_at'), fn($q) => $q->whereNull('p.deleted_at'))
            ->first([
                'p.id',
                $this->hasColumn('problems', 'ticket_no') ? 'p.ticket_no' : DB::raw("NULL as ticket_no"),
                $this->hasColumn('problems', 'article_name') ? 'p.article_name' : DB::raw("NULL as article_name"),
                $this->hasColumn('problems', 'title') ? 'p.title' : DB::raw("NULL as title"),
                $this->hasColumn('problems', 'subject') ? 'p.subject' : DB::raw("NULL as subject"),
                $this->hasColumn('problems', 'problem') ? 'p.problem' : DB::raw("NULL as problem"),
                $this->hasColumn('problems', 'status') ? 'p.status' : DB::raw("NULL as status"),
                $this->hasColumn('problems', 'priority') ? 'p.priority' : DB::raw("NULL as priority"),
                $this->hasColumn('problems', 'date') ? 'p.date' : DB::raw("NULL as date"),
                $this->hasColumn('problems', 'customer_id') ? 'p.customer_id' : DB::raw("NULL as customer_id"),
            ]);

        if (!$row) {
            return $fallback;
        }

        $title = trim((string) ($row->article_name ?? $row->title ?? $row->subject ?? $row->ticket_no ?? ''));

        $customerName = '';
        if (!empty($row->customer_id) && $this->hasTable('new_leads')) {
            $customer = DB::table('new_leads')
                ->where('id', $row->customer_id)
                ->first([
                    'id',
                    $this->hasColumn('new_leads', 'firma') ? 'firma' : DB::raw("NULL as firma"),
                    $this->hasColumn('new_leads', 'name') ? 'name' : DB::raw("NULL as name"),
                    $this->hasColumn('new_leads', 'lastname') ? 'lastname' : DB::raw("NULL as lastname"),
                ]);

            if ($customer) {
                $customerName = trim(($customer->firma ?? '') ?: (($customer->lastname ?? '') . ' ' . ($customer->name ?? '')));
            }
        }

        return array_merge($fallback, [
            'exists' => true,
            'title' => $title ?: 'Ticket #' . $id,
            'subtitle' => Str::limit($this->cleanText($row->problem ?? ''), 140),
            'target_label' => 'Ticket',
            'target_no' => $row->ticket_no ? (string) $row->ticket_no : '#' . $id,
            'status' => $this->statusLabelDe($row->status ?? ''),
            'priority' => $this->priorityLabelDe($row->priority ?? ''),
            'customer' => $customerName,
            'date' => (string) ($row->date ?? ''),
            'link' => url("/problem/profile/{$id}"),
        ]);
    }

    private function reportTargetInquiryDetails(int $id, array $fallback): array
    {
        if (!$this->hasTable('inquiries')) {
            return $fallback;
        }

        $row = DB::table('inquiries as i')
            ->where('i.id', $id)
            ->when($this->hasColumn('inquiries', 'deleted_at'), fn($q) => $q->whereNull('i.deleted_at'))
            ->first([
                'i.id',
                $this->hasColumn('inquiries', 'title') ? 'i.title' : DB::raw("NULL as title"),
                $this->hasColumn('inquiries', 'firma') ? 'i.firma' : DB::raw("NULL as firma"),
                $this->hasColumn('inquiries', 'name') ? 'i.name' : DB::raw("NULL as name"),
                $this->hasColumn('inquiries', 'lastname') ? 'i.lastname' : DB::raw("NULL as lastname"),
                $this->hasColumn('inquiries', 'next_step') ? 'i.next_step' : DB::raw("NULL as next_step"),
                $this->hasColumn('inquiries', 'status') ? 'i.status' : DB::raw("NULL as status"),
                $this->hasColumn('inquiries', 'periority') ? 'i.periority' : DB::raw("NULL as periority"),
                $this->hasColumn('inquiries', 'created_at') ? 'i.created_at' : DB::raw("NULL as created_at"),
            ]);

        if (!$row) {
            return $fallback;
        }

        $customerName = trim(($row->firma ?? '') ?: (($row->lastname ?? '') . ' ' . ($row->name ?? '')));
        $title = trim((string) ($row->title ?? ''));

        return array_merge($fallback, [
            'exists' => true,
            'title' => $title ?: ($customerName ?: 'Anfrage #' . $id),
            'subtitle' => Str::limit($this->cleanText($row->next_step ?? ''), 140),
            'target_label' => 'Anfrage',
            'target_no' => '#' . $id,
            'status' => $this->statusLabelDe($row->status ?? ''),
            'priority' => $this->priorityLabelDe($row->periority ?? ''),
            'customer' => $customerName,
            'date' => (string) ($row->created_at ?? ''),
            'link' => url("/inquiry_show/{$id}"),
        ]);
    }

    private function reportTargetLeadDetails(int $id, array $fallback): array
    {
        if (!$this->hasTable('lead_product_lists')) {
            return $fallback;
        }

        $row = DB::table('lead_product_lists as lpl')
            ->leftJoin('new_leads as nl', 'nl.id', '=', 'lpl.customer_id')
            ->leftJoin('lead_alternative_adds as laa', 'laa.id', '=', 'lpl.alternative_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
            ->where('lpl.id', $id)
            ->when($this->hasColumn('lead_product_lists', 'deleted_at'), fn($q) => $q->whereNull('lpl.deleted_at'))
            ->first([
                'lpl.id',
                'lpl.customer_id',
                $this->hasColumn('lead_product_lists', 'status') ? 'lpl.status' : DB::raw("NULL as status"),
                $this->hasColumn('lead_product_lists', 'stage') ? 'lpl.stage' : DB::raw("NULL as stage"),
                $this->hasColumn('lead_product_lists', 'updated_at') ? 'lpl.updated_at' : DB::raw("NULL as updated_at"),
                DB::raw("COALESCE(nl.firma, TRIM(CONCAT(COALESCE(nl.lastname,''),' ',COALESCE(nl.name,''))), '') as customer_name"),
                DB::raw("COALESCE(laa.object_name, '') as object_name"),
                DB::raw("COALESCE(ag.article_group, '') as product_name"),
            ]);

        if (!$row) {
            return $fallback;
        }

        $customerName = trim((string) $row->customer_name);
        $productName = trim((string) $row->product_name);
        $objectName = trim((string) $row->object_name);

        return array_merge($fallback, [
            'exists' => true,
            'title' => $customerName ?: 'Lead #' . $id,
            'subtitle' => implode(' • ', array_filter([$productName, $objectName])),
            'target_label' => 'Lead',
            'target_no' => '#' . $id,
            'status' => $this->statusLabelDe($row->status ?? $row->stage ?? ''),
            'priority' => 'Normal',
            'customer' => $customerName,
            'date' => (string) ($row->updated_at ?? ''),
            'link' => $row->customer_id ? url("/new_lead_profile/{$row->customer_id}") : null,
        ]);
    }

    public function markReportNotificationRead($report)
    {
        OverdueReportRead::updateOrCreate(
            [
                'report_id' => $report,
                'employee_id' => auth()->user()->name,
            ],
            [
                'read_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
        ]);
    }

    public function markAllReportNotificationsRead()
    {
        $employeeId = auth()->user()->name;

        $unreadReportIds = DB::table('overdue_reports')
            ->whereNull('deleted_at')
            ->pluck('id');

        foreach ($unreadReportIds as $reportId) {
            OverdueReportRead::updateOrCreate(
                [
                    'report_id' => $reportId,
                    'employee_id' => $employeeId,
                ],
                [
                    'read_at' => now(),
                ]
            );
        }

        return response()->json([
            'success' => true,
        ]);
    }

    private function reportTargetTitle(?string $type, $id): string
    {
        if (!$type || !$id) {
            return 'Bericht';
        }

        return match ($type) {
            'inquiry' => 'Anfrage #' . $id,
            'task' => 'Aufgabe #' . $id,
            'appointment' => 'Termin #' . $id,
            'ticket' => 'Ticket #' . $id,
            'lead' => 'Lead #' . $id,
            default => ucfirst($type) . ' #' . $id,
        };
    }

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


    private function applyReminderHiding(Collection $items, int $employeeId): Collection
    {
        if ($employeeId <= 0)
            return $items;
        if (!$this->hasTable('reminders'))
            return $items;
        if ($items->isEmpty())
            return $items;

        $now = now();

        // Build ids-per-type from current results (limits the reminder query)
        $byType = [];
        foreach ($items as $it) {
            $t = (string) ($it['type'] ?? '');
            $id = (int) ($it['id'] ?? 0);
            if ($t === '' || $id <= 0)
                continue;
            $byType[$t] ??= [];
            $byType[$t][$id] = true; // unique
        }

        if (empty($byType))
            return $items;

        // Find reminders that should HIDE items (future reminders)
        $q = DB::table('reminders')
            ->whereNull('deleted_at')
            ->where('employee_id', $employeeId)
            ->whereIn('status', ['active', 'snoozed'])
            ->where('next_remind_at', '>', $now);

        // Apply entity filters efficiently (OR groups per type)
        $q->where(function ($w) use ($byType) {
            foreach ($byType as $type => $idsAssoc) {
                $ids = array_keys($idsAssoc);
                if (empty($ids))
                    continue;
                $w->orWhere(function ($x) use ($type, $ids) {
                    $x->where('entity_type', $type)->whereIn('entity_id', $ids);
                });
            }
        });

        $hiddenKeys = $q->get(['entity_type', 'entity_id'])
            ->mapWithKeys(fn($r) => [((string) $r->entity_type) . ':' . ((int) $r->entity_id) => true])
            ->all();

        if (!$hiddenKeys)
            return $items;

        // Remove hidden items
        return $items->reject(function ($it) use ($hiddenKeys) {
            $key = (string) ($it['type'] ?? '') . ':' . (int) ($it['id'] ?? 0);
            return isset($hiddenKeys[$key]);
        })->values();
    }

    /**
     * Keep as-is: shows all employees (if admin) and all reports center.
     */
    public function recentReportsIndex()
    {
        $employees = collect();
        $customers = collect();
        $products = collect();
        $departments = collect();

        // FIX P0-12: employeeId real setzen; "Alles sehen" nur fuer Admin oder Mitarbeiter-Leserecht
        // (vorher hart canViewAll=true + employeeId=0 -> jeder Auth-User sah volle MA-/Kundenliste).
        $employeeId = $this->currentEmployeeId();
        $canViewAll = (bool) auth()->user()->is_admin
            || (bool) auth()->user()->hasPermission('Employee', 'read');

        if ($canViewAll && $this->hasTable('employees')) {
            $employees = DB::table('employees')
                ->select('id', 'name', 'lastname')
                ->orderBy('name')
                ->orderBy('lastname')
                ->get();
        }

        if ($canViewAll && $this->hasTable('new_leads')) {
            $customers = DB::table('new_leads')
                ->when($this->hasColumn('new_leads', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->select([
                    'id',
                    'customer_no',
                    'firma',
                    'name',
                    'lastname',
                ])
                ->orderByRaw("COALESCE(NULLIF(firma,''), lastname, name, customer_no) ASC")
                ->limit(1000)
                ->get();
        }

        if ($canViewAll && $this->hasTable('article_groups')) {
            $products = DB::table('article_groups')
                ->when($this->hasColumn('article_groups', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->select('id', 'article_group', 'initial')
                ->orderBy('article_group')
                ->get();
        }

        if ($canViewAll && $this->hasTable('departments')) {
            $departments = DB::table('departments')
                ->when($this->hasColumn('departments', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->select('id', 'department_name')
                ->orderBy('department_name')
                ->get();
        }

        return view('admin.dashboard.employee.partials.recent_reports_center', [
            'employees' => $employees,
            'customers' => $customers,
            'products' => $products,
            'departments' => $departments,
            'employeeId' => $employeeId,
            'canViewAll' => $canViewAll,
        ]);
    }

    /* =========================================================================
       BULK REPORT STORE
       ========================================================================= */

    public function reportBulkStore(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer'],
            'items.*.type' => ['required', 'string'],
            'report' => ['required', 'string', 'min:3'],
        ]);

        $items = $request->input('items');
        $employeeId = $this->currentEmployeeId();

        if ($employeeId <= 0) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                $type = $item['type'];
                $id = (int) $item['id'];

                // Security: Ensure user is actually allowed to edit this item
                // This throws 403 if not involved, which triggers rollback via catch
                $this->abortIfNotInvolved($type, $id);

                // Reuse existing single-store logic. 
                // We pass $request because it already contains the 'report' text.
                // The last argument 'false' means "not skipped".
                match ($type) {
                    'appointment' => $this->storeReportAppointment($id, $employeeId, $request, false),
                    'task' => $this->storeReportTask($id, $employeeId, $request, false),
                    'ticket' => $this->storeReportTicket($id, $employeeId, $request, false),
                    'inquiry' => $this->storeReportInquiry($id, $employeeId, $request, false),
                    'lead' => $this->storeReportLeadProduct($id, $employeeId, $request, false),
                    default => null,
                };
            }

            DB::commit();

            return response()->json(['ok' => true, 'count' => count($items)]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Bulk Error: ' . $e->getMessage()
            ], 500);
        }
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
        $threshold = now()->subHours(self::HOURS);
        $employeeId = $this->currentEmployeeId();

        if ($employeeId <= 0) {
            return response()->json([
                'items' => [],
                'stats' => ['total' => 0, 'inquiry' => 0, 'task' => 0, 'appointment' => 0, 'ticket' => 0, 'lead' => 0],
                'pagination' => ['page' => 1, 'per_page' => self::DEFAULT_PER_PAGE, 'total' => 0, 'has_more' => false],
            ]);
        }

        $q = trim((string) $request->get('q', ''));
        $types = $this->normalizeTypes($request->input('types', self::TYPES));
        $status = $request->get('status');
        $prio = $request->get('priority');
        $branch = $request->get('branch_id');

        $sort = (string) $request->get('sort', 'oldest');
        $page = max(1, (int) $request->get('page', 1));
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
        $items = $this->applyReminderHiding($items, $employeeId);


        $items = match ($sort) {
            'newest' => $items->sortByDesc('last_activity_at')->values(),
            'most_overdue' => $items->sortByDesc('overdue_hours')->values(),
            default => $items->sortBy('last_activity_at')->values(),
        };

        $total = $items->count();
        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        $stats = [
            'total' => $total,
            'inquiry' => $items->where('type', 'inquiry')->count(),
            'task' => $items->where('type', 'task')->count(),
            'appointment' => $items->where('type', 'appointment')->count(),
            'ticket' => $items->where('type', 'ticket')->count(),
            'lead' => $items->where('type', 'lead')->count(),
        ];

        $reportTarget = $this->reportTargetPerson($employeeId);
        return response()->json([
            'items' => $slice,
            'stats' => $stats,
            'report_to' => $reportTarget,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
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
        $id = (int) $request->get('id');

        if (!in_array($type, self::TYPES, true)) {
            return response()->json(['message' => 'Unknown type'], 422);
        }

        $this->abortIfNotInvolved($type, $id);

        return match ($type) {
            'lead' => $this->historyLeadProduct($id),
            'task' => $this->historyTask($id),
            'appointment' => $this->historyAppointment($id),
            'inquiry' => $this->historyInquiry($id),
            'ticket' => $this->historyTicket($id),
            default => response()->json(['message' => 'Unknown type'], 422),
        };
    }

    public function reportsList(Request $request)
    {
        $type = $request->string('type')->toString();
        $id = (int) $request->get('id');

        if (!in_array($type, self::TYPES, true)) {
            return response()->json(['message' => 'Unknown type'], 422);
        }

        $this->abortIfNotInvolved($type, $id);

        // ONLY MY reports
        return match ($type) {
            'appointment' => $this->reportsAppointment($id),
            'task' => $this->reportsTask($id),
            'ticket' => $this->reportsTicket($id),
            'inquiry' => $this->reportsInquiry($id),
            'lead' => $this->reportsLeadProduct($id),
            default => response()->json(['rows' => []]),
        };
    }

    /* =========================================================================
       REPORT STORE / SKIP
       ========================================================================= */

    public function reportStore(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', Rule::in(self::TYPES)],
            'id' => ['required', 'integer', 'min:1'],
            'report' => ['required', 'string', 'min:3'],
        ]);

        $type = $request->string('type')->toString();
        $id = (int) $request->get('id');

        $employeeId = $this->currentEmployeeId();

        if ($employeeId <= 0) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->abortIfNotInvolved($type, $id);

        DB::beginTransaction();

        try {
            $response = match ($type) {
                'appointment' => $this->storeReportAppointment($id, $employeeId, $request, false),
                'task' => $this->storeReportTask($id, $employeeId, $request, false),
                'ticket' => $this->storeReportTicket($id, $employeeId, $request, false),
                'inquiry' => $this->storeReportInquiry($id, $employeeId, $request, false),
                'lead' => $this->storeReportLeadProduct($id, $employeeId, $request, false),
                default => response()->json(['message' => 'Unknown type'], 422),
            };

            /**
             * Optional notification entry.
             * This keeps your notification center working,
             * but the real report is now saved first.
             */
            $notification = $this->createOverdueReportNotification(
                $type,
                $id,
                $employeeId,
                (string) $request->input('report')
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'ok' => true,
                'message' => 'Bericht gespeichert.',
                'notification_id' => $notification?->id,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Report Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function createOverdueReportNotification(string $type, int $targetId, int $employeeId, string $report): ?\App\Models\OverdueReport
    {
        if (!$this->hasTable('overdue_reports')) {
            return null;
        }

        $notification = \App\Models\OverdueReport::create([
            'type' => $type,
            'target_id' => $targetId,
            'report' => $report,
            'employee_id' => $employeeId,
        ]);

        if ($this->hasTable('overdue_report_reads')) {
            $employees = Employee::query()
                ->where('status', 'Active')
                ->where('id', '!=', $employeeId)
                ->pluck('id');

            foreach ($employees as $empId) {
                OverdueReportRead::firstOrCreate([
                    'report_id' => $notification->id,
                    'employee_id' => $empId,
                ]);
            }
        }

        try {
            broadcast(new OverdueReportCreated($notification))->toOthers();
        } catch (\Throwable $e) {
            // Do not break report saving if broadcast fails.
        }

        return $notification;
    }
    public function reportSkip(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', Rule::in(self::TYPES)],
            'id' => ['required', 'integer', 'min:1'],
            'skip_reason' => ['required', 'string', 'min:3', 'max:1000'],
            'due_date' => ['nullable', 'date'],
            'report_date' => ['nullable', 'date'],
        ]);

        $type = $request->string('type')->toString();
        $id = (int) $request->get('id');

        $employeeId = $this->currentEmployeeId();
        if ($employeeId <= 0)
            abort(403);

        $this->abortIfNotInvolved($type, $id);

        $request->merge(['report' => self::SKIP_TEXT]);

        return match ($type) {
            'appointment' => $this->storeReportAppointment($id, $employeeId, $request, true),
            'task' => $this->storeReportTask($id, $employeeId, $request, true),
            'ticket' => $this->storeReportTicket($id, $employeeId, $request, true),
            'inquiry' => $this->storeReportInquiry($id, $employeeId, $request, true),
            'lead' => $this->storeReportLeadProduct($id, $employeeId, $request, true),
            default => response()->json(['message' => 'Unknown type'], 422),
        };
    }

    /* =========================================================================
       OVERDUE QUERIES (ASSIGNED TO ME + NOT YET REPORTED BY ME)
       ========================================================================= */

    private function overdueInquiries(Carbon $threshold, string $q, $status, $prio, $branch, int $emp)
    {
        if (!$this->hasTable('inquiries') || !$this->hasTable('inquiry_product_lists'))
            return collect();

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
                if (!$any)
                    $w->whereRaw('1=0');
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
                    if ($this->hasColumn('inquiries', $col))
                        $w->orWhere("i.$col", 'like', "%{$q}%");
                }
            });
        }

        if ($status && $this->hasColumn('inquiries', 'status'))
            $base->where('i.status', $status);
        if ($prio && $this->hasColumn('inquiries', 'periority'))
            $base->where('i.periority', $prio);
        if ($branch && $this->hasColumn('inquiries', 'branch_id'))
            $base->where('i.branch_id', $branch);

        $rows = $base->limit(self::LIMIT_INQUIRY)->get();

        // preload wanted products (max 3) for this inquiry (all products)
        $wantedByInquiryId = [];
        if ($this->hasTable('article_groups') && $this->hasColumn('inquiry_product_lists', 'product_id')) {
            $inqIds = $rows->pluck('id')->map(fn($x) => (int) $x)->filter()->values()->all();
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
                    $iid = (int) ($pr->inquiry_id ?? 0);
                    $pname = trim((string) ($pr->article_group ?? ''));
                    if ($iid <= 0 || $pname === '')
                        continue;
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

            if ($lastAt->gt($threshold))
                return null;

            $dueAt = $lastAt->copy()->addHours(self::HOURS);
            $overdueHours = max(0, $dueAt->diffInHours(now(), false));

            $products = $wantedByInquiryId[(int) $r->id] ?? [];
            $prodLine = !empty($products) ? implode(', ', $products) : '';

            $subtitleParts = [];
            if ($prodLine !== '')
                $subtitleParts[] = $prodLine;

            $sub = $this->cleanText($r->subtitle ?? '');
            if ($sub !== '')
                $subtitleParts[] = Str::limit($sub, 120);

            return [
                'type' => 'inquiry',
                'id' => (int) $r->id,
                'title' => trim((string) $r->title) ?: 'Anfrage',
                'subtitle' => implode(' • ', $subtitleParts),
                'status_raw' => (string) ($r->status ?? 'open'),
                'status' => $this->statusLabelDe($r->status ?? 'open'),
                'priority_raw' => (string) ($r->priority ?? 'normal'),
                'priority' => $this->priorityLabelDe($r->priority ?? 'normal'),
                'due_at' => $dueAt->toDateTimeString(),
                'last_activity_at' => $lastAt->toDateTimeString(),
                'overdue_hours' => (int) $overdueHours,
                'progress_pct' => 25,
                'changed_summary' => 'Keine Änderung / kein Bericht seit 48h',
                'link' => url("/inquiry_show/{$r->id}"),
                'meta' => [],
            ];
        })->filter()->values();
    }

    private function overdueTasks(Carbon $threshold, string $q, $status, $prio, $branch, int $emp)
    {
        if (!$this->hasTable('personal_tasks') || !$this->hasTable('employees_personal_tasks'))
            return collect();

        // Assignment subquery with "Reject" check
        $eptSub = DB::table('employees_personal_tasks as ept')
            ->where('ept.employee_id', $emp)
            // Rule: Exclude if assignment is rejected
            ->whereNotIn('ept.status', ['reject', 'rejected'])
            ->groupBy('ept.task_id')
            ->select(['ept.task_id', DB::raw('MAX(ept.created_at) as my_last_change_at')]);

        $base = DB::table('personal_tasks as t')
            ->joinSub($eptSub, 'eptx', fn($j) => $j->on('eptx.task_id', '=', 't.id'))
            // Rule: Exclude soft-deleted tasks
            ->whereNull('t.deleted_at')
            // Rule: Exclude Canceled tasks
            ->where(function ($qq) {
                $col = $this->hasColumn('personal_tasks', 'task_status') ? 'task_status' : 'status';
                if ($this->hasColumn('personal_tasks', $col))
                    $qq->where("t.$col", '!=', 'canceled');
            })
            ->select(['t.id', 't.task_title', 't.description', 't.task_status', 't.priority', 't.updated_at', 't.created_at', 't.due_date', 't.due_time', 't.start_date']);

        // Exclude tasks that already have any comment/report from this employee
        if ($this->hasTable('personal_task_comments')) {
            $base->whereNotExists(function ($query) use ($emp) {
                $query->select(DB::raw(1))
                    ->from('personal_task_comments as ptc')
                    ->whereColumn('ptc.task_id', 't.id')
                    ->where('ptc.comment_by', $emp)
                    ->when(
                        $this->hasColumn('personal_task_comments', 'deleted_at'),
                        fn($q) => $q->whereNull('ptc.deleted_at')
                    );
            });
        }

        return $base->limit(self::LIMIT_TASK)->get()->map(function ($r) use ($threshold) {
            $point = $r->due_date ? Carbon::parse($r->due_date . ' ' . ($r->due_time ?? '23:59:59')) : Carbon::parse($r->start_date ?? $r->updated_at);
            if ($point->gt($threshold))
                return null;

            $dueAt = $point->copy()->addHours(self::HOURS);
            return [
                'type' => 'task',
                'id' => (int) $r->id,
                'title' => $r->task_title ?: 'Aufgabe',
                'subtitle' => Str::limit($this->cleanText($r->description), 140),
                'status_raw' => (string) ($r->task_status ?: 'open'),
                'status' => $this->statusLabelDe($r->task_status ?: 'open'),
                'priority_raw' => (string) ($r->priority ?: 'normal'),
                'priority' => $this->priorityLabelDe($r->priority ?: 'normal'),
                'due_at' => $dueAt->toDateTimeString(),
                'last_activity_at' => $point->toDateTimeString(),
                'overdue_hours' => (int) max(0, $dueAt->diffInHours(now(), false)),
                'progress_pct' => 30,
                'link' => url("/personal-tasks/{$r->id}/profile")
            ];
        })->filter()->values();
    }

    private function overdueAppointments(Carbon $threshold, string $q, $status, $prio, $branch, int $emp)
    {
        if (!$this->hasTable('main_appointments') || !$this->hasTable('main_appointment_employees'))
            return collect();

        $maeSub = DB::table('main_appointment_employees as mae')
            ->where('mae.employee_id', $emp)
            // Rule: Assignment must not be rejected
            ->whereNotIn('mae.status', ['reject', 'rejected'])
            ->groupBy('mae.appointment_id')
            ->select(['mae.appointment_id', DB::raw('MAX(mae.updated_at) as my_last_assign_at')]);

        $base = DB::table('main_appointments as a')
            ->joinSub($maeSub, 'maex', fn($j) => $j->on('maex.appointment_id', '=', 'a.id'))
            // Rule: Appointment record not deleted
            ->whereNull('a.deleted_at')
            ->select(['a.id', 'a.name', 'a.status', 'a.priority', 'a.updated_at', 'a.created_at', 'a.start_date', 'a.end_date', 'a.start_time', 'a.end_time', 'a.full_address']);

        // Exclude if report exists
        if ($this->hasTable('appointment_reports')) {
            $base->whereNotExists(function ($query) use ($emp) {
                $query->select(DB::raw(1))->from('appointment_reports as ar')
                    ->whereColumn('ar.appointment_id', 'a.id')->where('ar.employee_id', $emp);
            });
        }

        return $base->limit(self::LIMIT_APPOINTMENT)->get()->map(function ($r) use ($threshold) {
            $endPoint = $r->end_date ? Carbon::parse($r->end_date . ' ' . ($r->end_time ?? '23:59:59')) : Carbon::parse($r->start_date ?? $r->updated_at);
            if ($endPoint->gt($threshold))
                return null;

            $dueAt = $endPoint->copy()->addHours(self::HOURS);
            return [
                'type' => 'appointment',
                'id' => (int) $r->id,
                'title' => $r->name ?: 'Termin',
                'subtitle' => Str::limit($r->full_address, 160),
                'status' => $r->status ?: 'open',
                'priority' => $r->priority ?: 'normal',
                'due_at' => $dueAt->toDateTimeString(),
                'last_activity_at' => $endPoint->toDateTimeString(),
                'overdue_hours' => (int) max(0, $dueAt->diffInHours(now(), false)),
                'progress_pct' => 40,
                'link' => url("/customer/appointments/{$r->id}")
            ];
        })->filter()->values();
    }
    private function overdueTickets(Carbon $threshold, string $q, $status, $prio, $branch, int $emp)
    {
        if (!$this->hasTable('problems') || !$this->hasTable('employee_problem'))
            return collect();

        $titleCandidates = [];
        foreach (['article_name', 'title', 'subject', 'ticket_no'] as $col) {
            if ($this->hasColumn('problems', $col))
                $titleCandidates[] = "NULLIF(p.$col,'')";
        }
        $titleExpr = $titleCandidates
            ? "COALESCE(" . implode(',', $titleCandidates) . ", 'Ticket')"
            : "'Ticket'";

        $subCandidates = [];
        foreach (['problem', 'progress', 'solution'] as $col) {
            if ($this->hasColumn('problems', $col))
                $subCandidates[] = "NULLIF(p.$col,'')";
        }
        $subExpr = $subCandidates
            ? "COALESCE(" . implode(',', $subCandidates) . ", '')"
            : "''";

        $statusSql = $this->hasColumn('problems', 'status') ? "COALESCE(p.status,'open')" : "'open'";
        $prioritySql = $this->hasColumn('problems', 'priority') ? "COALESCE(p.priority,'normal')" : "'normal'";
        $updatedSql = $this->hasColumn('problems', 'updated_at') ? "p.updated_at" : "p.created_at";
        $branchSql = $this->hasColumn('problems', 'branch_id') ? "p.branch_id" : "NULL";

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
                    if ($this->hasColumn('problems', $col))
                        $w->orWhere("p.$col", 'like', "%{$q}%");
                }
            });
        }

        if ($status && $this->hasColumn('problems', 'status'))
            $base->where('p.status', $status);
        if ($prio && $this->hasColumn('problems', 'priority'))
            $base->where('p.priority', $prio);
        if ($branch && $this->hasColumn('problems', 'branch_id'))
            $base->where('p.branch_id', $branch);

        $rows = $base->limit(self::LIMIT_TICKET)->get();

        return $rows->map(function ($r) use ($threshold) {
            $lastAt = Carbon::parse($r->updated_at ?? $r->created_at);
            if ($lastAt->gt($threshold))
                return null;

            $dueAt = $lastAt->copy()->addHours(self::HOURS);
            $overdueHours = max(0, $dueAt->diffInHours(now(), false));

            return [
                'type' => 'ticket',
                'id' => (int) $r->id,
                'title' => trim((string) ($r->title ?? '')) ?: 'Ticket',
                'subtitle' => Str::limit($this->cleanText($r->subtitle ?? ''), 140),
                'status' => (string) ($r->status ?? 'open'),
                'priority' => (string) ($r->priority ?? 'normal'),
                'due_at' => $dueAt->toDateTimeString(),
                'last_activity_at' => $lastAt->toDateTimeString(),
                'overdue_hours' => (int) $overdueHours,
                'progress_pct' => 35,
                'changed_summary' => 'Kein Bericht / keine Änderung seit 48h',
                'link' => url("/problem/profile/{$r->id}"),
                'meta' => [],
            ];
        })->filter()->values();
    }

    private function overdueLeadProducts(Carbon $threshold, string $q, $status, $prio, $branch, int $emp)
    {
        if (!$this->hasTable('lead_product_lists'))
            return collect();

        $myReportSub = null;
        if ($this->hasTable('customer_reports')) {
            $byCol = $this->hasColumn('customer_reports', 'report_by') ? 'report_by' : ($this->hasColumn('customer_reports', 'employee_id') ? 'employee_id' : null);
            if ($byCol) {
                $myReportSub = DB::table('customer_reports as cr')
                    ->when($this->hasColumn('customer_reports', 'deleted_at'), fn($x) => $x->whereNull('cr.deleted_at'))
                    ->where("cr.$byCol", $emp)
                    ->groupBy('cr.customer_id', 'cr.alternative_id', 'cr.product_id')
                    ->select(['cr.customer_id', 'cr.alternative_id', 'cr.product_id', DB::raw('MAX(cr.created_at) as my_last_report_at')]);
            }
        }

        $base = DB::table('lead_product_lists as lpl')
            ->leftJoin('new_leads as nl', 'nl.id', '=', 'lpl.customer_id')
            ->leftJoin('lead_alternative_adds as laa', 'laa.id', '=', 'lpl.alternative_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
            // Rule: Exclude soft-deleted records
            ->whereNull('lpl.deleted_at')
            // Rule: Exclude Archived or Junk statuses
            ->whereNotIn('lpl.status', ['archive', 'junk'])
            ->where(function ($w) {
                if (Schema::hasTable('new_leads') && Schema::hasColumn('new_leads', 'deleted_at')) {
                    $w->whereNull('nl.deleted_at');
                }
            })
            // Assignment Logic
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
                if ($this->hasColumn('lead_product_lists', 'teams') && $this->supportsJsonSearch()) {
                    $w->orWhereRaw("JSON_SEARCH(lpl.teams, 'one', ?, NULL, '$[*].employee_id') IS NOT NULL", [(string) $emp]);
                    $has = true;
                }
                if (!$has)
                    $w->whereRaw('1=0');
            })
            ->select([
                DB::raw("'lead' as type"),
                'lpl.id',
                'lpl.customer_id',
                'lpl.alternative_id',
                'lpl.product_id',
                'lpl.updated_at',
                'lpl.created_at',
                'lpl.status',
                'lpl.work_status',
                'lpl.stage',
                'lpl.stage_history',
                'lpl.teams',
                DB::raw("COALESCE(ag.article_group, '') as product_name"),
                DB::raw("COALESCE(laa.object_name, '') as object_name"),
                DB::raw("COALESCE(nl.firma, TRIM(CONCAT(COALESCE(nl.lastname,''),' ',COALESCE(nl.name,''))), '') as customer_name"),
                (Schema::hasTable('new_leads') && Schema::hasColumn('new_leads', 'branch_id')) ? 'nl.branch_id' : DB::raw("NULL as branch_id"),
            ]);

        if ($myReportSub) {
            $base->leftJoinSub($myReportSub, 'crx', function ($j) {
                $j->on('crx.customer_id', '=', 'lpl.customer_id')->on('crx.alternative_id', '=', 'lpl.alternative_id')->on('crx.product_id', '=', 'lpl.product_id');
            })->whereNull('crx.my_last_report_at');
        }

        // Apply Filters (Search, Branch, etc.)
        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $w->orWhere('ag.article_group', 'like', "%{$q}%")->orWhere('nl.firma', 'like', "%{$q}%")
                    ->orWhere('nl.lastname', 'like', "%{$q}%")->orWhere('nl.name', 'like', "%{$q}%");
            });
        }
        if ($branch && Schema::hasColumn('new_leads', 'branch_id'))
            $base->where('nl.branch_id', $branch);

        return $base->limit(self::LIMIT_LEAD)->get()->map(function ($r) use ($threshold, $emp) {
            $lastAt = Carbon::parse(collect([$r->updated_at, $r->created_at])->filter()->max());
            if ($lastAt->gt($threshold))
                return null;

            $dueAt = $lastAt->copy()->addHours(self::HOURS);
            return [
                'type' => 'lead',
                'id' => (int) $r->id,
                'title' => trim((string) $r->customer_name) ?: 'Lead',
                'subtitle' => implode(' • ', array_filter([$r->product_name, $r->object_name])),
                'status' => (string) $r->status,
                'priority' => 'normal',
                'due_at' => $dueAt->toDateTimeString(),
                'last_activity_at' => $lastAt->toDateTimeString(),
                'overdue_hours' => (int) max(0, $dueAt->diffInHours(now(), false)),
                'progress_pct' => 35,
                'link' => url("/new_lead_profile/{$r->customer_id}"),
                'meta' => ['customer_id' => $r->customer_id]
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
                'teams' => $this->parseJsonHistory($row?->teams),
                'stage_history' => $this->parseJsonHistory($row?->stage_history),
                'price_history' => $this->parseJsonHistory($row?->price_history),
                'status' => $row?->status,
                'work_status' => $row?->work_status,
                'updated_at' => $row?->updated_at,
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
                    'ept.status',
                    'ept.reason',
                    'ept.note',
                    'ept.change_date',
                    'ept.change_reason',
                    'e.name as changed_by_name',
                    'ept.created_at',
                ])
            : collect();

        $keys = $this->hasTable('personal_task_keys')
            ? DB::table('personal_task_keys as ptk')
                ->where('ptk.personal_task_id', $id)
                ->when($this->hasColumn('personal_task_keys', 'deleted_at'), fn($x) => $x->whereNull('ptk.deleted_at'))
                ->orderByDesc('ptk.created_at')
                ->limit(60)
                ->get([
                    'ptk.task',
                    'ptk.status',
                    'ptk.is_completed',
                    'ptk.done_by',
                    'ptk.done_date',
                    'ptk.work_progress',
                    'ptk.total_time',
                    'ptk.reason',
                    'ptk.created_at',
                ])
            : collect();


        return response()->json([
            'type' => 'task',
            'id' => $id,
            'history' => [
                'changes' => $changes,
                'keys' => $keys,
            ],
        ]);
    }

    private function historyAppointment(int $id)
    {
        $ap = DB::table('main_appointments')
            ->where('id', $id)
            ->first([
                'id',
                'name',
                'status',
                'change_date',
                'change_reason',
                'changed_by',
                'updated_at',
                'is_report',
                'report',
                'report_date',
            ]);

        $assignees = $this->hasTable('main_appointment_employees')
            ? DB::table('main_appointment_employees as mae')
                ->leftJoin('employees as e', 'e.id', '=', 'mae.employee_id')
                ->where('mae.appointment_id', $id)
                ->when($this->hasColumn('main_appointment_employees', 'deleted_at'), fn($x) => $x->whereNull('mae.deleted_at'))
                ->orderByDesc('mae.updated_at')
                ->limit(80)
                ->get(['mae.employee_id', 'e.name', 'e.lastname', 'mae.status', 'mae.reason', 'mae.updated_at'])
            : collect();


        return response()->json(['type' => 'appointment', 'id' => $id, 'history' => ['appointment' => $ap, 'assignees' => $assignees]]);
    }

    private function historyInquiry(int $id)
    {
        $inq = DB::table('inquiries')
            ->where('id', $id)
            ->first([
                'id',
                'title',
                'status',
                'periority',
                'next_step',
                'verify_by',
                'verify_date',
                'updated_at',
                'created_at',
                'note',
            ]);

        $products = $this->hasTable('inquiry_product_lists')
            ? DB::table('inquiry_product_lists as ipl')
                ->where('ipl.inquiry_id', $id)
                ->when($this->hasColumn('inquiry_product_lists', 'deleted_at'), fn($x) => $x->whereNull('ipl.deleted_at'))
                ->orderByDesc('ipl.updated_at')
                ->limit(60)
                ->get([
                    'ipl.id',
                    'ipl.status',
                    'ipl.appointment_date',
                    'ipl.employee_id',
                    'ipl.field_employee',
                    'ipl.department_id',
                    'ipl.product_id',
                    'ipl.service_id',
                    'ipl.updated_at',
                ])
            : collect();


        return response()->json(['type' => 'inquiry', 'id' => $id, 'history' => ['inquiry' => $inq, 'products' => $products]]);
    }

    private function historyTicket(int $id)
    {
        $p = DB::table('problems')
            ->where('id', $id)
            ->first([
                'id',
                'ticket_no',
                'status',
                'date',
                'progress_date',
                'end_date',
                'edit_date',
                'updated_at',
                'problem',
                'progress',
                'solution',
                'priority',
            ]);

        $assignees = $this->hasTable('employee_problem')
            ? DB::table('employee_problem as ep')
                ->leftJoin('employees as e', 'e.id', '=', 'ep.employee_id')
                ->where('ep.problem_id', $id)
                ->when($this->hasColumn('employee_problem', 'deleted_at'), fn($x) => $x->whereNull('ep.deleted_at'))
                ->orderByDesc('ep.updated_at')
                ->limit(80)
                ->get(['ep.employee_id', 'e.name', 'e.lastname', 'ep.created_at'])
            : collect();


        return response()->json(['type' => 'ticket', 'id' => $id, 'history' => ['problem' => $p, 'assignees' => $assignees]]);
    }

    /* =========================================================================
       REPORTS LIST (ONLY MY REPORTS)
       ========================================================================= */

    private function reportsAppointment(int $id)
    {
        $emp = $this->currentEmployeeId();
        if ($emp <= 0)
            abort(403);

        $byCol = $this->hasColumn('appointment_reports', 'report_by')
            ? 'report_by'
            : ($this->hasColumn('appointment_reports', 'employee_id') ? 'employee_id' : null);

        $appointmentIdCol = $this->hasColumn('appointment_reports', 'appointment_id')
            ? 'appointment_id'
            : ($this->hasColumn('appointment_reports', 'main_appointment_id') ? 'main_appointment_id' : null);

        if (!$this->hasTable('appointment_reports') || !$byCol || !$appointmentIdCol) {
            return response()->json(['rows' => []]);
        }

        $reportByExpr = $this->appointmentReportEmployeeExpr('ar');

        $rows = DB::table('appointment_reports as ar')
            ->leftJoin('employees as e', function ($join) use ($reportByExpr) {
                $join->on('e.id', '=', DB::raw($reportByExpr));
            })
            ->where("ar.$appointmentIdCol", $id)
            ->whereRaw("$reportByExpr = ?", [$emp])
            ->when($this->hasColumn('appointment_reports', 'deleted_at'), fn($q) => $q->whereNull('ar.deleted_at'))
            ->orderByDesc('ar.created_at')
            ->limit(80)
            ->get([
                'ar.id',
                'ar.report',
                'ar.report_date',
                'ar.next_step',
                'ar.due_date',
                DB::raw("$reportByExpr as employee_id"),
                DB::raw("
            COALESCE(
                NULLIF(TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))), ''),
                CONCAT('Mitarbeiter #', $reportByExpr)
            ) as report_by_name
        "),
                'ar.created_at',
            ]);

        return response()->json(['rows' => $rows]);
    }

    private function inquiryReportEmployeeExpr(string $alias = 'ir'): string
    {
        $parts = [];

        if ($this->hasColumn('inquiry_reports', 'report_by')) {
            $parts[] = "NULLIF($alias.report_by, 0)";
        }

        if ($this->hasColumn('inquiry_reports', 'employee_id')) {
            $parts[] = "NULLIF($alias.employee_id, 0)";
        }

        if ($this->hasColumn('inquiry_reports', 'created_by')) {
            $parts[] = "NULLIF($alias.created_by, 0)";
        }

        if (empty($parts)) {
            return '0';
        }

        return 'COALESCE(' . implode(', ', $parts) . ', 0)';
    }

    private function reportsTask(int $id)
    {
        $emp = $this->currentEmployeeId();

        if ($emp <= 0) {
            abort(403);
        }

        if (!$this->hasTable('personal_task_comments')) {
            return response()->json(['rows' => []]);
        }

        $rows = DB::table('personal_task_comments as ptc')
            ->leftJoin('employees as e', 'e.id', '=', 'ptc.comment_by')
            ->where('ptc.task_id', $id)
            ->where('ptc.comment_by', $emp)
            ->whereIn('ptc.status', ['report', 'skipped_report'])
            ->when(
                $this->hasColumn('personal_task_comments', 'deleted_at'),
                fn($q) => $q->whereNull('ptc.deleted_at')
            )
            ->orderByDesc('ptc.created_at')
            ->limit(80)
            ->get([
                'ptc.id',
                DB::raw('ptc.comment as report'),
                DB::raw('NULL as report_date'),
                DB::raw('NULL as next_step'),
                DB::raw('NULL as due_date'),
                DB::raw('ptc.comment_by as employee_id'),
                DB::raw("
                COALESCE(
                    NULLIF(TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))), ''),
                    CONCAT('Mitarbeiter #', ptc.comment_by)
                ) as report_by_name
            "),
                'ptc.status',
                'ptc.created_at',
            ]);

        return response()->json(['rows' => $rows]);
    }
    private function reportsTicket(int $id)
    {
        $emp = $this->currentEmployeeId();
        if ($emp <= 0)
            abort(403);

        if (!$this->hasTable('ticket_reports'))
            return response()->json(['rows' => []]);

        $byCol = $this->hasColumn('ticket_reports', 'employee_id')
            ? 'employee_id'
            : ($this->hasColumn('ticket_reports', 'report_by') ? 'report_by' : null);

        if (!$byCol)
            return response()->json(['rows' => []]);

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
        if ($emp <= 0)
            abort(403);

        $byCol = $this->hasColumn('inquiry_reports', 'report_by')
            ? 'report_by'
            : ($this->hasColumn('inquiry_reports', 'employee_id') ? 'employee_id' : null);

        if (!$this->hasTable('inquiry_reports') || !$byCol)
            return response()->json(['rows' => []]);

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
        if ($emp <= 0)
            abort(403);

        $meta = DB::table('lead_product_lists')
            ->where('id', $id)
            ->first(['customer_id', 'alternative_id', 'product_id']);

        if (!$meta || !$this->hasTable('customer_reports'))
            return response()->json(['rows' => []]);

        $byCol = $this->hasColumn('customer_reports', 'report_by')
            ? 'report_by'
            : ($this->hasColumn('customer_reports', 'employee_id') ? 'employee_id' : null);

        if (!$byCol)
            return response()->json(['rows' => []]);

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
            return response()->json(['message' => 'Appointment reports table not available'], 422);
        }

        $text = trim((string) $request->input('report', ''));

        if ($isSkipped) {
            $text = $this->buildSkipReportText('appointment', $id, $employeeId, $request);
        }

        AppointmentReport::create([
            'employee_id' => $employeeId,
            'appointment_id' => $id,
            'task_id' => null,
            'type' => 'appointment',
            'report' => $text,
            'report_date' => $request->input('report_date') ?? now()->toDateString(),
            'next_step' => $request->input('next_step'),
            'due_date' => $request->input('due_date'),
            'report_by' => $employeeId,
            'comments' => [
                'items' => [
                    [
                        'employee_id' => $employeeId,
                        'text' => $text,
                        'created_at' => now()->toIso8601String(),
                    ],
                ],
            ],
            'likes' => 0,
            'dislikes' => 0,
            'meta' => [
                'items' => [],
                'likes' => [],
                'dislikes' => [],
            ],
        ]);

        if ($this->hasTable('main_appointments')) {
            $updates = ['updated_at' => now()];

            if ($this->hasColumn('main_appointments', 'is_report')) {
                $updates['is_report'] = true;
            }

            if ($this->hasColumn('main_appointments', 'report_date')) {
                $updates['report_date'] = now()->toDateString();
            }

            if ($this->hasColumn('main_appointments', 'report_by')) {
                $updates['report_by'] = $employeeId;
            }

            DB::table('main_appointments')
                ->where('id', $id)
                ->update($updates);
        }

        return response()->json(['ok' => true]);
    }
    private function storeReportTask(int $id, int $employeeId, Request $request, bool $isSkipped)
    {
        if (!$this->hasTable('personal_task_comments')) {
            return response()->json(['message' => 'Personal task comments table not available'], 422);
        }

        $text = trim((string) $request->input('report', ''));

        if ($isSkipped) {
            $text = $this->buildSkipReportText('task', $id, $employeeId, $request);
        }

        PersonalTaskComment::create([
            'task_id' => $id,
            'comment_by' => $employeeId,
            'comment' => $text,
            'status' => $isSkipped ? 'skipped_report' : 'report',
            'parent_id' => null,
        ]);

        if ($this->hasTable('personal_tasks')) {
            $updates = ['updated_at' => now()];

            if ($this->hasColumn('personal_tasks', 'is_report')) {
                $updates['is_report'] = true;
            }

            if ($this->hasColumn('personal_tasks', 'report_date')) {
                $updates['report_date'] = now()->toDateString();
            }

            if ($this->hasColumn('personal_tasks', 'report_by')) {
                $updates['report_by'] = $employeeId;
            }

            DB::table('personal_tasks')
                ->where('id', $id)
                ->update($updates);
        }

        return response()->json(['ok' => true]);
    }
    private function storeReportTicket(int $id, int $employeeId, Request $request, bool $isSkipped)
    {
        if (!$this->hasTable('ticket_reports')) {
            return response()->json(['message' => 'Not available'], 422);
        }

        $p = $this->hasTable('problems') ? DB::table('problems')->where('id', $id)->first() : null;

        $customerId = 0;
        $alternativeId = 0;
        $productId = 0;

        if ($p) {
            if ($this->hasColumn('problems', 'customer_id'))
                $customerId = (int) ($p->customer_id ?? 0);
            if ($this->hasColumn('problems', 'alternative_id'))
                $alternativeId = (int) ($p->alternative_id ?? 0);
            if ($this->hasColumn('problems', 'product_id'))
                $productId = (int) ($p->product_id ?? 0);
        }

        $customerId = $customerId ?: (int) $request->input('customer_id', 0);
        $alternativeId = $alternativeId ?: (int) $request->input('alternative_id', 0);
        $productId = $productId ?: (int) $request->input('product_id', 0);

        if ($customerId <= 0 || $alternativeId <= 0 || $productId <= 0) {
            return response()->json([
                'message' => 'Missing ticket linkage (customer_id / alternative_id / product_id). Add these columns to problems or send them from the UI.',
            ], 422);
        }

        $reportText = trim((string) $request->input('report', ''));
        if ($isSkipped)
            $reportText = $this->buildSkipReportText('ticket', $id, $employeeId, $request);

        $title = trim((string) $request->input('title', ''));
        if ($title === '') {
            $title = $this->getRecordTitle('ticket', $id) ?: '';
            if ($title === '') {
                $plain = trim(preg_replace('/\s+/', ' ', $reportText));
                $title = mb_substr($plain, 0, 80) ?: ($isSkipped ? self::SKIP_TEXT : 'Ticket Report');
            }
        }

        $language = $request->input('language') ?? app()->getLocale();

        DB::table('ticket_reports')->insert([
            'ticket_id' => $id,
            'employee_id' => $employeeId,
            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product_id' => $productId,
            'title' => $title,
            'report' => $reportText,
            'language' => $language,
            'report_date' => $request->input('report_date') ?? now()->toDateString(),
            'likes' => 0,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        return response()->json(['ok' => true]);
    }

    private function storeReportInquiry(int $id, int $employeeId, Request $request, bool $isSkipped)
    {
        $text = trim((string) $request->input('report', ''));
        if ($isSkipped)
            $text = $this->buildSkipReportText('inquiry', $id, $employeeId, $request);

        $meta = $isSkipped ? json_encode([
            'skipped' => true,
            'skip_reason' => (string) $request->input('skip_reason', ''),
            'skipped_at' => now()->toDateTimeString(),
            'skipped_by' => $employeeId,
        ]) : null;

        InquiryReport::create([
            'inquiry_id' => $id,
            'report_by' => $employeeId,
            'report' => $text,
            'report_date' => $request->input('report_date') ?? now(),
            'due_date' => $request->input('due_date'),
            'meta' => $meta,
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

        if (!$meta)
            return response()->json(['message' => 'Not found'], 404);

        $text = trim((string) $request->input('report', ''));
        if ($isSkipped)
            $text = $this->buildSkipReportText('lead', $id, $employeeId, $request);

        $details = $isSkipped ? json_encode([
            'skipped' => true,
            'skip_reason' => (string) $request->input('skip_reason', ''),
            'skipped_at' => now()->toDateTimeString(),
            'skipped_by' => $employeeId,
        ]) : null;

        DB::table('customer_reports')->insert([
            'customer_id' => $meta->customer_id,
            'alternative_id' => $meta->alternative_id,
            'product_id' => $meta->product_id,
            'report_by' => $employeeId,
            'stage' => $meta->stage ?: $meta->status,
            'report' => $text,
            'report_details' => $details,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        return response()->json(['ok' => true]);
    }

    /* =========================================================================
       SKIP TEXT BUILDERS
       ========================================================================= */

    private function typeLabelDe(string $type): string
    {
        return match ($type) {
            'inquiry' => 'Anfrage',
            'task' => 'Aufgabe',
            'appointment' => 'Termin',
            'ticket' => 'Ticket',
            'lead' => 'Lead',
            default => 'Eintrag',
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
        $name = trim((string) (auth()->user()->name ?? ''));
        if ($name === '')
            $name = "MA #{$employeeId}";

        $date = $this->formatDateDe($request->input('report_date') ?? now());
        $reason = trim((string) $request->input('skip_reason', ''));
        $reason = preg_replace('/\s+/', ' ', $reason) ?: '—';

        $label = $this->typeLabelDe($type);

        $title = trim((string) ($this->getRecordTitle($type, $id) ?? ''));
        $ref = $title !== '' ? "„{$title}“" : "#{$id}";

        return "Am {$date} hat {$name} den Bericht für {$label} {$ref} übersprungen. Grund: {$reason}.";
    }

    /* =========================================================================
       ACCESS CONTROL (assignment-based, not creator-based)
       ========================================================================= */

    private function abortIfNotInvolved(string $type, int $id): void
    {
        $emp = $this->currentEmployeeId();
        if ($emp <= 0)
            abort(403);

        $ok = match ($type) {
            'inquiry' => $this->isEmployeeInvolvedInInquiry($id, $emp),
            'task' => $this->isEmployeeInvolvedInTask($id, $emp),
            'appointment' => $this->isEmployeeInvolvedInAppointment($id, $emp),
            'ticket' => $this->isEmployeeInvolvedInTicket($id, $emp),
            'lead' => $this->isEmployeeInvolvedInLeadProduct($id, $emp),
            default => false,
        };

        if (!$ok)
            abort(403);
    }

    private function isEmployeeInvolvedInInquiry(int $id, int $emp): bool
    {
        if (!$this->hasTable('inquiry_product_lists'))
            return false;

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
                if (!$any)
                    $w->whereRaw('1=0');
            })
            ->exists();
    }

    private function isEmployeeInvolvedInTask(int $id, int $emp): bool
    {
        if (!$this->hasTable('employees_personal_tasks'))
            return false;

        return DB::table('employees_personal_tasks as ept')
            ->where('ept.task_id', $id)
            ->where('ept.employee_id', $emp)
            ->when($this->hasColumn('employees_personal_tasks', 'deleted_at'), fn($x) => $x->whereNull('ept.deleted_at'))
            ->exists();
    }

    private function isEmployeeInvolvedInAppointment(int $id, int $emp): bool
    {
        if (!$this->hasTable('main_appointment_employees'))
            return false;

        return DB::table('main_appointment_employees as mae')
            ->where('mae.appointment_id', $id)
            ->where('mae.employee_id', $emp)
            ->exists();
    }

    private function isEmployeeInvolvedInTicket(int $id, int $emp): bool
    {
        if (!$this->hasTable('employee_problem'))
            return false;

        return DB::table('employee_problem as ep')
            ->where('ep.problem_id', $id)
            ->where('ep.employee_id', $emp)
            ->exists();
    }

    private function isEmployeeInvolvedInLeadProduct(int $id, int $emp): bool
    {
        if (!$this->hasTable('lead_product_lists'))
            return false;

        $row = DB::table('lead_product_lists as lpl')
            ->where('lpl.id', $id)
            ->when($this->hasColumn('lead_product_lists', 'deleted_at'), fn($x) => $x->whereNull('lpl.deleted_at'))
            ->first([
                'id',
                $this->hasColumn('lead_product_lists', 'employee_id') ? 'employee_id' : DB::raw('NULL as employee_id'),
                $this->hasColumn('lead_product_lists', 'field_employee') ? 'field_employee' : DB::raw('NULL as field_employee'),
                $this->hasColumn('lead_product_lists', 'teams') ? 'teams' : DB::raw('NULL as teams'),
            ]);

        if (!$row)
            return false;

        if ((int) ($row->employee_id ?? 0) === $emp)
            return true;
        if ((int) ($row->field_employee ?? 0) === $emp)
            return true;

        $teams = $this->parseJsonHistory($row->teams ?? null);
        foreach ($teams as $t) {
            if ((int) ($t['employee_id'] ?? 0) === $emp)
                return true;
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
        if (!$user)
            return 0;

        $raw = trim((string) ($user->name ?? ''));

        if ($raw === '' || !preg_match('/^\d+$/', $raw)) {
            return 0;
        }

        $empId = (int) $raw;
        if ($empId <= 0)
            return 0;

        if ($this->hasTable('employees')) {
            $exists = DB::table('employees')->where('id', $empId)->exists();
            if (!$exists)
                return 0;
        }

        return $empId;
    }

    private function canViewAllEmployees(): bool
    {
        $user = auth()->user();
        if (!$user)
            return false;

        if (isset($user->is_admin) && (bool) $user->is_admin === true)
            return true;
        if (isset($user->admin) && (bool) $user->admin === true)
            return true;
        if (isset($user->role) && is_string($user->role) && in_array(strtolower($user->role), ['admin', 'superadmin', 'owner'], true))
            return true;

        if (method_exists($user, 'hasRole')) {
            try {
                if ($user->hasRole('admin') || $user->hasRole('superadmin') || $user->hasRole('owner'))
                    return true;
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return false;
    }

    private function appointmentReportEmployeeExpr(string $alias = 'ar'): string
    {
        $parts = [];

        if ($this->hasColumn('appointment_reports', 'report_by')) {
            $parts[] = "NULLIF($alias.report_by, 0)";
        }

        if ($this->hasColumn('appointment_reports', 'employee_id')) {
            $parts[] = "NULLIF($alias.employee_id, 0)";
        }

        if ($this->hasColumn('appointment_reports', 'created_by')) {
            $parts[] = "NULLIF($alias.created_by, 0)";
        }

        if (empty($parts)) {
            return '0';
        }

        return 'COALESCE(' . implode(', ', $parts) . ', 0)';
    }

    /* =========================================================================
       RECENT REPORTS (unchanged behavior; you said this part is correct)
       ========================================================================= */

    public function recentReportsFetch(Request $request)
    {
        $currentEmployeeId = $this->currentEmployeeId();
        $canViewAll = $this->canViewAllEmployees();

        $sort = (string) $request->get('sort', 'newest');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = min(
            self::MAX_PER_PAGE,
            max(self::MIN_PER_PAGE, (int) $request->get('per_page', self::DEFAULT_PER_PAGE))
        );

        $blockIfFilterCannotMatch = function ($query, bool $canMatch) {
            if (!$canMatch) {
                $query->whereRaw('1 = 0');
            }

            return $query;
        };
        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
        $q = trim((string) $request->get('q', ''));

        // Frontend sends "type", old API can still send "types"
        $typeFromUi = trim((string) $request->get('type', ''));

        if ($typeFromUi !== '') {
            $types = $this->normalizeTypes([$typeFromUi]);
        } else {
            $types = $this->normalizeTypes($request->input('types', self::TYPES));
        }

        /*
        |--------------------------------------------------------------------------
        | Employee Filter
        |--------------------------------------------------------------------------
        | Important:
        | Your app stores employee id in auth()->user()->name.
        | But here employee_id filter is selected employee id from dropdown.
        |--------------------------------------------------------------------------
        */
        $employeeId = (int) $request->get('employee_id', 0);

        if (!$canViewAll) {
            $employeeId = $currentEmployeeId;
        } else {
            if ($employeeId < 0) {
                $employeeId = 0;
            }
        }

        $customerId = max(0, (int) $request->get('customer_id', 0));
        $productId = max(0, (int) $request->get('product_id', 0));
        $departmentId = max(0, (int) $request->get('department_id', 0));

        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        | Frontend sends "from" and "to".
        | Older API can still send "date_from" and "date_to".
        |--------------------------------------------------------------------------
        */
        try {
            $fromInput = $request->get('date_from') ?: $request->get('from');

            $from = $fromInput
                ? Carbon::parse($fromInput)->startOfDay()
                : now()->subDays(30)->startOfDay();
        } catch (\Throwable $e) {
            $from = now()->subDays(30)->startOfDay();
        }

        try {
            $toInput = $request->get('date_to') ?: $request->get('to');

            $to = $toInput
                ? Carbon::parse($toInput)->endOfDay()
                : now()->endOfDay();
        } catch (\Throwable $e) {
            $to = now()->endOfDay();
        }

        $items = collect();

        /*
        |--------------------------------------------------------------------------
        | Result Packer
        |--------------------------------------------------------------------------
        */
        $pack = function (string $type, int $entityId, int $reportId, string $title, string $report, ?string $createdAt, ?string $reportDate, ?string $dueDate, string $nextStep, int $reportById, string $reportByName, ?string $link) {
            $title = trim($title) !== '' ? trim($title) : ucfirst($type);
            $report = $this->cleanText($report);
            $nextStep = $this->cleanText($nextStep);
            $createdAt = (string) ($createdAt ?? '');
            $reportByName = trim($reportByName) !== '' ? trim($reportByName) : 'Unbekannt';

            return [
                /*
                |--------------------------------------------------------------------------
                | Keys used by recent_reports_center frontend
                |--------------------------------------------------------------------------
                */
                'type' => $type,
                'entity_id' => $entityId,
                'ref_title' => $title,
                'employee_id' => $reportById,
                'employee_name' => $reportByName,
                'report_text' => $report,
                'created_at' => $createdAt,
                'report_id' => $reportId,
                'link' => $link,

                /*
                |--------------------------------------------------------------------------
                | Extra keys for other UIs
                |--------------------------------------------------------------------------
                */
                'id' => $entityId,
                'title' => $title,
                'report' => $report,
                'report_date' => $reportDate,
                'due_date' => $dueDate,
                'next_step' => $nextStep,
                'report_by' => $reportById,
                'report_by_name' => $reportByName,

                'can_add_report' => true,
            ];
        };

        /*
        |--------------------------------------------------------------------------
        | APPOINTMENT_REPORTS
        | Used for appointments and tasks
        |--------------------------------------------------------------------------
        */
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
                /*
                |--------------------------------------------------------------------------
                | Appointment Reports
                |--------------------------------------------------------------------------
                */
                if ($appointmentIdCol && in_array('appointment', $types, true)) {
                    $reportByExpr = $this->appointmentReportEmployeeExpr('ar');

                    $qr = DB::table('appointment_reports as ar')
                        ->when(
                            $this->hasColumn('appointment_reports', 'deleted_at'),
                            fn($query) => $query->whereNull('ar.deleted_at')
                        )
                        ->whereBetween('ar.created_at', [$from, $to])
                        ->whereNotNull("ar.$appointmentIdCol");

                    if ($employeeId > 0) {
                        $qr->whereRaw("$reportByExpr = ?", [$employeeId]);
                    }

                    $qr->leftJoin('employees as e', function ($join) use ($reportByExpr) {
                        $join->on('e.id', '=', DB::raw($reportByExpr));
                    });

                    if ($this->hasTable('main_appointments')) {
                        $qr->leftJoin('main_appointments as a', 'a.id', '=', "ar.$appointmentIdCol");

                        if ($this->hasColumn('main_appointments', 'deleted_at')) {
                            $qr->whereNull('a.deleted_at');
                        }

                        if ($customerId > 0) {
                            if ($this->hasColumn('main_appointments', 'customer_id')) {
                                $qr->where('a.customer_id', $customerId);
                            } elseif ($this->hasColumn('main_appointments', 'lead_id')) {
                                $qr->where('a.lead_id', $customerId);
                            } elseif ($this->hasColumn('main_appointments', 'new_lead_id')) {
                                $qr->where('a.new_lead_id', $customerId);
                            } else {
                                $qr->whereRaw('1 = 0');
                            }
                        }

                        if ($productId > 0) {
                            if ($this->hasColumn('main_appointments', 'product_id')) {
                                $qr->where('a.product_id', $productId);
                            } elseif ($this->hasColumn('main_appointments', 'article_group_id')) {
                                $qr->where('a.article_group_id', $productId);
                            } else {
                                $qr->whereRaw('1 = 0');
                            }
                        }

                        if ($departmentId > 0) {
                            if ($this->hasColumn('main_appointments', 'department_id')) {
                                $qr->where('a.department_id', $departmentId);
                            } else {
                                $qr->whereRaw('1 = 0');
                            }
                        }

                        if ($q !== '') {
                            $qr->where(function ($w) use ($q) {
                                $w->orWhere('ar.report', 'like', "%{$q}%");

                                if ($this->hasColumn('appointment_reports', 'next_step')) {
                                    $w->orWhere('ar.next_step', 'like', "%{$q}%");
                                }

                                if ($this->hasColumn('main_appointments', 'name')) {
                                    $w->orWhere('a.name', 'like', "%{$q}%");
                                }

                                if ($this->hasColumn('main_appointments', 'title')) {
                                    $w->orWhere('a.title', 'like', "%{$q}%");
                                }

                                if ($this->hasColumn('main_appointments', 'subject')) {
                                    $w->orWhere('a.subject', 'like', "%{$q}%");
                                }

                                if ($this->hasColumn('main_appointments', 'full_address')) {
                                    $w->orWhere('a.full_address', 'like', "%{$q}%");
                                }
                            });
                        }
                    } else {
                        if ($customerId > 0 || $productId > 0 || $departmentId > 0) {
                            $qr->whereRaw('1 = 0');
                        }

                        if ($q !== '') {
                            $qr->where('ar.report', 'like', "%{$q}%");
                        }
                    }

                    $titleExpr = "'Termin'";

                    if ($this->hasTable('main_appointments')) {
                        $cands = [];

                        foreach (['name', 'title', 'subject'] as $col) {
                            if ($this->hasColumn('main_appointments', $col)) {
                                $cands[] = "NULLIF(a.$col,'')";
                            }
                        }

                        if (!empty($cands)) {
                            $titleExpr = "COALESCE(" . implode(',', $cands) . ", 'Termin')";
                        }
                    }

                    $rows = $qr->orderByDesc('ar.created_at')
                        ->limit(2000)
                        ->get([
                            'ar.id as report_id',
                            DB::raw("ar.$appointmentIdCol as item_id"),
                            DB::raw("$titleExpr as title"),
                            'ar.report',
                            $this->hasColumn('appointment_reports', 'report_date') ? 'ar.report_date' : DB::raw('NULL as report_date'),
                            $this->hasColumn('appointment_reports', 'due_date') ? 'ar.due_date' : DB::raw('NULL as due_date'),
                            $this->hasColumn('appointment_reports', 'next_step') ? 'ar.next_step' : DB::raw('NULL as next_step'),
                            DB::raw("$reportByExpr as report_by_id"),
                            DB::raw("
                COALESCE(
                    NULLIF(TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))), ''),
                    CONCAT('Mitarbeiter #', $reportByExpr)
                ) as report_by_name
            "),
                            'ar.created_at',
                        ]);

                    $items = $items->merge($rows->map(function ($row) use ($pack) {
                        $itemId = (int) ($row->item_id ?? 0);

                        return $pack(
                            'appointment',
                            $itemId,
                            (int) ($row->report_id ?? 0),
                            (string) ($row->title ?? 'Termin'),
                            (string) ($row->report ?? ''),
                            (string) ($row->created_at ?? ''),
                            $row->report_date ? (string) $row->report_date : null,
                            $row->due_date ? (string) $row->due_date : null,
                            (string) ($row->next_step ?? ''),
                            (int) ($row->report_by_id ?? 0),
                            (string) ($row->report_by_name ?? ''),
                            $itemId > 0 ? url("/customer/appointments/{$itemId}") : null
                        );
                    }));
                }
                /*
                |--------------------------------------------------------------------------
                | Task Reports
                |--------------------------------------------------------------------------
                */
                if ($taskIdCol && in_array('task', $types, true)) {
                    $qr = DB::table('appointment_reports as ar')
                        ->when(
                            $this->hasColumn('appointment_reports', 'deleted_at'),
                            fn($query) => $query->whereNull('ar.deleted_at')
                        )
                        ->whereBetween('ar.created_at', [$from, $to])
                        ->whereNotNull("ar.$taskIdCol");

                    if ($employeeId > 0) {
                        $qr->where("ar.$byCol", $employeeId);
                    }

                    if ($q !== '') {
                        $qr->where(function ($w) use ($q) {
                            $w->orWhere('ar.report', 'like', "%{$q}%");

                            if ($this->hasColumn('appointment_reports', 'next_step')) {
                                $w->orWhere('ar.next_step', 'like', "%{$q}%");
                            }
                        });
                    }

                    $reportByExpr = $this->appointmentReportEmployeeExpr('ar');

                    $qr->leftJoin('employees as e', function ($join) use ($reportByExpr) {
                        $join->on('e.id', '=', DB::raw($reportByExpr));
                    });
                    if ($this->hasTable('personal_tasks')) {
                        $qr->leftJoin('personal_tasks as t', 't.id', '=', "ar.$taskIdCol");

                        if ($this->hasColumn('personal_tasks', 'deleted_at')) {
                            $qr->whereNull('t.deleted_at');
                        }

                        if ($customerId > 0) {
                            if ($this->hasColumn('personal_tasks', 'customer_id')) {
                                $qr->where('t.customer_id', $customerId);
                            } elseif ($this->hasColumn('personal_tasks', 'lead_id')) {
                                $qr->where('t.lead_id', $customerId);
                            } elseif ($this->hasColumn('personal_tasks', 'new_lead_id')) {
                                $qr->where('t.new_lead_id', $customerId);
                            } else {
                                $qr->whereRaw('1 = 0');
                            }
                        }

                        if ($productId > 0) {
                            if ($this->hasColumn('personal_tasks', 'product_id')) {
                                $qr->where('t.product_id', $productId);
                            } elseif ($this->hasColumn('personal_tasks', 'article_group_id')) {
                                $qr->where('t.article_group_id', $productId);
                            } else {
                                $qr->whereRaw('1 = 0');
                            }
                        }

                        if ($departmentId > 0) {
                            if ($this->hasColumn('personal_tasks', 'department_id')) {
                                $qr->where('t.department_id', $departmentId);
                            } else {
                                $qr->whereRaw('1 = 0');
                            }
                        }
                    } else {
                        if ($customerId > 0 || $productId > 0 || $departmentId > 0) {
                            $qr->whereRaw('1 = 0');
                        }
                    }

                    $titleExpr = "'Aufgabe'";

                    if ($this->hasTable('personal_tasks')) {
                        $cands = [];

                        foreach (['task_title', 'title', 'task', 'name', 'subject'] as $col) {
                            if ($this->hasColumn('personal_tasks', $col)) {
                                $cands[] = "NULLIF(t.$col,'')";
                            }
                        }

                        if (!empty($cands)) {
                            $titleExpr = "COALESCE(" . implode(',', $cands) . ", 'Aufgabe')";
                        }
                    }

                    $rows = $qr->orderByDesc('ar.created_at')
                        ->limit(2000)
                        ->get([
                            'ar.id as report_id',
                            DB::raw("ar.$taskIdCol as item_id"),
                            DB::raw("$titleExpr as title"),
                            'ar.report',
                            $this->hasColumn('appointment_reports', 'report_date') ? 'ar.report_date' : DB::raw('NULL as report_date'),
                            $this->hasColumn('appointment_reports', 'due_date') ? 'ar.due_date' : DB::raw('NULL as due_date'),
                            $this->hasColumn('appointment_reports', 'next_step') ? 'ar.next_step' : DB::raw('NULL as next_step'),
                            DB::raw("$reportByExpr as report_by_id"),
                            DB::raw("
                                COALESCE(
                                    NULLIF(TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))), ''),
                                    CONCAT('Mitarbeiter #', $reportByExpr)
                                ) as report_by_name
                            "),
                        ]);

                    $items = $items->merge($rows->map(function ($row) use ($pack) {
                        $itemId = (int) ($row->item_id ?? 0);

                        return $pack(
                            'task',
                            $itemId,
                            (int) ($row->report_id ?? 0),
                            (string) ($row->title ?? 'Aufgabe'),
                            (string) ($row->report ?? ''),
                            (string) ($row->created_at ?? ''),
                            $row->report_date ? (string) $row->report_date : null,
                            $row->due_date ? (string) $row->due_date : null,
                            (string) ($row->next_step ?? ''),
                            (int) ($row->report_by_id ?? 0),
                            (string) ($row->report_by_name ?? ''),
                            $itemId > 0 ? url("/personal-tasks/{$itemId}/profile") : null
                        );
                    }));
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INQUIRY_REPORTS
        |--------------------------------------------------------------------------
        */
        $reportByExpr = $this->inquiryReportEmployeeExpr('ir');

        $qr = DB::table('inquiry_reports as ir')
            ->when(
                $this->hasColumn('inquiry_reports', 'deleted_at'),
                fn($query) => $query->whereNull('ir.deleted_at')
            )
            ->whereBetween('ir.created_at', [$from, $to]);

        if ($employeeId > 0) {
            $qr->whereRaw("$reportByExpr = ?", [$employeeId]);
        }

        /*
        |--------------------------------------------------------------------------
        | TICKET_REPORTS
        |--------------------------------------------------------------------------
        */
        if ($this->hasTable('ticket_reports') && in_array('ticket', $types, true)) {
            $byCol = $this->hasColumn('ticket_reports', 'employee_id')
                ? 'employee_id'
                : ($this->hasColumn('ticket_reports', 'report_by') ? 'report_by' : null);

            if ($byCol) {
                $qr = DB::table('ticket_reports as tr')
                    ->when(
                        $this->hasColumn('ticket_reports', 'deleted_at'),
                        fn($query) => $query->whereNull('tr.deleted_at')
                    )
                    ->whereBetween('tr.created_at', [$from, $to]);

                if ($employeeId > 0) {
                    $qr->where("tr.$byCol", $employeeId);
                }

                if ($q !== '') {
                    $qr->where(function ($w) use ($q) {
                        if ($this->hasColumn('ticket_reports', 'title')) {
                            $w->orWhere('tr.title', 'like', "%{$q}%");
                        }

                        $w->orWhere('tr.report', 'like', "%{$q}%");
                    });
                }

                $qr->leftJoin('employees as e', 'e.id', '=', "tr.$byCol");

                $titleExpr = $this->hasColumn('ticket_reports', 'title')
                    ? "COALESCE(NULLIF(tr.title,''), 'Ticket')"
                    : "'Ticket'";

                if ($this->hasTable('problems')) {
                    $qr->leftJoin('problems as p', 'p.id', '=', 'tr.ticket_id');

                    if ($this->hasColumn('problems', 'deleted_at')) {
                        $qr->whereNull('p.deleted_at');
                    }

                    if ($customerId > 0) {
                        if ($this->hasColumn('problems', 'customer_id')) {
                            $qr->where('p.customer_id', $customerId);
                        } elseif ($this->hasColumn('problems', 'lead_id')) {
                            $qr->where('p.lead_id', $customerId);
                        } elseif ($this->hasColumn('problems', 'new_lead_id')) {
                            $qr->where('p.new_lead_id', $customerId);
                        } elseif ($this->hasColumn('ticket_reports', 'customer_id')) {
                            $qr->where('tr.customer_id', $customerId);
                        } else {
                            $qr->whereRaw('1 = 0');
                        }
                    }

                    if ($productId > 0) {
                        if ($this->hasColumn('problems', 'product_id')) {
                            $qr->where('p.product_id', $productId);
                        } elseif ($this->hasColumn('problems', 'article_group_id')) {
                            $qr->where('p.article_group_id', $productId);
                        } elseif ($this->hasColumn('ticket_reports', 'product_id')) {
                            $qr->where('tr.product_id', $productId);
                        } else {
                            $qr->whereRaw('1 = 0');
                        }
                    }

                    if ($departmentId > 0) {
                        if ($this->hasColumn('problems', 'department_id')) {
                            $qr->where('p.department_id', $departmentId);
                        } elseif ($this->hasColumn('ticket_reports', 'department_id')) {
                            $qr->where('tr.department_id', $departmentId);
                        } else {
                            $qr->whereRaw('1 = 0');
                        }
                    }

                    $cands = [];

                    foreach (['article_name', 'title', 'subject', 'ticket_no'] as $col) {
                        if ($this->hasColumn('problems', $col)) {
                            $cands[] = "NULLIF(p.$col,'')";
                        }
                    }

                    if (!empty($cands)) {
                        $titleExpr = "COALESCE(" . implode(',', $cands) . ", " . ($this->hasColumn('ticket_reports', 'title') ? "NULLIF(tr.title,''), " : "") . "'Ticket')";
                    }
                }

                $rows = $qr->orderByDesc('tr.created_at')
                    ->limit(2000)
                    ->get([
                        'tr.id as report_id',
                        'tr.ticket_id as item_id',
                        DB::raw("$titleExpr as title"),
                        'tr.report',
                        $this->hasColumn('ticket_reports', 'report_date') ? 'tr.report_date' : DB::raw('NULL as report_date'),
                        DB::raw('NULL as due_date'),
                        DB::raw('NULL as next_step'),
                        DB::raw("tr.$byCol as report_by_id"),
                        DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as report_by_name"),
                        'tr.created_at',
                    ]);

                $items = $items->merge($rows->map(function ($row) use ($pack) {
                    $itemId = (int) ($row->item_id ?? 0);

                    return $pack(
                        'ticket',
                        $itemId,
                        (int) ($row->report_id ?? 0),
                        (string) ($row->title ?? 'Ticket'),
                        (string) ($row->report ?? ''),
                        (string) ($row->created_at ?? ''),
                        $row->report_date ? (string) $row->report_date : null,
                        null,
                        '',
                        (int) ($row->report_by_id ?? 0),
                        (string) ($row->report_by_name ?? ''),
                        $itemId > 0 ? url("/problem/profile/{$itemId}") : null
                    );
                }));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER_REPORTS / LEADS
        |--------------------------------------------------------------------------
        */
        if ($this->hasTable('customer_reports') && in_array('lead', $types, true)) {
            $byCol = $this->hasColumn('customer_reports', 'report_by')
                ? 'report_by'
                : ($this->hasColumn('customer_reports', 'employee_id') ? 'employee_id' : null);

            if ($byCol) {
                $qr = DB::table('customer_reports as cr')
                    ->when(
                        $this->hasColumn('customer_reports', 'deleted_at'),
                        fn($query) => $query->whereNull('cr.deleted_at')
                    )
                    ->whereBetween('cr.created_at', [$from, $to]);

                if ($customerId > 0) {
                    if ($this->hasColumn('customer_reports', 'customer_id')) {
                        $qr->where('cr.customer_id', $customerId);
                    } else {
                        $qr->whereRaw('1 = 0');
                    }
                }

                if ($productId > 0) {
                    if ($this->hasColumn('customer_reports', 'product_id')) {
                        $qr->where('cr.product_id', $productId);
                    } else {
                        $qr->whereRaw('1 = 0');
                    }
                }

                if ($employeeId > 0) {
                    $qr->where("cr.$byCol", $employeeId);
                }

                if ($q !== '') {
                    $qr->where(function ($w) use ($q) {
                        $w->orWhere('cr.report', 'like', "%{$q}%");

                        if ($this->hasColumn('customer_reports', 'stage')) {
                            $w->orWhere('cr.stage', 'like', "%{$q}%");
                        }

                        if ($this->hasColumn('customer_reports', 'report_details')) {
                            $w->orWhere('cr.report_details', 'like', "%{$q}%");
                        }
                    });
                }

                $qr->leftJoin('employees as e', 'e.id', '=', "cr.$byCol");

                $titleExpr = "'Lead'";

                if ($this->hasTable('new_leads')) {
                    $qr->leftJoin('new_leads as nl', 'nl.id', '=', 'cr.customer_id');

                    if ($this->hasColumn('new_leads', 'deleted_at')) {
                        $qr->whereNull('nl.deleted_at');
                    }

                    $titleExpr = "COALESCE(NULLIF(nl.firma,''), NULLIF(TRIM(CONCAT(COALESCE(nl.lastname,''),' ',COALESCE(nl.name,''))), ''), 'Lead')";
                }

                if ($departmentId > 0) {
                    $departmentMatched = false;

                    if ($this->hasColumn('customer_reports', 'department_id')) {
                        $qr->where('cr.department_id', $departmentId);
                        $departmentMatched = true;
                    } elseif ($this->hasTable('lead_product_lists')) {
                        $qr->whereExists(function ($exists) use ($departmentId) {
                            $exists->select(DB::raw(1))
                                ->from('lead_product_lists as lpl')
                                ->whereColumn('lpl.customer_id', 'cr.customer_id');

                            if ($this->hasColumn('customer_reports', 'alternative_id') && $this->hasColumn('lead_product_lists', 'alternative_id')) {
                                $exists->whereColumn('lpl.alternative_id', 'cr.alternative_id');
                            }

                            if ($this->hasColumn('customer_reports', 'product_id') && $this->hasColumn('lead_product_lists', 'product_id')) {
                                $exists->whereColumn('lpl.product_id', 'cr.product_id');
                            }

                            if ($this->hasColumn('lead_product_lists', 'deleted_at')) {
                                $exists->whereNull('lpl.deleted_at');
                            }

                            if ($this->hasColumn('lead_product_lists', 'department_id')) {
                                $exists->where('lpl.department_id', $departmentId);
                            } else {
                                $exists->whereRaw('1 = 0');
                            }
                        });

                        $departmentMatched = true;
                    }

                    if (!$departmentMatched) {
                        $qr->whereRaw('1 = 0');
                    }
                }

                if ($this->hasTable('article_groups') && $this->hasColumn('customer_reports', 'product_id')) {
                    $qr->leftJoin('article_groups as ag', 'ag.id', '=', 'cr.product_id');

                    if ($this->hasColumn('article_groups', 'deleted_at')) {
                        $qr->whereNull('ag.deleted_at');
                    }

                    $titleExpr = "TRIM(CONCAT($titleExpr, CASE WHEN ag.article_group IS NULL OR ag.article_group = '' THEN '' ELSE CONCAT(' • ', ag.article_group) END))";
                }

                $rows = $qr->orderByDesc('cr.created_at')
                    ->limit(2000)
                    ->get([
                        'cr.id as report_id',
                        'cr.customer_id as item_id',
                        DB::raw("$titleExpr as title"),
                        'cr.report',
                        $this->hasColumn('customer_reports', 'report_details') ? 'cr.report_details' : DB::raw('NULL as report_details'),
                        DB::raw('NULL as report_date'),
                        DB::raw('NULL as due_date'),
                        $this->hasColumn('customer_reports', 'stage') ? 'cr.stage as next_step' : DB::raw('NULL as next_step'),
                        DB::raw("cr.$byCol as report_by_id"),
                        DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as report_by_name"),
                        'cr.created_at',
                    ]);

                $items = $items->merge($rows->map(function ($row) use ($pack) {
                    $customerId = (int) ($row->item_id ?? 0);

                    $reportText = trim((string) ($row->report ?? ''));

                    if ($reportText === '' && !empty($row->report_details)) {
                        $reportText = (string) $row->report_details;
                    }

                    return $pack(
                        'lead',
                        $customerId,
                        (int) ($row->report_id ?? 0),
                        (string) ($row->title ?? 'Lead'),
                        $reportText,
                        (string) ($row->created_at ?? ''),
                        null,
                        null,
                        (string) ($row->next_step ?? ''),
                        (int) ($row->report_by_id ?? 0),
                        (string) ($row->report_by_name ?? ''),
                        $customerId > 0 ? url("/new_lead_profile/{$customerId}") : null
                    );
                }));
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sort + Pagination
        |--------------------------------------------------------------------------
        */
        $items = $items->filter()->values();

        $items = match ($sort) {
            'oldest' => $items->sortBy('created_at')->values(),
            default => $items->sortByDesc('created_at')->values(),
        };

        $total = $items->count();

        $slice = $items
            ->slice(($page - 1) * $perPage, $perPage)
            ->values();

        return response()->json([
            'items' => $slice,
            'rows' => $slice,

            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'has_more' => ($page * $perPage) < $total,
            ],

            'meta' => [
                'can_view_all' => $canViewAll,
                'current_employee_id' => $currentEmployeeId,
                'employee_id' => $employeeId,
                'types' => $types,
                'date_from' => $from->toDateString(),
                'date_to' => $to->toDateString(),
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
    /* =========================================================================
       HELPERS
       ========================================================================= */

    private function statusLabelDe($status): string
    {
        $raw = trim((string) ($status ?? ''));

        if ($raw === '') {
            return 'Offen';
        }

        $key = mb_strtolower($raw, 'UTF-8');
        $key = str_replace(['-', '_', '.', '/', '\\'], ' ', $key);
        $key = preg_replace('/\s+/', ' ', $key);
        $key = trim($key);

        return match ($key) {
            // Offen
            'open',
            'opened',
            'offen',
            'new',
            'neu',
            'created',
            'erstellt',
            'todo',
            'to do',
            'not started',
            'nicht gestartet',
            'unbearbeitet',
            'draft',
            'entwurf' => 'Offen',

            // In Bearbeitung
            'in progress',
            'in process',
            'in process',
            'process',
            'processing',
            'progress',
            'working',
            'running',
            'started',
            'bearbeitung',
            'in bearbeitung',
            'wird bearbeitet',
            'laufend',
            'läuft',
            'laeuft',
            'ongoing',
            'doing',
            'assigned',
            'zugewiesen' => 'In Bearbeitung',

            // Wartend
            'pending',
            'waiting',
            'wait',
            'waiting customer',
            'waiting for customer',
            'waiting external',
            'waiting internal',
            'on hold',
            'hold',
            'paused',
            'pause',
            'snoozed',
            'wartet',
            'wartend',
            'warten',
            'wartet auf kunde',
            'wartet auf kunden',
            'wartet extern',
            'wartet intern',
            'zuruckgestellt',
            'zurückgestellt',
            'verschoben',
            'postponed',
            'deferred' => 'Wartend',

            // Erledigt
            'done',
            'completed',
            'complete',
            'finished',
            'finish',
            'end',
            'ended',
            'closed',
            'close',
            'resolved',
            'solved',
            'verified',
            'approved',
            'success',
            'successful',
            'won',
            'published',
            'delivered',
            'submitted',
            'sent',
            'fertig',
            'erledigt',
            'abgeschlossen',
            'geschlossen',
            'beendet',
            'gelöst',
            'geloest',
            'genehmigt',
            'bestätigt',
            'bestaetigt',
            'erfolgreich',
            'gewonnen',
            'veröffentlicht',
            'veroeffentlicht',
            'geliefert',
            'eingereicht',
            'gesendet' => 'Erledigt',

            // Abgebrochen
            'cancel',
            'canceled',
            'cancelled',
            'cancled',
            'storno',
            'storniert',
            'abgebrochen',
            'abbruch',
            'terminated',
            'stopped',
            'stop',
            'discontinued',
            'eingestellt' => 'Abgebrochen',

            // Abgelehnt
            'reject',
            'rejected',
            'decline',
            'declined',
            'denied',
            'refused',
            'not approved',
            'abgelehnt',
            'zurückgewiesen',
            'zuruckgewiesen',
            'verweigert',
            'nicht genehmigt' => 'Abgelehnt',

            // Fehlgeschlagen
            'failed',
            'fail',
            'error',
            'problem',
            'lost',
            'invalid',
            'not valid',
            'fehlgeschlagen',
            'fehler',
            'verloren',
            'ungültig',
            'ungueltig',
            'nicht gültig',
            'nicht gueltig' => 'Fehlgeschlagen',

            // Archiv / Junk
            'archive',
            'archived',
            'archiv',
            'archiviert' => 'Archiviert',

            'junk',
            'spam',
            'trash',
            'papierkorb',
            'müll',
            'muell' => 'Junk',

            // Aktiv / Inaktiv
            'active',
            'aktiv' => 'Aktiv',

            'inactive',
            'inaktiv',
            'disabled',
            'deactivated',
            'deaktiviert',
            'gesperrt',
            'blocked',
            'blockiert' => 'Inaktiv',

            default => $this->humanizeGermanStatus($raw),
        };
    }

    private function humanizeGermanStatus(string $status): string
    {
        $value = trim($status);

        if ($value === '') {
            return 'Offen';
        }

        $normalized = mb_strtolower($value, 'UTF-8');
        $normalized = str_replace(['_', '-', '.', '/', '\\'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        $normalized = trim($normalized);

        if (
            str_contains($normalized, 'complete') ||
            str_contains($normalized, 'end') ||
            str_contains($normalized, 'finish') ||
            str_contains($normalized, 'ended') ||
            str_contains($normalized, 'closed') ||
            str_contains($normalized, 'done') ||
            str_contains($normalized, 'resolved') ||
            str_contains($normalized, 'erledigt') ||
            str_contains($normalized, 'abgeschlossen') ||
            str_contains($normalized, 'beendet') ||
            str_contains($normalized, 'geschlossen')
        ) {
            return 'Erledigt';
        }

        if (
            str_contains($normalized, 'progress') ||
            str_contains($normalized, 'process') ||
            str_contains($normalized, 'working') ||
            str_contains($normalized, 'running') ||
            str_contains($normalized, 'bearbeitung') ||
            str_contains($normalized, 'laufend')
        ) {
            return 'In Bearbeitung';
        }

        if (
            str_contains($normalized, 'open') ||
            str_contains($normalized, 'offen') ||
            str_contains($normalized, 'new') ||
            str_contains($normalized, 'neu') ||
            str_contains($normalized, 'todo')
        ) {
            return 'Offen';
        }

        if (
            str_contains($normalized, 'wait') ||
            str_contains($normalized, 'pending') ||
            str_contains($normalized, 'hold') ||
            str_contains($normalized, 'paused') ||
            str_contains($normalized, 'wart') ||
            str_contains($normalized, 'verschoben')
        ) {
            return 'Wartend';
        }

        if (
            str_contains($normalized, 'cancel') ||
            str_contains($normalized, 'storno') ||
            str_contains($normalized, 'abbruch') ||
            str_contains($normalized, 'abgebrochen')
        ) {
            return 'Abgebrochen';
        }

        if (
            str_contains($normalized, 'reject') ||
            str_contains($normalized, 'decline') ||
            str_contains($normalized, 'denied') ||
            str_contains($normalized, 'abgelehnt')
        ) {
            return 'Abgelehnt';
        }

        if (
            str_contains($normalized, 'fail') ||
            str_contains($normalized, 'error') ||
            str_contains($normalized, 'lost') ||
            str_contains($normalized, 'fehler') ||
            str_contains($normalized, 'verloren')
        ) {
            return 'Fehlgeschlagen';
        }

        if (
            str_contains($normalized, 'archive') ||
            str_contains($normalized, 'archiv')
        ) {
            return 'Archiviert';
        }

        if (
            str_contains($normalized, 'junk') ||
            str_contains($normalized, 'spam') ||
            str_contains($normalized, 'trash')
        ) {
            return 'Junk';
        }

        return Str::of($normalized)
            ->replace(['_', '-'], ' ')
            ->title()
            ->toString();
    }

    private function priorityLabelDe($priority): string
    {
        $raw = trim((string) ($priority ?? ''));

        if ($raw === '') {
            return 'Normal';
        }

        $key = mb_strtolower($raw, 'UTF-8');
        $key = str_replace(['_', '-', '.', '/', '\\'], ' ', $key);
        $key = preg_replace('/\s+/', ' ', $key);
        $key = trim($key);

        return match ($key) {
            'urgent',
            'critical',
            'very high',
            'sehr hoch',
            'kritisch',
            'dringend' => 'Sehr hoch',

            'high',
            'hoch',
            'important',
            'wichtig' => 'Hoch',

            'medium',
            'middle',
            'mittel',
            'normal' => 'Normal',

            'low',
            'niedrig',
            'minor',
            'klein' => 'Niedrig',

            default => Str::of($key)->title()->toString(),
        };
    }

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
        if (!$raw)
            return [];

        $arr = is_string($raw) ? json_decode($raw, true) : $raw;
        if (!is_array($arr))
            return [];

        // remove "deleted" entries (common keys across your histories)
        return array_values(array_filter($arr, function ($x) {
            if (!is_array($x))
                return true;

            // any of these means "this entry is deleted"
            $flags = [
                'deleted',
                'is_deleted',
                'isDeleted',
                'removed',
                'is_removed',
                'deleted_at',
                'deletedAt',
                'is_archived',
                'archived',
                'archived_at',
                'action',
                'event',
                'type',
            ];

            // explicit boolean flags
            foreach (['deleted', 'is_deleted', 'isDeleted', 'removed', 'is_removed'] as $k) {
                if (array_key_exists($k, $x) && (bool) $x[$k] === true)
                    return false;
            }

            // deleted timestamp
            foreach (['deleted_at', 'deletedAt', 'archived_at'] as $k) {
                if (!empty($x[$k]))
                    return false;
            }

            // action/type markers
            foreach (['action', 'event', 'type'] as $k) {
                if (!empty($x[$k])) {
                    $v = strtolower(trim((string) $x[$k]));
                    if (in_array($v, ['delete', 'deleted', 'remove', 'removed', 'archive', 'archived'], true))
                        return false;
                }
            }

            return true;
        }));
    }

    private function firstExistingColumn(string $table, array $candidates): ?string
    {
        if (!$this->hasTable($table))
            return null;
        foreach ($candidates as $c) {
            if ($this->hasColumn($table, $c))
                return $c;
        }
        return null;
    }

    private function cleanText($value): string
    {
        $s = (string) ($value ?? '');
        if ($s === '')
            return '';
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

    public function reminderUpsert(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', Rule::in(self::TYPES)],
            'id' => ['required', 'integer', 'min:1'],
            'minutes' => ['nullable', 'integer', 'min:1', 'max:525600'], // up to 1 year
            'next_remind_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $employeeId = $this->currentEmployeeId();
        if ($employeeId <= 0)
            abort(403);

        $type = $request->string('type')->toString();
        $id = (int) $request->get('id');

        $this->abortIfNotInvolved($type, $id);

        if (!$this->hasTable('reminders')) {
            return response()->json(['message' => 'Reminders not available'], 422);
        }

        $next = $this->computeNextRemindAt($request);

        $existing = DB::table('reminders')
            ->whereNull('deleted_at')
            ->where('employee_id', $employeeId)
            ->where('entity_type', $type)
            ->where('entity_id', $id)
            ->first(['id', 'next_remind_at']);

        $now = now();

        if ($existing) {
            $oldNext = $existing->next_remind_at ? Carbon::parse($existing->next_remind_at) : null;

            DB::table('reminders')->where('id', $existing->id)->update([
                'status' => 'snoozed',
                'next_remind_at' => $next,
                'updated_at' => $now,
            ]);

            $this->logReminderEvent((int) $existing->id, 'snoozed', $oldNext, $next, $request->input('note'), $employeeId);

            return response()->json(['ok' => true, 'id' => (int) $existing->id, 'updated' => true]);
        }

        $rid = DB::table('reminders')->insertGetId([
            'employee_id' => $employeeId,
            'entity_type' => $type,
            'entity_id' => $id,
            'status' => 'active',
            'next_remind_at' => $next,
            'last_reminded_at' => null,
            'created_by' => $employeeId,
            'meta' => json_encode(['source' => 'overdue48h']),
            'created_at' => $now,
            'updated_at' => $now,
            'deleted_at' => null,
        ]);

        $this->logReminderEvent($rid, 'created', null, $next, $request->input('note'), $employeeId);

        return response()->json(['ok' => true, 'id' => (int) $rid, 'created' => true]);
    }

    public function reminderBulkUpsert(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', 'string', Rule::in(self::TYPES)],
            'items.*.id' => ['required', 'integer', 'min:1'],
            'minutes' => ['nullable', 'integer', 'min:1', 'max:525600'],
            'next_remind_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $employeeId = $this->currentEmployeeId();
        if ($employeeId <= 0)
            abort(403);

        if (!$this->hasTable('reminders')) {
            return response()->json(['message' => 'Reminders not available'], 422);
        }

        $next = $this->computeNextRemindAt($request);
        $items = $request->input('items', []);
        $now = now();

        DB::beginTransaction();
        try {
            $count = 0;

            foreach ($items as $it) {
                $type = (string) ($it['type'] ?? '');
                $id = (int) ($it['id'] ?? 0);
                if ($type === '' || $id <= 0)
                    continue;

                $this->abortIfNotInvolved($type, $id);

                $existing = DB::table('reminders')
                    ->whereNull('deleted_at')
                    ->where('employee_id', $employeeId)
                    ->where('entity_type', $type)
                    ->where('entity_id', $id)
                    ->first(['id', 'next_remind_at']);

                if ($existing) {
                    $oldNext = $existing->next_remind_at ? Carbon::parse($existing->next_remind_at) : null;

                    DB::table('reminders')->where('id', $existing->id)->update([
                        'status' => 'snoozed',
                        'next_remind_at' => $next,
                        'updated_at' => $now,
                    ]);

                    $this->logReminderEvent((int) $existing->id, 'snoozed', $oldNext, $next, $request->input('note'), $employeeId);
                    $count++;
                    continue;
                }

                $rid = DB::table('reminders')->insertGetId([
                    'employee_id' => $employeeId,
                    'entity_type' => $type,
                    'entity_id' => $id,
                    'status' => 'active',
                    'next_remind_at' => $next,
                    'last_reminded_at' => null,
                    'created_by' => $employeeId,
                    'meta' => json_encode(['source' => 'overdue48h_bulk']),
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ]);

                $this->logReminderEvent($rid, 'created', null, $next, $request->input('note'), $employeeId);
                $count++;
            }

            DB::commit();
            return response()->json(['ok' => true, 'count' => $count]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Bulk reminder error: ' . $e->getMessage()], 500);
        }
    }

    private function computeNextRemindAt(Request $request): ?string
    {
        $minutes = $request->input('minutes');
        $dt = $request->input('next_remind_at');

        if ($minutes !== null && is_numeric($minutes)) {
            return now()->addMinutes((int) $minutes)->toDateTimeString();
        }

        if ($dt) {
            try {
                return Carbon::parse($dt)->toDateTimeString();
            } catch (\Throwable $e) {
            }
        }

        // fallback default: +2 hours
        return now()->addHours(2)->toDateTimeString();
    }

    private function logReminderEvent(int $reminderId, string $event, $oldNext, $newNext, $note, int $actorEmployeeId): void
    {
        if (!$this->hasTable('reminder_events'))
            return;

        DB::table('reminder_events')->insert([
            'reminder_id' => $reminderId,
            'event' => $event,
            'old_next_remind_at' => $oldNext ? Carbon::parse($oldNext)->toDateTimeString() : null,
            'new_next_remind_at' => $newNext ? Carbon::parse($newNext)->toDateTimeString() : null,
            'note' => $note ? (string) $note : null,
            'actor_employee_id' => $actorEmployeeId > 0 ? $actorEmployeeId : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }


    public function recentRecordReports(Request $request)
    {
        $type = strtolower(trim((string) $request->get('type', '')));
        $id = (int) $request->get('id');

        if (!in_array($type, self::TYPES, true) || $id <= 0) {
            return response()->json([
                'rows' => [],
            ]);
        }

        $rows = collect();

        /*
        |--------------------------------------------------------------------------
        | Appointment Reports
        |--------------------------------------------------------------------------
        */
        if ($type === 'appointment' && $this->hasTable('appointment_reports')) {
            $byCol = $this->hasColumn('appointment_reports', 'report_by')
                ? 'report_by'
                : ($this->hasColumn('appointment_reports', 'employee_id') ? 'employee_id' : null);

            $appointmentIdCol = $this->hasColumn('appointment_reports', 'appointment_id')
                ? 'appointment_id'
                : ($this->hasColumn('appointment_reports', 'main_appointment_id') ? 'main_appointment_id' : null);

            if ($byCol && $appointmentIdCol) {
                $reportByExpr = $this->appointmentReportEmployeeExpr('ar');

                $rows = DB::table('appointment_reports as ar')
                    ->leftJoin('employees as e', function ($join) use ($reportByExpr) {
                        $join->on('e.id', '=', DB::raw($reportByExpr));
                    })
                    ->where("ar.$appointmentIdCol", $id)
                    ->when($this->hasColumn('appointment_reports', 'deleted_at'), fn($q) => $q->whereNull('ar.deleted_at'))
                    ->orderByDesc('ar.created_at')
                    ->limit(100)
                    ->get([
                        'ar.id',
                        'ar.report',
                        'ar.report_date',
                        'ar.due_date',
                        'ar.next_step',
                        DB::raw("$reportByExpr as employee_id"),
                        DB::raw("
                            COALESCE(
                                NULLIF(TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))), ''),
                                CONCAT('Mitarbeiter #', $reportByExpr)
                            ) as employee_name
                        "),
                        'ar.created_at',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Task Reports
        |--------------------------------------------------------------------------
        */
        if ($type === 'task' && $this->hasTable('appointment_reports')) {
            $byCol = $this->hasColumn('appointment_reports', 'report_by')
                ? 'report_by'
                : ($this->hasColumn('appointment_reports', 'employee_id') ? 'employee_id' : null);

            $taskIdCol = $this->hasColumn('appointment_reports', 'task_id')
                ? 'task_id'
                : ($this->hasColumn('appointment_reports', 'personal_task_id') ? 'personal_task_id' : null);

            if ($byCol && $taskIdCol) {
                $rows = DB::table('appointment_reports as ar')
                    ->leftJoin('employees as e', 'e.id', '=', "ar.$byCol")
                    ->where("ar.$taskIdCol", $id)
                    ->when($this->hasColumn('appointment_reports', 'deleted_at'), fn($q) => $q->whereNull('ar.deleted_at'))
                    ->orderByDesc('ar.created_at')
                    ->limit(100)
                    ->get([
                        'ar.id',
                        'ar.report',
                        'ar.report_date',
                        'ar.due_date',
                        'ar.next_step',
                        DB::raw("ar.$byCol as employee_id"),
                        DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as employee_name"),
                        'ar.created_at',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Inquiry Reports
        |--------------------------------------------------------------------------
        */
        if ($type === 'inquiry' && $this->hasTable('inquiry_reports')) {
            $byCol = $this->hasColumn('inquiry_reports', 'report_by')
                ? 'report_by'
                : ($this->hasColumn('inquiry_reports', 'employee_id') ? 'employee_id' : null);

            if ($byCol) {
                $rows = DB::table('inquiry_reports as ir')
                    ->leftJoin('employees as e', 'e.id', '=', "ir.$byCol")
                    ->where('ir.inquiry_id', $id)
                    ->when($this->hasColumn('inquiry_reports', 'deleted_at'), fn($q) => $q->whereNull('ir.deleted_at'))
                    ->orderByDesc('ir.created_at')
                    ->limit(100)
                    ->get([
                        'ir.id',
                        'ir.report',
                        'ir.report_date',
                        'ir.due_date',
                        DB::raw("NULL as next_step"),
                        DB::raw("ir.$byCol as employee_id"),
                        DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as employee_name"),
                        'ir.created_at',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Ticket Reports
        |--------------------------------------------------------------------------
        */
        if ($type === 'ticket' && $this->hasTable('ticket_reports')) {
            $byCol = $this->hasColumn('ticket_reports', 'employee_id')
                ? 'employee_id'
                : ($this->hasColumn('ticket_reports', 'report_by') ? 'report_by' : null);

            if ($byCol) {
                $rows = DB::table('ticket_reports as tr')
                    ->leftJoin('employees as e', 'e.id', '=', "tr.$byCol")
                    ->where('tr.ticket_id', $id)
                    ->when($this->hasColumn('ticket_reports', 'deleted_at'), fn($q) => $q->whereNull('tr.deleted_at'))
                    ->orderByDesc('tr.created_at')
                    ->limit(100)
                    ->get([
                        'tr.id',
                        'tr.title',
                        'tr.report',
                        'tr.report_date',
                        DB::raw("NULL as due_date"),
                        DB::raw("NULL as next_step"),
                        DB::raw("tr.$byCol as employee_id"),
                        DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as employee_name"),
                        'tr.created_at',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Lead Reports
        |--------------------------------------------------------------------------
        */
        if ($type === 'lead' && $this->hasTable('customer_reports')) {
            $byCol = $this->hasColumn('customer_reports', 'report_by')
                ? 'report_by'
                : ($this->hasColumn('customer_reports', 'employee_id') ? 'employee_id' : null);

            $meta = $this->hasTable('lead_product_lists')
                ? DB::table('lead_product_lists')
                    ->where('id', $id)
                    ->first(['customer_id', 'alternative_id', 'product_id'])
                : null;

            if ($byCol && $meta) {
                $rows = DB::table('customer_reports as cr')
                    ->leftJoin('employees as e', 'e.id', '=', "cr.$byCol")
                    ->where('cr.customer_id', $meta->customer_id)
                    ->where('cr.alternative_id', $meta->alternative_id)
                    ->where('cr.product_id', $meta->product_id)
                    ->when($this->hasColumn('customer_reports', 'deleted_at'), fn($q) => $q->whereNull('cr.deleted_at'))
                    ->orderByDesc('cr.created_at')
                    ->limit(100)
                    ->get([
                        'cr.id',
                        'cr.stage',
                        'cr.report',
                        'cr.report_details',
                        DB::raw("NULL as report_date"),
                        DB::raw("NULL as due_date"),
                        DB::raw("NULL as next_step"),
                        DB::raw("cr.$byCol as employee_id"),
                        DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as employee_name"),
                        'cr.created_at',
                    ]);
            }
        }

        $rows = $rows->map(function ($row) {
            $report = trim((string) ($row->report ?? ''));

            if ($report === '' && !empty($row->report_details)) {
                $report = trim((string) $row->report_details);
            }

            return [
                'id' => (int) ($row->id ?? 0),
                'employee_id' => (int) ($row->employee_id ?? 0),
                'employee_name' => trim((string) ($row->employee_name ?? '')) ?: 'Unbekannt',
                'report_text' => $this->cleanText($report),
                'report_date' => $row->report_date ?? null,
                'due_date' => $row->due_date ?? null,
                'next_step' => $this->cleanText($row->next_step ?? ''),
                'created_at' => $row->created_at ?? null,
            ];
        })->values();

        return response()->json([
            'rows' => $rows,
        ]);
    }
    public function recentReportSourceDetails(Request $request)
    {
        $request->validate([
            'type' => ['required', 'string', Rule::in(self::TYPES)],
            'id' => ['required', 'integer', 'min:1'],
            'report_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $type = $request->string('type')->toString();
        $id = (int) $request->get('id');
        $reportId = (int) $request->get('report_id', 0);

        /**
         * Keep security check.
         * If you want admins to open every report without being assigned,
         * later we can adjust abortIfNotInvolved().
         */
        $this->abortIfNotInvolved($type, $id);

        return match ($type) {
            'appointment' => $this->recentAppointmentSourceDetails($id, $reportId),
            'task' => $this->recentTaskSourceDetails($id, $reportId),
            'ticket' => $this->recentTicketSourceDetails($id, $reportId),

            default => response()->json([
                'success' => true,
                'type' => $type,
                'id' => $id,
                'report' => null,
                'appointment' => null,
                'task' => null,
                'ticket' => null,
                'appointments' => [],
                'message' => 'Für diesen Typ gibt es aktuell keine erweiterten Details.',
            ]),
        };
    }


    private function recentTicketSourceDetails(int $id, int $reportId = 0)
    {
        $ticket = Problem::query()
            ->with([
                'customer',
                'alternative',
                'product',
                'responsibleEmployee:id,name,lastname,image,email,phone',
                'firstContactEmployee:id,name,lastname,image,email,phone',
                'startUser:id,name,lastname,image,email,phone',
                'progressUser:id,name,lastname,image,email,phone',
                'endUser:id,name,lastname,image,email,phone',
                'editUser:id,name,lastname,image,email,phone',
                'employees:id,name,lastname,image,email,phone',
                'ticketTasks',
                'errors',
                'comments',
                'appointments',
            ])
            ->findOrFail($id);

        $report = null;

        if ($reportId > 0 && $this->hasTable('ticket_reports')) {
            $report = TicketReport::query()
                ->with([
                    'employee:id,name,lastname,image,email,phone',
                    'customer',
                    'alternative',
                    'product',
                    'comments',
                ])
                ->where('ticket_id', $id)
                ->where('id', $reportId)
                ->first();
        }

        $reports = collect();

        if ($this->hasTable('ticket_reports')) {
            $reports = TicketReport::query()
                ->with([
                    'employee:id,name,lastname,image,email,phone',
                    'comments',
                ])
                ->where('ticket_id', $id)
                ->when($this->hasColumn('ticket_reports', 'deleted_at'), function ($q) {
                    $q->whereNull('deleted_at');
                })
                ->latest('report_date')
                ->latest('created_at')
                ->limit(20)
                ->get();
        }

        return response()->json([
            'success' => true,
            'type' => 'ticket',
            'id' => $id,
            'report' => $report,
            'ticket' => $ticket,
            'ticket_reports' => $reports,
            'appointment' => null,
            'task' => null,
            'appointments' => $ticket->appointments ?? [],
        ]);
    }

    private function recentAppointmentSourceDetails(int $id, int $reportId = 0)
    {
        $appointment = MainAppointment::query()
            ->with([
                'createdBy:id,name,lastname,image,email,phone',
                'changedBy:id,name,lastname,image,email,phone',
                'customer',
                'branch',
                'branchAddress',
                'employees:id,name,lastname,image,email,phone',
                'appointmentEmployees.employee:id,name,lastname,image,email,phone',
                'problem',
                'problemTask',
                'task',
                'reports.reporter:id,name,lastname,image,email,phone',
                'comments',
            ])
            ->findOrFail($id);

        $report = null;

        if ($reportId > 0 && $this->hasTable('appointment_reports')) {
            $report = AppointmentReport::query()
                ->with([
                    'employee:id,name,lastname,image,email,phone',
                    'author:id,name,lastname,image,email,phone',
                    'reporter:id,name,lastname,image,email,phone',
                ])
                ->where('appointment_id', $id)
                ->where('id', $reportId)
                ->first();
        }

        $task = null;

        if (!empty($appointment->task_id)) {
            $task = PersonalTask::query()
                ->with([
                    'assignedBy:id,name,lastname,image,email,phone',
                    'changedBy:id,name,lastname,image,email,phone',
                    'customer',
                    'product',
                    'employees:id,name,lastname,image,email,phone',
                    'steps',
                    'keys',
                    'attachments',
                    'histories',
                    'comments.author:id,name,lastname,image,email,phone',
                    'comments.children.author:id,name,lastname,image,email,phone',
                ])
                ->find($appointment->task_id);
        }

        return response()->json([
            'success' => true,
            'type' => 'appointment',
            'id' => $id,
            'report' => $report,
            'appointment' => $appointment,
            'task' => $task,
            'appointments' => [],
        ]);
    }


    private function recentTaskSourceDetails(int $id, int $reportId = 0)
    {
        $task = PersonalTask::query()
            ->with([
                'assignedBy:id,name,lastname,image,email,phone',
                'changedBy:id,name,lastname,image,email,phone',
                'customer',
                'product',
                'employees:id,name,lastname,image,email,phone',
                'steps',
                'keys',
                'attachments',
                'histories',
                'comments.author:id,name,lastname,image,email,phone',
                'comments.children.author:id,name,lastname,image,email,phone',
            ])
            ->findOrFail($id);

        $report = null;

        if ($reportId > 0 && $this->hasTable('personal_task_comments')) {
            $report = PersonalTaskComment::query()
                ->with([
                    'author:id,name,lastname,image,email,phone',
                    'employee:id,name,lastname,image,email,phone',
                ])
                ->where('task_id', $id)
                ->where('id', $reportId)
                ->first();
        }

        $appointments = collect();

        if ($this->hasTable('main_appointments')) {
            $appointments = MainAppointment::query()
                ->with([
                    'createdBy:id,name,lastname,image,email,phone',
                    'employees:id,name,lastname,image,email,phone',
                    'customer',
                    'comments',
                    'reports.reporter:id,name,lastname,image,email,phone',
                ])
                ->where('task_id', $id)
                ->when($this->hasColumn('main_appointments', 'deleted_at'), function ($q) {
                    $q->whereNull('deleted_at');
                })
                ->latest('start_date')
                ->limit(20)
                ->get();
        }

        return response()->json([
            'success' => true,
            'type' => 'task',
            'id' => $id,
            'report' => $report,
            'task' => $task,
            'appointment' => null,
            'appointments' => $appointments,
        ]);
    }

    private function remainingTicketCountForEmployee(int $employeeId, Carbon $threshold): int
    {
        if (!$this->hasTable('problems') || !$this->hasTable('employee_problem')) {
            return 0;
        }

        $query = DB::table('problems as p')
            ->join('employee_problem as ep', 'ep.problem_id', '=', 'p.id')
            ->where('ep.employee_id', $employeeId)
            ->when($this->hasColumn('problems', 'deleted_at'), fn($q) => $q->whereNull('p.deleted_at'))
            ->where(function ($q) use ($threshold) {
                if ($this->hasColumn('problems', 'updated_at')) {
                    $q->where('p.updated_at', '<=', $threshold);
                } elseif ($this->hasColumn('problems', 'date')) {
                    $q->whereDate('p.date', '<=', $threshold->toDateString());
                }
            })
            ->when($this->hasColumn('problems', 'status'), function ($q) {
                $q->whereNotIn('p.status', ['done', 'completed', 'closed', 'cancelled', 'canceled']);
            });

        if ($this->hasTable('ticket_reports')) {
            $query->whereNotExists(function ($sub) use ($employeeId) {
                $sub->select(DB::raw(1))
                    ->from('ticket_reports as tr')
                    ->whereColumn('tr.ticket_id', 'p.id')
                    ->where('tr.employee_id', $employeeId)
                    ->when($this->hasColumn('ticket_reports', 'deleted_at'), fn($x) => $x->whereNull('tr.deleted_at'));
            });
        }

        return (int) $query->distinct('p.id')->count('p.id');
    }


    private function remainingTaskCountForEmployee(int $employeeId, Carbon $threshold): int
    {
        if (!$this->hasTable('personal_tasks') || !$this->hasTable('employees_personal_tasks')) {
            return 0;
        }

        $query = DB::table('personal_tasks as t')
            ->join('employees_personal_tasks as ept', 'ept.task_id', '=', 't.id')
            ->where('ept.employee_id', $employeeId)
            ->whereNotIn('ept.status', ['reject', 'rejected'])
            ->when($this->hasColumn('personal_tasks', 'deleted_at'), fn($q) => $q->whereNull('t.deleted_at'))
            ->where(function ($q) {
                if ($this->hasColumn('personal_tasks', 'task_status')) {
                    $q->whereNull('t.task_status')
                        ->orWhereNotIn('t.task_status', ['canceled', 'cancelled', 'done', 'completed']);
                }
            })
            ->where(function ($q) use ($threshold) {
                if ($this->hasColumn('personal_tasks', 'due_date')) {
                    $q->whereDate('t.due_date', '<=', $threshold->toDateString());
                } elseif ($this->hasColumn('personal_tasks', 'updated_at')) {
                    $q->where('t.updated_at', '<=', $threshold);
                }
            });

        if ($this->hasTable('personal_task_comments')) {
            $query->whereNotExists(function ($sub) use ($employeeId) {
                $sub->select(DB::raw(1))
                    ->from('personal_task_comments as ptc')
                    ->whereColumn('ptc.task_id', 't.id')
                    ->where('ptc.comment_by', $employeeId)
                    ->when($this->hasColumn('personal_task_comments', 'deleted_at'), fn($x) => $x->whereNull('ptc.deleted_at'));
            });
        }

        return (int) $query->distinct('t.id')->count('t.id');
    }


    private function remainingAppointmentCountForEmployee(int $employeeId, Carbon $threshold): int
    {
        if (!$this->hasTable('main_appointments') || !$this->hasTable('main_appointment_employees')) {
            return 0;
        }

        $query = DB::table('main_appointments as a')
            ->join('main_appointment_employees as mae', 'mae.appointment_id', '=', 'a.id')
            ->where('mae.employee_id', $employeeId)
            ->whereNotIn('mae.status', ['reject', 'rejected'])
            ->when($this->hasColumn('main_appointments', 'deleted_at'), fn($q) => $q->whereNull('a.deleted_at'))
            ->where(function ($q) use ($threshold) {
                if ($this->hasColumn('main_appointments', 'end_date')) {
                    $q->whereDate('a.end_date', '<=', $threshold->toDateString());
                } elseif ($this->hasColumn('main_appointments', 'start_date')) {
                    $q->whereDate('a.start_date', '<=', $threshold->toDateString());
                } elseif ($this->hasColumn('main_appointments', 'updated_at')) {
                    $q->where('a.updated_at', '<=', $threshold);
                }
            });

        if ($this->hasTable('appointment_reports')) {
            $query->whereNotExists(function ($sub) use ($employeeId) {
                $sub->select(DB::raw(1))
                    ->from('appointment_reports as ar')
                    ->whereColumn('ar.appointment_id', 'a.id')
                    ->where('ar.employee_id', $employeeId)
                    ->when($this->hasColumn('appointment_reports', 'deleted_at'), fn($x) => $x->whereNull('ar.deleted_at'));
            });
        }

        return (int) $query->distinct('a.id')->count('a.id');
    }


    private function remainingInquiryCountForEmployee(int $employeeId, Carbon $threshold): int
    {
        if (!$this->hasTable('inquiries') || !$this->hasTable('inquiry_product_lists')) {
            return 0;
        }

        $query = DB::table('inquiries as i')
            ->join('inquiry_product_lists as ipl', 'ipl.inquiry_id', '=', 'i.id')
            ->where(function ($q) use ($employeeId) {
                $hasEmployee = false;

                if ($this->hasColumn('inquiry_product_lists', 'employee_id')) {
                    $q->orWhere('ipl.employee_id', $employeeId);
                    $hasEmployee = true;
                }

                if ($this->hasColumn('inquiry_product_lists', 'field_employee')) {
                    $q->orWhere('ipl.field_employee', $employeeId);
                    $hasEmployee = true;
                }

                if (!$hasEmployee) {
                    $q->whereRaw('1=0');
                }
            })
            ->when($this->hasColumn('inquiries', 'deleted_at'), fn($q) => $q->whereNull('i.deleted_at'))
            ->where(function ($q) use ($threshold) {
                if ($this->hasColumn('inquiries', 'updated_at')) {
                    $q->where('i.updated_at', '<=', $threshold);
                } elseif ($this->hasColumn('inquiries', 'created_at')) {
                    $q->where('i.created_at', '<=', $threshold);
                }
            });

        if ($this->hasTable('inquiry_reports')) {
            $reportByColumn = $this->hasColumn('inquiry_reports', 'report_by')
                ? 'report_by'
                : ($this->hasColumn('inquiry_reports', 'employee_id') ? 'employee_id' : null);

            if ($reportByColumn) {
                $query->whereNotExists(function ($sub) use ($employeeId, $reportByColumn) {
                    $sub->select(DB::raw(1))
                        ->from('inquiry_reports as ir')
                        ->whereColumn('ir.inquiry_id', 'i.id')
                        ->where("ir.$reportByColumn", $employeeId)
                        ->when($this->hasColumn('inquiry_reports', 'deleted_at'), fn($x) => $x->whereNull('ir.deleted_at'));
                });
            }
        }

        return (int) $query->distinct('i.id')->count('i.id');
    }


    private function remainingLeadCountForEmployee(int $employeeId, Carbon $threshold): int
    {
        if (!$this->hasTable('lead_product_lists')) {
            return 0;
        }

        $query = DB::table('lead_product_lists as lpl')
            ->when($this->hasColumn('lead_product_lists', 'deleted_at'), fn($q) => $q->whereNull('lpl.deleted_at'))
            ->where(function ($q) use ($threshold) {
                if ($this->hasColumn('lead_product_lists', 'updated_at')) {
                    $q->where('lpl.updated_at', '<=', $threshold);
                } elseif ($this->hasColumn('lead_product_lists', 'created_at')) {
                    $q->where('lpl.created_at', '<=', $threshold);
                }
            })
            ->where(function ($q) use ($employeeId) {
                $hasEmployee = false;

                if ($this->hasColumn('lead_product_lists', 'employee_id')) {
                    $q->orWhere('lpl.employee_id', $employeeId);
                    $hasEmployee = true;
                }

                if ($this->hasColumn('lead_product_lists', 'field_employee')) {
                    $q->orWhere('lpl.field_employee', $employeeId);
                    $hasEmployee = true;
                }

                if ($this->hasColumn('lead_product_lists', 'teams') && $this->supportsJsonSearch()) {
                    $q->orWhereRaw(
                        "JSON_SEARCH(lpl.teams, 'one', ?, NULL, '$[*].employee_id') IS NOT NULL",
                        [(string) $employeeId]
                    );
                    $hasEmployee = true;
                }

                if (!$hasEmployee) {
                    $q->whereRaw('1=0');
                }
            })
            ->when($this->hasColumn('lead_product_lists', 'status'), function ($q) {
                $q->whereNotIn('lpl.status', ['archive', 'junk', 'done', 'completed']);
            });

        if ($this->hasTable('customer_reports')) {
            $reportByColumn = $this->hasColumn('customer_reports', 'report_by')
                ? 'report_by'
                : ($this->hasColumn('customer_reports', 'employee_id') ? 'employee_id' : null);

            if ($reportByColumn) {
                $query->whereNotExists(function ($sub) use ($employeeId, $reportByColumn) {
                    $sub->select(DB::raw(1))
                        ->from('customer_reports as cr')
                        ->whereColumn('cr.customer_id', 'lpl.customer_id')
                        ->whereColumn('cr.alternative_id', 'lpl.alternative_id')
                        ->whereColumn('cr.product_id', 'lpl.product_id')
                        ->where("cr.$reportByColumn", $employeeId)
                        ->when($this->hasColumn('customer_reports', 'deleted_at'), fn($x) => $x->whereNull('cr.deleted_at'));
                });
            }
        }

        return (int) $query->distinct('lpl.id')->count('lpl.id');
    }

    public function recentReportsRecordReports(Request $request)
    {
        return $this->recentRecordReports($request);
    }
}
