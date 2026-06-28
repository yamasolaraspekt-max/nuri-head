<?php

namespace App\Http\Controllers\Customer\Kanban;

use App\Http\Controllers\Controller;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class LeadStageSubStageController extends Controller
{
    public function index(Request $request, LeadStage $stage)
    {
        $stage->load([
            'subStages' => function ($query) use ($request) {
                $query->when(!$request->boolean('include_inactive'), function ($q) {
                    $q->where('is_active', true);
                })->orderBy('sort_order')->orderBy('id');
            },
        ]);

        $subStages = $stage->subStages;

        if ($this->wantsJson($request)) {
            return response()->json($this->stagePayload($stage, $subStages));
        }

        return view('admin.kanban.stage-sub-stages.index', compact('stage', 'subStages'));
    }

    public function store(Request $request, LeadStage $stage)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('lead_stage_sub_stages', 'key')
                    ->where('lead_stage_id', $stage->id)
                    ->whereNull('deleted_at'),
            ],
            'color' => ['nullable', 'string', 'max:40'],
            'icon' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $key = $this->makeUniqueKey($stage->id, $data['key'] ?? $data['name']);
            $isDefault = $request->boolean('is_default');
            $isActive = $request->has('is_active') ? $request->boolean('is_active') : true;

            if ($isDefault) {
                LeadStageSubStage::query()
                    ->where('lead_stage_id', $stage->id)
                    ->update(['is_default' => false]);
            }

            $sortOrder = $data['sort_order'] ?? (((int) LeadStageSubStage::query()
                ->where('lead_stage_id', $stage->id)
                ->max('sort_order')) + 10);

            $subStage = LeadStageSubStage::create([
                'lead_stage_id' => $stage->id,
                'key' => $key,
                'name' => $data['name'],
                'color' => $data['color'] ?? '#93c21c',
                'icon' => $data['icon'] ?? 'list',
                'sort_order' => $sortOrder,
                'is_default' => $isDefault,
                'is_active' => $isActive,
            ]);

            DB::commit();

            if ($this->wantsJson($request)) {
                return response()->json(array_merge($this->stagePayload($stage->fresh(), $this->subStagesForPayload($stage->id, true)), [
                    'message' => 'Unterphase wurde erstellt.',
                    'sub_stage' => $this->formatSubStage($subStage->fresh()),
                ]));
            }

            return redirect()
                ->route('admin.kanban.stages.sub-stages.index', $stage->id)
                ->with('success', 'Unterphase wurde erstellt.');
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('LeadStageSubStage store failed', [
                'stage_id' => $stage->id,
                'error' => $e->getMessage(),
            ]);

            if ($this->wantsJson($request)) {
                return $this->jsonError('Unterphase konnte nicht erstellt werden: ' . $e->getMessage(), 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Unterphase konnte nicht erstellt werden.');
        }
    }

    public function update(Request $request, LeadStageSubStage $subStage)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('lead_stage_sub_stages', 'key')
                    ->where('lead_stage_id', $subStage->lead_stage_id)
                    ->whereNull('deleted_at')
                    ->ignore($subStage->id),
            ],
            'color' => ['nullable', 'string', 'max:40'],
            'icon' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'is_default' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $key = filled($data['key'] ?? null)
                ? Str::slug($data['key'], '_')
                : Str::slug($data['name'], '_');

            $key = $this->makeUniqueKey($subStage->lead_stage_id, $key, $subStage->id);
            $isDefault = $request->has('is_default') ? $request->boolean('is_default') : (bool) $subStage->is_default;
            $isActive = $request->has('is_active') ? $request->boolean('is_active') : (bool) $subStage->is_active;

            if ($isDefault) {
                LeadStageSubStage::query()
                    ->where('lead_stage_id', $subStage->lead_stage_id)
                    ->where('id', '!=', $subStage->id)
                    ->update(['is_default' => false]);
            }

            $subStage->update([
                'key' => $key,
                'name' => $data['name'],
                'color' => $data['color'] ?? $subStage->color ?? '#93c21c',
                'icon' => $data['icon'] ?? $subStage->icon ?? 'list',
                'sort_order' => $data['sort_order'] ?? $subStage->sort_order,
                'is_default' => $isDefault,
                'is_active' => $isActive,
            ]);

            DB::commit();

            if ($this->wantsJson($request)) {
                $stage = LeadStage::find($subStage->lead_stage_id);

                return response()->json(array_merge($this->stagePayload($stage, $this->subStagesForPayload($subStage->lead_stage_id, true)), [
                    'message' => 'Unterphase wurde aktualisiert.',
                    'sub_stage' => $this->formatSubStage($subStage->fresh()),
                ]));
            }

            return back()->with('success', 'Unterphase wurde aktualisiert.');
        } catch (Throwable $e) {
            DB::rollBack();

            Log::error('LeadStageSubStage update failed', [
                'sub_stage_id' => $subStage->id,
                'error' => $e->getMessage(),
            ]);

            if ($this->wantsJson($request)) {
                return $this->jsonError('Unterphase konnte nicht aktualisiert werden: ' . $e->getMessage(), 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Unterphase konnte nicht aktualisiert werden.');
        }
    }

    public function destroy(Request $request, LeadStageSubStage $subStage)
    {
        try {
            $stageId = $subStage->lead_stage_id;
            $subStage->delete();

            if ($this->wantsJson($request)) {
                $stage = LeadStage::find($stageId);

                return response()->json(array_merge($this->stagePayload($stage, $this->subStagesForPayload($stageId, true)), [
                    'message' => 'Unterphase wurde gelöscht.',
                ]));
            }

            return redirect()
                ->route('admin.kanban.stages.sub-stages.index', $stageId)
                ->with('success', 'Unterphase wurde gelöscht.');
        } catch (Throwable $e) {
            Log::error('LeadStageSubStage destroy failed', [
                'sub_stage_id' => $subStage->id,
                'error' => $e->getMessage(),
            ]);

            if ($this->wantsJson($request)) {
                return $this->jsonError('Unterphase konnte nicht gelöscht werden: ' . $e->getMessage(), 500);
            }

            return back()->with('error', 'Unterphase konnte nicht gelöscht werden.');
        }
    }

    public function toggle(Request $request, LeadStageSubStage $subStage)
    {
        $subStage->update([
            'is_active' => !$subStage->is_active,
        ]);

        if ($this->wantsJson($request)) {
            $stage = LeadStage::find($subStage->lead_stage_id);

            return response()->json(array_merge($this->stagePayload($stage, $this->subStagesForPayload($subStage->lead_stage_id, true)), [
                'message' => 'Status wurde geändert.',
                'sub_stage' => $this->formatSubStage($subStage->fresh()),
            ]));
        }

        return back()->with('success', 'Status wurde geändert.');
    }

    public function makeDefault(Request $request, LeadStageSubStage $subStage)
    {
        DB::transaction(function () use ($subStage) {
            LeadStageSubStage::query()
                ->where('lead_stage_id', $subStage->lead_stage_id)
                ->update(['is_default' => false]);

            $subStage->update([
                'is_default' => true,
                'is_active' => true,
            ]);
        });

        if ($this->wantsJson($request)) {
            $stage = LeadStage::find($subStage->lead_stage_id);

            return response()->json(array_merge($this->stagePayload($stage, $this->subStagesForPayload($subStage->lead_stage_id, true)), [
                'message' => 'Standard-Unterphase wurde gesetzt.',
                'sub_stage' => $this->formatSubStage($subStage->fresh()),
            ]));
        }

        return back()->with('success', 'Standard-Unterphase wurde gesetzt.');
    }

    public function reorder(Request $request, LeadStage $stage)
    {
        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:lead_stage_sub_stages,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($data, $stage) {
            foreach ($data['items'] as $item) {
                LeadStageSubStage::query()
                    ->where('id', $item['id'])
                    ->where('lead_stage_id', $stage->id)
                    ->update([
                        'sort_order' => $item['sort_order'],
                    ]);
            }
        });

        return response()->json(array_merge($this->stagePayload($stage->fresh(), $this->subStagesForPayload($stage->id, true)), [
            'message' => 'Reihenfolge wurde gespeichert.',
        ]));
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson() || $request->ajax() || $request->wantsJson();
    }

    private function jsonError(string $message, int $status = 422, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
        ], $extra), $status);
    }

    private function subStagesForPayload(int $stageId, bool $includeInactive = false)
    {
        return LeadStageSubStage::query()
            ->where('lead_stage_id', $stageId)
            ->whereNull('deleted_at')
            ->when(!$includeInactive, fn($query) => $query->where('is_active', true))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function stagePayload(?LeadStage $stage, $subStages): array
    {
        return [
            'success' => true,
            'stage' => $stage ? [
                'id' => $stage->id,
                'key' => $stage->key,
                'name' => $stage->name,
                'label' => $stage->name,
                'color' => $stage->color,
                'icon' => $stage->icon,
                'sort_order' => $stage->sort_order,
                'is_active' => (bool) $stage->is_active,
            ] : null,
            'sub_stages' => collect($subStages)->map(fn($subStage) => $this->formatSubStage($subStage))->values()->all(),
            // Alias used by the offer-folder Blade.
            'stages' => collect($subStages)->map(fn($subStage) => $this->formatSubStage($subStage))->values()->all(),
        ];
    }

    private function formatSubStage(LeadStageSubStage $subStage): array
    {
        return [
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
            'source' => 'lead_stage_sub_stages',
        ];
    }

    private function makeUniqueKey(int $stageId, string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value, '_') ?: 'unterphase';
        $key = $base;
        $counter = 2;

        while (
            LeadStageSubStage::query()
                ->where('lead_stage_id', $stageId)
                ->where('key', $key)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->whereNull('deleted_at')
                ->exists()
        ) {
            $key = $base . '_' . $counter;
            $counter++;
        }

        return $key;
    }
}
