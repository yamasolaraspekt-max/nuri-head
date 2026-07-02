<?php

namespace App\Http\Controllers\Phase;

use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\LeadStage;
use App\Models\PhaseActivities;
use App\Models\PhaseSection;
use App\Models\Stage;
use App\Models\TaskPhase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class LeadTaskPhaseManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * New AJAX task-management page.
     * Uses LeadStage + LeadStageSubStage instead of old stages table.
     * No Master Set data is loaded here.
     */
    public function manage(Request $request, $product, $section_id)
    {
        $section = PhaseSection::query()
            ->whereKey((int) $section_id)
            ->where('product_id', (int) $product)
            ->firstOrFail();

        $productModel = ArticleGroup::query()->findOrFail((int) $product);

        $leadStages = LeadStage::query()
            ->with(['activeSubStages'])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $departments = DB::table('departments')
            ->leftJoin('branches', 'branches.id', '=', 'departments.branch_id')
            ->select('departments.department_name', 'departments.id', 'departments.parent_id', DB::raw('COALESCE(branches.branch, "") as branch'))
            ->where('departments.status', 'Published')
            ->orderBy('departments.department_name')
            ->get();

        $positions = DB::table('positions')
            ->select('id', 'position')
            ->where('status', 'Published')
            ->orderBy('position')
            ->get();

        $articles = DB::table('products')
            ->select('id', 'article_no', 'product', 'article_group', 'sub_article')
            ->where('status', 'Published')
            ->orderBy('product')
            ->limit(500)
            ->get();

        // B1: rang-tragende Qualifikationen (position_qualifications) fuer das
        // "Mindest-Qualifikation"-Dropdown im Aktivitaets-Modal, nach Rang sortiert.
        $requiredQualifications = DB::table('position_qualifications')
            ->select('id', 'name', 'sort_order')
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.task.phase.phase_management', [
            'section' => $section,
            'productModel' => $productModel,
            'leadStages' => $leadStages,
            'departments' => $departments,
            'positions' => $positions,
            'articles' => $articles,
            'requiredQualifications' => $requiredQualifications,
        ]);
    }

    /**
     * Full board payload for the AJAX page.
     */
    public function ajaxBoard(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:article_groups,id'],
            'section_id' => ['required', 'integer', 'exists:phase_sections,id'],
        ]);

        $leadStages = LeadStage::query()
            ->with(['activeSubStages'])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $phaseQuery = TaskPhase::query()
            ->with([
                'activities' => function ($query) {
                    $query->whereNull('deleted_at')
                        ->orderBy('sort_order')
                        ->orderBy('id');
                }
            ])
            ->where('product_id', (int) $data['product_id'])
            ->where('section_id', (int) $data['section_id'])
            ->whereNull('deleted_at')
            ->orderBy('order')
            ->orderBy('id');

        if (Schema::hasColumn('task_phases', 'lead_stage_id')) {
            $phaseQuery->whereNotNull('lead_stage_id');
        }

        $phases = $phaseQuery->get();

        $board = $leadStages->map(function ($stage) use ($phases) {
            $stageId = (int) $stage->id;
            $subStages = $stage->activeSubStages->values();

            $rootLane = [
                'id' => null,
                'key' => 'main',
                'name' => 'Hauptstage',
                'color' => $stage->color ?: '#74b2d4',
                'sort_order' => 0,
                'tasks' => $this->phaseCardsForLane($phases, $stageId, null),
            ];

            $lanes = collect([$rootLane])->merge($subStages->map(function ($subStage) use ($phases, $stageId) {
                return [
                    'id' => (int) $subStage->id,
                    'key' => $subStage->key,
                    'name' => $subStage->name,
                    'color' => $subStage->color ?: '#93c21c',
                    'sort_order' => (int) $subStage->sort_order,
                    'tasks' => $this->phaseCardsForLane($phases, $stageId, (int) $subStage->id),
                ];
            }))->values();

            return [
                'id' => $stageId,
                'key' => $stage->key,
                'name' => $stage->name,
                'color' => $stage->color ?: '#74b2d4',
                'icon' => $stage->icon,
                'sort_order' => (int) $stage->sort_order,
                'lanes' => $lanes,
                'task_count' => $lanes->sum(fn($lane) => count($lane['tasks'])),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'board' => $board,
        ]);
    }

    public function ajaxStoreTask(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:article_groups,id'],
            'section_id' => ['required', 'integer', 'exists:phase_sections,id'],
            'lead_stage_id' => ['required', 'integer', 'exists:lead_stages,id'],
            'lead_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
            'phase_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['Published', 'Unpublished'])],
        ]);

        $stage = LeadStage::query()->findOrFail((int) $data['lead_stage_id']);
        $section = PhaseSection::query()->findOrFail((int) $data['section_id']);

        if (!empty($data['lead_sub_stage_id'])) {
            $belongs = DB::table('lead_stage_sub_stages')
                ->where('id', (int) $data['lead_sub_stage_id'])
                ->where('lead_stage_id', (int) $stage->id)
                ->whereNull('deleted_at')
                ->exists();

            if (!$belongs) {
                return response()->json([
                    'success' => false,
                    'message' => 'Der ausgewählte Sub-Stage gehört nicht zu dieser Lead-Stage.',
                ], 422);
            }
        }

        $order = $this->nextPhaseOrder((int) $data['product_id'], (int) $data['section_id'], (int) $stage->id, $data['lead_sub_stage_id'] ?? null);

        $phase = new TaskPhase();
        $phase->product_id = (int) $data['product_id'];
        $phase->section_id = (int) $data['section_id'];
        $phase->section_name = $section->phase_section;
        $phase->phase_name = $data['phase_name'];
        $phase->stage = $stage->key ?: $stage->name;
        $phase->stage_id = (int) $stage->id; // backward compatibility: old column now mirrors LeadStage id on this page.
        $phase->status = $data['status'] ?? 'Published';
        $phase->count = 0;
        $phase->order = $order;

        if (Schema::hasColumn('task_phases', 'lead_stage_id')) {
            $phase->lead_stage_id = (int) $stage->id;
        }

        if (Schema::hasColumn('task_phases', 'lead_sub_stage_id')) {
            $phase->lead_sub_stage_id = $data['lead_sub_stage_id'] ?? null;
        }

        if (Schema::hasColumn('task_phases', 'description')) {
            $phase->description = $data['description'] ?? null;
        }

        $phase->save();

        return response()->json([
            'success' => true,
            'message' => 'Task wurde erstellt.',
            'task' => $this->phaseCard($phase->fresh(['activities'])),
        ]);
    }

    public function ajaxUpdateTask(Request $request, TaskPhase $phase): JsonResponse
    {
        $data = $request->validate([
            'lead_stage_id' => ['required', 'integer', 'exists:lead_stages,id'],
            'lead_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
            'phase_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['Published', 'Unpublished'])],
        ]);

        $stage = LeadStage::query()->findOrFail((int) $data['lead_stage_id']);

        if (!empty($data['lead_sub_stage_id'])) {
            $belongs = DB::table('lead_stage_sub_stages')
                ->where('id', (int) $data['lead_sub_stage_id'])
                ->where('lead_stage_id', (int) $stage->id)
                ->whereNull('deleted_at')
                ->exists();

            if (!$belongs) {
                return response()->json([
                    'success' => false,
                    'message' => 'Der ausgewählte Sub-Stage gehört nicht zu dieser Lead-Stage.',
                ], 422);
            }
        }

        $phase->phase_name = $data['phase_name'];
        $phase->stage = $stage->key ?: $stage->name;
        $phase->stage_id = (int) $stage->id;
        $phase->status = $data['status'] ?? $phase->status ?? 'Published';

        if (Schema::hasColumn('task_phases', 'lead_stage_id')) {
            $phase->lead_stage_id = (int) $stage->id;
        }

        if (Schema::hasColumn('task_phases', 'lead_sub_stage_id')) {
            $phase->lead_sub_stage_id = $data['lead_sub_stage_id'] ?? null;
        }

        if (Schema::hasColumn('task_phases', 'description')) {
            $phase->description = $data['description'] ?? null;
        }

        $phase->save();

        return response()->json([
            'success' => true,
            'message' => 'Task wurde aktualisiert.',
            'task' => $this->phaseCard($phase->fresh(['activities'])),
        ]);
    }

    public function ajaxDeleteTask(TaskPhase $phase): JsonResponse
    {
        DB::transaction(function () use ($phase) {
            PhaseActivities::query()
                ->where('phase_id', $phase->id)
                ->delete();

            $phase->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Task wurde gelöscht.',
        ]);
    }

    public function ajaxCloneTask(TaskPhase $phase): JsonResponse
    {
        $newPhase = DB::transaction(function () use ($phase) {
            $copy = $phase->replicate();
            $copy->phase_name = $phase->phase_name . ' Kopie';
            $copy->order = $this->nextPhaseOrder(
                (int) $phase->product_id,
                (int) $phase->section_id,
                $this->phaseLeadStageId($phase),
                $this->phaseLeadSubStageId($phase)
            );
            $copy->save();

            $this->duplicatePhaseActivities((int) $phase->id, (int) $copy->id);

            return $copy->fresh(['activities']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Task wurde kopiert.',
            'task' => $this->phaseCard($newPhase),
        ]);
    }

    public function ajaxMoveTask(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phase_id' => ['required', 'integer', 'exists:task_phases,id'],
            'lead_stage_id' => ['required', 'integer', 'exists:lead_stages,id'],
            'lead_sub_stage_id' => ['nullable', 'integer', 'exists:lead_stage_sub_stages,id'],
            'target_index' => ['required', 'integer', 'min:0'],
        ]);

        $phase = TaskPhase::query()->findOrFail((int) $data['phase_id']);
        $stage = LeadStage::query()->findOrFail((int) $data['lead_stage_id']);

        DB::transaction(function () use ($phase, $stage, $data) {
            $phase->stage = $stage->key ?: $stage->name;
            $phase->stage_id = (int) $stage->id;

            if (Schema::hasColumn('task_phases', 'lead_stage_id')) {
                $phase->lead_stage_id = (int) $stage->id;
            }

            if (Schema::hasColumn('task_phases', 'lead_sub_stage_id')) {
                $phase->lead_sub_stage_id = $data['lead_sub_stage_id'] ?? null;
            }

            $phase->save();

            $this->placePhaseInLeadLane(
                phaseId: (int) $phase->id,
                productId: (int) $phase->product_id,
                sectionId: (int) $phase->section_id,
                leadStageId: (int) $stage->id,
                leadSubStageId: $data['lead_sub_stage_id'] ?? null,
                targetIndex: (int) $data['target_index']
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Task wurde verschoben.',
        ]);
    }

    public function ajaxReorderTasks(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lead_stage_id' => ['required', 'integer', 'exists:lead_stages,id'],
            'lead_sub_stage_id' => ['nullable', 'integer'],
            'phase_ids' => ['required', 'array'],
            'phase_ids.*' => ['integer', 'exists:task_phases,id'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['phase_ids'] as $index => $phaseId) {
                TaskPhase::query()
                    ->whereKey((int) $phaseId)
                    ->update(['order' => $index, 'updated_at' => now()]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Sortierung wurde gespeichert.',
        ]);
    }

    public function ajaxTaskDetails(TaskPhase $phase): JsonResponse
    {
        $phase->load([
            'activities' => function ($query) {
                $query->whereNull('deleted_at')
                    ->orderBy('sort_order')
                    ->orderBy('id');
            }
        ]);

        return response()->json([
            'success' => true,
            'task' => $this->phaseCard($phase),
            'activities' => $this->activityCards($phase->activities),
        ]);
    }

    public function ajaxStoreActivity(Request $request): JsonResponse
    {
        // B1: leere Auswahl ("keine Anforderung") als null behandeln, damit nullable+exists greift.
        if ($request->input('required_qualification_id') === '') {
            $request->merge(['required_qualification_id' => null]);
        }

        $data = $request->validate([
            'phase_id' => ['required', 'integer', 'exists:task_phases,id'],
            'required_qualification_id' => ['nullable', 'integer', 'exists:position_qualifications,id'],
            'parent_id' => ['nullable', 'integer', 'exists:phase_activities,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration' => ['nullable', 'numeric', 'min:0'],
            'duration_type' => ['nullable', 'string', 'max:50'],
            'photo_required' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::in(['Published', 'Unpublished'])],
            'qualification_ids' => ['nullable', 'array'],
            'qualification_ids.*' => ['integer'],
            'department_ids' => ['nullable', 'array'],
            'department_ids.*' => ['integer'],
            'article_ids' => ['nullable', 'array'],
            'article_ids.*' => ['integer'],
        ]);

        $phase = TaskPhase::query()->findOrFail((int) $data['phase_id']);

        $activity = new PhaseActivities();
        $activity->phase_id = (int) $phase->id;
        $activity->product_id = (int) $phase->product_id;
        $activity->section_id = (int) $phase->section_id;
        $activity->section_name = $phase->section_name;
        $activity->parent_id = $data['parent_id'] ?? null;
        $activity->title = $data['title'];
        $activity->description = $data['description'] ?? null;
        $activity->duration = $data['duration'] ?? 0;
        $activity->duration_type = $data['duration_type'] ?? 'minutes';
        $activity->photo = !empty($data['photo_required']) ? 'needed' : 'not_needed';
        $activity->status = $data['status'] ?? 'Published';
        $activity->sort_order = $this->nextActivityOrder((int) $phase->id, $data['parent_id'] ?? null);

        if (Schema::hasColumn('phase_activities', 'lead_stage_id')) {
            $activity->lead_stage_id = $this->phaseLeadStageId($phase);
        }

        if (Schema::hasColumn('phase_activities', 'lead_sub_stage_id')) {
            $activity->lead_sub_stage_id = $this->phaseLeadSubStageId($phase);
        }

        // B1: Mindest-Qualifikation (Neuanlage: ?? null unkritisch, kein Bestandswert).
        if (Schema::hasColumn('phase_activities', 'required_qualification_id')) {
            $activity->required_qualification_id = $data['required_qualification_id'] ?? null;
        }

        $activity->save();
        $this->syncActivityRelations($activity, $data);

        return response()->json([
            'success' => true,
            'message' => 'Aktivität wurde erstellt.',
            'activity' => $this->activityCard($activity->fresh()),
        ]);
    }

    public function ajaxUpdateActivity(Request $request, PhaseActivities $activity): JsonResponse
    {
        // B1: leere Auswahl ("keine Anforderung") als null behandeln.
        if ($request->input('required_qualification_id') === '') {
            $request->merge(['required_qualification_id' => null]);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'required_qualification_id' => ['nullable', 'integer', 'exists:position_qualifications,id'],
            'description' => ['nullable', 'string'],
            'duration' => ['nullable', 'numeric', 'min:0'],
            'duration_type' => ['nullable', 'string', 'max:50'],
            'photo_required' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::in(['Published', 'Unpublished'])],
            'qualification_ids' => ['nullable', 'array'],
            'qualification_ids.*' => ['integer'],
            'department_ids' => ['nullable', 'array'],
            'department_ids.*' => ['integer'],
            'article_ids' => ['nullable', 'array'],
            'article_ids.*' => ['integer'],
        ]);

        $activity->title = $data['title'];
        $activity->description = $data['description'] ?? null;
        $activity->duration = $data['duration'] ?? 0;
        $activity->duration_type = $data['duration_type'] ?? $activity->duration_type ?? 'minutes';
        $activity->photo = !empty($data['photo_required']) ? 'needed' : 'not_needed';
        $activity->status = $data['status'] ?? $activity->status ?? 'Published';

        // B1: Mindest-Qualifikation NUR setzen, wenn das Feld im Request kam (has()),
        // sonst wuerde ein Pfad ohne das Feld den Bestandswert stillschweigend nullen.
        if (Schema::hasColumn('phase_activities', 'required_qualification_id') && $request->has('required_qualification_id')) {
            $activity->required_qualification_id = $data['required_qualification_id'] ?? null;
        }

        $activity->save();

        $this->syncActivityRelations($activity, $data);

        return response()->json([
            'success' => true,
            'message' => 'Aktivität wurde aktualisiert.',
            'activity' => $this->activityCard($activity->fresh()),
        ]);
    }

    public function ajaxDeleteActivity(PhaseActivities $activity): JsonResponse
    {
        DB::transaction(function () use ($activity) {
            PhaseActivities::query()
                ->where('parent_id', $activity->id)
                ->delete();

            $activity->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Aktivität wurde gelöscht.',
        ]);
    }

    public function ajaxCloneActivity(PhaseActivities $activity): JsonResponse
    {
        $copy = DB::transaction(function () use ($activity) {
            $newId = $this->duplicateActivityTree((int) $activity->id, (int) $activity->phase_id, $activity->parent_id);
            $copy = PhaseActivities::query()->findOrFail($newId);
            $copy->title = $copy->title . ' Kopie';
            $copy->sort_order = $this->nextActivityOrder((int) $copy->phase_id, $copy->parent_id);
            $copy->save();

            return $copy->fresh();
        });

        return response()->json([
            'success' => true,
            'message' => 'Aktivität wurde kopiert.',
            'activity' => $this->activityCard($copy),
        ]);
    }

    public function ajaxMoveActivity(Request $request): JsonResponse
    {
        $data = $request->validate([
            'activity_id' => ['required', 'integer', 'exists:phase_activities,id'],
            'to_phase_id' => ['required', 'integer', 'exists:task_phases,id'],
            'target_parent_id' => ['nullable', 'integer', 'exists:phase_activities,id'],
            'target_index' => ['required', 'integer', 'min:0'],
        ]);

        $activity = PhaseActivities::query()->findOrFail((int) $data['activity_id']);
        $toPhase = TaskPhase::query()->findOrFail((int) $data['to_phase_id']);
        $targetParentId = $data['target_parent_id'] ?? null;

        DB::transaction(function () use ($activity, $toPhase, $targetParentId, $data) {
            $ids = $this->descendantIds((int) $activity->id);

            PhaseActivities::query()
                ->whereIn('id', $ids)
                ->update([
                    'phase_id' => (int) $toPhase->id,
                    'product_id' => (int) $toPhase->product_id,
                    'section_id' => (int) $toPhase->section_id,
                    'section_name' => $toPhase->section_name,
                    'updated_at' => now(),
                ]);

            $activity->refresh();
            $activity->parent_id = $targetParentId;
            $activity->save();

            $this->placeActivityInScope((int) $toPhase->id, $targetParentId, (int) $activity->id, (int) $data['target_index']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Aktivität wurde verschoben.',
        ]);
    }

    public function ajaxReorderActivities(Request $request): JsonResponse
    {
        $data = $request->validate([
            'phase_id' => ['required', 'integer', 'exists:task_phases,id'],
            'parent_id' => ['nullable', 'integer'],
            'activity_ids' => ['required', 'array'],
            'activity_ids.*' => ['integer', 'exists:phase_activities,id'],
        ]);

        foreach ($data['activity_ids'] as $index => $activityId) {
            PhaseActivities::query()
                ->whereKey((int) $activityId)
                ->update([
                    'sort_order' => $index,
                    'parent_id' => $data['parent_id'] ?? null,
                    'updated_at' => now(),
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Aktivitäten wurden sortiert.',
        ]);
    }

    public function ajaxActivityDetails(PhaseActivities $activity): JsonResponse
    {
        return response()->json([
            'success' => true,
            'activity' => $this->activityCard($activity),
        ]);
    }



    /* ---------------------------------------------------------------------
     | Route-name aliases
     | These keep both the new route group and any previously generated route
     | names working without changing the blade.
     | ------------------------------------------------------------------ */

    public function board(Request $request): JsonResponse
    {
        return $this->ajaxBoard($request);
    }

    public function storeTask(Request $request): JsonResponse
    {
        return $this->ajaxStoreTask($request);
    }

    public function showTask(TaskPhase $task): JsonResponse
    {
        return $this->ajaxTaskDetails($task);
    }

    public function updateTask(Request $request, TaskPhase $task): JsonResponse
    {
        return $this->ajaxUpdateTask($request, $task);
    }

    public function deleteTask(TaskPhase $task): JsonResponse
    {
        return $this->ajaxDeleteTask($task);
    }

    public function cloneTask(TaskPhase $task): JsonResponse
    {
        return $this->ajaxCloneTask($task);
    }

    public function moveTask(Request $request): JsonResponse
    {
        return $this->ajaxMoveTask($request);
    }

    public function reorderTasks(Request $request): JsonResponse
    {
        return $this->ajaxReorderTasks($request);
    }

    public function storeActivity(Request $request): JsonResponse
    {
        return $this->ajaxStoreActivity($request);
    }

    public function showActivity(PhaseActivities $activity): JsonResponse
    {
        return $this->ajaxActivityDetails($activity);
    }

    public function updateActivity(Request $request, PhaseActivities $activity): JsonResponse
    {
        return $this->ajaxUpdateActivity($request, $activity);
    }

    public function deleteActivity(PhaseActivities $activity): JsonResponse
    {
        return $this->ajaxDeleteActivity($activity);
    }

    public function cloneActivity(PhaseActivities $activity): JsonResponse
    {
        return $this->ajaxCloneActivity($activity);
    }

    public function moveActivity(Request $request): JsonResponse
    {
        return $this->ajaxMoveActivity($request);
    }

    public function reorderActivities(Request $request): JsonResponse
    {
        return $this->ajaxReorderActivities($request);
    }

    public function leadStages(): JsonResponse
    {
        $stages = LeadStage::query()
            ->with(['activeSubStages'])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stages,
        ]);
    }

    public function leadSubStages(LeadStage $leadStage): JsonResponse
    {
        $subStages = $leadStage->activeSubStages()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $subStages,
        ]);
    }

    /* ---------------------------------------------------------------------
     | Existing compatibility methods used elsewhere in your app
     | Keep these if your old routes still call them.
     | ------------------------------------------------------------------ */

    public function transferPhase(Request $request): JsonResponse
    {
        return $this->ajaxMoveTask($request);
    }

    public function transferActivity(Request $request): JsonResponse
    {
        return $this->ajaxMoveActivity($request);
    }

    public function updateOrder(Request $request): JsonResponse
    {
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->order as $index => $id) {
            TaskPhase::query()->whereKey((int) $id)->update(['order' => $index, 'updated_at' => now()]);
        }

        return response()->json(['status' => 'success']);
    }

    public function destroy($id): JsonResponse
    {
        $phase = TaskPhase::query()->findOrFail((int) $id);
        return $this->ajaxDeleteTask($phase);
    }

    public function toggleStatus(Request $request, $id): JsonResponse
    {
        $phase = TaskPhase::query()->findOrFail((int) $id);
        $phase->status = $phase->status === 'Published' ? 'Unpublished' : 'Published';
        $phase->save();

        return response()->json([
            'success' => true,
            'status' => $phase->status,
        ]);
    }

    /* ---------------------------------------------------------------------
     | Helper methods
     | ------------------------------------------------------------------ */

    private function phaseCardsForLane($phases, int $leadStageId, ?int $leadSubStageId): array
    {
        return $phases
            ->filter(function ($phase) use ($leadStageId, $leadSubStageId) {
                return $this->phaseLeadStageId($phase) === $leadStageId
                    && $this->phaseLeadSubStageId($phase) === $leadSubStageId;
            })
            ->sortBy([['order', 'asc'], ['id', 'asc']])
            ->map(fn($phase) => $this->phaseCard($phase))
            ->values()
            ->all();
    }

    private function phaseCard(TaskPhase $phase): array
    {
        $activities = $phase->relationLoaded('activities')
            ? $phase->activities
            : $phase->activities()->whereNull('deleted_at')->orderBy('sort_order')->get();

        return [
            'id' => (int) $phase->id,
            'product_id' => (int) $phase->product_id,
            'section_id' => (int) $phase->section_id,
            'section_name' => $phase->section_name,
            'lead_stage_id' => $this->phaseLeadStageId($phase),
            'lead_sub_stage_id' => $this->phaseLeadSubStageId($phase),
            'title' => $phase->phase_name,
            'description' => Schema::hasColumn('task_phases', 'description') ? ($phase->description ?? '') : '',
            'status' => $phase->status ?: 'Published',
            'order' => (int) ($phase->order ?? 0),
            'activities_count' => $activities->whereNull('parent_id')->count(),
            'activities' => $this->activityCards($activities),
        ];
    }

    private function activityCards($activities): array
    {
        return collect($activities)
            ->whereNull('parent_id')
            ->sortBy([['sort_order', 'asc'], ['id', 'asc']])
            ->map(fn($activity) => $this->activityCard($activity, $activities))
            ->values()
            ->all();
    }

    private function activityCard(PhaseActivities $activity, $allActivities = null): array
    {
        $allActivities = $allActivities ? collect($allActivities) : PhaseActivities::query()
            ->where('phase_id', (int) $activity->phase_id)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->get();

        $children = $allActivities
            ->where('parent_id', (int) $activity->id)
            ->sortBy([['sort_order', 'asc'], ['id', 'asc']])
            ->map(fn($child) => $this->activityCard($child, $allActivities))
            ->values()
            ->all();

        return [
            'id' => (int) $activity->id,
            'phase_id' => (int) $activity->phase_id,
            'parent_id' => $activity->parent_id ? (int) $activity->parent_id : null,
            'title' => $activity->title,
            'description' => $activity->description,
            'duration' => $activity->duration,
            'duration_type' => $activity->duration_type ?: 'minutes',
            'status' => $activity->status ?: 'Published',
            'photo_required' => $activity->photo === 'needed',
            'sort_order' => (int) ($activity->sort_order ?? 0),
            'required_qualification_id' => $activity->required_qualification_id ? (int) $activity->required_qualification_id : null,
            'qualification_ids' => $this->pivotIds('activity_positions', 'activity_id', 'position_id', (int) $activity->id),
            'department_ids' => $this->pivotIds('activity_departments', 'activity_id', 'department_id', (int) $activity->id),
            'article_ids' => $this->pivotIds('activity_articles', 'activity_id', 'article_id', (int) $activity->id),
            'children' => $children,
        ];
    }

    private function phaseLeadStageId(TaskPhase $phase): int
    {
        if (Schema::hasColumn('task_phases', 'lead_stage_id') && !empty($phase->lead_stage_id)) {
            return (int) $phase->lead_stage_id;
        }

        return (int) $phase->stage_id;
    }

    private function phaseLeadSubStageId(TaskPhase $phase): ?int
    {
        if (Schema::hasColumn('task_phases', 'lead_sub_stage_id') && !empty($phase->lead_sub_stage_id)) {
            return (int) $phase->lead_sub_stage_id;
        }

        return null;
    }

    private function nextPhaseOrder(int $productId, int $sectionId, int $leadStageId, ?int $leadSubStageId): int
    {
        $query = TaskPhase::query()
            ->where('product_id', $productId)
            ->where('section_id', $sectionId)
            ->whereNull('deleted_at');

        if (Schema::hasColumn('task_phases', 'lead_stage_id')) {
            $query->where('lead_stage_id', $leadStageId);
        } else {
            $query->where('stage_id', $leadStageId);
        }

        if (Schema::hasColumn('task_phases', 'lead_sub_stage_id')) {
            $leadSubStageId ? $query->where('lead_sub_stage_id', $leadSubStageId) : $query->whereNull('lead_sub_stage_id');
        }

        return ((int) $query->max('order')) + 1;
    }

    private function placePhaseInLeadLane(int $phaseId, int $productId, int $sectionId, int $leadStageId, ?int $leadSubStageId, int $targetIndex): void
    {
        $query = TaskPhase::query()
            ->where('product_id', $productId)
            ->where('section_id', $sectionId)
            ->whereNull('deleted_at');

        if (Schema::hasColumn('task_phases', 'lead_stage_id')) {
            $query->where('lead_stage_id', $leadStageId);
        } else {
            $query->where('stage_id', $leadStageId);
        }

        if (Schema::hasColumn('task_phases', 'lead_sub_stage_id')) {
            $leadSubStageId ? $query->where('lead_sub_stage_id', $leadSubStageId) : $query->whereNull('lead_sub_stage_id');
        }

        $ids = $query->orderBy('order')->orderBy('id')->pluck('id')->map(fn($id) => (int) $id)->all();
        $ids = array_values(array_filter($ids, fn($id) => $id !== $phaseId));
        $targetIndex = max(0, min($targetIndex, count($ids)));
        array_splice($ids, $targetIndex, 0, [$phaseId]);

        foreach ($ids as $index => $id) {
            TaskPhase::query()->whereKey($id)->update(['order' => $index, 'updated_at' => now()]);
        }
    }

    private function nextActivityOrder(int $phaseId, ?int $parentId): int
    {
        $query = PhaseActivities::query()
            ->where('phase_id', $phaseId)
            ->whereNull('deleted_at');

        $parentId ? $query->where('parent_id', $parentId) : $query->whereNull('parent_id');

        return ((int) $query->max('sort_order')) + 1;
    }

    private function placeActivityInScope(int $phaseId, ?int $parentId, int $activityId, int $targetIndex): void
    {
        $query = PhaseActivities::query()
            ->where('phase_id', $phaseId)
            ->whereNull('deleted_at');

        $parentId ? $query->where('parent_id', $parentId) : $query->whereNull('parent_id');

        $ids = $query->orderBy('sort_order')->orderBy('id')->pluck('id')->map(fn($id) => (int) $id)->all();
        $ids = array_values(array_filter($ids, fn($id) => $id !== $activityId));
        $targetIndex = max(0, min($targetIndex, count($ids)));
        array_splice($ids, $targetIndex, 0, [$activityId]);

        foreach ($ids as $index => $id) {
            PhaseActivities::query()->whereKey($id)->update(['sort_order' => $index, 'updated_at' => now()]);
        }
    }

    private function syncActivityRelations(PhaseActivities $activity, array $data): void
    {
        $this->syncPivot('activity_positions', 'activity_id', 'position_id', (int) $activity->id, $data['qualification_ids'] ?? []);
        $this->syncPivot('activity_departments', 'activity_id', 'department_id', (int) $activity->id, $data['department_ids'] ?? []);
        $this->syncPivot('activity_articles', 'activity_id', 'article_id', (int) $activity->id, $data['article_ids'] ?? []);
    }

    private function syncPivot(string $table, string $ownerColumn, string $targetColumn, int $ownerId, array $targetIds): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        DB::table($table)->where($ownerColumn, $ownerId)->delete();

        $rows = collect($targetIds)
            ->filter(fn($id) => !empty($id))
            ->unique()
            ->map(fn($id) => [
                $ownerColumn => $ownerId,
                $targetColumn => (int) $id,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->values()
            ->all();

        if (!empty($rows)) {
            DB::table($table)->insert($rows);
        }
    }

    private function pivotIds(string $table, string $ownerColumn, string $targetColumn, int $ownerId): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        return DB::table($table)
            ->where($ownerColumn, $ownerId)
            ->pluck($targetColumn)
            ->map(fn($id) => (int) $id)
            ->values()
            ->all();
    }

    private function descendantIds(int $rootId): array
    {
        $ids = [$rootId];
        $queue = [$rootId];

        while (!empty($queue)) {
            $children = PhaseActivities::query()
                ->whereIn('parent_id', $queue)
                ->whereNull('deleted_at')
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();

            $children = array_values(array_diff($children, $ids));

            if (empty($children)) {
                break;
            }

            $ids = array_merge($ids, $children);
            $queue = $children;
        }

        return $ids;
    }

    private function duplicatePhaseActivities(int $fromPhaseId, int $toPhaseId): void
    {
        $rows = PhaseActivities::query()
            ->where('phase_id', $fromPhaseId)
            ->whereNull('deleted_at')
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $map = [];
        $remaining = $rows->keyBy('id')->all();
        $guard = 0;

        while (!empty($remaining) && $guard++ < 10000) {
            foreach ($remaining as $oldId => $row) {
                $oldParent = $row->parent_id;

                if ($oldParent !== null && !isset($map[$oldParent])) {
                    continue;
                }

                $copy = $row->replicate();
                $copy->phase_id = $toPhaseId;
                $copy->parent_id = $oldParent === null ? null : $map[$oldParent];
                $copy->title = $row->title;
                $copy->save();

                $this->copyActivityPivots((int) $oldId, (int) $copy->id);

                $map[$oldId] = $copy->id;
                unset($remaining[$oldId]);
            }
        }
    }

    private function duplicateActivityTree(int $rootActivityId, int $toPhaseId, ?int $targetParentId): int
    {
        $ids = $this->descendantIds($rootActivityId);
        $rows = PhaseActivities::query()->whereIn('id', $ids)->whereNull('deleted_at')->get()->keyBy('id');

        $map = [];
        $newRootId = null;
        $remaining = $rows->all();
        $guard = 0;

        while (!empty($remaining) && $guard++ < 10000) {
            foreach ($remaining as $oldId => $row) {
                $oldParent = $row->parent_id;

                if ((int) $oldId === $rootActivityId) {
                    $copy = $row->replicate();
                    $copy->phase_id = $toPhaseId;
                    $copy->parent_id = $targetParentId;
                    $copy->save();

                    $this->copyActivityPivots((int) $oldId, (int) $copy->id);

                    $map[$oldId] = $copy->id;
                    $newRootId = (int) $copy->id;
                    unset($remaining[$oldId]);
                    continue;
                }

                if ($oldParent !== null && !isset($map[$oldParent])) {
                    continue;
                }

                $copy = $row->replicate();
                $copy->phase_id = $toPhaseId;
                $copy->parent_id = $oldParent === null ? null : $map[$oldParent];
                $copy->save();

                $this->copyActivityPivots((int) $oldId, (int) $copy->id);

                $map[$oldId] = $copy->id;
                unset($remaining[$oldId]);
            }
        }

        return (int) $newRootId;
    }

    private function copyActivityPivots(int $fromActivityId, int $toActivityId): void
    {
        foreach ([
            ['activity_positions', 'activity_id'],
            ['activity_departments', 'activity_id'],
            ['activity_articles', 'activity_id'],
        ] as [$table, $ownerColumn]) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $rows = DB::table($table)->where($ownerColumn, $fromActivityId)->get();

            foreach ($rows as $row) {
                $copy = (array) $row;
                unset($copy['id']);
                $copy[$ownerColumn] = $toActivityId;
                if (array_key_exists('created_at', $copy))
                    $copy['created_at'] = now();
                if (array_key_exists('updated_at', $copy))
                    $copy['updated_at'] = now();
                DB::table($table)->insert($copy);
            }
        }
    }
}
