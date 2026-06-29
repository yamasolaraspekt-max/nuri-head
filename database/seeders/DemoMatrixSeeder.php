<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DEMO-Zuständigkeitsmatrix (product_positions): ordnet jedes Produkt einer Gewerk-Abteilung
 * und den zuständigen Innendienst-/Außendienst-Funktionen zu — exakt im Format, das die
 * Konfigurationsseite „Zuständigkeitsmatrix" (ProductPositionController@save) erzeugt:
 *   position_ids = {"internal": [positionIds], "external": [positionIds]}
 *
 * Damit greift in der Anfrage die automatische Auswahl Innendienst/Außendienst je Produkt.
 * Idempotent. Setzt voraus, dass Produkte (article_groups), Abteilungen und Funktionen (positions) existieren.
 */
class DemoMatrixSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $branchId = DB::table('branches')->where('slug', 'solar-aspekt-nord')->value('id');

        $products = DB::table('article_groups')->pluck('id', 'article_group')->all();   // Name => id
        $depIds   = DB::table('departments')->where('branch_id', $branchId)->pluck('id', 'department_name')->all();
        $posIds   = DB::table('positions')->pluck('id', 'position')->all();              // Funktionsname => id

        // Produkt -> zuständiges Gewerk/Abteilung
        $prodDept = [
            'Photovoltaik' => 'Elektro', 'Wärmepumpe' => 'Heizung', 'Batteriespeicher' => 'Elektro',
            'Wallbox' => 'Elektro', 'Fenster' => 'Bauelemente', 'Türen' => 'Bauelemente',
            'Badsanierung' => 'SHK', 'Küche' => 'Schreiner', 'Fliesen' => 'Fliesenleger',
            'Dach' => 'Dachdecker', 'Insektenschutz' => 'Bauelemente', 'Fliegengitter' => 'Bauelemente',
            'Tapete' => 'Maler',
        ];

        // Innendienst-Funktionen (Büro) vs. Außendienst-Funktionen (Vor-Ort)
        $internalNames = ['Innendienstmitarbeiter/in', 'Sachbearbeiter/in', 'Planer/in'];
        $externalNames = ['Außendienstmitarbeiter/in', 'Meister/in', 'Geselle/in', 'Techniker/in'];
        $internal = array_values(array_filter(array_map(fn($n) => $posIds[$n] ?? null, $internalNames)));
        $external = array_values(array_filter(array_map(fn($n) => $posIds[$n] ?? null, $externalNames)));

        // Idempotenter Reset
        DB::table('product_positions')->delete();

        $n = 0;
        foreach ($prodDept as $product => $dep) {
            if (! isset($products[$product])) {
                continue;
            }
            DB::table('product_positions')->insert([
                'stage'            => 'inquiry',
                'article_group_id' => $products[$product],
                'service_id'       => null,
                'department_id'    => $depIds[$dep] ?? null,
                'position_ids'     => json_encode(['internal' => $internal, 'external' => $external]),
                'created_at'       => $now, 'updated_at' => $now,
            ]);
            $n++;
        }

        $this->command->info('Zuständigkeitsmatrix: ' . $n . ' Produkt-Zuordnungen (Innendienst ' . count($internal) . ' / Außendienst ' . count($external) . ' Funktionen).');
    }
}
