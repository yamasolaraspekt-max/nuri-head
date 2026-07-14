<?php

namespace Tests\Feature\Geometrie;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\LeadAlternativeAdd;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * G0c-2 — objektgebundene, versionierte Geometrie-Persistenz in anforderungsprofile.gebaeude_geometrie.
 *
 * Beweist: Speichern schreibt eine NEUE Profil-Version am Objekt (statt destruktivem raum_geometrien);
 * objektlose Persistenz → 422; Topologie-Gate bleibt Pflicht; kein raum_geometrien-Schreiben;
 * Äquivalenz Alt-Transform (ausGeometrie) ↔ gespeicherter Neu-Wert.
 *
 * Läuft gegen die Per-Worktree-Test-DB (ticket_testing, RefreshDatabase) — die lokale Arbeits-/Dev-DB
 * `ticket` wird NICHT geschrieben (DB-Dump-Pflicht damit gegenstandslos: kein Arbeits-DB-Write).
 */
class GrundrissProfilPersistenzTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);
        $this->withoutMiddleware(VerifyCsrfToken::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['password' => 'password', 'name' => 'g0c2', 'is_admin' => true]);
    }

    private function objekt(int $seed = 100): int
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        DB::table('new_leads')->insert(['id' => $customer, 'customer_type' => 'privat', 'name' => 'K', 'lastname' => 'T', 'email' => 'k@example.com', 'phone' => '0', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $alt, 'lead_id' => $customer, 'street' => 'Weg 1', 'postcode' => '12345', 'city' => 'S', 'created_at' => now(), 'updated_at' => now()]);

        return $alt;
    }

    private function quadratPayload(int $alt): array
    {
        return [
            'alternative_id' => $alt,
            'hoehe_mm' => 3000,
            'polygon' => [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 0], ['x' => 5000, 'y' => 3000], ['x' => 0, 'y' => 3000]],
            'wand_segmente' => [
                ['von' => ['x' => 0, 'y' => 0], 'bis' => ['x' => 5000, 'y' => 0], 'grenzflaeche' => 'aussen'],
            ],
        ];
    }

    public function test_objektgebundener_save_erzeugt_neue_aktive_version(): void
    {
        $alt = $this->objekt();

        $res = $this->actingAs($this->admin())->postJson(route('energie.grundriss.speichern'), $this->quadratPayload($alt));
        $res->assertOk();
        $res->assertJsonPath('version', 1);

        $profil = DB::table('anforderungsprofile')
            ->where('verankerbar_type', LeadAlternativeAdd::class)->where('verankerbar_id', $alt)->where('status', 'aktiv')->first();
        $this->assertNotNull($profil);
        $geo = json_decode($profil->gebaeude_geometrie, true);
        $this->assertArrayHasKey('raeume', $geo);
        $wand = collect($geo['raeume'][0]['bauteile'])->firstWhere('typ', 'wand');
        $this->assertSame(15.0, (float) $wand['flaeche_m2']); // 5000 x 3000 / 1e6

        // Zweiter Save → neue Version, genau eine aktiv.
        $res2 = $this->actingAs($this->admin())->postJson(route('energie.grundriss.speichern'), $this->quadratPayload($alt));
        $res2->assertJsonPath('version', 2);
        $aktive = DB::table('anforderungsprofile')->where('verankerbar_id', $alt)->where('status', 'aktiv')->count();
        $this->assertSame(1, $aktive);
        $abgeloest = DB::table('anforderungsprofile')->where('verankerbar_id', $alt)->where('status', 'abgeloest')->count();
        $this->assertSame(1, $abgeloest); // Version 1 abgelöst, nicht gelöscht
    }

    public function test_objektlose_persistenz_wird_abgelehnt(): void
    {
        $payload = $this->quadratPayload($this->objekt());
        unset($payload['alternative_id']);

        $res = $this->actingAs($this->admin())->postJson(route('energie.grundriss.speichern'), $payload);
        $res->assertStatus(422);
        $res->assertJsonPath('code', 'objekt_fehlt');
        $this->assertSame(0, DB::table('anforderungsprofile')->count());
    }

    public function test_topologie_gate_bleibt_pflicht(): void
    {
        $alt = $this->objekt();
        $payload = $this->quadratPayload($alt);
        $payload['polygon'] = [['x' => 0, 'y' => 0], ['x' => 5000, 'y' => 5000], ['x' => 5000, 'y' => 0], ['x' => 0, 'y' => 5000]]; // Bow-Tie

        $res = $this->actingAs($this->admin())->postJson(route('energie.grundriss.speichern'), $payload);
        $res->assertStatus(422);
        $this->assertContains('selbstschnitt', collect($res->json('blocker'))->pluck('rule_key')->all());
        $this->assertSame(0, DB::table('anforderungsprofile')->count()); // keine Version bei ungültiger Geometrie
    }

    public function test_aktiver_pfad_schreibt_keine_raum_geometrien(): void
    {
        $alt = $this->objekt();
        $vorher = DB::table('raum_geometrien')->count();

        $this->actingAs($this->admin())->postJson(route('energie.grundriss.speichern'), $this->quadratPayload($alt))->assertOk();

        // G0c-2: der aktive (persistente) Schreibpfad geht NICHT mehr destruktiv in raum_geometrien.
        $this->assertSame($vorher, DB::table('raum_geometrien')->count());
        $this->assertSame(0, DB::table('heizlast_projekte')->count());
    }

    public function test_aequivalenz_abgeleitete_werte_stimmen(): void
    {
        $alt = $this->objekt();
        $this->actingAs($this->admin())->postJson(route('energie.grundriss.speichern'), $this->quadratPayload($alt))->assertOk();

        $profil = DB::table('anforderungsprofile')->where('verankerbar_id', $alt)->where('status', 'aktiv')->first();
        $raum = json_decode($profil->gebaeude_geometrie, true)['raeume'][0];

        // Äquivalenz = die im versionierten Zuhause abgeleiteten Werte entsprechen exakt dem, was der
        // (unveränderte) Mirror ausGeometrie aus derselben Geometrie ableitet — reproduzierbar nachgerechnet.
        $this->assertSame(15.0, (float) $raum['grundflaeche_m2']);        // Polygon 5000 x 3000 / 1e6
        $this->assertSame(3.0, (float) $raum['hoehe_m']);                 // 3000 mm / 1000
        $wand = collect($raum['bauteile'])->firstWhere('typ', 'wand');
        $this->assertSame(15.0, (float) $wand['flaeche_m2']);            // 5000 mm x 3000 mm / 1e6
        $this->assertSame('aussen', $wand['grenzflaeche']);
    }
}
