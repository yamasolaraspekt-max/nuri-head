# W-14 · Kopieren, Spiegeln, Drehen — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt. Keine
> abgeschriebenen Formeln.**

## Die Registerzeile nannte F-032 — sie trägt nicht

| F-Nr | laut Register | gemessen |
|---|---|---|
| **F-032** Transformation eines Punktes | ja | **NEIN** — keine Matrix, keine Verkettung |

**Die gesamte Mathematik von `geometry/editierGeometrie.ts`, 75 Zeilen:**

```text
Math.round  6x    ganzzahlige mm nach jeder Operation
Math.max    2x    bbox: die groessten Koordinaten
Math.min    2x    bbox: die kleinsten
```

**Kein `Math.cos`, kein `Math.sin`, kein `Math.atan2`, keine Matrix.** *Was das Modul rechnet:*

```text
Translation       x' = x + dx                    eine Addition
Achsenspiegelung  x' = 2·pos − x                 eine Subtraktion
Bbox              min/max ueber eine Punktmenge
Achsenmitte       (minX + maxX) / 2
```

> ***Dafür kennt die Sammlung keine Nummer, und sie braucht auch keine.*** **Eine erfundene wäre
> schlimmer als eine gemeldete Lücke** — die Lehre aus W-21.

**Berichtigt am 14.08. mit W-14/1** auf `~~F-032~~ ⓝ`.

### Und der Grund, warum F-032 hier PLAUSIBEL aussah

**F-032 ist die Transformation eines Punktes — Verschieben, Drehen, Skalieren als 4×4-Matrix.**
*Zwei der drei kommen in diesem Werkzeug vor.* **Aber sie sind einzeln und direkt gerechnet, nicht
über eine Matrix** — *und die Formel ist nicht „irgendeine Verschiebung", sondern die homogene
Matrixform mit ihrer Reihenfolge-Regel.*

> *Die Registerzeile hat den **Gegenstand** getroffen und die **Bauform** verfehlt.* **Das ist die
> Sorte Zuordnung, die niemandem auffällt, bis jemand die Formel sucht und nur zwei Additionen
> findet.**

### Wo F-032 wirklich gebaut ist

`renderers/three-d/szene.ts:621`/`:627` — *`makeBasis` und `applyMatrix4`, in der 3D-Darstellung
(W-12).* **Mit W-14 hat sie nichts zu tun.**

## Was ein DREHEN brauchen würde

**Eine Drehung wäre die erste Operation dieses Werkzeugs mit Trigonometrie** — *und damit die
erste, für die F-032 in Frage käme.* Siehe `7-GRENZEN`.

## Normative Größen

**Keine.**
