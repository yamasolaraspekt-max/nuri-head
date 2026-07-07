<?php

declare(strict_types=1);

namespace Tests\Unit\Heizlast;

use App\Services\Energie\Dto\HeatPumpKennlinie;
use App\Services\Heizlast\WpKennlinieService;
use PHPUnit\Framework\TestCase;

/**
 * Prüft den WpKennlinieService-Fallback über die EN-14511-Nenn-Spalten.
 *
 * Historie: Bis 2026-07-05 trug jede WP eine dichte 41-Punkt-Leistungskurve (leistungskurve).
 * Diese Kurven waren abgeleitet/nicht datenblatt-verifiziert und wurden entfernt
 * (kurve_semantik='en14511_nenn'); der Service rechnet seither über den Spalten-Fallback
 * (stuetzpunkteLeistung/Cop + Vorlauf-Faktor aus am7_w55).
 *
 * Erwartungswerte sind datenblatt-verifiziert (KEINE alten abgeleiteten Kurvenwerte):
 * Viessmann Vitocal 250-A 251.A10 (db-6246346, EN-14511, 09/2025) und Buderus WLW-7 MB AR (6721874368).
 *
 * Paritäts-Portierung aus wberechnung: Engine-Formeln und erwartete Ergebnisse unverändert; die
 * WP-Fixtures kommen aus dem ticket-Adapter-DTO `HeatPumpKennlinie::fromRow` (gleiche Spalten-/
 * Property-Namen, gleiche datenblatt-verifizierte Werte) statt aus dem wb-Model `Waermepumpe`.
 */
class WpKennlinieKurveTest extends TestCase
{
    /** Viessmann 251.A10 — Nennleistungs-Spalten (db-6246346). */
    private function viessmann251A10(): HeatPumpKennlinie
    {
        return HeatPumpKennlinie::fromRow([
            'hersteller' => 'Viessmann', 'modell' => '251.A10',
            'heizleistung_am7_w35_kw' => 10.0, 'cop_am7_w35' => 3.16,
            'heizleistung_a2_w35_kw' => 5.8,  'cop_a2_w35' => 4.46,
            'heizleistung_a7_w35_kw' => 7.3,  'cop_a7_w35' => 5.31,
            'heizleistung_am7_w55_kw' => 9.2, 'cop_am7_w55' => 2.1,
            'aussen_heizen_min_c' => -20.0, 'max_vorlauf_c' => 70.0,
        ]);
    }

    public function test_fallback_trifft_nenn_stuetzpunkte_w35(): void
    {
        $s = new WpKennlinieService;
        $wp = $this->viessmann251A10();

        // W35-Stützpunkte exakt aus den Datenblatt-Spalten (A-7 / A+2 / A+7).
        $this->assertEqualsWithDelta(10.0, $s->phiMax($wp, -7, 35), 0.01);
        $this->assertEqualsWithDelta(5.8, $s->phiMax($wp, 2, 35), 0.01);
        $this->assertEqualsWithDelta(7.3, $s->phiMax($wp, 7, 35), 0.01);
        $this->assertEqualsWithDelta(3.16, $s->cop($wp, -7, 35), 0.01);
        $this->assertEqualsWithDelta(4.46, $s->cop($wp, 2, 35), 0.01);
        $this->assertEqualsWithDelta(5.31, $s->cop($wp, 7, 35), 0.01);
    }

    public function test_fallback_vorlauf_w55_senkt_leistung_und_cop(): void
    {
        $s = new WpKennlinieService;
        $wp = $this->viessmann251A10();

        // W55 via Verhältnis am7_w55/am7_w35 (9,2/10) bzw. cop_am7_w55/cop_am7_w35 (2,1/3,16) bei A-7.
        $this->assertEqualsWithDelta(9.2, $s->phiMax($wp, -7, 55), 0.05);
        $this->assertEqualsWithDelta(2.1, $s->cop($wp, -7, 55), 0.05);
        $this->assertLessThan($s->phiMax($wp, -7, 35), $s->phiMax($wp, -7, 55));
        $this->assertLessThan($s->cop($wp, -7, 35), $s->cop($wp, -7, 55));
    }

    public function test_cop_steigt_mit_aussentemperatur(): void
    {
        $s = new WpKennlinieService;
        $wp = $this->viessmann251A10();

        $this->assertGreaterThan($s->cop($wp, -7, 35), $s->cop($wp, 2, 35));
        $this->assertGreaterThan($s->cop($wp, 2, 35), $s->cop($wp, 7, 35));
    }

    public function test_temperatur_interpolation_zwischen_stuetzpunkten(): void
    {
        $s = new WpKennlinieService;
        // bei 0 °C linear zwischen A-7 (10,0) und A+2 (5,8) → ~6,73 kW (W35).
        $this->assertEqualsWithDelta(6.73, $s->phiMax($this->viessmann251A10(), 0, 35), 0.05);
    }

    public function test_buderus_wlw7_nenn_a_minus7_datenblatt_korrekt(): void
    {
        $s = new WpKennlinieService;
        // Buderus WLW-7 MB AR, EN-14511-Nenn (Dok 6721874368): A-7/W35 = 6,71 kW / COP 2,36.
        $wp = HeatPumpKennlinie::fromRow([
            'hersteller' => 'Buderus', 'modell' => 'WLW-7 MB AR',
            'heizleistung_am7_w35_kw' => 6.71, 'cop_am7_w35' => 2.36,
            'heizleistung_a2_w35_kw' => 2.87, 'cop_a2_w35' => 4.06,
            'heizleistung_a7_w35_kw' => 2.84, 'cop_a7_w35' => 4.85,
            'aussen_heizen_min_c' => -22.0, 'max_vorlauf_c' => 75.0,
        ]);

        $this->assertEqualsWithDelta(6.71, $s->phiMax($wp, -7, 35), 0.01);
        $this->assertEqualsWithDelta(2.36, $s->cop($wp, -7, 35), 0.01);
    }

    public function test_unter_einsatzgrenze_keine_leistung(): void
    {
        $s = new WpKennlinieService;
        $wp = $this->viessmann251A10();

        $this->assertSame(0.0, $s->phiMax($wp, -25, 35)); // unter aussen_heizen_min_c (-20)
        $this->assertGreaterThan(0.0, $s->phiMax($wp, -20, 35));
    }
}
