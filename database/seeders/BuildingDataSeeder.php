<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class BuildingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     DB::table('building_data')->insert([
            ['year' => 1918, 'u_wand' => 2.2, 'u_wand_gut' => 2, 'u_wand_ad' => 0.34, 'u_wand_id' => 0.41, 'u_boden' => 1, 'u_boden_dae' => 0.29, 'u_kellerdecke' => 1, 'u_kg_decke_d' => 0.37, 'u_dach' => 2.9, 'u_fenster_1' => 5.5, 'u_fenster_2' => null, 'u_tuer' => null],
            ['year' => 1948, 'u_wand' => 1.7, 'u_wand_gut' => 1.4, 'u_wand_ad' => 0.32, 'u_wand_id' => 0.4, 'u_boden' => 0.8, 'u_boden_dae' => 0.27, 'u_kellerdecke' => 0.8, 'u_kg_decke_d' => 0.37, 'u_dach' => 2.6, 'u_fenster_1' => 4.5, 'u_fenster_2' => null, 'u_tuer' => null],
            ['year' => 1949, 'u_wand' => 1.4, 'u_wand_gut' => 0.9, 'u_wand_ad' => 0.31, 'u_wand_id' => 0.37, 'u_boden' => 0.8, 'u_boden_dae' => 0.27, 'u_kellerdecke' => 0.8, 'u_kg_decke_d' => 0.37, 'u_dach' => 1.4, 'u_fenster_1' => 2.5, 'u_fenster_2' => null, 'u_tuer' => null],
            ['year' => 1969, 'u_wand' => 1.1, 'u_wand_gut' => 1, 'u_wand_ad' => 0.32, 'u_wand_id' => 0.33, 'u_boden' => 0.6, 'u_boden_dae' => 0.27, 'u_kellerdecke' => 1, 'u_kg_decke_d' => 0.37, 'u_dach' => 0.8, 'u_fenster_1' => 2.7, 'u_fenster_2' => null, 'u_tuer' => null],
            ['year' => 1979, 'u_wand' => 0.9, 'u_wand_gut' => 0.6, 'u_wand_ad' => 0.31, 'u_wand_id' => 0.32, 'u_boden' => 0.5, 'u_boden_dae' => 0.29, 'u_kellerdecke' => 0.8, 'u_kg_decke_d' => 0.37, 'u_dach' => 0.5, 'u_fenster_1' => 2.5, 'u_fenster_2' => null, 'u_tuer' => null],
            ['year' => 1984, 'u_wand' => 0.6, 'u_wand_gut' => 0.5, 'u_wand_ad' => 0.32, 'u_wand_id' => 0.27, 'u_boden' => 0.3, 'u_boden_dae' => 0.17, 'u_kellerdecke' => 0.6, 'u_kg_decke_d' => 0.4, 'u_dach' => 0.4, 'u_fenster_1' => 1.9, 'u_fenster_2' => null, 'u_tuer' => null],
            ['year' => 1995, 'u_wand' => 0.5, 'u_wand_gut' => null, 'u_wand_ad' => null, 'u_wand_id' => null, 'u_boden' => null, 'u_boden_dae' => null, 'u_kellerdecke' => null, 'u_kg_decke_d' => null, 'u_dach' => null, 'u_fenster_1' => 1.5, 'u_fenster_2' => null, 'u_tuer' => null],
        ]);
    }
}
