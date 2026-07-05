<?php

namespace App\Services\Heizkoerper;

use App\Models\RadiatorSpec;

/**
 * Dünner Katalog-Adapter (M3 iv-a): product_radiator_specs -> Eingabe-Array für
 * RadiatorPerformanceService::qReal(). Hält den reinen Rechenkern unberührt & katalogtreu.
 *
 * Ableitung (Bauplan §6, Yama-Notiz): q_norm_w = q_norm_w_pro_m × Baulänge × Anzahl.
 * KEINE Rechenlogik/Physik hier — nur lesen (Spec-Felder), ableiten (Multiplikation), Array bauen.
 */
class RadiatorCatalogAdapter
{
    /**
     * Baut aus einem Katalog-Spec + Baulänge + Anzahl den qReal-Eingabeeintrag.
     * $baulaenge in Metern (q_norm_w_pro_m ist W je Meter). Override der Aufnahme (falls vorhanden)
     * setzt der Aufrufer vor; dieser Adapter nutzt die Spec-Werte.
     *
     * @return array{q_norm_w: float, exponent_n: float, norm_bedingung: string}
     */
    public function toQRealEntry(RadiatorSpec $spec, float $baulaenge, int $anzahl = 1): array
    {
        return [
            'q_norm_w' => (float) $spec->q_norm_w_pro_m * $baulaenge * max(1, $anzahl),
            'exponent_n' => (float) $spec->exponent_n,
            'norm_bedingung' => (string) ($spec->norm_bedingung ?: '75/65/20'),
        ];
    }
}
