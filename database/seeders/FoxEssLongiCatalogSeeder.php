<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Fox-ESS + LONGi Real-Geräte in den Produktkatalog (PRODUKTIVE main-DB).
 *
 * Muster: 1 products-Zeile + 1 Detailzeile (inverters/batteries) bzw. product_pv_module_specs;
 * EPS-Box = reines Zubehör-Produkt ohne Detailzeile. Idempotent (upsert auf Marke+Modell bzw.
 * product_id). Herkunfts-Marker (Haus-Konvention seit 150006): products.imported_from='fox-longi-seed'
 * — der maßgebliche Marker, über den der Teardown ausschließlich löscht. Zusätzlich version='fox-longi-seed'
 * auf inverters/batteries (Detail-Tabellen ohne imported_from-Spalte; Fallback/Auffindbarkeit). pv_specs
 * trägt keinen Marker -> im Teardown über product_id der markierten products entfernt. NIE über brand_id/
 * Gruppen zurückbauen (Marken/Gruppen sind mehrbesitzt — z. B. LONGi trägt auch wberechnung-Module).
 *
 * NUR datenblatt-wörtliche Werte. Bewusste Lücken (bleiben null): battery_max_charge_power (nur Ströme
 * im DB), Ah-Kapazität + num_cells (Batterien), efficiency_* (DB gibt nur max. Wirkungsgrad -> description),
 * EPS-Umschaltzeit. H3 PRO: Nennleistung ist im DB als VA deklariert (nicht W); Zahlenwert übernommen,
 * Einheit in description vermerkt. Quellen: docs/fox-ess-longi-katalog-erfassung.md.
 */
class FoxEssLongiCatalogSeeder extends Seeder
{
    public const MARKER = 'fox-longi-seed';

    public function run(): void
    {
        $now = now();
        $brandStatus = DB::table('brands')->whereNotNull('status')->value('status') ?? 'active';

        $foxId = $this->brand('Fox ESS', $brandStatus, $now);
        $longiId = $this->brand('LONGi', $brandStatus, $now);
        $agWR = $this->group('Wechselrichter', $now);
        $agZub = $this->group('Zubehör', $now);
        $agBatt = 7; // Batteriespeicher (bestehend)
        $agPV = 6; // Photovoltaik (bestehend)

        // ---------- Fox ESS H3 Smart (7) ----------
        $smartCommon = [
            'company' => 'Fox ESS', 'ac_nominal_voltage' => 400, 'num_phases' => 3, 'num_mpp_trackers' => 3,
            'min_mpp_voltage' => 120, 'max_mpp_voltage' => 950, 'max_input_current_per_mpp' => 20,
            'max_short_circuit_current_per_mpp' => 25, 'max_input_power_per_mpp' => 10000,
            'max_input_voltage' => 1000, 'dc_operating_max_voltage' => 950, 'dc_startup_voltage' => 140,
            'is_hybrid' => 1, 'eps_capable' => 1, 'battery_min_voltage' => 100, 'battery_max_voltage' => 800,
            'battery_max_current_a' => 50, 'operating_temp_min_c' => -25, 'operating_temp_max_c' => 60,
            'temp_derating_from_c' => 45, 'version' => self::MARKER,
        ];
        // [model, ac_nominal_power(W), max_ac_power(VA), max_array_power_wp, max_dc_power(W), maxEff]
        $smart = [
            ['H3-5.0-Smart', 5000, 5500, 11000, 11000, 97.30],
            ['H3-6.0-Smart', 6000, 6600, 14000, 13200, 97.70],
            ['H3-8.0-Smart', 8000, 8800, 18000, 17600, 97.70],
            ['H3-9.9-Smart', 9900, 9900, 20000, 18000, 97.90],
            ['H3-10.0-Smart', 10000, 11000, 20000, 18000, 97.90],
            ['H3-12.0-Smart', 12000, 13200, 24000, 22500, 97.90],
            ['H3-15.0-Smart', 15000, 16500, 30000, 22500, 97.90],
        ];
        foreach ($smart as [$model, $acP, $maxAc, $arr, $maxDc, $eff]) {
            $belg = $model === 'H3-10.0-Smart' ? ' (Belgien 10.000 VA)' : '';
            $desc = "Fox ESS {$model} – 3-phasiger Hybrid-Wechselrichter, {$acP} W / {$maxAc} VA{$belg}, "
                .'3 MPPT 120–950 V, Batterie 100–800 V (max. 50 A), EPS/Ersatzstrom <20 ms. '
                ."Max. Wirkungsgrad {$eff} %. Maße 600×450×226 mm, 34 kg.";
            $pid = $this->product([
                'brand_id' => $foxId, 'article_group' => (string) $agWR, 'product' => "Fox ESS {$model}",
                'model' => $model, 'description' => $desc,
            ], $now);
            $this->detail('inverters', $pid, $agWR, $smartCommon + [
                'name' => $model, 'ac_nominal_power' => $acP, 'max_ac_power' => $maxAc,
                'max_array_power_wp' => $arr, 'max_dc_power' => $maxDc, 'description' => $desc,
            ], $now);
        }

        // ---------- Fox ESS H3 PRO (6) ----------
        $proCommon = [
            'company' => 'Fox ESS', 'ac_nominal_voltage' => 400, 'num_phases' => 3, 'num_mpp_trackers' => 3,
            'min_mpp_voltage' => 150, 'max_mpp_voltage' => 850, 'max_input_current_per_mpp' => 16,
            'max_short_circuit_current_per_mpp' => 20, 'max_input_voltage' => 1000,
            'dc_operating_max_voltage' => 950, 'dc_startup_voltage' => 160, 'is_hybrid' => 1, 'eps_capable' => 1,
            'battery_min_voltage' => 150, 'battery_max_voltage' => 800, 'battery_max_current_a' => 50,
            'operating_temp_min_c' => -25, 'operating_temp_max_c' => 60, 'temp_derating_from_c' => 45,
            'version' => self::MARKER,
        ];
        // [model, nominal(VA), max_ac(VA), max_array_power_wp, max_input_power_per_mpp(W je MPP), maxEff]
        $pro = [
            ['H3-Pro-15.0', 15000, 16500, 30000, 7500, 97.20],
            ['H3-Pro-20.0', 20000, 22000, 40000, 10000, 97.20],
            ['H3-Pro-24.9', 24900, 24900, 50000, 12500, 97.10],
            ['H3-Pro-25.0', 25000, 27500, 50000, 12500, 97.10],
            ['H3-Pro-29.9', 29900, 29900, 60000, 15000, 97.10],
            ['H3-Pro-30.0', 30000, 33000, 60000, 15000, 97.10],
        ];
        foreach ($pro as [$model, $nomVa, $maxAc, $arr, $mppW, $eff]) {
            $desc = "Fox ESS {$model} – 3-phasiger Hybrid-Wechselrichter, Nennleistung {$nomVa} VA "
                ."(Datenblatt deklariert VA, nicht W) / max. {$maxAc} VA, 3 MPPT 150–850 V "
                .'(2 Strings/MPP, je 16 A / KS 20 A), 2 Batterie-Eingänge (je max. 50 A) 150–800 V, '
                ."EPS <10 ms. Max. Wirkungsgrad {$eff} %. Maße 600×560×225 mm, 52,5 kg.";
            $pid = $this->product([
                'brand_id' => $foxId, 'article_group' => (string) $agWR, 'product' => "Fox ESS {$model}",
                'model' => $model, 'description' => $desc,
            ], $now);
            $this->detail('inverters', $pid, $agWR, $proCommon + [
                'name' => $model, 'ac_nominal_power' => $nomVa, 'max_ac_power' => $maxAc,
                'max_array_power_wp' => $arr, 'max_input_power_per_mpp' => $mppW, 'description' => $desc,
            ], $now);
        }

        // ---------- Fox ESS Batterien EK6 / EK12 (LFP-Hochvolt) ----------
        // [model, kWh, nomV, minV, maxV, weight, width(W), height(H), length(D=Tiefe), maxKWh]
        $bat = [
            ['EK6', '5,76', 192, 174, 219, 51, 380, 640, 185, '23,04'],
            ['EK12', '11,52', 384, 348, 438, 98, 710, 640, 185, '46,08'],
        ];
        foreach ($bat as [$model, $kwh, $nomV, $minV, $maxV, $wt, $w, $h, $d, $maxKwh]) {
            $desc = "Fox ESS {$model} – LFP-Hochvolt-Speicher, {$kwh} kWh nutzbar, {$nomV} V "
                ."({$minV}–{$maxV} V), max. Lade-/Entladestrom 30 A (Empf. 15 A, Peak 65 A@60 s), "
                ."DOD 90 %, ≥95 % Round-Trip, skalierbar bis 4 Module = {$maxKwh} kWh. "
                ."Maße {$w}×{$h}×{$d} mm (B×H×T), {$wt} kg, IP65, Laden 0–55 °C / Entladen −10–55 °C.";
            $pid = $this->product([
                'brand_id' => $foxId, 'article_group' => (string) $agBatt, 'product' => "Fox ESS {$model}",
                'model' => $model, 'description' => $desc,
            ], $now);
            $this->detail('batteries', $pid, $agBatt, [
                'company' => 'Fox ESS', 'name' => $model, 'battery_type' => 'LFP',
                'nominal_voltage' => $nomV, 'min_voltage' => $minV, 'max_voltage' => $maxV,
                'max_current_a' => 30, 'width' => $w, 'height' => $h, 'length' => $d, 'weight' => $wt,
                'version' => self::MARKER, 'description' => $desc,
            ], $now);
        }

        // ---------- Fox ESS EPS BOX PRO (Zubehör, nur products) ----------
        $epsDesc = 'Fox ESS EPS BOX PRO – 3-phasige Notstrom-Umschaltbox (ATS), 63 A/Phase, '
            .'220/380–230/400 V 3L/N/PE, 50/60 Hz, IP65, Maße 550×420×138,5 mm, 16 kg, Betrieb −25–60 °C. '
            .'Umschaltzeit gemäß Wechselrichter (H3 Smart <20 ms / H3 PRO <10 ms; im EPS-Datenblatt nicht separat).';
        $this->product([
            'brand_id' => $foxId, 'article_group' => (string) $agZub, 'product' => 'Fox ESS EPS BOX PRO',
            'model' => 'EPS BOX PRO', 'description' => $epsDesc,
        ], $now);

        // ---------- LONGi PV-Module ----------
        // X10 (all-black = silber, elektrisch identisch -> 1 Zeile/Klasse). Common-TK: pmpp -0.260, voc -0.200, isc 0.050
        // [modelcode, produktname, color, pmpp, voc, vmpp, isc, impp, eff]
        $x10 = [
            ['LR7-54HVH-495M', 'LONGi Hi-MO X10 495 Wp (Silber/All-Black)', 'Silber / All-Black', 495, 40.64, 33.62, 15.43, 14.73, 24.3],
            ['LR7-54HVH-500M', 'LONGi Hi-MO X10 500 Wp (Silber)', 'Silber', 500, 40.75, 33.73, 15.53, 14.83, 24.5],
        ];
        foreach ($x10 as [$mc, $pname, $color, $p, $voc, $vmp, $isc, $imp, $eff]) {
            $ab = $p === 495 ? ' All-Black = LR7-54HVB-495M (elektrisch identisch).' : '';
            $desc = "LONGi Hi-MO X10, HPBC-2.0-Zelle, {$p} Wp, {$eff} %. 1800×1134×30 mm, 21,6 kg, "
                ."108 Halbzellen, Systemspg. 1500 V, Sicherung 25 A. Garantie 15 J Produkt / 30 J Leistung.{$ab}";
            $pid = $this->product([
                'brand_id' => $longiId, 'article_group' => (string) $agPV, 'product' => $pname,
                'model' => $mc, 'color' => $color, 'description' => $desc,
            ], $now);
            $this->pvSpec($pid, [
                'voc_v' => $voc, 'vmpp_v' => $vmp, 'isc_a' => $isc, 'impp_a' => $imp, 'pmpp_wp' => $p,
                'tk_voc_pct_k' => -0.200, 'tk_isc_pct_k' => 0.050, 'tk_pmpp_pct_k' => -0.260,
                'u_sys_max_v' => 1500, 'sicherung_max_a' => 25,
            ], $now);
        }

        // X6 Max (black HTB ≠ silber HTH -> getrennte Zeilen). Common-TK: pmpp -0.280, voc -0.230, isc 0.050
        // [modelcode, color-label, pmpp, voc, vmpp, isc, impp, eff]
        $x6 = [
            ['LR7-60HTB-500M', 'All-Black', 500, 43.50, 36.64, 14.63, 13.65, 22.2],
            ['LR7-60HTB-505M', 'All-Black', 505, 43.70, 36.84, 14.70, 13.71, 22.4],
            ['LR7-60HTB-510M', 'All-Black', 510, 43.90, 37.04, 14.76, 13.77, 22.6],
            ['LR7-60HTB-515M', 'All-Black', 515, 44.10, 37.24, 14.82, 13.83, 22.8],
            ['LR7-60HTB-520M', 'All-Black', 520, 44.30, 37.44, 14.88, 13.89, 23.0],
            ['LR7-60HTH-505M', 'Silber', 505, 43.50, 36.64, 14.77, 13.79, 22.4],
            ['LR7-60HTH-510M', 'Silber', 510, 43.70, 36.84, 14.84, 13.85, 22.6],
            ['LR7-60HTH-515M', 'Silber', 515, 43.90, 37.04, 14.89, 13.91, 22.8],
        ];
        foreach ($x6 as [$mc, $color, $p, $voc, $vmp, $isc, $imp, $eff]) {
            $desc = "LONGi Hi-MO X6 Max, HPBC-Zelle (Gen 1), {$p} Wp, {$eff} %. 1990×1134×30 mm, 24,8 kg, "
                ."120 Halbzellen, Systemspg. 1500 V, Sicherung 25 A. Ausführung {$color}.";
            $pid = $this->product([
                'brand_id' => $longiId, 'article_group' => (string) $agPV,
                'product' => "LONGi Hi-MO X6 Max {$p} Wp {$color}", 'model' => $mc, 'color' => $color,
                'description' => $desc,
            ], $now);
            $this->pvSpec($pid, [
                'voc_v' => $voc, 'vmpp_v' => $vmp, 'isc_a' => $isc, 'impp_a' => $imp, 'pmpp_wp' => $p,
                'tk_voc_pct_k' => -0.230, 'tk_isc_pct_k' => 0.050, 'tk_pmpp_pct_k' => -0.280,
                'u_sys_max_v' => 1500, 'sicherung_max_a' => 25,
            ], $now);
        }

        $this->command?->info('Fox-ESS/LONGi-Katalog geseedet (idempotent, Marker='.self::MARKER.').');
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

    /** upsert auf (brand_id, model); gibt product_id zurück. products hat 'short_description' (longtext), kein 'description'. */
    private function product(array $data, $now): int
    {
        if (isset($data['description'])) {
            $data['short_description'] = $data['description'];
            unset($data['description']);
        }
        $data['imported_from'] = self::MARKER; // Haus-Konvention (seit 150006); Teardown identifiziert hierüber
        $id = DB::table('products')->where('brand_id', $data['brand_id'])->where('model', $data['model'])->value('id');
        $data['updated_at'] = $now;
        if ($id) {
            DB::table('products')->where('id', $id)->update($data);

            return $id;
        }
        $data['created_at'] = $now;

        return DB::table('products')->insertGetId($data);
    }

    /** upsert Detailzeile (inverters|batteries) auf product_id. */
    private function detail(string $table, int $pid, int $agId, array $data, $now): void
    {
        $data['product_id'] = $pid;
        $data['article_group_id'] = $agId;
        $data['updated_at'] = $now;
        if (DB::table($table)->where('product_id', $pid)->exists()) {
            DB::table($table)->where('product_id', $pid)->update($data);
        } else {
            $data['created_at'] = $now;
            DB::table($table)->insert($data);
        }
    }

    /** upsert product_pv_module_specs auf product_id. */
    private function pvSpec(int $pid, array $data, $now): void
    {
        $data['product_id'] = $pid;
        $data['updated_at'] = $now;
        if (DB::table('product_pv_module_specs')->where('product_id', $pid)->exists()) {
            DB::table('product_pv_module_specs')->where('product_id', $pid)->update($data);
        } else {
            $data['created_at'] = $now;
            DB::table('product_pv_module_specs')->insert($data);
        }
    }
}
