<?php

namespace Tests\Unit\Offer;

use App\Services\Offer\CatalogPriceGuard;
use Tests\TestCase;

/**
 * P1-a — reine Guard-Logik (DB-frei, via applyWithCatalog).
 */
class CatalogPriceGuardTest extends TestCase
{
    private function guard(): CatalogPriceGuard
    {
        return new CatalogPriceGuard();
    }

    /** @param array<int,mixed> $items */
    private function sections(array $items): array
    {
        return [['title' => 'Abschnitt', 'items' => $items]];
    }

    public function test_component_mit_manipuliertem_payload_wird_auf_katalog_korrigiert(): void
    {
        $catalog = [5 => ['ek' => 100.0, 'vk' => 150.0]];
        $sections = $this->sections([
            ['kind' => 'article', 'component_id' => 5, 'qty' => 1, 'ek' => 1, 'price' => 1],
        ]);

        $node = $this->guard()->applyWithCatalog($sections, $catalog)[0]['items'][0];

        $this->assertSame(100.0, $node['ek']);
        $this->assertSame(150.0, $node['price']);
        $this->assertSame(100.0, $node['purchase_price']);
        $this->assertSame(150.0, $node['unit_price']);
        $this->assertSame(CatalogPriceGuard::SRC_CORRECTED, $node['preis_quelle']);
    }

    public function test_component_mit_passendem_preis_ist_verifiziert(): void
    {
        $catalog = [5 => ['ek' => 100.0, 'vk' => 150.0]];
        $sections = $this->sections([
            ['kind' => 'article', 'component_id' => 5, 'qty' => 1, 'ek' => 100, 'price' => 150],
        ]);

        $node = $this->guard()->applyWithCatalog($sections, $catalog)[0]['items'][0];

        $this->assertSame(CatalogPriceGuard::SRC_VERIFIED, $node['preis_quelle']);
        $this->assertSame(150.0, $node['price']);
    }

    public function test_component_ohne_katalog_treffer_wird_markiert_ohne_null_preis(): void
    {
        $sections = $this->sections([
            ['kind' => 'article', 'component_id' => 99, 'qty' => 1, 'ek' => 40, 'price' => 77],
        ]);

        $node = $this->guard()->applyWithCatalog($sections, [])[0]['items'][0];

        $this->assertSame(CatalogPriceGuard::SRC_MISSING, $node['preis_quelle']);
        $this->assertSame(77, $node['price']); // Preis bleibt erhalten — kein stiller 0
        $this->assertSame(40, $node['ek']);
    }

    public function test_position_ohne_anker_wird_manuell_markiert_preis_unveraendert(): void
    {
        $sections = $this->sections([
            ['kind' => 'article', 'qty' => 1, 'ek' => 10, 'price' => 20],
        ]);

        $node = $this->guard()->applyWithCatalog($sections, [])[0]['items'][0];

        $this->assertSame(CatalogPriceGuard::SRC_MANUAL, $node['preis_quelle']);
        $this->assertSame(20, $node['price']);
    }

    public function test_note_knoten_bekommt_keinen_marker(): void
    {
        $sections = $this->sections([
            ['kind' => 'note', 'text' => 'Hinweis'],
        ]);

        $node = $this->guard()->applyWithCatalog($sections, [])[0]['items'][0];

        $this->assertArrayNotHasKey('preis_quelle', $node);
    }

    public function test_verschachtelte_komponente_in_subitems_wird_korrigiert(): void
    {
        $catalog = [7 => ['ek' => 200.0, 'vk' => 300.0]];
        $sections = $this->sections([
            [
                'kind' => 'article', 'item_type' => 'master_set', 'master_set_id' => 3, 'qty' => 1,
                'subItems' => [
                    ['kind' => 'article', 'component_id' => 7, 'qty' => 2, 'ek' => 1, 'price' => 2],
                ],
            ],
        ]);

        $out = $this->guard()->applyWithCatalog($sections, $catalog);
        $container = $out[0]['items'][0];
        $child = $container['subItems'][0];

        $this->assertSame(300.0, $child['price']);
        $this->assertSame(200.0, $child['ek']);
        $this->assertSame(CatalogPriceGuard::SRC_CORRECTED, $child['preis_quelle']);
        // Container trägt Kinder → kein manuell-Marker
        $this->assertArrayNotHasKey('preis_quelle', $container);
    }

    public function test_b1_price_unit_value_wird_fuer_component_auf_1_normalisiert(): void
    {
        // Angriff: manipulierter Preiseinheits-Divisor. Formel = (qty/price_unit_value)*price.
        $catalog = [5 => ['ek' => 100.0, 'vk' => 150.0]];
        $sections = $this->sections([
            ['kind' => 'article', 'component_id' => 5, 'qty' => 1, 'ek' => 100, 'price' => 150,
             'price_unit_value' => 1000, 'cost_price_unit_value' => 500],
        ]);

        $node = $this->guard()->applyWithCatalog($sections, $catalog)[0]['items'][0];

        // Divisoren serverseitig neutralisiert → Formel nicht mehr manipulierbar.
        $this->assertSame(1.0, $node['price_unit_value']);
        $this->assertSame(1.0, $node['cost_price_unit_value']);
        $this->assertSame(150.0, $node['price']);
        $this->assertSame(100.0, $node['ek']);
        // Payload wich ab (Divisor ≠ 1) → korrigiert, NICHT fälschlich verifiziert.
        $this->assertSame(CatalogPriceGuard::SRC_CORRECTED, $node['preis_quelle']);
    }

    public function test_b1_verifiziert_nur_bei_vollstaendig_serverseitiger_formel(): void
    {
        // Preise passen, kein Divisor gesetzt → Divisor-Default 1 → verifiziert; Divisor wird trotzdem explizit gesetzt.
        $catalog = [5 => ['ek' => 100.0, 'vk' => 150.0]];
        $sections = $this->sections([
            ['kind' => 'article', 'component_id' => 5, 'qty' => 1, 'ek' => 100, 'price' => 150],
        ]);

        $node = $this->guard()->applyWithCatalog($sections, $catalog)[0]['items'][0];

        $this->assertSame(CatalogPriceGuard::SRC_VERIFIED, $node['preis_quelle']);
        $this->assertSame(1.0, $node['price_unit_value']);
        $this->assertSame(1.0, $node['cost_price_unit_value']);
    }

    public function test_b1_cost_price_unit_value_wird_normalisiert(): void
    {
        // EK-Formel = (qty/cost_price_unit_value)*ek → Divisor muss auch für EK kontrolliert sein.
        $catalog = [9 => ['ek' => 80.0, 'vk' => 120.0]];
        $sections = $this->sections([
            ['kind' => 'article', 'component_id' => 9, 'qty' => 1, 'ek' => 80, 'price' => 120,
             'cost_price_unit_value' => 250],
        ]);

        $node = $this->guard()->applyWithCatalog($sections, $catalog)[0]['items'][0];

        $this->assertSame(1.0, $node['cost_price_unit_value']);
        $this->assertSame(CatalogPriceGuard::SRC_CORRECTED, $node['preis_quelle']);
    }

    public function test_flag_logik_liegt_im_controller_guard_selbst_ist_immer_aktiv(): void
    {
        // Dokumentiert: der Guard kennt kein Flag; die Abschaltbarkeit sitzt im
        // Controller (config('offer.enforce_catalog_pricing')). Hier nur Beleg,
        // dass applyWithCatalog ohne Katalog nichts erzwingt.
        $sections = $this->sections([
            ['kind' => 'article', 'component_id' => 5, 'qty' => 1, 'ek' => 1, 'price' => 1],
        ]);

        $node = $this->guard()->applyWithCatalog($sections, [])[0]['items'][0];

        // component_id gesetzt, aber leerer Katalog → katalog_fehlt (nicht korrigiert)
        $this->assertSame(CatalogPriceGuard::SRC_MISSING, $node['preis_quelle']);
        $this->assertSame(1, $node['price']);
    }
}
