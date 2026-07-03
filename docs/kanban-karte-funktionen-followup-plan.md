# Kanban-Karte: Funktionen-Kartierung + Follow-up-Mechanismus (Design)

> **Reine Analyse + Design, kein Bau.** Einziges Schreibprodukt: dieses Doc. Baut auf `follow-up-bestandsaufnahme.md` (d1cadd8) + `follow-up-wiedervorlage-konzept.md` auf — wiederholt deren System-Inventur nicht, sondern kartiert die **Karten-Funktionen** und entwirft den fehlenden **Follow-up-Mechanismus**. Verzahnt mit Kanban Stufe A (parallel gebaut: `lead_product_lists.lead_stage_id`) und Weiche 6 (Rückfluss). Stand 2026-07-03. Code-Belege via 3 Explore-Agenten (Datei:Zeile), DB-Belege live.

---
## TEIL 1 — KARTEN-FUNKTIONEN: INVENTAR + BEWERTUNG

### 1. Inventar — was die Kunden-Karte heute wirklich bietet (10 Funktionen)
Karte = ein Gewerk = `lead_product_lists`. Auslöser in `kanban.blade.php` (Karten-Menü :2141-2217) / `public/js/kanban.js`.

| # | Funktion | Auslöser | Endpunkt | Speicherort |
|---|---|---|---|---|
| 1 | **Aufgabe (persönlich)** | Sidebar `data-menu="aufgabe"` | `KanbanPersonalTaskPanelController@tasks` / `PersonalTaskController@store` | `personal_tasks` |
| 2 | **Aufgabenmanagement** | `data-open-kanban-task-management` | `KanbanLeadTaskController@context` / `@storeManual` | `kanban_lead_tasks` (+ pivot) |
| 3 | **Notiz** | `data-open-notes` | `CustomerNoteController@context` / `@store` | `customer_notes` |
| 4 | **Termin** | `data-menu="termin"` | `LeadOverviewController@appointmentsIndex` / `@appointmentsStore` | `main_appointments` (+ pivot) |
| 5 | **Reminder / Wiedervorlage** | **nur bei Stage-Wechsel** `createReminderFromStageChange()` (kanban.js:4527/5491) | `LeadReminderController@store` | `lead_reminders` |
| 6 | **Kundenbericht** | Notes-Drawer → Tab | `CustomerReportController@kanbanIndex` / `@kanbanStore` | `customer_reports` |
| 7 | **Verlauf** | `data-menu="verlauf"` | `LeadReminderController@context` (activities) | `lead_activity_logs` (RO) |
| 8 | **Profil-Link** | icon-eye | `/new_lead_profile/{customer}` | `new_leads` (RO) |
| 9 | **Timer (Start/Pause/Stopp)** | `data-run=...` | Stage-Change + Grund | `lead_activity_logs` |
| 10 | **Junk / Archiv** | `data-act="delete/archive"` | Stage-Change | `lead_product_lists.status` |

→ **Sieben Schreibziele** (personal_tasks, kanban_lead_tasks, customer_notes, main_appointments, lead_reminders, customer_reports, lead_activity_logs) — genau Yamas „sowas habe ich überall". Zwei Aufgaben-Tabellen (1+2), ein Reminder (5, nur an Stage-Wechsel), zwei Berichts-/Notiz-Ziele (3+6) ohne Nachverfolgung.

### 2. Bewertung je Funktion gegen Yamas 4 Punkte
(a) nächster Schritt · (b) Erinnerung+Fälligkeit · (c) Dashboard-Sichtbarkeit (/home, Verantwortlicher) · (d) Abschluss-Ergebnis (3 Ausgänge) · [+ Verantwortlicher]

| Funktion | (a) n. Schritt | (b) Erinnerung/Fällig | (c) /home-Sichtbar | (d) Abschluss-Ergebnis | Verantwortl. |
|---|---|---|---|---|---|
| Aufgabe persönlich (personal_tasks) | fehlt | **ja** (Scheduler-Engine) | fehlt (Board, nicht /home) | fehlt | ja (assigned_by) |
| Aufgabenmgmt (kanban_lead_tasks) | teilw. (internal_note) | fehlt | teilw. (nur `reported` via „Zu prüfen", fa41c61) | **teilw.** (done/reported B3) | ja (performer) |
| Notiz (customer_notes) | fehlt | fehlt (nur optional due_date) | fehlt | fehlt | **fehlt** |
| Termin (main_appointments) | fehlt | ja (reminder_date) | ja (Dashboard listet Termine) | fehlt | ja |
| **Reminder (lead_reminders)** | teilw. (description) | **ja** (reminder_date+time) | **fehlt** (nicht auf /home!) | fehlt | ja (responsible) |
| Kundenbericht (customer_reports) | fehlt | fehlt | fehlt | fehlt | fehlt |
| Monteur-Report `next_step`/`due_date` | **ja** (Feld) | teilw. (due_date, keine aktive Erinnerung) | teilw. (Calendar-Widget zeigt es passiv) | fehlt | teilw. |

→ **Keine einzige Funktion erfüllt a–d vollständig.** `lead_reminders` ist am nächsten (a,b, Verantwortl.), scheitert aber an **(c) Dashboard** und **(d) Ausgang**. `kanban_lead_tasks` hat via B3 einen Ausgang (done/reported), aber keinen Follow-up/Erinnerung.

### 3. Gut / Fehlt — Urteil
**Heute schon brauchbar:**
- **`lead_reminders` ist bereits an die Karte verdrahtet** — `cardSummaries()` liefert Reminder-Zähler-Badges je Karte (kanban.js:12553), `context()` den Detail-Drawer (:12575). Verantwortlicher + Fälligkeit + Priorität + Status sind da; `due()` ist **exakt** die „meine fälligen"-Query (LeadReminderController:69-107).
- **`kanban_lead_tasks` hat den B3-Ausgang** done/reported + Prüfer + das „Zu prüfen"-Widget (fa41c61) auf /home.
- **`appointment_reports.next_step`/`due_date` persistieren als echte Spalten** und werden gelesen (s. §4).

**Strukturell fehlt:**
- **Kein Weg von Notiz/Aufgabe/Bericht → Follow-up** (nächster Schritt + Fällig + Verantwortlicher + Erinnerung). Ein Reminder entsteht **nur bei Stage-Wechsel** (§1.5), nicht beim Schreiben einer Notiz/eines Berichts.
- **`lead_reminders` erscheint auf KEINEM Dashboard** — die fertige `due()`-Query wird nur von kanban.js gepollt, nie auf /home gezeigt (EmployeeDashboardController: 0 Referenzen auf LeadReminder). Das ist Yamas Punkt (c) in Reinform: die Erinnerung existiert, landet aber nicht „bei mir".
- **Kein 3-Ausgang-Abschluss** (erledigt / weiterer Bericht / weitere Aufgaben) an irgendeinem Karten-Flow außer dem Monteur-B3.

**Wo Informationen versickern:**
- **Notiz (`customer_notes`)**: einmal geschrieben, kein Verantwortlicher, keine Fälligkeit als Wiedervorlage, kein Dashboard → **versickert**.
- **Kundenbericht (`customer_reports`)** vom Büro: kein due/Verantwortlicher/Ausgang → **versickert**.

### 4. Verbleib von `next_step` + `due_date` (Monteur-Report) — wörtlich
`PlannerEmployeeApiController::completeItemWithReport` validiert beide (:1693-1702) und schreibt sie an **drei** Ziele:
| Ziel | Form | Wird gelesen? |
|---|---|---|
| `planner_item_comments.meta` (JSON) :2065-2074 | JSON in meta | **NEIN** (kein Leser gefunden) |
| `customer_reports.report_details` (JSON) :2161-2186 | JSON | **NEIN** (kein Leser) |
| **`appointment_reports.next_step` / `due_date`** (echte Spalten) :2115-2116 | Spalten | **JA** — `OverdueCenterController:2758-2787` (Recent-Reports-Center) + `DashboardCalendarWidgetController:219-220` (Kalender-Widget) |

→ **Präzisierung (Selbstkorrektur):** next_step/due_date **versickern NICHT vollständig** — über `appointment_reports` sind sie persistiert und werden in zwei Ansichten **passiv angezeigt**. Aber: sie werden **nie zu einem handlungsfähigen Follow-up** (kein Verantwortlicher-Dashboard-Eintrag „das ist als Nächstes fällig", keine Erinnerung, kein 3-Ausgang). Der Gedanke existiert im Monteur-Pfad (Weiche 6), endet aber als **Report-Anzeige**, nicht als **Nachverfolgung**. Genau die Lücke, die dieses Konzept schließt.

---
## TEIL 2 — TRÄGER-PRÜFUNG: lead_reminders vs. personal_tasks

### 5. `lead_reminders` wörtlich (Andock-Kandidat)
Schema (Migration 2026_06_03_085649): `id, lead_product_list_id, customer_id, alternative_id, product_id, department_id, responsible_employee_id, title, description, reminder_date, reminder_time, priority, status(open/done/cancelled), created_by, notified_at, completed_at`. **Live: 0 Zeilen.** Einziger Schreiber: `LeadReminderController`.
- **store()** (:15-67): schreibt CRM-Bezug + responsible_employee_id + reminder_date/time + priority + status='open'. Validiert responsible/date/time/priority.
- **due()** (:69-107): `status=open` ∧ (responsible=me ∨ null) ∧ (reminder_date<heute ∨ heute∧time≤jetzt) ∧ notified_at null, sortiert Datum/Zeit, limit 10. **= die Dashboard-Query, fertig.**
- **cardSummaries()/context()** (:138-225): Karten-Badges + Drawer (kanban.js).
- **done()** (:109-136): status→done, completed_at.
- **Auf /home: NEIN** (kein Dashboard-Controller liest lead_reminders).

**Fehlende Spalten fürs Follow-up-Konzept** (additiv):
1. `next_step` (TEXT) — „was als Nächstes".
2. `outcome` (ENUM) — 3 Ausgänge (done / needs_report / needs_tasks).
3. `art` (ENUM) — Nachfass vs. Wiederaufnahme.
4. `source_type` + `source_id` — Bezug zur **konkreten** Notiz/Aufgabe/Bericht/Karte (heute nur Lead/Objekt/Produkt).
5. `snoozed_until` + Status-Wert `snoozed` — „verschoben" (heute nur open/done/cancelled).

### 6. Gegenprobe `personal_tasks` (die Reminder-Engine aus dem Vorbefund)
Schema: CRM-Refs (customer/alternative/product/lead_product_list/lead_stage), `assigned_by`, `due_date`+time, `reminder_date`/`next_reminder_at`/`reminder_count`/`is_notified`, `is_report`, `type`, Recurrence. **Live: 0 Zeilen.** Schreiber: `PersonalTaskController@store` (:684-840).
- **Aktive Erinnerungs-Engine JA:** `ProcessPersonalTaskScheduler` (Command `personal-tasks:process-scheduler`) liest `next_reminder_at` und **versendet** `PersonalTaskReminderNotification` (Mail/Push) + Broadcast. Plus Wiederholung.
- **Auf /home: NEIN** (board-orientiert: Offen/In Bearbeitung/Erledigt).

**Fairer Vergleich als Follow-up-Träger:**
| Kriterium | lead_reminders | personal_tasks |
|---|---|---|
| Karten-Verdrahtung | **ja** (cardSummaries/context Badges) | nein (eigenes Board) |
| CRM-Bezug | **lead-nativ** (Kunde/Objekt/Gewerk) | vorhanden, aber task-zentriert |
| Dashboard-Query | **fertig** (`due()`) | keine /home-Query |
| Erinnerungs-Mechanik | nur `notified_at`-Flag = **passt zu Stufe 1 (dashboard-only)** | **aktiver Mail/Push-Sender** = Stufe-2-Overkill für jetzt |
| Charakter | Lead-**Beziehungs-Ereignis** | Mitarbeiter-**Aufgaben-Artefakt** |
| Altdaten-Last | 0 | 0 |
| fehlende Spalten | 5 additive (§5) | outcome/art/source-ref fehlen ebenso; Board-Semantik im Weg |

> **⚠️ ÜBERHOLT durch Teil 2b (2026-07-03).** Die folgende Empfehlung galt bis zum /home-Korrektur-Fund; sie ruhte auf der FALSCHEN Prämisse „personal_tasks nicht auf /home". **Gültige Empfehlung: personal_tasks (Teil 2b).** Der Absatz bleibt als ehrlicher Trail stehen.

**~~EMPFEHLUNG: `lead_reminders` erweitern.~~** Begründung (überholt): bereits karten-verdrahtet, lead-nativ, `due()` = fertige Dashboard-Query, und die **dashboard-only Erinnerung (Stufe 1)** passt zu seinem passiven `notified_at`-Modell — personal_tasks' aktiver Sender ist mehr als Stufe 1 braucht und sein Board-Charakter zieht die Follow-up-Semantik in die falsche Richtung. *(Der Board-Charakter-Einwand war der Fehler: das Focus-Today-Widget zeigt personal_tasks sehr wohl auf /home.)*

---
## TEIL 2b — NACHTRAG (2026-07-03): Korrektur + dritter Träger-Kandidat → Empfehlung gekippt

**⚠️ Korrektur eines Fehlers oben:** §6 sagte „personal_tasks: Auf /home: NEIN". **Falsch.** personal_tasks **erscheinen auf /home** — im **„Focus Today"-Widget** (`EmployeeDashboardController::index :68-83` + `getDueToday() :830`), das über den `employees_personal_tasks`-Pivot (`ept.employee_id = ich`) filtert. Die Scheduler-Erinnerung sendet **in-app** (DB-Notification + WebSocket, `ProcessPersonalTaskScheduler :207-228` / `PersonalTaskReminderNotification` via `'database'`), nicht nur Mail.

**Dritter Träger-Fund (Termin-Pfad):** `MainAppointmentController :1254-1324` erzeugt bei `reminder_date`+`next_step`+`report_responsible` (dort **required**) einen **`PersonalTask`** (`type='personal_task'`, `due_date`+`reminder_date`, `controller_id`=Verantwortliche-JSON) + `PersonalTaskKey.task = next_step` + `EmployeesPersonalTask`-Pivot (status='send'). → Der Follow-up-Loop **(a) nächster Schritt + (b) Erinnerung + (c) /home-Sichtbarkeit** ist auf **personal_tasks bereits gebaut** und wird von Terminen genutzt.

**Korrigierter 3-Wege-Vergleich:**
| Yamas Punkt | lead_reminders | **personal_tasks** (Termin-Muster) |
|---|---|---|
| (a) nächster Schritt | fehlt | ✅ (`personal_task_keys.task`) |
| (b) Erinnerung+Fällig | ✅ passiv | ✅ due + Scheduler (in-app + Mail) |
| (c) /home-Sichtbar | **fehlt** | ✅ **Focus-Today-Widget** |
| (d) 3 Ausgänge | fehlt | fehlt |
| Karten-Verdrahtung | ✅ Badges | fehlt |
| Struktur | 1 flache Tabelle | 3 Tabellen + controller_id-JSON |
| „EINE Wahrheit" | cards→lead_reminders = **2. Wahrheit** | Termine nutzen es → **eine Wahrheit** |

**Empfehlung revidiert → `personal_tasks` (Termin-Muster verallgemeinern).** Meine ursprüngliche lead_reminders-Empfehlung ruhte auf der falschen Prämisse „nicht auf /home". Korrigiert kippt das Kernprinzip („EINE Wahrheit, nicht das 6. System danebenbauen") zu personal_tasks: (a,b,c) laufen schon, Termine nutzen es. **Preis:** 3-Tabellen-Struktur + JSON-Zuweisung (Komplexität). **Yama-Entscheidung 2026-07-03: `personal_tasks`** (Entscheidung 1 unten reversiert).

**`lead_reminders` — ausdrücklich VERWORFEN als Follow-up-Träger** (nicht erweitern). Langfrist-Los, damit die 5-Systeme-Landschaft eine Endaussage hat: `lead_reminders` bleibt vorerst **Legacy** in seiner **einen** heutigen Nutzung — der leichte Stage-Wechsel-Reminder als Karten-Badge (`createReminderFromStageChange` → `cardSummaries`), live **0 Zeilen**. Es wird **nicht** erweitert und **nicht** parallel bespielt. **Ziel-Endzustand:** die Stage-Wechsel-Reminder-Erzeugung **mündet später** (eigener Konsolidierungs-Schritt, NACH F1-F5) in den `personal_tasks`-Follow-up-Träger (ein Stage-Wechsel-mit-Nachverfolgung wird ein Follow-up-`personal_task` mit `source_type='lead_product_list'`), dann wird `lead_reminders` retiriert. Bis dahin: **eine** Follow-up-Wahrheit (`personal_tasks`) + ein eingefrorenes Legacy-Badge (`lead_reminders`), **keine** zwei aktiven Follow-up-Systeme.

---
## TEIL 3 — DESIGN (nicht bauen)

### 7. Der Abschluss-Dialog (Kern)
Beim **Erledigen/Berichten** von Notiz/Aufgabe/Bericht auf der Karte → drei Ausgänge:
1. **Vollständig erledigt** → kein Follow-up.
2. **Erledigt, aber Nachfass/weiterer Bericht nötig** → Follow-up (art=nachfass).
3. **Nicht erledigt / weitere Aufgaben nötig** → Follow-up (art=wiederaufnahme).

Bei **2/3 Pflichtfelder:** `nächster Schritt` (Text), `Verantwortlicher` (Mitarbeiter-Auswahl, Default=ich), `Fälligkeit` (Datum), `Erinnerung` (Fälligkeitstag / X Tage vorher / keine).

**Andockung:** an die bestehenden „Erledigen"-Buttons — Pilot an **einem** Flow (Teil 2/F2). **Speicherung:** ein `lead_reminders`-Datensatz (erweitert), Feld-Mapping:
| Dialog-Feld | lead_reminders-Spalte |
|---|---|
| Ausgang (1/2/3) | `outcome` (done→kein Insert; needs_report/needs_tasks→Insert) |
| Art (Nachfass/Wiederaufnahme) | `art` |
| nächster Schritt | `next_step` |
| Verantwortlicher | `responsible_employee_id` (Default auth) |
| Fälligkeit | `reminder_date` (+ `reminder_time`) |
| Erinnerung-Offset | (Stufe 1: nur Anzeige; Offset in `reminder_date` einrechnen oder Meta) |
| Bezug (Notiz/Aufgabe/Bericht/Karte) | `source_type` + `source_id` (+ bestehende lpl/customer-Felder) |
| Status | `open` |

### 8. Dashboard-Widget „Meine Follow-ups"
Exakt nach dem „Zu prüfen"-Muster (fa41c61, `partials/reviews.blade.php`):
- **Ort:** neues `partials/my-followups.blade.php`, `@include` in `mobile.blade.php` neben `reviews` (~:6256); Daten aus `EmployeeDashboardController::index()` (web.php:604) — ein `$myFollowups` analog zu `$reviews`.
- **Query:** `lead_reminders` where `status IN (open)` ∧ `responsible_employee_id = me` ∧ nach `reminder_date` sortiert — **wiederverwendet die `due()`-Logik** (LeadReminderController:76-96), erweitert um next_step/art/Bezug-Anzeige.
- **UI:** Zähler im Header (`.pill danger`), überfällig rot, je Eintrag: nächster Schritt, Kunde/Gewerk, fällig-am; **Aktionen:** `Erledigt` (PATCH done), `Verschieben (+X Tage)` (neuer Status `snoozed`/reminder_date+X), `Zum Kunden` (Profil-Link).
- **KEINE Mail/Push** (Stufe 2).

### 9. Verzahnungen (benennen, nicht bauen)
- **(i) Kanban Stufe D (Wiedervorlage-Zustand):** der follow_up-Zustand einer Karte (aus Stufe A: `status='follow_up'` + `lead_stage_id=offer`) = **ein offener `lead_reminders` mit `source_type='lead_product_list'`**. Das Follow-up-Feld ist damit die Zustands-Mechanik für Stufe D — ein Träger, keine zweite Wahrheit.
- **(ii) Monteur-`next_step` (Weiche 6, Fall 2):** heute enden next_step/due_date als `appointment_reports`-Anzeige (§4). Künftig könnte der Monteur-Report-Fall „erledigt mit Nachfass" **denselben `lead_reminders`-Insert** erzeugen wie der Büro-Abschluss-Dialog → ein Follow-up-Mechanismus für Büro **und** Feld (nicht zwei).
- **(iii) PL-Prüfliste vs. Follow-up:** **Prüfung ≠ Follow-up** (Prüfung = B3-Qualifikations-Gate der Karte; Follow-up = Nachverfolgung eines Vorgangs). **Vorschlag:** getrennte Queries/Partials, aber **ein „Mein Bereich" mit zwei Sektionen** („Zu prüfen (N)" | „Meine Follow-ups (N)") — visuell zusammen, datenmäßig getrennt. *(Yama-Entscheidung 4.)*

### 10. Gestufter Bau-Plan
| Stufe | Inhalt | Umfang | Risiko | Verifikation |
|---|---|---|---|---|
| **F1** | Träger erweitern: **`personal_tasks`** + Spalten `follow_up_art` (nachfass/wiederaufnahme), `source_type` (5 Werte), `source_id` (+ Index); `type='follow_up'`-Konvention. `next_step`/Zuweisung/due/(a,b,c) bestehen bereits (personal_task_keys/employees_personal_tasks/Focus-Today). | 1 additive Migration (3 nullable Spalten) | niedrig (nullable; store/getDueToday/Scheduler/Termin-Pfad unberührt) | Migration up/down; bestehende personal_tasks-Pfade grün; Follow-up-Zeile erscheint beim Verantwortlichen, Scheduler überspringt sie (kein next_reminder_at) |
| **F2** | Abschluss-Dialog an **einem** Flow pilotieren (Vorschlag: Karten-Aufgabe „Erledigen" — hat schon B3-Ausgang) | 1 Dialog + 1 Schreibpfad (lead_reminders-Insert) | mittel (neuer Flow; Legacy-Abschluss unberührt) | Ausgang 2/3 → lead_reminders-Insert korrekt; Ausgang 1 → kein Insert; Feld-Mapping stimmt |
| **F3** | Dashboard-Widget „Meine Follow-ups" (§8) | 1 Partial + `$myFollowups` in index() | niedrig (additiv, Muster fa41c61) | offene Follow-ups des Verantwortlichen erscheinen, überfällig rot, Aktionen erledigt/verschieben/Kunde |
| **F4** | Dialog auf die übrigen Flows ausrollen (Notiz, Kundenbericht, Termin, später Monteur-Report §9-ii) | je Flow 1 Andockung | mittel (Breite) | je Flow: Follow-up entsteht, versickert nicht mehr |
| **F5** | Harness-Szenarien (Follow-up-Fälle) im bestehenden `[TEST-HARNESS]` (df9cd12) | 1 Seeder-Erweiterung | niedrig (nur Testdaten) | Ausgang-2/3-Karte → Follow-up beim Verantwortlichen sichtbar; snooze/done; Teardown 0 |

**Sequenz:** F1 → F2 → F3 → F5(F2-Fälle) → F4. Jede Stufe eigener Pflicht-Stopp.

---
## YAMA-ENTSCHEIDUNGEN (getroffen 2026-07-03 — Design verbindlich)
1. **Träger:** ✅ **`personal_tasks` (Termin-Muster verallgemeinern)** — **reversiert** die ursprüngliche lead_reminders-Wahl nach dem /home-Korrektur-Fund (Teil 2b). F1 = additive Spalten `follow_up_art` + `source_type` + `source_id` auf personal_tasks (+ `type='follow_up'`-Konvention); Loop (a,b,c) besteht bereits.
2. **Abschluss-Dialog:** ✅ **optional anbietbar** (nicht erzwungen). „Vollständig erledigt" schließt ohne Follow-up; kein Zwang.
3. **Erinnerung Stufe 1:** ✅ **nur Dashboard** (kein Mail/Push). ⚠️ Umsetzung mit personal_tasks: Follow-ups setzen **`next_reminder_at` NICHT** → der bestehende Scheduler (sendet Mail/Push) überspringt sie; Anzeige rein über `due_date` im Widget.
4. **Widget:** ✅ **ein „Mein Bereich" mit zwei Sektionen** („Zu prüfen (N)" | „Meine Follow-ups (N)"), datenmäßig getrennt (Follow-up-Sektion filtert `type='follow_up'`). **Interpretation (F3, keine Revision):** „Mein Bereich" = der Container `#view-personal`; die „2 Sektionen" sind die zwei **nebeneinanderstehenden Widgets** (reviews + my-followups) — kein Umbau des committeten reviews-Widgets.

→ **Nächster Schritt: F1 (Träger-Erweiterung auf personal_tasks) als eigener Pflicht-Stopp.**

---
## Gelesen / NICHT gelesen (ehrlich)
**Geprüft (wörtlich, via 3 Explore-Agenten Datei:Zeile + live):** Karten-Menü `kanban.blade.php:2141-2217` + `kanban.js` (Handler 12235/17030/4527); `LeadReminderController` store/due/context/cardSummaries/done (:15-225 wörtlich); `PersonalTaskController@store` (:684-840) + `ProcessPersonalTaskScheduler` (Reminder-Engine); `completeItemWithReport` next_step/due_date-Ziele (:2065/2115/2161) + Leser (`OverdueCenterController:2758`, `DashboardCalendarWidgetController:219`); Dashboard-Einbindung (`mobile.blade.php:6255` @include reviews; `/home`→EmployeeDashboardController web.php:604); Schemas + Live-Counts (lead_reminders/personal_tasks=0, general_tasks=45).
**NUR gegrept / NICHT VERIFIZIERT:** ob eine der beiden Aufgaben-Funktionen (personal_tasks vs. kanban_lead_tasks) auf der Karte die **dominante** ist (Nutzungshäufigkeit nicht messbar, 0 Live-Zeilen personal_tasks); genaues Frontend-Verhalten des Abschluss-Buttons je Flow (nur Endpunkte, nicht jeder Klickpfad durchgespielt); ob `customer_notes`/`customer_reports` irgendwo doch einen Leser mit Fälligkeit haben (Agent fand keinen — Grep, nicht erschöpfend); der genaue „Erinnerung X Tage vorher"-Speicherweg (Meta vs. eingerechnetes Datum) = Design-offen; `general_tasks` (45 Zeilen) bewusst **außen vor** gelassen (nicht karten-gebunden).

## Selbstkritik
- **„next_step versickert" war zu grob** — Korrektur in §4: über `appointment_reports` persistieren + 2 Leser. Die echte Lücke ist nicht Datenverlust, sondern **kein handlungsfähiger Follow-up** (Erinnerung/Ausgang/Verantwortlichen-Dashboard). Wichtiger Unterschied, sonst baut man „Persistenz", die schon da ist, statt „Nachverfolgung".
- **Träger-Empfehlung ist eine Bewertung, kein Beweis** — beruht auf Karten-Verdrahtung + fertiger due()-Query + Passung zur dashboard-only Stufe 1. Bei Bedarf polymorpher (Nicht-Lead-)Follow-ups kippt sie.
- **Zwei Aufgaben-Tabellen auf der Karte** (personal_tasks + kanban_lead_tasks) sind selbst schon eine Doppel-Wahrheit — dieses Design fügt **keine** dritte hinzu (nutzt lead_reminders), aber die bestehende Aufgaben-Dopplung ist ein separates Konsolidierungs-Thema, hier nur benannt.
- **F2-Pilot-Flow ist ein Vorschlag** (Karten-Aufgabe), keine Messung des meistgenutzten Flows — bewusst als Yama-nahe Annahme markiert.

---
*Reine Analyse — nichts am Code/Schema geändert. Querverweise: `follow-up-bestandsaufnahme.md` (d1cadd8), `follow-up-wiedervorlage-konzept.md`, `leads-kanban-korrektur-plan.md` (Stufe A/D), `architektur-entscheidungen.md` (Weiche 1/6). Belege: Datei:Zeile inline + DB-Live 2026-07-03.*
