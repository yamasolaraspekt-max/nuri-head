# Audit – CRUD

Funde: 10  ·  🔴 1 kritisch · 🟠 3 hoch · 🟡 6 mittel · ⚪ 0 niedrig

### 🔴 Fehlende Autorisierung: Jeder angemeldete User kann jede GeneralTask bearbeiten/löschen/umsortieren  
**Modul:** Projekte & Planer · **Severity:** kritisch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Task/GeneralTaskController.php:161`  
**Problem:** Die Methoden `update()` (Z. 161), `destroy()` (Z. 358), `archive()` (Z. 330), `reorder()` (Z. 397) und `move()` (Z. 244) prüfen ausschließlich `auth`-Middleware, aber keinerlei Ownership oder Rollenberechtigung. Im `reorder()`-Closure fehlt `$employeeId` sogar im `use`-Block (Z. 419), obwohl er kurz davor abgerufen wird (Z. 415). Jeder eingeloggte Mitarbeiter kann beliebige Aufgaben anderer ändern, löschen oder in einen anderen Status verschieben.  
**Fix:** Für jede Mutationsmethode prüfen, ob `auth()->user()->name == $generalTask->created_by` oder ob der User ein Assignee/Admin ist. Idealerweise eine `GeneralTaskPolicy` anlegen und `$this->authorize('update', $generalTask)` einsetzen.

### 🟠 update() und destroy() pruefen keine Berechtigungen – jeder authentifizierte Nutzer kann fremde Anfragen aendern oder loeschen  
**Modul:** CRM – Anfragen · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Inquiry/InquiryController.php:1335,1515`  
**Problem:** Die Methoden update(), destroy() und junk() enthalten keinerlei Eigentuemerschafts- oder Rollenprufung. Die Berechtigungen (canUpdate, canDelete) werden in getInquiryPermissions() berechnet und an die View gegeben (Buttons ausblenden), aber im Controller selbst nie geprueft. Die Permissions werden also nur client-seitig erzwungen. Ein normaler Nutzer kann via direktem POST auf /inquiry_update beliebige Anfragen aendern.  
**Fix:** Vor jeder zustandsaendernden Operation die Permission erzwingen: $perms = $this->getInquiryPermissions(); abort_unless($perms['canUpdate'], 403); Alternativ FormRequest- oder Policy-Klassen verwenden.

### 🟠 DealInvoice update() und destroy() sind leere Stubs – kein Update/Delete möglich  
**Modul:** Vertrieb – Aufträge & Rechnungen · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Customer/Deal/DealInvoiceController.php:202–213`  
**Problem:** Die Methoden update() und destroy() enthalten nur einen Kommentar (//). Außerdem fehlen edit() und show() vollständig. Dadurch gibt es für die deal_invoices-Entität kein Bearbeiten und kein Löschen. Mehrfach erstellte oder fehlerhafte Rechnungseinträge können nur direkt per Datenbank korrigiert werden.  
**Fix:** update(), destroy(), show(), edit() implementieren analog zur store()-Methode. Validator mit denselben Regeln wie store() verwenden, für destroy() SoftDelete nutzen (das Modell hat bereits SoftDeletes). Routen für PUT/DELETE in der Routendatei ergänzen.

### 🟠 saveDistributorData() fügt Preiszeilen nur ein, ohne bestehende zu löschen → Duplikate  
**Modul:** Artikel · **Severity:** hoch · ✅ bestätigt  
**Ort:** `app/Http/Controllers/Product/ProductController.php:1723-1727`  
**Problem:** Im Unterschied zur Methode distributorStore() (Zeile 1333: DistributorPrice::where(product_id)->delete() vor Insert) führt saveDistributorData() kein Delete durch, sondern fügt alle empfangenen price[]-Zeilen direkt ein. Mehrfaches Abspeichern derselben Produktpreise erzeugt Duplikat-Einträge in distributor_prices.  
**Fix:** Vor dem foreach DistributorPrice::where('product_id', $product->id)->delete() einfügen, oder auf updateOrCreate umstellen (wie in store() Stage 2).

### 🟡 Fehlende Delete-Funktion für empfangene Lead-E-Mails  
**Modul:** CRM – Kommunikation · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Email/LeadEmailReaderController.php:1-414`  
**Problem:** Das LeadEmail-Model hat kein Soft-Delete und der LeadEmailReaderController implementiert kein destroy(). Es gibt keine Route und keine UI-Schaltfläche zum Löschen einzelner E-Mails. Die Inbox wächst unbegrenzt, es gibt keinen Workflow zum Archivieren oder Löschen verarbeiteter E-Mails.  
**Fix:** SoftDeletes zum LeadEmail-Model hinzufügen, eine DELETE-Route und Controller-Methode implementieren, einen 'Löschen'- oder 'Archivieren'-Button in der Inbox-View ergänzen.

### 🟡 ExternalDepartmentsController.update ohne jegliche Validierung – Mass Assignment  
**Modul:** CRM – Partner · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Product/Brand/ExternalDepartmentsController.php:74-77`  
**Problem:** Die update()-Methode ruft ExternalDepartments::find($id)->update($request->all()) direkt ohne vorherige Validierung auf. Damit können beliebige Spalten überschrieben werden, die zufällig in $fillable stehen (external_id, department, email, phone, status, name, position, home, office). Insbesondere kann external_id manipuliert werden, um Abteilungen einer anderen Firma zuzuordnen.  
**Fix:** Vor dem Update eine $request->validate([...]) mit den erlaubten Feldern durchführen (analog zu BrandDepartmentController) und anstatt $request->all() nur die validierten Felder übergeben.

### 🟡 OfferCommentController: fehlende Ownership-Prüfung bei Update und Delete  
**Modul:** Vertrieb – Angebote · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Customer/Offer/OfferCommentController.php:44-56`  
**Problem:** update($id) und destroy($id) prüfen nur ob der Kommentar existiert (findOrFail), nicht ob der aktuell authentifizierte Benutzer der Ersteller ist. Jeder eingeloggte Mitarbeiter kann beliebige Kommentare anderer Mitarbeiter überschreiben oder löschen.  
**Fix:** Auth-Check ergänzen: $comment = OfferComment::findOrFail($id); if($comment->employee_id !== auth()->user()->name) abort(403); Oder eine Policy einführen.

### 🟡 Fehlende Autorisierungsprüfung bei TicketReport Update/Delete  
**Modul:** Support – Tickets · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Http/Controllers/Ticket/TicketReportController.php:57-90`  
**Problem:** Die Methoden update() und destroy() für TicketReport prüfen nicht, ob der eingeloggte Benutzer der Ersteller des Berichts ist (kein $report->employee_id == auth Prüfung). Im Gegensatz dazu prüft ProblemCommentController@update korrekt (Zeile 66: 'Unauthorized' wenn employee_id != auth). Jeder authentifizierte Mitarbeiter kann somit Berichte anderer Kollegen überschreiben oder löschen.  
**Fix:** In TicketReportController@update und @destroy prüfen: if ($report->employee_id !== (int) auth()->user()->name) { return response()->json(['success' => false, 'message' => 'Unauthorized'], 403); }. Langfristig eine Policy (TicketReportPolicy) erstellen.

### 🟡 Product-Model ohne SoftDeletes trotz vieler Abhängigkeiten  
**Modul:** Artikel · **Severity:** mittel · · unverifiziert  
**Ort:** `app/Models/Product.php:1-200`  
**Problem:** Products sind mit LeadProductLists, MasterSetComponents, DistributorPrices, ProductImages, ProductDocuments, Inventories verknüpft. Ein hartes Delete (destroy()) löscht den Produkt-Datensatz, alle Cascade-FKs des Schemas reißen verknüpfte Daten mit. Historische Angebote/Aufträge referenzieren dann ungültige Produkt-IDs.  
**Fix:** SoftDeletes-Trait auf Product-Model aktivieren, Migration um deleted_at-Spalte ergänzen, Cascade-FKs auf RESTRICT umstellen und Restore-Funktion in der UI anbieten.

### 🟡 PurchaseRequest ohne Update-Route – CRUD unvollständig  
**Modul:** Lager · **Severity:** mittel · · unverifiziert  
**Ort:** `routes/web.php:2716-2725`  
**Problem:** Für PurchaseRequest existieren Routen für index, list, analytics, show, store und destroy – aber kein Update-Endpunkt. Einmal gespeicherte Bestellanfragen können über die Applikation nicht editiert werden. Der User muss löschen und neu anlegen.  
**Fix:** Route::put('/purchase_request_update/{id}', [PurchaseRequestController::class, 'update']) hinzufügen und eine entsprechende update()-Methode im Controller implementieren.
