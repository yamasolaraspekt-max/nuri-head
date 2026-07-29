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
    /**
     * **Umbenannt in AUF-66 (vorher „genau vier Felder").** Die Absicht ist unverändert: *nur das,
     * was die Fläche braucht — jedes zusätzliche Feld ist eine mögliche Leckage.* Nur ist es jetzt
     * ein Feld mehr, weil die Fläche eine Sache mehr tut: sie führt zum Projekt. **Die Zahl war nie
     * das Kriterium, die Notwendigkeit war es.**
     */
    public function test_k3_das_buendel_traegt_genau_die_fuenf_noetigen_felder_und_keine_kundendaten(): void
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
            $this->assertSame(['id', 'name', 'ort', 'datum', 'adresse'], array_keys($eintrag),
                'mehr Felder als die Flaeche anzeigt — jedes zusaetzliche ist eine moegliche Leckage');
        }
        // **Der Kundenname darf die INSEL nicht erreichen.**
        //
        // **Präzisiert in AUF-83-T1b, nicht abgeschwächt.** Vorher stand hier
        // `$antwort->assertDontSee(...)` über die **ganze Seite**. Das war deckungsgleich mit „die
        // Insel", **solange die Insel ein eigenes HTML-Dokument war** — die Seite *war* die Insel.
        // Seit T1b erbt sie die Ticket-Shell, und die trägt ein eigenes Kundenauswahlfeld
        // (`activity.blade.php`, auf **jeder** Admin-Seite; eigener Posten AUF-84).
        //
        // **Die Absicht dieser Zusage steht in ihrem eigenen Docblock:** *„jedes zusätzliche Feld
        // ist eine mögliche Leckage … die Zahl war nie das Kriterium, die Notwendigkeit war es."*
        // **Sie schützt das Bündel, nicht das Dokument.** Geprüft wird deshalb der Teilbaum
        // `#hausplaner-root` samt seiner `data-*`-Attribute — genau das, was die Insel bekommt.
        $this->assertStringNotContainsString('GEHEIM', $this->inselTeilbaum($antwort->getContent()),
            'ein Kundenname ist in das Buendel der Insel geraten');
    }

    /**
     * Der Teilbaum, den die Insel wirklich bekommt: `#hausplaner-root` mit allen `data-*`-Attributen
     * **und** die eingebettete Szene daneben.
     *
     * **Warum DOM und nicht Regex:** verschachtelte `<div>` lassen sich mit einem Ausdruck nicht
     * verlässlich abgrenzen, und eine Zusage, die am falschen Ende abschneidet, prüft weniger als
     * sie behauptet — dieselbe Klasse wie die Klammerzählung in `statische-inline-stile.mjs`.
     */
    private function inselTeilbaum(string $html): string
    {
        $doc = new \DOMDocument();
        $vorher = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($vorher);

        $wurzel = $doc->getElementById('hausplaner-root');
        $this->assertNotNull($wurzel, '#hausplaner-root nicht gefunden — dann prueft diese Zusage nichts');
        $teilbaum = (string) $doc->saveHTML($wurzel);

        // Die Szene liegt als Geschwister-Element daneben und gehoert zum Buendel.
        $szene = $doc->getElementById('hausplaner-scene');
        if ($szene !== null) {
            $teilbaum .= (string) $doc->saveHTML($szene);
        }

        return $teilbaum;
    }

    // --- AUF-66 K2: jeder Eintrag traegt die Adresse SEINES Objekts -------------------------------
    /**
     * **Der haeufigste Fehler solcher Listen ist die geteilte Adresse:** alle Eintraege zeigen auf
     * dasselbe Ziel, meistens auf das gerade geoeffnete Objekt. Das faellt niemandem auf, solange
     * nur ein Projekt existiert — und ist von da an falsch.
     *
     * **Und die Adresse entsteht auf dem Server**, nicht in der Insel: `route()` kennt das Praefix.
     */
    public function test_auf66_jeder_eintrag_zeigt_auf_sein_eigenes_objekt(): void
    {
        $id = $this->objekte(3);   // 7200, 7201, 7202 — geoeffnet wird 7200
        $antwort = $this->actingAs($this->admin())->get(route('hausplaner.objekt.seite', $id));
        $antwort->assertOk();

        preg_match('/data-projekte="([^"]*)"/', $antwort->getContent(), $treffer);
        $liste = json_decode(html_entity_decode($treffer[1], ENT_QUOTES), true);
        $this->assertGreaterThan(1, count($liste), 'fuer diesen Test braucht es mehr als einen Eintrag');

        $ziele = [];
        foreach ($liste as $eintrag) {
            $this->assertSame(route('hausplaner.objekt.seite', $eintrag['id']), $eintrag['adresse'],
                'die Adresse gehoert zu genau diesem Eintrag');
            $this->assertStringContainsString((string) $eintrag['id'], $eintrag['adresse']);
            $ziele[] = $eintrag['adresse'];
        }
        $this->assertCount(count($ziele), array_unique($ziele), 'keine zwei Eintraege teilen sich ein Ziel');
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
