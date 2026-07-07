<?php

declare(strict_types=1);

namespace Tests\Unit\Heizlast;

use App\Services\Energie\Dto\HeatPumpKennlinie;
use App\Services\Heizlast\BivalenzService;
use App\Services\Heizlast\KlimaBinService;
use App\Services\Heizlast\WpKennlinieService;
use PHPUnit\Framework\TestCase;

class BivalenzServiceTest extends TestCase
{
    public function test_berechnet_bivalenz_jaz_und_stromaufteilung_mit_ticket_services(): void
    {
        $wp = HeatPumpKennlinie::fromRow([
            'hersteller' => 'Test',
            'serie' => 'T',
            'modell' => 'T-10',
            'heizleistung_am7_w35_kw' => 8.0,
            'cop_am7_w35' => 2.8,
            'heizleistung_a2_w35_kw' => 9.0,
            'cop_a2_w35' => 3.8,
            'heizleistung_a7_w35_kw' => 10.0,
            'cop_a7_w35' => 5.0,
            'heizleistung_am7_w55_kw' => 7.0,
            'cop_am7_w55' => 1.9,
            'aussen_heizen_min_c' => -20.0,
            'max_vorlauf_c' => 70.0,
        ]);

        $service = new BivalenzService(new KlimaBinService, new WpKennlinieService);

        $result = $service->berechne(
            wp: $wp,
            phiHlKw: 9.0,
            qHeizKwh: 18000.0,
            qWwKwh: 2500.0,
            wwMitWp: true,
            vorlaufC: 45.0,
            plz: '60311',
        );

        $this->assertArrayHasKey('bivalenzpunkt_c', $result);
        $this->assertGreaterThan(1.0, $result['jaz']);
        $this->assertGreaterThan(0, $result['strom']['gesamt_kwh']);
        $this->assertGreaterThan(0, $result['waerme']['q_total_kwh']);
        $this->assertSame('mitte', $result['klima']['zone']);
    }
}
