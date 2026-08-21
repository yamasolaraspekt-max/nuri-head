# Fix-Ledger — alle 131 bestätigten schweren Funde

Status: ⬜ offen · ✅ behoben · ⏸️ zurückgestellt · ❓ Entscheidung nötig

| ID | Kat. | Sev | Regel | Ort | Titel | Status |
|--:|---|---|---|---|---|:--:|
| 1 | refactor | kritisch | architektur | `app/Http/Controllers/Customer/NewLeadsController.php:1` | Fat Controller Extrem: NewLeadsController (14.049 Zeilen, 121 Methoden | ⬜ |
| 2 | refactor | kritisch | architektur | `app/Http/Controllers/:1` | Keine FormRequest-Klassen im gesamten Projekt (0 von 380 Controllern) | ⬜ |
| 3 | refactor | kritisch | architektur | `app/Http/Controllers/:1` | Fehlende Service-/Repository-Schicht: 374 von 380 Controllern ohne Ser | ⬜ |
| 4 | security | kritisch | blade | `resources/views/admin/appointments/show.blade.php:1635` | Stored XSS: {!! $r->report !!} rendert Benutzer-HTML ungesanitized | ⬜ |
| 5 | security | kritisch | crud | `app/Http/Controllers/Task/GeneralTaskController.php:161` | Fehlende Autorisierung: Jeder angemeldete User kann jede GeneralTask b | ⬜ |
| 6 | bug | kritisch | kausalitaet | `app/Http/Controllers/EmployeeDashboardController.php:683-685` | getTabCounts() referenziert undefinierte Klassen Task und Appointment | ⬜ |
| 7 | bug | kritisch | kausalitaet | `app/Http/Controllers/Inquiry/InquiryController.php:1360` | update() setzt Status jeder Anfrage zwingend auf 'Unpublished' – verif | ⬜ |
| 8 | bug | kritisch | kausalitaet | `app/Http/Controllers/Contacts/AllContactController.php:994,1001` | Restore-Methoden auf Brand und Distributor ohne SoftDeletes-Trait – fa | ⬜ |
| 9 | bug | kritisch | kausalitaet | `app/Http/Controllers/Employee/Profile/LeaveController.php:230-258` | Leave-Genehmigung (approve) reduziert employees.remaining_day nicht | ⬜ |
| 10 | bug | kritisch | kausalitaet | `app/Http/Controllers/Employee/Position/QualificationController.php:102-118` | QualificationController::destroy() löscht FurtherEducation statt Quali | ⬜ |
| 11 | bug | kritisch | kausalitaet | `app/Http/Controllers/Customer/BegFundingsController.php:49` | BegFundingsController::store() referenziert nicht-existierende Klasse  | ⬜ |
| 12 | bug | kritisch | kausalitaet | `app/Http/Controllers/Admin/GarbageController.php:29` | GarbageController prüft Berechtigung gegen falsche Spalte (user->name  | ⬜ |
| 13 | bug | kritisch | kausalitaet | `app/Http/Middleware/isAdmin.php:22-29` | isAdmin-Middleware joiniert users.name (employee_id) mit user_rolls.us | ⬜ |
| 14 | bug | kritisch | kausalitaet | `app/Http/Controllers/EmployeeDashboardController.php:1821-1848` | Zweistufige N+1-Query-Schleife beim Laden der Kundenliste im Dashboard | ⬜ |
| 15 | consistency | kritisch | konsistenz | `app/Http/Controllers/EconomicCalculationController.php:84, resources/views/admin/customer_economic_calculation/economic_calculation/create.blade.php:117` | Flash-Message-Key 'update_msg' vs. 'updated_msg' – Silent-Fail in 143  | ⬜ |
| 16 | security | kritisch | plausibilitaet | `resources/views/admin/lead_email/email_config/account.blade.php:276 / app/Http/Controllers/Email/LeadEmailAccountsController.php:118` | IMAP-Passwörter im Klartext in DB und HTML gespeichert und exponiert | ⬜ |
| 17 | plausibility | kritisch | plausibilitaet | `app/Http/Controllers/Customer/NewLeadsController.php:6411` | Undefinierte Variable $oldValue im Audit-Log führt zu PHP-Fehler | ⬜ |
| 18 | cleanup | kritisch | redundanz | `app/Http/Controllers/Old/` | 40 tote Controller in app/Http/Controllers/Old/ - vollstaendig unrefer | ⬜ |
| 19 | cleanup | kritisch | redundanz | `resources/views/admin/` | 107 'blade copy'-Dateien in 25 Old-Code-Verzeichnissen in resources/vi | ⬜ |
| 20 | security | kritisch | routing | `routes/web.php:599` | Öffentliche Route gibt LeadEmail-Inhalte ohne Authentifizierung zurück | ⬜ |
| 21 | security | kritisch | routing | `routes/web.php:4003,4037,4057,4668` | 4 Routen verweisen auf nicht existente Controller-Methoden (500) | ⬜ |
| 22 | security | kritisch | routing | `routes/web.php:4002,4003,4004,4005,4006,4009,4107` | GET-Routen fuer destruktive Operationen (Delete, Junk, Restore, Verify | ⬜ |
| 23 | security | kritisch | routing | `routes/web.php:4609-4615` | AllContactController ohne Auth-Middleware: alle Kontakte und Export öf | ⬜ |
| 24 | security | kritisch | routing | `app/Http/Controllers/Customer/Offer/OfferCommentController.php:1-57` | OfferCommentController ohne Auth-Middleware – alle Endpoints unauthent | ⬜ |
| 25 | security | kritisch | routing | `routes/web.php:1539,1687,1698,2829,2836,1610` | Destruktive Operationen über GET-Routen (CSRF-Bypass, sofortige Löschu | ⬜ |
| 26 | security | kritisch | routing | `routes/web.php:1810-1815` | Leave-Notiz-Endpunkte ohne Auth-Middleware öffentlich erreichbar | ⬜ |
| 27 | security | kritisch | routing | `routes/web.php:2278,2310,2475,2844` | DELETE-Aktionen über GET-Routen CSRF-ungeschützt | ⬜ |
| 28 | security | kritisch | routing | `routes/web.php:2275` | is_Admin-Middleware wird im Produkt-Route-Group nicht angewandt | ⬜ |
| 29 | security | kritisch | routing | `routes/web.php:2696, 2716` | PurchaseRequest- und RequestOut-Routen ohne Auth-Middleware | ⬜ |
| 30 | security | kritisch | routing | `routes/web.php:5096` | GoodsReceipt-Routen ohne Auth-Middleware | ⬜ |
| 31 | security | kritisch | routing | `routes/web.php:860` | BEG-Förderungen ohne Auth-Middleware (unauthentifizierter Zugriff) | ⬜ |
| 32 | security | kritisch | routing | `routes/web.php:1444` | Middleware-Typo: 'middlware' statt 'middleware' — Auth-Schutz komplett | ⬜ |
| 33 | security | kritisch | routing | `routes/web.php:3961` | GET /route-cache führt Artisan-Befehle aus — kein Auth-Schutz | ⬜ |
| 34 | security | kritisch | routing | `routes/web.php:404` | GET /fix-notes schreibt Datenbankdaten ohne Authentifizierung | ⬜ |
| 35 | refactor | kritisch | workflow | `app/Http/Controllers/Planner/PlannerPlanController.php:861-1111` | O(N) updateOrCreate + O(N) Kind-Query pro Entity in syncAndLoad | ⬜ |
| 36 | refactor | hoch | architektur | `app/Http/Controllers/EmployeeDashboardController.php:1` | Monster-Controller: EmployeeDashboardController hat 2333 Zeilen | ⬜ |
| 37 | refactor | hoch | architektur | `app/Http/Controllers/Report/OverdueCenterController.php:1` | OverdueCenterController hat 4612 Zeilen – extremes Fat-Controller-Prob | ⬜ |
| 38 | refactor | hoch | architektur | `app/Http/Controllers/Chat/ChatController.php:123-143 / 185-232` | N+1-Queries bei Chat-Sidebar: 2 Queries pro User und 2 Queries pro Gru | ⬜ |
| 39 | refactor | hoch | architektur | `app/Http/Controllers/Inquiry/InquiryController.php:1075-1199,1364-1491` | Fat Controller: store() und update() enthalten vollstaendige Terminpla | ⬜ |
| 40 | refactor | hoch | architektur | `app/Http/Controllers/Customer/NewLeadsController.php:1` | Extremer Fat-Controller: 14.049 Zeilen, 80+ public-Methoden in einer K | ⬜ |
| 41 | refactor | hoch | architektur | `app/Http/Controllers/Customer/Offer/OfferController.php:1 und app/Http/Controllers/Customer/Offer/OfferFolderController.php:1` | Fat Controllers: OfferController (2929 Z.) und OfferFolderController ( | ⬜ |
| 42 | refactor | hoch | architektur | `resources/views/admin/offer/folder-show.blade.php:54-196` | Geschäftslogik (DB-Queries, Model-Zugriffe) direkt im @php-Block des B | ⬜ |
| 43 | refactor | hoch | architektur | `resources/views/admin/deal/customer_view.blade.php:1–4823` | Monster-View: customer_view.blade.php mit 2017 Zeilen Inline-CSS und 1 | ⬜ |
| 44 | refactor | hoch | architektur | `app/Http/Controllers/Planner/PlannerPlanController.php:1` | Extremer Fat Controller: PlannerPlanController hat 11.080 Zeilen und 6 | ⬜ |
| 45 | refactor | hoch | architektur | `resources/views/admin/todo/personal/task_details.blade.php:227` | Geschäftslogik und DB-Queries direkt im Blade-Template (task_details.b | ⬜ |
| 46 | refactor | hoch | architektur | `resources/views/admin/problem/profile.blade.php:132-281` | Datenbankabfragen in profile.blade.php (Geschäftslogik in der View) | ⬜ |
| 47 | refactor | hoch | architektur | `app/Http/Controllers/Employee/EmployeeController.php:1` | EmployeeController ist ein 3521-Zeilen Fat Controller mit 47 Methoden  | ⬜ |
| 48 | refactor | hoch | architektur | `app/Http/Controllers/Inventory/MachineController.php:313-315` | N+1-Queries in MachineController::machineResource() – 3 Extra-Queries  | ⬜ |
| 49 | refactor | hoch | architektur | `resources/views/admin/new_leads/overview/overview.blade.php:545` | SQL direkt in Blade-Template: overview.blade.php (5 DB::table() in for | ⬜ |
| 50 | refactor | hoch | architektur | `resources/views/admin/todo/appointment/appointment_edit.blade.php:249` | SQL direkt in Blade-Template: appointment_edit.blade.php (19 DB::table | ⬜ |
| 51 | refactor | hoch | architektur | `resources/views/admin/offer/set/sets/sets.blade.php:46` | Geschäftslogik in Blade: sets.blade.php berechnet Prozentwerte per DB- | ⬜ |
| 52 | refactor | hoch | architektur | `resources/views/admin/offer/configuration/offer/config.blade.php:1` | Massiver Blade-Monolith: offer/config.blade.php (25.064 Zeilen) | ⬜ |
| 53 | refactor | hoch | architektur | `app/Http/Controllers/Customer/Deal/DealController.php:34` | DealController: 752 Zeilen protected Helper-Methoden ohne Service-Extr | ⬜ |
| 54 | ui | hoch | blade | `resources/views/admin/dashboard/employee/mobile.blade.php:1` | mobile.blade.php ist ein 13.425-Zeilen-Monster mit massivem Inline-CSS | ⬜ |
| 55 | security | hoch | blade | `resources/views/admin/lead_email/inbox/index.blade.php:1033` | E-Mail-Body wird unbereinigt als innerHTML in den DOM injiziert (XSS) | ⬜ |
| 56 | security | hoch | blade | `resources/views/admin/new_leads/layouts/context-feed/tasks.blade.php:79,137,153` | Unescapiertes {!! !!} bei User-generierten Inhalten in Context-Feed-Vi | ⬜ |
| 57 | security | hoch | blade | `resources/views/admin/offer/folder-show.blade.php:5943 und :6282` | XSS durch unescaptes {!! !!} bei nutzergenerierten HTML-Feldern | ⬜ |
| 58 | security | hoch | blade | `resources/views/admin/problem/profile.blade.php:2090,2174,2328` | XSS durch {!! !!} bei nicht sanitizierten Nutzerdaten | ⬜ |
| 59 | security | hoch | blade | `resources/views/admin/problem/problem_edit.blade.php:1254` | XSS durch {!! !!} für Quill-Editor-Prefill in problem_edit | ⬜ |
| 60 | security | hoch | blade | `resources/views/admin/product/product/product_details.blade.php:1352` | XSS durch {!! !!} bei nutzergesteuertem short_description-HTML | ⬜ |
| 61 | security | hoch | crud | `app/Http/Controllers/Inquiry/InquiryController.php:1335,1515` | update() und destroy() pruefen keine Berechtigungen – jeder authentifi | ⬜ |
| 62 | security | hoch | crud | `app/Http/Controllers/Customer/Deal/DealInvoiceController.php:202–213` | DealInvoice update() und destroy() sind leere Stubs – kein Update/Dele | ⬜ |
| 63 | security | hoch | crud | `app/Http/Controllers/Product/ProductController.php:1723-1727` | saveDistributorData() fügt Preiszeilen nur ein, ohne bestehende zu lös | ⬜ |
| 64 | bug | hoch | kausalitaet | `app/Http/Controllers/Email/LeadEmailReaderController.php:17-18 / routes/web.php:1388` | GET-Route lead.email.fetch triggert synchronen IMAP-Abruf mit set_time | ⬜ |
| 65 | bug | hoch | kausalitaet | `app/Http/Controllers/Chat/ChatController.php:67 / 1217` | users.name wird als Fremdschlüssel zu employees.id verwendet – semanti | ⬜ |
| 66 | bug | hoch | kausalitaet | `app/Http/Controllers/Customer/NewLeadsController.php:628,1064,3244,3261` | Status-String-Inkonsistenz: Store setzt einfaches Leerzeichen, Filter  | ⬜ |
| 67 | bug | hoch | kausalitaet | `resources/views/admin/employee/external/external.blade.php:63` | Neu-Anlegen-Modal in external.blade sendet an external.update statt ex | ⬜ |
| 68 | bug | hoch | kausalitaet | `app/Http/Controllers/Product/Brand/ExternalDepartmentsController.php:27-34` | ExternalDepartmentsController: orWhere ohne Gruppierung umgeht ID-Filt | ⬜ |
| 69 | bug | hoch | kausalitaet | `app/Http/Controllers/Customer/Offer/OfferController.php:1354-1386` | Komplettes Löschen (delete_type=complete) löscht keine physischen Date | ⬜ |
| 70 | bug | hoch | kausalitaet | `app/Http/Controllers/Employee/EmployeeController.php:653` | EmployeeController::update() liest ID aus $_POST direkt statt Route Mo | ⬜ |
| 71 | bug | hoch | kausalitaet | `app/Http/Controllers/Product/ProductController.php:1067-1088` | Wizard-Stage 1 (Produkteigenschaft) speichert keine Felder | ⬜ |
| 72 | bug | hoch | kausalitaet | `app/Http/Controllers/Inventory/AssetInstallmentController.php:94, 172` | Fehlermeldung bei Store/Update-Fehler zeigt Erfolgsmeldung | ⬜ |
| 73 | bug | hoch | kausalitaet | `app/Http/Controllers/Inventory/AssetInstallmentController.php:109-112` | SQL-Logikfehler in AssetInstallmentController::show() – type-Filter wi | ⬜ |
| 74 | bug | hoch | kausalitaet | `app/Http/Controllers/Customer/BegFundingsController.php:19,66` | BEG-Förderungen: create()- und edit()-Views existieren nicht (ViewNotF | ⬜ |
| 75 | bug | hoch | kausalitaet | `app/Http/Controllers/Phase/TaskPhaseController.php:453` | TaskPhaseController::clone() referenziert undefinierte Klasse TaskSubT | ⬜ |
| 76 | bug | hoch | kausalitaet | `app/Http/Controllers/Phase/TaskPhaseController.php:352-364` | TaskPhaseController::storeNewPhase() erstellt unbeabsichtigt einen Pro | ⬜ |
| 77 | bug | hoch | kausalitaet | `app/Http/Controllers/Planner/PlannerPlanController.php:286-294` | N einzelne INSERTs statt Batch-Insert in syncPlannerItemEmployees | ⬜ |
| 78 | bug | hoch | kausalitaet | `app/Http/Controllers/Report/OverdueCenterController.php:1050-1060` | N firstOrCreate-Aufrufe beim Speichern eines Overdue-Reports (ein Eint | ⬜ |
| 79 | bug | hoch | kausalitaet | `app/Http/Controllers/Planner/PlannerPlanController.php:182,247,470,562,612 (145 Stellen)` | 145 Schema::hasTable/hasColumn-Aufrufe ohne Request-Caching in Planner | ⬜ |
| 80 | consistency | hoch | konsistenz | `app/Http/Controllers/Customer/NewLeadsController.php:3440,4552,4859,973,1979` | auth()->user()->name wird als Employee-ID misbraucht – semantisch fals | ⬜ |
| 81 | consistency | hoch | konsistenz | `resources/views/admin/todo/personal/task_details.blade.php:265` | PersonalTask-Status-Werte inkonsistent zwischen View und Controller | ⬜ |
| 82 | consistency | hoch | konsistenz | `database/migrations/2023_06_22_085602_create_products_table.php:33 vs app/Http/Controllers/Product/ProductController.php:740,760,1038` | Status-Werte inkonsistent: DB-Default 'active', Code nutzt 'Published' | ⬜ |
| 83 | consistency | hoch | konsistenz | `app/Http/Controllers/ (app-weit)` | Inkonsistente Flash-Message-Keys: 7 verschiedene Schlüsselnamen nebene | ⬜ |
| 84 | consistency | hoch | konsistenz | `app/Http/Controllers/PlaningController.php:193, app/Http/Controllers/PlaningController.php:222, app/Http/Controllers/ToolsController.php:110–117, app/Http/Controllers/HandoverController.php:144` | 'delete_msg' für Error-Szenarien missbraucht – semantisch falsch | ⬜ |
| 85 | consistency | hoch | konsistenz | `resources/views/admin/product/brand/brand.blade.php:1072–1080, resources/views/admin/supplier-connectors/index.blade.php:389–395, resources/views/admin/configurations/radiator/radiator_view.blade.php:127` | 3 parallele Notification-Systeme: toastr.*, custom toast(), HTML-Divs | ⬜ |
| 86 | consistency | hoch | konsistenz | `resources/views/admin/invoice/invoice_create.blade.php:264, resources/views/admin/invoice/invoice_draft_view.blade.php:224, resources/views/admin/invoice/invoice_approved.blade.php:203, resources/views/admin/offer/set/paragraph/set_paragraph.blade.php:364, resources/views/admin/product/delivery/pdf.blade.php:289` | toastr.danger() ist keine gültige Toastr-Methode – Notifications ersch | ⬜ |
| 87 | consistency | hoch | konsistenz | `resources/views/admin/appointments/index.blade.php, resources/views/admin/customer/customer_details.blade.php, resources/views/admin/checklist/profitablity_calculation/profit.blade.php (Beispiele)` | DE/EN-Mischung in UI-Texten: 136 Blade-Dateien mischen Sprachen im sel | ⬜ |
| 88 | consistency | hoch | konsistenz | `routes/web.php (z. B. Zeile /branch_create vs. /supplier-connectors, /admin/fusion-forms/entries/{form_id} vs. /branch_expense/{branchExpense}/other-costs)` | Route-URL-Konvention gemischt: 648 snake_case- vs. 712 kebab-case-URLs | ⬜ |
| 89 | plausibility | hoch | plausibilitaet | `app/Http/Controllers/EmployeeDashboardController.php:735` | Hardcodierter Tomorrow.io API-Key im Quellcode | ⬜ |
| 90 | plausibility | hoch | plausibilitaet | `app/Http/Controllers/Wordpress/FusionFormSubmissionController.php:39,74,99,132,168,366,455` | Hardcodierter API-Token und hardcodierte branch_id=1 in Produktionscod | ⬜ |
| 91 | plausibility | hoch | plausibilitaet | `app/Http/Controllers/Customer/Offer/OfferController.php:61-62 und OfferFolderController.php:641-642 und OfferCommentController.php:30` | users.name wird als employee_id missbraucht – fragile Identitätsauflös | ⬜ |
| 92 | plausibility | hoch | plausibilitaet | `app/Http/Controllers/Employee/Position/QualificationController.php:42-44` | Qualifikations-Store übergibt unkontrollierte Rohdaten aus $request->q | ⬜ |
| 93 | plausibility | hoch | plausibilitaet | `app/Http/Controllers/Employee/EmployeeController.php:339,624,660,1339` | Mitarbeiterbild-Upload ohne MIME-Typ-Validierung (Dateiendung aus Orig | ⬜ |
| 94 | plausibility | hoch | plausibilitaet | `app/Http/Controllers/Branch/BranchController.php:303-317` | Mass-Assignment ohne Validierung in BranchController::offerUpdate() | ⬜ |
| 95 | other | hoch | redundanz | `app/Http/Controllers/EmployeeDashboardController.php:60-367` | index()- und mobile()-Methode in EmployeeDashboardController sind nahe | ⬜ |
| 96 | other | hoch | redundanz | `app/Http/Controllers/Inquiry/InquiryController.php:2638 / app/Http/Controllers/Inquiry/InquiryVerificationController.php:317` | Identische lead()- und verify-Logik dupliziert in InquiryController un | ⬜ |
| 97 | cleanup | hoch | redundanz | `resources/views/admin/roof_config/config.blade copy.php bis config.blade copy 7.php` | 8 Kopien von config.blade.php im roof_config-Modul | ⬜ |
| 98 | cleanup | hoch | redundanz | `resources/views/admin/dashboard/old codes/` | 9 tote Dashboard-Blade-Kopien in old codes/-Ordner | ⬜ |
| 99 | cleanup | hoch | redundanz | `resources/js/chat copy.js (1962 Zeilen), resources/js/chat copy 2.js (4687 Zeilen), resources/js/chat v2.js (738 Zeilen)` | 3 tote chat.js-Varianten in resources/js/ neben der aktiven Datei | ⬜ |
| 100 | cleanup | hoch | redundanz | `resources/views/admin/layouts/OLD CODE/ (app.blade copy.php, copy 2, copy 3, copy mar 2025.php, app.blade last 25.php), resources/views/admin/layouts/app.blade copy.php, app.blade copy 2.php` | 7 Kopien des Haupt-Layouts app.blade.php in OLD CODE/ plus 2 direkte K | ⬜ |
| 101 | cleanup | hoch | redundanz | `resources/views/admin/checklist/profitablity_calculation/Old Code/ (26 Dateien)` | 6 Kopien von profit.blade.php und weitere Old-Code-Dateien in profitab | ⬜ |
| 102 | cleanup | hoch | redundanz | `app/Http/Controllers/Customer/NewLeadsController.php:107-165` | Employee-Datenladen wird zweimal ausgeführt - zweites Ergebnis übersch | ⬜ |
| 103 | routing-other | hoch | routing | `routes/web.php:637,642` | Doppelte POST-Route für reportStore unter zwei verschiedenen URIs und  | ⬜ |
| 104 | security | hoch | routing | `routes/web.php:1380-1381` | toggle-status und testConnection für E-Mail-Konten ohne Auth-Middlewar | ⬜ |
| 105 | security | hoch | routing | `routes/web.php:3241-3243` | Destroy, Publish und Unpublish für E-Mail-Konfiguration über GET-Route | ⬜ |
| 106 | routing-other | hoch | routing | `routes/web.php:666-667` | Doppelt registrierte Route fusion.webhook.ajax (unterschiedliche Contr | ⬜ |
| 107 | security | hoch | routing | `routes/web.php:808,837,1119,1120,1121,1122` | Destruktive GET-Routen: Löschen/Junk per HTTP-GET auslösbar | ⬜ |
| 108 | security | hoch | routing | `routes/web.php:2492,2493,2520,2527,2559,2560,2561` | Destruktive Aktionen (destroy, publish) über GET-Requests erreichbar | ⬜ |
| 109 | security | hoch | routing | `routes/web.php:4225–4235` | Destruktive Deal-Aktionen über GET-Routen | ⬜ |
| 110 | security | hoch | routing | `routes/web.php:3869` | DELETE-Operationen über GET-Routen (CSRF-umgehend, Crawler-gefährdet) | ⬜ |
| 111 | security | hoch | routing | `routes/web.php:1911,1917,1919,1935` | Zustandsändernde Aktionen per GET-Request | ⬜ |
| 112 | security | hoch | routing | `routes/web.php:2654` | GET-Route für Datensatz-Löschen (asset_installment_destroy) | ⬜ |
| 113 | security | hoch | routing | `routes/web.php:728,751,758,765` | DELETE-Operationen über GET-Verben (CSRF-Bypass) | ⬜ |
| 114 | security | hoch | routing | `routes/web.php:2182, 2190, 2199, 684` | Destruktive User- und Branch-Aktionen über GET-Routen (CSRF-Bypass, Bo | ⬜ |
| 115 | security | hoch | routing | `routes/web.php:602 und routes/web.php:3969` | Doppelter Auth::routes()-Aufruf erzeugt mehrfach registrierte Login-/A | ⬜ |
| 116 | security | hoch | routing | `routes/api.php:206-220` | api/secure/master-sets fehlt Authentifizierungsmiddleware — nur Thrott | ✅¹ |
| 117 | security | hoch | routing | `routes/api.php:54-58` | GET /api/lead-name-suggestions und /api/lead-lastname-suggestions ohne | ⬜ |
| 118 | routing-other | hoch | routing | `routes/web.php:666-667` | Kollidierende Route-Namen: 'fusion.webhook.ajax' zweimal mit verschied | ⬜ |
| 119 | routing-other | hoch | routing | `routes/web.php:691 und routes/web.php:703` | Kollidierende Route-Namen: 'branch.address.update' für zwei verschiede | ⬜ |
| 120 | security | hoch | routing | `routes/web.php:684 (branch_destroy), 808 (new_lead_delete), 837 (lead_junk), 1120 (delete_lead_product), 4231 (deal_delete), u.v.m.` | GET-Routen für destruktive Aktionen: >73 GET-Routen führen Delete/Dest | ⬜ |
| 121 | security | hoch | routing | `routes/web.php:1935` | GET /ticket/kanban/update/{ticket_id}/{stage} ändert Ticket-Status via | ⬜ |
| 122 | security | hoch | routing | `routes/web.php:2192-2193` | GET /make_admin/{id} und GET /make_limit/{id} sind privilege-escalatio | ⬜ |
| 123 | security | hoch | routing | `routes/web.php:4514-4515` | GET /dispatch-chat-jobs und /chat-jobs ohne Authentifizierung | ⬜ |
| 124 | security | hoch | routing | `routes/web.php:4519` | GET /run-backfill-phase-sections führt Artisan-Befehl ohne Auth aus | ⬜ |
| 125 | refactor | hoch | workflow | `app/Http/Controllers/Report/OverdueCenterController.php:114-136` | recentReportsEmployeeSummary führt 5 separate DB-Queries pro Mitarbeit | ⬜ |
| 126 | refactor | hoch | workflow | `app/Http/Controllers/Customer/Offer/OfferController.php:574-618` | data()-Endpunkt lädt ALLE Angebote ohne Pagination und triggert syncOf | ⬜ |
| 127 | refactor | hoch | workflow | `resources/views/admin/deal/customer_view.blade.php:2417–2478` | N+1-Queries in Blade-View: bis zu 7 DB-Abfragen pro Listeneintrag | ⬜ |
| 128 | refactor | hoch | workflow | `app/Http/Controllers/Task/GeneralTaskController.php:79` | GeneralTask-Index lädt alle Aufgaben inkl. 10 Eager-Relations ohne Pag | ⬜ |
| 129 | refactor | hoch | workflow | `app/Http/Controllers/Report/OverdueCenterController.php:37-41,872-899` | Bis zu 4.100 Zeilen pro Request in PHP-Memory geladen und in-memory so | ⬜ |
| 130 | refactor | hoch | workflow | `app/Http/Controllers/Customer/Deal/DealMeasurementController.php:34-93, resources/views/admin/deal_measurements/index.blade.php:2870-3353` | Alle Messungen ohne Pagination in PHP geladen und als JSON in die Blad | ⬜ |
| 131 | refactor | hoch | workflow | `app/Http/Controllers/Customer/NewLeadsController.php:90-92,191-193,223-225` | Employee::all(), Product::all(), ArticleGroup::all() ohne Einschränkun | ⬜ |

---

## Fussnoten

**¹ Nr. 116 — der Befundtext ist falsch, das Ergebnis stimmt trotzdem (berichtigt 21.08.2026, Z2-W0-10).**

Der Satz „fehlt Authentifizierungsmiddleware — nur Throttle“ ist ein **Fehlalarm**: er misst die
Routenzeile und schliesst daraus auf den Schutz. Der Schutz sitzt aber im Controller —
`Api/MasterSetApiController::authApi()` prueft `X-API-USER`/`X-API-PASSWORD` gegen
`MASTER_SET_API_USER`/`MASTER_SET_API_PASSWORD` und antwortet ohne gueltige Daten mit 401.
Nachgemessen am laufenden System: anonym 401, mit Zugangsdaten 200. Es war also nie offen.
Der urspruengliche Text bleibt hier unveraendert stehen — ein Fehlbefund wird berichtigt,
nicht getilgt; wer die alte Liste in der Hand hat, muss die Zeile wiederfinden koennen.

Die Lehre ist die aus P7: **Ort ist nicht Wirkung.** Eine leere `middleware()`-Liste beweist
nicht die Abwesenheit einer Pruefung, sie beweist nur die Abwesenheit einer Pruefung *an dieser
Stelle*. Wer Auth misst, muss den Weg der Anfrage messen, nicht eine Zeile davon.

Der **wirkliche** Befund liegt woanders und ist nicht der aufgeschriebene: die Schnittstelle
liefert Einkaufspreise, Margen, Skonto, Haendlerpreise, Stundensaetze, Klarnamen und Foto-URLs
hinter einem einzigen statischen Passwortpaar aus der `.env`, und es liess sich **kein Konsument
finden**. Y-11 (Yama, 21.08.) hat daraufhin nicht Haertung, sondern **reversible Stilllegung**
entschieden: `MASTER_SET_API_AKTIV` (Vorgabe `false`), alle drei Routen antworten 404,
Controller und Routen bleiben erhalten. Waechter:
`tests/Feature/Api/MasterSetApiSchalterTest.php`. Auftrag:
`docs/auftraege/generator-auftrag-z2-w0-10-master-set-api-haertung.md`.
