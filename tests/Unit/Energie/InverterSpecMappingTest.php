<?php

declare(strict_types=1);

namespace Tests\Unit\Energie;

use App\Services\Energie\Contracts\SizingInverter;
use App\Services\Energie\Dto\InverterSpec;
use PHPUnit\Framework\TestCase;

/**
 * DB-freier Mapping-Test für den ticket-inverters -> SizingInverter Adapter.
 */
final class InverterSpecMappingTest extends TestCase
{
    /** Repräsentative, voll befüllte ticket-inverters-Zeile (tinyint für bool wie in MySQL). */
    private function fullRow(): array
    {
        return [
            'max_input_voltage' => 1000,
            'dc_operating_max_voltage' => 950,
            'dc_startup_voltage' => 120,
            'min_mpp_voltage' => 140,
            'max_mpp_voltage' => 850,
            'num_mpp_trackers' => 2,
            'max_input_current_per_mpp' => 16.5,
            'max_input_current' => 33.0,
            'max_short_circuit_current_per_mpp' => 22.4,
            'ac_nominal_power' => 10000,
            'max_ac_power' => 11000,
            'max_dc_power' => 15000,
            'max_dc_ac_ratio' => 1.5,
            'max_array_power_wp' => 15000,
            'num_phases' => 3,
            'is_hybrid' => 1,
            'integrated_grid_protection' => 1,
            'vde4105_compliant' => 1,
            'active_power_limit' => '70%',
            'controllable_14a' => 1,
            'control_interface' => 'Modbus TCP',
            'operating_temp_min_c' => -25,
            'operating_temp_max_c' => 60,
            'temp_derating_from_c' => 45,
            'battery_min_voltage' => 80,
            'battery_max_voltage' => 600,
            'battery_max_charge_power_w' => 5000,
            'battery_max_current_a' => 25.0,
            'eps_capable' => 1,
        ];
    }

    public function test_maps_full_row_with_correct_values_and_types(): void
    {
        $s = InverterSpec::fromRow($this->fullRow());

        $this->assertSame(1000, $s->u_dc_max_v);
        $this->assertSame(950, $s->u_dc_betrieb_max_v);
        $this->assertSame(120, $s->u_dc_start_v);
        $this->assertSame(140, $s->u_mppt_min_v);
        $this->assertSame(850, $s->u_mppt_max_v);
        $this->assertSame(2, $s->anzahl_mppt);

        $this->assertSame(16.5, $s->i_dc_max_mppt_a);
        $this->assertSame(33.0, $s->i_dc_max_string_a);
        $this->assertSame(22.4, $s->i_sc_max_mppt_a);

        $this->assertSame(10000, $s->p_ac_nenn_w);
        $this->assertSame(11000, $s->p_ac_max_va);
        $this->assertSame(15000, $s->p_dc_max_w);
        $this->assertSame(1.5, $s->max_dc_ac_ratio);
        $this->assertSame(15000, $s->max_array_wp);

        $this->assertSame(3, $s->phasen);
        $this->assertIsInt($s->phasen);

        $this->assertTrue($s->ist_hybrid);
        $this->assertTrue($s->na_schutz_integriert);
        $this->assertTrue($s->vde4105_konform);
        $this->assertTrue($s->steuerbar_14a);
        $this->assertTrue($s->eps_faehig);

        $this->assertSame('70%', $s->wirkleistungsbegrenzung);
        $this->assertSame('Modbus TCP', $s->schnittstelle);

        $this->assertSame(-25, $s->temp_betrieb_min_c);
        $this->assertSame(60, $s->temp_betrieb_max_c);
        $this->assertSame(45, $s->temp_derating_ab_c);

        $this->assertSame(80, $s->u_bat_min_v);
        $this->assertSame(600, $s->u_bat_max_v);
        $this->assertSame(5000, $s->p_bat_lade_max_w);
        $this->assertSame(25.0, $s->i_bat_max_a);
    }

    public function test_wirkungsgrad_euro_pct_is_always_null(): void
    {
        // ticket hat keine Euro-Wirkungsgrad-Spalte -> Adapter setzt fest null.
        $row = $this->fullRow();
        $row['wirkungsgrad_euro_pct'] = 98.5; // selbst wenn irrtümlich gesetzt: ignoriert
        $s = InverterSpec::fromRow($row);

        $this->assertNull($s->wirkungsgrad_euro_pct);
    }

    public function test_nullable_columns_stay_null(): void
    {
        $row = [
            'max_input_voltage' => 600,
            'dc_startup_voltage' => 100,
            'min_mpp_voltage' => 120,
            'max_mpp_voltage' => 500,
            'num_mpp_trackers' => 1,
            'max_input_current_per_mpp' => 12.0,
            'ac_nominal_power' => 5000,
            'max_ac_power' => 5000,
            'max_dc_power' => 6000,
            'num_phases' => 1,
            // alle nullable wb-Felder fehlen / sind null:
            'dc_operating_max_voltage' => null,
            'max_input_current' => null,
            'max_short_circuit_current_per_mpp' => null,
            'max_dc_ac_ratio' => null,
            'max_array_power_wp' => null,
            'active_power_limit' => null,
            'control_interface' => null,
            'operating_temp_min_c' => null,
            'operating_temp_max_c' => null,
            'temp_derating_from_c' => null,
            'battery_min_voltage' => null,
            'battery_max_voltage' => null,
            'battery_max_charge_power_w' => null,
            'battery_max_current_a' => null,
        ];

        $s = InverterSpec::fromRow($row);

        $this->assertNull($s->u_dc_betrieb_max_v);
        $this->assertNull($s->i_dc_max_string_a);
        $this->assertNull($s->i_sc_max_mppt_a);
        $this->assertNull($s->max_dc_ac_ratio);
        $this->assertNull($s->max_array_wp);
        $this->assertNull($s->wirkleistungsbegrenzung);
        $this->assertNull($s->schnittstelle);
        $this->assertNull($s->temp_betrieb_min_c);
        $this->assertNull($s->temp_betrieb_max_c);
        $this->assertNull($s->temp_derating_ab_c);
        $this->assertNull($s->u_bat_min_v);
        $this->assertNull($s->u_bat_max_v);
        $this->assertNull($s->p_bat_lade_max_w);
        $this->assertNull($s->i_bat_max_a);
    }

    public function test_phasen_string_numeric_becomes_int(): void
    {
        $row = $this->fullRow();
        $row['num_phases'] = '1';
        $s = InverterSpec::fromRow($row);

        $this->assertSame(1, $s->phasen);
        $this->assertIsInt($s->phasen);
    }

    public function test_phasen_non_numeric_stays_string(): void
    {
        $row = $this->fullRow();
        $row['num_phases'] = 'dreiphasig';
        $s = InverterSpec::fromRow($row);

        $this->assertSame('dreiphasig', $s->phasen);
        $this->assertIsString($s->phasen);
    }

    public function test_booleans_from_tinyint_zero(): void
    {
        $row = $this->fullRow();
        $row['is_hybrid'] = 0;
        $row['integrated_grid_protection'] = 0;
        $row['vde4105_compliant'] = 0;
        $row['controllable_14a'] = 0;
        $row['eps_capable'] = 0;

        $s = InverterSpec::fromRow($row);

        $this->assertFalse($s->ist_hybrid);
        $this->assertFalse($s->na_schutz_integriert);
        $this->assertFalse($s->vde4105_konform);
        $this->assertFalse($s->steuerbar_14a);
        $this->assertFalse($s->eps_faehig);
    }

    public function test_accepts_stdclass_row(): void
    {
        $s = InverterSpec::fromRow((object) $this->fullRow());

        $this->assertSame(1000, $s->u_dc_max_v);
        $this->assertTrue($s->ist_hybrid);
    }

    public function test_is_instance_of_contract(): void
    {
        $this->assertInstanceOf(SizingInverter::class, InverterSpec::fromRow($this->fullRow()));
    }
}
