# W-05 · Raum erkennen — FUNKTION

## Das Verfahren in drei Schritten — so wie der Code es beschreibt

```text
1  Wandachsen -> Kanten-Graph; Waende an T-PUNKTEN GETEILT
   (Endpunkt einer Wand liegt EXAKT auf der Achse einer anderen)
2  Je Knoten die Halbkanten nach Winkel sortiert; der Umlauf nimmt die im
   Uhrzeigersinn naechste nach der Gegenkante
3  Innenflaechen = Umlaeufe mit POSITIVER Shoelace-Flaeche (y nach oben, CCW);
   der Aussenumlauf ist negativ und wird verworfen
```

*Wörtlich aus `resources/planner/hausplaner/geometry/roomDetection.ts:4-14`.*

## „mm-Integer-Welt, keine Toleranz-Magie"

Ein T-Punkt gilt, wenn ein Endpunkt **exakt** auf der Achse liegt — **nicht ungefähr**. Der Code sagt
es so, und die Wandlänge wird gegen **exakt 0** geprüft (`roomDetection.ts:89`), nicht gegen ein
Epsilon. *Das ist möglich, weil alles ganzzahlige Millimeter sind; ein Fangwerkzeug (W-01) sorgt
dafür, dass Punkte wirklich aufeinandertreffen.*

## Die Ausfuhren mit Fundstelle

| Zeile | Ausfuhr | Rolle |
|---|---|---|
| 26 | `RaumKante` | Kante mit `wallId` und **Offset-Fenster** entlang der Ursprungswand |
| 35 | `ErkannterRaum` | `polygon` · `kanten` · `flaecheMm2` · `volumenMm3` |
| 70 | `signierteFlaeche()` | Shoelace **mit Vorzeichen** |
| 82 | `erkenneRaeume()` | der ganze Durchgang |

**`RaumKante` merkt sich, aus welcher Wand sie stammt und welchen Abschnitt sie belegt** — deshalb
sind Wände an T-Punkten teilbar, ohne dass die Herkunft verlorengeht.

## Wo es läuft — es gibt keinen Knopf

```text
renderers/three-d/szene.ts:357        erkenneRaeume(waende, level.defaultWallHeight…)
app/ableitungen.ts:62                 raeumeAus() -> erkenneRaeume(…)
app/HausplanerApp.tsx:21              importiert
```

**Drei Aufrufstellen, keine davon ein Werkzeug.** *Siehe `4-BEDIENUNG`.*
