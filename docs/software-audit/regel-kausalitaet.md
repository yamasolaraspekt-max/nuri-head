# Audit – Kausalität

Funde: 33  ·  🔴 9 kritisch · 🟠 16 hoch · 🟡 8 mittel · ⚪ 0 niedrig

### 🔴 getTabCounts() referenziert undefinierte Klassen Task und Appointment  
**Modul:** Dashboard & Berichte · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/EmployeeDashboardController.php:683-685`  
**Problem:** Die Methode getTabCounts() ruft Task::count() und Appointment::count() auf. Weder Task noch Appointment sind in diesem Controller importiert (use-Anweisungen fehlen, vorhandene Klassen: PersonalTask, MainAppointment). Der Endpunkt /dashboard/tab-counts würde einen fatalen PHP-Fehler auslösen (Class 'App\Http\Controllers\Task' not found), was die Kernfunktion des Tab-Counters im Dashboard bricht.  
**Fix:** Klasse Task durch PersonalTask und Appointment durch MainAppointment ersetzen, entsprechende use-Statements prüfen. Alternativ Methode vollständig über getDueToday-Logik abbilden.

### 🔴 update() setzt Status jeder Anfrage zwingend auf 'Unpublished' – verifizierte Anfragen werden zurueckgestuft  
**Modul:** CRM – Anfragen · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Inquiry/InquiryController.php:1360`  
**Problem:** In der update()-Methode ist $inquiry->status = 'Unpublished' fest codiert (Zeile 1360). Eine bereits verifizierte (Published) Anfrage verliert damit beim naechsten Bearbeitungsvorgang ihren Verifikationsstatus. Das ist das genaue Gegenteil der erwarteten Wirkung ('Anfrage bearbeiten' darf Verifizierung nicht zuruecksetzen). Dasselbe Problem besteht in store() (Zeile 1072) und finalizeDraft() (Zeile 2519).  
**Fix:** Status nur setzen wenn er noch 'Draft' oder nicht gesetzt ist: $inquiry->status = $inquiry->status === 'Published' ? 'Published' : 'Unpublished';. Den Verifikationsstatus explizit schtzen oder einen separaten Workflow-Status einfhren.

### 🔴 Restore-Methoden auf Brand und Distributor ohne SoftDeletes-Trait – fatal error  
**Modul:** CRM – Partner · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Contacts/AllContactController.php:994,1001`  
**Problem:** AllContactController::brand() ruft Brand::withTrashed()->where(...)->restore() auf, und ::distributor() ruft Distributor::withTrashed()->...->restore() auf. Weder das Brand-Model (app/Models/Brand.php) noch das Distributor-Model (app/Models/Distributor.php) verwenden den SoftDeletes-Trait; die Tabellen haben auch keine deleted_at-Spalte (verifiziert durch Migrations). Der Aufruf von withTrashed() auf einem regulären Eloquent-Builder wirft eine BadMethodCallException – die Restore-Funktion für 'Hersteller' und 'Lieferant' in der Kontaktansicht ist komplett defekt.  
**Fix:** Entweder SoftDeletes-Trait und deleted_at-Migration für Brand und Distributor ergänzen, oder die Restore-Methoden entfernen/deaktivieren bis dahin.

### 🔴 Leave-Genehmigung (approve) reduziert employees.remaining_day nicht  
**Modul:** Personal / HR · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Employee/Profile/LeaveController.php:230-258`  
**Problem:** Die Methode approve() setzt leave.approved='Yes' und leave.status='accept', aktualisiert aber employees.remaining_day nicht. Nur LeaveController@save (Dashboard-Pfad, Zeile 1114-1146) dekrementiert remaining_day. Der Admin-Hauptpfad leave_approve/{id} (GET, Zeile 1790) genehmigt Urlaub, ohne dass die Resttage des Mitarbeiters abnehmen — der Mitarbeiter kann unbegrenzt genehmigten Urlaub akkumulieren ohne Tage-Verbrauch.  
**Fix:** In approve() nach $leave->save() die remaining_day des zugehörigen Mitarbeiters um $leave->duration verringern (mit DB::transaction und optimistischem Sperren): Employee::where('id',$leave->emp_id)->decrement('remaining_day', $leave->duration). Gleiches gilt für die change()-Methode bei type='accept'.

### 🔴 QualificationController::destroy() löscht FurtherEducation statt Qualification  
**Modul:** Personal / HR · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Employee/Position/QualificationController.php:102-118`  
**Problem:** Die Methode destroy($id) referenziert FurtherEducation::find($id) (Zeile 104), obwohl der Controller QualificationController heißt und die Route emp.qualification.delete (DELETE /qualification_delete/{id}) Qualifikationen löschen soll. FurtherEducation ist nicht importiert (kein 'use'-Statement), was einen fatalen PHP-Fehler/NamespaceException auslöst. Selbst wenn die Klasse im globalen Namespace aufgelöst würde, würde das falsche Model gelöscht.  
**Fix:** Zeile 104 auf Qualification::find($id) korrigieren und den FurtherEducation-Import entfernen. Anschließend sicherstellen, dass der Benutzer nur eigene Qualifikationen löschen darf (Ownership-Prüfung hinzufügen).

### 🔴 BegFundingsController::store() referenziert nicht-existierende Klasse BegFunding (Fatal Error)  
**Modul:** Finanzen · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/BegFundingsController.php:49`  
**Problem:** In store() wird BegFunding::create([...]) aufgerufen (singular). Importiert ist aber App\Models\BegFundings (plural). Eine Klasse BegFunding existiert nicht (Model-Datei nicht gefunden). Jeder POST auf beg-fundings.store endet mit einem PHP-Fatal-Error.  
**Fix:** Zeile 49 auf BegFundings::create([...]) korrigieren (konsistent mit dem Import in Zeile 6).

### 🔴 GarbageController prüft Berechtigung gegen falsche Spalte (user->name statt user->id)  
**Modul:** Admin & System · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Admin/GarbageController.php:29`  
**Problem:** ensureDeletePermission() sucht in user_rolls mit WHERE user_id = auth()->user()->name. user_rolls.user_id ist eine Foreign Key auf users.id (unsignedBigInteger, siehe Migration 2023_06_14). users.name speichert jedoch die employee_id (String). Ergebnis: Die Berechtigungsprüfung schlägt für alle Benutzer fehl (kein Match), abort_unless(false, 403) blockiert jeden Zugriff – oder, wenn employee_id numerisch ist und zufällig einer users.id entspricht, wird fälschlicherweise Zugang gewährt. Die gesamte Garbage-Bereinigung ist funktionsunfähig.  
**Fix:** Zeile 29 ändern auf ->where('user_id', $user->id), analog zur korrekten Implementierung in UserRollController.php.

### 🔴 isAdmin-Middleware joiniert users.name (employee_id) mit user_rolls.user_id – immer falsch  
**Modul:** Admin & System · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Middleware/isAdmin.php:22-29`  
**Problem:** JOIN users ON users.name = user_rolls.user_id: user_rolls.user_id verweist auf users.id (Integer-FK), users.name enthält employee_id (String). Der Join produziert nie valide Zeilen; auth()->user()->name == $user (null) ist niemals wahr. Alle durch diese Middleware geschützten Routen (Rechnung/Invoice-Zugang) sind entweder immer gesperrt oder zeigen undefiniertes Verhalten.  
**Fix:** Join-Bedingung auf users.id = user_rolls.user_id korrigieren; WHERE auf user_rolls.user_id = auth()->id() ändern.

### 🔴 Zweistufige N+1-Query-Schleife beim Laden der Kundenliste im Dashboard  
**Modul:** Dashboard · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/EmployeeDashboardController.php:1821-1848`  
**Problem:** Für jeden Kunden (Zeile 1821) wird eine separate DB::table('lead_alternative_adds')-Abfrage ausgeführt (Zeile 1822-1826). Für jedes Objekt dieses Kunden (Zeile 1828) folgt eine weitere DB::table('lead_product_lists')-Abfrage (Zeile 1829-1841). Bei 50 Kunden mit je 3 Objekten entstehen 1 + 50 + 150 = 201 Datenbankabfragen für einen einzigen API-Call.  
**Fix:** Alle customer_ids vorher sammeln, dann einmalig alle lead_alternative_adds und lead_product_lists mit whereIn($customerIds) laden und per PHP keyBy/groupBy zuordnen. Das reduziert 1+N+N*M Abfragen auf 3 Abfragen.

### 🟠 GET-Route lead.email.fetch triggert synchronen IMAP-Abruf mit set_time_limit(0)  
**Modul:** CRM – Kommunikation · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Email/LeadEmailReaderController.php:17-18 / routes/web.php:1388`  
**Problem:** fetchAndStore() wird per GET ausgelöst, setzt set_time_limit(0) und ini_set('memory_limit', '256M') und iteriert über bis zu 1000 IMAP-Nachrichten synchron im Request. Das blockiert den PHP-Worker für potenziell Minuten, ist per GET aufrufbar (jeder, der die URL kennt, kann den Server lahmlegen) und macht den Vorgang für normale Nutzer unzuverlässig (Browser-Timeout, kein Fortschrittsindikator).  
**Fix:** IMAP-Sync als Queue-Job (dispatch) ausführen, der Button löst nur den Job-Dispatch per POST aus und gibt sofort eine Antwort zurück. set_time_limit(0) in einem Job ist akzeptabel.

### 🟠 users.name wird als Fremdschlüssel zu employees.id verwendet – semantisch falsch  
**Modul:** CRM – Kommunikation · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Chat/ChatController.php:67 / 1217`  
**Problem:** Das users.name-Feld (eigentlich der Anzeigename eines Users) wird als Fremdschlüssel auf employees.id behandelt: `$authEmployeeId = (int) auth()->user()->name` und im JOIN: `join('employees', 'employees.id', '=', 'users.name')`. Das ist konzeptionell falsch: users.name ist ein string-Name-Feld, kein Integer-FK. Wenn ein User einen echten Namen hat, scheitert der INT-Cast lautlos. Die Verknüpfung von User zu Employee sollte über eine user_id-Spalte in der employees-Tabelle erfolgen.  
**Fix:** Eine ordentliche Beziehung herstellen: employees.user_id (FK) oder users.employee_id. Alle Stellen, die `users.name` als Integer-FK nutzen, auf die korrekte Spalte umstellen.

### 🟠 Status-String-Inkonsistenz: Store setzt einfaches Leerzeichen, Filter sucht doppeltes  
**Modul:** CRM – Leads & Kunden · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:628,1064,3244,3261`  
**Problem:** In store() (L628) wird gesetzt: 'um zu qualifizieren, bitte per Brief Kontakt aufnehmen' (ein Leerzeichen vor 'Kontakt'). In details_update() (L1064) und qualified() (L1776) wird dasselbe mit doppeltem Leerzeichen gesetzt: 'bitte per Brief  Kontakt aufnehmen'. Die Filterfunktion not_qualified_sort() (L3244) und incomplete_sort() (L3261) suchen jeweils nach der Doppel-Leerzeichen-Variante. Leads die über store() angelegt werden, erscheinen deshalb NIE in diesen Listen – kaputte Kernfunktion des Status-Filter-Workflows.  
**Fix:** Status-Strings als Enumerations-Konstanten in einem separaten StatusEnum oder einer Konstante in NewLeads definieren und überall einheitlich referenzieren. Sofort: Alle Vorkommen auf einheitliches Leerzeichen normalisieren und eine Datenbank-Migration schreiben, die bestehende Records bereinigt.

### 🟠 Neu-Anlegen-Modal in external.blade sendet an external.update statt external.store  
**Modul:** CRM – Partner · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/employee/external/external.blade.php:63`  
**Problem:** Das 'Neue hinzufügen'-Modal-Formular hat als action route('external.update'), statt route('external.store'). external.update ruft ExternalPersonal::find($request->id)->update($request->all()) auf. Da das Neuanlegen-Formular keine id enthält, gibt find(null) null zurück, was einen Fatal Error (null->update()) verursacht. Das Anlegen neuer Zeitarbeitsfirmen ist dadurch komplett defekt.  
**Fix:** Action des Neu-Anlegen-Formulars auf route('external.store') korrigieren.

### 🟠 ExternalDepartmentsController: orWhere ohne Gruppierung umgeht ID-Filter (Cross-Company-Datenleck)  
**Modul:** CRM – Partner · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Product/Brand/ExternalDepartmentsController.php:27-34`  
**Problem:** Die Suchabfrage in index() beginnt mit ->where('external_personals.id', '=', $id)->where('external_departments.department', 'like', ...) und setzt danach mehrere ->orWhere() ohne Klammerung fort (orWhere(name), orWhere(position), orWhere(phone) etc.). Wegen SQL-Präzedenz werden diese orWhere-Bedingungen als eigenständige OR-Klauseln ausgewertet, die den ID-Filter vollständig umgehen. Eine Suchanfrage gibt Abteilungen anderer Unternehmen zurück, die keine Beziehung zur übergebenen $id haben.  
**Fix:** Alle orWhere-Bedingungen in einen ->where(function($q){...})-Block einschließen, damit der AND-Kontext für die ID-Bedingung erhalten bleibt.

### 🟠 Komplettes Löschen (delete_type=complete) löscht keine physischen Dateien auf Disk  
**Modul:** Vertrieb – Angebote · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/Offer/OfferController.php:1354-1386`  
**Problem:** Bei destroy() mit delete_type='complete' werden offer_folder_attachments und offer_pdf_prints per DB-Delete entfernt (Zeilen 1370-1375), aber die zugehörigen Dateien im Storage werden nicht gelöscht. Dagegen löscht deleteAttachment() in OfferFolderController::deleteAttachment() die Datei korrekt via Storage::disk('public')->delete(). Das führt zu unbegrenztem Wachstum verwaister Dateien im Storage bei vollständigen Angebotslöschungen.  
**Fix:** Vor dem DB-Delete in destroy(): $attachments = DB::table('offer_folder_attachments')->where('offer_id',...)->pluck('file_path'); foreach($attachments as $path) { Storage::disk('public')->delete($path); }. Analog für pdf_prints.

### 🟠 EmployeeController::update() liest ID aus $_POST direkt statt Route Model Binding  
**Modul:** Personal / HR · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Employee/EmployeeController.php:653`  
**Problem:** $id=$_POST['id']; (Zeile 653) — obwohl die Methode Employee $employee als Route-Model-Binding-Parameter deklariert, wird das empfangene Binding ignoriert und stattdessen $_POST['id'] direkt gelesen. Das umgeht sowohl Laravel-Validierung als auch implizites Binding und kann zu einer PHP-Notice führen, wenn 'id' im POST fehlt. Außerdem: Der Methodenname update() gibt 'The Employee has added successfully!' zurück statt einer Update-Meldung.  
**Fix:** Zeile 653 durch $id = $employee->id; ersetzen und den Employee direkt nutzen ($employee->name = ...). Fehlermeldung korrigieren.

### 🟠 Wizard-Stage 1 (Produkteigenschaft) speichert keine Felder  
**Modul:** Artikel · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Product/ProductController.php:1067-1088`  
**Problem:** Wenn stage==='1' wird nur product->updated_by gesetzt und gespeichert – keine technischen Felder werden übernommen. Der Wizard-Schritt 'Produkteigenschaft' (product_technical.blade.php) enthält ein separates eigenes <form> mit eigenem Submit. Das Haupt-Wizard-Formular sendet für Stage 1 demnach leer auf den store()-Endpunkt, der nichts tut. Nutzer erhalten jedoch 'Technische Daten gespeichert.' – irreführendes Feedback.  
**Fix:** Entweder die technischen Felder ebenfalls ins Haupt-Wizard-Payload aufnehmen und im Store-Handler verarbeiten, oder den Stage-1-Sub-Form-Submit als eigenständigen AJAX-Call auf einen separaten Endpunkt leiten und aus dem Wizard-Flow herausnehmen.

### 🟠 Fehlermeldung bei Store/Update-Fehler zeigt Erfolgsmeldung  
**Modul:** Lager · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Inventory/AssetInstallmentController.php:94, 172`  
**Problem:** Im else-Zweig (Fehlerfall) von store() und update() wird 'Der Datensatz wurd erfulgreich gespeichert!' als delete_msg (roter Hinweis) zurückgegeben. Der Nutzer sieht eine rote Box mit der Aussage, die Aktion sei erfolgreich gewesen – eine direkte Falschinformation nach Fehlschlag.  
**Fix:** Fehlermeldung korrigieren: z.B. 'Der Datensatz konnte nicht gespeichert werden.' und den Schlüssel save_msg durch delete_msg ersetzen (oder ein eigenes error_msg-Flash einführen).

### 🟠 SQL-Logikfehler in AssetInstallmentController::show() – type-Filter wird durch orWhere gebrochen  
**Modul:** Lager · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Inventory/AssetInstallmentController.php:109-112`  
**Problem:** Die Abfrage lautet: ->where('type','asset') ->where('assets.item', LIKE ?) ->orWhere('assets.id', LIKE ?). Das ungekapselte orWhere gilt auf Top-Level und erzeugt SQL: WHERE type='asset' AND item LIKE ? OR id LIKE ?. Bei einer Suche nach einer numerischen ID gibt die Abfrage alle asset_installments zurück, bei denen id der Suche entspricht – unabhängig vom type-Filter. Maschinen-Ratenzahlungen können so in der Asset-Liste erscheinen.  
**Fix:** Suchbedingungen in eine Closure kapseln: ->where(function($q) use ($search) { $q->where('assets.item','LIKE',"%$search%")->orWhere('assets.id','LIKE',"%$search%"); })

### 🟠 BEG-Förderungen: create()- und edit()-Views existieren nicht (ViewNotFoundException)  
**Modul:** Finanzen · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/BegFundingsController.php:19,66`  
**Problem:** create() gibt view('beg_fundings.create') zurück, edit() gibt view('beg_fundings.edit') zurück. Diese Pfade existieren nicht. Die vorhandenen Views liegen unter admin.customer_economic_calculation.beg_fundings.{create,edit}. Aufruf von /beg-fundings/create und /beg-fundings/{id}/edit wirft eine ViewNotFoundException.  
**Fix:** Korrekten Pfad: return view('admin.customer_economic_calculation.beg_fundings.create') und ...edit verwenden.

### 🟠 TaskPhaseController::clone() referenziert undefinierte Klasse TaskSubTasks (statt TaskSubTask)  
**Modul:** Admin & System · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Phase/TaskPhaseController.php:453`  
**Problem:** Zeile 9 importiert App\Models\TaskSubTask (korrekte Klasse), aber Zeile 453 ruft TaskSubTasks::create([...]) auf. TaskSubTasks existiert nicht; bei Ausführung des Clone-Pfads (Kopieren von Sub-Tasks) tritt ein fataler Error (Class 'TaskSubTasks' not found) auf. Der gesamte Clone-Feature-Zweig ist kaputt.  
**Fix:** TaskSubTasks::create([...]) auf TaskSubTask::create([...]) korrigieren.

### 🟠 TaskPhaseController::storeNewPhase() erstellt unbeabsichtigt einen ProjectMontageChecklist-Datensatz  
**Modul:** Admin & System · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Phase/TaskPhaseController.php:352-364`  
**Problem:** storeNewPhase() erzeugt nach dem Speichern der Phase immer einen neuen ProjectMontageChecklist mit Hardcode-Werten (plan_montage=1, list_name='New Draft' usw.). Diese Funktion heißt "Phase erstellen", nicht "Checkliste erstellen". Der Seiteneffekt ist weder dokumentiert noch für den Aufrufer erkennbar; er erzeugt Daten-Müll bei jedem Phase-Anlegen über diese Route.  
**Fix:** Den Checklist-Erstellungsblock entfernen oder in eine separate, explizit aufgerufene Methode auslagern.

### 🟠 N einzelne INSERTs statt Batch-Insert in syncPlannerItemEmployees  
**Modul:** Planner · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Planner/PlannerPlanController.php:286-294`  
**Problem:** syncPlannerItemEmployees() löscht erst alle Einträge für ein PlannerItem und schreibt dann jeden Employee-Eintrag einzeln via DB::table('planner_item_employees')->insert([...]). Diese Methode wird für jedes syncierte PlannerItem (Termin, Ticket, Aufgabe) aufgerufen; bei 20 PlannerItems mit je 3 Mitarbeitern = 60 einzelne INSERT-Statements.  
**Fix:** Rows als Array aufbauen und mit einem einzigen DB::table('planner_item_employees')->insert($rows) schreiben.

### 🟠 N firstOrCreate-Aufrufe beim Speichern eines Overdue-Reports (ein Eintrag pro aktivem Mitarbeiter)  
**Modul:** Reporting · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Report/OverdueCenterController.php:1050-1060`  
**Problem:** Nach dem Speichern eines OverdueReport wird für jeden aktiven Mitarbeiter (Employee::pluck('id')) ein OverdueReportRead::firstOrCreate() aufgerufen. Bei 30 aktiven Mitarbeitern entstehen 30 SELECT+INSERT-Paare. Da kein Batch-Mechanismus verwendet wird, erhöht sich die Latenz des Speicher-Requests proportional zur Mitarbeiterzahl.  
**Fix:** Zeilen-Array aufbauen und DB::table('overdue_report_reads')->insertOrIgnore($rows) verwenden.

### 🟠 145 Schema::hasTable/hasColumn-Aufrufe ohne Request-Caching in PlannerPlanController  
**Modul:** Planner · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Planner/PlannerPlanController.php:182,247,470,562,612 (145 Stellen)`  
**Problem:** PlannerPlanController enthält 145 Schema::hasTable()- und Schema::hasColumn()-Aufrufe. Im Gegensatz zu OverdueCenterController (welcher einen statischen $schemaCache einsetzt) fehlt hier jegliches Caching. Jeder Schema-Check löst eine INFORMATION_SCHEMA-Abfrage aus (oder wird von Laravels internem SchemaBuilder gecacht, aber nur pro Request-Instanz). Bei syncAndLoad wird ein erheblicher Teil davon redundant mehrfach aufgerufen.  
**Fix:** Dasselbe statische Caching-Pattern wie in OverdueCenterController (Zeile 50-53) implementieren: private static array $schemaCache = [] mit hasTable()/hasColumn()-Wrapper-Methoden.

### 🟡 BitrixController.contact_list() und stage() rufen dd() auf – produktionsunfähig  
**Modul:** CRM – Kommunikation · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/BitrixController.php:47 / 92`  
**Problem:** Beide öffentlich erreichbaren Methoden (contact_list, stage) enden ausschließlich mit dd($data). Jeder Aufruf dieser Routen bricht den Request mit einem Debugger-Dump ab. Die Methoden liefern keine nutzbare Antwort und können nicht für die eigentliche Funktion (Kontaktliste anzeigen) eingesetzt werden.  
**Fix:** dd()-Aufrufe entfernen und durch eine echte Response ersetzen (return view(…) oder return response()->json(…)), oder die Routen bis zur Fertigstellung als 503 deklarieren.

### 🟡 Junk/Delete-Actor-Join auf employees.id schlägt fehl: Spalte speichert Namens-String  
**Modul:** CRM – Leads & Kunden · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:3337,11392,11612`  
**Problem:** destroyWithReason() speichert deleted_by = auth()->user()?->name ?? auth()->id() (L3337), also den Namens-String des Laravel-Auth-Users. deleted_lead() (L11612) versucht dann employees ON employees.id = new_leads.deleted_by zu joinen – dieser Join liefert NIE ein Ergebnis, da employees.id eine Integer-Spalte ist und deleted_by ein Namens-String. Gleiches Problem für junked_by/junk_actor (L3362, L11392). Die Anzeige des Lösch-/Junk-Akteurs in den Listen ist damit dauerhaft leer.  
**Fix:** Entweder employee_id statt Namen in deleted_by/junked_by speichern (wenn User.name die Employee-ID ist, direkt (int)auth()->user()->name verwenden), oder als Spalten-Typ VARCHAR belassen und ohne JOIN direkt als Text anzeigen.

### 🟡 Feinaufmaß wird bei Erstellung aus Auftrag sofort auf status='sent' gesetzt ohne explizite Nutzeraktion  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Deal/DealMeasurementController.php:470–472`  
**Problem:** storeFromDeal() setzt beim Anlegen eines neuen DealMeasurement den Status auf 'sent' und sent_at auf now(), sobald ein OfferDetail vorhanden ist. Der Benutzer hat diesen Versand zu keinem Zeitpunkt bestätigt. Damit erscheint ein gerade erzeugtes Feinaufmaß in der Kanban-Übersicht sofort als 'gesendet', was die Planung verfälscht und Statushistorien korrumpiert.  
**Fix:** Status initial immer auf 'draft' setzen und sent_at/sent_by nur setzen, wenn der Benutzer eine explizite Sende-Aktion auslöst (z.B. eigener POST-Endpunkt deal.measurements.send mit getrenntem Workflow-Schritt).

### 🟡 Appointment destroy(): Status-Update nach Soft-Delete – falsche Reihenfolge  
**Modul:** Projekte & Planer · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Appointment/MainAppointmentController.php:1534`  
**Problem:** `$data->delete()` wird aufgerufen (Z. 1534), was `deleted_at` setzt (SoftDelete). Anschließend wird `$data->update(['status' => 'GELÖSCHT'])` aufgerufen (Z. 1536). Da das Modell-Objekt die `deleted_at` bereits gesetzt hat, arbeitet das Update intern zwar auf der Instanz, bei einem Reload würde `withoutTrashed()` diesen Datensatz nicht mehr finden. Der Status `'GELÖSCHT'` ist außerdem redundant, wenn `deleted_at` als kanonisches Lösch-Signal dient. Die gesamte Löschaktion läuft zudem ohne `DB::transaction`.  
**Fix:** Reihenfolge umkehren: zuerst Status setzen, dann `delete()`. Besser: Status-Feld ganz entfernen und stattdessen `deleted_at` als einzige Quelle der Wahrheit verwenden. Beide Operationen in `DB::transaction()` einwickeln.

### 🟡 ProblemController@updateStatus akzeptiert beliebige Status-Strings  
**Modul:** Support – Tickets · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Ticket/ProblemController.php:1592-1594`  
**Problem:** Die Methode updateStatus (Route POST /ticket/{problem}/update-status) validiert 'status' nur als string max:255, ohne Enum-Einschränkung. Zwar normalisiert normalizeTicketStatus() einige Werte (Zeile 2741), aber unbekannte Werte werden ungefiltert gespeichert (default-Zweig: $status ?: 'offen'). Die Kanban-Route ticket/kanban/update dagegen validiert korrekt mit in:offen,open,process,end,junk (Zeile 1449). Dadurch können beliebige Status-Strings wie 'deleted' oder 'hacked' persistent in der Datenbank landen.  
**Fix:** In updateStatus() die Validierung angleichen: 'status' => ['required', 'string', 'in:offen,process,end,junk']. Alternativ normalizeTicketStatus() so ändern, dass ungültige Werte eine ValidationException werfen.

### 🟡 BranchExpense: Gespeicherte Summenfelder werden nie aktualisiert  
**Modul:** Finanzen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/BranchExpenseController.php:203-213`  
**Problem:** Die Tabelle branch_expenses hat Spalten rent_total, insurance_total, employee_total, installment_total, other_total, total (aus Migration 2026_06_16_000001). validatePayload() überträgt diese Felder nie. Weder BranchExpenseRentController noch BranchExpenseInsuranceController schreiben nach Add/Update/Delete der Unterposten zurück in die Elterntabelle. Die DB-Spalten bleiben dauerhaft 0, obwohl der Controller die Werte dynamisch berechnet und korrekt in der API zurückgibt – Inkonsistenz zwischen DB und berechneter Realität.  
**Fix:** Nach jeder Änderung an Unterposten (Rents, Insurances, OtherCosts) ein Model-Observer oder Methode aufrufen, die den BranchExpense-Parent mit den aggregierten Summen aktualisiert.

### 🟡 resequenceActivities() ignoriert NULL-parentId durch redundante Bedingung  
**Modul:** Admin & System · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Phase/TaskPhaseController.php:1042`  
**Problem:** ->where($parentId === null ? 'parent_id' : 'parent_id', $parentId) – Beide Zweige des ternären Ausdrucks liefern den String 'parent_id'. Bei parentId === null sollte ->whereNull('parent_id') verwendet werden, stattdessen wird ->where('parent_id', null) generiert, was als WHERE parent_id IS NULL oder WHERE parent_id = '' interpretiert wird (DB-abhängig). Die Methode sortiert daher beim Verschieben von Top-Level-Aktivitäten möglicherweise die falsche Teilmenge neu.  
**Fix:** Bedingung korrigieren: if ($parentId === null) { $q->whereNull('parent_id'); } else { $q->where('parent_id', $parentId); }

### 🟡 N einzelne EmployeesPersonalTask::create in foreach beim Zuweisen von Aufgaben  
**Modul:** PersonalTask · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Task/PersonalTaskController.php:1022-1036`  
**Problem:** Beim Speichern einer Aufgabenzuweisung wird für jeden Mitarbeiter in $allEmployeeIds ein einzelnes EmployeesPersonalTask::create() ausgeführt (Zeile 1023). Dasselbe Muster findet sich an Zeile 860, 1421 und 5681-5682. Gleiche Schwachstelle in MainAppointmentController Zeile 608-613 (MainAppointmentEmployee::create pro Mitarbeiter).  
**Fix:** Array der zu erstellenden Rows aufbauen und mit DB::table('employees_personal_tasks')->insert($rows) bzw. EmployeesPersonalTask::insert() in einem Schritt schreiben.
