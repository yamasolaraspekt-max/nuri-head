<?php

namespace Database\Seeders\Testing;

use App\Models\LeadStage;
use Illuminate\Database\Seeder;

/**
 * [TEST-HARNESS] Kanban-Durchklick-Bestand — dockt an den HarnessContext an.
 *
 * Erzeugt einen sichtbaren Board-Zustand fuer Yama (alle 6 Phasen befuellt, alle Pill-Typen,
 * die Zustands-Faelle und die B1-Netz-Regressionswaechter). KEIN Produktiv-Code.
 *
 * Szenarien:
 *   1. Je 1 Karte in allen 6 Phasen (Lead/Angebot/Auftrag/Montage/Abnahme/Abschluss) — status+FK
 *      konsistent (changeStage-Kanon). Abnahme = erste Karte ihrer Art (Spalte war leer).
 *   2. Montage-Karte mit Aufgaben in allen Status (open/done/reported+Pruefer) + ueberfaellig
 *      -> Offen/Erledigt/Pruefung/Aufgaben/Ueberfaellig-Pills alle sichtbar.
 *   3. Zustand: Angebot-Karte mit offenem Follow-up (Wiedervorlage MIT Datum);
 *      Alt-Karte status='follow_up' (Fold->Angebot, Wiedervorlage OHNE Datum); Lead-Karte = Kontrolle.
 *   4. B1-Netz: NULL-FK-Karte (Fallback+Log) + FK-auf-Zustands-Stage-Karte (FK-Fold).
 *
 * Idempotent (upsertId ueber Marker). Teardown ueber HarnessTeardownSeeder (inkl. personal_tasks-Erweiterung).
 */
class KanbanTestSeeder extends Seeder
{
    use HarnessSupport;

    public function run(): void
    {
        $this->guardLocal();

        $ctx = $this->context();
        $emp = $this->employees();
        $now = now();

        $stageId = LeadStage::query()->pluck('id', 'key')
            ->mapWithKeys(fn($id, $k) => [strtolower(trim((string) $k)) => (int) $id])
            ->toArray();

        // Board-Karte = eigenes Objekt (lead_alternative_adds) + lead_product_list; status+FK gesetzt (Kanon).
        // Raw-Upsert (kein Eloquent) -> B2-Hook feuert nicht -> FK bleibt exakt wie hier gesetzt (auch NULL/Zustands-Stage).
        $mkCard = function (string $suffix, string $status, ?int $leadStageId) use ($ctx) {
            $objId = $this->upsertId(
                'lead_alternative_adds',
                ['object_name' => $this->mark('Kanban ' . $suffix)],
                ['lead_id' => $ctx->customerId, 'street' => 'Teststrasse', 'address_no' => '1', 'postcode' => '00000', 'city' => 'Teststadt']
            );

            return $this->upsertId(
                'lead_product_lists',
                ['customer_id' => $ctx->customerId, 'product_id' => $ctx->productId, 'alternative_id' => $objId],
                ['status' => $status, 'stage' => $status, 'lead_stage_id' => $leadStageId, 'work_status' => 'playing']
            );
        };

        $mkTask = function (int $lplId, ?int $fk, string $suffix, string $status, array $extra = []) use ($ctx) {
            return $this->upsertId(
                'kanban_lead_tasks',
                ['lead_product_list_id' => $lplId, 'title' => $this->mark($suffix)],
                array_merge([
                    'lead_stage_id' => $fk, 'status' => $status, 'is_manual' => 1, 'is_scheduled' => 0,
                    'customer_id' => $ctx->customerId, 'product_id' => $ctx->productId,
                ], $extra)
            );
        };

        // (1) Je eine Karte in allen 6 Phasen
        $lead      = $mkCard('1 Lead',      'lead',      $stageId['lead'] ?? null);
        $angebot   = $mkCard('2 Angebot',   'offer',     $stageId['offer'] ?? null);
        $auftrag   = $mkCard('3 Auftrag',   'deal',      $stageId['deal'] ?? null);
        $montage   = $mkCard('4 Montage',   'project',   $stageId['project'] ?? null);
        $abnahme   = $mkCard('5 Abnahme',   'abnahme',   $stageId['abnahme'] ?? null);
        $abschluss = $mkCard('6 Abschluss', 'completed', $stageId['completed'] ?? null);

        // (2) Badge-Faelle auf der Montage-Karte
        $mkTask($montage, $stageId['project'] ?? null, 'Aufgabe offen', 'open');
        $mkTask($montage, $stageId['project'] ?? null, 'Aufgabe erledigt', 'done', ['done_by_employee_id' => $emp->qual]);
        $mkTask($montage, $stageId['project'] ?? null, 'Aufgabe Pruefung', 'reported', [
            'performer_employee_id' => $emp->unqual,
            'reviewer_employee_id' => $emp->reviewer,
        ]);
        // (5) ueberfaellige Aufgabe (planned_end_at in der Vergangenheit, offen)
        $mkTask($montage, $stageId['project'] ?? null, 'Aufgabe ueberfaellig', 'open', [
            'planned_end_at' => $now->copy()->subDays(3),
        ]);

        // (3) Zustands-Faelle
        // 3a: offenes Follow-up (Wiedervorlage MIT Datum) auf der Angebot-Karte
        $this->upsertId(
            'personal_tasks',
            ['type' => 'follow_up', 'source_type' => 'lead_product_list', 'source_id' => $angebot],
            [
                'task_status' => 'open',
                'task_title' => $this->mark('Wiedervorlage'),
                'due_date' => $now->copy()->addDays(7)->toDateString(),
                'customer_id' => $ctx->customerId,
            ]
        );
        // 3b: status='follow_up'-Altfall (FK=offer -> Fold nach Angebot, Pill OHNE Datum)
        $wvAlt = $mkCard('3b Wiedervorlage-Alt', 'follow_up', $stageId['offer'] ?? null);
        // 3c: Kontrolle ohne Signal = die Lead-Karte (kein Follow-up)

        // (4) B1-Netz-Regressionswaechter
        // 4a: NULL-FK (status gesetzt, FK NULL -> Rendering-Fallback + Log 'kanban fk-fallback')
        $nullFk = $mkCard('4a NULL-FK', 'offer', null);
        // 4b: FK auf Zustands-Stage (follow_up-Stage) -> Query-Fold nach Angebot
        $fkState = $mkCard('4b FK-Zustands-Stage', 'offer', $stageId['follow_up'] ?? null);

        $this->command?->info(self::TAG . " Kanban 6 Phasen: Lead=$lead Angebot=$angebot Auftrag=$auftrag Montage=$montage Abnahme=$abnahme Abschluss=$abschluss");
        $this->command?->info(self::TAG . " Zustand/Netz: WV-Datum(Angebot=$angebot) WV-Alt=$wvAlt | NULL-FK=$nullFk FK-Zustand=$fkState");
    }
}
