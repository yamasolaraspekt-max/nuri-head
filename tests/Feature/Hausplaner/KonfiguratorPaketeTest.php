<?php

namespace Tests\Feature\Hausplaner;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * AUF-81 — Konfigurator-Pakete serverseitig.
 *
 * **Das wichtigste Kriterium dieses Postens ist das Eigentumsgatter (K5):** ein Nutzer sieht und
 * öffnet ausschließlich seine eigenen Pakete. Die Kennung aus der Anfrage wird **nie** ohne
 * Eigentumsprüfung verwendet — Bauordnung `ticket`.
 *
 * **Und die Liste filtert am Server, nicht in der Anzeige.** Eine Liste, die alles lädt und die
 * Hälfte ausblendet, ist bereits geleakt — deshalb prüft K7 die abgesetzte Abfrage, nicht das
 * Ergebnis.
 *
 * Läuft gegen die Test-DB (RefreshDatabase); die Arbeits-DB `ticket` wird NICHT geschrieben.
 */
class KonfiguratorPaketeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);
    }

    private function admin(): User
    {
        return User::factory()->create(['password' => 'password', 'is_admin' => 1]);
    }

    /** @return array<string,mixed> */
    private function paketDaten(string $titel = 'Festverglasung'): array
    {
        return [
            'art' => 'fenster',
            'titel' => $titel,
            'schema_version' => 1,
            'paket' => ['id' => 'x1', 'type' => 'window', 'parameters' => ['bauart' => '01_festverglasung']],
        ];
    }

    // --- K5: das Eigentumsgatter ------------------------------------------------------------------
    public function test_k5_fremde_pakete_erscheinen_nicht_in_der_liste(): void
    {
        $a = $this->admin();
        $b = $this->admin();

        $this->actingAs($a)->postJson(route('hausplaner.objekt.pakete.speichern'), $this->paketDaten('GEHEIM-A'))
            ->assertCreated();

        $antwort = $this->actingAs($b)->getJson(route('hausplaner.objekt.pakete.liste'));

        $antwort->assertOk();
        $antwort->assertDontSee('GEHEIM-A', false);
        $this->assertSame(0, $antwort->json('total'), 'B sieht kein einziges Paket von A');
    }

    public function test_k5_ein_fremdes_paket_ist_nicht_abrufbar(): void
    {
        $a = $this->admin();
        $b = $this->admin();

        $id = $this->actingAs($a)
            ->postJson(route('hausplaner.objekt.pakete.speichern'), $this->paketDaten('GEHEIM-A'))
            ->json('id');

        $antwort = $this->actingAs($b)->getJson(route('hausplaner.objekt.pakete.zeigen', $id));

        $antwort->assertNotFound();
        $antwort->assertDontSee('GEHEIM-A', false);
        // 404 und nicht 403: der Aufrufer erfaehrt nicht einmal, dass es existiert.
    }

    public function test_das_eigene_paket_ist_abrufbar(): void
    {
        $a = $this->admin();
        $id = $this->actingAs($a)
            ->postJson(route('hausplaner.objekt.pakete.speichern'), $this->paketDaten('Meins'))
            ->json('id');

        $this->actingAs($a)->getJson(route('hausplaner.objekt.pakete.zeigen', $id))
            ->assertOk()
            ->assertJsonPath('titel', 'Meins');
    }

    // --- K7: serverseitig gefiltert ---------------------------------------------------------------
    public function test_k7_die_abfrage_ist_bereits_auf_den_besitzer_eingeschraenkt(): void
    {
        $a = $this->admin();

        $abfragen = [];
        DB::listen(function ($e) use (&$abfragen) {
            if (str_contains($e->sql, 'hausplaner_configurator_packages') && str_starts_with($e->sql, 'select')) {
                $abfragen[] = $e->sql;
            }
        });

        $this->actingAs($a)->getJson(route('hausplaner.objekt.pakete.liste'))->assertOk();

        $this->assertNotEmpty($abfragen);
        foreach ($abfragen as $sql) {
            $this->assertStringContainsString('user_id', $sql,
                'die Einschraenkung steht in der Abfrage, nicht in der Anzeige');
        }
    }

    // --- K6: ohne Recht kein Zugriff --------------------------------------------------------------
    public function test_k6_ohne_read_recht_keine_liste(): void
    {
        $ohne = User::factory()->create(['password' => 'password', 'is_admin' => 0]);
        $antwort = $this->actingAs($ohne)->getJson(route('hausplaner.objekt.pakete.liste'));
        $this->assertNotSame(200, $antwort->getStatusCode());
    }

    public function test_k6_ohne_add_recht_kein_speichern(): void
    {
        $ohne = User::factory()->create(['password' => 'password', 'is_admin' => 0]);
        $antwort = $this->actingAs($ohne)
            ->postJson(route('hausplaner.objekt.pakete.speichern'), $this->paketDaten());
        $this->assertNotSame(201, $antwort->getStatusCode());
        $this->assertSame(0, DB::table('hausplaner_configurator_packages')->count(), 'nichts geschrieben');
    }

    // --- K8: Paginierung ---------------------------------------------------------------------------
    public function test_k8_dreissig_pakete_ergeben_25_und_5(): void
    {
        $a = $this->admin();
        $zeilen = [];
        for ($i = 0; $i < 30; $i++) {
            $zeilen[] = [
                'user_id' => $a->id, 'alternative_id' => null, 'art' => 'fenster',
                'titel' => 'P'.$i, 'status' => 'entwurf', 'schema_version' => 1,
                'paket' => json_encode(['i' => $i]),
                'created_at' => now()->subMinutes($i), 'updated_at' => now()->subMinutes($i),
            ];
        }
        DB::table('hausplaner_configurator_packages')->insert($zeilen);

        $abfragen = 0;
        DB::listen(function ($e) use (&$abfragen) {
            if (str_contains($e->sql, 'hausplaner_configurator_packages') && str_starts_with($e->sql, 'select')
                && ! str_contains($e->sql, 'count(')) {
                $abfragen++;
            }
        });

        $s1 = $this->actingAs($a)->getJson(route('hausplaner.objekt.pakete.liste'));
        $s1->assertOk();
        $this->assertCount(25, $s1->json('data'));
        $this->assertSame(30, $s1->json('total'));

        $s2 = $this->actingAs($a)->getJson(route('hausplaner.objekt.pakete.liste', ['page' => 2]));
        $this->assertCount(5, $s2->json('data'));

        $this->assertSame(2, $abfragen, 'eine Abfrage je Seite — kein N+1');
    }

    // --- K9: autark bleibt autark -----------------------------------------------------------------
    public function test_k9_ein_paket_ohne_gebaeude_laesst_sich_speichern_und_abrufen(): void
    {
        // Der Konfigurator laeuft autark. Ein Pflichtfeld haette genau diesen Fall verboten.
        $a = $this->admin();
        $daten = $this->paketDaten('Autark');
        $this->assertArrayNotHasKey('alternative_id', $daten);

        $id = $this->actingAs($a)->postJson(route('hausplaner.objekt.pakete.speichern'), $daten)
            ->assertCreated()->json('id');

        $this->actingAs($a)->getJson(route('hausplaner.objekt.pakete.zeigen', $id))
            ->assertOk()
            ->assertJsonPath('alternative_id', null);
    }

    // --- K3: idempotent ----------------------------------------------------------------------------
    public function test_k3_die_migration_ist_idempotent(): void
    {
        // Zweimal migrieren darf nicht fehlschlagen — die Vorlage macht es genauso.
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('hausplaner_configurator_packages'));
        $this->artisan('migrate', ['--path' => 'database/migrations/2026_07_26_180000_create_hausplaner_configurator_packages_table.php'])
            ->assertExitCode(0);
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('hausplaner_configurator_packages'));
    }

    // --- K2: keine Bestandstabelle beruehrt --------------------------------------------------------
    public function test_k2_die_migration_faesst_keine_bestandstabelle_an(): void
    {
        $roh = file_get_contents(base_path('database/migrations/2026_07_26_180000_create_hausplaner_configurator_packages_table.php'));
        $this->assertSame(0, substr_count($roh, 'Schema::'.'table('), 'kein nachtraeglicher Aenderungs-Aufruf');
        $this->assertSame(0, substr_count($roh, 'dropColumn'));
        $this->assertSame(0, substr_count($roh, 'renameColumn'));
        $this->assertStringContainsString('dropIfExists', $roh, 'der Rueckweg verwirft nur die neue Tabelle');
    }
}
