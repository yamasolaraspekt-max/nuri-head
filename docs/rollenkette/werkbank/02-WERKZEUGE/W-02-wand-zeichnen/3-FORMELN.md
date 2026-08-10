# W-02 · Wand zeichnen — FORMELN

**Dieses Blatt nennt nur Nummern.** Die Formeln selbst stehen in
`../../01-MATHEMATIK/FORMELSAMMLUNG.md` und nirgends sonst — *eine zweite Kopie driftet gegen die
erste, und dann hat man zwei Wahrheiten statt einer.*

## Benutzte Formeln — jede mit Fundstelle im Code

| F-Nr | Wofür | Fundstelle |
|---|---|---|
| **F-001** Abstand zweier Punkte | Wandlänge | `wallGeometry.ts:13` |
| **F-002** Richtungswinkel | Azimut der Normalen | `wallGeometry.ts:37` |
| **F-030** Wand aus Achse extrudieren | Band aus Achse und Stärke | `wallGeometry.ts:153` |

## Zwei Abweichungen von der Sammlung — benannt, nicht ausgeschrieben

### F-002 wird auf die NORMALE angewandt, nicht auf die Wandachse

Die Sammlung gibt den Richtungswinkel einer Strecke. Der Code (`wallGeometry.ts:37-52`) bildet
zuerst die **Normale** der Wand — links und rechts getrennt — und misst erst deren Winkel, im
Uhrzeigersinn von Nord mit **Nord = +y**, ganzzahlig auf 0–359 normiert.

**Die Formel ist dieselbe, der Gegenstand nicht.** *Wer F-002 auf die Wandachse anwendet statt auf
ihre Normale, liegt um 90° daneben — ohne Fehlermeldung.* Festgehalten als Spec ▲K2 im Dateikopf.

### F-030 hat im Code einen engeren Grenzfall als in der Sammlung

Die Sammlung nennt zwei Absagen (Stärke und Höhe) und verlangt bei sehr spitzen Wandwinkeln ein
Verschneiden nach **F-004**. Der Code sagt an einer Stelle ab: **Länge 0 ergibt kein Band**
(`wallGeometry.ts:159-160` — Zeile 159 die Wache, Zeile 160 der Ausstieg).

**Zur Winkelverschneidung nach F-004 sagt dieses Blatt nichts** — *sie wurde beim Ableiten nicht
gemessen, und ein Satz ohne Beleg gehört nicht in ein Werkzeugblatt.*
