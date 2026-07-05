<?php

namespace App\Services\Heizkoerper;

/*
 * PORT (byte-genau) aus wberechnung: app/Services/Heizlast/HydraulikService.php @ d81faa8.
 * NUR Namespace (App\Services\Heizlast -> App\Services\Heizkoerper) + Klassenname
 * (HydraulikService -> HydraulicService) geändert. Logik/Formeln/Konstanten identisch
 * (M3 iv-a). Physik = wberechnung; hier reiner Port, kein Refactoring.
 * Original-Docblock unverändert darunter.
 */

/**
 * Hydraulischer Abgleich (Verfahren B, raumweise berechnet).
 *
 * Auslegungs-Volumenstrom je Raum aus Heizlast und Spreizung:
 *   V̇ [l/h] = Q [W] / (1,163 [Wh/(l·K)] · Δϑ [K])
 * Erforderlicher kv-Wert des Thermostatventils bei gewähltem Ventil-Differenzdruck:
 *   kv = V̇ [m³/h] / √(Δp [bar])
 * → Voreinstellung aus einer repräsentativen Thermostatventil-Tabelle (DN 15).
 *
 * Hinweis: Die Volumenströme sind exakt; Voreinstellung und Pumpenförderhöhe sind
 * Richtwerte (das endgültige Strangschema mit Rohrlängen/-durchmessern ist nicht
 * hinterlegt) und für die finale Einregulierung am Objekt zu prüfen.
 */
class HydraulicService
{
    /** Wärmekapazität Wasser: 1,163 Wh/(l·K). */
    private const C_WASSER = 1.163;

    /** Repräsentatives Thermostatventil DN 15: Voreinstellung → kv (m³/h/√bar), Richtwerte. */
    private const VENTIL_KV = [1 => 0.11, 2 => 0.18, 3 => 0.28, 4 => 0.40, 5 => 0.56, 6 => 0.77, 7 => 1.00];

    /** Richtwerte Druckverlust für die Pumpen-Überschlagsrechnung (mbar). */
    private const DP_WAERMEERZEUGER_MBAR = 250;

    private const DP_ROHRNETZ_MBAR = 150;

    /** Auslegungs-Volumenstrom (l/h) aus Heizlast (W) und Spreizung (K). */
    public function volumenstrom(float $qW, float $spreizung): float
    {
        return $spreizung > 0 ? $qW / (self::C_WASSER * $spreizung) : 0.0;
    }

    /** Erforderlicher kv-Wert bei gegebenem Volumenstrom (l/h) und Ventil-Differenzdruck (mbar). */
    public function kvErforderlich(float $vLh, float $dpMbar): float
    {
        $dpBar = max(0.005, $dpMbar / 1000);

        return ($vLh / 1000) / sqrt($dpBar);
    }

    /** Kleinste Voreinstellung, deren kv den erforderlichen kv erreicht (sonst „offen“). */
    public function voreinstellung(float $kvErf): int|string
    {
        foreach (self::VENTIL_KV as $stufe => $kv) {
            if ($kv >= $kvErf) {
                return $stufe;
            }
        }

        return 'offen';
    }

    /**
     * Raumweiser Abgleich + gebäudeweite Summen.
     *
     * @param  array<int, array<string, mixed>>  $raeume  Ergebnis-Räume (mit auslegungsheizlast_w, name)
     * @return array<string, mixed>
     */
    public function abgleich(array $raeume, float $spreizung, float $dpVentilMbar = 100): array
    {
        $rows = [];
        $gesamtV = 0.0;
        foreach ($raeume as $r) {
            $q = (float) ($r['auslegungsheizlast_w'] ?? 0);
            if ($q <= 0) {
                continue;
            }
            $v = $this->volumenstrom($q, $spreizung);
            $kv = $this->kvErforderlich($v, $dpVentilMbar);
            $gesamtV += $v;
            $rows[] = [
                'raum' => $r['name'] ?? 'Raum',
                'q_w' => (int) round($q),
                'volumenstrom_lh' => round($v, 1),
                'kv_erforderlich' => round($kv, 2),
                'voreinstellung' => $this->voreinstellung($kv),
            ];
        }

        // Pumpenförderhöhe (Überschlag): Ventil + Wärmeerzeuger + Rohrnetz, mbar → m (≈ /98).
        $dpGesamtMbar = $dpVentilMbar + self::DP_WAERMEERZEUGER_MBAR + self::DP_ROHRNETZ_MBAR;

        return [
            'spreizung_k' => $spreizung,
            'dp_ventil_mbar' => $dpVentilMbar,
            'raeume' => $rows,
            'gesamt_volumenstrom_lh' => (int) round($gesamtV),
            'gesamt_volumenstrom_m3h' => round($gesamtV / 1000, 2),
            'foerderhoehe_m_ueberschlag' => round($dpGesamtMbar / 98, 1),
        ];
    }
}
