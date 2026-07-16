# P3-d2-preisquelle — WP-Preisquellen-Inventur (READ-ONLY)

> **Status:** read-only Inventur. **Nur lesen. Keine Preisübernahme, keine Katalog-/DB-Änderung, kein Commit, kein Push.** SENSIBEL-Quellen nur referenziert, **nicht** inhaltlich wiedergegeben. Keine Annahme ohne Beleg.
> **Zweck:** Prüfen, ob **reale** WP-EK/VK im System/Importen existieren, die einem `product_id`/Modell zuordenbar sind — als Preisbasis für P3-d2a.
> **Bezug:** P3-d1 (`docs/bereich2-p3d1-...`) · P3-d2a (data-only, blockiert bis reale EK/VK) · Datum 2026-07-13.

---

## 1. Geprüfte Quellen & Belegqualität

| # | Quelle | Ort/Beleg | Herkunft | EK/VK | Datum/Stand | Zuordenbar zu Spec-`product_id`? | Belegqualität | verwendbar für P3-d2a |
|---|--------|-----------|----------|-------|-------------|----------------------------------|---------------|------------------------|
| 1 | `distributor_prices` (WP, ag=2) | 12 Zeilen, `distributor_id`, `price`+`purchase_price` | **Demo** (`DemoPartnerProfileSeeder`) | ja/ja | 2026-06-29 (geseedet) | **Nein** — alle auf Produkte **9/10/11** (ohne Specs) | **C (Demo)** | **NEIN** |
| 2 | `products.retail_price`/`purchase_price` (WP) | nur pid 9/10/11 | **Demo-Seeder** (`imported_from=NULL`) | ja/ja | — | **Nein** (spec-los; generische Namen „Standard/Komfort/Premium") | **C (Demo)** | **NEIN** |
| 3 | `product_heat_pump_specs`-Produkte (101–119, real: NIBE/Buderus/Viessmann) | 19 reale Modelle | Import `imported_from='wberechnung'` | **keine** (VK/EK NULL, 0 in distributor_prices/-_product) | — | ist die Spec-Wahrheit, aber **unbepreist** | **D (kein Preis)** | **NEIN (kein Preis)** |
| 4 | `distributor_product` | 0 Zeilen | — | — | — | — | **D** | NEIN |
| 5 | Seeder gesamt (`MasterSetSeeder`, `DemoMasterSetSeeder`, `HeatpumpSeeder`, `Waermepumpe*Seeder`, `Referenz*`) | hartkodierte Preise nur in **Demo**-Seedern | Demo | teils | — | Demo-Produkte | **C** | NEIN |
| 6 | Import-/Datendateien (`database/data/`, `WberechnungImportSeeder`, `spec_import_batches`, `supplier_import_logs`) | Import-Infra vorhanden, aber **Preis-frei** (Import setzte Kennlinie, nicht Preis) | wberechnung | keine | — | — | **D** | NEIN |
| 7 | Hochgeladene CSVs (`public/uploads/*.csv`) | 2 Dateien, je 4 Zeilen | **Angebots-/Auftrags-Export** (Spalten „Kundendaten, Ort, Angebots-/Auftragssumme …") — **SENSIBEL**, nur referenziert | n/a | n/a | **Nein** (keine Artikel-Preisliste) | **D** | NEIN |
| 8 | Wissens-Register (`~/wissensregister/ideensammlung.md` „Katalog & Einkauf") | Verweis MISC-002 (ITEK Warenkorb XSD/UGL, Großhandels-**Bestell**-EDI) | Zukunfts-Idee (EDI-Anbindung), **keine** aktuelle Preisliste | n/a | n/a | **Nein** | **D (Idee, nicht Datenquelle)** | NEIN |

**Bewertungsskala:** A = belegt real, zuordenbar, aktuell · B = real, aber unvollständig/alt/teilzuordenbar · C = Demo/geschätzt (nicht verwendbar) · D = kein Preis / nicht auffindbar.

---

## 2. Kernbefunde (belegt)
- Die **einzigen** WP-Preise im System (distributor_prices + products) hängen an **3 Demo-Produkten (9/10/11)** aus `DemoPartnerProfileSeeder`/Demo-Seedern — **ohne Kennlinie**, generische Namen. **Nicht real, nicht verwendbar.**
- Die **realen** WP-Geräte mit Kennlinie (101–119, konkrete Modelle) haben **nirgends** einen Preis (0 in `products`, 0 in `distributor_prices`, 0 in `distributor_product`).
- **Schnittmenge Preis ∩ Specs = 0** über **jeden** Kanal (products, distributor_prices).
- Keine Import-/Upload-Datei enthält eine **reale, artikel-zuordenbare WP-Preisliste**. Der einzige einschlägige Register-Eintrag ist eine **Bestell-EDI-Idee** (MISC-002), keine Preisquelle.
- EK/VK wären **grundsätzlich unterscheidbar** (`distributor_prices.purchase_price`/`price`, `products.purchase_price`/`retail_price`) — aber nur auf Demo-Daten befüllt.

---

## 3. Klare Aussage (Yamas 4 Fragen)
1. **Gibt es eine verwendbare Quelle A/B?** → **NEIN.** Alle gefundenen WP-Preise sind **C (Demo)**; die realen Spec-Geräte sind **D (kein Preis)**.
2. **Für welche `product_id`s?** → **Keine.** Bepreist sind nur die Demo-Produkte 9/10/11 (ohne Specs); die verwendbaren Spec-`product_id`s (101–119) sind unbepreist.
3. **Welche Preise dürfen für P3-d2a verwendet werden?** → **Keine aus dem System.** Demo-Preise sind ausgeschlossen (kein Falsch-/Demopreis, keine Schätzung). Die realen Modelle haben keine Preise.
4. **Müssen EK/VK extern von Yama geliefert werden?** → **JA, zwingend.** P3-d2a bleibt blockiert, bis Yama je gewähltem realen `product_id` (101–119) **belegte reale EK/VK** liefert (Format: `product_id | Hersteller | Modell | reale EK | reale VK | Quelle/Stand`).

---

## 4. Empfehlung
- **P3-d2a bleibt blockiert** — nicht mit Demo-/Seedpreisen füllen.
- **Preisbeschaffung ist ein Yama-Input**, kein Systemfund: reale Distributor-/Herstellerliste für die gewählten Modelle (NIBE S2125-* / Buderus WLW-* / Viessmann 251.*).
- **Optionaler späterer Weg** (eigenes Paket, nicht jetzt): die vorhandene, aber leere Preis-Infrastruktur (`distributor_prices` mit `price_date`/`distributor_id`, `ProductImportController`, MISC-002-EDI) als **realen** Import-Kanal nutzen — sobald eine belegte Preisliste vorliegt. Das ersetzt **nicht** die Yama-Lieferung, sondern wäre der saubere Einspielweg.

## 5. Nicht-Ziele (eingehalten)
Nur gelesen · keine Preisübernahme · keine Katalog-/DB-Änderung · kein Commit · kein Push · keine SENSIBEL-Inhalte zitiert (Upload-CSVs nur strukturell referenziert) · keine Annahme ohne Beleg. Nur dieses Dokument neu (nicht committet).
