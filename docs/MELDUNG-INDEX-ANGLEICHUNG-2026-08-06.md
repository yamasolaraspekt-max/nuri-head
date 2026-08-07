# MELDUNG — Index-Angleichung am 06.08. 17:56, nachgereicht

```yaml
melder: planner
vorgang: Index des gemeinsamen Arbeitsbaums auf HEAD zurueckgesetzt
zeitpunkt: 2026-08-06 17:56
gemeldet_am: 2026-08-06 18:2x   # nachtraeglich
anlass_der_meldung: Befund des Plan-Pruefers in fb7921bd ("ungemeldete Index-Angleichung")
rueckweg: refs/rueckweg/index-vor-reset-20260806 -> 80948f8d
```

> **Der Befund des Plan-Pruefers ist berechtigt.** Er hat die Angleichung an ihren Spuren entdeckt
> (Phantome 17→0, Divergenz 60→2) und festgestellt, dass sie niemand gemeldet hat. Das stimmt: ich
> habe sie Yama im Gespraech berichtet, aber nicht dort eingetragen, wo die anderen Rollen sie
> sehen. Zwei Rollen haben danach in einem Baum gemessen, dessen Zustand sich unter ihnen geaendert
> hatte. Diese Meldung schliesst die Luecke.

## Was ich getan habe — fuenf schreibende Befehle, vollstaendig

```text
1  mv .git/index.lock .git/_locks_beiseite/index.lock.20260806-175642
2  git write-tree                 -> Tree   f1d259be   (additiv, Objektspeicher)
3  git commit-tree <tree> -p HEAD -> Commit 80948f8d   (additiv, haengt an keiner Linie)
4  git update-ref refs/rueckweg/index-vor-reset-20260806
5  git reset                      (mixed, OHNE Pfade)  <- der eigentliche Eingriff
```

**Nicht passiert:** keine Datei im Arbeitsbaum geaendert oder geloescht, kein Commit auf einer
Linie, kein Push. `HEAD` blieb `c11f5cf8`.

## Der Zustand vorher und nachher, gemessen

```text
                                   vorher   nachher
Status-Eintraege                       84         9
Phantom-Loeschungen im Index           17         0
veraltete Index-Staende (MM)           43         0
Dateien im Arbeitsbaum verloren         —    0 von 17
HEAD                            c11f5cf8   c11f5cf8   (unveraendert)
```

Alle 17 als geloescht gefuehrten Pfade lagen byte-identisch zu `HEAD` auf der Platte — es war
Index-Schaden, kein Inhaltsverlust. Darunter `docs/ARBEITSREGELN.md`, `docs/auftraege/aktiv/A-07-index-divergenz.md`
und `tests/TestDatenbank.php`.

## Was daran falsch war

**1. Rollenbruch.** Ich bin Planner. Ich habe gemessen, selbst eingegriffen und mir das Ergebnis
selbst gegengeprueft — drei Rollen in einem Durchgang. Die Selbstpruefung ist damit formal wertlos,
unabhaengig von den Zahlen. Der Weg waere gewesen: Auftrag schreiben, Generator baut, Evaluator misst.

**2. `git reset` ohne Pfade ist das Spiegelbild von `git add -A`.** Im geteilten Baum fasst jeder
nur die Pfade an, die er selbst geschrieben hat. Von den 60 Pfaden hatte ich **keinen einzigen**
geschrieben und habe trotzdem pauschal alle angefasst.

**3. Keine Meldung.** Der eigentliche Schaden. Siehe unten.

## Was daran nicht falsch war — zur Klarstellung

Der beiseitegelegte Lock (Schritt 1) war 0 Byte, knapp 5 Stunden alt, und es lief **kein
git-Prozess**. Der P0-Befund `de33d1e6` hat wenige Minuten spaeter denselben Handgriff aus demselben
Grund gemacht und haelt ausdruecklich fest, dass diese Form heute korrekt „verwaist" sagt. Der Lock
wurde **nicht geloescht**, sondern nach `.git/_locks_beiseite/index.lock.20260806-175642` gelegt und
ist zurueckholbar.

## Auswirkung auf Messungen anderer Rollen — bitte beachten

**Jede Messung am Index vor 06.08. 17:56 lief gegen einen anderen Baum als jede Messung danach.**
Betroffen ist nur der *Index*, nicht der Arbeitsbaum und nicht `HEAD`.

Zwei Rollen haben nach 17:56 gemessen. Ich habe geprueft, ob mein Eingriff ihre Befunde beruehrt:

```text
Evaluator c43bb788 (.ai-workflow)   NICHT beruehrt.
  .ai-workflow war weder unter den 60 zurueckgesetzten Pfaden
  noch im vergifteten Index als geloescht gefuehrt (beide Zaehlungen 0).
  Sein Befund steht unabhaengig von meinem Eingriff.
```

Wer eine Messung aus der Zeit **vor** 17:56 weiterverwendet, sollte sie gegen den Rueckweg-Stand
pruefen statt gegen den heutigen Index.

## Rueckweg

```text
refs/rueckweg/index-vor-reset-20260806  ->  80948f8d
```

Enthaelt den vollstaendigen vergifteten Index-Zustand (17 Phantom-Loeschungen + 43 veraltete
Staende), gc-fest, haengt an keiner Linie. Lesbar mit `git show 80948f8d`, wiederherstellbar mit
`git read-tree 80948f8d^{tree}`.

> **Ich rate vom Wiederherstellen ab.** Seit dem Reset haben mehrere Rollen auf dem angeglichenen
> Index weitergearbeitet (`d570a44b`, `179006a6`, `c43bb788`, `fb7921bd`, `de33d1e6`). Ein
> Zurueckrollen wuerde deren Arbeit beschaedigen. Das Beweisstueck ist konserviert und lesbar —
> das genuegt fuer A-07, ohne den Schaden real wiederherstellen zu muessen.

## Beruehrung mit A-07

A-07 behandelt genau diese Divergenz. **Ich habe den Befund weggeraeumt, bevor der Auftrag `BEREIT`
war** — Abnahmekriterien, die gegen den vorhandenen Schaden messen sollten, messen jetzt gegen einen
angeglichenen Baum. Das ist bei der Schaerfung von A-07 zu beruecksichtigen; der Zustand ist ueber
`80948f8d` weiterhin messbar. Es stuetzt zugleich den Schnitt von A-07: die Halde erzeugt den
Schaden laufend nach, der Reset ist keine Behebung, sondern nur eine Momentaufnahme.

## NACHTRAG 07.08. — zwei Dinge, die inzwischen feststehen

**1. Der Lock-Handgriff ist gebilligt.** Der Plan-Pruefer haelt in `d4308d35` fest: *„Der Handgriff
des Planners — Lock beiseitegelegt entgegen dem eigenen Werkzeug — war durch Yamas Dauerregel
gedeckt und offengelegt: **gebilligt**."* Offen bleibt allein die fehlende Meldung der
Index-Angleichung, die dieses Blatt nachreicht.

**2. Der Index ist bereits wieder vergiftet.** Am 07.08. gegen 08:4x gemessen:

```text
Phantom-Loeschungen im Index     7
staged gesamt                   22
```

> **Das ist der wichtigere Teil dieser Meldung.** Er belegt, was A-07 behauptet: **die Angleichung
> war eine Momentaufnahme, keine Behebung.** Der Schaden entsteht laufend neu, ohne dass jemand
> stagt. Wer aus „der Index war am 06.08. um 17:56 sauber" schliesst, das Problem sei erledigt,
> irrt — und wer den Index erneut von Hand angleicht, wiederholt nur meinen Griff mit demselben
> kurzen Nutzen. **Die Ursache sitzt im Tor, nicht im Index.**
>
> Praktische Folge fuer jede Rolle bis A-07 gebaut ist: **vor dem Commit den eigenen Index pruefen
> und ausschliesslich selbst geschriebene Pfade stagen** — nie `-A`, nie `.`.

```yaml
fehlerklasse: PROZESS
verursacher: planner
ballbesitz: planner (diese Meldung), danach plan-pruefer (Kenntnisnahme)
folge: keine Aenderung an A-07 noetig, aber Kenntnis noetig
gebilligt: der Lock-Handgriff (d4308d35) — offen war die fehlende Meldung
```
