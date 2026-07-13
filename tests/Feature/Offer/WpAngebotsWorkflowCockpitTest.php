<?php

namespace Tests\Feature\Offer;

use App\Models\LeadAlternativeAdd;
use App\Models\User;
use App\Services\Offer\WpAngebotsWorkflowService;
use Database\Seeders\WpProduktFormularSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Paket 4a — READ-ONLY WP-Angebotsworkflow-Cockpit.
 * Prüft Aggregations-Service (Struktur/Technik-Preis-Trennung/kein Write) + Route (auth/404/Non-WP/render).
 */
class WpAngebotsWorkflowCockpitTest extends TestCase
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
        return User::factory()->create(['password' => 'password', 'name' => 'wftest', 'is_admin' => true]);
    }

    /** @return array{customer:int, alternative:int, lpl:int} */
    private function wpVorgang(int $seed, bool $reif, int $productId = 2, ?float $heizlast = null, array $pii = []): array
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        DB::table('new_leads')->insert(['id' => $customer, 'customer_type' => 'privat', 'name' => $pii['name'] ?? 'Kunde', 'lastname' => $pii['lastname'] ?? 'Test', 'email' => 'k@example.com', 'phone' => '0123', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $alt, 'lead_id' => $customer, 'street' => 'Weg 1', 'postcode' => '12345', 'city' => 'Stadt', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_product_lists')->insert(['id' => $seed, 'customer_id' => $customer, 'alternative_id' => $alt, 'product_id' => $productId, 'status' => 'lead', 'interest' => 'WP', 'service_id' => 1, 'service' => 'complete', 'department_id' => $reif ? 1 : null, 'employee_id' => $reif ? 1 : null, 'created_at' => now(), 'updated_at' => now()]);

        if ($heizlast !== null) {
            DB::table('anforderungsprofile')->insert(['id' => $seed + 500, 'verankerbar_type' => LeadAlternativeAdd::class, 'verankerbar_id' => $alt, 'version' => 1, 'status' => 'aktiv', 'created_at' => now(), 'updated_at' => now()]);
            DB::table('anforderungsprofil_werte')->insert(['anforderungsprofil_id' => $seed + 500, 'schluessel' => 'phi_hl_kw', 'wert' => (string) $heizlast, 'wert_num' => $heizlast, 'datenlage' => 'berechnet', 'created_at' => now(), 'updated_at' => now()]);
        }

        return ['customer' => $customer, 'alternative' => $alt, 'lpl' => $seed];
    }

    private function service(): WpAngebotsWorkflowService
    {
        return app(WpAngebotsWorkflowService::class);
    }

    // ---------- Service-Struktur ----------

    public function test_cockpit_liefert_5_schritte_und_getrennte_prozente(): void
    {
        $k = $this->wpVorgang(10, reif: true, heizlast: 8.5);
        $c = $this->service()->fuerId($k['lpl']);

        $this->assertTrue($c['anwendbar']);
        $this->assertSame(2, $c['gewerk_id']);
        $keys = array_column($c['schritte'], 'key');
        $this->assertSame(['bedarf', 'reife', 'auslegung', 'preis', 'angebot'], $keys);
        // Technik und Preis getrennt; Preisfähigkeit heute blockiert (Schnittmenge 0).
        $this->assertArrayHasKey('technik_prozent', $c);
        $this->assertArrayHasKey('preis_prozent', $c);
        $this->assertFalse($c['preis_moeglich']);
        $this->assertSame(0, $c['preis_prozent']);
        // Preis-Schritt ist kaufmännisch + blockiert.
        $preis = collect($c['schritte'])->firstWhere('key', 'preis');
        $this->assertSame('kaufmaennisch', $preis['zone']);
        $this->assertSame('blockiert', $preis['status']);
    }

    public function test_gesamt_ohne_preis_hoechstens_85(): void
    {
        $k = $this->wpVorgang(20, reif: true, heizlast: 8.5);
        $c = $this->service()->fuerId($k['lpl']);
        // gesamt = 0.85*technik + 0.15*preis(=0) ≤ 85.
        $this->assertLessThanOrEqual(85, $c['gesamt_prozent']);
        $this->assertSame((int) round(0.85 * $c['technik_prozent']), $c['gesamt_prozent']);
    }

    public function test_wp_ohne_daten_zeigt_offene_aufgaben_kein_fehler(): void
    {
        $k = $this->wpVorgang(30, reif: false); // kein Profil, kein reif
        $c = $this->service()->fuerId($k['lpl']);

        $this->assertTrue($c['anwendbar']);
        $this->assertGreaterThan(0, $c['aufgaben']['offen']);
        $this->assertNotEmpty($c['naechste_aktion']['label']);
        $auslegung = collect($c['schritte'])->firstWhere('key', 'auslegung');
        $this->assertSame('offen', $auslegung['status']);
    }

    public function test_non_wp_nicht_anwendbar(): void
    {
        $k = $this->wpVorgang(40, reif: false, productId: 3);
        $c = $this->service()->fuerId($k['lpl']);
        $this->assertFalse($c['anwendbar']);
    }

    public function test_service_schreibt_nichts(): void
    {
        $k = $this->wpVorgang(50, reif: true, heizlast: 8.5);
        $tabellen = ['offers', 'offer_details', 'anforderungsprofile', 'anforderungsprofil_werte', 'lead_product_lists'];
        $vorher = [];
        foreach ($tabellen as $t) {
            $vorher[$t] = DB::table($t)->count();
        }
        $this->service()->fuerId($k['lpl']);
        foreach ($tabellen as $t) {
            $this->assertSame($vorher[$t], DB::table($t)->count(), "Tabelle {$t} verändert.");
        }
    }

    // ---------- Route ----------

    public function test_route_auth_geschuetzt(): void
    {
        $k = $this->wpVorgang(60, reif: true, heizlast: 8.5);
        $this->get(route('offers.workflow', $k['lpl']))->assertStatus(302);
    }

    public function test_route_non_numeric_404(): void
    {
        $this->actingAs($this->admin())->get('/offers/workflow/abc')->assertNotFound();
    }

    public function test_route_rendert_cockpit(): void
    {
        $k = $this->wpVorgang(70, reif: true, heizlast: 8.5);

        $res = $this->actingAs($this->admin())->get(route('offers.workflow', $k['lpl']));

        $res->assertOk();
        $res->assertSee('WP-Angebotsworkflow', false);
        $res->assertSee('Prozessreife gesamt', false);
        $res->assertSee('Vorgang #'.$k['lpl'], false);
    }

    public function test_cockpit_dto_enthaelt_keine_pii(): void
    {
        // Die 4a-Garantie: der Cockpit-DTO selbst trägt keine Kundendaten (nur „Vorgang #id").
        // (Die umgebende admin.layouts.app-Chrome rendert eigene Kontextdaten — Bestand, außerhalb 4a.)
        $k = $this->wpVorgang(80, reif: true, heizlast: 8.5, pii: ['name' => 'GeheimVorname', 'lastname' => 'GeheimNachname']);

        $dto = $this->service()->fuerId($k['lpl']);
        $json = json_encode($dto, JSON_UNESCAPED_UNICODE);

        foreach (['GeheimVorname', 'GeheimNachname'] as $pii) {
            $this->assertStringNotContainsString($pii, (string) $json, "PII '$pii' darf nicht im Cockpit-DTO stehen.");
        }
        $this->assertStringContainsString('Vorgang #'.$k['lpl'], (string) $json);
    }
}
