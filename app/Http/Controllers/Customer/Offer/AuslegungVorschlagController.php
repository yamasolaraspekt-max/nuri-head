<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Http\Controllers\Controller;
use App\Services\Offer\AuslegungVorschlagService;

/**
 * P3-c-preview — READ-ONLY Vorschau der Auslegungs-Positionsvorschläge je WP-Gewerkzeile.
 *
 * Rendert NUR ein Panel aus dem on-the-fly erzeugten Vorschlags-DTO (AuslegungVorschlagService).
 * KEIN Schreiben, KEINE Angebotsübernahme, KEINE Persistenz, KEIN Preis.
 */
class AuslegungVorschlagController extends Controller
{
    /** Read-only Panel-Partial (Lazy-Einbettung / Vorschau). */
    public function panelPartial(int $leadProductList, AuslegungVorschlagService $service)
    {
        return view('admin.offer.partials.auslegung_vorschlag_panel', [
            'vorschlag' => $service->fuerId($leadProductList),
        ]);
    }
}
