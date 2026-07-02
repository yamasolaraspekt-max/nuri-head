# Rückfluss Stufe 1 — Bau-Plan (Pflicht-Stopp, NOCH NICHT gebaut)

> **Reiner Plan, kein Code geändert.** Legt die exakte, minimale Bau-Sequenz für den fehlenden Link `planner_item(phase_activity) ↔ Büro-Karte (kanban_lead_tasks)` fest — die Voraussetzung für den Monteur-Rückfluss mit Projektleiter-Prüfschritt (Weiche 6). Runtime-Grundlage: `nuriva-montage-flow-durchgespielt.md` (6ef328f) — Weg 2 (phase_activity) ist bewiesen der einzige aktionierbare Montage-Weg. Stand 2026-07-02.

> **Warten auf Yamas OK vor JEDEM Code-Schritt.** Der Plan ist bewusst gestuft: **1a+1b = additiv/risikoarm** (Link + Eindeutigkeit, ändert kein Verhalten), **1c = verhaltensändernd** (Melde→Prüf→Bestätigt) → eigener Checkpoint dazwischen.

---

## Ziel & Richtung
Monteur schließt eine Montage-Aufgabe ab (Nuriva → `phase_activity`-planner_item) → das Büro soll die zugehörige **Karte** sehen und als **„gemeldet"** (≠ bestätigt) markiert bekommen; Projektleiter **bestätigt** → „erledigt". Dafür muss das System wissen, **welche Karte zu welcher Aufgabe** gehört. Der Link zeigt **vom planner_item auf die Karte**: neue Spalte **`planner_items.kanban_lead_task_id`**.

---

## STUFE 1a — Eindeutigkeit herstellen (Voraussetzung, additiv)

**Problem (belegt):** `storeFromTemplate` legt Karten per `create()` an — **kein Dedup** → mehrere Karten je (Gewerk, Aktivität) möglich → Link mehrdeutig. Heute unkritisch (**0 Zeilen**), also idealer Zeitpunkt.

**Migration M1** — Unique-Index auf `kanban_lead_tasks`:
```php
$table->unique(['lead_product_list_id', 'phase_activity_id'], 'klt_lpl_activity_unique');
```
- **Sicher, weil:** MySQL behandelt NULLs als verschieden → **manuelle Karten** (`storeManual` setzt `phase_activity_id = NULL`, belegt :392-393) sind **ausgenommen** (beliebig viele erlaubt). Nur Template-Karten mit gesetzter `phase_activity_id` werden eindeutig — genau gewünscht. 0 Bestandszeilen → kein Dedup nötig.

**Code C1** — [KanbanLeadTaskController::storeFromTemplate:469](app/Http/Controllers/Customer/Kanban/KanbanLeadTaskController.php#L469): `KanbanLeadTask::create([...])` → **`firstOrCreate`** auf `['lead_product_list_id'=>…, 'phase_activity_id'=>$activity?->id]` (nur wenn `phase_activity_id` nicht null; sonst weiter `create()` für Phase-/Manuell-Karten). Macht die Übernahme **idempotent** (zweiter Klick = dieselbe Karte). Rest der Payload in den `firstOrCreate`-Defaults.

---

## STUFE 1b — Der Link (das eigentliche Stufe-1-Ziel, additiv)

**Migration M2** — Spalte auf `planner_items`:
```php
$table->unsignedBigInteger('kanban_lead_task_id')->nullable()->after('source_id')->index();
// FK: OFFEN (Design-Entscheidung, s.u.) — entweder ohne FK (lose, robust) ODER
// ->constrained('kanban_lead_tasks')->nullOnDelete()  (integritätssicher, koppelt)
```

**Code C2 — Link im Sync setzen (gebündelt, kein N+1).** In [syncPhaseActivities:466](app/Http/Controllers/Planner/PlannerPlanController.php#L466) **vor** der Phasen-Schleife eine Karten-Map laden (ein Query je Sync, nicht je Aktivität):
```php
$cardMap = [];
if (Schema::hasTable('kanban_lead_tasks')) {
    $cardMap = DB::table('kanban_lead_tasks')
        ->where('lead_product_list_id', (int) $project->id)   // project->id = lead_product_lists.id = plan.project_id
        ->whereNotNull('phase_activity_id')
        ->when($this->safeColumn('kanban_lead_tasks','deleted_at'), fn($q)=>$q->whereNull('deleted_at'))
        ->pluck('id', 'phase_activity_id')->all();             // [phase_activity_id => card_id]
}
```
Im Aktivitäts-Zweig ([:545](app/Http/Controllers/Planner/PlannerPlanController.php#L545)) den Wert in die Payload geben: `'kanban_lead_task_id' => $cardMap[$activityId] ?? null`.
In [pmoUpsertTemplatePlannerItem:768](app/Http/Controllers/Planner/PlannerPlanController.php#L768) vor `$item->save()` (analog zu den bestehenden `safeColumn`-Guards für `done_at`):
```php
if ($this->safeColumn('planner_items','kanban_lead_task_id') && array_key_exists('kanban_lead_task_id',$payload) && $payload['kanban_lead_task_id']) {
    $item->kanban_lead_task_id = (int) $payload['kanban_lead_task_id'];
}
```
*(Zu bestätigen beim Bau: dass `$this->planProject($plan)->id` wirklich die lead_product_list-id ist — sehr wahrscheinlich, da `plan.project_id = lpl.id`; im Bau per SELECT verifizieren.)*

**Code C3 — Rückrichtung bei Karten-Anlage** (deckt „Karte entsteht NACH dem Sync" ab). In `storeFromTemplate` nach dem `firstOrCreate`: das passende `phase_activity`-planner_item best-effort verlinken:
```php
if ($activity && $this->safeColumn('planner_items','kanban_lead_task_id')) {
    DB::table('planner_items')
      ->where('source_type','phase_activity')->where('source_id',$activity->id)
      ->whereIn('plan_id', DB::table('planner_plans')->where('project_id',$leadProduct->id)->pluck('id'))
      ->update(['kanban_lead_task_id' => $task->id]);
}
```
→ Beide Seiten pflegen den Link best-effort; der häufige Sync (bei jedem Planner-Öffnen) heilt Lücken.

**Nach 1a+1b: additiver Zustand, KEIN Verhaltenswechsel.** Der Link existiert, wird aber noch nicht genutzt. Guter Checkpoint für Yamas Zwischen-OK.

---

## STUFE 1c — Melde→Prüf→Bestätigt (verhaltensändernd, eigener Checkpoint)

**Neuer Karten-Status „gemeldet"** — `kanban_lead_tasks.status` ist ein **freier String** (`->string('status')->default('open')`, kein Enum) → additiv, keine Migration nötig.

**Code C4 — Rückfluss beim Abschluss.** In [completeItemWithReport:1682](app/Http/Controllers/Planner/PlannerEmployeeApiController.php#L1682) (bzw. `markPlannerSourceDone`): wenn das abgeschlossene planner_item `kanban_lead_task_id` gesetzt hat → die Karte auf **`status = 'reported'`** (gemeldet) setzen (NICHT `done`), plus optional `internal_note` (Bericht) + Zeitstempel. Der phase_activity→Kundenhistorie-Writeback bleibt unverändert; der Karten-Update kommt **additiv** dazu.

**Code C5 — Office-Bestätigung (Projektleiter).** [KanbanLeadTaskController::updateStatus:519](app/Http/Controllers/Customer/Kanban/KanbanLeadTaskController.php#L519): `Rule::in([...])` um **`'reported'`** erweitern; Projektleiter-Aktion `'reported' → 'done'` (bestätigt). Optional eigener `confirm()`-Endpunkt statt generischem updateStatus (sauberer Audit-Trail „bestätigt von").

**Code C6 — Sichtbarkeit.** Kanban-Board/Dashboard: „gemeldet"-Karten als eigene Spalte/Badge „zu prüfen" anzeigen (UI, Umfang je nach Board-Struktur — im Bau eingrenzen).

*(Genau HIER ist der Wert für Yama: das Büro sieht endlich, was der Monteur draußen gemeldet hat, und muss es bestätigen. 1a/1b sind nur das Fundament dafür.)*

---

## SEED-TEST-PLAN (Verifikation je Stufe, wie beim Durchgespielt-Test)
Harness wiederverwenden (Gewerk 53, Phase 2, Aktivitäten 2–4, Emp 122/User 126); temporär, Baseline danach wiederherstellen.

1. **Nach M1/C1:** zweimal `storeFromTemplate` für dieselbe (Gewerk, Aktivität) → **genau 1 Karte** (firstOrCreate greift); manuelle Karte (activity=null) daneben weiterhin möglich. Unique-Index hält.
2. **Nach M2/C2/C3:** `syncAndLoad` → assert `planner_items.kanban_lead_task_id == card.id` für das phase_activity-Item; Gegentest „Karte erst nach Sync anlegen" → C3 setzt den Link nachträglich.
3. **Nach C4:** Nuriva `complete-report` auf das verlinkte Item → assert Karte `status == 'reported'` (**nicht** `done`), planner_item `done`, Kundenhistorie +1 (wie gehabt).
4. **Nach C5:** Office `updateStatus 'reported'→'done'` → Karte `done`.
5. **Regression:** Progressbar `montageFieldProgress` (zählt planner_items `phase_activity` nach Status) unverändert; my-work weiter nur phase_activity; `migrate:rollback` stellt M1/M2 sauber zurück.
6. **Cleanup:** alle Seed-Daten entfernen, Baseline = 0.

---

## RISIKEN & offene Design-Entscheidungen
- **FK ja/nein auf `kanban_lead_task_id`** (Design-Entscheidung Yama): *ohne* FK = robust/lose (verwaiste id möglich, aber harmlos, Sync heilt); *mit* `nullOnDelete` = integritätssicher, koppelt planner_items an kanban_lead_tasks. **Empfehlung: ohne FK** (lose Kopplung, weniger Migrations-Risiko), Link ist nur ein Hinweis.
- **Unique-Index nur heute billig** (0 Zeilen). Würde M1 je auf befüllten Daten laufen, bräuchte es zuerst Dedup. → M1 **jetzt** ziehen, solange leer.
- **Randfall manuelle + Template-Karte für dieselbe Aktivität:** manuelle Karten haben `phase_activity_id=null` → kollidieren nie mit dem Unique-Index. Verifiziert im Seed-Test (Schritt 1).
- **Nuriva/meta unberührt:** Link ist eine **Spalte**, kein `meta`-Key → der latente `meta`-TypeError (Option B, s. `rueckfluss-stufe1-link-vorbereitung.md`) wird **nicht** aktiviert. Bewusst Option C.
- **kanbanTasksPayload SQL-Crash** (`laa.name`, HTTP 500) ist **orthogonal** — Stufe 1 fasst die kanban_task-Projektion nicht an. Bleibt eigener Fix-Kandidat.
- **`markPlannerSourceDone` hat bereits einen `kanban_task`-Zweig** (source_id=Karten-id) — der ist für unsere phase_activity-Items **nicht** einschlägig; C4 ergänzt den Karten-Update über den **neuen Link** (nicht über source_type). Kein Konflikt, aber beim Bau sauber trennen.
- **Reihenfolge M1 vor M2** (erst Eindeutigkeit, dann Link) — sonst könnte der Link auf eine mehrdeutige Karte zeigen.

---

## VORGESCHLAGENE BAU-SEQUENZ (je eigener Commit, Pflicht-Stopp-Kette)
1. **M1 + C1** (Eindeutigkeit) → Seed-Test 1 → Commit.
2. **M2 + C2 + C3** (Link) → Seed-Test 2 → Commit. **← Zwischen-Checkpoint (additiv, kein Verhaltenswechsel).**
3. **C4** (Karte → „gemeldet") → Seed-Test 3 → Commit.
4. **C5 (+C6)** (Projektleiter-Bestätigung + Sichtbarkeit) → Seed-Test 4 → Commit.

Jeder Schritt: kleiner Auftrag → Befund/Verifikation → dein OK → nächster. **Kein Schritt ohne OK.**

---

## Gelesen / NICHT gelesen (ehrlich)
**Wörtlich geprüft:** `storeFromTemplate` (:427-514, create-Payload, kein Dedup) + `storeManual` (activity=null); `kanban_lead_tasks`-Migration (kein Unique auf der Achse, `status` freier String); `syncPhaseActivities` (:466-558) + `pmoUpsertTemplatePlannerItem` (:768-818, safeColumn-Muster); `completeItemWithReport` (:1682-1857) + `markPlannerSourceDone` (source_type-Zweige) + `updateStatus` (:516, Rule::in-Liste); `planner_items`-`meta`/Casts (aus Vorbefunden). Runtime-Grundlage: `nuriva-montage-flow-durchgespielt.md`.
**NICHT verifiziert (im Bau zu prüfen):** dass `planProject($plan)->id` die lead_product_list-id ist (sehr wahrscheinlich); ob `montageFieldProgress` o.ä. Zähl-Queries durch die neue Spalte irgendwo `SELECT *`-seitig beeinflusst werden (unwahrscheinlich); die genaue Board-UI-Struktur für C6 (Sichtbarkeit); ob es weitere Karten-Erzeuger als storeFromTemplate/storeManual gibt (Grep im Bau).

## Selbstkritik
- **Der Plan ist größer als „ein Feld ergänzen".** Der reine Link (1a/1b) ist klein und additiv; der eigentliche Nutzen (Melde→Prüf→Bestätigt, 1c) ist verhaltensändernd und UI-behaftet — ehrlich als eigene Stufe ausgewiesen, nicht schöngeredet.
- **C3 (Rückrichtung) ist best-effort**, kein Transaktions-Garant: theoretisch könnte zwischen Karten-Anlage und nächstem Sync ein Fenster ohne Link bestehen. Für den Rückfluss unkritisch (der Sync läuft bei jedem Öffnen), aber kein „harter" 1:1-Zwang. Falls Härte gewünscht: den Link ausschließlich im Sync führen und die Karte als sekundär betrachten.
- **„gemeldet" als String-Status** ist bewusst additiv, aber es fügt der ohnehin uneinheitlichen Status-Landschaft einen weiteren Wert hinzu — passt zu Weiche 1 (Zustand), sollte dort später eingeordnet werden, nicht als isolierter Sonderfall verwildern.

---

*Reiner Bau-Plan — nichts gebaut. Querverweise: `nuriva-montage-flow-durchgespielt.md` (Runtime-Beweis Weg 2), `rueckfluss-stufe1-link-vorbereitung.md` (Optionen A–D, Nuriva-meta-Risiko), `architektur-entscheidungen.md` (Weiche 6), `fahrplan-ticket-crm.md` (Ebene 1.1).*
