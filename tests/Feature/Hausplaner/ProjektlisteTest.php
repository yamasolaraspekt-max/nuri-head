<?php

namespace Tests\Feature\Hausplaner;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * AUF-78 — die Projektliste erreicht den Startbildschirm.
 *
 * **Die Sicherheitsfrage dieses Postens steht in einer einzigen Zeile:** die Studio-Route trägt
 * **nur `auth`**, kein `permission:Hausplaner,read`. Wer die Objektliste dorthin durchreicht,
 * zeigt sie **jedem angemeldeten Nutzer** — auch dem, der den Hausplaner nicht sehen darf.
 *
 * Deshalb prüft dieser Test zuerst, dass die Liste die Studio-Fläche **nicht** erreicht, und erst
 * danach, dass sie auf der Objekt-Seite ankommt. Der Mutations-Gegenbeweis hängt sie versuchsweise
 * doch an die Studio-Fläche — dann muss der erste Test rot werden.
 *
 * Läuft gegen die Test-DB (RefreshDatabase); die Arbeits-DB `ticket` wird NICHT geschrieben.
 */
class ProjektlisteTest extends TestCase
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

    /** Legt `anzahl` Objekte an und gibt die ID des ersten zurück. */
    private function objekte(int $anzahl, string $praefix = 'Haus'): int
    {
        DB::table('new_leads')->insert([
            'id' => 7100, 'customer_type' => 'privat', 'name' => 'Vorname', 'lastname' => 'GEHEIM',
            'email' => 'geheim@example.com', 'phone' => '0', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $zeilen = [];
        for ($i = 0; $i < $anzahl; $i++) {
            $zeilen[] = [
                'id' => 7200 + $i, 'lead_id' => 7100, 'object_name' => $praefix.' '.$i,
                'street' => 'Weg '.$i, 'postcode' => '12345', 'city' => 'Musterstadt',
                'created_at' => now(), 'updated_at' => now()->subMinutes($i),
            ];
        }
        foreach (array_chunk($zeilen, 500) as $teil) {
            DB::table('lead_alternative_adds')->insert($teil);
        }

        return 7200;
    }

    // --- K1: die Liste erreicht die Studio-Fläche NICHT ------------------------------------------
    public function test_k1_die_studio_flaeche_bekommt_die_liste_nicht(): void
    {
        $this->objekte(3);
        $antwort = $this->actingAs($this->admin())->get(route('hausplaner.studio'));

        $antwort->assertOk();
        $antwort->assertDontSee('data-projekte', false);
        $antwort->assertDontSee('Haus 0', false);
    }

    public function test_k1_die_studio_vorlage_kennt_den_feldnamen_nicht(): void
    {
        $vorlage = file_get_contents(resource_path('views/admin/hausplaner/studio.blade.php'));
        $this->assertStringNotContainsString('hpProjekte', $vorlage);
        $this->assertStringNotContainsString('data-projekte', $vorlage);
    }

    public function test_k1_die_studio_route_traegt_weiterhin_nur_auth(): void
    {
        // Nicht „sie ist unverändert" behaupten, sondern die Middleware messen: genau das ist der
        // Grund, warum die Liste dort nicht hindarf.
        $route = collect(app('router')->getRoutes())->first(fn ($r) => $r->getName() === 'hausplaner.studio');
        $this->assertNotNull($route);
        $this->assertContains('auth', $route->gatherMiddleware());
        $this->assertNotContains('permission:Hausplaner,read', $route->gatherMiddleware());
    }

    // --- K2: ohne Recht kein Zugriff --------------------------------------------------------------
    public function test_k2_ohne_hausplaner_recht_kein_zugriff_auf_die_objekt_seite(): void
    {
        $id = $this->objekte(2);
        $ohneRecht = User::factory()->create(['password' => 'password', 'is_admin' => 0]);

        $antwort = $this->actingAs($ohneRecht)->get(route('hausplaner.objekt.seite', $id));

        $this->assertNotSame(200, $antwort->getStatusCode(), 'die Seite darf ohne Recht nicht laden');
        $antwort->assertDontSee('Haus 0', false);
    }

    // --- K3: nur die nötigen Felder ---------------------------------------------------------------
    public function test_k3_das_buendel_traegt_genau_vier_felder_und_keine_kundendaten(): void
    {
        $id = $this->objekte(2);
        $antwort = $this->actingAs($this->admin())->get(route('hausplaner.objekt.seite', $id));
        $antwort->assertOk();

        preg_match('/data-projekte="([^"]*)"/', $antwort->getContent(), $treffer);
        $this->assertNotEmpty($treffer, 'data-projekte fehlt im Markup');
        $liste = json_decode(html_entity_decode($treffer[1], ENT_QUOTES), true);
        $this->assertIsArray($liste);
        $this->assertNotEmpty($liste);

        foreach ($liste as $eintrag) {
            $this->assertSame(['id', 'name', 'ort', 'datum'], array_keys($eintrag),
                'mehr Felder als die Flaeche anzeigt — jedes zusaetzliche ist eine moegliche Leckage');
        }
        // Der Kundenname steht in der Datenbank und darf die Seite nicht erreichen.
        $antwort->assertDontSee('GEHEIM', false);
    }

    // --- K4: genau eine Abfrage, hart begrenzt ----------------------------------------------------
    public function test_k4_eine_abfrage_und_hart_begrenzt_auch_bei_dreitausend_objekten(): void
    {
        $id = $this->objekte(3000);

        $abfragen = [];
        DB::listen(function ($a) use (&$abfragen) {
            if (str_contains($a->sql, 'lead_alternative_adds')) {
                $abfragen[] = $a->sql;
            }
        });

        $antwort = $this->actingAs($this->admin())->get(route('hausplaner.objekt.seite', $id));
        $antwort->assertOk();

        preg_match('/data-projekte="([^"]*)"/', $antwort->getContent(), $treffer);
        $liste = json_decode(html_entity_decode($treffer[1], ENT_QUOTES), true);

        $this->assertLessThanOrEqual(6, count($liste), 'die Obergrenze haelt auch bei 3000 Objekten');
        // Kein N+1: die Beziehung `lead` wird gar nicht erst geladen.
        // Zwei Abfragen treffen die Tabelle: die Routen-Bindung fuer {objekt} (die es ohnehin gab)
        // und meine Liste. Gezaehlt wird MEINE — die mit Sortierung und Grenze.
        $meine = array_values(array_filter($abfragen, fn ($sql) => str_contains($sql, 'order by')));
        $this->assertCount(1, $meine, "genau eine sortierte Abfrage; gesehen:\n".implode("\n", $abfragen));
        $this->assertStringContainsString('limit 6', $meine[0], 'die Grenze steht in der Abfrage, nicht erst danach');
        $this->assertStringNotContainsString('new_leads', implode(' ', $abfragen), 'kein Nachladen der Kundendaten');
    }

    // --- Der Weg bis ins Markup -------------------------------------------------------------------
    public function test_die_objekt_seite_traegt_die_liste(): void
    {
        $id = $this->objekte(3);
        $antwort = $this->actingAs($this->admin())->get(route('hausplaner.objekt.seite', $id));

        $antwort->assertOk();
        $antwort->assertSee('data-projekte=', false);
        $antwort->assertSee('Haus 0', false);
    }

    public function test_ohne_objekte_ist_die_liste_leer_nicht_erfunden(): void
    {
        // Der ehrliche Leerzustand aus AUF-40 Teil A haengt daran, dass hier wirklich nichts kommt.
        DB::table('new_leads')->insert([
            'id' => 7300, 'customer_type' => 'privat', 'name' => 'V', 'lastname' => 'L',
            'email' => 'l@example.com', 'phone' => '0', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('lead_alternative_adds')->insert([
            'id' => 7301, 'lead_id' => 7300, 'object_name' => 'Einziges', 'street' => 'W', 'postcode' => '1',
            'city' => 'S', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $antwort = $this->actingAs($this->admin())->get(route('hausplaner.objekt.seite', 7301));
        $antwort->assertOk();
        preg_match('/data-projekte="([^"]*)"/', $antwort->getContent(), $treffer);
        $liste = json_decode(html_entity_decode($treffer[1], ENT_QUOTES), true);
        $this->assertCount(1, $liste, 'ein Objekt in der Datenbank, ein Eintrag — nichts erfunden');
    }

    // --- K5/K6: der Umfang -------------------------------------------------------------------------
    public function test_k5_kein_php_block_im_blade(): void
    {
        $roh = file_get_contents(resource_path('views/admin/hausplaner/objekt.blade.php'));
        $this->assertSame(0, substr_count($roh, '@'.'endphp'),
            'ein solcher Block hat in AUF-64 genau diese Route zerbrochen');
    }
}
