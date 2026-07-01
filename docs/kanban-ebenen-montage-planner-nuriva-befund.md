# Zwei Kanban-Ebenen + Montage → Planner → Nuriva — Code-Befund

> **Reine Analyse (nur Lesen), kein Code geändert.** Prüft Yamas Bild: (1) 6 Hauptphasen in EINEM Kanban, (2) je Hauptphase ein zweites Kanban (Unterphasen), (3) in der Montage-Phase erstellte Aufgaben gehen über den Planner an Nuriva. Baut auf `struktur-systeme-verhaeltnis-befund.md`. Begriffe: `glossar.md` (Gewerk=`lead_product_lists`).
>
> **Kurzfazit: Yamas Bild stimmt — belegt.** (1)+(2) sind ein zweistufiges Lead-Kanban aus **`lead_stages`** (6 Hauptphasen) × **`lead_stage_sub_stages`** (Unterphasen), Karten = Gewerke (`lead_product_lists`). (3) Die Montage läuft **Planner-geführt**: der Planner **synct** die Template-Aufgaben (`phase_activities`) in **`planner_items`**, Nuriva liest sie über `/api/planner/*`, und der Status/Foto fließt **zurück** auf die Aufgabe + einen Completion-Report. **Planner ist das führende Ausführungssystem der Montage** (weil Nuriva-angebunden und Aggregator aller Quellen). **Offen bleibt v. a., WO genau Yama die Montage-Aufgabe anlegt** — davon hängt ab, ob sie Nuriva überhaupt erreicht (siehe „Offene Fragen").

---

## 1. Die zwei Kanban-Ebenen (belegt)

**Ebene 1 — Hauptphasen-Kanban (die 6 Phasen):**
- Spalten = **`lead_stages`** (`2026_05_28_144108`): `key`, `name`, `color`, `icon`, `sort_order`, `is_default`, `is_protected`, `is_closed`, `is_active`. → dynamisch gepflegte 6-Phasen-Spalten (Lead→Angebot→Auftrag→Montage→Abnahme→Abschluss).
- Karten = **Gewerke** `lead_product_lists`, positioniert über **`lead_product_lists.lead_stage_id`**.
- Controller/View: `Customer/Kanban/LeadOverviewController::kanban` (`:219`) + `kanbanFeed` (`:444`), „Dynamic stage labels from lead_stages" (`:143`). Admin der Phasen: `Phase/LeadStageAdminController`.

**Ebene 2 — Unterphasen-Kanban (innerhalb einer Hauptphase):**
- Spalten = **`lead_stage_sub_stages`** (`2026_06_05_101346`): `lead_stage_id` (FK zur Hauptphase), `key`, `name`, `color`, `icon`, `sort_order`, `is_default`, `is_active`, unique(`lead_stage_id`,`key`). → **je Hauptphase eigene Unterspalten**.
- Karten = **dasselbe Gewerk**, positioniert über **`lead_product_lists.lead_stage_sub_stage_id`** (`2026_06_05_113119`).
- **Beide Ebenen in EINER Datenstruktur:** `LeadOverviewController::kanban` liefert je Stage die eingebetteten **`sub_stages`** (`:249` `$stage->subStages->map(...)` → id/key/name/color/icon/sort/…). D. h. der Feed ist `stages[]` **mit** `sub_stages[]` — das Frontend rendert Hauptspalten und darin die Unterspalten. Verwaltung: View `admin/kanban/stage-sub-stages/index.blade.php`.

→ **Bild (1)+(2) bestätigt:** Ein Gewerk trägt gleichzeitig `lead_stage_id` (Hauptphase) **und** `lead_stage_sub_stage_id` (Unterphase); das Kanban ist zweistufig (Hauptphasen-Spalten, je Hauptphase Unterphasen). *(Die genaue UI-Interaktion — Drill-down vs. Swimlanes — ist vom Datenmodell voll gestützt; die Blade-Render-Details wurden nicht Zeile für Zeile gelesen.)*

---

## 2. Wie entstehen die Aufgaben je (Unter-)Phase?

**Die Aufgaben-Karten je Gewerk = `kanban_lead_tasks`** (`Customer/Kanban/KanbanLeadTaskController`). Herkunft:
- **Aus Template A:** `storeFromTemplate` (`:352`) — validiert `task_phase_id` (exists:task_phases) + optional `phase_activity_id` (exists:phase_activities) und legt `kanban_lead_tasks` mit `task_phase_id`+`phase_activity_id` an (`source='task_phase_template'`, `:417`). → **die (Unter-)Phasen-Aufgaben stammen aus `task_phases`/`phase_activities` (System A)**, gefiltert je Gewerk.
- **Manuell:** `storeManual` (`:284`) — `task_phase_id=null`, `phase_activity_id=null`.
- `context`/`summaries` (`:28`/`:114`) mischen gespeicherte Tasks + Template (`'source' => saved_task | task_phase`, `:279`).
- **Brücken zu anderen Systemen:** derselbe Controller erzeugt auch `PersonalTask` (`:736`) + `PersonalTaskKey` (`:774`) + `MainAppointment` (`:809`) → eine Kanban-Aufgabe kann zu einer persönlichen Aufgabe oder einem Termin werden (relevant für Planner, s. u.).

→ Das per-Gewerk-Aufgabenboard (im Kundenprofil der `customerKanbanTaskDrawer`) ist eine **Instanz von Template A**. Es ist **nicht** dasselbe wie die Planner-Items (Ebene 3, Ausführung).

---

## 3. Montage → Planner → Nuriva (Schritt für Schritt, belegt)

**Schritt 1 — Gewerk wird Planner-„Projekt".** Über das Projekt-Cockpit: `storeProjectFromLeadProduct` (`web.php:5188`), `ensureProjectPlan` (`:5191`), `moveProjectKanban` (`:5194`). Ein Gewerk (`lead_product_lists`) wird als Planner-Projekt geführt.

**Schritt 2 — Plan sicherstellen.** `PlannerPlan::firstOrCreate(...)` (`PlannerPlanController:420`, u. a. `:4123`, `:4420`, `:5956`) — `planner_plans` mit `customer_id→new_leads`, `project_id→lead_product_lists`, **`stage` = `montage | inbetriebnahme`**. → **kein automatischer Trigger bei Phaseneintritt gefunden**; der Plan entsteht **on-demand** (firstOrCreate beim Öffnen/Sichern).

**Schritt 3 — Template A wird in Planner-Items gesynct.** `syncAndLoad` (`:402`) ruft `pmoUpsertTemplatePlannerItem($plan, 'task_phase', $phaseId, …)` und `…'phase_activity', $activityId, …` (`:545`, Kontext `:500–560`). Der Upsert (`:770–808`) schreibt `planner_items` mit `source_type`/`source_id`, dedupliziert per unique(`plan_id`,`source_type`,`source_id`). **Planner ist Aggregator:** zusätzlich `source_type` = `master_set` (`:845`), `appointment` (`:910`), `ticket` (`:990`), `personal_task` (`:1069`). → **planner_items = phase_activities (A) + Termine + Tickets + persönliche Aufgaben + Master-Sets in EINER Ausführungsliste.**
*(Ob der Sync die phase_activities nach Plan-Stage=montage filtert, ist wahrscheinlich, wurde aber nicht abschließend als WHERE-Klausel belegt — als Prüfpunkt markiert.)*

**Schritt 4 — Nuriva liest.** `PlannerEmployeeApiController::myWork` (`:19`, Route `GET /api/planner/my-work`) liest `planner_items` (`:233`), filtert nach Mitarbeiter + offen (`:258`). Pro Item an die App: `source_type`, `title`, `status`, `is_done` (`:426–433`) + **Material, Kommentare, Bilder** (`:169–191`), Abhängigkeiten. `myDayReport` (`:45`) aggregiert erledigt/offen + Material/Kommentare/Bilder zu einem Tagesbericht.

**Schritt 5 — Status/Foto fließt zurück.** Monteur schließt ab → `completeItemWithReport` (Route `PATCH /api/planner/items/{item}/complete-report`) → im Controller: `pmoUpdatePhaseActivityStatus` (`:1777`, für `source_type='phase_activity'`) schreibt `is_done`/`status`/`done_by`/`done_date`/`note` **zurück auf die Aufgaben-Instanz** (`:1383–1391`, K-Block: `is_done`/`done_by`/`done_date`/`status`); `pmoStoreCustomerCompletionReport` (`:1814` für `phase_activity`/`kanban_task`/`task_phase`/`master_set`) legt einen **Completion-Report** an; Fotos über `PlannerMobileCustomerImageController::upload` (`/api/planner/customer-images/upload`) in die zentrale `images`-Tabelle (kunden-/objektbezogen).

→ **Bild (3) bestätigt:** Montage-Aufgaben gehen über den Planner (`planner_items`) an Nuriva; Status + Foto laufen zurück auf die Aufgabe + einen Report. **Planner ist das führende Ausführungssystem der Montage.**

---

## 4. Arbeitsteilung der drei Ausführungs-Systeme in der Praxis

Es ist **teils Arbeitsteilung, teils echte Doppelung**:

| Zweck | System | Bezug |
|---|---|---|
| **Pipeline-Position** (in welcher der 6 Phasen + Unterphase steht das Gewerk) | 2-stufiges **Lead-Kanban** (`lead_stages`×`lead_stage_sub_stages` auf `lead_product_lists`) | über alle Phasen |
| **Büro-Aufgaben je Gewerk** (Planung/Checkliste im CRM) | **`kanban_lead_tasks`** (KanbanLeadTaskController, `customerKanbanTaskDrawer`) | aus Template A |
| **Feld-/Montage-Ausführung** (Terminierung, Monteur, Status, Foto) | **`planner_items`** (Planner), **Nuriva-angebunden** | Aggregat: A + Termine + Tickets + personal_task + master_set |
| **ältere Kunden-Phasenliste** (Fortschritt) | **`customer_phase_lists`** | Instanz von A, teils Rückschreib-Ziel |

**Arbeitsteilung (sauber):** Pipeline-Position ≠ Büro-Aufgaben ≠ Feld-Ausführung — drei unterschiedliche Zwecke. Nuriva hängt **nur am Planner**.

**Echte Doppelung (Risiko „mehrere Wahrheiten"):** `kanban_lead_tasks` **und** `planner_items` **und** `customer_phase_lists` instanziieren teils **dieselbe `phase_activity`**. Der Planner mildert das, indem er den Status **zurückschreibt**; aber jedes System hat **eine eigene Status-Spalte**, und eine im Büro-Kanban (`kanban_lead_tasks`) angelegte Aufgabe erscheint **nicht automatisch** im Planner/Nuriva (nur wenn sie als `personal_task`/`appointment`/manuelles planner_item existiert). → Die „eine Wahrheit" je Aufgabe ist **nicht garantiert**.

---

## 5. Fazit + offene Fragen fürs „Aufgaben in der Montage-Phase erstellen"

**Stimmt Yamas Bild?** — **Ja, in allen drei Punkten belegt.** Zweistufiges Kanban (lead_stages × sub_stages), Montage-Aufgaben via Planner → Nuriva, Status-Rückfluss. **Abweichung:** die „Aufgaben" sind kein einzelner Tabellen-Typ — je nach Anlage-Weg landen sie in `kanban_lead_tasks`, `phase_activities`, `personal_tasks`, `main_appointments` **oder** direkt `planner_items`; nur bestimmte davon erreichen Nuriva.

**Ist die Montage klar Planner-geführt?** — **Ja** (Nuriva liest ausschließlich `planner_items`; Planner aggregiert + schreibt zurück).

**Offene Fragen — VOR dem Aufgaben-Anlegen zu klären (Design/Geschäftsregel für Yama, nicht geraten):**
1. **WO legt Yama die Montage-Aufgabe an?** Im Büro-Kanban (`kanban_lead_tasks`), im Template (`phase_activities` → gilt dann für alle Gewerke des Produkts), oder direkt im Planner (`planner_items` manuell)? **Jeder Weg hat andere Reichweite** — nur `phase_activity`/`personal_task`/`appointment`/manuelles planner_item erreichen Nuriva.
2. **Erreicht eine im Büro-Kanban angelegte Aufgabe automatisch den Planner/Nuriva?** Heute: **nein** — nur nach Umwandlung in `personal_task`/`appointment` (KanbanLeadTaskController `:736`/`:809`) oder manuellem planner_item. Soll das automatisch verdrahtet werden?
3. **Wird der Montage-Plan automatisch erzeugt**, sobald ein Gewerk in die Montage-Phase kommt, oder muss er manuell angelegt werden (`storeProjectFromLeadProduct`/`ensureProjectPlan`)? Heute: **on-demand, kein Auto-Trigger gefunden.**
4. **Was ist die eine Status-Wahrheit** einer Montage-Aufgabe — `planner_items`, die zurückgeschriebene `phase_activity`-Instanz (`customer_phase_lists`/`customer_histories`), oder `kanban_lead_tasks`? (Planner schreibt zurück, aber das Büro-Kanban hat eine eigene Status-Spalte.)
5. **Sieht das CRM-Büro den Nuriva-Rückfluss** (erledigt/Foto) an der Stelle, an der die Aufgabe angelegt wurde? Completion-Report + `images` werden gespeichert — ob das Büro-Kanban (`kanban_lead_tasks`) es spiegelt, ist **nicht belegt** und zu prüfen.

*(Diese Fragen hängen direkt an Weiche 1 (Statusquelle) und der noch offenen Planner-Detail-Inventur, Fahrplan 5.1.)*

---

*Reine Analyse — nichts geändert. Belege: Migrationen `lead_stages`(2026_05_28), `lead_stage_sub_stages`(2026_06_05), `add_lead_stage_sub_stage_id_to_lead_product_lists`(2026_06_05), `kanban_lead_tasks`(2026_06_05), `planner_plans`/`planner_items`(2026_01_21); Controller `Customer/Kanban/LeadOverviewController` (kanban:219, kanbanFeed:444, sub_stages:249), `Customer/Kanban/KanbanLeadTaskController` (storeFromTemplate:352, storeManual:284, PersonalTask:736, MainAppointment:809), `Planner/PlannerPlanController` (syncAndLoad:402, pmoUpsertTemplatePlannerItem:545/770, source_types:845/910/990/1069, pmoUpdatePhaseActivityStatus:1777, pmoStoreCustomerCompletionReport:1814, PlannerPlan::firstOrCreate:420/4123/4420), `Planner/PlannerEmployeeApiController` (myWork:19, myDayReport:45); Routen `web.php:5176–5200` (Projekt-Cockpit), `api.php` (`/planner/my-work`, `/items/{item}/complete-report`, `/customer-images/upload`). Querverweis: `struktur-systeme-verhaeltnis-befund.md`, `nuriva-sync-anbindung-befund.md`, `glossar.md`. Als vermutet/ungelesen markiert: Montage-Filter in syncAndLoad, exakte UI-Render-Details der Sub-Kanbans, ob kanban_lead_tasks den Nuriva-Rückfluss spiegelt.*
