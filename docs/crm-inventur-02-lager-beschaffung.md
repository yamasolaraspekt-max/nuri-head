# CRM-Inventur Zone 02 — Lager & Beschaffung

**Zone:** Bestand & Einkauf (Lager, Inventur, Beschaffung, Bestellungen, Wareneingang, Großhandels-Schnittstellen)
**Nicht in dieser Zone:** Produktkatalog/Artikelstammdaten = Zone 01 · Assets/Fuhrpark/Maschinen = Zone 06 (hier nur erwähnt, wo Lager-Tabellen sie berühren)
**Methode:** NUR Lesen/Analyse. Breite vor Tiefe. Quellen: `routes/web.php`, `app/Http/Controllers/Inventory/*`, `app/Http/Controllers/Product/*` (Einkaufsseite), Migrationen, Sidebar `admin/layouts/sidebar.blade.php` (Block „Artikel & Lager").
**Glossar:** Kunde = `new_leads`. Distributor = Großhändler/Lieferant.

**Sidebar-Struktur „Artikel & Lager" > Untermenü „Lager"** (sidebar.blade.php ~Z. 1180–1227):
Inventar · Lieferscheine · Betriebsmittel (→ Zone 06) · Übergaben (→ Zone 06) · Lagerausgaben · Kaufanfragen · Maschinen & Fahrzeuge (→ Zone 06).
Zusätzlich unter „Artikel": „Lieferanten-Schnittstellen" (`admin.supplier-connectors.index`, ~Z. 1128). Distributoren-Verwaltung liegt in einem separaten Sidebar-Zweig (~Z. 641).

---

## 1. Lager / Inventar / Bestandsbewegungen

**(a) Zweck:** Physischer Lagerbestand pro Produkt (Menge, Seriennr., Standort/Regal/Reihe/Fach, geografische Koordinaten). Bestandsentnahme („Produkt verwenden") mit lückenloser Historie inkl. Kundenzuordnung. Analytics/Übersicht via AJAX-Tabellen.
**(b) Controller/Routen:** `Inventory\InventoryController` — Prefix `inventory` (web.php ~Z. 2577–2592), Routen: `index`, `analytics`, `list-ajax`, `store-ajax`, `update-ajax/{id}`, `delete-ajax/{id}`, `product-data/{id}`, `history-ajax`, `use-product-ajax/{id}` (Entnahme mit Historie), `find-by-product/{product}`. Views unter `resources/views/admin/product/inventory`.
**(c) Kern-Tabellen:** `inventories` (product_id, responsible_id, serial_no, article_no, ean, location/shelf/row, quantity, add/edit/delete-Audit; erweitert um Geo-Spalten `2026_03_26_094329` und Maße `2026_03_26_134050`). `inventory_histories` (`2026_03_26_102458`: type=used/created/updated/manual_adjustment, quantity_before/used/after, customer_id→new_leads, used_by→employees, usage_location, note). Models: `Inventory`, `InventoryHistory`.
**(d) Größe:** Controller ~773 Z. (einer der größten der Zone). Historie/Geo/Maße erst 2026 nachgerüstet.
**(e) Status:** **Aktiv.** Voll ausgebautes CRUD + Entnahme-Workflow mit Historie; jüngste Erweiterungen (Geo, Maße, Historie) 03/2026.

## 2. Lagerausgaben (Bestandsanforderung raus)

**(a) Zweck:** Anforderung/Ausgabe von Lagerartikeln durch Mitarbeiter (Anforderer → Verantwortlicher), mit Grund, Menge, Status. Interne Materialentnahme-Freigabe.
**(b) Controller/Routen:** `Inventory\InventoryRequestOutController` — Routen `request_out_create`/`_details` (name `request.out.create`/`request.out.details`), `request_out/products`, `request_out/requests`, `request_out/analytics`, `request_out_save|update|delete` (web.php ~Z. 2720–2727). Sidebar „Lagerausgaben".
**(c) Kern-Tabellen:** `inventory_request_outs` (`2023_08_23`: product_id, responsible_id, requester_id→employees, reason, quantity, status default `Unpublished`, Audit-Felder). Model: `InventoryRequestOut`.
**(d) Größe:** Controller ~351 Z.
**(e) Status:** **Aktiv** (CRUD + Analytics vorhanden). Status-Feld nutzt Alt-Konvention `Unpublished` — begrifflich zu prüfen.

## 3. Kaufanfragen / Bestellantrag (Beschaffung)

**(a) Zweck:** Beschaffungsanforderung: Mitarbeiter meldet Bedarf (Produkt, Marke, Distributor, Menge, EK/UVP/Rabatt, Link/Bild, optional Kunde/Problembezug). Vorstufe zur Bestellung; **keine** echte Bestell-/Auftrags-Pipeline zum Lieferanten erkennbar.
**(b) Controller/Routen:** `Product\PurchaseRequestController` — Routen `purchase_request` (name `purchase.request`), `purchase_request_create`, `.../list`, `.../analytics`, `purchase_request_show/{id}`, `purchase_request_save`, `purchase_request_delete/{id}` (web.php ~Z. 2731–2737). Sidebar „Kaufanfragen".
**(c) Kern-Tabellen:** `purchase_requests` (`2023_08_28`: brand→FK, distributor_id→FK, product/model/color, request_from/request_to→employees, EK/UVP/Rabatt, customer_id→new_leads, employee_id, problem_id, link/image, quantity, status default `Unpublished`, Audit). Model: `PurchaseRequest`.
**(d) Größe:** Controller ~365 Z.
**(e) Status:** **Teilweise / Antrags-Ebene.** Erfassung + Liste + Analytics aktiv, aber nur Anfrage-Verwaltung — Übergang zu echter Bestellung/Wareneingang ist nicht verdrahtet (manueller Prozess).

## 4. Wareneingang (Goods Receipt)

**(a) Zweck:** Erfassung eingehender Ware mit Prüf-/Freigabe-Workflow: Wareneingang → Prüfung (ok/issue) → Status (pending/processing/completed/issued) → Ausgabe/Ausbuchung (Lager vs. Kommission), mit Bezug zu Kunde/Objekt/Artikelgruppe/Abteilung und Beleg-Anhängen. Jüngster, sauber modellierter Baustein der Zone.
**(b) Controller/Routen:** `Inventory\DeliveryNotes\GoodsReceiptController` — Routen unter `goods-receipts` (web.php ~Z. 5119–5143): `relation-options`, `index`, `data`, `store`, `show/{gr}`, `update/{gr}`, `destroy/{gr}`, `issue/{gr}` (Ausbuchung), `quick-status/{gr}` (PATCH). Kein eigener Sidebar-Eintrag im Lager-Block gefunden — Einstieg vermutlich kontextuell (Belegbezug).
**(c) Kern-Tabellen:** `goods_receipts` (`2026_02_26_070038`: code unique, customer_id→new_leads, object_id→lead_alternative_adds, lead_product_list_id, article_group_id, department_id, diverse *_by_employee_id, received_at, status-Enum, inspection_status-Enum, destination lager/kommission, qty/unit/purchase_price, outbound_*-Felder, meta JSON, softDeletes). `goods_receipt_attachments` (`2026_02_26_093748`). Models: `GoodsReceipt`, `GoodsReceiptAttachment`.
**(d) Größe:** Controller ~781 Z. (größter der Zone).
**(e) Status:** **Aktiv, modern** (02/2026, Enums + softDeletes + Objekt-/Kundenbezug). Reifster Beschaffungs-Endpunkt.

## 5. Lieferscheine (Delivery Notes)

**(a) Zweck:** Verwaltung von Lieferscheinen (Wareneingang von Distributor „delivered_from", Bestellbezug order_no/order_by, Kommission, Handover, Fortschritt/progress, PDF/Bild-Anhänge, Verknüpfung mit Deals/Kunden und untereinander via linked_delivery_note_id). Bindeglied Lieferant ↔ Deal/Objekt.
**(b) Controller/Routen:** `Inventory\DeliveryNotes\DeliveryNoteController` (~642 Z.) + `DeliveryNoteImageController` (~142 Z.) — Prefix `admin/delivery-notes` (web.php ~Z. 2680–2705): index/list/analytics/store/show/update/destroy, `customers/search`, `deals/find`, `progress`, `pdf`, `toggle-status`, `linked`, `images.*`, `createFromDeal`, `byDeal`, `profile`. Zusätzlich `LinkedDeliveryController` (~121 Z.). Sidebar „Lieferscheine".
**(c) Kern-Tabellen:** `delivery_notes` (`2023_10_06_073316` + Fixes `2026_04_23`: delivery_note unique, delivered_from, branch_id, handover_by→employees, order_by/no, comission, dates, status default `Verfügbar`, progress, pdf/image, linked/linked_delivery_note_id, level, softDeletes). `delivery_note_images` (`2023_10_06_125535`). Models: `DeliveryNote`, `DeliveryNoteImage`.
**(d) Größe:** ~905 Z. (Controller-Trio).
**(e) Status:** **Aktiv**, breit genutzt (Deal-Integration), 04/2026 nachgebessert.

## 6. Distributoren / Einkaufspreise (Lieferantenstamm — Einkaufsseite)

**(a) Zweck:** Stammdaten der Großhändler/Lieferanten und deren produktbezogene Einkaufskonditionen (EK, UVP, Rabatt in €/%, Verfügbarkeit, Skonto/Zahlungsziele). Preisdifferenz-Vergleich zwischen Distributoren, CSV-Import von Distributor-Preislisten, Abteilungen je Distributor. *Abgrenzung: Distributor-Preise sind Einkaufsseite (Zone 02); der Produktkatalog selbst ist Zone 01.*
**(b) Controller/Routen:** `Product\Distributor\DistributorController` (~1072 Z.: index, products, productPriceDifference, importCsv, publish/unpublish, byBrand …), `DistributorPriceController` (~552 Z.: CRUD Preise), `DistributorDepartmentController` (~154 Z.), `MasterSet\MasterSetDistributorCompareController` (~154 Z.). Routen unter `distributors.*` (web.php ~Z. 2545–2574) + Produkt-Distributor-AJAX (~Z. 2302–2318, 2460–2463).
**(c) Kern-Tabellen:** `distributors` (`2023_07_26`, erweitert 2025/2026 um Excel-Spalten, Skonto/Zahlungsziele, Wartungs-Checklisten), `distributor_departments`, `distributor_product` (Pivot), `distributor_prices` (`2023_10_16`: EK `purchase_price`, UVP `price`, Rabatt €/%, availability, discount_group_id; 2026 um creator/notice ergänzt), `distributor_maintenance_checklists` (`2025_11_28`). Models: `Distributor`, `DistributorPrice`, `DistributorDepartment`, `DistributorMaintenanceChecklist`.
**(d) Größe:** ~1930 Z. (Controller-Familie) — großer Block, teils Katalog-nah.
**(e) Status:** **Aktiv**, umfangreich (CSV-Import, Preisvergleich). Enthält Legacy-Routen (`distributor_destroy/publish/unpublish` GET) — Alt-Reste.

## 7. Großhandels-Schnittstellen — generischer Konnektor (IDS / OCI / Punchout)

**(a) Zweck:** Zentrale, **generische** Anbindung an Lieferanten-Shops (Punchout-Prinzip): Warenkorb im externen Shop befüllen und Positionen per IDS/OCI-Rückgabe ins CRM importieren (→ Produkte/Distributor-Preise anlegen). Konfigurierbar über Presets, Feld-Mappings und Auth-Parametersätze pro Lieferant. Import-Logging.
**(b) Controller/Routen:**
- `Product\IDS\SupplierConnectionController` (~1106 Z., größter der Zone) — Prefix `admin/supplier-connectors` (web.php ~Z. 516–551): CRUD, `test`, `duplicate`, `apply-preset`, `open`, `search`, `forward`, `latest-logs`, `logs/{log}/preview|import`, `mappings.*`, öffentliche `handleReturn` (Callback GET/POST). **Presets** (`authPreset()`): `gc_online`, `standard_ids`, `standard_oci`, `empty_custom` — plus `connector_type`-Labels ids/oci/api/csv/xml/bmecat/datanorm.
- **Sonepar / FEGA / GC Online** sind **keine eigenen Klassen**, sondern werden generisch per Heuristik über `supplier_key`/Name/Endpoint-URL erkannt (`_form.blade.php`, `open()`/`resolveAuthParamMap()` mit Sonepar-Sonderbehandlung) und über Presets konfiguriert. Bestätigt die Prompt-Vermutung.
- `Product\IDS\gconline\IdsController` (~319 Z.) + `IdsSearchController` (~188 Z.): konkrete GC-Online-Anbindung (öffentlicher `/ids/callback`, `results/{batchId}`, `promoteToProduct`, `localSearch`, Suche/Forward). `ImportedIdsItemController` (~65 Z.).
**(c) Kern-Tabellen:** `supplier_connections` (`2026_05_26_084701`: distributor_id, connector_type default `ids`, auth_type, endpoint/test/return_url, verschlüsselte username/password/customer_number/token, `extra_auth_data`/`request_config`/`import_config` JSON, last_test_*, is_active, softDeletes), `supplier_connection_mappings` (source_field→target_table/field + transformer/required/sort), `supplier_import_logs` (`2026_05_26_084703`: status, counts, payload JSON, started/finished). `imported_ids_items` (`2025_12_04`: Rohimport IDS-Positionen, später product_id-Verknüpfung). Models: `SupplierConnection`, `SupplierConnectionMapping`, `ImportedIdsItem`.
**(d) Größe:** ~1678 Z. (Konnektor-Familie) + Views `resources/views/admin/supplier-connectors/*` (12 Blades). Größter zusammenhängender Block der Zone.
**(e) Status:** **Aktiv, jung, generisch** (Kern 05/2026). GC Online produktiv verdrahtet (dedizierter Callback); Sonepar/FEGA über Presets/Heuristik konfigurierbar. **Braucht eigene Detail-Inventur** (s. u.).

## 8. DATANORM

**(a) Zweck:** Einlesen einer DATANORM-Artikeldatei (Standard-Austauschformat des Elektro-/SHK-Großhandels). Aktuell nur Datei-Upload → Parsing der `T;A;`-Zeilen → **Anzeige** von article_no/description in einer View. **Kein DB-Import**, keine Verknüpfung zu Produkten/Preisen.
**(b) Controller/Routen:** `DatanormController` (~49 Z., Prototyp) — `datanorm-upload` (showForm), `datanorm-parse` (parseFile) (web.php ~Z. 4642–4643). View `resources/views/admin/datanorm/upload.blade.php`. **Kein Sidebar-Eintrag** gefunden. Daneben existiert `datanorm` nur als `connector_type`-Label in `supplier_connections` (ohne implementierten Import-Pfad).
**(c) Kern-Tabellen:** **Keine eigene Tabelle.** (Nur als Label-Wert in `supplier_connections.connector_type` referenziert.)
**(d) Größe:** ~49 Z.
**(e) Status:** **Prototyp / weitgehend tot.** Parsen-und-Anzeigen ohne Persistenz; nicht in Sidebar/Workflow eingebunden.

---

## Braucht eigene Detail-Inventur

1. **Großhandels-Schnittstellen (höchste Priorität)** — Der generische `SupplierConnectionController` (~1106 Z.) + IDS-gconline-Familie + Presets/Mappings/Auth-Parametersätze + Import-Logs bilden den mit Abstand komplexesten, jüngsten und geschäftskritischsten Block. Zu klären in Detail-Inventur:
   - Welche Lieferanten sind real konfiguriert (`supplier_connections`-Daten) und welche Presets/Heuristiken (Sonepar, FEGA, GC Online, Buderus) sind produktiv vs. nur vorbereitet?
   - Mapping-Engine: Zieltabellen `products`/`brands`/`distributor_prices` — Überschneidung/Konflikt mit Zone 01 (Katalog) genau prüfen (Import schreibt in Katalog!).
   - Sicherheit: verschlüsselte Credentials, öffentliche Callback-/Return-Routen (`handleReturn`, `/ids/callback`) ohne Auth — Auth/CSRF/Missbrauchsschutz bewerten.
   - OCI/BMEcat/API/CSV/XML als `connector_type` gelistet — welche davon tatsächlich implementiert (import_config-Pfad) vs. nur Label?
2. **DATANORM** — Entscheidung nötig: ausbauen (echter Import → supplier_connections/Produkte) oder als Prototyp entfernen. Aktuell toter Ballast.
3. **Kaufanfrage → Bestellung → Wareneingang** — Prozesskette ist nicht durchverdrahtet (drei separate Inseln: `purchase_requests`, keine echte „Bestellung"-Tabelle, `goods_receipts`, `delivery_notes`). Fließt in Architektur-Gate (Kernprozess-Weichen). Eigene Prozess-Inventur sinnvoll: Wo endet Antrag, wo beginnt Wareneingang, gibt es eine fehlende „Bestellung"-Entität?
4. **Distributor-Familie (~1930 Z.)** — Grenze Einkaufspreise (Zone 02) vs. Produktkatalog (Zone 01) ist unscharf (distributor_prices hängt an products); Legacy-GET-Routen. Gemeinsame Detail-Inventur mit Zone 01 empfohlen.

## Belege

- Routen: `routes/web.php` — IDS/Supplier-Connectors ~Z. 496–551; Distributoren ~Z. 2302–2318, 2460–2463, 2545–2574; Inventar ~Z. 2577–2592; Lagerausgaben ~Z. 2720–2727; Kaufanfragen ~Z. 2731–2737; Lieferscheine ~Z. 2680–2705; DATANORM ~Z. 4642–4643; Wareneingang ~Z. 5119–5143. (`routes/api.php` enthält keine Zone-02-Routen.)
- Controller: `app/Http/Controllers/Inventory/InventoryController.php` (773), `InventoryRequestOutController.php` (351), `DeliveryNotes/GoodsReceiptController.php` (781), `DeliveryNotes/DeliveryNoteController.php` (642), `DeliveryNotes/DeliveryNoteImageController.php` (142); `app/Http/Controllers/Product/PurchaseRequestController.php` (365), `Product/Distributor/DistributorController.php` (1072), `DistributorPriceController.php` (552), `DistributorDepartmentController.php` (154); `Product/IDS/SupplierConnectionController.php` (1106), `Product/IDS/gconline/IdsController.php` (319), `IdsSearchController.php` (188); `app/Http/Controllers/DatanormController.php` (49), `ImportedIdsItemController.php` (65), `LinkedDeliveryController.php` (121).
- Migrationen: `2023_08_14_..._create_inventories_table`, `2026_03_26_102458_create_inventory_histories_table` (+ Geo/Maße-Ergänzungen 2026_03_26), `2023_08_23_..._inventory_request_outs`, `2023_08_28_..._purchase_requests`, `2026_02_26_070038_goods_receipts` (+ `..._093748_goods_receipt_attachments`), `2023_10_06_073316_delivery_notes` (+ Fixes 2026_04_23), `..._125535_delivery_note_images`, `2023_07_26_..._distributors` / `_distributor_departments`, `2023_10_16_..._distributor_product` / `_distributor_prices`, `2025_11_28_..._distributor_maintenance_checklists`, `2026_05_26_084701..084703_supplier_connections` / `_mappings` / `_import_logs`, `2025_12_04_191014_imported_ids_items`.
- Models: `Inventory`, `InventoryHistory`, `InventoryRequestOut`, `PurchaseRequest`, `GoodsReceipt`, `GoodsReceiptAttachment`, `DeliveryNote`, `DeliveryNoteImage`, `Distributor`, `DistributorPrice`, `DistributorDepartment`, `DistributorMaintenanceChecklist`, `SupplierConnection`, `SupplierConnectionMapping`, `ImportedIdsItem`.
- Sidebar: `resources/views/admin/layouts/sidebar.blade.php` — Gruppe „Artikel & Lager" (~Z. 1074), Untermenü „Lager" (~Z. 1180–1227), „Lieferanten-Schnittstellen" (~Z. 1128).
- Presets/Heuristik-Beleg: `SupplierConnectionController::authPreset()` (Z. 1029–1060), `createDefaultMappingsIfMissing()` (Z. 1062–1092); `resources/views/admin/supplier-connectors/_form.blade.php` (Sonepar/GC/FEGA-Erkennung Z. 18–43), `create.blade.php` (GC-Online-Beispiel), `index.blade.php` (Typ-Labels Z. 407/463).
