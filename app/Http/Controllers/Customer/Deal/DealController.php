<?php

namespace App\Http\Controllers\Customer\Deal;
use App\Http\Controllers\Controller;

use App\Models\Deal;
use App\Models\NewLeads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Image;
use App\Models\DealNote;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\OfferDetail;
use App\Models\PlannerPlan;
use App\Models\PlannerItem;
use App\Models\PlannerItemMaterial;
use App\Models\PlannerItemDependency;
use App\Models\LeadProductList;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
use App\Models\Employee;
use App\Models\MainAppointment;
use Carbon\Carbon;
use App\Models\OfferFolderAttachment;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Illuminate\Support\Facades\Schema;
use Throwable;
class DealController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Returns SQL fragments used to calculate the latest real activity on a deal.
     *
     * It includes:
     * - deal changes
     * - Feinaufmaß header/details/items/history
     * - material save timestamp
     * - notes
     * - documents/images/attachments
     * - invoices
     * - planning rows
     */
    protected function dealActivitySqlParts(): array
    {
        $zero = "'1970-01-01 00:00:00'";

        $parts = [
            'deal' => "COALESCE(deals.updated_at, deals.created_at, {$zero})",
        ];

        if (Schema::hasTable('deal_measurements')) {
            $parts['feinaufmass'] = "COALESCE((
                SELECT MAX(GREATEST(
                    COALESCE(dm.updated_at, {$zero}),
                    COALESCE(dm.created_at, {$zero}),
                    COALESCE(dm.sent_at, {$zero}),
                    COALESCE(dm.materials_saved_at, {$zero})
                ))
                FROM deal_measurements dm
                WHERE dm.deleted_at IS NULL
                AND (
                    dm.deal_id = deals.id
                    OR (
                        dm.deal_id IS NULL
                        AND dm.customer_id = deals.customer_id
                        AND (dm.alternative_id = deals.alternative_id OR dm.alternative_id IS NULL)
                        AND (dm.product_id = deals.product_id OR dm.product_id IS NULL)
                    )
                    OR (dm.offer_id IS NOT NULL AND dm.offer_id = deals.offer_id)
                    OR (dm.offer_folder_id IS NOT NULL AND dm.offer_folder_id = deals.offer_folder_id)
                )
            ), {$zero})";
        } else {
            $parts['feinaufmass'] = $zero;
        }

        if (Schema::hasTable('deal_measurement_details') && Schema::hasTable('deal_measurements')) {
            $parts['feinaufmass_details'] = "COALESCE((
                SELECT MAX(COALESCE(dmd.saved_at, dmd.updated_at, dmd.created_at, {$zero}))
                FROM deal_measurement_details dmd
                INNER JOIN deal_measurements dm2 ON dm2.id = dmd.deal_measurement_id
                WHERE dm2.deleted_at IS NULL
                AND (
                    dm2.deal_id = deals.id
                    OR (dm2.offer_id IS NOT NULL AND dm2.offer_id = deals.offer_id)
                    OR (dm2.offer_folder_id IS NOT NULL AND dm2.offer_folder_id = deals.offer_folder_id)
                )
            ), {$zero})";
        } else {
            $parts['feinaufmass_details'] = $zero;
        }

        if (Schema::hasTable('deal_measurement_items') && Schema::hasTable('deal_measurements')) {
            $parts['material'] = "COALESCE((
                SELECT MAX(COALESCE(dmi.updated_at, dmi.created_at, {$zero}))
                FROM deal_measurement_items dmi
                INNER JOIN deal_measurements dm3 ON dm3.id = dmi.deal_measurement_id
                WHERE dm3.deleted_at IS NULL
                AND (
                    dmi.deal_id = deals.id
                    OR dm3.deal_id = deals.id
                    OR (dm3.offer_id IS NOT NULL AND dm3.offer_id = deals.offer_id)
                    OR (dm3.offer_folder_id IS NOT NULL AND dm3.offer_folder_id = deals.offer_folder_id)
                )
            ), {$zero})";
        } else {
            $parts['material'] = $zero;
        }

        if (Schema::hasTable('deal_measurement_histories') && Schema::hasTable('deal_measurements')) {
            $parts['measurement_history'] = "COALESCE((
                SELECT MAX(COALESCE(dmh.created_at, {$zero}))
                FROM deal_measurement_histories dmh
                INNER JOIN deal_measurements dm4 ON dm4.id = dmh.deal_measurement_id
                WHERE dm4.deleted_at IS NULL
                AND (
                    dm4.deal_id = deals.id
                    OR (dm4.offer_id IS NOT NULL AND dm4.offer_id = deals.offer_id)
                    OR (dm4.offer_folder_id IS NOT NULL AND dm4.offer_folder_id = deals.offer_folder_id)
                )
            ), {$zero})";
        } else {
            $parts['measurement_history'] = $zero;
        }

        if (Schema::hasTable('deal_notes')) {
            $parts['notes'] = "COALESCE((
                SELECT MAX(COALESCE(dn.updated_at, dn.created_at, {$zero}))
                FROM deal_notes dn
                WHERE dn.deal_id = deals.id
            ), {$zero})";
        } else {
            $parts['notes'] = $zero;
        }

        if (Schema::hasTable('images')) {
            $parts['documents_images'] = "COALESCE((
                SELECT MAX(COALESCE(images.updated_at, images.created_at, {$zero}))
                FROM images
                WHERE images.customer_id = deals.customer_id
                AND images.deleted_at IS NULL
                AND images.status IN ('order', 'deal', 'active')
                AND (
                    images.alternative_id = deals.alternative_id
                    OR images.alternative_id IS NULL
                )
                AND (
                    images.article_group = deals.product_id
                    OR images.article_group IS NULL
                    OR images.article_group = 0
                )
            ), {$zero})";
        } else {
            $parts['documents_images'] = $zero;
        }

        if (Schema::hasTable('offer_folder_attachments')) {
            $parts['documents_attachments'] = "COALESCE((
                SELECT MAX(COALESCE(ofa.updated_at, ofa.created_at, {$zero}))
                FROM offer_folder_attachments ofa
                WHERE ofa.deleted_at IS NULL
                AND (
                    ofa.offer_folder_id = COALESCE(
                        deals.offer_folder_id,
                        (
                            SELECT odx.offer_folder_id
                            FROM offer_details odx
                            WHERE odx.offer_id = deals.offer_id
                            ORDER BY odx.id DESC
                            LIMIT 1
                        )
                    )
                    OR ofa.offer_id = deals.offer_id
                )
            ), {$zero})";
        } else {
            $parts['documents_attachments'] = $zero;
        }

        // deal_invoices stillgelegt (invoices = führende Schiene, 2026-07-05) — kein Aktivitäts-Signal aus der Alt-Schiene.
        // (invoices-basiertes Aktivitätssignal ist ein separater, bewusster Ausbau — nicht Teil des Rückbaus.)
        $parts['invoices'] = $zero;

        if (Schema::hasTable('planner_plans') && Schema::hasColumn('planner_plans', 'meta')) {
            $parts['planning'] = "COALESCE((
                SELECT MAX(COALESCE(pp.updated_at, pp.created_at, {$zero}))
                FROM planner_plans pp
                WHERE JSON_UNQUOTE(JSON_EXTRACT(pp.meta, '$.deal_id')) = CAST(deals.id AS CHAR)
                OR JSON_UNQUOTE(JSON_EXTRACT(pp.meta, '$.offer_id')) = CAST(deals.offer_id AS CHAR)
                OR JSON_UNQUOTE(JSON_EXTRACT(pp.meta, '$.offer_folder_id')) = CAST(deals.offer_folder_id AS CHAR)
            ), {$zero})";
        } else {
            $parts['planning'] = $zero;
        }

        return $parts;
    }

    protected function dealLatestChangeAtSql(): string
    {
        return 'GREATEST(' . implode(',', array_values($this->dealActivitySqlParts())) . ')';
    }

    protected function dealLatestChangeSourceSql(): string
    {
        $parts = $this->dealActivitySqlParts();
        $latestSql = 'GREATEST(' . implode(',', array_values($parts)) . ')';

        $labels = [
            'planning' => 'Projektplanung',
            'invoices' => 'Rechnung',
            'documents_attachments' => 'Dokument',
            'documents_images' => 'Dokument',
            'notes' => 'Notiz',
            'measurement_history' => 'Feinaufmaß Verlauf',
            'material' => 'Materialliste',
            'feinaufmass_details' => 'Feinaufmaß Details',
            'feinaufmass' => 'Feinaufmaß',
            'deal' => 'Auftrag',
        ];

        $case = 'CASE';

        foreach ($labels as $key => $label) {
            if (isset($parts[$key])) {
                $case .= " WHEN {$latestSql} = {$parts[$key]} THEN '{$label}'";
            }
        }

        return $case . " ELSE 'Auftrag' END";
    }

    protected function dealLatestChangeTextSql(): string
    {
        $parts = $this->dealActivitySqlParts();
        $latestSql = 'GREATEST(' . implode(',', array_values($parts)) . ')';

        $texts = [
            'planning' => 'Projektplanung wurde geändert',
            'invoices' => 'Rechnung wurde geändert',
            'documents_attachments' => 'Dokument wurde geändert',
            'documents_images' => 'Bild/Dokument wurde geändert',
            'notes' => 'Notiz wurde hinzugefügt/geändert',
            'measurement_history' => 'Feinaufmaß-Verlauf wurde geändert',
            'material' => 'Materialliste wurde geändert',
            'feinaufmass_details' => 'Feinaufmaß-Details wurden gespeichert',
            'feinaufmass' => 'Feinaufmaß wurde geändert',
            'deal' => 'Auftrag wurde geändert',
        ];

        $case = 'CASE';

        foreach ($texts as $key => $text) {
            if (isset($parts[$key])) {
                $case .= " WHEN {$latestSql} = {$parts[$key]} THEN '{$text}'";
            }
        }

        return $case . " ELSE 'Auftrag wurde geändert' END";
    }

    protected function baseDealQuery(bool $onlyTrashed = false)
    {
        $query = Deal::query()
            ->when($onlyTrashed, fn($q) => $q->onlyTrashed())
            ->when(!$onlyTrashed, fn($q) => $q->whereNull('deals.deleted_at'))
            ->leftJoin('new_leads', 'new_leads.id', '=', 'deals.customer_id')
            ->leftJoin('lead_alternative_adds as alt', 'alt.id', '=', 'deals.alternative_id')
            ->leftJoin('article_groups', 'article_groups.id', '=', 'deals.product_id')
            ->leftJoin('employees', 'employees.id', '=', 'deals.employee_id')
            ->leftJoin('employees as checked_employee', 'checked_employee.id', '=', 'deals.checked_by')
            ->leftJoin('employees as reviewer_employee', 'reviewer_employee.id', '=', 'deals.reviewer_id')
            ->select([
                'deals.*', // Select all deal fields
                'new_leads.customer_no',
                'new_leads.firma',
                'new_leads.title',
                'new_leads.name',
                'new_leads.lastname',
                'new_leads.email',
                'new_leads.phone',
                'new_leads.telephone',
                'new_leads.contact_person',
                'alt.object_name',
                'alt.full_address',
                'alt.street',
                'alt.postcode',
                'alt.city',
                'alt.lat',
                'alt.lon',
                'alt.stage as alternative_stage',
                'article_groups.article_group',
                'article_groups.initial',
                'employees.name as emp_name',
                'employees.lastname as emp_lastname',
                'employees.image as emp_image',
                'employees.gender as emp_gender',
                'checked_employee.name as checked_name',
                'checked_employee.lastname as checked_lastname',
                'reviewer_employee.name as reviewer_name',
                'reviewer_employee.lastname as reviewer_lastname',

                // --- ADDED TOTALS SUBQUERIES ---
                DB::raw("(SELECT COUNT(*) FROM deal_notes WHERE deal_notes.deal_id = deals.id) as notes_count"),
                DB::raw("
                    (
                        (
                            SELECT COUNT(*)
                            FROM images
                            WHERE images.customer_id = deals.customer_id
                            AND images.status IN ('order', 'deal')
                            AND images.deleted_at IS NULL
                            AND (
                                images.alternative_id = deals.alternative_id
                                OR images.alternative_id IS NULL
                            )
                            AND (
                                images.article_group = deals.product_id
                                OR images.article_group IS NULL
                                OR images.article_group = 0
                            )
                        )
                        +
                        (
                            SELECT COUNT(*)
                            FROM offer_folder_attachments
                            WHERE offer_folder_attachments.deleted_at IS NULL
                            AND (
                                offer_folder_attachments.offer_folder_id = COALESCE(
                                    deals.offer_folder_id,
                                    (
                                        SELECT offer_details.offer_folder_id
                                        FROM offer_details
                                        WHERE offer_details.offer_id = deals.offer_id
                                        ORDER BY offer_details.id DESC
                                        LIMIT 1
                                    )
                                )
                                OR offer_folder_attachments.offer_id = deals.offer_id
                            )
                        )
                    ) as files_count
                "),
                DB::raw($this->dealLatestChangeAtSql() . ' as latest_change_at'),
                DB::raw($this->dealLatestChangeSourceSql() . ' as latest_change_source'),
                DB::raw($this->dealLatestChangeTextSql() . ' as latest_change_text')
            ])
            ->distinct('deals.id');

        return $query;
    }



    protected function applyDealFilters($query, Request $request, array $options = [])
    {
        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $projectStatus = trim((string) $request->input('project_status', ''));
        $productId = $request->input('product_id');
        $departmentId = $request->input('department_id');
        $employeeFilter = trim((string) $request->input('employee_filter', ''));
        $dateFrom = trim((string) $request->input('date_from', ''));
        $dateTo = trim((string) $request->input('date_to', ''));
        $sortBy = trim((string) $request->input('sort_by', 'latest_change'));
        $sortDir = strtolower(trim((string) $request->input('sort_dir', 'desc'))) === 'asc' ? 'asc' : 'desc';
        $authEmployeeId = (string) auth()->user()->name;

        if (!empty($options['exclude_statuses'])) {
            $query->whereNotIn('deals.status', $options['exclude_statuses']);
        }

        if (!empty($options['force_status'])) {
            $query->where('deals.status', $options['force_status']);
        }

        if ($search !== '') {
            $digits = preg_replace('/\D+/', '', $search);

            $query->where(function ($q) use ($search, $digits) {
                $q->where('new_leads.firma', 'like', "%{$search}%")
                    ->orWhere('new_leads.name', 'like', "%{$search}%")
                    ->orWhere('new_leads.lastname', 'like', "%{$search}%")
                    ->orWhere('new_leads.email', 'like', "%{$search}%")
                    ->orWhere('article_groups.article_group', 'like', "%{$search}%")
                    ->orWhere('article_groups.initial', 'like', "%{$search}%")
                    ->orWhere('alt.object_name', 'like', "%{$search}%")
                    ->orWhere('alt.city', 'like', "%{$search}%")
                    ->orWhere('alt.postcode', 'like', "%{$search}%")
                    ->orWhere('deals.offer_number', 'like', "%{$search}%")
                    ->orWhere('deals.order_number', 'like', "%{$search}%")
                    ->orWhere('deals.info', 'like', "%{$search}%");

                if ($digits !== '') {
                    $q->orWhereRaw("REGEXP_REPLACE(COALESCE(new_leads.phone,''), '[^0-9]', '') LIKE ?", ["%{$digits}%"])
                        ->orWhereRaw("REGEXP_REPLACE(COALESCE(new_leads.telephone,''), '[^0-9]', '') LIKE ?", ["%{$digits}%"]);
                }
            });
        }

        if ($status !== '') {
            $query->where('deals.status', $status);
        }

        if ($projectStatus !== '') {
            $query->where('deals.project_status', $projectStatus);
        }

        if (!empty($productId)) {
            $query->where('deals.product_id', $productId);
        }

        if (!empty($departmentId)) {
            $query->where('deals.department_id', $departmentId);
        }

        if ($dateFrom !== '') {
            $query->whereDate('deals.created_at', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('deals.created_at', '<=', $dateTo);
        }

        if ($employeeFilter !== '') {
            if ($employeeFilter === 'mine') {
                $query->where(function ($q) use ($authEmployeeId) {
                    $q->where('deals.employee_id', $authEmployeeId)
                        ->orWhere('deals.checked_by', $authEmployeeId)
                        ->orWhere('deals.reviewer_id', $authEmployeeId);
                });
            } elseif (is_numeric($employeeFilter)) {
                $employeeFilter = (int) $employeeFilter;

                $query->where(function ($q) use ($employeeFilter) {
                    $q->where('deals.employee_id', $employeeFilter)
                        ->orWhere('deals.checked_by', $employeeFilter)
                        ->orWhere('deals.reviewer_id', $employeeFilter);
                });
            }
        }

        $sortable = [
            'created_at' => 'deals.created_at',
            'updated_at' => 'deals.updated_at',
            'latest_change' => 'latest_change_at',
            'customer' => 'new_leads.name',
            'product' => 'article_groups.article_group',
            'status' => 'deals.status',
            'project_status' => 'deals.project_status',
            'price' => 'deals.price',
            'offer_number' => 'deals.offer_number',
            'city' => 'alt.city',
        ];

        if ($sortBy === 'latest_change') {
            $query->orderByRaw('latest_change_at ' . $sortDir);
        } else {
            $query->orderBy($sortable[$sortBy] ?? 'deals.created_at', $sortDir);
        }

        $query->orderBy('deals.id', 'desc');

        return $query;
    }
    protected function sidebarData(): array
    {
        return [
            'customers' => NewLeads::query()
                ->select('id', 'firma', 'name', 'lastname')
                ->orderBy('firma')
                ->orderBy('name')
                ->get(),

            'products' => DB::table('article_groups')
                ->select('id', 'article_group')
                ->orderBy('article_group')
                ->get(),

            'employees' => DB::table('employees')
                ->select('id', 'name', 'lastname')
                ->where('status', 'Active')
                ->orderBy('name')
                ->get(),

            'departments' => DB::table('departments')
                ->select('id', 'department_name')
                ->orderBy('department_name')
                ->get(),
        ];
    }


    protected function dealStageCandidateKeys(): array
    {
        return ['deal', 'auftrag'];
    }

    protected function findDealWorkflowMainStage(): ?LeadStage
    {
        if (!Schema::hasTable('lead_stages')) {
            return null;
        }

        $candidateKeys = $this->dealStageCandidateKeys();

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

        return LeadStage::query()
            ->whereNull('deleted_at')
            ->where(function ($query) use ($candidateKeys) {
                foreach ($candidateKeys as $candidateKey) {
                    $query->orWhereRaw('LOWER(`key`) LIKE ?', ['%' . $candidateKey . '%'])
                        ->orWhereRaw('LOWER(`name`) LIKE ?', ['%' . $candidateKey . '%']);
                }
            })
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();
    }

    protected function dealWorkflowSubStages(bool $includeInactive = false)
    {
        if (!Schema::hasTable('lead_stage_sub_stages')) {
            return collect($this->legacyDealWorkflowStages());
        }

        $stage = $this->findDealWorkflowMainStage();

        if (!$stage) {
            return collect($this->legacyDealWorkflowStages());
        }

        $rows = LeadStageSubStage::query()
            ->where('lead_stage_id', $stage->id)
            ->whereNull('deleted_at')
            ->when(!$includeInactive, fn($q) => $q->where('is_active', true))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($rows->isEmpty()) {
            return collect($this->legacyDealWorkflowStages());
        }

        return $rows->values()->map(function (LeadStageSubStage $subStage, int $index) {
            return (object) [
                'id' => $subStage->id,
                'lead_stage_id' => $subStage->lead_stage_id,
                'lead_stage_sub_stage_id' => $subStage->id,
                'key' => $subStage->key,
                'name' => $subStage->name,
                'label' => $subStage->name,
                'color' => $subStage->color ?: '#93c21c',
                'icon' => $subStage->icon ?: 'list',
                'sort_order' => (int) ($subStage->sort_order ?: ($index + 1)),
                'position' => (int) ($subStage->sort_order ?: ($index + 1)),
                'is_default' => (bool) $subStage->is_default,
                'is_active' => (bool) $subStage->is_active,
                'source' => 'lead_stage_sub_stages',
            ];
        });
    }

    protected function legacyDealWorkflowStages(): array
    {
        return [
            (object) ['id' => null, 'lead_stage_id' => null, 'lead_stage_sub_stage_id' => null, 'key' => 'open', 'name' => 'Offen', 'label' => 'Offen', 'color' => '#f59e0b', 'icon' => 'clock', 'sort_order' => 10, 'position' => 10, 'is_default' => true, 'is_active' => true, 'source' => 'legacy'],
            (object) ['id' => null, 'lead_stage_id' => null, 'lead_stage_sub_stage_id' => null, 'key' => 'confirm', 'name' => 'Bestätigt', 'label' => 'Bestätigt', 'color' => '#10b981', 'icon' => 'check-circle', 'sort_order' => 20, 'position' => 20, 'is_default' => false, 'is_active' => true, 'source' => 'legacy'],
            (object) ['id' => null, 'lead_stage_id' => null, 'lead_stage_sub_stage_id' => null, 'key' => 'inconfirm', 'name' => 'Unbestätigt', 'label' => 'Unbestätigt', 'color' => '#ef4444', 'icon' => 'alert-circle', 'sort_order' => 30, 'position' => 30, 'is_default' => false, 'is_active' => true, 'source' => 'legacy'],
            (object) ['id' => null, 'lead_stage_id' => null, 'lead_stage_sub_stage_id' => null, 'key' => 'pause', 'name' => 'Pausiert', 'label' => 'Pausiert', 'color' => '#f59e0b', 'icon' => 'pause-circle', 'sort_order' => 40, 'position' => 40, 'is_default' => false, 'is_active' => true, 'source' => 'legacy'],
            (object) ['id' => null, 'lead_stage_id' => null, 'lead_stage_sub_stage_id' => null, 'key' => 'cancel', 'name' => 'Absage', 'label' => 'Absage', 'color' => '#ef4444', 'icon' => 'x-circle', 'sort_order' => 50, 'position' => 50, 'is_default' => false, 'is_active' => true, 'source' => 'legacy'],
        ];
    }

    protected function dealWorkflowStageKeys(): array
    {
        return $this->dealWorkflowSubStages(true)
            ->pluck('key')
            ->filter()
            ->map(fn($key) => strtolower(trim((string) $key)))
            ->unique()
            ->values()
            ->all();
    }

    protected function dealWorkflowDefaultKey(): string
    {
        $stages = $this->dealWorkflowSubStages(true);
        $default = $stages->firstWhere('is_default', true) ?: $stages->first();

        return (string) ($default->key ?? 'open');
    }

    protected function normalizeDealWorkflowStatus(?string $status): string
    {
        $status = strtolower(trim((string) $status));
        $keys = $this->dealWorkflowStageKeys();

        if ($status !== '' && in_array($status, $keys, true)) {
            return $status;
        }

        $aliasGroups = [
            'open' => ['open', 'offen', 'auftrag_erhalten', 'auftragspruefung', 'auftrag_pruefung', 'auftrag_erhalten'],
            'confirm' => ['confirm', 'confirmed', 'bestaetigt', 'bestatigt', 'geprueft', 'gepruft', 'auftrag_bestaetigt', 'auftrag_bestaetigt'],
            'inconfirm' => ['inconfirm', 'unconfirmed', 'unbestaetigt', 'unbestatigt', 'daten_fehlen', 'klaerung', 'wartet'],
            'pause' => ['pause', 'paused', 'pausiert', 'on_hold', 'warten'],
            'cancel' => ['cancel', 'cancelled', 'storniert', 'abgesagt', 'absage'],
            'complete' => ['complete', 'completed', 'abgeschlossen', 'fertig'],
            'junk' => ['junk'],
        ];

        $wantedAliases = $aliasGroups[$status] ?? [$status];

        foreach ($wantedAliases as $alias) {
            if (in_array($alias, $keys, true)) {
                return $alias;
            }
        }

        foreach ($keys as $key) {
            foreach ($wantedAliases as $alias) {
                if ($alias !== '' && str_contains($key, $alias)) {
                    return $key;
                }
            }
        }

        // Best effort for old rows: keep archived/special statuses outside the deal workflow.
        if (in_array($status, ['junk', 'complete'], true)) {
            return $status === 'junk' ? 'Junk' : 'complete';
        }

        return $this->dealWorkflowDefaultKey();
    }

    protected function dealWorkflowLabelMap(): array
    {
        return $this->dealWorkflowSubStages(true)
            ->mapWithKeys(fn($stage) => [(string) $stage->key => (string) ($stage->label ?? $stage->name ?? $stage->key)])
            ->all();
    }

    protected function dealWorkflowColorMap(): array
    {
        return $this->dealWorkflowSubStages(true)
            ->mapWithKeys(fn($stage) => [(string) $stage->key => (string) ($stage->color ?? '#93c21c')])
            ->all();
    }

    protected function syncDealWorkflowTargets(Deal $deal, string $statusKey): void
    {
        $mainStage = $this->findDealWorkflowMainStage();
        $subStage = null;

        if ($mainStage && Schema::hasTable('lead_stage_sub_stages')) {
            $subStage = LeadStageSubStage::query()
                ->where('lead_stage_id', $mainStage->id)
                ->where('key', $statusKey)
                ->whereNull('deleted_at')
                ->first();
        }

        if (Schema::hasTable('lead_product_lists')) {
            $leadProduct = LeadProductList::query()
                ->where('customer_id', $deal->customer_id)
                ->where('product_id', $deal->product_id)
                ->when($deal->alternative_id, fn($q) => $q->where('alternative_id', $deal->alternative_id))
                ->first();

            if ($leadProduct) {
                $update = ['updated_at' => now()];

                if (Schema::hasColumn('lead_product_lists', 'status')) {
                    $update['status'] = 'deal';
                }

                if (Schema::hasColumn('lead_product_lists', 'stage')) {
                    $update['stage'] = 'deal';
                }

                if ($mainStage && Schema::hasColumn('lead_product_lists', 'lead_stage_id')) {
                    $update['lead_stage_id'] = $mainStage->id;
                }

                if ($subStage && Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
                    $update['lead_stage_sub_stage_id'] = $subStage->id;
                }

                if ($subStage && Schema::hasColumn('lead_product_lists', 'lead_sub_stage_id')) {
                    $update['lead_sub_stage_id'] = $subStage->id;
                }

                if (Schema::hasColumn('lead_product_lists', 'sub_stage_key')) {
                    $update['sub_stage_key'] = $statusKey;
                }

                DB::table('lead_product_lists')->where('id', $leadProduct->id)->update($update);
            }
        }

        if (Schema::hasTable('offer_folders') && !empty($deal->offer_folder_id)) {
            $folderUpdate = ['updated_at' => now()];

            if (Schema::hasColumn('offer_folders', 'document_status')) {
                $folderUpdate['document_status'] = 'deal';
            }

            if (Schema::hasColumn('offer_folders', 'deal_status')) {
                $folderUpdate['deal_status'] = $statusKey;
            }

            if (Schema::hasColumn('offer_folders', 'status')) {
                $folderUpdate['status'] = $statusKey;
            }

            DB::table('offer_folders')->where('id', $deal->offer_folder_id)->update($folderUpdate);
        }
    }


    protected function statsFromQuery($baseQuery): array
    {
        $rows = (clone $baseQuery)->get([
            'deals.id',
            'deals.status',
            'deals.project_status',
            'deals.price',
        ]);

        $normalizedRows = $rows->map(function ($row) {
            $row->normalized_status = $this->normalizeDealWorkflowStatus($row->status);
            return $row;
        });

        $stageCounts = $this->dealWorkflowSubStages(true)
            ->mapWithKeys(function ($stage) use ($normalizedRows) {
                return [(string) $stage->key => $normalizedRows->where('normalized_status', (string) $stage->key)->count()];
            })
            ->all();

        $defaultKey = $this->dealWorkflowDefaultKey();
        $keys = array_keys($stageCounts);

        return [
            'totalCount' => $rows->count(),
            'openCount' => $stageCounts[$defaultKey] ?? $rows->where('status', 'open')->count(),
            'confirmCount' => $rows->whereIn('status', ['confirm', 'complete'])->count(),
            'inconfirmCount' => $rows->whereIn('status', ['inconfirm', 'cancel'])->count(),
            'draftCount' => $rows->where('project_status', 'draft')->count(),
            'priceTotal' => (float) $rows->sum(fn($r) => (float) ($r->price ?? 0)),
            'dealStageCounts' => $stageCounts,
            'dealStageTotal' => array_sum($stageCounts),
        ];
    }

    public function index(Request $request)
    {
        $base = $this->baseDealQuery(false);

        // --- NEU: Zur Sicherheit die Spalte explizit mitladen ---
        // (Nur nötig, falls baseDealQuery() nicht eh schon deals.* lädt)
        // $base->addSelect('deals.measurement_status'); 

        $base = $this->applyDealFilters($base, $request, [
            'exclude_statuses' => ['complete', 'Junk'],
        ]);

        $stats = $this->statsFromQuery(clone $base);

        $data = $base->paginate(19)->appends($request->query());

        return view('admin.deal.customer_view', array_merge([
            'data' => $data,
            'pageTitle' => 'AUFTRÄGE',
            'routeType' => 'default',
            'dealWorkflowStages' => $this->dealWorkflowSubStages(true),
            'dealWorkflowStageKeys' => $this->dealWorkflowStageKeys(),
            'dealWorkflowLabelMap' => $this->dealWorkflowLabelMap(),
            'dealWorkflowColorMap' => $this->dealWorkflowColorMap(),
        ], $stats, $this->sidebarData()));
    }

    public function all(Request $request)
    {
        $base = $this->baseDealQuery(false);
        $base = $this->applyDealFilters($base, $request, [
            'exclude_statuses' => ['Junk'],
        ]);

        $stats = $this->statsFromQuery(clone $base);
        $data = $base->paginate(19)->appends($request->query());

        return view('admin.deal.customer_view', array_merge([
            'data' => $data,
            'pageTitle' => 'ALLE AUFTRÄGE',
            'routeType' => 'all',
            'dealWorkflowStages' => $this->dealWorkflowSubStages(true),
            'dealWorkflowStageKeys' => $this->dealWorkflowStageKeys(),
            'dealWorkflowLabelMap' => $this->dealWorkflowLabelMap(),
            'dealWorkflowColorMap' => $this->dealWorkflowColorMap(),
        ], $stats, $this->sidebarData()));
    }

    public function junk_list(Request $request)
    {
        $base = $this->baseDealQuery(false);
        $base = $this->applyDealFilters($base, $request, [
            'force_status' => 'Junk',
            'exclude_statuses' => ['complete'],
        ]);

        $stats = $this->statsFromQuery(clone $base);
        $data = $base->paginate(19)->appends($request->query());

        return view('admin.deal.customer_view', array_merge([
            'data' => $data,
            'pageTitle' => 'JUNK AUFTRÄGE',
            'routeType' => 'junk',
            'dealWorkflowStages' => $this->dealWorkflowSubStages(true),
            'dealWorkflowStageKeys' => $this->dealWorkflowStageKeys(),
            'dealWorkflowLabelMap' => $this->dealWorkflowLabelMap(),
            'dealWorkflowColorMap' => $this->dealWorkflowColorMap(),
        ], $stats, $this->sidebarData()));
    }

    public function delete_list(Request $request)
    {
        $base = $this->baseDealQuery(true);
        $base = $this->applyDealFilters($base, $request);

        $stats = $this->statsFromQuery(clone $base);
        $data = $base->paginate(19)->appends($request->query());

        return view('admin.deal.customer_view', array_merge([
            'data' => $data,
            'pageTitle' => 'GELÖSCHTE AUFTRÄGE',
            'routeType' => 'deleted',
            'dealWorkflowStages' => $this->dealWorkflowSubStages(true),
            'dealWorkflowStageKeys' => $this->dealWorkflowStageKeys(),
            'dealWorkflowLabelMap' => $this->dealWorkflowLabelMap(),
            'dealWorkflowColorMap' => $this->dealWorkflowColorMap(),
        ], $stats, $this->sidebarData()));
    }

    public function loadKanbanColumn($status, Request $request)
    {
        $statusKey = $this->normalizeDealWorkflowStatus($status);

        $query = $this->baseDealQuery(false)
            ->whereNotIn('deals.status', ['complete', 'Junk']);

        // Load old rows into the correct shared Auftrag sub-stage column as well.
        $matchingStatuses = collect(['open', 'confirm', 'inconfirm', 'pause', 'cancel'])
            ->filter(fn($legacy) => $this->normalizeDealWorkflowStatus($legacy) === $statusKey)
            ->push($statusKey)
            ->unique()
            ->values()
            ->all();

        $query->whereIn('deals.status', $matchingStatuses);

        if ($request->filled('product_id')) {
            $query->where('deals.product_id', $request->product_id);
        }

        if ($request->filled('employee_id')) {
            $employeeId = (int) $request->employee_id;

            $query->where(function ($q) use ($employeeId) {
                $q->where('deals.employee_id', $employeeId)
                    ->orWhere('deals.checked_by', $employeeId)
                    ->orWhere('deals.reviewer_id', $employeeId);
            });
        }

        $data = $query
            ->orderByRaw('latest_change_at desc')
            ->orderBy('deals.id', 'desc')
            ->get();

        $dealWorkflowStages = $this->dealWorkflowSubStages(true);
        $dealWorkflowLabelMap = $this->dealWorkflowLabelMap();
        $dealWorkflowColorMap = $this->dealWorkflowColorMap();

        return view('admin.deal.partials.kanban_column', compact(
            'data',
            'status',
            'statusKey',
            'dealWorkflowStages',
            'dealWorkflowLabelMap',
            'dealWorkflowColorMap'
        ))->render();
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer', 'exists:deals,id'],
            'status' => ['required', 'string', 'max:120'],
            'reason' => ['required', 'string', 'max:2000'],
        ], [
            'reason.required' => 'Bitte geben Sie einen Grund für die Statusänderung ein.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler: ' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $requestedStatus = trim((string) $request->input('status'));
        $statusKey = $this->normalizeDealWorkflowStatus($requestedStatus);

        $allowedSpecial = ['complete', 'Junk'];
        $allowedWorkflow = $this->dealWorkflowStageKeys();

        if (!in_array($statusKey, $allowedWorkflow, true) && !in_array($statusKey, $allowedSpecial, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Dieser Auftrag-Status ist nicht in den Kanban-Unterphasen konfiguriert.',
                'requested_status' => $requestedStatus,
                'normalized_status' => $statusKey,
                'allowed_statuses' => $allowedWorkflow,
            ], 422);
        }

        DB::beginTransaction();

        try {
            /** @var Deal $deal */
            $deal = Deal::query()->lockForUpdate()->findOrFail((int) $request->input('id'));

            $oldStatus = $deal->status;
            $reason = trim((string) $request->input('reason', ''));

            $deal->status = $statusKey;
            $deal->updated_at = now();
            $deal->save();

            if (!in_array($statusKey, $allowedSpecial, true)) {
                $this->syncDealWorkflowTargets($deal, $statusKey);
            }

            if ($reason !== '' && class_exists(\App\Models\DealNote::class)) {
                \App\Models\DealNote::create([
                    'deal_id' => $deal->id,
                    'customer_id' => $deal->customer_id,
                    'alternative_id' => $deal->alternative_id,
                    'product_id' => $deal->product_id,
                    'description' => 'Status geändert: ' . ($this->dealWorkflowLabelMap()[$oldStatus] ?? $oldStatus) . ' → ' . ($this->dealWorkflowLabelMap()[$statusKey] ?? $statusKey) . "\n\nGrund: " . $reason,
                    'created_by' => auth()->user()->name,
                    'updated_by' => auth()->user()->name,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Auftrag-Unterphase wurde aktualisiert.',
                'old_status' => $oldStatus,
                'status' => $statusKey,
                'label' => $this->dealWorkflowLabelMap()[$statusKey] ?? $statusKey,
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Status konnte nicht aktualisiert werden: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function history(Deal $deal, Request $request)
    {
        $limit = max(10, min(100, (int) $request->input('limit', 80)));
        $items = $this->buildDealHistoryItems($deal, $limit);

        return response()->json([
            'success' => true,
            'deal_id' => $deal->id,
            'count' => count($items),
            'html' => $this->renderDealHistoryHtml($items),
            'items' => $items,
        ]);
    }

    protected function buildDealHistoryItems(Deal $deal, int $limit = 80): array
    {
        $items = [];
        $employeeIds = collect([
            $deal->employee_id ?? null,
            $deal->checked_by ?? null,
            $deal->reviewer_id ?? null,
            $deal->created_by ?? null,
            $deal->updated_by ?? null,
        ])->filter()->map(fn($id) => (int) $id)->unique()->values();

        if (Schema::hasTable('deal_notes')) {
            $employeeIds = $employeeIds->merge(
                DB::table('deal_notes')
                    ->where('deal_id', $deal->id)
                    ->whereNull('deleted_at')
                    ->pluck('created_by')
                    ->filter()
                    ->map(fn($id) => (int) $id)
            );
        }

        if (Schema::hasTable('deal_measurement_histories') && Schema::hasTable('deal_measurements')) {
            $measurementIds = DB::table('deal_measurements')
                ->where('deal_id', $deal->id)
                ->whereNull('deleted_at')
                ->pluck('id');

            if ($measurementIds->isNotEmpty()) {
                $employeeIds = $employeeIds->merge(
                    DB::table('deal_measurement_histories')
                        ->whereIn('deal_measurement_id', $measurementIds)
                        ->pluck('created_by')
                        ->filter()
                        ->map(fn($id) => (int) $id)
                );
            }
        }

        $employeeMap = Employee::query()
            ->whereIn('id', $employeeIds->filter()->unique()->values()->all())
            ->get(['id', 'name', 'lastname'])
            ->mapWithKeys(function ($employee) {
                return [
                    (string) $employee->id => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                ];
            })
            ->all();

        $employeeName = function ($employeeId) use ($employeeMap) {
            $key = (string) $employeeId;
            return $employeeMap[$key] ?? ($employeeId ? ('Mitarbeiter #' . $employeeId) : 'System');
        };

        $push = function (?string $at, string $type, string $title, string $text = '', $employeeId = null, array $meta = []) use (&$items, $employeeName) {
            if (!$at) {
                return;
            }

            $items[] = [
                'at' => Carbon::parse($at)->toDateTimeString(),
                'type' => $type,
                'title' => $title,
                'text' => $text,
                'employee' => $employeeName($employeeId),
                'meta' => $meta,
            ];
        };

        $push($deal->created_at?->toDateTimeString(), 'auftrag', 'Auftrag erstellt', 'Auftrag wurde angelegt.', $deal->created_by ?? $deal->employee_id, [
            'status' => $deal->status,
            'order_number' => $deal->order_number,
        ]);

        $push($deal->updated_at?->toDateTimeString(), 'auftrag', 'Auftrag aktualisiert', 'Auftrag-Stammdaten wurden geändert.', $deal->updated_by ?? null, [
            'status' => $deal->status,
        ]);

        if (Schema::hasTable('deal_notes')) {
            DB::table('deal_notes')
                ->where('deal_id', $deal->id)
                ->whereNull('deleted_at')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get()
                ->each(function ($note) use ($push) {
                    $description = trim((string) ($note->description ?? ''));
                    $descriptionLower = mb_strtolower($description);
                    $isStatus = str_contains($descriptionLower, 'status geändert')
                        || str_contains($descriptionLower, 'status geaendert')
                        || str_contains($descriptionLower, 'unterphase wurde aktualisiert');

                    $reason = null;
                    if ($isStatus && preg_match('/(?:Grund|Begründung|Begruendung)\s*:\s*(.+)$/isu', $description, $matches)) {
                        $reason = trim((string) $matches[1]);
                    }

                    $push(
                        $note->created_at ?? $note->updated_at,
                        $isStatus ? 'status' : 'notiz',
                        $isStatus ? 'Status geändert' : 'Notiz erstellt',
                        $description,
                        $note->created_by ?? null,
                        [
                            'note_id' => $note->id,
                            'reason' => $reason,
                            'is_status_change' => $isStatus,
                        ]
                    );
                });
        }

        // deal_invoices stillgelegt (invoices = führende Schiene) — Rechnungs-Timeline aus der Alt-Schiene entfernt.
        // (Timeline aus invoices ist ein separater Ausbau, nicht Teil des Rückbaus.)

        if (Schema::hasTable('images')) {
            Image::query()
                ->where('customer_id', $deal->customer_id)
                ->whereIn('status', ['order', 'deal'])
                ->when($deal->alternative_id, fn($q) => $q->where(function ($qq) use ($deal) {
                    $qq->where('alternative_id', $deal->alternative_id)->orWhereNull('alternative_id');
                }))
                ->when($deal->product_id, fn($q) => $q->where(function ($qq) use ($deal) {
                    $qq->where('article_group', $deal->product_id)->orWhereNull('article_group')->orWhere('article_group', 0);
                }))
                ->whereNull('deleted_at')
                ->latest('updated_at')
                ->limit($limit)
                ->get()
                ->each(function ($image) use ($push) {
                    $push(
                        $image->updated_at?->toDateTimeString() ?? $image->created_at?->toDateTimeString(),
                        'dokument',
                        'Dokument/Bild aktualisiert',
                        trim(($image->image_name ?: 'Datei') . ' · ' . ($image->stage ?? 'Dokument')),
                        $image->update_by ?? $image->created_by ?? null,
                        ['image_id' => $image->id]
                    );
                });
        }

        if (Schema::hasTable('offer_folder_attachments')) {
            $folderId = $this->resolveDealFolderId($deal);
            $offerId = $deal->offer_id;

            OfferFolderAttachment::query()
                ->whereNull('deleted_at')
                ->where(function ($q) use ($folderId, $offerId) {
                    if ($folderId) {
                        $q->orWhere('offer_folder_id', $folderId);
                    }
                    if ($offerId) {
                        $q->orWhere('offer_id', $offerId);
                    }
                })
                ->latest('updated_at')
                ->limit($limit)
                ->get()
                ->each(function ($attachment) use ($push) {
                    $push(
                        $attachment->updated_at?->toDateTimeString() ?? $attachment->created_at?->toDateTimeString(),
                        'dokument',
                        'Ordner-Dokument aktualisiert',
                        $attachment->title ?: $attachment->original_name ?: $attachment->file_name ?: 'Datei',
                        $attachment->updated_by ?? $attachment->created_by ?? null,
                        ['attachment_id' => $attachment->id]
                    );
                });
        }

        if (Schema::hasTable('deal_measurements')) {
            $measurements = DB::table('deal_measurements')
                ->where('deal_id', $deal->id)
                ->whereNull('deleted_at')
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get();

            $measurementIds = $measurements->pluck('id')->filter()->values();

            $measurements->each(function ($measurement) use ($push) {
                $push(
                    $measurement->updated_at ?? $measurement->created_at,
                    'feinaufmass',
                    'Feinaufmaß aktualisiert',
                    trim(($measurement->measurement_no ?? 'Feinaufmaß') . ' · ' . ($measurement->status ?? '')),
                    $measurement->updated_by ?? $measurement->created_by ?? $measurement->sent_by ?? null,
                    ['measurement_id' => $measurement->id]
                );

                if (!empty($measurement->materials_saved_at)) {
                    $push(
                        $measurement->materials_saved_at,
                        'materialliste',
                        'Materialliste gespeichert',
                        'Materialien: ' . (int) ($measurement->materials_approved_count ?? 0) . '/' . (int) ($measurement->materials_total_count ?? 0) . ' geprüft',
                        $measurement->updated_by ?? $measurement->created_by ?? null,
                        ['measurement_id' => $measurement->id]
                    );
                }
            });

            if ($measurementIds->isNotEmpty() && Schema::hasTable('deal_measurement_details')) {
                DB::table('deal_measurement_details')
                    ->whereIn('deal_measurement_id', $measurementIds)
                    ->orderByDesc('updated_at')
                    ->limit($limit)
                    ->get()
                    ->each(function ($detail) use ($push) {
                        $push(
                            $detail->saved_at ?? $detail->updated_at ?? $detail->created_at,
                            'feinaufmass',
                            'Feinaufmaß-Details gespeichert',
                            'Formular/Objektdaten wurden gespeichert.',
                            $detail->updated_by ?? null,
                            ['measurement_detail_id' => $detail->id]
                        );
                    });
            }

            if ($measurementIds->isNotEmpty() && Schema::hasTable('deal_measurement_histories')) {
                DB::table('deal_measurement_histories')
                    ->whereIn('deal_measurement_id', $measurementIds)
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get()
                    ->each(function ($history) use ($push) {
                        $text = trim(($history->section ? $history->section . ' · ' : '') . ($history->field ? $history->field . ': ' : '') . ($history->action ?? 'Änderung'));
                        $push(
                            $history->created_at,
                            'feinaufmass',
                            'Feinaufmaß-Historie',
                            $text,
                            $history->created_by ?? null,
                            ['measurement_history_id' => $history->id]
                        );
                    });
            }
        }

        if (Schema::hasTable('planner_plans')) {
            DB::table('planner_plans')
                ->where(function ($q) use ($deal) {
                    $q->where('customer_id', $deal->customer_id);
                    $q->whereRaw("JSON_EXTRACT(meta, '$.deal_id') = ?", [(string) $deal->id]);
                })
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get()
                ->each(function ($plan) use ($push) {
                    $push(
                        $plan->updated_at ?? $plan->created_at,
                        'planung',
                        'Projektplanung aktualisiert',
                        trim(($plan->title ?? 'Planung') . ' · ' . ($plan->status ?? '')),
                        $plan->created_by ?? null,
                        ['plan_id' => $plan->id]
                    );
                });
        }

        return collect($items)
            ->sortByDesc('at')
            ->take($limit)
            ->values()
            ->all();
    }

    protected function renderDealHistoryHtml(array $items): string
    {
        if (empty($items)) {
            return '<div class="deal-history-empty">Keine Historie gefunden.</div>';
        }

        $iconMap = [
            'status' => 'refresh-cw',
            'auftrag' => 'briefcase',
            'notiz' => 'message-square',
            'dokument' => 'image',
            'feinaufmass' => 'clipboard',
            'materialliste' => 'package',
            'rechnung' => 'file-text',
            'planung' => 'calendar',
        ];

        return collect($items)->map(function ($item) use ($iconMap) {
            $type = e($item['type'] ?? 'auftrag');
            $icon = e($iconMap[$item['type'] ?? 'auftrag'] ?? 'clock');
            $title = e($item['title'] ?? 'Änderung');
            $rawText = (string) ($item['text'] ?? '');
            $meta = is_array($item['meta'] ?? null) ? $item['meta'] : [];
            $reason = trim((string) ($meta['reason'] ?? ''));

            if ($reason === '' && preg_match('/(?:Grund|Begründung|Begruendung)\s*:\s*(.+)$/isu', $rawText, $matches)) {
                $reason = trim((string) $matches[1]);
            }

            $text = nl2br(e($rawText));
            $reasonHtml = $reason !== ''
                ? '<div class="deal-history-reason"><strong>Grund:</strong> ' . e($reason) . '</div>'
                : '';
            $employee = e($item['employee'] ?? 'System');
            $date = !empty($item['at']) ? Carbon::parse($item['at'])->format('d.m.Y H:i') : '—';
            $relative = !empty($item['at']) ? Carbon::parse($item['at'])->diffForHumans() : '';

            return <<<HTML
                <div class="deal-history-item type-{$type}">
                    <div class="deal-history-icon"><i class="feather icon-{$icon}"></i></div>
                    <div class="deal-history-content">
                        <div class="deal-history-top">
                            <strong>{$title}</strong>
                            <span>{$date}</span>
                        </div>
                        <div class="deal-history-text">{$text}</div>
                        {$reasonHtml}
                        <div class="deal-history-meta">{$employee} · {$relative}</div>
                    </div>
                </div>
            HTML;
        })->implode('');
    }

    public function getDealId(Request $request)
    {
        $deal = \App\Models\Deal::where([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'alternative_id' => $request->alternative_id,
            'service' => $request->service,
        ])->first();

        if ($deal) {
            return response()->json(['success' => true, 'deal_id' => $deal->id]);
        }

        return response()->json(['success' => false]);
    }

    public function loadCustomerNotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deal_id' => ['required', 'integer', 'exists:deals,id'],
            'customer_id' => ['nullable', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'search' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ungültige Anfrage.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employeeId = auth()->user()->name;
        $search = trim((string) $request->input('search', ''));

        $notes = DealNote::query()
            ->where('deal_id', $request->deal_id)
            ->when($request->filled('customer_id'), fn($q) => $q->where('customer_id', $request->customer_id))
            ->when($request->filled('alternative_id'), fn($q) => $q->where('alternative_id', $request->alternative_id))
            ->when($request->filled('product_id'), fn($q) => $q->where('product_id', $request->product_id))
            ->when($search !== '', function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $groupedNotes = $notes->groupBy('parent_id');

        return view('admin.deal.partials.notes-list', compact('groupedNotes', 'employeeId'))->render();
    }

    public function storeCustomerNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deal_id' => ['required', 'integer', 'exists:deals,id'],
            'customer_id' => ['nullable', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'parent_id' => ['nullable', 'integer', 'exists:deal_notes,id'],
            'description' => ['required', 'string', 'max:10000'],
        ], [
            'deal_id.required' => 'Die Deal-ID ist erforderlich.',
            'description.required' => 'Bitte eine Notiz eingeben.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deal = Deal::findOrFail($request->deal_id);

        $note = DealNote::create([
            'deal_id' => $deal->id,
            'customer_id' => $request->customer_id ?? $deal->customer_id,
            'alternative_id' => $request->alternative_id ?? $deal->alternative_id,
            'product_id' => $request->product_id ?? $deal->product_id,
            'parent_id' => $request->parent_id,
            'description' => trim($request->description),
            'created_by' => auth()->user()->name,
            'updated_by' => auth()->user()->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notiz wurde gespeichert.',
            'note_id' => $note->id,
        ]);
    }

    public function updateCustomerNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note_id' => ['required', 'integer', 'exists:deal_notes,id'],
            'description' => ['required', 'string', 'max:10000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employeeId = (string) auth()->user()->name;

        $note = DealNote::findOrFail($request->note_id);

        if ((string) $note->created_by !== $employeeId) {
            return response()->json([
                'success' => false,
                'message' => 'Sie dürfen diese Notiz nicht bearbeiten.',
            ], 403);
        }

        $note->update([
            'description' => trim($request->description),
            'updated_by' => $employeeId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notiz wurde aktualisiert.',
        ]);
    }

    public function deleteCustomerNote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note_id' => ['required', 'integer', 'exists:deal_notes,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ungültige Anfrage.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employeeId = (string) auth()->user()->name;

        $note = DealNote::findOrFail($request->note_id);

        if ((string) $note->created_by !== $employeeId) {
            return response()->json([
                'success' => false,
                'message' => 'Sie dürfen diese Notiz nicht löschen.',
            ], 403);
        }

        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notiz wurde gelöscht.',
        ]);
    }

    protected function resolveDealFolderId(?Deal $deal): ?int
    {
        if (!$deal) {
            return null;
        }

        if (!empty($deal->offer_folder_id)) {
            return (int) $deal->offer_folder_id;
        }

        if (!empty($deal->offer_id)) {
            return DB::table('offer_details')
                ->where('offer_id', $deal->offer_id)
                ->orderByDesc('id')
                ->value('offer_folder_id');
        }

        return null;
    }

    protected function getDealFileCount(?Deal $deal, ?int $customerId = null, ?int $alternativeId = null, ?int $productId = null): int
    {
        $folderId = $this->resolveDealFolderId($deal);
        $offerId = $deal?->offer_id;

        $imageCount = Image::query()
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->whereIn('status', ['order', 'deal'])
            ->when($alternativeId, fn($q) => $q->where(function ($qq) use ($alternativeId) {
                $qq->where('alternative_id', $alternativeId)
                    ->orWhereNull('alternative_id');
            }))
            ->when($productId, fn($q) => $q->where(function ($qq) use ($productId) {
                $qq->where('article_group', $productId)
                    ->orWhereNull('article_group')
                    ->orWhere('article_group', 0);
            }))
            ->whereNull('deleted_at')
            ->count();

        $attachmentCount = OfferFolderAttachment::query()
            ->whereNull('deleted_at')
            ->where(function ($q) use ($folderId, $offerId) {
                if ($folderId) {
                    $q->orWhere('offer_folder_id', $folderId);
                }

                if ($offerId) {
                    $q->orWhere('offer_id', $offerId);
                }
            })
            ->count();

        return $imageCount + $attachmentCount;
    }

    protected function normalizeDealImageFile(Image $image): object
    {
        $path = 'uploads/customers/' . $image->image;
        $exists = Storage::disk('local')->exists($path);

        $extension = strtolower((string) ($image->file_type ?: pathinfo($image->image, PATHINFO_EXTENSION)));
        $isPreviewableImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
        $isPdf = $extension === 'pdf';

        return (object) [
            'id' => $image->id,
            'source' => 'image',
            'image_name' => $image->image_name ?: pathinfo($image->image, PATHINFO_FILENAME),
            'file_type' => $extension,
            'stage' => $image->stage,
            'status' => $image->status,
            'url' => route('deal.file.preview.source', ['source' => 'image', 'id' => $image->id]),
            'download_url' => route('deal.file.download.source', ['source' => 'image', 'id' => $image->id]),
            'exists' => $exists,
            'is_image' => $isPreviewableImage,
            'is_pdf' => $isPdf,
        ];
    }

    protected function normalizeOfferFolderAttachmentFile(OfferFolderAttachment $attachment): object
    {
        $extension = strtolower((string) ($attachment->extension ?: pathinfo($attachment->file_name ?: $attachment->file_path, PATHINFO_EXTENSION)));
        $mimeType = strtolower((string) $attachment->mime_type);

        $isPreviewableImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)
            || str_starts_with($mimeType, 'image/');

        $isPdf = $extension === 'pdf' || $mimeType === 'application/pdf';

        return (object) [
            'id' => $attachment->id,
            'source' => 'attachment',
            'image_name' => $attachment->title ?: $attachment->original_name ?: $attachment->file_name ?: 'Datei',
            'file_type' => $extension ?: ($attachment->file_type ?: 'file'),
            'stage' => $attachment->document_type ?: 'offer_folder',
            'status' => 'offer_folder',
            'url' => route('deal.file.preview.source', ['source' => 'attachment', 'id' => $attachment->id]),
            'download_url' => route('deal.file.download.source', ['source' => 'attachment', 'id' => $attachment->id]),
            'exists' => true,
            'is_image' => $isPreviewableImage,
            'is_pdf' => $isPdf,
        ];
    }

    public function uploadCustomerFile(Request $request)
    {
        Log::info('Deal upload request received', $request->all());

        try {
            $request->validate([
                'file' => 'required',
                'file.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:20480',
                'deal_id' => 'nullable|integer|exists:deals,id',
                'customer_id' => 'required|exists:new_leads,id',
                'alternative_id' => 'nullable|integer|exists:lead_alternative_adds,id',
                'product_id' => 'nullable|integer|exists:article_groups,id',
                'stage_id' => 'required|string|max:255',
                'offer_folder_id' => 'nullable|integer',
            ]);

            $deal = $request->filled('deal_id')
                ? Deal::find($request->deal_id)
                : null;

            $files = is_array($request->file('file'))
                ? $request->file('file')
                : [$request->file('file')];

            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                $safeOriginalName = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $originalName);
                $extension = strtolower($file->getClientOriginalExtension());

                $fileName = time() . '_' . uniqid() . '_' . $safeOriginalName;

                $storedPath = $file->storeAs('uploads/customers', $fileName);

                if (!$storedPath || !Storage::disk('local')->exists($storedPath)) {
                    Log::error('Deal upload file store failed', [
                        'original' => $originalName,
                        'stored_as' => $fileName,
                    ]);

                    continue;
                }

                Image::create([
                    'customer_id' => $request->customer_id,
                    'alternative_id' => $request->alternative_id,
                    'article_group' => $request->product_id ?? null,
                    'stage' => $request->stage_id,
                    'image_name' => pathinfo($originalName, PATHINFO_FILENAME),
                    'image' => $fileName,
                    'file_type' => $extension,
                    'status' => 'order',
                    'created_by' => auth()->user()->name ?? null,
                    'update_by' => auth()->user()->name ?? null,
                ]);
            }

            $count = $this->getDealFileCount(
                $deal,
                (int) $request->customer_id,
                $request->alternative_id ? (int) $request->alternative_id : null,
                $request->product_id ? (int) $request->product_id : null
            );

            return response()->json([
                'success' => true,
                'message' => 'Datei(en) erfolgreich hochgeladen.',
                'files_count' => $count,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Deal upload validation error', [
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validierung fehlgeschlagen.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Deal upload failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Datei konnte nicht hochgeladen werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function loadCustomerFiles(Request $request)
    {
        try {
            $request->validate([
                'deal_id' => 'nullable|integer|exists:deals,id',
                'customer_id' => 'required|exists:new_leads,id',
                'alternative_id' => 'nullable|integer',
                'product_id' => 'nullable|integer',
                'stage' => 'nullable|string',
                'offer_folder_id' => 'nullable|integer',
            ]);

            $deal = $request->filled('deal_id')
                ? Deal::find($request->deal_id)
                : null;

            $folderId = $request->filled('offer_folder_id')
                ? (int) $request->offer_folder_id
                : $this->resolveDealFolderId($deal);

            $offerId = $deal?->offer_id;
            $stage = trim((string) $request->input('stage', ''));
            $productId = $request->input('product_id');

            $images = Image::query()
                ->where('customer_id', $request->customer_id)
                ->whereIn('status', ['order', 'deal'])
                ->when($request->filled('alternative_id'), function ($q) use ($request) {
                    $q->where(function ($qq) use ($request) {
                        $qq->where('alternative_id', $request->alternative_id)
                            ->orWhereNull('alternative_id');
                    });
                })
                ->when(!empty($productId), function ($q) use ($productId) {
                    $q->where(function ($qq) use ($productId) {
                        $qq->where('article_group', $productId)
                            ->orWhereNull('article_group')
                            ->orWhere('article_group', 0);
                    });
                })
                ->when($stage !== '', function ($q) use ($stage) {
                    $q->where('stage', $stage);
                })
                ->whereNull('deleted_at')
                ->latest()
                ->get()
                ->filter(fn($image) => !empty($image->id) && !empty($image->image))
                ->map(fn($image) => $this->normalizeDealImageFile($image));

            $attachments = OfferFolderAttachment::query()
                ->whereNull('deleted_at')
                ->where(function ($q) use ($folderId, $offerId) {
                    if ($folderId) {
                        $q->orWhere('offer_folder_id', $folderId);
                    }

                    if ($offerId) {
                        $q->orWhere('offer_id', $offerId);
                    }
                })
                ->when($stage !== '', function ($q) use ($stage) {
                    $q->where(function ($qq) use ($stage) {
                        $qq->where('document_type', $stage)
                            ->orWhere('file_type', $stage);
                    });
                })
                ->orderBy('sort_order')
                ->latest()
                ->get()
                ->map(fn($attachment) => $this->normalizeOfferFolderAttachmentFile($attachment));

            $files = $attachments
                ->merge($images)
                ->values();

            $html = view('admin.deal.partials.gallary', [
                'images' => $files,
            ])->render();

            if ($request->boolean('json')) {
                return response()->json([
                    'success' => true,
                    'html' => $html,
                    'files_count' => $files->count(),
                ]);
            }

            return $html;
        } catch (\Throwable $e) {
            Log::error('Load deal customer files failed', [
                'message' => $e->getMessage(),
            ]);

            if ($request->boolean('json')) {
                return response()->json([
                    'success' => false,
                    'html' => '<div class="text-danger p-2">Dateien konnten nicht geladen werden.</div>',
                    'files_count' => 0,
                ], 500);
            }

            return '<div class="text-danger p-2">Dateien konnten nicht geladen werden.</div>';
        }
    }
    public function renameCustomerFile(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
                'source' => 'nullable|string|in:image,attachment',
                'new_name' => 'required|string|max:255',
            ]);

            $source = $request->input('source', 'image');

            if ($source === 'attachment') {
                $attachment = OfferFolderAttachment::findOrFail($request->id);
                $attachment->title = trim($request->new_name);
                $attachment->save();
            } else {
                $image = Image::where('id', $request->id)
                    ->whereIn('status', ['order', 'deal'])
                    ->firstOrFail();

                $image->image_name = trim($request->new_name);
                $image->update_by = auth()->user()->name ?? null;
                $image->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Dateiname wurde aktualisiert.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validierung fehlgeschlagen.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Rename deal file failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Datei konnte nicht umbenannt werden.',
            ], 500);
        }
    }
    public function deleteCustomerFile(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
                'source' => 'nullable|string|in:image,attachment',
            ]);

            $source = $request->input('source', 'image');

            if ($source === 'attachment') {
                $attachment = OfferFolderAttachment::findOrFail($request->id);
                $attachment->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Datei wurde erfolgreich gelöscht.',
                ]);
            }

            $image = Image::where('id', $request->id)
                ->whereIn('status', ['order', 'deal'])
                ->firstOrFail();

            $path = 'uploads/customers/' . $image->image;

            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }

            $image->update_by = auth()->user()->name ?? null;
            $image->save();
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Datei wurde erfolgreich gelöscht.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validierung fehlgeschlagen.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Delete deal file failed', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Datei konnte nicht gelöscht werden.',
            ], 500);
        }
    }
    public function previewCustomerFile($source, $id = null)
    {
        if ($id === null) {
            $id = $source;
            $source = 'image';
        }

        if ($source === 'attachment') {
            $attachment = OfferFolderAttachment::findOrFail($id);

            if (!empty($attachment->file_url)) {
                return redirect()->away($attachment->file_url);
            }

            $path = $attachment->file_path;

            if (!$path || !Storage::disk('local')->exists($path)) {
                abort(404, 'Datei nicht gefunden.');
            }

            $mime = $attachment->mime_type ?: Storage::disk('local')->mimeType($path);
            $filename = $attachment->original_name ?: $attachment->file_name ?: 'datei';

            return response(Storage::disk('local')->get($path), Response::HTTP_OK)
                ->header('Content-Type', $mime)
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
        }

        $image = Image::where('id', $id)
            ->whereIn('status', ['order', 'deal'])
            ->firstOrFail();

        $path = 'uploads/customers/' . $image->image;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Datei nicht gefunden.');
        }

        $mime = Storage::disk('local')->mimeType($path);
        $content = Storage::disk('local')->get($path);

        return response($content, Response::HTTP_OK)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . $image->image . '"');
    }

    public function downloadCustomerFile($source, $id = null)
    {
        if ($id === null) {
            $id = $source;
            $source = 'image';
        }

        if ($source === 'attachment') {
            $attachment = OfferFolderAttachment::findOrFail($id);

            if (!empty($attachment->file_url)) {
                return redirect()->away($attachment->file_url);
            }

            $path = $attachment->file_path;

            if (!$path || !Storage::disk('local')->exists($path)) {
                abort(404, 'Datei nicht gefunden.');
            }

            return Storage::disk('local')->download(
                $path,
                $attachment->original_name ?: $attachment->file_name ?: 'datei'
            );
        }

        $image = Image::where('id', $id)
            ->whereIn('status', ['order', 'deal'])
            ->firstOrFail();

        $path = 'uploads/customers/' . $image->image;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Datei nicht gefunden.');
        }

        return Storage::disk('local')->download(
            $path,
            ($image->image_name ?: 'datei') . '.' . ($image->file_type ?: pathinfo($image->image, PATHINFO_EXTENSION))
        );
    }

    public function loadDeliveryNotes(Deal $deal)
    {
        $deliveryNotes = \App\Models\DeliveryNote::query()
            ->where('deal_id', $deal->id)
            ->with(['branch', 'handoverEmployee'])
            ->latest()
            ->get();

        return view('admin.deal.partials.delivery-notes-modal-list', [
            'deal' => $deal,
            'deliveryNotes' => $deliveryNotes,
        ])->render();
    }

    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:deals,id'],
            'action' => ['required', 'string', 'in:delete,junk,unjunk,restore,status'],
            'status' => ['nullable', 'required_if:action,status', 'string', 'in:open,confirm,inconfirm,complete,Junk'],
        ], [
            'ids.required' => 'Bitte mindestens einen Auftrag auswählen.',
            'ids.min' => 'Bitte mindestens einen Auftrag auswählen.',
            'action.required' => 'Bitte eine Aktion auswählen.',
            'status.required_if' => 'Bitte einen Status auswählen.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $ids = collect($request->input('ids', []))
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Keine gültigen Aufträge ausgewählt.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $action = $request->input('action');
            $affected = 0;

            if ($action === 'delete') {
                $affected = Deal::query()
                    ->whereIn('id', $ids)
                    ->delete();
            }

            if ($action === 'junk') {
                $affected = Deal::query()
                    ->whereIn('id', $ids)
                    ->update([
                        'status' => 'Junk',
                        'updated_at' => now(),
                    ]);
            }

            if ($action === 'unjunk') {
                $affected = Deal::query()
                    ->whereIn('id', $ids)
                    ->where('status', 'Junk')
                    ->update([
                        'status' => 'open',
                        'updated_at' => now(),
                    ]);
            }

            if ($action === 'restore') {
                $affected = Deal::withTrashed()
                    ->whereIn('id', $ids)
                    ->restore();
            }

            if ($action === 'status') {
                $affected = Deal::query()
                    ->whereIn('id', $ids)
                    ->update([
                        'status' => $request->input('status'),
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $affected . ' Auftrag/Aufträge wurden aktualisiert.',
                'affected' => $affected,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Deal bulk action failed', [
                'message' => $e->getMessage(),
                'ids' => $ids,
                'action' => $request->input('action'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bulk-Aktion konnte nicht ausgeführt werden.',
            ], 500);
        }
    }

    public function planningPreview(Deal $deal)
    {
        if (!$deal->offer_id || !$deal->offer_folder_id) {
            return response()->json([
                'success' => false,
                'message' => 'Dieser Auftrag hat kein Angebot oder keinen Angebotsordner.',
            ], 422);
        }

        $detail = OfferDetail::query()
            ->where('offer_id', $deal->offer_id)
            ->where('offer_folder_id', $deal->offer_folder_id)
            ->first();

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Angebotsdetails gefunden.',
            ], 404);
        }

        $sections = $detail->sections ?? [];

        return response()->json([
            'success' => true,
            'deal' => [
                'id' => $deal->id,
                'order_number' => $deal->order_number,
                'customer_id' => $deal->customer_id,
                'alternative_id' => $deal->alternative_id,
                'product_id' => $deal->product_id,
                'department_id' => $deal->department_id,
                'offer_id' => $deal->offer_id,
                'offer_folder_id' => $deal->offer_folder_id,
            ],
            'material_list' => $this->extractPlanningMaterialList($sections),
            'required_qualifications' => $this->extractPlanningRequiredQualifications($sections),
        ]);
    }

    public function planningCheck(Request $request, Deal $deal)
    {
        $validator = Validator::make($request->all(), [
            'planned_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte Datum, Startzeit und Endzeit korrekt eingeben.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$deal->offer_id || !$deal->offer_folder_id) {
            return response()->json([
                'success' => false,
                'message' => 'Dieser Auftrag kann nicht geplant werden.',
            ], 422);
        }

        $detail = OfferDetail::query()
            ->where('offer_id', $deal->offer_id)
            ->where('offer_folder_id', $deal->offer_folder_id)
            ->first();

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Angebotsdetails gefunden.',
            ], 404);
        }

        $required = $this->extractPlanningRequiredQualifications($detail->sections ?? []);

        $suggestions = $this->buildPlanningEmployeeSuggestions(
            $required,
            $request->planned_date,
            $request->start_time,
            $request->end_time,
            $deal->department_id
        );

        return response()->json([
            'success' => true,
            'required_qualifications' => $required,
            'suggestions' => $suggestions,
        ]);
    }

    public function planningStore(Request $request, Deal $deal)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'planned_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],

            'employees' => ['nullable', 'array'],
            'employees.*.employee_id' => ['required_with:employees', 'integer', 'exists:employees,id'],
            'employees.*.qualification_id' => ['nullable', 'integer'],
            'employees.*.qualification_name' => ['nullable', 'string'],
            'employees.*.employee_real_qualification_id' => ['nullable', 'integer'],
            'employees.*.employee_real_qualification_name' => ['nullable', 'string'],
            'employees.*.planned_hours' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$deal->offer_id || !$deal->offer_folder_id) {
            return response()->json([
                'success' => false,
                'message' => 'Projektplanung nicht möglich. Angebot oder Angebotsordner fehlt.',
            ], 422);
        }

        $detail = OfferDetail::query()
            ->where('offer_id', $deal->offer_id)
            ->where('offer_folder_id', $deal->offer_folder_id)
            ->firstOrFail();

        $sections = $detail->sections ?? [];

        $materialList = $this->extractPlanningMaterialList($sections);
        $requiredQualifications = $this->extractPlanningRequiredQualifications($sections);
        $projectId = $this->findPlanningLeadProductListId($deal);

        try {
            return DB::transaction(function () use ($request, $deal, $projectId, $materialList, $requiredQualifications) {
                $plan = PlannerPlan::create([
                    'account_id' => null,
                    'customer_id' => $deal->customer_id,
                    'project_id' => $projectId,
                    'stage' => 'project',
                    'title' => $request->title,
                    'status' => 'draft',
                    'created_by' => auth()->user()->name ?? null,
                    'meta' => [
                        'deal_id' => $deal->id,
                        'offer_id' => $deal->offer_id,
                        'offer_folder_id' => $deal->offer_folder_id,
                        'alternative_id' => $deal->alternative_id,
                        'product_id' => $deal->product_id,
                        'department_id' => $deal->department_id,
                        'planned_date' => $request->planned_date,
                        'start_time' => $request->start_time,
                        'end_time' => $request->end_time,
                        'material_summary' => $materialList,
                        'qualification_summary' => $requiredQualifications,
                        'selected_employees' => $request->employees ?? [],
                        'source' => 'deal_project_planning',
                    ],
                ]);

                $plannedStart = Carbon::parse($request->planned_date . ' ' . $request->start_time);
                $currentStart = $plannedStart->copy();

                /*
                |--------------------------------------------------------------------------
                | Material item
                |--------------------------------------------------------------------------
                */
                $materialItem = PlannerItem::create([
                    'plan_id' => $plan->id,
                    'source_type' => 'offer_detail_material',
                    'source_id' => $deal->offer_id,
                    'title' => 'Materialbereitstellung',
                    'category' => 'material',
                    'description' => 'Automatisch aus Angebots-Materialliste erstellt.',
                    'duration_minutes' => 30,
                    'status' => 'todo',
                    'planned_start_at' => $currentStart->copy(),
                    'planned_end_at' => $currentStart->copy()->addMinutes(30),
                    'sort_order' => 1,
                    'meta' => [
                        'deal_id' => $deal->id,
                        'offer_id' => $deal->offer_id,
                        'offer_folder_id' => $deal->offer_folder_id,
                        'material_rows' => $materialList,
                        'type' => 'material_preparation',
                    ],
                ]);

                foreach ($materialList as $material) {
                    PlannerItemMaterial::create([
                        'planner_item_id' => $materialItem->id,
                        'master_set_id' => $material['master_set_id'] ?? null,
                        'name' => $material['name'] ?? 'Unbenannt',
                        'qty' => $material['qty_total'] ?? $material['qty'] ?? 0,
                        'unit_price' => $material['price'] ?? 0,
                        'total_price' => (float) ($material['qty_total'] ?? $material['qty'] ?? 0) * (float) ($material['price'] ?? 0),
                    ]);
                }

                $currentStart->addMinutes(30);

                /*
                |--------------------------------------------------------------------------
                | Labor items
                |--------------------------------------------------------------------------
                */
                $createdLaborItems = [];
                $previousItemId = $materialItem->id;
                $sortOrder = 2;

                foreach ($requiredQualifications as $qualification) {
                    $hours = (float) ($qualification['hours_total'] ?? $qualification['qty'] ?? 0);
                    $durationMinutes = max(30, (int) round($hours * 60));

                    $item = PlannerItem::create([
                        'plan_id' => $plan->id,
                        'source_type' => 'offer_detail_labor',
                        'source_id' => $deal->offer_id,
                        'title' => ($qualification['qualification_name'] ?? 'Qualifikation') . ' · Ausführung',
                        'category' => 'labor',
                        'description' => 'Automatisch aus Angebots-Arbeitsposition erstellt.',
                        'duration_minutes' => $durationMinutes,
                        'status' => 'todo',
                        'planned_start_at' => $currentStart->copy(),
                        'planned_end_at' => $currentStart->copy()->addMinutes($durationMinutes),
                        'sort_order' => $sortOrder,
                        'meta' => [
                            'deal_id' => $deal->id,
                            'offer_id' => $deal->offer_id,
                            'offer_folder_id' => $deal->offer_folder_id,
                            'qualification_id' => $qualification['qualification_id'] ?? null,
                            'qualification_name' => $qualification['qualification_name'] ?? null,
                            'hours_total' => $hours,
                            'unit' => $qualification['unit'] ?? 'Std.',
                            'ek' => $qualification['ek'] ?? null,
                            'rate' => $qualification['rate'] ?? null,
                            'total' => $qualification['total'] ?? null,
                            'type' => 'labor',
                        ],
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Dependency
                    |--------------------------------------------------------------------------
                    | Fits your current PlannerItemDependency model.
                    | If your DB has reason column, you can add it later.
                    */
                    PlannerItemDependency::create([
                        'planner_item_id' => $item->id,
                        'depends_on_item_id' => $previousItemId,
                    ]);

                    foreach (($request->employees ?? []) as $employeeRow) {
                        $requiredId = (string) ($qualification['qualification_id'] ?? '');
                        $employeeRequiredId = (string) ($employeeRow['qualification_id'] ?? '');

                        if ($requiredId !== '' && $requiredId === $employeeRequiredId) {
                            $role = $qualification['qualification_name'] ?? 'Mitarbeiter';

                            if (!empty($employeeRow['employee_real_qualification_name'])) {
                                $role .= ' / Qualifikation: ' . $employeeRow['employee_real_qualification_name'];
                            }

                            $item->employees()->syncWithoutDetaching([
                                $employeeRow['employee_id'] => [
                                    'role' => $role,
                                ],
                            ]);
                        }
                    }

                    $createdLaborItems[] = $item;

                    $previousItemId = $item->id;
                    $currentStart->addMinutes($durationMinutes);
                    $sortOrder++;
                }

                /*
                |--------------------------------------------------------------------------
                | Calendar appointments
                |--------------------------------------------------------------------------
                */
                foreach ($createdLaborItems as $createdItem) {
                    $appointment = MainAppointment::create([
                        'created_by' => auth()->user()->name ?? null,
                        'name' => $createdItem->title,
                        'note' => 'Automatisch aus Projektplanung erstellt.',
                        'start_date' => $createdItem->planned_start_at?->toDateString(),
                        'end_date' => $createdItem->planned_end_at?->toDateString(),
                        'start_time' => $createdItem->planned_start_at?->format('H:i:s'),
                        'end_time' => $createdItem->planned_end_at?->format('H:i:s'),
                        'customer_id' => $deal->customer_id,
                        'products' => [
                            [
                                'deal_id' => $deal->id,
                                'plan_id' => $plan->id,
                                'planner_item_id' => $createdItem->id,
                                'product_id' => $deal->product_id,
                                'offer_id' => $deal->offer_id,
                                'folder_id' => $deal->offer_folder_id,
                            ],
                        ],
                        'source' => 'planner_item',
                        'type' => 'project',
                        'status' => 'planned',
                        'planner_item_id' => $createdItem->id,
                    ]);

                    $employeeSync = [];

                    foreach ($createdItem->employees as $employee) {
                        $employeeSync[$employee->id] = [
                            'status' => 'planned',
                            'reason' => $createdItem->title,
                        ];
                    }

                    if (!empty($employeeSync)) {
                        $appointment->employees()->syncWithoutDetaching($employeeSync);
                    }
                }

                $deal->update([
                    'project_status' => 'planned',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Projektplanung wurde erstellt.',
                    'plan_id' => $plan->id,
                    'items_count' => 1 + count($createdLaborItems),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Deal project planning failed', [
                'deal_id' => $deal->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Projektplanung konnte nicht gespeichert werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function findPlanningLeadProductListId(Deal $deal): ?int
    {
        return LeadProductList::query()
            ->where('customer_id', $deal->customer_id)
            ->where('product_id', $deal->product_id)
            ->when($deal->alternative_id, fn($q) => $q->where('alternative_id', $deal->alternative_id))
            ->latest('id')
            ->value('id');
    }

    private function extractPlanningMaterialList(array $sections): array
    {
        $rows = [];

        foreach ($sections as $section) {
            foreach (($section['items'] ?? []) as $item) {
                $this->collectPlanningMaterialItem($rows, $item, $section['title'] ?? null, 0, null);
            }
        }

        return collect($rows)
            ->groupBy(fn($row) => ($row['master_set_id'] ?? 'x') . '|' . ($row['product_id'] ?? 'x') . '|' . ($row['component_id'] ?? 'x') . '|' . ($row['name'] ?? ''))
            ->map(function ($group) {
                $first = $group->first();

                $first['qty_total'] = $group->sum(fn($row) => (float) ($row['qty_total'] ?? $row['qty'] ?? 0));

                return $first;
            })
            ->values()
            ->all();
    }

    private function collectPlanningMaterialItem(array &$rows, array $item, ?string $sectionTitle, int $depth, ?int $parentMasterSetId): void
    {
        $kind = $item['kind'] ?? null;
        $itemType = $item['item_type'] ?? null;

        $isMasterSet = $itemType === 'master_set';
        $currentMasterSetId = $isMasterSet
            ? ($item['product_id'] ?? $item['productId'] ?? null)
            : $parentMasterSetId;

        if ($kind !== 'labor') {
            $qty = (float) ($item['qty'] ?? 1);
            $qtyTotal = (float) ($item['qty_total'] ?? $qty);

            $rows[] = [
                'section' => $sectionTitle,
                'depth' => $depth,
                'item_type' => $itemType,
                'master_set_id' => $currentMasterSetId,
                'product_id' => $item['product_id'] ?? $item['productId'] ?? null,
                'component_id' => $item['component_id'] ?? null,
                'article_no' => $item['article_no'] ?? $item['distributor_article_no'] ?? null,
                'name' => $item['name'] ?? 'Unbenannt',
                'qty' => $qty,
                'qty_total' => $qtyTotal,
                'unit' => $item['unit'] ?? $item['measure'] ?? 'Stk.',
                'supplier' => $item['supplier'] ?? $item['distributor_name'] ?? null,
                'order_status' => $item['order_status'] ?? null,
                'stock_allocation' => $item['stock_allocation'] ?? null,
                'price' => $item['price'] ?? 0,
                'ek' => $item['ek'] ?? null,
                'image' => $item['img'] ?? null,
            ];
        }

        foreach (($item['subItems'] ?? []) as $subItem) {
            $this->collectPlanningMaterialItem($rows, $subItem, $sectionTitle, $depth + 1, $currentMasterSetId);
        }
    }

    private function extractPlanningRequiredQualifications(array $sections): array
    {
        $rows = [];

        foreach ($sections as $section) {
            foreach (($section['items'] ?? []) as $item) {
                $this->collectPlanningLaborRows($rows, $item, $section['title'] ?? null);
            }
        }

        return collect($rows)
            ->groupBy(fn($row) => ($row['qualification_id'] ?? 'x') . '|' . ($row['qualification_name'] ?? ''))
            ->map(function ($group) {
                $first = $group->first();

                $first['qty'] = $group->sum(fn($row) => (float) ($row['qty'] ?? 0));
                $first['hours_total'] = $group->sum(fn($row) => (float) ($row['hours_total'] ?? $row['qty'] ?? 0));
                $first['total'] = $group->sum(fn($row) => (float) ($row['total'] ?? 0));

                return $first;
            })
            ->values()
            ->all();
    }

    private function collectPlanningLaborRows(array &$rows, array $item, ?string $sectionTitle): void
    {
        foreach (($item['labor_rows'] ?? []) as $laborRow) {
            $qty = (float) ($laborRow['qty'] ?? 0);

            $rows[] = [
                'section' => $sectionTitle,
                'qualification_id' => $laborRow['qualification_id'] ?? null,
                'qualification_name' => $laborRow['qualification_name'] ?? 'Unbekannt',
                'qty' => $qty,
                'unit' => $laborRow['unit'] ?? 'Std.',
                'ek' => $laborRow['ek'] ?? null,
                'rate' => $laborRow['rate'] ?? null,
                'total' => $laborRow['total'] ?? null,
                'hours_total' => $qty,
            ];
        }

        foreach (($item['subItems'] ?? []) as $subItem) {
            $this->collectPlanningLaborRows($rows, $subItem, $sectionTitle);
        }
    }

    private function buildPlanningEmployeeSuggestions(array $requiredQualifications, string $date, string $startTime, string $endTime, ?int $departmentId): array
    {
        $result = [];

        foreach ($requiredQualifications as $required) {
            $requiredQualificationId = $required['qualification_id'] ?? null;
            $allowedQualificationIds = $this->allowedPlanningQualificationIds($requiredQualificationId);

            $employees = Employee::query()
                ->select([
                    'employees.id',
                    'employees.name',
                    'employees.lastname',
                    'employees.image',
                    'employees.status',

                    'positions.id as position_id',
                    'positions.position as position_name',
                    'positions.qualification_id',
                    'positions.qualification as qualification_name',
                    'positions.price as position_price',

                    'department_positions.department_id',
                    'department_positions.percent',
                    'department_positions.montage_percent',
                    'department_positions.office_percent',
                    'department_positions.working_hours',
                    'department_positions.main',
                ])
                ->join('department_positions', 'department_positions.employee_id', '=', 'employees.id')
                ->join('positions', 'positions.id', '=', 'department_positions.position_id')
                ->where('employees.status', 'Active')
                ->when($departmentId, fn($q) => $q->where('department_positions.department_id', $departmentId))
                ->when(!empty($allowedQualificationIds), fn($q) => $q->whereIn('positions.qualification_id', $allowedQualificationIds))
                ->distinct()
                ->get();

            $employees = $employees->map(function ($employee) use ($date, $startTime, $endTime) {
                $available = !$this->planningEmployeeHasConflict($employee->id, $date, $startTime, $endTime);

                return [
                    'employee_id' => $employee->id,
                    'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    'position_id' => $employee->position_id,
                    'position_name' => $employee->position_name,
                    'qualification_id' => $employee->qualification_id,
                    'qualification_name' => $employee->qualification_name,
                    'price' => $employee->position_price,
                    'working_hours' => $employee->working_hours,
                    'montage_percent' => $employee->montage_percent,
                    'main' => $employee->main,
                    'available' => $available,
                    'score' => $this->planningEmployeeScore($employee, $available),
                ];
            })
                ->sortByDesc('score')
                ->values();

            $result[] = [
                'required_qualification_id' => $required['qualification_id'] ?? null,
                'required_qualification_name' => $required['qualification_name'] ?? null,
                'required_hours' => $required['hours_total'] ?? $required['qty'] ?? 0,
                'employees' => $employees->all(),
                'best_available' => $employees->firstWhere('available', true),
            ];
        }

        return $result;
    }

    private function planningEmployeeHasConflict(int $employeeId, string $date, string $startTime, string $endTime): bool
    {
        return MainAppointment::query()
            ->join('main_appointment_employees', 'main_appointment_employees.appointment_id', '=', 'main_appointments.id')
            ->where('main_appointment_employees.employee_id', $employeeId)
            ->whereDate('main_appointments.start_date', '<=', $date)
            ->whereDate('main_appointments.end_date', '>=', $date)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('main_appointments.start_time', '<', $endTime)
                    ->where('main_appointments.end_time', '>', $startTime);
            })
            ->whereNull('main_appointments.deleted_at')
            ->exists();
    }

    private function planningEmployeeScore($employee, bool $available): int
    {
        $score = 0;

        if ($available) {
            $score += 1000;
        }

        if (($employee->main ?? null) === 'Yes') {
            $score += 100;
        }

        $score += (int) ($employee->montage_percent ?? 0);
        $score += (int) ($employee->percent ?? 0);
        $score += (int) ($employee->working_hours ?? 0);

        return $score;
    }

    private function allowedPlanningQualificationIds(?int $requiredQualificationId): array
    {
        if (!$requiredQualificationId) {
            return [];
        }

        $table = 'position_qualification_hierarchies';

        if (!Schema::hasTable($table)) {
            return [(int) $requiredQualificationId];
        }

        /*
        |--------------------------------------------------------------------------
        | Support different possible column names
        |--------------------------------------------------------------------------
        | Your DB does not have parent_qualification_id, so this method checks
        | which columns really exist before querying.
        */

        $possibleParentColumns = [
            'parent_qualification_id',
            'parent_id',
            'higher_qualification_id',
            'qualification_id',
            'can_replace_qualification_id',
        ];

        $possibleChildColumns = [
            'child_qualification_id',
            'child_id',
            'lower_qualification_id',
            'replaceable_qualification_id',
            'depends_on_qualification_id',
        ];

        $parentColumn = collect($possibleParentColumns)
            ->first(fn($column) => Schema::hasColumn($table, $column));

        $childColumn = collect($possibleChildColumns)
            ->first(fn($column) => Schema::hasColumn($table, $column));

        if (!$parentColumn || !$childColumn) {
            return [(int) $requiredQualificationId];
        }

        $higher = DB::table($table)
            ->where($childColumn, $requiredQualificationId)
            ->pluck($parentColumn)
            ->filter()
            ->map(fn($id) => (int) $id)
            ->all();

        return collect([(int) $requiredQualificationId])
            ->merge($higher)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function profile(\App\Models\Deal $deal)
    {
        $dealRow = $this->baseDealQuery(false)
            ->where('deals.id', $deal->id)
            ->first();

        if (!$dealRow) {
            abort(404, 'Auftrag wurde nicht gefunden.');
        }

        $workflowStages = method_exists($this, 'dealWorkflowSubStages')
            ? $this->dealWorkflowSubStages(true)
            : collect();

        $workflowLabelMap = method_exists($this, 'dealWorkflowLabelMap')
            ? $this->dealWorkflowLabelMap()
            : [];

        $workflowColorMap = method_exists($this, 'dealWorkflowColorMap')
            ? $this->dealWorkflowColorMap()
            : [];

        $currentStatus = method_exists($this, 'normalizeDealWorkflowStatus')
            ? $this->normalizeDealWorkflowStatus($deal->status)
            : ($deal->status ?: 'open');

        $offerDetail = $this->profileFindOfferDetail($deal);
        $measurement = $this->profileFindMeasurement($deal, $offerDetail);

        $materialSections = [];
        $materialSource = 'Keine Materialdaten';

        if ($measurement) {
            $snapshot = $this->profileArrayValue($measurement->materials_snapshot ?? null);
            $summary = $this->profileArrayValue($measurement->material_summary ?? null);

            if (!empty($snapshot)) {
                $materialSections = $snapshot;
                $materialSource = 'Feinaufmaß Materialliste';
            } elseif (!empty($summary)) {
                $materialSections = $this->profileMaterialSummaryToSections($summary);
                $materialSource = 'Angebotsmaterial aus Feinaufmaß';
            }
        }

        if (empty($materialSections) && $offerDetail) {
            $sections = $this->profileArrayValue($offerDetail->sections ?? null);
            $materialSections = $this->profileExtractMaterialSectionsFromOfferSections($sections);
            $materialSource = 'Angebotspositionen';
        }

        $materialSections = $this->profileHydrateMaterialImagesAndLocations($materialSections);
        $materialStats = $this->profileMaterialStats($materialSections);

        // deal_invoices stillgelegt (invoices = führende Schiene) — leere Sammlung, verhaltensneutral (Tabelle war leer).
        $invoices = collect();

        $images = $this->profileImagesQuery($deal)
            ->latest()
            ->limit(60)
            ->get()
            ->map(fn($image) => $this->profileFormatImageDocument($image));

        $attachments = $this->profileAttachmentsQuery($deal)
            ->latest()
            ->limit(60)
            ->get()
            ->map(fn($attachment) => $this->profileFormatAttachmentDocument($attachment));

        $documents = $images->merge($attachments)
            ->sortByDesc(fn($doc) => $doc['created_at_sort'] ?? '')
            ->values();

        $notes = DealNote::query()
            ->where('deal_id', $deal->id)
            ->latest()
            ->limit(80)
            ->get();

        $leadProduct = LeadProductList::query()
            ->where('customer_id', $deal->customer_id)
            ->where('product_id', $deal->product_id)
            ->when($deal->alternative_id, fn($q) => $q->where('alternative_id', $deal->alternative_id))
            ->first();

        $employeeMap = $this->profileEmployeeMap($deal, $measurement, $notes);
        $measurementReport = $this->profileMeasurementReport($measurement, $images->count());

        $employees = Employee::query()
            ->where('status', 'Active')
            ->orderBy('name')
            ->orderBy('lastname')
            ->get(['id', 'name', 'lastname', 'image', 'status']);

        return view('admin.deal.profile', [
            'deal' => $deal,
            'dealRow' => $dealRow,
            'workflowStages' => $workflowStages,
            'workflowLabelMap' => $workflowLabelMap,
            'workflowColorMap' => $workflowColorMap,
            'currentStatus' => $currentStatus,
            'offerDetail' => $offerDetail,
            'measurement' => $measurement,
            'measurementReport' => $measurementReport,
            'materialSections' => $materialSections,
            'materialSource' => $materialSource,
            'materialStats' => $materialStats,
            'invoices' => $invoices,
            'images' => $images,
            'attachments' => $attachments,
            'documents' => $documents,
            'notes' => $notes,
            'leadProduct' => $leadProduct,
            'employeeMap' => $employeeMap,
            'employees' => $employees,
            'documentCount' => $documents->count(),
            'noteCount' => $notes->count(),
        ]);
    }

    public function profileStoreNote(Request $request, Deal $deal)
    {
        $validator = Validator::make($request->all(), [
            'description' => ['required', 'string', 'max:10000'],
        ], [
            'description.required' => 'Bitte eine Notiz eingeben.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $employeeId = auth()->user()->name ?? auth()->id();

        $note = DealNote::create([
            'deal_id' => $deal->id,
            'customer_id' => $deal->customer_id,
            'alternative_id' => $deal->alternative_id,
            'product_id' => $deal->product_id,
            'description' => trim((string) $request->input('description')),
            'created_by' => $employeeId,
            'updated_by' => $employeeId,
        ]);

        $employeeMap = $this->profileEmployeeMap($deal, null, collect([$note]));

        return response()->json([
            'success' => true,
            'message' => 'Notiz wurde gespeichert.',
            'note' => $this->profileFormatNote($note, $employeeMap),
            'note_count' => DealNote::query()->where('deal_id', $deal->id)->count(),
        ]);
    }

    public function profileUploadDocument(Request $request, Deal $deal)
    {
        $request->validate([
            'document' => ['required', 'file', 'max:20480'],
            'image_name' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('document');
        $extension = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'auftrag-dokument';
        $filename = $safeName . '_' . time() . '_' . Str::lower(Str::random(10)) . '.' . $extension;
        $path = $file->storeAs('uploads/customers', $filename);

        if (!$path || !Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Dokument konnte nicht gespeichert werden.',
            ], 500);
        }

        $employeeId = auth()->user()->name ?? auth()->id();
        $isImage = str_starts_with((string) $file->getMimeType(), 'image/');

        $imageId = DB::table('images')->insertGetId(array_filter([
            'customer_id' => $deal->customer_id,
            'alternative_id' => $deal->alternative_id,
            'article_group' => $deal->product_id,
            'image_name' => $request->input('image_name') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'image' => $filename,
            'file_type' => $extension,
            'status' => $isImage ? 'deal' : 'deal_document',
            'stage' => 'Auftrag Profil',
            'created_by' => $employeeId,
            'update_by' => $employeeId,
            'created_at' => now(),
            'updated_at' => now(),
        ], fn($value) => !is_null($value)));

        $image = Image::query()->find($imageId);

        return response()->json([
            'success' => true,
            'message' => 'Dokument wurde hochgeladen.',
            'document' => $this->profileFormatImageDocument($image),
            'document_count' => $this->profileImagesQuery($deal)->count() + $this->profileAttachmentsQuery($deal)->count(),
        ]);
    }

    public function profileDeleteImageDocument(Deal $deal, Image $image)
    {
        if ((int) $image->customer_id !== (int) $deal->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Dieses Dokument gehört nicht zu diesem Auftrag.',
            ], 403);
        }

        $filename = ltrim((string) $image->image, '/');
        $path = 'uploads/customers/' . $filename;

        if ($filename && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        $image->update_by = auth()->user()->name ?? auth()->id();
        $image->save();
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dokument wurde gelöscht.',
            'document_count' => $this->profileImagesQuery($deal)->count() + $this->profileAttachmentsQuery($deal)->count(),
        ]);
    }

    public function profileDeleteAttachmentDocument(Deal $deal, $attachment)
    {
        $row = DB::table('offer_folder_attachments')->where('id', $attachment)->first();

        if (!$row) {
            return response()->json([
                'success' => false,
                'message' => 'Dokument wurde nicht gefunden.',
            ], 404);
        }

        $belongsToDeal = false;

        if ($deal->offer_folder_id && (int) ($row->offer_folder_id ?? 0) === (int) $deal->offer_folder_id) {
            $belongsToDeal = true;
        }

        if ($deal->offer_id && (int) ($row->offer_id ?? 0) === (int) $deal->offer_id) {
            $belongsToDeal = true;
        }

        if (!$belongsToDeal) {
            return response()->json([
                'success' => false,
                'message' => 'Dieses Dokument gehört nicht zu diesem Auftrag.',
            ], 403);
        }

        foreach (['path', 'file_path', 'file', 'stored_name'] as $field) {
            if (!empty($row->{$field})) {
                $filePath = ltrim((string) $row->{$field}, '/');
                if (Storage::disk('local')->exists($filePath)) {
                    Storage::disk('local')->delete($filePath);
                }
                if (Storage::disk('local')->exists('uploads/customers/' . $filePath)) {
                    Storage::disk('local')->delete('uploads/customers/' . $filePath);
                }
            }
        }

        if (Schema::hasColumn('offer_folder_attachments', 'deleted_at')) {
            DB::table('offer_folder_attachments')->where('id', $row->id)->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('offer_folder_attachments')->where('id', $row->id)->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Dokument wurde gelöscht.',
            'document_count' => $this->profileImagesQuery($deal)->count() + $this->profileAttachmentsQuery($deal)->count(),
        ]);
    }

    public function profileUpdateStatus(Request $request, Deal $deal)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required', 'string', 'max:120'],
            'reason' => ['required', 'string', 'min:3', 'max:5000'],
        ], [
            'status.required' => 'Bitte einen Status auswählen.',
            'reason.required' => 'Bitte eine Begründung eingeben.',
            'reason.min' => 'Die Begründung ist zu kurz.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $requestedStatus = trim((string) $request->input('status'));
        $statusKey = method_exists($this, 'normalizeDealWorkflowStatus')
            ? $this->normalizeDealWorkflowStatus($requestedStatus)
            : $requestedStatus;

        $allowedWorkflow = method_exists($this, 'dealWorkflowStageKeys') ? $this->dealWorkflowStageKeys() : [];
        $allowedSpecial = ['complete', 'Junk'];

        if (!in_array($statusKey, $allowedWorkflow, true) && !in_array($statusKey, $allowedSpecial, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Dieser Auftrag-Status ist nicht in den Unterphasen konfiguriert.',
            ], 422);
        }

        return DB::transaction(function () use ($deal, $statusKey, $request, $allowedSpecial) {
            $oldStatus = $deal->status;
            $deal->status = $statusKey;
            $deal->updated_at = now();
            $deal->save();

            if (!in_array($statusKey, $allowedSpecial, true) && method_exists($this, 'syncDealWorkflowTargets')) {
                $this->syncDealWorkflowTargets($deal, $statusKey);
            }

            $employeeId = auth()->user()->name ?? auth()->id();
            $labelMap = method_exists($this, 'dealWorkflowLabelMap') ? $this->dealWorkflowLabelMap() : [];
            $oldLabel = $labelMap[$oldStatus] ?? $oldStatus;
            $newLabel = $labelMap[$statusKey] ?? $statusKey;

            $note = DealNote::create([
                'deal_id' => $deal->id,
                'customer_id' => $deal->customer_id,
                'alternative_id' => $deal->alternative_id,
                'product_id' => $deal->product_id,
                'description' => 'Status geändert: ' . $oldLabel . ' → ' . $newLabel . "\n\nBegründung: " . trim((string) $request->input('reason')),
                'created_by' => $employeeId,
                'updated_by' => $employeeId,
            ]);

            $employeeMap = $this->profileEmployeeMap($deal, null, collect([$note]));
            $colorMap = method_exists($this, 'dealWorkflowColorMap') ? $this->dealWorkflowColorMap() : [];

            return response()->json([
                'success' => true,
                'message' => 'Auftrag-Status wurde aktualisiert.',
                'status' => $statusKey,
                'status_label' => $newLabel,
                'status_color' => $colorMap[$statusKey] ?? '#93c21c',
                'note' => $this->profileFormatNote($note, $employeeMap),
                'note_count' => DealNote::query()->where('deal_id', $deal->id)->count(),
            ]);
        });
    }

    protected function profileFindOfferDetail(Deal $deal): ?OfferDetail
    {
        $offerDetail = OfferDetail::query()
            ->when($deal->offer_folder_id, fn($q) => $q->where('offer_folder_id', $deal->offer_folder_id))
            ->when($deal->offer_id, fn($q) => $q->orWhere('offer_id', $deal->offer_id))
            ->orderByDesc('id')
            ->first();

        if (!$offerDetail && $deal->offer_id) {
            $offerDetail = OfferDetail::query()
                ->where('offer_id', $deal->offer_id)
                ->orderByDesc('id')
                ->first();
        }

        return $offerDetail;
    }

    protected function profileFindMeasurement(Deal $deal, ?OfferDetail $offerDetail): ?\App\Models\DealMeasurement
    {
        if (!class_exists(\App\Models\DealMeasurement::class)) {
            return null;
        }

        return \App\Models\DealMeasurement::query()
            ->with(['items', 'histories', 'editDetail'])
            ->where(function ($query) use ($deal, $offerDetail) {
                $query->where('deal_id', $deal->id);

                if ($deal->offer_folder_id) {
                    $query->orWhere('offer_folder_id', $deal->offer_folder_id);
                }

                if ($deal->offer_id) {
                    $query->orWhere('offer_id', $deal->offer_id);
                }

                if ($offerDetail?->id) {
                    $query->orWhere('offer_detail_id', $offerDetail->id);
                }
            })
            ->orderByDesc('id')
            ->first();
    }

    protected function profileImagesQuery(Deal $deal)
    {
        return Image::query()
            ->where('customer_id', $deal->customer_id)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($deal) {
                $query->whereNull('alternative_id')
                    ->orWhere('alternative_id', $deal->alternative_id);
            })
            ->where(function ($query) use ($deal) {
                $query->whereNull('article_group')
                    ->orWhere('article_group', 0)
                    ->orWhere('article_group', $deal->product_id);
            });
    }

    protected function profileAttachmentsQuery(Deal $deal)
    {
        return OfferFolderAttachment::query()
            ->whereNull('deleted_at')
            ->where(function ($query) use ($deal) {
                if ($deal->offer_folder_id) {
                    $query->orWhere('offer_folder_id', $deal->offer_folder_id);
                }

                if ($deal->offer_id) {
                    $query->orWhere('offer_id', $deal->offer_id);
                }
            });
    }

    protected function profileEmployeeMap(Deal $deal, $measurement = null, $notes = null): array
    {
        $ids = collect([
            $deal->employee_id,
            $deal->checked_by,
            $deal->reviewer_id,
            $deal->created_by ?? null,
            $deal->updated_by ?? null,
        ]);

        if ($measurement) {
            $ids = $ids->merge([
                $measurement->created_by ?? null,
                $measurement->updated_by ?? null,
                $measurement->sent_by ?? null,
            ]);

            if ($measurement->histories) {
                $ids = $ids->merge($measurement->histories->pluck('created_by'));
            }
        }

        if ($notes) {
            $ids = $ids->merge(collect($notes)->pluck('created_by'));
            $ids = $ids->merge(collect($notes)->pluck('updated_by'));
        }

        return Employee::query()
            ->whereIn('id', $ids->filter()->map(fn($id) => (int) $id)->unique()->values())
            ->get(['id', 'name', 'lastname', 'image', 'gender'])
            ->mapWithKeys(function ($employee) {
                $name = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
                return [
                    (string) $employee->id => [
                        'id' => $employee->id,
                        'name' => $name ?: ('Mitarbeiter #' . $employee->id),
                        'image' => $employee->image,
                        'gender' => $employee->gender,
                    ]
                ];
            })
            ->all();
    }

    protected function profileEmployeeName($employeeId, array $employeeMap): string
    {
        if (!$employeeId) {
            return 'System';
        }

        return $employeeMap[(string) $employeeId]['name'] ?? ('Mitarbeiter #' . $employeeId);
    }

    protected function profileFormatNote(DealNote $note, array $employeeMap): array
    {
        return [
            'id' => $note->id,
            'description' => $note->description,
            'created_by' => $note->created_by,
            'created_by_name' => $this->profileEmployeeName($note->created_by, $employeeMap),
            'created_at' => optional($note->created_at)->format('d.m.Y H:i'),
            'created_at_iso' => optional($note->created_at)->toISOString(),
        ];
    }

    protected function profileMeasurementReport($measurement, int $measurementImageCount = 0): array
    {
        if (!$measurement) {
            return [
                'percent' => 0,
                'done' => 0,
                'total' => 6,
                'items' => [
                    ['key' => 'created', 'label' => 'Feinaufmaß erstellt', 'done' => false, 'detail' => 'Noch nicht erstellt'],
                    ['key' => 'sent', 'label' => 'An Mitarbeiter gesendet', 'done' => false, 'detail' => 'Noch offen'],
                    ['key' => 'form', 'label' => 'Formulardaten gespeichert', 'done' => false, 'detail' => 'PV/WP/OTHER Formular fehlt'],
                    ['key' => 'material', 'label' => 'Material gespeichert', 'done' => false, 'detail' => 'Material wurde noch nicht gespeichert'],
                    ['key' => 'approved', 'label' => 'Material freigegeben', 'done' => false, 'detail' => 'Keine Freigaben'],
                    ['key' => 'images', 'label' => 'Fotos/Dokumente hochgeladen', 'done' => false, 'detail' => 'Keine Bilder'],
                ],
            ];
        }

        $detail = $measurement->editDetail;
        $formData = $this->profileArrayValue($detail?->form_data ?? null);
        $roofData = $this->profileArrayValue($detail?->roof_data ?? null);
        $pvData = $this->profileArrayValue($detail?->pv_data ?? null);
        $wpData = $this->profileArrayValue($detail?->wp_data ?? null);
        $rawSnapshot = $this->profileArrayValue($detail?->raw_snapshot ?? null);
        $materialsSaved = !empty($measurement->materials_saved_at) || !empty($measurement->materials_snapshot);
        $totalMaterials = (int) ($measurement->materials_total_count ?? 0);
        $approvedMaterials = (int) ($measurement->materials_approved_count ?? 0);

        $items = [
            [
                'key' => 'created',
                'label' => 'Feinaufmaß erstellt',
                'done' => true,
                'detail' => ($measurement->measurement_no ?? '#' . $measurement->id) . ' · ' . optional($measurement->created_at)->format('d.m.Y H:i'),
            ],
            [
                'key' => 'sent',
                'label' => 'An Mitarbeiter gesendet',
                'done' => !empty($measurement->sent_at) || in_array($measurement->status, ['sent', 'completed'], true),
                'detail' => !empty($measurement->sent_at) ? optional($measurement->sent_at)->format('d.m.Y H:i') : 'Noch nicht gesendet',
            ],
            [
                'key' => 'form',
                'label' => 'Formulardaten gespeichert',
                'done' => !empty($formData) || !empty($pvData) || !empty($wpData) || !empty($rawSnapshot),
                'detail' => $detail ? (($detail->type ?? 'Formular') . ' · ' . optional($detail->saved_at)->format('d.m.Y H:i')) : 'Formular fehlt',
            ],
            [
                'key' => 'roof',
                'label' => 'Dach-/Objektdaten ergänzt',
                'done' => !empty($roofData),
                'detail' => !empty($roofData) ? count($roofData) . ' Einträge gespeichert' : 'Noch keine Dach-/Objektdaten',
            ],
            [
                'key' => 'material',
                'label' => 'Material gespeichert',
                'done' => $materialsSaved,
                'detail' => $materialsSaved ? ($totalMaterials . ' Positionen · ' . optional($measurement->materials_saved_at)->format('d.m.Y H:i')) : 'Material wurde noch nicht gespeichert',
            ],
            [
                'key' => 'approved',
                'label' => 'Material freigegeben',
                'done' => $totalMaterials > 0 && $approvedMaterials >= $totalMaterials,
                'detail' => $approvedMaterials . '/' . $totalMaterials . ' freigegeben',
            ],
            [
                'key' => 'images',
                'label' => 'Fotos/Dokumente hochgeladen',
                'done' => $measurementImageCount > 0,
                'detail' => $measurementImageCount . ' Bilder/Dokumente',
            ],
            [
                'key' => 'completed',
                'label' => 'Feinaufmaß abgeschlossen',
                'done' => $measurement->status === 'completed',
                'detail' => $measurement->status === 'completed' ? 'Abgeschlossen und gesperrt' : 'Noch offen',
            ],
        ];

        $done = collect($items)->where('done', true)->count();
        $total = count($items);

        return [
            'percent' => $total > 0 ? round(($done / $total) * 100) : 0,
            'done' => $done,
            'total' => $total,
            'items' => $items,
        ];
    }

    protected function profileHydrateMaterialImagesAndLocations(array $sections): array
    {
        foreach ($sections as $sectionIndex => $section) {
            foreach (($section['items'] ?? []) as $itemIndex => $item) {
                $sections[$sectionIndex]['items'][$itemIndex]['image_url'] = $this->profileMaterialImageUrl($item['image'] ?? $item['img'] ?? null);
                $sections[$sectionIndex]['items'][$itemIndex]['location'] = $item['location']
                    ?? $item['room']
                    ?? $item['position']
                    ?? $item['section_title']
                    ?? $section['title']
                    ?? 'Lager/Projekt';
            }
        }

        return $sections;
    }

    protected function profileMaterialImageUrl(?string $image): ?string
    {
        $image = trim((string) $image);

        if ($image === '') {
            return null;
        }

        if (Str::startsWith($image, ['http://', 'https://', '/'])) {
            return $image;
        }

        foreach (['images/products/', 'images/product/', 'images/articles/', 'uploads/products/', 'uploads/customers/'] as $prefix) {
            if (file_exists(public_path($prefix . $image))) {
                return asset($prefix . $image);
            }
        }

        return asset('uploads/customers/' . $image);
    }

    protected function profileFormatImageDocument(Image $image): array
    {
        $filename = ltrim((string) $image->image, '/');
        $isImage = in_array(strtolower((string) $image->file_type), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true)
            || preg_match('/\.(jpg|jpeg|png|webp|gif|svg)$/i', $filename);

        $url = $this->profileSecureImageUrl($image, $filename);

        return [
            'source' => 'image',
            'id' => $image->id,
            'title' => $image->image_name ?: $filename,
            'name' => $image->image_name ?: $filename,
            'url' => $url,
            'preview_url' => $url,
            'download_url' => $url,
            'is_image' => $isImage,
            'type' => $image->file_type ?: 'file',
            'stage' => $image->stage ?: $image->status,
            'created_at' => optional($image->created_at)->format('d.m.Y H:i'),
            'created_at_sort' => optional($image->created_at)->toISOString(),
            'delete_url' => route('deal.profile.documents.images.destroy', [$image->customer_id ? $this->profileDealIdForImage($image) : 0, $image->id]),
        ];
    }

    protected function profileFormatAttachmentDocument($attachment): array
    {
        $title = $attachment->title ?? $attachment->file_name ?? $attachment->name ?? ('Dokument #' . $attachment->id);
        $path = $attachment->path ?? $attachment->file_path ?? $attachment->file ?? $attachment->stored_name ?? null;
        $extension = strtolower(pathinfo((string) ($path ?: $title), PATHINFO_EXTENSION));
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true);

        $url = $attachment->url ?? null;
        if (!$url && $path) {
            $url = url('/storage/' . ltrim((string) $path, '/'));
        }

        return [
            'source' => 'attachment',
            'id' => $attachment->id,
            'title' => $title,
            'name' => $title,
            'url' => $url,
            'preview_url' => $url,
            'download_url' => $url,
            'is_image' => $isImage,
            'type' => $extension ?: 'file',
            'stage' => 'Anhang',
            'created_at' => optional($attachment->created_at)->format('d.m.Y H:i'),
            'created_at_sort' => optional($attachment->created_at)->toISOString(),
            'delete_url' => null,
        ];
    }

    protected function profileSecureImageUrl(Image $image, string $filename): string
    {
        if (\Illuminate\Support\Facades\Route::has('secure.image.byFilename')) {
            return route('secure.image.byFilename', ['filename' => $filename]);
        }

        if (\Illuminate\Support\Facades\Route::has('images.secure.filename')) {
            return route('images.secure.filename', ['filename' => $filename]);
        }

        if (\Illuminate\Support\Facades\Route::has('images.secure')) {
            return route('images.secure', ['id' => $image->id]);
        }

        if (\Illuminate\Support\Facades\Route::has('secure.image')) {
            return route('secure.image', ['id' => $image->id]);
        }

        return url('/secure-image/file/' . rawurlencode($filename));
    }

    protected function profileDealIdForImage(Image $image): int
    {
        $deal = Deal::query()
            ->where('customer_id', $image->customer_id)
            ->when($image->alternative_id, fn($q) => $q->where('alternative_id', $image->alternative_id))
            ->when($image->article_group, fn($q) => $q->where('product_id', $image->article_group))
            ->orderByDesc('id')
            ->first();

        return (int) ($deal?->id ?? 0);
    }

    protected function profileArrayValue($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->toArray();
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    protected function profileMaterialSummaryToSections(array $rows): array
    {
        $grouped = [];

        foreach ($rows as $row) {
            $sectionTitle = $row['section_title'] ?? $row['section'] ?? 'Material';

            if (!isset($grouped[$sectionTitle])) {
                $grouped[$sectionTitle] = [
                    'id' => \Illuminate\Support\Str::slug($sectionTitle) ?: uniqid('section_'),
                    'title' => $sectionTitle,
                    'items' => [],
                ];
            }

            $grouped[$sectionTitle]['items'][] = [
                'id' => $row['id'] ?? uniqid('mat_'),
                'name' => $row['name'] ?? $row['title'] ?? 'Unbenannt',
                'description' => $row['description'] ?? null,
                'image' => $row['image'] ?? $row['img'] ?? $row['product_image'] ?? null,
                'location' => $row['location'] ?? $row['room'] ?? $row['position'] ?? $sectionTitle,
                'section_title' => $sectionTitle,
                'article_no' => $row['article_no'] ?? $row['sku'] ?? null,
                'unit' => $row['unit'] ?? 'Stk',
                'plan_qty' => (float) ($row['qty_offer'] ?? $row['qty'] ?? $row['plan_qty'] ?? 0),
                'verbrauch_qty' => (float) ($row['qty_measurement'] ?? $row['qty_final'] ?? $row['verbrauch_qty'] ?? $row['qty_offer'] ?? 0),
                'approved' => (bool) ($row['approved'] ?? false),
                'delta_reason' => $row['delta_reason'] ?? null,
            ];
        }

        return array_values($grouped);
    }

    protected function profileExtractMaterialSectionsFromOfferSections(array $sections): array
    {
        $result = [];

        foreach ($sections as $sectionIndex => $section) {
            $items = [];

            foreach (($section['items'] ?? $section['rows'] ?? []) as $item) {
                $kind = strtolower((string) ($item['kind'] ?? $item['item_type'] ?? $item['type'] ?? ''));

                if (in_array($kind, ['labor', 'lohn', 'employee'], true)) {
                    continue;
                }

                $items[] = [
                    'id' => $item['id'] ?? uniqid('offer_mat_'),
                    'name' => $item['name'] ?? $item['title'] ?? $item['product_name'] ?? 'Unbenannt',
                    'description' => $item['description'] ?? $item['notes'] ?? null,
                    'image' => $item['image'] ?? $item['img'] ?? $item['product_image'] ?? $item['photo'] ?? null,
                    'location' => $item['location'] ?? $item['room'] ?? $item['position'] ?? ($section['title'] ?? $section['name'] ?? 'Material'),
                    'section_title' => $section['title'] ?? $section['name'] ?? 'Material',
                    'article_no' => $item['article_no'] ?? $item['sku'] ?? $item['distributor_article_no'] ?? null,
                    'unit' => $item['unit'] ?? $item['measure_unit'] ?? 'Stk',
                    'plan_qty' => (float) ($item['qty'] ?? $item['quantity'] ?? $item['qty_offer'] ?? 0),
                    'verbrauch_qty' => (float) ($item['qty_measurement'] ?? $item['qty_final'] ?? $item['qty'] ?? $item['quantity'] ?? 0),
                    'approved' => (bool) ($item['approved'] ?? false),
                    'delta_reason' => $item['delta_reason'] ?? null,
                ];
            }

            if (!empty($items)) {
                $result[] = [
                    'id' => $section['id'] ?? 'section_' . ($sectionIndex + 1),
                    'title' => $section['title'] ?? $section['name'] ?? 'Material',
                    'items' => $items,
                ];
            }
        }

        return $result;
    }

    protected function profileMaterialStats(array $sections): array
    {
        $total = 0;
        $approved = 0;
        $plan = 0.0;
        $verbrauch = 0.0;

        foreach ($sections as $section) {
            foreach (($section['items'] ?? []) as $item) {
                $total++;
                $approved += !empty($item['approved']) ? 1 : 0;
                $plan += (float) ($item['plan_qty'] ?? $item['qty'] ?? 0);
                $verbrauch += (float) ($item['verbrauch_qty'] ?? 0);

                foreach (($item['subItems'] ?? []) as $subItem) {
                    $total++;
                    $approved += !empty($subItem['approved']) ? 1 : 0;
                    $plan += (float) ($subItem['plan_qty'] ?? $subItem['qty'] ?? 0);
                    $verbrauch += (float) ($subItem['verbrauch_qty'] ?? 0);
                }
            }
        }

        return [
            'total' => $total,
            'approved' => $approved,
            'open' => max(0, $total - $approved),
            'plan' => $plan,
            'verbrauch' => $verbrauch,
            'delta' => $verbrauch - $plan,
            'percent' => $total > 0 ? round(($approved / $total) * 100) : 0,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | P1-16: Auftrags-Anlage (Angebot -> Auftrag) + Lebenszyklus
    |--------------------------------------------------------------------------
    | Wiederhergestellte, real genutzte Endpunkte. Rechte nach dem reparierten
    | Muster (User::hasPermission -> user_rolls ueber users.id, Flag=1,
    | is_admin-Bypass). Felder werden server-seitig aus dem lead_product_list
    | abgeleitet, nicht aus spoofbaren Hidden-Feldern uebernommen.
    */

    /** Mitarbeiter-ID des eingeloggten Kontos (users.name speichert hier die employees.id). */
    private function dealCurrentEmployeeId(): ?int
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }
        if (is_numeric($user->name ?? null)) {
            return (int) $user->name;
        }
        if (! empty($user->employee_id) && is_numeric($user->employee_id)) {
            return (int) $user->employee_id;
        }
        return $user->employee?->id ? (int) $user->employee->id : null;
    }

    private function authorizeDealUpdate(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->hasPermission('Customer', 'update')) {
            abort(403, 'Keine Berechtigung, Auftraege zu bearbeiten.');
        }
    }

    private function authorizeDealDelete(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->hasPermission('Customer', 'delete')) {
            abort(403, 'Keine Berechtigung, Auftraege zu loeschen.');
        }
    }

    /**
     * Auftrag aus dem gewaehlten Produkt (lead_product_lists) anlegen.
     * Modal "Neues Projekt erstellen" -> route('deal.store').
     */
    public function dealStore(Request $request)
    {
        $this->authorizeDealUpdate();

        $validated = $request->validate([
            'customer_id'          => ['required', 'integer'],
            'lead_product_list_id' => ['required', 'integer'],
        ]);

        // Massgebliche Quelle: das Produkt des Leads (nicht die spoofbaren Hidden-Felder).
        $lpl = DB::table('lead_product_lists')->where('id', $validated['lead_product_list_id'])->first();
        if (! $lpl) {
            return redirect()->back()->with('error', 'Das gewaehlte Produkt wurde nicht gefunden.');
        }

        if ((int) $lpl->customer_id !== (int) $validated['customer_id']) {
            return redirect()->back()->with('error', 'Produkt und Kunde passen nicht zusammen.');
        }

        // employee_id ist Pflicht -> Fallback auf den eingeloggten Nutzer.
        $employeeId = $lpl->employee_id ?: $this->dealCurrentEmployeeId();
        if (! $employeeId) {
            return redirect()->back()->with('error', 'Dem Produkt ist kein Mitarbeiter zugeordnet und das eigene Konto hat keinen Mitarbeiter.');
        }

        // Angebot verknuepfen: bevorzugt der angenommene Ordner, sonst ein passendes Angebot.
        $offerFolderId = $lpl->accepted_offer_folder_id ?: null;
        $offerId = $offerFolderId
            ? DB::table('offer_folders')->where('id', $offerFolderId)->value('offer_id')
            : null;
        if (! $offerId) {
            $offerId = DB::table('offers')
                ->where('customer_id', $lpl->customer_id)
                ->where('product_id', $lpl->product_id)
                ->where('alternative_id', $lpl->alternative_id)
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->value('id');
        }

        $deal = DB::transaction(function () use ($lpl, $employeeId, $offerId, $offerFolderId) {
            $deal = Deal::create([
                'customer_id'     => $lpl->customer_id,
                'product_id'      => $lpl->product_id,
                'alternative_id'  => $lpl->alternative_id,
                'service_id'      => $lpl->service_id,
                'department_id'   => $lpl->department_id,
                'service'         => $lpl->service,
                'employee_id'     => $employeeId,
                'offer_id'        => $offerId,
                'offer_folder_id' => $offerFolderId,
                'status'          => 'deal',
            ]);

            // Kanban konsistent halten: Lead-Stufe (status UND stage gemeinsam) auf 'deal' setzen und
            // den Vor-Deal-Stand in old_stage merken (fuer die Rueckbuchung beim Storno, Schwaeche 6).
            $priorStage = $lpl->stage ?: $lpl->status;
            DB::table('lead_product_lists')->where('id', $lpl->id)->update([
                'old_stage'  => ($priorStage && strtolower((string) $priorStage) !== 'deal')
                    ? $priorStage
                    : ($lpl->old_stage ?: 'accepted'),
                'status'     => 'deal',
                'stage'      => 'deal',
                'updated_at' => now(),
            ]);

            return $deal;
        });

        return redirect()->back()->with('success', 'Auftrag wurde angelegt (Nr. ' . $deal->order_number . ').');
    }

    /** Auftrag als Junk markieren (reversibel ueber unjunk). */
    public function junk($id)
    {
        $this->authorizeDealDelete();

        $deal = Deal::findOrFail($id);

        // Schwaeche 5 + 6 (gespiegelt zu destroy): Rechnungen rueckabwickeln + Lead-Stufe zuruecksetzen
        // (atomar mit der Junk-Markierung). KEIN delete() — Junk ist reversibel ueber unjunk.
        $invoiceInfo = DB::transaction(function () use ($deal) {
            $deal->status = 'Junk';
            $deal->save();
            $info = $this->cancelInvoicesForDeal($deal);   // Schwaeche 5
            $this->restoreLeadStageForDeal($deal);      // Schwaeche 6
            return $info;
        });

        $redirect = redirect()->back()->with('success', 'Auftrag wurde als Junk markiert.');
        if (($invoiceInfo['paid_count'] ?? 0) > 0) {
            $redirect->with('warning', 'Als Junk markiert, enthaelt ' . $invoiceInfo['paid_count']
                . ' bezahlte Rechnung(en) ueber ' . number_format($invoiceInfo['paid_sum'], 2, ',', '.')
                . ' EUR - bitte buchhalterisch pruefen.');
        }

        return $redirect;
    }

    /** Auftrag aus Junk zurueckholen. */
    public function unjunk($id)
    {
        $this->authorizeDealDelete();

        $deal = Deal::findOrFail($id);
        $deal->status = 'deal';
        $deal->save();

        // Schwaeche 6 (symmetrisch): Lead-Stufe wieder auf 'deal' setzen.
        $this->markLeadStageDealForDeal($deal);

        return redirect()->back()->with('success', 'Auftrag wurde aus Junk wiederhergestellt.');
    }

    /** Auftrag loeschen (SoftDelete -> ueber restore wiederherstellbar). */
    public function destroy($id)
    {
        $this->authorizeDealDelete();

        $deal = Deal::findOrFail($id);

        // Schwaeche 5 + 6: Rechnungen rueckabwickeln + Lead-Stufe zuruecksetzen (atomar mit dem Loeschen).
        $invoiceInfo = DB::transaction(function () use ($deal) {
            $info = $this->cancelInvoicesForDeal($deal);   // Schwaeche 5
            $this->restoreLeadStageForDeal($deal);      // Schwaeche 6
            $deal->delete();
            return $info;
        });

        $redirect = redirect()->back()->with('success', 'Auftrag wurde geloescht.');
        if (($invoiceInfo['paid_count'] ?? 0) > 0) {
            $redirect->with('warning', 'Auftrag storniert, enthaelt ' . $invoiceInfo['paid_count']
                . ' bezahlte Rechnung(en) ueber ' . number_format($invoiceInfo['paid_sum'], 2, ',', '.')
                . ' EUR - bitte buchhalterisch pruefen (Rueckzahlung/Gutschrift).');
        }

        return $redirect;
    }

    /** Geloeschten Auftrag wiederherstellen. */
    public function restore($id)
    {
        $this->authorizeDealDelete();

        $deal = Deal::withTrashed()->findOrFail($id);
        $deal->restore();

        return redirect()->back()->with('success', 'Auftrag wurde wiederhergestellt.');
    }

    /**
     * Schwaeche 6: Lead-Stufe (status + stage konsistent) eines Auftrags auf den Vor-Deal-Stand
     * zuruecksetzen. Idempotent: wirkt nur, solange der Lead noch in 'deal' steht. Fallback 'accepted'.
     */
    private function restoreLeadStageForDeal(Deal $deal): void
    {
        $lpls = DB::table('lead_product_lists')
            ->where('customer_id', $deal->customer_id)
            ->where('product_id', $deal->product_id)
            ->where('alternative_id', $deal->alternative_id)
            ->get();

        foreach ($lpls as $lpl) {
            $inDeal = strtolower((string) $lpl->status) === 'deal'
                || strtolower((string) $lpl->stage) === 'deal';
            if (! $inDeal) {
                continue; // schon zurueckgesetzt -> nicht erneut (idempotent)
            }
            $target = $lpl->old_stage ?: 'accepted';
            DB::table('lead_product_lists')->where('id', $lpl->id)->update([
                'status'     => $target,
                'stage'      => $target,
                'updated_at' => now(),
            ]);
        }
    }

    /** Schwaeche 6 (symmetrisch): Lead-Stufe (status + stage) eines Auftrags wieder auf 'deal' setzen. */
    private function markLeadStageDealForDeal(Deal $deal): void
    {
        DB::table('lead_product_lists')
            ->where('customer_id', $deal->customer_id)
            ->where('product_id', $deal->product_id)
            ->where('alternative_id', $deal->alternative_id)
            ->update(['status' => 'deal', 'stage' => 'deal', 'updated_at' => now()]);
    }

    /**
     * Schwaeche 5: Beim Auftrags-Storno die deal-verknuepften Rechnungen rueckabwickeln (idempotent).
     * - Offene Rechnungen (paid_amount = 0) -> status = 'storniert'.
     * - Bezahlte Rechnungen (paid_amount > 0) -> status = 'storniert_bezahlt_pruefen' (NICHT loeschen,
     *   menschliche Pruefung noetig: Rueckzahlung/Gutschrift).
     * Behandelt die führende Rechnungs-Schiene 'invoices' (wo die Umsaetze liegen).
     * (Alt-Schiene stillgelegt 2026-07-05 — aus dieser Storno-Logik entfernt; invoices ist die einzige Wahrheit.)
     * Gibt Anzahl/Summe der bezahlten Rechnungen fuer eine Warnung zurueck.
     */
    private function cancelInvoicesForDeal(Deal $deal): array
    {
        $paidCount = 0;
        $paidSum = 0.0;

        foreach (['invoices' => 'total_amount'] as $table => $amountCol) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'deal_id')) {
                continue;
            }

            $query = DB::table($table)
                ->where('deal_id', $deal->id)
                ->whereNotIn('status', ['storniert', 'storniert_bezahlt_pruefen']); // idempotent: schon markierte ueberspringen

            if (Schema::hasColumn($table, 'deleted_at')) {
                $query->whereNull('deleted_at');
            }

            foreach ($query->get() as $inv) {
                $paid = (float) ($inv->paid_amount ?? 0);
                $newStatus = $paid > 0 ? 'storniert_bezahlt_pruefen' : 'storniert';

                DB::table($table)->where('id', $inv->id)->update([
                    'status'     => $newStatus,
                    'updated_at' => now(),
                ]);

                if ($paid > 0) {
                    $paidCount++;
                    $paidSum += (float) ($inv->{$amountCol} ?? 0);
                }
            }
        }

        return ['paid_count' => $paidCount, 'paid_sum' => $paidSum];
    }

    /** Mitarbeiterliste fuer den Reviewer-/Geprueft-durch-Auswaehler (JSON: id, name, lastname). */
    public function getEmployees()
    {
        $cols = ['id', 'name'];
        if (Schema::hasColumn('employees', 'lastname')) {
            $cols[] = 'lastname';
        }

        $employees = Employee::query()
            ->when(Schema::hasColumn('employees', 'status'), fn ($q) => $q->where('status', 'Active'))
            ->orderBy('name')
            ->get($cols);

        return response()->json($employees);
    }

    /** Pruefer / Geprueft-durch eines Auftrags setzen. */
    public function updateReviewers(Request $request)
    {
        $this->authorizeDealUpdate();

        $data = $request->validate([
            'deal_id'     => ['required', 'integer'],
            'checked_by'  => ['nullable', 'integer'],
            'reviewed_by' => ['nullable', 'integer'],
        ]);

        $deal = Deal::findOrFail($data['deal_id']);
        $deal->checked_by  = $data['checked_by'] ?: null;
        $deal->reviewer_id = $data['reviewed_by'] ?: null;
        $deal->save();

        return response()->json(['success' => true]);
    }

    /** Inline-Feld eines Auftrags aktualisieren (Whitelist: sign_date, confirmed_at, status, price). */
    public function updateDate(Request $request)
    {
        $this->authorizeDealUpdate();

        $data = $request->validate([
            'deal_id' => ['required', 'integer'],
            'field'   => ['required', 'string'],
            'value'   => ['nullable'],
        ]);

        $allowed = ['sign_date', 'confirmed_at', 'status', 'price'];
        if (! in_array($data['field'], $allowed, true)) {
            return response()->json(['success' => false, 'message' => 'Feld nicht erlaubt.'], 422);
        }

        $value = $data['value'];
        if ($data['field'] === 'price') {
            $value = ($value === null || $value === '') ? null : (float) $value;
        }

        $deal = Deal::findOrFail($data['deal_id']);
        $deal->{$data['field']} = $value;
        $deal->save();

        return response()->json(['success' => true]);
    }

    /**
     * Auftrags-Profil: Dokument loeschen. Dispatcher auf die vorhandenen
     * Bild- (profileDeleteImageDocument) bzw. Anhang-Methoden (profileDeleteAttachmentDocument).
     */
    public function profileDeleteDocument(Request $request, Deal $deal, $source = null, $id = null)
    {
        $this->authorizeDealDelete();

        if ($source === 'image') {
            $image = Image::findOrFail($id);
            return $this->profileDeleteImageDocument($deal, $image);
        }

        if ($source === 'attachment') {
            return $this->profileDeleteAttachmentDocument($deal, $id);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unbekannter Dokumenttyp.',
        ], 422);
    }

}
