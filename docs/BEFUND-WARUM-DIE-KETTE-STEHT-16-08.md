# Warum die Kette steht — offene Commits, wer schreibt, und woran es hängt

> **Release-Prüfer, 16.08. ~16:5x, in Yamas Namen.** Auf seine Frage *„was ist jetzt mit offene
> commits, wer commitet und warum passiert es nicht"*. Alles nach `fetch --multiple` frisch
> gemessen.

## 1 · Offene Commits — in den Rollenbäumen keine

```
ticket                     auto/hausplaner-integration   0 offen
ticket-rolle-planner       rolle/planner                 0
ticket-rolle-plan-pruefer  rolle/plan-pruefer            0
ticket-rolle-generator     rolle/generator               1  (1 untracked)
ticket-rolle-evaluator     rolle/evaluator               0
ticket-release-pruefung    rolle/release-pruefer         0
```

**Nichts hängt unbeachtet in einem Rollenbaum.** Alle fünf Rollenzweige stehen auf `fork` **und**
`backup-private` auf demselben Commit, je einzeln gegengeprüft.

Uncommittete Arbeit gibt es, aber **außerhalb der Rollenkette** — in alten Arbeitssträngen:

```
ticket-strang-C            18 offen   (5 geaendert, 13 ungetrackt)
ticket-strang-accounting    6 offen   (3 geaendert,  3 ungetrackt)
ticket-a01                  6 offen
ticket-g1b-0                2 offen
```

Das ist kein Stau der Rollenkette, sondern Altbestand aus der Zeit davor. **Er blockiert nichts** —
gehört aber aufgeräumt oder ausdrücklich stillgelegt, sonst sieht ihn irgendwann jemand für aktive
Arbeit an.

## 2 · Wer schreibt — vier von sechs, und einer fehlt auffällig

```
rolle/plan-pruefer     16:42   vor    0 min     aktivste Rolle
rolle/release-pruefer  16:39   vor    3 min
rolle/planner          16:35   vor    7 min
rolle/generator        16:30   vor   12 min
rolle/evaluator        14:57   vor  105 min  ← still
integrator             16:17   ein einziger Commit
```

## 3 · Warum es nicht passiert — drei gemessene Gründe

### Grund 1 — der Evaluator hat keinen Ball

Ballbesitz über alle aktiven Aufträge, am Commit gemessen:

```
plan-pruefer   4   A-38, A-39, A-40, W-17/1     alle ENTWURF
generator      2   A-37, A-41                   beide ENTWURF
—              2   A-05, A-12                   geschlossen
evaluator      0
```

**Er wartet nicht, ihm wurde nichts gegeben.** Und Schritt I — die unabhängige Prüfung der
Sperrfälle, an der `SCHREIBEND` hängt — ist ihm nie zugewiesen worden: A-37 liegt beim **Generator**.
Solange das so bleibt, kann Schritt I nicht stattfinden, egal wie lange man wartet.

### Grund 2 — es gibt nichts abzunehmen

> **⚠ BERICHTIGT 16.08. 17:0x — dieser Grund ist überholt, und zwar 5 Minuten nach seiner
> Niederschrift.** Gemessen habe ich um **16:46:39**; um **16:52:01** hat der Planner A-41 auf
> `CODE_FERTIG` gesetzt (`e1cc61ef`), danach W-17/1 auf `BEREIT` (16:55:56) und A-37 auf `BEREIT`
> (16:56:59). **Es gibt jetzt ein Übergabestück: A-41, Ball beim Evaluator.**
>
> Die Zahl war am Messstand richtig — im Stand `d9fd6471` sind es nachweislich **0** `CODE_FERTIG`.
> **Falsch war die Form, nicht der Wert:** der Satz unten steht im Präsens ohne Stand und liest sich
> darum wie ein Dauerzustand. Wer ihn zitiert, zitiert ihn ohne Verfallsdatum — und genau das ist
> passiert, als daraus geschlossen wurde, eine Regel sei „mangels Übergabestück" nicht anwendbar.
> **Lehre für mich: eine Zahl in einem Befund trägt den Messzeitpunkt, oder sie trägt eine
> Entscheidung, für die sie nicht mehr gilt.**

**Kein einziger Auftrag steht auf `CODE_FERTIG`.** Die zwei Treffer, die eine grobe Suche auf
`zustand: CODE_FERTIG` liefert, habe ich geöffnet: beide sind **Zitate in Fließtext** (Z.9780 und
Z.12490, Befundtexte über frühere Zustandswechsel), kein Datensatz. Alle acht aktiven Aufträge
hängen in `ENTWURF` — also in der DoR-Phase, vor dem Bau.

Die Kette staut sich nicht am Ende, sondern **ganz am Anfang**.

### Grund 3 — die Zustandskette steht seit der Zündung

```
Commits, die docs/STATUS.md aendern      12h:16  13h:43  14h:43  15h:35  16h:19
davon ZUSTANDSWECHSEL                    12h: 1  13h: 3  14h: 4  15h: 2  16h: 0
```

Der sprechendste Einzelfall: `f19557c8` (Generator, 16:23) trägt im Betreff
*„zustand: A-41 · CODE_FERTIG · generator"* — und hat `docs/STATUS.md` **gar nicht angefasst**, nur
`scripts/status-erzeugen.sh`. Der Datensatz sagt bis jetzt `ENTWURF`. **Der Zustandswechsel ist
gemeldet, aber nirgends angekommen.**

*Ehrlich zur Beweiskraft:* 0 in 45 Minuten gegen 1–4 je Stunde davor ist ein **Signal, kein Beweis**.
Der Einzelfall A-41 ist der harte Teil, die Stundenreihe nur der Rahmen.

## 4 · Der schärfste Befund: die Barriere wirkt verkehrt herum

Gemessen, welcher Baum das Tor überhaupt besitzt:

```
ticket (Integration)        0        ticket-rolle-generator      1
ticket-rolle-planner        0        ticket-rolle-evaluator      1
ticket-rolle-plan-pruefer   0        ticket-release-pruefung     1
                                                          --> 3 von 6
```

Damit erklärt sich, was zunächst wie ein Widerspruch aussah: **nach der Zündung um 16:17 haben
Planner und Plan-Prüfer `docs/STATUS.md` achtmal geschrieben.** Sie umgehen nichts — **das Tor liegt
in ihren Bäumen gar nicht.** Gesperrt sind Generator, Evaluator und ich; also genau die drei, die
die Barriere haben und sich an sie halten.

**Die Ausgestatteten stehen still, die Unausgestatteten schreiben weiter.** Das ist die Umkehrung
dessen, was die Sperre bezwecken soll, und sie ist heute der eigentliche Grund, warum die Kette
hängt: die Rollen, die Zustände wechseln müssten (Generator → `CODE_FERTIG`, Evaluator → `ABGENOMMEN`),
können es nicht; die Rollen, die schneiden und prüfen, können es — brauchen es aber selten.

### Eine Berichtigung am Befund des Planners

Er misst *„Generator 1, Evaluator 1, Integration 0, planner 0, plan-pruefer 0, release 0"* — also
**2 von 6**. Es sind **3 von 6**. Für „release" hat er `ticket-rolle-release` gemessen; dieser Baum
ist der **leere, abgelöste Rest aus P2H-09** (`HEAD` detached, `ls-files` 0). Mein tatsächlicher
Arbeitsbaum heißt `ticket-release-pruefung` und trägt das Tor.

Das ändert seinen Befund in der Sache nicht — die Hälfte der Bäume ist ohne Barriere, und A-37-18
bleibt richtig. Aber der leere Gleichnamige ist eine Falle, in die die nächste Messung wieder
tappt. **Er gehört benannt oder beseitigt** (Beseitigen wäre eine Löschung — die entscheidet Yama,
nicht ich).

## 5 · Was die Kette wieder in Gang bringt

Kein einziger dieser Punkte ist Bauarbeit:

1. **Drei Fast-Forwards** — Integration, Planner, Plan-Prüfer holen das Tor (alle `0 voraus`, je
   eine Zeile). Danach ist A-37-18 erfüllt und die Barriere wirkt in alle Richtungen gleich.
2. **A-37 aus der DoR**, dann Ball an den Evaluator für Schritt I. Ohne diesen Ballwechsel wartet
   Schritt I unbegrenzt.
3. **Dann Schritt J** — belegt statt gewettet.

Der leere Baum `ticket-rolle-release` und der Altbestand in den vier Strang-Bäumen sind davon
unabhängig und drängen nicht.
