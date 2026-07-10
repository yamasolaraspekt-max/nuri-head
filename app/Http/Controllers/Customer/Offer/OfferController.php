<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Events\OfferFolderActivityCreated;
use App\Events\OfferFolderUpdated;
use App\Events\OffersChanged;
use App\Http\Controllers\Controller;
use App\Models\LeadProductList;
use App\Models\Offer;
use App\Models\Employee;
use App\Models\OfferAssetList;
use App\Models\OfferDeleteLog;
use App\Models\OfferDetail;
use App\Models\OfferFolder;
use App\Models\OfferFolderActivity;
use App\Models\OfferFolderAttachment;
use App\Models\OfferPdfPrint;
use App\Models\OfferProductList;
use App\Models\OfferTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class OfferController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,update')->only(['update', 'updateFolder']);
        $this->middleware('permission:Customer,delete')->only(['destroy', 'destroyFolder']);
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.offer.index');
    }

    /**
     * Open an offer by redirecting to its primary folder detail page.
     *
     * Offers are viewed and edited through their folders. OfferWizardController@createOffer
     * already redirects to admin.offers.folders.show after creating an offer, and the smart
     * wizard uses "/offers/{id}" as a fallback navigation target. Without this action the
     * registered GET /offers/{offer} route resolves to a missing controller method and crashes
     * with a 500 (BadMethodCallException). This mirrors the existing create flow instead.
     */
    public function show(Offer $offer)
    {
        $folder = $offer->folders()->orderBy('id')->first();

        if ($folder) {
            return redirect()->route('admin.offers.folders.show', $folder->id);
        }

        return redirect()
            ->route('admin.offers.index')
            ->with('error', 'Zu diesem Angebot wurde kein Ordner gefunden.');
    }

    protected function prepareJsonResponse(): void
    {
        if (app()->bound('debugbar')) {
            try {
                app('debugbar')->disable();
            } catch (Throwable $e) {
                //
            }
        }
    }

    protected function employeeId(): ?int
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if (is_numeric($user->name ?? null)) {
            return (int) $user->name;
        }

        if (!empty($user->employee_id) && is_numeric($user->employee_id)) {
            return (int) $user->employee_id;
        }

        if ($user->employee?->id) {
            return (int) $user->employee->id;
        }

        return is_numeric($user->id ?? null) ? (int) $user->id : null;
    }

    protected function currentEmployeeIdFromAuthName(): ?int
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        return is_numeric($user->name ?? null)
            ? (int) $user->name
            : $this->employeeId();
    }

    protected function successResponse(array $payload = [], int $status = 200): JsonResponse
    {
        return response()->json($payload, $status, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }

    protected function errorResponse(string $message, int $status = 500, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
        ], $extra), $status, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }

    protected function validateFolderPayload(Request $request)
    {
        return Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);
    }

    protected function createFolderActivity(
        OfferFolder $folder,
        string $type,
        string $title,
        ?string $message = null,
        array $meta = []
    ): OfferFolderActivity {
        $activity = OfferFolderActivity::create([
            'offer_folder_id' => $folder->id,
            'offer_id' => $folder->offer_id,
            'employee_id' => $this->employeeId(),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'meta' => !empty($meta) ? $meta : null,
        ]);

        $activity->load('employee');

        return $activity;
    }

    protected function broadcastFolderChange(
        OfferFolder $folder,
        string $action,
        ?OfferFolderActivity $activity = null
    ): void {
        try {
            if ($activity) {
                broadcast(new OfferFolderActivityCreated($activity))->toOthers();
            }

            broadcast(new OfferFolderUpdated($folder, $action, [
                'folder_id' => $folder->id,
                'offer_id' => $folder->offer_id,
            ]))->toOthers();

            broadcast(new OffersChanged([
                'type' => 'folder_' . $action,
                'folder_id' => $folder->id,
                'offer_id' => $folder->offer_id,
            ]))->toOthers();
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function broadcastOfferChange(string $type, ?Offer $offer = null): void
    {
        try {
            broadcast(new OffersChanged([
                'type' => $type,
                'offer_id' => $offer?->id,
            ]))->toOthers();
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function findExistingOffer(
        int $customerId,
        int $alternativeId,
        int $productId,
        ?int $ignoreOfferId = null,
        bool $withTrashed = false
    ): ?Offer {
        $query = $withTrashed ? Offer::withTrashed() : Offer::query();

        return $query
            ->when($ignoreOfferId, fn($q) => $q->where('id', '!=', $ignoreOfferId))
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->with([
                'customer',
                'alternative',
                'product',
                'creator',
                'folders.creator',
            ])
            ->first();
    }

    protected function findExistingOfferForUpdate(
        int $customerId,
        int $alternativeId,
        int $productId,
        ?int $ignoreOfferId = null
    ): ?Offer {
        return Offer::withTrashed()
            ->when($ignoreOfferId, fn($q) => $q->where('id', '!=', $ignoreOfferId))
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();
    }

    protected function safeGenerateOfferNo(?int $customerId = null, ?int $departmentId = null, ?string $forcedBasePrefix = null, ?int $ignoreOfferId = null): string
    {
        return DB::transaction(function () use ($customerId, $departmentId, $forcedBasePrefix, $ignoreOfferId) {
            $basePrefix = $forcedBasePrefix;

            if (!$basePrefix) {
                $branchPrefix = Offer::resolveOfferPrefix($customerId, $departmentId);
                $basePrefix = $branchPrefix . '-AN' . date('y');
            }

            $lastOfferNo = Offer::withTrashed()
                ->where('offer_no', 'like', $basePrefix . '%')
                ->lockForUpdate()
                ->orderByRaw('CAST(SUBSTRING(offer_no, ?) AS UNSIGNED) DESC', [strlen($basePrefix) + 1])
                ->value('offer_no');

            $nextNumber = 1;

            if ($lastOfferNo && preg_match('/^' . preg_quote($basePrefix, '/') . '(\d{3,})$/', $lastOfferNo, $m)) {
                $nextNumber = ((int) $m[1]) + 1;
            }

            do {
                $offerNo = sprintf('%s%03d', $basePrefix, $nextNumber);

                $existsQuery = Offer::withTrashed()
                    ->where('offer_no', $offerNo);

                if ($ignoreOfferId) {
                    $existsQuery->where('id', '!=', $ignoreOfferId);
                }

                $exists = $existsQuery->exists();

                if ($exists) {
                    $nextNumber++;
                }
            } while ($exists);

            return $offerNo;
        }, 3);
    }

    protected function getOfferFolderQuery()
    {
        return method_exists(OfferFolder::class, 'withTrashed')
            ? OfferFolder::withTrashed()
            : OfferFolder::query();
    }

    protected function restoreFolderIfTrashed(?OfferFolder $folder): ?OfferFolder
    {
        if ($folder && method_exists($folder, 'trashed') && $folder->trashed()) {
            $folder->restore();
            $folder->refresh();
        }

        return $folder;
    }

    protected function clonePublicFileIfExists(?string $path, string $targetDir): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = ltrim($path, '/');

        if (!Storage::disk('public')->exists($path)) {
            return $path;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $newName = Str::uuid() . ($extension ? '.' . $extension : '');
        $newPath = trim($targetDir, '/') . '/' . $newName;

        Storage::disk('public')->copy($path, $newPath);

        return $newPath;
    }

    protected function syncOfferLeadProducts(): void
    {
        $employeeId = $this->employeeId();

        $rows = DB::table('lead_product_lists')
            ->select([
                'id',
                'customer_id',
                'alternative_id',
                'product_id',
                'service_id',
                'department_id',
                'service',
                'employee_id',
                'status',
                'deleted_at',
            ])
            ->where('status', 'offer')
            ->whereNull('deleted_at')
            ->get();

        foreach ($rows as $row) {
            try {
                $customerId = is_numeric($row->customer_id ?? null) ? (int) $row->customer_id : null;
                $alternativeId = is_numeric($row->alternative_id ?? null) ? (int) $row->alternative_id : null;
                $productId = is_numeric($row->product_id ?? null) ? (int) $row->product_id : null;
                $departmentId = is_numeric($row->department_id ?? null) ? (int) $row->department_id : null;

                if (!$customerId || !$alternativeId || !$productId) {
                    continue;
                }

                $alreadyExists = Offer::withTrashed()
                    ->where('customer_id', $customerId)
                    ->where('alternative_id', $alternativeId)
                    ->where('product_id', $productId)
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                DB::beginTransaction();

                $createdBy = $employeeId ?: (is_numeric($row->employee_id ?? null) ? (int) $row->employee_id : null);

                $serviceText = trim((string) ($row->service ?? ''));
                $serviceText = $serviceText !== '' ? $serviceText : 'complete';

                $offer = Offer::create([
                    'offer_no' => $this->safeGenerateOfferNo($customerId, $departmentId),
                    'customer_id' => $customerId,
                    'alternative_id' => $alternativeId,
                    'product_id' => $productId,
                    'service_id' => is_numeric($row->service_id ?? null) ? (int) $row->service_id : null,
                    'department_id' => $departmentId,
                    'service' => $serviceText,
                    'created_by' => $createdBy,
                    'created_for' => is_numeric($row->employee_id ?? null) ? (int) $row->employee_id : $createdBy,
                    'status' => 'draft',
                    'status_msg' => 'Auto import from lead_product_lists',
                ]);

                $folderData = [
                    'offer_id' => $offer->id,
                    'customer_id' => $offer->customer_id,
                    'alternative_id' => $offer->alternative_id,
                    'product_id' => $offer->product_id,
                    'department_id' => $offer->department_id,
                    'created_by' => $createdBy,
                    'name' => 'V1 Entwurf',
                    'color' => '#93c21c',
                    'status' => 'draft',
                ];

                if (Schema::hasColumn('offer_folders', 'document_status')) {
                    $folderData['document_status'] = 'offer';
                }

                if (Schema::hasColumn('offer_folders', 'offer_status')) {
                    $folderData['offer_status'] = 'draft';
                }

                if (Schema::hasColumn('offer_folders', 'deal_status')) {
                    $folderData['deal_status'] = 'open';
                }

                $folder = OfferFolder::create($folderData);

                if (class_exists(OfferFolderActivity::class)) {
                    $activity = $this->createFolderActivity(
                        $folder,
                        'folder_created',
                        'Ordner automatisch erstellt',
                        'V1 Entwurf wurde automatisch aus lead_product_lists angelegt.',
                        ['source' => 'lead_product_lists']
                    );

                    $this->broadcastFolderChange($folder, 'created', $activity);
                } else {
                    $this->broadcastOfferChange('offer_auto_imported', $offer);
                }

                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                report($e);
            }
        }
    }

    public function searchCustomerObjects(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        $leads = DB::table('new_leads')
            ->select(
                'new_leads.id as lead_id',
                'new_leads.firma',
                'new_leads.name',
                'new_leads.lastname',
                'lead_alternative_adds.id as alt_id',
                'lead_alternative_adds.street',
                'lead_alternative_adds.city',
                'lead_alternative_adds.object_name'
            )
            ->leftJoin('lead_alternative_adds', 'new_leads.id', '=', 'lead_alternative_adds.lead_id')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('new_leads.firma', 'like', "%{$q}%")
                        ->orWhere('new_leads.lastname', 'like', "%{$q}%")
                        ->orWhere('new_leads.name', 'like', "%{$q}%")
                        ->orWhere('lead_alternative_adds.street', 'like', "%{$q}%")
                        ->orWhere('lead_alternative_adds.city', 'like', "%{$q}%")
                        ->orWhere('lead_alternative_adds.object_name', 'like', "%{$q}%");
                });
            })
            ->limit(30)
            ->get();

        $pairs = $leads
            ->filter(fn($row) => !empty($row->alt_id))
            ->map(fn($row) => [
                'customer_id' => (int) $row->lead_id,
                'alternative_id' => (int) $row->alt_id,
            ])
            ->unique(fn($row) => $row['customer_id'] . '_' . $row['alternative_id'])
            ->values();

        $existingOfferMap = [];

        if ($pairs->isNotEmpty()) {
            $offers = Offer::withTrashed()
                ->select('id', 'customer_id', 'alternative_id', 'deleted_at')
                ->where(function ($query) use ($pairs) {
                    foreach ($pairs as $pair) {
                        $query->orWhere(function ($inner) use ($pair) {
                            $inner->where('customer_id', $pair['customer_id'])
                                ->where('alternative_id', $pair['alternative_id']);
                        });
                    }
                })
                ->get();

            foreach ($offers as $offer) {
                $key = $offer->customer_id . '_' . $offer->alternative_id;
                $existingOfferMap[$key] = [
                    'id' => (int) $offer->id,
                    'deleted' => !empty($offer->deleted_at),
                ];
            }
        }

        $results = $leads->map(function ($row) use ($existingOfferMap) {
            $customerName = trim(implode(' ', array_filter([
                $row->firma,
                $row->name,
                $row->lastname,
            ])));

            $existingOffer = null;

            if ($row->alt_id) {
                $objDetails = trim(implode(', ', array_filter([
                    $row->street,
                    $row->city,
                ])));

                $objName = $row->object_name ? " ({$row->object_name})" : '';
                $text = "{$customerName} | Objekt: {$objDetails}{$objName}";

                $key = ((int) $row->lead_id) . '_' . ((int) $row->alt_id);
                $existingOffer = $existingOfferMap[$key] ?? null;

                if ($existingOffer) {
                    $text .= $existingOffer['deleted']
                        ? " | Gelöschtes Angebot vorhanden #{$existingOffer['id']}"
                        : " | Angebot vorhanden #{$existingOffer['id']}";
                }
            } else {
                $text = "{$customerName} | Kein Objekt hinterlegt";
            }

            return [
                'id' => $row->alt_id ?: 'lead_' . $row->lead_id,
                'text' => $text,
                'customer_id' => (int) $row->lead_id,
                'alternative_id' => $row->alt_id ? (int) $row->alt_id : null,
                'has_existing_offer' => !empty($existingOffer) && empty($existingOffer['deleted']),
                'has_deleted_offer' => !empty($existingOffer) && !empty($existingOffer['deleted']),
                'existing_offer_id' => $existingOffer['id'] ?? null,
            ];
        })->values();

        return response()->json([
            'results' => $results,
        ]);
    }

    public function getProducts(Request $request): JsonResponse
    {
        $this->prepareJsonResponse();

        $validator = Validator::make($request->all(), [
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['nullable'],
            'offer_id' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validierungsfehler: ' . $validator->errors()->first(),
                422
            );
        }

        $customerId = (int) $request->customer_id;
        $alternativeId = $request->alternative_id;
        $offerId = $request->offer_id ? (int) $request->offer_id : null;

        $products = LeadProductList::query()
            ->select('lead_product_lists.product_id', 'article_groups.article_group')
            ->join('article_groups', 'article_groups.id', '=', 'lead_product_lists.product_id')
            ->where('lead_product_lists.customer_id', $customerId)
            ->when($alternativeId !== null && $alternativeId !== '', function ($q) use ($alternativeId) {
                $q->where('lead_product_lists.alternative_id', $alternativeId);
            })
            ->distinct()
            ->get();

        $existingOffers = Offer::withTrashed()
            ->select('id', 'customer_id', 'alternative_id', 'product_id', 'deleted_at')
            ->where('customer_id', $customerId)
            ->when($alternativeId !== null && $alternativeId !== '', function ($q) use ($alternativeId) {
                $q->where('alternative_id', $alternativeId);
            })
            ->get()
            ->keyBy('product_id');

        $results = $products->map(function ($product) use ($existingOffers, $offerId) {
            $existing = $existingOffers->get($product->product_id);

            $isDuplicate = $existing
                && empty($existing->deleted_at)
                && (!$offerId || (int) $existing->id !== $offerId);

            return [
                'id' => (int) $product->product_id,
                'text' => $product->article_group ?: ('Produkt #' . $product->product_id),
                'has_existing_offer' => $isDuplicate,
                'has_deleted_offer' => $existing && !empty($existing->deleted_at),
                'existing_offer_id' => $existing ? (int) $existing->id : null,
            ];
        })->values();

        return response()->json($results, 200, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $this->prepareJsonResponse();

        try {
            $this->syncOfferLeadProducts();

            $offers = Offer::query()
                ->with([
                    'customer',
                    'alternative',
                    'product',
                    'creator',
                    'department',
                    'detail',
                    'folders.creator',
                    'folders.detail',
                ])
                ->latest()
                ->get();

            $this->appendRecentChangeDataToOffers($offers);
            $this->appendOfferIndexTotalDataToOffers($offers);

            $offers = $offers
                ->sortByDesc(fn(Offer $offer) => data_get($offer, 'recent_change.sort_at') ?: optional($offer->updated_at)->timestamp ?: 0)
                ->values();

            return $this->successResponse([
                'success' => true,
                'data' => $offers,
            ]);
        } catch (Throwable $e) {
            report($e);

            return $this->errorResponse(
                'Angebote konnten nicht geladen werden: ' . $e->getMessage(),
                500,
                [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Offer index totals from active folders only
    |--------------------------------------------------------------------------
    | Proof marker: OFFER_FOLDER_EMPTY_INDEX_TOTAL_FIX_V1
    |
    | The offer index must never keep showing an old price after all folders are
    | deleted. Folder details are version/variant data; therefore the index total
    | is resolved from non-deleted offer_folders only. If no active folder remains,
    | the index price is forced to 0,00 €.
    */
    protected function buildEmptyOfferIndexDetail(float $taxRate = 19.0): array
    {
        return [
            'id' => null,
            'offer_id' => null,
            'offer_folder_id' => null,
            'document_status' => OfferDetail::DOCUMENT_STATUS_OFFER,
            'total_net' => 0.0,
            'tax_rate' => round($taxRate, 2),
            'total_gross' => 0.0,
            'sections' => [],
            'source' => 'no_active_folders',
        ];
    }

    protected function resolveOfferIndexDetailPayload(int $offerId): array
    {
        $activeFolders = OfferFolder::query()
            ->where('offer_id', $offerId)
            ->with(['detail'])
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        $activeFolderCount = $activeFolders->count();

        if ($activeFolderCount === 0) {
            $taxRate = (float) (OfferDetail::query()
                ->where('offer_id', $offerId)
                ->latest('updated_at')
                ->value('tax_rate') ?? 19);

            $detail = $this->buildEmptyOfferIndexDetail($taxRate);
            $detail['offer_id'] = $offerId;

            return [
                'active_folder_count' => 0,
                'has_active_folders' => false,
                'index_detail' => $detail,
            ];
        }

        $sourceFolder = $activeFolders->first(fn($folder) => $folder->detail !== null) ?: $activeFolders->first();
        $sourceDetail = $sourceFolder?->detail;

        if (!$sourceDetail) {
            $detail = $this->buildEmptyOfferIndexDetail(19.0);
            $detail['offer_id'] = $offerId;
            $detail['offer_folder_id'] = $sourceFolder?->id;

            return [
                'active_folder_count' => $activeFolderCount,
                'has_active_folders' => true,
                'index_detail' => $detail,
            ];
        }

        return [
            'active_folder_count' => $activeFolderCount,
            'has_active_folders' => true,
            'index_detail' => [
                'id' => $sourceDetail->id,
                'offer_id' => $offerId,
                'offer_folder_id' => $sourceFolder?->id ?: $sourceDetail->offer_folder_id,
                'document_status' => $sourceDetail->document_status ?: OfferDetail::DOCUMENT_STATUS_OFFER,
                'total_net' => round((float) ($sourceDetail->total_net ?? 0), 2),
                'tax_rate' => round((float) ($sourceDetail->tax_rate ?? 19), 2),
                'total_gross' => round((float) ($sourceDetail->total_gross ?? 0), 2),
                'sections' => is_array($sourceDetail->sections) ? $sourceDetail->sections : [],
                'source' => 'latest_active_folder',
            ],
        ];
    }

    protected function syncOfferIndexTotalsFromActiveFolders(int $offerId): array
    {
        $payload = $this->resolveOfferIndexDetailPayload($offerId);
        $detail = $payload['index_detail'];

        /*
         * Keep a lightweight offer-level summary row for old places that still
         * read $offer->detail. If your table does not allow a NULL
         * offer_folder_id, the index response still remains correct because
         * appendOfferIndexTotalDataToOffers() injects the resolved detail.
         */
        try {
            $summary = OfferDetail::query()->firstOrNew([
                'offer_id' => $offerId,
                'offer_folder_id' => null,
            ]);

            $summary->document_status = $detail['document_status'] ?? OfferDetail::DOCUMENT_STATUS_OFFER;
            $summary->sections = $payload['has_active_folders'] ? ($detail['sections'] ?? []) : [];
            $summary->total_net = (float) ($detail['total_net'] ?? 0);
            $summary->tax_rate = (float) ($detail['tax_rate'] ?? 19);
            $summary->total_gross = (float) ($detail['total_gross'] ?? 0);
            $summary->save();

            $payload['index_detail']['summary_detail_id'] = $summary->id;
        } catch (Throwable $e) {
            report($e);
        }

        try {
            Offer::query()->whereKey($offerId)->update(['updated_at' => now()]);
        } catch (Throwable $e) {
            report($e);
        }

        return $payload;
    }

    protected function appendOfferIndexTotalDataToOffers($offers): void
    {
        if (!$offers || $offers->isEmpty()) {
            return;
        }

        foreach ($offers as $offer) {
            $payload = $this->resolveOfferIndexDetailPayload((int) $offer->id);
            $detail = $payload['index_detail'];

            $detailModel = new OfferDetail($detail);
            $detailModel->exists = !empty($detail['id']);

            // Override only for the JSON index payload. This prevents stale
            // offer.detail totals from being used when all folders were deleted.
            $offer->setRelation('detail', $detailModel);
            $offer->setAttribute('index_detail', $detail);
            $offer->setAttribute('active_folder_count', $payload['active_folder_count']);
            $offer->setAttribute('has_active_folders', $payload['has_active_folders']);
            $offer->setAttribute('index_total_net', (float) ($detail['total_net'] ?? 0));
            $offer->setAttribute('index_total_gross', (float) ($detail['total_gross'] ?? 0));
        }
    }

    protected function appendRecentChangeDataToOffers($offers): void
    {
        if (!$offers || $offers->isEmpty()) {
            return;
        }

        $offerIds = $offers->pluck('id')->filter()->map(fn($id) => (int) $id)->values();
        $folderIds = $offers
            ->flatMap(fn(Offer $offer) => $offer->folders ?? collect())
            ->pluck('id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->values();

        $latestActivitiesByOffer = collect();
        $latestActivitiesByFolder = collect();

        if (Schema::hasTable('offer_folder_activities') && $offerIds->isNotEmpty()) {
            $activities = OfferFolderActivity::query()
                ->with('employee')
                ->whereIn('offer_id', $offerIds)
                ->orderByDesc('created_at')
                ->get();

            $latestActivitiesByOffer = $activities->groupBy('offer_id')->map(fn($rows) => $rows->first());
            $latestActivitiesByFolder = $activities->groupBy('offer_folder_id')->map(fn($rows) => $rows->first());
        }

        foreach ($offers as $offer) {
            $offerActivity = $latestActivitiesByOffer->get($offer->id);
            $offerRecent = $this->buildOfferRecentChangePayload($offer, $offerActivity, 'offer');
            $folderRecentPayloads = [];

            foreach (($offer->folders ?? collect()) as $folder) {
                $folderActivity = $latestActivitiesByFolder->get($folder->id);
                $folderRecent = $this->buildFolderRecentChangePayload($folder, $folderActivity);
                $folder->setAttribute('recent_change', $folderRecent);
                $folderRecentPayloads[] = $folderRecent;
            }

            $mostRecentFolder = collect($folderRecentPayloads)
                ->filter(fn($row) => !empty($row['sort_at']))
                ->sortByDesc('sort_at')
                ->first();

            $finalRecent = collect([$offerRecent, $mostRecentFolder])
                ->filter(fn($row) => !empty($row['sort_at']))
                ->sortByDesc('sort_at')
                ->first() ?: $offerRecent;

            $offer->setAttribute('recent_change', $finalRecent);
        }
    }

    protected function buildFolderRecentChangePayload(OfferFolder $folder, ?OfferFolderActivity $activity = null): array
    {
        $folder->loadMissing(['detail', 'creator']);

        $candidates = [];

        if ($folder->created_at) {
            $candidates[] = [
                'kind' => 'created',
                'title' => 'Ordner erstellt',
                'message' => $folder->name,
                'changed_at' => $folder->created_at,
                'sort_at' => $folder->created_at->timestamp,
            ];
        }

        if ($folder->updated_at) {
            $candidates[] = [
                'kind' => 'folder_updated',
                'title' => 'Ordner aktualisiert',
                'message' => $folder->name,
                'changed_at' => $folder->updated_at,
                'sort_at' => $folder->updated_at->timestamp,
            ];
        }

        if ($folder->detail?->updated_at) {
            $candidates[] = [
                'kind' => 'detail_updated',
                'title' => 'Angebotsinhalt geändert',
                'message' => 'Positionen, Preise oder Dokumentdaten wurden aktualisiert.',
                'changed_at' => $folder->detail->updated_at,
                'sort_at' => $folder->detail->updated_at->timestamp,
            ];
        }

        if ($activity) {
            $candidates[] = $this->buildRecentChangeFromActivity($activity);
        }

        $latest = collect($candidates)->sortByDesc('sort_at')->first() ?: [];

        return array_merge([
            'entity' => 'folder',
            'folder_id' => (int) $folder->id,
            'folder_name' => $folder->name,
            'badge' => 'Geändert',
            'changed_at' => optional($folder->updated_at ?: $folder->created_at)->toDateTimeString(),
            'changed_at_human' => optional($folder->updated_at ?: $folder->created_at)->format('d.m.Y H:i'),
            'sort_at' => optional($folder->updated_at ?: $folder->created_at)->timestamp ?: 0,
            'title' => 'Ordner aktualisiert',
            'message' => $folder->name,
            'changes' => [],
            'employee_name' => $folder->creator ? trim(($folder->creator->name ?? '') . ' ' . ($folder->creator->lastname ?? '')) : null,
        ], $latest, [
            'changed_at' => isset($latest['changed_at']) && $latest['changed_at'] instanceof \Carbon\CarbonInterface
                ? $latest['changed_at']->toDateTimeString()
                : ($latest['changed_at'] ?? optional($folder->updated_at ?: $folder->created_at)->toDateTimeString()),
            'changed_at_human' => isset($latest['changed_at']) && $latest['changed_at'] instanceof \Carbon\CarbonInterface
                ? $latest['changed_at']->format('d.m.Y H:i')
                : ($latest['changed_at_human'] ?? optional($folder->updated_at ?: $folder->created_at)->format('d.m.Y H:i')),
        ]);
    }

    protected function buildOfferRecentChangePayload(Offer $offer, ?OfferFolderActivity $activity = null, string $entity = 'offer'): array
    {
        $candidate = [
            'entity' => $entity,
            'offer_id' => (int) $offer->id,
            'badge' => $offer->created_at && $offer->updated_at && $offer->updated_at->equalTo($offer->created_at) ? 'Neu' : 'Geändert',
            'title' => $offer->created_at && $offer->updated_at && $offer->updated_at->equalTo($offer->created_at)
                ? 'Angebot erstellt'
                : 'Angebot aktualisiert',
            'message' => $offer->offer_no ? ('Angebotsnummer: ' . $offer->offer_no) : ('Angebot #' . $offer->id),
            'changed_at' => optional($offer->updated_at ?: $offer->created_at)->toDateTimeString(),
            'changed_at_human' => optional($offer->updated_at ?: $offer->created_at)->format('d.m.Y H:i'),
            'sort_at' => optional($offer->updated_at ?: $offer->created_at)->timestamp ?: 0,
            'changes' => [],
            'employee_name' => $offer->creator ? trim(($offer->creator->name ?? '') . ' ' . ($offer->creator->lastname ?? '')) : null,
        ];

        if ($activity) {
            $activityPayload = $this->buildRecentChangeFromActivity($activity);

            if (($activityPayload['sort_at'] ?? 0) >= ($candidate['sort_at'] ?? 0)) {
                return array_merge($candidate, $activityPayload, [
                    'entity' => 'folder',
                    'offer_id' => (int) $offer->id,
                ]);
            }
        }

        return $candidate;
    }

    protected function buildRecentChangeFromActivity(OfferFolderActivity $activity): array
    {
        $changes = $this->activityChangeLines($activity);
        $employeeName = $activity->employee
            ? trim(($activity->employee->name ?? '') . ' ' . ($activity->employee->lastname ?? ''))
            : null;

        return [
            'entity' => 'activity',
            'offer_id' => $activity->offer_id ? (int) $activity->offer_id : null,
            'folder_id' => $activity->offer_folder_id ? (int) $activity->offer_folder_id : null,
            'badge' => 'Aktualisiert',
            'kind' => $activity->type,
            'title' => $activity->title ?: 'Änderung',
            'message' => $activity->message,
            'changes' => $changes,
            'employee_name' => $employeeName,
            'changed_at' => optional($activity->created_at)->toDateTimeString(),
            'changed_at_human' => optional($activity->created_at)->format('d.m.Y H:i'),
            'sort_at' => optional($activity->created_at)->timestamp ?: 0,
        ];
    }

    protected function activityChangeLines(OfferFolderActivity $activity): array
    {
        $meta = is_array($activity->meta ?? null) ? $activity->meta : [];
        $old = $meta['old'] ?? [];
        $new = $meta['new'] ?? [];

        if (!is_array($old) || !is_array($new)) {
            return [];
        }

        $labels = [
            'name' => 'Name',
            'color' => 'Farbe',
            'status' => 'Status',
            'offer_status' => 'Angebotsstatus',
            'deal_status' => 'Auftragsstatus',
            'document_status' => 'Dokumenttyp',
            'total_net' => 'Netto',
            'total_gross' => 'Brutto',
        ];

        $lines = [];

        foreach ($new as $key => $newValue) {
            $oldValue = $old[$key] ?? null;

            if ((string) $oldValue === (string) $newValue) {
                continue;
            }

            $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', (string) $key));
            $lines[] = $label . ': ' . ($oldValue === null || $oldValue === '' ? '–' : (string) $oldValue) . ' → ' . ($newValue === null || $newValue === '' ? '–' : (string) $newValue);
        }

        return array_slice($lines, 0, 4);
    }

    public function store(Request $request): JsonResponse
    {
        $this->prepareJsonResponse();

        $validator = Validator::make($request->all(), [
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['required', 'integer'],
            'product_id' => ['required', 'integer'],
            'department_id' => ['nullable', 'integer'],
            'service' => ['nullable', 'string', 'max:255'],
            'folder_name' => ['required', 'string', 'max:255'],
            'folder_color' => ['required', 'string', 'max:50'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validierungsfehler: ' . $validator->errors()->first(),
                422
            );
        }

        $customerId = (int) $request->input('customer_id');
        $alternativeId = (int) $request->input('alternative_id');
        $productId = (int) $request->input('product_id');
        $departmentId = $request->filled('department_id') ? (int) $request->input('department_id') : null;

        DB::beginTransaction();

        try {
            $employeeId = $this->employeeId();

            $existingOffer = $this->findExistingOfferForUpdate(
                $customerId,
                $alternativeId,
                $productId
            );

            if ($existingOffer && !$existingOffer->trashed()) {
                DB::rollBack();

                return $this->errorResponse(
                    'Für diese Kunde/Objekt/Produkt-Kombination existiert bereits ein Angebot.',
                    422,
                    [
                        'code' => 'OFFER_ALREADY_EXISTS',
                        'existing_offer' => $existingOffer,
                        'existing_offer_id' => $existingOffer->id,
                    ]
                );
            }

            if ($existingOffer && $existingOffer->trashed()) {
                $existingOffer->restore();

                $existingOffer->update([
                    'customer_id' => $customerId,
                    'alternative_id' => $alternativeId,
                    'product_id' => $productId,
                    'department_id' => $departmentId,
                    'service' => $request->input('service') ?: 'Standard',
                    'created_by' => $employeeId,
                    'status' => 'draft',
                    'status_msg' => 'Aus gelöschtem Angebot wiederhergestellt und ersetzt',
                ]);

                if (blank($existingOffer->offer_no)) {
                    $existingOffer->offer_no = $this->safeGenerateOfferNo($customerId, $departmentId, null, $existingOffer->id);
                    $existingOffer->save();
                }

                $offer = $existingOffer->fresh();
            } else {
                $offer = Offer::create([
                    'offer_no' => $this->safeGenerateOfferNo($customerId, $departmentId),
                    'customer_id' => $customerId,
                    'alternative_id' => $alternativeId,
                    'product_id' => $productId,
                    'department_id' => $departmentId,
                    'service' => $request->input('service') ?: 'Standard',
                    'created_by' => $employeeId,
                    'status' => 'draft',
                    'status_msg' => 'Manuell erstellt',
                ]);
            }

            $folder = $this->getOfferFolderQuery()
                ->where('offer_id', $offer->id)
                ->where('customer_id', $offer->customer_id)
                ->where('alternative_id', $offer->alternative_id)
                ->where('product_id', $offer->product_id)
                ->latest('id')
                ->first();

            $folder = $this->restoreFolderIfTrashed($folder);

            if ($folder) {
                $folder->update([
                    'department_id' => $departmentId,
                    'created_by' => $employeeId,
                    'name' => $request->input('folder_name') ?: ('Ordner: ' . ($offer->offer_no ?: ('Offer #' . $offer->id))),
                    'color' => $request->input('folder_color') ?: '#93c21c',
                    'status' => 'draft',
                ]);
            } else {
                $folder = OfferFolder::create([
                    'offer_id' => $offer->id,
                    'customer_id' => $offer->customer_id,
                    'alternative_id' => $offer->alternative_id,
                    'product_id' => $offer->product_id,
                    'department_id' => $departmentId,
                    'created_by' => $employeeId,
                    'name' => $request->input('folder_name') ?: ('Ordner: ' . ($offer->offer_no ?: ('Offer #' . $offer->id))),
                    'color' => $request->input('folder_color') ?: '#93c21c',
                    'status' => 'draft',
                ]);
            }

            $activity = null;

            if (class_exists(OfferFolderActivity::class)) {
                $activity = $this->createFolderActivity(
                    $folder,
                    $existingOffer ? 'folder_restored' : 'folder_created',
                    $existingOffer ? 'Ordner wiederhergestellt' : 'Ordner erstellt',
                    $folder->name,
                    [
                        'source' => 'offer_store',
                        'restored_offer' => (bool) $existingOffer,
                    ]
                );
            }

            DB::commit();

            $offer = Offer::with([
                'customer',
                'alternative',
                'product',
                'creator',
                'department',
                'folders.creator',
            ])->find($offer->id);

            $this->broadcastOfferChange(
                $existingOffer ? 'offer_restored' : 'offer_created',
                $offer
            );

            if ($activity) {
                $this->broadcastFolderChange(
                    $folder->fresh(['creator']),
                    $existingOffer ? 'restored' : 'created',
                    $activity
                );
            }

            return $this->successResponse([
                'success' => true,
                'message' => $existingOffer
                    ? 'Gelöschtes Angebot wurde wiederhergestellt und ersetzt!'
                    : 'Angebot und Ordner erfolgreich erstellt!',
                'data' => $offer,
                'offer_id' => $offer->id,
                'offer_no' => $offer->offer_no,
                'folder_id' => $folder->id,
                'restored' => (bool) $existingOffer,
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return $this->errorResponse(
                'Fehler beim Erstellen: ' . $e->getMessage(),
                500
            );
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->prepareJsonResponse();

        $validator = Validator::make($request->all(), [
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['required', 'integer'],
            'product_id' => ['required', 'integer'],
            'service' => ['nullable', 'string', 'max:255'],
            'department_id' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validierungsfehler: ' . $validator->errors()->first(),
                422
            );
        }

        DB::beginTransaction();

        try {
            $offer = Offer::lockForUpdate()->find($id);

            if (!$offer) {
                DB::rollBack();
                return $this->errorResponse("Angebot nicht gefunden (ID: {$id}).", 404);
            }

            $customerId = (int) $request->customer_id;
            $alternativeId = (int) $request->alternative_id;
            $productId = (int) $request->product_id;
            $departmentId = $request->filled('department_id') ? (int) $request->department_id : $offer->department_id;

            $existingOffer = $this->findExistingOfferForUpdate(
                $customerId,
                $alternativeId,
                $productId,
                $offer->id
            );

            if ($existingOffer && !$existingOffer->trashed()) {
                DB::rollBack();

                return $this->errorResponse(
                    'Diese Kunde/Objekt/Produkt-Kombination ist bereits in einem anderen Angebot vorhanden.',
                    422,
                    [
                        'code' => 'OFFER_ALREADY_EXISTS',
                        'existing_offer' => $existingOffer,
                        'existing_offer_id' => $existingOffer->id,
                    ]
                );
            }

            $offer->update([
                'customer_id' => $customerId,
                'alternative_id' => $alternativeId,
                'product_id' => $productId,
                'department_id' => $departmentId,
                'service' => $request->service ?: $offer->service,
            ]);

            if (blank($offer->offer_no)) {
                $offer->offer_no = $this->safeGenerateOfferNo($customerId, $departmentId, null, $offer->id);
                $offer->save();
            }

            DB::commit();

            $offer->load([
                'customer',
                'alternative',
                'product',
                'creator',
                'department',
                'folders.creator',
            ]);

            $this->broadcastOfferChange('offer_updated', $offer);

            return $this->successResponse([
                'success' => true,
                'message' => 'Angebot erfolgreich aktualisiert!',
                'data' => $offer,
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return $this->errorResponse(
                'Fehler beim Aktualisieren: ' . $e->getMessage(),
                500
            );
        }
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $this->prepareJsonResponse();

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string', 'in:draft,sent,negotiation,final,cancel'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validierungsfehler: ' . $validator->errors()->first(),
                422
            );
        }

        try {
            $offer = Offer::find($id);

            if (!$offer) {
                return $this->errorResponse("Angebot nicht gefunden (ID: {$id}).", 404);
            }

            $offer->status = $request->status;
            $offer->save();

            $this->broadcastOfferChange('offer_status_updated', $offer);

            return $this->successResponse([
                'success' => true,
                'message' => 'Status aktualisiert!',
            ]);
        } catch (Throwable $e) {
            report($e);

            return $this->errorResponse(
                'Fehler beim Statuswechsel: ' . $e->getMessage(),
                500
            );
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        abort_unless((bool) (auth()->user()?->hasPermission('Customer', 'delete')), 403, 'Keine Berechtigung, Kunden/Objekte/Angebote zu loeschen (MASTER-01 P0-4).');
        $this->prepareJsonResponse();

        $validator = Validator::make($request->all(), [
            'delete_type' => ['nullable', 'string', 'in:soft,complete'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validierungsfehler: ' . $validator->errors()->first(),
                422
            );
        }

        $deleteType = $request->input('delete_type', 'soft');

        DB::beginTransaction();

        try {
            $offer = Offer::with([
                'customer',
                'alternative',
                'product',
                'creator',
                'department',
                'detail',
                'folders.creator',
                'folders.detail',
            ])->find($id);

            if (!$offer) {
                DB::rollBack();
                return $this->errorResponse("Angebot nicht gefunden (ID: {$id}).", 404);
            }

            OfferDeleteLog::create([
                'offer_id' => $offer->id,
                'offer_no' => $offer->offer_no,
                'user_id' => auth()->id(),
                'employee_id' => $this->employeeId(),
                'delete_type' => $deleteType,
                'reason' => $request->input('reason'),
                'snapshot' => $offer->toArray(),
                'deleted_at' => now(),
            ]);

            if ($deleteType === 'soft') {
                $offer->delete();

                DB::commit();

                $this->broadcastOfferChange('offer_soft_deleted', $offer);

                return $this->successResponse([
                    'success' => true,
                    'delete_type' => 'soft',
                    'message' => 'Angebot wurde weich gelöscht und protokolliert.',
                ]);
            }

            $folderIds = OfferFolder::query()
                ->where('offer_id', $offer->id)
                ->pluck('id');

            if (Schema::hasTable('offer_folder_activities')) {
                DB::table('offer_folder_activities')->where('offer_id', $offer->id)->delete();
            }

            if (Schema::hasTable('offer_product_lists')) {
                DB::table('offer_product_lists')->where('offer_id', $offer->id)->delete();
            }

            if (Schema::hasTable('offer_asset_lists')) {
                DB::table('offer_asset_lists')->where('offer_id', $offer->id)->delete();
            }

            if (Schema::hasTable('offer_folder_attachments')) {
                DB::table('offer_folder_attachments')->where('offer_id', $offer->id)->delete();
            }

            if (Schema::hasTable('offer_pdf_prints')) {
                DB::table('offer_pdf_prints')->where('offer_id', $offer->id)->delete();
            }

            if (Schema::hasTable('offer_details')) {
                DB::table('offer_details')->where('offer_id', $offer->id)->delete();
            }

            if ($folderIds->isNotEmpty()) {
                OfferFolder::query()->whereIn('id', $folderIds)->delete();
            }

            $offer->forceDelete();

            DB::commit();

            $this->broadcastOfferChange('offer_complete_deleted', $offer);

            return $this->successResponse([
                'success' => true,
                'delete_type' => 'complete',
                'message' => 'Angebot wurde vollständig gelöscht und protokolliert.',
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return $this->errorResponse(
                'Fehler beim Löschen: ' . $e->getMessage(),
                500
            );
        }
    }

    protected function offerHasFinalFolder(int $offerId): bool
    {
        return OfferFolder::query()
            ->where('offer_id', $offerId)
            ->whereIn('status', ['final', 'abgeschlossen'])
            ->exists();
    }

    public function storeFolder(Request $request, int $offerId): JsonResponse
    {
        $this->prepareJsonResponse();

        $validator = $this->validateFolderPayload($request);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validierungsfehler: ' . $validator->errors()->first(),
                422
            );
        }

        DB::beginTransaction();

        try {
            $offer = Offer::find($offerId);

            if (!$offer) {
                DB::rollBack();
                return $this->errorResponse("Angebot nicht gefunden (ID: {$offerId}).", 404);
            }

            if ($this->offerHasFinalFolder($offer->id)) {
                DB::rollBack();

                return $this->errorResponse(
                    'Das Angebot ist finalisiert und kann nicht geändert werden.',
                    422
                );
            }

            $folder = OfferFolder::create([
                'offer_id' => $offer->id,
                'customer_id' => $offer->customer_id,
                'alternative_id' => $offer->alternative_id,
                'product_id' => $offer->product_id,
                'created_by' => $this->employeeId(),
                'name' => $request->name,
                'color' => $request->color ?: '#93c21c',
                'status' => $request->status ?: 'draft',
            ]);

            $activity = null;

            if (class_exists(OfferFolderActivity::class)) {
                $activity = $this->createFolderActivity(
                    $folder,
                    'folder_created',
                    'Ordner erstellt',
                    $folder->name
                );
            }

            DB::commit();

            $folder->load('creator');

            if ($activity) {
                $this->broadcastFolderChange($folder, 'created', $activity);
            } else {
                $this->broadcastOfferChange('folder_created', $offer);
            }

            return $this->successResponse([
                'success' => true,
                'message' => 'Ordner erfolgreich erstellt.',
                'folder_id' => $folder->id,
                'folder' => $folder,
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return $this->errorResponse(
                'Server-Fehler: ' . $e->getMessage(),
                500,
                ['line' => $e->getLine()]
            );
        }
    }

    public function updateFolder(Request $request, OfferFolder $folder): JsonResponse
    {
        $this->prepareJsonResponse();

        $validator = $this->validateFolderPayload($request);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validierungsfehler: ' . $validator->errors()->first(),
                422
            );
        }

        DB::beginTransaction();

        try {
            $old = [
                'name' => $folder->name,
                'color' => $folder->color,
                'status' => $folder->status,
            ];

            $folder->update([
                'name' => $request->name,
                'color' => $request->color ?: $folder->color,
                'status' => $request->status ?: $folder->status,
            ]);

            $activity = null;

            if (class_exists(OfferFolderActivity::class)) {
                $activity = $this->createFolderActivity(
                    $folder,
                    'folder_updated',
                    'Ordner aktualisiert',
                    $folder->name,
                    [
                        'old' => $old,
                        'new' => [
                            'name' => $folder->name,
                            'color' => $folder->color,
                            'status' => $folder->status,
                        ],
                    ]
                );
            }

            DB::commit();

            $folder->load('creator');

            if ($activity) {
                $this->broadcastFolderChange($folder, 'updated', $activity);
            } else {
                $this->broadcastOfferChange('folder_updated', $folder->offer);
            }

            return $this->successResponse([
                'success' => true,
                'message' => 'Ordner erfolgreich aktualisiert.',
                'folder_id' => $folder->id,
                'folder' => $folder,
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return $this->errorResponse(
                'Fehler beim Aktualisieren des Ordners: ' . $e->getMessage(),
                500
            );
        }
    }

    public function destroyFolder(OfferFolder $folder): JsonResponse
    {
        $this->prepareJsonResponse();

        DB::beginTransaction();

        try {
            $folderId = $folder->id;
            $offerId = $folder->offer_id;
            $folderName = $folder->name;
            $offer = $folder->offer;

            $activity = null;

            if (class_exists(OfferFolderActivity::class)) {
                $activity = OfferFolderActivity::create([
                    'offer_folder_id' => $folder->id,
                    'offer_id' => $folder->offer_id,
                    'employee_id' => $this->employeeId(),
                    'type' => 'folder_deleted',
                    'title' => 'Ordner gelöscht',
                    'message' => $folderName,
                    'meta' => ['folder_id' => $folderId],
                ]);

                $activity->load('employee');
            }

            $folder->delete();

            $indexTotals = $this->syncOfferIndexTotalsFromActiveFolders((int) $offerId);

            DB::commit();

            if ($activity) {
                try {
                    broadcast(new OfferFolderActivityCreated($activity))->toOthers();

                    broadcast(new OfferFolderUpdated(new OfferFolder([
                        'id' => $folderId,
                        'offer_id' => $offerId,
                    ]), 'deleted', [
                        'folder_id' => $folderId,
                        'offer_id' => $offerId,
                    ]))->toOthers();

                    broadcast(new OffersChanged([
                        'type' => 'folder_deleted',
                        'folder_id' => $folderId,
                        'offer_id' => $offerId,
                    ]))->toOthers();
                } catch (Throwable $e) {
                    report($e);
                }
            } else {
                $this->broadcastOfferChange('folder_deleted', $offer);
            }

            return $this->successResponse([
                'success' => true,
                'message' => 'Ordner erfolgreich gelöscht.',
                'offer_id' => $offerId,
                'active_folder_count' => $indexTotals['active_folder_count'] ?? 0,
                'index_detail' => $indexTotals['index_detail'] ?? $this->buildEmptyOfferIndexDetail(),
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return $this->errorResponse(
                'Fehler beim Löschen des Ordners: ' . $e->getMessage(),
                500
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Server-side Angebot calculation engine
    |--------------------------------------------------------------------------
    | Proof marker: OFFER_CONTROLLER_CALC_FIX_V2
    |
    | The frontend may still send total_net / total_gross for instant UI updates,
    | but the backend must never trust those values. Every save recalculates the
    | numbers from sections/items/labor rows here.
    */
    protected function offerNormalizeDecimal($value, float $default = 0.0): float
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $value = trim($value);
            $value = str_replace(["\xc2\xa0", '€', 'EUR', ' '], '', $value);

            if (str_contains($value, ',') && str_contains($value, '.')) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } elseif (str_contains($value, ',')) {
                $value = str_replace(',', '.', $value);
            }

            return is_numeric($value) ? (float) $value : $default;
        }

        return $default;
    }

    protected function offerNormalizeSections($sections): array
    {
        if (is_string($sections)) {
            $decoded = json_decode($sections, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($sections) ? $sections : [];
    }

    protected function offerNodeIsActive(array $node): bool
    {
        foreach (['active', 'enabled', 'is_active'] as $key) {
            if (array_key_exists($key, $node)) {
                return filter_var($node[$key], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== false;
            }
        }

        return true;
    }

    protected function offerNodeKind(array $node): string
    {
        return strtolower(trim((string) ($node['kind'] ?? $node['type'] ?? $node['item_type'] ?? $node['category'] ?? '')));
    }

    protected function offerPriceUnitValue(array $node, string $prefix = ''): float
    {
        $keys = $prefix === 'cost'
            ? ['cost_price_unit_value', 'ek_price_unit_value', 'purchase_price_unit_value', 'price_unit_value']
            : ['price_unit_value', 'sale_price_unit_value', 'vk_price_unit_value'];

        foreach ($keys as $key) {
            if (array_key_exists($key, $node)) {
                $value = $this->offerNormalizeDecimal($node[$key], 1.0);
                return $value > 0 ? $value : 1.0;
            }
        }

        return 1.0;
    }

    protected function offerNodeQty(array $node, float $default = 1.0): float
    {
        foreach (['qty', 'quantity', 'amount', 'count', 'menge'] as $key) {
            if (array_key_exists($key, $node)) {
                return $this->offerNormalizeDecimal($node[$key], $default);
            }
        }

        return $default;
    }

    protected function offerNodeVkPrice(array $node): float
    {
        foreach (['price', 'unit_price', 'sale_price', 'vk', 'rate', 'total_price'] as $key) {
            if (array_key_exists($key, $node)) {
                return $this->offerNormalizeDecimal($node[$key], 0.0);
            }
        }

        return 0.0;
    }

    protected function offerNodeEkPrice(array $node): float
    {
        foreach (['ek', 'cost', 'buying_price', 'purchase_price', 'cost_price'] as $key) {
            if (array_key_exists($key, $node)) {
                return $this->offerNormalizeDecimal($node[$key], 0.0);
            }
        }

        return 0.0;
    }

    protected function offerNodeChildren(array $node): array
    {
        $children = [];

        foreach (['subItems', 'sub_items', 'children', 'components', 'items'] as $key) {
            if (!empty($node[$key]) && is_array($node[$key])) {
                foreach ($node[$key] as $child) {
                    if (is_array($child)) {
                        $children[] = $child;
                    }
                }
            }
        }

        return $children;
    }

    protected function offerLaborRows(array $node): array
    {
        foreach (['labor_rows', 'lohn_rows', 'employee_rows', 'employees'] as $key) {
            if (!empty($node[$key]) && is_array($node[$key])) {
                return array_values(array_filter($node[$key], 'is_array'));
            }
        }

        return [];
    }

    protected function offerLaborTotals(array $node): array
    {
        $totals = [
            'vk' => 0.0,
            'ek' => 0.0,
            'hours' => 0.0,
        ];

        foreach ($this->offerLaborRows($node) as $row) {
            $qty = $this->offerNormalizeDecimal($row['qty'] ?? $row['quantity'] ?? $row['hours_total'] ?? $row['hours'] ?? 0, 0.0);
            $hoursPerPerson = $this->offerNormalizeDecimal($row['hours_per_person'] ?? 1, 1.0);
            $persons = $this->offerNormalizeDecimal($row['persons'] ?? $row['count'] ?? 1, 1.0);

            if (isset($row['hours_total'])) {
                $hours = $this->offerNormalizeDecimal($row['hours_total'], 0.0);
            } elseif (isset($row['hours'])) {
                $hours = $this->offerNormalizeDecimal($row['hours'], 0.0) * max(1.0, $persons);
            } else {
                $hours = $qty * max(1.0, $hoursPerPerson);
            }

            $vkRate = $this->offerNormalizeDecimal($row['rate'] ?? $row['sale_price'] ?? $row['price'] ?? $row['vk'] ?? 0, 0.0);
            $ekRate = $this->offerNormalizeDecimal($row['ek'] ?? $row['cost'] ?? $row['buying_price'] ?? $row['purchase_price'] ?? 0, 0.0);

            $totals['hours'] += $hours;
            $totals['vk'] += $hours * $vkRate;
            $totals['ek'] += $hours * $ekRate;
        }

        return $totals;
    }

    protected function offerLineTotals(array $node): array
    {
        $zero = [
            'vk' => 0.0,
            'ek' => 0.0,
            'db' => 0.0,
            'material_vk' => 0.0,
            'material_ek' => 0.0,
            'labor_vk' => 0.0,
            'labor_ek' => 0.0,
            'service_vk' => 0.0,
            'service_ek' => 0.0,
            'logistics_vk' => 0.0,
            'logistics_ek' => 0.0,
            'other_vk' => 0.0,
            'other_ek' => 0.0,
            'hours' => 0.0,
            'positions' => 0,
        ];

        if (!$this->offerNodeIsActive($node)) {
            return $zero;
        }

        $kind = $this->offerNodeKind($node);

        if (in_array($kind, ['note', 'text', 'heading', 'headline', 'description'], true)) {
            return $zero;
        }

        $qty = $this->offerNodeQty($node, 1.0);
        $children = $this->offerNodeChildren($node);
        $labor = $this->offerLaborTotals($node);

        $totals = $zero;

        if (!empty($children)) {
            foreach ($children as $child) {
                $childTotals = $this->offerLineTotals($child);
                foreach ($totals as $key => $value) {
                    if ($key === 'positions') {
                        $totals[$key] += (int) ($childTotals[$key] ?? 0);
                    } else {
                        $totals[$key] += (float) ($childTotals[$key] ?? 0);
                    }
                }
            }

            if ($qty !== 1.0 && $qty > 0) {
                foreach ($totals as $key => $value) {
                    if ($key !== 'positions') {
                        $totals[$key] = $value * $qty;
                    }
                }
            }
        } else {
            $vk = ($qty / $this->offerPriceUnitValue($node)) * $this->offerNodeVkPrice($node);
            $ek = ($qty / $this->offerPriceUnitValue($node, 'cost')) * $this->offerNodeEkPrice($node);

            $totals['vk'] = $vk;
            $totals['ek'] = $ek;
            $totals['positions'] = $vk > 0 || $ek > 0 ? 1 : 0;

            if (in_array($kind, ['labor', 'lohn', 'employee', 'personal'], true)) {
                $totals['labor_vk'] = $vk;
                $totals['labor_ek'] = $ek;
            } elseif (in_array($kind, ['service', 'fremdleistung', 'subcontractor'], true)) {
                $totals['service_vk'] = $vk;
                $totals['service_ek'] = $ek;
            } elseif (in_array($kind, ['logistics', 'logistik', 'shipping', 'delivery'], true)) {
                $totals['logistics_vk'] = $vk;
                $totals['logistics_ek'] = $ek;
            } elseif (in_array($kind, ['material', 'product', 'component', 'subcomponent', 'article', ''], true)) {
                $totals['material_vk'] = $vk;
                $totals['material_ek'] = $ek;
            } else {
                $totals['other_vk'] = $vk;
                $totals['other_ek'] = $ek;
            }
        }

        if ($labor['vk'] > 0 || $labor['ek'] > 0) {
            $totals['vk'] += $labor['vk'] * max(1.0, $qty);
            $totals['ek'] += $labor['ek'] * max(1.0, $qty);
            $totals['labor_vk'] += $labor['vk'] * max(1.0, $qty);
            $totals['labor_ek'] += $labor['ek'] * max(1.0, $qty);
            $totals['hours'] += $labor['hours'] * max(1.0, $qty);
            $totals['positions'] += count($this->offerLaborRows($node));
        }

        $totals['db'] = $totals['vk'] - $totals['ek'];

        return $totals;
    }

    protected function offerSectionMultiplier(array $section): float
    {
        $config = is_array($section['config'] ?? null) ? $section['config'] : [];
        $unit = strtolower(trim((string) ($config['unit'] ?? $section['unit'] ?? '')));
        $qty = $this->offerNormalizeDecimal($config['qty'] ?? $section['qty'] ?? 1, 1.0);

        return $unit === 'set' ? max(1.0, $qty) : 1.0;
    }

    protected function calculateOfferSections(array $sections, float $taxRate = 19.0): array
    {
        $summary = [
            'total_net' => 0.0,
            'total_ek' => 0.0,
            'total_db' => 0.0,
            'tax_rate' => $taxRate,
            'tax_amount' => 0.0,
            'total_gross' => 0.0,
            'markup_percent' => 0.0,
            'margin_percent' => 0.0,
            'material_vk' => 0.0,
            'material_ek' => 0.0,
            'labor_vk' => 0.0,
            'labor_ek' => 0.0,
            'service_vk' => 0.0,
            'service_ek' => 0.0,
            'logistics_vk' => 0.0,
            'logistics_ek' => 0.0,
            'other_vk' => 0.0,
            'other_ek' => 0.0,
            'hours' => 0.0,
            'positions' => 0,
            'sections' => [],
        ];

        foreach ($sections as $sectionIndex => $section) {
            if (!is_array($section)) {
                continue;
            }

            $sectionTotals = [
                'index' => $sectionIndex,
                'title' => (string) ($section['title'] ?? $section['name'] ?? ('Abschnitt ' . ($sectionIndex + 1))),
                'vk' => 0.0,
                'ek' => 0.0,
                'db' => 0.0,
                'hours' => 0.0,
                'positions' => 0,
            ];

            foreach (($section['items'] ?? []) as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $itemTotals = $this->offerLineTotals($item);
                $sectionTotals['vk'] += $itemTotals['vk'];
                $sectionTotals['ek'] += $itemTotals['ek'];
                $sectionTotals['hours'] += $itemTotals['hours'];
                $sectionTotals['positions'] += (int) $itemTotals['positions'];

                foreach (['material_vk', 'material_ek', 'labor_vk', 'labor_ek', 'service_vk', 'service_ek', 'logistics_vk', 'logistics_ek', 'other_vk', 'other_ek'] as $bucket) {
                    $summary[$bucket] += (float) ($itemTotals[$bucket] ?? 0.0);
                }
            }

            $multiplier = $this->offerSectionMultiplier($section);

            foreach (['vk', 'ek', 'hours'] as $key) {
                $sectionTotals[$key] *= $multiplier;
            }

            $sectionTotals['db'] = $sectionTotals['vk'] - $sectionTotals['ek'];
            $sectionTotals['markup_percent'] = $sectionTotals['ek'] > 0 ? (($sectionTotals['vk'] - $sectionTotals['ek']) / $sectionTotals['ek']) * 100 : 0.0;
            $sectionTotals['margin_percent'] = $sectionTotals['vk'] > 0 ? (($sectionTotals['vk'] - $sectionTotals['ek']) / $sectionTotals['vk']) * 100 : 0.0;

            $summary['total_net'] += $sectionTotals['vk'];
            $summary['total_ek'] += $sectionTotals['ek'];
            $summary['hours'] += $sectionTotals['hours'];
            $summary['positions'] += $sectionTotals['positions'];
            $summary['sections'][] = $sectionTotals;
        }

        $summary['total_net'] = round($summary['total_net'], 2);
        $summary['total_ek'] = round($summary['total_ek'], 2);
        $summary['total_db'] = round($summary['total_net'] - $summary['total_ek'], 2);
        $summary['tax_amount'] = round($summary['total_net'] * ($taxRate / 100), 2);
        $summary['total_gross'] = round($summary['total_net'] + $summary['tax_amount'], 2);
        $summary['markup_percent'] = $summary['total_ek'] > 0 ? round(($summary['total_db'] / $summary['total_ek']) * 100, 2) : 0.0;
        $summary['margin_percent'] = $summary['total_net'] > 0 ? round(($summary['total_db'] / $summary['total_net']) * 100, 2) : 0.0;

        foreach (['material_vk', 'material_ek', 'labor_vk', 'labor_ek', 'service_vk', 'service_ek', 'logistics_vk', 'logistics_ek', 'other_vk', 'other_ek', 'hours'] as $key) {
            $summary[$key] = round((float) $summary[$key], 2);
        }

        foreach ($summary['sections'] as &$section) {
            $section['share_percent'] = $summary['total_net'] > 0 ? round(($section['vk'] / $summary['total_net']) * 100, 2) : 0.0;
            foreach (['vk', 'ek', 'db', 'hours', 'markup_percent', 'margin_percent'] as $key) {
                $section[$key] = round((float) $section[$key], 2);
            }
        }
        unset($section);

        return $summary;
    }

    public function saveDocument(Request $request): JsonResponse
    {
        $user = auth()->user();
        $employeeId = is_numeric($user?->name ?? null) ? (int) $user->name : (int) $user->id;

        $metaData = [
            'department_id' => $request->input('department_id') ?: null,
            'article_group_id' => $request->input('article_group_id') ?: null,
            'brand_id' => $request->input('brand_id') ?: null,
            'distributor_id' => $request->input('distributor_id') ?: null,
            'description' => $request->input('template_description') ?: null,
        ];

        if ($request->boolean('is_template')) {
            return $this->processTemplate($request, $employeeId, $metaData);
        }

        return $this->processOffer($request, $employeeId, $metaData);
    }

    private function processTemplate(Request $request, int $employeeId, array $metaData): JsonResponse
    {
        $validated = $request->validate([
            'template_name' => ['required', 'string', 'max:255'],
        ]);

        $sections = $this->offerNormalizeSections($request->input('sections', []));
        $taxRate = $this->offerNormalizeDecimal($request->input('tax_rate', 19), 19.0);
        $calculation = $this->calculateOfferSections($sections, $taxRate);

        $template = OfferTemplate::create(array_merge($metaData, [
            'name' => $validated['template_name'],
            'created_by' => $employeeId,
            'brand_color' => $request->input('branding.color'),
            'brand_mode' => $request->input('branding.mode'),
            'brand_logo_url' => $request->input('branding.logo'),
            'company_name' => $request->input('branding.company'),
            'cover_text_html' => $request->input('cover_text'),
            'sections' => $sections,
            'placed_images' => $request->input('canvas_images'),
            'biography_data' => $request->input('biography'),
            // Server-authoritative value, not the frontend total.
            'leistung' => $calculation['total_net'],
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Template successfully saved.',
            'template_id' => $template->id,
            'calculation' => $calculation,
        ]);
    }

    private function processOffer(Request $request, int $employeeId, array $metaData): JsonResponse
    {
        DB::beginTransaction();

        try {
            if ($request->boolean('is_snapshot')) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Security Block: Diese Momentaufnahme (Snapshot) ist schreibgeschützt und darf den aktiven Auftrag nicht überschreiben.',
                ], 403);
            }

            $offerId = $request->filled('offer_id') ? (int) $request->input('offer_id') : null;
            $folderId = $request->filled('offer_folder_id') ? (int) $request->input('offer_folder_id') : null;

            $customerId = (int) $request->input('customer_id');
            $alternativeId = (int) $request->input('alternative_id');
            $productId = (int) $request->input('product_id');

            $departmentId = !empty($metaData['department_id']) ? (int) $metaData['department_id'] : null;
            $service = (string) $request->input('service', 'Angebot');
            $status = (string) $request->input('status', 'todo');

            $companyName = (string) $request->input('branding.company', '');
            $branchPrefix = stripos($companyName, 'werk') !== false ? 'WS' : 'SA';
            $expectedPrefix = $branchPrefix . '-AN' . date('y');

            $existingOffer = $this->findExistingOfferForUpdate(
                $customerId,
                $alternativeId,
                $productId,
                $offerId
            );

            /*
             * If this customer/object/product combination already has an active offer,
             * do NOT fail. Use that existing offer as the target and update it.
             *
             * This fixes the wizard case where the URL already contains:
             * offer_id, offer_folder_id, customer_id, alternative_id, product_id,
             * but the save endpoint still detected the same combination as duplicate.
             */
            $useExistingCombinationOffer = false;

            if ($existingOffer && !$existingOffer->trashed()) {
                $offerId = (int) $existingOffer->id;
                $useExistingCombinationOffer = true;

                /*
                 * The incoming folder_id may belong to another offer/version.
                 * If it does not belong to the target existing offer, ignore it
                 * and let the folder resolver below find/create the correct folder.
                 */
                if ($folderId) {
                    $folderBelongsToExistingOffer = $this->getOfferFolderQuery()
                        ->whereKey($folderId)
                        ->where('offer_id', $offerId)
                        ->exists();

                    if (!$folderBelongsToExistingOffer) {
                        $folderId = null;
                    }
                }
            }

            if ($offerId) {
                $offer = Offer::withTrashed()->lockForUpdate()->findOrFail($offerId);

                if ($offer->trashed()) {
                    $offer->restore();
                }

                $updateData = [
                    'customer_id' => $customerId,
                    'product_id' => $productId,
                    'alternative_id' => $alternativeId,
                    'department_id' => $departmentId,
                    'service' => $service,
                    'created_by' => $employeeId,
                    'status' => $status,
                ];

                if (blank($offer->offer_no) || !str_starts_with((string) $offer->offer_no, $expectedPrefix)) {
                    $updateData['offer_no'] = $this->safeGenerateOfferNo(
                        $customerId,
                        $departmentId,
                        $expectedPrefix,
                        $offer->id
                    );
                }

                $offer->update($updateData);
                $offer->refresh();
            } elseif ($existingOffer && $existingOffer->trashed()) {
                $existingOffer->restore();

                $existingOffer->update([
                    'customer_id' => $customerId,
                    'product_id' => $productId,
                    'alternative_id' => $alternativeId,
                    'department_id' => $departmentId,
                    'service' => $service,
                    'created_by' => $employeeId,
                    'status' => $status,
                    'status_msg' => 'Aus gelöschtem Angebot wiederhergestellt und ersetzt',
                ]);

                if (blank($existingOffer->offer_no)) {
                    $existingOffer->offer_no = $this->safeGenerateOfferNo(
                        $customerId,
                        $departmentId,
                        $expectedPrefix,
                        $existingOffer->id
                    );
                    $existingOffer->save();
                }

                $offer = $existingOffer->fresh();
            } else {
                $offer = Offer::create([
                    'offer_no' => $this->safeGenerateOfferNo($customerId, $departmentId, $expectedPrefix),
                    'customer_id' => $customerId,
                    'product_id' => $productId,
                    'alternative_id' => $alternativeId,
                    'department_id' => $departmentId,
                    'service' => $service,
                    'created_by' => $employeeId,
                    'status' => $status,
                ]);

                $offer->refresh();
            }

            if (blank($offer->offer_no)) {
                $offer->offer_no = $this->safeGenerateOfferNo($offer->customer_id, $offer->department_id, null, $offer->id);
                $offer->save();
                $offer->refresh();
            }

            $folderData = [
                'offer_id' => $offer->id,
                'customer_id' => $offer->customer_id,
                'alternative_id' => $offer->alternative_id,
                'product_id' => $offer->product_id,
                'department_id' => $departmentId,
                'created_by' => $employeeId,
                'color' => $request->input('branding.color', '#93c21c'),
                'status' => $offer->status,
            ];

            if ($folderId) {
                $folder = $this->getOfferFolderQuery()->findOrFail($folderId);
                $folder = $this->restoreFolderIfTrashed($folder);

                /*
                 * Safety: never update a folder that belongs to a different offer.
                 */
                if ((int) $folder->offer_id !== (int) $offer->id) {
                    $folder = null;
                }
            } else {
                $folder = null;
            }

            if (!$folder) {
                $folder = $this->getOfferFolderQuery()
                    ->where('offer_id', $offer->id)
                    ->where('customer_id', $offer->customer_id)
                    ->where('alternative_id', $offer->alternative_id)
                    ->where('product_id', $offer->product_id)
                    ->latest('id')
                    ->first();

                $folder = $this->restoreFolderIfTrashed($folder);
            }

            if ($folder) {
                $folder->update($folderData);
            } else {
                $folder = OfferFolder::create(array_merge($folderData, [
                    'name' => 'Ordner: ' . $offer->offer_no,
                ]));
            }

            $sections = $this->offerNormalizeSections($request->input('sections', []));
            $taxRate = $this->offerNormalizeDecimal($request->input('tax_rate', 19), 19.0);
            $calculation = $this->calculateOfferSections($sections, $taxRate);

            $offer->detail()->updateOrCreate(
                [
                    'offer_id' => $offer->id,
                    'offer_folder_id' => $folder->id,
                ],
                array_merge($metaData, [
                    'offer_folder_id' => $folder->id,
                    'offer_no' => $offer->offer_no,
                    'brand_color' => $request->input('branding.color'),
                    'brand_mode' => $request->input('branding.mode'),
                    'brand_logo_url' => $request->input('branding.logo'),
                    'company_name' => $request->input('branding.company'),
                    'cover_text_html' => $request->input('cover_text'),
                    'sections' => $sections,
                    'placed_images' => $request->input('canvas_images'),
                    'biography_data' => $request->input('biography'),
                    // Server-authoritative totals. Frontend values are ignored here.
                    'total_net' => $calculation['total_net'],
                    'tax_rate' => $calculation['tax_rate'],
                    'total_gross' => $calculation['total_gross'],
                ])
            );

            $indexTotals = $this->syncOfferIndexTotalsFromActiveFolders((int) $offer->id);

            $history = $folder->history;

            if (is_string($history)) {
                $decoded = json_decode($history, true);
                $history = is_array($decoded) ? $decoded : [];
            } elseif (!is_array($history)) {
                $history = [];
            }

            array_unshift($history, [
                'time' => now()->toDateTimeString(),
                'employee_id' => $employeeId,
                'action' => 'Document Saved',
                'note' => 'Version updated. Gross: ' . number_format((float) $calculation['total_gross'], 2, '.', '') . '€',
            ]);

            $folder->update([
                'history' => array_slice($history, 0, 50),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'offer_id' => $offer->id,
                'offer_no' => $offer->offer_no,
                'folder_id' => $folder->id,
                'calculation' => $calculation,
                'index_detail' => $indexTotals['index_detail'] ?? null,
                'active_folder_count' => $indexTotals['active_folder_count'] ?? null,
                'reused_existing_offer' => (bool) ($useExistingCombinationOffer ?? false),
                'message' => ($useExistingCombinationOffer ?? false)
                    ? 'Existing offer updated successfully.'
                    : 'Successfully saved.',
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function cloneFolder(Request $request, OfferFolder $folder): JsonResponse
    {
        $this->prepareJsonResponse();

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
            'clone_everything' => ['nullable', 'boolean'],
            'create_new_offer' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validierungsfehler: ' . $validator->errors()->first(),
                422
            );
        }

        DB::beginTransaction();

        try {
            $folder->loadMissing([
                'offer',
                'detail',
                'productLists',
                'assetLists',
                'attachments',
                'pdfPrints',
            ]);

            $employeeId = $this->currentEmployeeIdFromAuthName();
            $cloneEverything = (bool) $request->boolean('clone_everything', true);
            $createNewOffer = (bool) $request->boolean('create_new_offer', false);

            $offerIdToUse = $folder->offer_id;

            if ($createNewOffer && $folder->offer) {
                $newOffer = $folder->offer->replicate();
                $newOffer->offer_no = $this->safeGenerateOfferNo(
                    $folder->offer->customer_id,
                    $folder->offer->department_id
                );
                $newOffer->status = 'draft';
                $newOffer->created_by = $employeeId;
                $newOffer->save();

                $offerIdToUse = $newOffer->id;
            }

            $newFolder = OfferFolder::create([
                'offer_id' => $offerIdToUse,
                'customer_id' => $folder->customer_id,
                'alternative_id' => $folder->alternative_id,
                'product_id' => $folder->product_id,
                'created_by' => $employeeId,
                'name' => $request->string('name')->toString(),
                'color' => $request->input('color') ?: ($folder->color ?: '#93c21c'),
                'status' => $request->input('status') ?: ($folder->status ?: 'draft'),
                'history' => $folder->history,
            ]);

            if ($folder->detail) {
                $sourceDetail = $folder->detail;

                $newDetail = $sourceDetail->replicate();
                $newDetail->offer_id = $offerIdToUse;
                $newDetail->offer_folder_id = $newFolder->id;
                $newDetail->document_status = 'offer';

                if ($cloneEverything && is_array($sourceDetail->print_attachments)) {
                    $newPrintAttachments = [];

                    foreach ($sourceDetail->print_attachments as $attachment) {
                        $copiedPath = $this->clonePublicFileIfExists(
                            $attachment['path'] ?? null,
                            'offers/print-attachments'
                        );

                        $newPrintAttachments[] = [
                            'id' => (string) Str::uuid(),
                            'original_name' => $attachment['original_name'] ?? $attachment['file_name'] ?? 'Datei',
                            'file_name' => $copiedPath ? basename($copiedPath) : ($attachment['file_name'] ?? null),
                            'path' => $copiedPath,
                            'url' => $copiedPath ? asset('storage/' . ltrim($copiedPath, '/')) : ($attachment['url'] ?? null),
                            'mime_type' => $attachment['mime_type'] ?? null,
                            'extension' => $attachment['extension'] ?? null,
                            'size' => $attachment['size'] ?? null,
                            'type' => $attachment['type'] ?? null,
                            'sort_order' => $attachment['sort_order'] ?? null,
                            'uploaded_at' => now()->toDateTimeString(),
                        ];
                    }

                    $newDetail->print_attachments = $newPrintAttachments;
                }

                $newDetail->save();
            }

            if ($cloneEverything) {
                foreach ($folder->productLists as $productList) {
                    $newProductList = $productList->replicate();
                    $newProductList->offer_id = $offerIdToUse;
                    $newProductList->offer_folder_id = $newFolder->id;
                    $newProductList->save();
                }

                if (class_exists(OfferAssetList::class)) {
                    $assetLists = OfferAssetList::query()
                        ->where('offer_folder_id', $folder->id)
                        ->get();

                    foreach ($assetLists as $asset) {
                        $newAsset = $asset->replicate();
                        $newAsset->offer_id = $offerIdToUse;
                        $newAsset->offer_folder_id = $newFolder->id;
                        $newAsset->save();
                    }
                }

                if (class_exists(OfferFolderAttachment::class)) {
                    $attachments = OfferFolderAttachment::query()
                        ->where('offer_folder_id', $folder->id)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->get();

                    foreach ($attachments as $attachment) {
                        $newFilePath = $this->clonePublicFileIfExists(
                            $attachment->file_path,
                            'offers/folder-attachments'
                        );

                        $newAttachment = $attachment->replicate();
                        $newAttachment->offer_id = $offerIdToUse;
                        $newAttachment->offer_folder_id = $newFolder->id;
                        $newAttachment->file_path = $newFilePath;
                        $newAttachment->file_name = $newFilePath ? basename($newFilePath) : $attachment->file_name;
                        $newAttachment->file_url = $newFilePath ? asset('storage/' . ltrim($newFilePath, '/')) : $attachment->file_url;
                        $newAttachment->save();
                    }
                }

                if (class_exists(OfferPdfPrint::class)) {
                    $pdfPrints = OfferPdfPrint::query()
                        ->where('offer_folder_id', $folder->id)
                        ->orderByDesc('created_at')
                        ->get();

                    foreach ($pdfPrints as $pdf) {
                        $newPdf = $pdf->replicate();
                        $newPdf->offer_id = $offerIdToUse;
                        $newPdf->offer_folder_id = $newFolder->id;

                        if (!empty($pdf->file_path)) {
                            $newPdfPath = $this->clonePublicFileIfExists(
                                $pdf->file_path,
                                'offers/pdf-prints'
                            );

                            $newPdf->file_path = $newPdfPath;
                            $newPdf->file_name = $newPdfPath ? basename($newPdfPath) : $pdf->file_name;
                        }

                        $newPdf->save();
                    }
                }
            }

            $activity = null;

            if (class_exists(OfferFolderActivity::class)) {
                $activity = $this->createFolderActivity(
                    $newFolder,
                    'folder_cloned',
                    'Ordner dupliziert',
                    'Quelle: ' . $folder->name,
                    [
                        'source_folder_id' => $folder->id,
                        'source_folder_name' => $folder->name,
                        'clone_everything' => $cloneEverything,
                    ]
                );
            }

            DB::commit();

            $newFolder->load(['creator', 'detail']);

            if ($activity) {
                $this->broadcastFolderChange($newFolder, 'created', $activity);
            } else {
                $this->broadcastOfferChange('folder_cloned', $folder->offer);
            }

            return $this->successResponse([
                'success' => true,
                'message' => 'Ordner wurde erfolgreich dupliziert.',
                'folder_id' => $newFolder->id,
                'folder' => $newFolder,
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return $this->errorResponse(
                'Fehler beim Duplizieren des Ordners: ' . $e->getMessage(),
                500
            );
        }
    }

    protected function findLeadProductListForOffer(Offer $offer): ?LeadProductList
    {
        return LeadProductList::query()
            ->with(['productStage'])
            ->where('customer_id', $offer->customer_id)
            ->where('product_id', $offer->product_id)
            ->when($offer->alternative_id, function ($query) use ($offer) {
                $query->where('alternative_id', $offer->alternative_id);
            })
            ->first();
    }

    public function employeeSearch(Request $request): JsonResponse
    {
        $this->prepareJsonResponse();

        $q = trim((string) $request->get('q', ''));

        $employees = Employee::query()
            ->select(['id', 'name', 'lastname', 'email', 'image', 'status'])
            ->where('status', 'Active')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('lastname', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->orderBy('lastname')
            ->limit(30)
            ->get()
            ->map(function (Employee $employee) {
                $name = trim(implode(' ', array_filter([
                    $employee->name ?? null,
                    $employee->lastname ?? null,
                ])));

                return [
                    'id' => (int) $employee->id,
                    'text' => $name !== '' ? $name : ('Mitarbeiter #' . $employee->id),
                    'name' => $employee->name,
                    'lastname' => $employee->lastname,
                    'email' => $employee->email,
                    'image' => $employee->image,
                ];
            })
            ->values();

        return $this->successResponse([
            'results' => $employees,
        ]);
    }

    protected function normalizeOfferTeamRows($teams): array
    {
        if (is_string($teams)) {
            $decoded = json_decode($teams, true);
            $teams = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($teams)) {
            return [];
        }

        return collect($teams)
            ->filter(fn($team) => is_array($team))
            ->filter(function ($team) {
                $employeeId = $team['employee_id'] ?? $team['id'] ?? null;

                return is_numeric($employeeId);
            })
            ->map(function ($team) {
                $employeeId = (int) ($team['employee_id'] ?? $team['id']);

                return array_merge($team, [
                    'employee_id' => $employeeId,
                    'id' => $employeeId,
                ]);
            })
            ->unique('employee_id')
            ->values()
            ->all();
    }

    protected function offerTeamEmployeeIds(?LeadProductList $leadProductList): array
    {
        return collect($this->normalizeOfferTeamRows($leadProductList?->teams ?? []))
            ->pluck('employee_id')
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    protected function offerTeamAccessMode(?LeadProductList $leadProductList): string
    {
        if (!$leadProductList || !Schema::hasColumn('lead_product_lists', 'offer_team_access_mode')) {
            return 'all';
        }

        $mode = strtolower(trim((string) ($leadProductList->offer_team_access_mode ?: 'all')));

        return in_array($mode, ['all', 'team_only'], true) ? $mode : 'all';
    }

    protected function isPrivilegedOfferUser(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if (!empty($user->is_admin)) {
            return true;
        }

        $roleText = strtolower(trim(implode(' ', array_filter([
            $user->role ?? null,
            $user->user_role ?? null,
            $user->type ?? null,
        ]))));

        foreach (['programmer', 'super', 'administrator', 'admin'] as $role) {
            if ($roleText !== '' && str_contains($roleText, $role)) {
                return true;
            }
        }

        return false;
    }

    protected function canEditOfferTeam(?LeadProductList $leadProductList): bool
    {
        $mode = $this->offerTeamAccessMode($leadProductList);

        if ($mode === 'all') {
            return true;
        }

        if ($this->isPrivilegedOfferUser()) {
            return true;
        }

        $employeeId = $this->currentEmployeeIdFromAuthName();

        return $employeeId !== null && in_array($employeeId, $this->offerTeamEmployeeIds($leadProductList), true);
    }

    protected function buildOfferTeamAccessPayload(Offer $offer, ?LeadProductList $leadProductList = null): array
    {
        $leadProductList = $leadProductList ?: $this->findLeadProductListForOffer($offer);
        $teamRows = $this->normalizeOfferTeamRows($leadProductList?->teams ?? []);
        $teamEmployeeIds = collect($teamRows)
            ->pluck('employee_id')
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $employees = Employee::query()
            ->select(['id', 'name', 'lastname', 'email', 'image', 'status'])
            ->whereIn('id', $teamEmployeeIds)
            ->get()
            ->keyBy('id');

        $teamMembers = collect($teamRows)->map(function ($team) use ($employees) {
            $employeeId = isset($team['employee_id']) && is_numeric($team['employee_id'])
                ? (int) $team['employee_id']
                : null;

            $employee = $employeeId ? $employees->get($employeeId) : null;
            $employeeName = trim(implode(' ', array_filter([
                $employee?->name,
                $employee?->lastname,
            ])));

            return [
                'employee_id' => $employeeId,
                'id' => $employeeId,
                'employee_name' => $employeeName !== '' ? $employeeName : ($employeeId ? 'Mitarbeiter #' . $employeeId : 'Unbekannt'),
                'name' => $employee?->name,
                'lastname' => $employee?->lastname,
                'employee_email' => $employee?->email,
                'employee_image' => $employee?->image,
                'image' => $employee?->image,
                'employee_status' => $employee?->status,
                'stage' => $team['stage'] ?? null,
                'old_stage' => $team['old_stage'] ?? null,
                'assigned_by' => $team['assigned_by'] ?? null,
                'assigned_at' => $team['assigned_at'] ?? null,
                'company_stage_key' => $team['company_stage_key'] ?? null,
                'product_stage_id' => $team['product_stage_id'] ?? null,
                'product_stage_name' => $team['product_stage_name'] ?? null,
                'product_task_phase_id' => $team['product_task_phase_id'] ?? null,
            ];
        })->values()->all();

        $mode = $this->offerTeamAccessMode($leadProductList);
        $currentEmployeeId = $this->currentEmployeeIdFromAuthName();
        $isTeamMember = $currentEmployeeId !== null && in_array($currentEmployeeId, $teamEmployeeIds->all(), true);
        $isPrivileged = $this->isPrivilegedOfferUser();
        $allowed = $mode === 'all' || $isTeamMember || $isPrivileged;

        return [
            'lead_product_list_id' => $leadProductList?->id,
            'access_mode' => $mode,
            'offer_team_access_mode' => $mode,
            'access_mode_label' => $mode === 'team_only'
                ? 'Nur Angebotsteam'
                : 'Alle Mitarbeiter',
            'team_employee_ids' => $teamEmployeeIds->all(),
            'team_members' => $teamMembers,
            'team' => $teamMembers,
            'teams' => $teamMembers,
            'members' => $teamMembers,
            'authorized_employees' => $teamMembers,
            'current_employee_id' => $currentEmployeeId,
            'is_team_member' => (bool) $isTeamMember,
            'is_privileged' => (bool) $isPrivileged,
            'can_view' => (bool) $allowed,
            'can_edit' => (bool) $allowed,
            'can_create_offer' => (bool) $allowed,
            'message' => $allowed
                ? null
                : 'Dieses Angebot ist auf das Angebotsteam beschränkt.',
        ];
    }

    public function team(Offer $offer): JsonResponse
    {
        $this->prepareJsonResponse();

        $leadProductList = $this->findLeadProductListForOffer($offer);
        $teamAccess = $this->buildOfferTeamAccessPayload($offer, $leadProductList);

        return $this->successResponse([
            'success' => true,
            'lead_product_list_id' => $leadProductList?->id,
            'access_mode' => $teamAccess['access_mode'],
            'offer_team_access_mode' => $teamAccess['offer_team_access_mode'],
            'teams' => $teamAccess['teams'],
            'team_members' => $teamAccess['team_members'],
            'team_access' => $teamAccess,
            'can_view' => $teamAccess['can_view'],
            'can_edit' => $teamAccess['can_edit'],
            'can_create_offer' => $teamAccess['can_create_offer'],
        ]);
    }

    public function syncTeam(Request $request, Offer $offer): JsonResponse
    {
        $this->prepareJsonResponse();

        $validator = Validator::make($request->all(), [
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['required', 'integer', 'exists:employees,id'],
            'stage' => ['nullable', 'string', 'max:50'],
            'offer_team_access_mode' => ['nullable', 'string', 'in:all,team_only'],
            'access_mode' => ['nullable', 'string', 'in:all,team_only'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse(
                'Validierungsfehler: ' . $validator->errors()->first(),
                422
            );
        }

        DB::beginTransaction();

        try {
            $leadProductList = $this->findLeadProductListForOffer($offer);

            if (!$leadProductList) {
                DB::rollBack();

                return $this->errorResponse(
                    'Kein passender LeadProductList-Datensatz für dieses Angebot gefunden.',
                    404
                );
            }

            if (!$this->canEditOfferTeam($leadProductList)) {
                DB::rollBack();

                return $this->errorResponse(
                    'Keine Berechtigung: Nur das Angebotsteam darf diese Einstellung ändern.',
                    403,
                    ['team_access' => $this->buildOfferTeamAccessPayload($offer, $leadProductList)]
                );
            }

            $requestedEmployeeIds = collect($request->input('employee_ids', []))
                ->filter(fn($employeeId) => is_numeric($employeeId))
                ->map(fn($employeeId) => (int) $employeeId)
                ->unique()
                ->values();

            if ($request->has('employee_ids')) {
                $activeEmployeeIds = Employee::query()
                    ->whereIn('id', $requestedEmployeeIds)
                    ->where('status', 'Active')
                    ->pluck('id')
                    ->map(fn($employeeId) => (int) $employeeId)
                    ->values();

                if ($activeEmployeeIds->count() !== $requestedEmployeeIds->count()) {
                    DB::rollBack();

                    return $this->errorResponse(
                        'Bitte wählen Sie nur aktive Mitarbeiter aus.',
                        422
                    );
                }

                $stage = $request->input('stage') ?: ($offer->detail?->document_status ?: 'offer');
                $existingTeams = collect($this->normalizeOfferTeamRows($leadProductList->teams ?? []));

                $newTeams = $requestedEmployeeIds
                    ->map(function ($employeeId) use ($stage, $leadProductList) {
                        return [
                            'employee_id' => (int) $employeeId,
                            'stage' => $stage,
                            'old_stage' => $leadProductList->stage ?? $leadProductList->status ?? null,
                            'assigned_by' => $this->currentEmployeeIdFromAuthName(),
                            'assigned_at' => now()->format('Y-m-d H:i:s'),
                            'company_stage_key' => $leadProductList->status ?? null,
                            'product_stage_id' => $leadProductList->product_stage_id ?? null,
                            'product_stage_name' => $leadProductList->productStage?->name ?? null,
                            'product_task_phase_id' => $leadProductList->product_task_phase_id ?? null,
                        ];
                    })
                    ->values();

                $leadProductList->teams = $existingTeams
                    ->reject(fn($team) => ($team['stage'] ?? null) === $stage)
                    ->merge($newTeams)
                    ->values()
                    ->toArray();
            }

            $accessMode = $request->input('offer_team_access_mode') ?: $request->input('access_mode');
            $hasAccessModeColumn = Schema::hasColumn('lead_product_lists', 'offer_team_access_mode');

            if ($hasAccessModeColumn && in_array($accessMode, ['all', 'team_only'], true)) {
                $leadProductList->offer_team_access_mode = $accessMode;
            }

            $leadProductList->save();
            $leadProductList->refresh();

            DB::commit();

            $this->broadcastOfferChange('offer_team_updated', $offer);

            $teamAccess = $this->buildOfferTeamAccessPayload($offer, $leadProductList);

            return $this->successResponse([
                'success' => true,
                'message' => 'Team-Berechtigung wurde erfolgreich gespeichert.',
                'lead_product_list_id' => $leadProductList->id,
                'teams' => $teamAccess['teams'],
                'team_members' => $teamAccess['team_members'],
                'access_mode' => $teamAccess['access_mode'],
                'offer_team_access_mode' => $teamAccess['offer_team_access_mode'],
                'team_access' => $teamAccess,
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return $this->errorResponse(
                'Fehler beim Speichern des Teams: ' . $e->getMessage(),
                500
            );
        }
    }

}