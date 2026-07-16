<?php

namespace App\Http\Controllers\Customer\Deal;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Materialbedarf & Bestellungen — Arbeitsvorbereitung (Welle A2/A3, 2026-07-16).
 *
 * REINE LESE-ÜBERSICHT über alle Aufträge: Wo ist die Materialliste gepflegt, wo nicht,
 * was war die letzte Material-Aktion? Die Pflege selbst passiert auf der BESTEHENDEN
 * Detailseite deal.material.list (DealMaterialListController) — eine Wahrheit, kein Duplikat.
 * Datenquellen: deals (Filter wie deal.all.list), offer_details (Materialliste vorhanden?),
 * deal_measurements.material_summary/materials_saved_at (letzte Pflege).
 */
class MaterialbedarfController extends Controller
{
    public function index(Request $request)
    {
        $days = in_array((int) $request->get('days'), [30, 90, 180, 365], true) ? (int) $request->get('days') : 90;
        $seit = Carbon::today()->subDays($days);

        $deals = Deal::query()
            ->with([
                'customer:id,firma,name,lastname',
                'folder.detail:id,offer_folder_id',
                'latestMeasurement:id,deal_id,material_summary,materials_saved_at',
            ])
            ->whereNotIn('status', ['Junk', 'complete'])
            ->where('created_at', '>=', $seit)
            ->orderByDesc('created_at')
            ->limit(500)
            ->get();

        $rows = $deals->map(function (Deal $deal) {
            $cust = $deal->customer;
            $kunde = trim((string) ($cust->firma ?? '')) !== ''
                ? $cust->firma
                : trim(($cust->name ?? '') . ' ' . ($cust->lastname ?? ''));

            $detail = $deal->folder->detail ?? null;
            $measurement = $deal->latestMeasurement;
            $savedAt = $measurement?->materials_saved_at;
            $last = is_array($measurement?->material_summary ?? null)
                ? ($measurement->material_summary['last_material_update'] ?? null)
                : null;

            return [
                'deal'       => $deal,
                'kunde'      => $kunde !== '' ? $kunde : '— kein Kunde verknüpft —',
                'detail_id'  => $detail?->id,
                'saved_at'   => $savedAt,
                'last_status' => is_array($last) ? ($last['status'] ?? null) : null,
                'last_at'    => is_array($last) ? ($last['created_at'] ?? null) : null,
            ];
        });

        return view('admin.deal.materialbedarf', [
            'rows'  => $rows,
            'days'  => $days,
            'ohnePflege' => $rows->filter(fn ($r) => $r['detail_id'] && !$r['saved_at'])->count(),
            'ohneListe'  => $rows->filter(fn ($r) => !$r['detail_id'])->count(),
            'bestellen'  => $rows->filter(fn ($r) => $r['last_status'] === 'bestellen')->count(),
        ]);
    }
}
