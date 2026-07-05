<?php

namespace App\Services\Heizlast;

/**
 * Zentrale, dokumentierte Defaults/Tabellen der Wärmepumpen-Auslegung.
 * Richtwerte; ergebnisrelevante Werte als to_verify kennzeichnen.
 * Grundlagen: DIN EN 12831-1/-3, VDI 4650, GEG/BEG-EM.
 */
final class HeizlastKonstanten
{
    /** Heizvollbenutzungsstunden (Default; 1.800–2.200). */
    public const B_VH_DEFAULT = 2000;

    /** WW-Leistungszuschlag je Person (kW). */
    public const WW_KW_PRO_PERSON = 0.20;

    /** Heizgrenztemperatur (°C): unterhalb wird geheizt (Bestand, VDI 4655; to_verify). */
    public const HEIZGRENZE_C = 15.0;

    /** Vorlauftemperatur der Trinkwarmwasser-Bereitung über die WP (°C, to_verify). */
    public const WW_VORLAUF_C = 50.0;

    /** Minimale (witterungsgeführte) Heiz-Vorlauftemperatur an der Heizgrenze (°C, to_verify). */
    public const VORLAUF_MIN_C = 28.0;

    /** Hilfsstrom (W) der Umwälz-/Heizkreispumpen während Verdichterlaufs (Richtwert, to_verify). */
    public const HILFSSTROM_LAUF_W = 45.0;

    /** Hilfsstrom (W) für Regelung/Standby ganzjährig (Richtwert, to_verify). */
    public const HILFSSTROM_STANDBY_W = 15.0;

    /** Zielkorridor des Bivalenzpunktes für Luft/Wasser-WP (°C, VDI 4645). */
    public const BIVALENZ_ZIEL_MIN_C = -7.0;

    public const BIVALENZ_ZIEL_MAX_C = -3.0;

    /** Sanity-Grenzen spezifische Heizlast (W/m²). */
    public const Q_SPEZ_MIN = 15;

    public const Q_SPEZ_MAX = 200;

    public const RAUMTEMP_DEFAULT_C = 20.0;

    public const NORM_AUSSENTEMP_DEFAULT_C = -12.0;

    /** Endenergie-Umrechnung je Heizmedium/Einheit → kWh. */
    public const HEIZMEDIUM_KWH = [
        'erdgas|kWh' => 1.0,
        'erdgas|m3' => 10.0,        // to_verify: Brennwert × Zustandszahl aus Rechnung
        'heizoel|Liter' => 10.0,
        'fluessiggas|Liter' => 6.6,
        'fluessiggas|kg' => 12.8,
        'pellets|kg' => 4.8,
        'scheitholz|kg' => 4.0,     // to_verify
        'scheitholz|Ster' => 1500.0,
        'strom|kWh' => 1.0,
        'fernwaerme|kWh' => 1.0,
    ];

    /** Jahresnutzungsgrad des Alterzeugers. */
    public const NUTZUNGSGRAD = [
        'konstant_alt' => 0.75,
        'niedertemperatur' => 0.88,
        'brennwert' => 0.96,
        'strom_direkt' => 1.00,
        'fernwaerme' => 1.00,
        'sonstige' => 0.85,
    ];

    /** WW-Jahresenergie je Person (kWh). */
    public const WW_KWH_PA = [
        'sparsam' => 500,
        'normal' => 700,
        'hoch' => 1000,
    ];

    /** Basis-Flächenheizlast nach Baualtersklasse (W/m²). */
    public const Q_BASE = [
        1977 => 150, 1994 => 90, 2001 => 70, 2008 => 55, 2015 => 45, 2022 => 35, 9999 => 25,
    ];

    /** Auslegungs-Vorlauftemperatur + JAZ-Klasse je Heizsystem. */
    public const VORLAUF = [
        'fussbodenheizung' => ['temp' => 35, 'klasse' => 'fbh'],
        'heizkoerper' => ['temp' => 55, 'klasse' => 'hk'],
        'beides' => ['temp' => 45, 'klasse' => 'beides'],
    ];

    /** JAZ-Richtwerte: wp_typ × Vorlaufklasse (to_verify; final VDI 4650). */
    public const JAZ = [
        'luft_wasser' => ['fbh' => 4.0, 'beides' => 3.4, 'hk' => 2.9],
        'sole_sonde' => ['fbh' => 4.7, 'beides' => 4.1, 'hk' => 3.5],
        'sole_kollektor' => ['fbh' => 4.4, 'beides' => 3.9, 'hk' => 3.3],
        'wasser_wasser' => ['fbh' => 5.0, 'beides' => 4.4, 'hk' => 3.7],
    ];

    public static function heizmediumFaktor(string $medium, string $einheit): float
    {
        return self::HEIZMEDIUM_KWH[$medium.'|'.$einheit] ?? 1.0;
    }

    public static function nutzungsgrad(string $typ): float
    {
        return self::NUTZUNGSGRAD[$typ] ?? self::NUTZUNGSGRAD['sonstige'];
    }

    public static function wwKwhPa(string $komfort): int
    {
        return self::WW_KWH_PA[$komfort] ?? self::WW_KWH_PA['normal'];
    }

    public static function qBase(int $baujahr): int
    {
        foreach (self::Q_BASE as $bis => $q) {
            if ($baujahr <= $bis) {
                return $q;
            }
        }

        return 25;
    }

    /** @return array{temp:int, klasse:string} */
    public static function vorlauf(string $heizsystem): array
    {
        return self::VORLAUF[$heizsystem] ?? self::VORLAUF['heizkoerper'];
    }

    public static function jaz(string $wpTyp, string $klasse): float
    {
        return self::JAZ[$wpTyp][$klasse] ?? self::JAZ['luft_wasser'][$klasse] ?? 3.0;
    }

    /** Multiplikative Korrekturfaktoren auf q_base (Methode H, überschlägig). */
    public static function korrekturFaktoren(HeizlastEingabe $e): array
    {
        $f = [];
        $f['fassade'] = match ($e->fassadendaemmung) {
            'wdvs' => 0.80, 'teilweise' => 0.90, default => 1.00,
        };
        $f['dach'] = match ($e->dachdaemmung) {
            'gut' => 0.90, 'teilweise' => 0.95, default => 1.00,
        };
        $f['keller'] = $e->kellerdecke_gedaemmt ? 0.97 : 1.00;
        $f['fenster'] = match ($e->fensterverglasung) {
            'einfach' => 1.15, '2fach_ws' => 0.97, '3fach' => 0.90, default => 1.00,
        };
        $f['lueftung'] = $e->lueftung === 'kwl_wrg' ? 0.90 : 1.00;
        $f['mauerwerk'] = match ($e->mauerwerk) {
            'beton', 'kalksandstein' => 1.05, 'zweischalig' => 1.00, 'fachwerk' => 1.15, 'holzstaender' => 0.95, default => 1.00,
        };
        $f['gebaeudetyp'] = match ($e->gebaeudetyp) {
            'freistehend' => 1.05, 'reihenmittel', 'wohnung' => 0.90, 'reihenend' => 0.95, default => 1.00,
        };

        return $f;
    }

    /** Norm-Außentemperatur je PLZ-Region (Anhaltswert). */
    public static function normAussentemp(?string $plz): float
    {
        $d = is_numeric(substr((string) $plz, 0, 1)) ? (int) substr((string) $plz, 0, 1) : 5;

        return match ($d) {
            8, 9 => -14.0,          // Süddeutschland/Alpenvorland
            1, 2 => -10.0,          // Nord/Ost
            0, 3 => -12.0,          // Ost/Zentral
            4, 5 => -10.0,          // West/Rheinland
            default => self::NORM_AUSSENTEMP_DEFAULT_C,
        };
    }
}
