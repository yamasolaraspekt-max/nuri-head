<?php

namespace Tests\Feature\Catalog;

use Database\Seeders\FoxEssLongiCatalogSeeder;
use Database\Seeders\FoxEssLongiCatalogTeardownSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Fix 2 — der Teardown darf ausschließlich eigene Zeilen (imported_from='fox-longi-seed') löschen
 * und geteilte, mehrbesitzte Marken schonen. Regressions-Schutz gegen den früheren brand_id-Rückbau,
 * der fremde wberechnung-LONGi + die geteilte Marke gerissen hätte.
 */
class FoxEssLongiTeardownTest extends TestCase
{
    use RefreshDatabase;

    private function produkt(int $brandId, string $model, string $marker): int
    {
        return DB::table('products')->insertGetId([
            'brand_id' => $brandId, 'model' => $model, 'product' => $model,
            'imported_from' => $marker, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_teardown_marker_basiert_schont_fremde_marke_und_products(): void
    {
        $marker = FoxEssLongiCatalogSeeder::MARKER;
        $longiId = DB::table('brands')->insertGetId(['name' => 'LONGi', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);

        // 2 eigene (fox-longi-seed) + 1 FREMDES (wberechnung) an DERSELBEN Marke LONGi
        $eigen = $this->produkt($longiId, 'LR7-54HVH-495M', $marker);
        $this->produkt($longiId, 'LR7-60HTB-500M', $marker);
        $this->produkt($longiId, 'LR7-72HGD-610M', 'wberechnung');
        DB::table('product_pv_module_specs')->insert(['product_id' => $eigen, 'pmpp_wp' => 495, 'created_at' => now(), 'updated_at' => now()]);

        $this->seed(FoxEssLongiCatalogTeardownSeeder::class);

        $this->assertSame(0, DB::table('products')->where('imported_from', $marker)->count(), 'eigene weg');
        $this->assertSame(1, DB::table('products')->where('imported_from', 'wberechnung')->count(), 'fremdes bleibt');
        $this->assertTrue(DB::table('brands')->where('id', $longiId)->exists(), 'LONGi-Marke bleibt (mehrbesitzt)');
        $this->assertSame(0, DB::table('product_pv_module_specs')->count(), 'pv_spec via product_id mitgelöscht');
    }

    public function test_teardown_entfernt_marke_nur_wenn_keine_fremden_dranhaengen(): void
    {
        $marker = FoxEssLongiCatalogSeeder::MARKER;
        $foxId = DB::table('brands')->insertGetId(['name' => 'Fox ESS', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        $this->produkt($foxId, 'H3-10.0-Smart', $marker);

        $this->seed(FoxEssLongiCatalogTeardownSeeder::class);

        $this->assertFalse(DB::table('brands')->where('id', $foxId)->exists(), 'Fox-Marke weg (keine fremden dranhängend)');
    }
}
