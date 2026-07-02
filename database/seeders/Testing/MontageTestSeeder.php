<?php

namespace Database\Seeders\Testing;

use Illuminate\Database\Seeder;

/**
 * Montage-Szenarien auf dem Test-Kontext. Jede planner_items-Zeile traegt den
 * 1b-Link (source_type='phase_activity' + source_id=<Taetigkeit> +
 * kanban_lead_task_id=<Karte>) UND eine pie-Zuordnung (completeItemWithReport
 * verlangt sie) - sonst feuert der B3-Rueckfluss nie.
 *
 * Karten:
 *   A offen, ohne Mitarbeiter                         (Fixture: unassigned)
 *   B offen, qualifizierter Monteur   -> Kette -> done
 *   C offen, unqualifizierter Monteur -> Kette -> reported + Pruefer
 *   D offen, Taetigkeit OHNE Anforderung -> Kette -> done (auch unqualifiziert)
 *   E bereits done                    -> Zustands-Guard laesst es
 *   F soft-deleted                    -> SoftDelete-Guard laesst es
 *
 * Je verlinktem Item ein eigener Plan wegen unique(plan_id, source_type, source_id).
 * Ein Item (B) traegt befuelltes meta (Bug-A-Regressionswaechter).
 */
class MontageTestSeeder extends Seeder
{
    use HarnessSupport;

    public function run(): void
    {
        $this->guardLocal();

        $ctx = $this->context();
        $emp = $this->employees();

        foreach (['qual', 'unqual', 'reviewer', 'other'] as $role) {
            if (empty($emp->{$role})) {
                throw new \RuntimeException(self::TAG . " Mitarbeiter '$role' fehlt - QualifikationTestSeeder zuerst ausfuehren.");
            }
        }

        $now = now();

        $cardBase = [
            'lead_product_list_id' => $ctx->lplId,
            'customer_id'          => $ctx->customerId,
            'alternative_id'       => $ctx->objectId,
            'product_id'           => $ctx->productId,
            'task_phase_id'        => $ctx->phaseId,
            'phase_activity_id'    => $ctx->activityReqId,
            'is_manual'            => 0,
            'is_scheduled'         => 0,
            'photo_required'       => 0,
        ];

        // --- Karten (mutable Felder werden bei jedem Seed auf den Soll-Startzustand gesetzt) ---
        $cardA = $this->upsertId('kanban_lead_tasks',
            ['title' => $this->mark('Karte A (offen, ohne MA)')],
            array_merge($cardBase, ['status' => 'open', 'performer_employee_id' => null, 'reviewer_employee_id' => null, 'done_at' => null, 'done_by_employee_id' => null, 'deleted_at' => null]));

        $cardB = $this->upsertId('kanban_lead_tasks',
            ['title' => $this->mark('Karte B (offen, qualifiziert -> done)')],
            array_merge($cardBase, ['status' => 'open', 'performer_employee_id' => $emp->qual, 'reviewer_employee_id' => null, 'done_at' => null, 'done_by_employee_id' => null, 'deleted_at' => null]));

        $cardC = $this->upsertId('kanban_lead_tasks',
            ['title' => $this->mark('Karte C (offen, unqualifiziert -> reported)')],
            array_merge($cardBase, ['status' => 'open', 'performer_employee_id' => $emp->unqual, 'reviewer_employee_id' => null, 'done_at' => null, 'done_by_employee_id' => null, 'deleted_at' => null]));

        $cardFree = $this->upsertId('kanban_lead_tasks',
            ['title' => $this->mark('Karte D (frei, keine Anforderung -> done)')],
            array_merge($cardBase, ['phase_activity_id' => $ctx->activityFreeId, 'status' => 'open', 'performer_employee_id' => $emp->unqual, 'reviewer_employee_id' => null, 'done_at' => null, 'done_by_employee_id' => null, 'deleted_at' => null]));

        $cardDone = $this->upsertId('kanban_lead_tasks',
            ['title' => $this->mark('Karte E (bereits done, Zustands-Guard)')],
            array_merge($cardBase, ['status' => 'done', 'performer_employee_id' => $emp->unqual, 'reviewer_employee_id' => null, 'done_at' => $now, 'done_by_employee_id' => $emp->unqual, 'deleted_at' => null]));

        $cardDeleted = $this->upsertId('kanban_lead_tasks',
            ['title' => $this->mark('Karte F (soft-deleted, Guard)')],
            array_merge($cardBase, ['status' => 'open', 'performer_employee_id' => $emp->unqual, 'reviewer_employee_id' => null, 'done_at' => null, 'done_by_employee_id' => null, 'deleted_at' => $now]));

        // --- klte fuer die zwei Haupt-Karten (Team-Anzeige / kanbanTaskEmployees-Pfad) ---
        foreach ([[$cardB, $emp->qual], [$cardC, $emp->unqual]] as [$cid, $eid]) {
            $this->upsertId('kanban_lead_task_employees',
                ['kanban_lead_task_id' => $cid, 'employee_id' => $eid],
                ['role' => 'performer', 'status' => 'assigned', 'assigned_at' => $now]);
        }

        // --- Plan + Item (mit 1b-Link) + pie ---
        $mkPlan = fn(string $suffix) => $this->upsertId('planner_plans',
            ['title' => $this->mark($suffix)],
            ['customer_id' => $ctx->customerId, 'project_id' => $ctx->lplId, 'stage' => 'project', 'status' => 'active']);

        $mkItem = function (int $planId, int $sourceId, int $cardId, int $performerId, string $suffix, ?string $meta = null) {
            $itemId = $this->upsertId('planner_items',
                ['plan_id' => $planId, 'source_type' => 'phase_activity', 'source_id' => $sourceId],
                ['kanban_lead_task_id' => $cardId, 'title' => $this->mark($suffix), 'status' => 'planned',
                 'duration_minutes' => 60, 'sort_order' => 1, 'client_uid' => 'th-' . strtolower(str_replace(' ', '-', $suffix)), 'meta' => $meta]);

            // completeItemWithReport verlangt eine pie-Zuordnung des Mitarbeiters
            $this->upsertId('planner_item_employees',
                ['planner_item_id' => $itemId, 'employee_id' => $performerId],
                ['role' => 'performer']);

            return $itemId;
        };

        // Item B traegt befuelltes meta -> Bug-A-Regressionswaechter (sync() muss Array-meta vertragen)
        $itemB = $mkItem($mkPlan('Plan B'), $ctx->activityReqId, $cardB, $emp->qual, 'Item B', json_encode(['seed' => 'bug-a-regression', 'note' => 'meta befuellt']));
        $itemC = $mkItem($mkPlan('Plan C'), $ctx->activityReqId, $cardC, $emp->unqual, 'Item C');
        $itemD = $mkItem($mkPlan('Plan D'), $ctx->activityFreeId, $cardFree, $emp->unqual, 'Item D');
        $itemE = $mkItem($mkPlan('Plan E'), $ctx->activityReqId, $cardDone, $emp->unqual, 'Item E');
        $itemF = $mkItem($mkPlan('Plan F'), $ctx->activityReqId, $cardDeleted, $emp->unqual, 'Item F');

        $this->command?->info(self::TAG . " Karten: A=$cardA B=$cardB C=$cardC D=$cardFree E=$cardDone F=$cardDeleted");
        $this->command?->info(self::TAG . " Items (1b-verlinkt): B=$itemB C=$itemC D=$itemD E=$itemE F=$itemF; meta befuellt auf Item B");
    }
}
