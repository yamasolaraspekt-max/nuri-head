<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Vollständiger Rückbau von FoxEssLongiCatalogSeeder. Beweist Reversibilität auf der produktiven DB:
 * Zählstand products/inverters/batteries/product_pv_module_specs vor Seed == nach Teardown.
 *
 * Rückbau-Weg: Marken 'Fox ESS'/'LONGi' (vom Seeder angelegt) -> deren products löschen.
 * product_pv_module_specs (kein FK-Cascade) explizit vorher; inverters/batteries kaskadieren beim
 * products-Delete (FK onDelete cascade). Danach die vom Seeder neu angelegten, jetzt leeren
 * Artikelgruppen ('Wechselrichter'/'Zubehör') und die Marken entfernen – nur wenn nichts mehr referenziert.
 */
class FoxEssLongiCatalogTeardownSeeder extends Seeder
{
    public function run(): void
    {
        $foxId = DB::table('brands')->where('name', 'Fox ESS')->value('id');
        $longiId = DB::table('brands')->where('name', 'LONGi')->value('id');
        $brandIds = array_values(array_filter([$foxId, $longiId]));

        if ($brandIds) {
            $productIds = DB::table('products')->whereIn('brand_id', $brandIds)->pluck('id')->all();
            if ($productIds) {
                DB::table('product_pv_module_specs')->whereIn('product_id', $productIds)->delete();
                DB::table('products')->whereIn('id', $productIds)->delete(); // -> inverters/batteries cascade
            }
        }

        // Neu angelegte, jetzt leere Artikelgruppen entfernen (nur wenn nichts mehr referenziert)
        foreach (['Wechselrichter', 'Zubehör'] as $g) {
            $gid = DB::table('article_groups')->where('article_group', $g)->value('id');
            if (! $gid) {
                continue;
            }
            $referenced = DB::table('products')->where('article_group', (string) $gid)->exists()
                || DB::table('inverters')->where('article_group_id', $gid)->exists()
                || DB::table('batteries')->where('article_group_id', $gid)->exists();
            if (! $referenced) {
                DB::table('article_groups')->where('id', $gid)->delete();
            }
        }

        // Marken entfernen, wenn keine Produkte mehr dranhängen
        foreach ($brandIds as $bid) {
            if (! DB::table('products')->where('brand_id', $bid)->exists()) {
                DB::table('brands')->where('id', $bid)->delete();
            }
        }

        $this->command?->info('Fox-ESS/LONGi-Katalog vollständig zurückgebaut.');
    }
}
