<?php

namespace Tests\Feature\Offer;

use App\Models\LeadAlternativeAdd;
use App\Models\User;
use App\Services\Offer\AuslegungVorschlagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * P3-c-preview — READ-ONLY Auslegungs-Positionsvorschau (WP).
 * Beweist: Vorschlagsstruktur, keine Preise/kein component_id, Operanden-Gate, KEIN Write.
 */
class AuslegungVorschlagPreviewTest extends TestCase
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
        // WP-Set + Komponenten (nur für den Katalog-Hinweis; wird NICHT als Anker verwendet).
        DB::table('master_sets')->insert(['id' => 2, 'article_group_id' => 2, 'name' => 'Wärmepumpe-Set Standard', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('master_set_components')->insert([
            ['id' => 4, 'master_set_id' => 2, 'product_id' => 9, 'unit_price' => 12237.12, 'purchase_price' => 9712, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'master_set_id' => 2, 'product_id' => 10, 'unit_price' => 10227.62, 'purchase_price' => 7358, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create(['password' => 'password', 'name' => 'p3ctest', 'is_admin' => true]);
    }

    /**
     * WP-Vorgang + optional aktives Anforderungsprofil mit Werten.
     * @return array{customer:int, alternative:int, lpl:int}
     */
    private function wpVorgang(int $seed, array $werte = [], int $productId = 2, array $pii = []): array
    {
        $customer = $seed + 1;
        $alt = $seed + 2;

        DB::table('new_leads')->insert([
            'id' => $customer, 'customer_type' => 'privat',
            'name' => $pii['name'] ?? 'Kunde', 'lastname' => $pii['lastname'] ?? 'Test',
            'email' => $pii['email'] ?? 'k@example.com', 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('lead_alternative_adds')->insert([
            'id' => $alt, 'lead_id' => $customer, 'street' => 'Musterweg 1', 'postcode' => '12345', 'city' => 'Stadt',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('lead_product_lists')->insert([
            'id' => $seed, 'customer_id' => $customer, 'alternative_id' => $alt, 'product_id' => $productId,
            'status' => 'lead', 'created_at' => now(), 'updated_at' => now(),
        ]);

        if ($werte !== []) {
            DB::table('anforderungsprofile')->insert([
                'id' => $seed + 500, 'verankerbar_type' => LeadAlternativeAdd::class, 'verankerbar_id' => $alt,
                'version' => 1, 'status' => 'aktiv', 'created_at' => now(), 'updated_at' => now(),
            ]);
            foreach ($werte as $key => $num) {
                DB::table('anforderungsprofil_werte')->insert([
                    'anforderungsprofil_id' => $seed + 500, 'schluessel' => $key,
                    'wert' => (string) $num, 'wert_num' => $num, 'einheit' => null,
                    'datenlage' => 'berechnet', 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        return ['customer' => $customer, 'alternative' => $alt, 'lpl' => $seed];
    }

    private function service(): AuslegungVorschlagService
    {
        return app(AuslegungVorschlagService::class);
    }

    // ---------- Struktur ----------

    public function test_wp_mit_profil_liefert_vorschlagsstruktur(): void
    {
        $k = $this->wpVorgang(10, ['phi_hl_kw' => 8.5, 'vorlauf_c' => 45]);
        $r = $this->service()->fuerId($k['lpl']);

        $this->assertTrue($r['anwendbar']);
        $this->assertSame(2, $r['gewerk_id']);
        $this->assertTrue($r['auslegung_vorhanden']);
        $klassen = array_column($r['sections'][0]['items'], 'klasse');
        $this->assertSame(['wp_geraet', 'pufferspeicher', 'warmwasserspeicher', 'hydraulik_zubehoer', 'montage_lohn'], $klassen);
    }

    // ---------- Kein Preis / kein component_id ----------

    public function test_alle_positionen_ohne_preis_und_ohne_component_id(): void
    {
        $k = $this->wpVorgang(20, ['phi_hl_kw' => 9.0, 'vorlauf_c' => 40]);
        $r = $this->service()->fuerId($k['lpl']);

        foreach ($r['sections'][0]['items'] as $item) {
            $this->assertSame('katalog_anker_fehlt', $item['preis_status']);
            $this->assertNull($item['component_id']);
            $this->assertArrayNotHasKey('price', $item);
            $this->assertArrayNotHasKey('unit_price', $item);
            $this->assertArrayNotHasKey('purchase_price', $item);
        }
    }

    // ---------- Datenlage / Operanden-Gate ----------

    public function test_wp_geraet_nutzt_heizlast_datenlage(): void
    {
        $k = $this->wpVorgang(30, ['phi_hl_kw' => 8.5]);
        $r = $this->service()->fuerId($k['lpl']);
        $geraet = $r['sections'][0]['items'][0];

        $this->assertSame('wp_geraet', $geraet['klasse']);
        $this->assertSame('berechnet', $geraet['datenlage']);
        $this->assertSame('gruen', $geraet['ampel']);
        $this->assertNotNull($geraet['kennzahl']);
        $this->assertNull($geraet['aufgabe']);
    }

    public function test_fehlender_operand_wird_als_aufgabe_markiert(): void
    {
        // Aktives Profil, aber OHNE Heizlast-Wert.
        $k = $this->wpVorgang(40, ['vorlauf_c' => 45]);
        $r = $this->service()->fuerId($k['lpl']);
        $geraet = $r['sections'][0]['items'][0];

        $this->assertSame('fehlt', $geraet['datenlage']);
        $this->assertSame('grau', $geraet['ampel']);
        $this->assertNotEmpty($geraet['aufgabe']);
        $this->assertGreaterThan(0, $r['offene_aufgaben']);
    }

    public function test_ohne_profil_kein_500_sondern_hinweis(): void
    {
        $k = $this->wpVorgang(50); // kein Profil
        $r = $this->service()->fuerId($k['lpl']);

        $this->assertTrue($r['anwendbar']);
        $this->assertFalse($r['auslegung_vorhanden']);
        $this->assertStringContainsString('keine auslegung', mb_strtolower($r['kurzergebnis']));
        $this->assertNotEmpty($r['sections'][0]['items']); // Positionen bleiben (als Aufgaben)
    }

    public function test_non_wp_keine_vorschau(): void
    {
        $k = $this->wpVorgang(60, ['phi_hl_kw' => 8.5], productId: 3);
        $r = $this->service()->fuerId($k['lpl']);

        $this->assertFalse($r['anwendbar']);
        $this->assertSame([], $r['sections']);
    }

    // ---------- Katalog-Hinweis (nur Hinweis, kein Anker) ----------

    public function test_katalog_hinweis_meldet_wp_set(): void
    {
        $k = $this->wpVorgang(70, ['phi_hl_kw' => 8.5]);
        $r = $this->service()->fuerId($k['lpl']);

        $this->assertTrue($r['katalog_hinweis']['vorhanden']);
        $this->assertStringContainsString('WP-Set', $r['katalog_hinweis']['text']);
    }

    // ---------- KEIN Write ----------

    public function test_service_schreibt_nichts(): void
    {
        $k = $this->wpVorgang(80, ['phi_hl_kw' => 8.5, 'vorlauf_c' => 45]);

        $vorher = [
            'offers' => DB::table('offers')->count(),
            'offer_details' => DB::table('offer_details')->count(),
            'anforderungsprofile' => DB::table('anforderungsprofile')->count(),
            'anforderungsprofil_werte' => DB::table('anforderungsprofil_werte')->count(),
        ];

        $this->service()->fuerId($k['lpl']);

        $this->assertSame($vorher['offers'], DB::table('offers')->count());
        $this->assertSame($vorher['offer_details'], DB::table('offer_details')->count());
        $this->assertSame($vorher['anforderungsprofile'], DB::table('anforderungsprofile')->count());
        $this->assertSame($vorher['anforderungsprofil_werte'], DB::table('anforderungsprofil_werte')->count());
    }

    // ---------- Route (read-only Panel) ----------

    public function test_panel_route_rendert_read_only(): void
    {
        $k = $this->wpVorgang(90, ['phi_hl_kw' => 8.5, 'vorlauf_c' => 45], pii: ['name' => 'GeheimVorname', 'lastname' => 'GeheimNachname']);

        $res = $this->actingAs($this->admin())->get(route('offers.auslegung.vorschau', $k['lpl']));

        $res->assertOk();
        $res->assertSee('Auslegungs-Vorschlag', false);
        $res->assertSee('nicht bepreist', false);
        $res->assertSee('Datenlage', false);
        // Keine PII im Panel.
        $res->assertDontSee('GeheimVorname', false);
        $res->assertDontSee('GeheimNachname', false);
    }

    public function test_panel_route_auth_geschuetzt(): void
    {
        $k = $this->wpVorgang(95, ['phi_hl_kw' => 8.5]);
        $this->get(route('offers.auslegung.vorschau', $k['lpl']))->assertStatus(302);
    }
}
