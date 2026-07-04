<?php

namespace Database\Seeders\Testing;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Raeumt ALLE [TEST-HARNESS]-Zeilen restlos ab (FK-sichere Reihenfolge: Kinder zuerst).
 * Nutzt DB::table()->delete() (Hard-Delete) -> erwischt auch die soft-deletete Karte.
 * position_qualifications werden NICHT angefasst (Referenzdaten).
 * Am Ende: Zaehl-Query je Tabelle; verbleibt irgendwo eine Marker-Zeile -> Exception.
 */
class HarnessTeardownSeeder extends Seeder
{
    use HarnessSupport;

    public function run(): void
    {
        $this->guardLocal();

        $tag = self::TAG;
        $like = $tag . '%';

        // IDs der Marker-Zeilen einsammeln (fuer Kind-Tabellen ohne eigenes Textfeld)
        $empIds      = DB::table('employees')->where('lastname', $tag)->pluck('id')->all();
        $cardIds     = DB::table('kanban_lead_tasks')->where('title', 'LIKE', $like)->pluck('id')->all();
        $planIds     = DB::table('planner_plans')->where('title', 'LIKE', $like)->pluck('id')->all();
        $customerIds = DB::table('new_leads')->where('name', 'LIKE', $like)->pluck('id')->all();
        $itemIds     = DB::table('planner_items')
            ->where(function ($q) use ($like, $planIds) {
                $q->where('title', 'LIKE', $like);
                if ($planIds) {
                    $q->orWhereIn('plan_id', $planIds);
                }
            })->pluck('id')->all();

        // --- Loeschen: Kinder -> Eltern ---

        // 1) planner_item_employees — NUR wenn IDs vorhanden (leeres Array darf NICHT die ganze Tabelle loeschen)
        if ($itemIds || $empIds) {
            DB::table('planner_item_employees')
                ->where(function ($q) use ($itemIds, $empIds) {
                    if ($itemIds) {
                        $q->whereIn('planner_item_id', $itemIds);
                    }
                    if ($empIds) {
                        $q->orWhereIn('employee_id', $empIds);
                    }
                })
                ->delete();
        }

        // 2) planner_items (title-LIKE ist immer gesetzt -> nie ungeschuetzt)
        DB::table('planner_items')
            ->where(function ($q) use ($like, $planIds) {
                $q->where('title', 'LIKE', $like);
                if ($planIds) {
                    $q->orWhereIn('plan_id', $planIds);
                }
            })
            ->delete();

        // 3) planner_plans
        DB::table('planner_plans')->where('title', 'LIKE', $like)->delete();

        // 4) kanban_lead_task_employees — NUR wenn IDs vorhanden
        if ($cardIds || $empIds) {
            DB::table('kanban_lead_task_employees')
                ->where(function ($q) use ($cardIds, $empIds) {
                    if ($cardIds) {
                        $q->whereIn('kanban_lead_task_id', $cardIds);
                    }
                    if ($empIds) {
                        $q->orWhereIn('employee_id', $empIds);
                    }
                })
                ->delete();
        }

        // 5) kanban_lead_tasks (Hard-Delete, erwischt auch die soft-deletete Karte)
        DB::table('kanban_lead_tasks')->where('title', 'LIKE', $like)->delete();

        // 6) phase_activities
        DB::table('phase_activities')->where('title', 'LIKE', $like)->delete();

        // 7) task_phases
        DB::table('task_phases')->where('phase_name', 'LIKE', $like)->delete();

        // 7b) phase_sections (nach task_phases + phase_activities, die es referenzieren)
        DB::table('phase_sections')->where('phase_section', 'LIKE', $like)->delete();

        // 7c) personal_tasks (Follow-up-Traeger; marked task_title ODER Test-Kunde) — VOR lead_product_lists,
        //     damit keine source_id-Waisen bleiben. Stufe-E-Erweiterung (KanbanTestSeeder 3a).
        DB::table('personal_tasks')
            ->where(function ($q) use ($like, $customerIds) {
                $q->where('task_title', 'LIKE', $like);
                if ($customerIds) {
                    $q->orWhereIn('customer_id', $customerIds);
                }
            })
            ->delete();

        // 8) lead_product_lists (ueber die Test-Kunden) — NUR wenn Kunden-IDs vorhanden
        if ($customerIds) {
            DB::table('lead_product_lists')->whereIn('customer_id', $customerIds)->delete();
        }

        // 9) lead_alternative_adds (object_name-LIKE ist immer gesetzt -> nie ungeschuetzt)
        DB::table('lead_alternative_adds')
            ->where(function ($q) use ($like, $customerIds) {
                $q->where('object_name', 'LIKE', $like);
                if ($customerIds) {
                    $q->orWhereIn('lead_id', $customerIds);
                }
            })
            ->delete();

        // 10) new_leads
        DB::table('new_leads')->where('name', 'LIKE', $like)->delete();

        // 11) users (Marker-Domain)
        DB::table('users')->where('email', 'LIKE', '%' . self::USER_DOMAIN)->delete();

        // 12) employees
        DB::table('employees')->where('lastname', $tag)->delete();

        // 13) departments
        DB::table('departments')->where('department_name', 'LIKE', $like)->delete();

        // 14) article_groups
        DB::table('article_groups')->where('article_group', 'LIKE', $like)->delete();

        // --- Beweis: 0 Reste je Tabelle ---
        $rest = $this->remnants();
        $total = array_sum($rest);

        foreach ($rest as $table => $n) {
            $this->command?->line(sprintf('  %-30s Rest: %d', $table, $n));
        }

        if ($total > 0) {
            throw new \RuntimeException($tag . " Teardown unvollstaendig - verbleibende Marker-Zeilen: " . json_encode($rest));
        }

        $this->command?->info($tag . ' Teardown OK - 0 Reste in allen Tabellen.');
    }

    /** Zaehlt verbleibende Marker-Zeilen je Tabelle (auch fuer externe Verifikation nutzbar). */
    public function remnants(): array
    {
        $tag = self::TAG;
        $like = $tag . '%';

        return [
            'article_groups'             => DB::table('article_groups')->where('article_group', 'LIKE', $like)->count(),
            'departments'                => DB::table('departments')->where('department_name', 'LIKE', $like)->count(),
            'new_leads'                  => DB::table('new_leads')->where('name', 'LIKE', $like)->count(),
            'lead_alternative_adds'      => DB::table('lead_alternative_adds')->where('object_name', 'LIKE', $like)->count(),
            'task_phases'                => DB::table('task_phases')->where('phase_name', 'LIKE', $like)->count(),
            'phase_sections'             => DB::table('phase_sections')->where('phase_section', 'LIKE', $like)->count(),
            'phase_activities'           => DB::table('phase_activities')->where('title', 'LIKE', $like)->count(),
            'kanban_lead_tasks'          => DB::table('kanban_lead_tasks')->where('title', 'LIKE', $like)->count(),
            'planner_plans'              => DB::table('planner_plans')->where('title', 'LIKE', $like)->count(),
            'planner_items'              => DB::table('planner_items')->where('title', 'LIKE', $like)->count(),
            'employees'                  => DB::table('employees')->where('lastname', $tag)->count(),
            'users'                      => DB::table('users')->where('email', 'LIKE', '%' . self::USER_DOMAIN)->count(),
        ];
    }
}
