<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * DEMO-Firma „Solar Aspekt Nord GmbH" — Stammdaten / Gerüst (Schritt 1).
 *
 * Idempotent (updateOrInsert auf natürliche Schlüssel). Nur frei erfundene Daten.
 * Reihenfolge: Niederlassung → Abteilungen → Teams → Qualifikationen/Positionen →
 * Vertragstypen → Produkte → Rechte-Items → Sprachen/Länder → Urlaubsanspruch → Arbeitsorte.
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

        // ── Abteilungen ────────────────────────────────────────────────
        $departments = ['Vertrieb', 'Planung & Technik', 'Montage & Bau', 'Lager & Einkauf', 'Buchhaltung & Verwaltung', 'Kundendienst'];
        $depIds = [];
        foreach ($departments as $i => $name) {
            DB::table('departments')->updateOrInsert(
                ['department_name' => $name, 'branch_id' => $branchId],
                ['order' => $i + 1, 'description' => $name . ' der Solar Aspekt Nord GmbH', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]
            );
            $depIds[$name] = DB::table('departments')->where('department_name', $name)->where('branch_id', $branchId)->value('id');
        }

        // ── Teams (je Abteilung) ───────────────────────────────────────
        $teams = [
            ['Vertriebsteam Nord', 'Vertrieb'], ['Planungsteam', 'Planung & Technik'],
            ['Montageteam A', 'Montage & Bau'], ['Montageteam B', 'Montage & Bau'],
            ['Lager- & Einkaufsteam', 'Lager & Einkauf'], ['Verwaltungsteam', 'Buchhaltung & Verwaltung'],
            ['Kundendienstteam', 'Kundendienst'],
        ];
        foreach ($teams as $t) {
            DB::table('teams')->updateOrInsert(
                ['slug' => Str::slug($t[0])],
                ['name' => $t[0], 'department_id' => $depIds[$t[1]], 'description' => $t[0], 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]
            );
        }

        // ── Qualifikationen (Gewerke) + Positionen (Funktionen) ────────
        // [Qualifikation, Funktion/Position, Stundensatz €]
        $roles = [
            ['Geschäftsführung', 'Geschäftsführer/in', 0],
            ['Elektromeister', 'Meister/in Elektrotechnik', 75],
            ['Elektrofachkraft', 'Elektriker/in', 55],
            ['Anlagenmechaniker SHK', 'SHK-Monteur/in', 55],
            ['PV-Monteur', 'PV-Monteur/in', 48],
            ['Dachmonteur', 'Dachmonteur/in', 50],
            ['Disponent', 'Disponent/in', 45],
            ['Vertriebsberater', 'Vertriebsberater/in', 50],
            ['Projektplaner', 'Projektplaner/in', 52],
            ['Lagerist', 'Lagerist/in', 38],
            ['Buchhalter', 'Buchhalter/in', 45],
            ['Bürokraft', 'Sachbearbeiter/in', 38],
        ];
        $qualId = [];
        $posId = [];
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
            $posId[$r[1]] = DB::table('positions')->where('position', $r[1])->value('id');
        }

        // ── Vertragstypen ──────────────────────────────────────────────
        foreach ([['Vollzeit unbefristet', 'unbefristet'], ['Vollzeit befristet', '24 Monate'], ['Teilzeit', 'unbefristet'], ['Minijob', '12 Monate'], ['Ausbildung', '36 Monate']] as $ct) {
            DB::table('contract_types')->updateOrInsert(['contract_type' => $ct[0]], ['contract_duration' => $ct[1], 'created_at' => $now, 'updated_at' => $now]);
        }

        // ── Produkte (article_groups) ──────────────────────────────────
        foreach ([['Photovoltaik-Anlage', 'PV'], ['Wärmepumpe', 'WP'], ['Stromspeicher', 'SP'], ['Wallbox', 'WB'], ['Elektroinstallation', 'EL']] as $ag) {
            DB::table('article_groups')->updateOrInsert(['article_group' => $ag[0]], ['initial' => $ag[1], 'min_value' => 0, 'max_value' => 0, 'created_at' => $now, 'updated_at' => $now]);
        }

        // ── Rechte-Items: Sidebar-Sektionen + Spezial-Items aus Paket 1 ─────
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
        // Hinweis: daily_report_work_places.branch_id ist FK auf branch_addresses (NICHT branches) und nullable -> null.
        foreach ([['Büro Hamburg', 'branch'], ['Zentrallager', 'warehouse'], ['Baustelle / Kunde', 'customer']] as $wp) {
            DB::table('daily_report_work_places')->updateOrInsert(['place_name' => $wp[0]], ['type' => $wp[1], 'branch_id' => null, 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]);
        }

        // ── Demo-Profile (3 Login-Konten für die Paket-1-Rechte-Verifikation) ──
        $this->seedDemoProfiles($branchId, $depIds, $qualId, $now);

        $this->command->info('Stammdaten gesetzt: 1 Niederlassung, ' . count($departments) . ' Abteilungen, ' . count($teams) . ' Teams, ' . count($roles) . ' Qualifikationen/Positionen + 14 Mitarbeiter (Belegschaft mit Rollen & Fotos).');
    }

    /**
     * Lebendige Demo-Belegschaft (14 Personen): employees + users + user_rolls + Profilfoto.
     * Verdrahtung exakt nach Code: users.name = (string) employees.id; user_rolls.user_id = users.id;
     * item_id = Item-Name (String); Flags tinyint (1/0). Passwort aller Konten: "demo1234".
     * Avatar: employees.image (Dateiname) in public/images/employee/ (Topbar/Liste/Profil via asset()).
     * Rollen-Stufen (macht „Rolle" sichtbar):
     *   0 ADMIN   -> is_admin=1, keine user_rolls (Bypass)            [Hoffmann = A]
     *   1 VOLL    -> alle Items, alle 4 Flags=1                       [Neumann  = B]
     *   2 LEITUNG -> volle CRUD auf Bereichs-Items + is_read Querschnitt
     *   3 SACHB.  -> nur is_read auf Bereichs-Items (Kundendienst +is_update)
     *   4 OHNE    -> keine user_rolls                                 [Wagner u.a. = C]
     * Fotos werden idempotent von randomuser.me geladen (nur falls Datei fehlt; danach offline).
     */
    private function seedDemoProfiles(int $branchId, array $depIds, array $qualId, $now): void
    {
        // Alte 3 Platzhalter-Profile restlos entfernen (keine Leichen).
        foreach (['admin.demo@san.test', 'berechtigt.demo@san.test', 'ohne.demo@san.test'] as $oldEmail) {
            $oldUser = DB::table('users')->where('email', $oldEmail)->first();
            if ($oldUser) {
                DB::table('user_rolls')->where('user_id', $oldUser->id)->delete();
            }
            DB::table('users')->where('email', $oldEmail)->delete();
            $oldEmp = DB::table('employees')->where('email', $oldEmail)->first();
            if ($oldEmp) {
                DB::table('employee_departments')->where('employee_id', $oldEmp->id)->delete();
            }
            DB::table('employees')->where('email', $oldEmail)->delete();
        }

        $contractTypeId = DB::table('contract_types')->where('contract_type', 'Vollzeit unbefristet')->value('id');

        $allItems = ['Customer', 'Email', 'Employee', 'Finance', 'Inquiry', 'Organization', 'Partner', 'Problem', 'Product', 'Users', 'Programmer', 'Administrator', 'Super', 'Invoice'];
        $areaItems = [
            'Vertrieb'                 => ['Customer', 'Inquiry', 'Partner'],
            'Planung & Technik'        => ['Product', 'Problem'],
            'Lager & Einkauf'          => ['Product', 'Organization'],
            'Buchhaltung & Verwaltung' => ['Finance', 'Invoice', 'Employee'],
            'Kundendienst'             => ['Problem', 'Customer'],
            'Montage & Bau'            => [],
        ];
        $crossReadItems = ['Customer', 'Product']; // Querschnitt-Leserechte für Leitung

        $team = [
            ['v' => 'Markus',  'n' => 'Hoffmann', 'fn' => 'Geschäftsführer',                'dep' => 'Buchhaltung & Verwaltung', 'qual' => 'Geschäftsführung',       'stufe' => 0, 'g' => 'men',   'pn' => 32],
            ['v' => 'Claudia', 'n' => 'Neumann',  'fn' => 'Büroleitung / Buchhalterin',     'dep' => 'Buchhaltung & Verwaltung', 'qual' => 'Buchhalter',            'stufe' => 1, 'g' => 'women', 'pn' => 44],
            ['v' => 'Sandra',  'n' => 'König',    'fn' => 'Vertriebsleiterin',              'dep' => 'Vertrieb',                 'qual' => 'Vertriebsberater',      'stufe' => 2, 'g' => 'women', 'pn' => 68],
            ['v' => 'Daniel',  'n' => 'Krüger',   'fn' => 'Vertriebsberater',               'dep' => 'Vertrieb',                 'qual' => 'Vertriebsberater',      'stufe' => 3, 'g' => 'men',   'pn' => 45],
            ['v' => 'Aylin',   'n' => 'Yıldız',   'fn' => 'Vertriebsberaterin',             'dep' => 'Vertrieb',                 'qual' => 'Vertriebsberater',      'stufe' => 3, 'g' => 'women', 'pn' => 65],
            ['v' => 'Thomas',  'n' => 'Bauer',    'fn' => 'Technische Leitung (E-Meister)', 'dep' => 'Planung & Technik',        'qual' => 'Elektromeister',        'stufe' => 2, 'g' => 'men',   'pn' => 52],
            ['v' => 'Lena',    'n' => 'Schmitt',  'fn' => 'Projektplanerin',                'dep' => 'Planung & Technik',        'qual' => 'Projektplaner',         'stufe' => 3, 'g' => 'women', 'pn' => 30],
            ['v' => 'Petra',   'n' => 'Wolf',     'fn' => 'Lagerleiterin / Disponentin',    'dep' => 'Lager & Einkauf',          'qual' => 'Disponent',             'stufe' => 2, 'g' => 'women', 'pn' => 12],
            ['v' => 'Tobias',  'n' => 'Richter',  'fn' => 'Lagerist',                       'dep' => 'Lager & Einkauf',          'qual' => 'Lagerist',              'stufe' => 3, 'g' => 'men',   'pn' => 76],
            ['v' => 'Julia',   'n' => 'Fischer',  'fn' => 'Sachbearbeiterin Verwaltung',    'dep' => 'Buchhaltung & Verwaltung', 'qual' => 'Bürokraft',             'stufe' => 3, 'g' => 'women', 'pn' => 22],
            ['v' => 'Andreas', 'n' => 'Schäfer',  'fn' => 'Kundendienst-Techniker',         'dep' => 'Kundendienst',             'qual' => 'Elektrofachkraft',      'stufe' => 3, 'g' => 'men',   'pn' => 41],
            ['v' => 'Kevin',   'n' => 'Wagner',   'fn' => 'PV-Monteur',                     'dep' => 'Montage & Bau',            'qual' => 'PV-Monteur',            'stufe' => 4, 'g' => 'men',   'pn' => 15],
            ['v' => 'Mehmet',  'n' => 'Demir',    'fn' => 'SHK-Monteur',                    'dep' => 'Montage & Bau',            'qual' => 'Anlagenmechaniker SHK', 'stufe' => 4, 'g' => 'men',   'pn' => 83],
            ['v' => 'Stefan',  'n' => 'Lange',    'fn' => 'Dachmonteur',                    'dep' => 'Montage & Bau',            'qual' => 'Dachmonteur',           'stufe' => 4, 'g' => 'men',   'pn' => 67],
        ];

        $slugify = function (string $s): string {
            $s = str_replace(['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'], ['ae', 'oe', 'ue', 'ae', 'oe', 'ue', 'ss'], $s);
            $s = Str::ascii($s);
            return strtolower(preg_replace('/[^a-zA-Z]/', '', $s));
        };

        $imgDir = public_path('images/employee');
        if (! is_dir($imgDir)) {
            @mkdir($imgDir, 0775, true);
        }

        $adminEmpId = null;

        foreach ($team as $t) {
            $slug  = $slugify($t['v']) . '.' . $slugify($t['n']);
            $email = $slug . '@solar-aspekt-nord.test';
            $file  = $slug . '.jpg';

            // Profilfoto idempotent laden (nur wenn Datei fehlt). Bei Fehler: Bild leer lassen + Vermerk.
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

            DB::table('employees')->updateOrInsert(
                ['email' => $email],
                ['title' => ($t['g'] === 'women' ? 'Frau' : 'Herr'), 'name' => $t['v'], 'lastname' => $t['n'],
                 'branch' => $branchId, 'contract_type_id' => $contractTypeId, 'qualification_id' => ($qualId[$t['qual']] ?? null),
                 'image' => $image, 'working_hour' => 40, 'working_type' => 'Vollzeit', 'status' => 'Active',
                 'remaining_day' => 30, 'leave' => 30, 'daily_start_time' => '08:00:00', 'daily_end_time' => '17:00:00',
                 'color' => '#16a34a', 'created_at' => $now, 'updated_at' => $now]
            );
            $empId = DB::table('employees')->where('email', $email)->value('id');
            if ($t['stufe'] === 0) {
                $adminEmpId = $empId;
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
                $upd = ($t['dep'] === 'Kundendienst') ? 1 : 0;
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
                // Position (Funktion) passend zur Qualifikation/Gewerk verknüpfen.
                $positionId = DB::table('positions')->where('qualification', $t['qual'])->value('id');
                if ($positionId) {
                    $isMontage = ($t['dep'] === 'Montage & Bau');
                    DB::table('department_positions')->updateOrInsert(
                        ['employee_id' => $empId, 'department_id' => $depId, 'position_id' => $positionId],
                        ['percent' => 100, 'montage_percent' => ($isMontage ? 100 : 0), 'office_percent' => ($isMontage ? 0 : 100),
                         'working_hours' => 40, 'main' => 'Yes', 'created_at' => $now, 'updated_at' => $now]
                    );
                }
            }
        }

        // branches.chairman auf die echte Geschäftsführer-employee-id (Hoffmann) setzen.
        if ($adminEmpId) {
            DB::table('branches')->where('slug', 'solar-aspekt-nord')->update(['chairman' => $adminEmpId]);
        }
    }
}
