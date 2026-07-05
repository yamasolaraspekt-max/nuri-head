<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * B2a-1: Referenz-Kataloge + Klima (Rechengrundlagen für den Heizlast-Kern), eingefroren aus
 * wberechnung@b4a9eda. materials (DIN 4108-4/ISO 10456) · konstruktionen (DIN EN ISO 6946, Schicht-
 * material_id von wb→ticket remapped) · baualtersklassen (IWU/TABULA-Richtwert) · klima_plz (DWD, 8168 PLZ).
 *
 * Haus-Konvention: imported_from='wberechnung' je Zeile · idempotent (skip-if-exists auf natürlichem
 * Schlüssel, nie update) · verifikations_status je Zeile (W-B2a-4, bis ins Rechenergebnis durchgereicht).
 * Rückbau: ReferenzKatalogTeardownSeeder. NUR lokal (Lokal-First; main = Yamas lokaler Hauptstand).
 */
class ReferenzKatalogSeeder extends Seeder
{
    public const MARKER = 'wberechnung';

    public function run(): void
    {
        $data = require database_path('data/b2a_referenz.php');
        $now = now();

        // ---- materials (verifikations_status=din_belegt) ----
        $wbIdToName = [];
        $nameToTicketId = [];
        $mCount = 0;
        foreach ($data['materials'] as $m) {
            $wbIdToName[$m['id']] = $m['name'];
            $tid = DB::table('materials')->where('name', $m['name'])->value('id');
            if ($tid === null) {
                $tid = DB::table('materials')->insertGetId([
                    'name' => $m['name'], 'kategorie' => $m['kategorie'], 'lambda_w_mk' => $m['lambda_w_mk'],
                    'rohdichte_kg_m3' => $m['rohdichte_kg_m3'], 'quelle' => $m['quelle'],
                    'verifikations_status' => 'din_belegt', 'imported_from' => self::MARKER,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $mCount++;
            }
            $nameToTicketId[$m['name']] = $tid;
        }

        // ---- konstruktionen (schichten.material_id wb→ticket) ----
        $kCount = 0;
        foreach ($data['konstruktionen'] as $k) {
            if (DB::table('konstruktionen')->where('name', $k['name'])->exists()) {
                continue;
            }
            $schichten = json_decode((string) $k['schichten'], true) ?: [];
            foreach ($schichten as &$s) {
                if (isset($s['material_id'])) {
                    $name = $wbIdToName[$s['material_id']] ?? null;
                    $s['material_id'] = $name !== null ? ($nameToTicketId[$name] ?? null) : null;
                }
            }
            unset($s);
            DB::table('konstruktionen')->insert([
                'name' => $k['name'], 'typ' => $k['typ'], 'schichten' => json_encode($schichten),
                'u_wert_berechnet' => $k['u_wert_berechnet'], 'quelle' => $k['quelle'], 'ist_vorlage' => $k['ist_vorlage'],
                'verifikations_status' => 'din_belegt', 'imported_from' => self::MARKER,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $kCount++;
        }

        // ---- baualtersklassen (TABULA-Richtwert, Stichprobe-Verifikation offen bis TABULA-Quelle) ----
        $bCount = 0;
        foreach ($data['baualtersklassen'] as $b) {
            $exists = DB::table('baualtersklassen')
                ->where('von_jahr', $b['von_jahr'])->where('bis_jahr', $b['bis_jahr'])
                ->where('sanierungsstufe', $b['sanierungsstufe'])->exists();
            if ($exists) {
                continue;
            }
            DB::table('baualtersklassen')->insert([
                'von_jahr' => $b['von_jahr'], 'bis_jahr' => $b['bis_jahr'], 'sanierungsstufe' => $b['sanierungsstufe'],
                'u_wand' => $b['u_wand'], 'u_dach' => $b['u_dach'], 'u_boden' => $b['u_boden'],
                'u_fenster' => $b['u_fenster'], 'u_tuer' => $b['u_tuer'], 'quelle' => $b['quelle'],
                'verifikations_status' => 'tabula_richtwert', 'imported_from' => self::MARKER,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $bCount++;
        }

        // ---- klima_plz (DWD, 8168 PLZ; idempotent: skip wenn bereits geseedet) ----
        $pCount = 0;
        if (DB::table('klima_plz')->where('imported_from', self::MARKER)->doesntExist()) {
            $path = database_path('data/klima_plz.csv');
            $handle = fopen($path, 'r');
            $header = fgetcsv($handle);
            $batch = [];
            while (($row = fgetcsv($handle)) !== false) {
                if ($row === [null] || $row === false) {
                    continue;
                }
                $d = array_combine($header, $row);
                $batch[] = [
                    'plz' => $d['plz'], 'ort' => $d['ort'], 'lat' => $d['lat'], 'lon' => $d['lon'],
                    'nat_c' => $d['nat_c'], 'temp_mittel_c' => $d['temp_mittel_c'],
                    'hgt15_kd' => $d['hgt15_kd'] !== '' ? $d['hgt15_kd'] : null,
                    'vollbenutz_h' => $d['vollbenutz_h'] !== '' ? $d['vollbenutz_h'] : null,
                    'hoehe_m' => $d['hoehe_m'] !== '' ? $d['hoehe_m'] : null,
                    'imported_from' => self::MARKER, 'created_at' => $now, 'updated_at' => $now,
                ];
                if (count($batch) >= 500) {
                    DB::table('klima_plz')->insert($batch);
                    $pCount += count($batch);
                    $batch = [];
                }
            }
            if ($batch !== []) {
                DB::table('klima_plz')->insert($batch);
                $pCount += count($batch);
            }
            fclose($handle);
        }

        $this->command?->info("Referenz-Kataloge: materials +{$mCount}, konstruktionen +{$kCount}, baualtersklassen +{$bCount}, klima_plz +{$pCount} (Marker=".self::MARKER.').');
    }
}
