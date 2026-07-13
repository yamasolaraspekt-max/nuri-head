<?php

namespace Tests\Feature\Offer;

use App\Models\User;
use App\Services\Offer\WpKatalogMatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * P3-d0a — READ-ONLY WP-Katalog-Matching-Diagnose.
 * Beweist: disjunkte Populationen (Schnittmenge 0), Status je Zeile, kuratierter Match erkannt,
 * Konflikt (article_group ≠ WP), KEIN component_id / KEIN Preisanker, KEIN Write.
 */
class WpKatalogMatchingPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null']);

        DB::table('article_groups')->insert([
            ['id' => 2, 'article_group' => 'Wärmepumpe', 'initial' => 'WP', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'article_group' => 'Photovoltaik', 'initial' => 'PV', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('brands')->insert([
            ['id' => 64, 'name' => 'Viessmann', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 99, 'name' => 'NIBE', 'created_at' => now(), 'updated_at' => now()],
        ]);
        // Preis-Set-Produkt: bepreist, article_group=2, OHNE Specs.
        DB::table('products')->insert(['id' => 9, 'product' => 'Viessmann Komfort', 'model' => 'Komfort', 'brand_id' => 64, 'retail_price' => 10227.62, 'purchase_price' => 7358, 'article_group' => 2, 'created_at' => now(), 'updated_at' => now()]);
        // Technischer Kandidat: Specs, article_group=2, UNBEPREIST, nicht im Set.
        DB::table('products')->insert(['id' => 101, 'product' => 'NIBE S2125-8', 'model' => 'S2125-8', 'brand_id' => 99, 'retail_price' => null, 'purchase_price' => null, 'article_group' => 2, 'created_at' => now(), 'updated_at' => now()]);

        DB::table('master_sets')->insert(['id' => 2, 'article_group_id' => 2, 'name' => 'Wärmepumpe-Set Standard', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('master_set_components')->insert(['id' => 4, 'master_set_id' => 2, 'product_id' => 9, 'article_no' => 'ART-0006', 'unit_price' => 10227.62, 'purchase_price' => 7358, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('product_heat_pump_specs')->insert(['id' => 1, 'product_id' => 101, 'heizleistung_a7_w35_kw' => 8.0, 'scop_35' => 4.5, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function admin(): User
    {
        return User::factory()->create(['password' => 'password', 'name' => 'p3d0atest', 'is_admin' => true]);
    }

    private function wpLead(int $seed, int $productId = 2, array $pii = []): int
    {
        DB::table('new_leads')->insert(['id' => $seed + 1, 'customer_type' => 'privat', 'name' => $pii['name'] ?? 'Kunde', 'lastname' => $pii['lastname'] ?? 'Test', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $seed + 2, 'lead_id' => $seed + 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_product_lists')->insert(['id' => $seed, 'customer_id' => $seed + 1, 'alternative_id' => $seed + 2, 'product_id' => $productId, 'status' => 'lead', 'created_at' => now(), 'updated_at' => now()]);

        return $seed;
    }

    private function service(): WpKatalogMatchingService
    {
        return app(WpKatalogMatchingService::class);
    }

    private function findeStatus(array $liste, string $key, $val, string $statusKey = 'status'): ?array
    {
        foreach ($liste as $row) {
            if (($row[$key] ?? null) == $val) {
                return $row;
            }
        }

        return null;
    }

    // ---------- Ist-Zustand: disjunkt, Schnittmenge 0 ----------

    public function test_disjunkte_populationen_schnittmenge_null(): void
    {
        $r = $this->service()->fuerId($this->wpLead(10));

        $this->assertTrue($r['anwendbar']);
        $this->assertSame(0, $r['schnittmenge']);
        $this->assertFalse($r['auto_anker_moeglich']);
        $this->assertStringContainsString('Kein automatischer Preisanker', $r['banner']);

        $kandidat = $this->findeStatus($r['kandidaten'], 'modell', 'S2125-8');
        $this->assertNotNull($kandidat);
        $this->assertSame('kandidat_unbepreist', $kandidat['status']);
        $this->assertFalse($kandidat['bepreist']);
        $this->assertFalse($kandidat['im_set']);

        $setProdukt = $r['set_produkte'][0];
        $this->assertSame('set_ohne_specs', $setProdukt['status']);
        $this->assertFalse($setProdukt['hat_specs']);
        $this->assertSame(10227.62, $setProdukt['vk_diagnostisch']); // VK diagnostisch erlaubt
    }

    // ---------- kuratierter Match wird erkannt ----------

    public function test_kuratierter_match_wird_als_eindeutig_erkannt(): void
    {
        // Ein Produkt, das im Set UND mit Specs ist (article_group=2).
        DB::table('products')->insert(['id' => 200, 'product' => 'Marke Modell', 'model' => 'M1', 'brand_id' => 64, 'retail_price' => 9000, 'purchase_price' => 7000, 'article_group' => 2, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('master_set_components')->insert(['id' => 7, 'master_set_id' => 2, 'product_id' => 200, 'article_no' => 'ART-9000', 'unit_price' => 9000, 'purchase_price' => 7000, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('product_heat_pump_specs')->insert(['id' => 2, 'product_id' => 200, 'heizleistung_a7_w35_kw' => 9.0, 'scop_35' => 4.6, 'created_at' => now(), 'updated_at' => now()]);

        $r = $this->service()->fuerId($this->wpLead(20));

        $this->assertGreaterThanOrEqual(1, $r['schnittmenge']);
        $this->assertFalse($r['auto_anker_moeglich']); // trotzdem NIE Auto-Anker in P3-d0a
        $kandidat = $this->findeStatus($r['kandidaten'], 'modell', 'M1');
        $this->assertNotNull($kandidat);
        $this->assertSame('eindeutig_gemappt', $kandidat['status']);
    }

    // ---------- Konflikt: gemappt, aber Produkt nicht WP-klassifiziert ----------

    public function test_konflikt_bei_falscher_artikelgruppe(): void
    {
        DB::table('products')->insert(['id' => 300, 'product' => 'Falschgewerk', 'model' => 'F1', 'brand_id' => 64, 'retail_price' => 5000, 'purchase_price' => 4000, 'article_group' => 3, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('master_set_components')->insert(['id' => 8, 'master_set_id' => 2, 'product_id' => 300, 'article_no' => 'ART-3000', 'unit_price' => 5000, 'purchase_price' => 4000, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('product_heat_pump_specs')->insert(['id' => 3, 'product_id' => 300, 'heizleistung_a7_w35_kw' => 6.0, 'scop_35' => 4.0, 'created_at' => now(), 'updated_at' => now()]);

        $r = $this->service()->fuerId($this->wpLead(30));

        $kandidat = $this->findeStatus($r['kandidaten'], 'modell', 'F1');
        $this->assertNotNull($kandidat);
        $this->assertSame('konflikt', $kandidat['status']);
        $this->assertNotEmpty($kandidat['konflikt_felder']);
    }

    // ---------- Kein component_id / kein Preisanker ----------

    public function test_keine_anker_felder_in_der_ausgabe(): void
    {
        $r = $this->service()->fuerId($this->wpLead(40));

        foreach ($r['kandidaten'] as $k) {
            $this->assertArrayNotHasKey('component_id', $k);
            $this->assertArrayNotHasKey('preis_anker', $k);
        }
        foreach ($r['set_produkte'] as $s) {
            $this->assertArrayNotHasKey('component_id', $s);
            $this->assertArrayNotHasKey('preis_anker', $s);
        }
    }

    // ---------- Kein Write ----------

    public function test_service_schreibt_nichts(): void
    {
        $lpl = $this->wpLead(50);
        $tabellen = ['offers', 'offer_details', 'master_sets', 'master_set_components', 'products', 'product_heat_pump_specs', 'anforderungsprofile'];
        $vorher = [];
        foreach ($tabellen as $t) {
            $vorher[$t] = DB::table($t)->count();
        }

        $this->service()->fuerId($lpl);

        foreach ($tabellen as $t) {
            $this->assertSame($vorher[$t], DB::table($t)->count(), "Tabelle {$t} wurde verändert.");
        }
    }

    // ---------- Non-WP ----------

    public function test_non_wp_nicht_anwendbar(): void
    {
        $r = $this->service()->fuerId($this->wpLead(60, productId: 3));
        $this->assertFalse($r['anwendbar']);
    }

    // ---------- Route (read-only Panel) ----------

    public function test_panel_route_rendert_read_only(): void
    {
        $lpl = $this->wpLead(70, pii: ['name' => 'GeheimVorname', 'lastname' => 'GeheimNachname']);

        $res = $this->actingAs($this->admin())->get(route('offers.wp-katalog-matching', $lpl));

        $res->assertOk();
        $res->assertSee('WP-Katalog-Matching', false);
        $res->assertSee('Schnittmenge', false);
        $res->assertSee('Kein automatischer Preisanker', false);
        $res->assertDontSee('GeheimVorname', false);
        $res->assertDontSee('GeheimNachname', false);
    }

    public function test_panel_route_auth_geschuetzt(): void
    {
        $lpl = $this->wpLead(80);
        $this->get(route('offers.wp-katalog-matching', $lpl))->assertStatus(302);
    }
}
