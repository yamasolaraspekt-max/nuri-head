# Tracking — Hausplaner-Wellen (Stand 2026-07-23)

Ein Ort für Ballbesitz und offenen Rest, damit zwischen den Wellen nichts verloren geht.

## Welle „Fenster/Treppe/Dach" — Evaluator: ALLE APPROVED
| Commit | Inhalt | Verdikt |
|---|---|---|
| 08e584a | Fenster-Produktkern (Uw/RC/Preis) | APPROVED |
| 49e4f50 | Fenster-Domain (additiv) | APPROVED |
| d02627d | Fenster-Konfigurator-Panel | APPROVED |
| 4135dfe | Treppentypen + treppeAlsSvg (reines Modul) | APPROVED (nicht eingebunden) |
| 0f05052 | Plattform-Registry | APPROVED |
| bf9510b | Sparren/Dachstuhl + Treppen-Validierung | APPROVED |

Ballbesitz: Yama. Prüf-Bundle war 438dd696.

## Welle „v9-Studio" — Generator fertig, Evaluator-Review offen
Commits: 970f0cc, 14c4d0e, 514967c, ff707f9, 95720de, 533a2e7.
Auftrag: `docs/evaluator-auftrag-2026-07-23-v9-welle-komplett.md`.
Generator-Gates: tsc Exit 0 · 285/285 Tests · Bundle reproduzierbar · alle Modi headless ohne Pageerror.

## Entscheidungen (Planner/Generator, selbst getroffen)
1. **`4135dfe` treppenTypen/treppeAlsSvg fürs Auswählen fallengelassen** — das Premium-Treppen-Icon-Raster (Yamas SVGs) ersetzt die gerechnete Strichzeichnung als Panel-Wähler. Die gerechneten Grundflächenmaße bleiben im Modul für die spätere echte Geometrie-Erzeugung reserviert.
2. **Farbsystem bleibt zwei-tönig, rollenbasiert**: Teal = Studio-Navigation/Chrome; Grün (Marke) = Primäraktion + Editor-Auswahl; semantische Statusfarben (rot/amber/grün) getrennt gehalten. Deckt die ux-Rubrik „Marke als Akzent, Status getrennt" ab; kein Umbau nötig.

## Offener Rest (nächste Scheiben, brauchen Entscheidung/Bau)
- [ ] Guided-Schritt-BADGE-Status aus dem Modell ableiten (Zahlen sind schon echt).
- [x] Treppen-Öffnung aus dem Wizard ins Modell — erledigt (4f997f6, ObjectNode stair, Standard-Lauflinie).
- [ ] ConfiguratorPackage serverseitig persistieren (Endpoint) statt nur JSON-Download.
- [ ] Schüco-/Glas-Echtdaten statt Katalog-Platzhalter (aus DATENBEDARF-SCHUECO-GLAS.md).
