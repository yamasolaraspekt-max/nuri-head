# Planner-Item ↔ Büro-Kanban-Karte — harte Zuordnungsprüfung

> **Reine Analyse (nur Lesen), kein Code geändert.** EINE Frage, harte Belege, keine Vermutung als Tatsache: Existiert eine **eindeutige** Zuordnung zwischen einem `planner_item` (`source_type='phase_activity'`) und einer `kanban_lead_tasks`-Karte? Anlass: der Fix-Vorschlag (`monteur-rueckfluss-vier-ziele-befund.md`, Punkt 1) setzt das voraus.
>
> **Antwort vorweg: NEIN — strukturell nicht eindeutig (praktisch TEILWEISE).** Es gibt eine gemeinsame Achse `(lead_product_list_id, phase_activity_id)`, aber **keinen direkten FK**, **keinen Unique-Constraint**, **keinen Dedup** und **keinen bestehenden Verknüpfungs-Query**. Der Fix ist so **nicht sicher** — die „richtige" Karte ist nicht eindeutig bestimmbar.

---

## Frage 1 — Hat `kanban_lead_tasks` eine Spalte, die auf phase_activity/planner_item zeigt?

**JA auf `phase_activity` (nullable FK), NEIN auf `planner_item`.** Wörtlich aus `2026_06_05_222249_create_kanban_lead_tasks_tables.php` (vollständig gelesen):

```php
$table->foreignId('lead_product_list_id')
    ->constrained('lead_product_lists')
    ->cascadeOnDelete();
...
$table->foreignId('task_phase_id')
    ->nullable()
    ->constrained('task_phases')
    ->nullOnDelete();

$table->foreignId('phase_activity_id')
    ->nullable()
    ->constrained('phase_activities')
    ->nullOnDelete();
```

→ Referenz-Spalten: `lead_product_list_id`, `customer_id`, `alternative_id`, `product_id`, `lead_stage_id`, `lead_sub_stage_id`, **`task_phase_id`**, **`phase_activity_id`**. **Keine** `planner_item_id`-Spalte (im gesamten Migrations-Text nicht vorhanden). `phase_activity_id` ist **nullable** (manuelle Karten haben es `null`, s. `is_manual`).

## Frage 2 — Hat `planner_items` eine Spalte, die auf `kanban_lead_tasks` zeigt?

**NEIN.** Wörtlich aus `2026_01_21_083334_create_planner_items_table.php` (vollständig gelesen):

```php
// links back to original modules (phase activity / appointment / manual / ticket task / personal task)
$table->string('source_type', 60)->nullable()->index(); // phase_activity|appointment|manual|ticket_task|personal_task
$table->unsignedBigInteger('source_id')->nullable()->index();
...
$table->unique(['plan_id', 'source_type', 'source_id'], 'planner_items_unique_source_per_plan');

$table->foreign('plan_id')->references('id')->on('planner_plans')->onDelete('cascade');
```

Plus die ALTER-Migrationen (`…add_status…`, `…add_planner_status_history_and_done_meta`): `status`, `done_at`, `done_by_employee_id`, `last_status_changed_by_employee_id` usw. — **keine** `kanban_lead_task_id`- und **keine** `phase_activity_id`-Spalte. `planner_items` kennt die phase_activity **nur** indirekt über `source_type='phase_activity'` + `source_id`, und das Gewerk **nur** über `plan_id → planner_plans.project_id → lead_product_lists`.

## Frage 3 — Ist die gemeinsame Achse `(lead_product_list_id, phase_activity_id)` EINDEUTIG (1:1)?

**NEIN — keine Seite erzwingt Eindeutigkeit.**

**(a) `kanban_lead_tasks` hat KEINEN Unique-Constraint auf das Paar.** Die einzigen Indizes in der Migration (wörtlich):

```php
$table->index([
    'lead_product_list_id',
    'lead_stage_id',
    'lead_sub_stage_id',
    'status',
], 'klt_context_status_idx');

$table->index([
    'customer_id',
    'alternative_id',
    'product_id',
], 'klt_lead_product_context_idx');
```

→ Beides sind **`index()`, nicht `unique()`**, und **keiner** enthält `phase_activity_id`. Es gibt **keinen** Unique auf `(lead_product_list_id, phase_activity_id)`. (Der einzige `unique()` der Datei ist auf `kanban_lead_task_employees`: `['kanban_lead_task_id','employee_id','role']`.)

**(b) Die Erzeugung dedupliziert NICHT.** `KanbanLeadTaskController::storeFromTemplate` (Z.352–420 gelesen) legt die Karte immer per `create()` an — kein `firstOrCreate`/`updateOrCreate`/Existenz-Check:

```php
if (!empty($data['phase_activity_id'])) {
    ... ->where('id', (int) $data['phase_activity_id'])
        ->where('phase_id', (int) $phase->id) ...
}
...
$task = KanbanLeadTask::query()->create([
    'lead_product_list_id' => $leadProduct->id,
    ...
    'phase_activity_id' => $activity?->id,
    ...
]);
```

→ Zweimaliger Aufruf für dasselbe `(lead_product_list_id, phase_activity_id)` erzeugt **zwei** Karten. **>1 Kandidat ist möglich.** Ebenso **0 Kandidaten** (wenn nie eine Template-Karte erzeugt wurde, oder `is_manual`/`phase_activity_id=null`).

**(c) Auch planner-seitig nicht Gewerk-eindeutig:** `unique(['plan_id','source_type','source_id'])` ist **pro Plan** eindeutig — ein Gewerk kann aber **mehrere Pläne** haben (`planner_plans.stage = montage|inbetriebnahme`), also kann dieselbe phase_activity in **mehreren** planner_items desselben Gewerks liegen. *(Dass das in echten Daten vorkommt: **NICHT VERIFIZIERT** — das Schema erlaubt es.)*

→ **Fazit Q3: Die Zuordnung ist strukturell nicht 1:1.** Das Paar `(lead_product_list_id, phase_activity_id)` kann auf **0, 1 oder viele** kanban-Karten zeigen.

## Frage 4 — Gibt es im Code schon einen Query, der von planner_item/phase_activity die kanban-Karte findet?

**NEIN — keinen gefunden.** Grep „`kanban_lead_tasks` + `phase_activity_id` im selben Controller-Kontext" → **0 Treffer**. Die **einzige** kanban-Rückschreibung im Planner ist `pmoUpdateKanbanTaskSourceStatus` — und die findet die Karte **nicht** über `phase_activity_id`, sondern nimmt `source_id` **direkt als Primärschlüssel** der Karte (wörtlich, Z.2353):

```php
DB::table('kanban_lead_tasks')->where('id', $sourceId)->update($updates);
```

Und der Dispatch (wörtlich, Z.1775/1777):

```php
'kanban_task'    => $this->pmoUpdateKanbanTaskSourceStatus($sourceId, $status, $employeeId, $note),
...
'phase_activity' => $this->pmoUpdatePhaseActivityStatus($plan, $sourceId, $status, $employeeId, $note),
```

→ Die planner↔kanban-Verbindung existiert **nur**, wenn das planner_item **aus einer kanban-Karte** stammt (`source_type='kanban_task'`, dann `source_id = kanban_lead_tasks.id`). Für `source_type='phase_activity'` ist `source_id = phase_activities.id`, und es gibt **keinen** Code-Pfad, der daraus die kanban-Karte auflöst.
*(Ob eine Rück-Referenz in `planner_items.meta` oder `kanban_lead_tasks.meta` (JSON) steckt: **NICHT VERIFIZIERT** — die JSON-Inhalte wurden nicht inspiziert.)*

## Frage 5 — FAZIT: Ist der Fix sicher machbar?

**NEIN, nicht so wie vorgeschlagen.** Beim `phase_activity`-Abschluss die „zugehörige" kanban-Karte auf `done` zu setzen, setzt eine eindeutige Karte voraus — die es **strukturell nicht gibt**:
- **Kein direkter Link** (weder `planner_item_id` auf kanban noch `kanban_lead_task_id`/`phase_activity_id` auf planner_items).
- **Kein Unique** auf `(lead_product_list_id, phase_activity_id)` + **kein Dedup** in `storeFromTemplate` → **mehrere Karten** möglich (kein Tiebreaker, welche „die richtige" ist).
- **0 Karten** möglich (dann stiller No-Op — tolerierbar) — aber **>1** ist das gefährliche: der Fix würde raten oder mehrere Karten anfassen.
- **Kein bestehender Query** zum Auflösen — es müsste ein **neuer** Join auf `(lead_product_list_id, phase_activity_id)` gebaut werden, der eben nicht 1:1 ist.

**Damit der Fix sicher würde, müsste ZUERST eine der Voraussetzungen geschaffen werden** (Design-Entscheidung, nicht hier umgesetzt):
1. **Direkter Link:** beim Sync/Erzeugen eine Rück-Referenz setzen (z. B. `planner_items.meta.kanban_lead_task_id` oder eine echte Spalte), sodass der Abschluss die Karte **direkt** kennt — analog zum `kanban_task`-Pfad, der `source_id=id` schon nutzt.
2. **Eindeutigkeit erzwingen:** Unique-Index auf `kanban_lead_tasks(lead_product_list_id, phase_activity_id)` **plus** `firstOrCreate` in `storeFromTemplate` — dann wäre der Join eindeutig.

Ohne (1) oder (2) ist der Fix **Raten**.

---

## Gelesen / NICHT gelesen (ehrlich)

**Vollständig gelesen:**
- `database/migrations/2026_06_05_222249_create_kanban_lead_tasks_tables.php` (komplett, per `cat`).
- `database/migrations/2026_01_21_083334_create_planner_items_table.php` (komplett, per `cat`).
- ALTER `2026_01_24_122149_add_status_to_planner_items_table.php` + `2026_06_24_131829_add_planner_status_history_and_done_meta.php` (relevante `$table->`-Zeilen, nicht Datei-Rumpf).
- `KanbanLeadTaskController::storeFromTemplate` (Z.352–420).

**NICHT (vollständig) gelesen — mögliche Lücken meiner Analyse:**
- `PlannerPlanController` (~11.000 Z.) **nicht** end-to-end — nur gezielt gegrept (`kanban_lead_tasks`, `phase_activity_id`, `pmoUpdateKanbanTaskSourceStatus`). Ein Verknüpfungs-Query mit **variabler** Tabellen-/Spaltenbezeichnung könnte dem Grep entgehen. **NICHT AUSGESCHLOSSEN.**
- **JSON-`meta`-Inhalte** von `planner_items` und `kanban_lead_tasks` **nicht** inspiziert — eine Rück-Referenz dort ist **NICHT VERIFIZIERT** (weder bestätigt noch widerlegt).
- **Echte Daten** nicht abgefragt — ob faktisch je Gewerk+phase_activity genau eine Karte existiert, ist **NICHT VERIFIZIERT** (nur das Schema geprüft, das Mehrfachheit erlaubt).
- Das **Frontend** (Profil-JS) — ob es clientseitig eine eigene Zuordnung/Matching macht — **nicht** gelesen.

## Schwachstellen dieser Analyse (Selbstkritik, kein „passt")
- Die Aussage „kein Verknüpfungs-Query" beruht auf **Grep**, nicht auf vollständiger Lektüre des 11k-Zeilen-Controllers → sie ist **stark, aber nicht absolut**. Falls ein dynamisch gebauter Query existiert, wäre Q4 zu revidieren.
- Die Nicht-Eindeutigkeit ist **strukturell bewiesen** (kein Unique + `create()` ohne Dedup) — das ist hart. Ob sie **in der Praxis** zu Mehrfach-Karten führt, ist datenabhängig und hier nicht gemessen.
- „0 Karten" als tolerierbaren No-Op einzustufen ist meine **Bewertung**, keine Code-Aussage.

---

*Reine Analyse — nichts geändert. Wörtliche Belege aus: Migrationen `create_kanban_lead_tasks_tables`(2026_06_05_222249), `create_planner_items_table`(2026_01_21_083334), `add_status_to_planner_items`(2026_01_24), `add_planner_status_history_and_done_meta`(2026_06_24); `KanbanLeadTaskController::storeFromTemplate`(:352–420); `PlannerPlanController` `pmoUpdateKanbanTaskSourceStatus`(:2353), Dispatch(:1775/1777). Querverweis: `monteur-rueckfluss-vier-ziele-befund.md`, `monteur-rueckfluss-verknuepfungen-befund.md`, `kanban-ebenen-montage-planner-nuriva-befund.md`, `glossar.md`.*
