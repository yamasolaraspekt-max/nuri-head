<?php

namespace Tests\Feature\Hausplaner;

use App\Models\LeadAlternativeAdd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * B2-Regression — Speichern darf die Szene NICHT beschneiden.
 *
 * Vorher (Bug): HausplanerController::speichern gab die per $request->validate() BESCHNITTENE Szene
 * an die Action → nicht validierte Schlüssel (scene.roofs, künftige v3-Felder) fielen still weg,
 * HTTP 200 + neue Revision, Dach verloren. Fix: Gate validiert Invarianten, PERSISTIERT wird die
 * ungeschnittene Nutzlast aus $request->input('scene').
 *
 * Läuft gegen die Test-DB (RefreshDatabase); die Arbeits-DB `ticket` wird NICHT geschrieben.
 */
class HausplanerSpeichernNutzlastTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);
    }

    private function objekt(int $seed = 500): int
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        DB::table('new_leads')->insert(['id' => $customer, 'customer_type' => 'privat', 'name' => 'K', 'lastname' => 'T', 'email' => 'k@example.com', 'phone' => '0', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $alt, 'lead_id' => $customer, 'street' => 'Weg 1', 'postcode' => '12345', 'city' => 'S', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('hausplaner_documents')->insert([
            'alternative_id' => $alt, 'schema_version' => 1, 'revision' => 1,
            'scene_json' => json_encode(['schemaVersion' => 1, 'units' => 'mm', 'revision' => 1, 'levels' => [['id' => 'L', 'sortOrder' => 0, 'defaultWallHeight' => 2500]], 'nodes' => []]),
            'checksum' => 't', 'created_by' => null, 'updated_by' => null, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return $alt;
    }

    /** @return array<string,mixed> */
    private function v2SzeneMitDach(): array
    {
        return [
            'schemaVersion' => 2,
            'units' => 'mm',
            'revision' => 1,
            'levels' => [['id' => 'L', 'name' => 'EG', 'elevation' => 0, 'defaultWallHeight' => 2500, 'floorThickness' => 200, 'sortOrder' => 0]],
            'nodes' => [],
            'roofs' => [[
                'id' => 'r1', 'type' => 'roof', 'levelId' => 'L', 'visible' => true, 'locked' => false, 'tags' => [],
                'createdAt' => '2026-07-19T00:00:00.000Z', 'updatedAt' => '2026-07-19T00:00:00.000Z',
                'polygon' => [['x' => 0, 'y' => 0], ['x' => 8000, 'y' => 0], ['x' => 8000, 'y' => 10000], ['x' => 0, 'y' => 10000]],
                'roofType' => 'sattel', 'neigungGrad' => 35, 'firstAzimutGrad' => 90, 'ueberstandMm' => 500, 'traufhoeheMm' => 2500,
            ]],
            'zukunft_v3_probe' => ['unbekanntes' => 'feld'], // Gegen-Beweis: kein roofs-only-Whitelist-Pflaster
        ];
    }

    /** B2-Kern: roofs überlebt das Speichern (wird NICHT beschnitten). */
    public function test_speichern_persistiert_roofs_ungeschnitten(): void
    {
        $alt = $this->objekt();
        $admin = User::factory()->create(['password' => 'password', 'is_admin' => 1]);

        $res = $this->actingAs($admin)->putJson("/admin/hausplaner/objekt/{$alt}/dokument", [
            'base_revision' => 1,
            'schema_version' => 2,
            'scene' => $this->v2SzeneMitDach(),
        ]);

        $res->assertOk(); // HTTP 200

        $doc = DB::table('hausplaner_documents')->where('alternative_id', $alt)->first();
        $scene = json_decode($doc->scene_json, true);

        // Kern: roofs ist da und vollständig.
        $this->assertArrayHasKey('roofs', $scene, 'roofs wurde beim Speichern beschnitten (B2-Regression)');
        $this->assertCount(1, $scene['roofs'], 'roofs-Eintrag fehlt');
        $this->assertSame('r1', $scene['roofs'][0]['id']);
        $this->assertSame('sattel', $scene['roofs'][0]['roofType']);
        // Spalte + Revision korrekt.
        $this->assertSame(2, (int) $doc->schema_version);
        $this->assertSame(2, (int) $doc->revision);
        // Gegen-Beweis: auch ein nicht validiertes Zukunftsfeld überlebt (keine roofs-only-Whitelist).
        $this->assertArrayHasKey('zukunft_v3_probe', $scene, 'Fix heilt nur roofs statt der Nutzlast-Weitergabe');
    }

    /** P2: uebergrosse Szene wird abgelehnt (422), Dokument bleibt unveraendert. */
    public function test_uebergrosse_szene_wird_abgelehnt(): void
    {
        $alt = $this->objekt(540);
        $admin = User::factory()->create(['password' => 'password', 'is_admin' => 1]);
        $scene = $this->v2SzeneMitDach();
        $scene['muell'] = str_repeat('x', 2_100_000); // > MAX_SCENE_BYTES (2 MB)

        $this->actingAs($admin)->putJson("/admin/hausplaner/objekt/{$alt}/dokument", [
            'base_revision' => 1, 'schema_version' => 2, 'scene' => $scene,
        ])->assertStatus(422);

        $doc = DB::table('hausplaner_documents')->where('alternative_id', $alt)->first();
        $this->assertSame(1, (int) $doc->revision, 'Dokument darf bei zu grosser Szene nicht veraendert werden');
    }

    /** Kante bleibt: unbekannte schemaVersion → 422 (Gate nicht zu weit geöffnet). */
    public function test_unbekannte_schema_version_bleibt_422(): void
    {
        $alt = $this->objekt(520);
        $admin = User::factory()->create(['password' => 'password', 'is_admin' => 1]);
        $scene = $this->v2SzeneMitDach();
        $scene['schemaVersion'] = 3;

        $this->actingAs($admin)->putJson("/admin/hausplaner/objekt/{$alt}/dokument", [
            'base_revision' => 1, 'schema_version' => 3, 'scene' => $scene,
        ])->assertStatus(422);
    }
}
