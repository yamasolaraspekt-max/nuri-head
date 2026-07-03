<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DEMO-CRM-Pipeline: füllt die bisher leere „hintere" CRM-Kette + offene Listen, damit das ganze
 * CRM testbar ist (umgeht bewusst die defekten Anlage-Workflows Auftrag/Angebot).
 *  Angebot (offers + offer_details) -> Auftrag (deals) -> Rechnung (invoices) — stufenkonform zum Kanban.
 *  + Aufgaben (general_tasks), Tickets (problems), externe Firmen, Termin<->MA, Anfrage-Positionen.
 *
 * Idempotent (Reset über die Demo-Kunden @demo-kunde.test). Setzt DemoOperationalDataSeeder voraus.
 */
class DemoCrmPipelineSeeder extends Seeder
{
    public function run(): void
    {
        $now  = Carbon::now();
        $pick = fn(array $a) => $a[array_rand($a)];

        $branchId = DB::table('branches')->where('slug', 'solar-aspekt-nord')->value('id');
        $custIds  = DB::table('new_leads')->where('email', 'like', '%@demo-kunde.test')->pluck('id')->all();
        $empIds   = DB::table('employees')->where('email', 'like', '%@solar-aspekt-nord.test')->pluck('id')->all();
        $leads    = DB::table('lead_product_lists')->whereIn('customer_id', $custIds)->get();
        $inqIds   = DB::table('inquiries')->where('email', 'like', '%@demo-kunde.test')->pluck('id')->all();
        $apptIds  = DB::table('main_appointments')->pluck('id', 'id')->all();
        $apptRows = DB::table('main_appointments')->select('id', 'created_by')->get();
        if (! $custIds || ! $empIds || $leads->isEmpty()) {
            $this->command->warn('Demo-Operativdaten fehlen — bitte zuerst DemoOperationalDataSeeder ausführen.');
            return;
        }

        // ── Reset (idempotent) ─────────────────────────────────────────
        $oldOfferIds = DB::table('offers')->whereIn('customer_id', $custIds)->pluck('id')->all();
        if ($oldOfferIds) {
            DB::table('offer_details')->whereIn('offer_id', $oldOfferIds)->delete();
            DB::table('offer_folders')->whereIn('offer_id', $oldOfferIds)->delete();
        }
        DB::table('invoices')->whereIn('customer_id', $custIds)->delete();
        DB::table('deals')->whereIn('customer_id', $custIds)->delete();
        DB::table('offers')->whereIn('customer_id', $custIds)->delete();
        DB::table('problems')->whereIn('customer_id', $custIds)->delete();
        DB::table('customer_maintenance_contracts')->whereIn('lead_id', $custIds)->delete();
        if ($inqIds) {
            DB::table('inquiry_product_lists')->whereIn('inquiry_id', $inqIds)->delete();
        }
        if ($apptIds) {
            DB::table('main_appointment_employees')->whereIn('appointment_id', array_values($apptIds))->delete();
        }
        $oldTaskIds = DB::table('general_tasks')->where('description', 'like', '%[CRM-Demo]%')->pluck('id')->all();
        if ($oldTaskIds) {
            DB::table('general_task_assignees')->whereIn('general_task_id', $oldTaskIds)->delete();
            DB::table('general_tasks')->whereIn('id', $oldTaskIds)->delete();
        }
        DB::table('external_personals')->where('email', 'like', '%@extern.test')->delete();

        // ── Angebot -> Auftrag -> Rechnung (stufenkonform) ─────────────
        $offerStages   = ['offer', 'follow_up', 'accepted', 'deal', 'project'];
        $dealStages    = ['accepted', 'deal', 'project'];
        $invoiceStages = ['deal', 'project'];
        $nOf = 0; $nDe = 0; $nIn = 0; $i = 0;

        foreach ($leads as $l) {
            $stage = strtolower((string) ($l->status ?: 'lead'));
            if (! in_array($stage, $offerStages, true)) {
                continue;
            }
            $i++;
            $net   = (float) rand(3000, 28000);
            $gross = round($net * 1.19, 2);
            $emp   = $l->employee_id ?: $pick($empIds);

            $offerId = DB::table('offers')->insertGetId([
                'offer_no' => 'ANG-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'customer_id' => $l->customer_id, 'product_id' => $l->product_id, 'alternative_id' => $l->alternative_id,
                'service_id' => null, 'department_id' => $l->department_id, 'service' => $l->service ?: 'Komplettlösung',
                'created_by' => $emp, 'created_for' => $emp, // created_for = FK auf employees
                'status' => in_array($stage, $dealStages, true) ? 'accepted' : $pick(['draft', 'sent', 'sent']),
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $folderId = DB::table('offer_folders')->insertGetId([
                'offer_id' => $offerId, 'customer_id' => $l->customer_id, 'alternative_id' => $l->alternative_id,
                'product_id' => $l->product_id, 'created_by' => $emp, 'color' => '#16a34a',
                'created_at' => $now, 'updated_at' => $now,
            ]);
            DB::table('offer_details')->insert([
                'offer_id' => $offerId, 'offer_folder_id' => $folderId, 'branch_id' => $branchId, 'offer_no' => 'ANG-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'document_status' => 'final', 'company_name' => 'Solar Aspekt Nord GmbH',
                'sections' => json_encode([['title' => 'Leistungen', 'items' => [['name' => 'Lieferung & Montage', 'net' => $net]]]]),
                'total_net' => $net, 'tax_rate' => 19, 'total_gross' => $gross,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $nOf++;

            if (! in_array($stage, $dealStages, true)) {
                continue;
            }
            $dealId = DB::table('deals')->insertGetId([
                'offer_id' => $offerId, 'offer_folder_id' => $folderId,
                'customer_id' => $l->customer_id, 'product_id' => $l->product_id, 'alternative_id' => $l->alternative_id,
                'service_id' => null, 'department_id' => $l->department_id, 'service' => $l->service ?: 'Komplettlösung',
                'employee_id' => $emp, 'price' => $gross, 'sign_date' => $now->copy()->subDays(rand(5, 90))->toDateString(),
                'order_number' => 'AUF-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT), 'offer_number' => 'ANG-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'confirmed_at' => $now->copy()->subDays(rand(1, 80)), 'checked_by' => $pick($empIds), 'reviewer_id' => $pick($empIds),
                'deal_status' => 'confirmed', 'status' => 'active', 'project_status' => $pick(['offen', 'in Bearbeitung', 'Montage', 'abgeschlossen']),
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $nDe++;

            if (! in_array($stage, $invoiceStages, true)) {
                continue;
            }
            $paid = $pick([true, false]);
            // S1-01/D2-A: keine eigene Nummernlogik im Seeder mehr. Ausgestellte Demo-Rechnungen
            // erhalten ihre Nummer ausschließlich über den führenden InvoiceNumberService
            // (Model-saving-Hook). Produktiver Lebenszyklus (paid/sent) statt Legacy-Status 'open'.
            $demoInvoice = new \App\Models\Invoice();
            $demoInvoice->forceFill([
                'customer_id' => $l->customer_id, 'object_id' => $l->alternative_id, 'deal_id' => $dealId,
                'type' => 'final',
                'status' => $paid ? 'paid' : 'sent',
                'issue_date' => $now->copy()->subDays(rand(1, 60))->toDateString(),
                'due_date' => $now->copy()->addDays(14)->toDateString(), 'currency' => 'EUR',
                'subtotal' => $net, 'tax_rate' => 19, 'tax_amount' => round($gross - $net, 2), 'total_amount' => $gross,
                'paid_amount' => $paid ? $gross : 0, 'paid_at' => $paid ? $now->copy()->subDays(rand(1, 30)) : null,
                'created_by' => $emp, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $demoInvoice->save();
            $nIn++;
        }

        // ── Aufgaben (general_tasks) je Abteilung/Mitarbeiter ──────────
        $depList = DB::table('departments')->where('branch_id', $branchId)->pluck('id')->all();
        $taskTitles = ['Angebot nachfassen', 'Aufmaß einplanen', 'Material bestellen', 'Montagetermin koordinieren',
            'Kundenrückfrage klären', 'Dokumentation erstellen', 'Rechnung prüfen', 'Wartung terminieren',
            'Förderantrag vorbereiten', 'Abnahmeprotokoll erstellen'];
        $nTa = 0;
        for ($k = 1; $k <= 45; $k++) {
            $soll = rand(30, 480);
            $taskId = DB::table('general_tasks')->insertGetId([
                'title' => $pick($taskTitles), 'description' => 'Demo-Aufgabe [CRM-Demo]', 'status' => $pick(['open', 'in_progress', 'review', 'done']),
                'priority' => $pick(['low', 'normal', 'important', 'urgent']), 'visibility' => 'all', 'task_mode' => 'single',
                'department_id' => $pick($depList), 'created_by' => $pick($empIds), 'claimed_by' => $pick($empIds),
                'due_at' => $now->copy()->addDays(rand(-10, 30)), 'soll_minutes' => $soll, 'ist_minutes' => rand(0, $soll),
                'progress_percent' => rand(0, 100), 'sort_order' => $k,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            foreach (array_slice($empIds, array_rand($empIds), 1) as $a) {
                DB::table('general_task_assignees')->insert(['general_task_id' => $taskId, 'employee_id' => $a, 'created_at' => $now, 'updated_at' => $now]);
            }
            $nTa++;
        }

        // ── Tickets (problems) ─────────────────────────────────────────
        $nPr = 0;
        foreach ($leads->take(15) as $idx => $l) {
            DB::table('problems')->insert([
                'ticket_no' => 'TIC-' . str_pad((string) ($idx + 1), 5, '0', STR_PAD_LEFT), 'source' => 'Telefon',
                'customer_id' => $l->customer_id, 'alternative_id' => $l->alternative_id, 'product_id' => $l->product_id,
                'lead_product_list_id' => $l->id, 'article_name' => 'Anlage', 'problem' => 'Demo-Störungsmeldung zum Vorgang.',
                'solution' => null, 'responsible' => $pick($empIds), 'date' => $now->copy()->subDays(rand(1, 40))->toDateString(),
                'status' => $pick(['open', 'in_progress', 'closed']), 'priority' => $pick(['low', 'normal', 'high']),
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $nPr++;
        }

        // ── Wartungsverträge ───────────────────────────────────────────
        $nWa = 0;
        foreach (array_slice($custIds, 0, 10) as $cid) {
            DB::table('customer_maintenance_contracts')->insert([
                'lead_id' => $cid, 'title' => 'Wartungsvertrag PV/WP', 'created_at' => $now, 'updated_at' => $now,
            ]);
            $nWa++;
        }

        // ── Externe Firmen ─────────────────────────────────────────────
        $extern = [['Elektro Nord GmbH', 'Subunternehmer'], ['Gerüstbau Hansen', 'Subunternehmer'], ['Dachprofi GmbH', 'Subunternehmer'],
            ['SHK-Service Küste', 'Subunternehmer'], ['Statik-Büro Petersen', 'Planung'], ['Vermessung Elbe', 'Planung'],
            ['Entsorgung Nord', 'Service'], ['Kran & Logistik HH', 'Service']];
        $prodIds = DB::table('article_groups')->pluck('id')->all();
        $nEx = 0;
        foreach ($extern as $idx => $e) {
            DB::table('external_personals')->insert([
                'company_name' => $e[0], 'price_type' => 'fixed', 'price' => rand(40, 120), 'tax_id' => 'DE' . rand(100000000, 999999999),
                'product_id' => $pick($prodIds), 'admin_name' => 'Ansprechpartner', 'email' => 'kontakt' . ($idx + 1) . '@extern.test',
                'phone' => '040 ' . rand(100000, 999999), 'status' => 'active', 'created_at' => $now, 'updated_at' => $now,
            ]);
            $nEx++;
        }

        // ── Termine <-> Mitarbeiter (Pivot) ────────────────────────────
        $nAe = 0;
        foreach ($apptRows as $a) {
            $emp = $a->created_by ?: $pick($empIds);
            DB::table('main_appointment_employees')->insert(['appointment_id' => $a->id, 'employee_id' => $emp, 'status' => 'assigned', 'created_at' => $now, 'updated_at' => $now]);
            $nAe++;
        }

        // ── Anfrage-Positionen (inquiry_product_lists) ─────────────────
        $depByName = DB::table('departments')->where('branch_id', $branchId)->pluck('id')->all();
        $nIp = 0;
        foreach (DB::table('inquiries')->whereIn('id', $inqIds)->get() as $inq) {
            DB::table('inquiry_product_lists')->insert([
                'inquiry_id' => $inq->id, 'product_id' => $pick($prodIds), 'service_id' => null,
                'department_id' => $inq->department_id ?: $pick($depByName), 'employee_id' => $pick($empIds), 'field_employee' => $pick($empIds),
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $nIp++;
        }

        $this->command->info("CRM-Pipeline: {$nOf} Angebote, {$nDe} Auftraege, {$nIn} Rechnungen, {$nTa} Aufgaben, {$nPr} Tickets, {$nWa} Wartungsvertraege, {$nEx} externe Firmen, {$nAe} Termin-Zuordnungen, {$nIp} Anfrage-Positionen.");
    }
}
