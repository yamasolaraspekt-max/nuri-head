<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * wberechnung-Katalog-Import (Cut-over Stufe 2) — 19 Wärmepumpen + 5 PV-Module (AIKO 2 + LONGi LR7 3).
 *
 * Netto-Neuwert: importiert nur Zeilen, die ticket noch nicht hat. Herkunfts-Marker
 * products.imported_from='wberechnung' (zeilengenau reversibel via WberechnungImportTeardownSeeder).
 * Dedup auf (brand_id, model): vorhandenes Modell => SKIP (nie update, Bestand unberührt) => idempotent.
 *
 * Daten eingefroren in database/data/wberechnung_import.php (Quelle wb@b4a9eda, datenblatt-verifiziert;
 * kein Live-Zugriff auf die wberechnung-DB). WP: leistungskurve=null + kurve_semantik='en14511_nenn'
 * (Spalten-Fallback des WpKennlinieService, byte-unberührte künftige B2-Port-Quelle).
 *
 * Diese Stufe läuft NUR gegen ticket_testing; produktive main-Ausführung = W3/M5 (separat freizugeben).
 */
class WberechnungImportSeeder extends Seeder
{
    public const MARKER = 'wberechnung';

    public function run(): void
    {
        $data = require database_path('data/wberechnung_import.php');
        $now = now();
        $brandStatus = DB::table('brands')->whereNotNull('status')->value('status') ?? 'active';
        $agWp = (string) $this->group('Wärmepumpe', $now);
        $agPv = (string) $this->group('Photovoltaik', $now);

        $wpCount = 0;
        foreach ($data['wp'] as $wp) {
            $brandId = $this->brand($wp['hersteller'], $brandStatus, $now);
            if ($this->exists($brandId, $wp['modell'])) {
                continue; // Dedup-Skip — Bestand nie überschreiben
            }
            $pid = DB::table('products')->insertGetId([
                'brand_id' => $brandId,
                'article_group' => $agWp,
                'product' => $wp['hersteller'].' '.$wp['modell'],
                'model' => $wp['modell'],
                'category' => 'Wärmepumpe',
                'heatpump_type' => $wp['geraetetyp'],
                'refrigerant' => $wp['kaeltemittel'],
                'phase_count' => $wp['phasen'] !== null ? (int) $wp['phasen'] : null,
                'scop' => $this->f($wp['scop_35']),
                'noise_level_db' => $this->f($wp['schall_volllast_db']),
                'short_description' => $wp['serie'].' — '.$wp['quelle'],
                'imported_from' => self::MARKER,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            DB::table('product_heat_pump_specs')->insert([
                'product_id' => $pid,
                'leistungskurve' => null,
                'kurve_semantik' => $wp['kurve_semantik'],
                'heizleistung_am7_w35_kw' => $this->f($wp['heizleistung_am7_w35_kw']),
                'heizleistung_a2_w35_kw' => $this->f($wp['heizleistung_a2_w35_kw']),
                'heizleistung_a7_w35_kw' => $this->f($wp['heizleistung_a7_w35_kw']),
                'heizleistung_am7_w55_kw' => $this->f($wp['heizleistung_am7_w55_kw']),
                'cop_am7_w35' => $this->f($wp['cop_am7_w35']),
                'cop_a2_w35' => $this->f($wp['cop_a2_w35']),
                'cop_a7_w35' => $this->f($wp['cop_a7_w35']),
                'cop_am7_w55' => $this->f($wp['cop_am7_w55']),
                'scop_35' => $this->f($wp['scop_35']),
                'scop_55' => $this->f($wp['scop_55']),
                'max_vorlauf_c' => $this->f($wp['max_vorlauf_c']),
                'aussen_heizen_min_c' => $this->f($wp['aussen_heizen_min_c']),
                'modulation_min_kw' => $this->f($wp['modulation_min_kw']),
                'modulation_max_kw' => $this->f($wp['modulation_max_kw']),
                'geraetetyp' => $wp['geraetetyp'],
                'serie' => $wp['serie'],
                'kaeltemittel' => $wp['kaeltemittel'],
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $wpCount++;
        }

        $pvCount = 0;
        foreach ($data['pv'] as $pv) {
            $brandId = $this->brand($pv['hersteller'], $brandStatus, $now);
            if ($this->exists($brandId, $pv['modell'])) {
                continue;
            }
            $pid = DB::table('products')->insertGetId([
                'brand_id' => $brandId,
                'article_group' => $agPv,
                'product' => $pv['hersteller'].' '.$pv['serie'].' '.$pv['pmpp_wp'].' Wp',
                'model' => $pv['modell'],
                'category' => 'PV-Modul',
                'short_description' => $pv['serie'].' — '.$pv['pmpp_wp'].' Wp',
                'imported_from' => self::MARKER,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            DB::table('product_pv_module_specs')->insert([
                'product_id' => $pid,
                'pmpp_wp' => $this->f($pv['pmpp_wp']),
                'voc_v' => $this->f($pv['voc_v']),
                'vmpp_v' => $this->f($pv['vmpp_v']),
                'isc_a' => $this->f($pv['isc_a']),
                'impp_a' => $this->f($pv['impp_a']),
                'tk_voc_pct_k' => $this->f($pv['tk_voc_pct_k']),
                'tk_isc_pct_k' => $this->f($pv['tk_isc_pct_k']),
                'tk_pmpp_pct_k' => $this->f($pv['tk_pmpp_pct_k']),
                'tk_vmpp_pct_k' => $this->f($pv['tk_vmpp_pct_k']),
                'u_sys_max_v' => $this->f($pv['u_sys_max_v']),
                'sicherung_max_a' => $this->f($pv['sicherung_max_a']),
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $pvCount++;
        }

        $this->command?->info("wberechnung-Import: WP +{$wpCount}, PV +{$pvCount} (Marker=".self::MARKER.', Dedup-Skip aktiv).');
    }

    private function f($v): ?float
    {
        return ($v === null || $v === '') ? null : (float) $v;
    }

    private function brand(string $name, string $status, $now): int
    {
        return DB::table('brands')->where('name', $name)->value('id')
            ?? DB::table('brands')->insertGetId(['name' => $name, 'status' => $status, 'created_at' => $now, 'updated_at' => $now]);
    }

    private function group(string $name, $now): int
    {
        return DB::table('article_groups')->where('article_group', $name)->value('id')
            ?? DB::table('article_groups')->insertGetId(['article_group' => $name, 'created_at' => $now, 'updated_at' => $now]);
    }

    private function exists(int $brandId, string $model): bool
    {
        return DB::table('products')->where('brand_id', $brandId)->where('model', $model)->exists();
    }
}
