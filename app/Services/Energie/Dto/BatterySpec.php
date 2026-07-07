<?php

declare(strict_types=1);

namespace App\Services\Energie\Dto;

use App\Services\Energie\Contracts\SizingBattery;

/**
 * DTO/Adapter: mappt eine ticket-`batteries`-Zeile auf den SizingBattery-Contract.
 * Einheit p_lade_max_kw ist kW (Contract-Einheit); der Sizing-Kern rechnet gegen die
 * WR-Spalte battery_max_charge_power_w [W] mit ×1000 – hier bewusst 1:1 wie DB/Contract.
 */
final class BatterySpec implements SizingBattery
{
    public ?int $u_min_v;

    public ?int $u_max_v;

    public ?float $p_lade_max_kw;

    public ?float $i_max_a;

    public static function fromRow(array|object $row): self
    {
        $r = (array) $row;
        $o = new self;

        $o->u_min_v = self::nInt($r['min_voltage'] ?? null);
        $o->u_max_v = self::nInt($r['max_voltage'] ?? null);
        $o->p_lade_max_kw = self::nFloat($r['max_charge_power_kw'] ?? null);
        $o->i_max_a = self::nFloat($r['max_current_a'] ?? null);

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
}
