# W-20 · Stückliste und Mengen — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er will wissen, wie viel Holz wirklich verbaut wird** — nicht wie viel in einem Rechteck Platz
hätte.

## Der Grund, wörtlich aus dem Dateikopf — und er ist der Kern dieses Blattes

> *„Problem vorher: Die Material-/Holzliste **schätzte** Sparren-/Lattenlängen aus dem
> Rechteck-Rahmen (Anzahl × Höhe bzw. Anzahl × Breite). Die Engine zeichnet die Stäbe aber bereits
> an die reale (an Walm/L/T geclippte) Geometrie → **zwei Wahrheiten**. Diese Funktion summiert die
> echten Stab-Längen je Bauteilart, damit 3D-Darstellung und Holzliste dieselbe Mengenbasis nutzen."*
> — `geometry/holzMengen.ts:5-9`

**Dieses Werkzeug ist als Beseitigung einer zweiten Wahrheit entstanden.** *Vorher rechnete die
Stückliste aus dem Rahmen, während die Darstellung die geclippte Geometrie zeigte — **zwei Zahlen
für dasselbe Holz, und die falsche stand in der Liste.***

> **Das erklärt, warum die Aggregation über die ECHTE Liste läuft und nicht über Formeln:** *jede
> Formel, die aus Umriss und Anzahl rechnet, erzeugt die zweite Wahrheit neu.* **Wer hier eine
> Schätzung einbaut, baut den behobenen Fehler wieder ein.**

## Wann greift der Anwender danach?

*Wenn das Dach steht und die Frage von „wie sieht es aus" zu „was kostet es" wechselt* — Bauholz in
Kubikmetern, Lohn nach laufenden Metern, Latten für den Einkauf.

## Woran merkt er, dass es fehlt?

**Er nimmt die Zahl aus der Zeichnung und die Menge aus der Liste — und sie stimmen nicht überein.**
*Bei einem Walmdach oder einem L-Grundriss ist der Unterschied nicht klein: dort sind die Sparren
geclippt, und genau diese Differenz war der Anlass für die Reparatur.*

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs?

| Nicht dieses Werkzeug | Sondern | Warum die Trennung |
|---|---|---|
| **Wie weit** die Latten auseinanderliegen | **W-21L / F-053** — Lattmaß in mm | zwei verschiedene Fragen an dieselbe Latte |
| Die Ziegel**menge** | offen — Zieladresse in `7-GRENZEN.md` | die Fläche kommt aus F-011, der Faktor aus W-23 |
| Materialzuweisung (Farbe, Textur) | **W-15** | Zuweisung, keine Menge |
| Die Dachfläche selbst | **W-08** (F-011) | W-20 summiert Stäbe, keine Flächen |

> ### Die Unterscheidung, die ich selbst falsch gemessen habe — und deshalb steht sie hier
>
> **`Lattmaß` ist nicht `Lattenlänge`.**
>
> ```text
> W-21L / F-053   WIE WEIT liegen die Latten auseinander?   ->  Lattmass in mm
> W-20            WIE VIELE laufende Meter Latte?           ->  Summe der echten Laengen
> ```
>
> *Beim Schneiden dieses Blattes wurde nach `lattenMengen` gesucht — **0 Treffer**, also „die Lattung
> fehlt".* **Falsch: das Feld heißt `lattenLaenge` (`holzMengen.ts:35`) und ist gefüllt.** *Das
> Muster suchte eine **Schreibweise**, nicht die Sache — **H-9**.* **Wer die zwei Fragen vermischt,
> sucht die Lattweite in der Mengendatei und findet sie nie.**
