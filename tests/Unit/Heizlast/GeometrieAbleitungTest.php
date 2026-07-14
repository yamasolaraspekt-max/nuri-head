<?php

namespace Tests\Unit\Heizlast;

use App\Models\HeizlastRaum;
use App\Models\RaumGeometrie;
use App\Services\Heizlast\GeometrieAbleitungService;
use Tests\TestCase;

/**
 * G0a / AP-4a — Referenztests der VORHANDENEN Geometrie-Ableitung (test/spec-only).
 * Belegt die P1/P2/P4-Fälle der Startblock-Matrix (docs/ap4a-geometrie-referenztests-startblock.md).
 * KEIN Produktivcode geändert, KEIN Topologie-Gate (das ist G0b).
 *
 * Einheiten: Polygon/Segmente in mm-Integer; polygonFlaecheM2 = Shoelace, /1_000_000 → m².
 */
class GeometrieAbleitungTest extends TestCase
{
    private function service(): GeometrieAbleitungService
    {
        return new GeometrieAbleitungService;
    }

    /** P1 — Rechteck 5 m × 5 m = 25,00 m² (Shoelace, mm → m²). */
    public function test_p1_rechteck_5x5_ist_25_qm(): void
    {
        // 5000 mm × 5000 mm; Shoelace: |Σ(xᵢ·yⱼ − xⱼ·yᵢ)| / 2 / 1e6 = 50e6/2/1e6 = 25,00
        $polygon = [
            ['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0],
            ['x' => 5000, 'y' => 5000], ['x' => 0, 'y' => 5000],
        ];
        $this->assertSame(25.0, round($this->service()->polygonFlaecheM2($polygon), 2));
    }

    /** P4 — L-Form: 8×6 − 3×2 = 42,00 m² (Rechenweg im Kommentar). */
    public function test_p4_l_form_ist_42_qm(): void
    {
        // Vollrechteck 8000×6000 = 48 m² minus Ausschnitt oben-rechts 3000×2000 = 6 m² → 42 m²
        $polygon = [
            ['x' => 0, 'y' => 0], ['x' => 8000, 'y' => 0],
            ['x' => 8000, 'y' => 4000], ['x' => 5000, 'y' => 4000],
            ['x' => 5000, 'y' => 6000], ['x' => 0, 'y' => 6000],
        ];
        $this->assertSame(42.0, round($this->service()->polygonFlaecheM2($polygon), 2));
    }

    /** P2 — Wandfläche 5 m × 3 m = 15,00 m² über die tatsächliche Ableitung (Segmentlänge × Höhe). */
    public function test_p2_wandflaeche_5x3_ist_15_qm(): void
    {
        $geo = new RaumGeometrie;
        $geo->polygon = [
            ['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0],
            ['x' => 5000, 'y' => 3000], ['x' => 0, 'y' => 3000],
        ];
        $geo->wand_segmente = [[
            'von' => ['x' => 0, 'y' => 0], 'bis' => ['x' => 5000, 'y' => 0],
            'bauteil_typ' => 'wand', 'grenzflaeche' => 'aussen',
        ]];
        $geo->hoehe_mm = 3000;
        $raum = new HeizlastRaum;
        $raum->name = 'R';
        $raum->nutzung = 'wohnen';
        $geo->setRelation('heizlastRaum', $raum);

        $out = $this->service()->ausGeometrie($geo);
        $wand = collect($out['bauteile'])->firstWhere('typ', 'wand');

        // 5000 mm × 3000 mm / 1e6 = 15,00 m²
        $this->assertNotNull($wand);
        $this->assertSame(15.0, (float) $wand['flaeche_m2']);
    }

    /**
     * I2.8 — Dokumentierter Mutations-Gegenbeweis (kein Produktivcode geändert):
     * Die Referenz ist diskriminierend — eine Formel ohne die ÷2- bzw. ÷1e6-Terme liefert NICHT 25.
     */
    public function test_i2_8_referenz_ist_diskriminierend(): void
    {
        $polygon = [
            ['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0],
            ['x' => 5000, 'y' => 5000], ['x' => 0, 'y' => 5000],
        ];
        $korrekt = $this->service()->polygonFlaecheM2($polygon);
        $this->assertSame(25.0, round($korrekt, 2));

        // Gegenprobe: rohe Shoelace-Summe ohne /2 (= 50) und ohne /1e6 (= 50e6) ≠ Sollwert.
        $n = count($polygon);
        $roh = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $roh += $polygon[$i]['x'] * $polygon[$j]['y'] - $polygon[$j]['x'] * $polygon[$i]['y'];
        }
        $this->assertNotEqualsWithDelta(25.0, abs($roh), 0.01);          // fehlendes /2 und /1e6 → 50e6
        $this->assertNotEqualsWithDelta(25.0, abs($roh) / 2.0, 0.01);    // fehlendes /1e6 → 25e6
    }
}
