<?php

namespace App\Http\Controllers;

use App\Models\BranchExpense;
use App\Models\BranchExpenseOtherCost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchExpenseOtherCostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, BranchExpense $branchExpense): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 10), 5), 100);

        $items = $branchExpense->otherCosts()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->query('search'));
                $query->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('vendor', 'like', "%{$search}%")
                        ->orWhere('invoice_no', 'like', "%{$search}%");
                });
            })
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date')
            ->paginate($perPage);

        return response()->json([
            'items' => $items->getCollection()->map(fn(BranchExpenseOtherCost $cost) => $this->resource($cost))->values(),
            'pagination' => $this->pagination($items),
        ]);
    }

    public function show(BranchExpense $branchExpense, BranchExpenseOtherCost $otherCost): JsonResponse
    {
        abort_unless((int) $otherCost->branch_expense_id === (int) $branchExpense->id, 404);

        return response()->json(['item' => $this->resource($otherCost)]);
    }

    public function store(Request $request, BranchExpense $branchExpense): JsonResponse
    {
        $payload = $this->payload($request);
        $payload['branch_expense_id'] = $branchExpense->id;
        $payload['branch_id'] = $branchExpense->branch_id;

        $cost = BranchExpenseOtherCost::create($payload);

        return response()->json(['message' => 'Sonstige Kosten wurden gespeichert.', 'item' => $this->resource($cost)]);
    }

    public function update(Request $request, BranchExpense $branchExpense, BranchExpenseOtherCost $otherCost): JsonResponse
    {
        abort_unless((int) $otherCost->branch_expense_id === (int) $branchExpense->id, 404);

        $otherCost->update($this->payload($request));

        return response()->json(['message' => 'Sonstige Kosten wurden aktualisiert.', 'item' => $this->resource($otherCost)]);
    }

    public function destroy(BranchExpense $branchExpense, BranchExpenseOtherCost $otherCost): JsonResponse
    {
        abort_unless((int) $otherCost->branch_expense_id === (int) $branchExpense->id, 404);
        $otherCost->delete();

        return response()->json(['message' => 'Sonstige Kosten wurden gelöscht.']);
    }

    private function payload(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(array_keys(BranchExpenseOtherCost::categories()))],
            'amount' => ['required', 'numeric', 'min:0'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'invoice_no' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'payment_cycle' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(array_keys(BranchExpenseOtherCost::statuses()))],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function resource(BranchExpenseOtherCost $cost): array
    {
        return [
            'id' => $cost->id,
            'title' => $cost->title,
            'category' => $cost->category,
            'category_label' => $cost->category_label,
            'amount' => (float) $cost->amount,
            'vendor' => $cost->vendor,
            'invoice_no' => $cost->invoice_no,
            'payment_date' => optional($cost->payment_date)->format('Y-m-d'),
            'due_date' => optional($cost->due_date)->format('Y-m-d'),
            'payment_cycle' => $cost->payment_cycle,
            'status' => $cost->status,
            'status_label' => $cost->status_label,
            'notes' => $cost->notes,
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
