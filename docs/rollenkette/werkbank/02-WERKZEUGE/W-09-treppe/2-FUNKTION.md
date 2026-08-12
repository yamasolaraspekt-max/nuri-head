# W-09 · Treppe — FUNKTION

## Vier Schichten, sieben Module — jede Zeile selbst gezählt

### 1 · AUSLEGUNG — hier wird gerechnet und geprüft

| Modul | Z / Ausfuhren |
|---|---|
| `resources/planner/hausplaner/geometry/treppenBerechnung.ts` | **114 / 6** |

`berechneTreppe()` liefert die Maße **und** eine Prüfliste. *Das ist die einzige Stelle, an der
DIN 18065 im Code steht.*

### 2 · KATALOG — was es an Formen und Bauarten gibt

| Modul | Z / Ausfuhren | |
|---|---|---|
| `resources/planner/hausplaner/geometry/treppenTypen.ts` | **153 / 4** | Grundriss-Geometrie der Typen |
| `resources/planner/hausplaner/geometry/treppenBauarten.ts` | **38 / 3** | SVG-Bauarten, Icons unter `public/hausplaner/icons/treppe/` |

### 3 · DARSTELLUNG — wie sie gezeichnet wird

| Modul | Z / Ausfuhren | |
|---|---|---|
| `resources/planner/hausplaner/geometry/treppe2D.ts` | **93 / 4** | 2D-Symbol |
| `resources/planner/hausplaner/geometry/treppe3D.ts` | **74 / 4** | 3D-Körper |
| `resources/planner/hausplaner/geometry/treppeSvg.ts` | **142 / 5** | maßstäbliche Grundriss-Zeichnung |

### 4 · OBJEKT — wie sie in der Szene lebt

| Modul | Z / Ausfuhren | |
|---|---|---|
| `resources/planner/hausplaner/geometry/treppeObjekt.ts` | **84 / 4** | Treppe als `ObjectNode`, `objectType 'stair'` |

**Summe: 698 Zeilen.** *Deckt sich mit der Angabe des Auftrags — nachgezählt, nicht übernommen.*

## Woran die Schichten hängen

**Die Auslegung ist die Wurzel.** `treppe2D` und `treppe3D` nennen im Kopf ausdrücklich, dass ihre
Geometrie aus dem getesteten `berechneTreppe` kommt. *Wer die Rechnung ändert, ändert das Bild mit —
und umgekehrt nicht.*
