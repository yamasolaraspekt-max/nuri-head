# W-21 · Sparren und Lattung — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

Zwei, und sie gehören verschiedenen Leuten:

1. **„Welchen Querschnitt braucht der Sparren?"** — eine **Vorbemessung** nach Eurocode, für das
   Gespräch beim Kunden.
2. **„Wie viel Holz ist das?"** — Längen und Stückzahlen, **aus der wirklich gezeichneten Geometrie**
   und nicht geschätzt.

## Warum das zweite überhaupt ein Problem war

Der Code sagt es selbst:

> *„Die Material-/Holzliste **schätzte** Sparren-/Lattenlängen aus dem Rechteck-Rahmen. Die Engine
> zeichnet die Stäbe aber bereits an die reale (an Walm/L/T geclippte) Geometrie → **zwei
> Wahrheiten**."* (`resources/planner/hausplaner/geometry/holzMengen.ts:5-8`)

**Die Aufgabe war nicht, besser zu schätzen, sondern nicht mehr zu schätzen.**

## Die wichtigste Zeile dieses Werkzeugs

**`berechneSparren()` ist eine VORBEMESSUNG und ersetzt keine prüffähige Statik.** Sie steht hier im
Zweck, weil sie in `7-GRENZEN` noch einmal steht — *wer eine Vorbemessung für eine Bemessung nimmt,
baut ein Dach danach.*
