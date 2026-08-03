<?php

namespace Tests\Feature\Hausplaner;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Z-06-N1, **vierte Naht** — der Snapshot-Rückweg führt die Schema-Version mit.
 *
 * Gefunden vom Evaluator (`96041e73`) bei einer Suche über die ganze Codebasis nach Stellen, die
 * die Schemaversion kennen. **Der Hinweg koppelt Spalte und Szene ausdrücklich**
 * (`SpeichereHausplanerDokument:39`, „Spalte folgt der Szene"); der Rückweg tat es nicht — er
 * schrieb `scene_json`, `revision` und `checksum` und liess `schema_version` stehen.
 *
 * **Vor dieser Datei gab es KEINE Zusage für `StelleSnapshotWieder`** (gemessen: 0 Treffer in
 * `tests/`). *Eine Aktion, die Bestandsdaten überschreibt und die niemand ausübt — genau die
 * Sorte Stelle, an der ein Bruch jahrelang unbemerkt bleibt.*
 */
class SnapshotRueckwegVersionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);
    }

    /** @return array<string, mixed> */
    private function szene(int $alt, int $version, int $revision = 1): array
    {
        $dach = [
            'id' => 'r1', 'type' => 'roof', 'levelId' => 'L', 'visible' => true, 'locked' => false,
            'tags' => [], 'createdAt' => '2026-07-19T00:00:00.000Z', 'updatedAt' => '2026-07-19T00:00:00.000Z',
            'polygon' => [['x' => 0, 'y' => 0], ['x' => 8000, 'y' => 0], ['x' => 8000, 'y' => 10000], ['x' => 0, 'y' => 10000]],
            'roofType' => 'sattel', 'neigungGrad' => 35, 'firstAzimutGrad' => 90,
            'ueberstandMm' => 500, 'traufhoeheMm' => 2500,
        ];
        if ($version >= 3) {
            $dach['geometrieHerkunft'] = 'manuell';
            $dach['freigabe'] = 'bestaetigt';
        }

        return [
            'id' => "doc-{$alt}", 'projectId' => $alt, 'schemaVersion' => $version,
            'revision' => $revision, 'units' => 'mm',
            'settings' => ['gridSize' => 100, 'snapEnabled' => true, 'angleSnap' => 15],
            'levels' => [['id' => 'L', 'name' => 'EG', 'elevation' => 0, 'defaultWallHeight' => 2500, 'floorThickness' => 200, 'sortOrder' => 0]],
            'nodes' => [], 'materials' => [], 'roofs' => [$dach],
            'metadata' => ['createdAt' => '2026-07-19T00:00:00.000Z', 'updatedAt' => '2026-07-19T00:00:00.000Z'],
        ];
    }

    private function aufbau(int $seed = 700): array
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        DB::table('new_leads')->insert(['id' => $customer, 'customer_type' => 'privat', 'name' => 'K', 'lastname' => 'T', 'email' => "s{$seed}@example.com", 'phone' => '0', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $alt, 'lead_id' => $customer, 'street' => 'Weg 1', 'postcode' => '12345', 'city' => 'S', 'created_at' => now(), 'updated_at' => now()]);

        // Das Dokument steht auf v3 — so, wie es nach einem heutigen Speichern aussieht.
        $dokId = DB::table('hausplaner_documents')->insertGetId([
            'alternative_id' => $alt, 'schema_version' => 3, 'revision' => 5,
            'scene_json' => json_encode($this->szene($alt, 3, 5)),
            'checksum' => 'aktuell', 'created_by' => null, 'updated_by' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Der Snapshot stammt aus der Zeit VOR der Anhebung: eine echte v2-Szene.
        $snapId = DB::table('hausplaner_snapshots')->insertGetId([
            'hausplaner_document_id' => $dokId, 'revision' => 2,
            'scene_json' => json_encode($this->szene($alt, 2, 2)),
            'label' => 'vor der Anhebung', 'reason' => 'manuell',
            'created_by' => null, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return [$alt, $snapId];
    }

    private function user(): User
    {
        return User::factory()->create(['password' => 'password', 'is_admin' => 1]);
    }

    public function test_rueckweg_fuehrt_die_schema_version_mit(): void
    {
        [$alt, $snapId] = $this->aufbau();

        $this->actingAs($this->user())
            ->postJson("/admin/hausplaner/objekt/{$alt}/snapshots/{$snapId}/wiederherstellen")
            ->assertOk();

        $doc = (array) DB::table('hausplaner_documents')->where('alternative_id', $alt)->first();
        $scene = json_decode($doc['scene_json'], true);

        // **Die tragende Zusage.** Ohne die Nachführung stünde hier 3 über einem v2-Inhalt.
        $this->assertSame(2, (int) $scene['schemaVersion'], 'die zurückgeholte Szene ist nicht die v2-Szene');
        $this->assertSame(2, (int) $doc['schema_version'], 'die Spalte folgt der Szene nicht — Anzeige und Inhalt lügen auseinander');
        $this->assertSame(
            (int) $scene['schemaVersion'],
            (int) $doc['schema_version'],
            'Spalte und Szene müssen nach dem Rückweg dieselbe Version tragen.',
        );
    }

    public function test_rueckweg_prueft_eine_AKTUELLE_szene_gegen_das_schema(): void
    {
        // K-N5, zweiter Teil: was die heutige Version traegt, wird geprueft — ein kaputter
        // v3-Snapshot darf nicht zurueckgeschrieben werden.
        [$alt, ] = $this->aufbau(740);
        $dokId = (int) DB::table('hausplaner_documents')->where('alternative_id', $alt)->value('id');
        $kaputt = $this->szene($alt, 3, 3);
        $kaputt['roofs'][0]['neigungGrad'] = 95;          // ausserhalb [0,89]
        $snapId = DB::table('hausplaner_snapshots')->insertGetId([
            'hausplaner_document_id' => $dokId, 'revision' => 3,
            'scene_json' => json_encode($kaputt), 'label' => 'kaputt', 'reason' => 'manuell',
            'created_by' => null, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $vorher = (array) DB::table('hausplaner_documents')->where('alternative_id', $alt)->first();

        $this->actingAs($this->user())
            ->postJson("/admin/hausplaner/objekt/{$alt}/snapshots/{$snapId}/wiederherstellen")
            ->assertStatus(500);

        $this->assertSame($vorher, (array) DB::table('hausplaner_documents')->where('alternative_id', $alt)->first(),
            'ein abgelehnter Rueckweg darf das Dokument nicht veraendert haben');
    }

    public function test_ein_ALTER_snapshot_bleibt_wiederherstellbar(): void
    {
        // **Die Gegenprobe zur Prueflogik, und der Grund fuer die Versions-Bedingung.**
        // Gemessen: ein echter v2-Snapshot erzeugt gegen das heutige Schema 2 Fehler. Wuerde der
        // Rueckweg unbedingt pruefen, waere jede Geschichte vor der Anhebung dauerhaft tot.
        [$alt, $snapId] = $this->aufbau(760);

        $this->actingAs($this->user())
            ->postJson("/admin/hausplaner/objekt/{$alt}/snapshots/{$snapId}/wiederherstellen")
            ->assertOk();

        $doc = (array) DB::table('hausplaner_documents')->where('alternative_id', $alt)->first();
        $this->assertSame(2, (int) $doc['schema_version']);
    }

    public function test_rueckweg_auf_gleiche_version_laesst_die_spalte_stehen(): void
    {
        // Die Umkehrung: ein v3-Snapshot in ein v3-Dokument darf nichts verstellen.
        [$alt, ] = $this->aufbau(720);
        $dokId = (int) DB::table('hausplaner_documents')->where('alternative_id', $alt)->value('id');
        $snapId = DB::table('hausplaner_snapshots')->insertGetId([
            'hausplaner_document_id' => $dokId, 'revision' => 3,
            'scene_json' => json_encode($this->szene($alt, 3, 3)),
            'label' => 'v3', 'reason' => 'manuell',
            'created_by' => null, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($this->user())
            ->postJson("/admin/hausplaner/objekt/{$alt}/snapshots/{$snapId}/wiederherstellen")
            ->assertOk();

        $doc = (array) DB::table('hausplaner_documents')->where('alternative_id', $alt)->first();
        $this->assertSame(3, (int) $doc['schema_version']);
    }
}
