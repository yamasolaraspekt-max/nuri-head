<?php

namespace Tests\Feature\Offer;

use App\Models\OfferDetail;
use App\Models\User;
use App\Services\Offer\CatalogPriceGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * P1-a — DB-Fetch des Guards + Speicherpfad (POST /offers/save-document).
 */
class OfferCatalogPricingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Speicherpfad feuert Broadcast-Events — im Test kein Pusher/WebSocket.
        config(['broadcasting.default' => 'null']);
    }

    /** Komponente ohne Parent-Graph anlegen (FK-Checks aus). */
    private function seedComponent(int $id, float $ek, float $vk): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('master_set_components')->insert([
            'id' => $id,
            'master_set_id' => 1,
            'product_id' => 1,
            'purchase_price' => $ek,
            'unit_price' => $vk,
            'qty' => 1,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Schema::enableForeignKeyConstraints();
    }

    public function test_apply_zieht_katalogpreis_aus_db_und_verwirft_payload(): void
    {
        $this->seedComponent(5, 100.00, 150.00);

        $sections = [[
            'title' => 'A',
            'items' => [
                ['kind' => 'article', 'component_id' => 5, 'qty' => 1, 'ek' => 1, 'price' => 1],
            ],
        ]];

        $out = app(CatalogPriceGuard::class)->apply($sections);
        $node = $out[0]['items'][0];

        $this->assertSame(100.0, $node['ek']);
        $this->assertSame(150.0, $node['price']);
        $this->assertSame(CatalogPriceGuard::SRC_CORRECTED, $node['preis_quelle']);
    }

    public function test_apply_markiert_freitextposition_als_manuell(): void
    {
        $sections = [[
            'title' => 'A',
            'items' => [
                ['kind' => 'article', 'qty' => 1, 'ek' => 10, 'price' => 20],
            ],
        ]];

        $node = app(CatalogPriceGuard::class)->apply($sections)[0]['items'][0];

        $this->assertSame(CatalogPriceGuard::SRC_MANUAL, $node['preis_quelle']);
    }

    public function test_save_document_persistiert_katalogpreis_nicht_manipulierten_payload(): void
    {
        $this->seedComponent(5, 100.00, 150.00);
        // Fokus dieses Tests = Preis-Effekt im Speicherpfad, nicht die FK-Kette der
        // offers-God-Tables. FK-Prüfung im Testlauf aus (nach seedComponent gesetzt).
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $user = User::factory()->create(['password' => 'password', 'name' => 'offertest', 'is_admin' => true]);

        $payload = [
            'customer_id' => 1,
            'alternative_id' => 1,
            'product_id' => 1,
            'tax_rate' => 19,
            'branding' => ['mode' => 'standard', 'color' => '#93c21c', 'company' => 'Testfirma', 'logo' => ''],
            'sections' => [[
                'title' => 'A',
                'items' => [
                    // Manipulierter Browser-Preis (1/1) — muss durch Katalog (100/150) ersetzt werden.
                    ['kind' => 'article', 'component_id' => 5, 'qty' => 1, 'ek' => 1, 'price' => 1],
                ],
            ]],
        ];

        $res = $this->actingAs($user)->postJson(route('offers.save-document'), $payload);
        $res->assertOk();

        $detail = OfferDetail::latest('id')->first();
        $this->assertNotNull($detail, 'OfferDetail wurde nicht gespeichert.');

        // Server-autoritative Summe = Katalog-VK (150), nicht der Payload (1).
        $this->assertEqualsWithDelta(150.0, (float) $detail->total_net, 0.01);

        $node = $detail->sections[0]['items'][0];
        $this->assertSame(CatalogPriceGuard::SRC_CORRECTED, $node['preis_quelle']);
        $this->assertEqualsWithDelta(150.0, (float) $node['price'], 0.01);
    }

    public function test_b1_price_unit_value_manipulation_wird_neutralisiert(): void
    {
        $this->seedComponent(5, 100.00, 150.00);
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $user = User::factory()->create(['password' => 'password', 'name' => 'offertest', 'is_admin' => true]);

        $payload = [
            'customer_id' => 1,
            'alternative_id' => 1,
            'product_id' => 1,
            'tax_rate' => 19,
            'branding' => ['mode' => 'standard', 'color' => '#93c21c', 'company' => 'Testfirma', 'logo' => ''],
            'sections' => [[
                'title' => 'A',
                'items' => [
                    // Angriff: Preiseinheit-Divisor 1000 → ohne B1-Schutz würde total_net = (1/1000)*150 = 0,15.
                    ['kind' => 'article', 'component_id' => 5, 'qty' => 1, 'ek' => 100, 'price' => 150, 'price_unit_value' => 1000],
                ],
            ]],
        ];

        $res = $this->actingAs($user)->postJson(route('offers.save-document'), $payload);
        $res->assertOk();

        $detail = OfferDetail::latest('id')->first();
        // B1 geschlossen: Divisor neutralisiert → Summe bleibt Katalog-VK (150), NICHT 0,15.
        $this->assertEqualsWithDelta(150.0, (float) $detail->total_net, 0.01);

        $node = $detail->sections[0]['items'][0];
        $this->assertEqualsWithDelta(1.0, (float) $node['price_unit_value'], 0.001);
        $this->assertSame(CatalogPriceGuard::SRC_CORRECTED, $node['preis_quelle']);
    }

    public function test_flag_aus_laesst_payload_preis_unveraendert(): void
    {
        config(['offer.enforce_catalog_pricing' => false]);
        $this->seedComponent(5, 100.00, 150.00);
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $user = User::factory()->create(['password' => 'password', 'name' => 'offertest', 'is_admin' => true]);

        $payload = [
            'customer_id' => 1,
            'alternative_id' => 1,
            'product_id' => 1,
            'tax_rate' => 19,
            'branding' => ['mode' => 'standard', 'color' => '#93c21c', 'company' => 'Testfirma', 'logo' => ''],
            'sections' => [[
                'title' => 'A',
                'items' => [
                    ['kind' => 'article', 'component_id' => 5, 'qty' => 1, 'ek' => 1, 'price' => 1],
                ],
            ]],
        ];

        $res = $this->actingAs($user)->postJson(route('offers.save-document'), $payload);
        $res->assertOk();

        $detail = OfferDetail::latest('id')->first();
        // Flag aus → alter Pfad: Payload-Preis (1) führt, kein Marker.
        $this->assertEqualsWithDelta(1.0, (float) $detail->total_net, 0.01);
        $node = $detail->sections[0]['items'][0];
        $this->assertArrayNotHasKey('preis_quelle', $node);
    }
}
