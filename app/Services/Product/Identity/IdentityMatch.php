<?php

namespace App\Services\Product\Identity;

use App\Models\Product;

/**
 * Ergebnis eines Leiter-Laufs (Spezifikation 11-…md §4 + §6).
 */
final class IdentityMatch
{
    public const AUTOMATISCH = 'automatisch';  // Stufe 1-4, zugeordnet
    public const VORSCHLAG   = 'vorschlag';    // Stufe 5, NICHT zugeordnet
    public const KONFLIKT    = 'konflikt';     // Abbruch nach oben griff
    public const NEU         = 'neu';          // kein Treffer -> neuer Artikel

    public function __construct(
        public readonly string   $ergebnis,
        public readonly ?Product $product,
        public readonly ?int     $stufe,
        public readonly string   $begruendung,   // fuer Protokoll und Vorschlagsliste
    ) {}
}
