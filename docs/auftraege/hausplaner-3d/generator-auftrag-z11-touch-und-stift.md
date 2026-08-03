# Z-11 — Touch und Stift: eine Eingabe-Wahrheit statt zwei

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 23:2x*

```yaml
auftrag:
  id: Z-11
  strang: hausplaner-3d
  status: entwurf   # B8 - Planner-Blatt. GEGENLESER UMVERTEILT 03.08. 08:3x: Pruefer -> EVALUATOR. Gemessen, nicht geurteilt: der Pruefer hat NULL Voten im ganzen Ledger und seit 01.08. 23:00 keinen Commit; dieses Blatt sperrt N2, N3 und ueber B10 das Dach - die ganze Kette zum Geschoss. B8 verlangt eine ANDERE Rolle als den Schreiber, keine bestimmte; der Evaluator hat mit neun Voten heute frueh die Kapazitaet belegt. Die d1cecdcf-Zuordnung (Planner-Blatt -> Pruefer) gilt wieder, sobald der Pruefer ein Lebenszeichen setzt.
  gegengelesen_von: evaluator   # umverteilt 03.08.; der Pruefer ist inzwischen wieder aktiv, ab dem naechsten Planner-Blatt gilt d1cecdcf
  gegengelesen_am: 2026-08-03
  befund: >
    TRAEGT NICHT IN DER VORLIEGENDEN FASSUNG. Drei sperrende Befunde, alle selbst gemessen,
    keiner am Ziel des Blattes (Touch-Bedienung ist richtig und noetig) sondern an Ist-Befund
    und Bauanweisung.
    (1) ZWEI DER DREI ZU ERSETZENDEN HANDLER EXISTIEREN NICHT. Gemessen an Buehne.tsx mit
    zaehle.mjs UND rohem grep, beide Wege gleich: onMouseDown 0, onMouseUp 0, onMouseMove 1
    (Zeile 106). Die Bauart-Entscheidung onMouseDown/Move/Up nach onPointerDown/Move/Up
    greift zu zwei Dritteln ins Leere. Die Buehne ist ein Konva-Stage und traegt
    onClick 105, onMouseMove 106, onMouseLeave 107, onMouseEnter 108, onWheel 109,
    draggable 104 und onDragMove/onDragEnd 120/121.
    (2) K-02 IST IM EIGENEN BLATT UNERFUELLBAR. Der Ausgangswert 3 zaehlt zwei KOMMENTARE mit
    (Zeilen 42 und 117); kommentarfrei ist es 1. Zeile 42 ist genau der Kommentar, den Kante 6
    als Beleg zitiert und ausdruecklich erhalten wissen will. Auf die geforderte 0 kommt der
    Bauende nur, indem er Dokumentation loescht. Fix ist eine Zeile - Befehl auf
    node scripts/zaehle.mjs umstellen, Ausgangswert 1, Partner dazu (B4).
    (3) DIE PRAEMISSE IST IM BROWSER WIDERLEGT. Selbst gefahren am gebauten Stand 5df61a37,
    headful, mit roter Kontrolle: OHNE Eingabe 0 neue Konva-Knoten; mit MAUS plus 2;
    mit TOUCH (CDP dispatchTouchEvent) EBENFALLS plus 2. Mitgeschrieben an der Stage:
    bei Touch feuern konva.tap 2 UND konva.click 2, dom.touchstart 2, dom.mousedown 2.
    Zeichnen mit dem Finger funktioniert also HEUTE. Der Zeichen-Einstieg ist onClick
    (Buehne.tsx:105), und genau den schliesst das Blatt aus. Der Satz "auf einem Tablet
    passiert heute nichts" ist so nicht haltbar.
    WAS BLEIBT UND WERTVOLL IST: touch-action (K-03), Fangradius je Zeigerart (K-04/K-06),
    Zwei-Finger-Abbruch (K-05), Vorschau ohne Hover. Das sind die echten Luecken - kleiner
    als das Blatt annimmt, aber real. Neu schneiden auf DIESE vier, mit onClick als
    benanntem Einstieg statt als Ausschluss.
    KLEIN: K-08 nennt hoechstens 118 Inline-Stellen - heute gemessen 118 bei 0 offenen,
    also die Obergrenze exakt erreicht; und die Suite-Basis ist an HEAD 1649/0, nicht 1641.
    B8-Fragen: Befehle laufen, aber K-01/K-02 messen die Gestalt und nicht die Wirkung (F-06);
    kein maschineller Befehl mutiert.
```

## Warum jetzt

**Z-11 hängt laut Bestandsaufnahme nur an Z-01 und Z-02 — beide sind abgenommen.** Die Scheibe
wartet auf nichts.

**Und sie ist die einzige im Z-Strang, die entscheidet, ob man den Planer überhaupt vorführen
kann.** Auf einem Tablet passiert heute nichts: die Bühne hört ausschließlich auf Maus.

```text
grep -o 'onPointer'   resources/planner/hausplaner/app/rahmen/Buehne.tsx | wc -l  -> 0
grep -o 'onMouseMove' resources/planner/hausplaner/app/rahmen/Buehne.tsx | wc -l  -> 3   (Partner)
grep -ro 'pointerType' resources/planner/hausplaner/                     | wc -l  -> 0
grep -o 'touch-action' resources/planner/hausplaner/hausplaner.css       | wc -l  -> 0
grep -o 'cursor'       resources/planner/hausplaner/hausplaner.css       | wc -l  -> 18  (Partner)
```

**Ein einziger `PointerEvent` steckt im Bestand** — in `renderers/three-d/szene.ts:648`, also im
3D-Teil. *Die 2D-Bühne, auf der gezeichnet wird, kennt ihn nicht.*

## Die Entscheidung

**Pointer Events ersetzen die Maus-Ereignisse. Es entsteht KEIN zweiter Touch-Pfad.**

```text
onMouseDown/Move/Up   ->   onPointerDown/Move/Up
```

**Das ist die ganze Bauart-Entscheidung, und sie ist eine Sparentscheidung:** `PointerEvent` deckt
Maus, Finger und Stift in *einem* Ereignis ab und trägt den Typ als Feld (`pointerType`). **Ein
eigener Touch-Zweig wäre eine zweite Wahrheit über denselben Vorgang** — dieselbe Klasse, gegen die
Z-01 die sieben Aufräumstellen zu einer gemacht hat.

**Drei Stellen, an denen der Typ dann doch zählt — und nur diese drei:**

```text
1  FANGRADIUS. Ein Finger ist dicker als ein Mauszeiger. Z-02 hat die Toleranz vom festen
   Millimeterwert auf Bildschirm-Pixel umgestellt; hier kommt ein Faktor dazu:
      pointerType 'mouse'        Faktor 1
      pointerType 'touch'        Faktor 2   (Daumenkuppe statt Pixel)
      pointerType 'pen'          Faktor 1   (der Stift ist genauer als der Finger)
   Der Faktor sitzt an der EINEN Stelle, an der die Toleranz schon berechnet wird:
   `toleranzAusZoom(zoom, fangPx = FANG_PX)` in `geometry/fangKern.ts:230` - sie bekommt
   einen dritten Parameter mit Vorgabewert 'mouse', keinen Zwilling daneben.

2  VORSCHAU OHNE HOVER. Maus hat einen Zeiger, der schwebt; ein Finger nicht. Was heute
   `onMouseMove` ohne gedrueckte Taste zeigt (Fangpunkt, Vorschaulinie), erscheint bei
   Touch erst NACH dem Aufsetzen. Das ist kein Mangel, sondern die Physik der Eingabe -
   und es gehoert benannt, damit niemand es als Fehler meldet.

3  ZWEI FINGER SIND KEIN ZEICHNEN. Ab dem zweiten gleichzeitigen Pointer wird der laufende
   Zug ABGEBROCHEN (wie Escape) und die Geste gehoert der Ansicht: Zoom und Verschieben.
   Wer mit zwei Fingern zoomt und dabei eine Wand zieht, bekommt sonst eine Wand quer
   durchs Haus.
```

**Druck und Neigung des Stifts werden ignoriert.** *Ein Bauplan kennt keine Strichstärke.*

## Nahtstellen

```text
Hier wird geschrieben:
  resources/planner/hausplaner/app/rahmen/Buehne.tsx     die drei Ereignisse + touch-action
  resources/planner/hausplaner/geometry/fangKern.ts      der Faktor an der EINEN Toleranzstelle
  resources/planner/hausplaner/hausplaner.css            `touch-action: none` auf der Buehne

Hier bewusst NICHT:
  renderers/three-d/szene.ts        hat seinen eigenen PointerEvent und seine eigene Kamera.
                                    3D-Bedienung ist eine eigene Scheibe.
  Die 20 Dateien mit onClick        Knoepfe funktionieren mit Touch von selbst. Nur die
                                    BUEHNE braucht den Umbau - dort wird gezogen.
  Druck, Neigung, Radiergummi-Ende  Ein Bauplan kennt keine Strichstaerke.
  Gesten ueber zwei Finger hinaus   Drei-Finger-Gesten sind auf keinem Geraet einheitlich.
```

**Erweiterungspunkt, jetzt nicht gebaut:** der Faktor aus (1) steht als benannte Konstante je
`pointerType`, nicht als `if touch then *2`. Ein späteres Gerät (Digitizer, Touchpad) hängt sich
dort an, ohne die Fangrechnung anzufassen.

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/app/rahmen/Buehne.tsx
    - resources/planner/hausplaner/geometry/fangKern.ts
    - resources/planner/hausplaner/hausplaner.css
  population_command: "grep -o 'onPointer' resources/planner/hausplaner/app/rahmen/Buehne.tsx | wc -l"
  ausschluesse:
    - stelle: "renderers/three-d/szene.ts"
      grund: "Eigener PointerEvent, eigene Kamera. 3D-Bedienung ist eine eigene Scheibe."
      entschieden_von: planner
    - stelle: "Die 20 Dateien mit onClick"
      grund: "Knoepfe koennen Touch von selbst. Nur die Buehne wird gezogen."
      entschieden_von: planner
    - stelle: "Druck, Neigung, Gesten ueber zwei Finger"
      grund: "Ein Bauplan kennt keine Strichstaerke; Drei-Finger-Gesten sind nicht einheitlich."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Die Buehne hoert auf Pointer, nicht mehr auf Maus."
    pruefung:
      befehl: "grep -o 'onPointer' resources/planner/hausplaner/app/rahmen/Buehne.tsx | wc -l"
      erwartet: "mindestens 3"
    ausgangswert: "0 (gemessen 01.08. 23:2x; Partner 'onMouseMove' -> 3, die Messung ist nicht leer)"

  - id: K-02
    typ: absence
    kritikalitaet: P1
    aussage: "KEIN zweiter Eingabepfad - die Maus-Ereignisse sind ERSETZT, nicht ergaenzt."
    pruefung:
      befehl: "grep -o 'onMouseDown\\|onMouseMove\\|onMouseUp' resources/planner/hausplaner/app/rahmen/Buehne.tsx | wc -l"
      erwartet: "0"
    ausgangswert: "3"
    gegenbeweis: |
      Steht hier nach dem Bau etwas anderes als 0, gibt es zwei Wege in denselben Zustand -
      und der zweite wird bei der naechsten Aenderung vergessen. Das ist dieselbe Klasse,
      gegen die Z-01 die sieben Aufraeumstellen zu einer gemacht hat.

  - id: K-03
    typ: presence
    kritikalitaet: P1
    aussage: "Der Browser scrollt die Buehne nicht weg."
    pruefung:
      befehl: "grep -o 'touch-action' resources/planner/hausplaner/hausplaner.css | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (Partner 'cursor' -> 18)"
    gegenbeweis: |
      Ohne `touch-action: none` verschiebt der Browser beim Wischen die SEITE statt zu zeichnen.
      Der Zug beginnt, das Ereignis wird abgefangen, und der Strich bleibt auf halber Strecke
      liegen - fuer den Benutzer sieht das aus, als sei das Werkzeug kaputt.

  - id: K-04
    typ: presence
    kritikalitaet: P1
    aussage: "Der Fangradius kennt den Eingabetyp - an EINER Stelle."
    pruefung:
      befehl: "grep -o 'pointerType' -r resources/planner/hausplaner/geometry/ | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0"
    gegenbeweis: |
      Bewusst ueber das VERZEICHNIS gemessen: waechst die Zahl in mehr als einer Datei,
      ist der Faktor verstreut. Die Toleranz wird seit Z-02 an EINER Stelle gerechnet und
      soll es bleiben. Partner: dasselbe Muster ueber die ganze Insel liefert heute 0,
      ueber `renderers/` findet es den 3D-PointerEvent - die Messung greift also.

  - id: K-05
    typ: behavioural
    kritikalitaet: P1
    aussage: "Zwei Finger brechen den Zug ab, statt ihn zu verzerren."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Entscheidungsfunktion, nicht gegen die Oberflaeche:
          zweiterPointer(zugLaeuft=true)   -> Zug wird abgebrochen, Zustand ist der von Escape
          zweiterPointer(zugLaeuft=false)  -> nichts wird abgebrochen, Geste gehoert der Ansicht
          erster Pointer hebt ab, zweiter bleibt -> KEIN neuer Zug beginnt
        Der Abbruch laeuft ueber DIESELBE Aufraeumstelle wie Escape (Z-01). Wer eine zweite
        anlegt, macht Z-01 rueckgaengig.
      erwartet: "drei Zusagen, davon eine ROTE"

  - id: K-06
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Faktor je Eingabetyp haengt an toleranzAusZoom() und ist gegen die Rechnung geprueft, nicht gegen das Gefuehl."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        KORREKTUR 02.08. (Planner): der erste Entwurf nannte eine Funktion `toleranz(art, zoom)`,
        die es nicht gibt. Die EINE Stelle heisst `toleranzAusZoom(zoom, fangPx = FANG_PX)` und
        steht in geometry/fangKern.ts, Zeile 230. Gemessen, nicht erinnert.

        Sie bekommt einen DRITTEN Parameter mit Vorgabewert - keinen Zwilling daneben:
          toleranzAusZoom(zoom, fangPx, zeigerArt = 'mouse')

        Reine Rechnung, ohne Browser:
          toleranzAusZoom(zoom)                     == toleranzAusZoom(zoom, FANG_PX, 'mouse')
          toleranzAusZoom(zoom, FANG_PX, 'touch')   == 2 * toleranzAusZoom(zoom, FANG_PX, 'mouse')
          toleranzAusZoom(zoom, FANG_PX, 'pen')     ==     toleranzAusZoom(zoom, FANG_PX, 'mouse')
          ueber mindestens drei Zoomstufen, darunter 0,02.

        UND der Waechter: bei zoom = 0 gibt die Funktion heute `fangPx` ZURUECK, ohne zu teilen.
        Genau dort faellt ein naiv eingebauter Faktor (fangPx / zoom * faktor) heraus.
          toleranzAusZoom(0, FANG_PX, 'touch')      == 2 * FANG_PX
        Ist das nicht so, fangt der Finger ausgerechnet im kaputten Zoomfall so fein wie die Maus.

        Der VORGABEWERT ist zugleich der Beweis fuer K-07: jeder heutige Aufrufer ruft weiter
        zweistellig auf und bleibt gruen, OHNE angefasst zu werden.
      erwartet: "gruen ueber drei Zoomstufen plus zoom = 0; kein bestehender Aufrufer geaendert"

  - id: K-07
    typ: behavioural
    aussage: "Die ganze Insel bleibt gruen - besonders die Fang-Zusagen aus Z-02."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail. Die Fang-Zusagen von Z-02 duerfen NICHT angepasst werden, um gruen zu werden - wenn sie fallen, ist der Faktor falsch eingehaengt."

  - id: K-08
    typ: behavioural
    aussage: "Kein neuer Inline-Stil - AUF-38 bleibt bei NULL offenen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "node scripts/statische-inline-stile.mjs"
      erwartet: "0 offen, Gesamtzahl hoechstens 118 (Stand fba3083f)"

  - id: K-09
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 8 Mutationen: Faktor fuer touch auf 1 · Faktor auf alle Typen ·
        zweiter Pointer bricht NICHT ab · Abbruch ueber eine eigene Aufraeumstelle ·
        touch-action entfernt · pointerType ignoriert und immer Maus angenommen ·
        onPointerUp fehlt (Zug bleibt haengen) · Rueckfall bei fehlendem pointerType auf touch.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest an http://ticket.test - mit dem FINGER zeichnen."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, angemeldet, Expertenmodus.
        Geraete-Emulation im Browser (Touch), Viewport 1024 und 375:
        (a) Wandwerkzeug, mit einem Finger ziehen -> die Wand entsteht
        (b) auf einen vorhandenen Punkt zielen -> der Fang greift SPUERBAR frueher als mit Maus
        (c) waehrend eines Zuges den zweiten Finger aufsetzen -> Zug bricht ab, nichts bleibt liegen
        (d) mit zwei Fingern zoomen, ohne dass ein Werkzeug laeuft -> Ansicht zoomt
        (e) ueber die Buehne wischen -> die SEITE scrollt nicht
        PARTNERMESSUNG in jedem Viewport: mit der MAUS geht alles weiter wie bisher.
        Ist die Partnermessung rot, ist der Umbau kaputt und nicht das Geraet.

  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
```

## Kantenliste — jede Zeile hat eine Zusage oder einen Grund (B9)

```text
1  Zwei Finger verzerren den laufenden Zug.                          -> K-05
2  Der Faktor liegt an zwei Stellen und driftet auseinander.         -> K-04 misst das Verzeichnis
3  `touch-action` fehlt, der Browser scrollt beim Zeichnen.          -> K-03
4  Maus- UND Pointer-Ereignisse stehen nebeneinander, jedes Ziehen
   laeuft zweimal.                                                    -> K-02
5  Fehlender `pointerType` (aeltere Browser) faellt auf 'touch'
   statt auf 'mouse' zurueck - dann fangt die Maus zu grob.          -> K-06 erste Zusage
6  `onPointerUp` kommt nicht an, weil der Finger die Buehne verlaesst.
   OHNE ZUSAGE, mit Grund: das ist derselbe Fall wie der Maus-Austritt,
   den Z-01 bereits geloest hat (`onMouseMove` haengt an der Buehne,
   Kommentar Buehne.tsx:42). Wer ihn hier neu loest, baut Z-01 nach.
   Der Generator PRUEFT, dass die vorhandene Loesung auch fuer Pointer greift.
7  iPad-Safari meldet `pointerType` fuer den Apple Pencil als 'pen',
   fuer den Finger als 'touch' - erwartet. OHNE ZUSAGE, mit Grund:
   nicht ohne echtes Geraet pruefbar; L-01 laeuft in der Emulation,
   und das steht so im Blatt statt als stille Annahme.
8  Der Fangfaktor 2 ist geraten, nicht gemessen. OHNE ZUSAGE, mit Grund:
   eine Zahl, die sich am Daumen orientiert, laesst sich nur am Geraet
   pruefen. K-06 nagelt das VERHAELTNIS fest, nicht seine Richtigkeit -
   damit eine spaetere Korrektur EINE Konstante ist und kein Umbau.
```

## Rückweg und Entdeckung

**Rückweg:** drei Dateien, kein Datenpfad, kein Schema, keine Migration — der Commit lässt sich
zurückdrehen. **Und der Rückweg ist diesmal wirklich einer:** die Maus-Ereignisse werden ersetzt,
nicht gelöscht — wer zurückdreht, hat sofort wieder den alten Stand.

**Entdeckung:** die Maus. **Wenn der Umbau schiefgeht, merkt man es zuerst an der Maus, nicht am
Finger** — deshalb ist die Partnermessung in L-01 Pflicht und nicht Kür. *Der stille Fall ist
Kante 4: beides hört zu, jedes Ziehen läuft doppelt, und auf einem schnellen Rechner sieht man es
nicht.*

## Danach

**Z-10** (Maßeingabe) wird auf dem Tablet erst richtig wichtig — dort zieht niemand pixelgenau.
Die beiden Scheiben sind unabhängig baubar, aber sie gehören zusammen vorgeführt.
