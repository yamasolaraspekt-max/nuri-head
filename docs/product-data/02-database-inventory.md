# 02 · Datenbankinventur — Produkt, Artikel, Lieferant, Preis, Merkmale, Medien

> **Abgrenzung:** Dieses Dokument inventarisiert den Produkt- und Beschaffungsdatenbereich.
> Die CRM-, Auftrags-, Rechnungs- und Prozesstabellen sind in `04-crm-erp-capability-matrix.md` erfasst.

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
>
> **NACHTRAG 2026-08-01 (nachtraeglich gepruefte Stelle, Abschnitt 2 Frage 1):** Die dort
> als „vier Nummernfelder" erfasste Spalte `sku` wurde gesondert nachgeprueft. Ergebnis:
> `sku` traegt an drei Orten drei verschiedene Bedeutungen, und `HeatpumpSeeder.php:86`
> ist ein **sechster** Identitaets-Schreibpfad. Zugleich existiert eine im Code
> dokumentierte Absicht (`SupplierProductImportService.php:251-255`): `article_no` =
> Hersteller-Artikelnummer, `sku` = Lieferanten-Artikelnummer. Vollstaendige Einordnung
> in `10-target-domain-model.md` Abschnitt 4a. Der Text unten bleibt als Beleg unveraendert.

---

**Basis:** `/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket`
Alle Migrationsdateien im Folgenden liegen unter `/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/database/migrations/`, alle Models unter `/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Models/`.
Methodik: `Schema::create`-Scan über alle 612 Migrationen, Model-Abgleich, Schreibpfad-Scan (`::create|updateOrCreate|firstOrCreate|insert|upsert` + `DB::table()->insert/update`) über `app/`, `routes/`, `database/seeders/`, Routen-Abgleich in `routes/web.php`. Keine DB-Zugriffe, keine Zeilenzahlen aus der Datenbank.

---

## 1 · Matrix

Legende Datenquelle: **M**=manuell/UI · **I**=Import (CSV/Seeder/IDS) · **A**=API-Konnektor · **?**=unbekannt
Legende Wiederverwendbarkeit: **U**=unverändert · **E**=erweitern · **MIG**=migrieren · **X**=ersetzen

### 1.1 Kern Artikel / Produkt

| Tabelle | Migration | Zweck | PK | wichtigste FKs | Quelle | Hist. | Audit | IDS | OMD | Wiederverw. | Risiko |
|---|---|---|---|---|---|---|---|---|---|---|---|
| `products` | `2023_06_22_085602_create_products_table.php` (+5 ALTER) | Zentrale Artikelstammtabelle | `id` | `brand_id`→brands, `article_group`→article_groups, `sub_article`→sub_article_groups, `measure_unit`→measures | M+I+A | nein (nur `product_histories`) | ja (`created_by`/`updated_by`, 2026_05_05) | ja | ja | **E** | **hoch** — kein Unique auf `article_no`/`ean`/`sku`; Preis- und Technikspalten direkt am Stamm |
| `article_groups` | `2023_06_22_085600_...` | Gewerk/Warengruppe (auch als „Gewerk" in Lead/Offer missbraucht) | `id` | — | M | nein | nein | ja | ja | **E** | **hoch** — dient gleichzeitig als Warengruppe UND als Gewerk-/Prozessanker (`heat_pumps.product_id`→`article_groups`) |
| `sub_article_groups` | `2023_06_22_085601_...` | Untergruppe | `id` | `article_group_id` | M | nein | nein | ja | ja | **U** | niedrig |
| `measures` | `2023_06_22_085601_create_measures_table.php` | Mengeneinheit (nur Freitext `measure`) | `id` | — | M+I (`Measure::firstOrCreate` in IdsController) | nein | nein | ja | ja | **X** | **hoch** — keine Norm-Codes (kein UN/ECE, kein ISO), Einheiten wachsen unkontrolliert per Import |
| `brands` | `2023_06_21_101726_...` | Hersteller UND Lieferant in einer Tabelle (`type` default `brand`, Import setzt `manufacturer`) | `id` | — | M+I | nein | nein | ja | ja | **E** | mittel — Hersteller/Marke nicht von Lieferant getrennt; `status` als String |
| `product_types` | `2023_06_22_085602_create_product_types_table.php` | Artikel-„Typ"/Variantenzeile mit eigenen Preisen | `id` | `product_id` (**untypisiert `integer`, kein FK**), `distributor_id` | M | nein | nein | nein | nein | **X** | **hoch** — `ean`/`serial`/`purchase_price`/`tax` als `integer`; einzige Nutzung `ProductTypeController` |
| `product_descriptions` | `2023_08_03_100317_...` | Freitext-Merkmal je Produkt (`field`/`description`) — Key-Value | `id` | `product_id` | M | nein | nein | nein | nein | **MIG** | mittel — Quasi-EAV ohne Einheit/Datentyp, `description` nur `string(255)` |
| `product_histories` | `2026_05_05_093331_...` | Änderungshistorie products (JSON diff) | `id` | `product_id`, `changed_by` | automatisch | **ja** | ja | – | – | **U** | niedrig — einziges echtes Historisierungsobjekt im Bereich |
| `product_installation_cases` | `2023_08_10_102258_...` | Montagefälle je Produkt | `id` | `product_id` | M | nein | nein | nein | nein | **U** | niedrig |
| `product_positions` | `2024_07_14_012639_...` | Zuständige Positionen je Artikelgruppe (JSON `position_ids`) | `id` | `article_group_id`, `department_id`, `service_id` | M | nein | nein | – | – | **U** | niedrig |
| `product_formulas` | `2025_06_02_083017_...` | Formular-/Formel-Definition (JSON `fields`), `version`-Spalte | `id` | **`product_id`→`article_groups` (Fehl-FK)** | M | teilweise (`version`, SoftDeletes) | ja | – | – | **E** | **hoch** — FK zeigt auf falsche Tabelle; Spaltenname suggeriert `products` |
| `product_formula_routing_rules` | `2026_07_08_130000_...` | Routing Formular→Gewerk/Objekttyp | `id` | `product_formula_id`, `article_group_id`, `lead_product_list_id` | M | nein | nein | – | – | **U** | niedrig |
| `product_favorite_lists` | `2025_11_27_082341_...` | Favoritenlisten je Mitarbeiter | `id` | `employee_id` | M | nein | nein | – | – | **U** | niedrig |
| `product_favorite_list_items` | `2025_11_27_082408_...` | Listeninhalt | `id` | `product_favorite_list_id`, `product_id`, `employee_id` | M | nein | nein | – | – | **U** | niedrig |
| `stamp_article_lists` | `2025_11_27_082453_...` | „Stammartikel"-Listen je Mitarbeiter | `id` | `employee_id` | M | nein | nein | – | – | **U** | niedrig |
| `stamp_article_list_items` | `2025_11_27_082515_...` | Listeninhalt (`stamp_article_id`→**products**) | `id` | `stamp_article_id`→products, `stamp_article_list_id` | M | nein | nein | – | – | **U** | niedrig — irreführender Spaltenname |
| `activity_articles` | `2025_04_08_102627_...` | n:m Phasen-Aktivität ↔ Artikel | `id` | `activity_id`→phase_activities, `article_id`→products | M | nein | nein | – | – | **U** | niedrig |

### 1.2 Lieferant · Preis · Konditionen

| Tabelle | Migration | Zweck | PK | wichtigste FKs | Quelle | Hist. | Audit | IDS | OMD | Wiederverw. | Risiko |
|---|---|---|---|---|---|---|---|---|---|---|---|
| `distributors` | `2023_07_26_082336_...` | Lieferant/Großhändler | `id` | — | M+I (`firstOrCreate`) | nein | nein | ja | ja | **E** | mittel — keine Lieferantennummer, keine Kundennummer beim Lieferanten |
| `distributor_product` | `2023_10_16_131323_...` | n:m Produkt↔Lieferant (reine Zuordnung) | `id` | `product_id`, `distributor_id` | I | nein | nein | ja | ja | **X** | mittel — redundant zu `distributor_prices`, kein Unique |
| `distributor_prices` | `2023_10_16_141346_...` | **Preisträger:** UVP/EK/Rabatt je Lieferant+Produkt, Lieferanten-Artikelnr | `id` | `distributor_id`, `product_id`, `discount_group_id` | I+A | **nein** (`price_date` überschreibend) | nein | ja | ja | **MIG** | **sehr hoch** — kein Gültigkeitszeitraum, keine Historie, kein Unique, keine Währung, keine Preisbezugsmenge |
| `discount_groups` | `2023_08_08_125245_...` | Rabattgruppe | `id` | — | M | nein | nein | ja | ja | **X** | **hoch** — `discount` als **`integer`** → keine Nachkommastellen, keine Staffel, kein Gültigkeitsdatum |
| `taxes` | `2023_08_08_121926_...` | Steuersätze | `id` | — | M | nein | nein | – | – | **X** | mittel — `tax` als **String**, kein Land, kein Gültigkeitszeitraum |
| `supplier_connections` | `2026_05_26_084701_...` | Konnektor-Konfiguration (IDS/OCI/OMD/Datanorm), Credentials, `import_config` JSON | `id` | `distributor_id` | M | SoftDeletes | teilweise (`last_test_*`) | **ja** | **ja** | **U** | mittel — Credentials als `text` (Verschlüsselung nur im Model-Cast prüfbar) |
| `supplier_connection_mappings` | `2026_05_26_084702_...` | Feldmapping Quelle→Zieltabelle/-feld | `id` | `supplier_connection_id` | M | nein | nein | ja | ja | **U** | niedrig |
| `supplier_import_logs` | `2026_05_26_084703_...` | Importlauf-Protokoll | `id` | `supplier_connection_id` | automatisch | ja (Log) | ja | ja | ja | **U** | niedrig |
| `supplier_article_map` | `2026_07_04_140007_...` | **Neutraler Lieferantenartikel-Schlüssel** (`hersteller`+`herst_artikelnr`+`channel`) | `id` | `distributor_id`, `product_id`, `accessory_id` | A (nur IDS aktiv) | nein | teilweise (`last_synced_at`) | **ja** | **ja (Stub)** | **U** | niedrig — konzeptionell der beste Baustein im Bereich; nur ein Kanal befüllt |
| `imported_ids_items` | `2025_12_04_191014_...` + `2025_12_04_223128_add_product_id...` | IDS/GC-Online Warenkorb-Rohzeilen (Staging) | `id` | `product_id` (nachträglich) | A | nein | nein | **ja** | nein | **E** | mittel — kein `distributor_id`, `batch_id` als loser String, keine FK zu `supplier_import_logs` |

### 1.3 Technische Merkmale (Specs)

| Tabelle | Migration | Zweck | PK | wichtigste FKs | Quelle | Hist. | Audit | IDS | OMD | Wiederverw. | Risiko |
|---|---|---|---|---|---|---|---|---|---|---|---|
| `product_pv_module_specs` | `2026_07_04_150003_...` | PV-Modul Kennwerte (Voc/Isc/Pmpp/TK) | `id` | `product_id` (**kein FK-Constraint**) | I (Seeder/SpecImport) | via `import_batch_id` | nein | nein | nein | **E** | mittel — kein Model, nur `DB::table`; kein FK, kein Unique auf `product_id` |
| `product_heat_pump_specs` | `2026_07_04_150004_...` (+`..150005_add_kurve_semantik`) | WP-Kennwerte + `leistungskurve` JSON | `id` | `product_id` (kein FK) | I | via `import_batch_id` | nein | nein | nein | **E** | mittel — dito; Kurve als JSON ohne Schema-Zwang |
| `product_radiator_specs` | `2026_07_04_140001_...` | Heizkörper EN-442-Kennwerte | `id` | `product_id`→products (nullOnDelete) | I (Seeder) | `imported_from` | nein | nein | nein | **U** | niedrig — sauberste Spec-Tabelle (Quelle, Norm-Bedingung, Exponent) |
| `product_p_v_s` | `2024_01_17_091804_...` | Alte PV-Modul-Kennwerte (≈45 Spalten) | `id` | `product_id`, `article_group_id` | M | nein | nein | nein | nein | **X** | **hoch** — inhaltlich redundant zu `product_pv_module_specs`, andere Namen/Einheiten |
| `product_w_p_s` | `2024_02_01_090242_...` | WP-Leistungspunkte (temp/min_kw/max_kw) | `id` | `product_id` | M | nein | nein | nein | nein | **X** | **hoch** — redundant zu `product_heat_pump_specs.leistungskurve` |
| `inverters` | `2024_06_26_135513_...` | Wechselrichter-Kennwerte | `id` | `product_id`, `article_group_id` | M + SpecImport | via `import_batch_id` | nein | nein | nein | **E** | mittel — Doppelrolle: eigenständige Gerätetabelle UND Spec-Ziel |
| `batteries` | `2024_06_26_073120_...` | Batterie-Kennwerte | `id` | `product_id`, `article_group_id` | M + SpecImport | via `import_batch_id` | nein | nein | nein | **E** | mittel — dito |
| `battery_systems` | `2024_06_26_081846_...` | Speichersystem (ESS) | `id` | `product_id`, `article_group_id` | M | nein | nein | nein | nein | **U** | mittel — nur `BatterySystemController` |
| `battery_inverters` | `2024_06_26_084433_...` | Batteriewechselrichter | `id` | `product_id`, `article_group_id` | M | nein | nein | nein | nein | **U** | mittel |
| `power_optimizers` | `2024_06_26_101947_...` | Leistungsoptimierer | `id` | `product_id`, `article_group_id` | M | nein | nein | nein | nein | **U** | mittel |
| `backup_generators` | `2024_06_26_140622_...` | Notstromerzeuger | `id` | `product_id`, `article_group_id` | M | nein | nein | nein | nein | **U** | mittel |
| `electric_vehicles` | `2024_06_26_100120_...` | E-Fahrzeuge | `id` | `product_id`, `article_group_id` | M | nein | nein | nein | nein | **U** | mittel |
| `radiators` | `2024_01_18_173007_...` | „Heizkörper" — enthält aber **Wechselrichter-Felder** (dt. Spaltennamen) | `id` | `product_id` (`integer`, kein FK) | M | nein | nein | nein | nein | **X** | **hoch** — Tabellenname ≠ Inhalt; kollidiert fachlich mit `product_radiator_specs` |
| `spec_import_batches` | `2026_07_05_150009_...` | Import-Batch-Kopf (UUID-PK) für Spec-Import + Rollback | `uuid` | — | I | ja (Batch) | ja | nein | nein | **U** | niedrig — **kein Model**, nur `DB::table` |
| `materials` | `2026_07_05_170001_...` | Baustoff-Kennwerte (λ) für Bauphysik | `id` | — | I (Seeder) | `imported_from`, `verifikations_status` | nein | nein | nein | **U** | niedrig — nicht Artikelstamm, sondern Physik-Katalog |
| `accessories` | `2026_07_04_140003_...` | Armaturen/Zubehör mit **eigener** Hersteller-Artikelnr | `id` | `accessory_category_id`, `product_id` | I (Seeder) | nein | nein | ja (via map) | ja (via map) | **MIG** | **hoch** — Parallel-Artikelstamm neben `products`, Unique auf (hersteller, herst_artikelnr) — anders als `products` |
| `accessory_categories` | `2026_07_04_140002_...` | Zubehörkategorien (`code` unique) | `id` | — | I | nein | nein | – | – | **U** | niedrig |
| `valve_insert_compatibility` | `2026_07_04_140006_...` | Kompatibilitätsregeln Ventileinsatz/Adapter | `id` | `einsatz_accessory_id`, `adapter_accessory_id` | I | Baujahr von/bis | nein | – | – | **U** | niedrig — einzige Tabelle mit echter Zeitgültigkeit |
| `radiator_connection_factors` | `2026_07_04_140008_...` | Anschlusskorrekturfaktoren | `id` | — | I | nein | nein | – | – | **U** | **toter Bestand** — Model existiert, **kein** Lese- und **kein** Schreibpfad |
| `tiles` | `2024_05_28_141715_...` | Dachziegel-Bilder (name/model/image) | `id` | — | M | nein | nein | nein | nein | **X** | niedrig — Mini-Katalog außerhalb `products` |

### 1.4 Sets · Stücklisten · Bundles

| Tabelle | Migration | Zweck | PK | wichtigste FKs | Quelle | Hist. | Audit | IDS | OMD | Wiederverw. | Risiko |
|---|---|---|---|---|---|---|---|---|---|---|---|
| `master_sets` | `2026_01_05_103819_...` | **Aktives** Set/Stücklisten-Kopfobjekt | `id` | `article_group_id`, `responsible_department_position_id` | M | SoftDeletes | nein | – | – | **E** | mittel — keine Version, kein Gültigkeitsdatum |
| `master_set_components` | `2026_01_05_103845_...` (+4 ALTER) | Positionen (self-ref `parent_id`), Preise, Marge, Skonto | `id` | `master_set_id`, `parent_id`, `product_id`, `distributor_id`, `distributor_price_id` | M | nein | nein | – | – | **E** | **hoch** — Preis wird in die Position kopiert; keine Versionierung, kein Regelwerk |
| `master_set_component_descriptions` | `2026_02_10_065251_...` | Textvarianten je Position (Quill-Delta + HTML) | `id` | `master_set_component_id` | M | `context`/`sort_order` | nein | – | – | **U** | niedrig |
| `master_set_groups` | `2026_02_10_110420_...` | Set-Gruppierung | `id` | `article_group_id` | M | SoftDeletes | nein | – | – | **U** | niedrig |
| `master_set_group_master_set` | `2026_02_10_110447_...` | n:m Gruppe↔Set (**kein `id`**, Unique-Paar) | zusammengesetzt | beide | M | nein | nein | – | – | **U** | niedrig |
| `master_set_cart_items` | `2026_03_10_123814_...` | Angebots-Warenkorbzeile aus Set (Snapshot) | `id` | `cart_id`, `section_id`, `parent_id`, `product_id`, `distributor_id`, `distributor_price_id` | M | Snapshot | nein | – | – | **U** | mittel — `skonto` hier `decimal(12,2)`, in `master_set_components` `decimal(5,2)` |
| `product_master_sets` | `2024_01_29_110442_...` | **Legacy** Set-Kopf | `id` | `article_group`, `sub_article`, `phase_id` | M | nein | nein | – | – | **X** | **hoch** — nur noch gelesen (`OfferDetailsController.php:294`), Schreib-Controller unter `app/Http/Controllers/Old/` **ohne Route** |
| `product_sub_sets` | `2024_09_18_110916_...` | Legacy Set-Position | `id` | `master_set_id`, `product_id`, `main_product` | M | nein | nein | – | – | **X** | hoch — `double(10,2)` für Preise |
| `add_product_to_sets` | `2024_09_18_110144_...` | Legacy Hauptprodukt im Set | `id` | `master_set_id`, `product_id`, `distributor_id` | M | nein | nein | – | – | **X** | hoch — Legacy |
| `add_image_to_sets` | `2024_03_12_104203_...` | Legacy Set-Bilder | `id` | `master_set_id`, `product_id` | M | nein | nein | – | – | **X** | hoch — Legacy |
| `product_set_descriptions` | `2024_01_30_102323_...` | Legacy Set-Texte | `id` | `master_set_id` | ? | nein | nein | – | – | **X** | **toter Bestand** — kein Model, kein Schreib-/Lesepfad gefunden |
| `group_sets` | `2024_02_05_102034_...` | Legacy Gewerk-Set mit Material-/Lohnanteilen | `id` | `set_id`→product_master_sets | ? | nein | nein | – | – | **X** | **toter Bestand** — nur `Old/GroupSetController.php`, nicht geroutet |
| `group_set_product_master_set` | `2024_02_05_124312_...` | Legacy n:m | `id` | beide | ? | nein | nein | – | – | **X** | toter Bestand |

### 1.5 Bestand · Lager

| Tabelle | Migration | Zweck | PK | wichtigste FKs | Quelle | Hist. | Audit | IDS | OMD | Wiederverw. | Risiko |
|---|---|---|---|---|---|---|---|---|---|---|---|
| `inventories` | `2023_08_14_055930_...` | Lagerbestand je Produkt (Ort/Regal/Reihe/Menge) | `id` | `product_id`, `responsible_id`, `add_by`/`edit_by`/`delete_by`→employees | M | nein | ja (add/edit/delete by+date) | nein | nein | **E** | mittel — `quantity` als `integer`; eigene `article_no`/`ean`-Kopien |
| `inventory_histories` | `2026_03_26_102458_...` | Bestandsbewegungen (before/used/after) | `id` | `inventory_id`, `product_id`, `customer_id`, `used_by` | automatisch | **ja** | ja | – | – | **U** | niedrig |
| `inventory_request_outs` | `2023_08_23_083150_...` | Materialanforderung | `id` | `product_id`, `responsible_id`, `requester_id` | M | nein | ja (Strings) | – | – | **U** | niedrig — Audit-Spalten als `string` statt FK |

### 1.6 Medien · Dokumente

| Tabelle | Migration | Zweck | PK | wichtigste FKs | Quelle | Hist. | Audit | IDS | OMD | Wiederverw. | Risiko |
|---|---|---|---|---|---|---|---|---|---|---|---|
| `product_images` | `2023_08_09_084555_...` | Produktbilder (`name`, `image`) | `id` | `product_id` | M+I (URL-Download) | nein | nein | nein | nein | **X** | **sehr hoch** — kein Typcode, kein Hash, keine Sprache, keine Herkunft, keine Sortierung, kein Unique; Model-`$fillable` passt nicht zur Spalte (s. §6) |
| `product_documents` | `2023_08_11_062919_...` | Produktdokumente (`title`, `document`) | `id` | `product_id` | M | nein | nein | nein | nein | **X** | **sehr hoch** — kein Dokumenttyp (Datenblatt/CE/Zeichnung), kein Hash, keine Sprache, kein Gültigkeitsdatum, keine Version |
| `images` | `2025_01_24_093117_...` | Universal-Bildtabelle (Lead/Phase/Task) mit FK auf `article_groups` | `id` | `customer_id`→new_leads, `article_group`, `alternative_id`, `phase_id`, `task_id`, `sub_task_id` | M | SoftDeletes | ja (`created_by`/`update_by`) | – | – | **U** | mittel — 8 FKs, Anker nur per Konvention; nicht produktbezogen |
| `image_categories` | `2024_05_27_110457_...` | Bildkategorien je Artikelgruppe | `id` | `article_group_id` | M | nein | nein | – | – | **U** | niedrig |
| `customer_product_info_media` | `2026_02_04_110832_...` | Medien zu installierten Kundengeräten — **mit** `disk`/`path`/`mime`/`size`/`type`/`sort_order` | `id` | `customer_product_info_id`, `uploaded_by` | M | nein | ja (`uploaded_by`) | – | – | **U** | niedrig — **fachlich das beste Medienmodell im Repo**, nur nicht am Produktstamm |

### 1.7 Kunden-/Objektbezug zum Artikel

| Tabelle | Migration | Zweck | PK | wichtigste FKs | Quelle | Hist. | Audit | IDS | OMD | Wiederverw. | Risiko |
|---|---|---|---|---|---|---|---|---|---|---|---|
| `customer_product` | `2023_06_22_085603_...` | n:m Kunde↔Produkt | `id` | `customer_id`, `product_id` | ? | nein | nein | – | – | **X** | **toter Bestand** — Model `CustomerProduct.php:13` zeigt auf `customer_products` (Tabelle existiert nicht) |
| `customer_product_lists` | `2024_06_14_134917_...` | Produktliste je Kunde | `id` | `customer_id`, `product_id` | M | nein | nein | – | – | **U** | niedrig |
| `customer_product_infos` | `2025_06_08_220427_...` | Installierte Geräte beim Kunden (Seriennr, Garantie) | `id` | `products`→products, **`product_id`→`article_groups`** | M | Garantie-/Gewährleistungsdaten | nein | – | – | **E** | **hoch** — zwei Produktspalten, `product_id` zeigt auf Gewerk statt Produkt |
| `hausplaner_catalog_items` | `2026_07_16_211128_create_hausplaner_foundation_tables.php` | Geometrie-/Symbolkatalog (Fenster, Tür, Heizkörper, WP) mit `manufacturer`/`model`/`dimensions` JSON | `id` | — (`spec_ref` opak) | M/I | `schema_version` | nein | nein | nein | **E** | **hoch** — dritter Herstellerkatalog ohne FK zu `products`/`brands` |
| `hausplaner_configurator_packages` | `2026_07_26_180000_...` | Konfigurator-Pakete (`paket` JSON) | `id` | `user_id`, `alternative_id` | M | `schema_version` | ja (`user_id`) | – | – | **U** | mittel — Produktdaten in JSON eingefroren |

---

## 2 · Die neun Fragen

### Frage 1 — Artikelidentität

**BELEGT**
- `products` hat vier Nummernfelder: `article_no` (`.../database/migrations/2023_06_22_085602_create_products_table.php:17`), `ean` (`:18`), `sku` (`.../2025_08_26_075553_add_fields_for_katalog_to_products_table.php`, `after('article_no')`) und `model` (`:25`).
- Die Semantik ist **nur im Importservice dokumentiert, nicht im Schema**: `/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Services/Suppliers/SupplierProductImportService.php:251-252` — `// products.article_no = Hersteller-Artikelnummer`; `:255` — `'sku' => $productData['sku'] ?? $distributorArticleNo` (SKU = Lieferanten-Artikelnr, sofern keine eigene übergeben).
- Die **Lieferanten-Artikelnummer** liegt fachlich in `distributor_prices.article_no` (`.../2023_10_16_141346_create_distributor_prices_table.php:20`), bestätigt in `/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Http/Controllers/Product/ProductDifferenceController.php:193-196`: `'manufacturer_article_no' => $product->article_no` vs. `'supplier_article_no' => $price?->article_no`.
- Sauber getrennt ist die Identität **nur** in `supplier_article_map`: `hersteller` + `herst_artikelnr` + `lieferanten_artikelnr` mit Unique-Key `(hersteller, herst_artikelnr, supplier_channel)` (`.../2026_07_04_140007_create_supplier_article_map_table.php:18-30`).
- Ein zweiter, unabhängiger Identitätsraum existiert in `accessories`: `hersteller` + `herst_artikelnr` mit eigenem Unique (`.../2026_07_04_140003_create_accessories_table.php`).
- **Auf `products` existiert kein einziger Unique- oder Index-Constraint** auf `article_no`, `ean` oder `sku` — Prüfung: kein Treffer für `unique`/`index` in irgendeiner `products`-Migration.
- GTIN/EAN existiert dreifach mit unterschiedlichem Typ: `products.ean` = `string`, `product_types.ean` = **`integer`** (`.../2023_06_22_085602_create_product_types_table.php`), `inventories.ean` = `string`.

**BEWERTUNG**
Es ist *nicht* alles ein `article_no` — aber die Trennung ist **implizit und nur teilweise durchgehalten**. `article_no` bedeutet je nach Schreibpfad Hersteller-Artikelnummer (Importservice), IDS-Artikelnummer (`IdsController.php:225-227`) oder freie CSV-Spalte (`ProductImportController.php:119`). Ohne Unique und ohne Herstellerbindung ist `products.article_no` kein Identifikator, sondern ein Suchtext.

**ANNAHME**
`model` wird im Spec-Import als eigentliche Modellbezeichnung genutzt und bildet dort zusammen mit `brand_id` die faktische Identität (`.../app/Services/Spec/SpecImportService.php:352`).

**OFFEN**
Ob `sku` in Bestandsdaten überhaupt gefüllt ist, ist ohne DB-Zugriff nicht feststellbar. Ob `products.article_no` in Bestandsdaten Duplikate enthält, ebenfalls nicht.

---

### Frage 2 — Wie wird heute zugeordnet/gematcht?

Es existieren **vier voneinander unabhängige Matching-Verfahren**:

**BELEGT — (a) Konnektor-Import (IDS/OCI)**
`/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Services/Suppliers/SupplierProductImportService.php:209-239`, Methode `resolveProduct()`, Kaskade:
1. `Product::where('ean', $productData['ean'])->first()` (`:212`)
2. `Product::where('article_no', $productData['article_no'])->first()`, sofern ≠ `'Not filled'` (`:219-220`)
3. `DistributorPrice::where('distributor_id',…)->where('article_no',…)->with('product')` (`:227-231`)

Alles **exakte Gleichheit ohne Normalisierung** — kein `LOWER`, kein `TRIM`, keine Führungsnull-Behandlung, keine Herstellerbindung. Punkt 2 kann branchenübergreifend falsch treffen, weil `article_no` global nicht eindeutig ist.

**BELEGT — (b) Neutraler Lieferantenmap (W2)**
`/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Services/Suppliers/Mappers/IdsMapper.php:43-58` — `SupplierArticleMap::updateOrCreate(['hersteller'=>…, 'herst_artikelnr'=>…, 'supplier_channel'=>'ids'], …)`. Hersteller kommt aus `$p->brand?->name` (`:34`); fehlt Marke oder Produkt, wird **nicht** geschrieben, sondern geskippt und gezählt (`:28-39`, `:61-74`). Kanalauflösung in `MapperRegistry.php:24-29` (`'ids','oci' => IdsMapper`). **`OmdMapper.php:19` und `DatanormMapper.php:19` geben `null` zurück — Stubs ohne Wirkung.**

**BELEGT — (c) Spec-/Katalogimport**
`/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Services/Spec/SpecImportService.php:340-352`, `findExisting()`: `brands.name` **exakt** → `brand_id`, dann `products.where('brand_id')->where('model', $modell)->first()`. Marke wird bei Fehlen angelegt (`:355-360`). Also: Identität = **(Marke, Modell)**, nicht Artikelnummer. Downgrade-Schutz gegen Überschreiben verifizierter Datensätze in `:177-182`.

**BELEGT — (d) IDS-Staging → Produkt („Promote")**
`/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Http/Controllers/Product/IDS/gconline/IdsController.php:225-227`: `Product::firstOrCreate(['article_no' => $item->article_no], […])` — **allein über `article_no`, ohne Hersteller**. Anschließend werden alle weiteren Staging-Zeilen mit derselben `article_no` demselben Produkt zugeschlagen: `:286-288` — `ImportedIdsItem::where('article_no', $item->article_no)->whereNull('product_id')->update(['product_id'=>$product->id])`.

**BELEGT — (e) CSV-Import**
`/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Http/Controllers/Product/ProductImportController.php:130-136`: erst `Product::where('article_no', $articleNo)`, dann **Fallback auf den Produktnamen** `Product::where('product', $productName)`. Namensmatching auf einem `string(255)`-Feld.

**BELEGT — kein `LOWER(TRIM(` im Produktbereich.** Alle Treffer für `LOWER(TRIM(` liegen in Lead-/Inquiry-/Controlling-Kontext (`app/Http/Controllers/Inquiry/InquiryController.php:249,266,446ff`; `app/Http/Controllers/Controlling/UmsaetzeController.php:29,52`). Die einzigen produktnahen `whereRaw`-Stellen normalisieren nur den Konnektortyp, nicht den Artikel: `app/Http/Controllers/Customer/Offer/OfferSupplierSearchController.php:31` und `app/Http/Controllers/Customer/Offer/OfferTemplateSupplierController.php:60`.

**BELEGT — (f) Technik↔Preis-Matching ist bereits als ungelöst dokumentiert**
`/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Services/Offer/WpKatalogMatchingService.php:135-137`: „Kein automatischer Preisanker möglich — Schnittmenge (gemeinsames product_id) = 0. Manuelle Zuordnung oder Katalog-Kuratierung erforderlich." und „Auto-Anker bleibt dennoch NEIN".

**BEWERTUNG**
Fünf Schreibpfade, fünf verschiedene Identitätsbegriffe: EAN → article_no → (Lieferant, article_no) → (Marke, Modell) → Produktname. Ohne Unique-Constraint auf `products` erzeugt jeder Pfad potentiell einen Zwilling, den ein anderer Pfad später wieder trifft. Das ist die strukturell schwerwiegendste Eigenschaft des Bereichs.

**OFFEN**
Ob `supplier_article_map` produktiv befüllt wird, hängt daran, ob `products.brand_id` gesetzt ist — Skips werden nur ins Log geschrieben (`IdsMapper.php:63-69`), nicht in eine Tabelle.

---

### Frage 3 — Preisstruktur

**BELEGT — Preisfelder und Genauigkeit**

| Tabelle | Feld | Typ | Bedeutung | Fundstelle |
|---|---|---|---|---|
| `distributor_prices` | `price` | `decimal(10,2)` | UVP | `2023_10_16_141346_...:25` |
| `distributor_prices` | `purchase_price` | `decimal(10,2)` | EK | `:26` |
| `distributor_prices` | `discount_price` | `decimal(10,2)` | Rabatt in € | `:23` |
| `distributor_prices` | `discount_percent` | `decimal(5,2)` | Rabatt in % | `:24` |
| `distributor_prices` | `price_date` | `date` | Preisstand | `:28` |
| `products` | `retail_price` | `decimal(12,2)` | VK am Stamm | `2025_11_10_101908_...` |
| `products` | `purchase_price` | `decimal(12,2)` | EK am Stamm | `2025_11_10_101908_...` |
| `products` | `vat_percent` | **zweimal definiert**: `float(4,1)` in `2025_08_26_075553_...`, dann `decimal(5,2)` in `2025_11_10_101908_...` | MwSt | beide Migrationen |
| `product_types` | `purchase_price`, `payment_method_price`, `plus_price`, `tax` | **`integer`** | Preise ohne Nachkomma | `2023_06_22_085602_create_product_types_table.php` |
| `supplier_article_map` | `ek_preis`, `vk_preis` | `decimal(12,2)` | Kanalpreise | `2026_07_04_140007_...:23-24` |
| `master_set_components` | `unit_price` | `decimal(10,2)` | VK Position | `2026_01_05_103845_...` |
| `master_set_components` | `purchase_price` / `margin` / `skonto` | `decimal(10,2)` / `decimal(5,2)` / `decimal(5,2)` | EK / Marge % / Skonto % | `2026_03_02_093129_add_commercial_fields_to_master_set_components.php:19-21` |
| `master_set_cart_items` | `unit_price`, `purchase_price`, `margin`, `skonto` | alle `decimal(12,2)` | Warenkorb-Snapshot | `2026_03_10_123814_...` |
| `product_master_sets` / `product_sub_sets` / `add_product_to_sets` / `group_sets` | `price`, `retail_price`, `purchase_price`, `total`, `material_price`, `employee_price` | **`double(10,1)` bzw. `double(10,2)`** | Legacy-Preise | jeweilige Create-Migration |
| `imported_ids_items` | `offer_price`, `net_price` `decimal(10,2)`, `vat` `decimal(5,2)`, `qty` `decimal(10,3)` | Staging | `2025_12_04_191014_...` |

**BELEGT — Gültigkeitszeiträume: nein.** Einziges Datumsfeld ist `distributor_prices.price_date` (`date`, ein Stichtag, kein Von/Bis). `DistributorPrice::updateOrCreate` überschreibt den Preis (z. B. `.../app/Http/Controllers/Product/ProductImportController.php:189-198`, `.../IDS/gconline/IdsController.php:264-280`) — **der Vorgängerpreis ist danach weg**.

**BELEGT — Staffeln: nein.** Keine Tabelle mit Mengenschwelle; Suche nach `staffel|scale_price|price_tier|tier_price|preisstaffel` liefert keinen Treffer in Migrationen oder Models.

**BELEGT — Preisarten: nur implizit über Spaltennamen** (UVP `price`, EK `purchase_price`, Rabattpreis `discount_price`). Kein Preisarten-Schlüssel, keine Preisliste.

**BELEGT — Währung: nein.** `currency`/`waehrung` existiert ausschließlich in `salaries` (`2024_01_08_150312_...:65`), `customer_maintenance_contracts` (`2025_11_28_104113_...:53`), `invoices` (`2023_07_19_100437_...:32`), `accounting_documents` (`2026_07_05_180002_...:47`), `chart_of_accounts.default_currency` (`2026_07_05_180001_...:41`). **In keiner Produkt- oder Preistabelle.**

**BELEGT — Preisbezugsmenge: nur als Freitext.** `products.price_unit` (`string`), `products.package_unit` (`string`), `master_set_components.price_unit` (`string` default `'stk'`, `2026_03_03_114421_...`), `master_set_components.vpe` (`decimal(10,2)`, `2026_03_02_093129_...`), `master_set_cart_items.vpe` (**`string`**). Kein numerischer Preisfaktor („Preis je 100 m").

**BEWERTUNG**
Der Preis wohnt an mindestens vier Orten gleichzeitig (`products`, `distributor_prices`, `supplier_article_map`, `master_set_*`) und wird bei jedem Set-/Warenkorbschritt kopiert statt referenziert. Ein Preisstand ist nicht rekonstruierbar; eine Nachkalkulation eines alten Angebots gegen den damaligen EK ist strukturell unmöglich.

---

### Frage 4 — Rabatte / Skonto / Konditionen

**BELEGT**
- Rabattgruppe: `discount_groups` (`2023_08_08_125245_...`) mit genau zwei Feldern: `discount_group` (`string`) und `discount` (**`integer`**). Keine Nachkommastellen, kein Lieferantenbezug, kein Gültigkeitsdatum, keine Staffel. Schreibpfad: `DiscountGroup::create` (1 Fundstelle, `app/Http/Controllers/Product/DiscountGroupController.php`).
- Rabatt je Preiszeile: `distributor_prices.discount_price` (€) und `discount_percent` (%) sowie `discount_group_id` (`2023_10_16_141346_...:20-27`). Drei parallele Rabattausdrücke ohne Vorrangregel im Schema.
- Skonto: **zwei Stellen mit unterschiedlicher Einheit** —
  `master_set_components.skonto` `decimal(5,2)` mit Kommentar `// %` (`2026_03_02_093129_...:20`)
  `master_set_cart_items.skonto` `decimal(12,2)` ohne Einheitshinweis (`2026_03_10_123814_...`).
- Zahlungsziel: `master_set_components.payment_terms` `integer` default 14 mit Kommentar `// Tage Ziel` (`2026_03_02_093129_...:21`); in `master_set_cart_items` dagegen **`string`**.
- Marge: `master_set_components.margin` `decimal(5,2)` default 50 (`2026_03_02_093129_...:19`).
- Steuer: `taxes` (`tax` als **String**, `class`, `remark` — `2023_08_08_121926_...`), zusätzlich `products.vat_percent`, zusätzlich `tax_codes` im Buchhaltungsfundament (`2026_07_05_180001_create_accounting_foundation_tables.php`).

**BEWERTUNG**
Es gibt **keine Konditionentabelle**. Rabatt, Skonto und Zahlungsziel sind Attribute einzelner Zeilen, nicht Vereinbarungen mit einem Lieferanten. Es gibt keine kundenseitigen Konditionen. Der Einheitenbruch bei `skonto` (% vs. absolut) zwischen `master_set_components` und `master_set_cart_items` ist ein konkreter Rechenfehler-Kandidat beim Übertrag Set → Warenkorb.

**OFFEN**
Wie `discount_price`, `discount_percent` und `discount_group_id` bei gleichzeitiger Befüllung priorisiert werden, ist im Schema nicht geregelt und wurde in den gelesenen Services nicht aufgelöst.

---

### Frage 5 — Technische Merkmale

**BELEGT — es existieren drei Generationen nebeneinander:**

*Generation 1 — je Gerätetyp eine breite Tabelle (2024):*
`inverters` (`2024_06_26_135513_...`), `batteries` (`2024_06_26_073120_...`), `battery_systems` (`2024_06_26_081846_...`), `battery_inverters` (`2024_06_26_084433_...`), `power_optimizers` (`2024_06_26_101947_...`), `backup_generators` (`2024_06_26_140622_...`), `electric_vehicles` (`2024_06_26_100120_...`), `radiators` (`2024_01_18_173007_...`), `product_p_v_s` (`2024_01_17_091804_...`, ≈45 Spalten), `product_w_p_s` (`2024_02_01_090242_...`). Alle mit `product_id` + `article_group_id`, jeweils genau ein Controller unter `app/Http/Controllers/Product/PV/`. **Keine Einheiten im Schema** — die Einheit steckt nur im Spaltennamen (`max_ac_power`, `range_wltp`) oder gar nicht.
**`radiators` enthält Wechselrichter-Felder** (`dc_nennleistung_kw`, `anzahl_mpp_tracker`, `max_eingangsspannung_v`) — der Tabellenname trägt nicht.

*Generation 2 — normierte Spec-Tabellen (2026-07):*
`product_pv_module_specs`, `product_heat_pump_specs`, `product_radiator_specs`. Hier **stehen die Einheiten in Spaltenkommentaren**, z. B. `.../2026_07_04_150003_create_product_pv_module_specs_table.php` — `->comment('[V] Leerlaufspannung STC (voc_v)')`; `.../2026_07_04_150004_create_product_heat_pump_specs_table.php` — `->comment('[kW] Heizleistung A-7/W35')`. `product_radiator_specs` führt zusätzlich `norm_bedingung` (default `'75/65/20'`), `exponent_n` (default 1.30) und `quelle` (`.../2026_07_04_140001_...`).
Kennlinien als JSON: `product_heat_pump_specs.leistungskurve` + `kurve_semantik` (`.../2026_07_05_150005_add_kurve_semantik_to_product_heat_pump_specs_table.php`).

*Generation 3 — Merkmals-Registry im Code, nicht in der DB:*
`/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Services/Spec/SpecSchema.php:45-68` definiert je Gerätetyp (`waermepumpe`, `pv_modul`, `wechselrichter`, `batterie`) Zieltabelle, `products`-Spalten und Extra-Spalten. Kanonische Blöcke `identitaet` / `fachdaten` / `herkunft` in `:90-208`, inkl. `pflicht_alternativ`, `paare`, `semantik_pflicht`. Import: `app/Services/Spec/SpecImportService.php` (`parse`/`validate`/`dryRun`/`commit`/`rollback`), Batchführung in `spec_import_batches` (`:187`), Rollback über `import_batch_id` (`:216-219`), das per `.../2026_07_05_150008_add_import_batch_id_to_spec_targets.php` auf `products`, `product_heat_pump_specs`, `product_pv_module_specs`, `inverters`, `batteries` gelegt wurde.

**BELEGT — Freitext-Merkmale:** `product_descriptions` (`field`/`description`/`remark`, `.../2023_08_03_100317_...`) — Key-Value ohne Einheit, ohne Datentyp, `description` nur `string(255)`. Aktiv beschrieben in `app/Http/Controllers/Product/ProductDescriptionController.php:77,117,181`.

**BELEGT — Merkmale am Stamm:** `products.heatpump_type`, `construction_type`, `refrigerant`, `phase_count`, `scop`, `noise_level_db` (`.../2025_08_26_075553_add_fields_for_katalog_to_products_table.php`) — gerätetypspezifische Technik direkt in der Stammtabelle.

**BEWERTUNG**
Ein **generisches Merkmal-/Einheiten-Modell existiert nicht in der Datenbank**. Es existiert in `SpecSchema.php` als Code-Registry — d. h. jede neue Gerätekategorie erfordert eine Migration plus Code-Änderung. `measures` ist die einzige Einheiten-Tabelle und enthält nur einen Freitext (`.../2023_06_22_085601_create_measures_table.php`), der beim IDS-Promote per `Measure::firstOrCreate` beliebig wächst (`IdsController.php:220-222`).

**ANNAHME**
Generation 1 (`product_p_v_s`, `product_w_p_s`, `radiators`) ist fachlich durch Generation 2 abgelöst; die Altdaten sind aber weder migriert noch stillgelegt — `inverters` und `batteries` werden von **beiden** Generationen beschrieben (`SpecSchema.php:59-65`).

---

### Frage 6 — Medien / Dokumente

**BELEGT — `product_images`** (`.../2023_08_09_084555_create_product_images_table.php:14-24`): genau `id`, `product_id`, `name` (nullable), `image`, `timestamps`.
Kein Typcode, kein Hash, keine Sprache, keine Herkunft, keine Sortierung, kein Haupt-Bild-Flag, keine MIME, keine Größe, kein Unique.
**Fehler:** `/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Models/ProductImage.php:11` — `protected $fillable = ['product_id', 'image', 'title'];`. Die Spalte heißt `name`, nicht `title`. In `/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Http/Controllers/Product/ProductImportController.php:220-227` wird `ProductImage::firstOrCreate([...], ['name' => $product->product])` aufgerufen — `name` ist nicht fillable und wird von Eloquent **stillschweigend verworfen**; `title` existiert als Spalte nicht.
Die „Haupt­bild"-Logik ist reine Sortierkonvention: `app/Models/Product.php:99-101` — `hasOne(ProductImage::class)->orderBy('id','asc')`.

**BELEGT — `product_documents`** (`.../2023_08_11_062919_create_product_documents_table.php:14-21`): `id`, `product_id`, `title` (**not null**), `document`, `timestamps`.
Kein Dokumenttyp (Datenblatt / CE / Konformität / Montageanleitung), kein Hash, keine Sprache, keine Version, kein Gültigkeitsdatum, keine Herkunft, keine Dateigröße, keine MIME. Nutzung: `app/Http/Controllers/Product/ProductDocumentsController.php`, `app/Http/Controllers/Product/ProductController.php`.

**BELEGT — Gegenbeispiel im selben Repo:** `customer_product_info_media` (`.../2026_02_04_110832_create_customer_product_info_media_table.php`) führt `disk`, `path`, `stored_name`, `original_name`, `mime`, `size`, `type` (`image|pdf|file`, indiziert), `uploaded_by`, `sort_order` — also genau das, was am Produktstamm fehlt. Auch hier: **kein Hash, keine Sprache**.

**BELEGT — `images`** (`.../2025_01_24_093117_create_images_table.php`) ist die Universal-Bildtabelle mit acht FKs (u. a. `article_group`→`article_groups`), `file_type`, SoftDeletes, `created_by`/`update_by`. Sie ist Lead-/Prozess-bezogen, nicht produktbezogen.

**BELEGT — Bildherkunft aus Lieferantenkanal:** `supplier_article_map.bild_url` (`.../2026_07_04_140007_...`), befüllt in `IdsMapper.php:41,54` — eine URL, kein persistiertes Asset, kein Hash.

**BEWERTUNG**
Der Medienbereich ist der schwächste Teil des Bereichs. Ohne Hash ist Dedublizierung unmöglich; ohne Typcode ist „zeige das Datenblatt" nicht beantwortbar; ohne Sprache ist Mehrsprachigkeit ausgeschlossen; ohne Herkunft ist nach einem Lieferantenimport nicht mehr entscheidbar, welches Bild kuratiert und welches importiert war.

---

### Frage 7 — Sets / Stücklisten / Bundles

**BELEGT — aktive Generation (2026):**
- `master_sets` (`.../2026_01_05_103819_...`): Kopf, `article_group_id`, `status`, SoftDeletes. **Keine Versionsspalte, kein Gültigkeitszeitraum.**
- `master_set_components` (`.../2026_01_05_103845_...`): **echte Baumstruktur** über `parent_id` (self-referencing FK), `product_id`, `qty` `decimal(10,2)`, `sort_order`, plus `distributor_id` und `distributor_price_id` als Preisanker.
  Erweitert um kaufmännische Felder (`.../2026_03_02_093129_...`), `article_no` (`.../2026_03_03_083524_...`), `distributor_article_no` (`.../2026_03_06_135925_...`), `price_unit` (`.../2026_03_03_114421_...`).
  **Hinweis:** In `.../2026_03_06_135925_add_distributor_id_to_master_set_components_table.php` steht im `down()` `if (Schema::hasColumn('distributor_article_no'))` — `hasColumn` erwartet zwei Argumente (Tabelle, Spalte); der Rollback ist fehlerhaft.
- `master_set_component_descriptions` (`.../2026_02_10_065251_...`): Textvarianten je Position mit `context` (default `'angebot'`), `title`, `sort_order`, `delta` (Quill-JSON), `html`, `text`. Das ist die einzige Stelle mit **Varianten** — allerdings Textvarianten, keine Produktvarianten.
- `master_set_groups` + `master_set_group_master_set` (`.../2026_02_10_110420_...`, `.../2026_02_10_110447_...`): Gruppierung, letztere ohne `id`, mit Unique-Paar.
- Snapshot in den Angebotskorb: `master_set_carts` / `master_set_cart_sections` / `master_set_cart_items` (`.../2026_03_10_123814_...`) mit `source_type`, `node_type`, `depth`, `parent_id` — die Set-Struktur wird beim Übernehmen **kopiert**, nicht referenziert.
- Ergänzend: `master_set_checklists`, `master_set_labor`, `master_set_tasks`, `master_set_task_labors`.

**BELEGT — Legacy-Generation (2024):**
`product_master_sets`, `product_sub_sets`, `add_product_to_sets`, `add_image_to_sets`, `product_set_descriptions`, `group_sets`, `group_set_product_master_set`.
Sämtliche Schreib-Controller liegen unter `/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Http/Controllers/Old/` (`ProductMasterSetController.php`, `ProductSubSetController.php`, `AddProductToSetController.php`, `AddImageToSetController.php`, `GroupSetController.php`, `OfferConfigController.php`). **In `routes/web.php` gibt es keinen einzigen Verweis auf den Namespace `Old\`** — geprüft, null Treffer. Diese Tabellen sind schreibseitig tot.
**Lesend lebt `product_master_sets` aber weiter:** `/sessions/rcw-011su8gzjddstk7zunsypoae/mnt/ticket/app/Http/Controllers/Customer/Offer/OfferDetailsController.php:294` — `ProductMasterSet::with([...])`.
`product_set_descriptions` und `group_sets` haben weder aktiven Lese- noch Schreibpfad → **toter Bestand**.

**BEWERTUNG**
Struktur ja (Baum über `parent_id`), **Versionierung nein**, **Regeln nein**. Es gibt keine Gültigkeitszeiträume, keine Set-Version, keine Konfigurationsregel (Pflicht/Alternative/Ausschluss/Bedingung). `master_set_components.type` (`'haupt'|'zubehoer'`, `.../2026_03_02_093129_...`) ist die einzige regelartige Information. Zwei komplette Set-Generationen existieren parallel, eine davon wird nur noch gelesen.

---

### Frage 8 — Varianten

**BELEGT**
Ein Variantenmodell für Produkte **existiert nicht**. Suche nach `variant|variante` in `database/migrations` und `app/Models`:
- `sanierungs_varianten` (`.../2026_07_07_180004_create_sanierungs_varianten_table.php`) — Sanierungsszenarien im Heizlast-Kontext, kein Produktbezug.
- `MasterSetComponent::descriptionVariants()` (`app/Models/MasterSetComponent.php:76`) — **Text**varianten.
- Kein `product_variants`, kein `variant_options`, keine Achsenmodellierung (Farbe/Größe/Leistung) — Prüfung `product_variants` in `database/migrations`: kein Treffer.

Die faktischen Variantenträger sind:
- `products.color`, `products.model`, `products.roof_type` als Attribute am Stamm (`.../2023_06_22_085602_create_products_table.php:25-27`),
- `product_types` (`.../2023_06_22_085602_create_product_types_table.php`) — eine Zeile je „Typ" mit eigenem Preis/EAN/Serial, `product_id` als **`integer` ohne FK**; nur ein Controller (`ProductTypeController`), geroutet über `routes/web.php:2385-2389`.

**BEWERTUNG**
Varianten werden heute durch **eigenständige Produktdatensätze** abgebildet. `product_types` war offenbar ein Variantenversuch, ist aber typschwach (Preise als `integer`), FK-los und praktisch isoliert. Für eine Produktdatenplattform ist der Variantenbegriff neu zu bauen — nicht zu migrieren.

---

### Frage 9 — Lebenszyklus (Auslauf / Nachfolger / Ersatzartikel)

**BELEGT — es gibt keine Lebenszyklus-Felder.**
Suche nach `nachfolger|successor|ersatzartikel|replacement_|auslauf|discontinued|end_of_life|eol_|obsolete|abkuendig` über `database/migrations`, `app/Models`, `app/Services`: **ein einziger Treffer**, und der ist unbeteiligt — `app/Models/PlannerItem.php:59` (`// Items that depend on THIS item (Successors)`, Vorgangsplanung).

Was stattdessen existiert:
- `products.status` `string` default `'active'` (`.../2023_06_22_085602_create_products_table.php:32`). Die Werte sind uneinheitlich: `'active'` (Migration), `'Published'` (`IdsController.php:229`), `1` (`SupplierProductImportService.php:286` — `'status' => $productData['status'] ?? 1`), `'active'` (`ProductImportController.php:146`). Vier Wertwelten in einer Spalte, kein Enum, kein Check.
- `distributor_prices.availability` (`string`, nullable) und `distributor_prices.status` (default `'Published'`) — Verfügbarkeit je Lieferant, nicht Artikellebenszyklus.
- `master_set_components.availability` (`boolean`, `.../2026_03_02_093129_...`).
- `accessories.aktiv`, `product_radiator_specs.aktiv` (`boolean`) — nur an den neuen Katalogen.
- Qualitätszustand statt Lebenszyklus: `products.verifikations_status` (`'datenblatt_verifiziert' | 'importiert_ungeprueft'`), `verifikations_datum`, `datenblatt_referenz` (`.../2026_07_05_150007_add_verifikation_fields_to_products_table.php`) und `products.imported_from` (`.../2026_07_05_150006_...`).

**BEWERTUNG**
Es gibt keine Möglichkeit, einen auslaufenden Artikel als solchen zu kennzeichnen, ein Auslaufdatum zu setzen oder einen Nachfolger zu benennen. In der Praxis bedeutet das: ausgelaufene Artikel bleiben `active` und tauchen in Sets und Angeboten weiter auf, oder sie werden gelöscht — und reißen dann per `onDelete('cascade')` die Historie mit (`distributor_prices`, `product_images`, `product_documents`, `product_histories`, `master_set_components` hängen alle an `cascade`).

---

## 3 · Toter und defekter Bestand (gesondert)

| Objekt | Befund | Beleg |
|---|---|---|
| `radiator_connection_factors` | Model `RadiatorConnectionFactor.php` existiert, **null** Lese- und Schreibpfade außerhalb des Models | Grep `RadiatorConnectionFactor::` in `app/` → 0 Dateien |
| `product_set_descriptions` | Kein Model, kein Schreib-/Lesepfad | Grep über `app/` → nur Migration |
| `group_sets`, `group_set_product_master_set` | Nur `app/Http/Controllers/Old/GroupSetController.php`, nicht geroutet | `routes/web.php`: 0 Treffer für `Old\` |
| `customer_product` | Model `CustomerProduct.php:13` deklariert `protected $table = 'customer_products'` — **diese Tabelle existiert nicht**; kein Schreib-/Lesepfad | Grep `'customer_products'` in `database/migrations` → 0 |
| `ProductFavorite` (Model) | zeigt implizit auf `product_favorites` — **Tabelle existiert nicht**; `Product::favorites()` (`app/Models/Product.php:147`) und `isFavoritedByEmployee()` (`:157-165`) laufen ins Leere | Grep `'product_favorites'` in `database/migrations` → 0 |
| `StampArticle` (Model) | zeigt implizit auf `stamp_articles` — **Tabelle existiert nicht**; `Product::stampArticles()` (`app/Models/Product.php:152`), `isStampedByEmployee()` (`:168`) betroffen | Grep `'stamp_articles'` in `database/migrations` → 0 |
| `Product::subArticle()` | referenziert `SubArticle::class` — Model heißt `SubArticleGroup` | `app/Models/Product.php:122-125` |
| `ProductImportController` setzt `$product->discount_price` | Spalte existiert auf `products` nicht (nur auf `distributor_prices`) | `app/Http/Controllers/Product/ProductImportController.php:157`; Grep `discount_price` in `database/migrations` → nur `2023_10_16_141346_...:23` |
| `DatanormController` | parst Datanorm-Dateien und gibt sie **nur an eine View** zurück — **kein Persistenzpfad**; Routen `datanorm.form` / `datanorm.parse` (`routes/web.php:4742-4743`) | `app/Http/Controllers/DatanormController.php:14-46` |
| `OmdMapper`, `DatanormMapper` | `map()` gibt konstant `null` zurück (dokumentierte Stubs) | `app/Services/Suppliers/Mappers/OmdMapper.php:19`, `DatanormMapper.php:19` |
| `spec_import_batches` | Kein Eloquent-Model, ausschließlich `DB::table` | `app/Services/Spec/SpecImportService.php:187,205,216` |
| `product_formulas.product_id` | FK zeigt auf `article_groups`, nicht auf `products` | `.../2025_06_02_083017_create_product_formulas_table.php` |
| `heat_pumps.product_id`, `solar_systems.product_id`, `customer_product_infos.product_id` | alle drei FKs zeigen auf `article_groups` | jeweilige Create-Migration |

---

## 4 · Die fünf größten strukturellen Risiken für eine Produktdatenplattform

**1 · Es gibt keinen Artikelschlüssel — nur Suchtexte.**
`products` trägt `article_no`, `ean`, `sku`, `model` **ohne einen einzigen Unique- oder Index-Constraint**. Fünf Schreibpfade legen Artikel nach fünf verschiedenen Identitätsbegriffen an: EAN (`SupplierProductImportService.php:212`), `article_no` global (`:220`, `IdsController.php:225`), `(distributor_id, article_no)` (`:228`), `(brand, model)` (`SpecImportService.php:352`), Produktname (`ProductImportController.php:135`). Keiner normalisiert (kein `LOWER`, kein `TRIM`, keine Führungsnullen). Folge: Zwillinge entstehen zwangsläufig, und *welcher* Zwilling beim nächsten Lauf getroffen wird, hängt vom Importweg ab. Jede Deduplizierung, jede Preiszuordnung, jede Mediensammlung baut auf einem Fundament, das keine Identität kennt. Der einzige Baustein, der es richtig macht — `supplier_article_map` mit Unique `(hersteller, herst_artikelnr, supplier_channel)` — deckt nur importierte Lieferantenzeilen ab und wird ausgesprungen, sobald `products.brand_id` fehlt (`IdsMapper.php:35-39`).

**2 · Preise haben keine Zeitachse und werden vervielfältigt statt referenziert.**
`distributor_prices` kennt nur `price_date` als Stichtag (`.../2023_10_16_141346_...:28`) und wird per `updateOrCreate` überschrieben (`ProductImportController.php:189`, `IdsController.php:264`). Es gibt keine Preishistorie, keine Von/Bis-Gültigkeit, keine Staffel, keine Währung, keine numerische Preisbezugsmenge. Gleichzeitig existiert derselbe Preis vier Mal: `products.retail_price`/`purchase_price`, `distributor_prices`, `supplier_article_map.ek_preis`/`vk_preis`, `master_set_components.unit_price`/`purchase_price` plus nochmals kopiert in `master_set_cart_items`. Nach einem Lieferantenimport ist weder rekonstruierbar, welcher Preis wann galt, noch welche Angebotsposition auf welchem Preisstand beruht. Für eine Plattform mit Nachkalkulation oder Preisverhandlung ist das nicht reparierbar, sondern neu zu bauen.

**3 · Merkmale sind dreifach modelliert und ohne Einheiten-Modell.**
Drei Generationen technischer Daten laufen parallel: breite Gerätetabellen von 2024 (`product_p_v_s`, `product_w_p_s`, `radiators`, `inverters`, …), normierte Spec-Tabellen von 2026 (`product_pv_module_specs`, `product_heat_pump_specs`, `product_radiator_specs`) und Merkmale direkt am Stamm (`products.scop`, `refrigerant`, `phase_count` aus `.../2025_08_26_075553_...`). `inverters` und `batteries` werden von **beiden** Generationen beschrieben (`SpecSchema.php:59-65`) — dieselbe Zeile kann aus zwei Quellen mit zwei Konventionen kommen. Ein Einheiten-Modell existiert nur als Spaltenkommentar; die einzige Einheiten-Tabelle `measures` hat genau ein Freitextfeld und wächst per `firstOrCreate` beim Import (`IdsController.php:220`). `radiators` trägt Wechselrichter-Felder. Jede neue Gerätekategorie kostet heute eine Migration plus eine Code-Änderung in `SpecSchema.php` — eine Plattform braucht das Gegenteil.

**4 · Medien und Dokumente haben keine Metadaten — Import und Kuratierung sind nicht unterscheidbar.**
`product_images` = `product_id` + `name` + `image`. `product_documents` = `product_id` + `title` + `document`. Kein Typcode, kein Hash, keine Sprache, keine Herkunft, keine Version, kein Gültigkeitsdatum, keine Sortierung, kein Unique (`.../2023_08_09_084555_...`, `.../2023_08_11_062919_...`). Damit ist keine Frage beantwortbar, die eine Produktdatenplattform beantworten muss: „welches ist das aktuelle Datenblatt", „ist dieses Bild schon vorhanden", „welches Bild hat der Nutzer gepflegt und welches kam vom Lieferanten". Der Importer lädt Bilder per URL herunter und schreibt sie ohne Hash-Prüfung (`ProductImportController.php:212-230`) — und der eine Metadatenwert, der gesetzt werden soll, wird durch den `$fillable`-Fehler in `app/Models/ProductImage.php:11` stillschweigend verworfen. Dass das Repo mit `customer_product_info_media` (`.../2026_02_04_110832_...`) ein deutlich besseres Medienmodell besitzt, das aber am Kundengerät statt am Produkt hängt, unterstreicht den Befund.

**5 · Kein Lebenszyklus, aber flächendeckendes `onDelete('cascade')`.**
Es gibt kein Feld für Auslauf, Nachfolger oder Ersatzartikel — die Suche nach den einschlägigen Begriffen liefert im gesamten Produktbereich null Treffer. `products.status` trägt gleichzeitig `'active'`, `'Published'` und `1` (`.../2023_06_22_085602_...:32` vs. `IdsController.php:229` vs. `SupplierProductImportService.php:286`). Wer einen ausgelaufenen Artikel aus dem Weg räumen will, hat nur das Löschen — und dann greifen die `cascade`-Regeln auf `distributor_prices`, `product_images`, `product_documents`, `product_histories`, `master_set_components`, `add_product_to_sets`, `product_sub_sets`, `activity_articles`, `stamp_article_list_items`, `inventories`. Ein einziger Löschvorgang entfernt Preisstand, Medien, Änderungshistorie und Stücklistenpositionen gleichzeitig. Zusätzlich verschärfend: die Legacy-Set-Tabellen (`product_master_sets`-Familie) werden schreibseitig nicht mehr bedient — die zugehörigen Controller liegen unter `app/Http/Controllers/Old/` und sind **nicht geroutet** — aber `OfferDetailsController.php:294` liest sie weiter. Alte Angebote hängen damit an Daten, die niemand mehr pflegt und die beim Produktlöschen mitverschwinden.
