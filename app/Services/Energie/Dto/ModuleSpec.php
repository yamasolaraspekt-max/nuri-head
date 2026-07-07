<?php

declare(strict_types=1);

namespace App\Services\Energie\Dto;

use App\Services\Energie\Contracts\SizingModule;

/**
 * DTO/Adapter: mappt eine ticket-`product_pv_module_specs`-Zeile auf den SizingModule-Contract.
 * Spaltennamen sind 1:1 identisch mit den wb-Property-Namen (trivialer Adapter, nur Typcasts).
 */
final class ModuleSpec implements SizingModule
{
    public float $voc_v;

    public float $vmpp_v;

    public float $isc_a;

    public float $impp_a;

    public int $pmpp_wp;

    public float $tk_voc_pct_k;

    public ?float $tk_isc_pct_k;

    public ?float $tk_pmpp_pct_k;

    public ?float $tk_vmpp_pct_k;

    public int $u_sys_max_v;

    public ?float $sicherung_max_a;

    public static function fromRow(array|object $row): self
    {
        $r = (array) $row;
        $o = new self;

        // Pflichtfelder – hart casten
        $o->voc_v = (float) ($r['voc_v'] ?? 0);
        $o->vmpp_v = (float) ($r['vmpp_v'] ?? 0);
        $o->isc_a = (float) ($r['isc_a'] ?? 0);
        $o->impp_a = (float) ($r['impp_a'] ?? 0);
        $o->pmpp_wp = (int) ($r['pmpp_wp'] ?? 0);
        $o->tk_voc_pct_k = (float) ($r['tk_voc_pct_k'] ?? 0);
        $o->u_sys_max_v = (int) ($r['u_sys_max_v'] ?? 0);

        // Nullable-Felder – null-erhaltend
        $o->tk_isc_pct_k = self::nFloat($r['tk_isc_pct_k'] ?? null);
        $o->tk_pmpp_pct_k = self::nFloat($r['tk_pmpp_pct_k'] ?? null);
        $o->tk_vmpp_pct_k = self::nFloat($r['tk_vmpp_pct_k'] ?? null);
        $o->sicherung_max_a = self::nFloat($r['sicherung_max_a'] ?? null);

        return $o;
    }

    private static function nFloat(mixed $v): ?float
    {
        return $v === null ? null : (float) $v;
    }
}
