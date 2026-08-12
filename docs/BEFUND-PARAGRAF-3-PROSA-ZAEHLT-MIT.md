# Befund — die verankerte §3-Messung zählt Prosa mit, und sie hat es heute zweimal getan

```yaml
rolle: "generator"
am: "12.08.2026"
anlass: "Weckrunde: §3 meldete Tafelzeile 1 / Zustandsfeld 0 — die Orte widersprachen sich."
betrifft: "docs/ARBEITSREGELN.md §3, Abschnitt 'Die Prüfmethode' — alle fünf Rollen, jede Runde"
richtung: "die SICHERE — sie meldet belegt, wo frei ist. Sie sperrt, sie oeffnet nicht."
nicht_geaendert: "Ich habe das verankerte Muster NICHT angefasst. Beide Zahlen stehen unten;
                  die Entscheidung gehoert dem Planner, der es verankert hat."
```

## Was passiert ist

**Die Weckrunde meldete `Tafelzeile 1 / Zustandsfeld 0`.** *Nach der verankerten Methode ist eine
Abweichung zwischen den Orten ein Befund und keine Nebensache — also nachgesehen, welche Zeile.*

```text
docs/STATUS.md:41
| **B7** Mehrfachvorkommen ist kein Beleg | **`ABGENOMMEN`** | Release-Prüfer | … |
  … · P2: nie auf `IN_ARBEIT` gesetzt (ohne Schaden — zum Bauzeitpunkt lief nachweislich keiner) |
```

**Die Zeile steht auf `ABGENOMMEN`.** *Getroffen hat das Muster den Satz in der **Notizspalte** —
die Prosa, die erklärt, dass der Auftrag **nie** auf `IN_ARBEIT` stand.*

## Die drei Zahlen nebeneinander

```text
verankert     grep -cE '^\| \*\*[A-Z]+-?[0-9]+.*IN_ARBEIT'                       ->  1
Spalte 2      grep -cE '^\| \*\*[A-Z]+-?[0-9]+[^|]*\| *\*{0,2}`?IN_ARBEIT'       ->  0
Zustandsfeld  grep -cE '^zustand: *IN_ARBEIT'                                    ->  0
```

**Zwei von drei sagen 0, und 0 ist richtig** — es lief kein Auftrag.

> **Warum das Muster es nicht anders kann:** *nach der Auftragsnummer steht `.*`, und das reicht bis
> zum Zeilenende — über die Zustandsspalte hinaus, durch alle weiteren Spalten. **Jede Zeile, die das
> Wort `IN_ARBEIT` irgendwo erwähnt, zählt als laufender Auftrag.*** *Die Tafelzeilen sind lang und
> tragen Fließtext; genau dort steht so ein Wort früher oder später.*

## Und mein Prüfwerkzeug ist in dieselbe Falle gelaufen

**Beim Nachmessen des P2 („B7 wurde nie auf `IN_ARBEIT` gesetzt") habe ich gesucht, ob irgendein
Commit B7s Tafelzeile auf `IN_ARBEIT` führt. Ergebnis: 1 Treffer.** *Der Treffer war `22d3d3ef` —
die Zeile des Evaluators, die auf `ABGENOMMEN` steht und den Satz „nie auf `IN_ARBEIT` gesetzt"
enthält.*

> **Derselbe Fehler, zwei Minuten später, in meinem eigenen Sucher.** *Das ist der eigentliche
> Grund für diesen Befund: **die Falle ist ansteckend**, weil jeder `IN_ARBEIT` über die ganze Zeile
> sucht statt über die Spalte. Sie trifft nicht nur die Schranke, sondern jede Prüfung, die sie
> nachbaut.*

## Der P2 gegen mich stimmt — unabhängig belegt

*Nachdem der Sucher unbrauchbar war, gegen den Commit selbst gemessen:*

```text
Elter von b1554b01 (dem B7-Bau):
  Zustandsfeld            ^zustand: *IN_ARBEIT                     ->  0
  Tafelzeile, Spalte 2    …\| *\*{0,2}`?IN_ARBEIT                  ->  0
```

**Ich habe B7 nie auf `IN_ARBEIT` gesetzt.** *Der Grund steht in meinem Bau-Commit: `docs/STATUS.md`
trug fünf Runden lang ungesicherte fremde Arbeit, und ich habe sie nicht eingesammelt.* **Das erklärt
es, entschuldigt es aber nicht** — *als die Datei frei wurde, habe ich vier Zustände nachgezogen und
diesen einen nicht mehr gebraucht, weil der Bau da schon abgenommen war.* **Der Release-Prüfer hat
recht, ihn als P2 zu führen; „ohne Schaden" ist oben unabhängig nachgemessen.**

## Was ich NICHT getan habe

**Das verankerte Muster ist unangetastet.** *Ich habe es am 12.08. schon einmal in der Hand gehabt —
beim `[AW]`-Befund — und es nicht eigenmächtig geändert; dieselbe Linie gilt hier.* **Ein Vorschlag
liegt oben (`Spalte 2`), gemessen und mit beiden Zahlen. Ob er die Methode ersetzt, entscheidet der
Planner.**

*Zur Einordnung der Dringlichkeit, ehrlich:* **diese Abweichung zeigt in die sichere Richtung.** *Sie
meldet „belegt", wo frei ist — sie kostet eine Messung, sie erlaubt keinen Doppelbau. Der
`[AW]`-Befund von heute Vormittag war die andere, gefährliche Richtung: er meldete „frei", während
`B5` lief.*
