<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IDS-Faehigkeitsabfragen (LI = Logininformationen, SV = Schnittstellenversion)
    |--------------------------------------------------------------------------
    | aktiv=false ist der Rueckweg ohne Deploy: die Verbindungspruefung verhaelt
    | sich dann exakt wie vor AUF-IDS-LI-SV.
    | max_bytes: Antworten oberhalb dieser Groesse werden nicht gelesen (Kante 11).
    */

    'capabilities' => [
        'aktiv' => env('IDS_CAPABILITIES_AKTIV', true),
        'timeout' => (int) env('IDS_CAPABILITIES_TIMEOUT', 10),
        'max_bytes' => 1048576,
    ],

];
