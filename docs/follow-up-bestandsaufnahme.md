# Follow-up / Wiedervorlage — Bestandsaufnahme (reine Analyse)

> **Nur Lesen/Analyse, keine Änderung.** Beantwortet Abschnitt 4 des Konzepts (`follow-up-wiedervorlage-konzept.md`): **Was existiert schon**, bevor irgendetwas gebaut wird — um „danebenbauen" zu vermeiden. Stand 2026-07-02, Belege wörtlich.

> **⚠️ ERGEBNIS: Yamas „sowas habe ich überall" ist massiv bestätigt.** Es gibt **mindestens fünf** überlappende Systeme mit Follow-up-/Erinnerungs-/Fälligkeits-Semantik. Zwei sind fast exakt die gewünschte Follow-up-Entität. **Klarer Rat: NICHT neu bauen — erweitern.** Die eigentliche Arbeit ist eine **Konsolidierungs-/Design-Entscheidung**, nicht ein neues System. Details + empfohlener Kandidat unten.

---

## Die überlappenden Systeme (Live-geprüft)

| System | Tabelle(n) | Live-verdrahtet? | Zeilen (live) | Fälligkeit | Verantwortl. | Status | Historie | Bezug |
|---|---|---|---|---|---|---|---|---|
| **Generisches Reminder** | `reminders` + `reminder_events` | **NEIN** (nur Model-Relationen, kein Controller erzeugt/liest) | **0** | `next_remind_at` | `employee_id` | active/snoozed/done/canceled | `reminder_events` (created/snoozed/reminded/…+note) | **polymorph** `entity_type`(inquiry/task/appointment/ticket/lead)+`entity_id` |
| **Lead-Reminder** | `lead_reminders` | **JA** (`LeadReminderController`: store/due/context/done/cardSummaries) | **0** | `reminder_date`+`reminder_time` | `responsible_employee_id` | open/done/cancelled | `notified_at`/`completed_at` + `lead_activity_logs` | **Kunde/Objekt/Gewerk** (customer/alternative/product/lead_product_list) |
| **General Task** | `general_tasks` (+ assignees, reports, deps) | **JA** (`GeneralTaskController` /general-tasks) | **45** ✅ real genutzt | `due_at` | `claimed_by` + assignees-Pivot | open/in_progress/review/done/archived | `general_task_reports` (comment/report) | frei (kein CRM-Bezug) |
| **Personal Task** | `personal_tasks` (+ histories, Reminder-Engine) | **JA** (`PersonalTaskController`/`PersonalTaskBoardController`) | *(n. gezählt)* | Scheduler-Felder | employees-Pivot | board_column/status | `personal_task_histories` | Mitarbeiter-eigen |
| **Overdue-Report** | `overdue_reports` + `overdue_report_reads` | *(Controller n. verifiziert)* | *(n. gezählt)* | — (Berichts-Log) | `employee_id` | — | timestamps + reads | **polymorph** `type`+`target_id` |

**Zusatz-Systeme (peripher, aber vorhanden):** `main_appointment_reminder_logs` (Termin-Erinnerungen), `notifications` (2024), `lead_activity_logs` (schon von LeadReminder genutzt). Und: **`customer_profile.blade.php` mappt bereits `nachfassen → 'follow_up'`** (:7178-7179, :18620) mit Fälligkeits-Validierung (:10805 „Bitte wählen Sie ein Fälligkeitsdatum.") — es existiert also schon ein **`follow_up`-Zustand** in der Kundenprofil-UI *(Flow im Detail NICHT verifiziert)*.

---

## Antworten auf die 4 Prüf-Fragen (Abschnitt 4 des Konzepts)

**1. Gibt es schon eine Wiedervorlage-/Reminder-/Follow-up-Tabelle?**
**Ja, mehrfach.** Ein **generisches polymorphes** `reminders`-System (owner + entity_type/id + next_remind_at + status + Event-Historie) — das ist strukturell **exakt** die Follow-up-Entität aus dem Konzept. **ABER:** 0 Zeilen und **kein Live-Erzeuger** (nur `Reminder`↔`ReminderEvent`-Model-Relationen; kein Controller ruft `Reminder::create`) → **dormant / built-but-unused**. Das ist die klassische Falle: sieht perfekt aus, ist aber tot. Daneben das **live-verdrahtete `lead_reminders`** (CRM-Kontext).

**2. personal_tasks / appointments / kanban_lead_tasks — haben sie Fälligkeit + Verantwortlichen + Status?**
- **`kanban_lead_tasks`** (Büro-Karte): `status` (open/scheduled/in_progress/done/cancelled), `planned_start_at`/`planned_end_at`, `performer_employee_id`, `done_at`/`done_by_employee_id` → **Verantwortlicher + Status + Termin JA**, aber **keine „Fälligkeit als Wiedervorlage"** und keine Erinnerungs-Felder.
- **`personal_tasks`**: hat sogar eine **Reminder-Engine** — `next_reminder_at`, `last_reminded_at`, `reminder_count`, plus **Recurrence** (`next_repeat_at`, `last_repeated_at`, `repeat_parent_id`) und `board_column`, Report/Archive, `personal_task_histories`. → Am **nächsten an einer echten Erinnerungs-Mechanik**, aber Mitarbeiter-eigen (kein CRM-Bezug).
- **`main_appointments`**: eigene Reminder-Logs (`main_appointment_reminder_logs` mit `reminder_count`).

**3. Gibt es schon ein Dashboard mit „meine offenen X"?**
Es gibt ein **konfigurierbares Widget-Dashboard** (`DashboardWidgetController`: /dashboard/widgets/load, /registry, /save; + `DashboardLiveInboxController`, `EmployeeDashboardController`). `LeadReminderController::due()` liefert bereits **die „meine fälligen/überfälligen Reminder"-Query** (`status=open`, `responsible_employee_id = me` oder null, `reminder_date < heute` oder heute+Zeit, sortiert nach Datum) — **genau die Dashboard-Logik aus dem Konzept, bereits gebaut.** *(NICHT VERIFIZIERT: ob `due()` heute in einem Dashboard-Widget konsumiert wird, oder nur im Kanban; wo die Widget-Registry die verfügbaren Widgets definiert.)*

**4. customer_histories / completion-reports — entsteht dort schon etwas Follow-up-artiges?**
`customer_profile.blade` kennt einen **`follow_up`-Zustand** (Nachfassen). Der Planner hat `pmoStoreCompletionReport` (Monteur-Abschlussbericht). *(NICHT VERIFIZIERT: ob completion-reports oder customer_histories heute schon ein Fälligkeit/Verantwortlichen-Follow-up erzeugen — nur die Existenz des `follow_up`-Status belegt.)*

---

## Leitentscheidung (Empfehlung — Yama entscheidet)

**NICHT neu bauen.** Bei 5 überlappenden Systemen wäre ein sechstes genau die Kernkrankheit. Die Arbeit ist **Konsolidierung + Erweiterung eines bestehenden**.

**Empfohlener Kandidat: `lead_reminders` (via `LeadReminderController`) erweitern** — Begründung:
- **Am nächsten an Yamas Konzept:** hat bereits Verantwortlicher (`responsible_employee_id`), Fälligkeit (`reminder_date`+`time`), Priorität, Status (open/done/cancelled), **Kunde/Objekt/Gewerk-Bezug** (den das generische `reminders` NICHT hat) und **`notified_at`/`completed_at`**.
- **Dashboard-Query existiert schon** (`due()`).
- **Live-verdrahtet**, aber **0 Zeilen** → noch keine Altdaten-Last, sauberer Erweiterungs-Zeitpunkt.
- **Fehlt für Yamas Konzept:** (a) Feld „Art" (Nachfass vs. Wiederaufnahme), (b) expliziter Bezug zu **der berichteten Aufgabe** (heute nur Lead/Objekt/Produkt, nicht die konkrete `kanban_lead_task`/Aktivität), (c) Anbindung an den **Abschluss-Dialog** (Fall 2/3 erzeugt automatisch einen lead_reminder), (d) die **Dashboard-Widget-Oberfläche** (Query da, Widget-Anzeige n. verifiziert), (e) „verschoben"-Status (heute nur open/done/cancelled — „snooze" fehlt, das hätte das dormante `reminders` via `reminder_events` gehabt).

**Alternative zu bedenken:** Das **generische `reminders`** ist strukturell die sauberste Follow-up-Entität (polymorph, snooze, Event-Historie) — aber **dormant**. Es zu **reaktivieren** statt `lead_reminders` zu erweitern wäre eine Option, wenn Follow-ups an **beliebige** Entitäten (nicht nur Leads) hängen sollen. Trade-off: `lead_reminders` = fertig verdrahtet aber lead-fixiert; `reminders` = flexibler aber tot (müsste ganz neu angebunden werden — praktisch fast wie neu bauen).

**Design-Entscheidung, die Yama treffen muss:**
> **(A)** `lead_reminders` erweitern (schneller, live, aber CRM-/Lead-gebunden) — **meine Empfehlung**, ODER **(B)** das generische `reminders` reaktivieren (flexibler/polymorph, aber dormant → hoher Anbindungsaufwand)? Und: **Sollen die anderen Systeme** (`general_tasks` 45 Zeilen, `personal_tasks`-Reminder, der `follow_up`-Status im Kundenprofil) **mit-konsolidiert** oder bewusst getrennt bleiben?

**Verbindung zu Weiche 6:** Der Monteur-Fall „erledigt, aber Nachfass" (Fall 2) sollte denselben Follow-up-Mechanismus erzeugen wie der Büro-Abschluss — zusammen denken, nicht getrennt (sonst zwei Follow-up-Wahrheiten).

---

## Gelesen / NICHT gelesen (ehrlich)
**Geprüft (wörtlich/live):** Migrations-Schemata `reminders`, `reminder_events`, `lead_reminders`, `general_tasks` (+ Sub-Tabellen), `personal_tasks`-Scheduler-Migration, `overdue_reports`; Live-Zeilenzahlen (reminders/reminder_events/lead_reminders=0, general_tasks=45); Live-Wiring (Routen + Controller-Existenz) für Lead/General/Appointment/Note-Reminder; `LeadReminderController` (store/due/context/done/cardSummaries — Felder + due-Query); generisches `Reminder`-Model nur in Relationen; `follow_up`-Mapping in `customer_profile.blade`.
**NUR gegrept / NICHT VERIFIZIERT:** ob `LeadReminderController::due()` in einem **Dashboard-Widget** konsumiert wird (vs. nur Kanban); wo die **Widget-Registry** die verfügbaren Widgets definiert (Grep in DashboardWidgetController leer → Registry liegt woanders); `general_tasks`-**UI/Board** (nur Existenz + 45 Zeilen); `personal_tasks`-**Basis-Tabelle** (due/assignee-Details, nur die Scheduler-Nachtrags-Migration gelesen); ob **completion-reports/customer_histories** real Follow-ups erzeugen; ob `overdue_reports` einen Controller/Dashboard hat; `notifications`-Nutzung.

## Selbstkritik / Risiken
- **„lead_reminders erweitern" ist eine Empfehlung, kein Beweis der Überlegenheit** — sie beruht auf „am meisten Felder passen + live + Dashboard-Query da". Wenn Follow-ups an Nicht-Lead-Aufgaben (z. B. reine Bürovorgänge) hängen sollen, kippt die Empfehlung Richtung generisches `reminders`.
- **Ich habe die 5 Systeme inventarisiert, aber nicht ihre UIs durchgespielt** — „welches sieht Yama, wenn er sagt ‚überall'" ist teils Vermutung. Der `follow_up`-Status im Kundenprofil + general_tasks (45 Zeilen) sind die wahrscheinlichsten „das habe ich schon"-Kandidaten, aber deren genaues Verhalten ist NICHT verifiziert.
- **0 Zeilen bei lead_reminders/reminders** heißt: die „habe ich überall"-Wahrnehmung stammt eher von general_tasks/personal_tasks/dem customer_profile-follow_up als von den Reminder-Tabellen — die Konsolidierung muss also **auch die genutzten** Systeme (general_tasks 45) einbeziehen, nicht nur die leeren. Das macht die Design-Entscheidung wichtiger (und größer) als „nur ein Feld ergänzen".

---

*Reine Analyse — nichts geändert. Querverweise: `follow-up-wiedervorlage-konzept.md`, `architektur-entscheidungen.md` (Weiche 1 Zustand/Weiche 6), `fahrplan-ticket-crm.md` (Ebene 1.1 + 2.2 Dashboard).*
