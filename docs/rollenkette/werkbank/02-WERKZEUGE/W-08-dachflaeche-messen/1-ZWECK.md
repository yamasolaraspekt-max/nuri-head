# W-08 · Dachfläche messen — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er soll die richtige Dachfläche bekommen und nicht eine zu große.** Der Code sagt, warum es das
Werkzeug gibt:

> *„Problem vorher: Die Material-/Holzliste berechnete die Dachfläche teils als Rechteck-Rahmen
> (width × height). Für rechteckige Satteldachflächen ist das unauffällig, für **Walm-, L-, T- und
> sonstige polygonale Flächen aber deutlich zu hoch** → überhöhte Flächen-/Materialmengen."*
> (`resources/planner/hausplaner/geometry/polygonFlaeche.ts:5-8`)

## Warum „unauffällig" das eigentliche Wort ist

Beim Satteldach stimmte die alte Rechnung. **Der Fehler zeigte sich nur dort, wo niemand hinsah** —
und er ging immer in dieselbe Richtung: zu viel Material. *Ein Fehler, der bei der häufigsten Form
richtig rechnet, wird spät gefunden.*

## Was es liefert

Die **echte geneigte** Dachfläche in m² — nicht die Grundfläche und nicht den Rahmen. 48 Zeilen,
zwei Ausfuhren, keine Abhängigkeit.
