# W-02 · Wand zeichnen — BEDIENUNG

## Was der Anwender tut

Zwei Punkte setzen. **Der Fang (W-01) liegt darunter** und bestimmt, wo die Punkte landen.

## Was er einstellt

| Größe | Wirkung |
|---|---|
| **Stärke** | Breite des Bandes, symmetrisch zur Achse |
| **Höhe** | Extrusion nach oben |
| **Seite** | `links` oder `rechts` — bestimmt den Azimut der Normalen |
| **Bezugsmaß** | `roh` oder `fertig` (`wandFlaeche.ts:38`) |

## Was er zurückbekommt — und was nicht

**Entweder Mengen oder Meldungen.** Nie beides.

| Meldungsart | Wann |
|---|---|
| `oeffnung-ragt-hinaus` | Öffnung liegt teilweise ausserhalb der Wand |
| `oeffnungen-ueberlappen` | zwei Öffnungen überschneiden sich |
| `oeffnung-hoeher-als-wand` | Öffnung höher als die Wand |
| `schichten-dicker-als-wand` | Aufbau dicker als die Wand selbst |
| `fremde-oeffnung` | Öffnung gehört nicht zu dieser Wand |

*Belegt als `MeldungArt` in `resources/planner/hausplaner/geometry/wandFlaeche.ts:77`.*

**Jede Meldung trägt Klartext mit den beteiligten Kennungen** — *„eine Meldung ohne Bezug ist keine
Meldung"* (`wandFlaeche.ts:84`).

## Die mm-Regel

Punkte sind **ganzzahlige Millimeter**. `istGanzzahlig()` (`wallGeometry.ts:53`) prüft die
Invariante. **Wandlängen dürfen für Vergleiche Gleitkomma sein — was gespeichert wird, ist ganzzahlig.**
