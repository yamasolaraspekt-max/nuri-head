<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Vollständiger, MARKER-BASIERTER Rückbau von FoxEssLongiCatalogSeeder (products.imported_from='fox-longi-seed').
 *
 * NIE über brand_id/Gruppen-Zugehörigkeit löschen — geteilte Stammdaten (Marken, Gruppen) sind per Definition
 * mehrbesitzt (z. B. Marke „LONGi" trägt auch wberechnung-Module). Marke/Gruppe werden NUR entfernt, wenn
 * danach KEINE products mehr dranhängen (egal welcher Herkunft), sonst stehen gelassen + im Output gemeldet.
 *
 * Rückbau-Weg: products (Marker) -> product_pv_module_specs (kein FK-Cascade) explizit per product_id;
 * inverters/batteries kaskadieren beim products-Delete (FK onDelete cascade).
 */
class FoxEssLongiCatalogTeardownSeeder extends Seeder
{
    public function run(): void
    {
        $marker = FoxEssLongiCatalogSeeder::MARKER;

        $productIds = DB::table('products')->where('imported_from', $marker)->pluck('id')->all();
        if ($productIds) {
            DB::table('product_pv_module_specs')->whereIn('product_id', $productIds)->delete();
            DB::table('products')->whereIn('id', $productIds)->delete(); // inverters/batteries kaskadieren
        }

        $gemeldet = [];

        // Geteilte Marken NUR entfernen, wenn nichts mehr dranhängt (mehrbesitzt!).
        foreach (['Fox ESS', 'LONGi'] as $name) {
            $bid = DB::table('brands')->where('name', $name)->value('id');
            if (! $bid) {
                continue;
            }
            $rest = DB::table('products')->where('brand_id', $bid)->count();
            if ($rest === 0) {
                DB::table('brands')->where('id', $bid)->delete();
            } else {
                $gemeldet[] = "Marke '{$name}' bleibt ({$rest} fremde products)";
            }
        }

        // Vom Seeder neu angelegte Gruppen NUR entfernen, wenn nichts mehr referenziert.
        foreach (['Wechselrichter', 'Zubehör'] as $g) {
            $gid = DB::table('article_groups')->where('article_group', $g)->value('id');
            if (! $gid) {
                continue;
            }
            $rest = DB::table('products')->where('article_group', (string) $gid)->count()
                + DB::table('inverters')->where('article_group_id', $gid)->count()
                + DB::table('batteries')->where('article_group_id', $gid)->count();
            if ($rest === 0) {
                DB::table('article_groups')->where('id', $gid)->delete();
            } else {
                $gemeldet[] = "Gruppe '{$g}' bleibt ({$rest} fremde Referenzen)";
            }
        }

        $meldung = $gemeldet !== [] ? ' — '.implode('; ', $gemeldet) : '';
        $this->command?->info('Fox-ESS/LONGi-Katalog marker-basiert zurückgebaut (Marker='.$marker.').'.$meldung);
    }
}
