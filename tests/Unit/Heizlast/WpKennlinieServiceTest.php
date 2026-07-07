<?php

declare(strict_types=1);

namespace Tests\Unit\Heizlast;

use App\Services\Energie\Dto\HeatPumpKennlinie;
use App\Services\Heizlast\WpKennlinieService;
use PHPUnit\Framework\TestCase;

/**
 * Paritäts-Portierung aus wberechnung: Engine-Formeln und erwartete Ergebnisse unverändert.
 * Die WP-Fixture wird statt aus dem wb-Eloquent-Model `Waermepumpe` aus dem ticket-Adapter-DTO
 * `HeatPumpKennlinie::fromRow` gebaut (gleiche Spalten-/Property-Namen, gleiche Datenblatt-Werte).
 */
class WpKennlinieServiceTest extends TestCase
{
    private function geraet(): HeatPumpKennlinie
    {
        return HeatPumpKennlinie::fromRow([
            'hersteller' => 'Test', 'modell' => 'T-10',
            'heizleistung_am7_w35_kw' => 8.0, 'cop_am7_w35' => 2.8,
            'heizleistung_a2_w35_kw' => 9.0, 'cop_a2_w35' => 3.8,
            'heizleistung_a7_w35_kw' => 10.0, 'cop_a7_w35' => 5.0,
            'heizleistung_am7_w55_kw' => 7.0, 'cop_am7_w55' => 1.9,
            'aussen_heizen_min_c' => -20.0, 'max_vorlauf_c' => 70.0,
        ]);
    }

    public function test_stuetzpunkte_werden_bei_w35_exakt_getroffen(): void
    {
        $s = new WpKennlinieService;
        $wp = $this->geraet();

        $this->assertEqualsWithDelta(8.0, $s->phiMax($wp, -7, 35), 0.01);
        $this->assertEqualsWithDelta(10.0, $s->phiMax($wp, 7, 35), 0.01);
        $this->assertEqualsWithDelta(2.8, $s->cop($wp, -7, 35), 0.01);
        $this->assertEqualsWithDelta(5.0, $s->cop($wp, 7, 35), 0.01);
    }

    public function test_cop_steigt_mit_aussentemperatur(): void
    {
        $s = new WpKennlinieService;
        $wp = $this->geraet();

        $this->assertGreaterThan($s->cop($wp, -7, 35), $s->cop($wp, 2, 35));
        $this->assertGreaterThan($s->cop($wp, 2, 35), $s->cop($wp, 7, 35));
    }

    public function test_hoeherer_vorlauf_senkt_cop(): void
    {
        $s = new WpKennlinieService;
        $wp = $this->geraet();

        $this->assertLessThan($s->cop($wp, -7, 35), $s->cop($wp, -7, 55));
        // Verhältnis W55/W35 bei A-7: 1.9/2.8 → COP(55 °C) ≈ 1.9
        $this->assertEqualsWithDelta(1.9, $s->cop($wp, -7, 55), 0.05);
    }

    public function test_unter_einsatzgrenze_keine_leistung(): void
    {
        $wp = $this->geraet();
        $wp->aussen_heizen_min_c = -10.0;
        $s = new WpKennlinieService;

        $this->assertSame(0.0, $s->phiMax($wp, -15, 35));
        $this->assertGreaterThan(0.0, $s->phiMax($wp, -7, 35));
    }
}
