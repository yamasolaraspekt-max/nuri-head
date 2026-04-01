<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MasterSetSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $now = Carbon::now();

            // -------------------------------------------------------
            // Helper: find or insert (ignores soft-deleted)
            $firstOrCreateSimple = function (string $table, array $where, array $attrs = []) {
                $row = DB::table($table)->where($where)->whereNull('deleted_at')->first();
                if ($row) return $row;
                $payload = array_merge($where, $attrs, [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                DB::table($table)->insert($payload);
                return DB::table($table)->where($where)->first();
            };

            // -------------------------------------------------------
            // Helper: find product by SKU
            $findProductBySku = function (string $sku) {
                return DB::table('products')
                    ->where(function ($q) use ($sku) {
                        $q->where('sku', $sku)->orWhere('article_no', $sku);
                    })
                    ->first();
            };

            // -------------------------------------------------------
            // Helper: ensure Brand + Distributor
            $ensureBrandAndDistributor = function (string $name) use ($firstOrCreateSimple) {
                $brand = $firstOrCreateSimple('brands', ['name' => $name], [
                    'type' => 'brand',
                    'initial' => strtoupper(Str::of($name)->substr(0, 2)),
                    'purpose' => 'WÄRMEPUMPE',
                    'status' => 'Published',
                ]);
                $dist  = $firstOrCreateSimple('distributors', ['name' => $name], [
                    'status' => 'Published',
                ]);
                return [$brand, $dist];
            };

            // -------------------------------------------------------
            // Ensure Article Group + Sub Articles + Phase
            $agWp = $firstOrCreateSimple('article_groups', ['article_group' => 'Wärmepumpe']);
            $sagMono = $firstOrCreateSimple('sub_article_groups', [
                'article_group_id' => $agWp->id,
                'sub_article'      => 'Monoblock',
            ]);
            $sagSplit = $firstOrCreateSimple('sub_article_groups', [
                'article_group_id' => $agWp->id,
                'sub_article'      => 'Split',
            ]);
            $phase = $firstOrCreateSimple('task_phases', ['phase_name' => 'WP Installation'], [
                'product_id' => $agWp->id,
            ]);

            // -------------------------------------------------------
            // ✅ Ensure Positions (real positions table)
            $ensurePosition = function (string $name, string $desc = '') use ($firstOrCreateSimple) {
                return $firstOrCreateSimple('positions', ['position' => $name], [
                    'description' => $desc ?: $name,
                    'status' => 'Published',
                ]);
            };

            $posMonteur = $ensurePosition('Monteur', 'Führt die Hauptmontagearbeiten durch.');
            $posHelfer  = $ensurePosition('Helfer', 'Unterstützt Monteur bei Montage & Logistik.');
            $posElektr  = $ensurePosition('Elektriker', 'Installiert elektrische Komponenten.');
            $posIT      = $ensurePosition('IT-Manager', 'Konfiguriert Smart-Home/Steuerung.');

            // -------------------------------------------------------
            // ✅ Ensure Assets
            $assetPress = $firstOrCreateSimple('assets', ['item' => 'Rohrpressmaschine']);
            $assetLift  = $firstOrCreateSimple('assets', ['item' => 'Baustellenlift']);
            $assetVac   = $firstOrCreateSimple('assets', ['item' => 'Vakuumpumpe/Manometer']);

            // -------------------------------------------------------
            // ✅ Ensure Brands + Distributors
            [$brandAero, $distAero]   = $ensureBrandAndDistributor('Aero');
            [$brandBeisp, $distBeisp] = $ensureBrandAndDistributor('BeispielMarke');
            [$brandHydra, $distHydra] = $ensureBrandAndDistributor('Hydra');
            [$brandSense, $distSense] = $ensureBrandAndDistributor('Sense');

            // -------------------------------------------------------
            // ✅ Products from previous seed (lookup)
            $p = fn($sku) => $findProductBySku($sku);

            $wp8   = $p('WP-V08');
            $wp10  = $p('WP-A10');
            $wp12  = $p('WP-A12-3P');
            $sp200 = $p('SPEICH-200');
            $puf50 = $p('PUFFER-50');
            $ctrl  = $p('CTRL-SMART');

            // -------------------------------------------------------
            // Helper: add product line
            $addProduct = function (int $setId, $product, int $distId, int $qty = 1) use ($now) {
                if (!$product) return null;
                $retail = (float)($product->retail_price ?? 0);
                $purchase = (float)($product->purchase_price ?? 0);
                $total = $purchase * $qty;
                $lineId = DB::table('add_product_to_sets')->insertGetId([
                    'master_set_id'  => $setId,
                    'product_id'     => $product->id,
                    'distributor_id' => $distId,
                    'product_count'  => $qty,
                    'measure_unit'   => 1,
                    'retail_price'   => $retail,
                    'discount_group' => $product->discount_group ?? null,
                    'purchase_price' => $purchase,
                    'total'          => $total,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
                return (object)['line_id' => $lineId, 'purchase' => $total];
            };

            // Helper: sub product line
            $addSubProduct = function (int $setId, int $mainLineId, $product, int $distId, int $qty = 1) use ($now) {
                if (!$product) return null;
                $purchase = (float)($product->purchase_price ?? 0);
                $total = $purchase * $qty;
                DB::table('product_sub_sets')->insert([
                    'master_set_id'  => $setId,
                    'product_id'     => $product->id,
                    'main_product'   => $mainLineId,
                    'product_count'  => $qty,
                    'measure_unit'   => 1,
                    'distributor_id' => $distId,
                    'retail_price'   => $product->retail_price ?? 0,
                    'discount_group' => $product->discount_group ?? null,
                    'purchase_price' => $purchase,
                    'total'          => $total,
                    'status'         => 'Published',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
                return (object)['purchase' => $total];
            };

            // Helper: employee line
            $addEmployee = function (int $setId, int $positionId, float $hours, float $buyRate, ?float $saleRate = null) use ($now) {
                $saleRate = $saleRate ?? ($buyRate * 1.35);
                $total = $hours * $buyRate;
                DB::table('employee_sets')->insert([
                    'master_set_id' => $setId,
                    'product_id'    => 16, // keep null or your article_group_id if needed
                    'position_id'   => $positionId,
                    'work_hour'     => $hours,
                    'buying_price'  => $buyRate,
                    'sale_price'    => $saleRate,
                    'total'         => $total,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
                return (object)['purchase' => $total];
            };

            // Helper: asset line
            $addAsset = function (int $setId, int $assetId, string $name, int $count, float $totalPrice) use ($now) {
                DB::table('asset_sets')->insert([
                    'asset_id'    => $assetId,
                    'master_id'   => $setId,
                    'name'        => $name,
                    'count'       => $count,
                    'total_price' => $totalPrice,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
                return (object)['purchase' => $totalPrice];
            };

            // -------------------------------------------------------
            // Function: build a master set
            $buildSet = function (array $cfg) use (
                $agWp, $sagMono, $sagSplit, $phase,
                $addProduct, $addSubProduct, $addEmployee, $addAsset,$now
            ) {
                $setId = DB::table('product_master_sets')->insertGetId([
                    'setname'          => $cfg['name'],
                    'article_group'    => $agWp->id,
                    'sub_article'      => $cfg['split'] ? $sagSplit->id : $sagMono->id,
                    'phase_id'         => $phase->id,
                    'status'           => 'Published',
                    'price'            => 0,
                    'employee_price'   => 0,
                    'material_price'   => 0,
                    'asset_price'      => 0,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);

                $mat = 0; $lohn = 0; $tools = 0;

                $main = $addProduct($setId, $cfg['wp'], $cfg['wp_dist_id']);
                if ($main) $mat += $main->purchase;

                if ($cfg['speicher']) {
                    $ln = $addProduct($setId, $cfg['speicher'], $cfg['speicher_dist_id']);
                    if ($ln) $mat += $ln->purchase;
                }

                if ($cfg['puffer']) {
                    $ln = $addProduct($setId, $cfg['puffer'], $cfg['puffer_dist_id']);
                    if ($ln) $mat += $ln->purchase;
                }

                if ($cfg['ctrl'] && $main) {
                    $sn = $addSubProduct($setId, $main->line_id, $cfg['ctrl'], $cfg['ctrl_dist_id']);
                    if ($sn) $mat += $sn->purchase;
                }

                foreach ($cfg['employees'] as $e) {
                    $es = $addEmployee($setId, $e['position_id'], $e['hours'], $e['buy_rate'], $e['sale_rate'] ?? null);
                    if ($es) $lohn += $es->purchase;
                }

                foreach ($cfg['assets'] as $a) {
                    $as = $addAsset($setId, $a['asset_id'], $a['name'], $a['count'], $a['total_price']);
                    if ($as) $tools += $as->purchase;
                }

                DB::table('product_master_sets')->where('id', $setId)->update([
                    'material_price' => round($mat, 2),
                    'employee_price' => round($lohn, 2),
                    'asset_price'    => round($tools, 2),
                    'price'          => round($mat + $lohn + $tools, 2),
                    'updated_at'     => $now,
                ]);
            };

            // -------------------------------------------------------
            // Set definitions
            $rateMonteur = 40;
            $rateHelfer  = 38;
            $rateElektr  = 50;
            $rateIT      = 60;

            if ($wp8) {
                $buildSet([
                    'name' => 'WP 8 kW – Monoblock',
                    'split' => false,
                    'wp' => $wp8,
                    'wp_dist_id' => $distBeisp->id,
                    'speicher' => $sp200, 'speicher_dist_id' => $distHydra->id,
                    'puffer' => $puf50,   'puffer_dist_id'   => $distHydra->id,
                    'ctrl'   => $ctrl,    'ctrl_dist_id'     => $distSense->id,
                    'employees' => [
                        ['position_id' => $posMonteur->id, 'hours' => 30, 'buy_rate' => $rateMonteur],
                        ['position_id' => $posHelfer->id,  'hours' => 24, 'buy_rate' => $rateHelfer],
                        ['position_id' => $posElektr->id,  'hours' => 8,  'buy_rate' => $rateElektr],
                    ],
                    'assets' => [
                        ['asset_id' => $assetPress->id, 'name' => 'Rohrpressmaschine', 'count' => 1, 'total_price' => 120],
                        ['asset_id' => $assetVac->id,   'name' => 'Vakuumpumpe/Manometer', 'count' => 1, 'total_price' => 80],
                    ],
                ]);
            }

            if ($wp10) {
                $buildSet([
                    'name' => 'WP 10 kW – Monoblock',
                    'split' => false,
                    'wp' => $wp10,
                    'wp_dist_id' => $distAero->id,
                    'speicher' => $sp200, 'speicher_dist_id' => $distHydra->id,
                    'puffer' => $puf50,   'puffer_dist_id'   => $distHydra->id,
                    'ctrl'   => $ctrl,    'ctrl_dist_id'     => $distSense->id,
                    'employees' => [
                        ['position_id' => $posMonteur->id, 'hours' => 34, 'buy_rate' => $rateMonteur],
                        ['position_id' => $posHelfer->id,  'hours' => 26, 'buy_rate' => $rateHelfer],
                        ['position_id' => $posElektr->id,  'hours' => 10, 'buy_rate' => $rateElektr],
                    ],
                    'assets' => [
                        ['asset_id' => $assetPress->id, 'name' => 'Rohrpressmaschine', 'count' => 1, 'total_price' => 140],
                        ['asset_id' => $assetVac->id,   'name' => 'Vakuumpumpe/Manometer', 'count' => 1, 'total_price' => 90],
                    ],
                ]);
            }

            if ($wp12) {
                $buildSet([
                    'name' => 'WP 12 kW – Split 3~',
                    'split' => true,
                    'wp' => $wp12,
                    'wp_dist_id' => $distAero->id,
                    'speicher' => $sp200, 'speicher_dist_id' => $distHydra->id,
                    'puffer' => $puf50,   'puffer_dist_id'   => $distHydra->id,
                    'ctrl'   => $ctrl,    'ctrl_dist_id'     => $distSense->id,
                    'employees' => [
                        ['position_id' => $posMonteur->id, 'hours' => 38, 'buy_rate' => $rateMonteur],
                        ['position_id' => $posHelfer->id,  'hours' => 28, 'buy_rate' => $rateHelfer],
                        ['position_id' => $posElektr->id,  'hours' => 12, 'buy_rate' => $rateElektr],
                        ['position_id' => $posIT->id,      'hours' => 4,  'buy_rate' => $rateIT],
                    ],
                    'assets' => [
                        ['asset_id' => $assetPress->id, 'name' => 'Rohrpressmaschine', 'count' => 1, 'total_price' => 160],
                        ['asset_id' => $assetLift->id,  'name' => 'Baustellenlift',    'count' => 1, 'total_price' => 180],
                        ['asset_id' => $assetVac->id,   'name' => 'Vakuumpumpe/Manometer', 'count' => 1, 'total_price' => 100],
                    ],
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
