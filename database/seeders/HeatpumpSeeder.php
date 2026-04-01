<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Distributor;
use App\Models\DistributorPrice;

class HeatpumpSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            [
                'sku' => "WP-A07", 'name' => "AeroTherm 7 kW Monoblock R32", 
                'kategorie' => "waermepumpe", 'typ' => "Luft-Wasser", 
                'bauart' => "Monoblock", 'kaeltemittel' => "R32", 
                'phasen' => 1, 'capacity_kw_A-7_W35' => 7.2, 
                'scop' => 4.5, 'schall_db' => 54, 
                'preis' => 7200, 'kosten' => 5600, 'mwst' => 19, 
                'marke' => "Aero"
            ],
            [
                'sku' => "WP-A10", 'name' => "AeroTherm 10 kW Monoblock R32", 
                'kategorie' => "waermepumpe", 'typ' => "Luft-Wasser", 
                'bauart' => "Monoblock", 'kaeltemittel' => "R32", 
                'phasen' => 1, 'capacity_kw_A-7_W35' => 9.8, 
                'scop' => 4.3, 'schall_db' => 56, 
                'preis' => 8300, 'kosten' => 6400, 'mwst' => 19, 
                'marke' => "Aero"
            ],
            [
                'sku' => "WP-A12-3P", 'name' => "AeroTherm 12 kW Split R290 (3~)", 
                'kategorie' => "waermepumpe", 'typ' => "Luft-Wasser", 
                'bauart' => "Split", 'kaeltemittel' => "R290", 
                'phasen' => 3, 'capacity_kw_A-7_W35' => 12.0, 
                'scop' => 4.6, 'schall_db' => 52, 
                'preis' => 9800, 'kosten' => 7500, 'mwst' => 19, 
                'marke' => "Aero"
            ],
            [
                'sku' => "WP-V08", 'name' => "BeispielMarke 8 kW R290 Monoblock", 
                'kategorie' => "waermepumpe", 'typ' => "Luft-Wasser", 
                'bauart' => "Monoblock", 'kaeltemittel' => "R290", 
                'phasen' => 1, 'capacity_kw_A-7_W35' => 8.0, 
                'scop' => 4.7, 'schall_db' => 49, 
                'preis' => 9900, 'kosten' => 7800, 'mwst' => 19, 
                'marke' => "BeispielMarke"
            ],
            [
                'sku' => "WP-V12-3P", 'name' => "BeispielMarke 12 kW R290 Monoblock (3~)", 
                'kategorie' => "waermepumpe", 'typ' => "Luft-Wasser", 
                'bauart' => "Monoblock", 'kaeltemittel' => "R290", 
                'phasen' => 3, 'capacity_kw_A-7_W35' => 12.5, 
                'scop' => 4.6, 'schall_db' => 50, 
                'preis' => 11800, 'kosten' => 9300, 'mwst' => 19, 
                'marke' => "BeispielMarke"
            ],
        ];

        foreach ($catalog as $item) {
            // 1) Brand
            $brand = Brand::firstOrCreate(
                ['name' => $item['marke']],
                [
                    'type' => 'brand',
                    'initial' => strtoupper(substr($item['marke'], 0, 2)),
                    'purpose' => 'WÄRMEPUMPE',
                    'status' => 'Published',
                ]
            );

            // 2) Distributor (same name as brand for demo)
            $distributor = Distributor::firstOrCreate(
                ['name' => $item['marke']],
                [
                    'status' => 'Published',
                ]
            );

            // 3) Product
            $product = Product::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'article_no' => $item['sku'],
                    'product' => $item['name'],
                    'category' => $item['kategorie'],
                    'heatpump_type' => $item['typ'],
                    'construction_type' => $item['bauart'],
                    'refrigerant' => $item['kaeltemittel'],
                    'phase_count' => $item['phasen'],
                    'scop' => $item['scop'],
                    'noise_level_db' => $item['schall_db'],
                    'retail_price' => $item['preis'],
                    'purchase_price' => $item['kosten'],
                    'vat_percent' => $item['mwst'],
                    'brand_id' => $brand->id,
                    'status' => 'active',
                ]
            );

            // 4) Distributor Price
            DistributorPrice::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'distributor_id' => $distributor->id,
                ],
                [
                    'article_no' => $item['sku'],
                    'price' => $item['preis'],
                    'purchase_price' => $item['kosten'],
                    'discount_price' => null,
                    'discount_percent' => null,
                    'price_date' => Carbon::today(),
                    'availability' => 'in-stock',
                    'status' => 'Published',
                ]
            );
        }
    }
}
