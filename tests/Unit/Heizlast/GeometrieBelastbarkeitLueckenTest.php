<?php

namespace Tests\Unit\Heizlast;

use App\Services\Heizlast\GeometrieAbleitungService;
use Tests\TestCase;

/**
 * G0a / AP-4a — bekannte Belastbarkeits-LÜCKEN (P6/P7/P8 der Startblock-Matrix).
 *
 * Diese Fälle werden AUSSCHLIESSLICH dokumentiert (markTestIncomplete) — es existiert heute
 * KEIN Topologie-/Einheiten-Gate; das ist Scope von G0b/AP-4b. Sie werden NICHT durch
 * Produktivcode, Guards oder Refactoring grün gemacht (Startblock §7/§8, I3).
 *
 * @group belastbarkeit-luecke
 */
class GeometrieBelastbarkeitLueckenTest extends TestCase
{
    private function service(): GeometrieAbleitungService
    {
        return new GeometrieAbleitungService;
    }

    /** P6 — Selbstschneidendes Polygon (Schmetterling) wird heute still gerechnet, nicht abgelehnt. */
    public function test_p6_selbstschneidendes_polygon_wird_nicht_abgelehnt(): void
    {
        // „Schmetterling": Kanten kreuzen sich → topologisch ungültig.
        $bowtie = [
            ['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 5000],
            ['x' => 5000, 'y' => 0], ['x' => 0, 'y' => 5000],
        ];
        $flaeche = $this->service()->polygonFlaecheM2($bowtie);

        // Ist-Verhalten: liefert stillschweigend einen (physikalisch nicht sinnvollen) Wert, keine Ablehnung.
        $this->assertIsFloat($flaeche);
        $this->markTestIncomplete('Kein Topologie-Gate: Selbstschnitt wird still gerechnet statt als Blocker abgelehnt (G0b/AP-4b).');
    }

    /** P7 — Entartetes Polygon (kollineare Punkte, Nullfläche) wird nicht markiert. */
    public function test_p7_entartetes_polygon_wird_nicht_markiert(): void
    {
        // Kollinear → Fläche 0; wird nicht von einem echten (nicht existenten) Punkt-Duplikat-Guard unterschieden.
        $kollinear = [
            ['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 10000, 'y' => 0],
        ];
        $flaeche = $this->service()->polygonFlaecheM2($kollinear);

        $this->assertSame(0.0, round($flaeche, 6));
        $this->markTestIncomplete('Kein Entartungs-Gate: Nullfläche/kollinear wird still als 0 geliefert statt als ungültig markiert (G0b/AP-4b).');
    }

    /** P8 — Falsche Einheit (Meter statt mm) fällt heute nicht auf. */
    public function test_p8_falsche_einheit_meter_faellt_nicht_auf(): void
    {
        // 5 m × 5 m als Meter-Koordinaten interpretiert → 25/1e6 = 0,000025 m² statt 25 m².
        $meterKoordinaten = [
            ['x' => 0, 'y' => 0], ['x' => 5, 'y' => 0],
            ['x' => 5, 'y' => 5], ['x' => 0, 'y' => 5],
        ];
        $flaeche = $this->service()->polygonFlaecheM2($meterKoordinaten);

        // Ist: 0,000025 m² — kein Einheiten-Guard, kein Plausibilitäts-Hinweis.
        $this->assertEqualsWithDelta(0.000025, $flaeche, 1e-9);
        $this->markTestIncomplete('Kein Einheiten-Guard: Meter-statt-mm-Eingabe liefert stillschweigend 0,000025 m² (G0b/AP-4b).');
    }
}
