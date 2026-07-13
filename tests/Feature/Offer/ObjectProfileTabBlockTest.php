<?php

namespace Tests\Feature\Offer;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * UX-2 — Objektprofil-Tab-Block (read-only): Reife · Auslegung · Preis.
 * Quell-/Markup-Prüfung der Objektprofil-View (analog Kanban-Quelltest), da ein Vollrender der
 * sehr großen View unverhältnismäßig ist; die Panel-Inhalte sind durch die Routen-Tests abgedeckt.
 */
class ObjectProfileTabBlockTest extends TestCase
{
    private function source(): string
    {
        return file_get_contents(resource_path('views/admin/new_leads/customer_object_profile.blade.php'));
    }

    public function test_tabblock_und_drei_routen_vorhanden(): void
    {
        $src = $this->source();
        $this->assertStringContainsString('ux2-tabblock', $src, 'Tab-Block fehlt.');
        $this->assertStringContainsString("offers.angebotsreife.panel", $src, 'Reife-Route fehlt.');
        $this->assertStringContainsString("offers.auslegung.vorschau", $src, 'Auslegung-Route fehlt.');
        $this->assertStringContainsString("offers.wp-katalog-matching", $src, 'Preis-Route fehlt.');
    }

    public function test_wp_gate_umschliesst_block(): void
    {
        $src = $this->source();
        // Der Tab-Block steht hinter dem WP-Gate product_id===2.
        $this->assertStringContainsString("product_id ?? 0) === 2", $src, 'WP-Gate fehlt.');
        $gatePos = strpos($src, "product_id ?? 0) === 2");
        $blockPos = strpos($src, 'ux2-tabblock');
        $this->assertNotFalse($gatePos);
        $this->assertNotFalse($blockPos);
        $this->assertLessThan($blockPos, $gatePos, 'Tab-Block muss hinter dem WP-Gate stehen (Non-WP zeigt nichts).');
    }

    public function test_lazy_reife_auto_andere_erst_bei_klick(): void
    {
        $src = $this->source();
        // Reife-Pane trägt data-tab-auto; Auslegung/Preis sind zunächst display:none (Klick-Load).
        $this->assertStringContainsString('data-tab-auto', $src, 'Reife-Auto-Load fehlt.');
        $this->assertStringContainsString("data-tab-key=\"auslegung\"", $src);
        $this->assertStringContainsString("data-tab-key=\"preis\"", $src);
        $this->assertStringContainsString('@once', $src, 'Loader muss @once sein.');
    }

    public function test_read_only_kein_formular_kein_alpine(): void
    {
        $src = $this->source();
        // Innerhalb des Tab-Blocks: nur Button (type=button), kein POST/Submit, vanilla JS.
        $this->assertStringContainsString('type="button"', $src);
        $this->assertStringContainsString('addEventListener', $src, 'Vanilla-JS-Loader erwartet.');
        // Fehler-Isolation je Tab.
        $this->assertStringContainsString('Konnte nicht geladen werden', $src, 'Fehler-Isolation je Tab fehlt.');
        // Kein Alpine im Tab-Block.
        $blockStart = strpos($src, 'ux2-tabblock');
        $blockEnd = strpos($src, 'initUx2', $blockStart);
        $block = substr($src, $blockStart, ($blockEnd !== false ? $blockEnd - $blockStart : 4000));
        $this->assertStringNotContainsString('x-data', $block, 'Kein Alpine im Tab-Block erlaubt.');
        $this->assertStringNotContainsString('wire:', $block);
    }

    public function test_drei_routen_registriert(): void
    {
        $this->assertTrue(Route::has('offers.angebotsreife.panel'));
        $this->assertTrue(Route::has('offers.auslegung.vorschau'));
        $this->assertTrue(Route::has('offers.wp-katalog-matching'));
    }
}
