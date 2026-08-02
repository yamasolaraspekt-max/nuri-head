# W-09 — Das Commit-Tor räumt den Lock VORHER weg, nicht nachher

**Spur B** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 03.08. 00:2x*

```yaml
auftrag:
  id: W-09
  strang: werkzeuge
  status: bereit   # B8 ERFUELLT: gegengelesen vom Evaluator 03.08. (TRAEGT MIT EINER MITTLEREN UND ZWEI KLEINEN AUFLAGEN), alle drei eingearbeitet 03.08. 00:5x. MITTEL: K-01 mass einen KOMMENTAR - nachgemessen steht der erste echte git-Aufruf in Zeile 36, der erste lock-erzeugende in Zeile 59; erwartet auf "hoechstens 35" korrigiert. KLEIN 1: K-05-Ausgangswert 91 an HEAD nachgemessen, erledigt. KLEIN 2: Ausweichpfad je Prozess eindeutig, plus die GRENZE von Stufe 5 als eigener Abschnitt (sie wirkt nur fuer Laeufe durchs Tor). Eingetragen vom Planner.
  gegengelesen_von: evaluator   # Werkzeug-Blatt -> Evaluator (B8/d1cecdcf; Kopfkommentar sagt noch Pruefer)
  gegengelesen_am: 2026-08-03
  befund: >
    TRAEGT MIT EINER MITTLEREN UND ZWEI KLEINEN AUFLAGEN. Nachgemessen: 855 Locks exakt,
    Skript 65 Zeilen, Aufraeumung bei 63/64, K-01-Ausgangswert 63, Sicherheitsregel und
    Kantenliste decken die gefaehrlichen Faelle. MITTEL: die Begruendung von K-01 misst
    einen KOMMENTAR - "Zeile 5: git commit" ist Prosa (Kopfkommentar); echte git-Aufrufe
    stehen ab Zeile 36/37 (ausdruecklich lockfrei via --no-optional-locks), der erste
    lock-erzeugende ist Zeile 59. Das erwartet "hoechstens 4" darf als einfache Invariante
    bleiben, aber die Begruendung muss den echten Boden nennen - dritter Kommentar-Treffer
    der Woche (toolRegistry:268, breiten.test.ts:51, jetzt commit-pruefen.sh:5). KLEIN 1:
    K-05-Ausgangswert 91 setzt die unverbuchten W-06/W-07-Commits voraus (an HEAD gemessen:
    82) - Reihenfolge benennen. KLEIN 2: Stufe 5 braucht einen je Prozess EINDEUTIGEN
    Ausweichpfad (PID/Rolle im Namen), sonst teilen sich zwei parallele Tor-Laeufe denselben
    externen Index und die Kollision wandert nur nach draussen; und Stufe 5 wirkt nur fuer
    Laeufe DURCHS Tor - direkte git-Aufrufe locken weiter im Mount. Wenn "nie wieder" gelten
    soll, gehoert die Tor-Pflicht fuer alle Rollen ausgesprochen; ich stelle meine eigenen
    Commits ab sofort auf commit-pruefen.sh um. B8-Fragen: Befehle laufen, K-02/K-07 messen
    Wirkung mit roten Gegenproben, kein maschineller Befehl mutiert.
```

## Warum — ein echter Baufehler im Tor. NICHT zwei Probleme in einem

**RICHTIGSTELLUNG 03.08., auf den Befund des Generators.** *Die erste Fassung dieses Blattes
behauptete: „855 Locks und elf Stunden unverbuchte Arbeit haben dieselbe Ursache" und „der
Generator meldet vier fertige Scheiben, die er nicht loswird". **Beides zusammen war falsch.***

```text
Aufrufe von commit-pruefen.sh durch den Generator   ->  0     (er hat gemessen)
ls .git/*.lock bei ihm                             ->  kein Lock im Weg
```

**Nicht das Werkzeug hat ihn gehindert, sondern die Regel:** `CLAUDE.md` — *„Commits nur auf
Yamas ausdrückliches Wort."* **Ich habe eine Aussage über seine Fähigkeit gemacht, ohne einen
Befehl, der sie ausübt — B5, und diesmal von mir über ihn.** *Derselbe Fehler, den ich am 01.08.
beim Push erlebt habe, aus der anderen Richtung.*

**Was bleibt, ist ein echter und gemessener Baufehler:**

```text
ls .git/_locks_beiseite/*/ | grep -c lock          ->  855
davon heute                                        ->   40, alle vom PLANNER
```

*Der Planner ruft das Tor ständig, und bei ihm blockiert der Lock tatsächlich — vierzig
`mv`-Aufrufe von Hand an einem Tag.* **Das Tor räumt NACH dem Commit auf, während der
blockierende Lock schon VORHER daliegt. Dieser Fehler steht für sich und braucht keine zweite
Begründung.**

*Die elf Stunden unverbuchter Arbeit haben eine ANDERE Ursache — den Regelkonflikt, und der ist
seit 03.08. aufgelöst (siehe STAND, Regel B zurückgenommen). Wer zwei Probleme als eines
behandelt, baut die Lösung für das falsche.*

## Die Ursache steht in zwei Zeilen, und sie stehen an der falschen Stelle

```text
scripts/commit-pruefen.sh   65 Zeilen
  Zeile  5   git commit …
  Zeile 63   mkdir -p .git/_locks_beiseite/"$(date +%F)"
  Zeile 64   mv .git/*.lock .git/_locks_beiseite/"$(date +%F)"/
```

**Das Tor räumt NACH dem Commit auf. Der Lock, der den Commit blockiert, liegt aber schon VORHER
da** — aus einem früheren `git add`, das ihn auf diesem Mount nicht entfernen konnte (F-10,
`unlink` verboten).

```text
git add <pfade>          Lock entsteht, kann nicht entfernt werden   -> bleibt liegen
bash commit-pruefen.sh   git commit  ->  fatal: Unable to create index.lock
                         Zeile 63/64 wird NIE erreicht
```

**Die Aufräumung, die das Problem lösen soll, läuft nur, wenn das Problem nicht auftritt.**
*Der Planner umgeht das seit gestern von Hand — 40 `mv`-Aufrufe allein heute. Wer das nicht
weiß, dessen Commit scheitert, und die Arbeit bleibt liegen.*

## Die Entscheidung

```text
Dieselbe Aufraeumung laeuft AUCH VOR dem Commit - mit der Sicherheitsregel davor:
  0 Byte  UND  seit mindestens 60 s unveraendert   ->  Rest, wird beiseitegeschoben
  sonst                                            ->  laufender Vorgang, HAENDE WEG
```

**Die Sicherheitsregel ist nicht Zierrat, sie ist der ganze Unterschied.** *Ein Lock kann auch
einem laufenden `git` gehören. Wer ihn dann wegzieht, zerstört genau das, was er sichern will.*
**Die Größe unterscheidet die Fälle: ein laufender Vorgang schreibt hinein (879 KB gemessen am
01.08.), ein Rest bleibt bei 0 Byte.**

*Warum nicht `rm`: auf diesem Mount ist `unlink` verboten. `mv` ist der einzige Weg, und er ist
zugleich der bessere — nichts geht verloren, alles ist nachlesbar.*

## Yamas Auflage: „nie wieder" — und was das in der Wirkhierarchie heißt

**Wortlaut, 03.08.: *„sorg dafür dass dieses problem nie wieder passiert"*.** *„Nie wieder" ist
Stufe 5 — Unmöglichkeit —, nicht Stufe 4. Gemessen, wie weit die Wurzel erreichbar ist:*

```text
rm im Arbeitsbaum       Operation not permitted
rm in .git/             Operation not permitted
rm AUSSERHALB des Mounts  GEHT

GIT_INDEX_FILE=<pfad ausserhalb>  +  git status
  Lock im Mount danach       0        <- der Lock entsteht GAR NICHT MEHR im Mount
  Lock am Ausweichort        1        <- und dort ist er loeschbar
```

**Der Index-Lock ist der, der blockiert: 37 von 40 Locks heute waren `index.lock`, nur 3 waren
`HEAD.lock` — und HEAD.lock hat nie einen Commit verhindert.** *Wird der Index aus dem Mount
gelegt, kann der blockierende Fall nicht mehr eintreten. Das ist keine Milderung mehr, das ist
Stufe 5.*

**Deshalb hat dieses Blatt ZWEI Stufen, und die Reihenfolge ist Absicht:**

```text
STUFE 4  Das Tor raeumt vorher auf.  PFLICHT, wirkt sofort, kein Risiko.        K-01…K-04
STUFE 5  Der Index liegt ausserhalb des Mounts. WIRKT AN DER WURZEL.            K-07
         PREIS, ehrlich benannt: der Index ueberlebt den Sitzungswechsel nicht.
         Verloren geht dabei KEINE Arbeit - der Arbeitsbaum bleibt, und `git status`
         baut den Index neu auf. Verloren geht der STAGING-Zustand: was jemand
         schon `git add`-ed hatte, muss er erneut stagen.
         Das ist zumutbar, weil in diesem Projekt ohnehin mit ausdruecklichen
         Pfaden committet wird (R13) - niemand baut einen Index ueber Stunden auf.
```

*Warum Stufe 4 trotzdem gebaut wird, obwohl Stufe 5 stärker ist:* **Stufe 5 hängt an einer
Umgebungsvariablen, die jede Rolle in jeder Sitzung gesetzt haben muss. Vergisst sie einer, ist
er wieder im alten Zustand — und dann trägt Stufe 4.** *Zwei Riegel, von denen der äußere nichts
kostet.*

### Die GRENZE von Stufe 5, benannt statt verschwiegen

**Auflage des Evaluators, 03.08., angenommen:** *Stufe 5 wirkt nur für Läufe **durch das Tor**.
Wer `git commit` direkt aufruft, lockt weiter im Mount.*

```text
"nie wieder" gilt also unter EINER Bedingung: alle Rollen committen DURCH das Tor.
```

**Das ist damit ausgesprochen und nicht mehr stillschweigend vorausgesetzt** — *und der Evaluator
hat seine eigenen Commits bereits umgestellt.* **Die Tor-Pflicht gehört in den Ledger und in die
STAND-Regeln, nicht in eine Fußnote dieses Blattes.** *Ein Riegel, dessen Bedingung niemand
kennt, ist ein halber Riegel.*

## Nahtstellen
## Nahtstellen

```text
Hier wird geschrieben:
  scripts/commit-pruefen.sh        die Aufraeumung vor den Commit, mit Sicherheitsregel

Hier bewusst NICHT:
  scripts/zeile-ersetzen.mjs       schreibt keine Locks - es benutzt kein git.
  Ein git-Wrapper                  waere die groessere Loesung und die falsche: das Tor ist
                                   die EINE Stelle, durch die jeder Commit geht. Wer einen
                                   Wrapper baut, hat zwei Stellen und muss beide pflegen.
  Das Aufraeumen der 855 Reste     Sie stoeren nicht, sie liegen in `_locks_beiseite`.
                                   Wer sie loeschen will, braucht `rm` - und das geht hier
                                   nicht. Eigene Frage, eigener Ort.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/commit-pruefen.sh
  population_command: "grep -c '_locks_beiseite' scripts/commit-pruefen.sh"
  ausschluesse:
    - stelle: "Ein git-Wrapper"
      grund: "Das Tor ist die EINE Stelle, durch die jeder Commit geht. Ein Wrapper waeren zwei Stellen."
      entschieden_von: planner
    - stelle: "Das Aufraeumen der 855 Reste"
      grund: "Sie liegen in `_locks_beiseite` und stoeren nicht. Sie zu loeschen braucht `rm`, und das geht auf diesem Mount nicht."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Die Aufraeumung steht VOR dem Commit."
    pruefung:
      befehl: "grep -n '_locks_beiseite' scripts/commit-pruefen.sh | head -1 | cut -d: -f1"
      erwartet: "hoechstens 35 - VOR dem ersten echten git-Aufruf in Zeile 36"
    ausgangswert: "63 (gemessen 03.08. 00:2x). AUFLAGE DES EVALUATORS EINGEARBEITET 03.08. 00:5x: die erste Begruendung nannte 'Zeile 5: git commit' - das ist ein KOMMENTAR, Prosa im Kopf des Skripts, kein Aufruf. Nachgemessen mit `grep -n 'git ' scripts/commit-pruefen.sh` ohne Kommentarzeilen: erster echter git-Aufruf Zeile 36 (`git --no-optional-locks diff`, ausdruecklich lockfrei), erster LOCK-ERZEUGENDER Zeile 59 (`git commit`). Die Aufraeumung steht also 23 Zeilen zu spaet gegenueber dem ersten Aufruf und 4 Zeilen zu spaet gegenueber dem lock-erzeugenden - nicht 58. Die Zahl 63 stimmt, die Begruendung war falsch gemessen. DRITTER Kommentar-Treffer der Woche (toolRegistry:268 · breiten.test.ts:51 · commit-pruefen.sh:5) - F-09, und alle drei hat jemand anderes gefunden."
    gegenbeweis: |
      Bleibt sie hinten, laeuft sie nur, wenn der Commit gelungen ist - also genau dann,
      wenn sie nicht gebraucht wird. Das ist keine Aufraeumung, sondern eine Nachsorge fuer
      den Erfolgsfall.

  - id: K-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE WIRKUNG: ein liegengebliebener Lock verhindert den Commit nicht mehr."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Wirkung, nicht gegen die Zeilennummer:
          eine 0-Byte-Datei .git/index.lock anlegen, aelter als 60 s
          dann commit-pruefen.sh mit einer echten Aenderung fahren
            -> der Commit GELINGT, der Lock liegt in _locks_beiseite/<datum>/
        UND die ROTEN Gegenproben, und sie sind hier die wichtigeren:
          ein Lock mit INHALT (nicht 0 Byte)          -> Tor bricht ab, Lock bleibt
          ein Lock, der gerade erst entstanden ist    -> Tor bricht ab, Lock bleibt
        Ein Tor, das jeden Lock wegzieht, ist gefaehrlicher als eines, das gar nicht
        aufraeumt: es zerstoert den laufenden Vorgang eines anderen.
      erwartet: "drei Zusagen, zwei davon ROT"

  - id: K-03
    typ: presence
    kritikalitaet: P1
    aussage: "Der Abbruch NENNT den Grund - er schweigt nicht."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Bricht das Tor wegen eines lebenden Locks ab, steht im Text: welche Datei, welche
        Groesse, wie alt. Ein "Abbruch" ohne Grund schickt den naechsten Leser suchen -
        dieselbe Auflage wie bei der Erlaubnisliste in W-01 und bei `verlangeFreigabe`
        in Z-06-N1.
      erwartet: "Dateiname, Groesse und Alter in der Meldung"

  - id: K-04
    typ: behavioural
    aussage: "Die Nachsorge am Ende bleibt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Die Zeilen 63/64 verschwinden NICHT - sie raeumen den Lock weg, den der eigene
        Commit gerade erzeugt hat. Vorher UND nachher, nicht statt.
        Wer sie verschiebt statt zu ergaenzen, hinterlaesst nach jedem Commit einen Rest,
        den erst der naechste Lauf wegraeumt - und der naechste Lauf kann Stunden spaeter sein.
      erwartet: "beide Stellen vorhanden"

  - id: K-05
    typ: behavioural
    aussage: "Die Werkzeug-Suite bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/*.mjs"
      erwartet: "0 fail. Ausgangswert 91 pass / 0 fail — vom Planner AN HEAD nachgemessen 03.08. 00:5x (`node --test scripts/__tests__/*.mjs`), nachdem der Generator W-06 und W-07 um 00:44 verbucht hat. Die Auflage des Evaluators (an HEAD waren es 82) ist damit erledigt, nicht nur benannt."

  - id: K-07
    typ: behavioural
    kritikalitaet: P1
    aussage: "STUFE 5 - der Index-Lock kann im Mount gar nicht mehr entstehen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Das Tor setzt `GIT_INDEX_FILE` auf einen Pfad AUSSERHALB des Mounts, falls die
        Variable nicht schon gesetzt ist, und legt den Index dort an.
        Vom Planner vorgemessen (03.08. 00:3x), mit genau diesem Weg:
            GIT_INDEX_FILE=<ausserhalb> git status
              Lock im Mount        0
              Lock am Ausweichort  1   und dort ist `rm` erlaubt

        Zusagen:
          nach einem vollstaendigen Tor-Lauf liegt in .git/ KEIN index.lock
          der Ausweichpfad liegt nachweislich NICHT unter dem Mount
          der Ausweichpfad ist je PROZESS eindeutig (PID oder Rolle im Namen)
            AUFLAGE des Evaluators: teilen sich zwei parallele Tor-Laeufe denselben
            externen Index, wandert die Kollision nur nach draussen statt zu verschwinden.
            Zusage: zwei gleichzeitige Laeufe benutzen NICHT denselben Pfad.
          fehlt der Ausweichort (erster Lauf), wird er angelegt - kein Abbruch
          ist GIT_INDEX_FILE bereits von aussen gesetzt, wird sie NICHT ueberschrieben

        DIE ROTE GEGENPROBE, und sie ist die wichtigste:
          derselbe Lauf OHNE die Variable  ->  ein index.lock bleibt in .git/ liegen
        Ohne sie misst die Zusage nur, dass irgendwo kein Lock ist.

        Die vierte Zeile ist die stille Falle: wer die Variable hart setzt, zieht einem
        Aufrufer den Index unter den Fuessen weg, der bewusst einen eigenen benutzt.
      erwartet: "fuenf Zusagen, davon eine ROTE"

  - id: K-08
    typ: presence
    kritikalitaet: P1
    aussage: "Der PREIS von Stufe 5 steht im Werkzeug, nicht nur im Blatt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Im Kopf von commit-pruefen.sh steht in Klartext, was der verlegte Index kostet:
        der STAGING-Zustand ueberlebt den Sitzungswechsel nicht; wer `git add`-ed hatte,
        muss erneut stagen. KEINE Arbeit geht verloren - der Arbeitsbaum bleibt.
        Ein Werkzeug, das eine Umgebung veraendert, ohne den Preis zu nennen, ist eine
        Ueberraschung mit Halbwertszeit - dieselbe Klasse wie eine Naeherung ohne Vermerk
        (B10) und wie ein toter Zweig, der aussieht wie eine Pruefung (W-06 K-02).
      erwartet: "der Preis steht im Kopf, in Klartext"

  - id: K-06
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 6 Mutationen: Aufraeumung wieder nach hinten · Sicherheitsregel entfernt
        (jeder Lock wird gezogen) · nur die Groesse geprueft, nicht das Alter · nur das
        Alter, nicht die Groesse · Abbruch ohne Grund · die Nachsorge am Ende entfernt.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    aussage: "KEIN Browsertest - das Tor hat keine Oberflaeche."
    pruefung:
      typ: verfahren
      schritte: |
        Ausdruecklich benannt statt weggelassen. Der Beleg ist K-02 mit seinen zwei roten
        Gegenproben, nicht ein Schirm.
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  Das Tor zieht auch einen LEBENDEN Lock weg und zerstoert einen laufenden Vorgang.
                                                                        -> K-02 rote Zeilen
2  Nur die Groesse wird geprueft - ein frischer 0-Byte-Lock wird gezogen. -> K-02, K-06
3  Nur das Alter wird geprueft - ein alter, voller Lock wird gezogen.    -> K-06
4  Die Aufraeumung wird VERSCHOBEN statt ergaenzt, und nach jedem Commit
   bleibt ein Rest bis zum naechsten Lauf.                              -> K-04
5  Der Abbruch schweigt.                                                -> K-03
6  Auf einem anderen Rechner gibt es das Problem gar nicht.
   OHNE ZUSAGE, mit Grund: die Aufraeumung ist dann wirkungslos, nicht schaedlich -
   `mv` auf eine nicht vorhandene Datei tut nichts. Eine Zusage darueber waere eine
   Zusage ueber eine Umgebung, die dieses Repo nicht kennt.
7  Zwei Rollen fahren das Tor gleichzeitig.
   OHNE ZUSAGE, mit Grund: dann sieht die zweite einen frischen, VOLLEN Lock und bricht
   ab - genau richtig. Das ist keine neue Kante, sondern die Sicherheitsregel bei der Arbeit.
```

## Rückweg und Entdeckung

**Rückweg:** ein Block in einem 65-Zeilen-Skript. **Der Zustand davor ist der heutige** — jeder
räumt von Hand auf, und wer es nicht weiß, verliert seinen Commit.

**Entdeckung:** K-02 zweite Gegenprobe. **Ein Tor, das jeden Lock wegzieht, sieht in jedem Test
grün aus und zerstört irgendwann den laufenden `git`-Vorgang einer anderen Rolle** — und das fällt
erst auf, wenn eine Historie kaputt ist.

## Warum Spur B und nicht A

**Eine Datei, ein Block, kein Datenpfad, kein Schema.** *Der Rückweg ist ein `git revert`, und die
Wirkung ist am ersten Commit danach sichtbar.* **Aber die Zusagen sind vollständig** — Spur B
heißt kürzerer Weg, nicht weniger Beleg.
