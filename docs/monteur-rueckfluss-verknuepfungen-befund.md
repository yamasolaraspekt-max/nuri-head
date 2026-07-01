# Monteur-Rückfluss — vollständige Verknüpfungs-Karte

> **Reine Analyse (nur Lesen), kein Code geändert.** Anlass: `architektur-bewertung-zweitmeinung.md` fand nur **einen** Rückfluss-Pfad (`customer_histories`/Audit). Yama sagt aus der Praxis: es geht **auch** ins Kundenprofil + als Monteur-Bericht, mit „verschiedenen Verknüpfungen". Diese Lücke wird hier geschlossen.
>
> **Ehrliche Korrektur vorab:** Mein früherer Befund war **unvollständig**. Ein Monteur-Abschluss schreibt **nicht** nur in `customer_histories`, sondern in **mindestens 6 Tabellen** in EINER Transaktion. **Yama hat recht** — die Erledigung erscheint im Kundenprofil (über `customer_reports`, view-belegt). Die eigentliche Lücke liegt woanders (Progressbar + Büro-Kanban, s. u.).

---

## 1. Der Abschluss-Vorgang end-to-end — ALLE Schreibstellen

**Endpoint:** `PATCH /api/planner/items/{item}/complete-report` → `PlannerEmployeeApiController::completeItemWithReport` (`:1682`). Alles in **einer** `DB::transaction` (`:1752`):

| # | Tabelle | Was | Beleg |
|---|---|---|---|
| 1 | **`planner_items`** | `status='done'`, `done_at`, `done_by_employee_id` | `completeItemWithReport:1771–1774` (`DB::table('planner_items')->update`) |
| 2 | **`planner_item_comments`** | Bericht/Kommentar als Planner-Mirror | `storePlannerItemReportMirror` (mehrere Inserts in `planner_item_comments`) |
| 3 | **`customer_reports`** | „**Fertig-Bericht**" (`customer_id`, `report_by`=Monteur, `report`='Fertig-Bericht', `report_details` JSON) | Schritt 3 → `pmoStoreCustomerCompletionReport:1814` → `plannerInsertRow('customer_reports', …)` |
| 4 | **`customer_histories`** *(bei `source_type='phase_activity'`)* | `updateOrInsert` per `product_id`/`alternative_id`: `is_done`/`status`/`done_by`/`done_date`/`note` | Schritt 4 → `pmoUpdatePhaseActivityStatus:1777` (`customer_histories`:66) |
|   | *alternativ* `kanban_lead_tasks` *(source=`kanban_task`)* / `personal_tasks` *(source=`personal_task`)* | Status zurück auf die Quell-Aufgabe | `pmoUpdateKanbanTaskSourceStatus:2307`, `pmoUpdatePersonalTaskSourceStatus` |
| 5 | **`planner_item_status_histories`** | Status-Übergang (alt→neu, Mitarbeiter, Notiz) | `storePlannerItemStatusHistory:1826` (`DB::table('planner_item_status_histories')->insert`) |
| 6 | **`images`** | Fotos vom Feld (kunden-/objektbezogen) | separater Endpoint `POST /api/planner/customer-images/upload` → `PlannerMobileCustomerImageController::upload` |

→ **Sechs Schreibstellen**, nicht eine. Zusätzlich zuvor angelegte `planner_item_materials`/Kommentare (Material-Anforderungen des Monteurs).

**Wichtig — was der Abschluss NICHT anfasst:** `phase_activities` (die Template-Aufgabe bleibt unverändert — der „done"-Status liegt als `customer_histories`-Zeile per `product_id/alternative_id`, **nicht** auf der Aktivitäts-Zeile), `customer_phase_lists` (gar nicht), und `kanban_lead_tasks` **nur** wenn die Quelle ein `kanban_task` war.

---

## 2. Geht es ins Kundenprofil? — **JA (view-belegt)**

- **`customer_reports`** (Schritt 3) wird im Profil angezeigt: View `admin/new_leads/layouts/context-feed/customer-reports.blade.php` rendert `@if($report->report_details) … @foreach((array)$report->report_details …)` (`:64–68`); geladen von `CustomerContextFeedController` + `Kanban/KanbanCustomerPanelController`. → Der „Fertig-Bericht" des Monteurs erscheint im **Kontext-Feed / Berichte** des Kundenprofils.
- **`customer_histories`** wird ebenfalls geschrieben und im Profil breit verwendet (46 Referenzen in Profil/Customer-Controllern) — die Historie-Ansicht.

→ **Yamas Praxis-Aussage bestätigt:** nach einem Monteur-Abschluss ist im Kundenprofil sichtbar: der **Fertig-Bericht** (customer_reports, mit report_details) + ein **Historie-Eintrag** (customer_histories) + die **Fotos** (images). *(Ob die Historie-Ansicht customer_histories UND customer_reports gemeinsam zeigt oder getrennt, ist im Feed über den `type-switcher` gesteuert — Detail nicht vollständig gelesen, als Prüfpunkt markiert.)*

---

## 3. Der Tagesbericht des Monteurs — **Live-Aggregation, keine eigene Buchung**

- `PlannerEmployeeApiController::myDayReport` (`:45`) → `employeeDayReportResponse` (`:57`) **liest** `planner_items` (`:69`) und aggregiert erledigt/offen + Material/Kommentare/Bilder zu einem Bericht-Objekt (`:163–196`).
- **Keine Schreibzugriffe auf `daily_reports`** aus den Planner-Controllern (grep: 0 Treffer in `PlannerEmployeeApiController`+`PlannerPlanController`). → myDayReport ist eine **Live-Aggregation**, keine persistente Tagesbericht-Buchung.
- **Aber die Zuordnung „Monteur X, Aufgabe Y, Zeitpunkt Z" ist persistiert** — auf **Item-Ebene**: `planner_items.done_by_employee_id` + `done_at` (Schreibstelle 1) + `planner_item_status_histories` (Schreibstelle 5). Nur eben **nicht** als konsolidierte `daily_reports`-Zeile.
- Das separate **`daily_reports`-Modul** (`DailyReportController`, Tabellen `daily_reports`/`daily_report_times`/`daily_report_time_customers`) wird vom Montage-Sync **nicht** gefüttert — es ist ein eigener Strang.

---

## 4. Die Verknüpfungen — gezeichnet

```
   Monteur hakt in Nuriva ab  ──►  PATCH /api/planner/items/{item}/complete-report
                                          │  (eine Transaktion)
        ┌───────────────┬────────────────┼──────────────────┬─────────────────────┐
        ▼               ▼                ▼                  ▼                     ▼
  planner_items   planner_item_    customer_reports   customer_histories   planner_item_
  (done/done_at/  comments         ("Fertig-Bericht", (is_done/status/     status_histories
   done_by)       (Mirror-Bericht)  report_details)    done_by/done_date)   (alt→neu)
        │                                  │                  │
        │  (myDayReport liest live)        └─ Kundenprofil ◄──┘  (Kontext-Feed/Historie)
        ▼                                        ▲
  Tagesbericht (Live-Aggregation)                │
                                    Fotos: /customer-images/upload ──► images (kunden-/objektbezogen)

  NICHT berührt (bei source=phase_activity):  phase_activities · kanban_lead_tasks · customer_phase_lists
```

---

## 5. Was fließt HEUTE zurück — und was nicht

| Rückfluss-Ziel | Heute? | Über welche Tabelle |
|---|---|---|
| **Kundenprofil-Historie** | ✅ ja | `customer_reports` (Fertig-Bericht, view-belegt) + `customer_histories` |
| **Fotos im Profil** | ✅ ja | `images` (kunden-/objektbezogen) |
| **Planner-Item erledigt** | ✅ ja | `planner_items` (done/done_at/done_by) |
| **Status-Audit** | ✅ ja | `planner_item_status_histories` + `customer_histories` |
| **Monteur-Zuordnung + Zeit** | ✅ persistiert (Item-Ebene), ⚠️ kein gebuchter Tagesbericht | `planner_items.done_by_employee_id/done_at` |
| **Büro-Kanban (`kanban_lead_tasks`) sieht Erledigung** | ❌ **nein** (bei `source=phase_activity`) | — (nur bei `source=kanban_task` → kanban_lead_tasks) |
| **Fortschrittsbalken bewegt sich** | ❌/⚠️ **entkoppelt** | Progressbar zählt `kanban_lead_tasks.done` — die der Abschluss nicht anfasst |
| **Template `phase_activities` erledigt** | ❌ nein | Status liegt in `customer_histories` per product/alternative, nicht auf der Aktivität |

**Die tatsächliche Lücke** ist NICHT „geht nichts ins Profil" (das tut es) — sondern:
1. **Büro-Kanban blind:** die im Büro-Board (`kanban_lead_tasks`) sichtbare Aufgabe wird bei einer `phase_activity`-Quelle **nicht** auf „done" gesetzt.
2. **Progressbar entkoppelt:** er zählt `kanban_lead_tasks`, die der Abschluss (bei phase_activity) nicht bewegt → der Balken rührt sich nicht (Detail in `monteur-rueckfluss-vier-ziele-befund.md`).
3. **Kein gebuchter Tagesbericht** (nur Live-Aggregation).

---

## 6. Fazit — die Karte

**Monteur-Abschluss → 6 Rückfluss-Pfade existieren heute** (planner_items, planner_item_comments, customer_reports, customer_histories/[kanban/personal], planner_item_status_histories, images). **Das Kundenprofil ist verdrahtet** (customer_reports + customer_histories + images). **Nicht** verdrahtet sind: Büro-Kanban-Status (bei phase_activity-Quelle), der daraus berechnete Progressbar, und eine persistente Tagesbericht-Buchung.

→ Ein späterer Fix darf **nichts** doppelt bauen: Profil-Rückfluss existiert. Er muss **nur ergänzen**: (a) Status auch auf die Büro-Kanban-Karte/Aktivitäts-Instanz spiegeln, damit (b) der Progressbar sich bewegt; optional (c) eine gebuchte Tagesbericht-Zeile. Details je Ziel: **`monteur-rueckfluss-vier-ziele-befund.md`**.

---

*Reine Analyse — nichts geändert. Belege: `PlannerEmployeeApiController::completeItemWithReport:1682` (planner_items-Update:1771, storePlannerItemReportMirror→`planner_item_comments`, storePlannerItemStatusHistory→`planner_item_status_histories`:1826, myDayReport:45→employeeDayReportResponse liest planner_items:69), `PlannerPlanController::pmoStoreCustomerCompletionReport:1814`→`customer_reports`, `pmoUpdatePhaseActivityStatus:1777`→`customer_histories`:66, `pmoUpdateKanbanTaskSourceStatus:2307`→`kanban_lead_tasks`; View `context-feed/customer-reports.blade.php:64–68`; `CustomerContextFeedController`/`KanbanCustomerPanelController`; 0 `daily_reports`-Writes in Planner-Controllern. Querverweis: `kanban-ebenen-montage-planner-nuriva-befund.md`, `architektur-bewertung-zweitmeinung.md`, `glossar.md`. Vermutet/ungelesen markiert: genaue type-switcher-Anzeige customer_histories vs customer_reports im Feed.*
