<?php

return [
    /*
    | Heizkörper-Modul (M4). Default OFF in prod: die HK-Schema-Tabellen existieren erst
    | ab M5 produktiv. Bei OFF liefert die heizkoerper.*-Routen-Gruppe 404 (Middleware
    | EnsureHeizkoerperEnabled) — es wird garantiert keine Query auf Testing-only-Tabellen
    | ausgeführt. M5 fährt die Migrationen produktiv UND schaltet dieses Flag frei.
    */
    'heizkoerper' => (bool) env('HEIZKOERPER_MODULE_ENABLED', false),

    /*
    | S-1a: hartes Deny für Aufmaß-Waisen (deal_measurements ohne ableitbaren Owner). Default OFF (weich):
    | Übergangsphase erlaubt+loggt Waisen-Writes. Nach waisenfreiem Zeitraum (Yama terminiert) auf true.
    */
    'deal_measurement_orphan_hard_deny' => (bool) env('DEAL_MEASUREMENT_ORPHAN_HARD_DENY', false),

    /*
    | S-1b-2: Übergangs-Hart-Flags je enge Ability. Default OFF (weich): der breitere write-Kreis
    | darf noch (geloggt/gezählt), bis der jeweilige waisenfreie Zeitraum (Yama) auf true schaltet.
    */
    'deal_measurement_assign_hard_deny' => (bool) env('DEAL_MEASUREMENT_ASSIGN_HARD_DENY', false),
    'deal_measurement_unlock_hard_deny' => (bool) env('DEAL_MEASUREMENT_UNLOCK_HARD_DENY', false),
    'deal_measurement_delete_hard_deny' => (bool) env('DEAL_MEASUREMENT_DELETE_HARD_DENY', false),
];
