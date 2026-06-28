<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DEMO-Operativdaten (Schritt 3): Kunden (new_leads) + Objekte (lead_alternative_adds) +
 * Anfragen (inquiries) + Projekte (projects) — bezogen auf die Produktpalette & Dienstleistungen.
 *
 * Idempotent: alle Demo-Operativdaten (Kunden-Mail @demo-kunde.test) werden zuerst entfernt.
 * Nur frei erfundene Daten. Setzt voraus, dass DemoCompanyMasterDataSeeder gelaufen ist
 * (Niederlassung, 16 Abteilungen, 50 Mitarbeiter, 13 Produkte, 8 Dienstleistungen).
 */
class DemoOperationalDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $branchId = DB::table('branches')->where('slug', 'solar-aspekt-nord')->value('id');
        if (! $branchId) {
            $this->command->warn('Niederlassung fehlt — bitte zuerst DemoCompanyMasterDataSeeder ausführen.');
            return;
        }

        $empIds   = DB::table('employees')->where('email', 'like', '%@solar-aspekt-nord.test')->pluck('id')->all();
        $products = DB::table('article_groups')->pluck('id', 'article_group')->all(); // Name => id
        $depIds   = DB::table('departments')->where('branch_id', $branchId)->pluck('id', 'department_name')->all();
        $services = DB::table('inquiry_types')->pluck('type')->all();
        if (! $empIds || ! $products || ! $services) {
            $this->command->warn('Stammdaten unvollständig (Mitarbeiter/Produkte/Dienstleistungen) — Abbruch.');
            return;
        }

        // Produkt -> zuständiges Gewerk/Abteilung
        $prodDept = [
            'Photovoltaik' => 'Elektro', 'Wärmepumpe' => 'Heizung', 'Batteriespeicher' => 'Elektro',
            'Wallbox' => 'Elektro', 'Fenster' => 'Bauelemente', 'Türen' => 'Bauelemente',
            'Badsanierung' => 'SHK', 'Küche' => 'Schreiner', 'Fliesen' => 'Fliesenleger',
            'Dach' => 'Dachdecker', 'Insektenschutz' => 'Bauelemente', 'Fliegengitter' => 'Bauelemente',
            'Tapete' => 'Maler',
        ];
        $productNames = array_keys($products);

        // ── Idempotenter Reset der Demo-Operativdaten ──────────────────
        $oldCust = DB::table('new_leads')->where('email', 'like', '%@demo-kunde.test')->pluck('id')->all();
        if ($oldCust) {
            DB::table('projects')->whereIn('customer_id', $oldCust)->delete();
            DB::table('lead_alternative_adds')->whereIn('lead_id', $oldCust)->delete();
            DB::table('new_leads')->whereIn('id', $oldCust)->delete();
        }
        DB::table('inquiries')->where('email', 'like', '%@demo-kunde.test')->delete();

        // ── Namens-/Adress-Pools ───────────────────────────────────────
        $vorM = ['Lukas', 'Jonas', 'Leon', 'Paul', 'Felix', 'Max', 'Tim', 'Jan', 'Tom', 'David', 'Erik', 'Marco', 'Sven', 'Jens', 'Dirk', 'Uwe', 'Olaf', 'Malte'];
        $vorW = ['Mia', 'Emma', 'Hannah', 'Lea', 'Lina', 'Sophie', 'Marie', 'Laura', 'Sarah', 'Nina', 'Katrin', 'Britta', 'Silke', 'Anke', 'Maren'];
        $nach = ['Petersen', 'Hansen', 'Jansen', 'Boldt', 'Carstens', 'Thomsen', 'Andresen', 'Paulsen', 'Nielsen', 'Clausen', 'Voß', 'Stahl', 'Reimer', 'Harms', 'Ahrens', 'Brandt', 'Sauer', 'Greve', 'Lindner', 'Marquardt', 'Behrens', 'Block', 'Dittmer', 'Engel', 'Freese'];
        $firmen = ['Müller GmbH', 'Nordbau AG', 'Hansa Immobilien', 'Elbtech GmbH', 'Küste Wohnbau', 'Meier & Sohn', 'Lübecker Hausverwaltung', 'Baltic Estates', 'Förde Facility', 'Nordlicht Energie GmbH'];
        $orte = [['Hamburg', '22045'], ['Lübeck', '23552'], ['Kiel', '24103'], ['Bremen', '28195'], ['Hannover', '30159'], ['Schwerin', '19053'], ['Rostock', '18055'], ['Buxtehude', '21614'], ['Pinneberg', '25421'], ['Norderstedt', '22844']];
        $strassen = ['Lindenweg', 'Bahnhofstraße', 'Dorfstraße', 'Gartenstraße', 'Hauptstraße', 'Birkenallee', 'Mühlenweg', 'Am Deich', 'Sonnenstraße', 'Feldweg'];
        $quellen = ['Website', 'Empfehlung', 'Messe', 'Telefon', 'Google Ads', 'Flyer'];

        $pick = fn(array $a) => $a[array_rand($a)];

        // ── 50 Kunden (+ je 1 Objekt) ──────────────────────────────────
        $customers = [];
        for ($i = 1; $i <= 50; $i++) {
            $isFirma = (rand(1, 100) <= 35);
            $women   = (rand(0, 1) === 1);
            $vorname = $women ? $pick($vorW) : $pick($vorM);
            $nachname = $pick($nach);
            $ort     = $pick($orte);
            $strasse = $pick($strassen) . ' ' . rand(1, 180);
            $firma   = $isFirma ? $pick($firmen) : null;
            $email   = strtolower($this->ascii($vorname) . '.' . $this->ascii($nachname) . $i) . '@demo-kunde.test';

            $custId = DB::table('new_leads')->insertGetId([
                'customer_type'   => $isFirma ? 'Gewerbe' : 'Privat',
                'customer_no'     => 'SAN-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'title'           => $women ? 'Frau' : 'Herr',
                'firma'           => $firma,
                'name'            => $vorname,
                'lastname'        => $nachname,
                'street'          => $strasse,
                'postcode'        => $ort[1],
                'city'            => $ort[0],
                'full_address'    => $strasse . ', ' . $ort[1] . ' ' . $ort[0],
                'phone'           => '0' . rand(151, 179) . ' ' . rand(1000000, 9999999),
                'email'           => $email,
                'source'          => $pick($quellen),
                'contact_person'  => (string) $pick($empIds),
                'branch'          => $branchId,
                'interest_rating' => rand(2, 5),
                'status'          => 'Active',
                'created_at'      => $now, 'updated_at' => $now,
            ]);

            $objId = DB::table('lead_alternative_adds')->insertGetId([
                'lead_id'      => $custId,
                'object_name'  => 'Objekt ' . $ort[0],
                'full_address' => $strasse . ', ' . $ort[1] . ' ' . $ort[0],
                'street'       => $strasse,
                'postcode'     => $ort[1],
                'city'         => $ort[0],
                'main'         => 1,
                'status'       => 'active',
                'created_at'   => $now, 'updated_at' => $now,
            ]);

            $customers[] = ['id' => $custId, 'obj' => $objId, 'name' => $vorname, 'lastname' => $nachname,
                            'firma' => $firma, 'street' => $strasse, 'postcode' => $ort[1], 'city' => $ort[0],
                            'email' => $email, 'phone' => '040 ' . rand(100000, 999999), 'title' => $women ? 'Frau' : 'Herr'];
        }

        // ── 40 Anfragen (Produkt + Dienstleistung + zuständiges Gewerk) ─
        $statusInq = ['Unpublished', 'Published', 'Verified'];
        for ($i = 0; $i < 40; $i++) {
            $c       = $pick($customers);
            $product = $pick($productNames);
            $service = $pick($services);
            $dep     = $prodDept[$product] ?? 'Management';

            DB::table('inquiries')->insert([
                'branch_id'      => $branchId,
                'contact_person' => (string) $pick($empIds),
                'department_id'  => $depIds[$dep] ?? null,
                'type'           => $service,
                'source'         => $pick($quellen),
                'title'          => $product . ' – ' . $service,
                'firma'          => $c['firma'],
                'name'           => $c['name'],
                'lastname'       => $c['lastname'],
                'street'         => $c['street'],
                'postcode'       => $c['postcode'],
                'city'           => $c['city'],
                'full_address'   => $c['street'] . ', ' . $c['postcode'] . ' ' . $c['city'],
                'phone'          => $c['phone'],
                'email'          => $c['email'],
                'note'           => 'Anfrage zu ' . $product . ' (' . $service . ').',
                'status'         => $pick($statusInq),
                'created_at'     => $now, 'updated_at' => $now,
            ]);
        }

        // ── 30 Projekte (Kunde + Produkt + Objekt + Dienstleistung) ────
        $projStatus = ['offen', 'in Bearbeitung', 'Montage', 'abgeschlossen'];
        for ($i = 0; $i < 30; $i++) {
            $c       = $pick($customers);
            $product = $pick($productNames);
            $service = $pick($services);
            $dep     = $prodDept[$product] ?? 'Management';
            $depId   = $depIds[$dep] ?? null;

            DB::table('projects')->insert([
                'customer_id'    => $c['id'],
                'product_id'     => $products[$product],
                'alternative_id' => $c['obj'],
                'department_id'  => $depId,
                'service'        => $service,
                'employee_id'    => $pick($empIds),
                'project_leader' => $pick($empIds),
                'project_start'  => $now->copy()->subDays(rand(0, 120))->toDateString(),
                'progress'       => rand(0, 100),
                'priority'       => $pick(['niedrig', 'mittel', 'hoch']),
                'color'          => '#16a34a',
                'project_status' => $pick($projStatus),
                'status'         => 'active',
                'created_at'     => $now, 'updated_at' => $now,
            ]);
        }

        $this->command->info('Operative Demo-Daten gesetzt: ' . count($customers) . ' Kunden (+Objekte), 40 Anfragen, 30 Projekte.');
    }

    /** Umlaute/Sonderzeichen in ASCII für E-Mail-Slugs. */
    private function ascii(string $s): string
    {
        $s = str_replace(['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'], ['ae', 'oe', 'ue', 'ae', 'oe', 'ue', 'ss'], $s);
        return preg_replace('/[^a-zA-Z]/', '', $s);
    }
}
