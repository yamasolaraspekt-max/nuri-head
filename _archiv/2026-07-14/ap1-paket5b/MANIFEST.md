# Rückfall-Manifest — AP-1 / Paket 5b: Heizlast-Belastbarkeits-Gate

**Datum:** 2026-07-14 · **Slice:** AP-1 / Paket 5b (freigegeben durch Yama, Scope „Slice 1").
**Regel:** `docs/rueckfall-archiv-regeln.md` — Originale erhalten, additiver Umbau, Rückfall path-scoped.

## Zweck der Änderung
Die technische Auslegung (WP-Reife) gilt nicht mehr binär durch bloße Existenz eines Heizlast-Werts.
Ein neuer read-only Klassifizierer stuft die Heizlast anhand **vorhandener** Felder
(`datenlage` / `quelle` / `erfassungsweg` + Präsenz `ergebnis_hinweis`) in
belastbar / eingeschränkt / vorläufig / unzureichend ein und speist einen abgestuften Reifegrad
(1.0 / 0.6 / 0.3 / 0.0). Keine Migration, keine Persistenz, keine zweite Heizlast-Wahrheit,
keine Preis-/Kataloglogik, kein offer_details-Schreibpfad.

## Neu (kein Original nötig)
- `app/Services/Heizlast/HeizlastBelastbarkeit.php` — reiner Klassifizierer.
- `tests/Feature/Offer/HeizlastBelastbarkeitsGateTest.php` — 9 Tests.

## Geändert (Original hier als `*.original`)
| Datei | Original | Art der Änderung |
|---|---|---|
| `app/Services/Offer/OfferReadinessService.php` | `OfferReadinessService.php.original` | `gradTechnischeAuslegung` von `exists()→1.0` auf abgestuften Grad via Klassifizierer; DTO um `heizlast_belastbarkeit` ergänzt (additiv). |
| `app/Services/Offer/AuslegungVorschlagService.php` | `AuslegungVorschlagService.php.original` | `werteLesen` liest zusätzlich `quelle`/`erfassungsweg`; DTO um `verbindlich`/`belastbarkeit` + Markierung in `kurzergebnis` (additiv, read-only). |
| `app/Services/Heizlast/WaermepumpenMatchService.php` | `WaermepumpenMatchService.php.original` | optionaler additiver Parameter `bool $belastbar = true`; Ergebnis um `verbindlich` + Hinweis ergänzt. Default = unverändertes Verhalten. |
| `resources/views/admin/offer/partials/angebotsreife_panel.blade.php` | `angebotsreife_panel.blade.php.original` | kleines read-only Belastbarkeits-Chip (reine Anzeige des DTO). |
| `tests/Feature/Offer/OfferReadinessServiceTest.php` | `OfferReadinessServiceTest.php.original` | Fixture `setzeAuslegung`: belastbare Heizlast (`datenlage='berechnet'`, `quelle='HeizlastRechner'`) — spiegelt den realen Adapter. |
| `tests/Feature/Offer/WpAngebotsreifePanelTest.php` | `WpAngebotsreifePanelTest.php.original` | dieselbe Fixture-Anpassung. |

## Rückfall
Reine Datei-Reverts (kein Schema, keine Daten berührt):
1. `*.original` zurückkopieren über die jeweilige Datei.
2. Neue Dateien (`HeizlastBelastbarkeit.php`, `HeizlastBelastbarkeitsGateTest.php`) löschen.
3. Keine Migration/Seeder rückgängig zu machen (es wurde keine ausgeführt).

## Bewusste Heuristik-Grenze (dokumentiert, kein Bug)
„belastbar" verlangt `datenlage='berechnet'` **und** `quelle` enthält `HeizlastRechner` (raumweise DIN).
Eine **manuell** eingetragene, fachlich freigegebene DIN-Heizlast ohne diesen Quelle-Marker wird
konservativ als **eingeschränkt** eingestuft (Downgrade im Zweifel = gewollt). Ein echter
„freigegeben"-Marker gehört zu Kapitel 13 (Freigabe-Workflow) und ist bewusst nicht Teil von 5b.

## Evaluator-Auflagen (read-only Evaluator, 2026-07-14 — Doku, kein Code-Blocker)
1. **Match-Naht vorbereitet, produktiv noch NICHT verdrahtet:** Der optionale `belastbar`-Parameter von
   `WaermepumpenMatchService::kandidaten` existiert additiv (Default true). Der einzige Produktiv-Aufrufer
   (`HeizlastController::wpBerechnen`, Z. 180) übergibt ihn NICHT → die „unverbindliches Ranking"-Markierung
   ist real erst über die Vorschau (`AuslegungVorschlagService.verbindlich`) aktiv; die Match-Verdrahtung
   folgt in einem späteren Slice (Kapitel 7, Bivalenz-/Match-Verdrahtung am Kern).
2. **Offene Abhängigkeit Kapitel 13:** „belastbar" verlangt strikt `quelle` enthält `HeizlastRechner`.
   Eine manuell freigegebene DIN-Heizlast ohne diesen Marker wird konservativ als „eingeschränkt" gewertet.
   Sobald der Freigabe-Workflow (Kapitel 13) einen „fachlich freigegeben"-Marker liefert, ist dieser als
   zweiter Belastbar-Pfad zu ergänzen.

## Tests (Zahlen)
- Neuer Test: 9 passed (43 Assertions).
- Offer+Anforderungsprofil+Heizlast: 125 passed (501 Assertions).
- Volle Suite: 548 passed, 1 failed — der eine Fehler `Invoice\InvoiceDeletionGuardTest`
  ist ein umgebungsbedingter Vorbestand (Pusher/Reverb `localhost:6001` nicht erreichbar,
  Broadcasting; referenziert keine AP-1-Symbole), unabhängig von dieser Änderung.
