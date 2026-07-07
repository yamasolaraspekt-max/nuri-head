<?php

declare(strict_types=1);

namespace Tests\Unit\Energie;

use App\Services\Energie\Contracts\WpKennlinie;
use App\Services\Energie\Dto\HeatPumpKennlinie;
use PHPUnit\Framework\TestCase;

/**
 * DB-freier Mapping-Test für den ticket-product_heat_pump_specs -> WpKennlinie Adapter.
 */
final class HeatPumpKennlinieMappingTest extends TestCase
{
    private function kurveJson(): string
    {
        // 3-Ebenen-Schema: {"35":[[t_C,p_kW,cop],...],"45":[...],"55":[...]}
        return json_encode([
            '35' => [[-7, 6.2, 2.8], [2, 7.1, 3.6], [7, 8.0, 4.5]],
            '45' => [[-7, 5.8, 2.4], [2, 6.7, 3.1], [7, 7.6, 3.9]],
            '55' => [[-7, 5.2, 2.0], [2, 6.1, 2.6], [7, 7.0, 3.2]],
        ], JSON_THROW_ON_ERROR);
    }

    private function fullRow(): array
    {
        return [
            'leistungskurve' => $this->kurveJson(),
            'kurve_semantik' => 'en14511_nenn',
            'heizleistung_am7_w35_kw' => 6.2,
            'heizleistung_a2_w35_kw' => 7.1,
            'heizleistung_a7_w35_kw' => 8.0,
            'heizleistung_am7_w55_kw' => 5.2,
            'cop_am7_w35' => 2.8,
            'cop_a2_w35' => 3.6,
            'cop_a7_w35' => 4.5,
            'cop_am7_w55' => 2.0,
            'scop_35' => 4.6,
            'scop_55' => 3.4,
            'max_vorlauf_c' => 60.0,
            'aussen_heizen_min_c' => -20.0,
            'modulation_min_kw' => 2.0,
            'modulation_max_kw' => 9.0,
            'geraetetyp' => 'luft_wasser',
            'serie' => 'Vitocal 250',
            'kaeltemittel' => 'R290',
            'modell' => 'AWO-M-E-AC 251.A10',
            'hersteller' => 'Viessmann',
        ];
    }

    public function test_maps_full_row_with_correct_values_and_types(): void
    {
        $s = HeatPumpKennlinie::fromRow($this->fullRow());

        $this->assertSame('en14511_nenn', $s->kurve_semantik);
        $this->assertSame(6.2, $s->heizleistung_am7_w35_kw);
        $this->assertSame(7.1, $s->heizleistung_a2_w35_kw);
        $this->assertSame(8.0, $s->heizleistung_a7_w35_kw);
        $this->assertSame(5.2, $s->heizleistung_am7_w55_kw);
        $this->assertSame(2.8, $s->cop_am7_w35);
        $this->assertSame(3.6, $s->cop_a2_w35);
        $this->assertSame(4.5, $s->cop_a7_w35);
        $this->assertSame(2.0, $s->cop_am7_w55);
        $this->assertSame(4.6, $s->scop_35);
        $this->assertSame(3.4, $s->scop_55);
        $this->assertSame(60.0, $s->max_vorlauf_c);
        $this->assertSame(-20.0, $s->aussen_heizen_min_c);
        $this->assertSame(2.0, $s->modulation_min_kw);
        $this->assertSame(9.0, $s->modulation_max_kw);
        $this->assertSame('luft_wasser', $s->geraetetyp);
        $this->assertSame('Vitocal 250', $s->serie);
        $this->assertSame('R290', $s->kaeltemittel);
        $this->assertSame('AWO-M-E-AC 251.A10', $s->modell);
        $this->assertSame('Viessmann', $s->hersteller);
    }

    public function test_leistungskurve_json_decodes_to_array_with_level_keys(): void
    {
        $s = HeatPumpKennlinie::fromRow($this->fullRow());

        $this->assertIsArray($s->leistungskurve);
        $this->assertArrayHasKey('35', $s->leistungskurve);
        $this->assertArrayHasKey('45', $s->leistungskurve);
        $this->assertArrayHasKey('55', $s->leistungskurve);
        // Zeile [t_C, p_kW, cop]
        $this->assertSame([-7, 6.2, 2.8], $s->leistungskurve['35'][0]);
    }

    public function test_invalid_json_becomes_null(): void
    {
        $row = $this->fullRow();
        $row['leistungskurve'] = '{not valid json';
        $s = HeatPumpKennlinie::fromRow($row);

        $this->assertNull($s->leistungskurve);
    }

    public function test_null_and_empty_kurve_become_null(): void
    {
        $rowNull = $this->fullRow();
        $rowNull['leistungskurve'] = null;
        $this->assertNull(HeatPumpKennlinie::fromRow($rowNull)->leistungskurve);

        $rowEmpty = $this->fullRow();
        $rowEmpty['leistungskurve'] = '';
        $this->assertNull(HeatPumpKennlinie::fromRow($rowEmpty)->leistungskurve);
    }

    public function test_spalten_fallback_row_leistungskurve_null_semantik_set(): void
    {
        // wberechnung-Fall: leistungskurve=null + kurve_semantik='en14511_nenn' (Spalten-Fallback)
        $row = $this->fullRow();
        $row['leistungskurve'] = null;
        $s = HeatPumpKennlinie::fromRow($row);

        $this->assertNull($s->leistungskurve);
        $this->assertSame('en14511_nenn', $s->kurve_semantik);
        $this->assertSame(6.2, $s->heizleistung_am7_w35_kw);
    }

    public function test_nullable_numeric_and_label_columns_stay_null(): void
    {
        $s = HeatPumpKennlinie::fromRow([
            'leistungskurve' => null,
            'kurve_semantik' => null,
            'heizleistung_am7_w35_kw' => null,
            'cop_a7_w35' => null,
            'scop_35' => null,
            'aussen_heizen_min_c' => null,
            'geraetetyp' => null,
            'modell' => null,
            'hersteller' => null,
        ]);

        $this->assertNull($s->kurve_semantik);
        $this->assertNull($s->heizleistung_am7_w35_kw);
        $this->assertNull($s->cop_a7_w35);
        $this->assertNull($s->scop_35);
        $this->assertNull($s->aussen_heizen_min_c);
        $this->assertNull($s->geraetetyp);
        $this->assertNull($s->modell);
        $this->assertNull($s->hersteller);
    }

    public function test_already_decoded_array_is_passed_through(): void
    {
        $row = $this->fullRow();
        $row['leistungskurve'] = ['35' => [[-7, 6.2, 2.8]]];
        $s = HeatPumpKennlinie::fromRow($row);

        $this->assertIsArray($s->leistungskurve);
        $this->assertArrayHasKey('35', $s->leistungskurve);
    }

    public function test_accepts_stdclass_row(): void
    {
        $s = HeatPumpKennlinie::fromRow((object) $this->fullRow());

        $this->assertSame('Viessmann', $s->hersteller);
        $this->assertIsArray($s->leistungskurve);
    }

    public function test_is_instance_of_contract(): void
    {
        $this->assertInstanceOf(WpKennlinie::class, HeatPumpKennlinie::fromRow($this->fullRow()));
    }
}
