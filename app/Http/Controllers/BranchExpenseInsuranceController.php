<?php

namespace App\Http\Controllers;

use App\Models\BranchExpense;
use App\Models\BranchInsurance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchExpenseInsuranceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, BranchExpense $branchExpense): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 10), 5), 100);

        $items = $branchExpense->insurances()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->query('search'));
                $query->where(function ($sub) use ($search) {
                    $sub->where('insurance_for', 'like', "%{$search}%")
                        ->orWhere('provider', 'like', "%{$search}%")
                        ->orWhere('policy_number', 'like', "%{$search}%");
                });
            })
            ->orderByRaw('next_due_date IS NULL')
            ->orderBy('next_due_date')
            ->paginate($perPage);

        return response()->json([
            'items' => $items->getCollection()->map(fn(BranchInsurance $insurance) => $this->resource($insurance))->values(),
            'pagination' => $this->pagination($items),
        ]);
    }

    public function show(BranchExpense $branchExpense, BranchInsurance $branchInsurance): JsonResponse
    {
        abort_unless((int) $branchInsurance->branch_expense_id === (int) $branchExpense->id, 404);

        return response()->json(['item' => $this->resource($branchInsurance)]);
    }

    public function store(Request $request, BranchExpense $branchExpense): JsonResponse
    {
        $payload = $this->payload($request);
        $payload['branch_expense_id'] = $branchExpense->id;
        $payload['branch_id'] = $branchExpense->branch_id;

        $insurance = BranchInsurance::create($payload);

        return response()->json(['message' => 'Versicherung wurde gespeichert.', 'item' => $this->resource($insurance)]);
    }

    public function update(Request $request, BranchExpense $branchExpense, BranchInsurance $branchInsurance): JsonResponse
    {
        abort_unless((int) $branchInsurance->branch_expense_id === (int) $branchExpense->id, 404);

        $branchInsurance->update($this->payload($request));

        return response()->json(['message' => 'Versicherung wurde aktualisiert.', 'item' => $this->resource($branchInsurance)]);
    }

    public function destroy(BranchExpense $branchExpense, BranchInsurance $branchInsurance): JsonResponse
    {
        abort_unless((int) $branchInsurance->branch_expense_id === (int) $branchExpense->id, 404);
        $branchInsurance->delete();

        return response()->json(['message' => 'Versicherung wurde gelöscht.']);
    }

    private function payload(Request $request): array
    {
        return $request->validate([
            'insurance_for' => ['required', 'string', 'max:255'],
            'provider' => ['nullable', 'string', 'max:255'],
            'policy_number' => ['nullable', 'string', 'max:255'],
            'coverage_amount' => ['nullable', 'numeric', 'min:0'],
            'monthly_payable' => ['nullable', 'numeric', 'min:0'],
            'payment_cycle' => ['nullable', 'string', 'max:100'],
            'due_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'next_due_date' => ['nullable', 'date'],
            'payment_date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(array_keys(BranchInsurance::statuses()))],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function resource(BranchInsurance $insurance): array
    {
        return [
            'id' => $insurance->id,
            'insurance_for' => $insurance->insurance_for,
            'provider' => $insurance->provider,
            'policy_number' => $insurance->policy_number,
            'coverage_amount' => (float) $insurance->coverage_amount,
            'monthly_payable' => (float) $insurance->monthly_payable,
            'payment_cycle' => $insurance->payment_cycle,
            'due_day' => $insurance->due_day,
            'next_due_date' => optional($insurance->next_due_date)->format('Y-m-d'),
            'payment_date' => optional($insurance->payment_date)->format('Y-m-d'),
            'start_date' => optional($insurance->start_date)->format('Y-m-d'),
            'end_date' => optional($insurance->end_date)->format('Y-m-d'),
            'status' => $insurance->status,
            'status_label' => $insurance->status_label,
            'notes' => $insurance->notes,
        ];
    }

    private function pagination($items): array
    {
        return [
            'current_page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
            'per_page' => $items->perPage(),
            'total' => $items->total(),
            'from' => $items->firstItem(),
            'to' => $items->lastItem(),
        ];
    }
}
