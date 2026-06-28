<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\LeadProductList;
use App\Models\MainAppointment;
use App\Models\NewLeads;
use App\Models\Offer;
use App\Models\PersonalTask;
use App\Models\Problem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardCompanyController extends Controller
{
    private function employeeId(): int
    {
        return (int) auth()->user()->name;
    }

    public function overview(Request $request): JsonResponse
    {
        $employeeId = $this->employeeId();

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->startOfYear();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        $mainDepartment = $this->mainDepartment($employeeId);

        $employeeIds = Employee::query()
            ->pluck('id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Company leads
        |--------------------------------------------------------------------------
        | Company dashboard "Leads" means ALL records from new_leads.
        | Do NOT count lead_product_lists here, because that only counts customers
        | that already have products.
        |--------------------------------------------------------------------------
        */
        $allLeadIds = NewLeads::query()
            ->pluck('id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $periodLeadIds = NewLeads::query()
            ->whereBetween('created_at', [$from, $to])
            ->pluck('id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $customerIdsForChanges = collect($allLeadIds)
            ->merge(Offer::query()->pluck('customer_id'))
            ->merge(Deal::query()->pluck('customer_id'))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $invoiceBase = Invoice::query();

        $paidInvoiceBase = Invoice::query()
            ->where('status', 'paid');

        $kpis = [
            'employees' => count($employeeIds),

            'departments' => Department::query()->count(),

            // All company leads/customers from new_leads.
            'leads' => count($allLeadIds),

            // Period-based new_leads.
            'new_leads' => count($periodLeadIds),

            'offers' => Offer::query()
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'deals' => Deal::query()
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'tickets' => Problem::query()
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'appointments' => MainAppointment::query()
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'tasks' => PersonalTask::query()
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'invoices' => (clone $invoiceBase)
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'paid_invoices' => (clone $paidInvoiceBase)
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'invoice_total' => round((float) (clone $paidInvoiceBase)
                ->whereBetween('created_at', [$from, $to])
                ->sum('total_amount'), 2),

            'invoice_open' => round((float) (clone $invoiceBase)
                ->whereBetween('created_at', [$from, $to])
                ->whereNotIn('status', ['paid', 'cancelled'])
                ->selectRaw('SUM(GREATEST(total_amount - paid_amount, 0)) as open_sum')
                ->value('open_sum'), 2),
        ];

        return response()->json([
            'success' => true,

            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],

            'main_department' => $mainDepartment,

            'kpis' => $kpis,

            'charts' => [
                'monthly_revenue' => $this->monthlyRevenue($from, $to),
                'items_by_type' => $this->itemsByType($from, $to),
                'invoice_status' => $this->invoiceStatus($from, $to),
                'department_performance' => $this->departmentPerformance($from, $to),
            ],

            'recent' => [
                'departments' => $this->departmentTable($from, $to),
                'changes' => $this->recentChanges($customerIdsForChanges, $from, $to),
            ],
        ]);
    }

    private function mainDepartment(int $employeeId): ?array
    {
        $departmentPosition = DB::table('department_positions')
            ->where('employee_id', $employeeId)
            ->orderByDesc('main')
            ->orderByDesc('percent')
            ->orderBy('department_id')
            ->first();

        if (!$departmentPosition) {
            return null;
        }

        $department = Department::query()
            ->with([
                'branch:id,branch,slug,initial,color,second_color,logo_url',
                'parent:id,department_name',
            ])
            ->find($departmentPosition->department_id);

        if (!$department) {
            return null;
        }

        return [
            'id' => $department->id,
            'name' => $department->department_name,
            'branch' => $this->branchName($department->branch),
            'parent' => $department->parent?->department_name,
            'percent' => (int) ($departmentPosition->percent ?? 0),
            'office_percent' => (int) ($departmentPosition->office_percent ?? 0),
            'montage_percent' => (int) ($departmentPosition->montage_percent ?? 0),
            'working_hours' => (float) ($departmentPosition->working_hours ?? 0),
            'main' => (bool) ($departmentPosition->main ?? false),
        ];
    }

    private function monthlyRevenue(Carbon $from, Carbon $to): array
    {
        $start = $from->copy()->startOfMonth();
        $end = $to->copy()->endOfMonth();

        $rows = Invoice::query()
            ->where('status', 'paid')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_key, SUM(total_amount) as total')
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $labels = [];
        $values = [];

        $cursor = $start->copy();

        while ($cursor <= $end) {
            $key = $cursor->format('Y-m');

            $labels[] = $cursor->translatedFormat('M Y');
            $values[] = round((float) ($rows[$key]->total ?? 0), 2);

            $cursor->addMonth();
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'total' => round((float) array_sum($values), 2),
        ];
    }

    private function itemsByType(Carbon $from, Carbon $to): array
    {
        return [
            'labels' => [
                'Leads',
                'Angebote',
                'Aufträge',
                'Rechnungen',
                'Tickets',
                'Termine',
                'Aufgaben',
            ],

            'values' => [
                // Correct company chart value: new_leads in selected period.
                NewLeads::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                Offer::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                Deal::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                Invoice::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                Problem::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                MainAppointment::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                PersonalTask::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),
            ],
        ];
    }

    private function invoiceStatus(Carbon $from, Carbon $to): array
    {
        $rows = Invoice::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('COALESCE(status, "Unbekannt") as status_label, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('status_label')
            ->orderBy('status_label')
            ->get();

        return [
            'labels' => $rows->pluck('status_label')->values(),
            'counts' => $rows->pluck('count')->map(fn($v) => (int) $v)->values(),
            'totals' => $rows->pluck('total')->map(fn($v) => round((float) $v, 2))->values(),
        ];
    }

    private function departmentPerformance(Carbon $from, Carbon $to): array
    {
        $departments = Department::query()
            ->select('id', 'department_name')
            ->orderBy('department_name')
            ->get();

        return [
            'labels' => $departments->pluck('department_name')->values(),

            /*
            |--------------------------------------------------------------------------
            | Department leads
            |--------------------------------------------------------------------------
            | Department leads are NOT all new_leads.
            | They are customers connected through:
            | department_id -> product_id / ArticleGroup -> customer_id.
            |--------------------------------------------------------------------------
            */
            'leads' => $departments->map(
                fn($department) => $this->departmentLeadCountByProducts($department->id, $from, $to)
            )->values(),

            'offers' => $departments->map(
                fn($department) => Offer::query()
                    ->where('department_id', $department->id)
                    ->whereBetween('created_at', [$from, $to])
                    ->count()
            )->values(),

            'deals' => $departments->map(
                fn($department) => Deal::query()
                    ->where('department_id', $department->id)
                    ->whereBetween('created_at', [$from, $to])
                    ->count()
            )->values(),
        ];
    }

    private function departmentTable(Carbon $from, Carbon $to): array
    {
        return Department::query()
            ->withCount([
                'employees as team_count',
            ])
            ->orderBy('department_name')
            ->get()
            ->map(function ($department) use ($from, $to) {
                $productIds = $this->departmentProductIds($department->id);

                $allCustomerIds = $this->departmentCustomerIdsByProducts(
                    departmentId: $department->id
                );

                $periodCustomerIds = $this->departmentCustomerIdsByProducts(
                    departmentId: $department->id,
                    from: $from,
                    to: $to
                );

                return [
                    'id' => $department->id,
                    'name' => $department->department_name,
                    'team_count' => (int) $department->team_count,

                    // All-time total for this department through products/article groups.
                    'leads' => $allCustomerIds->count(),

                    // Period total for this department through products/article groups.
                    'new_leads' => $periodCustomerIds->count(),

                    // Debug/helper value.
                    'product_groups' => $productIds->count(),

                    'offers' => Offer::query()
                        ->where('department_id', $department->id)
                        ->whereBetween('created_at', [$from, $to])
                        ->count(),

                    'deals' => Deal::query()
                        ->where('department_id', $department->id)
                        ->whereBetween('created_at', [$from, $to])
                        ->count(),
                ];
            })
            ->values()
            ->all();
    }

    private function departmentProductIds(int $departmentId)
    {
        return LeadProductList::query()
            ->where('department_id', $departmentId)
            ->whereNotNull('product_id')
            ->distinct()
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();
    }

    private function departmentCustomerIdsByProducts(
        int $departmentId,
        ?Carbon $from = null,
        ?Carbon $to = null
    ) {
        $productIds = $this->departmentProductIds($departmentId);

        if ($productIds->isEmpty()) {
            return collect();
        }

        $query = LeadProductList::query()
            ->whereIn('product_id', $productIds)
            ->whereNotNull('customer_id');

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        return $query
            ->distinct()
            ->pluck('customer_id')
            ->filter()
            ->unique()
            ->values();
    }

    private function departmentLeadCountByProducts(
        int $departmentId,
        ?Carbon $from = null,
        ?Carbon $to = null
    ): int {
        return $this->departmentCustomerIdsByProducts($departmentId, $from, $to)->count();
    }

    private function recentChanges(array $customerIds, Carbon $from, Carbon $to): array
    {
        return DB::table('lead_activity_logs')
            ->whereBetween('created_at', [$from, $to])
            ->when(!empty($customerIds), function ($q) use ($customerIds) {
                $q->whereIn('new_leads_id', $customerIds);
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn($log) => [
                'id' => $log->id,
                'event_type' => $log->event_type,
                'model_type' => class_basename($log->model_type),
                'model_id' => $log->model_id,
                'user_name' => $log->user_name,
                'date' => Carbon::parse($log->created_at)->format('d.m.Y H:i'),
            ])
            ->values()
            ->all();
    }

    private function branchName($branch): ?string
    {
        if (!$branch) {
            return null;
        }

        return $branch->branch
            ?? $branch->initial
            ?? $branch->slug
            ?? ('Filiale #' . $branch->id);
    }
}