<?php

return [

    /*
    |--------------------------------------------------------------------------
    | P1-a Preis-Integrität — Server-Pricing-Schutz
    |--------------------------------------------------------------------------
    |
    | Erzwingt beim Angebots-Speichern (OfferController::processOffer) den
    | Katalog-EK/VK für sections-Knoten mit stabiler `component_id`-Referenz
    | (App\Services\Offer\CatalogPriceGuard). Rückfall: auf `false` setzen —
    | dann verhält sich der Speicherpfad exakt wie vor P1-a.
    |
    */

    'enforce_catalog_pricing' => env('OFFER_ENFORCE_CATALOG_PRICING', true),

];
