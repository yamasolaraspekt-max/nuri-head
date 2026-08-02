# W-04 — `commit-pruefen.sh` scheitert an neuen Dateien, und das Tor hat keine einzige Zusage

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 02.08. 09:1x*

```yaml
auftrag:
  id: W-04
  status: entwurf   # B8 - Werkzeug-Blatt, Gegenleser ist der Evaluator
  gegengelesen_von:
  gegengelesen_am:
  befund:
```

## Warum — der Befund kommt vom Generator, ich bestätige ihn aus eigener Erfahrung

**Sein Wortlaut (`3d3941f2`, 02.08. 08:55):**

> *„Es fährt `git commit -- <pfade>` und scheitert damit an NEUEN Dateien (pathspec did not match).
> Wer ein neues Werkzeug baut, muss vorher von Hand stagen — genau dann, wenn 19 fremde Dateien im
> Index liegen und ein Fehlgriff teuer wäre."*

**Mir ist es am 01.08. um 19:35 genauso gegangen.** Ich habe `git add <pfad>` von Hand nachgeschoben
und es als Kleinigkeit abgetan. **Es ist keine:** der Moment, in dem man von Hand stagen muss, ist
genau der Moment mit dem größten Beifang-Risiko — und **F-05 (Beifang im Index) hat es schon
zweimal gegeben.**

## Der zweite, größere Befund — beim Messen gefunden

```text
ls -1 scripts/*.mjs scripts/*.sh | wc -l   -> 15   Werkzeuge
ls scripts/__tests__/ | wc -l              ->  3   mit Zusagen
```

**Zwölf Werkzeuge haben keine einzige Zusage — darunter `commit-pruefen.sh` selbst.**

*Das Tor, durch das seit dem 01.08. jeder Commit dieses Projekts geht, ist das einzige Werkzeug,
das nie geprüft wurde.* **Es ist gegen F-14 gebaut worden und trägt selbst kein einziges
Beweisstück.** Diese Scheibe holt das für `commit-pruefen.sh` nach — die anderen elf sind ein
eigener Posten, nicht dieser.

## Bestand — gemessen 02.08. 09:1x

```text
wc -l < scripts/commit-pruefen.sh                              -> 65
node scripts/zaehle.mjs scripts/commit-pruefen.sh 'git add'    --raute  -> 0
node scripts/zaehle.mjs scripts/commit-pruefen.sh 'git commit' --raute  -> 1  (Partner: misst)
ls scripts/__tests__/ | grep -ci commit                        -> 0
```

**Die Prüfungen im Skript sind korrekt** — es erkennt `FEHLT`, `LEER`, `UNVERAENDERT` (inklusive
`^??` für ungetrackte Dateien), prüft `node --check` für `.mjs` und den YAML-Kopf für `.md`.
**Nur die letzte Zeile stolpert:**

```text
git commit -q -m "$BOTSCHAFT" -- "$@" || exit 1
```

**`git commit -- <pfad>` kennt eine Datei nicht, die noch nie im Index war.**

## Die Entscheidung

**Das Skript stagt genau die Pfade, die es ohnehin schon geprüft hat — und nur die.**

```text
Fuer jeden Pfad aus "$@", der als `??` gemeldet wird:   git add -- "$p"
Danach wie bisher:                                       git commit -- "$@"
```

**Drei Grenzen, ohne die daraus ein Beifang-Werkzeug wird:**

```text
1  NIE `git add -A`, NIE `git add .`, NIE ein Muster. Nur die Pfade aus der Argumentliste,
   einzeln, mit `--` davor. Die harte Regel verbietet das Pauschale, nicht das Benannte.
2  Das Skript SAGT, was es gestagt hat - eine Zeile je Pfad. Ein stiller Nebeneffekt an
   einem Tor ist schlimmer als der Fehler, den er behebt.
3  Es stagt NUR, wenn alle Pruefungen schon durch sind. Stagen ist eine Aenderung am Index;
   sie darf nicht passieren, wenn der Commit danach ohnehin abgelehnt wird.
```

**Warum das sicher ist, obwohl `git add` auf der Denylist des Validators steht:** die Denylist
gilt für Befehle **in Blättern**, die ein Validator ausführt. Hier ist es das Commit-Werkzeug
selbst, das ausschließlich mit den Pfaden arbeitet, die der Aufrufer genannt und die es geprüft
hat. **Der gefährliche Fall ist `-A` und `.` — und genau der wird durch eine Zusage verriegelt.**

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/commit-pruefen.sh                  das Stagen der ungetrackten Pfade + die Meldung
  scripts/__tests__/commitPruefen.test.mjs   NEU - die ersten Zusagen fuer dieses Tor

Hier bewusst NICHT:
  Die vorhandenen Pruefungen           FEHLT/LEER/UNVERAENDERT/Syntax/YAML bleiben unveraendert.
                                       Sie werden nur zum ersten Mal FESTGENAGELT.
  Die elf anderen Werkzeuge ohne Zusagen   Eigener Posten. Ein Blatt, ein Tor.
  scripts/auftrag-pruefen.mjs          gehoert W-01/W-03.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/commit-pruefen.sh
    - scripts/__tests__/commitPruefen.test.mjs
  population_command: "node scripts/zaehle.mjs scripts/commit-pruefen.sh 'git\\s+add' --raute"
  ausschluesse:
    - stelle: "Die elf anderen Werkzeuge ohne Zusagen"
      grund: "Ein Blatt, ein Tor. Der Rest ist ein eigener Posten."
      entschieden_von: planner
    - stelle: "Die vorhandenen Pruefungen"
      grund: "Sie bleiben unveraendert und werden nur zum ersten Mal festgenagelt."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Das Skript stagt die ungetrackten Pfade selbst."
    pruefung:
      befehl: "node scripts/zaehle.mjs scripts/commit-pruefen.sh 'git\\s+add' --raute"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 02.08. 09:1x; Partner 'git commit' -> 1, die Messung ist nicht leer)"

  - id: K-02
    typ: absence
    kritikalitaet: P1
    aussage: "NIE pauschal - kein -A, kein Punkt, kein Muster."
    pruefung:
      befehl: "node scripts/zaehle.mjs scripts/commit-pruefen.sh 'git\\s+add\\s+-A' --raute"
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      Waechst diese Zahl auf 1, sammelt das Tor die Arbeit aller anderen Instanzen ein - F-05,
      und zwar am gefaehrlichsten Ort. Am 01.08. lagen 19 fremde Dateien im Index; heute noch 2.
      Die Zahl ist nie zuverlaessig 0, deshalb muss die Sperre im Werkzeug sitzen und nicht
      in der Aufmerksamkeit des Aufrufers.

  - id: K-03
    typ: presence
    kritikalitaet: P1
    aussage: "Die ersten Zusagen fuer dieses Tor existieren ueberhaupt."
    pruefung:
      befehl: "ls scripts/__tests__/ | grep commitPruefen | wc -l"
      erwartet: "1"
    ausgangswert: "0 - 15 Werkzeuge, 3 Zusagen-Dateien. Das Tor, durch das jeder Commit geht, hat keine"

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Eine NEUE Datei wird committet - der Fall, an dem es heute scheitert."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        In einem WEGWERF-Repo (mktemp -d, git init), nie im Arbeitsbaum:
          neue Datei anlegen, commit-pruefen.sh aufrufen  -> Commit entsteht, Datei ist drin
        UND die rote Gegenprobe:
          zweite ungetrackte Datei danebenlegen, die NICHT in der Argumentliste steht
          -> sie ist NICHT im Commit und NICHT im Index
        Die zweite ist die eigentliche Zusage. Ohne sie belegt K-04 nur, dass etwas passiert.
      erwartet: "zwei Zusagen, die zweite ist die entscheidende"

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "Bei einer fehlgeschlagenen Pruefung wird NICHTS gestagt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Wegwerf-Repo: eine gueltige neue Datei UND eine mit kaputtem YAML-Kopf zusammen uebergeben.
          -> KEIN Commit (wie bisher)
          -> UND der Index ist unveraendert: auch die gueltige Datei ist NICHT gestagt
        Ohne diese Zusage hinterlaesst ein abgelehnter Aufruf einen halb gefuellten Index -
        und der naechste Commit einer anderen Rolle nimmt ihn mit.
      erwartet: "kein Commit, Index unveraendert"

  - id: K-06
    typ: behavioural
    aussage: "Die vorhandenen Pruefungen sind zum ersten Mal festgenagelt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Je eine Zusage im Wegwerf-Repo: fehlende Datei -> exit 1 · leere Datei -> exit 1 ·
        unveraenderte Datei -> exit 1 · .mjs mit Syntaxfehler -> exit 1 ·
        .md mit kaputtem yaml-Kopf -> exit 1 · alles heil -> exit 0 und Commit da.
        Das ist der Bestand, nicht die Aenderung - er wird nur zum ersten Mal belegt.
      erwartet: "sechs Zusagen"

  - id: K-07
    typ: behavioural
    aussage: "Die bestehenden Zusagen bleiben gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/auftragPruefen.test.mjs scripts/__tests__/zaehle.test.mjs scripts/__tests__/zeileErsetzen.test.mjs"
      erwartet: "0 fail. Ausgangswert 82 pass (02.08. 09:0x). Danach mehr, nie weniger. Dateien EINZELN nennen, nicht das Verzeichnis - ein Verzeichnis laeuft als EIN fehlschlagender Test (Generator-Befund vom 02.08.)."

  - id: K-08
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 6 Mutationen: `git add -A` statt der Pfadliste · stagen VOR der Pruefung ·
        auch getrackte Pfade nochmal stagen · die Meldung weglassen ·
        bei Fehlschlag trotzdem stagen · `--` vor dem Pfad weglassen (Datei namens `-f`).
        Wie viele kommen durch?
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  `git add -A` schleicht sich ein.                                   -> K-02
2  Ein abgelehnter Aufruf hinterlaesst einen halb gefuellten Index.   -> K-05
3  Ein Pfad, der NICHT in der Liste steht, wird mitgestagt.           -> K-04 Gegenprobe
4  Eine Datei heisst wie eine Option (`-f`). Ohne `--` vor dem Pfad
   liest git sie als Schalter.                                        -> K-08 Mutation 6
5  Das Stagen passiert still.                                          -> K-08 Mutation 4
6  Eine Datei ist im Index UND im Arbeitsbaum anders (Status `MM`).
   OHNE ZUSAGE, mit Grund: `git commit -- <pfad>` committet den ARBEITSBAUM-Stand, das ist
   das gewuenschte Verhalten und aendert sich durch diese Scheibe nicht. Wer es aendert,
   aendert etwas anderes als das Blatt sagt.
7  Der Test legt ein Wegwerf-Repo an und raeumt es nicht weg.
   OHNE ZUSAGE, mit Grund: `rm` ist auf dem Mount verboten; die Zusagen laufen in `mktemp -d`
   ausserhalb des Mounts. Das gehoert in den Testkopf, nicht in ein Kriterium.
```

## Rückweg und Entdeckung

**Rückweg:** ein Shell-Skript und eine neue Zusagen-Datei — zurückdrehbar. **Und wer zurückdreht,
hat den heutigen Zustand: ein Tor, das an neuen Dateien scheitert, aber nichts kaputt macht.**

**Entdeckung:** die Zahl der gestagten Fremddateien im `git status`. Sie stand am 01.08. um 23:2x
bei **19**, heute früh bei **2**. **Wächst sie nach dieser Scheibe, hat das Tor angefangen zu
sammeln** — und das ist rot, egal wie grün die Zusagen sind.

## Danach

**Die elf übrigen Werkzeuge ohne Zusagen.** `commit-pruefen.sh` ist das dringendste, weil jeder
Commit hindurchgeht — aber `pfade-pruefen.sh` und `statische-inline-stile.mjs` liefern Zahlen, die
in Abnahmen zitiert werden. **Eine Zahl aus einem ungeprüften Werkzeug ist eine Behauptung mit
Nachkommastelle.**
