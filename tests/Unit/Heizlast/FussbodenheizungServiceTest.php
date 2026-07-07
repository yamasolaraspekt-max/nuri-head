<?php

namespace Tests\Unit\Heizlast;

use App\Services\Heizlast\FussbodenheizungService;
use PHPUnit\Framework\TestCase;

class FussbodenheizungServiceTest extends TestCase
{
    private function fbh(array $over = []): array
    {
        return array_merge(['rohr_aussen_mm' => 16, 'verlegeabstand_mm' => 100, 'bodenbelag_r' => 0.0, 'estrich_ueber_mm' => 45], $over);
    }

    public function test_spezifische_leistung_und_geometrie(): void
    {
        $s = new FussbodenheizungService;
        // 16 mm, 10 cm, Fliesen, 45 mm Estrich, 20 m², 1500 W, 20 °C, VL 35/Spreizung 5 K
        $a = $s->analyse($this->fbh(), 20.0, 1500.0, 20.0, 35.0, 5.0, 29.0);

        $this->assertEqualsWithDelta(88.6, $a['q_eff_w_m2'], 3.0);     // ≈ 89 W/m² bei 35 °C
        $this->assertEqualsWithDelta(1772, $a['q_real_w'], 80);
        $this->assertSame(10.0, $a['laenge_pro_m2']);                  // 1 / 0,10 m
        $this->assertSame(200, $a['gesamtlaenge_m']);                  // 20 m² · 10 m/m²
        $this->assertSame(2, $a['heizkreise']);                        // 200 m / max 100 m (16 mm)
        $this->assertSame('gruen', $a['status']);                      // deckt 1500 W
    }

    public function test_min_vorlauftemperatur_ist_niedrig(): void
    {
        $s = new FussbodenheizungService;
        // FBH deckt 1500 W in 20 m² schon bei ~33 °C → niedrige Vorlauftemp (JAZ-Vorteil)
        $minVl = $s->analyse($this->fbh(), 20.0, 1500.0, 20.0, 35.0, 5.0, 29.0)['min_vorlauf_c'];
        $this->assertNotNull($minVl);
        $this->assertEqualsWithDelta(33.0, $minVl, 2.5);
        $this->assertLessThan(40, $minVl);
    }

    public function test_oberflaechengrenze_begrenzt_die_leistung(): void
    {
        $s = new FussbodenheizungService;
        // 150 W/m² Bedarf übersteigt die Grenze (29 °C → ~97 W/m²) → unzureichend
        $a = $s->analyse($this->fbh(), 20.0, 3000.0, 20.0, 35.0, 5.0, 29.0);

        $this->assertTrue($a['oberflaeche_ueberschritten']);
        $this->assertNull($a['min_vorlauf_c']);
        $this->assertSame('rot', $a['status']);
        $this->assertLessThanOrEqual($a['q_max_oberflaeche_w_m2'] + 0.1, $a['q_eff_w_m2']);
    }

    public function test_groesserer_verlegeabstand_senkt_die_leistung(): void
    {
        $s = new FussbodenheizungService;
        $eng = $s->analyse($this->fbh(['verlegeabstand_mm' => 100]), 20.0, 1500.0, 20.0, 35.0, 5.0, 29.0);
        $weit = $s->analyse($this->fbh(['verlegeabstand_mm' => 250]), 20.0, 1500.0, 20.0, 35.0, 5.0, 29.0);

        $this->assertGreaterThan($weit['q_eff_w_m2'], $eng['q_eff_w_m2']);          // enger = mehr Leistung
        $this->assertGreaterThan($weit['laenge_pro_m2'], $eng['laenge_pro_m2']);    // enger = mehr Rohr/m²
    }

    public function test_randzone_erhoeht_leistung_und_liefert_detail(): void
    {
        $s = new FussbodenheizungService;
        $ohne = $s->analyse($this->fbh(), 25.0, 1400.0, 20.0, 35.0, 5.0, 29.0);
        $mit = $s->analyse($this->fbh([
            'randzone' => true, 'aussenwand_laenge_m' => 8.0, 'randzone_breite_m' => 1.0, 'randzone_verlegeabstand_mm' => 75,
        ]), 25.0, 1400.0, 20.0, 35.0, 5.0, 29.0);

        // Randzone deckt zusätzliche Leistung am Außenwand-Streifen → mehr W gesamt.
        $this->assertGreaterThan($ohne['q_real_w'], $mit['q_real_w']);
        $this->assertArrayHasKey('randzone', $mit);
        $this->assertSame(8.0, $mit['randzone']['flaeche_m2']);        // 8 m · 1 m
        $this->assertSame(75, $mit['randzone']['verlegeabstand_mm']);
        $this->assertSame(35.0, $mit['randzone']['max_oberflaeche_c']);
        // Randstreifen liefert mehr W/m² als der mittlere Wert (35 °C statt 29 °C, enger).
        $this->assertGreaterThan($mit['q_eff_w_m2'], $mit['randzone']['q_eff_w_m2']);
        $this->assertArrayNotHasKey('randzone', $ohne);
    }

    public function test_randzone_ermoeglicht_deckung_an_der_kapazitaetsgrenze(): void
    {
        $s = new FussbodenheizungService;
        // 102 W/m² Bedarf > 29-°C-Grenze (97,2) → Aufenthaltszone allein unzureichend.
        $ohne = $s->analyse($this->fbh(), 25.0, 2550.0, 20.0, 40.0, 5.0, 29.0);
        $this->assertTrue($ohne['oberflaeche_ueberschritten']);
        $this->assertNull($ohne['min_vorlauf_c']);

        // Mit Randzone (35 °C, 75 mm) wird die Last deckbar.
        $mit = $s->analyse($this->fbh([
            'randzone' => true, 'aussenwand_laenge_m' => 10.0, 'randzone_breite_m' => 1.0, 'randzone_verlegeabstand_mm' => 75,
        ]), 25.0, 2550.0, 20.0, 40.0, 5.0, 29.0);

        $this->assertFalse($mit['oberflaeche_ueberschritten']);
        $this->assertNotNull($mit['min_vorlauf_c']);
        $this->assertLessThan(50.0, $mit['min_vorlauf_c']);
        $this->assertGreaterThanOrEqual(100, $mit['deckung_pct']);
    }

    public function test_randzone_breite_ist_auf_einen_meter_begrenzt(): void
    {
        $s = new FussbodenheizungService;
        // EN 1264: max. 1 m Streifenbreite – 2,0 wird auf 1,0 gekappt.
        $breit = $s->analyse($this->fbh(['randzone' => true, 'aussenwand_laenge_m' => 6.0, 'randzone_breite_m' => 2.0]), 25.0, 1400.0, 20.0, 35.0, 5.0, 29.0);
        $eins = $s->analyse($this->fbh(['randzone' => true, 'aussenwand_laenge_m' => 6.0, 'randzone_breite_m' => 1.0]), 25.0, 1400.0, 20.0, 35.0, 5.0, 29.0);

        $this->assertSame(6.0, $breit['randzone']['flaeche_m2']);   // 6 m · max 1 m
        $this->assertSame($eins['randzone']['flaeche_m2'], $breit['randzone']['flaeche_m2']);
    }

    public function test_randzone_ohne_aussenwand_bleibt_einzonig(): void
    {
        $s = new FussbodenheizungService;
        $r = $s->analyse($this->fbh(['randzone' => true, 'aussenwand_laenge_m' => 0.0]), 25.0, 1400.0, 20.0, 35.0, 5.0, 29.0);
        $basis = $s->analyse($this->fbh(), 25.0, 1400.0, 20.0, 35.0, 5.0, 29.0);

        $this->assertArrayNotHasKey('randzone', $r);
        $this->assertSame($basis['q_real_w'], $r['q_real_w']);   // identisch zum Nicht-Randzonen-Pfad
    }
}
