<?php
 namespace App\Http\Controllers\Inventory;
 use App\Http\Controllers\Controller;

use App\Models\Assets;
use App\Models\AssetSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetSetController extends Controller
{
    public function index(int $masterId)
    {
        $rows = AssetSet::with('asset')
            ->where('master_id', $masterId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($r) {
                $a = $r->asset;
                return [
                    'id'            => $r->id,
                    'asset_id'      => $r->asset_id,
                    'name'          => $r->name,
                    'count'         => $r->count,
                    'total_price'   => (float) $r->total_price,
                    'item'          => $a?->item,
                    'model'         => $a?->model,
                    'category'      => $a?->category,
                    'serial_no'     => $a?->serial_no,
                    'article_no'    => $a?->article_no,
                    'purchase_price'=> (float) ($a?->purchase_price ?? 0),
                ];
            });

        return response()->json([
            'rows'   => $rows,
            'totals' => [
                'count'       => (int) $rows->sum('count'),
                'total_price' => (float) $rows->sum('total_price'),
            ],
        ]);
    }

    public function search(Request $request, int $masterId)
    {
        $term = trim($request->get('term', ''));

        // limit to the same article_group as master
        $articleGroupId = (int) DB::table('product_master_sets')
            ->where('id', $masterId)
            ->value('article_group');

        $linked = AssetSet::where('master_id', $masterId)->pluck('asset_id');

        $q = DB::table('assets')
            ->when($articleGroupId, fn($qq) => $qq->where('used_for', $articleGroupId))
            ->when($term !== '', function($qq) use ($term) {
                $qq->where(function($w) use ($term){
                    $w->where('item','like',"%{$term}%")
                      ->orWhere('model','like',"%{$term}%")
                      ->orWhere('serial_no','like',"%{$term}%")
                      ->orWhere('article_no','like',"%{$term}%");
                });
            })
            ->whereNotIn('id', $linked)
            ->orderBy('item')
            ->limit(20)
            ->get(['id','item','model','serial_no','purchase_price']);

        return response()->json($q->map(function($a){
            $label = trim(($a->item ?? '').' '.($a->model ?? '').' #'.($a->serial_no ?? ''));
            return [
                'id'    => $a->id,
                'label' => $label,
                'price' => (float) ($a->purchase_price ?? 0),
            ];
        }));
    }

    public function store(Request $request, int $masterId)
    {
        $data = $request->validate([
            'asset_id' => ['required','integer','exists:assets,id'],
            'count'    => ['required','integer','min:1','max:100000'],
            'name'     => ['nullable','string','max:255'],
        ]);

        $asset = Assets::findOrFail($data['asset_id']);
        $unit  = (float) ($asset->purchase_price ?? 0);

        $row = AssetSet::firstOrNew([
            'asset_id' => $asset->id,
            'master_id'=> $masterId,
        ]);

        $row->count = $row->exists ? $row->count + (int)$data['count'] : (int)$data['count'];
        $row->name  = $data['name'] ?? $row->name;
        $row->total_price = round($row->count * $unit, 2);
        $row->save();

        $this->syncMasterAssetPrice($masterId);

        return response()->json(['ok' => true, 'id' => $row->id]);
    }

    public function update(Request $request, int $masterId, int $rowId)
    {
        $data = $request->validate([
            'count' => ['required','integer','min:1','max:100000'],
            'name'  => ['nullable','string','max:255'],
        ]);

        $row = AssetSet::findOrFail($rowId);
        abort_unless($row->master_id === $masterId, 404);

        $unit = (float) ($row->asset?->purchase_price ?? 0);

        $row->count       = (int) $data['count'];
        $row->name        = $data['name'] ?? $row->name;
        $row->total_price = round($row->count * $unit, 2);
        $row->save();

        $this->syncMasterAssetPrice($masterId);

        return response()->json(['ok' => true]);
    }

    public function destroy(int $masterId, int $rowId)
    {
        $row = AssetSet::findOrFail($rowId);
        abort_unless($row->master_id === $masterId, 404);

        $row->delete();

        $this->syncMasterAssetPrice($masterId);

        return response()->json(['ok' => true]);
    }

    private function syncMasterAssetPrice(int $masterId): void
    {
        $sum = (float) AssetSet::where('master_id', $masterId)->sum('total_price');
        DB::table('product_master_sets')->where('id', $masterId)->update(['asset_price' => round($sum,2)]);
    }
}
