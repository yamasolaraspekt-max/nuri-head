# Arbeitsliste nach echter Ausnutzbarkeit

**Basis für die Fix-Phase** (ersetzt die Roh-CSV als Priorisierung). Stand: 2026-06-28.
Legende Verifikation: ✅ dynamisch bestätigt · ⚠️ statisch — **vor Fix gegen Controller-Konstruktor prüfen** (wie #28/#32 könnten False Positives sein) · 🛑 Zugriff offen, Effekt nicht getriggert.

## 🔴 Tier A — anonym ausnutzbar (kein Login nötig) — 18
_Höchste Priorität. ✅ = dynamisch reproduziert, ⚠️ = statisch (erst Konstruktor prüfen)._

- ✅ **#20 [kritisch/routing]** Öffentliche Route gibt LeadEmail-Inhalte ohne Authentifizierung zurück — `routes/web.php:599`
- ✅ **#23 [kritisch/routing]** AllContactController ohne Auth-Middleware: alle Kontakte und Export öffentlich — `routes/web.php:4609-4615`
- ✅ **#26 [kritisch/routing]** Leave-Notiz-Endpunkte ohne Auth-Middleware öffentlich erreichbar — `routes/web.php:1810-1815`
- ✅ **#29 [kritisch/routing]** PurchaseRequest- und RequestOut-Routen ohne Auth-Middleware — `routes/web.php:2696, 2716`
- ✅ **#30 [kritisch/routing]** GoodsReceipt-Routen ohne Auth-Middleware — `routes/web.php:5096`
- ✅ **#31 [kritisch/routing]** BEG-Förderungen ohne Auth-Middleware (unauthentifizierter Zugriff) — `routes/web.php:860`
- 🛑 **#33 [kritisch/routing]** GET /route-cache führt Artisan-Befehle aus — kein Auth-Schutz — `routes/web.php:3961`
- 🛑 **#34 [kritisch/routing]** GET /fix-notes schreibt Datenbankdaten ohne Authentifizierung — `routes/web.php:404`
- ⚠️ **#103 [hoch/routing]** Doppelte POST-Route für reportStore unter zwei verschiedenen URIs und Namen — `routes/web.php:637,642`
- ⚠️ **#104 [hoch/routing]** toggle-status und testConnection für E-Mail-Konten ohne Auth-Middleware — `routes/web.php:1380-1381`
- ⚠️ **#106 [hoch/routing]** Doppelt registrierte Route fusion.webhook.ajax (unterschiedliche Controller) — `routes/web.php:666-667`
- ⚠️ **#108 [hoch/routing]** Destruktive Aktionen (destroy, publish) über GET-Requests erreichbar — `routes/web.php:2492,2493,2520,2527,2559,2560,2561`
- ⚠️ **#115 [hoch/routing]** Doppelter Auth::routes()-Aufruf erzeugt mehrfach registrierte Login-/Auth-Routen — `routes/web.php:602 und routes/web.php:3969`
- ⚠️ **#116 [hoch/routing]** api/secure/master-sets fehlt Authentifizierungsmiddleware — nur Throttle — `routes/api.php:206-220`
- ⚠️ **#118 [hoch/routing]** Kollidierende Route-Namen: 'fusion.webhook.ajax' zweimal mit verschiedenen Controllern — `routes/web.php:666-667`
- ⚠️ **#119 [hoch/routing]** Kollidierende Route-Namen: 'branch.address.update' für zwei verschiedene Routen — `routes/web.php:691 und routes/web.php:703`
- ⚠️ **#123 [hoch/routing]** GET /dispatch-chat-jobs und /chat-jobs ohne Authentifizierung — `routes/web.php:4514-4515`
- ⚠️ **#124 [hoch/routing]** GET /run-backfill-phase-sections führt Artisan-Befehl ohne Auth aus — `routes/web.php:4519`

## 🟠 Tier B — braucht eingeloggtes Opfer (XSS / CSRF / fehlende Rollenprüfung) — 23

- ⚠️ **#4 [kritisch/blade]** Stored XSS: {!! $r->report !!} rendert Benutzer-HTML ungesanitized — `resources/views/admin/appointments/show.blade.php:1635`
- ⚠️ **#5 [kritisch/crud]** Fehlende Autorisierung: Jeder angemeldete User kann jede GeneralTask bearbeiten/löschen/umsortieren — `app/Http/Controllers/Task/GeneralTaskController.php:161`
- ⚠️ **#16 [kritisch/plausibilitaet]** IMAP-Passwörter im Klartext in DB und HTML gespeichert und exponiert — `resources/views/admin/lead_email/email_config/account.blade.php:276 / app/Http/Controllers/Email/LeadEmailAccountsController.php:118`
- ⚠️ **#22 [kritisch/routing]** GET-Routen fuer destruktive Operationen (Delete, Junk, Restore, Verify) — `routes/web.php:4002,4003,4004,4005,4006,4009,4107`
- ⚠️ **#25 [kritisch/routing]** Destruktive Operationen über GET-Routen (CSRF-Bypass, sofortige Löschung per Link) — `routes/web.php:1539,1687,1698,2829,2836,1610`
- ⚠️ **#27 [kritisch/routing]** DELETE-Aktionen über GET-Routen CSRF-ungeschützt — `routes/web.php:2278,2310,2475,2844`
- ⚠️ **#55 [hoch/blade]** E-Mail-Body wird unbereinigt als innerHTML in den DOM injiziert (XSS) — `resources/views/admin/lead_email/inbox/index.blade.php:1033`
- ⚠️ **#56 [hoch/blade]** Unescapiertes {!! !!} bei User-generierten Inhalten in Context-Feed-Views — `resources/views/admin/new_leads/layouts/context-feed/tasks.blade.php:79,137,153`
- ⚠️ **#57 [hoch/blade]** XSS durch unescaptes {!! !!} bei nutzergenerierten HTML-Feldern — `resources/views/admin/offer/folder-show.blade.php:5943 und :6282`
- ⚠️ **#58 [hoch/blade]** XSS durch {!! !!} bei nicht sanitizierten Nutzerdaten — `resources/views/admin/problem/profile.blade.php:2090,2174,2328`
- ⚠️ **#59 [hoch/blade]** XSS durch {!! !!} für Quill-Editor-Prefill in problem_edit — `resources/views/admin/problem/problem_edit.blade.php:1254`
- ⚠️ **#60 [hoch/blade]** XSS durch {!! !!} bei nutzergesteuertem short_description-HTML — `resources/views/admin/product/product/product_details.blade.php:1352`
- ⚠️ **#61 [hoch/crud]** update() und destroy() pruefen keine Berechtigungen – jeder authentifizierte Nutzer kann fremde Anfragen aendern oder loeschen — `app/Http/Controllers/Inquiry/InquiryController.php:1335,1515`
- ⚠️ **#105 [hoch/routing]** Destroy, Publish und Unpublish für E-Mail-Konfiguration über GET-Routen — `routes/web.php:3241-3243`
- ⚠️ **#107 [hoch/routing]** Destruktive GET-Routen: Löschen/Junk per HTTP-GET auslösbar — `routes/web.php:808,837,1119,1120,1121,1122`
- ⚠️ **#109 [hoch/routing]** Destruktive Deal-Aktionen über GET-Routen — `routes/web.php:4225–4235`
- ⚠️ **#110 [hoch/routing]** DELETE-Operationen über GET-Routen (CSRF-umgehend, Crawler-gefährdet) — `routes/web.php:3869`
- ⚠️ **#111 [hoch/routing]** Zustandsändernde Aktionen per GET-Request — `routes/web.php:1911,1917,1919,1935`
- ⚠️ **#112 [hoch/routing]** GET-Route für Datensatz-Löschen (asset_installment_destroy) — `routes/web.php:2654`
- ⚠️ **#113 [hoch/routing]** DELETE-Operationen über GET-Verben (CSRF-Bypass) — `routes/web.php:728,751,758,765`
- ⚠️ **#114 [hoch/routing]** Destruktive User- und Branch-Aktionen über GET-Routen (CSRF-Bypass, Bookmarkable Deletes) — `routes/web.php:2182, 2190, 2199, 684`
- ⚠️ **#120 [hoch/routing]** GET-Routen für destruktive Aktionen: >73 GET-Routen führen Delete/Destroy/Junk/Activate aus — `routes/web.php:684 (branch_destroy), 808 (new_lead_delete), 837 (lead_junk), 1120 (delete_lead_product), 4231 (deal_delete), u.v.m.`
- ⚠️ **#121 [hoch/routing]** GET /ticket/kanban/update/{ticket_id}/{stage} ändert Ticket-Status via GET — `routes/web.php:1935`

## 🟡 Tier C — Crash-/Logik-Bugs (kein Exploit, aber Funktion kaputt) — 26

- ⚠️ **#6 [kritisch/kausalitaet]** getTabCounts() referenziert undefinierte Klassen Task und Appointment — `app/Http/Controllers/EmployeeDashboardController.php:683-685`
- ⚠️ **#7 [kritisch/kausalitaet]** update() setzt Status jeder Anfrage zwingend auf 'Unpublished' – verifizierte Anfragen werden zurueckgestuft — `app/Http/Controllers/Inquiry/InquiryController.php:1360`
- ⚠️ **#8 [kritisch/kausalitaet]** Restore-Methoden auf Brand und Distributor ohne SoftDeletes-Trait – fatal error — `app/Http/Controllers/Contacts/AllContactController.php:994,1001`
- ⚠️ **#9 [kritisch/kausalitaet]** Leave-Genehmigung (approve) reduziert employees.remaining_day nicht — `app/Http/Controllers/Employee/Profile/LeaveController.php:230-258`
- ⚠️ **#10 [kritisch/kausalitaet]** QualificationController::destroy() löscht FurtherEducation statt Qualification — `app/Http/Controllers/Employee/Position/QualificationController.php:102-118`
- ⚠️ **#11 [kritisch/kausalitaet]** BegFundingsController::store() referenziert nicht-existierende Klasse BegFunding (Fatal Error) — `app/Http/Controllers/Customer/BegFundingsController.php:49`
- ⚠️ **#12 [kritisch/kausalitaet]** GarbageController prüft Berechtigung gegen falsche Spalte (user->name statt user->id) — `app/Http/Controllers/Admin/GarbageController.php:29`
- ⚠️ **#13 [kritisch/kausalitaet]** isAdmin-Middleware joiniert users.name (employee_id) mit user_rolls.user_id – immer falsch — `app/Http/Middleware/isAdmin.php:22-29`
- ⚠️ **#14 [kritisch/kausalitaet]** Zweistufige N+1-Query-Schleife beim Laden der Kundenliste im Dashboard — `app/Http/Controllers/EmployeeDashboardController.php:1821-1848`
- ⚠️ **#21 [kritisch/routing]** 4 Routen verweisen auf nicht existente Controller-Methoden (500) — `routes/web.php:4003,4037,4057,4668`
- ⚠️ **#64 [hoch/kausalitaet]** GET-Route lead.email.fetch triggert synchronen IMAP-Abruf mit set_time_limit(0) — `app/Http/Controllers/Email/LeadEmailReaderController.php:17-18 / routes/web.php:1388`
- ⚠️ **#65 [hoch/kausalitaet]** users.name wird als Fremdschlüssel zu employees.id verwendet – semantisch falsch — `app/Http/Controllers/Chat/ChatController.php:67 / 1217`
- ⚠️ **#66 [hoch/kausalitaet]** Status-String-Inkonsistenz: Store setzt einfaches Leerzeichen, Filter sucht doppeltes — `app/Http/Controllers/Customer/NewLeadsController.php:628,1064,3244,3261`
- ⚠️ **#67 [hoch/kausalitaet]** Neu-Anlegen-Modal in external.blade sendet an external.update statt external.store — `resources/views/admin/employee/external/external.blade.php:63`
- ⚠️ **#68 [hoch/kausalitaet]** ExternalDepartmentsController: orWhere ohne Gruppierung umgeht ID-Filter (Cross-Company-Datenleck) — `app/Http/Controllers/Product/Brand/ExternalDepartmentsController.php:27-34`
- ⚠️ **#69 [hoch/kausalitaet]** Komplettes Löschen (delete_type=complete) löscht keine physischen Dateien auf Disk — `app/Http/Controllers/Customer/Offer/OfferController.php:1354-1386`
- ⚠️ **#70 [hoch/kausalitaet]** EmployeeController::update() liest ID aus $_POST direkt statt Route Model Binding — `app/Http/Controllers/Employee/EmployeeController.php:653`
- ⚠️ **#71 [hoch/kausalitaet]** Wizard-Stage 1 (Produkteigenschaft) speichert keine Felder — `app/Http/Controllers/Product/ProductController.php:1067-1088`
- ⚠️ **#72 [hoch/kausalitaet]** Fehlermeldung bei Store/Update-Fehler zeigt Erfolgsmeldung — `app/Http/Controllers/Inventory/AssetInstallmentController.php:94, 172`
- ⚠️ **#73 [hoch/kausalitaet]** SQL-Logikfehler in AssetInstallmentController::show() – type-Filter wird durch orWhere gebrochen — `app/Http/Controllers/Inventory/AssetInstallmentController.php:109-112`
- ⚠️ **#74 [hoch/kausalitaet]** BEG-Förderungen: create()- und edit()-Views existieren nicht (ViewNotFoundException) — `app/Http/Controllers/Customer/BegFundingsController.php:19,66`
- ⚠️ **#75 [hoch/kausalitaet]** TaskPhaseController::clone() referenziert undefinierte Klasse TaskSubTasks (statt TaskSubTask) — `app/Http/Controllers/Phase/TaskPhaseController.php:453`
- ⚠️ **#76 [hoch/kausalitaet]** TaskPhaseController::storeNewPhase() erstellt unbeabsichtigt einen ProjectMontageChecklist-Datensatz — `app/Http/Controllers/Phase/TaskPhaseController.php:352-364`
- ⚠️ **#77 [hoch/kausalitaet]** N einzelne INSERTs statt Batch-Insert in syncPlannerItemEmployees — `app/Http/Controllers/Planner/PlannerPlanController.php:286-294`
- ⚠️ **#78 [hoch/kausalitaet]** N firstOrCreate-Aufrufe beim Speichern eines Overdue-Reports (ein Eintrag pro aktivem Mitarbeiter) — `app/Http/Controllers/Report/OverdueCenterController.php:1050-1060`
- ⚠️ **#79 [hoch/kausalitaet]** 145 Schema::hasTable/hasColumn-Aufrufe ohne Request-Caching in PlannerPlanController — `app/Http/Controllers/Planner/PlannerPlanController.php:182,247,470,562,612 (145 Stellen)`

## 🟢 Bereits widerlegt — FALSE POSITIVES (5) — nicht anfassen

- #24 OfferCommentController ohne Auth-Middleware – alle Endpoints unauthentifiziert aufrufbar — dynamisch — FALSE POSITIVE
- #28 is_Admin-Middleware wird im Produkt-Route-Group nicht angewandt — dynamisch — FALSE POSITIVE
- #32 Middleware-Typo: 'middlware' statt 'middleware' — Auth-Schutz komplett inaktiv — dynamisch — FALSE POSITIVE
- #117 GET /api/lead-name-suggestions und /api/lead-lastname-suggestions ohne Authentifizierung — dynamisch — FALSE POSITIVE (401 Unauthenticated)
- #122 GET /make_admin/{id} und GET /make_limit/{id} sind privilege-escalation-fähige GET-Routen — statisch — FALSE POSITIVE (Konstruktor-auth)

## ⚪ Übrige 59 bestätigte Funde (Architektur/Redundanz/Konsistenz/Workflow/Plausibilität)
Nicht in dieser Sicherheits-Arbeitsliste — siehe `befunde-bestaetigt.csv` / `regel-*.md`. Größtenteils Qualität/Refactor, kein direkter Exploit.