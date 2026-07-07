<?php

namespace App\Services\Heizlast;

use App\Services\Energie\Contracts\WpKennlinie;

/**
 * Geräte-Kennlinie einer Wärmepumpe aus den DB-Stützpunkten.
 *
 * Heizleistung φ_max(ϑ) und Leistungszahl COP(ϑ) werden aus den genormten
 * Prüfpunkten interpoliert/extrapoliert:
 *   - W35-Kurve aus A-7/A2/A7 (DIN EN 14825-Prüfpunkte),
 *   - Vorlauf-Korrektur über das Verhältnis W55/W35 bei A-7 (sonst Richtwert-Derating),
 *   - Sperrung unterhalb der Geräte-Einsatzgrenze (aussen_heizen_min_c).
 *
 * Quelle der Stützpunkte: Geräte-Datenblätter / DIN EN 14825.
 */
class WpKennlinieService
{
    /** COP-Derating je K Vorlauferhöhung, wenn keine W55-Daten vorliegen (to_verify). */
    private const COP_DERATE_PRO_K = 0.016;

    private const COP_MIN = 1.3;

    private const COP_MAX = 7.0;

    /** Maximale Heizleistung (kW) bei Außentemperatur ϑ und Vorlauf vorlaufC. */
    public function phiMax(WpKennlinie $wp, float $t, float $vorlaufC): float
    {
        if ($wp->aussen_heizen_min_c !== null && $t < $wp->aussen_heizen_min_c) {
            return 0.0;
        }

        // Volle Hersteller-Leistungskurve, falls vorhanden (genauer als 3-Punkt-Derating).
        $kurve = $this->ausKurve($wp, $t, $vorlaufC);
        if ($kurve !== null) {
            return max(0.0, round($kurve[0], 3));
        }

        $p = $this->stuetzpunkteLeistung($wp);
        if ($p === null) {
            return 0.0;
        }

        $leistung = $this->interpoliere($p, $t) * $this->leistungVorlaufFaktor($wp, $vorlaufC);

        return max(0.0, round($leistung, 3));
    }

    /** Leistungszahl COP bei Außentemperatur ϑ und Vorlauf vorlaufC. */
    public function cop(WpKennlinie $wp, float $t, float $vorlaufC): float
    {
        $kurve = $this->ausKurve($wp, $t, $vorlaufC);
        if ($kurve !== null) {
            return round(max(self::COP_MIN, min(self::COP_MAX, $kurve[1])), 2);
        }

        $c = $this->stuetzpunkteCop($wp);
        if ($c === null) {
            return self::COP_MIN;
        }

        $cop = $this->interpoliere($c, $t) * $this->copVorlaufFaktor($wp, $vorlaufC);

        return round(max(self::COP_MIN, min(self::COP_MAX, $cop)), 2);
    }

    /**
     * Heizleistung + COP aus der vollen Leistungskurve (Interpolation über Außentemperatur
     * und – zwischen W35/W45/W55 – über die Vorlauftemperatur). null ohne Kurve.
     *
     * @return array{0: float, 1: float}|null [phi_max_kw, cop]
     */
    private function ausKurve(WpKennlinie $wp, float $t, float $vorlaufC): ?array
    {
        $k = $wp->leistungskurve;
        if (! is_array($k) || ! isset($k['35'], $k['45'], $k['55'])) {
            return null;
        }

        $pProEbene = [];
        $copProEbene = [];
        foreach (['35' => 35.0, '45' => 45.0, '55' => 55.0] as $key => $ebene) {
            $rows = $k[$key] ?? null;
            if (! is_array($rows) || $rows === []) {
                return null;
            }
            $pPkt = array_map(fn ($r) => ['t' => (float) $r[0], 'y' => (float) $r[1]], $rows);
            $copPkt = array_map(fn ($r) => ['t' => (float) $r[0], 'y' => (float) $r[2]], $rows);
            $pProEbene[] = ['t' => $ebene, 'y' => $this->interpoliere($pPkt, $t)];
            $copProEbene[] = ['t' => $ebene, 'y' => $this->interpoliere($copPkt, $t)];
        }

        // Vorlauf auf den belegten Bereich klemmen (keine Extrapolation jenseits W35/W55).
        $vl = max(35.0, min(55.0, $vorlaufC));

        return [$this->interpoliere($pProEbene, $vl), $this->interpoliere($copProEbene, $vl)];
    }

    /**
     * Auslegungspunkt (Norm-Außentemperatur): Leistung, COP, Deckung.
     *
     * @return array{phi_max_kw: float, cop: float}
     */
    public function auslegungspunkt(WpKennlinie $wp, float $thetaE, float $vorlaufC): array
    {
        return [
            'phi_max_kw' => $this->phiMax($wp, $thetaE, $vorlaufC),
            'cop' => $this->cop($wp, $thetaE, $vorlaufC),
        ];
    }

    /**
     * W35-Leistungsstützpunkte (ϑ → kW), aufsteigend nach ϑ.
     *
     * @return array<int, array{t: float, y: float}>|null
     */
    private function stuetzpunkteLeistung(WpKennlinie $wp): ?array
    {
        $pkt = array_values(array_filter([
            $wp->heizleistung_am7_w35_kw !== null ? ['t' => -7.0, 'y' => $wp->heizleistung_am7_w35_kw] : null,
            $wp->heizleistung_a2_w35_kw !== null ? ['t' => 2.0, 'y' => $wp->heizleistung_a2_w35_kw] : null,
            $wp->heizleistung_a7_w35_kw !== null ? ['t' => 7.0, 'y' => $wp->heizleistung_a7_w35_kw] : null,
        ]));

        return $pkt === [] ? null : $pkt;
    }

    /**
     * W35-COP-Stützpunkte (ϑ → COP), aufsteigend nach ϑ.
     *
     * @return array<int, array{t: float, y: float}>|null
     */
    private function stuetzpunkteCop(WpKennlinie $wp): ?array
    {
        $pkt = array_values(array_filter([
            $wp->cop_am7_w35 !== null ? ['t' => -7.0, 'y' => $wp->cop_am7_w35] : null,
            $wp->cop_a2_w35 !== null ? ['t' => 2.0, 'y' => $wp->cop_a2_w35] : null,
            $wp->cop_a7_w35 !== null ? ['t' => 7.0, 'y' => $wp->cop_a7_w35] : null,
        ]));

        return $pkt === [] ? null : $pkt;
    }

    /** Vorlauf-Korrektur der Heizleistung (1.0 bei W35, Verhältnis W55/W35 bei W55). */
    private function leistungVorlaufFaktor(WpKennlinie $wp, float $vorlaufC): float
    {
        if ($wp->heizleistung_am7_w55_kw !== null && $wp->heizleistung_am7_w35_kw) {
            $r55 = $wp->heizleistung_am7_w55_kw / $wp->heizleistung_am7_w35_kw;

            return $this->lerpFaktor($vorlaufC, $r55);
        }

        return 1.0;
    }

    /** Vorlauf-Korrektur des COP (1.0 bei W35, Verhältnis W55/W35 bei W55, sonst Derating). */
    private function copVorlaufFaktor(WpKennlinie $wp, float $vorlaufC): float
    {
        if ($wp->cop_am7_w55 !== null && $wp->cop_am7_w35) {
            $r55 = $wp->cop_am7_w55 / $wp->cop_am7_w35;

            return $this->lerpFaktor($vorlaufC, $r55);
        }

        return max(0.5, 1.0 - self::COP_DERATE_PRO_K * ($vorlaufC - 35.0));
    }

    /** Linear zwischen Faktor 1.0 (35 °C) und r55 (55 °C); außerhalb geklemmt-extrapoliert. */
    private function lerpFaktor(float $vorlaufC, float $r55): float
    {
        $a = ($vorlaufC - 35.0) / 20.0; // 0 bei 35, 1 bei 55

        return max(0.4, 1.0 + $a * ($r55 - 1.0));
    }

    /**
     * Stückweise lineare Interpolation mit linearer Extrapolation an den Rändern.
     *
     * @param  array<int, array{t: float, y: float}>  $pkt  aufsteigend nach t, ≥ 1 Punkt
     */
    private function interpoliere(array $pkt, float $t): float
    {
        $n = count($pkt);
        if ($n === 1) {
            return $pkt[0]['y'];
        }
        if ($t <= $pkt[0]['t']) {
            return $this->geradeY($pkt[0], $pkt[1], $t);
        }
        if ($t >= $pkt[$n - 1]['t']) {
            return $this->geradeY($pkt[$n - 2], $pkt[$n - 1], $t);
        }
        for ($i = 0; $i < $n - 1; $i++) {
            if ($t >= $pkt[$i]['t'] && $t <= $pkt[$i + 1]['t']) {
                return $this->geradeY($pkt[$i], $pkt[$i + 1], $t);
            }
        }

        return $pkt[$n - 1]['y'];
    }

    /**
     * @param  array{t: float, y: float}  $a
     * @param  array{t: float, y: float}  $b
     */
    private function geradeY(array $a, array $b, float $t): float
    {
        $dt = $b['t'] - $a['t'];
        if (abs($dt) < 1e-9) {
            return $a['y'];
        }

        return $a['y'] + ($b['y'] - $a['y']) * ($t - $a['t']) / $dt;
    }
}
