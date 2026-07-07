<?php

declare(strict_types=1);

namespace Tests\Unit\Energie;

use App\Services\Energie\Contracts\SizingModule;
use App\Services\Energie\Dto\ModuleSpec;
use PHPUnit\Framework\TestCase;

/**
 * DB-freier Mapping-Test für den ticket-product_pv_module_specs -> SizingModule Adapter.
 */
final class ModuleSpecMappingTest extends TestCase
{
    private function fullRow(): array
    {
        return [
            'voc_v' => 41.5,
            'vmpp_v' => 34.8,
            'isc_a' => 13.9,
            'impp_a' => 13.1,
            'pmpp_wp' => 445,
            'tk_voc_pct_k' => -0.25,
            'tk_isc_pct_k' => 0.045,
            'tk_pmpp_pct_k' => -0.34,
            'tk_vmpp_pct_k' => -0.29,
            'u_sys_max_v' => 1500,
            'sicherung_max_a' => 25.0,
        ];
    }

    public function test_maps_full_row_with_correct_values_and_types(): void
    {
        $s = ModuleSpec::fromRow($this->fullRow());

        $this->assertSame(41.5, $s->voc_v);
        $this->assertSame(34.8, $s->vmpp_v);
        $this->assertSame(13.9, $s->isc_a);
        $this->assertSame(13.1, $s->impp_a);
        $this->assertSame(445, $s->pmpp_wp);
        $this->assertIsInt($s->pmpp_wp);
        $this->assertSame(-0.25, $s->tk_voc_pct_k);
        $this->assertSame(0.045, $s->tk_isc_pct_k);
        $this->assertSame(-0.34, $s->tk_pmpp_pct_k);
        $this->assertSame(-0.29, $s->tk_vmpp_pct_k);
        $this->assertSame(1500, $s->u_sys_max_v);
        $this->assertIsInt($s->u_sys_max_v);
        $this->assertSame(25.0, $s->sicherung_max_a);
    }

    public function test_nullable_columns_stay_null(): void
    {
        $row = $this->fullRow();
        $row['tk_isc_pct_k'] = null;
        $row['tk_pmpp_pct_k'] = null;
        $row['tk_vmpp_pct_k'] = null;
        $row['sicherung_max_a'] = null;

        $s = ModuleSpec::fromRow($row);

        $this->assertNull($s->tk_isc_pct_k);
        $this->assertNull($s->tk_pmpp_pct_k);
        $this->assertNull($s->tk_vmpp_pct_k);
        $this->assertNull($s->sicherung_max_a);
    }

    public function test_required_int_from_numeric_string(): void
    {
        $row = $this->fullRow();
        $row['pmpp_wp'] = '445';
        $s = ModuleSpec::fromRow($row);

        $this->assertSame(445, $s->pmpp_wp);
        $this->assertIsInt($s->pmpp_wp);
    }

    public function test_accepts_stdclass_row(): void
    {
        $s = ModuleSpec::fromRow((object) $this->fullRow());

        $this->assertSame(41.5, $s->voc_v);
    }

    public function test_is_instance_of_contract(): void
    {
        $this->assertInstanceOf(SizingModule::class, ModuleSpec::fromRow($this->fullRow()));
    }
}
