<?php

namespace App\Http\Controllers;

use App\Models\BranchExpense;
use App\Models\BranchRent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchExpenseRentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, BranchExpense $branchExpense): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 10), 5), 100);

        $items = $branchExpense->rents()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->query('search'));
                $query->where(function ($sub) use ($search) {
                    $sub->where('object_name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('street', 'like', "%{$search}%")
                        ->orWhere('landlord_name', 'like', "%{$search}%");
                });
            })
            ->orderByRaw('next_due_date IS NULL')
            ->orderBy('next_due_date')
            ->paginate($perPage);

        return response()->json([
            'items' => $items->getCollection()->map(fn(BranchRent $rent) => $this->resource($rent))->values(),
            'pagination' => $this->pagination($items),
        ]);
    }

    public function show(BranchExpense $branchExpense, BranchRent $branchRent): JsonResponse
    {
        abort_unless((int) $branchRent->expense_details_id === (int) $branchExpense->id, 404);

        return response()->json(['item' => $this->resource($branchRent)]);
    }

    public function store(Request $request, BranchExpense $branchExpense): JsonResponse
    {
        $payload = $this->payload($request);
        $payload['expense_details_id'] = $branchExpense->id;
        $payload['branch_id'] = $branchExpense->branch_id;
        $payload['total'] = ($payload['rent_cost'] ?? 0) + ($payload['extra_cost'] ?? 0);

        $rent = BranchRent::create($payload);

        return response()->json(['message' => 'Miete / Objekt wurde gespeichert.', 'item' => $this->resource($rent)]);
    }

    public function update(Request $request, BranchExpense $branchExpense, BranchRent $branchRent): JsonResponse
    {
        abort_unless((int) $branchRent->expense_details_id === (int) $branchExpense->id, 404);

        $payload = $this->payload($request);
        $payload['total'] = ($payload['rent_cost'] ?? 0) + ($payload['extra_cost'] ?? 0);
        $branchRent->update($payload);

        return response()->json(['message' => 'Miete / Objekt wurde aktualisiert.', 'item' => $this->resource($branchRent)]);
    }

    public function destroy(BranchExpense $branchExpense, BranchRent $branchRent): JsonResponse
    {
        abort_unless((int) $branchRent->expense_details_id === (int) $branchExpense->id, 404);
        $branchRent->delete();

        return response()->json(['message' => 'Miete / Objekt wurde gelöscht.']);
    }

    private function payload(Request $request): array
    {
        return $request->validate([
            'object_name' => ['required', 'string', 'max:255'],
            'object_type' => ['nullable', 'string', 'max:255'],
            'rent_cost' => ['nullable', 'numeric', 'min:0'],
            'extra_cost' => ['nullable', 'numeric', 'min:0'],
            'city' => ['nullable', 'string', 'max:255'],
            'street' => ['nullable', 'string', 'max:255'],
            'house_no' => ['nullable', 'string', 'max:50'],
            'postcode' => ['nullable', 'string', 'max:50'],
            'landlord_name' => ['nullable', 'string', 'max:255'],
            'landlord_contact' => ['nullable', 'string', 'max:255'],
            'contract_start' => ['nullable', 'date'],
            'contract_end' => ['nullable', 'date', 'after_or_equal:contract_start'],
            'payment_cycle' => ['nullable', 'string', 'max:100'],
            'due_day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'next_due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(array_keys(BranchRent::statuses()))],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function resource(BranchRent $rent): array
    {
        return [
            'id' => $rent->id,
            'object_name' => $rent->object_name,
            'object_type' => $rent->object_type,
            'rent_cost' => (float) $rent->rent_cost,
            'extra_cost' => (float) $rent->extra_cost,
            'total' => (float) $rent->total,
            'city' => $rent->city,
            'street' => $rent->street,
            'house_no' => $rent->house_no,
            'postcode' => $rent->postcode,
            'landlord_name' => $rent->landlord_name,
            'landlord_contact' => $rent->landlord_contact,
            'contract_start' => optional($rent->contract_start)->format('Y-m-d'),
            'contract_end' => optional($rent->contract_end)->format('Y-m-d'),
            'payment_cycle' => $rent->payment_cycle,
            'due_day' => $rent->due_day,
            'next_due_date' => optional($rent->next_due_date)->format('Y-m-d'),
            'status' => $rent->status,
            'status_label' => $rent->status_label,
            'notes' => $rent->notes,
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
