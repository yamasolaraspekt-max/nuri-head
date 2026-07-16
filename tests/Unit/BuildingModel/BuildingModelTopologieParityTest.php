<?php

namespace Tests\Unit\BuildingModel;

use App\Services\BuildingModel\CanonicalBuildingModelValidator;
use App\Services\Geometrie\TopologieGate;
use Tests\TestCase;

/**
 * G1a / CAD-A6 (Startblock §11) — Paritätsnachweis: derselbe geometrische Grenzfall erhält im bestehenden
 * App\Services\Geometrie\TopologieGate UND im CanonicalBuildingModelValidator dasselbe Urteil. Damit ist
 * belegt, dass der neue Validator KEIN zweites Toleranzregime aufbaut, sondern den zentralen Vertrag
 * (config/geometrie.php via GeometrieToleranz/TopologieGate) verwendet.
 *
 * Gegenbeweis-Anker (Mutation im Startblock §15.3): ersetzt der Validator die zentrale Delegation durch ein
 * hartkodiertes Epsilon, divergiert er hier vom TopologieGate und dieser Test wird rot.
 */
class BuildingModelTopologieParityTest extends TestCase
{
    private function fixture(string $rel): array
    {
        return json_decode((string) file_get_contents(__DIR__.'/../../Fixtures/building-model/'.$rel), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Extrahiert den Raum-Boundary-Knotenring (in Laufrichtung) aus einem Ein-Raum-Fixture — dieselbe
     * Ableitung, die der Validator intern für die Topologie-Delegation nutzt.
     *
     * @return array<int,array{x:int,y:int}>
     */
    private function ringAusFixture(array $model): array
    {
        $storey = $model['buildings'][0]['storeys'][0];
        $nodes = [];
        foreach ($storey['nodes'] as $n) {
            $nodes[$n['id']] = ['x' => $n['x_mm'], 'y' => $n['y_mm']];
        }
        $wallSeg = [];
        foreach ($storey['walls'] as $w) {
            $ids = $w['reference_path']['node_ids'];
            $segs = [];
            for ($i = 0; $i < count($ids) - 1; $i++) {
                $segs[] = [$ids[$i], $ids[$i + 1]];
            }
            $wallSeg[$w['id']] = $segs;
        }
        $ring = [];
        foreach ($storey['rooms'][0]['boundary_edges'] as $edge) {
            [$a, $b] = $wallSeg[$edge['wall_id']][$edge['host_segment_index']];
            $fromId = $edge['traversal'] === 'backward' ? $b : $a;
            $ring[] = $nodes[$fromId];
        }

        return $ring;
    }

    public function test_gueltiger_ring_wird_von_beiden_akzeptiert(): void
    {
        $model = $this->fixture('valid/01-rectangle-four-axis-walls.json');
        $ring = $this->ringAusFixture($model);

        $gate = (new TopologieGate)->pruefePolygon($ring);
        $validator = (new CanonicalBuildingModelValidator)->validate($model);

        $this->assertTrue($gate->valid, 'TopologieGate sollte den gültigen Ring akzeptieren.');
        $this->assertTrue($validator->isValid(), 'Validator sollte das gültige Modell akzeptieren.');
    }

    public function test_toleranzgrenzfall_wird_von_beiden_abgelehnt(): void
    {
        $model = $this->fixture('invalid/15-tolerance-borderline-vs-topologiegate.json');
        $ring = $this->ringAusFixture($model);

        $gate = (new TopologieGate)->pruefePolygon($ring);
        $validator = (new CanonicalBuildingModelValidator)->validate($model);

        // PARITÄT: beide lehnen denselben 8-mm-Grenzfall ab (Mindestsegment aus dem zentralen Vertrag).
        $this->assertFalse($gate->valid, 'TopologieGate muss den 8-mm-Grenzfall ablehnen.');
        $this->assertContains('min_segment', $gate->ruleKeys());
        $this->assertTrue($validator->hasErrorCode('room.boundary_invalid_topology'), 'Validator muss denselben Grenzfall ablehnen.');
    }
}
