# Monteur-Rückfluss — die vier erwarteten Ziele geprüft

> **Reine Analyse (nur Lesen), kein Code geändert.** Yama benennt aus der Praxis **vier** erwartete Rückfluss-Ziele beim Monteur-Abschluss. Für jedes: heute verdrahtet (ja/teilweise/nein) + Beleg. Vollständige Schreibstellen-Karte in **`monteur-rueckfluss-verknuepfungen-befund.md`** (dieses Dokument fokussiert die 4 Ziele + die Lücke).
>
> **Kurzfazit:** **2 von 4 ganz verdrahtet** (Kundenprofil-Historie ✅, Aufgabe-erledigt ✅ — aber nur in `planner_items`), **1 nur halb** (Tagesbericht: persistiert auf Item-Ebene, kein gebuchter Tagesbericht), **1 gebrochen** (Progressbar: entkoppelt — der Abschluss bewegt ihn bei der normalen Montage-Quelle NICHT). Der Progressbar ist der kritische Ausfall.

---

## Ziel 1 — Tagesbericht des Monteurs (wer/wann/welche Aufgabe) — **TEILWEISE**

- **Persistiert auf Item-Ebene:** `planner_items.done_by_employee_id` + `done_at` (`completeItemWithReport:1771–1774`) + `planner_item_status_histories` (alt→neu, Mitarbeiter, Zeit; `storePlannerItemStatusHistory:1826`). → „Monteur X hat Aufgabe Y am Zeitpunkt Z erledigt" ist **dauerhaft** nachvollziehbar.
- **Aber kein gebuchter Tagesbericht:** `myDayReport:45` (`employeeDayReportResponse`) ist eine **Live-Aggregation** aus `planner_items` — **0 Schreibzugriffe** auf `daily_reports` aus den Planner-Controllern. Das separate `daily_reports`-Modul (`DailyReportController`) wird vom Montage-Sync **nicht** gefüttert.
- **Sichtbar:** über den Live-Tagesbericht (myDayReport) + die persistierten Item-Felder. Eine gespeicherte Tagesbericht-Zeile pro Monteur/Tag aus dem Sync gibt es nicht.
- **Urteil:** ✅ Zuordnung+Zeit persistiert · ❌ keine gebuchte Tagesbericht-Entität.

## Ziel 2 — Kundenprofil / Historie — **VERDRAHTET (ja)**

- **`customer_reports`** („Fertig-Bericht", `report_by`=Monteur, `report_details` JSON) via `pmoStoreCustomerCompletionReport:1814`. **Angezeigt** im Profil: View `context-feed/customer-reports.blade.php:64–68` (`$report->report_details`), geladen von `CustomerContextFeedController`/`KanbanCustomerPanelController`.
- **`customer_histories`** via `pmoUpdatePhaseActivityStatus:1777` (`:66`), breit im Profil verwendet (46 Refs).
- **Fotos** via `/customer-images/upload` → `images` (kunden-/objektbezogen).
- **Urteil:** ✅ Fertig-Bericht + Historie + Fotos erscheinen im Kundenprofil. **Yama hat recht.** *(Genaue Feed-Darstellung customer_histories vs customer_reports über den `type-switcher` nicht Zeile-für-Zeile gelesen — Prüfpunkt.)*

## Ziel 3 — Projektfortschritt / Progressbar — **GEBROCHEN / ENTKOPPELT (der kritische Punkt)**

- **Wo/womit:** Der Profil-Progressbar (`customerKanbanProgressBar`/`-Percent`/`-Text` im `customerKanbanTaskDrawer`) wird von `KanbanLeadTaskController::summaries`/`context` gerechnet: `done_count = kanban_lead_tasks mit status='done'` / Gesamt (`:159–161`, `$doneTasks = …status==='done'` `:89–90`). → **Quelle des Balkens = `kanban_lead_tasks`.**
- **Bewegt der Abschluss diese Quelle? NEIN** — bei `source_type='phase_activity'` (der normalen Montage-Aufgabe) schreibt `completeItemWithReport` in `planner_items`+`customer_reports`+`customer_histories`, **nicht** in `kanban_lead_tasks` (Rückschreiben nach `kanban_lead_tasks` passiert **nur** bei `source_type='kanban_task'`, `pmoUpdateKanbanTaskSourceStatus:2307`, Dispatch `:1775`).
- **Folge:** Der Progressbar zählt eine Tabelle, die der Montage-Abschluss (phase_activity-Quelle) nicht anfasst → **er rührt sich nicht.** *(Ausnahme: Aufgaben, die als `kanban_task` in den Plan kamen — dann bewegt er sich.)*
- **Nebenbefund:** Das **Planner-Cockpit** hat einen eigenen Fortschritt aus erledigten `planner_items` (bewegt sich also dort) — aber das ist die Feld-Sicht, nicht der Büro-/Profil-Balken.
- **Urteil:** ❌ Der erwartete „erledigt bewegt den Fortschritt" gilt heute **nicht** für die normale Montage-Aufgabe. **Das ist die kritischste Lücke.**

## Ziel 4 — Die Aufgabe selbst gilt als erledigt — **NUR IN EINER TABELLE**

- **`planner_items`:** ✅ `status='done'`, `is_done`, `done_at`, `done_by_employee_id` (`:1771`).
- **`phase_activities`:** ❌ die Template-Aktivität bleibt unverändert; „done" liegt als `customer_histories`-Zeile per `product_id/alternative_id`, **nicht** auf der Aktivitäts-Zeile.
- **`kanban_lead_tasks`:** ❌ (nur bei `source=kanban_task`).
- **`customer_phase_lists`:** ❌ nicht berührt.
- **Urteil:** ⚠️ Die Aufgabe ist **in `planner_items` erledigt** (+ Audit in `customer_histories`), aber **nicht** in den anderen Tabellen, die „dieselbe" Aufgabe halten. Das ist das „mehrere Instanzen, nur eine aktualisiert"-Muster.

---

## Gesamtbild

| Ziel | Verdrahtet? | Über welche Tabelle | Lücke |
|---|---|---|---|
| **1 Tagesbericht** | ⚠️ teilweise | `planner_items` (done_by/done_at) + `planner_item_status_histories`; Live-`myDayReport` | keine gebuchte `daily_reports`-Zeile aus dem Sync |
| **2 Kundenprofil-Historie** | ✅ ja | `customer_reports` (view-belegt) + `customer_histories` + `images` | — (funktioniert) |
| **3 Progressbar** | ❌ gebrochen | Balken zählt `kanban_lead_tasks.done`; Abschluss schreibt dorthin nicht (bei phase_activity) | **Balken bewegt sich nicht** |
| **4 Aufgabe erledigt** | ⚠️ nur `planner_items` | `planner_items` (+ `customer_histories`-Audit) | `phase_activities`/`kanban_lead_tasks`/`customer_phase_lists` bleiben „offen" |

---

## Wo klafft es? (Yamas Wunsch: Tagesbericht + Historie + Progressbar + erledigt, alle vom Sync gespeist)

- **Historie:** erfüllt. ✅
- **Erledigt-Status:** halb — nur die Feld-Instanz (`planner_items`) weiß es; Büro-Sichten nicht.
- **Progressbar:** **nicht erfüllt** — entkoppelt, bewegt sich bei Montage-Abschluss nicht.
- **Tagesbericht:** halb — live sichtbar, aber nicht als dauerhafte Buchung.

## Was FEHLT für Yamas Wunsch (zu ergänzen — NICHT umgesetzt, ohne Bestehendes zu brechen)

1. **Erledigt-Status auf die Büro-Ebene spiegeln:** beim `phase_activity`-Abschluss zusätzlich die zugehörige `kanban_lead_tasks`-Karte (falls vorhanden) auf `done` setzen — analog zum bereits existierenden `pmoUpdateKanbanTaskSourceStatus` (`:2307`), nur auch für die phase_activity-Quelle. **→ bewegt automatisch den Progressbar (Ziel 3), weil der aus `kanban_lead_tasks` rechnet.** *(Voraussetzung: eindeutige Zuordnung planner_item.phase_activity ↔ kanban_lead_tasks-Karte — zu klären, ob die Verknüpfung existiert.)*
2. **Progressbar-Quelle vereinheitlichen (Alternative zu 1):** den Balken statt aus `kanban_lead_tasks` aus den erledigten `planner_items`/`phase_activities`-Status rechnen — dann bewegt ihn jeder Abschluss. *(Design-Entscheidung: welche Quelle ist die Fortschritts-Wahrheit — hängt an Weiche 1.)*
3. **Optional gebuchter Tagesbericht:** beim Abschluss (oder Tagesende) eine `daily_reports`-Zeile aus den erledigten `planner_items` je Monteur/Tag buchen — statt nur live zu aggregieren.
4. **Erledigt-Status auf `phase_activities`/`customer_phase_lists`** nur dann spiegeln, wenn eine dieser Tabellen die Fortschritts-Wahrheit werden soll (siehe `architektur-bewertung-zweitmeinung.md` — Reihenfolge nach Weiche 1).

> **Wichtig:** Punkt 1 ist der kleinste, sicherste Fix mit dem größten Alltags-Effekt (schließt Progressbar **und** Büro-Sicht in einem), baut auf einem **schon existierenden** Mechanismus auf (`pmoUpdateKanbanTaskSourceStatus`) und bricht nichts. Aber erst nach Klärung der Zuordnung phase_activity ↔ kanban-Karte und nach Weiche 1 bauen.

---

*Reine Analyse — nichts geändert. Belege: `PlannerEmployeeApiController` (completeItemWithReport:1682, planner_items-Update:1771, myDayReport:45); `PlannerPlanController` (pmoStoreCustomerCompletionReport:1814→customer_reports, pmoUpdatePhaseActivityStatus:1777→customer_histories:66, pmoUpdateKanbanTaskSourceStatus:2307→kanban_lead_tasks, Dispatch:1775); `KanbanLeadTaskController::summaries` (done_count aus kanban_lead_tasks:89–90/159–161); View `context-feed/customer-reports.blade.php:64–68`; 0 daily_reports-Writes in Planner-Controllern. Querverweis: `monteur-rueckfluss-verknuepfungen-befund.md`, `architektur-bewertung-zweitmeinung.md`, `kanban-ebenen-montage-planner-nuriva-befund.md`, `glossar.md`. Vermutet/ungelesen markiert: Existenz einer eindeutigen Zuordnung planner_item.phase_activity ↔ kanban_lead_tasks-Karte; Feed-type-switcher-Darstellung.*
