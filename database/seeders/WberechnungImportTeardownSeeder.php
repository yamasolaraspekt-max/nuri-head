<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Rückbau des wberechnung-Imports (Cut-over Stufe 2). Löscht zeilengenau über den Marker
 * products.imported_from='wberechnung': erst die Spec-Detailzeilen (diese Tabellen tragen keinen
 * FK-cascade), dann die products selbst. Bestand (imported_from=null) bleibt unberührt.
 */
class WberechnungImportTeardownSeeder extends Seeder
{
    public function run(): void
    {
        $ids = DB::table('products')->where('imported_from', WberechnungImportSeeder::MARKER)->pluck('id');
        DB::table('product_heat_pump_specs')->whereIn('product_id', $ids)->delete();
        DB::table('product_pv_module_specs')->whereIn('product_id', $ids)->delete();
        $n = DB::table('products')->where('imported_from', WberechnungImportSeeder::MARKER)->delete();

        $this->command?->info("wberechnung-Import zurückgebaut: {$n} products + zugehörige Specs gelöscht.");
    }
}
