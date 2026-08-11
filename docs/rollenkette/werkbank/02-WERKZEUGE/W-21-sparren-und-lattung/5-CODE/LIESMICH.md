# W-21 · Sparren und Lattung — CODE

**Angebunden aus fünf vorhandenen Modulen** — 496 Zeilen, 25 Ausfuhren. Jede Zeilenzahl einzeln
nachgezählt, nicht aus der Summe abgeleitet.

| Modul | Z | Ausfuhren |
|---|---|---|
| `resources/planner/hausplaner/geometry/sparrenBerechnung.ts` | 131 | `Schneezone` (16) · `Holzklasse` (17) · `bodenschneelast()` (33) · `formbeiwertSchnee()` (45) · `SparrenEingabe` (52) · `SparrenErgebnis` (68) · `berechneSparren()` (86) |
| `resources/planner/hausplaner/geometry/sparrenTrennung.ts` | 67 | `SparrenTeilstueck` (19) · `sparrenTeilstuecke()` (37) · `istSicherTrennbar()` (59) |
| `resources/planner/hausplaner/geometry/schifterListe.ts` | 152 | `Punkt2D` (28) · `SchifterArt` (30) · `SchifterSparren` (32) · `SchifterMengen` (40) · `klassifiziereSchifter()` (58) · `schifterAusFlaeche()` (94) · `schifterMengen()` (113) · `HolzStueckRef` (134) · `schifterMengenAusListe()` (141) |
| `resources/planner/hausplaner/geometry/holzBauteile.ts` | 82 | `HolzStueckRef` (22) · `HolzBauteilMengen` (28) · `OFFENE_HOLZBAUTEILE` (45) · `holzBauteileAusListe()` (56) |
| `resources/planner/hausplaner/geometry/holzMengen.ts` | 64 | `HolzStueck` (23) · `HolzMengen` (29) · `holzMengenAusListe()` (44) |

## Eine Doppelung, die auffallen soll

**`HolzStueckRef` gibt es zweimal** — `schifterListe.ts:134` und `holzBauteile.ts:22`. *Kein Import
verbindet sie.* **Dieselbe Lage wie `MassPunkt` bei W-11:** ändert eine Seite, divergieren sie stumm.
*Hier nur benannt, nicht gemessen, ob die Felder heute deckungsgleich sind.*

## Was gebaut ist und was nicht

**Gebaut:** die Rechen- und Aggregationsschicht, rein — der Kopf sagt *„Keine three/Konva",
„Rein (keine React-/THREE-Abhängigkeit)"*.
**Nicht vorhanden:** eine Werkzeugschicht.
