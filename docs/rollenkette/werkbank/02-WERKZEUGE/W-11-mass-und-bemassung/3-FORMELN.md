# W-11 · Maß und Bemaßung — FORMELN

**Nur Nummern, keine ausgeschriebene Formel.** Die Formeln stehen in
`../../01-MATHEMATIK/FORMELSAMMLUNG.md`.

## Das Register nennt drei — gemessen sind es andere

| F-Nr | laut Register | im Code gefunden? |
|---|---|---|
| **F-001** Abstand zweier Punkte | ja | **JA** — `bemassung.ts:77` und `masseingabe.ts:58` (`Math.hypot`) |
| **F-002** Richtungswinkel | ja | **NEIN** — kein `atan2` in keinem der drei Module |
| **F-003** Lotfußpunkt | ja | **NEIN** — `lotAufGerade` kommt null mal vor |

**Eine Registerangabe ist keine Fundstelle.** *Zwei von drei genannten Formeln stehen nicht im Code
dieses Werkzeugs; das ist gemessen und wird hier benannt statt abgeschrieben.*

## Was stattdessen dasteht — der umgekehrte Weg

`masseingabe.ts:99-100` rechnet **`Math.cos` und `Math.sin`** — also **aus einem Winkel eine
Richtung**, nicht aus einer Richtung einen Winkel. **Das ist die Umkehrung von F-002 und nicht F-002.**
*Der Anwender tippt den Winkel; das Werkzeug misst ihn nicht.*

## Und eine Rechnung, die keine F-Nummer hat

Die Entdopplung in `masskette()` (Z.29-38): sortieren, runden, und alles, was näher als `toleranz`
beieinanderliegt, gilt als **ein** Bezugspunkt. **Dafür gibt es in der Sammlung keine Nummer.**
*Gemeldet, nicht erfunden — eine Formel zweiter Ordnung wäre schlimmer als eine fehlende.*
