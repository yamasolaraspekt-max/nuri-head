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

class LeadStageController extends Controller
{
    public function index(): JsonResponse
    {
        $stages = LeadStage::query()
            ->with([
                'subStages' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('id');
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn(LeadStage $stage) => $this->stagePayload($stage));

        return response()->json([
            'success' => true,
            'stages' => $stages,
        ]);
    }

    /**
     * HTML-Verwaltungsseite fuer Lead-Phasen.
     * Nutzt dieselbe Nutzlast wie index(); alle Mutationen laufen ueber die
     * bestehende JSON-API (store/update/destroy/reorder).
     */
    public function manage()
    {
        $stages = LeadStage::query()
            ->with([
                'subStages' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('id');
                },
            ])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn(LeadStage $stage) => $this->stagePayload($stage))
            ->values();

        return view('admin.lead_stages.manage', ['stages' => $stages]);
    }

    public function store(Request $request): JsonResponse
    {
        /*
         |--------------------------------------------------------------------------
         | Safety net for broken frontend edit mode
         |--------------------------------------------------------------------------
         | If the edit form accidentally posts to the create endpoint, do NOT create
         | a duplicate default stage. Update the existing row by id or original_key.
         */
        $editStageId = $request->input('stage_id')
            ?: $request->input('id')
            ?: $request->input('lsm_stage_id');

        if ($editStageId) {
            return $this->update($request, $editStageId);
        }

        $originalKey = strtolower(trim((string) (
            $request->input('original_key')
            ?: $request->input('stage_key')
            ?: $request->input('current_key')
            ?: ''
        )));

        if ($originalKey !== '') {
            $existingByOriginalKey = LeadStage::withTrashed()
                ->where('key', $originalKey)
                ->first();

            if ($existingByOriginalKey) {
                return $this->update($request, $existingByOriginalKey->id);
            }
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:60'],
            'is_closed' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $name = trim((string) $data['name']);
        $rawKey = $this->makeKey($name);
        $canonicalKey = $this->canonicalStageKey($rawKey);

        /*
         |--------------------------------------------------------------------------
         | Reserved/default stage labels must not create duplicate stages
         |--------------------------------------------------------------------------
         | If a user types "Annehmen", "Annemen", "Angenommen", etc. we treat it as
         | the existing accepted stage. If that existing stage is active, the user
         | must edit it. If it is trashed, restore and update it.
         */
        if ($canonicalKey !== $rawKey || $this->isReservedCanonicalStageKey($canonicalKey)) {
            $existing = LeadStage::withTrashed()
                ->where('key', $canonicalKey)
                ->first();

            if ($existing && !$existing->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => "Diese Phase existiert bereits als „{$existing->name}”. Bitte diese Phase bearbeiten statt eine neue zu erstellen.",
                    'stage' => $this->stagePayload($existing),
                ], 422);
            }

            if ($existing && $existing->trashed()) {
                $existing->restore();

                return $this->update($request, $existing->id);
            }

            $key = $canonicalKey;
        } else {
            $key = $rawKey;
            $counter = 2;

            while (LeadStage::withTrashed()->where('key', $key)->exists()) {
                $key = $rawKey . '_' . $counter;
                $counter++;
            }
        }

        $maxOrder = (int) (LeadStage::query()->max('sort_order') ?? 0);

        $stage = LeadStage::create([
            'key' => $key,
            'name' => $name,
            'color' => $data['color'] ?? '#74b2d4',
            'icon' => $data['icon'] ?? 'circle',
            'sort_order' => $maxOrder + 10,
            'is_default' => false,
            'is_protected' => false,
            'is_closed' => $request->boolean('is_closed'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Phase wurde erstellt.',
            'stage' => $this->stagePayload($stage->fresh(['subStages'])),
        ]);
    }

    public function show(mixed $stage): JsonResponse
    {
        if (!$stage instanceof LeadStage) {
            $stage = LeadStage::withTrashed()
                ->with([
                    'subStages' => function ($query) {
                        $query->orderBy('sort_order')->orderBy('id');
                    },
                ])
                ->whereKey((int) $stage)
                ->first();
        }

        if (!$stage) {
            return response()->json([
                'success' => false,
                'message' => 'Phase wurde nicht gefunden.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'stage' => $this->stagePayload($stage),
        ]);
    }

    public function update(Request $request, mixed $stage): JsonResponse
    {
        /*
         |--------------------------------------------------------------------------
         | Protected/default stages are editable
         |--------------------------------------------------------------------------
         | Protection means: do not casually delete the technical phase.
         | It does NOT mean: block name/color/icon/is_active changes.
         */
        if (!$stage instanceof LeadStage) {
            $stage = LeadStage::withTrashed()->whereKey((int) $stage)->first();
        }

        if (!$stage) {
            $originalKey = strtolower(trim((string) (
                $request->input('original_key')
                ?: $request->input('stage_key')
                ?: $request->input('current_key')
                ?: ''
            )));

            if ($originalKey !== '') {
                $stage = LeadStage::withTrashed()->where('key', $originalKey)->first();
            }
        }

        if (!$stage) {
            return response()->json([
                'success' => false,
                'message' => 'Phase wurde nicht gefunden.',
            ], 404);
        }

        if (method_exists($stage, 'trashed') && $stage->trashed()) {
            $stage->restore();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_closed' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        /*
         * Never update the technical key from the new name.
         *
         * Correct example:
         *   key  = accepted
         *   name = Gewonnen
         *
         * lead_product_lists.status stores key, so key must remain stable.
         */
        $stage->forceFill([
            'name' => trim((string) $data['name']),
            'color' => $data['color'] ?? $stage->color ?? '#74b2d4',
            'icon' => $data['icon'] ?? $stage->icon ?? 'circle',
            'sort_order' => $data['sort_order'] ?? $stage->sort_order,
            'is_closed' => $request->has('is_closed') ? $request->boolean('is_closed') : (bool) $stage->is_closed,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : (bool) $stage->is_active,

            /*
             * Keep protection/default metadata unchanged.
             * Only the editable presentation/workflow flags are changed.
             */
            'is_default' => (bool) $stage->is_default,
            'is_protected' => (bool) $stage->is_protected,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Phase wurde aktualisiert.',
            'stage' => $this->stagePayload($stage->fresh(['subStages'])),
        ]);
    }

    public function destroy(Request $request, LeadStage $stage): JsonResponse
    {
        $targetStage = $this->previousStageFor($stage);
        $usage = $this->usageDetails($stage);

        if (!$targetStage) {
            return response()->json([
                'success' => false,
                'blocked' => true,
                'message' => 'Diese Phase kann nicht gelöscht werden, weil es keine vorherige Phase gibt.',
                'usage' => $usage,
                'usage_count' => $usage['total'],
            ], 422);
        }

        /*
         * First request should return confirmation data.
         * Your JS should show SweetAlert and then call DELETE again with:
         * {
         *   move_to_previous: true,
         *   force_delete_protected: true
         * }
         */
        if ($usage['total'] > 0 && !$request->boolean('move_to_previous')) {
            return response()->json([
                'success' => false,
                'blocked' => true,
                'requires_transfer' => true,
                'message' => "Diese Phase enthält {$usage['total']} Einträge. Diese Einträge müssen zuerst in die vorherige Phase verschoben werden.",
                'usage' => $usage,
                'usage_count' => $usage['total'],
                'source_stage' => $this->stagePayload($stage),
                'target_stage' => $this->stagePayload($targetStage),
            ], 409);
        }

        if ($stage->is_protected && !$request->boolean('force_delete_protected')) {
            return response()->json([
                'success' => false,
                'blocked' => true,
                'requires_protected_confirmation' => true,
                'requires_transfer' => true,
                'message' => 'Diese Phase ist geschützt. Wenn du sie wirklich löschen willst, werden alle Daten in die vorherige Phase verschoben.',
                'usage' => $usage,
                'usage_count' => $usage['total'],
                'source_stage' => $this->stagePayload($stage),
                'target_stage' => $this->stagePayload($targetStage),
            ], 409);
        }

        try {
            DB::beginTransaction();

            $this->moveStageDataToPreviousStage($stage, $targetStage);

            LeadStageSubStage::query()
                ->where('lead_stage_id', $stage->id)
                ->delete();

            $wasDefault = (bool) $stage->is_default;

            $stage->delete();

            if ($wasDefault) {
                LeadStage::query()
                    ->whereKey($targetStage->id)
                    ->update(['is_default' => true]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Phase wurde gelöscht. Alle Daten wurden in die vorherige Phase verschoben.',
                'target_stage' => $this->stagePayload($targetStage->fresh(['subStages'])),
            ]);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('LeadStage destroy failed', [
                'stage_id' => $stage->id,
                'stage_key' => $stage->key,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Phase konnte nicht gelöscht werden: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:lead_stages,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['items'] as $item) {
                LeadStage::query()
                    ->whereKey($item['id'])
                    ->update([
                        'sort_order' => (int) $item['sort_order'],
                    ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Sortierung wurde gespeichert.',
        ]);
    }

    private function moveStageDataToPreviousStage(LeadStage $fromStage, LeadStage $toStage): void
    {
        $targetSubStageId = $this->defaultSubStageIdForStage($toStage);
        $fromSubStageIds = $this->childSubStageIds($fromStage);

        $this->leadProductsForStageQuery($fromStage)
            ->orderBy('id')
            ->chunkById(200, function ($leads) use ($fromStage, $toStage, $targetSubStageId) {
                foreach ($leads as $lead) {
                    $history = $this->decodeHistory($lead->stage_history ?? null);

                    $oldStage = $lead->status ?? $lead->stage ?? null;

                    $history[] = [
                        'mode' => 'stage_deleted_transfer',
                        'from' => $fromStage->key,
                        'from_name' => $fromStage->name,
                        'to' => $toStage->key,
                        'to_name' => $toStage->name,
                        'changed_by' => auth()->user()?->name,
                        'changed_user_id' => auth()->id(),
                        'changed_at' => now()->toDateTimeString(),
                        'description' => 'Phase gelöscht, Daten automatisch in die vorherige Phase verschoben.',
                    ];

                    $update = [];

                    if (Schema::hasColumn('lead_product_lists', 'old_stage')) {
                        $update['old_stage'] = $oldStage;
                    }

                    if (Schema::hasColumn('lead_product_lists', 'status')) {
                        $update['status'] = $toStage->key;
                    }

                    if (Schema::hasColumn('lead_product_lists', 'stage')) {
                        $update['stage'] = $toStage->key;
                    }

                    // Stufe-A-Kanon (changeStage, 9e0d64a): FK-Wahrheit lead_stage_id MUSS mit status
                    // mitwandern. Ohne das zeigt sie nach dem (Soft-)Delete auf die geloeschte Phase
                    // (FK ist nullOnDelete, feuert bei SoftDelete aber nie). Es gibt keinen zentralen
                    // Model-Hook, der das abfaengt (Stufe B geparkt) -> hier explizit setzen.
                    if (Schema::hasColumn('lead_product_lists', 'lead_stage_id')) {
                        $update['lead_stage_id'] = $toStage->id;
                    }

                    if (Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
                        $update['lead_stage_sub_stage_id'] = $targetSubStageId;
                    }

                    if (Schema::hasColumn('lead_product_lists', 'stage_history')) {
                        $update['stage_history'] = $history;
                    }

                    if (!empty($update)) {
                        $lead->forceFill($update)->save();
                    }
                }
            });

        $this->moveRelatedStageReferences($fromStage, $toStage, $fromSubStageIds, $targetSubStageId);
    }

    private function moveRelatedStageReferences(
        LeadStage $fromStage,
        LeadStage $toStage,
        array $fromSubStageIds,
        ?int $targetSubStageId
    ): void {
        $tables = [
            'task_phases',
            'kanban_lead_tasks',
            'personal_tasks',
        ];

        foreach ($tables as $table) {
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
                $update[$stageColumn] = $toStage->id;
            }

            if ($subStageColumn) {
                $update[$subStageColumn] = $targetSubStageId;
            }

            if (Schema::hasColumn($table, 'updated_at')) {
                $update['updated_at'] = now();
            }

            if (empty($update)) {
                continue;
            }

            DB::table($table)
                ->when(Schema::hasColumn($table, 'deleted_at'), fn($query) => $query->whereNull('deleted_at'))
                ->where(function ($query) use ($stageColumn, $subStageColumn, $fromStage, $fromSubStageIds) {
                    if ($stageColumn) {
                        $query->orWhere($stageColumn, $fromStage->id);
                    }

                    if ($subStageColumn && !empty($fromSubStageIds)) {
                        $query->orWhereIn($subStageColumn, $fromSubStageIds);
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
            ->when(Schema::hasColumn('lead_product_lists', 'deleted_at'), fn($query) => $query->whereNull('deleted_at'))
            ->where(function ($query) use ($aliases, $subStageIds) {
                $hasCondition = false;

                if (!empty($aliases) && Schema::hasColumn('lead_product_lists', 'status')) {
                    $query->orWhereIn(
                        DB::raw('LOWER(COALESCE(NULLIF(status, ""), "lead"))'),
                        $aliases
                    );
                    $hasCondition = true;
                }

                if (!empty($aliases) && Schema::hasColumn('lead_product_lists', 'stage')) {
                    $query->orWhereIn(
                        DB::raw('LOWER(COALESCE(NULLIF(stage, ""), "lead"))'),
                        $aliases
                    );
                    $hasCondition = true;
                }

                if (!empty($subStageIds) && Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
                    $query->orWhereIn('lead_stage_sub_stage_id', $subStageIds);
                    $hasCondition = true;
                }

                if (!$hasCondition) {
                    $query->whereRaw('1 = 0');
                }
            });
    }

    private function usageDetails(LeadStage $stage): array
    {
        $leadProducts = Schema::hasTable('lead_product_lists')
            ? $this->leadProductsForStageQuery($stage)->count()
            : 0;

        $fromSubStageIds = $this->childSubStageIds($stage);

        $taskPhases = $this->relatedStageUsageCount('task_phases', $stage, $fromSubStageIds);
        $kanbanTasks = $this->relatedStageUsageCount('kanban_lead_tasks', $stage, $fromSubStageIds);
        $personalTasks = $this->relatedStageUsageCount('personal_tasks', $stage, $fromSubStageIds);

        return [
            'lead_products' => $leadProducts,
            'task_phases' => $taskPhases,
            'kanban_tasks' => $kanbanTasks,
            'personal_tasks' => $personalTasks,
            'total' => $leadProducts + $taskPhases + $kanbanTasks + $personalTasks,
        ];
    }

    private function relatedStageUsageCount(string $table, LeadStage $stage, array $subStageIds): int
    {
        if (!Schema::hasTable($table)) {
            return 0;
        }

        $stageColumn = Schema::hasColumn($table, 'lead_stage_id') ? 'lead_stage_id' : null;
        $subStageColumn = $this->firstExistingColumn($table, [
            'lead_sub_stage_id',
            'lead_stage_sub_stage_id',
        ]);

        if (!$stageColumn && !$subStageColumn) {
            return 0;
        }

        return DB::table($table)
            ->when(Schema::hasColumn($table, 'deleted_at'), fn($query) => $query->whereNull('deleted_at'))
            ->where(function ($query) use ($stageColumn, $subStageColumn, $stage, $subStageIds) {
                if ($stageColumn) {
                    $query->orWhere($stageColumn, $stage->id);
                }

                if ($subStageColumn && !empty($subStageIds)) {
                    $query->orWhereIn($subStageColumn, $subStageIds);
                }
            })
            ->count();
    }

    private function previousStageFor(LeadStage $stage): ?LeadStage
    {
        return LeadStage::query()
            ->where('is_active', true)
            ->whereKeyNot($stage->id)
            ->where(function ($query) use ($stage) {
                $query->where('sort_order', '<', (int) $stage->sort_order)
                    ->orWhere(function ($subQuery) use ($stage) {
                        $subQuery->where('sort_order', (int) $stage->sort_order)
                            ->where('id', '<', $stage->id);
                    });
            })
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->first();
    }

    private function defaultSubStageIdForStage(LeadStage $stage): ?int
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

    private function stageAliases(LeadStage $stage): array
    {
        $key = strtolower(trim((string) $stage->key));
        $nameKey = $this->makeKey((string) $stage->name);

        /*
         * Only canonical default stages get their legacy aliases.
         * If a bad duplicate stage exists with key "annemen",
         * deleting that duplicate should not accidentally move all "accepted" cards.
         */
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

    private function isReservedCanonicalStageKey(string $key): bool
    {
        return in_array($key, [
            'lead',
            'offer',
            'follow_up',
            'accepted',
            'deal',
            'project',
            'completed',
            'archive',
            'junk',
            'ticket',
        ], true);
    }

    private function makeKey(string $name): string
    {
        $key = Str::slug($name, '_');
        $key = strtolower(trim($key, '_'));

        return $key !== '' ? $key : 'stage';
    }

    private function decodeHistory($value): array
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
        $usage = $this->usageDetails($stage);

        $subStages = $stage->relationLoaded('subStages')
            ? $stage->subStages
            : LeadStageSubStage::query()
                ->where('lead_stage_id', $stage->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

        return [
            'id' => $stage->id,
            'key' => $stage->key,
            'name' => $stage->name,
            'label' => $stage->name,
            'color' => $stage->color ?: '#74b2d4',
            'icon' => $stage->icon ?: 'circle',
            'sort_order' => (int) $stage->sort_order,
            'is_default' => (bool) $stage->is_default,
            'is_protected' => (bool) $stage->is_protected,
            'is_closed' => (bool) $stage->is_closed,
            'is_active' => (bool) $stage->is_active,
            'usage_count' => $usage['total'],
            'usage' => $usage,
            'sub_stages' => collect($subStages)
                ->map(fn(LeadStageSubStage $subStage) => [
                    'id' => $subStage->id,
                    'lead_stage_id' => $subStage->lead_stage_id,
                    'lead_stage_sub_stage_id' => $subStage->id,
                    'key' => $subStage->key,
                    'name' => $subStage->name,
                    'label' => $subStage->name,
                    'color' => $subStage->color ?: '#93c21c',
                    'icon' => $subStage->icon ?: 'list',
                    'sort_order' => (int) $subStage->sort_order,
                    'position' => (int) $subStage->sort_order,
                    'is_default' => (bool) $subStage->is_default,
                    'is_active' => (bool) $subStage->is_active,
                ])
                ->values()
                ->all(),
        ];
    }
}