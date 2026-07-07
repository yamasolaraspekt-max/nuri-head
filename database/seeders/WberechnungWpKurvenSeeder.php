<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Backfill der Wärmepumpen-Leistungskurven (Cut-over Stufe 2, Nachtrag).
 *
 * Der WberechnungImportSeeder setzte product_heat_pump_specs.leistungskurve bewusst NULL
 * (Spalten-Fallback kurve_semantik='en14511_nenn'). Diese Stufe füllt die 3-Ebenen-COP-Kurve
 * {"35":[[t,p,cop],…],"45":…,"55":…} nach — der WpKennlinieService-Rechenkern braucht sie.
 *
 * Quelle eingefroren in database/data/wberechnung_wp_kurven.json (extrahiert aus wberechnung-DB,
 * Schlüssel hersteller|modell). NUR additiv: UPDATE der nullable JSON-Spalte auf den bereits
 * importierten WP-Zeilen (products.imported_from='wberechnung'). Idempotent: nur wo leistungskurve
 * noch NULL ist. Reversibilität über den bestehenden WberechnungImportTeardownSeeder (entfernt die
 * Marker-Zeilen inkl. Kurve). Kein Bestand außerhalb der Import-Marker wird berührt.
 */
class WberechnungWpKurvenSeeder extends Seeder
{
    public const MARKER = 'wberechnung';

    public function run(): void
    {
        $path = database_path('data/wberechnung_wp_kurven.json');
        $kurven = json_decode(file_get_contents($path), true);
        if (! is_array($kurven)) {
            $this->command?->error('wberechnung_wp_kurven.json ungültig — Abbruch.');

            return;
        }

        $updated = 0;
        $skipped = 0;
        $missing = [];

        foreach ($kurven as $key => $kurve) {
            [$hersteller, $modell] = array_pad(explode('|', (string) $key, 2), 2, '');

            $productId = DB::table('products')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->where('products.imported_from', self::MARKER)
                ->where('products.model', $modell)
                ->where('brands.name', $hersteller)
                ->value('products.id');

            if ($productId === null) {
                $missing[] = $key;

                continue;
            }

            // Idempotent: nur füllen, wo noch NULL (Re-Lauf ändert nichts).
            $affected = DB::table('product_heat_pump_specs')
                ->where('product_id', $productId)
                ->whereNull('leistungskurve')
                ->update(['leistungskurve' => json_encode($kurve), 'updated_at' => now()]);

            $affected > 0 ? $updated++ : $skipped++;
        }

        $msg = "WP-Leistungskurven: {$updated} gefüllt, {$skipped} bereits vorhanden";
        if ($missing) {
            $msg .= ', nicht zugeordnet: '.implode(', ', $missing);
        }
        $this->command?->info($msg.'.');
    }
}
