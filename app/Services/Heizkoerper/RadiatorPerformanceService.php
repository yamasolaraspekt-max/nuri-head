<?php

namespace App\Services\Heizkoerper;

/*
 * PORT (byte-genau) aus wberechnung: app/Services/Heizlast/HeizkoerperService.php @ d81faa8.
 * NUR Namespace (App\Services\Heizlast -> App\Services\Heizkoerper) + Klassenname
 * (HeizkoerperService -> RadiatorPerformanceService) geändert. Logik/Formeln/Konstanten
 * identisch (M3 iv-a). Physik = wberechnung; hier reiner Port, kein Refactoring.
 * Original-Docblock unverändert darunter.
 */

/**
 * Heizkörper-Abgleich nach EN 442: reale Leistung bei abweichender Vorlauftemperatur
 *   Q_real = Q_norm · (Δt_log / Δt_log,norm)^n
 * mit der logarithmischen Übertemperatur Δt_log zwischen Heizmittel (Vorlauf/Rücklauf)
 * und Raumluft. Liefert je Raum den Status (Ampel) und die niedrigste Vorlauftemperatur,
 * bei der die vorhandenen Heizkörper die Raum-Auslegungsheizlast noch decken — das
 * gebäudeweite Maximum davon ist der entscheidende Wert für die WP-Auswahl.
 */
class RadiatorPerformanceService
{
    /**
     * Reale Gesamtleistung der Heizkörper eines Raums bei gegebener Vorlauftemperatur (W).
     *
     * @param  array<int, array{q_norm_w: float, exponent_n?: float, norm_bedingung?: string}>  $heizkoerper
     */
    public function qReal(array $heizkoerper, float $vorlauf, float $raumtemp, float $spreizung): float
    {
        $rl = $vorlauf - $spreizung;
        $q = 0.0;
        foreach ($heizkoerper as $hk) {
            $dtNorm = $this->dtLogNorm($hk['norm_bedingung'] ?? '75/65/20');
            $dt = $this->dtLog($vorlauf, $rl, $raumtemp);
            if ($dtNorm <= 0) {
                continue;
            }
            $q += ((float) ($hk['q_norm_w'] ?? 0)) * ($dt / $dtNorm) ** ((float) ($hk['exponent_n'] ?? 1.30));
        }

        return $q;
    }

    /**
     * Niedrigste Vorlauftemperatur (°C), bei der die Heizkörper die Heizlast decken;
     * null, wenn auch bei 90 °C nicht ausreichend (oder keine Heizkörper).
     *
     * @param  array<int, array<string, mixed>>  $heizkoerper
     */
    public function minVorlauf(array $heizkoerper, float $heizlast, float $raumtemp, float $spreizung): ?float
    {
        if ($heizlast <= 0 || $heizkoerper === []) {
            return null;
        }
        $hi = 90.0;
        if ($this->qReal($heizkoerper, $hi, $raumtemp, $spreizung) < $heizlast) {
            return null;
        }
        $lo = $raumtemp + $spreizung + 0.5;
        for ($i = 0; $i < 40; $i++) {
            $mid = ($lo + $hi) / 2;
            if ($this->qReal($heizkoerper, $mid, $raumtemp, $spreizung) >= $heizlast) {
                $hi = $mid;
            } else {
                $lo = $mid;
            }
        }

        return round($hi, 1);
    }

    /**
     * Wärmeleistung des Heizkörper-Satzes bei den gängigen Systemtemperaturen (EN 442).
     *
     * @param  array<int, array<string, mixed>>  $heizkoerper
     * @return array<int, array{vorlauf: int, ruecklauf: int, q_w: int}>
     */
    public function leistungstabelle(array $heizkoerper, float $raumtemp): array
    {
        $paare = [[75, 65], [55, 45], [50, 40], [45, 35], [35, 28]];

        return array_map(fn ($p) => [
            'vorlauf' => $p[0],
            'ruecklauf' => $p[1],
            'q_w' => (int) round($this->qReal($heizkoerper, (float) $p[0], $raumtemp, (float) ($p[0] - $p[1]))),
        ], $paare);
    }

    /** Ampel: grün (deckt), gelb (knapp ≥ 90 %), rot (Unterdeckung). */
    public function status(float $qReal, float $heizlast): string
    {
        if ($heizlast <= 0) {
            return 'na';
        }
        $deckung = $qReal / $heizlast;

        return $deckung >= 1.0 ? 'gruen' : ($deckung >= 0.9 ? 'gelb' : 'rot');
    }

    /** Logarithmische Übertemperatur (K) bei Vorlauf/Rücklauf gegen Raumluft. */
    private function dtLog(float $vorlauf, float $ruecklauf, float $raumtemp): float
    {
        $dVl = $vorlauf - $raumtemp;
        $dRl = $ruecklauf - $raumtemp;
        if ($dVl <= 0) {
            return 0.0;
        }
        if ($dRl <= 0.1) {
            $dRl = 0.1;
        }
        if (abs($dVl - $dRl) < 1e-6) {
            return $dVl;
        }

        return ($dVl - $dRl) / log($dVl / $dRl);
    }

    /** Δt_log der Normbedingung "VL/RL/Raum" (z. B. 75/65/20 → 49,8 ≈ 50 K). */
    private function dtLogNorm(string $bedingung): float
    {
        $teile = array_map('floatval', explode('/', $bedingung));
        if (count($teile) !== 3) {
            return 49.83;
        }
        [$vl, $rl, $raum] = $teile;

        return $this->dtLog($vl, $rl, $raum);
    }
}
