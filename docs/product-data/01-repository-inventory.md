# 01 · Repository-Inventur

<!-- Erzeugt in der Analysephase (Phase A). Rein lesende Untersuchung. -->

> **Rolle:** Planner · **Modus:** restriktiv lesend · **Stand:** 2026-07-30
> **Heimat-App:** `ticket` (Laravel 11.44, PHP 8.2, MySQL, DB `ticket`)
> **Auftrag:** Masterprompt „Produktdatenplattform, IDS Connect, Open Masterdata" — Phase A
>
> **Grenzen dieser Untersuchung (belegt):** Es wurde ausschliesslich gelesen. Keine Migration, kein
> Schreibvorgang, kein Datenbankzugriff. In der Analyse-Umgebung stehen weder `php` noch ein
> `mysql`-Client zur Verfuegung (`command -v php` und `command -v mysql` → nicht gefunden), und die
> MySQL-Instanz auf `127.0.0.1:3307` liegt auf dem Rechner des Auftraggebers und ist von hier aus
> nicht erreichbar. **Alle Aussagen stammen daher aus Migrationen, Models, Controllern, Services,
> Routen, Konfiguration und Repository-Dokumentation — nicht aus dem laufenden Datenbestand.**
> Zeilenzahlen aus dem Datenbestand sind gesondert in `03-data-quality-report.md` behandelt.
>
> **Legende der Aussagearten** — durchgaengig getrennt:
> · **BELEGT** — nachweisbar, mit Fundstelle (Datei:Zeile)
> · **BEWERTUNG** — fachliche Einschaetzung des Planners
> · **ANNAHME** — ausdruecklich als solche gekennzeichnet, nicht belegt
> · **OFFEN** — nicht geklaert; bewusst offen gelassen statt geraten
>
> **Pfad-Praefix aller Fundstellen:** Repository-Wurzel von `ticket`.

---

Basis: `/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket`
Git-Stand beim Lesen: Branch `auto/hausplaner-integration`, HEAD `4c2fe123`, 26 Commits ahead, Arbeitsbaum nicht sauber (3 modifizierte Dateien, 1 untracked Ordner `tests/Feature/Styleguide/`) — belegt via `git --no-optional-locks status -sb`.

---

## 0 · Kennzahlen (nachgemessen)

| Kennzahl | Vorgabe | gemessen | Befehl/Beleg |
|---|---|---|---|
| Migrationen | 612 | **612** | `ls database/migrations/*.php \| wc -l` |
| Models | 410 | **410** (`app/Models/*.php`, +4 in `app/Domain/Hausplaner/Models/`) | `ls app/Models/*.php \| wc -l` |
| Controller | 406 | **406** | `find app/Http/Controllers -name '*.php' \| wc -l` |
| Services | 112 | **114** (14 top-level + 100 in 20 Unterordnern) | `find app/Services -name '*.php'` |
| Jobs | 10 | **10** | `ls app/Jobs` |
| Commands | 15 | **14** Dateien in `app/Console/Commands` + 1 Closure `inspire` in `routes/console.php:18` = 15 | s. §4 |
| Events | 24 | **24** | `ls app/Events` |
| Tests | 128 | **128** (93 Feature / 35 Unit) | `find tests -name '*Test.php'` |
| Web-Routen | 2617 | **2382 Route-Definitions-Aufrufe** in `routes/web.php` (5700 Zeilen), davon 4 `Route::resource` | `grep -coE "Route::(get\|post\|...)\(" routes/web.php` |
| API-Routen | 52 | **44 Definitionen** in `routes/api.php` (515 Zeilen) | `grep -coE "Route::(get\|post\|...)\(" routes/api.php` |
| Blade-Views | 805 | **805** | `find resources/views -name '*.blade.php' \| wc -l` |

**BEWERTUNG:** Die Differenz bei Routen (2617 vs. 2382 / 52 vs. 44) erklärt sich plausibel durch `route:list`-Zählung (Resource-Routen expandieren zu je 7 Einträgen, Broadcast-/Sanctum-/Ignition-Routen kommen hinzu). **ANNAHME** — nicht verifizierbar, weil `php artisan route:list` eine Ausführung wäre; ich habe nur gelesen.

---

## 1 · Architekturstil

### BELEGT

`app/` Unterordner mit Dateizahl (direkte `*.php`, ohne Unterordner):

| Ordner | Dateien | Unterordner |
|---|---|---|
| `app/Models` | **410** | nur `Traits/` (1 Datei: `HasChatRelations.php`) |
| `app/Http/Controllers` | 78 direkt / **406 gesamt** | 56 Unterordner |
| `app/Services` | 14 direkt / **114 gesamt** | 20 Unterordner |
| `app/Events` | 24 | — |
| `app/Http/Middleware` | 20 | — |
| `app/Notifications` | 18 | — |
| `app/Console/Commands` | 14 | — |
| `app/Jobs` | 10 | — |
| `app/Policies` | 5 | — |
| `app/Providers` | 5 | — |
| `app/Listeners` | 3 | — |
| `app/Mail` | 2 (+3 in `passwords/`) | — |
| `app/Support` | 3 | — |
| `app/Exceptions` | 3 | — |
| `app/Repositories` | 1 (`CatalogDeviceRepository.php`) | — |
| `app/Traits` | 1 (`AuditableLead.php`) | — |
| `app/Enums` | 1 (`KonstruktionTyp.php`) | — |
| `app/Domain` | 0 direkt | `Domain/Hausplaner/{Models,Actions,Validation}` = **10 Dateien** |
| `app/Http/Requests` | **0** | — |

- Ein einziger Namespace `App\` → `app/` (`composer.json:35-40`). Kein Modul-Autoloading, keine Package-Struktur, kein `modules/`.
- Routing: **nur zwei Route-Dateien**, `routes/web.php` (369 KB, 5700 Zeilen) und `routes/api.php`; registriert in `app/Providers/RouteServiceProvider.php:31-39`. Keine modulweise Route-Registrierung.
- Legacy-Layer im Code: `app/Http/Controllers/Old/` mit **37 Controllern**, die in `routes/web.php` **nicht referenziert** sind (`grep -c "Controllers\\\\Old" routes/web.php` = 0).
- Einziger echter Domänen-Schnitt: `app/Domain/Hausplaner/` mit Models/Actions/Validation (z. B. `app/Domain/Hausplaner/Actions/UebernehmeSzeneInAuslegung.php`, `.../Validation/SceneDocumentValidator.php`).
- Bootstrap ist **Laravel-10-Stil**, nicht 11-Stil: `bootstrap/app.php:31-42` bindet `App\Http\Kernel`, `App\Console\Kernel`, `App\Exceptions\Handler` — kein `Application::configure()`. `app/Http/Kernel.php` und `app/Console/Kernel.php` existieren und tragen die Middleware-Aliase bzw. den Scheduler.
- Repo-Hygiene: Ordner `_to_delete/` mit **124 Einträgen** (überwiegend `HEAD.lock.*`-Reste), `_archiv/`, sowie im Repo-Root liegende Fremdkörper: `report.blade.php` (43 KB), `page.html`, `longi_54htb_440_scientist_black.pdf` (14 MB), `datetime,` (0 Byte).

### BEWERTUNG
Klassischer **Laravel-Monolith ohne Modulgrenzen**. Die Ordnung entsteht ausschließlich über Controller-Unterordner; die Modell-Schicht ist mit 410 Klassen in **einem flachen Verzeichnis** die schwerste Stelle — dort gibt es keinerlei Domänen-Schnitt. `app/Services/*` ist der einzige Bereich mit erkennbarer Fachgliederung (Heizlast, Auslegung, Energie, Geometrie, Accounting, Suppliers, Offer, Form). `app/Domain/Hausplaner` ist ein neuerer, sauberer Ansatz (Actions + Validator + eigene Models) — er ist der Ausreißer nach oben, aber Einzelfall.
Die 5700-Zeilen-`web.php` ist der größte strukturelle Hebel: sie ist gleichzeitig Navigationsverzeichnis, Rechtekonzept und Prozesslandkarte. Merge-Konflikte und Rechte-Lücken entstehen zwangsläufig dort.
`app/Http/Requests` ist leer → Validierung liegt vollständig in Controllern. Bei 406 Controllern ist das ein systematisches Duplikationsrisiko.

### OFFEN
Ob `app/Http/Controllers/Old/` tot ist oder über `Route::resource`/String-Referenzen erreichbar bleibt, konnte ich nicht abschließend klären — ich habe nur auf `Old\`-Namespace gegrept, nicht auf jede Klassenkurzform.

---

## 2 · Mandantenfähigkeit

### BELEGT — eindeutig **widerlegt**

| Prüfung | Ergebnis | Beleg |
|---|---|---|
| `company_id`/`tenant_id` in `app/Models/` | **0 Treffer** | `grep -rl "company_id\|tenant_id" app/Models/ \| wc -l` = 0 |
| `company_id`/`tenant_id` in `database/migrations/` | **2 Dateien — beides Kommentare, die das Fehlen begründen** | s. u. |
| Global Scopes für Mandanten | **keiner**; einziger `addGlobalScope` ist SoftDeleting | `app/Models/OfferFolderActivity.php:49` |

Wörtliche Belege:
- `database/migrations/2026_07_16_211128_create_hausplaner_foundation_tables.php:15` — „…**Kein tenant_id** (spätere additive Nachrüstung möglich)."
- `database/migrations/2026_07_26_180000_create_hausplaner_configurator_packages_table.php:19` — „**Kein `tenant_id`** — die Bestandstabellen haben auch keines…"

Was es **stattdessen** gibt (organisatorisch, nicht mandantentrennend):
- `branches` (Filialen), `database/migrations/2023_06_13_100801_create_branches_table.php`; `branch_id` in **16 Models** und **20 Migrationen**. Firmenprofilfelder ergänzt in `2026_04_14_080216_add_company_profile_fields_to_branches_table.php`.
- **FiBu-Mandanten** ausschließlich im Accounting-Subsystem: `accounting_clients` mit `datev_berater_nr`/`datev_mandant_nr` (`database/migrations/2026_07_05_180001_create_accounting_foundation_tables.php:34-50`), `accounting_client_id` als FK auf `accounts` (Z. 56) und `tax_codes` (Z. 73).

### BEWERTUNG
Das System ist **Single-Tenant**. `accounting_clients` ist kein Mandantenkonzept der Anwendung, sondern ein DATEV-Buchungskreis innerhalb der Buchhaltung — es scopet keine Kunden, Angebote, Produkte oder Aufträge. `branch_id` ist eine Organisationsdimension, kein Sicherheitsgrenzobjekt (kein Global Scope, keine Middleware). Eine spätere Mandantenfähigkeit wäre ein Eingriff über 612 Migrationen und 410 Models und ist **kein additiver Schritt** — die Migrations-Kommentare sind hier ehrlicher als optimistisch.

---

## 3 · Prozess-Landschaft

### BELEGT — abgeleitet aus Controller-Ordnern (`find app/Http/Controllers -type d`) und Route-Namensräumen (`routes/web.php`)

Häufigste Route-Namenspräfixe (Top): `admin` 68 · `deal` 67 · `product` 62 · `employee` 54 · `lead` 53 · `inquiry` 42 · `personal` 40 · `branch` 38 · `plans` 35 · `machine` 31 · `task` 30 · `kanban` 29 · `ticket` 27 · `energie` 27 · `invoices` 23 · `department` 23.

| Prozess | Leit-Controller (Pfad unter `app/Http/Controllers/`) | Umfang |
|---|---|---|
| **Vertrieb / Lead / CRM** | `Customer/NewLeadsController.php`, `Customer/MassManagerController.php`, `Customer/ObjektakteController.php`, `Customer/CustomerHistoryController.php`, `LeadImportController.php` | Ordner `Customer/` 19 Dateien |
| **Kanban / Vertriebsphasen** | `Customer/Kanban/` (8), `Phase/LeadStageAdminController.php`, `Phase/TaskPhaseController.php`, `Phase/PhaseSectionController.php`, `CustomerPhaseStageController.php`, `CustomProcessStageController.php` | Phase 7 + Kanban 8 |
| **Angebot / Kalkulation** | `Customer/Offer/OfferController.php`, `OffersController.php`, `OfferFolderController.php`, `OfferWizardController.php`, `OfferTemplateController.php`, `WpAngebotsWorkflowController.php`, `WpAngebotsreifeController.php`, `WpKatalogMatchingController.php`, `AuslegungVorschlagController.php`, `KonfigurationsprojektController.php` | **26 Dateien** — größter Einzelbereich |
| **Auslegung / Energie / Heizlast** | `Energie/HeizlastController.php`, `Energie/EnergieAuslegungController.php`, `Energie/EnergiekonzeptController.php`, `Energie/GrundrissController.php`, `Energie/SanierungController.php`, `Energie/MateriallisteController.php`, `Energie/PlanUploadController.php`, `Energie/FussbodenCheckController.php`, `Heizkoerper/`, `Hausplaner/` | 8 + 1 + 1; Services: `Heizlast/` 21, `Auslegung/` 5, `Energie/` 4, `Geometrie/` 4, `BuildingModel/` 8 |
| **Auftrag / Aufmaß / Deal** | `Customer/Deal/` (7, u. a. `DealMeasurementController`, `DealMeasurementMaterialController`), `DealNoteController.php` | 7 |
| **Projekt / Planung (Bauplaner)** | `Planner/PlannerPlanController.php`, `PlannerItemStateController.php`, `PlannerItemMaterialController.php`, `PlannerMasterSetController.php`, `Project/AddEmployeeToProjectController.php`, `PlaningController.php` | 8 + 1 |
| **Lager / Inventar / Assets** | `Inventory/InventoryController.php`, `InventoryRequestOutController.php`, `MaterialentnahmenController.php`, `AssetController.php`, `AssetSetController.php`, `MachineController.php`, `MachineServiceController.php`, `Inventory/DeliveryNotes/` (3) | 10 + 3 |
| **Beschaffung / Lieferanten / Kataloge** | `Product/IDS/SupplierConnectionController.php`, `Product/IDS/gconline/` (2), `IdsController.php`, `ImportedIdsItemController.php`, `Product/PurchaseRequestController.php`, `DatanormController.php`, `Inquiry/` (6), `Product/Distributor/` (3) | Services `Suppliers/` 8 |
| **Artikel / Produkte / Preise** | `Product/ProductController.php`, `ProductWPController.php`, `ProductPVController.php`, `DiscountGroupController.php`, `ProductFormulaController.php`, `Product/MasterSet/` (6), `Product/Brand/` (4), `ArticleGroup/` (2), `CostingSetController.php` | 20 + 13 |
| **Rechnung / Forderungen / FiBu** | `Invoice/InvoiceController.php`, `AusgangsrechnungenController.php`, `GutschriftenController.php`, `OffenePostenController.php`, `MahnwesenController.php`, `InvoiceCanvasController.php`, `InstallmentPaymentController.php` | 6 + Services `Accounting/` 5 |
| **Ticket / Störung / Service** | `Ticket/ProblemController.php`, `ErrorController.php`, `TicketTaskController.php`, `TicketAppointmentController.php`, `TicketReportController.php` | 9 |
| **Aufgaben / Arbeitsliste** | `Task/PersonalTaskController.php`, `GeneralTaskController.php`, `PersonalTaskBoardController.php`, `ArbeitslisteController.php`, `FollowUpController.php` | 7 + 2 |
| **HR / Zeit / Organisation** | `Employee/Profile/` (24), `Employee/Position/` (5), `Employee/Department/` (3), `Employee/TimeManagement/`, `AttendanceController.php`, `HandoverController.php`, `Branch*Controller` (6) | ~45 |
| **Dashboard / Controlling** | `Dashboard/` (9), `Controlling/` (1), `Report/` (5), `SidebarCountController` | 15 |
| **Kommunikation** | `Chat/` (5+2), `Email/` (5), `Notification/` (2), `MessageController.php`, `VideoCallController.php`, `BreakingNews/` | ~17 |
| **KI / Assistenz** | `Ai/` (3) + Services `OllamaClient.php`, `EmbeddingClient.php`, `ConversationMemory.php`, `PromptFactory.php` | 3 + 4 |
| **Integrationen** | `Wordpress/` (2), `FusionWebhookController.php`, `Customer/Moser/` (2), `BitrixController.php`, `BitrixChatController.php` | 7 |

### BEWERTUNG
Der fachliche Schwerpunkt liegt eindeutig auf **Angebot + Auslegung (Wärmepumpe/PV)** — dort steckt die meiste jüngste Arbeit (26 Offer-Controller, 21 Heizlast-Services, eigene Domain-Schicht Hausplaner, 60 % der Tests). Der klassische kaufmännische Rückwärtsprozess (Auftrag → Lieferschein → Rechnung → Mahnwesen → FiBu) ist vorhanden, aber deutlich dünner besetzt und jünger (Accounting-Migrationen ab 2026-07). **Beschaffung** existiert real nur als IDS/OMD-Anbindung; DATANORM ist ein Stub (s. §10). Auffällig: `Project/` hat genau **einen** Controller — „Projekt" wird faktisch über `Planner/` und `Customer/Deal/` abgebildet, nicht als eigene Entität.

---

## 4 · Hintergrundverarbeitung

### BELEGT — Jobs (`app/Jobs/`, alle `implements ShouldQueue`)

| Job | Zweck (aus Klassenkopf/`handle()`) |
|---|---|
| `EmbedMessage.php:9,21` | erzeugt Vektor-Embedding einer Chat-Nachricht via `EmbeddingClient` |
| `FusionFormEntryJob.php:8,23` | verarbeitet einen Fusion-Formular-Eingang |
| `ImportClimateWorkbookJob.php:22,35` | liest hochgeladene **.xlsx**-Klimadaten-Mappe (ZipArchive, `:579-642`) ein |
| `PlanKlassifizieren.php:25,31` | klassifiziert hochgeladenen Plan über `ImportServiceClient` (Python-Dienst) |
| `ProcessChatChunk.php:13,26` | verarbeitet einen Chat-Textabschnitt (RAG-Chunking) |
| `ProcessChatData.php:14,33` | verarbeitet Chat-Daten (Sammel-Pipeline) |
| `ProcessFusionEntry.php:12,23` | verarbeitet einen einzelnen Fusion-Eintrag weiter |
| `ProcessWeatherData.php:13,28` | holt/verarbeitet Wetterdaten |
| `ScheduleTaskReminder.php:14,25` | plant/verschickt Aufgaben-Erinnerung |
| `UpdateMemory.php:10,27` | schreibt Konversationsgedächtnis via `ConversationMemory` fort |

### BELEGT — Scheduler

`app/Console/Kernel.php:22-33` (5 aktiv, 3 auskommentiert):

| Eintrag | Takt | Zeile |
|---|---|---|
| `chat:sync-solar-news` | `everyFifteenMinutes` | 26 |
| `breaking-news:deactivate-expired` | `everyMinute` | 27 |
| `lead-emails:sync` | `everyMinute()->withoutOverlapping()` | 28 |
| `appointments:dispatch-reminders` | `everyMinute` | 29 |
| `personal-tasks:process-scheduler` | `everyMinute()->withoutOverlapping()` | 30-32 |
| *(auskommentiert)* `inspire`, `leaves:update-status`, `job_representatives:update-status` | — | 23-25 |

**Doppelregistrierung:** `personal-tasks:process-scheduler` steht zusätzlich in `routes/console.php:22-24` — und `routes/console.php` wird von `app/Console/Kernel.php:42` geladen. Beide Einträge tragen `withoutOverlapping()`, laufen also nicht gleichzeitig, sind aber redundant.

### BELEGT — Artisan-Commands (Signatur / Zweck)

| Signatur | Zweck | Datei:Zeile |
|---|---|---|
| `deal-measurements:backfill-owner {--dry-run}` | füllt fehlende `created_by` der Aufmaße aus `deals.employee_id` | `BackfillDealMeasurementOwner.php:17,19` |
| `leads:backfill-lead-stage-id {--rollback}` | `lead_product_lists.lead_stage_id` aus `status` backfüllen | `BackfillLeadStageId.php:25,27` |
| `backfill:phase-sections` | Default-Zeilen für `phase_sections` je Artikelgruppe | `BackfillPhaseSections.php:17,24` |
| `breaking-news:deactivate-expired` | abgelaufene Breaking News deaktivieren | `DeactivateExpiredBreakingNews.php:10,11` |
| `appointments:dispatch-reminders {--debug}` | Realtime-Erinnerung 10 min vor Haupttermin | `DispatchMainAppointmentReminders.php:13,15` |
| `followup:dedupe-tasks {--dry-run}` | Dubletten auf `personal_tasks` je (type, source_type, source_id) bereinigen | `FollowUpDedupeTasks.php:24,26` |
| `personal-tasks:process-scheduler` | Erinnerungen + Wiederholaufgaben abarbeiten | `ProcessPersonalTaskScheduler.php:16,18` |
| `garbage:soft-deleted` | soft-deleted Zeilen aller Tabellen mit `deleted_at` endgültig löschen | `PurgeSoftDeletedGarbage.php:11,17` |
| `spec:import {datei}` | Geräte-Specs (JSON/CSV) importieren, **Default DRY-RUN** | `SpecImportCommand.php:15,22` |
| `spec:rollback {batch_id}` | Rückbau eines `spec:import`-Batches (nur reine Insert-Läufe) | `SpecRollbackCommand.php:14,16` |
| `lead-emails:sync` | neue Mails aus aktiven Lead-Mailkonten holen | `SyncLeadEmails.php:15,16` |
| `chat:sync-solar-news` | Solar-News aus NewsAPI in Chat-Gruppe pushen | `SyncSolarNewsToChat.php:10,11` |
| `job_representatives:update-status` | Status abgelaufener Vertretungen aktualisieren | `UpdateJobRepresentativeStatus.php:12,13` |
| `leaves:update-status` | Urlaubsstatus nach Enddatum aktualisieren | `UpdateLeaveStatus.php:11,12` |
| `inspire` (Closure) | Zitat ausgeben | `routes/console.php:18-20` |

### BEWERTUNG
Die Job-Landschaft ist **klein und schief verteilt**: 6 von 10 Jobs bedienen Chat/KI/Wetter, kein einziger bedient die Kernprozesse Angebot, Rechnung, Lager oder Beschaffung. Rechnungslauf, Mahnlauf und Lieferanten-Import laufen offensichtlich synchron im Request. Bei `QUEUE_CONNECTION=database` und `everyMinute`-Scheduler ist das ein Skalierungsdeckel. `garbage:soft-deleted` ist ein scharfes Löschwerkzeug ohne erkennbares Dry-Run-Flag in der Signatur (mehrzeilige Signatur ab `:11` — Optionen nicht vollständig gelesen).

### OFFEN
Ob ein Queue-Worker/Supervisor produktiv läuft, ist aus dem Repo nicht belegbar (nur `laravel-reverb.service` im Root gefunden, kein Worker-Unit). `docker/` habe ich nicht geöffnet.

---

## 5 · Events / Listener / Broadcasting

### BELEGT — 24 Events, 23 davon `ShouldBroadcast`

| Event | Kanal (aus `broadcastOn()`) |
|---|---|
| `ChatMentionCreated` | private `chat.user.{id}` |
| `ClipboardUpdated` | private `user.clipboard.{id}` |
| `CustomerNoteChanged` | private `customer-notes.{cust}.{alt}.{list}` (+`.stage.*`/`.sub-stage.*`), `:24-36` |
| `DashboardEmployeeStatusUpdated` | public `dashboard.employee-status` |
| `DashboardLiveActivityCreated` | private `employee.{id}` — `:24` |
| `EmployeeLocationUpdated` | public `planner.plan.{id}`, `planner.employee.{id}` |
| `GeneralTaskChanged` | public `general-tasks` |
| `GroupMembershipUpdated` | private `chat.user.{id}` |
| `GroupMessageRead` | private `chat.group.{id}` |
| `IdsItemsImported` | public `ids` |
| `LeadActivityBroadcast` | private `company-activities` |
| `LeadEmailReceived` | private `lead-emails` |
| `LeadRecordChanged` | **kein Broadcast** — reines Domain-Event |
| `LeadSidebarCountsUpdated` | public `lead-sidebar-counts` |
| `MainAppointmentReminderDue` | private `employee-appointment.{id}` — `:27` |
| `MessageRead` | private `chat.user.{senderId}` |
| `MessageSent` | private `chat.user.{id}`, `chat.group.{id}`, `chat.{min}.{max}` |
| `OfferSupplierProductsReturned` | private `offer-folder.{id}` |
| `OverdueReportCreated` | public `overdue-reports` |
| `PersonalTaskReminderTriggered` | private `employee.{id}.tasks` — `:25` |
| `PlannerRealtimeEvent` | private, Kanäle dynamisch aus `$this->channels`, `:25-30` |
| `SolarNewsPushed` | public `solar.news` |
| `SystemWarningUpdated` | public `system-warning` |
| `TestNotificationEvent` | public `notifications` |

Registrierte Kanäle in `routes/channels.php` (15): `chat.{a}.{b}` (:79), `chat.user.{id}` (:84), `chat.group.{id}` (:89), `online` (:96, Presence), `notifications.user.{id}` (:101), `App.Models.User.{id}` (:105), `ids` (:109), `planner.plan.{planId}` (:113), `planner.employee.{employeeId}` (:119), `planner.account.{accountId}` (:135), `offer-folder.{folderId}` (:148, Presence), `user.clipboard.{id}` (:159), `customer-notes.{c}.{a}.{l}` (:164), `company-activities` (:182), `lead-emails` (:186).

Listener (3, `app/Providers/EventServiceProvider.php:17-32`): `LogUserLogin` (Auth\Login), `LogUserLogout` (Auth\Logout), `StoreLeadActivity` (`LeadRecordChanged`). `shouldDiscoverEvents()` = `false` (`:44-47`).

**Befund — nicht registrierte Private Channels:** `employee.{id}` (`DashboardLiveActivityCreated:24`), `employee.{id}.tasks` (`PersonalTaskReminderTriggered:25`) und `employee-appointment.{id}` (`MainAppointmentReminderDue:27`) haben **keine Entsprechung** in `routes/channels.php` (`grep -n "employee" routes/channels.php` liefert nur `planner.employee` und den Payload-Helper).

### BEWERTUNG
Die drei nicht registrierten Private Channels sind ein konkreter Funktionsdefekt: Laravel Echo bekommt beim `/broadcasting/auth`-Aufruf 403, der Client abonniert nie — Dashboard-Live-Aktivität, Aufgaben-Erinnerung und Terminerinnerung erreichen den Mitarbeiter dann nicht. Das ist **stillschweigend**, weil der Fehler nur im Browser-Netzwerk-Tab sichtbar wird. Nächster Schritt wäre eine Laufzeitprobe (nicht Teil dieses Auftrags).
Zweite Beobachtung: Die Identität wird über `users.name` = `employees.id` aufgelöst (`routes/channels.php:24,30`). Das ist eine Zweckentfremdung einer Namensspalte als Fremdschlüssel und zieht sich durch `User::employeeId()` (`app/Models/User.php:79-82`) und alle Gates.

---

## 6 · Such-Technologie

### BELEGT

| Prüfung | Ergebnis |
|---|---|
| Laravel Scout / Meilisearch / Algolia / Typesense in `composer.json` | **nicht vorhanden** (`grep -n "scout\|meili\|algolia\|typesense" composer.json` = leer) |
| Elasticsearch | nur als **Suggest** in `monolog` (`composer.lock:3381,3395,3402`) — keine Anwendung |
| FULLTEXT-Indizes in Migrationen | **0** (`grep -rl "fullText\|FULLTEXT" database/migrations/` = leer) |
| `whereFullText` / `MATCH()` im Code | **0 Treffer** in `app/` |
| `'like'`-Vergleiche in `app/` | **1447 Fundstellen**, in 109 von 406 Controllern |
| benannte Such-Routen in `web.php` | 37 distinkte (`customers.search`, `products.search`, `contacts.global.search`, `lead.kanban.search`, `offer-templates.search.*`, `ids.local_search`, …), 57 Route-Zeilen mit `search`/`suche` |

### BEWERTUNG
**Keine Volltextsuche.** Die gesamte Suche im System ist SQL-`LIKE`, ganz überwiegend als `LIKE '%term%'` (führendes Wildcard ⇒ kein Index nutzbar). Bei 1447 Fundstellen ist das kein Detail, sondern die Suchstrategie des Hauses. Konsequenzen: Volltable-Scans auf den großen Tabellen (Kunden, Produkte, Angebotspositionen), keine Relevanzsortierung, keine Tippfehlertoleranz, keine feldübergreifende Suche ohne handgeschriebene `orWhere`-Ketten. Das ist gleichzeitig der billigste Performance-Hebel im Repo: MySQL-FULLTEXT auf den drei bis fünf meistgesuchten Tabellen wäre additiv und ohne neue Infrastruktur machbar. Meilisearch/Scout wäre die größere, aber betriebsseitig teurere Antwort.

---

## 7 · Caching

### BELEGT — `CACHE_DRIVER=file` (laut Auftrag), `Cache::` in `app/` = **48 Fundstellen** in 10 Dateien, **0** in `resources/views/`

| Datei | Treffer | Zweck (belegt) |
|---|---|---|
| `app/Http/Controllers/Customer/Offer/OfferDocumentController.php` | **21** (`:275-435`) | Presence + Bearbeitungssperre auf Angebotsdokumenten, TTL 1–3 min (`Cache::put($presenceKey, …, now()->addMinutes(3))`) |
| `app/Services/Sperre/BearbeitungsSperreService.php` | **12** (`:35-99`) | dieselbe Sperr-/Presence-Logik als Service, `PRESENCE_TTL_MIN` |
| `app/Http/Controllers/Customer/Offer/ClipboardController.php` | 4 | Zwischenablage Angebotspositionen |
| `app/Services/Suppliers/Omd/OmdAuthService.php` | 3 | OMD-Token-Cache |
| `app/Services/Suppliers/Mappers/IdsMapper.php` | 2 (`:8`, Tages-Zähler) | Skip-Zähler für Abdeckungslücken |
| `app/Policies/DealMeasurementPolicy.php` | 2 | Deny-Zähler |
| `app/Providers/AuthServiceProvider.php` | 1 (`:76`) | `image_delete_denied_count` |
| `app/Services/RoofAreaEstimator.php`, `app/Services/Import/ImportServiceClient.php`, `.../DealMaterialListController.php` | je 1 | Einzelfälle |

### BEWERTUNG
Cache wird im System **nicht zur Beschleunigung**, sondern fast ausschließlich als **verteilter Sperr-/Präsenzspeicher** (33 von 48 Fundstellen) und als Zähler benutzt. Kein einziges Dashboard-Widget, keine Stammdatenliste, keine Preisberechnung wird gecacht. Zwei Folgerungen:
1. Der `file`-Treiber ist für Sperren die falsche Wahl — er ist nicht atomar über Prozesse hinweg wie Redis, und `Cache::get`/`put` ohne `lock()` ist ein Race (Lost Update auf `$users`). Bei mehreren gleichzeitigen Bearbeitern eines Angebotsordners ist die Sperre nicht verlässlich.
2. Der Cache als Performance-Instrument ist **ungenutzt** — bei `LIKE`-Suche über große Tabellen liegt dort echter Spielraum.
3. Zähler im Cache (`image_delete_denied_count`) sind bei `file`-Treiber flüchtig und nicht auswertbar — für Sicherheits-Telemetrie ungeeignet; das gehört in ein Log oder eine Tabelle.

---

## 8 · Rollen und Berechtigungen

### BELEGT

**Modell (dreistufig, ohne RBAC-Paket):**
1. **Super-Admin-Flag**: `users.is_admin` → `User::isSuperAdmin()` (`app/Models/User.php:51-54`); `hasPermission()` gibt für Super-Admin **immer `true`** zurück (`:56-59`).
2. **Rechtezeilen**: `user_rolls` (`app/Models/UserRoll.php:12-28`), Spalten `user_id`, `item_id`, `is_read`, `is_add`, `is_update`, `is_delete`; Migrationen `2023_06_14_131732_create_user_rolls_table.php`, `2023_07_24_131308_create_user_roll_items_table.php`, Korrektur `2026_04_25_161053_fix_user_rolls_columns.php`.
3. **Prüfung**: `User::hasPermission($item, $action)` mappt Aktion → Spalte (`app/Models/User.php:61-72`), Middleware `permission` = `App\Http\Middleware\CheckUserPermission` (`app/Http/Kernel.php:77`), Implementierung `CheckUserPermission.php:9-22` (401 ohne User, 403 ohne Recht).

**Es gibt keine `roles`-Tabelle und keine `permissions`-Tabelle** — `ls database/migrations/ | grep -iE "role|permiss"` liefert nur `costing_set_roles` (fachfremd) und `personal_access_tokens`. Kein `spatie/laravel-permission` in `composer.json`.

**Durchdringung in `routes/web.php` (5700 Zeilen):**

| Middleware | Fundstellen | Beleg |
|---|---|---|
| `auth` | 222 | — |
| `permission:…` | **18** | `:2271-2275` (Users), `:4989-5022` (Hausplaner) |
| distinkte Rechte-Items | **2**: `Users`, `Hausplaner` | `grep -oE "permission:[A-Za-z,]+"` |
| `is_Admin` | 5 Gruppen | `:2315, 2522, 2883, 2891, 2910` — mit Kommentar „FIX P0-08: is_Admin jetzt wirksam (war Array-Index, wurde ignoriert)" |
| `isAdmin` (Alias, `app/Http/Kernel.php:71`) | 0 in web.php | — |
| `super` (`CheckSuperUser`) | 0 in web.php | — |
| `InvoiceMiddleware` | 2 | — |

**Gates/Policies:** 5 Policies (`app/Policies/`: AiChat, ChatGroup, DealMeasurement, GeneralTask, PersonalTask), registriert `app/Providers/AuthServiceProvider.php:19-26`. 3 Gate-Definitionen: `manage-chat-groups` (`:35-38`), `write-deal-measurement-offer` (`:42-52`), `delete-measurement-image` (`:57-81`).
**Controller mit Gate-Nutzung: 10** (`grep -rl "Gate::" app/Http/Controllers/`), davon **4** mit `$this->authorize()`/`->can()`.

Feature-Flags als weiche Rechte: `config/features.php` — `heizkoerper` (Modul-Kill-Switch via `EnsureHeizkoerperEnabled`), sowie 4 `*_hard_deny`-Flags, alle **Default `false` = weich** (`:15,21-24`), inkl. Kommentar „Übergangsphase erlaubt+loggt Waisen-Writes".

**Bekanntes Defizit ist dokumentiert:** `docs/backlog-rbac.md` — „Posten für ein späteres Rechte-/RBAC-Vorhaben (Ablösung `is_admin`/`user_rolls`)".

### BEWERTUNG
Das Rechtemodell ist **de facto binär**: Admin oder nicht. Von 2382 Routen sind 18 rechtegeprüft und 5 Gruppen adminbeschränkt — die Absicherung liegt praktisch vollständig auf `auth` (222 Fundstellen), also „eingeloggt = darf". `user_rolls` existiert technisch, wird aber an genau zwei Items (`Users`, `Hausplaner`) angewendet; die Tabelle ist damit weitgehend totes Kapital. Gates sind mit 10 von 406 Controllern (2,5 %) punktuell — sie decken exakt die Bereiche ab, für die es Security-Tests gibt (Aufmaß, HR, Produkt, Ticket-Finanz), was auf gezielte Nachbesserung nach einem Audit hindeutet, nicht auf ein Konzept.
Die `*_hard_deny`-Flags stehen alle auf weich: die neuen Ownership-Regeln **loggen** derzeit nur, sie sperren nicht. Wer meint, das Aufmaß sei abgesichert, irrt bis zum Umlegen dieser Schalter.
Der Kommentar bei `is_Admin` („war Array-Index, wurde ignoriert") belegt, dass Middleware hier schon einmal wirkungslos verdrahtet war — ein Muster, das bei String-basierter Middleware in einer 5700-Zeilen-Datei wiederkehren kann und nur durch Tests sichtbar wird.

---

## 9 · Audit / Historie

### BELEGT — eigene Lösung, kein `activity_log`, kein `audits` (kein `spatie/laravel-activitylog` in `composer.json`)

**Generisches Lead-Audit:** Trait `app/Traits/AuditableLead.php` hakt `created`/`updated`/`deleted` ein (`:12-44`) und schreibt Feld-Diffs `{from,to}` (`:20-37`) nach `lead_activity_logs`. Tabelle: `database/migrations/2026_01_07_073407_create_lead_activity_logs_table.php:12-32` — `new_leads_id`, `alternative_id`, `product_id`, `user_id`, `user_name` (Snapshot), `event_type`, `model_type`, `model_id`, `changes` (JSON). Indizes ergänzt in `2026_04_12_110537_add_activity_indexes_to_lead_activity_logs_table.php`. **In 27 Models eingebunden** (`grep -rl "AuditableLead" app/Models/ | wc -l`), u. a. `Offer`, `CustomerNote`, `LeadAlternativeAdd`, `LeadObjectRoom`, `MaintenanceAsset`.

**Fachspezifische Historientabellen (je eigene Migration + Model):**

| Tabelle | Migration | Model |
|---|---|---|
| `customer_histories` | `2025_06_26_053144_…` | `CustomerHistory.php` |
| `personal_task_histories` | `2025_12_03_120440_…` | `PersonalTaskHistory.php` |
| `inventory_histories` | `2026_03_26_102458_…` | `InventoryHistory.php` |
| `system_warning_histories` | `2026_04_24_091333_…` | `SystemWarningHistory.php` |
| `deal_measurement_histories` | `2026_04_30_150345_…` | `DealMeasurementHistory.php` (+ Logger `app/Support/DealMeasurementHistoryLogger.php`) |
| `product_histories` | `2026_05_05_093331_…` | `ProductHistory.php` |
| `offer_delete_logs` | `2026_05_06_085152_…` | `OfferDeleteLog.php` |
| `supplier_import_logs` | `2026_05_26_084703_…` | `SupplierImportLog.php` |
| `main_appointment_reminder_logs` | `2026_04_30_073948_…` | `MainAppointmentReminderLog.php` |
| `lead_activity_log_reads` | `2026_04_24_102818_…` | — |

**Historie als Spalte (nicht als Tabelle):** `2026_03_26_082948_add_history_to_offer_folders.php`, `2026_03_30_100614_add_material_history_to_offer_details_table.php`, `2026_06_09_123236_add_deal_link_and_history_to_invoices.php`, `2026_06_24_131829_add_planner_status_history_and_done_meta.php`.

**Anmelde-Historie:** `LogUserLogin` / `LogUserLogout` (`app/Providers/EventServiceProvider.php:22-28`).

**Aktivitäts-Feed (nicht Audit):** `lead_activity_logs` + `LeadActivityBroadcast` + `user_activity_filters` (`2026_04_10_133449_…`, erweitert `2026_04_17_075950_…`) speisen den Live-Feed und die Sidebar-Zähler.

### BEWERTUNG
Es gibt Historie — aber **fünf verschiedene Bauarten nebeneinander**: (a) generisches Trait mit JSON-Diff, (b) je Fachbereich eine eigene `*_histories`-Tabelle mit eigenem Schema, (c) Historie als JSON-Spalte am Datensatz, (d) reine Log-Tabellen für Löschungen/Importe, (e) Auth-Listener. Eine bereichsübergreifende Frage („wer hat diesen Datensatz wann angefasst") ist damit nicht in einer Abfrage beantwortbar. Für ein System mit FiBu-/DATEV-Anschluss (GoBD) ist das der schwächste Punkt der Inventur: revisionssichere, lückenlose Nachvollziehbarkeit über Belege hinweg ist auf dieser Basis nicht belegbar.
Konstruktiv: `AuditableLead` ist die einzige Lösung, die generisch skaliert (27 Models ohne Zusatzcode) — sie wäre der natürliche Kandidat für Verallgemeinerung, wenn man den Lead-Bezug (`customer_id`/`lead_id`/`alternative_id`, `:49-60`) durch einen polymorphen Anker ersetzt.

---

## 10 · Import / Export

### BELEGT — Importer

| Komponente | Format | Beleg |
|---|---|---|
| `Product/ProductCsvImportController.php` + `app/Services/ProductCsvImporter.php` | **CSV** (`league/csv` ^9.19, `composer.json:23`) | Dateiname/Service |
| `Product/ProductImageCsvImportController.php` | **CSV** (Bildzuordnung) | Dateiname |
| `Product/ProductImportController.php` | — | Dateiname |
| `LeadImportController.php` | — | Dateiname |
| `Customer/CustomerHistoryImportController.php` | — | Dateiname |
| `Customer/Climate/ClimateImportController.php` → `app/Jobs/ImportClimateWorkbookJob.php` | **XLSX** — eigener ZIP/XML-Parser, `sharedStrings`, kein PhpSpreadsheet (`:20,579-582,610,642`) | Code |
| `Customer/Moser/MoserWpImportController.php`, `MoserWpInvoiceImportController.php` | Fremdsystem-Übernahme | Dateiname |
| `ImportedIdsItemController.php` + `IdsController.php` | **IDS** (Shop-Roundtrip, `config/services.php:76-81`) | Code |
| `app/Services/Suppliers/SupplierProductImportService.php` + `Mappers/` | Kanäle laut Validierung: `ids, oci, api, csv, xml, bmecat, datanorm` (`Product/IDS/SupplierConnectionController.php:809`) | Code |
| `app/Services/Suppliers/Mappers/IdsMapper.php` | **produktiv** — „W2, erster produktiver Kanal" (`:11-13`) | Docblock |
| `app/Services/Suppliers/Mappers/DatanormMapper.php` | **Stub** — „Implementierung folgt bei Portierung. Kein Seiteneffekt, `map()` gibt null zurück" (`:9`, `:17-19`) | Code |
| `app/Services/Suppliers/Mappers/OmdMapper.php` + `Omd/OmdClient.php`, `OmdAuthService.php` | OMD-API | Dateien |
| `SpecImportCommand.php` + `app/Services/Spec/SpecImportService.php` | **JSON (kanonisch) oder CSV (Zubringer)**, Default DRY-RUN (`:15-22`) | Signatur |
| `DatanormController.php` | DATANORM (UI-Seite) | Dateiname |
| `app/Services/Import/ImportServiceClient.php` + `import-service/` (Python: `extractor.py`, `ocr.py`, `pdf_extractor.py`, `pdf_raster.py`) | **PDF/DXF + OCR** via HTTP-Microservice, `config/services.php:87-89` (`IMPORT_SERVICE_URL`, lokal `127.0.0.1:8001`, sonst „graceful aus") | Config + Dateien |

### BELEGT — Exporter

| Komponente | Format | Beleg |
|---|---|---|
| `app/Services/Accounting/DatevExtfExportService.php` | **DATEV EXTF „Buchungsstapel" Kat. 21 v13**, CP1252, Semikolon, Dezimalkomma, S/H-Kennzeichen (`:8-13`, Spaltenliste `:26-31`) | Code |
| `Contacts/AllContactController.php:77-92` | **CSV** (`text/csv; charset=UTF-8`, `fputcsv`), Route `all.contacts.export` | Code |
| `Product/ProductController.php:314-336` | **CSV**, Route `products.export.no-images` | Code |
| Lead-E-Mail-Export | Routen `lead.email.export.csv`, `lead.email.export.pdf` | `routes/web.php` |
| `Report/DailyReportController.php` | **PDF** (`barryvdh/laravel-dompdf` ^3.1 / `spatie/browsershot` ^5.2) — einzige Datei mit `Pdf::loadView`/`Browsershot` | `grep -rl` |
| `Report/DailyReportAttachmentController.php:369` | Mime-Mapping u. a. `csv` | Code |

Wichtiger Betriebsvorbehalt im Code: `DatevExtfExportService.php:22-23` — „**Betriebsordnung 2.3: Versand nur durch Yama.** Dieser Service erzeugt/prüft die Datei; er verschickt nichts."

### BEWERTUNG
**Real verarbeitet werden: CSV, XLSX (eigener Parser), JSON, IDS/OCI-Roundtrip, PDF/DXF (über Python-Dienst), DATEV-EXTF (raus).** **Nicht real**: DATANORM (Stub) und BMEcat (nur als erlaubter Enum-Wert, kein Mapper). Die Enum-Liste in `SupplierConnectionController.php:809` verspricht sieben Kanäle, geliefert werden zwei (IDS, OMD) — das ist eine Erwartungsfalle in der Oberfläche.
Der XLSX-Import als handgeschriebener ZIP/XML-Parser (`ImportClimateWorkbookJob:579-642`) ist ein Sonderweg ohne Bibliothek: er funktioniert für die eine erwartete Mappe, ist aber gegenüber Formatvarianten (inline strings, Datumsserien, mehrere Sheets) brüchig.
Export ist **deutlich schwächer als Import**: vier CSV-/PDF-Ausleitungen im ganzen System. Für ein CRM mit 3000 Kunden fehlt jede generische Listenausleitung.
Der Python-Microservice (`import-service/`) ist die einzige Stelle außerhalb PHP — er hat eigene Tests (`test_extractor.py`, `test_ocr.py`, `test_pdf_extractor.py`, `test_pdf_raster.py`), die **nicht** Teil der 128 PHPUnit-Tests sind.

---

## 11 · APIs

### BELEGT — `routes/api.php` (515 Zeilen, 44 Definitionen), Prefix `/api` via `RouteServiceProvider:32-34`, Rate-Limiter `api` = 60/min je User/IP (`:27-29`)

| Gruppe | Auth | Endpunkte (Auszug) | Zeile |
|---|---|---|---|
| `GET /api/user` | `auth:sanctum` | Benutzerobjekt | 40-42 |
| **Lead-Vorschläge** | **KEINE** | `/lead-name-suggestions`, `/lead-lastname-suggestions` | 56-60 |
| Task-Notifications | `auth:sanctum` | `/notifications/task/{task_id}` | 69-73 |
| **Fusion-Webhook (Entries)** | **nur `throttle:10,1`** | `POST /fusion-form/webhook/entries` | 80-83 |
| Fusion-Webhook (receive) | `fusion.token` (`VerifyFusionToken`) | `POST /fusion/webhook` | 85-87 |
| Mobile-Login | `throttle:20,1` | `POST /mobile/login` | 99-105 |
| **Mobile-App** | `auth:sanctum` | `/mobile/me`, `/logout`, `/profile`, `/tasks`, `/tasks/sync`, `/attendance/{action,location,sync,history,log}`, `/calendar` (GET/POST/{id}), `/employees`, `/customers` | 113-186 |
| **Master-Sets** | **nur `throttle:60,1`** | `GET /secure/master-sets`, `/secure/master-sets/{id}`, `GET /secure/master-sets-debug` | 195-212 |
| Planner-Auth | `throttle:10,1` | `POST /planner/auth/token` | 232-234 |
| **Planner/Nuriva** | `auth:sanctum` | `/planner/auth/{me,logout,logout-all}`, `/my-work`, `/my-day-report`, `/employees/{id}/work`, `/employees/{id}/day-report`, `PATCH /items/{item}/complete-report`, `POST /customer-images/upload`, `GET /customer-images`, `/master-sets*` (6 Routen), `/items/{item}/materials` (GET/POST) | 240-353 |

Sanctum: `laravel/sanctum` ^4.0 (`composer.json:18`), `config/sanctum.php`, `personal_access_tokens`-Migration vorhanden.

### BEWERTUNG
Die API ist **zweigeteilt**: ein sauberer, durchgängig Sanctum-geschützter Teil (Mobile-App + Planner/Nuriva, ~35 Routen) und drei ungeschützte Ausreißer.
Konkret ungeschützt und aus dem Netz erreichbar:
- `/api/secure/master-sets*` — der Pfadbestandteil „secure" ist **irreführend**: die Gruppe hat nur `throttle:60,1`, keine Auth (`:195-212`). Ein `…-debug`-Endpunkt liegt daneben ebenfalls offen (`:210-212`).
- `/api/fusion-form/webhook/entries` — `POST`, nur Throttle, während der Schwester-Endpunkt `/api/fusion/webhook` korrekt `fusion.token` trägt (`:80-87`). Asymmetrie im selben Abschnitt.
- `/api/lead-name-suggestions`, `/api/lead-lastname-suggestions` — offene Autovervollständigung über Kundennamen; erlaubt Enumeration des Kundenbestands.

Das ist die belastbarste Sicherheitsaussage dieser Inventur, weil sie ohne Interpretation aus den Route-Definitionen folgt.

---

## 12 · Tests

### BELEGT — 128 Dateien: **93 Feature / 35 Unit**, `phpunit.xml` zwei Suites

Test-Isolation ist erzwungen: `phpunit.xml:24-27` setzt `DB_DATABASE=ticket_testing` mit `force="true"` („erzwingt jeden Testlauf gegen die isolierte Test-DB, NIE gegen die reale Dev-DB ticket"); zusätzlich `BROADCAST_DRIVER=null` (`:33`, Begründung im Kommentar), `QUEUE_CONNECTION=sync`, `CACHE_DRIVER=array`, `SESSION_DRIVER=array`, `MAIL_MAILER=array`.

| Fachbereich | Dateien | Ordner |
|---|---|---|
| Heizlast (Unit) | 13 | `tests/Unit/Heizlast/` |
| Security | **14** | `tests/Feature/Security/` |
| Offer | 13 (+2 Unit) | `tests/Feature/Offer/`, `tests/Unit/Offer/` |
| Hausplaner | 10 | `tests/Feature/Hausplaner/` |
| Accounting | 7 | `tests/Feature/Accounting/` |
| BuildingModel | 2 + 4 Unit | — |
| Geometrie | 5 + 2 Unit | — |
| Form | 4 + 4 Unit | — |
| Heizkörper | 5 + 2 Unit | — |
| Energie | 3 + 5 Unit | — |
| Invoice | 4 | `tests/Feature/Invoice/` |
| Suppliers | 3 | IdsMapper (2), OmdClient |
| Spec | 2 | Import, Commit |
| Auslegung (Unit) | 2 | — |
| Catalog | 2 | `FoxEssLongiTeardownTest`, `WberechnungImportTest` |
| Arbeitsliste | 2 | — |
| Anforderungsprofil | 2 | — |
| Planner / Kanban / Customer / Dashboard / Maintenance / Styleguide / VideoCall / DealMeasurement / FollowUp | je 1 | — |
| Root-Feature | 5 | `ExampleTest`, `FusionWebhookTest`, `PlanUploadTest`, `WaermepumpeKomplettloesungSeederTest` |

**Zur konkreten Frage:**

| Bereich | vorhanden? | Beleg |
|---|---|---|
| **Produkte** | **nein, kein fachlicher Produkt-Test.** Nur ein Rechte-Test `tests/Feature/Security/ProductPermissionGateTest.php` und ein Formular-Pilot `tests/Feature/Form/WpProduktFormularPilotTest.php` | `find tests -iname '*product*'` |
| **Preise** | **teilweise**: `tests/Unit/Offer/CatalogPriceGuardTest.php`, `tests/Feature/Offer/OfferCatalogPricingTest.php`, `tests/Feature/DealMeasurement/PriceNullableTest.php`, `tests/Unit/Auslegung/WpCostingServiceTest.php` | — |
| **Lieferanten** | **ja, dünn**: `tests/Feature/Suppliers/{IdsMapperTest, IdsMapperHookTest, OmdClientTest}.php` | — |
| **Import** | **ja, teilweise**: `tests/Feature/Spec/SpecImportTest.php`, `tests/Feature/Catalog/WberechnungImportTest.php`, `tests/Feature/PlanUploadTest.php` — **kein Test für ProductCsvImporter, LeadImport, ClimateImport (XLSX), CustomerHistoryImport, Moser-Import** | `find tests -iname '*import*'` |

### BEWERTUNG
Die Testabdeckung folgt exakt der jüngsten Bauaktivität und **nicht** dem Geschäftsrisiko. Rechenkerne (Heizlast, Auslegung, Geometrie, Form) sind mit Unit-Tests solide unterlegt — das ist die beste Zone im Repo. Die 14 Security-Tests sind erkennbar Nachlauf eines Audits (Namen wie `UngatedWriteRoutesAuthTest`, `BulkDeleteAdminGateTest`).
Die Lücke ist der **kaufmännische Stammdatenpfad**: 62 Produkt-Controller, 20 Produkt-Controller-Dateien, ein CSV-Importer, ein XLSX-Importer, ein Preis-/Rabattsystem — und dafür kein einziger Importer-Test und kein Produkt-CRUD-Test. Genau dort passieren stille Datenschäden (falsche Preise, doppelte Artikel), die kein Nutzer sofort meldet.
128 Tests auf 406 Controller + 410 Models sind rechnerisch dünn; die Verteilung macht es schlimmer als die Zahl.

---

## 13 · Dokumentation

### BELEGT — `docs/`: **2933 Dateien**, 19 Unterordner, 196 `.md` direkt im Wurzelverzeichnis

| Ordner | Dateien | Inhalt |
|---|---|---|
| `docs/_playground-archiv` | **2342** | Archiv aus Fremd-App `playground` — 80 % des gesamten `docs/` |
| `docs/auftraege` | 121 | Auftragsblätter |
| `docs/planner` | 78 | Planner-Rolle: Zielbilder, Bestandsaufnahmen, Agent-/Skill-Matrizen, `PRUEFER-BEFUNDE.md` |
| `docs/accounting` | 50 | FiBu-Konzepte, u. a. `umsatzdefinition.md` |
| `docs/uebernahme` | 28 | Übernahmen aus `wberechnung`/`playground` |
| `docs/audit` | 23 | Code-Audits |
| `docs/software-audit` | 21 | Regelwerke `regel-*.md` (architektur, blade, crud, routing, workflow …), Befund-CSVs |
| `docs/building-planner` | 20 | 3D-Planer |
| `docs/agents` | 15 | Rollen: `01-planner.md`, `02-generator.md`, `03-evaluator.md`, `regeln/` |
| `docs/configuration` | 11 | Konfigurator/Auslegung, ADRs |
| `docs/accounting`, `3d`, `befunde`, `analyse`, `deploy`, `konzept`, `quellen`, `spec-import` | 1–6 | — |

**Die 15 wichtigsten Dokumente** (Auswahl nach Verbindlichkeit + Aktualität; Zweck aus dem jeweiligen Kopf, Zeilen 1–4):

| # | Datei | Zweck (belegt) |
|---|---|---|
| 1 | `docs/BETRIEBSORDNUNG.md` | oberste verbindliche Hausordnung; „Autorität: Yama… bindet JEDE Instanz", Grundgesetze zu Datenschutz, Additivität, „eine Wahrheit je Sachverhalt", Git-Hygiene |
| 2 | `docs/STAND.md` | einseitiges, **überschriebenes** Arbeitsgedächtnis — aktueller Baustand, was abgenommen/offen ist (zuletzt 01.08. 10:25) |
| 3 | `docs/arbeitskompass-ticket.md` | laufende Orientierung, was gerade woran gearbeitet wird (Stand 21.07., selbst als „11 Tage alt" markiert) |
| 4 | `docs/architektur/bauordnung.md` | Wie-baue-ich-Leitfaden, abgeleitet aus CODE-AUDIT-01; benennt gute Muster als Hausmaßstab und schlechte (ungegatete Routen, God-Table, Status-Zoo, Inline-JS, Zombie `customers`) als Verbot |
| 5 | `docs/architektur/ui-bauordnung.md` | Styleguide-Pflicht, Echtdaten-Prüfung, visuelle Regression für jede UI-Änderung |
| 6 | `docs/agents/regeln/kern.md` (+ `planner/generator/evaluator/plan-reviewer.md`, zusammen 736 Z.) | gültige Rollenregeln seit 30.07.; `docs/agents/00-REGELWERK.md` markiert sich selbst als ältere Ablage |
| 7 | `docs/architektur-entscheidungen.md` | die fünf Grundsatz-Weichen für tickets Kernprozess — Fundament für Cockpit, Controlling, Buchhaltung |
| 8 | `docs/entscheidungen.md` | strangübergreifendes Weichen-Register (Datum · Alternativen · Begründung), Änderung = Eskalation |
| 9 | `docs/system-bereichsstruktur.md` | aus den Inventuren abgeleitete Bereichsstruktur (read-only, Vorstufe zum Gesamtkonzept) |
| 10 | `docs/workflow-sollkonzept.md` | Soll-Beschreibung des gewerkeübergreifenden Vorgangs-Systems (kein Bauauftrag) |
| 11 | `docs/zielbild-objekt-zentriertes-crm.md` | Vision: ticket als objektzentriertes Komplettsanierungs-CRM, inkl. zwingender Reihenfolge |
| 12 | `docs/crm-inventur-00-index.md` | navigierbare Landkarte über 8 Zonen-Inventuren (`crm-inventur-01..08`) + Lückenliste |
| 13 | `docs/gesamtfahrplan-gebaeude-energie-angebot.md` | kapitelweiser Fahrplan Gebäude/Energie/Angebot: was fertig, was fehlt, was als Nächstes, wo Browser-Test erlaubt |
| 14 | `docs/accounting/umsatzdefinition.md` | Dauerregel: `invoices` = einzige Umsatzwahrheit, bindet Cockpit/Controlling/FiBu/Reporting |
| 15 | `docs/stabilitaet-routing-workflow.md` (80 KB) | priorisierte Stabilitätsbefunde zu Routing/Verknüpfungen/Workflow, am Code verifiziert; Ergänzung `docs/stabilitaet-fixliste.md` |

Ehrenwerte Erwähnung: `docs/backlog-rbac.md` (Rechte-Schuldenregister, §8), `docs/software-audit/regel-*.md` (10 Prüfregeln), `docs/handoff-status.md` (1,85 MB — seit 31.07. **selbst als Archiv markiert**: „AB 31.07.2026, 10:30 IST DIESE DATEI ARCHIV").

### BEWERTUNG
Die Dokumentation ist **außergewöhnlich umfangreich und ungewöhnlich diszipliniert im Anspruch** (Betriebsordnung, Weichenregister, Bauordnung, Rollenregeln) — und gleichzeitig ihr eigenes Problem. Drei belegte Symptome: (a) `docs/_playground-archiv` stellt mit 2342 von 2933 Dateien 80 % und ist Fremdmaterial; (b) `docs/agents/00-REGELWERK.md` und `00-zyklus.md` warnen im ersten Satz, dass sie von `docs/agents/regeln/` überholt wurden — es gab also zwei konkurrierende Regelablagen; (c) `handoff-status.md` musste auf 1,85 MB anwachsen, bevor `STAND.md` als bewusst *überschriebene* Einseiter-Lösung eingeführt wurde. Der Betrieb hat das Problem erkannt und gegengesteuert; die Altlast liegt aber noch im Baum.

---

## 14 · Konfiguration — externe Dienste

### BELEGT — `config/services.php`

| Schlüssel | Dienst | Zeile |
|---|---|---|
| `mailgun` | Mailgun (Domain/Secret/Endpoint) | 18-23 |
| `postmark` | Postmark Token | 25-27 |
| `ses` | AWS SES | 29-33 |
| `fusion_forms` | **doppelt definiert** — Z. 35-37 `FUSION_FORMS_TOKEN`, Z. 39-41 `FUSION_WEBHOOK_TOKEN`; der zweite überschreibt den ersten | 35-41 |
| `myuplink` | myUplink (Wärmepumpen-Telemetrie), Client-ID/Secret | 43-46 |
| `openweather` | OpenWeather (`WEATHER_API_KEY`) | 48-50 |
| `tomorrowio` | Tomorrow.io (`DASHBOARD_KEY`) | 52-54 |
| `rapidapi` | RapidAPI | 56-58 |
| `ollama` | **lokales LLM** — Host `127.0.0.1:11434`, Modell `llama3:instruct`, Embed `mxbai-embed-large` | 60-64 |
| `overpass` | Overpass API (OpenStreetMap) | 66-68 |
| `google` | Google Maps Key | 69-71 |
| `newsapi` | NewsAPI + Gruppen-Slug `solar-news` | 73-76 |
| `ids` | IDS-Shop `gconlineplus.de/ids.aspx`, Kundennr./User/Passwort | 78-83 |
| `import` | Python-Import-Microservice, `IMPORT_SERVICE_URL`, lokal `127.0.0.1:8001`, sonst „graceful aus" | 85-89 |

Weitere Dienstkonfiguration außerhalb `services.php`: `config/mapbox.php` + `koossaayy/laravel-mapbox`, `config/jitsi.php` (Videokonferenz), `config/imap.php` + `webklex/laravel-imap` (Lead-Mail-Sync), `config/reverb.php` + `laravel/reverb` ^1.5 (WebSockets, dazu `laravel-reverb.service` im Repo-Root), `config/barcode.php` + `milon/barcode`, `config/dompdf.php`, `config/branding.php`, `config/mahnwesen.php`, `config/offer.php`, `config/vertrieb.php`, `config/geometrie.php`, `config/wp_auslegung.php`, `config/features.php`, `config/stats.php`.

### BEWERTUNG
14 externe Dienste in `services.php`, davon **drei Mail-Provider** parallel konfiguriert (Mailgun, Postmark, SES) — welcher aktiv ist, entscheidet `config/mail.php`/`.env`, nicht diese Datei. Der Doppelschlüssel `fusion_forms` (Z. 35 vs. 39) ist ein echter Bug: PHP nimmt den zweiten, `FUSION_FORMS_TOKEN` ist damit **wirkungslos**. Wer die Fusion-Integration über die falsche Env-Variable konfiguriert, bekommt stillschweigend keinen Token.
Bemerkenswert positiv: KI läuft über **lokales Ollama**, nicht über eine Cloud-API — kein Kundendatenabfluss, aber auch eine harte Betriebsabhängigkeit von `127.0.0.1:11434`. Der Import-Microservice ist sauber „graceful aus", wenn `IMPORT_SERVICE_URL` fehlt (Z. 87-88) — das ist der richtige Umgang mit einer optionalen Abhängigkeit und sollte Vorbild für andere Integrationen sein.

---

## Zusammenfassung — was ich als die fünf schwersten Punkte sehe (BEWERTUNG)

1. **Ungeschützte API-Endpunkte** (§11): `/api/secure/master-sets*` inkl. `-debug` und `/api/fusion-form/webhook/entries` tragen nur Throttling; die offenen Lead-Namensvorschläge erlauben Bestandsenumeration. Belegt, nicht interpretiert.
2. **Rechtemodell faktisch binär** (§8): 18 von 2382 Routen mit `permission:`, zwei Rechte-Items, alle Ownership-Härtungen per Flag auf „weich". Das Haus weiß es (`docs/backlog-rbac.md`), aber der Zustand ist der Zustand.
3. **Suche = 1447× `LIKE`** (§6): keine Volltextinfrastruktur, keine Indexnutzbarkeit. Größter, billigster Performance-Hebel.
4. **Audit fünffach gebaut** (§9) bei gleichzeitigem DATEV-Anschluss (§10) — GoBD-taugliche, übergreifende Nachvollziehbarkeit ist auf dieser Basis nicht belegbar.
5. **Testabdeckung folgt der Bauaktivität, nicht dem Risiko** (§12): Rechenkerne gut, kaufmännische Stammdaten und **alle** Importer ohne Test.

Dazu drei kleine, sofort belegbare Defekte: nicht registrierte Broadcast-Kanäle `employee.*` / `employee-appointment.*` (§5), doppelter `fusion_forms`-Schlüssel in `config/services.php:35/39` (§14), doppelte Scheduler-Registrierung von `personal-tasks:process-scheduler` (§4).

## OFFEN (nicht geklärt, statt geraten)
- Tatsächliche Routenzahl (`route:list` wäre eine Ausführung) — §0.
- Läuft ein Queue-Worker produktiv? Kein Supervisor-/systemd-Unit außer `laravel-reverb.service` gefunden; `docker/` nicht geöffnet — §4.
- Ob `app/Http/Controllers/Old/` (37 Dateien) wirklich unerreichbar ist — §1.
- Inhalt von `.env` (Produktiv-Schalter der 14 Dienste) nicht gelesen — §14.
- Ob die drei fehlenden Broadcast-Kanäle im Betrieb tatsächlich 403 liefern, ist nicht laufzeitgeprüft, nur aus `channels.php` abgeleitet — §5.
