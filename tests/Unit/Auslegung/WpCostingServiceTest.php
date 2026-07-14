<?php

namespace Tests\Unit\Auslegung;

use App\Services\Auslegung\WpCostingService;
use App\Services\Auslegung\WpKostenErgebnis;
use PHPUnit\Framework\TestCase;

/**
 * P1b-1 — fokussierte Tests des verhaltensgleich extrahierten WpCostingService. Die Sollwerte
 * entsprechen exakt dem P1a-Golden-Master (Investition summe_netto + Stromkosten roh = stromKwh×preis).
 * Reine Unit-Ebene (KostenService ist pure PHP, keine DB).
 */
class WpCostingServiceTest extends TestCase
{
    private function service(): WpCostingService
    {
        return new WpCostingService;
    }

    public function test_standardausgabe(): void
    {
        $r = $this->service()->berechne(25000.0, 6483.0, 0.30);
        $this->assertInstanceOf(WpKostenErgebnis::class, $r);
        $this->assertSame(25000.0, $r->investitionNetto);          // KostenService summe_netto
        $this->assertEqualsWithDelta(1944.9, $r->stromkostenJahr, 0.0001); // 6483 * 0.30 (roh, Float)
        $this->assertSame(1945.0, round($r->stromkostenJahr));     // Controller-Ausgabe-Rundung wie P1a
    }

    public function test_fehlender_preis_investition_null(): void
    {
        $r = $this->service()->berechne(0.0, 6483.0, 0.30);
        $this->assertSame(0.0, $r->investitionNetto);              // keine Sonstiges-Position
        $this->assertEqualsWithDelta(1944.9, $r->stromkostenJahr, 0.0001);
    }

    public function test_warmwasser_an_vs_aus_ueber_stromverbrauch(): void
    {
        // WW an/aus wirkt über den (vom Aufrufer gelieferten) Stromverbrauch, nicht über den Kostenservice.
        $an = $this->service()->berechne(25000.0, 6483.0, 0.30);
        $aus = $this->service()->berechne(25000.0, 5517.0, 0.30);
        $this->assertEqualsWithDelta(1944.9, $an->stromkostenJahr, 0.0001);
        $this->assertEqualsWithDelta(1655.1, $aus->stromkostenJahr, 0.0001); // 5517 * 0.30
        $this->assertSame(1655.0, round($aus->stromkostenJahr));   // P1a-Golden ww_ohne
    }

    public function test_rundungsgrenze_bleibt_beim_aufrufer(): void
    {
        // Service liefert Rohwert; die Rundung (round) sitzt bewusst im Controller-View-Model.
        $r = $this->service()->berechne(0.0, 100.0, 0.305);
        $this->assertEqualsWithDelta(30.5, $r->stromkostenJahr, 0.0001); // roh, ungerundet
        $this->assertSame(31.0, round($r->stromkostenJahr));       // Aufrufer-Rundung (halbzahlig auf)
    }

    public function test_foerderdeckel_relevante_kostengroesse_ungedeckelt(): void
    {
        // Der Service liefert die volle Investition; die Deckelung (30000) ist FoerderungService-Sache (nicht hier).
        $r = $this->service()->berechne(50000.0, 6483.0, 0.30);
        $this->assertSame(50000.0, $r->investitionNetto);
    }

    public function test_geraete_unabhaengig_und_deterministisch(): void
    {
        // A1-Altverhalten: der Kostenservice kennt KEIN Gerät (Signatur ohne Gerät) → gleiche Eingabe, gleiche Kosten.
        $a = $this->service()->berechne(25000.0, 6483.0, 0.30);
        $b = $this->service()->berechne(25000.0, 6483.0, 0.30);
        $this->assertSame($a->investitionNetto, $b->investitionNetto);
        $this->assertSame($a->stromkostenJahr, $b->stromkostenJahr);
    }
}
