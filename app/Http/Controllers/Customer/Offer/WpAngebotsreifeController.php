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
}
