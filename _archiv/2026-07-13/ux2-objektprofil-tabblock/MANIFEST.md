# Archiv-Manifest — UX-2: Objektprofil-Tab-Block (READ-ONLY Anzeige)

**Datum:** 2026-07-13 · **Rolle:** Generator · **Status:** kein Commit
**Bezug:** UX-1 Konzept · Paket 1b-a (bisherige Einzel-Einbettung Angebotsreife) · P3-c · P3-d0a(+fix).

## Warum archiviert
Eine **committete Bestandsdatei** (Objektprofil-View) wird geändert → Sicherungskopie (zusätzlich zu `git revert`).

## Originalpfad → Archivkopie
- `resources/views/admin/new_leads/customer_object_profile.blade.php` → `customer_object_profile.blade.php.original`

## Was geändert ist (reine Anzeige)
Der bisherige 1b-a-Block (nur Angebotsreife lazy) wird durch einen **kompakten Tab-Block** ersetzt, der die
**drei bestehenden read-only Routen** wiederverwendet:
- Tab **„Reife"** (Technik) → `offers.angebotsreife.panel` — lädt automatisch beim Aufklappen.
- Tab **„Auslegung"** (Technik) → `offers.auslegung.vorschau` — lädt **erst beim Klick**.
- Tab **„Preis"** (Kaufmännisch / OMD-IDS) → `offers.wp-katalog-matching` — lädt **erst beim Klick**.

Jeder Tab lädt seinen Partial per **GET-Fetch** (`X-Requested-With`, `credentials:'same-origin'`, `try/catch`);
**Fehler in einem Tab** trifft nur diesen (isoliert). WP-Gate `product_id === 2` bleibt; Non-WP zeigt keinen Block.
Vanilla JS + Bootstrap-Stil, **kein Alpine**, `@once`-Loader (mehrfach-Block-fähig via `.ux2-tabblock`).

## Nicht geändert / Nicht-Ziele
Kein Controller · keine Route · kein Service · keine DB/Migration · kein neuer Schreibpfad · kein `<form>`/POST ·
kein `component_id`/Preisanker · keine Preislogik/Katalogänderung · kein Alpine.

## Rückweg (Notfall)
**Variante A:** `git revert` des UX-2-Commits. Alternativ die View aus diesem Archiv zurückspielen.
Rein anzeigende Änderung, kein DB-/Schema-/Routeneingriff → verlustfrei.
