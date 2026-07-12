# Archiv-Manifest — Paket 1b-a: WP-Angebotsreife-Panel ins Objektprofil einbetten

**Datum:** 2026-07-12 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** `docs/bereich2-angebotsworkflow-konzept.md` (Paket 1b) · Paket-1-Commit `1faeba7`

## Warum archiviert
Zwei **Bestandsdateien** werden berührt → `rueckfall-archiv-regeln.md` Variante B.

## Originalpfade → Archivkopien
- `routes/web.php` → `_archiv/2026-07-12/wp-angebotsreife-1b/web.php.original`
- `resources/views/admin/new_leads/customer_object_profile.blade.php`
  → `_archiv/2026-07-12/wp-angebotsreife-1b/resources/views/admin/new_leads/customer_object_profile.blade.php`

## Was geändert/neu ist
**Bestandsdateien geändert:**
- `routes/web.php`: +1 read-only Route `GET /offers/angebotsreife/{leadProductList}/panel`
  (`offers.angebotsreife.panel`, `whereNumber`, gleiche `auth`-`offers`-Gruppe wie Paket 1).
- `customer_object_profile.blade.php`: **nur für WP-Gewerkzeilen** (`$product->product_id == 2`) ein kleiner
  Platzhalter `<div class="wp-angebotsreife-lazy" data-reife-url="…">` im Accordion-Body + ein einmaliger
  (`@once`) vanilla-JS-Loader, der das Panel per Fetch nachlädt. Bei Fehler graceful Text (View bricht nicht).

**Neu (additiv):**
- `WpAngebotsreifeController::panelPartial()` (Methode in bestehender Datei) — gibt NUR das Panel-Partial zurück.
- `tests/Feature/Offer/WpAngebotsreifeEmbedTest.php`.

## Bewusst NICHT geändert
`NewLeadsController` (kein Daten-Fluss-Eingriff — der Loader lädt read-only über die Offer-Route),
`LeadOverviewController`/Kanban (= Folgepaket 1b-b), Angebotslogik, `OfferController`/Wizard. Keine Migration,
keine Persistenz, keine zweite Statuswahrheit, kein Button-Gate, keine PV/Bivalenz/Auslegung.

## Geprüfte Nutzung
`customer_object_profile` gerendert von `NewLeadsController:2736`; iteriert `$product_list` (lead_product_list) je
Kunde/Objekt (`:420-423`), Accordion-Body ab `:531`. `$product->id` = lead_product_list-Zeilen-ID → Panel-Ziel.
Der Loader ruft `offers.angebotsreife.panel` (read-only, auth, whereNumber).

## Tests
Partial-Endpoint (200/Markup, Gast→Redirect/401, nicht-numerisch→404) + View-Platzhalter (WP-Zeile rendert Hook,
Nicht-WP nicht) + Paket-1-/Offer-Regression grün.

## Rückweg (Notfall)
1. `routes/web.php` und `customer_object_profile.blade.php` aus diesem Archiv zurückspielen (bzw. `git revert`).
2. Neue Controller-Methode + Test löschen. Rein additiv, kein DB-/Schemaeingriff, keine Persistenz → verlustfrei.
