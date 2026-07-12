# Archiv-Manifest — Paket 1: WP-Angebotsreife-Panel (read-only)

**Datum:** 2026-07-12 · **Kontext:** autonomer 3h-Block · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** `docs/bereich2-angebotsworkflow-konzept.md` (Kapitel 4 + Paket 1)

## Warum archiviert
**Eine** Bestandsdatei wird berührt: `routes/web.php` (+1 `use`-Import, +2 read-only Routen). → `rueckfall-archiv-regeln.md` Variante B.

## Originalpfad → Archivkopie
- `routes/web.php` → `_archiv/2026-07-12/wp-angebotsreife-panel/web.php.original`

## Was geändert/neu ist
**Bestandsdatei geändert:**
- `routes/web.php`: Import `WpAngebotsreifeController` + zwei read-only Routen in der bestehenden `offers.`-Gruppe:
  `GET /offers/angebotsreife/{leadProductList}` (`offers.angebotsreife`) und `…/json` (`offers.angebotsreife.json`).

**Neu (additiv):**
- `app/Services/Offer/OfferReadinessService.php` — on-the-fly Angebotsreife je `lead_product_lists`-Zeile (Kriterien-Katalog WP, DTO). Keine Persistenz, keine 2. Statuswahrheit.
- `app/Http/Controllers/Customer/Offer/WpAngebotsreifeController.php` — read-only `show()` (Panel) + `json()` (DTO).
- `resources/views/admin/offer/partials/angebotsreife_panel.blade.php` — reine Anzeige.
- `resources/views/admin/offer/angebotsreife.blade.php` — self-contained Wrapper-Seite.
- `tests/Feature/Offer/OfferReadinessServiceTest.php` (6) + `tests/Feature/Offer/WpAngebotsreifePanelTest.php` (3).

## Bewusst NICHT geändert
Keine Angebotserstellung/-logik (`OfferController`/`OfferWizardController` unberührt), keine Migration/Tabelle, keine Persistenz von Reife-Prozent, keine PV/Bivalenz/Auslegungsberechnung/sections-Erzeugung/Vorlagen/Matching, kein Prod-Seed, keine DatabaseSeeder-Registrierung.

## Geprüfte Nutzung
`routes/web.php`-Gruppe `Route::middleware('auth')->prefix('offers')` (ab Z. ~3276). Der Service liest nur:
`lead_product_lists`(+customer/alternative/articleGroup/checklistValues), `new_leads`, `lead_alternative_adds`,
`product_formulas`+`lead_product_checklist_values`, `master_sets`/`products`, `anforderungsprofile`+`anforderungsprofil_werte`.

## Tests
44 grün (Offer+Form+Unit), davon neu: OfferReadinessService 6, WpAngebotsreifePanel 3.

## Rückweg (Notfall)
1. `routes/web.php` aus `web.php.original` zurückspielen (bzw. die 3 hinzugefügten Zeilen entfernen / `git revert`).
2. Neue Dateien (Service/Controller/Views/Tests) löschen — rein additiv, kein DB-/Schemaeingriff, keine Persistenz → kein Datenverlust.
