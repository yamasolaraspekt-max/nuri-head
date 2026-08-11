# W-05 · Raum erkennen — CODE

**Angebunden aus `resources/planner/hausplaner/geometry/roomDetection.ts`** — 190 Zeilen, 4 Ausfuhren.
Aus dem Code abgeleitet, nicht umgekehrt.

`RaumKante` (26) · `ErkannterRaum` (35) · `signierteFlaeche()` (70) · `erkenneRaeume()` (82)

**Eingaben:** `WallNode` aus `domain/scene.types`, `Punkt` aus `geometry/wallGeometry` (beides
Typ-Importe, Z.23-24).

## ZWEI AUSSCHLÜSSE — beide mit Grund

```text
resources/planner/hausplaner/geometry/grundriss.ts        NICHT Gegenstand
resources/planner/hausplaner/geometry/polygonFlaeche.ts   NICHT Gegenstand
```

**`grundriss.ts`** heißt „Grundriss" und wird deshalb immer wieder W-05 zugeordnet — *die Matrix des
Planners hat es selbst getan*. **Der Name ist die Falle, nicht der Inhalt.**

**`polygonFlaeche.ts`** ist der gefährlichere Ausschluss: sie rechnet **dieselbe Schuhbandformel ein
zweites Mal** — `polygonFlaecheM2()`, mit `Math.abs(summe) / 2`, Ergebnis in **m²**, mit Prüfung auf
`Number.isFinite` und Rückgabe `0` bei unbrauchbarer Eingabe.

| | `signierteFlaeche()` (W-05) | `polygonFlaecheM2()` (Ausschluss) |
|---|---|---|
| Betrag | **nein** — Vorzeichen wird gebraucht | **ja** |
| Einheit | mm² | **m²** |
| bei kaputter Eingabe | keine Prüfung | **`0`** |

**Zwei Rechnungen derselben Formel, absichtlich getrennt.** *Wer sie zusammenlegt, muss vorher
entscheiden, wessen Verhalten gilt — das ist keine Aufräumarbeit.*

## Was gebaut ist und was nicht

**Gebaut:** die Rechenschicht, rein. **Nicht vorhanden:** eine Werkzeugschicht — es gibt keine.
*Stufe 2 (`GEBAUT`) müsste erst eine schaffen; das ist ein eigener Auftrag und eine eigene Frage.*
