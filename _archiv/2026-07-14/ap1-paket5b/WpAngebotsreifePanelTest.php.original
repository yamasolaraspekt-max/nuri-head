<?php

namespace Tests\Feature\Offer;

use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductChecklistValue;
use App\Models\ProductFormula;
use App\Models\User;
use Database\Seeders\WpProduktFormularSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Paket 1 — Panel/Controller/Route: die read-only Angebotsreife-Anzeige rendert korrekt aus dem DTO.
 */
class WpAngebotsreifePanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        config(['broadcasting.default' => 'null']);
        $this->relax(function () {
            DB::table('article_groups')->insert(['id' => 2, 'article_group' => 'Wärmepumpe', 'initial' => 'WP', 'created_at' => now(), 'updated_at' => now()]);
            DB::table('master_sets')->insert(['id' => 1, 'article_group_id' => 2, 'name' => 'WP-Set', 'created_at' => now(), 'updated_at' => now()]);
        });
        $this->seed(WpProduktFormularSeeder::class);
    }

    private function relax(callable $fn): void
    {
        DB::statement("SET SESSION sql_mode=''");
        $fn();
        DB::statement("SET SESSION sql_mode='STRICT_TRANS_TABLES'");
    }

    private function admin(): User
    {
        return User::factory()->create(['password' => 'password', 'name' => 'reifetest', 'is_admin' => true]);
    }

    private function wpLead(int $id, bool $full): void
    {
        $cid = $id + 1;
        $aid = $id + 2;
        $this->relax(function () use ($id, $cid, $aid, $full) {
            DB::table('new_leads')->insert(['id' => $cid, 'customer_type' => 'privat', 'name' => $full ? 'Max' : null, 'lastname' => $full ? 'Muster' : null, 'email' => $full ? 'max@muster.de' : null, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('lead_alternative_adds')->insert(['id' => $aid, 'street' => $full ? 'Weg 1' : null, 'postcode' => $full ? '10115' : null, 'city' => $full ? 'Berlin' : null, 'objective' => $full ? 'WP' : null, 'ready_for_offer' => 0, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('lead_product_lists')->insert(['id' => $id, 'customer_id' => $cid, 'alternative_id' => $aid, 'product_id' => 2, 'service_id' => $full ? 1 : null, 'department_id' => $full ? 1 : null, 'employee_id' => $full ? 1 : null, 'status' => 'lead', 'created_at' => now(), 'updated_at' => now()]);
        });

        if ($full) {
            $formula = ProductFormula::where('product_id', 2)->firstOrFail();
            $required = collect($formula->fields)->filter(fn ($f) => ! empty($f['required']))->map(fn ($f) => $f['key']);
            LeadProductChecklistValue::create([
                'lead_product_list_id' => $id, 'product_formula_id' => $formula->id, 'customer_id' => $cid, 'alternative_id' => $aid, 'product_id' => 2, 'section_name' => $formula->section_name,
                'filled_values' => $required->mapWithKeys(fn ($k) => [$k => 'x'])->all(), 'formula_snapshot' => $formula->fields, 'formula_version' => $formula->version,
            ]);
            $this->relax(function () use ($aid) {
                DB::table('anforderungsprofile')->insert(['id' => 1, 'verankerbar_type' => LeadAlternativeAdd::class, 'verankerbar_id' => $aid, 'version' => 1, 'status' => 'aktiv', 'created_at' => now(), 'updated_at' => now()]);
                DB::table('anforderungsprofil_werte')->insert(['id' => 1, 'anforderungsprofil_id' => 1, 'schluessel' => 'phi_hl_kw', 'wert_num' => 8.5, 'created_at' => now(), 'updated_at' => now()]);
            });
        }
    }

    public function test_panel_rendert_fuer_blockierten_vorgang(): void
    {
        $this->wpLead(300, false);

        $res = $this->actingAs($this->admin())->get(route('offers.angebotsreife', 300));

        $res->assertOk();
        $res->assertSee('Angebotsreife');
        $res->assertSee('blockiert');
        $res->assertSee('Kunde vorhanden (Name + Kontakt)');
        $res->assertSee('disabled', false); // Button-Gate: nicht angebotsfähig → disabled
    }

    public function test_panel_zeigt_angebotsfaehig_wenn_alle_erfuellt(): void
    {
        $this->wpLead(400, true);

        $res = $this->actingAs($this->admin())->get(route('offers.angebotsreife', 400));

        $res->assertOk();
        $res->assertSee('angebotsfähig');
        $res->assertSee('Angebot kann erstellt werden.');
        $res->assertDontSee('cursor:not-allowed'); // Button aktiv (kein disabled-Style)
    }

    public function test_json_endpoint_liefert_dto(): void
    {
        $this->wpLead(500, false);

        $res = $this->actingAs($this->admin())->getJson(route('offers.angebotsreife.json', 500));

        $res->assertOk();
        $res->assertJsonStructure(['lead_product_list_id', 'percent', 'status', 'angebotsfaehig', 'aufgaben' => ['pflicht_gesamt', 'erledigt', 'offen', 'blockierend', 'optional'], 'kriterien', 'blocker_offen', 'naechster_schritt', 'hinweis']);
        $res->assertJsonPath('status', 'blockiert');
        $res->assertJsonPath('angebotsfaehig', false);
    }

    /** Auflage 2 — Auth-Schutz: Gast darf weder Panel noch JSON frei abrufen. */
    public function test_gast_ohne_auth_wird_abgewiesen(): void
    {
        // Panel (web): unauthentifiziert → Redirect (kein 200).
        $panel = $this->get(route('offers.angebotsreife', 300));
        $this->assertSame(302, $panel->getStatusCode(), 'Gast darf das Panel nicht sehen.');
        $panel->assertRedirect(route('login'));

        // JSON: unauthentifiziert → 401 (kein Datenaustritt).
        $this->getJson(route('offers.angebotsreife.json', 300))->assertStatus(401);
    }

    /** Auflage 1 — whereNumber: nicht-numerisches Segment → sauberes 404 statt möglichem 500. */
    public function test_nicht_numerische_id_wird_als_404_abgewiesen(): void
    {
        $res = $this->actingAs($this->admin())->get('/offers/angebotsreife/abc');
        $res->assertNotFound();

        $this->actingAs($this->admin())->get('/offers/angebotsreife/abc/json')->assertNotFound();
    }
}
