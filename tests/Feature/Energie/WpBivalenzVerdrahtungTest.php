<?php

namespace Tests\Feature\Energie;

use App\Http\Controllers\Energie\EnergieAuslegungController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Tests\TestCase;

/**
 * Stufe 3b — belegt, dass wpBerechnen die vorhandene WP-Auslegungskette (Bivalenz-Orchestrator)
 * REAL erreicht (Reuse, keine Parallelrechnung) und das Operanden-Gate greift:
 *  - mit plz ⇒ auslegungskette anwendbar (Kette gerechnet), Bivalenz-Struktur je Kandidat;
 *  - ohne plz ⇒ Kette liefert Operanden-Gate (plz_fehlt), KEIN erfundener Wert.
 * Ergänzt (bricht nicht) den Charakterisierungs-Golden-Master.
 */
class WpBivalenzVerdrahtungTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        DB::table('brands')->insert(['id' => 1, 'name' => 'TestMarke', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('products')->insert([
            ['id' => 1, 'brand_id' => 1, 'model' => 'TM-8', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'brand_id' => 1, 'model' => 'TM-12', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('product_heat_pump_specs')->insert([
            ['id' => 1, 'product_id' => 1, 'heizleistung_a7_w35_kw' => 8.0, 'heizleistung_am7_w35_kw' => 6.0, 'scop_35' => 4.5, 'scop_55' => 3.2, 'max_vorlauf_c' => 60.0, 'modulation_min_kw' => 2.0, 'modulation_max_kw' => 10.0, 'geraetetyp' => 'luft_wasser', 'serie' => 'TM', 'kaeltemittel' => 'R290', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'product_id' => 2, 'heizleistung_a7_w35_kw' => 12.0, 'heizleistung_am7_w35_kw' => 9.0, 'scop_35' => 5.0, 'scop_55' => 3.6, 'max_vorlauf_c' => 65.0, 'modulation_min_kw' => 3.0, 'modulation_max_kw' => 14.0, 'geraetetyp' => 'luft_wasser', 'serie' => 'TM', 'kaeltemittel' => 'R290', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /** @return array<string,mixed> */
    private function normalfall(array $over = []): array
    {
        return array_merge([
            'wp_index' => 0, 'heizlast_kw' => 8.0, 'heizsystem' => 'heizkoerper', 'wp_typ' => 'luft_wasser',
            'personen_im_haushalt' => 4, 'ww_mit_wp' => true, 'badewanne_vorhanden' => false, 'investition' => 25000,
            'heizungsart' => 'gas', 'heizung_alter' => 25, 'anzahl_we' => 1, 'selbst_bewohnte_we' => 1,
            'effizienzbonus' => false, 'einkommensbonus' => false, 'strompreis' => 0.30,
        ], $over);
    }

    /** @return array<string,mixed> */
    private function ergebnis(array $form): array
    {
        $request = Request::create(route('energie.wp-auslegung.berechnen'), 'POST', $form);
        $view = (new EnergieAuslegungController)->wpBerechnen($request);
        $this->assertInstanceOf(View::class, $view);

        return $view->getData()['ergebnis'];
    }

    public function test_mit_plz_erreicht_die_auslegungskette_und_liefert_bivalenz(): void
    {
        $e = $this->ergebnis($this->normalfall(['plz' => '20095']));

        $this->assertArrayHasKey('auslegungskette', $e, 'wpErgebnis muss die Auslegungskette mergen (Reuse).');
        $ak = $e['auslegungskette'];
        $this->assertTrue($ak['anwendbar'], 'Mit vollständigen Operanden (inkl. plz) ist die Kette anwendbar.');
        $this->assertFalse($ak['verbindlich'], 'Stufe 3a bleibt informativ, nicht verbindlich.');

        // Reuse-Beleg: die Kandidaten tragen die Bivalenz-Struktur des Orchestrators.
        if ($ak['kandidaten'] !== []) {
            $b = $ak['kandidaten'][0]['bivalenz'];
            foreach (['bivalenzpunkt_c', 'deckung_ne_pct', 'jaz', 'estab_waerme_anteil_pct', 'laufstunden_h'] as $feld) {
                $this->assertArrayHasKey($feld, $b, "Kandidat muss Bivalenz-Feld $feld tragen.");
            }
        }
    }

    public function test_ohne_plz_greift_das_operanden_gate_statt_erfundener_werte(): void
    {
        $e = $this->ergebnis($this->normalfall()); // kein plz

        $this->assertArrayHasKey('auslegungskette', $e);
        $ak = $e['auslegungskette'];
        $this->assertFalse($ak['anwendbar'], 'Ohne plz darf die Kette NICHT rechnen (Operanden-Gate).');
        $this->assertContains('plz_fehlt', $ak['gates_offen'] ?? [], 'Der fehlende plz-Operand muss als Gate erscheinen.');
    }
}
