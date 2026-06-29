<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DEMO-Partnerprofile: reichert Lieferanten (und später Hersteller) so an, dass die Profil-/
 * Detailansichten Inhalt zeigen.
 *  - Lieferanten-Sortiment: distributor_prices (welche Artikel ein Lieferant zu welchem Preis liefert)
 *  - distributors.cash_discount (Skonto)
 *
 * Idempotent (Reset über die Demo-Lieferanten). Setzt DemoPartnersArticlesSeeder voraus.
 */
class DemoPartnerProfileSeeder extends Seeder
{
    public function run(): void
    {
        $now  = Carbon::now();
        $pick = fn(array $a) => $a[array_rand($a)];

        $groupId = DB::table('article_groups')->pluck('id', 'article_group')->all(); // Name => id

        // Lieferant (short_name) -> belieferte Produktgruppen (Sortiment-Schwerpunkt)
        $supCat = [
            'KRA' => ['Photovoltaik', 'Batteriespeicher', 'Wallbox'],
            'BAY' => ['Photovoltaik', 'Wärmepumpe', 'Batteriespeicher'],
            'MEM' => ['Photovoltaik', 'Batteriespeicher', 'Wallbox'],
            'SON' => ['Photovoltaik', 'Wallbox'],
            'GCG' => ['Wärmepumpe', 'Badsanierung', 'Fliesen'],
            'RUF' => ['Wärmepumpe', 'Badsanierung'],
            'EGL' => ['Wallbox', 'Photovoltaik'],
            'BAU' => ['Dach', 'Fenster', 'Türen', 'Fliesen', 'Tapete'],
        ];

        $distributors = DB::table('distributors')->get();
        $distIds = $distributors->pluck('id')->all();
        if ($distIds) {
            DB::table('distributor_prices')->whereIn('distributor_id', $distIds)->delete();
        }

        $nPos = 0;
        foreach ($distributors as $d) {
            // Skonto setzen
            DB::table('distributors')->where('id', $d->id)->update(['cash_discount' => $pick([2, 2.5, 3])]);

            // Produktgruppen des Lieferanten (Fallback: erste 3 Gruppen)
            $cats = $supCat[$d->short_name] ?? array_slice(array_keys($groupId), 0, 3);
            $gids = array_values(array_filter(array_map(fn($c) => $groupId[$c] ?? null, $cats)));
            if (! $gids) {
                continue;
            }

            $articles = DB::table('products')->whereIn('article_group', $gids)->get();
            foreach ($articles as $a) {
                $purchase = (float) ($a->purchase_price ?: rand(50, 500));
                $supEk    = round($purchase * (0.88 + rand(0, 16) / 100), 2); // Lieferanten-EK leicht unter Artikel-EK
                DB::table('distributor_prices')->insert([
                    'distributor_id'   => $d->id,
                    'product_id'       => $a->id,
                    'article_no'       => $a->article_no,
                    'purchase_price'   => $supEk,
                    'price'            => round($supEk * 1.05, 2),
                    'discount_percent' => $pick([0, 3, 5, 8]),
                    'availability'     => $pick(['sofort', '2-3 Tage', '1 Woche', 'auf Anfrage']),
                    'price_date'       => $now->toDateString(),
                    'status'           => 'active',
                    'created_at'       => $now, 'updated_at' => $now,
                ]);
                $nPos++;
            }
        }

        $this->command->info('Lieferanten-Profile: Skonto gesetzt + ' . $nPos . ' Sortiment-/Preis-Positionen für ' . $distributors->count() . ' Lieferanten.');
    }
}
