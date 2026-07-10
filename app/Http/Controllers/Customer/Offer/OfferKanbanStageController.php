<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Http\Controllers\Controller;
use App\Models\OfferKanbanStage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OfferKanbanStageController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,update')->only(['reorder', 'update']);
        $this->middleware('permission:Customer,delete')->only(['destroy']);
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $documentStatus = OfferKanbanStage::normalizeDocumentStatus($request->query('document_status', 'offer'));
        $includeInactive = $request->boolean('include_inactive');

        $stages = OfferKanbanStage::query()
            ->forDocument($documentStatus)
            ->when(!$includeInactive, fn($query) => $query->active())
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'document_status' => $documentStatus,
            'stages' => $stages,
            'keys' => $stages->where('is_active', true)->pluck('key')->values(),
            'labels' => $stages->where('is_active', true)->pluck('label', 'key'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $documentStatus = OfferKanbanStage::normalizeDocumentStatus($request->input('document_status', 'offer'));

        $validator = Validator::make($request->all(), [
            'label' => ['required', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:120', Rule::unique('offer_kanban_stages', 'key')->where('document_status', $documentStatus)->whereNull('deleted_at')],
            'icon' => ['nullable', 'string', 'max:80'],
            'color' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:500'],
            'position' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $key = $request->filled('key')
            ? OfferKanbanStage::makeKey((string) $request->input('key'))
            : OfferKanbanStage::makeKey((string) $request->input('label'));

        $exists = OfferKanbanStage::withTrashed()
            ->where('document_status', $documentStatus)
            ->where('key', $key)
            ->first();

        if ($exists && !$exists->trashed()) {
            return response()->json(['success' => false, 'message' => 'Diese Kanban-Spalte existiert bereits.'], 422);
        }

        $stage = DB::transaction(function () use ($request, $documentStatus, $key, $exists) {
            $position = $request->integer('position') ?: ((int) OfferKanbanStage::forDocument($documentStatus)->max('position') + 1);

            if ($exists && $exists->trashed()) {
                $exists->restore();
                $exists->update([
                    'label' => $request->input('label'),
                    'icon' => $request->input('icon'),
                    'color' => $request->input('color') ?: '#93c21c',
                    'description' => $request->input('description'),
                    'position' => $position,
                    'is_active' => true,
                ]);
                return $exists->fresh();
            }

            return OfferKanbanStage::create([
                'document_status' => $documentStatus,
                'key' => $key,
                'label' => $request->input('label'),
                'icon' => $request->input('icon'),
                'color' => $request->input('color') ?: '#93c21c',
                'description' => $request->input('description'),
                'position' => $position,
                'is_active' => true,
                'is_default' => false,
                'is_system' => false,
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Kanban-Spalte wurde erstellt.', 'stage' => $stage]);
    }

    public function update(Request $request, OfferKanbanStage $stage): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'label' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:80'],
            'color' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $stage->fill($request->only(['label', 'icon', 'color', 'description', 'position']));

        if ($request->has('is_active')) {
            $stage->is_active = $request->boolean('is_active');
        }

        $stage->save();

        return response()->json(['success' => true, 'message' => 'Kanban-Spalte wurde aktualisiert.', 'stage' => $stage->fresh()]);
    }

    public function destroy(OfferKanbanStage $stage): JsonResponse
    {
        if ($stage->is_system) {
            return response()->json(['success' => false, 'message' => 'System-Spalten können nicht gelöscht werden. Deaktivieren Sie die Spalte stattdessen.'], 422);
        }

        $stage->delete();

        return response()->json(['success' => true, 'message' => 'Kanban-Spalte wurde entfernt.']);
    }

    public function reorder(Request $request): JsonResponse
    {
        $documentStatus = OfferKanbanStage::normalizeDocumentStatus($request->input('document_status', 'offer'));

        $validator = Validator::make($request->all(), [
            'ordered_ids' => ['required', 'array', 'min:1'],
            'ordered_ids.*' => ['required', 'integer', 'exists:offer_kanban_stages,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        DB::transaction(function () use ($request, $documentStatus) {
            foreach ($request->input('ordered_ids', []) as $index => $id) {
                OfferKanbanStage::query()
                    ->where('document_status', $documentStatus)
                    ->where('id', (int) $id)
                    ->update(['position' => $index + 1]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Reihenfolge gespeichert.',
            'stages' => OfferKanbanStage::orderedFor($documentStatus, false),
        ]);
    }
}
