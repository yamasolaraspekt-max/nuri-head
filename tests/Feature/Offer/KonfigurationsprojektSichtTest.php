<?php

namespace Tests\Feature\Offer;

use App\Models\LeadAlternativeAdd;
use App\Models\User;
use App\Services\Offer\KonfigurationsprojektService;
use Database\Seeders\WpProduktFormularSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * AP-3a — READ-ONLY Konfigurationsprojekt-Sicht je Objekt.
 * Prüft Aggregation je Objekt (WP verdrahtet + Belastbarkeit; PV/Nicht-WP verdrahtet=false),
 * kein Write, PII-arm, leeres/fehlendes Objekt, Route auth/404/render.
 */
class KonfigurationsprojektSichtTest extends TestCase
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
        DB::table('master_sets')->insert(['id' => 1, 'article_group_id' => 2, 'name' => 'WP-Set', 'created_at' => now(), 'updated_at' => now()]);
        $this->seed(WpProduktFormularSeeder::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['password' => 'password', 'name' => 'kptest', 'is_admin' => true]);
    }

    private function service(): KonfigurationsprojektService
    {
        return app(KonfigurationsprojektService::class);
    }

    /**
     * Objekt mit WP-Modul (mit belastbarer Heizlast) + PV-Modul.
     *
     * @return array{customer:int, alternative:int, wp:int, pv:int}
     */
    private function objektMitWpUndPv(int $seed, array $pii = []): array
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        $wp = $seed + 3;
        $pv = $seed + 4;

        DB::table('new_leads')->insert(['id' => $customer, 'customer_type' => 'privat', 'name' => $pii['name'] ?? 'Kunde', 'lastname' => $pii['lastname'] ?? 'Test', 'email' => 'k@example.com', 'phone' => '0123', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $alt, 'lead_id' => $customer, 'street' => 'Weg 1', 'postcode' => '12345', 'city' => 'Stadt', 'created_at' => now(), 'updated_at' => now()]);

        DB::table('lead_product_lists')->insert(['id' => $wp, 'customer_id' => $customer, 'alternative_id' => $alt, 'product_id' => 2, 'status' => 'lead', 'interest' => 'WP', 'service_id' => 1, 'service' => 'complete', 'department_id' => 1, 'employee_id' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_product_lists')->insert(['id' => $pv, 'customer_id' => $customer, 'alternative_id' => $alt, 'product_id' => 3, 'status' => 'lead', 'interest' => 'PV', 'service_id' => 1, 'service' => 'complete', 'created_at' => now(), 'updated_at' => now()]);

        // Belastbare Heizlast am WP-Gewerk (raumweise DIN, wie der reale Adapter schreibt).
        DB::table('anforderungsprofile')->insert(['id' => $seed + 500, 'verankerbar_type' => LeadAlternativeAdd::class, 'verankerbar_id' => $alt, 'version' => 1, 'status' => 'aktiv', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('anforderungsprofil_werte')->insert(['anforderungsprofil_id' => $seed + 500, 'schluessel' => 'phi_hl_kw', 'wert' => '8.5', 'wert_num' => 8.5, 'datenlage' => 'berechnet', 'quelle' => 'HeizlastRechner', 'erfassungsweg' => 'berechnet', 'created_at' => now(), 'updated_at' => now()]);

        return ['customer' => $customer, 'alternative' => $alt, 'wp' => $wp, 'pv' => $pv];
    }

    // ---------- Aggregation ----------

    public function test_objekt_aggregiert_wp_und_pv(): void
    {
        $o = $this->objektMitWpUndPv(10);
        $c = $this->service()->fuerObjekt($o['alternative']);

        $this->assertTrue($c['anwendbar']);
        $this->assertSame(2, $c['gewerke_gesamt']);
        $this->assertSame(1, $c['gewerke_verdrahtet']); // nur WP verdrahtet

        $gewerke = array_column($c['module'], 'gewerk_id');
        $this->assertContains(2, $gewerke);
        $this->assertContains(3, $gewerke);

        $wpModul = collect($c['module'])->firstWhere('gewerk_id', 2);
        $pvModul = collect($c['module'])->firstWhere('gewerk_id', 3);

        $this->assertTrue($wpModul['verdrahtet']);
        $this->assertTrue($wpModul['ist_wp']);
        $this->assertFalse($pvModul['verdrahtet']);
        $this->assertFalse($pvModul['ist_wp']);
    }

    public function test_wp_belastbarkeit_wird_durchgereicht(): void
    {
        $o = $this->objektMitWpUndPv(20);
        $c = $this->service()->fuerObjekt($o['alternative']);
        $wpModul = collect($c['module'])->firstWhere('gewerk_id', 2);

        $this->assertNotNull($wpModul['heizlast_belastbarkeit']);
        $this->assertSame('belastbar', $wpModul['heizlast_belastbarkeit']['stufe']);
        $this->assertTrue($wpModul['auslegung_vorhanden']);
    }

    public function test_pv_ist_verdrahtet_false_ohne_belastbarkeit_und_ohne_fehler(): void
    {
        $o = $this->objektMitWpUndPv(30);
        $c = $this->service()->fuerObjekt($o['alternative']);
        $pvModul = collect($c['module'])->firstWhere('gewerk_id', 3);

        $this->assertFalse($pvModul['verdrahtet']);
        $this->assertNull($pvModul['heizlast_belastbarkeit']);
        $this->assertFalse($pvModul['auslegung_vorhanden']);
        $this->assertStringContainsString('nicht verdrahtet', $pvModul['hinweis']);
    }

    public function test_preisfaehigkeit_getrennt_und_omd_offen(): void
    {
        $o = $this->objektMitWpUndPv(35);
        $c = $this->service()->fuerObjekt($o['alternative']);
        $wpModul = collect($c['module'])->firstWhere('gewerk_id', 2);

        $this->assertFalse($wpModul['preis_moeglich']);
        $this->assertStringContainsString('OMD/IDS', $wpModul['preis_hinweis']);
        $this->assertArrayHasKey('technik_prozent', $wpModul);
    }

    // ---------- Rand ----------

    public function test_objekt_ohne_module_sauber(): void
    {
        $alt = 402;
        DB::table('new_leads')->insert(['id' => 401, 'customer_type' => 'privat', 'name' => 'K', 'lastname' => 'T', 'email' => 'k@example.com', 'phone' => '0', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $alt, 'lead_id' => 401, 'street' => 'W', 'postcode' => '1', 'city' => 'S', 'created_at' => now(), 'updated_at' => now()]);

        $c = $this->service()->fuerObjekt($alt);
        $this->assertTrue($c['anwendbar']);
        $this->assertSame(0, $c['gewerke_gesamt']);
        $this->assertSame([], $c['module']);
    }

    public function test_nicht_existentes_objekt_nicht_anwendbar(): void
    {
        $c = $this->service()->fuerObjekt(999999);
        $this->assertFalse($c['anwendbar']);
    }

    // ---------- PII-arm + kein Write ----------

    public function test_dto_ist_pii_arm(): void
    {
        $o = $this->objektMitWpUndPv(80, pii: ['name' => 'GeheimVorname', 'lastname' => 'GeheimNachname']);
        $dto = $this->service()->fuerObjekt($o['alternative']);
        $json = json_encode($dto, JSON_UNESCAPED_UNICODE);

        foreach (['GeheimVorname', 'GeheimNachname'] as $pii) {
            $this->assertStringNotContainsString($pii, (string) $json, "PII '$pii' darf nicht im DTO stehen.");
        }
        $this->assertStringContainsString('Objekt #'.$o['alternative'], (string) $json);
    }

    public function test_service_schreibt_nichts(): void
    {
        $o = $this->objektMitWpUndPv(50);
        $tabellen = ['anforderungsprofile', 'anforderungsprofil_werte', 'offers', 'offer_details', 'lead_product_lists', 'lead_alternative_adds'];
        $vorher = [];
        foreach ($tabellen as $t) {
            $vorher[$t] = DB::table($t)->count();
        }
        $this->service()->fuerObjekt($o['alternative']);
        foreach ($tabellen as $t) {
            $this->assertSame($vorher[$t], DB::table($t)->count(), "Tabelle {$t} verändert.");
        }
    }

    // ---------- Route ----------

    public function test_route_auth_geschuetzt(): void
    {
        $o = $this->objektMitWpUndPv(60);
        $this->get(route('objekt.konfiguration', $o['alternative']))->assertStatus(302);
    }

    public function test_route_non_numeric_404(): void
    {
        $this->actingAs($this->admin())->get('/objekt/abc/konfiguration')->assertNotFound();
    }

    public function test_route_rendert(): void
    {
        $o = $this->objektMitWpUndPv(70);
        $res = $this->actingAs($this->admin())->get(route('objekt.konfiguration', $o['alternative']));

        $res->assertOk();
        $res->assertSee('Konfigurationsprojekt', false);
        $res->assertSee('Objekt #'.$o['alternative'], false);
        $res->assertSee('Photovoltaik', false);
    }
}
