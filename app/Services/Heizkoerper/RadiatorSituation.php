<?php

namespace App\Services\Heizkoerper;

/**
 * Eingabe für CompatibilityService (M3 iv-b): die Ist-Situation eines Heizkörpers/Anschlusses,
 * gegen die die D3-Regeln (Bauplan §5) kompatibles Zubehör bestimmen.
 */
class RadiatorSituation
{
    public function __construct(
        public bool $istVentilHeizkoerper = false,          // integriertes Ventilunterteil? (§5.1 vs §5.2)
        public string $anschlussFuehrung = 'zweirohr',      // 'zweirohr' | 'einrohr' (§5.3)
        public ?string $kopfNormBestand = null,             // M30x1_5 | RA | RAV | RAVL | sonstige (§5.6)
        public ?string $hkHersteller = null,                // für valve_insert_compatibility (serien-präzise)
        public ?string $hkSerie = null,
        public ?int $baujahr = null,
        public ?float $heizlastW = null,                    // für Voreinstellstufe (§5.5)
        public ?float $spreizungK = null,
        public float $dpVentilMbar = 100,
    ) {}
}
