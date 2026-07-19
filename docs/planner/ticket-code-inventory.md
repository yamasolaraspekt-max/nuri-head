# Ticket-Code-Inventar (Planner-Reuse)

Gespiegelt aus `.claude/skills/ticket-code-reuse/references/ticket-component-inventory.md`.
**Verifiziert** = in dieser Sitzung real gesehen. **Zu verifizieren** = Phase 2 real suchen.
Keine erfundenen Pfade.

## VERIFIZIERT
- Designsystem/CI: `resources/views/admin/styleguide/index.blade.php`; Tokens `--sa-accent #93c21c`,
  `--sa-accent-hover #7baa18`, `--sa-accent-light #f4fae7`, `--sa-accent-ink #fff`,
  `--sa-danger #ef4444`, `--sa-warning #f59e0b`, `--sa-success #10b981`, `--sa-info #6b7280`; Font Inter.
- Seitenkopf: `<x-page-head>` + `<x-slot:actions>` (z. B. `resources/views/admin/objekte/akte.blade.php`).
- Sidebar: `resources/views/admin/layouts/sidebar.blade.php`.
- Bearbeitungssperre: `resources/views/admin/layouts/partials/bearbeitungs-sperre.blade.php`
  (Routen `system.sperre.ping|leave`).
- Rechte: `app/Models/User.php` (`hasPermission`, `is_admin`), `permission:Item,action`,
  `app/Http/Controllers/User/UserRollController.php::permissionModules()`.
- Objektanker: Model `LeadAlternativeAdd` (`alternative_id`).
- Uploads/Dokumente: `app/Http/Controllers/Energie/PlanUploadController.php`, `app/Models/PlanUpload.php`,
  `app/Models/BuildingModelVersion.php`.
- Hausplaner-Domain: `app/Http/Controllers/Hausplaner/HausplanerController.php`,
  `app/Domain/Hausplaner/Models/*`, `app/Domain/Hausplaner/Actions/*` (Revision/Checksum/409),
  `resources/planner/hausplaner/*`, `resources/views/admin/hausplaner/*`,
  Migration `database/migrations/2026_07_16_211128_create_hausplaner_foundation_tables.php`,
  Build `vite.hausplaner.config.ts` + Scripts `build|test|tsc:hausplaner`.
- Energie/Fach: Routen `energie.grundriss|plan-upload|wr-auslegung|wp-auslegung|sanierung|
  energiekonzept|heizlast`; `app/Services/Heizlast/GeometrieAbleitungService.php`;
  `app/Domain/Hausplaner/Actions/UebernehmeSzeneInAuslegung.php`.

## ZU VERIFIZIEREN (Phase 2, nicht annehmen)
Aufgaben/Tasks · Kommentare/Erwähnungen/Beobachter · Aktivitäts-/Audit-/Historienlogik ·
Benachrichtigungen (Notifications/Events/Jobs) · allgemeine Status-/Workflow-/Freigabemodelle ·
generische Tabellen-/Filter-/Such-/Modal-/Leerzustand-Komponenten · Factory-/Org-/Projekttestmuster.

---

## VERIFIZIERT — Sitzung 2 (per Shell, echte Pfade)

### UI / Designsystem (wiederverwenden statt neu)
- CI-Tokens: `resources/views/admin/layouts/partials/sa-ui.blade.php` (`:root --sa-accent #93c21c`,
  `--sa-accent-hover #7baa18`, `--sa-accent-light #f4fae7`, `--sa-danger/warning/success/info`).
  Eingebunden in `resources/views/admin/layouts/app.blade.php`, `styleguide/index.blade.php`,
  `master_sets/editor.blade.php`.
- Seitenkopf: `resources/views/components/page-head.blade.php` (`<x-page-head title sub current>`
  + `<x-slot:actions>`, Titel 26px/800 GROSS, Breadcrumb, `.sa-ph-*`-Klassen).
- Shell/Layout: `resources/views/admin/layouts/app.blade.php`; Sidebar `.../sidebar.blade.php`;
  Aktivitäts-/Notification-Layouts `activity.blade.php`, `notification.blade.php`.
- Wiederverwendbare Partials: Modale/Tabellen/Pagination unter
  `resources/views/admin/general_tasks/partials/*`, `.../product/*/partials/{table,pagination,modals}`,
  `.../todo/*/partials/*`; `partials/bearbeitungs-sperre.blade.php`, `partials/zuletzt-besucht.blade.php`.

### Kommentare (8 Modelle — R4 Adapter, nicht neu)
`AppointmentComment`, `CustomerReportComment`, `InquiryComment`, `OfferComment`,
`PersonalTaskComment`, `ProblemComment`, `ProjectTaskComment`, `TicketReportComment`
(+ Controller, z. B. `app/Http/Controllers/Task/PersonalTaskCommentController.php`).

### Aufgaben/Tasks (großes Bestandssystem — R4, nicht neu)
`PersonalTask(+SubTask/Comment/Attachment/History/Progress)`, `ProjectTask(+Comment/Attachment)`,
`GeneralTask(+Report/Step)`, `KanbanLeadTask`, `TaskPhase`, `TaskSubTask`, `TaskLabel`, `TaskTags`,
`TaskToDo`, `Reminder`/`LeadReminder`/`ReminderEvent`; Controller unter `app/Http/Controllers/Task/*`,
`.../Phase/*`.

### Aktivität / Historie / Audit (R4, nicht neu)
`CustomerActivity`, `CustomerHistory`, `LeadActivityLogs`, `ProjectTimeline(+DoneDate)`,
`PhaseActivities`, `DashboardLiveActivity`, diverse `*History`; Trait `app/Traits/AuditableLead.php`.

### Benachrichtigungen (R4, nicht neu)
`app/Notifications/*` (`LeadNotification`, `OfferNotification`, `PersonalTaskNotification`,
`AppointmentNotification`, …); Events `app/Events/*` (`ChatMentionCreated`,
`DashboardLiveActivityCreated`, `CustomerNoteChanged`); Jobs `app/Jobs/*` (`PlanKlassifizieren`
— Plan-Klassifizierung, relevant für Grundriss-Import).

### Dokumente / Import (R2/R4)
`app/Models/PlanUpload.php`, `app/Http/Controllers/Energie/PlanUploadController.php`,
`app/Jobs/PlanKlassifizieren.php`.

### Tests (Muster wiederverwenden)
114 Tests; strukturiert `tests/Unit/{Energie,Offer,Form,Auslegung,BuildingModel,Geometrie,
Heizlast,Heizkoerper}`; Factories `database/factories/{User,Anforderungsprofil,…}Factory.php`.
Für Planner-Geometrie/BuildingModel gibt es bereits Test-Nachbarschaft (`Unit/Geometrie`, `Unit/BuildingModel`).
