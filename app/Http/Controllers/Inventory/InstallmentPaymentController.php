<?php

namespace App\Http\Controllers;

use App\Models\AssetInstallment;
use App\Models\InstallmentPayment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $payments = InstallmentPayment::query()
            ->with(['installment:id,installment_id,total,asset_id,type', 'branch:id,branch'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('payment_date', 'like', "%{$search}%")
                        ->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhere('payment_status', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            })
            ->latest('payment_date')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json($payments);
        }

        return view('admin.product.assets.installment.payment.pay', ['data' => $payments]);
    }

    public function create(int $id)
    {
        $installment = AssetInstallment::query()
            ->with(['machine:id,name,model'])
            ->findOrFail($id);

        return view('admin.product.assets.installment.payment.pay', ['data' => $installment]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $payload = $this->validatedPayload($request);

        DB::beginTransaction();
        try {
            $payment = InstallmentPayment::create($payload);
            $this->syncInstallmentStatus($payment->installment);
            DB::commit();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Zahlung wurde gespeichert.',
                    'item' => $payment,
                ]);
            }

            return redirect()->route('machine.inventory')->with('save_msg', 'Zahlung wurde gespeichert.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Zahlung konnte nicht gespeichert werden.'], 500);
            }

            return back()->withInput()->with('delete_msg', 'Zahlung konnte nicht gespeichert werden.');
        }
    }

    public function update(Request $request, InstallmentPayment $installmentPayment): JsonResponse|RedirectResponse
    {
        $payload = $this->validatedPayload($request);
        $installmentPayment->fill($payload)->save();
        $this->syncInstallmentStatus($installmentPayment->installment);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'message' => 'Zahlung wurde aktualisiert.',
                'item' => $installmentPayment,
            ]);
        }

        return redirect()->route('machine.inventory')->with('updated_msg', 'Zahlung wurde aktualisiert.');
    }

    public function destroy(Request $request, InstallmentPayment $installmentPayment): JsonResponse|RedirectResponse
    {
        $installment = $installmentPayment->installment;
        $installmentPayment->delete();
        $this->syncInstallmentStatus($installment);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => 'Zahlung wurde gelöscht.']);
        }

        return redirect()->route('machine.inventory')->with('delete_msg', 'Zahlung wurde gelöscht.');
    }

    private function validatedPayload(Request $request): array
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
}
