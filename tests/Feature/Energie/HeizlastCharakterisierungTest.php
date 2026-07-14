<?php

namespace Tests\Feature\Energie;

use App\Http\Controllers\Energie\HeizlastController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * P1a (Stufe 3b) — CHARAKTERISIERUNG des HEUTIGEN HeizlastController (Ist-Heizlast-Rechner + optionaler
 * WP-Match-Vorschlag). Schreibt das aktuelle Verhalten fest: transientes Projekt (create→delete), keine
 * Persistenz; Ausgabe = Heizlast-Ergebnis + WP-Match-Block (hartkodiert luft_wasser/heizkoerper).
 * KEINE Produktivänderung. HeizlastController bleibt laut Entscheidung schlank (kein Orchestrator).
 * Die detaillierte Heizlast-Numerik ist zusätzlich durch die committeten HeizlastRechner-Referenztests gedeckt.
 */
class HeizlastCharakterisierungTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        DB::table('brands')->insert(['id' => 1, 'name' => 'TestMarke', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('products')->insert(['id' => 1, 'brand_id' => 1, 'model' => 'TM-8', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('product_heat_pump_specs')->insert(['id' => 1, 'product_id' => 1, 'heizleistung_a7_w35_kw' => 8.0, 'scop_35' => 4.5, 'scop_55' => 3.2, 'max_vorlauf_c' => 60.0, 'geraetetyp' => 'luft_wasser', 'serie' => 'TM', 'kaeltemittel' => 'R290', 'created_at' => now(), 'updated_at' => now()]);
    }

    /** @return array<string,mixed> Standard-Gebäude (ohne PLZ → norm_aussentemp Fallback -8.5). */
    private function gebaeude(array $over = []): array
    {
        return array_merge([
            'projekt' => ['standort_plz' => '', 'baujahr' => 1990, 'ziel_vorlauf_c' => 55, 'spreizung_k' => 7],
            'raum' => ['grundflaeche_m2' => 100, 'hoehe_m' => 2.5, 'theta_int_c' => 20, 'luftwechsel_1h' => 0.5],
            'bauteile' => [
                ['typ' => 'wand', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 120, 'u_wert' => 0.28],
                ['typ' => 'dach', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 100, 'u_wert' => 0.20],
                ['typ' => 'boden', 'grenzflaeche' => 'erdreich', 'flaeche_m2' => 100, 'u_wert' => 0.30],
                ['typ' => 'fenster', 'grenzflaeche' => 'aussen', 'flaeche_m2' => 20, 'u_wert' => 1.1],
            ],
        ], $over);
    }

    private function berechnen(array $form): View
    {
        $req = Request::create(route('energie.heizlast.berechnen'), 'POST', $form);
        $view = app(HeizlastController::class)->berechnen($req);
        $this->assertInstanceOf(View::class, $view);

        return $view;
    }

    public function test_heizlast_ergebnis_und_wp_matchblock(): void
    {
        $data = $this->berechnen($this->gebaeude())->getData();

        $this->assertNull($data['fehler']);
        $g = $data['ergebnis']['gebaeude'];
        // Deterministische Ist-Heizlast (norm_aussentemp -8.5 Fallback, saniertes Gebäude).
        $this->assertSame(4.35, $g['auslegungsheizlast_kw']);
        $this->assertSame(4.35, $g['standardheizlast_kw']);
        $this->assertSame(100.0, $g['grundflaeche_m2']);
        $this->assertSame(43.5, $g['spezifische_heizlast_w_m2']);

        // WP-Match-Block: heutiges Ist = hartkodierter Vorschlag über WaermepumpenMatchService.
        $this->assertIsArray($data['wp']);
        $this->assertArrayHasKey('geraete', $data['wp']);
        $this->assertArrayHasKey('design_punkt', $data['wp']);
        $this->assertArrayHasKey('verbindlich', $data['wp']);
    }

    public function test_transient_kein_projekt_persistiert(): void
    {
        $vorher = DB::table('heizlast_projekte')->count();
        $this->berechnen($this->gebaeude());
        // Rechner-Tool: Projekt wird nach der Rechnung per Cascade wieder verworfen.
        $this->assertSame($vorher, DB::table('heizlast_projekte')->count());
    }

    public function test_fehlende_pflichtfelder_werfen_validierungsfehler(): void
    {
        $form = $this->gebaeude();
        unset($form['raum']['grundflaeche_m2']); // Pflichtfeld

        $this->expectException(ValidationException::class);
        $this->berechnen($form);
    }
}
