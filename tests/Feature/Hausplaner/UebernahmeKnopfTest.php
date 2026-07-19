<?php

namespace Tests\Feature\Hausplaner;

use App\Models\LeadAlternativeAdd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * W-A — Übernehmen-Knopf (Szene → Auslegung): POST-Route + Rechte-Gate + Staleness-Anzeige.
 * Grundlage: PLANNER-SPEC Welle W-A (Abnahmekriterien §5 + Kantenliste §4).
 *
 * Deckt ab:
 *  - Rechte: ohne Hausplaner-update-Recht 403, KEINE Seiteneffekte.
 *  - Version additiv: Übernahme ⇒ genau EINE neue aktive Profil-Version; hausplaner_documents
 *    byte-unverändert (Übernahme liest die Szene nur); Alt-Versionen bleiben unverändert.
 *  - Doppel-Submit: idempotent per Bestand (gleicher Szenen-Hash ⇒ 'unveraendert', keine Doppel-Version).
 *  - Kanten: keine_szene ⇒ 422 + nichts geschrieben · kein_raum ⇒ Meldung, keine Version ·
 *    unbekanntes Objekt ⇒ 404.
 *  - Staleness kippt: nie → aktuell (nach Übernahme) → VERALTET (nach Szene-Änderung via Speichern-Route).
 *
 * Läuft gegen die Test-DB (RefreshDatabase, phpunit.xml erzwingt ticket_testing);
 * die Arbeits-/Dev-DB `ticket` wird NICHT geschrieben.
 * Muster: Objekt-/Szene-Fixtures aus UebernehmeSzeneInAuslegungTest, User/Grant aus ProductPermissionGateTest.
 */
class UebernahmeKnopfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);
    }

    private function objekt(int $seed = 700): int
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        DB::table('new_leads')->insert(['id' => $customer, 'customer_type' => 'privat', 'name' => 'K', 'lastname' => 'T', 'email' => 'k@example.com', 'phone' => '0', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $alt, 'lead_id' => $customer, 'street' => 'Weg 1', 'postcode' => '12345', 'city' => 'S', 'created_at' => now(), 'updated_at' => now()]);

        return $alt;
    }

    /** @param array<string,mixed> $scene */
    private function dokument(int $alt, array $scene): void
    {
        DB::table('hausplaner_documents')->insert([
            'alternative_id' => $alt, 'schema_version' => 1, 'revision' => (int) ($scene['revision'] ?? 1),
            'scene_json' => json_encode($scene), 'checksum' => 't',
            'created_by' => null, 'updated_by' => null, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** @return array<string,mixed> */
    private function wall(string $id, int $sx, int $sy, int $ex, int $ey): array
    {
        return ['id' => $id, 'type' => 'wall', 'levelId' => 'L', 'start' => ['x' => $sx, 'y' => $sy], 'end' => ['x' => $ex, 'y' => $ey], 'height' => 2500];
    }

    /** Zwei angrenzende 5×5-Räume (Muster UebernehmeSzeneInAuslegungTest). @return array<string,mixed> */
    private function zweiRaumSzene(): array
    {
        return [
            'schemaVersion' => 1,
            'units' => 'mm',
            'revision' => 1,
            'levels' => [['id' => 'L', 'sortOrder' => 0, 'defaultWallHeight' => 2500]],
            'nodes' => [
                $this->wall('a_u', 0, 0, 5000, 0), $this->wall('b_u', 5000, 0, 10000, 0),
                $this->wall('re', 10000, 0, 10000, 5000), $this->wall('b_o', 10000, 5000, 5000, 5000),
                $this->wall('a_o', 5000, 5000, 0, 5000), $this->wall('li', 0, 5000, 0, 0),
                $this->wall('mid', 5000, 0, 5000, 5000),
            ],
        ];
    }

    private function user(bool $admin = false): User
    {
        return User::factory()->create(['password' => 'password', 'name' => (string) random_int(1, 9999), 'is_admin' => $admin]);
    }

    /** @param array<string,int> $flags */
    private function grant(User $u, array $flags): void
    {
        DB::table('user_rolls')->insert(array_merge([
            'user_id' => $u->id, 'item_id' => 'Hausplaner', 'is_read' => 0, 'is_add' => 0, 'is_update' => 0, 'is_delete' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ], $flags));
    }

    /** @return array<string,mixed> */
    private function dokumentZeile(int $alt): array
    {
        return (array) DB::table('hausplaner_documents')->where('alternative_id', $alt)->first();
    }

    // ---- Rechte -------------------------------------------------------------------------------

    public function test_ohne_schreibrecht_403_und_keine_seiteneffekte(): void
    {
        $alt = $this->objekt(700);
        $this->dokument($alt, $this->zweiRaumSzene());
        $vorher = $this->dokumentZeile($alt);

        $u = $this->user(); // kein Grant

        $this->actingAs($u)->post("/admin/hausplaner/objekt/{$alt}/uebernehmen")->assertForbidden();

        $this->assertSame(0, DB::table('anforderungsprofile')->count());
        $this->assertSame($vorher, $this->dokumentZeile($alt), 'Dokument darf bei 403 nicht verändert werden');
    }

    public function test_unbekanntes_objekt_404(): void
    {
        $admin = $this->user(true);
        $this->actingAs($admin)->post('/admin/hausplaner/objekt/999999/uebernehmen')->assertNotFound();
        $this->assertSame(0, DB::table('anforderungsprofile')->count());
    }

    // ---- Übernahme (Erfolg) -------------------------------------------------------------------

    public function test_uebernahme_erzeugt_genau_eine_version_dokument_byte_unveraendert(): void
    {
        $alt = $this->objekt(710);
        $this->dokument($alt, $this->zweiRaumSzene());
        $vorher = $this->dokumentZeile($alt);

        $u = $this->user();
        $this->grant($u, ['is_update' => 1]);

        $res = $this->actingAs($u)->post("/admin/hausplaner/objekt/{$alt}/uebernehmen");

        $res->assertRedirect(route('hausplaner.objekt.seite', $alt));
        $res->assertSessionHas('hausplaner_uebernahme');
        $flash = session('hausplaner_uebernahme');
        $this->assertSame('erfolg', $flash['typ']);
        $this->assertStringContainsString('2 Räume übernommen, Version 1', $flash['text']);

        // Genau EINE neue, aktive Profil-Version.
        $this->assertSame(1, DB::table('anforderungsprofile')->where('verankerbar_id', $alt)->count());
        $profil = DB::table('anforderungsprofile')
            ->where('verankerbar_type', LeadAlternativeAdd::class)
            ->where('verankerbar_id', $alt)->where('status', 'aktiv')->first();
        $this->assertNotNull($profil);
        $this->assertSame(1, (int) $profil->version);

        // Übernahme liest die Szene NUR: hausplaner_documents byte-unverändert.
        $this->assertSame($vorher, $this->dokumentZeile($alt), 'Übernahme darf das Szenen-Dokument nicht verändern');
    }

    public function test_doppel_submit_idempotent_keine_doppel_version(): void
    {
        $alt = $this->objekt(720);
        $this->dokument($alt, $this->zweiRaumSzene());

        $u = $this->user();
        $this->grant($u, ['is_update' => 1]);

        $this->actingAs($u)->post("/admin/hausplaner/objekt/{$alt}/uebernehmen")->assertRedirect();
        $res2 = $this->actingAs($u)->post("/admin/hausplaner/objekt/{$alt}/uebernehmen");

        $res2->assertRedirect(route('hausplaner.objekt.seite', $alt));
        $flash = session('hausplaner_uebernahme');
        $this->assertSame('info', $flash['typ']);
        $this->assertStringContainsString('bereits übernommen', $flash['text']);

        // Idempotent bei gleicher Quell-Revision (Bestand: UebernehmeSzeneInAuslegung, source_hash-Vergleich).
        $this->assertSame(1, DB::table('anforderungsprofile')->where('verankerbar_id', $alt)->count());
    }

    // ---- Kanten -------------------------------------------------------------------------------

    public function test_keine_szene_422_und_nichts_geschrieben(): void
    {
        $alt = $this->objekt(730); // KEIN hausplaner_documents-Eintrag

        $u = $this->user();
        $this->grant($u, ['is_update' => 1]);

        $res = $this->actingAs($u)->post("/admin/hausplaner/objekt/{$alt}/uebernehmen");

        $res->assertStatus(422);
        $res->assertJson(['status' => 'keine_szene']);
        $this->assertSame(0, DB::table('anforderungsprofile')->count());
        $this->assertSame(0, DB::table('hausplaner_documents')->count(), 'uebernehmen darf KEIN Dokument anlegen');
    }

    public function test_kein_raum_meldung_und_keine_version(): void
    {
        $alt = $this->objekt(740);
        // Offener Wandzug → 0 geschlossene Räume (Muster UebernehmeSzeneInAuslegungTest).
        $this->dokument($alt, [
            'schemaVersion' => 1, 'units' => 'mm', 'revision' => 1,
            'levels' => [['id' => 'L', 'sortOrder' => 0, 'defaultWallHeight' => 2500]],
            'nodes' => [$this->wall('w1', 0, 0, 5000, 0), $this->wall('w2', 5000, 0, 5000, 5000), $this->wall('w3', 5000, 5000, 0, 5000)],
        ]);
        $vorher = $this->dokumentZeile($alt);

        $u = $this->user();
        $this->grant($u, ['is_update' => 1]);

        $res = $this->actingAs($u)->post("/admin/hausplaner/objekt/{$alt}/uebernehmen");

        $res->assertRedirect(route('hausplaner.objekt.seite', $alt));
        $flash = session('hausplaner_uebernahme');
        $this->assertSame('warnung', $flash['typ']);
        $this->assertStringContainsString('keine geschlossenen Räume', $flash['text']);

        $this->assertSame(0, DB::table('anforderungsprofile')->count());
        $this->assertSame($vorher, $this->dokumentZeile($alt));
    }

    // ---- Staleness-Anzeige --------------------------------------------------------------------

    public function test_staleness_kippt_nie_aktuell_veraltet(): void
    {
        $alt = $this->objekt(750);
        $this->dokument($alt, $this->zweiRaumSzene());

        $u = $this->user();
        $this->grant($u, ['is_read' => 1, 'is_update' => 1]);

        // 1) Noch nie übernommen.
        $this->actingAs($u)->get("/admin/hausplaner/objekt/{$alt}")
            ->assertOk()->assertSee('Noch nie übernommen');

        // 2) Übernehmen ⇒ aktuell (Szene Rev. 1).
        $this->actingAs($u)->post("/admin/hausplaner/objekt/{$alt}/uebernehmen")->assertRedirect();
        $this->actingAs($u)->get("/admin/hausplaner/objekt/{$alt}")
            ->assertOk()->assertSee('Übernommen — aktuell (Szene Rev. 1)');

        // 3) Szene über den ECHTEN Speicherpfad ändern (Wand verschoben) ⇒ VERALTET.
        $geaendert = $this->zweiRaumSzene();
        $geaendert['nodes'][6] = $this->wall('mid', 6000, 0, 6000, 5000);
        $this->actingAs($u)->putJson("/admin/hausplaner/objekt/{$alt}/dokument", [
            'base_revision' => 1, 'schema_version' => 1, 'scene' => $geaendert,
        ])->assertOk();

        $this->actingAs($u)->get("/admin/hausplaner/objekt/{$alt}")
            ->assertOk()->assertSee('Übernommen — VERALTET (Szene geändert seit Übernahme)');
    }

    public function test_zweite_uebernahme_nach_aenderung_version_additiv_altversion_unveraendert(): void
    {
        $alt = $this->objekt(760);
        $this->dokument($alt, $this->zweiRaumSzene());

        $u = $this->user();
        $this->grant($u, ['is_update' => 1]);

        // Version 1 übernehmen, Alt-Zustand festhalten.
        $this->actingAs($u)->post("/admin/hausplaner/objekt/{$alt}/uebernehmen")->assertRedirect();
        $v1Vorher = (array) DB::table('anforderungsprofile')->where('verankerbar_id', $alt)->where('version', 1)->first();

        // Szene ändern (echter Speicherpfad), dann erneut übernehmen.
        $geaendert = $this->zweiRaumSzene();
        $geaendert['nodes'][6] = $this->wall('mid', 6000, 0, 6000, 5000);
        $this->actingAs($u)->putJson("/admin/hausplaner/objekt/{$alt}/dokument", [
            'base_revision' => 1, 'schema_version' => 1, 'scene' => $geaendert,
        ])->assertOk();

        $res = $this->actingAs($u)->post("/admin/hausplaner/objekt/{$alt}/uebernehmen");
        $res->assertRedirect(route('hausplaner.objekt.seite', $alt));
        $this->assertStringContainsString('Version 2', session('hausplaner_uebernahme')['text']);

        // Additiv: v2 aktiv, v1 abgelöst und byte-unverändert (bis auf den Status/updated_at).
        $this->assertSame(2, DB::table('anforderungsprofile')->where('verankerbar_id', $alt)->count());
        $v2 = DB::table('anforderungsprofile')->where('verankerbar_id', $alt)->where('status', 'aktiv')->first();
        $this->assertSame(2, (int) $v2->version);

        $v1Nachher = (array) DB::table('anforderungsprofile')->where('verankerbar_id', $alt)->where('version', 1)->first();
        $this->assertSame('abgeloest', $v1Nachher['status']);
        foreach (['gebaeude_geometrie', 'verankerbar_type', 'verankerbar_id', 'bezeichnung', 'version', 'created_at'] as $spalte) {
            $this->assertEquals($v1Vorher[$spalte], $v1Nachher[$spalte], "Alt-Version 1: Spalte {$spalte} darf sich nicht ändern");
        }
    }
}
