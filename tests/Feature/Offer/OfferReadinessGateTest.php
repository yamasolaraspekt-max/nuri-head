<?php

namespace Tests\Feature\Offer;

use App\Models\User;
use App\Services\Offer\OfferReadinessGate;
use Database\Seeders\WpProduktFormularSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Paket 2a — Angebotsreife-GATE im Erstellpfad.
 *
 * Deckt ab: Gate-Entscheidungslogik (Unit) + die drei Erstellpfade (Feature) blockiert/reif,
 * Non-WP durchgelassen, Kein-LPL durchgelassen, keine PII in der Fehlerantwort.
 *
 * sql_mode wird für die ganze Klasse gelockert: getestet wird das GATE (Early-Return), nicht die
 * NOT-NULL-Strenge der Angebots-Inserts. FK-Checks aus, damit Fixtures ohne alle Referenzzeilen sitzen.
 */
class OfferReadinessGateTest extends TestCase
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
        DB::table('employees')->insert(['id' => 1, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('planings')->insert(['id' => 1, 'status' => 'lead', 'created_at' => now(), 'updated_at' => now()]);
        $this->seed(WpProduktFormularSeeder::class);
    }

    private function admin(): User
    {
        return User::factory()->create(['password' => 'password', 'name' => 'gatetest', 'is_admin' => true]);
    }

    /**
     * WP-Kunde/Objekt/Gewerkzeile. $reif=true erfüllt ALLE Blocker; sonst fehlt „Zuständigkeit".
     *
     * @return array{customer:int, alternative:int, lpl:int}
     */
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

    private function gate(): OfferReadinessGate
    {
        return app(OfferReadinessGate::class);
    }

    // ---------- GATE-Entscheidungslogik (Unit) ----------

    public function test_gate_non_wp_gibt_null(): void
    {
        $k = $this->wpKombi(10, reif: false); // blockierte Kombi, aber Produkt 3 (Non-WP)
        $this->assertNull($this->gate()->pruefe($k['customer'], $k['alternative'], 3));
    }

    public function test_gate_kein_lpl_gibt_null(): void
    {
        // Kunde/Objekt existieren, aber kein lead_product_lists-Eintrag für die Kombi.
        DB::table('new_leads')->insert(['id' => 501, 'customer_type' => 'privat', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => 502, 'lead_id' => 501, 'created_at' => now(), 'updated_at' => now()]);
        $this->assertNull($this->gate()->pruefe(501, 502, 2));
    }

    public function test_gate_wp_mit_blocker_gibt_verletzung(): void
    {
        $k = $this->wpKombi(20, reif: false);
        $v = $this->gate()->pruefe($k['customer'], $k['alternative'], 2);

        $this->assertIsArray($v);
        $this->assertNotEmpty($v['blocker']);
        $this->assertArrayHasKey('hinweis', $v);
    }

    public function test_gate_wp_ohne_blocker_gibt_null(): void
    {
        $k = $this->wpKombi(30, reif: true);
        $this->assertNull($this->gate()->pruefe($k['customer'], $k['alternative'], 2));
    }

    // ---------- Pfad 1: OfferWizardController::createOffer ----------

    public function test_pfad1_wizard_blockiert_wp_kein_offer(): void
    {
        $k = $this->wpKombi(100, reif: false);

        $res = $this->actingAs($this->admin())->postJson(route('offers.wizard.create'), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_ids' => [2],
        ]);

        $res->assertStatus(422)->assertJsonPath('code', 'OFFER_NOT_READY');
        $this->assertDatabaseCount('offers', 0);
    }

    public function test_pfad1_wizard_reif_erstellt_offer(): void
    {
        $k = $this->wpKombi(110, reif: true);

        $res = $this->actingAs($this->admin())->postJson(route('offers.wizard.create'), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_ids' => [2],
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('offers', 1);
    }

    // ---------- Pfad 2: OfferController::store ----------

    public function test_pfad2_offercontroller_blockiert_wp_kein_offer(): void
    {
        $k = $this->wpKombi(200, reif: false);

        $res = $this->actingAs($this->admin())->postJson(route('admin.offers.store'), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2,
            'folder_name' => 'Ordner', 'folder_color' => '#fff',
        ]);

        $res->assertStatus(422)->assertJsonPath('code', 'OFFER_NOT_READY');
        $this->assertDatabaseCount('offers', 0);
    }

    public function test_pfad2_offercontroller_reif_erstellt_offer(): void
    {
        $k = $this->wpKombi(210, reif: true);

        $res = $this->actingAs($this->admin())->postJson(route('admin.offers.store'), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2,
            'folder_name' => 'Ordner', 'folder_color' => '#fff',
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('offers', 1);
    }

    // ---------- Pfad 3: OffersController::store ----------

    public function test_pfad3_offerscontroller_blockiert_wp_kein_offer(): void
    {
        $k = $this->wpKombi(300, reif: false);

        $res = $this->actingAs($this->admin())->postJson(route('offer.save'), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2, 'service' => 'Standard',
            'employee_id' => 1, 'plan_id' => 1,
        ]);

        $res->assertStatus(422)->assertJsonPath('code', 'OFFER_NOT_READY');
        $this->assertDatabaseCount('offers', 0);
    }

    public function test_pfad3_offerscontroller_reif_erstellt_offer(): void
    {
        $k = $this->wpKombi(310, reif: true);

        $res = $this->actingAs($this->admin())->postJson(route('offer.save'), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2, 'service' => 'Standard',
            'employee_id' => 1, 'plan_id' => 1,
        ]);

        $res->assertOk();
        $this->assertDatabaseCount('offers', 1);
    }

    // ---------- Nicht-Ziele-Durchlass ----------

    public function test_non_wp_wird_durchgelassen(): void
    {
        // Produkt 3 (PV) → Gate greift nicht, Angebot entsteht trotz „unreifer" Datenlage.
        $k = $this->wpKombi(400, reif: false);

        $res = $this->actingAs($this->admin())->postJson(route('offer.save'), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 3, 'service' => 'Standard',
            'employee_id' => 1, 'plan_id' => 1,
        ]);

        $res->assertOk();
        $this->assertDatabaseCount('offers', 1);
    }

    public function test_kein_lpl_wird_durchgelassen(): void
    {
        // WP (Produkt 2), aber kein lead_product_lists-Eintrag → Legacy-/ungegateter Fall → durchlassen.
        DB::table('new_leads')->insert(['id' => 601, 'customer_type' => 'privat', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => 602, 'lead_id' => 601, 'created_at' => now(), 'updated_at' => now()]);

        $res = $this->actingAs($this->admin())->postJson(route('offer.save'), [
            'customer_id' => 601, 'alternative_id' => 602, 'product_id' => 2, 'service' => 'Standard',
            'employee_id' => 1, 'plan_id' => 1,
        ]);

        $res->assertOk();
        $this->assertDatabaseCount('offers', 1);
    }

    // ---------- Datenschutz ----------

    public function test_fehlerantwort_enthaelt_keine_pii(): void
    {
        $k = $this->wpKombi(700, reif: false, pii: [
            'name' => 'GeheimVorname', 'lastname' => 'GeheimNachname',
            'email' => 'geheim@example.com', 'street' => 'GeheimStrasse 7',
        ]);

        $res = $this->actingAs($this->admin())->postJson(route('offer.save'), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2, 'service' => 'Standard',
            'employee_id' => 1, 'plan_id' => 1,
        ]);

        $res->assertStatus(422);
        $body = $res->getContent();
        foreach (['GeheimVorname', 'GeheimNachname', 'geheim@example.com', 'GeheimStrasse'] as $pii) {
            $this->assertStringNotContainsString($pii, $body, "PII '$pii' darf nicht in der Gate-Fehlerantwort stehen.");
        }
    }

    // ---------- Zusatz-Erstellwege (Evaluator-Befund BLOCKER-1 / MAJOR-2) ----------

    /**
     * Auto-Anlage über data() → syncOfferLeadProducts(): WP-Zeile mit Blocker darf KEIN Angebot bekommen,
     * reife WP-Zeile und Non-WP-Zeile werden weiterhin materialisiert.
     */
    public function test_auto_sync_ueberspringt_wp_blocker_erstellt_reif_und_nonwp(): void
    {
        $blockiert = $this->wpKombi(800, reif: false, status: 'offer');            // WP blockiert
        $reif = $this->wpKombi(810, reif: true, status: 'offer');                  // WP reif
        $nonwp = $this->wpKombi(820, reif: false, status: 'offer', productId: 3);  // Non-WP

        $this->actingAs($this->admin())->getJson(route('admin.offers.data'))->assertSuccessful();

        $this->assertDatabaseMissing('offers', ['customer_id' => $blockiert['customer'], 'product_id' => 2]);
        $this->assertDatabaseHas('offers', ['customer_id' => $reif['customer'], 'product_id' => 2]);
        $this->assertDatabaseHas('offers', ['customer_id' => $nonwp['customer'], 'product_id' => 3]);
    }

    /**
     * saveDocument() → processOffer() (else-Zweig, neues Angebot): WP mit Blocker → 422, kein Offer.
     */
    public function test_savedocument_blockiert_wp_kein_offer(): void
    {
        $k = $this->wpKombi(900, reif: false);

        $res = $this->actingAs($this->admin())->postJson(route('offers.save-document'), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2,
            'service' => 'Standard', 'sections' => [],
        ]);

        $res->assertStatus(422)->assertJsonPath('code', 'OFFER_NOT_READY');
        $this->assertDatabaseCount('offers', 0);
    }

    /**
     * MINOR-3 — Pfad 3 mit alternative_id=null: Gate löst die WP-Gewerkzeile des Kunden auf und
     * sperrt bei offenem Blocker (Verhalten neigt zum Gaten, nicht zum stillen Durchlassen).
     */
    public function test_pfad3_alternative_null_blockiert_ueber_kundenaufloesung(): void
    {
        $k = $this->wpKombi(950, reif: false);

        $res = $this->actingAs($this->admin())->postJson(route('offer.save'), [
            'customer_id' => $k['customer'], 'product_id' => 2, 'service' => 'Standard',
            'employee_id' => 1, 'plan_id' => 1,
        ]);

        $res->assertStatus(422)->assertJsonPath('code', 'OFFER_NOT_READY');
        $this->assertDatabaseCount('offers', 0);
    }

    /**
     * Nachtrag 2a — Gate-Platzierung (nur else/Neu-Create) blockiert KEINE Updates:
     * saveDocument() auf einen reifen WP-Vorgang mit BESTEHENDEM Angebot aktualisiert dieses weiter,
     * legt KEIN zweites Angebot an und wird vom Gate nicht abgewiesen.
     */
    public function test_savedocument_aktualisiert_bestehendes_wp_angebot_ohne_gate_block(): void
    {
        $k = $this->wpKombi(960, reif: true);

        // Bestehendes Angebot für exakt die Kombination (customer/alternative/product) anlegen.
        DB::table('offers')->insert([
            'id' => 9600,
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2,
            'offer_no' => 'SA-AN26999', 'service' => 'Alt', 'status' => 'todo',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $res = $this->actingAs($this->admin())->postJson(route('offers.save-document'), [
            'customer_id' => $k['customer'], 'alternative_id' => $k['alternative'], 'product_id' => 2,
            'service' => 'Aktualisiert-Nachtrag', 'status' => 'todo', 'sections' => [],
            'branding' => ['mode' => 'light', 'color' => '#ffffff', 'logo' => '', 'company' => 'Testfirma'],
            'cover_text' => 'Deckblatt', 'canvas_images' => [], 'biography' => [],
        ]);

        $res->assertSuccessful();
        // Kein neues Angebot (weiterhin genau eines), und das bestehende wurde aktualisiert.
        $this->assertDatabaseCount('offers', 1);
        $this->assertDatabaseHas('offers', ['id' => 9600, 'service' => 'Aktualisiert-Nachtrag']);
    }
}
