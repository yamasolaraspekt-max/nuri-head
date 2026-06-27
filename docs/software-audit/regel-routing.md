# Audit – Routing

Funde: 57  ·  🔴 15 kritisch · 🟠 25 hoch · 🟡 14 mittel · ⚪ 3 niedrig

### 🔴 Öffentliche Route gibt LeadEmail-Inhalte ohne Authentifizierung zurück  
**Modul:** CRM – Kommunikation · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:599`  
**Problem:** Route::get('lead/email/api/{id}', [WebsiteController::class, 'getEmailDetails']) liegt außerhalb jeder Auth-Middleware-Gruppe und gibt Absender, Betreff, Body und Domain von Lead-E-Mails zurück. Jeder unauthentifizierte Besucher kann durch Enumeration aller IDs sämtliche eingehenden Kunden-E-Mails lesen. Der WebsiteController enthält keinerlei Auth-Check für diese Methode.  
**Fix:** Die Route in eine auth-geschützte Gruppe verschieben oder die Route zugunsten der bereits existierenden Route lead.email.show (admin/lead-email/show/{id}, geschützt) entfernen. Beide Endpunkte tun dasselbe.

### 🔴 4 Routen verweisen auf nicht existente Controller-Methoden (500)  
**Modul:** CRM – Anfragen · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:4003,4037,4057,4668`  
**Problem:** Vier registrierte Routen erzeugen zur Laufzeit eine MethodNotAllowedException bzw. einen 500-Fehler, weil die zugeordneten Methoden im Controller nicht existieren: (1) GET /inquiry_publish/{id} → InquiryController@publish (Methode fehlt komplett); (2) POST /admin/inquiries/ai-save → InquiryController@storeFromAI (Methode fehlt); (3) GET /admin/fusion-forms/import → FusionFormSubmissionController@importFromGoneo (Methode fehlt); (4) POST /fusion/import/one → FusionFormSubmissionController@importFusionEntryToInquiry (Methode fehlt). Alle vier Endpunkte sind im Frontend erreichbar, liefern aber stets einen Fehler.  
**Fix:** Methoden implementieren oder Routen entfernen. Bis dahin temporaer 501 NotImplemented zurueckgeben oder Route auskommentieren, damit das Routing-Register sauber bleibt.

### 🔴 GET-Routen fuer destruktive Operationen (Delete, Junk, Restore, Verify)  
**Modul:** CRM – Anfragen · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:4002,4003,4004,4005,4006,4009,4107`  
**Problem:** Sieben Routen verwenden HTTP GET fuer zustandsaendernde/destruktive Operationen: GET /inquiry_delete/{id} (loescht), GET /inquiry_publish/{id} (veroeffentlicht), GET /inquiry_verify/{id} (verifiziert), GET|POST /inquiry_junk/{id} (markiert als Junk), GET|POST /inquiry_unjunk/{id} (entfernt Junk), GET /inquiry_restore/{id} (stellt wieder her), GET /inquiry_type_destroy/{id} (loescht Typ). GET-Requests werden von Browsern vorgeladen (Prefetch, Google Bot), koennen in Logs landen und sind nicht CSRF-geschuetzt. Ein einfacher Link in einer E-Mail oder einer externen Seite koennte Datensaetze loeschen.  
**Fix:** Alle Zustandsaenderungen auf POST/PUT/PATCH/DELETE umstellen. Vorhandene REST-Routen (z.B. DELETE inquiries/{inquiry}/discard) als Vorbild nehmen und die alten Routen entfernen.

### 🔴 AllContactController ohne Auth-Middleware: alle Kontakte und Export öffentlich  
**Modul:** CRM – Partner · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:4609-4615`  
**Problem:** Die Routen /all-contacts, /all-contacts/export, /global-search und /global-restore/* liegen in einem Route::middleware('web')-Block ohne 'auth'. Der AllContactController hat keinen Middleware-Aufruf im Konstruktor. Damit kann jeder anonyme Nutzer die konsolidierte Kontaktliste (Kunden, Mitarbeiter, Hersteller, Lieferanten), den CSV-Export und die Restore-Endpunkte aufrufen.  
**Fix:** Middleware-Gruppe auf ['web', 'auth'] ändern oder im AllContactController-Konstruktor $this->middleware('auth') ergänzen.

### 🔴 OfferCommentController ohne Auth-Middleware – alle Endpoints unauthentifiziert aufrufbar  
**Modul:** Vertrieb – Angebote · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/Offer/OfferCommentController.php:1-57`  
**Problem:** OfferCommentController (index, store, update, destroy) besitzt weder einen __construct-Aufruf mit $this->middleware('auth') noch ist er in einer Route-Gruppe mit auth-Middleware registriert (routes/web.php Zeile 108 zeigt nur den use-Import, keine Route-Registrierung im auth-Block). Damit können Kommentare ohne Login gelesen, erstellt, editiert und gelöscht werden. Zusätzlich fehlt in store() jede Validierung der Eingaben (customer_id, comment etc.).  
**Fix:** Sofort __construct mit $this->middleware('auth') ergänzen. Alle Routen in einen Route::middleware(['auth'])-Block verschieben. Request-Validierung (FormRequest oder Validator::make) für store/update ergänzen.

### 🔴 Destruktive Operationen über GET-Routen (CSRF-Bypass, sofortige Löschung per Link)  
**Modul:** Personal / HR · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:1539,1687,1698,2829,2836,1610`  
**Problem:** Mindestens 6 HR-Löschaktionen sind als GET-Routen registriert: emp_destroy/{id} (Mitarbeiterlöschen), holiday_destroy/{id}, leave_day_destroy/{id}, country_destroy/{id}, tax_destroy/{id}, contract_type_destroy/{id}. GET-Requests werden von Browsern verfolgt (Prefetch, Logs, History, eingebettete Links), unterliegen keinem CSRF-Schutz und erlauben es jedem, der eine URL mit gültiger ID kennt, den Datensatz ohne Bestätigungs-Token zu löschen. Ein normaler Nutzer kann über einen manipulierten Link alle Mitarbeiterdatensätze löschen.  
**Fix:** Routen auf DELETE umstellen (Route::delete), Formulare mit @csrf und @method('DELETE') absichern. Alternativ AJAX-Call mit X-CSRF-Token. Alle 70+ im Projekt gefundenen GET-Destroy-Routen systematisch migrieren.

### 🔴 Leave-Notiz-Endpunkte ohne Auth-Middleware öffentlich erreichbar  
**Modul:** Personal / HR · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:1810-1815`  
**Problem:** Der Prefix-Block 'Route::prefix("leaves")' hat keinerlei Middleware (weder 'auth' noch 'web'). Damit sind GET /leaves/{id}/notes, POST /leaves/{id}/notes/store, PUT /leaves/{id}/notes/update/{index} und DELETE /leaves/{id}/notes/delete/{index} ohne Authentifizierung abrufbar. Jeder Internetzugang kann Urlaubsnotizen lesen, anlegen, ändern und löschen.  
**Fix:** Route::prefix('leaves')->middleware(['web','auth'])->group(...) — identisch wie der Block ab Zeile 1784 für die übrigen Leave-Routen.

### 🔴 DELETE-Aktionen über GET-Routen CSRF-ungeschützt  
**Modul:** Artikel · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:2278,2310,2475,2844`  
**Problem:** Die Routen /product_destroy/{id}, /product_description_destroy/{id}, /measure_destroy/{id} und /discount_group_destroy/{id} sind alle als GET definiert. Damit können Datensätze durch einfaches Einbetten einer URL (z. B. <img src="/product_destroy/5">) ohne CSRF-Schutz irreversibel gelöscht werden.  
**Fix:** Auf DELETE-Verb umstellen (Route::delete) und in den Blade-Views Löschformulare mit @method('DELETE') und @csrf verwenden.

### 🔴 is_Admin-Middleware wird im Produkt-Route-Group nicht angewandt  
**Modul:** Artikel · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:2275`  
**Problem:** Route::group(['middleware' => 'web', 'is_Admin'], ...) setzt den Schlüssel 'middleware' auf den String 'web'; der Eintrag 'is_Admin' liegt an Array-Index 0 und wird von Laravel als unbekannter Gruppenattribut-Schlüssel ignoriert. Dadurch gilt für alle Produkt-CRUD-Routen (Anlage, Änderung, Löschung) ausschließlich 'web' – kein Admin-Rollenschutz. Jeder authentifizierte Nutzer darf Produkte anlegen, bearbeiten und löschen.  
**Fix:** Route::middleware(['web', 'is_Admin'])->group(...) verwenden, oder innerhalb der Gruppe ->middleware('is_Admin') je Route setzen.

### 🔴 PurchaseRequest- und RequestOut-Routen ohne Auth-Middleware  
**Modul:** Lager · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:2696, 2716`  
**Problem:** Beide Route-Gruppen verwenden ausschließlich middleware('web'). Die 'web'-Gruppe enthält laut Kernel.php (Zeile 35–43) kein 'auth'. Weder PurchaseRequestController noch InventoryRequestOutController setzen im Konstruktor middleware('auth'). Unauthentifizierte Nutzer können so alle Purchase-Request- und Request-Out-Endpunkte (index, list, analytics, store, destroy) aufrufen. auth()->user() wird in diesen Controllern nur mit ?? 'System'-Fallback verwendet, was den Fehler stumm schluckt.  
**Fix:** middleware('web') in beiden Route::group-Aufrufen durch middleware(['web','auth']) ersetzen.

### 🔴 GoodsReceipt-Routen ohne Auth-Middleware  
**Modul:** Lager · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:5096`  
**Problem:** Route::prefix('admin')->name('admin.') bei Zeile 5096 enthält kein ->middleware('auth'). GoodsReceiptController hat keinen __construct mit middleware-Aufruf. index() und data() führen gar keine Auth-Prüfung durch. relationOptions(), show() ebenfalls. Nur store/update/destroy nutzen authEmployeeId(), die bei fehlendem Login null zurückgibt ohne Redirect.  
**Fix:** Die Route-Gruppe auf ->middleware(['auth']) oder ->middleware(['web','auth']) ändern; alternativ im Controller-Konstruktor middleware('auth') hinzufügen.

### 🔴 BEG-Förderungen ohne Auth-Middleware (unauthentifizierter Zugriff)  
**Modul:** Finanzen · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:860`  
**Problem:** Route::resource('beg-fundings', ...) liegt im 'web'-Only-Middleware-Gruppe (Zeile 769). Kein 'auth'-Middleware. BegFundingsController hat auch keinen __construct()-Middleware-Aufruf. Index, Store, Update und Destroy der BEG-Förderdaten sind somit ohne Login aufrufbar.  
**Fix:** Die Route in eine auth-gesicherte Gruppe verschieben oder im BegFundingsController $this->middleware('auth') im Konstruktor ergänzen.

### 🔴 Middleware-Typo: 'middlware' statt 'middleware' — Auth-Schutz komplett inaktiv  
**Modul:** Querschnitt · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:1444`  
**Problem:** Route::group(['middlware'=>'auth'], function(){...}) verwendet den falschen Schlüssel 'middlware'. Laravel ignoriert unbekannte Keys stillschweigend, sodass alle 5 darin enthaltenen Routen (customer_phase_manage, customer/phase/manage, customer_phase_get, customer_phase_get_new, customer_phase_management_store, customer_phase_management/color, customer_phase_management_delete) ohne Authentifizierung erreichbar sind.  
**Fix:** Schlüssel in 'middleware' korrigieren: Route::group(['middleware'=>'auth'], function(){...}). Anschließend sicherstellen, dass betroffene Routen wirklich auth-geschützt sind.

### 🔴 GET /route-cache führt Artisan-Befehle aus — kein Auth-Schutz  
**Modul:** Querschnitt · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:3961`  
**Problem:** Route::get('/route-cache', function(){ Artisan::call('route:cache'); Artisan::call('view:clear'); ... }) liegt außerhalb jeder Middleware-Gruppe. Jeder anonyme HTTP-Request kann damit den Route- und View-Cache der Produktion manipulieren (Cache Poisoning / DoS).  
**Fix:** Route entfernen oder in eine auth- und admin-only-Middleware-Gruppe verschieben. Für Cache-Clearing besser einen Artisan-Befehl direkt auf dem Server ausführen.

### 🔴 GET /fix-notes schreibt Datenbankdaten ohne Authentifizierung  
**Modul:** Querschnitt · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `routes/web.php:404`  
**Problem:** Route::get('/fix-notes', function(){...}) führt DB-Schreiboperationen auf CustomerNote-Datensätzen durch (note->save()) und ist nicht durch Auth-Middleware geschützt. Jeder anonyme Benutzer kann diesen Migrations-Helper beliebig oft aufrufen.  
**Fix:** Route nach der Datenmigration komplett entfernen. Solange sie benötigt wird: in eine auth+admin-Middleware-Gruppe verschieben.

### 🟠 Doppelte POST-Route für reportStore unter zwei verschiedenen URIs und Namen  
**Modul:** Dashboard & Berichte · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:637,642`  
**Problem:** admin/overdue-center/reports (POST, Name: overdue.reports.store) und admin/overdue-center/report-store (POST, Name: overdue-center.report.store) zeigen auf dieselbe Methode OverdueCenterController@reportStore. Diese Redundanz führt zu inkonsistenter Nutzung im Frontend, erschwerter Wartung und Verwirrung beim Debugging.  
**Fix:** Einen der beiden Einträge (vorzugsweise report-store als veraltetes Muster) entfernen und alle Frontend-Aufrufe auf die eine Route admin/overdue-center/reports zeigen lassen.

### 🟠 toggle-status und testConnection für E-Mail-Konten ohne Auth-Middleware  
**Modul:** CRM – Kommunikation · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:1380-1381`  
**Problem:** Route::post('/admin/lead-email-accounts/toggle-status/{id}') und Route::post('/admin/lead-email-accounts/test/{id}') liegen innerhalb eines Route::group(['middleware' => 'web'])-Blocks, aber explizit AUSSERHALB des inneren ['auth']-Blocks. Dadurch kann jeder nicht-eingeloggte Nutzer den Status eines E-Mail-Kontos umschalten oder eine IMAP-Verbindung auf fremde Server testen (SSRF-Risiko).  
**Fix:** Beide Routen in den bestehenden prefix('admin')->middleware(['auth'])-Block (Zeile 1376) verschieben.

### 🟠 Destroy, Publish und Unpublish für E-Mail-Konfiguration über GET-Routen  
**Modul:** CRM – Kommunikation · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:3241-3243`  
**Problem:** Route::get('/email_configuration_destroy/{id}') löscht einen Datensatz, Route::get('/email_config_publish/{id}') und ::get('/email_config_unpublish/{id}') ändern den Status. GET-Requests dürfen keine Zustandsänderungen auslösen (HTTP-Standard). Diese URLs können von Browsern, Crawlern oder in <img>-Tags referenziert werden, was zu CSRF-artigen Angriffen und ungewollten Löschungen führt.  
**Fix:** DELETE/POST verwenden und CSRF-Token prüfen: Route::delete('/email-configurations/{id}', …) mit CSRF-Schutz. GET-Routen für delete/publish entfernen.

### 🟠 Doppelt registrierte Route fusion.webhook.ajax (unterschiedliche Controller)  
**Modul:** CRM – Anfragen · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:666-667`  
**Problem:** Dieselbe URI POST /fusion/webhook/ajax wird zweimal mit demselben Namen fusion.webhook.ajax registriert – einmal fuer FusionFormSubmissionController@webhookAjax, einmal fuer FusionWebhookController@handleAjax. Laravel verwendet immer die zuletzt registrierte Route; FusionFormSubmissionController@webhookAjax ist damit tot. Der handleAjax-Controller fuehrt hingegen gar keine Sync-Logik aus (count bleibt 0).  
**Fix:** Einen der beiden Eintraege entfernen und sicherstellen, dass die korrekte Methode (webhookAjax mit der echten Importlogik) aktiv ist. Route umbenennen, falls beide benoetigt werden.

### 🟠 Destruktive GET-Routen: Löschen/Junk per HTTP-GET auslösbar  
**Modul:** CRM – Leads & Kunden · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:808,837,1119,1120,1121,1122`  
**Problem:** Mehrere destruktive Aktionen sind per GET erreichbar: GET /new_lead_delete/{id} ruft destroy() auf (L808), GET /lead_junk/{id} ruft junk() auf (L837), GET /delete_lead_responsible/{id} (L1119), GET /delete_lead_product/{id} (L1120), GET /delete_lead_alternative/{id} (L1121), GET /junk_lead_alternative/{id} (L1122). GET-Anfragen werden von Browsern vorabgecacht, in Proxy-Logs gespeichert, per Link-Prefetching automatisch ausgelöst und sind CSRF-offen. Zugleich existieren parallele POST-Routen mit demselben URI (z.B. POST /new_lead_delete/{id} auf L796, POST /lead_junk/{id} auf L797) – doppelte Endpunkte mit verschiedenen Verben/Actions.  
**Fix:** GET-Routen für destruktive Aktionen entfernen. Nur POST/DELETE mit @csrf-Token verwenden. Formulare in Views entsprechend anpassen (method_field). Die Legacy-GET-Routen können als @deprecated mit 301-Redirect auf eine Fehlerseite vorübergehend belassen werden.

### 🟠 Destruktive Aktionen (destroy, publish) über GET-Requests erreichbar  
**Modul:** CRM – Partner · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:2492,2493,2520,2527,2559,2560,2561`  
**Problem:** brand.destroy (/brand/destroy/{id}), brand.publish, external_destroy, external_department_delete, distributor_destroy und distributor_publish/unpublish sind als GET-Routen definiert. GET-Requests dürfen gemäß HTTP-Standard keine Seiteneffekte haben. Browser-Prefetch, Crawler oder CSRF-ungeschützte Links können Datensätze unbeabsichtigt löschen oder veröffentlichen. In brand.blade.php (Zeile 809) ist die destroy-Aktion als einfacher <a href>-Link implementiert.  
**Fix:** Destruktive Aktionen auf POST/DELETE umstellen, mit @csrf und entsprechenden Form-Wrappern oder JavaScript-fetch mit CSRF-Token versehen.

### 🟠 Destruktive Deal-Aktionen über GET-Routen  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:4225–4235`  
**Problem:** deal_junk/{id}, deal_unjunk/{id}, deal_delete/{id} und deal_restore/{id} sind als Route::get() definiert. Damit können Aufträge durch ein einfaches Link-Klick (oder CSRF-losen Browser-Request) gelöscht, als Junk markiert oder wiederhergestellt werden. GET darf laut HTTP-Standard keinen State ändern.  
**Fix:** Auf Route::patch/delete umstellen. Im Frontend per Form+CSRF oder per AJAX-DELETE/-PATCH Request aufrufen. Alternativ die bestehenden bulk-action POST-Endpunkte nutzen.

### 🟠 DELETE-Operationen über GET-Routen (CSRF-umgehend, Crawler-gefährdet)  
**Modul:** Projekte & Planer · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:3869`  
**Problem:** Mindestens drei Lösch-/Restore-Aktionen im Modul sind als GET registriert: `GET /appointments/destroy/{id}` (Z. 3869), `GET /personal_task_delete/{id}` (Z. 3635, web.php), `GET /personal_task_restore/{id}`. GET-Anfragen sind nicht CSRF-geschützt, können von Web-Crawlern, Browser-Prefetch oder eingebetteten Bildern (`<img src="/appointments/destroy/5">`) ausgelöst werden und vernichten Daten ohne Nutzerinteraktion.  
**Fix:** Alle Lösch-Routen auf `Route::delete(...)` umstellen. Im Frontend Formulare mit `@method('DELETE')` oder explizite Ajax-DELETE-Requests verwenden. Die alten GET-Routen können mit einer Weiterleitung auf eine Fehlerseite ersetzt werden.

### 🟠 Zustandsändernde Aktionen per GET-Request  
**Modul:** Support – Tickets · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:1911,1917,1919,1935`  
**Problem:** Vier Routen, die Datenbankzustand verändern, sind als GET definiert: problem_destroy/{id} (löscht), problem_open/{id} (Status-Wechsel), problem_progress/{id} (Status-Wechsel) und ticket/kanban/update/{ticket_id}/{stage} (Status-Wechsel). GET-Requests dürfen nach HTTP-Standard keine Seiteneffekte haben. Dadurch können Browser-Prefetch, Link-Crawler oder eingebettete Bilder (img src) versehentlich Datensätze löschen oder Statusänderungen auslösen. CSRF-Schutz greift bei GET nicht.  
**Fix:** problem_destroy auf DELETE, problem_open/problem_progress auf PATCH/POST, ticket/kanban/update auf PATCH umstellen. Im Frontend statt einfachem Link ein Mini-Formular oder AJAX mit korrektem HTTP-Verb verwenden.

### 🟠 GET-Route für Datensatz-Löschen (asset_installment_destroy)  
**Modul:** Lager · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:2654`  
**Problem:** Route::get('asset_installment_destroy/{id}', ...) löscht Ratenzahlungs-Datensätze per GET-Request. Das erlaubt CSRF-freies Löschen durch einfache Link-Aufrufe (Browser-Prefetch, E-Mail-Verlinkung etc.).  
**Fix:** Auf Route::delete(...) umstellen und im Blade einen DELETE-Request (z.B. via Form-Methode-Spoofing @method('DELETE')) verwenden.

### 🟠 DELETE-Operationen über GET-Verben (CSRF-Bypass)  
**Modul:** Finanzen · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:728,751,758,765`  
**Problem:** Alle Lösch-Routen im Branch-Expense-Modul (BranchExpense, BranchRent, BranchInsurance, OtherCost) sind als GET registriert (z.B. /branch_expense_delete/{branchExpense}). Ein einfacher Link reicht aus, um Datensätze zu löschen – auch per versehentlichem Bot-Aufruf oder CSRF-via-GET. HTTP-Semantik verbietet Zustandsänderungen über GET.  
**Fix:** Routen auf DELETE umstellen (Route::delete). Im Frontend statt direktem Link ein Mini-Formular mit @csrf und @method('DELETE') oder einen AJAX-DELETE-Call verwenden.

### 🟠 Destruktive User- und Branch-Aktionen über GET-Routen (CSRF-Bypass, Bookmarkable Deletes)  
**Modul:** Admin & System · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:2182, 2190, 2199, 684`  
**Problem:** Route::get('/user_destroy/{id}'), Route::get('/admin_destroy/{id}'), Route::get('/limit_destroy/{id}') und Route::get('/branch_destroy/{id}') löschen Datensätze per HTTP-GET. GET-Requests unterliegen keinem CSRF-Schutz (Laravels VerifyCsrfToken greift nur bei POST/PUT/PATCH/DELETE). Ein eingebettetes <img src="/admin_destroy/1"> auf einer externen Seite löscht den Benutzer, sobald ein angemeldeter Admin sie aufruft. Zusätzlich: GET-Löschen verstößt gegen REST und kann von Bots/Prefetchern ausgelöst werden.  
**Fix:** Alle destroy-Routen auf DELETE-Methode umstellen; in den Views form method="POST" mit @method('DELETE') und @csrf verwenden.

### 🟠 Doppelter Auth::routes()-Aufruf erzeugt mehrfach registrierte Login-/Auth-Routen  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:602 und routes/web.php:3969`  
**Problem:** Auth::routes() wird an zwei verschiedenen Stellen aufgerufen. Laravel überschreibt dabei zwar bestehende gleichnamige Routen, jedoch kann das Routing-Caching zu unvorhersehbarem Verhalten führen. Außerdem bleibt /register in beiden Fällen öffentlich zugänglich (für B2B-interne Anwendungen meist unerwünscht).  
**Fix:** Einen der beiden Auth::routes()-Aufrufe entfernen. Die /register-Route explizit deaktivieren: Auth::routes(['register' => false]), sofern die App keine Selbstregistrierung vorsieht.

### 🟠 api/secure/master-sets fehlt Authentifizierungsmiddleware — nur Throttle  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/api.php:206-220`  
**Problem:** Die als 'secure' bezeichneten Routen GET /api/secure/master-sets und GET /api/secure/master-sets-debug sind ausschließlich mit throttle:60,1 geschützt, aber ohne auth:sanctum oder vergleichbare Middleware. Der Name 'secure' suggeriert einen beabsichtigten Schutz, der tatsächlich fehlt. /master-sets-debug-Endpunkte können besonders sensible interne Daten preisgeben.  
**Fix:** ->middleware(['auth:sanctum', 'throttle:60,1']) zu beiden Routengruppen hinzufügen. Den debug-Endpunkt nur in Nicht-Produktionsumgebungen aktivieren.

### 🟠 GET /api/lead-name-suggestions und /api/lead-lastname-suggestions ohne Authentifizierung  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/api.php:54-58`  
**Problem:** Beide Endpunkte führen unbeschränkte LIKE-Queries auf der NewLeads-Tabelle aus (customer names, lastnames) und sind für jeden ohne Token erreichbar. Das ermöglicht Customer-Enumeration über die öffentliche API.  
**Fix:** ->middleware('auth:sanctum') hinzufügen, analog zu den anderen API-Routen in derselben Datei.

### 🟠 Kollidierende Route-Namen: 'fusion.webhook.ajax' zweimal mit verschiedenen Controllern  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:666-667`  
**Problem:** POST /fusion/webhook/ajax ist auf zwei aufeinanderfolgende Zeilen mit demselben Route-Namen, aber verschiedenen Controllern registriert (FusionFormSubmissionController@webhookAjax und FusionWebhookController@handleAjax). Laravel verwendet immer die zuletzt registrierte, sodass der erste Controller-Call nie ausgeführt wird. Dies führt zu stillschweigendem Funktionsverlust.  
**Fix:** Dopplung auflösen: entweder beide Endpunkte mit unterschiedlichen Namen und URIs versehen, oder die Logik in einem einzelnen Controller zusammenführen.

### 🟠 Kollidierende Route-Namen: 'branch.address.update' für zwei verschiedene Routen  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:691 und routes/web.php:703`  
**Problem:** POST /branch_address_update (BranchController@addressUpdate) und POST /branch_address_update (BranchAddressController@update) haben denselben Route-Namen. Die zweite Registrierung überschreibt die erste.  
**Fix:** Routen umbenennen: branch.address.update.main und branch.address.update oder in eine einzige Methode konsolidieren.

### 🟠 GET-Routen für destruktive Aktionen: >73 GET-Routen führen Delete/Destroy/Junk/Activate aus  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:684 (branch_destroy), 808 (new_lead_delete), 837 (lead_junk), 1120 (delete_lead_product), 4231 (deal_delete), u.v.m.`  
**Problem:** Über 73 Routen verwenden HTTP GET für destruktive oder zustandsändernde Aktionen (destroy, delete, junk, unjunk, active, deactive, approve, publish, unpublish). GET-Requests werden vom Browser prefetched, von Proxies gecacht und in Logs gespeichert. Ein einfacher Link-Aufruf oder das Crawlen der App würde Daten löschen oder Status verändern.  
**Fix:** Destruktive Aktionen (destroy, delete, junk) auf DELETE umstellen. Zustandsänderungen (active/deactive, publish/unpublish) auf PATCH/POST umstellen. Blade-Templates und JS-Aufrufe entsprechend mit Formularen oder fetch-Requests anpassen.

### 🟠 GET /ticket/kanban/update/{ticket_id}/{stage} ändert Ticket-Status via GET  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:1935`  
**Problem:** Die Route GET ticket/kanban/update/{ticket_id}/{stage} ruft ProblemController@updateStage auf. Eine Statusänderung per GET ist semantisch falsch, CSRF-ungeschützt (GET hat keinen CSRF-Token) und anfällig für Browser-Prefetch oder Cross-Site-Request.  
**Fix:** Auf PATCH /ticket/kanban/{ticket_id}/stage umstellen und CSRF-Schutz sicherstellen.

### 🟠 GET /make_admin/{id} und GET /make_limit/{id} sind privilege-escalation-fähige GET-Routen  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:2192-2193`  
**Problem:** Beide Routen ändern User-Privilegien (Admin-Status, Limit-Status) über HTTP GET. Zwar in einer auth-Gruppe, aber ohne CSRF-Token und nicht gegen CSRF-Angriffe via IMG-Tags oder Links gesichert.  
**Fix:** Auf POST oder PATCH umstellen und explizit admin-only-Gate hinzufügen: Route::post('/users/{user}/make-admin', ...) mit Gate::authorize('admin').

### 🟠 GET /dispatch-chat-jobs und /chat-jobs ohne Authentifizierung  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:4514-4515`  
**Problem:** Beide Routen (dispatch-chat-jobs/{startId}/{endId}/{chunkSize?} und chat-jobs/{startId}/{endId}/{chunkSize?}) stehen außerhalb jeder Middleware-Gruppe. Sie dispatchen Queue-Jobs und könnten für DoS-Angriffe oder unerwünschte Datenbankoperationen missbraucht werden.  
**Fix:** In eine auth-Gruppe verschieben oder durch ein API-Secret sichern. Wenn nur intern, Route entfernen und direkt per Artisan/Scheduler aufrufen.

### 🟠 GET /run-backfill-phase-sections führt Artisan-Befehl ohne Auth aus  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php:4519`  
**Problem:** Route ruft Artisan::call('backfill:phase-sections') ohne jegliche Middleware auf. Damit kann jeder anonyme Benutzer einen potenziell ressourcenintensiven Backfill-Job triggern.  
**Fix:** Route entfernen (einmalige Migrations sollten über Artisan direkt laufen) oder in eine auth+admin-Gruppe verschieben.

### 🟡 Route /home hat Tippfehler im Namen ('dashbaord' statt 'dashboard')  
**Modul:** Dashboard & Berichte · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:605`  
**Problem:** Route::get('/home', ...)->name('dashbaord') enthält den Tippfehler dashbaord statt dashboard. Wenn Code oder Views route('dashbaord') verwendet, funktioniert es zufällig; Suchoperationen nach 'dashboard' finden diese Route nicht.  
**Fix:** Umbenennen auf 'home' (wie die /-Route) oder 'dashboard.home'. route('dashbaord')-Vorkommen in Views/Controllern prüfen und anpassen.

### 🟡 6 separate Route::group-Blöcke mit demselben middleware/prefix/name für 'dashboard.*'  
**Modul:** Dashboard & Berichte · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:440,452,456,466,476`  
**Problem:** Der Dashboard-Namespace ist auf mindestens 6 separate Route-Gruppen aufgeteilt (alle mit prefix('dashboard') und name('dashboard.')), anstatt in einer einzigen Gruppe zu stehen. Das erschwert die Übersicht, erhöht das Risiko von Namenskollisionen und macht Middleware-Anpassungen fehleranfällig.  
**Fix:** Alle dashboard.-Routen in einer einzigen Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group()-Gruppe zusammenführen.

### 🟡 export/csv und export/pdf Routen sind Stubs ohne Implementierung  
**Modul:** CRM – Kommunikation · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:1391-1392`  
**Problem:** Beide Routen geben literalen Text 'TODO: CSV Export' bzw. 'TODO: PDF Export' zurück. Im View (inbox/index.blade.php:546+555) sind die Buttons mit echten route()-Aufrufen verlinkt, sodass Nutzer auf diese Buttons klicken und rohen Text als Response erhalten.  
**Fix:** Entweder die Buttons im View temporär ausblenden/deaktivieren, bis die Implementierung fertig ist, oder die Route auf einen Controller-Stub umleiten, der eine 501-Antwort mit einer Fehlermeldung zurückgibt.

### 🟡 Doppelt definierte Route inventory/product-data  
**Modul:** Lager · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:2571-2572`  
**Problem:** Die Route GET /inventory/product-data/{product_id} mit Name 'inventory.product.data' ist zweimal identisch definiert. Nur die zweite Registrierung ist in der Laravel-Routentabelle aktiv; die erste ist tot. Dies verwirrt Entwickler und kann bei Refactoring zu Fehlern führen.  
**Fix:** Zeile 2572 entfernen.

### 🟡 DeliveryNote-Update via POST statt PUT/PATCH  
**Modul:** Lager · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:2677`  
**Problem:** Route::post('/{deliveryNote}', ...) wird für das Update verwendet. REST-Konvention fordert PUT oder PATCH. Alle anderen Update-Routen im Modul (z.B. GoodsReceipt Line 5112) verwenden korrekt PUT.  
**Fix:** Auf Route::put oder Route::patch umstellen; im zugehörigen Frontend-Aufruf @method('PUT') bzw. den Axios-Verb anpassen.

### 🟡 Stage-Update-Route verwendet POST statt PUT/PATCH (REST-Verstoß)  
**Modul:** Admin & System · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:3193`  
**Problem:** Route::post('update/{id}', [StageController::class, 'update']) – Gemäß REST-Konventionen sollte eine Update-Operation PUT oder PATCH verwenden. Das neue Admin-Stage-Modul (admin/stages) nutzt POST sowohl für store (admin/stages/store) als auch für update (admin/stages/update/{id}), was nicht differenzierbar ist. Im Kontrast dazu verwenden verwandte Kanban-Routen korrekt PUT/PATCH/DELETE.  
**Fix:** Route auf Route::put/patch ändern; im Frontend _method-Spoofing verwenden.

### 🟡 inquiry_junk und inquiry_unjunk akzeptieren GET und POST (Route::match)  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:4005`  
**Problem:** Route::match(['get', 'post'], '/inquiry_junk/{id}', ...) ermöglicht sowohl GET als auch POST für eine zustandsändernde Aktion. GET-Requests können unbeabsichtigt durch Browser oder Crawler den Junk-Status setzen.  
**Fix:** Auf Route::post('/inquiry_junk/{id}', ...) beschränken und alle Aufrufer auf POST umstellen.

### 🟡 chat/group/leave/{id} und chat/group/leave/{group} sind identische URIs mit verschiedenen Namen  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:4693 und routes/web.php:4697`  
**Problem:** POST /chat/group/leave/{id} und POST /chat/group/leave/{group} sind für denselben Controller und dieselbe Methode (ChatGroupController@leave) registriert. Dies sind zwei identisch aufgelöste URIs mit nur verschiedenen Wildcard-Namen. Laravel registriert beide, aber die zweite überschattet die erste im Cache.  
**Fix:** Eine der beiden Routen entfernen. Die verbleibende Route mit dem eindeutigen Parameter-Namen benennen: /chat/group/leave/{groupId}.

### 🟡 Inkonsistente URI-Konvention: Mischung aus snake_case und kebab-case im selben Modul  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:826 (lead_product_list), routes/web.php:1124 (lead-product-lists)`  
**Problem:** Das Lead-Modul verwendet sowohl snake_case-URIs (lead_product_list, new_lead_save, lead_junk) als auch kebab-case-URIs (lead-product-lists, new-leads/{id}/history-feed, lead/kanban). Dutzende Module zeigen dieselbe Inkonsistenz. Besonders auffällig: POST /lead_product_list und GET /lead_product_lists (mit s) zeigen auf denselben Controller-Method, was verwirrend ist.  
**Fix:** Einheitlich auf kebab-case umstellen (Laravel-Standard). Bestehende snake_case-Routen als Legacy-Routen mit Weiterleitungen deprecaten. Naming-Convention im Team festlegen.

### 🟡 Kollidierende Route-Namen: 'ajax.distributors' doppelt vergeben  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:522 und routes/web.php:2301`  
**Problem:** Der Name ajax.distributors ist sowohl für GET /admin/supplier-connectors/ajax/distributors (SupplierConnectionController@select2Distributors) als auch für GET /ajax/distributors (DistributorController@index) registriert. route('ajax.distributors') löst auf die zuletzt registrierte Route auf.  
**Fix:** Den ersten auf admin.supplier-connectors.ajax.distributors umbenennen (passend zum existierenden Präfix).

### 🟡 Kollidierende Route-Namen: 'customers.search' dreifach vergeben  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:2672, 3727, und 4759`  
**Problem:** Der Route-Name customers.search wird an drei verschiedenen Stellen für verschiedene Controller-Methoden vergeben. Laravel löst immer die letzte Registrierung auf, die vorherigen Vorkommen sind für Blade und JS unsichtbar.  
**Fix:** Routen kontextspezifisch benennen: offers.customers.search, kanban.customers.search, ai.customers.search.

### 🟡 GET /activities_destroy/{id} führt Löschung per GET aus  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `routes.txt:10 (activities.destroy)`  
**Problem:** Phase/PhaseActivitiesController@destroy ist als GET-Route registriert. Destruktive Aktionen sollten DELETE-Verb verwenden.  
**Fix:** Route::delete('/activities/{id}', ...) verwenden.

### 🟡 Kollidierende Route-Namen: 'daily.report.delete' doppelt vergeben  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:1867 und routes/web.php:1883`  
**Problem:** Der Route-Name daily.report.delete ist für zwei verschiedene Routen registriert. Die zweite Registrierung überschreibt die erste.  
**Fix:** Eine der Routen umbenennen und prüfen, ob beide wirklich benötigt werden.

### 🟡 planner/plans/sync akzeptiert GET und POST — GET für Sync-Aktion semantisch falsch  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:5263`  
**Problem:** Route::match(['GET', 'POST'], '/plans/sync', ...) ist laut Kommentar für Kompatibilität mit altem JS. Ein GET-Request auf /planner/plans/sync triggert eine Sync-Aktion (Seiteneffekt), was gegen REST-Semantik verstößt und Browser-Prefetch-Probleme verursachen kann.  
**Fix:** JS auf POST umstellen und GET-Variante entfernen. Wenn Legacy-Support nötig: Ablaufdatum setzen und als deprecated markieren.

### ⚪ Unbenannte und middleware-lose Routen /chat/unread-count und /chat/mark-as-read  
**Modul:** CRM – Kommunikation · **Severity:** niedrig · · unverifiziert  
**Ort:** `routes/web.php:4714-4719`  
**Problem:** Route::get('/chat/unread-count') und Route::post('/chat/mark-as-read') sind als anonyme Closures außerhalb der auth-Middleware-Gruppe definiert, haben keine Routennamen und nutzen auth()->id() – der bei unauthentifizierten Anfragen null ist, was zu einem Fehler oder falschen Daten führt.  
**Fix:** In die auth-Middleware-Gruppe verschieben, Routennamen vergeben (chat.unread-count, chat.mark-as-read) und Closures zu Controller-Methoden extrahieren.

### ⚪ Test-/Debug-Routen in Produktion erreichbar (testnav, testnav2, test, visual/plan, solar, roofs, roof)  
**Modul:** Querschnitt · **Severity:** niedrig · · unverifiziert  
**Ort:** `routes/web.php:4623, 4626, 3623, 5091, 3596, 4630, 4634`  
**Problem:** Mehrere Routes ohne Middleware und ohne Controller-Logik (nur fn(){} Stubs oder alte Tests) sind als GET-Routen registriert: /testnav, /testnav2, /test, /visual/plan, /solar, /roofs, /roof. Diese sollten nicht in einer Produktionsumgebung verfügbar sein.  
**Fix:** Alle Debug-/Test-Routen entfernen oder hinter eine ENV-Prüfung (app()->isLocal()) stellen.

### ⚪ Inkonsistente Route-Namen-Konvention: snake_case vs. kebab-case vs. camelCase  
**Modul:** Querschnitt · **Severity:** niedrig · · unverifiziert  
**Ort:** `routes/web.php (appweit)`  
**Problem:** Route-Namen verwenden alle drei Stile gleichzeitig: snake_case (lead_junk, emp.add), kebab-case (lead-product.purge, employee-sick.store) und Punkt-Notation mit Mischung (customer.phase.managment — auch Schreibfehler 'managment' statt 'management'). Der Schreibfehler 'managment' in Routennamen wie customer.phase.managment.get ist besonders problematisch, da er in Blade-Templates und JS-Aufrufen reproduziert wird.  
**Fix:** Einheitlich Punkt-Notation mit kebab-case-Segmenten verwenden (Laravel-Standard). customer.phase.managment.* auf customer.phase.management.* korrigieren.
