# Audit – Architektur

Funde: 43  ·  🔴 4 kritisch · 🟠 21 hoch · 🟡 16 mittel · ⚪ 2 niedrig

### 🔴 Fat Controller Extrem: NewLeadsController (14.049 Zeilen, 121 Methoden)  
**Modul:** Querschnitt · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:1`  
**Problem:** Der Controller umfasst 14.049 Zeilen und 121 public-Methoden. Einzelne Methoden wie loadHistoryFeed (693 Zeilen, L10594), loadHistoryModal (637L, L7946), view (633L, L1904), customerFeed (495L, L4028), store (493L, L517) und checkCustomer (407L, L5135) enthalten vollständige Geschäftslogik: Lead-Qualifizierung, Kundennummer-Generierung, Geo-Koordinaten-Verarbeitung, E-Mail-Statusprüfung, PV-Daten-Speicherung, Kalkulations-Logik. Der Controller importiert 53 verschiedene Model-Klassen direkt.  
**Fix:** Den Controller in mind. 8 thematische Klassen aufteilen: LeadStoreService, LeadUpdateService, LeadViewService, LeadFeedService, LeadHistoryService, PvDataService, LeadQualificationService, LeadSearchService. FormRequest-Klassen für store() und update() anlegen (je ~40 Validierungsregeln). Geschäftslogik in Services auslagern, Controller reduziert auf Routing-Aufgaben.

### 🔴 Keine FormRequest-Klassen im gesamten Projekt (0 von 380 Controllern)  
**Modul:** Querschnitt · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/:1`  
**Problem:** Im gesamten Projekt existiert kein einziges app/Http/Requests-Verzeichnis und keine FormRequest-Klasse. Alle Validierungen sind inline im Controller-Body per $request->validate(). Allein NewLeadsController und PlannerPlanController haben je 31 inline-validate()-Aufrufe; MainAppointmentController 15, PersonalTaskBoardController 19, DepartmentController 12, EmployeeController 10. Validierungsregeln sind nicht wiederverwendbar, nicht testbar und vergrößern Controller-Methoden unnötig.  
**Fix:** app/Http/Requests/ anlegen. Priorität: StoreNewLeadRequest, UpdateNewLeadRequest (je ~40 Regeln), StorePlannerItemRequest, UpdateItemStatusRequest. Für jede Controller-Methode mit >5 validate()-Regeln eine eigene FormRequest-Klasse erstellen. Sofortiger Nutzen: Methoden wie store() in NewLeadsController schrumpfen von 493 auf ~50 Zeilen.

### 🔴 Fehlende Service-/Repository-Schicht: 374 von 380 Controllern ohne Service-Nutzung  
**Modul:** Querschnitt · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/:1`  
**Problem:** Von 380 Controllern nutzen nur 6 (1,6%) eine Service-Klasse aus app/Services/. Gleichzeitig gibt es 16 Service-Klassen und 0 Repository-Klassen. Sämtliche Geschäftslogik, DB-Abfragen, Berechnungen und externe API-Aufrufe sind direkt in Controller-Methoden implementiert. 53 Model-Importe in einem einzelnen Controller (NewLeadsController) sind symptomatisch für fehlende Abstraktionsschichten.  
**Fix:** Service-Layer-Nutzung auf alle fetten Controller ausweiten. Repository-Pattern für häufige komplexe Queries (z.B. Lead-Suche, Kanban-Feed, Daily-Report-Reload) einführen. Faustregel: Jede Methode >50 Zeilen oder >1 Model-Schreiboperation gehört in einen Service.

### 🟠 Monster-Controller: EmployeeDashboardController hat 2333 Zeilen  
**Modul:** Dashboard & Berichte · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/EmployeeDashboardController.php:1`  
**Problem:** Ein einzelner Controller vereint Dashboard-Rendering, Wetter-API-Abfragen, Urlaubs-/Kranktags-Berechnung, Task/Appointment/Lead/Inquiry/Leave/TicketTask-Aggregation, markAsDone-Logik für 7 Entitätstypen mit vollständiger DB-Schreiblogik sowie HR-Widgets. Keine Service-Schicht, keine FormRequest-Validierung, keine Repository. Dies führt zu enger Kopplung, schwieriger Testbarkeit und fehlendem Separation of Concerns.  
**Fix:** Aufteilen in DashboardQueryService (Datenaggregation), MarkAsDoneService (Schreiboperationen je Typ), DashboardController (nur View-Return). FormRequest-Klassen für getDueToday und markAsDone einführen.

### 🟠 OverdueCenterController hat 4612 Zeilen – extremes Fat-Controller-Problem  
**Modul:** Dashboard & Berichte · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Report/OverdueCenterController.php:1`  
**Problem:** Der Controller enthält sämtliche Overdue-Abfragelogik (5 Entitätstypen), Report-Store-Logik (5 Entitätstypen), History-Logik, Notification-Logik, Reminder-Logik, Schema-Introspektionslogik und N Hilfsformatierungsmethoden. Für die Mitarbeiter-Zusammenfassung (recentReportsEmployeeSummary) werden 5 separate Sub-Controller-Methoden aufgerufen. Auch eine Kopie existiert: Old/OverdueCenterController copy.php (104 kB).  
**Fix:** Logik in OverdueQueryService, OverdueReportService und OverdueNotificationService auslagern. Old/-Kopie löschen. Schema-Introspektionshelfer in eigenem Trait kapseln.

### 🟠 N+1-Queries bei Chat-Sidebar: 2 Queries pro User und 2 Queries pro Gruppe  
**Modul:** CRM – Kommunikation · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Chat/ChatController.php:123-143 / 185-232`  
**Problem:** getEmployeesAndGroups() lädt alle Nutzer und alle Gruppen in je einer Query, iteriert dann aber in einer foreach-Schleife über jeden User (n) und jede Gruppe (m) und führt pro Iteration 2 separate Chat::query()-Aufrufe aus (lastMsg + unreadCount). Bei 20 Mitarbeitern und 30 Gruppen = 100 Datenbankabfragen pro API-Aufruf, der bei jedem Öffnen des Chat-Panels ausgelöst wird.  
**Fix:** lastMsg und unreadCount pro User/Gruppe als Subquery oder mittels DB::select mit GROUP BY vorab aggregieren und den Ergebnissen zuordnen, statt N einzelne Queries auszuführen.

### 🟠 Fat Controller: store() und update() enthalten vollstaendige Terminplanungslogik (~300 Zeilen)  
**Modul:** CRM – Anfragen · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Inquiry/InquiryController.php:1075-1199,1364-1491`  
**Problem:** Die Methoden store() und update() enthalten jeweils ~120 Zeilen fuer die synchrone Erstellung von MainAppointment- und MainAppointmentEmployee-Eintraegen direkt im HTTP-Request-Zyklus. Dieselbe Logik ist in beiden Methoden nahezu identisch dupliziert (Unterschied: einmal Model->save(), einmal DB::insertGetId()). Ausserdem fehlt eine DB-Transaktion: wenn das Erstellen des Appointments nach dem Speichern der Inquiry fehlschlaegt, bleibt der Datensatz inkonsistent.  
**Fix:** Appointment-Erstellung in einen InquiryService oder einen Job (Queue) auslagern. Private Methode createAppointmentsForInquiry() extrahieren, die von store() und update() aufgerufen wird. Gesamten Ablauf (Inquiry + ProductList + Appointments) in DB::transaction() einbetten.

### 🟠 Extremer Fat-Controller: 14.049 Zeilen, 80+ public-Methoden in einer Klasse  
**Modul:** CRM – Leads & Kunden · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:1`  
**Problem:** NewLeadsController enthält 14.049 Zeilen und über 80 öffentliche Methoden. Er übernimmt Lead-CRUD, Objekt-Verwaltung, PV-Daten, Mitarbeiter-Zuweisung, Finanzierungsdaten, Referenzen, Duplikat-Erkennung, Audit-Logging, Notification-Dispatch, Analytics, Export und mehr. Keinerlei Service-Klassen, FormRequests oder Repositories vorhanden. Die Geschäftslogik (Qualifizierungs-Status-Berechnung, Kundenummer-Generierung, Mitarbeiter-Verfügbarkeitsprüfung) ist vollständig im Controller inliniert.  
**Fix:** Aufteilen in dedizierte Service-Klassen (LeadQualificationService, LeadCreationService, LeadNotificationService), FormRequest-Klassen für store/update/details_update, Repository für NewLeads-Queries. Kleinere thematische Controller ableiten.

### 🟠 Fat Controllers: OfferController (2929 Z.) und OfferFolderController (3810 Z.) mit gesamter Geschäftslogik  
**Modul:** Vertrieb – Angebote · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/Offer/OfferController.php:1 und app/Http/Controllers/Customer/Offer/OfferFolderController.php:1`  
**Problem:** OfferController (2929 Zeilen) übernimmt Angebotserstellung, Ordnerverwaltung, Lead-Synchronisation, Angebotsnummern-Generierung, Aktivitäts-Broadcasting und Index-Aggregation in einer einzigen Klasse. OfferFolderController (3810 Zeilen) enthält Workflow-Stage-Auflösung, Team-Zugriffslogik, Sektion-Hydration, Attachment-Verwaltung, Kanban-Logik und Lieferanten-Synchronisation. Kein Service Layer, keine FormRequests, keine Repositories vorhanden.  
**Fix:** Extrahieren: OfferNumberService (Nummernvergabe), OfferFolderWorkflowService (Stage-Logik), OfferTeamAccessService (Zugriffslogik), OfferSyncService (Lead-Sync). FormRequests für store/update anlegen. Controller reduzieren auf Routing und HTTP-Response.

### 🟠 Geschäftslogik (DB-Queries, Model-Zugriffe) direkt im @php-Block des Blade-Views  
**Modul:** Vertrieb – Angebote · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/offer/folder-show.blade.php:54-196`  
**Problem:** Der @php-Block in folder-show.blade.php (ca. 140 Zeilen) führt mehrere eigenständige Eloquent-Queries aus: LeadStage::query()->...->first() (Zeile 61), erneut LeadStage::query()->...->first() (Zeile 76), LeadStage::query()->...->get() (Zeile 93), LeadStageSubStage::query()->...->get() (Zeile 115, zweimal aufgerufen), Employee::query()->...->find() (Zeile 161). Diese Logik dupliziert identische Methoden in OfferFolderController (findWorkflowMainStage, getKanbanStagesForDocument).  
**Fix:** Alle Queries aus dem Blade entfernen. Der Controller übergibt die aufgelösten Daten als View-Variablen ($offerWorkflowMainStage, $dealWorkflowMainStage, $availableWorkflowMainStages, $currentPresenceEmployee). Existierende Controller-Methoden wiederverwenden.

### 🟠 Monster-View: customer_view.blade.php mit 2017 Zeilen Inline-CSS und 1900 Zeilen Inline-JS  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/deal/customer_view.blade.php:1–4823`  
**Problem:** Die Datei ist 4823 Zeilen lang. Davon enthalten @section('style') ca. 2017 Zeilen reines CSS und @section('script') ca. 1900 Zeilen JavaScript. Business-Logik (DB-Queries, Permission-Checks, Carbon-Formatierungen) ist im @php-Block direkt im Template verteilt. Das verletzt MVC, macht Tests unmöglich und erschwert jede Wartung erheblich.  
**Fix:** CSS in dedizierte Stylesheet-Dateien (z.B. public/css/deal-list.css) auslagern. JS in app-assets oder Vite-compilierte Module. DB-Abfragen und Permission-Checks vollständig in den Controller oder einen DealListPresenter verschieben. Den @forelse-Block in eine Blade-Komponente oder ein dediziertes Partial refactoren.

### 🟠 Extremer Fat Controller: PlannerPlanController hat 11.080 Zeilen und 67 Methoden  
**Modul:** Projekte & Planer · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Planner/PlannerPlanController.php:1`  
**Problem:** Der `PlannerPlanController` umfasst 11.080 Zeilen und 67 öffentliche Methoden (inkl. Auth, Realtime, Notifications, Materialien, Projektverwaltung, Kanban, Attendance, History, etc.). Dazu kommen `PersonalTaskController` (6.570 Zeilen) und `MainAppointmentController` (3.509 Zeilen) mit ähnlichem Muster. Keine einzige Service-Klasse oder FormRequest-Klasse existiert im gesamten Projekt (`app/Http/Requests/` fehlt). Die Testbarkeit und Wartbarkeit sind praktisch null.  
**Fix:** Verantwortlichkeiten in dedizierte Service-Klassen auslagern (z. B. `PlannerPlanService`, `PlannerAttendanceService`). Validierungslogik in FormRequest-Klassen verschieben. Der Controller sollte nur HTTP-Koordination übernehmen (unter 200 Zeilen/Methode anstreben).

### 🟠 Geschäftslogik und DB-Queries direkt im Blade-Template (task_details.blade.php)  
**Modul:** Projekte & Planer · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/todo/personal/task_details.blade.php:227`  
**Problem:** Das Template enthält mindestens 3 direkte `DB::table(...)->...->get()/first()` Aufrufe innerhalb von `@php`-Blöcken (Z. 227, 280, 652), die Datenbankabfragen in der View ausführen. Außerdem wird Business-Logik berechnet (Zeitdifferenz, Prozentwert). Das verletzt MVC-Schichten, macht Caching unmöglich und erschwert Testing.  
**Fix:** DB-Abfragen in den Controller verschieben und als View-Variablen übergeben. Berechnungslogik (Prozent, Status-Icon) in einen Service oder einen View-Composer auslagern.

### 🟠 Datenbankabfragen in profile.blade.php (Geschäftslogik in der View)  
**Modul:** Support – Tickets · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/problem/profile.blade.php:132-281`  
**Problem:** Die View enthält in einem großen @php-Block echte DB/Eloquent-Abfragen: MainAppointment::query()->where('problem_id', ...)->get() (Zeile 132), ProblemComment::where()->count() (139), ProblemComment::with('employee')->latest()->get() (143), DB::table('employee_problem')->join(...)->get() (269), Employee::query()->get() (263). Diese Logik gehört in den Controller oder einen Service. Wenn der Controller die Variable bereits übergibt (z.B. $ticketAppointments), passiert die Abfrage doppelt (der Blade-Code ist als Fallback mit ?? geschrieben, wird aber ausgeführt wenn der Controller die Variable nicht setzt).  
**Fix:** Alle Datenbankzugriffe in ProblemController@profile auslagern und als View-Variable übergeben. Den @php-Block in der View auf rein präsentationsbezogene Logik (Labels, Formatierungen) reduzieren. Alternativ ein TicketProfileViewComposer anlegen.

### 🟠 EmployeeController ist ein 3521-Zeilen Fat Controller mit 47 Methoden und 244 DB-Abfragen  
**Modul:** Personal / HR · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Employee/EmployeeController.php:1`  
**Problem:** Der Controller enthält Mitarbeiterlisten, Profil-Update, CV-Generierung, Passcode-Reset, Kapazitäts-Analyse, Department-Zuordnungen, Positions-Main-Flag, Kalender-API, Effizienz-Score-Berechnung, Delegations-Logik u.v.m. — alles in einer einzigen Klasse ohne Service-Layer. Die next_employee()-Methode (Zeile 408) feuert über 15 DB-Abfragen zur Vorbereitung einer einzelnen View. Keine FormRequest-Klassen, keine Repository-Schicht.  
**Fix:** Aufteilen in spezialisierte Service-Klassen (EmployeeProfileService, EmployeeCalendarService) und FormRequest-Klassen (CreateEmployeeRequest, UpdateEmployeeProfileRequest). Datenbankzugriffe in Repository- oder Query-Klassen auslagern.

### 🟠 N+1-Queries in MachineController::machineResource() – 3 Extra-Queries pro Maschine  
**Modul:** Lager · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Inventory/MachineController.php:313-315`  
**Problem:** machineResource() wird per map() über jede paginierte Maschine aufgerufen (Zeile 84). Es werden darin 3 separate Queries abgesetzt: AssetInstallment::pluck(), AssetInstallment::sum() und InstallmentPayment::sum(). Bei einer Seitengröße von 12 Maschinen = 37 zusätzliche Datenbankabfragen pro Seitenaufruf, obwohl data() bereits withCount/withSum eager-lädt.  
**Fix:** Installment-Summen in der machineQuery() via leftJoinSub oder withSum eagern und die drei Einzelqueries aus machineResource() entfernen.

### 🟠 SQL direkt in Blade-Template: overview.blade.php (5 DB::table() in foreach-Schleife)  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/new_leads/overview/overview.blade.php:545`  
**Problem:** Innerhalb eines @foreach über $product_list (L457) werden pro Iteration 5 separate DB::table()-Abfragen abgesetzt: lead_alternative_adds (L545), planings (L577), offers (L610), deals (L643), projects (L676) – klassisches N+1-Problem. Bei 100 Produkten = 500 zusätzliche Queries pro Seitenaufruf. Datenzugriff ist direkt im Template, völlig unkontrolliert und nicht cachebar.  
**Fix:** Alle benötigten Daten im Controller vorladen (eager loading oder ein einziger JOIN-Query). Das Blade erhält nur fertig vorbereitete Kollektionen. Alternativ einen LeadProductStatusService mit batch-Abfrage einführen.

### 🟠 SQL direkt in Blade-Template: appointment_edit.blade.php (19 DB::table-Aufrufe)  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/todo/appointment/appointment_edit.blade.php:249`  
**Problem:** Die Blade-Datei führt 19 direkte DB::table()-Abfragen aus, darunter Lookups auf main_appointment_employees, new_leads, brands, distributors mit DB::raw()-Fragmenten. Geschäftslogik und Datenbankzugriff in der Präsentationsschicht – untestbar, schwer wartbar, kein Caching möglich.  
**Fix:** Alle Datenabfragen in den zugehörigen Controller auslagern. Controller-Methode gibt $appointment mit eager-geladenen Relations zurück. Im Blade nur noch {{ $appointment->contact->name }} statt DB::table('new_leads')->where(...).

### 🟠 Geschäftslogik in Blade: sets.blade.php berechnet Prozentwerte per DB-Query  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/offer/set/sets/sets.blade.php:46`  
**Problem:** @php-Block direkt in Blade fragt DB::table('product_master_sets')->...->first() für Prozentwerte ab und DB::table('asset_sets')->sum('total_price') für Gesamtsummen. Preisberechnungs-Logik im Template, ohne Testmöglichkeit und mit verstecktem N+1-Risiko.  
**Fix:** Controller übergibt $masterSet (eager-loaded) mit bereits berechneten Werten. Berechnungslogik in einen MasterSetPricingService auslagern.

### 🟠 Massiver Blade-Monolith: offer/config.blade.php (25.064 Zeilen)  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/offer/configuration/offer/config.blade.php:1`  
**Problem:** Eine einzelne Blade-Datei mit 25.064 Zeilen ist schlicht nicht wartbar. Selbst ohne direkte DB-Abfragen (2 @php-Blöcke, 0 DB::) enthält die Datei komplexe JavaScript-Logik, multiple modale Fenster, Formular-Gruppen, Konfigurations-Sektionen und Berechnungslogik, alles unstrukturiert in einer Datei.  
**Fix:** Aufteilen in Blade-Komponenten (@include oder <x-component>): offer-header, offer-sections, offer-items, offer-pricing, offer-modals. Jede Komponente max. 300–500 Zeilen. JavaScript in externe .js-Dateien auslagern.

### 🟠 DealController: 752 Zeilen protected Helper-Methoden ohne Service-Extraktion  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/Deal/DealController.php:34`  
**Problem:** Direkt nach dem Konstruktor folgen 19 protected-Methoden (dealActivitySqlParts, dealLatestChangeAtSql, dealLatestChangeSourceSql, baseDealQuery, applyDealFilters, sidebarData, dealStageCandidateKeys, syncDealWorkflowTargets, statsFromQuery u.a.) mit insgesamt 752 Zeilen. Diese SQL-Generierungs- und Workflow-Logik gehört nicht in eine Controller-Klasse. profileUpdateStatus hat 559 Zeilen (L3063), planningStore 547 Zeilen (L2225).  
**Fix:** DealQueryBuilder oder DealRepository für SQL-Generierung, DealWorkflowService für Stage/Status-Logik. Die protected-Methoden können 1:1 in Service-Klassen verschoben werden.

### 🟡 Hardcoded externer n8n-Webhook-URL in Blade-View  
**Modul:** CRM – Kommunikation · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/lead_email/inbox/index.blade.php:1059`  
**Problem:** Die URL https://sadid2024.app.n8n.cloud/webhook-test/email-leads?id=${id} ist direkt im JavaScript des Blade-Templates hardcodiert. Sie zeigt auf einen 'webhook-test'-Endpunkt (kein Produktions-Webhook) und ist nicht konfigurierbar. Wenn sich die n8n-URL ändert, muss der View-Code bearbeitet werden. Der AI-Verify-Flow sendet außerdem zuerst eine Anfrage an /lead/email/api/{id} (die nicht authentifizierte Route), deren Antwort vollständig ignoriert wird.  
**Fix:** URL in eine .env-Variable (N8N_WEBHOOK_URL) auslagern und per @json(config('services.n8n.url')) in den View übergeben. Den webhook-test-Endpunkt durch den produktiven Webhook ersetzen.

### 🟡 sharedListData() laedt saemtliche Stammdaten fuer jede Liste ohne Caching  
**Modul:** CRM – Anfragen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Inquiry/InquiryController.php:349-405`  
**Problem:** sharedListData() laedt bei jedem Listenaufruf (index, customer, my_inquiry, junk_list, published, deleted_list) vollstaendige Tabellen: InquiryType::all(), Employee::select(...)->get(), Department::get(), Branch::get(), DB::table('article_groups')->get(), DB::table('phase_sections')->get() sowie einen LEFT-JOIN ueber inquiry_product_lists fuer alle IDs der aktuellen Seite. Diese 6+ Queries werden ausschliesslich fuer Filter-Dropdowns benoetigt, die sich selten aendern. Bei grossem Stammdatenbestand belastet das jede Seitenladung erheblich.  
**Fix:** Stammdaten (Types, Employees, Departments, Products) mit Cache::remember() fuer 5–15 Minuten cachen. Produktliste fuer die aktuelle Seite (productQuery) ist seitenspezifisch und muss frisch bleiben.

### 🟡 ExternalPersonalController und ExternalDepartmentsController ohne FormRequest, mit DB-Facade  
**Modul:** CRM – Partner · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Product/Brand/ExternalPersonalController.php:25,71 / ExternalDepartmentsController.php:74`  
**Problem:** ExternalPersonalController nutzt DB::table() statt Eloquent-Scopes, LogInfo-Aufrufe für Debugging bleiben produktiv aktiv (Zeile 69), und store() verwendet $request->all() direkt an das Model. ExternalDepartmentsController hat keine Validierung in update() und ebenfalls direkte DB-Facade-Aufrufe. Im Vergleich zeigen BrandDepartmentController und DistributorDepartmentController deutlich bessere Strukturierung mit vollständiger Validierung.  
**Fix:** FormRequest-Klassen für ExternalPersonal und ExternalDepartments erstellen. Debug-Log-Aufruf entfernen. DB::table()-Aufrufe durch Eloquent-Methoden ersetzen.

### 🟡 Geschäftslogik (DB-Queries) im Blade-Partial note-item.blade.php  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/deal/partials/note-item.blade.php:5–8`  
**Problem:** Das Partial führt im @php-Block per DB::table('employees')->where('id', $note->created_by)->first() eine Datenbankabfrage aus. Jede gerenderte Notiz (inkl. Replies) feuert eine zusätzliche Query. Beim rekursiven Include für Antworten potenziert sich der Effekt.  
**Fix:** Mitarbeiter-Daten für alle Notiz-Autoren im Controller per whereIn vorladen und als $employeeMap an das Partial übergeben. Das Partial greift dann nur noch auf $employeeMap[$note->created_by] zu.

### 🟡 DealController ist ein Fat Controller (3623 Zeilen) mit eingebetteter SQL-Generierung  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Deal/DealController.php:51–215`  
**Problem:** Der Controller enthält 3623 Zeilen und übernimmt: Listen-/Kanban-/Filter-/Stats-Logik, rohe SQL-Fragment-Generierung (dealActivitySqlParts, dealLatestChangeAtSql etc.), Workflow-Stage-Auflösung, Note/File/Document/Planning/History-Endpunkte, Employee-Scoring und Planungslogik. Komplexe CASE-Ausdrücke und GREATEST()-Konstrukte werden als PHP-Strings zusammengebaut. Keine Service-Klasse, kein Repository, keine FormRequests.  
**Fix:** Extrahieren: DealWorkflowService (Stage-Mapping/-Normalisierung), DealActivityService (SQL-Fragment-Generierung), DealFilterService (applyDealFilters), DealStatsService. FormRequest-Klassen für updateStatus, storeCustomerNote. Repository oder QueryBuilder-Klasse für baseDealQuery.

### 🟡 users.name-Feld als Employee-ID missbraucht – systemweites Designproblem im Modul sichtbar  
**Modul:** Support – Tickets · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Ticket/ProblemController.php:51,277,472,681,1166,1330,1946`  
**Problem:** An 14+ Stellen im ProblemController wird auth()->user()->name als Employee-ID interpretiert: $employeeId = (int) auth()->user()->name. Die User.php-Dokumentation bestätigt dies: 'users.name seems to store employees.id'. Dieses Design macht Name-Felder für Anzeigezwecke unbrauchbar, verhindert einen sauberen Auth-User/Employee-Split und ist fehleranfällig (z.B. wenn users.name einen echten Namen statt einer Zahl enthält).  
**Fix:** Ein dediziertes employee_id-Feld zur users-Tabelle hinzufügen (Migration + Beziehung). Alle (int) auth()->user()->name-Aufrufe durch auth()->user()->employee_id oder auth()->user()->employee->id ersetzen. Diesen Fix mit einer Datenmigration begleiten.

### 🟡 Fat Controller und Monster-Views: ProductController (1964 Z.) und MasterSetController (2464 Z.)  
**Modul:** Artikel · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Product/ProductController.php:1-1964 / app/Http/Controllers/Product/MasterSet/MasterSetController.php:1-2464`  
**Problem:** Beide Controller enthalten Geschäftslogik, Duplikations-Hilfsmethoden, Formatter-Closures und Datenbank-Abfragen direkt. Es gibt keine Service-Klasse, kein Repository und keine FormRequest-Klassen außer inline-Validierung. Dasselbe gilt für die Views: master_sets/index.blade.php hat 15.270 Zeilen mit 6 <style>- und 9 <script>-Blöcken; product.blade.php hat 6.252 Zeilen mit 4 <style>-Blöcken.  
**Fix:** Extrahieren: ProductService für History/Duplikat-Logik, ProductUpdateRequest / ProductStoreRequest für Validierung, Blade-Komponenten oder @include-Partials für wiederverwendbare UI-Blöcke. MasterSetController in Methoden-Controller aufteilen.

### 🟡 DB::table()-Abfragen direkt im Blade (branch_rent.blade.php)  
**Modul:** Finanzen · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/expense/rent/branch_rent.blade.php:26,35`  
**Problem:** Die View führt zwei DB::table()-Abfragen direkt im @php-Block aus (branch_expenses und branch_rents per request()->expense). Datenbanklogik gehört in Controller oder Service, nicht in Templates. Fehlerbehandlung fehlt; ein ungültiger Query-Parameter führt zu einem Null-Fehler.  
**Fix:** Die Abfragen in den zugehörigen Controller auslagern und die Daten per compact()/with() an die View übergeben.

### 🟡 TaskPhaseController ist ein Fat Controller mit 1697 Zeilen und vermischter Verantwortung  
**Modul:** Admin & System · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Phase/TaskPhaseController.php:1-1697`  
**Problem:** Der Controller enthält CRUD für TaskPhase, Duplikat-Bereinigung für PhaseSections, Master-Set-Verknüpfungslogik, Baum-Traversal (descendantIds, duplicateActivityTree), Transfer- und Resequencing-Algorithmen sowie mehrere AJAX-Datenabruf-Endpunkte. Geschäftslogik (Baum-Duplikation, Cleanup-Algorithmen) gehört in Service-Klassen. Zusätzlich: 10 Debug-Log::info()-Aufrufe mit vollen Request-Daten sind in der Produktion aktiv.  
**Fix:** Service-Klassen extrahieren (z. B. PhaseTransferService, PhaseSectionCleanupService). Log::info-Aufrufe auf Log::debug umstellen oder entfernen.

### 🟡 UserController: 17 DB-Writes ohne eine einzige DB::transaction()  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/User/UserController.php:383`  
**Problem:** 17 ->save()-Aufrufe verteilt über den Controller, ohne Transaktionsschutz. Konkret: L383 $user->save() und L387 $employee->save() direkt hintereinander ohne Transaktion. Wenn $user gespeichert wird, aber $employee->save() fehlschlägt (z.B. wegen Constraint), ist der User mit neuem Bild gespeichert, Employee aber nicht aktualisiert.  
**Fix:** Jeden Bereich mit >1 DB-Write in DB::transaction() einwickeln. Für das Profilbild-Update (L382-388) mindestens: DB::transaction(function() use ($user, $employee, $imageName) { $user->save(); $employee?->save(); }).

### 🟡 Enge Kopplung: NewLeadsController importiert 53 Model-Klassen direkt  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:6`  
**Problem:** 53 use App\Models\*-Imports in einem Controller: NewLeads, Employee, Product, ArticleGroup, NewLeadImage, LeadProductList, LeadAlternativeAdd, CustomerAlternativeAdd, Leave, JobRepresentative, CustomerResponsible, NewLeadResponsibility, Image, CustomerProductList, Department, Customer, Branch, ImageCategory, Inquiry, Planing und weitere. Jede Model-Änderung kann alle 121 Controller-Methoden betreffen. Maximale strukturelle Kopplung.  
**Fix:** Service-Klassen als Intermediäre einführen. Controller kennt nur seinen direkten Service, nicht alle dahinterliegenden Models. Dependency Injection per Konstruktor statt statischer Model-Aufrufe.

### 🟡 FusionFormSubmissionController: 514 Zeilen, 2 Writes, keine Validierung, keine Transaktion  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Wordpress/FusionFormSubmissionController.php:1`  
**Problem:** Verarbeitet externe WordPress-Formular-Einreichungen (L418: $inquiry->save(), L507: $inquiry->save()) ohne jegliche $request->validate()-Aufruf und ohne FormRequest. Beliebige externe Eingaben werden direkt in der Datenbank gespeichert. Kein Transaktionsschutz.  
**Fix:** Sofort StoreFusionFormRequest mit Validierungsregeln für alle erwarteten Felder einführen. Writes in DB::transaction() kapseln. Sensible Felder mit sanitize()-Aufrufen schützen.

### 🟡 Model-Bloat: Employee.php (485 Zeilen, 80+ Methoden) – alles in einem Model  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Models/Employee.php:1`  
**Problem:** 485 Zeilen mit über 60 hasMany/belongsTo-Relationen (masterSetCarts, appointments, personalTasks, salaries, leaves, machineServicesProvided, machineFaultsDetected, createdInvoices, uploadedInvoiceFiles...) plus Business-Methoden (initials(), getDisplayNameAttribute()). Das Model kennt buchstäblich jeden anderen Teil der Anwendung und ist ein God-Object.  
**Fix:** Nicht die Relationen entfernen, aber häufig verwendete Query-Kombinationen in EmployeeRepository oder EmployeeQueryService auslagern. Computed-Attribute (initials, displayName) in einen EmployeePresenter.

### 🟡 LeadOverviewController::search() – 987-Zeilen-Methode ohne Service, mit 6 DB::table()-Calls  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Kanban/LeadOverviewController.php:967`  
**Problem:** 987-Zeilen-Suchmethode ohne Transaction, 6 direkte DB::table()-Aufrufe, kein Service. Die gesamte Lead-Suchlogik (Kanban-Filter, Paginierung, Mitarbeiter-Filter, Stage-Filter, Datums-Ranges) ist inline. Parallel dazu moveStageWorkflow (968L, L4824) mit 8 DB-Writes aber mit korrekter Transaktion.  
**Fix:** LeadSearchService mit search(array $filters): Collection extrahieren. DB::table()-Aufrufe durch Eloquent-Scopes ersetzen (scopeByStage, scopeByEmployee, scopeByDateRange).

### 🟡 Blade-Dateien mit bis zu 25.000 Zeilen - schwere initiale HTML-Payload  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/offer/configuration/offer/config.blade.php:1-25064, resources/views/admin/new_leads/customer_profile.blade.php:1-23145, resources/views/admin/offer/old/config.blade.php:1-21284`  
**Problem:** Die drei größten Blades sind 21.000-25.000 Zeilen lang und rendern trotz konditioneller Abschnitte die meisten Bereiche im initialen HTML-Durchlauf. Das verlangsamt die Server-Render-Zeit (Blade-Compilation + Template-Rendering) und erhöht die initiale HTML-Größe für den Browser erheblich.  
**Fix:** Große Sektionen in @include-Partials aufteilen. Lazy-Rendering für Tabs und Modals: Inhalt erst per AJAX-Endpoint laden wenn der Tab geöffnet wird (analog zum bestehenden Kanban-Muster). Blade-Templates mit php artisan view:cache in Production vorab kompilieren.

### 🟡 Fehlende Route-Cache-Datei bei 5.400 Zeilen routes/web.php  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:1-5412, bootstrap/cache/`  
**Problem:** Die Datei routes/web.php enthält über 5.400 Zeilen und mehr als 2.500 Route-Definitionen. Im bootstrap/cache/-Verzeichnis wurden keine vorkompilierten routes-*.php-Dateien gefunden. Laravel muss deshalb bei jedem Request alle 2.500+ Routen parsen und matchen, was mehrere Millisekunden Overhead pro Request bedeutet.  
**Fix:** In Production/Staging php artisan route:cache und php artisan config:cache ausführen und in die Deployment-Pipeline aufnehmen. Routen in thematische Dateien (z. B. routes/planner.php, routes/inquiry.php) auslagern und in RouteServiceProvider registrieren.

### ⚪ 37 Schema::hasTable/hasColumn-Aufrufe im DealController in Hot-Paths  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** niedrig · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Deal/DealController.php:59–208 (dealActivitySqlParts)`  
**Problem:** dealActivitySqlParts() und verwandte Methoden rufen Schema::hasTable/hasColumn 37 Mal auf. Diese Methoden führen bei jedem Request INFORMATION_SCHEMA-Abfragen aus (wenn kein Schema-Cache vorhanden), was in Produktionsumgebungen ohne Schema-Caching spürbar die Latenz erhöht. Die Methode wird bei jedem index/all/junk_list/delete_list-Aufruf eingesetzt.  
**Fix:** Schema-Checks in einen gecachten Service auslagern (z.B. app('schema.cache')->hasTable()), oder eine einmalige Konfigurationsprüfung beim App-Start. In Laravel kann php artisan config:cache / Route::cache genutzt werden; für Schema-Checks empfiehlt sich Cache::remember('schema_tables', 3600, ...).

### ⚪ Blade-Monolithen: customer_profile.blade.php (23.145L) und new_leads/layouts/profile.blade.php (12.352L)  
**Modul:** Querschnitt · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/new_leads/customer_profile.blade.php:1`  
**Problem:** customer_profile.blade.php hat 23.145 Zeilen (6 @php-Blöcke, 2 DB-Aufrufe). new_leads/layouts/profile.blade.php hat 12.352 Zeilen (11 @php-Blöcke). Beide sind nicht wartbare Monolithen, die trotz @include- bzw. @php-Fragmentierung keine echte Komponenten-Trennung kennen.  
**Fix:** Blade-Komponenten-Architektur einführen: <x-customer.profile-header />, <x-customer.product-list />, <x-customer.notes-section /> etc. Jede Komponente kapselt ihre Logik und ihr Template.
