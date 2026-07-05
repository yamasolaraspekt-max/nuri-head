<?php

namespace App\Services\Heizlast;

/**
 * Raumweiser Heizlast-Rechenkern nach DIN EN 12831-1 i. V. m. DIN/TS 12831-1.
 *
 * Liefert je Raum und Gebäude ZWEI Werte:
 *  - Standardheizlast: Transmission + Lüftung mit Norm-Innentemperaturen, ohne
 *    Leistungszuschläge (Grundlage hydraulischer Abgleich Verfahren B, GEG §60c).
 *  - Auslegungsheizlast: zusätzlich Komfortzuschlag auf θint und ggf.
 *    Wiederaufheizleistung Φ_RH (Grundlage Wärmeerzeuger-/WP-Dimensionierung).
 *
 *   Φ_T = Σ fx · A · (U + ΔU_WB) · (θint − θe)      (Transmission inkl. Wärmebrücken)
 *   Φ_V = 0,34 · n · V · (θint − θe)                 (Lüftung)
 *   Φ_RH = Zuschlag nur bei intermittierendem Betrieb
 *
 * Eingabe (Array): siehe berechne(); arbeitet ohne DB und ist damit voll testbar.
 */
class HeizlastRechner
{
    public function __construct(private RaumHuelleService $huelle) {}

    /**
     * @param  array{norm_aussentemp_c?: float, waermebruecken?: string, komfortzuschlag_k?: float, intermittierend?: bool, raeume?: array<int, array<string, mixed>>}  $eingabe
     * @return array<string, mixed>
     */
    public function berechne(array $eingabe): array
    {
        $thetaE = (float) ($eingabe['norm_aussentemp_c'] ?? -12.0);
        $wbModus = $eingabe['waermebruecken'] ?? 'pauschal';
        $deltaUwb = HeizlastNormwerte::deltaUwb($wbModus);
        $komfort = min(HeizlastNormwerte::KOMFORTZUSCHLAG_MAX_K, max(0.0, (float) ($eingabe['komfortzuschlag_k'] ?? 0)));
        $intermittierend = (bool) ($eingabe['intermittierend'] ?? false);

        $raeumeOut = [];
        $sumStd = 0.0;
        $sumAusl = 0.0;
        $sumFlaeche = 0.0;
        foreach ($eingabe['raeume'] ?? [] as $raum) {
            $r = $this->raum($raum, $thetaE, $deltaUwb, $komfort, $intermittierend);
            $raeumeOut[] = $r;
            $sumStd += $r['standardheizlast_w'];
            $sumAusl += $r['auslegungsheizlast_w'];
            $sumFlaeche += $r['grundflaeche_m2'];
        }

        $spez = $sumFlaeche > 0 ? $sumAusl / $sumFlaeche : 0.0;

        return [
            'norm_aussentemp_c' => $thetaE,
            'waermebruecken' => ['modus' => $wbModus, 'delta_u_wb' => $deltaUwb],
            'komfortzuschlag_k' => $komfort,
            'raeume' => $raeumeOut,
            'gebaeude' => [
                'standardheizlast_kw' => round($sumStd / 1000, 2),
                'auslegungsheizlast_kw' => round($sumAusl / 1000, 2),
                'grundflaeche_m2' => round($sumFlaeche, 1),
                'spezifische_heizlast_w_m2' => round($spez, 1),
                'plausi' => HeizlastNormwerte::plausi($spez),
            ],
            'huellbilanz' => $this->huelle->huellbilanz($eingabe['raeume'] ?? []),
            'quellen' => ['DIN EN 12831-1:2017-09', 'DIN/TS 12831-1:2020-04'],
        ];
    }

    /**
     * @param  array<string, mixed>  $raum
     * @return array<string, mixed>
     */
    private function raum(array $raum, float $thetaE, float $deltaUwb, float $komfort, bool $intermittierend): array
    {
        $nutzung = $raum['nutzung'] ?? 'wohnen';
        $thetaInt = (float) ($raum['theta_int_c'] ?? HeizlastNormwerte::thetaInt($nutzung));
        $grundflaeche = (float) ($raum['grundflaeche_m2'] ?? 0);
        $hoehe = (float) ($raum['hoehe_m'] ?? 2.5);
        $volumen = $grundflaeche * $hoehe;
        $n = (float) ($raum['luftwechsel_1h'] ?? HeizlastNormwerte::luftwechsel($nutzung));

        // Spezifischer Transmissionsverlust H_T (W/K) = Σ fx · A_netto · (U + ΔU_WB)
        $hT = 0.0;
        $bauteileOut = [];
        foreach ($this->huelle->effektiveBauteile($raum['bauteile'] ?? []) as $b) {
            $fx = HeizlastNormwerte::fx($b['grenzflaeche'] ?? 'aussen');
            $u = (float) ($b['u_wert'] ?? 0) + $deltaUwb;
            $a = (float) $b['flaeche_eff'];
            $h = $fx * $a * $u;
            $hT += $h;
            $bauteileOut[] = [
                'typ' => $b['typ'] ?? null,
                'grenzflaeche' => $b['grenzflaeche'] ?? null,
                'flaeche_eff_m2' => $a,
                'u_wert' => round((float) ($b['u_wert'] ?? 0), 3),
                'fx' => $fx,
                'h_w_k' => round($h, 2),
            ];
        }
        $hV = HeizlastNormwerte::LUFT_RHO_CP * $n * $volumen; // Lüftungs-Wärmeverlust H_V (W/K)

        // Standardheizlast (Norm-θint, ohne Zuschläge)
        $dStd = max(0.0, $thetaInt - $thetaE);
        $standard = ($hT + $hV) * $dStd;

        // Auslegungsheizlast (+ Komfortzuschlag auf θint, + Wiederaufheizung)
        $dA = max(0.0, ($thetaInt + $komfort) - $thetaE);
        $phiT = $hT * $dA;
        $phiV = $hV * $dA;
        $phiRH = $intermittierend ? $this->wiederaufheizung($grundflaeche) : 0.0;
        $auslegung = $phiT + $phiV + $phiRH;

        $spez = $grundflaeche > 0 ? $auslegung / $grundflaeche : 0.0;

        return [
            'name' => $raum['name'] ?? '',
            'nutzung' => $nutzung,
            'theta_int_c' => $thetaInt,
            'grundflaeche_m2' => round($grundflaeche, 1),
            'h_t_w_k' => round($hT, 2),
            'h_v_w_k' => round($hV, 2),
            'bauteile' => $bauteileOut,
            'phi_t_w' => (int) round($phiT),
            'phi_v_w' => (int) round($phiV),
            'phi_rh_w' => (int) round($phiRH),
            'standardheizlast_w' => (int) round($standard),
            'auslegungsheizlast_w' => (int) round($auslegung),
            'spezifische_heizlast_w_m2' => round($spez, 1),
            'plausi' => HeizlastNormwerte::plausi($spez),
        ];
    }

    /** Wiederaufheiz-Zuschlag bei intermittierendem Betrieb (Richtwert W/m², to_verify). */
    private function wiederaufheizung(float $grundflaeche): float
    {
        return $grundflaeche * 11.0;
    }
}
