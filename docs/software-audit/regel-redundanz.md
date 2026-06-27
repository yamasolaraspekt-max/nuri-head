# Audit – Redundanz

Funde: 42  ·  🔴 2 kritisch · 🟠 9 hoch · 🟡 25 mittel · ⚪ 6 niedrig

### 🔴 40 tote Controller in app/Http/Controllers/Old/ - vollstaendig unreferenziert  
**Modul:** Querschnitt · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Old/`  
**Problem:** 40 PHP-Controller-Dateien in einem dedizierten Old/-Ordner (AppointmentController.php, CustomerController.php, ProjectController.php, TaskToDoController.php, OfferConfigController.php u.v.m.). Grep auf routes/ ergab 0 Treffer fuer Controllers\Old\. Aktive Pendants existieren in Appointment/, Customer/, Project/ usw. Der Ordner ist toter Code, wird aber weiterhin von Composer autoloaded (psr-4).  
**Fix:** Den gesamten Ordner app/Http/Controllers/Old/ loeschen. Vor dem Loeschen sicherstellen, dass kein dynamischer require/use-Aufruf existiert (vollstaendiger app/-Scan auf 'Old\\'). Danach composer dump-autoload ausfuehren.

### 🔴 107 'blade copy'-Dateien in 25 Old-Code-Verzeichnissen in resources/views/  
**Modul:** Querschnitt · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `resources/views/admin/`  
**Problem:** 107 Blade-Dateien mit Namensmuster '*.blade copy*.php', '*.blade copy 2.php' etc. verteilt in 25 Verzeichnissen mit Namen wie 'Old Code', 'old codes', 'oldcode'. Groesste Ansammlungen: checklist/profitablity_calculation/Old Code (26 Dateien), planner/old (14), new_leads/old code (14), dashboard/old codes (10), layouts/OLD CODE (10). Keines dieser Verzeichnisse wird per @include oder view() referenziert.  
**Fix:** Alle 25 Old-Code-Verzeichnisse und die 107 copy-Dateien loeschen. Empfohlen: git-basiertes Loeschprotokoll (git rm -r), damit die Historie erhalten bleibt. Kein Backup noetig - Git ist das Backup.

### 🟠 index()- und mobile()-Methode in EmployeeDashboardController sind nahezu identisch (~300 Zeilen Duplikat)  
**Modul:** Dashboard & Berichte · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/EmployeeDashboardController.php:60-367`  
**Problem:** index() (Zeile 60) und mobile() (Zeile 235) enthalten exakt denselben Datenaufbereitungscode für Tasks, Appointments, DepartmentPositions, Leaves, SickDays und TimeManagement. Beide geben dieselbe View zurück (admin.dashboard.employee.mobile). Jede Änderung muss doppelt gepflegt werden; aktuell unterscheidet sich mobile() nur durch fehlende activeDepartments, myCustomerCount, myProjectCount.  
**Fix:** Gemeinsame Logik in private Hilfsmethode prepareEmployeeDashboardData() extrahieren. index() ergänzt das Ergebnis um Admin-Felder, mobile() nicht. Beide rufen dann dieselbe Hilfsmethode auf.

### 🟠 Identische lead()- und verify-Logik dupliziert in InquiryController und InquiryVerificationController  
**Modul:** CRM – Anfragen · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Inquiry/InquiryController.php:2638 / app/Http/Controllers/Inquiry/InquiryVerificationController.php:317`  
**Problem:** Die Lead-Erstellungslogik (lead(), distributor(), brand(), createBrandDepartment(), createDistributorDepartment(), updateVerification()) ist in beiden Controllern fast wortgleich implementiert. Ebenso sind importSingle() und importAll() in FusionFormSubmissionController.php eine 1:1-Kopie desselben URL-basierten Note-Aufbaus (ca. 50 Zeilen).  
**Fix:** Gemeinsame Logik in einen InquiryConversionService extrahieren. Beide Controller nutzen dann denselben Service. Fuer FusionFormSubmission: eine private buildNoteFromEntries()-Methode, die von beiden Methoden gerufen wird.

### 🟠 8 Kopien von config.blade.php im roof_config-Modul  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/roof_config/config.blade copy.php bis config.blade copy 7.php`  
**Problem:** 8 Versionen von config.blade copy.php bis config.blade copy 7.php liegen neben der aktiven config.blade.php. Ausserdem config2.blade copy.php. Insgesamt 9 tote Dateien in einem einzigen Verzeichnis - ein klares Zeichen fuer manuelle Versionierung statt Git-Branches.  
**Fix:** Alle copy-Varianten loeschen (config.blade copy*.php, config2.blade copy.php). Aktive Version ist config.blade.php und config2.blade.php.

### 🟠 9 tote Dashboard-Blade-Kopien in old codes/-Ordner  
**Modul:** Dashboard · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/dashboard/old codes/`  
**Problem:** 6 Kopien von dashboard.blade (copy, copy 2-5, copy new) plus 3 Kopien von mobile.blade (copy, copy 2, copy 3) in old codes/. Aktive Dateien: resources/views/admin/dashboard/employee/ und dashboard/test.blade.php. Zusaetzlich eine test.blade.php im Dashboard-Root, die nicht in routes/ referenziert wird.  
**Fix:** Ordner resources/views/admin/dashboard/old codes/ vollstaendig loeschen. dashboard/test.blade.php pruefen und falls nicht produktiv verwendet, ebenfalls entfernen.

### 🟠 3 tote chat.js-Varianten in resources/js/ neben der aktiven Datei  
**Modul:** Chat · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/js/chat copy.js (1962 Zeilen), resources/js/chat copy 2.js (4687 Zeilen), resources/js/chat v2.js (738 Zeilen)`  
**Problem:** Neben der aktiven resources/js/chat.js (5685 Zeilen, in vite.config.js registriert und per @vite geladen) liegen drei Backup-Versionen. 'chat v2.js' wird in keiner View per @vite oder asset() geladen. Weder 'chat copy.js' noch 'chat copy 2.js' sind in vite.config.js registriert.  
**Fix:** Die drei Dateien chat copy.js, chat copy 2.js und chat v2.js aus resources/js/ entfernen. Die aktive chat.js bleibt unveraendert.

### 🟠 7 Kopien des Haupt-Layouts app.blade.php in OLD CODE/ plus 2 direkte Kopien im layouts/-Root  
**Modul:** Layouts · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/layouts/OLD CODE/ (app.blade copy.php, copy 2, copy 3, copy mar 2025.php, app.blade last 25.php), resources/views/admin/layouts/app.blade copy.php, app.blade copy 2.php`  
**Problem:** Das kritische Hauptlayout wird in 7 Kopien im Old-Code-Ordner und 2 direkt neben der aktiven app.blade.php gespeichert. Zusaetzlich liegen test.blade.php und test2.blade.php im layouts/-Root, die keine Route referenziert. Der OLD CODE/-Ordner enthaelt auch 5 Kopien von test.blade.php.  
**Fix:** Old CODE/-Ordner loeschen. app.blade copy.php und app.blade copy 2.php im layouts/-Root entfernen. test.blade.php und test2.blade.php im layouts/-Root loeschen, falls keine Route sie rendert.

### 🟠 6 Kopien von profit.blade.php und weitere Old-Code-Dateien in profitablity_calculation/  
**Modul:** Checklist/Profit · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/checklist/profitablity_calculation/Old Code/ (26 Dateien)`  
**Problem:** Groesste Old-Code-Ansammlung im Projekt: 26 Dateien, darunter profit.blade copy.php bis profit.blade copy 5.php, bonus.blade copy.php und weitere. Die aktiven Dateien profit.blade.php und bonus.blade.php liegen im Elternordner. 26 tote Dateien in einem einzigen Unterordner.  
**Fix:** Den gesamten Unterordner Old Code/ mit allen 26 Dateien loeschen.

### 🟠 Employee-Datenladen wird zweimal ausgeführt - zweites Ergebnis überschreibt das erste (Dead Code)  
**Modul:** NewLeads · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:107-165`  
**Problem:** In new_object() werden ab Zeile 107 alle Mitarbeiter geholt, mit Urlaub- und Vertretungs-Tabellen verknüpft ($availableEmployees) und das Ergebnis in $data['employees'] gesetzt (Zeile 162). Zeile 165 überschreibt sofort mit einem einfachen Employee::select()->get() ohne die Leave-Logik. Der gesamte Code von Zeile 107-162 ist damit toter Code: zwei unnötige DB-Abfragen (leaves, job_representatives) und eine Collection-Operation werden immer für nichts ausgeführt.  
**Fix:** Die Zeilen 107-162 entfernen. Die zweite Zuweisung in Zeile 165 ist der effektive Pfad und sollte (ggf. mit Leave-Logik) übernommen werden.

### 🟡 Mindestens 10 dead/copy-Blade-Dateien im Dashboard-Verzeichnis  
**Modul:** Dashboard & Berichte · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/dashboard/employee/Old/ (7 copy-Dateien), resources/views/admin/dashboard/old codes/dashbaord.blade.php, resources/views/admin/dashboard/employee/mobile.blade copy.php, resources/views/admin/dashboard/test.blade.php`  
**Problem:** Im Dashboard-Verzeichnis befinden sich 'dashboard.blade copy.php', 'dashboard.blade copy 2-5.php', 'mobile.blade copy.php' usw. sowie das 'Old'-Verzeichnis mit 13 alten Partials. Diese toten Dateien erhöhen die Konfusion beim Debugging, können versehentlich referenziert werden und blähen das Repository auf.  
**Fix:** Alle Dateien in Old/ und *.blade copy*.php löschen. OverdueCenterController copy.php in app/Http/Controllers/Old/ ebenfalls entfernen.

### 🟡 Doppelte Implementierungen: createGroup, leave group, mark-email-as-read, getEmailDetails  
**Modul:** CRM – Kommunikation · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:4669+4680 / routes/web.php:4670+4693+4697 / routes/web.php:1378+1394 / app/Http/Controllers/WebsiteController.php:457 + LeadEmailReaderController.php:402`  
**Problem:** 1) createGroup existiert unter /chat/group/creates (ChatController) und /chat/group/create (ChatGroupController) – zwei unterschiedliche Controller mit ähnlicher Logik. 2) leaveGroup hat drei Routen: /chat/group/leave (ChatController), /chat/group/leave/{id} und /chat/group/leave/{group} (ChatGroupController). 3) mark-email-as-read ist in zwei Controllern mit zwei Routen implementiert (lead-emails.mark-read und lead.email.mark.read). 4) getEmailDetails ist in LeadEmailReaderController und WebsiteController parallel implementiert.  
**Fix:** Je Funktion einen kanonischen Endpunkt definieren und alle anderen entfernen. Insbesondere die öffentliche /lead/email/api-Route (WebsiteController) löschen und nur die gesicherte admin-Route behalten.

### 🟡 Tote Kopien/Old-Code-Dateien im Chat-Views-Verzeichnis  
**Modul:** CRM – Kommunikation · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/chats/Old Codes/chat v1.blade.php / resources/views/admin/chats/Old Codes/chat.blade copy.php`  
**Problem:** Im Verzeichnis 'Old Codes' liegen zwei nicht mehr genutzte Blade-Dateien (172 und 542 Zeilen), die noch im Repo verbleiben. Der Ordnername enthält ein Leerzeichen, was in Deployment-Scripts und git-Operationen regelmäßig Probleme erzeugt. Zusätzlich liegt resources/views/admin/chats/chat.blade.php neben der aktiven employee/chat.blade.php, ohne erkennbaren aktiven Einsatz.  
**Fix:** Old-Code-Dateien aus dem Repository entfernen (git rm). Wenn Versionierung benötigt wird, git-History nutzen.

### 🟡 In departmentEmployees() werden innendienst und aussendienst mit identischer DB-Abfrage befuellt  
**Modul:** CRM – Anfragen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Inquiry/InquiryController.php:1695-1710`  
**Problem:** Fuer jede Mapping-Zeile werden $innendienst und $aussendienst mit exakt identischen JOIN-Abfragen auf employees/employee_departments befuellt (same table, same WHERE, same SELECT, same ORDER BY). Das verdoppelt unnoetig die DB-Last. Ausserdem wird pro Mapping noch eine separate article_groups-Abfrage gefeuert (Zeile 1713), was bei vielen Produkten ein N+1-Problem erzeugt.  
**Fix:** $innendienst einmal abfragen, $aussendienst = $innendienst setzen (oder eine echte Unterscheidung implementieren, z.B. ueber eine 'type'-Spalte in product_positions). Die article_group-Abfrage vor der foreach-Schleife mit einem einmaligen IN-Query vorladen.

### 🟡 Massenhaft tote Copy-Backup-Blades im Produktionscode  
**Modul:** CRM – Leads & Kunden · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/new_leads/layouts/history_modal.blade copy.php:1`  
**Problem:** Im CRM-Modul existieren mindestens 9 '.blade copy.php'-Dateien (z.B. history_modal.blade copy.php, profile.blade copy.php, customer_view.blade copy.php, customer_profile.blade copy.php u.a.) sowie eine 'old code/'-Unterordner-Gruppe und eine partials.zip im Verzeichnis. Diese Dateien liegen im Produktions-Repository, sind nicht über Routen erreichbar, blähen das Repo auf und verursachen Verwirrung welche Version aktuell ist.  
**Fix:** Alle *.blade copy*.php und Old-Code-Ordner über Git entfernen (git rm). Versionierung über Git-History, nicht über Copy-Dateien.

### 🟡 Zwei parallele Routen-/Controller-Stacks für Distributor-Preise speichern  
**Modul:** CRM – Partner · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:2303 und routes/web.php:2448`  
**Problem:** Es existieren zwei separate POST-Endpunkte zum Erstellen von Distributor-Preisen: /products/{product}/distributor-prices (DistributorPriceController@storeSingle) und products/{product}/distributor-prices (DistributorController@save, prefix 'products'). Beide haben unterschiedliche Route-Namen ('products.distributor-prices.store' vs. 'distributor-prices.store'), unterschiedliche Validierungs- und Berechnungslogik und liegen in unterschiedlichen Controllern. Dasselbe Muster wiederholt sich für update und delete.  
**Fix:** Einen der Stacks als kanonisch festlegen (DistributorController ist besser strukturiert) und den DistributorPriceController auf diesen konsolidieren. Den veralteten Stack mit einem TODO-Deprecation-Kommentar versehen und nach Migration entfernen.

### 🟡 Doppelter document_status-Key in data()-JSON-Response und mehrfache DB-Aufrufe für denselben Wert  
**Modul:** Vertrieb – Angebote · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Offer/OfferFolderController.php:923 und :934`  
**Problem:** Im Array des jsonSuccess()-Aufrufs in data() ist 'document_status' zweimal als Key gesetzt (Zeilen 923 und 934). PHP überschreibt den ersten Wert – der erste Eintrag (inkl. 'document_status_label', 'angebot_snapshot_sections' usw.) geht verloren. Zusätzlich wird $this->resolveFolderDocumentStatus($folder) sieben Mal (Zeilen 923, 934, 942, 943, 944, 948, 954) und $this->findWorkflowMainStage(...) viermal in derselben Methode aufgerufen, je mit erneuten DB-Queries.  
**Fix:** Den ersten document_status-Block (Zeile 923) aus dem Array entfernen, da Zeile 934 die korrekte Position ist. $documentStatus = $this->resolveFolderDocumentStatus($folder) vor dem Array einmalig berechnen und die Variable wiederverwenden. findWorkflowMainStage einmalig in $mainStage speichern.

### 🟡 Zwei parallele Offer-Controller (OfferController + OffersController) mit überschneidender Funktionalität  
**Modul:** Vertrieb – Angebote · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Offer/OfferController.php:30 und app/Http/Controllers/Customer/Offer/OffersController.php:15`  
**Problem:** OfferController (2929 Zeilen) und OffersController (290 Zeilen) existieren nebeneinander für dasselbe Modul. OffersController::store() (über POST customer_offer_save) dupliziert Teile der Angebotsanlage aus OfferController::store(). OffersController::index() rendert admin.offer.view.offer_view – eine parallele Ansicht neben admin.offer.index aus OfferController::index(). Zwei inkonsistente Einstiegspunkte für dasselbe Modul.  
**Fix:** OffersController in OfferController zusammenführen oder OffersController explizit als 'Legacy/deprecated' markieren und schrittweise migrieren. Route customer_offer_save auf den modernen OfferController::store() umlenken.

### 🟡 Doppelte Route-Registrierung für deal-measurements.notes.store  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:4391–4392 und 4407–4408`  
**Problem:** Die Route POST /deal-measurements/{measurement}/notes mit dem Namen deal-measurements.notes.store wird zweimal identisch registriert. Laravel überschreibt die erste Registrierung mit der zweiten. Dies ist toter Code, kann zu Debugging-Verwirrung führen und bricht bei zukünftigen Middleware-Änderungen an einer der Stellen.  
**Fix:** Eine der doppelten Definitionen (Zeile 4391–4392) entfernen. Nur die sauber eingerückte Variante (Zeile 4407–4408) behalten.

### 🟡 Massenhaft tote Copy-/Old-View-Dateien im gesamten Modul (25+ Dateien)  
**Modul:** Projekte & Planer · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/planner/old/index.blade.php:1`  
**Problem:** Im Modul existieren über 25 tote Arbeitsdateien: `planner/old/` (14 Dateien inkl. `index.blade copy.php`, `index.blade feb.php`, `scripts.blade copy.php`), `todo/todo_checklist.blade copy.php`, `todo/todo_checklist.blade copy 2.php` (199 KB), `todo/personal/Old Codes/` (7 Dateien inkl. `task_view.blade copy.php`, `task_view.blade copy 2.php`), `planner/visual.blade copy.php`, `task_to_do/oldCode/project.blade copy.php`. Dazu kommen in general_tasks 2 ungenutzte Gantt-Views (`gantt-view-v3.blade.php`, `gantt-view.blade.php` – nur `gantt-view-v4` wird per `@include` eingebunden).  
**Fix:** Alle `old/`, `Old Codes/` und `*.blade copy*.php`-Dateien sowie die ungenutzten Gantt-Views in Git löschen (`git rm`). Version-History ist über Git verfügbar; redundante Dateien im Repo erzeugen Verwirrung und erschweren Refactoring.

### 🟡 Doppelte Route-Definitionen für Kommentare und Likes  
**Modul:** Support – Tickets · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:2006,2122 und 2247,2085`  
**Problem:** Die Route POST /ticket/comments ist zweimal mit demselben Namen comments.store definiert (Zeilen 2006 und 2122). Das gleiche gilt für die Like-Route: ticket-reports.like (Zeile 2003) und ticket-report.like (Zeile 2085) zeigen auf dieselbe Methode. Außerdem gibt es zwei Routen für Berichts-Kommentare: ticket-reports.comments.store (2004) und ticket-report-comments.store (2090). Doppelte Route-Namen überschreiben sich in Laravel gegenseitig, was zu unvorhersehbaren URL-Generierungen führt.  
**Fix:** Alle doppelten Routen-Blöcke konsolidieren. Die 'Backward-compatible old route names' im Kommentar zeigen, dass dies absichtlich entstanden ist – die alten Namen per Route::redirect() oder einem einzigen Alias lösen, nicht durch Duplizierung der Definitionen.

### 🟡 Doppelte Beziehungs-Methoden im Problem-Model (error/errors, employee/employees)  
**Modul:** Support – Tickets · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Models/Problem.php:114,119,134,140`  
**Problem:** Das Model definiert sowohl error() (Zeile 114, BelongsToMany ohne explizite Pivot-Tabelle) als auch errors() (Zeile 134, BelongsToMany mit error_problem-Tabelle). Ebenso employee() (119, ohne explizite Pivot-Tabelle) und employees() (140, mit employee_problem-Tabelle). Im Controller wird per method_exists() zwischen beiden gewechselt (ProblemController.php:296-305, 302-306). Diese Dopplung führt zu Verwirrung und kann Bugs verursachen, wenn beide Beziehungen unterschiedlich konfiguriert sind.  
**Fix:** error() und employee() entfernen, nur die explizit konfigurierten errors() und employees() behalten. Alle Controller-Prüfungen mit method_exists() entfernen und direkt die Eloquent-Beziehungen aufrufen.

### 🟡 Zwei parallele Create-Employee-Flows (add() und store()) mit inkonsistenter Validierung  
**Modul:** Personal / HR · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Employee/EmployeeController.php:256,604`  
**Problem:** Es existieren zwei separate Methoden zum Anlegen eines Mitarbeiters: add() (Zeile 256, Route emp_add) mit vollständiger Validierung (17 Felder), Sprachsync, Department/Position-Zuordnung und TimeManagement-Plan-Erzeugung; und store() (Zeile 604, Route emp_save) mit nur 3 Feldern (name, lastname, branch) ohne Image-Validierung. Beide erzeugen Employee-Datensätze, aber store() erzeugt keinen TimeManagementPlan und hat keine Abteilungszuordnung. Views emp_create und emp_add zeigen unterschiedliche Formulare ohne klare Nutzerführung.  
**Fix:** store() entweder entfernen und emp_save-Route auf add() umleiten, oder store() um die gleiche Geschäftslogik erweitern. Den doppelten Zustand dokumentieren und nur einen Einstiegspunkt für Mitarbeiter-Anlage behalten.

### 🟡 Drei identische /getEmployees-Routen für drei verschiedene Controller  
**Modul:** Personal / HR · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:822,1796,3653`  
**Problem:** POST /getEmployees ist dreifach registriert: NewLeadsController@getEmployee (Zeile 822), LeaveController@getEmployees (Zeile 1796 mit Name leave.employees) und EmployeeController@getEmployees (Zeile 3653 mit Name get.employees). Laravel löst die letzte Registration auf — die erste und zweite Route sind de facto tot. Die route()-Hilfsfunktion liefert für leave.employees einen anderen Pfad als /getEmployees.  
**Fix:** Jede Route unter einem eindeutigen Pfad registrieren (z.B. /employee/leave-mentions, /admin/employee-list) und alle Aufrufer entsprechend anpassen.

### 🟡 Doppelt registrierte Routen: products.list und product.description.store.ajax  
**Modul:** Artikel · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:2293-2294 und 2308,2318`  
**Problem:** Die Route GET /product/list mit Name products.list ist zweimal identisch registriert (Zeilen 2293 und 2294). Die Route POST /product/description/store mit Name product.description.store.ajax erscheint ebenfalls zweimal (Zeilen 2308 und 2318). Doppelt registrierte Named Routes überschreiben sich gegenseitig; dies erschwert Debugging und führt bei route()-Aufrufen zu unvorhersehbarem Verhalten.  
**Fix:** Doppelte Definitionen entfernen. Eine Code-Review mit php artisan route:list --name=products.list durchführen.

### 🟡 Zahlreiche Old-Code/Backup-Blade-Dateien im Repo  
**Modul:** Artikel · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/product/Old Code/product_create.blade.php / resources/views/admin/master_sets/index.blade copy.php (3 Kopien)`  
**Problem:** Im Verzeichnis 'Old Code' liegt eine 2.464-Zeilen-Kopie der alten product_create-View. Im master_sets-Verzeichnis existieren drei Backup-Kopien (index.blade copy.php, copy 2, copy 3). Diese Dateien sind nicht über Routen oder Includes eingebunden, aber sie steigen bei Suchanfragen auf, vergrößern das Repo und gefährden versehentliche Einbindung.  
**Fix:** Alle *copy*.blade.php und 'Old Code'-Verzeichnisse aus dem Repo entfernen und ggf. in einem separaten Git-Branch archivieren.

### 🟡 Tote Old-Code-Views im Lager-Modul (13 Blade-Dateien)  
**Modul:** Lager · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/product/assets/Old Cod/, resources/views/admin/product/delivery/oldCode/, resources/views/admin/product/inventory/purchase_request/oldCode/`  
**Problem:** 13 Blade-Dateien in drei 'Old Cod'/'oldCode'-Verzeichnissen existieren im Repo. Kein aktiver Controller referenziert diese Views (per grep bestätigt). Sie enthalten {!!$errors->first(...)!!}-Muster (unescaped) und veraltete Formularstrukturen, was Sicherheitsrisiken bei versehentlicher Wiederverwendung darstellt.  
**Fix:** Alle Dateien in diesen Verzeichnissen löschen; falls historisch benötigt, in Git-History belassen.

### 🟡 Zwei parallele, inkonsistente User-Verwaltungssysteme (Legacy + Neu) mit duplizierten Routen  
**Modul:** Admin & System · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:2181-2228`  
**Problem:** Es existieren zwei vollständige User-CRUD-Systeme: (1) Legacy-Routen /admin_user, /admin_destroy/{id}, /make_admin/{id}, /make_limit/{id}, /deactive/{id}, /active/{id} (GET-basiert, nicht REST); (2) Neue AJAX-Routen /admin/users, /admin/users/fetch, /admin/users/{user} (PUT/DELETE, JSON). Beide laufen auf demselben Controller, verwalten dieselben Daten, aber mit unterschiedlicher Validierung, unterschiedlichen Response-Formaten und unterschiedlichem Sicherheitsniveau. Der admin_user() Controller (Zeile 161) rendert dieselbe View wie adminUsersPage() (Zeile 540).  
**Fix:** Legacy-Routen und zugehörige Controller-Methoden nach Migrationsabschluss entfernen. Alle Aufrufer auf die neuen AJAX-Endpunkte umstellen.

### 🟡 Doppeltes mini_chat.js: resources/js/ (Quelle) vs public/js/ (stale Kopie) plus public/js/mini_chat copy.js  
**Modul:** Chat · **Severity:** mittel · · unverifiziert  
**Ort:** `public/js/mini_chat copy.js, public/js/mini_chat.js (vs. resources/js/mini_chat.js)`  
**Problem:** Vite kompiliert resources/js/mini_chat.js nach public/build/. Die Datei public/js/mini_chat.js (23031 Bytes) ist eine aeltere, manuell abgelegte Version (Dec 2025), waehrend resources/js/mini_chat.js 38150 Bytes hat (neuerer Stand). public/js/mini_chat copy.js (19730 Bytes, Aug 2025) ist eine noch aeltere Kopie. Risiko: Entwickler laden versehentlich den veralteten Stand aus public/js/.  
**Fix:** public/js/mini_chat.js und public/js/mini_chat copy.js loeschen. Sicherstellen, dass alle Blade-Views per @vite laden und nicht per asset('js/mini_chat.js').

### 🟡 15 test*.blade.php-Dateien ausserhalb dedizierter Testinfrastruktur  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/test/test.blade.php, resources/views/admin/dashboard/test.blade.php, resources/views/admin/layouts/test.blade.php, resources/views/admin/formula/test.blade.php u.a.`  
**Problem:** 15 Blade-Dateien mit Namen test.blade.php, test1.blade.php, test2.blade.php in Produktionsverzeichnissen. Keine dieser Dateien wird in routes/ oder Controllern per view()-Aufruf referenziert. Potenzielles Sicherheitsrisiko: Falls Route-Wildcards existieren, koennte ein test-View unbefugt auslieferbar sein.  
**Fix:** Alle 15 test-Blade-Dateien loeschen. Fuer Prototyping dedizierte Feature-Branches nutzen statt Dateien in main zu committen.

### 🟡 Identische getStatusLabelAttribute()-Implementierung in 3 Branch-Modellen ohne Trait  
**Modul:** Querschnitt/Models · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Models/BranchExpense.php:88, app/Models/BranchRent.php:75, app/Models/BranchInsurance.php:73`  
**Problem:** Alle drei Modelle implementieren exakt denselben Code: `return self::statuses()[$this->status] ?? (string) $this->status;`. Kein gemeinsamer Trait oder abstrakte Basisklasse. Jede Aenderung der Logik muss dreifach gepflegt werden.  
**Fix:** Einen Trait HasStatusLabel erstellen (app/Models/Traits/HasStatusLabel.php) mit der gemeinsamen Methode. In allen drei Modellen den Trait einbinden (use HasStatusLabel). Falls die statuses()-Methode ebenfalls identisch ist, auch diese in den Trait aufnehmen.

### 🟡 4 tote Kanban-Blade-Varianten im Hauptordner plus 6 im oldcode/-Unterordner  
**Modul:** Kanban · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/kanban/kanban.blade copy.php, kanban.blade copy 2.php, resources/views/admin/kanban/oldcode/ (6 Dateien)`  
**Problem:** Neben der aktiven kanban.blade.php liegen 2 copy-Dateien direkt im Ordner und 6 weitere im oldcode/-Unterordner. Ausserdem gibt es eine caban.blade.php (Tippfehler) im Hauptordner. Insgesamt 9 tote Dateien in einem Modul.  
**Fix:** Alle copy-Dateien und den oldcode/-Unterordner loeschen. caban.blade.php (Tippfehler) ebenfalls entfernen, sofern keine Route darauf verweist.

### 🟡 OverdueCenterController copy.php in Old/ als Duplikat des aktiven Report-Controllers  
**Modul:** Controller/Old · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Old/OverdueCenterController copy.php (2289 Zeilen) vs. app/Http/Controllers/Report/OverdueCenterController.php (4612 Zeilen)`  
**Problem:** Die copy-Datei in Old/ hat einen anderen Namespace (App\Http\Controllers) als der aktive Controller (App\Http\Controllers\Report). Sie ist mit 2289 Zeilen halb so gross wie der aktive Controller. Durch den Leerzeichen-im-Dateinamen kann PHP sie nicht korrekt als Klasse laden - sie ist vollstaendig toter Code.  
**Fix:** Die Datei 'OverdueCenterController copy.php' loeschen. Wird bereits durch Fund #1 (gesamter Old/-Ordner) abgedeckt - explizit erwaehnt wegen des Risikos durch den Namespace-Konflikt.

### 🟡 Doppelte Controller-Dateien: InstallmentPaymentController und MobileCalendarController  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/InstallmentPaymentController.php vs. app/Http/Controllers/Inventory/InstallmentPaymentController.php; app/Http/Controllers/MobileCalendarController.php vs. app/Http/Controllers/Api/MobileCalendarController.php`  
**Problem:** InstallmentPaymentController existiert identisch an zwei Orten (diff leer). MobileCalendarController existiert in zwei Namespaces mit unterschiedlichem Inhalt (Api-Variante ist signifikant erweitert). Die Root-Version wird in routes/web.php nur als 'InstallmentPaymentController' (Zeile 146) importiert, ist aber durch Inventory/MachineInstallmentController größtenteils obsolet.  
**Fix:** Root-Level-Doppelgänger entfernen. Den MobileCalendarController klar auf Api/MobileCalendarController konsolidieren und den Root-Import aus routes/web.php entfernen.

### 🟡 Employee::all() in sieben verschiedenen Methoden des HandoverControllers ohne Caching  
**Modul:** HandoverModule · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/HandoverController.php:27,51,67,98,108,160,170`  
**Problem:** Jede Methode (index, multiple, handover, next, print u. a.) lädt Employee::all() neu. Obwohl das kein N+1-Problem im klassischen Sinne ist, werden bei je eigenem Tab-Aufruf alle Mitarbeiter komplett aus der DB geladen, selbst wenn sich die Daten im Request-Zyklus nicht ändern.  
**Fix:** Ein einziges Employee-Collection im Konstruktor oder einer privaten Hilfsmethode mit Cache::remember('employees-dropdown', 300, fn() => Employee::select('id','name','lastname')->get()) bereitstellen.

### ⚪ Doppelte Qualifizierungs-Logik in store(), details_update() und qualified()  
**Modul:** CRM – Leads & Kunden · **Severity:** niedrig · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:624,1057,1769`  
**Problem:** Die Status-Bestimmungslogik (QUALIFIZIERT / KEINE KONTAKTDATEN / bitte per Brief / per E-Mail / telefonisch) ist identisch dreimal implementiert: in store() (ab L624), in details_update() (ab L1057) und in qualified() (ab L1769). Änderungen müssen dreifach gepflegt werden – was bereits zu den Inkonsistenzen bei Leerzeichen geführt hat.  
**Fix:** Logik in private function calculateQualificationStatus(string $street, string $postcode, string $city, ?string $email, ?string $phone, ?string $telephone): string auslagern und an allen drei Stellen aufrufen.

### ⚪ Orphan-Views: problem_view.blade.php und Old-Codes-Verzeichnis  
**Modul:** Support – Tickets · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/problem/problem_view.blade.php:1 und resources/views/admin/problem/Old codes/problem_view.blade copy.php:1`  
**Problem:** Die Route problem.view (GET /problem_view) zeigt auf ProblemController@index, der admin.problem.problem_board rendert – nicht problem_view.blade.php. problem_view.blade.php wird von keinem Controller mehr zurückgegeben. Das 'Old codes'-Verzeichnis enthält eine .blade copy.php-Datei (47 KB), die ebenfalls nie geladen wird. Diese toten Dateien erhöhen die Codebasis-Größe, verwirren neue Entwickler und können bei Suchen falsche Treffer liefern.  
**Fix:** problem_view.blade.php, Old codes/problem_view.blade copy.php und pages/old code/view.blade copy.php löschen. Sicherstellen, dass keine Blade-@include-Ketten diese Dateien noch referenzieren.

### ⚪ Veraltete Old-Views im Expense-Modul  
**Modul:** Finanzen · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/expense/Old/`  
**Problem:** Unter Old/ liegen 9 Blade-Dateien (branch_expense.blade.php, expense_details.blade.php, expense_year.blade.php, pages/*, tabs/rent.blade.php). Keine Route oder kein Controller referenziert diese Views mehr. Sie sind toter Code und erzeugen Verwirrung beim Onboarding.  
**Fix:** Den gesamten Old/-Ordner aus dem Repository entfernen.

### ⚪ Tote Kopien von Phase- und Chat-Views in OldCode-Verzeichnissen  
**Modul:** Admin & System · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/task/phase/OldCode/phase_details.blade copy.php, resources/views/admin/chats/Old Codes/chat v1.blade.php`  
**Problem:** Mindestens 4 tote Blade-Dateien in OldCode/Old-Codes-Verzeichnissen innerhalb des Admin-Moduls (phase_details.blade copy.php, phase_management.blade copy.php, chat v1.blade.php, chat.blade copy.php). Diese werden nicht geroutet, erhöhen aber die Wartungslast und verwirren neue Entwickler bezüglich des aktuellen Stands.  
**Fix:** Verzeichnisse OldCode und 'Old Codes' vollständig löschen und aus dem Repository entfernen.

### ⚪ chat_show.blade copy.php neben aktivem chat_show.blade.php in resources/views/ai/  
**Modul:** AI/Chat · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/ai/chat_show.blade copy.php`  
**Problem:** Direkt neben der aktiven Datei liegt eine Kopie. Typisches Muster der manuellen Versionierung, die quer durch das gesamte Projekt zu beobachten ist.  
**Fix:** resources/views/ai/chat_show.blade copy.php loeschen.

### ⚪ folder-structure-initial.blade.php als veralteter Vorlaefer von folder-structure.blade.php  
**Modul:** Task/Phase · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/task/phase/partials/folder-structure-initial.blade.php (42 Zeilen) vs folder-structure.blade.php (104 Zeilen)`  
**Problem:** Beide Dateien existieren nebeneinander. Die '-initial'-Version ist ein frueherer, reduzierter Stand (42 Zeilen) der weiterentwickelten Version (104 Zeilen). Diff zeigt, dass -initial nur die Gruppierungsvariable setzt, waehrend folder-structure.blade.php das vollstaendige Rendering enthaelt. Falls -initial noch aktiv per @include eingebunden ist, existiert doppeltes Partial-Rendering.  
**Fix:** Pruefen, ob folder-structure-initial.blade.php noch per @include aufgerufen wird. Falls ja, durch die vollstaendige folder-structure.blade.php ersetzen und -initial loeschen. Falls nein, direkt loeschen.
