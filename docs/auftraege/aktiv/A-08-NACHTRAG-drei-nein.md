# A-08 NACHTRAG — verwaist ist ein Lock erst nach DREI Nein

```yaml
art: NACHTRAG — kein eigener Auftrag
gehoert_zu: docs/auftraege/aktiv/A-08-halter-nach-kommando.md
traegerblatt_bleibt: JA   # dessen §5-Block, Wiederverwendungspruefung und Erstnutzer-Angabe gelten
liefert: die getroffene Richtungsentscheidung + drei fehlende Kriterien
basis_sha: 6953198a       # §12.2: Nachbesserung auf der Linie des Baus
anlass: de33d1e6 (P0-Befund) · d377683a (Evaluator) · d4308d35 (Richtungsentscheidung) · f5098c40 (SPEC_BLOCKED Generator) · 0a4efd84 (Triage Plan-Pruefer)
ballbesitz: plan-pruefer (Zusammenfuehrung + BEREIT), danach generator
```

> **⚠ Doppelfuehrung, von mir verursacht und hier aufgeloest.** Ich habe dieses Blatt als zweites
> `A-08` geschnitten, ohne zu sehen, dass drei Minuten vorher bereits
> [`A-08-halter-nach-kommando.md`](A-08-halter-nach-kommando.md) lag. **Das Traegerblatt ist
> jenes, nicht dieses.** Dieser Nachtrag ist kein konkurrierender Auftrag, sondern die Zulieferung
> dessen, was das Traegerblatt zeitlich noch nicht haben konnte.
>
> **Was das Traegerblatt behaelt und dieser Nachtrag NICHT dupliziert:** die
> Wiederverwendungspruefung (§5), der Auswirkungen-Block (§5), die Erstnutzer-Angabe, der Rueckweg,
> sowie seine Kriterien `A-08-3` (alle A-02-Zusagen bleiben gruen) und `A-08-4` (die Meldung nennt
> das **Kommando** des Halters, nicht nur die PID). *Letzteres fehlt hier und ist ein guter Punkt.*
>
> **Was dieser Nachtrag liefert:** das Traegerblatt legt die Richtung dem Plan-Pruefer **vor**
> („nicht vorentschieden", Stand 08:38). Der Plan-Pruefer hat sie **08:38:59 in `d4308d35`
> entschieden** — und **gegen beide dort vorgeschlagenen Wege einzeln**. Diese Entscheidung, ihre
> Begruendung und drei daraus folgende Kriterien stehen unten.

## Anlass — das Tor hat zwei Rollen ausgesperrt, und das Kriterium ist meins

**06.08., ab 18:06.** Ein Commit lief auf `exit 3` mit
`GEHALTENER LOCK .git/index.lock, Halter 59792`. Kurz darauf traf es den Plan-Pruefer am selben
Lock (`fb7921bd`). Das Werkzeug hat **genau getan, was A-02 verlangt**. Der Fehler liegt in dem,
was ich als Kriterium geschrieben habe.

Gemessen (Befunder, Evaluator und Plan-Pruefer unabhaengig): PID 59792 ist die
**Virtualization.framework-VM**, seit Tagen laufend, **kein git-Prozess**. Sie wird als Halter fuer
`.git/config`, `.git/HEAD`, `docs/STATUS.md`, `CLAUDE.md` und `README.md` gemeldet.

> **Eine Frage, die zu selten „nein" sagen kann, ist keine Pruefung, sondern eine Blockade.**
> A-02 wurde geschnitten, um das Raten zu beenden — und treibt die Rollen jetzt in genau das
> Handaufraeumen, gegen das es gebaut wurde.

## Die Ursache — mein Messfehler, benennbar an einer Zeile

`A-02` Zeile 61–66 traegt die Grundlage des Kriteriums:

```text
  Lock von lebendem Prozess gehalten   lsof -> 1 Halter    mtime stillstehend: JA
  verwaister Lock                      lsof -> 0 Halter    mtime stillstehend: JA
  -> die Ruhe trennt die Faelle NICHT. lsof trennt sie exakt.
```

Die Messung ist **fuer ihren Ort richtig** und war **fuer diesen Ort nicht uebertragbar**: gefahren
im Wegwerf-Repo, geschlossen auf den echten Arbeitsbaum. Widerlegt ist der letzte Satz.

Der Fehlertyp darunter ist der wichtigere: **`lsof` beantwortet „hat jemand die Datei offen", nicht
„arbeitet gerade git daran".** Die Frage war plausibel, die Zuordnung ungeprueft.

## Ist-Zustand — was gemessen ist und was ausdruecklich nicht

```text
GEMESSEN (mehrfach, unabhaengig):
  lsof -t .git/config · .git/HEAD · docs/STATUS.md · CLAUDE.md · README.md  -> 59792
  ps  -p 59792                -> Virtualization.VirtualMachine, KEIN git, seit Tagen
  pgrep git                   -> 0 sichtbare git-Prozesse
  Der Zweig HALTER=0 IST erreichbar: eine frisch angelegte Datei meldet keinen Halter,
  auch nach Lesen, nach Schreiben und nach 700 s nicht — zz-unlink-probe vom 03.08. dagegen schon.

NICHT ERMITTELT — und deshalb hier keine Erklaerung:
  Was die beiden Dateigruppen trennt, ist offen. Die Inode-Deutung ist Vermutung geblieben.
  Ob ein git INNERHALB der Sandbox-VM im Host-`ps`/`lsof` ueberhaupt sichtbar ist, ist UNGEMESSEN.
```

> **Die Formulierung haengt daran.** „Die Maschine kann nicht antworten" ist nicht pruefbar und
> ausserdem widerlegt — der Zweig `HALTER=0` wurde erreicht. Pruefbar ist:
> **`lsof` antwortet auf eine andere Frage als die gestellte.** Nur diese Fassung traegt.

*Zur Reichweite meiner eigenen Probe: ich habe mit einer per Umleitung angelegten Datei gemessen —
genau der Sorte, die den Phantom-Halter nie bekommt. Der Evaluator hat denselben blinden Fleck an
seiner Probe vom 03.08. benannt. **Eine Lock-Probe muss von einem echten git-Lauf stammen, nicht
von `touch`.** Das gilt auch fuer die Tests dieses Auftrags.*

## DECISION — drei Nein, UND-verknuepft, NUR bei 0-Byte-Locks (entschieden in `d4308d35`, eingeschraenkt im Umschnitt 07.08.)

```text
GILT NUR FUER 0-BYTE-LOCKS — bei Inhalt > 0 Byte stellt sich die Kommando-Frage NICHT (Korrektur 3):
1  Halter-Kommando ist kein git                 (Form A — NEU)
2  kein git-Prozess DIESES Repositoriums        (Form B — NEU, billig zuerst)
3  das Altersmass des Tors ist erfuellt         (BESTEHEND, unveraendert uebernommen;
                                                 fuer 0 Byte heisst es: >= 60 s, Z.163)

ALLE DREI nein UND Lock = 0 Byte   ->  beiseitelegen nach Dauerregel (NIE loeschen,
                                       Zielpfad + Groesse + Alter in der Meldung),
                                       Commit laeuft weiter
Lock MIT Inhalt (> 0 Byte) + Halter -> bleibt liegen wie HEUTE (commit-pruefen.sh:142-148),
                                       egal welches Kommando der Halter traegt —
                                       ENV_BLOCKED, exit 3
SONST                              ->  heutiges Verhalten unveraendert (Z.161-191)
```

> **⚠ KORREKTUR 07.08. nach `ec051a1c` (`SPEC_BLOCKED`, Ballbesitz Planner) — zwei Punkte.**
>
> **(1) Bedingung 3 zitiert das Tor, sie formuliert es nicht neu.** Meine erste Fassung schrieb
> „0 Byte UND Alter >= 60 s". Das Tor fuehrt aber seit `2f56e9e8` einen **Doppelpfad**
> (`commit-pruefen.sh:163`):
>
> ```text
> if { [ "$GROESSE" -eq 0 ] && [ "$ALTER" -ge 60 ]; } || [ "$ALTER" -ge 120 ]; then
> ```
>
> Der zweite Zweig (Stillstand: `>=120 s` plus zwei ruhige Proben) stammt aus der Blockade des
> Evaluators vom 03.08. — 317 s alt, 885 kB, dreifach als tot belegt. **Zwei gruene Zusagen haengen
> daran, eine davon traegt `must_preserve` im Namen.** Wer meine erste Fassung woertlich baut,
> nimmt den `>=120 s`-Zweig heraus und faerbt genau diese beiden rot. Der Testkommentar sagt
> woertlich: *„Die alte Regel ‚0 Byte UND >=60s' konnte ihn nicht erkennen — sie trennte die
> Faelle nur zur Haelfte."* **Ich hatte genau diese alte Regel wieder hingeschrieben.**
>
> **A-08 aendert die dritte Bedingung nicht.** Sie bleibt, was das Tor heute misst — beide Zweige.
> Die Drei-Nein-Regel setzt ausschliesslich die **zwei neuen** Bedingungen davor.
>
> **(2) Bedingung 2 braucht den Repo-Bezug.** „Kein laufender git-Prozess" ohne Bezug haette einen
> git-Lauf in einem **fremden** Verzeichnis mitgezaehlt und hier blockiert. Gemeint sind
> git-Prozesse, die auf **dieses** Repositorium arbeiten. *Der Evaluator hat das ausdruecklich als
> offene Frage und nicht als Befund gemeldet — die Praezisierung kostet nichts und schliesst sie.*

> **⚠ KORREKTUR 3 — UMSCHNITT 07.08. nach `f5098c40` (SPEC_BLOCKED des Generators, dritter Fund
> derselben Klasse) und `0a4efd84` (Triage des Plan-Pruefers): die Kommando-Frage ersetzt die
> Halter-Blockade NUR bei 0-Byte-Locks.**
>
> Die Drei-Nein-Tabelle **ohne Groessen-Schranke** faerbt zwei heute gruene Zusagen rot, vom
> Generator gefunden und von mir an den Testzeilen nachgemessen: `A-02-2`
> (`commitPruefen.test.mjs:512` — Lock 900 B, 400 s, NODE-Halter, erwartet LIEGT + `exit 3` +
> Halter-PID) und `A-02-4` (Z.579 — 50 B, 400 s, NODE-Halter, erwartet `exit 3` +
> `ENV_BLOCKED`-Zeile). Beide kodieren den Kern von A-02: **die EXISTENZ eines lebenden Halters
> schuetzt einen Lock MIT Inhalt** — meine Richtung `d4308d35` hatte das auf **git**-Halter
> verengt und damit genau den Schutz aufgeloest, den A-02 gebaut hat.
>
> Deshalb: **bei Inhalt > 0 Byte bleibt die heutige Blockade unveraendert** (`commit-pruefen.sh:
> 142-148`). Die Kommando-Frage greift nur dort, wo kein Inhalt auf dem Spiel steht — der
> Vorfalls-Fall vom 06.08. war **0 Byte**. *Die Klasse „Content-Lock, verwaist, phantom-gehalten"
> bleibt damit bewusst `ENV_BLOCKED` mit Handraeumung nach Yamas Dauerregel — ehrliche Grenze,
> siehe Traegerblatt.*

**Warum keine der Formen allein genuegt:**

```text
FORM A allein   Der Halter ist hier immer die VM — moeglicherweise auch, waehrend ein git
                AKTIV arbeitet. Form A allein erklaerte dann JEDEN gehaltenen Lock fuer
                verwaist und legte mitten im fremden Commit beiseite: der heutige Fehler,
                nur spiegelverkehrt und teurer.
FORM B allein   Bei mehreren parallelen Rollen ist „irgendwo laeuft git" haeufig -> dauerhaft
                falsches GEHALTEN. Und die Sichtbarkeit von git in der Sandbox-VM ist
                ungemessen — dieselbe Klasse ungepruefter Zuordnung, aus der dieser Befund stammt.
BEDINGUNG 3     deckt genau den Fall, den weder A noch B sehen KANN: ein lebendiges git haelt
                den Lock Sekunden. Was das Alters-/Groessenmass des Tors reisst und dabei
                nirgends sichtbar ist, ist kein arbeitendes git.
                Dieses Mass wird von A-08 NICHT neu formuliert, sondern uebernommen.
```

## Nicht-Ziele

- **Kein Loeschen von Locks.** Beiseitelegen bleibt beiseitelegen, mit Zielpfad in der Meldung.
- **Keine Aenderung an A-02-2/-3/-4/-6** und keine an Stufe 4/5 (Ort, Zeitpunkt, ausgelagerter Index).
- **Keine Sonderbehandlung der Virtualisierung.** Kein Ausschluss von PID 59792, keine
  Mount-Erkennung. *Eine Ausnahmeliste verschoebe den Fehler nur — sie beantwortet wieder nicht,
  ob git arbeitet.*
- **Keine Erklaerung der zwei Dateigruppen.** Ungeklaert ist ungeklaert; der Fix greift in beiden
  Richtungen und braucht sie nicht.

## Scope

```text
scripts/commit-pruefen.sh                  die Drei-Nein-Pruefung
scripts/__tests__/commitPruefen.test.mjs   die Zusagen
docs/auftraege/aktiv/A-02-...md            Zeile 61-66 richtigstellen (A-08-7)
```

## Akzeptanzkriterien

**Jedes P1 ist an `6953198a` wirksam rot** — der Plan-Pruefer bestaetigt das vor dem Bau. *Die
Rot-Lage zu A-08-1 hat er bereits gemessen (zweimal `exit 3`); die uebrigen nicht ich.*

**A-08-1 (P1, der Vorfall — 0-BYTE-FASSUNG, Umschnitt 07.08., FUEHRENDER WORTLAUT):** Ein
**0-Byte-Lock**, der **das bestehende Altersmass des Tors erfuellt** (fuer 0 Byte: >= 60 s,
`commit-pruefen.sh:163`) und **weder einen git-Halter noch einen git-Prozess dieses Repositoriums
aufweist**, wird **beiseitegelegt, nie geloescht** — die Meldung nennt Zielpfad, Groesse und
Alter; der Commit laeuft weiter. Ein Lock **MIT Inhalt (> 0 Byte) und Halter bleibt liegen wie
heute** — unabhaengig vom Kommando des Halters. *Rot an der Basis: der Vorfall vom 06.08.
(0 Byte, 239 s, VM-Halter) endet heute in `exit 3`, zweimal gemessen.*

**A-08-2 (`must_preserve`, Gegenhalter Zeit):** Ein **frischer** Lock (< 60 s) bleibt liegen ->
`ENV_BLOCKED`, `exit 3`. *Ohne dieses Kriterium waere „legt immer beiseite" gruen.*

**A-08-3 (`must_preserve`, Gegenhalter Bestand — KORRIGIERT 07.08.):** **Alle heute gruenen
A-02-Zusagen bleiben gruen**, ausdruecklich einschliesslich der beiden am Stillstandspfad
(`>= 120 s`): *„Tor Teil 2: ein ALTER Lock MIT Inhalt, dessen mtime stillsteht, ist ein Rest"* und
*„A-02-1 KONTROLLE: Lock MIT Inhalt, alt, ohne Halter -> beiseite (`must_preserve`)"*. Der
Doppelpfad in `commit-pruefen.sh:163` wird **nicht angetastet**. **Nach dem Umschnitt schliesst
das ausdruecklich `A-02-2` (Z.512) und `A-02-4` (Z.579) ein** — die Simulationstabelle unten
spielt die 0-Byte-Fassung gegen alle 30 Zusagen durch; erst sie macht dieses Kriterium erfuellbar.

> *Meine erste Fassung sagte hier „ein Lock mit Inhalt bleibt liegen, egal wie alt" und haette
> genau diese zwei Zusagen rot gefaerbt. Der Vorfall vom 04.08. (887 796 B / 888 008 B) war
> **pauschales Raeumen von Hand am Tor vorbei** — er ist kein Argument gegen den Stillstandspfad
> des Tors, das ihn nie beruehrt hat. Ich hatte beides verwechselt.*

**A-08-4 (P1, die gefaehrliche Richtung in Form A):** Die Halter-Pruefung vergleicht den
**Basenamen** des Kommandos und erkennt `git-*`-Unterprozesse (z. B. `git-remote-https`). *Rot an
der Basis messbar: `ps -o comm=` liefert hier den **vollen Pfad**; ein Vergleich auf `= "git"`
haelt `/usr/bin/git` fuer einen Nicht-git-Prozess.*

**A-08-5 (P1, Unklarheit bleibt konservativ):** Laesst sich zu einer gemeldeten Halter-PID **kein
Kommando ermitteln**, waehrend die PID existiert, gilt der Halter als **unbekannt** -> Lock bleibt
liegen, `ENV_BLOCKED: ... (Halter: unbekannt)`.

**A-08-6 (P1, Mutationsprobe — die toedliche zuerst):** Mindestens **sieben** Mutationen fallen:
**die drei Bedingungen mit `||` statt `&&` verknuepft** · Kommando-Pruefung entfernt · deren
Ergebnis ignoriert · Basename durch Pfad-Gleichheit ersetzt · `git-*` nicht erkannt · unbekannter
Halter als „nicht gehalten" gewertet · **die 0-Byte-Schranke entfernt (Kommando-Frage auch bei
Locks MIT Inhalt)** — diese letzte faellt bereits durch die bestehenden Zusagen `A-02-2` (Z.512)
und `A-02-4` (Z.579); *sie ist exakt der Fall aus `f5098c40` und darf nie wieder stumm gruen sein.*

> *`&&` -> `||` ist die eine Mutation, die alle drei Schutzbedingungen gleichzeitig entwertet und
> dabei jeden Einzeltest gruen laesst, der nur einen Zweig prueft. Sie gehoert an den Anfang der
> Probe, nicht ans Ende.*

**A-08-7 (P0, Doku):** `A-02` Zeile 61–66 wird richtiggestellt — *„lsof trennt sie exakt"* gilt hier
nicht, mit Verweis auf `de33d1e6`, `d377683a` und dieses Blatt. *Ein freigegebenes Blatt, das eine
widerlegte Messung als Grundlage stehen laesst, erzeugt den naechsten Fehler anderswo.*

**A-08-8 (P1, Probenherkunft):** Mindestens **eine** Zusage arbeitet mit einem Lock aus einem
**echten git-Lauf**, nicht aus `touch`/Umleitung — und der Test benennt das. *Beide bisherigen
Gegenproben (03.08. Evaluator, 06.08. Planner) waren echt und trotzdem blind fuer genau diesen
Fall.*

## Kantenliste (UMGESCHNITTEN 07.08. — jede Zeile traegt ihre Lage: IST = heutiges Verhalten unveraendert · SOLL = Aenderung durch A-08)

> *Der Generator hat in `f5098c40` als Nebenbefund gemeldet, dass die alte dritte Zeile eine
> Verhaltens**aenderung** als Bestandserhalt beschrieb: ein **gehaltener** Lock erreicht den
> Stillstandspfad heute nie — `commit-pruefen.sh:142-148` blockt vorher mit `GEHALTENER LOCK`;
> die zwei gruenen Stillstandspfad-Zusagen (Z.115, Z.547) laufen **ohne** Halter. Selbst
> nachgelesen und hier behoben: unter der 0-Byte-Fassung bleibt dieser Fall die heutige Blockade.*

```text
VM haelt die Datei, kein git sichtbar, 0 Byte, 61 s   -> beiseite            (SOLL — der Vorfall
                                                                              06.08.; heute exit 3)
dasselbe, aber Lock 30 s alt                          -> liegen lassen       (IST=SOLL: Bedingung 3
                                                                              fehlt, < 60 s)
dasselbe, 800 kB, 300 s still                         -> liegen lassen       (IST=SOLL: Inhalt +
                                                                              Halter = Blockade
                                                                              Z.142-148; die
                                                                              03.08.-Klasse —
                                                                              ehrliche Grenze,
                                                                              Handraeumung)
dasselbe, 800 kB, 90 s alt                            -> liegen lassen       (IST=SOLL: Inhalt +
                                                                              Halter, Z.142-148 —
                                                                              der Alterszweig wird
                                                                              gar nicht erreicht)
git-Halter sichtbar, Lock alt und leer                -> liegen lassen       (IST=SOLL: Form A
                                                                              greift — Bedingung 1
                                                                              nicht erfuellt)
git-Prozess dieses Repos, kein Halter, Lock alt/leer  -> liegen lassen       (SOLL — Form B; HEUTE
                                                                              legte Z.161-167 ohne
                                                                              Prozess-Frage beiseite)
git-Prozess auf einem FREMDEN REPOSITORIUM            -> irrelevant          (Repo-Bezug, s. o.)
   ⚠ RICHTIGGESTELLT 08.08. — hier stand "in einem FREMDEN VERZEICHNIS". Das verwechselt
   "fremde cwd" mit "fremdem Repo": `git --git-dir=<dieses .git>` mit fremder cwd arbeitet
   auf DIESEM Repo und wurde nach dem alten Wortlaut uebersehen. Der Generator hat die Zeile
   korrekt befolgt, der Evaluator hat es in der Abnahme gemessen (23b3a490, Probe C).
   Klasse SPEC, Verursacher Planner, laeuft als A-09 — A-08 bleibt ABGENOMMEN (§12.5).
mehrere Halter, EINER davon git, Lock 0 Byte          -> liegen lassen       (konservativ)
Halter-PID existiert, Kommando nicht ermittelbar      -> liegen lassen       (A-08-5, konservativ)
nicht-git-Halter, Lock MIT Inhalt, beliebig alt       -> liegen lassen       (IST=SOLL: A-02-2/
                                                                              A-02-4, Z.512/579 —
                                                                              der Kern des Umschnitts)
PID zwischen lsof und ps wiederverwendet              -> im Zweifel gehalten
lsof haengt                                           -> A-02-6 unveraendert (Zeitgrenze)
lsof fehlt                                            -> A-02-3 unveraendert (konservativer Rueckfall)
```

## Simulation der 0-Byte-Fassung gegen den Zusagen-Bestand (30 Zusagen, selbst gefahren: 30/30)

*Vom Planner am 07.08. je Zusage am Testcode nachgemessen — Eingaben aus den
`lockSetzen`-Aufrufen der Suite, Zeilennummern aus `scripts/__tests__/commitPruefen.test.mjs`,
Tor-Zeilen aus `scripts/commit-pruefen.sh`. Der Plan-Pruefer vollzieht diese Tabelle in der
DoR-Runde nach — die BEREIT-Runde vom Vormittag hatte genau diese Simulation ausgelassen
(Triage `0a4efd84`).*

| Zusage (Zeile) | Eingabe | Verhalten unter der 0-Byte-Fassung | Ergebnis |
|---|---|---|---|
| A-02-2 (Z.512) | 900 B, 400 s, NODE-Halter | Inhalt > 0 + Halter -> Blockade wie heute (Z.142-148): LIEGT, exit 3, Halter-PID | **gruen** — unter der Fassung OHNE Schranke ROT (der Fund aus `f5098c40`) |
| A-02-2 GEGENPROBE (Z.531) | 900 B, 400 s, Halter beendet | HALTER=0-Pfad unveraendert (Z.161-167): 400 >= 120 -> beiseite, exit 0 | gruen |
| A-02-1 KONTROLLE (Z.547, must_preserve) | 885 kB, 317 s, kein Halter | HALTER=0-Pfad unveraendert: 317 >= 120 -> beiseite, exit 0 | gruen |
| A-02-3 (Z.559) | 900 B, 400 s, lsof FEHLT | unbekannt-Pfad unveraendert (Z.176-191): Lock liegt, exit != 0 | gruen |
| A-02-4 (Z.579) | 50 B, 400 s, NODE-Halter | Inhalt > 0 + Halter -> Blockade wie heute: exit 3 + ENV_BLOCKED-Zeile | **gruen** — ohne Schranke ROT (zweiter Fund aus `f5098c40`) |
| A-02-4 ROT (Z.599) | 0 B, 0 s, kein Halter | 0-Byte-Pfad: Bedingung 3 fehlt (0 < 60) -> liegt, exit 3 „zu jung" | gruen |
| A-02 Kante 2 (Z.613) | 900 B, 400 s, lsof HAENGT | Zeitgrenze + unbekannt-Pfad unveraendert: liegt, exit 3, „Halter: unbekannt" | gruen |
| W-09/K-02 (Z.90) | 0 B, 300 s, kein Halter | drei Nein erfuellt (kein Repo-git zum Pruefzeitpunkt — die Aufraeumung steht VOR dem ersten git-Aufruf, W-09/K-01 Z.221) -> beiseite, exit 0, wie heute | gruen |
| W-09/K-02 ROT (Z.101) | 22 B, 5 s, kein Halter | Inhalt-Pfad unveraendert: junger Lock, liegt, exit != 0 | gruen |
| Tor Teil 2 (Z.115, Stillstandspfad) | 31 B, 300 s, kein Halter | HALTER=0-Pfad unveraendert: 300 >= 120 -> beiseite, exit 0 | gruen |
| W-09/K-02 ROT (Z.130) | 0 B, 0 s, kein Halter | Bedingung 3 fehlt: liegt, exit != 0 | gruen |
| W-09/K-02 Tiefe (Z.140) | Ref-Lock 0 B, 300 s, kein Halter | Suche unveraendert; drei Nein -> beiseite, exit 0 | gruen |
| W-09/K-03 (Z.158) | 14 B, 300 s, kein Halter | HALTER=0-Pfad: BEISEITE-Meldung nennt Name, Groesse, Alter (Tor Z.166) | gruen |
| uebrige 17 Zusagen (W-09/K-07 · K-08 · K-01 · K-04 · sieben W-04 · zwei Tor/GNU · Alters-Zweig Z.471) | Stufe-4/5-, Staging-, stat- und Strukturlogik | vom Umschnitt unberuehrt — er aendert nur den Halter-Zweig (Z.142-148, Schranke GROESSE=0) und stellt die Prozess-Frage am 0-Byte-Pfad | gruen |

**Einzige Verhaltensaenderungen gegenueber heute, beide von keiner Zusage belegt und beide in
Richtung „raeumt weniger, nie mehr" bzw. „raeumt den Vorfalls-Fall":** (1) 0-Byte-Lock mit
nicht-git-Halter, Mass erfuellt, kein Repo-git -> beiseite statt `exit 3` — die Rot-Lage von
A-08-1. (2) 0-Byte-Lock ohne Halter, Mass erfuellt, aber ein git-Prozess dieses Repos laeuft ->
liegen lassen statt beiseite — konservativer als heute, Richtung von A-02-3.

## Rueckweg und Entdeckung

**Rueckweg:** Skriptaenderung ohne Datenmigration — der Commit ist zurueckdrehbar; der vorherige
Stand von `commit-pruefen.sh` liegt auf der Linie.

**Entdeckung — woran man merkt, dass es falsch ist:** Die Meldung nennt beim Beiseitelegen
**Groesse, Alter und Zielpfad**. Ein verwaister Lock ist 0 Byte. Taucht ein beiseitegelegter Lock
**mit Inhalt** auf, wurde einem laufenden git der Index weggezogen — dann ist die Pruefung defekt
und der Vorgang gehoert sofort zurueck an den Planner. *Dasselbe Signal, an dem der Vorfall vom
04.08. erkennbar gewesen waere: 887 796 B und 888 008 B.*

## Was an meinem ersten Schnitt dieses Blattes falsch war

*Steht hier, weil es derselbe Fehlertyp ist wie der, den das Blatt behebt.*

```text
1  Form A fuehrend, Form B nur Vorpruefung — vom Plan-Pruefer widerlegt: Form A allein
   haette JEDEN gehaltenen Lock fuer verwaist erklaert.
2  A-08-1 ohne Groessen-/Altersbedingung formuliert — damit waere der Vorfall vom 04.08.
   (888 kB, beiseitegeschoben) nach dem neuen Blatt gruen gewesen.
3  Einen Mechanismus behauptet ("die VM haelt offen, was sie schon angefasst hat"), den
   niemand gemessen hat. Der Evaluator haelt ausdruecklich fest, dass die Trennung der
   beiden Dateigruppen UNERMITTELT ist.
4  Ein zweites Blatt unter derselben Nummer angelegt, ohne den Bestand zu pruefen —
   Doppelfuehrung, dieselbe Klasse, die der Plan-Pruefer schon an A-01 beanstandet hat
   (Doppelfuehrung mit Z-07). Ein `ls docs/auftraege/aktiv/` vor dem Schreiben haette
   gereicht. Aufgeloest durch Umbenennung in diesen Nachtrag; das aeltere Blatt traegt.
5  Die dritte Bedingung NEU FORMULIERT statt das Tor zu zitieren ("0 Byte UND >=60s")
   und in A-08-3 "Lock mit Inhalt bleibt liegen, egal wie alt" geschrieben. Beides
   haette zwei heute gruene Zusagen rot gefaerbt, eine davon `must_preserve`. Gemeldet
   vom Evaluator (ec051a1c, SPEC_BLOCKED) VOR dem Bau; oben korrigiert.
   Der Fehlertyp ist derselbe wie in Punkt 3 und wie der Anlass des ganzen Blattes:
   eine plausible Regel aufgeschrieben, ohne sie am Bestand zu messen. Zwei Zeilen
   `grep` in commit-pruefen.sh haetten es gezeigt.
6  Die Drei-Nein-Frage OHNE Groessen-Schranke auf den Halter-Pfad gestellt und die
   Tabelle nie gegen den Zusagen-BESTAND simuliert, nur die Rot-Lagen gemessen. A-02
   schuetzt bei Locks MIT Inhalt die EXISTENZ eines lebenden Halters (A-02-2/A-02-4,
   Z.512/579); meine Fassung fragte nur noch nach dem KOMMANDO und haette beide rot
   gefaerbt. Gemeldet vom Generator (f5098c40) VOR dem Bau — dritter Fund derselben
   Klasse, am Halter-Pfad statt am Stillstandspfad —, Triage 0a4efd84. Aufgeloest
   durch die 0-Byte-Schranke (Umschnitt oben) und kuenftig durch die Pflicht-Tabelle:
   jede Regelaenderung am Tor wird VOR der DoR-Runde gegen alle Zusagen durchgespielt.
```

```yaml
fehlerklasse: SPEC
verursacher: planner
prioritaet: P0
ballbesitz: plan-pruefer (BEREIT-Pruefung), danach generator
warteschlange: keine (P0-Begruendung ist gemessen)
```
