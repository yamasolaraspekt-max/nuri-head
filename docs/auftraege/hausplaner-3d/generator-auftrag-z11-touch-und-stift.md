# Z-11 — Touch fertig machen: vier Lücken, kein Umbau

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *NEU GESCHNITTEN 03.08. 09:2x — die erste Fassung trug nicht (Prämisse im Browser widerlegt)*

```yaml
auftrag:
  id: Z-11
  strang: hausplaner-3d
  status: bereit   # BEREIT 03.08. 23:0x (B14, Planner): Pruefer-Gegenlesung BAUBAR ohne Einwand, alle Praemissen am Code bestaetigt. Baureihenfolge: NACH Z-07 und N2.
  gegengelesen_von: pruefer
  gegengelesen_am: "2026-08-03"
  befund: "BAUBAR, KEIN Einwand. Alle Ausgangswerte an HEAD 14079a93 selbst nachgemessen und EXAKT getroffen: touch-action 0 / cursor 18 / zeigerArt 0 / toleranzAusZoom 1. Auch die Ruecknahme-Behauptungen halten am Code: Signatur fangKern.ts:230 OHNE Zeigerart, onClick Buehne.tsx:105, onMouseDown/Up 0 Treffer, und der zoom=0-Waechter gibt fangPx ohne Division zurueck - genau die Kante, die K-03 prueft. Die zweite Fassung ist das Gegenteil der ersten: jede Praemisse gemessen, der tragende Einstieg bleibt unangetastet, Luecke 4 wird ehrlich benannt statt gebaut. Validator 0 Fehlschlag. Status stellt der Planner (B14)."
  ruecknahme: "Fassung 1 wollte onMouseDown/Move/Up durch Pointer-Events ERSETZEN. Der Evaluator hat im Browser gemessen (headful, Stand 5df61a37): ZEICHNEN MIT DEM FINGER FUNKTIONIERT HEUTE - der Einstieg ist onClick (Buehne.tsx:105, Konva-Stage), zwei der drei zu ersetzenden Handler EXISTIEREN NICHT (onMouseDown 0, onMouseUp 0), und K-02 war unerfuellbar, weil sein Ausgangswert 2 Kommentare mitzaehlte. Das Ziel bleibt; der Weg war falsch."
```

## Warum — 10 Zeilen (B15)

**Touch zeichnet heute** (Evaluator: 2 CDP-Tipps → +2 Konva-Knoten, Kontrolle ohne Eingabe → 0).
**Vier Lücken bleiben, alle gemessen:**

```text
1  touch-action fehlt          grep -o 'touch-action' hausplaner.css | wc -l  ->  0
                               (die SEITE scrollt beim Wischen ueber die Buehne)
2  Fangradius kennt keine      toleranzAusZoom(zoom, fangPx) hat KEINEN Zeigerart-Parameter
   Zeigerart                   (fangKern.ts:230 - ein Finger ist dicker als ein Mauszeiger)
3  Zwei-Finger-Geste bricht    kein zweiter-Pointer-Abbruch; Zoom-Geste und Zug kollidieren
   den Zug nicht ab
4  Vorschau ohne Hover         unbenannt - bei Touch erscheint der Fangpunkt erst NACH dem
                               Aufsetzen. Physik, kein Fehler: wird BENANNT, nicht gebaut.
```

**Der Einstieg bleibt `onClick` (Buehne.tsx:105) — er wird NICHT ersetzt.** *Konva liefert Touch
dort schon heute; wer Handler tauscht, die es nicht gibt, gefährdet die Kette, die trägt.*

## Nahtstellen

```text
Hier wird geschrieben:
  hausplaner.css                    touch-action: none auf der Buehne
  geometry/fangKern.ts              dritter Parameter zeigerArt mit Vorgabe 'mouse'
  app/rahmen/Buehne.tsx             Zwei-Finger-Abbruch + Vorschau-Verhalten benannt

Hier bewusst NICHT:
  onClick/onMouseMove der Stage     der Einstieg TRAEGT (im Browser belegt). Kein Ersatz.
  renderers/three-d/szene.ts        eigener PointerEvent, eigene Kamera, eigene Scheibe.
  Druck/Neigung des Stifts          ein Bauplan kennt keine Strichstaerke.
```

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/hausplaner.css
    - resources/planner/hausplaner/geometry/fangKern.ts
    - resources/planner/hausplaner/app/rahmen/Buehne.tsx
  population_command: "node scripts/zaehle.mjs resources/planner/hausplaner/hausplaner.css 'touch-action'"
  ausschluesse:
    - stelle: "onClick/onMouseMove der Konva-Stage"
      grund: "Traegt heute nachweislich Touch (Browser-Messung des Evaluators). Ersatz waere Risiko ohne Luecke."
      entschieden_von: planner
    - stelle: "renderers/three-d/szene.ts"
      grund: "Eigener PointerEvent, eigene Kamera. Eigene Scheibe."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Der Browser scrollt die Buehne nicht weg."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/hausplaner.css 'touch-action'"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 03.08. mit zaehle.mjs - kommentarfrei, die K-02-Falle der ersten Fassung kann nicht zurueckkommen. Partner 'cursor' -> 18)"

  - id: K-02
    typ: presence
    kritikalitaet: P1
    aussage: "Der Fangradius kennt die Zeigerart - an der EINEN Toleranzstelle."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/geometry/fangKern.ts 'zeigerArt'"
      erwartet: "mindestens 2"
    ausgangswert: "0 (Partner 'toleranzAusZoom' -> 1, kommentarfrei mit zaehle.mjs gemessen 03.08. - die erste Fassung schrieb 'mehrfach' ohne Messung, F-04 am eigenen Blatt)"

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Faktor ist gegen die Rechnung geprueft - samt zoom=0-Waechter."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        toleranzAusZoom(zoom)                    == toleranzAusZoom(zoom, FANG_PX, 'mouse')
        toleranzAusZoom(zoom, FANG_PX, 'touch')  == 2 * toleranzAusZoom(zoom, FANG_PX, 'mouse')
        toleranzAusZoom(zoom, FANG_PX, 'pen')    ==     toleranzAusZoom(zoom, FANG_PX, 'mouse')
        ueber drei Zoomstufen inkl. 0,02 - UND toleranzAusZoom(0, FANG_PX, 'touch') == 2*FANG_PX
        (bei zoom=0 gibt die Funktion fangPx OHNE Division zurueck - dort faellt ein naiver
        Faktor heraus). Vorgabewert = Beweis: kein bestehender Aufrufer wird angefasst.
      erwartet: "gruen ueber drei Zoomstufen plus zoom=0, kein Aufrufer geaendert"

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Zwei Finger brechen den Zug ab - ueber die EINE Aufraeumstelle aus Z-01."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        zweiterPointer(zugLaeuft=true)  -> Zustand wie Escape (dieselbe Aufraeumstelle, Z-01)
        zweiterPointer(zugLaeuft=false) -> nichts bricht ab, Geste gehoert der Ansicht
        Wer eine zweite Aufraeumstelle anlegt, macht Z-01 rueckgaengig.
      erwartet: "zwei Zusagen, eine davon ROT"

  - id: K-05
    typ: behavioural
    aussage: "Insel gruen, Fang-Zusagen aus Z-02 UNANGETASTET."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail. Basis misst der Bauende VOR dem Zug und benennt sie (F-20)."

  - id: K-06
    typ: behavioural
    aussage: "Mutationsprobe VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 5: Faktor fuer touch auf 1 · Faktor auf alle Arten · zweiter Pointer bricht
        nicht ab · Abbruch ueber eigene Aufraeumstelle · touch-action entfernt. Wie viele durch?

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest - mit dem FINGER, in der Emulation."
    pruefung:
      typ: browser
      schritte: |
        Geraete-Emulation (Touch), Viewport 1024 und 375:
        (a) Wand mit dem Finger zeichnen -> entsteht (KONTROLLE: das ging schon VOR dem Bau -
            der Beleg des Evaluators; neu ist nur, was b-d pruefen)
        (b) ueber die Buehne wischen -> die SEITE scrollt nicht
        (c) auf einen Punkt zielen -> der Fang greift SPUERBAR frueher als mit der Maus
        (d) zweiter Finger waehrend des Zugs -> Abbruch, nichts bleibt liegen; Zwei-Finger-Zoom
            OHNE laufenden Zug zoomt die Ansicht
        PARTNERMESSUNG je Viewport: mit der MAUS alles wie bisher.

  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
```

## Kantenliste (B9)

```text
1  Seite scrollt beim Zeichnen.                                   -> K-01, L-01(b)
2  Faktor verstreut statt an der einen Stelle.                    -> K-02 misst die Datei, K-03
3  Zwei Finger verzerren den Zug.                                 -> K-04, L-01(d)
4  Rueckfall bei fehlender Zeigerart faellt auf 'touch' -> Maus
   fangt zu grob.                                                 -> K-03 erste Zeile
5  Vorschau ohne Hover wird als Fehler gemeldet.
   OHNE ZUSAGE, mit Grund: Physik der Eingabe. Sie wird im Blatt und im Fuss BENANNT -
   eine Zusage ueber Nicht-Verhalten waere Gestalt statt Wirkung (F-06).
```

## Rückweg und Entdeckung

**Rückweg:** drei Dateien, kein Schema. **Entdeckung:** die Maus — bricht die Partnermessung, ist
der Umbau schuld, nicht das Gerät. *Und die Lehre der ersten Fassung steht im Kopf: eine Prämisse
über den Browser wird IM Browser gemessen, bevor ein Blatt sie trägt.*
