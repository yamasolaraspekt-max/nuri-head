# Baubericht W-20 — Stückliste und Mengen. Eine Ablesung, und drei Zahlen für dieselbe Sache

```yaml
auftrag: "W-20"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-20-stueckliste-und-mengen.md
basis_sha: a146e0b3
gebaut_am: "12.08.2026"
ziel: "BESCHRIEBEN — Ablesung, nicht Vorgabe"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

## W-20-1 · Abgelesen mit Zeilenangabe — jede einzeln geprüft

```text
geometry/holzMengen.ts    64 Zeilen · 3 Exporte
  :23  export interface HolzStueck        :29  export interface HolzMengen
  :44  export function holzMengenAusListe
  :41  gueltigeLaenge — Rueckgabe 0 statt NaN/Infinity/negativ
  :52  type === 'latte'      :56  startsWith Sparren/Schiftsparren      :58  die Begruendung
```

**Alle sieben Angaben aus dem Kriterium sind gegen die Datei geprüft, nicht übernommen.**

## W-20-2 · Der tragende Punkt — zwei Wahrheiten, wörtlich

`1-ZWECK.md` trägt den Grund im Wortlaut des Dateikopfs (`:5-9`):

> *„Die Material-/Holzliste **schätzte** Sparren-/Lattenlängen aus dem Rechteck-Rahmen … Die Engine
> zeichnet die Stäbe aber bereits an die reale (an Walm/L/T geclippte) Geometrie → **zwei
> Wahrheiten**."*

**Ohne diesen Satz liest die nächste Rolle eine gewöhnliche Aggregation und weiß nicht, warum sie
nicht rechnen darf.** *Deshalb steht in `3-FORMELN.md` ausdrücklich: **hier steht absichtlich keine
Formel** — die Vorgängerlösung hatte eine, und sie war der Fehler.*

## W-20-3 · Die Schiftsparren-Begründung

`2-FUNKTION.md` zitiert `holzMengen.ts:57-58`:

> **„Schiftsparren sind Gemeinsparren … sie MÜSSEN hier mitzählen, sonst fallen die an Kehle/Grat
> geclippten Sparren aus Bauholz-m³ und Lohn heraus (Unter-Count)."**

**Ein Unter-Count in der Stückliste ist ein Fehlbetrag im Angebot** — *das ist die Begründung, nicht
die Bequemlichkeit.* Dazu die Feinheit, die im Code steckt und die ich mitaufgenommen habe: *die
Reihenfolge der Zweige. `type === 'latte'` steht **vor** der Namensprüfung — **die Art schlägt den
Namen**.*

## W-20-4 · Die Ziegelmenge als Grenze, mit den Messzahlen

```text
'stueck.*m2'   0 Treffer      'bedarf'   1 Treffer (eine Gaubenbemerkung)
```

**Und die Gegenprobe, die das Kriterium ausdrücklich verlangt:** *`ziegel` **16** Treffer — als
**TYP** (`RoofCovering`); `deckung` **79** — der erste eine **LASTannahme** in `sparrenBerechnung`,
also Gewicht.* **Wer diese 95 Treffer für eine Mengenrechnung hält, sucht falsch.**

*Die Zieladresse steht im Blatt, damit die Lücke nicht verwaist:* **Ziegelmenge = Dachfläche (F-011)
× `Bedarf_Stk_m2` (W-23, Spalte 28/29)** — *beide Faktoren liegen vor; es fehlt nur die
Multiplikation, und sie gehört in einen eigenen Auftrag.*

## W-20-5 · Lattmaß ist nicht Lattenlänge

```text
W-21L / F-053   WIE WEIT liegen die Latten auseinander?   ->  Lattmass in mm
W-20            WIE VIELE laufende Meter Latte?           ->  Summe der echten Laengen
```

*Die Unterscheidung steht in `1-ZWECK.md` samt ihrem Anlass: **beim Schneiden wurde nach
`lattenMengen` gesucht — 0 Treffer, also „die Lattung fehlt". Falsch — das Feld heißt
`lattenLaenge` (`:35`) und ist gefüllt.*** **Das Muster suchte eine Schreibweise, nicht die Sache.**

## Drei Zahlen für dieselbe Sache — und alle drei sind richtig

**Das Blatt sagt „sechs Testzusagen", der DoR-Beleg sagt „25 assert-Aufrufe". Gemessen:**

```text
grep -cE '^test\('    ->   6      test-Bloecke
grep -cE 'assert\.'   ->  24      echte Assertions
grep -c  'assert'     ->  25      Zeilen mit dem Wort — die 25. ist  import assert from 'node:assert/strict'
```

> **Alle drei Muster messen genau, was sie messen — und drei verschiedene Dinge.** *Die Zahl der
> Zusagen ist **24**.* **Die 25 entsteht, weil das Muster die Import-Zeile mitnimmt: `assert` ohne
> Punkt ist der Name, nicht der Aufruf.** *Ich melde alle drei, statt eine auszuwählen.*

## W-20-6 · `must_preserve`

| | Ergebnis |
|---|---|
| `resources/**` · `app/**` | **0 Dateien** — geändert, hinzugefügt, entfernt je 0 |
| `holzMengen.ts` **und** sein Test | **unberührt, 0 Dateien** |
| Register | **genau eine Werkzeugzeile** (`LEER` → `BESCHRIEBEN`) |
| Abschlusszähler `BESCHRIEBEN` | **12 → 13** — *hier soll er steigen: Ziel ist `BESCHRIEBEN`* |

## Platzhalter — zwei Treffer, keiner ist einer

```text
grep -nE '<[^>]+>' über die sieben Blätter  ->  2   (Rot vorher: 21)
  2-FUNKTION.md:10   ReadonlyArray<HolzStueck>    TypeScript-Generic
  5-CODE:32          ReadonlyArray<HolzStueck>    dasselbe, in der Signatur
```

*Dieselbe Klasse wie bei W-23 — **die Platzhalterzählung trifft spitze Klammern, nicht
Platzhalter**.* **Deshalb steht die Zahl mit ihren Zeilen.**

## Ein Befund, den ich benenne statt behebe

**`gueltigeLaenge` (`:40-42`) macht aus jeder kaputten Länge eine `0` — und niemand erfährt es.**

*Das Verhalten ist richtig: eine Stückliste, die wegen eines Stabes gar nichts liefert, ist
unbrauchbarer als eine, die ihn auslässt.* **Aber es gibt keine Meldung, keine Zählung, keinen
Hinweis — der Stab verschwindet aus der Summe, und die Liste sieht vollständig aus.**

> *In einer Stückliste, aus der ein Angebot wird, ist eine stille Null die unangenehmste Zahl von
> allen.* **Es ist nicht der Dach-Vorfall — hier wird nichts geworfen und nichts geschluckt. Es ist
> die leisere Verwandte: ein Ergebnis, das seine Auslassung nicht mitteilt.** *In `7-GRENZEN.md` als
> offener Punkt der Fänger-Prüfung geführt, nicht in diesem Auftrag behoben.*

## W-20-7 · §3, zweimal gemessen

*Das Kriterium verlangt die Messung **unmittelbar vor der ersten Änderung** und weist ausdrücklich
darauf hin, dass `REGISTER.md` im Scope mehrerer W-Blätter liegt — also **vor der Registerzeile
erneut**:*

```text
vor dem Bau            Tafelzeile 0 · Zustandsfeld 0
vor der Registerzeile  Tafelzeile 1 · Zustandsfeld 1   (beide W-20, meiner)
REGISTER.md dabei      unverändert im Arbeitsbaum — kein fremder Bau darin
```

## Berührte Dateien

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-20-stueckliste-und-mengen/1-ZWECK.md … 7-GRENZEN.md
docs/rollenkette/werkbank/02-WERKZEUGE/W-20-stueckliste-und-mengen/5-CODE/LIESMICH.md
docs/rollenkette/werkbank/02-WERKZEUGE/REGISTER.md      Werkzeugzeile + Fundstelle
docs/BERICHT-W-20-stueckliste-und-mengen.md             dieser Bericht
docs/STATUS.md                                          Zustand an beiden Orten
```
