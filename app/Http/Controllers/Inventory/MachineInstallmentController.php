<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;

use App\Models\AssetInstallment;
use App\Models\InstallmentPayment;
use App\Models\Machine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class MachineInstallmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Machine $machine, Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 10), 5), 50);
        $search = trim((string) $request->query('search'));

        $installments = AssetInstallment::query()
            ->with(['branch:id,branch', 'paidByEmployee:id,name,lastname'])
            ->withSum('payments as paid_total', 'payment_amount')
            ->where('type', 'machine')
            ->where('asset_id', $machine->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('installment_id', 'like', "%{$search}%")
                        ->orWhere('purchased_from', 'like', "%{$search}%")
                        ->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        $all = AssetInstallment::query()
            ->where('type', 'machine')
            ->where('asset_id', $machine->id);

        $allIds = (clone $all)->pluck('id');
        $total = (float) (clone $all)->sum('total');
        $paid = (float) InstallmentPayment::whereIn('installment_id', $allIds)->sum('payment_amount');

        return response()->json([
            'items' => $installments->getCollection()->map(fn(AssetInstallment $item) => $this->installmentResource($item))->values(),
            'summary' => [
                'count' => (clone $all)->count(),
                'total' => $total,
                'paid' => $paid,
                'remaining' => max($total - $paid, 0),
                'open' => (clone $all)->where('status', AssetInstallment::STATUS_OPEN)->count(),
                'overdue' => (clone $all)->where('status', AssetInstallment::STATUS_OVERDUE)->count(),
            ],
            'pagination' => [
                'current_page' => $installments->currentPage(),
                'last_page' => $installments->lastPage(),
                'per_page' => $installments->perPage(),
                'total' => $installments->total(),
                'from' => $installments->firstItem(),
                'to' => $installments->lastItem(),
            ],
        ]);
    }

    public function show(AssetInstallment $installment): JsonResponse
    {
        $installment->load(['branch:id,branch', 'paidByEmployee:id,name,lastname'])->loadSum('payments as paid_total', 'payment_amount');

        return response()->json(['item' => $this->installmentResource($installment)]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $payload = $this->validatedInstallment($request);
        $payload['type'] = 'machine';

        DB::beginTransaction();
        try {
            $installment = AssetInstallment::create($payload);
            $this->storeContractIfPresent($request, $installment);
            $installment->save();
            DB::commit();

            if ($request->ajax() || $request->expectsJson()) {
                $installment->load(['branch:id,branch', 'paidByEmployee:id,name,lastname'])->loadSum('payments as paid_total', 'payment_amount');
                return response()->json([
                    'message' => 'Ratenzahlung wurde gespeichert.',
                    'item' => $this->installmentResource($installment),
                ]);
            }

            return redirect()->route('machine.inventory', ['machine_id' => $installment->asset_id])->with('save_msg', 'Ratenzahlung wurde gespeichert.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);
            return $this->errorResponse($request, 'Ratenzahlung konnte nicht gespeichert werden.');
        }
    }

    public function update(Request $request, ?AssetInstallment $installment = null): JsonResponse|RedirectResponse
    {
        $installment = $installment ?: AssetInstallment::findOrFail($request->input('id'));
        $payload = $this->validatedInstallment($request, $installment->id);
        $payload['type'] = 'machine';

        DB::beginTransaction();
        try {
            $installment->fill($payload);
            $this->storeContractIfPresent($request, $installment);
            $installment->save();
            DB::commit();

            if ($request->ajax() || $request->expectsJson()) {
                $installment->load(['branch:id,branch', 'paidByEmployee:id,name,lastname'])->loadSum('payments as paid_total', 'payment_amount');
                return response()->json([
                    'message' => 'Ratenzahlung wurde aktualisiert.',
                    'item' => $this->installmentResource($installment),
                ]);
            }

            return redirect()->route('machine.inventory', ['machine_id' => $installment->asset_id])->with('updated_msg', 'Ratenzahlung wurde aktualisiert.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);
            return $this->errorResponse($request, 'Ratenzahlung konnte nicht aktualisiert werden.');
        }
    }

    public function destroy(Request $request, AssetInstallment $installment): JsonResponse|RedirectResponse
    {
        $assetId = $installment->asset_id;
        $this->deleteContract($installment->contract_document);
        $installment->delete();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => 'Ratenzahlung wurde gelöscht.']);
        }

        return redirect()->route('machine.inventory', ['machine_id' => $assetId])->with('delete_msg', 'Ratenzahlung wurde gelöscht.');
    }

    public function uploadContract(Request $request, AssetInstallment $installment): JsonResponse
    {
        $request->validate(['contract_document' => ['required', 'file', 'mimes:pdf', 'max:10240']]);
        $this->storeContractIfPresent($request, $installment);
        $installment->save();

        return response()->json([
            'message' => 'Vertrag wurde hochgeladen.',
            'item' => $this->installmentResource($installment->fresh()),
        ]);
    }

    public function payments(AssetInstallment $installment, Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 8), 5), 50);
        $search = trim((string) $request->query('search'));

        $payments = InstallmentPayment::query()
            ->where('installment_id', $installment->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('payment_date', 'like', "%{$search}%")
                        ->orWhere('payment_status', 'like', "%{$search}%")
                        ->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->latest('payment_date')
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        $paid = (float) InstallmentPayment::where('installment_id', $installment->id)->sum('payment_amount');
        $lateFees = (float) InstallmentPayment::where('installment_id', $installment->id)->sum('late_fee');

        return response()->json([
            'items' => $payments->getCollection()->map(fn(InstallmentPayment $payment) => $this->paymentResource($payment))->values(),
            'summary' => [
                'total' => (float) $installment->total,
                'paid' => $paid,
                'remaining' => max(((float) $installment->total) - $paid, 0),
                'late_fees' => $lateFees,
            ],
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem(),
            ],
        ]);
    }

    public function storePayment(Request $request): JsonResponse|RedirectResponse
    {
        $payload = $this->validatedPayment($request);

        DB::beginTransaction();
        try {
            $payment = InstallmentPayment::create($payload);
            $this->syncInstallmentStatus($payment->installment);
            DB::commit();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Ratenzahlung wurde bezahlt/gespeichert.',
                    'item' => $this->paymentResource($payment),
                ]);
            }

            return redirect()->route('machine.inventory')->with('save_msg', 'Ratenzahlung wurde bezahlt/gespeichert.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);
            return $this->errorResponse($request, 'Zahlung konnte nicht gespeichert werden.');
        }
    }

    public function updatePayment(Request $request, ?InstallmentPayment $payment = null): JsonResponse|RedirectResponse
    {
        $payment = $payment ?: InstallmentPayment::findOrFail($request->input('id'));
        $payload = $this->validatedPayment($request, $payment->id);

        DB::beginTransaction();
        try {
            $payment->fill($payload)->save();
            $this->syncInstallmentStatus($payment->installment);
            DB::commit();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Zahlung wurde aktualisiert.',
                    'item' => $this->paymentResource($payment),
                ]);
            }

            return redirect()->route('machine.inventory')->with('updated_msg', 'Zahlung wurde aktualisiert.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);
            return $this->errorResponse($request, 'Zahlung konnte nicht aktualisiert werden.');
        }
    }

    public function destroyPayment(Request $request, InstallmentPayment $payment): JsonResponse|RedirectResponse
    {
        $installment = $payment->installment;
        $payment->delete();
        $this->syncInstallmentStatus($installment);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => 'Zahlung wurde gelöscht.']);
        }

        return redirect()->route('machine.inventory')->with('delete_msg', 'Zahlung wurde gelöscht.');
    }

    private function validatedInstallment(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'asset_id' => ['required', 'integer', 'exists:machines,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'installment_id' => ['required', 'string', 'max:255'],
            'purchased_from' => ['nullable', 'string', 'max:255'],
            'price_per_month' => ['required', 'numeric', 'min:0'],
            'fines' => ['nullable', 'numeric', 'min:0'],
            'installment_duration' => ['required', 'integer', 'min:1'],
            'due_date' => ['nullable', 'date'],
            'total' => ['required', 'numeric', 'min:0'],
            'paid_by' => ['nullable', 'integer', 'exists:employees,id'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'insurance_provider' => ['nullable', 'string', 'max:255'],
            'insurance_amount' => ['nullable', 'numeric', 'min:0'],
            'insurance_payment_month' => ['nullable', 'string', 'max:100'],
            'insurance_expiry_date' => ['nullable', 'date'],
            'contract_document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'status' => ['nullable', Rule::in(array_keys(AssetInstallment::statuses()))],
        ]);
    }

    private function validatedPayment(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'installment_id' => ['required', 'integer', 'exists:asset_installments,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'payment_amount' => ['required', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'paid_month_count' => ['required', 'integer', 'min:1'],
            'payment_remained' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['required', 'string', 'max:100'],
            'late_fee' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function installmentResource(AssetInstallment $item): array
    {
        $paid = (float) ($item->paid_total ?? $item->paid_total);
        $total = (float) $item->total;

        return [
            'id' => $item->id,
            'asset_id' => $item->asset_id,
            'type' => $item->type,
            'branch_id' => $item->branch_id,
            'branch' => $item->branch?->branch,
            'installment_id' => $item->installment_id,
            'purchased_from' => $item->purchased_from,
            'price_per_month' => (float) $item->price_per_month,
            'fines' => (float) $item->fines,
            'installment_duration' => (int) $item->installment_duration,
            'due_date' => optional($item->due_date)->format('Y-m-d'),
            'total' => $total,
            'paid_total' => $paid,
            'remaining_total' => max($total - $paid, 0),
            'paid_by' => $item->paid_by,
            'paid_by_name' => trim(($item->paidByEmployee?->name ?? '') . ' ' . ($item->paidByEmployee?->lastname ?? '')) ?: null,
            'payment_method' => $item->payment_method,
            'insurance_provider' => $item->insurance_provider,
            'insurance_amount' => (float) $item->insurance_amount,
            'insurance_payment_month' => $item->insurance_payment_month,
            'insurance_expiry_date' => optional($item->insurance_expiry_date)->format('Y-m-d'),
            'contract_document' => $item->contract_document,
            'contract_url' => $item->contract_url,
            'status' => $item->status ?: AssetInstallment::STATUS_OPEN,
            'status_label' => AssetInstallment::statuses()[$item->status ?: AssetInstallment::STATUS_OPEN] ?? $item->status,
        ];
    }

    private function paymentResource(InstallmentPayment $payment): array
    {
        return [
            'id' => $payment->id,
            'installment_id' => $payment->installment_id,
            'branch_id' => $payment->branch_id,
            'payment_amount' => (float) $payment->payment_amount,
            'payment_date' => optional($payment->payment_date)->format('Y-m-d'),
            'paid_month_count' => (int) $payment->paid_month_count,
            'payment_remained' => (float) $payment->payment_remained,
            'payment_status' => $payment->payment_status,
            'late_fee' => (float) $payment->late_fee,
            'payment_method' => $payment->payment_method,
            'notes' => $payment->notes,
        ];
    }

    private function storeContractIfPresent(Request $request, AssetInstallment $installment): void
    {
        if (!$request->hasFile('contract_document')) {
            return;
        }

        $this->deleteContract($installment->contract_document);
        $destination = public_path('images/installment/contract');
        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $name = time() . '_' . $request->file('contract_document')->getClientOriginalName();
        $request->file('contract_document')->move($destination, $name);
        $installment->contract_document = $name;
    }

    private function deleteContract(?string $file): void
    {
        if (!$file) {
            return;
        }

        $path = public_path('images/installment/contract/' . $file);
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function syncInstallmentStatus(?AssetInstallment $installment): void
    {
        if (!$installment) {
            return;
        }

        $paid = (float) $installment->payments()->sum('payment_amount');
        if ($paid >= (float) $installment->total && (float) $installment->total > 0) {
            $installment->status = AssetInstallment::STATUS_PAID;
        } elseif ($installment->due_date && $installment->due_date->isPast()) {
            $installment->status = AssetInstallment::STATUS_OVERDUE;
        } else {
            $installment->status = AssetInstallment::STATUS_OPEN;
        }
        $installment->save();
    }

    private function errorResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => $message], 500);
        }

        return back()->withInput()->with('delete_msg', $message);
    }
}
