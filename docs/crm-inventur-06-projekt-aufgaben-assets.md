# CRM-Inventur 06 — Projektmanagement / Aufgaben / Tickets / Fuhrpark & Assets

**Read-only Analyse, nichts geändert.** Stand: 2026-07-01 · Zone 06 von mehreren Inventur-Zonen.
Ziel: Breiten-Überblick (BREITE vor TIEFE) über Projekt-/Planer-Bereich, Aufgaben, Tickets/Reklamationen und Fuhrpark/Assets.

> **Verweis:** Der Termin-/Kalender-Bereich (`main_appointments`, `CustomerMainAppointmentController`, Ticket-Termine)
> ist bereits in [`docs/kalender-termine-bestandsaufnahme.md`](kalender-termine-bestandsaufnahme.md) kartiert und wird hier
> **nur referenziert, nicht wiederholt**. Abteilungen/HR (Employees/Departments) gehören zu Zone 05.

> **Glossar** (projektweit): Kunde = `new_leads` · Objekt = `lead_alternative_adds` · Gewerk = `lead_product_lists` · Mitarbeiter = `employees`.

> **Kernbefund:** In dieser Zone laufen **drei parallele Phasen-/Aufgaben-Systeme** nebeneinander
> (klassisch phase_sections→…, kanban_lead_tasks, planner_plans→planner_items) **plus** ein separates
> `projects`-Universum (14 Tabellen) **plus** zwei eigenständige Aufgaben-Welten (`personal_tasks`, `general_tasks`).
> Der Planer-Bereich ist mit `PlannerPlanController` (**11.080 Zeilen**) der mit Abstand größte Controller-Knoten
> und braucht eine eigene Detail-Inventur (siehe Abschnitt am Ende).

---

## 1. Planer / Projekt-Cockpit / Montageplanung (NEU, flexibel — planner_plans → planner_items)

**(a) Zweck.** Zentrales, neues Planungssystem. Route `/planner` (Sidebar „Projektplanung") redirectet auf
`planner.projects` = **Projekt-Cockpit** (Kanban der Projekte je Bauphase/Stage), von dort in Montage-Arbeitslisten,
Terminplanung (Drag&Drop-Kalender), Material, Master-Sets, Anwesenheit. „stage" = montage | inbetriebnahme | … .
Angebunden an eine mobile Monteur-App (Nuriva-Integration) über eine eigene Token-Auth-API.

**(b) Controller / Routen.**
- Web: `Route::prefix('planner')` (routes/web.php ab Z. 5149), dominiert von **`Planner\PlannerPlanController`** — u.a. `projectCockpit`, `projectCockpitData`, `projectKanbanData`, `projectCandidates`, `storeProjectFromLeadProduct`, `ensureProjectPlan`, `moveProjectKanban`, `projectProfile`, `saveProjectTeam`, `montageWorkPayload`, `storeProjectWorkItem`, DnD `add`/`move`, Item-Comments/Gallery, `index` (cockpit) u.v.m.
- `Planner\PlannerItemStateController` (214), `PlannerItemMaterialController` (610), `PlannerMasterSetController` (229), `PlannerAttendanceController` (652).
- Mobile/API: `Api\MobilePlannerApiController` (213, `/mobile/tasks`+`sync`), `Planner\PlannerApiAuthController` (930, Token-Auth), `Planner\PlannerEmployeeApiController` (2214, `my-work`/`day-report`/`completeItemWithReport`), `Planner\PlannerMobileCustomerImageController` (297). API-Gruppe „Planner / Nuriva Integration API" (routes/api.php ab Z. 224).

**(c) Kern-Tabellen.** `planner_plans` (customer_id→new_leads, project_id→**lead_product_lists**, stage, status draft/published/archived, meta json) · `planner_items` (plan_id, source_type = phase_activity|appointment|manual|ticket_task|personal_task, status open→done, planned_start/end, duration_minutes) · Satelliten: `planner_item_employees`, `planner_item_dependencies` (+reason), `planner_item_assets`, `planner_item_checklists`, `planner_item_master_sets`, `planner_item_materials` (+group_material), `planner_group_materials`, `planner_item_material_requests` (+accept/reject), `planner_item_id_to_main_appointments`, `planner_status_history`, `master_set_tasks`/`master_set_task_labors`/`master_set_checklists`. Verknüpfung zu Alt-Welten via `add_planner_id_to_personal_tasks_and_problems`.

**(d) Größe.** SEHR GROSS. Ein Controller mit 11.080 Zeilen + 8 weitere (zus. ~5.400 Z.), ~25 Migrationen (viele davon 2026, „hot"/aktiv in Entwicklung — mehrfach nachträgliche „add_missing_columns"/„fix"-Migrationen).

**(e) Status.** Aktiv, neu, jüngstes System (2026). Wächst schnell (Attendance, Material-Requests, Status-History in 06/2026 nachgezogen). Konsumiert die anderen Systeme als `source_type`. **Braucht eigene Detail-Inventur.**

---

## 2. Projekt-Universum (klassisch — projects + ~13 Satelliten-Tabellen)

**(a) Zweck.** Ältere, tabellen-schwere Projekt-/Montage-Repräsentation (ein Projekt = Kunde × Produkt × Objekt × Gewerk/Service).
Trägt Zeitplan (project_start/montage_start/end_date), Projektleiter, Team, Timeline, Awards/Coins (Gamification), Feedback, Kontrollpersonen, Zeitanfragen. Wird vom neuen Planer als `project_id`/Datenquelle mitbenutzt.

**(b) Controller / Routen.** Kein dedizierter „ProjectController" mehr in Betrieb — der Zugriff läuft heute überwiegend über `Planner\PlannerPlanController` (dessen `/projects/*`-Routen), Phase-/Task-Controller und `Old\ProjectTaskAttachmentController` (Alt). Eigenständig nur `Project\AddEmployeeToProjectController` (Team-Zuordnung).

**(c) Kern-Tabellen.** `projects` (customer_id→new_leads, product_id→article_groups, alternative_id→lead_alternative_adds, service_id→phase_sections, project_leader, project_status/priority/status, softDeletes) + `project_tasks`, `project_task_comments`, `project_task_attachments`, `project_montage_checklists`, `project_montage_phase_lists`, `project_timelines` (+`project_timeline_done_dates`), `project_awards`, `project_feedback`, `project_control_people`, `project_time_requests`, `employee_project_coins`, `add_employee_to_projects`. **14 Tabellen.**

**(d) Größe.** GROSS in Tabellen (14), aber klein in eigener Controller-Fläche (Logik liegt in PlannerPlanController + Phase-Controllern verstreut).

**(e) Status.** Etabliert (2024–2025), teils in den neuen Planer aufgehend. Übergangszustand: `projects` lebt weiter, wird aber zunehmend vom Planner-Layer überlagert. **Braucht eigene Detail-Inventur** (Verhältnis projects ↔ planner_plans klären).

---

## 3. Phasen-System klassisch (phase_sections → task_phases → phase_activities → task_sub_tasks / task_to_dos)

**(a) Zweck.** Vorlagen-/Ablauf-Definition je Produkt: Gewerk-Abschnitte (sections) → Phasen → Aktivitäten → Unteraufgaben.
Aus diesen Vorlagen entstehen die konkreten Ausführungs-Aufgaben (`task_to_dos`, `project_tasks`). Basis für Bauphasen-Checklisten.

**(b) Controller / Routen.**
- `Phase\TaskPhaseController` (1697, Routen `/task_phase*`, `/task-phases/*`, manager-sections, cleanup-duplicates), `Phase\PhaseSectionController` (179, `/task_section_*`, `/phase-sections/*`), `Phase\PhaseActivitiesController` (365), `Phase\TaskSubTaskController` (143 — **DEPRECATED**), `Phase\PhaseCopyController`, `Phase\LeadTaskPhaseManagementController` (1104), `Phase\LeadStageAdminController` (538).
- Kunden-Instanz-Ebene: `CustomerPhaseListController`, `CustomerPhaseStageController`, `CustomerStageController`, `CustomProcessStageController` (Root-Controllers).

**(c) Kern-Tabellen.** `phase_sections` (product_id, phase_section, sort_order) · `task_phases` (product_id, section_id, phase_name, stage, order; + lead_stage-Felder 06/2026) · `phase_activities` (phase_id, section_id, stage_id, title, duration) · `task_sub_tasks` (**deprecated**) · Ausführung: `task_to_dos`, `project_tasks` · Instanzen: `customer_phase_lists`, `customer_phase_stages`, `phase_stages`, `lead_product_checklist_values`, `task_documents`.

**(d) Größe.** MITTEL–GROSS (7 Phase-Controller, ~4.500 Z. zusammen; `task_sub_tasks` DEPRECATED).

**(e) Status.** Etabliert (2023), teilweise abgelöst: `task_sub_tasks` tot; 06/2026 mit „lead_stage"-Feldern nachgerüstet → Übergang Richtung kanban_lead_tasks. **Braucht eigene Detail-Inventur.**

---

## 4. Kanban-Lead-Aufgaben (kanban_lead_tasks — Lead-Stage-bewusst)

**(a) Zweck.** Zweites Aufgaben-System, an Lead-Stufe (lead_stage / lead_sub_stage) und Gewerk (`lead_product_lists`) gekoppelt.
Aufgaben pro Kunden-/Gewerk-Kanban-Karte, manuell oder aus Vorlage; mit Foto-Pflicht, Terminplanung und Mitarbeiter-Rollen.

**(b) Controller / Routen.** `Customer\Kanban\KanbanLeadTaskController` (922) unter `admin/kanban/tasks/*` (context, summaries, storeManual, storeFromTemplate, updateStatus, destroy). Ergänzend `Customer\Kanban\KanbanPersonalTaskPanelController` (Panel, Kommentare, Key-Toggle) und `kanban_filter_settings`.

**(c) Kern-Tabellen.** `kanban_lead_tasks` (lead_product_list_id, customer_id, alternative_id, product_id, lead_stage_id, lead_sub_stage_id, task_phase_id, phase_activity_id, is_manual, is_scheduled, photo_required, status, *_employee_id, meta) + `kanban_lead_task_employees` (role, status, assigned/done). `kanban_filter_settings`.

**(d) Größe.** MITTEL (1 Hauptcontroller ~922 Z., 2 Tabellen).

**(e) Status.** NEU (06/2026), aktiv. Verbindet klassische Phasen-Vorlagen mit Lead-Stufen — konkurriert/überlappt mit Phasen- und Planner-System. **Braucht eigene Detail-Inventur.**

---

## 5. Persönliche Aufgaben (personal_tasks — private/Team-To-Dos)

**(a) Zweck.** Persönliche und Team-Aufgaben-Verwaltung mit Board-Ansicht, Schritten, Kommentaren, Anhängen, Historie,
Wiederholung/Scheduler, optionaler Kunden-/Gewerk-Kopplung. Größte Alt-Aufgaben-Welt.

**(b) Controller / Routen.** `Task\PersonalTaskController` (**6.570** — sehr groß; `/personal/task/*`, store/update/accept/availability/status), `Task\PersonalTaskBoardController` (3.152, Board), `PersonalTaskStepController`, `PersonalTaskCommentController`, `PersonalTaskAttachmentController` (Routen `/personal_task_*`, `/personal-tasks/*/steps`). API: `/notifications/task/{id}`.

**(c) Kern-Tabellen.** `personal_tasks` (customer/alternative/product optional, task_status, priority, repeat, reminder/start/due, board_column, scheduler-/lead_stage-context-Felder) + `personal_task_keys`, `personal_sub_tasks`, `personal_task_progress`, `personal_task_attachments`, `personal_task_comments`, `personal_task_histories`, `employees_personal_tasks`. Planner-Kopplung via `planner_id`.

**(d) Größe.** SEHR GROSS in Controller-Fläche (2 Controller > 9.700 Z. zusammen), ~8 Tabellen.

**(e) Status.** Etabliert (2024), aktiv weitergepflegt (2025/26: report/archive, histories, scheduler, board_column, lead_stage_context). Eigenes Board neben Kanban-Lead und General-Tasks.

---

## 6. Allgemeine Aufgaben (general_tasks — abteilungsweite Aufgaben-Pinnwand)

**(a) Zweck.** Drittes Aufgaben-System: abteilungs-/firmenweite Aufgaben („Allgemeine Aufgaben", Sidebar), Kanban-Status
(open→in_progress→review→done→archived), Claiming (übernehmen), Schritte, Zeiterfassung (planned_hours_today/hours),
Wiederholung, Abhängigkeiten. Nicht kunden-gebunden.

**(b) Controller / Routen.** `Task\GeneralTaskController` (1.057, `/general-tasks/*`: index, store, move, claim, archive, restore, reports, dependencies, reorder, card), `Task\GeneralTaskStepController` (steps/toggle).

**(c) Kern-Tabellen.** `general_tasks` (title, status, priority, visibility all/department/specific, department_id, created_by, claimed_by, planned_hours_today, recurrence/step_progress-Felder) + `general_task_assignees`, `general_task_reports` (comment/report, hours), `general_task_dependencies`, `general_task_steps` (+`general_task_step_assignees`).

**(d) Größe.** MITTEL (1 Hauptcontroller ~1.057 Z., ~6 Tabellen).

**(e) Status.** NEU (06/2026, sehr „hot" — viele nachgeschobene Migrationen im Juni). Aktiv. Dritte parallele Aufgaben-Welt neben personal_tasks und kanban_lead_tasks.

---

## 7. Tickets / Reklamationen (problems + errors)

**(a) Zweck.** Reklamations-/Störungs-Ticketsystem (Sidebar „Tickets": Neues Ticket, Ticket-Übersicht, Fehlerkatalog).
Ticket = Problem zu Kunde/Objekt/Produkt, mit Ticket-Nr, Fehlercode/-typ, Garantie-Infos, Status-Workflow
(open→progress→close), Priorität, Kanban, Zuweisung, Ticket-Aufgaben, Berichten, Kommentaren, Bildern, Dateien.
Termine zum Ticket → siehe Kalender-Doku (`TicketAppointmentController` / `main_appointments`).

**(b) Controller / Routen.**
- `Ticket\ProblemController` (**2.864** — groß; `/problem_*`, `/tickets/*`, kanban, updateStage/updateStatus, assign, profile, lead-stage-context), `Ticket\ProblemCommentController`.
- Ticket-Aufgaben: `Ticket\TicketTaskController` (685, `ticket-tasks/*`). Berichte: `Ticket\TicketReportController` (+CommentController). Anhänge/Bilder: `Ticket\TicketFileController`, `Ticket\TicketImageController`. Team: `Ticket\TicketEmployeeController`. Termine: `Ticket\TicketAppointmentController` (→ Kalender-Zone).
- Fehlerkatalog: `Ticket\ErrorController` (258, `/error*`).

**(c) Kern-Tabellen.** `problems` (ticket_no, source, error_code/type, customer/alternative/product, start/progress/end_user, warranty_*, status, priority, repeated, softDeletes; + stage-Felder 06/2026) · `errors` + `error_problem` (Fehlerkatalog-Verknüpfung) · `employee_problem` · `problem_comments` · `problem_images` · `ticket_tasks` (ticket_id, employee_id, error_id, appointment_id, status, teams json; + stage-Felder) · `ticket_reports` (+`ticket_report_comments`) · `ticket_images` · `ticket_files`.

**(d) Größe.** GROSS (ProblemController ~2.864 Z. + ~9 Ticket-Controller, ~13 Tabellen).

**(e) Status.** Etabliert (2023 errors / 2024 problems), aktiv erweitert (2025 reports/comments/files/employees; 06/2026 stage-Felder → Lead-Stage-Integration). Ticketize aus Lead-Produkt vorhanden (`lead-product.ticketize`).

---

## 8. Fuhrpark / Maschinen & Fahrzeuge (machines)

**(a) Zweck.** Fuhrpark-/Maschinenverwaltung (Sidebar „Lager → Maschinen & Fahrzeuge"). Fahrzeuge/Maschinen mit
Kauf/Leasing, Wartung/Service, TÜV/technische Inspektion, Ratenkauf (Finanzierung) inkl. Zahlungen und Vertrags-PDF.

**(b) Controller / Routen.** `Inventory\MachineController` (392, `/machine_view`, `_ajax`, `_analytics`, CRUD), `Inventory\MachineServiceController` (309, `/machine_service_*`), `Inventory\MachineInstallmentController` (414, `/machine_installment_*` + payments + contract). Alle unter `auth`-Gruppe (routes/web.php ab ~Z. 2611). E-Fahrzeuge separat: `Product\PV\ElectricVehicleController`.

**(c) Kern-Tabellen.** `machines` (name, model, year, engine_type, purchase/leasing_*, technical_inspection*, branch_id, owner_*) · `machine_services` · `asset_installments` (auch für Maschinen-Raten genutzt) · `electric_vehicles`. 06/2026 „create_or_upgrade"-Migrationen (Schema nachgezogen).

**(d) Größe.** MITTEL (3 Controller ~1.115 Z., ~4 Tabellen).

**(e) Status.** Etabliert (2024), 06/2026 überarbeitet (create_or_upgrade machines/services/installments).

---

## 9. Betriebsmittel / Vermögensbestand (assets + handovers)

**(a) Zweck.** Anlagevermögen/Betriebsmittel-Bestand mit Übergaben an Mitarbeiter (Sidebar „Lager → Betriebsmittel"
= `handover.details.asset`, + „Übergaben"). Assets mit Kauf/Leasing, Kategorie, Standort, Menge, Status, Zuordnung.
Zusätzlich Asset-Sets und Angebots-Asset-Listen. Wird auch vom Planer referenziert (`planner_item_assets`).

**(b) Controller / Routen.** `Inventory\AssetController` (538, Prefix `admin/lager/vermoegensbestand/*`: assets fetch/store/update/destroy, handovers CRUD, `handover.assets.available`), `Inventory\AssetSetController` (145). Übergaben allgemein: `HandoverController` (303). Angebots-Assets: `OfferAssetList`-Model.

**(c) Kern-Tabellen.** `assets` (serial_no, item, model, category, parent_id, purchase/leasing_*, location, quantity, status, handover_id, branch_id, used_for) · `handovers` · `handover_tos` · `assets_handover` · `asset_installments` · `asset_sets` · `offer_asset_lists` · (Verknüpfung) `planner_item_assets`. 2026 responsible-/FK-Anpassungen.

**(d) Größe.** MITTEL (~2 Controller + Handover, ~8 Tabellen).

**(e) Status.** Etabliert (2023), 2025/26 erweitert (asset_sets, offer_asset_lists, responsible/FK-Fixes). Getrennt von „Produkt-Inventar" (`InventoryController` — Lagerware, hier nur am Rand).

---

## 10. Checklisten (checklists — Bauphasen-/Montage-Checklisten)

**(a) Zweck.** Produkt-/Gewerk-spezifische Montage-/Bauphasen-Checklisten (Heatpump, PV, WP, Wartung) als Vorlagen und
kundenbezogene Instanzen inkl. Foto-Alben. Bindeglied Phasen-/Projekt-/Planner-System ↔ Ausführung vor Ort.

**(b) Controller / Routen.** Root-Controller: `CustomerChecklistController`, `CustomerChecklistAlbumController`,
`ChecklistAssembleController`, `ChecklistApartmentController`, `ChecklistRoomController`, `ChecklistEndTaskController`,
`HeatpumpChecklistController`, `BrandMaintenanceChecklistController`, `LeadProductChecklistValueController`.

**(c) Kern-Tabellen.** `checklists` (product_id, article_group, checklist_title, viele Montage-Flags) · `checklist_sets` · `checklist_assembles` · `checklist_end_tasks` · `checklist_apartments` · `checklist_rooms` · `heatpump_checklists` · `p_v_checklistss` / `p_v_long_checklists` · `w_p_checklists` · `customer_checklists` · `customer_checklist_albums` · Wartung: `maintenance_checklists` (+items, brand/distributor, `maintenance_assets`) · `master_set_checklists`, `planner_item_checklists`.

**(d) Größe.** MITTEL–GROSS in Tabellen-Zahl (~15 Checklist-Tabellen), Controller einzeln klein.

**(e) Status.** Etabliert (2024), Wartungs-Checklisten neu (11/2025), Master-Set-/Planner-Checklisten 2026 angebunden.

---

## „Braucht eigene Detail-Inventur"

1. **`Planner\PlannerPlanController` (11.080 Zeilen) + Planner-Ökosystem** — mit Abstand größter Knoten der Zone.
   Nur oberflächlich benannt; Cockpit, Kanban, Montage-Arbeitslisten, DnD-Terminplanung, Material/Master-Sets,
   Attendance, Nuriva-Mobile-API gehören einzeln zerlegt.
2. **Drei parallele Phasen-/Aufgaben-Systeme + Doppelungen klären** —
   (a) klassisch `phase_sections→task_phases→phase_activities→task_sub_tasks` (task_sub_tasks DEPRECATED),
   (b) `kanban_lead_tasks` (Lead-Stage-bewusst, neu),
   (c) `planner_plans→planner_items` (neu, konsumiert a+b als source_type).
   Plus **drei Aufgaben-Welten** `personal_tasks` / `general_tasks` / `kanban_lead_tasks` und das separate
   **`projects`-Universum (14 Tabellen)** neben `planner_plans`. Überlappungen/Migrationspfad sind offen.
3. **`Task\PersonalTaskController` (6.570) + `PersonalTaskBoardController` (3.152)** und **`Ticket\ProblemController` (2.864)** —
   je eigener Detail-Durchgang wegen Größe.
4. **projects ↔ planner_plans ↔ lead_product_lists (Gewerk)** — welche Tabelle ist führend? Für Zielbild
   objekt-zentriertes CRM entscheidend, hier nur als offene Weiche vermerkt (nicht bewerten).

---

## Belege (Dateipfade & Zeilen, alle absolut unter /Users/yamanuri/Documents/ticket)

**Controller (Zeilenzahl):**
- `app/Http/Controllers/Planner/PlannerPlanController.php` — 11.080
- `app/Http/Controllers/Planner/PlannerEmployeeApiController.php` — 2.214 · `PlannerApiAuthController.php` — 930 · `PlannerAttendanceController.php` — 652 · `PlannerItemMaterialController.php` — 610 · `PlannerMobileCustomerImageController.php` — 297 · `PlannerMasterSetController.php` — 229 · `PlannerItemStateController.php` — 214
- `app/Http/Controllers/Api/MobilePlannerApiController.php` — 213
- `app/Http/Controllers/Task/PersonalTaskController.php` — 6.570 · `PersonalTaskBoardController.php` — 3.152 · `GeneralTaskController.php` — 1.057
- `app/Http/Controllers/Ticket/ProblemController.php` — 2.864 · `TicketTaskController.php` — 685 · `ErrorController.php` — 258
- `app/Http/Controllers/Phase/TaskPhaseController.php` — 1.697 · `LeadTaskPhaseManagementController.php` — 1.104 · `LeadStageAdminController.php` — 538 · `PhaseActivitiesController.php` — 365 · `PhaseSectionController.php` — 179 · `TaskSubTaskController.php` — 143 (DEPRECATED)
- `app/Http/Controllers/Customer/Kanban/KanbanLeadTaskController.php` — 922
- `app/Http/Controllers/Inventory/AssetController.php` — 538 · `MachineInstallmentController.php` — 414 · `MachineController.php` — 392 · `MachineServiceController.php` — 309 · `AssetSetController.php` — 145
- `app/Http/Controllers/HandoverController.php` — 303 · `app/Http/Controllers/Project/AddEmployeeToProjectController.php`

**Routen:** `routes/web.php` (5.434 Z. gesamt) — Planner-Prefix ab Z. 5149; Ticket/Problem ab Z. 1920 & 1971; Fehlerkatalog Z. 2110; Assets `admin/lager/vermoegensbestand` ab Z. 2594; Maschinen ab Z. 2611; general-tasks ab Z. 3783; personal-task ab Z. 3645; Phase-System ab Z. 2913 & 3006; kanban-tasks ab Z. 970. · `routes/api.php` — Planner/Nuriva-API ab Z. 224.

**Sidebar:** `resources/views/admin/layouts/sidebar.blade.php` — „Projektplanung" (`planner.index`), „Meine Aufgaben"/„Allgemeine Aufgaben", „Tickets" (Neues Ticket/Übersicht/Fehlerkatalog), „Lager" (Inventar/Betriebsmittel/Übergaben/Maschinen & Fahrzeuge).

**Migrationen (Schlüssel):** `database/migrations/` — `2024_10_23_101624_create_projects_table.php` (+13 project_*), `2026_01_21_082633_create_planner_plans_table.php` / `..._083334_create_planner_items_table.php` (+~23 planner_*), `2023_08_31_060955_create_phase_sections_table.php` / `..._task_phases` / `..._phase_activities` / `..._task_sub_tasks`, `2024_10_23_101625_create_task_to_dos_table.php`, `2026_06_05_222249_create_kanban_lead_tasks_tables.php`, `2024_12_17_092051_create_personal_tasks_table.php` (+personal_*), `2026_06_03_000000_create_general_tasks_tables.php` (+general_task_*), `2024_12_13_110732_create_problems_table.php`, `2023_08_09_055656_create_errors_table.php`, `2025_01_29_120753_create_ticket_tasks_table.php`, `2023_08_30_084628_create_assets_table.php` (+handovers/asset_sets/asset_installments), `2024_04_01_010402_create_machines_table.php` (+machine_services), `2024_09_23_125725_create_checklists_table.php` (+~14 checklist/maintenance).
