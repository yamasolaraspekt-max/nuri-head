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
}
