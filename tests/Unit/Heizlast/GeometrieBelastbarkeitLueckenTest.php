<?php

namespace Tests\Unit\Heizlast;

use App\Services\Geometrie\TopologieGate;
use App\Services\Heizlast\GeometrieAbleitungService;
use Tests\TestCase;

/**
 * G0b / AP-4b — die in G0a als LÜCKE dokumentierten Fälle P6/P7/P8 sind jetzt GESCHLOSSEN:
 * das Topologie-Gate lehnt ungültige Geometrie ab (statt still zu rechnen). Der gespiegelte Kern
 * bleibt unverändert — die Ablehnung kommt ausschließlich aus dem vorgelagerten Gate.
 *
 * (In G0a standen diese Fälle als `markTestIncomplete` mit Verweis auf G0b/AP-4b.)
 */
class GeometrieBelastbarkeitLueckenTest extends TestCase
{
    private function gate(): TopologieGate
    {
        return new TopologieGate;
    }

    /** P6 — Selbstschneidendes Polygon (Schmetterling) wird jetzt vom Gate abgelehnt. */
    public function test_p6_selbstschneidendes_polygon_wird_abgelehnt(): void
    {
        $bowtie = [
            ['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 5000],
            ['x' => 5000, 'y' => 0], ['x' => 0, 'y' => 5000],
        ];
        $ergebnis = $this->gate()->pruefePolygon($bowtie);

        $this->assertFalse($ergebnis->valid);
        $this->assertContains('selbstschnitt', $ergebnis->ruleKeys());

        // Beleg Mirror unverändert: die rohe Engine liefert weiterhin einen Wert (nur hinter dem Gate genutzt).
        $this->assertIsFloat((new GeometrieAbleitungService)->polygonFlaecheM2($bowtie));
    }

    /** P7 — Entartetes Polygon (kollinear/Nullfläche) wird jetzt vom Gate abgelehnt. */
    public function test_p7_entartetes_polygon_wird_abgelehnt(): void
    {
        $kollinear = [
            ['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 10000, 'y' => 0],
        ];
        $ergebnis = $this->gate()->pruefePolygon($kollinear);

        $this->assertFalse($ergebnis->valid);
        $this->assertContains('degeneriert', $ergebnis->ruleKeys());
    }

    /** P8 — Falsche Einheit (Meter statt mm) fällt jetzt auf: zu kurze Segmente / zu kleine Fläche. */
    public function test_p8_falsche_einheit_meter_wird_abgelehnt(): void
    {
        $meterKoordinaten = [
            ['x' => 0, 'y' => 0], ['x' => 5, 'y' => 0],
            ['x' => 5, 'y' => 5], ['x' => 0, 'y' => 5],
        ];
        $ergebnis = $this->gate()->pruefePolygon($meterKoordinaten);

        $this->assertFalse($ergebnis->valid);
        // 5-mm-Segmente < 10 mm UND Fläche 25 mm² < 10 000 mm² → abgelehnt.
        $this->assertContains('degeneriert', $ergebnis->ruleKeys());
    }
}
