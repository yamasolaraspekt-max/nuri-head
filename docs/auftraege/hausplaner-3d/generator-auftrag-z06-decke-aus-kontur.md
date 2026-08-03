# Z-06 — Die Decke nimmt die gezeichnete Kontur statt der Bounding-Box

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · **Vorbedingung: Z-05 abgenommen** ·
*Geschnitten 01.08. 12:2x*

```yaml
auftrag:
  id: Z-06
  strang: hausplaner-3d
  status: abgenommen   # Votum GRUEN vom Evaluator in 20bbfcc2 (03.08., Mutation an der Entscheidung gefuehrt). DIE ZWISCHENDECKE STEHT. Eingetragen vom Planner.
```

## Warum das der Posten ist, auf den Yama wartet

**Seine Frage seit Tagen:** *„wann kann ich Geschoße bauen, wann kann ich sauber eine Zwischendecke
ziehen damit ich mit der nächsten Etage anfange."*

**Geschoss anlegen und duplizieren gehen heute schon.** Was nicht geht, ist die **saubere** Decke:

```text
HausplanerApp.tsx:591  /** Default-Traufkontur = Gebäude-Umriss (Bounding-Box der Wände …) */
HausplanerApp.tsx:745  polygon: gebaeudeUmriss(),      <- die Decke
HausplanerApp.tsx:727  polygon: gebaeudeUmriss(),      <- das Dach  (das ist Z-08, NICHT hier)

node scripts/zaehle.mjs …/HausplanerApp.tsx 'polygon: gebaeudeUmriss\(\)'  ->  2
```

**Bei einem Rechteck stimmt die Bounding-Box zufällig. Bei L-, T- und U-Form ist sie falsch — und
zwar still:** die Decke erscheint, sie ragt nur über den Grundriss hinaus. *Ein falsches Ergebnis,
das richtig aussieht — dieselbe Klasse wie PB-047 und wie der Canvas-Befund von heute früh.*

## Die Entscheidung

**Gibt es eine gezeichnete Kontur, wird sie genommen. Gibt es keine, bleibt die Bounding-Box —
mit einem sichtbaren Hinweis, dass sie eine Näherung ist.**

*Kein Zwang zum Konturzeichnen: für den rechteckigen Fall ist die Bounding-Box richtig, und wer
schnell ein Geschoss stapeln will, soll nicht erst sechs Punkte klicken müssen.* **Aber niemand
darf glauben, er habe eine exakte Decke, wenn er eine Näherung hat.**

**Der Dach-Aufruf in Zeile 727 wird NICHT angefasst** — das ist Z-08. *Wer beides in einer Scheibe
macht, kann bei einer roten Abnahme nicht mehr sagen, welche Hälfte schuld ist.*

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/app/HausplanerApp.tsx
  population_command: "node scripts/zaehle.mjs resources/planner/hausplaner/app/HausplanerApp.tsx 'polygon: gebaeudeUmriss\\(\\)'"
  ausschluesse:
    - stelle: "Zeile 727 - polygon: gebaeudeUmriss() im ADD_ROOF-Zweig"
      grund: "Das Dach aus Kontur ist Z-08. Zwei Aenderungen in einer Scheibe machen eine rote Abnahme unlesbar."
      entschieden_von: planner
    - stelle: "gebaeudeUmriss() selbst"
      grund: "Bleibt als Rueckfall bestehen. Sie ist fuer den rechteckigen Fall richtig."
      entschieden_von: planner
    - stelle: "Treppendurchbrueche"
      grund: "Schneidet der Command bereits selbst. Nicht Gegenstand."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: absence
    kritikalitaet: P1
    aussage: "Die Decke nimmt die Bounding-Box nicht mehr bedingungslos."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/HausplanerApp.tsx 'polygon: gebaeudeUmriss\\(\\)'"
      erwartet: "1"
    ausgangswert: "2 (gemessen 01.08. 12:2x - Decke UND Dach)"
    gegenbeweis: |
      Erwartet wird 1, nicht 0. Steht dort 0, ist auch der DACH-Aufruf angefasst worden -
      das ist Z-08 und in dieser Scheibe ausdruecklich ausgeschlossen. Steht dort 2, hat
      sich nichts geaendert. Die Zahl unterscheidet "richtig gebaut" von beiden Fehlern.

  - id: K-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Decke einer L-Form hat die Flaeche der L-Form, nicht die des umschliessenden Rechtecks."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Neue Zusage in __tests__/decke.test.ts (heute 7 Zusagen):
          L-Form 8x10 m mit einem ausgesparten Eck von 3x4 m
          -> polygonFlaecheM2(decke.polygon) == 80 - 12 == 68 m2
          -> und ausdruecklich NICHT 80
        Die Zusage prueft die FLAECHE, nicht die Punktliste - eine Punktliste friert den
        gebauten Zustand ein (F-06), die Flaeche prueft die Aussage.
      erwartet: "gruen, 8 Zusagen"

  - id: K-03
    typ: presence
    kritikalitaet: P1
    aussage: "Ohne Kontur sagt die Oberflaeche, dass die Decke eine Naeherung ist."
    ausgefuehrt_von: generator
    pruefung:
      typ: browser
      schritte: |
        Ohne gezeichnete Kontur eine Decke anlegen: die Statusleiste nennt sie als Naeherung
        aus dem Gebaeude-Umriss. Der Hinweis ist Text, kein Symbol allein.
    begruendung: |
      Ohne diesen Satz ist die Naeherung nicht von der exakten Decke unterscheidbar - und
      genau das ist der heutige Fehler, nur mit besserem Gewissen.

  - id: K-04
    typ: behavioural
    aussage: "Die ganze Insel bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail"

  - id: K-05
    typ: absence
    aussage: "Kein persistiertes Schema wurde angefasst."
    pruefung:
      befehl: "git diff main -- resources/planner/hausplaner/domain/validation.ts | wc -l"
      erwartet: "0"
    ausgangswert: "0"

  - id: K-06
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Tests."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 8 Mutationen: Kontur ignoriert, Kontur und Umriss vertauscht, Umlaufsinn
        gedreht, Hinweis auch bei vorhandener Kontur gezeigt, Decke doppelt angelegt.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest an http://ticket.test - die Zwischendecke einer L-Form."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, Expertenmodus.
        (a) L-Form aus Waenden zeichnen
        (b) Kontur mit dem Werkzeug aus Z-05 nachzeichnen und schliessen
        (c) Decke anlegen -> sie deckt die L-Form, nicht das umschliessende Rechteck
        (d) Geschoss darueber anlegen und duplizieren -> die Decke des unteren bleibt
        (e) OHNE Kontur: Decke anlegen -> Bounding-Box UND der Naeherungs-Hinweis
        Drei Pflicht-Viewports: 1440, 1024, 375.
    gegenbeweis: |
      Schritt (c) ist der ganze Zweck. Wer nur mit einem rechteckigen Grundriss probiert,
      sieht keinen Unterschied zu vorher - dort ist die Bounding-Box zufaellig richtig.

  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
```

## Rückweg und Entdeckung

**Rückweg:** eine Verzweigung in einem Bauteil, kein Schema, keine Migration.
**Entdeckung:** die Deckenfläche ist sichtbar und in K-02 gemessen. *Der stille Fall wäre die
Näherung ohne Hinweis — dafür ist K-03 da.*

## Danach

**Z-07** Vorschlagskontur aus dem Grundriss (korrigierbar) · **Z-08** Dach aus Kontur (Zeile 727).
