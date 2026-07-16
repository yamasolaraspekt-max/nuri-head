<?php

namespace Tests\Feature\BuildingModel;

use App\Models\BuildingModelVersion;
use App\Services\BuildingModel\CanonicalModelInvalidException;
use App\Services\BuildingModel\DerivedBuildingModelVersionStore;
use App\Services\BuildingModel\ProjectionConflictException;
use App\Services\BuildingModel\SourceGeometryRef;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * G1b-1 — Derived-only Store: Validierung vor Speicherung, Idempotenz, Projektionskonflikt, Scope-Treue.
 * Nutzt die dedizierte Worktree-Test-DB (RefreshDatabase).
 */
class BuildingModelVersionStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string,mixed> */
    private function payload(string $rel = 'valid/01-rectangle-four-axis-walls.json'): array
    {
        return json_decode((string) file_get_contents(__DIR__.'/../../Fixtures/building-model/'.$rel), true, 512, JSON_THROW_ON_ERROR);
    }

    private function source(array $payload, array $roh = ['raeume' => []]): SourceGeometryRef
    {
        return new SourceGeometryRef(
            objectId: (int) $payload['object_id'],
            profileId: 4711,
            version: 1,
            sourceGeometry: $roh,
        );
    }

    private function store(): DerivedBuildingModelVersionStore
    {
        return new DerivedBuildingModelVersionStore;
    }

    public function test_gueltiger_payload_wird_als_derived_only_gespeichert(): void
    {
        $p = $this->payload();
        $version = $this->store()->projiziere($p, $this->source($p));

        $this->assertDatabaseCount('building_model_versions', 1);
        $this->assertSame(BuildingModelVersion::ROLE_DERIVED_ONLY, $version->projection_role);
        $this->assertSame('valid', $version->validation_status);
        $this->assertSame('gebaeude_geometrie', $version->source_type);
        $this->assertSame($p['revision_id'], $version->revision_id);      // Revisions-ID aus dem Payload
        $this->assertNotSame($p['revision_id'], $version->id);            // PK ist surrogat (nicht payload-gekoppelt)
        $this->assertSame($p['model_id'], $version->model_id);
    }

    public function test_ungueltiger_payload_wird_nicht_gespeichert(): void
    {
        $p = $this->payload();
        unset($p['buildings'][0]['storeys'][0]['walls'][0]['reference_line_type']); // A1-Verstoß

        try {
            $this->store()->projiziere($p, $this->source($p));
            $this->fail('Erwartete CanonicalModelInvalidException.');
        } catch (CanonicalModelInvalidException $e) {
            $this->assertTrue($e->result()->hasErrorCode('wall.missing_reference_line_type'));
        }
        $this->assertDatabaseCount('building_model_versions', 0);
    }

    public function test_idempotenz_gleicher_schluessel_und_hash_liefert_dieselbe_zeile(): void
    {
        $p = $this->payload();
        $src = $this->source($p);
        $a = $this->store()->projiziere($p, $src);
        $b = $this->store()->projiziere($p, $src);

        $this->assertSame($a->id, $b->id);
        $this->assertDatabaseCount('building_model_versions', 1);
    }

    public function test_gleicher_schluessel_anderer_payload_ist_konflikt(): void
    {
        $p = $this->payload();
        $src = $this->source($p);
        $this->store()->projiziere($p, $src);

        // gleicher Source-/Schema-/Projektionsschlüssel, aber abweichender Payload
        $p2 = $p;
        $p2['buildings'][0]['storeys'][0]['walls'][0]['thickness_mm'] = 300;

        $this->expectException(ProjectionConflictException::class);
        try {
            $this->store()->projiziere($p2, $src);
        } finally {
            $this->assertDatabaseCount('building_model_versions', 1); // keine Überschreibung, kein Duplikat
        }
    }

    public function test_store_veraendert_keine_fremde_tabelle(): void
    {
        // gebaeude_geometrie liegt auf anforderungsprofile — der Store fasst diese Tabelle nicht an.
        $vorher = Schema::hasTable('anforderungsprofile') ? DB::table('anforderungsprofile')->count() : 0;
        $p = $this->payload();
        $this->store()->projiziere($p, $this->source($p));
        $nachher = Schema::hasTable('anforderungsprofile') ? DB::table('anforderungsprofile')->count() : 0;

        $this->assertSame($vorher, $nachher, 'Store darf anforderungsprofile/gebaeude_geometrie nicht verändern.');
        $this->assertDatabaseCount('building_model_versions', 1);
    }
}
