<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * DEMO-Master-Sets (Set-Bereich): je Gewerk ein Set mit Artikeln (Komponenten), Aufgaben und Arbeit/Labor.
 * Seedet die nötige Phasen-Basis mit (phase_sections -> task_phases -> phase_activities), da
 * master_set_tasks.phase_activity_id eine Pflicht-FK ist und diese Tabellen leer waren.
 *
 * Idempotent (Reset über Marker "[Set-Demo]"). Setzt Produkte/Artikel (DemoPartnersArticlesSeeder) voraus.
 */
class DemoMasterSetSeeder extends Seeder
{
    public function run(): void
    {
        $now  = Carbon::now();
        $pick = fn(array $a) => $a[array_rand($a)];
        $MARK = ' [Set-Demo]';

        $groups   = DB::table('article_groups')->pluck('id', 'article_group')->all(); // Name => id
        $deptPos  = DB::table('department_positions')->pluck('id')->all();
        $quals    = DB::table('position_qualifications')->pluck('id', 'name')->all();
        $empIds   = DB::table('employees')->where('email', 'like', '%@solar-aspekt-nord.test')->pluck('id')->all();
        if (! $groups || DB::table('products')->count() === 0) {
            $this->command->warn('Produkte/Artikel fehlen — bitte zuerst DemoPartnersArticlesSeeder ausführen.');
            return;
        }

        // ── Reset (idempotent) ─────────────────────────────────────────
        $oldSetIds = DB::table('master_sets')->where('description', 'like', '%[Set-Demo]%')->pluck('id')->all();
        if ($oldSetIds) {
            DB::table('master_set_components')->whereIn('master_set_id', $oldSetIds)->delete();
            DB::table('master_set_labor')->whereIn('master_set_id', $oldSetIds)->delete();
            $oldTaskIds = DB::table('master_set_tasks')->whereIn('master_set_id', $oldSetIds)->pluck('id')->all();
            if ($oldTaskIds) {
                DB::table('master_set_task_labors')->whereIn('master_set_task_id', $oldTaskIds)->delete();
            }
            DB::table('master_set_tasks')->whereIn('master_set_id', $oldSetIds)->delete();
            DB::table('master_sets')->whereIn('id', $oldSetIds)->delete();
        }
        DB::table('phase_activities')->where('description', 'like', '%[Set-Demo]%')->delete();
        DB::table('task_phases')->where('description', 'like', '%[Set-Demo]%')->delete();
        DB::table('phase_sections')->where('phase_section', 'like', '%[Set-Demo]%')->delete();

        $taskNames = ['Aufmaß / Vor-Ort-Aufnahme', 'Lieferung & Bereitstellung', 'Montage', 'Inbetriebnahme', 'Abnahme & Übergabe'];
        $nSet = 0; $nComp = 0; $nTask = 0; $nLab = 0;

        foreach ($groups as $gName => $gId) {
            // Phasen-Kette je Gewerk
            $sectionId = DB::table('phase_sections')->insertGetId([
                'product_id' => $gId, 'phase_section' => $gName . ' – Standardablauf' . $MARK, 'status' => 'active',
                'sort_order' => 1, 'created_at' => $now, 'updated_at' => $now,
            ]);
            $phaseId = DB::table('task_phases')->insertGetId([
                'product_id' => $gId, 'section_id' => $sectionId, 'section_name' => $gName . ' – Standardablauf',
                'phase_name' => 'Umsetzung', 'description' => 'Standard-Phase' . $MARK, 'status' => 'active', 'sort_order' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            // Komponenten = Artikel dieser Produktgruppe
            $articles = DB::table('products')->where('article_group', $gId)->limit(5)->get();
            $compNet  = 0.0;
            $setId = DB::table('master_sets')->insertGetId([
                'article_group_id' => $gId, 'task_phase_id' => $phaseId, 'name' => $gName . '-Set Standard',
                'description' => 'Standard-Baukasten ' . $gName . $MARK, 'status' => 'active',
                'responsible_department_position_id' => $deptPos ? $pick($deptPos) : null, 'creator_id' => $empIds ? $pick($empIds) : null,
                'main_total' => 0, 'sub_total' => 0, 'labor_total' => 0, 'total' => 0,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $nSet++;

            $sort = 1;
            foreach ($articles as $a) {
                $qty   = rand(1, 6);
                $price = (float) ($a->retail_price ?: rand(100, 2000));
                $compNet += $qty * $price;
                DB::table('master_set_components')->insert([
                    'master_set_id' => $setId, 'product_id' => $a->id, 'article_no' => $a->article_no,
                    'unit_price' => $price, 'purchase_price' => $a->purchase_price ?? round($price * 0.8, 2),
                    'type' => 'product', 'qty' => $qty, 'measure' => 'Stück', 'sort_order' => $sort++,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $nComp++;
            }

            // Aufgaben des Sets (referenzieren die Phasen-Aktivität)
            $sort = 1; $laborTotal = 0.0;
            foreach (array_slice($taskNames, 0, rand(3, 5)) as $tn) {
                $hours = rand(2, 16);
                $activityId = DB::table('phase_activities')->insertGetId([
                    'phase_id' => $phaseId, 'product_id' => $gId, 'section_id' => $sectionId, 'section_name' => $gName . ' – Standardablauf',
                    'title' => $tn, 'description' => 'Set-Aktivität' . $MARK, 'duration' => $hours, 'duration_type' => 'hours',
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                DB::table('master_set_tasks')->insert([
                    'master_set_id' => $setId, 'phase_activity_id' => $activityId, 'task_phase_id' => $phaseId,
                    'stage_name' => 'Umsetzung', 'phase_name' => 'Umsetzung', 'title' => $tn,
                    'description' => 'Set-Aufgabe', 'duration' => $hours, 'duration_type' => 'hours',
                    'priority' => 'normal', 'hours' => $hours, 'sort_order' => $sort++,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $nTask++;
            }

            // Arbeit / Labor des Sets
            $sort = 1;
            foreach ([['Meister', 70], ['Geselle', 45]] as $lab) {
                $h = rand(4, 24); $rate = $lab[1];
                $laborTotal += $h * $rate;
                DB::table('master_set_labor')->insert([
                    'master_set_id' => $setId, 'qualification_id' => $quals[$lab[0]] ?? null, 'department_id' => null,
                    'position_id' => null, 'employee_id' => null, 'hourly_rate' => $rate, 'hours' => $h, 'sort_order' => $sort++,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $nLab++;
            }

            // Summen am Set nachziehen
            DB::table('master_sets')->where('id', $setId)->update([
                'main_total' => round($compNet, 2), 'sub_total' => round($compNet, 2),
                'labor_total' => round($laborTotal, 2), 'total' => round($compNet + $laborTotal, 2),
            ]);
        }

        $this->command->info("Master-Sets: {$nSet} Sets, {$nComp} Artikel-Komponenten, {$nTask} Set-Aufgaben, {$nLab} Arbeit-Positionen (+ Phasen-Basis je Gewerk).");
    }
}
