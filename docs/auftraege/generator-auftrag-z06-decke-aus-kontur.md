# Z-06 — Die Decke nimmt die gezeichnete Kontur statt der Bounding-Box

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · **Vorbedingung: Z-05 abgenommen** ·
*Geschnitten 01.08. 12:2x*

```yaml
auftrag:
  id: Z-06
  strang: hausplaner-3d
  status: bereit   # ENTSPERRT 02.08. 12:4x. Beide Bedingungen erfuellt: (1) Z-05 hat sein Votum GRUEN vom Evaluator in 44817747 - K-01..K-05 plus tsc, Gegenbeweis per Mutation. (2) Z-05-N1 gebaut und abgenommen seit a0a6e250, 01.08. 21:43. Eingetragen vom Planner. DIES IST DIE ZWISCHENDECKE - der Posten, auf den Yama seit Tagen wartet.
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
    typ: presence
    aussage: "Die Buehne ist MONTIERT, bevor irgendeine Zahl abgelesen wird - und der Weg dahin steht im Blatt."
    pruefung:
      typ: browser
      schritte: |
        Dreistufig. Stufe 2 ist ein SCHRITT, kein Zweig - es wird KEIN Projekt geoeffnet.
        1  SEITE       HTTP 200 - document.title enthaelt "Hausplaner"
                       - #hausplaner-root existiert und ist groesser als 0x0
                       - #hausplaner-scene existiert (das JSON-Element aus der Blade-Seite;
                         ohne es meldet main.tsx "Mount oder Szene fehlt" und montiert nie)
        2  MONTIEREN   Knopf "Expertenmodus" innerhalb #hausplaner-root klicken, bis 5 s warten.
                       Kein Projekt, kein Schreiben in die Datenbank.
        3  BUEHNE      ERST DANN: querySelectorAll('canvas') mindestens 1 (gemessen: 2)
        Bleibt canvas NACH Stufe 2 bei 0, ist DAS der rote Befund - der Startzustand davor
        ist keiner.
        Herkunft: Planner-Befund docs/planner/befund-anker-startzustand-2026-08-02.md,
        korrigiert durch den Pruefer-Befund vom 02.08. (Expertenmodus montiert ohne Projekt).
        NICHT uebernommen: "#hausplaner-scene mit 0 Kindern" als Startzeichen - das Element
        ist ein <script type="application/json">, es hat NIE Element-Kinder. Als Zeichen
        taugt seine EXISTENZ, nicht seine Kinderzahl. (Planner, 02.08., an der Quelle gemessen:
        studio.blade.php:93, main.tsx:28.)
```

## Rückweg und Entdeckung

**Rückweg:** eine Verzweigung in einem Bauteil, kein Schema, keine Migration.
**Entdeckung:** die Deckenfläche ist sichtbar und in K-02 gemessen. *Der stille Fall wäre die
Näherung ohne Hinweis — dafür ist K-03 da.*

## Danach

**Z-07** Vorschlagskontur aus dem Grundriss (korrigierbar) · **Z-08** Dach aus Kontur (Zeile 727).
