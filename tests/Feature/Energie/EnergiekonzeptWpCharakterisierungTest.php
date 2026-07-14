<?php

namespace Tests\Feature\Energie;

use App\Http\Controllers\Energie\EnergiekonzeptController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * P1a (Stufe 3b) — CHARAKTERISIERUNG des HEUTIGEN WP-Teils im Energiekonzept
 * (EnergiekonzeptController::baueWp, aufgerufen über berechnen → baueKonzept). Dieser WP-Teil ist ein
 * Teil-DUPLIKAT der Rechenkette aus EnergieAuslegungController::wpErgebnis. P1a schreibt fest, dass er
 * bei gleichen Eingaben dieselben tragenden Kernwerte liefert — Grundlage dafür, dass P1b beide auf
 * denselben extrahierten Dienst (WpCosting/WpFunding) umstellen kann. KEINE Produktivänderung.
 */
class EnergiekonzeptWpCharakterisierungTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        $this->seedWaermepumpe();
    }

    private function seedWaermepumpe(): void
    {
        DB::table('brands')->insert(['id' => 1, 'name' => 'TestMarke', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('products')->insert(['id' => 1, 'brand_id' => 1, 'model' => 'TM-8', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('product_heat_pump_specs')->insert([
            'id' => 1, 'product_id' => 1,
            'heizleistung_a7_w35_kw' => 8.0, 'heizleistung_am7_w35_kw' => 6.0,
            'scop_35' => 4.5, 'scop_55' => 3.2, 'max_vorlauf_c' => 60.0,
            'modulation_min_kw' => 2.0, 'modulation_max_kw' => 10.0,
            'geraetetyp' => 'luft_wasser', 'serie' => 'TM', 'kaeltemittel' => 'R290',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** @return array<string,mixed> konzept['wp'] über den echten Controller-Pfad. */
    private function konzeptWp(): array
    {
        $form = [
            'wp_aktiv' => 1,
            'sanierung_aktiv' => 0,
            'pv_aktiv' => 0,
            'wp' => [
                'wp_index' => 0, 'heizlast_kw' => 8.0, 'heizsystem' => 'heizkoerper', 'wp_typ' => 'luft_wasser',
                'personen_im_haushalt' => 4, 'ww_mit_wp' => 1, 'badewanne_vorhanden' => 0, 'investition' => 25000,
                'heizungsart' => 'gas', 'heizung_alter' => 25, 'anzahl_we' => 1, 'selbst_bewohnte_we' => 1,
                'effizienzbonus' => 0, 'einkommensbonus' => 0, 'strompreis' => 0.30,
            ],
        ];
        $request = Request::create(route('energie.energiekonzept.berechnen'), 'POST', $form);
        $view = (new EnergiekonzeptController)->berechnen($request);
        $this->assertInstanceOf(View::class, $view);

        return $view->getData()['konzept']['wp'];
    }

    public function test_wp_teil_gleiche_kernwerte_wie_wp_auslegung(): void
    {
        $wp = $this->konzeptWp();

        // Dieselben Golden-Werte wie EnergieAuslegungController (Duplikat der Rechenkette).
        $this->assertSame('TestMarke', $wp['hersteller']);
        $this->assertSame('TM-8', $wp['modell']);
        $this->assertSame(2.9, $wp['jaz']);
        $this->assertSame(6483.0, $wp['strom_kwh']);
        $this->assertSame(1945.0, $wp['stromkosten_jahr']);
        $this->assertSame(25000.0, $wp['investition_netto']);
        $this->assertSame(12500.0, $wp['foerderung']['zuschuss']);
        $this->assertSame(50.0, $wp['foerderung']['effektiver_satz_pct']);
        $this->assertSame(12500.0, $wp['foerderung']['netto_investition']);
    }

    public function test_wp_index_ohne_geraet_liefert_null_wp_teil(): void
    {
        // baueWp gibt null zurück, wenn die gewählte WP nicht existiert (kein WP-Block, kein Crash).
        $form = [
            'wp_aktiv' => 1, 'sanierung_aktiv' => 0, 'pv_aktiv' => 0,
            'wp' => [
                'wp_index' => 9, 'heizlast_kw' => 8.0, 'heizsystem' => 'heizkoerper', 'wp_typ' => 'luft_wasser',
                'personen_im_haushalt' => 4, 'investition' => 25000,
            ],
        ];
        $request = Request::create(route('energie.energiekonzept.berechnen'), 'POST', $form);
        $konzept = (new EnergiekonzeptController)->berechnen($request)->getData()['konzept'];

        $this->assertNull($konzept['wp']);
    }

    /** Matrix 5.8: nur WP aktiv → sanierung/pv bleiben null; Gesamt-Summen spiegeln ausschließlich den WP-Teil. */
    public function test_nur_wp_aktiv_sanierung_und_pv_null_gesamt_spiegelt_wp(): void
    {
        $form = [
            'wp_aktiv' => 1, 'sanierung_aktiv' => 0, 'pv_aktiv' => 0,
            'wp' => [
                'wp_index' => 0, 'heizlast_kw' => 8.0, 'heizsystem' => 'heizkoerper', 'wp_typ' => 'luft_wasser',
                'personen_im_haushalt' => 4, 'ww_mit_wp' => 1, 'investition' => 25000,
                'heizungsart' => 'gas', 'heizung_alter' => 25, 'anzahl_we' => 1, 'selbst_bewohnte_we' => 1, 'strompreis' => 0.30,
            ],
        ];
        $request = Request::create(route('energie.energiekonzept.berechnen'), 'POST', $form);
        $konzept = (new EnergiekonzeptController)->berechnen($request)->getData()['konzept'];

        $this->assertNotNull($konzept['wp']);
        $this->assertNull($konzept['sanierung']);
        $this->assertNull($konzept['pv']);
        // Gesamt = nur WP: Investition 25000, Förderung 12500, Eigenanteil 12500, Einsparung 0.
        $this->assertSame(25000.0, (float) $konzept['gesamt']['investition_eur']);
        $this->assertSame(12500.0, (float) $konzept['gesamt']['foerderung_eur']);
        $this->assertSame(12500.0, (float) $konzept['gesamt']['eigenanteil_eur']);
        $this->assertSame(0.0, (float) $konzept['gesamt']['einsparung_eur_a']);
    }
}
