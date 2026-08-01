# Z-03 + Z-04 — der Fang sagt, WAS er gefangen hat, und kann mehr als Endpunkte

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · **Vorbedingung: Z-02 abgenommen** ·
*Geschnitten 01.08. 19:4x*

```yaml
auftrag:
  id: Z-03+Z-04
  status: bereit
```

## Warum zusammen

**Beide hängen an derselben Rückgabe.** `fange()` liefert seit Z-02 `{ punkt, art }` — und die
Insel benutzt heute nur `punkt`:

```text
node scripts/zaehle.mjs …/HausplanerApp.tsx 'const \{ punkt \} = fange'   ->  1
```

**Die Auskunft, was gefangen wurde, wird weggeworfen.** Z-03 zeigt sie an; Z-04 erweitert, was es
zu zeigen gibt. *Getrennte Blätter hätten dieselbe Zeile zweimal angefasst.*

## Bestand — gemessen 01.08. 19:4x

```text
geometry/fangKern.ts    FangArt = 'endpunkt' | 'ortho' | 'raster' | 'keiner'      12 Zusagen
                        node scripts/zaehle.mjs …/fangKern.ts "'mittelpunkt'|'achse'|'verlaengerung'"  ->  0
HausplanerApp.tsx       const { punkt } = fange(…)      die Art wird verworfen
Statusleiste            app/rahmen/FussUndUeberlagerungen.tsx  (dort liegt die Fussfläche)
```

## Die Entscheidung

**Z-03: die Art wandert in die Fußfläche, nicht in ein neues Bedienelement.** Dort steht schon die
Statuszeile. *Ein eigenes Abzeichen wäre eine zweite Fläche für dieselbe Auskunft.*

**Z-04: drei neue Fangarten** — `mittelpunkt` (Wandmitte), `achse` (Verlängerung der Wandachse),
`verlaengerung` (gerade Fortsetzung des laufenden Wegs). **Die Reihenfolge ist Teil des Vertrags:**
Endpunkt schlägt Mittelpunkt schlägt Achse schlägt Verlängerung schlägt Raster. *Ohne feste
Rangfolge springt der Fang bei dichten Grundrissen zwischen zwei Kandidaten hin und her, und das
sieht aus wie ein Wackelkontakt.*

**Was NICHT Gegenstand ist:** eine Einstellung, mit der man einzelne Fangarten abschaltet. *Das ist
eine Bedienfrage und braucht eine eigene Entscheidung — hier wird erst gebaut, was es zu schalten
gäbe.*

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/geometry/fangKern.ts
    - resources/planner/hausplaner/app/HausplanerApp.tsx
    - resources/planner/hausplaner/app/rahmen/FussUndUeberlagerungen.tsx
  population_command: "node scripts/zaehle.mjs resources/planner/hausplaner/geometry/fangKern.ts \"'mittelpunkt'|'achse'|'verlaengerung'\""
  ausschluesse:
    - stelle: "Eine Einstellung zum Abschalten einzelner Fangarten"
      grund: "Bedienfrage mit eigener Entscheidung. Erst bauen, was es zu schalten gaebe."
      entschieden_von: planner
    - stelle: "Ein eigenes Abzeichen fuer den Fangtyp"
      grund: "Die Fussflaeche traegt schon die Statuszeile. Eine zweite Flaeche fuer dieselbe Auskunft ist Overload."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence
    kritikalitaet: P1
    aussage: "Z-03 - die Fangart wird nicht mehr weggeworfen."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/HausplanerApp.tsx 'const \\{ punkt \\} = fange'"
      erwartet: "0"
    ausgangswert: "1 (gemessen 01.08. 19:4x)"
    gegenbeweis: |
      Rotprobe des Planners: dieselbe Datei mit `const { punkt, art } = fange` -> 0, ohne -> 1.
      Die Null allein genuegt nicht - K-02 verlangt, dass die Art auch ankommt.

  - id: K-02
    typ: presence
    kritikalitaet: P1
    aussage: "Z-03 - die Fussflaeche zeigt die Fangart im Klartext."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Neue Zusage in __tests__/fussUndUeberlagerungen.test.ts (heute 12):
        je Fangart ein deutscher Klartext - Endpunkt, Mittelpunkt, Achse, Verlaengerung,
        Raster, kein Fang. Die Zusage prueft die ZUORDNUNG, nicht die Zeichenkette einer
        einzelnen Art (F-06), und sie prueft mit Wortgrenze (F-11).
      erwartet: "gruen"

  - id: K-03
    typ: presence
    kritikalitaet: P1
    aussage: "Z-04 - die drei neuen Fangarten existieren."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/geometry/fangKern.ts \"'mittelpunkt'|'achse'|'verlaengerung'\""
      erwartet: "mindestens 3"
    ausgangswert: "0"
    gegenbeweis: |
      Rotprobe des Planners: Kopie plus eine Zeile mit 'mittelpunkt' -> 1. Der Befehl trifft.
      Gezaehlt wird mit `zaehle.mjs`, damit ein Kommentar, der die Namen nennt, nicht mitzaehlt
      (F-09 - zweimal an einem Tag zugeschnappt).

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Rangfolge ist festgenagelt, nicht zufaellig."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Neue Zusagen in __tests__/fangKern.test.ts (heute 12): ein Punkt, der GLEICHZEITIG
        in Reichweite eines Endpunkts und einer Wandmitte liegt, faengt den Endpunkt.
        Je Nachbarpaar der Rangfolge eine Zusage:
        endpunkt vor mittelpunkt vor achse vor verlaengerung vor raster.
      erwartet: "gruen, mindestens 16 Zusagen"

  - id: K-05
    typ: behavioural
    aussage: "Die zwoelf vorhandenen Zusagen des Kerns bleiben gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail. fangKern waechst von 12 auf mindestens 16 - jede neue Zusage wird im Bericht genannt."

  - id: K-06
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Tests."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 8 Mutationen: Rangfolge vertauscht, Mittelpunkt statt Endpunkt zuerst,
        Achsen-Toleranz mit dem Zoom multipliziert statt geteilt, Fangart im Klartext vertauscht,
        `art` gesetzt aber nie angezeigt. Wie viele kommen durch?

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest an http://ticket.test - jede Fangart einmal sichtbar gefangen."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, Expertenmodus.
        Zwei Waende zeichnen, dann eine dritte beginnen und der Reihe nach ansteuern:
        Wandende, Wandmitte, Achsenverlaengerung, gerade Fortsetzung, Raster.
        Die Fussflaeche nennt jedes Mal die richtige Art.
        Und: WEIT herauszoomen - die Arten muessen dort genauso greifen (das war der
        eigentliche Befund von Z-02: 150 mm sind bei Zoom 0,02 drei Bildschirmpixel).
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

**Z-07** Vorschlagskontur aus dem Grundriss · **Z-08** Dach aus Kontur · **Z-09** Gehrung und
T-Anschluss · **Z-10** Maßeingabe · **Z-11** Touch und Stift.
