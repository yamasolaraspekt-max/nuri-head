# AUF-38-P1 — Inline-Stile aus `EigenschaftenPanel.tsx` in die Stilschicht

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 30.07. 22:41*

## Warum dieses Blatt „P1" heisst und nicht „Scheibe 7"

**Die alte Scheibe 7 gibt es nicht mehr.** Sie war beschrieben als *„`HausplanerApp.tsx`, 78
Stellen"* und **gesperrt** mit dem gemessenen Grund *„drei Posten in einer 2305-Zeilen-Datei"*.

**AUF-48 hat diesen Grund aufgeloest, nicht erfuellt:**

```text
HausplanerApp.tsx   2511 -> 1130 Zeilen,  von 78 Inline-Stellen bleiben 4
```

*Wer „Scheibe 7" heute zieht, sucht 78 Stellen in einer Datei, die 4 hat.* **Stattdessen ein
Blatt je Datei — und dieses ist das erste, weil es zwei Drittel traegt.**

## Bestand (gemessen, bevor ein Kriterium formuliert wurde)

```text
Datei                      rahmen/EigenschaftenPanel.tsx     551 Zeilen
letzter Commit             15de0857 (30.07. 22:17, AUF-48-S4d)
Tests, die sie einlesen    __tests__/eigenschaftenPanel.test.ts · __tests__/_zerlegteApp.ts

DIE FUEHRENDE ZAHL - gemessen 30.07. 23:15 mit dem Messwerkzeug, nicht mit grep:

  node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx
  ->  71 Stellen insgesamt, davon 37 OFFEN

  node scripts/statische-inline-stile.mjs
  ->  195 Stellen insgesamt, davon 77 offen     (das Panel traegt 37 davon, also 48 %)

Danebenstehend, NICHT Gegenstand dieses Blattes (Begruendung unten):
  style={bezeichner} im Panel      56 (mein grep) / 58 (Messung des Evaluators)
  className=                        2
```

## Die Entscheidung: EINE fuehrende Zahl, und es ist `37 offen von 71`

*Der Evaluator hat am frisch geschnittenen Blatt zu Recht angemerkt, dass zwei Zahlen darin
Verschiedenes meinen: das Blatt plante gegen `129 von 196`, die Abnahme misst `37 offen von 71`.
**Beide sind richtig und beantworten verschiedene Fragen** - „wie viele `style`-Attribute gibt es"
gegen „wie viele davon sind statisch und damit umstellbar".*

**Massgeblich ist, was die Abnahme messen kann.** `scripts/statische-inline-stile.mjs` kennt genau
eine Form: den `style={{…}}`-Block. Eine Zahl im Blatt, die kein Abnahmebefehl erreicht, ist ein
Posten, der auf nichts zeigt.

**Damit ist `style={bezeichner}` NICHT Gegenstand von AUF-38-P1.** Das hebt eine fruehere
Evaluator-Auflage zu AUF-38 auf („BEIDE Schreibweisen") - **offen, nicht stillschweigend.** Der
Grund: ein `style={bezeichner}` verweist auf eine Variable; ob deren Inhalt statisch ist, kann
das Werkzeug heute nicht entscheiden. Die Auflage verlangte also etwas, das kein Befehl belegt.

**Der Posten verschwindet nicht, er bekommt ein eigenes Blatt:** AUF-38-P3 - zuerst das Werkzeug
um die Bezeichner-Form erweitern, dann messen, dann umstellen. In dieser Reihenfolge, sonst
wiederholt sich genau der Fehler, den der Evaluator hier gefunden hat.

**Widerspruch bitte vor dem Bau, nicht bei der Abnahme.**

**Die Stilschicht existiert und ist erprobt:** `public/hausplaner/hausplaner.css` mit **207**
`hp-*`-Klassen, gespeist aus `app/stil/tokenVariablen.ts`, verriegelt durch
`__tests__/stilschicht.test.ts` (171 Zusagen). *Scheibe 1 bis 3 haben die Mechanik bewiesen —
hier wird sie angewandt, nicht neu erfunden.*

## Kriterien

*Auf das Validator-Schema umgestellt am 30.07. 23:27 (VORLAGE-Regel 8). Vorher meldete
`node scripts/auftrag-pruefen.mjs` fuer dieses Blatt: „KEIN PRUEFBEFEHL im Kopf gefunden".*

```yaml
scope:
  datei: resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx
  population_command: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx | tail -1"
  ausschluesse:
    - stelle: "style={bezeichner} (56 bis 58 Stellen im Panel)"
      grund: "Das Messwerkzeug kennt diese Form nicht; eine Zahl, die kein Abnahmebefehl erreicht, ist ein Posten, der auf nichts zeigt. Eigenes Blatt AUF-38-P3, das zuerst das Werkzeug erweitert."
      entschieden_von: planner
    - stelle: "__tests__/eigenschaftenPanel.test.ts"
      grund: "S4d hat dort zwei ungeschuetzte A11y-Entscheidungen geschlossen. Die Datei wird nicht angefasst."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence
    kritikalitaet: P1
    aussage: "Im Panel bleibt keine offene statische Inline-Stelle."
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx | tail -1"
      erwartet: "davon 0 offen"
    ausgangswert: "71 Stellen insgesamt, davon 37 offen (gemessen 30.07. 23:15)"
    gegenbeweis: >
      Die Null allein ist erreichbar, indem man Stellen dynamisch macht statt sie umzustellen.
      Deshalb zaehlt der GESAMTWERT mit: er muss von 71 auf 34 fallen, also um genau die 37
      offenen. Faellt er weiter, ist Inhalt verschwunden; faellt er weniger, wurde eine
      statische Stelle in eine dynamische verwandelt statt in die Stilschicht gehoben.

  - id: K-01b
    typ: coverage
    aussage: "Ausserhalb des Panels hat sich nichts bewegt."
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs | tail -1"
      erwartet: "158 Stellen insgesamt, davon 40 offen."
    ausgangswert: "195 Stellen insgesamt, davon 77 offen"
    gegenbeweis: >
      195-37=158, 77-37=40. Steht dort weniger als 40 offen, ist ausserhalb des Auftrags
      mitgeraeumt worden - kein Bonus, sondern eine ungeprueft geaenderte Datei. Steht dort
      mehr, ist eine Stelle in eine Nachbardatei gewandert.

  - id: K-02
    typ: absence
    aussage: "Kein roher Farbwert in den neuen CSS-Regeln."
    pruefung:
      befehl: "git diff main -- public/hausplaner/hausplaner.css | grep '^+' | grep -oE '#[0-9a-fA-F]{3,6}|rgb[(]' | wc -l"
      erwartet: "0"
    ausgangswert: "0 (heute ist die Datei gegenueber main unveraendert)"
    gegenbeweis: >
      Der Befehl misst gegen main, nicht gegen den Arbeitsbaum - sonst waere er nach dem
      Commit blind. Zum Gegenbeweis eine Zeile mit einem rohen Wert einfuegen: der Zaehler
      muss steigen. Jede Farbe kommt aus var(--hp-...); das ist die Regel aus Scheibe 1-3
      und der Grund, warum die Stilschicht ueberhaupt etwas wert ist.

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Verriegelung der Stilschicht bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=stilschicht"
      erwartet: >
        gruen, 171 Zusagen unveraendert. Besonders assert.doesNotMatch(quelle, /@media/) -
        die Zusage, die "Responsive ist L7" traegt. Wer sie bricht, hat den falschen Weg genommen.
    begruendung: "Gate - der Planner faehrt keine npm-Laeufe; der Bauende fuehrt sie aus und legt die Rohausgabe bei."

  - id: K-04
    typ: behavioural
    aussage: "Das Panel sieht gleich aus - gemessen, nicht betrachtet."
    pruefung:
      typ: browser
      schritte: >
        Vor und nach dem Umbau ein Bauteil auswaehlen und getBoundingClientRect() von Panel,
        Reiterleiste und erstem Feld notieren. Die Werte muessen gleich sein.
    gegenbeweis: >
      Aendere EINE Klasse absichtlich (etwa padding) und miss erneut. Bleiben die Werte
      gleich, misst die Probe nicht, was sie zu messen behauptet.

  - id: K-05
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Tests."
    pruefung:
      typ: verfahren
      schritte: >
        Mindestens 8 Mutationen an dem, was der Umbau anfasst: eine Klasse am falschen
        Element, zwei Klassen vertauscht, eine Regel ohne Wirkung. Wie viele kommen durch?
    gegenbeweis: >
      In AUF-48 waren es ueber acht Scheiben 38 von 52. Die Zahl gehoert in den Bericht,
      auch wenn sie 0 ist.

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die A11y-Entscheidungen des Panels ueberleben."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=eigenschaftenPanel"
      erwartet: "gruen, Zahl der Zusagen UNVERAENDERT"
    begruendung: >
      S4d hat gemessen, dass zwei Zusagen durch nichts geschuetzt waren, und sie geschlossen:
      Schwere als Symbol UND Text sowie die Rueckfrage vor dem Entsperren.

  - id: L-01
    typ: presence
    aussage: "Browsertest gefahren, an http://ticket.test."
    pruefung:
      typ: browser
      schritte: >
        npm run build:hausplaner, dann /admin/hausplaner/studio, Expertenmodus, Wand zeichnen,
        auswaehlen, Panel zeigt ihre Werte, Reiter wechseln, Sicht- und Sperrschalter reagieren.
        Drei Pflicht-Viewports: 1440, 1024, 375.

  - id: L-01-anker
    typ: presence
    aussage: "Die Seite ist ueberhaupt da, bevor irgendeine Zahl abgelesen wird."
    pruefung:
      typ: browser
      schritte: >
        VOR jeder anderen Zahl: HTTP 200, querySelectorAll('canvas') mindestens 1,
        document.title enthaelt "Hausplaner". Auch melden, wenn alles gut aussah.
```

## Danach

**P2 bis P5** je Datei: `GruppenzeileUndSchiene` (27) · `FussUndUeberlagerungen` (20) ·
`Kopfrahmen` (16) · `HausplanerApp` (4). *`Buehne.tsx` hat null — dort ist nichts zu tun.*
**Erst nach P1**, weil P1 die Mechanik an der schwersten Datei zeigt.


---

## NACHTRAG 22:54 — **K-03 steht auf einer Zusage, die selbst einen Befund traegt**

**PB-010 (Pruefer):** *„`stilschicht.test.ts` — Wirkungs-Zusage prueft gegen **3 tote
Bezeichner**."* Rang P3, Zustand *ANGENOMMEN, ABER ANDERS GESCHNITTEN*.

**Das ist genau die Datei, auf die K-03 dieses Blattes sich stuetzt.** *Eine Zusage, die gegen
Bezeichner prueft, die es nicht mehr gibt, ist an diesen Stellen gruen, ohne etwas zu pruefen.*

### Was daraus folgt — und was ausdruecklich NICHT

**K-03 bleibt gueltig.** Der Kern der Zusage — `assert.doesNotMatch(quelle, /@media/)` und
`/!important/` — traegt unabhaengig davon; er prueft **Abwesenheit in der Quelle**, nicht
einen Bezeichner. *Die drei toten Stellen betreffen die Wirkungs-Zusage, nicht diese beiden.*

**Aber der Bericht nennt sie.** Wer P1 baut, faellt ueber sie:

```yaml
  - id: K-03b
    aussage: "Die drei toten Bezeichner aus PB-010 sind benannt, nicht stillschweigend geheilt."
    nachweis: >
      Nenne im Bericht, WELCHE Bezeichner `stilschicht.test.ts` prueft, die es in der
      gebauten CSS nicht gibt. **Beheben ist NICHT Teil dieses Auftrags** — PB-010 liegt
      beim Evaluator und ist anders geschnitten worden.
    warnung: >
      *Meine eigene Zaehlung dazu war unbrauchbar: ich habe `hp-`-Vorkommen im Test gegen
      die CSS gehalten und 26 "tote" gefunden — darunter `hp-accent` (das ist die CSS-Variable
      `--hp-accent`, keine Klasse) und die Praefixe `hp-ef-` und `hp-gf-`.* **Der Befehl mass
      die Gestalt, nicht die Sache.** Der Pruefer nennt drei; seine Messung gilt, meine nicht.
      **Wer hier zaehlt, sagt dazu, WONACH er zaehlt.**
```
