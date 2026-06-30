<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DEMO-Lagerbestand: füllt die `inventories`-Tabelle mit Beständen für einige Produkte,
 * damit die Lagerausgabe (request_out) real durchgeklickt/getestet werden kann.
 *
 * Idempotent (Reset über Marker: inventories.article_no 'DEMO-LAGER-%').
 * Kein Bezug zu echten Daten. Setzt vorhandene Produkte voraus.
 */
class DemoInventorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ── Reset (idempotent) ─────────────────────────────────────────
        DB::table('inventories')->where('article_no', 'like', 'DEMO-LAGER-%')->delete();

        $products = DB::table('products')->orderBy('id')->limit(15)->get();
        if ($products->isEmpty()) {
            $this->command->warn('Keine Produkte vorhanden — bitte zuerst die Produkt-Seeder ausführen.');
            return;
        }

        $locations  = ['Zentrallager Hamburg', 'Werkstatt', 'Außenlager Nord'];
        $categories = ['Lagerartikel', 'Verbrauchsmaterial', 'Ersatzteil'];
        $n = 0;

        foreach ($products as $p) {
            $n++;
            DB::table('inventories')->insert([
                'product_id'         => $p->id,
                'article_no'         => 'DEMO-LAGER-' . str_pad((string) $n, 3, '0', STR_PAD_LEFT),
                'serial_no'          => 'SN-LAGER-' . str_pad((string) $n, 3, '0', STR_PAD_LEFT),
                'quantity'           => rand(50, 300),
                'quantity_unit'      => 'Stück',
                'location'           => $locations[array_rand($locations)],
                'inventory_category' => $categories[array_rand($categories)],
                'add_date'           => $now->toDateString(),
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }

        $this->command->info("Demo-Lagerbestand: {$n} inventories-Zeilen angelegt (Marker DEMO-LAGER-%).");
    }
}
