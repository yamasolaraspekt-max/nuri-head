# CRM-Inventur Zone 01 — Artikel / Produktkatalog

- **Zone:** 01 — Artikel / Produktkatalog (WAS für Artikel gibt es, wie strukturiert)
- **Stand:** 2026-07-01
- **Art:** Reine Analyse / Inventur (NUR Lesen, kein Code geändert, kein Git)
- **Arbeitsverzeichnis:** `/Users/yamanuri/Documents/ticket` (Laravel 11)

## Abgrenzung zu Nachbarzonen

- **Zone 02 (Lager/Bestand/Beschaffung/Großhandel):** Sidebar-Block „Lager" (Inventar, Lieferscheine, Betriebsmittel, Übergaben, Lagerausgaben, Kaufanfragen, Maschinen), sowie Distributor/Brand/IDS/Supplier-Connectors. Am `Product`-Model hängen zwar `inventory()`, `requestOuts()`, `purchaseRequests()`, `distributors()`/`prices()` — die gehören inhaltlich zu Zone 02, nur die FK-Verankerung liegt am Artikel.
- **Zone 03 (Set-/Angebotskonfiguration inkl. MasterSet):** `Product/MasterSet/*`, `product_master_sets`, `product_set_descriptions`, `master_set_components`, sowie die Angebots-/Lead-Produktlisten (`lead_product_lists`, `customer_product_lists`, `offer_product_lists`). **Grenzfall `ProductFormulaController`** → gehört fachlich zu Zone 03 (Checkliste/Konfiguration), siehe unten.

**Glossar (zur Einordnung, nicht Teil dieser Zone):** Kunde = `new_leads`, Objekt = `lead_alternative_adds`, Gewerk = `lead_product_lists`. Katalog-Artikel dieser Zone = `products` / `article_groups`.

**Sidebar-Verankerung:** `resources/views/admin/layouts/sidebar.blade.php`, Block „Artikel & Lager" (ab Z. 1074). Meine Zone = die Menüpunkte **„Artikel"** (Z. 1077, Katalog) und **„Artikel-Daten"** (Z. 1141, Stammdaten). „Lager" (Z. 1180) = Zone 02.

---

## Unterbereiche

### 1. Produktkatalog (Kern) — `products`
| Feld | Inhalt |
|---|---|
| **Zweck** | Zentraler Artikelstamm: einzelne Katalog-Artikel (PV-Module, Wärmepumpen, Zubehör …) mit Stammdaten, Preisen, Klassifizierung. Das mit Abstand größte Herzstück der Zone. |
| **Controller/Routen** | `Product/ProductController.php` (**1964 Z.**, größter Controller der Zone). Routen-Gruppe ab `web.php` Z. 2289 (`product.info`, `product.show`, `product.store`, `product.edit`, `product.update`, `product.destroy`, `product.create`, `products.list` (ajaxList), `products.bulk`, `product.history`, `products.duplicate.*`, `products.export.no-images`). Sidebar: „Katalog" → `product.info`; „Neuer Artikel" → `product.create`. View: `admin.product.product.product`. |
| **Kern-Tabellen** | `products` (id, article_no, sku, ean, product, model, category, brand_id, article_group→FK, sub_article→FK, measure_unit→FK, discount_group→FK, color, roof_type, WP-Felder heatpump_type/construction_type/refrigerant/phase_count/scop/noise_level_db, Preise retail_price/purchase_price/vat_percent, status). Historie: `product_histories` (JSON old/new/changed_fields, changed_by). Model `Product.php` mit ~30 Relationen. |
| **Größe** | **groß** (Controller ~2000 Z., ~40 Routen, 8+ angehängte Untertabellen) |
| **Status** | **aktiv** (Index lädt volle Filter/Favoriten/Stamp-UI; Katalog + Neu-Anlage sind Sidebar-Hauptpunkte; Workflow-/History-Migrationen bis 2026-05) |

### 2. Artikelgruppen & Sub-Artikelgruppen (Kategorien/Hierarchie)
| Feld | Inhalt |
|---|---|
| **Zweck** | Zweistufige Klassifizierungs-Hierarchie der Artikel: Artikelgruppe → Sub-Artikelgruppe. Ordnet jeden Artikel ein (`products.article_group`, `products.sub_article`). |
| **Controller/Routen** | `ArticleGroup/ArticleGroupController.php` (**619 Z.**, verwaltet auch Sub-Gruppen), `ArticleGroup/SubArticleGroupController.php` (122 Z.). Routen `web.php` Z. 2864–2870 (`article_group.index/store/update/destroy`, `sub_article_group.*`) + `article-groups.store` (Z. 2959). Sidebar: „Artikel-Daten → Artikel-Gruppen" → `article_group.index`. |
| **Kern-Tabellen** | `article_groups` (article_group, initial, min_value, max_value, image; SoftDeletes 2025-08), `sub_article_groups` (article_group_id→FK, sub_article, initial, value, status; SoftDeletes 2025-08). Models `ArticleGroup.php`, `SubArticleGroup.php`. |
| **Größe** | **mittel** |
| **Status** | **aktiv** (Sidebar count_key `article_groups`; SoftDeletes-Migrationen 2025) |

### 3. Einheiten (Measures)
| Feld | Inhalt |
|---|---|
| **Zweck** | Mengeneinheiten-Stammdaten (Stück, m, kg …), referenziert über `products.measure_unit`. |
| **Controller/Routen** | `Product/MeasureController.php` (100 Z.). Routen `web.php` Z. 2487–2490 (`measure.info/store/update/destroy`). Sidebar „Artikel-Daten → Einheiten". |
| **Kern-Tabellen** | `measures` (nur `measure` + timestamps). Model `Measure.php`. |
| **Größe** | **klein** |
| **Status** | **aktiv** |

### 4. Rabattgruppen (DiscountGroups)
| Feld | Inhalt |
|---|---|
| **Zweck** | Rabattklassen (Name + Prozent), referenziert über `products.discount_group`. Preis-nahe Stammdaten am Artikel. |
| **Controller/Routen** | `Product/DiscountGroupController.php` (109 Z.). Routen `web.php` Z. 2856–2859 (`discount_group.info/store/update/destroy`). Sidebar „Artikel-Daten → Rabattgruppen". |
| **Kern-Tabellen** | `discount_groups` (discount_group, discount:int). Model `DiscountGroup.php`. |
| **Größe** | **klein** |
| **Status** | **aktiv** |

### 5. Produktbeschreibungen (technische Merkmale)
| Feld | Inhalt |
|---|---|
| **Zweck** | Freie Feld/Wert-Beschreibungen je Artikel (technische Datenblatt-Zeilen). |
| **Controller/Routen** | `Product/ProductDescriptionController.php` (223 Z.). Routen `web.php` Z. 2320–2334 + Bulk 2412–2416 (`product.description.*`, `products.descriptions.*`). |
| **Kern-Tabellen** | `product_descriptions` (product_id→FK, field, description, remark, status). Relation `Product::descriptions()`. |
| **Größe** | **klein–mittel** |
| **Status** | **aktiv** |

### 6. Produktbilder (+ CSV-Import)
| Feld | Inhalt |
|---|---|
| **Zweck** | Bildergalerie je Artikel inkl. Hauptbild; Massen-Import per CSV. |
| **Controller/Routen** | `Product/ProductImageController.php` (304 Z.), `Product/ProductImageCsvImportController.php` (CSV, Routen Z. 1454–1455). Bild-Routen `web.php` Z. 2336–2346. |
| **Kern-Tabellen** | `product_images` (product_id→FK, name, image). Relationen `Product::images()/firstImage()`, Accessor `main_image`. |
| **Größe** | **mittel** |
| **Status** | **aktiv** |

### 7. Produktdokumente
| Feld | Inhalt |
|---|---|
| **Zweck** | Datenblätter/PDFs je Artikel. |
| **Controller/Routen** | `Product/ProductDocumentsController.php`. Routen `web.php` Z. 2348–2351 + Upload/List/Delete Z. 2425–2428. |
| **Kern-Tabellen** | `product_documents` (product_id→FK, title, document). |
| **Größe** | **klein** |
| **Status** | **aktiv** |

### 8. Installationsfälle (Montage-Aufwand je Artikel)
| Feld | Inhalt |
|---|---|
| **Zweck** | Definiert Installations-/Montagefälle mit Rate je Artikel (Kalkulationsbasis). |
| **Controller/Routen** | `Product/ProductInstallationCaseController.php` (126 Z.). Routen `web.php` Z. 2353–2356. |
| **Kern-Tabellen** | `product_installation_cases` (product_id→FK, case, description, rate). |
| **Größe** | **klein** |
| **Status** | **aktiv** |

### 9. Produkt-Typen / Varianten (ProductType)
| Feld | Inhalt |
|---|---|
| **Zweck** | Varianten/Ausführungen je Marke (Artikel, EAN, Serial, Typ, Preis-/Verfügbarkeits-/Rückgabe-Felder). Achtung: `product_types.product_id` joint gegen **`brands`**, nicht gegen `products` (markenbasiert). |
| **Controller/Routen** | `Product/ProductTypeController.php` (149 Z.). Routen `web.php` Z. 2358–2362 (`product.type`, `product.type.save/update/save.image`). |
| **Kern-Tabellen** | `product_types` (viele Preis-/Logistikfelder). Model `ProductType.php`. |
| **Größe** | **mittel** |
| **Status** | **teilweise** (breite Tabelle, aber Nutzung nur über Marken-Detailseite; nicht in Haupt-Sidebar). Grenzfall Richtung Beschaffung/Zone 02 (Verfügbarkeit/Rückgabe/purchase_price). |

### 10. Favoriten-Listen
| Feld | Inhalt |
|---|---|
| **Zweck** | Persönliche Artikel-Favoritenlisten je Mitarbeiter. |
| **Controller/Routen** | `Product/ProductFavoriteListController.php` (402 Z.). Routen `web.php` Z. 2433–2447 (`products.favorite-lists`, `ajax.products.favorite-lists.*`, `product.favorites.index`). Sidebar „Artikel → Favoriten". |
| **Kern-Tabellen** | `product_favorite_lists`, `product_favorite_list_items` (2025-11). Relation `Product::favorites()`. |
| **Größe** | **mittel** |
| **Status** | **aktiv** (neu, Nov 2025) |

### 11. Stamm-/Stempel-Listen (StampArticle)
| Feld | Inhalt |
|---|---|
| **Zweck** | Kuratierte „Stamm"-Artikellisten je Mitarbeiter (type z. B. `master`); ähnlich Favoriten, aber als benannte Sammlungen. |
| **Controller/Routen** | `Product/StampArticleListController.php` (393 Z.). Routen `web.php` Z. 2449–2457. Sidebar „Artikel → Stamm-Listen". |
| **Kern-Tabellen** | `stamp_article_lists`, `stamp_article_list_items` (2025-11). Models `StampArticle.php`, `StampArticleList.php`, `StampArticleListItem.php`. |
| **Größe** | **mittel** |
| **Status** | **aktiv** (neu, Nov 2025) |

### 12. Preisvergleich (ProductDifference)
| Feld | Inhalt |
|---|---|
| **Zweck** | Vergleicht Artikel-/Distributorpreise (Differenz-Analyse). Preis-nah, aber Datenquelle sind Distributor-Preise (Zone 02) über die Artikel. |
| **Controller/Routen** | `Product/ProductDifferenceController.php` (489 Z.). Routen `web.php` Z. 2390–2391 (`admin.products.difference`, `.compare`). Sidebar „Artikel → Preisvergleich". |
| **Kern-Tabellen** | liest `products` + `distributor_prices` (Zone-02-Grenze). |
| **Größe** | **mittel** |
| **Status** | **aktiv** |

### 13. Produkt-Import (Master-CSV/Excel)
| Feld | Inhalt |
|---|---|
| **Zweck** | Massen-Anlage/-Update von Artikeln aus Datei (Preview → Store). |
| **Controller/Routen** | `Product/ProductImportController.php` (422 Z.) — Routen Z. 2420–2422; zusätzlich `Product/ProductCsvImportController.php` (Routen Z. 554–557). |
| **Kern-Tabellen** | schreibt `products` (+ Relationen). |
| **Größe** | **mittel** |
| **Status** | **aktiv** |

### 14. Fliesen-Stammdaten (Tiles)
| Feld | Inhalt |
|---|---|
| **Zweck** | Kleiner Stammdatenkatalog für Dachziegel/Fliesen (Name, Modell, Bild) — vermutlich für Dach-/Montage-Auswahl. |
| **Controller/Routen** | `Product/TilesController.php` (124 Z.). Routen `web.php` Z. 2479–2482. Nicht in Haupt-Sidebar der Zone. |
| **Kern-Tabellen** | `tiles` (name, model, image). Model `Tiles.php`. |
| **Größe** | **klein** |
| **Status** | **teilweise** (eigenständiger Mini-Stamm, geringe Integration) |

---

## Grenzfälle (kurz zugeordnet, NICHT vertieft)

- **`ProductFormulaController` (259 Z.)** → **fachlich Zone 03 (Konfiguration/Checklisten).** Belegt: `product_formulas.product_id` hat FK auf **`article_groups`** (nicht `products`), und der Controller lädt/verwaltet `ArticleGroup::with('formulas')`. Sidebar-Label ist „Checklisten-Formulare" (Z. 1166), nicht Katalog. Nur die *verwalteten Artikelgruppen* selbst liegen in Zone 01; die Formular-/Checklisten-Logik gehört zu 03.
- **`TemperatureController`** (Product-Ordner) → Heizkörper-/Temperatur-Konfiguration; gehört zum „Heizkörper-Konfigurator" (Sidebar Z. 1115, Route `radiator.config.view`) → eher **Zone 03**.
- **`ProductPVController` / `ProductWPController` / `Product/PV/*` (Radiator, Battery, Inverter …)** → PV-/WP-spezifische Detaildaten am Artikel; sitzen im Product-Ordner, sind aber domänenspezifische Erweiterungen. Randständig zwischen 01 (Artikel-Detaildaten) und 03 (Konfiguration) — hier nur erwähnt.
- **`PurchaseRequestController`, `Product/Distributor/*`, `Product/Brand/*`, `Product/IDS/*`, `Product/MasterSet/*`, `Product/Stage/*`** → liegen physisch im `Product/`-Ordner, gehören aber inhaltlich zu **Zone 02** (Beschaffung/Großhandel/Marken/Lieferanten-Schnittstellen) bzw. **Zone 03** (MasterSet). Nicht Teil dieser Inventur.

---

## Braucht eigene Detail-Inventur

1. **Produktkatalog / `ProductController` (~1964 Z.)** — GRÖSSTER Sub-Bereich der Zone. Eigene Tief-Inventur nötig: alle ~40 Routen, ajaxList/Filter, bulkAction, duplicate/bulkDuplicate, history, publish/unpublish, Distributor-Verknüpfung, Preislogik (retail/purchase/vat), Workflow-Spalten. **→ Zone-01-Detail-01.**
2. **`ArticleGroupController` (~619 Z.)** — enthält Artikelgruppen UND Sub-Artikelgruppen inkl. `save()`-Zweitpfad; Detail-Inventur der Hierarchie-/min-max-Value-Logik empfehlenswert.
3. **Grenzklärung `ProductFormula` / `Temperature` / `Product/PV+WP`** — verbindliche Zuordnung 01 vs. 03 sollte mit Zone 03 abgeglichen werden (nur Landkarte, nicht jetzt umbauen).

---

## Belege (Dateien / Greps)

**Routen:** `routes/web.php` (5434 Z.) — Katalog Z. 2289–2445; Beschreibung 2320–2334/2412–2416; Bilder 2336–2346/1454–1455; Dokumente 2348–2351/2425–2428; Installation 2353–2356; Typ 2358–2362; PV 2364–2366; Preisvergleich 2390–2391; Import 2420–2422/554–557; Favoriten 2433–2447; Stamp 2449–2457; Tiles 2479–2482; Measure 2487–2490; Discount 2856–2859; ArticleGroup 2864–2870/2959; Formula 2875–2887. `routes/api.php` (365 Z.) — **keine** product/article-Routen.

**Controller:** `app/Http/Controllers/Product/` (ProductController 1964, ProductDifference 489, ProductImport 422, ProductFavoriteList 402, StampArticleList 393, ProductImage 304, ProductFormula 259, ProductDescription 223, ProductType 149, ProductInstallationCase 126, Tiles 124, DiscountGroup 109, Measure 100). `app/Http/Controllers/ArticleGroup/` (ArticleGroupController 619, SubArticleGroupController 122).

**Migrationen:** `create_products_table`, `add_fields_for_katalog_to_products` (2025-08), `add_price_fields_to_products` (2025-11), `create_article_groups_table` + `add_soft_deletes_to_article_groups` (2025-08), `create_sub_article_groups_table` + soft deletes, `create_product_types_table`, `create_measures_table`, `create_discount_groups_table`, `create_product_descriptions_table`, `create_product_images_table`, `create_product_documents_table`, `create_product_installation_cases_table`, `create_tiles_table`, `create_product_favorite_lists(_items)_table` (2025-11), `create_stamp_article_lists(_items)_table` (2025-11), `create_product_histories_table` (2026-05), `add_created_by_updated_by_to_products` (2026-05). Grenzfall: `create_product_formulas_table` (FK auf `article_groups`).

**Models:** `app/Models/` — Product.php (~218 Z., ~30 Relationen), ArticleGroup.php, SubArticleGroup.php, ProductType.php, Measure.php, DiscountGroup.php, Tiles.php, StampArticle.php, StampArticleList.php, StampArticleListItem.php.

**Sidebar:** `resources/views/admin/layouts/sidebar.blade.php` — Block „Artikel & Lager" Z. 1074; „Artikel" (Katalog/Favoriten/Stamm-Listen/Preisvergleich) Z. 1077–1139; „Artikel-Daten" (Einheiten/Rabattgruppen/Artikel-Gruppen/Checklisten-Formulare/Anfragevorschläge) Z. 1141–1178; „Lager" (Zone 02) Z. 1180–1228.
