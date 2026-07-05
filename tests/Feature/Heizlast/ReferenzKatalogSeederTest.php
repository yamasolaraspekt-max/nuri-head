<?php

namespace Tests\Feature\Heizlast;

use Database\Seeders\ReferenzKatalogSeeder;
use Database\Seeders\ReferenzKatalogTeardownSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * B2a-1 — Referenz-Kataloge + Klima (materials/konstruktionen/baualtersklassen/klima_plz).
 */
class ReferenzKatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_zeilen_soll(): void
    {
        $this->seed(ReferenzKatalogSeeder::class);

        $this->assertSame(23, DB::table('materials')->where('imported_from', 'wberechnung')->count());
        $this->assertSame(5, DB::table('konstruktionen')->where('imported_from', 'wberechnung')->count());
        $this->assertSame(25, DB::table('baualtersklassen')->where('imported_from', 'wberechnung')->count());
        $this->assertSame(8168, DB::table('klima_plz')->where('imported_from', 'wberechnung')->count());
    }

    public function test_schichten_material_id_remapped_gueltig(): void
    {
        $this->seed(ReferenzKatalogSeeder::class);
        $materialIds = DB::table('materials')->pluck('id')->all();

        foreach (DB::table('konstruktionen')->pluck('schichten') as $json) {
            foreach (json_decode($json, true) as $s) {
                if (($s['material_id'] ?? null) !== null) {
                    $this->assertContains($s['material_id'], $materialIds, 'Schicht-material_id → gültiges ticket-material');
                }
            }
        }
    }

    public function test_verifikations_status_je_zeile(): void
    {
        $this->seed(ReferenzKatalogSeeder::class);

        $this->assertSame(23, DB::table('materials')->where('verifikations_status', 'din_belegt')->count());
        $this->assertSame(5, DB::table('konstruktionen')->where('verifikations_status', 'din_belegt')->count());
        $this->assertSame(25, DB::table('baualtersklassen')->where('verifikations_status', 'tabula_richtwert')->count());
    }

    public function test_stichprobe_werte(): void
    {
        $this->seed(ReferenzKatalogSeeder::class);

        $vollziegel = DB::table('materials')->where('name', 'Vollziegel (Mauerziegel)')->first();
        $this->assertNotNull($vollziegel);
        $this->assertGreaterThan(0, (float) $vollziegel->lambda_w_mk);

        $dresden = DB::table('klima_plz')->where('plz', '01067')->first();
        $this->assertNotNull($dresden);
        $this->assertEqualsWithDelta(-8.5, (float) $dresden->nat_c, 0.01);
    }

    public function test_idempotenz(): void
    {
        $this->seed(ReferenzKatalogSeeder::class);
        $this->seed(ReferenzKatalogSeeder::class);

        $this->assertSame(23, DB::table('materials')->count());
        $this->assertSame(5, DB::table('konstruktionen')->count());
        $this->assertSame(8168, DB::table('klima_plz')->count());
    }

    public function test_rueckbau(): void
    {
        $this->seed(ReferenzKatalogSeeder::class);
        $this->seed(ReferenzKatalogTeardownSeeder::class);

        $this->assertSame(0, DB::table('materials')->count());
        $this->assertSame(0, DB::table('konstruktionen')->count());
        $this->assertSame(0, DB::table('baualtersklassen')->count());
        $this->assertSame(0, DB::table('klima_plz')->count());
    }
}
