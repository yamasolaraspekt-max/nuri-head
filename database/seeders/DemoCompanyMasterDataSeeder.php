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

        $this->command->info('Stammdaten gesetzt: 1 Niederlassung, ' . count($departments) . ' Abteilungen, ' . count($teams) . ' Teams, ' . count($roles) . ' Qualifikationen/Positionen + 3 Demo-Profile (Admin / Berechtigt / Ohne Rechte).');
    }

    /**
     * Drei verknüpfte Login-Profile (employees + users + user_rolls), um die
     * Paket-1-Rechtelogik REAL zu prüfen.
     * Verdrahtung exakt nach Code: users.name = employees.id; user_rolls.user_id = users.id.
     * Passwort aller Demo-Konten: "demo1234".
     *   A) admin.demo@san.test      — is_admin=1 (Bypass: sieht/darf alles)
     *   B) berechtigt.demo@san.test — is_admin=0, user_rolls Flag=1 für alle Bereiche
     *   C) ohne.demo@san.test       — is_admin=0, KEINE user_rolls (gesperrt)
     */
    private function seedDemoProfiles(int $branchId, array $depIds, array $qualId, $now): void
    {
        $contractTypeId = DB::table('contract_types')->where('contract_type', 'Vollzeit unbefristet')->value('id');
        $sidebarItems = ['Customer', 'Email', 'Employee', 'Finance', 'Inquiry', 'Organization', 'Partner', 'Problem', 'Product', 'Users'];
        $specialItems = ['Programmer', 'Administrator', 'Super', 'Invoice'];

        $profiles = [
            ['email' => 'admin.demo@san.test',      'name' => 'Demo',  'lastname' => 'Admin',      'is_admin' => 1, 'qual' => 'Geschäftsführung', 'dep' => 'Buchhaltung & Verwaltung', 'rolls' => 'none'],
            ['email' => 'berechtigt.demo@san.test', 'name' => 'Berta', 'lastname' => 'Berechtigt', 'is_admin' => 0, 'qual' => 'Buchhalter',        'dep' => 'Buchhaltung & Verwaltung', 'rolls' => 'full'],
            ['email' => 'ohne.demo@san.test',       'name' => 'Olaf',  'lastname' => 'Ohne',       'is_admin' => 0, 'qual' => 'PV-Monteur',        'dep' => 'Vertrieb',                 'rolls' => 'none'],
        ];

        foreach ($profiles as $p) {
            DB::table('employees')->updateOrInsert(
                ['email' => $p['email']],
                ['title' => 'Frau/Herr', 'name' => $p['name'], 'lastname' => $p['lastname'],
                 'branch' => $branchId, 'contract_type_id' => $contractTypeId, 'qualification_id' => ($qualId[$p['qual']] ?? null),
                 'working_hour' => 40, 'working_type' => 'Vollzeit', 'status' => 'Active', 'remaining_day' => 30, 'leave' => 30,
                 'daily_start_time' => '08:00:00', 'daily_end_time' => '17:00:00', 'color' => '#16a34a',
                 'created_at' => $now, 'updated_at' => $now]
            );
            $empId = DB::table('employees')->where('email', $p['email'])->value('id');

            // users.name MUSS die employees.id tragen (Projekt-Konvention).
            DB::table('users')->updateOrInsert(
                ['email' => $p['email']],
                ['name' => (string) $empId, 'is_admin' => $p['is_admin'], 'is_active' => 1,
                 'password' => bcrypt('demo1234'), 'created_at' => $now, 'updated_at' => $now]
            );
            $userId = DB::table('users')->where('email', $p['email'])->value('id');

            // user_rolls.user_id = users.id (genau der Wurzelfehler aus Paket 1 — hier korrekt verdrahtet).
            if ($p['rolls'] === 'full') {
                foreach (array_merge($sidebarItems, $specialItems) as $item) {
                    DB::table('user_rolls')->updateOrInsert(
                        ['user_id' => $userId, 'item_id' => $item],
                        ['is_read' => 1, 'is_add' => 1, 'is_update' => 1, 'is_delete' => 1, 'created_at' => $now, 'updated_at' => $now]
                    );
                }
            } else {
                DB::table('user_rolls')->where('user_id', $userId)->delete();
            }

            $depId = $depIds[$p['dep']] ?? null;
            if ($depId) {
                DB::table('employee_departments')->updateOrInsert(
                    ['employee_id' => $empId, 'department_id' => $depId],
                    ['created_at' => $now, 'updated_at' => $now]
                );
            }
        }
    }
}
