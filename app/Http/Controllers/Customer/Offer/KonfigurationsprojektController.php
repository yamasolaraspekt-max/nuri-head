<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Http\Controllers\Controller;
use App\Services\Offer\KonfigurationsprojektService;

/**
 * AP-3a — READ-ONLY Konfigurationsprojekt-Sicht je Objekt (`lead_alternative_adds`).
 *
 * Rendert eine objektbezogene Übersicht aller aktivierten Gewerke/Module aus dem
 * on-the-fly aggregierten DTO (KonfigurationsprojektService). KEIN Schreiben,
 * KEIN Angebot, KEIN Preisanker, KEINE neue Tabelle.
 */
class KonfigurationsprojektController extends Controller
{
    public function show(int $alternative, KonfigurationsprojektService $service)
    {
        return view('admin.konfiguration.objekt', [
            'konfig' => $service->fuerObjekt($alternative),
            'alternative' => $alternative,
        ]);
    }
}
