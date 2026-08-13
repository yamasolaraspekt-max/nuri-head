# W-21 · Sparren und Lattung — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

Drei, und sie gehören verschiedenen Leuten:

1. **„Welchen Querschnitt braucht der Sparren?"** — eine **Vorbemessung** nach Eurocode, für das
   Gespräch beim Kunden.
2. **„Wie viel Holz ist das?"** — Längen und Stückzahlen, **aus der wirklich gezeichneten Geometrie**
   und nicht geschätzt.
3. **„Was passiert mit dem Sparren, wenn ein Loch ins Dach kommt?"** — welche Sparren eine Öffnung
   schneidet, ob ein **Wechselholz** nötig ist und wie lang es wird (`auswechslung.ts`, seit
   W-21/2 · 13.08.).

## Warum `auswechslung.ts` hier zuhause ist und nicht bei der Gaube

**Ein Wechselholz ist Tragwerk, und seine Verbraucher sind mehrere:** die **Gaube** (W-22) und die
**Dachdurchdringungen** (W-29, heute leer) — Kamin, Dachfenster, Lüfter. **Ein Modul, das mehrere
Werkzeuge brauchen, gehört zum Fundament und nicht zu einem seiner Verbraucher.** *Dieselbe Logik,
mit der der Fang unter den Werkzeugen liegt und keines von ihnen ist.*

Der Code sagt dasselbe von der anderen Seite: `sparrenTrennung.ts` — **längst hier zuhause** — nennt
sich im Kopf *„Trennung eines Sparrens an einer Öffnung (Dachfenster/Kamin); ergänzt
`auswechslung.ts` (Sicher-Entscheidung)"*. **Die zwei arbeiten an derselben Sache; eines davon stand
schon in diesem Blatt.**

> **Ohne diesen Absatz liest die nächste Rolle eine willkürliche Einordnung und schiebt sie beim
> nächsten Anlass zurück.** *Deshalb steht der Grund hier und nicht nur im Auftrag.*

**Und was hier NICHT behauptet wird — gemessen, nicht angenommen:** *die zwei Verbraucher sind der
**Grund** der Zuordnung, nicht ihr Beleg.* **Heute liest kein einziger Produktivcode
`auswechslung.ts`** — der einzige Importeur ist `__tests__/auswechslung.test.ts`. *Das Modul ist
zuhause, aber es ist noch nicht angeschlossen; was daraus für die Grenzen folgt, steht in
`7-GRENZEN`.*

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
