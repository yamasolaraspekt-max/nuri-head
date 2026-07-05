<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Rückbau der B2a-1 Referenz-Kataloge über den Marker imported_from='wberechnung'.
 * konstruktionen zuerst (referenziert materials über schichten.material_id), dann materials/baualtersklassen/klima_plz.
 */
class ReferenzKatalogTeardownSeeder extends Seeder
{
    public function run(): void
    {
        $marker = ReferenzKatalogSeeder::MARKER;
        $geloescht = [];
        foreach (['konstruktionen', 'materials', 'baualtersklassen', 'klima_plz'] as $t) {
            $geloescht[$t] = DB::table($t)->where('imported_from', $marker)->delete();
        }

        $this->command?->info('Referenz-Kataloge zurückgebaut: '.json_encode($geloescht));
    }
}
