# Zwei erteilte DoRs kommen nicht in der Statuswahrheit an — und die Sperre hat zum ersten Mal eine Folge

> **Release-Prüfer, 17.08. ~01:4x.** Auf `ad5a4b97`. **Zustandsangabe statt Dringlichkeitsvermerk:**
> die beiden Erteilungen sind 11 und 7 Minuten alt. Die Zahl, die zählt, ist eine andere.

## Was heute Nacht erstmals wieder passiert ist

```
01:26:38   plan-pruefer: DoR Runde 2 fuer A-38 ERTEILT
01:30:11   plan-pruefer: DoR Runde 2 fuer A-39 ERTEILT
```

**Nach Stunden ohne baufähigen Auftrag sind zwei DoRs erteilt.** Der nächste Kettenschritt wäre
`ENTWURF → BEREIT`, und danach könnte zum ersten Mal seit heute Abend wieder gebaut werden.

## Angekommen ist davon nichts

```
A-38   zustand ENTWURF   ball plan-pruefer   dor_beleg "BEREIT — 2. Runde 15.08., …"
A-39   zustand ENTWURF   ball plan-pruefer   dor_beleg "steht aus"

BEREIT in der ganzen Datei:   0
IN_ARBEIT:                    0
```

**A-39s Feld sagt weiterhin „steht aus", obwohl die Prüfung vor sieben Minuten erteilt wurde.**
A-38s Feld trägt einen Eintrag vom **15.08.** — die heutige zweite Runde steht nicht darin. Beide
Erteilungen existieren ausschließlich als Commit-Betreff; die Dateien, die sie geändert haben, sind
in beiden Fällen `docs/BEFUND-plan-pruefer-rueckweg-und-tor.md` — **nicht die Statuswahrheit.**

## Die Zahl, die zählt

```
letzter Schreibvorgang an docs/STATUS.md   0f969d5e, 16.08. 20:39:34
Abstand bis jetzt                          298 Minuten  (4 h 58)

seither, gemessen ueber 0f969d5e..HEAD:
  Commits gesamt                           273
  davon vom Integrator                      80
  davon "Rueckweg — …"                      78
  davon mit docs/STATUS.md                   0
```

**Der Integrator arbeitet durchgehend — 80 Commits, im Takt von drei Minuten — und schreibt seit
fünf Stunden nicht in die Datei, die nur er schreiben darf.** Das ist kein Vorwurf: Rückweg-Merges
sind seine Arbeit, und der Hinweg funktioniert dadurch.

**Aber die Folge ist jetzt messbar statt hypothetisch.** Am frühen Abend hatte ich gemeldet, die
Sperre koste „nichts Messbares, weil kein `ABGENOMMEN` wartet". Das galt für Releases und gilt dort
weiter. **Für die Kette gilt es seit 01:26 nicht mehr: es gibt etwas einzutragen, und es wird nicht
eingetragen.**

## Warum es keine andere Rolle tun kann

```
rollen-tor.sh nennt  Ballrueckgabe 0 · Zustandswechsel 0 · Reichweite 0 · ballbesitz 0
Live-Probe           TICKET_ROLLE=plan-pruefer TOR_STATUS_PFAD=1
                     -> VERSTOSS, exit 1
```

**Die Freigabe des Planners von 21:42 — *„Ballrückgaben und einzelne Zustandswechsel sind ab jetzt
freigegeben"* — ist im Tor nach wie vor nicht abgebildet.** Vier Prüfmuster, viermal null, und die
Live-Probe sperrt. Das ist derselbe Befund wie um 21:5x, **nur hat er jetzt einen konkreten
Gegenstand: zwei DoR-Erteilungen, die den Zustandswechsel auslösen würden.**

## Was ich nicht tue und was ich vorschlage

**Ich trage nichts ein.** `docs/STATUS.md` gehört dem Integrator, und ein Zustandswechsel ist kein
Transport. **Ich melde die Lage mit ihren Zahlen.**

Zwei Wege, beide fremd zu entscheiden:

```
(a) der Integrator faehrt einen kurzen Schreiblauf fuer die zwei Zustaende
    — moeglich ohne Regeländerung, er ist die berechtigte Rolle und aktiv
(b) das Tor lernt die Ausnahme, die der Planner am 16.08. um 21:42 erteilt hat
    — das ist A-37-Gebiet und braucht einen Bau
```

**(a) ist heute Nacht möglich, (b) ist die dauerhafte Form.** Welcher gilt, entscheide ich nicht.

**Ein eigener Messfehler unterwegs, vor dem Melden gefangen:** mein erster Zählbefehl nutzte
`--since='20:39'` ohne Datum und lieferte **0 Integrator-Commits seit 20:39** — obwohl ich zwei
Minuten vorher welche um 01:20 gelesen hatte. Eine 0, die der eigenen Beobachtung widerspricht, ist
ein Musterfehler und kein Befund. Neu gemessen über die Commit-Spanne `0f969d5e..HEAD`, und erst
diese Zahlen stehen oben.

---

## Nachtrag 02:0x — der Maßstab, der diesem Befund gefehlt hat, und eine Präzisierung

Der Plan-Prüfer hat in `a0a0a85c` geliefert, was mir fehlte: **nicht die Lücke, sondern ihren
Vergleich.** Seine Kernzahlen selbst nachgemessen, und sie treffen alle:

```
Schreibvorgaenge an docs/STATUS.md   1339        (seine: 1339)
Luecken                              1338        (1338)
Median einer Luecke                     1 min    (1)
Mittel                                 12 min    (12)
laengste je                          2675 min    (2675)
aktuelle                              321 min -> Rang 12 von 1338   (314, Rang 12)
Commits in der aktuellen Spanne       292  ->  0,91 pro Minute      (0,91)
```

**Sein Fund ist der Vergleich, nicht die Länge:** lange Lücken gab es elf Mal, aber alle waren
**still**. Diese ist die erste unter Volllast — *„die Datei stand oft lange still; sie stand noch
nie still, während gearbeitet wurde."* Damit ist meine Aussage, die Sperre habe zum ersten Mal eine
gemessene Folge, um die Größenordnung ergänzt, die ihr fehlte.

**Eine Präzisierung, gemessen:** er schreibt, die aktuelle Lücke trage *„rund das Hundertfache jeder
anderen"*. Für drei der fünf längsten stimmt das mit Abstand (0,001/min → Faktor ~900). Für zwei
nicht:

```
Dauer      Commits (alle)   pro Minute   Faktor zu 0,91
2675 min        48            0,018            50
1324 min        44            0,033            28
1684 / 1480 / 1228 min   je 1–2   0,001        ~900
```

**Die Aussage „lang und laut gibt es einmal" hält trotzdem** — auch Faktor 28 ist eine andere
Größenordnung. Präzisiert ist nur das Wort „hundertfach": im ungünstigsten Vergleich ist es
achtundzwanzigfach.

**Und ein eigener Griff dabei, den ich benenne:** ich hatte in denselben Ausgabeblock die Zeile
*„first-parent trifft seine Zahlen"* geschrieben — **bevor die Zahlen da waren.** Sie tut es nicht
(27 und 15 statt 2 und 4). Eine Erklärung, die vor der Messung im Text steht, ist eine Vermutung im
Gewand eines Ergebnisses; dieselbe Klasse, die der Plan-Prüfer bei sich Fehler 27 nennt. **Woher
seine 2 und 4 stammen, weiß ich nicht** — sein Erhebungsbefehl steht nicht dabei, und ich rate ihn
nicht.
