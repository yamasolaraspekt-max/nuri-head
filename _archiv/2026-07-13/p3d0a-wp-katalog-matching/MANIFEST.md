# Archiv-Manifest — P3-d0a: WP-Katalog-Matching-Vorschau (READ-ONLY)

**Datum:** 2026-07-13 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** P3-d0 `docs/bereich2-p3d0-wp-katalog-inventur.md` (Befund: Populationen disjunkt, Auto-Anker NEIN) · P3-c `b59d4e6`.

## Warum archiviert
Eine **Bestandsdatei** (`routes/web.php`) wird berührt → `rueckfall-archiv-regeln.md` Variante B.

## Originalpfad → Archivkopie
- `routes/web.php` → `_archiv/2026-07-13/p3d0a-wp-katalog-matching/web.php.original`

## Was neu/geändert ist
**Neu (additiv, read-only):**
- `app/Services/Offer/WpKatalogMatchingService.php` — stellt technische Kandidaten (`product_heat_pump_specs`)
  den Preis-Set-Produkten (`master_set_components`, WP-Set ag=2) gegenüber; berechnet Schnittmenge über
  `product_id`, Status je Zeile, Konflikte. **Kein Write, kein Preisanker, kein `component_id`,
  keine Preisberechnung.** VK/EK der Set-Komponenten NUR diagnostisch.
- `app/Http/Controllers/Customer/Offer/WpKatalogMatchingController.php` — read-only `panelPartial`.
- `resources/views/admin/offer/partials/wp_katalog_matching_panel.blade.php` — Zwei-Spalten-Vergleich
  (links Kandidaten, rechts Preis-Set), Schnittmenge, roter Banner bei 0, Status-Badges, Konflikt-Hinweise,
  nächste Aufgabe; **kein Übernahme-Button, keine Angebotsposition**.
- `tests/Feature/Offer/WpKatalogMatchingPreviewTest.php`.

**Bestandsdatei geändert:**
- `routes/web.php`: **+1 read-only GET** `/offers/wp-katalog-matching/{leadProductList}` (`offers.wp-katalog-matching`, `auth`, whereNumber).

## Verbindliche Regeln (Yama-Freigabe P3-d0a)
Nur WP `article_groups.id=2`; `id=16` (=„Tapete") nie. Kein Preisanker/`component_id`/Preisberechnung/
Angebotsposition. VK/EK nur diagnostisch. Auto-`component_id` bleibt NEIN. Vuexy/jQuery, kein Alpine.

## Bewusst NICHT geändert / Nicht-Ziele
`OfferController`/Wizard/`OfferTemplatePickerController` · `offer_details`-Schreibpfad · `CatalogPriceGuard` ·
MasterSet/Komponenten/Preise/Artikel · Anforderungsprofil · Seeder · Migration. Kein Push, kein `git add -A`.

## Rückweg (Notfall)
1. `routes/web.php` aus Archiv zurückspielen (bzw. `git revert`).
2. Die drei neuen `app/`-Dateien + Blade + Test löschen. Rein additiv, read-only, kein DB-/Schemaeingriff
   → verlustfrei.
