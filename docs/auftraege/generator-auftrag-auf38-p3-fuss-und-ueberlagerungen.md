# AUF-38-P3 — Inline-Stile aus `FussUndUeberlagerungen.tsx` in die Stilschicht

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 19:1x*

```yaml
auftrag:
  id: AUF-38-P3
  strang: hausplaner-3d
  status: abgenommen   # gebaut bbd4be07 19:21, Votum GRUEN vom Evaluator in fa1da402 19:37 - eingetragen vom Planner, nicht abgenommen vom Planner
```

## Bestand — gemessen 01.08. 19:0x

```text
Datei          app/rahmen/FussUndUeberlagerungen.tsx        208 Zeilen
Stilschicht    resources/planner/hausplaner/hausplaner.css  (P1 und P2 haben sie erweitert)

  node scripts/statische-inline-stile.mjs …/FussUndUeberlagerungen.tsx | tail -1
  ->  20 Stellen insgesamt, davon 12 offen.

  node scripts/statische-inline-stile.mjs | tail -1
  ->  140 Stellen insgesamt, davon 22 offen.     (diese Datei traegt 12 davon, also 55 %)

Schon in der Stilschicht, NICHT anfassen:  hp-kontur-* · hp-pause-*

Diese Testdateien lesen sie ein:
  fussUndUeberlagerungen.test.ts (12 Zusagen) · gesperrtAppWeit.test.ts (9) · _zerlegteApp.ts
```

**Nach P3 bleiben global 10 offene Stellen** — `Kopfrahmen` 9 und `HausplanerApp` 1. *Das ist die
vorletzte Scheibe dieses Programms.*

## Die Entscheidung

**Klassenpraefix `hp-fu-`.** Die vorhandenen `hp-kontur-` und `hp-pause-` bleiben unangetastet —
sie liegen schon in der Stilschicht, und wer sie umbenennt, bewegt Stellen, die dieses Blatt nicht
misst.

**Der Stil-Bruecken-Test ist Pflichtteil.** *In P1 kamen sieben von acht Mutationen durch, in P2
wieder — daraus ist die Klasse **F-15** entstanden. Eine Klasse verlagert die sichtbare Wahrheit in
eine zweite Datei, und zwischen beiden liegt nichts, wenn niemand die Bruecke prueft.*
Vorbild: `eigenschaftenPanelStil.test.ts` (179 Z.) und `gruppenzeileUndSchieneStil.test.ts` (161 Z.).

**`style={bezeichner}` ist auch hier NICHT Gegenstand** — eigenes Blatt, das zuerst das Messwerkzeug
erweitert.

## Kriterien

```yaml
scope:
  datei: resources/planner/hausplaner/app/rahmen/FussUndUeberlagerungen.tsx
  population_command: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/rahmen/FussUndUeberlagerungen.tsx | tail -1"
  ausschluesse:
    - stelle: "style={bezeichner}"
      grund: "Das Messwerkzeug kennt diese Form nicht; eine Zahl, die kein Abnahmebefehl erreicht, zeigt auf nichts."
      entschieden_von: planner
    - stelle: "hp-kontur-* und hp-pause-*"
      grund: "Liegen bereits in der Stilschicht."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence
    kritikalitaet: P1
    aussage: "In der Datei bleibt keine offene statische Inline-Stelle."
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/rahmen/FussUndUeberlagerungen.tsx | tail -1"
      erwartet: "8 Stellen insgesamt, davon 0 offen."
    ausgangswert: "20 Stellen insgesamt, davon 12 offen (gemessen 01.08. 19:0x)"
    gegenbeweis: |
      Die Null allein ist erreichbar, indem man Stellen dynamisch macht statt sie umzustellen.
      Deshalb zaehlt der Gesamtwert mit: 20 minus die 12 offenen ergibt 8. Mehr als 8 heisst,
      eine statische Stelle wurde dynamisch gemacht; weniger heisst, Inhalt ist verschwunden.
      Rotprobe des Planners: Kopie plus eine statische Stelle -> 21 / 13 offen.

  - id: K-01b
    typ: coverage
    aussage: "Ausserhalb dieser Datei hat sich nichts bewegt."
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs | tail -1"
      erwartet: "128 Stellen insgesamt, davon 10 offen."
    ausgangswert: "140 Stellen insgesamt, davon 22 offen"
    gegenbeweis: |
      140-12=128, 22-12=10. Weniger offen heisst: ausserhalb des Auftrags mitgeraeumt - kein
      Bonus, sondern eine ungeprueft geaenderte Datei. Mehr heisst: eine Stelle ist in eine
      Nachbardatei gewandert.

  - id: K-02
    typ: absence
    aussage: "Kein roher Farbwert in den neuen CSS-Zeilen."
    pruefung:
      befehl: "git diff main -- resources/planner/hausplaner/hausplaner.css | grep '^+' | grep -oE '#[0-9a-fA-F]{3,6}|rgb[(]' | wc -l"
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      Gemessen wird gegen main und die QUELLE, nicht die gebaute Fassung - die hat eine Zeile
      und null Rohfarben, dort konnte der Zaehler nie steigen (Befund an P1).
      Achtung: der Zaehler sieht auch Kommentare. Wer eine Farbe in einem neuen Kommentar
      erwaehnt, macht das Kriterium rot - das ist Absicht, dann steht sie im Diff.

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Stil-Bruecken-Test existiert und ist gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=fussUndUeberlagerungenStil"
      erwartet: |
        gruen. Neue Datei __tests__/fussUndUeberlagerungenStil.test.ts nach dem Vorbild der
        beiden vorhandenen Bruecken-Tests. Sie prueft drei Dinge:
        1. jede benutzte hp-fu-Klasse ist in hausplaner.css definiert - und keine Regel ohne Nutzer
        2. jede Klasse traegt die Eigenschaften, die sie ersetzt hat
        3. jede Klasse sitzt an ihrem Element

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die vorhandenen Zusagen bleiben gruen und behalten ihre Zahl."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: |
        0 fail. fussUndUeberlagerungen 12 Zusagen · gesperrtAppWeit 9 - unveraendert.
        Weicht eine Zahl ab, sagt der Bauende warum, nach oben wie nach unten.

  - id: K-05
    typ: behavioural
    aussage: "Die Verriegelung der Stilschicht bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=stilschicht"
      erwartet: "gruen. Besonders die Zusage, dass in der Quelle kein @media und kein !important steht - Responsive ist L7."

  - id: K-06
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Tests."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 8 Mutationen an dem, was der Umbau anfasst: Klasse am falschen Element,
        zwei vertauscht, Regel ohne Wirkung, Klassenname mit Tippfehler, Abstand still veraendert.
        Wie viele kommen durch?
    gegenbeweis: |
      P1: 7 von 8. P2: wieder. Die Zahl gehoert in den Bericht, auch wenn sie 0 ist. Kommt
      mehr als die Haelfte durch, ist der Bruecken-Test zu duenn und wird nachgezogen.

  - id: L-01
    typ: presence
    aussage: "Browsertest gefahren, an http://ticket.test."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, Expertenmodus.
        Fussleiste und Ueberlagerungen zeigen: Statuszeile, Pause-Hinweis, Kontur-Hinweis.
        getBoundingClientRect von Fussleiste und erster Ueberlagerung vor und nach dem
        Umbau - die Werte muessen gleich sein.
        Drei Pflicht-Viewports: 1440, 1024, 375.

  - id: L-01-anker
    typ: presence
    aussage: "Die Seite ist ueberhaupt da, bevor irgendeine Zahl abgelesen wird."
    pruefung:
      typ: browser
      schritte: |
        VOR jeder anderen Zahl: HTTP 200, querySelectorAll('canvas') mindestens 1,
        document.title enthaelt "Hausplaner".
```

## Danach

**P4** `Kopfrahmen` (9 offen) · **P5** `HausplanerApp` (1 offen) — dann ist das Programm zu Ende.
