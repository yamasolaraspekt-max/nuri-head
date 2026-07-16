<?php

/*
|--------------------------------------------------------------------------
| Vertrieb — Nachfass-Regeln (Welle A2 · 2026-07-16)
|--------------------------------------------------------------------------
| EINE Wahrheit für die Nachfass-Logik: Änderungen nur hier, nie im Code.
| Ein Angebot ist "Nachfassen fällig", wenn es in einem aktiven Angebots-
| status steht und sich seit X Tagen nichts bewegt hat (updated_at).
*/

return [

    // Tage ohne Bewegung, ab denen ein Angebot nachzufassen ist.
    'nachfassen_tage' => 7,

    // Angebotsstatus, die überhaupt nachgefasst werden (OfferFolder::OFFER_STATUS_*).
    // Entwürfe sind noch nicht draußen; akzeptiert/abgelehnt/storniert/abgelaufen sind entschieden.
    'nachfassen_status' => ['sent', 'viewed', 'negotiation', 'revised', 'pending_approval'],

    // Eskalations-Schwelle (Tage ohne Bewegung → rote Markierung).
    'nachfassen_eskalation_tage' => 21,
];
