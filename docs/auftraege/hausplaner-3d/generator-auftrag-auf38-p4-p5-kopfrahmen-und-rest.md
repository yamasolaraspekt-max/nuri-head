# AUF-38-P4+P5 — die letzten zehn offenen Inline-Stellen

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 19:3x*

```yaml
auftrag:
  id: AUF-38-P4+P5
  strang: hausplaner-3d
  status: abgenommen   # Votum GRUEN vom Evaluator (01.08. 21:3x, fba3083f, Ledger Z. 33409). Eingetragen vom Planner 03.08. - das Votum stand seit VORGESTERN, mein Eintrag fehlte.
```

## Warum P4 und P5 in EINEM Blatt

**P5 ist eine einzige Stelle.** `HausplanerApp.tsx` trägt noch **4 Stellen, davon 1 offen** (Z1127).
*Ein eigenes Blatt für eine Zeile kostet mehr Papier, als es Ordnung schafft* — und der Prüfer hält
uns zu Recht ein Verhältnis von 7:1 zwischen Doku und Code vor.

**Sie werden trotzdem getrennt gemessen**, damit eine rote Abnahme sagt, welche Hälfte schuld ist.

## Bestand — gemessen 01.08. 19:3x

```text
P4  app/dashboard/Kopfrahmen.tsx     330 Zeilen   16 Stellen, davon  9 offen
P5  app/HausplanerApp.tsx                          4 Stellen, davon  1 offen  (Z1127)
                                                  --------------------------
global                                           140 Stellen, davon 22 offen
minus P3 (12, eigenes Blatt) und diese 10        ->  0 offen, wenn beide durch sind

Schon in der Stilschicht, NICHT anfassen:  hp-az-* · hp-ok-*

Diese Zusagen lesen `Kopfrahmen.tsx` ein:
  objektkopf 13 · reiterLeisteGeteilt 9 · kopfrahmen 9 · opKnopfZustand 9
  fussUndUeberlagerungen 12 · gesperrtAppWeit 9 · _zerlegteApp
```

**Mit P3, P4 und P5 ist das Programm AUF-38 zu Ende** — von 331 Inline-Stellen in 35 Dateien, mit
denen es am 25.07. begann, bleiben null offene statische.

## Die Entscheidung

**Klassenpräfix `hp-kr-` für den Kopfrahmen.** Die vorhandenen `hp-az-` und `hp-ok-` bleiben.
**Für die eine Stelle in `HausplanerApp.tsx` gilt das Präfix der Fläche, an der sie sitzt** — der
Bauende nennt es im Bericht, statt ein achtes Präfix für eine Zeile zu erfinden.

**Stil-Brücken-Test ist Pflichtteil** (F-15). In P1 und P2 kamen je 7 von 8 Mutationen durch.

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/app/dashboard/Kopfrahmen.tsx
    - resources/planner/hausplaner/app/HausplanerApp.tsx
  population_command: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/dashboard/Kopfrahmen.tsx | tail -1"
  ausschluesse:
    - stelle: "style={bezeichner}"
      grund: "Das Messwerkzeug kennt diese Form nicht; eine Zahl ohne Abnahmebefehl zeigt auf nichts."
      entschieden_von: planner
    - stelle: "hp-az-* und hp-ok-*"
      grund: "Liegen bereits in der Stilschicht."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence
    kritikalitaet: P1
    aussage: "P4 - im Kopfrahmen bleibt keine offene statische Inline-Stelle."
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/dashboard/Kopfrahmen.tsx | tail -1"
      erwartet: "7 Stellen insgesamt, davon 0 offen."
    ausgangswert: "16 Stellen insgesamt, davon 9 offen (gemessen 01.08. 19:3x)"
    gegenbeweis: |
      16 minus die 9 offenen ergibt 7. Mehr heisst, eine statische Stelle wurde dynamisch
      gemacht statt in die Stilschicht gehoben; weniger heisst, Inhalt ist verschwunden.
      Rotprobe des Planners: Kopie plus eine statische Stelle -> 17 / 10 offen.

  - id: K-02
    typ: absence
    kritikalitaet: P1
    aussage: "P5 - die letzte offene Stelle in HausplanerApp.tsx ist weg."
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/HausplanerApp.tsx | tail -1"
      erwartet: "3 Stellen insgesamt, davon 0 offen."
    ausgangswert: "4 Stellen insgesamt, davon 1 offen (Z1127)"
    gegenbeweis: |
      4 minus die eine offene ergibt 3. Rotprobe des Planners: Kopie plus eine Stelle -> 5 / 2.
      Getrennt gemessen von K-01, damit eine rote Abnahme sagt, welche Haelfte schuld ist.

  - id: K-03
    typ: coverage
    aussage: "Ausserhalb dieser beiden Dateien hat sich nichts bewegt."
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs | tail -1"
      erwartet: "118 Stellen insgesamt, davon 0 offen."
    ausgangswert: "128 Stellen insgesamt, davon 10 offen (gemessen 01.08. 19:4x nach AUF-38-P3 bbd4be07 - Kopfrahmen.tsx 9, HausplanerApp.tsx 1). Das Blatt trug 140/22 aus der Zeit VOR P3; S-08 hat die Drift gemeldet, der Fehler war meiner."
    gegenbeweis: |
      128-10=118. Die zwoelf Stellen von AUF-38-P3 sind seit bbd4be07 heraus - daher 128 und
      nicht mehr 140. Steht dort MEHR als 118, hat sich eine dritte Datei bewegt; steht dort
      WENIGER, wurde ausserhalb dieses Blattes mitgeraeumt. Beides ist rot, nicht Bonus.

  - id: K-04
    typ: absence
    aussage: "Kein roher Farbwert in den neuen CSS-Zeilen."
    pruefung:
      befehl: "git diff main -- resources/planner/hausplaner/hausplaner.css | grep '^+' | grep -oE '#[0-9a-fA-F]{3,6}|rgb[(]' | wc -l"
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      Gemessen gegen main und gegen die QUELLE. Der Zaehler sieht auch Kommentare - wer eine
      Farbe in einem neuen Kommentar erwaehnt, macht das Kriterium rot. Das ist Absicht.

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Stil-Bruecken-Test existiert und ist gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=kopfrahmenStil"
      erwartet: |
        gruen. Neue Datei __tests__/kopfrahmenStil.test.ts nach dem Vorbild der drei
        vorhandenen Bruecken-Tests: benutzt-gleich-definiert, Regel traegt die ersetzten
        Eigenschaften, Klasse sitzt am richtigen Element.

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die sieben Zusagendateien bleiben gruen und behalten ihre Zahl."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: |
        0 fail. objektkopf 13 · reiterLeisteGeteilt 9 · kopfrahmen 9 · opKnopfZustand 9 ·
        fussUndUeberlagerungen 12 · gesperrtAppWeit 9 - unveraendert.

  - id: K-07
    typ: behavioural
    aussage: "Die Verriegelung der Stilschicht bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=stilschicht"
      erwartet: "gruen, kein @media und kein !important in der Quelle."

  - id: K-08
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Tests."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 8 Mutationen: Klasse am falschen Element, zwei vertauscht, Regel ohne
        Wirkung, Klassenname mit Tippfehler, Abstand still veraendert. Wie viele kommen durch?

  - id: L-01
    typ: presence
    aussage: "Browsertest gefahren, an http://ticket.test."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, Expertenmodus.
        Kopfrahmen mit Objektkopf, Reiterleiste und Knopfzustaenden zeigen.
        getBoundingClientRect von Kopfrahmen, Reiterleiste und erstem Knopf vor und nach
        dem Umbau - gleich. Drei Pflicht-Viewports: 1440, 1024, 375.

  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
  - id: K-09
    typ: behavioural
    aussage: "Die drei Zaehlbefehle wurden EINMAL ohne Pipe gefahren, mit genanntem Exitcode."
    ausgefuehrt_von: generator
    pruefung:
      typ: verfahren
      schritte: |
        K-01, K-02 und K-03 enden auf `| tail -1`. Die Pipe schluckt den Exitcode des Skripts -
        ein Absturz saehe aus wie eine Messung. Einmal je Befehl OHNE die Pipe fahren und den
        Exitcode in den Beleg schreiben. Erwartet 0 bei allen dreien.
      erwartet: "drei Exitcodes im Beleg, alle 0"
```

## Danach

**Das Programm AUF-38 ist zu Ende** — bis auf `style={bezeichner}`, das ein eigenes Blatt bekommt,
sobald das Messwerkzeug diese Form kennt.
