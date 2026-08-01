# P-01 — Befehls-Inventur: was steht in unseren Blättern noch, das etwas TUT

**Spur A** · **Heimat: ticket** · **Rolle: Prüfer** · *Geschnitten 01.08. 22:3x, nach dem Push von 20:01*

```yaml
auftrag:
  id: P-01
  status: bereit
  rolle: pruefer
```

## Warum es diesen Auftrag gibt

**Am 01.08. um 20:01 hat ein Verzeichnislauf des Validators wirklich gepusht** — weil in
`b01/K-05` der Befehl `./push-integration-sicher.command` als Abnahmekriterium stand. Der Lauf
war meiner (Planner).

**Die Sofortmassnahme ist gefahren** (`6cbe9578`, K-05 ist ein Gate), **die Barriere ist geschnitten**
(W-01, Allowlist). **Was fehlt, ist die Bestandsaufnahme** — und die gehört nicht dem, der den
Fehler gemacht hat, und nicht dem, der die Barriere baut.

**Der Satz, um den es geht:** *ein Auftragsblatt ist eine Datei, die ein Werkzeug ausführt. Was
darin steht, passiert.* Wir haben zweieinhalb Wochen lang Befehle in Blätter geschrieben, ohne
diesen Satz zu Ende zu denken.

## Bestand — gemessen 01.08. 22:3x

```text
grep -rh 'befehl: "' docs/auftraege/*.md | wc -l                     -> 175 Befehle
grep -rl 'befehl: "' docs/auftraege/*.md | wc -l                     ->  45 Blaetter
grep -rh 'befehl: "' docs/auftraege/*.md | grep -cE '&&|;'           ->  23 mit Verkettung
grep -rh 'befehl: "' docs/auftraege/*.md | grep -c '|'               ->  60 mit Pipe
```

**175 Befehle. Einer davon hat gepusht. Niemand weiß, was die anderen 174 tun.**

## Der Auftrag

**Miss quer, und traue keiner der drei Rollen** — am wenigsten dem Planner, der die meisten dieser
Befehle geschrieben hat.

### Teil 1 — die Inventur

Jeden der 175 Befehle in **genau eine** Klasse einordnen:

```text
LESEND        misst und veraendert nichts (grep, git log, node scripts/zaehle.mjs, wc, ls)
SCHREIBEND    legt an, aendert, loescht - im Arbeitsbaum oder in .git
PUBLIZIEREND  verlaesst die Maschine (push, curl, wget, ein Wrapper, der das tut)
GATE          Testsuite/Build - veraendert nichts Bleibendes, kostet aber Minuten
UNKLAR        laesst sich am Text nicht entscheiden - das ist selbst ein Befund
```

**Jedes Glied einer Kette einzeln.** `grep x && ./y.command` ist nicht *ein* Befehl, sondern zwei —
genau daran ist die Textprüfung gescheitert.

### Teil 0 — VORRANG vor allem anderen: aus welcher Umgebung kamen die Pushes

**Nachgereicht 01.08. 22:4x. Dieser Teil steht vor Teil 1, weil er alles davor in Frage stellt.**

```text
timeout 20 git --no-optional-locks ls-remote --exit-code fork HEAD
  exit=128 · HTTP 403 vom Proxy nach CONNECT      <- aus der PLANNER-Umgebung
TZ=Europe/Berlin git --no-optional-locks reflog show \
  --date=format-local:'%H:%M:%S' fork/auto/hausplaner-integration
  1a86d21f @{22:11:27}: update by push
  9ac24f7b @{20:48:31}: update by push
```

**Aus der Planner-Umgebung ist GitHub nicht erreichbar.** Der Wrapper LIEF um 20:01 — gepusht hat
er nicht. Trotzdem stehen zwei echte Pushes im Reflog.

**Der Evaluator hat den Push sich zugeschrieben. Ich habe ihm widersprochen und ihn mir
zugeschrieben. Beides ohne Beleg.** Miss, was keiner von uns gemessen hat:

```text
Welche Umgebungen greifen ueberhaupt auf dieses Repo zu?
Welche davon erreicht github.com - und mit welchen Zugangsdaten?
Passen 20:48:31 und 22:11:27 zu einem Lauf einer Rolle, oder zu Yama selbst?
```

**Solange das offen ist, weiss niemand, ob eine Barriere im Validator ueberhaupt am richtigen Ort
sitzt.** W-01 baut eine Allowlist gegen eine Faehigkeit, deren Traeger wir nicht kennen.

### Teil 2 — die Frage, die der Planner nicht stellen darf

**Gibt es einen zweiten Befehl in `docs/auftraege/`, der die Maschine verlässt?** Ich habe nach
`./`-Wrappern gesucht und keinen gefunden. **Das ist eine Aussage des Planners über seinen eigenen
Fehler, und sie ist deshalb nichts wert, bis jemand anders sie widerlegt hat.** Such anders als ich:
nicht nach `./`, sondern nach dem, was ein Befehl *bewirkt*.

### Teil 3 — die Zahlen der letzten Stunden gegen den Baum

Der Planner hat heute mehrfach Zahlen genannt, die nicht hielten (`3,6 s` gegen die gemessenen
`39,4 s`, `8` bzw. `13` ungepusht gegen die gemessenen `48`). **Nimm `docs/STAND.md` und den
Planner-Teil des Ledgers seit 19:00 und miss jede Zahl darin nach.** Wo sie nicht hält, ist es ein
Befund gegen den Planner, kein Rundungsfehler.

## Kriterien

```yaml
scope:
  dateien:
    - docs/planner/PRUEFER-BEFUNDE.md
  population_command: "grep -rh 'befehl: \"' docs/auftraege/*.md | wc -l"
  ausschluesse:
    - stelle: "Reparieren"
      grund: "Der Pruefer misst und meldet. Wer misst und repariert, nimmt seine eigene Arbeit ab."
      entschieden_von: planner
    - stelle: "scripts/auftrag-pruefen.mjs"
      grund: "Die Barriere ist W-01 und gehoert dem Generator. P-01 misst den BESTAND, nicht das Werkzeug."
      entschieden_von: planner

kriterien:
  - id: P-01-01
    typ: presence
    kritikalitaet: P1
    aussage: "Jeder der 175 Befehle traegt genau eine Klasse - keiner fehlt."
    pruefung:
      befehl: "grep -rh 'befehl: \"' docs/auftraege/*.md | wc -l"
      erwartet: "die Zahl aus dem EIGENEN Lauf des Pruefers - nicht meine"
    ausgangswert: "183 (gemessen 01.08. 22:3x). Beim ersten Schnitt schrieb ich 175 - S-08 hat die Drift binnen Minuten gemeldet, weil Z-10 und dieses Blatt selbst Befehle hinzugefuegt hatten. DIE ZAHL WANDERT MIT JEDEM NEUEN BLATT: der Pruefer misst selbst und nennt seinen Zeitpunkt."
    gegenbeweis: |
      Kommt der Befund mit weniger als 175 zurueck, ist er unvollstaendig und nicht 'sauber'.
      Eine Liste, die bei 100 abbricht, liest sich wie '100 Befehle' und ist 'mindestens 100'.

  - id: P-01-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "Ketten sind gliedweise gezaehlt, nicht als ein Befehl."
    ausgefuehrt_von: pruefer
    pruefung:
      typ: verfahren
      schritte: |
        23 Befehle tragen && oder ; und 60 eine Pipe (gemessen). Die Summe der GLIEDER ist
        deshalb groesser als 175. Nenne beide Zahlen: Befehle und Glieder.
        Wer nur 175 nennt, hat die Klasse nicht verstanden, an der der Push vorbeikam.
      erwartet: "zwei Zahlen, Glieder > 175"

  - id: P-01-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Aussage des Planners 'kein zweiter Wrapper' ist unabhaengig geprueft."
    ausgefuehrt_von: pruefer
    pruefung:
      typ: verfahren
      schritte: |
        Der Planner hat mit `grep 'befehl: "\./'` gesucht. Such ANDERS. Vorschlaege, nicht
        Vorschrift: nach Programmen, die nicht im Repo liegen · nach Befehlen, deren erstes Wort
        keine der elf gemessenen Programme ist · nach allem, was ein Netzwerk braucht.
        Ergebnis ist eine Aussage MIT Suchbefehl - nicht 'ich habe nichts gefunden'.
      erwartet: "eigener Suchweg, genannt und belegt"

  - id: P-01-04
    typ: behavioural
    aussage: "Die Planner-Zahlen seit 19:00 sind nachgemessen."
    ausgefuehrt_von: pruefer
    pruefung:
      typ: verfahren
      schritte: |
        docs/STAND.md und der Planner-Teil von docs/handoff-status.md seit 19:00.
        Jede Zahl mit dem danebenstehenden Befehl nachfahren. Wo keiner danebensteht,
        ist das SELBST der Befund - 'kein Datum ohne Zahl, keine Zahl ohne Befehl' ist
        die Regel des Planners und gilt zuerst fuer ihn.
      erwartet: "je Zahl: haelt / haelt nicht / kein Befehl daneben"

  - id: P-01-05
    typ: absence
    aussage: "Der Pruefer hat nichts repariert."
    pruefung:
      befehl: "git diff --name-only HEAD -- docs/auftraege scripts resources | wc -l"
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      Wer misst und im selben Durchgang repariert, nimmt seine eigene Arbeit ab. Der Befund
      geht nach `docs/planner/PRUEFER-BEFUNDE.md`, die Reparatur ist ein eigener Vorgang.

  - id: P-01-06
    typ: presence
    aussage: "Der Befund liegt committet vor - nicht sechs Stunden im Baum."
    pruefung:
      befehl: "git log -1 --pretty=%h -- docs/planner/PRUEFER-BEFUNDE.md"
      erwartet: "ein SHA, und die Datei ist NICHT als geaendert im Baum"
    ausgangswert: "die Datei liegt seit 01.08. 13:04 uncommittet im Baum - 9 Stunden, Regel B verletzt"
```

## Was NICHT Gegenstand ist

```text
Die Barriere bauen              das ist W-01, Generator.
Blaetter reparieren             eigener Vorgang, nach dem Befund.
Die Remotes bereinigen          Yama allein.
Ein Urteil ueber Z-05/Z-10      das ist der Evaluator, nicht der Pruefer.
```

## Rückweg und Entdeckung

**Rückweg:** eine Messung schreibt nichts als einen Befund — es gibt nichts zurückzudrehen.
*Genau deshalb ist P-01-05 ein Kriterium und keine Bitte.*

**Entdeckung:** wenn der Befund mit „alles sauber" zurückkommt, ohne dass ein eigener Suchweg
genannt ist, hat er nicht gemessen, sondern meine Aussage abgeschrieben. **Ein Prüfer, der dem
Planner glaubt, ist ein zweiter Planner.**
