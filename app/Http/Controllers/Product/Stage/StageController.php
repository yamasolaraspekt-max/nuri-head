<?php

namespace App\Http\Controllers\Product\Stage;
use App\Http\Controllers\Controller;

use App\Models\Stage;
use App\Models\ArticleGroup;
use App\Models\PhaseSection;
use App\Models\TaskPhase;
use App\Models\PhaseActivities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class StageController extends Controller
{
    public function index()
    {
        $products = ArticleGroup::orderBy('article_group')->get();
        return view('admin.task.stage.stage', compact('products'));
    }

    // ---------------------------
    // Helpers: schema safe sort
    // ---------------------------
    private function hasSortOrder(): bool
    {
        return DB::getSchemaBuilder()->hasColumn((new Stage)->getTable(), 'sort_order');
    }

    private function ensureCompleteSection(int $productId): PhaseSection
    {
        return PhaseSection::firstOrCreate(
            ['product_id' => $productId, 'phase_section' => 'complete'],
            ['status' => 'Published', 'sort_order' => 0]
        );
    }

    // ---------------------------
    // Sections for product
    // ---------------------------
    public function getSections(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:article_groups,id']);
        $productId = (int) $request->product_id;

        $complete = $this->ensureCompleteSection($productId);

        $sections = PhaseSection::query()
            ->select('id', 'phase_section', 'status', 'sort_order')
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success'            => true,
            'sections'           => $sections,
            'default_section_id' => $complete->id,
        ]);
    }

    // ---------------------------
    // Versions for product
    // ---------------------------
    public function getVersions(Request $request)
    {
        $productId = (int) $request->input('product_id');

        $versions = Stage::query()
            ->where('product_id', $productId)
            ->whereNotNull('version')
            ->where('version', '!=', '')
            ->pluck('version')
            ->unique()
            ->values();

        return response()->json(['versions' => $versions]);
    }

    // ---------------------------
    // Fetch list
    // ---------------------------
    public function fetch(Request $request)
    {
        $q = Stage::query()
            ->with(['product', 'section'])
            ->whereNull('deleted_at');

        if ($request->filled('product_id')) {
            $productId = (int)$request->product_id;
            $q->where('product_id', $productId);

            // default section "complete" if missing
            if (!$request->filled('phase_section_id')) {
                $completeId = PhaseSection::where('product_id', $productId)
                    ->whereNull('deleted_at')
                    ->where('phase_section', 'complete')
                    ->value('id');

                if ($completeId) {
                    $request->merge(['phase_section_id' => (int)$completeId]);
                }
            }
        }

        if ($request->filled('version')) {
            $q->where('version', $request->version);
        }

        if ($request->filled('phase_section_id')) {
            $q->where('phase_section_id', (int)$request->phase_section_id);
        }

        if ($request->filled('search')) {
            $q->where('stage', 'like', '%' . $request->search . '%');
        }

        $hasSort = $this->hasSortOrder();
        $q->orderBy($hasSort ? 'sort_order' : 'id');

        $all = $q->get();

        // group by product||version
        $grouped = $all->groupBy(fn($s) => $s->product_id . '||' . ($s->version ?? ''));

        // paginate groups
        $page    = (int) $request->get('page', 1);
        $perPage = 10;

        $pagedGroups = $grouped->slice(($page - 1) * $perPage, $perPage);

        $pagination = new LengthAwarePaginator(
            $pagedGroups,
            $grouped->count(),
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return response()->json([
            'html' => view('admin.task.stage.partials.list', [
                'grouped' => $pagedGroups,
                'stages'  => $pagination,
            ])->render()
        ]);
    }

    // ---------------------------
    // CRUD
    // ---------------------------
    public function store(Request $request)
    {
        $request->merge([
            'version' => $request->filled('version') ? trim((string)$request->version) : null,
        ]);

        $request->validate([
            'stage'            => 'required|string|max:255',
            'product_id'       => 'nullable|exists:article_groups,id',
            'phase_section_id' => 'nullable|exists:phase_sections,id',
            'version'          => 'nullable|string|max:255',
            'status'           => 'required|string|max:50',
        ]);

        // default complete section
        if ($request->filled('product_id') && !$request->filled('phase_section_id')) {
            $complete = $this->ensureCompleteSection((int)$request->product_id);
            $request->merge(['phase_section_id' => $complete->id]);
        }

        // section must belong to product
        if ($request->filled('product_id') && $request->filled('phase_section_id')) {
            $ok = PhaseSection::where('id', (int)$request->phase_section_id)
                ->where('product_id', (int)$request->product_id)
                ->exists();
            if (!$ok) return response()->json(['message' => 'Sektion passt nicht zum Produkt.'], 422);
        }

        $sort = 0;
        if ($this->hasSortOrder()) {
            $max = Stage::where('product_id', $request->product_id)
                ->where('version', $request->version)
                ->where('phase_section_id', $request->phase_section_id)
                ->max('sort_order');
            $sort = is_null($max) ? 0 : ((int)$max + 1);
        }

        $stage = Stage::create([
            'stage'            => $request->stage,
            'product_id'       => $request->product_id,
            'phase_section_id' => $request->phase_section_id,
            'version'          => $request->version,
            'status'           => $request->status,
            'sort_order'       => $sort,
        ]);

        return response()->json(['success' => true, 'id' => $stage->id]);
    }

    public function edit($id)
    {
        return response()->json(Stage::with('section')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $stage = Stage::findOrFail($id);

        $request->merge([
            'version' => $request->filled('version') ? trim((string)$request->version) : null,
        ]);

        $request->validate([
            'stage'            => 'required|string|max:255',
            'product_id'       => 'nullable|exists:article_groups,id',
            'phase_section_id' => 'nullable|exists:phase_sections,id',
            'version'          => 'nullable|string|max:255',
            'status'           => 'required|string|max:50',
        ]);

        if ($request->filled('product_id') && !$request->filled('phase_section_id')) {
            $complete = $this->ensureCompleteSection((int)$request->product_id);
            $request->merge(['phase_section_id' => $complete->id]);
        }

        if ($request->filled('product_id') && $request->filled('phase_section_id')) {
            $ok = PhaseSection::where('id', (int)$request->phase_section_id)
                ->where('product_id', (int)$request->product_id)
                ->exists();
            if (!$ok) return response()->json(['message' => 'Sektion passt nicht zum Produkt.'], 422);
        }

        $stage->update($request->only(['stage','product_id','phase_section_id','version','status']));
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $stage = Stage::findOrFail($id);

        if (TaskPhase::where('stage_id', $stage->id)->exists() ||
            PhaseActivities::where('stage_id', $stage->id)->exists()) {
            return response()->json(['message' => 'Diese Phase ist in Verwendung. Löschen nicht erlaubt.'], 422);
        }

        $stage->delete();
        return response()->json(['success' => true]);
    }

    // ---------------------------
    // Reorder
    // ---------------------------
    public function reorder(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:stages,id',
        ]);

        if (!$this->hasSortOrder()) {
            return response()->json(['message' => 'sort_order Spalte fehlt. Migration ausführen.'], 422);
        }

        foreach ($request->ids as $i => $id) {
            Stage::where('id', $id)->update(['sort_order' => $i]);
        }

        return response()->json(['success' => true]);
    }

    // ---------------------------
    // Section stats (counts)
    // ---------------------------
    public function sectionStats(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:article_groups,id',
            'version'    => 'nullable|string',
        ]);

        $productId = (int)$request->product_id;
        $version   = trim((string)($request->version ?? ''));

        $complete = $this->ensureCompleteSection($productId);

        $sections = PhaseSection::query()
            ->select('id', 'phase_section', 'status', 'sort_order')
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $counts = Stage::query()
            ->select('phase_section_id', DB::raw('COUNT(*) as cnt'))
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->when($version !== '', fn($q) => $q->where('version', $version))
            ->groupBy('phase_section_id')
            ->pluck('cnt', 'phase_section_id');

        $out = $sections->map(fn($s) => [
            'id'           => (int)$s->id,
            'phase_section'=> $s->phase_section,
            'status'       => $s->status,
            'sort_order'   => $s->sort_order,
            'stage_count'  => (int)($counts[(int)$s->id] ?? 0),
        ])->values();

        return response()->json([
            'success'            => true,
            'default_section_id' => $complete->id,
            'sections'           => $out,
        ]);
    }

    // ---------------------------
    // Duplicate: section -> section (same product + version)
    // ---------------------------
    public function duplicateSection(Request $request)
    {
        $request->merge([
            'version' => $request->filled('version') ? trim((string)$request->version) : null,
        ]);

        $request->validate([
            'product_id'         => 'required|exists:article_groups,id',
            'version'            => 'nullable|string|max:255',
            'source_section_id'  => 'required|exists:phase_sections,id',
            'target_section_id'  => 'required|exists:phase_sections,id',
        ]);

        $productId = (int)$request->product_id;
        $version   = $request->version;
        $src       = (int)$request->source_section_id;
        $tgt       = (int)$request->target_section_id;

        if ($src === $tgt) return response()->json(['message' => 'Quelle und Ziel dürfen nicht identisch sein.'], 422);

        // both sections must belong to product
        $ok = PhaseSection::whereIn('id', [$src,$tgt])->where('product_id', $productId)->count() === 2;
        if (!$ok) return response()->json(['message' => 'Sektion passt nicht zum Produkt.'], 422);

        $hasSort = $this->hasSortOrder();

        return DB::transaction(function() use ($productId,$version,$src,$tgt,$hasSort){
            $sourceStages = Stage::query()
                ->where('product_id', $productId)
                ->whereNull('deleted_at')
                ->where('phase_section_id', $src)
                ->when($version !== null && $version !== '', fn($q) => $q->where('version', $version))
                ->orderBy($hasSort ? 'sort_order' : 'id')
                ->get();

            if ($sourceStages->isEmpty()) return response()->json(['success'=>true,'created'=>0]);

            $sort = 0;
            if ($hasSort) {
                $max = Stage::query()
                    ->where('product_id', $productId)
                    ->whereNull('deleted_at')
                    ->where('phase_section_id', $tgt)
                    ->when($version !== null && $version !== '', fn($q)=>$q->where('version',$version))
                    ->max('sort_order');
                $sort = is_null($max) ? 0 : (int)$max;
            }

            $created = 0;
            foreach ($sourceStages as $st) {
                $new = $st->replicate();
                $new->phase_section_id = $tgt;
                $new->version = $version;
                if ($hasSort) $new->sort_order = ++$sort;
                $new->save();
                $created++;
            }

            return response()->json(['success'=>true,'created'=>$created]);
        });
    }

    // ---------------------------
    // Duplicate selected stages -> multiple targets
    // ---------------------------
    public function bulkTransferMultiTargets(Request $request)
    {
        $ids     = $request->input('ids', []);
        $targets = $request->input('targets', []);

        if (!is_array($ids) || !count($ids)) return response()->json(['message'=>'Keine Schritte gewählt.'], 422);
        if (!is_array($targets) || !count($targets)) return response()->json(['message'=>'Keine Ziele gewählt.'], 422);

        $hasSort = $this->hasSortOrder();
        $stages = Stage::whereIn('id', $ids)->orderBy($hasSort ? 'sort_order' : 'id')->get();
        if ($stages->isEmpty()) return response()->json(['message'=>'Schritte nicht gefunden.'], 404);

        $copied = 0;

        DB::transaction(function() use ($stages,$targets,$hasSort,&$copied){
            foreach ($targets as $t) {
                $pid = (int)($t['product_id'] ?? 0);
                $sid = (int)($t['section_id'] ?? 0);
                $ver = trim((string)($t['version'] ?? ''));

                if (!$pid || !$sid || $ver === '') continue;

                // ensure section belongs to product
                $ok = PhaseSection::where('id',$sid)->where('product_id',$pid)->exists();
                if (!$ok) continue;

                $sort = 0;
                if ($hasSort) {
                    $max = Stage::where('product_id',$pid)->where('phase_section_id',$sid)->where('version',$ver)->max('sort_order');
                    $sort = is_null($max) ? 0 : (int)$max;
                }

                foreach ($stages as $s) {
                    $clone = $s->replicate();
                    $clone->product_id = $pid;
                    $clone->phase_section_id = $sid;
                    $clone->version = $ver;
                    if ($hasSort) $clone->sort_order = ++$sort;
                    $clone->save();
                    $copied++;
                }
            }
        });

        return response()->json(['copied' => $copied]);
    }

    // ---------------------------
    // Copy whole bucket (product+version+section) -> one target bucket
    // ---------------------------
    public function copyBucket(Request $request)
    {
        $v = Validator::make($request->all(), [
            'source_product_id' => ['required','integer','min:1'],
            'source_version'    => ['nullable','string','max:191'],
            'source_section_id' => ['required','integer','min:1'],

            'target_product_id' => ['required','integer','min:1'],
            'target_version'    => ['required','string','max:191'],
            'target_section_id' => ['required','integer','min:1'],
        ]);

        if ($v->fails()) {
            return response()->json(['message'=>'Validation failed.','errors'=>$v->errors()], 422);
        }

        $srcP = (int)$request->source_product_id;
        $srcV = trim((string)($request->source_version ?? ''));
        $srcS = (int)$request->source_section_id;

        $tgtP = (int)$request->target_product_id;
        $tgtV = trim((string)$request->target_version);
        $tgtS = (int)$request->target_section_id;

        // ensure target section belongs to target product
        $ok = PhaseSection::where('id',$tgtS)->where('product_id',$tgtP)->exists();
        if (!$ok) return response()->json(['message'=>'Ziel-Sektion passt nicht zum Ziel-Produkt.'], 422);

        $hasSort = $this->hasSortOrder();

        $srcQ = Stage::query()
            ->where('product_id',$srcP)
            ->where('phase_section_id',$srcS);

        if ($srcV === '') {
            $srcQ->where(function($q){ $q->whereNull('version')->orWhere('version',''); });
        } else {
            $srcQ->where('version',$srcV);
        }

        $sourceStages = $srcQ->orderBy($hasSort ? 'sort_order' : 'id')->get();
        if ($sourceStages->isEmpty()) return response()->json(['copied'=>0,'message'=>'Quelle enthält keine Schritte.']);

        $sort = 0;
        if ($hasSort) {
            $max = Stage::where('product_id',$tgtP)->where('phase_section_id',$tgtS)->where('version',$tgtV)->max('sort_order');
            $sort = is_null($max) ? 0 : (int)$max;
        }

        $copied = 0;

        DB::transaction(function() use ($sourceStages,$tgtP,$tgtS,$tgtV,$hasSort,&$sort,&$copied){
            foreach ($sourceStages as $st) {
                $new = $st->replicate();
                $new->product_id = $tgtP;
                $new->phase_section_id = $tgtS;
                $new->version = $tgtV;
                if ($hasSort) $new->sort_order = ++$sort;
                $new->save();
                $copied++;
            }
        });

        return response()->json(['copied'=>$copied,'message'=>'Bucket wurde kopiert.']);
    }
}
