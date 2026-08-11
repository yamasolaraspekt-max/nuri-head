# W-08 · Dachfläche messen — CODE

**Angebunden aus `resources/planner/hausplaner/geometry/polygonFlaeche.ts`** — 48 Zeilen, 2 Ausfuhren, selbst nachgezählt.
`Punkt2D` (19) · `polygonFlaecheM2()` (31). **Keine Importe** — das Modul hängt an nichts.

## Die Aufrufer — vier, gemessen

| Datei | Zeile |
|---|---|
| `resources/planner/hausplaner/renderers/three-d/deckenMesh.ts` | 7 |
| `resources/planner/hausplaner/geometry/dachAusschnitt.ts` | 26 |
| `resources/planner/hausplaner/geometry/dachformVorlagen.ts` | 33 |
| `resources/planner/hausplaner/geometry/grundriss.ts` | 19 |

**Der Auftrag nennt fünf — gemessen sind es vier.** *Nicht korrigiert, sondern gemeldet.*

## AUSSCHLUSS `wandFlaeche.ts` — und die Begründung des Auftrags trifft nicht zu

`resources/planner/hausplaner/geometry/wandFlaeche.ts` gehört zu **W-02** und ist hier Nicht-Gegenstand.

**Der Auftrag begründet den Ausschluss damit, dass `wandFlaeche.ts` `polygonFlaecheM2` *benutzt*.
Gemessen tut es das nicht.** Es gibt **keinen Import**; die einzige Fundstelle ist ein Kommentar, und
der sagt das Gegenteil:

```text
wandFlaeche.ts:27
  * - **Keine zweite Flaechenengine.** `polygonFlaecheM2` und die Raumerkennung bleiben, was sie sind.
```

**Der Ausschluss bleibt richtig, seine Begründung nicht.** *`wandFlaeche.ts` grenzt sich ausdrücklich
ab — es ist ein Nachbar, der Abstand hält, kein Benutzer.*

## Was gebaut ist und was nicht

**Gebaut:** die Rechnung, rein — *„bewusst rein (keine React-/THREE-Abhängigkeit)"* (Z.15).
**Keine Werkzeugschicht**, und hier ist das kein Mangel (siehe `4-BEDIENUNG`).
