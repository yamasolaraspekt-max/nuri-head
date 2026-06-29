<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DEMO-Lead-Aktivität: hängt jedem Lead (lead_product_lists) Berichte, Termine und Notizen an,
 * damit die Kanban-Karten Inhalt zum Testen haben.
 *   Berichte = customer_histories · Termine = main_appointments · Notizen = customer_notes
 *
 * Idempotent (Reset über die Demo-Kunden @demo-kunde.test). Setzt DemoOperationalDataSeeder voraus.
 */
class DemoLeadActivitySeeder extends Seeder
{
    public function run(): void
    {
        $now  = Carbon::now();
        $pick = fn(array $a) => $a[array_rand($a)];

        $custIds = DB::table('new_leads')->where('email', 'like', '%@demo-kunde.test')->pluck('id')->all();
        if (! $custIds) {
            $this->command->warn('Keine Demo-Kunden — bitte zuerst DemoOperationalDataSeeder ausführen.');
            return;
        }
        $leads   = DB::table('lead_product_lists')->whereIn('customer_id', $custIds)->get();
        $empIds  = DB::table('employees')->where('email', 'like', '%@solar-aspekt-nord.test')->pluck('id')->all();
        $stages  = DB::table('lead_stages')->get()->keyBy('key'); // key => {id,name,color}

        // ── Reset ──────────────────────────────────────────────────────
        DB::table('customer_histories')->whereIn('customer_id', $custIds)->delete();
        DB::table('customer_notes')->whereIn('customer_id', $custIds)->delete();
        $leadIds = $leads->pluck('id')->all();
        if ($leadIds) {
            DB::table('main_appointments')->whereIn('lead_product_list_id', $leadIds)->delete();
        }

        $berichte = ['Erstkontakt telefonisch', 'Vor-Ort-Aufmaß durchgeführt', 'Angebot versendet',
            'Nachfass-Telefonat', 'Unterlagen angefordert', 'Kunde hat Rückfragen zur Förderung',
            'Termin bestätigt', 'Angebot besprochen'];
        $terminTyp = ['Beratung', 'Aufmaß', 'Montagebesprechung', 'Übergabe'];
        $kontakt   = ['Vor-Ort', 'Telefon', 'Video'];
        $notizen   = ['Kunde bevorzugt Termine nachmittags.', 'Zufahrt für Montagefahrzeug eng — beachten.',
            'Interesse an Förderung/BEG.', 'Entscheidung im Familienkreis, etwas Geduld nötig.',
            'Bestandsanlage vorhanden, Wechsel geplant.', 'Empfehlung durch Nachbarn.'];

        $nB = 0; $nT = 0; $nN = 0;
        foreach ($leads as $l) {
            $stageKey = strtolower((string) ($l->status ?: 'lead'));
            $stage    = $stages[$stageKey] ?? $stages['lead'] ?? null;
            $emp      = $empIds ? $pick($empIds) : null;
            $field    = $l->field_employee ?: $emp;

            // ── Berichte (1-3 je Lead) ──
            foreach (range(1, rand(1, 3)) as $j) {
                DB::table('customer_histories')->insert([
                    'customer_id' => $l->customer_id, 'alternative_id' => $l->alternative_id, 'product_id' => $l->product_id,
                    'done_by' => $emp, 'marked_by' => $emp, 'is_done' => 1,
                    'notes' => $pick($berichte), 'done_date' => $now->copy()->subDays(rand(1, 90))->toDateString(),
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $nB++;
            }

            // ── Termine (1-2 je Lead) ──
            foreach (range(1, rand(1, 2)) as $j) {
                $start = $now->copy()->addDays(rand(-20, 30))->setTime(rand(8, 16), $pick([0, 30]));
                DB::table('main_appointments')->insert([
                    'lead_product_list_id' => $l->id, 'lead_stage_id' => $stage->id ?? null,
                    'created_by' => $field, 'name' => $pick($terminTyp) . ' – Lead #' . $l->customer_id,
                    'appointment_type' => $pick($terminTyp), 'contact_mode' => $pick($kontakt),
                    'note' => 'Termin zum Vorgang.', 'color' => '#16a34a',
                    'start_date' => $start->toDateString(), 'end_date' => $start->toDateString(),
                    'start_time' => $start->format('H:i:s'), 'end_time' => $start->copy()->addHour()->format('H:i:s'),
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $nT++;
            }

            // ── Notizen (1-2 je Lead) ──
            foreach (range(1, rand(1, 2)) as $j) {
                DB::table('customer_notes')->insert([
                    'customer_id' => $l->customer_id, 'alternative_id' => $l->alternative_id, 'product_id' => $l->product_id,
                    'lead_product_list_id' => $l->id, 'created_by' => $emp, 'description' => $pick($notizen),
                    'color' => '#f8ac00', 'stage' => $stageKey,
                    'lead_stage_id' => $stage->id ?? null, 'lead_stage_key' => $stageKey,
                    'lead_stage_name' => $stage->name ?? null, 'lead_stage_color' => $stage->color ?? null,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $nN++;
            }
        }

        $this->command->info('Lead-Aktivität: ' . $nB . ' Berichte, ' . $nT . ' Termine, ' . $nN . ' Notizen für ' . $leads->count() . ' Leads.');
    }
}
