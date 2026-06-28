<?php

namespace App\Http\Controllers\Customer\Kanban;

use App\Http\Controllers\Controller;
use App\Models\AppointmentReport;
use App\Models\CustomerNote;
use App\Models\CustomerReport;
use App\Models\MainAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class KanbanCustomerPanelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
    |--------------------------------------------------------------------------
    | Sidebar counts
    |--------------------------------------------------------------------------
    */

    public function counts(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'lead_product_list_id' => ['nullable', 'integer'],
        ]);

        $customerId = (int) $data['customer_id'];
        $alternativeId = $this->nullableInt($data['alternative_id'] ?? null);
        $productId = $this->nullableInt($data['product_id'] ?? null);
        $leadProductListId = $this->nullableInt($data['lead_product_list_id'] ?? null);

        $notes = $this->notesCountQuery(
            $customerId,
            $alternativeId,
            $productId,
            $leadProductListId
        )->count();

        $customerReports = CustomerReport::query()
            ->where('customer_id', $customerId)
            ->when($alternativeId, fn($q) => $q->where('alternative_id', $alternativeId))
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->count();

        /*
         * Termin Bericht is customer-wide in Kanban.
         * Do NOT filter appointments by alternative/product/lead_product_list here,
         * because old Termine were often saved only with customer_id.
         */
        $appointmentIds = $this->appointmentsQuery(
            $customerId,
            null,
            null,
            null
        )->pluck('id');

        $appointmentReportsByAppointment = $appointmentIds->isEmpty()
            ? collect()
            : AppointmentReport::query()
                ->select('appointment_id', DB::raw('COUNT(*) as total'))
                ->whereIn('appointment_id', $appointmentIds)
                ->groupBy('appointment_id')
                ->pluck('total', 'appointment_id');

        $appointmentTotal = $appointmentIds->count();
        $terminDone = $appointmentReportsByAppointment
            ->filter(fn($count) => (int) $count > 0)
            ->count();

        $terminOpen = max(0, $appointmentTotal - $terminDone);
        $terminReports = (int) $appointmentReportsByAppointment->sum();

        return response()->json([
            'status' => 'ok',
            'counts' => [
                'notes' => $notes,
                'customer_reports' => $customerReports,
                'appointments' => $appointmentTotal,
                'termin_reports' => $terminReports,
                'termin_done' => $terminDone,
                'termin_open' => $terminOpen,
                'total' => $notes + $customerReports + $terminReports,
            ],
        ]);
    }

    protected function notesCountQuery(
        int $customerId,
        ?int $alternativeId,
        ?int $productId,
        ?int $leadProductListId
    ) {
        $query = CustomerNote::query()
            ->where('customer_id', $customerId)
            ->whereNull('parent_id')
            ->when($alternativeId, fn($q) => $q->where('alternative_id', $alternativeId));

        if ($leadProductListId || $productId) {
            $query->where(function ($q) use ($leadProductListId, $productId) {
                if ($leadProductListId) {
                    $q->where('lead_product_list_id', $leadProductListId);
                }

                if ($productId) {
                    $method = $leadProductListId ? 'orWhere' : 'where';

                    $q->{$method}(function ($qq) use ($productId) {
                        $qq->where('product_id', $productId)
                            ->where(function ($idScope) {
                                $idScope->whereNull('lead_product_list_id')
                                    ->orWhere('lead_product_list_id', 0);
                            });
                    });
                }
            });

            return $query;
        }

        return $query
            ->whereNull('product_id')
            ->whereNull('lead_product_list_id')
            ->where(function ($q) {
                $q->whereNull('type')
                    ->orWhere('type', 'general')
                    ->orWhere('type', 'customer');
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Customer reports
    |--------------------------------------------------------------------------
    */

    public function customerReportsIndex(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'lead_product_list_id' => ['nullable', 'integer'],
        ]);

        $customerId = (int) $data['customer_id'];
        $alternativeId = $this->nullableInt($data['alternative_id'] ?? null);
        $productId = $this->nullableInt($data['product_id'] ?? null);

        $reports = CustomerReport::query()
            ->with(['reporter:id,name,lastname,image'])
            ->where('customer_id', $customerId)
            ->when($alternativeId, fn($q) => $q->where('alternative_id', $alternativeId))
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->latest('created_at')
            ->get();

        return response()->json([
            'status' => 'ok',
            'count' => $reports->count(),
            'html' => $this->renderCustomerReportsHtml($reports),
        ]);
    }

    public function customerReportsStore(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'lead_product_list_id' => ['nullable', 'integer'],
            'stage' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'report_date' => ['nullable', 'date'],
            'report' => ['required', 'string'],
            'plain_text' => ['nullable', 'string'],
        ]);

        $customerId = (int) $data['customer_id'];
        $alternativeId = $this->nullableInt($data['alternative_id'] ?? null);
        $productId = $this->nullableInt($data['product_id'] ?? null);

        $employeeId = $this->currentEmployeeId();
        $cleanHtml = $this->cleanRichHtml($data['report']);

        $report = CustomerReport::create([
            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product_id' => $productId,
            'report_by' => $employeeId,
            'stage' => $data['stage'] ?: 'Kunden Bericht',
            'report' => $cleanHtml,
            'report_details' => [
                'title' => $data['title'] ?? null,
                'report_date' => $data['report_date'] ?? now()->toDateString(),
                'plain_text' => Str::limit($data['plain_text'] ?? strip_tags($cleanHtml), 2000, ''),
                'source' => 'kanban_sidebar_modal',
                'created_by' => $employeeId,
                'created_at' => now()->toDateTimeString(),
            ],
        ]);

        $report->load('reporter:id,name,lastname,image');

        return response()->json([
            'status' => 'ok',
            'message' => 'Kunden Bericht wurde gespeichert.',
            'count' => CustomerReport::query()
                ->where('customer_id', $customerId)
                ->when($alternativeId, fn($q) => $q->where('alternative_id', $alternativeId))
                ->when($productId, fn($q) => $q->where('product_id', $productId))
                ->count(),
            'report' => $report,
            'html' => $this->renderCustomerReportCard($report),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Appointments + appointment reports
    |--------------------------------------------------------------------------
    */

    public function appointmentsIndex(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'lead_product_list_id' => ['nullable', 'integer'],
        ]);

        $customerId = (int) $data['customer_id'];
        $alternativeId = $this->nullableInt($data['alternative_id'] ?? null);
        $productId = $this->nullableInt($data['product_id'] ?? null);
        $leadProductListId = $this->nullableInt($data['lead_product_list_id'] ?? null);

        /*
         * Termin Bericht must load all Termine for the selected customer.
         * Keep reports/context safe by checking appointment.customer_id only.
         */
        $appointments = $this->appointmentsQuery(
            $customerId,
            null,
            null,
            null
        )
            ->with([
                'createdBy:id,name,lastname,image',
                'employees:id,name,lastname,image',
                'reports' => function ($q) {
                    $q->with(['reporter:id,name,lastname,image', 'author:id,name,lastname,image'])
                        ->latest('report_date')
                        ->latest('created_at');
                },
            ])
            ->get();

        return response()->json([
            'status' => 'ok',
            'count' => $appointments->count(),
            'appointments_count' => $appointments->count(),
            'reports_count' => $appointments->sum(fn($appointment) => $appointment->reports->count()),
            'html' => $this->renderAppointmentsHtml($appointments),
        ]);
    }

    public function appointmentReportsIndex(Request $request, MainAppointment $appointment)
    {
        if (!$this->appointmentIsAllowedForRequest($request, $appointment)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dieser Termin gehört nicht zu diesem Kunden/Kontext.',
                'html' => '<div class="kb-empty-state small">Dieser Termin gehört nicht zu diesem Kunden/Kontext.</div>',
            ], 403);
        }

        $reports = AppointmentReport::query()
            ->with(['reporter:id,name,lastname,image', 'author:id,name,lastname,image'])
            ->where('appointment_id', $appointment->id)
            ->latest('report_date')
            ->latest('created_at')
            ->get();

        return response()->json([
            'status' => 'ok',
            'count' => $reports->count(),
            'html' => $this->renderAppointmentReportsHtml($reports),
        ]);
    }

    public function appointmentReportsStore(Request $request, MainAppointment $appointment)
    {
        if (!$this->appointmentIsAllowedForRequest($request, $appointment)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dieser Termin gehört nicht zu diesem Kunden/Kontext.',
            ], 403);
        }

        $data = $request->validate([
            'customer_id' => ['nullable', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'lead_product_list_id' => ['nullable', 'integer'],
            'type' => ['nullable', 'string', 'max:80'],
            'title' => ['nullable', 'string', 'max:255'],
            'report' => ['required', 'string'],
            'plain_text' => ['nullable', 'string'],
            'next_step' => ['nullable', 'string', 'max:2000'],
            'due_date' => ['nullable', 'date'],
            'report_date' => ['nullable', 'date'],
        ]);

        $employeeId = $this->currentEmployeeId();
        $cleanHtml = $this->cleanRichHtml($data['report']);

        $report = AppointmentReport::create([
            'employee_id' => $employeeId,
            'appointment_id' => $appointment->id,
            'task_id' => $appointment->task_id,
            'type' => $data['type'] ?: 'Termin Bericht',
            'report' => $cleanHtml,
            'report_date' => $data['report_date'] ?? now()->toDateString(),
            'next_step' => $data['next_step'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'report_by' => $employeeId,
            'comments' => ['items' => []],
            'likes' => 0,
            'dislikes' => 0,
            'meta' => [
                'items' => [
                    [
                        'action' => 'created_from_kanban_sidebar_modal',
                        'employee_id' => $employeeId,
                        'customer_id' => $appointment->customer_id,
                        'appointment_id' => $appointment->id,
                        'title' => $data['title'] ?? null,
                        'plain_text' => Str::limit($data['plain_text'] ?? strip_tags($cleanHtml), 2000, ''),
                        'created_at' => now()->toDateTimeString(),
                    ],
                ],
                'likes' => [],
                'dislikes' => [],
            ],
        ]);

        $appointment->forceFill([
            'is_report' => true,
            'report' => $report->report,
            'report_date' => $report->report_date,
            'report_by' => $employeeId,
        ])->save();

        $report->load(['reporter:id,name,lastname,image', 'author:id,name,lastname,image']);

        return response()->json([
            'status' => 'ok',
            'message' => 'Termin Bericht wurde gespeichert.',
            'report' => $report,
            'html' => $this->renderAppointmentReportCard($report),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Queries
    |--------------------------------------------------------------------------
    */

    protected function appointmentsQuery(
        int $customerId,
        ?int $alternativeId = null,
        ?int $productId = null,
        ?int $leadProductListId = null
    ) {
        $query = MainAppointment::query();

        /*
        |--------------------------------------------------------------------------
        | CRITICAL FIX
        |--------------------------------------------------------------------------
        | Never use:
        | customer_id OR contact_id OR other_id
        |
        | In Kanban customer panel this leaks appointments/reports from another
        | customer because contact_id/other_id can point to other modules.
        */
        $query->where('customer_id', $customerId);

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
        |--------------------------------------------------------------------------
        | products JSON fallback
        |--------------------------------------------------------------------------
        | Your DB stores products sometimes as:
        | [17]
        |
        | and sometimes as:
        | [{"uid":"WÄRMEPUMPE_6655","product_id":16,"alternative_id":6655,"customer_id":"6505"}]
        |
        | This fallback supports both formats without allowing another customer.
        */
        if (Schema::hasColumn('main_appointments', 'products') && ($productId || $alternativeId)) {
            $query->where(function ($scope) use ($productId, $alternativeId, $customerId) {
                /*
                 * Keep appointments that have no products value.
                 * Some old appointments only have customer_id.
                 */
                $scope->whereNull('products')
                    ->orWhere('products', '')
                    ->orWhere('products', '[]');

                if ($productId) {
                    $scope->orWhere(function ($productScope) use ($productId, $alternativeId, $customerId) {
                        /*
                         * Format: [17]
                         */
                        $productScope
                            ->whereJsonContains('products', (int) $productId)
                            ->orWhereJsonContains('products', (string) $productId);

                        /*
                         * Format: [{"product_id":17}]
                         * LIKE fallback is used because many existing rows are mixed JSON shapes.
                         */
                        $productScope->orWhere(function ($jsonObjectScope) use ($productId, $alternativeId, $customerId) {
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

        return $query
            ->orderByRaw('COALESCE(start_date, created_at) DESC')
            ->orderByDesc('start_time')
            ->orderByDesc('created_at');
    }

    protected function appointmentIsAllowedForRequest(Request $request, MainAppointment $appointment): bool
    {
        $customerId = $this->nullableInt($request->input('customer_id'));
        $alternativeId = $this->nullableInt($request->input('alternative_id'));
        $productId = $this->nullableInt($request->input('product_id'));
        $leadProductListId = $this->nullableInt($request->input('lead_product_list_id'));

        /*
         * If frontend does not send panel context, keep route usable.
         * But if it sends context, verify the appointment belongs to it.
         */
        if (!$customerId && !$alternativeId && !$productId && !$leadProductListId) {
            return true;
        }

        if ($customerId && (int) $appointment->customer_id !== $customerId) {
            return false;
        }

        if (
            $leadProductListId &&
            Schema::hasColumn('main_appointments', 'lead_product_list_id') &&
            (int) ($appointment->lead_product_list_id ?? 0) !== $leadProductListId
        ) {
            return false;
        }

        if (
            $alternativeId &&
            Schema::hasColumn('main_appointments', 'alternative_id') &&
            (int) ($appointment->alternative_id ?? 0) !== $alternativeId
        ) {
            return false;
        }

        if (
            $productId &&
            Schema::hasColumn('main_appointments', 'product_id') &&
            (int) ($appointment->product_id ?? 0) !== $productId
        ) {
            return false;
        }

        return $this->appointmentProductsMatchContext($appointment, $customerId, $alternativeId, $productId);
    }

    protected function appointmentProductsMatchContext(
        MainAppointment $appointment,
        ?int $customerId,
        ?int $alternativeId,
        ?int $productId
    ): bool {
        if (!$productId && !$alternativeId) {
            return true;
        }

        $products = $appointment->products;

        if (empty($products)) {
            return true;
        }

        if (is_string($products)) {
            $decoded = json_decode($products, true);
            $products = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        if (!is_array($products)) {
            return true;
        }

        foreach ($products as $item) {
            if (is_numeric($item)) {
                if ($productId && (int) $item === $productId) {
                    return true;
                }

                continue;
            }

            if (!is_array($item)) {
                continue;
            }

            $itemProductId = $this->nullableInt($item['product_id'] ?? null);
            $itemAlternativeId = $this->nullableInt($item['alternative_id'] ?? null);
            $itemCustomerId = $this->nullableInt($item['customer_id'] ?? null);

            if ($customerId && $itemCustomerId && $itemCustomerId !== $customerId) {
                continue;
            }

            if ($productId && $itemProductId && $itemProductId !== $productId) {
                continue;
            }

            if ($alternativeId && $itemAlternativeId && $itemAlternativeId !== $alternativeId) {
                continue;
            }

            return true;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | HTML renderers
    |--------------------------------------------------------------------------
    */

    protected function renderCustomerReportsHtml($reports): string
    {
        $html = '<div class="kb-panel-tools kb-panel-tools--sticky">
                    <input type="search" class="form-control kb-panel-search" placeholder="Kunden Bericht suchen..." data-kb-panel-search>
                    <button type="button" class="btn btn-sm btn-primary kb-new-customer-report">
                        <i class="feather icon-plus"></i> Neuer Bericht
                    </button>
                 </div>';

        $html .= '<div class="kb-report-list">';

        if ($reports->isEmpty()) {
            $html .= '<div class="kb-empty-state">Noch kein Kunden Bericht vorhanden. Klicke auf „Neuer Bericht“, um einen Bericht zu schreiben.</div>';
        } else {
            foreach ($reports as $report) {
                $html .= $this->renderCustomerReportCard($report);
            }
        }

        $html .= '</div>';

        return $html;
    }

    protected function renderCustomerReportCard(CustomerReport $report): string
    {
        $reporter = $report->reporter
            ? trim(($report->reporter->name ?? '') . ' ' . ($report->reporter->lastname ?? ''))
            : 'Unbekannt';

        $title = data_get($report->report_details, 'title') ?: ($report->stage ?: 'Kunden Bericht');

        $date = data_get($report->report_details, 'report_date')
            ? Carbon::parse(data_get($report->report_details, 'report_date'))->format('d.m.Y')
            : optional($report->created_at)->format('d.m.Y H:i');

        $body = $this->renderRichHtml($report->report);

        return '<article class="kb-report-card cr-card"
                    data-report-id="' . e($report->id) . '"
                    data-customer-id="' . e($report->customer_id) . '"
                    data-alternative-id="' . e($report->alternative_id) . '"
                    data-product-id="' . e($report->product_id) . '"
                    data-search-text="' . e($this->searchText($title . ' ' . $reporter . ' ' . $report->stage . ' ' . $report->report)) . '">
                    <div class="kb-report-card-head">
                        <div>
                            <strong>' . e($title) . '</strong>
                            <small>' . e($reporter) . ' • ' . e($date) . '</small>
                        </div>
                        <span class="kb-pill">' . e($report->stage ?: 'Report') . '</span>
                    </div>
                    <div class="kb-rich-content">' . $body . '</div>
                </article>';
    }

    protected function renderAppointmentsHtml($appointments): string
    {
        $totalAppointments = $appointments->count();
        $doneAppointments = $appointments
            ->filter(fn($appointment) => $appointment->reports && $appointment->reports->count() > 0)
            ->count();

        $openAppointments = max(0, $totalAppointments - $doneAppointments);
        $totalReports = $appointments->sum(fn($appointment) => $appointment->reports ? $appointment->reports->count() : 0);

        $html = '<div class="kb-panel-tools kb-panel-tools--sticky kb-panel-tools--bericht">
                    <input type="search" class="form-control kb-panel-search" placeholder="Termin, Datum oder Termin Bericht suchen..." data-kb-panel-search>
                 </div>';

        $html .= '<div class="kb-termin-analytics">
                    <div class="kb-termin-stat">
                        <span>Termine</span>
                        <strong>' . (int) $totalAppointments . '</strong>
                    </div>
                    <div class="kb-termin-stat is-done">
                        <span>Bericht erledigt</span>
                        <strong>' . (int) $doneAppointments . '</strong>
                    </div>
                    <div class="kb-termin-stat is-open">
                        <span>Bericht offen</span>
                        <strong>' . (int) $openAppointments . '</strong>
                    </div>
                    <div class="kb-termin-stat is-total">
                        <span>Berichte gesamt</span>
                        <strong>' . (int) $totalReports . '</strong>
                    </div>
                 </div>';

        if ($appointments->isEmpty()) {
            return $html . '<div class="kb-empty-state">Für diesen Kunden/Kontext wurden keine Termine gefunden.</div>';
        }

        $html .= '<div class="kb-appointment-list">';

        foreach ($appointments as $appointment) {
            $html .= $this->renderAppointmentGroup($appointment);
        }

        $html .= '</div>';

        return $html;
    }

    protected function renderAppointmentGroup(MainAppointment $appointment): string
    {
        $title = $appointment->name ?: $appointment->appointment_type ?: 'Termin';
        $date = $this->appointmentDateText($appointment);

        $employeeNames = $appointment->employees
            ->map(fn($e) => trim(($e->name ?? '') . ' ' . ($e->lastname ?? '')))
            ->filter()
            ->implode(', ');

        $reportCount = $appointment->reports ? $appointment->reports->count() : 0;
        $statusClass = $reportCount > 0 ? 'is-done' : 'is-open';
        $statusLabel = $reportCount > 0 ? 'Bericht erledigt' : 'Bericht offen';

        $searchText = $this->searchText(
            $title . ' ' .
            $date . ' ' .
            $employeeNames . ' ' .
            $appointment->note . ' ' .
            $appointment->report
        );

        return '<details class="kb-appointment-group ' . e($statusClass) . '"
                    open
                    data-appointment-id="' . e($appointment->id) . '"
                    data-customer-id="' . e($appointment->customer_id) . '"
                    data-appointment-title="' . e($title) . '"
                    data-appointment-date="' . e($date) . '"
                    data-search-text="' . e($searchText) . '">
                    <summary>
                        <span>
                            <strong>' . e($title) . '</strong>
                            <small>' . e($date) . ($employeeNames ? ' • ' . e($employeeNames) : '') . '</small>
                        </span>
                        <span class="kb-pill ' . e($statusClass) . '">' . e($statusLabel) . ' · ' . (int) $reportCount . '</span>
                    </summary>
                    <div class="kb-appointment-body">
                        ' . ($appointment->note ? '<div class="kb-appointment-note">' . $this->renderRichHtml($appointment->note) . '</div>' : '') . '
                        <div class="kb-appointment-actions">
                            <a class="btn btn-sm btn-outline-secondary kb-ap-profile-btn"
                               href="' . url('customer/appointments/' . $appointment->id) . '"
                               target="_blank"
                               rel="noopener">
                                Terminprofil öffnen
                            </a>
                            <button type="button"
                                    class="btn btn-sm btn-primary kb-open-appointment-report-form"
                                    data-appointment-id="' . e($appointment->id) . '"
                                    data-appointment-title="' . e($title) . '"
                                    data-appointment-date="' . e($date) . '">
                                Termin Bericht schreiben
                            </button>
                        </div>
                        <div class="kb-appointment-reports">'
            . $this->renderAppointmentReportsHtml($appointment->reports ?: collect()) .
            '</div>
                    </div>
                </details>';
    }

    protected function renderAppointmentReportsHtml($reports): string
    {
        if (!$reports || $reports->isEmpty()) {
            return '<div class="kb-empty-state kb-empty-state--sm">Für diesen Termin ist noch kein Bericht geschrieben.</div>';
        }

        return $reports->map(fn($report) => $this->renderAppointmentReportCard($report))->implode('');
    }

    protected function renderAppointmentReportCard(AppointmentReport $report): string
    {
        $author = $report->reporter ?: $report->author;

        $authorName = $author
            ? trim(($author->name ?? '') . ' ' . ($author->lastname ?? ''))
            : 'Unbekannt';

        $date = $report->report_date
            ? Carbon::parse($report->report_date)->format('d.m.Y')
            : optional($report->created_at)->format('d.m.Y H:i');

        $title = data_get($report->meta, 'items.0.title') ?: ($report->type ?: 'Termin Bericht');

        return '<article class="kb-report-card ap-report-card"
                    data-report-id="' . e($report->id) . '"
                    data-appointment-id="' . e($report->appointment_id) . '"
                    data-search-text="' . e($this->searchText($title . ' ' . $authorName . ' ' . $report->report . ' ' . $report->next_step)) . '">
                    <div class="kb-report-card-head">
                        <div>
                            <strong>' . e($title) . '</strong>
                            <small>' . e($authorName) . ' • ' . e($date) . '</small>
                        </div>
                        <span class="kb-pill">' . e($report->next_step ? 'Nächster Schritt' : 'Report') . '</span>
                    </div>
                    <div class="kb-rich-content">' . $this->renderRichHtml($report->report) . '</div>
                    ' . ($report->next_step ? '<div class="kb-next-step"><strong>Nächster Schritt:</strong> ' . e($report->next_step) . '</div>' : '') . '
                </article>';
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function appointmentDateText(MainAppointment $appointment): string
    {
        $date = $appointment->start_date
            ? Carbon::parse($appointment->start_date)->format('d.m.Y')
            : optional($appointment->created_at)->format('d.m.Y');

        $time = trim(($appointment->start_time ?? '') . ($appointment->end_time ? ' - ' . $appointment->end_time : ''));

        return trim($date . ($time ? ' • ' . $time : ''));
    }

    protected function currentEmployeeId(): ?int
    {
        return Auth::check() ? (int) Auth::user()->name : null;
    }

    protected function nullableInt($value): ?int
    {
        if ($value === null || $value === '' || $value === '0' || $value === 0 || $value === 'null' || $value === 'undefined') {
            return null;
        }

        return (int) $value;
    }

    protected function searchText(?string $html): string
    {
        return Str::lower(strip_tags(html_entity_decode((string) $html, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    protected function renderRichHtml(?string $html): string
    {
        $clean = $this->cleanRichHtml($html);

        if ($clean === '') {
            return '';
        }

        if (!preg_match('/<[^>]+>/', $clean)) {
            return nl2br(e($clean));
        }

        return $clean;
    }

    protected function cleanRichHtml(?string $html): string
    {
        $html = (string) $html;

        for ($i = 0; $i < 3; $i++) {
            $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if ($decoded === $html) {
                break;
            }

            $html = $decoded;
        }

        $html = preg_replace('#<(script|style|iframe|object|embed)\b[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('/\son\w+="[^"]*"/i', '', $html);
        $html = preg_replace("/\son\w+='[^']*'/i", '', $html);
        $html = preg_replace('/\s(href|src)\s*=\s*"(javascript:[^"]*)"/i', ' $1="#"', $html);
        $html = preg_replace("/\s(href|src)\s*=\s*'(javascript:[^']*)'/i", " $1='#'", $html);

        $allowed = '<p><br><strong><b><em><i><u><s><ul><ol><li><a><span><blockquote><pre><code><h1><h2><h3><h4><h5><h6>';
        $html = strip_tags($html, $allowed);

        return trim($html);
    }
}