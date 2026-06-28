<?php

namespace App\Http\Controllers\Customer\Kanban;

use App\Http\Controllers\Controller;
use App\Models\LeadProductList;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class LeadStageBulkMoveController extends Controller
{
    public function options(): JsonResponse
    {
        $stages = $this->availableStages();

        return response()->json([
            'success' => true,
            'stages' => $stages,
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $data = $request->validate([
            'source_stage' => ['required', 'string', 'max:120'],
            'target_stage' => ['nullable', 'string', 'max:120'],
        ]);

        $sourceStage = $this->resolveStage($data['source_stage']);
        $targetStage = filled($data['target_stage'] ?? null)
            ? $this->resolveStage($data['target_stage'])
            : null;

        if (!$sourceStage) {
            return response()->json([
                'success' => false,
                'message' => 'Quell-Phase wurde nicht gefunden.',
                'stages' => $this->availableStages(),
            ], 404);
        }

        if (filled($data['target_stage'] ?? null) && !$targetStage) {
            return response()->json([
                'success' => false,
                'message' => 'Ziel-Phase wurde nicht gefunden.',
                'stages' => $this->availableStages(),
            ], 404);
        }

        $count = $this->leadProductsForStageQuery($sourceStage)->count();

        return response()->json([
            'success' => true,
            'count' => $count,
            'source_stage' => $this->stagePayload($sourceStage),
            'target_stage' => $targetStage ? $this->stagePayload($targetStage) : null,
            'target_sub_stages' => $targetStage ? $this->subStagePayloads($targetStage) : [],
            'stages' => $this->availableStages(),
        ]);
    }

    public function move(Request $request): JsonResponse
    {
        $data = $request->validate([
            'source_stage' => ['required', 'string', 'max:120'],
            'target_stage' => ['required', 'string', 'max:120'],
            'target_sub_stage_id' => ['nullable', 'integer'],
            'reason' => ['nullable', 'string', 'max:500'],
            'move_related_tasks' => ['nullable', 'boolean'],
            'confirm' => ['nullable', 'boolean'],
        ]);

        $sourceStage = $this->resolveStage($data['source_stage']);
        $targetStage = $this->resolveStage($data['target_stage']);

        if (!$sourceStage) {
            return response()->json([
                'success' => false,
                'message' => 'Quell-Phase wurde nicht gefunden.',
                'stages' => $this->availableStages(),
            ], 404);
        }

        if (!$targetStage) {
            return response()->json([
                'success' => false,
                'message' => 'Ziel-Phase wurde nicht gefunden.',
                'stages' => $this->availableStages(),
            ], 404);
        }

        if ((int) $sourceStage->id === (int) $targetStage->id) {
            return response()->json([
                'success' => false,
                'message' => 'Quelle und Ziel dürfen nicht dieselbe Phase sein.',
            ], 422);
        }

        $count = $this->leadProductsForStageQuery($sourceStage)->count();

        if ($count <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'In dieser Phase gibt es keine Einträge zum Verschieben.',
                'count' => 0,
                'stages' => $this->availableStages(),
            ], 422);
        }

        if (!$request->boolean('confirm')) {
            return response()->json([
                'success' => false,
                'requires_confirmation' => true,
                'message' => "Es werden {$count} Einträge verschoben. Bitte bestätigen.",
                'count' => $count,
                'source_stage' => $this->stagePayload($sourceStage),
                'target_stage' => $this->stagePayload($targetStage),
                'target_sub_stages' => $this->subStagePayloads($targetStage),
                'stages' => $this->availableStages(),
            ], 409);
        }

        try {
            DB::beginTransaction();

            $targetSubStageId = $this->resolveTargetSubStageId(
                $targetStage,
                $data['target_sub_stage_id'] ?? null
            );

            $moved = $this->moveLeadProducts(
                sourceStage: $sourceStage,
                targetStage: $targetStage,
                targetSubStageId: $targetSubStageId,
                reason: trim((string) ($data['reason'] ?? ''))
            );

            if ($request->boolean('move_related_tasks', true)) {
                $this->moveRelatedRows(
                    sourceStage: $sourceStage,
                    targetStage: $targetStage,
                    targetSubStageId: $targetSubStageId
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$moved} Einträge wurden verschoben.",
                'moved_count' => $moved,
                'source_stage' => $this->stagePayload($sourceStage),
                'target_stage' => $this->stagePayload($targetStage),
                'target_sub_stage_id' => $targetSubStageId,
                'stages' => $this->availableStages(),
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('Bulk stage move failed', [
                'source_stage' => $sourceStage->key,
                'target_stage' => $targetStage->key,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bulk-Verschiebung fehlgeschlagen: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function availableStages(): array
    {
        return LeadStage::query()
            ->with([
                'subStages' => function ($query) {
                    $query->where('is_active', true)
                        ->orderByDesc('is_default')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                },
            ])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn(LeadStage $stage) => array_merge(
                $this->stagePayload($stage),
                [
                    'sub_stages' => $stage->subStages
                        ->map(fn(LeadStageSubStage $subStage) => [
                            'id' => $subStage->id,
                            'lead_stage_id' => $subStage->lead_stage_id,
                            'key' => $subStage->key,
                            'name' => $subStage->name,
                            'label' => $subStage->name,
                            'is_default' => (bool) $subStage->is_default,
                            'is_active' => (bool) $subStage->is_active,
                        ])
                        ->values()
                        ->all(),
                ]
            ))
            ->values()
            ->all();
    }

    private function moveLeadProducts(
        LeadStage $sourceStage,
        LeadStage $targetStage,
        ?int $targetSubStageId,
        string $reason = ''
    ): int {
        $moved = 0;

        $this->leadProductsForStageQuery($sourceStage)
            ->orderBy('id')
            ->chunkById(200, function ($leads) use ($sourceStage, $targetStage, $targetSubStageId, $reason, &$moved) {
                foreach ($leads as $lead) {
                    $oldStage = $lead->status ?: ($lead->stage ?? null);
                    $oldSubStageId = $lead->lead_stage_sub_stage_id ?? null;
                    $history = $this->decodeHistory($lead->stage_history ?? null);

                    $history[] = [
                        'mode' => 'bulk_stage_transfer',
                        'from' => $sourceStage->key,
                        'from_name' => $sourceStage->name,
                        'to' => $targetStage->key,
                        'to_name' => $targetStage->name,
                        'from_sub_stage_id' => $oldSubStageId,
                        'to_sub_stage_id' => $targetSubStageId,
                        'reason' => $reason,
                        'changed_by' => auth()->user()?->name,
                        'changed_user_id' => auth()->id(),
                        'changed_at' => now()->toDateTimeString(),
                    ];

                    $update = [];

                    if (Schema::hasColumn('lead_product_lists', 'old_stage')) {
                        $update['old_stage'] = $oldStage;
                    }

                    if (Schema::hasColumn('lead_product_lists', 'status')) {
                        $update['status'] = $targetStage->key;
                    }

                    if (Schema::hasColumn('lead_product_lists', 'stage')) {
                        $update['stage'] = $targetStage->key;
                    }

                    if (Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
                        $update['lead_stage_sub_stage_id'] = $targetSubStageId;
                    }

                    if (Schema::hasColumn('lead_product_lists', 'stage_history')) {
                        $update['stage_history'] = $history;
                    }

                    if (!empty($update)) {
                        $lead->forceFill($update)->save();
                        $moved++;
                    }
                }
            });

        return $moved;
    }

    private function moveRelatedRows(
        LeadStage $sourceStage,
        LeadStage $targetStage,
        ?int $targetSubStageId
    ): void {
        $sourceSubStageIds = $this->childSubStageIds($sourceStage);

        foreach (['task_phases', 'kanban_lead_tasks', 'personal_tasks'] as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $stageColumn = Schema::hasColumn($table, 'lead_stage_id') ? 'lead_stage_id' : null;
            $subStageColumn = $this->firstExistingColumn($table, [
                'lead_sub_stage_id',
                'lead_stage_sub_stage_id',
            ]);

            if (!$stageColumn && !$subStageColumn) {
                continue;
            }

            $update = [];

            if ($stageColumn) {
                $update[$stageColumn] = $targetStage->id;
            }

            if ($subStageColumn) {
                $update[$subStageColumn] = $targetSubStageId;
            }

            if (Schema::hasColumn($table, 'updated_at')) {
                $update['updated_at'] = now();
            }

            DB::table($table)
                ->when(Schema::hasColumn($table, 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
                ->where(function ($q) use ($stageColumn, $subStageColumn, $sourceStage, $sourceSubStageIds) {
                    if ($stageColumn) {
                        $q->orWhere($stageColumn, $sourceStage->id);
                    }

                    if ($subStageColumn && !empty($sourceSubStageIds)) {
                        $q->orWhereIn($subStageColumn, $sourceSubStageIds);
                    }
                })
                ->update($update);
        }
    }

    private function leadProductsForStageQuery(LeadStage $stage)
    {
        $aliases = array_values(array_filter($this->stageAliases($stage), fn($value) => $value !== ''));
        $subStageIds = $this->childSubStageIds($stage);

        return LeadProductList::query()
            ->when(Schema::hasColumn('lead_product_lists', 'deleted_at'), fn($q) => $q->whereNull('deleted_at'))
            ->where(function ($q) use ($aliases, $subStageIds) {
                $hasCondition = false;

                if (Schema::hasColumn('lead_product_lists', 'status') && !empty($aliases)) {
                    $q->orWhereIn(
                        DB::raw('LOWER(COALESCE(NULLIF(status, ""), "lead"))'),
                        $aliases
                    );
                    $hasCondition = true;
                }

                if (Schema::hasColumn('lead_product_lists', 'stage') && !empty($aliases)) {
                    $q->orWhereIn(
                        DB::raw('LOWER(COALESCE(NULLIF(stage, ""), "lead"))'),
                        $aliases
                    );
                    $hasCondition = true;
                }

                if (Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id') && !empty($subStageIds)) {
                    $q->orWhereIn('lead_stage_sub_stage_id', $subStageIds);
                    $hasCondition = true;
                }

                if (!$hasCondition) {
                    $q->whereRaw('1 = 0');
                }
            });
    }

    private function resolveStage(string $value): ?LeadStage
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return LeadStage::query()
                ->whereKey((int) $value)
                ->where('is_active', true)
                ->first();
        }

        $key = $this->canonicalStageKey($value);

        return LeadStage::query()
            ->where('is_active', true)
            ->where(function ($query) use ($value, $key) {
                $query->where('key', $key)
                    ->orWhere('key', $value);
            })
            ->first();
    }

    private function resolveTargetSubStageId(LeadStage $targetStage, mixed $subStageId): ?int
    {
        $subStageId = (int) ($subStageId ?: 0);

        if ($subStageId <= 0) {
            return $this->defaultSubStageId($targetStage);
        }

        $exists = LeadStageSubStage::query()
            ->whereKey($subStageId)
            ->where('lead_stage_id', $targetStage->id)
            ->where('is_active', true)
            ->exists();

        if (!$exists) {
            abort(response()->json([
                'success' => false,
                'message' => 'Die gewählte Ziel-Unterphase gehört nicht zur Ziel-Phase.',
            ], 422));
        }

        return $subStageId;
    }

    private function defaultSubStageId(LeadStage $stage): ?int
    {
        if (!Schema::hasTable('lead_stage_sub_stages')) {
            return null;
        }

        return LeadStageSubStage::query()
            ->where('lead_stage_id', $stage->id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');
    }

    private function childSubStageIds(LeadStage $stage): array
    {
        if (!Schema::hasTable('lead_stage_sub_stages')) {
            return [];
        }

        return LeadStageSubStage::withTrashed()
            ->where('lead_stage_id', $stage->id)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter()
            ->values()
            ->all();
    }

    private function subStagePayloads(LeadStage $stage): array
    {
        if (!Schema::hasTable('lead_stage_sub_stages')) {
            return [];
        }

        return LeadStageSubStage::query()
            ->where('lead_stage_id', $stage->id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn(LeadStageSubStage $subStage) => [
                'id' => $subStage->id,
                'lead_stage_id' => $subStage->lead_stage_id,
                'key' => $subStage->key,
                'name' => $subStage->name,
                'label' => $subStage->name,
                'is_default' => (bool) $subStage->is_default,
                'is_active' => (bool) $subStage->is_active,
            ])
            ->values()
            ->all();
    }

    private function stageAliases(LeadStage $stage): array
    {
        $key = strtolower(trim((string) $stage->key));
        $nameKey = $this->makeKey((string) $stage->name);

        $aliases = match ($key) {
            'lead' => ['lead', 'open', 'new', 'neu', 'neue', ''],
            'offer' => ['offer', 'angebot', 'verkauf'],
            'follow_up' => ['follow_up', 'followup', 'nachfassen'],
            'accepted' => ['accepted', 'annehmen', 'annemen', 'angenommen'],
            'deal' => ['deal', 'auftrag'],
            'project' => ['project', 'projekt', 'montage'],
            'completed' => ['completed', 'complete', 'abschluss'],
            'archive' => ['archive', 'archiv'],
            'junk' => ['junk', 'reject', 'rejeck'],
            default => [$key, $nameKey],
        };

        return collect($aliases)
            ->map(fn($value) => strtolower(trim((string) $value)))
            ->unique()
            ->values()
            ->all();
    }

    private function canonicalStageKey(string $value): string
    {
        $key = $this->makeKey($value);

        return match ($key) {
            'open', 'new', 'neu', 'neue', 'lead' => 'lead',
            'angebot', 'verkauf', 'offer' => 'offer',
            'nachfassen', 'followup', 'follow_up' => 'follow_up',
            'annehmen', 'annemen', 'angenommen', 'accepted' => 'accepted',
            'auftrag', 'deal' => 'deal',
            'montage', 'projekt', 'project' => 'project',
            'abschluss', 'complete', 'completed' => 'completed',
            'archive', 'archiv' => 'archive',
            'junk', 'reject', 'rejeck' => 'junk',
            'ticket' => 'ticket',
            default => $key,
        };
    }

    private function makeKey(string $value): string
    {
        $key = Str::slug($value, '_');
        $key = strtolower(trim($key, '_'));

        return $key !== '' ? $key : 'stage';
    }

    private function decodeHistory(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function firstExistingColumn(string $table, array $columns): ?string
    {
        foreach ($columns as $column) {
            if (Schema::hasColumn($table, $column)) {
                return $column;
            }
        }

        return null;
    }

    private function stagePayload(LeadStage $stage): array
    {
        return [
            'id' => $stage->id,
            'key' => $stage->key,
            'value' => $stage->key,
            'name' => $stage->name,
            'label' => $stage->name,
            'color' => $stage->color ?: '#74b2d4',
            'icon' => $stage->icon ?: 'circle',
            'sort_order' => (int) $stage->sort_order,
            'is_default' => (bool) $stage->is_default,
            'is_protected' => (bool) $stage->is_protected,
            'is_closed' => (bool) $stage->is_closed,
            'is_active' => (bool) $stage->is_active,
        ];
    }
}