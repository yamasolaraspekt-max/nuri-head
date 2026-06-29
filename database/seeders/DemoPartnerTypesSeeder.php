<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DEMO-Partnertypen: füllt die Partner-Reiter der brands-Tabelle (type) —
 * Hersteller (brand), Nachunternehmer (sub_contractor), Architekten (architect),
 * Banken (bank), Versicherungen (insurance), Geschäftspartner (contractor), Weitere (other).
 * Jeder Partner mit 1-2 Ansprechpartnern (brand_departments).
 *
 * Korrigiert zudem die Demo-Hersteller von 'Hersteller' auf den kanonischen Wert 'brand'.
 * Idempotent (Reset der Nicht-Hersteller-Partner). Nur frei erfundene/branchenübliche Namen.
 */
class DemoPartnerTypesSeeder extends Seeder
{
    public function run(): void
    {
        $now  = Carbon::now();
        $pick = fn(array $a) => $a[array_rand($a)];

        $vor  = ['Lukas', 'Jonas', 'Paul', 'Tim', 'Jan', 'Mia', 'Emma', 'Lea', 'Sophie', 'Nina', 'Marco', 'Sven', 'Katrin', 'Anke', 'Dirk', 'Silke'];
        $nach = ['Schmidt', 'Meyer', 'Becker', 'Wolf', 'Richter', 'Hansen', 'Voß', 'Stahl', 'Brandt', 'Engel', 'Greve', 'Block', 'Petersen', 'Paulsen'];
        $orte = [['Hamburg', '22045'], ['Lübeck', '23552'], ['Kiel', '24103'], ['Bremen', '28195'], ['Hannover', '30159'], ['Frankfurt', '60311'], ['Köln', '50667'], ['Nürnberg', '90402']];
        $person = fn() => $pick($vor) . ' ' . $pick($nach);

        $types = ['sub_contractor', 'architect', 'bank', 'insurance', 'contractor', 'other'];

        // Hersteller auf kanonischen Typ 'brand' setzen (statt 'Hersteller')
        DB::table('brands')->where('type', 'Hersteller')->update(['type' => 'brand']);

        // Reset der Nicht-Hersteller-Partner (idempotent)
        $oldIds = DB::table('brands')->whereIn('type', $types)->pluck('id')->all();
        if ($oldIds) {
            DB::table('brand_departments')->whereIn('brand_id', $oldIds)->delete();
            DB::table('brands')->whereIn('id', $oldIds)->delete();
        }

        // [type, Name, Zweck/Bereich]
        $partners = [
            // Nachunternehmer
            ['sub_contractor', 'Elektro Nord GmbH', 'Elektroinstallation (NU)'],
            ['sub_contractor', 'Gerüstbau Hansen', 'Gerüstbau (NU)'],
            ['sub_contractor', 'Dachprofi Subunternehmer', 'Dacharbeiten (NU)'],
            ['sub_contractor', 'SHK-Service Küste', 'Heizung/Sanitär (NU)'],
            ['sub_contractor', 'Putz & Fassade Meyer', 'Fassade/Putz (NU)'],
            // Architekten / Planung
            ['architect', 'Architekturbüro Petersen', 'Planung/Entwurf'],
            ['architect', 'Planungsbüro Nord', 'Bauplanung'],
            ['architect', 'Ingenieurbüro Statik Hamburg', 'Statik/Tragwerk'],
            ['architect', 'Energieberatung & Planung GmbH', 'Energieberatung/BEG'],
            // Banken / Finanzierung
            ['bank', 'KfW Bankengruppe', 'Förderkredite'],
            ['bank', 'Sparkasse Holstein', 'Finanzierung'],
            ['bank', 'Hamburger Volksbank', 'Finanzierung'],
            ['bank', 'UmweltBank AG', 'Öko-Finanzierung'],
            ['bank', 'GLS Bank', 'Nachhaltige Finanzierung'],
            // Versicherungen
            ['insurance', 'Allianz Versicherung', 'Sach-/Haftpflicht'],
            ['insurance', 'AXA Konzern AG', 'Betriebsversicherung'],
            ['insurance', 'Gothaer Versicherung', 'Photovoltaik-Versicherung'],
            ['insurance', 'HDI Versicherung', 'Bauleistung/Haftpflicht'],
            ['insurance', 'R+V Versicherung', 'Sachversicherung'],
            // Geschäftspartner
            ['contractor', 'Bauträger Elbblick GmbH', 'Bauträger'],
            ['contractor', 'Immobilien Service Nord', 'Immobilienverwaltung'],
            ['contractor', 'Hausverwaltung Lübeck', 'Hausverwaltung'],
            // Weitere Partner
            ['other', 'Entsorgung & Recycling Nord', 'Entsorgung'],
            ['other', 'Reinigungsservice Hanse', 'Reinigung'],
            ['other', 'Marketing-Agentur Welle', 'Marketing'],
        ];

        $posByType = [
            'sub_contractor' => ['Bauleiter/in', 'Vorarbeiter/in'],
            'architect'      => ['Architekt/in', 'Projektleitung'],
            'bank'           => ['Firmenkundenberater/in', 'Förderspezialist/in'],
            'insurance'      => ['Außendienst', 'Sachbearbeitung'],
            'contractor'     => ['Geschäftsführung', 'Ansprechpartner/in'],
            'other'          => ['Ansprechpartner/in', 'Disposition'],
        ];

        $nP = 0; $nC = 0;
        foreach ($partners as $p) {
            [$type, $name, $zweck] = $p;
            $ort = $pick($orte);
            DB::table('brands')->insert([
                'type' => $type, 'name' => $name,
                'initial' => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3)),
                'purpose' => $zweck, 'address' => $ort[1] . ' ' . $ort[0],
                'status' => 'Published', 'created_at' => $now, 'updated_at' => $now,
            ]);
            $bid = DB::table('brands')->where('name', $name)->where('type', $type)->value('id');
            $nP++;

            $abts = $posByType[$type] ?? ['Ansprechpartner/in'];
            foreach (array_slice($abts, 0, rand(1, 2)) as $pos) {
                DB::table('brand_departments')->insert([
                    'brand_id' => $bid, 'brand_department' => $zweck, 'name' => $person(),
                    'position' => $pos, 'phone' => '0' . rand(30, 89) . ' ' . rand(1000000, 9999999),
                    'email' => strtolower(preg_replace('/[^a-z]/i', '', $name)) . '@partner.test',
                    'status' => 'active', 'created_at' => $now, 'updated_at' => $now,
                ]);
                $nC++;
            }
        }

        $this->command->info('Partnertypen: Hersteller->brand korrigiert; ' . $nP . ' Partner (Nachunternehmer/Architekten/Banken/Versicherungen/Geschäftspartner/Weitere) + ' . $nC . ' Ansprechpartner.');
    }
}
