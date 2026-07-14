<?php

namespace App\Services\Auslegung;

/**
 * WP Stufe 3 — Eingabe-Operanden der Auslegungskette (input-agnostisch: Quelle Request ODER
 * versioniertes Anforderungsprofil — E-WP1; die Profil-Verankerung ist Stufe 3b).
 *
 * Nullable = fehlender Operand → definierter Gate-Fehlerzustand (G1–G5), nie geraten.
 */
final class WpAuslegungsEingabe
{
    public function __construct(
        public readonly ?float $phiHlKw,      // Bedarf (Heizlast), G-Bedarf
        public readonly ?float $qHeizKwh,     // Jahres-Heizarbeit (E-WP2/G2)
        public readonly ?float $qWwKwh,       // Jahres-WW-Energie
        public readonly ?bool $wwMitWp,       // G3 (Fach-Entscheidung)
        public readonly ?float $vorlaufC,     // G4
        public readonly ?string $plz,         // Klima
        public readonly ?string $wpTyp,       // E-WP5/G1 (z. B. luft_wasser)
        public readonly ?string $heizsystem,  // E-WP5/G1 (z. B. heizkoerper/fussboden)
        public readonly bool $belastbar = false, // AP-1 (G5): steuert „verbindlich" (Stufe 3a: immer informativ)
        public readonly float $raumtempC = 20.0,
        public readonly ?float $lat = null,
        public readonly ?float $lon = null,
        public readonly int $limit = 6,
    ) {}
}
