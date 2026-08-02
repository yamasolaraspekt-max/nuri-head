# S-14 — Unverbuchte Arbeit ist nicht mehr unsichtbar

**Spur B** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 03.08. 00:4x auf Yamas „nie wieder"*

```yaml
auftrag:
  id: S-14
  strang: werkzeuge
  status: entwurf   # B8 - Planner-Blatt, Gegenleser ist der Pruefer
  gegengelesen_von:
  gegengelesen_am:
  befund:
  fachentscheidung: "Yama, 03.08.: 'sorg dafuer dass dieses problem nie wieder passiert'. W-09 macht das Scheitern unmoeglich; S-14 macht das Liegenbleiben SICHTBAR. Ohne das zweite ist 'nie wieder' ein Vorsatz."
```

## Warum W-09 allein nicht reicht

**W-09 räumt den Lock weg, der den Commit blockiert. Das war die Ursache von heute — es ist nicht
die einzige mögliche.** *Arbeit bleibt auch liegen, wenn ein Bau halb fertig ist, wenn eine Rolle
abbricht, wenn jemand schlicht vergisst.* **Und der teure Teil war nie das Scheitern, sondern
dass es elf Stunden lang niemandem aufgefallen ist.**

```text
decke.test.ts        02.08. 13:22  ->  11 Stunden   niemand hat es gemeldet
auftrag-pruefen.mjs  02.08. 15:28  ->   9 Stunden   der Planner hat es zweimal GESEHEN
                                                    und fuer einen Umstand gehalten
```

**Regel B (*„keine Arbeit liegt länger als zwanzig Minuten uncommittet"*) steht seit Tagen im
STAND. Sie ist eine Regel — Stufe 3 — und sie hat um das Dreißigfache nicht getragen.**

## Die Entscheidung

```text
Der Validator meldet am Ende jedes Laufs, was unverbucht im Baum liegt - mit ALTER.

  ── STRUKTUR S-14  3 Datei(en) unverbucht, aelteste seit 11 h
                    resources/…/decke.test.ts            11 h
                    scripts/auftrag-pruefen.mjs           9 h
                    docs/planner/PRUEFER-BEFUNDE.md       2 h
```

**Warum der Validator und kein eigener Wächter:** *er läuft ohnehin bei jedem Blatt, von jeder
Rolle, mehrmals am Tag.* **Ein Wächter, den man starten muss, wird genau dann nicht gestartet,
wenn er gebraucht wird.** *Dieselbe Überlegung wie bei S-11: die Sperre sitzt am Übergang, den
ohnehin jeder passiert.*

## Meldung, nicht Sperre — und das ist eine Entscheidung

```text
S-14 setzt den Exitcode NICHT. Es meldet.
```

**Eine Sperre wäre hier falsch:** *unverbuchte Arbeit ist der Normalzustand während des Bauens.*
**Wer baut, hat Änderungen im Baum — ihn daran zu hindern, den Validator zu fahren, würde ihn
davon abhalten, seine Arbeit zu prüfen.** *Das wäre eine Sperre, die genau das Verhalten
bestraft, das sie fördern soll (F-02: eine Sperre, die mehr sperrt als ihr Grund trägt).*

**Die Zahl allein wirkt, weil sie mit dem ALTER kommt.** *„3 Dateien unverbucht" liest niemand.
„älteste seit 11 h" liest jeder.*

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/auftrag-pruefen.mjs      der S-14-Block bei den anderen Strukturbefunden
  scripts/__tests__/auftragPruefen.test.mjs   die Zusagen dazu

Hier bewusst NICHT:
  Ein eigener Waechter-Prozess     muss gestartet werden - und wird genau dann nicht
                                   gestartet, wenn er gebraucht wird.
  Der Exitcode                     S-14 meldet, es sperrt nicht. Begruendung oben.
  `.gitignore`-Kandidaten          Bauartefakte wie public/hausplaner/hausplaner.js
                                   gehoeren gemeldet wie alles andere. Ob sie ignoriert
                                   werden sollen, ist eine eigene Frage - und sie zu
                                   verstecken waere genau der Fehler, den S-14 abstellt.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/auftrag-pruefen.mjs
  population_command: "grep -o 'S-06' scripts/auftrag-pruefen.mjs | wc -l"
  ausschluesse:
    - stelle: "Ein eigener Waechter-Prozess"
      grund: "Muss gestartet werden und wird genau dann nicht gestartet, wenn er gebraucht wird. Der Validator laeuft ohnehin."
      entschieden_von: planner
    - stelle: "Der Exitcode"
      grund: "Unverbuchte Arbeit ist waehrend des Bauens der Normalzustand. Eine Sperre bestrafte genau das Verhalten, das sie foerdern soll (F-02)."
      entschieden_von: planner
    - stelle: "Bauartefakte verstecken"
      grund: "Sie zu ignorieren waere der Fehler, den S-14 abstellt. Ob sie in .gitignore gehoeren, ist eine eigene Frage."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "S-14 steht im Validator."
    pruefung:
      befehl: "grep -o 'S-14' scripts/auftrag-pruefen.mjs | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 03.08. 00:4x; Partner 'S-06' -> 3, die Messung ist nicht leer)"

  - id: K-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE WIRKUNG: die Meldung nennt ZAHL und ALTER, nicht nur die Zahl."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Entscheidungsfunktion, nicht gegen den Bildschirm:
          drei geaenderte Dateien, die aelteste 11 h alt
            -> die Meldung nennt 3 UND "aelteste seit 11 h"
          eine Datei, 2 Minuten alt
            -> die Meldung nennt sie, aber ohne Dringlichkeit
          KEINE geaenderte Datei
            -> KEINE Meldung. Ein Waechter, der auch bei sauberem Baum spricht,
               wird nach drei Tagen ueberlesen.
        Die dritte Zeile ist die scharfe: sie faellt, wenn jemand die Meldung
        bedingungslos ausgibt.
      erwartet: "drei Zusagen, die dritte ist die tragende"

  - id: K-03
    typ: absence
    kritikalitaet: P1
    aussage: "S-14 setzt den Exitcode NICHT."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Ein Lauf mit unverbuchter Arbeit und sonst sauberen Blaettern
          -> Exitcode 0, Meldung vorhanden
        Wer hier sperrt, hindert den Bauenden daran, seine eigene Arbeit zu pruefen -
        und der committet dann seltener, nicht oefter. Genau das Gegenteil des Zwecks.
      erwartet: "Exitcode 0 trotz Meldung"

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Validator misst den Baum, ohne ihn zu bewegen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Die Messung laeuft mit `git --no-optional-locks status --porcelain` - die Form,
        die die Bauordnung ohnehin vorschreibt. Zusagen:
          der Lauf hinterlaesst KEINEN neuen Lock in .git/
          der Lauf veraendert den Index NICHT
        `git status` ohne `--no-optional-locks` schreibt den Index neu - auf diesem Mount
        heisst das: ein weiterer Lock, der liegen bleibt. Ein Waechter gegen liegengebliebene
        Arbeit, der selbst Locks hinterlaesst, waere eine Pointe, die uns niemand abnimmt.
      erwartet: "zwei Zusagen, beide gegen die Nebenwirkung"

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
        Mindestens 5 Mutationen: Meldung ohne Alter · Meldung auch bei sauberem Baum ·
        Exitcode gesetzt · `--no-optional-locks` entfernt · nur die Zahl der Dateien,
        ohne Namen. Wie viele kommen durch?

  - id: L-01
    typ: presence
    aussage: "KEIN Browsertest - der Validator hat keine Oberflaeche."
    pruefung:
      typ: verfahren
      schritte: |
        Ausdruecklich benannt statt weggelassen. Der Beleg ist K-02 mit der dritten Zusage
        und K-04 mit den zwei Nebenwirkungs-Zusagen.
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  Die Meldung nennt nur die Zahl - "3 Dateien" liest niemand.        -> K-02
2  Sie erscheint auch bei sauberem Baum und wird ueberlesen.           -> K-02 dritte Zeile
3  S-14 sperrt und haelt den Bauenden vom Pruefen ab.                  -> K-03
4  Der Waechter hinterlaesst selbst einen Lock.                        -> K-04
5  Bauartefakte werden versteckt, damit die Meldung kuerzer wird.      -> Ausschluss, K-06
6  Die Meldung wird nach einer Woche ignoriert, weil immer etwas liegt.
   OHNE ZUSAGE, mit Grund: dagegen hilft keine Zusage in diesem Blatt, sondern nur, dass
   die Zahl KLEIN wird - und dafuer sorgt W-09. Die beiden gehoeren zusammen: eines macht
   das Verbuchen moeglich, das andere macht das Liegenbleiben sichtbar. Getrennt traegt
   keines von beiden.
```

## Rückweg und Entdeckung

**Rückweg:** ein Block im Validator. **Der Zustand davor ist der heutige** — Arbeit liegt elf
Stunden und niemand sieht es.

**Entdeckung:** K-02 dritte Zeile. **Ein Wächter, der bei jedem Lauf spricht, auch wenn nichts ist,
wird nach drei Tagen überlesen** — und dann ist er schlimmer als keiner, weil alle glauben, es
werde geschaut.
