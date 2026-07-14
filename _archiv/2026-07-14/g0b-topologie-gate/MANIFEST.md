# Rückfall-Manifest — G0b/AP-4b: Topologie-Gate & Toleranzvertrag

**Datum:** 2026-07-14 · **Regel:** `docs/rueckfall-archiv-regeln.md`. Alles lokal/reversibel, kein Push, keine Migration, kein Mirror-Eingriff.

## Zweck
Vorgelagertes Topologie-Gate am Ingestion-Punkt (`GrundrissController::vorschau/speichern`); ungültige Geometrie wird abgelehnt statt still gerechnet. Mirror (`GeometrieAbleitungService`/`RaumHuelleService`/`HeizlastRechner`) byte-unverändert.

## Neu (kein Original nötig)
- `config/geometrie.php`, `app/Support/GeometrieToleranz.php`
- `app/Services/Geometrie/{TopologieGate,TopologieErgebnis,GeometrieUngueltigException}.php`
- `tests/Unit/Geometrie/TopologieGateTest.php`
- `tests/Feature/Geometrie/{GrundrissGateHttpTest,GeometrieSchreibpfadWaechterTest,BestandScanTest}.php`
- `tests/Fixtures/geometrie-bestand-2026-07-14.json`
- `docs/{g0b-topologie-gate-startblock,g0b-release-manifest,deploy-tag-checkliste-g0b-geometrie-gate}.md`

## Geändert (Original als `*.original`)
| Datei | Original | Änderung |
|---|---|---|
| `app/Http/Controllers/Energie/GrundrissController.php` | `GrundrissController.php.original` | TopologieGate injiziert; `gatePruefung()` + Aufruf in `vorschau`/`speichern`; `GeometrieUngueltigException`-Catch (422 + Blocker). |
| `tests/Unit/Heizlast/GeometrieBelastbarkeitLueckenTest.php` | `GeometrieBelastbarkeitLueckenTest.php.original` | P6/P7/P8 von `markTestIncomplete` auf echte grüne Gate-Ablehnung gehoben. |
| `tests/Unit/Heizlast/RaumHuelleServiceTest.php` | `RaumHuelleServiceTest.php.original` | Öffnung>Wand-Fall auf Gate-Ablehnung gehoben (Mirror-Clamp bleibt dokumentiert). |

## Rückfall
1. `GrundrissController.php.original` zurückkopieren (Gate-Verankerung entfernt).
2. Die 2 Test-Originale zurückkopieren (Fälle wieder incomplete).
3. Neue Dateien löschen (Geometrie-Service/Config/Support/Tests/Fixture/Docs).
Kein Schema/Daten berührt → path-scoped, sofort reversibel.
