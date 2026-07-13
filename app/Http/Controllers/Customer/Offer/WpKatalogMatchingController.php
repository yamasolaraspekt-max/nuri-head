<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Http\Controllers\Controller;
use App\Services\Offer\WpKatalogMatchingService;

/**
 * P3-d0a — READ-ONLY WP-Katalog-Matching-Diagnose je WP-Gewerkzeile.
 *
 * Rendert NUR ein Vergleichs-Panel aus dem Diagnose-DTO (WpKatalogMatchingService).
 * KEIN Schreiben, KEIN Preisanker, KEIN component_id, KEINE Angebotsposition.
 */
class WpKatalogMatchingController extends Controller
{
    public function panelPartial(int $leadProductList, WpKatalogMatchingService $service)
    {
        return view('admin.offer.partials.wp_katalog_matching_panel', [
            'matching' => $service->fuerId($leadProductList),
        ]);
    }
}
