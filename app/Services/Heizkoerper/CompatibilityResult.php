<?php

namespace App\Services\Heizkoerper;

/**
 * Ausgabe des CompatibilityService (M3 iv-b). Trägt die maschinenlesbare Datenlage-Stufe,
 * damit die M4-UI ehrlich labeln kann.
 *
 * datenqualitaet:
 *  - 'serien-praezise'  = Treffer in valve_insert_compatibility (HK-Serie/Baujahr → Einsatz/Kopf)
 *  - 'regel-kandidaten' = keine Serien-Kompatibilität hinterlegt → Regel-gefilterte Katalog-Kandidaten
 *                          (ohne Serien-Präzision; SKU/Preis kommen später aus supplier_article_map)
 */
class CompatibilityResult
{
    /**
     * @param  array<int, array<string, mixed>>  $positionen  je {kategorie, accessory_id?, hersteller?, herst_artikelnr?, name?, begruendung}
     * @param  string[]  $sperren   harte Sperren (z.B. Zweirohr-Armatur an Einrohr, §5.3)
     * @param  string[]  $hinweise  z.B. Altnorm-Austauschempfehlung (§5.6)
     */
    public function __construct(
        public string $datenqualitaet,
        public array $positionen = [],
        public int|string|null $voreinstellstufe = null,
        public array $sperren = [],
        public array $hinweise = [],
    ) {}

    /** @return string[] die Kategorien der Positionen (für einfache Assertions/UI). */
    public function kategorien(): array
    {
        return array_values(array_unique(array_map(fn ($p) => $p['kategorie'], $this->positionen)));
    }
}
