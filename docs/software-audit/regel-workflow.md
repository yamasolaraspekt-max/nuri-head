# Audit – Workflow-Effizienz

Funde: 21  ·  🔴 1 kritisch · 🟠 7 hoch · 🟡 13 mittel · ⚪ 0 niedrig

### 🔴 O(N) updateOrCreate + O(N) Kind-Query pro Entity in syncAndLoad  
**Modul:** Planner · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Planner/PlannerPlanController.php:861-1111`  
**Problem:** syncAndLoad() ruft syncAppointments(), syncTickets(), syncPersonalTasks(), syncPhaseActivities() und syncMasterSets() in einer einzigen Transaktion auf. Jede Methode iteriert über ihre Entitätenliste und führt pro Zeile ein PlannerItem::updateOrCreate() aus. Zusätzlich wird in syncAppointments (Zeile 893-897) pro Termin eine extra DB::table('main_appointment_employees')->where('appointment_id', $id)->pluck() ausgeführt, in syncPersonalTasks (Zeile 1086-1102) pro Aufgabe zwei weitere Abfragen (personal_task_keys, employees_personal_tasks). Damit entstehen pro Planner-Öffnung 3×N+2×N DB-Operationen für N Projektentitäten.  
**Fix:** Alle IDs zuerst sammeln, Pivot-Daten mit whereIn in einer Abfrage laden (keyBy('id')), dann mit upsert()/insertOrIgnore() als Batch schreiben. syncPlannerItemEmployees() (Zeile 286-294) sollte statt N Einzel-INSERTs ein einziges DB::table()->insert($rows) mit vorbereiteter Array-Liste verwenden.

### 🟠 recentReportsEmployeeSummary führt 5 separate DB-Queries pro Mitarbeiter aus (N+1)  
**Modul:** Dashboard & Berichte · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Report/OverdueCenterController.php:114-136`  
**Problem:** Für jeden aktiven Mitarbeiter werden 5 count-Queries aufgerufen (remainingTicketCount, remainingTaskCount, remainingLeadCount, remainingInquiryCount, remainingAppointmentCount). Bei 50 Mitarbeitern = 250 DB-Queries pro Request. Kein Caching, keine Batch-Aggregation.  
**Fix:** Alle 5 Counts per Mitarbeiter in einer einzigen SQL-Abfrage mit bedingten SUMs/COUNT+GROUP BY employee_id aggregieren. Alternativ Ergebnis in Cache (z.B. 5 Minuten) ablegen.

### 🟠 data()-Endpunkt lädt ALLE Angebote ohne Pagination und triggert syncOfferLeadProducts() bei jedem Aufruf  
**Modul:** Vertrieb – Angebote · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/Offer/OfferController.php:574-618`  
**Problem:** GET /admin/offers/data ruft zuerst syncOfferLeadProducts() auf (vollständiger Tabellenscan auf lead_product_lists, schreibt dabei neue Angebote in einer Loop), dann lädt Offer::query()->with([...8 Relations...])->latest()->get() alle Angebote ohne Limit. Anschließend wird für jedes Angebot resolveOfferIndexDetailPayload() aufgerufen (mehrere Queries je Angebot = N+1). Bei 100 Angeboten entstehen 300+ Queries pro Seitenaufruf.  
**Fix:** syncOfferLeadProducts() in einen Queue-Job auslagern (stündlich per Scheduler). data()-Endpunkt auf paginate(50) umstellen. resolveOfferIndexDetailPayload() durch eine einzige Batch-Query (whereIn + groupBy) ersetzen.

### 🟠 N+1-Queries in Blade-View: bis zu 7 DB-Abfragen pro Listeneintrag  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** hoch · ✅ bestätigt  
**Ort:** `resources/views/admin/deal/customer_view.blade.php:2417–2478`  
**Problem:** Im @forelse($data as $item)-Loop der Listenansicht werden pro Zeile mindestens 5–7 unkontrollierte DB::table()-Abfragen ausgeführt: employees für checked_by, employees für reviewer_id, offers per offer_id, offers per customer/product/alternative als Fallback, offer_details, user_rolls für canUpdateCustomer, user_rolls für canDeleteCustomer. Bei 19 Einträgen (paginate(19)) entstehen bis zu 133 zusätzliche Queries pro Seitenaufruf.  
**Fix:** Permission-Checks (user_rolls) einmalig vor dem Loop auflösen und als Variablen übergeben. employees für checked_by/reviewer_id per whereIn-Query vorab laden und als Map bereitstellen. Offer/OfferDetail-Lookups in den Controller verlagern und per WITH oder JOIN in die baseDealQuery() integrieren.

### 🟠 GeneralTask-Index lädt alle Aufgaben inkl. 10 Eager-Relations ohne Pagination  
**Modul:** Projekte & Planer · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Task/GeneralTaskController.php:79`  
**Problem:** `$tasks = $this->applyPriorityOrdering($query)->get()` (Z. 79) lädt alle sichtbaren Aufgaben eines Mitarbeiters in einem Request. Das `taskRelations()`-Array hat 10 Relationen (`department`, `claimedBy`, `assignees`, `reports.employee`, `steps.assignees`, `steps.checkedBy`, `dependsOn.assignees`, `blockingTasks.assignees`, `dependencyParents.assignees`, `dependencyChildren.assignees`). Bei wachsender Datenmenge (hunderte Aufgaben) führt das zu massiven Speicher- und Query-Problemen. Zusätzlich werden alle archivierten Aufgaben separat mit `->get()` geladen (Z. 91).  
**Fix:** Pagination einführen (`->paginate(50)`). Für Board-/Kanban-Ansichten separate AJAX-Endpoints mit Lazy-Loading der Relationen anbieten. Schwergewichtige Relationen (`reports.employee`, `dependsOn.assignees`) nur auf Detailseiten laden.

### 🟠 Bis zu 4.100 Zeilen pro Request in PHP-Memory geladen und in-memory sortiert (OverdueCenter)  
**Modul:** Reporting · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Report/OverdueCenterController.php:37-41,872-899`  
**Problem:** fetch() lädt pro Aufruf bis zu LIMIT_INQUIRY=800 + LIMIT_TASK=800 + LIMIT_APPOINTMENT=800 + LIMIT_TICKET=800 + LIMIT_LEAD=900 = 4.100 Zeilen in PHP-Collections, führt einen in-memory Sort (sortBy/sortByDesc) durch und schneidet erst dann mit slice() auf die angefragte Seite. Jede erneute Filterung oder Sortierung des Nutzers löst denselben vollen Datenbankdurchlauf aus.  
**Fix:** Entweder serverseitige Sortierung und DB-Pagination pro Typ einführen und die Ergebnisse mit UNION ALL oder einem separaten Tabellen-Ansatz zusammenfassen, oder den Hard-Limit stark reduzieren (z. B. 100 pro Typ) und für weitere Seiten AJAX-Nachladen anbieten.

### 🟠 Alle Messungen ohne Pagination in PHP geladen und als JSON in die Blade serialisiert  
**Modul:** DealMeasurement · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/Deal/DealMeasurementController.php:34-93, resources/views/admin/deal_measurements/index.blade.php:2870-3353`  
**Problem:** index() lädt alle DealMeasurement-Datensätze mit 9 eager-geladenen Beziehungen (deal, customer, alternative, pvWpDetail, roofs, product, items, offer, detail, editDetail, histories) ohne LIMIT oder paginate(). Die Blade-View serialisiert das vollständige Ergebnis als JSON (Zeile 3353: let records = @json($measurementRecords)) und macht die Pagination client-seitig per JavaScript. Bei wachsender Datenmenge steigt der Arbeitsspeicher- und Netzwerkverbrauch pro Seitenaufruf linear.  
**Fix:** Server-seitige Pagination mit paginate(20) einführen. JS kann die URL per fetch() laden. Alternativ einen AJAX-Endpunkt bauen der nur die aktuelle Seite liefert.

### 🟠 Employee::all(), Product::all(), ArticleGroup::all() ohne Einschränkung auf jeder Formularseite  
**Modul:** NewLeads · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:90-92,191-193,223-225`  
**Problem:** Die Methoden new_object(), create() und weitere laden Employee::all(), Product::all() und ArticleGroup::all() ohne WHERE-Einschränkung oder Spalten-Selektion. Bei wachsendem Datensatz werden alle Zeilen aller drei Tabellen in jedes Formular geladen. Product- und ArticleGroup-Tabellen können bei einem PV/Heizung-Fachbetrieb tausende Zeilen haben.  
**Fix:** Selektive Abfragen mit select('id','name',...) und Where-Status-Filter verwenden. Dropdown-Daten für häufig genutzte Stammdaten (Employee, ArticleGroup) mit Cache::remember (5-60 Min.) cachen.

### 🟡 markAllReportNotificationsRead führt N einzelne Upserts statt Bulk-Insert aus  
**Modul:** Dashboard & Berichte · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Report/OverdueCenterController.php:615-624`  
**Problem:** Die Schleife foreach ($unreadReportIds as $reportId) { OverdueReportRead::updateOrCreate(...) } führt pro Report-Eintrag einen separaten DB-Write aus. Bei vielen ungelesenen Reports (potenziell Hunderte) entsteht eine massive Anzahl von Einzeloperationen ohne Transaktion.  
**Fix:** Alle zu erstellenden overdue_report_reads-Einträge per DB::table('overdue_report_reads')->upsert([...], ...) in einem einzigen Statement anlegen. Transaktionsschutz hinzufügen.

### 🟡 fetch() lädt bis zu 4100 Datenbankzeilen in den PHP-Speicher, paginiert dann in-memory  
**Modul:** Dashboard & Berichte · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Report/OverdueCenterController.php:37-41,898-899`  
**Problem:** Die Konstanten LIMIT_INQUIRY=800, LIMIT_TASK=800, LIMIT_APPOINTMENT=800, LIMIT_TICKET=800, LIMIT_LEAD=900 ergeben maximal 4100 Datensätze, die alle in PHP geholt werden. Danach erfolgt Sortierung und Slice in PHP-Collections. Bei wachsenden Datenmengen führt das zu Speicher- und Laufzeitproblemen.  
**Fix:** Pagination auf DB-Ebene verlagern (OFFSET/LIMIT in SQL), Sortierung per ORDER BY im Query, Gesamtanzahl per separatem COUNT-Query. Alternativ Cursor-Pagination verwenden.

### 🟡 getExistingLeadMatchesForInquiries() kann bei 19 Anfragen/Seite bis zu 38+ Queries ausloesen  
**Modul:** CRM – Anfragen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Inquiry/InquiryController.php:764-854`  
**Problem:** sharedListData() ruft getExistingLeadMatchesForInquiries($idsOnPage) auf (max. 19 IDs per Page). Darin laeuft fuer jede Inquiry ein foreach (Zeile 798) mit jeweils zwei separaten Datenbankqueries: findMatchingLeadsForInquiry() und findMatchingDuplicateInquiries(). Bei 19 Anfragen pro Seite sind das mindestens 38 zusaetzliche Queries – plus 2 Queries fuer Vorbereitungen = 40+ Queries nur fuer Duplikaterkennungen auf jeder Listenseite.  
**Fix:** Doppelerkennungs-Queries buendeln: alle E-Mails/Phones aller Inquiries der Seite in einen einzigen IN-Query zusammenfassen. Alternativ die Ergebnisse 5 Minuten cachen (Cache::remember()) oder die Duplikaterkennung in einen Hintergrundjob auslagern.

### 🟡 Unbeschränktes ->get() aller lead_product_lists auf der Index-Seite  
**Modul:** CRM – Leads & Kunden · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/NewLeadsController.php:3608,3627,3647,3665,3668`  
**Problem:** Die index()-Methode führt nach der paginierten Lead-Abfrage (L3550) noch 5 weitere unbeschränkte ->get()-Abfragen aus: productcount (L3619) lädt ALLE lead_product_lists, customer_product_lists (L3644) lädt ALLE lead_product_lists mit Joins, current_request (L3655) lädt nochmals ALLE lead_product_lists, alternatives (L3665) lädt ALLE lead_alternative_adds, employees (L3668) lädt ALLE Mitarbeiter. Mit wachsenden Daten (Tausende Leads, Zehntausende Produkt-Einträge) werden diese Abfragen zunehmend langsamer und speicherintensiv. Ähnlich in waiting_leads() (L3966–4004) und setCommonData() (L3285–3322).  
**Fix:** customer_product_lists/productcount mit den bereits paginierten Lead-IDs einschränken (whereIn customer_id). Status-Counts als Aggregat-Query (COUNT + GROUP BY status) statt Collection-Filter berechnen. alternatives auf den aktuellen Seiten-Bereich beschränken oder lazy-loadbar machen.

### 🟡 Feinaufmaß-Index und Kanban laden alle Datensätze ohne Pagination  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Deal/DealMeasurementController.php:93 (index) und 1307 (kanban)`  
**Problem:** Beide Endpunkte rufen ->get() ohne Limit oder paginate() auf und laden dazu pro Eintrag eager relations (deal, customer, alternative, pvWpDetail, roofs, product, items, offer, detail, editDetail, histories). Mit wachsender Datenmenge führt das zu Memory-Problemen und langen Ladezeiten. Das Kanban lädt zusätzlich per foreach für jeden Eintrag findMeasurementAppointment() und findActiveMeasurementPersonalTask() (weitere Queries).  
**Fix:** Pagination für die Listenansicht einführen (->paginate(25)). Für die Kanban-Ansicht Lazy Loading oder AJAX per Spalte implementieren (analog zu deal.kanban.column). findMeasurementAppointment/PersonalTask per eager load oder JOIN statt foreach auflösen.

### 🟡 statsFromQuery() lädt alle gefilterten Deals in PHP-Memory statt SQL-Aggregation zu nutzen  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Deal/DealController.php:753–783`  
**Problem:** Die Statistiken (totalCount, openCount, confirmCount, priceTotal usw.) werden berechnet, indem alle passenden Deals per ->get() vollständig in den PHP-Speicher geladen und dann mit Collection-Methoden (->where, ->count, ->sum) ausgewertet werden. Bei großen Datensätzen (> 1000 Deals) ist das speicher- und zeitintensiv; ein einfaches SELECT COUNT / SUM GROUP BY wäre drastisch effizienter.  
**Fix:** Die Statistiken per DB::table('deals')->select(DB::raw('status, COUNT(*) as cnt, SUM(price) as total'))->groupBy('status')->get() berechnen und die Ergebnisse im PHP-Code zusammenfassen. So wird eine einzige aggregierende SQL-Query statt n vollständiger Objekte ausgeführt.

### 🟡 sidebarData() lädt alle Kunden (NewLeads) ohne Limit bei jedem Listenaufruf  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Deal/DealController.php:483–487`  
**Problem:** Bei jedem Aufruf von index/all/junk_list/delete_list wird sidebarData() aufgerufen, das mit NewLeads::query()->...->get() alle Kunden ohne Limit lädt. In produktiven Umgebungen mit Tausenden Kunden wird die gesamte Tabelle in den PHP-Speicher geladen und als Dropdown an die View übergeben.  
**Fix:** Sidebar-Dropdowns auf Select2-AJAX-Endpoints umstellen (wie in InvoiceController bereits umgesetzt mit selectCustomers/selectObjects). Alternativ Limit + Suche einführen oder die Kundenliste gecacht ausliefern.

### 🟡 allImages() lädt alle ProductImages ohne Limit in JSON-Response  
**Modul:** Artikel · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Product/ProductController.php:1794-1811`  
**Problem:** ProductImage::with('product')->get() lädt alle Bilder ohne Pagination oder Limit. Bei einer großen Produktdatenbank (1000+ Bilder) führt das zu hohem Speicher- und Netzwerkverbrauch im Browser.  
**Fix:** Pagination einführen (->paginate(50)) oder zumindest ein ->limit(200) und einen Suchparameter ergänzen.

### 🟡 N+1: resource() führt 3+ Queries pro Datensatz in der paginierten Liste aus  
**Modul:** Finanzen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/BranchExpenseController.php:241-244`  
**Problem:** data() lädt die paginierten BranchExpense-Datensätze mit withCount(['rents','insurances','otherCosts']), ruft dann aber per map() für jedes Item resource() auf, das rents()->sum(), insurances()->sum() und otherCosts()->sum() sowie linkedCosts() ausführt. Bei 12 Einträgen pro Seite entstehen mindestens 36 zusätzliche Queries (3 SUM-Queries × 12 Items), plus Schema::hasTable-Checks pro Item.  
**Fix:** Die drei Summen per DB-Aggregat vorberechnen (z.B. mit selectRaw-Subqueries oder einer wiederverwendbaren Query-Scope-Methode) und in der Hauptquery für alle Datensätze auf einmal laden.

### 🟡 dueAlerts() wird pro Profil-Aufruf dreifach ausgeführt (9 redundante Queries)  
**Modul:** Finanzen · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/BranchExpenseController.php:300-301,58`  
**Problem:** Beim Aufruf von profile() werden profileKpis() und dueAlerts() separat aufgerufen. profileKpis() ruft seinerseits dueAlerts() zweimal auf (Zeile 300 und 301) – einmal für due_soon, einmal für overdue. Jeder dueAlerts()-Aufruf startet 3 Datenbankabfragen. Das ergibt 9 Queries nur für Fälligkeitszähler, obwohl 3 ausreichen würden.  
**Fix:** dueAlerts() einmal aufrufen und Ergebnis cachen: $alerts = $this->dueAlerts($expense); dann darüber filtern. In profileKpis() das Ergebnis als Parameter übergeben statt neu zu laden.

### 🟡 UserRollController::ajaxIndex() führt pro angezeigte Seite N+1 Queries durch matrixForUser()  
**Modul:** Admin & System · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/User/UserRollController.php:114, 304-322`  
**Problem:** Für jeden Benutzer auf der paginierten Seite (default 10) wird matrixForUser() aufgerufen, das eine separate DB-Abfrage (UserRoll::query()->where('user_id', $userId)->get()) auslöst. Bei 10 Benutzern pro Seite entstehen 10 zusätzliche Queries + 8 Analytics-Queries = mindestens 20+ Queries pro Request. Die Analytics-Funktion klonat denselben Base-Query 8-mal.  
**Fix:** Alle UserRolls für die aktuell paginierten User-IDs in einer Query laden (wie es der rolls-Block auf Zeile 81-86 macht) und die Matrix client-seitig oder aus diesem gemeinsamen Datensatz ableiten. Analytics in einem einzigen GROUP-BY-Query zusammenfassen.

### 🟡 N einzelne ::create-Aufrufe in den Duplikations-Methoden des MasterSetControllers (kein Batch-Insert)  
**Modul:** MasterSet · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Product/MasterSet/MasterSetController.php:2008-2082,1376-1410,1910-1988`  
**Problem:** duplicateLaborToTarget(), duplicateTasksToTarget(), duplicateTasksToTarget()/task.labor und duplicateComponentsToTarget() iterieren jeweils über die Quell-Collection und erzeugen pro Zeile ein einzelnes Eloquent::create(). Bei einem MasterSet mit 50 Labor-Einträgen, 30 Tasks und 5 Checklisten entstehen mindestens 85 INSERTs. Transaktions-Schutz ist vorhanden (Zeile 1779), die Einzelausführung bleibt trotzdem unnötig langsam.  
**Fix:** Rows in ein Array sammeln und mit Model::insert($rows) oder DB::table()->insert($rows) als Batch ausführen. Für timestamps beforehand now() setzen.

### 🟡 CustomerPhaseListController lädt Product::all() innerhalb einer Request-Verarbeitung  
**Modul:** Planner · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/CustomerPhaseListController.php:53`  
**Problem:** In einem Request der CustomerPhaseList wird Product::all() ohne Spaltenauswahl geladen (Zeile 53). Da dieser Controller je nach Produkt-Varianten auch mehrfach für denselben Kunden aufgerufen werden kann, wird die vollständige Produkttabelle ohne Filter in den Speicher geladen.  
**Fix:** Product::select('id','name','article_group')->orderBy('article_group')->get() verwenden. Mit Cache::remember für kurze TTL wenn die Produktliste sich selten ändert.
