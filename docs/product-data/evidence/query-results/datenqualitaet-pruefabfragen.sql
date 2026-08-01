-- ============================================================================
-- Datenqualitaets-Pruefabfragen · Produktdatenplattform ticket
-- Phase A (Analyse) · Stand 2026-07-30
--
-- AUSSCHLIESSLICH LESEND. Kein INSERT, UPDATE, DELETE, ALTER, DROP.
-- Gehoert zu docs/product-data/03-data-quality-report.md
--
-- Ausfuehren:
--   mysql -h 127.0.0.1 -P 3307 -u <user> -p ticket < datenqualitaet-pruefabfragen.sql > ergebnis.txt
--
-- VOR DEM ERSTEN LAUF: Spaltennamen gegenpruefen.
--   SHOW COLUMNS FROM products;
--   SHOW COLUMNS FROM distributor_prices;
-- Die Abfragen sind gegen das Schema AUS DEN MIGRATIONEN geschrieben.
-- Weicht der Live-Stand ab, ist das selbst ein Befund und gehoert ins Protokoll.
-- ============================================================================

SELECT '=== 1 · MENGENGERUEST ===' AS abschnitt;

SELECT 'products'             AS tabelle, COUNT(*) AS zeilen FROM products
UNION ALL SELECT 'brands',               COUNT(*) FROM brands
UNION ALL SELECT 'distributors',         COUNT(*) FROM distributors
UNION ALL SELECT 'distributor_prices',   COUNT(*) FROM distributor_prices
UNION ALL SELECT 'product_images',       COUNT(*) FROM product_images
UNION ALL SELECT 'product_documents',    COUNT(*) FROM product_documents
UNION ALL SELECT 'article_groups',       COUNT(*) FROM article_groups
UNION ALL SELECT 'sub_article_groups',   COUNT(*) FROM sub_article_groups
UNION ALL SELECT 'measures',             COUNT(*) FROM measures
UNION ALL SELECT 'inventories',          COUNT(*) FROM inventories
UNION ALL SELECT 'supplier_connections', COUNT(*) FROM supplier_connections
UNION ALL SELECT 'supplier_article_map', COUNT(*) FROM supplier_article_map
UNION ALL SELECT 'supplier_import_logs', COUNT(*) FROM supplier_import_logs
UNION ALL SELECT 'imported_ids_items',   COUNT(*) FROM imported_ids_items;


SELECT '=== 2 · VOLLSTAENDIGKEIT ARTIKELSTAMM ===' AS abschnitt;

SELECT
  COUNT(*)                                                      AS artikel_gesamt,
  SUM(article_no IS NULL OR TRIM(article_no) = '')              AS ohne_artikelnummer,
  SUM(ean        IS NULL OR TRIM(ean)        = '')              AS ohne_ean,
  SUM(brand_id      IS NULL)                                    AS ohne_hersteller,
  SUM(article_group IS NULL)                                    AS ohne_warengruppe,
  SUM(measure_unit  IS NULL)                                    AS ohne_einheit,
  SUM(short_description IS NULL OR TRIM(short_description) = '') AS ohne_beschreibung,
  SUM((article_no IS NULL OR TRIM(article_no) = '')
      AND (ean IS NULL OR TRIM(ean) = ''))                      AS ohne_jede_identitaet
FROM products;


SELECT '=== 3 · DUBLETTEN JE IDENTITAETSBEGRIFF ===' AS abschnitt;
-- Fuenf Schreibpfade benutzen fuenf Schluessel (siehe 03-data-quality-report.md §3.1).
-- Deshalb je Schluessel getrennt messen.

SELECT '3a · nach normalisierter Artikelnummer' AS pruefung;
SELECT LOWER(TRIM(article_no)) AS schluessel, COUNT(*) AS n
FROM products
WHERE article_no IS NOT NULL AND TRIM(article_no) <> ''
GROUP BY 1 HAVING n > 1 ORDER BY n DESC LIMIT 100;

SELECT '3b · nach EAN' AS pruefung;
SELECT TRIM(ean) AS ean, COUNT(*) AS n, COUNT(DISTINCT brand_id) AS verschiedene_hersteller
FROM products
WHERE ean IS NOT NULL AND TRIM(ean) <> ''
GROUP BY 1 HAVING n > 1 ORDER BY n DESC LIMIT 100;

SELECT '3c · nach Hersteller + Modell' AS pruefung;
SELECT brand_id, LOWER(TRIM(model)) AS modell, COUNT(*) AS n
FROM products
WHERE model IS NOT NULL AND TRIM(model) <> ''
GROUP BY 1,2 HAVING n > 1 ORDER BY n DESC LIMIT 100;

SELECT '3d · nach Produktname' AS pruefung;
SELECT LOWER(TRIM(product)) AS name, COUNT(*) AS n
FROM products GROUP BY 1 HAVING n > 1 ORDER BY n DESC LIMIT 100;

SELECT '3e · KERNFRAGE: gleiche EAN, verschiedene Hersteller (echter Konflikt)' AS pruefung;
SELECT TRIM(ean) AS ean, GROUP_CONCAT(DISTINCT brand_id) AS hersteller_ids, COUNT(*) AS n
FROM products
WHERE ean IS NOT NULL AND TRIM(ean) <> ''
GROUP BY 1 HAVING COUNT(DISTINCT brand_id) > 1;


SELECT '=== 4 · GTIN/EAN-PLAUSIBILITAET ===' AS abschnitt;
-- Pruefziffernberechnung nach GS1 gehoert in ein Artisan-Kommando, nicht in SQL.

SELECT
  COUNT(*)                                        AS mit_ean,
  SUM(CHAR_LENGTH(TRIM(ean)) NOT IN (8,12,13,14)) AS falsche_laenge,
  SUM(TRIM(ean) REGEXP '[^0-9]')                  AS enthaelt_nicht_ziffern,
  SUM(TRIM(ean) REGEXP '^0+$')                    AS nur_nullen
FROM products WHERE ean IS NOT NULL AND TRIM(ean) <> '';


SELECT '=== 5 · PREISQUALITAET ===' AS abschnitt;

SELECT
  COUNT(*)                                                        AS preiszeilen,
  COUNT(DISTINCT product_id)                                      AS artikel_mit_preis,
  COUNT(DISTINCT distributor_id)                                  AS lieferanten,
  SUM(price IS NULL AND purchase_price IS NULL)                   AS ohne_jeden_preis,
  SUM(purchase_price IS NULL)                                     AS ohne_ek,
  SUM(price IS NULL)                                              AS ohne_vk,
  SUM(price_date IS NULL)                                         AS ohne_preisdatum,
  SUM(price_date < DATE_SUB(CURDATE(), INTERVAL 180 DAY))         AS aelter_als_180_tage,
  SUM(purchase_price <= 0)                                        AS ek_null_oder_negativ,
  SUM(price > 0 AND purchase_price > 0 AND purchase_price > price) AS ek_groesser_vk
FROM distributor_prices;

SELECT 'Artikel ohne jeden Lieferantenpreis' AS pruefung;
SELECT COUNT(*) AS artikel_ohne_preis
FROM products p LEFT JOIN distributor_prices dp ON dp.product_id = p.id
WHERE dp.id IS NULL;

SELECT 'Preiswiderspruch Stamm vs. Lieferantenpreis (vier Ablageorte, §3.2)' AS pruefung;
SELECT COUNT(*) AS stamm_weicht_ab
FROM products p JOIN distributor_prices dp ON dp.product_id = p.id
WHERE p.purchase_price IS NOT NULL AND dp.purchase_price IS NOT NULL
  AND ABS(p.purchase_price - dp.purchase_price) > 0.005;


SELECT '=== 6 · EINHEITEN ===' AS abschnitt;

SELECT m.id, m.measure, COUNT(p.id) AS verwendungen
FROM measures m LEFT JOIN products p ON p.measure_unit = m.id
GROUP BY 1,2 ORDER BY verwendungen DESC;

SELECT 'Freitext-Dubletten (Stk / Stueck / STK / ST)' AS pruefung;
SELECT LOWER(TRIM(measure)) AS normalisiert, COUNT(*) AS n,
       GROUP_CONCAT(measure) AS schreibweisen
FROM measures GROUP BY 1 HAVING n > 1;


SELECT '=== 7 · MEDIEN UND DOKUMENTE ===' AS abschnitt;

SELECT
  (SELECT COUNT(*) FROM products)                            AS artikel,
  (SELECT COUNT(DISTINCT product_id) FROM product_images)    AS artikel_mit_bild,
  (SELECT COUNT(DISTINCT product_id) FROM product_documents) AS artikel_mit_dokument,
  (SELECT COUNT(*) FROM product_images)                      AS bilder_gesamt,
  (SELECT COUNT(*) FROM product_documents)                   AS dokumente_gesamt;

SELECT 'Doppelte Bilder (kein Unique, kein Hash — §3.4)' AS pruefung;
SELECT product_id, image, COUNT(*) AS n
FROM product_images GROUP BY 1,2 HAVING n > 1 LIMIT 100;

SELECT 'Verwaiste Medien' AS pruefung;
SELECT COUNT(*) AS bilder_ohne_artikel
FROM product_images pi LEFT JOIN products p ON p.id = pi.product_id WHERE p.id IS NULL;
SELECT COUNT(*) AS dokumente_ohne_artikel
FROM product_documents pd LEFT JOIN products p ON p.id = pd.product_id WHERE p.id IS NULL;


SELECT '=== 8 · STATUSWERTE (Beleg fuer §3.5) ===' AS abschnitt;
-- Erwartung nach Codebefund: mindestens drei Wertwelten (active, Published, 1).

SELECT status, COUNT(*) AS n FROM products GROUP BY 1 ORDER BY n DESC;
SELECT status, COUNT(*) AS n FROM distributor_prices GROUP BY 1 ORDER BY n DESC;


SELECT '=== 9 · TECHNISCHE MERKMALE JE GERAETETYP ===' AS abschnitt;

SELECT 'product_heat_pump_specs' AS tabelle, COUNT(*) AS zeilen,
       COUNT(DISTINCT product_id) AS artikel FROM product_heat_pump_specs
UNION ALL SELECT 'product_pv_module_specs', COUNT(*), COUNT(DISTINCT product_id) FROM product_pv_module_specs
UNION ALL SELECT 'inverters',               COUNT(*), COUNT(DISTINCT product_id) FROM inverters
UNION ALL SELECT 'batteries',               COUNT(*), COUNT(DISTINCT product_id) FROM batteries;

SELECT 'Doppelbeschreibung durch zwei Generationen (§3.3)' AS pruefung;
SELECT COUNT(*) AS wechselrichter_in_beiden_generationen
FROM inverters i JOIN products p ON p.id = i.product_id
WHERE p.scop IS NOT NULL OR p.refrigerant IS NOT NULL;


SELECT '=== 10 · HERKUNFT UND PRUEFSTAND ===' AS abschnitt;
-- Setzt Migrationsweiche M-A voraus (docs/spec-import/00-spec-standard.md:179).
-- Schlaegt die Abfrage fehl, ist M-A produktiv noch nicht gezogen — auch das ist ein Befund.

SELECT imported_from, verifikations_status, COUNT(*) AS n
FROM products GROUP BY 1,2 ORDER BY n DESC;

-- ============================================================================
-- ENDE
-- ============================================================================
