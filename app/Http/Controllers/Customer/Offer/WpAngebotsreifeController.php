<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Http\Controllers\Controller;
use App\Services\Offer\OfferReadinessService;
use Illuminate\Http\Request;

/**
 * Paket 1 — READ-ONLY Anzeige der Angebotsreife je Gewerkzeile (`lead_product_lists`).
 *
 * Rendert NUR ein Panel aus dem on-the-fly berechneten Reife-DTO (OfferReadinessService).
 * KEIN Schreiben, KEINE Angebotserstellung, KEIN Umbau der Angebotslogik.
 */
class WpAngebotsreifeController extends Controller
{
    public function show(int $leadProductList, OfferReadinessService $service)
    {
        $reife = $service->fuerId($leadProductList);

        return view('admin.offer.angebotsreife', ['reife' => $reife]);
    }

    /** Optionaler read-only JSON-Endpoint für das Frontend (gleiches DTO). */
    public function json(int $leadProductList, OfferReadinessService $service)
    {
        return response()->json($service->fuerId($leadProductList));
    }

    /**
     * Paket 1b-a — nur das Panel-Partial (für Lazy-AJAX-Einbettung ins Objektprofil).
     * Read-only, kein Wrapper/Layout.
     */
    public function panelPartial(int $leadProductList, OfferReadinessService $service)
    {
        return view('admin.offer.partials.angebotsreife_panel', ['reife' => $service->fuerId($leadProductList)]);
    }

    /**
     * Paket 1b-b — READ-ONLY Batch-Status für Kanban-Badges.
     * `ids` = kommagetrennte/Array-Liste, NUR numerisch, hart auf 100 gecappt, ungültig/leer → [].
     * JSON gibt ausschließlich {id, status, percent, angebotsfaehig} zurück — KEINE PII.
     */
    public function index(Request $request, OfferReadinessService $service)
    {
        $raw = $request->query('ids', '');
        $ids = collect(is_array($raw) ? $raw : explode(',', (string) $raw))
            ->map(fn ($v) => trim((string) $v))
            ->filter(fn ($v) => $v !== '' && ctype_digit($v)) // nur rein numerisch
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->take(100); // harte Obergrenze

        $out = $ids->map(function (int $id) use ($service) {
            $r = $service->fuerId($id);

            return [
                'id' => (int) $r['lead_product_list_id'],
                'status' => $r['status'],
                'percent' => (int) $r['percent'],
                'angebotsfaehig' => (bool) $r['angebotsfaehig'],
            ];
        })->values();

        return response()->json($out);
    }
}
