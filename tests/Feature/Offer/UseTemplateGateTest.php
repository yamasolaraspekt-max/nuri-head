<?php

namespace Tests\Feature\Offer;

use App\Models\User;
use Database\Seeders\WpProduktFormularSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Paket 2b — Angebotsreife-GATE + WP-Objektbindung (Option B) in
 * OfferTemplatePickerController::useTemplate (Pfad 4 aus Paket 2a).
 *
 * sql_mode gelockert: getestet wird das Gate/die Vorprüfung (Early-Return VOR der Transaktion),
 * nicht die NOT-NULL-Strenge der Vorlagen-Inserts. FK-Checks aus für schlanke Fixtures.
 */
class UseTemplateGateTest extends TestCase
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
        return User::factory()->create(['password' => 'password', 'name' => 'tpltest', 'is_admin' => true]);
    }

    /** @return array{customer:int, alternative:int, lpl:int} */
    private function wpKombi(int $seed, bool $reif, array $pii = [], string $status = 'lead', int $productId = 2): array
    {
        $customer = $seed + 1;
        $alt = $seed + 2;

        DB::table('new_leads')->insert([
            'id' => $customer, 'customer_type' => 'privat',
            'name' => $pii['name'] ?? 'Kunde', 'lastname' => $pii['lastname'] ?? 'Test',
            'email' => $pii['email'] ?? 'k@example.com', 'phone' => $pii['phone'] ?? '0123456',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('lead_alternative_adds')->insert([
            'id' => $alt, 'lead_id' => $customer,
            'street' => $pii['street'] ?? 'Musterweg 1', 'postcode' => '12345', 'city' => 'Musterstadt',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('lead_product_lists')->insert([
            'id' => $seed, 'customer_id' => $customer, 'alternative_id' => $alt, 'product_id' => $productId,
            'status' => $status, 'interest' => 'WP gewünscht', 'service_id' => 1, 'service' => 'complete',
            'department_id' => $reif ? 1 : null, 'employee_id' => $reif ? 1 : null,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return ['customer' => $customer, 'alternative' => $alt, 'lpl' => $seed];
    }

    private function makeTemplate(int $id, int $articleGroupId = 2): int
    {
        DB::table('offer_templates')->insert([
            'id' => $id,
            'name' => 'WP-Vorlage',
            'article_group_id' => $articleGroupId,
            'department_id' => 1,
            'brand_color' => '#2563eb',
            'brand_mode' => 'light',
            'brand_logo_url' => '',
            'company_name' => 'Testfirma',
            'cover_text_html' => '<p>Deckblatt</p>',
            'sections' => json_encode([]),
            'placed_images' => json_encode([]),
            'biography_data' => json_encode([]),
            'usage_count' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return $id;
    }

    private function assertNichtsErzeugt(int $templateId): void
    {
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('offer_folders', 0);
        $this->assertDatabaseCount('offer_details', 0);
        $this->assertDatabaseHas('offer_templates', ['id' => $templateId, 'usage_count' => 0]);
    }

    // ---------- Option B: WP ohne Objekt ----------

    public function test_wp_ohne_objekt_wird_abgewiesen(): void
    {
        $k = $this->wpKombi(100, reif: true); // sogar reif — trotzdem Objektpflicht
        $tpl = $this->makeTemplate(10);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'product_id' => 2, // KEIN alternative_id
        ]);

        $res->assertStatus(422)->assertJsonPath('code', 'OFFER_OBJECT_REQUIRED');
        $this->assertNichtsErzeugt($tpl);
    }

    // ---------- WP + Objekt + Blocker ----------

    public function test_wp_mit_objekt_und_blocker_wird_abgewiesen(): void
    {
        $k = $this->wpKombi(200, reif: false);
        $tpl = $this->makeTemplate(20);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2,
        ]);

        $res->assertStatus(422)->assertJsonPath('code', 'OFFER_NOT_READY');
        $this->assertNichtsErzeugt($tpl);
    }

    // ---------- WP + Objekt + reif ----------

    public function test_wp_reif_verwendet_vorlage(): void
    {
        $k = $this->wpKombi(300, reif: true);
        $tpl = $this->makeTemplate(30);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2, 'folder_name' => 'Ordner',
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('offers', 1);
        $this->assertDatabaseCount('offer_folders', 1);
        $this->assertDatabaseCount('offer_details', 1);
        $this->assertDatabaseHas('offer_templates', ['id' => $tpl, 'usage_count' => 1]);
    }

    // ---------- Non-WP ----------

    public function test_non_wp_wird_durchgelassen(): void
    {
        $k = $this->wpKombi(400, reif: false, productId: 3);
        $tpl = $this->makeTemplate(40, articleGroupId: 3);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 3, 'folder_name' => 'Ordner',
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('offers', 1);
        $this->assertDatabaseHas('offer_templates', ['id' => $tpl, 'usage_count' => 1]);
    }

    // ---------- WP + Objekt, aber kein LPL (Legacy) ----------

    public function test_wp_ohne_lpl_wird_durchgelassen(): void
    {
        // Kunde + Objekt existieren, aber kein lead_product_lists-Eintrag für die Kombi.
        DB::table('new_leads')->insert(['id' => 501, 'customer_type' => 'privat', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => 502, 'lead_id' => 501, 'created_at' => now(), 'updated_at' => now()]);
        $tpl = $this->makeTemplate(50);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => 501, 'alternative_id' => 502, 'product_id' => 2, 'folder_name' => 'Ordner',
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('offers', 1);
    }

    // ---------- Datenschutz ----------

    public function test_fehlerantwort_enthaelt_keine_pii(): void
    {
        $k = $this->wpKombi(700, reif: false, pii: [
            'name' => 'GeheimVorname', 'lastname' => 'GeheimNachname',
            'email' => 'geheim@example.com', 'street' => 'GeheimStrasse 7',
        ]);
        $tpl = $this->makeTemplate(70);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2,
        ]);

        $res->assertStatus(422);
        $body = $res->getContent();
        foreach (['GeheimVorname', 'GeheimNachname', 'geheim@example.com', 'GeheimStrasse'] as $pii) {
            $this->assertStringNotContainsString($pii, $body, "PII '$pii' darf nicht in der Fehlerantwort stehen.");
        }
    }

    // ---------- H1: optionales folder_name ohne "Undefined array key" ----------

    public function test_ohne_folder_name_kein_500_und_fallback_name(): void
    {
        $k = $this->wpKombi(800, reif: true);
        $tpl = $this->makeTemplate(80); // Template-Name = "WP-Vorlage"

        // KEIN folder_name im Request → früher "Undefined array key".
        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2,
        ]);

        $this->assertLessThan(500, $res->status(), 'Kein 500 (Undefined array key behoben).');
        $res->assertSuccessful();
        $this->assertDatabaseCount('offer_folders', 1);
        $name = DB::table('offer_folders')->value('name');
        $this->assertStringStartsWith('WP-Vorlage', (string) $name, 'Fallback = Template-Name bei fehlendem folder_name.');
    }

    public function test_mit_folder_name_wird_exakt_uebernommen(): void
    {
        $k = $this->wpKombi(810, reif: true);
        $tpl = $this->makeTemplate(81);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2,
            'folder_name' => 'Mein Ordner',
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseHas('offer_folders', ['name' => 'Mein Ordner']);
    }

    // ---------- H2: alternative_id validieren / Kunden-Zugehörigkeit ----------

    public function test_h2_null_wird_nicht_als_invalid_abgewiesen(): void
    {
        // null bleibt wie bisher: H2-Guard feuert NICHT für null; WP+null → weiterhin OFFER_OBJECT_REQUIRED
        // (nicht OFFER_OBJECT_INVALID). Beweist, dass die Zugehörigkeitsprüfung null nicht fälschlich sperrt.
        $k = $this->wpKombi(820, reif: true);
        $tpl = $this->makeTemplate(82);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'product_id' => 2, 'folder_name' => 'Ordner', // KEIN alternative_id
        ]);

        $res->assertStatus(422)->assertJsonPath('code', 'OFFER_OBJECT_REQUIRED');
        $this->assertDatabaseCount('offers', 0);
    }

    public function test_h2_gueltiges_eigenes_objekt_wird_genutzt(): void
    {
        $k = $this->wpKombi(830, reif: true);
        $tpl = $this->makeTemplate(83);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2, 'folder_name' => 'Ordner',
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('offers', 1);
        $this->assertDatabaseCount('offer_folders', 1);
    }

    public function test_h2_nicht_existente_alternative_id_wird_abgewiesen(): void
    {
        $k = $this->wpKombi(840, reif: true);
        $tpl = $this->makeTemplate(84);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'alternative_id' => 999999, 'product_id' => 2, 'folder_name' => 'Ordner',
        ]);

        $res->assertStatus(422);
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('offer_folders', 0);
        $this->assertDatabaseCount('offer_details', 0);
    }

    public function test_h2_fremde_alternative_id_wird_abgewiesen(): void
    {
        $a = $this->wpKombi(850, reif: true);         // Kunde A + eigenes Objekt
        $b = $this->wpKombi(860, reif: true);         // Kunde B + eigenes Objekt
        $tpl = $this->makeTemplate(85);

        // Kunde A verwendet das Objekt von Kunde B → muss abgewiesen werden.
        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $a['customer'], 'alternative_id' => $b['alternative'], 'product_id' => 2, 'folder_name' => 'Ordner',
        ]);

        $res->assertStatus(422)->assertJsonPath('code', 'OFFER_OBJECT_INVALID');
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('offer_folders', 0);
        $this->assertDatabaseCount('offer_details', 0);
    }

    // ---------- H3: Non-WP + null → sauberes 422 statt 500 (offers.alternative_id NOT NULL) ----------

    public function test_h3_nonwp_ohne_objekt_wird_abgewiesen(): void
    {
        // Non-WP (Produkt 3) OHNE alternative_id → früher 500 (offers.alternative_id NOT NULL), jetzt 422.
        $k = $this->wpKombi(870, reif: false, productId: 3);
        $tpl = $this->makeTemplate(87, articleGroupId: 3);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'product_id' => 3, 'folder_name' => 'Ordner', // KEIN alternative_id
        ]);

        $res->assertStatus(422)->assertJsonPath('code', 'OFFER_OBJECT_REQUIRED');
        $this->assertLessThan(500, $res->status(), 'Kein 500 mehr (Guard greift vor Offer::create).');
        $this->assertDatabaseCount('offers', 0);
        $this->assertDatabaseCount('offer_folders', 0);
        $this->assertDatabaseCount('offer_details', 0);
    }

    public function test_h3_nonwp_mit_gueltigem_objekt_wird_angelegt(): void
    {
        // Non-WP MIT gültigem eigenem Objekt → Anlage wie bisher (unverändert).
        $k = $this->wpKombi(880, reif: false, productId: 3);
        $tpl = $this->makeTemplate(88, articleGroupId: 3);

        $res = $this->actingAs($this->admin())->postJson(route('offer-templates.use', $tpl), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 3, 'folder_name' => 'Ordner',
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('offers', 1);
    }
}
