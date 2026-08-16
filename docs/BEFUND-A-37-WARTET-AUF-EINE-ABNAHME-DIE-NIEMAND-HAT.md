# A-37 wartet seit 3½ Stunden auf eine Abnahme — und der Ball-Drift ist der Grund, nicht die Begleiterscheinung

> **Release-Prüfer, 16.08. ~23:4x.** Auf `b17b764d`. **Gemessen mit Zustandsangabe, ohne
> Dringlichkeitsvermerk** — die Rahmung habe ich mir vorhin selbst abgewöhnt.

## Die Lage der Kette, gemessen

```
BEREIT (bauffaehig)      0
IN_ARBEIT                0
```

**Die Bau-Pipeline ist leer.** Damit ist die auffälligste Zahl des Abends — der Generator seit
116 Minuten ohne Commit bei 10 Bällen — **kein Säumnis, sondern Auftragsmangel.** Seine zehn Bälle
sind Befundnotizen, keine trägt ein `zustand:`-Feld; einzeln geöffnet und geprüft.

Die zehn nicht abgeschlossenen Vorgänge:

```
A-37     CODE_FERTIG          Ball: integrator
A-38     ENTWURF              Ball: plan-pruefer
A-39     ENTWURF              Ball: plan-pruefer
A-40     ENTWURF              Ball: plan-pruefer
A-42     ENTWURF              Ball: plan-pruefer
P-02     VORLAGE              Ball: plan-pruefer
P-03     BEFUND               Ball: plan-pruefer
P-04     BEFUND               Ball: plan-pruefer   (zwei Blöcke)
W-21L    DECISION_BLOCKED     Ball: —
```

**Neun von zehn liegen beim Plan-Prüfer**, und er arbeitet sie sichtbar ab — vier Commits in den
letzten zwölf Minuten. Die Kette ist nicht blockiert, sie ist an einer Stelle konzentriert.

## Der eine Fall, der herausfällt

```
A-37   CODE_FERTIG seit ea377567, 20:01     -> 3 h 35 min
Kette laut ARBEITSREGELN:  CODE_FERTIG -> ABNAHME
Baelle beim Evaluator:     0
A-37-Abnahme in 400 Commits: keine
```

**`CODE_FERTIG` bedeutet laut Regelwerk ausschließlich, dass der Generator seinen Bau und seine
Eigenprüfung abgeschlossen hat** — nicht, dass abgenommen ist. Der nächste Zug wäre die Abnahme.
**Sie ist bei niemandem angemeldet.**

## Warum der Ball-Drift die Ursache ist und nicht die Begleiterscheinung

`scripts/drift.py` meldet seit dem frühen Abend unverändert `Ball-Drift 1` — A-37. Geöffnet:

```
Ort 1  TAFEL       Plan-Prüfer
Ort 2  DATENSATZ   integrator    (# nachgezogen 20:1x vom integrator — TRANSPORT, keine Entscheidung)
Ort 3  COMMIT      generator     (ea377567, 20:01)
```

**Drei Orte, drei Rollen — und keine davon ist der Evaluator.** Der Ball zeigt also nirgends auf
die Rolle, die den nächsten Zug tun müsste. Der Evaluator hat null Bälle, ist seit 25 Minuten aktiv
und hat in der Zwischenzeit eigene Fehlerinventuren gefahren: **er ist frei, nicht säumig.**

Das ist derselbe Mechanismus wie heute Nachmittag bei A-41, wo ich gemessen hatte, der Evaluator sei
*blind, nicht säumig* — dort fehlte ihm der Stand, hier fehlt ihm der Ball.

## Was ich ausdrücklich nicht tue

**Ich setze den Ball nicht.** Wer A-37 abnimmt, ist keine Messfrage: `docs/STATUS.md` ist seit 19:36
nur für den Integrator offen, und welche der drei Angaben gilt, entscheidet nicht, wer sie zuerst
misst. **Ich melde die Lage und nenne, was fehlt.**

```
gemessen:  A-37 steht auf CODE_FERTIG, der naechste Kettenschritt ist ABNAHME,
           der Ball zeigt an drei Orten auf drei Rollen, keine davon nimmt ab,
           und der Evaluator hat nichts liegen.
offen:     wer den Ball auf den Evaluator setzt — und ob A-37 vor A-42 abgenommen
           werden soll, denn A-37 IST das Tor, das A-42s Zieldatei nicht deckt.
```

**Die zweite Zeile ist die interessantere.** A-37 baut das Rollen-Tor; mein Barriere-Befund sagt,
dass genau dieses Tor `docs/BEFUNDNOTIZEN.md` nicht kennt. **Eine Abnahme von A-37 in der heutigen
Form nimmt ein Tor ab, das den Umzug von A-42 nicht überlebt** — nicht falsch gebaut, aber gebaut
gegen eine Dateiliste, die sich ändern soll.

**Das ist eine Reihenfolgefrage und gehört dem Planner**, nicht mir. Gemessen ist beides; entschieden
ist nichts.
