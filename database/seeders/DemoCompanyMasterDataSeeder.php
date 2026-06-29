<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * DEMO-Firma „Solar Aspekt Nord GmbH" — Stammdaten / Gerüst (Schritt 1+2).
 *
 * Idempotent: bestehende Demo-Belegschaft & -Struktur (@solar-aspekt-nord.test bzw. dieser
 * Niederlassung) werden zuerst restlos entfernt, dann frisch gesetzt. Nur frei erfundene Daten.
 * Reihenfolge: Niederlassung → Reset → Abteilungen (16) → Teams → Positionen → Stammdaten → 50 MA.
 */
class DemoCompanyMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ── Niederlassung ──────────────────────────────────────────────
        DB::table('branches')->updateOrInsert(
            ['slug' => 'solar-aspekt-nord'],
            ['branch' => 'Solar Aspekt Nord GmbH', 'initial' => 'SAN', 'color' => '#16a34a',
             'chairman' => 3, 'street' => 'Solarstraße 12', 'postcode' => '22045',
             'city' => 'Hamburg', 'country' => 'Deutschland', 'email' => 'info@solar-aspekt-nord.test',
             'phone' => '040 1234560', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]
        );
        $branchId = DB::table('branches')->where('slug', 'solar-aspekt-nord')->value('id');

        // ── Idempotenter Reset: alte Demo-Belegschaft & -Struktur dieser Niederlassung entfernen ──
        $demoEmpIds  = DB::table('employees')->where('email', 'like', '%@solar-aspekt-nord.test')->pluck('id')->all();
        $demoUserIds = DB::table('users')->where('email', 'like', '%@solar-aspekt-nord.test')->pluck('id')->all();
        if ($demoUserIds) {
            DB::table('user_rolls')->whereIn('user_id', $demoUserIds)->delete();
            DB::table('users')->whereIn('id', $demoUserIds)->delete();
        }
        if ($demoEmpIds) {
            DB::table('employee_departments')->whereIn('employee_id', $demoEmpIds)->delete();
            DB::table('department_positions')->whereIn('employee_id', $demoEmpIds)->delete();
            DB::table('employees')->whereIn('id', $demoEmpIds)->delete();
        }
        $oldDepIds = DB::table('departments')->where('branch_id', $branchId)->pluck('id')->all();
        if ($oldDepIds) {
            DB::table('teams')->whereIn('department_id', $oldDepIds)->delete();
            DB::table('employee_departments')->whereIn('department_id', $oldDepIds)->delete();
            DB::table('department_positions')->whereIn('department_id', $oldDepIds)->delete();
            DB::table('departments')->whereIn('id', $oldDepIds)->delete();
        }

        // ── Abteilungen (16, gewerkeorientiert) ────────────────────────
        $departments = [
            'Heizung', 'Elektro', 'SHK', 'Bauelemente', 'Schreiner', 'Dachdecker', 'Maler',
            'Fliesenleger', 'Baudekoration', 'Controlling', 'Marketing', 'Finanzen',
            'Buchhaltung', 'Verwaltung', 'Management', 'Geschäftsführung',
        ];
        $depIds = [];
        foreach ($departments as $i => $name) {
            DB::table('departments')->updateOrInsert(
                ['department_name' => $name, 'branch_id' => $branchId],
                ['order' => $i + 1, 'description' => 'Abteilung ' . $name . ' der Solar Aspekt Nord GmbH', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]
            );
            $depIds[$name] = DB::table('departments')->where('department_name', $name)->where('branch_id', $branchId)->value('id');
        }

        // ── Teams (eines je Abteilung) ─────────────────────────────────
        foreach ($depIds as $depName => $depId) {
            DB::table('teams')->updateOrInsert(
                ['slug' => Str::slug('Team ' . $depName)],
                ['name' => 'Team ' . $depName, 'department_id' => $depId, 'description' => 'Team ' . $depName, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // ── Qualifikationen / Funktionen (Rollen) + Positionen ─────────
        // [Qualifikation/Rolle, Funktionsbezeichnung, Stundensatz €]
        $roles = [
            ['Geschäftsführung', 'Geschäftsführer/in', 0],
            ['Management', 'Manager/in', 65],
            ['Meister', 'Meister/in', 70],
            ['Geselle', 'Geselle/in', 45],
            ['Helfer', 'Helfer/in', 30],
            ['Techniker', 'Techniker/in', 50],
            ['Planer', 'Planer/in', 52],
            ['Designer', 'Designer/in', 48],
            ['Controlling', 'Controller/in', 58],
            ['Außendienst', 'Außendienstmitarbeiter/in', 50],
            ['Innendienst', 'Innendienstmitarbeiter/in', 40],
            ['Buchhaltung', 'Buchhalter/in', 45],
            ['Marketing', 'Marketing-Manager/in', 48],
            ['Verwaltung', 'Sachbearbeiter/in', 38],
            ['Ausbildung', 'Azubi', 14],
        ];
        $qualId = [];
        foreach ($roles as $i => $r) {
            DB::table('position_qualifications')->updateOrInsert(
                ['name' => $r[0]],
                ['default_price' => $r[2], 'sort_order' => $i + 1, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]
            );
            $qualId[$r[0]] = DB::table('position_qualifications')->where('name', $r[0])->value('id');

            DB::table('positions')->updateOrInsert(
                ['position' => $r[1]],
                ['qualification_id' => $qualId[$r[0]], 'qualification' => $r[0], 'price' => $r[2],
                 'description' => $r[1], 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // ── Vertragstypen ──────────────────────────────────────────────
        foreach ([['Vollzeit unbefristet', 'unbefristet'], ['Vollzeit befristet', '24 Monate'], ['Teilzeit', 'unbefristet'], ['Minijob', '12 Monate'], ['Ausbildung', '36 Monate']] as $ct) {
            DB::table('contract_types')->updateOrInsert(['contract_type' => $ct[0]], ['contract_duration' => $ct[1], 'created_at' => $now, 'updated_at' => $now]);
        }

        // ── Produkte / Gewerke (article_groups) ────────────────────────
        // Alte Platzhalter-Gruppen entfernen, vollständige Produktpalette setzen.
        DB::table('article_groups')->whereIn('article_group', ['Photovoltaik-Anlage', 'Stromspeicher', 'Elektroinstallation'])->delete();
        $products = [
            ['Photovoltaik', 'PV'], ['Wärmepumpe', 'WP'], ['Batteriespeicher', 'BS'], ['Wallbox', 'WB'],
            ['Fenster', 'FE'], ['Türen', 'TR'], ['Badsanierung', 'BAD'], ['Küche', 'KU'],
            ['Fliesen', 'FL'], ['Dach', 'DA'], ['Insektenschutz', 'IS'], ['Fliegengitter', 'FG'], ['Tapete', 'TA'],
        ];
        foreach ($products as $ag) {
            DB::table('article_groups')->updateOrInsert(['article_group' => $ag[0]], ['initial' => $ag[1], 'min_value' => 0, 'max_value' => 0, 'created_at' => $now, 'updated_at' => $now]);
        }

        // ── Dienstleistungen (inquiry_types) ───────────────────────────
        foreach (['Komplettlösung', 'Verkauf', 'Montage', 'Planung', 'Reparatur', 'Reklamation', 'Wartung', 'Sonstiges'] as $svc) {
            DB::table('inquiry_types')->updateOrInsert(['type' => $svc], ['created_at' => $now, 'updated_at' => $now]);
        }

        // ── Rechte-Items: Sidebar-Sektionen + Spezial-Items ────────────
        foreach (['Customer', 'Email', 'Employee', 'Finance', 'Inquiry', 'Organization', 'Partner', 'Problem', 'Product', 'Users', 'Programmer', 'Administrator', 'Super', 'Invoice'] as $item) {
            DB::table('user_roll_items')->updateOrInsert(['item' => $item], ['created_at' => $now, 'updated_at' => $now]);
        }

        // ── Sprachen / Länder ──────────────────────────────────────────
        foreach (['Deutsch', 'Englisch', 'Türkisch', 'Polnisch', 'Arabisch'] as $lang) {
            DB::table('languages')->updateOrInsert(['language' => $lang], ['created_at' => $now, 'updated_at' => $now]);
        }
        foreach ([['Deutschland', 'deutsch'], ['Türkei', 'türkisch'], ['Polen', 'polnisch']] as $c) {
            DB::table('countries')->updateOrInsert(['country' => $c[0]], ['nationality' => $c[1], 'created_at' => $now, 'updated_at' => $now]);
        }

        // ── Urlaubsanspruch / Arbeitsorte ──────────────────────────────
        DB::table('leave_days')->updateOrInsert(['year' => (int) $now->year], ['leave_day' => 30, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]);
        foreach ([['Büro Hamburg', 'branch'], ['Zentrallager', 'warehouse'], ['Baustelle / Kunde', 'customer']] as $wp) {
            DB::table('daily_report_work_places')->updateOrInsert(['place_name' => $wp[0]], ['type' => $wp[1], 'branch_id' => null, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]);
        }

        // ── Belegschaft (50 Personen) ──────────────────────────────────
        $count = $this->seedDemoProfiles($branchId, $depIds, $qualId, $now);

        $this->command->info('Stammdaten gesetzt: 1 Niederlassung, ' . count($departments) . ' Abteilungen, ' . count($depIds) . ' Teams, ' . count($roles) . ' Rollen/Positionen + ' . $count . ' Mitarbeiter (Belegschaft mit Rollen, Vorgesetzten & Fotos).');
    }

    /**
     * Demo-Belegschaft (50 Personen): employees + users + user_rolls + Vorgesetzte + Profilfoto.
     * Verdrahtung exakt nach Code: users.name = (string) employees.id; user_rolls.user_id = users.id;
     * item_id = Item-Name (String); Flags tinyint (1/0). Passwort aller Konten: "demo1234".
     * 3 feste Login-Profile für die Rechte-Verifikation (Stufen 0/1/4):
     *   A Hoffmann  = Geschäftsführer, is_admin=1 (Bypass)
     *   B Neumann   = Leitung Buchhaltung, alle Items/Flags=1 (Voll, Nicht-Admin)
     *   C Wagner    = Elektrogeselle, keine user_rolls (gesperrt)
     * Vorgesetzte: jede Abteilung hat einen Kopf (Meister/Leitung); dessen supervisor = Geschäftsführer.
     */
    private function seedDemoProfiles(int $branchId, array $depIds, array $qualId, $now): int
    {
        $contractTypeId = DB::table('contract_types')->where('contract_type', 'Vollzeit unbefristet')->value('id');

        $allItems = ['Customer', 'Email', 'Employee', 'Finance', 'Inquiry', 'Organization', 'Partner', 'Problem', 'Product', 'Users', 'Programmer', 'Administrator', 'Super', 'Invoice'];
        $areaItems = [
            'Management'       => ['Customer', 'Inquiry', 'Partner', 'Employee'],
            'Controlling'      => ['Finance', 'Employee'],
            'Marketing'        => ['Customer', 'Partner'],
            'Finanzen'         => ['Finance', 'Invoice'],
            'Buchhaltung'      => ['Finance', 'Invoice', 'Employee'],
            'Verwaltung'       => ['Employee', 'Organization'],
            'Heizung'          => ['Product', 'Problem'],
            'Elektro'          => ['Product', 'Problem'],
            'SHK'              => ['Product', 'Problem'],
            'Bauelemente'      => ['Product', 'Problem'],
            'Schreiner'        => ['Product', 'Problem'],
            'Dachdecker'       => ['Product', 'Problem'],
            'Maler'            => ['Product', 'Problem'],
            'Fliesenleger'     => ['Product', 'Problem'],
            'Baudekoration'    => ['Product', 'Problem'],
            'Geschäftsführung' => [],
        ];
        $crossReadItems = ['Customer', 'Product'];
        $tradeDeps = ['Heizung', 'Elektro', 'SHK', 'Bauelemente', 'Schreiner', 'Dachdecker', 'Maler', 'Fliesenleger', 'Baudekoration'];

        $funktion = [
            'Geschäftsführung' => 'Geschäftsführer/in', 'Management' => 'Manager/in', 'Meister' => 'Meister/in',
            'Geselle' => 'Geselle/in', 'Helfer' => 'Helfer/in', 'Techniker' => 'Techniker/in', 'Planer' => 'Planer/in',
            'Designer' => 'Designer/in', 'Controlling' => 'Controller/in', 'Außendienst' => 'Außendienstmitarbeiter/in',
            'Innendienst' => 'Innendienstmitarbeiter/in', 'Buchhaltung' => 'Buchhalter/in', 'Marketing' => 'Marketing-Manager/in',
            'Verwaltung' => 'Sachbearbeiter/in', 'Ausbildung' => 'Azubi',
        ];

        // Namens-Pools (frei erfunden; ohne Hoffmann/Neumann/Wagner -> keine E-Mail-Kollision mit A/B/C).
        $men = ['Lukas', 'Jonas', 'Leon', 'Finn', 'Paul', 'Felix', 'Max', 'Tim', 'Jan', 'Niklas', 'Tom', 'David', 'Erik', 'Marco', 'Sven', 'Jens', 'Dirk', 'Ralf', 'Uwe', 'Olaf', 'Malte', 'Carsten', 'Hauke', 'Bjarne'];
        $women = ['Mia', 'Emma', 'Hannah', 'Lea', 'Lina', 'Sophie', 'Marie', 'Laura', 'Sarah', 'Nina', 'Katrin', 'Britta', 'Silke', 'Anke', 'Maren', 'Frauke', 'Insa', 'Wiebke', 'Imke', 'Birte'];
        $last = ['Schmidt', 'Meyer', 'Schulz', 'Becker', 'Koch', 'Bauer', 'Richter', 'Klein', 'Wolf', 'Schröder', 'Braun', 'Werner', 'Krause', 'Lehmann', 'Köhler', 'Hermann', 'Walter', 'König', 'Mayer', 'Huber', 'Kaiser', 'Fuchs', 'Peters', 'Möller', 'Weiß', 'Jung', 'Hahn', 'Vogel', 'Friedrich', 'Keller', 'Günther', 'Frank', 'Berger', 'Winkler', 'Roth', 'Beck', 'Lorenz', 'Baumann', 'Franke', 'Albrecht', 'Ludwig', 'Winter', 'Kraus', 'Schumacher', 'Krämer', 'Vogt', 'Stein', 'Brandt', 'Sauer', 'Arnold'];
        $mi = 0; $wi = 0; $li = 0; $pnMen = 40; $pnWomen = 50;

        // Generierungsplan je Abteilung: [Rolle, Stufe, head]. Feste Profile A/B/C separat eingehängt.
        $gen = [
            'Management'    => [['Management', 2, true], ['Planer', 3, false], ['Außendienst', 3, false], ['Innendienst', 4, false]],
            'Controlling'   => [['Controlling', 2, true], ['Controlling', 3, false]],
            'Marketing'     => [['Marketing', 2, true], ['Designer', 3, false], ['Designer', 4, false], ['Innendienst', 4, false]],
            'Finanzen'      => [['Buchhaltung', 2, true], ['Controlling', 3, false]],
            'Buchhaltung'   => [['Buchhaltung', 3, false], ['Verwaltung', 3, false]],
            'Verwaltung'    => [['Verwaltung', 2, true], ['Innendienst', 3, false], ['Innendienst', 4, false], ['Ausbildung', 4, false]],
            'Heizung'       => [['Meister', 2, true], ['Geselle', 4, false], ['Geselle', 4, false], ['Helfer', 4, false]],
            'Elektro'       => [['Meister', 2, true], ['Geselle', 4, false], ['Ausbildung', 4, false]],
            'SHK'           => [['Meister', 2, true], ['Geselle', 4, false], ['Geselle', 4, false], ['Techniker', 4, false]],
            'Bauelemente'   => [['Meister', 2, true], ['Geselle', 4, false], ['Geselle', 4, false]],
            'Schreiner'     => [['Meister', 2, true], ['Geselle', 4, false], ['Ausbildung', 4, false]],
            'Dachdecker'    => [['Meister', 2, true], ['Geselle', 4, false], ['Geselle', 4, false]],
            'Maler'         => [['Meister', 2, true], ['Geselle', 4, false], ['Helfer', 4, false]],
            'Fliesenleger'  => [['Meister', 2, true], ['Geselle', 4, false], ['Geselle', 4, false]],
            'Baudekoration' => [['Meister', 2, true], ['Geselle', 4, false], ['Designer', 4, false]],
        ];

        // Roster zusammenstellen (genau 50): 3 feste + generierte.
        $roster = [
            ['v' => 'Markus', 'n' => 'Hoffmann', 'dep' => 'Geschäftsführung', 'qual' => 'Geschäftsführung', 'stufe' => 0, 'g' => 'men', 'pn' => 32, 'head' => true],
            ['v' => 'Claudia', 'n' => 'Neumann', 'dep' => 'Buchhaltung', 'qual' => 'Buchhaltung', 'stufe' => 1, 'g' => 'women', 'pn' => 44, 'head' => true],
            ['v' => 'Kevin', 'n' => 'Wagner', 'dep' => 'Elektro', 'qual' => 'Geselle', 'stufe' => 4, 'g' => 'men', 'pn' => 15, 'head' => false],
        ];
        foreach ($gen as $dep => $members) {
            foreach ($members as $m) {
                [$qual, $stufe, $head] = $m;
                $g = in_array($qual, ['Marketing', 'Buchhaltung', 'Verwaltung'], true) ? 'women' : (($li % 3 === 0) ? 'women' : 'men');
                if ($g === 'men') { $v = $men[$mi % count($men)]; $mi++; $pn = $pnMen++; }
                else { $v = $women[$wi % count($women)]; $wi++; $pn = $pnWomen++; }
                $n = $last[$li % count($last)]; $li++;
                $roster[] = ['v' => $v, 'n' => $n, 'dep' => $dep, 'qual' => $qual, 'stufe' => $stufe, 'g' => $g, 'pn' => $pn, 'head' => (bool) $head];
            }
        }

        $slugify = function (string $s): string {
            $s = str_replace(['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'], ['ae', 'oe', 'ue', 'ae', 'oe', 'ue', 'ss'], $s);
            $s = Str::ascii($s);
            return strtolower(preg_replace('/[^a-zA-Z]/', '', $s));
        };

        $imgDir = public_path('images/employee');
        if (! is_dir($imgDir)) {
            @mkdir($imgDir, 0775, true);
        }

        // Stundensätze je Rolle (Gehalt) + Vertragstypen.
        $rate = [
            'Geschäftsführung' => 95, 'Management' => 65, 'Meister' => 70, 'Geselle' => 45, 'Helfer' => 30,
            'Techniker' => 50, 'Planer' => 52, 'Designer' => 48, 'Controlling' => 58, 'Außendienst' => 50,
            'Innendienst' => 40, 'Buchhaltung' => 45, 'Marketing' => 48, 'Verwaltung' => 38, 'Ausbildung' => 14,
        ];
        $ctAzubi = DB::table('contract_types')->where('contract_type', 'Ausbildung')->value('id');
        $ctTeil  = DB::table('contract_types')->where('contract_type', 'Teilzeit')->value('id');

        $created = [];
        $headByDep = [];
        $ceoEmpId = null;

        foreach ($roster as $t) {
            $slug  = $slugify($t['v']) . '.' . $slugify($t['n']);
            $email = $slug . '@solar-aspekt-nord.test';
            $file  = $slug . '.jpg';

            // Profilfoto idempotent laden (nur wenn Datei fehlt). Bei Fehler: Bild leer + Vermerk.
            $image = '';
            $dest  = $imgDir . '/' . $file;
            if (is_file($dest) && filesize($dest) > 0) {
                $image = $file;
            } else {
                $url   = "https://randomuser.me/api/portraits/{$t['g']}/{$t['pn']}.jpg";
                $bytes = @file_get_contents($url);
                if ($bytes !== false && strlen($bytes) > 1000) {
                    @file_put_contents($dest, $bytes);
                    $image = $file;
                } else {
                    $this->command->warn("Foto-Download fehlgeschlagen ({$email}): {$url}");
                }
            }

            // Arbeitsvertrag / Gehalt / Urlaub / Krankheit
            $isAzubi  = ($t['qual'] === 'Ausbildung');
            $salary   = round(($rate[$t['qual']] ?? 40) * (1 + rand(-5, 9) / 100), 2);
            $ctId     = $isAzubi ? ($ctAzubi ?: $contractTypeId) : $contractTypeId;
            $workHour = 40; $workType = 'Vollzeit';
            if (! $isAzubi && in_array($t['qual'], ['Verwaltung', 'Innendienst'], true) && rand(0, 2) === 0) {
                $ctId = $ctTeil ?: $ctId; $workHour = 25; $workType = 'Teilzeit';
            }
            $contractDate = $now->copy()->subDays(rand(120, 3650))->toDateString();
            $age = $isAzubi ? rand(17, 21) : rand(24, 60);
            $dob = $now->copy()->subYears($age)->subDays(rand(0, 360))->toDateString();
            $krank = rand(0, 14);

            DB::table('employees')->updateOrInsert(
                ['email' => $email],
                ['title' => ($t['g'] === 'women' ? 'Frau' : 'Herr'), 'name' => $t['v'], 'lastname' => $t['n'],
                 'bio' => ($funktion[$t['qual']] ?? $t['qual']) . ' · ' . $t['dep'],
                 'branch' => $branchId, 'contract_type_id' => $ctId, 'contract_date' => $contractDate,
                 'qualification_id' => ($qualId[$t['qual']] ?? null), 'salary_per_hour' => $salary,
                 'dob' => $dob, 'phone' => '0' . rand(151, 179) . ' ' . rand(1000000, 9999999),
                 'image' => $image, 'working_hour' => $workHour, 'working_type' => $workType, 'status' => 'Active',
                 'leave' => 30, 'remaining_day' => 30 - rand(0, 18),
                 'sick_leave' => 42, 'sick_leave_remaining' => 42 - $krank,
                 'daily_start_time' => '08:00:00', 'daily_end_time' => '17:00:00',
                 'color' => '#16a34a', 'created_at' => $now, 'updated_at' => $now]
            );
            $empId = DB::table('employees')->where('email', $email)->value('id');
            if ($t['stufe'] === 0) {
                $ceoEmpId = $empId;
            }
            if ($t['head']) {
                $headByDep[$t['dep']] = $empId;
            }

            DB::table('users')->updateOrInsert(
                ['email' => $email],
                ['name' => (string) $empId, 'is_admin' => ($t['stufe'] === 0 ? 1 : 0), 'is_active' => 1,
                 'image' => null, 'password' => bcrypt('demo1234'), 'created_at' => $now, 'updated_at' => $now]
            );
            $userId = DB::table('users')->where('email', $email)->value('id');

            // user_rolls je Stufe (idempotent: erst leeren, dann gemäß Stufe befüllen).
            DB::table('user_rolls')->where('user_id', $userId)->delete();
            $rolls = [];
            if ($t['stufe'] === 1) {
                foreach ($allItems as $it) {
                    $rolls[$it] = ['is_read' => 1, 'is_add' => 1, 'is_update' => 1, 'is_delete' => 1];
                }
            } elseif ($t['stufe'] === 2) {
                foreach (($areaItems[$t['dep']] ?? []) as $it) {
                    $rolls[$it] = ['is_read' => 1, 'is_add' => 1, 'is_update' => 1, 'is_delete' => 1];
                }
                foreach ($crossReadItems as $it) {
                    $rolls[$it] = $rolls[$it] ?? ['is_read' => 1, 'is_add' => 0, 'is_update' => 0, 'is_delete' => 0];
                }
            } elseif ($t['stufe'] === 3) {
                $upd = in_array($t['dep'], $tradeDeps, true) ? 1 : 0;
                foreach (($areaItems[$t['dep']] ?? []) as $it) {
                    $rolls[$it] = ['is_read' => 1, 'is_add' => 0, 'is_update' => $upd, 'is_delete' => 0];
                }
            }
            foreach ($rolls as $item => $flags) {
                DB::table('user_rolls')->insert(array_merge(
                    ['user_id' => $userId, 'item_id' => $item, 'created_at' => $now, 'updated_at' => $now],
                    $flags
                ));
            }

            $depId = $depIds[$t['dep']] ?? null;
            if ($depId) {
                DB::table('employee_departments')->updateOrInsert(
                    ['employee_id' => $empId, 'department_id' => $depId],
                    ['created_at' => $now, 'updated_at' => $now]
                );
                $positionId = DB::table('positions')->where('qualification', $t['qual'])->value('id');
                if ($positionId) {
                    $isTrade = in_array($t['dep'], $tradeDeps, true);
                    DB::table('department_positions')->updateOrInsert(
                        ['employee_id' => $empId, 'department_id' => $depId, 'position_id' => $positionId],
                        ['percent' => 100, 'montage_percent' => ($isTrade ? 100 : 0), 'office_percent' => ($isTrade ? 0 : 100),
                         'working_hours' => 40, 'main' => 'Yes', 'created_at' => $now, 'updated_at' => $now]
                    );
                }
            }

            $created[] = ['empId' => $empId, 'dep' => $t['dep'], 'head' => $t['head'], 'isCeo' => ($t['stufe'] === 0)];
        }

        // ── Vorgesetzte setzen (Geschäftsführer → Abteilungsköpfe → Mitarbeiter) ──
        foreach ($created as $c) {
            if ($c['isCeo']) {
                $sup = null;
            } elseif ($c['head']) {
                $sup = $ceoEmpId;
            } else {
                $sup = $headByDep[$c['dep']] ?? $ceoEmpId;
            }
            DB::table('employees')->where('id', $c['empId'])->update(['supervisor' => $sup]);
        }

        // branches.chairman auf den Geschäftsführer (Hoffmann) setzen.
        if ($ceoEmpId) {
            DB::table('branches')->where('slug', 'solar-aspekt-nord')->update(['chairman' => $ceoEmpId]);
        }

        // is_active=1 als "Konto aktiv"-Baseline für ALLE Demo-Konten (is_active ist zugleich Online-Flag;
        // Login erzwingt is_active NICHT -> 0 sperrt keinen Login).
        DB::table('users')->where('email', 'like', '%@solar-aspekt-nord.test')->update(['is_active' => 1]);

        return count($created);
    }
}
