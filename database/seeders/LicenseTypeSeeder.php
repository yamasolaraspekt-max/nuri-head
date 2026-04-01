<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class LicenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $licenses = [
            [
                'initials' => 'AM',
                'de_name' => 'Kleinkrafträder, Mopeds',
                'en_name' => 'small motorcycles, mopeds',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><path d="M20 80 L40 80 L50 60 L70 60 L80 80 L60 80 L50 100 Z" fill="black"/></svg>'
            ],
            [
                'initials' => 'A1',
                'de_name' => 'Leichtkrafträder',
                'en_name' => 'light motorcycles up to 125 cc',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><path d="M20 80 L30 60 L40 60 L50 40 L60 40 L70 60 L80 60 L90 80 Z" fill="black"/></svg>'
            ],
            [
                'initials' => 'A2',
                'de_name' => 'Motorräder mit einer Leistung von bis zu 35 kW',
                'en_name' => 'motorcycles with power up to 35 kW',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><path d="M10 80 L30 60 L40 50 L50 30 L60 50 L70 60 L90 80 Z" fill="black"/></svg>'
            ],
            [
                'initials' => 'A',
                'de_name' => 'Motorräder',
                'en_name' => 'motorcycles',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><path d="M15 80 L35 60 L45 45 L55 25 L65 45 L75 60 L95 80 Z" fill="black"/></svg>'
            ],
            [
                'initials' => 'B',
                'de_name' => 'PKW',
                'en_name' => 'passenger cars',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><path d="M20 80 L80 80 L70 60 L30 60 Z" fill="black"/></svg>'
            ],
            [
                'initials' => 'BE',
                'de_name' => 'PKW mit Anhänger',
                'en_name' => 'passenger cars with trailer',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><path d="M20 80 L60 80 L50 60 L30 60 Z" fill="black"/><rect x="60" y="60" width="30" height="20" fill="black"/></svg>'
            ],
            [
                'initials' => 'B96',
                'de_name' => 'PKW mit Anhänger (more than 3.5 t and up to 4.25 t total mass)',
                'en_name' => 'passenger cars with trailer (more than 3.5 t and up to 4.25 t total mass)',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><path d="M20 80 L60 80 L50 60 L30 60 Z" fill="black"/><rect x="50" y="60" width="40" height="20" fill="black"/></svg>'
            ],
            [
                'initials' => 'C1',
                'de_name' => 'Leichte LKW',
                'en_name' => 'light trucks',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><rect x="20" y="60" width="60" height="20" fill="black"/></svg>'
            ],
            [
                'initials' => 'C1E',
                'de_name' => 'Leichte LKW mit Anhänger',
                'en_name' => 'light trucks with trailer',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><rect x="20" y="60" width="40" height="20" fill="black"/><rect x="60" y="60" width="20" height="20" fill="black"/></svg>'
            ],
            [
                'initials' => 'C',
                'de_name' => 'LKW',
                'en_name' => 'trucks',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><rect x="20" y="50" width="60" height="30" fill="black"/></svg>'
            ],
            [
                'initials' => 'CE',
                'de_name' => 'LKW mit Anhänger',
                'en_name' => 'trucks with trailer',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><rect x="10" y="50" width="50" height="30" fill="black"/><rect x="60" y="50" width="20" height="30" fill="black"/></svg>'
            ],
            [
                'initials' => 'D1',
                'de_name' => 'Kleinbusse',
                'en_name' => 'small buses',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><rect x="20" y="40" width="60" height="40" fill="black"/></svg>'
            ],
            [
                'initials' => 'D1E',
                'de_name' => 'Kleinbusse mit Anhänger',
                'en_name' => 'small buses with trailer',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><rect x="20" y="40" width="40" height="40" fill="black"/><rect x="60" y="40" width="20" height="40" fill="black"/></svg>'
            ],
            [
                'initials' => 'D',
                'de_name' => 'Busse',
                'en_name' => 'buses',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><rect x="10" y="40" width="70" height="40" fill="black"/></svg>'
            ],
            [
                'initials' => 'DE',
                'de_name' => 'Busse mit Anhänger',
                'en_name' => 'buses with trailer',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><rect x="10" y="40" width="50" height="40" fill="black"/><rect x="60" y="40" width="20" height="40" fill="black"/></svg>'
            ],
            [
                'initials' => 'L',
                'de_name' => 'Landwirtschaftliche Zugmaschinen',
                'en_name' => 'agricultural tractors',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><circle cx="50" cy="50" r="20" fill="black"/></svg>'
            ],
            [
                'initials' => 'T',
                'de_name' => 'Landwirtschaftliche Zugmaschinen (more powerful than L)',
                'en_name' => 'agricultural tractors (more powerful than L)',
                'svg_data' => '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="white"/><circle cx="50" cy="50" r="30" fill="black"/></svg>'
            ]
            ];


        DB::table('license_types')->insert($licenses);
    }
}
