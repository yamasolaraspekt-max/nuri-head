# Rückfall-Archiv — G0c-2 Geometrie-Profil-Persistenz

**Stand:** 2026-07-14 · **Zweck:** Rückfallpfad für den G0c-2-Umbau (objektgebundene, versionierte
Geometrie-Persistenz). Gemäß Rückfall-/Archiv-Regel: kein Löschen, Original erhalten, Rückfallpfad je Paket.

## Was G0c-2 ändert (Verhalten-only, keine Migration)
`GrundrissController::speichern` schreibt die Geometrie NICHT mehr destruktiv nach `raum_geometrien`
(`updateOrCreate`) + `heizlast_bauteile` (`bauteile()->delete()`), sondern als **neue, objektgebundene,
versionierte Anforderungsprofil-Version** in `anforderungsprofile.gebaeude_geometrie`
(`AnforderungsprofilService::neueVersion`/`anlegen` + `aktivieren`). Objekt-Pflicht (`alternative_id`);
objektlose Persistenz → 422 `objekt_fehlt`. Topologie-Gate (G0b) bleibt vorgelagerter Pflichtdurchgang.
Alt-Pfad (`baueProjekt`/`schreibeInProjekt`) bleibt im Code (deprecated, nur noch transiente `vorschau`),
NICHT gelöscht.

## Archivierte Originale (Vor-Zustand, git `7a2b829`)
| Datei | Original hier | Rückfall |
|---|---|---|
| `app/Http/Controllers/Energie/GrundrissController.php` | `GrundrissController.php.original` | `cp` zurück ODER `git checkout 7a2b829 -- app/Http/Controllers/Energie/GrundrissController.php` |
| `tests/Feature/Geometrie/GrundrissGateHttpTest.php` | `GrundrissGateHttpTest.php.original` | Original vor der G0c-2-Vertragsanpassung (Objekt-Pflicht vor Gate); Rückfall via `cp` ODER `git checkout 6a45985 -- <pfad>` |

## Neu erzeugt (kein Rückfall nötig, additiv)
- `tests/Feature/Geometrie/GrundrissProfilPersistenzTest.php` (5 Tests, neu)

## Rückfall-Kommando (kompletter Revert des G0c-2-Verhaltens)
```
git checkout 7a2b829 -- app/Http/Controllers/Energie/GrundrissController.php
git checkout 6a45985 -- tests/Feature/Geometrie/GrundrissGateHttpTest.php
rm tests/Feature/Geometrie/GrundrissProfilPersistenzTest.php
```
Kein Schema-Rückfall nötig (keine Migration). Kein Datenrückfall nötig (lokal 0 Bestandsgeometrie-Zeilen;
Hetzner unberührt bis Deploy-Tag).

## Wächter-Nachweis zum Archivzeitpunkt
`php artisan test` → **606 passed, 1 failed** (einziger Rotfall `InvoiceDeletionGuardTest`, Reverb
`localhost:6001` — E4-anerkannter Vorbestand). Geometrie-Tests: `GrundrissGateHttpTest` 4/4 +
`GrundrissProfilPersistenzTest` 5/5.
