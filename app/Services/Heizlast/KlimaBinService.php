<?php

namespace App\Services\Heizlast;

/**
 * Klima-Grundlage des Temperatur-Bin-Verfahrens (Bivalenz/JAZ/Saison).
 *
 * Liefert je Klimazone (PLZ-Region) eine Jahres-Temperaturhäufigkeit h(ϑ)
 * (Stunden je 1-K-Temperatur-Bin) sowie eine Saison-Zuordnung je Bin.
 *
 * Die Verteilung ist eine je Zone an die DWD-Testreferenzjahre (TRY) angelehnte,
 * deterministische Näherung (Normalverteilung um die Jahresmitteltemperatur mit
 * Kältetail bis zur Norm-Außentemperatur). Ergebnisrelevante Klimawerte sind
 * Anhaltswerte (to_verify) und können später mit echten TRY-Datensätzen
 * (DWD, ortsbezogen) geschärft werden. Für den Energie-Split ist primär die
 * Form der Verteilung maßgeblich – die absolute Jahresheizarbeit wird in der
 * Bin-Simulation auf den maßgeblichen Heizwärmebedarf normiert.
 *
 * Grundlagen: DWD Testreferenzjahr (TRY), Heizgrenztemperatur nach VDI 4655.
 */
class KlimaBinService
{
    /** Standardabweichung der Jahres-Temperaturverteilung (K, to_verify). */
    private const SIGMA_JAHR = 7.5;

    /** Standardabweichung der Saison-Zugehörigkeit (K, to_verify). */
    private const SIGMA_SAISON = 6.0;

    /** Bin-Raster (°C). */
    private const T_MIN = -22.0;

    private const T_MAX = 30.0;

    private const T_STEP = 1.0;

    public const SAISONS = ['winter', 'fruehling', 'sommer', 'herbst'];

    /**
     * Klimazonen je PLZ-Anfangsziffer (konsistent zu HeizlastKonstanten::normAussentemp).
     * theta_m = Jahresmitteltemperatur (°C, to_verify, DWD-Anhaltswert).
     *
     * @var array<string, array{theta_e: float, theta_m: float, region: string}>
     */
    private const ZONEN = [
        'sued' => ['theta_e' => -14.0, 'theta_m' => 8.0, 'region' => 'Süddeutschland / Alpenvorland'],
        'nordost' => ['theta_e' => -10.0, 'theta_m' => 9.5, 'region' => 'Nord / Ost'],
        'ost' => ['theta_e' => -12.0, 'theta_m' => 9.0, 'region' => 'Ost / Zentral'],
        'west' => ['theta_e' => -10.0, 'theta_m' => 10.5, 'region' => 'West / Rheinland'],
        'mitte' => ['theta_e' => -12.0, 'theta_m' => 9.0, 'region' => 'Mitte / Mittelgebirge'],
    ];

    /** Heizgrenztemperatur (°C): unterhalb wird geheizt. Default Bestand (VDI 4655). */
    public const HEIZGRENZE_C = 15.0;

    /**
     * @return array{zone: string, theta_e: float, theta_m: float, region: string, heizgrenze_c: float, bins: array<int, array{t: float, h: float, saison: array<string, float>}>}
     */
    public function profil(?string $plz): array
    {
        $zoneKey = $this->zoneKey($plz);
        $z = self::ZONEN[$zoneKey];

        $bins = $this->bins($z['theta_m']);

        return [
            'zone' => $zoneKey,
            'theta_e' => $z['theta_e'],
            'theta_m' => $z['theta_m'],
            'region' => $z['region'],
            'heizgrenze_c' => self::HEIZGRENZE_C,
            'bins' => $bins,
        ];
    }

    /** Norm-Außentemperatur (°C) der Zone (delegiert an die zentrale Tabelle). */
    public function normAussentemp(?string $plz): float
    {
        return HeizlastKonstanten::normAussentemp($plz);
    }

    /**
     * Temperatur-Bins mit Stundenzahl und Saison-Zuordnung.
     *
     * @return array<int, array{t: float, h: float, saison: array<string, float>}>
     */
    private function bins(float $thetaM): array
    {
        $saisonMittel = [
            'winter' => $thetaM - 8.0,
            'fruehling' => $thetaM + 0.5,
            'sommer' => $thetaM + 8.5,
            'herbst' => $thetaM + 1.0,
        ];

        $roh = [];
        $summe = 0.0;
        for ($t = self::T_MIN; $t <= self::T_MAX + 1e-9; $t += self::T_STEP) {
            $w = $this->gauss($t, $thetaM, self::SIGMA_JAHR);
            $roh[] = ['t' => round($t, 1), 'w' => $w];
            $summe += $w;
        }

        $bins = [];
        foreach ($roh as $b) {
            $bins[] = [
                't' => $b['t'],
                'h' => $summe > 0 ? $b['w'] / $summe * 8760.0 : 0.0,
                'saison' => $this->saisonGewichte($b['t'], $saisonMittel),
            ];
        }

        return $bins;
    }

    /**
     * Saison-Zugehörigkeit eines Temperatur-Bins (Summe = 1).
     *
     * @param  array<string, float>  $saisonMittel
     * @return array<string, float>
     */
    private function saisonGewichte(float $t, array $saisonMittel): array
    {
        $w = [];
        $summe = 0.0;
        foreach (self::SAISONS as $s) {
            $g = $this->gauss($t, $saisonMittel[$s], self::SIGMA_SAISON);
            $w[$s] = $g;
            $summe += $g;
        }
        foreach ($w as $s => $g) {
            $w[$s] = $summe > 0 ? $g / $summe : 0.25;
        }

        return $w;
    }

    private function gauss(float $x, float $mu, float $sigma): float
    {
        $z = ($x - $mu) / $sigma;

        return exp(-0.5 * $z * $z);
    }

    private function zoneKey(?string $plz): string
    {
        $d = is_numeric(substr((string) $plz, 0, 1)) ? (int) substr((string) $plz, 0, 1) : 5;

        return match ($d) {
            8, 9 => 'sued',
            1, 2 => 'nordost',
            0, 3 => 'ost',
            4, 5 => 'west',
            default => 'mitte',
        };
    }
}
