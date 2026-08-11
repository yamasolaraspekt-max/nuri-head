# W-05 · Raum erkennen — PRÜFUNG

## Was eine Prüfung hier belegen muss

1. **Dass es nicht hängenbleibt.** Die Endlosschleifen-Freiheit ist eine ausdrückliche Zusicherung
   des Dateikopfs — und ein automatisch laufendes Werkzeug hat keinen Ausweg.
2. **Dass der Außenumlauf verworfen wird.** Sonst ist das ganze Haus ein Raum.
3. **Dass die Referenzwerte auf Achsmaß gerechnet sind** — nicht lichte Maße (siehe `7-GRENZEN`).
4. **Dass ein offener Wandzug keinen Raum erzeugt** — und dass das Ergebnis leer ist, nicht falsch.

## Warum Punkt 1 nicht durch Nachdenken zu prüfen ist

Die Zusicherung stützt sich auf einen Mechanismus, den man messen kann:
**jede Halbkante wird genau einmal verbraucht** (`roomDetection.ts:153-154` —
`if (start.flaecheId !== -1) continue`). **Eine Prüfung, die nur „läuft durch" feststellt, prüft die
Eingabe, nicht die Zusicherung.**

## Der Prüfpunkt, den ich NICHT beantwortet habe

**Kann der Halbkanten-Umlauf ein selbstschneidendes Polygon erzeugen?** Wenn ja, liefert die
Schuhbandformel laut Sammlung *„eine falsche, aber plausible Zahl — keine Fehlermeldung"*, und es
gibt hier keine F-013-Prüfung. **Ich habe das nicht gemessen.** *Es steht als Frage im Blatt, nicht
als Zusage.*
