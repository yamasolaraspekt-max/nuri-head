# Z-02 — `fangKern` an den laufenden Weg anschließen, Toleranz in Bildschirm-Pixeln

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 12:0x*

```yaml
auftrag:
  id: Z-02
  status: abgenommen   # gebaut 8811e638 13:24, Votum GRUEN vom Evaluator in 211f3f91 - Gates 1592/0, tsc 0. Eingetragen vom Planner
```

## Warum das die erste Zeichnen-Scheibe nach Z-01 ist

**Yamas Ziel steht seit Tagen fest: Geschosse bauen und eine saubere Zwischendecke ziehen.** Die
Kette dorthin ist **Z-02 → Z-05 (Polygonwerkzeug) → Z-06 (Decke aus Kontur)**. *Ohne einen Fang,
der trifft, klickt man die Kontur nicht zu — und ohne geschlossene Kontur bleibt die Decke die
Bounding-Box aller Wände.* **Z-02 ist die Voraussetzung, nicht die Kür.**

## Bestand — gemessen 01.08. 12:0x

```text
geometry/fangKern.ts                                        103 Zeilen, 12 Zusagen im Test
Importeure ausserhalb der Tests                               0     <- er wird von NICHTS benutzt
  grep -rl "geometry/fangKern" resources/planner/hausplaner --include='*.ts*' | grep -v __tests__ | wc -l

HausplanerApp.tsx:605  weltPunkt()  hat eine EIGENE Endpunkt-Schleife:
  :616   // 1) Endpunkt-Snap (150 mm Radius) hat Vorrang.
  :619   if (Math.hypot(p.x - x, p.y - y) <= 150) { return { x: p.x, y: p.y }; }

  node scripts/zaehle.mjs …/HausplanerApp.tsx 'hypot\(p\.x - x, p\.y - y\)'  ->  1
```

**Der Kern ist gebaut, geprüft und wird von nichts benutzt. Daneben steht eine zweite, schlechtere
Wahrheit im Bauteil.** *Das ist genau die „verwaiste zweite Wahrheit", die der Wächter verbietet —
nur andersherum: nicht der Wert ist doppelt, sondern die Regel.*

## Die Entscheidung

**`weltPunkt()` ruft `fange()` auf und rechnet die Toleranz aus dem Zoom.**

```text
toleranzMm = FANG_PX / zoom          FANG_PX = 12 (Bildschirmpixel)
```

**Warum in Bildschirmpixeln und nicht in Millimetern — das ist der eigentliche Befund:**
die 150 mm sind **fest in Weltkoordinaten** verdrahtet. Bei Zoom 0,02 sind das **3 Bildschirmpixel** —
der Fang ist praktisch tot. Bei Zoom 0,5 sind es 75 px — er reißt den Zeiger von überall an sich.
**Ein Fangradius, der sich nicht mit dem Zoom ändert, ist entweder unbrauchbar oder aufdringlich,
nie beides richtig.**

**`fange()` kann das schon:** `FangOptionen.toleranzMm` ist ausdrücklich dokumentiert als
*„Endpunkt-Toleranz in mm (Aufrufer: px→mm via Zoom)"*. **Der Kern wurde für genau diesen Anschluss
gebaut. Er wird angeschlossen, nicht neu geschrieben.**

**Was NICHT Gegenstand ist:** Mittelpunkt-, Wandachsen- und Verlängerungsfang (das ist Z-04), und
der sichtbare Fangtyp-Hinweis (Z-03). *Wer sie mitbaut, sprengt die Scheibe und die Abnahme.*

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/app/HausplanerApp.tsx
    - resources/planner/hausplaner/geometry/fangKern.ts
  population_command: "grep -rl 'geometry/fangKern' resources/planner/hausplaner --include='*.ts*' | grep -v __tests__ | wc -l"
  ausschluesse:
    - stelle: "Mittelpunkt-, Achsen- und Verlaengerungsfang"
      grund: "Eigene Scheibe Z-04. Diese hier schliesst nur an, was schon gebaut ist."
      entschieden_von: planner
    - stelle: "Sichtbarer Fangtyp-Hinweis (Badge/Statusleiste)"
      grund: "Eigene Scheibe Z-03."
      entschieden_von: planner
    - stelle: "Die 150 in den Zeilen 969/970"
      grund: "Bemassungs-Kette, hat mit dem Fang nichts zu tun. Gemessen: von vier Vorkommen der Zahl 150 gehoeren zwei zur Bemassung und eines steht im Kommentar."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "fangKern wird vom laufenden Weg benutzt, nicht nur vom Test."
    pruefung:
      befehl: "grep -rl 'geometry/fangKern' resources/planner/hausplaner --include='*.ts*' | grep -v __tests__ | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 01.08. 12:0x)"
    gegenbeweis: |
      Die Zahl allein ist auch durch einen unbenutzten Import erreichbar. Deshalb zaehlt K-02
      gegen: die alte Schleife MUSS verschwinden. Beides zusammen heisst "angeschlossen",
      eines allein heisst nichts.

  - id: K-02
    typ: absence
    kritikalitaet: P1
    aussage: "Die zweite Fang-Wahrheit im Bauteil ist weg."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/HausplanerApp.tsx 'hypot\\(p\\.x - x, p\\.y - y\\)'"
      erwartet: "0"
    ausgangswert: "1"
    gegenbeweis: |
      Rotprobe des Planners: dieselbe Datei ohne Zeile 619 -> 0, mit -> 1. Der Befehl trifft.
      Gezaehlt wird mit `zaehle.mjs`, nicht mit grep: die Zeile darueber ist ein Kommentar,
      der "150 mm Radius" nennt. Wer roh zaehlt, zaehlt den Kommentar mit - das ist F-09,
      und sie ist heute schon zweimal zugeschnappt.

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Toleranz haengt am Zoom - in Bildschirmpixeln, nicht in Millimetern."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Neue Zusage in __tests__/fangKern.test.ts oder einer Nachbardatei:
        bei Zoom 0.02 und bei Zoom 0.2 muss die uebergebene toleranzMm sich um den
        Faktor 10 unterscheiden. Die Zusage prueft die RECHNUNG, nicht die Zahl 12 -
        eine Zusage auf den Zahlenwert friert nur den gebauten Zustand ein (F-06).
      erwartet: "gruen"

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die zwoelf vorhandenen Zusagen des Kerns bleiben gruen und unveraendert in der Zahl."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=fangKern"
      erwartet: "gruen, 12 Zusagen. Weicht die Zahl ab, sagt der Bauende warum - nach oben genauso wie nach unten."

  - id: K-05
    typ: behavioural
    aussage: "Die ganze Insel bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail"

  - id: K-06
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Tests."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 8 Mutationen an dem, was der Umbau anfasst: Toleranz mit dem Zoom
        MULTIPLIZIERT statt geteilt, Vorzeichen der y-Spiegelung gedreht, `aktiv` ignoriert,
        Raster vor Endpunkt statt danach. Wie viele kommen durch?
    gegenbeweis: |
      In AUF-38-P1 waren es 7 von 8, in P2 wieder. Die Zahl gehoert in den Bericht, auch wenn
      sie 0 ist.

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest an http://ticket.test - der Fang trifft bei WEIT und bei NAH."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, Expertenmodus.
        Zwei Waende zeichnen. Dann:
        (a) weit herauszoomen  - das Wandende faengt immer noch, ohne Pixeljagd
        (b) weit hineinzoomen  - der Zeiger wird NICHT aus grosser Entfernung angezogen
        (c) Fang in den Einstellungen ausschalten - nichts faengt mehr
        Drei Pflicht-Viewports: 1440, 1024, 375.
    gegenbeweis: |
      Genau (a) ist der heutige Fehler: bei Zoom 0,02 sind 150 mm drei Bildschirmpixel.
      Wer nur bei Standardzoom probiert, sieht keinen Unterschied zu vorher - und die
      Scheibe waere umsonst gebaut.

  - id: L-01-anker
    typ: presence
    aussage: "Die Seite ist ueberhaupt da, bevor irgendeine Zahl abgelesen wird."
    pruefung:
      typ: browser
      schritte: |
        VOR jeder anderen Zahl: HTTP 200, querySelectorAll('canvas') mindestens 1,
        document.title enthaelt "Hausplaner". Auch melden, wenn alles gut aussah.
```

## Rückweg und Entdeckung

**Rückweg:** eine Funktion, kein Datenpfad, keine Migration — der Commit lässt sich zurückdrehen.
**Entdeckung:** fällt der Fang aus, merkt man es beim ersten Wandende, das nicht mehr einrastet.
K-02 und die Zoom-Zusage fangen den stillen Fall vorher ab.

## Danach

**Z-03** Fangtyp sichtbar · **Z-04** Mittelpunkt/Achse/Verlängerung · **Z-05** Polygonwerkzeug ·
**Z-06 Decke aus Kontur** — *das ist die Scheibe, auf die Yama wartet.*
