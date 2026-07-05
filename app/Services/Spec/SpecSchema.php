<?php

namespace App\Services\Spec;

/**
 * Kanonische Regelquelle des Geräte-Spec-Standards (docs/spec-import/00-spec-standard.md §3).
 *
 * EINE deklarative Definition je Gerätetyp — genutzt von SpecImportService (Validierung beim Schreiben)
 * UND (ab Baustufe 3) SpecEligibilityService (Bewertung des Bestands). Nicht zwei Regelwerke.
 *
 * Feld-Constraint: ['typ'=>'num'|'bool'|'str', 'min'=>?, 'max'=>?, 'req'=>bool]. Einheit steckt im
 * Feldnamen (V3). Grenzen = Plausibilität (V2), nicht Datenblatt-Wahrheit.
 */
class SpecSchema
{
    /** @return list<string> */
    public static function types(): array
    {
        return ['waermepumpe', 'pv_modul', 'wechselrichter', 'batterie'];
    }

    public static function isType(string $typ): bool
    {
        return in_array($typ, self::types(), true);
    }

    /** Vollständige Regeldefinition je Typ. */
    public static function definition(string $typ): array
    {
        return match ($typ) {
            'waermepumpe' => self::waermepumpe(),
            'pv_modul' => self::pvModul(),
            'wechselrichter' => self::wechselrichter(),
            'batterie' => self::batterie(),
            default => throw new \InvalidArgumentException("Unbekannter Gerätetyp: {$typ}"),
        };
    }

    /**
     * Ziel-Mapping für den Write (Baustufe 2): Spec-Tabelle je Typ + Herleitung der products- und
     * Spec-Extra-Spalten aus den kanonischen Blöcken (Pfad "block.feld"). Fachdaten, deren Name eine
     * Spalte der Spec-Tabelle ist, werden 1:1 geschrieben; hier nur die Nicht-1:1-Fälle.
     */
    public static function ziel(string $typ): array
    {
        return match ($typ) {
            'waermepumpe' => [
                'spec_tabelle' => 'product_heat_pump_specs',
                'category' => 'Wärmepumpe',
                'products' => [
                    'heatpump_type' => 'fachdaten.bauart', 'refrigerant' => 'fachdaten.kaeltemittel',
                    'phase_count' => 'fachdaten.phasen', 'scop' => 'fachdaten.scop_35', 'noise_level_db' => 'fachdaten.schall_volllast_db',
                ],
                'spec_extra' => ['geraetetyp' => 'fachdaten.bauart', 'serie' => 'identitaet.serie', 'kaeltemittel' => 'fachdaten.kaeltemittel'],
            ],
            'pv_modul' => [
                'spec_tabelle' => 'product_pv_module_specs', 'category' => 'PV-Modul', 'products' => [], 'spec_extra' => [],
            ],
            'wechselrichter' => [
                'spec_tabelle' => 'inverters', 'category' => 'Wechselrichter',
                'products' => ['phase_count' => 'fachdaten.num_phases'], 'spec_extra' => [],
            ],
            'batterie' => [
                'spec_tabelle' => 'batteries', 'category' => 'Batterie', 'products' => [], 'spec_extra' => [],
            ],
            default => throw new \InvalidArgumentException("Unbekannter Gerätetyp: {$typ}"),
        };
    }

    // ---- Constraint-Helfer ----
    private static function num($min, $max, bool $req = false): array
    {
        return ['typ' => 'num', 'min' => $min, 'max' => $max, 'req' => $req];
    }

    private static function boolF(bool $req = false): array
    {
        return ['typ' => 'bool', 'req' => $req];
    }

    private static function str(bool $req = false): array
    {
        return ['typ' => 'str', 'req' => $req];
    }

    // ---- Typ-Definitionen ----
    private static function waermepumpe(): array
    {
        return [
            'identitaet' => ['hersteller', 'modell', 'kategorie'],
            'fachdaten' => [
                'heizleistung_am7_w35_kw' => self::num(1, 50),
                'heizleistung_a2_w35_kw' => self::num(1, 50),
                'heizleistung_a7_w35_kw' => self::num(1, 50),
                'heizleistung_am7_w55_kw' => self::num(1, 50),
                'cop_am7_w35' => self::num(1, 7),
                'cop_a2_w35' => self::num(1, 7),
                'cop_a7_w35' => self::num(1, 7),
                'cop_am7_w55' => self::num(1, 7),
                'scop_35' => self::num(1, 7),
                'scop_55' => self::num(1, 7),
                'modulation_min_kw' => self::num(0, 50),
                'modulation_max_kw' => self::num(0, 50),
                'max_vorlauf_c' => self::num(30, 90),
                'aussen_heizen_min_c' => self::num(-30, 5, true),
                'bauart' => self::str(),
                'kaeltemittel' => self::str(),
                'phasen' => self::num(1, 3),
                'schall_volllast_db' => self::num(20, 80),
            ],
            // V (alternativ-Pflicht): entweder die 6 W35-Stützpunkte ODER eine dichte leistungskurve
            'pflicht_alternativ' => [
                ['heizleistung_am7_w35_kw', 'heizleistung_a2_w35_kw', 'heizleistung_a7_w35_kw', 'cop_am7_w35', 'cop_a2_w35', 'cop_a7_w35'],
                ['leistungskurve'],
            ],
            // V1: kW + COP eines Punkts paarweise (beide gesetzt oder beide null) — keine Misch-/Halbpunkte
            'paare' => [
                ['heizleistung_am7_w35_kw', 'cop_am7_w35'],
                ['heizleistung_a2_w35_kw', 'cop_a2_w35'],
                ['heizleistung_a7_w35_kw', 'cop_a7_w35'],
                ['heizleistung_am7_w55_kw', 'cop_am7_w55'],
            ],
            // V4: kurve_semantik Pflicht, sobald Spalten-Fachdaten vorliegen
            'semantik_pflicht' => ['kurve_semantik'],
            'semantik_erlaubt' => ['kurve_semantik', 'leistungskurve'],
            'herkunft' => ['verifikations_status', 'datenblatt_referenz'],
        ];
    }

    private static function pvModul(): array
    {
        return [
            'identitaet' => ['hersteller', 'modell', 'kategorie'],
            'fachdaten' => [
                'voc_v' => self::num(0, 1500, true),
                'vmpp_v' => self::num(0, 1500, true),
                'isc_a' => self::num(0, 100, true),
                'impp_a' => self::num(0, 100, true),
                'pmpp_wp' => self::num(50, 1000, true),
                'tk_voc_pct_k' => self::num(-1, 0, true),
                'tk_isc_pct_k' => self::num(-1, 1),
                'tk_pmpp_pct_k' => self::num(-1, 0),
                'tk_vmpp_pct_k' => self::num(-1, 1),
                'u_sys_max_v' => self::num(0, 1500, true),
                'sicherung_max_a' => self::num(0, 100),
            ],
            'herkunft' => ['verifikations_status', 'datenblatt_referenz'],
        ];
    }

    private static function wechselrichter(): array
    {
        return [
            'identitaet' => ['hersteller', 'modell', 'kategorie'],
            'fachdaten' => [
                'max_input_voltage' => self::num(0, 1500, true),
                'min_mpp_voltage' => self::num(0, 1500, true),
                'max_mpp_voltage' => self::num(0, 1500, true),
                'dc_startup_voltage' => self::num(0, 1500, true),
                'num_mpp_trackers' => self::num(1, 12, true),
                'max_input_current_per_mpp' => self::num(0, 100, true),
                'max_short_circuit_current_per_mpp' => self::num(0, 100),
                'ac_nominal_power' => self::num(0, 100000, true),
                'max_ac_power' => self::num(0, 100000, true),
                'max_dc_power' => self::num(0, 200000, true),
                'num_phases' => self::num(1, 3, true),
                'integrated_grid_protection' => self::boolF(true),
                'vde4105_compliant' => self::boolF(true),
                'is_hybrid' => self::boolF(true),
                'max_dc_ac_ratio' => self::num(1, 2),
                'max_array_power_wp' => self::num(0, 200000),
                'eps_capable' => self::boolF(),
                'battery_min_voltage' => self::num(0, 1500),
                'battery_max_voltage' => self::num(0, 1500),
                'battery_max_charge_power_w' => self::num(0, 100000),
                'battery_max_current_a' => self::num(0, 100),
                'operating_temp_min_c' => self::num(-40, 80),
                'operating_temp_max_c' => self::num(-40, 80),
                'temp_derating_from_c' => self::num(0, 80),
                'active_power_limit' => self::str(),
                'controllable_14a' => self::boolF(),
                'control_interface' => self::str(),
                'wirkungsgrad_euro_pct' => self::num(80, 100),
            ],
            'herkunft' => ['verifikations_status', 'datenblatt_referenz'],
        ];
    }

    private static function batterie(): array
    {
        return [
            'identitaet' => ['hersteller', 'modell', 'kategorie'],
            'fachdaten' => [
                'min_voltage' => self::num(0, 1500, true),
                'max_voltage' => self::num(0, 1500, true),
                'max_charge_power_kw' => self::num(0, 100, true),
                'max_current_a' => self::num(0, 1000, true),
                'nominal_voltage' => self::num(0, 1500),
                'battery_type' => self::str(),
                'kapazitaet_kwh' => self::num(0, 1000),
                'kapazitaet_nutzbar_kwh' => self::num(0, 1000),
                'entladeleistung_kw' => self::num(0, 100),
                'dod_pct' => self::num(0, 100),
                'zyklen' => self::num(0, 20000),
                'operating_temp_min_c' => self::num(-40, 80),
                'operating_temp_max_c' => self::num(-40, 80),
            ],
            'herkunft' => ['verifikations_status', 'datenblatt_referenz'],
        ];
    }
}
