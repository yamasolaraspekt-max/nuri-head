<?php

namespace Tests\Feature\Offer;

use App\Models\User;
use Database\Seeders\WpProduktFormularSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Paket 1b-a — Einbettung des WP-Angebotsreife-Panels ins Objektprofil (Lazy-AJAX-Partial).
 * Prüft den read-only Partial-Endpoint + die WP-gegatete Platzhalter-Einbettung in der Bestandsview.
 */
class WpAngebotsreifeEmbedTest extends TestCase
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
        return User::factory()->create(['password' => 'password', 'name' => 'embedtest', 'is_admin' => true]);
    }

    private function wpLead(int $id): void
    {
        $this->relax(function () use ($id) {
            DB::table('new_leads')->insert(['id' => $id + 1, 'customer_type' => 'privat', 'created_at' => now(), 'updated_at' => now()]);
            DB::table('lead_alternative_adds')->insert(['id' => $id + 2, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('lead_product_lists')->insert(['id' => $id, 'customer_id' => $id + 1, 'alternative_id' => $id + 2, 'product_id' => 2, 'status' => 'lead', 'created_at' => now(), 'updated_at' => now()]);
        });
    }

    public function test_partial_endpoint_liefert_nur_das_panel(): void
    {
        $this->wpLead(300);

        $res = $this->actingAs($this->admin())->get(route('offers.angebotsreife.panel', 300));

        $res->assertOk();
        $res->assertSee('Angebotsreife');
        $res->assertSee('Kunde vorhanden (Name + Kontakt)');
        // Nur Partial, KEIN Full-Page-Wrapper.
        $res->assertDontSee('<!doctype', false);
        $res->assertDontSee('<html', false);
    }

    public function test_partial_gast_und_nicht_numerisch_abgewiesen(): void
    {
        $this->get(route('offers.angebotsreife.panel', 300))->assertStatus(302); // Gast → Login-Redirect
        $this->actingAs($this->admin())->get('/offers/angebotsreife/abc/panel')->assertNotFound(); // whereNumber
    }

    public function test_objektprofil_bettet_panel_nur_fuer_wp_ein(): void
    {
        // Die Bestandsview ist zu stark gekoppelt für ein vollständiges Render im Test;
        // wir sichern die Einbettung + WP-Gating + read-only Route auf Quellebene ab.
        $src = file_get_contents(resource_path('views/admin/new_leads/customer_object_profile.blade.php'));

        // UX-2: die Reife-Einbettung liegt jetzt im read-only Tab-Block (Tab „Reife"), gleiche Route/WP-Gate/@once.
        $this->assertStringContainsString('ux2-tabblock', $src, 'Tab-Block fehlt.');
        $this->assertStringContainsString("offers.angebotsreife.panel', \$product->id", $src, 'Reife-Route/Ziel fehlt.');
        $this->assertStringContainsString("product_id ?? 0) === 2", $src, 'WP-Gating (product_id==2) fehlt.');
        $this->assertStringContainsString('@once', $src, 'Loader-Script muss einmalig (@once) sein.');
    }
}
