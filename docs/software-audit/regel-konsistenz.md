# Audit – Konsistenz

Funde: 38  ·  🔴 1 kritisch · 🟠 9 hoch · 🟡 22 mittel · ⚪ 6 niedrig

### 🔴 Flash-Message-Key 'update_msg' vs. 'updated_msg' – Silent-Fail in 143 Views  
**Modul:** Querschnitt · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/EconomicCalculationController.php:84, resources/views/admin/customer_economic_calculation/economic_calculation/create.blade.php:117`  
**Problem:** Controller setzen den Session-Key 'update_msg' (19 Stellen), während 143 Views session('updated_msg') lesen. Die Meldung wird dadurch nie angezeigt. Exemplarisch: create.blade.php prüft Session::has('update_msg') korrekt, rendert aber session('updated_msg') – der Wert ist immer leer.  
**Fix:** Vereinheitlichen auf einen Key, z. B. 'update_msg'. Alle 143 Vorkommen von session('updated_msg') in Views sowie die 6 Controller-Stellen, die 'updated_msg' setzen, müssen angeglichen werden.

### 🟠 auth()->user()->name wird als Employee-ID misbraucht – semantisch falsch und fragil  
**Modul:** CRM – Leads & Kunden · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:3440,4552,4859,973,1979`  
**Problem:** Die App speichert offenbar die Employee-ID im User.name-Feld. Dies wird an über 20 Stellen mit auth()->user()->name als Integer-Employee-ID verwendet (z.B. L3440: $empId = (int) auth()->user()->name, L973: 'employee_id' => auth()->user()->name). Gleichzeitig wird an anderen Stellen $currentName = auth()->user()->name als Namens-String zum Vergleich mit employees.name genutzt (L4575: where('contact_person.name', $currentName), L4589: where('lp_inner.name', $currentName)). Das führt zu falschen Filterergebnissen wenn Employee-ID ≠ Employee-Name (was immer der Fall sein sollte). Der junked_by/deleted_by-Join (L11392, L11612) joiniert ->employees ON employees.id = junked_by, aber gespeichert wird auth()->user()->name (Namens-String), nicht die ID.  
**Fix:** Entweder eigene employees.user_id-Spalte einführen und Auth-User dediziert mit Employee verknüpfen, oder auth()->user()->employee_id-Relation anlegen. Dann alle auth()->user()->name-Stellen auf auth()->user()->employee->id bzw. auth()->user()->employee->name auflösen.

### 🟠 PersonalTask-Status-Werte inkonsistent zwischen View und Controller  
**Modul:** Projekte & Planer · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/todo/personal/task_details.blade.php:265`  
**Problem:** Die View `task_details.blade.php` mappt und bietet Status-Werte an, die im Controller nicht existieren: `'new'`, `'start'`, `'on_going'`, `'on_review'` (Z. 265–306). Der Controller `PersonalTaskController` verwendet `'open'`, `'on_progress'`, `'completed'`, `'pause'`, `'cancel'` (Z. 111, 125, 131, 137). `PersonalTaskBoardController` kennt zusätzlich noch `'working'`, `'in_progress'`, `'junk'`, `'rejected'`. Das Ergebnis: Der Status-Label zeigt für reale Datensätze immer 'Status unbekannt'.  
**Fix:** Einen einzigen kanonischen Status-Enum/Constant definieren und in Controller, Validation, Seeder und Views einheitlich verwenden. Alle Aliase (`new`→`open`, `on_going`→`on_progress`) in einer Migration bereinigen.

### 🟠 Status-Werte inkonsistent: DB-Default 'active', Code nutzt 'Published'/'Unpublished'  
**Modul:** Artikel · **Severity:** hoch · ✅ bestätigt  
**Ort:** `database/migrations/2023_06_22_085602_create_products_table.php:33 vs app/Http/Controllers/Product/ProductController.php:740,760,1038`  
**Problem:** Das Migration-Default für status ist 'active'. Im Controller und in allen Filter-/Status-Abfragen werden ausschließlich 'Published' und 'Unpublished' genutzt. Produkte, die vor der Codeänderung angelegt oder per Seed/Import mit status='active' erstellt wurden, erscheinen in keinem der Filter und gelten weder als aktiv noch inaktiv.  
**Fix:** Migration mit DB::statement("UPDATE products SET status='Published' WHERE status='active'") korrigieren und den DB-Default auf 'Published' setzen, oder einen Accessor im Model anlegen der 'active' auf 'Published' mappt.

### 🟠 Inkonsistente Flash-Message-Keys: 7 verschiedene Schlüsselnamen nebeneinander  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/ (app-weit)`  
**Problem:** Es existieren gleichzeitig: 'save_msg' (213x), 'delete_msg' (172x), 'success' (62x), 'error' (45x), 'update_msg' (19x), 'error_msg' (16x), 'updated_msg' (6x), 'not_save' (8x), 'not_msg' (6x), 'ok' (2x in Views). Jede View muss individuell entscheiden, welche Schlüssel sie abfragt. Es gibt keine zentrale Flash-Komponente im Layout.  
**Fix:** Standard-Konvention festlegen (z. B. 'success', 'error', 'warning', 'info') und alle Flash-Setzungen und View-Abfragen migrieren. Eine zentrale Blade-Komponente oder Layout-Block für Flash-Rendering einführen.

### 🟠 'delete_msg' für Error-Szenarien missbraucht – semantisch falsch  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/PlaningController.php:193, app/Http/Controllers/PlaningController.php:222, app/Http/Controllers/ToolsController.php:110–117, app/Http/Controllers/HandoverController.php:144`  
**Problem:** 'delete_msg' wird in 51 Fällen für Fehlermeldungen verwendet (z. B. 'Ungültiger Projektstatus.', 'Ein Fehler ist aufgetreten.', 'Failed to retrieve weather data'). Views rendern delete_msg vielfach als toast('bad') oder toastr.error(), was zufällig passt, aber semantisch falsch ist und zu falschen Titeln ('Gelöscht') führt.  
**Fix:** Fehlermeldungen konsequent mit 'error' setzen. 'delete_msg' nur für erfolgreiche Löschvorgänge verwenden.

### 🟠 3 parallele Notification-Systeme: toastr.*, custom toast(), HTML-Divs  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/product/brand/brand.blade.php:1072–1080, resources/views/admin/supplier-connectors/index.blade.php:389–395, resources/views/admin/configurations/radiator/radiator_view.blade.php:127`  
**Problem:** 976 toastr.*-Aufrufe (global), 588 custom toast()-Aufrufe und 116 Blade-Dateien mit HTML-Alert-Divs (alert-success / sc-toast) existieren parallel. Jedes System hat eigene Styling- und Positionierungsregeln, was inkonsistentes UX-Feedback erzeugt.  
**Fix:** Ein einziges Notification-System wählen. Bevorzugt toastr (bereits global geladen) oder die custom toast()-Funktion, falls diese zentral definiert ist. Alle HTML-Div-Varianten und das jeweils andere JS-System migrieren.

### 🟠 toastr.danger() ist keine gültige Toastr-Methode – Notifications erscheinen nicht  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/invoice/invoice_create.blade.php:264, resources/views/admin/invoice/invoice_draft_view.blade.php:224, resources/views/admin/invoice/invoice_approved.blade.php:203, resources/views/admin/offer/set/paragraph/set_paragraph.blade.php:364, resources/views/admin/product/delivery/pdf.blade.php:289`  
**Problem:** toastr hat keine .danger()-Methode. Die gültigen Methoden sind: .success(), .error(), .info(), .warning(). An 8 Stellen (in Produktionsviews) wird toastr.danger() aufgerufen, der Aufruf schlägt lautlos fehl – die Fehlermeldung wird nie angezeigt.  
**Fix:** toastr.danger() durch toastr.error() ersetzen an allen betroffenen Stellen.

### 🟠 DE/EN-Mischung in UI-Texten: 136 Blade-Dateien mischen Sprachen im selben Kontext  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/appointments/index.blade.php, resources/views/admin/customer/customer_details.blade.php, resources/views/admin/checklist/profitablity_calculation/profit.blade.php (Beispiele)`  
**Problem:** Analyse ergibt 1180 DE-Label-Treffer ('Speichern', 'Löschen' usw.) vs. 1209 EN-Label-Treffer ('Save', 'Delete', 'Create' usw.) in 136 gemeinsamen Dateien. Flash-Meldungen sind ebenfalls gemischt: 339 DE vs. 78 EN Meldungen, teils im selben Controller (BranchRentInfoController: 'The data is deleted successfully', KnowledgeQuestionController: 'The question is saved successfully').  
**Fix:** Sprach-Policy festlegen (die App ist erkennbar Deutsch). Alle EN-UI-Texte in DE übersetzen oder Laravel-Localization (lang/de.json) konsequent einsetzen.

### 🟠 Route-URL-Konvention gemischt: 648 snake_case- vs. 712 kebab-case-URLs, teils im gleichen Pfad  
**Modul:** Querschnitt · **Severity:** hoch · ✅ bestätigt  
**Ort:** `routes/web.php (z. B. Zeile /branch_create vs. /supplier-connectors, /admin/fusion-forms/entries/{form_id} vs. /branch_expense/{branchExpense}/other-costs)`  
**Problem:** Innerhalb desselben Projekts existieren snake_case-Pfade (/branch_create, /employee_dashboard) und kebab-case-Pfade (/system-warning, /supplier-connectors) nebeneinander. Darüber hinaus gibt es 20+ URLs, die beide Stile in einem einzigen Pfadsegment mischen (z. B. /admin/fusion-forms/entries/{form_id}, /branch_expense/{branchExpense}/other-costs).  
**Fix:** Kebab-case als Standard für Routen-URLs gemäß Laravel-Konvention festlegen. Ältere snake_case-Routen schrittweise migrieren (301-Redirect bei öffentlichen URLs).

### 🟡 Inkonsistentes Antwortformat zwischen overdue-Typen: Appointments fehlen status_raw, priority_raw, changed_summary, meta  
**Modul:** Dashboard & Berichte · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Report/OverdueCenterController.php:1355-1366`  
**Problem:** overdueInquiries() und overdueTasks() liefern status_raw, priority_raw, changed_summary und meta im JSON-Objekt. overdueAppointments() (Zeile 1355) liefert nur status (bereits übersetzt), priority (Rohwert), kein status_raw, kein changed_summary, kein meta. Dadurch muss das Frontend Sonderfälle pro Typ behandeln.  
**Fix:** Alle overdue*()-Methoden auf ein einheitliches Response-Schema vereinheitlichen: type, id, title, subtitle, status_raw, status, priority_raw, priority, due_at, last_activity_at, overdue_hours, progress_pct, changed_summary, link, meta.

### 🟡 progress_pct ist in allen overdue-Typen willkürlich hartcodiert (25, 30, 35, 40)  
**Modul:** Dashboard & Berichte · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Report/OverdueCenterController.php:1251,1316,1364,1461,1568`  
**Problem:** Das Feld progress_pct enthält für Inquiries=25, Tasks=30, Appointments=40, Tickets=35, Leads=35. Diese Werte sind ohne Berechnungsgrundlage im Code fixiert und spiegeln keinen tatsächlichen Fortschritt wider. Das führt zu irreführenden Darstellungen im Frontend.  
**Fix:** Entweder progress_pct aus real vorhandenen Daten (z.B. erledigte Schritte / Gesamtschritte) berechnen oder das Feld aus der API-Antwort entfernen, wenn kein valider Wert verfügbar ist.

### 🟡 Inkonsistente Statuscodes und Response-Struktur bei mark-read  
**Modul:** CRM – Kommunikation · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Email/LeadEmailReaderController.php:385-400 / app/Http/Controllers/Email/LeadEmailAccountsController.php:44-54`  
**Problem:** LeadEmailReaderController::markAsRead() gibt {'status':'ok', 'unread_count':…} zurück; LeadEmailAccountsController::markEmailAsRead() gibt {'success':true} zurück (ohne unread_count). Beide Routen existieren parallel (lead.email.mark.read und lead-emails.mark-read) und werden teilweise im gleichen View aufgerufen. Die JS-Seite nutzt markResponse.unread_count (Zeile 1038), was bei der zweiten Route undefined ergibt.  
**Fix:** Einen einzigen mark-as-read Endpunkt mit einheitlichem Response-Format {success: bool, unread_count: int} verwenden.

### 🟡 Parallele alte (inquiry_save, inquiry_update, inquiry_delete) und neue REST-Routen fuer dieselbe Ressource  
**Modul:** CRM – Anfragen · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:3998-4010 vs. 4021-4064`  
**Problem:** Fuer dieselbe Inquiry-Ressource existieren gleichzeitig das alte URL-Schema (POST /inquiry_save, POST /inquiry_update, GET /inquiry_delete/{id}, GET /inquiry_edit/{id}) und ein moderneres Schema (POST /inquiries/start-draft, DELETE /inquiries/{inquiry}/discard, POST /inquiries/{inquiry}/finalize, POST /inquiries/bulk-delete). Beide Welten sind aktiv und referenzieren denselben Controller. Das erzeugt Verwirrung im Code, im Frontend und erschwert Sicherheits-Audits.  
**Fix:** Schrittweise Migration auf REST-konforme Routen. Alte Routen deprecated markieren (Logging), dann nach Abloesung entfernen. In der Zwischenzeit beide Routen auf dieselbe Controller-Methode zeigen lassen.

### 🟡 Routen-URI-Tippfehler: /wating_leads statt /waiting_leads  
**Modul:** CRM – Leads & Kunden · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:842`  
**Problem:** Die Route für die Warte-Leads-Liste ist als /wating_leads registriert (L842, ein 'i' fehlt), obwohl die Methode waiting_leads() heißt. Der Route-Name lautet waiting.loop.leads. In URLs, Navigations-Links und E-Mails die auf diese Seite verweisen muss der Tippfehler bewusst repliziert werden, was zu Verwirrung führt.  
**Fix:** Route auf /waiting_leads umbenennen und einen permanenten Redirect von /wating_leads auf /waiting_leads für bereits gesendete Links registrieren.

### 🟡 Statistische Kennzahlen in Blade-Views auf Seitensubset statt Gesamtdaten  
**Modul:** CRM – Partner · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/product/brand/brand.blade.php:40-42`  
**Problem:** Die Statistikkacheln 'Veröffentlicht', 'Unveröffentlicht' und 'Mit Typ' werden über $items berechnet, das nur die Einträge der aktuellen Seite enthält (max. 20). Lediglich 'Gesamt' nutzt $data->total(). Das führt dazu, dass die angezeigten Zahlen beim Blättern variieren und nie das korrekte Gesamtbild widerspiegeln. Das gleiche Problem tritt in contacts.blade.php (Zeilen 17-21) auf.  
**Fix:** Statistiken entweder über eine separate COUNT-Abfrage im Controller berechnen oder die Fehlerweisung im UI klarstellen ('Auf dieser Seite: X von Y').

### 🟡 Inkonsistente Fehlerantworten: Interne Exception-Details (Datei, Zeile) im JSON an den Client  
**Modul:** Vertrieb – Angebote · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Offer/OfferController.php:610-615 und :1491-1493`  
**Problem:** In data() Zeile 613 werden 'file' => $e->getFile() und 'line' => $e->getLine() in der JSON-Fehlerantwort an den Browser gesendet. In storeFolder() Zeile 1493 wird 'line' => $e->getLine() zurückgegeben. Diese Informationen geben Angreifern Einblick in Serverpfade und Codestruktur. Andere Fehlerantworten im selben Modul enthalten diese Felder nicht – inkonsistentes Verhalten.  
**Fix:** Exception-Details nur im Log ausgeben (report($e) ist schon vorhanden), nie im API-Response. errorResponse() anpassen, sodass 'file' und 'line' nur bei APP_DEBUG=true enthalten sind.

### 🟡 Zwei parallele, inkonsistente Rechnungssysteme: DealInvoice (legacy) und Invoice (neu)  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Deal/DealInvoiceController.php (legacy) vs. app/Http/Controllers/Invoice/InvoiceController.php (neu)`  
**Problem:** Das System betreibt zwei vollständig getrennte Rechnungs-Entitäten: deal_invoices (DealInvoice-Modell, einfache Felder, Listenansicht unter /deal/invoices) und invoices (Invoice-Modell, volle CRUD-Canvas-Logik unter admin/invoices). Beide hängen an Deals, haben aber unterschiedliche Felder (invoice_number vs. invoice_no, invoice_amount vs. total_amount, status-Werte 'open' vs. 'draft/sent/paid'). Im Auftragsprofil (profile()) werden deal_invoices geladen, das Invoice-Canvas läuft über invoices. Es gibt keine Synchronisierung zwischen beiden Systemen.  
**Fix:** Klare Entscheidung treffen: DealInvoice als Altlast migrieren auf Invoice, oder beide Systeme klar trennen und im UI deutlich kennzeichnen. Mindestens das Auftragsprofil auf das neuere Invoice-System umstellen und deal_invoices deprecieren. Fehlende Migration dokumentieren.

### 🟡 Englischer UI-Text 'Next Step' in deutschsprachiger Oberfläche  
**Modul:** Projekte & Planer · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/appointments/show.blade.php:1640`  
**Problem:** Das Label `<span class="ap-report-next-label">Next Step</span>` erscheint zweimal (Z. 1640 und 2254) in einer ansonsten deutschen UI. Benachbarter Text lautet 'Nächster Schritt' (Z. 1901) und 'Nächster Schritt kurz …' (Z. 1599). Das erzeugt einen inkonsistenten Sprachwechsel im gleichen UI-Bereich.  
**Fix:** `Next Step` durch `Nächster Schritt` ersetzen (konsistent mit Z. 1901). Alle UI-Texte auf Englischfragmente prüfen und systematisch übersetzen.

### 🟡 user.name als Mitarbeiter-ID ist ein fragiles, undokumentiertes Design-Antimuster  
**Modul:** Personal / HR · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Employee/Profile/LeaveController.php:71,990,1057`  
**Problem:** auth()->user()->name wird an drei Stellen als employees.id-Ersatz verwendet (Zeilen 71, 990, 1057). Gleiches Muster in EmployeeController (Zeile 1117: User::where('name', $id)->first()). Das 'name'-Feld der User-Tabelle enthält also eine numerische Mitarbeiter-ID — entgegen allen Laravel-Konventionen. Jede Umbenennung eines Users oder Migration auf echte Usernamen bricht die gesamte Leave-Logik und Profil-Verknüpfung.  
**Fix:** Eine eigene Spalte users.employee_id (FK auf employees.id) einführen. currentEmployeeId() auf auth()->user()->employee_id umstellen. Migration mit Datentransfer schreiben.

### 🟡 Zwei widersprüchliche Distributor-Relationen auf Product-Model  
**Modul:** Artikel · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Models/Product.php:53-86`  
**Problem:** Product::distributor() verknüpft über die Pivot-Tabelle distributor_product, Product::distributors() verknüpft über distributor_prices als Pivot, und Product::distributorLinks() ist ein dritter Alias für distributor_product. Im Controller werden distributor() und distributors() abwechselnd und teils bedingungsbasiert eingesetzt (store() Zeilen 1104-1118). Dies führt zu Inkonsistenz: Sync auf distributor_product und Sync/Read auf distributor_prices meinen unterschiedliche Datensätze.  
**Fix:** Klare Trennung: distributor() für die reine M2M-Zuordnung (distributor_product), prices() für die Preistabelle. distributors() umbenennen oder entfernen. Alle Controller-Stellen auf die eindeutige Relation normieren.

### 🟡 Inkonsistenter JSON-Response-Schlüssel in InventoryController (success vs. status)  
**Modul:** Lager · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Inventory/InventoryController.php:735, 769`  
**Problem:** store() (Zeile 769) gibt {'status': true, 'message': ...} zurück, während alle anderen Methoden des selben Controllers (storeAjax, updateAjax, destroyAjax, useProductAjax) {'success': true} verwenden. Frontend-Code, der einheitlich auf 'success' prüft, verarbeitet store()-Antworten falsch.  
**Fix:** In store() den Schlüssel 'status' durch 'success' ersetzen, um das einheitliche Muster des Controllers beizubehalten.

### 🟡 Route-Namen: 125 gemischte dot+underscore-Namen neben 19 reinen underscore-Namen  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php: 'ids.inline_back', 'ids.local_search', 'employee.dashboard.hr_widget', 'new_leads.neighbor', 'customer_search', 'branch_search'`  
**Problem:** Laravel-Konvention ist dot-Notation für Namespace-Trennung (z. B. 'admin.customer.index'). Gleichzeitig existieren 19 reine Underscore-Namen ohne Namespace ('customer_search', 'calendar_feed') und 125 gemischte Namen ('ids.inline_back' statt 'ids.inline-back' oder 'ids.back-inline').  
**Fix:** Einheitlich dot-Notation für Namespaces, kebab-case für Segmente: 'ids.inline-back', 'employee.dashboard.hr-widget' usw. Underscore in Route-Namen vermeiden.

### 🟡 Blade-View-Verzeichnisse: snake_case- und kebab-case-Ordner gemischt  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/ (breaking-news/, supplier-connectors/, system-warning/ vs. customer_economic_calculation/, daily_report/, lead_email/, weather_station/)`  
**Problem:** 15 View-Verzeichnisse nutzen snake_case, 3 nutzen kebab-case. Kein einheitliches Muster. Da Laravel-View-Pfade Punkte als Trennzeichen nutzen, macht die Verzeichnisstruktur keinen funktionalen Unterschied, aber die Inkonsistenz erschert die Navigation.  
**Fix:** snake_case als Standard für View-Verzeichnisse wählen (Mehrheit) und die 3 kebab-case-Verzeichnisse umbenennen: breaking-news → breaking_news, supplier-connectors → supplier_connectors, system-warning → system_warning.

### 🟡 JSON-Response-Keys inkonsistent: 'success', 'ok', 'status' für dieselbe Semantik  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/CostingSetController.php:43,135,224 ('ok'), app/Http/Controllers/CustomerHeatingCircuitController.php:69 ('success'), app/Http/Controllers/CustomerSuggestEmployeeController.php:80 ('status')`  
**Problem:** Für positive AJAX-Responses existieren 3 verschiedene Keys: 'success' (92 Controller), 'ok' (26 Controller), 'status' (27 Controller). Frontend-Code muss unterschiedlich auf verschiedene Endpoints reagieren. CostingSetController nutzt ausschließlich 'ok', CustomerHeatingCircuitController 'success' – beide im selben Modul-Bereich.  
**Fix:** Standard: { success: true|false, message: '...' } für alle JSON-Responses. Alle 'ok' und 'status: ok/updated/deleted' auf 'success: true' migrieren.

### 🟡 Datumsformate inkonsistent: d.m.Y (100x), Y-m-d (46x), 'd, M Y' (English) und weitere  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/todo/personal/task_details.blade.php:181 (date('d, M Y'...)), resources/views/admin/offer/view/offer_view.blade.php:356 (isoFormat('DD.MM.YY')), resources/views/admin/customer_profile.blade.php:2425 (format('Y-m-d') für Formularwert)`  
**Problem:** Datumswiedergabe nutzt: 'd.m.Y' (DE-Standard, 100x), 'Y-m-d' (ISO, 46x), 'd, M Y' (Englisch, in task_details.blade.php), 'DD.MM.YY' (Kurzjahr, isoFormat), 'd. M Y' (7x), 'd.m.Y H:i' (97x). English-Monatsnamen (Jan, Feb) tauchen in deutschen Texten auf.  
**Fix:** Anzeigeformat: 'd.m.Y' (bzw. 'd.m.Y H:i') als DE-Standard. Input-Felder: 'Y-m-d'. Englische Monatsabkürzungen durch Carbon-Locale-Aware-Format oder manuelles Mapping ersetzen. Carbon::setLocale('de') in AppServiceProvider setzen.

### 🟡 Zahlenformat: number_format() ohne DE-Dezimaltrenner an 135 Stellen neben 265 mit korrektem DE-Format  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/customer/customer_details.blade.php:262,376, resources/views/admin/customer_details.blade.php (mehrfach)`  
**Problem:** DE-Konvention: Komma als Dezimaltrenner, Punkt als Tausendertrenner (number_format($x, 2, ',', '.')). An 135 Stellen wird number_format($x, 2) ohne Trennzeichen genutzt, was englisches Format (1234.56) ausgibt. Betroffen sind Prozentwerte in customer_details, die optisch inkonsistent zu Preisangaben daneben sind.  
**Fix:** Einheitlich number_format($x, 2, ',', '.') für alle Anzeige-Zahlen in der deutschen UI. Für Prozent reicht 0 Dezimalstellen: number_format($x, 0, ',', '.').

### 🟡 Namenskonvention Model: 'UserRoll' statt 'UserRole', 'AddEmployeeToProject' (Verb-Prefix), 'SendEmail' als Model  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Models/UserRoll.php:8, app/Models/AddEmployeeToProject.php, app/Models/AddImageToSet.php, app/Models/AddProductToSet.php, app/Models/SendEmail.php`  
**Problem:** 'UserRoll' ist ein Rechtschreibfehler (englisch: Role). Das Model wird in Blade-Templates auch als Text erwähnt ('Nur Benutzer mit UserRoll Administrator'). Zusätzlich haben 4 Modelle Verb-Prefix-Namen (AddEmployeeToProject, SendEmail) – Laravel-Konvention schreibt Substantive im Singular vor (ProjectEmployee, EmailRecord o. Ä.).  
**Fix:** UserRoll → UserRole umbenennen (Migration table 'user_roles', Klasse 'UserRole'). Verb-Prefix-Modelle in Pivot- oder Noun-Form umbenennen. Da UserRoll im ganzen System referenziert wird, Migration in Etappen durchführen.

### 🟡 Typos in Controllern: 'erfulgreich'/'wurd' in Flash-Meldungen – 30+ Stellen in Produktionscode  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/BuildingTypeValueController.php:71,111,123,125, app/Http/Controllers/RentPropertyController.php:144, app/Http/Controllers/BranchContractDetailsController.php:141, app/Http/Controllers/ArticleGroup/SubArticleGroupController.php:103, app/Http/Controllers/Product/TilesController.php:60`  
**Problem:** An 30+ Stellen (inkl. aktiver Produktions-Controller, nicht nur Old/) erscheint 'erfulgreich' statt 'erfolgreich' und 'wurd' statt 'wurde'. Die Meldungen erscheinen dem Benutzer direkt als Toast-Notifications.  
**Fix:** Suchen nach 'erfulgreich' und 'wurd ' (mit Leerzeichen) und korrekte Schreibweise einsetzen: 'erfolgreich', 'wurde'.

### 🟡 Namenskonvention Controller: Kein einheitliches Prinzip für Hierarchie (Root-Level vs. Subdirectory)  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/CustomerChecklistController.php (root) vs. app/Http/Controllers/Customer/CustomerCardNoteController.php (subdirectory)`  
**Problem:** 14 Customer*-Controller liegen direkt im Root-Verzeichnis (CustomerChecklistController, CustomerHeatingCircuitController usw.), während ein Customer/-Unterverzeichnis mit 20 weiteren Customer-Controllern existiert. Analoges Muster bei Employee: EmployeeDashboardController und EmployeeDepartmentController im Root, alles andere in Employee/.  
**Fix:** Alle fachlich zugehörigen Controller in das jeweilige Unterverzeichnis verschieben. Namespaces anpassen. Routes-Import-Referenzen aktualisieren.

### 🟡 Tippfehler im Verzeichnisnamen: 'profitablity_calculation' (fehlendes 'i')  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `resources/views/admin/checklist/profitablity_calculation/ (Verzeichnis)`  
**Problem:** Das Verzeichnis heißt 'profitablity_calculation' statt 'profitability_calculation'. Da Laravel view()-Pfade direkt Verzeichnisnamen verwenden, muss der falsche Name in allen view()-Aufrufen und @include-Direktiven mitgeschleppt werden.  
**Fix:** Verzeichnis umbenennen zu 'profitability_calculation' und alle Referenzen aktualisieren.

### 🟡 Controller-Tippfehler: 'PlaningController' statt 'PlanningController'  
**Modul:** Querschnitt · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/PlaningController.php (Dateiname), resources/views/admin/planing/ (Verzeichnis)`  
**Problem:** 'Planning' mit doppeltem 'n' ist die korrekte englische Schreibweise. Sowohl der Controller als auch das View-Verzeichnis 'planing/' sind falsch geschrieben. Parallel existiert korrekt 'Planner/' als Unterverzeichnis.  
**Fix:** PlaningController → PlanningController umbenennen, Verzeichnis planing/ → planning/ umbenennen, Routen-Import anpassen.

### ⚪ Debug-Log-Statements (SQL, Bindings, Filter) in Produktionscode belassen  
**Modul:** Dashboard & Berichte · **Severity:** niedrig · · unverifiziert  
**Ort:** `app/Http/Controllers/EmployeeDashboardController.php:407,553,558`  
**Problem:** Log::info('Filters received', [...]), Log::info('Appointment SQL:', ['sql' => $query->toSql(), 'bindings' => ...]) und Log::info('requested', [$request->all()]) schreiben bei jedem Dashboard-Request vollständige SQL-Queries und Benutzerdaten in die Logdatei. Das erhöht das Log-Volumen und kann sensible Mitarbeiterdaten in Logs exponieren.  
**Fix:** Debug-Log-Statements entfernen oder mit if (config('app.debug')) absichern. In Produktion nur Errors/Warnings loggen.

### ⚪ Inkonsistente @section('style') vs @push('style') Nutzung in Views  
**Modul:** Support – Tickets · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/problem/problem_board.blade.php:18 vs. problem.blade.php:9 und profile.blade.php:5`  
**Problem:** problem_board.blade.php verwendet @once/@push('style'), alle anderen Views (problem.blade.php, profile.blade.php, problem_edit.blade.php, problem_close.blade.php, comment.blade.php) verwenden @section('style'). Bei @section wird der Inhalt nur gerendert wenn das Layout @yield('style') aufruft und nicht durch andere Sections überschrieben. @push('styles') ist die moderne, stapelbare Methode. Die Inkonsistenz kann dazu führen, dass CSS-Blöcke sich je nach Layout-Umsetzung gegenseitig überschreiben.  
**Fix:** Alle Views auf @push('styles') / @stack('styles') vereinheitlichen und im Layout entsprechend @stack('styles') platzieren. Dadurch werden alle Style-Blöcke aus allen Views korrekt gesammelt.

### ⚪ AssetInstallmentController: falsche Flash-Message-Keys und Tippfehler in Erfolgsmeldungen  
**Modul:** Finanzen · **Severity:** niedrig · · unverifiziert  
**Ort:** `app/Http/Controllers/Inventory/AssetInstallmentController.php:94,172,198`  
**Problem:** Erfolgsmeldungen werden in 'delete_msg' statt 'save_msg' gespeichert (Zeile 94, 172). In der Fehlermeldung nach einem erfolgreichen Speichern steht 'Der Datensatz wurd erfulgreich gespeichert!' (Typo, kein richtiges Deutsch). Zeile 198 (destroy) schreibt ebenfalls 'wurd erfulgreich'. Diese Keys werden von den Views als rote Toast-Meldungen angezeigt.  
**Fix:** Erfolgsmeldungen konsequent in 'save_msg' einsetzen, Fehlermeldungen in 'delete_msg'. Deutsch korrigieren: 'wurde erfolgreich'.

### ⚪ Tippfehler 'hasPremission' durchgängig im Code und Routennamen  
**Modul:** Admin & System · **Severity:** niedrig · · unverifiziert  
**Ort:** `app/Http/Controllers/User/UserController.php:65, routes/web.php:2208`  
**Problem:** Die Methode heißt hasPremission (statt hasPermission), die Route heißt user.has_permission. Im selben Codebase verwendet User::hasPermission() die korrekte Schreibweise. Inkonsistenz erschwert die Suche und ist fehleranfällig bei String-Vergleichen (z. B. Middleware-Matching).  
**Fix:** Methode und Routennamen auf hasPermission umbenennen; alle Aufrufer anpassen.

### ⚪ Duplizierte Route-Gruppen: 'dashboard.'-Namespace 5-mal, 'admin.'-Namespace 3-mal separat definiert  
**Modul:** Querschnitt · **Severity:** niedrig · · unverifiziert  
**Ort:** `routes/web.php:440,452,456,460,466,476 (dashboard.), 481,486 (admin.)`  
**Problem:** Statt einen einzigen Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group() Block zu nutzen, werden 5 separate identisch konfigurierte Gruppen geöffnet. Das erhöht den Wartungsaufwand und birgt das Risiko, bei Middleware-Änderungen nicht alle Gruppen zu aktualisieren.  
**Fix:** Alle dashboard.*-Routen in eine einzige Gruppe zusammenführen. Gleiches für admin.*-Routen.

### ⚪ toLocaleString() ohne Locale-Parameter in deutschen Views  
**Modul:** Querschnitt · **Severity:** niedrig · · unverifiziert  
**Ort:** `resources/views/admin/fusion/index.blade.php:151, resources/views/admin/offer/offer/configuration/wp/index.blade.php:1325–1344`  
**Problem:** toLocaleString() ohne Parameter ist Browser-abhängig und gibt je nach System-Locale unterschiedliche Formate aus (englisches Datum oder deutsche Zahl). In einer deutschen Anwendung sollte immer toLocaleString('de-DE') oder toLocaleString('de-DE', {options}) genutzt werden.  
**Fix:** Alle toLocaleString()-Aufrufe durch toLocaleString('de-DE') oder toLocaleString('de-DE', { minimumFractionDigits: 0 }) ersetzen.
