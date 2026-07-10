<?php

namespace App\Http\Controllers;

use App\Models\AssetInstallment;
use App\Models\Branch;
use App\Models\BranchExpense;
use App\Models\BranchExpenseOtherCost;
use App\Models\BranchInsurance;
use App\Models\BranchRent;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class BranchExpenseController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR Finance: Rollen-Gate (permission:Finance)
        $this->middleware('permission:Finance,update')->only(['update']);
        $this->middleware('permission:Finance,delete')->only(['destroy']);
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('admin.expense.expense_type.branch_expense.index', [
            'branches' => Branch::where('status', 'Published')->orderBy('branch')->get(),
            'expenseStatuses' => BranchExpense::statuses(),
            'selectedBranchId' => $request->query('branch_id'),
            'selectedYear' => $request->query('year', now()->year),
        ]);
    }

    public function profile(Request $request, BranchExpense $branchExpense, ?string $tab = 'overview')
    {
        $tab = $tab ?: 'overview';
        $allowedTabs = ['overview', 'rents', 'insurances', 'other-costs', 'linked'];

        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'overview';
        }

        $branchExpense->load('branch');

        return view('admin.expense.expense_type.branch_expense.profile', [
            'expense' => $branchExpense,
            'activeTab' => $tab,
            'tabs' => $this->profileTabs($branchExpense),
            'branches' => Branch::where('status', 'Published')->orderBy('branch')->get(),
            'expenseStatuses' => BranchExpense::statuses(),
            'rentStatuses' => BranchRent::statuses(),
            'insuranceStatuses' => BranchInsurance::statuses(),
            'otherCostStatuses' => BranchExpenseOtherCost::statuses(),
            'otherCategories' => BranchExpenseOtherCost::categories(),
            'kpis' => $this->profileKpis($branchExpense),
            'alerts' => $this->dueAlerts($branchExpense),
            'linked' => $this->linkedCosts($branchExpense),
        ]);
    }

    public function legacyDetails(int $id, string $branch, int $year): RedirectResponse
    {
        return redirect()->route('branch.expense.profile', [$id, 'overview']);
    }

    public function data(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 12), 6), 100);

        $expenses = $this->query($request)
            ->with('branch:id,branch,street,postcode,city,color,initial')
            ->withCount(['rents', 'insurances', 'otherCosts'])
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'items' => $expenses->getCollection()->map(fn(BranchExpense $expense) => $this->resource($expense))->values(),
            'pagination' => [
                'current_page' => $expenses->currentPage(),
                'last_page' => $expenses->lastPage(),
                'per_page' => $expenses->perPage(),
                'total' => $expenses->total(),
                'from' => $expenses->firstItem(),
                'to' => $expenses->lastItem(),
            ],
        ]);
    }

    public function analytics(Request $request): JsonResponse
    {
        $query = $this->query($request);
        $ids = (clone $query)->pluck('id');
        $branchIds = (clone $query)->pluck('branch_id')->filter()->unique()->values();
        $years = (clone $query)->pluck('year')->filter()->unique()->values();

        $rentTotal = BranchRent::whereIn('expense_details_id', $ids)->sum('total');
        $insuranceTotal = BranchInsurance::whereIn('branch_expense_id', $ids)->sum('monthly_payable');
        $otherTotal = BranchExpenseOtherCost::whereIn('branch_expense_id', $ids)->sum('amount');
        $employeeTotal = $this->employeeSalaryTotal($branchIds);
        $installmentTotal = $this->installmentTotal($branchIds);

        $dueSoon = $this->dueCount($ids, false);
        $overdue = $this->dueCount($ids, true);

        return response()->json([
            'total_records' => (clone $query)->count(),
            'total_cost' => (float) ($rentTotal + $insuranceTotal + $otherTotal + $employeeTotal + $installmentTotal),
            'rent_total' => (float) $rentTotal,
            'insurance_total' => (float) $insuranceTotal,
            'other_total' => (float) $otherTotal,
            'employee_total' => (float) $employeeTotal,
            'installment_total' => (float) $installmentTotal,
            'due_soon' => $dueSoon,
            'overdue' => $overdue,
        ]);
    }

    public function profileData(BranchExpense $branchExpense): JsonResponse
    {
        return response()->json([
            'expense' => $this->resource($branchExpense->load('branch')),
            'kpis' => $this->profileKpis($branchExpense),
            'alerts' => $this->dueAlerts($branchExpense),
            'linked' => $this->linkedCosts($branchExpense),
        ]);
    }

    public function show(BranchExpense $branchExpense): JsonResponse
    {
        return response()->json([
            'item' => $this->resource($branchExpense->load('branch')),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $payload = $this->validatePayload($request);

        $exists = BranchExpense::where('branch_id', $payload['branch_id'])
            ->where('year', $payload['year'])
            ->exists();

        if ($exists) {
            return $this->fail($request, 'Diese Branch hat für dieses Jahr bereits einen Expense-Datensatz.', 422);
        }

        $expense = BranchExpense::create($payload);

        return $this->success($request, 'Branch Expense wurde gespeichert.', $expense);
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $expense = BranchExpense::findOrFail($request->input('id'));
        $payload = $this->validatePayload($request, $expense->id);

        $exists = BranchExpense::where('branch_id', $payload['branch_id'])
            ->where('year', $payload['year'])
            ->where('id', '!=', $expense->id)
            ->exists();

        if ($exists) {
            return $this->fail($request, 'Diese Branch hat für dieses Jahr bereits einen Expense-Datensatz.', 422);
        }

        $expense->update($payload);

        return $this->success($request, 'Branch Expense wurde aktualisiert.', $expense);
    }

    public function destroy(Request $request, BranchExpense $branchExpense): JsonResponse|RedirectResponse
    {
        $branchExpense->delete();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => 'Branch Expense wurde gelöscht.']);
        }

        return redirect()->route('branch.expense')->with('delete_msg', 'Branch Expense wurde gelöscht.');
    }

    private function query(Request $request)
    {
        return BranchExpense::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->query('search'));
                $query->where(function ($sub) use ($search) {
                    $sub->where('year', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('branch', fn($branch) => $branch->where('branch', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('branch_id'), fn($query) => $query->where('branch_id', $request->query('branch_id')))
            ->when($request->filled('year'), fn($query) => $query->where('year', $request->query('year')))
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->query('status')));
    }

    private function validatePayload(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:' . (now()->year + 5)],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'status' => ['required', Rule::in(array_keys(BranchExpense::statuses()))],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function success(Request $request, string $message, BranchExpense $expense): JsonResponse|RedirectResponse
    {
        $expense->load('branch');

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'item' => $this->resource($expense),
                'profile_url' => route('branch.expense.profile', [$expense->id, 'overview']),
            ]);
        }

        return redirect()->route('branch.expense.profile', [$expense->id, 'overview'])->with('save_msg', $message);
    }

    private function fail(Request $request, string $message, int $status): JsonResponse|RedirectResponse
    {
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return back()->withInput()->with('delete_msg', $message);
    }

    private function resource(BranchExpense $expense): array
    {
        $rentTotal = (float) $expense->rents()->sum('total');
        $insuranceTotal = (float) $expense->insurances()->sum('monthly_payable');
        $otherTotal = (float) $expense->otherCosts()->sum('amount');
        $linked = $this->linkedCosts($expense, true);
        $total = $rentTotal + $insuranceTotal + $otherTotal + ($linked['employee_total'] ?? 0) + ($linked['installment_total'] ?? 0);

        return [
            'id' => $expense->id,
            'branch_id' => $expense->branch_id,
            'branch' => $expense->branch?->branch,
            'branch_initial' => $expense->branch?->initial ?: mb_substr((string) $expense->branch?->branch, 0, 2),
            'branch_street' => $expense->branch?->street,
            'branch_postcode' => $expense->branch?->postcode,
            'branch_city' => $expense->branch?->city,
            'year' => $expense->year,
            'period_start' => optional($expense->period_start)->format('Y-m-d'),
            'period_end' => optional($expense->period_end)->format('Y-m-d'),
            'total' => (float) $total,
            'rent_total' => $rentTotal,
            'insurance_total' => $insuranceTotal,
            'other_total' => $otherTotal,
            'employee_total' => (float) ($linked['employee_total'] ?? 0),
            'installment_total' => (float) ($linked['installment_total'] ?? 0),
            'rents_count' => (int) ($expense->rents_count ?? $expense->rents()->count()),
            'insurances_count' => (int) ($expense->insurances_count ?? $expense->insurances()->count()),
            'other_costs_count' => (int) ($expense->other_costs_count ?? $expense->otherCosts()->count()),
            'status' => $expense->status,
            'status_label' => $expense->status_label,
            'notes' => $expense->notes,
            'profile_url' => route('branch.expense.profile', [$expense->id, 'overview']),
        ];
    }

    private function profileTabs(BranchExpense $expense): array
    {
        return [
            'overview' => ['label' => 'Übersicht', 'icon' => 'layout-dashboard', 'url' => route('branch.expense.profile', [$expense->id, 'overview'])],
            'rents' => ['label' => 'Miete / Haus', 'icon' => 'home', 'url' => route('branch.expense.profile', [$expense->id, 'rents'])],
            'insurances' => ['label' => 'Versicherung', 'icon' => 'shield-check', 'url' => route('branch.expense.profile', [$expense->id, 'insurances'])],
            'other-costs' => ['label' => 'Sonstige Kosten', 'icon' => 'receipt', 'url' => route('branch.expense.profile', [$expense->id, 'other-costs'])],
            'linked' => ['label' => 'Mitarbeiter / Raten', 'icon' => 'link', 'url' => route('branch.expense.profile', [$expense->id, 'linked'])],
        ];
    }

    private function profileKpis(BranchExpense $expense): array
    {
        $rentTotal = (float) $expense->rents()->sum('total');
        $insuranceTotal = (float) $expense->insurances()->sum('monthly_payable');
        $otherTotal = (float) $expense->otherCosts()->sum('amount');
        $linked = $this->linkedCosts($expense, true);
        $total = $rentTotal + $insuranceTotal + $otherTotal + ($linked['employee_total'] ?? 0) + ($linked['installment_total'] ?? 0);

        return [
            'total' => (float) $total,
            'rent_total' => $rentTotal,
            'insurance_total' => $insuranceTotal,
            'other_total' => $otherTotal,
            'employee_total' => (float) ($linked['employee_total'] ?? 0),
            'installment_total' => (float) ($linked['installment_total'] ?? 0),
            'due_soon' => count(array_filter($this->dueAlerts($expense), fn($a) => $a['status'] === 'due')),
            'overdue' => count(array_filter($this->dueAlerts($expense), fn($a) => $a['status'] === 'overdue')),
        ];
    }

    private function dueAlerts(BranchExpense $expense): array
    {
        $today = now()->startOfDay();
        $limit = now()->addDays(30)->endOfDay();
        $alerts = [];

        foreach ($expense->rents()->whereNotNull('next_due_date')->whereDate('next_due_date', '<=', $limit)->get() as $rent) {
            $alerts[] = [
                'type' => 'rent',
                'title' => 'Miete: ' . $rent->object_name,
                'date' => optional($rent->next_due_date)->format('Y-m-d'),
                'amount' => (float) $rent->total,
                'status' => $rent->next_due_date && $rent->next_due_date->lt($today) ? 'overdue' : 'due',
            ];
        }

        foreach ($expense->insurances()->whereNotNull('next_due_date')->whereDate('next_due_date', '<=', $limit)->get() as $insurance) {
            $alerts[] = [
                'type' => 'insurance',
                'title' => 'Versicherung: ' . $insurance->insurance_for,
                'date' => optional($insurance->next_due_date)->format('Y-m-d'),
                'amount' => (float) $insurance->monthly_payable,
                'status' => $insurance->next_due_date && $insurance->next_due_date->lt($today) ? 'overdue' : 'due',
            ];
        }

        foreach ($expense->otherCosts()->whereNotNull('due_date')->whereDate('due_date', '<=', $limit)->get() as $cost) {
            $alerts[] = [
                'type' => 'other',
                'title' => 'Kosten: ' . $cost->title,
                'date' => optional($cost->due_date)->format('Y-m-d'),
                'amount' => (float) $cost->amount,
                'status' => $cost->due_date && $cost->due_date->lt($today) ? 'overdue' : 'due',
            ];
        }

        return collect($alerts)->sortBy('date')->values()->all();
    }

    private function dueCount($expenseIds, bool $overdue): int
    {
        $operator = $overdue ? '<' : '>=';
        $upper = now()->addDays(30)->toDateString();
        $today = now()->toDateString();

        $rent = BranchRent::whereIn('expense_details_id', $expenseIds)
            ->whereNotNull('next_due_date')
            ->when($overdue, fn($q) => $q->whereDate('next_due_date', '<', $today), fn($q) => $q->whereDate('next_due_date', '>=', $today)->whereDate('next_due_date', '<=', $upper))
            ->count();

        $insurance = BranchInsurance::whereIn('branch_expense_id', $expenseIds)
            ->whereNotNull('next_due_date')
            ->when($overdue, fn($q) => $q->whereDate('next_due_date', '<', $today), fn($q) => $q->whereDate('next_due_date', '>=', $today)->whereDate('next_due_date', '<=', $upper))
            ->count();

        $other = BranchExpenseOtherCost::whereIn('branch_expense_id', $expenseIds)
            ->whereNotNull('due_date')
            ->when($overdue, fn($q) => $q->whereDate('due_date', '<', $today), fn($q) => $q->whereDate('due_date', '>=', $today)->whereDate('due_date', '<=', $upper))
            ->count();

        return $rent + $insurance + $other;
    }

    private function linkedCosts(BranchExpense $expense, bool $summaryOnly = false): array
    {
        $branchId = $expense->branch_id;

        $employees = collect();
        if (Schema::hasTable('department_positions') && Schema::hasTable('departments') && Schema::hasTable('employees')) {
            $employees = DB::table('department_positions')
                ->join('departments', 'departments.id', '=', 'department_positions.department_id')
                ->join('employees', 'employees.id', '=', 'department_positions.employee_id')
                ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
                ->leftJoin('salaries', 'salaries.emp_id', '=', 'employees.id')
                ->select('employees.id', 'employees.name', 'employees.lastname', 'departments.department_name', 'positions.position', DB::raw('COALESCE(salaries.total_monthly_salary, 0) as salary'))
                ->where('employees.status', 'Active')
                ->when($branchId && Schema::hasColumn('departments', 'branch_id'), fn($q) => $q->where('departments.branch_id', $branchId))
                ->get();
        }

        $installments = collect();
        if (Schema::hasTable('asset_installments')) {
            $installments = DB::table('asset_installments')
                ->select('id', 'installment_id', 'type', 'asset_id', 'branch_id', 'due_date', 'total')
                ->when($branchId && Schema::hasColumn('asset_installments', 'branch_id'), fn($q) => $q->where('branch_id', $branchId))
                ->orderByDesc('due_date')
                ->limit($summaryOnly ? 0 : 100)
                ->get();
        }

        $assets = collect();
        if (!$summaryOnly && Schema::hasTable('assets')) {
            $assets = DB::table('assets')
                ->select('id', 'item', 'model', 'purchase_price as amount', 'status')
                ->when($branchId && Schema::hasColumn('assets', 'branch_id'), fn($q) => $q->where('branch_id', $branchId))
                ->limit(100)
                ->get();
        }

        $machines = collect();
        if (!$summaryOnly && Schema::hasTable('machines')) {
            $machines = DB::table('machines')
                ->select('id', 'name', 'model', 'purchase_price as amount', 'status')
                ->when($branchId && Schema::hasColumn('machines', 'branch_id'), fn($q) => $q->where('branch_id', $branchId))
                ->limit(100)
                ->get();
        }

        $installmentTotal = 0;
        if (Schema::hasTable('asset_installments')) {
            $installmentTotal = DB::table('asset_installments')
                ->when($branchId && Schema::hasColumn('asset_installments', 'branch_id'), fn($q) => $q->where('branch_id', $branchId))
                ->sum('total');
        }

        return [
            'employees' => $summaryOnly ? [] : $employees,
            'employee_total' => (float) $employees->sum('salary'),
            'installments' => $summaryOnly ? [] : $installments,
            'installment_total' => (float) $installmentTotal,
            'assets' => $summaryOnly ? [] : $assets,
            'machines' => $summaryOnly ? [] : $machines,
        ];
    }

    private function employeeSalaryTotal($branchIds): float
    {
        if (!Schema::hasTable('department_positions') || !Schema::hasTable('departments') || !Schema::hasTable('employees')) {
            return 0;
        }

        return (float) DB::table('department_positions')
            ->join('departments', 'departments.id', '=', 'department_positions.department_id')
            ->join('employees', 'employees.id', '=', 'department_positions.employee_id')
            ->leftJoin('salaries', 'salaries.emp_id', '=', 'employees.id')
            ->where('employees.status', 'Active')
            ->when($branchIds->isNotEmpty() && Schema::hasColumn('departments', 'branch_id'), fn($q) => $q->whereIn('departments.branch_id', $branchIds))
            ->sum(DB::raw('COALESCE(salaries.total_monthly_salary, 0)'));
    }

    private function installmentTotal($branchIds): float
    {
        if (!Schema::hasTable('asset_installments')) {
            return 0;
        }

        return (float) DB::table('asset_installments')
            ->when($branchIds->isNotEmpty() && Schema::hasColumn('asset_installments', 'branch_id'), fn($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('total');
    }
}
