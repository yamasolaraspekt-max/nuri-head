<?php

namespace App\Http\Controllers\Customer\Kanban;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\NewLeads;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ArticleGroup;
use App\Models\NewLeadImage;
use App\Models\LeadProductList;
use App\Models\LeadStage;
use App\Models\LeadAlternativeAdd;
use App\Models\CustomerAlternativeAdd;
use App\Models\Leave;
use App\Models\JobRepresentative;
use App\Models\CustomerResponsible;
use App\Models\NewLeadResponsibility;
use App\Models\Image;
use App\Models\CustomerProductList;
use App\Models\Customer;
use App\Models\ImageCategory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Inquiry;
use App\Models\Planing;
use Illuminate\Support\Facades\Validator;
use App\Models\TaskPhase;
use App\Models\PhaseActivities;
use App\Models\RadiatorInstallation;
use App\Models\CustomerHeatingCircuit;
use App\Models\CustomerRoomDimension;
use App\Notifications\LeadNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\PersonalTask;
use App\Models\PersonalTaskKey;
use App\Notifications\PersonalTaskNotification;
use App\Notifications\LeadResponsibleChange;
use App\Models\EmployeesPersonalTask;
use App\Models\HeatingType;
use App\Models\SubArticleGroup;
use App\Models\CustomerCart;
use App\Models\PVChecklist;
use App\Models\Deal;
use Illuminate\Support\Arr;
use Throwable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Expression;
use App\Models\MainAppointment;
use App\Models\MainAppointmentEmployee;
use Illuminate\Support\Facades\Auth;
use App\Events\LeadActivityBroadcast;
use App\Models\Stage;
use App\Models\LeadStageSubStage;
class LeadOverviewController extends Controller
{

    public function __construct()
    {
        // MASTER-01 P1-IDOR Customer-Rest: Belegkette-Gate (permission:Customer)
        $this->middleware('permission:Customer,delete')->only(['purge', 'appointmentsDestroy']);
        $this->middleware('permission:Customer,update')->only(['changeStage', 'ticketize', 'updateProgress']);
        $this->middleware('auth');
    }


    /**
     * Helper to log activity manually.
     * Resolves the Employee Name from the ID stored in auth()->user()->name.
     */

    /**
     * Helper to log activity manually.
     * Resolves the Employee Name from the ID stored in auth()->user()->name.
     */
    private function logActivity($event, $modelType, $modelId, $leadId, $altId = null, $prodId = null, $changes = [])
    {
        $userName = 'System';
        $employeeId = null;

        if (Auth::check()) {
            $employeeId = Auth::user()->name;
            $employee = DB::table('employees')->where('id', $employeeId)->first();
            if ($employee) {
                $userName = trim($employee->name . ' ' . $employee->lastname);
            } else {
                $userName = 'Mitarbeiter #' . $employeeId;
            }
        }

        // In DB speichern
        DB::table('lead_activity_logs')->insert([
            'new_leads_id' => $leadId,
            'alternative_id' => $altId,
            'product_id' => $prodId,
            'user_id' => Auth::id(),
            'user_name' => $employeeId ?? 'System',
            'event_type' => $event,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'changes' => json_encode($changes),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $classNameRaw = class_basename($modelType);
        $classNameDe = match ($classNameRaw) {
            'LeadProductList' => 'Prozess',
            'NewLeads' => 'Kunde',
            'Problem' => 'Ticket',
            'CustomerNote' => 'Notizen',
            'Appointment' => 'Termin',
            default => $classNameRaw
        };

        // Namen holen
        $customerName = 'Unbekannter Kunde';
        if ($leadId) {
            $customer = DB::table('new_leads')->find($leadId);
            if ($customer) {
                $customerName = trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? ''));
                if (empty($customerName))
                    $customerName = $customer->firma ?: '#' . $leadId;
            }
        }

        $productName = 'Allgemein';
        if ($prodId) {
            $product = DB::table('article_groups')->find($prodId);
            if ($product)
                $productName = $product->article_group;
        }

        $actionDe = match ($event) {
            'created' => 'erstellt', 'updated' => 'aktualisiert', 'deleted' => 'gelöscht', default => $event
        };

        // Dynamic stage labels from lead_stages, with safe fallback aliases.
        $stageDE = array_merge([
            'open' => 'Lead',
            'new' => 'Lead',
            'neue' => 'Lead',
            'lead' => 'Lead',
            'offer' => 'Angebot',
            'follow_up' => 'Nachfassen',
            'accepted' => 'Annehmen',
            'deal' => 'Auftrag',
            'project' => 'Montage',
            'completed' => 'Abschluss',
            'archive' => 'Archive',
            'archiv' => 'Archive',
            'junk' => 'Junk',
            'reject' => 'Junk',
            'cancel' => 'Abgebrochen',
        ], $this->leadStageLabels());

        $workStatusDE = [
            'playing' => 'Läuft',
            'paused' => 'Pausiert',
            'stopped' => 'Gestoppt'
        ];

        $detailText = "Eintrag {$actionDe}";

        if (!empty($changes['stage'])) {
            $fromRaw = strtolower($changes['stage']['from'] ?? '');
            $toRaw = strtolower($changes['stage']['to'] ?? '');

            $from = $stageDE[$fromRaw] ?? ucfirst($fromRaw ?: 'Unbekannt');
            $to = $stageDE[$toRaw] ?? ucfirst($toRaw ?: 'Unbekannt');

            $detailText = "Status: '{$from}' ➞ '{$to}'";

        } elseif (!empty($changes['work_status'])) {
            $fromRaw = strtolower($changes['work_status']['from'] ?? '');
            $toRaw = strtolower($changes['work_status']['to'] ?? '');

            $from = $workStatusDE[$fromRaw] ?? ucfirst($fromRaw ?: 'Unbekannt');
            $to = $workStatusDE[$toRaw] ?? ucfirst($toRaw ?: 'Unbekannt');

            $detailText = "Arbeitsstatus: '{$from}' ➞ '{$to}'";

        } elseif (!empty($changes['info'])) {
            $detailText = $changes['info'];
        }

        // Live an Reverb senden
        broadcast(new LeadActivityBroadcast([
            'customer_id' => $leadId,
            'product_id' => $prodId,
            'employee_id' => $employeeId,
            'action' => $event,
            'model_de' => $classNameDe,
            'customer_name' => $customerName,
            'product_name' => $productName,
            'creator_name' => $userName,
            'detail_text' => $detailText,
            'time' => now()->format('H:i')
        ]))->toOthers();
    }

    private function statusNormExpr(string $qualifiedCol = 'p.status', string $fallback = 'open'): Expression
    {
        // Ensures "p.status" if only "status" was passed
        if (!str_contains($qualifiedCol, '.')) {
            $qualifiedCol = 'p.' . $qualifiedCol;
        }

        // Treat NULL/'' as fallback (for tickets that default to "open"), then lowercase
        return DB::raw("LOWER(COALESCE(NULLIF({$qualifiedCol}, ''), '{$fallback}'))");

    }

    public function kanban(Request $request)
    {
        $includeClosed = $this->includeClosed($request);

        /*
        |--------------------------------------------------------------------------
        | Lightweight first page load
        |--------------------------------------------------------------------------
        | The heavy data is loaded by AJAX only when a tab is opened.
        | This prevents Hetzner/Nginx 504 on the Kanban screen.
        */
        $leadStages = $this->leadStagesForUi();

        $stageNames = $leadStages
            ->pluck('name', 'key')
            ->toArray();

        $stageMeta = $leadStages
            ->mapWithKeys(fn($stage) => [
                $stage->key => [
                    'id' => $stage->id,
                    'key' => $stage->key,
                    'name' => $stage->name,
                    'color' => $stage->color,
                    'icon' => $stage->icon,
                    'sort_order' => $stage->sort_order,
                    'is_default' => (bool) $stage->is_default,
                    'is_protected' => (bool) $stage->is_protected,
                    'is_closed' => (bool) $stage->is_closed,
                    'is_active' => (bool) $stage->is_active,
                    'sub_stages' => $stage->subStages
                        ->map(fn($subStage) => [
                            'id' => $subStage->id,
                            'key' => $subStage->key,
                            'name' => $subStage->name,
                            'color' => $subStage->color,
                            'icon' => $subStage->icon,
                            'sort_order' => $subStage->sort_order,
                            'is_default' => (bool) $subStage->is_default,
                            'is_active' => (bool) $subStage->is_active,
                        ])
                        ->values()
                        ->all(),
                ],
            ])
            ->toArray();

        // Stufe B: Board zeigt nur die 6 Phasen-Spalten. follow_up/accepted sind in offer/deal gefoldet;
        // archive/junk/ticket -> kein Spalten-Eintrag (Zugang via bestehende Tabs). lead_stages-Zeilen bleiben.
        $kanbanPhaseKeys = ['lead', 'offer', 'deal', 'project', 'abnahme', 'completed'];
        $kanbanStageNames = collect($stageNames)
            ->reject(fn($label, $key) => !in_array(strtolower((string) $key), $kanbanPhaseKeys, true))
            ->toArray();

        $kanbanStageMeta = collect($stageMeta)
            ->reject(fn($meta, $key) => !in_array(strtolower((string) $key), $kanbanPhaseKeys, true))
            ->toArray();

        $page = (int) $request->get('page', 1) ?: 1;
        $leads = new \Illuminate\Pagination\LengthAwarePaginator(
            collect(),
            0,
            15,
            $page,
            ['path' => $request->url(), 'pageName' => 'page']
        );

        $archive = new \Illuminate\Pagination\LengthAwarePaginator(
            collect(),
            0,
            10,
            (int) $request->get('archive_page', 1) ?: 1,
            ['path' => $request->url(), 'pageName' => 'archive_page']
        );

        $junk = new \Illuminate\Pagination\LengthAwarePaginator(
            collect(),
            0,
            10,
            (int) $request->get('junk_page', 1) ?: 1,
            ['path' => $request->url(), 'pageName' => 'junk_page']
        );

        $tickets = new \Illuminate\Pagination\LengthAwarePaginator(
            collect(),
            0,
            12,
            (int) $request->get('ticket_page', 1) ?: 1,
            ['path' => $request->url(), 'pageName' => 'ticket_page']
        );

        /*
        |--------------------------------------------------------------------------
        | Cheap dropdown data only
        |--------------------------------------------------------------------------
        */
        $employees = DB::table('employees')
            ->where('status', 'Active')
            ->select('id', 'name', 'lastname', 'image')
            ->orderBy('lastname')
            ->orderBy('name')
            ->get();

        // Personal-Task Teams for the Kanban task drawer.
        // Safe because some installations do not have a teams table yet.
        $teams = collect();
        if (Schema::hasTable('teams')) {
            $teamNameSelect = Schema::hasColumn('teams', 'name')
                ? 'name as name'
                : (Schema::hasColumn('teams', 'team_name')
                    ? 'team_name as name'
                    : "CONCAT('Team #', id) as name");

            $teams = DB::table('teams')
                ->when(Schema::hasColumn('teams', 'deleted_at'), fn($query) => $query->whereNull('deleted_at'))
                ->select('id', DB::raw($teamNameSelect))
                ->orderBy('name')
                ->get();
        }

        $products = DB::table('article_groups')
            ->select('id', 'article_group', 'initial')
            ->orderBy('article_group')
            ->get();

        $departments = DB::table('departments')
            ->whereNull('deleted_at')
            ->select('id', 'department_name')
            ->orderBy('department_name')
            ->get();

        $branches = DB::table('branches')
            ->select('id', 'branch', 'color')
            ->orderBy('branch')
            ->get();

        $branchAddresses = Schema::hasTable('branch_addresses')
            ? DB::table('branch_addresses')
                ->whereNull('deleted_at')
                ->select('id', 'branch_id', 'name', 'full_address', 'street', 'postcode', 'city', 'status')
                ->orderBy('branch_id')
                ->orderBy('name')
                ->orderBy('city')
                ->get()
            : collect();

        $kanbanFilterSettings = Schema::hasTable('kanban_filter_settings')
            ? \App\Models\KanbanFilterSetting::query()
                ->forCurrentUser()
                ->orderByDesc('is_default')
                ->orderBy('name')
                ->get(['id', 'name', 'filters', 'is_default'])
            : collect();

        $defaultKanbanFilterSetting = $kanbanFilterSettings->firstWhere('is_default', true);

        /*
        |--------------------------------------------------------------------------
        | Filter dropdown data
        |--------------------------------------------------------------------------
        | Customer select uses AJAX for full search, but we still load the latest
        | 250 customers as fallback so the dropdown is not empty when JS/cache fails.
        */
        $customers = DB::table('new_leads')
            ->whereNull('deleted_at')
            ->select('id', 'name', 'lastname', 'firma', 'email', 'phone')
            ->orderByDesc('id')
            ->limit(250)
            ->get();

        $totalEmployees = $employees->count();
        $totalProducts = $products->count();
        $totalDepartments = $departments->count();
        $totalCustomers = Schema::hasTable('new_leads')
            ? DB::table('new_leads')->whereNull('deleted_at')->count()
            : $customers->count();

        // Avoid multiple expensive count queries during initial page rendering.
        $statusCounts = [
            'offen' => 0,
            'zusage' => 0,
            'absage' => 0,
        ];

        $statusPercentages = [
            'offen' => 0,
            'zusage' => 0,
            'absage' => 0,
        ];

        $tabCounts = [
            'kanban' => 0,
            'list' => 0,
            'archive' => 0,
            'junk' => 0,
            'ticket' => 0,
            'value' => 0,
        ];

        return view('admin.kanban.kanban', compact(
            'leads',
            'archive',
            'junk',
            'tickets',
            'stageNames',
            'stageMeta',
            'kanbanStageNames',
            'kanbanStageMeta',
            'leadStages',
            'branches',
            'branchAddresses',
            'kanbanFilterSettings',
            'defaultKanbanFilterSetting',
            'employees',
            'teams',
            'products',
            'departments',
            'customers',
            'totalEmployees',
            'totalProducts',
            'totalCustomers',
            'totalDepartments',
            'statusCounts',
            'statusPercentages',
            'tabCounts'
        ));
    }

    public function kanbanFeed(Request $request)
    {
        $customerId = (int) $request->get('customer_id');
        $alternativeId = $request->get('alternative_id');
        $productId = $request->get('product_id');

        if (!$customerId) {
            return response()->json([
                'status' => 'error',
                'message' => 'customer_id ist erforderlich.',
            ], 422);
        }

        Carbon::setLocale('de');
        $now = Carbon::now();
        $items = collect();

        /*
         * -------------------- PERSONAL TASKS --------------------
         * - Limit + order in SQL (cheaper)
         */
        $taskQuery = DB::table('personal_tasks as t')
            ->leftJoin('employees as e', 'e.id', '=', 't.assigned_by')
            ->select(
                't.id',
                't.task_title',
                't.description',
                't.priority',
                't.task_status',
                't.reminder_date',
                't.reminder_time',
                't.start_date',
                't.due_date',
                't.customer_id',
                't.alternative_id',
                't.product_id',
                't.created_at',
                DB::raw("'task' as kind"),
                DB::raw("CONCAT(e.lastname,' ',e.name) as owner_name")
            )
            ->whereNull('t.deleted_at')
            ->where('t.customer_id', $customerId)
            ->when($alternativeId, fn($q) => $q->where('t.alternative_id', $alternativeId))
            ->when($productId, fn($q) => $q->where('t.product_id', $productId))
            ->orderByDesc(DB::raw("COALESCE(t.reminder_date, t.start_date, t.due_date, t.created_at)"))
            ->limit(20);

        $items = $items->merge(
            $taskQuery->get()->map(function ($r) use ($now) {
                $date = $r->reminder_date ?? $r->start_date ?? $r->due_date ?? $r->created_at;
                $time = $r->reminder_time ?? null;

                $at = $date
                    ? Carbon::parse($date . ($time ? (' ' . $time) : ''))
                    : null;

                return [
                    'id' => $r->id,
                    'type' => 'task',
                    'type_label' => 'Aufgabe',
                    'badge' => $r->task_status ?: 'Offen',
                    'title' => $r->task_title ?: 'Aufgabe',
                    'text' => Str::limit(strip_tags($r->description ?? ''), 160),
                    'priority' => $r->priority ?: 'normal',
                    'status' => $r->task_status,
                    'when' => $at?->toIso8601String(),
                    'when_human' => $at ? $at->diffForHumans($now, ['parts' => 2]) : null,
                    'owner_name' => $r->owner_name,
                    'customer_id' => $r->customer_id,
                    'alternative_id' => $r->alternative_id,
                    'product_id' => $r->product_id,
                    'icon' => 'icon-check-square',
                    'link' => route('personal.task.details', $r->id ?? 0) ?? null,
                ];
            })
        );

        /*
         * -------------------- APPOINTMENTS --------------------
         */
        $apQuery = DB::table('main_appointments as a')
            ->leftJoin('employees as e', 'e.id', '=', 'a.created_by')
            ->select(
                'a.id',
                'a.name',
                'a.note',
                'a.priority',
                'a.status',
                'a.appointment_type',
                'a.execution_type',
                'a.start_date',
                'a.start_time',
                'a.end_date',
                'a.end_time',
                'a.customer_id',
                'a.created_at',
                DB::raw("'appointment' as kind"),
                DB::raw("CONCAT(e.lastname,' ',e.name) as owner_name")
            )
            ->whereNull('a.deleted_at')
            ->where('a.customer_id', $customerId)
            ->orderByDesc(DB::raw("COALESCE(a.start_date, a.created_at)"))
            ->limit(20);

        $items = $items->merge(
            $apQuery->get()->map(function ($r) use ($now) {
                $date = $r->start_date ?? $r->created_at;
                $time = $r->start_time;

                $at = $date
                    ? Carbon::parse($date . ($time ? (' ' . $time) : ''))
                    : null;

                $timeLabel = $r->start_time
                    ? $r->start_time . ($r->end_time ? ' – ' . $r->end_time : '')
                    : null;

                $subtitle = trim(
                    implode(' • ', array_filter([
                        $r->appointment_type ?: null,
                        $r->execution_type ?: null,
                        $timeLabel,
                    ]))
                );

                return [
                    'id' => $r->id,
                    'type' => 'appointment',
                    'type_label' => 'Termin',
                    'badge' => $r->appointment_type ?: 'Termin',
                    'title' => $r->name ?: 'Termin',
                    'text' => $subtitle ?: Str::limit(strip_tags($r->note ?? ''), 160),
                    'priority' => $r->priority ?: 'normal',
                    'status' => $r->status,
                    'when' => $at?->toIso8601String(),
                    'when_human' => $at ? $at->diffForHumans($now, ['parts' => 2]) : null,
                    'owner_name' => $r->owner_name,
                    'customer_id' => $r->customer_id,
                    'alternative_id' => null,
                    'product_id' => null,
                    'icon' => 'icon-calendar',
                    'link' => route('appointment.details', $r->id ?? 0) ?? null,
                ];
            })
        );

        /*
         * -------------------- TICKETS (PROBLEMS) --------------------
         */
        $ticketQuery = DB::table('problems as p')
            ->leftJoin('employees as e', 'e.id', '=', 'p.responsible')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'p.product_id')
            ->whereNull('p.deleted_at')
            ->select(
                'p.id',
                'p.ticket_no',
                'p.problem',
                'p.priority',
                'p.status',
                'p.date',
                'p.progress_date',
                'p.end_date',
                'p.customer_id',
                'p.alternative_id',
                'p.product_id',
                'p.created_at',
                DB::raw("'ticket' as kind"),
                DB::raw("CONCAT(e.lastname,' ',e.name) as owner_name"),
                'ag.initial as product_initial'
            )
            ->whereNull('p.deleted_at')
            ->where('p.customer_id', $customerId)
            ->when($alternativeId, fn($q) => $q->where('p.alternative_id', $alternativeId))
            ->when($productId, fn($q) => $q->where('p.product_id', $productId))
            ->orderByDesc(DB::raw("COALESCE(p.date, p.progress_date, p.end_date, p.created_at)"))
            ->limit(20);

        $items = $items->merge(
            $ticketQuery->get()->map(function ($r) use ($now) {
                $date = $r->date ?? $r->progress_date ?? $r->end_date ?? $r->created_at;
                $at = $date ? Carbon::parse($date) : null;

                $subtitle = trim(
                    implode(' • ', array_filter([
                        $r->product_initial ?: null,
                        $r->status ?: null,
                    ]))
                );

                return [
                    'id' => $r->id,
                    'type' => 'ticket',
                    'type_label' => 'Ticket',
                    'badge' => 'Ticket #' . $r->ticket_no,
                    'title' => $subtitle ?: ('Ticket #' . $r->ticket_no),
                    'text' => Str::limit(strip_tags($r->problem ?? ''), 160),
                    'priority' => $r->priority ?: 'normal',
                    'status' => $r->status,
                    'when' => $at?->toIso8601String(),
                    'when_human' => $at ? $at->diffForHumans($now, ['parts' => 2]) : null,
                    'owner_name' => $r->owner_name,
                    'customer_id' => $r->customer_id,
                    'alternative_id' => $r->alternative_id,
                    'product_id' => $r->product_id,
                    'icon' => 'icon-alert-triangle',
                    'link' => route('problem.profile', $r->id ?? 0) ?? null,
                ];
            })
        );

        /*
         * -------------------- SORT + LIMIT (combined) --------------------
         * nearest to "now" (upcoming + recent mixed)
         */
        $items = $items
            ->filter(fn($i) => !empty($i['when']))
            ->sortBy(function ($i) use ($now) {
                $at = Carbon::parse($i['when']);
                return abs($now->diffInSeconds($at, false));
            })
            ->values()
            ->take(50);

        return response()->json([
            'status' => 'ok',
            'count' => $items->count(),
            'items' => $items->values(),
        ]);
    }


    /**
     * MODIFIED: Added $excludedStatuses param to allow fetching archive items when needed.
     */
    private function latestActiveLeadIds(bool $includeClosed = false, array $excludedStatuses = ['archive', 'archiv', 'junk', 'ticket']): \Illuminate\Database\Query\Builder
    {
        $q = DB::table('lead_product_lists as l1')
            ->select(DB::raw('MAX(l1.id) as id'))
            ->join('new_leads as nl', 'nl.id', '=', 'l1.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'l1.alternative_id')
            ->whereNull('l1.deleted_at')
            ->whereNull('nl.deleted_at')
            ->whereNull('alt.deleted_at');

        if (!$includeClosed) {
            $q->where(function ($w) use ($excludedStatuses) {
                $w->whereNull('l1.status')
                    ->orWhere('l1.status', '')
                    ->orWhereNotIn(DB::raw('LOWER(l1.status)'), $excludedStatuses);
            });
        }

        return $q->groupBy('l1.customer_id', 'l1.alternative_id', 'l1.product_id', 'l1.service_id');
    }


    public function purge(Request $request, int $id)
    {
        try {
            DB::beginTransaction();

            // Fetch lead BEFORE deleting to get IDs for logging
            $lead = DB::table('lead_product_lists')->where('id', $id)->first();

            if (!$lead) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Lead nicht gefunden.'], 404);
            }

            $cid = $lead->customer_id;
            $aid = $lead->alternative_id;
            $pid = $lead->product_id;
            $svc = $lead->service;

            // ★ LOG ACTIVITY (Must happen before delete)
            $this->logActivity('deleted', 'App\Models\LeadProductList', $id, $cid, $aid, $pid, [
                'info' => 'Lead endgültig gelöscht (Purge)',
                'last_status' => $lead->status
            ]);

            // Delete Related Records
            DB::table('planings')->where(['customer_id' => $cid, 'alternative_id' => $aid, 'product_id' => $pid])->delete();
            DB::table('offers')->where(['customer_id' => $cid, 'alternative_id' => $aid, 'product_id' => $pid])->delete();
            DB::table('projects')->where(['customer_id' => $cid, 'alternative_id' => $aid, 'product_id' => $pid])->delete();

            $dealIds = DB::table('deals')->where(['customer_id' => $cid, 'alternative_id' => $aid, 'product_id' => $pid])->pluck('id');
            if ($dealIds->isNotEmpty()) {
                // deal_invoices-Schiene stillgelegt 2026-07-05 (invoices = führend) — Alt-Schienen-Löschung entfernt.
                DB::table('deals')->whereIn('id', $dealIds)->delete();
            }

            $problemIds = DB::table('problems')->where(['customer_id' => $cid, 'alternative_id' => $aid, 'product_id' => $pid])->pluck('id');
            if ($problemIds->isNotEmpty()) {
                DB::table('employee_problem')->whereIn('problem_id', $problemIds)->delete();
                DB::table('problems')->whereIn('id', $problemIds)->delete();
            }

            DB::table('customer_histories')->where(['customer_id' => $cid, 'alternative_id' => $aid, 'product_id' => $pid])->delete();

            // Finally delete the lead
            DB::table('lead_product_lists')->where('id', $id)->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Lead endgültig gelöscht.']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Lead purge failed', ['id' => $id, 'err' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Serverfehler beim Löschen.'], 500);
        }
    }
    public function purgeLegacy(Request $request, int $id)
    {
        // For old links that do a window.location to GET /delete_lead_product/{id}
        // We can call the same purge code but keep semantics.
        $request->setMethod('DELETE');
        return $this->purge($request, $id);
    }

    public function searchForm(Request $request)
    {
        $base = $this->baseActiveQuery(false, ['junk', 'ticket'])
            ->select($this->baseSelectColumns());

        // This applies both filters AND the correct sorting via alias 'lpl'
        $this->applyCommonFilters($base, $request); // Der dritte Parameter existiert jetzt nicht mehr

        // DO NOT add another orderBy here using 'lead_product_lists.created_at' default
        // The applyCommonFilters above handles 'sort_by' request params correctly.

        $paginator = $base
            ->paginate(15)
            ->appends($request->all());

        // Transform results
        $items = $paginator->getCollection();
        $this->hydrateLeadTeams($items);
        $items->each(function ($lead) {
            $lead->employee = $lead->employee_id ? (object) [
                'employee_id' => $lead->employee_id,
                'name' => $lead->employee_name,
                'lastname' => $lead->employee_lastname,
                'image' => $lead->employee_image,
            ] : null;

            $lead->field_employee = $lead->field_employee_id ? (object) [
                'employee_id' => $lead->field_employee_id,
                'name' => $lead->field_employee_name,
                'lastname' => $lead->field_employee_lastname,
                'image' => $lead->field_employee_image,
            ] : null;

            $latest = DB::table('customer_histories')
                ->leftJoin('task_phases', 'task_phases.id', '=', 'customer_histories.phase_id')
                ->leftJoin('phase_activities', 'phase_activities.id', '=', 'customer_histories.activity_id')
                ->where('customer_histories.customer_id', $lead->customer_id)
                ->where('customer_histories.alternative_id', $lead->alternative_id)
                ->where('customer_histories.product_id', $lead->product_id)
                ->orderByDesc('customer_histories.updated_at')
                ->first(['task_phases.phase_name', 'phase_activities.title as activity_title', 'customer_histories.done_date', 'customer_histories.updated_at']);

            $lead->latest_phase = $latest->phase_name ?? null;
            $lead->latest_activity = $latest->activity_title ?? null;
            $lead->done_date = $latest->done_date ?? $latest->updated_at ?? null;
        });

        // Calculate totals
        $sub = DB::query()->fromSub(clone $base, 't');

        $stats = [
            'totalEmployees' => (clone $sub)->distinct()->count('t.employee_id'),
            'totalProducts' => (clone $sub)->distinct()->count('t.product_id'),
            'totalCustomers' => (clone $sub)->distinct()->count('t.customer_id'),
            'totalDepartments' => (clone $sub)->distinct()->count('t.department_id'),
            'total' => (clone $sub)->count(),
        ];

        $total = $stats['total'];
        $openStageKeys = $this->openStageKeys();
        $positiveStageKeys = $this->positiveStageKeys();

        $statusCounts = [
            'offen' => (clone $sub)->whereIn('t.stage', $openStageKeys)->count(),
            'zusage' => (clone $sub)->whereIn('t.stage', $positiveStageKeys)->count(),
            'absage' => (clone $sub)->whereNotIn('t.stage', array_unique(array_merge($openStageKeys, $positiveStageKeys)))->count(),
        ];

        $statusPercentages = collect($statusCounts)->map(fn($c) => $total ? round(($c / $total) * 100) : 0);

        return response()->json([
            'leads' => $items->values(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'prev_page_url' => $paginator->previousPageUrl(),
                'next_page_url' => $paginator->nextPageUrl(),
            ],
            ...$stats,
            'statusCounts' => $statusCounts,
            'statusPercentages' => $statusPercentages,
        ]);
    }

    public function getLead()
    {
        $stagesMap = $this->stageMap();

        // Latest history per tuple
        $latestHistory = DB::table('customer_histories as ch1')
            ->select('ch1.*')
            ->join(DB::raw('(
                SELECT MAX(id) AS max_id
                FROM customer_histories
                GROUP BY customer_id, alternative_id, product_id
            ) ch2'), 'ch1.id', '=', 'ch2.max_id');

        $rows = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'lead_product_lists.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('employees', 'employees.id', '=', 'lead_product_lists.employee_id')
            ->leftJoin('employees as field_emp', 'field_emp.id', '=', 'lead_product_lists.field_employee') // FIX: Außendienst-Join (fehlte -> getLead crashte bei field_employee_id)
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->leftJoinSub($latestHistory, 'history', function ($join) {
                $join->on('history.customer_id', '=', 'lead_product_lists.customer_id')
                    ->on('history.alternative_id', '=', 'lead_product_lists.alternative_id')
                    ->on('history.product_id', '=', 'lead_product_lists.product_id');
            })
            ->leftJoin('task_phases', 'task_phases.id', '=', 'history.phase_id')
            ->leftJoin('phase_activities', 'phase_activities.id', '=', 'history.activity_id')
            ->select(
                'new_leads.id as customer_id',
                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',
                'new_leads.firma',
                'new_leads.phone',
                'new_leads.email',
                'alt.id as alternative_id',
                'alt.street',
                'alt.postcode',
                'alt.city',
                'alt.object_name',
                'alt.request_date',
                'alt.periority as priority',
                'article_groups.id as product_id',
                'article_groups.initial',
                'lead_product_lists.id as lead_product_id',
                'lead_product_lists.stage_history',
                // FIX 1: Map 'open', NULL, and empty strings to 'lead'
                DB::raw("CASE WHEN lead_product_lists.status IS NULL OR lead_product_lists.status = '' OR LOWER(lead_product_lists.status) = 'open' THEN 'lead' ELSE LOWER(lead_product_lists.status) END as stage"),
                'lead_product_lists.work_status',
                'lead_product_lists.service',
                'lead_product_lists.service_id',
                'lead_product_lists.department_id',
                'lead_product_lists.interest',
                'lead_product_lists.created_at',
                'lead_product_lists.updated_at',
                'employees.id as employee_id',
                'employees.name as employee_name',
                'employees.lastname as employee_lastname',
                'employees.image as employee_image',
                'field_emp.id as field_employee_id',
                'field_emp.name as field_employee_name',
                'field_emp.lastname as field_employee_lastname',
                'field_emp.image as field_employee_image',
                'departments.department_name',
                'phase_sections.phase_section as phase_section_title',
                'phase_sections.status as phase_status',
                'task_phases.phase_name as latest_phase',
                'phase_activities.title as latest_activity'
            )
            ->whereNull('lead_product_lists.deleted_at')
            ->whereNull('alt.deleted_at')
            ->whereNull('new_leads.deleted_at')
            // FIX 2: Properly exclude items that shouldn't be in the active Kanban
            ->whereNotIn(DB::raw('LOWER(COALESCE(lead_product_lists.status, ""))'), ['archive', 'archiv', 'junk', 'ticket'])
            ->get();
        $this->hydrateLeadTeams($rows);

        $leads = $rows->map(function ($lead) use ($stagesMap) {
            $lead->stage = $stagesMap[strtolower($lead->stage ?? 'lead')] ?? 'lead';

            // Innendienst / canonical
            $lead->employee = $lead->employee_id ? (object) [
                'employee_id' => $lead->employee_id,
                'name' => $lead->employee_name,
                'lastname' => $lead->employee_lastname,
                'image' => $lead->employee_image,
            ] : null;

            // Außendienst – NEW
            $lead->field_employee = $lead->field_employee_id ? (object) [
                'employee_id' => $lead->field_employee_id,
                'name' => $lead->field_employee_name,
                'lastname' => $lead->field_employee_lastname,
                'image' => $lead->field_employee_image,
            ] : null;

            $lead->department = $lead->department_name
                ? (object) ['name' => $lead->department_name]
                : null;

            $lead->phase_section = $lead->phase_section_title
                ? (object) ['title' => $lead->phase_section_title]
                : null;

            unset(
                $lead->department_name,
                $lead->phase_section_title,
                $lead->employee_name,
                $lead->employee_lastname,
                $lead->employee_image,
                $lead->field_employee_name,
                $lead->field_employee_lastname,
                $lead->field_employee_image
            );

            return $lead;
        });



        return response()->json($leads);
    }

    public function search(Request $request)
    {
        $startedAt = microtime(true);

        try {
            $stagesMap = $this->stageMap();

            $requestedStage = $request->get('stage');
            $defaultLimit = $requestedStage ? 20 : 300;
            $limit = (int) $request->get('limit', $defaultLimit);
            $limit = max(1, min($limit, 100));

            $offset = (int) $request->get('offset', 0);
            $offset = max(0, $offset);

            $includeClosed = $this->includeClosed($request);

            $q = $this->baseActiveQuery($includeClosed, ['junk', 'ticket']);

            if (Schema::hasTable('customer_histories')) {
                $latestHistoryIds = DB::table('customer_histories')
                    ->selectRaw('MAX(id) AS max_id')
                    ->groupBy('customer_id', 'alternative_id', 'product_id');

                $latestHistory = DB::table('customer_histories as ch')
                    ->joinSub($latestHistoryIds, 'mxh', function ($join) {
                        $join->on('ch.id', '=', 'mxh.max_id');
                    })
                    ->select([
                        'ch.id',
                        'ch.customer_id',
                        'ch.alternative_id',
                        'ch.product_id',
                        Schema::hasColumn('customer_histories', 'phase_id') ? 'ch.phase_id' : DB::raw('NULL as phase_id'),
                        Schema::hasColumn('customer_histories', 'activity_id') ? 'ch.activity_id' : DB::raw('NULL as activity_id'),
                        Schema::hasColumn('customer_histories', 'done_date') ? 'ch.done_date' : DB::raw('NULL as done_date'),
                        Schema::hasColumn('customer_histories', 'updated_at') ? 'ch.updated_at' : DB::raw('NULL as updated_at'),
                    ]);

                $q->leftJoinSub($latestHistory, 'history', function ($join) {
                    $join->on('history.customer_id', '=', 'lpl.customer_id')
                        ->on('history.alternative_id', '=', 'lpl.alternative_id')
                        ->on('history.product_id', '=', 'lpl.product_id');
                });
            } else {
                $q->leftJoin(DB::raw('(SELECT NULL as customer_id, NULL as alternative_id, NULL as product_id, NULL as phase_id, NULL as activity_id, NULL as done_date, NULL as updated_at) as history'), function ($join) {
                    $join->whereRaw('1 = 0');
                });
            }

            if (Schema::hasTable('phase_sections') && Schema::hasColumn('lead_product_lists', 'service_id')) {
                $q->leftJoin('phase_sections', 'phase_sections.id', '=', 'lpl.service_id');
            }

            if (Schema::hasTable('task_phases')) {
                $q->leftJoin('task_phases', 'task_phases.id', '=', 'history.phase_id');
            }

            if (Schema::hasTable('phase_activities')) {
                $q->leftJoin('phase_activities', 'phase_activities.id', '=', 'history.activity_id');
            }

            $selectExtras = [];
            $selectExtras[] = Schema::hasTable('phase_sections') && Schema::hasColumn('phase_sections', 'phase_section')
                ? 'phase_sections.phase_section as phase_section_title'
                : DB::raw('NULL as phase_section_title');

            $selectExtras[] = Schema::hasTable('task_phases') && Schema::hasColumn('task_phases', 'phase_name')
                ? DB::raw('task_phases.phase_name as latest_phase')
                : DB::raw('NULL as latest_phase');

            $selectExtras[] = Schema::hasTable('phase_activities') && Schema::hasColumn('phase_activities', 'title')
                ? DB::raw('phase_activities.title as latest_activity')
                : DB::raw('NULL as latest_activity');

            $selectExtras[] = DB::raw('COALESCE(history.done_date, history.updated_at) as done_date');

            $q->select(array_merge($this->baseSelectColumns(), $selectExtras));

            $this->applyCommonFilters($q, $request, true);

            $rows = $q
                ->when($offset > 0, fn($query) => $query->offset($offset))
                ->limit($limit + 1)
                ->get();

            $limited = $rows->count() > $limit;
            if ($limited) {
                $rows = $rows->take($limit)->values();
            }

            $this->hydrateLeadTeamsForKanbanFast($rows);

            $keyById = $this->leadStageKeyById();
            $foldToParent = ['follow_up' => 'offer', 'accepted' => 'deal'];

            $leads = $rows->map(function ($lead) use ($keyById, $foldToParent) {
                // Stufe B: Spalten-Key FK-first (deckt Backfill-Fold); Fallback gefoldeter Status + Log bei NULL-FK.
                if (!empty($lead->lead_stage_id) && isset($keyById[(int) $lead->lead_stage_id])) {
                    $rawStage = $keyById[(int) $lead->lead_stage_id];
                } else {
                    if (empty($lead->lead_stage_id)) {
                        Log::warning('kanban fk-fallback', [
                            'lpl_id' => $lead->lead_product_id ?? null,
                            'status' => $lead->stage ?? null,
                        ]);
                    }
                    $rawStage = $this->normalizeStage((string) ($lead->stage ?? 'lead'));
                }
                $lead->stage = $foldToParent[$rawStage] ?? $rawStage;

                $lead->employee = $lead->employee_id ? (object) [
                    'employee_id' => $lead->employee_id,
                    'name' => $lead->employee_name,
                    'lastname' => $lead->employee_lastname,
                    'image' => $lead->employee_image,
                ] : null;

                $lead->field_employee = $lead->field_employee_id ? (object) [
                    'employee_id' => $lead->field_employee_id,
                    'name' => $lead->field_employee_name,
                    'lastname' => $lead->field_employee_lastname,
                    'image' => $lead->field_employee_image,
                ] : null;

                $lead->department = $lead->department_name
                    ? (object) ['name' => $lead->department_name]
                    : null;

                $lead->phase_section = $lead->phase_section_title
                    ? (object) ['title' => $lead->phase_section_title]
                    : null;

                unset(
                    $lead->department_name,
                    $lead->phase_section_title,
                    $lead->employee_name,
                    $lead->employee_lastname,
                    $lead->employee_image,
                    $lead->field_employee_name,
                    $lead->field_employee_lastname,
                    $lead->field_employee_image
                );

                return $lead;
            });

            Log::info('[LeadOverview::search] loaded', [
                'rows' => $leads->count(),
                'limited' => $limited,
                'limit' => $limit,
                'offset' => $offset,
                'stage' => $requestedStage,
                'ms' => round((microtime(true) - $startedAt) * 1000),
            ]);

            return response()->json([
                'success' => true,
                'leads' => $leads->values(),
                'limited' => $limited,
                'limit' => $limit,
                'offset' => $offset,
                'next_offset' => $offset + $leads->count(),
                'has_more' => $limited,
                'stage' => $requestedStage,
                'ms' => round((microtime(true) - $startedAt) * 1000),
            ]);
        } catch (Throwable $e) {
            Log::error('[LeadOverview::search] failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kanban konnte nicht geladen werden.',
                'error' => $e->getMessage(),
                'file' => basename($e->getFile()),
                'line' => $e->getLine(),
            ], 500);
        }
    }


    /**
     * Fast Kanban team hydration.
     * Does not call hydrateLeadOfferWorkflow() or hydrateKanbanNextStepSummaries()
     * because those can add heavy extra queries on large boards.
     */
    private function hydrateLeadTeamsForKanbanFast($leads): void
    {
        $col = $leads instanceof \Illuminate\Support\Collection ? $leads : collect($leads);

        if ($col->isEmpty()) {
            return;
        }

        $decodedByKey = [];
        $employeeIds = [];

        foreach ($col as $idx => $lead) {
            $currentStage = $this->normalizeStage((string) ($lead->stage ?? $lead->status ?? 'lead'));
            $assignments = $this->decodeLeadTeamAssignments($lead->teams ?? null, $currentStage);
            $decodedByKey[$idx] = [$assignments, $currentStage];

            foreach ($assignments as $assignment) {
                $employeeId = (int) ($assignment['employee_id'] ?? 0);
                $assignedBy = (int) ($assignment['assigned_by'] ?? 0);
                if ($employeeId > 0) {
                    $employeeIds[] = $employeeId;
                }
                if ($assignedBy > 0) {
                    $employeeIds[] = $assignedBy;
                }
            }
        }

        $employees = collect();
        $employeeIds = array_values(array_unique(array_filter($employeeIds)));

        if ($employeeIds) {
            $employees = Employee::query()
                ->select('id', 'name', 'lastname', 'image')
                ->whereIn('id', $employeeIds)
                ->get()
                ->keyBy('id');
        }

        foreach ($col as $idx => $lead) {
            [$assignments, $currentStage] = $decodedByKey[$idx] ?? [[], 'lead'];

            $decorated = collect($assignments)
                ->map(function ($assignment) use ($employees, $currentStage) {
                    $employeeId = (int) ($assignment['employee_id'] ?? 0);
                    $member = $employees->get($employeeId);

                    if (!$member) {
                        return null;
                    }

                    $stage = $this->normalizeStage((string) ($assignment['stage'] ?? $currentStage), $currentStage);
                    $assignedById = !empty($assignment['assigned_by']) ? (int) $assignment['assigned_by'] : null;

                    return [
                        'employee_id' => $employeeId,
                        'member' => $member,
                        'stage' => $stage,
                        'stage_label' => ucfirst(str_replace('_', ' ', $stage)),
                        'old_stage' => $assignment['old_stage'] ?? null,
                        'old_stage_label' => !empty($assignment['old_stage']) ? ucfirst(str_replace('_', ' ', $assignment['old_stage'])) : null,
                        'assigned_by' => $assignedById,
                        'assigned_by_user' => $assignedById ? $employees->get($assignedById) : null,
                        'assigned_at' => $assignment['assigned_at'] ?? null,
                        'assigned_at_iso' => $assignment['assigned_at'] ?? null,
                    ];
                })
                ->filter()
                ->values();

            $current = $decorated
                ->filter(fn($row) => ($row['stage'] ?? null) === $currentStage)
                ->values();

            $lead->teams = $assignments;
            $lead->team_assignments = $decorated->all();
            $lead->current_team_assignments = $current->all();
            $lead->team_members = $current->pluck('member')->filter()->values()->all();
            $lead->team_ids = $current->pluck('employee_id')->filter()->values()->all();

            // Defaults expected by the card/list renderers.
            $lead->offer_workflow = $lead->offer_workflow ?? null;
            $lead->kanban_next_step = $lead->kanban_next_step ?? null;
            $lead->next_task_title = $lead->next_task_title ?? null;
            $lead->kanban_open_task_count = $lead->kanban_open_task_count ?? 0;
            $lead->kanban_done_task_count = $lead->kanban_done_task_count ?? 0;
        }
    }

    /**
     * Decode lead_product_lists.teams into a stable historical assignment list.
     *
     * Supported formats:
     * - legacy: [18, 23]
     * - current: [{"employee_id":18,"stage":"follow_up","old_stage":"offer","assigned_by":7,"assigned_at":"..."}]
     *
     * IMPORTANT:
     * The returned list is historical. Do not treat every item as the current team.
     * Use current stage filtering when building team_members/team_ids.
     */
    private function decodeLeadTeamAssignments($raw, ?string $fallbackStage = null): array
    {
        if (is_string($raw) && trim($raw) !== '') {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [];
        }

        if ($raw instanceof \Illuminate\Support\Collection) {
            $raw = $raw->toArray();
        }

        if (!is_array($raw)) {
            $raw = [];
        }

        $fallbackStage = $this->normalizeStage($fallbackStage ?: 'lead');
        $assignments = [];

        foreach ($raw as $row) {
            if (is_object($row)) {
                $row = (array) $row;
            }

            if (is_array($row)) {
                $employeeId = (int) ($row['employee_id'] ?? $row['id'] ?? $row['emp_id'] ?? 0);
                if ($employeeId <= 0) {
                    continue;
                }

                $stage = $this->normalizeStage((string) ($row['stage'] ?? $fallbackStage), $fallbackStage);
                $oldStage = trim((string) ($row['old_stage'] ?? ''));

                $assignments[] = [
                    'employee_id' => $employeeId,
                    'stage' => $stage,
                    'old_stage' => $oldStage !== '' ? $this->normalizeStage($oldStage, $oldStage) : null,
                    'assigned_by' => !empty($row['assigned_by']) ? (int) $row['assigned_by'] : null,
                    'assigned_at' => $row['assigned_at'] ?? null,
                ];

                continue;
            }

            // Legacy array of IDs.
            $employeeId = (int) $row;
            if ($employeeId <= 0) {
                continue;
            }

            $assignments[] = [
                'employee_id' => $employeeId,
                'stage' => $fallbackStage,
                'old_stage' => null,
                'assigned_by' => null,
                'assigned_at' => null,
            ];
        }

        return array_values($assignments);
    }

    /**
     * Attach employee/assigner objects to team assignments and split:
     * - team_assignments: full historical list
     * - current_team_assignments/team_members/team_ids: only current stage
     */
    private function decorateLeadTeamAssignments(array $assignments, ?string $currentStage = null): array
    {
        $currentStage = $this->normalizeStage($currentStage ?: 'lead');

        $ids = [];
        foreach ($assignments as $row) {
            $eid = (int) ($row['employee_id'] ?? 0);
            $ab = (int) ($row['assigned_by'] ?? 0);
            if ($eid > 0)
                $ids[] = $eid;
            if ($ab > 0)
                $ids[] = $ab;
        }

        $employees = collect();
        $ids = array_values(array_unique(array_filter($ids)));

        if (!empty($ids)) {
            $employees = \App\Models\Employee::query()
                ->select('id', 'name', 'lastname', 'image')
                ->whereIn('id', $ids)
                ->get()
                ->keyBy('id');
        }

        $stageLabels = array_merge([
            'lead' => 'Lead',
            'offer' => 'Angebot',
            'follow_up' => 'Nachfassen',
            'accepted' => 'Annehmen',
            'deal' => 'Auftrag',
            'project' => 'Montage',
            'completed' => 'Abschluss',
            'archive' => 'Archiv',
            'archiv' => 'Archiv',
            'junk' => 'Junk',
            'ticket' => 'Ticket',
        ], $this->leadStageLabels());

        $decorated = collect($assignments)
            ->map(function ($a) use ($employees, $stageLabels, $currentStage) {
                $memberId = (int) ($a['employee_id'] ?? 0);
                $member = $employees->get($memberId);

                if (!$member) {
                    return null;
                }

                $stageKey = $this->normalizeStage((string) ($a['stage'] ?? $currentStage), $currentStage);
                $oldKey = !empty($a['old_stage'])
                    ? $this->normalizeStage((string) $a['old_stage'], (string) $a['old_stage'])
                    : null;

                $assignerId = !empty($a['assigned_by']) ? (int) $a['assigned_by'] : null;
                $assigner = $assignerId ? $employees->get($assignerId) : null;

                $assignedAtIso = null;
                if (!empty($a['assigned_at'])) {
                    try {
                        $assignedAtIso = \Illuminate\Support\Carbon::parse($a['assigned_at'])->toIso8601String();
                    } catch (\Throwable $e) {
                        $assignedAtIso = null;
                    }
                }

                return [
                    'employee_id' => $memberId,
                    'member' => $member,
                    'stage' => $stageKey,
                    'stage_label' => $stageLabels[$stageKey] ?? ucfirst(str_replace('_', ' ', $stageKey)),
                    'old_stage' => $oldKey,
                    'old_stage_label' => $oldKey ? ($stageLabels[$oldKey] ?? ucfirst(str_replace('_', ' ', $oldKey))) : null,
                    'assigned_by' => $assignerId,
                    'assigned_by_user' => $assigner,
                    'assigned_at' => $a['assigned_at'] ?? null,
                    'assigned_at_iso' => $assignedAtIso,
                ];
            })
            ->filter()
            ->values();

        $current = $decorated
            ->filter(fn($row) => ($row['stage'] ?? null) === $currentStage)
            ->values();

        return [
            'team_assignments' => $decorated->all(),
            'current_team_assignments' => $current->all(),
            'team_members' => $current->pluck('member')->filter()->values()->all(),
            'team_ids' => $current->pluck('employee_id')->filter()->values()->all(),
        ];
    }



    /**
     * Preload the next-step/task sequence for Kanban cards and list rows.
     *
     * This prevents the card/list "Nächster Schritt" from waiting until the task modal opens.
     * It only loads compact summary data, not the full task modal payload.
     */
    private function hydrateKanbanNextStepSummaries($leads): void
    {
        $col = $leads instanceof \Illuminate\Support\Collection ? $leads : collect($leads);

        if ($col->isEmpty()) {
            return;
        }

        try {
            $col->transform(function ($lead) {
                $lead->stage_landed_at = $this->resolveStageLandedAt($lead);
                $lead->previous_kanban_task_title = null;
                $lead->current_kanban_task_title = null;
                $lead->next_kanban_task_title = null;
                $lead->next_task_title = null;
                $lead->next_kanban_task_description = null;
                $lead->next_kanban_task_source = null;
                $lead->kanban_open_task_count = 0;
                $lead->kanban_done_task_count = 0;
                $lead->kanban_next_step = null;

                return $lead;
            });

            $leadProductIds = $col
                ->map(fn($lead) => (int) ($lead->lead_product_id ?? $lead->lead_product_list_id ?? $lead->id ?? 0))
                ->filter()
                ->unique()
                ->values();

            $savedByLeadProduct = collect();

            if ($leadProductIds->isNotEmpty() && Schema::hasTable('kanban_lead_tasks')) {
                $savedByLeadProduct = DB::table('kanban_lead_tasks as klt')
                    ->select([
                        'klt.id',
                        'klt.lead_product_list_id',
                        'klt.title',
                        'klt.description',
                        'klt.status',
                        'klt.estimated_minutes',
                        'klt.planned_start_at',
                        'klt.planned_end_at',
                        'klt.done_at',
                        'klt.created_at',
                        'klt.updated_at',
                    ])
                    ->whereNull('klt.deleted_at')
                    ->whereIn('klt.lead_product_list_id', $leadProductIds)
                    ->orderByRaw("
                        CASE klt.status
                            WHEN 'in_progress' THEN 1
                            WHEN 'scheduled' THEN 2
                            WHEN 'open' THEN 3
                            WHEN 'done' THEN 4
                            ELSE 5
                        END
                    ")
                    ->orderByRaw('COALESCE(klt.planned_start_at, klt.created_at) ASC')
                    ->get()
                    ->groupBy('lead_product_list_id');
            }

            $stageIdsByKey = LeadStage::query()
                ->pluck('id', 'key')
                ->mapWithKeys(fn($id, $key) => [$this->normalizeStage((string) $key) => (int) $id])
                ->toArray();

            $templateRows = collect();

            if (Schema::hasTable('task_phases')) {
                $phaseTitleColumn = Schema::hasColumn('task_phases', 'phase_name')
                    ? 'tp.phase_name'
                    : DB::raw('NULL as phase_name');

                $phaseDescriptionColumn = Schema::hasColumn('task_phases', 'description')
                    ? 'tp.description'
                    : DB::raw('NULL as phase_description');

                $activityTitleColumn = Schema::hasTable('phase_activities') && Schema::hasColumn('phase_activities', 'title')
                    ? 'pa.title'
                    : DB::raw('NULL as activity_title');

                $activityDescriptionColumn = Schema::hasTable('phase_activities') && Schema::hasColumn('phase_activities', 'description')
                    ? 'pa.description'
                    : DB::raw('NULL as activity_description');

                $activityDurationColumn = Schema::hasTable('phase_activities') && Schema::hasColumn('phase_activities', 'duration')
                    ? 'pa.duration'
                    : DB::raw('NULL as activity_duration');

                $q = DB::table('task_phases as tp')
                    ->when(
                        Schema::hasTable('phase_activities'),
                        fn($query) => $query->leftJoin('phase_activities as pa', 'pa.phase_id', '=', 'tp.id'),
                        fn($query) => $query->leftJoin('phase_activities as pa', function ($join) {
                            $join->whereRaw('1 = 0');
                        })
                    )
                    ->select([
                        'tp.id as phase_id',
                        'tp.product_id',
                        Schema::hasColumn('task_phases', 'lead_stage_id') ? 'tp.lead_stage_id' : DB::raw('NULL as lead_stage_id'),
                        Schema::hasColumn('task_phases', 'lead_sub_stage_id') ? 'tp.lead_sub_stage_id' : DB::raw('NULL as lead_sub_stage_id'),
                        Schema::hasColumn('task_phases', 'order') ? 'tp.order' : DB::raw('0 as phase_order'),
                        $phaseTitleColumn,
                        $phaseDescriptionColumn,
                        DB::raw('pa.id as activity_id'),
                        $activityTitleColumn,
                        $activityDescriptionColumn,
                        $activityDurationColumn,
                    ])
                    ->when(Schema::hasColumn('task_phases', 'deleted_at'), fn($query) => $query->whereNull('tp.deleted_at'))
                    ->when(Schema::hasColumn('task_phases', 'status'), function ($query) {
                        $query->where(function ($q) {
                            $q->whereNull('tp.status')->orWhere('tp.status', 'Published');
                        });
                    })
                    ->when(Schema::hasTable('phase_activities') && Schema::hasColumn('phase_activities', 'deleted_at'), fn($query) => $query->whereNull('pa.deleted_at'));

                $productIds = $col->map(fn($lead) => (int) ($lead->product_id ?? 0))->filter()->unique()->values();

                if ($productIds->isNotEmpty()) {
                    $q->whereIn('tp.product_id', $productIds);
                }

                $templateRows = $q
                    ->orderByRaw(Schema::hasColumn('task_phases', 'order') ? 'tp.`order` ASC' : 'tp.id ASC')
                    ->orderBy('tp.id')
                    ->orderBy('pa.id')
                    ->get();
            }

            $col->transform(function ($lead) use ($savedByLeadProduct, $templateRows, $stageIdsByKey) {
                $leadProductId = (int) ($lead->lead_product_id ?? $lead->lead_product_list_id ?? $lead->id ?? 0);
                $tasks = $savedByLeadProduct->get($leadProductId, collect());

                $doneTasks = $tasks->filter(fn($task) => strtolower((string) $task->status) === 'done')->values();
                $openTasks = $tasks->reject(fn($task) => in_array(strtolower((string) $task->status), ['done', 'cancelled'], true))->values();

                $previous = $doneTasks
                    ->sortByDesc(fn($task) => $task->done_at ?? $task->updated_at ?? $task->created_at)
                    ->first();

                $current = $openTasks->first();

                $stageKey = $this->normalizeStage((string) ($lead->stage ?? $lead->status ?? 'lead'));
                $stageId = $stageIdsByKey[$stageKey] ?? null;
                $subStageId = (int) ($lead->lead_stage_sub_stage_id ?? 0);
                $productId = (int) ($lead->product_id ?? 0);

                $template = null;
                if (!$current && $productId > 0 && $stageId) {
                    $template = $templateRows
                        ->filter(function ($row) use ($productId, $stageId, $subStageId) {
                            if ((int) ($row->product_id ?? 0) !== $productId) {
                                return false;
                            }

                            if (!empty($row->lead_stage_id) && (int) $row->lead_stage_id !== (int) $stageId) {
                                return false;
                            }

                            $rowSub = (int) ($row->lead_sub_stage_id ?? 0);
                            if ($subStageId > 0 && $rowSub > 0 && $rowSub !== $subStageId) {
                                return false;
                            }

                            if ($subStageId <= 0 && $rowSub > 0) {
                                return false;
                            }

                            return true;
                        })
                        ->first();
                }

                $title = $current?->title
                    ?: ($template?->activity_title ?: $template?->phase_name)
                    ?: ($lead->latest_activity ?? $lead->latest_phase ?? null);

                $description = $current?->description
                    ?: ($template?->activity_description ?: $template?->phase_description)
                    ?: null;

                $lead->previous_kanban_task_title = $previous?->title;
                $lead->current_kanban_task_title = $current?->title;
                $lead->next_kanban_task_title = $title;
                $lead->next_task_title = $title;
                $lead->next_kanban_task_description = $description;
                $lead->next_kanban_task_source = $current ? 'saved_task' : ($template ? 'task_phase' : null);
                $lead->kanban_open_task_count = $openTasks->count();
                $lead->kanban_done_task_count = $doneTasks->count();
                $lead->kanban_next_step = [
                    'title' => $title,
                    'description' => $description,
                    'source' => $lead->next_kanban_task_source,
                    'stage_landed_at' => $lead->stage_landed_at,
                    'previous_title' => $lead->previous_kanban_task_title,
                    'open_count' => $lead->kanban_open_task_count,
                    'done_count' => $lead->kanban_done_task_count,
                ];

                return $lead;
            });
        } catch (Throwable $e) {
            Log::warning('Kanban next-step summary preload failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function resolveStageLandedAt($lead): ?string
    {
        $stage = $this->normalizeStage((string) ($lead->stage ?? $lead->status ?? 'lead'));

        $history = $lead->stage_history ?? [];
        if (is_string($history)) {
            $decoded = json_decode($history, true);
            $history = is_array($decoded) ? $decoded : [];
        }

        if ($history instanceof \Illuminate\Support\Collection) {
            $history = $history->toArray();
        }

        if (is_array($history)) {
            foreach (array_reverse($history) as $row) {
                if (is_object($row)) {
                    $row = (array) $row;
                }

                if (!is_array($row)) {
                    continue;
                }

                $toStage = $this->normalizeStage((string) ($row['to'] ?? $row['stage'] ?? ''));
                if ($toStage === $stage) {
                    return $row['changed_at'] ?? $row['created_at'] ?? $row['updated_at'] ?? null;
                }
            }
        }

        return $lead->updated_at ?? $lead->created_at ?? null;
    }



    private function hydrateLeadTeams($leads)
    {
        $col = $leads instanceof \Illuminate\Support\Collection ? $leads : collect($leads);

        $col->transform(function ($lead) {
            $currentStage = $this->normalizeStage((string) ($lead->stage ?? $lead->status ?? 'lead'));
            $assignments = $this->decodeLeadTeamAssignments($lead->teams ?? null, $currentStage);
            $decorated = $this->decorateLeadTeamAssignments($assignments, $currentStage);

            // Keep raw historical assignment data in "teams" for JSON/API consumers.
            $lead->teams = $assignments;

            // Full history for the modal / SweetAlert.
            $lead->team_assignments = $decorated['team_assignments'];

            // Current phase only for compact card display and stage-change preselection.
            $lead->current_team_assignments = $decorated['current_team_assignments'];
            $lead->team_members = $decorated['team_members'];
            $lead->team_ids = $decorated['team_ids'];

            return $lead;
        });

        $this->hydrateLeadOfferWorkflow($col);
        $this->hydrateKanbanNextStepSummaries($col);

        return $leads;
    }

    /**
     * Adds the matching Offer/Auftrag Kanban status to Lead Kanban rows.
     *
     * Matching key:
     * - customer_id
     * - alternative_id
     * - product_id
     *
     * This lets the Lead Kanban card show, for example:
     * "Angebot versendet" or "Material bestellt" when the lead is already in
     * the Offer/Deal workflow.
     */
    private function hydrateLeadOfferWorkflow($leads): void
    {
        $col = $leads instanceof \Illuminate\Support\Collection ? $leads : collect($leads);

        if ($col->isEmpty() || !Schema::hasTable('offers')) {
            return;
        }

        $keys = $col
            ->map(function ($lead) {
                return [
                    'customer_id' => (int) ($lead->customer_id ?? 0),
                    'alternative_id' => (int) ($lead->alternative_id ?? 0),
                    'product_id' => (int) ($lead->product_id ?? 0),
                ];
            })
            ->filter(fn($row) => $row['customer_id'] > 0 && $row['alternative_id'] > 0 && $row['product_id'] > 0)
            ->unique(fn($row) => $row['customer_id'] . ':' . $row['alternative_id'] . ':' . $row['product_id'])
            ->values();

        if ($keys->isEmpty()) {
            return;
        }

        $offerHasDeletedAt = Schema::hasColumn('offers', 'deleted_at');
        $folderTableExists = Schema::hasTable('offer_folders');
        $folderHasDeletedAt = $folderTableExists && Schema::hasColumn('offer_folders', 'deleted_at');

        $query = DB::table('offers as o')
            ->when($folderTableExists, function ($query) {
                $query->leftJoin('offer_folders as f', 'f.offer_id', '=', 'o.id');
            })
            ->when($offerHasDeletedAt, fn($query) => $query->whereNull('o.deleted_at'))
            ->where(function ($query) use ($keys) {
                foreach ($keys as $row) {
                    $query->orWhere(function ($sub) use ($row) {
                        $sub->where('o.customer_id', $row['customer_id'])
                            ->where('o.alternative_id', $row['alternative_id'])
                            ->where('o.product_id', $row['product_id']);
                    });
                }
            });

        if ($folderTableExists && $folderHasDeletedAt) {
            $query->where(function ($q) {
                $q->whereNull('f.deleted_at')->orWhereNull('f.id');
            });
        }

        $select = [
            'o.id as offer_id',
            'o.customer_id',
            'o.alternative_id',
            'o.product_id',
        ];

        $select[] = Schema::hasColumn('offers', 'offer_no')
            ? 'o.offer_no'
            : DB::raw('NULL as offer_no');

        if ($folderTableExists) {
            $select[] = 'f.id as folder_id';
            $select[] = Schema::hasColumn('offer_folders', 'name') ? 'f.name as folder_name' : DB::raw('NULL as folder_name');
            $select[] = Schema::hasColumn('offer_folders', 'document_status') ? 'f.document_status as folder_document_status' : DB::raw('NULL as folder_document_status');
            $select[] = Schema::hasColumn('offer_folders', 'offer_status') ? 'f.offer_status' : DB::raw('NULL as offer_status');
            $select[] = Schema::hasColumn('offer_folders', 'deal_status') ? 'f.deal_status' : DB::raw('NULL as deal_status');
            $select[] = Schema::hasColumn('offer_folders', 'status') ? 'f.status as folder_status' : DB::raw('NULL as folder_status');
            $select[] = Schema::hasColumn('offer_folders', 'updated_at') ? 'f.updated_at as folder_updated_at' : DB::raw('NULL as folder_updated_at');
            $select[] = Schema::hasColumn('offer_folders', 'created_at') ? 'f.created_at as folder_created_at' : DB::raw('NULL as folder_created_at');
        } else {
            $select[] = DB::raw('NULL as folder_id');
            $select[] = DB::raw('NULL as folder_name');
            $select[] = DB::raw('NULL as folder_document_status');
            $select[] = DB::raw('NULL as offer_status');
            $select[] = DB::raw('NULL as deal_status');
            $select[] = DB::raw('NULL as folder_status');
            $select[] = DB::raw('NULL as folder_updated_at');
            $select[] = DB::raw('NULL as folder_created_at');
        }

        $rows = $query
            ->select($select)
            ->orderByDesc(DB::raw('COALESCE(f.updated_at, f.created_at, o.updated_at, o.created_at)'))
            ->get();

        $stageMeta = $this->offerWorkflowStageMeta();

        $byKey = [];
        foreach ($rows as $row) {
            $key = ((int) $row->customer_id) . ':' . ((int) $row->alternative_id) . ':' . ((int) $row->product_id);
            if (!isset($byKey[$key])) {
                $byKey[$key] = $row;
            }
        }

        $col->transform(function ($lead) use ($byKey, $stageMeta) {
            $key = ((int) ($lead->customer_id ?? 0)) . ':' . ((int) ($lead->alternative_id ?? 0)) . ':' . ((int) ($lead->product_id ?? 0));
            $row = $byKey[$key] ?? null;

            $leadStage = $this->normalizeStage((string) ($lead->stage ?? 'lead'));
            $isOfferLikeStage = in_array($leadStage, ['offer', 'deal', 'auftrag'], true);

            if (!$row && !$isOfferLikeStage) {
                $lead->offer_workflow = null;
                return $lead;
            }

            $documentStatus = $this->normalizeOfferWorkflowDocumentStatus(
                (string) ($row->folder_document_status ?? ($leadStage === 'deal' || $leadStage === 'auftrag' ? 'deal' : 'offer'))
            );

            $statusKey = $documentStatus === 'deal'
                ? (string) ($row->deal_status ?? $row->folder_status ?? 'open')
                : (string) ($row->offer_status ?? $row->folder_status ?? 'draft');

            $statusKey = strtolower(trim($statusKey ?: ($documentStatus === 'deal' ? 'open' : 'draft')));

            $meta = $stageMeta[$documentStatus][$statusKey]
                ?? $this->fallbackOfferWorkflowStageMeta($documentStatus, $statusKey);

            $lead->offer_workflow = [
                'exists' => (bool) $row,
                'offer_id' => $row?->offer_id ? (int) $row->offer_id : null,
                'offer_no' => $row->offer_no ?? null,
                'folder_id' => $row?->folder_id ? (int) $row->folder_id : null,
                'folder_name' => $row->folder_name ?? null,
                'document_status' => $documentStatus,
                'document_status_label' => $documentStatus === 'deal' ? 'Auftrag' : 'Angebot',
                'status_key' => $statusKey,
                'status_label' => $meta['label'] ?? ucfirst(str_replace('_', ' ', $statusKey)),
                'status_color' => $meta['color'] ?? ($documentStatus === 'deal' ? '#10b981' : '#74b2d4'),
                'stage_position' => $meta['position'] ?? null,
                'updated_at' => $row->folder_updated_at ?? $row->folder_created_at ?? null,
                'url' => !empty($row?->folder_id) ? url('/admin/offers/folders/' . $row->folder_id) : null,
            ];

            return $lead;
        });
    }

    private function normalizeOfferWorkflowDocumentStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));

        return in_array($status, ['deal', 'auftrag'], true) ? 'deal' : 'offer';
    }

    private function offerWorkflowStageMeta(): array
    {
        $meta = [
            'offer' => [],
            'deal' => [],
        ];

        if (!Schema::hasTable('offer_kanban_stages')) {
            return $meta;
        }

        $rows = DB::table('offer_kanban_stages')
            ->select([
                'document_status',
                'key',
                'label',
                'color',
                'position',
            ])
            ->where('is_active', true)
            ->when(Schema::hasColumn('offer_kanban_stages', 'deleted_at'), fn($query) => $query->whereNull('deleted_at'))
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        foreach ($rows as $row) {
            $doc = $this->normalizeOfferWorkflowDocumentStatus((string) $row->document_status);
            $key = strtolower((string) $row->key);

            $meta[$doc][$key] = [
                'label' => $row->label,
                'color' => $row->color ?: ($doc === 'deal' ? '#10b981' : '#74b2d4'),
                'position' => (int) ($row->position ?? 0),
            ];
        }

        return $meta;
    }

    private function fallbackOfferWorkflowStageMeta(string $documentStatus, string $statusKey): array
    {
        $fallback = [
            'offer' => [
                'draft' => 'Entwurf',
                'pending_approval' => 'Wartet auf Freigabe',
                'sent' => 'Gesendet',
                'viewed' => 'Gesehen',
                'negotiation' => 'Verhandlung',
                'revised' => 'Überarbeitet',
                'accepted' => 'Akzeptiert',
                'rejected' => 'Abgelehnt',
                'expired' => 'Abgelaufen',
                'cancelled' => 'Storniert',

                'lead_anfrage' => 'Lead / Anfrage',
                'erstkontakt' => 'Erstkontakt',
                'beratung_geplant' => 'Beratung geplant',
                'beratung_durchgefuehrt' => 'Beratung durchgeführt',
                'daten_unterlagen_fehlen' => 'Daten / Unterlagen fehlen',
                'technische_pruefung' => 'Technische Prüfung',
                'angebot_in_erstellung' => 'Angebot in Erstellung',
                'angebot_versendet' => 'Angebot versendet',
                'rueckfrage_nachbearbeitung' => 'Rückfrage / Nachbearbeitung',
                'warten_auf_entscheidung' => 'Warten auf Entscheidung',
                'angebot_angenommen' => 'Angebot angenommen',
                'angebot_abgelehnt' => 'Angebot abgelehnt',
                'angebot_pausiert' => 'Angebot pausiert',
            ],
            'deal' => [
                'open' => 'Offen',
                'qualified' => 'Qualifiziert',
                'proposal' => 'Angebotsphase',
                'negotiation' => 'Verhandlung',
                'won' => 'Gewonnen',
                'lost' => 'Verloren',
                'on_hold' => 'Pausiert',

                'auftrag_erhalten' => 'Auftrag erhalten',
                'auftragspruefung' => 'Auftragsprüfung',
                'vertrag_bestaetigung_versendet' => 'Vertrag / Bestätigung versendet',
                'anzahlung_offen' => 'Anzahlung offen',
                'anzahlung_erhalten' => 'Anzahlung erhalten',
                'materialplanung' => 'Materialplanung',
                'material_bestellt' => 'Material bestellt',
                'material_vollstaendig_verfuegbar' => 'Material vollständig verfügbar',
                'montage_terminplanung' => 'Montage / Terminplanung',
                'montagetermin_bestaetigt' => 'Montagetermin bestätigt',
                'in_ausfuehrung' => 'In Ausführung',
                'montage_abgeschlossen' => 'Montage abgeschlossen',
                'abnahme_qualitaetskontrolle' => 'Abnahme / Qualitätskontrolle',
                'rechnung_erstellt' => 'Rechnung erstellt',
                'rechnung_bezahlt' => 'Rechnung bezahlt',
                'abgeschlossen' => 'Abgeschlossen',
                'problem_reklamation' => 'Problem / Reklamation',
            ],
        ];

        $doc = $this->normalizeOfferWorkflowDocumentStatus($documentStatus);

        return [
            'label' => $fallback[$doc][$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey)),
            'color' => $doc === 'deal' ? '#10b981' : '#74b2d4',
            'position' => null,
        ];
    }



    public function changeStage(
        Request $request,
        $customer_id,
        $alternative_id,
        $product_id,
        $employee_id = null,
        $service = null,
        $stage = null,
        $service_id = null,
        $department_id = null
    ) {
        Log::info('🔄 changeStage', [
            'params' => compact('customer_id', 'alternative_id', 'product_id', 'employee_id', 'service', 'stage', 'service_id', 'department_id'),
            'body' => $request->all(),
        ]);

        $cid = (int) $customer_id;
        $aid = (int) $alternative_id;
        $pid = (int) $product_id;

        if ($cid <= 0 || $aid <= 0 || $pid <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ungültige IDs.',
            ], 422);
        }

        $incomingStage = $stage ?? $request->input('stage', 'open');
        $newStage = $this->normalizeStage((string) $incomingStage);

        if (!$this->stageExists($newStage)) {
            return response()->json([
                'success' => false,
                'message' => 'Diese Phase existiert nicht oder ist deaktiviert.',
            ], 422);
        }

        $leadIdFromBody = $request->integer('lead_product_id') ?: null;

        if ($leadIdFromBody) {
            $lead = DB::table('lead_product_lists')
                ->where('id', $leadIdFromBody)
                ->whereNull('deleted_at')
                ->first(['id', 'customer_id', 'alternative_id', 'product_id', 'status', 'teams', 'stage_history']);

            if (!$lead) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lead nicht gefunden (ID).',
                ], 404);
            }
        } else {
            $lead = DB::table('lead_product_lists')
                ->where('customer_id', $cid)
                ->where('alternative_id', $aid)
                ->where('product_id', $pid)
                ->whereNull('deleted_at')
                ->orderByDesc(DB::raw('COALESCE(updated_at, created_at)'))
                ->first(['id', 'customer_id', 'alternative_id', 'product_id', 'status', 'teams', 'stage_history']);

            if (!$lead) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kein Lead gefunden.',
                ], 404);
            }
        }

        $assignedBy = (int) (auth()->user()->name ?? 0); // employee_id in your app
        $actor = $assignedBy > 0 ? $assignedBy : (auth()->user()->name ?? 'system');

        $description = trim(strip_tags((string) $request->input('description', '')));
        $description = $description !== '' ? $description : null;

        $teamIds = $request->input('teams', []);
        if (!is_array($teamIds)) {
            $teamIds = [];
        }

        $teamIds = array_values(array_unique(array_filter(array_map('intval', $teamIds))));

        try {
            $allTeamAssignments = [];
            $currentDecorated = [];
            $oldStage = null;

            DB::transaction(function () use ($lead, $newStage, $actor, $description, $teamIds, $assignedBy, &$allTeamAssignments, &$currentDecorated, &$oldStage) {
                $locked = DB::table('lead_product_lists')
                    ->where('id', $lead->id)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->first();

                if (!$locked) {
                    throw new \RuntimeException('Lead nicht gefunden (Lock).');
                }

                $oldStage = $this->normalizeStage((string) ($locked->status ?? 'lead'));

                $history = [];
                if (!empty($locked->stage_history)) {
                    $decoded = json_decode($locked->stage_history, true);
                    if (is_array($decoded)) {
                        $history = $decoded;
                    }
                }

                $existingAssignments = $this->decodeLeadTeamAssignments($locked->teams ?? null, $oldStage);

                /*
                 * Preserve all previous phase teams.
                 * Replace only the team of the target/new stage, so editing the current
                 * phase does not duplicate old entries for the same phase.
                 */
                $historicalAssignments = collect($existingAssignments)
                    ->reject(fn($row) => $this->normalizeStage((string) ($row['stage'] ?? 'lead')) === $newStage)
                    ->values()
                    ->all();

                $newStageAssignments = array_values(array_map(function ($eid) use ($newStage, $oldStage, $assignedBy) {
                    return [
                        'employee_id' => (int) $eid,
                        'stage' => (string) $newStage,
                        'old_stage' => (string) $oldStage,
                        'assigned_by' => $assignedBy ?: null,
                        'assigned_at' => now()->toDateTimeString(),
                    ];
                }, $teamIds));

                $allTeamAssignments = array_values(array_merge($historicalAssignments, $newStageAssignments));

                $history[] = [
                    'from' => $oldStage,
                    'to' => $newStage,
                    'stage' => $newStage,
                    'team_ids' => $teamIds,
                    'changed_by' => $actor,
                    'changed_at' => now()->toDateTimeString(),
                    'description' => $description,
                ];

                $payload = [
                    'status' => $newStage,
                    'stage_history' => json_encode($history, JSON_UNESCAPED_UNICODE),
                    'teams' => json_encode($allTeamAssignments, JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ];

                if (Schema::hasColumn('lead_product_lists', 'lead_product_status')) {
                    $payload['lead_product_status'] = $newStage;
                }

                // Stufe A (additiv): FK-Stage-Bindung zusaetzlich schreiben; Legacy `status`
                // bleibt exakt wie bisher die Bruecke. Unbekannter Key -> null + Log, kein Crash.
                if (Schema::hasColumn('lead_product_lists', 'lead_stage_id')) {
                    $targetStageId = LeadStage::query()->where('key', $newStage)->value('id');
                    if ($targetStageId === null) {
                        Log::warning('changeStage: kein lead_stages.id fuer Stage-Key', [
                            'key' => $newStage,
                            'lead_product_list_id' => $locked->id,
                        ]);
                    }
                    $payload['lead_stage_id'] = $targetStageId;
                }

                DB::table('lead_product_lists')
                    ->where('id', $locked->id)
                    ->update($payload);

                $currentDecorated = $this->decorateLeadTeamAssignments($allTeamAssignments, $newStage);

                $this->logActivity(
                    'updated',
                    'App\Models\LeadProductList',
                    $locked->id,
                    $locked->customer_id,
                    $locked->alternative_id,
                    $locked->product_id,
                    [
                        'stage' => ['from' => $oldStage, 'to' => $newStage],
                        'teams_count' => count($teamIds),
                        'info' => 'Status & Teams geändert',
                    ]
                );
            });

            return response()->json([
                'success' => true,
                'message' => 'Phase & Team erfolgreich geändert.',
                'final' => [
                    'id' => $lead->id,
                    'status' => $newStage,
                    'old_stage' => $oldStage,
                    'teams' => $allTeamAssignments,
                    'team_ids' => $currentDecorated['team_ids'] ?? [],
                    'team_members' => $currentDecorated['team_members'] ?? [],
                    'current_team_assignments' => $currentDecorated['current_team_assignments'] ?? [],
                    'team_assignments' => $currentDecorated['team_assignments'] ?? [],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('changeStage failed', [
                'err' => $e->getMessage(),
                'cid' => $cid,
                'aid' => $aid,
                'pid' => $pid,
                'lead_id' => $lead->id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Serverfehler: ' . $e->getMessage(),
            ], 500);
        }
    }


    /** Base query for ARCHIVE/JUNK with standard joins. */
    private function baseArchiveQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('lead_product_lists as lpl')
            ->join('new_leads', 'new_leads.id', '=', 'lpl.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'lpl.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'lpl.product_id')
            ->leftJoin('branches as br', 'br.id', '=', 'new_leads.branch')
            ->leftJoin('employees', 'employees.id', '=', 'lpl.employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'lpl.department_id')
            ->whereNull('lpl.deleted_at')
            ->whereNull('new_leads.deleted_at')
            ->whereNull('alt.deleted_at')
            ->select(
                'lpl.id as lead_product_id',
                'lpl.stage_history', // <--- ADD THIS LINE
                DB::raw("LOWER(COALESCE(NULLIF(lpl.status,''),'lead')) as stage"),
                'lpl.work_status',
                'lpl.updated_at',
                'lpl.created_at',
                'lpl.service',
                'lpl.service_id',
                'lpl.department_id',
                'lpl.interest',
                'new_leads.id as customer_id',
                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',
                'new_leads.phone',
                'new_leads.email',
                'new_leads.firma',
                'new_leads.branch as branch_id',
                'br.branch as branch_name',
                'br.initial as branch_initial',
                'alt.id as alternative_id',
                'alt.street',
                'alt.postcode',
                'alt.city',
                'alt.object_name',
                'article_groups.id as product_id',
                'article_groups.initial',
                'employees.id as employee_id',
                'employees.name as employee_name',
                'employees.lastname as employee_lastname',
                'employees.image as employee_image',
                'departments.department_name'
            );
    }

    /**
     * Apply filters to archive/junk (DO NOT exclude by stage; caller enforces stage).
     * Requires baseArchiveQuery() to have:
     *  - leftJoin('branches as br', 'br.id', '=', 'new_leads.branch')
     */
    private function applyArchiveJunkFilters(\Illuminate\Database\Query\Builder $q, Request $request): void
    {
        // -------------------- Free text search --------------------
        if (($term = trim((string) $request->input('search', ''))) !== '') {

            // main OR block for text fields + branch
            $q->where(function ($qq) use ($term) {
                $qq->where('new_leads.name', 'like', "%{$term}%")
                    ->orWhere('new_leads.lastname', 'like', "%{$term}%")
                    ->orWhere('alt.street', 'like', "%{$term}%")
                    ->orWhere('alt.city', 'like', "%{$term}%")
                    ->orWhere('alt.postcode', 'like', "%{$term}%")
                    ->orWhere('article_groups.article_group', 'like', "%{$term}%")
                    // ✅ BRANCH searchable (name + initial)
                    ->orWhere('br.branch', 'like', "%{$term}%")
                    ->orWhere('br.initial', 'like', "%{$term}%");
            });

            // employee name search (keep INSIDE same search intention)
            $empIds = DB::table('employees')
                ->where('name', 'like', "%{$term}%")
                ->orWhere('lastname', 'like', "%{$term}%")
                ->pluck('id');

            if ($empIds->isNotEmpty()) {
                // IMPORTANT: keep within search context, but don't break other filters.
                $q->orWhereIn('lpl.employee_id', $empIds->all());
            }
        }

        // -------------------- Dropdown filters --------------------
        if ($request->filled('customer'))
            $q->where('lpl.customer_id', $request->customer);
        if ($request->filled('product'))
            $q->where('lpl.product_id', $request->product);
        if ($request->filled('department'))
            $q->where('lpl.department_id', $request->department);

        // ✅ BRANCH dropdown filter (id or name/initial)
        if ($request->filled('branch')) {
            $branchRaw = trim((string) $request->branch);

            if (ctype_digit($branchRaw)) {
                $q->where('new_leads.branch', (int) $branchRaw);
            } else {
                $branchIds = DB::table('branches')
                    ->where('branch', 'like', "%{$branchRaw}%")
                    ->orWhere('initial', 'like', "%{$branchRaw}%")
                    ->pluck('id');

                if ($branchIds->isNotEmpty()) {
                    $q->whereIn('new_leads.branch', $branchIds->all());
                } else {
                    $q->whereRaw('1=0');
                }
            }
        }

        // employee filter (id or name)
        if ($request->filled('employee')) {
            $emp = trim((string) $request->employee);

            if (ctype_digit($emp)) {
                $q->where('lpl.employee_id', (int) $emp);
            } else {
                $ids = DB::table('employees')
                    ->where('name', 'like', "%{$emp}%")
                    ->orWhere('lastname', 'like', "%{$emp}%")
                    ->pluck('id');

                if ($ids->isNotEmpty())
                    $q->whereIn('lpl.employee_id', $ids->all());
                else
                    $q->whereRaw('1=0');
            }
        }

        if ($request->filled('interest'))
            $q->where('lpl.interest', $request->interest);
        if ($request->filled('date_from'))
            $q->whereDate('lpl.created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $q->whereDate('lpl.created_at', '<=', $request->date_to);

        // -------------------- Sorting --------------------
        $sortBy = $request->get('sort_by', 'lpl.updated_at');
        $sortDir = strtolower($request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowed = [
            'lpl.created_at',
            'lpl.updated_at',
            'lpl.status',
            'lpl.employee_id',
            'lpl.department_id',
            'new_leads.id',
            'new_leads.lastname',
            'alt.city',
            // ✅ allow branch sorting too if you want
            'new_leads.branch',
            'br.branch',
            'br.initial',
        ];

        if (!in_array($sortBy, $allowed, true))
            $sortBy = 'lpl.updated_at';

        $q->orderBy($sortBy, $sortDir);
    }

    /** Apply filters to archive/junk (DO NOT exclude by stage; caller enforces stage). */

    public function archivePartial(Request $request)
    {
        Log::info('[archivePartial] incoming', [
            'params' => $request->all(),
        ]);

        $q = $this->baseArchiveQuery()
            ->whereIn(DB::raw('LOWER(COALESCE(NULLIF(lpl.status,""),"lead"))'), ['archive', 'archiv']);
        $this->applyArchiveJunkFilters($q, $request);

        $debugQ = clone $q;
        $count = (clone $q)->count();

        Log::info('[archivePartial] query after filters', [
            'sql' => $debugQ->toSql(),
            'bindings' => $debugQ->getBindings(),
            'count' => $count,
        ]);

        $archive = $q->paginate(10, ['*'], 'archive_page')->appends($request->all());

        return view('admin.kanban.partials.archive', ['archive' => $archive]);
    }

    public function junkPartial(Request $request)
    {
        Log::info('[junkPartial] incoming', [
            'params' => $request->all(),
        ]);

        $q = $this->baseArchiveQuery()
            ->where(DB::raw('LOWER(COALESCE(NULLIF(lpl.status,""),"lead"))'), 'junk');
        $this->applyArchiveJunkFilters($q, $request);

        $junk = $q->paginate(10, ['*'], 'junk_page')->appends($request->all());

        return view('admin.kanban.partials.junk', ['junk' => $junk]);
    }





    public function showStageHistory(Request $request, $customer_id, $alternative_id, $product_id)
    {
        $customer_id = (int) $customer_id;
        $alternative_id = (int) $alternative_id;
        $product_id = (int) $product_id;

        // ── Lead row (only need stage_history)
        $lead = DB::table('lead_product_lists')
            ->where('customer_id', $customer_id)
            ->where('alternative_id', $alternative_id)
            ->where('product_id', $product_id)
            ->whereNull('deleted_at')
            ->select('stage_history')
            ->first();

        // ── Resolve customer display name (new_leads → customers → fallback)
        $customerName = null;

        $nl = DB::table('new_leads')->where('id', $customer_id)
            ->select('customer_type', 'firma', 'title', 'name', 'lastname', 'email', 'phone')->first();

        if ($nl) {
            if (!empty($nl->customer_type) && strtolower($nl->customer_type) === 'company') {
                $customerName = trim((string) ($nl->firma ?? ''));
            } else {
                $customerName = trim(implode(' ', array_filter([
                    trim((string) ($nl->title ?? '')),
                    trim((string) ($nl->name ?? '')),
                    trim((string) ($nl->lastname ?? '')),
                ])));
            }
            if ($customerName === '') {
                $customerName = $nl->firma
                    ?: trim(implode(' ', array_filter([$nl->title ?? null, $nl->name ?? null, $nl->lastname ?? null])))
                    ?: ($nl->email ?? $nl->phone ?? null);
            }
        }

        if (!$customerName) {
            $c = DB::table('customers')->where('id', $customer_id)
                ->select('firma', 'title', 'firstname', 'name', 'lastname', 'email', 'phone')->first();
            if ($c) {
                $display = trim(implode(' ', array_filter([
                    trim((string) ($c->firma ?? '')),
                    trim((string) ($c->title ?? '')),
                    trim((string) ($c->firstname ?? $c->name ?? '')),
                    trim((string) ($c->lastname ?? '')),
                ])));
                $customerName = $display !== '' ? $display : ($c->email ?? $c->phone ?? null);
            }
        }

        if (!$customerName)
            $customerName = "Kunde #{$customer_id}";

        // ── Timeline from stage_history
        $timeline = [];
        if ($lead && $lead->stage_history) {
            $raw = json_decode($lead->stage_history, true);
            if (is_array($raw)) {
                $rows = array_values(array_filter($raw, fn($e) => is_array($e) && !empty($e['stage'])));
                usort($rows, function ($a, $b) {
                    $ta = !empty($a['changed_at']) ? strtotime($a['changed_at']) : 0;
                    $tb = !empty($b['changed_at']) ? strtotime($b['changed_at']) : 0;
                    return $ta <=> $tb; // asc to compute from_stage
                });

                $prev = null;
                foreach ($rows as $e) {
                    $stage = (string) ($e['stage'] ?? 'unknown');
                    $changedAt = !empty($e['changed_at']) ? Carbon::parse($e['changed_at']) : null;
                    $changedById = Arr::get($e, 'changed_by');
                    $description = trim((string) Arr::get($e, 'description', ''));

                    $changedBy = 'Unbekannt';
                    if (is_numeric($changedById)) {
                        $emp = DB::table('employees')->where('id', (int) $changedById)->select('name', 'lastname')->first();
                        if ($emp) {
                            $full = trim(implode(' ', array_filter([$emp->name ?? null, $emp->lastname ?? null])));
                            $changedBy = $full !== '' ? $full : ("Mitarbeiter #" . $changedById);
                        }
                    } elseif (!empty($changedById)) {
                        $changedBy = (string) $changedById;
                    }

                    $timeline[] = [
                        'from_stage' => $prev,
                        'to_stage' => $stage,
                        'changed_at' => $changedAt?->format('Y-m-d H:i:s'),
                        'changed_by' => $changedBy,
                        'description' => $description,
                    ];
                    $prev = $stage;
                }

                // Display desc
                usort($timeline, fn($a, $b) => strtotime($b['changed_at'] ?? '0') <=> strtotime($a['changed_at'] ?? '0'));
            }
        }

        // ── Customer history with dynamic author column
        $columns = Schema::hasTable('customer_histories') ? Schema::getColumnListing('customer_histories') : [];
        $has = fn(string $col) => in_array($col, $columns, true);

        $hasChannel = $has('channel');
        $hasNote = $has('note');
        $hasMeta = $has('meta');

        $authorCandidates = ['created_by', 'employee_id', 'user_id', 'author_id', 'author', 'by_user_id', 'by'];
        $authorCol = null;
        foreach ($authorCandidates as $cand) {
            if ($has($cand)) {
                $authorCol = $cand;
                break;
            }
        }

        $q = DB::table('customer_histories as ch')
            ->leftJoin('task_phases as tp', 'tp.id', '=', 'ch.phase_id')
            ->leftJoin('phase_activities as pa', 'pa.id', '=', 'ch.activity_id')
            ->where('ch.customer_id', $customer_id)
            ->where('ch.alternative_id', $alternative_id)
            ->where('ch.product_id', $product_id)
            ->orderByDesc('ch.updated_at');

        $select = [
            'ch.id',
            DB::raw('tp.phase_name as phase_name'),
            DB::raw('pa.title as activity_title'),
            DB::raw('COALESCE(ch.done_date, ch.updated_at, ch.created_at) as at'),
        ];

        $joinEmployees = $authorCol && Str::endsWith($authorCol, '_id');
        if ($joinEmployees) {
            $q->leftJoin('employees as e', 'e.id', '=', DB::raw("ch.`{$authorCol}`"));
            $select[] = DB::raw("COALESCE(NULLIF(CONCAT(e.name,' ',e.lastname),' '), ch.`{$authorCol}`) as by_name");
        } elseif ($authorCol) {
            $select[] = DB::raw("ch.`{$authorCol}` as by_name");
        } else {
            $select[] = DB::raw("'Unbekannt' as by_name");
        }

        if ($hasChannel)
            $select[] = 'ch.channel';
        if ($hasNote)
            $select[] = 'ch.note';
        if ($hasMeta)
            $select[] = 'ch.meta';

        $rows = $q->get($select);

        $customerHistory = $rows->map(function ($r) use ($hasChannel, $hasNote, $hasMeta) {
            return [
                'id' => $r->id,
                'phase_name' => $r->phase_name ?? null,
                'activity_title' => $r->activity_title ?? null,
                'channel' => $hasChannel ? ($r->channel ?? null) : null,
                'note' => $hasNote ? ($r->note ?? null) : null,
                'meta' => $hasMeta ? ($r->meta ? json_decode($r->meta, true) : null) : null,
                'at' => $r->at ? Carbon::parse($r->at)->format('Y-m-d H:i:s') : null,
                'by' => ($r->by_name ?? 'Unbekannt') ?: 'Unbekannt',
            ];
        })->toArray();

        // ── IMPORTANT: ALWAYS return JSON for the drawer
        return response()->json([
            'customerName' => $customerName,
            'timeline' => $timeline,
            'customerHistory' => $customerHistory,
        ]);
    }
    public function stageHistory($customer, $alternative, $product)
    {
        $lead = DB::table('lead_product_lists')
            ->where([
                'customer_id' => $customer,
                'alternative_id' => $alternative,
                'product_id' => $product,
            ])
            ->whereNull('deleted_at')
            ->first();

        if (!$lead || !$lead->stage_history) {
            return response()->json(['history' => []]);
        }

        $history = json_decode($lead->stage_history, true);

        // Resolve user IDs → names
        $userIds = collect($history)->pluck('changed_by')->unique()->filter()->values()->all();
        $users = DB::table('employees')
            ->whereIn('id', $userIds)
            ->pluck(DB::raw("CONCAT(name, ' ', lastname)"), 'id')
            ->toArray();

        return response()->json([
            'history' => $history,
            'users' => $users
        ]);
    }
    public function restoreLeadStage(Request $request, $id)
    {
        $stage = $this->normalizeStage($request->input('stage'));

        if (!$stage || !$this->stageExists($stage)) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte wählen Sie eine gültige aktive Phase.',
            ], 422);
        }

        $lead = LeadProductList::find($id);

        if (!$lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead nicht gefunden.',
            ], 404);
        }

        $oldStage = $this->normalizeStage($lead->status);

        $history = is_array($lead->stage_history) ? $lead->stage_history : [];
        if (!is_array($history)) {
            $history = [];
        }

        $history[] = [
            'from' => $oldStage,
            'to' => $stage,
            'stage' => $stage,
            'changed_by' => auth()->user()->name ?? auth()->id(),
            'changed_user_id' => auth()->id(),
            'changed_employee_id' => auth()->user()->name ?? null,
            'changed_at' => now()->toDateTimeString(),
            'description' => 'Lead wiederhergestellt',
        ];

        $lead->old_stage = $oldStage;
        $lead->stage = $stage;
        $lead->status = $stage;
        $lead->stage_history = $history;
        $lead->save();

        $this->logActivity('updated', 'App\Models\LeadProductList', $lead->id, $lead->customer_id, $lead->alternative_id, $lead->product_id, [
            'stage' => ['from' => $oldStage, 'to' => $stage],
            'info' => 'Lead wiederhergestellt',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead erfolgreich wiederhergestellt.',
            'stage' => $stage,
        ]);
    }




    public function updateProgress(Request $request, $leadProductId, $state): \Illuminate\Http\JsonResponse
    {
        if (!in_array($state, ['playing', 'paused', 'stopped'], true)) {
            return response()->json(['success' => false, 'message' => 'Ungültiger Status'], 422);
        }

        $lead = DB::table('lead_product_lists')
            ->where('id', $leadProductId)
            ->whereNull('deleted_at')
            ->first();
        if (!$lead) {
            return response()->json(['success' => false, 'message' => 'Lead nicht gefunden.'], 404);
        }

        $oldState = $lead->work_status;
        $reason = $request->input('reason', '');

        // --- NEW: Read existing history and append the reason ---
        $history = [];
        if (!empty($lead->stage_history)) {
            $decoded = json_decode($lead->stage_history, true);
            if (is_array($decoded)) {
                $history = $decoded;
            }
        }

        if ($state === 'paused' || $state === 'stopped') {
            $history[] = [
                'stage' => $lead->status,
                'changed_by' => auth()->user()->name ?? 'System',
                'changed_at' => now()->toDateTimeString(),
                'description' => $reason,
            ];
        }

        // Perform Update
        DB::table('lead_product_lists')
            ->where('id', $leadProductId)
            ->whereNull('deleted_at')
            ->update([
                'work_status' => $state,
                'stage_history' => json_encode($history, JSON_UNESCAPED_UNICODE), // <--- Save the updated history
                'updated_at' => now(),
            ]);

        // ★ LOG ACTIVITY
        $this->logActivity('updated', 'App\Models\LeadProductList', $leadProductId, $lead->customer_id, $lead->alternative_id, $lead->product_id, [
            'work_status' => ['from' => $oldState, 'to' => $state],
            'info' => 'Arbeitsstatus (Play/Pause) geändert'
        ]);

        return response()->json(['success' => true]);
    }
    /**
     * Canonical stage mapping.
     *
     * IMPORTANT:
     * - lead_product_lists.status must store stable keys like "accepted".
     * - lead_stages.name is only the visible label and can be renamed freely.
     * - renamed labels are also accepted as aliases, but are converted back to their stable key.
     */
    private function stageMap(): array
    {
        $map = [];

        if (Schema::hasTable('lead_stages')) {
            LeadStage::query()
                ->where('is_active', true)
                ->get(['key', 'name'])
                ->each(function ($stage) use (&$map) {
                    $key = strtolower(trim((string) $stage->key));
                    $nameKey = $this->stageSlug((string) $stage->name);

                    if ($key !== '') {
                        $map[$key] = $key;
                    }

                    // Allow renamed labels like "Gewonnen" to resolve to the original key "accepted".
                    if ($nameKey !== '') {
                        $map[$nameKey] = $key;
                    }
                });
        }

        $aliases = [
            'open' => 'lead',
            'new' => 'lead',
            'neue' => 'lead',
            'neu' => 'lead',
            'lead' => 'lead',

            'angebot' => 'offer',
            'verkauf' => 'offer',
            'offer' => 'offer',

            'nachfassen' => 'follow_up',
            'followup' => 'follow_up',
            'follow_up' => 'follow_up',

            'annehmen' => 'accepted',
            'annemen' => 'accepted',
            'angenommen' => 'accepted',
            'accepted' => 'accepted',

            'auftrag' => 'deal',
            'deal' => 'deal',

            'montage' => 'project',
            'projekt' => 'project',
            'project' => 'project',

            'abschluss' => 'completed',
            'complete' => 'completed',
            'completed' => 'completed',

            'archive' => 'archive',
            'archiv' => 'archive',

            'junk' => 'junk',
            'reject' => 'junk',
            'rejeck' => 'junk',

            'ticket' => 'ticket',
        ];

        // Aliases must override accidentally-created dynamic keys like "annemen".
        return array_replace($map, $aliases);
    }
    private function normalizeStage(?string $stage, string $fallback = 'lead'): string
    {
        $s = strtolower(trim((string) $stage));

        if ($s === '') {
            return $fallback;
        }

        $map = $this->stageMap();

        return $map[$s] ?? $fallback;
    }

    /**
     * Stufe B: [key => id] der Lead-Stages (lowercased). Genutzt vom FK-Fold in
     * applyCommonFilters (Query) und vom FK-first-Transform in search (Rendering).
     */
    private function leadStageIdByKey(): array
    {
        return LeadStage::query()
            ->pluck('id', 'key')
            ->mapWithKeys(fn($id, $key) => [strtolower(trim((string) $key)) => (int) $id])
            ->toArray();
    }

    private function leadStageKeyById(): array
    {
        return array_flip($this->leadStageIdByKey());
    }

    private function stageExists(string $stage): bool
    {
        return LeadStage::query()
            ->where('key', $stage)
            ->where('is_active', true)
            ->exists();
    }

    private function leadStagesForUi()
    {
        $this->ensureDefaultLeadStages();

        return LeadStage::query()
            ->with([
                'subStages' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('id');
                }
            ])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function leadStageLabels(): array
    {
        return $this->leadStagesForUi()
            ->pluck('name', 'key')
            ->toArray();
    }

    private function openStageKeys(): array
    {
        $keys = LeadStage::query()
            ->where('is_active', true)
            ->where('is_closed', false)
            ->pluck('key')
            ->map(fn($key) => strtolower((string) $key))
            ->values()
            ->all();

        return $keys ?: ['lead', 'offer', 'follow_up'];
    }

    private function positiveStageKeys(): array
    {
        $keys = LeadStage::query()
            ->where('is_active', true)
            ->whereIn('key', ['accepted', 'deal', 'project', 'completed', 'archive'])
            ->pluck('key')
            ->map(fn($key) => strtolower((string) $key))
            ->values()
            ->all();

        return $keys ?: ['accepted', 'deal', 'project', 'completed', 'archive'];
    }

    private function ensureDefaultLeadStages(): void
    {
        if (!Schema::hasTable('lead_stages')) {
            return;
        }

        /*
         |--------------------------------------------------------------------------
         | Default stages are technical defaults only
         |--------------------------------------------------------------------------
         | NEVER update existing rows here.
         |
         | The user may rename/hide/change color/icon of protected/default stages.
         | Existing rows must keep their edited name/color/icon/is_active values.
         | Only missing technical keys are created.
         */
        foreach ($this->defaultLeadStages() as $row) {
            $exists = LeadStage::withTrashed()
                ->where('key', $row['key'])
                ->exists();

            if (!$exists) {
                LeadStage::create($row);
            }
        }

        $this->repairLegacyDuplicateLeadStages();
    }

    /**
     * Repairs old duplicate stages created from labels/typos, for example:
     * - annehmen / annemen / angenommen  => accepted
     * - nachfassen / followup            => follow_up
     *
     * It does not rename the canonical stage. It only moves references and hides duplicates.
     */
    private function repairLegacyDuplicateLeadStages(): void
    {
        if (!Schema::hasTable('lead_stages')) {
            return;
        }

        $groups = [
            'lead' => ['open', 'new', 'neu', 'neue'],
            'offer' => ['angebot', 'verkauf'],
            'follow_up' => ['nachfassen', 'followup'],
            'accepted' => ['annehmen', 'annemen', 'angenommen'],
            'deal' => ['auftrag'],
            'project' => ['montage', 'projekt'],
            'completed' => ['abschluss', 'complete'],
            'archive' => ['archiv'],
            'junk' => ['reject', 'rejeck'],
        ];

        foreach ($groups as $canonicalKey => $duplicateKeys) {
            $target = LeadStage::withTrashed()
                ->where('key', $canonicalKey)
                ->first();

            if (!$target) {
                continue;
            }

            $duplicates = LeadStage::withTrashed()
                ->whereIn('key', $duplicateKeys)
                ->get();

            foreach ($duplicates as $duplicate) {
                if ((int) $duplicate->id === (int) $target->id) {
                    continue;
                }

                $this->moveDuplicateStageReferences($duplicate, $target);

                $update = ['is_active' => false];

                if (Schema::hasColumn('lead_stages', 'deleted_at') && empty($duplicate->deleted_at)) {
                    $update['deleted_at'] = now();
                }

                $duplicate->forceFill($update)->save();
            }

            $this->normalizeLeadProductStatusAliases($canonicalKey, $duplicateKeys);
        }
    }

    private function moveDuplicateStageReferences(LeadStage $duplicate, LeadStage $target): void
    {
        if (Schema::hasTable('lead_stage_sub_stages')) {
            LeadStageSubStage::withTrashed()
                ->where('lead_stage_id', $duplicate->id)
                ->update([
                    'lead_stage_id' => $target->id,
                    'updated_at' => now(),
                ]);
        }

        $tables = [
            'task_phases',
            'kanban_lead_tasks',
            'personal_tasks',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'lead_stage_id')) {
                continue;
            }

            $update = ['lead_stage_id' => $target->id];

            if (Schema::hasColumn($table, 'updated_at')) {
                $update['updated_at'] = now();
            }

            DB::table($table)
                ->where('lead_stage_id', $duplicate->id)
                ->update($update);
        }
    }

    private function normalizeLeadProductStatusAliases(string $canonicalKey, array $aliases): void
    {
        if (!Schema::hasTable('lead_product_lists')) {
            return;
        }

        $aliases = collect($aliases)
            ->map(fn($value) => strtolower(trim((string) $value)))
            ->filter()
            ->values()
            ->all();

        if (!$aliases) {
            return;
        }

        $update = [];

        if (Schema::hasColumn('lead_product_lists', 'status')) {
            DB::table('lead_product_lists')
                ->whereIn(DB::raw('LOWER(COALESCE(status, ""))'), $aliases)
                ->update(['status' => $canonicalKey, 'updated_at' => now()]);
        }

        if (Schema::hasColumn('lead_product_lists', 'stage')) {
            DB::table('lead_product_lists')
                ->whereIn(DB::raw('LOWER(COALESCE(stage, ""))'), $aliases)
                ->update(['stage' => $canonicalKey, 'updated_at' => now()]);
        }
    }

    private function stageSlug(string $value): string
    {
        $slug = Str::slug($value, '_');

        return strtolower(trim($slug, '_'));
    }
    private function defaultLeadStages(): array
    {
        return [
            ['key' => 'lead', 'name' => 'Lead', 'color' => '#74b2d4', 'icon' => 'user-plus', 'sort_order' => 10, 'is_default' => true, 'is_protected' => true, 'is_closed' => false, 'is_active' => true],
            ['key' => 'offer', 'name' => 'Angebot', 'color' => '#93c21c', 'icon' => 'file-text', 'sort_order' => 20, 'is_default' => true, 'is_protected' => true, 'is_closed' => false, 'is_active' => true],
            ['key' => 'follow_up', 'name' => 'Nachfassen', 'color' => '#f8ac00', 'icon' => 'phone-call', 'sort_order' => 30, 'is_default' => true, 'is_protected' => true, 'is_closed' => false, 'is_active' => true],
            ['key' => 'accepted', 'name' => 'Annehmen', 'color' => '#93c21c', 'icon' => 'check-circle', 'sort_order' => 40, 'is_default' => true, 'is_protected' => true, 'is_closed' => false, 'is_active' => true],
            ['key' => 'deal', 'name' => 'Auftrag', 'color' => '#74b2d4', 'icon' => 'briefcase', 'sort_order' => 50, 'is_default' => true, 'is_protected' => true, 'is_closed' => false, 'is_active' => true],
            ['key' => 'project', 'name' => 'Montage', 'color' => '#c0d8ea', 'icon' => 'tool', 'sort_order' => 60, 'is_default' => true, 'is_protected' => true, 'is_closed' => false, 'is_active' => true],
            ['key' => 'completed', 'name' => 'Abschluss', 'color' => '#93c21c', 'icon' => 'flag', 'sort_order' => 70, 'is_default' => true, 'is_protected' => true, 'is_closed' => true, 'is_active' => true],
            ['key' => 'archive', 'name' => 'Archive', 'color' => '#6b7280', 'icon' => 'archive', 'sort_order' => 80, 'is_default' => true, 'is_protected' => true, 'is_closed' => true, 'is_active' => true],
            ['key' => 'junk', 'name' => 'Junk', 'color' => '#e50656', 'icon' => 'trash-2', 'sort_order' => 90, 'is_default' => true, 'is_protected' => true, 'is_closed' => true, 'is_active' => true],
        ];
    }

    private function stageUsageCount(string $key): int
    {
        return DB::table('lead_product_lists')
            ->whereNull('deleted_at')
            ->whereRaw('LOWER(COALESCE(NULLIF(status, ""), "lead")) = ?', [strtolower($key)])
            ->count();
    }


    /**
     * Base ACTIVE query (excludes archive/junk) with standard joins.
     * Employee is resolved as:
     * 1) first responsibility (nlr.employee_id) if available
     * 2) otherwise fallback to lpl.employee_id
     * MODIFIED: Added $excludedStatuses to customization.
     */

    /**
     * Safe SELECT helper for optional columns.
     * Returns a real column only when the database column exists; otherwise it
     * returns a harmless NULL/empty alias so Kanban AJAX never dies on a missing
     * optional column.
     */
    private function safeColumnSelect(string $table, string $alias, string $column, string $as, string $fallback = 'null')
    {
        if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
            return "{$alias}.{$column} as {$as}";
        }

        if ($fallback === 'empty') {
            return DB::raw("'' as {$as}");
        }

        if ($fallback === 'zero') {
            return DB::raw("0 as {$as}");
        }

        return DB::raw("NULL as {$as}");
    }


    private function baseActiveQuery(bool $includeClosed = false, array $excludedStatuses = ['archive', 'archiv', 'junk', 'ticket']): \Illuminate\Database\Query\Builder
    {
        $hasFieldEmployee = Schema::hasColumn('lead_product_lists', 'field_employee');
        $hasFieldEmployeeId = Schema::hasColumn('lead_product_lists', 'field_employee_id');
        $hasDepartmentId = Schema::hasColumn('lead_product_lists', 'department_id');
        $hasProductStageId = Schema::hasColumn('lead_product_lists', 'product_stage_id');
        $hasProductTaskPhaseId = Schema::hasColumn('lead_product_lists', 'product_task_phase_id');
        $hasLeadStageSubStageId = Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id');
        $hasBranchOnLead = Schema::hasColumn('new_leads', 'branch');

        $q = DB::table('lead_product_lists as lpl')
            ->join('new_leads', 'new_leads.id', '=', 'lpl.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'lpl.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'lpl.product_id')
            ->leftJoin('employees as e_lpl', 'e_lpl.id', '=', 'lpl.employee_id');

        if ($hasFieldEmployee) {
            $q->leftJoin('employees as e_field', 'e_field.id', '=', 'lpl.field_employee');
        } elseif ($hasFieldEmployeeId) {
            $q->leftJoin('employees as e_field', 'e_field.id', '=', 'lpl.field_employee_id');
        } else {
            $q->leftJoin(DB::raw('(SELECT NULL as id, NULL as name, NULL as lastname, NULL as image) as e_field'), function ($join) {
                $join->whereRaw('1 = 0');
            });
        }

        if (Schema::hasTable('departments') && $hasDepartmentId) {
            $q->leftJoin('departments', 'departments.id', '=', 'lpl.department_id');
        } else {
            $q->leftJoin(DB::raw('(SELECT NULL as id, NULL as department_name) as departments'), function ($join) {
                $join->whereRaw('1 = 0');
            });
        }

        if (Schema::hasTable('branches') && $hasBranchOnLead) {
            $q->leftJoin('branches as br', 'br.id', '=', 'new_leads.branch');
        } else {
            $q->leftJoin(DB::raw('(SELECT NULL as id, NULL as branch, NULL as initial, NULL as color) as br'), function ($join) {
                $join->whereRaw('1 = 0');
            });
        }

        if (Schema::hasTable('lead_stage_sub_stages') && $hasLeadStageSubStageId) {
            $q->leftJoin('lead_stage_sub_stages as lsss', 'lsss.id', '=', 'lpl.lead_stage_sub_stage_id');
        } else {
            $q->leftJoin(DB::raw('(SELECT NULL as id, NULL as name, NULL as color, NULL as icon) as lsss'), function ($join) {
                $join->whereRaw('1 = 0');
            });
        }

        if (Schema::hasTable('stages') && $hasProductStageId) {
            $q->leftJoin('stages as product_stages', 'product_stages.id', '=', 'lpl.product_stage_id');
        } else {
            $q->leftJoin(DB::raw('(SELECT NULL as id, NULL as stage, NULL as name, NULL as title, NULL as color, NULL as icon, 0 as sort_order) as product_stages'), function ($join) {
                $join->whereRaw('1 = 0');
            });
        }

        if (Schema::hasTable('task_phases') && $hasProductTaskPhaseId) {
            $q->leftJoin('task_phases as product_task_phases', 'product_task_phases.id', '=', 'lpl.product_task_phase_id');
        } else {
            $q->leftJoin(DB::raw('(SELECT NULL as id, NULL as phase_name, NULL as name, NULL as title) as product_task_phases'), function ($join) {
                $join->whereRaw('1 = 0');
            });
        }

        $q->whereNull('lpl.deleted_at')
            ->whereNull('new_leads.deleted_at')
            ->whereNull('alt.deleted_at');

        if (!$includeClosed) {
            $q->where(function ($w) use ($excludedStatuses) {
                $w->whereNull('lpl.status')
                    ->orWhere('lpl.status', '')
                    ->orWhereNotIn(DB::raw('LOWER(lpl.status)'), $excludedStatuses);
            });
        }

        return $q;
    }

    private function baseSelectColumns(): array
    {
        $hasLeadStageMode = Schema::hasColumn('lead_product_lists', 'stage_mode');
        $hasLeadProductStageId = Schema::hasColumn('lead_product_lists', 'product_stage_id');
        $hasLeadProductTaskPhaseId = Schema::hasColumn('lead_product_lists', 'product_task_phase_id');
        $hasFieldEmployee = Schema::hasColumn('lead_product_lists', 'field_employee');
        $hasFieldEmployeeId = Schema::hasColumn('lead_product_lists', 'field_employee_id');
        $hasLeadStageSubStageId = Schema::hasTable('lead_stage_sub_stages') && Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id');

        $productStageName = DB::raw('NULL as product_stage_name');
        if (Schema::hasTable('stages')) {
            if (Schema::hasColumn('stages', 'stage')) {
                $productStageName = 'product_stages.stage as product_stage_name';
            } elseif (Schema::hasColumn('stages', 'name')) {
                $productStageName = 'product_stages.name as product_stage_name';
            } elseif (Schema::hasColumn('stages', 'title')) {
                $productStageName = 'product_stages.title as product_stage_name';
            }
        }

        $productTaskPhaseName = DB::raw('NULL as product_task_phase_name');
        if (Schema::hasTable('task_phases')) {
            if (Schema::hasColumn('task_phases', 'phase_name')) {
                $productTaskPhaseName = 'product_task_phases.phase_name as product_task_phase_name';
            } elseif (Schema::hasColumn('task_phases', 'name')) {
                $productTaskPhaseName = 'product_task_phases.name as product_task_phase_name';
            } elseif (Schema::hasColumn('task_phases', 'title')) {
                $productTaskPhaseName = 'product_task_phases.title as product_task_phase_name';
            }
        }

        return [
            'new_leads.id as customer_id',
            $this->safeColumnSelect('new_leads', 'new_leads', 'name', 'customer_name', 'empty'),
            $this->safeColumnSelect('new_leads', 'new_leads', 'lastname', 'customer_lastname', 'empty'),
            $this->safeColumnSelect('new_leads', 'new_leads', 'phone', 'phone'),
            $this->safeColumnSelect('new_leads', 'new_leads', 'email', 'email'),
            $this->safeColumnSelect('new_leads', 'new_leads', 'firma', 'firma', 'empty'),
            $this->safeColumnSelect('new_leads', 'new_leads', 'branch', 'branch_id'),

            $this->safeColumnSelect('branches', 'br', 'branch', 'branch_name', 'empty'),
            $this->safeColumnSelect('branches', 'br', 'initial', 'branch_initial', 'empty'),
            $this->safeColumnSelect('branches', 'br', 'color', 'branch_color'),

            'alt.id as alternative_id',
            $this->safeColumnSelect('lead_alternative_adds', 'alt', 'street', 'street', 'empty'),
            $this->safeColumnSelect('lead_alternative_adds', 'alt', 'postcode', 'postcode', 'empty'),
            $this->safeColumnSelect('lead_alternative_adds', 'alt', 'city', 'city', 'empty'),
            $this->safeColumnSelect('lead_alternative_adds', 'alt', 'object_name', 'object_name', 'empty'),
            $this->safeColumnSelect('lead_alternative_adds', 'alt', 'request_date', 'request_date'),
            $this->safeColumnSelect('lead_alternative_adds', 'alt', 'periority', 'priority'),

            'article_groups.id as product_id',
            $this->safeColumnSelect('article_groups', 'article_groups', 'initial', 'initial', 'empty'),
            $this->safeColumnSelect('article_groups', 'article_groups', 'article_group', 'article_group', 'empty'),

            'lpl.id as lead_product_id',
            DB::raw("CASE WHEN lpl.status IS NULL OR lpl.status = '' OR LOWER(lpl.status) = 'open' THEN 'lead' ELSE LOWER(lpl.status) END as stage"),
            // Stufe B: FK-Wahrheit mitselektieren (Board rendert FK-first, Fallback auf status-String)
            Schema::hasColumn('lead_product_lists', 'lead_stage_id') ? 'lpl.lead_stage_id' : DB::raw('NULL as lead_stage_id'),

            $hasLeadStageMode ? 'lpl.stage_mode' : DB::raw("'company' as stage_mode"),
            $hasLeadProductStageId ? 'lpl.product_stage_id' : DB::raw('NULL as product_stage_id'),
            $hasLeadProductTaskPhaseId ? 'lpl.product_task_phase_id' : DB::raw('NULL as product_task_phase_id'),

            $productStageName,
            Schema::hasTable('stages') && Schema::hasColumn('stages', 'sort_order') ? 'product_stages.sort_order as product_stage_sort_order' : DB::raw('0 as product_stage_sort_order'),
            $productTaskPhaseName,

            $this->safeColumnSelect('lead_product_lists', 'lpl', 'teams', 'teams'),
            $this->safeColumnSelect('lead_product_lists', 'lpl', 'stage_history', 'stage_history'),
            $this->safeColumnSelect('lead_product_lists', 'lpl', 'work_status', 'work_status'),
            $this->safeColumnSelect('lead_product_lists', 'lpl', 'service', 'service'),
            $this->safeColumnSelect('lead_product_lists', 'lpl', 'service_id', 'service_id'),
            $this->safeColumnSelect('lead_product_lists', 'lpl', 'department_id', 'department_id'),
            $this->safeColumnSelect('lead_product_lists', 'lpl', 'interest', 'interest'),
            'lpl.created_at',
            'lpl.updated_at',

            $hasLeadStageSubStageId ? 'lpl.lead_stage_sub_stage_id' : DB::raw('NULL as lead_stage_sub_stage_id'),
            $hasLeadStageSubStageId ? 'lsss.name as lead_stage_sub_stage_name' : DB::raw('NULL as lead_stage_sub_stage_name'),
            $hasLeadStageSubStageId ? 'lsss.color as lead_stage_sub_stage_color' : DB::raw('NULL as lead_stage_sub_stage_color'),
            $hasLeadStageSubStageId ? 'lsss.icon as lead_stage_sub_stage_icon' : DB::raw('NULL as lead_stage_sub_stage_icon'),

            'e_lpl.id as employee_id',
            $this->safeColumnSelect('employees', 'e_lpl', 'name', 'employee_name', 'empty'),
            $this->safeColumnSelect('employees', 'e_lpl', 'lastname', 'employee_lastname', 'empty'),
            $this->safeColumnSelect('employees', 'e_lpl', 'image', 'employee_image'),

            $hasFieldEmployee ? 'lpl.field_employee as field_employee_id' : ($hasFieldEmployeeId ? 'lpl.field_employee_id as field_employee_id' : DB::raw('NULL as field_employee_id')),
            $this->safeColumnSelect('employees', 'e_field', 'name', 'field_employee_name', 'empty'),
            $this->safeColumnSelect('employees', 'e_field', 'lastname', 'field_employee_lastname', 'empty'),
            $this->safeColumnSelect('employees', 'e_field', 'image', 'field_employee_image'),

            $this->safeColumnSelect('departments', 'departments', 'department_name', 'department_name', 'empty'),
        ];
    }

    private function applyCommonFilters(\Illuminate\Database\Query\Builder $q, Request $request, bool $forKanban = false): void
    {
        $LPL = $this->lplAlias($q);
        $branchesHasBranch = Schema::hasTable('branches') && Schema::hasColumn('branches', 'branch');
        $branchesHasInitial = Schema::hasTable('branches') && Schema::hasColumn('branches', 'initial');
        $hasFieldEmployee = Schema::hasColumn('lead_product_lists', 'field_employee');
        $hasFieldEmployeeId = Schema::hasColumn('lead_product_lists', 'field_employee_id');
        $hasTeams = Schema::hasColumn('lead_product_lists', 'teams');

        $norm = "CASE WHEN {$LPL}.status IS NULL OR {$LPL}.status = '' OR LOWER({$LPL}.status) = 'open' THEN 'lead' ELSE LOWER({$LPL}.status) END";

        if (($term = trim((string) $request->input('search', ''))) !== '') {
            $q->where(function ($qq) use ($term, $LPL, $branchesHasBranch, $branchesHasInitial, $hasFieldEmployee, $hasFieldEmployeeId) {
                if (Schema::hasColumn('new_leads', 'name'))
                    $qq->orWhere('new_leads.name', 'like', "%{$term}%");
                if (Schema::hasColumn('new_leads', 'lastname'))
                    $qq->orWhere('new_leads.lastname', 'like', "%{$term}%");
                if (Schema::hasColumn('new_leads', 'firma'))
                    $qq->orWhere('new_leads.firma', 'like', "%{$term}%");
                if (Schema::hasColumn('lead_alternative_adds', 'street'))
                    $qq->orWhere('alt.street', 'like', "%{$term}%");
                if (Schema::hasColumn('lead_alternative_adds', 'city'))
                    $qq->orWhere('alt.city', 'like', "%{$term}%");
                if (Schema::hasColumn('lead_alternative_adds', 'postcode'))
                    $qq->orWhere('alt.postcode', 'like', "%{$term}%");
                if (Schema::hasColumn('article_groups', 'article_group'))
                    $qq->orWhere('article_groups.article_group', 'like', "%{$term}%");
                if ($branchesHasBranch)
                    $qq->orWhere('br.branch', 'like', "%{$term}%");
                if ($branchesHasInitial)
                    $qq->orWhere('br.initial', 'like', "%{$term}%");

                if (Schema::hasTable('employees')) {
                    $empIds = DB::table('employees')
                        ->where(function ($empQ) use ($term) {
                            if (Schema::hasColumn('employees', 'name'))
                                $empQ->orWhere('name', 'like', "%{$term}%");
                            if (Schema::hasColumn('employees', 'lastname'))
                                $empQ->orWhere('lastname', 'like', "%{$term}%");
                        })
                        ->pluck('id');

                    if ($empIds->isNotEmpty()) {
                        $qq->orWhereIn("{$LPL}.employee_id", $empIds->all());
                        if ($hasFieldEmployee)
                            $qq->orWhereIn("{$LPL}.field_employee", $empIds->all());
                        if ($hasFieldEmployeeId)
                            $qq->orWhereIn("{$LPL}.field_employee_id", $empIds->all());
                    }
                }
            });
        }

        if ($request->filled('status_group')) {
            $group = strtolower(trim((string) $request->status_group));

            if ($group === 'offen') {
                $q->whereIn(DB::raw($norm), $this->openStageKeys());
            } elseif ($group === 'zusage') {
                $q->whereIn(DB::raw($norm), $this->positiveStageKeys());
            } elseif ($group === 'absage') {
                $q->whereNotIn(DB::raw($norm), array_unique(array_merge($this->openStageKeys(), $this->positiveStageKeys())));
            }
        }

        if ($request->filled('stage')) {
            $stage = strtolower(trim((string) $request->stage));

            if (str_starts_with($stage, 'product_stage_')) {
                $productStageId = (int) str_replace('product_stage_', '', $stage);
                if ($productStageId > 0 && Schema::hasColumn('lead_product_lists', 'product_stage_id')) {
                    $q->where("{$LPL}.product_stage_id", $productStageId);
                }
            } else {
                $aliases = [
                    'open' => 'lead',
                    'new' => 'lead',
                    'neue' => 'lead',
                    'angebot' => 'offer',
                    'nachfassen' => 'follow_up',
                    'followup' => 'follow_up',
                    'annehmen' => 'accepted',
                    'angenommen' => 'accepted',
                    'auftrag' => 'deal',
                    'montage' => 'project',
                    'abschluss' => 'completed',
                    'archiv' => 'archive',
                    'rejeck' => 'junk',
                    'reject' => 'junk',
                ];
                $canonical = $aliases[$stage] ?? $stage;

                if ($forKanban && Schema::hasColumn('lead_product_lists', 'lead_stage_id')) {
                    // Stufe B: FK-first mit SYMMETRISCHEM Fold (follow_up->offer, accepted->deal).
                    // Spalte holt Karten (a) deren FK auf die Spalte ODER die gefoldete Kind-Stage zeigt,
                    // (b) NULL-FK mit gefoldetem Status. Zweige disjunkt (FK gesetzt vs. NULL) -> keine Doppelzaehlung.
                    // Bruecke bis B2-Hook: solange raw-Writer FK=follow_up/accepted erzeugen koennen, faengt der FK-Fold sie.
                    $childrenOf = ['offer' => ['follow_up'], 'deal' => ['accepted']];
                    $statusSet = array_values(array_unique(array_merge([$canonical], $childrenOf[$canonical] ?? [])));
                    $keyToId = $this->leadStageIdByKey();
                    $stageIdSet = collect($statusSet)->map(fn($k) => $keyToId[$k] ?? null)->filter()->values()->all();

                    if (!empty($stageIdSet)) {
                        $q->where(function ($qq) use ($LPL, $stageIdSet, $norm, $statusSet) {
                            $qq->whereIn("{$LPL}.lead_stage_id", $stageIdSet)
                                ->orWhere(function ($q2) use ($LPL, $norm, $statusSet) {
                                    $q2->whereNull("{$LPL}.lead_stage_id")->whereIn(DB::raw($norm), $statusSet);
                                });
                        });
                    } else {
                        $q->whereIn(DB::raw($norm), $statusSet);
                    }
                } else {
                    $q->where(DB::raw($norm), '=', $canonical);
                }
            }
        }

        if ($request->filled('employee')) {
            $empRaw = $request->employee;

            if (ctype_digit((string) $empRaw)) {
                $ids = collect([(int) $empRaw]);
            } elseif (Schema::hasTable('employees')) {
                $ids = DB::table('employees')
                    ->where(function ($empQ) use ($empRaw) {
                        if (Schema::hasColumn('employees', 'name'))
                            $empQ->orWhere('name', 'like', "%{$empRaw}%");
                        if (Schema::hasColumn('employees', 'lastname'))
                            $empQ->orWhere('lastname', 'like', "%{$empRaw}%");
                    })
                    ->pluck('id');
            } else {
                $ids = collect();
            }

            if ($ids->isEmpty()) {
                $q->whereRaw('1=0');
            } else {
                $idArr = $ids->all();
                $q->where(function ($qq) use ($LPL, $idArr, $hasFieldEmployee, $hasFieldEmployeeId, $hasTeams) {
                    $qq->whereIn("{$LPL}.employee_id", $idArr);
                    if ($hasFieldEmployee)
                        $qq->orWhereIn("{$LPL}.field_employee", $idArr);
                    if ($hasFieldEmployeeId)
                        $qq->orWhereIn("{$LPL}.field_employee_id", $idArr);

                    // Safe fallback for JSON/text teams column. Avoid JSON_CONTAINS because
                    // old rows may contain invalid JSON and that breaks the whole query.
                    if ($hasTeams) {
                        foreach ($idArr as $empId) {
                            $qq->orWhere("{$LPL}.teams", 'like', '%"employee_id":' . (int) $empId . '%')
                                ->orWhere("{$LPL}.teams", 'like', '%"employee_id":"' . (int) $empId . '"%');
                        }
                    }
                });
            }
        }

        if ($request->filled('department') && Schema::hasColumn('lead_product_lists', 'department_id')) {
            $dep = $request->department;
            if (ctype_digit((string) $dep)) {
                $q->where("{$LPL}.department_id", $dep);
            } elseif (Schema::hasTable('departments') && Schema::hasColumn('departments', 'department_name')) {
                $depIds = DB::table('departments')
                    ->where('department_name', 'like', "%{$dep}%")
                    ->pluck('id');

                $depIds->isNotEmpty()
                    ? $q->whereIn("{$LPL}.department_id", $depIds)
                    : $q->whereRaw('1=0');
            }
        }

        if ($request->filled('branch') && Schema::hasColumn('new_leads', 'branch')) {
            $branchRaw = trim((string) $request->branch);
            if (ctype_digit($branchRaw)) {
                $q->where('new_leads.branch', (int) $branchRaw);
            } elseif (Schema::hasTable('branches')) {
                $branchQuery = DB::table('branches');
                $branchQuery->where(function ($bq) use ($branchRaw, $branchesHasBranch, $branchesHasInitial) {
                    if ($branchesHasBranch)
                        $bq->orWhere('branch', 'like', "%{$branchRaw}%");
                    if ($branchesHasInitial)
                        $bq->orWhere('initial', 'like', "%{$branchRaw}%");
                });

                $branchIds = $branchQuery->pluck('id');
                $branchIds->isNotEmpty()
                    ? $q->whereIn('new_leads.branch', $branchIds->all())
                    : $q->whereRaw('1=0');
            }
        }

        if ($request->filled('branch_address')) {
            $branchAddressId = (int) $request->input('branch_address');

            if ($branchAddressId > 0 && Schema::hasTable('branch_addresses')) {
                if (Schema::hasColumn('new_leads', 'branch_address_id')) {
                    $q->where('new_leads.branch_address_id', $branchAddressId);
                } elseif (Schema::hasColumn('lead_product_lists', 'branch_address_id')) {
                    $q->where("{$LPL}.branch_address_id", $branchAddressId);
                } elseif (Schema::hasColumn('lead_alternative_adds', 'branch_address_id')) {
                    $q->where('alt.branch_address_id', $branchAddressId);
                } else {
                    $addr = DB::table('branch_addresses')->where('id', $branchAddressId)->first();

                    if ($addr) {
                        $q->where(function ($qa) use ($addr) {
                            if (!empty($addr->street) && Schema::hasColumn('lead_alternative_adds', 'street')) {
                                $qa->where('alt.street', 'like', '%' . $addr->street . '%');
                            }
                            if (!empty($addr->postcode) && Schema::hasColumn('lead_alternative_adds', 'postcode')) {
                                $qa->where('alt.postcode', $addr->postcode);
                            }
                            if (!empty($addr->city) && Schema::hasColumn('lead_alternative_adds', 'city')) {
                                $qa->where('alt.city', 'like', '%' . $addr->city . '%');
                            }
                        });
                    }
                }
            }
        }

        if ($request->filled('product') && Schema::hasColumn('lead_product_lists', 'product_id')) {
            $q->where("{$LPL}.product_id", $request->product);
        }

        $customerInput = $request->input('customer', []);
        if (!is_array($customerInput)) {
            $customerInput = [$customerInput];
        }

        $customerIds = collect($customerInput)
            ->filter()
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->values()
            ->all();

        if (!empty($customerIds)) {
            $q->whereIn("{$LPL}.customer_id", $customerIds);
        }

        if ($request->filled('interest') && Schema::hasColumn('lead_product_lists', 'interest')) {
            $q->where("{$LPL}.interest", $request->interest);
        }

        if ($request->filled('date_from')) {
            $q->whereDate("{$LPL}.created_at", '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $q->whereDate("{$LPL}.created_at", '<=', $request->date_to);
        }

        if ($request->filled('lead_age')) {
            $age = $request->lead_age;
            $now = Carbon::now();

            $q->where(DB::raw($norm), '=', 'lead');

            if ($age === 'green') {
                $q->where("{$LPL}.created_at", '>=', $now->copy()->subHours(24));
            } elseif ($age === 'orange') {
                $q->where("{$LPL}.created_at", '<', $now->copy()->subHours(24))
                    ->where("{$LPL}.created_at", '>=', $now->copy()->subHours(48));
            } elseif ($age === 'red') {
                $q->where("{$LPL}.created_at", '<', $now->copy()->subHours(48));
            }
        }

        $sortByRaw = $request->get('sort_by', 'created_at');
        $sortDir = strtolower((string) $request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowed = [
            'created_at',
            'updated_at',
            'status',
            'department_id',
            'employee_id',
            'customer_id',
            'lead_product_lists.id',
            'new_leads.id',
            'name',
            'lastname',
            'city',
            'customer_lastname',
        ];

        $sortKey = in_array($sortByRaw, $allowed, true) ? $sortByRaw : 'created_at';
        $orderByCol = $this->mapSortKey($sortKey, $LPL);

        $q->orderBy($orderByCol, $sortDir);
    }

    // Add these helpers inside the controller (private scope)

    /** Detect the alias used for lead_product_lists in a Builder. */
    private function lplAlias(\Illuminate\Database\Query\Builder $q): string
    {
        $from = $q->from; // e.g. "lead_product_lists as lpl" or "lead_product_lists"
        if (is_string($from)) {
            // "table as alias"
            if (preg_match('/^\s*\S+\s+as\s+(\w+)\s*$/i', $from, $m))
                return $m[1];
            // "table alias"
            if (preg_match('/^\s*\S+\s+(\w+)\s*$/i', $from, $m))
                return $m[1];
            // exact table name
            if (preg_match('/^\s*lead_product_lists\s*$/i', $from))
                return 'lead_product_lists';
        }
        // Fallback (safe for non-aliased builders)
        return 'lead_product_lists';
    }

    /** Map incoming sort keys to fully-qualified columns, alias-aware. */


    private function mapSortKey(string $key, string $LPL): string
    {
        $map = [
            'created_at' => "{$LPL}.created_at",
            'updated_at' => "{$LPL}.updated_at",
            'status' => "{$LPL}.status",
            'department_id' => "{$LPL}.department_id",
            'employee_id' => "{$LPL}.employee_id",
            'customer_id' => "new_leads.id",

            'lead_product_lists.id' => "{$LPL}.id",
            'new_leads.id' => "new_leads.id",

            'name' => "new_leads.name",
            'lastname' => "new_leads.lastname",

            // 🔴 FIX: Map the keys to the actual DB columns
            'customer_lastname' => "new_leads.lastname",
            'city' => "alt.city", // 'alt' is the alias for lead_alternative_adds
        ];
        return $map[$key] ?? "{$LPL}.updated_at";
    }

    // Generates a unique, sequential ticket number like "T-25-00001"

    private function randomTicketDigits(int $digits = 6): int
    {
        $min = (int) pow(10, $digits - 1); // 100000 for 6
        $max = (int) pow(10, $digits) - 1; // 999999 for 6
        return random_int($min, $max);
    }

    private function generateUniqueTicketNo(string $table = 'problems', string $col = 'ticket_no', int $digits = 6): int|string
    {
        // If the target column is INT, return int; if VARCHAR, return numeric string — both are fine.
        // We'll just return an int; DB will coerce or store as string if VARCHAR.
        $attempts = 0;
        do {
            $attempts++;
            $cand = $this->randomTicketDigits($digits);        // e.g. 475439
            $exists = DB::table($table)->where($col, $cand)->exists();
            if (!$exists)
                return $cand;
        } while ($attempts < 8);

        // Fallback: still return a random; insertion code will retry on duplicate key.
        return $this->randomTicketDigits($digits);
    }

    /** Resolve currently logged-in user's employee id for first_contact (FK expects employees.id) */
    private function currentEmployeeIdOrNull(): ?int
    {
        // In your setup, auth()->user()->name actually holds the employees.id
        $raw = auth()->user()->name ?? null;

        Log::info('[currentEmployeeIdOrNull] raw user->name', ['raw' => $raw]);

        if (!$raw || !ctype_digit((string) $raw)) {
            Log::warning('[currentEmployeeIdOrNull] raw is empty or not numeric', ['raw' => $raw]);
            return null;
        }

        $empId = (int) $raw;

        // Optional: verify it exists in employees table
        $exists = DB::table('employees')->where('id', $empId)->exists();
        if (!$exists) {
            Log::warning('[currentEmployeeIdOrNull] employee not found for id from auth()->user()->name', [
                'emp_id' => $empId,
            ]);
            return null;
        }

        Log::info('[currentEmployeeIdOrNull] resolved employee id', ['emp_id' => $empId]);

        return $empId;
    }


    public function ticketize(Request $request, int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $lpl = DB::table('lead_product_lists')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();
            if (!$lpl) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Lead nicht gefunden.'], 404);
            }

            $problemTable = 'problems';
            $now = now();
            $baseKey = [
                'customer_id' => $lpl->customer_id,
                'alternative_id' => $lpl->alternative_id,
                'product_id' => $lpl->product_id,
            ];

            // Check if ticket exists
            $exists = DB::table($problemTable)
                ->where($baseKey)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (!$exists) {
                // Prepare Insert Data
                $customer = DB::table('new_leads')->select('name', 'lastname')->find($lpl->customer_id);
                $product = DB::table('article_groups')->select('initial')->find($lpl->product_id);

                $titleVal = trim(($customer->lastname ?? '') . ' ' . ($customer->name ?? '')) ?: 'Kunde';
                $titleVal .= ' • ' . ($product->initial ?? 'Produkt') . ' • Ticket';

                $insert = array_merge($baseKey, [
                    'created_at' => $now,
                    'updated_at' => $now,
                    'status' => 'open',
                    'source' => 'lead',
                    'created_by' => auth()->id() ?? 1,
                    // Assume 'ticket_no' logic handled by your helper or defaults
                ]);

                // Add title if column exists
                if (Schema::hasColumn($problemTable, 'title'))
                    $insert['title'] = $titleVal;
                elseif (Schema::hasColumn($problemTable, 'subject'))
                    $insert['subject'] = $titleVal;

                // Insert Ticket
                DB::table($problemTable)->insert($insert);
            } else {
                // Ensure existing ticket is open
                DB::table($problemTable)->where('id', $exists->id)->update([
                    'status' => 'open',
                    'updated_at' => $now
                ]);
            }

            // Update Lead status to 'ticket'
            DB::table('lead_product_lists')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->update([
                    'status' => 'ticket',
                    'updated_at' => $now,
                ]);

            // ★ LOG ACTIVITY
            $this->logActivity('updated', 'App\Models\LeadProductList', $id, $lpl->customer_id, $lpl->alternative_id, $lpl->product_id, [
                'stage' => ['from' => $lpl->status, 'to' => 'ticket'],
                'info' => 'Lead in Ticket umgewandelt'
            ]);

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ticketize failed', ['lpl' => $id, 'err' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Serverfehler beim Ticketisieren.'], 500);
        }
    }

    /** Base query for TICKETS tab with progress aggregates. */
    private function baseTicketsQuery(): \Illuminate\Database\Query\Builder
    {
        $taskAgg = DB::table('ticket_tasks as tt')
            ->select(
                'tt.ticket_id',
                DB::raw("COUNT(*) as total_tasks"),
                DB::raw("SUM(CASE WHEN (LOWER(COALESCE(tt.status,'')) IN ('open','todo') OR (tt.status IS NULL AND COALESCE(tt.is_done,0)=0)) THEN 1 ELSE 0 END) as open_tasks"),
                DB::raw("SUM(CASE WHEN LOWER(COALESCE(tt.status,'')) IN ('in_progress','doing') THEN 1 ELSE 0 END) as progress_tasks"),
                DB::raw("SUM(CASE WHEN LOWER(COALESCE(tt.status,'')) IN ('done','completed') OR COALESCE(tt.is_done,0)=1 THEN 1 ELSE 0 END) as done_tasks"),
                DB::raw("SUM(COALESCE(JSON_LENGTH(tt.teams),0)) as team_slots")
            )
            ->groupBy('tt.ticket_id');

        $empAgg = DB::table('employee_problem as ep')
            ->join('employees as e', 'e.id', '=', 'ep.employee_id')
            ->select(
                'ep.problem_id',
                DB::raw('COUNT(*) as ep_count'),
                DB::raw("GROUP_CONCAT(CONCAT(e.lastname, ' ', e.name) SEPARATOR ', ') as ep_names")
            )
            ->groupBy('ep.problem_id');

        return DB::table('problems as p')
            ->join('new_leads as nl', 'nl.id', '=', 'p.customer_id')
            ->leftJoin('branches as br', 'br.id', '=', 'nl.branch')
            ->leftJoin('lead_alternative_adds as alt', 'alt.id', '=', 'p.alternative_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'p.product_id')
            ->leftJoin('employees as er', 'er.id', '=', 'p.responsible')
            ->leftJoinSub($taskAgg, 'agg', fn($j) => $j->on('agg.ticket_id', '=', 'p.id'))
            ->leftJoinSub($empAgg, 'ep', fn($j) => $j->on('ep.problem_id', '=', 'p.id'))
            ->whereNull('p.deleted_at')
            ->whereNull('nl.deleted_at')
            ->whereNull('alt.deleted_at')
            ->select([
                'p.id as ticket_id',
                'p.ticket_no',
                'p.status',
                'p.priority',
                'p.source',
                'p.date as created_at',
                'p.updated_at',
                'p.customer_id',
                'p.alternative_id',
                'p.product_id',

                'nl.name as customer_name',
                'nl.lastname as customer_lastname',
                'nl.firma',

                'nl.branch as branch_id',
                'br.branch as branch_name',
                'br.initial as branch_initial',

                'alt.street',
                'alt.postcode',
                'alt.city',
                'ag.initial as product_initial',
                'er.id as responsible_id',
                'er.name as responsible_name',
                'er.lastname as responsible_lastname',
                'er.image as responsible_image',

                DB::raw('COALESCE(ep.ep_count,0) as team_count'),
                DB::raw('ep.ep_names as team_names'),
                DB::raw('COALESCE(agg.total_tasks,0)      as total_tasks'),
                DB::raw('COALESCE(agg.open_tasks,0)       as open_tasks'),
                DB::raw('COALESCE(agg.progress_tasks,0)   as progress_tasks'),
                DB::raw('COALESCE(agg.done_tasks,0)       as done_tasks'),
                DB::raw('COALESCE(agg.team_slots,0)       as team_slots'),
            ]);
    }

    /**
     * Apply the SAME drawer filters to tickets as to leads.
     * Requires baseTicketsQuery() to have:
     *  - leftJoin('branches as br', 'br.id', '=', 'nl.branch')
     */
    private function applyTicketFilters(\Illuminate\Database\Query\Builder $q, Request $request): void
    {
        // -------------------- Free text search --------------------
        if (($term = trim((string) $request->input('search', ''))) !== '') {
            $q->where(function ($qq) use ($term) {
                $qq->where('nl.name', 'like', "%{$term}%")
                    ->orWhere('nl.lastname', 'like', "%{$term}%")
                    ->orWhere('alt.street', 'like', "%{$term}%")
                    ->orWhere('alt.city', 'like', "%{$term}%")
                    ->orWhere('alt.postcode', 'like', "%{$term}%")
                    ->orWhere('ag.article_group', 'like', "%{$term}%")
                    ->orWhere('p.ticket_no', 'like', "%{$term}%")
                    // ✅ BRANCH searchable
                    ->orWhere('br.branch', 'like', "%{$term}%")
                    ->orWhere('br.initial', 'like', "%{$term}%");
            });
        }

        // -------------------- Dropdown filters --------------------
        if ($request->filled('customer'))
            $q->where('p.customer_id', $request->customer);
        if ($request->filled('product'))
            $q->where('p.product_id', $request->product);

        // ✅ BRANCH dropdown filter (id or name/initial)
        if ($request->filled('branch')) {
            $branchRaw = trim((string) $request->branch);

            if (ctype_digit($branchRaw)) {
                $q->where('nl.branch', (int) $branchRaw);
            } else {
                $branchIds = DB::table('branches')
                    ->where('branch', 'like', "%{$branchRaw}%")
                    ->orWhere('initial', 'like', "%{$branchRaw}%")
                    ->pluck('id');

                if ($branchIds->isNotEmpty()) {
                    $q->whereIn('nl.branch', $branchIds->all());
                } else {
                    $q->whereRaw('1=0');
                }
            }
        }

        // employee filter — responsible OR any employee_problem
        if ($request->filled('employee')) {
            $emp = trim((string) $request->employee);

            $ids = ctype_digit($emp)
                ? collect([(int) $emp])
                : DB::table('employees')
                    ->where('name', 'like', "%{$emp}%")
                    ->orWhere('lastname', 'like', "%{$emp}%")
                    ->pluck('id');

            if ($ids->isEmpty()) {
                $q->whereRaw('1=0');
            } else {
                $idArr = $ids->all();

                $q->where(function ($qq) use ($idArr) {
                    $qq->whereIn('p.responsible', $idArr)
                        ->orWhereIn('p.id', function ($sub) use ($idArr) {
                            $sub->from('employee_problem')
                                ->whereIn('employee_id', $idArr)
                                ->select('problem_id');
                        });
                });
            }
        }

        // -------------------- Date range --------------------
        if ($request->filled('date_from')) {
            $q->whereRaw('DATE(COALESCE(p.created_at, p.date)) >= ?', [$request->date_from]);
        }
        if ($request->filled('date_to')) {
            $q->whereRaw('DATE(COALESCE(p.created_at, p.date)) <= ?', [$request->date_to]);
        }

        // -------------------- Stage guard --------------------
        if ($request->filled('stage')) {
            $st = strtolower(trim((string) $request->stage));
            if ($st !== 'ticket')
                $q->whereRaw('1=0');
        }

        // -------------------- Status group --------------------
        if ($request->filled('status_group')) {
            $g = strtolower(trim((string) $request->status_group));
            $norm = "LOWER(COALESCE(NULLIF(p.status,''),'open'))";

            if ($g === 'offen') {
                $q->whereIn(DB::raw($norm), ['open']);
            } elseif ($g === 'zusage') {
                $q->whereIn(DB::raw($norm), ['in progress', 'in_progress', 'progress']);
            } elseif ($g === 'absage') {
                $q->whereNotIn(DB::raw($norm), ['open', 'in progress', 'in_progress', 'progress']);
            }
        }
    }



    /** Server-rendered partial for the Ticket tab. */
    public function ticketsPartial(Request $request)
    {
        Log::info('[ticketsPartial] incoming', [
            'params' => $request->all(),
        ]);

        $q = $this->baseTicketsQuery();
        $this->applyTicketFilters($q, $request);

        $sortBy = $request->get('sort_by', 'p.created_at');
        $sortDir = strtolower($request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowed = ['p.created_at', 'p.updated_at', 'p.priority', 'p.status', 'p.ticket_no', 'nl.lastname', 'alt.city', 'ag.initial'];
        if (!in_array($sortBy, $allowed, true))
            $sortBy = 'p.created_at';
        $q->orderBy($sortBy, $sortDir);

        $tickets = $q->paginate(12, ['*'], 'ticket_page')->appends($request->all());

        $total = $tickets->total();

        return view('admin.kanban.partials.ticket', [
            'tickets' => $tickets,
            'total' => $total,
        ]);
    }


    public function tickets(Request $request)
    {
        $perPage = 12;

        $q = DB::table('problems as p')
            ->join('new_leads as nl', 'nl.id', '=', 'p.customer_id')
            ->leftJoin('lead_alternative_adds as alt', 'alt.id', '=', 'p.alternative_id')
            ->join('article_groups as ag', 'ag.id', '=', 'p.product_id')
            ->whereNull('p.deleted_at')
            ->whereNull('nl.deleted_at')
            ->whereNull('alt.deleted_at')
            ->where('p.status', '!=', 'end')
            ->select([
                'p.id',
                'p.ticket_no',
                'p.status',
                'p.updated_at',
                'p.customer_id',
                'p.alternative_id',
                'p.product_id',
                'nl.name as customer_name',
                'nl.lastname as customer_lastname',
                'alt.city',
                'ag.article_group', // ← change
            ]);


        // Optional filters from your drawer/querystring:
        if ($s = $request->filled('status_group') ? $request->string('status_group')->lower() : null) {
            if ($s === 'offen')
                $q->whereIn(DB::raw('LOWER(COALESCE(NULLIF(p.status,""),"open"))'), ['open']);
            if ($s === 'zusage')
                $q->whereIn(DB::raw('LOWER(COALESCE(NULLIF(p.status,""),"open"))'), ['in progress', 'progress', 'in prograss', 'inprogess']);
            if ($s === 'absage')
                $q->whereNotIn(DB::raw('LOWER(COALESCE(NULLIF(p.status,""),"open"))'), ['open', 'in progress', 'progress', 'in prograss', 'inprogess']);
        }

        $tickets = $q->orderByDesc('p.updated_at')
            ->paginate($perPage, ['*'], 'page')
            ->appends($request->query());

        $total = $tickets->total();

        return view('admin.kanban.partials.ticket', compact('tickets', 'total'));
    }

    /**
     * Search customers for the appointment drawer (Select2 format).
     */
    public function appointmentCustomerSearch(Request $request): JsonResponse
    {
        $term = trim($request->get('q', ''));

        $query = NewLeads::query();

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('lastname', 'like', "%{$term}%")
                    ->orWhere('city', 'like', "%{$term}%")
                    ->orWhere('postcode', 'like', "%{$term}%");
            });
        }

        $customers = $query
            ->orderBy('lastname')
            ->limit(20)
            ->get();

        $results = $customers->map(function (NewLeads $c) {
            $fullAddress = trim(implode(' ', array_filter([
                $c->street ?? null,
                $c->house_number ?? null,
                $c->postcode ?? null,
                $c->city ?? null,
            ])));

            return [
                'id' => $c->id,
                'text' => trim(($c->lastname . ' ' . $c->name) . ' • ' . ($c->city ?? '')),
                'full_address' => $fullAddress,
                'street' => $c->street ?? null,
                'postcode' => $c->postcode ?? null,
                'city' => $c->city ?? null,
                'phone' => $c->mobile ?? $c->phone ?? null,
                'email' => $c->email ?? null,
                'latitude' => $c->latitude ?? null,
                'longitude' => $c->longitude ?? null,
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * List appointments for one lead (customer + alt + product).
     */
    public function appointmentsIndex(Request $request): JsonResponse
    {
        $customerId = $request->integer('customer_id');
        $alternativeId = $this->nullableInt($request->input('alternative_id'));
        $productId = $this->nullableInt($request->input('product_id'));
        $leadProductListId = $this->nullableInt($request->input('lead_product_list_id'));

        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'customer_id ist erforderlich.',
                'appointments' => [],
            ], 422);
        }

        $query = MainAppointment::query()
            ->with(['employees:id,name,lastname,image'])
            ->where('customer_id', $customerId);

        /*
         * Do NOT force only type=appointment.
         * Some Kanban appointments can be stored as:
         * appointment, kanban_task, customer_appointment, phone, online, etc.
         */
        $query->where(function ($q) {
            $q->whereNull('type')
                ->orWhereIn('type', [
                    'appointment',
                    'customer_appointment',
                    'kanban',
                    'kanban_task',
                    'phone',
                    'online',
                    'termin',
                ]);
        });

        if (Schema::hasColumn('main_appointments', 'lead_product_list_id') && $leadProductListId) {
            $query->where('lead_product_list_id', $leadProductListId);
        }

        if (Schema::hasColumn('main_appointments', 'alternative_id') && $alternativeId) {
            $query->where('alternative_id', $alternativeId);
        }

        if (Schema::hasColumn('main_appointments', 'product_id') && $productId) {
            $query->where('product_id', $productId);
        }

        /*
         * Fallback for your mixed products JSON:
         * [17]
         * [{"uid":"WÄRMEPUMPE_6655","product_id":16,"alternative_id":6655,"customer_id":"6505"}]
         */
        if (Schema::hasColumn('main_appointments', 'products') && ($productId || $alternativeId)) {
            $query->where(function ($scope) use ($productId, $alternativeId, $customerId) {
                $scope->whereNull('products')
                    ->orWhere('products', '')
                    ->orWhere('products', '[]');

                if ($productId) {
                    $scope->orWhereJsonContains('products', (int) $productId)
                        ->orWhereJsonContains('products', (string) $productId)
                        ->orWhere(function ($jsonObjectScope) use ($productId, $alternativeId, $customerId) {
                            $jsonObjectScope
                                ->where(function ($productJson) use ($productId) {
                                    $productJson
                                        ->where('products', 'like', '%"product_id":' . (int) $productId . '%')
                                        ->orWhere('products', 'like', '%"product_id":"' . (int) $productId . '"%');
                                })
                                ->where(function ($customerJson) use ($customerId) {
                                    $customerJson
                                        ->where('products', 'not like', '%"customer_id"%')
                                        ->orWhere('products', 'like', '%"customer_id":' . (int) $customerId . '%')
                                        ->orWhere('products', 'like', '%"customer_id":"' . (int) $customerId . '"%');
                                });

                            if ($alternativeId) {
                                $jsonObjectScope->where(function ($alternativeJson) use ($alternativeId) {
                                    $alternativeJson
                                        ->where('products', 'not like', '%"alternative_id"%')
                                        ->orWhere('products', 'like', '%"alternative_id":' . (int) $alternativeId . '%')
                                        ->orWhere('products', 'like', '%"alternative_id":"' . (int) $alternativeId . '"%');
                                });
                            }
                        });
                } elseif ($alternativeId) {
                    $scope->orWhere(function ($alternativeOnlyScope) use ($alternativeId, $customerId) {
                        $alternativeOnlyScope
                            ->where(function ($alternativeJson) use ($alternativeId) {
                                $alternativeJson
                                    ->where('products', 'like', '%"alternative_id":' . (int) $alternativeId . '%')
                                    ->orWhere('products', 'like', '%"alternative_id":"' . (int) $alternativeId . '"%');
                            })
                            ->where(function ($customerJson) use ($customerId) {
                                $customerJson
                                    ->where('products', 'not like', '%"customer_id"%')
                                    ->orWhere('products', 'like', '%"customer_id":' . (int) $customerId . '%')
                                    ->orWhere('products', 'like', '%"customer_id":"' . (int) $customerId . '"%');
                            });
                    });
                }
            });
        }

        if (Schema::hasColumn('main_appointments', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        if (Schema::hasColumn('main_appointments', 'is_deleted')) {
            $query->where(function ($q) {
                $q->whereNull('is_deleted')->orWhere('is_deleted', 0);
            });
        }

        $appointments = $query
            ->orderByRaw('COALESCE(start_date, created_at) DESC')
            ->orderByDesc('start_time')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'appointments' => $appointments,
            'count' => $appointments->count(),
        ]);
    }


    /**
     * Create appointment.
     */
    public function appointmentsStore(Request $request): JsonResponse
    {
        $data = $this->validateAppointment($request);

        DB::beginTransaction();
        try {
            $appointment = new MainAppointment();

            $appointment->created_by = auth()->user()->name;
            $appointment->type = 'appointment';
            $appointment->source = 'lead';

            $appointment->customer_id = $data['customer_id'];
            $appointment->name = $data['name'];
            $appointment->note = $data['note'] ?? null;
            $appointment->start_date = $data['start_date'];
            $appointment->start_time = $data['start_time'] ?? null;
            $appointment->end_time = $data['end_time'] ?? null;
            $appointment->appointment_type = $data['appointment_type'] ?? null;
            $appointment->contact_mode = $data['contact_mode'] ?? null;
            $appointment->color = $data['color'] ?? null;

            $appointment->full_address = $data['full_address'] ?? null;
            $appointment->street = $data['street'] ?? null;
            $appointment->postcode = $data['postcode'] ?? null;
            $appointment->city = $data['city'] ?? null;
            $appointment->phone = $data['phone'] ?? null;
            $appointment->email = $data['email'] ?? null;
            $appointment->latitude = $data['latitude'] ?? null;
            $appointment->longitude = $data['longitude'] ?? null;

            // store lead context in JSON "products"


            $appointment->save();

            if (!empty($data['employee_ids'])) {
                $appointment->employees()->sync($data['employee_ids']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'appointment' => $appointment->load('employees:id,name,lastname,image'),
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('appointmentsStore failed', ['err' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Termin konnte nicht gespeichert werden.',
            ], 500);
        }
    }

    /**
     * Update appointment.
     */
    public function appointmentsUpdate(Request $request, MainAppointment $appointment): JsonResponse
    {
        $data = $this->validateAppointment($request);

        DB::beginTransaction();
        try {
            $appointment->customer_id = $data['customer_id'];
            $appointment->name = $data['name'];
            $appointment->note = $data['note'] ?? null;
            $appointment->start_date = $data['start_date'];
            $appointment->start_time = $data['start_time'] ?? null;
            $appointment->end_time = $data['end_time'] ?? null;
            $appointment->appointment_type = $data['appointment_type'] ?? null;
            $appointment->contact_mode = $data['contact_mode'] ?? null;
            $appointment->color = $data['color'] ?? null;

            $appointment->full_address = $data['full_address'] ?? null;
            $appointment->street = $data['street'] ?? null;
            $appointment->postcode = $data['postcode'] ?? null;
            $appointment->city = $data['city'] ?? null;
            $appointment->phone = $data['phone'] ?? null;
            $appointment->email = $data['email'] ?? null;
            $appointment->latitude = $data['latitude'] ?? null;
            $appointment->longitude = $data['longitude'] ?? null;


            $appointment->save();

            $appointment->employees()->sync($data['employee_ids'] ?? []);

            DB::commit();

            return response()->json([
                'success' => true,
                'appointment' => $appointment->load('employees:id,name,lastname,image'),
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('appointmentsUpdate failed', ['err' => $e->getMessage(), 'id' => $appointment->id]);
            return response()->json([
                'success' => false,
                'message' => 'Termin konnte nicht aktualisiert werden.',
            ], 500);
        }
    }

    /**
     * Delete appointment.
     */
    public function appointmentsDestroy(MainAppointment $appointment): JsonResponse
    {
        try {
            $appointment->delete();

            return response()->json([
                'success' => true,
            ]);
        } catch (Throwable $e) {
            Log::error('appointmentsDestroy failed', ['err' => $e->getMessage(), 'id' => $appointment->id]);
            return response()->json([
                'success' => false,
                'message' => 'Termin konnte nicht gelöscht werden.',
            ], 500);
        }
    }

    /**
     * Shared validator for store / update.
     */
    protected function validateAppointment(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],

            'name' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'appointment_type' => ['nullable', 'string', 'max:255'],
            'contact_mode' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:50'],

            'full_address' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],

            'employee_ids' => ['array'],
            'employee_ids.*' => ['integer', 'exists:employees,id'],
        ]);
    }

    public function investment(Request $request)
    {
        Log::info('[investment] incoming', ['params' => $request->all()]);

        $q = LeadProductList::query()
            ->join('new_leads', 'lead_product_lists.customer_id', '=', 'new_leads.id')
            ->join('article_groups', 'lead_product_lists.product_id', '=', 'article_groups.id')

            // ✅ BRANCH (so we can filter/search by branch if needed later)
            ->leftJoin('branches as br', 'br.id', '=', 'new_leads.branch')

            ->leftJoin('employees', 'lead_product_lists.employee_id', '=', 'employees.id')
            ->leftJoin('departments', 'lead_product_lists.department_id', '=', 'departments.id')
            ->whereNull('lead_product_lists.deleted_at')
            ->whereNull('new_leads.deleted_at')
            ->select(
                'new_leads.id as customer_id',
                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',
                'article_groups.id as product_id',
                'article_groups.article_group as product_name',

                DB::raw('MIN(article_groups.min_value) as min_value'),
                DB::raw('MAX(article_groups.max_value) as max_value'),
                DB::raw('COUNT(*) as product_count'),
                DB::raw('SUM(COALESCE(article_groups.min_value, 0)) as sum_min_value'),
                DB::raw('SUM(COALESCE(article_groups.max_value, 0)) as sum_max_value')
            );

        $norm = "CASE
                    WHEN lead_product_lists.status IS NULL
                        OR lead_product_lists.status = ''
                    THEN 'lead'
                    ELSE LOWER(lead_product_lists.status)
                END";

        $q->where(function ($w) {
            $w->whereNull('lead_product_lists.status')
                ->orWhere('lead_product_lists.status', '')
                ->orWhereNotIn(DB::raw('LOWER(lead_product_lists.status)'), ['archive', 'archiv', 'junk', 'ticket']);
        });

        // ---------------- Filter ----------------
        if ($request->filled('customer'))
            $q->where('lead_product_lists.customer_id', $request->customer);
        if ($request->filled('product'))
            $q->where('lead_product_lists.product_id', $request->product);

        // ✅ BRANCH filter (id or name/initial)
        if ($request->filled('branch')) {
            $branchRaw = trim((string) $request->branch);

            if (ctype_digit($branchRaw)) {
                $q->where('new_leads.branch', (int) $branchRaw);
            } else {
                $branchIds = DB::table('branches')
                    ->where('branch', 'like', "%{$branchRaw}%")
                    ->orWhere('initial', 'like', "%{$branchRaw}%")
                    ->pluck('id');

                if ($branchIds->isNotEmpty()) {
                    $q->whereIn('new_leads.branch', $branchIds->all());
                } else {
                    $q->whereRaw('1=0');
                }
            }
        }

        if ($request->filled('employee')) {
            $emp = $request->employee;

            if (ctype_digit((string) $emp)) {
                $q->where('lead_product_lists.employee_id', (int) $emp);
            } else {
                $q->where(function ($qq) use ($emp) {
                    $qq->where('employees.name', 'like', "%{$emp}%")
                        ->orWhere('employees.lastname', 'like', "%{$emp}%");
                });
            }
        }

        if ($request->filled('department')) {
            $dep = $request->department;

            if (ctype_digit((string) $dep)) {
                $q->where('lead_product_lists.department_id', (int) $dep);
            } else {
                $q->where('departments.department_name', 'like', "%{$dep}%");
            }
        }

        if ($request->filled('interest'))
            $q->where('lead_product_lists.interest', $request->interest);
        if ($request->filled('date_from'))
            $q->whereDate('lead_product_lists.created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))
            $q->whereDate('lead_product_lists.created_at', '<=', $request->date_to);

        if ($request->filled('stage')) {
            $stage = strtolower(trim((string) $request->stage));
            $aliases = [
                'angebot' => 'offer',
                'nachfassen' => 'follow_up',
                'followup' => 'follow_up',
                'annehmen' => 'accepted',
                'angenommen' => 'accepted',
                'auftrag' => 'deal',
                'montage' => 'project',
                'abschluss' => 'completed',
                'archiv' => 'archive',
                'rejeck' => 'junk',
                'reject' => 'junk',
            ];
            $canonical = $aliases[$stage] ?? $stage;
            $q->where(DB::raw($norm), '=', $canonical);
        }

        if ($request->filled('status_group')) {
            $group = strtolower(trim((string) $request->status_group));

            if ($group === 'offen') {
                $q->whereIn(DB::raw($norm), $this->openStageKeys());
            } elseif ($group === 'zusage') {
                $q->whereIn(DB::raw($norm), $this->positiveStageKeys());
            } elseif ($group === 'absage') {
                $q->whereNotIn(DB::raw($norm), array_unique(array_merge($this->openStageKeys(), $this->positiveStageKeys())));
            }
        }


        $debugQ = clone $q;
        Log::info('[investment] base query before groupBy', [
            'sql' => $debugQ->toSql(),
            'bindings' => $debugQ->getBindings(),
        ]);

        $rows = $q->groupBy(
            'new_leads.id',
            'new_leads.name',
            'new_leads.lastname',
            'article_groups.id',
            'article_groups.article_group'
        )
            ->get();

        Log::info('[investment] rows fetched', [
            'rows_count' => $rows->count(),
        ]);

        // ------------- Aggregation in PHP -------------

        $customers = [];
        $productMap = [];
        $overallMin = 0.0;
        $overallMax = 0.0;
        $overallCount = 0;

        foreach ($rows as $row) {
            $cid = $row->customer_id;

            if (!isset($customers[$cid])) {
                $customers[$cid] = [
                    'id' => $cid,
                    'name' => trim(($row->customer_lastname ?? '') . ' ' . ($row->customer_name ?? '')) ?: ('Kunde #' . $cid),
                    'products' => [],
                    'total_min' => 0.0,
                    'total_max' => 0.0,
                    'total_count' => 0,
                    'avg_min' => 0.0,
                    'avg_max' => 0.0,
                    'avg_total' => 0.0,
                    'request_val' => 0.0, // Anfragewert pro Kunde
                ];
            }

            $min = $row->min_value !== null ? (float) $row->min_value : 0.0;
            $max = $row->max_value !== null ? (float) $row->max_value : 0.0;
            $count = (int) $row->product_count;

            $sumMin = (float) $row->sum_min_value;
            $sumMax = (float) $row->sum_max_value;

            $customers[$cid]['products'][$row->product_id] = [
                'id' => $row->product_id,
                'name' => $row->product_name,
                'min_value' => $min,
                'max_value' => $max,
                'count' => $count,
                'sum_min' => $sumMin,
                'sum_max' => $sumMax,
            ];

            $customers[$cid]['total_min'] += $sumMin;
            $customers[$cid]['total_max'] += $sumMax;
            $customers[$cid]['total_count'] += $count;

            $overallMin += $sumMin;
            $overallMax += $sumMax;
            $overallCount += $count;

            $productMap[$row->product_id] = $row->product_name;
        }

        foreach ($customers as &$customer) {
            $cnt = $customer['total_count'] ?? 0;

            if ($cnt > 0) {
                $customer['avg_min'] = $customer['total_min'] / $cnt;
                $customer['avg_max'] = $customer['total_max'] / $cnt;
                $customer['avg_total'] = ($customer['total_min'] + $customer['total_max']) / $cnt;
            } else {
                $customer['avg_min'] =
                    $customer['avg_max'] =
                    $customer['avg_total'] = 0.0;
            }

            // Anfragewert pro Kunde = (Summe Min + Summe Max) / 2
            $customer['request_val'] = ($customer['total_min'] + $customer['total_max']) / 2;
        }
        unset($customer);

        if ($overallCount > 0) {
            $overallAvgMin = $overallMin / $overallCount;
            $overallAvgMax = $overallMax / $overallCount;
            $overallAvgTotal = ($overallMin + $overallMax) / $overallCount;
        } else {
            $overallAvgMin = $overallAvgMax = $overallAvgTotal = 0.0;
        }

        // Gesamt-Anfragewert = (Gesamtwert Min + Gesamtwert Max) / 2
        $overallRequest = ($overallMin + $overallMax) / 2;

        Log::info('[investment] totals', [
            'overallMin' => $overallMin,
            'overallMax' => $overallMax,
            'overallCount' => $overallCount,
            'overallAvgMin' => $overallAvgMin,
            'overallAvgMax' => $overallAvgMax,
            'overallAvgTotal' => $overallAvgTotal,
            'overallRequest' => $overallRequest,
            'customer_count' => count($customers),
        ]);

        $productColumns = collect($productMap)
            ->map(fn($name, $id) => (object) ['id' => $id, 'name' => $name])
            ->sortBy('name')
            ->values();

        return view('admin.kanban.partials.investment', [
            'customers' => $customers,
            'productColumns' => $productColumns,
            'overallMin' => $overallMin,
            'overallMax' => $overallMax,
            'overallCount' => $overallCount,
            'overallAvgMin' => $overallAvgMin,
            'overallAvgMax' => $overallAvgMax,
            'overallAvgTotal' => $overallAvgTotal,
            'overallRequest' => $overallRequest, // neu
        ]);
    }

    private function includeClosed(Request $request): bool
    {
        if ($request->boolean('include_closed'))
            return true;

        $s = strtolower(trim((string) $request->get('stage', '')));
        return in_array($s, ['archive', 'archiv', 'junk', 'ticket'], true);
    }
    public function updateStage(Request $request, LeadStage $stage): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'color' => ['nullable', 'string', 'max:40'],
            'icon' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'is_closed' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        /*
         |--------------------------------------------------------------------------
         | IMPORTANT
         |--------------------------------------------------------------------------
         | Do NOT change the technical key when renaming a phase.
         |
         | Example:
         | key  = accepted
         | name = Any new visible label
         |
         | lead_product_lists.status stores the key, so the key must stay stable.
         */
        DB::beginTransaction();

        try {
            $stage->forceFill([
                'name' => trim((string) $data['name']),
                'color' => $data['color'] ?? $stage->color ?? '#74b2d4',
                'icon' => $data['icon'] ?? $stage->icon ?? 'circle',
                'sort_order' => $data['sort_order'] ?? $stage->sort_order,
                'is_closed' => $request->has('is_closed') ? $request->boolean('is_closed') : (bool) $stage->is_closed,
                'is_active' => $request->has('is_active') ? $request->boolean('is_active') : (bool) $stage->is_active,

                // Keep original default/protected flags.
                'is_default' => (bool) $stage->is_default,
                'is_protected' => (bool) $stage->is_protected,
            ])->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Phase wurde aktualisiert.',
                'stage' => method_exists($this, 'stagePayload')
                    ? $this->stagePayload($stage->fresh(['subStages']))
                    : $stage->fresh(['subStages']),
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('LeadStageAdmin updateStage failed', [
                'stage_id' => $stage->id,
                'stage_key' => $stage->key,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Phase konnte nicht aktualisiert werden: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getKanbanView(Request $request)
    {
        $leads = collect($request->leads)->map(function ($lead) {
            $lead = (object) $lead;

            // Make sure employee is also an object if it exists
            if (isset($lead->employee) && is_array($lead->employee)) {
                $lead->employee = (object) $lead->employee;
            }

            return $lead;
        });

        return view('admin.new_leads.layouts.kanban', compact('leads'));
    }

    public function getCustomer($customer_id, $alternative_id, $product_id)
    {
        $stagesMap = [
            'lead' => 'lead',
            'offer' => 'offer',
            'deal' => 'deal',
            'project' => 'project',
            'completed' => 'completed',
            'junk' => 'junk',
            'ticket' => 'ticket',
            'reject' => 'reject'
        ];

        $leads = DB::table('lead_product_lists')
            ->join('new_leads', 'new_leads.id', '=', 'lead_product_lists.customer_id')
            ->join('lead_alternative_adds as alt', 'alt.id', '=', 'lead_product_lists.alternative_id')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->leftJoin('employees', 'employees.id', '=', 'lead_product_lists.employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'lead_product_lists.department_id')
            ->leftJoin('phase_sections', 'phase_sections.id', '=', 'lead_product_lists.service_id')
            ->select(
                'new_leads.id as customer_id',
                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',
                'new_leads.phone',
                'new_leads.email',
                'new_leads.firma',

                'alt.id as alternative_id',
                'alt.street',
                'alt.postcode',
                'alt.city',
                'alt.object_name',

                'article_groups.id as product_id',
                'article_groups.initial',

                'lead_product_lists.id as lead_product_id',
                'lead_product_lists.status as stage',
                'lead_product_lists.work_status',
                'lead_product_lists.service',
                'lead_product_lists.service_id',
                'lead_product_lists.department_id',
                'lead_product_lists.created_at',
                'lead_product_lists.updated_at',

                'departments.department_name',
                'phase_sections.phase_section',

                'employees.id as employee_id',
                'employees.name as employee_name',
                'employees.lastname as employee_lastname',
                'employees.image as employee_image'
            )
            ->where([
                ['lead_product_lists.customer_id', '=', $customer_id],
                ['lead_product_lists.alternative_id', '=', $alternative_id],
                ['lead_product_lists.product_id', '=', $product_id]
            ])
            ->whereNull('lead_product_lists.deleted_at')
            ->whereNull('alt.deleted_at')
            ->whereNull('new_leads.deleted_at')
            ->get();
        $this->hydrateLeadTeams($leads);


        $leads = $leads->map(function ($lead) use ($stagesMap) {
            $lead->stage = $stagesMap[strtolower($lead->stage ?? 'lead')] ?? 'lead';

            $lead->employee = $lead->employee_id ? (object) [
                'employee_id' => $lead->employee_id,
                'name' => $lead->employee_name,
                'lastname' => $lead->employee_lastname,
                'image' => $lead->employee_image
            ] : null;

            unset($lead->employee_name, $lead->employee_lastname, $lead->employee_image);

            return $lead;
        });

        return response()->json($leads);
    }

    public function stageWorkflowConfig(Request $request): JsonResponse
    {
        $mode = strtolower((string) $request->get('mode', 'company'));
        $productId = $request->integer('product_id') ?: null;

        if (!in_array($mode, ['company', 'product'], true)) {
            $mode = 'company';
        }

        if ($mode === 'company') {
            $stages = LeadStage::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
                ->map(fn(LeadStage $stage) => [
                    'type' => 'company',
                    'id' => $stage->id,
                    'key' => $stage->key,
                    'name' => $stage->name,
                    'color' => $stage->color ?: '#93c21c',
                    'icon' => $stage->icon ?: 'circle',
                    'sort_order' => $stage->sort_order,
                    'is_closed' => (bool) $stage->is_closed,
                ])
                ->values();

            return response()->json([
                'success' => true,
                'mode' => 'company',
                'product_id' => null,
                'stages' => $stages,
            ]);
        }

        if (!$productId) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte zuerst ein Produkt auswählen.',
                'stages' => [],
            ], 422);
        }

        $stageHasSortOrder = Schema::hasColumn('stages', 'sort_order');
        $phaseHasSortOrder = Schema::hasColumn('task_phases', 'sort_order');

        $stageQuery = Stage::query()
            ->with(['section:id,phase_section'])
            ->where('product_id', $productId)
            ->where('status', 'Published');

        if ($stageHasSortOrder) {
            $stageQuery->orderBy('sort_order');
        }

        $productStages = $stageQuery
            ->orderBy('id')
            ->get();

        $stageIds = $productStages->pluck('id')->filter()->values();

        $phaseSelect = ['id', 'stage_id', 'phase_name'];
        if ($phaseHasSortOrder) {
            $phaseSelect[] = 'sort_order';
        }

        $phaseQuery = TaskPhase::query()
            ->whereIn('stage_id', $stageIds)
            ->select($phaseSelect)
            ->orderBy('stage_id');

        if ($phaseHasSortOrder) {
            $phaseQuery->orderBy('sort_order');
        }

        $phasesByStage = $stageIds->isNotEmpty()
            ? $phaseQuery->orderBy('id')->get()->groupBy('stage_id')
            : collect();

        $stages = $productStages
            ->map(function (Stage $stage, int $stageIndex) use ($phasesByStage, $stageHasSortOrder, $phaseHasSortOrder) {
                $phaseRows = collect($phasesByStage->get($stage->id, collect()))
                    ->values()
                    ->map(fn($phase, int $phaseIndex) => [
                        'id' => $phase->id,
                        'name' => $phase->phase_name,
                        'sort_order' => $phaseHasSortOrder
                            ? (int) ($phase->sort_order ?? (($phaseIndex + 1) * 10))
                            : (($phaseIndex + 1) * 10),
                    ]);

                return [
                    'type' => 'product',
                    'id' => $stage->id,
                    'key' => 'product_stage_' . $stage->id,
                    'name' => $stage->stage,
                    'product_id' => $stage->product_id,
                    'phase_section_id' => $stage->phase_section_id,
                    'section_name' => $stage->section?->phase_section,
                    'sort_order' => $stageHasSortOrder
                        ? (int) ($stage->sort_order ?? (($stageIndex + 1) * 10))
                        : (($stageIndex + 1) * 10),
                    'color' => '#93c21c',
                    'icon' => 'layers',
                    'phases' => $phaseRows->values(),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'mode' => 'product',
            'product_id' => $productId,
            'stages' => $stages,
        ]);
    }

    public function moveStageWorkflow(Request $request, $leadProduct): JsonResponse
    {
        $data = $request->validate([
            'mode' => ['required', 'string', 'in:company,product'],
            'company_stage_key' => ['nullable', 'string', 'max:100'],
            'product_stage_id' => ['nullable', 'integer', 'exists:stages,id'],
            'product_task_phase_id' => ['nullable', 'integer', 'exists:task_phases,id'],
            'reason' => ['nullable', 'string', 'max:2000'],
            'teams' => ['nullable', 'array'],
            'teams.*' => ['integer', 'exists:employees,id'],
            'move_forward' => ['nullable', 'boolean'],
            'lead_stage_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
            'accepted_offer_folder_id' => ['nullable', 'integer', 'exists:offer_folders,id'],
            'skip_offer_gate_without_folder' => ['nullable', 'boolean'],
            'skip_offer_acceptance_without_offer' => ['nullable', 'boolean'],
        ]);

        $mode = $data['mode'];
        $hasStageModeColumn = Schema::hasColumn('lead_product_lists', 'stage_mode');
        $hasProductStageColumn = Schema::hasColumn('lead_product_lists', 'product_stage_id');
        $hasProductTaskPhaseColumn = Schema::hasColumn('lead_product_lists', 'product_task_phase_id');

        if ($mode === 'product' && (!$hasProductStageColumn || !$hasProductTaskPhaseColumn)) {
            return response()->json([
                'success' => false,
                'message' => 'Produkt-Workflow ist noch nicht vollständig mit lead_product_lists verbunden. Bitte Migration für product_stage_id und product_task_phase_id ausführen.',
            ], 422);
        }

        $reason = trim((string) ($data['reason'] ?? ''));
        $teamIds = collect($data['teams'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $employeeId = auth()->user()->name ?? auth()->id();
        $now = now();

        try {
            DB::beginTransaction();

            $leadProductId = (int) $leadProduct;

            $lead = LeadProductList::query()
                ->whereKey($leadProductId)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (!$lead) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => "LeadProduct #{$leadProductId} wurde nicht gefunden oder ist gelöscht.",
                ], 404);
            }

            $oldStatus = $this->normalizeStage($lead->status);
            $oldProductStageId = $hasProductStageColumn ? ($lead->product_stage_id ?? null) : null;
            $oldProductTaskPhaseId = $hasProductTaskPhaseColumn ? ($lead->product_task_phase_id ?? null) : null;

            $history = is_array($lead->stage_history) ? $lead->stage_history : [];
            $teamsHistory = is_array($lead->teams) ? $lead->teams : [];

            if ($mode === 'company') {
                $newCompanyStage = $this->normalizeStage($data['company_stage_key'] ?? null);

                if (!$this->stageExists($newCompanyStage)) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Diese Unternehmensphase existiert nicht oder ist deaktiviert.',
                    ], 422);
                }

                $acceptedOfferFolder = null;
                $offerGatePayload = null;

                if ($this->requiresAcceptedOfferBeforeEnteringDeal($oldStatus, $newCompanyStage)) {
                    $offerGatePayload = $this->resolveAcceptedOfferGateForLead(
                        $lead,
                        isset($data['accepted_offer_folder_id']) ? (int) $data['accepted_offer_folder_id'] : null,
                        $newCompanyStage,
                        ((bool) ($data['skip_offer_gate_without_folder'] ?? false) || (bool) ($data['skip_offer_acceptance_without_offer'] ?? false))
                    );

                    if (!empty($offerGatePayload['requires_offer_selection'])) {
                        DB::rollBack();

                        return response()->json($offerGatePayload, 200);
                    }

                    $acceptedOfferFolder = $offerGatePayload['accepted_folder'] ?? null;
                }

                $targetCompanySubStage = $this->resolveLeadSubStageForCompanyMove(
                    $newCompanyStage,
                    isset($data['lead_stage_sub_stage_id']) ? (int) $data['lead_stage_sub_stage_id'] : null
                );

                $companyProductStage = null;
                $companyProductTaskPhaseId = null;

                if ($hasProductStageColumn && !empty($data['product_stage_id'])) {
                    $companyProductStage = Stage::query()
                        ->where('id', (int) $data['product_stage_id'])
                        ->where('product_id', $lead->product_id)
                        ->where('status', 'Published')
                        ->first();

                    if (!$companyProductStage) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Die gewählte Produktphase gehört nicht zu diesem Produkt oder ist nicht veröffentlicht.',
                        ], 422);
                    }

                    if ($hasProductTaskPhaseColumn && !empty($data['product_task_phase_id'])) {
                        $phaseExists = TaskPhase::query()
                            ->where('id', (int) $data['product_task_phase_id'])
                            ->where('stage_id', $companyProductStage->id)
                            ->exists();

                        if (!$phaseExists) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Diese Unterphase gehört nicht zur gewählten Produktphase.',
                            ], 422);
                        }

                        $companyProductTaskPhaseId = (int) $data['product_task_phase_id'];
                    }
                }

                $teamObjects = $this->buildTeamStageObjects(
                    $teamIds,
                    $newCompanyStage,
                    $oldStatus,
                    $employeeId,
                    $now,
                    [
                        'company_stage_key' => $newCompanyStage,
                        'product_stage_id' => $companyProductStage?->id,
                        'product_stage_name' => $companyProductStage?->stage,
                        'product_task_phase_id' => $companyProductTaskPhaseId,
                    ]
                );

                $teamsHistory = $this->mergeTeamHistory($teamsHistory, $teamObjects);

                $offerGateSkippedWithoutFolder = (bool) ($offerGatePayload['skipped_offer_gate_without_folder'] ?? false);
                $offerAcceptanceStatus = $offerGateSkippedWithoutFolder
                    ? 'moved_without_offer_acceptance'
                    : ($acceptedOfferFolder ? 'accepted_offer' : null);

                $history[] = [
                    'mode' => 'company',
                    'from' => $oldStatus,
                    'to' => $newCompanyStage,
                    'stage' => $newCompanyStage,
                    'product_stage_id' => $companyProductStage?->id ?? $oldProductStageId,
                    'product_stage_name' => $companyProductStage?->stage,
                    'product_task_phase_id' => $companyProductTaskPhaseId ?? $oldProductTaskPhaseId,
                    'lead_stage_sub_stage_id' => $targetCompanySubStage?->id,
                    'lead_stage_sub_stage_key' => $targetCompanySubStage?->key,
                    'lead_stage_sub_stage_name' => $targetCompanySubStage?->name,
                    'accepted_offer_folder_id' => $acceptedOfferFolder?->id,
                    'offer_acceptance_status' => $offerAcceptanceStatus,
                    'moved_without_offer_acceptance' => $offerGateSkippedWithoutFolder,
                    'offer_gate_skipped_without_folder' => $offerGateSkippedWithoutFolder,
                    'changed_by' => $employeeId,
                    'changed_user_id' => auth()->id(),
                    'changed_employee_id' => $employeeId,
                    'changed_at' => $now->toDateTimeString(),
                    'description' => $reason,
                    'teams' => $teamObjects,
                ];

                if ($hasStageModeColumn) {
                    $lead->stage_mode = 'company';
                }
                $lead->old_stage = $oldStatus;
                $lead->stage = $newCompanyStage;
                $lead->status = $newCompanyStage;
                if ($hasProductStageColumn && $companyProductStage) {
                    $lead->product_stage_id = $companyProductStage->id;
                }
                if ($hasProductTaskPhaseColumn && $companyProductStage) {
                    $lead->product_task_phase_id = $companyProductTaskPhaseId;
                }
                if (Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
                    $lead->lead_stage_sub_stage_id = $targetCompanySubStage?->id;
                }

                if ($offerGateSkippedWithoutFolder) {
                    if (Schema::hasColumn('lead_product_lists', 'offer_acceptance_status')) {
                        $lead->offer_acceptance_status = 'moved_without_offer_acceptance';
                    }
                    if (Schema::hasColumn('lead_product_lists', 'accepted_offer_folder_id')) {
                        $lead->accepted_offer_folder_id = null;
                    }
                    if (Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance')) {
                        $lead->moved_without_offer_acceptance = true;
                    }
                    if (Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance_at')) {
                        $lead->moved_without_offer_acceptance_at = $now;
                    }
                    if (Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance_by')) {
                        $lead->moved_without_offer_acceptance_by = is_numeric($employeeId) ? (int) $employeeId : null;
                    }
                    if (Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance_reason')) {
                        $lead->moved_without_offer_acceptance_reason = $reason ?: 'Ohne angenommenes Angebot weiter verschoben.';
                    }
                } elseif ($acceptedOfferFolder) {
                    if (Schema::hasColumn('lead_product_lists', 'offer_acceptance_status')) {
                        $lead->offer_acceptance_status = 'accepted_offer';
                    }
                    if (Schema::hasColumn('lead_product_lists', 'accepted_offer_folder_id')) {
                        $lead->accepted_offer_folder_id = $acceptedOfferFolder->id;
                    }
                    if (Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance')) {
                        $lead->moved_without_offer_acceptance = false;
                    }
                    if (Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance_at')) {
                        $lead->moved_without_offer_acceptance_at = null;
                    }
                    if (Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance_by')) {
                        $lead->moved_without_offer_acceptance_by = null;
                    }
                    if (Schema::hasColumn('lead_product_lists', 'moved_without_offer_acceptance_reason')) {
                        $lead->moved_without_offer_acceptance_reason = null;
                    }
                }

                $lead->stage_history = $history;
                $lead->teams = $teamsHistory;
                $lead->save();

                if ($acceptedOfferFolder) {
                    $this->promoteAcceptedOfferFolderFromKanban(
                        $acceptedOfferFolder,
                        $lead,
                        $newCompanyStage,
                        $targetCompanySubStage,
                        $reason ?: 'Angebot wurde im Kanban als angenommen ausgewählt.',
                        $employeeId
                    );
                }

                $this->logActivity('updated', LeadProductList::class, $lead->id, $lead->customer_id, $lead->alternative_id, $lead->product_id, [
                    'stage' => ['from' => $oldStatus, 'to' => $newCompanyStage],
                    'info' => $reason ?: 'Unternehmensphase geändert',
                ]);

                DB::commit();

                $freshLead = $lead->fresh();
                if ($companyProductStage) {
                    $freshLead->product_stage_name = $companyProductStage->stage;
                    $freshLead->product_task_phase_name = $companyProductTaskPhaseId
                        ? TaskPhase::query()->where('id', $companyProductTaskPhaseId)->value('phase_name')
                        : null;
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Unternehmensphase wurde aktualisiert.',
                    'mode' => 'company',
                    'lead' => $freshLead,
                    'accepted_offer_folder_id' => $acceptedOfferFolder?->id,
                    'offer_acceptance_status' => $offerAcceptanceStatus,
                    'moved_without_offer_acceptance' => $offerGateSkippedWithoutFolder,
                    'offer_consistency' => $acceptedOfferFolder ? [
                        'accepted_folder_id' => $acceptedOfferFolder->id,
                        'target_stage' => $newCompanyStage,
                        'target_sub_stage_id' => $targetCompanySubStage?->id,
                    ] : null,
                    'final' => [
                        'team_ids' => $teamIds,
                        'team_assignments' => $teamObjects,
                    ],
                ]);
            }

            $targetProductStageId = $data['product_stage_id'] ?? null;

            if (!empty($data['move_forward'])) {
                $targetProductStageId = $this->nextProductStageId($lead);
            }

            if (!$targetProductStageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bitte wählen Sie eine Produktphase aus.',
                ], 422);
            }

            $productStage = Stage::query()
                ->where('id', $targetProductStageId)
                ->where('product_id', $lead->product_id)
                ->where('status', 'Published')
                ->first();

            if (!$productStage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diese Produktphase gehört nicht zu diesem Produkt oder ist nicht veröffentlicht.',
                ], 422);
            }

            $productTaskPhaseId = $data['product_task_phase_id'] ?? null;

            if ($productTaskPhaseId) {
                $phaseExists = TaskPhase::query()
                    ->where('id', $productTaskPhaseId)
                    ->where('stage_id', $productStage->id)
                    ->exists();

                if (!$phaseExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Diese Aufgabe/Phase gehört nicht zur gewählten Produktphase.',
                    ], 422);
                }
            }

            $productStageKey = 'product_stage_' . $productStage->id;

            $teamObjects = $this->buildTeamStageObjects(
                $teamIds,
                $productStageKey,
                $oldProductStageId ? 'product_stage_' . $oldProductStageId : null,
                $employeeId,
                $now,
                [
                    'product_stage_id' => $productStage->id,
                    'product_stage_name' => $productStage->stage,
                    'product_task_phase_id' => $productTaskPhaseId,
                ]
            );

            $teamsHistory = $this->mergeTeamHistory($teamsHistory, $teamObjects);

            $history[] = [
                'mode' => 'product',
                'from' => $oldProductStageId ? 'product_stage_' . $oldProductStageId : null,
                'to' => $productStageKey,
                'stage' => $productStageKey,
                'company_status' => $lead->status,
                'product_stage_id' => $productStage->id,
                'product_stage_name' => $productStage->stage,
                'old_product_stage_id' => $oldProductStageId,
                'product_task_phase_id' => $productTaskPhaseId,
                'old_product_task_phase_id' => $oldProductTaskPhaseId,
                'changed_by' => $employeeId,
                'changed_user_id' => auth()->id(),
                'changed_employee_id' => $employeeId,
                'changed_at' => $now->toDateTimeString(),
                'description' => $reason,
                'teams' => $teamObjects,
            ];

            if ($hasStageModeColumn) {
                $lead->stage_mode = 'product';
            }
            $lead->product_stage_id = $productStage->id;
            $lead->product_task_phase_id = $productTaskPhaseId;
            $lead->stage_history = $history;
            $lead->teams = $teamsHistory;
            $lead->save();

            $this->logActivity('updated', LeadProductList::class, $lead->id, $lead->customer_id, $lead->alternative_id, $lead->product_id, [
                'info' => 'Produktphase geändert: ' . $productStage->stage,
                'product_stage' => [
                    'from' => $oldProductStageId,
                    'to' => $productStage->id,
                ],
            ]);

            DB::commit();

            $freshLead = $lead->fresh();
            $freshLead->product_stage_name = $productStage->stage;
            $freshLead->product_task_phase_name = $productTaskPhaseId
                ? TaskPhase::query()->where('id', $productTaskPhaseId)->value('phase_name')
                : null;

            return response()->json([
                'success' => true,
                'message' => 'Produktphase wurde aktualisiert.',
                'mode' => 'product',
                'lead' => $freshLead,
                'final' => [
                    'team_ids' => $teamIds,
                    'team_assignments' => $teamObjects,
                ],
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('moveStageWorkflow failed', [
                'lead_product_id' => $leadProduct->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Serverfehler beim Verschieben: ' . $e->getMessage(),
            ], 500);
        }
    }


    private function stageSortOrder(?string $stageKey): ?int
    {
        $stageKey = $this->normalizeStage($stageKey);

        $stage = LeadStage::query()
            ->where('key', $stageKey)
            ->first(['id', 'sort_order']);

        return $stage ? (int) ($stage->sort_order ?? $stage->id) : null;
    }

    private function requiresAcceptedOfferBeforeEnteringDeal(?string $oldStage, ?string $newStage): bool
    {
        $oldStage = $this->normalizeStage($oldStage);
        $newStage = $this->normalizeStage($newStage);

        /*
        |--------------------------------------------------------------------------
        | Final Angebot selection gate
        |--------------------------------------------------------------------------
        | Do not ask when the card only moves through Angebot -> Nachfassen ->
        | Annehmen. The final offer must be selected only at the last business
        | step, when the card enters Auftrag / Deal.
        */
        if ($newStage !== 'deal') {
            return false;
        }

        // Already in Auftrag or later phases should not reopen the offer modal.
        if (in_array($oldStage, ['deal', 'project', 'completed', 'archive', 'junk', 'ticket'], true)) {
            return false;
        }

        $oldOrder = $this->stageSortOrder($oldStage);
        $dealOrder = $this->stageSortOrder('deal');

        if ($oldOrder !== null && $dealOrder !== null) {
            return $oldOrder < $dealOrder;
        }

        return in_array($oldStage, ['lead', 'offer', 'follow_up', 'accepted'], true);
    }

    private function resolveLeadSubStageForCompanyMove(string $stageKey, ?int $requestedSubStageId = null): ?LeadStageSubStage
    {
        if (!Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
            return null;
        }

        $stageKey = $this->normalizeStage($stageKey);

        $mainStage = LeadStage::query()
            ->where('key', $stageKey)
            ->first();

        if (!$mainStage) {
            return null;
        }

        if ($requestedSubStageId) {
            $requested = LeadStageSubStage::query()
                ->where('id', $requestedSubStageId)
                ->where('lead_stage_id', $mainStage->id)
                ->where('is_active', true)
                ->first();

            if ($requested) {
                return $requested;
            }
        }

        return LeadStageSubStage::query()
            ->where('lead_stage_id', $mainStage->id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();
    }

    private function offerFoldersForLead(LeadProductList $lead)
    {
        if (!Schema::hasTable('offer_folders')) {
            return collect();
        }

        return \App\Models\OfferFolder::query()
            ->with(['offer', 'detail'])
            ->where(function ($query) use ($lead) {
                $query->where(function ($q) use ($lead) {
                    $q->where('customer_id', $lead->customer_id)
                        ->where('alternative_id', $lead->alternative_id)
                        ->where('product_id', $lead->product_id);
                });

                if (Schema::hasTable('offers')) {
                    $query->orWhereHas('offer', function ($offerQuery) use ($lead) {
                        $offerQuery->where('customer_id', $lead->customer_id)
                            ->where('alternative_id', $lead->alternative_id)
                            ->where('product_id', $lead->product_id);
                    });
                }
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();
    }

    private function activeOfferFoldersForLead(LeadProductList $lead)
    {
        return $this->offerFoldersForLead($lead)
            ->filter(function ($folder) {
                $status = strtolower(trim((string) ($folder->status ?? '')));
                $offerStatus = strtolower(trim((string) ($folder->offer_status ?? '')));

                return !in_array($status, ['cancel', 'cancelled', 'deleted'], true)
                    && !in_array($offerStatus, ['cancel', 'cancelled'], true)
                    && !$folder->trashed();
            })
            ->values();
    }

    private function folderDocumentStatus($folder): string
    {
        return strtolower(trim((string) (
            $folder->document_status
            ?: $folder->detail?->document_status
            ?: $folder->offer?->detail?->document_status
            ?: 'offer'
        )));
    }

    private function acceptedOfferFolderPayload($folders): array
    {
        return collect($folders)->map(function ($folder) {
            $detail = $folder->detail ?: $folder->offer?->detail;
            $documentStatus = $this->folderDocumentStatus($folder);

            return [
                'id' => $folder->id,
                'offer_id' => $folder->offer_id,
                'name' => $folder->name ?: ('Ordner #' . $folder->id),
                'status' => $folder->status,
                'document_status' => $documentStatus,
                'offer_status' => $folder->offer_status,
                'deal_status' => $folder->deal_status,
                'is_deal' => $documentStatus === 'deal',
                'is_accepted' => $documentStatus === 'deal' || in_array(strtolower((string) $folder->offer_status), ['accepted', 'angebot_angenommen'], true),
                'total_gross' => $detail?->total_gross,
                'updated_at' => optional($folder->updated_at)->toDateTimeString(),
                'created_at' => optional($folder->created_at)->toDateTimeString(),
            ];
        })->values()->all();
    }

    private function resolveAcceptedOfferGateForLead(LeadProductList $lead, ?int $acceptedFolderId, string $targetStage, bool $skipWithoutFolder = false): array
    {
        $activeFolders = $this->activeOfferFoldersForLead($lead);

        if ($activeFolders->isEmpty()) {
            if ($skipWithoutFolder) {
                return [
                    'success' => true,
                    'accepted_folder' => null,
                    'folders' => [],
                    'skipped_offer_gate_without_folder' => true,
                ];
            }

            return [
                'success' => false,
                'requires_offer_selection' => true,
                'business_block' => true,
                'code' => 'OFFER_SELECTION_REQUIRED_NO_FOLDERS',
                'title' => 'Angebot auswählen erforderlich',
                'message' => 'Dieser Vorgang kann noch nicht in die Phase Auftrag verschoben werden, weil kein aktiver Angebotsordner gefunden wurde.',
                'help_text' => 'Sie können entweder zuerst ein Angebot erstellen oder diesen Schritt bewusst überspringen und die Phase trotzdem ändern.',
                'next_steps' => [
                    '1. Angebot öffnen oder neues Angebot erstellen.',
                    '2. Oder bewusst überspringen und die Kanban-Phase trotzdem ändern.',
                    '3. Der Übersprung wird in der Historie des Kanban-Vorgangs gespeichert.',
                ],
                'allow_skip_without_folder' => true,
                'target_stage' => $targetStage,
                'folders' => [],
            ];
        }

        if ($acceptedFolderId) {
            $accepted = $activeFolders->firstWhere('id', $acceptedFolderId);

            if (!$accepted) {
                return [
                    'success' => false,
                    'requires_offer_selection' => true,
                    'business_block' => true,
                    'code' => 'INVALID_ACCEPTED_OFFER_FOLDER',
                    'title' => 'Ungültiger Angebotsordner',
                    'message' => 'Der gewählte Angebotsordner gehört nicht zu diesem Kanban-Vorgang oder ist bereits storniert.',
                    'help_text' => 'Bitte wählen Sie einen aktiven Ordner aus der angezeigten Liste. Nur dieser Ordner wird zum Auftrag; alle anderen aktiven Ordner werden storniert.',
                    'target_stage' => $targetStage,
                    'folders' => $this->acceptedOfferFolderPayload($activeFolders),
                ];
            }

            return [
                'success' => true,
                'accepted_folder' => $accepted,
                'folders' => $this->acceptedOfferFolderPayload($activeFolders),
            ];
        }

        $alreadyAccepted = $activeFolders
            ->filter(fn($folder) => $this->folderDocumentStatus($folder) === 'deal')
            ->values();

        if ($alreadyAccepted->count() === 1) {
            return [
                'success' => true,
                'accepted_folder' => $alreadyAccepted->first(),
                'folders' => $this->acceptedOfferFolderPayload($activeFolders),
            ];
        }

        return [
            'success' => false,
            'requires_offer_selection' => true,
            'business_block' => true,
            'code' => 'OFFER_SELECTION_REQUIRED',
            'title' => 'Welches Angebot wurde angenommen?',
            'message' => 'Bevor der Vorgang in die Phase Auftrag verschoben wird, muss genau ein Angebotsordner als angenommen ausgewählt werden.',
            'help_text' => 'Der ausgewählte Ordner wird automatisch zum Auftrag. Alle anderen aktiven Ordner dieses Angebots werden automatisch storniert, damit Kanban und Angebot/Ordner konsistent bleiben.',
            'next_steps' => [
                'Wählen Sie den angenommenen Ordner aus der Liste.',
                'Bestätigen Sie mit „Dieses Angebot annehmen“.',
                'Danach verschiebt das System die Kanban-Karte und aktualisiert die Offer-/Auftrag-Daten in Echtzeit.',
            ],
            'target_stage' => $targetStage,
            'folders' => $this->acceptedOfferFolderPayload($activeFolders),
        ];
    }

    private function promoteAcceptedOfferFolderFromKanban($acceptedFolder, LeadProductList $lead, string $targetStage, ?LeadStageSubStage $targetSubStage, ?string $reason, $employeeId): void
    {
        $acceptedFolder->loadMissing(['offer', 'detail']);

        $dealSubStage = $this->resolveLeadSubStageForCompanyMove('deal', null);
        $dealStatusKey = $dealSubStage?->key ?: ($targetSubStage?->key ?: 'open');

        $history = is_array($acceptedFolder->history) ? $acceptedFolder->history : (json_decode($acceptedFolder->history ?? '[]', true) ?: []);
        $history[] = [
            'type' => 'kanban_offer_accepted',
            'title' => 'Angebot im Kanban angenommen',
            'target_stage' => $targetStage,
            'target_sub_stage_id' => $targetSubStage?->id,
            'reason' => $reason,
            'changed_by' => $employeeId,
            'created_at' => now()->toDateTimeString(),
        ];

        $acceptedFolder->status = 'active';
        $acceptedFolder->document_status = 'deal';
        $acceptedFolder->offer_status = 'accepted';
        $acceptedFolder->deal_status = $dealStatusKey;
        $acceptedFolder->history = array_values($history);
        $acceptedFolder->save();

        if ($acceptedFolder->detail) {
            $bio = is_array($acceptedFolder->detail->biography_data)
                ? $acceptedFolder->detail->biography_data
                : (json_decode($acceptedFolder->detail->biography_data ?? '[]', true) ?: []);

            $bio[] = [
                'type' => 'kanban_offer_accepted',
                'from' => $acceptedFolder->detail->document_status ?: 'offer',
                'to' => 'deal',
                'reason' => $reason,
                'changed_by' => $employeeId,
                'created_at' => now()->toDateTimeString(),
            ];

            $acceptedFolder->detail->document_status = 'deal';
            $acceptedFolder->detail->biography_data = array_values($bio);
            $acceptedFolder->detail->save();
        }

        $cancelledFolderIds = [];
        $otherFolders = $this->activeOfferFoldersForLead($lead)
            ->where('id', '!=', $acceptedFolder->id)
            ->values();

        foreach ($otherFolders as $folder) {
            $folderHistory = is_array($folder->history) ? $folder->history : (json_decode($folder->history ?? '[]', true) ?: []);
            $folderHistory[] = [
                'type' => 'auto_cancelled_by_kanban_acceptance',
                'title' => 'Automatisch storniert',
                'reason' => "Storniert, weil '{$acceptedFolder->name}' im Kanban als angenommen gewählt wurde.",
                'accepted_folder_id' => $acceptedFolder->id,
                'changed_by' => $employeeId,
                'created_at' => now()->toDateTimeString(),
            ];

            $folder->status = 'cancel';
            $folder->offer_status = 'cancelled';
            $folder->history = array_values($folderHistory);
            $folder->save();

            if ($folder->detail) {
                $bio = is_array($folder->detail->biography_data)
                    ? $folder->detail->biography_data
                    : (json_decode($folder->detail->biography_data ?? '[]', true) ?: []);
                $bio[] = [
                    'type' => 'auto_cancelled_by_kanban_acceptance',
                    'to' => 'cancel',
                    'reason' => 'Ein anderer Angebotsordner wurde angenommen.',
                    'accepted_folder_id' => $acceptedFolder->id,
                    'changed_by' => $employeeId,
                    'created_at' => now()->toDateTimeString(),
                ];
                $folder->detail->biography_data = array_values($bio);
                $folder->detail->save();
            }

            $cancelledFolderIds[] = $folder->id;
            $this->broadcastOfferFolderRealtime($folder, 'auto_cancelled_by_kanban');
        }

        $this->upsertDealFromAcceptedFolder($acceptedFolder, $lead, $employeeId);
        $this->broadcastOfferFolderRealtime($acceptedFolder, 'accepted_from_kanban', [
            'cancelled_folder_ids' => $cancelledFolderIds,
            'lead_product_id' => $lead->id,
            'target_stage' => $targetStage,
        ]);
    }

    private function upsertDealFromAcceptedFolder($folder, LeadProductList $lead, $employeeId): void
    {
        if (!Schema::hasTable('deals')) {
            return;
        }

        /*
         * Important:
         * Some existing installations have legacy NOT NULL columns on `deals`
         * without database defaults. The Kanban acceptance flow must therefore
         * build a complete payload before inserting. The previous implementation
         * missed `service`, which caused:
         * SQLSTATE[HY000]: Field 'service' doesn't have a default value.
         */
        $folder->loadMissing(['offer.detail', 'detail']);

        $offer = $folder->offer ?? null;
        $detail = $folder->detail ?? $offer?->detail ?? null;

        $now = now();
        $employeeId = is_numeric($employeeId) ? (int) $employeeId : null;

        $service = trim((string) (
            $lead->service
            ?? $offer?->service
            ?? $folder->service
            ?? $folder->name
            ?? 'Angebot'
        ));

        if ($service === '') {
            $service = 'Angebot';
        }

        $serviceId = $lead->service_id
            ?? $offer?->service_id
            ?? $folder->service_id
            ?? null;

        $departmentId = $lead->department_id
            ?? $offer?->department_id
            ?? $folder->department_id
            ?? null;

        $offerNumber = $offer?->offer_no
            ?? $offer?->offer_number
            ?? null;

        $price = null;
        foreach ([
            $detail?->total_gross ?? null,
            $detail?->total_net ?? null,
            $offer?->total_gross ?? null,
            $offer?->total_net ?? null,
            $folder->price ?? null,
        ] as $candidatePrice) {
            if ($candidatePrice !== null && $candidatePrice !== '' && is_numeric($candidatePrice)) {
                $price = (float) $candidatePrice;
                break;
            }
        }

        $dealData = [
            'updated_at' => $now,
        ];

        $put = function (string $column, mixed $value) use (&$dealData): void {
            if (Schema::hasColumn('deals', $column)) {
                $dealData[$column] = $value;
            }
        };

        $put('customer_id', (int) $lead->customer_id);
        $put('alternative_id', (int) $lead->alternative_id);
        $put('product_id', (int) $lead->product_id);
        $put('offer_id', $folder->offer_id ?: $offer?->id);
        $put('offer_folder_id', (int) $folder->id);
        $put('employee_id', $employeeId);
        $put('created_by', $employeeId);
        $put('updated_by', $employeeId);
        $put('department_id', $departmentId);
        $put('service', $service);
        $put('service_id', $serviceId);
        $put('price', $price ?? 0);
        $put('total', $price ?? 0);
        $put('total_net', $detail?->total_net ?? $price ?? 0);
        $put('total_gross', $detail?->total_gross ?? $price ?? 0);
        $put('offer_number', $offerNumber);
        $put('offer_no', $offerNumber);
        $put('offer_status', $folder->offer_status ?? null);
        $put('deal_status', $folder->deal_status ?? 'open');
        $put('status', 'active');

        if (Schema::hasColumn('deals', 'deleted_at')) {
            $dealData['deleted_at'] = null;
        }

        $existing = DB::table('deals')
            ->where('customer_id', $lead->customer_id)
            ->where('alternative_id', $lead->alternative_id)
            ->where('product_id', $lead->product_id)
            ->when(Schema::hasColumn('deals', 'offer_folder_id'), function ($query) use ($folder) {
                $query->where(function ($sub) use ($folder) {
                    $sub->where('offer_folder_id', $folder->id)
                        ->orWhereNull('offer_folder_id');
                });
            })
            ->when(Schema::hasColumn('deals', 'deleted_at'), fn($query) => $query->whereNull('deleted_at'))
            ->orderByDesc('id')
            ->first();

        if ($existing) {
            DB::table('deals')->where('id', $existing->id)->update($dealData);
            return;
        }

        $dealData['created_at'] = $now;

        if (Schema::hasColumn('deals', 'order_number') && empty($dealData['order_number']) && class_exists(\App\Models\Deal::class)) {
            try {
                $dealData['order_number'] = \App\Models\Deal::generateOrderNo($lead->customer_id, $departmentId);
            } catch (Throwable $e) {
                Log::warning('Deal order number generation failed during Kanban offer acceptance', [
                    'lead_product_id' => $lead->id,
                    'offer_folder_id' => $folder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        /*
         * Final safety net:
         * Fill any remaining NOT NULL / no-default legacy columns so the insert
         * does not fail with another "Field ... doesn't have a default value".
         */
        $requiredColumns = DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'deals')
            ->where('IS_NULLABLE', 'NO')
            ->whereNull('COLUMN_DEFAULT')
            ->where('EXTRA', 'not like', '%auto_increment%')
            ->pluck('DATA_TYPE', 'COLUMN_NAME')
            ->toArray();

        foreach ($requiredColumns as $column => $dataType) {
            if (array_key_exists($column, $dealData)) {
                continue;
            }

            if (in_array($column, ['created_at', 'updated_at'], true)) {
                $dealData[$column] = $now;
                continue;
            }

            if ($column === 'service') {
                $dealData[$column] = $service;
                continue;
            }

            if ($column === 'status') {
                $dealData[$column] = 'active';
                continue;
            }

            if ($column === 'order_number' && Schema::hasColumn('deals', 'order_number') && class_exists(\App\Models\Deal::class)) {
                try {
                    $dealData[$column] = \App\Models\Deal::generateOrderNo($lead->customer_id, $departmentId);
                    continue;
                } catch (Throwable $e) {
                    //
                }
            }

            $dealData[$column] = match (strtolower((string) $dataType)) {
                'bigint', 'int', 'integer', 'smallint', 'tinyint', 'mediumint' => 0,
                'decimal', 'double', 'float', 'real' => 0,
                'date' => $now->toDateString(),
                'datetime', 'timestamp' => $now,
                'json' => json_encode([]),
                default => '',
            };
        }

        DB::table('deals')->insert($dealData);
    }

    private function broadcastOfferFolderRealtime($folder, string $action, array $extra = []): void
    {
        try {
            if (class_exists(\App\Events\OfferFolderUpdated::class)) {
                broadcast(new \App\Events\OfferFolderUpdated($folder, $action, array_merge([
                    'folder_id' => $folder->id,
                    'offer_id' => $folder->offer_id,
                    'document_status' => $folder->document_status,
                    'offer_status' => $folder->offer_status,
                    'deal_status' => $folder->deal_status,
                ], $extra)))->toOthers();
            }

            if (class_exists(\App\Events\OffersChanged::class)) {
                broadcast(new \App\Events\OffersChanged(array_merge([
                    'type' => 'kanban_offer_consistency_' . $action,
                    'folder_id' => $folder->id,
                    'offer_id' => $folder->offer_id,
                ], $extra)))->toOthers();
            }
        } catch (Throwable $e) {
            Log::warning('Offer folder realtime broadcast failed', [
                'folder_id' => $folder->id ?? null,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function moveToNextProductStage(Request $request, LeadProductList $leadProduct): JsonResponse
    {
        $nextStageId = $this->nextProductStageId($leadProduct);

        if (!$nextStageId) {
            return response()->json([
                'success' => false,
                'message' => 'Keine nächste Produktphase gefunden.',
            ], 422);
        }

        $request->merge([
            'mode' => 'product',
            'product_stage_id' => $nextStageId,
            'move_forward' => false,
        ]);

        return $this->moveStageWorkflow($request, $leadProduct);
    }


    private function nextProductStageId(LeadProductList $lead): ?int
    {
        if (!Schema::hasColumn('lead_product_lists', 'product_stage_id')) {
            return null;
        }

        $stageHasSortOrder = Schema::hasColumn('stages', 'sort_order');

        $currentStage = null;

        if (!empty($lead->product_stage_id)) {
            $currentStage = Stage::query()
                ->where('id', $lead->product_stage_id)
                ->where('product_id', $lead->product_id)
                ->first();
        }

        $baseQuery = Stage::query()
            ->where('product_id', $lead->product_id)
            ->where('status', 'Published');

        if (!$currentStage) {
            if ($stageHasSortOrder) {
                $baseQuery->orderBy('sort_order');
            }

            return $baseQuery->orderBy('id')->value('id');
        }

        $nextQuery = Stage::query()
            ->where('product_id', $lead->product_id)
            ->where('status', 'Published');

        if ($stageHasSortOrder) {
            $currentOrder = (int) ($currentStage->sort_order ?? 0);

            $nextQuery->where(function ($q) use ($currentStage, $currentOrder) {
                $q->where('sort_order', '>', $currentOrder)
                    ->orWhere(function ($qq) use ($currentStage, $currentOrder) {
                        $qq->where('sort_order', $currentOrder)
                            ->where('id', '>', $currentStage->id);
                    });
            })
                ->orderBy('sort_order');
        } else {
            $nextQuery->where('id', '>', $currentStage->id);
        }

        return $nextQuery
            ->orderBy('id')
            ->value('id');
    }


    private function buildTeamStageObjects(
        array $teamIds,
        ?string $stage,
        ?string $oldStage,
        int|string|null $assignedBy,
        Carbon $now,
        array $extra = []
    ): array {
        return collect($teamIds)
            ->map(fn($employeeId) => array_merge([
                'employee_id' => (int) $employeeId,
                'stage' => $stage,
                'old_stage' => $oldStage,
                'assigned_by' => $assignedBy,
                'assigned_at' => $now->toDateTimeString(),
            ], $extra))
            ->values()
            ->all();
    }

    private function mergeTeamHistory(array $existingTeams, array $newTeams): array
    {
        $existingTeams = collect($existingTeams)
            ->filter(fn($row) => is_array($row) && !empty($row['employee_id']))
            ->values();

        $newTeams = collect($newTeams)
            ->filter(fn($row) => is_array($row) && !empty($row['employee_id']))
            ->values();

        return $existingTeams
            ->merge($newTeams)
            ->values()
            ->all();
    }

    private function syncOfferFoldersFromLeadSubStage(LeadProductList $lead, string $mainStageKey, ?LeadStageSubStage $subStage): void
    {
        if (!$subStage || !Schema::hasTable('offer_folders')) {
            return;
        }

        $folders = $this->activeOfferFoldersForLead($lead);

        foreach ($folders as $folder) {
            $documentStatus = $this->folderDocumentStatus($folder);

            if ($mainStageKey === 'offer' && $documentStatus !== 'deal') {
                $folder->offer_status = $subStage->key;
                $folder->save();
                $this->broadcastOfferFolderRealtime($folder, 'offer_sub_stage_synced_from_kanban');
            }

            if ($mainStageKey === 'deal' && $documentStatus === 'deal') {
                $folder->deal_status = $subStage->key;
                $folder->save();
                $this->broadcastOfferFolderRealtime($folder, 'deal_sub_stage_synced_from_kanban');
            }
        }
    }


    public function updateLeadSubStage(Request $request, $leadProduct): JsonResponse
    {
        $data = $request->validate([
            'lead_stage_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $reason = trim(strip_tags((string) ($data['reason'] ?? '')));
        $reason = $reason !== '' ? $reason : null;

        if (!Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Die Spalte lead_stage_sub_stage_id fehlt in lead_product_lists.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            $leadProductId = (int) $leadProduct;

            $lead = LeadProductList::query()
                ->whereKey($leadProductId)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (!$lead) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => "LeadProduct #{$leadProductId} wurde nicht gefunden oder ist gelöscht.",
                ], 404);
            }

            $mainStageKey = $this->normalizeStage((string) ($lead->status ?: $lead->stage ?: 'lead'));

            $mainStage = LeadStage::query()
                ->where('key', $mainStageKey)
                ->first();

            if (!$mainStage) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Hauptphase wurde nicht gefunden.',
                ], 422);
            }

            $subStageId = $data['lead_stage_sub_stage_id'] ?? null;
            $subStage = null;

            if ($subStageId) {
                $subStage = LeadStageSubStage::query()
                    ->where('id', $subStageId)
                    ->where('lead_stage_id', $mainStage->id)
                    ->where('is_active', true)
                    ->first();

                if (!$subStage) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Diese Unterphase gehört nicht zur aktuellen Hauptphase.',
                    ], 422);
                }
            }

            $oldSubStageId = $lead->lead_stage_sub_stage_id;

            $history = is_array($lead->stage_history)
                ? $lead->stage_history
                : (json_decode($lead->stage_history ?? '[]', true) ?: []);

            $history[] = [
                'mode' => 'lead_sub_stage',
                'stage' => $mainStageKey,
                'from_sub_stage_id' => $oldSubStageId,
                'to_sub_stage_id' => $subStageId,
                'to_sub_stage_name' => $subStage?->name,
                'changed_by' => auth()->user()?->name,
                'changed_user_id' => auth()->id(),
                'changed_at' => now()->toDateTimeString(),
                'description' => $reason,
            ];

            $lead->lead_stage_sub_stage_id = $subStageId;
            $lead->stage_history = $history;
            $lead->save();

            if (in_array($mainStageKey, ['offer', 'deal'], true)) {
                $this->syncOfferFoldersFromLeadSubStage($lead, $mainStageKey, $subStage);
            }

            $this->logActivity(
                'updated',
                LeadProductList::class,
                $lead->id,
                $lead->customer_id,
                $lead->alternative_id,
                $lead->product_id,
                [
                    'info' => 'Unterphase geändert: ' . ($subStage?->name ?: 'Keine Unterphase') . ($reason ? ' — ' . $reason : ''),
                    'lead_stage_sub_stage' => [
                        'from' => $oldSubStageId,
                        'to' => $subStageId,
                    ],
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Unterphase wurde gespeichert.',
                'lead' => [
                    'id' => $lead->id,
                    'status' => $lead->status,
                    'stage' => $lead->stage,
                    'lead_stage_sub_stage_id' => $subStageId,
                    'lead_stage_sub_stage_name' => $subStage?->name,
                    'lead_stage_sub_stage_color' => $subStage?->color,
                    'lead_stage_sub_stage_icon' => $subStage?->icon,
                ],
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('updateLeadSubStage failed', [
                'lead_product_id' => $leadProduct->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unterphase konnte nicht gespeichert werden.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Kanban personal filter settings
    |--------------------------------------------------------------------------
    */

    public function kanbanFilterSettingsIndex(Request $request): JsonResponse
    {
        $settings = \App\Models\KanbanFilterSetting::query()
            ->forCurrentUser()
            ->orderByDesc('updated_at')
            ->get(['id', 'name', 'filters', 'is_default', 'created_at', 'updated_at']);

        return response()->json([
            'status' => 'ok',
            'settings' => $settings,
            'default' => $settings->firstWhere('is_default', true),
        ]);
    }

    public function kanbanFilterSettingsStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:120'],
            'filters' => ['required', 'array'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $employeeId = auth()->user()?->name && is_numeric(auth()->user()->name)
            ? (int) auth()->user()->name
            : null;

        $allowedFilterKeys = [
            'customer',
            'stage',
            'lead_age',
            'branch',
            'branch_address',
            'employee',
            'department',
            'product',
            'interest',
            'date_from',
            'date_to',
            'search',
            'sort_by',
            'sort_dir',
            'status_group',
        ];

        $filters = collect($data['filters'] ?? [])
            ->only($allowedFilterKeys)
            ->map(function ($value) {
                if (is_array($value)) {
                    return collect($value)
                        ->filter(fn($item) => !is_null($item) && $item !== '')
                        ->values()
                        ->all();
                }

                return $value;
            })
            ->filter(function ($value) {
                if (is_array($value)) {
                    return count($value) > 0;
                }

                return !is_null($value) && $value !== '';
            })
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Important: create a NEW filter unless an explicit id is sent
        |--------------------------------------------------------------------------
        | The old code used ->when(id)->first(). When id was empty, first()
        | returned the first saved filter of the user and overwrote it.
        | This is why only one filter appeared in "Meine Filter".
        */
        $setting = null;

        if (!empty($data['id'])) {
            $setting = \App\Models\KanbanFilterSetting::query()
                ->forCurrentUser()
                ->where('id', (int) $data['id'])
                ->firstOrFail();
        }

        if (!$setting) {
            $setting = new \App\Models\KanbanFilterSetting();
            $setting->user_id = auth()->id();
            $setting->employee_id = $employeeId;
        }

        $setting->fill([
            'name' => $data['name'],
            'filters' => $filters,
            'is_default' => (bool) ($data['is_default'] ?? false),
        ]);

        $setting->save();

        if ($setting->is_default) {
            \App\Models\KanbanFilterSetting::query()
                ->forCurrentUser()
                ->where('id', '!=', $setting->id)
                ->update(['is_default' => false]);
        }

        $settings = \App\Models\KanbanFilterSetting::query()
            ->forCurrentUser()
            ->orderByDesc('updated_at')
            ->get(['id', 'name', 'filters', 'is_default', 'created_at', 'updated_at']);

        return response()->json([
            'status' => 'ok',
            'message' => 'Filter wurde gespeichert.',
            'setting' => $setting->fresh(),
            'settings' => $settings,
        ]);
    }

    public function kanbanCustomerSearch(Request $request): JsonResponse
    {
        $term = trim((string) $request->input('q', $request->input('term', '')));
        $page = max(1, (int) $request->input('page', 1));
        $limit = 30;
        $offset = ($page - 1) * $limit;

        if (!Schema::hasTable('new_leads')) {
            return response()->json([
                'status' => 'ok',
                'results' => [],
                'pagination' => ['more' => false],
            ]);
        }

        $query = DB::table('new_leads')
            ->whereNull('deleted_at')
            ->select('id', 'name', 'lastname', 'firma', 'email', 'phone', 'postcode', 'city')
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', "%{$term}%")
                        ->orWhere('lastname', 'like', "%{$term}%")
                        ->orWhere('firma', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('postcode', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('id');

        $rows = $query->skip($offset)->limit($limit + 1)->get();
        $more = $rows->count() > $limit;
        $rows = $rows->take($limit);

        return response()->json([
            'status' => 'ok',
            'results' => $rows->map(function ($row) {
                $name = trim(implode(' ', array_filter([
                    $row->firma ?: null,
                    trim(($row->name ?? '') . ' ' . ($row->lastname ?? '')) ?: null,
                ])));

                $details = trim(implode(' · ', array_filter([
                    $row->email ?? null,
                    $row->phone ?? null,
                    trim(($row->postcode ?? '') . ' ' . ($row->city ?? '')) ?: null,
                ])));

                return [
                    'id' => $row->id,
                    'text' => trim(($name ?: ('Kunde #' . $row->id)) . ($details ? ' — ' . $details : '')),
                    'name' => $name,
                    'email' => $row->email,
                    'phone' => $row->phone,
                    'city' => $row->city,
                ];
            })->values(),
            'pagination' => ['more' => $more],
        ]);
    }

    public function kanbanBranchAddresses(Request $request): JsonResponse
    {
        $branchId = (int) $request->query('branch_id', 0);
        $term = trim((string) $request->query('q', $request->query('term', '')));

        if (!Schema::hasTable('branch_addresses')) {
            return response()->json([
                'status' => 'ok',
                'success' => true,
                'results' => [],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Branch address dropdown
        |--------------------------------------------------------------------------
        | Do NOT filter too strictly by status here. Some projects store status as:
        | active, Active, Aktiv, 1, empty or NULL. Only soft-deleted addresses are hidden.
        */
        $items = DB::table('branch_addresses')
            ->whereNull('deleted_at')
            ->when($branchId > 0, fn($q) => $q->where('branch_id', $branchId))
            ->when($term !== '', function ($q) use ($term) {
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', "%{$term}%")
                        ->orWhere('full_address', 'like', "%{$term}%")
                        ->orWhere('street', 'like', "%{$term}%")
                        ->orWhere('postcode', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('telephone', 'like', "%{$term}%");
                });
            })
            ->orderBy('branch_id')
            ->orderBy('name')
            ->orderBy('city')
            ->orderBy('street')
            ->limit(200)
            ->get(['id', 'branch_id', 'name', 'full_address', 'street', 'postcode', 'city', 'phone', 'telephone', 'email']);

        $results = $items->map(function ($item) {
            $fallback = trim(implode(' ', array_filter([
                $item->street ?? null,
                $item->postcode ?? null,
                $item->city ?? null,
            ])));

            $label = trim(($item->name ? $item->name . ' · ' : '') . ($item->full_address ?: $fallback));

            return [
                'id' => (string) $item->id,
                'branch_id' => (string) $item->branch_id,
                'text' => $label ?: ('Adresse #' . $item->id),
                'name' => $item->name,
                'full_address' => $item->full_address,
                'street' => $item->street,
                'postcode' => $item->postcode,
                'city' => $item->city,
                'phone' => $item->phone,
                'telephone' => $item->telephone,
                'email' => $item->email,
            ];
        })->values();

        return response()->json([
            'status' => 'ok',
            'success' => true,
            'results' => $results,
        ]);
    }

    public function kanbanFilterSettingsUpdate(Request $request, \App\Models\KanbanFilterSetting $setting): JsonResponse
    {
        abort_unless((int) $setting->user_id === (int) auth()->id(), 403);

        $request->merge([
            'id' => $setting->id,
        ]);

        return $this->kanbanFilterSettingsStore($request);
    }

    public function kanbanFilterSettingsMakeDefault(Request $request, \App\Models\KanbanFilterSetting $setting): JsonResponse
    {
        abort_unless((int) $setting->user_id === (int) auth()->id(), 403);

        \App\Models\KanbanFilterSetting::query()
            ->forCurrentUser()
            ->update(['is_default' => false]);

        $setting->forceFill([
            'is_default' => true,
        ])->save();

        $settings = \App\Models\KanbanFilterSetting::query()
            ->forCurrentUser()
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->get(['id', 'name', 'filters', 'is_default', 'created_at', 'updated_at']);

        return response()->json([
            'status' => 'ok',
            'success' => true,
            'message' => 'Standardfilter wurde gesetzt.',
            'setting' => $setting->fresh(),
            'settings' => $settings,
        ]);
    }

    public function kanbanFilterSettingsDestroy(Request $request, \App\Models\KanbanFilterSetting $setting): JsonResponse
    {
        abort_unless((int) $setting->user_id === (int) auth()->id(), 403);

        $setting->delete();

        $settings = \App\Models\KanbanFilterSetting::query()
            ->forCurrentUser()
            ->orderByDesc('is_default')
            ->orderByDesc('updated_at')
            ->get(['id', 'name', 'filters', 'is_default', 'created_at', 'updated_at']);

        return response()->json([
            'status' => 'ok',
            'success' => true,
            'message' => 'Filter wurde gelöscht.',
            'settings' => $settings,
        ]);
    }

    public function valueAnalytics(Request $request): JsonResponse
    {
        [$base, $stageNorm, $minExpr, $maxExpr] = $this->valueAnalyticsBaseQuery();

        /*
        |--------------------------------------------------------------------------
        | Reuse your existing Kanban filters
        |--------------------------------------------------------------------------
        | This keeps the value tab synced with:
        | customer, product, employee, department, branch, date, stage,
        | status_group, interest, lead_age, search, etc.
        */
        if (method_exists($this, 'applyCommonFilters')) {
            $this->applyCommonFilters($base, $request, true);
        } else {
            $this->applyValueAnalyticsFallbackFilters($base, $request, $stageNorm);
        }

        /*
        |--------------------------------------------------------------------------
        | Default: exclude closed/system columns
        |--------------------------------------------------------------------------
        | If the user explicitly filters archive/junk/ticket, includeClosed() returns true.
        */
        if (!$this->includeClosed($request)) {
            $base->whereNotIn(DB::raw($stageNorm), ['archive', 'archiv', 'junk', 'ticket']);
        }

        if ($request->filled('sub_stage') || $request->filled('substage') || $request->filled('lead_stage_sub_stage_id')) {
            $subStageId = (int) (
                $request->input('sub_stage')
                ?: $request->input('substage')
                ?: $request->input('lead_stage_sub_stage_id')
            );

            if ($subStageId > 0 && Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
                $base->where('lpl.lead_stage_sub_stage_id', $subStageId);
            }
        }

        $money = static fn($value): float => round((float) $value, 2);

        $valuePayload = static function (float $min, float $max, int $count, int $customerCount = 0, int $objectCount = 0, int $productTypeCount = 0) use ($money): array {
            $avgTotal = ($min + $max) / 2;

            return [
                'product_count' => $count,
                'product_type_count' => $productTypeCount,
                'customer_count' => $customerCount,
                'object_count' => $objectCount,

                'total_min' => $money($min),
                'total_max' => $money($max),
                'total_avg' => $money($avgTotal),

                'avg_min_per_product' => $count > 0 ? $money($min / $count) : 0.0,
                'avg_max_per_product' => $count > 0 ? $money($max / $count) : 0.0,
                'avg_value_per_product' => $count > 0 ? $money($avgTotal / $count) : 0.0,
            ];
        };

        /*
        |--------------------------------------------------------------------------
        | Company / filtered total
        |--------------------------------------------------------------------------
        */
        $totals = (clone $base)
            ->selectRaw('COUNT(*) as product_count')
            ->selectRaw('COUNT(DISTINCT lpl.customer_id) as customer_count')
            ->selectRaw("COUNT(DISTINCT CONCAT(lpl.customer_id, ':', COALESCE(lpl.alternative_id, 0))) as object_count")
            ->selectRaw('COUNT(DISTINCT lpl.product_id) as product_type_count')
            ->selectRaw("SUM({$minExpr}) as total_min")
            ->selectRaw("SUM({$maxExpr}) as total_max")
            ->first();

        $summary = $valuePayload(
            (float) ($totals->total_min ?? 0),
            (float) ($totals->total_max ?? 0),
            (int) ($totals->product_count ?? 0),
            (int) ($totals->customer_count ?? 0),
            (int) ($totals->object_count ?? 0),
            (int) ($totals->product_type_count ?? 0)
        );

        /*
        |--------------------------------------------------------------------------
        | Stage + sub-stage summary
        |--------------------------------------------------------------------------
        */
        $leadStages = $this->leadStagesForUi();

        $stagePayload = [];

        foreach ($leadStages as $stage) {
            $stagePayload[$stage->key] = [
                'id' => $stage->id,
                'key' => $stage->key,
                'name' => $stage->name,
                'color' => $stage->color ?: '#74b2d4',
                'icon' => $stage->icon ?: 'circle',
                'sort_order' => (int) $stage->sort_order,

                'product_count' => 0,
                'product_type_count' => 0,
                'customer_count' => 0,
                'object_count' => 0,
                'total_min' => 0.0,
                'total_max' => 0.0,
                'total_avg' => 0.0,
                'avg_value_per_product' => 0.0,

                'sub_stages' => collect($stage->subStages ?? [])
                    ->map(fn($subStage) => [
                        'id' => $subStage->id,
                        'key' => $subStage->key,
                        'name' => $subStage->name,
                        'color' => $subStage->color ?: '#93c21c',
                        'icon' => $subStage->icon ?: 'git-branch',
                        'sort_order' => (int) $subStage->sort_order,

                        'product_count' => 0,
                        'customer_count' => 0,
                        'object_count' => 0,
                        'total_min' => 0.0,
                        'total_max' => 0.0,
                        'total_avg' => 0.0,
                        'avg_value_per_product' => 0.0,
                    ])
                    ->values()
                    ->all(),
            ];
        }

        $stageRows = (clone $base)
            ->selectRaw("{$stageNorm} as stage_key")
            ->selectRaw('COALESCE(ls.id, 0) as stage_id')
            ->selectRaw("COALESCE(ls.name, {$stageNorm}) as stage_name")
            ->selectRaw("COALESCE(ls.color, '#74b2d4') as stage_color")
            ->selectRaw("COALESCE(ls.icon, 'circle') as stage_icon")
            ->selectRaw('COALESCE(ls.sort_order, 9999) as stage_sort_order')
            ->selectRaw('COUNT(*) as product_count')
            ->selectRaw('COUNT(DISTINCT lpl.product_id) as product_type_count')
            ->selectRaw('COUNT(DISTINCT lpl.customer_id) as customer_count')
            ->selectRaw("COUNT(DISTINCT CONCAT(lpl.customer_id, ':', COALESCE(lpl.alternative_id, 0))) as object_count")
            ->selectRaw("SUM({$minExpr}) as total_min")
            ->selectRaw("SUM({$maxExpr}) as total_max")
            ->groupByRaw("{$stageNorm}, ls.id, ls.name, ls.color, ls.icon, ls.sort_order")
            ->orderBy('stage_sort_order')
            ->get();

        foreach ($stageRows as $row) {
            $key = strtolower((string) $row->stage_key);

            if (!isset($stagePayload[$key])) {
                $stagePayload[$key] = [
                    'id' => (int) $row->stage_id,
                    'key' => $key,
                    'name' => $row->stage_name ?: ucfirst($key),
                    'color' => $row->stage_color ?: '#74b2d4',
                    'icon' => $row->stage_icon ?: 'circle',
                    'sort_order' => (int) $row->stage_sort_order,
                    'sub_stages' => [],
                ];
            }

            $stagePayload[$key] = array_merge(
                $stagePayload[$key],
                $valuePayload(
                    (float) $row->total_min,
                    (float) $row->total_max,
                    (int) $row->product_count,
                    (int) $row->customer_count,
                    (int) $row->object_count,
                    (int) $row->product_type_count
                )
            );
        }

        $subStageRows = (clone $base)
            ->selectRaw("{$stageNorm} as stage_key")
            ->selectRaw('COALESCE(lsss.id, 0) as sub_stage_id')
            ->selectRaw("COALESCE(lsss.name, 'Ohne Unterphase') as sub_stage_name")
            ->selectRaw("COALESCE(lsss.color, '#cbd5e1') as sub_stage_color")
            ->selectRaw("COALESCE(lsss.icon, 'minus') as sub_stage_icon")
            ->selectRaw('COALESCE(lsss.sort_order, 9999) as sub_stage_sort_order')
            ->selectRaw('COUNT(*) as product_count')
            ->selectRaw('COUNT(DISTINCT lpl.customer_id) as customer_count')
            ->selectRaw("COUNT(DISTINCT CONCAT(lpl.customer_id, ':', COALESCE(lpl.alternative_id, 0))) as object_count")
            ->selectRaw("SUM({$minExpr}) as total_min")
            ->selectRaw("SUM({$maxExpr}) as total_max")
            ->groupByRaw("{$stageNorm}, lsss.id, lsss.name, lsss.color, lsss.icon, lsss.sort_order")
            ->orderBy('sub_stage_sort_order')
            ->get();

        foreach ($subStageRows as $row) {
            $stageKey = strtolower((string) $row->stage_key);

            if (!isset($stagePayload[$stageKey])) {
                continue;
            }

            $subStageId = (int) $row->sub_stage_id;

            $subStageData = array_merge([
                'id' => $subStageId,
                'key' => $subStageId > 0 ? (string) $subStageId : 'none',
                'name' => $row->sub_stage_name ?: 'Ohne Unterphase',
                'color' => $row->sub_stage_color ?: '#cbd5e1',
                'icon' => $row->sub_stage_icon ?: 'minus',
                'sort_order' => (int) $row->sub_stage_sort_order,
            ], $valuePayload(
                        (float) $row->total_min,
                        (float) $row->total_max,
                        (int) $row->product_count,
                        (int) $row->customer_count,
                        (int) $row->object_count
                    ));

            $found = false;

            foreach ($stagePayload[$stageKey]['sub_stages'] as $index => $existing) {
                if ((int) ($existing['id'] ?? 0) === $subStageId) {
                    $stagePayload[$stageKey]['sub_stages'][$index] = array_merge($existing, $subStageData);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $stagePayload[$stageKey]['sub_stages'][] = $subStageData;
            }
        }

        $stages = collect($stagePayload)
            ->sortBy('sort_order')
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Customer -> object/home -> products
        |--------------------------------------------------------------------------
        */
        $detailRows = (clone $base)
            ->select([
                'new_leads.id as customer_id',
                'new_leads.customer_no',
                'new_leads.firma',
                'new_leads.name as customer_name',
                'new_leads.lastname as customer_lastname',

                'lpl.alternative_id',
                'alt.object_name',
                'alt.tile_name',
                'alt.full_address as object_full_address',
                'alt.street as object_street',
                'alt.postcode as object_postcode',
                'alt.city as object_city',

                'lpl.product_id',
                'article_groups.article_group as product_name',
                'article_groups.initial as product_initial',

                'ls.name as stage_name',
                'ls.color as stage_color',
                'lsss.id as sub_stage_id',
                'lsss.name as sub_stage_name',
                'lsss.color as sub_stage_color',
            ])
            ->selectRaw("{$stageNorm} as stage_key")
            ->selectRaw('COUNT(*) as product_count')
            ->selectRaw("AVG({$minExpr}) as unit_min_value")
            ->selectRaw("AVG({$maxExpr}) as unit_max_value")
            ->selectRaw("SUM({$minExpr}) as sum_min_value")
            ->selectRaw("SUM({$maxExpr}) as sum_max_value")
            ->groupBy(
                'new_leads.id',
                'new_leads.customer_no',
                'new_leads.firma',
                'new_leads.name',
                'new_leads.lastname',
                'lpl.alternative_id',
                'alt.object_name',
                'alt.tile_name',
                'alt.full_address',
                'alt.street',
                'alt.postcode',
                'alt.city',
                'lpl.product_id',
                'article_groups.article_group',
                'article_groups.initial',
                'ls.name',
                'ls.color',
                'lsss.id',
                'lsss.name',
                'lsss.color'
            )
            ->groupByRaw($stageNorm)
            ->orderByRaw('SUM(' . $maxExpr . ') DESC')
            ->get();

        $customers = [];

        foreach ($detailRows as $row) {
            $customerId = (int) $row->customer_id;
            $objectId = (int) ($row->alternative_id ?: 0);
            $stageKey = strtolower((string) ($row->stage_key ?: 'lead'));
            $subStageId = (int) ($row->sub_stage_id ?: 0);

            $count = (int) $row->product_count;
            $sumMin = (float) $row->sum_min_value;
            $sumMax = (float) $row->sum_max_value;

            if (!isset($customers[$customerId])) {
                $customers[$customerId] = [
                    'id' => $customerId,
                    'customer_no' => $row->customer_no,
                    'name' => $this->analyticsCustomerName($row),

                    'product_count' => 0,
                    'product_type_count' => 0,
                    'object_count' => 0,

                    'total_min' => 0.0,
                    'total_max' => 0.0,
                    'total_avg' => 0.0,
                    'avg_value_per_product' => 0.0,

                    'stage_summaries' => [],
                    'objects' => [],
                    '_product_ids' => [],
                ];
            }

            if (!isset($customers[$customerId]['objects'][$objectId])) {
                $customers[$customerId]['objects'][$objectId] = [
                    'id' => $objectId,
                    'name' => $this->analyticsObjectName($row),
                    'product_count' => 0,
                    'total_min' => 0.0,
                    'total_max' => 0.0,
                    'total_avg' => 0.0,
                    'products' => [],
                ];
            }

            $productId = (int) ($row->product_id ?: 0);

            $productData = [
                'id' => $productId,
                'name' => $row->product_name ?: ('Produkt #' . $productId),
                'initial' => $row->product_initial,
                'stage_key' => $stageKey,
                'stage_name' => $row->stage_name ?: ucfirst($stageKey),
                'stage_color' => $row->stage_color ?: '#74b2d4',
                'sub_stage_id' => $subStageId,
                'sub_stage_name' => $row->sub_stage_name ?: 'Ohne Unterphase',
                'sub_stage_color' => $row->sub_stage_color ?: '#cbd5e1',
                'count' => $count,
                'unit_min' => $money($row->unit_min_value),
                'unit_max' => $money($row->unit_max_value),
                'unit_avg' => $money(((float) $row->unit_min_value + (float) $row->unit_max_value) / 2),
                'sum_min' => $money($sumMin),
                'sum_max' => $money($sumMax),
                'sum_avg' => $money(($sumMin + $sumMax) / 2),
            ];

            $customers[$customerId]['objects'][$objectId]['products'][] = $productData;

            $customers[$customerId]['objects'][$objectId]['product_count'] += $count;
            $customers[$customerId]['objects'][$objectId]['total_min'] += $sumMin;
            $customers[$customerId]['objects'][$objectId]['total_max'] += $sumMax;

            $customers[$customerId]['product_count'] += $count;
            $customers[$customerId]['total_min'] += $sumMin;
            $customers[$customerId]['total_max'] += $sumMax;
            $customers[$customerId]['_product_ids'][$productId] = true;

            if (!isset($customers[$customerId]['stage_summaries'][$stageKey])) {
                $customers[$customerId]['stage_summaries'][$stageKey] = [
                    'key' => $stageKey,
                    'name' => $row->stage_name ?: ucfirst($stageKey),
                    'color' => $row->stage_color ?: '#74b2d4',
                    'product_count' => 0,
                    'total_avg' => 0.0,
                    'sub_stages' => [],
                ];
            }

            $customers[$customerId]['stage_summaries'][$stageKey]['product_count'] += $count;
            $customers[$customerId]['stage_summaries'][$stageKey]['total_avg'] += (($sumMin + $sumMax) / 2);

            $subKey = $subStageId ?: 0;

            if (!isset($customers[$customerId]['stage_summaries'][$stageKey]['sub_stages'][$subKey])) {
                $customers[$customerId]['stage_summaries'][$stageKey]['sub_stages'][$subKey] = [
                    'id' => $subStageId,
                    'name' => $row->sub_stage_name ?: 'Ohne Unterphase',
                    'color' => $row->sub_stage_color ?: '#cbd5e1',
                    'product_count' => 0,
                ];
            }

            $customers[$customerId]['stage_summaries'][$stageKey]['sub_stages'][$subKey]['product_count'] += $count;
        }

        foreach ($customers as &$customer) {
            $customer['product_type_count'] = count($customer['_product_ids']);
            unset($customer['_product_ids']);

            $customer['object_count'] = count($customer['objects']);
            $customer['total_min'] = $money($customer['total_min']);
            $customer['total_max'] = $money($customer['total_max']);
            $customer['total_avg'] = $money(($customer['total_min'] + $customer['total_max']) / 2);
            $customer['avg_value_per_product'] = $customer['product_count'] > 0
                ? $money($customer['total_avg'] / $customer['product_count'])
                : 0.0;

            foreach ($customer['objects'] as &$object) {
                $object['total_min'] = $money($object['total_min']);
                $object['total_max'] = $money($object['total_max']);
                $object['total_avg'] = $money(($object['total_min'] + $object['total_max']) / 2);

                usort($object['products'], fn($a, $b) => $b['sum_avg'] <=> $a['sum_avg']);
            }
            unset($object);

            foreach ($customer['stage_summaries'] as &$stageSummary) {
                $stageSummary['total_avg'] = $money($stageSummary['total_avg']);
                $stageSummary['sub_stages'] = array_values($stageSummary['sub_stages']);
            }
            unset($stageSummary);

            $customer['objects'] = array_values($customer['objects']);
            $customer['stage_summaries'] = array_values($customer['stage_summaries']);
        }
        unset($customer);

        $customers = array_values($customers);
        usort($customers, fn($a, $b) => $b['total_avg'] <=> $a['total_avg']);

        $limit = min(max((int) $request->input('limit', 250), 25), 1000);

        return response()->json([
            'success' => true,
            'generated_at' => now()->toDateTimeString(),
            'filters' => $request->query(),
            'summary' => $summary,
            'stages' => $stages,
            'customers' => array_slice($customers, 0, $limit),
            'total_customers_loaded' => count($customers),
            'limit' => $limit,
        ]);
    }

    private function valueAnalyticsBaseQuery(): array
    {
        $stageNorm = "CASE
        WHEN lpl.status IS NULL
            OR lpl.status = ''
            OR LOWER(lpl.status) = 'open'
        THEN 'lead'
        ELSE LOWER(lpl.status)
    END";

        $minExpr = "CAST(COALESCE(NULLIF(article_groups.min_value, ''), 0) AS DECIMAL(15,2))";
        $maxExpr = "CAST(COALESCE(NULLIF(article_groups.max_value, ''), 0) AS DECIMAL(15,2))";

        $query = DB::table('lead_product_lists as lpl')
            ->join('new_leads', 'lpl.customer_id', '=', 'new_leads.id')
            ->leftJoin('lead_alternative_adds as alt', function ($join) {
                $join->on('alt.id', '=', 'lpl.alternative_id');

                if (Schema::hasColumn('lead_alternative_adds', 'deleted_at')) {
                    $join->whereNull('alt.deleted_at');
                }
            })
            ->leftJoin('article_groups', 'article_groups.id', '=', 'lpl.product_id')
            ->leftJoin('branches as br', 'br.id', '=', 'new_leads.branch')
            ->leftJoin('lead_stages as ls', function ($join) use ($stageNorm) {
                $join->whereRaw("LOWER(ls.key) = {$stageNorm}");

                if (Schema::hasColumn('lead_stages', 'deleted_at')) {
                    $join->whereNull('ls.deleted_at');
                }
            })
            ->leftJoin('lead_stage_sub_stages as lsss', function ($join) {
                $join->on('lsss.id', '=', 'lpl.lead_stage_sub_stage_id');

                if (Schema::hasColumn('lead_stage_sub_stages', 'deleted_at')) {
                    $join->whereNull('lsss.deleted_at');
                }
            })
            ->whereNull('lpl.deleted_at')
            ->whereNull('new_leads.deleted_at');

        if (Schema::hasColumn('article_groups', 'deleted_at')) {
            $query->whereNull('article_groups.deleted_at');
        }

        return [$query, $stageNorm, $minExpr, $maxExpr];
    }

    private function analyticsCustomerName(object $row): string
    {
        if (!empty($row->firma)) {
            return trim((string) $row->firma);
        }

        $full = trim(($row->customer_name ?? '') . ' ' . ($row->customer_lastname ?? ''));

        if ($full !== '') {
            return $full;
        }

        if (!empty($row->customer_no)) {
            return '#' . $row->customer_no;
        }

        return 'Kunde #' . ($row->customer_id ?? '');
    }

    private function analyticsObjectName(object $row): string
    {
        foreach (['object_name', 'tile_name', 'object_full_address'] as $field) {
            if (!empty($row->{$field})) {
                return trim((string) $row->{$field});
            }
        }

        $address = trim(implode(' ', array_filter([
            $row->object_street ?? null,
            $row->object_postcode ?? null,
            $row->object_city ?? null,
        ])));

        return $address !== '' ? $address : 'Hauptadresse';
    }

    private function applyValueAnalyticsFallbackFilters(
        \Illuminate\Database\Query\Builder $query,
        Request $request,
        string $stageNorm
    ): void {
        if ($request->filled('customer')) {
            $customerInput = $request->input('customer');
            $customerIds = is_array($customerInput) ? $customerInput : [$customerInput];

            $customerIds = collect($customerIds)
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->values()
                ->all();

            if (!empty($customerIds)) {
                $query->whereIn('lpl.customer_id', $customerIds);
            }
        }

        if ($request->filled('product')) {
            $query->where('lpl.product_id', (int) $request->input('product'));
        }

        if ($request->filled('employee')) {
            $query->where('lpl.employee_id', (int) $request->input('employee'));
        }

        if ($request->filled('department')) {
            $query->where('lpl.department_id', (int) $request->input('department'));
        }

        if ($request->filled('branch')) {
            $query->where('new_leads.branch', (int) $request->input('branch'));
        }

        if ($request->filled('interest')) {
            $query->where('lpl.interest', $request->input('interest'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('lpl.created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('lpl.created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('stage')) {
            $stage = strtolower(trim((string) $request->input('stage')));

            $aliases = [
                'open' => 'lead',
                'new' => 'lead',
                'neue' => 'lead',
                'angebot' => 'offer',
                'nachfassen' => 'follow_up',
                'followup' => 'follow_up',
                'annehmen' => 'accepted',
                'angenommen' => 'accepted',
                'auftrag' => 'deal',
                'montage' => 'project',
                'abschluss' => 'completed',
                'archiv' => 'archive',
                'rejeck' => 'junk',
                'reject' => 'junk',
            ];

            $query->where(DB::raw($stageNorm), $aliases[$stage] ?? $stage);
        }
    }

}