<?php

namespace App\Http\Controllers\Phase;

use App\Http\Controllers\Controller;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
use App\Models\UserRoll;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadStageAdminController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorizeAdministrator('read');

        $stages = LeadStage::query()
            ->with(['subStages'])
            ->ordered()
            ->get()
            ->map(fn(LeadStage $stage) => $this->stagePayload($stage))
            ->values();

        return response()->json([
            'success' => true,
            'stages' => $stages,
        ]);
    }

    public function showStage(LeadStage $leadStage): JsonResponse
    {
        $this->authorizeAdministrator('read');

        return response()->json([
            'success' => true,
            'stage' => $this->stagePayload($leadStage->load('subStages')),
        ]);
    }

    public function storeStage(Request $request): JsonResponse
    {
        $this->authorizeAdministrator('add');

        $request->merge([
            'name' => $request->input('name', $request->input('label', $request->input('title'))),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:80'],
        ]);

        $stage = LeadStage::create([
            'name' => $data['name'],
            'key' => $this->uniqueStageKey($data['key'] ?? $data['name']),
            'color' => $data['color'] ?? '#74b2d4',
            'icon' => $data['icon'] ?? null,
            'sort_order' => ((int) LeadStage::query()->max('sort_order')) + 10,
            'is_default' => $this->requestBool($request, 'is_default', false),
            'is_protected' => $this->requestBool($request, 'is_protected', false),
            'is_closed' => $this->requestBool($request, 'is_closed', false),
            'is_active' => $this->requestBool($request, 'is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'LeadStage wurde erstellt.',
            'stage' => $this->stagePayload($stage->fresh(['subStages'])),
        ]);
    }

    public function updateStage(Request $request, LeadStage $leadStage): JsonResponse
    {
        $this->authorizeAdministrator('update');

        $request->merge([
            'name' => $request->input('name', $request->input('label', $request->input('title'))),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:80'],
        ]);

        $newKey = LeadStage::makeKey($data['key'] ?? $data['name']);

        $exists = LeadStage::withTrashed()
            ->where('key', $newKey)
            ->whereKeyNot($leadStage->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Dieser Key wird bereits verwendet.',
                'errors' => [
                    'key' => ['Dieser Key wird bereits verwendet.'],
                ],
            ], 422);
        }

        $leadStage->update([
            'name' => $data['name'],
            'key' => $newKey,
            'color' => $data['color'] ?? '#74b2d4',
            'icon' => $data['icon'] ?? null,
            'is_default' => $this->requestBool($request, 'is_default', false),
            'is_protected' => $this->requestBool($request, 'is_protected', false),
            'is_closed' => $this->requestBool($request, 'is_closed', false),
            'is_active' => $this->requestBool($request, 'is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'LeadStage wurde aktualisiert.',
            'stage' => $this->stagePayload($leadStage->fresh(['subStages'])),
        ]);
    }

    public function deleteStage(LeadStage $leadStage): JsonResponse
    {
        $this->authorizeAdministrator('delete');

        if ($leadStage->is_protected) {
            return response()->json([
                'success' => false,
                'message' => 'Geschützte LeadStages können nicht gelöscht werden.',
            ], 403);
        }

        if ($leadStage->usageCount() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Diese LeadStage wird bereits von Phasen verwendet. Bitte zuerst Phasen verschieben.',
            ], 409);
        }

        DB::transaction(function () use ($leadStage) {
            LeadStageSubStage::query()
                ->where('lead_stage_id', $leadStage->id)
                ->delete();

            $leadStage->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'LeadStage wurde gelöscht.',
        ]);
    }

    public function reorderStages(Request $request): JsonResponse
    {
        $this->authorizeAdministrator('update');

        $ids = $this->extractOrderIds($request->input('items', $request->input('ids', [])));

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Keine gültigen Stage-IDs empfangen.',
                'errors' => [
                    'items' => ['Die Reihenfolge muss Stage-IDs enthalten.'],
                ],
            ], 422);
        }

        $existingIds = LeadStage::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $missingIds = array_values(array_diff($ids, $existingIds));

        if (!empty($missingIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Einige Stage-IDs existieren nicht.',
                'errors' => [
                    'items' => ['Ungültige Stage-IDs: ' . implode(', ', $missingIds)],
                ],
            ], 422);
        }

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                LeadStage::query()
                    ->whereKey($id)
                    ->update([
                        'sort_order' => ($index + 1) * 10,
                        'updated_at' => now(),
                    ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'LeadStage-Reihenfolge wurde gespeichert.',
        ]);
    }

    public function storeSubStage(Request $request, LeadStage $leadStage): JsonResponse
    {
        $this->authorizeAdministrator('add');

        $request->merge([
            'lead_stage_id' => $leadStage->id,
            'name' => $request->input('name', $request->input('label', $request->input('title'))),
        ]);

        $data = $request->validate([
            'lead_stage_id' => ['required', 'integer', 'exists:lead_stages,id'],
            'name' => ['required', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:80'],
        ]);

        $subStage = LeadStageSubStage::create([
            'lead_stage_id' => (int) $data['lead_stage_id'],
            'name' => $data['name'],
            'key' => $this->uniqueSubStageKey((int) $data['lead_stage_id'], $data['key'] ?? $data['name']),
            'color' => $data['color'] ?? '#93c21c',
            'icon' => $data['icon'] ?? null,
            'sort_order' => ((int) LeadStageSubStage::query()
                ->where('lead_stage_id', (int) $data['lead_stage_id'])
                ->max('sort_order')) + 10,
            'is_default' => $this->requestBool($request, 'is_default', false),
            'is_active' => $this->requestBool($request, 'is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub-Stage wurde erstellt.',
            'sub_stage' => $this->subStagePayload($subStage),
        ]);
    }

    public function showSubStage(LeadStageSubStage $subStage): JsonResponse
    {
        $this->authorizeAdministrator('read');

        return response()->json([
            'success' => true,
            'sub_stage' => $this->subStagePayload($subStage),
        ]);
    }

    public function updateSubStage(Request $request, LeadStageSubStage $subStage): JsonResponse
    {
        $this->authorizeAdministrator('update');

        $request->merge([
            'lead_stage_id' => $request->input('lead_stage_id', $subStage->lead_stage_id),
            'name' => $request->input('name', $request->input('label', $request->input('title'))),
        ]);

        $data = $request->validate([
            'lead_stage_id' => ['required', 'integer', 'exists:lead_stages,id'],
            'name' => ['required', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:30'],
            'icon' => ['nullable', 'string', 'max:80'],
        ]);

        $newKey = LeadStage::makeKey($data['key'] ?? $data['name']);

        $exists = LeadStageSubStage::withTrashed()
            ->where('lead_stage_id', (int) $data['lead_stage_id'])
            ->where('key', $newKey)
            ->whereKeyNot($subStage->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Dieser Sub-Stage-Key wird in dieser LeadStage bereits verwendet.',
                'errors' => [
                    'key' => ['Dieser Sub-Stage-Key wird in dieser LeadStage bereits verwendet.'],
                ],
            ], 422);
        }

        $subStage->update([
            'lead_stage_id' => (int) $data['lead_stage_id'],
            'name' => $data['name'],
            'key' => $newKey,
            'color' => $data['color'] ?? '#93c21c',
            'icon' => $data['icon'] ?? null,
            'is_default' => $this->requestBool($request, 'is_default', false),
            'is_active' => $this->requestBool($request, 'is_active', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub-Stage wurde aktualisiert.',
            'sub_stage' => $this->subStagePayload($subStage->fresh()),
        ]);
    }

    public function deleteSubStage(LeadStageSubStage $subStage): JsonResponse
    {
        $this->authorizeAdministrator('delete');

        if ($subStage->usageCount() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Diese Sub-Stage wird bereits von Phasen verwendet. Bitte zuerst Phasen verschieben.',
            ], 409);
        }

        $subStage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sub-Stage wurde gelöscht.',
        ]);
    }

    public function reorderSubStages(Request $request, LeadStage $leadStage): JsonResponse
    {
        $this->authorizeAdministrator('update');

        $ids = $this->extractOrderIds($request->input('items', $request->input('ids', [])));

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Keine gültigen Sub-Stage-IDs empfangen.',
                'errors' => [
                    'items' => ['Die Reihenfolge muss Sub-Stage-IDs enthalten.'],
                ],
            ], 422);
        }

        $existingIds = LeadStageSubStage::query()
            ->where('lead_stage_id', $leadStage->id)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        $missingIds = array_values(array_diff($ids, $existingIds));

        if (!empty($missingIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Einige Sub-Stage-IDs existieren nicht oder gehören nicht zu dieser Stage.',
                'errors' => [
                    'items' => ['Ungültige Sub-Stage-IDs: ' . implode(', ', $missingIds)],
                ],
            ], 422);
        }

        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                LeadStageSubStage::query()
                    ->whereKey($id)
                    ->update([
                        'sort_order' => ($index + 1) * 10,
                        'updated_at' => now(),
                    ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Sub-Stage-Reihenfolge wurde gespeichert.',
        ]);
    }

    private function stagePayload(LeadStage $stage): array
    {
        $stage->loadMissing('subStages');

        return [
            'id' => (int) $stage->id,
            'key' => (string) $stage->key,
            'name' => (string) $stage->name,
            'label' => (string) $stage->name,
            'color' => (string) ($stage->color ?: '#74b2d4'),
            'icon' => $stage->icon,
            'sort_order' => (int) $stage->sort_order,
            'is_default' => (bool) $stage->is_default,
            'is_protected' => (bool) $stage->is_protected,
            'is_closed' => (bool) $stage->is_closed,
            'is_active' => (bool) $stage->is_active,
            'usage_count' => $stage->usageCount(),
            'sub_stages' => $stage->subStages
                ->map(fn(LeadStageSubStage $subStage) => $this->subStagePayload($subStage))
                ->values(),
        ];
    }

    private function subStagePayload(LeadStageSubStage $subStage): array
    {
        return [
            'id' => (int) $subStage->id,
            'lead_stage_id' => (int) $subStage->lead_stage_id,
            'key' => (string) $subStage->key,
            'name' => (string) $subStage->name,
            'label' => (string) $subStage->name,
            'color' => (string) ($subStage->color ?: '#93c21c'),
            'icon' => $subStage->icon,
            'sort_order' => (int) $subStage->sort_order,
            'is_default' => (bool) $subStage->is_default,
            'is_active' => (bool) $subStage->is_active,
            'usage_count' => $subStage->usageCount(),
        ];
    }

    private function extractOrderIds(mixed $items): array
    {
        if (is_string($items)) {
            $decoded = json_decode($items, true);
            $items = json_last_error() === JSON_ERROR_NONE ? $decoded : [$items];
        }

        if (!is_array($items)) {
            return [];
        }

        $ids = [];

        foreach ($items as $item) {
            $id = null;

            if (is_numeric($item)) {
                $id = (int) $item;
            } elseif (is_string($item)) {
                if (preg_match('/(\d+)$/', $item, $matches)) {
                    $id = (int) $matches[1];
                }
            } elseif (is_array($item)) {
                $raw = $item['id']
                    ?? $item['value']
                    ?? $item['stage_id']
                    ?? $item['sub_stage_id']
                    ?? null;

                if (is_numeric($raw)) {
                    $id = (int) $raw;
                } elseif (is_string($raw) && preg_match('/(\d+)$/', $raw, $matches)) {
                    $id = (int) $matches[1];
                }
            }

            if ($id && $id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    private function requestBool(Request $request, string $key, bool $default = false): bool
    {
        if (!$request->has($key)) {
            return $default;
        }

        $value = $request->input($key);

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        $value = strtolower(trim((string) $value));

        return in_array($value, ['1', 'true', 'yes', 'on', 'checked'], true);
    }

    private function uniqueStageKey(string $seed): string
    {
        $base = LeadStage::makeKey($seed);
        $key = $base;
        $counter = 2;

        while (LeadStage::withTrashed()->where('key', $key)->exists()) {
            $key = "{$base}_{$counter}";
            $counter++;
        }

        return $key;
    }

    private function uniqueSubStageKey(int $stageId, string $seed): string
    {
        $base = LeadStage::makeKey($seed);
        $key = $base;
        $counter = 2;

        while (
            LeadStageSubStage::withTrashed()
                ->where('lead_stage_id', $stageId)
                ->where('key', $key)
                ->exists()
        ) {
            $key = "{$base}_{$counter}";
            $counter++;
        }

        return $key;
    }

    private function authorizeAdministrator(string $action = 'read'): void
    {
        $user = auth()->user();

        abort_unless($user, 403, 'Bitte zuerst anmelden.');

        $userIds = collect([
            $user->id,
            is_numeric($user->name ?? null) ? (int) $user->name : null,
        ])->filter()->unique()->values()->all();

        $column = UserRoll::permissionColumn($action);

        $allowed = UserRoll::query()
            ->whereIn('user_id', $userIds)
            ->where('item_id', 'Administrator')
            ->where($column, true)
            ->exists();

        abort_unless($allowed, 403, 'Nur Administratoren dürfen LeadStages verwalten.');
    }
}
