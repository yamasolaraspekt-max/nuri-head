<?php

namespace App\Http\Controllers\Product\MasterSet;

use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\LeadStage;
use App\Models\LeadStageSubStage;
use App\Models\MasterSetTask;
use App\Models\PhaseActivities;
use App\Models\PhaseSection;
use App\Models\TaskPhase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TaskWizardController extends Controller
{
    /**
     * Sections -> Phases -> Activities tree.
     *
     * IMPORTANT:
     * This controller no longer uses App\Models\Stage or the legacy stages table.
     * The frontend may still send/read "stage_id"; we treat it as an alias of lead_stage_id.
     */
    public function getTree($productId)
    {
        $sections = PhaseSection::query()
            ->where('product_id', $productId)
            ->with([
                'taskPhases' => function ($q) {
                    $q->orderByRaw('COALESCE(`order`, 999999) asc')
                        ->orderBy('id')
                        ->with([
                            'leadStage:id,key,name,color,icon,sort_order',
                            'leadSubStage:id,lead_stage_id,key,name,color,icon,sort_order',
                            'activities' => function ($a) {
                                $a->orderByRaw('COALESCE(sort_order, 999999) asc')
                                    ->orderBy('id')
                                    ->with([
                                        'leadStage:id,key,name,color,icon,sort_order',
                                        'leadSubStage:id,lead_stage_id,key,name,color,icon,sort_order',
                                    ]);
                            },
                        ]);
                },
            ])
            ->get();

        $defaultLeadStage = LeadStage::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first(['id', 'key', 'name', 'color', 'icon', 'sort_order']);

        $sections->transform(function ($section) use ($defaultLeadStage) {
            $section->label = $section->german_name ?? $section->phase_section ?? 'Sektion';

            $section->task_phases = $section->taskPhases->map(function ($phase) use ($defaultLeadStage) {
                $leadStage = $phase->leadStage ?: $defaultLeadStage;
                $leadStageId = $phase->lead_stage_id ?: ($leadStage?->id);

                // Compatibility aliases for old JS.
                $phase->stage_id = $leadStageId;
                $phase->stage_name = $leadStage?->name;
                $phase->lead_stage_id = $leadStageId;
                $phase->lead_stage_name = $leadStage?->name;
                $phase->lead_sub_stage_name = $phase->leadSubStage?->name;
                $phase->stage_type = $leadStage?->key ?? $phase->stage ?? null;

                $phase->activities = $phase->activities->map(function ($activity) use ($phase, $leadStage) {
                    $activityLeadStage = $activity->leadStage ?: $leadStage;
                    $activityLeadStageId = $activity->lead_stage_id ?: $phase->lead_stage_id;

                    // Compatibility aliases for old JS.
                    $activity->stage_id = $activityLeadStageId;
                    $activity->stage_name = $activityLeadStage?->name ?? $phase->stage_name;
                    $activity->lead_stage_id = $activityLeadStageId;
                    $activity->lead_stage_name = $activityLeadStage?->name ?? $phase->stage_name;
                    $activity->lead_sub_stage_id = $activity->lead_sub_stage_id ?: $phase->lead_sub_stage_id;
                    $activity->lead_sub_stage_name = $activity->leadSubStage?->name ?? $phase->lead_sub_stage_name;

                    $activity->photo_required = ($activity->photo === '__REQUIRED__');
                    $activity->has_photo = (!empty($activity->photo) && $activity->photo !== '__REQUIRED__');

                    return $activity;
                });

                return $phase;
            });

            unset($section->taskPhases);

            return $section;
        });

        $stages = $this->leadStagePayload();

        return response()->json([
            'status' => 'ok',
            'data' => $sections,
            'stages' => $stages,
        ]);
    }

    // ---------------------------------------------------------------------
    // PHASE CRUD
    // ---------------------------------------------------------------------

    public function storePhase(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('article_groups', 'id')],
            'section_id' => ['required', 'integer', Rule::exists('phase_sections', 'id')],
            'phase_name' => ['required', 'string', 'max:255'],

            // stage_id is kept as frontend alias.
            'stage_id' => ['nullable', 'integer', Rule::exists('lead_stages', 'id')],
            'lead_stage_id' => ['nullable', 'integer', Rule::exists('lead_stages', 'id')],
            'lead_sub_stage_id' => ['nullable', 'integer', Rule::exists('lead_stage_sub_stages', 'id')],
        ]);

        $leadStageId = $this->resolveLeadStageId($data);
        $leadStage = $leadStageId ? LeadStage::find($leadStageId) : null;
        $leadSubStageId = $this->resolveLeadSubStageId($leadStageId, $data['lead_sub_stage_id'] ?? null);

        $nextOrder = ((int) TaskPhase::where('section_id', $data['section_id'])->max('order')) + 1;

        $phase = TaskPhase::create([
            'product_id' => (int) $data['product_id'],
            'section_id' => (int) $data['section_id'],
            'phase_name' => $data['phase_name'],
            'section_name' => null,

            // New workflow fields.
            'lead_stage_id' => $leadStageId,
            'lead_sub_stage_id' => $leadSubStageId,

            // Legacy string column; keep useful text, but do not use legacy stages table.
            'stage' => $leadStage?->key ?? 'project',

            'status' => 'Published',
            'order' => $nextOrder ?: 1,
            'count' => null,
            'version' => null,
        ]);

        return response()->json([
            'status' => 'ok',
            'data' => $this->phasePayload($phase->fresh(['leadStage', 'leadSubStage'])),
        ]);
    }

    public function updatePhase(Request $request, $id)
    {
        $phase = TaskPhase::findOrFail($id);

        $data = $request->validate([
            'phase_name' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'version' => ['sometimes', 'nullable', 'string', 'max:50'],

            // stage_id is kept as frontend alias.
            'stage_id' => ['sometimes', 'nullable', 'integer', Rule::exists('lead_stages', 'id')],
            'lead_stage_id' => ['sometimes', 'nullable', 'integer', Rule::exists('lead_stages', 'id')],
            'lead_sub_stage_id' => ['sometimes', 'nullable', 'integer', Rule::exists('lead_stage_sub_stages', 'id')],
        ]);

        if (array_key_exists('stage_id', $data) || array_key_exists('lead_stage_id', $data)) {
            $leadStageId = $this->resolveLeadStageId($data, $phase->lead_stage_id);
            $leadStage = $leadStageId ? LeadStage::find($leadStageId) : null;

            $data['lead_stage_id'] = $leadStageId;
            $data['lead_sub_stage_id'] = $this->resolveLeadSubStageId($leadStageId, $data['lead_sub_stage_id'] ?? $phase->lead_sub_stage_id);
            $data['stage'] = $leadStage?->key ?? $phase->stage;
        }

        unset($data['stage_id']);

        $phase->update($data);

        return response()->json([
            'status' => 'ok',
            'data' => $this->phasePayload($phase->fresh(['leadStage', 'leadSubStage'])),
        ]);
    }

    public function deletePhase($id)
    {
        TaskPhase::destroy($id);

        return response()->json(['status' => 'ok']);
    }

    public function reorderPhases(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', Rule::exists('task_phases', 'id')],
            'items.*.order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($data['items'] as $item) {
            TaskPhase::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['status' => 'ok']);
    }

    // ---------------------------------------------------------------------
    // ACTIVITY CRUD
    // ---------------------------------------------------------------------

    public function storeActivity(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('article_groups', 'id')],
            'section_id' => ['required', 'integer', Rule::exists('phase_sections', 'id')],
            'phase_id' => ['required', 'integer', Rule::exists('task_phases', 'id')],

            'title' => ['required', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:8'],
            'description' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'link' => ['nullable', 'string', 'max:1000'],
            'photo_required' => ['nullable'],

            // stage_id is kept as frontend alias.
            'stage_id' => ['nullable', 'integer', Rule::exists('lead_stages', 'id')],
            'lead_stage_id' => ['nullable', 'integer', Rule::exists('lead_stages', 'id')],
            'lead_sub_stage_id' => ['nullable', 'integer', Rule::exists('lead_stage_sub_stages', 'id')],
        ]);

        $phase = TaskPhase::findOrFail($data['phase_id']);
        $leadStageId = $this->resolveLeadStageId($data, $phase->lead_stage_id);
        $leadSubStageId = $this->resolveLeadSubStageId($leadStageId, $data['lead_sub_stage_id'] ?? $phase->lead_sub_stage_id);

        $PHOTO_REQUIRED_SENTINEL = '__REQUIRED__';

        $photoRequired = $data['photo_required'] ?? null;
        $isNeeded = ($photoRequired === true || $photoRequired === 1 || $photoRequired === '1' || $photoRequired === 'needed' || $photoRequired === 'true');

        $nextOrder = ((int) PhaseActivities::where('phase_id', $data['phase_id'])->max('sort_order')) + 1;

        $activity = PhaseActivities::create([
            'product_id' => (int) $data['product_id'],
            'section_id' => (int) $data['section_id'],
            'phase_id' => (int) $data['phase_id'],
            'section_name' => null,

            // New workflow fields.
            'lead_stage_id' => $leadStageId,
            'lead_sub_stage_id' => $leadSubStageId,

            'title' => $data['title'],
            'duration' => $data['duration'] ?? null,
            'description' => $data['description'] ?? null,
            'notes' => $data['notes'] ?? null,
            'link' => $data['link'] ?? null,
            'photo' => $isNeeded ? $PHOTO_REQUIRED_SENTINEL : null,
            'sort_order' => $nextOrder ?: 1,
            'status' => 'Published',
        ]);

        return response()->json([
            'status' => 'ok',
            'data' => $this->activityPayload($activity->fresh(['leadStage', 'leadSubStage'])),
        ]);
    }

    public function updateActivity(Request $request, $id)
    {
        $activity = PhaseActivities::findOrFail($id);

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'duration' => ['sometimes', 'nullable', 'string', 'max:8'],
            'description' => ['sometimes', 'nullable', 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'link' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'sort_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'phase_id' => ['sometimes', 'nullable', 'integer', Rule::exists('task_phases', 'id')],

            // stage_id is kept as frontend alias.
            'stage_id' => ['sometimes', 'nullable', 'integer', Rule::exists('lead_stages', 'id')],
            'lead_stage_id' => ['sometimes', 'nullable', 'integer', Rule::exists('lead_stages', 'id')],
            'lead_sub_stage_id' => ['sometimes', 'nullable', 'integer', Rule::exists('lead_stage_sub_stages', 'id')],

            'photo' => ['sometimes', 'nullable', 'string', 'max:255'],
            'photo_required' => ['sometimes', 'nullable', Rule::in(['needed', 'off', '1', '0'])],
        ]);

        if (array_key_exists('photo_required', $data) && !array_key_exists('photo', $data)) {
            $data['photo'] = in_array($data['photo_required'], ['needed', '1'], true) ? '__REQUIRED__' : null;
            unset($data['photo_required']);
        }

        if (array_key_exists('stage_id', $data) || array_key_exists('lead_stage_id', $data) || array_key_exists('phase_id', $data)) {
            $phase = !empty($data['phase_id'])
                ? TaskPhase::find($data['phase_id'])
                : TaskPhase::find($activity->phase_id);

            $leadStageId = $this->resolveLeadStageId($data, $activity->lead_stage_id ?: ($phase?->lead_stage_id));
            $data['lead_stage_id'] = $leadStageId;
            $data['lead_sub_stage_id'] = $this->resolveLeadSubStageId($leadStageId, $data['lead_sub_stage_id'] ?? $activity->lead_sub_stage_id ?? $phase?->lead_sub_stage_id);
        }

        unset($data['stage_id']);

        $activity->update($data);

        return response()->json([
            'status' => 'ok',
            'data' => $this->activityPayload($activity->fresh(['leadStage', 'leadSubStage'])),
        ]);
    }

    public function deleteActivity($id)
    {
        PhaseActivities::destroy($id);

        return response()->json(['status' => 'ok']);
    }

    public function reorderActivities(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', Rule::exists('phase_activities', 'id')],
            'items.*.phase_id' => ['required', 'integer', Rule::exists('task_phases', 'id')],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($data['items'] as $item) {
            PhaseActivities::where('id', $item['id'])->update([
                'phase_id' => $item['phase_id'],
                'sort_order' => $item['sort_order'],
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function cloneActivity(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'integer', Rule::exists('phase_activities', 'id')],
        ]);

        $original = PhaseActivities::findOrFail($data['id']);
        $new = $original->replicate();

        $new->title = ($new->title ?? 'Aufgabe') . ' (Kopie)';
        $new->sort_order = ((int) ($new->sort_order ?? 0)) + 1;
        $new->copy_from = $original->id;
        $new->copy_count = ((int) ($original->copy_count ?? 0)) + 1;
        $new->save();

        return response()->json([
            'status' => 'ok',
            'data' => $this->activityPayload($new->fresh(['leadStage', 'leadSubStage'])),
        ]);
    }

    // ---------------------------------------------------------------------
    // APPLY WIZARD
    // ---------------------------------------------------------------------

    public function apply(Request $request)
    {
        $payload = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', Rule::exists('article_groups', 'id')],

            // old frontend name, now means lead_stage_ids
            'stage_ids' => ['nullable', 'array'],
            'stage_ids.*' => ['integer', Rule::exists('lead_stages', 'id')],
            'lead_stage_ids' => ['nullable', 'array'],
            'lead_stage_ids.*' => ['integer', Rule::exists('lead_stages', 'id')],

            'section_keys' => ['required', 'array', 'min:1'],
            'section_keys.*' => ['string', 'max:50'],

            'phase_name' => ['required', 'string', 'max:255'],
            'phase_status' => ['nullable', 'string', 'max:50'],
            'phase_version' => ['nullable', 'string', 'max:50'],

            'activities' => ['required', 'array', 'min:1'],
            'activities.*.title' => ['required', 'string', 'max:255'],
            'activities.*.duration' => ['nullable', 'string', 'max:8'],
            'activities.*.description' => ['nullable', 'string', 'max:255'],
            'activities.*.link' => ['nullable', 'string', 'max:1000'],
            'activities.*.photo_required' => ['nullable', Rule::in(['needed', 'off'])],
            'activities.*.sort_order' => ['nullable', 'integer'],
            'activities.*.status' => ['nullable', 'string', 'max:50'],
        ]);

        $leadStageIds = collect($payload['lead_stage_ids'] ?? $payload['stage_ids'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($leadStageIds)) {
            return response()->json([
                'ok' => false,
                'message' => 'Bitte mindestens eine Lead-Phase auswählen.',
                'errors' => ['lead_stage_ids' => ['Mindestens eine Lead-Phase ist erforderlich.']],
            ], 422);
        }

        $PHOTO_REQUIRED_SENTINEL = '__REQUIRED__';

        $sectionKeys = collect($payload['section_keys'])
            ->map(fn($s) => strtolower(trim($s)))
            ->filter()
            ->values()
            ->all();

        $created = ['phases' => 0, 'activities' => 0];

        DB::transaction(function () use ($payload, $leadStageIds, $sectionKeys, $PHOTO_REQUIRED_SENTINEL, &$created) {
            $products = ArticleGroup::query()
                ->whereIn('id', $payload['product_ids'])
                ->get()
                ->keyBy('id');

            $leadStages = LeadStage::query()
                ->whereIn('id', $leadStageIds)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            foreach ($products as $product) {
                $sectionsByKey = PhaseSection::query()
                    ->where('product_id', $product->id)
                    ->whereIn('phase_section', $sectionKeys)
                    ->get()
                    ->keyBy(fn($s) => strtolower($s->phase_section));

                foreach ($sectionKeys as $key) {
                    if (!isset($sectionsByKey[$key])) {
                        $sectionsByKey[$key] = PhaseSection::create([
                            'product_id' => $product->id,
                            'phase_section' => $key,
                            'status' => 'Published',
                        ]);
                    }
                }

                foreach ($sectionsByKey as $section) {
                    foreach ($leadStages as $leadStage) {
                        $leadSubStageId = $this->resolveLeadSubStageId((int) $leadStage->id);

                        $phase = TaskPhase::firstOrCreate(
                            [
                                'product_id' => $product->id,
                                'section_id' => $section->id,
                                'lead_stage_id' => $leadStage->id,
                                'phase_name' => $payload['phase_name'],
                                'version' => $payload['phase_version'] ?? null,
                            ],
                            [
                                'lead_sub_stage_id' => $leadSubStageId,
                                'section_name' => $section->phase_section,
                                'stage' => $leadStage->key ?? 'project',
                                'status' => $payload['phase_status'] ?? 'Published',
                                'order' => null,
                                'count' => null,
                            ]
                        );

                        if ($phase->wasRecentlyCreated) {
                            $created['phases']++;
                        }

                        foreach ($payload['activities'] as $idx => $activityData) {
                            $photo = (($activityData['photo_required'] ?? 'off') === 'needed')
                                ? $PHOTO_REQUIRED_SENTINEL
                                : null;

                            $activity = PhaseActivities::firstOrCreate(
                                [
                                    'phase_id' => $phase->id,
                                    'title' => $activityData['title'],
                                ],
                                [
                                    'product_id' => $product->id,
                                    'section_id' => $section->id,
                                    'lead_stage_id' => $leadStage->id,
                                    'lead_sub_stage_id' => $leadSubStageId,
                                    'section_name' => $section->phase_section,
                                    'duration' => $activityData['duration'] ?? null,
                                    'description' => $activityData['description'] ?? null,
                                    'link' => $activityData['link'] ?? null,
                                    'photo' => $photo,
                                    'sort_order' => $activityData['sort_order'] ?? ($idx + 1),
                                    'status' => $activityData['status'] ?? 'Published',
                                ]
                            );

                            if ($activity->wasRecentlyCreated) {
                                $created['activities']++;
                            }
                        }
                    }
                }
            }
        });

        return response()->json([
            'ok' => true,
            'message' => 'Wizard applied successfully.',
            'created' => $created,
        ]);
    }

    // ---------------------------------------------------------------------
    // OPTIONAL LOOKUPS / QUICK INSERT
    // ---------------------------------------------------------------------

    public function storeActivityAt(Request $request)
    {
        \Log::info('incoming data;', [$request->all()]);

        $data = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('article_groups', 'id')],
            'section_id' => ['required', 'integer', Rule::exists('phase_sections', 'id')],
            'phase_id' => ['required', 'integer', Rule::exists('task_phases', 'id')],

            'after_activity_id' => ['nullable', 'integer', Rule::exists('phase_activities', 'id')],
            'master_set_id' => ['nullable', 'integer', Rule::exists('master_sets', 'id')],
            'set_after_activity_id' => ['nullable', 'integer', Rule::exists('phase_activities', 'id')],

            'title' => ['required', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:8'],
            'description' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'link' => ['nullable', 'string', 'max:1000'],
            'photo_required' => ['nullable'],

            // stage_id is kept as frontend alias.
            'stage_id' => ['nullable', 'integer', Rule::exists('lead_stages', 'id')],
            'lead_stage_id' => ['nullable', 'integer', Rule::exists('lead_stages', 'id')],
            'lead_sub_stage_id' => ['nullable', 'integer', Rule::exists('lead_stage_sub_stages', 'id')],
        ]);

        $phase = TaskPhase::findOrFail($data['phase_id']);
        $leadStageId = $this->resolveLeadStageId($data, $phase->lead_stage_id);
        $leadStage = $leadStageId ? LeadStage::find($leadStageId) : null;
        $leadSubStageId = $this->resolveLeadSubStageId($leadStageId, $data['lead_sub_stage_id'] ?? $phase->lead_sub_stage_id);

        $PHOTO_REQUIRED_SENTINEL = '__REQUIRED__';

        $photoRequired = $data['photo_required'] ?? null;
        $isNeeded = (
            $photoRequired === true ||
            $photoRequired === 1 ||
            $photoRequired === '1' ||
            $photoRequired === 'needed' ||
            $photoRequired === 'true'
        );

        $phaseId = (int) $data['phase_id'];
        $afterId = $data['after_activity_id'] ?? null;

        $masterSetId = isset($data['master_set_id']) ? (int) $data['master_set_id'] : null;
        $setAfterActivityId = $data['set_after_activity_id'] ?? null;

        return DB::transaction(function () use ($data, $phase, $phaseId, $afterId, $PHOTO_REQUIRED_SENTINEL, $isNeeded, $masterSetId, $setAfterActivityId, $leadStageId, $leadStage, $leadSubStageId) {
            PhaseActivities::where('phase_id', $phaseId)->lockForUpdate()->get(['id']);

            $insertAt = (int) PhaseActivities::where('phase_id', $phaseId)->max('sort_order');
            $insertAt = ($insertAt < 0) ? 0 : ($insertAt + 1);

            if ($afterId) {
                $after = PhaseActivities::where('id', $afterId)->lockForUpdate()->first();

                if ($after && (int) $after->phase_id === $phaseId) {
                    $insertAt = ((int) ($after->sort_order ?? 0)) + 1;

                    PhaseActivities::where('phase_id', $phaseId)
                        ->where('sort_order', '>=', $insertAt)
                        ->increment('sort_order');
                }
            }

            $activity = PhaseActivities::create([
                'product_id' => (int) $data['product_id'],
                'section_id' => (int) $data['section_id'],
                'phase_id' => $phaseId,
                'lead_stage_id' => $leadStageId,
                'lead_sub_stage_id' => $leadSubStageId,
                'section_name' => null,
                'title' => $data['title'],
                'duration' => $data['duration'] ?? null,
                'description' => $data['description'] ?? null,
                'notes' => $data['notes'] ?? null,
                'link' => $data['link'] ?? null,
                'photo' => $isNeeded ? $PHOTO_REQUIRED_SENTINEL : null,
                'sort_order' => $insertAt,
                'status' => 'Published',
            ]);

            $phaseName = $phase->phase_name ?? null;
            $stageName = $leadStage?->name;

            $setItem = null;

            if ($masterSetId) {
                $exists = MasterSetTask::where('master_set_id', $masterSetId)
                    ->where('phase_activity_id', $activity->id)
                    ->exists();

                if (!$exists) {
                    MasterSetTask::where('master_set_id', $masterSetId)->lockForUpdate()->get(['id']);

                    $setInsertAt = (int) MasterSetTask::where('master_set_id', $masterSetId)->max('sort_order');
                    $setInsertAt = ($setInsertAt < 0) ? 0 : ($setInsertAt + 1);

                    if ($setAfterActivityId) {
                        $afterSet = MasterSetTask::where('master_set_id', $masterSetId)
                            ->where('phase_activity_id', $setAfterActivityId)
                            ->lockForUpdate()
                            ->first();

                        if ($afterSet) {
                            $setInsertAt = ((int) ($afterSet->sort_order ?? 0)) + 1;

                            MasterSetTask::where('master_set_id', $masterSetId)
                                ->where('sort_order', '>=', $setInsertAt)
                                ->increment('sort_order');
                        }
                    }

                    $setItem = MasterSetTask::create([
                        'master_set_id' => $masterSetId,
                        'phase_activity_id' => $activity->id,
                        'task_phase_id' => $phaseId,

                        // Compatibility: stage_id remains empty; lead_stage_id is the real FK.
                        'stage_id' => null,
                        'lead_stage_id' => $leadStageId,
                        'lead_sub_stage_id' => $leadSubStageId,
                        'stage_name' => $stageName,
                        'phase_name' => $phaseName,

                        'title' => $activity->title,
                        'description' => $activity->description,
                        'duration' => $activity->duration,
                        'notes' => $activity->notes,
                        'hours' => 0,
                        'sort_order' => $setInsertAt,
                    ]);
                }
            }

            $payloadForUi = [
                // stage_id is only an alias for the frontend.
                'stage_id' => $leadStageId,
                'lead_stage_id' => $leadStageId,
                'lead_sub_stage_id' => $leadSubStageId,
                'stage_name' => $stageName,
                'lead_stage_name' => $stageName,
                'task_phase_id' => $phaseId,
                'phase_name' => $phaseName,
                'phase_activity_id' => $activity->id,
                'title' => $activity->title,
                'description' => $activity->description ?? '',
                'duration' => $activity->duration ?? '00:00',
                'duration_type' => '',
                'notes' => $activity->notes,
                'priority' => null,
                'percent' => null,
                'hours' => 0,
            ];

            $encoded = base64_encode(json_encode($payloadForUi));

            return response()->json([
                'status' => 'ok',
                'data' => $this->activityPayload($activity->fresh(['leadStage', 'leadSubStage'])),
                'set_item' => $setItem,
                'encoded' => $encoded,
                'task' => $payloadForUi,
            ]);
        });
    }

    public function lookupProducts(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        return ArticleGroup::query()
            ->when($q !== '', fn($qq) => $qq->where('article_group', 'like', "%{$q}%"))
            ->orderBy('article_group')
            ->limit(30)
            ->get(['id', 'article_group']);
    }

    public function lookupStages(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        return $this->leadStagePayload($q);
    }

    public function lookupSections(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        return PhaseSection::query()
            ->when($q !== '', fn($qq) => $qq->where('phase_section', 'like', "%{$q}%"))
            ->orderBy('phase_section')
            ->limit(50)
            ->get(['id', 'product_id', 'phase_section', 'status']);
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function defaultLeadStageId(): ?int
    {
        return LeadStage::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');
    }

    private function resolveLeadStageId(array $data, ?int $fallback = null): ?int
    {
        $id = $data['lead_stage_id'] ?? $data['stage_id'] ?? $fallback ?? $this->defaultLeadStageId();

        if (!$id) {
            return null;
        }

        $exists = LeadStage::query()
            ->whereKey((int) $id)
            ->where('is_active', true)
            ->exists();

        return $exists ? (int) $id : $this->defaultLeadStageId();
    }

    private function resolveLeadSubStageId(?int $leadStageId, ?int $requestedId = null): ?int
    {
        if ($requestedId) {
            $exists = LeadStageSubStage::query()
                ->whereKey((int) $requestedId)
                ->when($leadStageId, fn($q) => $q->where('lead_stage_id', $leadStageId))
                ->where('is_active', true)
                ->exists();

            if ($exists) {
                return (int) $requestedId;
            }
        }

        if (!$leadStageId) {
            return null;
        }

        return LeadStageSubStage::query()
            ->where('lead_stage_id', $leadStageId)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');
    }

    private function leadStagePayload(string $q = '')
    {
        return LeadStage::query()
            ->select(['id', 'key', 'name', 'color', 'icon', 'sort_order', 'is_default'])
            ->where('is_active', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('key', 'like', "%{$q}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(100)
            ->get()
            ->map(fn(LeadStage $stage) => [
                'id' => (int) $stage->id,
                'stage_id' => (int) $stage->id,
                'lead_stage_id' => (int) $stage->id,
                'stage' => (string) $stage->name,
                'name' => (string) $stage->name,
                'key' => (string) $stage->key,
                'color' => $stage->color,
                'icon' => $stage->icon,
                'sort_order' => (int) ($stage->sort_order ?? 0),
                'is_default' => (bool) $stage->is_default,
            ])
            ->values();
    }

    private function phasePayload(TaskPhase $phase): array
    {
        $leadStageId = $phase->lead_stage_id;

        return [
            'id' => (int) $phase->id,
            'product_id' => (int) $phase->product_id,
            'section_id' => $phase->section_id ? (int) $phase->section_id : null,
            'phase_name' => $phase->phase_name,
            'name' => $phase->phase_name,
            'stage_id' => $leadStageId ? (int) $leadStageId : null,
            'lead_stage_id' => $leadStageId ? (int) $leadStageId : null,
            'lead_sub_stage_id' => $phase->lead_sub_stage_id ? (int) $phase->lead_sub_stage_id : null,
            'stage_name' => $phase->leadStage?->name,
            'lead_stage_name' => $phase->leadStage?->name,
            'lead_sub_stage_name' => $phase->leadSubStage?->name,
            'status' => $phase->status,
            'version' => $phase->version,
            'order' => $phase->order !== null ? (int) $phase->order : null,
        ];
    }

    private function activityPayload(PhaseActivities $activity): array
    {
        $leadStageId = $activity->lead_stage_id;

        return [
            'id' => (int) $activity->id,
            'product_id' => (int) $activity->product_id,
            'section_id' => $activity->section_id ? (int) $activity->section_id : null,
            'phase_id' => $activity->phase_id ? (int) $activity->phase_id : null,
            'title' => $activity->title,
            'description' => $activity->description,
            'duration' => $activity->duration,
            'duration_type' => $activity->duration_type,
            'notes' => $activity->notes,
            'priority' => $activity->priority,
            'percent' => $activity->percent !== null ? (float) $activity->percent : null,
            'status' => $activity->status,
            'link' => $activity->link,
            'photo' => $activity->photo,
            'photo_required' => $activity->photo === '__REQUIRED__',
            'sort_order' => (int) ($activity->sort_order ?? 0),

            // Compatibility aliases.
            'stage_id' => $leadStageId ? (int) $leadStageId : null,
            'lead_stage_id' => $leadStageId ? (int) $leadStageId : null,
            'lead_sub_stage_id' => $activity->lead_sub_stage_id ? (int) $activity->lead_sub_stage_id : null,
            'stage_name' => $activity->leadStage?->name,
            'lead_stage_name' => $activity->leadStage?->name,
            'lead_sub_stage_name' => $activity->leadSubStage?->name,
        ];
    }
}
