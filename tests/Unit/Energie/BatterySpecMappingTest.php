<?php

declare(strict_types=1);

namespace Tests\Unit\Energie;

use App\Services\Energie\Contracts\SizingBattery;
use App\Services\Energie\Dto\BatterySpec;
use PHPUnit\Framework\TestCase;

/**
 * DB-freier Mapping-Test für den ticket-batteries -> SizingBattery Adapter.
 */
final class BatterySpecMappingTest extends TestCase
{
    private function fullRow(): array
    {
        return [
            'min_voltage' => 90,
            'max_voltage' => 550,
            'max_charge_power_kw' => 5.0,
            'max_current_a' => 25.0,
        ];
    }

    public function test_maps_full_row_with_correct_values_and_types(): void
    {
        $s = BatterySpec::fromRow($this->fullRow());

        $this->assertSame(90, $s->u_min_v);
        $this->assertIsInt($s->u_min_v);
        $this->assertSame(550, $s->u_max_v);
        $this->assertSame(5.0, $s->p_lade_max_kw);
        $this->assertIsFloat($s->p_lade_max_kw);
        $this->assertSame(25.0, $s->i_max_a);
    }

    public function test_nullable_columns_stay_null(): void
    {
        $row = [
            'min_voltage' => null,
            'max_voltage' => null,
            'max_charge_power_kw' => null,
            'max_current_a' => null,
        ];

        $s = BatterySpec::fromRow($row);

        $this->assertNull($s->u_min_v);
        $this->assertNull($s->u_max_v);
        $this->assertNull($s->p_lade_max_kw);
        $this->assertNull($s->i_max_a);
    }

    public function test_missing_keys_default_to_null(): void
    {
        $s = BatterySpec::fromRow([]);

        $this->assertNull($s->u_min_v);
        $this->assertNull($s->u_max_v);
        $this->assertNull($s->p_lade_max_kw);
        $this->assertNull($s->i_max_a);
    }

    public function test_accepts_stdclass_row(): void
    {
        $s = BatterySpec::fromRow((object) $this->fullRow());

        $this->assertSame(90, $s->u_min_v);
    }

    public function test_is_instance_of_contract(): void
    {
        $this->assertInstanceOf(SizingBattery::class, BatterySpec::fromRow($this->fullRow()));
    }
}
