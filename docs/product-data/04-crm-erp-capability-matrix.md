# 04 · CRM-/ERP-Abdeckungsmatrix

<!-- Erzeugt in der Analysephase (Phase A). Rein lesende Untersuchung. -->

> **Rolle:** Planner · **Modus:** restriktiv lesend · **Stand:** 2026-07-30
> **Heimat-App:** `ticket` (Laravel 11.44, PHP 8.2, MySQL, DB `ticket`)
> **Auftrag:** Masterprompt „Produktdatenplattform, IDS Connect, Open Masterdata" — Phase A
>
> **Grenzen dieser Untersuchung (belegt):** Es wurde ausschliesslich gelesen. Keine Migration, kein
> Schreibvorgang, kein Datenbankzugriff. In der Analyse-Umgebung stehen weder `php` noch ein
> `mysql`-Client zur Verfuegung (`command -v php` und `command -v mysql` → nicht gefunden), und die
> MySQL-Instanz auf `127.0.0.1:3307` liegt auf dem Rechner des Auftraggebers und ist von hier aus
> nicht erreichbar. **Alle Aussagen stammen daher aus Migrationen, Models, Controllern, Services,
> Routen, Konfiguration und Repository-Dokumentation — nicht aus dem laufenden Datenbestand.**
> Zeilenzahlen aus dem Datenbestand sind gesondert in `03-data-quality-report.md` behandelt.
>
> **Legende der Aussagearten** — durchgaengig getrennt:
> · **BELEGT** — nachweisbar, mit Fundstelle (Datei:Zeile)
> · **BEWERTUNG** — fachliche Einschaetzung des Planners
> · **ANNAHME** — ausdruecklich als solche gekennzeichnet, nicht belegt
> · **OFFEN** — nicht geklaert; bewusst offen gelassen statt geraten
>
> **Pfad-Praefix aller Fundstellen:** Repository-Wurzel von `ticket`.

---

**Pfad-Präfix für alle Fundstellen:** `$T = /sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket`
(Alle Angaben `$T/…:Zeile` sind absolut aufzulösen.)

Methodik: Tabellenliste vollständig aus allen `Schema::create(...)` der 612 Migrationen extrahiert (485 Tabellen); Schreibpfade über `::create(` / `::updateOrCreate(` / `::firstOrCreate(` / `->save()` / `DB::table()->insert` in `app/` verifiziert. Kein DB-Zugriff, kein Schreibvorgang.

---

## 1. CRM

| Objekt | Status | Tabelle(n) | Model | Controller / Fundstelle (Schreibpfad) | Bemerkung zum Reifegrad |
|---|---|---|---|---|---|
| **Kunde** | vorhanden | `new_leads` (führend); `customers` (Alt, tot) | `NewLeads`; `Customer` (ohne Schreibpfad) | `$T/app/Http/Controllers/Customer/NewLeadsController.php:642` `NewLeads::create([…])`; auch `…/Inquiry/InquiryController.php:2706`, `…/LeadImportController.php:296` | Kunde und Lead sind **dieselbe Zeile** — Unterscheidung nur über `status`/`purchase_status`/`purchase_date`. `Customer::create` kommt im gesamten `app/` **nicht** vor → `customers` ist ein toter Strang, an dem aber noch `checklist_apartments` per FK hängt. |
| **Interessent / Lead** | vorhanden (aber nicht getrennt) | `new_leads`, `inquiries`, `leads` (tot), `lead_stages`, `lead_stage_sub_stages` | `NewLeads`, `Inquiry`, `Leads` (ohne Schreibpfad) | `$T/app/Http/Controllers/Inquiry/InquiryController.php:2706` (Anfrage → Lead); `$T/app/Http/Controllers/Appointment/MainAppointmentController.php:1343` `Inquiry::create` | Vorstufe (`inquiries`) und Lead (`new_leads`) sind sauber getrennt. Der Schritt Lead→Kunde ist **kein Objektwechsel**, sondern Statuswechsel. `leads`-Tabelle + `Leads`-Model: 0 Schreibpfade. |
| **Objekt / Gebäude** | vorhanden | `lead_alternative_adds` (+ `lead_alternative_pv_wp_details`, `p_v_roofs`, `building_data`) | `LeadAlternativeAdd` | `$T/app/Http/Controllers/Customer/NewLeadsController.php:681` und `:1206` `LeadAlternativeAdd::create([…])` | Der zentrale Anker des Systems: `alternative_id` ist FK in `deals`, `projects`, `offers`, `invoices`, `lead_product_lists`, `goods_receipts`, `delivery_notes`. Tabelle ist mit ~140 Spalten (Gebäude + Dach + Heizung + Verbrauch + Finanzierung, Migration `2023_06_13_100802`) massiv unternormalisiert. |
| **Wohneinheit** | teilweise | `checklist_apartments`, `checklist_rooms`, `lead_object_rooms`, Zähler `lead_alternative_adds.number_we` | `ChecklistApartment`, `ChecklistRoom`, `LeadObjectRoom` | `$T/app/Http/Controllers/ChecklistApartmentController.php:60` `ChecklistApartment::create([…])` | Keine eigenständige WE im Kern. `checklist_apartments.customer_id` zeigt per FK auf die **stillgelegte** `customers`-Tabelle (Migration `2024_10_16_100344:26`). `$T/app/Http/Controllers/LeadObjectRoomController.php:29` — `store()` ist ein **leerer Stub** → `lead_object_rooms` hat keinen Schreibpfad. |
| **Projekt** | teilweise | `projects`, `project_tasks`, `project_timelines`, `planner_plans`/`planner_items` | `Project`, `PlannerPlan`, `PlannerItem` | `$T/app/Http/Controllers/PlaningController.php:186-207` `$data = new Project; … $data->save();` — einziger Schreibpfad | `Project::create()` existiert im gesamten `app/` **nicht**. Der operativ genutzte „Projekt"-Begriff ist faktisch `deals` + `planner_plans/planner_items`; `projects` wird überwiegend nur gelesen (`$T/app/Http/Controllers/Report/DailyReportController.php:1950`). |
| **Verkaufschance** | vorhanden | `lead_product_lists` (Kunde × Objekt × Artikelgruppe), `lead_stages`, `lead_stage_sub_stages`, `kanban_lead_tasks` | `LeadProductList`, `LeadStage` | `$T/app/Http/Controllers/Customer/NewLeadsController.php:881`, `:1394`, `:1639` (`updateOrCreate`), `:9443` | Reifste CRM-Fläche: Stufen, Sub-Stufen, `stage_history` (JSON), `price` + `price_history` (JSON), Kanban, Reminder, Aktivitätslog. **Keine Gewichtung/Abschlusswahrscheinlichkeit, kein Forecast-Feld.** |
| **Beratung** | teilweise | keine eigene Tabelle; `main_appointments`, `appointment_reports`, `offer_kanban_stages` | `MainAppointment`, `AppointmentReport` | `$T/app/Http/Controllers/Appointment/MainAppointmentController.php:2761` `MainAppointment::create([…])` | Beratung existiert nur als **Termin + Stufenname**: `$T/database/migrations/2026_06_01_104043_create_offer_kanban_stages_table.php:34-35` seedet `beratung_geplant` / `beratung_durchgefuehrt`. Kein Beratungsobjekt mit Ergebnis, Teilnehmern, Bedarfsprotokoll. |
| **Bedarfsermittlung** | teilweise | `anforderungsprofile` + `anforderungsprofil_werte` (EAV, versioniert), `heizlast_projekte`/`_raeume`/`_bauteile`, `p_v_checklists`, `w_p_checklists`, `heatpump_checklists` | `Anforderungsprofil`, `AnforderungsprofilWert`, `HeizlastProjekt` | `$T/app/Services/Anforderungsprofil/AnforderungsprofilService.php:34,53,99,139` `->save()`; `$T/app/Http/Controllers/Energie/GrundrissController.php:349,369` `HeizlastProjekt::create` / `HeizlastRaum::create` | Technisch sehr sauber gebaut (Datenlage-Stufe je Wert, Versionierung — `$T/database/migrations/2026_07_05_170006`). **Aber**: das ist die *Auslegungs*-Bedarfsermittlung (Energie), nicht die kaufmännische. Der Vertriebs-Bedarf liegt als Rohfelder in `lead_alternative_adds` + Checklisten. Zwei parallele Wahrheiten. |
| **Produktempfehlung** | teilweise | keine Tabelle | keins | `$T/app/Services/Offer/AuslegungVorschlagService.php` (nur `DB::table(...)->first()`, Zeilen 253/257 — **kein Schreiben**) | Ausdrücklich read-only: `$T/app/Http/Controllers/Customer/Offer/AuslegungVorschlagController.php:10-12` „KEIN Schreiben, KEINE Angebotsübernahme, KEINE Persistenz, KEIN Preis". Ebenso `$T/app/Http/Controllers/Customer/Offer/WpKatalogMatchingController.php:9-12`. Empfehlung ist eine Anzeige, kein Objekt. |
| **Angebot** | vorhanden | `offers` (Kopf, `offer_no`), `offer_folders` (Varianten), `offer_details` (Inhalt + Summen), `offer_templates`, `offer_pdf_prints`, `offer_folder_activities` | `Offer`, `OfferFolder`, `OfferDetail` | `$T/app/Http/Controllers/Customer/Offer/OfferController.php:373` `Offer::create`, `:411` `OfferFolder::create`; `$T/app/Http/Controllers/Customer/Offer/OfferFolderController.php:2264-2272` (`persistDetailSections` → `$detail->sections = …; $detail->save();`) | Dreistufig: Kopf → Ordner (= Variante) → Detail. Nummernkreis im Model-Hook (`$T/app/Models/Offer.php:33-35`). Positionen liegen als **JSON** in `offer_details.sections` (s. Frage 4). |
| **Auftrag** | vorhanden | `deals` (+ `deal_notes`, `deal_measurements`), `order_confirmations` (AB-Snapshot) | `Deal`; für AB **kein Model** (nur `DB::table`) | `$T/app/Http/Controllers/Customer/Deal/DealController.php:3686` `Deal::create([…])`; `$T/app/Http/Controllers/Customer/Offer/OfferFolderController.php:3157/3166` `DB::table('deals')->update/insert`; AB: `$T/app/Http/Controllers/Customer/Deal/AuftragseingangController.php:84` `insertGetId` | Auftragsnummer per `$T/app/Models/Deal.php:52-77` (`SA-AB26001`). **Der Auftrag hat keine eigenen Positionen** — er trägt nur `price` (Kopfsumme) und zeigt auf das Angebot zurück. AB friert Positionen als JSON ein (`order_confirmations.positions`). |
| **Historie** | vorhanden (fragmentiert) | `lead_activity_logs`, `customer_histories`, `invoice_histories`, `product_histories`, `deal_measurement_histories`, `inventory_histories`, `personal_task_histories`, `offer_folder_activities`, `system_warning_histories` + JSON: `offer_folders.history`, `offer_details.biography_data`, `lead_product_lists.stage_history` | `CustomerHistory`, `InvoiceHistory`, `ProductHistory`, `LeadActivityLogs` | `$T/app/Traits/AuditableLead.php:92` `DB::table('lead_activity_logs')->insert`; `$T/app/Listeners/StoreLeadActivity.php:20`; `$T/app/Http/Controllers/Customer/CustomerHistoryController.php:110/316` (`firstOrNew` + save) | Mindestens **9 Tabellen + 3 JSON-Blobs** ohne gemeinsames Historienmodell. `LeadActivityLogs::create` wird nie verwendet — geschrieben wird durchgängig per `DB::table()->insert` am Model vorbei. |
| **Kommunikation** | teilweise | eingehend: `lead_emails`, `lead_email_accounts`, `email_open_events`; intern: `chats`, `chat_groups`, `chat_mentions`, `messages`, `ai_chats`; ausgehend: `send_emails` (**tot**) | `LeadEmail`, `Chat`, `ChatGroup`; `SendEmail` (ohne Schreibpfad) | eingehend: `$T/app/Http/Controllers/Email/LeadEmailReaderController.php:174` + `$T/app/Console/Commands/SyncLeadEmails.php:82`; intern: `$T/app/Http/Controllers/Chat/ChatController.php:961/1463` | **Ausgehende Kundenkommunikation fehlt praktisch vollständig.** Der einzige `Mail::to(...)->send(...)` im gesamten `app/` ist `$T/app/Http/Controllers/VideoCallController.php:126` (Videocall-Einladung). `SendEmail::create` = 0 Treffer, `email_open_events` = 0 Code-Treffer. Angebot/Mahnung per Mail existiert nicht. |

---

## 2. ERP / Beschaffung

| Objekt | Status | Tabelle(n) | Model | Controller / Fundstelle (Schreibpfad) | Bemerkung zum Reifegrad |
|---|---|---|---|---|---|
| **Lieferant** | vorhanden | `distributors` (+ `distributor_departments`, `distributor_maintenance_checklist`) | `Distributor` | `$T/app/Http/Controllers/Product/IDS/gconline/IdsController.php:100` `Distributor::firstOrCreate`; Excel-Import `$T/app/Http/Controllers/Product/Distributor/DistributorController.php:394-395` | Stammdaten breit (Migration `2025_08_29_114130`: Kurzname, Konto, Eigentümer …) + Skonto/Zahlungsziel (`2026_03_12_103309`). Kein Kreditorenkonto-Bezug zur FiBu. |
| **Hersteller** | vorhanden | `brands` (+ `brand_departments`) | `Brand` | `$T/app/Services/Suppliers/SupplierProductImportService.php:200` `Brand::firstOrCreate`; `$T/app/Http/Controllers/Inquiry/InquiryController.php:2601` | Reines Namens-/Logo-Stammdatum. Kein Hersteller-Konditionsbezug. |
| **Artikel** | vorhanden | `products` (+ `article_groups`, `sub_article_groups`, `measures`, `accessories`, `product_pv_module_specs`, `product_heat_pump_specs`, `product_radiator_specs`, `product_histories`) | `Product`, `ArticleGroup`, `Accessory` | `$T/app/Http/Controllers/Product/ProductController.php:1317` `Product::updateOrCreate`; `$T/app/Services/Suppliers/SupplierConnectorService.php:699` `Product::create` | Sehr reif: Spec-Tabellen, Import-Batches, Verifikations-Felder, Änderungshistorie. Schwäche: Preise redundant in `products.retail_price/purchase_price` **und** `distributor_prices` **und** `master_set_components`. |
| **Lieferantenartikel** | vorhanden | `distributor_prices`, `distributor_product`, `supplier_article_map` (herstellerneutraler Kanal-Index ids/omd/datanorm), `imported_ids_items` | `DistributorPrice`, `SupplierArticleMap` | `$T/app/Http/Controllers/Product/IDS/gconline/IdsController.php:106` `DistributorPrice::updateOrCreate`; `$T/app/Services/Suppliers/Mappers/IdsMapper.php:43` `SupplierArticleMap::updateOrCreate` | Gut gebaut. Fehlend: Mengenstaffel, Gültig-von/bis, Mindestabnahme. |
| **Bestellanforderung** | teilweise | `purchase_requests`, `inventory_request_outs`, `planner_item_material_requests` | `PurchaseRequest`, `InventoryRequestOut` | `$T/app/Http/Controllers/Product/PurchaseRequestController.php:260`; `$T/app/Http/Controllers/Inventory/InventoryRequestOutController.php:254`; `$T/app/Http/Controllers/Planner/PlannerItemMaterialController.php:19` (`DB::table('planner_item_material_requests')`) | **Drei parallele Anforderungs-Schienen** (Betriebsmittel / Lagerentnahme / Baustellenmaterial), keine davon geht in eine Bestellung über. |
| **Bestellung** | **fehlt** | keine (`purchase_orders`/`orders`/`bestellungen` existieren nicht — vollständige Tabellenliste geprüft) | keins | Ersatz: `deal_measurement_items.order_status` (`$T/database/migrations/2026_04_29_114107:54`) + JSON-Blob; Pflege: `$T/app/Http/Controllers/Customer/Offer/DealMaterialListController.php:1754-1866` (`updateOrderDetails`) | Bestellung existiert nur als **Etikett an der Materialzeile**: `order_status ∈ open/ordered/delivered/cancelled` (`:1809-1815`). `order_no`, `ordered_at`, `expected_delivery_at`, `delivery_target`, `distributor_id` liegen im JSON-Feld `order_details`/`purchase_order` (`:1818-1856`) — **kein Bestellkopf, kein Nummernkreis, keine Bestellposition, kein Lieferanten-FK, keine Bestellung als Beleg.** |
| **Wareneingang** | vorhanden | `goods_receipts` (+ `goods_receipt_attachments`), `delivery_notes` (+ `delivery_note_images`, `linked_deliveries`) | `GoodsReceipt`, `DeliveryNote` | `$T/app/Http/Controllers/Inventory/DeliveryNotes/GoodsReceiptController.php:227` `GoodsReceipt::create($payload)`; `$T/app/Http/Controllers/Inventory/DeliveryNotes/DeliveryNoteController.php:602` | Fachlich stark (Prüfstatus, Ziel `lager`/`kommission`, Ausgang mit Empfänger). **Kein Bestellbezug** — FK gehen auf `lead_product_list_id`, `article_group_id`, `deal_id` (`2026_04_23_132825:34`), nie auf eine Bestellung. |
| **Eingangsrechnung** | teilweise | `accounting_documents` (`document_type='eingangsrechnung'`), `accounting_journal_entries`/`_lines` | keins (nur `DB::table`) | `$T/app/Services/Accounting/EingangsBelegflussService.php:68` `insertGetId` — **aber 0 Aufrufer** (grep über `app/Http`, `routes/`, `app/Console`) | Der Docblock benennt die Lücke selbst: `$T/app/Services/Accounting/EingangsBelegflussService.php:14-16` „Es gibt in ticket noch KEINE Eingangsrechnungs-Quelltabelle". Buchungs-Primitiv vorhanden, Fachobjekt und Erfassungsfläche fehlen. |
| **Gutschrift** | teilweise | keine eigene — Wert von `invoices.type` | `Invoice` | Schreibpfad = regulärer Rechnungspfad `$T/app/Http/Controllers/Invoice/InvoiceController.php:213-245`; Typen `$T/app/Http/Controllers/Invoice/InvoiceController.php:1543-1544` (`'Gutschrift'`, `'Stornorechnung'`) | Register ist read-only: `$T/app/Http/Controllers/Invoice/GutschriftenController.php:11-17`. Dort steht auch der Kernmangel wörtlich: „das Schema kennt KEINE Verknüpfung zur Ursprungsrechnung" → **kein Storno-/Gutschriftbezug**, keine Verrechnung (`$T/app/Http/Controllers/Controlling/UmsaetzeController.php:15-17`). |
| **Lagerbestand** | vorhanden | `inventories`, `inventory_histories` | `Inventory`, `InventoryHistory` | `$T/app/Http/Controllers/Inventory/InventoryController.php:748` `Inventory::create`; `:614` `InventoryHistory::create` | Bestand = `quantity` je Inventarzeile mit `location/shelf/row` (Migration `2023_08_14_055930:22-25`). Kein Mehr-Lager-Buchungsmodell, keine Chargen/Seriennummern-Bestandsführung (Seriennummer nur als Feld). |
| **Reservierung** | **fehlt** | keine | keins | Nächstliegend: JSON `deal_measurement_items.stock_allocation` (`2026_04_29_114107:55`), normalisiert in `$T/app/Http/Controllers/Customer/Offer/OfferFolderController.php:2965-2969` (Schlüssel `lager`/`bestellen`/`offen`/`final`) | Das ist eine **Planungsaufteilung im Aufmaß-JSON**, sie verändert `inventories.quantity` nicht und sperrt nichts. Keine Reservierungstabelle, kein verfügbarer-Bestand-Begriff. |
| **Rückstand** | **fehlt** | keine | keins | 0 Treffer für `rückstand`/`rueckstand`/`backorder` in `database/migrations`, `app/Models`, `app/Http/Controllers`. Nächstliegend: JSON-Key `missing_qty` (`$T/app/Http/Controllers/Customer/Offer/DealMaterialListController.php:1849`) | Nicht persistiert als Spalte, nicht auswertbar, keine Rückstandsliste. |
| **Liefertermin** | teilweise | `delivery_notes.order_date`/`handover_date`, `goods_receipts.received_at`/`outbound_at`, `planner_item_material_requests.needed_at`, `checklists.direct_delivery_date` | `DeliveryNote`, `GoodsReceipt` | `$T/app/Http/Controllers/Inventory/DeliveryNotes/DeliveryNoteController.php:602` | Der **bestellbezogene** Liefertermin existiert nur als JSON-Key `expected_delivery_at` (`$T/app/Http/Controllers/Customer/Offer/DealMaterialListController.php:1771`, gespeichert unter `order_details`) — keine Spalte ⇒ nicht filterbar, nicht überwachbar, keine Terminverfolgung. |
| **Einkaufsbedingungen** | teilweise | `distributors.cash_discount` + `payment_terms` (`2026_03_12_103309`); `master_set_components.skonto`/`payment_terms` (`2026_03_02_093129:20-21`) | `Distributor`, `MasterSetComponent` | `$T/app/Http/Controllers/Product/Distributor/DistributorController.php:394-395` (Excel-Import) | **Zwei Skalarfelder je Lieferant** — keine Konditionsstruktur: keine Gültigkeit, keine Staffel, keine Bedingungsarten, kein Vertragsbezug. `payment_terms` ist ein `string(255)` (Freitext). |
| **Preislisten** | teilweise | `distributor_prices`; Import: `supplier_connections`, `supplier_connection_mappings`, `supplier_import_logs`, `spec_import_batches` | `DistributorPrice`, `SupplierConnection` | `$T/app/Http/Controllers/Product/ProductImportController.php:189` `DistributorPrice::updateOrCreate`; `$T/app/Http/Controllers/Product/IDS/SupplierConnectionController.php:111` | **Eine Zeile je Lieferant × Produkt**, überschreibend (`updateOrCreate`). Nur `price_date` als Zeitmarker — kein Versionsstand, kein Gültig-von/bis, keine Staffel ⇒ **historische Preise sind nicht rekonstruierbar**, alte Angebote nicht nachrechenbar. |
| **Rabatte** | vorhanden (fragmentiert) | `discount_groups`; `distributor_prices.discount_percent`/`discount_price`; `offer_product_lists.discount_pct`/`discount_abs`/`global_discount_pct` | `DiscountGroup` | `$T/app/Http/Controllers/Product/DiscountGroupController.php:56` `DiscountGroup::create($validated)`; `$T/app/Http/Controllers/Customer/Offer/OfferDetailsController.php:162` (`discount_pct`) | Drei unabhängige Rabattmodelle. **Kritisch**: die führende Positionsschiene `offer_details.sections` kennt keinen Rabatt-Key — die Preisrechnung `$T/app/Http/Controllers/Customer/Offer/OfferController.php:1917-1918` (`offerLineTotals`) zieht **keinen Rabatt ab**. |
| **Skonto** | teilweise | `distributors.cash_discount`; `master_set_components.skonto`; JSON-Key `skonto` in Angebotspositionen | `Distributor`, `MasterSetComponent` | `$T/app/Http/Controllers/Customer/Offer/OfferSupplierSearchController.php:541`; `$T/app/Http/Controllers/Customer/Offer/OfferWizardController.php:1014-1016` (Fallback Komponente → Lieferant) | Wird sauber **durchgereicht**, aber **nirgends gerechnet**: kein Skontoabzug in `invoices`, kein Skontofrist-Feld, keine Zahlungslogik. |
| **Zahlungsziele** | teilweise | Einkauf: `distributors.payment_terms` (String), `master_set_components.payment_terms` (Tage, Default 14). Verkauf: `invoices.due_date` | `Invoice`, `Distributor` | Verkauf zentral im Model-Hook: dokumentiert in `$T/app/Http/Controllers/Invoice/InvoiceCanvasController.php:70-72` („leitet der Invoice-Model-saving-Hook zentral ab (issue_date + `Invoice::ZAHLUNGSZIEL_TAGE`)"); Validierung `$T/app/Http/Controllers/Invoice/InvoiceController.php:1097` | Verkaufsseite: eine Wahrheit, sauber. Einkaufsseite: Freitextfeld ohne Semantik. |
| **Boni** | **fehlt** | keine | keins | 0 Treffer. `salaries.bonus` = Mitarbeiterprämie, `beg_fundings` = staatliche Förderung — beides fachfremd | Keine Lieferantenbonus-/Jahresbonus-Struktur, keine Bonusstaffel, keine Bonusabgrenzung. |
| **Frachtkosten** | teilweise | `costing_sets.freight_fixed` / `handling_fixed` / `disposal_fixed`; `offer_product_lists.shipping_net_eur` + `apply_global_to_shipping` | `CostingSet`, `OfferProductList` | `$T/app/Http/Controllers/CostingSetController.php:71-73` (Validierung) → `:106` `CostingSet::create` | Parameter existieren, greifen aber nicht in die führende Positionsschiene: `offer_details.sections` hat keinen Fracht-Key; Fracht kann nur als eigene Position mit `kind='logistics'/'shipping'` erfasst werden (`$T/app/Http/Controllers/Customer/Offer/OfferController.php:1930-1931`). |
| **Mindestbestellwerte** | **fehlt** | keine | keins | 0 Treffer für `mindestbestell`/`min_order` in Migrationen/Models/Controllern | Weder je Lieferant noch je Artikel. |
| **Rahmenverträge** | **fehlt** (Einkauf) | vorhandene Vertragstabellen sind fachfremd: `maintenance_contracts`/`customer_maintenance_contracts` (Kunden-Wartung), `contract_types` (Arbeitsvertragsarten), `branch_contract_details` (Standort-/Mietverträge) | `MaintenanceContract`, `ContractType` | `$T/database/migrations/2025_02_20_111046_create_maintenance_contracts_table.php:16-19` (`customer_id`, `contact_person`, `type`) | Kein Lieferanten-Rahmenvertrag, keine Abrufmechanik, keine Kontraktmengen. |

---

## 3. Verkauf / Kalkulation

| Objekt | Status | Tabelle(n) | Model | Controller / Fundstelle (Schreibpfad) | Bemerkung zum Reifegrad |
|---|---|---|---|---|---|
| **Verkaufspreis** | vorhanden (redundant) | `products.retail_price` (`2025_11_10_101908`), `distributor_prices.price`, `master_set_components.unit_price`, `supplier_article_map.vk_preis`, `offer_product_lists.unit_price`, JSON-Key `price` | `Product`, `DistributorPrice`, `MasterSetComponent` | `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:1263 ff.`; `$T/app/Http/Controllers/Customer/Offer/OfferDetailsController.php:152` | **Fünf Ablageorte** für „Verkaufspreis" ohne definierte Hierarchie. Serverseitige Absicherung nur für `component_id`-Anker (`CatalogPriceGuard`, s. Frage 2). |
| **Einkaufspreis** | vorhanden (redundant) | `distributor_prices.purchase_price`, `products.purchase_price`, `master_set_components.purchase_price`, `supplier_article_map.ek_preis`, `offer_product_lists.cost`, JSON-Keys `ek`/`purchase_price` | dieselben | `$T/app/Http/Controllers/Product/ProductImportController.php:189`; `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:1263` | Wie oben. Der EK **wandert als Snapshot** in `master_set_components` (Migrationskommentar `2026_01_05_103845:24`: „snapshot price at time of saving") und von dort in das Angebots-JSON. |
| **Kalkulationsbasis** | vorhanden (nicht durchgezogen) | `costing_sets` + `costing_set_roles`; `master_sets.costing_set_id`/`costing_rate_mode`/`costing_fallback` | `CostingSet`, `CostingSetRole` | `$T/app/Http/Controllers/CostingSetController.php:106` `CostingSet::create([…])` | Modell vollständig: AW-Minuten, Material-/Lohn-/Baustellen-GK, Wagnis, Gewinn, Kleinteile, Fracht, Rundung (`rounding_step`/`rounding_mode`), Provision inkl. `db_percent` (`$T/database/migrations/2026_03_05_112752`). **Aber**: die tatsächliche Set-Kalkulation rechnet mit `master_sets.global_gemeinkosten`/`global_wagnis`, **nicht** mit dem CostingSet — `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:226-227` und `:454-460`. Zwei Kalkulationsquellen nebeneinander. |
| **Aufschlag** | vorhanden (transient) | `costing_sets.default_sell_markup_percent`, `offer_product_lists.global_markup_pct`, `master_sets.global_gemeinkosten` | `CostingSet`, `MasterSet` | Rechnung: `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:454-458`; `$T/app/Http/Controllers/Customer/Offer/OfferController.php:2029` / `:2044` | Wird berechnet und ausgegeben, **nicht gespeichert** (kein `markup`-Feld auf `offer_details`). |
| **Marge** | vorhanden | Spalte `master_set_components.margin` (`2026_03_02_093129:17`, Default 50), `master_set_cart_items.margin` | `MasterSetComponent`, `MasterSetCartItem` | `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:1263` `'margin' => (float) ($data['margin'] ?? 50)`; `$T/app/Http/Controllers/Product/MasterSet/MasterSetCartController.php:541` | Marge **je Komponente** ist persistiert. Marge **je Angebot/Auftrag** wird nur zur Laufzeit gerechnet (`$T/app/Http/Controllers/Customer/Offer/OfferController.php:2030`, `:2045`) und nicht abgelegt. |
| **Deckungsbeitrag** | teilweise | **keine Spalte** | — | Rechnung: `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:457-462` (`$dbPerPiece`, `$dbLine`, `mainDb`/`subDb`); `$T/app/Http/Controllers/Customer/Offer/OfferController.php:2028` (`db = vk − ek`) und `:2041` (`total_db`) | Wird gerechnet, **nie persistiert**: `offer_details` speichert ausschließlich `total_net`/`tax_rate`/`total_gross` (`$T/database/migrations/2026_03_09_123239:33-35`). Auf Rechnungsebene existiert **kein EK** (`invoice_items` hat nur `unit_price`) ⇒ **kein DB auf der Umsatz-Wahrheit**. DB ist eine Bildschirmanzeige, keine auswertbare Kennzahl. |
| **Kundenspezifischer Preis** | **fehlt** | keine | keins | 0 Treffer für `customer_price`/`kundenpreis`/`special_price`/`customer_specific` in Migrationen, Models, Controllern | Der einzige kundenbezogene Preis ist die frei überschriebene Zahl im Angebots-JSON. Diese wird sogar **zurückgesetzt**, sobald ein Katalog-Anker existiert: `$T/app/Services/Offer/CatalogPriceGuard.php:9-13` („erzwingt … Katalog-EK und -VK; ein abweichender Browser-Payload-Preis wird verworfen"). |
| **Projektbezogener Preis** | **fehlt** | keine | keins | Nächstliegend: `lead_product_lists.price` + `price_history` (`$T/database/migrations/2024_07_19_144003_lead_product_lists.php:33-36`) | Das ist **ein Preis je Verkaufschance** (Kopfsumme), kein Preis je Artikel im Projekt. Keine projektbezogene Preisliste, kein Preisvertrag. |
| **Angebotsposition** | vorhanden — **ohne eigene Tabelle** | führend: `offer_details.sections` (JSON); daneben `offer_product_lists` (echte Zeilen), `offer_employee_lists`, `offer_asset_lists` | `OfferDetail`; `OfferProductList` | JSON: `$T/app/Http/Controllers/Customer/Offer/OfferFolderController.php:2249-2276` (`persistDetailSections`) und `$T/app/Http/Controllers/Customer/Offer/OfferController.php:2318/2337`. Tabelle: `$T/app/Http/Controllers/Customer/Offer/OfferDetailsController.php:158-165` (`$folder->offerProductLists()->create($payload)`) | **Zwei Positionsschienen parallel.** Führend für Auftrag/AB/Rechnung/Aufmaß ist ausschließlich das JSON (s. Fragen 1, 2, 4). `offer_product_lists` wird über Route `offer.details.update` (`$T/routes/web.php:3600`) beschrieben, ist aber **Quelle für nichts** nachgelagertes. |
| **Stückliste** | vorhanden | `master_sets`, `master_set_components` (`parent_id`-Hierarchie), `master_set_labor`, `master_set_tasks`, `master_set_task_labors`, `master_set_checklists`, `master_set_component_descriptions` | `MasterSet`, `MasterSetComponent`, `MasterSetLabor` | `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:578` `MasterSet::create`, `:1340` `MasterSetComponent::create($mainData)`, `:1365` (Sub-Komponente) | **Eigene Tabellen, zweistufige Hierarchie** — sauber. Bruch entsteht erst bei Übernahme ins Angebot (s. Frage 4). Legacy-Doppelung: `product_master_sets`, `product_sub_sets`, `add_product_to_sets`, `group_sets` (Schreibpfade in `$T/app/Http/Controllers/Old/`). |
| **Set** | vorhanden | wie Stückliste + `master_set_groups`, `master_set_group_master_set`, `master_set_carts`/`_sections`/`_items` | `MasterSetGroup`, `MasterSetCart` | `$T/app/Http/Controllers/Product/MasterSet/MasterSetCartController.php:811` `MasterSet::create` | Der Begriff „Set" ist im System **mehrfach belegt**: Artikel-Set, `employee_sets`, `checklist_sets`, `asset_sets`, `costing_sets`. |
| **Alternativposition** | **fehlt** (auf Positionsebene) | Alternativen nur auf Dokumentebene: `offer_folders` | `OfferFolder` | Varianten: `$T/app/Http/Controllers/Customer/Offer/OfferController.php:411` `OfferFolder::create`; Auto-Storno der Geschwister beim Auftrag: `$T/app/Http/Controllers/Customer/Offer/OfferFolderController.php:3204-3238` | Auf Positionsebene gibt es nur `active` / `status='inactive'` (`$T/app/Http/Controllers/Invoice/InvoiceController.php:964`, `:970`) und `print_hidden` (`:989`, `:1003`) — das bedeutet „zählt nicht mit" bzw. „wird nicht gedruckt". **Kein Bezug „Alternative zu Position X"**, keine Alternativgruppe, keine Auswahlsemantik. |
| **Eventualposition** | **fehlt** | keine | keins | Die Summenbildung `$T/app/Http/Controllers/Customer/Offer/OfferController.php:1881-1883` (`offerNodeIsActive`) kennt ausschließlich aktiv/inaktiv | Keine Kennzeichnung „Eventual"/„Bedarfsposition", keine getrennte Eventualsumme, keine Aktivierung bei Auftragserteilung. |
| **Nachtragsposition** | **fehlt** | keine | keins | Nächstliegend: `deal_measurement_items.qty_offer` / `qty_measurement` / `qty_final` (`$T/database/migrations/2026_04_29_114107:43-45`), geschrieben in `$T/app/Http/Controllers/Customer/Deal/DealMeasurementController.php:239/464` | Die **Mengenabweichung Angebot vs. Aufmaß** ist die faktische Nachtragsmechanik — aber ohne Nachtragsobjekt, ohne Nachtragsnummer, ohne Freigabe, ohne eigenen Preis und ohne Weg in die Rechnung als kenntlicher Nachtrag. |

---

## Gesonderte Fragen

### Frage 1 — Wie hängen Angebot → Auftrag → Rechnung technisch zusammen?

**BELEGT**

Angebot → Auftrag (Statuswechsel `offer` → `deal` am Dokument):
- `$T/app/Http/Controllers/Customer/Offer/OfferFolderController.php:3118` — Übergang `DOCUMENT_STATUS_OFFER` → `DOCUMENT_STATUS_DEAL`.
- `:3125-3128` — Angebots-Snapshot wird eingefroren: `$detail->angebot_snapshot_sections = $detail->sections; $detail->angebot_snapshot_at = now();`
- `:3131-3145` — `$dealData` mit `'price' => $detail->total_net`, `'offer_id' => …`, `'offer_folder_id' => $folder->id`, `'offer_number' => $folder->offer->offer_no`.
- `:3147-3167` — **Match über fachliches Tripel, nicht über ID**: `DB::table('deals')->where('customer_id',…)->where('alternative_id',…)->where('product_id',…)->first()` → `update()` oder `insert()`.
- `:3170-3174` — `lead_product_lists.status = 'deal'`.
- Zweiter, unabhängiger Auftrags-Schreibpfad: `$T/app/Http/Controllers/Customer/Deal/DealController.php:3686` `Deal::create([…'offer_id' => $offerId, 'offer_folder_id' => $offerFolderId…])`, Angebot dort über `lead_product_lists.accepted_offer_folder_id` bzw. Fallback-Suche `:3671-3683`.
- Auftragsnummer: `$T/app/Models/Deal.php:52-77` (`generateOrderNo`, Präfix aus `Offer::resolveOfferPrefix`).
- Schema: `$T/database/migrations/2026_04_02_073002_add_offer_relations_to_deals_table.php:13-24` (`deals.offer_id`, `deals.offer_folder_id`, beide `nullOnDelete`).

Auftrag → Auftragsbestätigung:
- `$T/database/migrations/2026_07_16_130001_create_order_confirmations_table.php:19-27` — `deal_id`, `ab_no`, `positions` (JSON), `total_net/tax_rate/total_gross`, `ohne_snapshot`.
- `$T/app/Http/Controllers/Customer/Deal/AuftragseingangController.php:84-96` — Insert, Positionen aus `$deal->folder->detail` (Snapshot), append-only.

Auftrag/Angebot → Rechnung:
- Schema: `$T/database/migrations/2026_06_09_123236_add_deal_link_and_history_to_invoices.php:13-27` — `invoices.deal_id`, `invoices.offer_detail_id`, `deal_limit_amount`, `deal_remaining_before/after`; zusätzlich `invoice_histories`.
- `$T/database/migrations/2026_06_15_121245_add_auftrag_sync_columns_to_invoices_table.php:12-27` — `source_offer_detail_id`, `source_offer_items_hash`, `source_offer_synced_at`, `source_offer_updated_at`.
- Auflösung Deal → OfferDetail (5-stufige Fallback-Kaskade): `$T/app/Http/Controllers/Invoice/InvoiceController.php:848-900` (`findOfferDetailForDeal`: `deals.offer_detail_id` → `offer_id`+`folder_id` → `folder_id` → `offers` …).
- Positionen aus dem Angebot in die Rechnung: `$T/app/Http/Controllers/Invoice/InvoiceController.php:831-835` (`decodeOfferSections` + `invoiceItemsFromOfferSections`).
- Limitprüfung gegen den Auftragswert: `:1259-1330` (`syncDealLimitSnapshot`, `guardDealLimit`, `dealInvoiceBalance`).
- Alt-Schiene stillgelegt: `$T/app/Http/Controllers/Customer/Deal/DealController.php:188` „deal_invoices stillgelegt (invoices = führende Schiene, 2026-07-05)"; `$T/routes/web.php:4429` (Routen entfernt).

**BEWERTUNG**
- Es gibt eine durchgehende Referenzkette **Angebot → Auftrag → Rechnung**, aber sie ist **an drei Stellen brüchig**:
  1. **Der Auftrag wird beim Statuswechsel nicht per ID, sondern per Tripel (`customer_id`, `alternative_id`, `product_id`) gesucht** (`OfferFolderController.php:3147-3151`). Existiert zu diesem Tripel bereits ein älterer Deal, wird dieser **überschrieben** statt ein zweiter angelegt — ein zweites Angebot zu derselben Kunde/Objekt/Gewerk-Kombination verliert damit den eigenen Auftrag.
  2. **Der einzige direkte Weg Angebot → Rechnung setzt `deal_id` bewusst auf `null`**: `$T/app/Http/Controllers/Invoice/InvoiceCanvasController.php:56-62` — `'deal_id' => null, 'offer_detail_id' => $offerDetail->id`. Eine so erzeugte Rechnung hängt am Angebot, **nicht am Auftrag** ⇒ die Limitprüfung `guardDealLimit` (die auf `invoices.deal_id` prüft, `InvoiceController.php:1259`) greift für diese Rechnungen **nicht**.
  3. **`deals.offer_detail_id` ist nirgends als Migration angelegt** — der Code prüft defensiv `Schema::hasColumn('deals','offer_detail_id')` (`InvoiceController.php:862`), findet die Spalte in keiner der 612 Migrationen. Die stabilste Referenz der Kette existiert also nur als Fallback-Erwartung.
- Der Snapshot-Mechanismus (`angebot_snapshot_sections`, `order_confirmations.positions`, `deal_measurements.sections_snapshot`) ist konsequent und gut dokumentiert — er friert Zustände ein, ersetzt aber **keine Belegkette**: jeder Snapshot ist ein eigener JSON-Blob ohne Positions-IDs.

**ANNAHME**
- `Invoice::TYPEN_OHNE_ZAHLUNG` und `Invoice::ZAHLUNGSZIEL_TAGE` sind Model-Konstanten; ich habe sie aus den Nutzungsstellen (`GutschriftenController.php:29`, `InvoiceCanvasController.php:70-72`) erschlossen, nicht aus `$T/app/Models/Invoice.php` selbst gelesen.

**OFFEN**
- Ob in der laufenden Datenbank `deals.offer_detail_id` doch existiert (z. B. per Hand ergänzt) — nicht prüfbar ohne DB-Zugriff.
- Wie viele Aufträge de facto durch den Tripel-Match überschrieben wurden — nur per Daten feststellbar.

---

### Frage 2 — Wo kommt der Preis einer Angebotsposition heute her?

**BELEGT** — Kette in vier Stufen:

**Stufe 1 — Ursprung (Stammdaten/Import):**
- `distributor_prices.purchase_price` (EK) / `.price` (UVP), geschrieben per `updateOrCreate` aus IDS/OMD/Datanorm und Excel: `$T/app/Http/Controllers/Product/IDS/gconline/IdsController.php:106`, `$T/app/Http/Controllers/Product/ProductImportController.php:189`.
- Alternativ `products.retail_price` / `products.purchase_price` (`$T/database/migrations/2025_11_10_101908`).

**Stufe 2 — Katalog-Snapshot in die Stückliste:**
- `master_set_components.unit_price` (VK) und `.purchase_price` (EK) + `.margin`, mit FK `distributor_id`/`distributor_price_id`.
- Migrationskommentar: `$T/database/migrations/2026_01_05_103845_create_master_set_components_table.php:24` „snapshot price at time of saving".
- Schreibpfad: `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:1263` (`'margin' => (float)($data['margin'] ?? 50)`), `:1340`, `:1365`.
- Kalkulation je Stück: `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:454-461`
  `$gkPerPiece = purchase_price × global_gemeinkosten/100; $wagnisPerPiece = purchase_price × global_wagnis/100; $dbPerPiece = purchase_price × margin/100; $vkPerPiece = purchase_price + gk + wagnis + db;`

**Stufe 3 — Übernahme in das Angebots-JSON:**
- Aus MasterSet: `$T/app/Http/Controllers/Customer/Offer/OfferWizardController.php:1010-1012` (`$vk = $child->unit_price; $ek = $child->purchase_price;`) → `:1041-1043` (`'unit_price' => $vk, 'purchase_price' => $ek, 'margin' => …`).
- Aus Lieferantensuche direkt: `$T/app/Http/Controllers/Customer/Offer/OfferSupplierSearchController.php:487-500` — `purchasePrice = distributorPrice?->purchase_price ?? saved['purchase_price'] ?? saved['price'] ?? 0`; `unitPrice = distributorPrice?->price ?? saved['price'] ?? purchasePrice`; `marginPercent` daraus **oder Default 20** (`:499`) → Payload `:534-538` (`'price' => $unitPrice, 'ek' => $purchasePrice`).
- Lohn: `$T/app/Http/Controllers/Customer/Offer/OfferWizardController.php:1861-1867` — `rate = master_set_labor.hourly_rate`, sonst `qualification.default_price`; Marge daraus abgeleitet.

**Stufe 4 — Server-Korrektur beim Speichern und Auswertung:**
- `$T/app/Http/Controllers/Customer/Offer/OfferController.php:2318` — `$sections = app(CatalogPriceGuard::class)->apply($sections);` (einziger Aufrufer).
- `$T/app/Services/Offer/CatalogPriceGuard.php:9-17` — für Knoten mit `component_id` werden Katalog-EK/-VK **erzwungen**, Browser-Preis verworfen; Knoten ohne Anker werden als `preis_quelle='manuell'` markiert; `component_id` ohne Treffer → `preis_quelle='katalog_fehlt'`, bestehender Preis bleibt, **nie stiller 0-Preis**.
- Lesereihenfolge beim Rechnen: `$T/app/Http/Controllers/Customer/Offer/OfferController.php:1779-1788` VK-Keys `['price','unit_price','sale_price','vk','rate','total_price']`; `:1790-1799` EK-Keys `['ek','cost','buying_price','purchase_price','cost_price']`.
- Zeilensumme: `:1917-1918` `vk = (qty / price_unit_value) × VK-Preis`; `ek = (qty / cost_price_unit_value) × EK-Preis`.
- In die Rechnung: `$T/app/Http/Controllers/Invoice/InvoiceController.php:1020-1021` `$unitPrice = (float)($item['price'] ?? $item['unit_price'] ?? 0); $lineTotal = round($qty × $unitPrice, 2);` — **`price_unit_value` wird hier nicht mehr berücksichtigt.**

**BEWERTUNG**
- Der Preis ist ein **mehrfach kopierter Snapshot**: Lieferantenpreis → Komponenten-Snapshot → JSON-Knoten → (Rechnung). Kein Punkt der Kette hält fest, *welcher* `distributor_prices`-Stand zu welchem Zeitpunkt galt (`distributor_prices` wird per `updateOrCreate` überschrieben, nur `price_date`).
- `CatalogPriceGuard` ist die einzige serverseitige Preisintegrität — und er greift **nur bei gesetztem `component_id`**. Der Docblock benennt das selbst (`CatalogPriceGuard.php:23-25`): „Scope P1-a (bewusst eng): NUR `component_id`. `product_id`-Reprice, `sub_*`-ID-Verlust, GK/Wagnis und Engine 2 sind ausdrücklich NICHT Teil."
- Zwei stille Preisrisiken:
  1. **Default-Marge 20 %** bei fehlendem EK (`OfferSupplierSearchController.php:499`) — eine erfundene Zahl, die als echte Marge weitergereicht wird.
  2. **Preiseinheit-Verlust beim Rechnungsübergang**: Angebot rechnet `qty / price_unit_value × price` (`OfferController.php:1917`), Rechnung rechnet `qty × unit_price` (`InvoiceController.php:1022`). Bei jeder Position mit `price_unit_value ≠ 1` (z. B. Preis je 100 m) entsteht eine Abweichung zwischen Angebots- und Rechnungssumme.
- Ein „gültiger Preis für Kunde X / Projekt Y" existiert nicht — es gibt nur Katalogpreis und freie Eingabe.

**ANNAHME**
- Dass die Angebots-UI die Keys `price`/`ek` konsistent setzt, entnehme ich den Payload-Buildern (`OfferSupplierSearchController.php:534-538`, `OfferWizardController.php:1041`); die JS-Seite habe ich nicht ausgewertet.

**OFFEN**
- Ob `price_unit_value` in der Praxis häufig ≠ 1 ist (Datenfrage).
- Ob es weitere Payload-Erzeuger außerhalb der geprüften Controller gibt (z. B. im Frontend-Bundle).

---

### Frage 3 — Gibt es ein Margen-/Deckungsbeitragskonzept? Wo wird gerechnet?

**BELEGT**

Konzept-Bausteine, die existieren:
- Marge **persistiert** je Komponente: `$T/database/migrations/2026_03_02_093129_add_commercial_fields_to_master_set_components.php:16-17` (`purchase_price`, `margin` Default 50) und `$T/database/migrations/2026_03_10_123814_create_master_set_cart_items_table.php:60`.
- Kalkulationsparameter **persistiert**: `costing_sets` mit `risk_percent`, `profit_percent`, `material_overhead_percent`, `commission_mode ∈ {revenue_percent, fixed, db_percent}` (`$T/database/migrations/2026_03_05_112752_create_costing_sets_table.php`), Rollensätze `costing_set_roles` (`wage_cost_per_hour`, `full_cost_rate_per_hour`, `sell_rate_per_hour`).
- Set-Ebene: `master_sets.global_gemeinkosten`, `global_wagnis`, `global_mat_margin`, `min_mat_margin` (gelesen in `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:226-229`).

Wo gerechnet wird (drei Orte, alle zur Laufzeit):
1. **Set-/Stücklistenebene** — `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:445-472`:
   `dbPerPiece = purchase_price × margin/100`; `vkPerPiece = purchase_price + gk + wagnis + db`; `ekLine`, `vkLine`, `dbLine = vkLine − ekLine`; Aggregation nach `mainEk/mainVk/mainDb` und `subEk/subVk/subDb`.
2. **Angebotsebene** — `$T/app/Http/Controllers/Customer/Offer/OfferController.php:1965-2058` (`calculateOfferSections`):
   je Abschnitt `db = vk − ek` (`:2028`), `markup_percent = (vk−ek)/ek × 100` (`:2029`), `margin_percent = (vk−ek)/vk × 100` (`:2030`); gesamt `total_db` (`:2041`), `markup_percent` (`:2044`), `margin_percent` (`:2045`); zusätzlich Kostenarten-Buckets `material_vk/ek`, `labor_vk/ek`, `service_vk/ek`, `logistics_vk/ek`, `other_vk/ek` (`:1976-1985`, `:2017-2019`).
3. **Lohnvorschlag** — `$T/app/Http/Controllers/Customer/Offer/OfferFolderController.php:3730-3731`:
   `sellPerHour = round(realEkPerHour × (1 + marginPercent/100), 2)`; und `$T/app/Http/Controllers/Customer/Offer/OfferWizardController.php:1867` `margin = (rate − qualPrice)/qualPrice × 100`.

Was **nicht** existiert:
- Keine Spalte `deckungsbeitrag`/`contribution_margin` in 612 Migrationen (0 Treffer).
- `offer_details` speichert nur `total_net`, `tax_rate`, `total_gross` (`$T/database/migrations/2026_03_09_123239:33-35`) — `total_ek` / `total_db` werden von `calculateOfferSections` erzeugt und **verworfen**.
- `invoice_items` hat keinen EK (`$T/database/migrations/2026_01_19_201601_create_invoice_items_table.php` — `unit_price`, `tax_rate`, `line_total`, kein `cost`).
- `deals` hat nur `price` (Kopf-VK), keinen EK.
- Das Controlling rechnet ausschließlich Umsatz: `$T/app/Http/Controllers/Controlling/UmsaetzeController.php:33-44` (`SUM(total_amount)`) — kein DB, keine Marge.
- `profitability_calculations` (`$T/database/migrations/2024_11_12_112352`) ist die **Kunden-Wirtschaftlichkeit** (Amortisation, ROI, CO₂), nicht die Unternehmensmarge.

**BEWERTUNG**
- Ein Margenkonzept ist **angelegt, aber nicht geschlossen**. Marge lebt als Zahl an der Komponente; Deckungsbeitrag lebt nur als Rechenergebnis auf dem Bildschirm.
- Drei Konsequenzen:
  1. **Kein Vorher/Nachher**: Da `total_db` nicht gespeichert wird, lässt sich die Marge eines Angebots nach einer Preislistenänderung nicht mehr rekonstruieren — der EK im JSON ändert sich nicht, aber es gibt keinen Vergleichspunkt.
  2. **Kein DB auf der Umsatz-Wahrheit**: `invoices` ist laut `$T/app/Http/Controllers/Controlling/UmsaetzeController.php:11-13` die „EINZIGE Umsatz-Wahrheit" — und trägt keinen EK. Ein Auftrags- oder Monats-Deckungsbeitrag ist mit dem heutigen Schema **nicht berechenbar**.
  3. **Zwei konkurrierende Kalkulationsquellen**: `costing_sets` (mit Wagnis, Gewinn, Rundung, Provision) ist an `master_sets.costing_set_id` angebunden (`$T/database/migrations/2026_03_05_131608`), wird aber in der Set-Kalkulation `MasterSetController.php:454-460` **nicht verwendet** — dort greifen `master_sets.global_gemeinkosten`/`global_wagnis`. `rounding_step`, `rounding_mode`, `commission_mode='db_percent'`, `small_parts_percent` habe ich in keinem Rechenpfad gefunden.

**ANNAHME**
- Dass `costing_sets` gar nicht in die Preisbildung eingeht, schließe ich aus dem Fehlen jedes Rechenaufrufs; `$T/app/Services/Auslegung/WpCostingService.php` habe ich nicht im Detail geprüft — dort könnte ein weiterer, WP-spezifischer Pfad liegen.

**OFFEN**
- Rolle von `WpCostingService`/`WpKostenErgebnis` in der Preisbildung.
- Ob `master_sets.main_total`/`sub_total`/`data`-Blob (Migrationen `2026_03_03_101707`, `2026_01_30_133141`) DB-Werte mitführen.

---

### Frage 4 — Wie sind Stücklisten/Sets modelliert: JSON-Blob oder eigene Tabellen?

**BELEGT** — **Beides, nacheinander. Der Bruch liegt an der Übergabe ins Angebot.**

Im Katalog: **eigene Tabellen**
- `$T/database/migrations/2026_01_05_103819_create_master_sets_table.php`, `…103845_create_master_set_components_table.php` (mit `parent_id` → Selbstreferenz = zweistufige Hierarchie, `product_id`, `distributor_id`, `distributor_price_id`, `qty`, `unit_price`, `sort_order`), `…103910_create_master_set_labors_table.php`.
- Ergänzt um `master_set_tasks`, `master_set_task_labors`, `master_set_checklists`, `master_set_component_descriptions`, `master_set_groups`, `master_set_group_master_set`.
- Schreibpfade: `$T/app/Http/Controllers/Product/MasterSet/MasterSetController.php:578` (`MasterSet::create`), `:1340` (`MasterSetComponent::create($mainData)`), `:1365` (Sub-Komponente), `:1920`/`:1956` (Kopie).
- Legacy-Tabellenschiene daneben: `product_master_sets`, `product_sub_sets`, `add_product_to_sets`, `group_sets` — Schreibpfade `$T/app/Http/Controllers/Old/ProductMasterSetController.php:175`, `$T/app/Http/Controllers/Old/ProductSubSetController.php:214`.

Im Angebot: **JSON-Blob**
- `$T/database/migrations/2026_03_09_123239_create_offer_details_table.php:29-30` — `$table->json('sections'); $table->json('canvas_images');` mit Kommentar „The ‚Meat' of the Offer - Stored as JSON" / „Full hierarchy of items, sets, and labor".
- Persistierung: `$T/app/Http/Controllers/Customer/Offer/OfferFolderController.php:2264-2272` (`$detail->sections = $sections; … $detail->save();`).
- Verschachtelung im JSON: `$T/app/Http/Controllers/Customer/Offer/OfferController.php:1805-1813` — Kind-Knoten unter **fünf verschiedenen Keys** `['subItems','sub_items','children','components','items']`; gespiegelt in `$T/app/Services/Offer/CatalogPriceGuard.php:29`.
- Weitere JSON-Kopien derselben Struktur: `offer_details.angebot_snapshot_sections`, `offer_templates.sections`, `order_confirmations.positions`, `deal_measurements.sections_snapshot`, `deal_measurement_items.raw_snapshot`, `invoice_items.source_payload`.

Rückverweis vom JSON in die Tabellen (nur als Datenfeld, kein FK):
- `offer_product_lists.master_set_id` → `product_master_sets` (`$T/database/migrations/2025_08_27_122931:17`) — zeigt auf die **Legacy**-Tabelle, nicht auf `master_sets`.
- Im JSON-Knoten: `component_id`, `master_set_id`, `product_id`, `distributor_price_id` — ausgelesen in `$T/app/Http/Controllers/Invoice/InvoiceController.php:1046-1057`; als Anker genutzt in `$T/app/Services/Offer/CatalogPriceGuard.php:60-63`.

**BEWERTUNG**
- Die **Katalogseite ist relational und sauber** (eigene Tabellen, Hierarchie, FKs auf Produkt und Lieferantenpreis).
- Die **Angebots-/Auftrags-/Rechnungsseite ist ein JSON-Baum ohne Schema**. Konsequenzen:
  1. **Fünf zulässige Kind-Keys** bedeuten fünf mögliche Schreibweisen derselben Struktur — jede auswertende Stelle muss alle fünf kennen. `CatalogPriceGuard` spiegelt die Liste bewusst, ist damit aber dauerhaft an `OfferController` gekoppelt.
  2. **Kein referenzieller Schutz**: Wird eine `master_set_components`-Zeile gelöscht, bleibt die Position im Angebot bestehen und wird von `CatalogPriceGuard` als `preis_quelle='katalog_fehlt'` markiert (`CatalogPriceGuard.php:48`) — bewusst gewählt, aber es bedeutet, dass Angebote Positionen ohne Katalogdeckung tragen können.
  3. **Positions-IDs sind nicht stabil**: `$T/app/Http/Controllers/Customer/Offer/OfferSupplierSearchController.php:505` erzeugt IDs wie `'supplier_'.$connection->id.'_'.$product->id.'_'.now()->timestamp.'_'.Str::random(5)`. Damit ist eine Position über Angebot → AB → Aufmaß → Rechnung nicht durchgängig identifizierbar; `invoice_items.source_item_id` ist folgerichtig ein `string`, kein FK (`$T/database/migrations/2026_06_15_095524:36`).
  4. **Keine Auswertbarkeit per SQL**: „Welche Artikel stecken in offenen Angeboten?" ist ohne JSON-Parsing in PHP nicht beantwortbar — sichtbar daran, dass `MaterialbedarfController` (`$T/app/Http/Controllers/Customer/Deal/MaterialbedarfController.php:29-36`) mit `limit(500)` in PHP über die Deals iterieren muss.
- Der Docblock der AB-Migration formuliert die gewählte Strategie explizit: „append-only (Korrektur = neue AB, nie Edit) … die AB friert den Stand zum Erzeugungszeitpunkt ein" (`$T/database/migrations/2026_07_16_130001:9-11`). Das ist bewusste Snapshot-Architektur — sie ersetzt aber keine Positionsdatenhaltung.

**ANNAHME**
- Dass `offer_product_lists` (die echte Positionstabelle) fachlich stillgelegt ist, schließe ich daraus, dass weder `InvoiceController`, `DealMaterialListController`, `DealMeasurementController` noch `AuftragseingangController` sie lesen — alle lesen `offer_details.sections`. Eine ausdrückliche Stilllegungsnotiz wie bei `deal_invoices` fehlt.

**OFFEN**
- Ob `offer_product_lists` noch aktiv befüllt wird (Route `$T/routes/web.php:3600` existiert) und wenn ja, von welcher Oberfläche.

---

### Frage 5 — Gibt es Projekt- und Objektbezug an Positionen?

**BELEGT**

Auf **Beleg-/Kopfebene**: ja, durchgängig.
- `offers.customer_id` → `new_leads`, `offers.alternative_id` → `lead_alternative_adds`, `offers.product_id` → `article_groups` (`$T/database/migrations/2024_12_09_104537:27-29`).
- `offer_folders`: dieselben drei FKs (`$T/database/migrations/2025_08_27_062916:17-19`).
- `deals`: `customer_id`, `alternative_id`, `product_id` (`$T/database/migrations/2025_02_05_125814:41-43`).
- `invoices.object_id` → `lead_alternative_adds` (`$T/database/migrations/2023_07_19_100437:23`, FK `:66`); `invoices.deal_id` (`$T/database/migrations/2026_06_09_123236:17`).
- `lead_product_lists`, `goods_receipts` (`customer_id`, `object_id`, `lead_product_list_id`), `delivery_notes` (`customer_id`, `alternative_id`, `lead_product_list_id`, `deal_id` — `$T/database/migrations/2026_04_23_132825:19-35`).
- `daily_report_time_customers`: „object_product"-Felder (`$T/database/migrations/2025_05_22_140145`).
- `plan_uploads.lead_alternative_add_id` (`$T/database/migrations/2026_07_30_105516:21`) — Docblock `:13-15`: „Der ‚Hausplaner-Projekt'-Begriff aus dem Auftrag ist in diesem Bestand `LeadAlternativeAdd`".

Auf **Positionsebene**: nein.
- `invoice_items`: `invoice_id`, `product_id`, `article_product_id`, `component_id`, `distributor_id`, `distributor_price_id`, `source_item_*` — **kein `object_id`, kein `alternative_id`, kein `project_id`, kein `deal_id`** (`$T/database/migrations/2026_01_19_201601` + `2026_06_15_095524`).
- `offer_product_lists`: `offer_id`, `offer_folder_id`, `master_set_id` — kein Objekt-/Projektbezug (`$T/database/migrations/2025_08_27_122931:16-18`).
- `offer_details.sections`-Knoten: die Payload-Builder (`$T/app/Http/Controllers/Customer/Offer/OfferSupplierSearchController.php:503-545`, `$T/app/Http/Controllers/Customer/Offer/OfferWizardController.php:1021-1067`) setzen **kein** Objekt- oder Projektfeld.
- `deal_measurement_items`: `deal_id`, `offer_id`, `offer_detail_id`, `master_set_id`, `product_id` — **kein `alternative_id`** (`$T/database/migrations/2026_04_29_114107:18-32`), obwohl der Kopf `deal_measurements` `alternative_id` führt (`…114057:21`).
- `order_confirmations.positions`: JSON-Snapshot ohne Objektfelder (`$T/database/migrations/2026_07_16_130001:22`).
- Der FiBu-Zweig verzichtet ausdrücklich: `$T/database/migrations/2026_07_05_180001_create_accounting_foundation_tables.php:10` „Weiche 5 (kein project_id)"; `accounting_documents` führt `customer_id` + `deal_id` „lose … (Weiche 5: kein project_id)" (`$T/database/migrations/2026_07_05_180002:38`).

**BEWERTUNG**
- **Objektbezug: ja — aber ausschließlich über den Beleg, nie an der Position.** Für das heutige Geschäft (ein Angebot = ein Objekt = ein Gewerk) trägt das. Sobald ein Beleg mehrere Objekte umfassen soll (Mehrfamilienhaus mit WE-weiser Abrechnung, Liegenschaftsportfolio, gemischter Auftrag), ist die Position **nicht zuordenbar** — die Zuordnung müsste über getrennte Belege erzwungen werden.
- **Projektbezug: faktisch nicht vorhanden.** Die `projects`-Tabelle hat einen einzigen, praktisch unbenutzten Schreibpfad (`PlaningController.php:186`); kein Positionsträger referenziert sie. Der operative Projektbegriff ist `deals` bzw. `planner_plans/planner_items` — und die Verbindung dorthin läuft ebenfalls über Kopfsätze (`planner_items.kanban_lead_task_id`, `planner_item_materials`), nicht über Angebots-/Rechnungspositionen.
- Der schmerzhafteste konkrete Punkt: **`deal_measurement_items` verliert den Objektbezug**, obwohl das Aufmaß objektbezogen erhoben wird. Wer Material je Objekt auswerten will, muss über `deal_measurement_id` → `deal_measurements.alternative_id` joinen — das funktioniert, ist aber ein Umweg, der bei Mehr-Objekt-Aufmaßen bricht.
- Positiv: `lead_alternative_adds` ist als Objektanker **konsequent durchgezogen** — er taucht in `offers`, `deals`, `projects`, `invoices`, `lead_product_lists`, `goods_receipts`, `delivery_notes`, `customer_notes`, `customer_histories`, `plan_uploads` auf. Ein Positions-Objektbezug wäre damit additiv nachrüstbar, ohne die Kette zu berühren.

**ANNAHME**
- Dass „Projekt" im Sinne der Fragestellung den Auftrag/das Bauvorhaben meint (nicht die `projects`-Tabelle); unter dieser Lesart ist der Bezug an der Position über `deal_id` ebenfalls nicht vorhanden — `invoice_items` trägt kein `deal_id`, nur die Rechnung tut es.

**OFFEN**
- Ob im Angebots-JSON in der Praxis objektbezogene Freitextfelder (z. B. Raum/Etage im `name` oder `desc_html`) gepflegt werden — nur an echten Daten feststellbar.

---

## Zusammenfassung der Lücken (nach Schwere)

**BELEGT + BEWERTUNG**

1. **Bestellung als Beleg fehlt vollständig** — kein Kopf, keine Position, kein Nummernkreis, kein Lieferanten-FK. Der Einkauf läuft über ein Statuswort und einen JSON-Blob an der Aufmaßzeile (`$T/app/Http/Controllers/Customer/Offer/DealMaterialListController.php:1809-1856`). Folgefehlend: Rückstand, Reservierung, Liefertermin als Spalte, Mindestbestellwert, Bonus, Rahmenvertrag — alle sechs hängen an diesem einen fehlenden Objekt.
2. **Kein Deckungsbeitrag auf der Umsatz-Wahrheit** — `invoice_items` trägt keinen EK; `offer_details` speichert `total_db` nicht. Marge existiert als Anzeige, nicht als Kennzahl (`$T/app/Http/Controllers/Customer/Offer/OfferController.php:2041` vs. `$T/database/migrations/2026_03_09_123239:33-35`).
3. **Die Position ist kein Objekt** — sie ist ein JSON-Knoten mit instabiler ID, ohne Schema, ohne FK, ohne Objekt-/Projektbezug, ohne Rabatt-, Alternativ-, Eventual- oder Nachtragssemantik.
4. **Auftrag ohne eigene Positionen** — `deals` trägt nur `price`; jede nachgelagerte Sicht (AB, Aufmaß, Materialliste, Rechnung) muss zurück ins Angebots-JSON greifen (`$T/app/Http/Controllers/Invoice/InvoiceController.php:848-900`).
5. **Kein kunden- oder projektspezifischer Preis** — und der einzige serverseitige Preisschutz (`CatalogPriceGuard`) setzt abweichende Preise sogar aktiv zurück.
6. **Keine ausgehende Kundenkommunikation** — ein einziger `Mail::to` im gesamten `app/` (`$T/app/Http/Controllers/VideoCallController.php:126`).

**OFFEN (nicht ohne DB/Frontend entscheidbar)**
- Reales Nutzungsvolumen der Doppelschienen (`offer_product_lists` vs. `offer_details.sections`; `master_sets` vs. `product_master_sets`; `new_leads` vs. `customers`).
- Ob `deals.offer_detail_id` in der laufenden DB existiert.
- Rolle von `WpCostingService` in der Preisbildung.
- Ob `price_unit_value ≠ 1` praktisch vorkommt (bestimmt die Schwere des Angebot-↔-Rechnung-Rundungsbruchs).
