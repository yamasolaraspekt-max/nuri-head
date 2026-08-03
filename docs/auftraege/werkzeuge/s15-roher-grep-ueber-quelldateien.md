# S-15 — Ein Kriterium, das mit rohem `grep` über Code misst, wird gemeldet

**Spur B** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 03.08. 02:0x — nach dem VIERTEN Kommentar-Treffer derselben Woche*

```yaml
auftrag:
  id: S-15
  strang: werkzeuge
  status: entwurf   # B8 - Planner-Blatt, Gegenleser ist der Pruefer
  gegengelesen_von: evaluator   # Werkzeug-Blatt -> Evaluator; die vier Kommentar-Treffer waren meine Funde
  gegengelesen_am: 2026-08-03
  befund: >
    TRAEGT MIT EINER AUFLAGE: der sechste K-02-Fall ist mechanisch kaputt -
    "grep -o 'x' scripts/y.mjs --mit-kommentaren" ist keine grep-Option; der Befehl
    selbst wuerde im Runner scheitern, bevor S-15 ihn beurteilen kann. Der legitime
    Kommentare-mitzaehlen-Weg existiert schon als zaehle.mjs --mit-kommentaren und ist
    ueber Fall 3 (zaehle-Aufrufe still) bereits gedeckt - Fall 6 streichen oder auf
    diese Form umschreiben. Sonst exakt: K-01-Ausgangswert 0/Partner 3 an HEAD
    bestaetigt; Bestand plausibilisiert ueber die Strang-Ordner (19-21 grep-ueber-Code
    je nach Zaehlweise, grep -n exakt 3, zaehle exakt 10 - Groessenordnung traegt);
    K-06-Basis 115 ist an heutigem HEAD 118 (Drift durch die Bauten seit 01:1x, nur
    Zahl nachziehen). Melden-statt-sperren, Ersatzbefehl in der Meldung und die vier
    Stille-Zusagen sind genau richtig - die Klasse waren meine vier Funde, die Mechanik
    dagegen gehoert gebaut. B8-Fragen: Befehle laufen, K-02 misst Wirkung mit Stille-
    Zusagen, kein maschineller Befehl mutiert.
```

## Warum — vier Treffer, viermal umgangen, kein einziges Mal beseitigt

**Yamas Frage: „warum ist die Klasse nicht erledigt?" Die Antwort ist unbequem: weil ich jedes Mal
den EINZELFALL umgehe statt die Ursache.**

```text
02.08.  toolRegistry.ts:268       "PAKET_WERKZEUGE" im Kommentar mitgezaehlt   Evaluator fand es
02.08.  breiten.test.ts:51        Beleg zeigte auf einen Kommentar             Evaluator fand es
03.08.  commit-pruefen.sh:5       K-01 mass "git commit" im Kopfkommentar      Evaluator fand es
03.08.  commit-pruefen.sh:5       zweiter Versuch, derselbe Kommentar          Planner fand es
```

**Viermal die Reaktion: einen Filter in das eine Kriterium einbauen.** *Das ist Stufe 1 — ein
Urteil im Einzelfall. Es hat viermal funktioniert und die Klasse kein einziges Mal berührt.*

**Und die Mechanik dagegen liegt seit dem 01.08. im Haus:**

```text
scripts/zaehle.mjs:41   export function ohneKommentare(text, { raute = false })
```

*`zaehle.mjs` zählt kommentarfrei und maskiert Zeichenketten. Es ist gebaut, abgenommen, benutzt —
**aber nur in 10 von 31 Kriterien**, die über Quelldateien messen.*

## Gemessen — der Bestand, bevor entschieden wurde

```text
Kriterien mit rohem `grep` ueber .ts/.tsx/.mjs/.js/.sh   ->  21
davon mit `grep -n` (Zeilennummer, der teuerste Fall)    ->   3
Kriterien, die `zaehle.mjs` benutzen                     ->  10
```

**21 offene Stellen derselben Klasse.** *Jede einzelne kann morgen einen Kommentar treffen und
einen fertigen Bau als unfertig melden — oder, schlimmer, einen unfertigen als fertig.*

## Die Entscheidung — melden, nicht sperren

```text
S-15  Ein `pruefung.befehl`, der `grep` auf eine Datei mit Code-Endung anwendet
      (.ts .tsx .mjs .js .sh) und NICHT ueber `zaehle.mjs` geht, wird GEMELDET.

      ── STRUKTUR S-15  3 Kriterien messen Code mit rohem grep
                        w07/K-03 · w09/K-01 · z11/K-04
                        -> `node scripts/zaehle.mjs <datei> <muster>` zaehlt kommentarfrei
```

**Warum melden und nicht sperren — dieselbe Begründung wie bei S-14:** *es gibt legitime Fälle.*
**Ein `grep` auf eine `.md`-Datei ist kein Fehler, ein `grep` auf ein Verzeichnis auch nicht, und
manchmal WILL man den Kommentar mitzählen** (`zaehle.mjs --mit-kommentaren` gibt es dafür).
*Eine Sperre erzwänge Umgehungen, und Umgehungen sind unsichtbar.*

**Die Meldung nennt Blatt UND Kriterium UND den Ersatzbefehl** — *ohne den dritten Teil ist sie
eine Rüge statt einer Hilfe.*

## Was S-15 NICHT kann, und das gehört gesagt

```text
`grep -n` fuer ZEILENNUMMERN loest zaehle.mjs nicht ab - es zaehlt VORKOMMEN.
```

**Für den teuersten Fall — „an welcher Zeile steht X" — fehlt das Werkzeug noch.** *Alle drei
`grep -n`-Kriterien im Bestand messen eine Position, und für die gibt es heute keinen
kommentarfreien Ersatz.* **S-15 meldet sie trotzdem, denn die Meldung ist der erste Schritt: sie
macht sichtbar, dass drei Kriterien auf etwas messen, das niemand kommentarfrei messen kann.**

*Ob daraus ein `zaehle.mjs --zeile` wird, entscheidet, wer die Meldung zum dritten Mal liest —
B11.*

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/auftrag-pruefen.mjs                 der S-15-Block bei den Strukturbefunden
  scripts/__tests__/auftragPruefen.test.mjs   die Zusagen dazu

Hier bewusst NICHT:
  Die 21 Kriterien nachziehen    S-15 MELDET sie. Wer sie umstellt, entscheidet je Blatt -
                                 einige wollen Kommentare mitzaehlen, und das ist zulaessig.
  zaehle.mjs erweitern           `--zeile` waere die Antwort auf den grep -n-Fall und ist
                                 eine eigene Entscheidung mit eigenem Rueckweg.
  Der Exitcode                   Melden, nicht sperren. Begruendung oben.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/auftrag-pruefen.mjs
  population_command: "grep -o 'S-14' scripts/auftrag-pruefen.mjs | wc -l"
  ausschluesse:
    - stelle: "Die 21 Kriterien nachziehen"
      grund: "S-15 meldet sie. Einige wollen Kommentare mitzaehlen - das ist zulaessig und je Blatt zu entscheiden."
      entschieden_von: planner
    - stelle: "zaehle.mjs um --zeile erweitern"
      grund: "Waere die Antwort auf den grep -n-Fall, ist aber eine eigene Entscheidung mit eigenem Rueckweg."
      entschieden_von: planner
    - stelle: "Der Exitcode"
      grund: "Melden, nicht sperren - eine Sperre erzwaenge Umgehungen, und Umgehungen sind unsichtbar (F-02)."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "S-15 steht im Validator."
    pruefung:
      befehl: "grep -o 'S-15' scripts/auftrag-pruefen.mjs | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 03.08. 02:0x; Partner 'S-14' -> 3, die Messung ist nicht leer)"

  - id: K-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE WIRKUNG: roher grep auf CODE wird gemeldet, auf anderes nicht."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Entscheidungsfunktion:
          befehl "grep -o 'x' scripts/y.mjs | wc -l"              -> GEMELDET
          befehl "grep -o 'x' resources/../a.tsx | wc -l"         -> GEMELDET
          befehl "node scripts/zaehle.mjs scripts/y.mjs 'x'"      -> still
          befehl "grep -o 'x' docs/auftraege/blatt.md | wc -l"    -> still   (.md ist kein Code)
          befehl "grep -rl 'x' resources/ | wc -l"                -> still   (Verzeichnis)
          befehl "grep -o 'x' scripts/y.mjs --mit-kommentaren"    -> still   (Absicht erklaert)
        Die letzten drei sind die tragenden Zusagen: eine Meldung, die auch bei
        legitimen Faellen kommt, wird nach drei Tagen ueberlesen - dann ist sie
        schlimmer als keine.
      erwartet: "sechs Zusagen, davon zwei Meldungen und vier Stille"

  - id: K-03
    typ: presence
    kritikalitaet: P1
    aussage: "Die Meldung nennt den ERSATZBEFEHL, nicht nur den Vorwurf."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Die Meldung enthaelt: Blattname · Kriterium-Id · den fertigen Ersatz
          `node scripts/zaehle.mjs <datei> <muster>`
        Eine Meldung ohne Ersatz ist eine Ruege. Dieselbe Auflage wie bei der
        Erlaubnisliste in W-01 ("die Meldung nennt den PFAD, nicht nur `bash`")
        und bei `verlangeFreigabe` in Z-06-N1.
      erwartet: "drei Teile in der Meldung"

  - id: K-04
    typ: absence
    kritikalitaet: P1
    aussage: "S-15 setzt den Exitcode NICHT."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Ein Lauf mit rohem grep und sonst sauberem Blatt -> Exitcode 0, Meldung vorhanden.
        Wer hier sperrt, bekommt Umgehungen statt Umstellungen - und eine Umgehung
        sieht man nicht.
      erwartet: "Exitcode 0 trotz Meldung"

  - id: K-05
    typ: behavioural
    aussage: "Der heutige Bestand wird korrekt gefunden."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Vom Planner vorgemessen (03.08. 02:0x, ueber docs/auftraege/*/*.md):
          Kriterien mit rohem grep ueber Code   ->  21
          davon mit `grep -n`                   ->   3
          Kriterien mit zaehle.mjs              ->  10
        S-15 findet dieselbe Groessenordnung. Weicht es stark ab, misst es etwas anderes
        als gemeint - und die Zahl ist der einzige Anhalt, den es dafuer gibt.
      erwartet: "in derselben Groessenordnung, Abweichung benannt"

  - id: K-06
    typ: behavioural
    aussage: "Die Werkzeug-Suite bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node --test scripts/__tests__/*.mjs"
      erwartet: "0 fail. Ausgangswert 115 pass / 0 fail (Planner, 03.08. 01:1x an HEAD gemessen)."

  - id: K-07
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 6 Mutationen: auch `.md` wird gemeldet · Verzeichnis-greps werden gemeldet ·
        zaehle.mjs-Aufrufe werden gemeldet · Meldung ohne Ersatzbefehl · Exitcode gesetzt ·
        `--mit-kommentaren` wird ignoriert. Wie viele kommen durch?

  - id: L-01
    typ: presence
    aussage: "KEIN Browsertest - der Validator hat keine Oberflaeche."
    pruefung:
      typ: verfahren
      schritte: |
        Ausdruecklich benannt statt weggelassen. Der Beleg sind die vier Stille-Zusagen
        aus K-02.
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  Auch `.md`-greps werden gemeldet - dann rauscht es.                 -> K-02, K-07
2  Verzeichnis-greps werden gemeldet, obwohl dort keine Zeile zaehlt.  -> K-02
3  `zaehle.mjs`-Aufrufe werden mitgemeldet.                            -> K-02
4  Die Meldung ist ein Vorwurf ohne Ersatz.                            -> K-03
5  S-15 sperrt und erzeugt Umgehungen.                                 -> K-04
6  `grep -n` fuer Zeilennummern hat KEINEN kommentarfreien Ersatz.
   OHNE ZUSAGE, mit Grund: das ist wahr und steht im Blatt. S-15 meldet die drei Faelle
   trotzdem - die Meldung IST der Punkt, weil sie sichtbar macht, dass drei Kriterien auf
   etwas messen, das niemand kommentarfrei messen kann. Ob daraus `zaehle.mjs --zeile`
   wird, entscheidet, wer die Meldung zum dritten Mal liest (B11).
7  Jemand schreibt `--mit-kommentaren` dazu, nur um die Meldung loszuwerden.
   OHNE ZUSAGE, mit Grund: dagegen hilft keine Mechanik, sondern das Gegenlesen. S-15
   macht die Wahl SICHTBAR; wer sie falsch trifft, tut es ab jetzt schriftlich.
   Dieselbe Begruendung wie bei S-13, und aus demselben Grund.
```

## Rückweg und Entdeckung

**Rückweg:** ein Block im Validator. **Der Zustand davor ist der heutige** — 21 Kriterien messen
Code mit rohem `grep`, und alle paar Tage trifft eines einen Kommentar.

**Entdeckung:** K-02, die vier Stille-Zusagen. **Eine Meldung, die auch bei legitimen Fällen
kommt, wird nach drei Tagen überlesen** — und dann ist sie schlimmer als keine, weil alle glauben,
es werde geschaut. *Dieselbe Falle wie bei S-14, und deshalb steht sie hier zweimal.*
