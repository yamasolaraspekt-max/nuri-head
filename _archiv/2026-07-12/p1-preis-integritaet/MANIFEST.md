# Archiv-Manifest — P1-a Preis-Integrität (component_id-Guard)

**Datum:** 2026-07-12 · **Freigeber:** Yama („Bau frei für P1-a im korrigierten Scope") · **Rolle:** Generator
**Bezug:** `docs/bereich2-preis-integritaet-konzept.md` (gesperrter Scope, Yama 2026-07-12)

## Warum archiviert
`OfferController::processOffer` (Angebots-Speicher-/Rechenpfad) wird geändert: vor `calculateOfferSections`
wird ein serverseitiger Preis-Guard eingehängt. Rechen-/Speicherpfad → `rueckfall-archiv-regeln.md` **Variante B**
(Original zusätzlich sichern, nicht nur Git).

## Originalpfad
`app/Http/Controllers/Customer/Offer/OfferController.php`
→ **Original (vor Änderung):** `_archiv/2026-07-12/p1-preis-integritaet/OfferController.php.original`

## Was ersetzt/ergänzt wird (aktiver Pfad)
- **NEU** `app/Services/Offer/CatalogPriceGuard.php` — Guard-Logik (`apply` = DB-Fetch, `applyWithCatalog` = reine Logik).
- **NEU** `config/offer.php` — Flag `enforce_catalog_pricing` (Default `true`).
- **GEÄNDERT** `OfferController::processOffer` — genau ein Aufruf des Guards hinter dem Flag, vor `calculateOfferSections`.
  Effekt: Knoten mit `component_id` bekommen EK aus `MasterSetComponent.purchase_price` + VK aus `unit_price`
  (Payload verliert), Marker `preis_quelle`; ankerlose Positionen → `preis_quelle='manuell'`; `component_id`
  ohne DB-Treffer → `preis_quelle='katalog_fehlt'` (kein Abbruch, kein stiller 0).

## Bewusst NICHT geändert
`calculateOfferSections`, `offerLineTotals`, `offerNodeVkPrice/EkPrice/Qty`, Engine 2 (`OfferFolderController`),
`processTemplate`, alle Views/JS. Kein `product_id`-Reprice, kein `sub_*`-Fix, kein GK/Wagnis, keine Migration, kein UI.

## Geprüfte Nutzung
`processOffer` ist der Handler von `POST /offers/save-document` (`routes/web.php:3524`, `offers.save-document`),
aufgerufen vom aktiven Wizard `resources/views/admin/offer/configuration/offer/config.blade.php`.
`MasterSetComponent` trägt `id`/`purchase_price`(EK)/`unit_price`(VK). Knoten-`component_id` wird von
`OfferFolderController::hydrateNodeFromComponent` + dem Wizard-Bauweg `makeComponentItem` geschrieben.

## Tests
- Unit `tests/Unit/Offer/CatalogPriceGuardTest.php` (reine Logik, DB-frei).
- Feature `tests/Feature/Offer/OfferCatalogPricingTest.php` (DB-Fetch + Speicherpfad).

## Rückweg (Notfall)
1. **Flag aus:** `OFFER_ENFORCE_CATALOG_PRICING=false` (bzw. `config/offer.php` → `false`) → Speicherpfad verhält sich exakt wie vorher.
2. **Vollständiger Rückbau:** neuen Service + config löschen, `OfferController.php` aus
   `OfferController.php.original` zurückspielen (bzw. `git revert` des path-scoped Commits).
3. Kein Datenverlust: Guard schreibt nur additive JSON-Marker, keine destruktive Bestands-/Schemaänderung.

## Nachtrag B1 (2026-07-12, nach Evaluator-Veto) — Preiseinheits-Divisor geschlossen
- **Befund (Evaluator):** die persistierte Zeilensumme ist `(qty / price_unit_value) * price`; der
  Divisor `price_unit_value`/`cost_price_unit_value` (frei editierbares Payload-Feld) wurde vom Guard
  nicht kontrolliert → manipulierbar trotz `katalog_verifiziert`.
- **Fix (nur in `CatalogPriceGuard.php`, additiv):** für `component_id`-Knoten werden `price_unit_value`
  und `cost_price_unit_value` serverseitig auf `1.0` normalisiert (Katalog-Preise sind Stück-Preise,
  Divisor 1 wie beim Hydrieren). `preis_quelle='katalog_verifiziert'` nur noch, wenn Preis + EK + beide
  Divisoren serverseitig konsistent sind; jede Payload-Abweichung → `katalog_korrigiert`.
- **`OfferController.php` unverändert** gegenüber der ersten Fassung (nur der Guard wurde erweitert) →
  `OfferController.php.original` bleibt gültiger Rückfall.
- **Tests ergänzt:** Unit `test_b1_price_unit_value…`/`…verifiziert_nur…`/`…cost_price_unit_value…`;
  Feature `test_b1_price_unit_value_manipulation_wird_neutralisiert` (End-to-End: total_net bleibt 150).
