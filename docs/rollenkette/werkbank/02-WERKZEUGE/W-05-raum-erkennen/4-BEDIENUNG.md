# W-05 · Raum erkennen — BEDIENUNG

## Nichts Eigenes.

**Es gibt kein Werkzeug für „Raum erkennen".** Kein Eintrag in der Registry, keine Schaltfläche,
keinen Modus. *Gemessen: 0 Treffer auf `raum`/`room` in der Werkzeugregistrierung.*

**W-05 ist damit in derselben Lage wie W-01:** die Rechenschicht steht, die Werkzeugschicht gibt es
nicht — **und das Blatt benennt die Lage, statt sie zu lösen.**

## Woran der Anwender es trotzdem merkt

| Er tut | Er sieht |
|---|---|
| eine Wand so ziehen, dass ein Umlauf sich schließt | ein Raum entsteht — mit Fläche und Volumen |
| eine Wand verschieben | die Zahlen ändern sich mit, ohne Zutun |
| einen Wandzug offen lassen | **nichts** — kein Raum, keine Meldung |

**Der letzte Fall ist der wichtige.** *Ein fehlender Raum sieht genauso aus wie ein Raum, den es
nicht geben soll.* Siehe `7-GRENZEN`.

## Wann es läuft

Bei jeder Ableitung aus den Wänden — `app/ableitungen.ts:62` — und beim Aufbau der 3D-Szene
(`renderers/three-d/szene.ts:357`). **Der Anwender löst es nicht aus; er löst es mit aus.**
