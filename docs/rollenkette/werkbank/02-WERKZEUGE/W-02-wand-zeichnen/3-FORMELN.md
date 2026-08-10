# W-02 · Wand zeichnen — FORMELN

## Benutzte Formeln — jede mit Fundstelle

| F-Nr | Wofür | Fundstelle | Grenzfall betrifft uns? |
|---|---|---|---|
| **F-001** Abstand zweier Punkte | Wandlänge | `wallGeometry.ts:13` | **JA** — Gleitkomma für Vergleiche, Persistenz bleibt ganzzahlig |
| **F-002** Richtungswinkel | Azimut der **Normalen** | `wallGeometry.ts:37` | **JA** — `atan2`, sonst geht der Quadrant verloren |
| **F-030** Wand aus Achse extrudieren | Band aus Achse + Stärke | `wallGeometry.ts:153` | **JA** — Länge 0 ergibt kein Band |

## Was der Code anders macht als die Sammlung

### F-002: gemessen wird die NORMALE, nicht die Achse

```text
Formelsammlung   Richtungswinkel einer Strecke
Code (Z.37-52)   Normale links = (-dy, dx), rechts = (dy, -dx)
                 Azimut = atan2(nx, ny), im Uhrzeigersinn von NORD (+y)
                 Ergebnis auf 0-359 normiert: ((grad % 360) + 360) % 360
```

**Die Formel ist dieselbe, der Gegenstand nicht.** *Wer F-002 auf die Wandachse anwendet statt auf
ihre Normale, liegt um 90° daneben — und zwar ohne Fehlermeldung.*

### F-030: der Grenzfall im Code ist enger als in der Sammlung

```text
Sammlung   "Bei d <= 0 oder h <= 0 Absage. Bei sehr spitzen Wandwinkeln (< 15°)
            ueberlappen sich die Ecken - dann verschneiden (F-004)."
Code       Laenge 0 -> `continue`, kein Band (wallGeometry.ts:159-160)
```

**Zur Winkelverschneidung nach F-004 sagt dieses Blatt nichts** — *sie wurde beim Ableiten nicht
gemessen, und ein Satz ohne Beleg gehört nicht in ein Werkzeugblatt.*
