<?php

namespace App\Http\Controllers\Customer\Offer;


use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\DistributorPrice;
use App\Models\OfferDetail;
use App\Models\OfferFolder;
use App\Models\Product;
use App\Models\LeadProductList;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\OfferFolderAttachment;
use App\Models\MasterSetComponent;
use Illuminate\Support\Facades\Log;
use App\Support\OfferDefaultContent;
use App\Models\Employee;
use App\Models\PositionQualification;
use App\Models\PositionQualificationHierarchy;
class OfferFolderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function jsonSuccess(array $data = [], int $status = 200): JsonResponse
    {
        return response()->json($data, $status, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }

    protected function resolveDisplayDetail(OfferFolder $folder): ?OfferDetail
    {
        $folder->loadMissing(['detail', 'offer.detail']);

        if ($folder->detail) {
            return $folder->detail;
        }

        // FIX: Ensure the folder gets its own empty detail record
        // instead of inheriting the parent offer's items and 'deal' status.
        $this->syncFolderDetailFromOfferDetailIfNewer($folder);

        return $folder->detail;
    }
    public function clone(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'clone_everything' => 'nullable|boolean',
            'create_new_offer' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            $folder->loadMissing([
                'offer',
                'detail',
                'offer.detail',
                'attachments',
            ]);

            if (!$folder->offer_id && !$folder->offer?->id) {
                return $this->jsonError('Kein Angebot für diesen Ordner gefunden.', 422);
            }

            $newFolder = DB::transaction(function () use ($folder, $request) {
                $sourceDetail = $folder->detail ?: $folder->offer?->detail;
                $sourceAttachments = $folder->attachments ?? collect();

                $oldFolderStatus = $folder->status;
                $oldOfferStatus = $folder->offer_status;
                $oldDealStatus = $folder->deal_status;

                // alten Ordner stornieren
                $folder->status = 'cancel';
                $folder->offer_status = \App\Models\OfferFolder::OFFER_STATUS_CANCELLED;

                $history = is_array($folder->history) ? $folder->history : [];
                $history[] = [
                    'type' => 'folder_cloned_and_cancelled',
                    'title' => 'Ordner geklont',
                    'reason' => 'Alter Ordner wurde storniert, neuer Ordner wurde aktiv erstellt.',
                    'changed_by' => $this->currentHistoryActor(),
                    'created_at' => now()->toDateTimeString(),
                    'meta' => [
                        'old_status' => $oldFolderStatus,
                        'old_offer_status' => $oldOfferStatus,
                        'old_deal_status' => $oldDealStatus,
                    ],
                ];
                $folder->history = $history;
                $folder->save();

                // nur echte vorhandene/spätestens sichere Felder setzen
                $newFolder = new OfferFolder();
                $newFolder->offer_id = $folder->offer_id ?: $folder->offer?->id;
                $newFolder->customer_id = $folder->customer_id ?? null;
                $newFolder->alternative_id = $folder->alternative_id ?? null;
                $newFolder->product_id = $folder->product_id ?? null;
                $newFolder->created_by = auth()->user()->name;

                $newFolder->name = $request->filled('name')
                    ? $request->string('name')->toString()
                    : (($folder->name ?: 'Ordner') . ' (Kopie)');

                $newFolder->status = 'active';
                $newFolder->document_status = $folder->document_status ?: 'offer';

                // WICHTIG: color ist laut Fehler Pflicht
                $newFolder->color = !empty($folder->color) ? $folder->color : '#93c21c';

                // nur setzen, wenn das Attribut im Model überhaupt existiert / befüllt ist
                if (array_key_exists('description', $folder->getAttributes())) {
                    $newFolder->description = $folder->description;
                }

                if (array_key_exists('sort_order', $folder->getAttributes())) {
                    $newFolder->sort_order = $folder->sort_order ?? 0;
                }

                if (array_key_exists('is_favorite', $folder->getAttributes())) {
                    $newFolder->is_favorite = $folder->is_favorite ?? 0;
                }

                if (array_key_exists('agb', $folder->getAttributes())) {
                    $newFolder->agb = $folder->agb;
                }

                // Workflow zurücksetzen
                if (($newFolder->document_status ?: 'offer') === 'deal') {
                    $newFolder->deal_status = \App\Models\OfferFolder::DEAL_STATUS_OPEN;
                    $newFolder->offer_status = $folder->offer_status ?: \App\Models\OfferFolder::OFFER_STATUS_DRAFT;
                } else {
                    $newFolder->offer_status = \App\Models\OfferFolder::OFFER_STATUS_DRAFT;
                    $newFolder->deal_status = $folder->deal_status ?: \App\Models\OfferFolder::DEAL_STATUS_OPEN;
                }

                $newFolder->history = [
                    [
                        'type' => 'folder_cloned',
                        'title' => 'Ordner aus bestehendem Ordner erstellt',
                        'reason' => 'Neuer aktiver Ordner wurde aus bestehendem Ordner erstellt.',
                        'changed_by' => $this->currentHistoryActor(),
                        'created_at' => now()->toDateTimeString(),
                        'meta' => [
                            'source_folder_id' => $folder->id,
                            'same_offer_id' => $newFolder->offer_id,
                        ],
                    ]
                ];

                $newFolder->save();

                // Detail klonen
                if ($sourceDetail) {
                    $newDetail = $sourceDetail->replicate();
                    $newDetail->offer_id = $newFolder->offer_id;
                    $newDetail->offer_folder_id = $newFolder->id;
                    $newDetail->document_status = 'offer';

                    $bio = is_array($sourceDetail->biography_data) ? $sourceDetail->biography_data : [];
                    $bio[] = [
                        'type' => 'cloned_from_folder',
                        'from' => $folder->id,
                        'to' => $newFolder->id,
                        'reason' => 'Neuer Ordner wurde als Kopie erstellt.',
                        'changed_by' => $this->currentHistoryActor(),
                        'created_at' => now()->toDateTimeString(),
                    ];
                    $newDetail->biography_data = $bio;

                    $newDetail->save();
                }

                // Attachments physisch kopieren
                foreach ($sourceAttachments as $attachment) {
                    $newAttachment = $attachment->replicate();
                    $newAttachment->offer_folder_id = $newFolder->id;
                    $newAttachment->offer_id = $newFolder->offer_id;

                    if (!empty($attachment->file_path) && Storage::disk('public')->exists($attachment->file_path)) {
                        $ext = pathinfo($attachment->file_path, PATHINFO_EXTENSION);
                        $newPath = 'offers/folder-attachments/' . Str::uuid() . ($ext ? '.' . $ext : '');

                        Storage::disk('public')->copy($attachment->file_path, $newPath);

                        $newAttachment->file_path = $newPath;
                        $newAttachment->file_name = basename($newPath);
                        $newAttachment->file_url = asset('storage/' . $newPath);
                    }

                    $newAttachment->save();
                }

                return $newFolder;
            });

            return $this->jsonSuccess([
                'success' => true,
                'message' => 'Ordner wurde erfolgreich innerhalb desselben Angebots geklont.',
                'folder_id' => $newFolder->id,
                'redirect_url' => route('admin.offers.folders.show', $newFolder),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return $this->jsonError('Ordner konnte nicht geklont werden: ' . $e->getMessage(), 500);
        }
    }


    protected function offerIsLocked(OfferFolder $folder): bool
    {
        $documentStatus = $this->resolveFolderDocumentStatus($folder);

        if ($documentStatus !== OfferFolder::DOCUMENT_STATUS_OFFER) {
            return false;
        }

        $offerStatus = (string) ($folder->offer_status ?: OfferFolder::OFFER_STATUS_DRAFT);

        return in_array($offerStatus, [
            OfferFolder::OFFER_STATUS_ACCEPTED,
            OfferFolder::OFFER_STATUS_CANCELLED,
        ], true);
    }
    protected function resolveFolderDocumentStatus(OfferFolder $folder): string
    {
        $folder->loadMissing(['detail', 'offer.detail']);

        $detailDocumentStatus = $folder->detail?->document_status
            ?? $folder->offer?->detail?->document_status
            ?? null;

        $folderDocumentStatus = $folder->document_status ?? null;

        $resolved = strtolower((string) ($detailDocumentStatus ?: $folderDocumentStatus ?: 'offer'));

        return in_array($resolved, [
            OfferFolder::DOCUMENT_STATUS_OFFER,
            OfferFolder::DOCUMENT_STATUS_DEAL,
        ], true) ? $resolved : OfferFolder::DOCUMENT_STATUS_OFFER;
    }

    protected function resolveAllowedWorkflowStatuses(string $documentStatus): array
    {
        return $this->getKanbanStagesForDocument($documentStatus)
            ->pluck('key')
            ->filter()
            ->values()
            ->unique()
            ->toArray();
    }

    protected function workflowMainStageCandidateKeys(string $documentStatus): array
    {
        return $documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL
            ? ['deal', 'auftrag']
            : ['offer', 'angebot'];
    }

    protected function normalizeWorkflowDocumentStatus(?string $documentStatus): string
    {
        $status = strtolower(trim((string) $documentStatus));

        return in_array($status, [OfferFolder::DOCUMENT_STATUS_DEAL, 'auftrag'], true)
            ? OfferFolder::DOCUMENT_STATUS_DEAL
            : OfferFolder::DOCUMENT_STATUS_OFFER;
    }

    protected function findWorkflowMainStage(string $documentStatus): ?LeadStage
    {
        if (!Schema::hasTable('lead_stages')) {
            return null;
        }

        $documentStatus = $this->normalizeWorkflowDocumentStatus($documentStatus);
        $candidateKeys = $this->workflowMainStageCandidateKeys($documentStatus);

        $stage = LeadStage::query()
            ->whereNull('deleted_at')
            ->where(function ($query) use ($candidateKeys) {
                $query->whereIn(DB::raw('LOWER(`key`)'), $candidateKeys)
                    ->orWhereIn(DB::raw('LOWER(`name`)'), $candidateKeys);
            })
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($stage) {
            return $stage;
        }

        $normalizedLabels = $documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL
            ? ['auftrag', 'deal']
            : ['angebot', 'offer'];

        return LeadStage::query()
            ->whereNull('deleted_at')
            ->where(function ($query) use ($normalizedLabels) {
                foreach ($normalizedLabels as $label) {
                    $query->orWhereRaw('LOWER(`name`) LIKE ?', ['%' . $label . '%'])
                        ->orWhereRaw('LOWER(`key`) LIKE ?', ['%' . $label . '%']);
                }
            })
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();
    }

    protected function workflowStageMappingPayload(string $documentStatus): array
    {
        $documentStatus = $this->normalizeWorkflowDocumentStatus($documentStatus);

        $availableStages = LeadStage::query()
            ->whereNull('deleted_at')
            ->ordered()
            ->get(['id', 'key', 'name', 'color', 'icon', 'sort_order', 'is_active'])
            ->map(fn(LeadStage $stage) => [
                'id' => $stage->id,
                'key' => $stage->key,
                'name' => $stage->name,
                'label' => $stage->name,
                'color' => $stage->color,
                'icon' => $stage->icon,
                'sort_order' => $stage->sort_order,
                'is_active' => (bool) $stage->is_active,
            ])
            ->values()
            ->all();

        return [
            'needs_stage_mapping' => true,
            'document_status' => $documentStatus,
            'required_stage' => $documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL ? 'deal' : 'offer',
            'message' => $documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL
                ? 'Bitte wählen Sie, welche Hauptphase für Auftrag/Deal verwendet werden soll.'
                : 'Bitte wählen Sie, welche Hauptphase für Angebot/Offer verwendet werden soll.',
            'available_stages' => $availableStages,
        ];
    }

    protected function workflowSubStagesForDocument(string $documentStatus)
    {
        if (!Schema::hasTable('lead_stage_sub_stages')) {
            return collect();
        }

        $stage = $this->findWorkflowMainStage($documentStatus);

        if (!$stage) {
            return collect();
        }

        return LeadStageSubStage::query()
            ->where('lead_stage_id', $stage->id)
            ->whereNull('deleted_at')
            ->active()
            ->ordered()
            ->get();
    }

    protected function workflowSubStageByKey(string $documentStatus, string $status): ?LeadStageSubStage
    {
        $status = LeadStage::makeKey($status);

        return $this->workflowSubStagesForDocument($documentStatus)
            ->first(fn(LeadStageSubStage $subStage) => (string) $subStage->key === $status);
    }

    protected function workflowDefaultSubStage(string $documentStatus): ?LeadStageSubStage
    {
        $subStages = $this->workflowSubStagesForDocument($documentStatus);

        return $subStages->firstWhere('is_default', true) ?: $subStages->first();
    }

    protected function getKanbanStagesForDocument(string $documentStatus)
    {
        return $this->workflowSubStagesForDocument($documentStatus)
            ->values()
            ->map(function (LeadStageSubStage $subStage, int $index) use ($documentStatus) {
                return (object) [
                    'id' => $subStage->id,
                    'lead_stage_id' => $subStage->lead_stage_id,
                    'lead_stage_sub_stage_id' => $subStage->id,
                    'document_status' => $this->normalizeWorkflowDocumentStatus($documentStatus),
                    'key' => $subStage->key,
                    'label' => $subStage->name,
                    'name' => $subStage->name,
                    'icon' => $subStage->icon,
                    'color' => $subStage->color ?: '#93c21c',
                    'position' => $subStage->sort_order ?: ($index + 1),
                    'sort_order' => $subStage->sort_order ?: ($index + 1),
                    'is_active' => (bool) $subStage->is_active,
                    'is_default' => (bool) $subStage->is_default,
                    'is_system' => false,
                    'source' => 'lead_stage_sub_stages',
                ];
            });
    }


    protected function getKanbanStageContextForDocument(string $documentStatus): array
    {
        $documentStatus = $this->normalizeWorkflowDocumentStatus($documentStatus);
        $stage = $this->findWorkflowMainStage($documentStatus);

        return [
            'source' => 'lead_stage_sub_stages',
            'document_status' => $documentStatus,
            'main_stage' => $stage ? [
                'id' => $stage->id,
                'key' => $stage->key,
                'name' => $stage->name,
                'label' => $stage->name,
                'color' => $stage->color,
                'icon' => $stage->icon,
                'sort_order' => $stage->sort_order,
                'is_active' => (bool) $stage->is_active,
            ] : null,
            'needs_stage_mapping' => !$stage,
            'available_stages' => LeadStage::query()
                ->whereNull('deleted_at')
                ->ordered()
                ->get(['id', 'key', 'name', 'color', 'icon', 'sort_order', 'is_active'])
                ->map(fn(LeadStage $availableStage) => [
                    'id' => $availableStage->id,
                    'key' => $availableStage->key,
                    'name' => $availableStage->name,
                    'label' => $availableStage->name,
                    'color' => $availableStage->color,
                    'icon' => $availableStage->icon,
                    'sort_order' => $availableStage->sort_order,
                    'is_active' => (bool) $availableStage->is_active,
                ])
                ->values()
                ->all(),
        ];
    }

    protected function getFolderWorkflowStatus(OfferFolder $folder): string
    {
        $documentStatus = $this->resolveFolderDocumentStatus($folder);

        if ($documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL) {
            return (string) ($folder->deal_status ?: OfferFolder::DEAL_STATUS_OPEN);
        }

        return (string) ($folder->offer_status ?: OfferFolder::OFFER_STATUS_DRAFT);
    }

    protected function setFolderWorkflowStatus(OfferFolder $folder, string $status): void
    {
        $documentStatus = $this->resolveFolderDocumentStatus($folder);

        if ($documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL) {
            $folder->deal_status = $status;
        } else {
            $folder->offer_status = $status;
        }
    }

    protected function getWorkflowStatusLabelByContext(string $documentStatus, string $status): string
    {
        $subStage = $this->workflowSubStageByKey($documentStatus, $status);

        if ($subStage) {
            return $subStage->name;
        }

        return ucfirst(str_replace('_', ' ', (string) $status));
    }

    protected function syncFolderWorkflowToLeadProductList(OfferFolder $folder, string $documentStatus, LeadStageSubStage $subStage): void
    {
        $leadProductList = $this->findLeadProductListForFolder($folder);

        if (!$leadProductList) {
            return;
        }

        $updates = [];

        if (Schema::hasColumn('lead_product_lists', 'lead_stage_id')) {
            $updates['lead_stage_id'] = $subStage->lead_stage_id;
        }

        if (Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
            $updates['lead_stage_sub_stage_id'] = $subStage->id;
        }

        if (Schema::hasColumn('lead_product_lists', 'lead_sub_stage_id')) {
            $updates['lead_sub_stage_id'] = $subStage->id;
        }

        if (Schema::hasColumn('lead_product_lists', 'status')) {
            $updates['status'] = $documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL ? 'deal' : 'offer';
        }

        if (Schema::hasColumn('lead_product_lists', 'stage_mode')) {
            $updates['stage_mode'] = $documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL ? 'deal' : 'offer';
        }

        if (Schema::hasColumn('lead_product_lists', 'sub_stage_key')) {
            $updates['sub_stage_key'] = $subStage->key;
        }

        if (!empty($updates)) {
            $leadProductList->forceFill($updates)->save();
        }
    }

    protected function resolveDetailForFolder(OfferFolder $folder, ?int $fallbackOfferId = null): OfferDetail
    {
        $folder->loadMissing(['offer.detail', 'detail']);

        if ($folder->detail) {
            return $folder->detail;
        }

        $offerId = (int) ($folder->offer_id ?: $folder->offer?->id ?: $fallbackOfferId);

        if ($offerId <= 0) {
            throw new \RuntimeException('Für diesen Ordner konnte keine gültige Angebot-ID ermittelt werden.');
        }

        // FIX: Create a fresh, empty detail. Do NOT fallback to $folder->offer->detail
        return OfferDetail::create([
            'offer_id' => $offerId,
            'offer_folder_id' => $folder->id,
            'document_status' => OfferDetail::DOCUMENT_STATUS_OFFER, // Always start as an offer
            'angebot_snapshot_sections' => [],
            'angebot_snapshot_at' => null,
            'tax_rate' => $folder->offer?->detail?->tax_rate ?? 19,
            'total_net' => 0,
            'total_gross' => 0,
            'sections' => [], // Empty!
            'placed_images' => [],
            'print_attachments' => [],
            'biography_data' => [],
            'material_history' => [],
            'company_name' => $folder->offer?->detail?->company_name,
            'brand_mode' => $folder->offer?->detail?->brand_mode,
            'brand_color' => $folder->offer?->detail?->brand_color,
            'brand_logo_url' => $folder->offer?->detail?->brand_logo_url,
            'cover_text' => $folder->offer?->detail?->cover_text,
            'cover_text_html' => $folder->offer?->detail?->cover_text_html,
        ]);
    }
    protected function jsonError(string $message, int $status = 422, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
        ], $extra), $status, [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }


    /**
     * Offer team / access control
     * Source of truth: lead_product_lists.teams + lead_product_lists.offer_team_access_mode
     */
    protected function findLeadProductListForFolder(OfferFolder $folder): ?LeadProductList
    {
        $folder->loadMissing('offer');

        $offer = $folder->offer;

        $customerId = $folder->customer_id ?: $offer?->customer_id;
        $alternativeId = $folder->alternative_id ?: $offer?->alternative_id;
        $productId = $folder->product_id ?: $offer?->product_id;

        if (!$customerId || !$productId) {
            return null;
        }

        return LeadProductList::query()
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->when($alternativeId, function ($query) use ($alternativeId) {
                $query->where('alternative_id', $alternativeId);
            })
            ->first();
    }

    protected function normalizeTeamRows($teams): array
    {
        if (is_string($teams)) {
            $decoded = json_decode($teams, true);
            $teams = is_array($decoded) ? $decoded : [];
        }

        return is_array($teams) ? array_values($teams) : [];
    }

    protected function extractTeamEmployeeIds($teams): array
    {
        return collect($this->normalizeTeamRows($teams))
            ->pluck('employee_id')
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    protected function currentEmployeeId(): ?int
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

    protected function resolveOfferTeamAccessMode(?LeadProductList $leadProductList): string
    {
        if (!$leadProductList || !Schema::hasColumn('lead_product_lists', 'offer_team_access_mode')) {
            return 'all';
        }

        $mode = strtolower(trim((string) ($leadProductList->offer_team_access_mode ?: 'all')));

        return in_array($mode, ['team_only', 'all'], true) ? $mode : 'all';
    }

    protected function buildOfferTeamAccessPayload(OfferFolder $folder): array
    {
        $leadProductList = $this->findLeadProductListForFolder($folder);
        $teamRows = $this->normalizeTeamRows($leadProductList?->teams ?? []);
        $teamEmployeeIds = $this->extractTeamEmployeeIds($teamRows);
        $currentEmployeeId = $this->currentEmployeeId();
        $accessMode = $this->resolveOfferTeamAccessMode($leadProductList);
        $isPrivileged = $this->isPrivilegedOfferUser();
        $isTeamMember = $currentEmployeeId && in_array($currentEmployeeId, $teamEmployeeIds, true);
        $allowed = $accessMode === 'all' || $isTeamMember || $isPrivileged;

        $employees = Employee::query()
            ->select(['id', 'name', 'lastname', 'email', 'image', 'status'])
            ->whereIn('id', $teamEmployeeIds)
            ->get()
            ->keyBy('id');

        $teamMembers = collect($teamRows)->map(function ($row) use ($employees) {
            $employeeId = isset($row['employee_id']) && is_numeric($row['employee_id'])
                ? (int) $row['employee_id']
                : null;

            $employee = $employeeId ? $employees->get($employeeId) : null;
            $name = trim(($employee?->name ?? '') . ' ' . ($employee?->lastname ?? ''));

            return [
                'employee_id' => $employeeId,
                'employee_name' => $name !== '' ? $name : ($employeeId ? 'Mitarbeiter #' . $employeeId : 'Unbekannt'),
                'employee_email' => $employee?->email,
                'employee_image' => $employee?->image,
                'employee_status' => $employee?->status,
                'stage' => $row['stage'] ?? null,
                'assigned_at' => $row['assigned_at'] ?? null,
                'assigned_by' => $row['assigned_by'] ?? null,
            ];
        })->values()->all();

        return [
            'lead_product_list_id' => $leadProductList?->id,
            'access_mode' => $accessMode,
            'access_mode_label' => $accessMode === 'team_only'
                ? 'Nur Team darf sehen & erstellen'
                : 'Alle Mitarbeiter dürfen sehen & erstellen',
            'team_employee_ids' => $teamEmployeeIds,
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
        ];
    }

    protected function canAccessFolderByTeam(OfferFolder $folder): bool
    {
        return (bool) ($this->buildOfferTeamAccessPayload($folder)['can_view'] ?? false);
    }

    protected function denyFolderAccessJson(OfferFolder $folder): JsonResponse
    {
        return $this->jsonError(
            'Keine Berechtigung: Dieses Angebot ist auf das zugewiesene Team beschränkt.',
            403,
            ['team_access' => $this->buildOfferTeamAccessPayload($folder)]
        );
    }

    public function show(OfferFolder $folder)
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            abort(403, 'Keine Berechtigung: Dieses Angebot ist auf das zugewiesene Team beschränkt.');
        }

        $folder->load([
            'offer.customer',
            'offer.alternative',
            'offer.product',
            'offer.detail',
            'creator',
            'detail',
        ]);

        $defaultAgb = OfferDefaultContent::defaultAgb();
        $folderAgb = filled($folder->agb) ? $folder->agb : $defaultAgb;
        $detail = $this->resolveDisplayDetail($folder);

        return view('admin.offer.folder-show', [
            'folder' => $folder,
            'detail' => $detail,
            'defaultAgb' => $defaultAgb,
            'folderAgb' => $folderAgb,
        ]);
    }


    public function data(OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $this->syncFolderDetailFromOfferDetailIfNewer($folder);
        $folder->refresh();
        $folder->load([
            'offer.customer',
            'offer.alternative',
            'offer.product',
            'offer.detail',
            'creator',
            'detail',
            'attachments',
            'pdfPrints',
        ]);

        $offer = $folder->offer;
        $detail = $this->resolveDisplayDetail($folder);

        $sections = is_array($detail?->sections) ? $detail->sections : [];

        // Repair missing product/distributor data from master_set_components
        $sections = $this->hydrateSectionsFromMasterSetComponents($sections);

        // Keep repaired data in memory for this response
        if ($detail) {
            $detail->sections = $sections;
        }

        $distributorIds = $this->collectDistributorIdsFromSections($sections);

        $distributors = Distributor::query()
            ->whereIn('id', $distributorIds)
            ->get(['id', 'name', 'short_name'])
            ->mapWithKeys(function (Distributor $distributor) {
                $name = trim($distributor->short_name ?: $distributor->name ?: ('Lieferant #' . $distributor->id));
                return [(string) $distributor->id => $name];
            })
            ->toArray();

        $defaultAgb = OfferDefaultContent::defaultAgb();
        $folderAgb = filled($folder->agb) ? $folder->agb : null;

        $pdfs = $folder->pdfPrints()
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($pdf, $index) {
                $url = null;

                if (!empty($pdf->file_path)) {
                    $url = asset('storage/' . ltrim($pdf->file_path, '/'));
                }

                return [
                    'id' => $pdf->id,
                    'title' => pathinfo($pdf->file_name, PATHINFO_FILENAME),
                    'file_name' => $pdf->file_name,
                    'file_path' => $pdf->file_path,
                    'url' => $url,
                    'status' => $pdf->status,
                    'is_active' => (bool) $pdf->is_active,
                    'employee_id' => $pdf->employee_id,
                    'date' => optional($pdf->created_at)->format('d.m.Y H:i'),
                    'created_at' => $pdf->created_at,
                    'is_latest' => $index === 0,
                ];
            })
            ->values();

        Log::info('folder.data debug', [
            'folder_id' => $folder->id,
            'offer_id' => $offer?->id,
            'detail_id' => $detail?->id,
            'sections_count' => count($sections),
            'distributor_ids' => $distributorIds,
            'first_section' => $sections[0] ?? null,
            'section_items_summary' => collect($sections)->map(function ($section, $sectionIndex) {
                $items = collect($section['items'] ?? [])->map(function ($item, $itemIndex) use ($sectionIndex) {
                    return [
                        'section_index' => $sectionIndex,
                        'item_index' => $itemIndex,
                        'name' => $item['name'] ?? $item['title'] ?? null,
                        'kind' => $item['kind'] ?? null,
                        'id' => $item['id'] ?? null,
                        'product_id' => $item['product_id'] ?? null,
                        'productId' => $item['productId'] ?? null,
                        'article_no' => $item['article_no'] ?? null,
                        'distributor_article_no' => $item['distributor_article_no'] ?? null,
                        'distributor_id' => $item['distributor_id'] ?? null,
                        'distributor_name' => $item['distributor_name'] ?? null,
                        'distributor_price_id' => $item['distributor_price_id'] ?? null,
                        'subItems_count' => count($item['subItems'] ?? []),
                        'subItems' => collect($item['subItems'] ?? [])->map(function ($subItem, $subIndex) use ($sectionIndex, $itemIndex) {
                            return [
                                'section_index' => $sectionIndex,
                                'item_index' => $itemIndex,
                                'sub_index' => $subIndex,
                                'name' => $subItem['name'] ?? $subItem['title'] ?? null,
                                'kind' => $subItem['kind'] ?? null,
                                'id' => $subItem['id'] ?? null,
                                'component_id' => $subItem['component_id'] ?? null,
                                'product_id' => $subItem['product_id'] ?? null,
                                'productId' => $subItem['productId'] ?? null,
                                'article_no' => $subItem['article_no'] ?? null,
                                'distributor_article_no' => $subItem['distributor_article_no'] ?? null,
                                'distributor_id' => $subItem['distributor_id'] ?? null,
                                'distributor_name' => $subItem['distributor_name'] ?? null,
                                'distributor_price_id' => $subItem['distributor_price_id'] ?? null,
                            ];
                        })->values()->all(),
                    ];
                })->values()->all();

                return [
                    'section_index' => $sectionIndex,
                    'section_title' => $section['title'] ?? null,
                    'items' => $items,
                ];
            })->values()->all(),
        ]);

        return $this->jsonSuccess([
            'success' => true,
            'folder' => $folder,
            'offer' => $offer,
            'detail' => $detail,
            'document_status' => $this->resolveFolderDocumentStatus($folder),
            'document_status_label' => $detail?->documentStatusLabel() ?? 'Angebot',
            'angebot_snapshot_sections' => is_array($detail?->angebot_snapshot_sections) ? $detail->angebot_snapshot_sections : [],
            'angebot_snapshot_at' => $detail?->angebot_snapshot_at,
            'distributors' => $distributors,
            'pdfs' => $pdfs,
            'agb' => [
                'title' => $folderAgb['title'] ?? $detail?->agb_title ?? $defaultAgb['title'] ?? null,
                'text' => $folderAgb['text'] ?? $detail?->agb_text ?? $defaultAgb['text'] ?? null,
            ],
            'attachments' => $folder->attachments()->orderBy('sort_order')->orderBy('id')->get(),
            'document_status' => $this->resolveFolderDocumentStatus($folder),
            'offer_status' => $folder->offer_status,
            'offer_status_label' => $folder->offer_status_label,
            'deal_status' => $folder->deal_status,
            'deal_status_label' => $folder->deal_status_label,
            'workflow_status' => $folder->workflow_status,
            'workflow_status_label' => $folder->workflow_status_label,
            'workflow_status_badge_class' => $folder->workflow_status_badge_class,
            'kanban_stages' => $this->getKanbanStagesForDocument($this->resolveFolderDocumentStatus($folder))->values(),
            'kanban_stage_context' => $this->getKanbanStageContextForDocument($this->resolveFolderDocumentStatus($folder)),
            'workflow_stage_mapping' => $this->findWorkflowMainStage($this->resolveFolderDocumentStatus($folder))
                ? [
                    'needs_stage_mapping' => false,
                    'main_stage' => [
                        'id' => $this->findWorkflowMainStage($this->resolveFolderDocumentStatus($folder))?->id,
                        'key' => $this->findWorkflowMainStage($this->resolveFolderDocumentStatus($folder))?->key,
                        'name' => $this->findWorkflowMainStage($this->resolveFolderDocumentStatus($folder))?->name,
                    ],
                    'source' => 'lead_stage_sub_stages',
                ]
                : $this->workflowStageMappingPayload($this->resolveFolderDocumentStatus($folder)),
            'team_access' => $this->buildOfferTeamAccessPayload($folder),
        ]);
    }



    protected function hydrateSectionsFromMasterSetComponents(array $sections): array
    {
        $componentIds = [];

        foreach ($sections as $section) {
            foreach (($section['items'] ?? []) as $item) {
                foreach (($item['subItems'] ?? []) as $subItem) {
                    $componentId = (int) ($subItem['component_id'] ?? 0);
                    if ($componentId > 0) {
                        $componentIds[] = $componentId;
                    }
                }
            }
        }

        $componentIds = array_values(array_unique(array_filter($componentIds)));

        if (empty($componentIds)) {
            return $sections;
        }

        $components = MasterSetComponent::query()
            ->with([
                'distributor:id,name,short_name',
                'distributorPrice:id,distributor_id,product_id,article_no,price,purchase_price,availability',
                'product:id,product,article_no',
            ])
            ->whereIn('id', $componentIds)
            ->get()
            ->keyBy('id');

        foreach ($sections as &$section) {
            $items = is_array($section['items'] ?? null) ? $section['items'] : [];

            foreach ($items as &$item) {
                // Repair parent item too if needed
                $item = $this->hydrateNodeFromOwnFields($item);

                $subItems = is_array($item['subItems'] ?? null) ? $item['subItems'] : [];

                foreach ($subItems as &$subItem) {
                    $componentId = (int) ($subItem['component_id'] ?? 0);

                    if ($componentId > 0 && isset($components[$componentId])) {
                        $subItem = $this->hydrateNodeFromComponent($subItem, $components[$componentId]);
                    } else {
                        $subItem = $this->hydrateNodeFromOwnFields($subItem);
                    }
                }

                unset($subItem);

                $item['subItems'] = $subItems;
            }

            unset($item);

            $section['items'] = $items;
        }

        unset($section);

        return $sections;
    }

    protected function hydrateNodeFromComponent(array $node, MasterSetComponent $component): array
    {
        $distributor = $component->distributor;
        $distributorPrice = $component->distributorPrice;
        $product = $component->product;

        $distributorName = null;
        if ($distributor) {
            $distributorName = trim(
                $distributor->short_name
                ?: $distributor->name
                ?: ('Lieferant #' . $distributor->id)
            );
        }

        $resolvedProductId = $node['product_id'] ?? $node['productId'] ?? $component->product_id ?? $product?->id ?? null;

        $node['component_id'] = $node['component_id'] ?? $component->id;
        $node['product_id'] = $resolvedProductId;
        $node['productId'] = $resolvedProductId;

        $node['article_no'] = !empty($node['article_no'])
            ? $node['article_no']
            : ($component->article_no ?: ($product?->article_no));

        $node['distributor_article_no'] = !empty($node['distributor_article_no'])
            ? $node['distributor_article_no']
            : ($component->distributor_article_no ?: ($distributorPrice?->article_no));

        $node['distributor_id'] = $node['distributor_id'] ?? $component->distributor_id ?? $distributor?->id;
        $node['distributor_price_id'] = $node['distributor_price_id'] ?? $component->distributor_price_id ?? $distributorPrice?->id;
        $node['distributor_name'] = $node['distributor_name'] ?? $distributorName;
        $node['supplier'] = $node['supplier'] ?? $distributorName;

        $node['purchase_price'] = $this->preferNumeric(
            $node['purchase_price'] ?? null,
            $component->purchase_price,
            $distributorPrice?->purchase_price
        );

        $node['price'] = $this->preferNumeric(
            $node['price'] ?? null,
            $component->unit_price,
            $distributorPrice?->price
        );

        $node['ek'] = $this->preferNumeric(
            $node['ek'] ?? null,
            $component->purchase_price,
            $distributorPrice?->purchase_price
        );

        $node['rate'] = $this->preferNumeric(
            $node['rate'] ?? null,
            $component->unit_price,
            $distributorPrice?->price
        );

        $node['skonto'] = $this->preferNumeric(
            $node['skonto'] ?? null,
            $component->skonto,
            $distributor?->cash_discount
        );

        $node['payment_terms'] = $node['payment_terms'] ?? $component->payment_terms ?? $distributor?->payment_terms;
        $node['availability'] = $node['availability'] ?? $component->availability ?? $distributorPrice?->availability;

        if (empty($node['name']) && $product) {
            $node['name'] = $product->product;
        }

        return $node;
    }

    protected function hydrateNodeFromOwnFields(array $node): array
    {
        $productId = (int) ($node['productId'] ?? $node['product_id'] ?? 0);

        if ($productId > 0) {
            $node['product_id'] = $productId;
            $node['productId'] = $productId;
        }

        if (!empty($node['component_id'])) {
            $componentId = (int) $node['component_id'];

            $component = MasterSetComponent::query()
                ->with([
                    'distributor:id,name,short_name',
                    'distributorPrice:id,distributor_id,product_id,article_no,price,purchase_price,availability',
                    'product:id,product,article_no',
                ])
                ->find($componentId);

            if ($component) {
                return $this->hydrateNodeFromComponent($node, $component);
            }
        }

        if (!empty($node['distributor_id']) && empty($node['distributor_name'])) {
            $distributor = Distributor::query()
                ->select('id', 'name', 'short_name')
                ->find((int) $node['distributor_id']);

            if ($distributor) {
                $name = trim($distributor->short_name ?: $distributor->name ?: ('Lieferant #' . $distributor->id));
                $node['distributor_name'] = $name;
                $node['supplier'] = $node['supplier'] ?? $name;
            }
        }

        return $node;
    }
    protected function preferNumeric(...$values): ?float
    {
        foreach ($values as $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (is_numeric($value)) {
                return (float) $value;
            }
        }

        return null;
    }

    public function uploadAttachments(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'offer_id' => 'nullable|integer|exists:offers,id',
            'document_type' => 'nullable|string|max:255',
            'notice' => 'nullable|string|max:2000',
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ]);

        if ($validator->fails()) {
            Log::warning('uploadAttachments validation failed', [
                'folder_route_id' => $folder->id ?? null,
                'request_offer_id' => $request->input('offer_id'),
                'errors' => $validator->errors()->toArray(),
            ]);

            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            if (!$folder->id) {
                Log::error('uploadAttachments missing folder id', [
                    'folder_model' => $folder ? $folder->toArray() : null,
                ]);

                return $this->jsonError('Ordner-ID fehlt.', 422);
            }

            $folder->loadMissing('offer');

            $offerId = $folder->offer_id
                ?: $folder->offer?->id
                ?: $request->integer('offer_id')
                ?: null;

            $documentType = trim((string) $request->input('document_type', ''));
            $notice = trim((string) $request->input('notice', ''));

            $documentType = $documentType !== '' ? $documentType : null;
            $notice = $notice !== '' ? $notice : null;

            $nextSort = ((int) OfferFolderAttachment::where('offer_folder_id', $folder->id)->max('sort_order')) + 1;

            Log::info('uploadAttachments started', [
                'folder_id' => (int) $folder->id,
                'offer_id' => $offerId,
                'files_count' => count($request->file('files', [])),
                'document_type' => $documentType,
                'notice' => $notice,
                'next_sort' => $nextSort,
            ]);

            foreach ($request->file('files', []) as $index => $file) {
                $storedPath = $file->store('offers/folder-attachments', 'public');
                $mime = (string) $file->getClientMimeType();
                $extension = strtolower((string) $file->getClientOriginalExtension());

                $type = 'other';

                if (str_starts_with($mime, 'image/')) {
                    $type = 'image';
                } elseif ($mime === 'application/pdf' || $extension === 'pdf') {
                    $type = 'pdf';
                }

                $payload = [
                    'offer_folder_id' => (int) $folder->id,
                    'offer_id' => $offerId ?: null,
                    'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'original_name' => $file->getClientOriginalName(),
                    'file_name' => basename($storedPath),
                    'file_path' => $storedPath,
                    'file_url' => asset('storage/' . $storedPath),
                    'mime_type' => $mime,
                    'extension' => $extension,
                    'file_size' => (int) $file->getSize(),
                    'file_type' => $type,
                    'document_type' => $documentType,
                    'notice' => $notice,
                    'sort_order' => $nextSort++,
                ];

                Log::info('uploadAttachments payload before save', [
                    'index' => $index,
                    'payload' => $payload,
                ]);

                $attachment = OfferFolderAttachment::create($payload);

                Log::info('uploadAttachments saved attachment', [
                    'saved_id' => $attachment->id,
                    'saved_notice' => $attachment->notice,
                    'saved_doc_type' => $attachment->document_type,
                    'saved_attributes' => $attachment->fresh()?->toArray(),
                ]);
            }

            $attachments = $folder->attachments()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            Log::info('uploadAttachments completed', [
                'folder_id' => (int) $folder->id,
                'offer_id' => $offerId,
                'attachments_count' => $attachments->count(),
                'attachment_ids' => $attachments->pluck('id')->values()->all(),
            ]);

            return $this->jsonSuccess([
                'success' => true,
                'message' => 'Dateien wurden hochgeladen.',
                'attachments' => $attachments,
            ]);
        } catch (\Throwable $e) {
            Log::error('uploadAttachments failed', [
                'folder_id' => $folder->id ?? null,
                'request_offer_id' => $request->input('offer_id'),
                'resolved_offer_id' => $folder->offer_id ?: $folder->offer?->id ?: $request->integer('offer_id') ?: null,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            report($e);

            return $this->jsonError('Dateien konnten nicht hochgeladen werden: ' . $e->getMessage(), 500);
        }
    }
    public function deleteAttachment(OfferFolder $folder, OfferFolderAttachment $attachment): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        try {
            if ((int) $attachment->offer_folder_id !== (int) $folder->id) {
                return $this->jsonError('Datei gehört nicht zu diesem Ordner.', 404);
            }

            if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $attachment->delete();

            $remaining = $folder->attachments()->orderBy('sort_order')->orderBy('id')->get();

            foreach ($remaining as $index => $item) {
                $item->sort_order = $index + 1;
                $item->save();
            }

            return $this->jsonSuccess([
                'success' => true,
                'message' => 'Datei wurde gelöscht.',
                'attachments' => $folder->attachments()->orderBy('sort_order')->orderBy('id')->get(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return $this->jsonError('Datei konnte nicht gelöscht werden: ' . $e->getMessage(), 500);
        }
    }


    public function sortAttachments(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:offer_folder_attachments,id',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            $ids = collect($request->input('ids', []));

            $attachments = OfferFolderAttachment::where('offer_folder_id', $folder->id)
                ->whereIn('id', $ids)
                ->get()
                ->keyBy('id');

            foreach ($ids as $index => $id) {
                $item = $attachments->get((int) $id);
                if ($item) {
                    $item->sort_order = $index + 1;
                    $item->save();
                }
            }

            return $this->jsonSuccess([
                'success' => true,
                'message' => 'Reihenfolge gespeichert.',
                'attachments' => $folder->attachments()->orderBy('sort_order')->orderBy('id')->get(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return $this->jsonError('Reihenfolge konnte nicht gespeichert werden: ' . $e->getMessage(), 500);
        }
    }


    public function saveAgb(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'offer_id' => 'nullable|integer|exists:offers,id',
            'agb_title' => 'nullable|string|max:255',
            'agb_text' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            $detail = $this->resolveDetailForFolder($folder, $request->integer('offer_id'));

            $agbTitle = $request->input('agb_title');
            $agbText = $request->input('agb_text');

            $detail->agb_title = $agbTitle;
            $detail->agb_text = $agbText;
            $detail->save();

            $folder->agb = [
                'title' => $agbTitle,
                'text' => $agbText,
            ];
            $folder->save();

            return $this->jsonSuccess([
                'success' => true,
                'message' => 'AGB wurde gespeichert.',
                'detail' => $detail,
                'agb' => $folder->agb,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return $this->jsonError('AGB konnte nicht gespeichert werden: ' . $e->getMessage(), 500);
        }
    }
    public function uploadPrintAttachments(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'offer_id' => 'nullable|integer|exists:offers,id',
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            $detail = $this->resolveDetailForFolder($folder, $request->integer('offer_id'));

            $existing = is_array($detail->print_attachments) ? $detail->print_attachments : [];
            $nextSort = count($existing) + 1;

            foreach ($request->file('files', []) as $file) {
                $storedPath = $file->store('offers/print-attachments', 'public');

                $existing[] = [
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'original_name' => $file->getClientOriginalName(),
                    'file_name' => basename($storedPath),
                    'path' => $storedPath,
                    'url' => asset('storage/' . $storedPath),
                    'mime_type' => $file->getClientMimeType(),
                    'extension' => strtolower($file->getClientOriginalExtension()),
                    'size' => (int) $file->getSize(),
                    'type' => str_starts_with((string) $file->getClientMimeType(), 'image/') ? 'image' : 'pdf',
                    'sort_order' => $nextSort++,
                    'uploaded_at' => now()->toDateTimeString(),
                ];
            }

            $detail->print_attachments = array_values($existing);
            $detail->save();

            return $this->jsonSuccess([
                'success' => true,
                'message' => 'Dateien wurden hochgeladen.',
                'attachments' => $detail->print_attachments,
                'detail' => $detail,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return $this->jsonError('Dateien konnten nicht hochgeladen werden: ' . $e->getMessage(), 500);
        }
    }


    public function deletePrintAttachment(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'attachment_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            $detail = $this->resolveDetailForFolder($folder, $request->integer('offer_id'));
            $attachments = is_array($detail->print_attachments) ? $detail->print_attachments : [];
            $attachmentId = (string) $request->input('attachment_id');

            $newAttachments = [];
            $deleted = null;

            foreach ($attachments as $attachment) {
                if (($attachment['id'] ?? null) === $attachmentId) {
                    $deleted = $attachment;
                    continue;
                }

                $newAttachments[] = $attachment;
            }

            if (!$deleted) {
                return $this->jsonError('Datei wurde nicht gefunden.', 404);
            }

            if (!empty($deleted['path']) && Storage::disk('public')->exists($deleted['path'])) {
                Storage::disk('public')->delete($deleted['path']);
            }

            $newAttachments = array_values(array_map(function ($item, $index) {
                $item['sort_order'] = $index + 1;
                return $item;
            }, $newAttachments, array_keys($newAttachments)));

            $detail->print_attachments = $newAttachments;
            $detail->save();

            return $this->jsonSuccess([
                'success' => true,
                'message' => 'Datei wurde entfernt.',
                'attachments' => $detail->print_attachments,
                'detail' => $detail,
            ]);
        } catch (Throwable $e) {
            report($e);

            return $this->jsonError('Datei konnte nicht gelöscht werden: ' . $e->getMessage(), 500);
        }
    }

    public function materialComparison(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|integer|exists:products,id',
            'items.*.component_id' => 'nullable|integer|exists:master_set_components,id',
            'items.*.name' => 'nullable|string',
            'items.*.qty' => 'nullable|numeric|min:0',
            'items.*.current_distributor_id' => 'nullable|integer',
            'items.*.current_distributor_price_id' => 'nullable|integer',
            'items.*.current_price' => 'nullable|numeric|min:0',
            'items.*.article_no' => 'nullable|string',
            'items.*.distributor_article_no' => 'nullable|string',
            'items.*.unit' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            $this->ensureExecutionDocumentStatus($folder);

            $items = collect($request->input('items', []))
                ->map(function (array $item) {
                    $productId = !empty($item['product_id']) ? (int) $item['product_id'] : null;
                    $componentId = !empty($item['component_id']) ? (int) $item['component_id'] : null;

                    if (!$productId && $componentId) {
                        $component = MasterSetComponent::query()
                            ->select('id', 'product_id')
                            ->find($componentId);

                        if ($component && (int) $component->product_id > 0) {
                            $productId = (int) $component->product_id;
                        }
                    }

                    return [
                        'product_id' => $productId,
                        'component_id' => $componentId,
                        'name' => $item['name'] ?? null,
                        'qty' => (float) ($item['qty'] ?? 0),
                        'unit' => $item['unit'] ?? null,
                        'article_no' => trim((string) ($item['article_no'] ?? '')),
                        'distributor_article_no' => trim((string) ($item['distributor_article_no'] ?? '')),
                        'current_distributor_id' => !empty($item['current_distributor_id']) ? (int) $item['current_distributor_id'] : null,
                        'current_distributor_price_id' => !empty($item['current_distributor_price_id']) ? (int) $item['current_distributor_price_id'] : null,
                        'current_price' => isset($item['current_price']) ? (float) $item['current_price'] : null,
                    ];
                })
                ->filter(function (array $item) {
                    return !empty($item['product_id']) || $item['article_no'] !== '' || $item['distributor_article_no'] !== '';
                })
                ->values();

            if ($items->isEmpty()) {
                return $this->jsonError('Keine gültigen Positionen für den Preisvergleich übergeben.', 422);
            }

            $productIds = $items->pluck('product_id')->filter()->unique()->values();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->get(['id', 'product', 'article_no'])
                ->keyBy('id');

            $prices = DistributorPrice::query()
                ->with(['distributor:id,name,short_name,payment_terms,cash_discount,status'])
                ->when($productIds->isNotEmpty(), function ($query) use ($productIds) {
                    $query->whereIn('product_id', $productIds);
                })
                ->get([
                    'id',
                    'distributor_id',
                    'product_id',
                    'article_no',
                    'price',
                    'purchase_price',
                    'discount_price',
                    'discount_percent',
                    'price_date',
                    'availability',
                    'status',
                    'notice',
                ]);

            $resultItems = $items->map(function (array $item) use ($products, $prices) {
                $product = !empty($item['product_id']) ? $products->get($item['product_id']) : null;

                $options = $prices->filter(function (DistributorPrice $price) use ($item) {
                    if (!empty($item['product_id']) && (int) $price->product_id === (int) $item['product_id']) {
                        return true;
                    }

                    $reqDistributorArticle = trim((string) ($item['distributor_article_no'] ?? ''));
                    $reqArticle = trim((string) ($item['article_no'] ?? ''));
                    $priceArticle = trim((string) ($price->article_no ?? ''));

                    if ($reqDistributorArticle !== '' && $priceArticle !== '' && $reqDistributorArticle === $priceArticle) {
                        return true;
                    }

                    if ($reqArticle !== '' && $priceArticle !== '' && $reqArticle === $priceArticle) {
                        return true;
                    }

                    return false;
                });

                $mappedOptions = $options->map(function (DistributorPrice $price) use ($item) {
                    // IMPORTANT: compare with EK, not VK
                    $basePrice = (float) ($price->purchase_price ?? $price->price ?? 0);
                    $cashDiscount = (float) ($price->distributor->cash_discount ?? 0);
                    $effectivePrice = $basePrice;

                    if ($cashDiscount > 0) {
                        $effectivePrice = $basePrice - ($basePrice * ($cashDiscount / 100));
                    }

                    $qty = (float) ($item['qty'] ?? 0);

                    return [
                        'distributor_price_id' => $price->id,
                        'distributor_id' => $price->distributor_id,
                        'distributor_name' => trim($price->distributor->short_name ?: $price->distributor->name ?: ('Lieferant #' . $price->distributor_id)),
                        'article_no' => $price->article_no,
                        'price' => (float) ($price->price ?? 0),                    // VK
                        'purchase_price' => (float) ($price->purchase_price ?? 0),  // EK
                        'discount_price' => (float) ($price->discount_price ?? 0),
                        'discount_percent' => (float) ($price->discount_percent ?? 0),
                        'availability' => $price->availability,
                        'payment_terms' => $price->distributor->payment_terms,
                        'cash_discount' => $cashDiscount,
                        'effective_price' => round($effectivePrice, 2),             // EK after cash discount
                        'line_total' => round($effectivePrice * $qty, 2),           // EK total
                        'is_current' => (int) ($item['current_distributor_price_id'] ?? 0) === (int) $price->id
                            || (
                                !empty($item['current_distributor_id'])
                                && (int) $item['current_distributor_id'] === (int) $price->distributor_id
                            ),
                    ];
                })->sortBy('effective_price')->values();

                $currentOption = $mappedOptions->first(function (array $option) {
                    return !empty($option['is_current']);
                });

                $bestOption = $mappedOptions->first();

                $alternativeOptions = $mappedOptions->filter(function (array $option) use ($currentOption) {
                    if (!$currentOption) {
                        return true;
                    }

                    return (int) ($option['distributor_price_id'] ?? 0) !== (int) ($currentOption['distributor_price_id'] ?? 0);
                })->values();

                return [
                    'product_id' => $item['product_id'],
                    'component_id' => $item['component_id'],
                    'product_name' => $item['name'] ?: ($product->product ?? 'Produkt'),
                    'default_article_no' => $item['article_no'] ?: ($product->article_no ?? '-'),
                    'qty' => (float) ($item['qty'] ?? 0),
                    'unit' => $item['unit'] ?: '-',
                    'options' => $mappedOptions,
                    'current_option' => $currentOption,
                    'best_option' => $bestOption,
                    'has_alternative_distributor' => $alternativeOptions->isNotEmpty(),
                    'comparison_message' => $alternativeOptions->isEmpty()
                        ? 'Für dieses Produkt haben wir keinen weiteren Lieferantenpreis zur Differenzierung.'
                        : null,
                ];
            })->values();

            $summaryByDistributor = [];

            foreach ($resultItems as $resultItem) {
                foreach (($resultItem['options'] ?? []) as $option) {
                    $key = (string) $option['distributor_id'];

                    if (!isset($summaryByDistributor[$key])) {
                        $summaryByDistributor[$key] = [
                            'distributor_id' => $option['distributor_id'],
                            'distributor_name' => $option['distributor_name'],
                            'items_count' => 0,
                            'total_effective' => 0,
                            'avg_payment_terms' => 0,
                            'avg_cash_discount' => 0,
                            'available_count' => 0,
                            'payment_terms_sum' => 0,
                            'cash_discount_sum' => 0,
                        ];
                    }

                    $summaryByDistributor[$key]['items_count']++;
                    $summaryByDistributor[$key]['total_effective'] += (float) $option['line_total'];
                    $summaryByDistributor[$key]['payment_terms_sum'] += (float) ($option['payment_terms'] ?? 0);
                    $summaryByDistributor[$key]['cash_discount_sum'] += (float) ($option['cash_discount'] ?? 0);

                    if (!empty($option['availability'])) {
                        $summaryByDistributor[$key]['available_count']++;
                    }
                }
            }

            $summary = collect($summaryByDistributor)
                ->map(function (array $row) {
                    $count = max(1, (int) $row['items_count']);

                    $row['total_effective'] = round((float) $row['total_effective'], 2);
                    $row['avg_payment_terms'] = round(((float) $row['payment_terms_sum']) / $count, 2);
                    $row['avg_cash_discount'] = round(((float) $row['cash_discount_sum']) / $count, 2);
                    $row['availability_ratio'] = round(((float) $row['available_count'] / $count) * 100, 2);

                    unset($row['payment_terms_sum'], $row['cash_discount_sum']);

                    return $row;
                })
                ->sortBy('total_effective')
                ->values();

            return $this->jsonSuccess([
                'success' => true,
                'items' => $resultItems,
                'summary' => $summary,
            ]);
        } catch (\RuntimeException $e) {
            return $this->jsonError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            report($e);

            return $this->jsonError(
                'Preisvergleich konnte nicht geladen werden: ' . $e->getMessage(),
                500
            );
        }
    }


    public function changeMaterialDistributor(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'distributor_id' => 'nullable|integer|exists:distributors,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|integer',
            'items.*.component_id' => 'nullable|integer',
            'items.*.current_distributor_price_id' => 'nullable|integer',
            'items.*.current_distributor_id' => 'nullable|integer',
            'items.*.target_distributor_id' => 'nullable|integer|exists:distributors,id',
            'items.*.article_no' => 'nullable|string',
            'items.*.name' => 'nullable|string',
            'items.*.qty' => 'nullable|numeric|min:0',
            'items.*.unit' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            $folder->load([
                'offer.customer',
                'offer.alternative',
                'offer.product',
                'offer.detail',
                'creator',
                'detail',
            ]);

            $workflowDetail = $this->ensureExecutionDocumentStatus($folder);

            $folderDetail = $folder->detail;
            $offerDetail = $folder->offer?->detail;

            if (!$folderDetail && !$offerDetail) {
                return $this->jsonError('Keine OfferDetail-Daten für diesen Ordner gefunden.', 404);
            }

            if ($folderDetail) {
                $this->ensureAngebotSnapshotExists($folderDetail);
            }
            if ($offerDetail && (!$folderDetail || (int) $offerDetail->id !== (int) $folderDetail->id)) {
                $this->ensureAngebotSnapshotExists($offerDetail);
            }

            $defaultDistributorId = $request->filled('distributor_id')
                ? (int) $request->integer('distributor_id')
                : null;

            $requestedItems = collect($request->input('items', []))
                ->map(function (array $item) use ($defaultDistributorId) {
                    $productId = !empty($item['product_id']) ? (int) $item['product_id'] : null;
                    $componentId = !empty($item['component_id']) ? (int) $item['component_id'] : null;

                    if (!$productId && $componentId) {
                        $component = MasterSetComponent::query()
                            ->select('id', 'product_id')
                            ->find($componentId);

                        if ($component && (int) $component->product_id > 0) {
                            $productId = (int) $component->product_id;
                        }
                    }

                    return [
                        'product_id' => $productId,
                        'component_id' => $componentId,
                        'current_distributor_price_id' => !empty($item['current_distributor_price_id']) ? (int) $item['current_distributor_price_id'] : null,
                        'current_distributor_id' => !empty($item['current_distributor_id']) ? (int) $item['current_distributor_id'] : null,
                        'target_distributor_id' => !empty($item['target_distributor_id'])
                            ? (int) $item['target_distributor_id']
                            : $defaultDistributorId,
                        'article_no' => trim((string) ($item['article_no'] ?? '')),
                        'name' => trim((string) ($item['name'] ?? '')),
                        'qty' => isset($item['qty']) ? (float) $item['qty'] : 0,
                        'unit' => $item['unit'] ?? null,
                    ];
                })
                ->filter(function (array $item) {
                    return (
                        !empty($item['product_id']) ||
                        !empty($item['component_id']) ||
                        $item['article_no'] !== ''
                    );
                })
                ->values();

            if ($requestedItems->isEmpty()) {
                return $this->jsonError('Keine gültigen Materialpositionen übergeben.', 422);
            }

            $resolvedItems = $requestedItems
                ->filter(function (array $item) {
                    return !empty($item['target_distributor_id']);
                })
                ->values();

            if ($resolvedItems->isEmpty()) {
                return $this->jsonError('Kein Ziel-Distributor konnte ermittelt werden.', 422);
            }

            $productIds = $resolvedItems
                ->pluck('product_id')
                ->filter()
                ->unique()
                ->values();

            $targetDistributorIds = $resolvedItems
                ->pluck('target_distributor_id')
                ->filter()
                ->unique()
                ->values();

            $targetPrices = DistributorPrice::query()
                ->with(['distributor:id,name,short_name,payment_terms,cash_discount'])
                ->whereIn('distributor_id', $targetDistributorIds)
                ->whereIn('product_id', $productIds)
                ->get()
                ->groupBy(function (DistributorPrice $price) {
                    return $price->product_id . ':' . $price->distributor_id;
                });

            DB::transaction(function () use ($folderDetail, $offerDetail, $resolvedItems, $targetPrices) {
                if ($folderDetail) {
                    $detailHistory = is_array($folderDetail->material_history) ? $folderDetail->material_history : [];

                    $updatedSections = $this->applyDistributorChangeToSections(
                        is_array($folderDetail->sections) ? $folderDetail->sections : [],
                        $resolvedItems,
                        $targetPrices,
                        $detailHistory
                    );

                    $this->persistDetailSections($folderDetail, $updatedSections, $detailHistory);
                }

                if ($offerDetail && (!$folderDetail || (int) $offerDetail->id !== (int) $folderDetail->id)) {
                    $detailHistory = is_array($offerDetail->material_history) ? $offerDetail->material_history : [];

                    $updatedSections = $this->applyDistributorChangeToSections(
                        is_array($offerDetail->sections) ? $offerDetail->sections : [],
                        $resolvedItems,
                        $targetPrices,
                        $detailHistory
                    );

                    $this->persistDetailSections($offerDetail, $updatedSections, $detailHistory);
                }
            });

            $folder->refresh();
            $folder->load([
                'offer.customer',
                'offer.alternative',
                'offer.product',
                'offer.detail',
                'creator',
                'detail',
                'attachments',
                'pdfPrints',
            ]);

            $detail = $this->resolveDisplayDetail($folder);

            return $this->jsonSuccess([
                'success' => true,
                'message' => 'Lieferant wurde erfolgreich aktualisiert.',
                'detail' => $detail,
                'document_status' => $workflowDetail->document_status,
                'folder_detail_id' => $folder->detail?->id,
                'offer_detail_id' => $folder->offer?->detail?->id,
            ]);
        } catch (\RuntimeException $e) {
            return $this->jsonError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            report($e);

            return $this->jsonError(
                'Lieferant konnte nicht übernommen werden: ' . $e->getMessage(),
                500
            );
        }
    }

    protected function appendStatusHistory(OfferFolder $folder, string $fromStatus, string $toStatus, ?string $reason = null): void
    {
        $history = is_array($folder->history) ? $folder->history : [];

        $user = auth()->user();
        $employeeId = null;
        $employeeName = null;

        if (method_exists($this, 'employeeId')) {
            $employeeId = $this->employeeId();
        }

        if ($employeeId) {
            $employee = \App\Models\Employee::find($employeeId);
            $employeeName = $employee
                ? trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))
                : null;
        }

        $history[] = [
            'type' => 'status_changed',
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'reason' => $reason,
            'changed_by' => [
                'user_id' => $user?->id,
                'employee_id' => $employeeId,
                'name' => $employeeName ?: ($user->name ?? null),
                'email' => $user->email ?? null,
            ],
            'changed_at' => now()->toDateTimeString(),
        ];

        $folder->history = $history;
    }


    protected function employeeId(): ?int
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if (!empty($user->name)) {
            return (int) $user->name;
        }

        if (!empty($user->name)) {
            return (int) $user->name;
        }

        return null;
    }

    protected function resolveHistoryDetail(OfferFolder $folder): ?OfferDetail
    {
        $folder->loadMissing(['detail', 'offer.detail']);

        if ($folder->detail) {
            return $folder->detail;
        }

        return $folder->offer?->detail;
    }

    protected function employeeNameById(?int $employeeId): ?string
    {
        if (!$employeeId || $employeeId <= 0) {
            return null;
        }

        $employee = Employee::query()
            ->select('id', 'name', 'lastname')
            ->find($employeeId);

        if (!$employee) {
            return null;
        }

        $fullName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

        return $fullName !== '' ? $fullName : null;
    }

    public function moveKanbanItem(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $folder->loadMissing(['detail', 'offer.detail']);

        $documentStatus = $this->resolveFolderDocumentStatus($folder);
        $mainStage = $this->findWorkflowMainStage($documentStatus);

        if (!$mainStage) {
            return $this->jsonError(
                $documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL
                ? 'Die Hauptphase für Auftrag/Deal konnte nicht automatisch erkannt werden.'
                : 'Die Hauptphase für Angebot/Offer konnte nicht automatisch erkannt werden.',
                422,
                $this->workflowStageMappingPayload($documentStatus)
            );
        }

        $allowedStatuses = $this->resolveAllowedWorkflowStatuses($documentStatus);

        if (empty($allowedStatuses)) {
            return $this->jsonError(
                $documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL
                ? 'Für die Auftrag-Hauptphase wurden keine aktiven Unterphasen gefunden.'
                : 'Für die Angebot-Hauptphase wurden keine aktiven Unterphasen gefunden.',
                422,
                array_merge($this->workflowStageMappingPayload($documentStatus), [
                    'needs_stage_mapping' => false,
                    'main_stage' => [
                        'id' => $mainStage->id,
                        'key' => $mainStage->key,
                        'name' => $mainStage->name,
                    ],
                    'message' => 'Bitte legen Sie zuerst aktive Unterphasen für diese Hauptphase an.',
                ])
            );
        }

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string', \Illuminate\Validation\Rule::in($allowedStatuses)],
            'reason' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422, [
                'allowed_statuses' => $allowedStatuses,
                'kanban_stages' => $this->getKanbanStagesForDocument($documentStatus)->values(),
                'kanban_stage_context' => $this->getKanbanStageContextForDocument($documentStatus),
            ]);
        }

        try {
            DB::transaction(function () use ($folder, $request, $documentStatus) {
                $oldStatus = $this->getFolderWorkflowStatus($folder);
                $newStatus = LeadStage::makeKey((string) $request->string('status')->toString());
                $reason = trim((string) $request->input('reason', ''));
                $newSubStage = $this->workflowSubStageByKey($documentStatus, $newStatus);

                if (!$newSubStage) {
                    throw new \RuntimeException('Die gewählte Unterphase wurde nicht gefunden.');
                }

                $employeeId = $this->employeeId();
                $employeeName = $this->employeeNameById($employeeId) ?: 'Unbekannt';

                $detail = $this->resolveHistoryDetail($folder);
                $history = is_array($folder->history) ? $folder->history : [];

                $history[] = [
                    'type' => 'workflow_sub_stage_changed',
                    'title' => $documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL
                        ? 'Auftrag-Unterphase geändert'
                        : 'Angebot-Unterphase geändert',
                    'document_status' => $documentStatus,
                    'source' => 'lead_stage_sub_stages',
                    'lead_stage_id' => $newSubStage->lead_stage_id,
                    'lead_stage_sub_stage_id' => $newSubStage->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'old_status_label' => $this->getWorkflowStatusLabelByContext($documentStatus, $oldStatus),
                    'new_status_label' => $newSubStage->name,
                    'reason' => $reason !== '' ? $reason : null,
                    'changed_by' => $employeeId,
                    'changed_by_name' => $employeeName,
                    'net_total' => (float) ($detail?->total_net ?? 0),
                    'gross_total' => (float) ($detail?->total_gross ?? 0),
                    'tax_rate' => (float) ($detail?->tax_rate ?? 19),
                    'company_name' => $detail?->company_name,
                    'created_at' => now()->toDateTimeString(),
                ];

                $this->setFolderWorkflowStatus($folder, $newStatus);

                /**
                 * Backward compatibility for old places that still read offer_folders.status.
                 */
                $folder->status = $newStatus;
                $folder->document_status = $documentStatus;
                $folder->history = $history;
                $folder->save();

                $this->syncFolderWorkflowToLeadProductList($folder, $documentStatus, $newSubStage);
            });

            $folder->refresh();

            return $this->jsonSuccess([
                'success' => true,
                'message' => $documentStatus === OfferFolder::DOCUMENT_STATUS_DEAL
                    ? 'Auftrag-Unterphase wurde aktualisiert.'
                    : 'Angebot-Unterphase wurde aktualisiert.',
                'folder' => $folder,
                'document_status' => $documentStatus,
                'workflow_status' => $this->getFolderWorkflowStatus($folder),
                'workflow_status_label' => $this->getWorkflowStatusLabelByContext($documentStatus, $this->getFolderWorkflowStatus($folder)),
                'kanban_stages' => $this->getKanbanStagesForDocument($documentStatus)->values(),
                'kanban_stage_context' => $this->getKanbanStageContextForDocument($documentStatus),
            ]);
        } catch (Throwable $e) {
            report($e);

            return $this->jsonError('Status konnte nicht aktualisiert werden: ' . $e->getMessage(), 500);
        }
    }
    protected function collectDistributorIdsFromSections(array $sections): array
    {
        $distributorIds = [];

        foreach ($sections as $section) {
            foreach (($section['items'] ?? []) as $item) {
                $itemDistributorId = (int) ($item['distributor_id'] ?? 0);
                if ($itemDistributorId > 0) {
                    $distributorIds[] = $itemDistributorId;
                }

                foreach (($item['subItems'] ?? []) as $subItem) {
                    $subDistributorId = (int) ($subItem['distributor_id'] ?? 0);
                    if ($subDistributorId > 0) {
                        $distributorIds[] = $subDistributorId;
                    }
                }
            }
        }

        return array_values(array_unique($distributorIds));
    }

    /*
    |--------------------------------------------------------------------------
    | Offer index totals from active folders only
    |--------------------------------------------------------------------------
    | Proof marker: OFFER_FOLDER_CONTROLLER_INDEX_TOTAL_SYNC_V1
    */
    protected function syncOfferIndexTotalsFromActiveFolders(int $offerId): void
    {
        try {
            $activeFolders = OfferFolder::query()
                ->where('offer_id', $offerId)
                ->with(['detail'])
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->get();

            $sourceFolder = $activeFolders->first(fn($folder) => $folder->detail !== null);
            $sourceDetail = $sourceFolder?->detail;

            $taxRate = (float) ($sourceDetail?->tax_rate ?? 19);
            $totalNet = $sourceDetail ? round((float) ($sourceDetail->total_net ?? 0), 2) : 0.0;
            $totalGross = $sourceDetail ? round((float) ($sourceDetail->total_gross ?? 0), 2) : 0.0;
            $documentStatus = $sourceDetail?->document_status ?: OfferDetail::DOCUMENT_STATUS_OFFER;
            $sections = $sourceDetail && is_array($sourceDetail->sections) ? $sourceDetail->sections : [];

            if ($activeFolders->isEmpty()) {
                $totalNet = 0.0;
                $totalGross = 0.0;
                $sections = [];
                $documentStatus = OfferDetail::DOCUMENT_STATUS_OFFER;
            }

            $summary = OfferDetail::query()->firstOrNew([
                'offer_id' => $offerId,
                'offer_folder_id' => null,
            ]);

            $summary->document_status = $documentStatus;
            $summary->sections = $sections;
            $summary->tax_rate = $taxRate;
            $summary->total_net = $totalNet;
            $summary->total_gross = $totalGross;
            $summary->save();
        } catch (Throwable $e) {
            report($e);
        }
    }

    protected function persistDetailSections(OfferDetail $detail, array $sections, ?array $materialHistory = null): void
    {
        $totals = $this->calculateDetailTotals($sections, (float) ($detail->tax_rate ?? 19));

        $documentStatus = (string) ($detail->document_status ?: OfferDetail::DOCUMENT_STATUS_OFFER);

        if (
            $documentStatus !== OfferDetail::DOCUMENT_STATUS_OFFER
            && (!is_array($detail->angebot_snapshot_sections) || count($detail->angebot_snapshot_sections) === 0)
        ) {
            $detail->angebot_snapshot_sections = is_array($detail->sections) ? $detail->sections : [];
            $detail->angebot_snapshot_at = now();
        }

        $detail->sections = $sections;
        $detail->total_net = $totals['total_net'];
        $detail->total_gross = $totals['total_gross'];

        if ($materialHistory !== null) {
            $detail->material_history = array_values($materialHistory);
        }

        $detail->save();

        if ((int) ($detail->offer_id ?? 0) > 0) {
            $this->syncOfferIndexTotalsFromActiveFolders((int) $detail->offer_id);
        }
    }

    protected function calculateDetailTotals(array $sections, float $taxRate = 19): array
    {
        $sumNodes = function (array $nodes) use (&$sumNodes): float {
            $total = 0.0;

            foreach ($nodes as $node) {
                if (($node['kind'] ?? null) !== 'labor') {
                    $total += (float) ($node['total'] ?? 0);
                }

                if (isset($node['subItems']) && is_array($node['subItems'])) {
                    $total += $sumNodes($node['subItems']);
                }
            }

            return $total;
        };

        $totalNet = 0.0;

        foreach ($sections as $section) {
            $totalNet += $sumNodes(is_array($section['items'] ?? null) ? $section['items'] : []);
        }

        $totalNet = round($totalNet, 2);
        $totalGross = round($totalNet + ($totalNet * $taxRate / 100), 2);

        return [
            'total_net' => $totalNet,
            'total_gross' => $totalGross,
        ];
    }

    protected function applyDistributorChangeToSections(
        array $sections,
        Collection $requestedItems,
        Collection $targetPrices,
        array &$detailHistory = []
    ): array {
        $applyRecursively = function (array $nodes) use (&$applyRecursively, $requestedItems, $targetPrices, &$detailHistory): array {
            foreach ($nodes as &$node) {
                $node = $this->updateNodeDistributorData($node, $requestedItems, $targetPrices, $detailHistory);

                if (isset($node['subItems']) && is_array($node['subItems'])) {
                    $node['subItems'] = $applyRecursively($node['subItems']);
                }
            }

            unset($node);

            return $nodes;
        };

        foreach ($sections as &$section) {
            $section['items'] = $applyRecursively(
                is_array($section['items'] ?? null) ? $section['items'] : []
            );
        }

        unset($section);

        return $sections;
    }

    protected function updateNodeDistributorData(
        array $node,
        Collection $requestedItems,
        Collection $targetPrices,
        array &$detailHistory = []
    ): array {
        $node = $this->hydrateNodeFromOwnFields($node);

        $productId = (int) ($node['productId'] ?? $node['product_id'] ?? 0);
        $componentId = (int) ($node['component_id'] ?? 0);

        if ($productId <= 0 && $componentId > 0) {
            $component = MasterSetComponent::query()
                ->select('id', 'product_id')
                ->find($componentId);

            if ($component && (int) $component->product_id > 0) {
                $productId = (int) $component->product_id;
                $node['product_id'] = $productId;
                $node['productId'] = $productId;
            }
        }

        if ($productId <= 0) {
            return $node;
        }

        $requestItem = $this->findMatchingRequestedItem($node, $requestedItems);

        if (!$requestItem) {
            return $node;
        }

        $targetDistributorId = (int) ($requestItem['target_distributor_id'] ?? 0);

        if ($targetDistributorId <= 0) {
            return $node;
        }

        $pricesForTarget = $targetPrices->get($productId . ':' . $targetDistributorId, collect());
        $price = $this->findBestDistributorPriceForNode($node, $pricesForTarget);

        if (!$price) {
            return $node;
        }

        $oldDistributorId = $node['distributor_id'] ?? null;
        $oldDistributorName = $node['distributor_name'] ?? $node['supplier'] ?? null;
        $oldDistributorArticleNo = $node['distributor_article_no'] ?? null;
        $oldUnitPrice = (float) ($node['price'] ?? $node['unit_price'] ?? 0);        // old VK
        $oldPurchasePrice = (float) ($node['purchase_price'] ?? $node['ek'] ?? 0);   // old EK
        $oldTotal = (float) ($node['total'] ?? 0);

        $qty = isset($node['qty']) ? (float) $node['qty'] : (float) ($requestItem['qty'] ?? 0);

        // VK / sales price
        $unitPrice = (float) ($price->price ?? 0);

        // EK / purchase price -> MUST drive cost comparison and total
        $purchasePrice = (float) ($price->purchase_price ?? $price->price ?? 0);

        $distributorName = trim(
            $price->distributor->short_name
            ?: $price->distributor->name
            ?: ('Lieferant #' . $price->distributor_id)
        );

        $paymentTerms = (int) ($price->distributor->payment_terms ?? ($node['payment_terms'] ?? 14));
        $cashDiscount = (float) ($price->distributor->cash_discount ?? ($node['skonto'] ?? 0));

        $node['supplier'] = $distributorName;
        $node['distributor_name'] = $distributorName;
        $node['distributor_id'] = (int) $price->distributor_id;
        $node['distributor_price_id'] = (int) $price->id;
        $node['distributor_article_no'] = $price->article_no ?: ($node['distributor_article_no'] ?? null);

        if (empty($node['article_no']) && !empty($node['product_id'])) {
            $product = Product::query()->select('id', 'article_no')->find((int) $node['product_id']);
            if ($product && !empty($product->article_no)) {
                $node['article_no'] = $product->article_no;
            }
        }

        // keep both VK and EK values on node
        $node['price'] = $unitPrice;                 // VK / unit
        $node['purchase_price'] = $purchasePrice;    // EK / unit
        $node['ek'] = $purchasePrice;
        $node['rate'] = $unitPrice;

        // IMPORTANT: total must be EK-based for supplier cost comparison
        $node['total'] = round($qty * $purchasePrice, 2);

        $node['qty'] = $qty;
        $node['payment_terms'] = $paymentTerms;
        $node['skonto'] = $cashDiscount;
        $node['availability'] = $price->availability;

        $changed =
            (int) ($oldDistributorId ?? 0) !== (int) $price->distributor_id
            || (string) ($oldDistributorArticleNo ?? '') !== (string) ($node['distributor_article_no'] ?? '')
            || round($oldUnitPrice, 4) !== round($unitPrice, 4)
            || round($oldPurchasePrice, 4) !== round($purchasePrice, 4)
            || round($oldTotal, 4) !== round((float) $node['total'], 4);

        if ($changed) {
            $historyEntry = [
                'type' => 'distributor_changed',
                'from' => $oldDistributorName ?: ('Lieferant #' . ($oldDistributorId ?: '?')),
                'to' => $distributorName,
                'qty' => $qty,
                'reason' => 'Distributor / Einkaufspreis geändert',
                'changed_by' => $this->currentHistoryActor(),
                'meta' => [
                    'old_distributor_id' => $oldDistributorId,
                    'new_distributor_id' => (int) $price->distributor_id,
                    'old_article_no' => $oldDistributorArticleNo,
                    'new_article_no' => $node['distributor_article_no'] ?? null,
                    'old_unit_price' => $oldUnitPrice,           // old VK
                    'new_unit_price' => $unitPrice,              // new VK
                    'old_purchase_price' => $oldPurchasePrice,   // old EK
                    'new_purchase_price' => $purchasePrice,      // new EK
                    'old_total' => $oldTotal,
                    'new_total' => (float) $node['total'],
                ],
                'created_at' => now()->toDateTimeString(),
            ];

            $this->appendMaterialNodeHistory($node, $historyEntry);

            $detailHistory[] = array_merge($historyEntry, [
                'node' => [
                    'product_id' => $node['product_id'] ?? $node['productId'] ?? null,
                    'component_id' => $node['component_id'] ?? null,
                    'article_no' => $node['article_no'] ?? null,
                    'distributor_article_no' => $node['distributor_article_no'] ?? null,
                    'name' => $node['name'] ?? $node['title'] ?? null,
                ]
            ]);
        }

        return $node;
    }

    protected function findMatchingRequestedItem(array $node, Collection $requestedItems): ?array
    {
        $productId = (int) ($node['productId'] ?? $node['product_id'] ?? 0);
        $componentId = (int) ($node['component_id'] ?? 0);

        $nodeArticle = trim((string) ($node['distributor_article_no'] ?? $node['article_no'] ?? ''));
        $nodeName = trim((string) ($node['name'] ?? $node['title'] ?? ''));

        return $requestedItems->first(function (array $item) use ($productId, $componentId, $nodeArticle, $nodeName) {
            $reqProductId = (int) ($item['product_id'] ?? 0);
            $reqComponentId = (int) ($item['component_id'] ?? 0);
            $reqArticle = trim((string) ($item['article_no'] ?? ''));
            $reqName = trim((string) ($item['name'] ?? ''));

            if ($reqComponentId > 0 && $componentId > 0) {
                return $reqComponentId === $componentId;
            }

            if ($reqProductId > 0 && $productId > 0 && $reqArticle !== '' && $nodeArticle !== '') {
                return $reqProductId === $productId && $reqArticle === $nodeArticle;
            }

            if ($reqProductId > 0 && $productId > 0 && $reqName !== '' && $nodeName !== '') {
                return $reqProductId === $productId && $reqName === $nodeName;
            }

            if ($reqProductId > 0 && $productId > 0) {
                return $reqProductId === $productId;
            }

            if ($reqArticle !== '' && $nodeArticle !== '') {
                return $reqArticle === $nodeArticle;
            }

            return false;
        });
    }

    protected function findBestDistributorPriceForNode(array $node, Collection $prices): ?DistributorPrice
    {
        if ($prices->isEmpty()) {
            return null;
        }

        $nodeArticleNo = trim((string) ($node['distributor_article_no'] ?? $node['article_no'] ?? ''));

        if ($nodeArticleNo !== '') {
            $exactArticleMatch = $prices->first(function (DistributorPrice $price) use ($nodeArticleNo) {
                return trim((string) ($price->article_no ?? '')) === $nodeArticleNo;
            });

            if ($exactArticleMatch) {
                return $exactArticleMatch;
            }
        }

        return $prices
            ->sortBy(function (DistributorPrice $price) {
                return (float) ($price->purchase_price ?? $price->price ?? PHP_FLOAT_MAX);
            })
            ->first();
    }


    protected function appendDetailMaterialHistory(OfferDetail $detail, array $entry, array $node = []): void
    {
        $history = is_array($detail->material_history) ? $detail->material_history : [];

        $history[] = [
            'type' => $entry['type'] ?? 'material_changed',
            'from' => $entry['from'] ?? null,
            'to' => $entry['to'] ?? null,
            'qty' => isset($entry['qty']) ? (float) $entry['qty'] : null,
            'reason' => $entry['reason'] ?? null,
            'changed_by' => $entry['changed_by'] ?? $this->currentHistoryActor(),
            'created_at' => $entry['created_at'] ?? now()->toDateTimeString(),
            'node' => [
                'product_id' => $node['product_id'] ?? $node['productId'] ?? null,
                'component_id' => $node['component_id'] ?? null,
                'article_no' => $node['article_no'] ?? null,
                'distributor_article_no' => $node['distributor_article_no'] ?? null,
                'name' => $node['name'] ?? $node['title'] ?? null,
                'position_no' => $node['position_no'] ?? null,
                'distributor_id' => $node['distributor_id'] ?? null,
                'distributor_name' => $node['distributor_name'] ?? $node['supplier'] ?? null,
            ],
            'meta' => $entry['meta'] ?? [],
        ];

        $detail->material_history = array_values($history);
    }

    protected function appendMaterialNodeHistory(array &$node, array $entry): void
    {
        $history = is_array($node['material_history'] ?? null) ? $node['material_history'] : [];
        $history[] = $entry;
        $node['material_history'] = array_values($history);
    }

    protected function currentHistoryActor(): array
    {
        $user = auth()->user();
        $employeeId = $this->employeeId();
        $employeeName = $this->employeeNameById($employeeId);

        return [
            'user_id' => $user?->id,
            'employee_id' => $employeeId,
            'name' => $employeeName ?: ($user->name ?? 'Unbekannt'),
            'email' => $user->email ?? null,
        ];
    }

    public function confirmMaterialFinalStatus(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|integer',
            'items.*.component_id' => 'nullable|integer',
            'items.*.article_no' => 'nullable|string',
            'items.*.source_status' => 'required|string|in:lager,bestellen',
            'items.*.final_qty' => 'required|numeric|min:0.0001',
            'items.*.remaining_to' => 'required|string|in:offen,lager,bestellen',
            'items.*.reason' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            $folder->load([
                'offer.customer',
                'offer.alternative',
                'offer.product',
                'offer.detail',
                'creator',
                'detail',
            ]);

            $workflowDetail = $this->ensureExecutionDocumentStatus($folder);

            $folderDetail = $folder->detail;
            $offerDetail = $folder->offer?->detail;

            if (!$folderDetail && !$offerDetail) {
                return $this->jsonError('Keine OfferDetail-Daten für diesen Ordner gefunden.', 404);
            }

            if ($folderDetail) {
                $this->ensureAngebotSnapshotExists($folderDetail);
            }
            if ($offerDetail && (!$folderDetail || (int) $offerDetail->id !== (int) $folderDetail->id)) {
                $this->ensureAngebotSnapshotExists($offerDetail);
            }

            $requestedItems = collect($request->input('items', []))
                ->map(function (array $item) {
                    $productId = !empty($item['product_id']) ? (int) $item['product_id'] : null;
                    $componentId = !empty($item['component_id']) ? (int) $item['component_id'] : null;

                    if (!$productId && $componentId) {
                        $component = MasterSetComponent::query()
                            ->select('id', 'product_id')
                            ->find($componentId);

                        if ($component && (int) $component->product_id > 0) {
                            $productId = (int) $component->product_id;
                        }
                    }

                    return [
                        'product_id' => $productId,
                        'component_id' => $componentId,
                        'article_no' => trim((string) ($item['article_no'] ?? '')),
                        'source_status' => (string) ($item['source_status'] ?? ''),
                        'final_qty' => (float) ($item['final_qty'] ?? 0),
                        'remaining_to' => (string) ($item['remaining_to'] ?? ''),
                        'reason' => trim((string) ($item['reason'] ?? '')),
                    ];
                })
                ->filter(function (array $item) {
                    return (
                        !empty($item['product_id']) ||
                        !empty($item['component_id']) ||
                        $item['article_no'] !== ''
                    ) && in_array($item['source_status'], ['lager', 'bestellen'], true)
                        && in_array($item['remaining_to'], ['offen', 'lager', 'bestellen'], true)
                        && $item['final_qty'] > 0;
                })
                ->values();

            if ($requestedItems->isEmpty()) {
                return $this->jsonError('Keine gültigen Final-Positionen übergeben.', 422);
            }

            DB::transaction(function () use ($folderDetail, $offerDetail, $requestedItems) {
                if ($folderDetail) {
                    $updatedSections = $this->applyFinalConfirmationToSections(
                        is_array($folderDetail->sections) ? $folderDetail->sections : [],
                        $requestedItems
                    );

                    $this->persistDetailSections($folderDetail, $updatedSections);
                }

                if ($offerDetail && (!$folderDetail || (int) $offerDetail->id !== (int) $folderDetail->id)) {
                    $updatedSections = $this->applyFinalConfirmationToSections(
                        is_array($offerDetail->sections) ? $offerDetail->sections : [],
                        $requestedItems
                    );

                    $this->persistDetailSections($offerDetail, $updatedSections);
                }
            });

            $folder->refresh();
            $folder->load([
                'offer.customer',
                'offer.alternative',
                'offer.product',
                'offer.detail',
                'creator',
                'detail',
                'attachments',
                'pdfPrints',
            ]);

            $detail = $this->resolveDisplayDetail($folder);

            return $this->jsonSuccess([
                'success' => true,
                'message' => 'Kommissionen Materialliste wurde aktualisiert.',
                'detail' => $detail,
                'document_status' => $workflowDetail->document_status,
                'folder_detail_id' => $folder->detail?->id,
                'offer_detail_id' => $folder->offer?->detail?->id,
            ]);
        } catch (\RuntimeException $e) {
            return $this->jsonError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            report($e);

            return $this->jsonError(
                'Kommissionen Materialliste konnte nicht aktualisiert werden: ' . $e->getMessage(),
                500
            );
        }
    }

    public function changeMaterialOrderStatus(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|integer',
            'items.*.component_id' => 'nullable|integer',
            'items.*.article_no' => 'nullable|string',
            'items.*.move_to' => 'required|string|in:offen,lager,bestellen,final',
            'items.*.move_qty' => 'required|numeric|min:0.0001',
            'items.*.source_status' => 'nullable|string|in:offen,lager,bestellen,final',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            $folder->load([
                'offer.customer',
                'offer.alternative',
                'offer.product',
                'offer.detail',
                'creator',
                'detail',
            ]);

            $workflowDetail = $this->ensureExecutionDocumentStatus($folder);

            $folderDetail = $folder->detail;
            $offerDetail = $folder->offer?->detail;

            if (!$folderDetail && !$offerDetail) {
                return $this->jsonError('Keine OfferDetail-Daten für diesen Ordner gefunden.', 404);
            }

            if ($folderDetail) {
                $this->ensureAngebotSnapshotExists($folderDetail);
            }
            if ($offerDetail && (!$folderDetail || (int) $offerDetail->id !== (int) $folderDetail->id)) {
                $this->ensureAngebotSnapshotExists($offerDetail);
            }

            $requestedItems = collect($request->input('items', []))
                ->map(function (array $item) {
                    $productId = !empty($item['product_id']) ? (int) $item['product_id'] : null;
                    $componentId = !empty($item['component_id']) ? (int) $item['component_id'] : null;

                    if (!$productId && $componentId) {
                        $component = MasterSetComponent::query()
                            ->select('id', 'product_id')
                            ->find($componentId);

                        if ($component && (int) $component->product_id > 0) {
                            $productId = (int) $component->product_id;
                        }
                    }

                    $moveTo = (string) ($item['move_to'] ?? '');
                    $sourceStatus = !empty($item['source_status'])
                        ? (string) $item['source_status']
                        : null;

                    if (!in_array($sourceStatus, ['offen', 'lager', 'bestellen', 'final'], true)) {
                        $sourceStatus = 'offen';
                    }

                    return [
                        'product_id' => $productId,
                        'component_id' => $componentId,
                        'article_no' => trim((string) ($item['article_no'] ?? '')),
                        'move_to' => $moveTo,
                        'move_qty' => (float) ($item['move_qty'] ?? 0),
                        'source_status' => $sourceStatus,
                    ];
                })
                ->filter(function (array $item) {
                    return (
                        !empty($item['product_id']) ||
                        !empty($item['component_id']) ||
                        $item['article_no'] !== ''
                    ) && in_array($item['move_to'], ['offen', 'lager', 'bestellen', 'final'], true)
                        && $item['move_qty'] > 0;
                })
                ->values();

            if ($requestedItems->isEmpty()) {
                return $this->jsonError('Keine gültigen Materialpositionen übergeben.', 422);
            }

            DB::transaction(function () use ($folderDetail, $offerDetail, $requestedItems) {
                if ($folderDetail) {
                    $detailHistory = is_array($folderDetail->material_history) ? $folderDetail->material_history : [];

                    $updatedSections = $this->applyStockAllocationToSections(
                        is_array($folderDetail->sections) ? $folderDetail->sections : [],
                        $requestedItems,
                        $detailHistory
                    );

                    $this->persistDetailSections($folderDetail, $updatedSections, $detailHistory);
                }

                if ($offerDetail && (!$folderDetail || (int) $offerDetail->id !== (int) $folderDetail->id)) {
                    $detailHistory = is_array($offerDetail->material_history) ? $offerDetail->material_history : [];

                    $updatedSections = $this->applyStockAllocationToSections(
                        is_array($offerDetail->sections) ? $offerDetail->sections : [],
                        $requestedItems,
                        $detailHistory
                    );

                    $this->persistDetailSections($offerDetail, $updatedSections, $detailHistory);
                }
            });

            $folder->refresh();
            $folder->load([
                'offer.customer',
                'offer.alternative',
                'offer.product',
                'offer.detail',
                'creator',
                'detail',
                'attachments',
                'pdfPrints',
            ]);

            $detail = $this->resolveDisplayDetail($folder);

            return $this->jsonSuccess([
                'success' => true,
                'message' => 'Materialverteilung wurde aktualisiert.',
                'detail' => $detail,
                'document_status' => $workflowDetail->document_status,
                'folder_detail_id' => $folder->detail?->id,
                'offer_detail_id' => $folder->offer?->detail?->id,
            ]);
        } catch (\RuntimeException $e) {
            return $this->jsonError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            report($e);

            return $this->jsonError(
                'Materialverteilung konnte nicht aktualisiert werden: ' . $e->getMessage(),
                500
            );
        }
    }

    protected function applyFinalConfirmationToSections(array $sections, Collection $requestedItems): array
    {
        $actor = $this->currentHistoryActor();

        $applyRecursive = function (array $nodes, float $multiplier = 1.0) use (&$applyRecursive, $requestedItems, $actor): array {
            foreach ($nodes as &$node) {
                $node = $this->hydrateNodeFromOwnFields($node);

                $nodeQty = (float) ($node['qty'] ?? 0);
                $effectiveQty = round($nodeQty * $multiplier, 4);

                $match = $this->findMatchingRequestedItem($node, $requestedItems);

                if ($match) {
                    $allocation = is_array($node['stock_allocation'] ?? null)
                        ? $node['stock_allocation']
                        : [
                            'offen' => 0,
                            'lager' => 0,
                            'bestellen' => 0,
                            'final' => 0,
                        ];

                    $allocation = [
                        'offen' => (float) ($allocation['offen'] ?? 0),
                        'lager' => (float) ($allocation['lager'] ?? 0),
                        'bestellen' => (float) ($allocation['bestellen'] ?? 0),
                        'final' => (float) ($allocation['final'] ?? 0),
                    ];

                    $sourceStatus = (string) ($match['source_status'] ?? '');
                    $finalQty = (float) ($match['final_qty'] ?? 0);
                    $remainingTo = (string) ($match['remaining_to'] ?? 'offen');
                    $reason = trim((string) ($match['reason'] ?? ''));

                    if (in_array($sourceStatus, ['lager', 'bestellen'], true) && $finalQty > 0) {
                        $availableSource = (float) ($allocation[$sourceStatus] ?? 0);
                        $actualFinalQty = min($finalQty, $availableSource);

                        if ($actualFinalQty > 0) {
                            $allocation[$sourceStatus] -= $actualFinalQty;
                            $allocation['final'] += $actualFinalQty;

                            $this->appendMaterialNodeHistory($node, [
                                'type' => 'final_confirmed',
                                'from' => $sourceStatus,
                                'to' => 'final',
                                'qty' => $actualFinalQty,
                                'reason' => $reason !== '' ? $reason : 'Physisch bestätigt',
                                'changed_by' => $actor,
                                'created_at' => now()->toDateTimeString(),
                            ]);
                        }

                        $remainingSource = (float) ($allocation[$sourceStatus] ?? 0);

                        if ($remainingSource > 0 && $remainingTo !== $sourceStatus) {
                            $allocation[$sourceStatus] -= $remainingSource;
                            $allocation[$remainingTo] += $remainingSource;

                            $this->appendMaterialNodeHistory($node, [
                                'type' => 'allocation_move',
                                'from' => $sourceStatus,
                                'to' => $remainingTo,
                                'qty' => $remainingSource,
                                'reason' => $reason !== '' ? $reason : 'Restmenge umgebucht',
                                'changed_by' => $actor,
                                'created_at' => now()->toDateTimeString(),
                            ]);
                        }
                    }

                    $node['stock_allocation'] = $this->normalizeStockAllocation($allocation, $effectiveQty);

                    if ($node['stock_allocation']['final'] >= $effectiveQty) {
                        $node['order_status'] = 'final';
                    } elseif ($node['stock_allocation']['lager'] > 0 || $node['stock_allocation']['bestellen'] > 0 || $node['stock_allocation']['offen'] > 0) {
                        $node['order_status'] = 'partial';
                    }
                }

                if (isset($node['subItems']) && is_array($node['subItems'])) {
                    $childMultiplier = $multiplier * max($nodeQty, 1);
                    $node['subItems'] = $applyRecursive($node['subItems'], $childMultiplier);
                }
            }

            unset($node);

            return $nodes;
        };

        foreach ($sections as &$section) {
            $sectionQty = (float) data_get($section, 'config.qty', 1);
            $sectionUnit = strtolower((string) data_get($section, 'config.unit', ''));
            $sectionMultiplier = $sectionUnit === 'set' ? max($sectionQty, 1) : 1.0;

            $section['items'] = $applyRecursive(
                is_array($section['items'] ?? null) ? $section['items'] : [],
                $sectionMultiplier
            );
        }

        unset($section);

        return $sections;
    }

    protected function ensureExecutionDocumentStatus(OfferFolder $folder): OfferDetail
    {
        $detail = $this->resolveEditableDetailForWorkflow($folder);

        $documentStatus = strtolower((string) ($detail->document_status ?: OfferDetail::DOCUMENT_STATUS_OFFER));

        if (
            !in_array($documentStatus, [
                OfferDetail::DOCUMENT_STATUS_OFFER,
                OfferDetail::DOCUMENT_STATUS_DEAL,
            ], true)
        ) {
            throw new \RuntimeException(
                'Materialänderungen sind nur im Dokumentstatus "Angebot" oder "Auftrag" erlaubt.'
            );
        }

        return $detail;
    }

    protected function resolveEditableDetailForWorkflow(OfferFolder $folder): OfferDetail
    {
        $folder->loadMissing(['detail', 'offer.detail']);

        if ($folder->detail) {
            return $folder->detail;
        }

        if ($folder->offer?->detail) {
            return $folder->offer->detail;
        }

        $offerId = (int) ($folder->offer_id ?: $folder->offer?->id);

        if ($offerId <= 0) {
            throw new \RuntimeException('Für diesen Ordner konnte keine gültige Angebot-ID ermittelt werden.');
        }

        return OfferDetail::create([
            'offer_id' => $offerId,
            'offer_folder_id' => $folder->id,
            'document_status' => OfferDetail::DOCUMENT_STATUS_OFFER,
            'angebot_snapshot_sections' => [],
            'angebot_snapshot_at' => null,
            'tax_rate' => 19,
            'total_net' => 0,
            'total_gross' => 0,
            'sections' => [],
            'placed_images' => [],
            'print_attachments' => [],
            'biography_data' => [],
            'material_history' => [],
        ]);
    }


    protected function ensureAngebotSnapshotExists(OfferDetail $detail): void
    {
        $documentStatus = (string) ($detail->document_status ?: OfferDetail::DOCUMENT_STATUS_OFFER);

        if ($documentStatus === OfferDetail::DOCUMENT_STATUS_OFFER) {
            return;
        }

        $hasSnapshot = is_array($detail->angebot_snapshot_sections) && count($detail->angebot_snapshot_sections) > 0;

        if (!$hasSnapshot) {
            $detail->angebot_snapshot_sections = is_array($detail->sections) ? $detail->sections : [];
            $detail->angebot_snapshot_at = now();
            $detail->save();
        }
    }

    public function changeDocumentStatus(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'document_status' => 'required|string|in:offer,deal',
            'reason' => 'required|string|max:5000',
            'revert_to_offer' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            $folder->loadMissing(['detail', 'offer.detail', 'offer']);

            $detail = $this->resolveEditableDetailForWorkflow($folder);
            $oldStatus = (string) ($detail->document_status ?: OfferDetail::DOCUMENT_STATUS_OFFER);
            $newStatus = (string) $request->input('document_status');
            $reason = trim((string) $request->input('reason'));

            if ($oldStatus === $newStatus) {
                return $this->jsonSuccess([
                    'success' => true,
                    'message' => 'Dokumentstatus ist unverändert.',
                    'document_status' => $detail->document_status,
                    'document_status_label' => $detail->documentStatusLabel(),
                    'detail' => $detail,
                ]);
            }

            $redirectUrl = null;
            $employeeId = $this->employeeId();

            DB::transaction(function () use ($folder, $detail, $oldStatus, $newStatus, $reason, &$redirectUrl, $employeeId) {
                $biography = is_array($detail->biography_data) ? $detail->biography_data : [];
                $folderHistory = is_array($folder->history) ? $folder->history : [];

                // ---------------------------------------------------------
                // SCENARIO 1: OFFER -> DEAL
                // ---------------------------------------------------------
                if ($oldStatus === OfferDetail::DOCUMENT_STATUS_OFFER && $newStatus === OfferDetail::DOCUMENT_STATUS_DEAL) {

                    $folder->document_status = $newStatus;
                    if (!$folder->deal_status) {
                        $folder->deal_status = OfferFolder::DEAL_STATUS_OPEN;
                    }

                    // Create Snapshot
                    if (!is_array($detail->angebot_snapshot_sections) || count($detail->angebot_snapshot_sections) === 0) {
                        $detail->angebot_snapshot_sections = is_array($detail->sections) ? $detail->sections : [];
                        $detail->angebot_snapshot_at = now();
                    }

                    // CREATE OR UPDATE DEALS TABLE
                    $dealData = [
                        'service_id' => $folder->offer->service_id ?? null,
                        'department_id' => $folder->department_id ?? null,
                        'service' => $folder->offer->service ?? 'Angebot',
                        'employee_id' => $employeeId ?: 1,
                        'price' => $detail->total_net ?? 0,
                        'offer_id' => $folder->offer_id ?: $folder->offer->id,
                        'offer_folder_id' => $folder->id,
                        'offer_number' => $folder->offer->offer_no ?? null,
                        'deal_status' => $folder->deal_status ?: 'open',
                        'status' => 'active',
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ];

                    $existingDeal = DB::table('deals')
                        ->where('customer_id', $folder->customer_id)
                        ->where('alternative_id', $folder->alternative_id)
                        ->where('product_id', $folder->product_id)
                        ->first();

                    if ($existingDeal) {
                        if (empty($existingDeal->order_number)) {
                            $dealData['order_number'] = \App\Models\Deal::generateOrderNo($folder->customer_id, $folder->department_id);
                        }

                        DB::table('deals')
                            ->where('id', $existingDeal->id)
                            ->update($dealData);
                    } else {
                        $dealData['customer_id'] = $folder->customer_id;
                        $dealData['alternative_id'] = $folder->alternative_id;
                        $dealData['product_id'] = $folder->product_id;
                        $dealData['created_at'] = now();
                        $dealData['order_number'] = \App\Models\Deal::generateOrderNo($folder->customer_id, $folder->department_id);
                        DB::table('deals')->insert($dealData);
                    }

                    // UPDATE LEAD PRODUCT LIST STATUS TO 'DEAL'
                    DB::table('lead_product_lists')
                        ->where('customer_id', $folder->customer_id)
                        ->where('alternative_id', $folder->alternative_id)
                        ->where('product_id', $folder->product_id)
                        ->update(['status' => 'deal']);

                    $biography[] = [
                        'type' => 'document_status_changed',
                        'from' => $oldStatus,
                        'to' => $newStatus,
                        'reason' => $reason,
                        'changed_by' => $this->currentHistoryActor(),
                        'created_at' => now()->toDateTimeString(),
                    ];

                    $folderHistory[] = [
                        'type' => 'document_status_changed',
                        'title' => 'Dokumentstatus geändert',
                        'old_document_status' => $oldStatus,
                        'new_document_status' => $newStatus,
                        'reason' => $reason,
                        'changed_by' => $this->currentHistoryActor(),
                        'created_at' => now()->toDateTimeString(),
                    ];

                    $detail->document_status = $newStatus;
                    $detail->biography_data = array_values($biography);
                    $detail->save();

                    $folder->history = array_values($folderHistory);
                    $folder->save();

                    // ---------------------------------------------------------
                    // AUTO-CANCEL SIBLING FOLDERS
                    // ---------------------------------------------------------
                    $siblingFolders = \App\Models\OfferFolder::where('offer_id', $folder->offer_id ?: $folder->offer->id)
                        ->where('id', '!=', $folder->id)
                        ->whereNotIn('status', ['cancel', 'cancelled'])
                        ->get();

                    foreach ($siblingFolders as $sibling) {
                        $sibling->status = 'cancel';
                        $sibling->offer_status = \App\Models\OfferFolder::OFFER_STATUS_CANCELLED;

                        $siblingHistory = is_array($sibling->history) ? $sibling->history : [];
                        $siblingHistory[] = [
                            'type' => 'auto_cancelled',
                            'title' => 'Automatisch storniert',
                            'reason' => "Storniert, da Ordner '{$folder->name}' zum Auftrag wurde.",
                            'changed_by' => $this->currentHistoryActor(),
                            'created_at' => now()->toDateTimeString(),
                        ];
                        $sibling->history = $siblingHistory;
                        $sibling->save();

                        // Also push to the sibling's detail biography if it exists
                        if ($sibling->detail) {
                            $siblingBio = is_array($sibling->detail->biography_data) ? $sibling->detail->biography_data : [];
                            $siblingBio[] = [
                                'type' => 'auto_cancelled',
                                'from' => $sibling->detail->document_status,
                                'to' => 'cancel',
                                'reason' => "Ein anderer Ordner wurde zum Auftrag.",
                                'changed_by' => $this->currentHistoryActor(),
                                'created_at' => now()->toDateTimeString(),
                            ];
                            $sibling->detail->biography_data = array_values($siblingBio);
                            $sibling->detail->save();
                        }
                    }
                }
                // ---------------------------------------------------------
                // SCENARIO 2: DEAL -> OFFER (REVERT AND CLONE)
                // ---------------------------------------------------------
                elseif ($oldStatus !== OfferDetail::DOCUMENT_STATUS_OFFER && $newStatus === OfferDetail::DOCUMENT_STATUS_OFFER) {

                    // 1. Cancel CURRENT folder and offer
                    $folder->offer_status = \App\Models\OfferFolder::OFFER_STATUS_CANCELLED;
                    $folder->status = 'cancelled';

                    if ($folder->offer) {
                        $folder->offer->status = 'cancel';
                        $folder->offer->status_msg = 'Automatisch storniert durch Rückwechsel auf Angebotsstatus.';
                        $folder->offer->save();
                    }

                    // 2. Revert Lead Product List
                    DB::table('lead_product_lists')
                        ->where('customer_id', $folder->customer_id)
                        ->where('alternative_id', $folder->alternative_id)
                        ->where('product_id', $folder->product_id)
                        ->update(['status' => 'offer']);

                    // 3. Soft Delete Deal
                    DB::table('deals')
                        ->where('customer_id', $folder->customer_id)
                        ->where('alternative_id', $folder->alternative_id)
                        ->where('product_id', $folder->product_id)
                        ->update(['deleted_at' => now(), 'status' => 'cancelled']);

                    $folderHistory[] = [
                        'type' => 'document_reverted_to_offer',
                        'title' => 'Zurück auf Angebot',
                        'old_document_status' => $oldStatus,
                        'new_document_status' => $newStatus,
                        'reason' => $reason,
                        'changed_by' => $this->currentHistoryActor(),
                        'created_at' => now()->toDateTimeString(),
                    ];
                    $folder->history = array_values($folderHistory);
                    $folder->save(); // Save current cancelled folder

                    // 4. Create new cloned folder (with status Draft)
                    $newFolder = new OfferFolder();
                    $newFolder->offer_id = $folder->offer_id;
                    $newFolder->customer_id = $folder->customer_id;
                    $newFolder->alternative_id = $folder->alternative_id;
                    $newFolder->product_id = $folder->product_id;
                    $newFolder->created_by = $this->employeeId() ?: 1;
                    $newFolder->name = ($folder->name ?: 'Ordner') . ' (Kopie)';
                    $newFolder->status = 'active';
                    $newFolder->document_status = 'offer';
                    $newFolder->offer_status = 'draft'; // 🟢 Forcing status to draft!
                    $newFolder->deal_status = 'open';
                    $newFolder->color = $folder->color ?: '#93c21c';

                    if (array_key_exists('description', $folder->getAttributes())) {
                        $newFolder->description = $folder->description;
                    }
                    if (array_key_exists('sort_order', $folder->getAttributes())) {
                        $newFolder->sort_order = $folder->sort_order ?? 0;
                    }
                    if (array_key_exists('agb', $folder->getAttributes())) {
                        $newFolder->agb = $folder->agb;
                    }
                    $newFolder->history = [
                        [
                            'type' => 'folder_cloned',
                            'title' => 'Ordner neu erstellt (Rückkehr zum Angebot)',
                            'reason' => $reason,
                            'changed_by' => $this->currentHistoryActor(),
                            'created_at' => now()->toDateTimeString(),
                        ]
                    ];
                    $newFolder->save();

                    // 5. Clone Detail and restore snapshot into sections
                    $newDetail = $detail->replicate();
                    $newDetail->offer_folder_id = $newFolder->id;
                    $newDetail->document_status = 'offer';

                    if (is_array($detail->angebot_snapshot_sections) && count($detail->angebot_snapshot_sections) > 0) {
                        $newDetail->sections = $detail->angebot_snapshot_sections;
                        $totals = $this->calculateDetailTotals(
                            $newDetail->sections,
                            (float) ($newDetail->tax_rate ?? 19)
                        );
                        $newDetail->total_net = $totals['total_net'];
                        $newDetail->total_gross = $totals['total_gross'];
                    }

                    // Clear snapshot on the new clone
                    $newDetail->angebot_snapshot_sections = [];
                    $newDetail->angebot_snapshot_at = null;
                    $newDetail->save();

                    // 6. Clone Attachments
                    $sourceAttachments = $folder->attachments ?? collect();
                    foreach ($sourceAttachments as $attachment) {
                        $newAttachment = $attachment->replicate();
                        $newAttachment->offer_folder_id = $newFolder->id;

                        if (!empty($attachment->file_path) && Storage::disk('public')->exists($attachment->file_path)) {
                            $ext = pathinfo($attachment->file_path, PATHINFO_EXTENSION);
                            $newPath = 'offers/folder-attachments/' . Str::uuid() . ($ext ? '.' . $ext : '');
                            Storage::disk('public')->copy($attachment->file_path, $newPath);

                            $newAttachment->file_path = $newPath;
                            $newAttachment->file_name = basename($newPath);
                            $newAttachment->file_url = asset('storage/' . $newPath);
                        }
                        $newAttachment->save();
                    }

                    // 7. Construct Wizard Redirect URL for frontend
                    $redirectUrl = url('/offers/wizard?' . http_build_query([
                        'offer_id' => $newFolder->offer_id,
                        'offer_folder_id' => $newFolder->id,
                        'customer_id' => $newFolder->customer_id,
                        'alternative_id' => $newFolder->alternative_id,
                        'product_id' => $newFolder->product_id,
                    ]));
                }
            });

            // If we generated a clone redirect URL, send it back immediately
            if ($redirectUrl) {
                return $this->jsonSuccess([
                    'success' => true,
                    'message' => 'Auftrag wurde storniert. Ein neuer Entwurf aus der Momentaufnahme wurde erstellt.',
                    'redirect_url' => $redirectUrl,
                ]);
            }

            $folder->refresh();
            $folder->load(['detail', 'offer.detail', 'offer']);
            $detail = $this->resolveDisplayDetail($folder);

            return $this->jsonSuccess([
                'success' => true,
                'message' => 'Dokumentstatus wurde geändert.',
                'document_status' => $detail?->document_status,
                'document_status_label' => $detail?->documentStatusLabel(),
                'detail' => $detail,
                'folder' => $folder,
            ]);

        } catch (\Throwable $e) {
            report($e);
            return $this->jsonError('Dokumentstatus konnte nicht geändert werden: ' . $e->getMessage(), 500);
        }
    }
    protected function applyStockAllocationToSections(
        array $sections,
        Collection $requestedItems,
        array &$detailHistory = []
    ): array {
        $actor = $this->currentHistoryActor();

        $applyRecursive = function (array $nodes, float $multiplier = 1.0) use (&$applyRecursive, $requestedItems, $actor, &$detailHistory): array {
            foreach ($nodes as &$node) {
                $node = $this->hydrateNodeFromOwnFields($node);

                $nodeQty = (float) ($node['qty'] ?? 0);
                $effectiveQty = round($nodeQty * $multiplier, 4);

                $match = $this->findMatchingRequestedItem($node, $requestedItems);

                if ($match) {
                    $allocation = is_array($node['stock_allocation'] ?? null)
                        ? $node['stock_allocation']
                        : [
                            'offen' => 0,
                            'lager' => 0,
                            'bestellen' => 0,
                            'final' => 0,
                        ];

                    $allocation = [
                        'offen' => (float) ($allocation['offen'] ?? 0),
                        'lager' => (float) ($allocation['lager'] ?? 0),
                        'bestellen' => (float) ($allocation['bestellen'] ?? 0),
                        'final' => (float) ($allocation['final'] ?? 0),
                    ];

                    $currentTotal = $allocation['offen'] + $allocation['lager'] + $allocation['bestellen'] + $allocation['final'];

                    if ($currentTotal <= 0 && $effectiveQty > 0) {
                        $oldStatus = (string) ($node['order_status'] ?? 'offen');
                        if (!in_array($oldStatus, ['offen', 'lager', 'bestellen', 'final'], true)) {
                            $oldStatus = 'offen';
                        }
                        $allocation[$oldStatus] = $effectiveQty;
                    }

                    $moveTo = (string) ($match['move_to'] ?? 'offen');
                    $moveQty = (float) ($match['move_qty'] ?? 0);
                    $sourceStatus = (string) ($match['source_status'] ?? 'offen');

                    if (!in_array($sourceStatus, ['offen', 'lager', 'bestellen', 'final'], true)) {
                        $sourceStatus = 'offen';
                    }

                    if (in_array($moveTo, ['offen', 'lager', 'bestellen', 'final'], true) && $moveQty > 0) {
                        if ($sourceStatus !== $moveTo) {
                            $availableSource = (float) ($allocation[$sourceStatus] ?? 0);
                            $actualMove = min($moveQty, $availableSource);

                            if ($actualMove > 0) {
                                // 1. Process the requested move (e.g., Offen -> Lager)
                                $allocation[$sourceStatus] -= $actualMove;
                                $allocation[$moveTo] += $actualMove;

                                $historyEntry = [
                                    'type' => 'allocation_move',
                                    'from' => $sourceStatus,
                                    'to' => $moveTo,
                                    'qty' => $actualMove,
                                    'reason' => 'Materialstatus geändert',
                                    'changed_by' => $actor,
                                    'created_at' => now()->toDateTimeString(),
                                ];

                                $this->appendMaterialNodeHistory($node, $historyEntry);
                                $detailHistory[] = array_merge($historyEntry, [
                                    'node' => [
                                        'product_id' => $node['product_id'] ?? $node['productId'] ?? null,
                                        'component_id' => $node['component_id'] ?? null,
                                        'article_no' => $node['article_no'] ?? null,
                                        'distributor_article_no' => $node['distributor_article_no'] ?? null,
                                        'name' => $node['name'] ?? $node['title'] ?? null,
                                    ]
                                ]);

                                // 2. NEW LOGIC: Auto-send the remainder to "bestellen"
                                // If we moved items to 'lager' from 'offen', check if anything is left in 'offen'
                                if ($moveTo === 'lager' && $sourceStatus === 'offen' && $allocation['offen'] > 0) {
                                    $autoMoveQty = $allocation['offen'];

                                    $allocation['offen'] -= $autoMoveQty;
                                    $allocation['bestellen'] += $autoMoveQty;

                                    $autoHistoryEntry = [
                                        'type' => 'allocation_move',
                                        'from' => 'offen',
                                        'to' => 'bestellen',
                                        'qty' => $autoMoveQty,
                                        'reason' => 'Automatisch Rest nach Bestellen verschoben',
                                        'changed_by' => $actor,
                                        'created_at' => now()->toDateTimeString(),
                                    ];

                                    $this->appendMaterialNodeHistory($node, $autoHistoryEntry);
                                    $detailHistory[] = array_merge($autoHistoryEntry, [
                                        'node' => [
                                            'product_id' => $node['product_id'] ?? $node['productId'] ?? null,
                                            'component_id' => $node['component_id'] ?? null,
                                            'article_no' => $node['article_no'] ?? null,
                                            'distributor_article_no' => $node['distributor_article_no'] ?? null,
                                            'name' => $node['name'] ?? $node['title'] ?? null,
                                        ]
                                    ]);
                                }
                            }
                        }
                    }

                    $node['stock_allocation'] = $this->normalizeStockAllocation($allocation, $effectiveQty);

                    if ($node['stock_allocation']['final'] >= $effectiveQty) {
                        $node['order_status'] = 'final';
                    } elseif ($node['stock_allocation']['lager'] >= $effectiveQty) {
                        $node['order_status'] = 'lager';
                    } elseif ($node['stock_allocation']['bestellen'] >= $effectiveQty) {
                        $node['order_status'] = 'bestellen';
                    } elseif ($node['stock_allocation']['offen'] >= $effectiveQty) {
                        $node['order_status'] = 'offen';
                    } else {
                        $node['order_status'] = 'partial';
                    }

                    $node['qty_total'] = $effectiveQty;
                }

                if (isset($node['subItems']) && is_array($node['subItems'])) {
                    $childMultiplier = $multiplier * max($nodeQty, 1);
                    $node['subItems'] = $applyRecursive($node['subItems'], $childMultiplier);
                }
            }

            unset($node);

            return $nodes;
        };

        foreach ($sections as &$section) {
            $sectionQty = (float) data_get($section, 'config.qty', 1);
            $sectionUnit = strtolower((string) data_get($section, 'config.unit', ''));
            $sectionMultiplier = $sectionUnit === 'set' ? max($sectionQty, 1) : 1.0;

            $section['items'] = $applyRecursive(
                is_array($section['items'] ?? null) ? $section['items'] : [],
                $sectionMultiplier
            );
        }

        unset($section);

        return $sections;
    }


    protected function normalizeStockAllocation(array $allocation, float $totalQty): array
    {
        $normalized = [
            'offen' => (float) ($allocation['offen'] ?? 0),
            'lager' => (float) ($allocation['lager'] ?? 0),
            'bestellen' => (float) ($allocation['bestellen'] ?? 0),
            'final' => (float) ($allocation['final'] ?? 0),
        ];

        foreach ($normalized as $key => $value) {
            if ($value < 0) {
                $normalized[$key] = 0;
            }
        }

        $sum = $normalized['offen'] + $normalized['lager'] + $normalized['bestellen'] + $normalized['final'];

        if ($sum <= 0 && $totalQty > 0) {
            $normalized['offen'] = $totalQty;
            $sum = $totalQty;
        }

        if ($sum > $totalQty && $sum > 0) {
            $factor = $totalQty / $sum;

            $normalized['offen'] *= $factor;
            $normalized['lager'] *= $factor;
            $normalized['bestellen'] *= $factor;
            $normalized['final'] *= $factor;
        }

        $sumAfter = $normalized['offen'] + $normalized['lager'] + $normalized['bestellen'] + $normalized['final'];
        $diff = $totalQty - $sumAfter;

        if (abs($diff) > 0.0001) {
            $normalized['offen'] += $diff;
        }

        return [
            'offen' => round((float) $normalized['offen'], 4),
            'lager' => round((float) $normalized['lager'], 4),
            'bestellen' => round((float) $normalized['bestellen'], 4),
            'final' => round((float) $normalized['final'], 4),
        ];
    }
    protected function resolveNodeEffectiveQty(array $node, float $multiplier = 1.0): float
    {
        $qty = (float) ($node['qty'] ?? 0);
        return round($qty * $multiplier, 4);
    }


    protected function syncFolderDetailFromOfferDetailIfNewer(OfferFolder $folder): void
    {
        $folder->loadMissing(['detail', 'offer.detail']);

        $folderDetail = $folder->detail;
        $offerDetail = $folder->offer?->detail;

        if (!$offerDetail) {
            return;
        }

        // If the folder has no detail record, create an EMPTY one, 
        // taking ONLY branding from the main offer
        if (!$folderDetail) {
            $folderDetail = OfferDetail::create([
                'offer_id' => $offerDetail->offer_id ?: $folder->offer_id,
                'offer_folder_id' => $folder->id,
                'document_status' => OfferDetail::DOCUMENT_STATUS_OFFER, // Force to 'offer'
                'angebot_snapshot_sections' => [],
                'angebot_snapshot_at' => null,
                'tax_rate' => $offerDetail->tax_rate ?? 19,
                'total_net' => 0,
                'total_gross' => 0,
                'sections' => [], // Force empty items!
                'placed_images' => [],
                'print_attachments' => [],
                'biography_data' => [],
                'company_name' => $offerDetail->company_name,
                'brand_mode' => $offerDetail->brand_mode,
                'brand_color' => $offerDetail->brand_color,
                'brand_logo_url' => $offerDetail->brand_logo_url,
                'cover_text' => $offerDetail->cover_text,
                'cover_text_html' => $offerDetail->cover_text_html,
                'agb_title' => $offerDetail->agb_title,
                'agb_text' => $offerDetail->agb_text,
                'material_history' => [],
            ]);

            $folder->setRelation('detail', $folderDetail);
            return;
        }

        // FIX: The aggressive overwriting logic has been completely removed.
        // A folder's sections and document_status should never be overwritten
        // by the parent offer after it has been created.
    }

    public function laborQualificationOptions(Request $request, OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        $validator = Validator::make($request->all(), [
            'required_qualification_id' => 'required|integer|exists:position_qualifications,id',
            'qty' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'margin_percent' => 'nullable|numeric',
            'current_rate' => 'nullable|numeric|min:0',
            'current_ek' => 'nullable|numeric|min:0',
            'labor_title' => 'nullable|string|max:255',
            'parent_title' => 'nullable|string|max:255',
            'section_title' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->jsonError($validator->errors()->first(), 422);
        }

        try {
            $requiredQualificationId = (int) $request->integer('required_qualification_id');

            $hours = (float) $request->input('qty', 1);
            if ($hours <= 0) {
                $hours = 1;
            }

            $marginPercent = $request->has('margin_percent')
                ? (float) $request->input('margin_percent')
                : null;

            $requiredQualification = PositionQualification::query()
                ->select('id', 'name', 'default_price')
                ->findOrFail($requiredQualificationId);

            $hierarchies = PositionQualificationHierarchy::query()
                ->with([
                    'performerQualification:id,name,default_price',
                    'requiredQualification:id,name,default_price',
                ])
                ->where('required_qualification_id', $requiredQualificationId)
                ->where('allowed', true)
                ->get()
                ->keyBy('performer_qualification_id');

            $performerIds = $hierarchies->keys()
                ->map(fn($id) => (int) $id)
                ->push($requiredQualificationId)
                ->unique()
                ->values();

            $performers = PositionQualification::query()
                ->select('id', 'name', 'default_price')
                ->whereIn('id', $performerIds)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            $options = $performers->map(function (PositionQualification $performer) use ($requiredQualification, $hierarchies, $requiredQualificationId, $hours, $marginPercent) {
                $isSelf = (int) $performer->id === (int) $requiredQualificationId;
                $rule = $hierarchies->get((int) $performer->id);

                $efficiencyFactor = $isSelf
                    ? 1.0
                    : (float) ($rule?->efficiency_factor ?? 1.0);

                $costFactor = $isSelf
                    ? 1.0
                    : (float) ($rule?->cost_factor ?? 1.0);

                $effectiveHours = round($hours * $efficiencyFactor, 4);

                $ekPerHour = (float) ($performer->default_price ?? 0);
                $realEkPerHour = round($ekPerHour * $costFactor, 4);

                $sellPerHour = $marginPercent !== null
                    ? round($realEkPerHour * (1 + ($marginPercent / 100)), 2)
                    : $realEkPerHour;

                $ekTotal = round($effectiveHours * $realEkPerHour, 2);
                $sellTotal = round($effectiveHours * $sellPerHour, 2);

                return [
                    'qualification_id' => $performer->id,
                    'qualification_name' => $performer->name,
                    'required_qualification_id' => $requiredQualification->id,
                    'required_qualification_name' => $requiredQualification->name,

                    'is_original' => $isSelf,
                    'allowed' => true,

                    'base_hours' => round($hours, 4),
                    'effective_hours' => $effectiveHours,

                    'efficiency_factor' => round($efficiencyFactor, 4),
                    'cost_factor' => round($costFactor, 4),

                    'ek_per_hour' => $realEkPerHour,
                    'sell_per_hour' => $sellPerHour,

                    'ek_total' => $ekTotal,
                    'sell_total' => $sellTotal,

                    'difference_to_original_hours' => round($effectiveHours - $hours, 4),
                ];
            })->values();

            return $this->jsonSuccess([
                'success' => true,
                'required_qualification' => [
                    'id' => $requiredQualification->id,
                    'name' => $requiredQualification->name,
                    'default_price' => (float) $requiredQualification->default_price,
                ],
                'labor' => [
                    'title' => $request->input('labor_title'),
                    'parent_title' => $request->input('parent_title'),
                    'section_title' => $request->input('section_title'),
                    'qty' => $hours,
                    'unit' => $request->input('unit', 'Std'),
                    'current_rate' => (float) $request->input('current_rate', 0),
                    'current_ek' => (float) $request->input('current_ek', 0),
                    'margin_percent' => $marginPercent,
                ],
                'options' => $options,
            ]);
        } catch (Throwable $e) {
            report($e);

            return $this->jsonError(
                'Qualifikationsmöglichkeiten konnten nicht geladen werden: ' . $e->getMessage(),
                500
            );
        }
    }

    public function getAttachments(OfferFolder $folder): JsonResponse
    {
        if (!$this->canAccessFolderByTeam($folder)) {
            return $this->denyFolderAccessJson($folder);
        }

        try {
            $attachments = $folder->attachments()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            return $this->jsonSuccess([
                'success' => true,
                'attachments' => $attachments,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return $this->jsonError('Fehler beim Laden der Anhänge: ' . $e->getMessage(), 500);
        }
    }

}