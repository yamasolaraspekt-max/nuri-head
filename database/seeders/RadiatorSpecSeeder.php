<?php

namespace Database\Seeders;

use App\Models\RadiatorSpec;
use Illuminate\Database\Seeder;

/**
 * Heizkörper-EN-442-Katalog — 1:1 übernommen aus wberechnung
 * (database/seeders/HeizkoerperSeeder.php, Kermi-therm-x2-kalibriert).
 *
 * q_norm_w_pro_m = Normleistung je m Baulänge bei 75/65/20 (EN 442, Δt 50 K).
 * WICHTIG: pro Meter — der Heizkörper-Adapter rechnet q_norm_w = q_norm_w_pro_m × Baulänge × Anzahl
 * (Baulänge = radiator_installations.width). Hier NICHT vormultiplizieren.
 *
 * Herkunft rückverfolgbar/rückbaubar über imported_from='wberechnung_seeder'.
 * bauart-Mapping: wberechnung 'platte' → ticket-Enum 'kompakt' (semantisch identisch,
 * Kompakt-/Plattenheizkörper); glieder/roehren/konvektor unverändert.
 */
class RadiatorSpecSeeder extends Seeder
{
    private const MARKER = 'wberechnung_seeder';

    public function run(): void
    {
        $kermi = 'Kermi therm-x2 Profil-K (EN 442, Herstellerdaten)';
        $richt = 'EN 442 Richtwert (herstellerneutral)';

        // Plattenheizkörper (bauart 'platte' → 'kompakt'): typ, bautiefe_mm, exponent, quelle, [bauhöhe => W/m @75/65/20]
        $platten = [
            ['Kompakt Typ 10', 50, 1.28, $richt, [300 => 375, 400 => 460, 500 => 540, 600 => 610, 900 => 830]],
            ['Kompakt Typ 11', 61, 1.28, $kermi, [300 => 550, 400 => 690, 500 => 790, 600 => 979, 900 => 1330]],
            ['Kompakt Typ 21', 66, 1.30, $richt, [300 => 830, 400 => 1040, 500 => 1180, 600 => 1338, 900 => 1820]],
            ['Kompakt Typ 22', 100, 1.31, $kermi, [300 => 960, 400 => 1205, 500 => 1441, 600 => 1666, 900 => 2295]],
            ['Kompakt Typ 33', 155, 1.33, $kermi, [300 => 1300, 400 => 1610, 500 => 1920, 600 => 2236, 750 => 2645, 900 => 3023]],
        ];
        foreach ($platten as [$typ, $tiefe, $n, $quelle, $hoehen]) {
            foreach ($hoehen as $hoehe => $qpm) {
                $this->upsert($typ, 'kompakt', $hoehe, $tiefe, $qpm, $n, $quelle);
            }
        }

        // Sonderbauarten: typ, bauart, bauhöhe, bautiefe, W/m, exponent
        $sonder = [
            ['Gliederheizkörper', 'glieder', 600, 110, 1200, 1.30],
            ['Röhrenradiator', 'roehren', 600, 100, 900, 1.30],
            ['Konvektor', 'konvektor', 600, 90, 1100, 1.40],
            ['Badheizkörper (Handtuch)', 'roehren', 1800, 70, 800, 1.27],
        ];
        foreach ($sonder as [$typ, $bauart, $hoehe, $tiefe, $qpm, $n]) {
            $this->upsert($typ, $bauart, $hoehe, $tiefe, $qpm, $n, $richt);
        }
    }

    private function upsert(string $typ, string $bauart, int $hoehe, int $tiefe, float $qpm, float $n, string $quelle): void
    {
        RadiatorSpec::updateOrCreate(
            ['typ' => $typ, 'bauhoehe_mm' => $hoehe, 'bauart' => $bauart],
            [
                'hersteller' => 'generisch',
                'bautiefe_mm' => $tiefe,
                'q_norm_w_pro_m' => $qpm,
                'norm_bedingung' => '75/65/20',
                'exponent_n' => $n,
                'quelle' => $quelle,
                'imported_from' => self::MARKER,
                'aktiv' => true,
            ],
        );
    }
}
