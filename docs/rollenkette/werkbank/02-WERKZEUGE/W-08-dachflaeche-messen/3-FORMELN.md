# W-08 · Dachfläche messen — FORMELN

**Nur Nummern.** Die Formeln stehen in `../../01-MATHEMATIK/FORMELSAMMLUNG.md`.

| F-Nr | laut Register | gemessen |
|---|---|---|
| **F-011** Fläche eines Polygons | ja | **JA** — `polygonFlaeche.ts:44` (die Summe) und `:46` (Betrag, halbiert) |
| **F-023** Wahre Dachfläche aus Grundfläche | ja | **NEIN, im Code nicht vorhanden** — ein **alternativer Weg**: F-023 rechnet die Grundfläche über die Neigung hoch; **hier wird direkt in der geneigten Ebene gemessen** |
| **F-024** Ausrichtung einer Dachfläche (Azimut) | ja | **NEIN** — F-024 liegt in `resources/planner/hausplaner/geometry/wallGeometry.ts`, nicht hier; dieses Modul kennt keine Ausrichtung |

## Warum F-023 kein Mangel ist, sondern eine andere Bauart

F-023 und dieses Modul beantworten **dieselbe Frage auf zwei Wegen**: hochrechnen aus Grundfläche und
Neigung, oder direkt in der geneigten Ebene messen. **Das Modul hat sich für den zweiten entschieden**
— und braucht dafür keine Neigung, sondern die richtige Eingabe-Ebene (`2-FUNKTION`).

*Beide Wege im Haus zu haben wäre nicht falsch; sie unausgesprochen nebeneinander zu führen schon.*

## Und die Formel steht dreimal im Haus

Die Schuhbandformel ist an **drei** Stellen umgesetzt. Die Nummer F-011 zeigt auf eine davon.
**Welche Fassung gilt, entscheidet die Einheit der Eingabe** — die Gegenüberstellung steht in
`7-GRENZEN`, damit sie nur an einer Stelle steht.
