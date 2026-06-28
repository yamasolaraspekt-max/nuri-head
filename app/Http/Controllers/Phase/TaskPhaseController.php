<?php

namespace App\Http\Controllers\Phase;
use App\Http\Controllers\Controller;

use App\Models\ArticleGroup;
use App\Models\TaskPhase;
use App\Models\PhaseActivities;
use App\Models\TaskSubTask;
use Illuminate\Http\Request;
use App\Models\ProjectMontageChecklist;
use App\Models\PhaseSection;
use App\Models\Stage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use DB;

class TaskPhaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $selectedProduct = $request->query('product'); // ?product=ID in URL
        $serviceSort = $request->query('service_sort', 'asc') === 'desc' ? 'desc' : 'asc';

        // Product list — unchanged behavior, only safer search handling.
        $articlesQuery = ArticleGroup::query();

        if ($search !== '') {
            $articlesQuery->where('article_group', 'LIKE', "%{$search}%");
        }

        $articles = $articlesQuery
            ->orderBy('article_group', 'asc')
            ->get();

        // Fallback: if no product explicitly selected, take first one.
        if (!$selectedProduct && $articles->count() > 0) {
            $selectedProduct = $articles->first()->id;
        }

        $currentProduct = $selectedProduct
            ? ArticleGroup::find($selectedProduct)
            : null;

        // NEW FEATURE:
        // The manager must not show duplicated phase sections.
        // We keep the first section per normalized name and expose duplicate metadata
        // so the blade can show a cleanup button without changing old behavior.
        $services = collect();
        $duplicateGroups = collect();

        if ($currentProduct) {
            $services = $this->uniquePhaseSectionsForProduct((int) $currentProduct->id, $serviceSort);
            $duplicateGroups = $this->duplicatePhaseSectionGroups((int) $currentProduct->id);
        }

        return view('admin.task.phase.manager', [
            'articles' => $articles,
            'currentProduct' => $currentProduct,
            'services' => $services,
            'search' => $search,
            'serviceSort' => $serviceSort,
            'duplicatePhaseSectionGroups' => $duplicateGroups,
            'duplicatePhaseSectionCount' => (int) $duplicateGroups->sum('duplicate_count'),
        ]);
    }

    public function details($product)
    {
        // NEW FEATURE:
        // Prevent duplicate phase sections from being rendered in the product details page.
        $data = DB::table('phase_sections')
            ->join('article_groups', 'article_groups.id', '=', 'phase_sections.product_id')
            ->select('phase_sections.*', 'article_groups.article_group', 'article_groups.image')
            ->where('phase_sections.product_id', $product)
            ->whereNull('phase_sections.deleted_at')
            ->orderBy('phase_sections.phase_section')
            ->orderBy('phase_sections.id')
            ->get()
            ->unique(function ($row) {
                return $this->normalizePhaseSectionValue($this->phaseSectionDisplayValue($row));
            })
            ->values();

        $stages = DB::table('stages')
            ->join('article_groups', 'article_groups.id', '=', 'stages.product_id')
            ->select('stages.*', 'article_groups.article_group')
            ->where('stages.product_id', $product)
            ->get();

        $groupedStages = $stages->groupBy('version');

        return view('admin.task.phase.phase_details')
            ->with('data', $data)
            ->with('groupedStages', $groupedStages);
    }

    public function manage(Request $request, $product, $section_id)
    {
        // Query params
        $versionParam = trim((string) $request->query('version', ''));
        $stageIdParam = $request->query('stage_id'); // highlight-only (NOT filtering the left tree)

        // -----------------------------
        // 1) Load section + base data
        // -----------------------------
        $section = DB::table('phase_sections')->where('id', $section_id)->first();
        if (!$section)
            abort(404, 'Section not found');

        // -----------------------------
        // 2) ✅ Load ONLY stages for: product + this section_id
        // -----------------------------
        $stages = collect(
            DB::table('stages')
                ->join('article_groups', 'article_groups.id', '=', 'stages.product_id')
                ->select('stages.*', 'article_groups.article_group')
                ->where('stages.product_id', (int) $product)
                ->where('stages.phase_section_id', (int) $section_id)   // ✅ IMPORTANT FILTER
                ->whereNull('stages.deleted_at')
                ->orderBy('stages.sort_order')
                ->orderBy('stages.id')
                ->get()
        );

        $groupedStages = $stages->groupBy(fn($s) => $s->version ?? '');

        // -----------------------------
        // 3) Determine current version (DEFAULT = V1 if it exists)
        // -----------------------------
        $currentVersion = null;

        if ($versionParam !== '') {
            $currentVersion = $versionParam;
        } elseif ($groupedStages->has('V1')) {
            $currentVersion = 'V1';
        } else {
            $currentVersion = optional($stages->first())->version; // fallback
        }

        if ($currentVersion !== null && $currentVersion !== '' && !$groupedStages->has($currentVersion)) {
            $currentVersion = optional($stages->first())->version;
        }

        $stagesInCurrentVersion = $currentVersion
            ? ($groupedStages->get($currentVersion) ?? collect())
            : collect();

        $defaultStageInVersion = $stagesInCurrentVersion->firstWhere('default', 'yes')
            ?: $stagesInCurrentVersion->first();

        // -----------------------------
        // 4) current stage (highlight only)
        // -----------------------------
        $currentStageId = $stageIdParam ?: optional($defaultStageInVersion)->id;

        // ensure highlight stage belongs to current version + this section
        if ($currentStageId && $currentVersion) {
            $chosenStage = $stages->firstWhere('id', (int) $currentStageId);
            if (!$chosenStage || ($chosenStage->version !== $currentVersion)) {
                $currentStageId = optional($defaultStageInVersion)->id;
            }
        }

        $currentStageName = optional($stages->firstWhere('id', (int) $currentStageId))->stage
            ?: optional($defaultStageInVersion)->stage;

        // -----------------------------
        // 5) Phases: product + section + version
        // -----------------------------
        $phases = TaskPhase::with([
            'activities' => function ($q) {
                $q->whereNull('parent_id')->orderBy('sort_order');
            }
        ])
            ->where('product_id', (int) $product)
            ->where('section_id', (int) $section_id)
            ->whereNull('deleted_at')
            ->when($currentVersion, function ($q) use ($currentVersion) {
                $q->where(function ($qq) use ($currentVersion) {
                    $qq->where('version', $currentVersion)
                        ->orWhereNull('version'); // legacy optional
                });
            })
            ->orderBy('order')
            ->get();

        // -----------------------------
        // 6) Other data (unchanged)
        // -----------------------------
        $positions = DB::table('positions')->where('status', 'Published')->get();

        $departments = DB::table('departments')
            ->join('branches', 'branches.id', '=', 'departments.branch_id')
            ->select('departments.department_name', 'departments.id', 'departments.parent_id', 'branches.branch')
            ->where('departments.status', 'Published')
            ->get();

        $articles = DB::table('products')
            ->select('id', 'article_no', 'product', 'article_group', 'sub_article')
            ->where('status', 'Published')
            ->get();

        $translatePhase = [
            'complete' => 'Komplettlösung',
            'montage' => 'Montage',
            'product' => 'Produkt',
            'plan' => 'Planung',
            'maintenance' => 'Wartung',
            'repair' => 'Reparatur',
            'others' => 'Sonstiges',
        ];

        // -----------------------------
        // 7) AJAX (left tree reload)
        // -----------------------------
        if ($request->ajax()) {
            return view('admin.task.phase.partials.folder-structure-initial', [
                'phases' => $phases,
                'groupedStages' => $groupedStages,
                'currentVersion' => $currentVersion,
                'currentStageId' => $currentStageId,
            ]);
        }

        // -----------------------------
        // 8) Full page
        // -----------------------------
        return view('admin.task.phase.phase_management', [
            'phases' => $phases,
            'groupedStages' => $groupedStages,
            'section' => $section,
            'positions' => $positions,
            'departments' => $departments,
            'articles' => $articles,
            'translatePhase' => $translatePhase,
            'defaultStage' => $currentStageName,
            'currentVersion' => $currentVersion,
            'currentStageId' => $currentStageId,
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $article = ArticleGroup::all();
        return view('admin.task.phase.phase_create')->with('article', $article);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        \Log::info('save New Phase', [$request->all()]);

        $request->validate([
            'phase_name' => 'required',
            'product_id' => 'required|exists:article_groups,id',
            'section_id' => 'required|exists:phase_sections,id',
            'stage_id' => 'required|exists:stages,id',
            'status' => 'nullable|string',
            'count' => 'nullable|integer',
            'stage' => 'nullable'
        ]);

        $stageModel = Stage::find($request->stage_id);

        \Log::info('🔍 StageModel:', ['stage_id' => $request->stage_id, 'stageModel' => $stageModel]);

        if (!$stageModel || !$stageModel->stage) {
            \Log::warning('❌ Stage not found or empty', ['stage_id' => $request->stage_id]);
            return redirect()->back()->with('delete_msg', 'Der Arbeitsprozess wurde nicht gefunden');
        }
        $stage = $stageModel->stage; // ✅ You forgot this line


        $data = new TaskPhase;
        $data->phase_name = $request->phase_name;
        $data->product_id = $request->product_id;
        $data->section_id = $request->section_id;
        $data->section_name = $request->section_name;
        $data->stage_id = $request->stage_id;
        $data->version = $request->version;

        $data->status = "Published";
        $data->count = 0;
        $data->stage = $stage;
        $data->save();


        return redirect()->back()->with('save_msg', 'Die Daten wurden erfolgreich gespeichert');
    }


    // Saveing the manuall phse in project 
    public function storeNewPhase(Request $request)
    {
        $request->validate([
            'phase_name' => 'required',
            'product_id' => 'required|exists:article_groups,id',
            'section_name' => 'required|string',
            'status' => 'nullable|string',
            'count' => 'nullable|integer',
            'stage' => 'nullable',
        ]);

        // 🔍 Try to find section by name + product
        $section = DB::table('phase_sections')
            ->where('product_id', $request->product_id)
            ->where('phase_section', $request->section_name)
            ->first();

        // ✏️ Create section if not found
        if (!$section) {
            $section_id = DB::table('phase_sections')->insertGetId([
                'product_id' => $request->product_id,
                'phase_section' => $request->section_name,
                'status' => 'Published',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $section_id = $section->id;
        }

        // 💾 Save the phase
        $data = new TaskPhase;
        $data->phase_name = $request->phase_name;
        $data->product_id = $request->product_id;
        $data->section_id = $section_id;
        $data->section_name = $request->section_name;
        $data->status = $request->status ?? "Published";
        $data->count = $request->count ?? 0;
        $data->stage = $request->stage ?? 'project';
        $data->save();

        $checklist = new ProjectMontageChecklist;
        $checklist->product_id = $request->product_id;
        $checklist->employee_id = auth()->user()->name;
        $checklist->list_name = 'New Draft';
        $checklist->plan_montage = 1;
        $checklist->supplier_section = 1;
        $checklist->cran_section = 1;
        $checklist->old_facility = 1;
        $checklist->photo_section = 1;
        $checklist->commission = 1;
        $checklist->save();




        return response()->json(['success' => true, 'message' => 'Die Phase wurde erfolgreich gespeichert']);
    }

    /**
     * Display the specified resource.
     */

    public function order($id)
    {
        // Fetch the task phases for the given product, ordered by 'order' field
        $data = TaskPhase::where('product_id', $id)->orderBy('order')->get();
        $title = $data->first(); // Assuming $title refers to the product or first phase
        return view('admin.task.phase.order')->with('data', $data)->with('title', $title);
    }
    public function updateOrder(Request $request)
    {
        $order = $request->order;

        if (!is_array($order)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid order data'], 422);
        }

        foreach ($order as $index => $id) {
            $taskPhase = TaskPhase::find($id);
            if ($taskPhase) {
                $taskPhase->order = $index;
                $taskPhase->save();
            }
        }

        return response()->json(['status' => 'success']);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function clone(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'id' => 'required|exists:task_phases,id',
            'phase_name' => 'required',
            'product_id' => 'required|exists:article_groups,id',
            'copy_to' => 'required|exists:article_groups,id',
        ]);

        // Find the existing task phase to be cloned
        $originalTaskPhase = TaskPhase::find($validated['id']);

        if ($originalTaskPhase) {
            // Create a new TaskPhase instance with cloned data
            $taskPhase = new TaskPhase;
            $taskPhase->phase_name = $validated['phase_name'];
            $taskPhase->product_id = $validated['copy_to'];
            $taskPhase->status = "Published";
            $taskPhase->count = 0;
            $taskPhase->stage = 0;
            $taskPhase->save();

            // Clone phase activities
            $activities = DB::table('phase_activities')
                ->where('product_id', $validated['product_id'])
                ->where('phase_id', $validated['id'])
                ->get();

            foreach ($activities as $activity) {
                PhaseActivities::create([
                    'phase_id' => $taskPhase->id, // new phase ID
                    'product_id' => $validated['copy_to'], // new product ID
                    'initial' => $activity->initial,
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'status' => $activity->status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Clone sub-tasks
            $subTasks = DB::table('task_sub_tasks')
                ->where('task_id', $validated['id'])
                ->get();

            foreach ($subTasks as $subTask) {
                TaskSubTasks::create([
                    'phase_id' => $taskPhase->id, // new phase ID for consistency
                    'task_id' => $taskPhase->id, // reference to the new task phase
                    'task_title' => $subTask->task_title,
                    'duration' => $subTask->duration,
                    'duration_type' => $subTask->duration_type,
                    'description' => $subTask->description,
                    'status' => $subTask->status,
                    'image' => $subTask->image,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->back()->with('save_msg', 'Data copied successfully');
        }

        return redirect()->back()->with('error_msg', 'Original task phase not found');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        \Log::info("🔄 Update Phase Request: ", $request->all());

        $request->validate([
            'phase_name' => 'required|string|max:255',
            'version' => 'required|string',
            'stage_id' => 'required|integer|exists:stages,id',
        ]);

        $phase = TaskPhase::findOrFail($id);

        // 🧠 Get the stage name from ID
        $stageName = DB::table('stages')->where('id', $request->stage_id)->value('stage');

        if (!$stageName) {
            return response()->json(['error' => 'Ungültige Phase'], 422);
        }

        $phase->update([
            'phase_name' => $request->phase_name,
            'version' => $request->version,
            'stage' => $stageName
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Phase erfolgreich aktualisiert.',
            'data' => $phase
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $data = TaskPhase::find($id);
        $data->delete();

        return response()->json(['success', 'Phase deleted Successfully']);
    }


    public function storeActivity(Request $request)
    {
        \Log::info('request activities: ', [$request->all()]);
        $request->validate([
            'product_id' => 'required|exists:article_groups,id',
            'section_id' => 'required|exists:phase_sections,id',
            'phase_id' => 'required|exists:task_phases,id',
            'title' => 'required|string|max:255',
        ]);


        $activity = PhaseActivities::create([
            'phase_id' => $request->phase_id,
            'product_id' => $request->product_id,
            'section_id' => $request->section_id,
            'section_name' => $request->section_name,
            'parent_id' => $request->parent_id,
            'title' => $request->title,
            'duration' => $request->duration,
            'description' => $request->description,
            'photo' => $request->photo,
            'answered_by' => $request->answered_by,
            'status' => $request->status ?? 'published',
            'duration_type' => $request->duration_type,
        ]);

        return response()->json($activity);
    }


    // Load phase_sections for a selected product
    public function getSectionsByProduct($product_id)
    {
        $translate = [
            'complete' => 'Komplettlösung',
            'montage' => 'Montage',
            'product' => 'Produkt',
            'plan' => 'Planung',
            'maintenance' => 'Wartung',
            'repair' => 'Reparatur',
            'reclaim' => 'Reklamation',
            'others' => 'Sonstiges',
        ];

        // NEW FEATURE:
        // Return only unique sections for dropdowns/manager AJAX consumers.
        $sections = $this->uniquePhaseSectionsForProduct((int) $product_id)->map(function ($section) use ($translate) {
            $value = $this->phaseSectionDisplayValue($section);

            return [
                'id' => $section->id,
                'phase_section' => $translate[$value] ?? $value,
                'duplicate_count' => (int) ($section->duplicate_count ?? 0),
            ];
        })->values();

        return response()->json([
            'sections' => $sections,
            'duplicates' => $this->duplicatePhaseSectionGroups((int) $product_id)->values(),
        ]);
    }


    // Load task_phases for a selected section
    public function getPhasesBySection($section_id)
    {
        $phases = TaskPhase::where('section_id', $section_id)->get();
        return response()->json(['phases' => $phases]);
    }


    public function createNewPhase(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:article_groups,id',
            'section_id' => 'required|exists:phase_sections,id',
            'phase_name' => 'required|string|max:255',
        ]);

        // Double-check section belongs to selected product
        $section = PhaseSection::where('id', $request->section_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$section) {
            return response()->json([
                'error' => 'Ungültiger Bereich für das ausgewählte Produkt.'
            ], 422);
        }

        $phase = TaskPhase::create([
            'product_id' => $request->product_id,
            'section_id' => $section->id,
            'section_name' => $section->phase_section, // ✅ Safely set
            'phase_name' => $request->phase_name,
            'status' => 'draft',
        ]);

        return response()->json([
            'id' => $phase->id,
            'phase_name' => $phase->phase_name
        ]);
    }




    public function searchPhases(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:article_groups,id',
            'section_id' => 'required|exists:phase_sections,id',
            'q' => 'nullable|string'
        ]);

        $query = TaskPhase::where('product_id', $request->product_id)
            ->where('section_id', $request->section_id);

        if ($request->has('q')) {
            $query->where('phase_name', 'like', '%' . $request->q . '%');
        }

        $phases = $query->limit(20)->get();

        return response()->json($phases->map(function ($p) {
            return ['id' => $p->id, 'text' => $p->phase_name];
        }));
    }


    public function getPhasesWithActivities(Request $request)
    {
        $productId = $request->query('product_id');
        $serviceId = $request->query('service_id');

        if (!$productId) {
            return response()->json(['error' => 'product_id is required'], 422);
        }

        $phasesQuery = TaskPhase::where('product_id', $productId);

        if (!empty($serviceId)) {
            $phasesQuery->where('section_id', $serviceId);
        }

        $phases = $phasesQuery->orderBy('order')->get();

        $phaseIds = $phases->pluck('id');

        $activities = PhaseActivities::whereIn('phase_id', $phaseIds)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'phases' => $phases,
            'activities' => $activities,
        ]);
    }


    public function toggleStatus(Request $request, $id)
    {
        $phase = \App\Models\TaskPhase::findOrFail($id);
        $phase->status = ($phase->status === 'Published') ? 'Unpublished' : 'Published';
        $phase->save();

        return response()->json([
            'success' => true,
            'status' => $phase->status
        ]);
    }

    public function getStagesByVersion(Request $request)
    {
        return response()->json(
            DB::table('stages')
                ->where('version', $request->version)
                ->where('product_id', $request->product_id)
                ->whereNull('deleted_at')
                ->select('id', 'stage', 'default')
                ->orderBy('sort_order')
                ->get()
        );
    }


    public function getStageDetails($id)
    {
        $stage = DB::table('stages')->where('id', $id)->first();

        if (!$stage) {
            return response()->json(['error' => 'Stage not found'], 404);
        }

        return response()->json([
            'id' => $stage->id,
            'stage' => $stage->stage,
        ]);
    }


    public function getPhasesByStage(Request $request)
    {
        $stageName = $request->stage;
        $productId = $request->product_id;
        $version = $request->version;

        $phases = TaskPhase::with([
            'activities' => function ($q) {
                $q->whereNull('parent_id')->orderBy('sort_order');
            }
        ])
            ->where('product_id', $productId)
            ->where('stage', $stageName)
            ->whereNull('deleted_at')
            ->orderBy('order')
            ->get();

        return view('admin.task.phase.partials.folder-structure-initial', [
            'phases' => $phases,
            'groupedStages' => collect(), // not used in partial
            'currentVersion' => $version ?? null // important for JS fallback
        ])->render();
    }


    public function getPhasesByVersion(Request $request)
    {
        $productId = $request->product_id;
        $version = $request->version;
        $sectionId = $request->section_id;

        \Log::info('📦 getPhasesByVersion request', [
            'product_id' => $productId,
            'version' => $version,
            'section_id' => $sectionId,
        ]);

        if (!$productId || !$version) {
            return '<div class="text-danger p-2">⚠️ Produkt oder Version fehlt.</div>';
        }

        // 1) Load all stages of this product+version
        $stagesInVersion = DB::table('stages')
            ->where('product_id', $productId)
            ->where('version', $version)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->get();

        \Log::info('✅ Stages in version', $stagesInVersion->pluck('stage')->toArray());

        if ($stagesInVersion->isEmpty()) {
            return '<div class="text-danger p-2">⚠️ Keine Stages für diese Version gefunden.</div>';
        }

        // 2) Load all phases of this product + section + version
        //    (keep old records without version visible if you want)
        $phases = TaskPhase::with([
            'activities' => function ($q) {
                $q->whereNull('parent_id')->orderBy('sort_order');
            }
        ])
            ->where('product_id', $productId)
            ->where('section_id', $sectionId)
            ->whereNull('deleted_at')
            ->where(function ($q) use ($version) {
                $q->where('version', $version)
                    ->orWhereNull('version');  // optional: keep legacy phases without version
            })
            ->orderBy('order')
            ->get();

        \Log::info('✅ Phases loaded', [
            'count' => $phases->count(),
            'stages' => $phases->pluck('stage')->unique()->values()->toArray(),
        ]);

        // 3) Render the partial exactly with what the Blade expects
        return view('admin.task.phase.partials.folder-structure-initial', [
            'phases' => $phases,
            'groupedStages' => collect([$version => $stagesInVersion]),
            'currentVersion' => $version,
        ])->render();
    }

    public function getStageVersion(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:article_groups,id',
            'version' => 'nullable|string'
        ]);

        $query = DB::table('stages')
            ->where('product_id', $request->product_id)
            ->whereNull('deleted_at');

        // Optional: filter by version
        if ($request->filled('version')) {
            $query->where('version', $request->version);
        }

        $stages = $query->orderBy('sort_order')
            ->select('id', 'stage', 'version', 'default')
            ->get();

        return response()->json($stages);
    }


    public function getStageVersions(Request $request)
    {
        $versions = DB::table('stages')
            ->where('product_id', $request->product_id)
            ->whereNull('deleted_at')
            ->pluck('version')
            ->filter()
            ->unique()
            ->values();

        return response()->json($versions);
    }

    public function getPhaseStage(Request $request)
    {
        \Log::info('getPhasesByStage request:', $request->all());

        $phases = TaskPhase::where('stage_id', $request->stage)
            ->where('version', $request->version)
            ->where('product_id', $request->product_id)
            ->whereNull('deleted_at')
            ->select('id', 'phase_name')
            ->get();

        \Log::info('Phases found:', $phases->toArray());

        return response()->json($phases);
    }

    public function getActivitiesByStage(Request $request)
    {
        $stageId = $request->stage;
        $version = $request->version;
        $productId = $request->product_id;

        $phases = TaskPhase::with('activities')
            ->where('stage_id', $stageId)
            ->where('version', $version)
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->get();

        return response()->json($phases);
    }

    public function transferPhase(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mode' => 'required|in:move,duplicate',
            'phase_id' => 'required|integer|exists:task_phases,id',
            'to_stage_id' => 'required|integer|exists:stages,id',
            'target_index' => 'required|integer|min:0',
        ]);

        $phase = TaskPhase::query()->findOrFail($data['phase_id']);
        $targetStage = DB::table('stages')->where('id', $data['to_stage_id'])->first();
        if (!$targetStage) {
            return response()->json(['success' => false, 'message' => 'Target stage not found'], 422);
        }

        $fromStageId = $phase->stage_id;

        $result = DB::transaction(function () use ($data, $phase, $targetStage, $fromStageId) {

            if ($data['mode'] === 'move') {
                // Update phase stage + version + stage name
                $phase->stage_id = $targetStage->id;
                $phase->stage = $targetStage->stage;
                $phase->version = $targetStage->version;
                $phase->save();

                // Ensure phase order in target stage
                $this->placePhaseInStage($phase->id, $phase->product_id, $phase->section_id, $phase->version, $targetStage->id, $data['target_index']);

                // Re-sequence source stage after removal
                if ($fromStageId && (int) $fromStageId !== (int) $targetStage->id) {
                    $this->resequencePhases($phase->product_id, $phase->section_id, $targetStage->version /*safe*/ , $fromStageId);
                }

                return ['new_phase_id' => $phase->id];
            }

            // duplicate
            $newPhase = $phase->replicate();
            $newPhase->stage_id = $targetStage->id;
            $newPhase->stage = $targetStage->stage;
            $newPhase->version = $targetStage->version;
            $newPhase->push();

            // Copy ALL activities of the phase (including children)
            $this->duplicatePhaseActivities($phase->id, $newPhase->id);

            // Place new phase
            $this->placePhaseInStage($newPhase->id, $newPhase->product_id, $newPhase->section_id, $newPhase->version, $targetStage->id, $data['target_index']);

            return ['new_phase_id' => $newPhase->id];
        });

        return response()->json(['success' => true] + $result);
    }

    public function transferActivity(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mode' => 'required|in:move,duplicate',
            'activity_id' => 'required|integer|exists:phase_activities,id',
            'from_phase_id' => 'nullable|integer|exists:task_phases,id',
            'to_phase_id' => 'required|integer|exists:task_phases,id',
            'target_index' => 'required|integer|min:0',
            // Optional future: allow dropping into another activity as parent
            'target_parent_id' => 'nullable|integer|exists:phase_activities,id',
        ]);

        $activity = PhaseActivities::query()->findOrFail($data['activity_id']);
        $fromPhaseId = $data['from_phase_id'] ?? $activity->phase_id;
        $toPhaseId = $data['to_phase_id'];
        $targetParentId = $data['target_parent_id'] ?? null;

        $result = DB::transaction(function () use ($data, $activity, $fromPhaseId, $toPhaseId, $targetParentId) {

            if ($data['mode'] === 'move') {
                $ids = $this->descendantIds($activity->id); // includes root

                // Move subtree to new phase
                PhaseActivities::query()
                    ->whereIn('id', $ids)
                    ->update(['phase_id' => $toPhaseId]);

                // Root may become child in target (optional)
                $activity->refresh();
                $activity->phase_id = $toPhaseId;
                $activity->parent_id = $targetParentId;
                $activity->save();

                // place root at target_index among target siblings (parent scope)
                $this->placeActivityInScope($toPhaseId, $targetParentId, $activity->id, $data['target_index']);

                // resequence source siblings
                $this->resequenceActivities($fromPhaseId, $activity->parent_id);

                return ['new_activity_id' => $activity->id];
            }

            // duplicate subtree
            $newRootId = $this->duplicateActivityTree(
                rootActivityId: $activity->id,
                toPhaseId: $toPhaseId,
                targetParentId: $targetParentId
            );

            $this->placeActivityInScope($toPhaseId, $targetParentId, $newRootId, $data['target_index']);

            return ['new_activity_id' => $newRootId];
        });

        return response()->json(['success' => true] + $result);
    }

    /**
     * Helpers
     */
    private function resequencePhases(int $productId, int $sectionId, ?string $version, int $stageId): void
    {
        $q = TaskPhase::query()
            ->where('product_id', $productId)
            ->where('section_id', $sectionId)
            ->where('stage_id', $stageId)
            ->whereNull('deleted_at');

        if ($version !== null) {
            $q->where('version', $version);
        }

        $ids = $q->orderBy('order')->orderBy('id')->pluck('id')->all();

        foreach ($ids as $i => $id) {
            TaskPhase::query()->where('id', $id)->update(['order' => $i]);
        }
    }

    private function placePhaseInStage(int $phaseId, int $productId, int $sectionId, ?string $version, int $stageId, int $targetIndex): void
    {
        $q = TaskPhase::query()
            ->where('product_id', $productId)
            ->where('section_id', $sectionId)
            ->where('stage_id', $stageId)
            ->whereNull('deleted_at');

        if ($version !== null) {
            $q->where('version', $version);
        }

        $ids = $q->orderBy('order')->orderBy('id')->pluck('id')->all();

        $ids = array_values(array_filter($ids, fn($id) => (int) $id !== (int) $phaseId));
        $targetIndex = max(0, min($targetIndex, count($ids)));
        array_splice($ids, $targetIndex, 0, [$phaseId]);

        foreach ($ids as $i => $id) {
            TaskPhase::query()->where('id', $id)->update(['order' => $i]);
        }
    }

    private function resequenceActivities(int $phaseId, ?int $parentId): void
    {
        $ids = PhaseActivities::query()
            ->where('phase_id', $phaseId)
            ->whereNull('deleted_at')
            ->where($parentId === null ? 'parent_id' : 'parent_id', $parentId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        foreach ($ids as $i => $id) {
            PhaseActivities::query()->where('id', $id)->update(['sort_order' => $i]);
        }
    }

    private function placeActivityInScope(int $phaseId, ?int $parentId, int $activityId, int $targetIndex): void
    {
        $q = PhaseActivities::query()
            ->where('phase_id', $phaseId)
            ->whereNull('deleted_at');

        if ($parentId === null) {
            $q->whereNull('parent_id');
        } else {
            $q->where('parent_id', $parentId);
        }

        $ids = $q->orderBy('sort_order')->orderBy('id')->pluck('id')->all();

        $ids = array_values(array_filter($ids, fn($id) => (int) $id !== (int) $activityId));
        $targetIndex = max(0, min($targetIndex, count($ids)));
        array_splice($ids, $targetIndex, 0, [$activityId]);

        foreach ($ids as $i => $id) {
            PhaseActivities::query()->where('id', $id)->update(['sort_order' => $i]);
        }
    }

    private function duplicatePhaseActivities(int $fromPhaseId, int $toPhaseId): void
    {
        $rows = PhaseActivities::query()
            ->where('phase_id', $fromPhaseId)
            ->whereNull('deleted_at')
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('parent_id')
            ->orderBy('id')
            ->get();

        $map = []; // old_id => new_id

        // Multi-pass in case of deeper trees
        $remaining = $rows->keyBy('id')->all();
        $guard = 0;

        while (!empty($remaining) && $guard++ < 10_000) {
            foreach ($remaining as $oldId => $row) {
                $oldParent = $row->parent_id;

                if ($oldParent !== null && !isset($map[$oldParent])) {
                    continue; // parent not copied yet
                }

                $copy = $row->replicate();
                $copy->phase_id = $toPhaseId;
                $copy->parent_id = $oldParent === null ? null : $map[$oldParent];
                $copy->push();

                $map[$oldId] = $copy->id;
                unset($remaining[$oldId]);
            }
        }
    }

    private function duplicateActivityTree(int $rootActivityId, int $toPhaseId, ?int $targetParentId): int
    {
        $ids = $this->descendantIds($rootActivityId);

        $rows = PhaseActivities::query()
            ->whereIn('id', $ids)
            ->whereNull('deleted_at')
            ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$rootActivityId])
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('parent_id')
            ->orderBy('id')
            ->get()
            ->keyBy('id');

        $map = []; // old => new
        $newRootId = null;

        $remaining = $rows->all();
        $guard = 0;

        while (!empty($remaining) && $guard++ < 10_000) {
            foreach ($remaining as $oldId => $row) {
                $oldParent = $row->parent_id;

                // root: force into targetParentId
                if ((int) $oldId === (int) $rootActivityId) {
                    $copy = $row->replicate();
                    $copy->phase_id = $toPhaseId;
                    $copy->parent_id = $targetParentId;
                    $copy->push();

                    $map[$oldId] = $copy->id;
                    $newRootId = $copy->id;

                    unset($remaining[$oldId]);
                    continue;
                }

                // non-root: require parent mapping
                if ($oldParent !== null && !isset($map[$oldParent])) {
                    continue;
                }

                $copy = $row->replicate();
                $copy->phase_id = $toPhaseId;
                $copy->parent_id = $oldParent === null ? null : $map[$oldParent];
                $copy->push();

                $map[$oldId] = $copy->id;
                unset($remaining[$oldId]);
            }
        }

        return (int) $newRootId;
    }

    // If you don't already have it:
    private function descendantIds(int $rootId): array
    {
        $ids = [$rootId];
        $queue = [$rootId];

        while (!empty($queue)) {
            $children = PhaseActivities::query()
                ->whereIn('parent_id', $queue)
                ->pluck('id')
                ->all();

            $children = array_values(array_diff($children, $ids));
            if (empty($children))
                break;

            $ids = array_merge($ids, $children);
            $queue = $children;
        }

        return $ids;
    }




    /**
     * AJAX endpoint for the manager page.
     * Keeps the old manager data shape, but sends duplicate metadata as well.
     */
    public function managerSectionsByProduct(Request $request, $product): JsonResponse
    {
        $sort = $request->query('service_sort', 'asc') === 'desc' ? 'desc' : 'asc';

        $translate = [
            'complete' => 'Komplettlösung',
            'montage' => 'Montage',
            'product' => 'Produkt',
            'plan' => 'Planung',
            'maintenance' => 'Wartung',
            'repair' => 'Reparatur',
            'reclaim' => 'Reklamation',
            'others' => 'Sonstiges',
        ];

        $services = $this->uniquePhaseSectionsForProduct((int) $product, $sort)->map(function ($section) use ($translate) {
            $value = $this->phaseSectionDisplayValue($section);

            return [
                'id' => (int) $section->id,
                'product_id' => (int) $section->product_id,
                'phase_section' => $value,
                'phase_section_label' => $translate[$value] ?? $value,
                'duplicate_count' => (int) ($section->duplicate_count ?? 0),
                'created_at' => $section->created_at,
                'updated_at' => $section->updated_at,
                'manage_url' => url('phase_management/' . (int) $section->product_id . '/' . (int) $section->id),
            ];
        })->values();

        $duplicates = $this->duplicatePhaseSectionGroups((int) $product)->values();

        return response()->json([
            'success' => true,
            'services' => $services,
            'duplicates' => $duplicates,
            'duplicate_count' => (int) $duplicates->sum('duplicate_count'),
        ]);
    }

    /**
     * One-click duplicate cleanup for phase sections of a product.
     * It keeps the oldest section per normalized name, moves linked records to it,
     * then soft-deletes/deletes duplicate section rows.
     */
    public function cleanupDuplicatePhaseSections(Request $request, $product): JsonResponse
    {
        $productId = (int) $product;

        if (!ArticleGroup::whereKey($productId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Produkt wurde nicht gefunden.',
            ], 404);
        }

        $result = DB::transaction(function () use ($productId) {
            $groups = $this->rawDuplicatePhaseSectionGroups($productId);
            $deletedIds = [];
            $updated = [
                'task_phases' => 0,
                'phase_activities' => 0,
                'stages' => 0,
            ];

            foreach ($groups as $group) {
                $ids = $group->pluck('id')->map(fn($id) => (int) $id)->values()->all();

                if (count($ids) < 2) {
                    continue;
                }

                $keepId = (int) $ids[0];
                $duplicateIds = array_values(array_slice($ids, 1));

                if (empty($duplicateIds)) {
                    continue;
                }

                if (\Schema::hasTable('task_phases') && \Schema::hasColumn('task_phases', 'section_id')) {
                    $updated['task_phases'] += DB::table('task_phases')
                        ->whereIn('section_id', $duplicateIds)
                        ->update(['section_id' => $keepId, 'updated_at' => now()]);
                }

                if (\Schema::hasTable('phase_activities') && \Schema::hasColumn('phase_activities', 'section_id')) {
                    $updated['phase_activities'] += DB::table('phase_activities')
                        ->whereIn('section_id', $duplicateIds)
                        ->update(['section_id' => $keepId, 'updated_at' => now()]);
                }

                if (\Schema::hasTable('stages') && \Schema::hasColumn('stages', 'phase_section_id')) {
                    $updated['stages'] += DB::table('stages')
                        ->whereIn('phase_section_id', $duplicateIds)
                        ->update(['phase_section_id' => $keepId, 'updated_at' => now()]);
                }

                if (\Schema::hasColumn('phase_sections', 'deleted_at')) {
                    DB::table('phase_sections')
                        ->whereIn('id', $duplicateIds)
                        ->update(['deleted_at' => now(), 'updated_at' => now()]);
                } else {
                    DB::table('phase_sections')
                        ->whereIn('id', $duplicateIds)
                        ->delete();
                }

                $deletedIds = array_merge($deletedIds, $duplicateIds);
            }

            return [
                'deleted_ids' => $deletedIds,
                'deleted_count' => count($deletedIds),
                'updated' => $updated,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => $result['deleted_count'] > 0
                ? 'Doppelte Leistungen wurden bereinigt.'
                : 'Keine Duplikate gefunden.',
            'deleted_count' => $result['deleted_count'],
            'deleted_ids' => $result['deleted_ids'],
            'updated' => $result['updated'],
            'duplicates' => $this->duplicatePhaseSectionGroups($productId)->values(),
        ]);
    }

    private function uniquePhaseSectionsForProduct(int $productId, string $sort = 'asc')
    {
        $sort = $sort === 'desc' ? 'desc' : 'asc';

        $rows = PhaseSection::query()
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->orderBy('phase_section', $sort)
            ->orderBy('id')
            ->get();

        $duplicates = $rows
            ->groupBy(fn($row) => $this->normalizePhaseSectionValue($this->phaseSectionDisplayValue($row)))
            ->map(fn($group) => max(0, $group->count() - 1));

        return $rows
            ->filter(fn($row) => $this->normalizePhaseSectionValue($this->phaseSectionDisplayValue($row)) !== '')
            ->unique(fn($row) => $this->normalizePhaseSectionValue($this->phaseSectionDisplayValue($row)))
            ->values()
            ->map(function ($row) use ($duplicates) {
                $key = $this->normalizePhaseSectionValue($this->phaseSectionDisplayValue($row));
                $row->duplicate_count = (int) ($duplicates[$key] ?? 0);
                return $row;
            });
    }

    private function duplicatePhaseSectionGroups(int $productId)
    {
        return $this->rawDuplicatePhaseSectionGroups($productId)
            ->map(function ($group, $key) {
                $keep = $group->first();
                $duplicates = $group->slice(1)->values();

                return [
                    'key' => $key,
                    'name' => $this->phaseSectionDisplayValue($keep),
                    'keep_id' => (int) $keep->id,
                    'duplicate_ids' => $duplicates->pluck('id')->map(fn($id) => (int) $id)->values()->all(),
                    'duplicate_count' => $duplicates->count(),
                    'total_count' => $group->count(),
                ];
            })
            ->values();
    }

    private function rawDuplicatePhaseSectionGroups(int $productId)
    {
        return DB::table('phase_sections')
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get()
            ->filter(fn($row) => $this->normalizePhaseSectionValue($this->phaseSectionDisplayValue($row)) !== '')
            ->groupBy(fn($row) => $this->normalizePhaseSectionValue($this->phaseSectionDisplayValue($row)))
            ->filter(fn($group) => $group->count() > 1);
    }

    private function phaseSectionDisplayValue($section): string
    {
        $phaseSection = trim((string) ($section->phase_section ?? ''));
        return $phaseSection;
    }

    private function normalizePhaseSectionValue(?string $value): string
    {
        $value = trim((string) $value);
        $value = preg_replace('/\s+/u', ' ', $value) ?: '';

        return mb_strtolower($value, 'UTF-8');
    }

    public function apiMasterSetsSearch(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:article_groups,id'],
            'q' => ['nullable', 'string', 'max:200'],
            'status' => ['nullable', 'string', 'max:50'], // optional filter
        ]);

        $q = trim((string) ($data['q'] ?? ''));

        $sets = DB::table('master_sets as ms')
            ->where('ms.article_group_id', $data['product_id'])
            ->whereNull('ms.deleted_at')
            ->when(!empty($data['status']), fn($qq) => $qq->where('ms.status', $data['status']))
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('ms.name', 'like', "%{$q}%")
                        ->orWhere('ms.description', 'like', "%{$q}%");
                });
            })
            ->select([
                'ms.id',
                'ms.article_group_id',
                'ms.name',
                'ms.description',
                'ms.status',
                'ms.task_phase_id',
                'ms.phase_activity_id',
                'ms.created_at',
                'ms.updated_at',
            ])
            ->selectRaw("
                COALESCE((
                    SELECT SUM(COALESCE(c.qty,0) * COALESCE(c.unit_price,0))
                    FROM master_set_components c
                    WHERE c.master_set_id = ms.id
                ), 0) as components_total
            ")
            ->selectRaw("
                COALESCE((
                    SELECT SUM(COALESCE(l.hours,0) * COALESCE(l.hourly_rate,0))
                    FROM master_set_labor l
                    WHERE l.master_set_id = ms.id
                ), 0) as labor_total
            ")
            ->orderBy('ms.id', 'desc')
            ->limit(200)
            ->get();

        return response()->json($sets);
    }

    // -----------------------------------------
    // API: LINKED list for a target (phase/activity)
    // -----------------------------------------
    public function apiMasterSetsLinked(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['phase', 'activity'])],
            'target_id' => ['required', 'integer'],
            'product_id' => ['nullable', 'integer', 'exists:article_groups,id'], // optional
        ]);

        $query = DB::table('master_sets as ms')
            ->whereNull('ms.deleted_at')
            ->when(!empty($data['product_id']), fn($q) => $q->where('ms.article_group_id', $data['product_id']))
            ->select([
                'ms.id',
                'ms.article_group_id',
                'ms.name',
                'ms.description',
                'ms.status',
                'ms.task_phase_id',
                'ms.phase_activity_id',
                'ms.created_at',
                'ms.updated_at',
            ])
            ->selectRaw("
                COALESCE((
                    SELECT SUM(COALESCE(c.qty,0) * COALESCE(c.unit_price,0))
                    FROM master_set_components c
                    WHERE c.master_set_id = ms.id
                ), 0) as components_total
            ")
            ->selectRaw("
                COALESCE((
                    SELECT SUM(COALESCE(l.hours,0) * COALESCE(l.hourly_rate,0))
                    FROM master_set_labor l
                    WHERE l.master_set_id = ms.id
                ), 0) as labor_total
            ");

        if ($data['type'] === 'phase') {
            $query->where('ms.task_phase_id', $data['target_id']);
        } else {
            $query->where('ms.phase_activity_id', $data['target_id']);
        }

        $sets = $query->orderBy('ms.id', 'desc')->get();
        return response()->json($sets);
    }

    // -----------------------------------------
    // API: DETAILS for collapse (components + labor)
    // -----------------------------------------
    public function apiMasterSetShow(int $id): \Illuminate\Http\JsonResponse
    {
        $set = DB::table('master_sets')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$set) {
            return response()->json(['message' => 'Master set not found'], 404);
        }

        $components = DB::table('master_set_components as c')
            ->leftJoin('products as p', 'p.id', '=', 'c.product_id')
            ->where('c.master_set_id', $id)
            ->select([
                'c.id',
                'c.parent_id',
                'c.product_id',
                DB::raw("COALESCE(p.product,'') as product_name"),
                'p.article_no',
                'c.qty',
                'c.unit_price',
                'c.description',
                'c.sort_order',
                DB::raw("(COALESCE(c.qty,0) * COALESCE(c.unit_price,0)) as line_total"),
            ])
            ->orderByRaw('CASE WHEN c.parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('c.parent_id')
            ->orderBy('c.sort_order')
            ->orderBy('c.id')
            ->get();

        $labor = DB::table('master_set_labor as l')
            ->leftJoin('departments as dep', 'dep.id', '=', 'l.department_id')
            ->leftJoin('positions as pos', 'pos.id', '=', 'l.position_id')
            ->leftJoin('employees as e', 'e.id', '=', 'l.employee_id')
            ->where('l.master_set_id', $id)
            ->select([
                'l.id',
                'l.department_id',
                DB::raw("COALESCE(dep.department_name,'') as department_name"),
                'l.position_id',
                DB::raw("COALESCE(pos.position,'') as position_name"),
                'l.employee_id',
                DB::raw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as employee_name"),
                'l.hours',
                'l.hourly_rate',
                'l.sort_order',
                DB::raw("(COALESCE(l.hours,0) * COALESCE(l.hourly_rate,0)) as line_total"),
            ])
            ->orderBy('l.sort_order')
            ->orderBy('l.id')
            ->get();

        $componentsTotal = (float) $components->sum('line_total');
        $laborTotal = (float) $labor->sum('line_total');

        // build parent/child tree
        $byParent = [];
        foreach ($components as $row) {
            $pid = $row->parent_id ?? 0;
            $byParent[$pid] = $byParent[$pid] ?? [];
            $byParent[$pid][] = $row;
        }
        $build = function ($parentId) use (&$build, $byParent) {
            $items = $byParent[$parentId] ?? [];
            foreach ($items as $it) {
                $it->children = $build($it->id);
            }
            return $items;
        };

        return response()->json([
            'set' => $set,
            'totals' => [
                'components_total' => round($componentsTotal, 2),
                'labor_total' => round($laborTotal, 2),
                'total' => round($componentsTotal + $laborTotal, 2),
            ],
            'components' => $components,
            'components_tree' => $build(0),
            'labor' => $labor,
        ]);
    }


    // -----------------------------------------
    // LINK set to phase/activity (rule-safe)
    // -----------------------------------------
    public function linkMasterSet(Request $request): JsonResponse
    {
        $data = $request->validate([
            'master_set_id' => ['required', 'integer', 'exists:master_sets,id'],
            'type' => ['required', Rule::in(['phase', 'activity'])],
            'target_id' => ['required', 'integer'],
        ]);

        $set = DB::table('master_sets')
            ->where('id', $data['master_set_id'])
            ->whereNull('deleted_at')
            ->first();

        if (!$set) {
            return response()->json(['success' => false, 'message' => 'Master set not found'], 404);
        }

        // Validate target exists + enforce “phase vs activity” rules
        if ($data['type'] === 'activity') {
            $activity = DB::table('phase_activities')->where('id', $data['target_id'])->first();
            if (!$activity) {
                return response()->json(['success' => false, 'message' => 'Activity not found'], 404);
            }

            // RULE: if phase has ANY set linked, activity cannot link
            $phaseHasSet = DB::table('master_sets')
                ->whereNull('deleted_at')
                ->where('task_phase_id', $activity->phase_id)
                ->exists();

            if ($phaseHasSet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diese Phase hat bereits ein Master-Set. Aktivitäten können dann keine eigenen Sets haben.'
                ], 422);
            }

            // (Optional symmetric rule)
            // If THIS activity already has a set and you want only one per activity:
            // $activityHasSet = DB::table('master_sets')->whereNull('deleted_at')->where('phase_activity_id',$activity->id)->exists();

            DB::table('master_sets')
                ->where('id', $data['master_set_id'])
                ->update([
                    'phase_activity_id' => $activity->id,
                    'task_phase_id' => null,
                    'updated_at' => now(),
                ]);

            return response()->json(['success' => true, 'message' => 'Set erfolgreich mit Aktivität verknüpft.']);
        }

        // type === 'phase'
        $phase = DB::table('task_phases')->where('id', $data['target_id'])->first();
        if (!$phase) {
            return response()->json(['success' => false, 'message' => 'Phase not found'], 404);
        }

        // SYMMETRIC RULE: if ANY activity in this phase has a set, phase cannot link
        $activityIds = DB::table('phase_activities')->where('phase_id', $phase->id)->pluck('id')->all();

        if (!empty($activityIds)) {
            $anyActivityHasSet = DB::table('master_sets')
                ->whereNull('deleted_at')
                ->whereIn('phase_activity_id', $activityIds)
                ->exists();

            if ($anyActivityHasSet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mindestens eine Aktivität in dieser Phase hat bereits ein Master-Set. Entfernen Sie zuerst die Aktivitäts-Verknüpfungen.'
                ], 422);
            }
        }

        DB::table('master_sets')
            ->where('id', $data['master_set_id'])
            ->update([
                'task_phase_id' => $phase->id,
                'phase_activity_id' => null,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Set erfolgreich mit Phase verknüpft.']);
    }

    // -----------------------------------------
    // UNLINK set (clear both foreign keys)
    // -----------------------------------------
    public function unlinkMasterSet(Request $request): JsonResponse
    {
        $data = $request->validate([
            'master_set_id' => ['required', 'integer', 'exists:master_sets,id'],
        ]);

        DB::table('master_sets')
            ->where('id', $data['master_set_id'])
            ->update([
                'task_phase_id' => null,
                'phase_activity_id' => null,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Set erfolgreich getrennt.']);
    }

}
