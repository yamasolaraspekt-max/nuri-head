<?php

declare(strict_types=1);

namespace App\Services\Energie\Dto;

use App\Services\Energie\Contracts\SizingInverter;

/**
 * DTO/Adapter: mappt eine ticket-`inverters`-Zeile auf die wberechnung-Property-Namen
 * des SizingInverter-Contracts. Reiner Struktur-Adapter (Spaltenname → wb-Property),
 * keine Fachlogik, keine Ableitung.
 */
final class InverterSpec implements SizingInverter
{
    public int $u_dc_max_v;

    public ?int $u_dc_betrieb_max_v;

    public int $u_dc_start_v;

    public int $u_mppt_min_v;

    public int $u_mppt_max_v;

    public int $anzahl_mppt;

    public float $i_dc_max_mppt_a;

    public ?float $i_dc_max_string_a;

    public ?float $i_sc_max_mppt_a;

    public int $p_ac_nenn_w;

    public int $p_ac_max_va;

    public int $p_dc_max_w;

    public ?float $max_dc_ac_ratio;

    public ?int $max_array_wp;

    public int|string $phasen;

    public bool $ist_hybrid;

    public bool $na_schutz_integriert;

    public bool $vde4105_konform;

    public ?string $wirkleistungsbegrenzung;

    public bool $steuerbar_14a;

    public ?string $schnittstelle;

    public ?int $temp_betrieb_min_c;

    public ?int $temp_betrieb_max_c;

    public ?int $temp_derating_ab_c;

    public ?int $u_bat_min_v;

    public ?int $u_bat_max_v;

    public ?int $p_bat_lade_max_w;

    public ?float $i_bat_max_a;

    public bool $eps_faehig;

    public ?float $wirkungsgrad_euro_pct;

    /**
     * Baut das DTO aus einer ticket-`inverters`-Zeile (array oder stdClass).
     */
    public static function fromRow(array|object $row): self
    {
        $r = (array) $row;
        $o = new self;

        // DC-Spannungen / MPPT (Pflicht)
        $o->u_dc_max_v = (int) ($r['max_input_voltage'] ?? 0);
        $o->u_dc_betrieb_max_v = self::nInt($r['dc_operating_max_voltage'] ?? null);
        $o->u_dc_start_v = (int) ($r['dc_startup_voltage'] ?? 0);
        $o->u_mppt_min_v = (int) ($r['min_mpp_voltage'] ?? 0);
        $o->u_mppt_max_v = (int) ($r['max_mpp_voltage'] ?? 0);
        $o->anzahl_mppt = (int) ($r['num_mpp_trackers'] ?? 0);

        // DC-Ströme
        $o->i_dc_max_mppt_a = (float) ($r['max_input_current_per_mpp'] ?? 0);
        $o->i_dc_max_string_a = self::nFloat($r['max_input_current'] ?? null);
        $o->i_sc_max_mppt_a = self::nFloat($r['max_short_circuit_current_per_mpp'] ?? null);

        // Leistungen (Pflicht)
        $o->p_ac_nenn_w = (int) ($r['ac_nominal_power'] ?? 0);
        $o->p_ac_max_va = (int) ($r['max_ac_power'] ?? 0);
        $o->p_dc_max_w = (int) ($r['max_dc_power'] ?? 0);
        $o->max_dc_ac_ratio = self::nFloat($r['max_dc_ac_ratio'] ?? null);
        $o->max_array_wp = self::nInt($r['max_array_power_wp'] ?? null);

        // Phasen: numerisch -> int, sonst string durchreichen
        $phasen = $r['num_phases'] ?? null;
        $o->phasen = is_numeric($phasen) ? (int) $phasen : (string) $phasen;

        // Booleans (aus tinyint 0/1)
        $o->ist_hybrid = (bool) ($r['is_hybrid'] ?? false);
        $o->na_schutz_integriert = (bool) ($r['integrated_grid_protection'] ?? false);
        $o->vde4105_konform = (bool) ($r['vde4105_compliant'] ?? false);
        $o->steuerbar_14a = (bool) ($r['controllable_14a'] ?? false);
        $o->eps_faehig = (bool) ($r['eps_capable'] ?? false);

        // Regulatorik / Schnittstelle (nullable string)
        $o->wirkleistungsbegrenzung = self::nStr($r['active_power_limit'] ?? null);
        $o->schnittstelle = self::nStr($r['control_interface'] ?? null);

        // Temperatur
        $o->temp_betrieb_min_c = self::nInt($r['operating_temp_min_c'] ?? null);
        $o->temp_betrieb_max_c = self::nInt($r['operating_temp_max_c'] ?? null);
        $o->temp_derating_ab_c = self::nInt($r['temp_derating_from_c'] ?? null);

        // Batterie-Kopplung
        $o->u_bat_min_v = self::nInt($r['battery_min_voltage'] ?? null);
        $o->u_bat_max_v = self::nInt($r['battery_max_voltage'] ?? null);
        $o->p_bat_lade_max_w = self::nInt($r['battery_max_charge_power_w'] ?? null);
        $o->i_bat_max_a = self::nFloat($r['battery_max_current_a'] ?? null);

        // Euro-Wirkungsgrad: ticket hat KEINE Euro-Wirkungsgrad-Spalte (nur die Kurve
        // efficiency_0..efficiency_100) und der Sizing-Kern liest wirkungsgrad_euro_pct nicht.
        // Ableitung aus der Kurve erfolgt bewusst NICHT (nichts erfinden) -> fest null.
        $o->wirkungsgrad_euro_pct = null;

        return $o;
    }

    private static function nInt(mixed $v): ?int
    {
        return $v === null ? null : (int) $v;
    }

    private static function nFloat(mixed $v): ?float
    {
        return $v === null ? null : (float) $v;
    }

    private static function nStr(mixed $v): ?string
    {
        return $v === null ? null : (string) $v;
    }
}
