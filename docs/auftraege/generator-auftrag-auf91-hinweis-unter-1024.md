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

```yaml
  - id: K-01
    aussage: "Unter 1024 px erscheint eine Hinweisflaeche, darueber nicht."
    nachweis: >
      Bei 375 px und 800 px: eine sichtbare Flaeche mit einem Satz und dem Weg zurueck.
      Bei 1024 px und 1440 px: unveraendert, KEIN zusaetzliches Element im DOM.
    gegenbeweis: >
      Miss bei 1023 und bei 1024 px. Springt die Flaeche nicht genau dort, ist die
      Schwelle nicht die, die im Kriterium steht.

  - id: K-02
    aussage: "Die Verriegelung gegen Medienabfragen bleibt bestehen."
    befehl: "npm run test:hausplaner -- --filter=stilschicht"
    erwartet: "gruen, 0 rote Faelle"
    hinweis: >
      Die Schwelle kommt aus der GEMESSENEN Behaelterbreite. `buehnenBreite.ts` misst sie
      bereits per ResizeObserver (`getBoundingClientRect`) — der Schalter existiert, er muss
      nur gelesen werden. **Kein zweiter Messweg.**

  - id: K-03
    aussage: "Der Hinweis ist ehrlich, nicht endgueltig."
    nachweis: >
      Der Satz nennt (a) dass der Planer eine Mindestbreite braucht, (b) welche,
      und (c) den Weg zurueck. **Er behauptet nicht, dass es nie gehen wird** —
      L7 ist eine Vertagung, keine Absage.

  - id: K-04
    aussage: "Die Hinweisflaeche haengt an keinem Zustand."
    befehl: "grep -cE 'useState|usePlannerUiStore|localStorage' <die neue Datei>"
    erwartet: "0 — sie liest die gemessene Breite und sonst nichts"

  - id: L-01
    aussage: "Die Buehne rendert bei normaler Breite unveraendert."
    nachweis: >
      npm run build:hausplaner, dann /admin/hausplaner/studio bei 1440 px →
      Expertenmodus → Taste W → zwei Klicks auf LEERER Flaeche → Wand mit Masszahl.
  - id: L-01-anker
    nachweis: >
      VOR jeder anderen Zahl: HTTP 200 · querySelectorAll('canvas') mindestens 1 ·
      document.title enthaelt "Hausplaner". Auch melden, wenn alles gut aussah.
```

## Vorbehalt

**AUF-91 erfuellt L7 nur teilweise.** Die Abnahme-Runde L7 umfasst ausserdem A11y-Kontrast,
2D/3D-Selektions-Sync und Aktivierungsgrund als Tooltip — **sie bleibt offen und gehoert dem
Evaluator.** *Wer AUF-91 abnimmt, nimmt nicht L7 ab.*

**Reihenfolge: nach AUF-48.** *Klein genug, um zwischen zwei Scheiben zu passen, falls der
Generator wartet.*
