# Archiv-Manifest — Paket 1b-b: Kanban Angebotsreife-Badge + Filter (read-only)

**Datum:** 2026-07-12 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** `docs/bereich2-angebotsworkflow-konzept.md` (Paket 1b) · Paket-1-Commit `1faeba7` · 1b-a `9b13b4f`

## Warum archiviert
Zwei **Bestandsdateien** werden berührt → `rueckfall-archiv-regeln.md` Variante B.

## Originalpfade → Archivkopien
- `routes/web.php` → `_archiv/2026-07-12/wp-angebotsreife-1b-b/web.php.original`
- `resources/views/admin/new_leads/layouts/kanban.blade.php`
  → `_archiv/2026-07-12/wp-angebotsreife-1b-b/resources/views/admin/new_leads/layouts/kanban.blade.php`

## Was geändert/neu ist
**Bestandsdateien geändert:**
- `routes/web.php`: +1 read-only Route `GET /offers/angebotsreife-batch` (`offers.angebotsreife.index`, `auth`, gleiche `offers.`-Gruppe).
- `kanban.blade.php`: Filterleiste in `.kb-toolbar` + **WP-gegateter** Badge-Hook je Karte (`$lead->product_id == 2`) + einmaliger (`@once`) vanilla-JS-Loader (Batch-Fetch + clientseitiger Filter). Graceful: Ladefehler bricht das Board nicht; ohne JS bleibt es normal nutzbar.

**Neu (additiv):**
- `WpAngebotsreifeController::index()` — read-only Batch: `ids` nur numerisch, hart auf 100 gecappt, ungültig/leer → `[]`; JSON nur `{id,status,percent,angebotsfaehig}` (keine PII).
- `tests/Feature/Offer/WpAngebotsreifeBatchTest.php`.

## Bewusst NICHT geändert
`LeadOverviewController` (keine Datenfluss-/Query-Änderung — Badges lazy per Batch, Filter clientseitig),
`NewLeadsController`, Angebotslogik/`OfferController`/Wizard. Keine Migration, keine Persistenz, keine zweite
Statuswahrheit, kein Button-Gate, keine PV/Bivalenz/Auslegung.

## Geprüfte Nutzung
`kanban.blade.php` gerendert von `LeadOverviewController:4742`; Karte trägt `data-id="{{ $lead->lead_product_id }}"`
= `lpl.id as lead_product_id` = `lead_product_lists.id` (`baseSelectColumns:3272`) → korrekter Reife-Anker. Der
Batch-Endpoint liest read-only über `OfferReadinessService::fuerId` (nur Labels/Status/%).

## Tests
Batch (gültige ids / leer-kaputt → [] / Cap greift / Gast abgewiesen / keine PII) + Kanban-Quellprüfung
(Filterleiste + Badge-Hook + WP-Gating + @once) + Regression Paket 1/1b-a/F2/Offer/Form grün.

## Rückweg (Notfall)
1. `routes/web.php` + `kanban.blade.php` aus diesem Archiv zurückspielen (bzw. `git revert`).
2. Neue Controller-Methode + Test löschen. Rein additiv, kein DB-/Schemaeingriff, keine Persistenz → verlustfrei.
