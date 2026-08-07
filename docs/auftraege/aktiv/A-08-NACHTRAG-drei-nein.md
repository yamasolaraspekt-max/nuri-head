# A-08 NACHTRAG — verwaist ist ein Lock erst nach DREI Nein

```yaml
art: NACHTRAG — kein eigener Auftrag
gehoert_zu: docs/auftraege/aktiv/A-08-halter-nach-kommando.md
traegerblatt_bleibt: JA   # dessen §5-Block, Wiederverwendungspruefung und Erstnutzer-Angabe gelten
liefert: die getroffene Richtungsentscheidung + drei fehlende Kriterien
basis_sha: 6953198a       # §12.2: Nachbesserung auf der Linie des Baus
anlass: de33d1e6 (P0-Befund) · d377683a (Evaluator) · d4308d35 (Richtungsentscheidung)
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

## DECISION — drei Nein, UND-verknuepft (entschieden in `d4308d35`)

```text
1  Halter-Kommando ist kein git                 (Form A — NEU)
2  kein git-Prozess DIESES Repositoriums        (Form B — NEU, billig zuerst)
3  das Alters-/Groessenmass des Tors ist erfuellt   (BESTEHEND, unveraendert uebernommen)

ALLE DREI nein  ->  beiseitelegen nach Dauerregel (NIE loeschen, Zielpfad in der
                    Meldung), Commit laeuft weiter
SONST           ->  ENV_BLOCKED mit Halter-Angabe, exit 3 — die heutige Form
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

**A-08-1 (P1, der Vorfall):** Ein Lock, der **das bestehende Alters-/Groessenmass des Tors erfuellt**
(konkret: 0 Byte, 61 s alt) und **weder einen git-Halter noch einen git-Prozess dieses Repositoriums
aufweist**, wird beiseitegelegt; der Commit laeuft weiter. *Rot an der Basis: heute `exit 3`,
zweimal gemessen.*

**A-08-2 (`must_preserve`, Gegenhalter Zeit):** Ein **frischer** Lock (< 60 s) bleibt liegen ->
`ENV_BLOCKED`, `exit 3`. *Ohne dieses Kriterium waere „legt immer beiseite" gruen.*

**A-08-3 (`must_preserve`, Gegenhalter Bestand — KORRIGIERT 07.08.):** **Alle heute gruenen
A-02-Zusagen bleiben gruen**, ausdruecklich einschliesslich der beiden am Stillstandspfad
(`>= 120 s`): *„Tor Teil 2: ein ALTER Lock MIT Inhalt, dessen mtime stillsteht, ist ein Rest"* und
*„A-02-1 KONTROLLE: Lock MIT Inhalt, alt, ohne Halter -> beiseite (`must_preserve`)"*. Der
Doppelpfad in `commit-pruefen.sh:163` wird **nicht angetastet**.

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

**A-08-6 (P1, Mutationsprobe — die toedliche zuerst):** Mindestens **sechs** Mutationen fallen:
**die drei Bedingungen mit `||` statt `&&` verknuepft** · Kommando-Pruefung entfernt · deren
Ergebnis ignoriert · Basename durch Pfad-Gleichheit ersetzt · `git-*` nicht erkannt · unbekannter
Halter als „nicht gehalten" gewertet.

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

## Kantenliste

```text
VM haelt die Datei, kein git sichtbar, 0 Byte, 61 s   -> beiseite            (der Fall von heute)
dasselbe, aber Lock 30 s alt                          -> liegen lassen       (Mass nicht erfuellt)
dasselbe, 800 kB, 300 s still                         -> beiseite            (Stillstandspfad des
                                                                              Tors, HEUTE gruen -
                                                                              A-08 aendert das NICHT)
dasselbe, 800 kB, 90 s alt                            -> liegen lassen       (weder Zweig erfuellt)
git-Halter sichtbar, Lock alt und leer                -> liegen lassen       (Form A greift)
git-Prozess dieses Repos, kein Halter, Lock alt/leer  -> liegen lassen       (Form B greift)
git-Prozess in einem FREMDEN Verzeichnis              -> irrelevant          (Repo-Bezug, s. o.)
mehrere Halter, EINER davon git                       -> liegen lassen       (konservativ)
PID zwischen lsof und ps wiederverwendet              -> im Zweifel gehalten
lsof haengt                                           -> A-02-6 unveraendert (Zeitgrenze)
lsof fehlt                                            -> A-02-3 unveraendert (konservativer Rueckfall)
```

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
```

```yaml
fehlerklasse: SPEC
verursacher: planner
prioritaet: P0
ballbesitz: plan-pruefer (BEREIT-Pruefung), danach generator
warteschlange: keine (P0-Begruendung ist gemessen)
```
