# 03 · Datenqualitätsbericht

<!-- Erzeugt in der Analysephase (Phase A). Rein lesende Untersuchung. -->

> **Rolle:** Planner · **Modus:** restriktiv lesend · **Stand:** 2026-07-30
> **Heimat-App:** `ticket` (Laravel 11.44, PHP 8.2, MySQL, DB `ticket`, Port 3307)
>
> **Legende:** · **BELEGT** (Fundstelle) · **BEWERTUNG** · **ANNAHME** · **OFFEN**

---

## 0 · Messbarkeitsgrenze — vorab und unmissverständlich

**BELEGT.** Der in Abschnitt 5 des Auftrags geforderte Ist-Datenbestand konnte in dieser Sitzung **nicht selbst gemessen werden**. Drei Gründe, alle geprüft:

| Prüfung | Ergebnis |
|---|---|
| `command -v mysql` in der Analyse-Umgebung | `mysql: command not found` |
| `php artisan tinker --execute=…` | `php: command not found` |
| Erreichbarkeit von `127.0.0.1:3307` | Die MySQL-Instanz läuft auf dem Rechner des Auftraggebers. Die Analyse-Umgebung ist eine getrennte VM, die lediglich das Repository-Verzeichnis eingebunden hat. `localhost` dort ist **nicht** der Datenbankrechner. |

**Konsequenz — und diese ist wichtig für die Bewertung des gesamten Berichts:** Jede Zahl über den tatsächlichen Datenbestand in diesem Dokument ist entweder

- **(S)** ein **Sekundärbeleg** aus einer früheren, im Repository abgelegten Inventur — mit Quelle und Datum gekennzeichnet, oder
- **(Q)** eine **noch auszuführende Prüfabfrage** — als SQL beigelegt, Ergebnis offen.

Es gibt in diesem Dokument **keine von mir selbst gemessene Bestandszahl**. Das ist keine Nachlässigkeit, sondern die ehrliche Kennzeichnung einer Umgebungsgrenze. Was dagegen **sehr wohl** selbst geprüft wurde, ist die **strukturelle** Datenqualität — also alles, was aus Migrationen, Constraints und Schreibpfaden ablesbar ist. Das ist Abschnitt 3 und dort liegt der eigentliche Befund.

---

## 1 · Sekundärbelegte Bestandszahlen (S)

Im Repository liegen zwei datierte Inventuren, die Zeilenzahlen nennen. Ich übernehme sie **als Sekundärbeleg**, nicht als eigene Messung.

| Tabelle | Zeilen | Quelle | Datum |
|---|---:|---|---|
| `distributor_prices` | **88** | `docs/audit/experten/04-beschaffung.md` | 2026-07-10 |
| `invoices` | **11** | `docs/audit/01-fehler-inventur.md` | 2026-07 |
| `article_groups` | **15** | `docs/audit/experten/*` | 2026-07 |
| `supplier_connections` | **0** | `docs/audit/experten/04-beschaffung.md:28,31` · bestätigt in `docs/bereich2-p3d2-ids-openmaster-preisfluss.md` (2026-07-13) | 2026-07-10 |
| `supplier_article_map` | **0** | ebd. | 2026-07-10 |
| `supplier_import_logs` | **0** | `docs/bereich2-p3d2-ids-openmaster-preisfluss.md` | 2026-07-13 |
| `imported_ids_items` | **0** | `docs/audit/experten/04-beschaffung.md:28` | 2026-07-10 |
| `purchase_requests` | **0** | ebd. | 2026-07-10 |
| `goods_receipts`, `delivery_notes` | **0** | ebd. | 2026-07-10 |
| `inventory_histories`, `inventory_request_outs` | **0** | ebd. | 2026-07-10 |
| `distributor_product` (Pivot) | **0** | ebd. | 2026-07-10 |

**Die Quelle bewertet die 88 Preiszeilen selbst als Demo-Daten** (`docs/bereich2-p3d2-ids-openmaster-preisfluss.md`, Abschnitt 2).

**BEWERTUNG.** Der gesamte Beschaffungs- und Lieferantenstrang ist datenseitig **unbenutzt**. Das ist für die Bewertung des Vorhabens die günstigste denkbare Ausgangslage: Es gibt praktisch keinen produktiven Datenbestand, der migriert werden müsste. Strukturelle Entscheidungen sind heute nahezu kostenlos und werden nach dem ersten produktiven Lieferanten teuer.

**ANNAHME.** Die Sekundärquellen sind rund drei Wochen alt. Ich nehme an, dass sich daran nichts Wesentliches geändert hat, weil in derselben Zeit ausweislich der git-Historie nur an UI-/Styleguide-Themen gearbeitet wurde. **Diese Annahme ist vor der Konzeptfreigabe durch einen Lauf der Abfragen in Abschnitt 4 zu bestätigen.**

**OFFEN.** Zeilenzahlen für `products`, `product_images`, `product_documents`, `brands`, `distributors`, `measures`, `customers`/`new_leads`, `inventories` sind in keiner Quelle genannt und damit unbekannt.

---

## 2 · Was der Auftrag fordert und was davon heute überhaupt messbar wäre

Abschnitt 5 des Auftrags nennt 27 Kennzahlen. Nicht alle sind auf dem heutigen Schema überhaupt bestimmbar — und **genau das ist bereits ein Datenqualitätsbefund**.

| Geforderte Kennzahl | Auf heutigem Schema messbar? | Begründung (BELEGT) |
|---|---|---|
| Anzahl Produkte / Lieferanten / Hersteller | ja | Tabellen `products`, `distributors`, `brands` vorhanden |
| Anzahl Preisdatensätze | ja | `distributor_prices` |
| Anzahl technischer Merkmale | **nur näherungsweise** | Es gibt kein generisches Merkmalsmodell; Merkmale liegen als Spalten in drei Generationen von Gerätetabellen (§3.3) |
| Anteil vollständiger Artikel | **nein** | „vollständig" ist nirgends definiert; es existiert kein Pflichtfeldkatalog |
| Anteil Dubletten | ja, aber mehrdeutig | Es gibt sechs konkurrierende Identitätsbegriffe (§3.1) — je nach gewähltem Schlüssel ein anderes Ergebnis |
| Anteil ohne Hersteller / Lieferant / Kategorie / Einheit / Preis | ja | über NULL-Prüfungen |
| **Anteil ohne Preisgültigkeit** | **nein — strukturell unmöglich** | `distributor_prices` hat kein Gültigkeitsfeld, nur `price_date` als Stichtag |
| Anteil ohne technische Daten | teilweise | nur je Gerätetyp gegen die jeweilige Spec-Tabelle |
| Anteil ohne Dokumente | ja | über `product_documents` |
| **Anteil ohne eindeutige Artikelidentität** | ja — und das ist die wichtigste Zahl | §3.1 |
| **Anteil veralteter Preise** | **nein** | ohne Gültigkeitszeitraum nicht definierbar; nur „älter als X" über `price_date` |
| **Anteil nicht mehr verfügbarer Artikel** | **nein** | es gibt kein Lebenszyklusfeld (§3.5) |
| Anteil nicht klassifizierter Produkte | ja | über `article_group` NULL |
| Mehrfach verwendete Herstellerartikelnummern | **ja, nach Festschreibung** | `products.article_no` **ist** laut Codekommentar die Hersteller-Artikelnummer (`SupplierProductImportService.php:251`) — die Bedeutung ist nur nicht verbindlich. Nach Festlegung (`10` §4a) messbar. |
| Widersprüchliche GTIN/EAN | ja | über GROUP BY `ean` |
| Widersprüchliche Einheiten | teilweise | `measures` ist eine reine Freitexttabelle |
| Widersprüchliche technische Werte | **nein** | ohne Herkunftskennzeichnung je Wert nicht entscheidbar, welcher Wert der widersprechende ist |

**BEWERTUNG.** **Sieben der 27 geforderten Kennzahlen sind auf dem heutigen Schema nicht bestimmbar** — nicht weil die Daten fehlen, sondern weil die Felder fehlen oder keine verbindliche Bedeutung haben: Preisgültigkeit, Lebenszyklus, Werteherkunft — und im Fall der Artikelnummern eine Bedeutung, die nur in einem Codekommentar steht. Das ist der kompakteste Beleg dafür, dass die geforderte Plattform eine strukturelle Erweiterung braucht und nicht nur eine Datenbereinigung.

---

## 3 · Strukturelle Datenqualität — selbst geprüft (BELEGT)

Das Folgende ist nicht aus Zeilenzahlen abgeleitet, sondern aus Schema und Schreibpfaden. Es ist damit **unabhängig vom heutigen Füllstand gültig** und beschreibt, welche Datenqualität das System künftig überhaupt zulassen kann.

### 3.1 Es gibt keinen Artikelschlüssel — nur Suchtexte

> **Nachtrag 2026-08-01:** Ursprünglich als *fünf* Pfade erfasst. Die Prüfung von `products.sku` hat einen sechsten ergeben (`HeatpumpSeeder`). Zugleich wurde eine im Code dokumentierte Bedeutung gefunden — `article_no` = Hersteller-Nr, `sku` = Lieferanten-Nr (`SupplierProductImportService.php:251-255`). Einordnung in `10-target-domain-model.md` §4a.

`products` trägt `article_no`, `ean`, `sku`, `model` — **ohne einen einzigen Unique- oder Index-Constraint** (`database/migrations/2023_06_22_085602_create_products_table.php`).

**Sechs Schreibpfade legen Artikel nach sechs verschiedenen Identitätsbegriffen an:**

| # | Schlüssel | Fundstelle |
|---|---|---|
| 1 | EAN | `app/Services/Suppliers/SupplierProductImportService.php:212` |
| 2 | `article_no` global | `…SupplierProductImportService.php:220`; `app/Http/Controllers/Product/IDS/gconline/IdsController.php:225` |
| 3 | `(distributor_id, article_no)` | `…SupplierProductImportService.php:228` |
| 4 | `(brand, model)` | `app/Services/Spec/SpecImportService.php:352` |
| 5 | Produktname | `app/Http/Controllers/Product/ProductImportController.php:135` |
| 6 | `sku` (setzt zugleich `article_no = sku`) | `database/seeders/HeatpumpSeeder.php:86-89` |

**Keiner dieser Pfade normalisiert** — kein `LOWER`, kein `TRIM`, keine Behandlung führender Nullen.

**BEWERTUNG.** Zwillinge entstehen nicht *vielleicht*, sondern zwangsläufig — und welcher Zwilling beim nächsten Importlauf getroffen wird, hängt davon ab, über welchen Weg importiert wird. Jede Deduplizierung, jede Preiszuordnung und jede Mediensammlung baut auf einem Fundament, das keine Identität kennt. Der einzige Baustein, der es richtig macht, ist `supplier_article_map` mit dem Unique `(hersteller, herst_artikelnr, supplier_channel)` (`database/migrations/2026_07_04_140007_create_supplier_article_map_table.php`) — dieser greift aber nur für importierte Lieferantenzeilen und wird übersprungen, sobald `products.brand_id` fehlt (`app/Services/Suppliers/Mappers/IdsMapper.php:35-39`).

Ergänzend: Es gibt an `products` **kein Feld für die Herstellerartikelnummer**. Die für IDS und Open Masterdata zwingende Trennung von Hersteller-, Lieferanten- und interner Nummer existiert im Stamm nicht.

### 3.2 Preise ohne Zeitachse, vierfach abgelegt

**BELEGT.** `distributor_prices` kennt nur `price_date` als Stichtag (`database/migrations/2023_10_16_141346_create_distributor_prices_table.php:28`) und wird per `updateOrCreate` überschrieben (`app/Http/Controllers/Product/ProductImportController.php:189`; `…/IdsController.php:264`). Es gibt keine Historie, keine Von/Bis-Gültigkeit, keine Staffel, keine Währung, keine numerische Preisbezugsmenge.

Derselbe Preis existiert an **vier** Orten:

1. `products.retail_price` / `products.purchase_price`
2. `distributor_prices.price` / `.purchase_price`
3. `supplier_article_map.ek_preis` / `.vk_preis`
4. `master_set_components.unit_price` / `.purchase_price` — nochmals kopiert nach `master_set_cart_items`

**BEWERTUNG.** Nach einem Lieferantenimport ist weder rekonstruierbar, welcher Preis wann galt, noch auf welchem Preisstand eine Angebotsposition beruht. Die Genauigkeit `decimal(10,2)` unterschreitet zusätzlich die von IDS 2.5.1 geforderten vier Nachkommastellen (`tgDecimal_10_4` im Schema `warenkorb_empfangen_2_5_1.xsd`).

### 3.3 Technische Merkmale dreifach modelliert, ohne Einheitenmodell

**BELEGT.** Drei Generationen laufen parallel:

- breite Gerätetabellen (2024): `product_p_v_s`, `product_w_p_s`, `radiators`, `inverters`, `batteries`
- normierte Spec-Tabellen (2026): `product_pv_module_specs`, `product_heat_pump_specs`, `product_radiator_specs`
- Merkmale direkt am Stamm: `products.scop`, `.refrigerant`, `.phase_count` (`database/migrations/2025_08_26_075553_*`)

`inverters` und `batteries` werden von **beiden** Generationen beschrieben (`app/Services/Spec/SpecSchema.php:59-65`). Die einzige Einheitentabelle `measures` hat genau ein Freitextfeld und wächst per `firstOrCreate` beim Import (`…/IdsController.php:220`). `radiators` trägt Wechselrichter-Felder.

**BEWERTUNG.** Dieselbe Produktzeile kann aus zwei Quellen mit zwei Konventionen beschrieben sein, ohne dass entscheidbar ist, welcher Wert gilt. Für berechnungsrelevante Werte — und darum geht es bei Auslegungstools — ist das der kritischste Befund nach der Artikelidentität.

### 3.4 Medien ohne Metadaten

**BELEGT.** `product_images` = `product_id` + `name` + `image` (`database/migrations/2023_08_09_084555_*`). `product_documents` = `product_id` + `title` + `document` (`2023_08_11_062919_*`). Kein Typcode, kein Hash, keine Sprache, keine Herkunft, keine Version, kein Gültigkeitsdatum, keine Sortierung, kein Unique.

Der Importer lädt Bilder per URL herunter und schreibt sie **ohne Hash-Prüfung** (`…/ProductImportController.php:212-230`); der eine Metadatenwert, der gesetzt werden soll, wird durch einen `$fillable`-Fehler in `app/Models/ProductImage.php:11` stillschweigend verworfen.

**BEWERTUNG.** Keine der Fragen, die eine Produktdatenplattform beantworten muss, ist beantwortbar: *Welches ist das aktuelle Datenblatt? Ist dieses Bild schon vorhanden? Welches Bild hat der Nutzer gepflegt, welches kam vom Lieferanten?* Bemerkenswert: Das Repository besitzt mit `customer_product_info_media` (`2026_02_04_110832_*`) bereits ein deutlich besseres Medienmodell — es hängt nur am Kundengerät statt am Produkt.

### 3.5 Kein Lebenszyklus, dafür flächendeckendes `onDelete('cascade')`

**BELEGT.** Es gibt kein Feld für Auslauf, Nachfolger oder Ersatzartikel — die Suche nach den einschlägigen Begriffen liefert im gesamten Produktbereich null Treffer. `products.status` trägt gleichzeitig drei Wertwelten: `'active'` (`2023_06_22_085602_*:32`), `'Published'` (`…/IdsController.php:229`) und `1` (`…SupplierProductImportService.php:286`).

Wer einen ausgelaufenen Artikel entfernen will, hat nur das Löschen — und dann greifen `cascade`-Regeln auf `distributor_prices`, `product_images`, `product_documents`, `product_histories`, `master_set_components`, `add_product_to_sets`, `product_sub_sets`, `activity_articles`, `stamp_article_list_items`, `inventories`.

**BEWERTUNG.** Ein einziger Löschvorgang entfernt Preisstand, Medien, Änderungshistorie und Stücklistenpositionen gleichzeitig. In Verbindung mit dem fehlenden Lebenszyklus ist das ein Datenverlustrisiko im Normalbetrieb, nicht im Fehlerfall.

### 3.6 Referenzielle Defekte (Auswahl, alle BELEGT)

| Defekt | Fundstelle |
|---|---|
| `heat_pumps.product_id`, `solar_systems.product_id`, `customer_product_infos.product_id`, `product_formulas.product_id` zeigen per FK auf **`article_groups`**, nicht auf `products` | jeweilige Create-Migration |
| Model `CustomerProduct` deklariert Tabelle `customer_products` — **existiert nicht** | `app/Models/CustomerProduct.php:13` |
| `Product::favorites()` und `Product::stampArticles()` zeigen auf `product_favorites` / `stamp_articles` — **beide Tabellen existieren nicht** | `app/Models/Product.php:147,152,157-168` |
| `Product::subArticle()` referenziert `SubArticle::class` — das Model heißt `SubArticleGroup` | `app/Models/Product.php:122-125` |
| `ProductImportController` schreibt `$product->discount_price` — Spalte existiert auf `products` nicht | `…/ProductImportController.php:157` |
| `DatanormController::parseFile()` rendert `view('datanorm.upload')` — View existiert nur als `admin.datanorm.upload` ⇒ **jeder Upload endet in einer Exception** | `app/Http/Controllers/DatanormController.php:47` |
| Route `ids.search.forward.inline` zeigt auf `IdsSearchController::forwardToShopInline` — **Methode existiert nicht** | `routes/web.php:520` |

---

## 4 · Reproduzierbare Prüfabfragen (Q)

**Diese Abfragen sind ausschließlich lesend.** Kein `INSERT`, kein `UPDATE`, kein `DELETE`, kein `ALTER`. Sie liegen zusätzlich als Datei unter `docs/product-data/evidence/query-results/datenqualitaet-pruefabfragen.sql`.

**Vor dem ersten Lauf:** Spaltennamen einmal gegenprüfen mit
`SHOW COLUMNS FROM products;` und `SHOW COLUMNS FROM distributor_prices;`
Die Abfragen sind gegen das Schema **aus den Migrationen** geschrieben; sollte der Live-Stand abweichen, ist das selbst ein Befund und gehört ins Protokoll.

Ausführung wahlweise:

```bash
mysql -h 127.0.0.1 -P 3307 -u <user> -p ticket < datenqualitaet-pruefabfragen.sql > ergebnis.txt
# oder
php artisan tinker
```

### 4.1 Mengengerüst

```sql
SELECT 'products'            AS tabelle, COUNT(*) AS zeilen FROM products
UNION ALL SELECT 'brands',              COUNT(*) FROM brands
UNION ALL SELECT 'distributors',        COUNT(*) FROM distributors
UNION ALL SELECT 'distributor_prices',  COUNT(*) FROM distributor_prices
UNION ALL SELECT 'product_images',      COUNT(*) FROM product_images
UNION ALL SELECT 'product_documents',   COUNT(*) FROM product_documents
UNION ALL SELECT 'article_groups',      COUNT(*) FROM article_groups
UNION ALL SELECT 'sub_article_groups',  COUNT(*) FROM sub_article_groups
UNION ALL SELECT 'measures',            COUNT(*) FROM measures
UNION ALL SELECT 'inventories',         COUNT(*) FROM inventories
UNION ALL SELECT 'supplier_connections',COUNT(*) FROM supplier_connections
UNION ALL SELECT 'supplier_article_map',COUNT(*) FROM supplier_article_map
UNION ALL SELECT 'supplier_import_logs',COUNT(*) FROM supplier_import_logs
UNION ALL SELECT 'imported_ids_items',  COUNT(*) FROM imported_ids_items;
```

### 4.2 Vollständigkeit des Artikelstamms

```sql
SELECT
  COUNT(*)                                                            AS artikel_gesamt,
  SUM(article_no IS NULL OR TRIM(article_no) = '')                     AS ohne_artikelnummer,
  SUM(ean         IS NULL OR TRIM(ean)        = '')                    AS ohne_ean,
  SUM(brand_id    IS NULL)                                             AS ohne_hersteller,
  SUM(article_group IS NULL)                                           AS ohne_warengruppe,
  SUM(measure_unit  IS NULL)                                           AS ohne_einheit,
  SUM(short_description IS NULL OR TRIM(short_description) = '')       AS ohne_beschreibung,
  SUM( (article_no IS NULL OR TRIM(article_no)='')
   AND (ean IS NULL OR TRIM(ean)='') )                                 AS ohne_jede_identitaet
FROM products;
```

### 4.3 Dubletten — je Identitätsbegriff getrennt

Der wichtigste Block. Weil sechs Schreibpfade sechs Schlüssel benutzen (§3.1), muss je Schlüssel gemessen werden.

```sql
-- (a) nach normalisierter Artikelnummer
SELECT LOWER(TRIM(article_no)) AS schluessel, COUNT(*) AS n
FROM products WHERE article_no IS NOT NULL AND TRIM(article_no) <> ''
GROUP BY 1 HAVING n > 1 ORDER BY n DESC;

-- (b) nach EAN
SELECT TRIM(ean) AS ean, COUNT(*) AS n, COUNT(DISTINCT brand_id) AS verschiedene_hersteller
FROM products WHERE ean IS NOT NULL AND TRIM(ean) <> ''
GROUP BY 1 HAVING n > 1 ORDER BY n DESC;

-- (c) nach Hersteller + Modell
SELECT brand_id, LOWER(TRIM(model)) AS modell, COUNT(*) AS n
FROM products WHERE model IS NOT NULL AND TRIM(model) <> ''
GROUP BY 1,2 HAVING n > 1 ORDER BY n DESC;

-- (d) nach Produktname
SELECT LOWER(TRIM(product)) AS name, COUNT(*) AS n
FROM products GROUP BY 1 HAVING n > 1 ORDER BY n DESC;

-- (e) Kernfrage: gleiche EAN, verschiedene Hersteller  → echter Konflikt
SELECT TRIM(ean) AS ean, GROUP_CONCAT(DISTINCT brand_id) AS hersteller_ids, COUNT(*) AS n
FROM products WHERE ean IS NOT NULL AND TRIM(ean) <> ''
GROUP BY 1 HAVING COUNT(DISTINCT brand_id) > 1;
```

### 4.4 GTIN/EAN-Plausibilität

```sql
SELECT
  COUNT(*)                                                     AS mit_ean,
  SUM(CHAR_LENGTH(TRIM(ean)) NOT IN (8,12,13,14))              AS falsche_laenge,
  SUM(TRIM(ean) REGEXP '[^0-9]')                               AS enthaelt_nicht_ziffern,
  SUM(TRIM(ean) REGEXP '^0+$')                                 AS nur_nullen
FROM products WHERE ean IS NOT NULL AND TRIM(ean) <> '';
```
*Prüfziffernberechnung nach GS1 ist in reinem SQL unhandlich; sie gehört in ein Kommando und ist als eigener Prüfschritt vorzusehen.*

### 4.5 Preisqualität

```sql
SELECT
  COUNT(*)                                                     AS preiszeilen,
  COUNT(DISTINCT product_id)                                   AS artikel_mit_preis,
  COUNT(DISTINCT distributor_id)                               AS lieferanten,
  SUM(price IS NULL AND purchase_price IS NULL)                AS ohne_jeden_preis,
  SUM(purchase_price IS NULL)                                  AS ohne_ek,
  SUM(price          IS NULL)                                  AS ohne_vk,
  SUM(price_date     IS NULL)                                  AS ohne_preisdatum,
  SUM(price_date < DATE_SUB(CURDATE(), INTERVAL 180 DAY))      AS aelter_als_180_tage,
  SUM(purchase_price <= 0)                                     AS ek_null_oder_negativ,
  SUM(price > 0 AND purchase_price > 0 AND purchase_price > price) AS ek_groesser_vk
FROM distributor_prices;

-- Artikel ohne jeden Lieferantenpreis
SELECT COUNT(*) AS artikel_ohne_preis
FROM products p LEFT JOIN distributor_prices dp ON dp.product_id = p.id
WHERE dp.id IS NULL;

-- Preiswidersprüche zwischen den vier Ablageorten (§3.2)
SELECT COUNT(*) AS stamm_weicht_von_lieferantenpreis_ab
FROM products p JOIN distributor_prices dp ON dp.product_id = p.id
WHERE p.purchase_price IS NOT NULL AND dp.purchase_price IS NOT NULL
  AND ABS(p.purchase_price - dp.purchase_price) > 0.005;
```

### 4.6 Einheiten

```sql
SELECT m.id, m.measure, COUNT(p.id) AS verwendungen
FROM measures m LEFT JOIN products p ON p.measure_unit = m.id
GROUP BY 1,2 ORDER BY verwendungen DESC;

-- Verdacht auf Dubletten durch Freitext (Stk / Stück / STK / ST)
SELECT LOWER(TRIM(measure)) AS normalisiert, COUNT(*) AS n, GROUP_CONCAT(measure) AS schreibweisen
FROM measures GROUP BY 1 HAVING n > 1;
```

### 4.7 Medien und Dokumente

```sql
SELECT
  (SELECT COUNT(*) FROM products)                                          AS artikel,
  (SELECT COUNT(DISTINCT product_id) FROM product_images)                  AS artikel_mit_bild,
  (SELECT COUNT(DISTINCT product_id) FROM product_documents)               AS artikel_mit_dokument,
  (SELECT COUNT(*) FROM product_images)                                    AS bilder_gesamt,
  (SELECT COUNT(*) FROM product_documents)                                 AS dokumente_gesamt;

-- Verdacht auf doppelte Bilder (kein Unique, kein Hash — §3.4)
SELECT product_id, image, COUNT(*) AS n
FROM product_images GROUP BY 1,2 HAVING n > 1;

-- Verwaiste Medien
SELECT COUNT(*) AS bilder_ohne_artikel
FROM product_images pi LEFT JOIN products p ON p.id = pi.product_id WHERE p.id IS NULL;
```

### 4.8 Statuswerte — Beleg für §3.5

```sql
SELECT status, COUNT(*) AS n FROM products GROUP BY 1 ORDER BY n DESC;
SELECT status, COUNT(*) AS n FROM distributor_prices GROUP BY 1 ORDER BY n DESC;
```
*Erwartung nach Codebefund: mindestens drei Wertwelten (`active`, `Published`, `1`). Trifft das zu, ist §3.5 auch datenseitig belegt.*

### 4.9 Technische Merkmale je Gerätetyp

```sql
SELECT 'product_heat_pump_specs' AS tabelle, COUNT(*) AS zeilen,
       COUNT(DISTINCT product_id) AS artikel FROM product_heat_pump_specs
UNION ALL SELECT 'product_pv_module_specs', COUNT(*), COUNT(DISTINCT product_id) FROM product_pv_module_specs
UNION ALL SELECT 'inverters',               COUNT(*), COUNT(DISTINCT product_id) FROM inverters
UNION ALL SELECT 'batteries',               COUNT(*), COUNT(DISTINCT product_id) FROM batteries;

-- Doppelbeschreibung: von zwei Generationen erfasst (§3.3)
SELECT COUNT(*) AS wechselrichter_in_beiden_generationen
FROM inverters i JOIN products p ON p.id = i.product_id
WHERE p.scop IS NOT NULL OR p.refrigerant IS NOT NULL;
```

### 4.10 Herkunft und Prüfstand

```sql
SELECT imported_from, verifikations_status, COUNT(*) AS n
FROM products GROUP BY 1,2 ORDER BY n DESC;
```
*Setzt die Migrationsweiche M-A aus `docs/spec-import/00-spec-standard.md:179` voraus. Schlägt die Abfrage fehl, ist M-A produktiv noch nicht gezogen — auch das ist ein Befund.*

---

## 5 · Datenschutz- und Sicherheitsrisiken im Datenbestand

**BELEGT.**

| Risiko | Fundstelle | Bewertung |
|---|---|---|
| **IDS-Zugangsdaten im Klartext** in `.env` (`IDS_KNDNR`, `IDS_USERNAME`, `IDS_PASSWORD`) und im HTML-Quelltext der Weiterleitungsseite gerendert | `config/services.php:77-82`; `app/Http/Controllers/Product/IDS/gconline/IdsSearchController.php:150-168` | Jeder angemeldete Nutzer sieht das Großhandelspasswort über „Quelltext anzeigen" |
| **Hart eincodierter Testzugang** (Kundennummer, Benutzername, Passwort) im Markup | `resources/views/admin/product/ids/ids.blade.php:70-78` | View ist unverlinkt, liegt aber im Repository und in jedem Backup |
| **Roh-Payload unverschlüsselt** in `supplier_import_logs.payload` | `app/Services/Suppliers/SupplierConnectorService.php:319`; Model-Cast `'array'` statt `'encrypted:array'` | Enthält vollständige Warenkörbe inkl. Einkaufspreisen — vertrauliche kaufmännische Daten |
| **Kompletter XML-Body im Anwendungslog** | `…/IdsController.php:32-35` | Einkaufspreise in `storage/logs` |
| **`request_config` unverschlüsselt**, obwohl dort `client_secret` liegen soll | `app/Models/SupplierConnection.php:34-47` vs. `app/Services/Suppliers/Omd/OmdAuthService.php:89-93` | OAuth-Geheimnis im Klartext |
| **Keine Aufbewahrungs-/Löschfrist** für `supplier_import_logs` | kein Pruning-Command gefunden | wächst unbegrenzt mit vertraulichen Inhalten |

**Korrekt gelöst — ausdrücklich festgehalten:** Die Credential-Felder von `supplier_connections` sind über `encrypted`-Casts verschlüsselt, und die Migration zieht den Spaltentyp korrekt mit (`2026_05_26_084701_*:31-35`, nachgebessert durch `2026_05_27_083225_*:11`). Das ist der häufigste Fehler an dieser Stelle und er wurde vermieden.

**Personenbezogene Daten:** Im Produkt- und Beschaffungsbereich sind keine personenbezogenen Daten enthalten. Der Kundenbestand liegt in `new_leads` und ist nicht Gegenstand dieses Berichts.

---

## 6 · Bewertung des Datenqualitätsstands

**BEWERTUNG.** Die Frage „wie gut sind die Daten" lässt sich für dieses System heute noch gar nicht sinnvoll stellen — weil der Beschaffungsstrang leer ist und der Artikelstamm keine Identität kennt. Der Befund lautet nicht *„die Daten sind schlecht"*, sondern:

> **Das Schema erlaubt keine messbare Datenqualität.** Sieben der 27 geforderten Kennzahlen sind strukturell nicht bestimmbar, und der wichtigste Schlüsselbegriff — die Artikelidentität — existiert in sechs konkurrierenden Ausprägungen ohne einen einzigen Constraint.

Daraus folgt für die Reihenfolge des Vorhabens: **Eine Datenbereinigung vor der Strukturkorrektur wäre verlorene Arbeit.** Die Identität muss zuerst definiert und erzwungen werden, sonst erzeugt der nächste Import dieselben Zwillinge erneut. Das ist der einzige Punkt, an dem ich der im Auftrag vorgeschlagenen Roadmap-Reihenfolge (Stufe 1 Bereinigung vor Stufe 2 Produktkern) fachlich widerspreche.

**Günstiger Umstand:** Weil der Bestand fast leer ist, kostet die Strukturkorrektur heute nahezu keine Migration.

---

## 7 · Offene Punkte für den fachlichen Prüfer

1. **Alle Zahlen in Abschnitt 1 sind Sekundärbelege.** Die Abfragen aus Abschnitt 4 müssen einmal gelaufen sein, bevor das Konzept freigegeben wird.
2. **Definition „vollständiger Artikel"** fehlt. Ohne Pflichtfeldkatalog ist die Kennzahl nicht bestimmbar — der Katalog ist eine fachliche Festlegung des Auftraggebers, keine technische.
3. **Welcher Identitätsbegriff soll führen?** Die Antwort bestimmt die Dublettenzahl, den Migrationsaufwand und das gesamte Zielmodell.
4. **Gilt die Live-DB als maßgeblich oder eine Testkopie?** `APP_ENV=local`, `DB_DATABASE=ticket`, daneben existiert `ticket_testing` (`docs/spec-import/00-spec-standard.md`).
5. **Zweite Datenbank `WP_DB_*`** ist in `.env` konfiguriert und in dieser Inventur nicht betrachtet. Enthält sie produktbezogene Daten?
6. **Zweiter Lieferantenstack außerhalb dieses Repositories** (App „wberechnung", mit real angebundenem OMD und ETIM-Merkmalen, belegt in `docs/heizkoerper-bestandsanalyse.md:51-54,155-157`). Ob konsolidiert werden soll, ist eine Grundsatzentscheidung vor jedem Zielmodell.
