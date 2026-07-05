<?php

namespace App\Http\Controllers\Customer\Deal;
use App\Http\Controllers\Controller;

use App\Models\Deal;
use App\Models\DealMeasurement;
use App\Models\DealMeasurementDetail;
use App\Models\DealMeasurementItem;
use App\Models\OfferDetail;
use App\Models\Product;
use App\Support\DealMeasurementHistoryLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;
use App\Models\Employee;
use App\Models\MainAppointment;
use App\Models\MainAppointmentEmployee;
use App\Models\PersonalTask;
use App\Models\EmployeesPersonalTask;
class DealMeasurementController extends Controller
{
    public function index(Request $request)
    {
        /*
         * First create missing DealMeasurement rows from offer_details.
         * Existing saved deal_measurements are never overwritten.
         */
        $this->createMissingMeasurementsFromOfferDetails($request);

        $measurements = DealMeasurement::query()
            ->with([
                'deal',
                'customer',
                'alternative',
                'alternative.pvWpDetail',
                'alternative.roofs',
                'product',
                'items',
                'offer',
                'detail',
                'editDetail',
                'histories',
            ])
            ->withCount('items')
            ->when($request->filled('customer_id'), function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })
            ->when($request->filled('alternative_id'), function ($query) use ($request) {
                $query->where('alternative_id', $request->alternative_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('measurement_no', 'like', "%{$search}%")
                        ->orWhere('order_number', 'like', "%{$search}%")
                        ->orWhere('offer_no', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('lastname', 'like', "%{$search}%")
                                ->orWhere('firma', 'like', "%{$search}%")
                                ->orWhere('city', 'like', "%{$search}%")
                                ->orWhere('street', 'like', "%{$search}%");
                        })
                        ->orWhereHas('alternative', function ($alternativeQuery) use ($search) {
                            $alternativeQuery
                                ->where('object_name', 'like', "%{$search}%")
                                ->orWhere('city', 'like', "%{$search}%")
                                ->orWhere('street', 'like', "%{$search}%")
                                ->orWhere('postcode', 'like', "%{$search}%");
                        })
                        ->orWhereHas('product', function ($productQuery) use ($search) {
                            $productQuery
                                ->where('article_group', 'like', "%{$search}%")
                                ->orWhere('product', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('title', 'like', "%{$search}%")
                                ->orWhere('article_no', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->get();

        if ($request->filled('type')) {
            $type = strtoupper((string) $request->type);

            $measurements = $measurements->filter(function ($measurement) use ($type) {
                $text = mb_strtolower(
                    trim(
                        ($measurement->product_name ?? '') . ' ' .
                        optional($measurement->product)->article_group . ' ' .
                        optional($measurement->product)->product . ' ' .
                        optional($measurement->product)->model . ' ' .
                        optional($measurement->product)->name . ' ' .
                        optional($measurement->product)->title
                    ),
                    'UTF-8'
                );

                $text = str_replace(
                    ['ä', 'ö', 'ü', 'ß'],
                    ['ae', 'oe', 'ue', 'ss'],
                    $text
                );

                if ($type === 'PV') {
                    return str_contains($text, 'pv')
                        || str_contains($text, 'photovoltaik')
                        || str_contains($text, 'photovoltaic')
                        || str_contains($text, 'solar');
                }

                if ($type === 'WP') {
                    return str_contains($text, 'waermepumpe')
                        || str_contains($text, 'wärmepumpe')
                        || str_contains($text, 'heatpump')
                        || str_contains($text, 'heat pump')
                        || preg_match('/\bwp\b/u', $text);
                }

                return true;
            })->values();
        }

        $employeeMap = $this->buildEmployeeMap($measurements);
        $employees = $this->getAssignableEmployees();

        return view('admin.deal_measurements.index', compact('measurements', 'employeeMap', 'employees'));
    }

    private function createMissingMeasurementsFromOfferDetails(Request $request): void
    {
        $existingOfferDetailIds = DealMeasurement::query()
            ->whereNotNull('offer_detail_id')
            ->pluck('offer_detail_id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        $details = OfferDetail::query()
            ->with([
                'offer',
                'offer.customer',
                'offer.alternative',
                'offer.product',
            ])
            ->when(!empty($existingOfferDetailIds), function ($query) use ($existingOfferDetailIds) {
                $query->whereNotIn('id', $existingOfferDetailIds);
            })
            ->when($request->filled('customer_id'), function ($query) use ($request) {
                $query->whereHas('offer', function ($offerQuery) use ($request) {
                    $offerQuery->where('customer_id', $request->customer_id);
                });
            })
            ->when($request->filled('alternative_id'), function ($query) use ($request) {
                $query->whereHas('offer', function ($offerQuery) use ($request) {
                    $offerQuery->where('alternative_id', $request->alternative_id);
                });
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('offer_no', 'like', "%{$search}%")
                        ->orWhereHas('offer', function ($offerQuery) use ($search) {
                            $offerQuery
                                ->where('offer_no', 'like', "%{$search}%")
                                ->orWhereHas('customer', function ($customerQuery) use ($search) {
                                    $customerQuery
                                        ->where('name', 'like', "%{$search}%")
                                        ->orWhere('lastname', 'like', "%{$search}%")
                                        ->orWhere('firma', 'like', "%{$search}%")
                                        ->orWhere('city', 'like', "%{$search}%")
                                        ->orWhere('street', 'like', "%{$search}%");
                                })
                                ->orWhereHas('alternative', function ($alternativeQuery) use ($search) {
                                    $alternativeQuery
                                        ->where('object_name', 'like', "%{$search}%")
                                        ->orWhere('city', 'like', "%{$search}%")
                                        ->orWhere('street', 'like', "%{$search}%")
                                        ->orWhere('postcode', 'like', "%{$search}%");
                                })
                                ->orWhereHas('product', function ($productQuery) use ($search) {
                                    $productQuery
                                        ->where('article_group', 'like', "%{$search}%")
                                        ->orWhere('product', 'like', "%{$search}%")
                                        ->orWhere('model', 'like', "%{$search}%")
                                        ->orWhere('name', 'like', "%{$search}%")
                                        ->orWhere('title', 'like', "%{$search}%")
                                        ->orWhere('article_no', 'like', "%{$search}%")
                                        ->orWhere('sku', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->latest('id')
            ->limit(100)
            ->get();

        foreach ($details as $detail) {
            try {
                DB::transaction(function () use ($detail) {
                    $existing = DealMeasurement::query()
                        ->where('offer_detail_id', $detail->id)
                        ->first();

                    if ($existing) {
                        return;
                    }

                    $offer = $detail->offer;

                    if (!$offer) {
                        return;
                    }

                    $sections = $this->normalizeArray($detail->sections ?? []);
                    $materialRows = $this->extractMaterialRows($sections);

                    $measurement = DealMeasurement::create([
                        'deal_id' => null,

                        'offer_id' => $detail->offer_id ?? $offer->id ?? null,
                        'offer_folder_id' => $detail->offer_folder_id ?? $offer->offer_folder_id ?? null,
                        'offer_detail_id' => $detail?->id,

                        'customer_id' => $offer->customer_id ?? null,
                        'alternative_id' => $offer->alternative_id ?? null,
                        'product_id' => $offer->product_id ?? null,
                        'department_id' => $offer->department_id ?? null,

                        'measurement_no' => $this->generateMeasurementNo(),
                        'order_number' => null,
                        'offer_no' => $detail->offer_no ?? $offer->offer_no ?? null,

                        'status' => 'draft',

                        'sections_snapshot' => $sections,
                        'material_summary' => $materialRows,

                        'created_by' => auth()->user()->name ?? auth()->id(),
                        'updated_by' => auth()->user()->name ?? auth()->id(),
                    ]);

                    foreach ($materialRows as $index => $row) {
                        DealMeasurementItem::create([
                            'deal_measurement_id' => $measurement->id,
                            'deal_id' => null,

                            'offer_id' => $measurement->offer_id,
                            'offer_detail_id' => $detail?->id,

                            'section_title' => $row['section_title'] ?? null,
                            'sort_order' => $index + 1,
                            'depth' => $row['depth'] ?? 0,

                            'item_type' => $row['item_type'] ?? null,
                            'kind' => $row['kind'] ?? null,

                            'master_set_id' => $row['master_set_id'] ?? null,
                            'master_set_component_id' => $row['master_set_component_id'] ?? null,
                            'product_id' => $row['product_id'] ?? null,
                            'component_id' => $row['component_id'] ?? null,

                            'article_no' => $row['article_no'] ?? null,
                            'distributor_article_no' => $row['distributor_article_no'] ?? null,
                            'distributor_id' => $row['distributor_id'] ?? null,
                            'distributor_name' => $row['distributor_name'] ?? null,

                            'name' => $row['name'] ?? 'Unbenannt',
                            'description' => $row['description'] ?? null,
                            'image' => $row['image'] ?? null,

                            'qty_offer' => $row['qty_offer'] ?? 0,
                            'qty_measurement' => $row['qty_offer'] ?? 0,
                            'qty_final' => $row['qty_offer'] ?? 0,

                            'unit' => $row['unit'] ?? null,
                            'measure' => $row['measure'] ?? null,

                            'unit_price' => $row['unit_price'] ?? 0,
                            'purchase_price' => $row['purchase_price'] ?? 0,
                            'total_price' => $row['total_price'] ?? 0,

                            'order_status' => $row['order_status'] ?? null,
                            'stock_allocation' => $row['stock_allocation'] ?? null,
                            'raw_snapshot' => $row['raw_snapshot'] ?? null,

                            'created_by' => auth()->user()->name ?? auth()->id(),
                            'updated_by' => auth()->user()->name ?? auth()->id(),
                        ]);
                    }

                    DealMeasurementHistoryLogger::log(
                        measurement: $measurement,
                        action: 'Feinaufmaß automatisch aus Angebot erstellt',
                        section: 'System',
                        changes: [
                            'offer_detail_id' => $detail?->id,
                            'offer_no' => $measurement->offer_no,
                            'materials_count' => count($materialRows),
                        ]
                    );
                });
            } catch (\Throwable $e) {
                Log::error('Auto create measurement from offer detail failed', [
                    'offer_detail_id' => $detail?->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    private function generateMeasurementNo(): string
    {
        $prefix = 'FM-' . now()->format('Ymd') . '-';

        $latestId = (int) DealMeasurement::query()->max('id') + 1;

        return $prefix . str_pad((string) $latestId, 5, '0', STR_PAD_LEFT);
    }

    public function storeFromDeal(Deal $deal)
    {
        try {
            return DB::transaction(function () use ($deal) {
                $detail = $this->findOfferDetailForDeal($deal);

                /*
                 * IMPORTANT FIX:
                 * The Auftrag profile must be able to create a Feinaufmaß even when
                 * no offer_detail row exists yet. In that case we create an empty
                 * Feinaufmaß shell so the user can assign employee/date/time and
                 * create appointment/task from the profile.
                 */
                $sections = $detail ? $this->normalizeArray($detail->sections ?? []) : [];
                $materialRows = $detail ? $this->extractMaterialRows($sections) : [];

                $existing = DealMeasurement::withTrashed()
                    ->where(function ($query) use ($detail, $deal) {
                        $query->where('deal_id', $deal->id);

                        if ($detail) {
                            $query->orWhere('offer_detail_id', $detail->id);
                        }

                        if (!empty($deal->offer_id)) {
                            $query->orWhere('offer_id', $deal->offer_id);
                        }

                        if (!empty($deal->offer_folder_id)) {
                            $query->orWhere('offer_folder_id', $deal->offer_folder_id);
                        }
                    })
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    $wasDeleted = method_exists($existing, 'trashed') && $existing->trashed();

                    if ($wasDeleted) {
                        $existing->restore();

                        DealMeasurementItem::withTrashed()
                            ->where('deal_measurement_id', $existing->id)
                            ->forceDelete();

                        if ($existing->editDetail) {
                            $existing->editDetail()->delete();
                        }
                    }

                    $existing->update([
                        'deal_id' => $deal->id,
                        'offer_id' => $deal->offer_id,
                        'offer_folder_id' => $deal->offer_folder_id,
                        'offer_detail_id' => $detail?->id,

                        'customer_id' => $deal->customer_id,
                        'alternative_id' => $deal->alternative_id,
                        'product_id' => $deal->product_id,
                        'department_id' => $deal->department_id,

                        'order_number' => $deal->order_number ?? $deal->auftrag_nr ?? null,
                        'offer_no' => $detail?->offer_no ?? optional($detail?->offer)->offer_no ?? $deal->offer_number ?? null,

                        'status' => $existing->status ?: 'draft',
                        'sent_at' => $existing->sent_at,
                        'sent_by' => $existing->sent_by,

                        'sections_snapshot' => $sections,
                        'material_summary' => $materialRows,

                        'materials_snapshot' => $existing->materials_snapshot,
                        'materials_approved_count' => $existing->materials_approved_count ?? 0,
                        'materials_total_count' => count($materialRows),
                        'materials_saved_at' => $existing->materials_saved_at,

                        'updated_by' => auth()->user()->name ?? auth()->id(),
                    ]);

                    if ($wasDeleted || $existing->items()->count() === 0) {
                        foreach ($materialRows as $index => $row) {
                            $this->createMeasurementItemFromRow(
                                measurement: $existing,
                                deal: $deal,
                                detail: $detail,
                                row: $row,
                                sortOrder: $index + 1
                            );
                        }
                    }

                    if ($deal->project_status !== 'measurement') {
                        $deal->update(['project_status' => 'measurement']);
                    }

                    DealMeasurementHistoryLogger::log(
                        measurement: $existing,
                        action: $wasDeleted
                        ? 'Gelöschtes Feinaufmaß wiederhergestellt'
                        : 'Feinaufmaß aus Auftrag geöffnet/verknüpft',
                        section: 'System',
                        changes: [
                            'deal_id' => $deal->id,
                            'offer_detail_id' => $detail?->id,
                            'materials_count' => count($materialRows),
                            'created_without_offer_detail' => $detail ? false : true,
                        ]
                    );

                    $fresh = $existing->fresh();

                    return response()->json([
                        'success' => true,
                        'message' => $detail
                            ? 'Feinaufmaß wurde verknüpft.'
                            : 'Feinaufmaß wurde ohne Angebotsdetails erstellt. Sie können jetzt Mitarbeiter, Termin und Aufgabe planen.',
                        'measurement_id' => $fresh->id,
                        'redirect_url' => route('deal.profile', $deal) . '?tab=measurement&setup=1',
                        'show_assignment_modal' => true,
                    ]);
                }

                $measurement = DealMeasurement::create([
                    'deal_id' => $deal->id,
                    'offer_id' => $deal->offer_id,
                    'offer_folder_id' => $deal->offer_folder_id,
                    'offer_detail_id' => $detail?->id,

                    'customer_id' => $deal->customer_id,
                    'alternative_id' => $deal->alternative_id,
                    'product_id' => $deal->product_id,
                    'department_id' => $deal->department_id,

                    'measurement_no' => $this->generateUniqueMeasurementNo(),
                    'order_number' => $deal->order_number ?? $deal->auftrag_nr ?? null,
                    'offer_no' => $detail?->offer_no ?? optional($detail?->offer)->offer_no ?? $deal->offer_number ?? null,

                    'status' => $detail ? 'sent' : 'draft',
                    'sent_at' => $detail ? now() : null,
                    'sent_by' => $detail ? (auth()->user()->name ?? auth()->id()) : null,

                    'sections_snapshot' => $sections,
                    'material_summary' => $materialRows,

                    'materials_total_count' => count($materialRows),
                    'materials_approved_count' => 0,

                    'note' => $detail ? null : 'Feinaufmaß wurde ohne Angebotsdetails direkt aus dem Auftrag erstellt.',
                    'created_by' => auth()->user()->name ?? auth()->id(),
                    'updated_by' => auth()->user()->name ?? auth()->id(),
                ]);

                foreach ($materialRows as $index => $row) {
                    $this->createMeasurementItemFromRow(
                        measurement: $measurement,
                        deal: $deal,
                        detail: $detail,
                        row: $row,
                        sortOrder: $index + 1
                    );
                }

                $oldDealStatus = $deal->project_status;

                if ($deal->project_status !== 'measurement') {
                    $deal->update(['project_status' => 'measurement']);
                }

                DealMeasurementHistoryLogger::log(
                    measurement: $measurement,
                    action: $detail
                    ? 'Feinaufmaß aus Auftrag erstellt'
                    : 'Feinaufmaß ohne Angebotsdetails aus Auftrag erstellt',
                    section: 'System',
                    field: 'project_status',
                    oldValue: $oldDealStatus,
                    newValue: 'measurement',
                    changes: [
                        'deal_id' => $deal->id,
                        'offer_detail_id' => $detail?->id,
                        'materials_count' => count($materialRows),
                        'created_without_offer_detail' => $detail ? false : true,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => $detail
                        ? 'Feinaufmaß wurde erstellt und gesendet.'
                        : 'Feinaufmaß wurde ohne Angebotsdetails erstellt. Sie können jetzt Mitarbeiter, Termin und Aufgabe planen.',
                    'measurement_id' => $measurement->id,
                    'redirect_url' => route('deal.profile', $deal) . '?tab=measurement&setup=1',
                    'show_assignment_modal' => true,
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Create deal measurement failed', [
                'deal_id' => $deal->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Feinaufmaß konnte nicht erstellt werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    private function createMeasurementItemFromRow(
        DealMeasurement $measurement,
        Deal $deal,
        ?OfferDetail $detail,
        array $row,
        int $sortOrder
    ): DealMeasurementItem {
        return DealMeasurementItem::create([
            'deal_measurement_id' => $measurement->id,
            'deal_id' => $deal->id,

            'offer_id' => $measurement->offer_id ?? $deal->offer_id,
            'offer_detail_id' => $detail?->id,

            'section_title' => $row['section_title'] ?? null,
            'sort_order' => $sortOrder,
            'depth' => $row['depth'] ?? 0,

            'item_type' => $row['item_type'] ?? null,
            'kind' => $row['kind'] ?? null,

            'master_set_id' => $row['master_set_id'] ?? null,
            'master_set_component_id' => $row['master_set_component_id'] ?? null,
            'product_id' => $row['product_id'] ?? null,
            'component_id' => $row['component_id'] ?? null,

            'article_no' => $row['article_no'] ?? null,
            'distributor_article_no' => $row['distributor_article_no'] ?? null,
            'distributor_id' => $row['distributor_id'] ?? null,
            'distributor_name' => $row['distributor_name'] ?? null,

            'name' => $row['name'] ?? 'Unbenannt',
            'description' => $row['description'] ?? null,
            'image' => $row['image'] ?? null,

            /*
             * This now uses the corrected total quantity from extractMaterialRows().
             */
            'qty_offer' => $row['qty_offer'] ?? 0,
            'qty_measurement' => $row['qty_offer'] ?? 0,
            'qty_final' => $row['qty_offer'] ?? 0,

            'unit' => $row['unit'] ?? null,
            'measure' => $row['measure'] ?? null,

            'unit_price' => $row['unit_price'] ?? 0,
            'purchase_price' => $row['purchase_price'] ?? 0,
            'total_price' => $row['total_price'] ?? 0,

            'order_status' => $row['order_status'] ?? null,
            'stock_allocation' => $row['stock_allocation'] ?? null,
            'raw_snapshot' => $row['raw_snapshot'] ?? null,

            'updated_by' => auth()->user()->name ?? auth()->id(),
        ]);
    }

    private function generateUniqueMeasurementNo(): string
    {
        $prefix = 'FM-' . now()->format('Ymd') . '-';

        for ($attempt = 1; $attempt <= 30; $attempt++) {
            /*
             * Include soft-deleted rows because their measurement_no is still blocked
             * by the unique index.
             */
            $latestNumber = DealMeasurement::withTrashed()
                ->where('measurement_no', 'like', $prefix . '%')
                ->selectRaw("
                MAX(CAST(SUBSTRING(measurement_no, ?) AS UNSIGNED)) as max_number
            ", [strlen($prefix) + 1])
                ->lockForUpdate()
                ->value('max_number');

            $nextNumber = ((int) $latestNumber) + $attempt;

            $measurementNo = $prefix . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);

            $exists = DealMeasurement::withTrashed()
                ->where('measurement_no', $measurementNo)
                ->exists();

            if (!$exists) {
                return $measurementNo;
            }
        }

        return $prefix . strtoupper(uniqid());
    }
    public function show(DealMeasurement $measurement)
    {
        $measurement->load([
            'deal',
            'customer',
            'alternative',
            'alternative.pvWpDetail',
            'alternative.roofs',
            'product',
            'folder',
            'detail',
            'editDetail',
            'creator',
            'items',
            'offer',
            'histories',
        ]);

        /*
         * Keep appointment/task loading independent from model relationships.
         * This avoids breaking the page while you are still adding the new relations
         * to DealMeasurement model.
         */
        $measurement->setRelation('appointmentRecord', $this->findMeasurementAppointment($measurement));
        $measurement->setRelation('personalTaskRecord', $this->findMeasurementPersonalTask($measurement));
        $measurement->setRelation('responsibleEmployeeRecord', $this->findEmployeeById($measurement->responsible_employee_id ?? null));

        /*
         * Your Blade expects $measurements as collection.
         * For the single show page, pass one loaded measurement as collection.
         */
        $measurements = collect([$measurement]);

        $employeeMap = $this->buildEmployeeMap($measurements);
        $employees = $this->getAssignableEmployees();

        return view('admin.deal_measurements.index', compact('measurement', 'measurements', 'employeeMap', 'employees'));
    }


    public function assignWork(Request $request, DealMeasurement $measurement)
    {
        /*
         * IMPORTANT:
         * status = completed means the technical Aufmaß form was completed.
         * It must NOT block planning when the Kanban assignment is still "Nicht geplant".
         * Only a completed assignment_status should lock the appointment/task planning.
         */
        $assignmentStatusForLock = (string) ($measurement->assignment_status ?: 'not_assigned');

        if ($assignmentStatusForLock === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Dieses Aufmaß ist im Kanban als abgeschlossen markiert. Bitte zuerst im Kanban zurück verschieben oder entsperren.',
            ], 423);
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => [
                'required',
                'integer',
                Rule::exists('employees', 'id')->where(fn($query) => $query->where('status', 'Active')),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
            'end_time' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'],
            'description' => ['nullable', 'string', 'max:5000'],
            'create_task' => ['nullable', 'boolean'],
            'task_priority' => ['nullable', 'string', 'max:50'],
        ], [
            'employee_id.required' => 'Bitte wählen Sie einen verantwortlichen Mitarbeiter.',
            'employee_id.exists' => 'Der ausgewählte Mitarbeiter wurde nicht gefunden oder ist nicht Active.',
            'start_date.required' => 'Bitte wählen Sie ein Startdatum.',
            'start_time.required' => 'Bitte wählen Sie eine Startzeit.',
            'start_time.regex' => 'Die Startzeit muss im Format HH:MM sein.',
            'end_time.required' => 'Bitte wählen Sie eine Endzeit.',
            'end_time.regex' => 'Die Endzeit muss im Format HH:MM sein.',
            'end_date.after_or_equal' => 'Das Enddatum darf nicht vor dem Startdatum liegen.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['start_time'] = substr((string) $validated['start_time'], 0, 5);
        $validated['end_time'] = substr((string) $validated['end_time'], 0, 5);

        try {
            return DB::transaction(function () use ($validated, $measurement) {
                $measurement = DealMeasurement::query()
                    ->with(['deal', 'customer', 'alternative', 'product', 'offer'])
                    ->lockForUpdate()
                    ->findOrFail($measurement->id);

                $employeeId = (int) $validated['employee_id'];
                $startDate = $validated['start_date'];
                $endDate = $validated['end_date'] ?? $validated['start_date'];
                $startTime = $validated['start_time'];
                $endTime = $validated['end_time'];
                $createTask = (bool) ($validated['create_task'] ?? false);
                $priority = $validated['task_priority'] ?? 'normal';
                $authEmployeeId = $this->authEmployeeId();

                $oldData = [
                    'appointment_id' => $measurement->appointment_id ?? null,
                    'personal_task_id' => $measurement->personal_task_id ?? null,
                    'responsible_employee_id' => $measurement->responsible_employee_id ?? null,
                    'scheduled_start_date' => $measurement->scheduled_start_date ?? null,
                    'scheduled_start_time' => $measurement->scheduled_start_time ?? null,
                    'scheduled_end_date' => $measurement->scheduled_end_date ?? null,
                    'scheduled_end_time' => $measurement->scheduled_end_time ?? null,
                    'assignment_status' => $measurement->assignment_status ?? null,
                ];

                $description = trim((string) ($validated['description'] ?? ''));
                $title = $this->buildMeasurementWorkTitle($measurement);
                $address = $this->buildMeasurementAddress($measurement);
                $workDescription = $description !== ''
                    ? $description
                    : $this->buildMeasurementDefaultDescription($measurement, $address);

                /*
                 * 1) Appointment sync
                 * - update active appointment if it exists
                 * - if the old appointment was deleted/missing, create a new one
                 * - always save the current deal_measurement_id/other_id so Today Plan and Calendar can reopen the Feinaufmaß
                 */
                $appointment = $this->findActiveMeasurementAppointment($measurement);
                $oldAppointmentExistsButDeleted = $this->measurementAppointmentWasDeleted($measurement);

                $appointmentData = [
                    'created_by' => $appointment?->created_by ?: $authEmployeeId,
                    'name' => $title,
                    'note' => $workDescription,
                    'color' => '#93c21c',

                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,

                    'repeat' => 'none',
                    'appointment_type' => 'feinaufmass',
                    'execution_type' => 'field',
                    'source' => 'deal_measurement',
                    'contact_mode' => 'appointment',

                    'products' => $measurement->product_id ? [(int) $measurement->product_id] : [],

                    'customer_id' => $measurement->customer_id,
                    'branch_id' => $measurement->offer->branch_id ?? null,

                    'street' => $measurement->alternative->street ?? $measurement->customer->street ?? null,
                    'postcode' => $measurement->alternative->postcode ?? $measurement->customer->postcode ?? null,
                    'city' => $measurement->alternative->city ?? $measurement->customer->city ?? null,
                    'full_address' => $address,

                    'email' => $measurement->customer->email ?? null,
                    'phone' => $measurement->customer->phone ?? $measurement->customer->telephone ?? null,

                    'status' => 'open',
                    'priority' => $priority,
                    'public' => 0,
                    'is_notified' => 0,

                    'type' => 'feinaufmass',
                    'other_id' => $measurement->id,
                    'changed_by' => $authEmployeeId,
                ];

                if (Schema::hasColumn('main_appointments', 'deal_measurement_id')) {
                    $appointmentData['deal_measurement_id'] = $measurement->id;
                }

                if ($appointment) {
                    $appointment->forceFill($appointmentData)->save();
                } else {
                    $appointment = MainAppointment::create($appointmentData);
                }

                // Keep only the selected employee as the current responsible employee for this Feinaufmaß appointment.
                MainAppointmentEmployee::query()
                    ->where('appointment_id', $appointment->id)
                    ->where('employee_id', '!=', $employeeId)
                    ->delete();

                MainAppointmentEmployee::updateOrCreate(
                    [
                        'appointment_id' => $appointment->id,
                        'employee_id' => $employeeId,
                    ],
                    [
                        'status' => 'assigned',
                        'reason' => 'Verantwortlich für Feinaufmaß ' . ($measurement->measurement_no ?? '#' . $measurement->id),
                    ]
                );

                /*
                 * 2) Personal task sync
                 * - if create_task is enabled, update active task or recreate it if missing/deleted
                 * - if create_task is disabled, unlink the appointment from the task and clear measurement.personal_task_id
                 */
                $task = $this->findActiveMeasurementPersonalTask($measurement);
                $oldTaskExistsButDeleted = $this->measurementPersonalTaskWasDeleted($measurement);

                if ($createTask) {
                    $taskData = [
                        'task_title' => $title,
                        'description' => $workDescription,
                        'assigned_by' => $task?->assigned_by ?: $authEmployeeId,
                        'task_status' => 'open',
                        'board_column' => $this->resolveTaskBoardColumn($startDate),
                        'priority' => $priority,
                        'color' => '#93c21c',

                        'repeat' => 'none',
                        'public' => 0,
                        'type' => 'feinaufmass',

                        'start_date' => $startDate,
                        'due_date' => $endDate,
                        'due_time' => $endTime,

                        'is_customer' => true,
                        'customer_id' => $measurement->customer_id,
                        'alternative_id' => $measurement->alternative_id,
                        'product_id' => $measurement->product_id,
                        'is_report' => false,
                    ];

                    if (Schema::hasColumn('personal_tasks', 'deal_measurement_id')) {
                        $taskData['deal_measurement_id'] = $measurement->id;
                    }

                    if ($task) {
                        $task->forceFill($taskData)->save();
                    } else {
                        $task = PersonalTask::create($taskData);
                    }

                    // Keep only the selected employee as the current responsible employee for this Feinaufmaß task.
                    EmployeesPersonalTask::query()
                        ->where('task_id', $task->id)
                        ->where('employee_id', '!=', $employeeId)
                        ->delete();

                    EmployeesPersonalTask::updateOrCreate(
                        [
                            'task_id' => $task->id,
                            'employee_id' => $employeeId,
                        ],
                        [
                            'status' => 'assigned',
                            'reason' => 'Feinaufmaß Aufgabe',
                            'note' => $workDescription,
                            'change_date' => now(),
                            'changed_by' => $authEmployeeId,
                            'change_reason' => $oldTaskExistsButDeleted
                                ? 'Feinaufmaß Aufgabe wurde neu erstellt, weil die alte Aufgabe gelöscht wurde'
                                : 'Aus Feinaufmaß erstellt/aktualisiert',
                        ]
                    );

                    $appointment->forceFill([
                        'task_id' => $task->id,
                        'changed_by' => $authEmployeeId,
                    ] + (Schema::hasColumn('main_appointments', 'deal_measurement_id')
                        ? ['deal_measurement_id' => $measurement->id]
                        : []))->save();
                } else {
                    // User intentionally disabled task creation: unlink, but do not delete historical task rows.
                    $appointment->forceFill([
                        'task_id' => null,
                        'changed_by' => $authEmployeeId,
                    ])->save();

                    $task = null;
                }

                /*
                 * 3) Save the new links on the measurement.
                 * This removes stale IDs when old appointment/task rows were deleted and stores the newly created IDs.
                 */
                $assignmentStatus = $createTask ? 'task_created' : 'appointment_created';

                $measurement->forceFill([
                    'appointment_id' => $appointment->id,
                    'personal_task_id' => $task?->id,
                    'responsible_employee_id' => $employeeId,

                    'scheduled_start_date' => $startDate,
                    'scheduled_end_date' => $endDate,
                    'scheduled_start_time' => $startTime,
                    'scheduled_end_time' => $endTime,

                    'assignment_description' => $workDescription,
                    'assignment_status' => $assignmentStatus,
                    'assigned_at' => now(),
                    'assigned_by' => $authEmployeeId,
                    'updated_by' => $authEmployeeId,
                ])->save();

                $newData = [
                    'appointment_id' => $appointment->id,
                    'personal_task_id' => $task?->id,
                    'responsible_employee_id' => $employeeId,
                    'scheduled_start_date' => $startDate,
                    'scheduled_start_time' => $startTime,
                    'scheduled_end_date' => $endDate,
                    'scheduled_end_time' => $endTime,
                    'assignment_status' => $assignmentStatus,
                    'appointment_recreated' => !$oldData['appointment_id'] || $oldAppointmentExistsButDeleted,
                    'task_recreated' => $createTask && (!$oldData['personal_task_id'] || $oldTaskExistsButDeleted),
                ];

                DealMeasurementHistoryLogger::log(
                    measurement: $measurement,
                    action: $createTask
                    ? 'Feinaufmaß als Termin und Aufgabe synchronisiert'
                    : 'Feinaufmaß als Termin synchronisiert',
                    section: 'Zuweisung',
                    field: 'assignment_status',
                    oldValue: $oldData['assignment_status'],
                    newValue: $newData['assignment_status'],
                    changes: [
                        'old' => $oldData,
                        'new' => $newData,
                        'description' => $workDescription,
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => $createTask
                        ? 'Feinaufmaß wurde mit Termin und persönlicher Aufgabe synchronisiert.'
                        : 'Feinaufmaß wurde mit Termin synchronisiert.',
                    'appointment_id' => $appointment->id,
                    'personal_task_id' => $task?->id,
                    'measurement_id' => $measurement->id,
                    'deal_measurement_id' => $measurement->id,
                    'assignment_status' => $assignmentStatus,
                    'appointment_recreated' => !$oldData['appointment_id'] || $oldAppointmentExistsButDeleted,
                    'task_recreated' => $createTask && (!$oldData['personal_task_id'] || $oldTaskExistsButDeleted),
                    'measurement' => $measurement->fresh(),
                    'history' => $this->formatHistories($measurement->fresh()),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Assign deal measurement work failed', [
                'measurement_id' => $measurement->id,
                'request' => $request->except(['_token']),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Feinaufmaß konnte nicht als Termin/Aufgabe geplant werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function findMeasurementAppointment(DealMeasurement $measurement): ?MainAppointment
    {
        return $this->findActiveMeasurementAppointment($measurement);
    }

    private function findActiveMeasurementAppointment(DealMeasurement $measurement): ?MainAppointment
    {
        $appointmentId = $measurement->appointment_id ?? null;

        if ($appointmentId) {
            $appointment = MainAppointment::query()
                ->with(['employees'])
                ->whereKey($appointmentId)
                ->first();

            if ($appointment) {
                return $appointment;
            }
        }

        if (Schema::hasColumn('main_appointments', 'deal_measurement_id')) {
            $appointment = MainAppointment::query()
                ->with(['employees'])
                ->where('deal_measurement_id', $measurement->id)
                ->latest('id')
                ->first();

            if ($appointment) {
                return $appointment;
            }
        }

        return MainAppointment::query()
            ->with(['employees'])
            ->where('source', 'deal_measurement')
            ->where('other_id', $measurement->id)
            ->latest('id')
            ->first();
    }

    private function measurementAppointmentWasDeleted(DealMeasurement $measurement): bool
    {
        $appointmentId = $measurement->appointment_id ?? null;

        if ($appointmentId && method_exists(MainAppointment::class, 'withTrashed')) {
            $appointment = MainAppointment::withTrashed()->whereKey($appointmentId)->first();
            if ($appointment && method_exists($appointment, 'trashed') && $appointment->trashed()) {
                return true;
            }
        }

        if (Schema::hasColumn('main_appointments', 'deal_measurement_id') && method_exists(MainAppointment::class, 'withTrashed')) {
            return MainAppointment::withTrashed()
                ->where('deal_measurement_id', $measurement->id)
                ->whereNotNull('deleted_at')
                ->exists();
        }

        return false;
    }

    private function findMeasurementPersonalTask(DealMeasurement $measurement): ?PersonalTask
    {
        return $this->findActiveMeasurementPersonalTask($measurement);
    }

    private function findActiveMeasurementPersonalTask(DealMeasurement $measurement): ?PersonalTask
    {
        $taskId = $measurement->personal_task_id ?? null;

        if ($taskId) {
            $task = PersonalTask::query()
                ->with(['employees'])
                ->whereKey($taskId)
                ->first();

            if ($task) {
                return $task;
            }
        }

        if (Schema::hasColumn('personal_tasks', 'deal_measurement_id')) {
            $task = PersonalTask::query()
                ->with(['employees'])
                ->where('deal_measurement_id', $measurement->id)
                ->latest('id')
                ->first();

            if ($task) {
                return $task;
            }
        }

        return null;
    }

    private function measurementPersonalTaskWasDeleted(DealMeasurement $measurement): bool
    {
        $taskId = $measurement->personal_task_id ?? null;

        if ($taskId && method_exists(PersonalTask::class, 'withTrashed')) {
            $task = PersonalTask::withTrashed()->whereKey($taskId)->first();
            if ($task && method_exists($task, 'trashed') && $task->trashed()) {
                return true;
            }
        }

        if (Schema::hasColumn('personal_tasks', 'deal_measurement_id') && method_exists(PersonalTask::class, 'withTrashed')) {
            return PersonalTask::withTrashed()
                ->where('deal_measurement_id', $measurement->id)
                ->whereNotNull('deleted_at')
                ->exists();
        }

        return false;
    }

    private function findEmployeeById($employeeId): ?Employee
    {
        if (blank($employeeId)) {
            return null;
        }

        return Employee::query()
            ->whereKey($employeeId)
            ->first();
    }

    private function getAssignableEmployees()
    {
        return Employee::query()
            ->where('status', 'Active')
            ->orderBy('name')
            ->orderBy('lastname')
            ->get(['id', 'name', 'lastname', 'image', 'status']);
    }

    private function buildMeasurementWorkTitle(DealMeasurement $measurement): string
    {
        $number = $measurement->measurement_no ?: ('#' . $measurement->id);
        $product = $measurement->product_label
            ?: ($measurement->product->article_group ?? $measurement->product->name ?? $measurement->product->title ?? null);

        return trim('Feinaufmaß ' . $number . ($product ? ' · ' . $product : ''));
    }

    private function buildMeasurementAddress(DealMeasurement $measurement): ?string
    {
        $alternative = $measurement->alternative;
        $customer = $measurement->customer;

        $address = $alternative->full_address
            ?? trim(
                ($alternative->street ?? '') . ', ' .
                ($alternative->postcode ?? '') . ' ' .
                ($alternative->city ?? '')
            );

        if (trim((string) $address, " \t\n\r\0\x0B,") === '') {
            $address = trim(
                ($customer->street ?? '') . ', ' .
                ($customer->postcode ?? '') . ' ' .
                ($customer->city ?? '')
            );
        }

        return trim((string) $address, " \t\n\r\0\x0B,") ?: null;
    }

    private function buildMeasurementDefaultDescription(DealMeasurement $measurement, ?string $address): string
    {
        $customer = $measurement->customer;
        $product = $measurement->product_label
            ?: ($measurement->product->article_group ?? $measurement->product->name ?? $measurement->product->title ?? '-');

        $customerName = trim(
            ($customer->firma ?? '') . ' ' .
            ($customer->name ?? '') . ' ' .
            ($customer->lastname ?? '')
        );

        return implode("\n", array_filter([
            'Feinaufmaß durchführen.',
            'Aufmaß-Nr.: ' . ($measurement->measurement_no ?? '-'),
            'Angebot-Nr.: ' . ($measurement->offer_no ?? '-'),
            'Auftrag-Nr.: ' . ($measurement->order_number ?? '-'),
            'Kunde: ' . ($customerName ?: '-'),
            'Produkt: ' . $product,
            $address ? 'Adresse: ' . $address : null,
        ]));
    }

    private function resolveTaskBoardColumn(string $date): string
    {
        $today = now()->startOfDay();
        $target = \Carbon\Carbon::parse($date)->startOfDay();

        if ($target->lt($today)) {
            return 'overdue';
        }

        if ($target->isSameDay($today)) {
            return 'today';
        }

        if ($target->betweenIncluded($today->copy()->addDay(), $today->copy()->endOfWeek())) {
            return 'this_week';
        }

        if ($target->betweenIncluded($today->copy()->startOfDay(), $today->copy()->endOfMonth())) {
            return 'this_month';
        }

        return 'later';
    }

    private function authEmployeeId()
    {
        return auth()->user()->name ?? auth()->id();
    }


    public function kanban(Request $request)
    {
        $this->createMissingMeasurementsFromOfferDetails($request);

        $authEmployeeId = (string) (auth()->user()->name ?? auth()->id());
        $scope = (string) $request->input('scope', $request->boolean('mine') ? 'mine' : 'all');
        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $employeeFilter = trim((string) $request->input('employee_id', ''));

        $query = DealMeasurement::query()
            ->with([
                'deal',
                'customer',
                'alternative',
                'alternative.pvWpDetail',
                'alternative.roofs',
                'product',
                'items',
                'offer',
                'detail',
                'editDetail',
                'histories',
            ])
            ->withCount('items')
            ->when($status !== '', function ($query) use ($status) {
                if ($status === 'not_assigned') {
                    $query->where(function ($q) {
                        $q->whereNull('assignment_status')
                            ->orWhere('assignment_status', '')
                            ->orWhere('assignment_status', 'not_assigned');
                    });
                } else {
                    $query->where('assignment_status', $status);
                }
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('measurement_no', 'like', "%{$search}%")
                        ->orWhere('order_number', 'like', "%{$search}%")
                        ->orWhere('offer_no', 'like', "%{$search}%")
                        ->orWhere('assignment_description', 'like', "%{$search}%")
                        ->orWhere('note', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('lastname', 'like', "%{$search}%")
                                ->orWhere('firma', 'like', "%{$search}%")
                                ->orWhere('city', 'like', "%{$search}%")
                                ->orWhere('street', 'like', "%{$search}%");
                        })
                        ->orWhereHas('alternative', function ($alternativeQuery) use ($search) {
                            $alternativeQuery
                                ->where('object_name', 'like', "%{$search}%")
                                ->orWhere('full_address', 'like', "%{$search}%")
                                ->orWhere('city', 'like', "%{$search}%")
                                ->orWhere('street', 'like', "%{$search}%")
                                ->orWhere('postcode', 'like', "%{$search}%");
                        })
                        ->orWhereHas('product', function ($productQuery) use ($search) {
                            $productQuery
                                ->where('article_group', 'like', "%{$search}%")
                                ->orWhere('product', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->orWhere('title', 'like', "%{$search}%")
                                ->orWhere('article_no', 'like', "%{$search}%")
                                ->orWhere('sku', 'like', "%{$search}%");
                        });
                });
            });

        if ($scope === 'mine') {
            $this->applyMeasurementEmployeeScope($query, $authEmployeeId);
        }

        if ($employeeFilter !== '') {
            $this->applyMeasurementEmployeeScope($query, $employeeFilter);
        }

        $measurements = $query
            ->latest('updated_at')
            ->latest('id')
            ->get();

        foreach ($measurements as $measurement) {
            $measurement->setRelation('appointmentRecord', $this->findMeasurementAppointment($measurement));
            $measurement->setRelation('personalTaskRecord', $this->findMeasurementPersonalTask($measurement));
            $measurement->setRelation('responsibleEmployeeRecord', $this->findEmployeeById($measurement->responsible_employee_id ?? null));
        }

        $employeeMap = $this->buildEmployeeMap($measurements);
        $employees = $this->getAssignableEmployees();

        return view('admin.deal_measurements.kanban', compact('measurements', 'employeeMap', 'employees'));
    }

    private function applyMeasurementEmployeeScope($query, string $employeeId): void
    {
        $query->where(function ($q) use ($employeeId) {
            $q->where('responsible_employee_id', $employeeId)
                ->orWhere('created_by', $employeeId)
                ->orWhere('updated_by', $employeeId)
                ->orWhere('sent_by', $employeeId)
                ->orWhere('assigned_by', $employeeId);

            if (Schema::hasTable('main_appointment_employees') && Schema::hasTable('main_appointments')) {
                $q->orWhereExists(function ($sub) use ($employeeId) {
                    $sub->selectRaw('1')
                        ->from('main_appointment_employees as mae')
                        ->join('main_appointments as ma', 'ma.id', '=', 'mae.appointment_id')
                        ->where(function ($link) {
                            $link->whereColumn('ma.other_id', 'deal_measurements.id')
                                ->orWhereColumn('ma.id', 'deal_measurements.appointment_id');
                        })
                        ->where(function ($source) {
                            $source->where('ma.source', 'deal_measurement')
                                ->orWhere('ma.type', 'feinaufmass')
                                ->orWhere('ma.appointment_type', 'feinaufmass');
                        })
                        ->where('mae.employee_id', $employeeId);
                });
            }

            if (Schema::hasTable('employees_personal_tasks') && Schema::hasTable('personal_tasks')) {
                $q->orWhereExists(function ($sub) use ($employeeId) {
                    $sub->selectRaw('1')
                        ->from('employees_personal_tasks as ept')
                        ->join('personal_tasks as pt', 'pt.id', '=', 'ept.task_id')
                        ->where(function ($link) {
                            $link->whereColumn('pt.id', 'deal_measurements.personal_task_id')
                                ->orWhereColumn('pt.customer_id', 'deal_measurements.customer_id');
                        })
                        ->where(function ($type) {
                            $type->where('pt.type', 'feinaufmass')
                                ->orWhereColumn('pt.id', 'deal_measurements.personal_task_id');
                        })
                        ->where('ept.employee_id', $employeeId);
                });
            }
        });
    }

    public function updateKanbanStatus(Request $request, DealMeasurement $measurement)
    {
        $validator = Validator::make($request->all(), [
            'assignment_status' => [
                'required',
                'string',
                Rule::in([
                    'not_assigned',
                    'appointment_created',
                    'task_created',
                    'completed',
                    'canceled',
                ])
            ],
        ], [
            'assignment_status.required' => 'Bitte wählen Sie einen Kanban Status.',
            'assignment_status.in' => 'Dieser Kanban Status ist ungültig.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler.',
                'errors' => $validator->errors(),
            ], 422);
        }

        /*
         * Do not use $measurement->status here. The normal Aufmaß status can be
         * completed while the planning/assignment Kanban is still not planned.
         * Only block moving away from the Kanban completed column.
         */
        $currentAssignmentStatus = (string) ($measurement->assignment_status ?: 'not_assigned');
        $targetAssignmentStatus = (string) $request->input('assignment_status');

        if ($currentAssignmentStatus === 'completed' && $targetAssignmentStatus !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Dieses Aufmaß ist im Kanban als abgeschlossen markiert. Bitte zuerst entsperren.',
            ], 423);
        }

        try {
            return DB::transaction(function () use ($request, $measurement) {
                $oldStatus = $measurement->assignment_status ?: 'not_assigned';
                $newStatus = (string) $request->input('assignment_status');

                $update = [
                    'assignment_status' => $newStatus,
                    'updated_by' => auth()->user()->name ?? auth()->id(),
                ];

                if ($newStatus === 'completed') {
                    $update['status'] = 'completed';
                }

                if ($newStatus === 'canceled' && $measurement->status !== 'completed') {
                    $update['status'] = 'canceled';
                }

                if ($newStatus === 'not_assigned' && in_array($measurement->status, ['canceled'], true)) {
                    $update['status'] = 'draft';
                }

                $measurement->forceFill($update)->save();

                if ($newStatus === 'canceled' && !empty($measurement->appointment_id)) {
                    MainAppointment::query()
                        ->whereKey($measurement->appointment_id)
                        ->update([
                            'status' => 'canceled',
                            'changed_by' => auth()->user()->name ?? auth()->id(),
                            'updated_at' => now(),
                        ]);
                }

                DealMeasurementHistoryLogger::log(
                    measurement: $measurement,
                    action: 'Kanban Status geändert',
                    section: 'Kanban',
                    field: 'assignment_status',
                    oldValue: $oldStatus,
                    newValue: $newStatus,
                    changes: [
                        'old' => ['assignment_status' => $oldStatus],
                        'new' => ['assignment_status' => $newStatus],
                        'changed_by' => auth()->user()->name ?? auth()->id(),
                        'changed_at' => now()->toDateTimeString(),
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Kanban Status wurde aktualisiert.',
                    'assignment_status' => $newStatus,
                    'measurement' => $measurement->fresh(),
                    'history' => $this->formatHistories($measurement->fresh()),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Update deal measurement kanban status failed', [
                'measurement_id' => $measurement->id,
                'message' => $e->getMessage(),
                'request' => $request->except(['_token']),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Kanban Status konnte nicht aktualisiert werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function storeNote(Request $request, DealMeasurement $measurement)
    {
        $validator = Validator::make($request->all(), [
            'note' => ['required', 'string', 'max:5000'],
        ], [
            'note.required' => 'Bitte schreiben Sie zuerst eine Notiz.',
            'note.max' => 'Die Notiz darf maximal 5000 Zeichen lang sein.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request, $measurement) {
                $noteText = trim((string) $request->input('note'));
                $oldNote = (string) ($measurement->note ?? '');

                $entryHeader = '[' . now()->format('d.m.Y H:i') . '] ' . (auth()->user()->name ?? auth()->id() ?? 'System');
                $newCombinedNote = trim($oldNote . ($oldNote !== '' ? "\n\n" : '') . $entryHeader . "\n" . $noteText);

                $measurement->forceFill([
                    'note' => $newCombinedNote,
                    'updated_by' => auth()->user()->name ?? auth()->id(),
                ])->save();

                DealMeasurementHistoryLogger::log(
                    measurement: $measurement,
                    action: 'Notiz hinzugefügt',
                    section: 'Notiz',
                    field: 'note',
                    oldValue: null,
                    newValue: $noteText,
                    changes: [
                        'note' => [
                            'old' => null,
                            'new' => $noteText,
                        ],
                    ]
                );

                $employee = $this->findEmployeeById(auth()->user()->name ?? auth()->id());

                $employeeName = $employee
                    ? trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))
                    : 'System';

                if ($employeeName === '') {
                    $employeeName = 'Mitarbeiter #' . (auth()->user()->name ?? auth()->id());
                }

                $image = $employee->image ?? null;

                if ($image) {
                    if (
                        str_starts_with($image, 'http://') ||
                        str_starts_with($image, 'https://') ||
                        str_starts_with($image, 'data:')
                    ) {
                        $imageUrl = $image;
                    } else {
                        $imageUrl = asset('images/employee/' . ltrim($image, '/'));
                    }
                } else {
                    $imageUrl = asset('images/icons/placeholder.svg');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Notiz wurde gespeichert.',
                    'note' => [
                        'id' => 'note-' . now()->timestamp,
                        'text' => $noteText,
                        'user' => $employeeName,
                        'userId' => auth()->user()->name ?? auth()->id(),
                        'userImage' => $imageUrl,
                        'date' => now()->toISOString(),
                    ],
                    'history' => $this->formatHistories($measurement->fresh()),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Store deal measurement note failed', [
                'measurement_id' => $measurement->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Notiz konnte nicht gespeichert werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateItem(Request $request, DealMeasurementItem $item)
    {
        if ($item->measurement) {
            Gate::authorize('write', $item->measurement); // S-1a Ownership
        }

        $validator = Validator::make($request->all(), [
            'qty_measurement' => ['nullable', 'numeric', 'min:0'],
            'qty_final' => ['nullable', 'numeric', 'min:0'],
            'is_checked' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldData = $item->only([
            'qty_measurement',
            'qty_final',
            'is_checked',
            'note',
        ]);

        $data = [];

        if ($request->has('qty_measurement')) {
            $data['qty_measurement'] = $request->qty_measurement;
        }

        if ($request->has('qty_final')) {
            $data['qty_final'] = $request->qty_final;
        }

        if ($request->has('is_checked')) {
            $data['is_checked'] = $request->boolean('is_checked');
        }

        if ($request->has('note')) {
            $data['note'] = $request->note;
        }

        $data['updated_by'] = auth()->user()->name ?? auth()->id();

        $item->update($data);

        $newData = $item->fresh()->only([
            'qty_measurement',
            'qty_final',
            'is_checked',
            'note',
        ]);

        $measurement = $item->measurement ?? DealMeasurement::find($item->deal_measurement_id);

        if ($measurement) {
            DealMeasurementHistoryLogger::logChanges(
                measurement: $measurement,
                action: 'Materialposition geändert: ' . ($item->name ?: 'Unbenannt'),
                section: 'Materialposition',
                oldData: $oldData,
                newData: $newData
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Position wurde aktualisiert.',
            'item' => $item->fresh(),
        ]);
    }



    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([
                'success' => true,
                'products' => [],
            ]);
        }

        $products = Product::query()
            ->with([
                'firstImage:id,product_id,image,name',
                'inventory:id,product_id,quantity,location,location_label,room_name,rack_name,shelf',
                'measureUnit:id,measure',
                'measure:id,measure',
            ])
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '!=', 'deleted');
            })
            ->where(function ($sub) use ($q) {
                $sub->where('product', 'like', "%{$q}%")
                    ->orWhere('model', 'like', "%{$q}%")
                    ->orWhere('article_no', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('ean', 'like', "%{$q}%")
                    ->orWhere('short_description', 'like', "%{$q}%")
                    ->orWhere('article_group', 'like', "%{$q}%");
            })
            ->orderByRaw("
                CASE
                    WHEN product LIKE ? THEN 0
                    WHEN article_no LIKE ? THEN 1
                    WHEN sku LIKE ? THEN 2
                    WHEN ean LIKE ? THEN 3
                    ELSE 4
                END
            ", ["{$q}%", "{$q}%", "{$q}%", "{$q}%"])
            ->limit(25)
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products->map(function (Product $product) {
                $image = optional($product->firstImage)->image;

                if ($image) {
                    if (
                        str_starts_with($image, 'http://') ||
                        str_starts_with($image, 'https://') ||
                        str_starts_with($image, 'data:')
                    ) {
                        $imageUrl = $image;
                    } else {
                        $imageUrl = asset('images/products/' . ltrim($image, '/'));
                    }
                } else {
                    $imageUrl = asset('images/icons/placeholder.svg');
                }

                $stockQty = optional($product->inventory)->quantity ?? 0;

                return [
                    'id' => $product->id,
                    'article_no' => $product->article_no,
                    'sku' => $product->sku,
                    'ean' => $product->ean,

                    'name' => $product->product
                        ?: $product->model
                        ?: $product->article_group
                        ?: 'Unbenannt',

                    'model' => $product->model,
                    'category' => $product->category,
                    'article_group' => $product->article_group,
                    'short_description' => strip_tags((string) $product->short_description),

                    'unit' => optional($product->measureUnit)->measure
                        ?: optional($product->measure)->measure
                        ?: $product->measure_unit
                        ?: 'Stk',

                    'image' => $imageUrl,

                    'stock_qty' => (float) $stockQty,
                    'stock_label' => $stockQty > 0 ? 'Auf Lager' : 'Nicht auf Lager',

                    'location' => optional($product->inventory)->location,
                    'location_label' => optional($product->inventory)->location_label,
                    'room_name' => optional($product->inventory)->room_name,
                    'rack_name' => optional($product->inventory)->rack_name,
                    'shelf' => optional($product->inventory)->shelf,
                ];
            })->values(),
        ]);
    }

    private function findOfferDetailForDeal(Deal $deal): ?OfferDetail
    {
        if ($deal->offer_id && $deal->offer_folder_id) {
            $detail = OfferDetail::query()
                ->where('offer_id', $deal->offer_id)
                ->where('offer_folder_id', $deal->offer_folder_id)
                ->latest('id')
                ->first();

            if ($detail) {
                return $detail;
            }
        }

        if ($deal->offer_id) {
            $detail = OfferDetail::query()
                ->where('offer_id', $deal->offer_id)
                ->latest('id')
                ->first();

            if ($detail) {
                return $detail;
            }
        }

        return OfferDetail::query()
            ->whereHas('offer', function ($q) use ($deal) {
                $q->where('customer_id', $deal->customer_id)
                    ->where('product_id', $deal->product_id)
                    ->where('alternative_id', $deal->alternative_id);
            })
            ->latest('id')
            ->first();
    }

    private function extractMaterialRows(mixed $sections): array
    {
        $sections = $this->normalizeArray($sections);
        $rows = [];

        foreach ($sections as $section) {
            if (!is_array($section)) {
                continue;
            }

            $sectionTitle = $section['title'] ?? 'Ohne Bereich';

            foreach (($section['items'] ?? []) as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $this->collectMaterialRows(
                    rows: $rows,
                    item: $item,
                    sectionTitle: $sectionTitle,
                    depth: 0,
                    parentMasterSetId: null,
                    parentQtyMultiplier: 1.0
                );
            }
        }

        return collect($rows)
            ->filter(fn($row) => ($row['kind'] ?? null) !== 'labor')
            ->values()
            ->all();
    }

    private function collectMaterialRows(
        array &$rows,
        array $item,
        ?string $sectionTitle,
        int $depth,
        ?int $parentMasterSetId,
        float $parentQtyMultiplier = 1.0
    ): void {
        $kind = $item['kind'] ?? null;
        $itemType = $item['item_type'] ?? null;

        $isMasterSet = $itemType === 'master_set';
        $isMasterSetComponent = $itemType === 'master_set_component';

        $ownQty = (float) (
            $item['qty_unit']
            ?? $item['unit_qty']
            ?? $item['qty']
            ?? $item['qty_offer']
            ?? 0
        );

        /*
         * Important:
         * - Master set itself keeps its own qty, e.g. 3 Set.
         * - Master set components must be multiplied by parent set qty.
         *
         * Example:
         * Set qty = 3
         * Component qty = 3
         * Component total = 9
         */
        if (array_key_exists('qty_total', $item) && is_numeric($item['qty_total'])) {
            $totalQty = (float) $item['qty_total'];
        } elseif ($isMasterSet) {
            $totalQty = $ownQty;
        } elseif ($isMasterSetComponent) {
            $totalQty = $ownQty * $parentQtyMultiplier;
        } else {
            $totalQty = $ownQty * $parentQtyMultiplier;
        }

        $currentMasterSetId = $isMasterSet
            ? ($item['product_id'] ?? $item['productId'] ?? null)
            : $parentMasterSetId;

        $unitPrice = (float) ($item['price'] ?? $item['unit_price'] ?? 0);

        if ($kind !== 'labor') {
            $rows[] = [
                'section_title' => $sectionTitle,
                'depth' => $depth,

                'item_type' => $itemType,
                'kind' => $kind,

                'master_set_id' => $currentMasterSetId,
                'master_set_component_id' => $item['component_id'] ?? null,
                'product_id' => $item['product_id'] ?? $item['productId'] ?? null,
                'component_id' => $item['component_id'] ?? null,

                'article_no' => $item['article_no'] ?? null,
                'distributor_article_no' => $item['distributor_article_no'] ?? null,
                'distributor_id' => $item['distributor_id'] ?? null,
                'distributor_name' => $item['distributor_name'] ?? $item['supplier'] ?? null,

                'name' => $item['name'] ?? 'Unbenannt',
                'description' => $item['desc_html'] ?? $item['desc'] ?? $item['description'] ?? null,
                'image' => $item['img'] ?? $item['image'] ?? null,

                /*
                 * qty_offer is now the REAL total quantity.
                 */
                'qty_offer' => $totalQty,

                /*
                 * Helpful debug values, stored in material_summary/raw style data.
                 */
                'qty_unit' => $ownQty,
                'qty_total' => $totalQty,
                'parent_qty_multiplier' => $parentQtyMultiplier,

                'unit' => $item['unit'] ?? $item['measure'] ?? 'Stk',
                'measure' => $item['measure'] ?? null,

                'unit_price' => $unitPrice,
                'purchase_price' => (float) ($item['purchase_price'] ?? $item['ek'] ?? 0),
                'total_price' => $totalQty * $unitPrice,

                'order_status' => $item['order_status'] ?? null,
                'stock_allocation' => $item['stock_allocation'] ?? null,

                'raw_snapshot' => array_merge($item, [
                    '_calculated_qty_unit' => $ownQty,
                    '_calculated_qty_total' => $totalQty,
                    '_parent_qty_multiplier' => $parentQtyMultiplier,
                ]),
            ];
        }

        /*
         * Children of a master set must receive the master set total quantity as multiplier.
         *
         * Example:
         * master set qty 3 -> child multiplier 3
         */
        $childQtyMultiplier = $isMasterSet
            ? $totalQty
            : $parentQtyMultiplier;

        foreach (($item['subItems'] ?? []) as $subItem) {
            if (!is_array($subItem)) {
                continue;
            }

            $this->collectMaterialRows(
                rows: $rows,
                item: $subItem,
                sectionTitle: $sectionTitle,
                depth: $depth + 1,
                parentMasterSetId: $currentMasterSetId ? (int) $currentMasterSetId : null,
                parentQtyMultiplier: $childQtyMultiplier
            );
        }
    }

    private function normalizeArray(mixed $value): array
    {
        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    public function saveDetail(Request $request, DealMeasurement $measurement)
    {
        if ($measurement->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Dieses Aufmaß ist abgeschlossen und gesperrt. Bitte zuerst entsperren.',
            ], 423);
        }

        $validated = $request->validate([
            'type' => ['required', 'string', 'in:PV,WP,OTHER'],
            'data' => ['required', 'array'],
        ]);


        return DB::transaction(function () use ($validated, $measurement) {
            $type = $validated['type'];
            $data = $validated['data'];

            $roofs = $data['roofs'] ?? [];
            unset($data['roofs']);

            $oldDetail = DealMeasurementDetail::query()
                ->where('deal_measurement_id', $measurement->id)
                ->first();

            $oldFormData = $oldDetail ? $this->normalizeArray($oldDetail->form_data) : [];
            $oldRoofData = $oldDetail ? $this->normalizeArray($oldDetail->roof_data) : [];

            $formChangeCount = DealMeasurementHistoryLogger::logChanges(
                measurement: $measurement,
                action: $type . ' Formular geändert',
                section: $type,
                oldData: $oldFormData,
                newData: $data
            );

            $roofChangeCount = 0;

            if ($type === 'PV') {
                $roofChangeCount = DealMeasurementHistoryLogger::logChanges(
                    measurement: $measurement,
                    action: 'Dachflächen geändert',
                    section: 'Dachflächen',
                    oldData: ['roofs' => $oldRoofData],
                    newData: ['roofs' => is_array($roofs) ? array_values($roofs) : []]
                );
            }

            $detail = DealMeasurementDetail::updateOrCreate(
                [
                    'deal_measurement_id' => $measurement->id,
                ],
                [
                    'deal_id' => $measurement->deal_id,
                    'offer_id' => $measurement->offer_id,
                    'offer_detail_id' => $measurement->offer_detail_id,
                    'type' => $type,

                    'form_data' => $data,
                    'roof_data' => is_array($roofs) ? array_values($roofs) : [],

                    'pv_data' => $type === 'PV' ? $data : null,
                    'wp_data' => $type === 'WP' ? $data : null,

                    'raw_snapshot' => [
                        'type' => $type,
                        'data' => array_merge($data, [
                            'roofs' => $roofs,
                        ]),
                    ],

                    'updated_by' => auth()->user()->name ?? auth()->id(),
                    'saved_at' => now(),
                ]
            );

            $measurement->update([
                'updated_by' => auth()->user()->name ?? auth()->id(),
            ]);

            if ($formChangeCount === 0 && $roofChangeCount === 0) {
                DealMeasurementHistoryLogger::log(
                    measurement: $measurement,
                    action: $type . ' Formular gespeichert ohne Änderung',
                    section: $type
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Feinaufmaß wurde gespeichert.',
                'detail' => $detail,
                'data' => array_merge($data, [
                    'roofs' => $roofs,
                ]),
                'history' => $this->formatHistories($measurement),
            ]);
        });
    }

    private function formatHistories(DealMeasurement $measurement): array
    {
        return $measurement->histories()
            ->latest()
            ->limit(150)
            ->get()
            ->map(function ($history) {
                return [
                    'id' => $history->id,
                    'action' => $history->action,
                    'section' => $history->section,
                    'field' => $history->field,
                    'oldValue' => $history->old_value,
                    'newValue' => $history->new_value,
                    'changes' => $history->changes ?: [],
                    'user' => $history->created_by ?: 'System',
                    'date' => optional($history->created_at)->toISOString(),
                ];
            })
            ->values()
            ->toArray();
    }

    public function complete(Request $request, DealMeasurement $measurement)
    {
        try {
            return DB::transaction(function () use ($measurement) {
                if ($measurement->status === 'completed') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Aufmaß ist bereits abgeschlossen.',
                        'status' => 'completed',
                    ]);
                }

                $oldStatus = $measurement->status;

                $completeUpdate = [
                    'status' => 'completed',
                    'updated_by' => auth()->user()->name ?? auth()->id(),
                ];

                if (Schema::hasColumn('deal_measurements', 'assignment_status')) {
                    $completeUpdate['assignment_status'] = 'completed';
                }

                $measurement->update($completeUpdate);

                DealMeasurementHistoryLogger::log(
                    measurement: $measurement,
                    action: 'Feinaufmaß abgeschlossen und gesperrt',
                    section: 'Sperre',
                    field: 'status',
                    oldValue: $oldStatus,
                    newValue: 'completed',
                    changes: [
                        'locked_by' => auth()->user()->name ?? auth()->id(),
                        'locked_at' => now()->toDateTimeString(),
                    ]
                );

                if ($measurement->deal_id) {
                    $deal = Deal::find($measurement->deal_id);

                    if ($deal) {
                        $deal->update([
                            'measurement_status' => 'completed',
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Aufmaß wurde abgeschlossen und gesperrt.',
                    'status' => 'completed',
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Error completing measurement', [
                'measurement_id' => $measurement->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Es gab ein Problem beim Abschließen des Aufmaßes.',
            ], 500);
        }
    }
    private function buildEmployeeMap($measurements): array
    {
        $ids = collect();

        foreach ($measurements as $measurement) {
            $ids->push($measurement->created_by);
            $ids->push($measurement->updated_by);
            $ids->push($measurement->sent_by);
            $ids->push($measurement->responsible_employee_id ?? null);
            $ids->push($measurement->assigned_by ?? null);

            if ($measurement->editDetail) {
                $ids->push($measurement->editDetail->updated_by);
            }

            foreach ($measurement->histories ?? [] as $history) {
                $ids->push($history->created_by);
                $ids->push($history->updated_by);
                $ids->push($history->user_id);
            }
        }

        $ids = $ids
            ->filter()
            ->map(fn($id) => (string) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        return Employee::query()
            ->whereIn('id', $ids)
            ->get()
            ->mapWithKeys(function ($employee) {
                $name = trim(
                    ($employee->firstname ?? '') . ' ' .
                    ($employee->name ?? '') . ' ' .
                    ($employee->lastname ?? '')
                );

                if ($name === '') {
                    $name = $employee->full_name
                        ?? $employee->display_name
                        ?? $employee->email
                        ?? ('Mitarbeiter #' . $employee->id);
                }

                $image = $employee->image
                    ?? $employee->profile_image
                    ?? $employee->picture
                    ?? $employee->photo
                    ?? null;

                if ($image) {
                    if (
                        str_starts_with($image, 'http://') ||
                        str_starts_with($image, 'https://') ||
                        str_starts_with($image, 'data:')
                    ) {
                        $imageUrl = $image;
                    } else {
                        $imageUrl = asset('images/employee/' . ltrim($image, '/'));
                    }
                } else {
                    $imageUrl = asset('images/icons/placeholder.svg');
                }

                return [
                    (string) $employee->id => [
                        'id' => (string) $employee->id,
                        'name' => $name,
                        'image' => $imageUrl,
                    ],
                ];
            })
            ->toArray();
    }

    public function unlock(Request $request, DealMeasurement $measurement)
    {
        try {
            return DB::transaction(function () use ($measurement) {
                if ($measurement->status !== 'completed') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Aufmaß ist bereits entsperrt.',
                        'status' => $measurement->status ?: 'open',
                    ]);
                }

                $oldStatus = $measurement->status;

                $unlockUpdate = [
                    'status' => 'open',
                    'updated_by' => auth()->user()->name ?? auth()->id(),
                ];

                if (Schema::hasColumn('deal_measurements', 'assignment_status')) {
                    $unlockUpdate['assignment_status'] = 'not_assigned';
                }

                $measurement->update($unlockUpdate);

                DealMeasurementHistoryLogger::log(
                    measurement: $measurement,
                    action: 'Feinaufmaß entsperrt',
                    section: 'Sperre',
                    field: 'status',
                    oldValue: $oldStatus,
                    newValue: 'open',
                    changes: [
                        'unlocked_by' => auth()->user()->name ?? auth()->id(),
                        'unlocked_at' => now()->toDateTimeString(),
                    ]
                );

                if ($measurement->deal_id) {
                    $deal = Deal::find($measurement->deal_id);

                    if ($deal) {
                        $deal->update([
                            'measurement_status' => 'open',
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Aufmaß wurde entsperrt.',
                    'status' => 'open',
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Error unlocking measurement', [
                'measurement_id' => $measurement->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Aufmaß konnte nicht entsperrt werden.',
            ], 500);
        }
    }

    public function quickView(DealMeasurement $measurement)
    {
        $measurement->loadMissing([
            'deal',
            'customer',
            'alternative',
            'product',
            'offer',
            'items',
            'histories',
            'detail',
            'editDetail',
        ]);

        $address = trim(implode(' ', array_filter([
            $measurement->alternative->object_name ?? null,
            $measurement->alternative->street ?? $measurement->customer->street ?? null,
            $measurement->alternative->postcode ?? $measurement->customer->postcode ?? null,
            $measurement->alternative->city ?? $measurement->customer->city ?? null,
        ])));

        $customerName = trim(implode(' ', array_filter([
            $measurement->customer->firma ?? null,
            $measurement->customer->name ?? null,
            $measurement->customer->lastname ?? null,
        ])));

        $productName = $measurement->product_label
            ?? $measurement->product->article_group
            ?? $measurement->product->name
            ?? $measurement->product->title
            ?? null;

        return response()->json([
            'success' => true,
            'measurement' => [
                'id' => $measurement->id,
                'measurement_no' => $measurement->measurement_no,
                'status' => $measurement->status,
                'assignment_status' => $measurement->assignment_status,
                'order_number' => $measurement->order_number,
                'offer_no' => $measurement->offer_no,
                'customer' => $customerName ?: '—',
                'address' => $address ?: '—',
                'product' => $productName ?: '—',
                'scheduled_start_date' => optional($measurement->scheduled_start_date)->format('Y-m-d') ?: $measurement->scheduled_start_date,
                'scheduled_start_time' => $measurement->scheduled_start_time,
                'scheduled_end_date' => optional($measurement->scheduled_end_date)->format('Y-m-d') ?: $measurement->scheduled_end_date,
                'scheduled_end_time' => $measurement->scheduled_end_time,
                'description' => $measurement->assignment_description ?: $measurement->note,
                'items_count' => $measurement->items->count(),
                'materials_total_count' => (int) ($measurement->materials_total_count ?? $measurement->items->count()),
                'materials_approved_count' => (int) ($measurement->materials_approved_count ?? 0),
                'deal_id' => $measurement->deal_id,
                'appointment_id' => $measurement->appointment_id,
                'personal_task_id' => $measurement->personal_task_id,
                'profile_url' => route('deal.measurements.show', $measurement),
                'deal_url' => $measurement->deal_id ? route('deal.profile', $measurement->deal_id) . '?tab=measurement' : null,
            ],
            'history' => method_exists($this, 'formatHistories') ? $this->formatHistories($measurement) : [],
        ]);
    }

    public function destroy(DealMeasurement $measurement)
    {
        if ($measurement->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Dieses Aufmaß ist abgeschlossen und gesperrt. Bitte zuerst entsperren.',
            ], 423);
        }

        try {
            return DB::transaction(function () use ($measurement) {
                DealMeasurementHistoryLogger::log(
                    measurement: $measurement,
                    action: 'Feinaufmaß gelöscht',
                    section: 'System',
                    changes: [
                        'deleted_by' => auth()->user()->name ?? auth()->id(),
                        'deleted_at' => now()->toDateTimeString(),
                    ]
                );

                $measurement->items()->delete();
                $measurement->editDetail()->delete();
                $measurement->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Aufmaß wurde gelöscht.',
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Delete deal measurement failed', [
                'measurement_id' => $measurement->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Aufmaß konnte nicht gelöscht werden.',
            ], 500);
        }
    }


}