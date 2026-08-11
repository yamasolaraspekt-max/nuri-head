# W-21 · Sparren und Lattung — FORMELN

**Nur Nummern.** Die Formeln stehen in `../../01-MATHEMATIK/FORMELSAMMLUNG.md`.

## Das Register nennt F-001 und F-030 — gemessen steht keine von beiden im Code

| F-Nr | laut Register | gemessen |
|---|---|---|
| **F-001** Abstand zweier Punkte | ja | **NEIN** — kein `Math.hypot`, kein `Math.sqrt` in **keinem** der fünf Module |
| **F-030** Wand aus Achse extrudieren | ja | **NEIN** — keine Extrusion; die Stäbe kommen fertig aus der 3D-Engine |

**Das ist stimmig, sobald man die Natur der Module kennt:** drei von fünf **aggregieren** aus einer
bereits erzeugten Liste — *sie brauchen keine Geometrieformel, weil sie keine Geometrie erzeugen.*

## Die gemeldete Lücke: `bodenschneelast()` und `formbeiwertSchnee()` haben keine F-Nummer

Beide rechnen — `sparrenBerechnung.ts:33` und `:45` — und **die Sammlung kennt sie nicht.**

**Das ist kein Mangel der Sammlung.** Es sind **normative Größen**, keine Geometrieformeln, und ihre
Quelle steht im Dateikopf:

```text
Schneelast       DIN EN 1991-1-3 + Nationaler Anhang
                 (charakt. Bodenschneelast sk je Zone/Hoehe, Formbeiwert my1 nach Dachneigung)
Holzbemessung    DIN EN 1995-1-1 (Eurocode 5): Biegenachweis und Durchbiegung
```

> **Eine erfundene F-Nummer wäre schlimmer als eine gemeldete Lücke.** *Die Sammlung ist ein
> Geometrie-Verzeichnis; eine Norm gehört nicht hinein, nur weil sie eine Zahl liefert.*

## Was sonst gerechnet wird

`sparrenBerechnung.ts:90` benutzt `Math.cos` für die **senkrechte Lastkomponente** — der Dateikopf
nennt genau das als Annahme der Vorbemessung. *Kein Winkel wird gemessen; der Winkel ist Eingabe.*
