<?php

namespace Tests\Feature\Hausplaner;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Domain\Hausplaner\Models\HausplanerDocument;

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
            'schema_version' => HausplanerDocument::SCHEMA_VERSION,
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
            'schemaVersion' => HausplanerDocument::SCHEMA_VERSION,
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
    // Z1-E4-1: der Vorgabewert folgt der Konstante statt einer Kopie — beim Sprung 3→4 hat
    // genau diese getippte 3 alle Speicher-Zusagen auf 422 gedreht.
    private function speichere(int $alt, array $scene, int $baseRevision = 1, ?int $schemaVersion = null)
    {
        return $this->actingAs($this->user())->putJson("/admin/hausplaner/objekt/{$alt}/dokument", [
            'base_revision' => $baseRevision,
            'schema_version' => $schemaVersion ?? HausplanerDocument::SCHEMA_VERSION,
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
        $this->assertSame(HausplanerDocument::SCHEMA_VERSION, (int) $doc['schema_version']);
        $this->assertSame((int) $gespeichert['schemaVersion'], (int) $doc['schema_version'],
            'Spalte und Szene müssen dieselbe Schema-Version tragen.');
        $this->assertSame(2, (int) $doc['revision']);
        $this->assertSame($antwort->json('checksum'), $doc['checksum']);
    }

    /**
     * **Z1-E4-1 (d) — die Bodenplatte erreicht den Speicherweg.**
     *
     * Die Absage-Regel des Blattes zu Kriterium (i) nennt den Grund: *„Ein neues Modellfeld ohne
     * PHP-Test erreicht den Speicherweg nicht — und der Speicherweg ist bei einem Schema-Sprung
     * die riskanteste Stelle."*
     *
     * Geprueft wird 200 UND die vollstaendige Persistenz: ein Feld, das der Validator durchlaesst
     * und die Ablage danach abschneidet, waere gruen und trotzdem verloren.
     */
    public function test_szene_mit_bodenplatte_wird_gespeichert_und_vollstaendig_persistiert(): void
    {
        $alt = $this->objekt(560);
        $scene = $this->v2SzeneMitDach($alt);
        $scene['foundationSlabs'] = [[
            ...$this->basisNode('b1', 'foundation_slab'),
            'polygon' => [['x' => 0, 'y' => 0], ['x' => 8000, 'y' => 0], ['x' => 8000, 'y' => 10000], ['x' => 0, 'y' => 10000]],
            'dickeMm' => 250,
            // NEGATIV — Yamas Bezugshoehe (22.08. 22:08). Genau der Wert, den ein `mmPos` im
            // Zod-Schema abgelehnt haette, und deshalb steht er hier und nicht eine 180.
            'oberkanteMm' => -180,
            'erdberuehrt' => true,
            'schichten' => [['materialId' => 'daemmung', 'dickeMm' => 120], ['materialId' => 'estrich', 'dickeMm' => 60]],
            'geometrieHerkunft' => 'manuell',
            'freigabe' => 'bestaetigt',
        ]];

        $this->speichere($alt, $scene)->assertOk();

        $gespeichert = json_decode($this->dokumentZeile($alt)['scene_json'], true);
        $erwartet = $scene;
        $erwartet['revision'] = 2;
        $this->assertEquals($erwartet, $gespeichert, 'Die Bodenplatte muss unbeschnitten persistiert werden.');
        $this->assertSame(-180, $gespeichert['foundationSlabs'][0]['oberkanteMm'],
            'Das Vorzeichen der Hoehenkote hat den Speicherweg nicht ueberlebt.');
    }

    /**
     * **Z1-E4-1 (d) — und der Bestand bleibt unberuehrt.**
     *
     * Ein Dokument OHNE die Sammlung muss weiterhin 200 liefern. *Waere `foundationSlabs`
     * pflichtig geworden, waere jedes Bestandsdokument ab sofort ein 422* — und der Fehler faellt
     * genau dort auf, wo niemand mehr hinsieht: beim Speichern eines alten Hauses.
     */
    public function test_szene_ohne_bodenplatte_bleibt_speicherbar(): void
    {
        $alt = $this->objekt(570);
        $scene = $this->v2SzeneMitDach($alt);
        $this->assertArrayNotHasKey('foundationSlabs', $scene, 'die Vorlage traegt schon eine Platte — die Zusage misst dann nichts');

        $this->speichere($alt, $scene)->assertOk();

        $gespeichert = json_decode($this->dokumentZeile($alt)['scene_json'], true);
        $this->assertArrayNotHasKey('foundationSlabs', $gespeichert,
            'dem Bestandsdokument wurde eine leere Sammlung untergeschoben — leer heisst nicht erfasst, nicht "es gibt keine"');
    }

    /**
     * **Z1-E4-1 (d2) — die Spiegelung, je Sammlung eine Zusage.**
     *
     * Der Dirigent, 22:52, in Yamas Namen: *„Bauform: ein TEST, der die Spiegelung prueft — kein
     * Kommentar, der sie behauptet."* Genau deshalb steht der Fall hier dreimal und nicht einmal
     * mit einer Schleife: **faellt eine Sammlung aus der Spiegelung, soll GENAU sie rot werden**,
     * nicht „irgendeine der drei".
     *
     * **Die Huellenversion ist die AKTUELLE.** Mit einer alten Version käme der 422 aus der
     * Versionsregel (`in:SCHEMA_VERSION`) und die Zusage waere grün, ohne die Level-Pruefung je
     * berührt zu haben — ein Test, der aus dem falschen Grund besteht.
     *
     * @param array<string, mixed> $eintrag
     */
    private function assert422WegenUnbekanntemLevel(int $seed, string $sammlung, array $eintrag, string $bezeichnung): void
    {
        $alt = $this->objekt($seed);
        $vorher = $this->dokumentZeile($alt);
        $scene = $this->v2SzeneMitDach($alt);
        $scene[$sammlung] = [$eintrag];

        $antwort = $this->speichere($alt, $scene)->assertStatus(422);

        $this->assertStringContainsString('unbekanntes Level', json_encode($antwort->json()),
            "Der 422 nennt den Grund nicht — er koennte aus einer ganz anderen Regel stammen.");
        $this->assertStringContainsString($bezeichnung, json_encode($antwort->json()),
            "Die Meldung nennt die Sammlung nicht beim Namen ({$bezeichnung}).");
        $this->assertSame($vorher, $this->dokumentZeile($alt), '422 darf nichts veraendert haben.');
    }

    public function test_decke_auf_unbekanntem_level_wird_abgelehnt(): void
    {
        $this->assert422WegenUnbekanntemLevel(580, 'ceilings', [
            ...$this->basisNode('c1', 'ceiling'),
            'levelId' => 'gibt-es-nicht',
            'polygon' => [['x' => 0, 'y' => 0], ['x' => 8000, 'y' => 0], ['x' => 8000, 'y' => 10000]],
            'dickeMm' => 200,
            'geometrieHerkunft' => 'manuell', 'freigabe' => 'bestaetigt',
        ], 'Decke');
    }

    public function test_dach_auf_unbekanntem_level_wird_abgelehnt(): void
    {
        $this->assert422WegenUnbekanntemLevel(590, 'roofs', [
            ...$this->basisNode('r9', 'roof'),
            'levelId' => 'gibt-es-nicht',
            'polygon' => [['x' => 0, 'y' => 0], ['x' => 8000, 'y' => 0], ['x' => 8000, 'y' => 10000]],
            'roofType' => 'sattel', 'neigungGrad' => 35, 'firstAzimutGrad' => 90,
            'ueberstandMm' => 500, 'traufhoeheMm' => 2500,
            'geometrieHerkunft' => 'manuell', 'freigabe' => 'bestaetigt',
        ], 'Dach');
    }

    public function test_bodenplatte_auf_unbekanntem_level_wird_abgelehnt(): void
    {
        $this->assert422WegenUnbekanntemLevel(600, 'foundationSlabs', [
            ...$this->basisNode('b9', 'foundation_slab'),
            'levelId' => 'gibt-es-nicht',
            'polygon' => [['x' => 0, 'y' => 0], ['x' => 8000, 'y' => 0], ['x' => 8000, 'y' => 10000]],
            'dickeMm' => 250, 'oberkanteMm' => -180, 'erdberuehrt' => true,
            'geometrieHerkunft' => 'manuell', 'freigabe' => 'bestaetigt',
        ], 'Bodenplatte');
    }

    /**
     * **Die Gegenprobe zu den drei — sonst waere „422" auch mit einer Sammlung erfuellt, die
     * IMMER abgelehnt wird.** Dieselben drei Eintraege auf einem EXISTIERENDEN Level gehen durch.
     */
    public function test_alle_drei_sammlungen_auf_bekanntem_level_gehen_durch(): void
    {
        $alt = $this->objekt(610);
        $scene = $this->v2SzeneMitDach($alt);
        $scene['ceilings'] = [[
            ...$this->basisNode('c1', 'ceiling'), 'polygon' => [['x' => 0, 'y' => 0], ['x' => 8000, 'y' => 0], ['x' => 8000, 'y' => 10000]],
            'dickeMm' => 200, 'geometrieHerkunft' => 'manuell', 'freigabe' => 'bestaetigt',
        ]];
        $scene['foundationSlabs'] = [[
            ...$this->basisNode('b1', 'foundation_slab'), 'polygon' => [['x' => 0, 'y' => 0], ['x' => 8000, 'y' => 0], ['x' => 8000, 'y' => 10000]],
            'dickeMm' => 250, 'oberkanteMm' => -180, 'erdberuehrt' => true,
            'geometrieHerkunft' => 'manuell', 'freigabe' => 'bestaetigt',
        ]];

        $this->speichere($alt, $scene)->assertOk();
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
