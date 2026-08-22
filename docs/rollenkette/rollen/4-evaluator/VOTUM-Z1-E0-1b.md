# VOTUM Z1-E0-1b — Umbenennung `deckenOberkanteMm` → `wandOberkanteMm`

**ABGENOMMEN (CODE) — vier von vier Kriterien. Kein Browserlauf, wie vom Dirigenten vorgesehen.**

| Feld | Wert |
|---|---|
| Kriterien | `dirigent-BAUPLATZ-umbenennung-wandOberkanteMm.yaml` (22:32) — kein eigenes Blatt, die Kriterien stehen dort |
| Bau | `d5e88f15` · Ausgang `3b4e8f6b` |
| Mein Stand | `81156b49` |
| gelesen_bis | 2026-08-22T23:24:30+02:00 |

## Warum die Umbenennung nötig war

Der alte Name sagte das Gegenteil: `deckenOberkanteMm` liefert die Höhe, auf der die Decke
**aufliegt** — also ihre **Unterkante**. *Der Name lag um eine Deckendicke daneben.* Der Dirigent
hat es so begründet: *„Ein Name, der das Gegenteil sagt, ist genau die Falle, die E4 (unteres Ende
der Kette) und Z1-E0-2 (Wandhöhe) noch treffen würde."* Das trägt — die Größe hat drei Leser, und
wer beim vierten dem Namen glaubt, legt die Decke eine Dicke zu hoch, **und das Bild sieht dabei
richtig aus**.

## Die vier Kriterien

**a · `deckenOberkanteMm` → 0 Treffer — ERFÜLLT.**

```
'deckenOberkanteMm' im Inselcode inkl. __tests__ :  0
Gegenprobe 'wandOberkanteMm'                     : 16
szene.ts: 'const oberkante' 0  ·  'const wandOberkante' 1
```

Die zweite Hälfte des Kriteriums (die lokale Variable in `szene.ts:483`) ist damit ebenfalls
belegt.

**b · Ausschließlich Bezeichner — ERFÜLLT.** Jede geänderte Zeile einzeln geprüft und in
Kommentar/Code getrennt. **Alle Code-Zeilen sind reine Bezeichnerwechsel:**

```
export function deckenOberkanteMm(…)   ->  export function wandOberkanteMm(…)
Math.round(deckenOberkanteMm(level)…)  ->  Math.round(wandOberkanteMm(level)…)
traufhoeheMm: deckenOberkanteMm(level) ->  traufhoeheMm: wandOberkanteMm(level)
const deckenHoehe = …                  ·   const oberkante = … -> const wandOberkante = …
Importe und Testnamen entsprechend
```

**Und die Zahlen stehen still:** die Testwerte in `decke.test.ts` bleiben `2500`, `5200`, `-750`
— identisch vor und nach dem Bau. Kein Operator, keine Rechenzeile. Die übrigen Änderungen sind
**JSDoc**, und die verlangt Kriterium (d) ausdrücklich.

**c · `tsc` 0, Suite grün mit unveränderter Anzahl, Bündel Teil der Lieferung — ERFÜLLT.**

```
tsc                        Rueckgabe 0
Suite Ausgang  3b4e8f6b    1809 tests
Suite Endstand d5e88f15    1809 tests      <- UNVERAENDERT
```

*Der Vergleich musste Ausgang gegen Endstand lauten, nicht gegen meinen letzten Stand:* zwischen
`7bcd9f0c` (1785) und hier liegt **Z1-E4-1**, und dessen neue Tests hätten die Zahl scheinbar
wandern lassen. Am richtigen Paar gemessen steht sie still.

**Zum Bündel:** Es steht **nicht** im Diff dieser Lieferung — und das ist richtig, nicht fehlend.
Bewiesen statt vermutet:

```
committetes Buendel  sha256  d14ec5166b1a9c5a
frisch gebaut        sha256  d14ec5166b1a9c5a      <- BYTE-IDENTISCH
'deckenOberkanteMm' im Buendel 0  ·  'wandOberkanteMm' im Buendel 0
```

Die Minifizierung ersetzt Funktionsnamen; eine reine Umbenennung erzeugt ein **byte-gleiches**
Bündel. *Es gab nichts zu liefern.* Hätte ich nur den fehlenden Diff-Eintrag gesehen, hätte ich
einen Mangel gemeldet, den es nicht gibt.

**d · Der Kommentar nennt die Größe korrekt — ERFÜLLT.**

> **Wandoberkante des Levels (mm) — das Auflager der Decke.**

## Eine Feinheit, die Anerkennung verdient

Der Bau erklärt im Kommentar ausführlich, *warum* umbenannt wurde — **ohne den alten Bezeichner
auszuschreiben**, mit dieser Begründung:

> *Der alte Bezeichner steht hier bewusst nicht ausgeschrieben:* Kriterium `Z1-E0-1b-a` zählt seine
> Zeichenkette auf 0, und eine Erinnerung an ihn wäre von seinem Fortleben nicht zu unterscheiden.

Das ist genau richtig gedacht: Der Bau bewahrt die **Messbarkeit** seines eigenen Kriteriums.
Ein erklärender Kommentar, der die gezählte Zeichenkette enthielte, hätte (a) unprüfbar gemacht —
die 0 wäre eine 1 geworden, und niemand hätte am Zählstand erkennen können, ob der Name noch lebt.

**Außerhalb der Insel berührt: 0 Dateien.**

**Ball:** Integrator (Transport).
