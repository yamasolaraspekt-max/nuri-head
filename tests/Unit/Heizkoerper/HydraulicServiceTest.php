<?php

namespace Tests\Unit\Heizkoerper;

use App\Services\Heizkoerper\HydraulicService;
use Tests\TestCase;

/**
 * Paritäts-Test (M3 iv-a): 1:1 aus wberechnung tests/Unit/Heizlast/HydraulikServiceTest.php @ d81faa8.
 * Erwartungswerte UNVERÄNDERT — beweist byte-genauen Port von HydraulikService -> HydraulicService.
 * DB-frei (reiner Kern).
 */
class HydraulicServiceTest extends TestCase
{
    public function test_volumenstrom_kv_und_voreinstellung(): void
    {
        $s = new HydraulicService;
        // 1000 W bei 7 K Spreizung → 122,8 l/h
        $this->assertEqualsWithDelta(122.8, $s->volumenstrom(1000, 7), 1.0);
        // kv bei 100 mbar Ventil-Differenzdruck
        $this->assertEqualsWithDelta(0.39, $s->kvErforderlich(122.8, 100), 0.02);
        // kleinste Voreinstellung mit kv ≥ erforderlich
        $this->assertSame(4, $s->voreinstellung(0.39));
        $this->assertSame('offen', $s->voreinstellung(2.0));
    }

    public function test_abgleich_summiert_volumenstrom(): void
    {
        $s = new HydraulicService;
        $a = $s->abgleich([
            ['name' => 'Wohnzimmer', 'auslegungsheizlast_w' => 1000],
            ['name' => 'Bad', 'auslegungsheizlast_w' => 600],
        ], 7, 100);

        $this->assertCount(2, $a['raeume']);
        // (1000 + 600) / (1,163 · 7) ≈ 196,6 l/h
        $this->assertEqualsWithDelta(197, $a['gesamt_volumenstrom_lh'], 3);
        $this->assertEqualsWithDelta(0.20, $a['gesamt_volumenstrom_m3h'], 0.01);
        $this->assertGreaterThan(0, $a['foerderhoehe_m_ueberschlag']);
        $this->assertSame('Wohnzimmer', $a['raeume'][0]['raum']);
    }
}
