<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Services\Energie\Dto\BatterySpec;
use App\Services\Energie\Dto\HeatPumpKennlinie;
use App\Services\Energie\Dto\InverterSpec;
use App\Services\Energie\Dto\ModuleSpec;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Liest tickets Produktkatalog (nur SELECT) und liefert die für die Auslegungs-Engine
 * gemappten DTO-Collections. Dünn, ohne Nebenwirkungen – reiner Lese-Adapter.
 */
class CatalogDeviceRepository
{
    /** @return Collection<int, InverterSpec> */
    public function inverters(): Collection
    {
        return DB::table('inverters')
            ->get()
            ->map(fn ($r) => InverterSpec::fromRow($r));
    }

    /** @return Collection<int, ModuleSpec> */
    public function modules(): Collection
    {
        return DB::table('product_pv_module_specs')
            ->get()
            ->map(fn ($r) => ModuleSpec::fromRow($r));
    }

    /** @return Collection<int, BatterySpec> */
    public function batteries(): Collection
    {
        return DB::table('batteries')
            ->get()
            ->map(fn ($r) => BatterySpec::fromRow($r));
    }

    /** @return Collection<int, HeatPumpKennlinie> */
    public function heatPumps(): Collection
    {
        return DB::table('product_heat_pump_specs as s')
            ->leftJoin('products as p', 'p.id', '=', 's.product_id')
            ->leftJoin('brands as b', 'b.id', '=', 'p.brand_id')
            ->select('s.*', 'p.model as modell', 'b.name as hersteller')
            ->get()
            ->map(fn ($r) => HeatPumpKennlinie::fromRow($r));
    }
}
