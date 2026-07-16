<?php

namespace Tests\Unit\BuildingModel;

use App\Services\BuildingModel\CanonicalBuildingModelValidator;
use Tests\TestCase;

/**
 * G1a — Tests des semantischen kanonischen Modell-Validators (Vertrag v1, Masterprompt V3.3 + CAD-A1..A6).
 *
 * Bootet Laravel (Tests\TestCase), weil der Validator den zentralen Toleranzvertrag über config('geometrie.*')
 * / GeometrieToleranz / TopologieGate liest (CAD-A6: kein zweites Toleranzregime). Kein DB-Zugriff.
 *
 * Gültige Fixtures müssen grün sein; jedes ungültige Fixture muss den erwarteten STABILEN Fehlercode
 * liefern (nicht nur „irgendein" Fehler / Meldungstext).
 */
class CanonicalBuildingModelValidatorTest extends TestCase
{
    private function fixture(string $rel): array
    {
        $path = __DIR__.'/../../Fixtures/building-model/'.$rel;

        return json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    private function validator(): CanonicalBuildingModelValidator
    {
        return new CanonicalBuildingModelValidator;
    }

    /** @return array<string, array{0:string}> */
    public static function gueltigeFixtures(): array
    {
        return [
            'Rechteck / vier Achswände' => ['valid/01-rectangle-four-axis-walls.json'],
            'Wandreferenz inner' => ['valid/02-wall-reference-inner.json'],
            'Wandreferenz outer' => ['valid/03-wall-reference-outer.json'],
            'Polylinienwand + Öffnung Segment 1' => ['valid/04-polyline-wall-opening-segment1.json'],
            'zwei Geschosse unterschiedliche OKFF' => ['valid/05-two-storeys-different-okff.json'],
            'L-Raum Boundary über Wandkanten' => ['valid/06-l-shaped-room-boundary-edges.json'],
            'Öffnung Rohbau + Fertigmaß' => ['valid/07-opening-rough-and-finished.json'],
            'schräger Bestandsraum' => ['valid/08-skewed-non-orthogonal-room.json'],
            'unbekannter Nordbezug (null)' => ['valid/09-unknown-north.json'],
            'Scanvorschlag draft' => ['valid/10-scan-proposal-draft.json'],
        ];
    }

    /** @dataProvider gueltigeFixtures */
    public function test_gueltige_fixtures_sind_valide(string $rel): void
    {
        $res = $this->validator()->validate($this->fixture($rel));
        $this->assertTrue($res->isValid(), 'Erwartet gültig, Fehler: '.json_encode($res->errorCodes()));
    }

    /** @return array<string, array{0:string, 1:string}> */
    public static function ungueltigeFixtures(): array
    {
        return [
            'Wand ohne reference_line_type' => ['invalid/01-wall-missing-reference-line-type.json', 'wall.missing_reference_line_type'],
            'ungültiger thickness_change_anchor' => ['invalid/02-invalid-thickness-anchor.json', 'wall.invalid_thickness_anchor'],
            'entartete Wand (identische Knoten)' => ['invalid/03-wall-degenerate.json', 'wall.degenerate'],
            'Segment unter Mindestlänge' => ['invalid/04-wall-segment-too-short.json', 'wall.segment_too_short'],
            'Öffnung falsches Segment' => ['invalid/05-opening-wrong-segment.json', 'opening.invalid_host_segment'],
            'Öffnung über Segmentende' => ['invalid/06-opening-exceeds-segment.json', 'opening.exceeds_wall_length'],
            'Öffnung vertikal außerhalb' => ['invalid/07-opening-outside-wall-height.json', 'opening.exceeds_wall_height'],
            'überlappende Öffnungen' => ['invalid/08-openings-overlap.json', 'opening.overlap'],
            'Boundary fehlende Wand' => ['invalid/09-room-boundary-missing-wall.json', 'room.boundary_missing_wall'],
            'Boundary geschossübergreifend' => ['invalid/10-room-boundary-cross-storey.json', 'room.boundary_cross_storey'],
            'falscher Umlaufsinn' => ['invalid/11-room-wrong-winding.json', 'room.wrong_winding'],
            'Boundary selbstschneidend' => ['invalid/12-room-self-intersect.json', 'room.boundary_self_intersect'],
            'widersprüchliche Höhen' => ['invalid/13-conflicting-heights.json', 'height.conflict'],
            'Millimeter als Float' => ['invalid/14-float-millimeter.json', 'unit.float_length'],
            'Toleranzgrenzfall vs. TopologieGate' => ['invalid/15-tolerance-borderline-vs-topologiegate.json', 'room.boundary_invalid_topology'],
            'Scan automatisch bestätigt' => ['invalid/16-scan-confirmed.json', 'source.unverified_confirmed'],
            'unbekannte Major-Version' => ['invalid/17-unknown-major-version.json', 'schema.unsupported_major_version'],
        ];
    }

    /** @dataProvider ungueltigeFixtures */
    public function test_ungueltige_fixtures_liefern_erwarteten_fehlercode(string $rel, string $code): void
    {
        $res = $this->validator()->validate($this->fixture($rel));
        $this->assertFalse($res->isValid(), "Erwartet ungültig ($code), war aber valide.");
        $this->assertTrue(
            $res->hasErrorCode($code),
            "Erwarteter Fehlercode '$code' fehlt. Vorhanden: ".json_encode($res->errorCodes())
        );
    }

    public function test_unbekannter_nordbezug_wird_nicht_still_zu_null_winkel(): void
    {
        $model = $this->fixture('valid/09-unknown-north.json');
        $this->assertNull($model['north_angle_deg']);
        // Gegenprobe: north_source=unknown MIT Winkel 0 → Fehler (kein stiller 0°-Nordbezug).
        $model['north_angle_deg'] = 0;
        $res = $this->validator()->validate($model);
        $this->assertTrue($res->hasErrorCode('north.unknown_with_value'));
    }

    public function test_fehlervertrag_enthaelt_code_path_message_severity(): void
    {
        $res = $this->validator()->validate($this->fixture('invalid/01-wall-missing-reference-line-type.json'));
        $issue = $res->errors()[0];
        foreach (['code', 'path', 'message', 'severity'] as $k) {
            $this->assertArrayHasKey($k, $issue);
        }
        $this->assertSame('error', $issue['severity']);
    }
}
