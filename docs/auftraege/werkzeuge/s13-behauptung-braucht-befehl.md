# S-13 — Eine Behauptung über eine Tatsache braucht den Befehl daneben

**Spur B** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 03.08. — nach zehn eigenen Fehlern derselben Klasse an einem Tag*

```yaml
auftrag:
  id: S-13
  strang: werkzeuge
  status: entwurf   # B8 - Planner-Blatt, Gegenleser ist der Pruefer
  gegengelesen_von:
  gegengelesen_am:
  befund:
```

## Warum — zehn Fehler an einem Tag, und alle zehn sind EINE Klasse

**Gezählt am 03.08., Fehler des Planners, jeder von einer anderen Rolle gefunden:**

```text
F-04  "vier Stellen"                        drei waren schon gefuellt      Planner selbst
F-04  "60 Commits ungepusht"                waren 13                       Evaluator
F-04  "ein Parser zoege eine Abhaengigkeit" typescript lag laengst da      Generator
W-08  Blattnamen-Ausnahme                   Zitat traf zwei Stunden spaeter Generator
W-08  zweite Ausnahme                       Zitat in FEHLERKLASSEN.md      Generator
B     "20 Minuten uncommittet"              steht gegen CLAUDE.md          Generator
B11   Beleg nannte den Lock                 Ursache war der Regelkonflikt  Generator
W-09  "der Generator kommt nicht durchs Tor" er hat es nullmal gerufen     Generator
K-07  `node --test <verzeichnis>`           laeuft auf Node 22 gar nicht   Planner selbst
K-01  `awk`-Kriterium                       nach der eigenen Sperre        Validator
```

**Alle zehn haben dieselbe Form: eine TATSACHE behauptet, ohne den Befehl daneben, der sie
misst.** *Und alle zehn stehen an Stellen, an denen der Validator nicht hinschaut — Prosa,
Ausschlussgründe, Belege, Ursachenzuschreibungen.*

**Der teuerste war der über eine andere Rolle:** *„der Generator kommt nicht durch das Tor" —
sechzehn Stunden Wartezeit, und er hatte es nullmal gerufen.*

## Was S-08 kann und was nicht

```text
S-08 prueft `ausgangswert` gegen die frische Messung      ->  hat mehrfach getroffen
S-08 prueft NICHT: Fliesstext · ausschluesse[].grund · Belege · Aussagen ueber Rollen
```

**Genau dort sind alle zehn passiert.** *R11 und S-08 decken die Stelle ab, an der schon einmal
jemand hingesehen hat — nicht die, an der niemand hinsieht.*

## Die Entscheidung — zwei Felder, und der Unterschied ist der ganze Punkt

```text
ausschluesse[]:
  - stelle:  …
    grund:   …
    art:     entscheidung | tatsache          NEU, Pflichtfeld
    beleg:   "<befehl>"                        Pflicht, wenn art = tatsache
```

**„Wir wollen das hier nicht" braucht keinen Beleg. „Das geht nicht" schon.**

*Mein W-06-Ausschluss war als Entscheidung formuliert und war in Wahrheit eine
Tatsachenbehauptung — genau diese Verwechslung hätte ein Pflichtfeld sichtbar gemacht.*

**Und die zweite Hälfte, die den teuersten Fall fängt:**

```text
Ein Ledger-Block oder Blatt, das eine ANDERE ROLLE als Ursache nennt, traegt den
BEFEHL, der ihre Handlung misst - oder er nennt sie nicht.
```

*Für „der Generator kommt nicht durch das Tor" wäre das gewesen:* **wie oft hat er es gerufen?**
*Die Antwort war null, und sie war in einer Zeile messbar.*

## Nahtstellen

```text
Hier wird geschrieben:
  scripts/auftrag-pruefen.mjs               S-13 bei den Strukturbefunden
  docs/auftraege/AUFTRAGSSCHEMA.md          die zwei Felder im Schema
  scripts/__tests__/auftragPruefen.test.mjs die Zusagen dazu

Hier bewusst NICHT:
  Fliesstext maschinell pruefen    Ein Werkzeug, das Prosa auf Behauptungen absucht,
                                   ist ein Sprachmodell und kein Validator. Die
                                   Rollen-Haelfte oben ist eine REGEL (B13) und
                                   bleibt eine - aber mit einem benannten Traeger.
  Bestandsblaetter nachruesten     35 Blaetter haben `ausschluesse` ohne `art`.
                                   S-13 greift nur bei aktiv/bereit/gebaut/entwurf -
                                   dieselbe Naht wie S-11, aus demselben Grund.
```

## Kriterien

```yaml
scope:
  dateien:
    - scripts/auftrag-pruefen.mjs
    - docs/auftraege/AUFTRAGSSCHEMA.md
  population_command: "grep -ro 'ausschluesse' docs/auftraege/ | wc -l"
  ausschluesse:
    - stelle: "Fliesstext maschinell pruefen"
      grund: "Ein Werkzeug, das Prosa auf Behauptungen absucht, ist ein Sprachmodell und kein Validator."
      entschieden_von: planner
    - stelle: "Bestandsblaetter nachruesten"
      grund: "S-13 greift nur bei aktiv/bereit/gebaut/entwurf - dieselbe Naht wie S-11, aus demselben Grund: die Sperre sitzt am Uebergang."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "S-13 steht im Validator."
    pruefung:
      befehl: "grep -o 'S-13' scripts/auftrag-pruefen.mjs | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 03.08.; Partner 'S-06' -> 3, die Messung ist nicht leer)"

  - id: K-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE WIRKUNG: eine TATSACHE ohne Beleg wird gemeldet, eine ENTSCHEIDUNG nicht."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Entscheidungsfunktion:
          art: tatsache    OHNE beleg   -> S-13 meldet
          art: tatsache    MIT  beleg   -> still
          art: entscheidung ohne beleg  -> still
          art fehlt ganz, Blatt aktiv   -> S-13 meldet (Pflichtfeld)
          art fehlt ganz, Blatt ruht    -> still
        Die DRITTE Zeile ist die tragende: wer sie nicht hinbekommt, hat ein Werkzeug
        gebaut, das jeden Ausschluss mit einem Befehl belegt haben will - und dann
        schreibt niemand mehr Ausschluesse, sondern laesst sie weg. Das waere schlimmer
        als heute.
      erwartet: "fuenf Zusagen, davon zwei Meldungen und drei Stille"

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Beleg wird AUSGEFUEHRT, nicht nur gezaehlt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Ein `beleg` ist ein Befehl und laeuft durch dieselbe Erlaubnisliste wie jeder
        `pruefung.befehl` - mit demselben Ergebnis-Bericht.
          ein Beleg, der laeuft            -> OK, Ausgabe im Bericht
          ein Beleg, der nicht laeuft      -> FEHLSCHLAG
          ein Beleg mit verbotenem Glied   -> UEBERSPRUNGEN, wie ueberall
        Ein Beleg, den niemand faehrt, ist eine zweite Behauptung - und dann hat S-13
        das Problem nur verschoben statt geloest.
      erwartet: "drei Zusagen, davon eine ROTE"

  - id: K-04
    typ: absence
    kritikalitaet: P1
    aussage: "S-13 setzt den Exitcode NICHT."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Wie S-14: melden, nicht sperren. Ein fehlendes `art` ist ein Schreibfehler, kein
        Baufehler - wer daran den Lauf abbricht, hindert den Bauenden am Pruefen.
        Ein Lauf mit fehlendem `art` und sonst sauberem Blatt -> Exitcode 0, Meldung da.
      erwartet: "Exitcode 0 trotz Meldung"

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
        Mindestens 6 Mutationen: `art` nicht geprueft · jeder Ausschluss braucht einen
        Beleg (auch Entscheidungen) · Beleg wird gezaehlt statt gefahren · S-13 greift
        auch bei `ruht` · Exitcode gesetzt · Meldung ohne Blattnamen.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    aussage: "KEIN Browsertest - der Validator hat keine Oberflaeche."
    pruefung:
      typ: verfahren
      schritte: |
        Ausdruecklich benannt statt weggelassen. Der Beleg ist K-02 mit seinen fuenf
        Zusagen und K-03 mit der roten.
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  Jeder Ausschluss braucht einen Beleg, auch eine Entscheidung -
   dann schreibt niemand mehr Ausschluesse.                            -> K-02 dritte Zeile
2  Der Beleg wird gezaehlt statt gefahren - eine zweite Behauptung.    -> K-03
3  S-13 sperrt und haelt den Bauenden vom Pruefen ab.                  -> K-04
4  S-13 greift im Archiv und erzwingt einen Massenumbau.               -> K-02, Ausschluss
5  Jemand schreibt `art: entscheidung` ueber eine Tatsache, um den
   Beleg zu sparen.
   OHNE ZUSAGE, mit Grund: dagegen hilft keine Mechanik, sondern nur das Gegenlesen -
   und genau das hat heute zehnmal funktioniert. S-13 macht die Wahl SICHTBAR; wer sie
   falsch trifft, tut es ab jetzt schriftlich und vor Zeugen.
```

## Rückweg und Entdeckung

**Rückweg:** ein Block im Validator, zwei Felder im Schema. **Der Zustand davor ist der heutige** —
Ausschlussgründe sind Prosa, und niemand prüft sie.

**Entdeckung:** K-02 dritte Zeile. **Ein Werkzeug, das jeden Ausschluss belegt haben will, führt
dazu, dass niemand mehr Ausschlüsse schreibt** — und dann verschwindet die Information ganz,
statt falsch zu sein. *Das wäre schlimmer als heute.*
