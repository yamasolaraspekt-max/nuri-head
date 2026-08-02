# Z-10 — Länge tippen statt ziehen: die direkte Maßeingabe

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 22:2x*

```yaml
auftrag:
  id: Z-10
  strang: hausplaner-3d
  status: gebaut   # c9af2243, 02.08. 09:20 - Suite 1641/1641, tsc 0, Inline-Stellen unveraendert. Wartet auf das Votum des Evaluators
```

## Warum das jetzt geht und warum es zählt

**Z-10 hängt laut Bestandsaufnahme nur an Z-01** (`docs/planner/programm-zeichnen-bestandsaufnahme.md`,
Zeile 172). **Z-01 liegt seit dem 01.08. abgenommen auf `main`.** Diese Scheibe ist damit ohne jede
weitere Vorbedingung baubar — sie wartet auf nichts.

**Warum sie zählt:** wer ein Geschoss baut, hat Maße im Kopf, nicht Pixel. **4200 tippen ist genauer
als ziehen und schneller.** Heute gibt es dafür keinen Weg:

```text
node scripts/zaehle.mjs resources/planner/hausplaner/app/tastenAbsicht.ts 'masseingabe'   -> 0
   (Partner 'kontur-schliessen' -> 2, die Messung ist nicht leer)
node scripts/zaehle.mjs resources/planner/hausplaner/app/rahmen/FussUndUeberlagerungen.tsx 'masseingabe' -> 0
grep -rln 'masseingabe|laengeEingabe' resources/planner/hausplaner/   -> 0 Dateien
```

## Was es NICHT ist — die Verwechslung, die hier droht

**`geometry/bemassung.ts` und `geometry/masskette.ts` existieren bereits** und heißen fast so.
Sie **zeigen** Maße an (`bemassung`, `masskette`, `grundrissMassketten`, `punkteMassketten`) —
sie **nehmen keine entgegen**. *Das ist die Anzeigerichtung; Z-10 ist die Eingaberichtung.*
**Wer die beiden verwechselt, baut ein Kriterium, das vor dem Bau schon grün ist (F-07).**

## Die Entscheidung

**Während ein Zeichenweg läuft, öffnet eine Ziffer die Maßeingabe.** Nicht ein Knopf, nicht ein
Menü — die erste getippte Ziffer.

```text
Zustand: es laeuft ein Zug (Wand, Kontur) und es gibt einen Ausgangspunkt
  Ziffer 0-9   ->  Maßeingabe oeffnet sich, die Ziffer steht schon drin
  Tab          ->  zwischen Laenge und Winkel wechseln
  Enter        ->  Punkt wird gesetzt: Richtung aus dem Zeiger, Laenge aus dem Feld
  Escape       ->  Eingabe verwerfen, der Zug laeuft weiter (NICHT der ganze Zug bricht ab)
```

**Die Richtung kommt weiter aus dem Zeiger, nur die Länge aus dem Feld.** *Wer beides tippen will,
tippt den Winkel dazu — aber niemand muss.* Das hält die Scheibe klein und den Bedienweg vertraut.

**Die Absicht gehört in `tastenAbsicht.ts`, nicht in einen neuen Tastenhörer.** Z-01 hat die
Tastenabbildung an **eine** Stelle gelegt; ein zweiter Hörer macht Z-01 rückgängig — derselbe
Fehler, den Z-05 bei den Aufräumstellen vermieden hat.

## Nahtstellen

```text
Hier wird geschrieben:
  resources/planner/hausplaner/geometry/masseingabe.ts        NEU - reine Rechnung:
      punktAusLaenge(start, zeigerRichtung, laengeMm, winkelGrad?)
      Keine React-Abhaengigkeit, keine Speicher-Wirkung.
  resources/planner/hausplaner/app/tastenAbsicht.ts           die Absichten 'masseingabe-oeffnen'
                                                              und 'masseingabe-uebernehmen'
  resources/planner/hausplaner/app/rahmen/FussUndUeberlagerungen.tsx   das Feld in der Statusleiste

Hier bewusst NICHT:
  geometry/bemassung.ts, geometry/masskette.ts   Anzeigerichtung. Unberuehrt.
  domain/validation.ts                           kein persistiertes Schema. Eine Eingabehilfe
                                                 erzeugt denselben Knoten wie das Ziehen.
  Winkel-Rasten (15°, 45°)                       eigene Entscheidung, eigener Bedienweg.
```

**Erweiterungspunkt, jetzt nicht gebaut:** `punktAusLaenge` nimmt den Winkel bereits als optionales
Argument. Z-09 (Eckanschluss) und eine spätere Winkelrastung docken dort an, ohne die Signatur zu
ändern.

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/geometry/masseingabe.ts
    - resources/planner/hausplaner/app/tastenAbsicht.ts
    - resources/planner/hausplaner/app/rahmen/FussUndUeberlagerungen.tsx
  population_command: "node scripts/zaehle.mjs resources/planner/hausplaner/app/tastenAbsicht.ts 'masseingabe'"
  ausschluesse:
    - stelle: "geometry/bemassung.ts und geometry/masskette.ts"
      grund: "Anzeigerichtung. Z-10 ist die Eingaberichtung. Wer sie anfasst, baut ein Kriterium, das schon steht."
      entschieden_von: planner
    - stelle: "Winkelrastung 15/45 Grad"
      grund: "Eigene Entscheidung mit eigenem Bedienweg."
      entschieden_von: planner
    - stelle: "Ein zweiter Tastenhoerer"
      grund: "Z-01 hat die Tastenabbildung auf EINE Stelle gelegt. Ein zweiter macht Z-01 rueckgaengig."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Die Absicht steht in der EINEN Tastenabbildung aus Z-01."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/tastenAbsicht.ts 'masseingabe'"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 01.08. 22:2x; Partner 'kontur-schliessen' -> 2)"

  - id: K-02
    typ: presence
    kritikalitaet: P1
    aussage: "Die Rechnung liegt in der Geometrie, nicht in der Ansicht."
    pruefung:
      befehl: "grep -o 'punktAusLaenge' -r resources/planner/hausplaner/geometry/ | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 01.08. 22:2x; Partner 'schneidetSichSelbst' -> 2, die Messung ist nicht leer)"
    gegenbeweis: |
      Bewusst ueber das VERZEICHNIS gemessen, nicht ueber die Datei. `zaehle.mjs <datei>` wirft
      ENOENT und exit 1, solange masseingabe.ts nicht existiert - ein Kriterium, das VOR dem Bau
      gar nicht laufen kann. Genau das verbietet Regel A. Der Verzeichnis-grep laeuft heute.

  - id: K-03
    typ: absence
    aussage: "Die Anzeigerichtung wurde nicht angefasst."
    pruefung:
      befehl: "git diff main -- resources/planner/hausplaner/geometry/bemassung.ts resources/planner/hausplaner/geometry/masskette.ts | wc -l"
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      Waechst diese Zahl, ist die Anzeigerichtung mitgebaut worden - dann misst K-01 etwas
      anderes, als das Blatt meint. Partner: dieselbe Form gegen geometry/kontur.ts liefert 181.

  - id: K-04
    typ: absence
    aussage: "Kein persistiertes Schema wurde angefasst."
    pruefung:
      befehl: "git diff main -- resources/planner/hausplaner/domain/validation.ts | wc -l"
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      Eine Eingabehilfe erzeugt denselben Knoten wie das Ziehen. `type`, `objectType`,
      `zoneType`, `routeType` bleiben, wie sie sind.

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Rechnung ist gegen die Faelle geprueft, an denen sie bricht."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        __tests__/masseingabe.test.ts mit mindestens diesen Faellen, jeder prueft die AUSSAGE
        und nicht die Zahl der Argumente (F-06):
          Laenge 4200, Zeiger nach rechts        -> Punkt liegt 4200 mm rechts, exakt
          Laenge 0                               -> abgelehnt, kein Punkt
          Laenge negativ                         -> abgelehnt
          Zeiger genau auf dem Startpunkt        -> keine Richtung, abgelehnt (Division durch 0)
          Winkel 360 und Winkel 0                -> dasselbe Ergebnis
          Winkel 90 mit Zeiger nach rechts       -> Winkel schlaegt den Zeiger
          sehr grosse Laenge (1e9 mm)            -> kein Ueberlauf, kein NaN
      erwartet: "gruen"

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "Escape verwirft die EINGABE, nicht den Zug."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        npm run test:hausplaner -- --filter=werkzeugEnde
        Erwartet gruen und die Zusagenzahl waechst oder bleibt - nie weniger. Z-01 hat sieben
        Aufraeumstellen zu einer gemacht; ein Escape, das hier den ganzen Zug abraeumt, ist rot.
      erwartet: "gruen"

  - id: K-07
    typ: behavioural
    aussage: "Die ganze Insel bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail"

  - id: K-08
    typ: behavioural
    aussage: "Stil-Bruecken-Test - das Eingabefeld bekommt eine Klasse, keinen Inline-Stil."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        node scripts/statische-inline-stile.mjs
        AUF-38 ist am 01.08. bei 118 Stellen / NULL offenen zu Ende gegangen (fba3083f).
        Ein neuer Inline-Stil macht das rueckgaengig. Ausgangswert: 118 / 0 offen.
      erwartet: "0 offen, Gesamtzahl hoechstens 118"

  - id: K-09
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 7 Mutationen: Laenge 0 zugelassen · negative Laenge zugelassen ·
        Richtung aus dem Feld statt aus dem Zeiger · Enter setzt keinen Punkt ·
        Escape raeumt den ganzen Zug ab · Ziffer oeffnet nicht · Winkel wird ignoriert.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest an http://ticket.test - 4200 tippen und die Wand steht."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, angemeldet, Expertenmodus.
        (a) Wandwerkzeug, ersten Punkt setzen, "4200" tippen -> das Feld oeffnet sich mit der 4
        (b) Enter -> die Wand ist 4200 mm lang. NACHMESSEN mit dem Messwerkzeug, nicht schaetzen
        (c) Escape waehrend der Eingabe -> Feld weg, der Zug laeuft weiter
        (d) "0" tippen und Enter -> abgelehnt, sichtbarer Grund, kein Punkt
        Drei Pflicht-Viewports: 1440, 1024, 375.
        PARTNERMESSUNG in jedem Viewport: die Statusleiste ist ueberhaupt sichtbar.

  - id: L-01-anker
    typ: presence
    aussage: "Die Seite ist ueberhaupt da, bevor irgendeine Zahl abgelesen wird."
    pruefung:
      typ: browser
      schritte: |
        VOR jeder anderen Zahl: HTTP 200, querySelectorAll('canvas') mindestens 1,
        document.title enthaelt "Hausplaner".
```

## Kantenliste — wo das erfahrungsgemäß bricht

```text
1  `bemassung.ts`/`masskette.ts` werden fuer die Eingabe gehalten. Sie zeigen an. -> Ausschluss
2  Zeiger genau auf dem Startpunkt: keine Richtung, Division durch 0, NaN als Koordinate.
   Ein NaN-Punkt malt NICHTS und wirft nicht - still falsch. -> K-05
3  Escape raeumt den ganzen Zug ab statt nur die Eingabe. -> K-06
4  Ein zweiter Tastenhoerer neben `tastenAbsicht.ts`. Dann tippt man in zwei Wahrheiten.
5  Die Ziffer oeffnet das Feld AUCH, wenn kein Zug laeuft - dann frisst das Feld jede Tastatur.
6  Das Feld nimmt den Fokus und die Leertaste scrollt nicht mehr / Werkzeugkuerzel gehen verloren.
7  Einheiten: das Schema rechnet in mm (`z.literal('mm')`). Wer 4,20 tippt und mm meint,
   baut eine 4 mm lange Wand. Die Einheit gehoert sichtbar ans Feld.
8  Ein neuer Inline-Stil macht AUF-38 rueckgaengig. -> K-08
```

## Rückweg und Entdeckung

**Rückweg:** neue Datei, zwei Absichten, ein Feld — kein Datenpfad, kein Schema, keine Migration.
Der Commit lässt sich zurückdrehen. **Solange nicht gepusht ist, liegt der Rückweg auf derselben
Platte wie die Arbeit.**

**Entdeckung:** man tippt eine Länge und misst sie nach. Der stille Fall ist Kante 2 und Kante 7 —
ein NaN-Punkt, der nichts malt, und eine Wand, die 4 mm statt 4200 mm lang ist. *Beide sehen nicht
nach Fehler aus, sondern nach „hat nicht geklappt".*

## Danach

**Z-09** (Eckanschluss: Gehrung, T, Kreuz) nimmt den optionalen Winkel von `punktAusLaenge`.
**Z-11** (Touch und Stift) braucht die Maßeingabe, weil auf dem Tablet niemand pixelgenau zieht.
