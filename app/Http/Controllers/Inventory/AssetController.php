<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Handover;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class AssetController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['assetsUpdate', 'handoversUpdate']);
        $this->middleware('permission:Product,delete')->only(['assetsDestroy', 'handoversDestroy']);
    }

    private const MAX_PER_PAGE = 100;
    private const MIN_PER_PAGE = 6;
    private const DEFAULT_PER_PAGE = 12;

    public function index()
    {
        $employees = DB::table('employees')
            ->select('id', 'name', 'lastname')
            ->orderBy('name')
            ->get();

        $branches = DB::table('branches')
            ->select('id', 'branch')
            ->orderBy('branch')
            ->get();

        $categories = DB::table('assets')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $assetStatuses = DB::table('assets')
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $purchaseTypes = DB::table('assets')
            ->whereNotNull('purchase_type')
            ->where('purchase_type', '!=', '')
            ->distinct()
            ->orderBy('purchase_type')
            ->pluck('purchase_type');

        $handoverStatuses = DB::table('handovers')
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        return view('admin.assets.center', compact(
            'employees',
            'branches',
            'categories',
            'assetStatuses',
            'purchaseTypes',
            'handoverStatuses'
        ));
    }

    /* =========================================================================
       ASSETS
       ========================================================================= */

    public function assetsFetch(Request $request)
    {
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = (int) $request->get('per_page', self::DEFAULT_PER_PAGE);
        $perPage = min(self::MAX_PER_PAGE, max(self::MIN_PER_PAGE, $perPage));

        $q      = trim((string) $request->get('q', ''));
        $sort   = (string) $request->get('sort', 'newest');

        $category     = trim((string) $request->get('category', ''));
        $status       = trim((string) $request->get('status', ''));
        $purchaseType = trim((string) $request->get('purchase_type', ''));

        // 1) FILTERS-ONLY QUERY (NO select() here!)
        $filters = DB::table('assets as a')
            ->leftJoin('employees as e', 'e.id', '=', 'a.handover_id')
            ->leftJoin('branches as b', 'b.id', '=', 'a.branch_id')
            ->leftJoin('assets as p', 'p.id', '=', 'a.parent_id');

        if ($q !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';
            $filters->where(function ($x) use ($like) {
                $x->where('a.item', 'like', $like)
                  ->orWhere('a.model', 'like', $like)
                  ->orWhere('a.category', 'like', $like)
                  ->orWhere('a.serial_no', 'like', $like)
                  ->orWhere('a.article_no', 'like', $like)
                  ->orWhere('a.location', 'like', $like)
                  ->orWhere('a.description', 'like', $like)
                  ->orWhereRaw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) like ?", [$like])
                  ->orWhereRaw("COALESCE(b.branch,'') like ?", [$like])
                  ->orWhereRaw("COALESCE(p.item,'') like ?", [$like]);
            });
        }

        if ($category !== '')     $filters->where('a.category', $category);
        if ($status !== '')       $filters->where('a.status', $status);
        if ($purchaseType !== '') $filters->where('a.purchase_type', $purchaseType);

        // 2) LIST QUERY
        $listQ = (clone $filters)->selectRaw("
            a.*,
            TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as handover_name,
            COALESCE(b.branch,'') as branch_name,
            COALESCE(p.item,'') as parent_item,
            (COALESCE(a.purchase_price,0) * COALESCE(a.quantity,0)) as total_value
        ");

        $sortMap = [
            'newest'     => ['a.created_at', 'desc'],
            'oldest'     => ['a.created_at', 'asc'],
            'item_asc'   => ['a.item', 'asc'],
            'item_desc'  => ['a.item', 'desc'],
            'category'   => ['a.category', 'asc'],
            'status'     => ['a.status', 'asc'],
            'qty_desc'   => ['a.quantity', 'desc'],
            'value_desc' => ['total_value', 'desc'],
        ];
        [$col, $dir] = $sortMap[$sort] ?? $sortMap['newest'];

        if ($col === 'total_value') $listQ->orderByRaw("total_value {$dir}");
        else $listQ->orderBy($col, $dir);

        // 3) TOTAL (COUNT only)
        $total = (clone $filters)
            ->selectRaw('COUNT(DISTINCT a.id) as cnt')
            ->value('cnt');

        // 4) ROWS
        $rows = (clone $listQ)
            ->forPage($page, $perPage)
            ->get();

        // 5) STATS (aggregates only)
        $in30 = Carbon::now()->addDays(30)->toDateString();

        $stats = (clone $filters)->selectRaw("
            COUNT(DISTINCT a.id) as total_assets,
            COALESCE(SUM(a.quantity),0) as total_qty,
            COALESCE(SUM(COALESCE(a.purchase_price,0) * COALESCE(a.quantity,0)),0) as total_value,
            COALESCE(SUM(CASE
                WHEN LOWER(COALESCE(a.purchase_type,'')) LIKE '%lease%'
                  OR LOWER(COALESCE(a.purchase_type,'')) LIKE '%leasing%'
                THEN 1 ELSE 0 END),0) as leasing_count,
            COALESCE(SUM(CASE
                WHEN a.expire_date IS NOT NULL AND a.expire_date <= ?
                THEN 1 ELSE 0 END),0) as expiring_30
        ", [$in30])->first();

        return response()->json([
            'items' => $rows,
            'stats' => [
                'total_assets'  => (int) ($stats->total_assets ?? 0),
                'total_qty'     => (int) ($stats->total_qty ?? 0),
                'total_value'   => (float) ($stats->total_value ?? 0),
                'leasing_count' => (int) ($stats->leasing_count ?? 0),
                'expiring_30'   => (int) ($stats->expiring_30 ?? 0),
            ],
            'pagination' => [
                'page'     => $page,
                'per_page' => $perPage,
                'total'    => (int) $total,
                'has_more' => ($page * $perPage) < $total,
            ],
        ]);
    }

    public function assetsStore(Request $request)
    {
        $data = $this->validateAsset($request);
        $asset = Asset::create($data);
        return response()->json(['ok' => true, 'id' => $asset->id]);
    }

    public function assetsUpdate(Request $request, Asset $asset)
    {
        $data = $this->validateAsset($request, $asset->id);
        $asset->update($data);
        return response()->json(['ok' => true]);
    }

    public function assetsDestroy(int $asset)
    {
        $q = Asset::query();

        // If SoftDeletes is enabled, allow deleting trashed rows too
        if (method_exists(Asset::class, 'bootSoftDeletes') || in_array('Illuminate\\Database\\Eloquent\\SoftDeletes', class_uses_recursive(Asset::class))) {
            $q = $q->withTrashed();
        }

        $row = $q->find($asset);

        if (!$row) {
            return response()->json([
                'ok' => false,
                'message' => 'Asset nicht gefunden.',
                'id' => $asset,
            ], 404);
        }

        // If you want "hard delete" when already trashed:
        if (method_exists($row, 'forceDelete') && !is_null($row->deleted_at)) {
            $row->forceDelete();
        } else {
            $row->delete(); // soft delete (or hard delete if no SoftDeletes)
        }

        return response()->json(['ok' => true]);
    }

    private function validateAsset(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'serial_no' => [
                'nullable','string','max:255',
                Rule::unique('assets','serial_no')->ignore($ignoreId)->whereNotNull('serial_no'),
            ],
            'article_no' => ['nullable','string','max:255'],
            'item' => ['required','string','max:255'],
            'model' => ['required','string','max:255'],
            'category' => ['required','string','max:255'],

            'parent_id' => ['nullable','integer', Rule::exists('assets','id')],

            'purchase_price' => ['nullable','numeric','min:0'],
            'purchase_type'  => ['required','string','max:255'],

            'leasing_from'     => ['nullable','string','max:255'],
            'leasing_date'     => ['nullable','date'],
            'leasing_end_date' => ['nullable','date','after_or_equal:leasing_date'],
            'leasing_price'    => ['nullable','numeric','min:0'],

            'purchase_date' => ['nullable','date'],
            'expire_date'   => ['nullable','date'],

            'location' => ['nullable','string','max:255'],
            'description' => ['nullable','string'],
            'image' => ['nullable','string','max:255'],

            'quantity' => ['required','integer','min:0'],
            'status' => ['required','string','max:255'],
            'pdf' => ['nullable','string','max:255'],

            'handover_id' => ['nullable','integer', Rule::exists('employees','id')],
            'branch_id'   => ['nullable','integer', Rule::exists('branches','id')],
            'used_for'    => ['nullable','integer', Rule::exists('article_groups','id')],
        ], [], [
            'item' => 'Artikel / Item',
            'model' => 'Modell',
            'category' => 'Kategorie',
            'purchase_type' => 'Kaufart',
            'purchase_price' => 'Kaufpreis',
        ]);
    }

    /* =========================================================================
       HANDOVERS
       ========================================================================= */

    public function handoversFetch(Request $request)
    {
        $page    = max(1, (int) $request->get('page', 1));
        $perPage = min(self::MAX_PER_PAGE, max(self::MIN_PER_PAGE, (int) $request->get('per_page', self::DEFAULT_PER_PAGE)));

        $q    = trim((string) $request->get('q', ''));
        $sort = (string) $request->get('sort', 'newest');

        $status     = trim((string) $request->get('status', ''));
        $employeeId = (int) $request->get('employee_id', 0);

        // 1) FILTERS-ONLY (no select!)
        $filters = DB::table('handovers as h')
            ->leftJoin('assets as a', 'a.id', '=', 'h.item_id')
            ->leftJoin('employees as e', 'e.id', '=', 'h.handover_id');

        if ($q !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';
            $filters->where(function ($x) use ($like) {
                $x->where('a.item', 'like', $like)
                  ->orWhere('a.model', 'like', $like)
                  ->orWhere('a.category', 'like', $like)
                  ->orWhere('h.remark', 'like', $like)
                  ->orWhere('h.status', 'like', $like)
                  ->orWhereRaw("TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) like ?", [$like]);
            });
        }

        if ($status !== '') $filters->where('h.status', $status);
        if ($employeeId > 0) $filters->where('h.handover_id', $employeeId);

        // 2) LIST
        $listQ = (clone $filters)->selectRaw("
            h.*,
            COALESCE(a.item,'') as asset_item,
            COALESCE(a.model,'') as asset_model,
            COALESCE(a.category,'') as asset_category,
            TRIM(CONCAT(COALESCE(e.name,''),' ',COALESCE(e.lastname,''))) as employee_name
        ");

        $sortMap = [
            'newest'   => ['h.created_at', 'desc'],
            'oldest'   => ['h.created_at', 'asc'],
            'qty_desc' => ['h.quantity', 'desc'],
            'status'   => ['h.status', 'asc'],
            'employee' => ['employee_name', 'asc'],
        ];
        [$col, $dir] = $sortMap[$sort] ?? $sortMap['newest'];

        if ($col === 'employee_name') {
            $listQ->orderByRaw("employee_name {$dir}");
        } else {
            $listQ->orderBy($col, $dir);
        }

        // 3) TOTAL (count only)
        $total = (clone $filters)
            ->selectRaw('COUNT(h.id) as cnt')
            ->value('cnt');

        // 4) ROWS
        $rows = (clone $listQ)->forPage($page, $perPage)->get();

        // 5) STATS (aggregates only)  ✅ FIXED
        $stats = (clone $filters)->selectRaw("
            COUNT(h.id) as total_handovers,
            COALESCE(SUM(COALESCE(h.quantity,0)),0) as total_qty
        ")->first();

        return response()->json([
            'items' => $rows,
            'stats' => [
                'total_handovers' => (int) ($stats->total_handovers ?? 0),
                'total_qty'       => (int) ($stats->total_qty ?? 0),
            ],
            'pagination' => [
                'page'     => $page,
                'per_page' => $perPage,
                'total'    => (int) $total,
                'has_more' => ($page * $perPage) < $total,
            ],
        ]);
    }

 
    public function handoversStore(Request $request)
    {
        $data = $this->validateHandover($request);

        return DB::transaction(function() use ($data){
            // lock asset row to prevent race conditions
            DB::table('assets')->where('id', $data['item_id'])->lockForUpdate()->first();

            $remaining = $this->remainingForAsset((int)$data['item_id'], null);
            $qty = (int)($data['quantity'] ?? 0);

            if ($qty <= 0) {
                abort(response()->json(['message' => 'Menge muss größer als 0 sein.'], 422));
            }
            if ($qty > $remaining) {
                abort(response()->json([
                    'message' => 'Nicht genug Bestand verfügbar.',
                    'errors'  => ['quantity' => ["Maximal verfügbar: {$remaining}"]],
                ], 422));
            }

            $h = Handover::create($data);
            return response()->json(['ok' => true, 'id' => $h->id]);
        });
    }

   public function handoversUpdate(Request $request, Handover $handover)
    {
        $data = $this->validateHandover($request);

        return DB::transaction(function() use ($data, $handover){
            DB::table('assets')->where('id', $data['item_id'])->lockForUpdate()->first();

            $remaining = $this->remainingForAsset((int)$data['item_id'], (int)$handover->id);
            $qty = (int)($data['quantity'] ?? 0);

            if ($qty <= 0) {
                abort(response()->json(['message' => 'Menge muss größer als 0 sein.'], 422));
            }
            if ($qty > $remaining) {
                abort(response()->json([
                    'message' => 'Nicht genug Bestand verfügbar.',
                    'errors'  => ['quantity' => ["Maximal verfügbar: {$remaining}"]],
                ], 422));
            }

            $handover->update($data);
            return response()->json(['ok' => true]);
        });
    }


    public function handoversDestroy(Handover $handover)
    {
        $assetId = $handover->item_id;

        DB::transaction(function () use ($handover, $assetId) {
            $handover->delete();

            // after delete: set asset holder to the latest remaining handover (or null)
            $latestEmployeeId = DB::table('handovers')
                ->where('item_id', $assetId)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->value('handover_id');

            DB::table('assets')->where('id', $assetId)->update([
                'handover_id' => $latestEmployeeId ?: null,
                'updated_at'  => now(),
            ]);
        });

        return response()->json(['ok' => true]);
    }


    private function validateHandover(Request $request): array
    {
        return $request->validate([
            'item_id'     => ['required','integer', Rule::exists('assets','id')],
            'handover_id' => ['required','integer', Rule::exists('employees','id')],
            'quantity'    => ['required','integer','min:1'],
            'remark'      => ['nullable','string','max:255'],
            'status'      => ['nullable','string','max:255'],
        ], [], [
            'item_id' => 'Asset',
            'handover_id' => 'Mitarbeiter',
            'quantity' => 'Menge',
        ]);
    }


    private function activeHandoverStatusesExcluded(): array
    {
        return ['returned','zurück','zurückgegeben','cancelled','storniert'];
    }

    private function remainingForAsset(int $assetId, ?int $ignoreHandoverId = null): int
    {
        $excluded = $this->activeHandoverStatusesExcluded();

        $out = DB::table('handovers as h')
            ->where('h.item_id', $assetId)
            ->when($ignoreHandoverId, fn($q) => $q->where('h.id', '!=', $ignoreHandoverId))
            ->where(function($q) use ($excluded){
                $q->whereNull('h.status')
                ->orWhereRaw('LOWER(h.status) NOT IN ('.implode(',', array_fill(0, count($excluded), '?')).')', $excluded);
            })
            ->sum(DB::raw('COALESCE(h.quantity,0)'));

        $assetQty = (int) DB::table('assets')->where('id', $assetId)->value('quantity');

        return max(0, $assetQty - (int)$out);
    }


    public function assetsAvailable(Request $request)
    {
        $q    = trim((string)$request->get('q', ''));
        $page = max(1, (int)$request->get('page', 1));
        $per  = 20;

        $excluded = $this->activeHandoverStatusesExcluded();

        $base = DB::table('assets as a')
            ->leftJoinSub(
                DB::table('handovers as h')
                    ->selectRaw('h.item_id, COALESCE(SUM(COALESCE(h.quantity,0)),0) as out_qty')
                    ->where(function($w) use ($excluded){
                        $w->whereNull('h.status')
                        ->orWhereRaw('LOWER(h.status) NOT IN ('.implode(',', array_fill(0, count($excluded), '?')).')', $excluded);
                    })
                    ->groupBy('h.item_id'),
                'hx',
                'hx.item_id',
                '=',
                'a.id'
            );

        if ($q !== '') {
            $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $q) . '%';
            $base->where(function($w) use ($like){
                $w->where('a.item', 'like', $like)
                ->orWhere('a.model', 'like', $like)
                ->orWhere('a.category', 'like', $like)
                ->orWhere('a.serial_no', 'like', $like);
            });
        }

        $base->selectRaw("
            a.id, a.item, a.model, a.category, a.purchase_date, a.quantity,
            (COALESCE(a.quantity,0) - COALESCE(hx.out_qty,0)) as remaining
        ")->whereRaw("(COALESCE(a.quantity,0) - COALESCE(hx.out_qty,0)) > 0");

        $total = (clone $base)->count();

        $rows = (clone $base)
            ->orderBy('a.item')
            ->forPage($page, $per)
            ->get();

        $results = $rows->map(function($r){
            $pd = $r->purchase_date ? (string)$r->purchase_date : '—';
            return [
                'id' => (int)$r->id,
                'text' => trim(($r->item ?: '—').' • '.($r->model ?: '—'))." — Kauf: {$pd} — Rest: ".(int)$r->remaining,
                'item' => $r->item,
                'model' => $r->model,
                'category' => $r->category,
                'purchase_date' => $pd,
                'remaining' => (int)$r->remaining,
            ];
        })->values();

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => ($page * $per) < $total],
        ]);
    }

}
