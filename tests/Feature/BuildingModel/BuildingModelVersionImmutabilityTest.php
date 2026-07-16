<?php

namespace Tests\Feature\BuildingModel;

use App\Models\BuildingModelVersion;
use App\Services\BuildingModel\BuildingModelVersionImmutableException;
use App\Services\BuildingModel\DerivedBuildingModelVersionStore;
use App\Services\BuildingModel\SourceGeometryRef;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * G1b-1 — Unveränderlichkeit + Migration von `building_model_versions`: Update/Delete werden abgewehrt,
 * neue Fassung erzeugt eine neue Version, kein Soft-Delete, kein updated_at; Tabelle existiert additiv.
 */
class BuildingModelVersionImmutabilityTest extends TestCase
{
    use RefreshDatabase;

    private function anlegen(int $profileId = 1, int $version = 1): BuildingModelVersion
    {
        $p = json_decode((string) file_get_contents(__DIR__.'/../../Fixtures/building-model/valid/01-rectangle-four-axis-walls.json'), true, 512, JSON_THROW_ON_ERROR);
        $src = new SourceGeometryRef(objectId: (int) $p['object_id'], profileId: $profileId, version: $version, sourceGeometry: ['raeume' => []]);

        return (new DerivedBuildingModelVersionStore)->projiziere($p, $src);
    }

    public function test_neue_version_anlegbar(): void
    {
        $v = $this->anlegen();
        $this->assertTrue($v->exists);
        $this->assertDatabaseCount('building_model_versions', 1);
    }

    public function test_update_wird_abgelehnt(): void
    {
        $v = $this->anlegen();
        $this->expectException(BuildingModelVersionImmutableException::class);
        $v->validation_status = 'geaendert';
        $v->save();
    }

    public function test_delete_wird_abgelehnt(): void
    {
        $v = $this->anlegen();
        $this->expectException(BuildingModelVersionImmutableException::class);
        $v->delete();
    }

    public function test_neue_revision_statt_update(): void
    {
        $this->anlegen(profileId: 1, version: 1);
        $this->anlegen(profileId: 1, version: 2); // andere Quellversion → neue Zeile
        $this->assertDatabaseCount('building_model_versions', 2);
    }

    public function test_tabelle_ist_additiv_ohne_softdelete_ohne_updated_at(): void
    {
        $this->assertTrue(Schema::hasTable('building_model_versions'));
        $this->assertTrue(Schema::hasColumns('building_model_versions', [
            'id', 'model_id', 'parent_revision_id', 'object_id',
            'source_type', 'source_profile_id', 'source_version', 'source_hash',
            'schema_version', 'projection_version', 'payload', 'payload_hash',
            'validation_status', 'warnings', 'projection_role', 'projected_at', 'created_at',
        ]));
        $this->assertFalse(Schema::hasColumn('building_model_versions', 'deleted_at'), 'Kein Soft-Delete.');
        $this->assertFalse(Schema::hasColumn('building_model_versions', 'updated_at'), 'Append-only: kein updated_at.');
    }
}
