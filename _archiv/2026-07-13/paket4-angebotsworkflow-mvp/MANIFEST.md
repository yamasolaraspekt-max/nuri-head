# Archiv-Manifest — Paket 4a: WP-Angebotsworkflow-Cockpit (READ-ONLY)

**Datum:** 2026-07-13 · **Rolle:** Generator (autonomer 3h-Auftrag) · **Status:** kein Push
**Bezug:** `docs/bereich2-paket4-angebotsworkflow-mvp-plan.md` · reuse OfferReadinessService/AuslegungVorschlagService/WpKatalogMatchingService.

## Warum archiviert
Eine Bestandsdatei (`routes/web.php`) wird berührt → Variante B.

## Originalpfad → Archivkopie
- `routes/web.php` → `web.php.original`

## Was neu/geändert ist
**Neu (additiv, read-only):**
- `app/Services/Offer/WpAngebotsWorkflowService.php` — aggregiert die drei read-only Services on-the-fly;
  5-Schritt-Prozessmodell, Fortschritt (85% Technik + 15% Preis, getrennt), nächste Aktion. KEIN Write,
  keine Persistenz, kein Preisanker, kein component_id, nur WP (id=2).
- `app/Http/Controllers/Customer/Offer/WpAngebotsWorkflowController.php` — `show` (Seite).
- `resources/views/admin/offer/workflow/cockpit.blade.php` — Cockpit (Vuexy/jQuery, kein Alpine, PII-arm),
  eingebettete Panels lazy über die bestehenden Panel-Routen.
- `tests/Feature/Offer/WpAngebotsWorkflowCockpitTest.php`.

**Bestandsdatei geändert:**
- `routes/web.php`: +1 read-only GET `/offers/workflow/{leadProductList}` (`offers.workflow`, auth, whereNumber).

## Nicht-Ziele
Kein Schreiben in offer_details.sections · kein Angebot-Create · kein Preisanker/component_id · keine
Preis-/Kataloglogik · keine Migration · keine zweite Wahrheit · kein Alpine · kein Push.

## Rückweg
Variante A `git revert` oder `routes/web.php` aus Archiv; neue Dateien löschen. Read-only, kein DB-Eingriff → verlustfrei.
