# AUF-91 — unter 1024 px sagt der Planer, dass er hier nicht bedienbar ist

**Spur B** *(eine Hinweisflaeche, kein Layout-Umbau, kein Datenpfad)* · **Heimat: ticket**
**Basis: HEAD zum Zeitpunkt des Ziehens** · *Geschnitten 30.07. 22:20*

**Herkunft:** `PB-046` (Pruefer, 20:32, aus der laufenden Anwendung) und `AUF-46` (Planner,
25.07.) — **derselbe Sachverhalt, zusammengefuehrt am 30.07. 21:45.**
**Entscheidung dazu:** `docs/auftraege/entscheidung-pb046-mindestbreite-2026-07-30.md`.

## Der Befund

```text
bei 375 px:  acht Bedienelemente liegen bei x 588–710, also ausserhalb des Sichtfelds
             scrollWidth == 375 == Sichtfeldbreite   ->  KEIN Weg dorthin
             Modusschalter 390 px breit auf 375 px
             Reiter "Expertenmodus" ragt 43 px ueber die rechte Kante
bei 1024 px und 1440 px:  keine Beanstandung
```

## Was NICHT das Ziel ist

**Der Planer muss auf 375 px nicht bedienbar werden.** Ein CAD-Werkzeug auf einem Telefon zu
bedienen ist unverhaeltnismaessig, und die Vertagung auf **L7** (`fahrplan-frontend-layout-
hausplaner.md:92`, Rolle *Evaluator*) gilt unveraendert.

**Und ausdruecklich nicht: `@media`.** `__tests__/stilschicht.test.ts:114` verbietet
Medienabfragen mit der Begruendung *„Responsive ist L7"*. **Diese Zusage bleibt gruen.**
*Faellt sie, ist der Weg falsch — nicht die Zusage.*

## Was das Ziel ist

**Er muss sagen, dass er dort nicht bedienbar ist.** Die ticket-Shell hat 38 Medienabfragen und
ist ausdruecklich fuer kleine Bildschirme gebaut — wer ueber sie ankommt, landet heute auf einer
Oberflaeche, die **funktionstuechtig aussieht und acht Werkzeuge verschweigt.**
*Das ist schlechter als eine ehrliche Sperre.*

## Kriterien

*Auf das Validator-Schema umgestellt am 30.07. 23:29 (VORLAGE-Regel 8). **Dabei musste eine
Festlegung nachgeholt werden:** K-04 nannte als Befehl `grep ... <die neue Datei>` — einen
Platzhalter in einem ausfuehrbaren Block, VORLAGE-Regel 3. Der Dateiname stand nirgends im
Blatt. **Der Planner legt ihn hiermit fest:**
`resources/planner/hausplaner/app/rahmen/MindestbreiteHinweis.tsx`.*

```yaml
scope:
  datei: resources/planner/hausplaner/app/rahmen/MindestbreiteHinweis.tsx
  population_command: "grep -c 'buehnenBreite' resources/planner/hausplaner/app/dashboard/buehnenBreite.ts"
  ausschluesse:
    - stelle: "@media in der Stilschicht"
      grund: "__tests__/stilschicht.test.ts:114 verbietet Medienabfragen mit der Begruendung Responsive ist L7. Die Schwelle kommt aus der gemessenen Behaelterbreite, nicht aus CSS."
      entschieden_von: planner
    - stelle: "Bedienbarkeit unter 1024 px herstellen"
      grund: "L7 bleibt vertagt. Dieses Blatt macht die Sperre ehrlich, es hebt sie nicht auf."
      entschieden_von: yama

kriterien:
  - id: K-01
    typ: behavioural
    kritikalitaet: P1
    aussage: "Unter 1024 px erscheint eine Hinweisflaeche, darueber nicht."
    pruefung:
      typ: browser
      schritte: >
        Bei 375 px und 800 px eine sichtbare Flaeche mit einem Satz und dem Weg zurueck.
        Bei 1024 px und 1440 px unveraendert, KEIN zusaetzliches Element im DOM.
    gegenbeweis: >
      Miss bei 1023 und bei 1024 px. Springt die Flaeche nicht genau dort, ist die Schwelle
      nicht die, die im Kriterium steht.

  - id: K-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Verriegelung gegen Medienabfragen bleibt bestehen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner -- --filter=stilschicht"
      erwartet: "gruen, 0 rote Faelle"
    begruendung: >
      Die Schwelle kommt aus der GEMESSENEN Behaelterbreite. buehnenBreite.ts misst sie
      bereits per ResizeObserver ueber getBoundingClientRect - der Schalter existiert, er
      muss nur gelesen werden. Kein zweiter Messweg.

  - id: K-03
    typ: presence
    aussage: "Der Hinweis ist ehrlich, nicht endgueltig."
    pruefung:
      typ: verfahren
      schritte: >
        Der Satz nennt (a) dass der Planer eine Mindestbreite braucht, (b) welche, und (c) den
        Weg zurueck. Er behauptet nicht, dass es nie gehen wird - L7 ist eine Vertagung,
        keine Absage.

  - id: K-04
    typ: absence
    aussage: "Die Hinweisflaeche haengt an keinem Zustand."
    pruefung:
      befehl: "grep -oE 'useState|usePlannerUiStore|localStorage' resources/planner/hausplaner/app/rahmen/MindestbreiteHinweis.tsx | wc -l"
      erwartet: "0"
    hinweis: >
      ACHTUNG, selbst nachgemessen und die eigene Erwartung korrigiert: der Validator meldet
      hier heute OK, obwohl die Datei noch gar nicht existiert. grep schreibt seinen Fehler
      nach stderr, wc -l zaehlt null Zeilen und endet mit 0 - der Befehl sieht erfolgreich aus.
      Das ist genau der zweite, gefaehrlichere Fall aus dem Kopf von auftrag-pruefen.mjs.
      DESHALB gilt fuer K-04 zusaetzlich: die Datei muss existieren.
      Pruefung: ls resources/planner/hausplaner/app/rahmen/MindestbreiteHinweis.tsx
    gegenbeweis: >
      Sie liest die gemessene Breite und sonst nichts. Zum Gegenbeweis ein useState einbauen:
      der Zaehler muss auf 1 springen.

  - id: L-01
    typ: presence
    aussage: "Die Buehne rendert bei normaler Breite unveraendert."
    pruefung:
      typ: browser
      schritte: >
        npm run build:hausplaner, dann /admin/hausplaner/studio bei 1440 px, Expertenmodus,
        Taste W, zwei Klicks auf LEERER Flaeche, Wand mit Masszahl.

  - id: L-01-anker
    typ: presence
    aussage: "Die Seite ist ueberhaupt da, bevor irgendeine Zahl abgelesen wird."
    pruefung:
      typ: browser
      schritte: >
        VOR jeder anderen Zahl: HTTP 200, querySelectorAll('canvas') mindestens 1,
        document.title enthaelt "Hausplaner". Auch melden, wenn alles gut aussah.
```

## Vorbehalt

**AUF-91 erfuellt L7 nur teilweise.** Die Abnahme-Runde L7 umfasst ausserdem A11y-Kontrast,
2D/3D-Selektions-Sync und Aktivierungsgrund als Tooltip — **sie bleibt offen und gehoert dem
Evaluator.** *Wer AUF-91 abnimmt, nimmt nicht L7 ab.*

**Reihenfolge: nach AUF-48.** *Klein genug, um zwischen zwei Scheiben zu passen, falls der
Generator wartet.*
