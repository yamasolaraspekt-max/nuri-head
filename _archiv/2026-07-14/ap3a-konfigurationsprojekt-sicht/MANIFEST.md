# Rückfall-Manifest — AP-3a: Read-only Konfigurationsprojekt-Sicht je Objekt

**Datum:** 2026-07-14 · **Slice:** AP-3a (Yama-Freigabe). **Regel:** `docs/rueckfall-archiv-regeln.md`.

## Zweck
Objektbezogene, read-only Übersicht aller aktivierten Gewerke/Module eines Objekts
(`lead_alternative_adds` → n×`lead_product_lists`) — Aggregation aus vorhandenen read-only
Services (`OfferReadinessService` inkl. AP-1 `HeizlastBelastbarkeit`, `AuslegungVorschlagService`
nur für WP). Umsetzung von Option E (AP-3). Keine neue Tabelle, kein Schreibpfad, keine Migration,
kein `offer_details`-Write, keine Preis-/Katalog-/3D-/PV-Logik, kein `PvProjektService`-Aufruf,
keine Änderung an WP-Gates, PII-arm.

## Neu (kein Original nötig)
- `app/Services/Offer/KonfigurationsprojektService.php`
- `app/Http/Controllers/Customer/Offer/KonfigurationsprojektController.php`
- `resources/views/admin/konfiguration/objekt.blade.php`
- `tests/Feature/Offer/KonfigurationsprojektSichtTest.php`

## Geändert (Original als `*.original`)
| Datei | Original | Änderung |
|---|---|---|
| `routes/web.php` | `web.php.original` | **eine** additive Route in der `['web','auth']`-Gruppe: `GET /objekt/{alternative}/konfiguration` → `KonfigurationsprojektController@show`, `whereNumber('alternative')`, Name `objekt.konfiguration`. |

## Nicht geändert (bewusst)
- **Kein Link ins große Objektprofil** (Startblock-Vorgabe: nur mit vorheriger STOPP-Meldung; hier nicht gemacht).
- WP-Gates (`OfferReadinessService`/`OfferReadinessGate`/`HeizlastBelastbarkeit`) nur gelesen, nicht geändert.

## Rückfall
1. Route-Zeile aus `routes/web.php` entfernen (bzw. `web.php.original` zurückkopieren).
2. Die 4 neuen Dateien löschen.
Kein Schema/Daten berührt → path-scoped, sofort rückbaubar.

## Tests (Zahlen)
- Neu: 11 passed (38 Assertions).
- Offer-Suite Regression: 112 passed (456 Assertions).
