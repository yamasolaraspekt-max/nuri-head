<?php

namespace App\Services\Heizlast;

/**
 * Normwerte für die raumweise Heizlast nach DIN EN 12831-1 i. V. m. DIN/TS 12831-1:
 * Norm-Innentemperaturen je Nutzung, Temperatur-Korrekturfaktoren fx je Grenzfläche,
 * Mindest-Luftwechsel, Wärmebrücken-Zuschläge, Komfortzuschlag und W/m²-Plausibilitätsbänder.
 * Richtwerte (to_verify gegen die gültige DIN/TS-Fassung).
 */
final class HeizlastNormwerte
{
    /** Norm-Innentemperatur θint je Nutzung (°C). */
    public const THETA_INT = [
        'wohnen' => 20.0, 'wohnzimmer' => 20.0, 'schlafen' => 20.0, 'kinderzimmer' => 20.0,
        'kueche' => 20.0, 'bad' => 24.0, 'wc' => 20.0, 'flur' => 15.0, 'diele' => 15.0,
        'treppenhaus' => 10.0, 'buero' => 20.0, 'keller_beheizt' => 15.0, 'hwr' => 15.0,
    ];

    public const THETA_INT_DEFAULT = 20.0;

    /** Temperatur-Korrekturfaktor fx je Grenzfläche (–). */
    public const FX = [
        'aussen' => 1.0,        // direkt nach außen
        'unbeheizt' => 0.5,     // an unbeheizten Raum/Pufferzone
        'erdreich' => 0.45,     // gegen Erdreich (vereinfacht)
        'beheizt' => 0.0,       // an gleich temperierten beheizten Raum
    ];

    public const FX_DEFAULT = 1.0;

    /** Mindest-/Infiltrations-Luftwechsel je Nutzung (1/h). */
    public const LUFTWECHSEL = [
        'wohnen' => 0.5, 'wohnzimmer' => 0.5, 'schlafen' => 0.5, 'kinderzimmer' => 0.5,
        'kueche' => 0.5, 'bad' => 0.5, 'wc' => 0.5, 'flur' => 0.5, 'buero' => 0.5,
    ];

    public const LUFTWECHSEL_DEFAULT = 0.5;

    /** ρ·cp der Luft (Wh/m³K) – Faktor in Φ_V = 0,34 · V̇ · Δθ. */
    public const LUFT_RHO_CP = 0.34;

    /** Pauschaler Wärmebrücken-Zuschlag ΔU_WB (W/m²K) je Modus. */
    public const WAERMEBRUECKEN = [
        'keine' => 0.0,
        'pauschal' => 0.10,            // ohne Nachweis (DIN/TS / DIN 4108 Bbl. 2)
        'pauschal_nachweis' => 0.05,   // mit Gleichwertigkeitsnachweis nach DIN 4108 Bbl. 2
    ];

    /** Maximaler Komfortzuschlag auf θint für die Auslegungsheizlast (K). */
    public const KOMFORTZUSCHLAG_MAX_K = 3.0;

    /**
     * Spezifische-Heizlast-Plausibilität (W/m²) → Einordnung + Warnflag.
     *
     * @return array{kategorie: string, warnung: bool}
     */
    public static function plausi(float $wProM2): array
    {
        return match (true) {
            $wProM2 < 10 => ['kategorie' => 'sehr niedrig – Eingaben prüfen', 'warnung' => true],
            $wProM2 < 30 => ['kategorie' => 'Neubau / Effizienzhaus', 'warnung' => false],
            $wProM2 < 60 => ['kategorie' => 'saniert', 'warnung' => false],
            $wProM2 <= 120 => ['kategorie' => 'Altbau unsaniert', 'warnung' => false],
            default => ['kategorie' => 'sehr hoch – Eingaben prüfen', 'warnung' => true],
        };
    }

    public static function thetaInt(string $nutzung): float
    {
        return self::THETA_INT[$nutzung] ?? self::THETA_INT_DEFAULT;
    }

    public static function fx(string $grenzflaeche): float
    {
        return self::FX[$grenzflaeche] ?? self::FX_DEFAULT;
    }

    public static function luftwechsel(string $nutzung): float
    {
        return self::LUFTWECHSEL[$nutzung] ?? self::LUFTWECHSEL_DEFAULT;
    }

    public static function deltaUwb(string $modus): float
    {
        return self::WAERMEBRUECKEN[$modus] ?? self::WAERMEBRUECKEN['pauschal'];
    }
}
