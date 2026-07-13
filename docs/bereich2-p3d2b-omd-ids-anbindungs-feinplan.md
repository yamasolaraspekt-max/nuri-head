# P3-d2b — OMD/IDS-Anbindungs-Feinplan für WP-Preise (READ-ONLY)

> **Status:** read-only Feinplan. **Nur lesen. Keine OMD-Zugangsdaten anfassen/anzeigen, keine Credentials, keine Preisübernahme, keine Katalog-/DB-Änderung, kein `CatalogPriceGuard`-Eingriff, kein `offer_details`-Schreibpfad, kein Commit, kein Push.** Keine Annahme ohne Beleg.
> **Ziel:** Belegen, **was konkret fehlt**, damit die realen WP-Geräte 101–119 über OMD/IDS **echte** Preise bekommen und später sauber in ein WP-Set gespiegelt werden.
> **Bezug:** P3-d2-ids-openmaster-preisfluss · Datum 2026-07-13.

---

## 1. Anbindungs-Bausteine (belegt vorhanden)
- **Verbindung:** `SupplierConnection` (Tabelle) — Felder: `distributor_id`, `connector_type`, `endpoint_url`, `username`/`password`/`token`/`customer_number` (**alle `encrypted`**), `extra_auth_data` (`encrypted:array`), `last_test_status`. Test-Weg: `SupplierConnectionTestService`.
- **Client/Auth:** `Services/Suppliers/Omd/OmdClient`, `OmdAuthService`. OMD-Basis-Config in `config/services.php` (Keys `shop_url`/`kndnr`/`username`/`password` — **Werte nicht Teil dieses Dokuments**).
- **Mapper:** `Mappers/{OmdMapper, IdsMapper, DatanormMapper, SupplierArticleMapper, MapperRegistry}`.
- **Import:** `SupplierProductImportService` → schreibt `products` (inkl. `article_no`=Hersteller-Artikelnr) + `DistributorPrice::updateOrCreate` (`purchase_price`=EK, `price`=VK).
- **Identität:** `supplier_article_map` (`hersteller` + Hersteller-Artikelnr + `distributor_id` → `product_id`).
- **Set-Spiegel:** `MasterSetDistributorCompareController::compare` (read-only Auswahl der `distributor_price_id`s je Produkt); der **Schreib-/Apply-Schritt** (setzt `master_set_components.distributor_price_id` + Snapshot `unit_price`/`purchase_price`) sitzt im Set-Komponenten-Editor (`PlannerMasterSetController` / Komponenten-Update).
- **Preis liest:** `CatalogPriceGuard` unverändert aus `master_set_components.unit_price`/`purchase_price`.

## 2. Was konkret fehlt (belegte Lückenliste)
| # | Fehlt | Beleg | Konsequenz |
|---|------|-------|------------|
| 1 | **Konfigurierte OMD-Verbindung** | `supplier_connections = 0` | kein Abruf möglich |
| 2 | **Realer Import** | `supplier_import_logs=0`, `imported_ids_items=0` | keine echten Preise/Artikel |
| 3 | **Hersteller-Artikelnummern für 101–119** | `products.article_no/sku/ean = NULL` (nur `model`+`brand`) | kein sicherer Match-Schlüssel |
| 4 | **Identitäts-Map** | `supplier_article_map = 0` | keine Zuordnung Lieferantenartikel↔`product_id` |
| 5 | **Reale WP-`distributor_prices`** | 88 Zeilen, alle **Demo** (creator/notice NULL) | keine verwertbaren EK/VK |
| 6 | **Katalog-Abdeckung im OMD** (sind NIBE/Buderus/Viessmann-Modelle beim Lieferanten gelistet?) | nicht prüfbar ohne Live-Verbindung | offen — Yama/Lieferant-seitig |

## 3. Antworten auf die Prüfpunkte
- **Welche `SupplierConnection`-Daten?** `distributor_id` + `connector_type` (omd/ids/datanorm) + `endpoint_url` + Credentials (`username`/`password`/`token`/`customer_number`, verschlüsselt) + ggf. `extra_auth_data`. **Yama-/Lieferanten-seitig; nie im Repo/Klartext.**
- **OMD/IDS-Zugang konfigurierbar?** Ja — über `SupplierConnection`-Zeile + `config/services.php`-Basis; `SupplierConnectionTestService` prüft die Verbindung. **Aktuell keine Zeile vorhanden.**
- **Welche Tabellen müssen gefüllt werden?** `supplier_connections` (Verbindung) → durch Import: `products` (`article_no`), `distributor_prices` (EK/VK), `supplier_article_map` (Identität), `supplier_import_logs`/`imported_ids_items` (Trail) → per Set-Editor: `master_set_components` (`distributor_price_id` + Snapshot).
- **Welche Artikelnummern/EAN/SKU fehlen für 101–119?** **Alle** (`article_no`, `sku`, `ean` = NULL). Sie tragen nur `model` (S2125-8 …) + `brand`.
- **NIBE/Buderus/Viessmann im OMD-Katalog matchbar?** Mechanisch **ja über Hersteller-Artikelnummer** (`products.article_no` ↔ Lieferantenartikel via `supplier_article_map`/Mapper). **Ob** die konkreten Modelle beim angebundenen Lieferanten gelistet sind, ist **erst mit Live-Verbindung** belegbar (offen). **Fuzzy über Hersteller+Modell = nicht zulässig als Auto-Schlüssel.**
- **Welche Importer/Mapper existieren?** `SupplierProductImportService` + `OmdMapper`/`IdsMapper`/`DatanormMapper`/`SupplierArticleMapper` (`MapperRegistry`). Vollständig vorhanden.
- **Wie werden `distributor_prices` erzeugt?** `SupplierProductImportService` → `DistributorPrice::updateOrCreate(distributor_id+product_id/article_no, {purchase_price, price, price_date, …})`. Idempotent je Distributor/Artikel.
- **Wie wird `supplier_article_map` genutzt?** Als neutrale Identität Hersteller+Artikelnr→`product_id`, damit wiederkehrende Importe denselben `product_id` treffen (kein Duplikat).
- **Wie wird `master_set_components.distributor_price_id` gesetzt?** Auswahl über `MasterSetDistributorCompareController::compare` (zeigt Optionen), Anwendung im Set-Komponenten-Editor (schreibt `distributor_price_id` + spiegelt `unit_price`/`purchase_price`).
- **Wie wird der Preis-Snapshot sauber gespiegelt?** Beim Setzen der Komponente wird der gewählte `distributor_prices`-Wert (EK/VK) als Snapshot in die Komponente kopiert (Zeitpunkt-fest); Quelle bleibt `distributor_prices` (aktualisierbar per Re-Import), Spiegel bleibt reproduzierbar.
- **Wie funktioniert `CatalogPriceGuard` danach unverändert?** Der Guard liest weiter `master_set_components.unit_price`/`purchase_price` (P1-a). Solange der Snapshot korrekt aus `distributor_prices` gespiegelt ist, **keine Änderung an P1-a nötig**.
- **Welche UI/Pflege-Schritte nötig?** (1) `Product/IDS/SupplierConnectionController`: Verbindung anlegen + testen. (2) IDS-Suche/Import (`IdsController`/`IdsSearchController` / `SupplierProductImportService`): Artikel+Preise holen. (3) `DistributorPriceController`: Preise sichten. (4) Set-Editor + `MasterSetDistributorCompareController`: Komponente→`distributor_price_id` wählen/spiegeln.
- **Welche Testdaten ohne echte Preise erlaubt — und welche nicht?**
  - **Erlaubt (nur Test-DB `ticket_testing`, isoliert, teardownbar):** synthetische `SupplierConnection`-Fixture mit Dummy-Werten, synthetische `distributor_prices`/`supplier_article_map`-Zeilen **klar als Test markiert**, um Mapping→Spiegel→Guard-**Mechanik** zu prüfen. Credentials als Fake, nie echte.
  - **Nicht erlaubt:** synthetische/Demo-Preise in den **Dev-/Prod-Katalog** (`products`/`distributor_prices`) schreiben · hartkodierte „echt aussehende" Preise · Demo-Preise als real behandeln · Fuzzy-Auto-Match als Anker.

## 4. Empfehlung — nächster richtiger Bau-Slice
1. **Operative Vorbedingung (Yama-Seite, kein Code):** eine reale `SupplierConnection` (OMD/IDS-Zugang) konfigurieren **und** klären, ob die WP-Modelle beim Lieferanten gelistet sind + welche **Hersteller-Artikelnummern** 101–119 tragen müssen.
2. **Dann Code-Slice (mit Freigabe):** realen Import laufen lassen (`SupplierProductImportService`) → füllt `products.article_no` + `distributor_prices` + `supplier_article_map`. **Kein neuer Seeder, keine erfundenen Preise.**
3. **Danach Spiegel-Slice:** kuratiertes WP-Set-Komponenten auf die importierten Produkte legen + `distributor_price_id` wählen/spiegeln (Set-Editor). Erst dann tragen `product_id`s Preis **und** Specs → P3-d0a-Schnittmenge>0 (nach dem separaten P3-d0a-Reader-Fix).
4. **Reine Mechanik-Prüfung ohne Live-OMD** ist als **Test-DB-Fixture** möglich (synthetisch, markiert) — nur um die Kette Mapping→`distributor_prices`→`distributor_price_id`→Snapshot→`CatalogPriceGuard` zu verifizieren, **ohne** den realen Katalog zu berühren.

## 5. Was bleibt verboten
OMD-Credentials anzeigen/committen · erfundene/Demo-Preise in den realen Katalog · Preis-Seeder · Fuzzy-Auto-Match als Anker · `CatalogPriceGuard`/P1-a ändern · Migration · `offer_details`-Schreibpfad · Commit · Push.

## 6. Nicht-Ziele (eingehalten)
Nur gelesen · keine Zugangsdaten berührt/angezeigt · keine Preisübernahme · keine Katalog-/DB-Änderung · kein Commit/Push. Nur dieses Dokument neu (nicht committet).
