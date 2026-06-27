# Software-Audit – Zusammenfassung

**Projekt:** ticket (Solar/PV-Business-App, Laravel 11) · **Stand:** 2026-06-27

**Methodik:** 159 Agenten · 14 Modul- + 5 Querschnitt-Prüfer gegen 11 Regeln, anschließend adversariale Verifikation jedes schweren Fundes. Evidenzbasiert (Datei:Zeile).

## Gesamtbild
- **291 Funde** gesamt.
- **140 schwer** (🔴 kritisch + 🟠 hoch) → verifiziert → **131 bestätigt**, 8 widerlegt.
- Mittel/Niedrig: 128 🟡 / 23 ⚪ (nicht einzeln verifiziert).

## Scorecard je Prüfregel (bestätigte schwere Funde)

| Regel | Health | 🔴 kritisch | bestätigt schwer | Funde gesamt |
|---|:--:|--:|--:|--:|
| Routing | 🔴 | 15 | 37 | 57 |
| Kausalität | 🔴 | 9 | 25 | 33 |
| Architektur | 🔴 | 3 | 21 | 43 |
| Redundanz | 🔴 | 2 | 10 | 42 |
| Plausibilität | 🔴 | 2 | 8 | 25 |
| Konsistenz | 🔴 | 1 | 10 | 38 |
| Workflow-Effizienz | 🔴 | 1 | 8 | 21 |
| Blade | 🔴 | 1 | 8 | 18 |
| CRUD | 🟠 | 1 | 4 | 10 |
| Modal | 🟢 | 0 | 0 | 1 |
| Usability | 🟢 | 0 | 0 | 3 |

_Health: 🟢 sauber · 🟡 kleinere Themen · 🟠 ernst · 🔴 dringend._

## Modul-Heatmap (bestätigte schwere Funde)

| Modul | 🔴 | 🟠 | Summe |
|---|--:|--:|--:|
| Querschnitt | 9 | 22 | 31 |
| Personal / HR | 4 | 4 | 8 |
| CRM – Anfragen | 3 | 5 | 8 |
| Dashboard & Berichte | 2 | 8 | 10 |
| CRM – Kommunikation | 2 | 6 | 8 |
| Projekte & Planer | 2 | 5 | 7 |
| Artikel | 2 | 4 | 6 |
| Lager | 2 | 4 | 6 |
| Admin & System | 2 | 4 | 6 |
| CRM – Partner | 2 | 3 | 5 |
| Finanzen | 2 | 2 | 4 |
| Sonstige | 1 | 10 | 11 |
| Vertrieb – Angebote | 1 | 6 | 7 |
| CRM – Leads & Kunden | 1 | 5 | 6 |
| Vertrieb – Aufträge & Rechnungen | 0 | 4 | 4 |
| Support – Tickets | 0 | 4 | 4 |

## 🔴 Top-Prioritäten – bestätigte KRITISCHE Funde

1. **[Architektur] Fat Controller Extrem: NewLeadsController (14.049 Zeilen, 121 Methoden)** — `app/Http/Controllers/Customer/NewLeadsController.php:1`
   - Der Controller umfasst 14.049 Zeilen und 121 public-Methoden. Einzelne Methoden wie loadHistoryFeed (693 Zeilen, L10594), loadHistoryModal (637L, L7946), view (633L, L1904), customerFeed (495L, L4028), store (493L, L517) und checkCustomer (407L, L5135) enthalten vollständige Geschäftslogik: Lead-Qualifizierung, Kundennummer-Generierung, Geo-Koordinaten-Verarbeitung, E-Mail-Statusprüfung, PV-Daten-Speicherung, Kalkulations-Logik. Der Controller importiert 53 verschiedene Model-Klassen direkt.
   - **Fix:** Den Controller in mind. 8 thematische Klassen aufteilen: LeadStoreService, LeadUpdateService, LeadViewService, LeadFeedService, LeadHistoryService, PvDataService, LeadQualificationService, LeadSearchService. FormRequest-Klassen für store() und update() anlegen (je ~40 Validierungsregeln). Geschäftslogik in Services auslagern, Controller reduziert auf Routing-Aufgaben.
2. **[Architektur] Keine FormRequest-Klassen im gesamten Projekt (0 von 380 Controllern)** — `app/Http/Controllers/:1`
   - Im gesamten Projekt existiert kein einziges app/Http/Requests-Verzeichnis und keine FormRequest-Klasse. Alle Validierungen sind inline im Controller-Body per $request->validate(). Allein NewLeadsController und PlannerPlanController haben je 31 inline-validate()-Aufrufe; MainAppointmentController 15, PersonalTaskBoardController 19, DepartmentController 12, EmployeeController 10. Validierungsregeln sind nicht wiederverwendbar, nicht testbar und vergrößern Controller-Methoden unnötig.
   - **Fix:** app/Http/Requests/ anlegen. Priorität: StoreNewLeadRequest, UpdateNewLeadRequest (je ~40 Regeln), StorePlannerItemRequest, UpdateItemStatusRequest. Für jede Controller-Methode mit >5 validate()-Regeln eine eigene FormRequest-Klasse erstellen. Sofortiger Nutzen: Methoden wie store() in NewLeadsController schrumpfen von 493 auf ~50 Zeilen.
3. **[Architektur] Fehlende Service-/Repository-Schicht: 374 von 380 Controllern ohne Service-Nutzung** — `app/Http/Controllers/:1`
   - Von 380 Controllern nutzen nur 6 (1,6%) eine Service-Klasse aus app/Services/. Gleichzeitig gibt es 16 Service-Klassen und 0 Repository-Klassen. Sämtliche Geschäftslogik, DB-Abfragen, Berechnungen und externe API-Aufrufe sind direkt in Controller-Methoden implementiert. 53 Model-Importe in einem einzelnen Controller (NewLeadsController) sind symptomatisch für fehlende Abstraktionsschichten.
   - **Fix:** Service-Layer-Nutzung auf alle fetten Controller ausweiten. Repository-Pattern für häufige komplexe Queries (z.B. Lead-Suche, Kanban-Feed, Daily-Report-Reload) einführen. Faustregel: Jede Methode >50 Zeilen oder >1 Model-Schreiboperation gehört in einen Service.
4. **[Blade] Stored XSS: {!! $r->report !!} rendert Benutzer-HTML ungesanitized** — `resources/views/admin/appointments/show.blade.php:1635`
   - Der Inhalt des Feldes `report` aus `AppointmentReport` wird über `{!! $r->report !!}` direkt als rohes HTML ausgegeben. Das Feld wird in `AppointmentReportController@store` mit `$request->input('report')` ohne jede HTML-Sanitisierung gespeichert (app/Http/Controllers/Appointment/AppointmentReportController.php:112). Jeder authentifizierte Benutzer kann damit beliebiges JavaScript einschleusen, das bei allen Betrachtern des Termins ausgeführt wird (Stored XSS).
   - **Fix:** Entweder `{{ $r->report }}` mit Blade-Auto-Escaping verwenden, oder – falls Rich-Text gewünscht ist – einen HTML-Purifier (z. B. `mews/purifier`) vor dem Speichern einsetzen und erst dann `{!! ... !!}` verwenden.
5. **[CRUD] Fehlende Autorisierung: Jeder angemeldete User kann jede GeneralTask bearbeiten/löschen/umsortieren** — `app/Http/Controllers/Task/GeneralTaskController.php:161`
   - Die Methoden `update()` (Z. 161), `destroy()` (Z. 358), `archive()` (Z. 330), `reorder()` (Z. 397) und `move()` (Z. 244) prüfen ausschließlich `auth`-Middleware, aber keinerlei Ownership oder Rollenberechtigung. Im `reorder()`-Closure fehlt `$employeeId` sogar im `use`-Block (Z. 419), obwohl er kurz davor abgerufen wird (Z. 415). Jeder eingeloggte Mitarbeiter kann beliebige Aufgaben anderer ändern, löschen oder in einen anderen Status verschieben.
   - **Fix:** Für jede Mutationsmethode prüfen, ob `auth()->user()->name == $generalTask->created_by` oder ob der User ein Assignee/Admin ist. Idealerweise eine `GeneralTaskPolicy` anlegen und `$this->authorize('update', $generalTask)` einsetzen.
6. **[Kausalität] getTabCounts() referenziert undefinierte Klassen Task und Appointment** — `app/Http/Controllers/EmployeeDashboardController.php:683-685`
   - Die Methode getTabCounts() ruft Task::count() und Appointment::count() auf. Weder Task noch Appointment sind in diesem Controller importiert (use-Anweisungen fehlen, vorhandene Klassen: PersonalTask, MainAppointment). Der Endpunkt /dashboard/tab-counts würde einen fatalen PHP-Fehler auslösen (Class 'App\Http\Controllers\Task' not found), was die Kernfunktion des Tab-Counters im Dashboard bricht.
   - **Fix:** Klasse Task durch PersonalTask und Appointment durch MainAppointment ersetzen, entsprechende use-Statements prüfen. Alternativ Methode vollständig über getDueToday-Logik abbilden.
7. **[Kausalität] update() setzt Status jeder Anfrage zwingend auf 'Unpublished' – verifizierte Anfragen werden zurueckgestuft** — `app/Http/Controllers/Inquiry/InquiryController.php:1360`
   - In der update()-Methode ist $inquiry->status = 'Unpublished' fest codiert (Zeile 1360). Eine bereits verifizierte (Published) Anfrage verliert damit beim naechsten Bearbeitungsvorgang ihren Verifikationsstatus. Das ist das genaue Gegenteil der erwarteten Wirkung ('Anfrage bearbeiten' darf Verifizierung nicht zuruecksetzen). Dasselbe Problem besteht in store() (Zeile 1072) und finalizeDraft() (Zeile 2519).
   - **Fix:** Status nur setzen wenn er noch 'Draft' oder nicht gesetzt ist: $inquiry->status = $inquiry->status === 'Published' ? 'Published' : 'Unpublished';. Den Verifikationsstatus explizit schtzen oder einen separaten Workflow-Status einfhren.
8. **[Kausalität] Restore-Methoden auf Brand und Distributor ohne SoftDeletes-Trait – fatal error** — `app/Http/Controllers/Contacts/AllContactController.php:994,1001`
   - AllContactController::brand() ruft Brand::withTrashed()->where(...)->restore() auf, und ::distributor() ruft Distributor::withTrashed()->...->restore() auf. Weder das Brand-Model (app/Models/Brand.php) noch das Distributor-Model (app/Models/Distributor.php) verwenden den SoftDeletes-Trait; die Tabellen haben auch keine deleted_at-Spalte (verifiziert durch Migrations). Der Aufruf von withTrashed() auf einem regulären Eloquent-Builder wirft eine BadMethodCallException – die Restore-Funktion für 'Hersteller' und 'Lieferant' in der Kontaktansicht ist komplett defekt.
   - **Fix:** Entweder SoftDeletes-Trait und deleted_at-Migration für Brand und Distributor ergänzen, oder die Restore-Methoden entfernen/deaktivieren bis dahin.
9. **[Kausalität] Leave-Genehmigung (approve) reduziert employees.remaining_day nicht** — `app/Http/Controllers/Employee/Profile/LeaveController.php:230-258`
   - Die Methode approve() setzt leave.approved='Yes' und leave.status='accept', aktualisiert aber employees.remaining_day nicht. Nur LeaveController@save (Dashboard-Pfad, Zeile 1114-1146) dekrementiert remaining_day. Der Admin-Hauptpfad leave_approve/{id} (GET, Zeile 1790) genehmigt Urlaub, ohne dass die Resttage des Mitarbeiters abnehmen — der Mitarbeiter kann unbegrenzt genehmigten Urlaub akkumulieren ohne Tage-Verbrauch.
   - **Fix:** In approve() nach $leave->save() die remaining_day des zugehörigen Mitarbeiters um $leave->duration verringern (mit DB::transaction und optimistischem Sperren): Employee::where('id',$leave->emp_id)->decrement('remaining_day', $leave->duration). Gleiches gilt für die change()-Methode bei type='accept'.
10. **[Kausalität] QualificationController::destroy() löscht FurtherEducation statt Qualification** — `app/Http/Controllers/Employee/Position/QualificationController.php:102-118`
   - Die Methode destroy($id) referenziert FurtherEducation::find($id) (Zeile 104), obwohl der Controller QualificationController heißt und die Route emp.qualification.delete (DELETE /qualification_delete/{id}) Qualifikationen löschen soll. FurtherEducation ist nicht importiert (kein 'use'-Statement), was einen fatalen PHP-Fehler/NamespaceException auslöst. Selbst wenn die Klasse im globalen Namespace aufgelöst würde, würde das falsche Model gelöscht.
   - **Fix:** Zeile 104 auf Qualification::find($id) korrigieren und den FurtherEducation-Import entfernen. Anschließend sicherstellen, dass der Benutzer nur eigene Qualifikationen löschen darf (Ownership-Prüfung hinzufügen).
11. **[Kausalität] BegFundingsController::store() referenziert nicht-existierende Klasse BegFunding (Fatal Error)** — `app/Http/Controllers/Customer/BegFundingsController.php:49`
   - In store() wird BegFunding::create([...]) aufgerufen (singular). Importiert ist aber App\Models\BegFundings (plural). Eine Klasse BegFunding existiert nicht (Model-Datei nicht gefunden). Jeder POST auf beg-fundings.store endet mit einem PHP-Fatal-Error.
   - **Fix:** Zeile 49 auf BegFundings::create([...]) korrigieren (konsistent mit dem Import in Zeile 6).
12. **[Kausalität] GarbageController prüft Berechtigung gegen falsche Spalte (user->name statt user->id)** — `app/Http/Controllers/Admin/GarbageController.php:29`
   - ensureDeletePermission() sucht in user_rolls mit WHERE user_id = auth()->user()->name. user_rolls.user_id ist eine Foreign Key auf users.id (unsignedBigInteger, siehe Migration 2023_06_14). users.name speichert jedoch die employee_id (String). Ergebnis: Die Berechtigungsprüfung schlägt für alle Benutzer fehl (kein Match), abort_unless(false, 403) blockiert jeden Zugriff – oder, wenn employee_id numerisch ist und zufällig einer users.id entspricht, wird fälschlicherweise Zugang gewährt. Die gesamte Garbage-Bereinigung ist funktionsunfähig.
   - **Fix:** Zeile 29 ändern auf ->where('user_id', $user->id), analog zur korrekten Implementierung in UserRollController.php.
13. **[Kausalität] isAdmin-Middleware joiniert users.name (employee_id) mit user_rolls.user_id – immer falsch** — `app/Http/Middleware/isAdmin.php:22-29`
   - JOIN users ON users.name = user_rolls.user_id: user_rolls.user_id verweist auf users.id (Integer-FK), users.name enthält employee_id (String). Der Join produziert nie valide Zeilen; auth()->user()->name == $user (null) ist niemals wahr. Alle durch diese Middleware geschützten Routen (Rechnung/Invoice-Zugang) sind entweder immer gesperrt oder zeigen undefiniertes Verhalten.
   - **Fix:** Join-Bedingung auf users.id = user_rolls.user_id korrigieren; WHERE auf user_rolls.user_id = auth()->id() ändern.
14. **[Kausalität] Zweistufige N+1-Query-Schleife beim Laden der Kundenliste im Dashboard** — `app/Http/Controllers/EmployeeDashboardController.php:1821-1848`
   - Für jeden Kunden (Zeile 1821) wird eine separate DB::table('lead_alternative_adds')-Abfrage ausgeführt (Zeile 1822-1826). Für jedes Objekt dieses Kunden (Zeile 1828) folgt eine weitere DB::table('lead_product_lists')-Abfrage (Zeile 1829-1841). Bei 50 Kunden mit je 3 Objekten entstehen 1 + 50 + 150 = 201 Datenbankabfragen für einen einzigen API-Call.
   - **Fix:** Alle customer_ids vorher sammeln, dann einmalig alle lead_alternative_adds und lead_product_lists mit whereIn($customerIds) laden und per PHP keyBy/groupBy zuordnen. Das reduziert 1+N+N*M Abfragen auf 3 Abfragen.
15. **[Konsistenz] Flash-Message-Key 'update_msg' vs. 'updated_msg' – Silent-Fail in 143 Views** — `app/Http/Controllers/EconomicCalculationController.php:84, resources/views/admin/customer_economic_calculation/economic_calculation/create.blade.php:117`
   - Controller setzen den Session-Key 'update_msg' (19 Stellen), während 143 Views session('updated_msg') lesen. Die Meldung wird dadurch nie angezeigt. Exemplarisch: create.blade.php prüft Session::has('update_msg') korrekt, rendert aber session('updated_msg') – der Wert ist immer leer.
   - **Fix:** Vereinheitlichen auf einen Key, z. B. 'update_msg'. Alle 143 Vorkommen von session('updated_msg') in Views sowie die 6 Controller-Stellen, die 'updated_msg' setzen, müssen angeglichen werden.
16. **[Plausibilität] IMAP-Passwörter im Klartext in DB und HTML gespeichert und exponiert** — `resources/views/admin/lead_email/email_config/account.blade.php:276 / app/Http/Controllers/Email/LeadEmailAccountsController.php:118`
   - Das IMAP-Passwort wird unverschlüsselt als VARCHAR in der Datenbank gespeichert (Migration Zeile 18: 'store encrypted if possible' – Kommentar ohne Umsetzung) und direkt als HTML-data-Attribut ins DOM gerendert (data-password="{{ $account->password }}"). Jeder Nutzer, der die Seite aufruft und DevTools öffnet, sieht alle IMAP-Passwörter aller Konten im Klartext. Außerdem wird das Passwort im Edit-Modal als Plaintext in ein type='text'-Feld gefüllt.
   - **Fix:** 1) Laravel Crypt::encryptString/decryptString im Model via $casts = ['password' => 'encrypted'] aktivieren. 2) Im Edit-Formular kein Passwort aus der DB vorausfüllen – stattdessen Platzhalter-Asterisks und nur bei Änderung ein neues Passwort akzeptieren. 3) HTML-Attribut data-password entfernen.
17. **[Plausibilität] Undefinierte Variable $oldValue im Audit-Log führt zu PHP-Fehler** — `app/Http/Controllers/Customer/NewLeadsController.php:6411`
   - In updateField() wird in der logActivity-Zeile $oldValue referenziert (L6411: ['from' => $oldValue, 'to' => $value]), obwohl diese Variable nirgends in der Methode definiert wird. In PHP 8 führt das zumindest zu einer Deprecation-Notice, in früheren Versionen zu einem Undefined variable-Notice. Das bedeutet: jedes Mal wenn ein Feld über den AJAX-Endpunkt update-field geändert wird, schlägt die Audit-Protokollierung mit einem PHP-Fehler fehl (je nach error_reporting kann die gesamte Antwort fehlschlagen).
   - **Fix:** Vor dem Update den Originalwert lesen: $oldValue = $alternative->{$dbField} ?? null; – direkt nach dem $alternative-Laden (L6389) einfügen.
18. **[Redundanz] 40 tote Controller in app/Http/Controllers/Old/ - vollstaendig unreferenziert** — `app/Http/Controllers/Old/`
   - 40 PHP-Controller-Dateien in einem dedizierten Old/-Ordner (AppointmentController.php, CustomerController.php, ProjectController.php, TaskToDoController.php, OfferConfigController.php u.v.m.). Grep auf routes/ ergab 0 Treffer fuer Controllers\Old\. Aktive Pendants existieren in Appointment/, Customer/, Project/ usw. Der Ordner ist toter Code, wird aber weiterhin von Composer autoloaded (psr-4).
   - **Fix:** Den gesamten Ordner app/Http/Controllers/Old/ loeschen. Vor dem Loeschen sicherstellen, dass kein dynamischer require/use-Aufruf existiert (vollstaendiger app/-Scan auf 'Old\\'). Danach composer dump-autoload ausfuehren.
19. **[Redundanz] 107 'blade copy'-Dateien in 25 Old-Code-Verzeichnissen in resources/views/** — `resources/views/admin/`
   - 107 Blade-Dateien mit Namensmuster '*.blade copy*.php', '*.blade copy 2.php' etc. verteilt in 25 Verzeichnissen mit Namen wie 'Old Code', 'old codes', 'oldcode'. Groesste Ansammlungen: checklist/profitablity_calculation/Old Code (26 Dateien), planner/old (14), new_leads/old code (14), dashboard/old codes (10), layouts/OLD CODE (10). Keines dieser Verzeichnisse wird per @include oder view() referenziert.
   - **Fix:** Alle 25 Old-Code-Verzeichnisse und die 107 copy-Dateien loeschen. Empfohlen: git-basiertes Loeschprotokoll (git rm -r), damit die Historie erhalten bleibt. Kein Backup noetig - Git ist das Backup.
20. **[Routing] Öffentliche Route gibt LeadEmail-Inhalte ohne Authentifizierung zurück** — `routes/web.php:599`
   - Route::get('lead/email/api/{id}', [WebsiteController::class, 'getEmailDetails']) liegt außerhalb jeder Auth-Middleware-Gruppe und gibt Absender, Betreff, Body und Domain von Lead-E-Mails zurück. Jeder unauthentifizierte Besucher kann durch Enumeration aller IDs sämtliche eingehenden Kunden-E-Mails lesen. Der WebsiteController enthält keinerlei Auth-Check für diese Methode.
   - **Fix:** Die Route in eine auth-geschützte Gruppe verschieben oder die Route zugunsten der bereits existierenden Route lead.email.show (admin/lead-email/show/{id}, geschützt) entfernen. Beide Endpunkte tun dasselbe.
21. **[Routing] 4 Routen verweisen auf nicht existente Controller-Methoden (500)** — `routes/web.php:4003,4037,4057,4668`
   - Vier registrierte Routen erzeugen zur Laufzeit eine MethodNotAllowedException bzw. einen 500-Fehler, weil die zugeordneten Methoden im Controller nicht existieren: (1) GET /inquiry_publish/{id} → InquiryController@publish (Methode fehlt komplett); (2) POST /admin/inquiries/ai-save → InquiryController@storeFromAI (Methode fehlt); (3) GET /admin/fusion-forms/import → FusionFormSubmissionController@importFromGoneo (Methode fehlt); (4) POST /fusion/import/one → FusionFormSubmissionController@importFusionEntryToInquiry (Methode fehlt). Alle vier Endpunkte sind im Frontend erreichbar, liefern aber stets einen Fehler.
   - **Fix:** Methoden implementieren oder Routen entfernen. Bis dahin temporaer 501 NotImplemented zurueckgeben oder Route auskommentieren, damit das Routing-Register sauber bleibt.
22. **[Routing] GET-Routen fuer destruktive Operationen (Delete, Junk, Restore, Verify)** — `routes/web.php:4002,4003,4004,4005,4006,4009,4107`
   - Sieben Routen verwenden HTTP GET fuer zustandsaendernde/destruktive Operationen: GET /inquiry_delete/{id} (loescht), GET /inquiry_publish/{id} (veroeffentlicht), GET /inquiry_verify/{id} (verifiziert), GET|POST /inquiry_junk/{id} (markiert als Junk), GET|POST /inquiry_unjunk/{id} (entfernt Junk), GET /inquiry_restore/{id} (stellt wieder her), GET /inquiry_type_destroy/{id} (loescht Typ). GET-Requests werden von Browsern vorgeladen (Prefetch, Google Bot), koennen in Logs landen und sind nicht CSRF-geschuetzt. Ein einfacher Link in einer E-Mail oder einer externen Seite koennte Datensaetze loeschen.
   - **Fix:** Alle Zustandsaenderungen auf POST/PUT/PATCH/DELETE umstellen. Vorhandene REST-Routen (z.B. DELETE inquiries/{inquiry}/discard) als Vorbild nehmen und die alten Routen entfernen.
23. **[Routing] AllContactController ohne Auth-Middleware: alle Kontakte und Export öffentlich** — `routes/web.php:4609-4615`
   - Die Routen /all-contacts, /all-contacts/export, /global-search und /global-restore/* liegen in einem Route::middleware('web')-Block ohne 'auth'. Der AllContactController hat keinen Middleware-Aufruf im Konstruktor. Damit kann jeder anonyme Nutzer die konsolidierte Kontaktliste (Kunden, Mitarbeiter, Hersteller, Lieferanten), den CSV-Export und die Restore-Endpunkte aufrufen.
   - **Fix:** Middleware-Gruppe auf ['web', 'auth'] ändern oder im AllContactController-Konstruktor $this->middleware('auth') ergänzen.
24. **[Routing] OfferCommentController ohne Auth-Middleware – alle Endpoints unauthentifiziert aufrufbar** — `app/Http/Controllers/Customer/Offer/OfferCommentController.php:1-57`
   - OfferCommentController (index, store, update, destroy) besitzt weder einen __construct-Aufruf mit $this->middleware('auth') noch ist er in einer Route-Gruppe mit auth-Middleware registriert (routes/web.php Zeile 108 zeigt nur den use-Import, keine Route-Registrierung im auth-Block). Damit können Kommentare ohne Login gelesen, erstellt, editiert und gelöscht werden. Zusätzlich fehlt in store() jede Validierung der Eingaben (customer_id, comment etc.).
   - **Fix:** Sofort __construct mit $this->middleware('auth') ergänzen. Alle Routen in einen Route::middleware(['auth'])-Block verschieben. Request-Validierung (FormRequest oder Validator::make) für store/update ergänzen.
25. **[Routing] Destruktive Operationen über GET-Routen (CSRF-Bypass, sofortige Löschung per Link)** — `routes/web.php:1539,1687,1698,2829,2836,1610`
   - Mindestens 6 HR-Löschaktionen sind als GET-Routen registriert: emp_destroy/{id} (Mitarbeiterlöschen), holiday_destroy/{id}, leave_day_destroy/{id}, country_destroy/{id}, tax_destroy/{id}, contract_type_destroy/{id}. GET-Requests werden von Browsern verfolgt (Prefetch, Logs, History, eingebettete Links), unterliegen keinem CSRF-Schutz und erlauben es jedem, der eine URL mit gültiger ID kennt, den Datensatz ohne Bestätigungs-Token zu löschen. Ein normaler Nutzer kann über einen manipulierten Link alle Mitarbeiterdatensätze löschen.
   - **Fix:** Routen auf DELETE umstellen (Route::delete), Formulare mit @csrf und @method('DELETE') absichern. Alternativ AJAX-Call mit X-CSRF-Token. Alle 70+ im Projekt gefundenen GET-Destroy-Routen systematisch migrieren.
26. **[Routing] Leave-Notiz-Endpunkte ohne Auth-Middleware öffentlich erreichbar** — `routes/web.php:1810-1815`
   - Der Prefix-Block 'Route::prefix("leaves")' hat keinerlei Middleware (weder 'auth' noch 'web'). Damit sind GET /leaves/{id}/notes, POST /leaves/{id}/notes/store, PUT /leaves/{id}/notes/update/{index} und DELETE /leaves/{id}/notes/delete/{index} ohne Authentifizierung abrufbar. Jeder Internetzugang kann Urlaubsnotizen lesen, anlegen, ändern und löschen.
   - **Fix:** Route::prefix('leaves')->middleware(['web','auth'])->group(...) — identisch wie der Block ab Zeile 1784 für die übrigen Leave-Routen.
27. **[Routing] DELETE-Aktionen über GET-Routen CSRF-ungeschützt** — `routes/web.php:2278,2310,2475,2844`
   - Die Routen /product_destroy/{id}, /product_description_destroy/{id}, /measure_destroy/{id} und /discount_group_destroy/{id} sind alle als GET definiert. Damit können Datensätze durch einfaches Einbetten einer URL (z. B. <img src="/product_destroy/5">) ohne CSRF-Schutz irreversibel gelöscht werden.
   - **Fix:** Auf DELETE-Verb umstellen (Route::delete) und in den Blade-Views Löschformulare mit @method('DELETE') und @csrf verwenden.
28. **[Routing] is_Admin-Middleware wird im Produkt-Route-Group nicht angewandt** — `routes/web.php:2275`
   - Route::group(['middleware' => 'web', 'is_Admin'], ...) setzt den Schlüssel 'middleware' auf den String 'web'; der Eintrag 'is_Admin' liegt an Array-Index 0 und wird von Laravel als unbekannter Gruppenattribut-Schlüssel ignoriert. Dadurch gilt für alle Produkt-CRUD-Routen (Anlage, Änderung, Löschung) ausschließlich 'web' – kein Admin-Rollenschutz. Jeder authentifizierte Nutzer darf Produkte anlegen, bearbeiten und löschen.
   - **Fix:** Route::middleware(['web', 'is_Admin'])->group(...) verwenden, oder innerhalb der Gruppe ->middleware('is_Admin') je Route setzen.
29. **[Routing] PurchaseRequest- und RequestOut-Routen ohne Auth-Middleware** — `routes/web.php:2696, 2716`
   - Beide Route-Gruppen verwenden ausschließlich middleware('web'). Die 'web'-Gruppe enthält laut Kernel.php (Zeile 35–43) kein 'auth'. Weder PurchaseRequestController noch InventoryRequestOutController setzen im Konstruktor middleware('auth'). Unauthentifizierte Nutzer können so alle Purchase-Request- und Request-Out-Endpunkte (index, list, analytics, store, destroy) aufrufen. auth()->user() wird in diesen Controllern nur mit ?? 'System'-Fallback verwendet, was den Fehler stumm schluckt.
   - **Fix:** middleware('web') in beiden Route::group-Aufrufen durch middleware(['web','auth']) ersetzen.
30. **[Routing] GoodsReceipt-Routen ohne Auth-Middleware** — `routes/web.php:5096`
   - Route::prefix('admin')->name('admin.') bei Zeile 5096 enthält kein ->middleware('auth'). GoodsReceiptController hat keinen __construct mit middleware-Aufruf. index() und data() führen gar keine Auth-Prüfung durch. relationOptions(), show() ebenfalls. Nur store/update/destroy nutzen authEmployeeId(), die bei fehlendem Login null zurückgibt ohne Redirect.
   - **Fix:** Die Route-Gruppe auf ->middleware(['auth']) oder ->middleware(['web','auth']) ändern; alternativ im Controller-Konstruktor middleware('auth') hinzufügen.
31. **[Routing] BEG-Förderungen ohne Auth-Middleware (unauthentifizierter Zugriff)** — `routes/web.php:860`
   - Route::resource('beg-fundings', ...) liegt im 'web'-Only-Middleware-Gruppe (Zeile 769). Kein 'auth'-Middleware. BegFundingsController hat auch keinen __construct()-Middleware-Aufruf. Index, Store, Update und Destroy der BEG-Förderdaten sind somit ohne Login aufrufbar.
   - **Fix:** Die Route in eine auth-gesicherte Gruppe verschieben oder im BegFundingsController $this->middleware('auth') im Konstruktor ergänzen.
32. **[Routing] Middleware-Typo: 'middlware' statt 'middleware' — Auth-Schutz komplett inaktiv** — `routes/web.php:1444`
   - Route::group(['middlware'=>'auth'], function(){...}) verwendet den falschen Schlüssel 'middlware'. Laravel ignoriert unbekannte Keys stillschweigend, sodass alle 5 darin enthaltenen Routen (customer_phase_manage, customer/phase/manage, customer_phase_get, customer_phase_get_new, customer_phase_management_store, customer_phase_management/color, customer_phase_management_delete) ohne Authentifizierung erreichbar sind.
   - **Fix:** Schlüssel in 'middleware' korrigieren: Route::group(['middleware'=>'auth'], function(){...}). Anschließend sicherstellen, dass betroffene Routen wirklich auth-geschützt sind.
33. **[Routing] GET /route-cache führt Artisan-Befehle aus — kein Auth-Schutz** — `routes/web.php:3961`
   - Route::get('/route-cache', function(){ Artisan::call('route:cache'); Artisan::call('view:clear'); ... }) liegt außerhalb jeder Middleware-Gruppe. Jeder anonyme HTTP-Request kann damit den Route- und View-Cache der Produktion manipulieren (Cache Poisoning / DoS).
   - **Fix:** Route entfernen oder in eine auth- und admin-only-Middleware-Gruppe verschieben. Für Cache-Clearing besser einen Artisan-Befehl direkt auf dem Server ausführen.
34. **[Routing] GET /fix-notes schreibt Datenbankdaten ohne Authentifizierung** — `routes/web.php:404`
   - Route::get('/fix-notes', function(){...}) führt DB-Schreiboperationen auf CustomerNote-Datensätzen durch (note->save()) und ist nicht durch Auth-Middleware geschützt. Jeder anonyme Benutzer kann diesen Migrations-Helper beliebig oft aufrufen.
   - **Fix:** Route nach der Datenmigration komplett entfernen. Solange sie benötigt wird: in eine auth+admin-Middleware-Gruppe verschieben.
35. **[Workflow-Effizienz] O(N) updateOrCreate + O(N) Kind-Query pro Entity in syncAndLoad** — `app/Http/Controllers/Planner/PlannerPlanController.php:861-1111`
   - syncAndLoad() ruft syncAppointments(), syncTickets(), syncPersonalTasks(), syncPhaseActivities() und syncMasterSets() in einer einzigen Transaktion auf. Jede Methode iteriert über ihre Entitätenliste und führt pro Zeile ein PlannerItem::updateOrCreate() aus. Zusätzlich wird in syncAppointments (Zeile 893-897) pro Termin eine extra DB::table('main_appointment_employees')->where('appointment_id', $id)->pluck() ausgeführt, in syncPersonalTasks (Zeile 1086-1102) pro Aufgabe zwei weitere Abfragen (personal_task_keys, employees_personal_tasks). Damit entstehen pro Planner-Öffnung 3×N+2×N DB-Operationen für N Projektentitäten.
   - **Fix:** Alle IDs zuerst sammeln, Pivot-Daten mit whereIn in einer Abfrage laden (keyBy('id')), dann mit upsert()/insertOrIgnore() als Batch schreiben. syncPlannerItemEmployees() (Zeile 286-294) sollte statt N Einzel-INSERTs ein einziges DB::table()->insert($rows) mit vorbereiteter Array-Liste verwenden.

## So liest du den Bericht
- **`befunde-bestaetigt.csv`** — die 131 verifizierten schweren Funde (die eigentliche Arbeitsliste).
- **`befunde-alle.csv`** — alle 291 Funde inkl. Status (bestätigt / nicht bestätigt / unverifiziert).
- **`regel-<name>.md`** — Funde je Prüfregel mit Beleg & Fix.

> Dieser Bericht ist rein analytisch — es wurde noch nichts am Code geändert.