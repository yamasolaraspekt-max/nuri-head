# Bereich 2 — Folge-Posten (offen, nicht im aktuellen Scope)

**Stand:** 2026-07-12 · Sammelstelle für belegte, aber bewusst zurückgestellte Befunde aus Bereich 2.

---

## N1 — Legacy-Preiseinheits-Lücke im `OfferDetailsController` / `offer_product_lists`

- **Endpoint / Datei:** `app/Http/Controllers/Customer/Offer/OfferDetailsController.php` (Speicherung `offer_product_lists`), Felder `price_unit_value` / `cost_price_unit_value`; Validierung nur `min:0.0001`. Route über den **Legacy-Positions-Pfad** (`offer.details.*`, `routes/web.php:3543`), **nicht** über `processOffer`/`save-document`.
- **Risiko:** gleiche Klasse wie P1-a-B1 — der Preiseinheits-Divisor ist frei aus dem Payload setzbar (z. B. 1000 oder 0,0001) und wird serverseitig **nicht** gegen den Katalog kontrolliert. Über diesen Pfad bliebe eine Preis-/Summen-Manipulation möglich.
- **Warum außerhalb P1-a:** **präexistent** (nicht durch P1-a verursacht) und in einem **anderen Endpoint/Model** (`offer_product_lists` = die relationale Legacy-Zweit-Positionswahrheit, nicht `offer_details.sections`). P1-a hat bewusst nur den führenden Speicherpfad `processOffer` gehärtet (gesperrter Scope, Yama 2026-07-12).
- **Empfohlener späterer Umgang:** im Zuge der geplanten **`offer_product_lists`-Stilllegung** (Ziel-Wahrheit „nur `offer_details.sections`", Bereich-2-Bewertung Punkt 2) miterledigen — entweder Pfad stilllegen (Variante B, Archiv) **oder**, falls er vorher aktiv bleibt, denselben `CatalogPriceGuard`-Schutz (inkl. Divisor-Neutralisierung) analog anwenden. Als eigenes „Legacy-Pricing"-Paket führen, mit eigener Evaluator-Runde.
- **Herkunft:** P1-a-Evaluator, zweite Runde (2026-07-12), Befund N1.

---

## F2 — product_formulas v2 Render/Erfassung: v1-Blade/Controller blockiert echte Nutzung

- **Endpoint / Dateien:** `resources/views/admin/new_leads/checklists/checklist.blade.php` (Render) + `app/Http/Controllers/LeadProductChecklistValueController.php` (`initChecklistRender`/`save`/`saveChecklist`).
- **Risiko / Befund (firsthand belegt):** der bestehende Lead-Checklisten-Render-/Erfassungspfad ist **v1** und mit einem **v2**-`product_formulas`-Formular nicht lauffähig:
  1. **`json_decode(array)`-Bug:** `checklist.blade.php:2` sowie `LeadProductChecklistValueController.php:86` (`save`) und `:119` (`saveChecklist`) rufen `json_decode()` auf bereits als `array` gecastete Attribute (`ProductFormula.fields`, `LeadProductChecklistValue.formula_snapshot`). In PHP 8.4 ist `json_decode(array)` ein **fataler TypeError** (empirisch bestätigt). Latent, weil `product_formulas` bisher 0 Zeilen hatte.
  2. **v1/v2-Schema-Inkompatibilität:** die Blade liest Feld-Identität über `field['name']` (v1), der Validator/Engine über `field['key']` (v2).
  3. **Fehlende v2-Typunterstützung:** die Blade rendert nur text/number/date/textarea/select/checkbox/file/multi-group/formula — **nicht** die v2-Typen `multiselect`, `area`, `length`, `volume`, `power`, `plz`, `consent`, `integer`, `decimal`.
  4. **Optionsformat-Konflikt:** die Blade erwartet `options` als **CSV-String** (`explode(',', $field['options'])`), das v2-Schema liefert `options` als **`{value,label}`-Array** → `explode` auf ein Array bricht.
- **Warum außerhalb des Pilots:** der Pilot ist bewusst **additiv** (nur Seeder + Test); die Behebung berührt **bestehende Dateien** (Blade + Controller) und ist ein eigenes, evaluator-pflichtiges Paket. Pilot-Ziel wurde daher (Yama, Option A) reduziert auf „WP-Formularinhalt lebt als v2 product_formula und validiert sauber".
- **Empfohlener späterer Schritt (eigenes Paket):** `checklist.blade.php` v2-fähig machen (key + Options-Array + fehlende Feldtypen) · `LeadProductChecklistValueController` array/JSON-robust machen (`json_decode`-auf-array beseitigen) · **v1-Kompatibilität erhalten** (schema_version-Weiche) · Save/Reload testen · eigener Evaluator · Variante-B-Archiv für die geänderten Bestandsdateien.
- **Herkunft:** WP-Formular-Pilot, Pflichtprüfung 3 (2026-07-12); v2-`product_formula` liegt bereits sauber vor (Seeder `WpProduktFormularSeeder`, `imported_from='playground:produkt_waermepumpe'`), aber ohne funktionierenden Render-/Erfassungspfad.
