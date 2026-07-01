# CRM-Inventur 03 — Angebot & Konfiguration (Zone 03)

**Stand:** 2026-07-01 · **Modus:** nur Lesen/Analyse · **Prinzip:** Breite vor Tiefe · **Ausgeschlossen:** `vendor/`, `node_modules/`, `storage/`, `.git`

## Abgrenzung dieser Zone

Diese Zone dokumentiert die **Konfigurations-/Erstellungs-Mechanik von Angeboten und Sets** — d. h. WIE ein Angebot inhaltlich gebaut, kalkuliert und als Dokument erzeugt wird (bis zum fertigen Angebot).

- **NICHT hier (Zone 04 – Auftragsseite):** was nach Angebotsannahme passiert (Auftrag = `deals`, Material-Bestellung, Feinaufmaß, Ausführung). Berührungspunkte wie `DealMaterialListController`, `deal_measurements`, `add_offer_relations_to_deals` sind nur als *Übergang* erwähnt, nicht inventarisiert.
- **NICHT hier (Status-Fluss):** die Zustandskette Lead → Angebot (`status`, `offer_status`, `document_status`, Kanban-Stages) ist bereits in `docs/workflow-analyse.md` beschrieben. Hier geht es um die **Konfigurations-Mechanik**, nicht um den Status-Fluss.
- **Glossar:** Angebot = `offers` · Auftrag = `deals` · Kunde = `new_leads` · Gewerk = `lead_product_lists` · Artikelgruppe/„Produkt" = `article_groups` (Offer/Folder `product_id` → `article_groups`).

**Grobstruktur der Zone:** Ein Angebot (`offers`) gruppiert eine oder mehrere **Mappen** (`offer_folders`); jede Mappe trägt ein **Angebotsdokument** (`offer_details`, JSON-Sections + Canvas). Inhalte werden aus **Master-Sets** (Bausteinkatalog), **Vorlagen** (`offer_templates`) und Einzelprodukten befüllt. Preise/Löhne entstehen über **Costing-Sets** (Kalkulationssätze) und **Product-Formulas** (Checklisten-Formulare). PDF wird client-seitig gerendert, das Ergebnis serverseitig als JSON gespeichert.

**Gesamtvolumen Zone (Controller):** ~26.000 Zeilen über ~38 Controller. Aktiver Angebots-Editor `config.blade.php` allein ~25.000 Zeilen; `master_sets/index.blade.php` ~15.000 Zeilen.

---

## 1. Angebots-Rumpf: offers, offer_folders, offer_details

- **(a) Zweck:** Zentrale Datenhaltung eines Angebots. `offers` = Kopf (Kunde, Objekt/`alternative_id`, Artikelgruppe, Ersteller/Empfänger, Status). `offer_folders` = **Mappe/Gruppierung** innerhalb eines Angebots (mehrere Varianten/Gewerke, eigener Status-Workflow, Farbe, History). `offer_details` = **das eigentliche Angebotsdokument** je Mappe: komplette Positions-/Set-/Lohn-Hierarchie als JSON (`sections`), frei platzierte Bilder (`canvas_images`/`placed_images`), Branding, Deckblatt (`cover_text`/`cover_text_html`) und die serverautoritativen Summen (`total_net`, `tax_rate`, `total_gross`).
- **(b) Controller/Routen:** `OfferController` (2.951 Z., CRUD, `index`/`data`, Kanban, Team, `storeFolder`), `OfferFolderController` (**3.810 Z.**, größter Controller: `show`/`data`, clone, AGB, Material-Aktionen/-Vergleich/-Distributor-Wechsel, Attachments, Kanban-Move, Status/Document-Status), `OfferDetailsController` (557 Z., `update`, `loadProducts`, `masterSetDetails`, Employee-/Asset-/Product-Listen je Mappe). Routen-Präfixe `admin/offers/...` (`routes/web.php` ~3386–3540), Folder-Routen mit `{offer}/folders/{folder}`.
- **(c) Kern-Tabellen:** `offers`, `offer_folders`, `offer_details` (+ Erweiterungen: `offer_folder_id`-Link, `brand_logo_url`, `cover_text_html`, `placed_images`, `print_attachments`, `agb`, `material_history`, `document_status`, `offer_no`, `branch_footer`). Kanban: `offer_kanban_stages`, `kanban_filter_settings`. Aktivität: `offer_folder_activities`, `offer_delete_logs`.
- **(d) Größe:** sehr groß — Kern-Trio ~7.300 Z. Controller; ~14 Migrationen ändern allein `offer_details`/`offer_folders` inkrementell (starke Evolution, viele Nach-Migrationen).
- **(e) Status:** Aktiv/produktiv, aber **hohe Migrations-Fluktuation** (Spalten laufend nachgezogen). `offer_folders.history`/`offer_details.material_history` = `longText`/JSON-Historien ohne eigene Tabelle. `offer_folders` trägt drei Status-Felder (`document_status`, `offer_status`, `deal_status`) → Überlappung mit dem Status-Fluss (Zone-übergreifend, siehe workflow-analyse.md).

## 2. Offer-Wizard (Angebotserstellung / Einstieg)

- **(a) Zweck:** Geführter Einstieg „Neues Angebot": Kunde suchen → Objekt wählen → Katalog (Group-Sets/Master-Sets/Produkte/Vorlagen) → Angebot anlegen (`createOffer`). Liefert JSON-Endpunkte für die Frontend-Suche im Editor.
- **(b) Controller/Routen:** `OfferWizardController` (1.921 Z.): `index`, `smart`, `searchCustomers`, `customerShow`, `customerObjects`, `createOffer`, `groupSetsCatalog`, `groupSetShow`, `productsList`, `searchProducts`, `showJson`. Routen-Präfix `offers/wizard/...` (web.php ~3265–3294), Einstieg auch via Sidebar „Neues Angebot" → `url('offers/wizard')`. Vorlagen-Auswahl: `OfferTemplatePickerController` (528 Z., `useTemplate`, `check`, diverse Suchen).
- **(c) Kern-Tabellen:** liest `new_leads`, `lead_alternative_adds` (Objekte), `article_groups`, `master_sets` / `master_set_groups`, `offer_templates`; schreibt `offers`(+`offer_folders`) beim Anlegen.
- **(d) Größe:** groß (~1.900 Z. Wizard + ~1.400 Z. Template-Picker/Controller). Blade `wizard-smart.blade.php` ~2.200 Z.
- **(e) Status:** Aktiv. FIX-Kommentar im Code (P0-04): Wizard inkl. Kalkulation/Netto-Preise hinter `auth` gestellt (Sicherheits-Nachbesserung). `smart`-Variante parallel zum klassischen `index`.

## 3. Angebots-Editor / Konfiguration (Positionen, Rabatte, Branding)

- **(a) Zweck:** Der eigentliche visuelle Angebots-Konfigurator: Sections mit Positionen/Sets/Lohn zusammenstellen, Mengen/Rabatte/Aktiv-Flags setzen, Deckblatt/Branding/Bilder platzieren, Live-Summenrechnung. Speichern via `saveDocument` → schreibt `offer_details` (oder `offer_templates`, wenn `is_template`). Server rechnet die Summen autoritativ nach (`calculateOfferSections`, `offerNormalizeSections`, `offerNormalizeDecimal` im `OfferController`, ~Z. 1681–2060).
- **(b) Controller/Routen:** `OfferController::saveDocument` (Route `offers.save-document`), `OfferDetailsController::update` (`offer.details.update`). Weitere Editor-Bausteine: `OfferDocumentController` (441 Z., `load`/`biography`/Präsenz-Ping — Mehrbenutzer-Präsenz via Broadcast-Channel `offer-folder.{folderId}`), `OfferCommentController`, `OfferGallaryController`, `OfferPageLibraryController` (586 Z., Seiten-Bibliothek), `OfferRoofLayoutConfigurationController` (255 Z., Dach-Layout). Frontend: **`resources/views/admin/offer/configuration/offer/config.blade.php` (~25.000 Z.)** = aktiver Editor.
- **(c) Kern-Tabellen:** `offer_details` (JSON `sections`/`canvas_images`), `offer_comments`, `offer_folder_attachments`, `offer_page_library_items` / `offer_folder_library_pages`, `offer_roof_layout_configurations`. Rabatt-Stammdaten: `discount_groups` (`DiscountGroupController`).
- **(d) Größe:** sehr groß, Schwerpunkt im Frontend (config.blade ~25k Z.). Server-Kalkulation kompakt im OfferController.
- **(e) Status:** Aktiv, aber **massive Alt-/Kopie-Last**: neben `config.blade.php` existieren `config.blade copy.php`, `config.blade copy 2–4.php`, ein ganzer `Old/`-Ordner und `admin/offer/old/` — mehrere tausend Zeilen totes/dupliziertes Editor-Blade parallel. Rabatte werden pro Position im JSON gehalten (keine normalisierte Positions-Tabelle → Auswertbarkeit eingeschränkt).

## 4. Master-Sets (Baustein-/Produktkonfiguration)

- **(a) Zweck:** Wiederverwendbare **Bausteine** (z. B. „PV-Komplettset Dach X"), aus Material-Komponenten (`master_set_components`, hierarchisch via `parent_id`) + Lohn-Positionen (`master_set_labors`, je Abteilung/Position/Qualifikation) + Aufgaben/Checklisten. Werden im Wizard/Editor ins Angebot gezogen. Gruppierung über `master_set_groups`. Hinweis (verifiziert): eigenes ~5.000-Z.-Subsystem — Controller 2.464 + API 1.600 + Cart 1.053 + Group 538 + weitere ≈ **6.700 Z. Controller** plus 8+ Migrationen und mehrere große Blades.
- **(b) Controller/Routen:** `Product/MasterSet/MasterSetController` (**2.464 Z.**: CRUD, `data`, `catalog`, `syncComponents`/`syncLabor`/`syncTasks`/`syncChecklists`, `duplicate`, `saveCostingSettings`, `taskCostingPayload`, `hydrateComponents`), `MasterSetCartController` (1.053 Z., Warenkorb-Sektionen/Items), `MasterSetGroupController` (538 Z.), `MasterSetComponentDescriptionController`, `MasterSetDistributorCompareController` (Lieferanten-Preisvergleich). API/extern: `Api/MasterSetApiController` (**1.600 Z.**, eigene Header-Auth `X-API-USER`/`X-API-PASSWORD`, `/api/secure/master-sets`). Planer-Anbindung: `Planner/PlannerMasterSetController` (229 Z.). Sidebar: „Master-Sets" → `admin.master_sets.index`. **Legacy:** `Old/ProductMasterSetController` (540 Z.) — ignorieren.
- **(c) Kern-Tabellen:** `master_sets` (+ Nachträge: `data`, `bio`, `totals`, `costing_set_id`), `master_set_components` (+ `commercial_fields`, `article_no`, `price_unit`, `distributor_id`), `master_set_labors` (+ `qualification`, `rate`), `master_set_tasks` / `master_set_task_labors`, `master_set_checklists`, `master_set_groups` (+ Pivot `master_set_group_master_set`), `master_set_carts`/`_cart_sections`/`_cart_items`, `planner_item_master_sets`, `asset_sets`.
- **(d) Größe:** **das größte Subsystem der Zone** (~6.700 Z. Controller, ~15.000-Z.-Blade `master_sets/index.blade.php`, ~6.700-Z.-`editor.blade.php`, mehrere `index.blade copy*.php`).
- **(e) Status:** Aktiv und stark ausgebaut, sehr hohe Änderungsfrequenz (Migrations-Nachträge Feb–Jun 2026). Eigene ungewöhnliche env-basierte API-Auth (nicht Sanctum). Viele `index.blade copy N.php`-Backups.

## 5. Costing-Sets (Kalkulationssätze / Preisbildung Lohn)

- **(a) Zweck:** Zentrale **Kalkulationsparameter** für die Preisbildung: AW-Minuten (1 AW = X min), Gemeinkosten (Material/Lohn/Baustelle), Wagnis, Gewinn, Kleinteile, Fracht/Handling/Entsorgung, Rundung, Provision. Je Costing-Set **Rollen-Sätze** (`costing_set_roles`) pro Qualifikation: Lohnkosten/h, Lohnnebenkosten-%, Firmen-GK-%, Vollkostensatz/h, VK-Satz/h. Ein Master-Set referenziert ein Costing-Set; `taskCostingPayload` berechnet daraus die Lohnpreise (Rate-Modi `full_cost`/`sell_rate`/`wage_only`, Fallback-Logik, Default-/Active-Auswahl).
- **(b) Controller/Routen:** `CostingSetController` (446 Z.: `index`/`list`/`store`/`update`/`destroy`, `makeDefault`, `roles`, `rolesBulkUpdate`, `rolesSyncFromQualifications`, `rolesApplyDefaults`, `options`, `show`), `CostingSetRoleController` (65 Z.). Routen `admin/costing-sets/...` (web.php ~1483–1492, +`options`/`{costingSet}`). Anwendung im Master-Set: `MasterSetController::saveCostingSettings` / `taskCostingPayload`. Sidebar (System): „Kalkulationssätze" → `admin.costing_sets.index`.
- **(c) Kern-Tabellen:** `costing_sets` (+ `default_active`, `vk_rule`), `costing_set_roles` (unique `costing_set_id`+`qualification_id`; `qualification_id` → `position_qualifications`), Verknüpfung `master_sets.costing_set_id`.
- **(d) Größe:** mittel (~510 Z. Controller); kalkulatorische Kernlogik liegt in `MasterSetController::taskCostingPayload`.
- **(e) Status:** Aktiv, wirkt neu/konsolidiert (März 2026, saubere Migrationen mit Kommentaren). Achtung: Query nutzt teils `is_active`/`is_default` — Spaltennamen ggf. via `add_default_active`/`add_vk_rule`-Migrationen (bei Detail-Inventur verifizieren).

## 6. Product-Formulas & Checklisten-Formulare

- **(a) Zweck:** JSON-basierte **Formular-/Formel-Builder** je Artikelgruppe (`section_name`, `fields` JSON, Versionierung, Publish-Status). Dienen als Checklisten-/Konfigurationsformulare in der Angebots-/Set-Konfiguration (Sidebar-Label: „Checklisten-Formulare"). `test`/`testSubmit` = Vorschau/Prüfung, `loadChecklist` bindet an Produkt.
- **(b) Controller/Routen:** `Product/ProductFormulaController` (259 Z.: `index`/`create`/`store`/`edit`/`show`/`update`/`destroy`, `editFormula`/`updateFormula`, `test`/`testSubmit`, `loadChecklist`). Routen `product-formula/...` + `admin/formula/create/{id}` (web.php ~2875–2887). Sidebar → `product.formula.index`.
- **(c) Kern-Tabellen:** `product_formulas` (`product_id` → `article_groups`, `fields` JSON, `version`, `status`, created/edited/deleted_by).
- **(d) Größe:** klein–mittel (~260 Z.).
- **(e) Status:** Aktiv. Datenmodell einfach (ein JSON-Feld trägt die gesamte Formularlogik → Auswertbarkeit im Reporting begrenzt).

## 7. Wirtschaftlichkeit & WP-Konfiguration (Angebots-Beilagen)

- **(a) Zweck:** Fachliche Zusatz-Konfiguration/Berechnung für Angebote: **Wirtschaftlichkeit** (Annahmen + Berechnungen, z. B. PV-Rendite) und **Wärmepumpen-Auslegung** (`customer_offer_w_p_s`: Gebäudetyp, Wärmeerzeuger, Deckungsgrad, Temperaturen, Sperrzeiten).
- **(b) Controller/Routen:** `EconomicCalculationController` (98 Z., `admin/economic-calculations/...`, web.php ~1313–1318), `EconomicAssumptionController` (65 Z., Resource-Style), `CustomerOfferWPController` (65 Z., Resource-Style, `create`/`store`/…). PVGIS/Wetter-Tools angrenzend (`ToolsController`/`PVToolsController`).
- **(c) Kern-Tabellen:** `economic_assumptions`, `economic_calculations`, `customer_offer_w_p_s`.
- **(d) Größe:** klein (~230 Z. zusammen).
- **(e) Status:** Aktiv, aber schlank/randständig. Kopplung ans Haupt-Angebotsdokument (`offer_details`) nicht offensichtlich — eher separate Beilagen (bei Detail-Inventur Verknüpfung klären).

## 8. Angebots-PDF / -Dokumente

- **(a) Zweck:** Erzeugung des ausgebbaren Angebotsdokuments. **Produktivweg:** Das PDF/Dokument wird **client-seitig im Angebots-Editor gerendert** (Canvas + `sections`) und via `POST /offers/save-document` als JSON (`sections`, `canvas_images`, Branding, Summen) in `offer_details` persistiert; Druck-/Print-Metadaten in `offer_pdf_prints`. Vorlagen-Speicherung analog in `offer_templates`.
- **(b) Controller/Routen:** `OfferController::saveDocument` (`offers.save-document`); `OfferDocumentController::load`/`biography` (Dokument laden). Print-Metadaten via `offer_pdf_prints` (in OfferController referenziert). Serverseitige dompdf-Vorlage: `resources/views/admin/offer/pdf_export.blade.php` (dompdf-HTML/CSS mit `@foreach($sections…)`).
- **(c) Kern-Tabellen:** `offer_details` (Dokument-JSON), `offer_pdf_prints` (Druck-Historie), `offer_folder_attachments` (Anhänge/AGB), `offer_templates` (Vorlagen).
- **(d) Größe:** klein (Speicher-Logik), Rendering im ~25k-Z.-Editor-Blade.
- **(e) Status:** **Bruch/Inkonsistenz:** Route `POST /offers/generate-pdf` → `OfferController@generatePdf` ist registriert (web.php ~3508), **die Methode `generatePdf` existiert im `OfferController` NICHT** (grep negativ) → toter/kaputter Endpunkt. `pdf_export.blade.php` (barryvdh/dompdf, `^3.1` in composer.json) hat keinen aktiven Aufrufer in dieser Zone (die einzige echte `Pdf::loadView`-Nutzung liegt in `DailyReportController`, nicht Angebot). `puppeteer` ist in `package.json`, aber ohne Fundstelle im App-Code (kein Server-Rendering-Aufruf gefunden). → Effektiv läuft die PDF-Erzeugung **im Browser**, Backend speichert nur das JSON-Dokument.

---

## Braucht eigene Detail-Inventur

1. **Master-Set-Subsystem (HÖCHSTE Priorität):** ~6.700 Z. Controller + `MasterSetApiController` (eigene env-Auth) + ~15k-Z.-Blade + Cart + Groups + Duplicate-/Hydrate-/Sync-Logik. Eigene Tiefen-Inventur zwingend (Komponenten-Hierarchie `parent_id`, Lohn-/Task-/Checklisten-Sync, Preisableitung).
2. **Angebots-Editor `config.blade.php` (~25.000 Z.):** Frontend-Kalkulation, Rabatt-/Positions-Handling, Canvas/Branding, Präsenz — plus die parallelen `config.blade copy*.php` / `Old/`-Duplikate (Alt-Last inventarisieren, tote Kopien identifizieren).
3. **Kalkulations-Kette Costing-Set → Master-Set → Angebotssumme:** `CostingSet`(+Rollen) → `taskCostingPayload` (Rate-Modi/Fallbacks) → `calculateOfferSections` (server-autoritativ). Formel-/Preislogik end-to-end verifizieren (inkl. `is_active`/`default_active`/`vk_rule`-Spaltennamen).
4. **`offer_details`-JSON-Schema (`sections`/`canvas_images`):** zentrales, un-normalisiertes Positions-/Dokumentmodell — Feldstruktur, Rabatt-/Aktiv-Flags, Material-History dokumentieren.
5. **PDF-Pfad-Bereinigung:** verwaiste Route `offers.generate-pdf` (Methode fehlt), ungenutzte `pdf_export.blade.php` (dompdf) und deklariertes, aber ungenutztes `puppeteer` — klären, welcher Weg der Soll-Weg ist.

## Belege

- **Controller:** `app/Http/Controllers/Customer/Offer/` (OfferController 2951 Z., OfferFolderController 3810 Z., OfferWizardController 1921 Z., OfferDetailsController 557 Z., OfferDocumentController, OfferTemplateController 901 Z., OfferTemplatePickerController 528 Z., OfferPageLibraryController 586 Z., OfferRoofLayoutConfigurationController 255 Z.); `app/Http/Controllers/Product/MasterSet/` (MasterSetController 2464 Z., MasterSetCartController 1053 Z., MasterSetGroupController 538 Z.); `app/Http/Controllers/Api/MasterSetApiController.php` (1600 Z.); `CostingSetController.php` (446 Z.), `CostingSetRoleController.php`; `Product/ProductFormulaController.php` (259 Z.); `EconomicCalculationController.php`, `EconomicAssumptionController.php`, `CustomerOfferWPController.php`; Legacy `Old/OfferConfigController.php`, `Old/ProductMasterSetController.php`, `Old/OfferCoverController.php`, `Old/OfferGreetingController.php`.
- **Kalkulations-/PDF-Kernstellen:** `OfferController::saveDocument`/`processOffer`/`calculateOfferSections`/`offerNormalizeSections` (~Z. 1681–2110, 1947); `OfferController` enthält KEIN `generatePdf` (grep negativ); `MasterSetController::taskCostingPayload` (~Z. 2147ff), `saveCostingSettings`.
- **Routen:** `routes/web.php` — Wizard ~3265–3294, Roof-Layout ~3301–3306, Supplier ~3310–3348, Templates ~3354–3375, Offer-Kern ~3386–3540, generate-pdf ~3508, save-document ~3512, Costing-Sets ~1483–1492 (+`options`/`{costingSet}`), Economic ~1313–1318, Product-Formula ~2875–2887; `routes/api.php` — `secure/master-sets` ~202–220, Planner Master-Sets ~326–353; `routes/channels.php` — `offer-folder.{folderId}` ~139–156.
- **Migrationen:** `create_offers_table` (2024_12_09), `create_offer_folders_table` (2025_08_27), `create_offer_details_table` (2026_03_09) + ~13 Nachträge (`extend_*`, `add_*_to_offer_details/folders`, `create_offer_kanban_stages`, `create_offer_folder_activities`, `create_offer_delete_logs`, `create_offer_page_library_items`); `create_master_sets_table` (2026_01_05) + `master_set_components/labors/tasks/task_labors/checklists/groups/carts` + ~10 `add_*/create_*`-Nachträge; `create_costing_sets_table` + `costing_set_roles` + `add_default_active`/`add_vk_rule`/`add_costing_set_to_master_sets`; `create_product_formulas_table` (2025_06_02); `create_economic_assumptions/calculations`; `create_customer_offer_w_p_s_table`.
- **Blades:** aktiver Editor `resources/views/admin/offer/configuration/offer/config.blade.php` (~25.064 Z.) + Duplikate `config.blade copy*.php` / `Old/` / `admin/offer/old/`; `wizard-smart.blade.php` (~2.191 Z.); `resources/views/admin/offer/pdf_export.blade.php` (dompdf-Vorlage, ungenutzt in Zone); `resources/views/admin/master_sets/index.blade.php` (~15.270 Z.), `editor.blade.php` (~6.702 Z.) + `index.blade copy*.php`.
- **Sidebar:** `resources/views/admin/layouts/sidebar.blade.php` — „Neues Angebot" (Z. ~714 Umfeld, `offers/wizard`), „Vorlagen" (`offer-templates.index`), „Master-Sets" (Z. ~1122, `admin.master_sets.index`), „Checklisten-Formulare" (Z. ~1168, `product.formula.index`), „Kalkulationssätze" (Z. ~1337, `admin.costing_sets.index`).
- **PDF-Engine:** `composer.json` → `barryvdh/laravel-dompdf: ^3.1`; `package.json` → `puppeteer: ^24.39.1` (im App-Code ohne Fundstelle). Einzige aktive `Pdf::loadView`-Nutzung: `Report/DailyReportController.php` (Z. 16, 3756) — außerhalb dieser Zone.
