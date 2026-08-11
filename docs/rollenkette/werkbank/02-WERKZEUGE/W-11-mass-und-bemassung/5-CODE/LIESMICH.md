# W-11 · Maß und Bemaßung — CODE

**Angebunden aus drei vorhandenen Modulen** — aus dem Code abgeleitet, nicht umgekehrt.

## Rechenschicht

**`resources/planner/hausplaner/geometry/masskette.ts` — 118 Zeilen, 7 Ausfuhren**
`MassPunkt` · `MassSegment` · `masskette()` · `Bbox` · `GrundrissMassketten` ·
`grundrissMassketten()` · `punkteMassketten()`

**`resources/planner/hausplaner/geometry/bemassung.ts` — 108 Zeilen, 6 Ausfuhren**
`BemPunkt` · `BemWand` · `BemOeffnung` · `AchsKetten` · `Bemassung` · `bemassung()`

## Eingabeschicht

**`resources/planner/hausplaner/geometry/masseingabe.ts` — 169 Zeilen, 9 Ausfuhren**
`MassPunkt` · `istBrauchbareLaenge()` · `richtungAus()` · `punktAusLaenge()` · `MassEingabe` ·
`oeffneMit()` · `wechsleFeld()` · `tippe()` · `massEingabeText()`

## Verdrahtung — einmal, an einer Stelle

```text
app/HausplanerApp.tsx:22    import { bemassung } from '../geometry/bemassung';
app/HausplanerApp.tsx:1268  const bem = bemassung(waende…, oeffnungen…);
```

**Der einzige Aufrufer.** Er übergibt **alle** Wände und **alle** Öffnungen.

## Was gebaut ist und was nicht

**Gebaut:** beide Schichten, rein. **Nicht Gegenstand dieser Stufe:** die Werkzeugschicht —
Stufe 2 (`GEBAUT`) folgt als eigener Auftrag.
