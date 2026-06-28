<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use App\Models\MainAppointment;
use App\Models\NewLeads;
use App\Models\Offer;
use App\Models\PersonalTask;
use App\Models\Problem;
use App\Models\PhaseSection;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardDepartmentController extends Controller
{
    private function employeeId(): int
    {
        return (int) auth()->user()->name;
    }

    public function departments(): JsonResponse
    {
        $employeeId = $this->employeeId();
        $mainDepartmentId = $this->defaultDepartmentId($employeeId);

        $query = Department::query()
            ->select([
                'departments.id',
                'departments.department_name',
                'departments.parent_id',
                'departments.branch_id',
                'departments.department_head',
                'departments.head_representative',
            ])
            ->with([
                'branch:id,branch,slug,initial,color,second_color,logo_url',
                'parent:id,department_name',
            ]);

        if (!auth()->user()?->is_admin) {
            $query->whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employees.id', $employeeId);
            });
        }

        $departments = $query
            ->get()
            ->sortByDesc(fn($department) => (int) $department->id === (int) $mainDepartmentId)
            ->values();

        return response()->json([
            'success' => true,
            'current_employee_id' => $employeeId,
            'default_department_id' => $mainDepartmentId ?: $departments->first()?->id,
            'departments' => $departments->map(fn($department) => [
                'id' => $department->id,
                'name' => $department->department_name,
                'parent' => $department->parent?->department_name,
                'branch' => $this->branchName($department->branch),
                'branch_color' => $department->branch?->color,
                'branch_second_color' => $department->branch?->second_color,
                'branch_logo' => $department->branch?->logo_url,
                'is_main' => (int) $department->id === (int) $mainDepartmentId,
            ])->values(),
        ]);
    }

    public function overview(Request $request): JsonResponse
    {
        $employeeId = $this->employeeId();

        $departmentId = (int) $request->input('department_id');

        if (!$departmentId) {
            $departmentId = $this->defaultDepartmentId($employeeId);
        }

        if (!$departmentId) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Abteilung gefunden.',
            ], 404);
        }

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : now()->endOfDay();

        $department = Department::query()
            ->with([
                'branch:id,branch,slug,initial,color,second_color,logo_url',
                'parent:id,department_name',
                'employees' => function ($q) {
                    $q->select('employees.id', 'employees.name', 'employees.lastname', 'employees.image')
                        ->withPivot([
                            'position_id',
                            'percent',
                            'montage_percent',
                            'office_percent',
                            'working_hours',
                            'main',
                        ]);
                },
                'departmentPositions.position:id,position,qualification_id',
                'departmentPositions.position.qualificationRef:id,name,default_price,status',
                'departmentPositions.employee:id,name,lastname,image',
            ])
            ->findOrFail($departmentId);

        $employeeIds = $department->employees
            ->pluck('id')
            ->filter()
            ->unique()
            ->values();

        $employeeIdsArray = $employeeIds->all();

        $allScope = $this->departmentProductScope($departmentId);
        $periodScope = $this->departmentProductScope($departmentId, $from, $to);

        $customerIdsArray = $allScope['customer_ids']->all();
        $alternativeIdsArray = $allScope['alternative_ids']->all();
        $productIdsArray = $allScope['product_ids']->all();

        $periodCustomerIdsArray = $periodScope['customer_ids']->all();
        $periodAlternativeIdsArray = $periodScope['alternative_ids']->all();
        $periodProductIdsArray = $periodScope['product_ids']->all();
        $periodProductListIdsArray = $periodScope['product_list_ids']->all();

        $offerIds = $this->departmentOfferIds(
            departmentId: $departmentId,
            customerIds: $customerIdsArray,
            alternativeIds: $alternativeIdsArray,
            productIds: $productIdsArray
        );

        $dealIds = $this->departmentDealIds(
            departmentId: $departmentId,
            customerIds: $customerIdsArray,
            alternativeIds: $alternativeIdsArray,
            productIds: $productIdsArray
        );

        $problemIds = $this->departmentProblemIds(
            employeeIds: $employeeIdsArray,
            customerIds: $customerIdsArray,
            alternativeIds: $alternativeIdsArray,
            productIds: $productIdsArray
        );

        $appointmentIds = $this->departmentAppointmentIds(
            employeeIds: $employeeIdsArray,
            customerIds: $customerIdsArray,
            alternativeIds: $alternativeIdsArray,
            problemIds: $problemIds->all()
        );

        $taskIds = $this->departmentTaskIds(
            employeeIds: $employeeIdsArray,
            customerIds: $customerIdsArray,
            alternativeIds: $alternativeIdsArray
        );

        $invoiceQuery = $this->invoiceBaseQuery(
            customerIds: $customerIdsArray,
            alternativeIds: $alternativeIdsArray,
            employeeIds: $employeeIdsArray
        );

        $kpis = $this->buildKpis(
            employeeIds: $employeeIdsArray,

            allCustomerIds: $customerIdsArray,
            allAlternativeIds: $alternativeIdsArray,
            allProductIds: $productIdsArray,
            allProductListIds: $allScope['product_list_ids']->all(),

            periodCustomerIds: $periodCustomerIdsArray,
            periodAlternativeIds: $periodAlternativeIdsArray,
            periodProductIds: $periodProductIdsArray,
            periodProductListIds: $periodProductListIdsArray,

            offerIds: $offerIds->all(),
            dealIds: $dealIds->all(),
            problemIds: $problemIds->all(),
            appointmentIds: $appointmentIds->all(),
            taskIds: $taskIds->all(),
            invoiceBase: $invoiceQuery,
            from: $from,
            to: $to
        );

        $charts = [
            'workload_by_employee' => $this->workloadByEmployee($employeeIdsArray, $from, $to),

            'items_by_type' => $this->itemsByType(
                periodCustomerIds: $periodCustomerIdsArray,
                periodAlternativeIds: $periodAlternativeIdsArray,
                periodProductIds: $periodProductIdsArray,
                periodProductListIds: $periodProductListIdsArray,
                offerIds: $offerIds->all(),
                dealIds: $dealIds->all(),
                problemIds: $problemIds->all(),
                appointmentIds: $appointmentIds->all(),
                taskIds: $taskIds->all(),
                invoiceBase: $invoiceQuery,
                from: $from,
                to: $to
            ),

            'revenue_by_status' => $this->invoiceByStatus($invoiceQuery, $from, $to),
            'team_by_position' => $this->teamByPosition($departmentId),
            'lead_pipeline' => $this->leadPipeline($departmentId, $from, $to),
            'products_by_group' => $this->productsByGroup($departmentId, $from, $to),
            'objects_by_status' => $this->objectsByStatus($periodAlternativeIdsArray),
        ];

        return response()->json([
            'success' => true,

            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],

            'department' => [
                'id' => $department->id,
                'name' => $department->department_name,
                'branch' => $this->branchName($department->branch),
                'branch_color' => $department->branch?->color,
                'branch_second_color' => $department->branch?->second_color,
                'branch_logo' => $department->branch?->logo_url,
                'parent' => $department->parent?->department_name,
            ],

            'team' => $this->team($department),
            'kpis' => $kpis,
            'charts' => $charts,

            'recent' => [
                'leads' => $this->recentLeads($departmentId, $from, $to),
                'products' => $this->recentLeadProducts($departmentId, $from, $to),
                'offers' => $this->recentOffers($offerIds->all(), $from, $to),
                'deals' => $this->recentDeals($dealIds->all(), $from, $to),
                'invoices' => $this->recentInvoices($invoiceQuery, $from, $to),
                'tickets' => $this->recentTickets($problemIds->all(), $from, $to),
                'appointments' => $this->recentAppointments($appointmentIds->all(), $from, $to),
                'tasks' => $this->recentTasks($taskIds->all(), $from, $to),
                'changes' => $this->recentChanges(
                    customerIds: $customerIdsArray,
                    productListIds: $allScope['product_list_ids']->all(),
                    offerIds: $offerIds->all(),
                    dealIds: $dealIds->all(),
                    problemIds: $problemIds->all(),
                    appointmentIds: $appointmentIds->all(),
                    taskIds: $taskIds->all(),
                    from: $from,
                    to: $to
                ),
            ],
        ]);
    }

    private function departmentProductScope(int $departmentId, ?Carbon $from = null, ?Carbon $to = null): array
    {
        /*
        |--------------------------------------------------------------------------
        | Correct department logic
        |--------------------------------------------------------------------------
        | 1. A department is connected to ArticleGroups/products through
        |    lead_product_lists.department_id.
        |
        | 2. After finding those product IDs, we count ALL leads/customers connected
        |    to those products.
        |
        | Example:
        | Department 2 => product IDs 17,16,18,33,49,34,38,51,53
        | Correct Leads => COUNT(DISTINCT customer_id) for these product IDs.
        |--------------------------------------------------------------------------
        */

        $departmentProductIds = LeadProductList::query()
            ->where('department_id', $departmentId)
            ->whereNotNull('product_id')
            ->distinct()
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();

        $query = LeadProductList::query()
            ->whereIn('product_id', $departmentProductIds)
            ->whereNotNull('customer_id')
            ->select([
                'id',
                'customer_id',
                'alternative_id',
                'product_id',
                'employee_id',
                'field_employee',
                'created_at',
            ]);

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        $rows = $query->get();

        return [
            'department_product_ids' => $departmentProductIds,

            'product_list_ids' => $rows
                ->pluck('id')
                ->filter()
                ->unique()
                ->values(),

            'customer_ids' => $rows
                ->pluck('customer_id')
                ->filter()
                ->unique()
                ->values(),

            'alternative_ids' => $rows
                ->pluck('alternative_id')
                ->filter()
                ->unique()
                ->values(),

            'product_ids' => $rows
                ->pluck('product_id')
                ->filter()
                ->unique()
                ->values(),

            'employee_ids' => $rows
                ->pluck('employee_id')
                ->merge($rows->pluck('field_employee'))
                ->filter()
                ->unique()
                ->values(),
        ];
    }
    private function buildKpis(
        array $employeeIds,

        array $allCustomerIds,
        array $allAlternativeIds,
        array $allProductIds,
        array $allProductListIds,

        array $periodCustomerIds,
        array $periodAlternativeIds,
        array $periodProductIds,
        array $periodProductListIds,

        array $offerIds,
        array $dealIds,
        array $problemIds,
        array $appointmentIds,
        array $taskIds,
        $invoiceBase,
        Carbon $from,
        Carbon $to
    ): array {
        return [
            'team_members' => count(array_unique($employeeIds)),

            // Main KPI totals: all department-connected customers/products
            'leads' => count(array_unique($allCustomerIds)),
            'customers' => count(array_unique($allCustomerIds)),
            'objects' => count(array_unique($allAlternativeIds)),
            'product_groups' => count(array_unique($allProductIds)),
            'products' => count(array_unique($allProductListIds)),

            // Optional period values
            'new_leads' => count(array_unique($periodCustomerIds)),
            'new_objects' => count(array_unique($periodAlternativeIds)),
            'new_product_groups' => count(array_unique($periodProductIds)),
            'new_products' => count(array_unique($periodProductListIds)),

            'offers' => Offer::query()
                ->whereIn('id', $offerIds)
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'deals' => Deal::query()
                ->whereIn('id', $dealIds)
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'tickets' => Problem::query()
                ->whereIn('id', $problemIds)
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'appointments' => MainAppointment::query()
                ->whereIn('id', $appointmentIds)
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'tasks' => PersonalTask::query()
                ->whereIn('id', $taskIds)
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'invoices' => (clone $invoiceBase)
                ->whereBetween('created_at', [$from, $to])
                ->count(),

            'invoice_total' => round((float) (clone $invoiceBase)
                ->whereBetween('created_at', [$from, $to])
                ->sum('total_amount'), 2),

            'invoice_open' => round((float) (clone $invoiceBase)
                ->whereBetween('created_at', [$from, $to])
                ->selectRaw('SUM(GREATEST(total_amount - paid_amount, 0)) as open_sum')
                ->value('open_sum'), 2),
        ];
    }

    private function itemsByType(
        array $periodCustomerIds,
        array $periodAlternativeIds,
        array $periodProductIds,
        array $periodProductListIds,
        array $offerIds,
        array $dealIds,
        array $problemIds,
        array $appointmentIds,
        array $taskIds,
        $invoiceBase,
        Carbon $from,
        Carbon $to
    ): array {
        return [
            'labels' => [
                'Leads',
                'Objekte',
                'Produktgruppen',
                'Produkte',
                'Angebote',
                'Aufträge',
                'Rechnungen',
                'Tickets',
                'Termine',
                'Aufgaben',
            ],

            'values' => [
                count(array_unique($periodCustomerIds)),
                count(array_unique($periodAlternativeIds)),
                count(array_unique($periodProductIds)),
                count(array_unique($periodProductListIds)),

                Offer::query()
                    ->whereIn('id', $offerIds)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                Deal::query()
                    ->whereIn('id', $dealIds)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                (clone $invoiceBase)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                Problem::query()
                    ->whereIn('id', $problemIds)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                MainAppointment::query()
                    ->whereIn('id', $appointmentIds)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                PersonalTask::query()
                    ->whereIn('id', $taskIds)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),
            ],
        ];
    }

    private function departmentOfferIds(
        int $departmentId,
        array $customerIds,
        array $alternativeIds,
        array $productIds
    ) {
        return Offer::query()
            ->where(function ($q) use ($departmentId, $customerIds, $alternativeIds, $productIds) {
                $this->orWhereColumnValue($q, 'offers', 'department_id', [$departmentId]);
                $this->orWhereColumnValue($q, 'offers', 'customer_id', $customerIds);
                $this->orWhereColumnValue($q, 'offers', 'alternative_id', $alternativeIds);
                $this->orWhereColumnValue($q, 'offers', 'object_id', $alternativeIds);
                $this->orWhereColumnValue($q, 'offers', 'product_id', $productIds);
            })
            ->pluck('id')
            ->unique()
            ->values();
    }

    private function departmentDealIds(
        int $departmentId,
        array $customerIds,
        array $alternativeIds,
        array $productIds
    ) {
        return Deal::query()
            ->where(function ($q) use ($departmentId, $customerIds, $alternativeIds, $productIds) {
                $this->orWhereColumnValue($q, 'deals', 'department_id', [$departmentId]);
                $this->orWhereColumnValue($q, 'deals', 'customer_id', $customerIds);
                $this->orWhereColumnValue($q, 'deals', 'alternative_id', $alternativeIds);
                $this->orWhereColumnValue($q, 'deals', 'object_id', $alternativeIds);
                $this->orWhereColumnValue($q, 'deals', 'product_id', $productIds);
            })
            ->pluck('id')
            ->unique()
            ->values();
    }

    private function departmentProblemIds(
        array $employeeIds,
        array $customerIds = [],
        array $alternativeIds = [],
        array $productIds = []
    ) {
        if (empty($employeeIds) && empty($customerIds) && empty($alternativeIds) && empty($productIds)) {
            return collect();
        }

        return Problem::query()
            ->where(function ($q) use ($employeeIds, $customerIds, $alternativeIds, $productIds) {
                if (!empty($employeeIds)) {
                    $q->whereIn('responsible', $employeeIds)
                        ->orWhereIn('start_user', $employeeIds)
                        ->orWhereIn('progress_user', $employeeIds)
                        ->orWhereIn('end_user', $employeeIds)
                        ->orWhereIn('edit_user', $employeeIds)
                        ->orWhereHas('employees', function ($sq) use ($employeeIds) {
                            $sq->whereIn('employees.id', $employeeIds);
                        });
                }

                $this->orWhereColumnValue($q, 'problems', 'customer_id', $customerIds);
                $this->orWhereColumnValue($q, 'problems', 'alternative_id', $alternativeIds);
                $this->orWhereColumnValue($q, 'problems', 'object_id', $alternativeIds);
                $this->orWhereColumnValue($q, 'problems', 'product_id', $productIds);
            })
            ->pluck('id')
            ->unique()
            ->values();
    }

    private function departmentAppointmentIds(
        array $employeeIds,
        array $customerIds,
        array $alternativeIds,
        array $problemIds
    ) {
        if (empty($employeeIds) && empty($customerIds) && empty($alternativeIds) && empty($problemIds)) {
            return collect();
        }

        return MainAppointment::query()
            ->where(function ($q) use ($employeeIds, $customerIds, $alternativeIds, $problemIds) {
                if (!empty($employeeIds)) {
                    $q->whereIn('created_by', $employeeIds)
                        ->orWhereIn('changed_by', $employeeIds)
                        ->orWhereHas('employees', function ($sq) use ($employeeIds) {
                            $sq->whereIn('employees.id', $employeeIds);
                        });
                }

                $this->orWhereColumnValue($q, 'main_appointments', 'customer_id', $customerIds);
                $this->orWhereColumnValue($q, 'main_appointments', 'alternative_id', $alternativeIds);
                $this->orWhereColumnValue($q, 'main_appointments', 'object_id', $alternativeIds);
                $this->orWhereColumnValue($q, 'main_appointments', 'problem_id', $problemIds);
            })
            ->pluck('id')
            ->unique()
            ->values();
    }

    private function departmentTaskIds(
        array $employeeIds,
        array $customerIds,
        array $alternativeIds
    ) {
        if (empty($employeeIds) && empty($customerIds) && empty($alternativeIds)) {
            return collect();
        }

        return PersonalTask::query()
            ->where(function ($q) use ($employeeIds, $customerIds, $alternativeIds) {
                if (!empty($employeeIds)) {
                    $q->whereIn('assigned_by', $employeeIds)
                        ->orWhereHas('employees', function ($sq) use ($employeeIds) {
                            $sq->whereIn('employees.id', $employeeIds);
                        });
                }

                $this->orWhereColumnValue($q, 'personal_tasks', 'customer_id', $customerIds);
                $this->orWhereColumnValue($q, 'personal_tasks', 'alternative_id', $alternativeIds);
                $this->orWhereColumnValue($q, 'personal_tasks', 'object_id', $alternativeIds);
            })
            ->pluck('id')
            ->unique()
            ->values();
    }

    private function invoiceBaseQuery(
        array $customerIds,
        array $alternativeIds,
        array $employeeIds
    ) {
        return Invoice::query()
            ->where(function ($q) use ($customerIds, $alternativeIds, $employeeIds) {
                $this->orWhereColumnValue($q, 'invoices', 'customer_id', $customerIds);
                $this->orWhereColumnValue($q, 'invoices', 'alternative_id', $alternativeIds);
                $this->orWhereColumnValue($q, 'invoices', 'object_id', $alternativeIds);
                $this->orWhereColumnValue($q, 'invoices', 'created_by', $employeeIds);
                $this->orWhereColumnValue($q, 'invoices', 'updated_by', $employeeIds);
            });
    }

    private function recentLeads(int $departmentId, Carbon $from, Carbon $to): array
    {
        $departmentProductIds = LeadProductList::query()
            ->where('department_id', $departmentId)
            ->whereNotNull('product_id')
            ->distinct()
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();

        $customerIds = LeadProductList::query()
            ->whereIn('product_id', $departmentProductIds)
            ->whereBetween('created_at', [$from, $to])
            ->pluck('customer_id')
            ->filter()
            ->unique()
            ->values();

        return NewLeads::query()
            ->with([
                'objects' => function ($q) {
                    $q->select(
                        'id',
                        'lead_id',
                        'object_name',
                        'address_no',
                        'street',
                        'postcode',
                        'city',
                        'status',
                        'stage',
                        'created_at'
                    );
                },
                'objects.productLists' => function ($q) use ($departmentProductIds) {
                    $q->select(
                        'id',
                        'customer_id',
                        'alternative_id',
                        'product_id',
                        'service_id',
                        'service',
                        'department_id',
                        'employee_id',
                        'field_employee',
                        'status',
                        'work_status',
                        'stage',
                        'created_at'
                    )->whereIn('product_id', $departmentProductIds);
                },
                'objects.productLists.product:id,article_group,initial,image',
                'objects.productLists.service:id,phase_section,product_id',
                'contact:id,name,lastname,image',
            ])
            ->whereIn('id', $customerIds)
            ->latest()
            ->limit(8)
            ->get()
            ->map(function ($lead) {
                $productLists = $lead->objects
                    ->flatMap(fn($object) => $object->productLists)
                    ->sortByDesc('created_at')
                    ->values();

                $mainProduct = $productLists->first();
                $mainObject = $lead->objects->firstWhere('id', $mainProduct?->alternative_id)
                    ?: $lead->objects->first();

                $objects = $lead->objects->map(function ($object) {
                    return [
                        'id' => $object->id,
                        'name' => $object->display_name,
                        'address_no' => $object->address_no,
                        'address' => $this->objectAddress($object),
                        'status' => $object->status ?? $object->stage,
                        'status_label' => $this->statusLabel($object->status ?? $object->stage, 'object'),
                        'products' => $object->productLists->map(fn($item) => [
                            'id' => $item->id,
                            'product' => $this->productName($item->product),
                            'service' => $this->serviceName($item),
                            'status' => $item->status ?? $item->work_status ?? $item->stage,
                            'status_label' => $this->statusLabel($item->status ?? $item->work_status ?? $item->stage, 'lead_product'),
                        ])->values(),
                    ];
                })->values();

                return [
                    'id' => $lead->id,
                    'type' => 'lead',
                    'type_label' => 'Lead',
                    'number' => $lead->customer_no,
                    'title' => $lead->display_name,
                    'status' => $lead->status,
                    'status_label' => $this->statusLabel($lead->status, 'lead'),
                    'customer' => $this->customerMini($lead),
                    'object' => $this->objectMini($mainObject),
                    'product' => $this->productName($mainProduct?->product),
                    'service' => $mainProduct ? $this->serviceName($mainProduct) : null,
                    'contact_person' => $this->employeeMini($lead->contact),
                    'objects_count' => $objects->count(),
                    'products_count' => $productLists->count(),
                    'objects' => $objects,
                    'url' => $this->customerProfileUrl($lead->id),
                    'action_label' => 'Kundenprofil öffnen',
                    'date' => optional($lead->created_at)->format('d.m.Y H:i'),
                ];
            })
            ->values()
            ->all();
    }

    private function recentLeadProducts(int $departmentId, Carbon $from, Carbon $to): array
    {
        $departmentProductIds = LeadProductList::query()
            ->where('department_id', $departmentId)
            ->whereNotNull('product_id')
            ->distinct()
            ->pluck('product_id')
            ->filter()
            ->unique()
            ->values();

        return LeadProductList::query()
            ->with([
                'customer:id,customer_no,firma,name,lastname,status,street,postcode,city,phone,telephone,email',
                'alternative:id,lead_id,object_name,address_no,street,postcode,city,status,stage',
                'employee:id,name,lastname,image',
                'fieldEmployee:id,name,lastname,image',
                'product:id,article_group,initial,image',
                'servicePhase:id,phase_section,product_id',
            ])
            ->whereIn('product_id', $departmentProductIds)
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'type' => 'product',
                'type_label' => 'Produktvorgang',
                'customer_id' => $item->customer_id,
                'title' => $this->productName($item->product) ?: ('Produktvorgang #' . $item->id),
                'status' => $item->status ?? $item->work_status ?? $item->stage,
                'status_label' => $this->statusLabel($item->status ?? $item->work_status ?? $item->stage, 'lead_product'),
                'customer' => $this->customerMini($item->customer),
                'object' => $this->objectMini($item->alternative),
                'employee' => $this->employeeMini($item->employee),
                'field_employee' => $this->employeeMini($item->fieldEmployee),
                'product' => $this->productName($item->product),
                'service' => $this->serviceName($item),
                'url' => $this->customerProfileUrl($item->customer_id),
                'action_label' => 'Kundenprofil öffnen',
                'date' => optional($item->created_at)->format('d.m.Y H:i'),
            ])
            ->values()
            ->all();
    }

    private function recentOffers(array $offerIds, Carbon $from, Carbon $to): array
    {
        return Offer::query()
            ->with([
                'customer:id,customer_no,firma,name,lastname,street,postcode,city,phone,telephone,email',
                'alternative:id,lead_id,object_name,address_no,street,postcode,city,status,stage',
                'creator:id,name,lastname,image',
                'assignee:id,name,lastname,image',
                'product:id,article_group,initial,image',
                'servicePhase:id,phase_section,product_id',
                'detail:id,offer_id,offer_folder_id,total_net,total_gross,document_status',
                'folders:id,offer_id,customer_id,alternative_id,product_id,name,status,document_status,offer_status,deal_status,created_at',
            ])
            ->whereIn('id', $offerIds)
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit(8)
            ->get()
            ->map(function ($offer) {
                $folder = $offer->folders->firstWhere('id', $offer->detail?->offer_folder_id)
                    ?: $offer->folders->sortByDesc('id')->first();

                $status = $folder?->workflow_status ?? $offer->status;

                return [
                    'id' => $offer->id,
                    'type' => 'offer',
                    'type_label' => 'Angebot',
                    'number' => $offer->offer_no,
                    'title' => $offer->offer_no ?: ('Angebot #' . $offer->id),
                    'status' => $status,
                    'status_label' => $this->statusLabel($status, 'offer'),
                    'customer' => $this->customerMini($offer->customer),
                    'object' => $this->objectMini($offer->alternative),
                    'product' => $this->productName($offer->product),
                    'service' => $this->phaseSectionNameFromModel($offer, 'servicePhase', 'service'),
                    'creator' => $this->employeeMini($offer->creator),
                    'assignee' => $this->employeeMini($offer->assignee),
                    'folder_id' => $folder?->id,
                    'folder_name' => $folder?->name,
                    'document_status' => $this->documentStatusLabel($offer->detail?->document_status ?? $folder?->document_status),
                    'total_net' => (float) ($offer->detail?->total_net ?? 0),
                    'total_gross' => (float) ($offer->detail?->total_gross ?? 0),
                    'url' => $folder?->id
                        ? url('/admin/offers/folders/' . $folder->id)
                        : url('/admin/offers') . '?' . http_build_query(['search' => $offer->offer_no ?: $offer->id]),
                    'action_label' => $folder?->id ? 'Angebotsordner öffnen' : 'Angebote öffnen',
                    'date' => optional($offer->created_at)->format('d.m.Y H:i'),
                ];
            })
            ->values()
            ->all();
    }

    private function recentDeals(array $dealIds, Carbon $from, Carbon $to): array
    {
        return Deal::query()
            ->with([
                'customer:id,customer_no,firma,name,lastname,street,postcode,city,phone,telephone,email',
                'alternative:id,lead_id,object_name,address_no,street,postcode,city,status,stage',
                'author:id,name,lastname,image',
                'checkedBy:id,name,lastname,image',
                'reviewer:id,name,lastname,image',
                'product:id,article_group,initial,image',
                'servicePhase:id,phase_section,product_id',
                'folder:id,name,status,document_status,offer_status,deal_status',
            ])
            ->whereIn('id', $dealIds)
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($deal) => [
                'id' => $deal->id,
                'type' => 'deal',
                'type_label' => 'Auftrag',
                'number' => $deal->order_number,
                'title' => $deal->order_number ?: ('Auftrag #' . $deal->id),
                'status' => $deal->deal_status ?? $deal->status ?? $deal->project_status,
                'status_label' => $this->statusLabel($deal->deal_status ?? $deal->status ?? $deal->project_status, 'deal'),
                'customer' => $this->customerMini($deal->customer),
                'object' => $this->objectMini($deal->alternative),
                'product' => $this->productName($deal->product),
                'service' => $this->phaseSectionNameFromModel($deal, 'servicePhase', 'service'),
                'employee' => $this->employeeMini($deal->author),
                'checked_by' => $this->employeeMini($deal->checkedBy),
                'reviewer' => $this->employeeMini($deal->reviewer),
                'folder_id' => $deal->offer_folder_id,
                'folder_name' => $deal->folder?->name,
                'price' => (float) ($deal->price ?? 0),
                'sign_date' => optional($deal->sign_date)->format('d.m.Y'),
                'url' => url('/deal/' . $deal->id . '/profile'),
                'action_label' => 'Auftragsprofil öffnen',
                'date' => optional($deal->created_at)->format('d.m.Y H:i'),
            ])
            ->values()
            ->all();
    }

    private function recentInvoices($invoiceQuery, Carbon $from, Carbon $to): array
    {
        return (clone $invoiceQuery)
            ->with([
                'customer:id,customer_no,firma,name,lastname,street,postcode,city,phone,telephone,email',
                'object:id,lead_id,object_name,address_no,street,postcode,city,status,stage',
                'creator:id,name,lastname,image',
                'offerDetail:id,offer_id,offer_folder_id,document_status,total_net,total_gross',
                'items:id,invoice_id,product_id,title,line_total',
                'items.product:id,article_group,initial,image',
            ])
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit(8)
            ->get()
            ->map(function ($invoice) {
                $firstItem = $invoice->items->first();

                return [
                    'id' => $invoice->id,
                    'type' => 'invoice',
                    'type_label' => 'Rechnung',
                    'number' => $invoice->invoice_no,
                    'title' => $invoice->invoice_no ?: ('Rechnung #' . $invoice->id),
                    'status' => $invoice->status,
                    'status_label' => $this->statusLabel($invoice->status, 'invoice'),
                    'customer' => $this->customerMini($invoice->customer),
                    'object' => $this->objectMini($invoice->object),
                    'product' => $this->productName($firstItem?->product),
                    'item_title' => $firstItem?->title,
                    'total' => (float) ($invoice->total_amount ?? 0),
                    'paid' => (float) ($invoice->paid_amount ?? 0),
                    'open' => (float) ($invoice->open_amount ?? max(($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0), 0)),
                    'issue_date' => optional($invoice->issue_date)->format('d.m.Y'),
                    'due_date' => optional($invoice->due_date)->format('d.m.Y'),
                    'creator' => $this->employeeMini($invoice->creator),
                    'url' => url('/admin/invoices') . '?' . http_build_query(['search' => $invoice->invoice_no ?: $invoice->id]),
                    'action_label' => 'Rechnung suchen',
                    'date' => optional($invoice->created_at)->format('d.m.Y H:i'),
                ];
            })
            ->values()
            ->all();
    }

    private function recentTickets(array $problemIds, Carbon $from, Carbon $to): array
    {
        return Problem::query()
            ->with([
                'customer:id,customer_no,firma,name,lastname,street,postcode,city,phone,telephone,email',
                'alternative:id,lead_id,object_name,address_no,street,postcode,city,status,stage',
                'responsibleEmployee:id,name,lastname,image',
                'firstContactEmployee:id,name,lastname,image',
                'startUser:id,name,lastname,image',
                'product:id,article_group,initial,image',
                'leadProductList:id,customer_id,alternative_id,product_id,status,work_status,stage',
            ])
            ->whereIn('id', $problemIds)
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($ticket) => [
                'id' => $ticket->id,
                'type' => 'ticket',
                'type_label' => 'Ticket',
                'number' => $ticket->ticket_no,
                'title' => $ticket->problem ?: ('Ticket #' . $ticket->id),
                'status' => $ticket->status,
                'status_label' => $this->statusLabel($ticket->status, 'ticket'),
                'priority' => $ticket->priority,
                'priority_label' => $this->priorityLabel($ticket->priority),
                'error_type' => $ticket->error_type,
                'source' => $ticket->source,
                'customer' => $this->customerMini($ticket->customer),
                'object' => $this->objectMini($ticket->alternative),
                'product' => $this->productName($ticket->product),
                'responsible' => $this->employeeMini($ticket->responsibleEmployee),
                'first_contact' => $this->employeeMini($ticket->firstContactEmployee),
                'created_by' => $this->employeeMini($ticket->startUser),
                'url' => url('/problem/profile/' . $ticket->id),
                'action_label' => 'Ticketprofil öffnen',
                'date' => optional($ticket->created_at)->format('d.m.Y H:i'),
            ])
            ->values()
            ->all();
    }

    private function recentAppointments(array $appointmentIds, Carbon $from, Carbon $to): array
    {
        return MainAppointment::query()
            ->with([
                'customer:id,customer_no,firma,name,lastname,street,postcode,city,phone,telephone,email',
                'createdBy:id,name,lastname,image',
                'employees:id,name,lastname,image',
                'leadProductList:id,customer_id,alternative_id,product_id,status,work_status,stage',
                'leadProductList.product:id,article_group,initial,image',
                'problem:id,ticket_no,problem',
                'task:id,task_title',
            ])
            ->whereIn('id', $appointmentIds)
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($appointment) => [
                'id' => $appointment->id,
                'type' => 'appointment',
                'type_label' => 'Termin',
                'number' => 'Termin #' . $appointment->id,
                'title' => $appointment->name ?: ('Termin #' . $appointment->id),
                'status' => $appointment->status,
                'status_label' => $this->statusLabel($appointment->status, 'appointment'),
                'appointment_type' => $appointment->appointment_type,
                'priority' => $appointment->priority,
                'priority_label' => $this->priorityLabel($appointment->priority),
                'customer' => $this->customerMini($appointment->customer),
                'product' => $this->productName($appointment->leadProductList?->product),
                'created_by' => $this->employeeMini($appointment->createdBy),
                'employees' => $appointment->employees->map(fn($e) => $this->employeeMini($e))->values(),
                'related_ticket' => $appointment->problem
                    ? (($appointment->problem->ticket_no ? $appointment->problem->ticket_no . ' · ' : '') . $appointment->problem->problem)
                    : null,
                'related_task' => $appointment->task?->task_title,
                'start' => trim((optional($appointment->start_date)->format('d.m.Y') ?? '') . ' ' . ($appointment->start_time ?? '')),
                'end' => trim((optional($appointment->end_date)->format('d.m.Y') ?? '') . ' ' . ($appointment->end_time ?? '')),
                'url' => url('/customer/appointments/' . $appointment->id),
                'action_label' => 'Termin öffnen',
                'date' => optional($appointment->created_at)->format('d.m.Y H:i'),
            ])
            ->values()
            ->all();
    }

    private function recentTasks(array $taskIds, Carbon $from, Carbon $to): array
    {
        return PersonalTask::query()
            ->with([
                'customer:id,customer_no,firma,name,lastname,street,postcode,city,phone,telephone,email',
                'alternative:id,lead_id,object_name,address_no,street,postcode,city,status,stage',
                'assignedBy:id,name,lastname,image',
                'employees:id,name,lastname,image',
                'product:id,article_group,initial,image',
                'leadProductList:id,customer_id,alternative_id,product_id,status,work_status,stage',
                'leadProductList.product:id,article_group,initial,image',
                'leadStage:id,name,key',
                'leadStageSubStage:id,name',
            ])
            ->whereIn('id', $taskIds)
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn($task) => [
                'id' => $task->id,
                'type' => 'task',
                'type_label' => 'Aufgabe',
                'number' => $task->task_id ?: ('Aufgabe #' . $task->id),
                'title' => $task->task_title ?: ('Aufgabe #' . $task->id),
                'status' => $task->task_status,
                'status_label' => $this->statusLabel($task->task_status, 'task'),
                'priority' => $task->priority,
                'priority_label' => $this->priorityLabel($task->priority),
                'progress' => (int) ($task->progress ?? 0),
                'customer' => $this->customerMini($task->customer),
                'object' => $this->objectMini($task->alternative),
                'product' => $this->productName($task->product ?: $task->leadProductList?->product),
                'assigned_by' => $this->employeeMini($task->assignedBy),
                'employees' => $task->employees->map(fn($e) => $this->employeeMini($e))->values(),
                'stage' => $task->leadStage?->name,
                'sub_stage' => $task->leadStageSubStage?->name,
                'due' => trim((optional($task->due_date)->format('d.m.Y') ?? '') . ' ' . ($task->due_time ?? '')),
                'url' => url('/personal-tasks/' . $task->id . '/profile'),
                'action_label' => 'Aufgabenprofil öffnen',
                'date' => optional($task->created_at)->format('d.m.Y H:i'),
            ])
            ->values()
            ->all();
    }

    private function recentChanges(
        array $customerIds,
        array $productListIds,
        array $offerIds,
        array $dealIds,
        array $problemIds,
        array $appointmentIds,
        array $taskIds,
        Carbon $from,
        Carbon $to
    ): array {
        $modelIdsByClass = [
            LeadProductList::class => $productListIds,
            Offer::class => $offerIds,
            Deal::class => $dealIds,
            Problem::class => $problemIds,
            MainAppointment::class => $appointmentIds,
            PersonalTask::class => $taskIds,
        ];

        $query = DB::table('lead_activity_logs')
            ->whereBetween('created_at', [$from, $to])
            ->where(function ($q) use ($customerIds, $modelIdsByClass) {
                if (!empty($customerIds)) {
                    $q->whereIn('new_leads_id', $customerIds);
                }

                foreach ($modelIdsByClass as $class => $ids) {
                    if (!empty($ids)) {
                        $q->orWhere(function ($sq) use ($class, $ids) {
                            $sq->where('model_type', $class)
                                ->whereIn('model_id', $ids);
                        });
                    }
                }
            })
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return $query->map(fn($log) => [
            'id' => $log->id,
            'event_type' => $log->event_type,
            'model_type' => class_basename($log->model_type),
            'model_id' => $log->model_id,
            'user_id' => $log->user_id,
            'user_name' => $log->user_name,
            'changes' => $this->decodeJson($log->changes),
            'date' => Carbon::parse($log->created_at)->format('d.m.Y H:i'),
        ])->values()->all();
    }

    private function workloadByEmployee(array $employeeIds, Carbon $from, Carbon $to): array
    {
        $employees = Employee::query()
            ->select('id', 'name', 'lastname', 'image')
            ->whereIn('id', $employeeIds)
            ->get();

        return [
            'labels' => $employees
                ->map(fn($e) => trim(($e->name ?? '') . ' ' . ($e->lastname ?? '')))
                ->values(),

            'tasks' => $employees->map(function ($employee) use ($from, $to) {
                return DB::table('employees_personal_tasks')
                    ->join('personal_tasks', 'personal_tasks.id', '=', 'employees_personal_tasks.task_id')
                    ->where('employees_personal_tasks.employee_id', $employee->id)
                    ->whereNull('personal_tasks.deleted_at')
                    ->whereBetween('personal_tasks.created_at', [$from, $to])
                    ->count();
            })->values(),

            'appointments' => $employees->map(function ($employee) use ($from, $to) {
                return DB::table('main_appointment_employees')
                    ->join('main_appointments', 'main_appointments.id', '=', 'main_appointment_employees.appointment_id')
                    ->where('main_appointment_employees.employee_id', $employee->id)
                    ->whereNull('main_appointments.deleted_at')
                    ->whereBetween('main_appointments.created_at', [$from, $to])
                    ->count();
            })->values(),

            'tickets' => $employees->map(function ($employee) use ($from, $to) {
                return Problem::query()
                    ->where(function ($q) use ($employee) {
                        $q->where('responsible', $employee->id)
                            ->orWhere('start_user', $employee->id)
                            ->orWhere('progress_user', $employee->id)
                            ->orWhere('end_user', $employee->id)
                            ->orWhereHas('employees', function ($sq) use ($employee) {
                                $sq->where('employees.id', $employee->id);
                            });
                    })
                    ->whereBetween('created_at', [$from, $to])
                    ->count();
            })->values(),
        ];
    }

    private function invoiceByStatus($invoiceQuery, Carbon $from, Carbon $to): array
    {
        $rows = (clone $invoiceQuery)
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

    private function teamByPosition(int $departmentId): array
    {
        $rows = DB::table('department_positions')
            ->leftJoin('positions', 'positions.id', '=', 'department_positions.position_id')
            ->where('department_positions.department_id', $departmentId)
            ->selectRaw('COALESCE(positions.position, "Ohne Position") as position_name, COUNT(DISTINCT department_positions.employee_id) as total')
            ->groupBy('position_name')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $rows->pluck('position_name')->values(),
            'values' => $rows->pluck('total')->map(fn($v) => (int) $v)->values(),
        ];
    }

    private function leadPipeline(int $departmentId, Carbon $from, Carbon $to): array
    {
        $rows = LeadProductList::query()
            ->where('department_id', $departmentId)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('COALESCE(stage, status, work_status, "Unbekannt") as label, COUNT(DISTINCT customer_id) as total')
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $rows->pluck('label')->values(),
            'values' => $rows->pluck('total')->map(fn($v) => (int) $v)->values(),
        ];
    }

    private function productsByGroup(int $departmentId, Carbon $from, Carbon $to): array
    {
        $rows = LeadProductList::query()
            ->leftJoin('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->where('lead_product_lists.department_id', $departmentId)
            ->whereBetween('lead_product_lists.created_at', [$from, $to])
            ->selectRaw('COALESCE(article_groups.article_group, CONCAT("Produkt #", lead_product_lists.product_id)) as label, COUNT(*) as total')
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $rows->pluck('label')->values(),
            'values' => $rows->pluck('total')->map(fn($v) => (int) $v)->values(),
        ];
    }

    private function objectsByStatus(array $alternativeIds): array
    {
        if (empty($alternativeIds)) {
            return [
                'labels' => [],
                'values' => [],
            ];
        }

        $rows = LeadAlternativeAdd::query()
            ->whereIn('id', $alternativeIds)
            ->selectRaw('COALESCE(status, stage, "Unbekannt") as label, COUNT(*) as total')
            ->groupBy('label')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $rows->pluck('label')->values(),
            'values' => $rows->pluck('total')->map(fn($v) => (int) $v)->values(),
        ];
    }

    private function team(Department $department): array
    {
        return $department->employees
            ->unique('id')
            ->map(function ($employee) use ($department) {
                $pivot = $employee->pivot;
                $departmentPosition = $department->departmentPositions
                    ->firstWhere('employee_id', $employee->id);

                return [
                    'id' => $employee->id,
                    'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    'image_url' => $this->employeeImageUrl($employee),
                    'position_id' => $pivot?->position_id,
                    'position' => $departmentPosition?->position?->position,
                    'qualification' => $departmentPosition?->position?->qualificationRef?->name,
                    'percent' => (int) ($pivot?->percent ?? 0),
                    'montage_percent' => (int) ($pivot?->montage_percent ?? 0),
                    'office_percent' => (int) ($pivot?->office_percent ?? 0),
                    'working_hours' => (float) ($pivot?->working_hours ?? 0),
                    'main' => (bool) ($pivot?->main ?? false),
                ];
            })
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

    private function defaultDepartmentId(int $employeeId): ?int
    {
        return DB::table('department_positions')
            ->where('employee_id', $employeeId)
            ->orderByDesc('main')
            ->orderByDesc('percent')
            ->orderBy('department_id')
            ->value('department_id');
    }

    private function orWhereColumnValue($query, string $table, string $column, array $values): void
    {
        $values = collect($values)->filter()->unique()->values()->all();

        if (empty($values)) {
            return;
        }

        if (!Schema::hasColumn($table, $column)) {
            return;
        }

        $query->orWhereIn($column, $values);
    }

    private function serviceName(LeadProductList $item): ?string
    {
        $phaseSection = null;

        if ($item->relationLoaded('service')) {
            $relation = $item->getRelation('service');

            if ($relation instanceof PhaseSection) {
                $phaseSection = $relation;
            }
        }

        return $phaseSection?->german_name
            ?? $phaseSection?->phase_section
            ?? $item->getAttribute('service')
            ?? null;
    }

    private function customerMini(?NewLeads $customer): ?array
    {
        if (!$customer) {
            return null;
        }

        return [
            'id' => $customer->id,
            'number' => $customer->customer_no,
            'name' => $customer->display_name,
            'email' => $customer->email,
            'phone' => $customer->phone ?: $customer->telephone,
            'city' => $customer->city,
            'address' => $this->customerAddress($customer),
            'url' => $this->customerProfileUrl($customer->id),
        ];
    }

    private function objectMini(?LeadAlternativeAdd $object): ?array
    {
        if (!$object) {
            return null;
        }

        return [
            'id' => $object->id,
            'name' => $object->display_name,
            'address_no' => $object->address_no,
            'address' => $this->objectAddress($object),
            'status' => $object->status ?? $object->stage,
            'status_label' => $this->statusLabel($object->status ?? $object->stage, 'object'),
        ];
    }

    private function customerAddress(?NewLeads $customer): ?string
    {
        if (!$customer) {
            return null;
        }

        $address = trim(implode(' ', array_filter([
            trim((string) ($customer->street ?? '')),
            trim((string) ($customer->postcode ?? '')),
            trim((string) ($customer->city ?? '')),
        ])));

        return $address !== '' ? $address : null;
    }

    private function objectAddress(?LeadAlternativeAdd $object): ?string
    {
        if (!$object) {
            return null;
        }

        $address = trim(implode(' ', array_filter([
            trim((string) ($object->street ?? '')),
            trim((string) ($object->postcode ?? '')),
            trim((string) ($object->city ?? '')),
        ])));

        return $address !== '' ? $address : null;
    }

    private function customerProfileUrl(?int $customerId): ?string
    {
        if (!$customerId) {
            return null;
        }

        return url('/customer/profile/' . $customerId);
    }

    private function documentStatusLabel(?string $value): string
    {
        return match (strtolower((string) $value)) {
            'deal', 'auftrag' => 'Auftrag',
            'offer' => 'Angebot',
            default => 'Angebot',
        };
    }

    private function priorityLabel(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        return match (strtolower(trim($value))) {
            'low', 'niedrig' => 'Niedrig',
            'normal', 'medium', 'mittel' => 'Mittel',
            'high', 'hoch' => 'Hoch',
            'urgent', 'critical', 'kritisch', 'sehr hoch' => 'Sehr hoch',
            default => ucfirst(str_replace('_', ' ', $value)),
        };
    }

    private function statusLabel(?string $value, string $type = ''): string
    {
        $raw = trim((string) $value);

        if ($raw === '') {
            return 'Status offen';
        }

        $key = strtolower($raw);

        $map = [
            'published' => 'Veröffentlicht',
            'draft' => 'Entwurf',
            'pending_approval' => 'Wartet auf Freigabe',
            'sent' => 'Gesendet',
            'viewed' => 'Gesehen',
            'negotiation' => 'Verhandlung',
            'revised' => 'Überarbeitet',
            'accepted' => 'Akzeptiert',
            'rejected' => 'Abgelehnt',
            'expired' => 'Abgelaufen',
            'cancelled' => 'Storniert',
            'cancel' => 'Storniert',

            'open' => 'Offen',
            'qualified' => 'Qualifiziert',
            'proposal' => 'Angebotsphase',
            'won' => 'Gewonnen',
            'lost' => 'Verloren',
            'on_hold' => 'Pausiert',

            'paid' => 'Bezahlt',
            'overdue' => 'Überfällig',
            'partly_paid' => 'Teilweise bezahlt',

            'completed' => 'Abgeschlossen',
            'complete' => 'Abgeschlossen',
            'done' => 'Erledigt',
            'in_progress' => 'In Bearbeitung',
            'progress' => 'In Bearbeitung',
            'pause' => 'Pausiert',
            'paused' => 'Pausiert',
            'junk' => 'Aussortiert',

            'new' => 'Neu',
            'planned' => 'Geplant',
            'confirmed' => 'Bestätigt',
            'not_confirmed' => 'Nicht bestätigt',
        ];

        return $map[$key] ?? ucfirst(str_replace('_', ' ', $raw));
    }

    private function decodeJson($value): mixed
    {
        if (!$value) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        return json_decode($value, true);
    }

    private function employeeMini(?Employee $employee): ?array
    {
        if (!$employee) {
            return null;
        }

        return [
            'id' => $employee->id,
            'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
            'image_url' => $this->employeeImageUrl($employee),
        ];
    }

    private function employeeImageUrl(?Employee $employee): string
    {
        if (!$employee || empty($employee->image)) {
            return asset('images/gender/male.png');
        }

        return asset('images/employee/' . $employee->image);
    }

    private function productName($product): ?string
    {
        if (!$product) {
            return null;
        }

        return $product->article_group
            ?? $product->article_group_name
            ?? $product->name
            ?? $product->title
            ?? $product->article_name
            ?? ('Produkt #' . $product->id);
    }

    private function phaseSectionNameFromModel($model, string $relationName = 'servicePhase', ?string $fallbackAttribute = null): ?string
    {
        if (!$model) {
            return null;
        }

        $phaseSection = null;

        if ($model instanceof PhaseSection) {
            $phaseSection = $model;
        }

        if (
            !$phaseSection
            && is_object($model)
            && method_exists($model, 'relationLoaded')
            && $model->relationLoaded($relationName)
        ) {
            $loadedRelation = $model->getRelation($relationName);

            if ($loadedRelation instanceof PhaseSection) {
                $phaseSection = $loadedRelation;
            }
        }

        if ($phaseSection instanceof PhaseSection) {
            $raw = $phaseSection->getAttribute('phase_section');

            return $phaseSection->german_name
                ?? $this->translatePhaseSectionName($raw)
                ?? $raw;
        }

        if ($fallbackAttribute && is_object($model) && method_exists($model, 'getAttribute')) {
            $fallbackValue = $model->getAttribute($fallbackAttribute);

            if (is_string($fallbackValue) && trim($fallbackValue) !== '') {
                return $this->translatePhaseSectionName($fallbackValue);
            }
        }

        return null;
    }

    private function translatePhaseSectionName(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return match (strtolower($value)) {
            'complete' => 'Komplett',
            'maintanence', 'maintenance' => 'Wartung',
            'montage' => 'Montage',
            'product' => 'Produkt',
            'plan' => 'Planung',
            'repair' => 'Reparatur',
            'reclaim' => 'Reklamation',
            'others' => 'Sonstiges',
            default => ucfirst($value),
        };
    }
}