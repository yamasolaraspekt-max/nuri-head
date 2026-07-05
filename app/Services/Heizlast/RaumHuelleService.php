<?php

namespace App\Services\Heizlast;

/**
 * Hüllflächen-Bilanz nach DIN EN 12831-1 §6: zieht Fenster/Türen von der
 * Außenwand ab (Netto-Wandfläche) und aggregiert die thermische Hülle des
 * Gebäudes (opak vs. transparent, je Bauteilgruppe).
 *
 * Bauteil: ['typ' => wand|dach|decke|boden|fenster|tuer, 'flaeche_m2' => float,
 *           'u_wert' => float, 'grenzflaeche' => aussen|unbeheizt|erdreich|beheizt]
 */
class RaumHuelleService
{
    /**
     * Bauteile mit effektiver Fläche: Außenwände um die Außen-Öffnungen reduziert.
     *
     * @param  array<int, array<string, mixed>>  $bauteile
     * @return array<int, array<string, mixed>>
     */
    public function effektiveBauteile(array $bauteile): array
    {
        $oeffnungen = 0.0;
        $wandAussen = 0.0;
        foreach ($bauteile as $b) {
            $typ = $b['typ'] ?? '';
            $gf = $b['grenzflaeche'] ?? 'aussen';
            $a = (float) ($b['flaeche_m2'] ?? 0);
            if ($gf === 'aussen' && in_array($typ, ['fenster', 'tuer'], true)) {
                $oeffnungen += $a;
            } elseif ($gf === 'aussen' && $typ === 'wand') {
                $wandAussen += $a;
            }
        }
        $faktor = $wandAussen > 0 ? max(0.0, ($wandAussen - $oeffnungen) / $wandAussen) : 1.0;

        return array_map(function (array $b) use ($faktor) {
            $istAussenwand = ($b['grenzflaeche'] ?? 'aussen') === 'aussen' && ($b['typ'] ?? '') === 'wand';
            $b['flaeche_eff'] = round(((float) ($b['flaeche_m2'] ?? 0)) * ($istAussenwand ? $faktor : 1.0), 2);

            return $b;
        }, $bauteile);
    }

    /**
     * Thermische Hülle des Gebäudes je Bauteilgruppe (Netto-Flächen + Verlust-Kennwert A·U).
     *
     * @param  array<int, array<string, mixed>>  $raeume
     * @return array<string, mixed>
     */
    public function huellbilanz(array $raeume): array
    {
        $gruppen = ['wand' => 0.0, 'fenster' => 0.0, 'tuer' => 0.0, 'dach' => 0.0, 'decke' => 0.0, 'boden' => 0.0];
        $au = $gruppen; // Σ A·U je Gruppe (W/K)

        foreach ($raeume as $raum) {
            foreach ($this->effektiveBauteile($raum['bauteile'] ?? []) as $b) {
                $typ = $b['typ'] ?? 'wand';
                if (! array_key_exists($typ, $gruppen)) {
                    continue;
                }
                $gruppen[$typ] += $b['flaeche_eff'];
                $au[$typ] += $b['flaeche_eff'] * (float) ($b['u_wert'] ?? 0);
            }
        }

        $flaecheGesamt = array_sum($gruppen) ?: 1.0;
        $auGesamt = array_sum($au);

        return [
            'flaeche_m2' => array_map(fn ($v) => round($v, 1), $gruppen),
            'verlust_w_k' => array_map(fn ($v) => round($v, 1), $au),
            'flaeche_gesamt_m2' => round(array_sum($gruppen), 1),
            'verlust_gesamt_w_k' => round($auGesamt, 1),
            'anteil_transparent_pct' => round(($gruppen['fenster'] + $gruppen['tuer']) / $flaecheGesamt * 100, 1),
        ];
    }
}
