<?php

namespace Database\Seeders\Testing;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Legt den EIGENEN Test-Kontext an (haengt sich NICHT an echte Daten):
 * Produkt, Gewerk, Kunde(Privat), Objekt, lead_product_list, task_phase und
 * zwei Taetigkeiten:
 *  - "Montage (Meister erforderlich)" -> required_qualification_id = Qual mit sort_order 3
 *  - "Aufmass (keine Anforderung)"    -> required_qualification_id = null
 *
 * Referenziert die 26 echten position_qualifications (legt KEINE neuen an).
 * Idempotent ueber die Marker-Felder.
 */
class HarnessContextSeeder extends Seeder
{
    use HarnessSupport;

    public function run(): void
    {
        $this->guardLocal();

        // Produkt (article_groups = Produkte)
        $productId = $this->upsertId('article_groups',
            ['article_group' => $this->mark('Produkt')],
            ['initial' => 'TH']
        );

        // Gewerk (departments = Gewerke/Trades)
        $gewerkId = $this->upsertId('departments',
            ['department_name' => $this->mark('Gewerk')]
        );

        // Kunde (new_leads); customer_type ist Pflicht
        $customerId = $this->upsertId('new_leads',
            ['name' => $this->mark('Kunde')],
            ['customer_type' => 'Privat', 'lastname' => 'Test']
        );

        // Objekt (lead_alternative_adds); lead_id ist Pflicht
        $objectId = $this->upsertId('lead_alternative_adds',
            ['object_name' => $this->mark('Objekt')],
            ['lead_id' => $customerId, 'street' => 'Teststrasse', 'address_no' => '1', 'postcode' => '00000', 'city' => 'Teststadt']
        );

        // Gewerk-Zeile (lead_product_lists): Kunde x Objekt x Produkt
        $lplId = $this->upsertId('lead_product_lists',
            ['customer_id' => $customerId, 'product_id' => $productId, 'alternative_id' => $objectId],
            ['stage' => 'project']
        );

        // phase_section (task_phases.section_id + phase_activities.section_id sind FK -> phase_sections;
        // bei phase_activities NOT NULL). PFLICHT: product_id, status.
        $sectionId = $this->upsertId('phase_sections',
            ['phase_section' => $this->mark('Sektion'), 'product_id' => $productId],
            ['status' => 'active', 'sort_order' => 1]
        );

        // task_phase fuer das Produkt
        $phaseId = $this->upsertId('task_phases',
            ['phase_name' => $this->mark('Phase'), 'product_id' => $productId],
            ['section_id' => $sectionId, 'section_name' => $this->mark('Sektion'), 'stage' => 'project']
        );

        // Anforderung = Qualifikation mit sort_order 3 (Meister-Niveau). Nur der sort_order zaehlt fuer B3.
        $meisterQualId = DB::table('position_qualifications')->where('sort_order', 3)->orderBy('id')->value('id');
        $meisterQualName = DB::table('position_qualifications')->where('id', $meisterQualId)->value('name');

        // Taetigkeit MIT Anforderung (phase_activities.phase_id = task_phases.id, section_id = phase_sections.id)
        $activityReqId = $this->upsertId('phase_activities',
            ['title' => $this->mark('Montage (Meister erforderlich)'), 'product_id' => $productId],
            ['phase_id' => $phaseId, 'section_id' => $sectionId, 'required_qualification_id' => $meisterQualId, 'sort_order' => 1, 'status' => 'active']
        );

        // Taetigkeit OHNE Anforderung -> B3 markiert immer done
        $activityFreeId = $this->upsertId('phase_activities',
            ['title' => $this->mark('Aufmass (keine Anforderung)'), 'product_id' => $productId],
            ['phase_id' => $phaseId, 'section_id' => $sectionId, 'required_qualification_id' => null, 'sort_order' => 2, 'status' => 'active']
        );

        $this->command?->info(self::TAG . " Kontext OK: product=$productId gewerk=$gewerkId customer=$customerId object=$objectId lpl=$lplId phase=$phaseId");
        $this->command?->info(self::TAG . " Taetigkeiten: req=$activityReqId (Anforderung: #$meisterQualId $meisterQualName sort=3) | free=$activityFreeId (ohne Anforderung)");
    }
}
