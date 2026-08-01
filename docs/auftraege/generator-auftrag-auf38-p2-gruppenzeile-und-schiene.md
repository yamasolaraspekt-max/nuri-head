# AUF-38-P2 — Inline-Stile aus `GruppenzeileUndSchiene.tsx` in die Stilschicht

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 10:20*

## Bestand — gemessen 01.08. 10:14, bevor ein Kriterium formuliert wurde

```text
Datei          app/rahmen/GruppenzeileUndSchiene.tsx        433 Zeilen
Bauteile       ArbeitsbereichZeilen (Z170) · PlanerSchiene (Z245)
Stilschicht    resources/planner/hausplaner/hausplaner.css  603 Zeilen, 238 hp-Klassen
               public/hausplaner/hausplaner.css ist die gebaute Fassung, ebenfalls 238

DIE FUEHRENDE ZAHL - mit dem Messwerkzeug, nicht mit grep:

  node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/rahmen/GruppenzeileUndSchiene.tsx | tail -1
  ->  27 Stellen insgesamt, davon 18 offen.

  node scripts/statische-inline-stile.mjs | tail -1
  ->  158 Stellen insgesamt, davon 40 offen.     (diese Datei traegt 18 davon, also 45 %)

Die 18 offenen Stellen, nach Zeile:
  Z102 Z128 Z130          Optionszeile oberhalb von ArbeitsbereichZeilen
  Z187                    ArbeitsbereichZeilen
  Z291 Z298 Z304 Z305     PlanerSchiene, Kopf
  Z335 Z336 Z337          PlanerSchiene, Reiter
  Z368 Z375 Z376 Z377     PlanerSchiene, Zeilenkoerper
  Z378 Z383 Z427          PlanerSchiene, Fuss

Schon in der Stilschicht, NICHT anfassen:  hp-schiene-kopf · hp-schiene-kopf-reiter

Diese sieben Testdateien lesen die Datei ein:
  gruppenzeileUndSchiene (7 Zusagen) · objektkopf (13) · kontextOptionenLeiste (5)
  fussleistenEhrlich (7) · gesperrtAppWeit (9) · werkzeugEnde (15) · _zerlegteApp (Hilfsdatei)
```

## Die Entscheidung

**Ein Klassenpraefix fuer diese Datei: `hp-gz-`.** Die zwei vorhandenen `hp-schiene-`-Klassen
bleiben, wie sie sind — sie liegen bereits in der Stilschicht, und wer sie umbenennt, bewegt
Stellen, die dieses Blatt nicht misst.

**`style={bezeichner}` ist auch hier NICHT Gegenstand** — dieselbe Begruendung wie in P1: das
Messwerkzeug kennt die Form nicht, und eine Zahl ohne Abnahmebefehl zeigt auf nichts. Bleibt
AUF-38-P3, das zuerst das Werkzeug erweitert.

**Der Stil-Bruecken-Test ist Pflichtteil, nicht Kuer.** *In P1 kamen **sieben von acht**
Mutationen durch — Klasse am falschen Element, zwei vertauscht, Regel ohne Wirkung, Klassenname
ins Leere. Ein Inline-Stil wird von den Bauteil-Zusagen mitgelesen; eine Klasse verlagert die
sichtbare Wahrheit in eine zweite Datei, und **zwischen beiden liegt nichts**, wenn niemand die
Bruecke prueft.* Vorbild: `__tests__/eigenschaftenPanelStil.test.ts` (179 Zeilen).

## Was ich vor dem Schneiden selbst gefahren habe — VORLAGE Regel 9

*Kein Blatt geht raus, bevor jeder Befehl darin einmal gelaufen ist. Auch gegen einen absichtlich
ROTEN Fall.*

```text
K-01  Kopie der Datei nach /tmp, EINE statische Stelle angehaengt:
      unveraendert  ->  27 Stellen insgesamt, davon 18 offen.
      mit Zusatz    ->  28 Stellen insgesamt, davon 19 offen.      = der Befehl kann rot werden
K-02  Filterkette gegen eine erfundene Diffzeile:
      printf '+  color: #ff0000;\n+  background: rgb(1,2,3);\n' | ... | wc -l  ->  2
      gegen den echten Stand  ->  0                                = gruen heute, rot moeglich
```

**Ein Befund am Rande, der P1 betrifft und dort nicht auffiel:** *P1s K-02 mass
`git diff main -- public/hausplaner/hausplaner.css`.* **Diese Datei hat EINE Zeile und
enthaelt null rohe Farbwerte** — der Zaehler dort konnte nie steigen, das Kriterium war gruen,
ohne etwas zu pruefen. **P2 misst deshalb die Quelle**, nicht die gebaute Fassung. *P1 ist
abgenommen; das ist eine Meldung an den Evaluator, keine Ruecknahme.*

**Und eine Warnung zum Zaehler selbst:** die Quell-CSS enthaelt heute **8 rohe Farbwerte, alle in
Kommentaren** (Z260–262, Z393 — sie dokumentieren Farben, die bewusst NICHT in die CSS geholt
wurden). *Der Zaehler unterscheidet Kommentar und Regel nicht.* Wer in einem neuen Kommentar eine
Farbe erwaehnt, macht K-02 rot. Das ist Absicht: dann steht die Farbe im Diff und will erklaert
werden.

## Kriterien

```yaml
scope:
  datei: resources/planner/hausplaner/app/rahmen/GruppenzeileUndSchiene.tsx
  population_command: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/rahmen/GruppenzeileUndSchiene.tsx | tail -1"
  ausschluesse:
    - stelle: "style={bezeichner}"
      grund: "Das Messwerkzeug kennt diese Form nicht; eine Zahl, die kein Abnahmebefehl erreicht, zeigt auf nichts. Eigenes Blatt AUF-38-P3, das zuerst das Werkzeug erweitert."
      entschieden_von: planner
    - stelle: "hp-schiene-kopf und hp-schiene-kopf-reiter"
      grund: "Liegen bereits in der Stilschicht. Umbenennen bewegt Stellen, die dieses Blatt nicht misst."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence
    kritikalitaet: P1
    aussage: "In der Datei bleibt keine offene statische Inline-Stelle."
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/rahmen/GruppenzeileUndSchiene.tsx | tail -1"
      erwartet: "9 Stellen insgesamt, davon 0 offen."
    ausgangswert: "27 Stellen insgesamt, davon 18 offen (gemessen 01.08. 10:14)"
    gegenbeweis: |
      Die Null allein ist erreichbar, indem man Stellen dynamisch macht statt sie umzustellen.
      Deshalb zaehlt der Gesamtwert mit: 27 minus die 18 offenen ergibt 9. Steht dort mehr als 9,
      wurde eine statische Stelle in eine dynamische verwandelt statt in die Stilschicht gehoben.
      Steht dort weniger, ist Inhalt verschwunden.
      Rotprobe des Planners: Kopie plus eine statische Stelle -> 28 / 19 offen.

  - id: K-01b
    typ: coverage
    aussage: "Ausserhalb dieser Datei hat sich nichts bewegt."
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs | tail -1"
      erwartet: "140 Stellen insgesamt, davon 22 offen."
    ausgangswert: "158 Stellen insgesamt, davon 40 offen"
    gegenbeweis: |
      158 minus 18 ergibt 140, 40 minus 18 ergibt 22. Weniger offen heisst: ausserhalb des
      Auftrags mitgeraeumt - kein Bonus, sondern eine ungeprueft geaenderte Datei. Mehr heisst:
      eine Stelle ist in eine Nachbardatei gewandert.

  - id: K-02
    typ: absence
    aussage: "Kein roher Farbwert in den neuen CSS-Zeilen."
    pruefung:
      befehl: "git diff main -- resources/planner/hausplaner/hausplaner.css | grep '^+' | grep -oE '#[0-9a-fA-F]{3,6}|rgb[(]' | wc -l"
      erwartet: "0"
    ausgangswert: "0 (gemessen 01.08. 10:16)"
    gegenbeweis: |
      Gemessen wird gegen main, nicht gegen den Arbeitsbaum - sonst waere der Befehl nach dem
      Commit blind. Gemessen wird die QUELLE, nicht public/: die gebaute Fassung hat eine Zeile
      und null Rohfarben, dort konnte der Zaehler nie steigen.
      Rotprobe des Planners: dieselbe Filterkette gegen zwei erfundene Diffzeilen -> 2.
      Achtung: der Zaehler zaehlt auch Kommentare. Acht Rohwerte stehen heute in Kommentaren
      der Quell-CSS und sind Bestand, kein Befund.

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Stil-Bruecken-Test existiert und ist gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=gruppenzeileUndSchieneStil"
      erwartet: |
        gruen. Neue Datei __tests__/gruppenzeileUndSchieneStil.test.ts nach dem Vorbild von
        eigenschaftenPanelStil.test.ts. Sie prueft drei Dinge:
        1. jede benutzte hp-gz-Klasse ist in hausplaner.css definiert - und keine Regel ohne Nutzer
        2. jede Klasse traegt die Eigenschaften, die sie ersetzt hat, Zeile fuer Zeile aus dem
           Stand VOR dem Umbau
        3. jede Klasse sitzt an ihrem Element - Optionszeile, Schienenkopf, Reiter, Zeilenkoerper, Fuss
    begruendung: "Gate - der Planner faehrt keine npm-Laeufe; der Bauende fuehrt sie aus und legt die Rohausgabe bei."

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die sieben Testdateien, die diese Datei einlesen, bleiben gruen und behalten ihre Zusagenzahl."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: |
        gruen, 0 fail. Die Zahlen der sechs Zusagendateien unveraendert:
        gruppenzeileUndSchiene 7 · objektkopf 13 · kontextOptionenLeiste 5 ·
        fussleistenEhrlich 7 · gesperrtAppWeit 9 · werkzeugEnde 15
    begruendung: "Statisch vom Planner gezaehlt (Zahl der test-Bloecke). Weicht die Laufzahl ab, sagt der Bauende, warum - eine hoehere Zahl ist genauso erklaerungsbeduerftig wie eine niedrigere."

  - id: K-05
    typ: behavioural
    aussage: "Die Verriegelung der Stilschicht bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=stilschicht"
      erwartet: "gruen, Zusagen unveraendert. Besonders die Zusage, dass in der Quelle kein @media und kein !important steht - Responsive ist L7, nicht dieses Blatt."

  - id: K-06
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Tests."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 8 Mutationen an genau dem, was der Umbau anfasst: Klasse am falschen Element,
        zwei Klassen vertauscht, Regel ohne Wirkung, Klassenname mit Tippfehler, Abstand still
        veraendert. Wie viele kommen durch?
    gegenbeweis: |
      In P1 waren es 7 von 8, und genau daraus ist der Bruecken-Test entstanden. Die Zahl gehoert
      in den Bericht, auch wenn sie 0 ist. Kommt wieder mehr als die Haelfte durch, ist der
      Bruecken-Test zu duenn und wird nachgezogen, bevor abgenommen wird.

  - id: L-01
    typ: presence
    aussage: "Browsertest gefahren, an http://ticket.test."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, Expertenmodus.
        Werkzeug waehlen, Optionszeile zeigt ihre Felder; Arbeitsbereich wechseln;
        Schiene auf- und zuklappen, Reiter wechseln.
        getBoundingClientRect von Optionszeile, Schienenkopf und erstem Reiter vor und nach dem
        Umbau - die Werte muessen gleich sein.
        Drei Pflicht-Viewports: 1440, 1024, 375.
    gegenbeweis: |
      Aendere EINE Klasse absichtlich, etwa das padding des Schienenkopfs, und miss erneut.
      Bleiben die Werte gleich, misst die Probe nicht, was sie zu messen behauptet.

  - id: L-01-anker
    typ: presence
    aussage: "Die Seite ist ueberhaupt da, bevor irgendeine Zahl abgelesen wird."
    pruefung:
      typ: browser
      schritte: |
        VOR jeder anderen Zahl: HTTP 200, querySelectorAll('canvas') mindestens 1,
        document.title enthaelt "Hausplaner". Auch melden, wenn alles gut aussah.
```

## Rueckweg und Entdeckung

**Rueckweg:** eine reine Frontend-Scheibe ohne Datenpfad und ohne Migration — der Commit laesst
sich zurueckdrehen, ohne dass Daten nachziehen muessen. *Ausserhalb der Maschine liegt der Stand
erst, wenn Yama gepusht hat; bis dahin ist der Rechner die einzige Wahrheit.*

**Entdeckung:** faellt etwas um, faellt es sichtbar um — ein ungestyltes Element in Optionszeile
oder Schiene. Der Bruecken-Test ist genau dafuer da, es VOR dem Browser zu sehen.

## Danach — gemessen 01.08. 10:22, nicht aus der alten Liste abgeschrieben

```text
FussUndUeberlagerungen.tsx    20 Stellen, 12 offen
Kopfrahmen.tsx                16 Stellen,  9 offen
HausplanerApp.tsx              4 Stellen,  1 offen   (Z1041)
                              ------------------
nach P2 offen                              22        = genau der Erwartungswert von K-01b
```

**P3** `FussUndUeberlagerungen` (12) · **P4** `Kopfrahmen` (9) · **P5** `HausplanerApp` (1) ·
danach **P6** `style={bezeichner}` — dort zuerst das Messwerkzeug erweitern, dann messen, dann
umstellen.

*Beim Schreiben dieses Blattes stand hier zuerst: „`FussUndUeberlagerungen` ist mit AUF-48-S8
gebaut und traegt keine offenen Stellen mehr." **Das war falsch** — die Datei hat 12 offene. Ich
habe es beim Nachmessen selbst gefunden, bevor das Blatt rausging. Genau dafuer ist Regel 9 da.*
