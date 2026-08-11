# W-21 · Sparren und Lattung — BEDIENUNG

## Nichts Eigenes.

**Kein Registry-Werkzeug** — 0 Treffer auf `sparren`, `holz`, `lattung`, `dachstuhl` in der
Werkzeugregistrierung. **W-21 ist damit in der Lage von W-01 und W-05:** die Rechenschicht steht,
die Werkzeugschicht gibt es nicht. *Das Blatt benennt die Lage, es löst sie nicht.*

## Was der Anwender eingibt — bei der Vorbemessung

| Größe | Anmerkung |
|---|---|
| **Schneezone** 1 / 2 / 3 | `Schneezone` ist ein Typ mit **genau drei** Werten (`sparrenBerechnung.ts:16`) |
| Geländehöhe (m) | geht in die Bodenschneelast ein |
| Dachneigung | bestimmt den Formbeiwert **und** die senkrechte Lastkomponente |
| Holzklasse **C24 / C30** | zwei Werte (`:17`) |
| Sparrenabstand, Stützweite, ständige Last | die ständige Last umfasst **Dachdeckung + Lattung + Sparren-Eigengewicht** (`:63`) |

## Was er bei den Mengen NICHT eingibt

**Nichts.** Die Mengen kommen aus der Holzliste, die die 3D-Engine beim Zeichnen erzeugt hat.
*Wer nichts gezeichnet hat, bekommt keine Mengen — und das ist richtig so.*
