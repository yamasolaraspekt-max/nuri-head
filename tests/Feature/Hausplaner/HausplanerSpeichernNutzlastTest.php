<?php

namespace Tests\Feature\Hausplaner;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** P2-Vertrag: Nur eine vollständig Zod-ladbare v2-Szene darf persistiert werden. */
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

    private function objekt(int $seed = 500, int $revision = 1): int
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        DB::table('new_leads')->insert(['id' => $customer, 'customer_type' => 'privat', 'name' => 'K', 'lastname' => 'T', 'email' => "k{$seed}@example.com", 'phone' => '0', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $alt, 'lead_id' => $customer, 'street' => 'Weg 1', 'postcode' => '12345', 'city' => 'S', 'created_at' => now(), 'updated_at' => now()]);
        $scene = $this->v2SzeneMitDach($alt);
        $scene['revision'] = $revision;
        DB::table('hausplaner_documents')->insert([
            'alternative_id' => $alt,
            // Z-06-N1: Spalte und Szene tragen dieselbe Version. Ein Fixture, dessen Spalte 2
            // sagt und dessen `scene_json` 3 ist, widerspricht sich selbst — und der Widerspruch
            // wandert in jede Zusage, die darauf aufsetzt.
            'schema_version' => 3,
            'revision' => $revision,
            'scene_json' => json_encode($scene),
            'checksum' => 'checksum-vorher',
            'created_by' => null,
            'updated_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $alt;
    }

    /** @return array<string, mixed> */
    private function basisNode(string $id, string $type): array
    {
        return [
            'id' => $id,
            'type' => $type,
            'levelId' => 'L',
            'visible' => true,
            'locked' => false,
            'tags' => [],
            'createdAt' => '2026-07-19T00:00:00.000Z',
            'updatedAt' => '2026-07-19T00:00:00.000Z',
        ];
    }

    /** @return array<string, mixed> */
    private function v2SzeneMitDach(int $alt): array
    {
        return [
            'id' => "doc-{$alt}",
            'projectId' => $alt,
            'schemaVersion' => 3,
            'revision' => 1,
            'units' => 'mm',
            'settings' => ['gridSize' => 100, 'snapEnabled' => true, 'angleSnap' => 15],
            'levels' => [['id' => 'L', 'name' => 'EG', 'elevation' => 0, 'defaultWallHeight' => 2500, 'floorThickness' => 200, 'sortOrder' => 0]],
            'nodes' => [[
                ...$this->basisNode('w1', 'wall'),
                'start' => ['x' => 0, 'y' => 0],
                'end' => ['x' => 8000, 'y' => 0],
                'thickness' => 240,
                'height' => 2500,
            ]],
            'materials' => [],
            'roofs' => [[
                ...$this->basisNode('r1', 'roof'),
                'polygon' => [['x' => 0, 'y' => 0], ['x' => 8000, 'y' => 0], ['x' => 8000, 'y' => 10000], ['x' => 0, 'y' => 10000]],
                'roofType' => 'sattel',
                'neigungGrad' => 35,
                'firstAzimutGrad' => 90,
                'ueberstandMm' => 500,
                'traufhoeheMm' => 2500,
                // Z-06-N1 (B10): Pflichtfelder ab v3. Testgeometrie ist gesetzt, nicht geraten.
                'geometrieHerkunft' => 'manuell',
                'freigabe' => 'bestaetigt',
            ]],
            'metadata' => ['createdAt' => '2026-07-19T00:00:00.000Z', 'updatedAt' => '2026-07-19T00:00:00.000Z'],
        ];
    }

    private function user(): User
    {
        return User::factory()->create(['password' => 'password', 'is_admin' => 1]);
    }

    /** @return array<string, mixed> */
    private function dokumentZeile(int $alt): array
    {
        return (array) DB::table('hausplaner_documents')->where('alternative_id', $alt)->first();
    }

    /** @param array<string, mixed> $scene */
    private function speichere(int $alt, array $scene, int $baseRevision = 1, int $schemaVersion = 3)
    {
        return $this->actingAs($this->user())->putJson("/admin/hausplaner/objekt/{$alt}/dokument", [
            'base_revision' => $baseRevision,
            'schema_version' => $schemaVersion,
            'scene' => $scene,
        ]);
    }

    /** @param callable(array<string, mixed>): void $veraendere */
    private function assert422OhneMutation(int $seed, callable $veraendere, int $schemaVersion = 2): void
    {
        $alt = $this->objekt($seed);
        $vorher = $this->dokumentZeile($alt);
        $scene = $this->v2SzeneMitDach($alt);
        $veraendere($scene);

        $this->speichere($alt, $scene, 1, $schemaVersion)->assertStatus(422);

        $this->assertSame($vorher, $this->dokumentZeile($alt), '422 darf scene_json, Revision und Checksum nicht verändern.');
    }

    public function test_gueltige_v2_dachszene_wird_vollstaendig_persistiert(): void
    {
        $alt = $this->objekt();
        $scene = $this->v2SzeneMitDach($alt);

        $antwort = $this->speichere($alt, $scene)->assertOk();
        $doc = $this->dokumentZeile($alt);
        $gespeichert = json_decode($doc['scene_json'], true);
        $erwartet = $scene;
        $erwartet['revision'] = 2;

        $this->assertEquals($erwartet, $gespeichert, 'Die validierte Szene muss vollständig und unbeschnitten persistiert werden.');
        // Z-06-N1: die Spalte FOLGT der Szene (`SpeichereHausplanerDokument:39`), und die Szene
        // ist seit dem N1-Bau v3. Die Zusage misst weiterhin dasselbe — dass Spalte und Szene
        // nicht auseinanderlaufen —, nur gegen die Version, die heute gilt.
        $this->assertSame(3, (int) $doc['schema_version']);
        $this->assertSame((int) $gespeichert['schemaVersion'], (int) $doc['schema_version'],
            'Spalte und Szene müssen dieselbe Schema-Version tragen.');
        $this->assertSame(2, (int) $doc['revision']);
        $this->assertSame($antwort->json('checksum'), $doc['checksum']);
    }

    public function test_unbekanntes_zukunftsfeld_ohne_schemawechsel_wird_abgelehnt(): void
    {
        $this->assert422OhneMutation(510, fn (array &$scene) => $scene['zukunft_v3_probe'] = ['neu' => true]);
    }

    public function test_float_millimeter_wird_abgelehnt(): void
    {
        $this->assert422OhneMutation(520, fn (array &$scene) => $scene['nodes'][0]['end']['x'] = 8000.5);
    }

    public function test_unbekannter_node_typ_wird_abgelehnt(): void
    {
        $this->assert422OhneMutation(530, fn (array &$scene) => $scene['nodes'][0]['type'] = 'mystery');
    }

    public function test_nullwand_wird_abgelehnt(): void
    {
        $this->assert422OhneMutation(540, fn (array &$scene) => $scene['nodes'][0]['end'] = $scene['nodes'][0]['start']);
    }

    public function test_verwaiste_oeffnung_wird_abgelehnt(): void
    {
        $this->assert422OhneMutation(550, function (array &$scene): void {
            $scene['nodes'][] = [
                ...$this->basisNode('f1', 'window'),
                'hostWallId' => 'nicht-da',
                'offsetFromWallStart' => 1000,
                'width' => 1200,
                'height' => 1400,
                'sillHeight' => 900,
            ];
        });
    }

    public function test_ueberstehende_oeffnung_wird_abgelehnt(): void
    {
        $this->assert422OhneMutation(560, function (array &$scene): void {
            $scene['nodes'][] = [
                ...$this->basisNode('f1', 'window'),
                'hostWallId' => 'w1',
                'offsetFromWallStart' => 7500,
                'width' => 1200,
                'height' => 1400,
                'sillHeight' => 900,
            ];
        });
    }

    public function test_fremde_project_id_wird_abgelehnt(): void
    {
        $this->assert422OhneMutation(570, fn (array &$scene) => $scene['projectId']++);
    }

    public function test_huellen_und_szenen_version_duerfen_nicht_abweichen(): void
    {
        $this->assert422OhneMutation(580, static function (array &$scene): void {}, 1);
    }

    public function test_huellen_und_szenen_revision_duerfen_nicht_abweichen(): void
    {
        $this->assert422OhneMutation(590, fn (array &$scene) => $scene['revision'] = 2);
    }

    public function test_dokument_id_darf_nicht_gewechselt_werden(): void
    {
        $this->assert422OhneMutation(600, fn (array &$scene) => $scene['id'] = 'anderes-dokument');
    }

    public function test_uebergrosse_szene_wird_ohne_mutation_abgelehnt(): void
    {
        $this->assert422OhneMutation(610, fn (array &$scene) => $scene['muell'] = str_repeat('x', 2_100_000));
    }

    public function test_revisionskonflikt_bleibt_409_und_schreibt_nichts(): void
    {
        $alt = $this->objekt(620, 2);
        $vorher = $this->dokumentZeile($alt);
        $scene = $this->v2SzeneMitDach($alt);

        $this->speichere($alt, $scene, 1)->assertStatus(409)->assertJson(['aktuelle_revision' => 2]);

        $this->assertSame($vorher, $this->dokumentZeile($alt), '409 darf scene_json, Revision und Checksum nicht verändern.');
    }

    /**
     * Z-06-N1 — **die Versions-Schranke selbst, nicht nur ihre Wirkung nebenbei.**
     *
     * Die Mutationsprobe hat es gezeigt: `in:3` auf `in:2,3` gedreht liess alle dreizehn Zusagen
     * gruen. *Keine einzige bewachte die Regel — sie profitierten nur davon, dass die Nutzlast
     * zufaellig v3 war.* Ohne diese Zusage kann die Schranke lautlos aufgehen.
     */
    public function test_v2_nutzlast_wird_mit_versions_fehler_abgewiesen(): void
    {
        $alt = $this->objekt(600);
        $scene = $this->v2SzeneMitDach($alt);
        $scene['schemaVersion'] = 2;
        unset($scene['roofs'][0]['geometrieHerkunft'], $scene['roofs'][0]['freigabe']);

        $antwort = $this->speichere($alt, $scene, 1, 2);

        $antwort->assertStatus(422);
        $antwort->assertJsonValidationErrors('schema_version');
    }

    /** Und die Umkehrung: eine v3-Nutzlast mit v2-Huelle faellt ebenfalls, nicht nur die Huelle. */
    public function test_huelle_und_szene_muessen_dieselbe_version_tragen(): void
    {
        $alt = $this->objekt(610);

        $this->speichere($alt, $this->v2SzeneMitDach($alt), 1, 2)
            ->assertStatus(422)
            ->assertJsonValidationErrors('schema_version');
    }
}
