<?php

namespace App\Services\Heizlast;

/**
 * Höhenkorrektur der Norm-Außentemperatur nach DIN/TS 12831-1.
 *
 * Die tabellierte Norm-Außentemperatur θe gilt für die Bezugshöhe der Klima-
 * Referenzregion. Liegt das Gebäude deutlich höher oder tiefer, wird θe mit dem
 * vertikalen Temperaturgradienten der Außenluft korrigiert:
 *
 *   θe,korr = θe,Ref − G · (h_Gebäude − h_Bezug),   G = 0,01 K/m (≙ 1 K je 100 m)
 *
 * Korrigiert wird erst ab einer Höhendifferenz von ≥ 200 m (darunter Δθe = 0 K).
 * Höher gelegene Gebäude erhalten eine kältere (niedrigere) Norm-Außentemperatur.
 *
 * Quelle: DIN/TS 12831-1:2020-04, Abschnitt Klimadaten / Höhenkorrektur.
 */
class HoehenkorrekturService
{
    /** Vertikaler Temperaturgradient der Außenluft [K/m]. */
    public const GRADIENT_K_PRO_M = 0.01;

    /** Schwellwert der Höhendifferenz, ab dem korrigiert wird [m]. */
    public const SCHWELLE_M = 200.0;

    /**
     * @return array{
     *     norm_aussentemp_c: float,
     *     basis_c: float,
     *     bezugshoehe_m: ?float,
     *     standorthoehe_m: ?float,
     *     hoehendifferenz_m: ?float,
     *     delta_theta_e_k: float,
     *     korrigiert: bool,
     *     hinweis: string
     * }
     */
    public function korrigiere(float $thetaERef, ?float $standorthoehe, ?float $bezugshoehe): array
    {
        $basis = round($thetaERef, 1);

        if ($standorthoehe === null || $bezugshoehe === null) {
            return [
                'norm_aussentemp_c' => $basis,
                'basis_c' => $basis,
                'bezugshoehe_m' => $bezugshoehe,
                'standorthoehe_m' => $standorthoehe,
                'hoehendifferenz_m' => null,
                'delta_theta_e_k' => 0.0,
                'korrigiert' => false,
                'hinweis' => $standorthoehe === null
                    ? 'Keine Standorthöhe angegeben – θe ohne Höhenkorrektur.'
                    : 'Keine Bezugshöhe der Klimadaten verfügbar – θe ohne Höhenkorrektur.',
            ];
        }

        $diff = $standorthoehe - $bezugshoehe;

        if (abs($diff) < self::SCHWELLE_M) {
            return [
                'norm_aussentemp_c' => $basis,
                'basis_c' => $basis,
                'bezugshoehe_m' => round($bezugshoehe),
                'standorthoehe_m' => round($standorthoehe),
                'hoehendifferenz_m' => round($diff),
                'delta_theta_e_k' => 0.0,
                'korrigiert' => false,
                'hinweis' => 'Höhendifferenz '.round($diff).' m < 200 m – keine Korrektur nach DIN/TS 12831-1.',
            ];
        }

        $delta = -self::GRADIENT_K_PRO_M * $diff;
        $korr = round($basis + $delta, 1);

        return [
            'norm_aussentemp_c' => $korr,
            'basis_c' => $basis,
            'bezugshoehe_m' => round($bezugshoehe),
            'standorthoehe_m' => round($standorthoehe),
            'hoehendifferenz_m' => round($diff),
            'delta_theta_e_k' => round($delta, 1),
            'korrigiert' => true,
            'hinweis' => 'θe von '.number_format($basis, 1, ',', '.').' °C auf '.number_format($korr, 1, ',', '.')
                .' °C höhenkorrigiert ('.($diff > 0 ? '+' : '').round($diff).' m gegenüber Bezugshöhe '.round($bezugshoehe).' m).',
        ];
    }
}
