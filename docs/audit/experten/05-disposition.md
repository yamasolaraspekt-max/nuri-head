# 05 — DISPOSITIONS-EXPERTE: Personal- & Einsatzplanung (Experten-Inventur)

> **Rolle:** Dispositions-Experte (Qualifikations-Matching · Verfügbarkeit · Team-/Termin-/Routenplanung · Montageplan-Erzeugung).
> **Auftrag:** CRM-AUTOMATISIERUNG-MASTER Stufe 1. **REIN LESEND.** Einzige geschriebene Datei: dies.
> **Stand:** 2026-07-10 · Repo `/Users/yamanuri/Documents/ticket` · Branch `private/app-code-backup`.
> **Baut auf:** `docs/audit/intelligenz-audit.md` (Achsen R1/H, Automatisierungsgrad), `docs/audit/automatisierungs-hebel.md` (H-B2, H-V1, H-A1/A2), `docs/audit/code-audit.md` (Datei-Landkarte, Gott-Klassen), `docs/glossar.md` (Gewerk=`lead_product_lists`, Objekt=`lead_alternative_adds`, Auftrag=`deals`).
> **TABU (nur an Nähten betrachtet):** Nuriva/Mobile-API, Video/Jitsi, Invoice, Legacy Bitrix/NIBE/IMAP.
> **Datenbasis-Warnung:** Dev-Restore ist ein Ein-Tages-Seed (~82 % Tabellen leer). Belege sind **Code (strukturell)** + **SQL-Stichprobe** auf dem lebendigen Kern (`main_appointments` 80, `department_positions` 50, `position_qualifications` 26, `master_sets` 13); **absolute Raten sind NICHT ableitbar**. `planner_items`/`planner_plans`/`kanban_lead_tasks`/`personal_tasks` sind im Seed **0 Zeilen** → deren Verhalten ist rein code-belegt, nicht datenbelegt.

---

## 0. Kernthese (eine Zeile)

**Die Disposition rechnet rückwärts statt vorwärts:** Qualifikation, Rang, Innen-/Außendienst-Kapazität, Territorium und Arbeitszeit sind als Datenmodell **vollständig vorhanden und großteils befüllt** — aber sie steuern **nie** die Vorwärts-Frage *„wer ist qualifiziert & frei, das zu tun?"*. Sie werden nur für **Preis** (Angebot), **Nachkontrolle** (Qualifikations-Rückfluss beim Melden) und **Dashboards** benutzt. Wer eine Arbeit macht, wird **immer von Hand** gewählt (sonst Fallback = Ersteller/Projektteam). Eine **Verfügbarkeits-/Doppelbuchungsprüfung existiert an keiner Stelle**.

---

## 1. IST-FUNKTIONEN (mit Beleg)

### 1.1 Termin-Disposition — `main_appointments` + `MainAppointmentController` (3.428 Z.)
- **Termin trägt Gewerk & Ort, aber keine Planner-Bindung.** SQL-Stichprobe (80 Termine): **80/80** an ein Gewerk gebunden (`lead_product_list_id`), **0/80** an ein `planner_item`, **0/80** mit Koordinaten (Spalten `latitude/longitude/full_address` existieren, sind aber leer), `execution_type` durchgehend NULL. Termin-Arten: Übergabe 25 · Beratung 19 · Montagebesprechung 19 · Aufmaß 17.
- **Zuweisung = Blind-INSERT über Pivot `main_appointment_employees`** (80 Zeilen, live). `store()` schleift über die im Formular gewählten Mitarbeiter und legt sie ohne jede Prüfung an: `MainAppointmentController.php:611-617` (`MainAppointmentEmployee::create([... 'status'=>'accept'])`). `update()` löscht alle Pivot-Zeilen und legt sie neu an (`:1168` delete, `:1248-1254` re-insert).
- **Innen-/Außendienst-Struktur ist im Formular vorgesehen** (`employee_id` = Innendienst, `field_employee_id` = Außendienst, je Inquiry-Zeile `:339-340`), aber der Default ist **der erste gewählte Mitarbeiter für beide Rollen** bzw. der aktuelle Nutzer (`:704-705` `fallbackIndoorId = fallbackFieldId = appointmentEmployeeIds[0]`). Keine Ableitung nach Rolle.
- **Einzige „Dublettenprüfung"** ist ein De-Dup *innerhalb desselben Termins* (gleicher Mitarbeiter schon auf **diesem** Termin?), kein Zeitkonflikt: `:810-815`.
- **Mitarbeiter-Kandidatenliste** kommt aus `InquiryController::departmentEmployees` — füllt nur die Dropdowns: `innendienst_employees` und `aussendienst_employees` werden **byte-gleich** aus derselben Query gebaut (`InquiryController.php:1736-1761`), d. h. beide Listen = alle Abteilungsmitglieder, **kein** Innen/Außen- oder Qualifikationsfilter.
- **Verfügbarkeits-Anzeige (passiv):** `InquiryController::availability` (`:2039`) lädt die Termine der Woche für die gewählten Mitarbeiter und gibt sie als Kalender-`events` zurück — **reine Anzeige, schlägt nichts vor, blockiert nichts**.

### 1.2 Montageplan-Erzeugung — `PlannerPlanController` (11.097 Z.) + `planner_plans`/`planner_items`
- **Einstieg ist manuell und bestätigungs-gated** (kein Auto-Trigger aus Auftragsannahme). Route `POST /projects/store` → `storeProjectFromLeadProduct` (`routes/web.php:5192`; `PlannerPlanController.php:7158`). Ist das Gewerk noch nicht in Montage-Phase und `force_montage` fehlt → **HTTP 409 `requires_confirmation`** mit Rückfrage „…noch nicht in Montage. Trotzdem übernehmen?" (`:7173-7179`); Historien-Eintrag „**Manuell** über Projektplanung erstellt" (`:7219`). → **Weiche 6 (Montageplan wird GEPLANT, nicht auto) im Erzeugungs-Trigger eingehalten.**
- **Aber: nach Auslösung wird die Struktur aus Vorlagen halb-automatisch geseedet** (`syncProjectScopedPlan` `:4468` ruft `syncAppointments/syncTickets/syncPersonalTasks/syncPhaseActivities/syncMasterSets`):
  - **MasterSet → planner_items:** `syncMasterSets` (`:840`) matcht `MasterSet::where('article_group_id', productId)` und materialisiert je Vorlage ein `PlannerItem::firstOrCreate([... source_type='master_set' ...])` (`:859-873`). Dev: `master_sets` 13 Zeilen, alle 13 mit gesetztem `responsible_department_position_id` → die **zuständige Rolle je Vorlage ist gepflegt**.
  - **Phase/Activity → planner_items:** `syncPhaseActivities` (`:466`) lädt `task_phases`/`phase_activities` (Dev: 13 / 49) für Produkt+Stage und upsertet je Aktivität ein Item (`pmoUpsertTemplatePlannerItem` `:519/:555`).
- **Termine (Dates) werden NICHT auto-vergeben:** Vorlagen-Items entstehen mit `planned_start_at=null`/`planned_end_at=null` (`:524-525`, `:560-561`, `:865-872`, Status `open`). → **Strukturplan ja, Zeitplan Handarbeit.**
- **Mitarbeiter-Vorbelegung existiert als Vorschlag** (nicht als Auto-Scheduling): `pmoEmployeeIdsForTemplatePhase` (`:717`) sammelt IDs aus der Activity-Zeile, `activity_employees` und `customer_suggest_employees` und hängt sie via `syncPlannerItemEmployees` an (`:833`). **Dev-Realität: `activity_employees`=0, `customer_suggest_employees`=0** → die Vorschlagsquelle ist im Seed leer; faktisch bleibt nur der Display-Fallback „Projektteam" (`montageWorkPayload` `auto_assigned_from_project_team=true` `:6031`, laut Kommentar `:6014-6022` reine Render-Fallback, überschreibt keine Quelle).
- **Datenmodell-Klärung:** `planner_plans.project_id` → **`lead_product_lists`** (Gewerk), **kein `deal_id`** (Model `PlannerPlan.php:16-26`; Migration `2026_01_21_082633:21,37`). Die **`projects`-Tabelle (31 Z.) ist separat/legacy**, nicht mit `planner_plans` verbunden (eigene Migration `2024_10_23_101624`, FKs auf `new_leads`/`article_groups`/`employees`). → Bestätigt Glossar §3.1 „Projekt hat zwei Bedeutungen".

### 1.3 Kanban-Aufgaben — `kanban_lead_tasks` + `KanbanLeadTaskController`
- **Bearbeiter = manuell, sonst aktueller Nutzer.** Beide Erzeugungspfade: `'performer_employee_id' => $data['performer_employee_id'] ?? $employeeId` (`KanbanLeadTaskController.php:444` storeManual, `:553` storeFromTemplate), wobei `$employeeId = authEmployeeId()` = `(int) auth()->user()->name` (`:1094-1097`). `created_by_employee_id` immer aktueller Nutzer.
- **Reiche Anker, aber nur gespeichert:** `product_id/lead_stage_id/alternative_id/customer_id` aus dem Gewerk kopiert (`:426-430`), `task_phase_id/phase_activity_id` aus Vorlage (`:540-541`). Konsumiert werden sie **nur** für Vorlagen-Validierung/Idempotenz/Planner-Verlinkung (`:491`, `:514-517`, `:576-579`) — **nie** zur Personenauswahl. Deckt sich mit Intelligenz-Audit **R1**.

### 1.4 Qualifikations-/Rang-Maschinerie — vorhanden, aber „rückwärts" verdrahtet
- **Rang-Katalog:** `position_qualifications` (26 Z.) ist eine Rang-Leiter mit `sort_order` + `default_price` (Geschäftsführung `sort=1`, Elektromeister `2`, Meister `3`, Geselle `4`, PV-Monteur `5` … Helfer/Ausbildung unten) und Stundensatz (Elektromeister 75 €, PV-Monteur 48 €).
- **Rang-Hierarchie hat echte Logik** (`PositionQualificationHierarchy::isAllowed()/ruleFor()` `:36-65`), wird aber **nur als Nach-Kontrolle beim Melden** durchgesetzt — nicht zur Auswahl: `PlannerEmployeeApiController::applyMontageQualificationRueckfluss` (`:1875-1938`): meldet ein Monteur ein Montage-Item, wird bei `performer.sort_order <= required.sort_order` die Karte auto-`done` (`:1916-1918`); ist er **nicht** qualifiziert → Karte `reported` + Prüfer per **Vorgesetztenkette** (`employees.supervisor` hochklettern bis qualifiziert, `resolveReviewer` `:1994-2018`) gesucht. **Reagiert auf den, der gearbeitet hat — wählt ihn nie aus.**
- **Weitere Nutzung der Hierarchie ist nicht-erzwingend:** Preis-Alternativen im Angebot (`OfferFolderController::laborQualificationOptions:3690-3760`), UI-Rechner (`PositionController::hierarchyCheck:139-179`), Matrix-Seed (`hierarchyAutoGenerate:91-137`).
- **Kapazitäts-Dimension liegt in Daten bereit, ungenutzt für Planung:** `department_positions` (50 Z.) trägt je Mitarbeiter `montage_percent`/`office_percent` (SQL: **30 montage-fähig, 20 büro-fähig**) — gelesen **nur** in Dashboards (`DashboardDepartmentController.php:1288-1289`, `DashboardCompanyController.php:192-193`), nie in einer Zuweisung.
- **Aktivitäts-Pivots sind inerte Speicher:** `ActivityEmployee/ActivityDepartment/ActivityPosition/EmployeeActivitySet` — nur `$fillable`, kein Verhalten (Belege in den Model-Dateien). Dev alle 0 Zeilen.

### 1.5 Verfügbarkeit / Doppelbuchung / Abwesenheit — **komplett abwesend im Buchungspfad**
- **Planner-Zuweisung = Delete+Blind-INSERT:** `syncPlannerItemEmployees` löscht `planner_item_employees` und fügt neu ein, ohne jede Verfügbarkeits-Query (`PlannerPlanController.php:278-296`), aufgerufen aus `:833/:944/:1049/:1125`.
- **Einzige Zeitfenster-Query ist ein LADE-Filter, kein Block:** `pmoApplyDateScope` (`:6472-6483`) baut ein Overlap-WHERE, um Items für die Kalenderansicht **zu holen** — es weist keine Buchung ab. Voll-Grep der 11k-Datei nach `kapazit|capacity|overlap|collision|conflict|doppel|verfüg|available|belegt` = nur dieser Scope + produktbezogene `availability`-Spalten (`:9613`).
- **Abwesenheit wird beim Planen nie konsultiert:** `EmployeeSick/EmployeeShortLeave/EmployeeRecurringLeave*/EmployeeMonthlyTimeBudget/TimeSummary` haben **0 Importe** in `Planner/` und `Appointment/`; sie leben nur in HR-Dashboards (`EmployeeCapacityStateController.php:94-98/267-268`, `DashboardEmployeeStatusController.php:132/275`, `EmployeeDashboardController.php:148/2251`). Arbeitszeit-Daten sind da: **51/51** Mitarbeiter mit `daily_start_time`, **49/51** mit `supervisor`.
- **Keine Frei-Slot-/Vorschlags-Funktion.** Deckt sich mit Automatisierungs-Hebel **H-B2** (Kapazitäts-/Doppelbuchungs-Check fehlt am häufigsten Ereignis „Termin").

### 1.6 Routen-/Geografieplanung — **existiert nicht**
- Kein Routing/Clustering/Umkreis/Nächster-Trupp: Distanz-Mathe-Grep (`haversine|ST_Distance|nearest|umkreis|radius`) = 0 relevante Treffer; die 398 `lat/lng`-Treffer sind GPS-Attendance (`PlannerEmployeeApiController::loadLatestLocation:905-931`), Karten-Anzeige, Wetter, PV-Dach-Geocoding (`GoogleGeocoder.php:8`) — **nichts** speist Truppauswahl.
- **`employee_postcode_lists`** (Modell `employee_id/postcode_from/postcode_to`, PLZ-2-stellig als Territorium `EmployeePostcodeListController.php:44-45`) ist **reine CRUD-Stammdatenpflege** (`EmployeeController.php:132/573` = Anzeige) — **kein** Code liest eine Kunden-/Objekt-PLZ, um daraus einen Mitarbeiter zu wählen. Dev: 0 Zeilen. Totes Feature-Fundament.

---

## 2. STÄRKEN

1. **Weiche 6 im Trigger sauber umgesetzt.** Montageplan-Erzeugung ist ein bewusst manueller, **bestätigungs-gated** Schritt (409 `requires_confirmation`, `:7173-7179`) — kein stiller Auto-Trigger. Das entspricht exakt dem Kuratier-Prinzip.
2. **Vorlagen-Seeding ist gebaut und funktioniert** (`syncMasterSets`/`syncPhaseActivities`): der Strukturplan entsteht aus `master_sets` (13, alle mit `responsible_department_position_id`) und `phase_activities` (49) — der teure Teil („welche Arbeitsschritte hat dieses Gewerk") ist automatisiert; der Planer baut nicht bei null an.
3. **Qualifikations-Nachkontrolle mit Vorgesetzten-Eskalation ist echte Intelligenz** (`applyMontageQualificationRueckfluss` + `resolveReviewer`, `:1875-2018`): ein Melde→Prüf-Mechanismus mit Rang-Vergleich und automatischer Prüferfindung über die Vorgesetztenkette. Das ist der einzige Ort, an dem der Rang wirklich *entscheidet* — und er ist gut gebaut.
4. **Datenmodell der Disposition ist reich und großteils befüllt:** Rang-Leiter mit `sort_order` + Satz (26), Innen/Außen-Kapazität je Mitarbeiter (`montage_percent`/`office_percent`, 30/20), Vorgesetztenkette (49/51), Arbeitszeiten (51/51), Territorium-Tabelle, Abwesenheits-Tabellen. **Das Fundament für Matching/Verfügbarkeit steht — es wird nur nicht abgefragt.**
5. **Gewerk-Anker `lead_product_lists.department_id` ist 52/52 gefüllt** — der Zuständigkeits-Anker für „Gewerk → Abteilung" liegt bereit (Hebel **H-V1**).
6. **FK-Sauberkeit des Planners:** `planner_plans.project_id` → `lead_product_lists` mit `firstOrCreate`-Idempotenz (kein Doppel-Plan), planner_items mit `source_type/source_id`-Herkunft.

---

## 3. SCHWÄCHEN

| # | Schwäche | Beleg | Klasse (a/b/c) | Bezug |
|---|---|---|---|---|
| D-1 | **Kein Verfügbarkeits-/Doppelbuchungs-Check** — zwei Termine/Items auf dieselbe Person zur selben Zeit ohne Warnung | `MainAppointmentController.php:611-617`; `PlannerPlanController.php:278-296`; Overlap nur als Lade-Scope `:6472-6483` | (a) sicher ableitbar | H-B2 |
| D-2 | **Abwesenheit/Urlaub/Krank beim Planen ignoriert** — nur in HR-Dashboards konsumiert | 0 Importe von `EmployeeSick/…Leave/…Budget` in `Planner/`+`Appointment/` | (a) | H-B2 |
| D-3 | **Kein Qualifikations-*Matching* vorwärts** — Rang/Qualifikation wählt nie den Bearbeiter, nur Nach-Kontrolle | `applyMontageQualificationRueckfluss:1875-1938` (post-hoc); Auswahl stets Picker | (b) Vorschlag+Bestätigung | R1 |
| D-4 | **Aufgaben-Routing manuell/Ersteller** — `performer ?? currentUser`; reiche Anker nur gespeichert | `KanbanLeadTaskController.php:444/:553`; `authEmployeeId:1094` | (a)+(b) | R1, H-V1 |
| D-5 | **„Gewerk→Abteilung→Innen/Außen→Person" nur Dropdown-Füllung** — Innen==Außen byte-gleich, kein Filter | `InquiryController::departmentEmployees:1736-1761` | (a) | H-V1 |
| D-6 | **Keine Routen-/Geografieplanung**; `employee_postcode_lists` totes CRUD-Fundament (0 Konsumenten) | Grep 0 Distanz-Logik; `EmployeePostcodeListController` nur CRUD | (b) Fach/Neubau | — |
| D-7 | **required_qualification_id je Aktivität leer** (Schema da, 0/49 befüllt) → selbst die Nach-Kontrolle hat in Dev keine Soll-Qualifikation zum Prüfen | SQL `phase_activities` 49/0; Rückfluss-Code liest `required_qualification_id` `:1908` | (a) Daten pflegen | — |
| D-8 | **Termin↔Planner entkoppelt** — 0/80 Termine mit `planner_item_id`; Aufmaß/Montagebesprechung/Übergabe erzeugen keinen Planner-Zeitpunkt | SQL `main_appointments` `has_planner_item=0` | (c) Medienbruch | Weiche 6-Naht |
| D-9 | **Alpine-Regelverletzung Planner-Liste** (aus Code-Audit übernommen) | `planner/list.blade.php:225` `x-data` (geroutet `:2898`) | Hygiene | code-audit |
| D-10 | **Gott-Klasse untestbar** — 11.097 Z., keine Verhaltens-Tests, jede Änderung hochriskant | `PlannerPlanController.php`; code-audit „Tests DÜNN/API-Contract" | Wartbarkeit | code-audit |
| D-11 | **Autorisierung dormant** — Planner-/Termin-Schreibrouten nur `auth`-gated (kein `permission:`) | code-audit 2.2a (5/1211 Write-Routen gegated) | Sicherheit | code-audit |

**Verantwortungsmatrix (Wissens-Register DOC-004):** Es existiert **keine** „wer ist verantwortlich für welches Gewerk/welche Phase"-Engine (Grep `verantwortungsmatrix` leer). Die einzige „Matrix" ist das Rang-Fähigkeits-Grid (`PositionController::hierarchyBoard` → `_hierarchy_matrix`), ein Rang-*Katalog*, keine Gewerk-Zuordnung. Verantwortungs-*Herkunft* existiert nur punktuell: `master_sets.responsible_department_position_id` (13/13, Vorlagen-Ebene) + die Rückfluss-Prüferkette. **Eine belastbare Verantwortungsmatrix Gewerk×Phase×Rolle ist NICHT gebaut — das Rohmaterial (department_positions, master_sets.responsible…, position_qualifications) liegt aber vor.**

---

## 4. REIFE je Teilfunktion (1 stumme DB … 5 mitdenkendes Assistenzsystem)

| Teilfunktion | Reife | Begründung (Beleg) |
|---|---|---|
| **Termin-Disposition** | **2** | Erfassung + Gewerk-Bindung sauber (80/80), aber Blind-INSERT, keine Kapazität, keine Planner-Bindung (0/80) |
| **Qualifikations-Matching (Auswahl)** | **1** | Kein Vorwärts-Matching; Rang wählt nie (D-3) — reine Speicherung |
| **Qualifikations-Nachkontrolle (Rückfluss)** | **3–4** | Echter Rang-Vergleich + Prüfer-Eskalation über Supervisor-Kette (`:1875-2018`) — die *eine* mitdenkende Stelle, aber post-hoc & req_qual-Daten leer (D-7) |
| **Verfügbarkeit/Doppelbuchung** | **1** | Existiert nicht; nur passive Wochen-Anzeige (D-1/D-2) |
| **Team-/Aufgaben-Zuweisung** | **2** | Manuell/Ersteller-Default; Anker vorhanden aber ungenutzt (D-4/D-5); Team = manuelle JSON-Liste am Gewerk |
| **Montageplan-Struktur** | **3** | Vorlagen-Seeding aus MasterSet/PhaseActivity funktioniert (halb-automatisch) — Struktur ja |
| **Montageplan-Zeitplanung** | **2** | Dates komplett Handarbeit (`planned_start_at=null`), bewusst (Weiche 6) — aber damit per Definition kein Automatismus |
| **Routen-/Geografieplanung** | **1** | Nicht vorhanden; Territorium-Tabelle totes CRUD (D-6) |
| **Verantwortungsmatrix** | **1–2** | Keine Engine; nur Vorlagen-Zuständigkeit + Rückfluss-Kette als Fragmente |

---

## 5. AUTOMATISIERUNGS-REIFE gesamt (Disposition)

**Gesamturteil: Reife ~2 („überwiegend stumme Disposition") mit einer 3–4-Insel (Qualifikations-Rückfluss).**

Die Disposition ist der klarste Fall des Audit-Leitbefunds *„Intelligenz gebaut, aber nicht verdrahtet"* — hier sogar **invertiert**: das reiche Rang-/Kapazitäts-/Territorium-Modell steuert die drei *falschen* Enden (Preis, Nachkontrolle, Dashboard) und **keines** der beiden richtigen (Auswahl „wer soll?", Prüfung „wer ist frei?"). Der Planner **seedet Struktur klug aus Vorlagen** (Reife 3), lässt aber **Zeit und Person** am Menschen — bei den Personen fehlt nicht nur die Automatik, sondern (Dev) auch die Vorschlagsdaten (`activity_employees`/`customer_suggest_employees` = 0). Die **teuersten, sichersten Hebel liegen hier im Ableiten aus bereits vorhandenen Daten**: Doppelbuchungs-/Abwesenheits-Warnung (D-1/D-2, Klasse a, häufigstes Ereignis Termin) und Zuständigkeits-*Vorschlag* nach Abteilung/Rang (D-4/D-5/D-3, Klasse a+b), beide mit gefülltem Anker (`department_id` 52/52, `department_positions` 50). **Grenze (Yama-Entscheidung):** Montageplan-*Erzeugung* bleibt kuratiert (Weiche 6) — der Automatisierungsgewinn liegt in **Warnung/Vorschlag**, nicht in Voll-Automatik der Zuweisung.

**Priorisierte Dispositions-Hebel (Wirkung ÷ Aufwand):**
1. **D-1/D-2 Doppelbuchungs-/Abwesenheits-Warnung am Termin** (a, S–M) — Overlap-Query gegen `main_appointment_employees`+`planner_item_employees`, Abgleich `EmployeeSick/…Leave`; die Lade-Scopes (`:6472`) sind fast der Baustein. = H-B2.
2. **D-3/D-4/D-5 Zuständigkeits-*Vorschlag*** aus `department_id`→`department_positions`(montage/office)→Rang, überschreibbar (a+b, M). = H-V1/R1. Nutzt zugleich die tote Vorschlagsquelle `customer_suggest_employees`.
3. **D-7 `phase_activities.required_qualification_id` pflegen** (a, S/Datenpflege) — aktiviert die bereits gebaute Rückfluss-Nachkontrolle mit echten Soll-Qualifikationen.
4. **D-8 Termin↔Planner-Kopplung** (c, M) — Aufmaß/Übergabe/Montagebesprechung als Planner-Zeitpunkte, schließt den Medienbruch.
5. **D-6 Territorium/Route** — nur, wenn Yama es fachlich will (b/Neubau); Fundament (`employee_postcode_lists`) existiert, alles darüber fehlt.

---

## 6. Gelesen / Nicht-gelesen · NICHT-VERIFIZIERT · Selbstkritik

**Gelesen/gemessen (firsthand):** `docs/audit/intelligenz-audit.md`, `docs/audit/automatisierungs-hebel.md`, `docs/glossar.md` (vollständig), Disposition-Teile von `docs/audit/code-audit.md`. **SQL firsthand:** Row-Counts + Spalten der Kern-Dispositionstabellen (`main_appointments`, `main_appointment_employees` 80, `position_qualifications` 26 mit sort_order/Preis, `department_positions` 50 mit montage/office, `employee_departments` 50, `employees` 51 mit daily_start/supervisor, `master_sets` 13 mit responsible_…, `phase_activities` 49 req_qual **0/49**, `lead_product_lists.department_id` 52/52, `planner_items`/`planner_plans`/`kanban_lead_tasks`/`personal_tasks`/`customer_suggest_employees`/`activity_employees` = 0); Termin-Verteilungen (Typ, Gewerk-/Planner-/Koord-Bindung). Modelle firsthand: `DepartmentPosition`, `AppointmentEmployee`. **Via Explore-Agenten (belegt mit file:line):** Verfügbarkeit/Doppelbuchung (MainAppointment/Planner/HR-Nutzung), Qualifikations-Matching (Position/Hierarchy/Suggest/Inquiry/Rückfluss), Montageplan-Erzeugung (storeProjectFromLeadProduct/syncMasterSets/syncPhaseActivities/PlannerPlan-Schema), Routen+kanban_lead_tasks (Geo-Grep/EmployeePostcode/KanbanLeadTask::store).

**Nicht gelesen / nur oberflächlich:** die Methodenkörper der 11k-Z.-`PlannerPlanController` außerhalb der genannten Methoden; `PlannerEmployeeApiController` (2.375 Z.) außer Rückfluss/loadLatestLocation; die Montage-/Planner-Blades und `kanban.js`; `CustomerMainAppointmentController` (1.795 Z.); Mobile-/Nuriva-Planner-API (TABU).

**NICHT-VERIFIZIERT:**
- **„0 Aufrufer/keine Prüfung" = statischer Grep** (Verfügbarkeit, Route, Qualifikations-Matching) — stark, aber kein Beweis gegen Reflection/String-Dispatch oder **clientseitige** Prüfung in Blade/JS. Ob das Frontend eine Doppelbuchungs-/Verfügbarkeitswarnung oder einen Zuständigen-Vorschlag clientseitig leistet, ist **serverseitig widerlegt, clientseitig nicht geprüft**.
- **Rückfluss-Kette (`:1875-2018`) code-belegt, nicht zur Laufzeit ausgeführt** — Verhalten aus Lesen abgeleitet.
- **Dev-Seed verzerrt die Person-Vorschlagslage:** `activity_employees`/`customer_suggest_employees`/`kanban_lead_tasks`/`planner_items` = 0 Zeilen; in Prod könnten diese Vorschlagsquellen gefüllt sein → dann wäre die Personen-Vorbelegung im Planner realer als hier sichtbar. Aussage „Person = manuell/Fallback" ist **code-strukturell**, nicht volumenbelegt.
- **`main_appointment_employees.employee_id`-Kopplung:** Werte sind numerische Employee-IDs (Stichprobe 122/164…), es existiert aber auch ein `name`-basierter Filterzweig (`MainAppointmentController.php:68`) — mögliche fragile Doppelkopplung (vgl. Intelligenz-Audit R3), **nicht abschließend geprüft**.
- **TABU respektiert:** Nuriva-Mobile-Planner-API, Video, Invoice, Legacy nur an Nähten betrachtet.

**Selbstkritik:** Der Befund „Disposition rechnet rückwärts" ist die stärkste Einzelaussage, stützt sich aber auf zwei firsthand gelesene Kernstellen (Rückfluss + Vorlagen-Seeding) plus Agenten-Grep für die Negativ-Belege (kein Matching/keine Kapazität). Die Reife-Note der Montageplan-Struktur (3) beruht auf `syncMasterSets`-Code, nicht auf einem ausgeführten Plan (Dev hat 0 planner_items). Frequenz-/Volumenaussagen fehlen bewusst (Ein-Tages-Seed) — die Hebel-Reihenfolge (D-1/D-2 vor Rest) ist mit „Termin = häufigstes Kern-Ereignis (80)" plausibilisiert, nicht raten-gemessen.

---

## 7. Fünf-Zeilen-Zusammenfassung

1. **Wer eine Arbeit macht, wird immer von Hand gewählt** (sonst Fallback = Ersteller/Projektteam); ein Qualifikations-/Rang-*Matching* zur Auswahl existiert nirgends — Rang entscheidet nur *nachträglich* beim Melden (Rückfluss + Prüfer-Eskalation über Vorgesetztenkette, die einzige mitdenkende Stelle).
2. **Eine Verfügbarkeits-/Doppelbuchungsprüfung gibt es an keiner Stelle** — Termin- und Planner-Zuweisung sind Blind-INSERTs; Urlaub/Krankheit/Zeitbudget werden nur in HR-Dashboards angezeigt, nie beim Planen abgefragt (H-B2 offen).
3. **Der Montageplan wird korrekt manuell + bestätigungs-gated ausgelöst (Weiche 6 gewahrt)** und dann **halb-automatisch aus MasterSet-/Phase-Vorlagen strukturell geseedet** — aber Termine und Personen bleiben Handarbeit (Person-Vorschlagsquellen im Dev leer).
4. **Das reiche Dispositions-Datenmodell ist invertiert verdrahtet:** Rang, Innen/Außen-Kapazität (`department_positions`), Territorium (`employee_postcode_lists`), Arbeitszeiten und `department_id` (52/52) sind da und großteils befüllt, steuern aber nur Preis/Nachkontrolle/Dashboards — keine Routen-/Geografieplanung, keine echte Verantwortungsmatrix Gewerk×Phase×Rolle.
5. **Automatisierungs-Reife gesamt ~2 mit 3–4-Insel Rückfluss;** die billigsten, sichersten Hebel sind Ableitungen aus bereits vorhandenen Daten: Doppelbuchungs-/Abwesenheits-*Warnung* (a) und Zuständigkeits-*Vorschlag* nach Abteilung/Rang (a+b), beide überschreibbar — Voll-Automatik der Zuweisung bleibt bewusst außen vor.
