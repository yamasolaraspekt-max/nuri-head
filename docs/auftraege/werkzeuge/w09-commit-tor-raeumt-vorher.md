# W-09 — Das Commit-Tor räumt den Lock VORHER weg, nicht nachher

**Spur B** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 03.08. 00:2x*

```yaml
auftrag:
  id: W-09
  strang: werkzeuge
  status: entwurf   # B8 - Planner-Blatt, Gegenleser ist der Pruefer
  gegengelesen_von:
  gegengelesen_am:
  befund:
```

## Warum — 855 Locks und elf Stunden unverbuchte Arbeit haben dieselbe Ursache

**Gemessen 03.08. 00:2x:**

```text
ls .git/_locks_beiseite/*/ | grep -c lock                     ->  855
davon heute                                                   ->   40

Alter der fertig gebauten, unverbuchten Arbeit:
  resources/…/__tests__/decke.test.ts      02.08. 13:22   ->  11 Stunden
  scripts/auftrag-pruefen.mjs              02.08. 15:28   ->   9 Stunden
```

**Regel B sagt: keine Arbeit liegt länger als zwanzig Minuten uncommittet. Sie ist um das
Dreißigfache gerissen** — und der Generator meldet vier fertige Scheiben im Baum, die er nicht
loswird.

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
      erwartet: "hoechstens 4 - der erste git-Aufruf steht in Zeile 5"
    ausgangswert: "63 (gemessen 03.08. 00:2x; der erste git-Aufruf steht in Zeile 5, die Aufraeumung also 58 Zeilen ZU SPAET)"
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
      erwartet: "0 fail. Ausgangswert 91 pass / 0 fail (Generator, 03.08. nach W-06)."

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
