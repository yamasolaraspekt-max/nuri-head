<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Http\Controllers\Controller;
use App\Services\Offer\WpAngebotsWorkflowService;

/**
 * Paket 4a — READ-ONLY WP-Angebotsworkflow-Cockpit je Gewerkzeile (`lead_product_lists`).
 *
 * Rendert eine geführte Statusseite aus dem on-the-fly aggregierten DTO (WpAngebotsWorkflowService).
 * KEIN Schreiben, KEIN Angebot erstellen, KEIN Preisanker, KEIN component_id.
 */
class WpAngebotsWorkflowController extends Controller
{
    public function show(int $leadProductList, WpAngebotsWorkflowService $service)
    {
        return view('admin.offer.workflow.cockpit', [
            'cockpit' => $service->fuerId($leadProductList),
            'leadProductList' => $leadProductList,
        ]);
    }
}
