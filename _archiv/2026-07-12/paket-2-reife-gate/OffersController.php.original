<?php

namespace App\Http\Controllers\Customer\Offer;
use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\LeadProductList;
use App\Models\Offer;
use App\Models\Planing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class OffersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of offers.
     */
    public function index(): View
    {
        $search = trim((string) request()->query('search', ''));

        $query = Offer::query()
            ->with([
                'customer',
                'alternative',
                'productGroup',
                'creator',
                'assignee',
                'department',
                'detail',
                'leadProductList',
            ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('lastname', 'LIKE', "%{$search}%")
                        ->orWhere('contact_person', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%")
                        ->orWhere('telephone', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('alternative', function ($alternativeQuery) use ($search) {
                        $alternativeQuery->where('postcode', 'LIKE', "%{$search}%")
                            ->orWhere('city', 'LIKE', "%{$search}%")
                            ->orWhere('street', 'LIKE', "%{$search}%")
                            ->orWhere('address_no', 'LIKE', "%{$search}%")
                            ->orWhere('note', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('productGroup', function ($productQuery) use ($search) {
                        $productQuery->where('article_group', 'LIKE', "%{$search}%")
                            ->orWhere('initial', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('offer_no', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('status_msg', 'LIKE', "%{$search}%");
            });
        }

        $data['data'] = $query
            ->latest('id')
            ->paginate(19)
            ->withQueryString();

        return view('admin.offer.view.offer_view', $data);
    }

    /**
     * Store a newly created offer.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'customer_id' => ['required', 'exists:new_leads,id'],
                'product_id' => ['required', 'exists:article_groups,id'],
                'employee_id' => ['required', 'exists:employees,id'],
                'alternative_id' => ['nullable', 'exists:lead_alternative_adds,id'],
                'service_id' => ['nullable', 'exists:phase_sections,id'],
                'department_id' => ['nullable', 'exists:departments,id'],
                'service' => ['nullable', 'string'],
                'plan_id' => ['required', 'exists:planings,id'],
            ]);

            $offer = Offer::create([
                'customer_id' => $validated['customer_id'],
                'product_id' => $validated['product_id'],
                'alternative_id' => $validated['alternative_id'] ?? null,
                'service_id' => $validated['service_id'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'service' => $validated['service'] ?? null,
                'created_by' => auth()->user()->name ?? auth()->id(),
                'created_for' => $validated['employee_id'],
                'status_msg' => 'Nicht qualifiziert',
                'status' => 'new',
            ]);

            $planing = Planing::find($validated['plan_id']);

            if ($planing) {
                $planing->status = 'offer';
                $planing->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer has been sent to offer successfully.',
                'offer' => $offer,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Select2 employee search for the offer team modal.
     */
    public function employeeSearch(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', $request->get('search', '')));
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 20;

        $query = Employee::query()
            ->select('id', 'name', 'lastname', 'email', 'image')
            ->when($q !== '', function ($employeeQuery) use ($q) {
                $employeeQuery->where(function ($searchQuery) use ($q) {
                    $searchQuery->where('name', 'LIKE', "%{$q}%")
                        ->orWhere('lastname', 'LIKE', "%{$q}%")
                        ->orWhere('email', 'LIKE', "%{$q}%")
                        ->orWhereRaw("CONCAT(COALESCE(name, ''), ' ', COALESCE(lastname, '')) LIKE ?", ["%{$q}%"]);
                });
            })
            ->orderBy('name')
            ->orderBy('lastname');

        $employees = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'results' => $employees->getCollection()->map(function (Employee $employee) {
                $fullName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

                return [
                    'id' => $employee->id,
                    'employee_id' => $employee->id,
                    'text' => $fullName !== '' ? $fullName : ('Mitarbeiter #' . $employee->id),
                    'name' => $employee->name,
                    'lastname' => $employee->lastname,
                    'email' => $employee->email,
                    'image' => $employee->image,
                    'employee_image' => $employee->image,
                ];
            })->values(),
            'pagination' => [
                'more' => $employees->hasMorePages(),
            ],
        ]);
    }

    /**
     * Return the offer team from the connected lead_product_lists row.
     */
    public function team(Offer $offer): JsonResponse
    {
        $leadProductList = $this->findLeadProductListForOffer($offer);
        $teams = collect($leadProductList?->teams ?? []);

        $employeeIds = $teams
            ->pluck('employee_id')
            ->filter()
            ->unique()
            ->values();

        $employees = Employee::query()
            ->whereIn('id', $employeeIds)
            ->get()
            ->keyBy('id');

        return response()->json([
            'success' => true,
            'lead_product_list_id' => $leadProductList?->id,
            'teams' => $teams->map(function ($team) use ($employees) {
                $employee = $employees->get($team['employee_id'] ?? null);

                return [
                    'employee_id' => $team['employee_id'] ?? null,
                    'employee_name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    'employee_image' => $employee->image ?? null,
                    'stage' => $team['stage'] ?? null,
                    'old_stage' => $team['old_stage'] ?? null,
                    'assigned_by' => $team['assigned_by'] ?? null,
                    'assigned_at' => $team['assigned_at'] ?? null,
                    'company_stage_key' => $team['company_stage_key'] ?? null,
                    'product_stage_id' => $team['product_stage_id'] ?? null,
                    'product_stage_name' => $team['product_stage_name'] ?? null,
                    'product_task_phase_id' => $team['product_task_phase_id'] ?? null,
                ];
            })->values(),
        ]);
    }

    /**
     * Sync the offer team into lead_product_lists.teams.
     */
    public function syncTeam(Request $request, Offer $offer): JsonResponse
    {
        $validated = $request->validate([
            'employee_ids' => ['required', 'array'],
            'employee_ids.*' => ['required', 'integer', 'exists:employees,id'],
            'stage' => ['nullable', 'string', 'max:100'],
        ]);

        $leadProductList = $this->findLeadProductListForOffer($offer);

        if (!$leadProductList) {
            return response()->json([
                'success' => false,
                'message' => 'No matching LeadProductList found for this offer.',
            ], 404);
        }

        $stage = $validated['stage']
            ?? $offer->detail?->document_status
            ?? 'offer';

        $existingTeams = collect($leadProductList->teams ?? []);

        $newTeams = collect($validated['employee_ids'])
            ->unique()
            ->values()
            ->map(function ($employeeId) use ($stage, $leadProductList) {
                return [
                    'employee_id' => (int) $employeeId,
                    'stage' => $stage,
                    'old_stage' => $leadProductList->stage ?? $leadProductList->status ?? null,
                    'assigned_by' => auth()->user()->name ?? auth()->id(),
                    'assigned_at' => now()->format('Y-m-d H:i:s'),
                    'company_stage_key' => $leadProductList->status ?? null,
                    'product_stage_id' => $leadProductList->product_stage_id ?? null,
                    'product_stage_name' => $leadProductList->productStage?->name ?? null,
                    'product_task_phase_id' => $leadProductList->product_task_phase_id ?? null,
                ];
            });

        $leadProductList->teams = $existingTeams
            ->reject(function ($team) use ($stage) {
                return ($team['stage'] ?? null) === $stage;
            })
            ->merge($newTeams)
            ->values()
            ->toArray();

        $leadProductList->save();

        return response()->json([
            'success' => true,
            'message' => 'Offer team has been updated successfully.',
            'lead_product_list_id' => $leadProductList->id,
            'teams' => $leadProductList->teams,
        ]);
    }

    /**
     * Find the LeadProductList that belongs to the offer workflow.
     */
    protected function findLeadProductListForOffer(Offer $offer): ?LeadProductList
    {
        return LeadProductList::query()
            ->where('customer_id', $offer->customer_id)
            ->where('product_id', $offer->product_id)
            ->when($offer->alternative_id, function ($query) use ($offer) {
                $query->where('alternative_id', $offer->alternative_id);
            })
            ->with(['productStage'])
            ->first();
    }
 
}
