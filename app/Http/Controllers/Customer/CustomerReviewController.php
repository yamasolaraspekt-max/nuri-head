<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use App\Models\ArticleGroup;
use App\Models\CustomerReview;
use App\Models\LeadAlternativeAdd;
use App\Models\NewLeads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerReviewController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,update')->only(['update']);
        $this->middleware('permission:Customer,delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'integer', 'exists:lead_alternative_adds,id'],
            'product_id' => ['nullable', 'integer', 'exists:article_groups,id'],
        ]);

        $customer = NewLeads::findOrFail($data['customer_id']);

        $alternative = null;
        if (!empty($data['alternative_id'])) {
            $alternative = LeadAlternativeAdd::where('lead_id', $customer->id)
                ->where('id', $data['alternative_id'])
                ->first();
        }

        $product = null;
        if (!empty($data['product_id'])) {
            $product = ArticleGroup::find($data['product_id']);
        }

        $reviews = CustomerReview::query()
            ->with(['employee', 'customer', 'alternative', 'product'])
            ->where('customer_id', $customer->id)
            ->when(!empty($data['alternative_id']), function ($q) use ($data) {
                $q->where('alternative_id', $data['alternative_id']);
            })
            ->when(empty($data['alternative_id']), function ($q) {
                $q->whereNull('alternative_id');
            })
            ->when(!empty($data['product_id']), function ($q) use ($data) {
                $q->where('product_id', $data['product_id']);
            })
            ->when(empty($data['product_id']), function ($q) {
                $q->whereNull('product_id');
            })
            ->latest()
            ->get();

        $stats = [
            'count' => $reviews->count(),
            'avg_stars' => round((float) $reviews->avg('stars'), 1),
            'critical_count' => $reviews->where('is_critical', true)->count(),
            'latest' => optional($reviews->first())->created_at?->format('d.m.Y H:i'),
        ];

        return view('admin.new_leads.layouts.review', [
            'customer' => $customer,
            'alternative' => $alternative,
            'product' => $product,
            'reviews' => $reviews,
            'stats' => $stats,
            'customerId' => $customer->id,
            'alternativeId' => $data['alternative_id'] ?? null,
            'productId' => $data['product_id'] ?? null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'integer', 'exists:lead_alternative_adds,id'],
            'product_id' => ['nullable', 'integer', 'exists:article_groups,id'],

            'stars' => ['required', 'integer', 'min:1', 'max:5'],
            'behavior' => ['nullable', 'string', 'max:100'],
            'caution_note' => ['nullable', 'string', 'max:10000'],
            'internet_feedback' => ['nullable', 'string', 'max:10000'],
            'internal_note' => ['nullable', 'string', 'max:10000'],
            'source' => ['nullable', 'string', 'max:255'],
            'is_critical' => ['nullable'],
        ]);

        $customer = NewLeads::findOrFail($data['customer_id']);

        if (!empty($data['alternative_id'])) {
            LeadAlternativeAdd::where('lead_id', $customer->id)
                ->where('id', $data['alternative_id'])
                ->firstOrFail();
        }

        $data['employee_id'] = (int) auth()->user()->name;
        $data['is_critical'] = $request->boolean('is_critical');

        CustomerReview::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Bewertung wurde gespeichert.',
            'count' => $this->countForContext($data),
        ]);
    }

    public function update(Request $request, CustomerReview $customerReview)
    {
        $data = $request->validate([
            'stars' => ['required', 'integer', 'min:1', 'max:5'],
            'behavior' => ['nullable', 'string', 'max:100'],
            'caution_note' => ['nullable', 'string', 'max:10000'],
            'internet_feedback' => ['nullable', 'string', 'max:10000'],
            'internal_note' => ['nullable', 'string', 'max:10000'],
            'source' => ['nullable', 'string', 'max:255'],
            'is_critical' => ['nullable'],
        ]);

        $data['is_critical'] = $request->boolean('is_critical');

        $customerReview->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Bewertung wurde aktualisiert.',
            'count' => $this->countForContext($customerReview->toArray()),
        ]);
    }

    public function destroy(CustomerReview $customerReview)
    {
        $context = [
            'customer_id' => $customerReview->customer_id,
            'alternative_id' => $customerReview->alternative_id,
            'product_id' => $customerReview->product_id,
        ];

        $customerReview->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bewertung wurde gelöscht.',
            'count' => $this->countForContext($context),
        ]);
    }

    private function countForContext(array $data): int
    {
        return CustomerReview::query()
            ->where('customer_id', $data['customer_id'])
            ->when(!empty($data['alternative_id']), function ($q) use ($data) {
                $q->where('alternative_id', $data['alternative_id']);
            })
            ->when(empty($data['alternative_id']), function ($q) {
                $q->whereNull('alternative_id');
            })
            ->when(!empty($data['product_id']), function ($q) use ($data) {
                $q->where('product_id', $data['product_id']);
            })
            ->when(empty($data['product_id']), function ($q) {
                $q->whereNull('product_id');
            })
            ->count();
    }
}