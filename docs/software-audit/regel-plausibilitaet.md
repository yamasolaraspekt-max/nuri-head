# Audit – Plausibilität

Funde: 25  ·  🔴 2 kritisch · 🟠 7 hoch · 🟡 15 mittel · ⚪ 1 niedrig

### 🔴 IMAP-Passwörter im Klartext in DB und HTML gespeichert und exponiert  
**Modul:** CRM – Kommunikation · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `resources/views/admin/lead_email/email_config/account.blade.php:276 / app/Http/Controllers/Email/LeadEmailAccountsController.php:118`  
**Problem:** Das IMAP-Passwort wird unverschlüsselt als VARCHAR in der Datenbank gespeichert (Migration Zeile 18: 'store encrypted if possible' – Kommentar ohne Umsetzung) und direkt als HTML-data-Attribut ins DOM gerendert (data-password="{{ $account->password }}"). Jeder Nutzer, der die Seite aufruft und DevTools öffnet, sieht alle IMAP-Passwörter aller Konten im Klartext. Außerdem wird das Passwort im Edit-Modal als Plaintext in ein type='text'-Feld gefüllt.  
**Fix:** 1) Laravel Crypt::encryptString/decryptString im Model via $casts = ['password' => 'encrypted'] aktivieren. 2) Im Edit-Formular kein Passwort aus der DB vorausfüllen – stattdessen Platzhalter-Asterisks und nur bei Änderung ein neues Passwort akzeptieren. 3) HTML-Attribut data-password entfernen.

### 🔴 Undefinierte Variable $oldValue im Audit-Log führt zu PHP-Fehler  
**Modul:** CRM – Leads & Kunden · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:6411`  
**Problem:** In updateField() wird in der logActivity-Zeile $oldValue referenziert (L6411: ['from' => $oldValue, 'to' => $value]), obwohl diese Variable nirgends in der Methode definiert wird. In PHP 8 führt das zumindest zu einer Deprecation-Notice, in früheren Versionen zu einem Undefined variable-Notice. Das bedeutet: jedes Mal wenn ein Feld über den AJAX-Endpunkt update-field geändert wird, schlägt die Audit-Protokollierung mit einem PHP-Fehler fehl (je nach error_reporting kann die gesamte Antwort fehlschlagen).  
**Fix:** Vor dem Update den Originalwert lesen: $oldValue = $alternative->{$dbField} ?? null; – direkt nach dem $alternative-Laden (L6389) einfügen.

### 🟠 Hardcodierter Tomorrow.io API-Key im Quellcode  
**Modul:** Dashboard & Berichte · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/EmployeeDashboardController.php:735`  
**Problem:** $apiKey = '810lNaHm47aq72XReqTSkAtQRi1P9jQh' ist direkt im Controller hartcodiert (Kommentar: 'Replace with your actual API key' – das wurde nie gemacht). Der Key ist im Git-Repository sichtbar und kann zu unberechtigter API-Nutzung führen.  
**Fix:** API-Key in .env als TOMORROW_IO_API_KEY auslagern und per config('services.tomorrow_io.key') einbinden. Key sofort in Tomorrow.io rotieren.

### 🟠 Hardcodierter API-Token und hardcodierte branch_id=1 in Produktionscode  
**Modul:** CRM – Anfragen · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Wordpress/FusionFormSubmissionController.php:39,74,99,132,168,366,455`  
**Problem:** Der externe API-Token (3Ho7JAeHMxL8kkjRDx83fhwq) ist an fuenf Stellen direkt in den Quellcode eingebettet. Token-Rotation erfordert ein Code-Deployment. Ausserdem ist branch_id = 1 in importSingle() (Zeile 366) und importAll() (Zeile 455) hartcodiert: bei Multi-Branch-Betrieb werden alle Webseiten-Anfragen pauschal der ersten Niederlassung zugewiesen.  
**Fix:** Token in .env / config/services.php auslagern (analog zu config('services.fusion_forms.token') wie bereits partiell vorhanden). Fuer branch_id: die Niederlassung aus dem Formularinhalt bestimmen oder als konfigurierbaren Wert in config/ ablegen.

### 🟠 users.name wird als employee_id missbraucht – fragile Identitätsauflösung im gesamten Modul  
**Modul:** Vertrieb – Angebote · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/Offer/OfferController.php:61-62 und OfferFolderController.php:641-642 und OfferCommentController.php:30`  
**Problem:** Die employee_id wird in mindestens 5 Methoden (employeeId(), currentEmployeeId(), createOffer(), clone(), OfferCommentController::store()) aus auth()->user()->name ausgelesen (is_numeric($user->name)), da users.name den Mitarbeiter-PK speichert. Das ist semantisch falsch, fehleranfällig (echter Name überschreibt die ID), nicht selbstdokumentierend und führt zu falschen Zuordnungen, wenn ein User angelegt wird, dessen name zufällig numerisch ist.  
**Fix:** Eine dedizierte Spalte users.employee_id (FK zu employees.id) einführen und konsequent nutzen. Übergangslösung: users.employee_id-Kolumne prüfen (existiert lt. Code schon: $user->employee_id) und als alleinige Quelle verwenden.

### 🟠 Qualifikations-Store übergibt unkontrollierte Rohdaten aus $request->qual direkt an Qualification::create()  
**Modul:** Personal / HR · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Employee/Position/QualificationController.php:42-44`  
**Problem:** foreach ($request->qual as $qualification) { Qualification::create($qualification); } — $qualification ist ein nicht-gefiltertes Unter-Array aus dem Request. Das emp_id-Feld (wird in die Tabelle gespeichert) ist nicht in den Validierungsregeln enthalten und nicht als 'exists:employees,id' geprüft. Ein Angreifer kann beliebige emp_id-Werte übermitteln, um Qualifikationen einem fremden Mitarbeiterprofil zuzuordnen.  
**Fix:** Validierung um 'qual.*.emp_id' => 'required|integer|exists:employees,id' erweitern. Im Schleifenbody explizit nur validierte Felder übergeben: Qualification::create(['emp_id'=>$qualification['emp_id'], 'degree'=>...]).

### 🟠 Mitarbeiterbild-Upload ohne MIME-Typ-Validierung (Dateiendung aus Originalnamen)  
**Modul:** Personal / HR · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Employee/EmployeeController.php:339,624,660,1339`  
**Problem:** An vier Stellen wird das hochgeladene Bild nur mit getClientOriginalExtension() benannt, ohne any MIME-Typ- oder Bildvalidierung. Eine Datei mit Namen 'shell.php' umbenannt in 'shell.jpg' könnte mit Endung 'jpg' hochgeladen und unter /images/employee/ abgelegt werden. Zusätzlich wird an Zeilen 625/661 in das nicht-public_path-Verzeichnis 'images/employee/' (relativ zum Working Directory) geschrieben, nicht public_path().  
**Fix:** Validierung hinzufügen: 'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'. Alle move()-Aufrufe auf public_path('images/employee/') vereinheitlichen.

### 🟠 Mass-Assignment ohne Validierung in BranchController::offerUpdate()  
**Modul:** Admin & System · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Branch/BranchController.php:303-317`  
**Problem:** $data = $request->except(['logo_file']); branch->update($data); – Alle Request-Parameter außer logo_file werden ungefiltert an das Model übergeben. Obwohl Branch::$fillable gesetzt ist, können trotzdem beliebige fillable Felder (iban, tax, vat, status usw.) durch einen API-Aufruf überschrieben werden. Es findet keine Validierung statt (kein Ruleset, kein FormRequest). Das steht im starken Kontrast zur vollständigen Validierung in store() und update().  
**Fix:** Gleichen $this->rules()-Satz wie in store()/update() auch für offerUpdate() verwenden; mindestens $request->validate([...]) vor branch->update($validated) aufrufen.

### 🟡 Urlaubs-Gesamttage werden als Summe aus verbrauchten + verbleibenden Tagen berechnet  
**Modul:** Dashboard & Berichte · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/EmployeeDashboardController.php:137`  
**Problem:** $annualLeaveTotal = $vacationDaysUsed + $vacationDaysRemain nimmt an, dass remaining_day die tatsächlich noch verfügbaren Tage im Jahresbudget darstellt. Der Wert wird aber aus dem neuesten Eintrag (lastLeaveRow) gezogen, der systembedingt 0 sein kann (alle Einträge verbraucht). Das ergibt einen Gesamtwert, der vom echten Jahresbudget abweichen kann. Dieselbe Logik ist in mobile() dupliziert (Zeile 312).  
**Fix:** Urlaubs-Gesamtanspruch direkt aus dem Employee-Profil (z.B. employees.leave_days oder einer Konfigurationstabelle) lesen, nicht als Summe aus zwei Transaktionswerten berechnen.

### 🟡 SSL-Zertifikat-Validierung beim IMAP-Abruf deaktiviert  
**Modul:** CRM – Kommunikation · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Email/LeadEmailReaderController.php:50`  
**Problem:** 'validate_cert' => false ist beim produktiven IMAP-Client hartcodiert. Dadurch ist die Verbindung anfällig für Man-in-the-Middle-Angriffe; E-Mail-Inhalte und Zugangsdaten können abgefangen werden. Im testConnection()-Pendant (LeadEmailAccountsController:167) ist validate_cert => true gesetzt – inkonsistente Behandlung.  
**Fix:** validate_cert auf true setzen und sicherstellen, dass der Server ein gültiges Zertifikat hat. Wenn selbst-signierte Zertifikate nötig sind, dies konfigurierbar machen (z.B. per .env IMAP_VALIDATE_CERT=true).

### 🟡 validateInquiryRequest() erlaubt vollstaendig leere Anfragen (alle Felder nullable)  
**Modul:** CRM – Anfragen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Inquiry/InquiryController.php:91-133`  
**Problem:** Die gemeinsame Validierungsregel validateInquiryRequest() markiert saemtliche Kundendatenfelder als nullable: name, lastname, email, phone, description, pre_type, street, city, postcode usw. Lediglich branch_id ist required. Das erlaubt das Speichern komplett leerer Anfragen (bis auf die Niederlassung). Der spaetere Verifizierungsschritt (InquiryVerificationController) erzwingt Felder, aber bis dahin koennen Phantomdatensaetze entstehen.  
**Fix:** Mindestvalidierung fuer Kernfelder einfuehren, z.B. required_without_all fuer Kombination name+firma+email oder zumindest eine der drei Kontaktmoeglichkeiten (phone, telephone, email) als required_without_all.

### 🟡 Kundennummer-Generierung per mt_rand ohne DB-Unique-Constraint – Race Condition möglich  
**Modul:** CRM – Leads & Kunden · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:605,606,607`  
**Problem:** Die Kundennummer wird via do-while mt_rand (L606) mit anschließendem exists()-Check (L607) generiert. Obwohl dies innerhalb einer DB::transaction liegt, besteht bei gleichzeitigen Anfragen eine TOCTOU-Race-Condition, da kein SELECT FOR UPDATE oder datenbankbasierter Unique-Constraint auf customer_no existiert (Migration L17: nur nullable string, kein unique()). Zwei simultane Requests können dieselbe Zufallsnummer belegen.  
**Fix:** In der Migration customer_no als UNIQUE INDEX anlegen. Beim Insert dann auf doppelten Unique-Constraint reagieren (try/catch IntegrityConstraintViolationException) statt des unsicheren Pre-Check-Musters. Alternativ Auto-Increment-basierte Sequenz verwenden.

### 🟡 Mehrfaches vollständiges Request-Logging inkl. PII-Daten  
**Modul:** CRM – Leads & Kunden · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:520,1012,1141,1450`  
**Problem:** Vor der Validierung werden komplette Request-Daten geloggt (L520: Log::info('lead Request: ', [$request->all()]), L1012, L1141, L1450). Diese enthalten PII wie Name, Nachname, Adresse, E-Mail, Telefon der Kunden im Klartext in den Application-Logs. DSGVO verlangt Datensparsamkeit in Logs.  
**Fix:** Log-Aufrufe mit request->all() entfernen oder auf nicht-sensible Felder beschränken (z.B. nur IDs, Produkt-IDs). Bei Debug-Bedarf dedizierte Debug-Middleware mit konfigurierbarem Log-Level einsetzen.

### 🟡 ExternalPersonalController.index: Suche verknüpft Felder mit AND statt OR  
**Modul:** CRM – Partner · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Product/Brand/ExternalPersonalController.php:27-29`  
**Problem:** Die Suchabfrage lautet ->where('company_name', 'LIKE', "%$search%")->where('admin_name', 'LIKE', "%$search%"). Beide Bedingungen sind mit AND verknüpft, sodass ein Eintrag nur gefunden wird, wenn der Suchbegriff gleichzeitig im Firmenname UND im Administratornamen vorkommt. Eine Suche nach einem Firmennamen allein liefert kein Ergebnis, was für Anwender völlig unerwartet ist.  
**Fix:** Die where-Bedingungen in eine ->where(function($q) use ($search) { $q->where(...)->orWhere(...); })-Gruppe zusammenfassen.

### 🟡 DealInvoice store() speichert unvalidierte Felder (product_id, alternative_id, employee_id …) die nicht im fillable stehen  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Deal/DealInvoiceController.php:159–177`  
**Problem:** DealInvoice::create() wird mit product_id, alternative_id, department_id, employee_id, service_id aufgerufen, die (1) nicht im Validator geprüft werden (kein exists:, kein nullable) und (2) nicht in $fillable des Modells stehen. Dadurch werden die Werte still ignoriert (Mass-Assignment Protection greift), obwohl der Controller-Code suggeriert, sie würden gespeichert. Fachlich fehlt ein Bezug der Rechnung zu Produkt/Objekt/Mitarbeiter in der Datenbank.  
**Fix:** Felder in $fillable des DealInvoice-Models ergänzen. Im Validator nullable|integer|exists:...-Regeln hinzufügen. Alternativ eine Migration prüfen, ob die Spalten überhaupt in der Tabelle existieren.

### 🟡 invoice_no ohne Eindeutigkeitsprüfung bei Anlage über InvoiceController  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Invoice/InvoiceController.php:1061 und 209–213`  
**Problem:** Die Validierung in validateInvoice() definiert 'invoice_no' als ['nullable', 'string', 'max:50'] ohne unique-Constraint. Damit können mehrere Rechnungen mit identischer Rechnungsnummer angelegt werden. Die automatische Nummernvergabe (auto number if empty, Zeile 209) ist ein Fallback, aber der User kann manuell eine bereits vergabene Nummer eingeben, ohne blockiert zu werden.  
**Fix:** Validierungsregel ergänzen: 'invoice_no' => ['nullable', 'string', 'max:50', Rule::unique('invoices', 'invoice_no')->ignore($invoice->id ?? null)]. Für das Update-Formular die ignore()-Klausel mit der aktuellen Invoice-ID nutzen.

### 🟡 Datei-Upload ohne Typ- und Größenvalidierung in ProblemController@image  
**Modul:** Support – Tickets · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Ticket/ProblemController.php:533-538`  
**Problem:** Der Endpunkt POST /problem_photo (problem.save_photo) validiert Datei-Felder nur als 'nullable' ohne mimes-, max- oder file-Regel. Jede beliebige Dateierweiterung und -größe wird akzeptiert und im public/ticket-files-Ordner gespeichert. Im Vergleich dazu validiert TicketImageController.php (Zeile 17) korrekt: 'file', 'mimes:jpeg,...pdf,...', 'max:12288'. Die Inkonsistenz erlaubt Upload von serverseitig ausführbaren Dateien (z.B. .php) über diesen älteren Endpunkt.  
**Fix:** Die Validierung in ProblemController@image an TicketImageController angleichen: 'image' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:10240']. Den Endpunkt langfristig zugunsten von TicketImageController@store deprecieren.

### 🟡 employees.remaining_day dreifach in $fillable deklariert  
**Modul:** Personal / HR · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Models/Employee.php:15,30,42`  
**Problem:** 'remaining_day' erscheint an Zeilen 15, 30 und 42 im $fillable-Array des Employee-Modells. Das ist redundant und ein Indiz für unkontrollierte Modell-Erweiterung. Außerdem ist 'mother-tongue' (Zeile 60) mit Bindestrich eingetragen, was dem DB-Spaltennamen 'mother_tongue' (Unterstrich, vgl. Zeile 723 in EmployeeController: profile_update) nicht entspricht und Massenzuweisungen ins Leere laufen lässt.  
**Fix:** Duplikate aus $fillable entfernen. 'mother-tongue' zu 'mother_tongue' korrigieren. $fillable-Analyse gegen tatsächliche DB-Schemafelder (migration) abgleichen.

### 🟡 update()-Methode im ProductController hat keinerlei Validierung  
**Modul:** Artikel · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Product/ProductController.php:1495-1534`  
**Problem:** Im Gegensatz zu store() (mit Validator::make und Pflichtfeldern) akzeptiert update() alle Rohdaten ohne $request->validate() oder FormRequest. Felder wie brand_id (integer, foreign key) und article_group (integer, foreign key) werden unkontrolliert in den Payload übernommen, was Integritätsverletzungen oder leere Pflichtfelder ermöglicht.  
**Fix:** FormRequest (ProductUpdateRequest) oder inline-Validierung nach dem Muster von store() ergänzen: mindestens product, model, category als required, brand_id als nullable|integer|exists:brands,id.

### 🟡 Widersprüchliche Pflichtfeld-Validierung zwischen storeAjax() und store() für Inventory  
**Modul:** Lager · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Inventory/InventoryController.php:300, 309, 710, 718`  
**Problem:** storeAjax() (Zeile 300, 309) deklariert 'location' und 'row' als required. store() (Zeile 710, 718) macht beide nullable. Beide Methoden schreiben in dieselbe Inventory-Tabelle. Über /ajax/inventory/store können Datensätze ohne Pflichtort und ohne Zeile angelegt werden, was die Datenqualität untergräbt.  
**Fix:** Validierungsregeln in eine gemeinsame private Methode oder einen FormRequest auslagern; 'location' und 'row' einheitlich behandeln.

### 🟡 Analytics: Raten- und Personalkosten ignorieren den Jahresfilter  
**Modul:** Finanzen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/BranchExpenseController.php:98-104`  
**Problem:** analytics() extrahiert $branchIds aus den gefilterten BranchExpense-Datensätzen und übergibt sie an employeeSalaryTotal() und installmentTotal(). Diese Methoden aggregieren aber ALLE Datensätze der jeweiligen Branch (ohne Jahresbezug). Wird z.B. nur Jahr 2024 gefiltert, zeigt der Analytics-Block trotzdem die Gesamtkosten über alle Jahre, was die KPI-Karte 'Gesamt' systematisch überhöht.  
**Fix:** installmentTotal() und employeeSalaryTotal() auf die konkreten $ids der gefilterten BranchExpenses einschränken (z.B. per Subquery auf asset_installments.branch_id mit year-Bezug oder via Snapshot-Datum).

### 🟡 BegFundings update(): Validierung prüft nur 3 Felder, übergibt aber $request->all()  
**Modul:** Finanzen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/BegFundingsController.php:71-77`  
**Problem:** update() validiert heating_type, basis_percentage, max_funding_amount. Dann wird $begFunding->update($request->all()) aufgerufen. Das BegFundings-Modell hat 21 fillable Felder (lead_id, alternative_id, product_id etc.). Ein Angreifer kann lead_id oder alternative_id durch einfaches Hinzufügen eines POST-Parameters überschreiben.  
**Fix:** update() auf $begFunding->update($request->only(['heating_type','basis_percentage','max_funding_amount'])) oder $request->validated() umstellen.

### 🟡 UserController::store() setzt is_active immer auf 1, unabhängig vom Checkbox-Wert  
**Modul:** Admin & System · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/User/UserController.php:131`  
**Problem:** $user->is_active = $request->has('is_active') ? 1 : 1; – Beide Zweige sind identisch. Das is_active-Feld kann beim Anlegen eines Benutzers nie auf 0 gesetzt werden, selbst wenn die Checkbox deaktiviert ist. Identisches Problem in limit_store() (Zeile 293). Das neue adminUsersStore() macht es richtig (Zeile 664).  
**Fix:** Korrigieren auf: $user->is_active = $request->boolean('is_active') ? 1 : 0;

### ⚪ OverdueCenterController lädt bis zu 4.100 Zeilen pro AJAX-Aufruf ohne DB-seitige Sortierung  
**Modul:** Reporting · **Severity:** niedrig · · unverifiziert  
**Ort:** `app/Http/Controllers/Report/OverdueCenterController.php:892-899`  
**Problem:** Die Sortierung (oldest/newest/most_overdue) wird vollständig in PHP auf dem gesamten in-memory Datensatz ausgeführt (sortBy/sortByDesc) bevor per slice() paginiert wird. DB-seitige ORDER BY wird damit nie genutzt, auch wenn nur die erste Seite angezeigt wird.  
**Fix:** ORDER BY direkt in die SQL-Abfragen jedes Sub-Typs einbauen und LIMIT auf seitengerechte Größe reduzieren (z. B. perPage + offset). Alternativ Cursor-Pagination pro Typ mit AJAX-Nachladen.
