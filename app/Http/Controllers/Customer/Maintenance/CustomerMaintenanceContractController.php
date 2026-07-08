<?php

namespace App\Http\Controllers\Customer\Maintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerMaintenanceContract;
use App\Models\NewLeads;              // falls dein Lead-Model anders heißt -> anpassen 
use App\Models\Asset;                // dein Anlagen- / Asset-Model (ggf. anpassen)
use App\Models\Employee;             // Mitarbeiter-Model (für Monteure)
use Illuminate\Support\Carbon;
use App\Models\LeadProductList;
use App\Models\LeadAlternativeAdd;
use App\Models\ArticleGroup;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\BrandDepartment;
use Illuminate\Support\Facades\Log;
use App\Models\MaintenanceChecklist;
use App\Models\MaintenanceProtocol;
use DB;
use App\Models\MaintenanceAsset;
use App\Models\MaintenanceProtocolTechnician;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class CustomerMaintenanceContractController extends Controller
{

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status');
        $interval = $request->input('interval_type');
        $sort = $request->input('sort', 'next_service_asc');

        $baseQuery = CustomerMaintenanceContract::query()
            ->with([
                'lead:id,name,lastname,firma,customer_no,street,postcode,city',
                'alternative:id,full_address,street,postcode,city',
                'asset:id,title,manufacturer,model,technical_data',
                'responsibleEmployee:id,name,image,status',
            ]);

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('contract_no', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('lead', function ($q2) use ($search) {
                        $q2->where('firma', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('customer_no', 'like', "%{$search}%")
                            ->orWhere('street', 'like', "%{$search}%")
                            ->orWhere('postcode', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%");
                    })
                    ->orWhereHas('alternative', function ($q2) use ($search) {
                        $q2->where('full_address', 'like', "%{$search}%")
                            ->orWhere('street', 'like', "%{$search}%")
                            ->orWhere('postcode', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%");
                    })
                    ->orWhereHas('asset', function ($q2) use ($search) {
                        $q2->where('title', 'like', "%{$search}%")
                            ->orWhere('manufacturer', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%");
                    })
                    ->orWhereHas('responsibleEmployee', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status) {
            $baseQuery->where('status', $status);
        }

        if ($interval) {
            $baseQuery->where('interval_type', $interval);
        }

        $statsItems = (clone $baseQuery)->get(['id', 'status', 'next_service_date', 'end_date']);

        $stats = [
            'total' => $statsItems->count(),
            'active' => $statsItems->where('status', 'active')->count(),
            'draft' => $statsItems->where('status', 'draft')->count(),
            'inactive' => $statsItems->whereIn('status', ['inactive', 'cancelled'])->count(),
            'upcoming' => $statsItems->filter(function ($contract) {
                $date = $contract->next_service_date ?: $contract->end_date;
                if (!$date) {
                    return false;
                }

                try {
                    return Carbon::parse($date)->between(
                        now()->startOfDay(),
                        now()->copy()->addDays(30)->endOfDay()
                    );
                } catch (\Throwable $e) {
                    return false;
                }
            })->count(),
        ];

        switch ($sort) {
            case 'created_desc':
                $baseQuery->orderByDesc('created_at');
                break;
            case 'created_asc':
                $baseQuery->orderBy('created_at');
                break;
            case 'start_desc':
                $baseQuery->orderByDesc('start_date')->orderBy('contract_no');
                break;
            case 'start_asc':
                $baseQuery->orderBy('start_date')->orderBy('contract_no');
                break;
            case 'next_service_desc':
                $baseQuery->orderByRaw('next_service_date IS NULL ASC')
                    ->orderByDesc('next_service_date')
                    ->orderBy('contract_no');
                break;
            case 'next_service_asc':
            default:
                $baseQuery->orderByRaw('next_service_date IS NULL ASC')
                    ->orderBy('next_service_date')
                    ->orderBy('contract_no');
                break;
        }

        $contracts = $baseQuery->paginate(20)->appends($request->query());

        return view('admin.maintenance.contracts.index', [
            'contracts' => $contracts,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'intervalType' => $interval,
                'sort' => $sort,
            ],
        ]);
    }

    public function create(Request $request)
    {
        // Basis-Setup für Historie
        $history = [
            'assetAgeText' => null,
            'totalMaintenanceCount' => null,
            'lastMaintenanceDate' => null,
            'averageIntervalDays' => null,
            'entries' => [],
        ];

        // Lead inkl. Wartungshistorie laden (falls lead_id übergeben wurde)
        $lead = null;
        $leadId = $request->integer('lead_id');

        if ($leadId) {
            $lead = NewLeads::with([
                'maintenanceAsset',
                'maintenanceProtocol',
                'mainAddress',
                'addresses',
            ])->find($leadId);

            if ($lead) {
                $history = $this->buildHistoryForLead($lead);
            }
        }

        // Alternative Adresse:
        // 1) explizit über ?alternative_id
        // 2) sonst mainAddress des Leads
        // 3) sonst erste alternative Adresse
        $alternative = null;

        if ($request->filled('alternative_id')) {
            $alternative = LeadAlternativeAdd::find($request->integer('alternative_id'));
        } elseif ($lead) {
            $alternative = $lead->mainAddress ?: $lead->addresses->first();
        }

        // Aktuell kein Asset direkt zugeordnet
        $asset = null;

        // Hersteller (Brand) – rechte Karte in Step 1
        $brand = Brand::first();

        // Mitarbeiter für Auswahl (inkl. Department / Position)
        $employees = Employee::with([
            'departmentPositions.department',
            'departmentPositions.position',
        ])
            ->orderBy('name')
            ->get();

        Log::info('Maintenance wizard: raw employees fetched', [
            'count' => $employees->count(),
            'ids' => $employees->pluck('id')->all(),
        ]);

        // Debug: what will be "shown" in the UI (resolved name / dept / position)
        $employeesDebug = $employees->map(function ($employee) {
            $displayName = $employee->name
                ?? $employee->name
                ?? ('Mitarbeiter #' . $employee->id);

            $deptName = null;
            $posName = null;

            $dpCollection = $employee->departmentPositions ?? collect();

            if ($dpCollection instanceof \Illuminate\Support\Collection && $dpCollection->isNotEmpty()) {
                $mainDp = $dpCollection->firstWhere('main', 'yes')
                    ?? $dpCollection->firstWhere('main', '1')
                    ?? $dpCollection->firstWhere('main', 1)
                    ?? $dpCollection->first();

                if ($mainDp) {
                    $deptModel = $mainDp->department ?? null;
                    $posModel = $mainDp->position ?? null;

                    if ($deptModel) {
                        $deptName = $deptModel->department_name
                            ?? $deptModel->name
                            ?? null;
                    }

                    if ($posModel) {
                        $posName = $posModel->position
                            ?? $posModel->name
                            ?? null;
                    }
                }
            }

            // Fallbacks nur auf Spalten
            $deptName = $deptName
                ?? $employee->department_name
                ?? $employee->department_label
                ?? null;

            $posName = $posName
                ?? $employee->position_title
                ?? $employee->position_name
                ?? null;

            return [
                'id' => $employee->id,
                'db_name' => $employee->name,
                'db_full_name' => $employee->name,
                'display_name' => $displayName,
                'resolved_department' => $deptName,
                'resolved_position' => $posName,
                'dp_count' => $dpCollection instanceof \Illuminate\Support\Collection
                    ? $dpCollection->count()
                    : null,
            ];
        });

        Log::debug('Maintenance wizard: employees as shown in UI', [
            'employees' => $employeesDebug->take(50)->toArray(), // limit to avoid huge logs
        ]);

        return view('admin.maintenance.wizard', [
            'lead' => $lead,
            'alternative' => $alternative,
            'asset' => $asset,
            'manufacturer' => $brand,
            'employees' => $employees,
            'history' => $history,
        ]);
    }

    public function technicians(Request $request)
    {
        // optional: Filter, z. B. nur aktive Mitarbeiter
        $employees = Employee::with(['departmentPositions.department', 'departmentPositions.position'])
            ->where('employees.status', 'Active')
            ->orderBy('name')
            ->get();

        $payload = $employees->map(function (Employee $employee) {
            $displayName = $employee->name
                ?? $employee->name
                ?? ('Mitarbeiter #' . $employee->id);

            $deptName = null;
            $posName = null;

            $dpCollection = $employee->departmentPositions ?? collect();

            if ($dpCollection->isNotEmpty()) {
                $mainDp = $dpCollection->firstWhere('main', 'yes')
                    ?? $dpCollection->firstWhere('main', '1')
                    ?? $dpCollection->firstWhere('main', 1)
                    ?? $dpCollection->first();

                if ($mainDp) {
                    $deptModel = $mainDp->department ?? null;
                    $posModel = $mainDp->position ?? null;

                    if ($deptModel) {
                        $deptName = $deptModel->department_name
                            ?? $deptModel->name
                            ?? null;
                    }

                    if ($posModel) {
                        $posName = $posModel->position
                            ?? $posModel->name
                            ?? null;
                    }
                }
            }

            // Fallbacks nur auf Spalten
            $deptName = $deptName
                ?? $employee->department_name
                ?? $employee->department_label
                ?? null;

            $posName = $posName
                ?? $employee->position_title
                ?? $employee->position_name
                ?? null;

            $metaLine = trim(
                trim($deptName ?? '') .
                (($deptName && $posName) ? ' · ' : '') .
                trim($posName ?? '')
            );

            return [
                'id' => $employee->id,
                'name' => $displayName,
                'department' => $deptName,
                'position' => $posName,
                'meta' => $metaLine,
                'avatar_url' => $employee->image
                    ? asset('images/employee/' . $employee->image)
                    : null,
            ];
        })->values();

        \Log::info('Maintenance wizard: technicians endpoint', [
            'count' => $payload->count(),
            'ids' => $payload->pluck('id')->all(),
        ]);

        return response()->json([
            'employees' => $payload,
        ]);
    }

    /**
     * Baut die Kennzahlen-Historie für eine Anlage auf Basis der Wartungsdaten
     * des Kunden (Lead).
     */
    protected function buildHistoryForLead(?NewLeads $lead): array
    {
        if (!$lead) {
            return [
                'assetAgeText' => null,
                'totalMaintenanceCount' => null,
                'lastMaintenanceDate' => null,
                'averageIntervalDays' => null,
                'entries' => [],
            ];
        }

        // Wartungs-Assets & Protokolle aus den Relationen des Leads
        $assets = $lead->maintenanceAsset ?? collect();
        $protocols = $lead->maintenanceProtocol ?? collect();

        // Installationsdatum: frühestes installation_date aus Assets (falls vorhanden)
        $installedAt = null;

        if ($assets->isNotEmpty()) {
            $installedAt = $assets
                ->pluck('installation_date')
                ->filter()
                ->map(function ($date) {
                    try {
                        return Carbon::parse($date);
                    } catch (\Throwable $e) {
                        return null;
                    }
                })
                ->filter()
                ->sort()
                ->first() ?: null;
        }

        $assetAgeText = $installedAt
            ? $installedAt->diffForHumans(now(), ['parts' => 2, 'short' => false])
            : null;

        // Gesamtanzahl Wartungen (Protokolle)
        $totalMaintenanceCount = $protocols->count() ?: null;

        // Letzte Wartung = Protokoll mit neuestem maintenance_date
        $lastMaintenanceDate = null;

        if ($protocols->isNotEmpty()) {
            $lastProtocol = $protocols
                ->filter(function ($protocol) {
                    return !empty($protocol->maintenance_date);
                })
                ->sortByDesc('maintenance_date')
                ->first();

            if ($lastProtocol && $lastProtocol->maintenance_date) {
                try {
                    $lastMaintenanceDate = Carbon::parse($lastProtocol->maintenance_date)->format('d.m.Y');
                } catch (\Throwable $e) {
                    $lastMaintenanceDate = null;
                }
            }
        }

        // Ø Wartungsintervall in Tagen: Differenzen zwischen den maintenance_date-Werten
        $averageIntervalDays = null;

        $dateCollection = $protocols
            ->pluck('maintenance_date')
            ->filter()
            ->map(function ($date) {
                try {
                    return Carbon::parse($date);
                } catch (\Throwable $e) {
                    return null;
                }
            })
            ->filter()
            ->sort()
            ->values();

        if ($dateCollection->count() >= 2) {
            $diffs = [];

            for ($i = 1; $i < $dateCollection->count(); $i++) {
                $diffs[] = $dateCollection[$i - 1]->diffInDays($dateCollection[$i]);
            }

            if (!empty($diffs)) {
                $averageIntervalDays = (int) round(array_sum($diffs) / count($diffs));
            }
        }

        // Letzte Wartungen (z. B. letzte 5 Protokolle, sortiert nach Datum)
        $entries = $protocols
            ->sortByDesc('maintenance_date')
            ->take(5)
            ->map(function ($protocol) {
                $date = null;

                if (!empty($protocol->maintenance_date)) {
                    try {
                        $date = Carbon::parse($protocol->maintenance_date)->format('d.m.Y');
                    } catch (\Throwable $e) {
                        $date = $protocol->maintenance_date;
                    }
                }

                return [
                    'date' => $date,
                    'type_label' => $protocol->type_label ?? $protocol->type ?? 'Wartung',
                    'technician_name' => $protocol->technician_name ?? null,
                    'notes' => $protocol->notes ?? $protocol->short_description ?? null,
                ];
            })
            ->values()
            ->all();

        return [
            'assetAgeText' => $assetAgeText,
            'totalMaintenanceCount' => $totalMaintenanceCount,
            'lastMaintenanceDate' => $lastMaintenanceDate,
            'averageIntervalDays' => $averageIntervalDays,
            'entries' => $entries,
        ];
    }


    public function brandSearch(Request $request)
    {
        $term = trim($request->get('q', ''));

        if ($term === '') {
            return response()->json(['results' => []]);
        }

        $brands = Brand::query()
            ->with([
                'departments' => function ($q) {
                    $q->orderBy('status', 'desc')->orderBy('id');
                }
            ])
            ->where('name', 'like', "%{$term}%")
            ->orWhere('purpose', 'like', "%{$term}%")
            ->orWhere('address', 'like', "%{$term}%")
            ->limit(25)
            ->get();

        $results = [];

        foreach ($brands as $brand) {
            $dept = $brand->departments->first();   // first responsible person, optional

            $results[] = [
                'id' => $brand->id,
                'text' => $brand->name, // Select2 fallback

                'brand_id' => $brand->id,
                'name' => $brand->name,
                'initial' => $brand->initial,
                'purpose' => $brand->purpose,
                'image' => $brand->image,
                'address' => $brand->address,

                // contact from brand_departments
                'contact_name' => $dept->name ?? null,
                'contact_position' => $dept->position ?? null,
                'contact_phone' => $dept->phone
                    ?? $dept->office
                    ?? $dept->home
                    ?? null,
                'contact_email' => $dept->email ?? null,
            ];
        }

        return response()->json(['results' => $results]);
    }


    public function store(Request $request)
    {
        Log::info('MaintenanceWizard.store: incoming request', [
            'user_id' => optional($request->user())->id,
            'keys' => array_keys($request->all()),
        ]);

        $validated = $request->validate([
            // Select2 liefert meistens customer_id (Lead-ID). lead_id bleibt optional.
            'lead_id' => ['nullable', 'integer'],
            'customer_id' => ['nullable', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'asset_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'lead_product_list_id' => ['nullable', 'integer'],
            'wizard_payload' => ['required', 'string'],
        ]);

        $payload = json_decode($validated['wizard_payload'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
            return back()->withInput()->withErrors([
                'wizard_payload' => 'Wizard-Payload ist kein gültiges JSON.',
            ]);
        }

        $context = $payload['context'] ?? [];

        // ✅ FIX: LeadId aus lead_id ODER customer_id ODER context ziehen
        $leadId = $validated['lead_id']
            ?? ($context['leadId'] ?? null)
            ?? ($validated['customer_id'] ?? null)
            ?? ($context['customerId'] ?? null);

        $altId = $validated['alternative_id']
            ?? ($context['alternativeId'] ?? null)
            ?? ($context['siteId'] ?? null);

        $assetId = $validated['asset_id'] ?? ($context['assetId'] ?? null);

        $productId = $validated['product_id']
            ?? ($context['productId'] ?? null);

        $leadProductListId = $validated['lead_product_list_id']
            ?? ($context['leadProductListId'] ?? null);

        // ✅ Hart prüfen: Kunde muss existieren (sonst FK/NOT NULL Fehler)
        if (!$leadId || !NewLeads::whereKey($leadId)->exists()) {
            return back()->withInput()->withErrors([
                'wizard_payload' => 'Bitte zuerst einen gültigen Kunden auswählen.',
            ]);
        }

        if ($altId && !LeadAlternativeAdd::whereKey($altId)->exists()) {
            $altId = null;
        }

        $installation = $payload['installation'] ?? [];
        $manufacturer = $payload['manufacturer'] ?? [];
        $history = $payload['history'] ?? [];
        $maintenance = $payload['maintenanceCurrent'] ?? ($payload['maintenance'] ?? []);
        $checklist = $payload['checklist'] ?? [];
        $contract = $payload['contract'] ?? [];
        $summary = $payload['summary'] ?? [];
        $system = $payload['system'] ?? [];
        $safety = $payload['safety'] ?? [];
        $measurements = $payload['measurements'] ?? [];
        $followUps = $payload['followUps'] ?? [];
        $signatures = $payload['signatures'] ?? [];
        $attachments = $payload['attachments'] ?? [];

        $firstChecklistId = null;
        if (!empty($checklist) && isset($checklist[0]['checklistId'])) {
            $firstChecklistId = (int) $checklist[0]['checklistId'];
        }

        $userId = optional($request->user())->id;

        // Responsible (optional) – aus mainTechnician ableiten
        $responsibleEmployeeId = null;
        $mainTech = $maintenance['mainTechnician'] ?? null;
        if (is_array($mainTech) && !empty($mainTech['employeeId'])) {
            $tmp = (int) $mainTech['employeeId'];
            if ($tmp > 0 && Employee::whereKey($tmp)->exists()) {
                $responsibleEmployeeId = $tmp;
            }
        }

        try {
            $result = DB::transaction(function () use ($leadId, $altId, $assetId, $productId, $leadProductListId, $installation, $manufacturer, $history, $maintenance, $checklist, $contract, $summary, $system, $safety, $measurements, $followUps, $signatures, $attachments, $payload, $firstChecklistId, $userId, $responsibleEmployeeId) {
                // -------------------------
                // 1) ASSET
                // -------------------------
                $asset = $assetId ? (MaintenanceAsset::find($assetId) ?: new MaintenanceAsset()) : new MaintenanceAsset();

                if (Schema::hasColumn('maintenance_assets', 'lead_id'))
                    $asset->lead_id = $leadId;
                if (Schema::hasColumn('maintenance_assets', 'alternative_id'))
                    $asset->alternative_id = $altId;
                if (Schema::hasColumn('maintenance_assets', 'product_id'))
                    $asset->product_id = $productId;
                if (Schema::hasColumn('maintenance_assets', 'lead_product_list_id'))
                    $asset->lead_product_list_id = $leadProductListId;

                $serials = $installation['serialNumbers'] ?? [];
                if (is_string($serials))
                    $serials = array_filter(array_map('trim', explode(',', $serials)));
                $firstSerial = is_array($serials) ? (reset($serials) ?: null) : null;

                $asset->title =
                    $installation['systemTypeLabel']
                    ?? $installation['productModel']
                    ?? 'Anlage';

                if (Schema::hasColumn('maintenance_assets', 'serial_no'))
                    $asset->serial_no = $firstSerial;
                if (Schema::hasColumn('maintenance_assets', 'manufacturer'))
                    $asset->manufacturer = $manufacturer['name'] ?? null;
                if (Schema::hasColumn('maintenance_assets', 'model'))
                    $asset->model = $installation['productModel'] ?? null;

                if (Schema::hasColumn('maintenance_assets', 'installation_date')) {
                    $asset->installation_date = $this->convertDateOrNull($installation['installationDate'] ?? null);
                }

                $location = $installation['installationLocation'] ?? [];
                if (Schema::hasColumn('maintenance_assets', 'location_name')) {
                    $asset->location_name = is_array($location) ? ($location['roomOrArea'] ?? null) : null;
                }
                if (Schema::hasColumn('maintenance_assets', 'location_note')) {
                    $asset->location_note = is_array($location) ? ($location['notes'] ?? null) : null;
                }

                if ($firstChecklistId && Schema::hasColumn('maintenance_assets', 'maintenance_checklist_id')) {
                    $asset->maintenance_checklist_id = $firstChecklistId;
                }

                if (Schema::hasColumn('maintenance_assets', 'responsible_employee_id') && $responsibleEmployeeId) {
                    $asset->responsible_employee_id = $responsibleEmployeeId;
                }

                if (Schema::hasColumn('maintenance_assets', 'technical_data'))
                    $asset->technical_data = $installation ?: null;
                if (Schema::hasColumn('maintenance_assets', 'meta')) {
                    $asset->meta = [
                        'history' => $history ?: null,
                        'safety' => $safety ?: null,
                        'measurements' => $measurements ?: null,
                    ];
                }

                if (Schema::hasColumn('maintenance_assets', 'updated_by'))
                    $asset->updated_by = $userId;

                $asset->save();

                if (Schema::hasColumn('maintenance_assets', 'asset_no') && empty($asset->asset_no)) {
                    $asset->asset_no = 'AS-' . str_pad((string) $asset->id, 6, '0', STR_PAD_LEFT);
                    $asset->save();
                }

                // -------------------------
                // 2) CONTRACT
                // -------------------------
                $contractModel = new CustomerMaintenanceContract();

                if (Schema::hasColumn('customer_maintenance_contracts', 'lead_id'))
                    $contractModel->lead_id = $leadId;
                if (Schema::hasColumn('customer_maintenance_contracts', 'alternative_id'))
                    $contractModel->alternative_id = $altId;
                if (Schema::hasColumn('customer_maintenance_contracts', 'asset_id'))
                    $contractModel->asset_id = $asset->id;

                if (Schema::hasColumn('customer_maintenance_contracts', 'responsible_employee_id') && $responsibleEmployeeId) {
                    $contractModel->responsible_employee_id = $responsibleEmployeeId;
                }

                $rawContractNo = $contract['contractNumber'] ?? null;
                $contractNo = (is_string($rawContractNo) && trim($rawContractNo) !== '') ? trim($rawContractNo) : null;

                if (Schema::hasColumn('customer_maintenance_contracts', 'contract_no'))
                    $contractModel->contract_no = $contractNo;
                if (Schema::hasColumn('customer_maintenance_contracts', 'title'))
                    $contractModel->title = $asset->title;
                if (Schema::hasColumn('customer_maintenance_contracts', 'contract_type'))
                    $contractModel->contract_type = $contract['type'] ?? null;
                if (Schema::hasColumn('customer_maintenance_contracts', 'billing_mode'))
                    $contractModel->billing_mode = $contract['billingMode'] ?? null;

                if (Schema::hasColumn('customer_maintenance_contracts', 'start_date')) {
                    $contractModel->start_date = $this->convertDateOrNull($contract['startDate'] ?? null);
                }
                if (Schema::hasColumn('customer_maintenance_contracts', 'end_date')) {
                    $contractModel->end_date = $this->convertDateOrNull($contract['endDate'] ?? null);
                }
                if (Schema::hasColumn('customer_maintenance_contracts', 'next_service_date')) {
                    $contractModel->next_service_date = $this->convertDateOrNull(
                        $summary['nextServiceDateRecommended']
                        ?? $contract['nextScheduledServiceDate']
                        ?? null
                    );
                }

                $intervalMonths = $summary['recommendedIntervalMonths'] ?? null;
                if (Schema::hasColumn('customer_maintenance_contracts', 'interval_type')) {
                    $contractModel->interval_type = $intervalMonths ? 'custom' : 'yearly';
                }
                if (Schema::hasColumn('customer_maintenance_contracts', 'interval_months')) {
                    $contractModel->interval_months = $intervalMonths;
                }

                $overall = $summary['statusOverall'] ?? null;
                if (Schema::hasColumn('customer_maintenance_contracts', 'status_overall'))
                    $contractModel->status_overall = $overall;

                if (Schema::hasColumn('customer_maintenance_contracts', 'status')) {
                    $contractModel->status = match ($overall) {
                        'cancelled' => 'cancelled',
                        'inactive' => 'inactive',
                        'draft' => 'draft',
                        default => 'active',
                    };
                }

                if (Schema::hasColumn('customer_maintenance_contracts', 'contract_duration_months')) {
                    $contractModel->contract_duration_months = $contract['durationMonths'] ?? null;
                }
                if (Schema::hasColumn('customer_maintenance_contracts', 'termination_notice_days')) {
                    $contractModel->termination_notice_days = $contract['terminationNoticeDays'] ?? null;
                }
                if (Schema::hasColumn('customer_maintenance_contracts', 'recommended_interval_months')) {
                    $contractModel->recommended_interval_months = $intervalMonths;
                }

                $summaryForCustomer = $summary['summaryForCustomer'] ?? null;
                $notesInternal = $summary['notesInternal'] ?? null;

                if (Schema::hasColumn('customer_maintenance_contracts', 'currency'))
                    $contractModel->currency = 'EUR';
                if (Schema::hasColumn('customer_maintenance_contracts', 'description'))
                    $contractModel->description = $summaryForCustomer;
                if (Schema::hasColumn('customer_maintenance_contracts', 'terms'))
                    $contractModel->terms = $notesInternal;
                if (Schema::hasColumn('customer_maintenance_contracts', 'internal_notes')) {
                    $contractModel->internal_notes = $contract['internalNotes'] ?? null;
                }

                if (Schema::hasColumn('customer_maintenance_contracts', 'payload'))
                    $contractModel->payload = $payload;
                if (Schema::hasColumn('customer_maintenance_contracts', 'meta')) {
                    $contractModel->meta = [
                        'source_system_version' => $system['version'] ?? 1,
                        'history' => $history ?: null,
                        'followUps' => $followUps ?: null,
                        'signatures' => $signatures ?: null,
                        'attachments' => $attachments ?: null,
                    ];
                }

                if (Schema::hasColumn('customer_maintenance_contracts', 'created_by'))
                    $contractModel->created_by = $userId;
                if (Schema::hasColumn('customer_maintenance_contracts', 'updated_by'))
                    $contractModel->updated_by = $userId;

                $contractModel->save();

                if (Schema::hasColumn('customer_maintenance_contracts', 'contract_no') && empty($contractModel->contract_no)) {
                    $contractModel->contract_no = 'CM-' . str_pad((string) $contractModel->id, 6, '0', STR_PAD_LEFT);
                    $contractModel->save();
                }

                // -------------------------
                // 3) PROTOCOL
                // -------------------------
                $maintenanceDate = $maintenance['date'] ?? null;
                $timeFrom = $maintenance['timeFrom'] ?? null;
                $timeTo = $maintenance['timeTo'] ?? null;

                $startedAt = $this->combineDateTimeOrNull($maintenanceDate, $timeFrom);
                $finishedAt = $this->combineDateTimeOrNull($maintenanceDate, $timeTo);

                $protocol = new MaintenanceProtocol();
                if (Schema::hasColumn('maintenance_protocols', 'lead_id'))
                    $protocol->lead_id = $leadId;
                if (Schema::hasColumn('maintenance_protocols', 'alternative_id'))
                    $protocol->alternative_id = $altId;
                if (Schema::hasColumn('maintenance_protocols', 'maintenance_asset_id'))
                    $protocol->maintenance_asset_id = $asset->id;

                if ($firstChecklistId && Schema::hasColumn('maintenance_protocols', 'maintenance_checklist_id')) {
                    $protocol->maintenance_checklist_id = $firstChecklistId;
                }

                $protocol->title = $summary['summaryForCustomerTitle']
                    ?? ('Wartung ' . ($maintenanceDate ?: now()->format('Y-m-d')));

                if (Schema::hasColumn('maintenance_protocols', 'started_at'))
                    $protocol->started_at = $startedAt;
                if (Schema::hasColumn('maintenance_protocols', 'finished_at'))
                    $protocol->finished_at = $finishedAt;
                if (Schema::hasColumn('maintenance_protocols', 'status'))
                    $protocol->status = 'completed';

                if (Schema::hasColumn('maintenance_protocols', 'checklist_snapshot'))
                    $protocol->checklist_snapshot = $checklist ?: null;
                if (Schema::hasColumn('maintenance_protocols', 'answers'))
                    $protocol->answers = $checklist ?: null;

                if (Schema::hasColumn('maintenance_protocols', 'result_summary'))
                    $protocol->result_summary = $summaryForCustomer;
                if (Schema::hasColumn('maintenance_protocols', 'internal_note')) {
                    $protocol->internal_note = $notesInternal ?? $contract['internalNotes'] ?? null;
                }

                if (Schema::hasColumn('maintenance_protocols', 'meta')) {
                    $protocol->meta = [
                        'operating_state_on_arrival' => $maintenance['operatingStateOnArrival'] ?? null,
                        'operating_state_on_departure' => $maintenance['operatingStateOnDeparture'] ?? null,
                        'customer_complaint' => $maintenance['customerComplaint'] ?? null,
                        'environment_conditions' => $maintenance['environmentConditions'] ?? null,
                        'safety' => $safety ?: null,
                        'measurements' => $measurements ?: null,
                    ];
                }

                if (Schema::hasColumn('maintenance_protocols', 'created_by'))
                    $protocol->created_by = $userId;
                if (Schema::hasColumn('maintenance_protocols', 'updated_by'))
                    $protocol->updated_by = $userId;

                $protocol->save();

                // -------------------------
                // 4) TECHNICIANS (FK-safe)
                // -------------------------
                $from = $startedAt;
                $to = $finishedAt;

                $mainTech = $maintenance['mainTechnician'] ?? null;
                if (is_array($mainTech) && !empty($mainTech['employeeId'])) {
                    $empId = (int) $mainTech['employeeId'];
                    if ($empId > 0 && Employee::whereKey($empId)->exists()) {
                        MaintenanceProtocolTechnician::create([
                            'maintenance_protocol_id' => $protocol->id,
                            'employee_id' => $empId,
                            'role' => 'lead',
                            'status' => 'completed',
                            'started_at' => $from,
                            'finished_at' => $to,
                            'meta' => ['name' => $mainTech['name'] ?? null],
                        ]);
                    }
                }

                $adds = $maintenance['additionalTechnicians'] ?? [];
                if (is_array($adds)) {
                    foreach ($adds as $tech) {
                        if (!is_array($tech) || empty($tech['employeeId']))
                            continue;
                        $empId = (int) $tech['employeeId'];
                        if ($empId <= 0 || !Employee::whereKey($empId)->exists())
                            continue;

                        MaintenanceProtocolTechnician::create([
                            'maintenance_protocol_id' => $protocol->id,
                            'employee_id' => $empId,
                            'role' => 'assistant',
                            'status' => 'completed',
                            'started_at' => $from,
                            'finished_at' => $to,
                            'meta' => ['name' => $tech['name'] ?? null],
                        ]);
                    }
                }

                return [
                    'asset' => $asset,
                    'contract' => $contractModel,
                    'protocol' => $protocol,
                ];
            });

        } catch (QueryException $e) {
            Log::error('MaintenanceWizard.store: QueryException', [
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'wizard_payload' => 'Datenbankfehler beim Speichern (FK / Constraints). Siehe Log.',
            ]);
        } catch (\Throwable $e) {
            Log::error('MaintenanceWizard.store: exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors([
                'wizard_payload' => 'Beim Speichern ist ein Fehler aufgetreten.',
            ]);
        }

        return redirect()
            ->route('admin.maintenance.contracts.index')
            ->with('success', 'Wartungsvertrag, Anlage und Protokoll wurden erfolgreich gespeichert.')
            ->with('new_contract_id', $result['contract']->id ?? null);
    }

    protected function convertDateOrNull(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            Log::warning('MaintenanceWizard.store: invalid date, ignoring', [
                'value' => $value,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function combineDateTimeOrNull(?string $date, ?string $time): ?string
    {
        if (empty($date)) {
            return null;
        }

        $timePart = $time ?: '00:00';
        try {
            return \Carbon\Carbon::parse($date . ' ' . $timePart)->toDateTimeString();
        } catch (\Throwable $e) {
            Log::warning('MaintenanceWizard.store: invalid datetime, ignoring', [
                'date' => $date,
                'time' => $time,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }


    public function show(CustomerMaintenanceContract $contract)
    {
        $contract->loadMissing([
            'lead',
            'alternative',
            'asset',
            'responsibleEmployee',
        ]);

        return view('admin.maintenance.contracts.show', compact('contract'));
    }
    /**
     * Dummy / Beispiel: Kennzahlen für eine Anlage.
     * Hier kannst du später echte Wartungen / Protokolle aus DB aggregieren.
     */
    protected function buildHistoryForAsset(Asset $asset): array
    {
        // Beispielwerte; hier könntest du echte Maintenance-Records abfragen
        $installedAt = $asset->installed_at ? Carbon::parse($asset->installed_at) : null;

        return [
            'assetAgeText' => $installedAt
                ? $installedAt->diffForHumans(now(), ['parts' => 2, 'short' => false])
                : '–',
            'totalMaintenanceCount' => $asset->maintenance_count ?? 0,
            'lastMaintenanceDate' => $asset->last_maintenance_at
                ? Carbon::parse($asset->last_maintenance_at)->format('d.m.Y')
                : '–',
            'averageIntervalDays' => $asset->average_maintenance_interval_days ?? null,
            'entries' => [], // optional: Liste der letzten Protokolle
        ];
    }



    public function objectSearch(Request $request)
    {
        $term = trim($request->get('q', ''));

        if ($term === '') {
            return response()->json(['results' => []]);
        }

        $items = LeadProductList::query()
            ->with([
                'customer.branchRelation',
                'alternative',
                'product',
                'employee',
                'fieldEmployee',
            ])
            ->where(function ($q) use ($term) {
                $q->whereHas('customer', function ($q2) use ($term) {
                    $q2->where('lastname', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('firma', 'like', "%{$term}%")
                        ->orWhere('customer_no', 'like', "%{$term}%");
                })
                    ->orWhereHas('alternative', function ($q2) use ($term) {
                        $q2->where('full_address', 'like', "%{$term}%")
                            ->orWhere('street', 'like', "%{$term}%")
                            ->orWhere('city', 'like', "%{$term}%")
                            ->orWhere('postcode', 'like', "%{$term}%");
                    })
                    ->orWhereHas('product', function ($q2) use ($term) {
                        $q2->where('name', 'like', "%{$term}%");
                    });
            })
            ->limit(25)
            ->get();

        $results = $items->map(function (LeadProductList $item) {
            $customer = $item->customer;
            $alt = $item->alternative;
            $product = $item->product;
            $branch = $customer?->branchRelation;
            $employee = $item->employee;
            $fieldEmp = $item->fieldEmployee;

            $customerLabel = trim(($customer->lastname ?? '') . ' ' . ($customer->name ?? ''));
            $customerLabel = $customerLabel !== '' ? $customerLabel : ($customer->firma ?? '');
            $customerLabel = $customerLabel !== '' ? $customerLabel : ('Kunde #' . ($customer->id ?? $item->customer_id));

            $customerNo = $customer->customer_no ?? '';

            $addrParts = array_filter([
                $alt->street ?? '',
                $alt->postcode ?? '',
                $alt->city ?? '',
            ]);
            $addressText = $alt->full_address ?? implode(', ', $addrParts);

            $productName = $product->name ?? ('Produkt #' . $item->product_id);

            $responsibleName = $employee->name
                ?? $employee->name
                ?? $fieldEmp->name
                ?? $fieldEmp->name
                ?? null;

            return [
                'id' => $item->id,

                'text' => sprintf(
                    '%s%s – %s – %s',
                    $customerLabel,
                    $customerNo ? ' (' . $customerNo . ')' : '',
                    $addressText !== '' ? $addressText : 'ohne Adresse',
                    $productName
                ),

                'customer_id' => $customer->id ?? null,
                'customer_name' => $customerLabel,
                'customer_no' => $customerNo,
                'customer_phone' => $customer->phone ?? $customer->telephone ?? null,
                'customer_email' => $customer->email ?? null,

                'alternative_id' => $alt->id ?? null,
                'address_text' => $addressText,

                'product_id' => $product->id ?? null,
                'product_name' => $productName,

                'lead_product_list_id' => $item->id,

                'branch_id' => $branch->id ?? null,
                'branch_name' => $branch->branch ?? null,
                'branch_phone' => $branch->phone ?? null,
                'branch_email' => $branch->email ?? null,
                'branch_city' => $branch->city ?? null,

                'responsible_employee_id' => $employee->id
                    ?? $fieldEmp->id
                    ?? null,
                'responsible_employee_name' => $responsibleName,
            ];
        })->values();

        return response()->json([
            'results' => $results,
        ]);
    }

    public function customerSearch(Request $request)
    {
        $term = trim($request->get('q', ''));

        if ($term === '') {
            return response()->json(['results' => []]);
        }

        $lpls = LeadProductList::query()
            ->with(['customer', 'product', 'alternative'])
            ->where(function ($q) use ($term) {
                // Kunde (NewLeads)
                $q->whereHas('customer', function ($q2) use ($term) {
                    $q2->where('firma', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('lastname', 'like', "%{$term}%")
                        ->orWhere('customer_no', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('phone', 'like', "%{$term}%")
                        ->orWhere('telephone', 'like', "%{$term}%");
                })
                    // Produkt (ArticleGroup)
                    ->orWhereHas('product', function ($q2) use ($term) {
                    $q2->where('article_group', 'like', "%{$term}%");
                })
                    // alternative Adresse
                    ->orWhereHas('alternative', function ($q2) use ($term) {
                    $q2->where('full_address', 'like', "%{$term}%")
                        ->orWhere('street', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%")
                        ->orWhere('postcode', 'like', "%{$term}%");
                });
            })
            ->limit(25)
            ->get();

        $results = [];

        foreach ($lpls as $lpl) {
            $lead = $lpl->customer;   // NewLeads
            $prod = $lpl->product;    // ArticleGroup
            $alt = $lpl->alternative;

            if (!$lead || !$prod) {
                continue;
            }

            $customerName = $lead->firma
                ?: trim(($lead->name ?? '') . ' ' . ($lead->lastname ?? ''));

            if ($customerName === '') {
                $customerName = '#' . $lead->id;
            }

            $addressText = $alt
                ? "{$alt->street}, {$alt->postcode} {$alt->city}"
                : "{$lead->street}, {$lead->postcode} {$lead->city}";

            $productModel = $prod->name ?? null;
            $productGroup = $prod->article_group ?? null;

            // optionale Anlagendaten
            $systemType = $prod->system_type ?? null;
            $systemTypeLabel = $prod->system_type_txt ?? null;
            $serialNumbers = $prod->serial_numbers ?? null;
            $firmwareVersion = $prod->firmware ?? null;

            $results[] = [
                'id' => $lpl->id,

                // wird als Fallback benutzt
                'text' => $customerName . ' – ' . ($productModel ?? 'Produkt #' . $prod->id),

                'customer_id' => $lead->id,
                'alternative_id' => $alt->id ?? null,
                'product_id' => $prod->id,
                'lead_product_list_id' => $lpl->id,

                'customer_name' => $customerName,
                'customer_no' => $lead->customer_no ?? null,
                'address_text' => $addressText,

                // Produktanzeige
                'product_name' => $productModel,
                'product_group' => $productGroup,

                // Anlagendaten für Autofill
                'system_type' => $systemType,
                'system_type_label' => $systemTypeLabel,
                'product_model' => $productModel,
                'serial_numbers' => $serialNumbers,
                'firmware_version' => $firmwareVersion,
            ];
        }

        return response()->json(['results' => $results]);
    }
    /**
     * Select2: Filial-Suche (Branches)
     */
    public function branchSearch(Request $request)
    {
        $term = trim($request->get('q', ''));

        if ($term === '') {
            return response()->json(['results' => []]);
        }

        $branches = Branch::query()
            ->where('branch', 'like', "%{$term}%")      // Name der Filiale
            ->orWhere('city', 'like', "%{$term}%")
            ->orWhere('postcode', 'like', "%{$term}%")
            ->limit(25)
            ->get();

        $results = [];

        foreach ($branches as $branch) {
            $results[] = [
                'id' => $branch->id,
                'text' => $branch->branch . ' – ' . ($branch->city ?? ''),

                'branch_id' => $branch->id,
                'name' => $branch->branch,
                'street' => $branch->street ?? null,
                'postcode' => $branch->postcode ?? null,
                'city' => $branch->city ?? null,
                'country' => $branch->country ?? 'DE',
                'email' => $branch->email ?? null,
                'phone' => $branch->phone ?? null,
                'website' => $branch->website ?? null,
                'note' => $branch->note ?? null,
            ];
        }

        return response()->json(['results' => $results]);
    }


    public function ajaxIndex(Request $request)
    {
        $q = $request->input('q');
        $brandId = $request->input('brand_id');
        $distributorId = $request->input('distributor_id');

        Log::info('MaintenanceWizard.ajaxIndex: incoming', [
            'q' => $q,
            'brand_id' => $brandId,
            'distributor_id' => $distributorId,
        ]);

        // Basis-Query
        $query = MaintenanceChecklist::query()
            ->where('status', 'active');

        // Filter: Brand
        if (!empty($brandId)) {
            $query->whereHas('brands', function ($sub) use ($brandId) {
                $sub->where('brands.id', $brandId);
            });
        }

        // Filter: Distributor
        if (!empty($distributorId)) {
            $query->whereHas('distributors', function ($sub) use ($distributorId) {
                $sub->where('distributors.id', $distributorId);
            });
        }

        // Freitextsuche
        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', '%' . $q . '%')
                    ->orWhere('description', 'like', '%' . $q . '%');
            });
        }

        $checklists = $query
            ->orderBy('title')
            ->limit(30)
            ->get(['id', 'title', 'description', 'type', 'status']);

        Log::info('MaintenanceWizard.ajaxIndex: result after filters', [
            'count' => $checklists->count(),
            'ids' => $checklists->pluck('id'),
            'applied_brand' => $brandId,
            'applied_dist' => $distributorId,
        ]);

        // Fallback: wenn durch Brand/Distributor-Filter nichts gefunden wurde,
        // dann erneut (nur mit status + q), damit das Dropdown nicht komplett leer ist.
        if ($checklists->isEmpty() && (!empty($brandId) || !empty($distributorId))) {
            Log::warning('MaintenanceWizard.ajaxIndex: no results with brand/distributor filter, using fallback');

            $fallback = MaintenanceChecklist::query()
                ->where('status', 'active');

            if (!empty($q)) {
                $fallback->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', '%' . $q . '%')
                        ->orWhere('description', 'like', '%' . $q . '%');
                });
            }

            $checklists = $fallback
                ->orderBy('title')
                ->limit(30)
                ->get(['id', 'title', 'description', 'type', 'status']);

            Log::info('MaintenanceWizard.ajaxIndex: fallback result', [
                'count' => $checklists->count(),
                'ids' => $checklists->pluck('id'),
            ]);
        }

        return response()->json([
            'checklists' => $checklists,
        ]);
    }

    /**
     * AJAX: einzelne Checkliste + Items für den Wizard.
     */
    public function ajaxShow(MaintenanceChecklist $checklist)
    {
        $checklist->load([
            'items' => function ($q) {
                $q->orderBy('sort_order');
            }
        ]);

        Log::info('MaintenanceWizard.ajaxShow: loaded checklist', [
            'id' => $checklist->id,
            'title' => $checklist->title,
            'items' => $checklist->items->pluck('id'),
        ]);

        return response()->json([
            'id' => $checklist->id,
            'title' => $checklist->title,
            'description' => $checklist->description,
            'type' => $checklist->type,
            'status' => $checklist->status,
            'items' => $checklist->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'label' => $item->label,
                    'field_name' => $item->field_name,
                    'field_type' => $item->field_type, // text, textarea, select, checkbox, radio, file_image, file_document
                    'options' => $item->options,    // via $casts already array oder null
                    'is_required' => (bool) $item->is_required,
                    'help_text' => $item->help_text,
                    'placeholder' => $item->placeholder,
                    'file_accept' => $item->file_accept,
                    'sort_order' => $item->sort_order,
                ];
            })->values(),
        ]);
    }
    public function bulkStatus(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:customer_maintenance_contracts,id'],
            'status' => ['required', 'in:draft,active,inactive,cancelled'],
        ]);

        CustomerMaintenanceContract::whereIn('id', $data['ids'])
            ->update([
                'status' => $data['status'],
            ]);

        return response()->json([
            'ok' => true,
            'message' => 'Status aktualisiert.',
        ]);
    }

    /**
     * Bulk delete for selected contracts.
     *
     * Expects JSON body:
     * {
     *   "ids": [1, 2, 3]
     * }
     */
    public function bulkDelete(Request $request)
    {
        abort_unless((bool) (auth()->user()->is_admin ?? false), 403, 'Massenloeschung nur fuer Administratoren (MASTER-01 P0-5).');
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:customer_maintenance_contracts,id'],
        ]);

        CustomerMaintenanceContract::whereIn('id', $data['ids'])->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Verträge gelöscht.',
        ]);
    }

    /**
     * Update Kanban positions + status via drag & drop.
     *
     * Expects JSON body:
     * {
     *   "items": [
     *     { "id": 5, "status": "active", "position": 1 },
     *     { "id": 9, "status": "active", "position": 2 },
     *     { "id": 3, "status": "inactive", "position": 1 }
     *   ]
     * }
     */
    public function kanbanUpdate(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:customer_maintenance_contracts,id'],
            'items.*.status' => ['required', 'in:draft,active,inactive,cancelled'],
            'items.*.position' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['items'] as $item) {
                $update = [
                    'status' => $item['status'],
                ];

                if (Schema::hasColumn('customer_maintenance_contracts', 'kanban_position')) {
                    $update['kanban_position'] = $item['position'];
                }

                if (Schema::hasColumn('customer_maintenance_contracts', 'updated_by')) {
                    $update['updated_by'] = optional(request()->user())->id;
                }

                CustomerMaintenanceContract::whereKey($item['id'])->update($update);
            }
        });

        return response()->json([
            'ok' => true,
            'message' => 'Kanban aktualisiert.',
        ]);
    }

    public function edit(CustomerMaintenanceContract $contract)
    {
        $contract->loadMissing([
            'lead',
            'alternative',
            'asset',
            'responsibleEmployee',
        ]);

        return view('admin.maintenance.wizard', [
            'mode' => 'edit',
            'contract' => $contract,
            'lead' => $contract->lead,
            'alternative' => $contract->alternative,
            'asset' => $contract->asset,
            'manufacturer' => \App\Models\Brand::first(),
            'employees' => \App\Models\Employee::with([
                'departmentPositions.department',
                'departmentPositions.position',
            ])->where('status', 'Active')->orderBy('name')->get(),
            'history' => [
                'assetAgeText' => null,
                'totalMaintenanceCount' => null,
                'lastMaintenanceDate' => null,
                'averageIntervalDays' => null,
                'entries' => [],
            ],
        ]);
    }

    public function update(Request $request, CustomerMaintenanceContract $contract)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:draft,active,inactive,cancelled'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'next_service_date' => ['nullable', 'date'],
            'interval_type' => ['nullable', 'in:yearly,monthly,custom'],
            'interval_months' => ['nullable', 'integer', 'min:1', 'max:120'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        // normalize empty strings -> null
        foreach ($data as $k => $v) {
            if ($v === '')
                $data[$k] = null;
        }

        $contract->fill($data);
        $contract->updated_by = optional($request->user())->id;
        $contract->save();

        return redirect()
            ->route('admin.maintenance.contracts.show', $contract->id)
            ->with('success', 'Vertrag wurde aktualisiert.');
    }


    public function incoming(Request $request)
    {
        $days = max(1, min(180, (int) $request->get('days', 30)));

        $contracts = CustomerMaintenanceContract::query()
            ->with([
                'lead:id,name,lastname,firma,customer_no,street,postcode,city',
                'alternative:id,full_address,street,postcode,city',
                'asset:id,title,manufacturer,model,technical_data',
                'responsibleEmployee:id,name,image,status',
            ])
            ->whereNotNull('next_service_date')
            ->whereDate('next_service_date', '<=', now()->addDays($days)->toDateString())
            ->whereIn('status', ['active', 'draft'])
            ->orderBy('next_service_date')
            ->limit(50)
            ->get();

        return response()->json([
            'ok' => true,
            'count' => $contracts->count(),
            'items' => $contracts->map(fn($contract) => $this->maintenanceContractPayload($contract))->values(),
        ]);
    }

    public function calendarFeed(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $query = CustomerMaintenanceContract::query()
            ->with(['lead', 'alternative', 'asset', 'responsibleEmployee'])
            ->whereNotNull('next_service_date');

        if ($start) {
            $query->whereDate('next_service_date', '>=', Carbon::parse($start)->toDateString());
        }

        if ($end) {
            $query->whereDate('next_service_date', '<=', Carbon::parse($end)->toDateString());
        }

        $contracts = $query->orderBy('next_service_date')->get();

        return response()->json($contracts->map(function (CustomerMaintenanceContract $contract) {
            $payload = $this->maintenanceContractPayload($contract);

            $color = match ($payload['status']) {
                'active' => '#10b981',
                'inactive' => '#f59e0b',
                'cancelled' => '#ef4444',
                default => '#6b7280',
            };

            return [
                'id' => $contract->id,
                'title' => trim(($contract->contract_no ? $contract->contract_no . ' · ' : '') . ($contract->title ?: 'Wartung')),
                'start' => $payload['next_service_date'],
                'allDay' => true,
                'url' => $payload['show_url'],
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => $payload,
            ];
        })->values());
    }

    public function kanbanFeed()
    {
        $query = CustomerMaintenanceContract::query()
            ->with(['lead', 'alternative', 'asset', 'responsibleEmployee'])
            ->orderByRaw("FIELD(status, 'draft','active','inactive','cancelled')");

        if (Schema::hasColumn('customer_maintenance_contracts', 'kanban_position')) {
            $query->orderBy('kanban_position');
        }

        $contracts = $query->orderByRaw('next_service_date IS NULL ASC')
            ->orderBy('next_service_date')
            ->orderBy('id')
            ->get();

        return response()->json([
            'ok' => true,
            'items' => $contracts->map(fn($contract) => $this->maintenanceContractPayload($contract))->values(),
        ]);
    }

    protected function maintenanceContractPayload(CustomerMaintenanceContract $contract): array
    {
        $lead = $contract->lead;
        $alt = $contract->alternative;
        $asset = $contract->asset;
        $resp = $contract->responsibleEmployee;

        $customer = null;
        if ($lead) {
            $customer = $lead->firma ?: trim(($lead->name ?? '') . ' ' . ($lead->lastname ?? ''));
        }

        $address = null;
        if ($alt) {
            $address = $alt->full_address ?: trim(($alt->street ?? '') . ', ' . ($alt->postcode ?? '') . ' ' . ($alt->city ?? ''));
        }

        if (!$address && $lead) {
            $address = trim(($lead->street ?? '') . ', ' . ($lead->postcode ?? '') . ' ' . ($lead->city ?? ''));
        }

        $product = trim(implode(' · ', array_filter([
            $asset->manufacturer ?? null,
            $asset->model ?? null,
            $asset->title ?? null,
        ])));

        $nextService = $contract->next_service_date ? Carbon::parse($contract->next_service_date) : null;
        $daysTo = $nextService ? now()->startOfDay()->diffInDays($nextService->copy()->startOfDay(), false) : null;

        return [
            'id' => $contract->id,
            'contract_no' => $contract->contract_no,
            'title' => $contract->title ?: ($asset->title ?? 'Wartung'),
            'status' => $contract->status ?: 'draft',
            'status_label' => match ($contract->status ?: 'draft') {
                'active' => 'Aktiv',
                'inactive' => 'Inaktiv',
                'cancelled' => 'Gekündigt',
                default => 'Entwurf',
            },
            'customer' => trim((string) $customer) !== '' ? $customer : '–',
            'address' => trim((string) $address) !== '' ? $address : '–',
            'product' => $product !== '' ? $product : '–',
            'responsible' => $resp?->name ?: '–',
            'next_service_date' => $nextService?->format('Y-m-d'),
            'next_service_de' => $nextService?->format('d.m.Y') ?: '–',
            'days_to_service' => $daysTo,
            'is_overdue' => $daysTo !== null && $daysTo < 0,
            'is_soon' => $daysTo !== null && $daysTo >= 0 && $daysTo <= 30,
            'show_url' => route('admin.maintenance.contracts.show', $contract->id),
            'edit_url' => route('admin.maintenance.contracts.edit', $contract->id),
        ];
    }

}
