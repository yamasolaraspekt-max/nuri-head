<?php

return [
    /*
    | Heizkörper-Modul (M4). Default OFF in prod: die HK-Schema-Tabellen existieren erst
    | ab M5 produktiv. Bei OFF liefert die heizkoerper.*-Routen-Gruppe 404 (Middleware
    | EnsureHeizkoerperEnabled) — es wird garantiert keine Query auf Testing-only-Tabellen
    | ausgeführt. M5 fährt die Migrationen produktiv UND schaltet dieses Flag frei.
    */
    'heizkoerper' => (bool) env('HEIZKOERPER_MODULE_ENABLED', false),
];
