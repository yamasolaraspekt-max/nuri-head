<?php

namespace Tests\Unit\Heizlast;

use App\Models\HeizlastRaum;
use App\Models\RaumGeometrie;
use App\Services\Heizlast\GeometrieAbleitungService;
use Tests\TestCase;

/**
 * AP-4b — Referenztest-Transplantation aus wberechnung (tests/Unit/GeometrieAbleitungTest.php),
 * test/spec-only: KEIN Produktivcode geändert. Ergänzt den Startblock (GeometrieAbleitungTest,
 * P1/P2/P4) um die wb-Referenzfälle, die dort noch fehlten: Trapez-Shoelace, Rechteck 5×6,
 * Brutto-Wand + separates Fenster (Flächen, Azimut, Grenzflächen-Erbschaft, U-Quelle A).
 *
 * Alle Fälle sind DB-frei (in-memory RaumGeometrie + setRelation, U-Werte direkt = Strategie A,
 * keine konstruktion_id) — die Sollwerte sind handgerechnet und im Kommentar belegt.
 */
class GeometrieAbleitungReferenzTest extends TestCase
{
    private function service(): GeometrieAbleitungService
    {
        return new GeometrieAbleitungService;
    }

    /**
     * In-memory-Geometrie mit Raum-Relation (kein Persist — ausGeometrie braucht keine DB).
     *
     * @param  array<string, mixed>  $geo
     */
    private function geometrie(array $geo): RaumGeometrie
    {
        $g = new RaumGeometrie;
        foreach ($geo as $k => $v) {
            $g->{$k} = $v;
        }
        $raum = new HeizlastRaum;
        $raum->name = 'Raum';
        $raum->nutzung = 'wohnen';
        $g->setRelation('heizlastRaum', $raum);

        return $g;
    }

    /** wb-Referenz: Rechteck 5000×6000 mm = 30,00 m² (Shoelace, mm² → m²). */
    public function test_shoelace_rechteck_5x6_ist_30_qm(): void
    {
        $this->assertEqualsWithDelta(30.0, $this->service()->polygonFlaecheM2([
            ['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 5000, 'y' => 6000], ['x' => 0, 'y' => 6000],
        ]), 0.001);
    }

    /** wb-Referenz: Trapez (6000 + 4000)/2 × 3000 = 15,00 m² — nicht-rechteckiger Shoelace-Fall. */
    public function test_shoelace_trapez_ist_15_qm(): void
    {
        $this->assertEqualsWithDelta(15.0, $this->service()->polygonFlaecheM2([
            ['x' => 0, 'y' => 0], ['x' => 6000, 'y' => 0], ['x' => 4000, 'y' => 3000], ['x' => 0, 'y' => 3000],
        ]), 0.001);
    }

    /**
     * wb-Referenz: Die Wand bleibt BRUTTO (Öffnung wird NICHT abgezogen — das tut
     * RaumHuelleService raumweise), das Fenster wird separates Bauteil und ERBT
     * Grenzfläche + Azimut der Wand; direkter U-Wert ⇒ Strategie A.
     */
    public function test_bruttowand_und_fenster_separat(): void
    {
        $g = $this->geometrie([
            'polygon' => [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 5000, 'y' => 4000], ['x' => 0, 'y' => 4000]],
            'hoehe_mm' => 2500,
            'wand_segmente' => [[
                'von' => ['x' => 0, 'y' => 0], 'bis' => ['x' => 5000, 'y' => 0],
                'grenzflaeche' => 'aussen', 'azimut_grad' => 180, 'bauteil_typ' => 'wand', 'u_wert' => 1.4,
                'oeffnungen' => [['breite_mm' => 1200, 'hoehe_mm' => 1400, 'typ' => 'fenster', 'u_wert' => 1.3]],
            ]],
        ]);

        $raum = $this->service()->ausGeometrie($g);
        $wand = $raum['bauteile'][0];
        $fenster = $raum['bauteile'][1];

        $this->assertSame('wand', $wand['typ']);
        $this->assertEqualsWithDelta(12.5, $wand['flaeche_m2'], 0.001);    // 5000 mm × 2500 mm brutto = 12,50 m²
        $this->assertSame(180.0, (float) $wand['azimut_grad']);
        $this->assertSame('A', $wand['u_strategie']);                      // direkter U 1,4 ⇒ Strategie A

        $this->assertSame('fenster', $fenster['typ']);
        $this->assertEqualsWithDelta(1.68, $fenster['flaeche_m2'], 0.001); // 1200 mm × 1400 mm = 1,68 m²
        $this->assertSame('aussen', $fenster['grenzflaeche']);             // erbt die Wand-Grenzfläche
        $this->assertSame(180.0, (float) $fenster['azimut_grad']);         // erbt das Wand-Azimut
        $this->assertSame('A', $fenster['u_strategie']);
        $this->assertSame(1.3, (float) $fenster['u_wert']);
    }

    /** wb-Referenz: Grundfläche/Höhe des Raum-Arrays kommen aus der Geometrie (30 m², 2,5 m). */
    public function test_raumkopf_aus_geometrie(): void
    {
        $g = $this->geometrie([
            'polygon' => [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 5000, 'y' => 6000], ['x' => 0, 'y' => 6000]],
            'hoehe_mm' => 2500,
            'wand_segmente' => [],
        ]);

        $raum = $this->service()->ausGeometrie($g);

        $this->assertEqualsWithDelta(30.0, $raum['grundflaeche_m2'], 0.001);
        $this->assertEqualsWithDelta(2.5, $raum['hoehe_m'], 0.001);
        $this->assertSame('Raum', $raum['name']);
        $this->assertSame('wohnen', $raum['nutzung']);
    }
}
