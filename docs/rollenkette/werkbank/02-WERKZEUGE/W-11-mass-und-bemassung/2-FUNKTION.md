# W-11 · Maß und Bemaßung — FUNKTION

## Zwei Schichten, drei Module

### RECHENSCHICHT — was gemessen wird

| Modul | Zeilen / Ausfuhren | Aufgabe |
|---|---|---|
| `resources/planner/hausplaner/geometry/masskette.ts` | 118 / 7 | **eine Kette aus Zahlen** — sortieren, entdoppeln, Abstände bilden |
| `resources/planner/hausplaner/geometry/bemassung.ts` | 108 / 6 | **die Ketten eines Grundrisses** — innen die Öffnungskette, außen das Gesamtmaß |

`MassPunkt` (9) · `MassSegment` (14) · `masskette()` (29) · `Bbox` (45) ·
`GrundrissMassketten` (52) · `grundrissMassketten()` (71) · `punkteMassketten()` (102)

`BemPunkt` (20) · `BemWand` (24) · `BemOeffnung` (30) · `AchsKetten` (36) · `Bemassung` (43) ·
`bemassung()` (52)

### EINGABESCHICHT — was der Anwender tippt

| Modul | Zeilen / Ausfuhren | Aufgabe |
|---|---|---|
| `resources/planner/hausplaner/geometry/masseingabe.ts` | 169 / 9 | **Maßeingabe während des Zeichnens** — Ziffern, Feldwechsel, Richtung aus dem Zeiger |

`MassPunkt` (25) · `istBrauchbareLaenge()` (40) · `richtungAus()` (55) · `punktAusLaenge()` (82) ·
`MassEingabe` (130) · `oeffneMit()` (138) · `wechsleFeld()` (143) · `tippe()` (148) ·
`massEingabeText()` (160)

## Die einzige Abhängigkeit zwischen den Modulen

```text
resources/planner/hausplaner/geometry/bemassung.ts:18
    import { masskette, type MassSegment, type Bbox } from './masskette';
```

**Sonst keine.** `masskette.ts` und `masseingabe.ts` haben **überhaupt keine Importe** — gemessen,
nicht angenommen. *Die Eingabeschicht ruft die Rechenschicht nicht auf und umgekehrt.*

## Was die Rechenschicht ausdrücklich NICHT tut

*„Der Renderer LIEST das Ergebnis; hier wird nur gerechnet, nichts gezeichnet, nichts geschrieben."*
(`masskette.ts:5-6`). An der einzigen Aufrufstelle steht derselbe Satz noch einmal:
*„mehrstufige Bemaßung (nur lesen, kein Command)"* (`app/HausplanerApp.tsx:1266`).
