<?php

namespace App\Services\Heizlast;

/**
 * Methode H – Heizlastmethode.
 *  - berechne(): überschlägiges Wohnflächen-/Baualtersverfahren mit Korrekturfaktoren (Default).
 *  - detailliert(): erweiterte Schätzung in Anlehnung an DIN EN 12831-1 (Hüllflächen aus
 *    Wohnfläche geschätzt, U-Werte aus qualitativen Angaben). Ersetzt KEINE raumweise Planung.
 * Neigt zur Überdimensionierung (Norm-Auslegungstag, alle Flächen beheizt).
 */
class HeizlastService
{
    /**
     * @return array<string, mixed>
     */
    public function berechne(HeizlastEingabe $e): array
    {
        $qBase = HeizlastKonstanten::qBase($e->baujahr);
        $faktoren = HeizlastKonstanten::korrekturFaktoren($e);
        $produkt = array_product($faktoren);

        $qSpez = max(HeizlastKonstanten::Q_SPEZ_MIN, min(HeizlastKonstanten::Q_SPEZ_MAX, $qBase * $produkt));
        $phiHl = $e->beheizte_wohnflaeche_m2 * $qSpez / 1000; // kW
        $bVh = $e->b_vh ?: HeizlastKonstanten::B_VH_DEFAULT;

        return [
            'methode' => 'heizlast',
            'verfahren' => 'überschlägig (Wohnflächen-/Baualtersverfahren)',
            'q_base' => $qBase,
            'korrekturfaktoren' => $faktoren,
            'q_spez' => round($qSpez, 1),
            'phi_hl_kw' => round($phiHl, 1),
            'q_heiz_kwh' => round($phiHl * $bVh), // abgeleiteter Jahresbedarf (Φ_HL × b_Vh)
            'b_vh' => $bVh,
            'annahmen' => [
                'q_base_w_m2' => $qBase,
                'q_spez_w_m2' => round($qSpez, 1),
                'b_vh' => $bVh,
            ],
        ];
    }

    /**
     * Erweiterte Schätzung (DIN EN 12831-nah). Alle Werte to_verify.
     *
     * @return array<string, mixed>
     */
    public function detailliert(HeizlastEingabe $e): array
    {
        $uWand = match ($e->fassadendaemmung) {
            'wdvs' => 0.25, 'teilweise' => 0.6, default => 1.4,
        };
        $uFenster = match ($e->fensterverglasung) {
            '3fach' => 0.8, '2fach_ws' => 1.3, '2fach_unbeschichtet' => 2.8, 'einfach' => 4.7, default => 1.3,
        };
        $uDach = match ($e->dachdaemmung) {
            'gut' => 0.20, 'teilweise' => 0.4, default => 1.0,
        };
        $uBoden = $e->kellerdecke_gedaemmt ? 0.35 : 0.8;

        $grund = $e->beheizte_wohnflaeche_m2 / max(1, $e->geschosse);
        $umfang = 4 * sqrt(max(1.0, $grund));
        $hoehe = $e->geschosse * 2.6;
        $aFenster = 0.18 * $e->beheizte_wohnflaeche_m2;
        $aWand = max(0.0, $umfang * $hoehe - $aFenster);
        $aDach = $grund * 1.2;
        $aBoden = $grund;

        $hT = $uWand * $aWand + $uFenster * $aFenster + $uDach * $aDach + $uBoden * $aBoden * 0.5;
        $n = $e->lueftung === 'kwl_wrg' ? 0.3 : 0.5;
        $hV = $n * ($e->beheizte_wohnflaeche_m2 * 2.6) * 0.34;

        $theE = HeizlastKonstanten::normAussentemp($e->standort_plz);
        $deltaT = $e->raumtemp_soll_c - $theE;
        $phiHl = ($hT + $hV) * $deltaT / 1000;

        return [
            'methode' => 'heizlast_detail',
            'verfahren' => 'erweiterte Schätzung (DIN EN 12831-nah, to_verify)',
            'u_werte' => ['wand' => $uWand, 'fenster' => $uFenster, 'dach' => $uDach, 'boden' => $uBoden],
            'flaechen_m2' => ['wand' => round($aWand), 'fenster' => round($aFenster), 'dach' => round($aDach), 'boden' => round($aBoden)],
            'norm_aussentemp_c' => $theE,
            'delta_t' => $deltaT,
            'phi_hl_kw' => round($phiHl, 1),
        ];
    }
}
