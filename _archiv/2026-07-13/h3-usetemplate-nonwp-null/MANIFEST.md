# Archiv-Manifest — H3: useTemplate Non-WP + alternative_id=null → 422 statt 500

**Datum:** 2026-07-13 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** Paket 2b `644eff5` (WP-Objektpflicht) · H2 `eca680f` (alternative_id-Zugehörigkeit) · offers.alternative_id NOT NULL.

## Warum archiviert
Eine **committete Bestandsdatei** wird geändert → Sicherungskopie (zusätzlich zu `git revert`).

## Originalpfad → Archivkopie
- `app/Http/Controllers/Customer/Offer/OfferTemplatePickerController.php` → `OfferTemplatePickerController.php.original`

## Was geändert ist (additiv, ein Guard-Block)
`OfferTemplatePickerController::useTemplate` — **nach** dem unveränderten 2b-WP-Block ein zusätzlicher Guard:
```
if ($alternativeId === null) {
    return 422 OFFER_OBJECT_REQUIRED ("Bitte zuerst ein Objekt auswählen …");
}
```
Begründung: `offers.alternative_id` ist NOT NULL + FK (`create_offers_table:18,29`) — jedes Angebot braucht
ein Objekt. Der WP-Block behandelt WP+null (2b-Meldung, unverändert); der neue Guard fängt den verbleibenden
Non-WP+null-Fall ab, der bisher zu `Offer::create(alternative_id=null)` → **500** führte. Real UI-erreichbar
(`customer-picker.blade.php:1998` sendet `alternative_id || ''`).

## Nicht geändert / Nicht-Ziele
2b-WP-Block wortgleich · `check()` unverändert · `OfferReadinessGate`/Reife-Logik · H2-Zugehörigkeits-Guard ·
Preis/`component_id`/Katalog · Transaktions-/Anlagelogik · Migration (kein `nullable`-Umbau). Kein Refactor,
kein Push, kein `git add -A`.

## Tests
`tests/Feature/Offer/UseTemplateGateTest.php` (additiv): WP+null → 422 `OFFER_OBJECT_REQUIRED` (2b) ·
Non-WP+null → **neu** 422 `OFFER_OBJECT_REQUIRED`, keine Anlage · Non-WP+gültiges Objekt → Anlage ·
H2 (ungültig/fremd → 422 `OFFER_OBJECT_INVALID`) bleibt grün.

## Rückweg (Notfall)
**Variante A:** `git revert` des H3-Commits. Alternativ die Datei aus `.original` zurückspielen.
Additiv-defensiver Guard, kein DB-/Schemaeingriff → verlustfrei.
