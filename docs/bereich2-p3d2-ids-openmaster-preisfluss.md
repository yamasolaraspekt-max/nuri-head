# P3-d2-ids-openmaster-preisfluss — IDS/OpenMaster-Preisfluss-Inventur (READ-ONLY)

> **Status:** read-only Inventur + Bewertung. **Nur lesen. Keine Preise erfinden, keine Preis-Seeder, keine Katalog-/DB-Änderung, keine Migration, kein Commit, kein Push.** SENSIBEL nur referenziert. Keine Annahme ohne Beleg.
> **Zweck:** Klären, wie EK/VK fachlich über IDS/OpenMaster (OMD) kommen und wie diese Preisquelle mit WP-Produkten / `product_id` / Artikelnummern / Set-Komponenten verbunden wird.
> **Bezug:** P3-d1/P3-d2-preisquelle · Datum 2026-07-13.

---

## 1. Vorhandene IDS/OMD-Architektur (belegt)

**Connector / Import / Mapper (Code vorhanden):**
- `app/Services/Suppliers/SupplierConnectorService.php`, `SupplierProductImportService.php`, `SupplierConnectionTestService.php`.
- `app/Services/Suppliers/Omd/OmdClient.php`, `OmdAuthService.php`.
- Mapper: `Mappers/{OmdMapper, IdsMapper, DatanormMapper, SupplierArticleMapper, MapperRegistry}.php`.
- UI/Controller: `Product/IDS/SupplierConnectionController`, `Product/IDS/gconline/{IdsController, IdsSearchController}`, `Product/Distributor/DistributorPriceController`, `Product/MasterSet/MasterSetDistributorCompareController`, `Customer/Offer/OfferSupplierSearchController`.
- Events: `IdsItemsImported`, `OfferSupplierProductsReturned`. Import-Log/Rohdaten: `SupplierImportLog`, `ImportedIdsItem`, `spec_import_batches`.

**Preis-/Identitäts-Tabellen:**
- **`distributor_prices`** = Lieferanten-Preis-Wahrheit: `distributor_id`, `product_id`, `article_no`, **`purchase_price` (EK)**, **`price` (VK)**, `discount_price/percent`, **`price_date`**, `status`. → **EK und VK unterscheidbar, mit Datum.**
- **`supplier_article_map`** = neutrale Identität: `hersteller` + Hersteller-Artikelnr + `distributor_id` → `product_id`.
- **`master_set_components`** = Set-Anker **mit Preis-Spiegel:** `product_id`, `distributor_id`, **`distributor_price_id`** (FK auf `distributor_prices`), `article_no`, `distributor_article_no`, **`unit_price`/`purchase_price` (Snapshot)**.

**Preisfluss (Soll laut Code):**
`OMD/IDS Import` → `SupplierProductImportService` (schreibt `products` inkl. `article_no`=Hersteller-Artikelnr [:251], `distributor_prices` via `DistributorPrice::updateOrCreate` [:373]) → Auswahl je Set via `MasterSetDistributorCompareController` (setzt `distributor_price_id` [:29] + spiegelt Preis in die Komponente) → **`CatalogPriceGuard` liest den Komponenten-Snapshot** (`master_set_components.unit_price`/`purchase_price` [:66-70]).

## 2. Ist-Zustand (belegt) — Pipeline vorhanden, aber inert
| Prüfung | Wert |
|---|---|
| `supplier_connections` (konfigurierte OMD/IDS-Verbindung) | **0** |
| `supplier_article_map` (Identitäts-Map) | **0** |
| `supplier_import_logs` / `imported_ids_items` (reale Importe) | **0 / 0** |
| `distributor_prices` | 88 Zeilen, **creator_id/notice = NULL → Demo** (`DemoPartnerProfileSeeder`), Distributoren 25–33 (Demo) |
| WP-Preise (ag=2) in `distributor_prices` | nur Demo 9/10/11 (ohne Specs) |
| Spec-Produkte 101–119: `article_no`/`sku`/`ean` | **fehlen** (nur `model`+`brand`) |

→ Der OMD/IDS-Weg ist **gebaut, aber nie real gelaufen**: keine Verbindung, kein Import, keine Identitäts-Map; die einzigen Preise sind Demo; die realen WP-Geräte tragen **keine Hersteller-Artikelnummer** (der Match-Schlüssel).

## 3. Antworten auf die 10 Fragen
1. **IDS/OMD-Code/Importer/Tabellen/Services/Controller?** — **Ja, vollständig** (Connector, OmdClient, Mapper, Import-Service, IDS-UI, Distributor-Preis-UI, Set↔Distributor-Compare). Aber **0 aktive Verbindungen/Importe.**
2. **Welche Tabellen enthalten EK/VK?** — **`distributor_prices`** (`purchase_price`=EK, `price`=VK, dat.); gespiegelt in `master_set_components` (`purchase_price`/`unit_price`).
3. **Artikelnummern/EAN/SKU?** — `products.article_no` (=Hersteller-Artikelnr laut Import [:251]), `products.sku`, `products.ean`; `supplier_article_map.hersteller`+Hersteller-Artikelnr; `master_set_components.article_no`/`distributor_article_no`; `distributor_prices.article_no`.
4. **Verbindung zu `products`?** — `distributor_prices.product_id → products.id`; `supplier_article_map.product_id → products.id`; Import setzt `products.article_no`.
5. **Verbindung zu `master_set_components`?** — `distributor_price_id → distributor_prices`; `product_id → products`; `unit_price/purchase_price` = **Spiegel** der gewählten Distributor-Preiszeile.
6. **Können 101–119 über Artikelnr/Hersteller/Modell gematcht werden?** — **Aktuell nein:** sie tragen **keine Artikelnummer/EAN/SKU**, nur Modell+Hersteller. Ein Match über Hersteller+Modell wäre **unsicher** (nicht belegt-eindeutig) → **kein Auto** (konsistent mit P3-d0-Regel). Für sicheres Matching brauchen sie Hersteller-Artikelnummern (aus dem OMD/Hersteller-Katalog).
7. **Ist OpenMaster die führende Preisquelle statt `master_set_components.unit_price`?** — **Quelle vs. Spiegel:** `distributor_prices` (aus OMD/IDS) ist die **Lieferanten-Preis-Wahrheit**; `master_set_components.unit_price` ist ein **ausgewählter Snapshot** davon. Nicht „entweder/oder", sondern **Quelle → Spiegel**.
8. **Welche Preiswahrheit gilt für `CatalogPriceGuard`?** — Der **Komponenten-Snapshot** (`master_set_components.unit_price`/`purchase_price`), gespeist aus der gewählten `distributor_prices`-Zeile. **P1-a bleibt unverändert.**
9. **Nur Anker aus `master_set_components`, Preis aus OMD?** — **Nein** im Ist-Design: die Komponente liefert Anker **und** gespiegelten Preis.
10. **OMD-Preise in `master_set_components` spiegeln?** — **Ja — genau das ist der vorhandene Weg** (`distributor_price_id` + Snapshot via `MasterSetDistributorCompareController`).

## 4. Klare Empfehlung
1. **IDS/OMD als Preisquelle nutzbar: JA (der richtige Hauptpfad) — aber derzeit NICHT operativ.** Manuelle EK/VK-Lieferung ist **nicht** der Hauptpfad (fachlich korrekt bestätigt); Demo-Preise sind ausgeschlossen.
2. **Führende Tabellen/Keys:** `distributor_prices` (Preis-Wahrheit, `purchase_price`/`price`, `price_date`, je `distributor_id`+`product_id`/`article_no`); Identität via **`products.article_no`=Hersteller-Artikelnr** + `supplier_article_map`; Set-Spiegel via `master_set_components.distributor_price_id`.
3. **Wie technische WP-Kandidaten (101–119) mit Preisartikeln verbunden werden:** sie brauchen **Hersteller-Artikelnummern**; dann per **realem OMD/IDS-Import** (`SupplierProductImportService`) → schreibt/aktualisiert `products.article_no` + `distributor_prices` + `supplier_article_map`. Kein Fuzzy-Auto über Hersteller+Modell.
4. **P3-d2a: NICHT als manueller Preis-Seeder bauen.** Stattdessen **IDS/OpenMaster-Mapping-/Import-Weg**: reale Lieferantenpreise über die bestehende Pipeline in `distributor_prices` bringen und per `distributor_price_id` in Set-Komponenten spiegeln. Kein Hardcode, keine erfundenen Preise.
5. **Nächster richtiger Bau-Slice (Vorschlag, read-only zuerst):** ein **OMD/IDS-Anbindungs-Feinplan** bzw. Vorbedingungs-Klärung: (a) reale `SupplierConnection` (OMD-Zugang/Credentials — Yama-Seite) konfigurierbar? (b) sind die WP-Artikel im Lieferanten-/OMD-Katalog vorhanden? (c) welcher Match-Schlüssel (Hersteller-Artikelnr) verbindet 101–119 mit OMD-Artikeln? Erst danach ein Import-/Spiegel-Slice — **nicht** ein Kuratierungs-Seeder mit manuellen Preisen.

## 5. Was bleibt verboten
Manuelle/erfundene Preise · Preis-Seeder · Demo-Preise verwenden · Fuzzy-Auto-Match (Hersteller+Modell) als Anker · Änderung an `CatalogPriceGuard`/P1-a · Migration · Schreiben in `offer_details.sections` · Commit · Push.

## 6. Nicht-Ziele (eingehalten)
Nur gelesen · keine Preisübernahme · keine Katalog-/DB-Änderung · kein Seeder · kein Commit/Push · keine SENSIBEL-Inhalte zitiert · keine Annahme ohne Beleg. Nur dieses Dokument neu (nicht committet).
