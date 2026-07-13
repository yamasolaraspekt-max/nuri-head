<?php

namespace Tests\Feature;

use Database\Seeders\WaermepumpeKomplettloesungSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * id=16-Fix — der Seeder bindet die WP-Komplettlösung an die per Name aufgelöste WP-Artikelgruppe
 * (article_group='Wärmepumpe', hier id=2), NICHT an die hartkodierte id=16 (= „Tapete").
 * Läuft ausschließlich gegen die isolierte Test-DB (RefreshDatabase), nie gegen die Dev-DB.
 */
class WaermepumpeKomplettloesungSeederTest extends TestCase
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
            ['id' => 16, 'article_group' => 'Tapete', 'initial' => 'TA', 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('phase_sections')->insert(['id' => 1, 'created_at' => now(), 'updated_at' => now()]);
        // Vom Seeder gemappte Stages (st_01..st_10 → diese ids).
        foreach ([158, 1, 2, 159, 3, 4, 5, 6] as $sid) {
            DB::table('stages')->insert(['id' => $sid, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function test_seeder_bindet_wp_phasen_an_product_id_2(): void
    {
        $this->seed(WaermepumpeKomplettloesungSeeder::class);

        // WP-Phasen/Aktivitäten liegen unter der WP-Gruppe (id=2), nicht unter Tapete (16).
        $this->assertGreaterThan(0, DB::table('task_phases')->where('product_id', 2)->count());
        $this->assertGreaterThan(0, DB::table('phase_activities')->where('product_id', 2)->count());
        $this->assertSame(0, DB::table('task_phases')->where('product_id', 16)->count());
        $this->assertSame(0, DB::table('phase_activities')->where('product_id', 16)->count());
    }

    public function test_fehlende_wp_gruppe_wirft_klaren_fehler(): void
    {
        DB::table('article_groups')->where('article_group', 'Wärmepumpe')->delete();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/Wärmepumpe/');

        $this->seed(WaermepumpeKomplettloesungSeeder::class);
    }
}
