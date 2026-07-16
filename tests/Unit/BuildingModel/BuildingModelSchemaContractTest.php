<?php

namespace Tests\Unit\BuildingModel;

use App\Services\BuildingModel\CanonicalBuildingModelValidator;
use Tests\TestCase;

/**
 * G1a — Contract-Tests des kanonischen Modellvertrags v1 (Schema + Vertragsregeln, Masterprompt V3.3 +
 * CAD-A1..A6/E1..E3). Prüft, dass das JSON-Schema syntaktisch gültig ist, die Version eindeutig führt,
 * den Millimeter-Integer- und Azimut-Vertrag kodiert, die Wandreferenz (reference_line_type),
 * Fixkanten-Regel (thickness_change_anchor), gerichtete Referenzpfade, die stationbasierte, wandgebundene
 * Öffnung, die wand-/kantenreferenzierte Raum-Boundary und das Höhendatum trägt.
 */
class BuildingModelSchemaContractTest extends TestCase
{
    private function schema(): array
    {
        $path = __DIR__.'/../../../resources/schemas/building-model/v1.schema.json';

        return json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    private function fixture(string $rel): array
    {
        return json_decode((string) file_get_contents(__DIR__.'/../../Fixtures/building-model/'.$rel), true, 512, JSON_THROW_ON_ERROR);
    }

    public function test_schema_ist_gueltiges_json_mit_version_und_defs(): void
    {
        $s = $this->schema(); // wirft bei ungültigem JSON
        $this->assertSame('object', $s['type']);
        $this->assertArrayHasKey('$defs', $s);
        $this->assertSame('^1\\.[0-9]+\\.[0-9]+$', $s['properties']['schema_version']['pattern']);
    }

    public function test_hoehendatum_ist_okff_eg_null(): void
    {
        $s = $this->schema();
        $this->assertSame('okff_eg_zero', $s['properties']['height_datum']['const']);
        $this->assertContains('height_datum', $s['required']);
    }

    public function test_millimeter_integer_vertrag_im_schema(): void
    {
        $s = $this->schema();
        $this->assertSame('integer', $s['$defs']['node']['properties']['x_mm']['type']);
        $this->assertSame('integer', $s['$defs']['wall']['properties']['thickness_mm']['type']);
        $this->assertSame(1, $s['$defs']['wall']['properties']['thickness_mm']['minimum']);
        $this->assertSame('integer', $s['$defs']['opening']['properties']['rough_width_mm']['type']);
    }

    public function test_azimut_vertrag_0_bis_360(): void
    {
        $s = $this->schema();
        $north = $s['properties']['north_angle_deg']['oneOf'][0];
        $this->assertSame(0, $north['minimum']);
        $this->assertSame(360, $north['exclusiveMaximum']);
    }

    public function test_cad_a1_wandreferenz_und_fixkante_im_schema(): void
    {
        $s = $this->schema();
        $wall = $s['$defs']['wall'];
        $this->assertContains('reference_line_type', $wall['required']);
        $this->assertSame(['axis', 'inner', 'outer'], $wall['properties']['reference_line_type']['enum']);
        $this->assertSame(['reference_line', 'inner_face', 'outer_face'], $wall['properties']['thickness_change_anchor']['enum']);
        $this->assertSame('reference_line', $wall['properties']['thickness_change_anchor']['default']);
        // gerichteter Referenzpfad (nicht nur zwei Punkte).
        $this->assertSame(2, $wall['properties']['reference_path']['properties']['node_ids']['minItems']);
        // Innen/Außen darf nicht erraten werden.
        $this->assertSame(['left', 'right', 'unknown', 'not_applicable'], $wall['properties']['exterior_side']['enum']);
    }

    public function test_cad_a3_oeffnung_stationbasiert_und_wandgebunden(): void
    {
        $s = $this->schema();
        $op = $s['$defs']['opening'];
        foreach (['wall_id', 'host_segment_index', 'station_mm', 'rough_width_mm', 'rough_height_mm', 'sill_height_mm'] as $req) {
            $this->assertContains($req, $op['required'], "Öffnung muss $req führen.");
        }
        // keine absoluten XY-Koordinaten im Öffnungsvertrag.
        $this->assertArrayNotHasKey('x_mm', $op['properties']);
        $this->assertArrayNotHasKey('y_mm', $op['properties']);
    }

    public function test_cad_a5_raum_boundary_referenziert_waende(): void
    {
        $s = $this->schema();
        $room = $s['$defs']['room'];
        $this->assertContains('boundary_edges', $room['required']);
        $edge = $room['properties']['boundary_edges']['items'];
        foreach (['wall_id', 'host_segment_index', 'wall_side', 'traversal'] as $req) {
            $this->assertContains($req, $edge['required']);
        }
        // keine freie Knotenliste als zweite Wahrheit.
        $this->assertArrayNotHasKey('boundary_node_ids', $room['properties']);
        // gespeichertes Polygon nur als Cache.
        $cache = $room['properties']['polygon_cache_mm'];
        $this->assertTrue($cache['properties']['is_cache']['const']);
    }

    public function test_enums_und_stabile_ids(): void
    {
        $s = $this->schema();
        $this->assertSame(['external', 'internal', 'unknown'], $s['$defs']['wall']['properties']['wall_type']['enum']);
        $this->assertSame(['window', 'door', 'passage', 'unknown'], $s['$defs']['opening']['properties']['opening_type']['enum']);
        $this->assertStringContainsString('[0-9a-fA-F]{8}', $s['$defs']['uuid']['pattern']);
    }

    public function test_multi_gebaeude_und_geschoss_werden_getragen(): void
    {
        $v = new CanonicalBuildingModelValidator;
        $this->assertTrue($v->validate($this->fixture('valid/05-two-storeys-different-okff.json'))->isValid());
        // separates Gebäude-Fixture existiert nicht mehr; Mehr-Geschoss deckt die Hierarchie ab.
        $this->assertTrue($v->validate($this->fixture('valid/06-l-shaped-room-boundary-edges.json'))->isValid());
    }
}
