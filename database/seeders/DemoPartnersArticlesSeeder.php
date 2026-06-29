<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DEMO-Partner & Artikel: Hersteller (brands + brand_departments),
 * Lieferanten (distributors + distributor_departments) und Artikel (products).
 *
 * Idempotent (Reset über Demo-Marker). Nur frei erfundene/branchenübliche Bezeichnungen.
 * Versorgt die Kontaktliste (Typ „Hersteller"/„Lieferant") und die Artikelstammdaten.
 */
class DemoPartnersArticlesSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $pick = fn(array $a) => $a[array_rand($a)];

        $vor   = ['Lukas', 'Jonas', 'Paul', 'Tim', 'Jan', 'Mia', 'Emma', 'Lea', 'Sophie', 'Nina', 'Marco', 'Sven', 'Katrin', 'Anke'];
        $nach  = ['Schmidt', 'Meyer', 'Becker', 'Wolf', 'Richter', 'Hansen', 'Voß', 'Stahl', 'Brandt', 'Engel', 'Greve', 'Block'];
        $orte  = [['Hamburg', '22045'], ['Stuttgart', '70173'], ['München', '80331'], ['Köln', '50667'], ['Berlin', '10115'], ['Nürnberg', '90402'], ['Kassel', '34117']];
        $person = fn() => $pick($vor) . ' ' . $pick($nach);

        // ── Reset Demo-Daten ───────────────────────────────────────────
        $oldBrandIds = DB::table('brands')->pluck('id')->all();
        if ($oldBrandIds) {
            DB::table('brand_departments')->whereIn('brand_id', $oldBrandIds)->delete();
            DB::table('brands')->whereIn('id', $oldBrandIds)->delete();
        }
        $oldDistIds = DB::table('distributors')->pluck('id')->all();
        if ($oldDistIds) {
            DB::table('distributor_departments')->whereIn('d_id', $oldDistIds)->delete();
            DB::table('distributors')->whereIn('id', $oldDistIds)->delete();
        }
        DB::table('products')->where('article_no', 'like', 'ART-%')->delete();

        // ── Hersteller (brands) ────────────────────────────────────────
        $brands = [
            'Meyer Burger', 'SMA Solar', 'Fronius', 'Viessmann', 'Vaillant', 'Stiebel Eltron',
            'BYD', 'sonnen', 'ABL', 'KEBA', 'Veka', 'Schüco', 'Kömmerling',
            'Villeroy & Boch', 'Grohe', 'Nobilia', 'Agrob Buchtal', 'Braas', 'Neher', 'Erismann',
        ];
        $brandId = [];
        foreach ($brands as $b) {
            $ort = $pick($orte);
            DB::table('brands')->insert([
                'type' => 'Hersteller', 'name' => $b, 'initial' => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $b), 0, 3)),
                'purpose' => 'Hersteller / Markenpartner', 'address' => $ort[1] . ' ' . $ort[0],
                'status' => 'active', 'created_at' => $now, 'updated_at' => $now,
            ]);
            $bid = DB::table('brands')->where('name', $b)->value('id');
            $brandId[$b] = $bid;
            // 1-2 Ansprechpartner je Hersteller
            foreach (['Vertrieb', 'Technik'] as $i => $abt) {
                if ($i === 1 && rand(0, 1) === 0) {
                    continue;
                }
                DB::table('brand_departments')->insert([
                    'brand_id' => $bid, 'brand_department' => $abt, 'name' => $person(),
                    'position' => $abt === 'Vertrieb' ? 'Vertriebsleitung' : 'Technischer Support',
                    'phone' => '0' . rand(30, 89) . ' ' . rand(1000000, 9999999),
                    'email' => strtolower(preg_replace('/[^a-z]/i', '', $b)) . '.' . strtolower($abt) . '@hersteller.test',
                    'status' => 'active', 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // ── Lieferanten (distributors) ─────────────────────────────────
        $distis = [
            ['KRA', 'Krannich Solar GmbH', 'PV-Großhandel'],
            ['BAY', 'BayWa r.e. GmbH', 'Erneuerbare-Energien-Großhandel'],
            ['MEM', 'Memodo GmbH', 'PV-/Speicher-Großhandel'],
            ['SON', 'Sonepar Deutschland', 'Elektro-Großhandel'],
            ['GCG', 'GC Gruppe', 'SHK-Großhandel'],
            ['RUF', 'Richter+Frenzel', 'SHK-Großhandel'],
            ['EGL', 'Eltako Großhandel', 'Elektro-Großhandel'],
            ['BAU', 'Baustoff Union', 'Baustoff-Großhandel'],
        ];
        foreach ($distis as $i => $d) {
            $ort = $pick($orte);
            DB::table('distributors')->insert([
                'short_name' => $d[0], 'created_date' => $now->toDateString(), 'type' => $d[2],
                'salutation' => 'Firma', 'name' => $d[1], 'street' => $pick(['Industriestraße', 'Gewerbering', 'Am Hafen']) . ' ' . rand(1, 90),
                'postal_code' => $ort[1], 'city' => $ort[0], 'phone' => '0' . rand(30, 89) . ' ' . rand(1000000, 9999999),
                'email' => 'einkauf@' . strtolower($d[0]) . '-grosshandel.test', 'mobile' => '01' . rand(50, 79) . rand(1000000, 9999999),
                'account_number' => 'KD-' . str_pad((string) ($i + 1), 5, '0', STR_PAD_LEFT),
                'payment_terms' => $pick(['14 Tage netto', '30 Tage netto', '2% Skonto / 10 Tage']),
                'status' => 'active', 'created_at' => $now, 'updated_at' => $now,
            ]);
            $did = DB::table('distributors')->where('short_name', $d[0])->value('id');
            foreach (['Einkauf', 'Disposition'] as $j => $abt) {
                if ($j === 1 && rand(0, 1) === 0) {
                    continue;
                }
                DB::table('distributor_departments')->insert([
                    'd_id' => $did, 'd_department' => $abt, 'name' => $person(),
                    'position' => $abt === 'Einkauf' ? 'Key Account Manager' : 'Disponent/in',
                    'phone' => '0' . rand(30, 89) . ' ' . rand(1000000, 9999999),
                    'email' => strtolower($abt) . '@' . strtolower($d[0]) . '-grosshandel.test',
                    'status' => 'active', 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }

        // ── Artikel (products) je Produktgruppe + Hersteller ───────────
        // [Produktgruppe => [Artikel-Präfix, Einheit, Preis von, Preis bis, [Hersteller...]]]
        $cat = [
            'Photovoltaik'    => ['PV-Modul', 'Stück', 180, 340, ['Meyer Burger', 'SMA Solar', 'Fronius']],
            'Wärmepumpe'      => ['Luft-Wasser-Wärmepumpe', 'Stück', 6000, 14000, ['Viessmann', 'Vaillant', 'Stiebel Eltron']],
            'Batteriespeicher' => ['Stromspeicher', 'Stück', 4000, 9000, ['BYD', 'sonnen']],
            'Wallbox'         => ['Wallbox 11 kW', 'Stück', 500, 1300, ['ABL', 'KEBA']],
            'Fenster'         => ['Kunststofffenster', 'Stück', 250, 900, ['Veka', 'Schüco']],
            'Türen'           => ['Haustür', 'Stück', 900, 2600, ['Kömmerling', 'Schüco']],
            'Badsanierung'    => ['Sanitärobjekt', 'Stück', 150, 1800, ['Villeroy & Boch', 'Grohe']],
            'Küche'           => ['Küchenzeile', 'Paket', 5000, 18000, ['Nobilia']],
            'Fliesen'         => ['Fliese', 'm²', 18, 70, ['Agrob Buchtal']],
            'Dach'            => ['Dachziegel', 'm²', 22, 60, ['Braas']],
            'Insektenschutz'  => ['Insektenschutzgitter', 'Stück', 60, 220, ['Neher']],
            'Fliegengitter'   => ['Fliegengitter-Rollo', 'Stück', 40, 160, ['Neher']],
            'Tapete'          => ['Vliestapete', 'Rolle', 12, 60, ['Erismann']],
        ];
        $groupId = DB::table('article_groups')->pluck('id', 'article_group')->all(); // Name => id (article_group ist eine FK-ID)
        $no = 1;
        foreach ($cat as $group => $c) {
            [$prefix, $unit, $min, $max, $cbrands] = $c;
            $variants = ['Standard', 'Komfort', 'Premium', 'Eco'];
            foreach (array_slice($variants, 0, rand(3, 4)) as $variant) {
                $brand = $pick($cbrands);
                $purchase = rand($min, $max);
                $retail   = round($purchase * (1.25 + rand(0, 30) / 100), 2);
                DB::table('products')->insert([
                    'article_no'   => 'ART-' . str_pad((string) $no, 4, '0', STR_PAD_LEFT),
                    'sku'          => 'SKU-' . str_pad((string) $no, 4, '0', STR_PAD_LEFT),
                    'article_group' => ($groupId[$group] ?? null),
                    'brand_id'     => $brandId[$brand] ?? null,
                    'product'      => $prefix . ' ' . $brand . ' ' . $variant,
                    'model'        => $variant,
                    'category'     => $group,
                    'measure_unit' => null, // FK auf measures.id; Demo ohne Einheiten-Stammdaten
                    'short_description' => 'Einheit: ' . $unit,
                    'purchase_price' => $purchase,
                    'retail_price' => $retail,
                    'vat_percent'  => 19,
                    'status'       => 'active',
                    'created_at'   => $now, 'updated_at' => $now,
                ]);
                $no++;
            }
        }

        $this->command->info('Partner & Artikel: ' . count($brands) . ' Hersteller, ' . count($distis) . ' Lieferanten, ' . ($no - 1) . ' Artikel.');
    }
}
