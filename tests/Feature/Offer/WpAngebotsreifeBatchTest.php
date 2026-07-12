<?php

namespace Tests\Feature\Offer;

use App\Models\User;
use Database\Seeders\WpProduktFormularSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Paket 1b-b — read-only Batch-Endpoint für Kanban-Angebotsreife-Badges + Kanban-Einbettung.
 */
class WpAngebotsreifeBatchTest extends TestCase
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
        return User::factory()->create(['password' => 'password', 'name' => 'batchtest', 'is_admin' => true]);
    }

    private function wpLead(int $id, array $pii = []): void
    {
        $this->relax(function () use ($id, $pii) {
            DB::table('new_leads')->insert(['id' => $id + 1, 'customer_type' => 'privat', 'name' => $pii['name'] ?? null, 'lastname' => $pii['lastname'] ?? null, 'email' => $pii['email'] ?? null, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('lead_alternative_adds')->insert(['id' => $id + 2, 'street' => $pii['street'] ?? null, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('lead_product_lists')->insert(['id' => $id, 'customer_id' => $id + 1, 'alternative_id' => $id + 2, 'product_id' => 2, 'status' => 'lead', 'created_at' => now(), 'updated_at' => now()]);
        });
    }

    public function test_batch_liefert_status_fuer_gueltige_ids(): void
    {
        $this->wpLead(10);
        $this->wpLead(20);

        $res = $this->actingAs($this->admin())->getJson(route('offers.angebotsreife.index', ['ids' => '10,20']));

        $res->assertOk();
        $res->assertJsonCount(2);
        $res->assertJsonStructure([['id', 'status', 'percent', 'angebotsfaehig']]);
        $ids = collect($res->json())->pluck('id')->sort()->values()->all();
        $this->assertSame([10, 20], $ids);
    }

    public function test_leere_und_kaputte_ids_geben_leeres_array(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->getJson(route('offers.angebotsreife.index', ['ids' => '']))->assertOk()->assertExactJson([]);
        $this->actingAs($admin)->getJson(route('offers.angebotsreife.index', ['ids' => 'abc,,x,-5,1.5']))->assertOk()->assertExactJson([]);
        $this->actingAs($admin)->getJson(route('offers.angebotsreife.index'))->assertOk()->assertExactJson([]);
    }

    public function test_cap_greift_bei_ueber_100_ids(): void
    {
        $res = $this->actingAs($this->admin())->getJson(route('offers.angebotsreife.index', ['ids' => implode(',', range(1, 150))]));

        $res->assertOk();
        $this->assertLessThanOrEqual(100, count($res->json()));
        $this->assertCount(100, $res->json());
    }

    public function test_array_form_ids_wird_numerisch_gefiltert_und_gecappt(): void
    {
        $this->wpLead(40);

        // Array-Form ?ids[]=40&ids[]=abc&ids[]=&ids[]=1..150 → nicht-numerisch raus, Cap 100.
        $ids = array_merge(['40', 'abc', '', '-5'], range(1, 150));
        $res = $this->actingAs($this->admin())->getJson(route('offers.angebotsreife.index', ['ids' => $ids]));

        $res->assertOk();
        $this->assertCount(100, $res->json(), 'Array-Form muss ebenfalls hart auf 100 gecappt sein.');
        $this->assertContains(40, collect($res->json())->pluck('id')->all());
    }

    public function test_gast_wird_abgewiesen(): void
    {
        $this->getJson(route('offers.angebotsreife.index', ['ids' => '10']))->assertStatus(401);
        $this->get(route('offers.angebotsreife.index', ['ids' => '10']))->assertStatus(302);
    }

    public function test_batch_enthaelt_keine_pii(): void
    {
        $this->wpLead(30, ['name' => 'GeheimVorname', 'lastname' => 'GeheimNachname', 'email' => 'geheim@example.com', 'street' => 'GeheimStrasse 7']);

        $res = $this->actingAs($this->admin())->getJson(route('offers.angebotsreife.index', ['ids' => '30']));

        $res->assertOk();
        // Nur die 4 erlaubten Schlüssel, keine PII-Werte im Body.
        $res->assertJsonStructure([['id', 'status', 'percent', 'angebotsfaehig']]);
        $body = $res->getContent();
        foreach (['GeheimVorname', 'GeheimNachname', 'geheim@example.com', 'GeheimStrasse'] as $pii) {
            $this->assertStringNotContainsString($pii, $body, "PII '$pii' darf nicht im Batch-JSON stehen.");
        }
        $keys = array_keys($res->json()[0]);
        sort($keys);
        $this->assertSame(['angebotsfaehig', 'id', 'percent', 'status'], $keys);
    }

    public function test_kanban_view_hat_filter_badge_und_wp_gating(): void
    {
        $src = file_get_contents(resource_path('views/admin/new_leads/layouts/kanban.blade.php'));

        $this->assertStringContainsString('kb-reife-filter', $src, 'Filterleiste fehlt.');
        $this->assertStringContainsString('kb-reife-badge', $src, 'Badge-Hook fehlt.');
        $this->assertStringContainsString('kb-reife-fbtn', $src, 'Filter-Buttons fehlen.');
        $this->assertStringContainsString("product_id ?? 0) === 2", $src, 'WP-Gating fehlt.');
        $this->assertStringContainsString("offers.angebotsreife.index", $src, 'Batch-Route fehlt.');
        $this->assertStringContainsString('@once', $src, 'Loader muss @once sein.');
    }
}
