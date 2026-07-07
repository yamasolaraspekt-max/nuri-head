<?php

namespace App\Services\Heizlast;

/**
 * Fußbodenheizung nach DIN EN 1264 (Auslegung Flächenheizung).
 *
 * Spezifische Wärmeleistung über das Widerstandsnetz Heizwasser → Raum:
 *   q = ΔθH / R_ges   [W/m²]
 *   R_ges = 1/α + R_Belag + R_Estrich,über + R_Verlege
 * mit α = 10,8 W/(m²K) (EN 1264 Wärmeübergang Fußboden→Raum), der logarithmischen
 * Heizmittel-Übertemperatur ΔθH und der Wärmespreizung im Estrich aus Verlegeabstand
 * und Rohraußendurchmesser (Glück/EN-1264-Modell):
 *   R_Verlege = T · ln(T / (π·D_a)) / (2π·λ_Estrich)
 *
 * Begrenzung über die zulässige mittlere Oberflächentemperatur (29 °C Aufenthalts-,
 * 33 °C Bad-, 35 °C Randzone): q_max = (θF,max − θi)·α. Liegt der Bedarf darüber, ist
 * die Fläche allein unzureichend (EN 1264 Grenzkurve). Richtwerte mit `to_verify`
 * gegen die vollständigen EN-1264-2-Kennlinientabellen.
 */
class FussbodenheizungService
{
    /** Wärmeübergangskoeffizient Fußbodenoberfläche → Raum (EN 1264). */
    private const ALPHA = 10.8;

    /** Wärmeleitfähigkeit Zementestrich (Richtwert DIN EN 1264-3). */
    private const LAMBDA_ESTRICH = 1.2;

    /** Zulässige mittlere Oberflächentemperatur der Randzone (EN 1264). */
    private const RANDZONE_OBERFLAECHE_C = 35.0;

    /** Maximale Breite der Randzone entlang der Außenwand (EN 1264). */
    private const RANDZONE_MAX_BREITE_M = 1.0;

    /**
     * Vollständige Auslegung einer Fußbodenheizung für einen Raum.
     *
     * Optional mit Randzone (EN 1264): ein Streifen ≤ 1 m entlang der Außenwände mit
     * zulässiger Oberflächentemperatur 35 °C (statt 29/33 °C) und meist engerem
     * Verlegeabstand liefert dort mehr Leistung, wo der Wärmeverlust sitzt.
     *
     * @param  array<string, mixed>  $fbh  rohr_aussen_mm, verlegeabstand_mm, bodenbelag_r, estrich_ueber_mm,
     *                                     randzone (bool), aussenwand_laenge_m, randzone_breite_m, randzone_verlegeabstand_mm
     * @return array<string, mixed>
     */
    public function analyse(array $fbh, float $flaeche, float $heizlast, float $raumtemp, float $vorlauf, float $spreizung, float $maxOberflaeche = 29.0): array
    {
        $rohrAussenM = max(0.010, (float) ($fbh['rohr_aussen_mm'] ?? 16) / 1000);
        $zonen = $this->zonen($fbh, $flaeche, $maxOberflaeche);

        $qRealW = 0.0;
        $qMaxW = 0.0;          // flächengewichtete Oberflächen-Grenzleistung
        $gesamtlaenge = 0.0;
        foreach ($zonen as $z) {
            $qMaxZ = max(0.0, ($z['max_oberflaeche'] - $raumtemp) * self::ALPHA);
            $qEffZ = min($this->qProM2($z['r_ges'], $vorlauf, $raumtemp, $spreizung), $qMaxZ);
            $qRealW += $qEffZ * $z['flaeche'];
            $qMaxW += $qMaxZ * $z['flaeche'];
            $gesamtlaenge += $z['flaeche'] / $z['verlegeabstand_m'];
        }
        $qEff = $flaeche > 0 ? $qRealW / $flaeche : 0.0;
        $qMaxOberflaeche = $flaeche > 0 ? $qMaxW / $flaeche : 0.0;

        $maxKreis = $this->maxKreislaenge($rohrAussenM);
        $kreise = max(1, (int) ceil($gesamtlaenge / $maxKreis));

        $bedarfProM2 = $flaeche > 0 ? $heizlast / $flaeche : 0.0;
        $oberflaecheUeberschritten = $bedarfProM2 > $qMaxOberflaeche + 0.5;

        $out = [
            'q_eff_w_m2' => round($qEff, 1),
            'q_real_w' => (int) round($qRealW),
            'q_max_oberflaeche_w_m2' => round($qMaxOberflaeche, 1),
            'oberflaeche_ueberschritten' => $oberflaecheUeberschritten,
            'laenge_pro_m2' => round($flaeche > 0 ? $gesamtlaenge / $flaeche : 0.0, 1),
            'gesamtlaenge_m' => (int) round($gesamtlaenge),
            'heizkreise' => $kreise,
            'w_pro_kreis' => (int) round($qRealW / $kreise),
            'laenge_pro_kreis_m' => (int) round($gesamtlaenge / $kreise),
            'deckung_pct' => $heizlast > 0 ? (int) round($qRealW / $heizlast * 100) : null,
            'status' => $this->status($qRealW, $heizlast),
            'min_vorlauf_c' => $this->minVorlauf($fbh, $flaeche, $heizlast, $raumtemp, $spreizung, $maxOberflaeche),
        ];

        // Randzonen-Detail (Zone 0 ist die Randzone, sofern aktiv).
        if (count($zonen) > 1) {
            $rand = $zonen[0];
            $qMaxRand = max(0.0, ($rand['max_oberflaeche'] - $raumtemp) * self::ALPHA);
            $out['randzone'] = [
                'flaeche_m2' => round($rand['flaeche'], 1),
                'verlegeabstand_mm' => (int) round($rand['verlegeabstand_m'] * 1000),
                'max_oberflaeche_c' => $rand['max_oberflaeche'],
                'q_eff_w_m2' => round(min($this->qProM2($rand['r_ges'], $vorlauf, $raumtemp, $spreizung), $qMaxRand), 1),
            ];
        }

        return $out;
    }

    /**
     * Zerlegt die Heizfläche in Zonen: bei aktiver Randzone in einen Außenwand-Streifen
     * (≤ 1 m, 35 °C, optional engerer Abstand) und die Aufenthaltszone, sonst eine Zone.
     *
     * @param  array<string, mixed>  $fbh
     * @return list<array{name: string, flaeche: float, verlegeabstand_m: float, max_oberflaeche: float, r_ges: float}>
     */
    private function zonen(array $fbh, float $flaeche, float $maxOberflaeche): array
    {
        $abstandNormM = max(0.05, (float) ($fbh['verlegeabstand_mm'] ?? 150) / 1000);
        $aufenthalt = [
            'name' => 'aufenthalt',
            'flaeche' => $flaeche,
            'verlegeabstand_m' => $abstandNormM,
            'max_oberflaeche' => $maxOberflaeche,
            'r_ges' => $this->rGes($fbh),
        ];

        if (empty($fbh['randzone'])) {
            return [$aufenthalt];
        }

        $lAussen = max(0.0, (float) ($fbh['aussenwand_laenge_m'] ?? 0));
        $breite = min(self::RANDZONE_MAX_BREITE_M, max(0.0, (float) ($fbh['randzone_breite_m'] ?? self::RANDZONE_MAX_BREITE_M)));
        $aRand = min($lAussen * $breite, $flaeche);
        if ($aRand < 0.5) {
            return [$aufenthalt]; // keine (sinnvolle) Außenwandfläche → keine Randzone
        }

        $abstandRandMm = (float) ($fbh['randzone_verlegeabstand_mm'] ?? min(100.0, (float) ($fbh['verlegeabstand_mm'] ?? 100)));
        $fbhRand = array_merge($fbh, ['verlegeabstand_mm' => $abstandRandMm]);

        return [
            [
                'name' => 'randzone',
                'flaeche' => $aRand,
                'verlegeabstand_m' => max(0.05, $abstandRandMm / 1000),
                'max_oberflaeche' => max($maxOberflaeche, self::RANDZONE_OBERFLAECHE_C),
                'r_ges' => $this->rGes($fbhRand),
            ],
            [
                'name' => 'aufenthalt',
                'flaeche' => $flaeche - $aRand,
                'verlegeabstand_m' => $abstandNormM,
                'max_oberflaeche' => $maxOberflaeche,
                'r_ges' => $this->rGes($fbh),
            ],
        ];
    }

    /**
     * Gesamtleistung [W] aller Zonen bei gegebener Vorlauftemperatur (je Zone durch die
     * zulässige Oberflächentemperatur begrenzt).
     *
     * @param  list<array{flaeche: float, max_oberflaeche: float, r_ges: float}>  $zonen
     */
    private function qRealBeiVorlauf(array $zonen, float $vorlauf, float $raumtemp, float $spreizung): float
    {
        $q = 0.0;
        foreach ($zonen as $z) {
            $qMaxZ = max(0.0, ($z['max_oberflaeche'] - $raumtemp) * self::ALPHA);
            $q += min($this->qProM2($z['r_ges'], $vorlauf, $raumtemp, $spreizung), $qMaxZ) * $z['flaeche'];
        }

        return $q;
    }

    /**
     * Spezifische Wärmeleistung q [W/m²] aus der Heizmittel-Übertemperatur.
     */
    public function qProM2(float $rGes, float $vorlauf, float $raumtemp, float $spreizung): float
    {
        $dt = $this->dtLog($vorlauf, $vorlauf - $spreizung, $raumtemp);

        return $dt <= 0 ? 0.0 : $dt / $rGes;
    }

    /**
     * Mindest-Vorlauftemperatur, bei der die Fläche die Heizlast deckt (EN 1264).
     * null, wenn die Oberflächentemperatur-Grenze die Deckung verhindert.
     */
    public function minVorlauf(array $fbh, float $flaeche, float $heizlast, float $raumtemp, float $spreizung, float $maxOberflaeche = 29.0): ?float
    {
        if ($flaeche <= 0 || $heizlast <= 0) {
            return null;
        }
        $zonen = $this->zonen($fbh, $flaeche, $maxOberflaeche);

        $qMaxW = 0.0;
        foreach ($zonen as $z) {
            $qMaxW += max(0.0, ($z['max_oberflaeche'] - $raumtemp) * self::ALPHA) * $z['flaeche'];
        }
        if ($heizlast > $qMaxW + 0.5 * $flaeche) {
            return null; // Grenzkurve: Fläche allein unzureichend
        }

        $lo = $raumtemp + 1;
        $hi = 60.0;
        for ($i = 0; $i < 40; $i++) {
            $mid = ($lo + $hi) / 2;
            if ($this->qRealBeiVorlauf($zonen, $mid, $raumtemp, $spreizung) >= $heizlast) {
                $hi = $mid;
            } else {
                $lo = $mid;
            }
        }

        return round($hi, 1);
    }

    public function status(float $qRealW, float $heizlast): string
    {
        if ($heizlast <= 0) {
            return 'na';
        }
        $deckung = $qRealW / $heizlast;

        return $deckung >= 1.0 ? 'gruen' : ($deckung >= 0.9 ? 'gelb' : 'rot');
    }

    /**
     * Spezifische Leistung q [W/m²] bei den gängigen niedrigen Vorlauftemperaturen (EN 1264),
     * begrenzt durch die zulässige Oberflächentemperatur.
     *
     * @param  array<string, mixed>  $fbh
     * @return array<int, array{vorlauf: int, ruecklauf: int, q_w_m2: float}>
     */
    public function leistungstabelle(array $fbh, float $raumtemp, float $maxOberflaeche = 29.0): array
    {
        $rGes = $this->rGes($fbh);
        $qMax = max(0.0, ($maxOberflaeche - $raumtemp) * self::ALPHA);
        $paare = [[30, 25], [35, 28], [40, 33], [45, 38]];

        return array_map(fn ($p) => [
            'vorlauf' => $p[0],
            'ruecklauf' => $p[1],
            'q_w_m2' => round(min($this->qProM2($rGes, (float) $p[0], $raumtemp, (float) ($p[0] - $p[1])), $qMax), 1),
        ], $paare);
    }

    /**
     * Gesamtwärmedurchgangswiderstand Heizwasser → Raum [m²K/W].
     *
     * @param  array<string, mixed>  $fbh
     */
    private function rGes(array $fbh): float
    {
        $belagR = max(0.0, (float) ($fbh['bodenbelag_r'] ?? 0.0));
        $estrichUeberM = max(0.0, (float) ($fbh['estrich_ueber_mm'] ?? 45) / 1000);
        $verlegeabstandM = max(0.05, (float) ($fbh['verlegeabstand_mm'] ?? 150) / 1000);
        $rohrAussenM = max(0.010, (float) ($fbh['rohr_aussen_mm'] ?? 16) / 1000);

        $rUebergang = 1 / self::ALPHA;
        $rEstrichUeber = $estrichUeberM / self::LAMBDA_ESTRICH;
        // Wärmespreizung im Estrich zwischen den Rohren (Verlegeabstand + Rohr-Ø).
        $arg = $verlegeabstandM / (M_PI * $rohrAussenM);
        $rVerlege = $arg > 1 ? $verlegeabstandM * log($arg) / (2 * M_PI * self::LAMBDA_ESTRICH) : 0.0;

        return $rUebergang + $belagR + $rEstrichUeber + $rVerlege;
    }

    /** Logarithmische Heizmittel-Übertemperatur (EN 1264). */
    private function dtLog(float $vorlauf, float $ruecklauf, float $raumtemp): float
    {
        $dV = $vorlauf - $raumtemp;
        $dR = $ruecklauf - $raumtemp;
        if ($dV <= 0 || $dR <= 0) {
            return 0.0;
        }
        if (abs($dV - $dR) < 0.01) {
            return $dV;
        }

        return ($dV - $dR) / log($dV / $dR);
    }

    /** Max. Heizkreislänge je Rohraußendurchmesser (Faustwerte, Druckverlust). */
    private function maxKreislaenge(float $rohrAussenM): float
    {
        $mm = $rohrAussenM * 1000;

        return $mm >= 20 ? 135.0 : ($mm >= 17 ? 120.0 : 100.0);
    }
}
