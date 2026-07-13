# Archiv-Manifest — P3-d0a-fix: WP-Katalog-Matching liest alle WP-Sets (READ-ONLY)

**Datum:** 2026-07-13 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** P3-d0a `c1cab4e` (Diagnose las nur das erste WP-Set) · P3-d2b (kuratiertes Set später sichtbar machen).

## Warum archiviert
Zwei **committete Bestandsdateien** werden geändert → Sicherungskopie (zusätzlich zu `git revert`).

## Originalpfade → Archivkopien
- `app/Services/Offer/WpKatalogMatchingService.php` → `WpKatalogMatchingService.php.original`
- `resources/views/admin/offer/partials/wp_katalog_matching_panel.blade.php` → `wp_katalog_matching_panel.blade.php.original`

## Was geändert ist (read-only Verhalten bleibt)
- `WpKatalogMatchingService::setProdukteLesen()`: liest **alle** `master_sets` mit `article_group_id=2`
  (Join `master_sets`, `whereNull(deleted_at)`) statt nur des ersten; selektiert zusätzlich
  `set_id`/`set_name`/`set_status`.
- `diagnose()`: Set-Produkt-Ausgabe trägt `set_id`/`set_name`/**`set_herkunft`**; Schnittmenge = Specs ∩
  **Vereinigung aller** ag=2-Set-Komponenten-`product_id`.
- Neuer Helfer `setHerkunft()` — **marker-basiert** (`kuratiert`/`demo` nur bei explizitem Marker in
  status/name, sonst `unbekannt`; **keine Annahme**).
- Panel: rechte Spalte **nach WP-Set gruppiert** (Set-Name + Herkunft-Badge je Block).

## Unverändert / Nicht-Ziele
`auto_anker_moeglich=false` bleibt · **kein** `component_id`/Preisanker in der Ausgabe · **kein** Schreiben ·
keine Katalogänderung · keine Migration · kein `CatalogPriceGuard` · kein `offer_details` · Controller/Route
unverändert.

## Rückweg (Notfall)
**Variante A:** `git revert` des d0a-fix-Commits. Alternativ die zwei Dateien aus diesem Archiv zurückspielen.
Rein logische Leseänderung, kein DB-/Schemaeingriff → verlustfrei.
