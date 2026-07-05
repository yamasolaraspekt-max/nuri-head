<?php

namespace Tests\Feature\Catalog;

use Database\Seeders\WberechnungImportSeeder;
use Database\Seeders\WberechnungImportTeardownSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Cut-over Stufe 2 — wberechnung-Katalog-Import (19 WP + 5 PV) gegen ticket_testing.
 * Prüft Zeilen-Soll, Datenblatt-Ankunft (Buderus-Fix, NIBE-Varianten), Semantik, Einheiten,
 * Dedup-Skip, Idempotenz und reversiblen Rückbau via Marker.
 */
class WberechnungImportTest extends TestCase
{
    use RefreshDatabase;

    private function hpVal(string $model, string $col)
    {
        return DB::table('product_heat_pump_specs')
            ->join('products', 'products.id', '=', 'product_heat_pump_specs.product_id')
            ->where('products.model', $model)->value($col);
    }

    private function markerProducts()
    {
        return DB::table('products')->where('imported_from', WberechnungImportSeeder::MARKER);
    }

    /** Legt ein Bestands-Produkt an (Felder wie der etablierte Katalog-Seeder), imported_from=null. */
    private function bestand(int $brandId, string $model): void
    {
        $ag = DB::table('article_groups')->value('id')
            ?? DB::table('article_groups')->insertGetId(['article_group' => 'Bestand', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('products')->insert([
            'brand_id' => $brandId, 'article_group' => (string) $ag, 'product' => 'Bestand '.$model,
            'model' => $model, 'short_description' => 'Bestand', 'imported_from' => null,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function brandId(string $name): int
    {
        $status = DB::table('brands')->whereNotNull('status')->value('status') ?? 'active';

        return DB::table('brands')->where('name', $name)->value('id')
            ?? DB::table('brands')->insertGetId(['name' => $name, 'status' => $status, 'created_at' => now(), 'updated_at' => now()]);
    }

    public function test_zeilen_soll_19_wp_5_pv(): void
    {
        $this->seed(WberechnungImportSeeder::class);

        $this->assertSame(24, $this->markerProducts()->count(), '24 products (19 WP + 5 PV)');
        $hp = DB::table('product_heat_pump_specs')->join('products', 'products.id', '=', 'product_heat_pump_specs.product_id')
            ->where('products.imported_from', WberechnungImportSeeder::MARKER)->count();
        $pv = DB::table('product_pv_module_specs')->join('products', 'products.id', '=', 'product_pv_module_specs.product_id')
            ->where('products.imported_from', WberechnungImportSeeder::MARKER)->count();
        $this->assertSame(19, $hp, '19 heat-pump-specs');
        $this->assertSame(5, $pv, '5 pv-module-specs');
    }

    public function test_buderus_wlw7_a_minus7_cop_angekommen(): void
    {
        $this->seed(WberechnungImportSeeder::class);
        // der WP-Fix: A-7/W35-COP war leer -> jetzt datenblatt-korrekt 2,36
        $this->assertEqualsWithDelta(2.36, (float) $this->hpVal('WLW-7 MB AR', 'cop_am7_w35'), 0.001);
        $this->assertEqualsWithDelta(6.71, (float) $this->hpVal('WLW-7 MB AR', 'heizleistung_am7_w35_kw'), 0.001);
    }

    public function test_nibe_14_16_echte_varianten(): void
    {
        $this->seed(WberechnungImportSeeder::class);
        $p14 = (float) $this->hpVal('S2125-14', 'heizleistung_am7_w35_kw');
        $p16 = (float) $this->hpVal('S2125-16', 'heizleistung_am7_w35_kw');
        $this->assertEqualsWithDelta(9.48, $p14, 0.001);
        $this->assertEqualsWithDelta(10.31, $p16, 0.001);
        $this->assertNotEquals($p14, $p16, 'echte Varianten, kein Duplikat');
    }

    public function test_kurve_semantik_und_leistungskurve_null(): void
    {
        $this->seed(WberechnungImportSeeder::class);
        $rows = DB::table('product_heat_pump_specs')->join('products', 'products.id', '=', 'product_heat_pump_specs.product_id')
            ->where('products.imported_from', WberechnungImportSeeder::MARKER)->get(['kurve_semantik', 'leistungskurve']);
        $this->assertCount(19, $rows);
        foreach ($rows as $r) {
            $this->assertSame('en14511_nenn', $r->kurve_semantik);
            $this->assertNull($r->leistungskurve);
        }
    }

    public function test_einheiten_kw_plausibel(): void
    {
        $this->seed(WberechnungImportSeeder::class);
        // Heizleistungen in kW (Stufe-i-Kommentar), nicht W: plausibel 1..50
        $max = (float) DB::table('product_heat_pump_specs')->max('heizleistung_am7_w35_kw');
        $this->assertGreaterThan(1, $max);
        $this->assertLessThan(50, $max, 'kW, nicht W');
    }

    public function test_dedup_skip_vorhandenes_modell(): void
    {
        $longi = $this->brandId('LONGi');
        $this->bestand($longi, 'LR7-72HGD-610M'); // Modell, das der Import auch bringt

        $this->seed(WberechnungImportSeeder::class);

        $rows = DB::table('products')->where('brand_id', $longi)->where('model', 'LR7-72HGD-610M')->get();
        $this->assertCount(1, $rows, 'nicht dupliziert');
        $this->assertNull($rows->first()->imported_from, 'Bestand nicht überschrieben');
        $this->assertSame(23, $this->markerProducts()->count(), '24 - 1 geskippt = 23 importiert');
    }

    public function test_idempotenz_zweiter_lauf_keine_duplikate(): void
    {
        $this->seed(WberechnungImportSeeder::class);
        $this->seed(WberechnungImportSeeder::class);
        $this->assertSame(24, $this->markerProducts()->count());
        $this->assertSame(19, DB::table('product_heat_pump_specs')->count());
        $this->assertSame(5, DB::table('product_pv_module_specs')->count());
    }

    public function test_rueckbau_via_marker_bestand_unberuehrt(): void
    {
        $this->bestand($this->brandId('Bestandsmarke'), 'BESTAND-X');
        $this->seed(WberechnungImportSeeder::class);
        $this->assertSame(24, $this->markerProducts()->count());

        $this->seed(WberechnungImportTeardownSeeder::class);

        $this->assertSame(0, $this->markerProducts()->count(), 'Import zurückgebaut');
        $this->assertSame(0, DB::table('product_heat_pump_specs')->count());
        $this->assertSame(0, DB::table('product_pv_module_specs')->count());
        $this->assertSame(1, DB::table('products')->where('model', 'BESTAND-X')->count(), 'Bestand unberührt');
    }
}
