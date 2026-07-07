<?php

namespace App\Services\Heizlast;

use App\Services\Energie\Contracts\WpKennlinie;

/**
 * Temperatur-Bin-Simulation der Wärmepumpe (Auslegungspunkt: Norm-Außentemperatur).
 *
 * Über die Jahres-Temperaturhäufigkeit (KlimaBinService) und die Geräte-Kennlinie
 * (WpKennlinieService) werden ermittelt:
 *   - Bivalenzpunkt (Schnitt Geräteleistung × Gebäudeheizlast), Zielkorridor −3…−7 °C,
 *   - Stromaufteilung Verdichter / Heizstab (E-Stab) / Pumpen (kWh und %),
 *   - JAZ (System inkl. E-Stab/Pumpen) und JAZ nur Verdichter,
 *   - Verteilung von Wärme und Strom auf die Jahreszeiten.
 *
 * Die Gebäude-Heizlast je Bin ist die DESIGN-Kennlinie in kW
 * (Φ(ϑ) = Φ_HL·(ϑ_i−ϑ)/(ϑ_i−ϑ_e), gekappt an der Heizgrenze) → korrekte
 * Bivalenz/E-Stab-Absolutwerte; die Jahres-Heizarbeit wird anschließend auf den
 * maßgeblichen Heizwärmebedarf normiert, sodass JAZ und Saison-kWh konsistent sind.
 *
 * Grundlagen: VDI 4645 (Auslegung/Betriebsweisen), VDI 4650 (JAZ), DIN EN 14825 (COP),
 * DWD-TRY (Temperaturhäufigkeit).
 */
class BivalenzService
{
    public function __construct(
        private KlimaBinService $klima,
        private WpKennlinieService $kennlinie,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function berechne(
        WpKennlinie $wp,
        float $phiHlKw,
        float $qHeizKwh,
        float $qWwKwh,
        bool $wwMitWp,
        float $vorlaufC,
        ?string $plz,
        float $raumtempC = 20.0,
        ?float $lat = null,
        ?float $lon = null,
    ): array {
        $profil = $this->klima->profil($plz);
        $thetaE = $profil['theta_e'];
        $thetaHg = $profil['heizgrenze_c'];
        $bins = $profil['bins'];

        $wwVorlauf = HeizlastKonstanten::WW_VORLAUF_C;
        $phiWwBase = $wwMitWp && $qWwKwh > 0 ? $qWwKwh / 8760.0 : 0.0; // kW Grundlast

        $nenner = max(1.0, $raumtempC - $thetaE);
        $lastFaktor = fn (float $t): float => $t < $thetaHg ? max(0.0, ($raumtempC - $t) / $nenner) : 0.0;

        // Witterungsgeführte Vorlauftemperatur: Auslegungs-Vorlauf bei Norm-Außentemperatur,
        // linear abgesenkt bis zur Heizgrenze (mildere Tage → niedrigerer Vorlauf → höhere COP).
        $vorlaufMin = min($vorlaufC, HeizlastKonstanten::VORLAUF_MIN_C);
        $vorlaufBei = function (float $t) use ($vorlaufC, $vorlaufMin, $thetaE, $thetaHg): float {
            if ($t <= $thetaE) {
                return $vorlaufC;
            }
            if ($t >= $thetaHg) {
                return $vorlaufMin;
            }

            return $vorlaufMin + ($vorlaufC - $vorlaufMin) * ($thetaHg - $t) / ($thetaHg - $thetaE);
        };

        // 1) Bivalenzpunkt aus den kW-Design-Kurven (Geräteleistung vs. Heizlast).
        $bivalenz = $this->bivalenzpunkt($wp, $phiHlKw, $lastFaktor, $thetaE, $thetaHg, $vorlaufBei);

        // 2) Energiebilanz je Bin.
        $sum = [
            'space_wp' => 0.0, 'space_estab' => 0.0, 'space_heat' => 0.0,
            'ww_wp' => 0.0, 'ww_estab' => 0.0,
            'space_heat_wp' => 0.0, 'space_heat_estab' => 0.0,
            'ww_heat_wp' => 0.0, 'ww_heat_estab' => 0.0,
            'runtime' => 0.0,
        ];
        $saison = $this->leereSaison();

        foreach ($bins as $b) {
            $t = $b['t'];
            $h = $b['h'];
            if ($h <= 0) {
                continue;
            }

            $demandSpace = $phiHlKw * $lastFaktor($t);
            $demandWw = $phiWwBase;
            if ($demandSpace <= 0 && $demandWw <= 0) {
                continue;
            }

            $vl = $vorlaufBei($t);
            $cap = $this->kennlinie->phiMax($wp, $t, $vl);
            $copSpace = $this->kennlinie->cop($wp, $t, $vl);
            $copWw = $this->kennlinie->cop($wp, $t, $wwVorlauf);

            // Warmwasser-Vorrang, dann Raumwärme mit der Restleistung.
            $wwWp = min($demandWw, $cap);
            $wwEstab = $demandWw - $wwWp;
            $capRest = max(0.0, $cap - $wwWp);
            $spaceWp = min($demandSpace, $capRest);
            $spaceEstab = $demandSpace - $spaceWp;

            $elSpaceWp = $copSpace > 0 ? $spaceWp / $copSpace * $h : 0.0;
            $elWwWp = $copWw > 0 ? $wwWp / $copWw * $h : 0.0;
            $elSpaceEstab = $spaceEstab * $h;
            $elWwEstab = $wwEstab * $h;
            $runtime = $cap > 0 ? min(1.0, ($wwWp + $spaceWp) / $cap) * $h : 0.0;

            $sum['space_wp'] += $elSpaceWp;
            $sum['space_estab'] += $elSpaceEstab;
            $sum['space_heat'] += $demandSpace * $h;
            $sum['space_heat_wp'] += $spaceWp * $h;
            $sum['space_heat_estab'] += $spaceEstab * $h;
            $sum['ww_wp'] += $elWwWp;
            $sum['ww_estab'] += $elWwEstab;
            $sum['ww_heat_wp'] += $wwWp * $h;
            $sum['ww_heat_estab'] += $wwEstab * $h;
            $sum['runtime'] += $runtime;

            // Saison-Zuordnung (Strom & Wärme nach Bin-Saisongewicht).
            foreach (KlimaBinService::SAISONS as $s) {
                $g = $b['saison'][$s];
                $saison[$s]['space_el'] += ($elSpaceWp + $elSpaceEstab) * $g;
                $saison[$s]['ww_el'] += ($elWwWp + $elWwEstab) * $g;
                $saison[$s]['space_heat'] += $demandSpace * $h * $g;
                $saison[$s]['ww_heat'] += $demandWw * $h * $g;
                $saison[$s]['runtime'] += $runtime * $g;
            }
        }

        // 3) Raumwärme auf maßgeblichen Heizwärmebedarf normieren (Form bleibt, Absolutwert = Q_heiz).
        $k = $sum['space_heat'] > 0 ? $qHeizKwh / $sum['space_heat'] : 1.0;

        // Strom getrennt nach Verbrauchsart: Heizung und Warmwasser bestehen je aus
        // Verdichter + E-Stab; die Pumpen/Hilfsstrom sind ein eigener Posten (nicht im Heizanteil).
        $elHeizVerdichter = $sum['space_wp'] * $k;
        $elHeizEstab = $sum['space_estab'] * $k;
        $elWwVerdichter = $sum['ww_wp'];
        $elWwEstab = $sum['ww_estab'];
        $wVerdichter = $elHeizVerdichter + $elWwVerdichter;
        $wEstab = $elHeizEstab + $elWwEstab;

        $laufstundenSkaliert = min(8760.0, $sum['runtime'] * $k);
        $wPumpen = (HeizlastKonstanten::HILFSSTROM_LAUF_W * $laufstundenSkaliert
            + HeizlastKonstanten::HILFSSTROM_STANDBY_W * 8760.0) / 1000.0;

        $wGesamt = $wVerdichter + $wEstab + $wPumpen;

        $qHeizWp = $sum['space_heat_wp'] * $k;
        $qHeizEstab = $sum['space_heat_estab'] * $k;
        $qWwWp = $sum['ww_heat_wp'];
        $qWwEstab = $sum['ww_heat_estab'];
        $qTotal = $qHeizWp + $qHeizEstab + $qWwWp + $qWwEstab;
        $qEstab = $qHeizEstab + $qWwEstab;
        $qWp = $qTotal - $qEstab;

        $jaz = $wGesamt > 0 ? $qTotal / $wGesamt : 0.0;
        $jazNurWp = $wVerdichter > 0 ? $qWp / $wVerdichter : 0.0;

        return [
            'wp' => [
                'id' => $wp->id,
                'hersteller' => $wp->hersteller,
                'serie' => $wp->serie,
                'modell' => $wp->modell,
                'phi_max_ne_kw' => $bivalenz['phi_max_ne_kw'],
                'cop_ne' => $bivalenz['cop_ne'],
            ],
            'bivalenzpunkt_c' => $bivalenz['punkt_c'],
            'bivalenz_status' => $bivalenz['status'],
            'bivalenz_hinweis' => $bivalenz['hinweis'],
            'deckung_ne_pct' => $bivalenz['deckung_ne_pct'],
            'laufstunden_h' => (int) round($laufstundenSkaliert),
            'jaz' => round($jaz, 2),
            'jaz_nur_wp' => round($jazNurWp, 2),
            'waerme' => [
                'q_heiz_kwh' => (int) round($qHeizWp + $qHeizEstab),
                'q_ww_kwh' => (int) round($qWwWp + $qWwEstab),
                'q_total_kwh' => (int) round($qTotal),
                'q_wp_kwh' => (int) round($qWp),
                'q_estab_kwh' => (int) round($qEstab),
            ],
            'strom' => $this->stromBlock($elHeizVerdichter, $elHeizEstab, $elWwVerdichter, $elWwEstab, $wPumpen),
            'wp_deckungsanteil_pct' => $qTotal > 0 ? round($qWp / $qTotal * 100, 1) : 0.0,
            'estab_waerme_anteil_pct' => $qTotal > 0 ? round($qEstab / $qTotal * 100, 1) : 0.0,
            'saison' => $this->saisonBlock($saison, $k, $wGesamt, $qTotal),
            'klima' => [
                'zone' => $profil['zone'],
                'region' => $profil['region'],
                'theta_e' => $thetaE,
                'theta_m' => $profil['theta_m'],
                'heizgrenze_c' => $thetaHg,
                'quelle' => $profil['quelle'] ?? null,
            ],
            'annahmen' => [
                'vorlauf_c' => $vorlaufC,
                'vorlauf_min_c' => round($vorlaufMin, 0),
                'vorlauf_witterungsgefuehrt' => true,
                'ww_vorlauf_c' => $wwVorlauf,
                'ww_ueber_wp' => $wwMitWp,
                'norm_faktor_q_heiz' => round($k, 3),
                'hilfsstrom_lauf_w' => HeizlastKonstanten::HILFSSTROM_LAUF_W,
                'hilfsstrom_standby_w' => HeizlastKonstanten::HILFSSTROM_STANDBY_W,
            ],
        ];
    }

    /**
     * Bivalenzpunkt: wärmste Außentemperatur, ab der die Geräteleistung die
     * Gebäude-Heizlast nicht mehr deckt. null = monovalent (deckt bis ϑ_e).
     *
     * @param  callable(float): float  $lastFaktor
     * @param  callable(float): float  $vorlaufBei  witterungsgeführte Vorlauftemperatur je Außentemperatur
     * @return array{punkt_c: ?float, status: string, hinweis: string, deckung_ne_pct: int, phi_max_ne_kw: float, cop_ne: float}
     */
    private function bivalenzpunkt(WpKennlinie $wp, float $phiHlKw, callable $lastFaktor, float $thetaE, float $thetaHg, callable $vorlaufBei): array
    {
        $phiMaxNe = $this->kennlinie->phiMax($wp, $thetaE, $vorlaufBei($thetaE));
        $copNe = $this->kennlinie->cop($wp, $thetaE, $vorlaufBei($thetaE));
        $lastNe = $phiHlKw * $lastFaktor($thetaE);
        $deckungNe = $lastNe > 0 ? $phiMaxNe / $lastNe : 1.0;

        // Von der Heizgrenze abwärts den ersten Unterschreitungspunkt suchen.
        $punkt = null;
        $prevT = $thetaHg;
        $prevDiff = $this->kennlinie->phiMax($wp, $thetaHg, $vorlaufBei($thetaHg)) - $phiHlKw * $lastFaktor($thetaHg);
        for ($t = $thetaHg - 0.25; $t >= $thetaE - 1e-9; $t -= 0.25) {
            $diff = $this->kennlinie->phiMax($wp, $t, $vorlaufBei($t)) - $phiHlKw * $lastFaktor($t);
            if ($prevDiff >= 0 && $diff < 0) {
                $span = $prevDiff - $diff;
                $punkt = $span > 0 ? $prevT + ($t - $prevT) * ($prevDiff / $span) : $t;
                break;
            }
            $prevT = $t;
            $prevDiff = $diff;
        }

        [$status, $hinweis] = $this->bivalenzBewertung($punkt, $deckungNe);

        return [
            'punkt_c' => $punkt !== null ? round($punkt, 1) : null,
            'status' => $status,
            'hinweis' => $hinweis,
            'deckung_ne_pct' => (int) round($deckungNe * 100),
            'phi_max_ne_kw' => round($phiMaxNe, 2),
            'cop_ne' => $copNe,
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function bivalenzBewertung(?float $punkt, float $deckungNe): array
    {
        $min = HeizlastKonstanten::BIVALENZ_ZIEL_MIN_C; // -7
        $max = HeizlastKonstanten::BIVALENZ_ZIEL_MAX_C; // -3

        if ($punkt === null) {
            return ['monovalent', 'Gerät deckt die Heizlast bis zur Norm-Außentemperatur ohne Heizstab (monovalent). '
                .($deckungNe > 1.3 ? 'Überdimensioniert (Taktgefahr im Teillastbetrieb).' : 'Reichlich dimensioniert.')];
        }
        if ($punkt > $max) {
            return ['zu_klein', 'Bivalenzpunkt '.round($punkt, 1).' °C liegt über dem Zielkorridor (−3…−7 °C) – '
                .'Gerät tendenziell zu klein, höherer Heizstab-Anteil. Nächstgrößeres Modell prüfen.'];
        }
        if ($punkt < $min) {
            return ['reichlich', 'Bivalenzpunkt '.round($punkt, 1).' °C liegt unter −7 °C – sehr geringer Heizstab-Anteil, '
                .'ggf. überdimensioniert (Taktgefahr).'];
        }

        return ['optimal', 'Bivalenzpunkt '.round($punkt, 1).' °C im empfohlenen Korridor −3…−7 °C (VDI 4645).'];
    }

    /**
     * Strom-Aufteilung. Heizung und Warmwasser bestehen je aus Verdichter + E-Stab
     * (Prozente innerhalb der jeweiligen Verbrauchsart, ohne Pumpen). Pumpen/Hilfsstrom
     * sind ein eigener Posten.
     *
     * @return array<string, mixed>
     */
    private function stromBlock(float $heizVerdichter, float $heizEstab, float $wwVerdichter, float $wwEstab, float $pumpen): array
    {
        $pct = fn (float $v, float $tot): float => $tot > 0 ? round($v / $tot * 100, 1) : 0.0;
        $heizTot = $heizVerdichter + $heizEstab;
        $wwTot = $wwVerdichter + $wwEstab;
        $verdichter = $heizVerdichter + $wwVerdichter;
        $estab = $heizEstab + $wwEstab;

        return [
            'heizung' => [
                'verdichter_kwh' => (int) round($heizVerdichter),
                'estab_kwh' => (int) round($heizEstab),
                'gesamt_kwh' => (int) round($heizTot),
                'verdichter_pct' => $pct($heizVerdichter, $heizTot),
                'estab_pct' => $pct($heizEstab, $heizTot),
            ],
            'warmwasser' => [
                'verdichter_kwh' => (int) round($wwVerdichter),
                'estab_kwh' => (int) round($wwEstab),
                'gesamt_kwh' => (int) round($wwTot),
                'verdichter_pct' => $pct($wwVerdichter, $wwTot),
                'estab_pct' => $pct($wwEstab, $wwTot),
            ],
            'verdichter_kwh' => (int) round($verdichter),
            'estab_kwh' => (int) round($estab),
            'pumpen_kwh' => (int) round($pumpen),
            'gesamt_kwh' => (int) round($verdichter + $estab + $pumpen),
        ];
    }

    /**
     * @return array<string, array{space_el: float, ww_el: float, space_heat: float, ww_heat: float, runtime: float}>
     */
    private function leereSaison(): array
    {
        $leer = [];
        foreach (KlimaBinService::SAISONS as $s) {
            $leer[$s] = ['space_el' => 0.0, 'ww_el' => 0.0, 'space_heat' => 0.0, 'ww_heat' => 0.0, 'runtime' => 0.0];
        }

        return $leer;
    }

    /**
     * Saison-Verteilung (kWh und %) für Strom und Wärme.
     *
     * @param  array<string, array<string, float>>  $saison
     * @return array<string, array{strom_kwh: int, strom_pct: float, q_kwh: int, q_pct: float}>
     */
    private function saisonBlock(array $saison, float $k, float $wGesamt, float $qTotal): array
    {
        // Pumpenstrom anteilig zur Laufzeit je Saison (Rest = Differenz zu wGesamt).
        $runtimeSumme = array_sum(array_map(fn ($s) => $s['runtime'], $saison)) ?: 1.0;
        $stromOhnePumpen = 0.0;
        $tmp = [];
        foreach (KlimaBinService::SAISONS as $s) {
            $strom = $saison[$s]['space_el'] * $k + $saison[$s]['ww_el'];
            $q = $saison[$s]['space_heat'] * $k + $saison[$s]['ww_heat'];
            $tmp[$s] = ['strom' => $strom, 'q' => $q, 'runtime' => $saison[$s]['runtime']];
            $stromOhnePumpen += $strom;
        }
        $pumpenGesamt = max(0.0, $wGesamt - $stromOhnePumpen);

        $out = [];
        foreach (KlimaBinService::SAISONS as $s) {
            $strom = $tmp[$s]['strom'] + $pumpenGesamt * ($tmp[$s]['runtime'] / $runtimeSumme);
            $q = $tmp[$s]['q'];
            $out[$s] = [
                'strom_kwh' => (int) round($strom),
                'strom_pct' => $wGesamt > 0 ? round($strom / $wGesamt * 100, 1) : 0.0,
                'q_kwh' => (int) round($q),
                'q_pct' => $qTotal > 0 ? round($q / $qTotal * 100, 1) : 0.0,
            ];
        }

        return $out;
    }
}
