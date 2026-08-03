<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Produkt-Identität (AUF-P1-S4)
    |--------------------------------------------------------------------------
    | Schalter für die Identitätsleiter (ProductIdentityService). Default false:
    | jeder der neun umgestellten Schreibpfade verhält sich exakt wie vor der
    | Änderung (Kante 15 — der Rückweg ohne Datenmigration, Spez 11-…md §6/§11).
    */

    'identitaet' => [
        'aktiv' => (bool) env('PRODUKT_IDENTITAET_AKTIV', false),
    ],

];
