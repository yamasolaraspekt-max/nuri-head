# Archiv-Manifest — P3-c-preview: Auslegung → proposed sections (READ-ONLY Vorschau)

**Datum:** 2026-07-13 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** P3-0 `docs/bereich2-auslegung-inventur-bewertung-layout.md` · Ziel-Wahrheiten Anforderungsprofil / offer_details.sections / P1-a.

## Warum archiviert
Eine **Bestandsdatei** (`routes/web.php`) wird berührt → `rueckfall-archiv-regeln.md` Variante B.

## Originalpfad → Archivkopie
- `routes/web.php` → `_archiv/2026-07-13/p3c-auslegung-vorschau/web.php.original`

## Was neu/geändert ist
**Neu (additiv, read-only):**
- `app/Services/Offer/AuslegungVorschlagService.php` — baut aus dem AKTIVEN Anforderungsprofil eine
  *Vorschlags*-Struktur (Form `offer_details.sections`). **Kein Write, keine Persistenz, kein Preis,
  kein `component_id`** (Geräte→Komponente-Zuordnung nicht eindeutig = P3-d). Operanden-Gate: fehlender
  Wert ⇒ Position als Aufgabe (`datenlage='fehlt'`), nichts erfunden.
- `app/Http/Controllers/Customer/Offer/AuslegungVorschlagController.php` — read-only `panelPartial`.
- `resources/views/admin/offer/partials/auslegung_vorschlag_panel.blade.php` — read-only Panel
  (Kurzergebnis, Ampel, Datenlage, Preisstatus, automatisch/zu-bestätigen, Aufgaben; Übernahme disabled).
- `tests/Feature/Offer/AuslegungVorschlagPreviewTest.php`.

**Bestandsdatei geändert:**
- `routes/web.php`: **+1 read-only GET** `/offers/auslegung-vorschau/{leadProductList}` (`offers.auslegung.vorschau`, `auth`, whereNumber).

## Verbindliche Regeln (Yama-Freigabe P3-c)
Nur WP `article_groups.id=2`; `id=16` (=„Tapete") NIE verwendet. Keine Preise/Schätzpreise, kein
`component_id` ⇒ alle Positionen `preis_status='katalog_anker_fehlt'`. WP-Set (master_sets ag=2) nur als
Hinweis („bepreisbares WP-Set vorhanden, Zuordnung offen P3-d"). Vuexy/jQuery, kein Alpine.

## Bewusst NICHT geändert / Nicht-Ziele
`OfferController`/`OfferWizardController`/`OfferTemplatePickerController` · `offer_details`-Schreibpfad ·
`CatalogPriceGuard` · MasterSet/Komponenten · Anforderungsprofil-Schreiben · Seeder · Migration.
Kein Alpine, kein Push, kein `git add -A`.

## Rückweg (Notfall)
1. `routes/web.php` aus Archiv zurückspielen (bzw. `git revert`).
2. Die drei neuen `app/`-Dateien + Blade + Test löschen. Rein additiv, read-only, kein DB-/Schemaeingriff,
   keine Persistenz → verlustfrei.
