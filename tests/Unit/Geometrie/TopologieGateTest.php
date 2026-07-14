<?php

namespace Tests\Unit\Geometrie;

use App\Services\Geometrie\GeometrieUngueltigException;
use App\Services\Geometrie\TopologieGate;
use App\Services\Heizlast\GeometrieAbleitungService;
use Tests\TestCase;

/**
 * G0b / AP-4b — Topologie-Gate: Regeln 1–7, Grenzwerte, Fuzz, Orientierung, Golden-Fassade,
 * Mutations-Gegenbeweis. Der gespiegelte Kern wird NICHT verändert (nur hinter dem Gate genutzt).
 */
class TopologieGateTest extends TestCase
{
    private function gate(): TopologieGate
    {
        return new TopologieGate;
    }

    /** @return array<int,array{x:int,y:int}> */
    private function quadrat(int $seite = 5000): array
    {
        return [['x' => 0, 'y' => 0], ['x' => $seite, 'y' => 0], ['x' => $seite, 'y' => $seite], ['x' => 0, 'y' => $seite]];
    }

    // ---- Gültige Geometrie ----

    public function test_gueltiges_quadrat_besteht(): void
    {
        $this->assertTrue($this->gate()->pruefePolygon($this->quadrat())->valid);
    }

    public function test_orientierung_ist_kein_blocker(): void
    {
        $ccw = $this->quadrat();
        $cw = array_reverse($ccw);
        $this->assertTrue($this->gate()->pruefePolygon($ccw)->valid);
        $this->assertTrue($this->gate()->pruefePolygon($cw)->valid);
        // Fläche orientierungsneutral (abs()).
        $this->assertSame(
            $this->gate()->flaecheOderException($ccw),
            $this->gate()->flaecheOderException($cw)
        );
    }

    // ---- Regel 1: Mindestpunktzahl ----

    public function test_regel1_zu_wenig_punkte(): void
    {
        $this->assertContains('min_punkte', $this->gate()->pruefePolygon([['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0]])->ruleKeys());
        $this->assertContains('min_punkte', $this->gate()->pruefePolygon([])->ruleKeys());
    }

    // ---- Regel 3: Mindestsegmentlänge (Grenzwerte 9,9 / 10,0 / 10,1 mm) ----

    /** @return array<int,array{x:float,y:int}> */
    private function duennesRechteck(float $breite): array
    {
        return [['x' => 0, 'y' => 0], ['x' => $breite, 'y' => 0], ['x' => $breite, 'y' => 20000], ['x' => 0, 'y' => 20000]];
    }

    public function test_regel3_grenzwerte_min_segment(): void
    {
        $this->assertContains('min_segment', $this->gate()->pruefePolygon($this->duennesRechteck(9.9))->ruleKeys());   // knapp unter
        $this->assertNotContains('min_segment', $this->gate()->pruefePolygon($this->duennesRechteck(10.0))->ruleKeys()); // exakt auf
        $this->assertNotContains('min_segment', $this->gate()->pruefePolygon($this->duennesRechteck(10.1))->ruleKeys()); // knapp über
    }

    // ---- Regel 4: Selbstschnitt (Bow-Tie) ----

    public function test_regel4_bowtie_selbstschnitt(): void
    {
        $bowtie = [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 5000], ['x' => 5000, 'y' => 0], ['x' => 0, 'y' => 5000]];
        $keys = $this->gate()->pruefePolygon($bowtie)->ruleKeys();
        $this->assertContains('selbstschnitt', $keys);
    }

    // ---- Regel 5: Selbstberührung (zusammenfallende nicht-benachbarte Ecken) ----

    public function test_regel5_selbstberuehrung(): void
    {
        $pinch = [['x' => 0, 'y' => 0], ['x' => 4000, 'y' => 0], ['x' => 4000, 'y' => 4000], ['x' => 0, 'y' => 0], ['x' => -4000, 'y' => 4000]];
        $this->assertContains('selbstberuehrung', $this->gate()->pruefePolygon($pinch)->ruleKeys());
    }

    // ---- Regel 6: Degeneriertheit (kollinear + Mindestflächen-Grenzwerte 99/100/101 mm) ----

    public function test_regel6_kollinear_degeneriert(): void
    {
        $kollinear = [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 10000, 'y' => 0]];
        $this->assertContains('degeneriert', $this->gate()->pruefePolygon($kollinear)->ruleKeys());
    }

    public function test_regel6_grenzwerte_min_flaeche(): void
    {
        // 99×99 = 9801 mm² < 10000 → degeneriert; 100×100 = 10000 (= Grenze, nicht <) → ok; 101×101 → ok.
        $this->assertContains('degeneriert', $this->gate()->pruefePolygon($this->quadrat(99))->ruleKeys());
        $this->assertNotContains('degeneriert', $this->gate()->pruefePolygon($this->quadrat(100))->ruleKeys());
        $this->assertNotContains('degeneriert', $this->gate()->pruefePolygon($this->quadrat(101))->ruleKeys());
    }

    // ---- Regel 7: Öffnungsplausibilität ----

    public function test_regel7_oeffnung_groesser_wand(): void
    {
        $bauteile = [
            ['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 30],
            ['typ' => 'fenster', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 40],
        ];
        $this->assertContains('oeffnung_groesser_wand', $this->gate()->pruefeOeffnungen($bauteile)->ruleKeys());
    }

    public function test_regel7_gleichheit_ist_zulaessig(): void
    {
        $bauteile = [
            ['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 30],
            ['typ' => 'fenster', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 30],
        ];
        $this->assertTrue($this->gate()->pruefeOeffnungen($bauteile)->valid);
    }

    // ---- Golden-Fassade + Exception ----

    public function test_fassade_liefert_gleiche_flaeche_wie_mirror(): void
    {
        $quadrat = $this->quadrat();
        $this->assertSame(
            (new GeometrieAbleitungService)->polygonFlaecheM2($quadrat),
            $this->gate()->flaecheOderException($quadrat)
        );
    }

    public function test_fassade_wirft_bei_ungueltig(): void
    {
        $this->expectException(GeometrieUngueltigException::class);
        $this->gate()->flaecheOderException([['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 5000], ['x' => 5000, 'y' => 0], ['x' => 0, 'y' => 5000]]);
    }

    // ---- Fuzz: kontrollierte Ablehnung, kein Crash ----

    public function test_fuzz_kontrollierte_ablehnung(): void
    {
        foreach ([[], [['x' => 0, 'y' => 0]], [['x' => 0, 'y' => 0], ['x' => 1, 'y' => 1]]] as $eingabe) {
            $this->assertFalse($this->gate()->pruefePolygon($eingabe)->valid);
        }
        // Doppelte Punkte → nach Dedup zu wenig.
        $this->assertFalse($this->gate()->pruefePolygon([['x' => 0, 'y' => 0], ['x' => 0, 'y' => 0], ['x' => 0, 'y' => 0]])->valid);
        // Sehr große Koordinaten → gültig, kein Crash/Endlosrechnung.
        $gross = [['x' => 0, 'y' => 0], ['x' => 1000000000, 'y' => 0], ['x' => 1000000000, 'y' => 1000000000], ['x' => 0, 'y' => 1000000000]];
        $this->assertTrue($this->gate()->pruefePolygon($gross)->valid);
    }

    // ---- Mutations-Gegenbeweis: Gate ist load-bearing ----

    public function test_mutation_gate_ist_load_bearing(): void
    {
        $bowtie = [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 5000], ['x' => 5000, 'y' => 0], ['x' => 0, 'y' => 5000]];

        // Am Gate vorbei (direkt in den Mirror) liefert ein selbstschneidendes Polygon STILL einen Wert:
        $ungeschuetzt = (new GeometrieAbleitungService)->polygonFlaecheM2($bowtie);
        $this->assertIsFloat($ungeschuetzt);

        // Durch das Gate wird derselbe Fall abgelehnt → das Gate macht den Unterschied.
        $this->expectException(GeometrieUngueltigException::class);
        $this->gate()->flaecheOderException($bowtie);
    }
}
