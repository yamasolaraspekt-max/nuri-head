<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DEMO-Assets / Inventar: Maschinen, Fahrzeuge, Werkzeuge und Mietobjekte (assets) sowie
 * Fahrzeuge/Maschinen mit Details (machines: Baujahr, Motor, km, TÜV).
 *
 * Idempotent (Reset über Marker: assets.article_no 'INV-%', machines.serial_no 'MCH-%').
 * Nur frei erfundene Daten. Setzt die Niederlassung (DemoCompanyMasterDataSeeder) voraus.
 */
class DemoAssetsSeeder extends Seeder
{
    public function run(): void
    {
        $now  = Carbon::now();
        $pick = fn(array $a) => $a[array_rand($a)];

        $branchId = DB::table('branches')->where('slug', 'solar-aspekt-nord')->value('id');
        $depIds   = DB::table('departments')->where('branch_id', $branchId)->pluck('id')->all();
        if (! $branchId) {
            $this->command->warn('Niederlassung fehlt — bitte zuerst DemoCompanyMasterDataSeeder ausführen.');
            return;
        }

        // ── Reset ──────────────────────────────────────────────────────
        DB::table('assets')->where('article_no', 'like', 'INV-%')->delete();
        DB::table('machines')->where('serial_no', 'like', 'MCH-%')->delete();

        // ── Assets (Inventar) ──────────────────────────────────────────
        // [Kategorie, [Bezeichnung, Modell] ..., purchase_type, Preis von, bis]
        $blocks = [
            ['Maschine', 'Kauf', [
                ['Minibagger', 'Kubota KX019-4'], ['Radlader', 'Weidemann 1160'], ['Hebebühne', 'Genie GS-1932'],
                ['Kernbohrgerät', 'Hilti DD 150-U'], ['Plattenverdichter', 'Wacker DPU 3050'], ['Stromaggregat', 'Honda EU22i'],
            ], 1500, 35000],
            ['Fahrzeug', 'Leasing', [
                ['Transporter', 'Mercedes Sprinter 316'], ['Pritschenwagen', 'VW Crafter'], ['Montagebus', 'Ford Transit'],
                ['PKW Kombi', 'Skoda Octavia'],
            ], 25000, 55000],
            ['Werkzeug', 'Kauf', [
                ['Akkuschrauber-Set', 'Makita 18V'], ['Messgerät-Koffer', 'Fluke 1664'], ['Werkzeugwagen', 'Hazet 179'],
                ['Leiter 3-teilig', 'Hailo ProfiStep'],
            ], 200, 4000],
            ['Mietobjekt', 'Miete', [
                ['Baugerüst 100m²', 'Layher Blitz'], ['Materialcontainer', 'CTX 20-Fuß'], ['Bauzaun-Set', 'Heras M200'],
                ['Toilettenkabine', 'TOI TOI Fresh'],
            ], 0, 0],
        ];
        $locations = ['Zentrallager Hamburg', 'Baustelle / Kunde', 'Werkstatt', 'Fuhrpark'];
        $statusOpts = ['verfügbar', 'in Nutzung', 'in Wartung'];
        $nA = 0; $no = 1;

        foreach ($blocks as $b) {
            [$cat, $ptype, $items, $min, $max] = $b;
            foreach ($items as $it) {
                $isMiete = ($ptype !== 'Kauf');
                $row = [
                    'serial_no' => 'SN-' . strtoupper(substr(md5($it[1] . $no), 0, 8)), 'article_no' => 'INV-' . str_pad((string) $no, 4, '0', STR_PAD_LEFT),
                    'item' => $it[0], 'model' => $it[1], 'category' => $cat, 'purchase_type' => $ptype,
                    'purchase_price' => $cat === 'Mietobjekt' ? 0 : rand($min, $max), 'quantity' => $cat === 'Werkzeug' ? rand(1, 8) : 1,
                    'location' => $pick($locations), 'used_for' => null, // FK-Spalte (int), kein Text
                    'description' => $cat . ' – Demo-Inventar', 'status' => $pick($statusOpts), 'branch_id' => $branchId,
                    'purchase_date' => $now->copy()->subDays(rand(60, 1500))->toDateString(),
                    'created_at' => $now, 'updated_at' => $now,
                ];
                if ($isMiete) {
                    $row['leasing_from'] = $cat === 'Fahrzeug' ? 'Leasingbank' : 'Vermieter Nord';
                    $row['leasing_date'] = $now->copy()->subDays(rand(30, 400))->toDateString();
                    $row['leasing_end_date'] = $now->copy()->addDays(rand(60, 700))->toDateString();
                    $row['leasing_price'] = rand(80, 900);
                }
                DB::table('assets')->insert($row);
                $no++; $nA++;
            }
        }

        // ── Fahrzeuge/Maschinen mit Details (machines) ─────────────────
        $vehicles = [
            ['Mercedes Sprinter', '316 CDI', 2022, 'weiß', 'Diesel', 48000],
            ['VW Crafter', '35 TDI', 2021, 'silber', 'Diesel', 72000],
            ['Ford Transit', 'Custom', 2023, 'blau', 'Diesel', 21000],
            ['Skoda Octavia', 'Combi 2.0 TDI', 2020, 'grau', 'Diesel', 95000],
            ['Kubota Minibagger', 'KX019-4', 2019, 'orange', 'Diesel', 1200],
            ['Weidemann Radlader', '1160', 2021, 'grün', 'Diesel', 800],
        ];
        $nM = 0; $mno = 1;
        foreach ($vehicles as $v) {
            DB::table('machines')->insert([
                'name' => $v[0], 'model' => $v[1], 'year' => $v[2], 'color' => $v[3], 'engine_type' => $v[4], 'mileage' => $v[5],
                'serial_no' => 'MCH-' . str_pad((string) $mno, 4, '0', STR_PAD_LEFT), 'purchase_type' => $mno <= 4 ? 'Leasing' : 'Kauf',
                'purchase_price' => rand(20000, 55000), 'purchase_date' => $now->copy()->subYears(rand(1, 5))->toDateString(),
                'last_service_date' => $now->copy()->subDays(rand(20, 300))->toDateString(),
                'technical_inspection_date' => $now->copy()->addDays(rand(30, 600))->toDateString(),
                'branch_id' => $branchId, 'department_id' => $depIds ? $pick($depIds) : null,
                'owner_name' => 'Solar Aspekt Nord GmbH', 'status' => 'active',
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $mno++; $nM++;
        }

        $this->command->info("Assets/Inventar: {$nA} Assets (Maschinen/Fahrzeuge/Werkzeuge/Mietobjekte) + {$nM} Fahrzeuge/Maschinen (machines, mit Bj./Motor/km/TÜV).");
    }
}
